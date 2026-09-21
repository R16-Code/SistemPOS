<div class="p-6 space-y-6 overflow-y-auto h-full bg-slate-50">
    <div class="flex items-center justify-between gap-4 flex-wrap">
        <div>
            <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Kelola Pengguna & Karyawan</h2>
            <p class="text-xs text-slate-500 mt-1">Kelola akun staf cafe, hak akses peran (Admin, Kasir, Dapur), dan status keaktifan</p>
        </div>

        <div class="flex items-center gap-3">
            <input type="text" 
                   wire:model.live.debounce.250ms="search" 
                   placeholder="Cari nama / email..." 
                   class="bg-white border border-slate-200 rounded-xl px-4 py-2 text-xs text-slate-900 focus:outline-none focus:border-amber-700 shadow-2xs">

            <select wire:model.live="roleFilter" class="bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-900 font-medium focus:outline-none focus:border-amber-700 shadow-2xs">
                <option value="">Semua Peran</option>
                <option value="admin">Admin / Owner</option>
                <option value="cashier">Kasir POS</option>
                <option value="kitchen">Dapur / Barista</option>
            </select>

            <button type="button" 
                    wire:click="openCreateModal" 
                    class="px-4 py-2 rounded-xl bg-amber-700 hover:bg-amber-800 text-white font-bold text-xs shadow-md shadow-amber-700/20 flex items-center gap-1.5 transition-all">
                <svg class="w-4 h-4 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                <span>Tambah Pengguna</span>
            </button>
        </div>
    </div>

    <div class="bg-white border border-slate-200/80 rounded-2xl overflow-hidden shadow-sm">
        <table class="w-full text-left text-xs text-slate-700">
            <thead class="bg-slate-100/70 text-slate-600 border-b border-slate-200 font-bold uppercase tracking-wider text-[11px]">
                <tr>
                    <th class="px-6 py-4">Nama Lengkap</th>
                    <th class="px-6 py-4">Email</th>
                    <th class="px-6 py-4 text-center">Peran (Role)</th>
                    <th class="px-6 py-4 text-center">Status Akun</th>
                    <th class="px-6 py-4">Terdaftar Sejak</th>
                    <th class="px-6 py-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($users as $user)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-amber-100 text-amber-900 border border-amber-200 flex items-center justify-center font-bold text-xs shrink-0">
                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                </div>
                                <div>
                                    <span class="font-bold text-slate-900 block">{{ $user->name }}</span>
                                    @if($user->id === auth()->id())
                                        <span class="text-[10px] text-amber-800 font-semibold bg-amber-50 px-1.5 py-0.5 rounded border border-amber-200">Akun Anda</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 font-medium text-slate-600">{{ $user->email }}</td>
                        <td class="px-6 py-4 text-center">
                            @if($user->role === 'admin')
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-amber-100 text-amber-900 border border-amber-300">
                                    Admin / Owner
                                </span>
                            @elseif($user->role === 'cashier')
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-blue-50 text-blue-700 border border-blue-200">
                                    Kasir POS
                                </span>
                            @else
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    Dapur / Barista
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <button type="button" 
                                    wire:click="toggleActive({{ $user->id }})" 
                                    @if($user->id === auth()->id()) disabled title="Tidak dapat menonaktifkan akun sendiri" @else title="Klik untuk mengubah status" @endif
                                    class="px-3 py-1 rounded-full text-[10px] font-bold transition-all shadow-2xs border {{ $user->is_active ? 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100' : 'bg-rose-50 text-rose-700 border-rose-200 hover:bg-rose-100' }} {{ $user->id === auth()->id() ? 'opacity-70 cursor-not-allowed' : '' }}">
                                {{ $user->is_active ? 'AKTIF' : 'NONAKTIF' }}
                            </button>
                        </td>
                        <td class="px-6 py-4 text-slate-500 whitespace-nowrap">{{ $user->created_at->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <button type="button" wire:click="editUser({{ $user->id }})" class="p-1.5 rounded-lg bg-slate-100 hover:bg-amber-100 text-slate-700 hover:text-amber-800 font-bold transition-colors">
                                    Edit
                                </button>
                                @if($user->id !== auth()->id())
                                    <button type="button" wire:click="deleteUser({{ $user->id }})" onclick="return confirm('Apakah Anda yakin ingin menghapus/menonaktifkan pengguna ini?')" class="p-1.5 rounded-lg bg-slate-100 hover:bg-rose-100 text-slate-700 hover:text-rose-700 font-bold transition-colors">
                                        Hapus
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-400">Belum ada pengguna ditemukan</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="p-4 border-t border-slate-100">
            {{ $users->links() }}
        </div>
    </div>

    <!-- FORM MODAL (TAMBAH / EDIT PENGGUNA) -->
    @if($showFormModal)
        <div class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white border border-slate-200 rounded-2xl w-full max-w-lg shadow-2xl overflow-hidden flex flex-col">
                <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4 bg-slate-50/50">
                    <h3 class="text-base font-extrabold text-slate-900">
                        {{ $isEditMode ? 'Edit Pengguna / Staf' : 'Tambah Pengguna Baru' }}
                    </h3>
                    <button type="button" wire:click="closeModal" class="text-slate-400 hover:text-slate-700 p-1 rounded-lg hover:bg-slate-100 transition-colors">
                        <svg class="w-5 h-5 fill-none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form wire:submit.prevent="saveUser" class="p-6 space-y-4 text-xs">
                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Nama Lengkap</label>
                        <input type="text" wire:model="name" placeholder="Contoh: Sarah Barista" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 font-bold text-slate-900 focus:outline-none focus:border-amber-700">
                        @error('name') <span class="text-rose-600 text-[11px] font-bold block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Alamat Email</label>
                        <input type="email" wire:model="email" placeholder="sarah@pos.com" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 font-medium text-slate-900 focus:outline-none focus:border-amber-700">
                        @error('email') <span class="text-rose-600 text-[11px] font-bold block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="font-bold text-slate-700 block mb-1">
                            Password
                            @if($isEditMode)
                                <span class="font-normal text-slate-400">(Kosongkan jika tidak ingin mengubah)</span>
                            @endif
                        </label>
                        <input type="password" wire:model="password" placeholder="Minimal 6 karakter" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 font-medium text-slate-900 focus:outline-none focus:border-amber-700">
                        @error('password') <span class="text-rose-600 text-[11px] font-bold block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Peran (Role Akses)</label>
                        <select wire:model="role" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 font-bold text-slate-900 focus:outline-none focus:border-amber-700">
                            <option value="admin">Admin / Owner (Akses Penuh)</option>
                            <option value="cashier">Kasir (Kasir POS, Meja, & Transaksi)</option>
                            <option value="kitchen">Dapur / Barista (Antrian Pesanan)</option>
                        </select>
                        @error('role') <span class="text-rose-600 text-[11px] font-bold block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex items-center gap-2 pt-1">
                        <input type="checkbox" wire:model="is_active" id="user_is_active" class="w-4 h-4 rounded border-slate-300 text-amber-700 focus:ring-amber-700">
                        <label for="user_is_active" class="font-bold text-slate-700 cursor-pointer">Status Akun Aktif (Dapat Login ke Sistem)</label>
                    </div>

                    <div class="flex gap-2 pt-3 border-t border-slate-100">
                        <button type="submit" class="flex-1 py-2.5 bg-amber-700 hover:bg-amber-800 text-white rounded-xl font-bold transition-all shadow-md shadow-amber-700/20">
                            Simpan Pengguna
                        </button>
                        <button type="button" wire:click="closeModal" class="px-5 py-2.5 bg-white border border-slate-200 text-slate-700 hover:bg-slate-100 rounded-xl font-bold transition-colors">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
