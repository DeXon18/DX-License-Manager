@if ($paginator->hasPages())
    <nav class="pagination" style="display: flex; justify-content: space-between; align-items: center; margin-top: 20px;">
        <div class="pagination-info" style="font-size: 11px; color: var(--muted); font-weight: 500;">
            Mostrando <span style="color: var(--primary);">{{ $paginator->firstItem() }} - {{ $paginator->lastItem() }}</span> de {{ $paginator->total() }}
        </div>

        <div class="pagination-links" style="display: flex; gap: 8px;">
            @if ($paginator->onFirstPage())
                <span class="page-link disabled" style="padding: 6px 14px; background: rgba(255,255,255,0.02); border: 1px solid var(--border); border-radius: 6px; color: var(--muted); font-size: 11px; cursor: not-allowed; opacity: 0.5;">« Anterior</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="page-link" style="padding: 6px 14px; background: rgba(255,255,255,0.03); border: 1px solid var(--border); border-radius: 6px; color: var(--primary); font-size: 11px; font-weight: 600; text-decoration: none; transition: all 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.08)'" onmouseout="this.style.background='rgba(255,255,255,0.03)'">« Anterior</a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="page-link" style="padding: 6px 14px; background: rgba(255,255,255,0.03); border: 1px solid var(--border); border-radius: 6px; color: var(--primary); font-size: 11px; font-weight: 600; text-decoration: none; transition: all 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.08)'" onmouseout="this.style.background='rgba(255,255,255,0.03)'">Siguiente »</a>
            @else
                <span class="page-link disabled" style="padding: 6px 14px; background: rgba(255,255,255,0.02); border: 1px solid var(--border); border-radius: 6px; color: var(--muted); font-size: 11px; cursor: not-allowed; opacity: 0.5;">Siguiente »</span>
            @endif
        </div>
    </nav>
@endif
