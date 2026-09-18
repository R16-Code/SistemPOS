<div class="p-6 space-y-6 overflow-y-auto h-full">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-white tracking-tight">Daftar Transaksi Penjualan</h2>
            <p class="text-xs text-slate-400 mt-1">Riwayat semua transaksi yang telah terjadi di cafe</p>
        </div>

        <div class="flex items-center gap-3">
            <input type="text" wire:model.live.debounce.250ms="search" placeholder="Cari No Invoice / Nama..." class="bg-slate-900 border border-slate-800 rounded-xl px-4 py-2 text-xs text-slate-200 focus:outline-none focus:border-amber-500">
            <select wire:model.live="paymentStatus" class="bg-slate-900 border border-slate-800 rounded-xl px-3 py-2 text-xs text-slate-200 focus:outline-none focus:border-amber-500">
                <option value="">Semua Status</option>
                <option value="paid">Lunas (Paid)</option>
                <option value="pending">Pending</option>
            </select>
        </div>
    </div>

    <div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden shadow-xl">
        <table class="w-full text-left text-xs text-slate-300">
            <thead class="bg-slate-950 text-slate-400 border-b border-slate-800 font-semibold uppercase tracking-wider text-[11px]">
                <tr>
                    <th class="px-6 py-4">Invoice</th>
                    <th class="px-6 py-4">Waktu</th>
                    <th class="px-6 py-4">Pelanggan</th>
                    <th class="px-6 py-4">Tipe / Meja</th>
                    <th class="px-6 py-4">Metode</th>
                    <th class="px-6 py-4">Total</th>
                    <th class="px-6 py-4 text-center">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/60">
                @forelse($orders as $order)
                    <tr class="hover:bg-slate-800/40 transition-colors">
                        <td class="px-6 py-4 font-bold text-white font-mono">{{ $order->invoice_number }}</td>
                        <td class="px-6 py-4 text-slate-400">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-6 py-4 font-medium text-slate-200">{{ $order->customer_name }}</td>
                        <td class="px-6 py-4">
                            <span class="capitalize font-medium text-slate-300">{{ $order->order_type }}</span>
                            @if($order->table)
                                <span class="text-amber-400 text-[11px]">({{ $order->table->table_number }})</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 uppercase font-semibold text-slate-400">{{ $order->payment_method }}</td>
                        <td class="px-6 py-4 font-bold text-amber-400">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                {{ $order->payment_status }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-slate-500">Belum ada transaksi ditemukan</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="p-4 border-t border-slate-800">
            {{ $orders->links() }}
        </div>
    </div>
</div>
