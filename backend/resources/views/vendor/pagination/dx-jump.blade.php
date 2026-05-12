@if ($paginator->hasPages())
    <nav class="pagination-jump" style="display: flex; justify-content: space-between; align-items: center; width: 100%; font-family: 'Inter', sans-serif;">
        <div style="font-size: 11px; color: var(--muted); font-weight: 500;">
            Mostrando <span style="color: var(--primary); font-weight: 700;">{{ $paginator->firstItem() }} - {{ $paginator->lastItem() }}</span> de {{ $paginator->total() }}
        </div>

        <div style="display: flex; align-items: center; gap: 10px;">
            {{-- Botón Anterior --}}
            @if ($paginator->onFirstPage())
                <span style="padding: 6px 12px; background: rgba(255,255,255,0.02); border: 1px solid var(--border); border-radius: 6px; color: var(--muted); font-size: 11px; cursor: not-allowed; opacity: 0.5;">« Anterior</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" style="padding: 6px 12px; background: rgba(255,255,255,0.03); border: 1px solid var(--border); border-radius: 6px; color: var(--primary); font-size: 11px; font-weight: 600; text-decoration: none; transition: all 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.08)'" onmouseout="this.style.background='rgba(255,255,255,0.03)'">« Anterior</a>
            @endif

            {{-- Selector de Página --}}
            <div style="position: relative; display: flex; align-items: center;">
                <select 
                    onchange="window.location.href = this.value"
                    style="
                        appearance: none;
                        background: rgba(255,255,255,0.03);
                        border: 1px solid var(--border);
                        border-radius: 6px;
                        padding: 6px 30px 6px 12px;
                        color: var(--primary);
                        font-size: 11px;
                        font-weight: 600;
                        cursor: pointer;
                        font-family: 'Inter', sans-serif;
                        outline: none;
                        transition: all 0.2s;
                    "
                    onmouseover="this.style.border='1px solid var(--accent)'"
                    onmouseout="this.style.border='1px solid var(--border)'"
                >
                    @for ($i = 1; $i <= $paginator->lastPage(); $i++)
                        <option value="{{ $paginator->url($i) }}" {{ $i == $paginator->currentPage() ? 'selected' : '' }}>
                            Página {{ $i }} de {{ $paginator->lastPage() }}
                        </option>
                    @endfor
                </select>
                <div style="position: absolute; right: 10px; pointer-events: none; opacity: 0.5;">
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                </div>
            </div>

            {{-- Botón Siguiente --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" style="padding: 6px 12px; background: rgba(255,255,255,0.03); border: 1px solid var(--border); border-radius: 6px; color: var(--primary); font-size: 11px; font-weight: 600; text-decoration: none; transition: all 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.08)'" onmouseout="this.style.background='rgba(255,255,255,0.03)'">Siguiente »</a>
            @else
                <span style="padding: 6px 12px; background: rgba(255,255,255,0.02); border: 1px solid var(--border); border-radius: 6px; color: var(--muted); font-size: 11px; cursor: not-allowed; opacity: 0.5;">Siguiente »</span>
            @endif
        </div>
    </nav>
@endif
