<?php

namespace App\Livewire\Report;

use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Laporan Penjualan - BrewPOS')]
class ReportIndex extends Component
{
    public $dateRange = 'today'; // today, this_week, this_month, all, custom

    public $startDate = null;

    public $endDate = null;

    public function mount()
    {
        $this->setDateRange('today');
    }

    public function setDateRange($range)
    {
        $this->dateRange = $range;

        if ($range === 'today') {
            $this->startDate = now()->format('Y-m-d');
            $this->endDate = now()->format('Y-m-d');
        } elseif ($range === 'this_week') {
            $this->startDate = now()->subDays(6)->format('Y-m-d');
            $this->endDate = now()->format('Y-m-d');
        } elseif ($range === 'this_month') {
            $this->startDate = now()->startOfMonth()->format('Y-m-d');
            $this->endDate = now()->format('Y-m-d');
        } elseif ($range === 'all') {
            $this->startDate = null;
            $this->endDate = null;
        }

        $this->dispatchChartsUpdate();
    }

    public function updatedStartDate()
    {
        $this->dateRange = 'custom';
        $this->dispatchChartsUpdate();
    }

    public function updatedEndDate()
    {
        $this->dateRange = 'custom';
        $this->dispatchChartsUpdate();
    }

    public function dispatchChartsUpdate(): void
    {
        $this->dispatch('charts-updated', ...$this->getChartData());
    }

    public function exportExcel()
    {
        $ordersQuery = Order::with(['items', 'table', 'user']);

        if (! empty($this->startDate) && ! empty($this->endDate)) {
            $ordersQuery->whereBetween('created_at', [
                $this->startDate.' 00:00:00',
                $this->endDate.' 23:59:59',
            ]);
        }

        $orders = $ordersQuery->latest()->get();
        $totalSubtotal = (float) $orders->sum('subtotal');
        $totalDiscount = (float) $orders->sum('discount_amount');
        $totalTax = (float) $orders->sum('tax_amount');
        $totalRevenue = (float) $orders->sum('total_amount');
        $totalOrders = $orders->count();
        $periodLabel = ($this->startDate && $this->endDate)
            ? "{$this->startDate} s/d {$this->endDate}"
            : 'Semua Waktu';

        $topProductsQuery = OrderItem::select('product_name', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(subtotal) as total_sales'))
            ->groupBy('product_name')
            ->orderByDesc('total_qty');

        if (! empty($this->startDate) && ! empty($this->endDate)) {
            $topProductsQuery->whereBetween('created_at', [
                $this->startDate.' 00:00:00',
                $this->endDate.' 23:59:59',
            ]);
        }
        $topProducts = $topProductsQuery->get();

        $filename = 'Laporan-Penjualan-BrewPOS-'.date('Ymd-His').'.xls';

        $content = view('reports.report-excel', [
            'orders' => $orders,
            'topProducts' => $topProducts,
            'periodLabel' => $periodLabel,
            'totalSubtotal' => $totalSubtotal,
            'totalDiscount' => $totalDiscount,
            'totalTax' => $totalTax,
            'totalRevenue' => $totalRevenue,
            'totalOrders' => $totalOrders,
        ])->render();

        return response()->streamDownload(function () use ($content) {
            echo "\xEF\xBB\xBF".$content;
        }, $filename, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control' => 'max-age=0',
        ]);
    }

    public function exportCsv()
    {
        return $this->exportExcel();
    }

    public function render()
    {
        $ordersQuery = Order::query();

        if (! empty($this->startDate) && ! empty($this->endDate)) {
            $ordersQuery->whereBetween('created_at', [
                $this->startDate.' 00:00:00',
                $this->endDate.' 23:59:59',
            ]);
        }

        $periodRevenue = (clone $ordersQuery)->sum('total_amount');
        $periodOrders = (clone $ordersQuery)->count();
        $averageOrder = $periodOrders > 0 ? ($periodRevenue / $periodOrders) : 0;

        $topProductsQuery = OrderItem::select('product_name', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(subtotal) as total_sales'))
            ->groupBy('product_name')
            ->orderByDesc('total_qty')
            ->take(5);

        if (! empty($this->startDate) && ! empty($this->endDate)) {
            $topProductsQuery->whereBetween('created_at', [
                $this->startDate.' 00:00:00',
                $this->endDate.' 23:59:59',
            ]);
        }

        $topProducts = $topProductsQuery->get();

        $allTimeRevenue = Order::sum('total_amount');
        $allTimeOrders = Order::count();

        $chartData = $this->getChartData();

        return view('livewire.report.report-index', array_merge([
            'periodRevenue' => $periodRevenue,
            'periodOrders' => $periodOrders,
            'averageOrder' => $averageOrder,
            'topProducts' => $topProducts,
            'allTimeRevenue' => $allTimeRevenue,
            'allTimeOrders' => $allTimeOrders,
        ], $chartData));
    }

    public function getChartData(): array
    {
        $ordersQuery = Order::query();

        if (! empty($this->startDate) && ! empty($this->endDate)) {
            $ordersQuery->whereBetween('created_at', [
                $this->startDate.' 00:00:00',
                $this->endDate.' 23:59:59',
            ]);
        }

        $isSingleDay = (! empty($this->startDate) && ! empty($this->endDate) && $this->startDate === $this->endDate);

        $chartLabels = [];
        $chartSales = [];
        $chartOrders = [];

        if ($isSingleDay) {
            $hourlyData = (clone $ordersQuery)
                ->select(DB::raw('HOUR(created_at) as hour'), DB::raw('SUM(total_amount) as total_sales'), DB::raw('COUNT(*) as total_orders'))
                ->groupBy(DB::raw('HOUR(created_at)'))
                ->orderBy('hour')
                ->get()
                ->keyBy('hour');

            for ($h = 8; $h <= 22; $h++) {
                $chartLabels[] = sprintf('%02d:00', $h);
                $chartSales[] = (int) ($hourlyData[$h]->total_sales ?? 0);
                $chartOrders[] = (int) ($hourlyData[$h]->total_orders ?? 0);
            }
        } elseif (! empty($this->startDate) && ! empty($this->endDate)) {
            $dailyData = (clone $ordersQuery)
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total_amount) as total_sales'), DB::raw('COUNT(*) as total_orders'))
                ->groupBy(DB::raw('DATE(created_at)'))
                ->get()
                ->keyBy('date');

            $current = Carbon::parse($this->startDate);
            $end = Carbon::parse($this->endDate);
            $daysDiff = $current->diffInDays($end);

            if ($daysDiff <= 31) {
                while ($current <= $end) {
                    $dateKey = $current->format('Y-m-d');
                    $chartLabels[] = $current->format('d M');
                    $chartSales[] = (int) ($dailyData[$dateKey]->total_sales ?? 0);
                    $chartOrders[] = (int) ($dailyData[$dateKey]->total_orders ?? 0);
                    $current->addDay();
                }
            } else {
                foreach ($dailyData->sortBy('date') as $dateKey => $row) {
                    $chartLabels[] = date('d M', strtotime($dateKey));
                    $chartSales[] = (int) $row->total_sales;
                    $chartOrders[] = (int) $row->total_orders;
                }
            }

            if (empty($chartLabels)) {
                $chartLabels = [now()->format('d M')];
                $chartSales = [0];
                $chartOrders = [0];
            }
        } else {
            $dailyData = (clone $ordersQuery)
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total_amount) as total_sales'), DB::raw('COUNT(*) as total_orders'))
                ->groupBy(DB::raw('DATE(created_at)'))
                ->orderBy('date')
                ->get();

            foreach ($dailyData as $row) {
                $chartLabels[] = date('d M', strtotime($row->date));
                $chartSales[] = (int) $row->total_sales;
                $chartOrders[] = (int) $row->total_orders;
            }

            if (empty($chartLabels)) {
                $chartLabels = [now()->format('d M')];
                $chartSales = [0];
                $chartOrders = [0];
            }
        }

        // Payment method distribution
        $paymentData = (clone $ordersQuery)
            ->select('payment_method', DB::raw('COUNT(*) as count'), DB::raw('SUM(total_amount) as total'))
            ->groupBy('payment_method')
            ->get();

        $paymentLabels = [];
        $paymentCounts = [];
        $paymentTotals = [];

        foreach ($paymentData as $pm) {
            $label = match (strtolower($pm->payment_method ?? 'cash')) {
                'cash' => 'Tunai (Cash)',
                'qris' => 'QRIS',
                'transfer' => 'Transfer Bank',
                default => strtoupper($pm->payment_method),
            };
            $paymentLabels[] = $label;
            $paymentCounts[] = (int) $pm->count;
            $paymentTotals[] = (int) $pm->total;
        }

        return [
            'chartLabels' => $chartLabels,
            'chartSales' => $chartSales,
            'chartOrders' => $chartOrders,
            'paymentLabels' => $paymentLabels,
            'paymentCounts' => $paymentCounts,
            'paymentTotals' => $paymentTotals,
        ];
    }
}
