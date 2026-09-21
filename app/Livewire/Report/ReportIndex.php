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

        // Top selling products for this period
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

        // Overall all-time stats for comparison
        $allTimeRevenue = Order::sum('total_amount');
        $allTimeOrders = Order::count();

        return view('livewire.report.report-index', [
            'periodRevenue' => $periodRevenue,
            'periodOrders' => $periodOrders,
            'averageOrder' => $averageOrder,
            'topProducts' => $topProducts,
            'allTimeRevenue' => $allTimeRevenue,
            'allTimeOrders' => $allTimeOrders,
        ]);
    }
}
