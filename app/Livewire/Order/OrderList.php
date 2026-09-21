<?php

namespace App\Livewire\Order;

use App\Models\Order;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Daftar Transaksi - BrewPOS')]
class OrderList extends Component
{
    use WithPagination;

    public $search = '';

    public $paymentStatus = '';

    public $orderStatus = '';

    public $dateRange = 'all'; // all, today, this_week, this_month, custom

    public $startDate = null;

    public $endDate = null;

    public ?Order $selectedOrderForReceipt = null;

    public bool $showReceiptModal = false;

    public ?Order $selectedOrderDetail = null;

    public bool $showOrderDetailModal = false;

    public function mount()
    {
        // Default to all or empty
    }

    public function setDateRange($range)
    {
        $this->dateRange = $range;
        $this->resetPage();

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
        $this->resetPage();
    }

    public function updatedEndDate()
    {
        $this->dateRange = 'custom';
        $this->resetPage();
    }

    public function updateOrderStatus(int $orderId, string $status): void
    {
        if (! in_array(auth()->user()?->role, ['admin', 'kitchen'])) {
            return;
        }

        $validStatuses = ['pending', 'cooking', 'ready', 'completed', 'cancelled'];
        if (! in_array($status, $validStatuses)) {
            return;
        }

        $order = Order::findOrFail($orderId);
        $order->update([
            'order_status' => $status,
        ]);

        if ($status === 'completed' && $order->table_id) {
            $order->table?->update(['status' => 'available']);
        }
    }

    public function showReceipt(int $orderId): void
    {
        $this->selectedOrderForReceipt = Order::with(['items', 'table', 'user'])->findOrFail($orderId);
        $this->showReceiptModal = true;
    }

    public function closeReceiptModal(): void
    {
        $this->showReceiptModal = false;
        $this->selectedOrderForReceipt = null;
    }

    public function showOrderDetail(int $orderId): void
    {
        $this->selectedOrderDetail = Order::with(['items', 'table', 'user'])->findOrFail($orderId);
        $this->showOrderDetailModal = true;
    }

    public function closeOrderDetailModal(): void
    {
        $this->showOrderDetailModal = false;
        $this->selectedOrderDetail = null;
    }

    public function render()
    {
        $ordersQuery = Order::with(['user', 'table', 'items'])->latest();

        if (! empty($this->search)) {
            $ordersQuery->where(function ($q) {
                $q->where('invoice_number', 'like', '%'.$this->search.'%')
                    ->orWhere('customer_name', 'like', '%'.$this->search.'%');
            });
        }

        if (! empty($this->paymentStatus)) {
            $ordersQuery->where('payment_status', $this->paymentStatus);
        }

        if (! empty($this->orderStatus)) {
            $ordersQuery->where('order_status', $this->orderStatus);
        }

        if (! empty($this->startDate) && ! empty($this->endDate)) {
            $ordersQuery->whereBetween('created_at', [
                $this->startDate.' 00:00:00',
                $this->endDate.' 23:59:59',
            ]);
        }

        return view('livewire.order.order-list', [
            'orders' => $ordersQuery->paginate(10),
        ]);
    }
}
