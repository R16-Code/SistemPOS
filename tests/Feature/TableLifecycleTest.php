<?php

namespace Tests\Feature;

use App\Livewire\Customer\CustomerOrder;
use App\Livewire\Kitchen\KitchenOrders;
use App\Livewire\Table\TableList;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Table;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TableLifecycleTest extends TestCase
{
    use RefreshDatabase;

    public function test_kitchen_marking_dine_in_order_as_served_keeps_table_occupied(): void
    {
        $table = Table::create([
            'table_number' => 'Meja 01',
            'capacity' => 4,
            'status' => 'occupied',
            'is_active' => true,
        ]);

        $order = Order::create([
            'invoice_number' => 'TRX-TEST-001',
            'table_id' => $table->id,
            'customer_name' => 'Budi',
            'order_source' => 'pos',
            'order_type' => 'dine_in',
            'subtotal' => 25000,
            'tax_amount' => 2750,
            'discount_amount' => 0,
            'total_amount' => 27750,
            'payment_method' => 'cash',
            'payment_status' => 'paid',
            'order_status' => 'ready',
        ]);

        Livewire::test(KitchenOrders::class)
            ->call('updateOrderStatus', $order->id, 'served');

        $this->assertEquals('served', $order->fresh()->order_status);
        $this->assertEquals('occupied', $table->fresh()->status);
    }

    public function test_kitchen_completing_takeaway_order(): void
    {
        $order = Order::create([
            'invoice_number' => 'TRX-TEST-002',
            'customer_name' => 'Siti',
            'order_source' => 'pos',
            'order_type' => 'takeaway',
            'subtotal' => 30000,
            'tax_amount' => 3300,
            'discount_amount' => 0,
            'total_amount' => 33300,
            'payment_method' => 'cash',
            'payment_status' => 'paid',
            'order_status' => 'ready',
        ]);

        Livewire::test(KitchenOrders::class)
            ->call('updateOrderStatus', $order->id, 'completed');

        $this->assertEquals('completed', $order->fresh()->order_status);
    }

    public function test_clear_table_marks_table_available_and_completes_active_orders(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $table = Table::create([
            'table_number' => 'Meja 02',
            'capacity' => 4,
            'status' => 'occupied',
            'is_active' => true,
        ]);

        $order1 = Order::create([
            'invoice_number' => 'TRX-TEST-003',
            'table_id' => $table->id,
            'customer_name' => 'Doni',
            'order_source' => 'pos',
            'order_type' => 'dine_in',
            'subtotal' => 50000,
            'tax_amount' => 5500,
            'discount_amount' => 0,
            'total_amount' => 55500,
            'payment_method' => 'cash',
            'payment_status' => 'paid',
            'order_status' => 'served',
        ]);

        $order2 = Order::create([
            'invoice_number' => 'TRX-TEST-004',
            'table_id' => $table->id,
            'customer_name' => 'Doni Teman',
            'order_source' => 'qr_table',
            'order_type' => 'dine_in',
            'subtotal' => 20000,
            'tax_amount' => 2200,
            'discount_amount' => 0,
            'total_amount' => 22200,
            'payment_method' => 'cash',
            'payment_status' => 'paid',
            'order_status' => 'ready',
        ]);

        Livewire::actingAs($admin)
            ->test(TableList::class)
            ->call('clearTable', $table->id);

        $this->assertEquals('available', $table->fresh()->status);
        $this->assertEquals('completed', $order1->fresh()->order_status);
        $this->assertEquals('completed', $order2->fresh()->order_status);
    }

    public function test_customer_order_detects_active_session_and_tags_add_on(): void
    {
        $category = Category::create([
            'name' => 'Minuman',
            'slug' => 'minuman',
            'is_active' => true,
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Es Kopi Susu',
            'slug' => 'es-kopi-susu',
            'sku' => 'KOP-001',
            'price' => 18000,
            'stock' => 50,
            'is_available' => true,
        ]);

        $table = Table::create([
            'table_number' => 'Meja 05',
            'capacity' => 4,
            'status' => 'occupied',
            'is_active' => true,
        ]);

        Order::create([
            'invoice_number' => 'TRX-TEST-005',
            'table_id' => $table->id,
            'customer_name' => 'Rangga',
            'order_source' => 'pos',
            'order_type' => 'dine_in',
            'subtotal' => 25000,
            'tax_amount' => 2750,
            'discount_amount' => 0,
            'total_amount' => 27750,
            'payment_method' => 'cash',
            'payment_status' => 'paid',
            'order_status' => 'served',
        ]);

        $component = Livewire::test(CustomerOrder::class, ['token' => $table->qr_token]);

        $this->assertNotNull($component->get('primaryActiveOrder'));
        $this->assertEquals('Rangga', $component->get('primaryActiveOrder')->customer_name);

        // Place add-on order
        $component->call('selectProductForOrder', $product->id)
            ->set('customerName', 'Cinta')
            ->set('customerNotes', 'Gula sedikit')
            ->set('paymentChoice', 'cashier')
            ->call('submitOrder');

        $latestOrder = Order::latest('id')->first();
        $this->assertEquals('Cinta', $latestOrder->customer_name);
        $this->assertStringContainsString('[Pesanan Tambahan / Add-on]', $latestOrder->notes);
        $this->assertStringContainsString('Gula sedikit', $latestOrder->notes);
        $this->assertEquals('occupied', $table->fresh()->status);
    }
}
