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
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
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
                'variant_ids' => $variantIds,
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
            ->whereIn('order_status', ['pending_confirmation', 'pending', 'cooking', 'ready', 'served'])
            ->latest('id')
            ->get();
    }

    public function getPrimaryActiveOrderProperty(): ?Order
    {
        return Order::where('table_id', $this->table->id)
            ->where('created_at', '>=', now()->subHours(12))
            ->whereIn('order_status', ['pending_confirmation', 'pending', 'cooking', 'ready', 'served'])
            ->oldest('id')
            ->first();
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

        // 1. Anti double-submit / spam order via Atomic Lock
        $lockKey = "order_submit_table_{$this->table->id}";
        $lock = Cache::lock($lockKey, 5);

        if (! $lock->get()) {
            $this->addError('cart', 'Pesanan Anda sedang diproses. Mohon tunggu...');

            return;
        }

        try {
            DB::beginTransaction();

            // 2. Consolidate required quantity per product_id to prevent multi-variant race conditions
            $productQuantities = [];
            foreach ($this->cart as $item) {
                $pid = (int) $item['product_id'];
                $qty = max(1, (int) $item['quantity']);
                $productQuantities[$pid] = ($productQuantities[$pid] ?? 0) + $qty;
            }

            // 3. Pessimistic Locking on all required products
            $lockedProducts = [];
            foreach ($productQuantities as $productId => $totalRequiredQty) {
                $product = Product::where('id', $productId)->lockForUpdate()->first();
                if (! $product || ! $product->is_available || $product->stock < $totalRequiredQty) {
                    DB::rollBack();
                    $productName = $product ? $product->name : 'Menu';
                    $availableStock = $product ? max(0, $product->stock) : 0;
                    $this->addError('cart', "Stok untuk menu '{$productName}' tidak mencukupi (tersisa {$availableStock}).");

                    return;
                }
                $lockedProducts[$productId] = $product;
            }

            // 4. Strict Price Tampering Prevention: Recalculate all totals from master DB
            $validatedItems = [];
            $serverSubtotal = 0;

            foreach ($this->cart as $item) {
                $productId = (int) $item['product_id'];
                $qty = max(1, (int) $item['quantity']);
                $lockedProduct = $lockedProducts[$productId];

                $unitPrice = (float) $lockedProduct->price;

                $variantIds = $item['variant_ids'] ?? [];
                if (! empty($variantIds)) {
                    $extraTotal = (float) ProductVariant::where('product_id', $productId)
                        ->whereIn('id', $variantIds)
                        ->sum('extra_price');
                    $unitPrice += $extraTotal;
                }

                $lineSubtotal = $unitPrice * $qty;
                $serverSubtotal += $lineSubtotal;

                $validatedItems[] = [
                    'product_id' => $productId,
                    'product_name' => $lockedProduct->name,
                    'unit_price' => $unitPrice,
                    'quantity' => $qty,
                    'subtotal' => $lineSubtotal,
                    'notes' => $item['notes'] ?? '',
                ];
            }

            // Calculate tax dynamically from verified settings
            $taxEnabled = (bool) ((int) Setting::get('tax_enabled', '1'));
            $taxPercentage = (float) Setting::get('tax_percentage', 11);
            $taxRate = $taxEnabled ? ($taxPercentage / 100) : 0;
            $serverTaxAmount = round($serverSubtotal * $taxRate);
            $serverTotalAmount = $serverSubtotal + $serverTaxAmount;

            // Collision-resistant unique invoice number
            $seq = Order::whereDate('created_at', now())->count() + 1;
            $randomSuffix = strtoupper(Str::random(3));
            $invoiceNumber = 'TRX-'.date('Ymd').'-'.$randomSuffix.'-'.str_pad($seq, 4, '0', STR_PAD_LEFT);

            $isGateway = $this->paymentChoice === 'gateway';

            $hasActiveSession = $this->primaryActiveOrder !== null;
            $finalNotes = trim($this->customerNotes);
            if ($hasActiveSession) {
                $tag = '[Pesanan Tambahan / Add-on]';
                $finalNotes = $finalNotes ? $tag.' '.$finalNotes : $tag;
            }

            $order = Order::create([
                'invoice_number' => $invoiceNumber,
                'user_id' => null,
                'table_id' => $this->table->id,
                'customer_name' => trim($this->customerName),
                'customer_phone' => trim($this->customerPhone) ?: null,
                'order_source' => 'qr_table',
                'order_type' => 'dine_in',
                'subtotal' => $serverSubtotal,
                'tax_amount' => $serverTaxAmount,
                'discount_amount' => 0,
                'total_amount' => $serverTotalAmount,
                'payment_method' => $isGateway ? 'qris' : 'unpaid',
                'payment_status' => $isGateway ? 'paid' : 'pending',
                'order_status' => 'pending_confirmation',
                'paid_amount' => $isGateway ? $serverTotalAmount : 0,
                'change_amount' => 0,
                'notes' => $finalNotes ?: null,
            ]);

            foreach ($validatedItems as $valItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $valItem['product_id'],
                    'product_name' => $valItem['product_name'],
                    'unit_price' => $valItem['unit_price'],
                    'quantity' => $valItem['quantity'],
                    'subtotal' => $valItem['subtotal'],
                    'notes' => $valItem['notes'],
                ]);
            }

            // Deduct stock safely from locked product rows
            foreach ($productQuantities as $productId => $totalQty) {
                $lockedProducts[$productId]->decrement('stock', $totalQty);
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
        } finally {
            $lock->release();
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
