<div class="p-6 space-y-6 overflow-y-auto h-full bg-slate-50">
    <div class="flex items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Manajemen Menu & Produk</h2>
            <p class="text-xs text-slate-500 mt-1">Tambah, ubah, atau nonaktifkan status ketersediaan menu cafe</p>
        </div>

        <div class="flex items-center gap-3">
            <input type="text" wire:model.live.debounce.250ms="search" placeholder="Cari nama produk / SKU..." class="bg-white border border-slate-200 rounded-xl px-4 py-2 text-xs text-slate-900 focus:outline-none focus:border-amber-700 shadow-2xs">
            
            <button wire:click="openCreateModal" class="px-4 py-2 rounded-xl bg-amber-700 hover:bg-amber-800 text-white font-bold text-xs shadow-md shadow-amber-700/20 flex items-center gap-1.5 transition-all">
                <svg class="w-4 h-4 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                <span>Tambah Produk</span>
            </button>
        </div>
    </div>

    <div class="bg-white border border-slate-200/80 rounded-2xl overflow-hidden shadow-sm">
        <table class="w-full text-left text-xs text-slate-700">
            <thead class="bg-slate-100/70 text-slate-600 border-b border-slate-200 font-bold uppercase tracking-wider text-[11px]">
                <tr>
                    <th class="px-6 py-4">SKU</th>
                    <th class="px-6 py-4">Nama Produk</th>
                    <th class="px-6 py-4">Kategori</th>
                    <th class="px-6 py-4">Harga</th>
                    <th class="px-6 py-4">Stok</th>
                    <th class="px-6 py-4 text-center">Status</th>
                    <th class="px-6 py-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($products as $p)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="px-6 py-4 font-mono font-bold text-amber-800">{{ $p->sku }}</td>
                        <td class="px-6 py-4">
                            <span class="font-bold text-slate-900 block">{{ $p->name }}</span>
                            <span class="text-[11px] text-slate-400 line-clamp-1">{{ $p->description }}</span>
                        </td>
                        <td class="px-6 py-4 font-medium text-slate-600">{{ $p->category->name ?? '-' }}</td>
                        <td class="px-6 py-4 font-extrabold text-slate-900">Rp {{ number_format($p->price, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 text-slate-800 font-mono font-bold">{{ $p->stock }}</td>
                        <td class="px-6 py-4 text-center">
                            <button wire:click="toggleAvailable({{ $p->id }})" title="Klik untuk mengubah status" class="px-3 py-1 rounded-full text-[10px] font-bold transition-all shadow-2xs border {{ $p->is_available ? 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100' : 'bg-rose-50 text-rose-700 border-rose-200 hover:bg-rose-100' }}">
                                {{ $p->is_available ? 'TERSEDIA' : 'HABIS' }}
                            </button>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <button wire:click="editProduct({{ $p->id }})" class="p-1.5 rounded-lg bg-slate-100 hover:bg-amber-100 text-slate-700 hover:text-amber-800 font-bold transition-colors">
                                    Edit
                                </button>
                                <button wire:click="deleteProduct({{ $p->id }})" onclick="return confirm('Apakah Anda yakin ingin menghapus produk ini?')" class="p-1.5 rounded-lg bg-slate-100 hover:bg-rose-100 text-slate-700 hover:text-rose-700 font-bold transition-colors">
                                    Hapus
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-slate-400">Belum ada produk ditemukan</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="p-4 border-t border-slate-100">
            {{ $products->links() }}
        </div>
    </div>

    <!-- FORM MODAL (TAMBAH / EDIT PRODUK) -->
    @if($showFormModal)
        <div class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white border border-slate-200 rounded-2xl w-full max-w-lg p-6 shadow-2xl space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="text-base font-extrabold text-slate-900">
                        {{ $isEditMode ? 'Edit Produk' : 'Tambah Produk Baru' }}
                    </h3>
                    <button wire:click="closeModal" class="text-slate-400 hover:text-slate-700">
                        <svg class="w-5 h-5 fill-none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form wire:submit.prevent="saveProduct" class="space-y-3 text-xs">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="font-bold text-slate-700 block mb-1">Kode SKU</label>
                            <input type="text" wire:model="sku" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 font-mono font-bold text-slate-900 focus:outline-none focus:border-amber-700">
                            @error('sku') <span class="text-rose-600 text-[11px] font-bold">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="font-bold text-slate-700 block mb-1">Kategori</label>
                            <select wire:model="category_id" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 font-medium text-slate-900 focus:outline-none focus:border-amber-700">
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id') <span class="text-rose-600 text-[11px] font-bold">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Nama Produk</label>
                        <input type="text" wire:model="name" placeholder="Contoh: Kopi Susu Aren" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 font-bold text-slate-900 focus:outline-none focus:border-amber-700">
                        @error('name') <span class="text-rose-600 text-[11px] font-bold">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="font-bold text-slate-700 block mb-1">Harga (Rp)</label>
                            <input type="number" wire:model="price" min="0" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 font-bold text-slate-900 focus:outline-none focus:border-amber-700">
                            @error('price') <span class="text-rose-600 text-[11px] font-bold">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="font-bold text-slate-700 block mb-1">Stok Awal</label>
                            <input type="number" wire:model="stock" min="0" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 font-bold text-slate-900 focus:outline-none focus:border-amber-700">
                            @error('stock') <span class="text-rose-600 text-[11px] font-bold">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Deskripsi Singkat</label>
                        <textarea wire:model="description" rows="2" placeholder="Komposisi atau catatan menu..." class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-900 focus:outline-none focus:border-amber-700"></textarea>
                    </div>

                    <div class="flex items-center gap-2 pt-1">
                        <input type="checkbox" wire:model="is_available" id="is_available" class="w-4 h-4 rounded border-slate-300 text-amber-700 focus:ring-amber-700">
                        <label for="is_available" class="font-bold text-slate-700 cursor-pointer">Status Produk Tersedia untuk Dipesan</label>
                    </div>

                    <div class="flex gap-2 pt-3 border-t border-slate-100">
                        <button type="submit" class="flex-1 py-2.5 bg-amber-700 hover:bg-amber-800 text-white rounded-xl font-bold transition-all shadow-md shadow-amber-700/20">
                            Simpan Produk
                        </button>
                        <button type="button" wire:click="closeModal" class="px-4 py-2.5 bg-slate-100 text-slate-700 rounded-xl font-bold">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
