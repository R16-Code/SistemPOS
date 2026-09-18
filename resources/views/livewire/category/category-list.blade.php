<div class="p-6 space-y-6 overflow-y-auto h-full">
    <div>
        <h2 class="text-2xl font-bold text-white tracking-tight">Kategori Produk</h2>
        <p class="text-xs text-slate-400 mt-1">Pengelompokan jenis menu di cafe</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
        @foreach($categories as $cat)
            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-amber-500/10 text-amber-400 flex items-center justify-center">
                        <i data-lucide="{{ $cat->icon ?: 'folder' }}" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-white text-base">{{ $cat->name }}</h3>
                        <p class="text-xs text-slate-400 mt-0.5">{{ $cat->products_count }} Menu Produk</p>
                    </div>
                </div>

                <span class="px-3 py-1 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                    Aktif
                </span>
            </div>
        @endforeach
    </div>
</div>
