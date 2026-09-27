@if ($paginator->hasPages())
    <div class="px-4 py-3 border-t border-navy-100 bg-navy-50/40 flex flex-col sm:flex-row items-center justify-between gap-3 print:hidden">
        <p class="text-xs text-navy-500">
            Menampilkan
            <span class="font-semibold text-navy-700">{{ $paginator->firstItem() }}</span>–<span class="font-semibold text-navy-700">{{ $paginator->lastItem() }}</span>
            dari <span class="font-semibold text-navy-700">{{ $paginator->total() }}</span> entri
            &middot; Halaman <span class="font-semibold text-navy-700">{{ $paginator->currentPage() }}</span> /
            {{ $paginator->lastPage() }}
        </p>

        <nav class="flex items-center gap-1" aria-label="Navigasi halaman">
            {{-- Sebelumnya --}}
            @if ($paginator->onFirstPage())
                <span class="px-3 py-1.5 rounded-lg text-xs font-bold text-navy-300 opacity-50 select-none">&larr; Sebelumnya</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
                   class="px-3 py-1.5 rounded-lg text-xs font-bold text-navy-600 hover:bg-white hover:text-gold-600 transition-colors">&larr; Sebelumnya</a>
            @endif

            {{-- Nomor halaman --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="px-2 py-1.5 text-xs text-navy-400 select-none">…</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page"
                                  class="px-3 py-1.5 rounded-lg text-xs font-bold bg-navy-700 text-white">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}"
                               class="px-3 py-1.5 rounded-lg text-xs font-bold text-navy-600 hover:bg-white hover:text-gold-600 transition-colors">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Berikutnya --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next"
                   class="px-3 py-1.5 rounded-lg text-xs font-bold text-navy-600 hover:bg-white hover:text-gold-600 transition-colors">Berikutnya &rarr;</a>
            @else
                <span class="px-3 py-1.5 rounded-lg text-xs font-bold text-navy-300 opacity-50 select-none">Berikutnya &rarr;</span>
            @endif
        </nav>
    </div>
@elseif ($paginator->total() > 0)
    <div class="px-4 py-3 border-t border-navy-100 bg-navy-50/40 text-xs text-navy-500 print:hidden">
        Menampilkan
        <span class="font-semibold text-navy-700">{{ $paginator->firstItem() }}</span>–<span class="font-semibold text-navy-700">{{ $paginator->lastItem() }}</span>
        dari <span class="font-semibold text-navy-700">{{ $paginator->total() }}</span> entri.
    </div>
@endif