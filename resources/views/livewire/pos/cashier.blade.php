<div class="flex-1 flex h-full min-h-0 overflow-hidden">
    
    <!-- LEFT PANEL: Product Catalog & Categories (65% width) -->
    <div class="flex-1 flex flex-col min-w-0 bg-slate-950 border-r border-slate-800">
        
        <!-- Header & Search Bar -->
        <div class="p-5 border-b border-slate-800 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold text-white tracking-tight">Katalog Menu & Produk</h2>
                    <p class="text-xs text-slate-400">Pilih item untuk ditambahkan ke keranjang pesanan</p>
                </div>
                
                <!-- Quick Search Input -->
                <div class="relative w-72">
                    <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                    <input type="text" 
                           wire:model.live.debounce.250ms="search" 
                           placeholder="Cari nama produk / SKU..." 
                           class="w-full bg-slate-900 border border-slate-800 rounded-xl pl-9 pr-4 py-2 text-sm text-slate-200 placeholder-slate-500 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-all">
                </div>
            </div>

            <!-- Category Pills Filter -->
            <div class="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-none">
                <button wire:click="selectCategory(null)" 
                        class="px-4 py-2 rounded-xl text-xs font-semibold whitespace-nowrap transition-all duration-150 {{ is_null($selectedCategory) ? 'bg-amber-500 text-slate-950 shadow-md shadow-amber-500/20' : 'bg-slate-900 text-slate-300 hover:bg-slate-800' }}">
                    Semua Menu
                </button>
                @foreach($categories as $cat)
                    <button wire:click="selectCategory({{ $cat->id }})" 
                            class="px-4 py-2 rounded-xl text-xs font-semibold whitespace-nowrap transition-all duration-150 {{ $selectedCategory === $cat->id ? 'bg-amber-500 text-slate-950 shadow-md shadow-amber-500/20' : 'bg-slate-900 text-slate-300 hover:bg-slate-800' }}">
                        {{ $cat->name }}
                    </button>
                @endforeach
            </div>
        </div>

        <!-- Product Grid Container -->
        <div class="flex-1 p-5 overflow-y-auto">
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                @forelse($products as $product)
                    <div wire:click="addToCart({{ $product->id }})" 
                         class="group bg-slate-900 border border-slate-800 hover:border-amber-500/50 rounded-2xl p-4 flex flex-col justify-between cursor-pointer transition-all duration-200 hover:-translate-y-1 hover:shadow-xl hover:shadow-amber-500/5">
                        <div>
                            <!-- Product Icon / Thumbnail Placeholder -->
                            <div class="w-full h-28 rounded-xl bg-slate-800/80 mb-3 flex items-center justify-center relative overflow-hidden group-hover:bg-slate-800 transition-colors">
                                <div class="w-12 h-12 rounded-full bg-slate-700/50 flex items-center justify-center text-amber-400">
                                    <i data-lucide="coffee" class="w-6 h-6"></i>
                                </div>
                                <span class="absolute top-2 right-2 px-2 py-0.5 rounded-md bg-slate-950/80 text-[10px] font-mono text-slate-400 border border-slate-800">
                                    {{ $product->sku }}
                                </span>
                            </div>

                            <!-- Title & Description -->
                            <h3 class="font-semibold text-white text-sm group-hover:text-amber-400 transition-colors line-clamp-1">
                                {{ $product->name }}
                            </h3>
                            <p class="text-xs text-slate-400 mt-1 line-clamp-2 leading-relaxed">
                                {{ $product->description }}
                            </p>
                        </div>

                        <!-- Price & Stock -->
                        <div class="mt-4 pt-3 border-t border-slate-800/60 flex items-center justify-between">
                            <div>
                                <span class="text-xs text-slate-400 block">Harga</span>
                                <span class="text-sm font-bold text-amber-400">
                                    Rp {{ number_format($product->price, 0, ',', '.') }}
                                </span>
                            </div>
                            
                            <button class="w-8 h-8 rounded-lg bg-amber-500/10 text-amber-400 group-hover:bg-amber-500 group-hover:text-slate-950 flex items-center justify-center transition-all">
                                <i data-lucide="plus" class="w-4 h-4 stroke-[3]"></i>
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-16 flex flex-col items-center justify-center text-center">
                        <div class="w-16 h-16 rounded-full bg-slate-900 border border-slate-800 flex items-center justify-center text-slate-500 mb-3">
                            <i data-lucide="search-x" class="w-8 h-8"></i>
                        </div>
                        <h4 class="text-base font-semibold text-slate-300">Produk tidak ditemukan</h4>
                        <p class="text-xs text-slate-500 mt-1">Coba kata kunci pencarian atau kategori lain</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- RIGHT PANEL: Order Cart & Billing (35% width) -->
    <div class="w-96 bg-slate-900 border-l border-slate-800 flex flex-col h-full shrink-0">
        
        <!-- Cart Header Info -->
        <div class="p-4 border-b border-slate-800 space-y-3 bg-slate-900/50">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i data-lucide="shopping-bag" class="w-5 h-5 text-amber-400"></i>
                    <h2 class="font-bold text-white text-base">Pesanan Aktif</h2>
                </div>
                @if(count($cart) > 0)
                    <button wire:click="clearCart" class="text-xs text-rose-400 hover:text-rose-300 font-medium transition-colors">
                        Reset Kart
                    </button>
                @endif
            </div>

            <!-- Order Type & Customer Name Input -->
            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="text-[11px] text-slate-400 font-medium block mb-1">Tipe Pesanan</label>
                    <select wire:model.live="orderType" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-2 py-1.5 text-xs text-slate-200 focus:outline-none focus:border-amber-500">
                        <option value="dine_in">Dine In (Makan di Tempat)</option>
                        <option value="takeaway">Takeaway (Bawa Pulang)</option>
                    </select>
                </div>

                <div>
                    <label class="text-[11px] text-slate-400 font-medium block mb-1">Nama Pelanggan</label>
                    <input type="text" wire:model.live="customerName" placeholder="Guest" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-2.5 py-1.5 text-xs text-slate-200 focus:outline-none focus:border-amber-500">
                </div>
            </div>

            <!-- Table Selector if Dine In -->
            @if($orderType === 'dine_in')
                <div>
                    <label class="text-[11px] text-slate-400 font-medium block mb-1">Pilih Meja</label>
                    <select wire:model.live="selectedTableId" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-2.5 py-1.5 text-xs text-slate-200 focus:outline-none focus:border-amber-500">
                        @foreach($tables as $t)
                            <option value="{{ $t->id }}">
                                {{ $t->table_number }} (Kapasitas: {{ $t->capacity }} - {{ strtoupper($t->status) }})
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif
        </div>

        <!-- Cart Items Scrollable List -->
        <div class="flex-1 p-4 overflow-y-auto space-y-3">
            @forelse($cart as $key => $item)
                <div class="bg-slate-950 border border-slate-800 rounded-xl p-3 flex flex-col gap-2 relative group">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <h4 class="text-xs font-semibold text-white leading-snug">{{ $item['name'] }}</h4>
                            <p class="text-[11px] text-amber-400 font-medium mt-0.5">
                                Rp {{ number_format($item['price'], 0, ',', '.') }}
                            </p>
                        </div>
                        <button wire:click="removeFromCart('{{ $key }}')" class="text-slate-500 hover:text-rose-400 transition-colors p-1">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                        </button>
                    </div>

                    <!-- Quantity Control & Subtotal -->
                    <div class="flex items-center justify-between pt-2 border-t border-slate-800/80">
                        <div class="flex items-center border border-slate-800 rounded-lg bg-slate-900 overflow-hidden">
                            <button wire:click="updateQuantity('{{ $key }}', -1)" class="w-7 h-7 flex items-center justify-center text-slate-400 hover:bg-slate-800 hover:text-white transition-colors">
                                -
                            </button>
                            <span class="w-8 text-center text-xs font-bold text-white">
                                {{ $item['quantity'] }}
                            </span>
                            <button wire:click="updateQuantity('{{ $key }}', 1)" class="w-7 h-7 flex items-center justify-center text-slate-400 hover:bg-slate-800 hover:text-white transition-colors">
                                +
                            </button>
                        </div>

                        <span class="text-xs font-bold text-white">
                            Rp {{ number_format($item['subtotal'], 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            @empty
                <div class="h-full flex flex-col items-center justify-center text-center py-12 text-slate-500">
                    <div class="w-12 h-12 rounded-full bg-slate-950 border border-slate-800 flex items-center justify-center mb-2">
                        <i data-lucide="shopping-cart" class="w-6 h-6"></i>
                    </div>
                    <p class="text-xs font-medium">Keranjang masih kosong</p>
                    <p class="text-[11px] text-slate-600 mt-0.5">Klik menu di sebelah kiri untuk memesan</p>
                </div>
            @endforelse
        </div>

        <!-- Billing Summary & Checkout Footer -->
        <div class="p-4 border-t border-slate-800 bg-slate-950 space-y-3">
            <div class="space-y-1.5 text-xs text-slate-400">
                <div class="flex justify-between">
                    <span>Subtotal</span>
                    <span class="text-slate-200 font-medium">Rp {{ number_format($this->subtotal, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span>Diskon (Rp)</span>
                    <input type="number" wire:model.live="discountAmount" min="0" class="w-24 bg-slate-900 border border-slate-800 rounded px-2 py-0.5 text-right text-xs text-white focus:outline-none focus:border-amber-500">
                </div>
                <div class="flex justify-between">
                    <span>PPN (11%)</span>
                    <span class="text-slate-200 font-medium">Rp {{ number_format($this->taxAmount, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between pt-2 border-t border-slate-800 text-sm font-bold text-white">
                    <span>Total Tagihan</span>
                    <span class="text-amber-400">Rp {{ number_format($this->totalAmount, 0, ',', '.') }}</span>
                </div>
            </div>

            <button wire:click="openPaymentModal" 
                    @if(count($cart) === 0) disabled @endif
                    class="w-full py-3 rounded-xl bg-amber-500 hover:bg-amber-400 disabled:opacity-50 disabled:cursor-not-allowed text-slate-950 font-bold text-sm shadow-lg shadow-amber-500/20 flex items-center justify-center gap-2 transition-all">
                <i data-lucide="credit-card" class="w-4 h-4"></i>
                <span>Bayar Sekarang</span>
            </button>
        </div>
    </div>

    <!-- PAYMENT MODAL -->
    @if($showPaymentModal)
        <div class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-slate-900 border border-slate-800 rounded-2xl w-full max-w-md p-6 shadow-2xl space-y-5">
                <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                    <h3 class="text-base font-bold text-white flex items-center gap-2">
                        <i data-lucide="wallet" class="w-5 h-5 text-amber-400"></i>
                        <span>Pembayaran Kasir</span>
                    </h3>
                    <button wire:click="closePaymentModal" class="text-slate-400 hover:text-white">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <!-- Total Amount Display -->
                <div class="bg-slate-950 rounded-xl p-4 border border-slate-800 text-center">
                    <span class="text-xs text-slate-400 block mb-1">Total Tagihan</span>
                    <span class="text-2xl font-extrabold text-amber-400">
                        Rp {{ number_format($this->totalAmount, 0, ',', '.') }}
                    </span>
                </div>

                <!-- Payment Method Selector -->
                <div>
                    <label class="text-xs text-slate-400 font-medium block mb-2">Metode Pembayaran</label>
                    <div class="grid grid-cols-3 gap-2">
                        @foreach(['cash' => 'Tunai / Cash', 'qris' => 'QRIS Static', 'transfer' => 'Transfer Bank'] as $key => $label)
                            <button type="button" 
                                    wire:click="$set('paymentMethod', '{{ $key }}')"
                                    class="py-2.5 px-3 rounded-xl border text-xs font-semibold transition-all {{ $paymentMethod === $key ? 'border-amber-500 bg-amber-500/10 text-amber-400' : 'border-slate-800 bg-slate-950 text-slate-400 hover:bg-slate-800' }}">
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <!-- Cash Quick Presets if Payment Method is Cash -->
                @if($paymentMethod === 'cash')
                    <div class="space-y-3">
                        <div>
                            <label class="text-xs text-slate-400 font-medium block mb-1.5">Nominal Uang Diterima (Rp)</label>
                            <input type="number" 
                                   wire:model.live="paidAmount" 
                                   class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-base font-bold text-white focus:outline-none focus:border-amber-500">
                            @error('paidAmount')
                                <p class="text-xs text-rose-400 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Quick Money Buttons -->
                        <div class="grid grid-cols-4 gap-2">
                            <button type="button" wire:click="setQuickMoney({{ $this->totalAmount }})" class="py-1.5 rounded-lg bg-slate-800 text-[11px] font-medium text-slate-300 hover:bg-slate-700">Uang Pas</button>
                            <button type="button" wire:click="setQuickMoney(50000)" class="py-1.5 rounded-lg bg-slate-800 text-[11px] font-medium text-slate-300 hover:bg-slate-700">50.000</button>
                            <button type="button" wire:click="setQuickMoney(100000)" class="py-1.5 rounded-lg bg-slate-800 text-[11px] font-medium text-slate-300 hover:bg-slate-700">100.000</button>
                            <button type="button" wire:click="setQuickMoney(200000)" class="py-1.5 rounded-lg bg-slate-800 text-[11px] font-medium text-slate-300 hover:bg-slate-700">200.000</button>
                        </div>

                        <!-- Kembalian Math -->
                        <div class="flex items-center justify-between p-3 rounded-xl bg-slate-950 border border-slate-800">
                            <span class="text-xs text-slate-400">Kembalian</span>
                            <span class="text-lg font-bold text-emerald-400">
                                Rp {{ number_format($this->changeAmount, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                @endif

                <!-- Process Button -->
                <button wire:click="processPayment" 
                        class="w-full py-3 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-sm shadow-lg shadow-amber-500/20 transition-all">
                    Selesaikan Transaksi & Cetak Struk
                </button>
            </div>
        </div>
    @endif

    <!-- RECEIPT MODAL -->
    @if($showReceiptModal && $completedOrder)
        <div class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white text-slate-900 rounded-2xl w-full max-w-sm p-6 shadow-2xl space-y-4 font-mono text-xs">
                
                <!-- Printable Receipt Content -->
                <div id="thermal-receipt" class="space-y-3">
                    <div class="text-center space-y-1">
                        <h2 class="font-bold text-base text-black uppercase tracking-wider">BREWPOS COFFEE</h2>
                        <p class="text-[11px] text-slate-600">Jl. Mawar No. 123, Jakarta</p>
                        <p class="text-[11px] text-slate-600">Telp: 0812-3456-7890</p>
                        <div class="border-b border-dashed border-slate-400 my-2"></div>
                    </div>

                    <div class="space-y-0.5 text-[11px]">
                        <p>No: <span class="font-bold">{{ $completedOrder->invoice_number }}</span></p>
                        <p>Tanggal: {{ $completedOrder->created_at->format('d/m/Y H:i') }}</p>
                        <p>Kasir: {{ $completedOrder->user->name ?? 'Kasir' }}</p>
                        <p>Pelanggan: {{ $completedOrder->customer_name }}</p>
                        <p>Tipe: {{ strtoupper($completedOrder->order_type) }} @if($completedOrder->table) ({{ $completedOrder->table->table_number }}) @endif</p>
                    </div>

                    <div class="border-b border-dashed border-slate-400"></div>

                    <!-- Items -->
                    <div class="space-y-1">
                        @foreach($completedOrder->items as $item)
                            <div class="flex justify-between">
                                <span>{{ $item->product_name }} x{{ $item->quantity }}</span>
                                <span>{{ number_format($item->subtotal, 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                    </div>

                    <div class="border-b border-dashed border-slate-400"></div>

                    <!-- Summary -->
                    <div class="space-y-0.5 text-right">
                        <div class="flex justify-between">
                            <span>Subtotal:</span>
                            <span>{{ number_format($completedOrder->subtotal, 0, ',', '.') }}</span>
                        </div>
                        @if($completedOrder->discount_amount > 0)
                            <div class="flex justify-between">
                                <span>Diskon:</span>
                                <span>-{{ number_format($completedOrder->discount_amount, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between">
                            <span>PPN (11%):</span>
                            <span>{{ number_format($completedOrder->tax_amount, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between font-bold text-sm text-black border-t border-slate-300 pt-1">
                            <span>TOTAL:</span>
                            <span>Rp {{ number_format($completedOrder->total_amount, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Bayar ({{ strtoupper($completedOrder->payment_method) }}):</span>
                            <span>{{ number_format($completedOrder->paid_amount, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Kembali:</span>
                            <span>{{ number_format($completedOrder->change_amount, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <div class="border-b border-dashed border-slate-400 my-2"></div>

                    <div class="text-center text-[10px] text-slate-500">
                        <p>Terima kasih atas kunjungan Anda!</p>
                        <p>Powered by BrewPOS</p>
                    </div>
                </div>

                <!-- Print & Close Action Buttons -->
                <div class="flex gap-2 pt-2 border-t border-slate-200">
                    <button onclick="window.print()" class="flex-1 py-2 bg-slate-900 text-white rounded-lg font-sans font-semibold text-xs flex items-center justify-center gap-1">
                        <i data-lucide="printer" class="w-4 h-4"></i>
                        <span>Cetak Struk</span>
                    </button>
                    <button wire:click="closeReceiptModal" class="px-4 py-2 bg-slate-200 text-slate-800 rounded-lg font-sans font-semibold text-xs">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
