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
    public function render()
    {
        $todayRevenue = Order::whereDate('created_at', now())->sum('total_amount');
        $todayOrders = Order::whereDate('created_at', now())->count();
        $totalRevenue = Order::sum('total_amount');
        $totalOrdersCount = Order::count();

        // Top selling products
        $topProducts = OrderItem::select('product_name', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(subtotal) as total_sales'))
            ->groupBy('product_name')
            ->orderByDesc('total_qty')
            ->take(5)
            ->get();

        return view('livewire.report.report-index', [
            'todayRevenue' => $todayRevenue,
            'todayOrders' => $todayOrders,
            'totalRevenue' => $totalRevenue,
            'totalOrdersCount' => $totalOrdersCount,
            'topProducts' => $topProducts,
        ]);
    }
}
