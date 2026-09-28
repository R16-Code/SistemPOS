<?php

namespace App\Livewire\Pos;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Setting;
use App\Models\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Kasir POS - BrewPOS')]
class Cashier extends Component
{
    public string $search = '';

    #[On('refresh-realtime')]
    public function handleRealtimeReconnect(): void
    {
        // Automatically syncs latest orders and tables on network recovery
    }

    public ?int $selectedCategory = null;

    public array $cart = [];

    public string $customerName = 'Guest';

    public string $orderType = 'dine_in';

    public ?int $selectedTableId = null;

    public bool $showVariantModal = false;

    public ?Product $selectedProduct = null;

    public array $selectedVariants = [];

    public string $itemNotes = '';

    public bool $showPaymentModal = false;

    public bool $showReceiptModal = false;

    public string $paymentMethod = 'cash';

    public float|int $paidAmount = 0;

    public float|int $discountAmount = 0;

    public float $taxRate = 0.11;

    public string $taxName = 'PPN';

    public ?Order $completedOrder = null;

    public bool $showIncomingOrdersModal = false;

    public ?int $selectedIncomingOrderId = null;

    public ?int $lastAlertedOrderId = null;

    public string $confirmPaymentMethod = 'cash';

    public float $confirmPaidAmount = 0;

    public function mount()
    {
        $taxEnabled = (bool) ((int) Setting::get('tax_enabled', '1'));
        $taxPercentage = (float) Setting::get('tax_percentage', 11);
        $this->taxRate = $taxEnabled ? ($taxPercentage / 100) : 0;
        $this->taxName = (string) Setting::get('tax_name', 'PPN');

        $firstTable = Table::where('status', 'available')->first();
        if ($firstTable) {
            $this->selectedTableId = $firstTable->id;
        }
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

            // Pick the first option of each variant group as default
            $groups = $product->variants->groupBy('variant_group');
            foreach ($groups as $groupName => $variants) {
                $this->selectedVariants[$groupName] = $variants->first()->id;
            }

            $this->showVariantModal = true;

            return;
        }

        $this->pushToCart($product, [], 0, '');
    }

    public function confirmVariantSelection()
    {
        if (! $this->selectedProduct) {
            return;
        }

        $variantIds = array_values($this->selectedVariants);
        $variants = ProductVariant::whereIn('id', $variantIds)->get();

        $extraTotal = $variants->sum('extra_price');
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

    public function closeVariantModal()
    {
        $this->showVariantModal = false;
        $this->selectedProduct = null;
        $this->selectedVariants = [];
        $this->itemNotes = '';
    }

    public function getVariantModalPriceProperty()
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

    protected function pushToCart(Product $product, array $variantIds, float $extraTotal, string $notes)
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

    public function clearCart()
    {
        $this->cart = [];
    }

    public function getSubtotalProperty()
    {
        return array_sum(array_column($this->cart, 'subtotal'));
    }

    public function getTaxAmountProperty()
    {
        $subAfterDiscount = max(0, $this->subtotal - (float) $this->discountAmount);

        return $subAfterDiscount * $this->taxRate;
    }

    public function getTotalAmountProperty()
    {
        $subAfterDiscount = max(0, $this->subtotal - (float) $this->discountAmount);

        return $subAfterDiscount + $this->taxAmount;
    }

    public function getChangeAmountProperty()
    {
        return max(0, (float) $this->paidAmount - $this->totalAmount);
    }

    public function openPaymentModal()
    {
        if (empty($this->cart)) {
            return;
        }
        $this->paidAmount = $this->totalAmount;
        $this->showPaymentModal = true;
    }

    public function closePaymentModal()
    {
        $this->showPaymentModal = false;
    }

    public function setQuickMoney(float|int $amount): void
    {
        $this->paidAmount = $amount;
    }

    public function processPayment()
    {
        if (empty($this->cart)) {
            return;
        }

        $userId = Auth::id() ?? 1;
        $lockKey = "cashier_payment_user_{$userId}";
        $lock = Cache::lock($lockKey, 5);

        if (! $lock->get()) {
            $this->addError('payment', 'Transaksi sedang diproses. Mohon tunggu...');

            return;
        }

        try {
            DB::beginTransaction();

            // 1. Consolidate required quantity per product_id to prevent multi-variant race conditions
            $productQuantities = [];
            foreach ($this->cart as $item) {
                $pid = (int) $item['product_id'];
                $qty = max(1, (int) $item['quantity']);
                $productQuantities[$pid] = ($productQuantities[$pid] ?? 0) + $qty;
            }

            // 2. Pessimistic Locking on required products & stock validation
            $lockedProducts = [];
            foreach ($productQuantities as $productId => $totalRequiredQty) {
                $product = Product::where('id', $productId)->lockForUpdate()->first();
                if (! $product || ! $product->is_available || $product->stock < $totalRequiredQty) {
                    DB::rollBack();
                    $productName = $product ? $product->name : 'Menu';
                    $availableStock = $product ? max(0, $product->stock) : 0;
                    $this->addError('payment', "Stok untuk menu '{$productName}' tidak mencukupi (tersisa {$availableStock}).");

                    return;
                }
                $lockedProducts[$productId] = $product;
            }

            // 3. Prevent Price Tampering: Recalculate all totals from master DB records
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

            // Validated discount (cannot exceed subtotal)
            $discount = min($serverSubtotal, max(0, (float) $this->discountAmount));
            $serverTotalAmount = max(0, $serverSubtotal + $serverTaxAmount - $discount);

            // Validate cash payment against authoritative total
            $paid = (float) $this->paidAmount;
            if ($this->paymentMethod === 'cash' && $paid < $serverTotalAmount) {
                DB::rollBack();
                $this->addError('paidAmount', 'Jumlah pembayaran kurang dari total tagihan (Rp '.number_format($serverTotalAmount, 0, ',', '.').').');

                return;
            }

            $changeAmount = $this->paymentMethod === 'cash' ? max(0, $paid - $serverTotalAmount) : 0;
            if ($this->paymentMethod !== 'cash') {
                $paid = $serverTotalAmount;
            }

            // Collision-resistant unique invoice number
            $seq = Order::whereDate('created_at', now())->count() + 1;
            $randomSuffix = strtoupper(Str::random(3));
            $invoiceNumber = 'TRX-'.date('Ymd').'-'.$randomSuffix.'-'.str_pad($seq, 4, '0', STR_PAD_LEFT);

            $order = Order::create([
                'invoice_number' => $invoiceNumber,
                'user_id' => $userId,
                'table_id' => $this->orderType === 'dine_in' ? $this->selectedTableId : null,
                'customer_name' => trim($this->customerName) ?: 'Guest',
                'order_type' => $this->orderType,
                'subtotal' => $serverSubtotal,
                'tax_amount' => $serverTaxAmount,
                'discount_amount' => $discount,
                'total_amount' => $serverTotalAmount,
                'payment_method' => $this->paymentMethod,
                'payment_status' => 'paid',
                'order_status' => 'pending',
                'paid_amount' => $paid,
                'change_amount' => $changeAmount,
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

            if ($this->orderType === 'dine_in' && $this->selectedTableId) {
                Table::where('id', $this->selectedTableId)->update(['status' => 'occupied']);
            }

            DB::commit();

            $this->completedOrder = $order->load(['items', 'table', 'user']);
            $this->showPaymentModal = false;
            $this->showReceiptModal = true;

            $this->cart = [];
            $this->customerName = 'Guest';
            $this->discountAmount = 0;
            $this->paidAmount = 0;
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->addError('payment', 'Gagal memproses transaksi: '.$e->getMessage());
        } finally {
            $lock->release();
        }
    }

    public function closeReceiptModal()
    {
        $this->showReceiptModal = false;
        $this->completedOrder = null;
    }

    public function openIncomingOrdersModal(): void
    {
        $this->showIncomingOrdersModal = true;

        $first = Order::where('order_source', 'qr_table')
            ->where('order_status', 'pending_confirmation')
            ->latest()
            ->first();

        if ($first) {
            $this->selectIncomingOrder($first->id);
        }
    }

    public function closeIncomingOrdersModal(): void
    {
        $this->showIncomingOrdersModal = false;
        $this->selectedIncomingOrderId = null;
    }

    public function selectIncomingOrder(int $orderId): void
    {
        $this->selectedIncomingOrderId = $orderId;
        $order = Order::find($orderId);
        if ($order) {
            $this->confirmPaidAmount = (float) $order->total_amount;
            $this->confirmPaymentMethod = $order->payment_method === 'qris' ? 'qris' : 'cash';
        }
    }

    public function setConfirmQuickMoney(float $amount): void
    {
        $this->confirmPaidAmount = $amount;
    }

    public function confirmIncomingOrder(int $orderId): void
    {
        $lock = Cache::lock("confirm_order_{$orderId}", 5);
        if (! $lock->get()) {
            return;
        }

        try {
            DB::beginTransaction();

            $order = Order::with('items')->where('id', $orderId)->lockForUpdate()->first();
            if (! $order || $order->order_status !== 'pending_confirmation') {
                DB::rollBack();

                return;
            }

            $isPaid = $order->payment_status === 'paid';
            $paidAmount = $isPaid ? (float) $order->paid_amount : ((float) $this->confirmPaidAmount ?: (float) $order->total_amount);
            $changeAmount = max(0, $paidAmount - (float) $order->total_amount);

            $order->update([
                'user_id' => Auth::id() ?? 1,
                'payment_method' => $isPaid ? $order->payment_method : $this->confirmPaymentMethod,
                'payment_status' => 'paid',
                'order_status' => 'pending',
                'paid_amount' => $paidAmount,
                'change_amount' => $changeAmount,
            ]);

            if ($order->table_id) {
                Table::where('id', $order->table_id)->update(['status' => 'occupied']);
            }

            DB::commit();

            $this->selectedIncomingOrderId = null;
            $this->dispatch('order-confirmed');
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->addError('incoming', 'Gagal mengonfirmasi pesanan: '.$e->getMessage());
        } finally {
            $lock->release();
        }
    }

    public function rejectIncomingOrder(int $orderId): void
    {
        $lock = Cache::lock("confirm_order_{$orderId}", 5);
        if (! $lock->get()) {
            return;
        }

        try {
            DB::beginTransaction();

            $order = Order::with('items')->where('id', $orderId)->lockForUpdate()->first();
            if (! $order || $order->order_status !== 'pending_confirmation') {
                DB::rollBack();

                return;
            }

            foreach ($order->items as $item) {
                if ($item->product_id) {
                    Product::where('id', $item->product_id)->increment('stock', $item->quantity);
                }
            }

            $order->update([
                'order_status' => 'cancelled',
            ]);

            if ($order->table_id) {
                $hasOtherOrders = Order::where('table_id', $order->table_id)
                    ->where('id', '!=', $order->id)
                    ->whereIn('order_status', ['pending_confirmation', 'pending', 'cooking', 'ready', 'served'])
                    ->exists();

                if (! $hasOtherOrders) {
                    Table::where('id', $order->table_id)->update(['status' => 'available']);
                }
            }

            DB::commit();

            $this->selectedIncomingOrderId = null;
            $this->dispatch('order-rejected');
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->addError('incoming', 'Gagal membatalkan pesanan: '.$e->getMessage());
        } finally {
            $lock->release();
        }
    }

    public function render()
    {
        $categories = Category::where('is_active', true)
            ->withCount('products')
            ->get();

        $totalProductsCount = Product::count();

        $productsQuery = Product::with('variants');

        if ($this->selectedCategory) {
            $productsQuery->where('category_id', $this->selectedCategory);
        }

        if (! empty($this->search)) {
            $productsQuery->where(function ($q) {
                $q->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('sku', 'like', '%'.$this->search.'%');
            });
        }

        $products = $productsQuery->get();
        $tables = Table::with(['currentSessionOrderRelation'])->get();

        $selectedCategoryModel = $this->selectedCategory ? Category::find($this->selectedCategory) : null;

        $incomingOrders = Order::with(['table', 'items'])
            ->where('order_source', 'qr_table')
            ->where('order_status', 'pending_confirmation')
            ->latest()
            ->get();

        if ($incomingOrders->isNotEmpty()) {
            $latestIncoming = $incomingOrders->first();
            if ($this->lastAlertedOrderId !== $latestIncoming->id) {
                $this->lastAlertedOrderId = $latestIncoming->id;
                $this->dispatch('play-order-chime');
            }
        }

        return view('livewire.pos.cashier', [
            'categories' => $categories,
            'totalProductsCount' => $totalProductsCount,
            'products' => $products,
            'tables' => $tables,
            'selectedCategoryModel' => $selectedCategoryModel,
            'incomingOrders' => $incomingOrders,
            'incomingOrdersCount' => $incomingOrders->count(),
        ]);
    }
}
