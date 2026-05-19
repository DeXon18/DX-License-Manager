@if ($paginator->hasPages())
    <nav class="dx-v2-pagination">
        <div class="dx-v2-pagination-info">
            Mostrando <span class="dx-v2-pagination-highlight">{{ $paginator->firstItem() }} - {{ $paginator->lastItem() }}</span> de {{ $paginator->total() }} registros
        </div>

        <div class="dx-v2-pagination-links">
            {{-- Anterior --}}
            @if ($paginator->onFirstPage())
                <span class="dx-v2-page-link dx-v2-page-link-disabled">«</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="dx-v2-page-link dx-v2-page-control" title="Anterior">«</a>
            @endif

            {{-- Números --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="dx-v2-page-link-dots">{{ $element }}</span>
                @endif

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

            {{-- Siguiente --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="dx-v2-page-link dx-v2-page-control" title="Siguiente">»</a>
            @else
                <span class="dx-v2-page-link dx-v2-page-link-disabled">»</span>
            @endif
        </div>
    </nav>
@endif
