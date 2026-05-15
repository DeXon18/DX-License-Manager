@if ($paginator->hasPages())
    <nav class="pagination" style="display: flex; justify-content: space-between; align-items: center; margin-top: 20px; font-family: 'Inter', sans-serif;">
        <div class="pagination-info" style="font-size: 11px; color: var(--muted); font-weight: 500;">
            Mostrando <span style="color: var(--primary); font-weight: 700;">{{ $paginator->firstItem() }} - {{ $paginator->lastItem() }}</span> de {{ $paginator->total() }} registros
        </div>

        <div class="pagination-links" style="display: flex; gap: 4px; align-items: center;">
            {{-- Anterior --}}
            @if ($paginator->onFirstPage())
                <span style="padding: 4px 8px; color: var(--muted); opacity: 0.3; font-size: 14px; cursor: not-allowed;">«</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" style="padding: 4px 8px; color: var(--primary); text-decoration: none; font-size: 14px; transition: all 0.2s;" onmouseover="this.style.color='var(--accent)'" onmouseout="this.style.color='var(--primary)'" title="Anterior">«</a>
            @endif

            {{-- Números --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span style="padding: 0 4px; color: var(--muted); font-size: 11px; opacity: 0.5;">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span style="min-width: 26px; height: 26px; display: flex; align-items: center; justify-content: center; background: var(--accent); color: white; border-radius: 6px; font-size: 11px; font-weight: 800; box-shadow: 0 2px 6px rgba(0,0,0,0.2);">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" style="min-width: 26px; height: 26px; display: flex; align-items: center; justify-content: center; color: var(--muted); text-decoration: none; font-size: 11px; border-radius: 6px; transition: all 0.2s; font-weight: 500;" onmouseover="this.style.background='rgba(255,255,255,0.05)'; this.style.color='var(--primary)'" onmouseout="this.style.background='transparent'; this.style.color='var(--muted)'">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Siguiente --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" style="padding: 4px 8px; color: var(--primary); text-decoration: none; font-size: 14px; transition: all 0.2s;" onmouseover="this.style.color='var(--accent)'" onmouseout="this.style.color='var(--primary)'" title="Siguiente">»</a>
            @else
                <span style="padding: 4px 8px; color: var(--muted); opacity: 0.3; font-size: 14px; cursor: not-allowed;">»</span>
            @endif
        </div>
    </nav>
@endif
