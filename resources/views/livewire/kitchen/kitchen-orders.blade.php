<div wire:poll.5s class="p-6 space-y-6 overflow-y-auto h-full bg-slate-50">
    
    <!-- Top Header & Live Indicator -->
    <div class="flex items-center justify-between gap-4 flex-wrap">
        <div>
            <div class="flex items-center gap-2">
                <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Antrian Pesanan Dapur & Barista</h2>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Live 5s</span>
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-1">Pantau pesanan yang masuk dan perbarui status proses makanan/minuman</p>
        </div>

        <div class="flex items-center gap-3">
            <div class="hidden sm:flex items-center gap-2 px-3 py-1.5 bg-white border border-slate-200/80 rounded-xl text-[11px] font-medium text-slate-600 shadow-2xs">
                <span class="text-slate-400 font-semibold text-[10px] uppercase tracking-wider mr-1">Waktu Tunggu:</span>
                <span class="inline-flex items-center gap-1 text-emerald-800"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>&lt;15m</span>
                <span class="text-slate-300">&bull;</span>
                <span class="inline-flex items-center gap-1 text-amber-800"><span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>15-30m</span>
                <span class="text-slate-300">&bull;</span>
                <span class="inline-flex items-center gap-1 text-rose-800 font-bold"><span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>&gt;30m (Prioritas)</span>
            </div>

            <input type="text" 
                   wire:model.live.debounce.250ms="search" 
                   placeholder="Cari invoice / pelanggan..." 
                   class="bg-white border border-slate-200 rounded-xl px-4 py-2 text-xs text-slate-900 focus:outline-none focus:border-amber-700 shadow-2xs w-56">
        </div>
    </div>

    <!-- Status Tabs / Counters -->
    <div class="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-none">
        <button type="button" 
                wire:click="setTab('all_active')" 
                class="px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition-all flex items-center gap-2 {{ $activeTab === 'all_active' ? 'bg-amber-700 text-white shadow-sm' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200' }}">
            <span>Semua Aktif</span>
            <span class="px-1.5 py-0.5 rounded-md text-[10px] {{ $activeTab === 'all_active' ? 'bg-amber-800 text-amber-100' : 'bg-slate-100 text-slate-700' }}">
                {{ $pendingCount + $cookingCount }}
            </span>
        </button>

        <button type="button" 
                wire:click="setTab('pending')" 
                class="px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition-all flex items-center gap-2 {{ $activeTab === 'pending' ? 'bg-amber-500 text-white shadow-sm' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200' }}">
            <span>Menunggu</span>
            <span class="px-1.5 py-0.5 rounded-md text-[10px] {{ $activeTab === 'pending' ? 'bg-amber-600 text-white' : 'bg-amber-100 text-amber-800' }}">
                {{ $pendingCount }}
            </span>
        </button>

        <button type="button" 
                wire:click="setTab('cooking')" 
                class="px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition-all flex items-center gap-2 {{ $activeTab === 'cooking' ? 'bg-blue-600 text-white shadow-sm' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200' }}">
            <span>Sedang Diracik / Dimasak</span>
            <span class="px-1.5 py-0.5 rounded-md text-[10px] {{ $activeTab === 'cooking' ? 'bg-blue-700 text-white' : 'bg-blue-100 text-blue-800' }}">
                {{ $cookingCount }}
            </span>
        </button>

        <button type="button" 
                wire:click="setTab('ready')" 
                class="px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition-all flex items-center gap-2 {{ $activeTab === 'ready' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200' }}">
            <span>Siap Disajikan</span>
            <span class="px-1.5 py-0.5 rounded-md text-[10px] {{ $activeTab === 'ready' ? 'bg-emerald-700 text-white' : 'bg-emerald-100 text-emerald-800' }}">
                {{ $readyCount }}
            </span>
        </button>

        <button type="button" 
                wire:click="setTab('completed')" 
                class="px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition-all flex items-center gap-2 {{ $activeTab === 'completed' ? 'bg-slate-800 text-white shadow-sm' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200' }}">
            <span>Selesai Hari Ini</span>
            <span class="px-1.5 py-0.5 rounded-md text-[10px] {{ $activeTab === 'completed' ? 'bg-slate-900 text-slate-200' : 'bg-slate-100 text-slate-700' }}">
                {{ $completedCount }}
            </span>
        </button>
    </div>

    <!-- Orders Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @forelse($orders as $order)
            <div wire:key="order-card-{{ $order->id }}" class="bg-white border rounded-2xl shadow-xs overflow-hidden flex flex-col justify-between transition-all {{ $order->order_status === 'cooking' ? 'border-blue-300 ring-1 ring-blue-100' : ($order->order_status === 'ready' ? 'border-emerald-300 ring-1 ring-emerald-100' : 'border-slate-200/90') }}">
                
                <!-- Card Header -->
                <div class="p-4 border-b border-slate-100 {{ $order->order_status === 'cooking' ? 'bg-blue-50/40' : ($order->order_status === 'ready' ? 'bg-emerald-50/40' : 'bg-slate-50/60') }}">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <span class="font-mono font-extrabold text-sm text-slate-900 block leading-tight whitespace-nowrap">{{ $order->invoice_number }}</span>
                            <p class="text-xs font-semibold text-slate-600 mt-1">Pelanggan: <strong class="text-slate-900 font-bold">{{ $order->customer_name }}</strong></p>
                        </div>

                        <!-- Waktu Pemesanan & Indikator Tunggu -->
                        @php
                            $diffMinutes = (int) $order->created_at->diffInMinutes(now());
                            if ($diffMinutes < 1) {
                                $timeText = '< 1 mnt';
                            } elseif ($diffMinutes < 60) {
                                $timeText = $diffMinutes . ' mnt';
                            } else {
                                $hours = floor($diffMinutes / 60);
                                $remainingMins = $diffMinutes % 60;
                                $timeText = $remainingMins > 0 ? "{$hours}j {$remainingMins}m" : "{$hours} jam";
                            }

                            if ($order->order_status === 'completed') {
                                $waitBadgeClass = 'bg-slate-100 text-slate-600 border-slate-200';
                                $waitDotClass = 'bg-slate-400';
                                $isOverdue = false;
                            } elseif ($diffMinutes < 15) {
                                $waitBadgeClass = 'bg-emerald-50 text-emerald-800 border-emerald-200';
                                $waitDotClass = 'bg-emerald-500';
                                $isOverdue = false;
                            } elseif ($diffMinutes < 30) {
                                $waitBadgeClass = 'bg-amber-50 text-amber-900 border-amber-200';
                                $waitDotClass = 'bg-amber-500';
                                $isOverdue = false;
                            } else {
                                $waitBadgeClass = 'bg-rose-50 text-rose-800 border-rose-300';
                                $waitDotClass = 'bg-rose-500';
                                $isOverdue = true;
                            }
                        @endphp
                        <div class="text-right shrink-0">
                            <span class="text-[11px] font-mono text-slate-500 block">{{ $order->created_at->format('H:i') }} WIB</span>
                            <span class="inline-flex items-center gap-1.5 text-[10px] font-bold px-2 py-0.5 rounded-md border mt-0.5 {{ $waitBadgeClass }}" title="Waktu tunggu pesanan: {{ $timeText }}">
                                <span class="relative flex h-1.5 w-1.5">
                                    @if($isOverdue && in_array($order->order_status, ['pending', 'cooking']))
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                                    @endif
                                    <span class="relative inline-flex rounded-full h-1.5 w-1.5 {{ $waitDotClass }}"></span>
                                </span>
                                <span>Tunggu: {{ $timeText }}</span>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Items List -->
                <div class="p-4 flex-1 space-y-3 divide-y divide-slate-100 text-xs">
                    @foreach($order->items as $item)
                        <div class="pt-2 first:pt-0 flex items-start justify-between gap-3">
                            <div class="flex items-start gap-2.5">
                                <span class="px-2 py-1 rounded-lg bg-slate-100 font-extrabold font-mono text-slate-800 text-xs shrink-0">
                                    {{ $item->quantity }}x
                                </span>
                                <div>
                                    <p class="font-bold text-slate-900 text-xs leading-snug">{{ $item->product_name }}</p>
                                    @if($item->notes)
                                        <div class="mt-1 inline-flex items-center gap-1 text-[11px] font-medium text-amber-900 bg-amber-50 px-2 py-0.5 rounded border border-amber-200">
                                            <svg class="w-3 h-3 text-amber-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg>
                                            <span>{{ $item->notes }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Card Footer & Action Buttons -->
                <div class="p-3.5 border-t border-slate-100 bg-slate-50/60 flex items-center justify-between gap-2">
                    <div class="shrink-0">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-0.5">Status</span>
                        @if($order->order_status === 'pending')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold whitespace-nowrap bg-amber-100 text-amber-800 border border-amber-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 shrink-0"></span>
                                <span>Menunggu</span>
                            </span>
                        @elseif($order->order_status === 'cooking')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold whitespace-nowrap bg-blue-100 text-blue-800 border border-blue-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse shrink-0"></span>
                                <span>Sedang Diracik</span>
                            </span>
                        @elseif($order->order_status === 'ready')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold whitespace-nowrap bg-emerald-100 text-emerald-800 border border-emerald-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shrink-0"></span>
                                <span>Siap Saji</span>
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold whitespace-nowrap bg-slate-100 text-slate-600 border border-slate-200">
                                <span>Selesai</span>
                            </span>
                        @endif
                    </div>

                    <div class="flex items-center gap-2 justify-end flex-wrap">
                        <span class="px-2.5 py-1 rounded-xl text-xs font-bold uppercase tracking-wider whitespace-nowrap {{ $order->order_type === 'dine_in' ? 'bg-amber-100 text-amber-900 border border-amber-200/90 shadow-2xs' : 'bg-slate-200 text-slate-700 border border-slate-300/80' }}">
                            {{ $order->order_type === 'dine_in' ? ($order->table->table_number ?? 'Dine In') : 'Takeaway' }}
                        </span>

                        @if($order->order_status === 'pending')
                            <button type="button" 
                                    wire:click="updateOrderStatus({{ $order->id }}, 'cooking')" 
                                    class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl transition-all shadow-xs flex items-center gap-1.5 cursor-pointer whitespace-nowrap">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>Mulai Proses</span>
                            </button>
                        @elseif($order->order_status === 'cooking')
                            <button type="button" 
                                    wire:click="updateOrderStatus({{ $order->id }}, 'ready')" 
                                    class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl transition-all shadow-xs flex items-center gap-1.5 cursor-pointer whitespace-nowrap">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>Siap Saji</span>
                            </button>
                        @elseif($order->order_status === 'ready')
                            <button type="button" 
                                    wire:click="updateOrderStatus({{ $order->id }}, 'completed')" 
                                    class="px-3 py-1.5 bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs rounded-xl transition-all shadow-xs flex items-center gap-1.5 cursor-pointer whitespace-nowrap">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>Selesaikan</span>
                            </button>
                        @endif
                    </div>
                </div>

            </div>
        @empty
            <div class="col-span-full py-16 text-center bg-white rounded-2xl border border-dashed border-slate-200">
                <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 mx-auto flex items-center justify-center mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <h4 class="font-bold text-slate-700 text-sm">Tidak ada antrian pesanan</h4>
                <p class="text-xs text-slate-400 mt-0.5">Pesanan baru yang dibuat di kasir akan langsung muncul di sini secara otomatis</p>
            </div>
        @endforelse
    </div>

</div>
