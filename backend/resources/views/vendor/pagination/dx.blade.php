@if ($paginator->hasPages())
    <nav class="dx-v2-pagination">
        <div class="dx-v2-pagination-info">
            Mostrando <span class="dx-v2-pagination-highlight">{{ $paginator->firstItem() }} - {{ $paginator->lastItem() }}</span> de {{ $paginator->total() }} registros
        </div>

        <div class="dx-v2-pagination-links">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span class="dx-v2-page-link dx-v2-page-link-disabled">«</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="dx-v2-page-link dx-v2-page-control" rel="prev">«</a>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <span class="dx-v2-page-link-dots">{{ $element }}</span>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="dx-v2-page-link-active">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="dx-v2-page-link">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="dx-v2-page-link dx-v2-page-control" rel="next">»</a>
            @else
                <span class="dx-v2-page-link dx-v2-page-link-disabled">»</span>
            @endif
        </div>
    </nav>
@endif
