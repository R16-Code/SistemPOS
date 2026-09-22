<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    @verbatim
    <!--[if gte mso 9]>
    <xml>
        <x:ExcelWorkbook>
            <x:ExcelWorksheets>
                <x:ExcelWorksheet>
                    <x:Name>Laporan Penjualan</x:Name>
                    <x:WorksheetOptions>
                        <x:DisplayGridlines/>
                        <x:FitToPage/>
                    </x:WorksheetOptions>
                </x:ExcelWorksheet>
            </x:ExcelWorksheets>
        </x:ExcelWorkbook>
    </xml>
    <![endif]-->
    @endverbatim
    <style>
        body {
            font-family: Calibri, 'Segoe UI', Arial, sans-serif;
            color: #1e293b;
            font-size: 11pt;
        }
        .banner-title {
            font-size: 16pt;
            font-weight: bold;
            color: #ffffff;
            background-color: #78350f;
            text-align: center;
            padding: 14px;
        }
        .banner-sub {
            font-size: 10pt;
            color: #fef3c7;
            background-color: #92400e;
            text-align: center;
            padding: 6px;
        }
        .section-header {
            background-color: #334155;
            color: #ffffff;
            font-weight: bold;
            font-size: 11pt;
            padding: 8px 10px;
        }
        .th-header {
            background-color: #92400e;
            color: #ffffff;
            font-weight: bold;
            font-size: 10pt;
            text-align: center;
            border: 1px solid #78350f;
            padding: 8px;
        }
        .meta-label {
            font-weight: bold;
            color: #475569;
            background-color: #f1f5f9;
            border: 1px solid #cbd5e1;
            padding: 6px 10px;
        }
        .meta-value {
            color: #0f172a;
            border: 1px solid #cbd5e1;
            padding: 6px 10px;
        }
        .meta-value-bold {
            color: #78350f;
            font-weight: bold;
            border: 1px solid #cbd5e1;
            padding: 6px 10px;
        }
        .cell-text {
            mso-number-format: "\@";
            border: 1px solid #cbd5e1;
            padding: 6px 8px;
        }
        .cell-center {
            text-align: center;
            border: 1px solid #cbd5e1;
            padding: 6px 8px;
        }
        .cell-number {
            mso-number-format: "\#\,\#\#0";
            text-align: right;
            border: 1px solid #cbd5e1;
            padding: 6px 8px;
        }
        .cell-percent {
            mso-number-format: "0\.0%";
            text-align: center;
            border: 1px solid #cbd5e1;
            padding: 6px 8px;
        }
        .badge-paid {
            background-color: #dcfce7;
            color: #166534;
            font-weight: bold;
            text-align: center;
            border: 1px solid #cbd5e1;
            padding: 6px 8px;
        }
        .badge-pending {
            background-color: #fee2e2;
            color: #991b1b;
            font-weight: bold;
            text-align: center;
            border: 1px solid #cbd5e1;
            padding: 6px 8px;
        }
        .row-even {
            background-color: #ffffff;
        }
        .row-odd {
            background-color: #f8fafc;
        }
        .total-row {
            background-color: #fef3c7;
            font-weight: bold;
            color: #78350f;
            border-top: 1px solid #92400e;
            border-bottom: 3px double #92400e;
            padding: 8px;
        }
        .total-number {
            mso-number-format: "\#\,\#\#0";
            text-align: right;
            background-color: #fef3c7;
            font-weight: bold;
            color: #78350f;
            border-top: 1px solid #92400e;
            border-bottom: 3px double #92400e;
            padding: 8px;
        }
    </style>
</head>
<body>
    <table>
        <!-- 1. Header Banner -->
        <tr>
            <td colspan="15" class="banner-title">
                BREWPOS COFFEE &bull; LAPORAN PENJUALAN
            </td>
        </tr>
        <tr>
            <td colspan="15" class="banner-sub">
                Jl. Mawar No. 123, Jakarta &bull; Telp: 0812-3456-7890 &bull; Sistem POS Kasir & Operasional Kafe
            </td>
        </tr>
        <tr><td colspan="15"></td></tr>

        <!-- 2. Metadata & KPI Ringkasan -->
        <tr>
            <td colspan="15" class="section-header">INFORMASI LAPORAN & RINGKASAN FINANSIAL</td>
        </tr>
        <tr>
            <td colspan="3" class="meta-label">Periode Laporan</td>
            <td colspan="4" class="meta-value-bold">{{ $periodLabel }}</td>
            <td colspan="3" class="meta-label">Total Omset Kotor (Subtotal)</td>
            <td colspan="5" class="meta-value cell-number">Rp {{ number_format($totalSubtotal, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td colspan="3" class="meta-label">Waktu Ekspor</td>
            <td colspan="4" class="meta-value">{{ now()->format('d/m/Y H:i:s') }} WIB</td>
            <td colspan="3" class="meta-label">Total Potongan Diskon</td>
            <td colspan="5" class="meta-value cell-number">Rp {{ number_format($totalDiscount, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td colspan="3" class="meta-label">Total Transaksi</td>
            <td colspan="4" class="meta-value">{{ $totalOrders }} Transaksi Berhasil</td>
            <td colspan="3" class="meta-label">Total Pajak PPN (11%)</td>
            <td colspan="5" class="meta-value cell-number">Rp {{ number_format($totalTax, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td colspan="3" class="meta-label">Dicetak Oleh</td>
            <td colspan="4" class="meta-value">{{ auth()->user()->name ?? 'Administrator' }}</td>
            <td colspan="3" class="total-row">TOTAL PENDAPATAN BERSIH</td>
            <td colspan="5" class="total-number">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</td>
        </tr>
        <tr><td colspan="15"></td></tr>

        <!-- 3. Rincian Transaksi Table -->
        <tr>
            <td colspan="15" class="section-header">DAFTAR TRANSAKSI PENJUALAN</td>
        </tr>
        <thead>
            <tr>
                <th class="th-header" style="width: 40px;">No</th>
                <th class="th-header" style="width: 140px;">No. Invoice</th>
                <th class="th-header" style="width: 90px;">Tanggal</th>
                <th class="th-header" style="width: 70px;">Waktu</th>
                <th class="th-header" style="width: 130px;">Pelanggan</th>
                <th class="th-header" style="width: 90px;">Tipe</th>
                <th class="th-header" style="width: 80px;">Meja</th>
                <th class="th-header" style="width: 110px;">Kasir</th>
                <th class="th-header" style="width: 90px;">Pembayaran</th>
                <th class="th-header" style="width: 90px;">Status Bayar</th>
                <th class="th-header" style="width: 100px;">Status Dapur</th>
                <th class="th-header" style="width: 100px;">Subtotal (Rp)</th>
                <th class="th-header" style="width: 90px;">Diskon (Rp)</th>
                <th class="th-header" style="width: 90px;">PPN 11% (Rp)</th>
                <th class="th-header" style="width: 110px;">Total Akhir (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $index => $order)
                @php
                    $rowClass = ($index % 2 === 0) ? 'row-even' : 'row-odd';
                @endphp
                <tr class="{{ $rowClass }}">
                    <td class="cell-center">{{ $index + 1 }}</td>
                    <td class="cell-text" style="font-weight: bold;">{{ $order->invoice_number }}</td>
                    <td class="cell-center">{{ $order->created_at->format('Y-m-d') }}</td>
                    <td class="cell-center">{{ $order->created_at->format('H:i') }}</td>
                    <td class="cell-text">{{ $order->customer_name }}</td>
                    <td class="cell-center">{{ strtoupper($order->order_type) }}</td>
                    <td class="cell-center">{{ $order->table ? $order->table->table_number : '-' }}</td>
                    <td class="cell-text">{{ $order->user ? $order->user->name : '-' }}</td>
                    <td class="cell-center">{{ strtoupper($order->payment_method) }}</td>
                    <td class="{{ $order->payment_status === 'paid' ? 'badge-paid' : 'badge-pending' }}">
                        {{ strtoupper($order->payment_status) }}
                    </td>
                    <td class="cell-center">
                        @if($order->order_status === 'pending')
                            Menunggu
                        @elseif($order->order_status === 'cooking')
                            Sedang Diracik
                        @elseif($order->order_status === 'ready')
                            Siap Saji
                        @elseif($order->order_status === 'completed')
                            Selesai
                        @else
                            {{ ucfirst($order->order_status) }}
                        @endif
                    </td>
                    <td class="cell-number">{{ $order->subtotal }}</td>
                    <td class="cell-number">{{ $order->discount_amount }}</td>
                    <td class="cell-number">{{ $order->tax_amount }}</td>
                    <td class="cell-number" style="font-weight: bold; color: #78350f;">{{ $order->total_amount }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="15" class="cell-center" style="padding: 16px; color: #94a3b8;">
                        Tidak ada transaksi ditemukan untuk periode ini.
                    </td>
                </tr>
            @endforelse

            <!-- Grand Total Row -->
            @if($orders->isNotEmpty())
                <tr>
                    <td colspan="11" class="total-row" style="text-align: right; letter-spacing: 0.5px;">
                        GRAND TOTAL KESELURUHAN (Rp):
                    </td>
                    <td class="total-number">{{ $totalSubtotal }}</td>
                    <td class="total-number">{{ $totalDiscount }}</td>
                    <td class="total-number">{{ $totalTax }}</td>
                    <td class="total-number" style="font-size: 11pt;">{{ $totalRevenue }}</td>
                </tr>
            @endif
        </tbody>
        <tr><td colspan="15"></td></tr>
        <tr><td colspan="15"></td></tr>

        <!-- 4. Rekapitulasi Produk Terlaris Table -->
        <tr>
            <td colspan="5" class="section-header">REKAPITULASI PENJUALAN PRODUK TERLARIS</td>
            <td colspan="10"></td>
        </tr>
        <thead>
            <tr>
                <th class="th-header" style="width: 40px;">Peringkat</th>
                <th class="th-header" style="width: 220px; text-align: left;">Nama Menu / Produk</th>
                <th class="th-header" style="width: 100px;">Kuantitas (pcs)</th>
                <th class="th-header" style="width: 130px; text-align: right;">Total Omset (Rp)</th>
                <th class="th-header" style="width: 100px;">Kontribusi (%)</th>
                <th colspan="10"></th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalProductQty = $topProducts->sum('total_qty');
                $totalProductSales = $topProducts->sum('total_sales');
            @endphp
            @forelse($topProducts as $pIndex => $tp)
                @php
                    $pRowClass = ($pIndex % 2 === 0) ? 'row-even' : 'row-odd';
                    $pct = ($totalProductSales > 0) ? ($tp->total_sales / $totalProductSales) : 0;
                @endphp
                <tr class="{{ $pRowClass }}">
                    <td class="cell-center" style="font-weight: bold;">{{ $pIndex + 1 }}</td>
                    <td class="cell-text" style="font-weight: bold;">{{ $tp->product_name }}</td>
                    <td class="cell-number" style="text-align: center;">{{ $tp->total_qty }}</td>
                    <td class="cell-number">Rp {{ number_format($tp->total_sales, 0, ',', '.') }}</td>
                    <td class="cell-percent">{{ number_format($pct * 100, 1) }}%</td>
                    <td colspan="10"></td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="cell-center" style="padding: 12px; color: #94a3b8;">
                        Belum ada data penjualan produk untuk periode ini.
                    </td>
                    <td colspan="10"></td>
                </tr>
            @endforelse

            @if($topProducts->isNotEmpty())
                <tr>
                    <td colspan="2" class="total-row" style="text-align: right;">TOTAL ITEM TERJUAL:</td>
                    <td class="total-row" style="text-align: center;">{{ $totalProductQty }} pcs</td>
                    <td class="total-number">Rp {{ number_format($totalProductSales, 0, ',', '.') }}</td>
                    <td class="total-row" style="text-align: center;">100.0%</td>
                    <td colspan="10"></td>
                </tr>
            @endif
        </tbody>
        <tr><td colspan="15"></td></tr>

        <!-- 5. Footer Notes -->
        <tr>
            <td colspan="15" style="font-size: 9pt; color: #64748b; font-style: italic; padding-top: 8px;">
                Dokumen ini digenerate secara otomatis oleh sistem BrewPOS Coffee POS pada {{ now()->translatedFormat('d F Y H:i:s') }}. Data sah dan tercatat pada database operasional kafe.
            </td>
        </tr>
    </table>
</body>
</html>
