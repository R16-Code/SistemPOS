<div class="p-6 space-y-6 overflow-y-auto h-full bg-slate-50">
    <div>
        <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Dashboard & Laporan Penjualan</h2>
        <p class="text-xs text-slate-500 mt-1">Ringkasan statistik omset dan performa penjualan cafe</p>
    </div>

    <!-- Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white border border-slate-200/80 rounded-2xl p-5 space-y-2 shadow-2xs">
            <span class="text-xs text-slate-500 font-bold">Omset Hari Ini</span>
            <h3 class="text-2xl font-black text-amber-800">Rp {{ number_format($todayRevenue, 0, ',', '.') }}</h3>
            <p class="text-[11px] text-slate-400 font-medium">{{ $todayOrders }} Transaksi Berhasil</p>
        </div>

        <div class="bg-white border border-slate-200/80 rounded-2xl p-5 space-y-2 shadow-2xs">
            <span class="text-xs text-slate-500 font-bold">Total Omset Keseluruhan</span>
            <h3 class="text-2xl font-black text-slate-900">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
            <p class="text-[11px] text-slate-400 font-medium">{{ $totalOrdersCount }} Total Transaksi</p>
        </div>

        <div class="bg-white border border-slate-200/80 rounded-2xl p-5 space-y-2 shadow-2xs">
            <span class="text-xs text-slate-500 font-bold">Transaksi Hari Ini</span>
            <h3 class="text-2xl font-black text-emerald-700">{{ $todayOrders }}</h3>
            <p class="text-[11px] text-slate-400 font-medium">Order Masuk Hari Ini</p>
        </div>

        <div class="bg-white border border-slate-200/80 rounded-2xl p-5 space-y-2 shadow-2xs">
            <span class="text-xs text-slate-500 font-bold">Status Sistem</span>
            <h3 class="text-2xl font-black text-amber-800">ONLINE</h3>
            <p class="text-[11px] text-slate-400 font-medium">BrewPOS Live Analytics</p>
        </div>
    </div>

    <!-- Top Products Table -->
    <div class="bg-white border border-slate-200/80 rounded-2xl p-6 space-y-4 shadow-sm">
        <h3 class="text-lg font-bold text-slate-900">🔥 Produk Terlaris (Top Selling)</h3>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-100/70 text-slate-600 border-b border-slate-200 font-bold uppercase tracking-wider text-[11px]">
                    <tr>
                        <th class="px-4 py-3">Nama Produk</th>
                        <th class="px-4 py-3">Jumlah Terjual</th>
                        <th class="px-4 py-3">Total Sales</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($topProducts as $item)
                        <tr>
                            <td class="px-4 py-3 font-bold text-slate-900">{{ $item->product_name }}</td>
                            <td class="px-4 py-3 font-mono text-amber-800 font-extrabold">{{ $item->total_qty }} Qty</td>
                            <td class="px-4 py-3 font-extrabold text-slate-900">Rp {{ number_format($item->total_sales, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-4 py-8 text-center text-slate-400">Belum ada data penjualan tersedia</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
