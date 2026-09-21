<div class="p-6 space-y-6 overflow-y-auto h-full bg-slate-50">
    <div class="flex items-center justify-between gap-4 flex-wrap">
        <div>
            <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Dashboard & Laporan Penjualan</h2>
            <p class="text-xs text-slate-500 mt-1">Ringkasan statistik omset dan performa penjualan cafe berdasarkan rentang waktu</p>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            <!-- Date Filter Quick Buttons -->
            <div class="bg-white border border-slate-200 p-1 rounded-xl flex items-center gap-1 text-xs">
                <button type="button" wire:click="setDateRange('today')" class="px-3 py-1.5 rounded-lg font-bold transition-all {{ $dateRange === 'today' ? 'bg-amber-700 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}">
                    Hari Ini
                </button>
                <button type="button" wire:click="setDateRange('this_week')" class="px-3 py-1.5 rounded-lg font-bold transition-all {{ $dateRange === 'this_week' ? 'bg-amber-700 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}">
                    7 Hari
                </button>
                <button type="button" wire:click="setDateRange('this_month')" class="px-3 py-1.5 rounded-lg font-bold transition-all {{ $dateRange === 'this_month' ? 'bg-amber-700 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}">
                    Bulan Ini
                </button>
                <button type="button" wire:click="setDateRange('all')" class="px-3 py-1.5 rounded-lg font-bold transition-all {{ $dateRange === 'all' ? 'bg-amber-700 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}">
                    Semua Waktu
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

    <!-- Stat Cards for Selected Period -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white border border-slate-200/80 rounded-2xl p-5 space-y-2 shadow-2xs">
            <span class="text-xs text-slate-500 font-bold">Omset Periode Terpilih</span>
            <h3 class="text-2xl font-black text-amber-800">Rp {{ number_format($periodRevenue, 0, ',', '.') }}</h3>
            <p class="text-[11px] text-slate-400 font-medium">{{ $periodOrders }} Transaksi Berhasil</p>
        </div>

        <div class="bg-white border border-slate-200/80 rounded-2xl p-5 space-y-2 shadow-2xs">
            <span class="text-xs text-slate-500 font-bold">Total Transaksi</span>
            <h3 class="text-2xl font-black text-emerald-700">{{ $periodOrders }}</h3>
            <p class="text-[11px] text-slate-400 font-medium">Dalam Rentang Tanggal Terpilih</p>
        </div>

        <div class="bg-white border border-slate-200/80 rounded-2xl p-5 space-y-2 shadow-2xs">
            <span class="text-xs text-slate-500 font-bold">Rata-rata Nilai Order (AOV)</span>
            <h3 class="text-2xl font-black text-slate-900">Rp {{ number_format($averageOrder, 0, ',', '.') }}</h3>
            <p class="text-[11px] text-slate-400 font-medium">Rata-rata Pengeluaran / Transaksi</p>
        </div>

        <div class="bg-white border border-slate-200/80 rounded-2xl p-5 space-y-2 shadow-2xs">
            <span class="text-xs text-slate-500 font-bold">Total Omset Sepanjang Waktu</span>
            <h3 class="text-2xl font-black text-slate-800">Rp {{ number_format($allTimeRevenue, 0, ',', '.') }}</h3>
            <p class="text-[11px] text-slate-400 font-medium">{{ $allTimeOrders }} Transaksi Keseluruhan</p>
        </div>
    </div>

    <!-- Top Products Table for Selected Period -->
    <div class="bg-white border border-slate-200/80 rounded-2xl p-6 space-y-4 shadow-sm">
        <div class="flex items-center justify-between">
            <h3 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                <span>🔥 Produk Terlaris Periode Ini (Top Selling)</span>
            </h3>
            <span class="text-xs text-slate-400 font-medium">5 Menu Teratas</span>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-100/70 text-slate-600 border-b border-slate-200 font-bold uppercase tracking-wider text-[11px]">
                    <tr>
                        <th class="px-5 py-3">Nama Produk</th>
                        <th class="px-5 py-3 text-center">Jumlah Terjual</th>
                        <th class="px-5 py-3 text-right">Total Pendapatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($topProducts as $item)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-5 py-3.5 font-bold text-slate-900">{{ $item->product_name }}</td>
                            <td class="px-5 py-3.5 font-mono text-amber-800 font-extrabold text-center">
                                <span class="bg-amber-50 px-2.5 py-1 rounded-md border border-amber-200 text-xs font-bold">
                                    {{ $item->total_qty }} Qty
                                </span>
                            </td>
                            <td class="px-5 py-3.5 font-extrabold text-slate-900 text-right">Rp {{ number_format($item->total_sales, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-5 py-8 text-center text-slate-400">Belum ada data penjualan pada rentang waktu ini</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
