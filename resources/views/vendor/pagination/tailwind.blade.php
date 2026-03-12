@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination" class="store-pagination">
        {{-- Previous --}}
        @if ($paginator->onFirstPage())
            <span class="store-pagination__arrow store-pagination__arrow--disabled" aria-disabled="true">
                <svg width="8" height="14" viewBox="0 0 8 14" fill="none"><path d="M7 1L1 7l6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="store-pagination__arrow" rel="prev">
                <svg width="8" height="14" viewBox="0 0 8 14" fill="none"><path d="M7 1L1 7l6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </a>
        @endif

        {{-- Pages --}}
        <div class="store-pagination__pages">
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="store-pagination__dots">&hellip;</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="store-pagination__page store-pagination__page--active">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="store-pagination__page">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach
        </div>

        {{-- Next --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="store-pagination__arrow" rel="next">
                <svg width="8" height="14" viewBox="0 0 8 14" fill="none"><path d="M1 1l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </a>
        @else
            <span class="store-pagination__arrow store-pagination__arrow--disabled" aria-disabled="true">
                <svg width="8" height="14" viewBox="0 0 8 14" fill="none"><path d="M1 1l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </span>
        @endif
    </nav>
@endif
