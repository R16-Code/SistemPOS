<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-900 text-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Coffee Shop POS' }}</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <!-- Tailwind & Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="h-full font-sans antialiased bg-slate-950 text-slate-100 flex overflow-hidden">

    <!-- Sidebar Navigation -->
    <aside class="w-64 bg-slate-900 border-r border-slate-800 flex flex-col justify-between shrink-0">
        <div>
            <!-- Brand / Logo -->
            <div class="h-16 flex items-center px-6 border-b border-slate-800 gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-amber-600 to-amber-400 flex items-center justify-center shadow-lg shadow-amber-500/20">
                    <i data-lucide="coffee" class="w-6 h-6 text-slate-950 font-bold"></i>
                </div>
                <div>
                    <h1 class="font-bold text-lg text-white leading-tight">BrewPOS</h1>
                    <p class="text-xs text-amber-400 font-medium">F&B Coffee System</p>
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="p-4 space-y-1.5">
                <a href="{{ route('pos') }}" 
                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('pos') ? 'bg-amber-500 text-slate-950 font-semibold shadow-lg shadow-amber-500/25' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-200' }}">
                    <i data-lucide="layout-grid" class="w-5 h-5"></i>
                    <span>Kasir POS</span>
                </a>

                <a href="{{ route('orders') }}" 
                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('orders*') ? 'bg-amber-500 text-slate-950 font-semibold shadow-lg shadow-amber-500/25' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-200' }}">
                    <i data-lucide="receipt" class="w-5 h-5"></i>
                    <span>Daftar Transaksi</span>
                </a>

                <a href="{{ route('products') }}" 
                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('products*') ? 'bg-amber-500 text-slate-950 font-semibold shadow-lg shadow-amber-500/25' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-200' }}">
                    <i data-lucide="cup-soda" class="w-5 h-5"></i>
                    <span>Menu Produk</span>
                </a>

                <a href="{{ route('categories') }}" 
                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('categories*') ? 'bg-amber-500 text-slate-950 font-semibold shadow-lg shadow-amber-500/25' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-200' }}">
                    <i data-lucide="folder-tree" class="w-5 h-5"></i>
                    <span>Kategori</span>
                </a>

                <a href="{{ route('tables') }}" 
                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('tables*') ? 'bg-amber-500 text-slate-950 font-semibold shadow-lg shadow-amber-500/25' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-200' }}">
                    <i data-lucide="armchair" class="w-5 h-5"></i>
                    <span>Manajemen Meja</span>
                </a>

                <a href="{{ route('reports') }}" 
                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('reports*') ? 'bg-amber-500 text-slate-950 font-semibold shadow-lg shadow-amber-500/25' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-200' }}">
                    <i data-lucide="bar-chart-3" class="w-5 h-5"></i>
                    <span>Laporan Penjualan</span>
                </a>
            </nav>
        </div>

        <!-- User / Shift Footer -->
        <div class="p-4 border-t border-slate-800">
            <div class="flex items-center justify-between p-3 rounded-xl bg-slate-800/60">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-slate-700 flex items-center justify-center font-bold text-amber-400 text-sm">
                        BK
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-white leading-tight">Budi Kasir</p>
                        <p class="text-xs text-slate-400">Shift Pagi</p>
                    </div>
                </div>
            </div>
        </div>
    </aside>

    <!-- Main Content Container -->
    <main class="flex-1 flex flex-col min-w-0 bg-slate-950 overflow-hidden">
        {{ $slot }}
    </main>

    @livewireScripts
    <script>
        lucide.createIcons();
        document.addEventListener('livewire:navigated', () => {
            lucide.createIcons();
        });
    </script>
</body>
</html>
