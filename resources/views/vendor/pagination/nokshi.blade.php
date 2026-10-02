@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination" class="flex items-center justify-center gap-1.5">
        @if ($paginator->onFirstPage())
            <span class="flex h-11 w-11 items-center justify-center rounded-full text-ink-300/60"><x-icon name="chevron-left" class="h-5 w-5" /></span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="flex h-11 w-11 items-center justify-center rounded-full border border-ink/10 bg-white text-ink transition hover:bg-ink hover:text-paper"><x-icon name="chevron-left" class="h-5 w-5" /></a>
        @endif

        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="px-2 text-ink-300">…</span>
            @endif
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span aria-current="page" class="flex h-11 w-11 items-center justify-center rounded-full bg-plum-950 text-sm font-bold text-marigold-400">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="flex h-11 w-11 items-center justify-center rounded-full border border-ink/10 bg-white text-sm font-semibold text-ink transition hover:bg-ink hover:text-paper">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="flex h-11 w-11 items-center justify-center rounded-full border border-ink/10 bg-white text-ink transition hover:bg-ink hover:text-paper"><x-icon name="chevron-right" class="h-5 w-5" /></a>
        @else
            <span class="flex h-11 w-11 items-center justify-center rounded-full text-ink-300/60"><x-icon name="chevron-right" class="h-5 w-5" /></span>
        @endif
    </nav>
@endif
