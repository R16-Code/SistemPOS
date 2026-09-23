<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak QR Meja {{ $table->table_number }} - BrewPOS</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; padding: 0 !important; }
            .print-card { box-shadow: none !important; border: 2px solid #cbd5e1 !important; margin: 0 auto !important; }
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen flex flex-col items-center justify-center p-6 text-slate-900">

    <!-- Action Toolbar (Hidden during print) -->
    <div class="no-print mb-6 flex items-center gap-3">
        <button onclick="window.print()" class="px-5 py-2.5 rounded-xl bg-amber-800 hover:bg-amber-900 text-white font-bold text-xs shadow-md shadow-amber-800/20 flex items-center gap-2 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
            </svg>
            <span>Cetak Kartu Meja</span>
        </button>

        <button onclick="window.close()" class="px-4 py-2.5 rounded-xl bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 font-bold text-xs transition-all">
            Tutup Jendela
        </button>
    </div>

    <!-- Printable Table Tent Card (Ratio ~ 10cm x 15cm) -->
    <div class="print-card w-80 bg-white rounded-3xl p-6 shadow-xl border border-slate-200 text-center flex flex-col items-center justify-between">
        <!-- Brand Header -->
        <div class="space-y-1">
            <div class="w-12 h-12 mx-auto rounded-2xl bg-amber-800 text-white flex items-center justify-center shadow-xs">
                <svg class="w-7 h-7 fill-current" viewBox="0 0 24 24">
                    <path d="M2,21V19H20V21H2M20,8V5H18V8H20M20,3A2,2 0 0,1 22,5V8A2,2 0 0,1 20,10H18V13A4,4 0 0,1 14,17H8A4,4 0 0,1 4,13V3H20M16,5H6V13A2,2 0 0,0 8,15H14A2,2 0 0,0 16,13V5Z"/>
                </svg>
            </div>
            <h1 class="text-xl font-black text-slate-900 tracking-tight">BrewPOS Cafe</h1>
            <p class="text-[11px] font-bold text-amber-800 uppercase tracking-widest">Self-Order Table</p>
        </div>

        <!-- Table Number Banner -->
        <div class="my-5 w-full py-2.5 px-4 rounded-2xl bg-slate-900 text-white shadow-xs">
            <span class="text-[10px] uppercase tracking-widest text-slate-400 font-bold block">Nomor Meja</span>
            <span class="text-2xl font-black text-amber-400 tracking-tight">{{ $table->table_number }}</span>
        </div>

        <!-- Scannable QR Code -->
        <div class="p-3 rounded-2xl bg-white border-2 border-slate-200 shadow-2xs">
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=260x260&data={{ urlencode($qrUrl) }}&margin=4"
                 alt="QR Code Meja {{ $table->table_number }}"
                 class="w-48 h-48 rounded-xl object-contain">
        </div>

        <!-- Instructions -->
        <div class="mt-5 space-y-2 text-left w-full bg-slate-50 p-3.5 rounded-2xl border border-slate-200/80">
            <p class="text-[11px] font-extrabold text-slate-800 uppercase tracking-wider text-center mb-1">
                Cara Memesan:
            </p>
            <div class="flex items-start gap-2 text-[11px] text-slate-600">
                <span class="w-4 h-4 rounded-full bg-amber-800 text-white flex items-center justify-center text-[10px] font-bold shrink-0">1</span>
                <span>Arahkan kamera HP ke kode QR di atas</span>
            </div>
            <div class="flex items-start gap-2 text-[11px] text-slate-600">
                <span class="w-4 h-4 rounded-full bg-amber-800 text-white flex items-center justify-center text-[10px] font-bold shrink-0">2</span>
                <span>Pilih menu dan kustomisasi pesanan Anda</span>
            </div>
            <div class="flex items-start gap-2 text-[11px] text-slate-600">
                <span class="w-4 h-4 rounded-full bg-amber-800 text-white flex items-center justify-center text-[10px] font-bold shrink-0">3</span>
                <span>Kirim pesanan dan lakukan pembayaran di kasir</span>
            </div>
        </div>

        <!-- URL Footer -->
        <div class="mt-4 pt-3 border-t border-slate-100 w-full text-center">
            <p class="text-[9px] text-slate-400 break-all select-all font-mono">{{ $qrUrl }}</p>
        </div>
    </div>

</body>
</html>
