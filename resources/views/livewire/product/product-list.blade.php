<div class="p-6 space-y-6 overflow-y-auto h-full bg-slate-50">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Manajemen Menu & Produk</h2>
            <p class="text-xs text-slate-500 mt-1">Tambah, ubah, atau nonaktifkan status ketersediaan menu cafe</p>
        </div>

        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
            <input type="text" wire:model.live.debounce.250ms="search" placeholder="Cari nama produk / SKU..." class="bg-white border border-slate-200 rounded-xl px-4 py-2 text-xs text-slate-900 focus:outline-none focus:border-amber-700 shadow-2xs">
            
            <button wire:click="openCreateModal" class="min-h-[44px] px-4 py-2 rounded-xl bg-amber-700 hover:bg-amber-800 text-white font-bold text-xs shadow-md shadow-amber-700/20 flex items-center justify-center gap-1.5 transition-all">
                <svg class="w-4 h-4 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                <span>Tambah Produk</span>
            </button>
        </div>
    </div>

    <div class="bg-white border border-slate-200/80 rounded-2xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[700px] text-left text-xs text-slate-700">
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
                            @if($p->variants->isNotEmpty())
                                <div class="flex items-center gap-1.5 mt-1 flex-wrap">
                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold text-amber-800 bg-amber-50 px-2 py-0.5 rounded-md border border-amber-200">
                                        <svg class="w-3 h-3 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/></svg>
                                        <span>{{ $p->variants->count() }} Varian ({{ $p->variants->pluck('variant_group')->unique()->implode(', ') }})</span>
                                    </span>
                                </div>
                            @endif
                            <span class="text-[11px] text-slate-400 line-clamp-1 mt-0.5">{{ $p->description }}</span>
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
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $products->links() }}
        </div>
    </div>

    <!-- FORM MODAL (TAMBAH / EDIT PRODUK) -->
    @if($showFormModal)
        <div class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white border border-slate-200 rounded-2xl w-full max-w-2xl max-h-[92vh] flex flex-col shadow-2xl overflow-hidden">
                <!-- Modal Header -->
                <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4 bg-slate-50/50 shrink-0">
                    <h3 class="text-base font-extrabold text-slate-900">
                        {{ $isEditMode ? 'Edit Produk' : 'Tambah Produk Baru' }}
                    </h3>
                    <button wire:click="closeModal" class="text-slate-400 hover:text-slate-700 min-h-[44px] min-w-[44px] flex items-center justify-center rounded-xl hover:bg-slate-100 transition-colors">
                        <svg class="w-5 h-5 fill-none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Modal Body (Scrollable) -->
                <form wire:submit.prevent="saveProduct" id="productForm" class="p-6 overflow-y-auto space-y-4 text-xs flex-1">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="font-bold text-slate-700 block mb-1">Kode SKU</label>
                            <input type="text" wire:model="sku" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 font-mono font-bold text-slate-900 focus:outline-none focus:border-amber-700">
                            @error('sku') <span class="text-rose-600 text-[11px] font-bold">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="font-bold text-slate-700 block mb-1">Kategori</label>
                            <select wire:model.live="category_id" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 font-medium text-slate-900 focus:outline-none focus:border-amber-700">
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
                            <label class="font-bold text-slate-700 block mb-1">Harga Dasar (Rp)</label>
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

                    <!-- PERTANYAAN / TOGGLE VARIAN -->
                    <div class="pt-3 border-t border-slate-100">
                        <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between gap-4">
                            <div>
                                <label for="toggle_has_variants" class="font-bold text-slate-800 text-xs block cursor-pointer">
                                    Apakah produk ini memiliki opsi varian?
                                </label>
                                <p class="text-[11px] text-slate-500 mt-0.5">
                                    Aktifkan jika produk memiliki pilihan seperti Suhu (Hot/Ice), Ukuran (Reguler/Large), atau Tambahan.
                                </p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer shrink-0">
                                <input type="checkbox" wire:model.live="hasVariants" id="toggle_has_variants" class="sr-only peer">
                                <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-amber-700"></div>
                            </label>
                        </div>
                    </div>

                    <!-- DAFTAR INPUT VARIAN (DITAMPILKAN JIKA hasVariants AKTIF) -->
                    @if($hasVariants)
                        <div class="p-4 bg-amber-50/40 border border-amber-200/80 rounded-xl space-y-3">
                            <div class="flex items-center justify-between flex-wrap gap-2">
                                <div>
                                    <span class="font-bold text-xs text-amber-950 block">Daftar Pilihan Varian</span>
                                    <span class="text-[11px] text-slate-500">Tentukan kelompok varian, nama pilihan, dan harga tambahan (jika ada).</span>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <button type="button" wire:click="addPreset('temperature')" class="px-2 py-1 bg-white border border-amber-200 text-amber-800 rounded-lg text-[10px] font-bold hover:bg-amber-100 transition-colors">
                                        + Preset Suhu
                                    </button>
                                    <button type="button" wire:click="addPreset('size')" class="px-2 py-1 bg-white border border-amber-200 text-amber-800 rounded-lg text-[10px] font-bold hover:bg-amber-100 transition-colors">
                                        + Preset Ukuran
                                    </button>
                                    <button type="button" wire:click="addVariantRow" class="px-2.5 py-1 bg-amber-700 hover:bg-amber-800 text-white rounded-lg font-bold text-[11px] flex items-center gap-1 transition-all shadow-xs">
                                        <svg class="w-3.5 h-3.5 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                        <span>Tambah Opsi</span>
                                    </button>
                                </div>
                            </div>

                            @if(empty($variants))
                                <div class="text-center py-5 text-xs text-slate-400 bg-white rounded-xl border border-dashed border-slate-200">
                                    Belum ada baris varian. Klik tombol <strong class="text-slate-600">+ Tambah Opsi</strong> atau gunakan preset di atas.
                                </div>
                            @else
                                <div class="space-y-2">
                                    @foreach($variants as $index => $variant)
                                        <div wire:key="variant-row-{{ $index }}" class="flex items-start gap-2 bg-white p-2.5 rounded-xl border border-slate-200 shadow-2xs">
                                            <div class="w-36">
                                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Grup Varian</label>
                                                <input type="text" wire:model="variants.{{ $index }}.variant_group" placeholder="Misal: Suhu" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs text-slate-800 font-bold focus:outline-none focus:border-amber-700">
                                                @error('variants.' . $index . '.variant_group') <span class="text-rose-600 text-[10px] font-bold block mt-0.5">{{ $message }}</span> @enderror
                                            </div>
                                            <div class="flex-1">
                                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Nama Opsi</label>
                                                <input type="text" wire:model="variants.{{ $index }}.name" placeholder="Misal: Hot / Ice" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs text-slate-800 font-bold focus:outline-none focus:border-amber-700">
                                                @error('variants.' . $index . '.name') <span class="text-rose-600 text-[10px] font-bold block mt-0.5">{{ $message }}</span> @enderror
                                            </div>
                                            <div class="w-28">
                                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">+ Harga (Rp)</label>
                                                <input type="number" min="0" wire:model="variants.{{ $index }}.extra_price" placeholder="0" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs text-slate-800 font-bold focus:outline-none focus:border-amber-700">
                                                @error('variants.' . $index . '.extra_price') <span class="text-rose-600 text-[10px] font-bold block mt-0.5">{{ $message }}</span> @enderror
                                            </div>
                                            <div class="pt-5">
                                                <button type="button" wire:click="removeVariantRow({{ $index }})" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors" title="Hapus opsi ini">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            @error('variants')
                                <p class="text-rose-600 text-xs font-bold">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif
                </form>

                <!-- Modal Footer -->
                <div class="flex gap-2 p-4 bg-slate-50 border-t border-slate-100 shrink-0">
                    <button type="submit" form="productForm" class="flex-1 py-2.5 bg-amber-700 hover:bg-amber-800 text-white rounded-xl font-bold transition-all shadow-md shadow-amber-700/20">
                        Simpan Produk
                    </button>
                    <button type="button" wire:click="closeModal" class="px-5 py-2.5 bg-white border border-slate-200 text-slate-700 hover:bg-slate-100 rounded-xl font-bold transition-colors">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
