<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penjualan - BrewPOS ({{ $periodLabel }})</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: #ffffff !important;
                color: #0f172a !important;
                padding: 0 !important;
            }
            .page-sheet {
                box-shadow: none !important;
                border: none !important;
                max-width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            thead {
                display: table-header-group;
            }
            tr {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body class="bg-slate-100 font-sans text-slate-800 antialiased min-h-screen">

    <!-- Top Action Toolbar (Hidden during Print) -->
    <div class="no-print sticky top-0 z-50 bg-white/95 backdrop-blur-md border-b border-slate-200 px-6 py-3 shadow-xs">
        <div class="max-w-5xl mx-auto flex items-center justify-between">
            <div class="flex items-center gap-2">
                <a href="{{ route('reports') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    <span>Kembali ke Laporan</span>
                </a>
                <span class="text-xs text-slate-400">|</span>
                <span class="text-xs text-slate-600 font-medium">Format Cetak Resmi & Ekspor PDF</span>
            </div>

            <button onclick="window.print()" class="inline-flex items-center gap-2 px-5 py-2 rounded-xl text-xs font-bold text-white bg-amber-800 hover:bg-amber-900 shadow-sm shadow-amber-800/20 transition-all cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Cetak / Simpan PDF</span>
            </button>
        </div>
    </div>

    <!-- Main Printable Sheet -->
    <div class="page-sheet max-w-5xl mx-auto my-8 p-10 bg-white shadow-xl rounded-2xl border border-slate-200 space-y-8">
        
        @php
            $storeName = \App\Models\Setting::get('store_name', 'BrewPOS Coffee');
            $storeTagline = \App\Models\Setting::get('store_tagline', 'Specialty Coffee & Kitchen POS System');
            $storeAddress = \App\Models\Setting::get('store_address', 'Jl. Mawar No. 123, Jakarta Selatan, DKI Jakarta 12410');
            $storePhone = \App\Models\Setting::get('store_phone', '0812-3456-7890');
            $storeEmail = \App\Models\Setting::get('store_email', 'management@brewpos.com');
            $storeLogo = \App\Models\Setting::get('store_logo');
        @endphp
        <!-- Header & Kop Dokumen -->
        <div class="border-b-2 border-slate-900 pb-6 flex items-start justify-between gap-6 flex-wrap">
            <div>
                <div class="flex items-center gap-2.5">
                    @if($storeLogo && \Illuminate\Support\Facades\Storage::disk('public')->exists($storeLogo))
                        <div class="w-10 h-10 rounded-xl bg-white border border-slate-200 p-0.5 flex items-center justify-center shrink-0 overflow-hidden">
                            <img src="{{ asset('storage/' . $storeLogo) }}" alt="{{ $storeName }}" class="w-full h-full object-contain">
                        </div>
                    @else
                        <div class="w-9 h-9 rounded-xl bg-amber-800 text-white flex items-center justify-center font-black text-lg">
                            {{ strtoupper(substr($storeName, 0, 1)) }}
                        </div>
                    @endif
                    <div>
                        <h1 class="text-xl font-black text-slate-950 tracking-tight">{{ strtoupper($storeName) }}</h1>
                        <p class="text-[11px] text-slate-500 font-medium">{{ $storeTagline }}</p>
                    </div>
                </div>
                <div class="mt-3 text-xs text-slate-600 leading-relaxed">
                    <p>{{ $storeAddress }}</p>
                    <p>Telp: {{ $storePhone }} @if($storeEmail) &bull; Email: {{ $storeEmail }} @endif</p>
                </div>
            </div>

            <div class="text-right space-y-1">
                <span class="inline-block px-3 py-1 rounded-md bg-amber-50 text-amber-900 border border-amber-200 text-[11px] font-bold uppercase tracking-wider">
                    Laporan Resmi Penjualan
                </span>
                <p class="text-xs text-slate-600 mt-2"><strong>Periode:</strong> {{ $periodLabel }}</p>
                <p class="text-[11px] text-slate-500"><strong>Waktu Cetak:</strong> {{ now()->format('d F Y, H:i') }} WIB</p>
                <p class="text-[11px] text-slate-500"><strong>Dicetak Oleh:</strong> {{ auth()->user()->name ?? 'Administrator' }}</p>
            </div>
        </div>

        <!-- KPI Summary Cards -->
        <div class="grid grid-cols-3 gap-4">
            <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/60 space-y-1">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Total Omset Pendapatan</span>
                <p class="text-xl font-black text-amber-900">Rp {{ number_format($periodRevenue, 0, ',', '.') }}</p>
                <p class="text-[10px] text-slate-500">Akumulasi seluruh transaksi lunas</p>
            </div>

            <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/60 space-y-1">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Volume Transaksi</span>
                <p class="text-xl font-black text-slate-900">{{ $periodOrders }} Transaksi</p>
                <p class="text-[10px] text-slate-500">Total struk transaksi yang tercatat</p>
            </div>

            <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/60 space-y-1">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Rata-rata Order (AOV)</span>
                <p class="text-xl font-black text-slate-900">Rp {{ number_format($averageOrder, 0, ',', '.') }}</p>
                <p class="text-[10px] text-slate-500">Rata-rata belanja per pelanggan</p>
            </div>
        </div>

        <!-- Section 1: Ringkasan Produk Terlaris -->
        <div class="space-y-3">
            <h2 class="text-xs font-black uppercase tracking-wider text-slate-900 flex items-center gap-1.5">
                <span>Rekapitulasi Penjualan Produk Teratas</span>
            </h2>
            <div class="border border-slate-200 rounded-xl overflow-hidden">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-100/90 text-slate-700 font-bold uppercase tracking-wider text-[10px] border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-2.5 w-12 text-center">No</th>
                            <th class="px-4 py-2.5">Nama Menu</th>
                            <th class="px-4 py-2.5 text-center w-28">Kuantitas Terjual</th>
                            <th class="px-4 py-2.5 text-right w-40">Total Penjualan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($topProducts->take(8) as $index => $item)
                            <tr class="even:bg-slate-50/50">
                                <td class="px-4 py-2.5 text-center text-slate-400 font-medium">{{ $index + 1 }}</td>
                                <td class="px-4 py-2.5 font-bold text-slate-900">{{ $item->product_name }}</td>
                                <td class="px-4 py-2.5 text-center font-bold text-slate-700">{{ $item->total_qty }} porsi</td>
                                <td class="px-4 py-2.5 text-right font-extrabold text-slate-900">Rp {{ number_format($item->total_sales, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-6 text-center text-slate-400">Tidak ada data produk pada periode ini</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Section 2: Tabel Detail Transaksi Lengkap -->
        <div class="space-y-3">
            <h2 class="text-xs font-black uppercase tracking-wider text-slate-900 flex items-center justify-between">
                <span>Rincian Transaksi Penjualan</span>
                <span class="text-[10px] font-normal text-slate-500 lowercase">{{ count($orders) }} catatan transaksi</span>
            </h2>
            <div class="border border-slate-200 rounded-xl overflow-hidden">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-100/90 text-slate-700 font-bold uppercase tracking-wider text-[10px] border-b border-slate-200">
                        <tr>
                            <th class="px-3 py-2.5 w-10 text-center">No</th>
                            <th class="px-3 py-2.5">Invoice</th>
                            <th class="px-3 py-2.5">Waktu</th>
                            <th class="px-3 py-2.5">Pelanggan</th>
                            <th class="px-3 py-2.5">Tipe & Meja</th>
                            <th class="px-3 py-2.5">Metode Bayar</th>
                            <th class="px-3 py-2.5 text-center">Status Dapur</th>
                            <th class="px-3 py-2.5 text-right">Subtotal</th>
                            <th class="px-3 py-2.5 text-right">Total Akhir</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($orders as $index => $order)
                            <tr class="even:bg-slate-50/50">
                                <td class="px-3 py-2.5 text-center text-slate-400 font-medium">{{ $index + 1 }}</td>
                                <td class="px-3 py-2.5 font-mono font-bold text-slate-900">{{ $order->invoice_number }}</td>
                                <td class="px-3 py-2.5 text-slate-600 whitespace-nowrap">{{ $order->created_at->format('d/m/y H:i') }}</td>
                                <td class="px-3 py-2.5 font-semibold text-slate-800">{{ $order->customer_name }}</td>
                                <td class="px-3 py-2.5 text-slate-600 capitalize">
                                    {{ $order->order_type }}
                                    @if($order->table)
                                        ({{ $order->table->table_number }})
                                    @endif
                                </td>
                                <td class="px-3 py-2.5 uppercase font-medium text-slate-700">{{ $order->payment_method }}</td>
                                <td class="px-3 py-2.5 text-center">
                                    <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded {{ $order->order_status === 'completed' ? 'text-slate-700 bg-slate-100' : 'text-amber-800 bg-amber-50' }}">
                                        {{ $order->order_status }}
                                    </span>
                                </td>
                                <td class="px-3 py-2.5 text-right text-slate-600 font-medium">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</td>
                                <td class="px-3 py-2.5 text-right font-bold text-slate-900">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-4 py-8 text-center text-slate-400">Tidak ada transaksi yang ditemukan untuk periode ini</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-slate-100/90 font-bold border-t-2 border-slate-300 text-xs">
                        <tr>
                            <td colspan="8" class="px-3 py-3 text-right uppercase tracking-wider text-slate-700">Grand Total Penjualan:</td>
                            <td class="px-3 py-3 text-right font-black text-amber-900 text-sm">Rp {{ number_format($periodRevenue, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Section 3: Lembar Pengesahan & Tanda Tangan -->
        <div class="pt-8 border-t border-slate-200 grid grid-cols-2 gap-8 text-center text-xs">
            <div class="space-y-16">
                <div>
                    <p class="text-slate-500">Dibuat dan Diperiksa Oleh,</p>
                    <p class="font-bold text-slate-800 mt-1">Petugas Kasir / Admin</p>
                </div>
                <div>
                    <p class="font-bold text-slate-900 uppercase">({{ auth()->user()->name ?? 'Administrator' }})</p>
                    <p class="text-[11px] text-slate-400">Staff Operasional</p>
                </div>
            </div>

            <div class="space-y-16">
                <div>
                    <p class="text-slate-500">Mengetahui dan Menyetujui,</p>
                    <p class="font-bold text-slate-800 mt-1">Pemilik / Owner Kafe</p>
                </div>
                <div>
                    <p class="font-bold text-slate-900 uppercase">( __________________________ )</p>
                    <p class="text-[11px] text-slate-400">Owner BrewPOS</p>
                </div>
            </div>
        </div>

    </div>

</body>
</html>
