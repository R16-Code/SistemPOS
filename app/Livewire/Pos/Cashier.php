<?php

namespace App\Livewire\Pos;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Table;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Kasir POS - BrewPOS')]
class Cashier extends Component
{
    public $search = '';

    public $selectedCategory = null;

    public $cart = [];

    public $customerName = 'Guest';

    public $orderType = 'dine_in';

    public $selectedTableId = null;

    public $showVariantModal = false;

    public $selectedProduct = null;

    public $selectedVariants = [];

    public $itemNotes = '';

    public $showPaymentModal = false;

    public $showReceiptModal = false;

    public $paymentMethod = 'cash';

    public $paidAmount = 0;

    public $discountAmount = 0;

    public $taxRate = 0.11;

    public $completedOrder = null;

    public bool $showIncomingOrdersModal = false;

    public ?int $selectedIncomingOrderId = null;

    public ?int $lastAlertedOrderId = null;

    public string $confirmPaymentMethod = 'cash';

    public float $confirmPaidAmount = 0;

    public function mount()
    {
        $firstTable = Table::where('status', 'available')->first();
        if ($firstTable) {
            $this->selectedTableId = $firstTable->id;
        }
    }

    public function selectCategory($categoryId)
    {
        $this->selectedCategory = $this->selectedCategory === $categoryId ? null : $categoryId;
    }

    public function selectProductForOrder($productId)
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
                'quantity' => 1,
                'notes' => $notes,
                'subtotal' => $finalPrice,
            ];
        }
    }

    public function updateQuantity($key, $delta)
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

    public function removeFromCart($key)
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

    public function setQuickMoney($amount)
    {
        $this->paidAmount = $amount;
    }

    public function processPayment()
    {
        if (empty($this->cart)) {
            return;
        }

        if ($this->paymentMethod === 'cash' && (float) $this->paidAmount < $this->totalAmount) {
            $this->addError('paidAmount', 'Jumlah pembayaran kurang dari total tagihan.');

            return;
        }

        DB::beginTransaction();
        try {
            $invoiceNumber = 'TRX-'.date('Ymd').'-'.str_pad(Order::whereDate('created_at', now())->count() + 1, 4, '0', STR_PAD_LEFT);
            $userId = auth()->id() ?? 2;

            $order = Order::create([
                'invoice_number' => $invoiceNumber,
                'user_id' => $userId,
                'table_id' => $this->orderType === 'dine_in' ? $this->selectedTableId : null,
                'customer_name' => $this->customerName ?: 'Guest',
                'order_type' => $this->orderType,
                'subtotal' => $this->subtotal,
                'tax_amount' => $this->taxAmount,
                'discount_amount' => (float) $this->discountAmount,
                'total_amount' => $this->totalAmount,
                'payment_method' => $this->paymentMethod,
                'payment_status' => 'paid',
                'order_status' => 'pending',
                'paid_amount' => (float) $this->paidAmount,
                'change_amount' => $this->changeAmount,
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

            if ($this->orderType === 'dine_in' && $this->selectedTableId) {
                Table::where('id', $this->selectedTableId)->update(['status' => 'occupied']);
            }

            DB::commit();

            $this->completedOrder = $order->load(['items', 'table', 'user']);
            $this->showPaymentModal = false;
            $this->showReceiptModal = true;

            $this->cart = [];
            $this->customerName = 'Guest';
        } catch (\Exception $e) {
            DB::rollBack();
            $this->addError('payment', 'Gagal memproses transaksi: '.$e->getMessage());
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
        $order = Order::with('items')->find($orderId);
        if (! $order || $order->order_status !== 'pending_confirmation') {
            return;
        }

        DB::beginTransaction();
        try {
            $isPaid = $order->payment_status === 'paid';
            $paidAmount = $isPaid ? $order->paid_amount : ((float) $this->confirmPaidAmount ?: $order->total_amount);
            $changeAmount = max(0, $paidAmount - (float) $order->total_amount);

            $order->update([
                'user_id' => auth()->id() ?? 1,
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
        }
    }

    public function rejectIncomingOrder(int $orderId): void
    {
        $order = Order::with('items')->find($orderId);
        if (! $order || $order->order_status !== 'pending_confirmation') {
            return;
        }

        DB::beginTransaction();
        try {
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
                    ->whereIn('order_status', ['pending', 'cooking', 'ready'])
                    ->exists();

                if (! $hasOtherOrders) {
                    Table::where('id', $order->table_id)->update(['status' => 'available']);
                }
            }

            DB::commit();

            $this->selectedIncomingOrderId = null;
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->addError('incoming', 'Gagal membatalkan pesanan: '.$e->getMessage());
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
        $tables = Table::all();

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
