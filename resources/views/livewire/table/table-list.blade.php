<div class="p-6 space-y-6 overflow-y-auto h-full bg-slate-50">
    <div class="flex items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Manajemen Meja Dine-In</h2>
            <p class="text-xs text-slate-500 mt-1">Monitoring, tambah, edit, atau ubah status ketersediaan meja cafe</p>
        </div>

        <button wire:click="openCreateModal" class="px-4 py-2 rounded-xl bg-amber-700 hover:bg-amber-800 text-white font-bold text-xs shadow-md shadow-amber-700/20 flex items-center gap-1.5 transition-all">
            <svg class="w-4 h-4 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            <span>Tambah Meja Baru</span>
        </button>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        @foreach($tables as $t)
            <div class="bg-white border border-slate-200/80 rounded-2xl p-5 flex flex-col justify-between space-y-4 shadow-2xs">
                <div class="flex items-start justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl {{ $t->status === 'available' ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-rose-50 text-rose-700 border border-rose-100' }} flex items-center justify-center font-bold">
                            <svg class="w-6 h-6 fill-none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h4m-4 0V11m0 0V8a2 2 0 012-2h2a2 2 0 012 2v3m-6 0h6"/></svg>
                        </div>
                        <div>
                            <h3 class="font-extrabold text-slate-900 text-base">{{ $t->table_number }}</h3>
                            <p class="text-xs text-slate-500">Kapasitas: {{ $t->capacity }} Orang</p>
                        </div>
                    </div>

                    @if($t->status === 'available')
                        <span class="px-3 py-1 rounded-full text-[10px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200">KOSONG</span>
                    @elseif($t->status === 'occupied')
                        <span class="px-3 py-1 rounded-full text-[10px] font-extrabold bg-rose-50 text-rose-700 border border-rose-200">TERISI</span>
                    @else
                        <span class="px-3 py-1 rounded-full text-[10px] font-extrabold bg-amber-50 text-amber-700 border border-amber-200">RESERVED</span>
                    @endif
                </div>

                <div class="flex items-center gap-2 pt-2 border-t border-slate-100">
                    <button wire:click="toggleStatus({{ $t->id }})" class="flex-1 py-2 px-3 rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100 text-xs font-bold text-slate-700 transition-colors">
                        Ubah Status
                    </button>
                    <button wire:click="editTable({{ $t->id }})" class="py-2 px-3 rounded-xl bg-slate-100 hover:bg-amber-100 text-slate-700 hover:text-amber-800 text-xs font-bold transition-colors">
                        Edit
                    </button>
                    <button wire:click="deleteTable({{ $t->id }})" onclick="return confirm('Apakah Anda yakin ingin menghapus meja ini?')" class="py-2 px-3 rounded-xl bg-slate-100 hover:bg-rose-100 text-slate-700 hover:text-rose-700 text-xs font-bold transition-colors">
                        Hapus
                    </button>
                </div>
            </div>
        @endforeach
    </div>

    <!-- FORM MODAL TAMBAH / EDIT MEJA -->
    @if($showModal)
        <div class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white border border-slate-200 rounded-2xl w-full max-w-md p-6 shadow-2xl space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="text-base font-extrabold text-slate-900">
                        {{ $isEditMode ? 'Edit Data Meja' : 'Tambah Meja Baru' }}
                    </h3>
                    <button wire:click="closeModal" class="text-slate-400 hover:text-slate-700">
                        <svg class="w-5 h-5 fill-none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form wire:submit.prevent="saveTable" class="space-y-3 text-xs">
                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Nomor / Nama Meja</label>
                        <input type="text" wire:model="table_number" placeholder="Contoh: Meja 06 atau VIP 2" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 font-bold text-slate-900 focus:outline-none focus:border-amber-700">
                        @error('table_number') <span class="text-rose-600 text-[11px] font-bold">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="font-bold text-slate-700 block mb-1">Kapasitas (Orang)</label>
                            <input type="number" wire:model="capacity" min="1" max="50" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 font-bold text-slate-900 focus:outline-none focus:border-amber-700">
                            @error('capacity') <span class="text-rose-600 text-[11px] font-bold">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="font-bold text-slate-700 block mb-1">Status Awal</label>
                            <select wire:model="status" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 font-bold text-slate-900 focus:outline-none focus:border-amber-700">
                                <option value="available">KOSONG (Available)</option>
                                <option value="occupied">TERISI (Occupied)</option>
                                <option value="reserved">RESERVED</option>
                            </select>
                            @error('status') <span class="text-rose-600 text-[11px] font-bold">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="flex gap-2 pt-3 border-t border-slate-100">
                        <button type="submit" class="flex-1 py-2.5 bg-amber-700 hover:bg-amber-800 text-white rounded-xl font-bold transition-all shadow-md shadow-amber-700/20">
                            Simpan Data Meja
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
