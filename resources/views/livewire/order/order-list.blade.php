<div class="p-6 space-y-6 overflow-y-auto h-full bg-slate-50">
    <div class="flex items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Daftar Transaksi Penjualan</h2>
            <p class="text-xs text-slate-500 mt-1">Riwayat semua transaksi yang telah terjadi di cafe</p>
        </div>

        <div class="flex items-center gap-3">
            <input type="text" wire:model.live.debounce.250ms="search" placeholder="Cari No Invoice / Nama..." class="bg-white border border-slate-200 rounded-xl px-4 py-2 text-xs text-slate-900 focus:outline-none focus:border-amber-700 shadow-2xs">
            <select wire:model.live="paymentStatus" class="bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-900 font-medium focus:outline-none focus:border-amber-700 shadow-2xs">
                <option value="">Semua Status</option>
                <option value="paid">Lunas (Paid)</option>
                <option value="pending">Pending</option>
            </select>
        </div>
    </div>

    <div class="bg-white border border-slate-200/80 rounded-2xl overflow-hidden shadow-sm">
        <table class="w-full text-left text-xs text-slate-700">
            <thead class="bg-slate-100/70 text-slate-600 border-b border-slate-200 font-bold uppercase tracking-wider text-[11px]">
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
            <tbody class="divide-y divide-slate-100">
                @forelse($orders as $order)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="px-6 py-4 font-bold text-slate-900 font-mono">{{ $order->invoice_number }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-6 py-4 font-bold text-slate-900">{{ $order->customer_name }}</td>
                        <td class="px-6 py-4">
                            <span class="capitalize font-bold text-slate-700">{{ $order->order_type }}</span>
                            @if($order->table)
                                <span class="text-amber-800 font-bold text-[11px]">({{ $order->table->table_number }})</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 uppercase font-bold text-slate-500">{{ $order->payment_method }}</td>
                        <td class="px-6 py-4 font-extrabold text-amber-800">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200">
                                {{ $order->payment_status }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-slate-400">Belum ada transaksi ditemukan</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="p-4 border-t border-slate-100">
            {{ $orders->links() }}
        </div>
    </div>
</div>
