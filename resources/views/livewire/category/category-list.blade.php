<div class="p-6 space-y-6 overflow-y-auto h-full bg-slate-50">
    <div>
        <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Kategori Produk</h2>
        <p class="text-xs text-slate-500 mt-1">Pengelompokan jenis menu di cafe</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
        @foreach($categories as $cat)
            <div class="bg-white border border-slate-200/80 rounded-2xl p-5 flex items-center justify-between shadow-2xs">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center border border-amber-100 font-bold">
                        <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24"><path d="M2,21V19H20V21H2M20,8V5H18V8H20M20,3A2,2 0 0,1 22,5V8A2,2 0 0,1 20,10H18V13A4,4 0 0,1 14,17H8A4,4 0 0,1 4,13V3H20M16,5H6V13A2,2 0 0,0 8,15H14A2,2 0 0,0 16,13V5Z"/></svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-900 text-base">{{ $cat->name }}</h3>
                        <p class="text-xs text-slate-500 mt-0.5">{{ $cat->products_count }} Menu Produk</p>
                    </div>
                </div>

                <span class="px-3 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                    Aktif
                </span>
            </div>
        @endforeach
    </div>
</div>
