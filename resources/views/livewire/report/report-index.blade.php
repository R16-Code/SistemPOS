<div class="p-6 space-y-6 overflow-y-auto h-full">
    <div>
        <h2 class="text-2xl font-bold text-white tracking-tight">Dashboard & Laporan Penjualan</h2>
        <p class="text-xs text-slate-400 mt-1">Ringkasan statistik omset dan performa penjualan cafe</p>
    </div>

    <!-- Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 space-y-2">
            <span class="text-xs text-slate-400 font-medium">Omset Hari Ini</span>
            <h3 class="text-2xl font-extrabold text-amber-400">Rp {{ number_format($todayRevenue, 0, ',', '.') }}</h3>
            <p class="text-[11px] text-slate-500">{{ $todayOrders }} Transaksi Berhasil</p>
        </div>

        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 space-y-2">
            <span class="text-xs text-slate-400 font-medium">Total Omset Keseluruhan</span>
            <h3 class="text-2xl font-extrabold text-white">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
            <p class="text-[11px] text-slate-500">{{ $totalOrdersCount }} Total Transaksi</p>
        </div>

        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 space-y-2">
            <span class="text-xs text-slate-400 font-medium">Transaksi Hari Ini</span>
            <h3 class="text-2xl font-extrabold text-emerald-400">{{ $todayOrders }}</h3>
            <p class="text-[11px] text-slate-500">Order Masuk Hari Ini</p>
        </div>

        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 space-y-2">
            <span class="text-xs text-slate-400 font-medium">Status Sistem</span>
            <h3 class="text-2xl font-extrabold text-amber-400">ONLINE</h3>
            <p class="text-[11px] text-slate-500">BrewPOS Live Analytics</p>
        </div>
    </div>

    <!-- Top Products Table -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 space-y-4">
        <h3 class="text-lg font-bold text-white">🔥 Produk Terlaris (Top Selling)</h3>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-950 text-slate-400 border-b border-slate-800 font-semibold uppercase tracking-wider text-[11px]">
                    <tr>
                        <th class="px-4 py-3">Nama Produk</th>
                        <th class="px-4 py-3">Jumlah Terjual</th>
                        <th class="px-4 py-3">Total Sales</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($topProducts as $item)
                        <tr>
                            <td class="px-4 py-3 font-semibold text-white">{{ $item->product_name }}</td>
                            <td class="px-4 py-3 font-mono text-amber-400 font-bold">{{ $item->total_qty }} Qty</td>
                            <td class="px-4 py-3 font-bold text-slate-200">Rp {{ number_format($item->total_sales, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-4 py-8 text-center text-slate-500">Belum ada data penjualan tersedia</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
