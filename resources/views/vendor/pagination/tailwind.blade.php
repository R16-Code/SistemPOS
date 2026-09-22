<div>
    @if ($paginator->hasPages())
        <nav role="navigation" aria-label="Navigasi Halaman" class="flex items-center justify-between gap-4 flex-wrap">
            {{-- Mobile Pagination --}}
            <div class="flex justify-between flex-1 sm:hidden items-center gap-2">
                @if ($paginator->onFirstPage())
                    <span class="inline-flex items-center px-3.5 py-1.5 text-xs font-bold text-slate-400 bg-slate-100 border border-slate-200 rounded-xl cursor-not-allowed">
                        &laquo; Sebelumnya
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" class="inline-flex items-center px-3.5 py-1.5 text-xs font-bold text-slate-700 bg-white border border-slate-200/90 rounded-xl hover:bg-amber-50 hover:text-amber-900 transition-colors shadow-2xs">
                        &laquo; Sebelumnya
                    </a>
                @endif

                <span class="text-xs font-semibold text-slate-500">
                    Hal. {{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}
                </span>

                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" class="inline-flex items-center px-3.5 py-1.5 text-xs font-bold text-slate-700 bg-white border border-slate-200/90 rounded-xl hover:bg-amber-50 hover:text-amber-900 transition-colors shadow-2xs">
                        Berikutnya &raquo;
                    </a>
                @else
                    <span class="inline-flex items-center px-3.5 py-1.5 text-xs font-bold text-slate-400 bg-slate-100 border border-slate-200 rounded-xl cursor-not-allowed">
                        Berikutnya &raquo;
                    </span>
                @endif
            </div>

            {{-- Desktop Pagination --}}
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between gap-4">
                <div>
                    <p class="text-xs text-slate-500 font-medium">
                        <span>Menampilkan</span>
                        <strong class="font-bold text-slate-800">{{ $paginator->firstItem() ?? 0 }}</strong>
                        <span>sampai</span>
                        <strong class="font-bold text-slate-800">{{ $paginator->lastItem() ?? 0 }}</strong>
                        <span>dari</span>
                        <strong class="font-bold text-slate-800">{{ $paginator->total() }}</strong>
                        <span>data</span>
                    </p>
                </div>

                <div>
                    <div class="inline-flex items-center gap-1 bg-white border border-slate-200/80 rounded-xl p-1 shadow-2xs">
                        {{-- Previous Page Link --}}
                        @if ($paginator->onFirstPage())
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-slate-300 cursor-not-allowed" aria-disabled="true" aria-label="Sebelumnya">
                                <svg class="w-4 h-4 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                                </svg>
                            </span>
                        @else
                            <a href="{{ $paginator->previousPageUrl() }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-slate-600 hover:text-amber-900 hover:bg-amber-50 transition-colors" aria-label="Sebelumnya">
                                <svg class="w-4 h-4 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                                </svg>
                            </a>
                        @endif

                        {{-- Pagination Elements --}}
                        @foreach ($elements as $element)
                            @if (is_string($element))
                                <span class="inline-flex items-center justify-center w-8 h-8 text-xs font-bold text-slate-400">
                                    {{ $element }}
                                </span>
                            @endif

                            @if (is_array($element))
                                @foreach ($element as $page => $url)
                                    @if ($page == $paginator->currentPage())
                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-xs font-black bg-amber-800 text-white shadow-xs" aria-current="page">
                                            {{ $page }}
                                        </span>
                                    @else
                                        <a href="{{ $url }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-xs font-bold text-slate-700 hover:text-amber-900 hover:bg-amber-50 transition-colors" aria-label="Ke halaman {{ $page }}">
                                            {{ $page }}
                                        </a>
                                    @endif
                                @endforeach
                            @endif
                        @endforeach

                        {{-- Next Page Link --}}
                        @if ($paginator->hasMorePages())
                            <a href="{{ $paginator->nextPageUrl() }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-slate-600 hover:text-amber-900 hover:bg-amber-50 transition-colors" aria-label="Berikutnya">
                                <svg class="w-4 h-4 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        @else
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-slate-300 cursor-not-allowed" aria-disabled="true" aria-label="Berikutnya">
                                <svg class="w-4 h-4 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                </svg>
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </nav>
    @endif
</div>
