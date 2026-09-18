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

    public function render()
    {
        $ordersQuery = Order::with(['user', 'table', 'items'])->latest();

        if (!empty($this->search)) {
            $ordersQuery->where('invoice_number', 'like', '%' . $this->search . '%')
                ->orWhere('customer_name', 'like', '%' . $this->search . '%');
        }

        if (!empty($this->paymentStatus)) {
            $ordersQuery->where('payment_status', $this->paymentStatus);
        }

        return view('livewire.order.order-list', [
            'orders' => $ordersQuery->paginate(10),
        ]);
    }
}
