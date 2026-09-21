<?php

namespace App\Livewire\Report;

use App\Models\Order;
use App\Models\OrderItem;
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
    }

    public function updatedStartDate()
    {
        $this->dateRange = 'custom';
    }

    public function updatedEndDate()
    {
        $this->dateRange = 'custom';
    }

    public function exportCsv()
    {
        $ordersQuery = Order::with(['items', 'table', 'user']);

        if (! empty($this->startDate) && ! empty($this->endDate)) {
            $ordersQuery->whereBetween('created_at', [
                $this->startDate.' 00:00:00',
                $this->endDate.' 23:59:59',
            ]);
        }

        $orders = $ordersQuery->latest()->get();
        $totalRevenue = $orders->sum('total_amount');
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

        $filename = 'Laporan-Penjualan-BrewPOS-'.date('Ymd-His').'.csv';

        return response()->streamDownload(function () use ($orders, $topProducts, $periodLabel, $totalRevenue, $totalOrders) {
            $handle = fopen('php://output', 'w');

            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, ['LAPORAN PENJUALAN - BREWPOS COFFEE']);
            fputcsv($handle, ['Periode', $periodLabel]);
            fputcsv($handle, ['Waktu Ekspor', now()->format('d/m/Y H:i:s')]);
            fputcsv($handle, ['Total Omset', 'Rp '.number_format($totalRevenue, 0, ',', '.')]);
            fputcsv($handle, ['Total Transaksi', $totalOrders]);
            fputcsv($handle, []);

            fputcsv($handle, ['RINCIAN TRANSAKSI']);
            fputcsv($handle, [
                'No',
                'Nomor Invoice',
                'Tanggal',
                'Waktu',
                'Nama Pelanggan',
                'Tipe Pesanan',
                'Meja',
                'Kasir',
                'Metode Pembayaran',
                'Status Pembayaran',
                'Status Dapur',
                'Subtotal (Rp)',
                'Diskon (Rp)',
                'PPN (Rp)',
                'Total Akhir (Rp)',
            ]);

            $no = 1;
            foreach ($orders as $order) {
                fputcsv($handle, [
                    $no++,
                    $order->invoice_number,
                    $order->created_at->format('Y-m-d'),
                    $order->created_at->format('H:i:s'),
                    $order->customer_name,
                    strtoupper($order->order_type),
                    $order->table ? $order->table->table_number : '-',
                    $order->user ? $order->user->name : '-',
                    strtoupper($order->payment_method),
                    strtoupper($order->payment_status),
                    strtoupper($order->order_status),
                    $order->subtotal,
                    $order->discount_amount,
                    $order->tax_amount,
                    $order->total_amount,
                ]);
            }

            fputcsv($handle, []);

            fputcsv($handle, ['REKAPITULASI PENJUALAN PRODUK']);
            fputcsv($handle, ['No', 'Nama Menu', 'Kuantitas Terjual', 'Total Omset (Rp)']);

            $pNo = 1;
            foreach ($topProducts as $tp) {
                fputcsv($handle, [
                    $pNo++,
                    $tp->product_name,
                    $tp->total_qty,
                    $tp->total_sales,
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
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

        // Chart 1: Trend dataset
        $isSingleDay = (! empty($this->startDate) && ! empty($this->endDate) && $this->startDate === $this->endDate);

        if ($isSingleDay) {
            $hourlyData = (clone $ordersQuery)
                ->select(DB::raw('HOUR(created_at) as hour'), DB::raw('SUM(total_amount) as total_sales'), DB::raw('COUNT(*) as total_orders'))
                ->groupBy(DB::raw('HOUR(created_at)'))
                ->orderBy('hour')
                ->get()
                ->keyBy('hour');

            $chartLabels = [];
            $chartSales = [];
            $chartOrders = [];

            for ($h = 8; $h <= 22; $h++) {
                $chartLabels[] = sprintf('%02d:00', $h);
                $chartSales[] = (int) ($hourlyData[$h]->total_sales ?? 0);
                $chartOrders[] = (int) ($hourlyData[$h]->total_orders ?? 0);
            }
        } else {
            $dailyData = (clone $ordersQuery)
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total_amount) as total_sales'), DB::raw('COUNT(*) as total_orders'))
                ->groupBy(DB::raw('DATE(created_at)'))
                ->orderBy('date')
                ->get();

            $chartLabels = [];
            $chartSales = [];
            $chartOrders = [];

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

        // Chart 2: Payment method dataset
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

        if (empty($paymentLabels)) {
            $paymentLabels = ['Tunai (Cash)'];
            $paymentCounts = [0];
            $paymentTotals = [0];
        }

        return view('livewire.report.report-index', [
            'periodRevenue' => $periodRevenue,
            'periodOrders' => $periodOrders,
            'averageOrder' => $averageOrder,
            'topProducts' => $topProducts,
            'allTimeRevenue' => $allTimeRevenue,
            'allTimeOrders' => $allTimeOrders,
            'chartLabels' => $chartLabels,
            'chartSales' => $chartSales,
            'chartOrders' => $chartOrders,
            'paymentLabels' => $paymentLabels,
            'paymentCounts' => $paymentCounts,
            'paymentTotals' => $paymentTotals,
        ]);
    }
}
