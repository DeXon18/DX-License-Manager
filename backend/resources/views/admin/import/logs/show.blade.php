@extends('layouts.app')

@section('content')
<div class="page-header">
    <div class="breadcrumb">
        <a href="{{ route('admin.import.logs.index') }}">Historial de Logs</a>
        <span class="muted">/</span>
        <span class="current">Detalle de Importación</span>
    </div>
    <div class="flex justify-between items-center">
        <div>
            <h1 class="page-title">{{ $log->filename }}</h1>
            <p class="page-sub">Importado el {{ $log->created_at->format('d/m/Y \a \l\a\s H:i') }}</p>
        </div>
        <div class="flex gap-3">
            <span class="badge {{ $log->status === 'success' ? 'badge-success' : ($log->status === 'partial' ? 'badge-warn' : 'badge-danger') }}">
                {{ strtoupper($log->status) }}
            </span>
        </div>
    </div>
</div>

<div class="metric-grid mb-8">
    <div class="metric-card">
        <div class="metric-label">TOTAL FILAS</div>
        <div class="metric-value">{{ $log->total_rows }}</div>
    </div>
    <div class="metric-card">
        <div class="metric-label">PROCESADAS OK</div>
        <div class="metric-value text-accent">{{ $log->processed_rows }}</div>
    </div>
    <div class="metric-card">
        <div class="metric-label text-danger">ERRORES CRÍTICOS</div>
        <div class="metric-value {{ count($log->errors ?? []) > 0 ? 'text-danger' : 'muted' }}">
            {{ count($log->errors ?? []) }}
        </div>
    </div>
    <div class="metric-card">
        <div class="metric-label text-warn">AVISOS NORMALIZACIÓN</div>
        <div class="metric-value {{ count($log->warnings ?? []) > 0 ? 'text-warn' : 'muted' }}">
            {{ count($log->warnings ?? []) }}
        </div>
    </div>
</div>

<div class="detail-container">
    <!-- Errores Críticos -->
    @if(count($log->errors ?? []) > 0)
    <div class="card mb-8">
        <div class="card-header border-b border-white/5 bg-white/[0.02] px-5 py-4 flex items-center gap-3">
            <div class="p-2 rounded-md bg-danger/10 text-danger text-xs">
                <i class="fa-solid fa-circle-xmark"></i>
            </div>
            <h3 class="text-xs font-bold uppercase tracking-wider">Errores Críticos (No procesados)</h3>
        </div>
        <div class="p-0 overflow-hidden">
            <table class="table text-sm">
                <tbody>
                    @foreach($log->errors as $error)
                    <tr class="hover:bg-white/[0.01]">
                        <td class="px-5 py-3 border-b border-white/5 last:border-0">
                            <div class="flex items-center gap-4">
                                <span class="font-mono text-[10px] text-muted opacity-50 whitespace-nowrap">
                                    [ENTRY_FAIL]
                                </span>
                                <span class="text-danger/90 font-mono text-[13px]">
                                    {{ $error }}
                                </span>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Avisos de Normalización (Sospechas) -->
    @if(count($log->warnings ?? []) > 0)
    <div class="card">
        <div class="card-header border-b border-white/5 bg-white/[0.02] px-5 py-4 flex items-center gap-3">
            <div class="p-2 rounded-md bg-warn/10 text-warn text-xs">
                <i class="fa-solid fa-wand-magic-sparkles"></i>
            </div>
            <h3 class="text-xs font-bold uppercase tracking-wider">Avisos de Inteligencia y Normalización</h3>
        </div>
        <div class="p-0">
            <table class="table text-sm">
                <thead>
                    <tr class="bg-white/[0.01]">
                        <th class="px-5 py-2 text-[10px] font-bold text-muted uppercase tracking-wider text-left border-b border-white/5">Mensaje del Sistema</th>
                        <th class="px-5 py-2 text-[10px] font-bold text-muted uppercase tracking-wider text-right border-b border-white/5">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($log->warnings as $warning)
                    <tr class="hover:bg-white/[0.01] group">
                        <td class="px-5 py-4 border-b border-white/5 last:border-0">
                            @if(str_contains($warning, 'sospecha') || str_contains($warning, 'parece'))
                                <div class="flex items-start gap-4">
                                    <div class="mt-1 w-1.5 h-1.5 rounded-full bg-warn"></div>
                                    <div>
                                        <div class="text-[11px] font-bold text-warn uppercase tracking-tight mb-1">Sospecha de Duplicado</div>
                                        <div class="text-[13px] opacity-70 font-mono leading-relaxed">{{ $warning }}</div>
                                    </div>
                                </div>
                            @else
                                <div class="flex items-start gap-4">
                                    <div class="mt-1 w-1.5 h-1.5 rounded-full bg-accent"></div>
                                    <div>
                                        <div class="text-[11px] font-bold text-accent uppercase tracking-tight mb-1">Nuevo Registro</div>
                                        <div class="text-[13px] opacity-70 font-mono leading-relaxed">{{ $warning }}</div>
                                    </div>
                                </div>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-right border-b border-white/5 last:border-0 align-top">
                            @if(str_contains($warning, 'sospecha') || str_contains($warning, 'parece'))
                                <div class="flex justify-end gap-2">
                                    <button class="btn-action-sm btn-accent-sm" onclick="alert('Resolución automática en desarrollo')">
                                        UNIFICAR
                                    </button>
                                    <button class="btn-action-sm btn-secondary-sm">
                                        IGNORAR
                                    </button>
                                </div>
                            @else
                                <span class="text-[10px] font-bold text-muted opacity-30 uppercase">Auto-OK</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection

@push('styles')
<style>
    .metric-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 12px;
    }
    .metric-card {
        background: var(--surface);
        border: 1px solid var(--border);
        padding: 16px 20px;
        border-radius: 10px;
        display: flex;
        flex-direction: column;
        gap: 4px;
    }
    .metric-label {
        font-size: 0.65rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: var(--muted);
    }
    .metric-value {
        font-size: 1.602rem;
        font-weight: 700;
        font-family: var(--font-mono);
        letter-spacing: -0.03em;
        color: var(--primary);
    }
    
    .text-warn { color: var(--warning); }
    .bg-warn { background-color: var(--warning); }
    
    .btn-action-sm {
        font-size: 10px;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 4px;
        letter-spacing: 0.02em;
        transition: all 0.2s;
    }
    .btn-accent-sm {
        background: var(--accent-muted);
        color: var(--accent);
        border: 1px solid var(--accent-border);
    }
    .btn-accent-sm:hover {
        background: var(--accent);
        color: white;
    }
    .btn-secondary-sm {
        background: var(--raised);
        color: var(--muted);
        border: 1px solid var(--border);
    }
    .btn-secondary-sm:hover {
        background: var(--border);
        color: var(--primary);
    }
</style>
@endpush
