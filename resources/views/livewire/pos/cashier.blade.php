<div class="flex-1 flex flex-col h-full min-h-0 overflow-hidden bg-slate-100">
    @if(!$showIncomingOrdersModal && !$showPaymentModal)
        <div wire:poll.5s class="hidden"></div>
    @endif

    <!-- Top Alert Banner for Incoming QR Orders -->
    @if($incomingOrdersCount > 0)
        <div class="bg-amber-700 text-white px-5 py-2.5 flex items-center justify-between shadow-xs shrink-0 border-b border-amber-800">
            <div class="flex items-center gap-3">
                <span class="w-7 h-7 rounded-lg bg-amber-800 flex items-center justify-center text-amber-200">
                    <svg class="w-4 h-4 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                </span>
                <div>
                    <p class="text-xs font-bold tracking-tight text-white">{{ $incomingOrdersCount }} Pesanan Masuk dari QR Meja</p>
                    <p class="text-[11px] text-amber-100 font-normal">Pelanggan menunggu konfirmasi kasir untuk diteruskan ke antrian dapur.</p>
                </div>
            </div>
            <button type="button" wire:click="openIncomingOrdersModal" class="px-3.5 py-1.5 rounded-xl bg-slate-900 hover:bg-slate-800 active:scale-95 text-white text-xs font-bold shadow-xs transition-all flex items-center gap-1.5">
                <span>Tinjau Pesanan</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </button>
        </div>
    @endif

    <div class="flex-1 flex min-h-0 overflow-hidden">
        <!-- LEFT PANEL: Product Catalog & Categories (65% width) -->
        <div class="flex-1 flex flex-col min-w-0 bg-slate-50 border-r border-slate-200">
            
            <!-- Header & Search Bar -->
            <div class="p-5 bg-white border-b border-slate-200 space-y-4">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Katalog Menu & Produk</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Pilih menu untuk ditambahkan ke keranjang pesanan</p>
                    </div>
                    
                    <div class="flex items-center gap-2.5">
                        <!-- QR Incoming Orders Button -->
                        <button type="button"
                                wire:click="openIncomingOrdersModal"
                                class="min-h-[40px] px-3.5 rounded-xl text-xs font-bold border transition-all flex items-center gap-2 {{ $incomingOrdersCount > 0 ? 'bg-amber-50 border-amber-600 text-amber-900 ring-1 ring-amber-600 shadow-xs' : 'bg-slate-50 border-slate-200 text-slate-700 hover:bg-slate-100' }}">
                            <svg class="w-4 h-4 text-amber-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                            </svg>
                            <span>Pesanan QR</span>
                            @if($incomingOrdersCount > 0)
                                <span class="px-1.5 py-0.5 rounded-full text-[10px] font-black bg-amber-800 text-white">
                                    {{ $incomingOrdersCount }}
                                </span>
                            @endif
                        </button>

                        <!-- Search Bar -->
                        <div class="relative w-72">
                            <svg class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2 fill-none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <input type="text" 
                                   wire:model.live.debounce.250ms="search" 
                                   placeholder="Cari menu kopi / SKU..." 
                                   class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-9 pr-4 py-2 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-amber-700 focus:bg-white focus:ring-2 focus:ring-amber-700/20 transition-all">
                        </div>
                    </div>
                </div>

            <!-- Category Filter Pills -->
            <div class="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-none">
                <button wire:click="selectCategory(null)" 
                        class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition-all duration-150 flex items-center gap-2 {{ is_null($selectedCategory) ? 'bg-amber-700 text-white shadow-sm' : 'bg-slate-100 text-slate-700 hover:bg-slate-200 border border-slate-200/60' }}">
                    <span>Semua Menu</span>
                    <span class="px-1.5 py-0.5 rounded-md text-[10px] font-bold {{ is_null($selectedCategory) ? 'bg-amber-800 text-amber-100' : 'bg-slate-200 text-slate-600' }}">
                        {{ $totalProductsCount }}
                    </span>
                </button>
                @foreach($categories as $cat)
                    <button wire:click="selectCategory({{ $cat->id }})" 
                            class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition-all duration-150 flex items-center gap-2 {{ $selectedCategory === $cat->id ? 'bg-amber-700 text-white shadow-sm' : 'bg-slate-100 text-slate-700 hover:bg-slate-200 border border-slate-200/60' }}">
                        <span>{{ $cat->name }}</span>
                        <span class="px-1.5 py-0.5 rounded-md text-[10px] font-bold {{ $selectedCategory === $cat->id ? 'bg-amber-800 text-amber-100' : 'bg-slate-200 text-slate-600' }}">
                            {{ $cat->products_count }}
                        </span>
                    </button>
                @endforeach
            </div>
        </div>

        <!-- Product Grid -->
        <div class="flex-1 p-5 overflow-y-auto space-y-3">
            <div class="flex items-center justify-between text-xs text-slate-500 font-medium px-1">
                <div class="flex items-center gap-2">
                    <span class="font-bold text-slate-700">Kategori: {{ $selectedCategoryModel ? $selectedCategoryModel->name : 'Semua Menu' }}</span>
                    <span>&bull;</span>
                    <span class="text-amber-800 font-semibold bg-amber-50 px-2 py-0.5 rounded-md border border-amber-200/60">
                        {{ $products->count() }} menu tersedia
                    </span>
                    @if($search)
                        <span class="text-slate-400">hasil pencarian "{{ $search }}"</span>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                @forelse($products as $product)
                    @php
                        $isOut = !$product->is_available || $product->stock <= 0;
                        $hasVariants = $product->variants->isNotEmpty();
                    @endphp
                    <div @if(!$isOut) wire:click="selectProductForOrder({{ $product->id }})" @endif
                         class="group border rounded-2xl p-4 flex flex-col justify-between transition-all duration-200 relative overflow-hidden {{ $isOut ? 'bg-slate-100/90 border-slate-200 opacity-65 cursor-not-allowed select-none' : 'bg-white border-slate-200/80 hover:border-amber-500 cursor-pointer hover:-translate-y-0.5 hover:shadow-md' }}">
                        
                        @if($isOut)
                            <span class="absolute top-2 left-2 px-2 py-0.5 rounded-md bg-rose-600 text-white font-extrabold text-[10px] tracking-wider uppercase shadow-2xs z-10">
                                HABIS / KOSONG
                            </span>
                        @elseif($hasVariants)
                            <span class="absolute top-2 left-2 px-2 py-0.5 rounded-md bg-amber-100 text-amber-900 border border-amber-200 font-bold text-[10px] tracking-wider uppercase shadow-2xs z-10">
                                ADA VARIAN
                            </span>
                        @endif

                        <div>
                            <!-- Product Icon / Image Card -->
                            <div class="w-full h-28 rounded-xl {{ $isOut ? 'bg-slate-200/70' : 'bg-amber-50 border border-amber-100 group-hover:bg-amber-100/60' }} mb-3 flex items-center justify-center relative overflow-hidden transition-colors">
                                <div class="w-12 h-12 rounded-full {{ $isOut ? 'bg-slate-300 text-slate-500' : 'bg-white shadow-sm text-amber-700' }} flex items-center justify-center">
                                    <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24">
                                        <path d="M2,21V19H20V21H2M20,8V5H18V8H20M20,3A2,2 0 0,1 22,5V8A2,2 0 0,1 20,10H18V13A4,4 0 0,1 14,17H8A4,4 0 0,1 4,13V3H20M16,5H6V13A2,2 0 0,0 8,15H14A2,2 0 0,0 16,13V5Z"/>
                                    </svg>
                                </div>
                                <span class="absolute top-2 right-2 px-2 py-0.5 rounded-md bg-white/90 text-[10px] font-mono font-semibold text-slate-600 border border-slate-200 shadow-2xs">
                                    {{ $product->sku }}
                                </span>
                            </div>

                            <!-- Name & Description -->
                            <h3 class="font-bold text-slate-900 text-sm {{ $isOut ? 'line-through text-slate-500' : 'group-hover:text-amber-700' }} transition-colors line-clamp-1">
                                {{ $product->name }}
                            </h3>
                            <p class="text-xs text-slate-500 mt-1 line-clamp-2 leading-relaxed">
                                {{ $product->description }}
                            </p>
                        </div>

                        <!-- Price & Add Button -->
                        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between">
                            <div>
                                <span class="text-[11px] text-slate-400 block font-medium">Harga</span>
                                <span class="text-sm font-extrabold {{ $isOut ? 'text-slate-500' : 'text-amber-800' }}">
                                    Rp {{ number_format($product->price, 0, ',', '.') }}
                                </span>
                            </div>
                            
                            @if($isOut)
                                <span class="px-2.5 py-1 rounded-lg bg-slate-200 text-slate-500 font-extrabold text-[10px] uppercase">
                                    TIDAK TERSEDIA
                                </span>
                            @else
                                <button class="w-8 h-8 rounded-lg bg-amber-50 text-amber-700 group-hover:bg-amber-700 group-hover:text-white flex items-center justify-center transition-all shadow-2xs">
                                    <svg class="w-4 h-4 fill-none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                                    </svg>
                                </button>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-16 flex flex-col items-center justify-center text-center">
                        <div class="w-16 h-16 rounded-full bg-slate-200/60 border border-slate-300 flex items-center justify-center text-slate-400 mb-3">
                            <svg class="w-8 h-8 fill-none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <h4 class="text-base font-bold text-slate-800">Produk tidak ditemukan</h4>
                        <p class="text-xs text-slate-500 mt-1">Coba kata kunci pencarian atau kategori lain</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- RIGHT PANEL: Order Cart & Billing (Bright White Panel) -->
    <div class="w-96 bg-white border-l border-slate-200 flex flex-col h-full shrink-0 shadow-sm">
        
        <!-- Cart Header Info -->
        <div class="p-4 border-b border-slate-200 space-y-3 bg-slate-50/50">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-amber-700 fill-none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    <h2 class="font-extrabold text-slate-900 text-base">Pesanan Aktif</h2>
                </div>
                @if(count($cart) > 0)
                    <button wire:click="clearCart" class="text-xs text-rose-600 hover:text-rose-700 font-bold transition-colors">
                        Reset Kart
                    </button>
                @endif
            </div>

            <!-- Order Type & Customer Input -->
            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="text-[11px] text-slate-500 font-bold block mb-1">Tipe Pesanan</label>
                    <select wire:model.live="orderType" class="w-full bg-white border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs text-slate-800 font-medium focus:outline-none focus:border-amber-700">
                        <option value="dine_in">Dine In (Makan di Tempat)</option>
                        <option value="takeaway">Takeaway (Bawa Pulang)</option>
                    </select>
                </div>

                <div>
                    <label class="text-[11px] text-slate-500 font-bold block mb-1">Nama Pelanggan</label>
                    <input type="text" wire:model.live="customerName" placeholder="Guest" class="w-full bg-white border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs text-slate-800 font-medium focus:outline-none focus:border-amber-700">
                </div>
            </div>

            <!-- Table Selector if Dine In -->
            @if($orderType === 'dine_in')
                <div>
                    <label class="text-[11px] text-slate-500 font-bold block mb-1">Pilih Meja</label>
                    <select wire:model.live="selectedTableId" class="w-full bg-white border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs text-slate-800 font-medium focus:outline-none focus:border-amber-700">
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
                <div class="bg-slate-50 border border-slate-200 rounded-xl p-3 flex flex-col gap-2 relative">
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex-1">
                            <h4 class="text-xs font-bold text-slate-900 leading-snug">{{ $item['name'] }}</h4>
                            @if(!empty($item['notes']))
                                <p class="text-[11px] text-slate-500 font-medium mt-0.5 bg-amber-50/80 px-2 py-0.5 rounded border border-amber-200/50 inline-block">
                                    {{ $item['notes'] }}
                                </p>
                            @endif
                            <p class="text-[11px] text-amber-800 font-extrabold mt-1">
                                Rp {{ number_format($item['price'], 0, ',', '.') }}
                            </p>
                        </div>
                        <button wire:click="removeFromCart('{{ $key }}')" class="text-slate-400 hover:text-rose-600 transition-colors p-1">
                            <svg class="w-4 h-4 fill-none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>

                    <!-- Quantity Control & Subtotal -->
                    <div class="flex items-center justify-between pt-2 border-t border-slate-200/80">
                        <div class="flex items-center border border-slate-200 rounded-lg bg-white overflow-hidden shadow-2xs">
                            <button wire:click="updateQuantity('{{ $key }}', -1)" class="w-7 h-7 flex items-center justify-center text-slate-600 font-bold hover:bg-slate-100 transition-colors">
                                -
                            </button>
                            <span class="w-8 text-center text-xs font-bold text-slate-900">
                                {{ $item['quantity'] }}
                            </span>
                            <button wire:click="updateQuantity('{{ $key }}', 1)" class="w-7 h-7 flex items-center justify-center text-slate-600 font-bold hover:bg-slate-100 transition-colors">
                                +
                            </button>
                        </div>

                        <span class="text-xs font-bold text-slate-900">
                            Rp {{ number_format($item['subtotal'], 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            @empty
                <div class="h-full flex flex-col items-center justify-center text-center py-12 text-slate-400">
                    <div class="w-12 h-12 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center mb-2">
                        <svg class="w-6 h-6 fill-none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 0a2 2 0 100 4 2 2 0 000-4z"/></svg>
                    </div>
                    <p class="text-xs font-bold text-slate-600">Keranjang masih kosong</p>
                    <p class="text-[11px] text-slate-400 mt-0.5">Klik menu di sebelah kiri untuk memesan</p>
                </div>
            @endforelse
        </div>

        <!-- Billing Summary & Checkout Footer -->
        <div class="p-4 border-t border-slate-200 bg-slate-50 space-y-3">
            <div class="space-y-1.5 text-xs text-slate-600">
                <div class="flex justify-between">
                    <span>Subtotal</span>
                    <span class="text-slate-900 font-bold">Rp {{ number_format($this->subtotal, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span>Diskon (Rp)</span>
                    <input type="number" wire:model.live="discountAmount" min="0" class="w-24 bg-white border border-slate-200 rounded px-2 py-0.5 text-right text-xs font-bold text-slate-900 focus:outline-none focus:border-amber-700">
                </div>
                <div class="flex justify-between">
                    <span>PPN (11%)</span>
                    <span class="text-slate-900 font-bold">Rp {{ number_format($this->taxAmount, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between pt-2 border-t border-slate-200 text-sm font-extrabold text-slate-900">
                    <span>Total Tagihan</span>
                    <span class="text-amber-800">Rp {{ number_format($this->totalAmount, 0, ',', '.') }}</span>
                </div>
            </div>

            <button wire:click="openPaymentModal" 
                    @if(count($cart) === 0) disabled @endif
                    class="w-full py-3 rounded-xl bg-amber-700 hover:bg-amber-800 disabled:opacity-50 disabled:cursor-not-allowed text-white font-bold text-sm shadow-md shadow-amber-700/20 flex items-center justify-center gap-2 transition-all">
                <svg class="w-4 h-4 fill-none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                <span>Bayar Sekarang</span>
            </button>
        </div>
    </div>
    </div>

    <!-- MODAL PILIH VARIAN & MODIFIERS -->
    @if($showVariantModal && $selectedProduct)
        <div class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white border border-slate-200 rounded-2xl w-full max-w-md p-6 shadow-2xl space-y-5">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900">{{ $selectedProduct->name }}</h3>
                        <p class="text-xs text-slate-500">Pilih opsi varian dan catatan pesanan</p>
                    </div>
                    <button wire:click="closeVariantModal" class="text-slate-400 hover:text-slate-700">
                        <svg class="w-5 h-5 fill-none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Variant Options Grouped -->
                <div class="space-y-4 max-h-80 overflow-y-auto p-1">
                    @foreach($selectedProduct->variants->groupBy('variant_group') as $groupName => $groupVariants)
                        <div class="space-y-2">
                            <span class="text-xs font-bold text-slate-700 block uppercase tracking-wider text-[11px]">{{ $groupName }}</span>
                            <div class="grid grid-cols-2 gap-2.5">
                                @foreach($groupVariants as $v)
                                    @php
                                        $isSelected = ($selectedVariants[$groupName] ?? null) == $v->id;
                                    @endphp
                                    <button type="button" 
                                            wire:click="$set('selectedVariants.{{ $groupName }}', {{ $v->id }})"
                                            class="py-2.5 px-3 rounded-xl text-xs transition-all text-left flex items-center justify-between {{ $isSelected ? 'border-2 border-amber-700 bg-amber-50/80 text-amber-950 font-bold shadow-2xs' : 'border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 hover:border-slate-300 font-medium' }}">
                                        <div class="flex items-center gap-2">
                                            <span class="w-3.5 h-3.5 rounded-full border flex items-center justify-center {{ $isSelected ? 'border-amber-700 bg-amber-700' : 'border-slate-300 bg-white' }}">
                                                @if($isSelected)
                                                    <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                                                @endif
                                            </span>
                                            <span>{{ $v->name }}</span>
                                        </div>
                                        @if($v->extra_price > 0)
                                            <span class="text-[11px] text-amber-800 font-extrabold">+Rp {{ number_format($v->extra_price, 0, ',', '.') }}</span>
                                        @else
                                            <span class="text-[10px] text-slate-400">Rp 0</span>
                                        @endif
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endforeach

                    <!-- Custom Notes Input -->
                    <div class="space-y-1.5 pt-3 border-t border-slate-100">
                        <label class="text-xs font-bold text-slate-700 block">Catatan Khusus (Opsional)</label>
                        <input type="text" 
                               wire:model="itemNotes" 
                               placeholder="Contoh: Kurangi gula, es sedikit, pisahkan sambal..." 
                               class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-900 focus:outline-none focus:border-amber-700 focus:bg-white">
                    </div>
                </div>

                <!-- Price Preview & Confirm Button -->
                <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-3">
                    <div>
                        <span class="text-[11px] text-slate-400 block font-medium">Total Harga Item</span>
                        <span class="text-base font-extrabold text-amber-800">
                            Rp {{ number_format($this->variantModalPrice, 0, ',', '.') }}
                        </span>
                    </div>

                    <button wire:click="confirmVariantSelection" 
                            class="py-2.5 px-5 rounded-xl bg-amber-700 hover:bg-amber-800 text-white font-bold text-xs shadow-md shadow-amber-700/20 transition-all">
                        Tambahkan ke Pesanan
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- PAYMENT MODAL -->
    @if($showPaymentModal)
        <div class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white border border-slate-200 rounded-2xl w-full max-w-md p-6 shadow-2xl space-y-5">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-amber-700 fill-none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        <span>Pembayaran Kasir</span>
                    </h3>
                    <button wire:click="closePaymentModal" class="text-slate-400 hover:text-slate-700">
                        <svg class="w-5 h-5 fill-none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Total Display -->
                <div class="bg-amber-50 rounded-xl p-4 border border-amber-200/60 text-center">
                    <span class="text-xs text-amber-800 font-bold block mb-1">Total Tagihan</span>
                    <span class="text-2xl font-black text-amber-900">
                        Rp {{ number_format($this->totalAmount, 0, ',', '.') }}
                    </span>
                </div>

                <!-- Payment Method -->
                <div>
                    <label class="text-xs text-slate-600 font-bold block mb-2">Metode Pembayaran</label>
                    <div class="grid grid-cols-3 gap-2">
                        @foreach(['cash' => 'Tunai / Cash', 'qris' => 'QRIS Static', 'transfer' => 'Transfer Bank'] as $key => $label)
                            <button type="button" 
                                    wire:click="$set('paymentMethod', '{{ $key }}')"
                                    class="py-2.5 px-3 rounded-xl border text-xs font-bold transition-all {{ $paymentMethod === $key ? 'border-amber-700 bg-amber-700 text-white shadow-xs' : 'border-slate-200 bg-slate-50 text-slate-700 hover:bg-slate-100' }}">
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <!-- Cash Quick Presets -->
                @if($paymentMethod === 'cash')
                    <div class="space-y-3">
                        <div>
                            <label class="text-xs text-slate-600 font-bold block mb-1.5">Nominal Uang Diterima (Rp)</label>
                            <input type="number" 
                                   wire:model.live="paidAmount" 
                                   class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-base font-extrabold text-slate-900 focus:outline-none focus:border-amber-700 focus:bg-white">
                            @error('paidAmount')
                                <p class="text-xs text-rose-600 font-bold mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Quick Money Buttons -->
                        <div class="grid grid-cols-4 gap-2">
                            <button type="button" wire:click="setQuickMoney({{ $this->totalAmount }})" class="py-1.5 rounded-lg bg-slate-100 text-[11px] font-bold text-slate-700 hover:bg-slate-200">Uang Pas</button>
                            <button type="button" wire:click="setQuickMoney(50000)" class="py-1.5 rounded-lg bg-slate-100 text-[11px] font-bold text-slate-700 hover:bg-slate-200">50.000</button>
                            <button type="button" wire:click="setQuickMoney(100000)" class="py-1.5 rounded-lg bg-slate-100 text-[11px] font-bold text-slate-700 hover:bg-slate-200">100.000</button>
                            <button type="button" wire:click="setQuickMoney(200000)" class="py-1.5 rounded-lg bg-slate-100 text-[11px] font-bold text-slate-700 hover:bg-slate-200">200.000</button>
                        </div>

                        <!-- Change Calculation -->
                        <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-200">
                            <span class="text-xs text-slate-600 font-bold">Kembalian</span>
                            <span class="text-lg font-black text-emerald-700">
                                Rp {{ number_format($this->changeAmount, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                @endif

                <!-- Process Button -->
                <button wire:click="processPayment" 
                        class="w-full py-3 rounded-xl bg-amber-700 hover:bg-amber-800 text-white font-bold text-sm shadow-md shadow-amber-700/20 transition-all">
                    Selesaikan Transaksi & Cetak Struk
                </button>
            </div>
        </div>
    @endif

    <!-- RECEIPT MODAL -->
    @if($showReceiptModal && $completedOrder)
        <div class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white text-slate-900 rounded-2xl w-full max-w-sm p-6 shadow-2xl space-y-4 font-mono text-xs border border-slate-200">
                
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
                            <div class="space-y-0.5">
                                <div class="flex justify-between">
                                    <span class="font-bold">{{ $item->product_name }} x{{ $item->quantity }}</span>
                                    <span>{{ number_format($item->subtotal, 0, ',', '.') }}</span>
                                </div>
                                @if(!empty($item->notes))
                                    <p class="text-[10px] text-slate-500 italic pl-2">({{ $item->notes }})</p>
                                @endif
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

                <div class="flex gap-2 pt-2 border-t border-slate-200">
                    <button onclick="window.print()" class="flex-1 py-2 bg-slate-900 text-white rounded-lg font-sans font-bold text-xs flex items-center justify-center gap-1">
                        <span>Cetak Struk</span>
                    </button>
                    <button wire:click="closeReceiptModal" class="px-4 py-2 bg-slate-200 text-slate-800 rounded-lg font-sans font-bold text-xs">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- MODAL: DAFTAR & KONFIRMASI PESANAN QR MEJA -->
    @if($showIncomingOrdersModal)
        <div class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white border border-slate-200 rounded-3xl w-full max-w-3xl max-h-[90vh] shadow-2xl flex flex-col overflow-hidden animate-in fade-in zoom-in-95 duration-150">
                <!-- Modal Header -->
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-amber-800 text-white flex items-center justify-center shadow-xs">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-extrabold text-slate-900">Pesanan Masuk dari QR Meja</h3>
                            <p class="text-xs text-slate-500 font-medium">Tinjau, terima pembayaran, atau teruskan pesanan pelanggan ke dapur</p>
                        </div>
                    </div>
                    <button type="button" wire:click="closeIncomingOrdersModal" class="w-9 h-9 rounded-xl text-slate-400 hover:text-slate-700 hover:bg-slate-200/60 flex items-center justify-center transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Modal Content -->
                <div class="flex-1 flex min-h-0 overflow-hidden">
                    @if($incomingOrders->isEmpty())
                        <div class="flex-1 p-12 text-center flex flex-col items-center justify-center">
                            <div class="w-16 h-16 rounded-2xl bg-amber-50 border border-amber-200 flex items-center justify-center text-amber-800 mb-3">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <h4 class="font-bold text-slate-900 text-sm">Tidak Ada Pesanan Tertunda</h4>
                            <p class="text-xs text-slate-500 mt-1 max-w-xs">Semua pesanan meja pelanggan telah dikonfirmasi atau belum ada pesanan baru yang masuk.</p>
                        </div>
                    @else
                        <!-- List of Orders (Left side, 38% width) -->
                        <div class="w-72 border-r border-slate-200 overflow-y-auto p-3 space-y-2.5 bg-slate-50/50">
                            @foreach($incomingOrders as $incOrder)
                                @php
                                    $isSelected = ($selectedIncomingOrderId ?? $incomingOrders->first()->id) === $incOrder->id;
                                @endphp
                                <button type="button"
                                        wire:click="selectIncomingOrder({{ $incOrder->id }})"
                                        class="w-full text-left p-3.5 rounded-2xl border transition-all {{ $isSelected ? 'bg-white border-amber-700 ring-2 ring-amber-700/20 shadow-xs' : 'bg-white border-slate-200 hover:border-slate-300' }}">
                                    <div class="flex items-center justify-between mb-1.5">
                                        <span class="px-2 py-0.5 rounded-lg text-xs font-black bg-amber-100 text-amber-900">
                                            {{ $incOrder->table?->table_number ?? 'Meja ?' }}
                                        </span>
                                        <span class="text-[10px] text-slate-400 font-medium">
                                            {{ $incOrder->created_at->diffForHumans(null, true) }}
                                        </span>
                                    </div>
                                    <h4 class="text-xs font-bold text-slate-900 truncate">{{ $incOrder->customer_name }}</h4>
                                    <div class="flex items-center justify-between mt-2 pt-2 border-t border-slate-100 text-xs">
                                        <span class="text-[11px] text-slate-500">{{ $incOrder->items->sum('quantity') }} Item</span>
                                        <span class="font-extrabold text-amber-900">Rp {{ number_format($incOrder->total_amount, 0, ',', '.') }}</span>
                                    </div>
                                </button>
                            @endforeach
                        </div>

                        <!-- Order Detail & Actions (Right side) -->
                        @php
                            $currentOrder = $incomingOrders->firstWhere('id', $selectedIncomingOrderId ?? $incomingOrders->first()->id) ?? $incomingOrders->first();
                        @endphp
                        @if($currentOrder)
                            <div class="flex-1 flex flex-col min-h-0 bg-white p-5 overflow-y-auto space-y-4">
                                <!-- Order Header Meta -->
                                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <span class="text-base font-extrabold text-slate-900">{{ $currentOrder->table?->table_number }}</span>
                                            <span class="text-xs text-slate-400">|</span>
                                            <span class="text-xs font-bold text-slate-700">{{ $currentOrder->customer_name }}</span>
                                            @if($currentOrder->customer_phone)
                                                <span class="text-[11px] text-slate-500">({{ $currentOrder->customer_phone }})</span>
                                            @endif
                                        </div>
                                        <p class="text-[11px] text-slate-400 mt-0.5">{{ $currentOrder->invoice_number }} &bull; Dibuat {{ $currentOrder->created_at->format('H:i:s') }}</p>
                                    </div>

                                    <div>
                                        @if($currentOrder->payment_status === 'paid')
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                                Sudah Dibayar (Online)
                                            </span>
                                        @else
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-900">
                                                Bayar di Kasir
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                @if($currentOrder->notes)
                                    <div class="p-3 rounded-xl bg-amber-50 border border-amber-200/80 text-xs text-amber-900">
                                        <span class="font-bold">Catatan Pelanggan:</span> {{ $currentOrder->notes }}
                                    </div>
                                @endif

                                <!-- Items List -->
                                <div class="space-y-2 flex-1">
                                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Rincian Menu Dipesan</p>
                                    <div class="divide-y divide-slate-100 border border-slate-200 rounded-2xl overflow-hidden">
                                        @foreach($currentOrder->items as $item)
                                            <div class="p-3 flex items-center justify-between text-xs bg-slate-50/40">
                                                <div class="min-w-0 flex-1 pr-3">
                                                    <p class="font-bold text-slate-900 leading-snug">
                                                        <span class="text-amber-800 font-extrabold mr-1">{{ $item->quantity }}x</span>
                                                        {{ $item->product_name }}
                                                    </p>
                                                    @if($item->notes)
                                                        <p class="text-[11px] text-slate-500 mt-0.5 leading-snug">{{ $item->notes }}</p>
                                                    @endif
                                                </div>
                                                <div class="text-right font-extrabold text-slate-800 shrink-0">
                                                    Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Financial Breakdown -->
                                <div class="p-3.5 rounded-2xl bg-slate-100 border border-slate-200 space-y-1 text-xs">
                                    <div class="flex justify-between text-slate-500">
                                        <span>Subtotal</span>
                                        <span>Rp {{ number_format($currentOrder->subtotal, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="flex justify-between text-slate-500">
                                        <span>PPN (11%)</span>
                                        <span>Rp {{ number_format($currentOrder->tax_amount, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="pt-1.5 border-t border-slate-200/80 flex justify-between font-extrabold text-slate-900 text-sm">
                                        <span>Total Tagihan</span>
                                        <span class="text-amber-900">Rp {{ number_format($currentOrder->total_amount, 0, ',', '.') }}</span>
                                    </div>
                                </div>

                                <!-- Payment Confirmation Inputs (if not already paid) -->
                                @if($currentOrder->payment_status !== 'paid')
                                    <div class="p-3.5 rounded-2xl bg-amber-50/60 border border-amber-200 space-y-3">
                                        <p class="text-xs font-bold text-amber-900 uppercase tracking-wider">Penerimaan Pembayaran Kasir</p>
                                        <div class="grid grid-cols-2 gap-2">
                                            <button type="button"
                                                    wire:click="$set('confirmPaymentMethod', 'cash')"
                                                    class="py-2 px-3 rounded-xl text-xs font-bold border transition-all {{ $confirmPaymentMethod === 'cash' ? 'bg-amber-800 text-white border-amber-800 shadow-xs' : 'bg-white border-slate-200 text-slate-700' }}">
                                                Tunai / Cash
                                            </button>
                                            <button type="button"
                                                    wire:click="$set('confirmPaymentMethod', 'qris')"
                                                    class="py-2 px-3 rounded-xl text-xs font-bold border transition-all {{ $confirmPaymentMethod === 'qris' ? 'bg-amber-800 text-white border-amber-800 shadow-xs' : 'bg-white border-slate-200 text-slate-700' }}">
                                                QRIS Kasir
                                            </button>
                                        </div>

                                        @if($confirmPaymentMethod === 'cash')
                                            <div wire:key="confirm-paid-section-{{ $currentOrder->id }}">
                                                <div class="flex items-center justify-between mb-1">
                                                    <label class="block text-[11px] font-bold text-slate-700">Nominal Uang Diterima (Rp)</label>
                                                    <span class="text-[10px] text-slate-400">Total: Rp {{ number_format($currentOrder->total_amount, 0, ',', '.') }}</span>
                                                </div>
                                                <input type="number"
                                                       wire:key="confirm-paid-input-{{ $currentOrder->id }}"
                                                       wire:model.live.debounce.300ms="confirmPaidAmount"
                                                       class="w-full px-3 py-2 text-xs rounded-xl bg-white border border-slate-300 font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-700">

                                                <div class="grid grid-cols-4 gap-1.5 mt-2">
                                                    <button type="button"
                                                            wire:click="setConfirmQuickMoney({{ $currentOrder->total_amount }})"
                                                            class="py-1 px-2 rounded-lg bg-white border border-slate-200 hover:bg-slate-50 text-[10px] font-bold text-slate-700 transition-colors">
                                                        Uang Pas
                                                    </button>
                                                    <button type="button"
                                                            wire:click="setConfirmQuickMoney(50000)"
                                                            class="py-1 px-2 rounded-lg bg-white border border-slate-200 hover:bg-slate-50 text-[10px] font-bold text-slate-700 transition-colors">
                                                        50.000
                                                    </button>
                                                    <button type="button"
                                                            wire:click="setConfirmQuickMoney(100000)"
                                                            class="py-1 px-2 rounded-lg bg-white border border-slate-200 hover:bg-slate-50 text-[10px] font-bold text-slate-700 transition-colors">
                                                        100.000
                                                    </button>
                                                    <button type="button"
                                                            wire:click="setConfirmQuickMoney(200000)"
                                                            class="py-1 px-2 rounded-lg bg-white border border-slate-200 hover:bg-slate-50 text-[10px] font-bold text-slate-700 transition-colors">
                                                        200.000
                                                    </button>
                                                </div>

                                                @if((float) $confirmPaidAmount > (float) $currentOrder->total_amount)
                                                    <p class="text-xs text-emerald-700 font-bold mt-2">
                                                        Kembalian: Rp {{ number_format((float) $confirmPaidAmount - (float) $currentOrder->total_amount, 0, ',', '.') }}
                                                    </p>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                @endif

                                <!-- Actions -->
                                <div class="pt-2 flex items-center gap-3">
                                    <button type="button"
                                            wire:click="rejectIncomingOrder({{ $currentOrder->id }})"
                                            wire:confirm="Yakin ingin membatalkan/menolak pesanan dari {{ $currentOrder->table?->table_number }} ini? Stok akan dikembalikan."
                                            class="min-h-[44px] px-4 rounded-xl border border-red-200 text-red-600 hover:bg-red-50 text-xs font-bold transition-colors">
                                        Tolak Pesanan
                                    </button>

                                    <button type="button"
                                            wire:click="confirmIncomingOrder({{ $currentOrder->id }})"
                                            class="flex-1 min-h-[44px] rounded-xl bg-amber-800 hover:bg-amber-900 active:scale-98 text-white text-xs font-bold flex items-center justify-center gap-2 shadow-xs transition-all">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        <span>Konfirmasi & Teruskan ke Dapur</span>
                                    </button>
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- Audio Chime Script (Web Audio API) -->
    <script>
        function playBrewOrderChime() {
            try {
                const AudioCtx = window.AudioContext || window.webkitAudioContext;
                if (!AudioCtx) return;
                const ctx = new AudioCtx();
                const now = ctx.currentTime;
                
                const osc1 = ctx.createOscillator();
                const gain1 = ctx.createGain();
                osc1.type = 'sine';
                osc1.frequency.setValueAtTime(587.33, now);
                gain1.gain.setValueAtTime(0.25, now);
                gain1.gain.exponentialRampToValueAtTime(0.001, now + 0.35);
                osc1.connect(gain1);
                gain1.connect(ctx.destination);
                osc1.start(now);
                osc1.stop(now + 0.35);

                const osc2 = ctx.createOscillator();
                const gain2 = ctx.createGain();
                osc2.type = 'sine';
                osc2.frequency.setValueAtTime(880, now + 0.12);
                gain2.gain.setValueAtTime(0.3, now + 0.12);
                gain2.gain.exponentialRampToValueAtTime(0.001, now + 0.6);
                osc2.connect(gain2);
                gain2.connect(ctx.destination);
                osc2.start(now + 0.12);
                osc2.stop(now + 0.6);
            } catch (e) {
                console.warn('Audio chime error:', e);
            }
        }
        window.addEventListener('play-order-chime', () => {
            playBrewOrderChime();
        });
    </script>
</div>
