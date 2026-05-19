@if ($paginator->hasPages())
    <nav class="dx-v2-pagination-jump">
        <div class="dx-v2-pagination-info">
            Mostrando <span class="dx-v2-pagination-highlight">{{ $paginator->firstItem() }} - {{ $paginator->lastItem() }}</span> de {{ $paginator->total() }}
        </div>

        <div class="dx-v2-pagination-links">
            {{-- Botón Anterior --}}
            @if ($paginator->onFirstPage())
                <span class="dx-v2-pagination-btn-disabled">« Anterior</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="dx-v2-pagination-btn">« Anterior</a>
            @endif

            {{-- Selector de Página --}}
            <div class="dx-v2-pagination-select-wrapper">
                <select 
                    onchange="window.location.href = this.value"
                    class="dx-v2-pagination-select"
                >
                    @for ($i = 1; $i <= $paginator->lastPage(); $i++)
                        <option value="{{ $paginator->url($i) }}" {{ $i == $paginator->currentPage() ? 'selected' : '' }} style="background: #111; color: #fff;">
                            Página {{ $i }} de {{ $paginator->lastPage() }}
                        </option>
                    @endfor
                </select>
                <div class="dx-v2-pagination-select-icon">
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                </div>
            </div>

            {{-- Botón Siguiente --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="dx-v2-pagination-btn">Siguiente »</a>
            @else
                <span class="dx-v2-pagination-btn-disabled">Siguiente »</span>
            @endif
        </div>
    </nav>
@endif
