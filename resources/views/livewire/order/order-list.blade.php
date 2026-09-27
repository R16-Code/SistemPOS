<div class="p-6 space-y-6 overflow-y-auto h-full bg-slate-50">
    <div class="flex items-center justify-between gap-4 flex-wrap">
        <div>
            <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Daftar Transaksi Penjualan</h2>
            <p class="text-xs text-slate-500 mt-1">Riwayat semua transaksi penjualan dan status pemrosesan dapur</p>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            <!-- Date Filter Quick Buttons -->
            <div class="bg-white border border-slate-200 p-1 rounded-xl flex items-center gap-1 text-xs">
                <button type="button" wire:click="setDateRange('all')" class="px-3 py-1.5 rounded-lg font-bold transition-all {{ $dateRange === 'all' ? 'bg-amber-700 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}">
                    Semua
                </button>
                <button type="button" wire:click="setDateRange('today')" class="px-3 py-1.5 rounded-lg font-bold transition-all {{ $dateRange === 'today' ? 'bg-amber-700 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}">
                    Hari Ini
                </button>
                <button type="button" wire:click="setDateRange('this_week')" class="px-3 py-1.5 rounded-lg font-bold transition-all {{ $dateRange === 'this_week' ? 'bg-amber-700 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}">
                    7 Hari
                </button>
                <button type="button" wire:click="setDateRange('this_month')" class="px-3 py-1.5 rounded-lg font-bold transition-all {{ $dateRange === 'this_month' ? 'bg-amber-700 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}">
                    Bulan Ini
                </button>
            </div>

            <!-- Custom Date Inputs -->
            <div class="flex items-center gap-1.5 bg-white border border-slate-200 px-3 py-1.5 rounded-xl text-xs">
                <input type="date" wire:model.live="startDate" class="text-slate-800 font-medium focus:outline-none bg-transparent">
                <span class="text-slate-400">s/d</span>
                <input type="date" wire:model.live="endDate" class="text-slate-800 font-medium focus:outline-none bg-transparent">
            </div>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
        <div class="relative w-full sm:w-72">
            <svg class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2 fill-none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" wire:model.live.debounce.250ms="search" placeholder="Cari No Invoice / Pelanggan..." class="w-full bg-white border border-slate-200 rounded-xl pl-9 pr-4 py-2 text-xs text-slate-900 focus:outline-none focus:border-amber-700 shadow-2xs">
        </div>

        <div class="flex items-center gap-2 sm:gap-3 flex-wrap">
            <!-- Filter Status Pesanan Dapur -->
            <select wire:model.live="orderStatus" class="bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-900 font-medium focus:outline-none focus:border-amber-700 shadow-2xs flex-1 sm:flex-none">
                <option value="">Semua Status Dapur</option>
                <option value="pending">Menunggu (Pending)</option>
                <option value="cooking">Sedang Diracik (Cooking)</option>
                <option value="ready">Siap Saji (Ready)</option>
                <option value="served">Telah Disajikan (Served)</option>
                <option value="completed">Selesai (Completed)</option>
            </select>

            <!-- Filter Status Pembayaran -->
            <select wire:model.live="paymentStatus" class="bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-900 font-medium focus:outline-none focus:border-amber-700 shadow-2xs flex-1 sm:flex-none">
                <option value="">Semua Pembayaran</option>
                <option value="paid">Lunas (Paid)</option>
                <option value="pending">Belum Lunas (Pending)</option>
            </select>
        </div>
    </div>

    <div class="bg-white border border-slate-200/80 rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-245 text-left text-xs text-slate-700">
                <thead class="bg-slate-100/70 text-slate-600 border-b border-slate-200 font-bold uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="px-4 py-3.5 whitespace-nowrap">Invoice</th>
                        <th class="px-3 py-3.5 whitespace-nowrap">Waktu</th>
                        <th class="px-4 py-3.5 min-w-35">Pelanggan & Meja</th>
                        <th class="px-4 py-3.5 min-w-45">Menu Dipesan</th>
                        <th class="px-4 py-3.5 text-right whitespace-nowrap">Total</th>
                        <th class="px-3 py-3.5 text-center whitespace-nowrap">Status Dapur</th>
                        <th class="px-3 py-3.5 text-center whitespace-nowrap">Pembayaran</th>
                        <th class="px-3 py-3.5 text-center whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($orders as $order)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-4 py-3 font-bold text-slate-900 font-mono whitespace-nowrap text-[11px]">{{ $order->invoice_number }}</td>
                            <td class="px-3 py-3 text-slate-500 whitespace-nowrap text-[11px]">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-3">
                                <span class="font-bold text-slate-900 block leading-tight truncate">{{ $order->customer_name }}</span>
                                <span class="text-[10px] text-slate-500 capitalize">
                                    {{ $order->order_type }}
                                    @if($order->table)
                                        &bull; <strong class="text-amber-800">{{ $order->table->table_number }}</strong>
                                    @endif
                                </span>
                            </td>
                            
                            <!-- Menu Dipesan: Ringkas & Klik untuk Rincian -->
                            <td class="px-3 py-3">
                                @if(count($order->items) > 0)
                                    @php
                                        $firstItem = $order->items->first();
                                        $remainingCount = count($order->items) - 1;
                                    @endphp
                                    <div class="text-[11px] leading-snug">
                                        <button type="button" 
                                                wire:click="showOrderDetail({{ $order->id }})" 
                                                title="Klik untuk melihat rincian pesanan"
                                                class="text-left group/item cursor-pointer block w-full">
                                            <div class="font-medium text-slate-800 truncate group-hover/item:text-amber-900 transition-colors">
                                                <span class="font-bold text-slate-900">{{ $firstItem->quantity }}x</span> {{ $firstItem->product_name }}
                                                @if($firstItem->notes)
                                                    <span class="text-amber-800 font-normal italic text-[10px]">({{ $firstItem->notes }})</span>
                                                @endif
                                            </div>

                                            @if($remainingCount > 0)
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 mt-0.5 rounded-md text-[10px] font-bold text-amber-900 bg-amber-50 group-hover/item:bg-amber-100 border border-amber-200/80 transition-all">
                                                    <span>+{{ $remainingCount }} menu lainnya</span>
                                                    <svg class="w-2.5 h-2.5 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                                </span>
                                            @endif
                                        </button>
                                    </div>
                                @else
                                    <span class="text-slate-400 italic text-[11px]">-</span>
                                @endif
                            </td>

                            <td class="px-3 py-3 font-extrabold text-amber-800 whitespace-nowrap text-right text-xs">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                            
                            <!-- Status Pesanan Dapur (Badge Informatif) -->
                            <td class="px-3 py-3 text-center whitespace-nowrap">
                                @if($order->order_status === 'pending')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-amber-100 text-amber-900 border border-amber-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                        <span>Menunggu</span>
                                    </span>
                                @elseif($order->order_status === 'cooking')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-blue-100 text-blue-900 border border-blue-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                        <span>Sedang Diracik</span>
                                    </span>
                                @elseif($order->order_status === 'ready')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-100 text-emerald-900 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        <span>Siap Saji</span>
                                    </span>
                                @elseif($order->order_status === 'served')
                                    <span class="inline-flex items-center justify-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-teal-100 text-teal-900 border border-teal-200" style="background-color: #ccfbf1; color: #134e4a; border-color: #99f6e4;">
                                        <span>Telah Disajikan</span>
                                    </span>
                                @elseif($order->order_status === 'completed')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-slate-100 text-slate-700 border border-slate-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                        <span>Selesai</span>
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-rose-100 text-rose-800 border border-rose-200">
                                        {{ $order->order_status }}
                                    </span>
                                @endif
                            </td>

                            <!-- Status Pembayaran -->
                            <td class="px-3 py-3 text-center whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $order->payment_status === 'paid' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                                    {{ $order->payment_status === 'paid' ? 'LUNAS' : 'PENDING' }}
                                </span>
                                <span class="text-[9px] text-slate-400 uppercase font-mono block mt-0.5">{{ $order->payment_method }}</span>
                            </td>

                            <!-- Aksi Cetak Ulang Struk -->
                            <td class="px-3 py-3 text-center whitespace-nowrap">
                                <button wire:click="showReceipt({{ $order->id }})" 
                                        type="button" 
                                        title="Cetak Ulang Struk"
                                        class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold text-slate-700 bg-slate-100 hover:bg-amber-50 hover:text-amber-900 border border-slate-200/80 hover:border-amber-200 transition-all shadow-2xs cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                    <span>Struk</span>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-slate-400">Belum ada transaksi ditemukan untuk filter ini</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $orders->links() }}
        </div>
    </div>

    <!-- MODAL RINCIAN PESANAN LENGKAP -->
    @if($showOrderDetailModal && $selectedOrderDetail)
        <div class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl w-full max-w-lg shadow-2xl border border-slate-200 overflow-hidden flex flex-col max-h-[85vh] p-6">
                <div class="flex items-start justify-between border-b border-slate-100 pb-3 shrink-0">
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-base font-black text-slate-900 font-mono">{{ $selectedOrderDetail->invoice_number }}</h3>
                            @if($selectedOrderDetail->order_status === 'pending')
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-amber-100 text-amber-900 border border-amber-200">
                                    Menunggu
                                </span>
                            @elseif($selectedOrderDetail->order_status === 'cooking')
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-blue-100 text-blue-900 border border-blue-200">
                                    Sedang Diracik
                                </span>
                            @elseif($selectedOrderDetail->order_status === 'ready')
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-100 text-emerald-900 border border-emerald-200">
                                    Siap Saji
                                </span>
                            @elseif($selectedOrderDetail->order_status === 'served')
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-teal-100 text-teal-900 border border-teal-200">
                                    Telah Disajikan
                                </span>
                            @elseif($selectedOrderDetail->order_status === 'completed')
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-slate-100 text-slate-700 border border-slate-200">
                                    Selesai
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-rose-100 text-rose-800 border border-rose-200">
                                    {{ $selectedOrderDetail->order_status }}
                                </span>
                            @endif
                        </div>
                        <p class="text-xs text-slate-500 mt-1">
                            {{ $selectedOrderDetail->created_at->format('d F Y, H:i') }} &bull; Pelanggan: <strong class="text-slate-800">{{ $selectedOrderDetail->customer_name }}</strong>
                            <span class="capitalize">({{ $selectedOrderDetail->order_type }} @if($selectedOrderDetail->table) &bull; Meja {{ $selectedOrderDetail->table->table_number }} @endif)</span>
                        </p>
                    </div>
                    <button wire:click="closeOrderDetailModal" type="button" class="text-slate-400 hover:text-slate-700 transition-colors p-1 cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- List of items -->
                <div class="space-y-2 max-h-60 overflow-y-auto pr-1">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500 block">Daftar Menu Dipesan ({{ count($selectedOrderDetail->items) }} Item)</span>
                    <div class="divide-y divide-slate-100 border border-slate-200/80 rounded-xl overflow-hidden bg-slate-50/50">
                        @foreach($selectedOrderDetail->items as $item)
                            <div class="p-3 flex items-start justify-between text-xs gap-3">
                                <div>
                                    <div class="font-bold text-slate-900 text-[13px]">
                                        <span>{{ $item->quantity }}x</span> {{ $item->product_name }}
                                    </div>
                                    @if($item->notes)
                                        <p class="text-[11px] text-amber-900 font-medium italic mt-0.5">Catatan Varian: {{ $item->notes }}</p>
                                    @endif
                                    <span class="text-[10px] text-slate-400 font-mono">@ Rp {{ number_format($item->unit_price, 0, ',', '.') }}</span>
                                </div>
                                <div class="font-extrabold text-slate-900 font-mono whitespace-nowrap text-right pt-0.5">
                                    Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Financial summary -->
                <div class="bg-slate-50 rounded-xl p-3.5 space-y-1.5 text-xs border border-slate-200/60">
                    <div class="flex justify-between text-slate-600">
                        <span>Subtotal:</span>
                        <span class="font-bold text-slate-900">Rp {{ number_format($selectedOrderDetail->subtotal, 0, ',', '.') }}</span>
                    </div>
                    @if($selectedOrderDetail->discount_amount > 0)
                        <div class="flex justify-between text-emerald-700 font-bold">
                            <span>Diskon:</span>
                            <span>- Rp {{ number_format($selectedOrderDetail->discount_amount, 0, ',', '.') }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between text-slate-600">
                        <span>PPN (11%):</span>
                        <span class="font-bold text-slate-900">Rp {{ number_format($selectedOrderDetail->tax_amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-sm font-black text-slate-950 border-t border-slate-200 pt-2">
                        <span>Total Pembayaran:</span>
                        <span class="text-amber-900">Rp {{ number_format($selectedOrderDetail->total_amount, 0, ',', '.') }}</span>
                    </div>
                </div>

                <!-- Modal Actions -->
                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100 shrink-0">
                    <button wire:click="showReceipt({{ $selectedOrderDetail->id }})" 
                            type="button" 
                            class="min-h-11 px-4 py-2 bg-amber-800 hover:bg-amber-900 text-white rounded-xl text-xs font-bold transition-all inline-flex items-center gap-1.5 shadow-sm shadow-amber-800/20 cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>Cetak Struk</span>
                    </button>
                    <button wire:click="closeOrderDetailModal" type="button" class="min-h-11 px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition-colors cursor-pointer flex items-center justify-center">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- MODAL CETAK STRUK THERMAL -->
    @if($showReceiptModal && $selectedOrderForReceipt)
        <div class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white text-slate-900 rounded-2xl w-full max-w-sm p-6 shadow-2xl space-y-4 font-mono text-xs border border-slate-200 max-h-[85vh] overflow-y-auto">
                
                <div id="thermal-receipt-modal" class="space-y-3">
                    @php
                        $receiptLogo = \App\Models\Setting::get('store_logo');
                        $receiptStoreName = \App\Models\Setting::get('store_name', 'BREWPOS COFFEE');
                        $receiptAddress = \App\Models\Setting::get('store_address', 'Jl. Mawar No. 123, Jakarta');
                        $receiptPhone = \App\Models\Setting::get('store_phone', '0812-3456-7890');
                    @endphp
                    <div class="text-center space-y-1">
                        @if($receiptLogo && \Illuminate\Support\Facades\Storage::disk('public')->exists($receiptLogo))
                            <img src="{{ asset('storage/' . $receiptLogo) }}" alt="Logo" class="w-12 h-12 mx-auto object-contain mb-1">
                        @endif
                        <h2 class="font-bold text-base text-black uppercase tracking-wider">{{ $receiptStoreName }}</h2>
                        <p class="text-[11px] text-slate-600">{{ $receiptAddress }}</p>
                        <p class="text-[11px] text-slate-600">Telp: {{ $receiptPhone }}</p>
                        <div class="border-b border-dashed border-slate-400 my-2"></div>
                    </div>

                    <div class="space-y-0.5 text-[11px]">
                        <p>No: <span class="font-bold">{{ $selectedOrderForReceipt->invoice_number }}</span></p>
                        <p>Tanggal: {{ $selectedOrderForReceipt->created_at->format('d/m/Y H:i') }}</p>
                        <p>Kasir: {{ $selectedOrderForReceipt->user->name ?? 'Kasir' }}</p>
                        <p>Pelanggan: {{ $selectedOrderForReceipt->customer_name }}</p>
                        <p>Tipe: {{ strtoupper($selectedOrderForReceipt->order_type) }} @if($selectedOrderForReceipt->table) ({{ $selectedOrderForReceipt->table->table_number }}) @endif</p>
                    </div>

                    <div class="border-b border-dashed border-slate-400"></div>

                    <!-- Items -->
                    <div class="space-y-1">
                        @foreach($selectedOrderForReceipt->items as $item)
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
                            <span>{{ number_format($selectedOrderForReceipt->subtotal, 0, ',', '.') }}</span>
                        </div>
                        @if($selectedOrderForReceipt->discount_amount > 0)
                            <div class="flex justify-between">
                                <span>Diskon:</span>
                                <span>-{{ number_format($selectedOrderForReceipt->discount_amount, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between">
                            <span>PPN (11%):</span>
                            <span>{{ number_format($selectedOrderForReceipt->tax_amount, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between font-bold text-sm text-black border-t border-slate-300 pt-1">
                            <span>TOTAL:</span>
                            <span>Rp {{ number_format($selectedOrderForReceipt->total_amount, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Bayar ({{ strtoupper($selectedOrderForReceipt->payment_method) }}):</span>
                            <span>{{ number_format($selectedOrderForReceipt->paid_amount, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Kembali:</span>
                            <span>{{ number_format($selectedOrderForReceipt->change_amount, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <div class="border-b border-dashed border-slate-400 my-2"></div>

                    <div class="text-center text-[10px] text-slate-500">
                        <p>Terima kasih atas kunjungan Anda!</p>
                        <p>Powered by BrewPOS</p>
                    </div>
                </div>

                <div class="flex gap-2 pt-2 border-t border-slate-200">
                    <button onclick="printOrderReceipt()" class="flex-1 py-2.5 bg-amber-800 hover:bg-amber-900 text-white rounded-xl font-sans font-bold text-xs flex items-center justify-center gap-1.5 transition-all shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>Cetak Struk</span>
                    </button>
                    <button wire:click="closeReceiptModal" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-800 rounded-xl font-sans font-bold text-xs transition-colors">
                        Tutup
                    </button>
                </div>
            </div>
        </div>

        <script>
            function printOrderReceipt() {
                const printContents = document.getElementById('thermal-receipt-modal').innerHTML;
                const printWindow = window.open('', '', 'width=380,height=600');
                printWindow.document.write('<html><head><title>Struk Pembayaran - BrewPOS</title>');
                printWindow.document.write('<style>');
                printWindow.document.write('@page { size: 80mm auto; margin: 0; } body { font-family: monospace; font-size: 11px; padding: 14px; margin: 0; color: #000; } .text-center { text-align: center; } .flex { display: flex; } .justify-between { justify-content: space-between; } .font-bold { font-weight: bold; } .border-b { border-bottom: 1px dashed #555; } .my-2 { margin: 8px 0; } .space-y-0\\.5 > * + * { margin-top: 2px; } .space-y-1 > * + * { margin-top: 4px; } .space-y-3 > * + * { margin-top: 12px; } .uppercase { text-transform: uppercase; } .text-sm { font-size: 13px; } .text-\\[10px\\] { font-size: 10px; } .text-\\[11px\\] { font-size: 11px; } .italic { font-style: italic; } .pl-2 { padding-left: 8px; }');
                printWindow.document.write('</style></head><body>');
                printWindow.document.write(printContents);
                printWindow.document.write('</body></html>');
                printWindow.document.close();
                printWindow.focus();
                setTimeout(() => {
                    printWindow.print();
                    printWindow.close();
                }, 300);
            }
        </script>
    @endif
</div>
