<div class="p-6 space-y-6 overflow-y-auto h-full bg-slate-50">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Manajemen Meja Dine-In</h2>
            <p class="text-xs text-slate-500 mt-1">Monitoring, tambah, edit, atau ubah status ketersediaan meja cafe</p>
        </div>

        <button wire:click="openCreateModal" class="min-h-[44px] px-4 py-2 rounded-xl bg-amber-700 hover:bg-amber-800 text-white font-bold text-xs shadow-md shadow-amber-700/20 flex items-center justify-center gap-1.5 transition-all">
            <svg class="w-4 h-4 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            <span>Tambah Meja Baru</span>
        </button>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-4 sm:gap-5">
        @foreach($tables as $t)
            <div class="bg-white border border-slate-200/80 rounded-2xl p-4 sm:p-5 flex flex-col justify-between space-y-4 shadow-2xs">
                <div class="flex items-start justify-between gap-2">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl {{ $t->status === 'available' ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-rose-50 text-rose-700 border border-rose-100' }} flex items-center justify-center font-bold shrink-0">
                            <svg class="w-6 h-6 fill-none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h4m-4 0V11m0 0V8a2 2 0 012-2h2a2 2 0 012 2v3m-6 0h6"/></svg>
                        </div>
                        <div>
                            <h3 class="font-extrabold text-slate-900 text-base leading-tight">{{ $t->table_number }}</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Kapasitas: {{ $t->capacity }} Orang</p>
                        </div>
                    </div>

                    @if($t->status === 'available')
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200 whitespace-nowrap shrink-0">KOSONG</span>
                    @elseif($t->status === 'occupied')
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-rose-50 text-rose-700 border border-rose-200 whitespace-nowrap shrink-0">TERISI</span>
                    @else
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-amber-50 text-amber-700 border border-amber-200 whitespace-nowrap shrink-0">RESERVED</span>
                    @endif
                </div>

                <div class="pt-3 border-t border-slate-100 space-y-2">
                    <button wire:click="toggleStatus({{ $t->id }})" 
                            class="w-full min-h-[40px] py-2 px-3 rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100 active:scale-98 text-xs font-bold text-slate-700 transition-all flex items-center justify-center gap-1.5 cursor-pointer">
                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        <span>Ubah Status</span>
                    </button>
                    <div class="grid grid-cols-3 gap-1.5">
                        <button wire:click="openQrModal({{ $t->id }})" 
                                class="min-h-[38px] py-1.5 px-2 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-900 border border-amber-200/80 text-xs font-bold transition-all flex items-center justify-center gap-1 cursor-pointer">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                            <span>QR</span>
                        </button>
                        <button wire:click="editTable({{ $t->id }})" 
                                class="min-h-[38px] py-1.5 px-2 rounded-xl bg-slate-100 hover:bg-amber-100 text-slate-700 hover:text-amber-800 text-xs font-bold transition-all flex items-center justify-center cursor-pointer">
                            <span>Edit</span>
                        </button>
                        <button wire:click="deleteTable({{ $t->id }})" 
                                onclick="return confirm('Apakah Anda yakin ingin menghapus meja ini?')" 
                                class="min-h-[38px] py-1.5 px-2 rounded-xl bg-slate-100 hover:bg-rose-100 text-slate-700 hover:text-rose-700 text-xs font-bold transition-all flex items-center justify-center cursor-pointer">
                            <span>Hapus</span>
                        </button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- FORM MODAL TAMBAH / EDIT MEJA -->
    @if($showModal)
        <div class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white border border-slate-200 rounded-2xl w-full max-w-md p-6 shadow-2xl space-y-4 max-h-[85vh] overflow-y-auto">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="text-base font-extrabold text-slate-900">
                        {{ $isEditMode ? 'Edit Data Meja' : 'Tambah Meja Baru' }}
                    </h3>
                    <button wire:click="closeModal" class="text-slate-400 hover:text-slate-700 min-h-[44px] min-w-[44px] flex items-center justify-center rounded-xl">
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

    <!-- MODAL: PREVIEW & CETAK QR CODE MEJA -->
    @if($showQrModal && $selectedQrTable)
        <div class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white border border-slate-200 rounded-3xl w-full max-w-md p-6 shadow-2xl space-y-4 max-h-[85vh] overflow-y-auto animate-in fade-in zoom-in-95 duration-150">
                <!-- Modal Header -->
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-xl bg-amber-800 text-white flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-base font-extrabold text-slate-900">QR Code {{ $selectedQrTable->table_number }}</h3>
                            <p class="text-xs text-slate-500 font-medium">Kapasitas {{ $selectedQrTable->capacity }} Orang</p>
                        </div>
                    </div>
                    <button type="button" wire:click="closeQrModal" class="text-slate-400 hover:text-slate-600 min-h-[44px] min-w-[44px] flex items-center justify-center rounded-xl">
                        <svg class="w-5 h-5 fill-none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Self-order Active Toggle -->
                <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-slate-900">Pemesanan QR Meja</p>
                        <p class="text-[11px] text-slate-500">{{ $selectedQrTable->is_active ? 'Aktif, pelanggan dapat memesan' : 'Dinonaktifkan sementara' }}</p>
                    </div>
                    <button type="button"
                            wire:click="toggleTableSelfOrder({{ $selectedQrTable->id }})"
                            class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all {{ $selectedQrTable->is_active ? 'bg-emerald-600 text-white' : 'bg-slate-300 text-slate-700' }}">
                        {{ $selectedQrTable->is_active ? 'Aktif' : 'Nonaktif' }}
                    </button>
                </div>

                <!-- QR Code Display -->
                <div class="text-center p-4 bg-slate-50 rounded-2xl border border-slate-200/80">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=250x250&data={{ urlencode($selectedQrTable->qr_url) }}&margin=8"
                         alt="QR Meja {{ $selectedQrTable->table_number }}"
                         class="w-44 h-44 mx-auto rounded-xl shadow-xs bg-white p-2 border border-slate-200">
                    <p class="text-[11px] text-slate-500 mt-2 font-medium">Pindai dengan kamera HP pelanggan</p>
                </div>

                <!-- Direct URL & Copy -->
                <div class="space-y-1.5" x-data="{ copied: false }">
                    <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider">URL Pemesanan Meja</label>
                    <div class="flex items-center gap-2">
                        <input type="text"
                               readonly
                               value="{{ $selectedQrTable->qr_url }}"
                               class="w-full text-xs bg-slate-100 border border-slate-200 rounded-xl px-3 py-2 text-slate-700 font-mono select-all focus:outline-none">
                        <button type="button"
                                @click="navigator.clipboard.writeText('{{ $selectedQrTable->qr_url }}'); copied = true; setTimeout(() => copied = false, 2000)"
                                class="min-h-[36px] px-3 rounded-xl bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold shrink-0 transition-colors">
                            <span x-show="!copied">Salin</span>
                            <span x-show="copied" class="text-emerald-300">Tersalin!</span>
                        </button>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-2.5 pt-2 border-t border-slate-100">
                    <a href="{{ $selectedQrTable->qr_url }}"
                       target="_blank"
                       class="flex-1 min-h-[42px] rounded-xl border border-slate-300 hover:bg-slate-50 text-slate-700 font-bold text-xs flex items-center justify-center gap-1.5 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        <span>Uji Buka URL</span>
                    </a>

                    <a href="{{ route('tables.print-qr', $selectedQrTable->id) }}"
                       target="_blank"
                       class="flex-1 min-h-[42px] rounded-xl bg-amber-800 hover:bg-amber-900 active:scale-95 text-white font-bold text-xs flex items-center justify-center gap-1.5 shadow-xs transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>Cetak Kartu Meja</span>
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>
