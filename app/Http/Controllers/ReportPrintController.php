<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportPrintController extends Controller
{
    public function printReport(Request $request): View
    {
        $startDate = $request->query('startDate');
        $endDate = $request->query('endDate');

        $ordersQuery = Order::with(['items', 'table', 'user']);

        if (! empty($startDate) && ! empty($endDate)) {
            $ordersQuery->whereBetween('created_at', [
                $startDate.' 00:00:00',
                $endDate.' 23:59:59',
            ]);
        }

        $orders = $ordersQuery->latest()->get();

        $periodRevenue = $orders->sum('total_amount');
        $periodOrders = $orders->count();
        $averageOrder = $periodOrders > 0 ? ($periodRevenue / $periodOrders) : 0;

        $topProductsQuery = OrderItem::select('product_name', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(subtotal) as total_sales'))
            ->groupBy('product_name')
            ->orderByDesc('total_qty');

        if (! empty($startDate) && ! empty($endDate)) {
            $topProductsQuery->whereBetween('created_at', [
                $startDate.' 00:00:00',
                $endDate.' 23:59:59',
            ]);
        }
        $topProducts = $topProductsQuery->get();

        $periodLabel = (! empty($startDate) && ! empty($endDate))
            ? date('d M Y', strtotime($startDate)).' - '.date('d M Y', strtotime($endDate))
            : 'Seluruh Periode Operasional';

        return view('reports.report-print', [
            'orders' => $orders,
            'topProducts' => $topProducts,
            'periodRevenue' => $periodRevenue,
            'periodOrders' => $periodOrders,
            'averageOrder' => $averageOrder,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'periodLabel' => $periodLabel,
        ]);
    }
}
