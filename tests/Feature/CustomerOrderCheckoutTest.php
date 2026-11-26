<?php

namespace Tests\Feature;

use App\Livewire\Customer\CustomerOrder;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Setting;
use App\Models\Table;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CustomerOrderCheckoutTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Setting::set('tax_enabled', '1', 'tax');
        Setting::set('tax_name', 'PPN', 'tax');
        Setting::set('tax_percentage', '11', 'tax');
    }

    public function test_customer_can_complete_qr_checkout_with_variants_and_cashier_payment(): void
    {
        $category = Category::create(['name' => 'Main Course', 'slug' => 'main-course', 'is_active' => true]);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Nasi Goreng Special',
            'sku' => 'MC-001',
            'price' => 32000,
            'stock' => 10,
            'is_available' => true,
        ]);
        $variantNormal = ProductVariant::create([
            'product_id' => $product->id,
            'variant_group' => 'Level',
            'name' => 'Sedang',
            'extra_price' => 0,
        ]);
        $variantSpicy = ProductVariant::create([
            'product_id' => $product->id,
            'variant_group' => 'Level',
            'name' => 'Pedas',
            'extra_price' => 3000,
        ]);

        $table = Table::create([
            'table_number' => 'Meja 01',
            'capacity' => 4,
            'status' => 'available',
            'is_active' => true,
        ]);

        $component = Livewire::test(CustomerOrder::class, ['token' => $table->qr_token])
            ->call('selectProductForOrder', $product->id)
            ->assertSet('showVariantModal', true)
            ->assertSet('selectedVariants.Level', $variantNormal->id)
            ->call('selectVariant', 'Level', $variantSpicy->id)
            ->set('itemNotes', 'Tanpa acar')
            ->call('confirmVariantSelection')
            ->assertSet('showVariantModal', false)
            ->call('openCart')
            ->assertSet('showCartModal', true)
            ->set('customerName', '  Andi Wijaya  ')
            ->set('customerPhone', '081234567890')
            ->set('customerNotes', 'Antar cepat')
            ->set('paymentChoice', 'cashier')
            ->call('submitOrder')
            ->assertHasNoErrors();

        $order = Order::with('items')->latest('id')->first();
        $this->assertNotNull($order);

        $component->assertRedirect(route('customer.order.status', [
            'token' => $table->qr_token,
            'invoice' => $order->invoice_number,
        ]));

        $this->assertEquals('Andi Wijaya', $order->customer_name);
        $this->assertEquals('081234567890', $order->customer_phone);
        $this->assertEquals('qr_table', $order->order_source);
        $this->assertEquals('unpaid', $order->payment_method);
        $this->assertEquals('pending', $order->payment_status);
        $this->assertEquals('pending_confirmation', $order->order_status);
        $this->assertEquals(35000, (float) $order->subtotal);
        $this->assertEquals(round(35000 * 0.11), (float) $order->tax_amount);
        $this->assertEquals(35000 + round(35000 * 0.11), (float) $order->total_amount);
        $this->assertEquals(9, $product->fresh()->stock);
        $this->assertEquals('occupied', $table->fresh()->status);

        $this->assertCount(1, $order->items);
        $this->assertEquals('Pedas | Tanpa acar', $order->items->first()->notes);
    }

    public function test_customer_can_complete_qr_checkout_with_gateway_payment(): void
    {
        $category = Category::create(['name' => 'Snack', 'slug' => 'snack', 'is_active' => true]);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Butter Croissant',
            'sku' => 'SN-001',
            'price' => 20000,
            'stock' => 5,
            'is_available' => true,
        ]);

        $table = Table::create([
            'table_number' => 'Meja 02',
            'capacity' => 2,
            'status' => 'available',
            'is_active' => true,
        ]);

        Livewire::test(CustomerOrder::class, ['token' => $table->qr_token])
            ->call('selectProductForOrder', $product->id)
            ->call('openCart')
            ->set('customerName', 'Sinta')
            ->set('paymentChoice', 'gateway')
            ->call('submitOrder')
            ->assertHasNoErrors();

        $order = Order::latest('id')->first();
        $this->assertNotNull($order);
        $this->assertEquals('qris', $order->payment_method);
        $this->assertEquals('paid', $order->payment_status);
        $this->assertEquals((float) $order->total_amount, (float) $order->paid_amount);
    }

    public function test_checkout_validates_trimmed_customer_name_and_phone_and_dispatches_error_event(): void
    {
        $category = Category::create(['name' => 'Snack', 'slug' => 'snack', 'is_active' => true]);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'French Fries',
            'sku' => 'SN-002',
            'price' => 18000,
            'stock' => 10,
            'is_available' => true,
        ]);

        $table = Table::create([
            'table_number' => 'Meja 03',
            'capacity' => 4,
            'status' => 'available',
            'is_active' => true,
        ]);

        // Whitespace-only customer name should fail required validation
        Livewire::test(CustomerOrder::class, ['token' => $table->qr_token])
            ->call('selectProductForOrder', $product->id)
            ->call('openCart')
            ->set('customerName', '   ')
            ->call('submitOrder')
            ->assertHasErrors(['customerName' => 'required'])
            ->assertDispatched('checkout-error');

        // Overly long phone number should fail max:20 validation
        Livewire::test(CustomerOrder::class, ['token' => $table->qr_token])
            ->call('selectProductForOrder', $product->id)
            ->call('openCart')
            ->set('customerName', 'Budi')
            ->set('customerPhone', '0812345678901234567890123')
            ->call('submitOrder')
            ->assertHasErrors(['customerPhone' => 'max'])
            ->assertDispatched('checkout-error');

        $this->assertEquals(0, Order::count());
    }

    public function test_checkout_blocks_order_when_table_is_deactivated(): void
    {
        $category = Category::create(['name' => 'Snack', 'slug' => 'snack', 'is_active' => true]);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Brownie',
            'sku' => 'SN-003',
            'price' => 20000,
            'stock' => 5,
            'is_available' => true,
        ]);

        $table = Table::create([
            'table_number' => 'Meja 04',
            'capacity' => 4,
            'status' => 'available',
            'is_active' => true,
        ]);

        $component = Livewire::test(CustomerOrder::class, ['token' => $table->qr_token])
            ->call('selectProductForOrder', $product->id)
            ->call('openCart')
            ->set('customerName', 'Budi');

        $table->update(['is_active' => false]);

        $component->call('submitOrder')
            ->assertHasErrors(['cart'])
            ->assertDispatched('checkout-error');

        $this->assertEquals(0, Order::count());
    }

    public function test_reducing_last_cart_item_to_zero_closes_cart_modal(): void
    {
        $category = Category::create(['name' => 'Snack', 'slug' => 'snack', 'is_active' => true]);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Muffin',
            'sku' => 'SN-004',
            'price' => 19000,
            'stock' => 5,
            'is_available' => true,
        ]);

        $table = Table::create([
            'table_number' => 'Meja 05',
            'capacity' => 2,
            'status' => 'available',
            'is_active' => true,
        ]);

        $component = Livewire::test(CustomerOrder::class, ['token' => $table->qr_token])
            ->call('selectProductForOrder', $product->id)
            ->call('openCart')
            ->assertSet('showCartModal', true);

        $cartKey = array_key_first($component->get('cart'));

        $component->call('updateQuantity', $cartKey, -1)
            ->assertSet('cart', [])
            ->assertSet('showCartModal', false);
    }
}
