@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination" class="flex items-center justify-between">
        @if ($paginator->onFirstPage())
            <span class="btn btn-outline btn-sm opacity-40">{{ __('Previous') }}</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="btn btn-outline btn-sm">{{ __('Previous') }}</a>
        @endif
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="btn btn-outline btn-sm">{{ __('Next') }}</a>
        @else
            <span class="btn btn-outline btn-sm opacity-40">{{ __('Next') }}</span>
        @endif
    </nav>
@endif
