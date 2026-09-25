<?php

namespace App\Livewire\Kitchen;

use App\Models\Order;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Antrian Pesanan Dapur - BrewPOS')]
class KitchenOrders extends Component
{
    public string $activeTab = 'all_active'; // all_active, pending, cooking, ready, served, completed

    public string $search = '';

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function updateOrderStatus(int $orderId, string $status): void
    {
        $validStatuses = ['pending', 'cooking', 'ready', 'served', 'completed', 'cancelled'];
        if (! in_array($status, $validStatuses)) {
            return;
        }

        $order = Order::findOrFail($orderId);
        $order->update([
            'order_status' => $status,
        ]);

        // When served for dine-in, ensure the table remains occupied while customer enjoys meal
        if ($status === 'served' && $order->table_id) {
            $order->table?->update(['status' => 'occupied']);
        }

        // When completed, only free table if there are no other active orders at this table
        if ($status === 'completed' && $order->table_id) {
            $hasOtherActiveOrders = Order::where('table_id', $order->table_id)
                ->where('id', '!=', $order->id)
                ->whereIn('order_status', ['pending_confirmation', 'pending', 'cooking', 'ready', 'served'])
                ->exists();

            if (! $hasOtherActiveOrders) {
                $order->table?->update(['status' => 'available']);
            }
        }
    }

    public function render()
    {
        // Status counts for active overview
        $pendingCount = Order::where('order_status', 'pending')->count();
        $cookingCount = Order::where('order_status', 'cooking')->count();
        $readyCount = Order::where('order_status', 'ready')->count();
        $servedCount = Order::where('order_status', 'served')->whereDate('created_at', now())->count();
        $completedCount = Order::whereDate('created_at', now())->where('order_status', 'completed')->count();

        $query = Order::with(['table', 'items', 'user']);

        if (! empty($this->search)) {
            $query->where(function ($q) {
                $q->where('invoice_number', 'like', '%'.$this->search.'%')
                    ->orWhere('customer_name', 'like', '%'.$this->search.'%');
            });
        }

        if ($this->activeTab === 'all_active') {
            $query->whereIn('order_status', ['pending', 'cooking', 'ready'])->orderBy('created_at', 'asc');
        } elseif ($this->activeTab === 'pending') {
            $query->where('order_status', 'pending')->orderBy('created_at', 'asc');
        } elseif ($this->activeTab === 'cooking') {
            $query->where('order_status', 'cooking')->orderBy('created_at', 'asc');
        } elseif ($this->activeTab === 'ready') {
            $query->where('order_status', 'ready')->orderBy('updated_at', 'desc');
        } elseif ($this->activeTab === 'served') {
            $query->where('order_status', 'served')->whereDate('created_at', now())->orderBy('updated_at', 'desc');
        } elseif ($this->activeTab === 'completed') {
            $query->where('order_status', 'completed')->whereDate('created_at', now())->orderBy('updated_at', 'desc');
        }

        $orders = $query->get();

        return view('livewire.kitchen.kitchen-orders', [
            'orders' => $orders,
            'pendingCount' => $pendingCount,
            'cookingCount' => $cookingCount,
            'readyCount' => $readyCount,
            'servedCount' => $servedCount,
            'completedCount' => $completedCount,
        ]);
    }
}
