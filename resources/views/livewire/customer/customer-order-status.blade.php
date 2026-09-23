<div wire:poll.4s class="flex-1 flex flex-col p-4 space-y-4 bg-slate-50 min-h-screen pb-12">
    <!-- Top Header Navigation -->
    <div class="flex items-center justify-between gap-3 pt-2">
        <a href="{{ route('customer.order', $token) }}"
           class="min-h-[44px] px-3 rounded-xl bg-white border border-slate-200 text-slate-700 text-xs font-bold inline-flex items-center gap-1.5 shadow-2xs hover:bg-slate-100 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            <span>Katalog Menu</span>
        </a>

        <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-amber-50 border border-amber-200 text-amber-900 text-xs font-bold">
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            <span>{{ $table->table_number }}</span>
        </div>
    </div>

    <!-- Live Status Banner Card -->
    <div class="bg-white rounded-3xl p-5 border border-slate-200 shadow-xs text-center space-y-3">
        @if($order->order_status === 'pending_confirmation')
            <div class="w-16 h-16 mx-auto rounded-2xl bg-amber-100 border border-amber-200 flex items-center justify-center text-amber-800 animate-bounce">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-amber-100 text-amber-800">
                    Menunggu Konfirmasi Kasir
                </span>
                <h2 class="text-lg font-extrabold text-slate-900 mt-2">Pesanan Anda Telah Masuk</h2>
                @if($order->payment_status === 'pending')
                    <p class="text-xs text-slate-600 mt-1 max-w-xs mx-auto leading-relaxed">
                        Silakan sebutkan nomor meja <span class="font-bold text-amber-900">({{ $table->table_number }})</span> ke kasir untuk pembayaran, atau tunggu kasir mengonfirmasi pesanan Anda.
                    </p>
                @else
                    <p class="text-xs text-slate-600 mt-1 max-w-xs mx-auto leading-relaxed">
                        Pembayaran telah terverifikasi. Pesanan sedang menunggu konfirmasi staf kasir untuk diteruskan ke dapur.
                    </p>
                @endif
            </div>

        @elseif($order->order_status === 'pending')
            <div class="w-16 h-16 mx-auto rounded-2xl bg-blue-100 border border-blue-200 flex items-center justify-center text-blue-800">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <div>
                <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-blue-100 text-blue-800">
                    Dikonfirmasi Kasir
                </span>
                <h2 class="text-lg font-extrabold text-slate-900 mt-2">Dalam Antrian Dapur</h2>
                <p class="text-xs text-slate-600 mt-1 max-w-xs mx-auto leading-relaxed">
                    Pesanan sudah dikonfirmasi kasir dan telah masuk ke sistem antrian dapur untuk segera diracik.
                </p>
            </div>

        @elseif($order->order_status === 'cooking')
            <div class="w-16 h-16 mx-auto rounded-2xl bg-amber-100 border border-amber-200 flex items-center justify-center text-amber-800">
                <svg class="w-8 h-8 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
            </div>
            <div>
                <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-amber-100 text-amber-900">
                    Sedang Dimasak
                </span>
                <h2 class="text-lg font-extrabold text-slate-900 mt-2">Sedang Diracik Barista & Dapur</h2>
                <p class="text-xs text-slate-600 mt-1 max-w-xs mx-auto leading-relaxed">
                    Pesanan Anda sedang disiapkan dengan teliti. Mohon ditunggu di meja Anda.
                </p>
            </div>

        @elseif($order->order_status === 'ready')
            <div class="w-16 h-16 mx-auto rounded-2xl bg-emerald-100 border border-emerald-200 flex items-center justify-center text-emerald-800 animate-pulse">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <div>
                <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-emerald-100 text-emerald-800">
                    Siap Disajikan
                </span>
                <h2 class="text-lg font-extrabold text-slate-900 mt-2">Pesanan Siap!</h2>
                <p class="text-xs text-slate-600 mt-1 max-w-xs mx-auto leading-relaxed">
                    Pesanan Anda sudah selesai dibuat dan sedang diantarkan ke meja Anda.
                </p>
            </div>

        @elseif($order->order_status === 'completed')
            <div class="w-16 h-16 mx-auto rounded-2xl bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-700">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"/>
                </svg>
            </div>
            <div>
                <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-slate-100 text-slate-700">
                    Pesanan Selesai
                </span>
                <h2 class="text-lg font-extrabold text-slate-900 mt-2">Selamat Menikmati!</h2>
                <p class="text-xs text-slate-600 mt-1 max-w-xs mx-auto leading-relaxed">
                    Terima kasih telah memesan di BrewPOS. Semoga Anda menikmati santapan hari ini!
                </p>
            </div>

        @elseif($order->order_status === 'cancelled')
            <div class="w-16 h-16 mx-auto rounded-2xl bg-red-100 border border-red-200 flex items-center justify-center text-red-800">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </div>
            <div>
                <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-red-100 text-red-800">
                    Dibatalkan
                </span>
                <h2 class="text-lg font-extrabold text-slate-900 mt-2">Pesanan Dibatalkan</h2>
                <p class="text-xs text-slate-600 mt-1 max-w-xs mx-auto leading-relaxed">
                    Pesanan ini telah dibatalkan oleh kasir/staf. Silakan hubungi kasir kami untuk info lebih lanjut.
                </p>
            </div>
        @endif

        <!-- Progress Steps Tracker -->
        @if($order->order_status !== 'cancelled')
            @php
                $statusOrder = ['pending_confirmation' => 1, 'pending' => 2, 'cooking' => 3, 'ready' => 4, 'completed' => 5];
                $currentStep = $statusOrder[$order->order_status] ?? 1;
            @endphp
            <div class="pt-4 border-t border-slate-100 flex items-center justify-between text-center relative px-2">
                <div class="absolute left-6 right-6 top-7 h-0.5 bg-slate-200 -z-0">
                    <div class="h-full bg-amber-700 transition-all duration-300" style="width: {{ min(100, max(0, ($currentStep - 1) * 33)) }}%;"></div>
                </div>

                <div class="flex flex-col items-center relative z-10">
                    <div class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-bold {{ $currentStep >= 1 ? 'bg-amber-800 text-white' : 'bg-slate-200 text-slate-500' }}">
                        1
                    </div>
                    <span class="text-[10px] font-semibold text-slate-700 mt-1">Order</span>
                </div>

                <div class="flex flex-col items-center relative z-10">
                    <div class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-bold {{ $currentStep >= 2 ? 'bg-amber-800 text-white' : 'bg-slate-200 text-slate-500' }}">
                        2
                    </div>
                    <span class="text-[10px] font-semibold text-slate-700 mt-1">Antrian</span>
                </div>

                <div class="flex flex-col items-center relative z-10">
                    <div class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-bold {{ $currentStep >= 3 ? 'bg-amber-800 text-white' : 'bg-slate-200 text-slate-500' }}">
                        3
                    </div>
                    <span class="text-[10px] font-semibold text-slate-700 mt-1">Masak</span>
                </div>

                <div class="flex flex-col items-center relative z-10">
                    <div class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-bold {{ $currentStep >= 4 ? 'bg-amber-800 text-white' : 'bg-slate-200 text-slate-500' }}">
                        4
                    </div>
                    <span class="text-[10px] font-semibold text-slate-700 mt-1">Siap</span>
                </div>
            </div>
        @endif
    </div>

    <!-- Order Details Card -->
    <div class="bg-white rounded-3xl p-5 border border-slate-200 shadow-xs space-y-3.5">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div>
                <p class="text-xs text-slate-400 font-medium">Nomor Transaksi</p>
                <p class="text-sm font-extrabold text-slate-900 tracking-tight">{{ $order->invoice_number }}</p>
            </div>
            <div class="text-right">
                <p class="text-xs text-slate-400 font-medium">Waktu Pesan</p>
                <p class="text-xs font-bold text-slate-700">{{ $order->created_at->format('H:i, d M Y') }}</p>
            </div>
        </div>

        <div>
            <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Item Pesanan</p>
            <div class="space-y-2">
                @foreach($order->items as $item)
                    <div class="flex items-start justify-between text-xs py-1.5 border-b border-slate-50 last:border-0">
                        <div class="min-w-0 flex-1 pr-2">
                            <p class="font-bold text-slate-900 leading-snug">
                                <span class="text-amber-800 font-black mr-1">{{ $item->quantity }}x</span>
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

        <div class="pt-3 border-t border-slate-100 space-y-1.5 text-xs">
            <div class="flex justify-between text-slate-500">
                <span>Subtotal</span>
                <span>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between text-slate-500">
                <span>PPN (11%)</span>
                <span>Rp {{ number_format($order->tax_amount, 0, ',', '.') }}</span>
            </div>
            <div class="pt-2 border-t border-slate-100 flex justify-between text-sm font-black text-slate-900">
                <span>Total Tagihan</span>
                <span class="text-amber-900 font-extrabold">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="pt-2 text-[11px] text-slate-500 flex items-center justify-between">
            <span>Metode Bayar:</span>
            <span class="font-bold text-slate-800 uppercase">
                {{ $order->payment_method === 'qris' ? 'QRIS Online' : ($order->payment_method === 'cash' ? 'Tunai' : 'Bayar di Kasir') }}
                ({{ $order->payment_status === 'paid' ? 'LUNAS' : 'BELUM BAYAR' }})
            </span>
        </div>
    </div>

    <!-- Additional Order Action Button -->
    <div>
        <a href="{{ route('customer.order', $token) }}"
           class="w-full min-h-[48px] rounded-2xl bg-amber-800 hover:bg-amber-900 active:scale-98 text-white font-bold text-xs flex items-center justify-center gap-2 shadow-xs transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            <span>Pesan Menu Tambahan</span>
        </a>
    </div>
</div>
