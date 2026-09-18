<div class="p-6 space-y-6 overflow-y-auto h-full bg-slate-50">
    <div>
        <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Manajemen Meja Dine-In</h2>
        <p class="text-xs text-slate-500 mt-1">Monitoring ketersediaan meja cafe secara real-time</p>
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
                    @else
                        <span class="px-3 py-1 rounded-full text-[10px] font-extrabold bg-rose-50 text-rose-700 border border-rose-200">TERISI</span>
                    @endif
                </div>

                <button wire:click="toggleStatus({{ $t->id }})" class="w-full py-2 px-3 rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100 text-xs font-bold text-slate-700 transition-colors">
                    Ubah Status Meja
                </button>
            </div>
        @endforeach
    </div>
</div>
