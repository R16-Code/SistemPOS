<?php

namespace App\Livewire\Customer;

use App\Models\Order;
use App\Models\Table;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.customer')]
#[Title('Status Pesanan - BrewPOS')]
class CustomerOrderStatus extends Component
{
    public Table $table;

    public string $token = '';

    public string $invoice = '';

    public function mount(string $token, string $invoice): void
    {
        $this->token = $token;
        $this->invoice = $invoice;
        $this->table = Table::where('qr_token', $token)->firstOrFail();
    }

    public function render()
    {
        $order = Order::with(['items', 'table'])
            ->where('invoice_number', $this->invoice)
            ->where('table_id', $this->table->id)
            ->firstOrFail();

        return view('livewire.customer.customer-order-status', [
            'order' => $order,
        ]);
    }
}
