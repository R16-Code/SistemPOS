<div class="p-6 space-y-6 overflow-y-auto h-full">
    <div>
        <h2 class="text-2xl font-bold text-white tracking-tight">Manajemen Meja Dine-In</h2>
        <p class="text-xs text-slate-400 mt-1">Monitoring ketersediaan meja cafe secara real-time</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        @foreach($tables as $t)
            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 flex flex-col justify-between space-y-4">
                <div class="flex items-start justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl {{ $t->status === 'available' ? 'bg-emerald-500/10 text-emerald-400' : 'bg-rose-500/10 text-rose-400' }} flex items-center justify-center">
                            <i data-lucide="armchair" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-white text-base">{{ $t->table_number }}</h3>
                            <p class="text-xs text-slate-400">Kapasitas: {{ $t->capacity }} Orang</p>
                        </div>
                    </div>

                    @if($t->status === 'available')
                        <span class="px-3 py-1 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">KOSONG</span>
                    @else
                        <span class="px-3 py-1 rounded-full text-[10px] font-bold bg-rose-500/10 text-rose-400 border border-rose-500/20">TERISI</span>
                    @endif
                </div>

                <button wire:click="toggleStatus({{ $t->id }})" class="w-full py-2 px-3 rounded-xl border border-slate-800 bg-slate-950 hover:bg-slate-800 text-xs font-semibold text-slate-300 transition-colors">
                    Ubah Status Meja
                </button>
            </div>
        @endforeach
    </div>
</div>
