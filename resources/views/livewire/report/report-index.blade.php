<div class="p-6 space-y-6 overflow-y-auto h-full bg-slate-50">
    <!-- Header & Action Controls -->
    <div class="flex items-center justify-between gap-4 flex-wrap">
        <div>
            <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Laporan & Analitik Penjualan</h2>
            <p class="text-xs text-slate-500 mt-1">Ringkasan performa finansial, tren penjualan, dan ekspor laporan kafe</p>
        </div>

        <div class="flex items-center gap-2.5 flex-wrap">
            <!-- Date Filter Quick Buttons -->
            <div class="bg-white border border-slate-200 p-1 rounded-xl flex items-center gap-1 text-xs shadow-2xs">
                <button type="button" wire:click="setDateRange('today')" class="px-3 py-1.5 rounded-lg font-bold transition-all {{ $dateRange === 'today' ? 'bg-amber-800 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}">
                    Hari Ini
                </button>
                <button type="button" wire:click="setDateRange('this_week')" class="px-3 py-1.5 rounded-lg font-bold transition-all {{ $dateRange === 'this_week' ? 'bg-amber-800 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}">
                    7 Hari
                </button>
                <button type="button" wire:click="setDateRange('this_month')" class="px-3 py-1.5 rounded-lg font-bold transition-all {{ $dateRange === 'this_month' ? 'bg-amber-800 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}">
                    Bulan Ini
                </button>
                <button type="button" wire:click="setDateRange('all')" class="px-3 py-1.5 rounded-lg font-bold transition-all {{ $dateRange === 'all' ? 'bg-amber-800 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}">
                    Semua Waktu
                </button>
            </div>

            <!-- Custom Date Range Inputs -->
            <div class="flex items-center gap-1.5 bg-white border border-slate-200 px-3 py-1.5 rounded-xl text-xs shadow-2xs">
                <input type="date" wire:model.live="startDate" class="text-slate-800 font-medium focus:outline-none bg-transparent">
                <span class="text-slate-400">s/d</span>
                <input type="date" wire:model.live="endDate" class="text-slate-800 font-medium focus:outline-none bg-transparent">
            </div>

            <!-- Export Actions Toolbar -->
            <div class="flex items-center gap-2">
                <!-- Export to Excel/CSV Button -->
                <button type="button" 
                        wire:click="exportCsv"
                        wire:loading.attr="disabled"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold text-emerald-800 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200/80 transition-all shadow-2xs cursor-pointer">
                    <svg wire:loading.remove wire:target="exportCsv" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <svg wire:loading wire:target="exportCsv" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                    <span>Ekspor Excel (.csv)</span>
                </button>

                <!-- Official Print / PDF View Button -->
                <a href="{{ route('reports.print', ['startDate' => $startDate, 'endDate' => $endDate]) }}" 
                   target="_blank"
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold text-amber-900 bg-amber-50 hover:bg-amber-100 border border-amber-200/80 transition-all shadow-2xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak PDF</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Stat Cards for Selected Period -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white border border-slate-200/80 rounded-2xl p-5 space-y-2 shadow-2xs">
            <span class="text-xs text-slate-500 font-bold uppercase tracking-wider text-[11px]">Omset Periode Terpilih</span>
            <h3 class="text-2xl font-black text-amber-900">Rp {{ number_format($periodRevenue, 0, ',', '.') }}</h3>
            <p class="text-[11px] text-slate-400 font-medium">{{ $periodOrders }} Transaksi Berhasil</p>
        </div>

        <div class="bg-white border border-slate-200/80 rounded-2xl p-5 space-y-2 shadow-2xs">
            <span class="text-xs text-slate-500 font-bold uppercase tracking-wider text-[11px]">Volume Transaksi</span>
            <h3 class="text-2xl font-black text-emerald-700">{{ $periodOrders }}</h3>
            <p class="text-[11px] text-slate-400 font-medium">Dalam Rentang Tanggal Terpilih</p>
        </div>

        <div class="bg-white border border-slate-200/80 rounded-2xl p-5 space-y-2 shadow-2xs">
            <span class="text-xs text-slate-500 font-bold uppercase tracking-wider text-[11px]">Rata-rata Order (AOV)</span>
            <h3 class="text-2xl font-black text-slate-900">Rp {{ number_format($averageOrder, 0, ',', '.') }}</h3>
            <p class="text-[11px] text-slate-400 font-medium">Rata-rata Pengeluaran / Transaksi</p>
        </div>

        <div class="bg-white border border-slate-200/80 rounded-2xl p-5 space-y-2 shadow-2xs">
            <span class="text-xs text-slate-500 font-bold uppercase tracking-wider text-[11px]">Total Omset Keseluruhan</span>
            <h3 class="text-2xl font-black text-slate-800">Rp {{ number_format($allTimeRevenue, 0, ',', '.') }}</h3>
            <p class="text-[11px] text-slate-400 font-medium">{{ $allTimeOrders }} Transaksi Sepanjang Waktu</p>
        </div>
    </div>

    <!-- Visual Charts Section (Chart.js via Alpine) -->
    <div x-data="{
            trendChart: null,
            paymentChart: null,
            init() {
                this.$nextTick(() => this.draw());
            },
            draw() {
                if (typeof Chart === 'undefined') return;
                if (this.trendChart) this.trendChart.destroy();
                if (this.paymentChart) this.paymentChart.destroy();

                const ctxTrend = document.getElementById('trendSalesChart')?.getContext('2d');
                if (ctxTrend) {
                    this.trendChart = new Chart(ctxTrend, {
                        type: 'line',
                        data: {
                            labels: @js($chartLabels),
                            datasets: [
                                {
                                    label: 'Omset Penjualan (Rp)',
                                    data: @js($chartSales),
                                    borderColor: '#b45309',
                                    backgroundColor: 'rgba(180, 83, 9, 0.08)',
                                    borderWidth: 2.5,
                                    fill: true,
                                    tension: 0.35,
                                    pointBackgroundColor: '#b45309',
                                    pointRadius: 4,
                                    pointHoverRadius: 6,
                                    yAxisID: 'y'
                                },
                                {
                                    label: 'Volume Transaksi',
                                    data: @js($chartOrders),
                                    borderColor: '#0284c7',
                                    backgroundColor: 'transparent',
                                    borderWidth: 2,
                                    borderDash: [4, 4],
                                    tension: 0.35,
                                    pointBackgroundColor: '#0284c7',
                                    pointRadius: 3,
                                    yAxisID: 'y1'
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            interaction: { mode: 'index', intersect: false },
                            plugins: {
                                legend: { 
                                    position: 'top', 
                                    labels: { 
                                        boxWidth: 12, 
                                        font: { family: 'Plus Jakarta Sans', size: 11, weight: '600' } 
                                    } 
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            if (context.datasetIndex === 0) {
                                                return ' Omset: Rp ' + new Intl.NumberFormat('id-ID').format(context.raw);
                                            }
                                            return ' Transaksi: ' + context.raw + ' pesanan';
                                        }
                                    }
                                }
                            },
                            scales: {
                                x: { 
                                    grid: { display: false }, 
                                    ticks: { font: { size: 10 } } 
                                },
                                y: {
                                    type: 'linear',
                                    display: true,
                                    position: 'left',
                                    grid: { color: '#f1f5f9' },
                                    ticks: {
                                        font: { size: 10 },
                                        callback: function(value) {
                                            if (value >= 1000000) return 'Rp ' + (value / 1000000).toFixed(1) + 'jt';
                                            if (value >= 1000) return 'Rp ' + (value / 1000).toFixed(0) + 'rb';
                                            return 'Rp ' + value;
                                        }
                                    }
                                },
                                y1: {
                                    type: 'linear',
                                    display: true,
                                    position: 'right',
                                    grid: { drawOnChartArea: false },
                                    ticks: { font: { size: 10 }, stepSize: 1 }
                                }
                            }
                        }
                    });
                }

                const ctxPayment = document.getElementById('paymentMethodChart')?.getContext('2d');
                if (ctxPayment) {
                    this.paymentChart = new Chart(ctxPayment, {
                        type: 'doughnut',
                        data: {
                            labels: @js($paymentLabels),
                            datasets: [{
                                data: @js($paymentCounts),
                                backgroundColor: ['#b45309', '#059669', '#0284c7', '#64748b'],
                                borderWidth: 2,
                                borderColor: '#ffffff',
                                hoverOffset: 4
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            cutout: '70%',
                            plugins: {
                                legend: { 
                                    position: 'bottom', 
                                    labels: { 
                                        boxWidth: 12, 
                                        font: { family: 'Plus Jakarta Sans', size: 11, weight: '500' } 
                                    } 
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            return ' ' + context.label + ': ' + context.raw + ' transaksi';
                                        }
                                    }
                                }
                            }
                        }
                    });
                }
            }
         }"
         wire:key="charts-{{ md5(json_encode($chartLabels) . json_encode($chartSales) . json_encode($paymentLabels)) }}"
         class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Chart 1: Daily/Hourly Trend -->
        <div class="lg:col-span-2 bg-white border border-slate-200/80 rounded-2xl p-5 shadow-2xs space-y-3">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div>
                    <h3 class="text-sm font-extrabold text-slate-900">Tren Penjualan & Fluktuasi Omset</h3>
                    <p class="text-[11px] text-slate-500">Pola pendapatan dan frekuensi transaksi harian dalam periode terpilih</p>
                </div>
                <div class="w-2 h-2 rounded-full bg-amber-800"></div>
            </div>
            <div class="relative h-64 w-full">
                <canvas id="trendSalesChart"></canvas>
            </div>
        </div>

        <!-- Chart 2: Payment Distribution -->
        <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-2xs space-y-3">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div>
                    <h3 class="text-sm font-extrabold text-slate-900">Komposisi Pembayaran</h3>
                    <p class="text-[11px] text-slate-500">Perbandingan tunai vs metode digital</p>
                </div>
                <div class="w-2 h-2 rounded-full bg-emerald-600"></div>
            </div>
            <div class="relative h-64 w-full flex items-center justify-center">
                <canvas id="paymentMethodChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Top Products Table for Selected Period -->
    <div class="bg-white border border-slate-200/80 rounded-2xl p-6 space-y-4 shadow-sm">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-amber-50 text-amber-900 flex items-center justify-center border border-amber-200/80">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                </div>
                <div>
                    <h3 class="text-sm font-extrabold text-slate-900">Produk Terlaris Periode Ini</h3>
                    <p class="text-[11px] text-slate-500">Daftar menu dengan frekuensi pemesanan tertinggi</p>
                </div>
            </div>
            <span class="text-xs text-slate-400 font-medium">5 Menu Teratas</span>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-100/70 text-slate-600 border-b border-slate-200 font-bold uppercase tracking-wider text-[11px]">
                    <tr>
                        <th class="px-5 py-3 w-12 text-center">No</th>
                        <th class="px-5 py-3">Nama Menu</th>
                        <th class="px-5 py-3 text-center w-36">Kuantitas Terjual</th>
                        <th class="px-5 py-3 text-right w-44">Total Pendapatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($topProducts as $idx => $item)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-5 py-3.5 text-center text-slate-400 font-semibold">{{ $idx + 1 }}</td>
                            <td class="px-5 py-3.5 font-bold text-slate-900">{{ $item->product_name }}</td>
                            <td class="px-5 py-3.5 font-mono text-amber-900 font-extrabold text-center">
                                <span class="bg-amber-50 px-2.5 py-1 rounded-md border border-amber-200 text-xs font-bold">
                                    {{ $item->total_qty }} Qty
                                </span>
                            </td>
                            <td class="px-5 py-3.5 font-extrabold text-slate-900 text-right">Rp {{ number_format($item->total_sales, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-8 text-center text-slate-400">Belum ada data transaksi pada rentang waktu ini</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Load Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</div>
