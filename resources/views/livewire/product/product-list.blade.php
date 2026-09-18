<div class="p-6 space-y-6 overflow-y-auto h-full">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-white tracking-tight">Manajemen Menu & Produk</h2>
            <p class="text-xs text-slate-400 mt-1">Daftar semua menu minuman, makanan, dan snack di cafe</p>
        </div>

        <div class="flex items-center gap-3">
            <input type="text" wire:model.live.debounce.250ms="search" placeholder="Cari nama produk / SKU..." class="bg-slate-900 border border-slate-800 rounded-xl px-4 py-2 text-xs text-slate-200 focus:outline-none focus:border-amber-500">
        </div>
    </div>

    <div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden shadow-xl">
        <table class="w-full text-left text-xs text-slate-300">
            <thead class="bg-slate-950 text-slate-400 border-b border-slate-800 font-semibold uppercase tracking-wider text-[11px]">
                <tr>
                    <th class="px-6 py-4">SKU</th>
                    <th class="px-6 py-4">Nama Produk</th>
                    <th class="px-6 py-4">Kategori</th>
                    <th class="px-6 py-4">Harga</th>
                    <th class="px-6 py-4">Stok</th>
                    <th class="px-6 py-4 text-center">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/60">
                @forelse($products as $p)
                    <tr class="hover:bg-slate-800/40 transition-colors">
                        <td class="px-6 py-4 font-mono font-semibold text-amber-400">{{ $p->sku }}</td>
                        <td class="px-6 py-4 font-semibold text-white">{{ $p->name }}</td>
                        <td class="px-6 py-4 text-slate-400">{{ $p->category->name ?? '-' }}</td>
                        <td class="px-6 py-4 font-bold text-slate-200">Rp {{ number_format($p->price, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 text-slate-300 font-mono">{{ $p->stock }}</td>
                        <td class="px-6 py-4 text-center">
                            @if($p->is_available)
                                <span class="px-3 py-1 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">Tersedia</span>
                            @else
                                <span class="px-3 py-1 rounded-full text-[10px] font-bold bg-rose-500/10 text-rose-400 border border-rose-500/20">Habis</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-500">Belum ada produk ditemukan</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="p-4 border-t border-slate-800">
            {{ $products->links() }}
        </div>
    </div>
</div>
