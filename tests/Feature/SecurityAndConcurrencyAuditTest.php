<?php

namespace Tests\Feature;

use App\Livewire\Customer\CustomerOrder;
use App\Livewire\Pos\Cashier;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Table;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SecurityAndConcurrencyAuditTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Setting::set('tax_enabled', '0', 'tax');
        Setting::set('tax_percentage', '0', 'tax');
    }

    public function test_customer_order_prevents_price_tampering(): void
    {
        $category = Category::create(['name' => 'Coffee', 'slug' => 'coffee', 'is_active' => true]);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Caramel Macchiato',
            'sku' => 'CF-001',
            'price' => 35000,
            'stock' => 10,
            'is_available' => true,
        ]);

        $table = Table::create([
            'table_number' => 'Meja 01',
            'capacity' => 2,
            'status' => 'available',
            'is_active' => true,
        ]);

        // Malicious client tries to send tampered price of Rp 100 instead of Rp 35.000
        $tamperedCart = [
            'p_'.$product->id.'_base' => [
                'product_id' => $product->id,
                'name' => 'Caramel Macchiato',
                'price' => 100, // Tampered!
                'variant_ids' => [],
                'quantity' => 2,
                'notes' => '',
                'subtotal' => 200, // Tampered!
                'image_path' => null,
            ],
        ];

        Livewire::test(CustomerOrder::class, ['token' => $table->qr_token])
            ->set('cart', $tamperedCart)
            ->set('customerName', 'Hacker')
            ->set('paymentChoice', 'cashier')
            ->call('submitOrder')
            ->assertHasNoErrors();

        $order = Order::latest('id')->first();
        $this->assertNotNull($order);

        // Assert backend strictly recalculated from database price (35,000 * 2 = 70,000)
        $this->assertEquals(70000, $order->subtotal);
        $this->assertEquals(70000, $order->total_amount);

        $item = $order->items->first();
        $this->assertEquals(35000, $item->unit_price);
        $this->assertEquals(70000, $item->subtotal);

        // Assert stock correctly decremented
        $this->assertEquals(8, $product->fresh()->stock);
    }

    public function test_customer_order_prevents_overselling_when_stock_insufficient(): void
    {
        $category = Category::create(['name' => 'Pastry', 'slug' => 'pastry', 'is_active' => true]);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Croissant',
            'sku' => 'BK-001',
            'price' => 20000,
            'stock' => 2, // Only 2 left
            'is_available' => true,
        ]);

        $table = Table::create([
            'table_number' => 'Meja 02',
            'capacity' => 4,
            'status' => 'available',
            'is_active' => true,
        ]);

        // Request 3 items when only 2 exist
        $cart = [
            'p_'.$product->id.'_base' => [
                'product_id' => $product->id,
                'name' => 'Croissant',
                'price' => 20000,
                'variant_ids' => [],
                'quantity' => 3,
                'notes' => '',
                'subtotal' => 60000,
            ],
        ];

        Livewire::test(CustomerOrder::class, ['token' => $table->qr_token])
            ->set('cart', $cart)
            ->set('customerName', 'Budi')
            ->set('paymentChoice', 'cashier')
            ->call('submitOrder')
            ->assertHasErrors(['cart']);

        // Assert no order was created and stock remains 2
        $this->assertEquals(0, Order::count());
        $this->assertEquals(2, $product->fresh()->stock);
    }

    public function test_cashier_prevents_overselling_and_recalculates_authoritative_total(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $category = Category::create(['name' => 'Coffee', 'slug' => 'coffee', 'is_active' => true]);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Americano',
            'sku' => 'CF-002',
            'price' => 22000,
            'stock' => 1,
            'is_available' => true,
        ]);

        $table = Table::create([
            'table_number' => 'Meja 03',
            'capacity' => 2,
            'status' => 'available',
            'is_active' => true,
        ]);

        // Cashier tries to process order with quantity exceeding remaining stock
        $cart = [
            'p_'.$product->id.'_base' => [
                'product_id' => $product->id,
                'name' => 'Americano',
                'price' => 22000,
                'variant_ids' => [],
                'quantity' => 2, // Exceeds stock (1)
                'notes' => '',
                'subtotal' => 44000,
            ],
        ];

        Livewire::actingAs($admin)
            ->test(Cashier::class)
            ->set('cart', $cart)
            ->set('selectedTableId', $table->id)
            ->set('orderType', 'dine_in')
            ->set('paymentMethod', 'cash')
            ->set('paidAmount', 50000)
            ->call('processPayment')
            ->assertHasErrors(['payment']);

        $this->assertEquals(0, Order::count());
        $this->assertEquals(1, $product->fresh()->stock);
    }
}
