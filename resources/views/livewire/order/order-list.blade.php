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
    <div class="flex items-center justify-between gap-4 flex-wrap">
        <div class="relative w-72">
            <svg class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2 fill-none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" wire:model.live.debounce.250ms="search" placeholder="Cari No Invoice / Pelanggan..." class="w-full bg-white border border-slate-200 rounded-xl pl-9 pr-4 py-2 text-xs text-slate-900 focus:outline-none focus:border-amber-700 shadow-2xs">
        </div>

        <div class="flex items-center gap-3">
            <!-- Filter Status Pesanan Dapur -->
            <select wire:model.live="orderStatus" class="bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-900 font-medium focus:outline-none focus:border-amber-700 shadow-2xs">
                <option value="">Semua Status Dapur</option>
                <option value="pending">Menunggu (Pending)</option>
                <option value="cooking">Sedang Diracik (Cooking)</option>
                <option value="ready">Siap Saji (Ready)</option>
                <option value="completed">Selesai (Completed)</option>
            </select>

            <!-- Filter Status Pembayaran -->
            <select wire:model.live="paymentStatus" class="bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-900 font-medium focus:outline-none focus:border-amber-700 shadow-2xs">
                <option value="">Semua Pembayaran</option>
                <option value="paid">Lunas (Paid)</option>
                <option value="pending">Belum Lunas (Pending)</option>
            </select>
        </div>
    </div>

    <div class="bg-white border border-slate-200/80 rounded-2xl overflow-hidden shadow-sm">
        <table class="w-full text-left text-xs text-slate-700">
            <thead class="bg-slate-100/70 text-slate-600 border-b border-slate-200 font-bold uppercase tracking-wider text-[11px]">
                <tr>
                    <th class="px-5 py-4">Invoice</th>
                    <th class="px-5 py-4">Waktu</th>
                    <th class="px-5 py-4">Pelanggan & Meja</th>
                    <th class="px-5 py-4">Menu Dipesan</th>
                    <th class="px-5 py-4">Total</th>
                    <th class="px-5 py-4 text-center">Status Dapur</th>
                    <th class="px-5 py-4 text-center">Pembayaran</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($orders as $order)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="px-5 py-4 font-bold text-slate-900 font-mono">{{ $order->invoice_number }}</td>
                        <td class="px-5 py-4 text-slate-500 whitespace-nowrap">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-5 py-4">
                            <span class="font-bold text-slate-900 block">{{ $order->customer_name }}</span>
                            <span class="text-[11px] text-slate-500 capitalize">
                                {{ $order->order_type }}
                                @if($order->table)
                                    &bull; <strong class="text-amber-800">{{ $order->table->table_number }}</strong>
                                @endif
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            <div class="space-y-1 max-w-xs">
                                @foreach($order->items as $item)
                                    <div class="text-[11px] leading-tight">
                                        <span class="font-bold text-slate-800">{{ $item->quantity }}x</span> {{ $item->product_name }}
                                        @if($item->notes)
                                            <span class="text-amber-700 font-medium">({{ $item->notes }})</span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </td>
                        <td class="px-5 py-4 font-extrabold text-amber-800 whitespace-nowrap">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                        
                        <!-- Status Pesanan Dapur -->
                        <td class="px-5 py-4 text-center whitespace-nowrap">
                            @if(in_array(auth()->user()?->role, ['admin', 'kitchen']))
                                <select 
                                    wire:change="updateOrderStatus({{ $order->id }}, $event.target.value)"
                                    class="text-[11px] font-bold uppercase tracking-wider rounded-lg px-2.5 py-1 border cursor-pointer transition-colors focus:ring-2 focus:ring-amber-500/20 focus:outline-none {{ 
                                        $order->order_status === 'pending' ? 'bg-amber-50 text-amber-900 border-amber-300' : (
                                        $order->order_status === 'cooking' ? 'bg-blue-50 text-blue-900 border-blue-300' : (
                                        $order->order_status === 'ready' ? 'bg-emerald-50 text-emerald-900 border-emerald-300' : (
                                        $order->order_status === 'completed' ? 'bg-slate-100 text-slate-700 border-slate-300' : 'bg-rose-50 text-rose-800 border-rose-300'
                                    ))) }}"
                                >
                                    <option value="pending" {{ $order->order_status === 'pending' ? 'selected' : '' }}>⏳ Menunggu</option>
                                    <option value="cooking" {{ $order->order_status === 'cooking' ? 'selected' : '' }}>🍳 Sedang Diracik</option>
                                    <option value="ready" {{ $order->order_status === 'ready' ? 'selected' : '' }}>🔔 Siap Saji</option>
                                    <option value="completed" {{ $order->order_status === 'completed' ? 'selected' : '' }}>✅ Selesai</option>
                                    <option value="cancelled" {{ $order->order_status === 'cancelled' ? 'selected' : '' }}>❌ Dibatalkan</option>
                                </select>
                            @else
                                @if($order->order_status === 'pending')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-amber-100 text-amber-900 border border-amber-200">
                                        Menunggu
                                    </span>
                                @elseif($order->order_status === 'cooking')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-blue-100 text-blue-900 border border-blue-200">
                                        Diproses
                                    </span>
                                @elseif($order->order_status === 'ready')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-100 text-emerald-900 border border-emerald-200">
                                        Siap Saji
                                    </span>
                                @elseif($order->order_status === 'completed')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-slate-100 text-slate-700 border border-slate-200">
                                        Selesai
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-rose-100 text-rose-800 border border-rose-200">
                                        {{ $order->order_status }}
                                    </span>
                                @endif
                            @endif
                        </td>

                        <!-- Status Pembayaran -->
                        <td class="px-5 py-4 text-center whitespace-nowrap">
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $order->payment_status === 'paid' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                                {{ $order->payment_status === 'paid' ? 'LUNAS' : 'PENDING' }}
                            </span>
                            <span class="text-[10px] text-slate-400 uppercase font-mono block mt-0.5">{{ $order->payment_method }}</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-slate-400">Belum ada transaksi ditemukan untuk filter ini</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="p-4 border-t border-slate-100">
            {{ $orders->links() }}
        </div>
    </div>
</div>
