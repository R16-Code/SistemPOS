<div class="flex-1 flex flex-col pb-32 relative bg-slate-50 min-h-screen">
    <!-- Top Header -->
    <header class="sticky top-0 z-30 bg-white/95 backdrop-blur-sm border-b border-slate-200 px-4 py-3 shadow-xs">
        <div class="flex items-center justify-between gap-3 mb-2.5">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-amber-700 flex items-center justify-center text-white shadow-xs shrink-0">
                    <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                        <path d="M2,21V19H20V21H2M20,8V5H18V8H20M20,3A2,2 0 0,1 22,5V8A2,2 0 0,1 20,10H18V13A4,4 0 0,1 14,17H8A4,4 0 0,1 4,13V3H20M16,5H6V13A2,2 0 0,0 8,15H14A2,2 0 0,0 16,13V5Z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-base font-bold text-slate-900 leading-tight">BrewPOS Cafe</h1>
                    <p class="text-xs text-slate-500 font-medium">Self-Order Menu</p>
                </div>
            </div>

            <!-- Table & Active Order Badge -->
            <div class="flex items-center gap-2 shrink-0">
                @if($this->activeOrders->isNotEmpty())
                    <a href="{{ route('customer.order.status', ['token' => $token, 'invoice' => $this->activeOrders->first()->invoice_number]) }}"
                       class="inline-flex items-center gap-1.5 min-h-11 px-3 py-1.5 rounded-full bg-amber-900 text-amber-100 hover:bg-amber-800 border border-amber-700/80 text-xs font-bold transition-all shadow-xs active:scale-95">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                        </span>
                        <span>Pesanan Aktif</span>
                    </a>
                @endif

                <div class="inline-flex items-center gap-1.5 min-h-11 px-3.5 py-1.5 rounded-full bg-amber-50 border border-amber-200/80 text-amber-900 text-xs font-bold">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span>{{ $table->table_number }}</span>
                </div>
            </div>
        </div>

        <!-- Search Bar -->
        <div class="relative">
            <input type="search"
                   inputmode="search"
                   autocomplete="off"
                   wire:model.live.debounce.300ms="search"
                   placeholder="Cari kopi, camilan, minuman..."
                   class="w-full pl-9 pr-10 py-2.5 rounded-xl bg-slate-100 border border-slate-200 text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-600 focus:bg-white transition-all">
            <div class="absolute left-3 top-3 text-slate-400 pointer-events-none">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            @if(!empty($search))
                <button type="button" wire:click="$set('search', '')" class="absolute right-1 top-1 bottom-1 min-w-11 min-h-11 flex items-center justify-center text-slate-400 hover:text-slate-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            @endif
        </div>
    </header>

    <!-- Category Pills (Horizontal Scroll) -->
    <div class="sticky top-26.25 z-20 bg-slate-50/95 backdrop-blur-xs px-4 py-2.5 border-b border-slate-200 overflow-x-auto no-scrollbar flex items-center gap-2">
        <button type="button"
                wire:click="selectCategory(null)"
                class="min-h-11 px-4 py-2 rounded-full text-xs font-semibold whitespace-nowrap transition-all shrink-0 {{ is_null($selectedCategory) ? 'bg-amber-800 text-white shadow-xs' : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-100' }}">
            Semua Menu
        </button>
        @foreach($categories as $cat)
            <button type="button"
                    wire:click="selectCategory({{ $cat->id }})"
                    class="min-h-11 px-4 py-2 rounded-full text-xs font-semibold whitespace-nowrap transition-all shrink-0 {{ $selectedCategory === $cat->id ? 'bg-amber-800 text-white shadow-xs' : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-100' }}">
                {{ $cat->name }}
            </button>
        @endforeach
    </div>

    <!-- Active Table Session Notice (for multi-device / add-ons) -->
    @if($this->primaryActiveOrder)
        <div class="mx-4 mt-3 p-3.5 rounded-2xl bg-amber-50/90 border border-amber-200/90 text-amber-950 text-xs shadow-2xs space-y-2">
            <div class="flex items-start gap-2.5">
                <div class="w-7 h-7 rounded-xl bg-amber-200/80 border border-amber-300 text-amber-900 flex items-center justify-center shrink-0 mt-0.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-1.5 flex-wrap">
                        <span class="font-bold text-amber-900 text-xs">Sesi Meja Sedang Aktif:</span>
                        <span class="px-2 py-0.5 rounded-md text-[11px] font-extrabold bg-amber-200/80 text-amber-900">{{ $this->primaryActiveOrder->customer_name }}</span>
                    </div>
                    <p class="text-[11px] text-amber-800/90 mt-1 leading-relaxed">
                        Meja {{ $table->table_number }} sedang terisi. Anda dapat memesan menu tambahan (add-on) dari perangkat ini untuk meja yang sama.
                    </p>
                </div>
            </div>
            <div class="pt-2 border-t border-amber-200/70 flex items-center justify-between text-[11px] text-amber-700">
                <span>Bukan rombongan Anda atau meja kosong?</span>
                <span class="font-bold text-amber-900 bg-white/80 px-2 py-0.5 rounded-md border border-amber-300 shadow-2xs">Panggil Pelayan</span>
            </div>
        </div>
    @endif

    <!-- Active Orders Alert Banner (if any active order on this table) -->
    @if($this->activeOrders->isNotEmpty())
        <div wire:poll.5s class="px-4 pt-3 space-y-2.5">
            @foreach($this->activeOrders as $activeOrder)
                @php
                    $statusConfig = [
                        'pending_confirmation' => [
                            'label' => 'Menunggu Kasir',
                            'badge' => 'bg-amber-400/20 text-amber-200 border-amber-400/30',
                            'dot' => 'bg-amber-400 animate-pulse',
                        ],
                        'pending' => [
                            'label' => 'Antrian Dapur',
                            'badge' => 'bg-blue-400/20 text-blue-200 border-blue-400/30',
                            'dot' => 'bg-blue-400',
                        ],
                        'cooking' => [
                            'label' => 'Sedang Diracik',
                            'badge' => 'bg-blue-400/25 text-blue-100 border-blue-400/40',
                            'dot' => 'bg-blue-400 animate-pulse',
                        ],
                        'ready' => [
                            'label' => 'Siap Saji',
                            'badge' => 'bg-emerald-400/25 text-emerald-100 border-emerald-400/40',
                            'dot' => 'bg-emerald-400 animate-ping',
                        ],
                        'served' => [
                            'label' => 'Telah Disajikan',
                            'badge' => 'bg-teal-400/25 text-teal-100 border-teal-400/40',
                            'dot' => 'bg-teal-400',
                        ],
                    ];
                    $cfg = $statusConfig[$activeOrder->order_status] ?? [
                        'label' => 'Diproses',
                        'badge' => 'bg-white/10 text-white border-white/20',
                        'dot' => 'bg-white',
                    ];
                @endphp
                <div class="bg-linear-to-br from-slate-900 via-amber-950 to-slate-900 text-white rounded-2xl p-3.5 shadow-md border border-amber-800/40 relative overflow-hidden">
                    <div class="flex items-center justify-between gap-3 relative z-10">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-10 h-10 rounded-xl bg-amber-800/70 border border-amber-600/40 flex items-center justify-center shrink-0 shadow-inner text-amber-300">
                                <svg class="w-5 h-5 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <span class="text-xs font-black tracking-wide text-white">{{ $activeOrder->invoice_number }}</span>
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold border whitespace-nowrap {{ $cfg['badge'] }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $cfg['dot'] }} shrink-0"></span>
                                        <span>{{ $cfg['label'] }}</span>
                                    </span>
                                </div>
                                <p class="text-[11px] text-amber-200/80 truncate mt-0.5">
                                    {{ $activeOrder->customer_name }} • {{ $activeOrder->items->count() }} menu • Rp {{ number_format($activeOrder->total_amount, 0, ',', '.') }}
                                </p>
                            </div>
                        </div>

                        <a href="{{ route('customer.order.status', ['token' => $token, 'invoice' => $activeOrder->invoice_number]) }}"
                           class="min-h-11 px-4 rounded-xl bg-amber-600 hover:bg-amber-500 active:scale-95 text-white text-xs font-bold shrink-0 flex items-center gap-1.5 shadow-sm transition-all whitespace-nowrap border border-amber-500/50">
                            <span>Lihat Status</span>
                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Inactive Table Banner (if deactivated by admin) -->
    @if(!$table->is_active)
        <div class="m-4 p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 text-center">
            <svg class="w-8 h-8 mx-auto text-amber-700 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <h3 class="font-bold text-sm">Pemesanan Mandiri Dinonaktifkan</h3>
            <p class="text-xs text-amber-800 mt-1">Meja ini sedang tidak membuka pemesanan QR. Silakan hubungi kasir atau barista kami untuk memesan.</p>
        </div>
    @else
        <!-- Products Grid / List -->
        <main class="p-4 space-y-3">
            @forelse($products as $product)
                <div class="bg-white rounded-2xl p-3.5 border border-slate-200/90 shadow-xs flex items-center justify-between gap-3 transition-all hover:border-amber-300">
                    <div class="flex items-center gap-3 min-w-0">
                        <!-- Product Thumbnail -->
                        <div class="w-16 h-16 rounded-xl bg-slate-100 border border-slate-200 overflow-hidden shrink-0 flex items-center justify-center relative aspect-square">
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                            @elseif($product->image_path)
                                <img src="{{ asset('storage/' . $product->image_path) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                            @else
                                <svg class="w-7 h-7 text-amber-800/40" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M2,21V19H20V21H2M20,8V5H18V8H20M20,3A2,2 0 0,1 22,5V8A2,2 0 0,1 20,10H18V13A4,4 0 0,1 14,17H8A4,4 0 0,1 4,13V3H20M16,5H6V13A2,2 0 0,0 8,15H14A2,2 0 0,0 16,13V5Z"/>
                                </svg>
                            @endif

                            @if($product->stock <= 0)
                                <div class="absolute inset-0 bg-slate-900/60 flex items-center justify-center">
                                    <span class="text-[9px] font-bold text-white uppercase tracking-wider">Habis</span>
                                </div>
                            @endif
                        </div>

                        <!-- Product Info -->
                        <div class="min-w-0 flex-1">
                            <h2 class="text-sm font-bold text-slate-900 truncate leading-snug">{{ $product->name }}</h2>
                            <p class="text-xs text-amber-900 font-extrabold mt-0.5">
                                Rp {{ number_format($product->price, 0, ',', '.') }}
                            </p>
                            @if($product->variants->isNotEmpty())
                                <p class="text-[11px] text-slate-500 flex items-center gap-1 mt-0.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span>
                                    <span>Pilihan Varian Tersedia</span>
                                </p>
                            @else
                                <p class="text-[11px] text-slate-400 mt-0.5">Stok: {{ $product->stock }}</p>
                            @endif
                        </div>
                    </div>

                    <!-- Action Button -->
                    <div class="shrink-0">
                        @if($product->stock > 0)
                            <button type="button"
                                    wire:click="selectProductForOrder({{ $product->id }})"
                                    class="min-h-11 min-w-11 px-3.5 rounded-xl bg-amber-800 hover:bg-amber-900 active:scale-95 text-white font-bold text-xs flex items-center justify-center gap-1.5 shadow-xs transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                <span>Pesan</span>
                            </button>
                        @else
                            <button type="button" disabled class="min-h-11 px-3 rounded-xl bg-slate-100 text-slate-400 font-medium text-xs cursor-not-allowed border border-slate-200">
                                Habis
                            </button>
                        @endif
                    </div>
                </div>
            @empty
                <!-- Empty State -->
                <div class="py-12 px-4 text-center">
                    <div class="w-16 h-16 mx-auto mb-3 rounded-2xl bg-amber-50 border border-amber-200 flex items-center justify-center text-amber-800">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <h3 class="font-bold text-slate-800 text-sm">Menu Tidak Ditemukan</h3>
                    <p class="text-xs text-slate-500 mt-1">Coba kata kunci pencarian lain atau pilih kategori yang berbeda.</p>
                </div>
            @endforelse
        </main>
    @endif

    <!-- Floating Bottom Cart Bar (Only shows when cart has items) -->
    @if(!empty($cart) && $table->is_active)
        <div class="fixed bottom-0 left-0 right-0 z-30 flex justify-center px-4 pb-4 pointer-events-none">
            <div class="w-full max-w-md pointer-events-auto">
                <button type="button"
                        wire:click="openCart"
                        class="w-full min-h-13 bg-slate-900 text-white rounded-2xl px-4 py-3 flex items-center justify-between shadow-xl shadow-slate-900/25 active:scale-[0.99] transition-all border border-slate-800">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-amber-600 text-white flex items-center justify-center text-xs font-black shrink-0">
                            {{ $this->totalItemsCount }}
                        </div>
                        <div class="text-left">
                            <p class="text-xs font-medium text-slate-300">Total Pesanan</p>
                            <p class="text-sm font-bold text-amber-300">
                                Rp {{ number_format($this->totalAmount, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-1.5 text-xs font-bold text-white bg-amber-700 hover:bg-amber-800 px-3.5 py-2 rounded-xl transition-colors">
                        <span>Lihat Keranjang</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                </button>
            </div>
        </div>
    @endif

    <!-- Modal: Variant Customization (Bottom Sheet) -->
    @if($showVariantModal && $selectedProduct)
        <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-slate-900/60 backdrop-blur-xs p-0 sm:p-4">
            <div class="w-full max-w-md bg-white rounded-t-3xl sm:rounded-3xl shadow-2xl border border-slate-200 flex flex-col max-h-[90vh] overflow-hidden animate-in slide-in-from-bottom duration-200">
                <!-- Modal Header -->
                <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-base text-slate-900">{{ $selectedProduct->name }}</h3>
                        <p class="text-xs text-amber-900 font-extrabold mt-0.5">
                            Rp {{ number_format($this->variantModalPrice, 0, ',', '.') }}
                        </p>
                    </div>
                    <button type="button" wire:click="closeVariantModal" class="min-h-11 min-w-11 flex items-center justify-center text-slate-400 hover:text-slate-600 rounded-full">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Modal Body (Scrollable) -->
                <div class="p-5 overflow-y-auto space-y-4">
                    @php
                        $variantGroups = $selectedProduct->variants->groupBy('variant_group');
                    @endphp

                    @foreach($variantGroups as $groupName => $variants)
                        <div>
                            <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-2">
                                {{ $groupName }}
                            </label>
                            <div class="grid grid-cols-2 gap-2">
                                @foreach($variants as $variant)
                                    <button type="button"
                                            wire:click="$set('selectedVariants.{{ $groupName }}', {{ $variant->id }})"
                                            class="min-h-11 px-3 py-2 rounded-xl text-xs font-medium border text-left flex flex-col justify-center transition-all {{ ($selectedVariants[$groupName] ?? null) == $variant->id ? 'bg-amber-50 border-amber-600 text-amber-950 font-bold ring-1 ring-amber-600' : 'bg-white border-slate-200 text-slate-700 hover:bg-slate-50' }}">
                                        <span>{{ $variant->name }}</span>
                                        @if($variant->extra_price > 0)
                                            <span class="text-[10px] text-amber-800 font-semibold">+Rp {{ number_format($variant->extra_price, 0, ',', '.') }}</span>
                                        @endif
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endforeach

                    <!-- Notes Input -->
                    <div>
                        <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-1.5">
                            Catatan Khusus (Opsional)
                        </label>
                        <input type="text"
                               wire:model="itemNotes"
                               placeholder="Contoh: Less ice, jangan terlalu manis"
                               class="w-full px-3.5 py-2.5 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-600">
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="p-4 border-t border-slate-100 bg-slate-50 flex items-center gap-3">
                    <button type="button"
                            wire:click="closeVariantModal"
                            class="min-h-12 px-4 rounded-xl border border-slate-300 bg-white text-slate-700 font-semibold text-xs hover:bg-slate-100 transition-colors">
                        Batal
                    </button>
                    <button type="button"
                            wire:click="confirmVariantSelection"
                            class="flex-1 min-h-12 rounded-xl bg-amber-800 hover:bg-amber-900 active:scale-98 text-white font-bold text-xs flex items-center justify-center gap-2 shadow-xs transition-all">
                        <span>Tambahkan ke Pesanan</span>
                        <span>(Rp {{ number_format($this->variantModalPrice, 0, ',', '.') }})</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal: Cart & Checkout (Slide Over / Full Modal) -->
    @if($showCartModal)
        <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-slate-900/60 backdrop-blur-xs p-0 sm:p-4">
            <div class="w-full max-w-md bg-white rounded-t-3xl sm:rounded-3xl shadow-2xl border border-slate-200 flex flex-col max-h-[92vh] overflow-hidden animate-in slide-in-from-bottom duration-200">
                <!-- Cart Header -->
                <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/80">
                    <div>
                        <h3 class="font-extrabold text-base text-slate-900">Konfirmasi Pesanan</h3>
                        <p class="text-xs text-slate-500 font-medium">Meja: <span class="font-bold text-amber-800">{{ $table->table_number }}</span></p>
                    </div>
                    <button type="button" wire:click="closeCart" class="min-h-11 min-w-11 flex items-center justify-center text-slate-400 hover:text-slate-600 rounded-full">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Cart Body -->
                <div class="p-5 overflow-y-auto space-y-4">
                    @if($errors->has('cart'))
                        <div class="p-3 rounded-xl bg-red-50 border border-red-200 text-red-700 text-xs font-semibold">
                            {{ $errors->first('cart') }}
                        </div>
                    @endif

                    <!-- Items List -->
                    <div class="space-y-2.5">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                            Daftar Menu ({{ count($cart) }})
                        </label>
                        @foreach($cart as $key => $item)
                            <div class="bg-slate-50 rounded-2xl p-3 border border-slate-200/80 flex items-center justify-between gap-3">
                                <div class="min-w-0 flex-1">
                                    <h4 class="text-xs font-bold text-slate-900 truncate">{{ $item['name'] }}</h4>
                                    @if(!empty($item['notes']))
                                        <p class="text-[11px] text-amber-900 font-medium mt-0.5 truncate">{{ $item['notes'] }}</p>
                                    @endif
                                    <p class="text-xs font-extrabold text-slate-700 mt-1">
                                        Rp {{ number_format($item['price'], 0, ',', '.') }}
                                    </p>
                                </div>

                                <!-- Quantity Controls (44x44px minimal touch target) -->
                                <div class="flex items-center gap-1 shrink-0 bg-white border border-slate-200 rounded-2xl p-0.5 shadow-2xs">
                                    <button type="button"
                                            wire:click="updateQuantity('{{ $key }}', -1)"
                                            class="min-w-11 min-h-11 flex items-center justify-center rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-base font-bold active:scale-95 transition-all">
                                        -
                                    </button>
                                    <span class="w-7 text-center text-xs font-black text-slate-800">
                                        {{ $item['quantity'] }}
                                    </span>
                                    <button type="button"
                                            wire:click="updateQuantity('{{ $key }}', 1)"
                                            class="min-w-11 min-h-11 flex items-center justify-center rounded-xl bg-amber-800 hover:bg-amber-900 text-white text-base font-bold active:scale-95 transition-all">
                                        +
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Customer Information Form -->
                    <div class="space-y-3 pt-2 border-t border-slate-200">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                            Informasi Pemesan
                        </label>

                        @if($this->primaryActiveOrder)
                            <div class="p-2.5 rounded-xl bg-amber-50 border border-amber-200 text-[11px] text-amber-900 flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span>
                                <span>Pesanan ini dicatat sebagai <strong>Pesanan Tambahan (Add-on)</strong> pada sesi <strong>{{ $this->primaryActiveOrder->customer_name }}</strong> ({{ $table->table_number }}).</span>
                            </div>
                        @endif

                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">
                                Nama Lengkap / Panggilan <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   wire:model="customerName"
                                   autocomplete="name"
                                   autocorrect="off"
                                   placeholder="Masukkan nama Anda..."
                                   class="w-full px-3.5 py-2.5 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-600">
                            @error('customerName')
                                <span class="text-[11px] text-red-600 font-medium mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">
                                No. WhatsApp / HP (Opsional)
                            </label>
                            <input type="tel"
                                   inputmode="numeric"
                                   autocomplete="tel"
                                   wire:model="customerPhone"
                                   placeholder="Contoh: 08123456789"
                                   class="w-full px-3.5 py-2.5 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-600">
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">
                                Catatan untuk Pesanan (Opsional)
                            </label>
                            <textarea wire:model="customerNotes"
                                      rows="2"
                                      placeholder="Pesan tambahan untuk staf..."
                                      class="w-full px-3.5 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-600"></textarea>
                        </div>
                    </div>

                    <!-- Payment Option Selection -->
                    <div class="space-y-2 pt-2 border-t border-slate-200">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                            Pilihan Pembayaran
                        </label>

                        <div class="space-y-2">
                            <!-- Option 1: Bayar di Kasir (Default) -->
                            <label class="p-3 rounded-2xl border flex items-start gap-3 cursor-pointer transition-all {{ $paymentChoice === 'cashier' ? 'bg-amber-50 border-amber-600 ring-1 ring-amber-600' : 'bg-slate-50 border-slate-200 hover:bg-slate-100' }}">
                                <input type="radio" wire:model.live="paymentChoice" value="cashier" class="mt-0.5 text-amber-800 focus:ring-amber-600">
                                <div>
                                    <p class="text-xs font-bold text-slate-900">Bayar di Kasir (Tunai / QRIS Kasir)</p>
                                    <p class="text-[11px] text-slate-500 mt-0.5 leading-snug">Pesanan masuk ke kasir terlebih dahulu. Anda dapat membayar langsung ke kasir saat pesanan dikonfirmasi.</p>
                                </div>
                            </label>

                            <!-- Option 2: Payment Gateway Online (Simulasi) -->
                            <label class="p-3 rounded-2xl border flex items-start gap-3 cursor-pointer transition-all {{ $paymentChoice === 'gateway' ? 'bg-amber-50 border-amber-600 ring-1 ring-amber-600' : 'bg-slate-50 border-slate-200 hover:bg-slate-100' }}">
                                <input type="radio" wire:model.live="paymentChoice" value="gateway" class="mt-0.5 text-amber-800 focus:ring-amber-600">
                                <div>
                                    <div class="flex items-center gap-1.5">
                                        <p class="text-xs font-bold text-slate-900">Payment Gateway Online</p>
                                        <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-amber-200 text-amber-900">Opsi</span>
                                    </div>
                                    <p class="text-[11px] text-slate-500 mt-0.5 leading-snug">Bayar online instan via QRIS Dinamis / E-Wallet (Simulasi terbayar otomatis untuk pengujian).</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Financial Summary -->
                    <div class="p-3.5 rounded-2xl bg-slate-100 border border-slate-200 space-y-1.5 text-xs">
                        <div class="flex justify-between text-slate-600">
                            <span>Subtotal Menu</span>
                            <span>Rp {{ number_format($this->subtotal, 0, ',', '.') }}</span>
                        </div>
                        @if($this->taxRate > 0)
                            <div class="flex justify-between text-slate-600">
                                <span>{{ $taxName }} ({{ round($taxRate * 100) }}%)</span>
                                <span>Rp {{ number_format($this->taxAmount, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        <div class="pt-2 border-t border-slate-200/80 flex justify-between font-bold text-slate-900 text-sm">
                            <span>Total Tagihan</span>
                            <span class="text-amber-900 font-extrabold">Rp {{ number_format($this->totalAmount, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Cart Footer Submit -->
                <div class="p-4 border-t border-slate-200 bg-white flex items-center gap-3">
                    <button type="button"
                            wire:click="closeCart"
                            class="min-h-12 px-4 rounded-xl border border-slate-300 text-slate-700 font-semibold text-xs hover:bg-slate-100 transition-colors">
                        Kembali
                    </button>
                    <button type="button"
                            wire:click="submitOrder"
                            wire:loading.attr="disabled"
                            class="flex-1 min-h-12 rounded-xl bg-amber-800 hover:bg-amber-900 active:scale-98 text-white font-bold text-xs flex items-center justify-center gap-2 shadow-xs transition-all disabled:opacity-50">
                        <span wire:loading.remove>Kirim Pesanan Sekarang</span>
                        <span wire:loading class="flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>Memproses...</span>
                        </span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
