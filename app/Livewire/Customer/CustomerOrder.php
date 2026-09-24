<?php

namespace App\Livewire\Customer;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Setting;
use App\Models\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.customer')]
#[Title('Pesan Menu - BrewPOS')]
class CustomerOrder extends Component
{
    public Table $table;

    public string $token = '';

    public ?int $selectedCategory = null;

    public string $search = '';

    public array $cart = [];

    public bool $showCartModal = false;

    public bool $showVariantModal = false;

    public ?Product $selectedProduct = null;

    public array $selectedVariants = [];

    public string $itemNotes = '';

    public string $customerName = '';

    public string $customerPhone = '';

    public string $customerNotes = '';

    public string $paymentChoice = 'cashier'; // 'cashier' or 'gateway'

    public float $taxRate = 0.11;

    public string $taxName = 'PPN';

    public function mount(string $token): void
    {
        $this->token = $token;
        $this->table = Table::where('qr_token', $token)->firstOrFail();

        $taxEnabled = (bool) ((int) Setting::get('tax_enabled', '1'));
        $taxPercentage = (float) Setting::get('tax_percentage', 11);
        $this->taxRate = $taxEnabled ? ($taxPercentage / 100) : 0;
        $this->taxName = (string) Setting::get('tax_name', 'PPN');
    }

    public function selectCategory(?int $categoryId): void
    {
        $this->selectedCategory = $this->selectedCategory === $categoryId ? null : $categoryId;
    }

    public function selectProductForOrder(int $productId): void
    {
        $product = Product::with('variants')->find($productId);
        if (! $product || ! $product->is_available || $product->stock <= 0) {
            return;
        }

        if ($product->variants->isNotEmpty()) {
            $this->selectedProduct = $product;
            $this->selectedVariants = [];
            $this->itemNotes = '';

            $groups = $product->variants->groupBy('variant_group');
            foreach ($groups as $groupName => $variants) {
                $this->selectedVariants[$groupName] = $variants->first()->id;
            }

            $this->showVariantModal = true;

            return;
        }

        $this->pushToCart($product, [], 0, '');
    }

    public function confirmVariantSelection(): void
    {
        if (! $this->selectedProduct) {
            return;
        }

        $variantIds = array_values($this->selectedVariants);
        $variants = ProductVariant::whereIn('id', $variantIds)->get();

        $extraTotal = (float) $variants->sum('extra_price');
        $variantNames = $variants->pluck('name')->toArray();

        $notesList = [];
        if (! empty($variantNames)) {
            $notesList[] = implode(', ', $variantNames);
        }
        if (! empty(trim($this->itemNotes))) {
            $notesList[] = trim($this->itemNotes);
        }
        $finalNotes = implode(' | ', $notesList);

        $this->pushToCart($this->selectedProduct, $variantIds, $extraTotal, $finalNotes);
        $this->closeVariantModal();
    }

    public function closeVariantModal(): void
    {
        $this->showVariantModal = false;
        $this->selectedProduct = null;
        $this->selectedVariants = [];
        $this->itemNotes = '';
    }

    public function getVariantModalPriceProperty(): float
    {
        if (! $this->selectedProduct) {
            return 0;
        }

        $extra = 0;
        if (! empty($this->selectedVariants)) {
            $extra = (float) ProductVariant::whereIn('id', array_values($this->selectedVariants))->sum('extra_price');
        }

        return (float) $this->selectedProduct->price + $extra;
    }

    protected function pushToCart(Product $product, array $variantIds, float $extraTotal, string $notes): void
    {
        $finalPrice = (float) $product->price + $extraTotal;
        $variantHash = ! empty($variantIds) ? implode('_', $variantIds) : 'base';
        $key = 'p_'.$product->id.'_'.md5($variantHash.$notes);

        if (isset($this->cart[$key])) {
            $this->cart[$key]['quantity'] += 1;
            $this->cart[$key]['subtotal'] = $this->cart[$key]['quantity'] * $this->cart[$key]['price'];
        } else {
            $this->cart[$key] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'price' => $finalPrice,
                'quantity' => 1,
                'notes' => $notes,
                'subtotal' => $finalPrice,
                'image_path' => $product->image_path,
            ];
        }
    }

    public function updateQuantity(string $key, int $delta): void
    {
        if (isset($this->cart[$key])) {
            $newQty = $this->cart[$key]['quantity'] + $delta;
            if ($newQty <= 0) {
                unset($this->cart[$key]);
            } else {
                $this->cart[$key]['quantity'] = $newQty;
                $this->cart[$key]['subtotal'] = $newQty * $this->cart[$key]['price'];
            }
        }
    }

    public function removeFromCart(string $key): void
    {
        unset($this->cart[$key]);
    }

    public function clearCart(): void
    {
        $this->cart = [];
    }

    public function getSubtotalProperty(): float
    {
        return (float) array_sum(array_column($this->cart, 'subtotal'));
    }

    public function getTaxAmountProperty(): float
    {
        return $this->subtotal * $this->taxRate;
    }

    public function getTotalAmountProperty(): float
    {
        return $this->subtotal + $this->taxAmount;
    }

    public function getTotalItemsCountProperty(): int
    {
        return (int) array_sum(array_column($this->cart, 'quantity'));
    }

    public function getActiveOrdersProperty(): Collection
    {
        return Order::with(['items'])
            ->where('table_id', $this->table->id)
            ->where('created_at', '>=', now()->subHours(12))
            ->whereIn('order_status', ['pending_confirmation', 'pending', 'cooking', 'ready'])
            ->latest('id')
            ->get();
    }

    public function openCart(): void
    {
        if (! empty($this->cart)) {
            $this->showCartModal = true;
        }
    }

    public function closeCart(): void
    {
        $this->showCartModal = false;
    }

    public function submitOrder()
    {
        $this->validate([
            'customerName' => 'required|string|min:2|max:50',
            'customerPhone' => 'nullable|string|max:20',
            'paymentChoice' => 'required|in:cashier,gateway',
        ], [
            'customerName.required' => 'Mohon masukkan nama pemesan.',
            'customerName.min' => 'Nama minimal 2 karakter.',
        ]);

        if (empty($this->cart)) {
            $this->addError('cart', 'Keranjang pesanan masih kosong.');

            return;
        }

        DB::beginTransaction();
        try {
            foreach ($this->cart as $item) {
                $currentProduct = Product::where('id', $item['product_id'])->lockForUpdate()->first();
                if (! $currentProduct || ! $currentProduct->is_available || $currentProduct->stock < $item['quantity']) {
                    DB::rollBack();
                    $this->addError('cart', 'Stok untuk menu "'.$item['name'].'" tidak mencukupi.');

                    return;
                }
            }

            $invoiceNumber = 'TRX-'.date('Ymd').'-'.str_pad(Order::whereDate('created_at', now())->count() + 1, 4, '0', STR_PAD_LEFT);

            $isGateway = $this->paymentChoice === 'gateway';

            $order = Order::create([
                'invoice_number' => $invoiceNumber,
                'user_id' => null,
                'table_id' => $this->table->id,
                'customer_name' => trim($this->customerName),
                'customer_phone' => trim($this->customerPhone) ?: null,
                'order_source' => 'qr_table',
                'order_type' => 'dine_in',
                'subtotal' => $this->subtotal,
                'tax_amount' => $this->taxAmount,
                'discount_amount' => 0,
                'total_amount' => $this->totalAmount,
                'payment_method' => $isGateway ? 'qris' : 'unpaid',
                'payment_status' => $isGateway ? 'paid' : 'pending',
                'order_status' => 'pending_confirmation',
                'paid_amount' => $isGateway ? $this->totalAmount : 0,
                'change_amount' => 0,
                'notes' => trim($this->customerNotes) ?: null,
            ]);

            foreach ($this->cart as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'product_name' => $item['name'],
                    'unit_price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'subtotal' => $item['subtotal'],
                    'notes' => $item['notes'],
                ]);

                Product::where('id', $item['product_id'])->decrement('stock', $item['quantity']);
            }

            $this->table->update(['status' => 'occupied']);

            DB::commit();

            return redirect()->route('customer.order.status', [
                'token' => $this->token,
                'invoice' => $order->invoice_number,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->addError('cart', 'Gagal membuat pesanan: '.$e->getMessage());
        }
    }

    public function render()
    {
        $categories = Category::where('is_active', true)->get();

        $productsQuery = Product::with('variants')
            ->where('is_available', true);

        if ($this->selectedCategory) {
            $productsQuery->where('category_id', $this->selectedCategory);
        }

        if (! empty(trim($this->search))) {
            $searchTerm = '%'.trim($this->search).'%';
            $productsQuery->where('name', 'like', $searchTerm);
        }

        $products = $productsQuery->orderBy('name')->get();

        return view('livewire.customer.customer-order', [
            'categories' => $categories,
            'products' => $products,
        ]);
    }
}
