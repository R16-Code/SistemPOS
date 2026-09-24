<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-100 text-slate-800">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Coffee Shop POS' }}</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Tailwind & Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Chart.js for Reports & Analytics -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @livewireStyles
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body x-data="{ mobileSidebarOpen: false }" class="h-full font-sans antialiased bg-slate-100 text-slate-800 flex flex-col lg:flex-row overflow-hidden">

    <!-- Mobile & Tablet Backdrop Overlay -->
    <div x-show="mobileSidebarOpen" 
         x-transition:enter="transition-opacity ease-linear duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="mobileSidebarOpen = false" 
         class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-40 lg:hidden"
         x-cloak
         style="display: none;"></div>

    <!-- Mobile & Tablet Top Navigation Bar (Visible on < lg) -->
    <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-4 shrink-0 lg:hidden z-30 shadow-2xs">
        <div class="flex items-center gap-3">
            <button type="button" 
                    @click="mobileSidebarOpen = true"
                    class="min-h-[44px] min-w-[44px] flex items-center justify-center rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 active:scale-95 transition-all cursor-pointer"
                    aria-label="Buka Menu Navigasi">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-amber-700 flex items-center justify-center text-white shadow-xs">
                    <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                        <path d="M2,21V19H20V21H2M20,8V5H18V8H20M20,3A2,2 0 0,1 22,5V8A2,2 0 0,1 20,10H18V13A4,4 0 0,1 14,17H8A4,4 0 0,1 4,13V3H20M16,5H6V13A2,2 0 0,0 8,15H14A2,2 0 0,0 16,13V5Z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="font-extrabold text-base text-slate-900 leading-tight">BrewPOS</h1>
                    <p class="text-[10px] text-amber-700 font-semibold leading-none">Coffee Shop Kasir</p>
                </div>
            </div>
        </div>

        @php
            $authUser = auth()->user();
            $userInitials = $authUser ? strtoupper(substr($authUser->name, 0, 2)) : 'US';
        @endphp
        <div class="flex items-center gap-2">
            <span class="text-xs font-semibold text-slate-600 hidden sm:inline">{{ $authUser->name ?? 'User' }}</span>
            <div class="w-9 h-9 rounded-full bg-amber-100 text-amber-900 border border-amber-200 flex items-center justify-center font-bold text-xs shadow-2xs">
                {{ $userInitials }}
            </div>
        </div>
    </header>

    <!-- Sidebar Navigation (Off-canvas drawer on mobile & tablet portrait, static on desktop & tablet landscape lg:) -->
    <aside :class="mobileSidebarOpen ? 'is-open' : 'is-closed'"
           class="sidebar-drawer fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-slate-200 flex flex-col justify-between shrink-0 shadow-2xl lg:shadow-sm lg:static lg:transform-none">
        <div>
            <!-- Brand / Logo & Close Button -->
            <div class="h-16 flex items-center justify-between px-6 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-700 flex items-center justify-center shadow-sm text-white">
                        <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24">
                            <path d="M2,21V19H20V21H2M20,8V5H18V8H20M20,3A2,2 0 0,1 22,5V8A2,2 0 0,1 20,10H18V13A4,4 0 0,1 14,17H8A4,4 0 0,1 4,13V3H20M16,5H6V13A2,2 0 0,0 8,15H14A2,2 0 0,0 16,13V5Z"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="font-extrabold text-lg text-slate-900 leading-tight tracking-tight">BrewPOS</h1>
                        <p class="text-xs text-amber-700 font-semibold">Coffee Shop Kasir</p>
                    </div>
                </div>

                <!-- Close button for drawer (visible on < lg) -->
                <button type="button" 
                        @click="mobileSidebarOpen = false" 
                        class="min-h-[44px] min-w-[44px] flex items-center justify-center rounded-xl text-slate-400 hover:text-slate-700 active:scale-95 transition-all lg:hidden cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Navigation Links -->
            <nav class="p-4 space-y-1">
                @php
                    $role = auth()->user()->role ?? 'cashier';
                @endphp

                @if(in_array($role, ['admin', 'cashier']))
                    <a href="{{ route('pos') }}" 
                       class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('pos') ? 'bg-amber-700 text-white font-bold shadow-md shadow-amber-700/20' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                        <span>Kasir POS</span>
                    </a>
                @endif

                @if(in_array($role, ['admin', 'kitchen']))
                    <a href="{{ route('kitchen') }}" 
                       class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('kitchen*') ? 'bg-amber-700 text-white font-bold shadow-md shadow-amber-700/20' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        <span>Antrian Dapur</span>
                    </a>
                @endif

                @if(in_array($role, ['admin', 'cashier', 'kitchen']))
                    <a href="{{ route('orders') }}" 
                       class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('orders*') ? 'bg-amber-700 text-white font-bold shadow-md shadow-amber-700/20' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>Daftar Transaksi</span>
                    </a>
                @endif

                @if($role === 'admin')
                    <a href="{{ route('products') }}" 
                       class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('products*') ? 'bg-amber-700 text-white font-bold shadow-md shadow-amber-700/20' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        <span>Menu Produk</span>
                    </a>
                @endif

                @if(in_array($role, ['admin', 'cashier']))
                    <a href="{{ route('tables') }}" 
                       class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('tables*') ? 'bg-amber-700 text-white font-bold shadow-md shadow-amber-700/20' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        <span>Manajemen Meja</span>
                    </a>
                @endif

                @if($role === 'admin')
                    <a href="{{ route('reports') }}" 
                       class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('reports*') ? 'bg-amber-700 text-white font-bold shadow-md shadow-amber-700/20' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 012-2h2a2 2 0 012 2v6a2 2 0 01-2 2h-2a2 2 0 01-2-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        <span>Laporan Penjualan</span>
                    </a>

                    <a href="{{ route('users') }}" 
                       class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-150 {{ request()->routeIs('users*') ? 'bg-amber-700 text-white font-bold shadow-md shadow-amber-700/20' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        <span>Kelola Pengguna</span>
                    </a>
                @endif
            </nav>
        </div>

        <!-- User Shift Info & Logout -->
        <div class="p-4 border-t border-slate-100">
            @php
                $authUser = auth()->user();
                $userInitials = $authUser ? strtoupper(substr($authUser->name, 0, 2)) : 'US';
            @endphp
            <div class="flex items-center justify-between p-2.5 rounded-xl bg-slate-50 border border-slate-200/80">
                <div class="flex items-center gap-2.5 min-w-0">
                    <div class="w-9 h-9 rounded-full bg-amber-100 text-amber-900 border border-amber-200 flex items-center justify-center font-bold text-xs shrink-0">
                        {{ $userInitials }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-bold text-slate-900 leading-tight truncate">
                            {{ $authUser->name ?? 'Pengguna' }}
                        </p>
                        <div class="mt-0.5">
                            @if(($authUser->role ?? '') === 'admin')
                                <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-amber-100 text-amber-900 border border-amber-300">Admin/Owner</span>
                            @elseif(($authUser->role ?? '') === 'cashier')
                                <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-blue-50 text-blue-700 border border-blue-200">Kasir POS</span>
                            @else
                                <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">Dapur/Barista</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Tombol Logout -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition-colors shrink-0" title="Keluar / Ganti Shift">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Main Content Container (Bright White Background) -->
    <main class="flex-1 flex flex-col min-w-0 bg-slate-50 overflow-hidden">
        {{ $slot }}
    </main>

    @livewireScripts
</body>
</html>
