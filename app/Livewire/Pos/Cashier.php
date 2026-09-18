<?php

namespace App\Livewire\Pos;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Table;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Kasir POS - BrewPOS')]
class Cashier extends Component
{
    public $search = '';
    public $selectedCategory = null;

    // Cart state
    public $cart = [];

    // Order info state
    public $customerName = 'Guest';
    public $orderType = 'dine_in';
    public $selectedTableId = null;

    // Checkout / Payment modal state
    public $showPaymentModal = false;
    public $showReceiptModal = false;
    public $paymentMethod = 'cash';
    public $paidAmount = 0;
    public $discountAmount = 0;
    public $taxRate = 0.11; // PPN 11%

    // Last completed order for receipt
    public $completedOrder = null;

    public function mount()
    {
        // Select first available table by default if dine_in
        $firstTable = Table::where('status', 'available')->first();
        if ($firstTable) {
            $this->selectedTableId = $firstTable->id;
        }
    }

    public function selectCategory($categoryId)
    {
        $this->selectedCategory = $this->selectedCategory === $categoryId ? null : $categoryId;
    }

    public function addToCart($productId)
    {
        $product = Product::find($productId);
        if (!$product || !$product->is_available) return;

        $key = 'p_' . $product->id;

        if (isset($this->cart[$key])) {
            $this->cart[$key]['quantity'] += 1;
            $this->cart[$key]['subtotal'] = $this->cart[$key]['quantity'] * $this->cart[$key]['price'];
        } else {
            $this->cart[$key] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'price' => (float) $product->price,
                'quantity' => 1,
                'notes' => '',
                'subtotal' => (float) $product->price,
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
        if (empty($this->cart)) return;
        $this->paidAmount = $this->totalAmount; // Default paid amount to total
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
        if (empty($this->cart)) return;

        if ($this->paymentMethod === 'cash' && (float) $this->paidAmount < $this->totalAmount) {
            $this->addError('paidAmount', 'Jumlah pembayaran kurang dari total tagihan!');
            return;
        }

        DB::beginTransaction();
        try {
            // Generate Invoice Number
            $invoiceNumber = 'TRX-' . date('Ymd') . '-' . str_pad(Order::whereDate('created_at', now())->count() + 1, 4, '0', STR_PAD_LEFT);

            // Default cashier user (user ID 2 from seeder or first user)
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
                'order_status' => 'completed',
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

                // Reduce stock if applicable
                Product::where('id', $item['product_id'])->decrement('stock', $item['quantity']);
            }

            // Update table status if occupied
            if ($this->orderType === 'dine_in' && $this->selectedTableId) {
                Table::where('id', $this->selectedTableId)->update(['status' => 'occupied']);
            }

            DB::commit();

            $this->completedOrder = $order->load(['items', 'table', 'user']);
            $this->showPaymentModal = false;
            $this->showReceiptModal = true;

            // Reset cart
            $this->cart = [];
            $this->customerName = 'Guest';
        } catch (\Exception $e) {
            DB::rollBack();
            $this->addError('payment', 'Gagal memproses transaksi: ' . $e->getMessage());
        }
    }

    public function closeReceiptModal()
    {
        $this->showReceiptModal = false;
        $this->completedOrder = null;
    }

    public function render()
    {
        $categories = Category::where('is_active', true)->get();

        $productsQuery = Product::where('is_available', true);

        if ($this->selectedCategory) {
            $productsQuery->where('category_id', $this->selectedCategory);
        }

        if (!empty($this->search)) {
            $productsQuery->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('sku', 'like', '%' . $this->search . '%');
            });
        }

        $products = $productsQuery->get();
        $tables = Table::all();

        return view('livewire.pos.cashier', [
            'categories' => $categories,
            'products' => $products,
            'tables' => $tables,
        ]);
    }
}
