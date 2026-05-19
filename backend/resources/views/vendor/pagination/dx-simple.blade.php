@if ($paginator->hasPages())
    <nav class="dx-v2-pagination">
        <div class="dx-v2-pagination-info">
            Mostrando <span class="dx-v2-pagination-highlight">{{ $paginator->firstItem() }} - {{ $paginator->lastItem() }}</span> de {{ $paginator->total() }}
        </div>

        <div class="dx-v2-pagination-links">
            @if ($paginator->onFirstPage())
                <span class="dx-v2-pagination-btn-disabled">« Anterior</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="dx-v2-pagination-btn">« Anterior</a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="dx-v2-pagination-btn">Siguiente »</a>
            @else
                <span class="dx-v2-pagination-btn-disabled">Siguiente »</span>
            @endif
        </div>
    </nav>
@endif
