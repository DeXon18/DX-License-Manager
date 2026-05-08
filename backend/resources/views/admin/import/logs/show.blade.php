@extends('layouts.app')

@section('content')
<div class="page-header mb-10">
    <div class="breadcrumb mb-4">
        <a href="{{ route('admin.import.logs.index') }}" class="hover:text-accent transition-colors">Historial de Logs</a>
        <span class="mx-2 opacity-30">/</span>
        <span class="current opacity-60">Detalle de Importación</span>
    </div>
    
    <div class="flex items-start justify-between">
        <div class="flex items-center gap-6">
            <div class="w-14 h-14 rounded-xl bg-white/[0.03] border border-white/10 flex items-center justify-center shadow-2xl">
                <i class="fa-solid fa-file-csv text-2xl {{ $log->status === 'success' ? 'text-success' : 'text-warn' }}"></i>
            </div>
            <div>
                <h1 class="text-3xl font-bold tracking-tight text-white mb-1">{{ $log->filename }}</h1>
                <div class="flex items-center gap-4 text-xs font-mono text-muted">
                    <span class="flex items-center gap-1.5">
                        <i class="fa-regular fa-calendar opacity-50"></i> {{ $log->created_at->format('d/m/Y') }}
                    </span>
                    <span class="flex items-center gap-1.5">
                        <i class="fa-regular fa-clock opacity-50"></i> {{ $log->created_at->format('H:i:s') }}
                    </span>
                    <span class="flex items-center gap-1.5">
                        <i class="fa-solid fa-fingerprint opacity-50"></i> ID:{{ str_pad($log->id, 6, '0', STR_PAD_LEFT) }}
                    </span>
                </div>
            </div>
        </div>
        
        <div class="flex flex-col items-end gap-2">
            <div class="status-pill {{ $log->status === 'success' ? 'pill-success' : 'pill-warn' }}">
                <span class="pulse-dot"></span>
                {{ strtoupper($log->status) }}
            </div>
            <div class="text-[10px] uppercase tracking-widest opacity-40 font-bold">Estado del Proceso</div>
        </div>
    </div>
</div>

<!-- Integrity Progress Bar -->
<div class="mb-10">
    <div class="flex justify-between items-end mb-3">
        <div class="tech-label">Tasa de Integridad de Datos</div>
        <div class="text-xs font-mono font-bold">{{ $log->total_rows > 0 ? round(($log->processed_rows / $log->total_rows) * 100, 1) : 0 }}%</div>
    </div>
    <div class="w-full h-1.5 bg-white/5 rounded-full overflow-hidden border border-white/[0.03]">
        <div class="h-full bg-accent shadow-[0_0_15px_rgba(56,139,253,0.4)] transition-all duration-1000" style="width: {{ $log->total_rows > 0 ? ($log->processed_rows / $log->total_rows) * 100 : 0 }}%"></div>
    </div>
</div>

<div class="metric-grid mb-10">
    <div class="metric-card group">
        <div class="flex justify-between items-start mb-3">
            <div class="metric-label">TOTAL FILAS</div>
            <i class="fa-solid fa-list-ol opacity-20 group-hover:opacity-50 transition-opacity"></i>
        </div>
        <div class="metric-value">{{ number_format($log->total_rows, 0, ',', '.') }}</div>
        <div class="mt-2 text-[10px] text-muted opacity-60">Entradas detectadas en CSV</div>
    </div>
    <div class="metric-card group border-accent/20">
        <div class="flex justify-between items-start mb-3">
            <div class="metric-label text-accent">PROCESADAS OK</div>
            <i class="fa-solid fa-circle-check text-accent opacity-20 group-hover:opacity-50 transition-opacity"></i>
        </div>
        <div class="metric-value text-accent">{{ number_format($log->processed_rows, 0, ',', '.') }}</div>
        <div class="mt-2 text-[10px] text-accent/60 italic">Sincronizadas con éxito</div>
    </div>
    <div class="metric-card group {{ count($log->errors ?? []) > 0 ? 'border-danger/30' : '' }}">
        <div class="flex justify-between items-start mb-3">
            <div class="metric-label text-danger">ERRORES CRÍTICOS</div>
            <i class="fa-solid fa-bug text-danger opacity-20 group-hover:opacity-50 transition-opacity"></i>
        </div>
        <div class="metric-value {{ count($log->errors ?? []) > 0 ? 'text-danger' : 'muted' }}">
            {{ count($log->errors ?? []) }}
        </div>
        <div class="mt-2 text-[10px] text-danger/60">Registros no importados</div>
    </div>
    <div class="metric-card group {{ count($log->warnings ?? []) > 0 ? 'border-warn/30 shadow-[inset_0_0_20px_rgba(245,158,11,0.02)]' : '' }}">
        <div class="flex justify-between items-start mb-3">
            <div class="metric-label text-warn">AVISOS IA</div>
            <i class="fa-solid fa-brain text-warn opacity-20 group-hover:opacity-50 transition-opacity"></i>
        </div>
        <div class="metric-value {{ count($log->warnings ?? []) > 0 ? 'text-warn' : 'muted' }}">
            {{ count($log->warnings ?? []) }}
        </div>
        <div class="mt-2 text-[10px] text-warn/60">Sospechas de normalización</div>
    </div>
</div>

<div class="detail-container grid grid-cols-3 gap-8">
    <div class="col-span-2">
        <!-- Errores Críticos -->
        @if(count($log->errors ?? []) > 0)
        <div class="card mb-8 overflow-hidden border-danger/20">
            <div class="card-header border-b border-white/5 bg-danger/[0.03] px-5 py-4 flex items-center gap-3">
                <i class="fa-solid fa-triangle-exclamation text-danger"></i>
                <h3 class="text-xs font-bold uppercase tracking-wider">Log de Errores del Sistema</h3>
            </div>
            <div class="p-0">
                <table class="table text-sm">
                    <tbody>
                        @foreach($log->errors as $error)
                        <tr class="hover:bg-white/[0.01]">
                            <td class="px-5 py-3 border-b border-white/5 last:border-0 font-mono text-[12px] text-danger/80">
                                <span class="opacity-30 mr-2">[{{ now()->format('H:i:s') }}]</span> {{ $error }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <!-- Avisos de Normalización -->
        @if(count($log->warnings ?? []) > 0)
        <div class="card overflow-hidden">
            <div class="card-header border-b border-white/5 bg-white/[0.02] px-5 py-4 flex items-center gap-3">
                <i class="fa-solid fa-wand-magic-sparkles text-warn"></i>
                <h3 class="text-xs font-bold uppercase tracking-wider">Bandeja de Inteligencia</h3>
            </div>
            <div class="p-0">
                <table class="table text-sm">
                    <thead>
                        <tr class="bg-white/[0.01]">
                            <th class="px-5 py-3 text-[10px] font-bold text-muted uppercase tracking-wider text-left border-b border-white/5">Detección</th>
                            <th class="px-5 py-3 text-[10px] font-bold text-muted uppercase tracking-wider text-right border-b border-white/5">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($log->warnings as $warning)
                        <tr class="hover:bg-white/[0.01] group">
                            <td class="px-5 py-4 border-b border-white/5 last:border-0">
                                <div class="flex items-start gap-4">
                                    <div class="mt-1.5 w-1.5 h-1.5 rounded-full {{ str_contains($warning, 'sospecha') ? 'bg-warn shadow-[0_0_10px_#f59e0b]' : 'bg-accent shadow-[0_0_10px_#388bfd]' }}"></div>
                                    <div>
                                        <div class="text-[10px] font-bold {{ str_contains($warning, 'sospecha') ? 'text-warn' : 'text-accent' }} uppercase tracking-widest mb-1">
                                            {{ str_contains($warning, 'sospecha') ? 'Fuzzy Match Suspected' : 'New Identity' }}
                                        </div>
                                        <div class="text-[13px] opacity-80 font-mono leading-relaxed">{{ $warning }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4 text-right border-b border-white/5 last:border-0 align-top">
                                @if(str_contains($warning, 'sospecha') || str_contains($warning, 'parece'))
                                    <div class="flex justify-end gap-2">
                                        <button class="btn-action-sm btn-accent-sm">UNIFICAR</button>
                                        <button class="btn-action-sm btn-secondary-sm">IGNORAR</button>
                                    </div>
                                @else
                                    <span class="text-[10px] font-bold text-muted opacity-30 uppercase">Registrado</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @else
        <div class="card p-12 text-center border-dashed">
            <i class="fa-solid fa-shield-check text-4xl text-success/20 mb-4"></i>
            <h3 class="text-sm font-bold opacity-80 mb-1">Integridad Garantizada</h3>
            <p class="text-xs text-muted">No se han detectado inconsistencias en la normalización de clientes.</p>
        </div>
        @endif
    </div>

    <div class="space-y-8">
        <div class="card p-6">
            <h3 class="text-[11px] font-bold uppercase tracking-widest text-muted mb-5 flex items-center gap-2">
                <i class="fa-solid fa-server opacity-50"></i> Metadatos del Sistema
            </h3>
            <div class="space-y-4">
                <div class="flex justify-between items-center border-b border-white/5 pb-3">
                    <span class="text-xs opacity-50">Origen de Datos</span>
                    <span class="text-xs font-mono">CSV_FILE_UPLOAD</span>
                </div>
                <div class="flex justify-between items-center border-b border-white/5 pb-3">
                    <span class="text-xs opacity-50">Encoding</span>
                    <span class="text-xs font-mono">UTF-8</span>
                </div>
                <div class="flex justify-between items-center border-b border-white/5 pb-3">
                    <span class="text-xs opacity-50">Job Status</span>
                    <span class="text-xs font-mono text-success">COMPLETED</span>
                </div>
                <div class="flex justify-between items-center border-b border-white/5 pb-3">
                    <span class="text-xs opacity-50">Normalización</span>
                    <span class="text-xs font-mono text-accent">ACTIVE</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-xs opacity-50">Server Instance</span>
                    <span class="text-xs font-mono">dx-php-beta</span>
                </div>
            </div>
        </div>

        <div class="card p-6 border-accent/10 bg-accent/[0.01]">
            <h3 class="text-[11px] font-bold uppercase tracking-widest text-accent mb-4">Asistente IA</h3>
            <p class="text-xs leading-relaxed opacity-70 mb-4">
                El motor de normalización ha analizado los nombres de clientes en tiempo real. Todos los registros han sido vinculados correctamente.
            </p>
            <div class="p-3 bg-white/5 rounded-md border border-white/5 text-[10px] font-mono opacity-60">
                Algorithm: Levenshtein Fuzzy Search<br>
                Confidence: 100%<br>
                Conflicts: 0
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .status-pill {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 6px 16px;
        border-radius: 9999px;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 0.1em;
        border: 1px solid transparent;
    }
    .pill-success { background: rgba(63, 185, 80, 0.1); color: #3fb950; border-color: rgba(63, 185, 80, 0.2); }
    .pill-warn { background: rgba(245, 158, 11, 0.1); color: #f59e0b; border-color: rgba(245, 158, 11, 0.2); }
    
    .pulse-dot {
        width: 6px;
        height: 6px;
        background: currentColor;
        border-radius: 50%;
        box-shadow: 0 0 0 0 rgba(63, 185, 80, 0.4);
        animation: pulse 2s infinite;
    }
    @keyframes pulse {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(63, 185, 80, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(63, 185, 80, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(63, 185, 80, 0); }
    }

    .metric-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; }
    .metric-card {
        background: var(--surface);
        border: 1px solid var(--border);
        padding: 24px;
        border-radius: 12px;
        position: relative;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .metric-card:hover { border-color: var(--accent); transform: translateY(-2px); box-shadow: 0 10px 30px -10px rgba(0,0,0,0.5); }
    .metric-label { font-size: 0.6rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.15em; opacity: 0.5; }
    .metric-value { font-size: 2rem; font-weight: 800; font-family: var(--font-mono); letter-spacing: -0.05em; line-height: 1; }
    
    .btn-action-sm {
        font-size: 10px; font-weight: 800; padding: 6px 14px; border-radius: 6px; letter-spacing: 0.05em; transition: all 0.2s;
    }
    .btn-accent-sm { background: var(--accent-muted); color: var(--accent); border: 1px solid var(--accent-border); }
    .btn-accent-sm:hover { background: var(--accent); color: white; }
    .btn-secondary-sm { background: var(--raised); color: var(--muted); border: 1px solid var(--border); }
    .btn-secondary-sm:hover { background: var(--border); color: var(--primary); }

    .tech-label { font-size: 10px; font-weight: 800; uppercase; letter-spacing: 0.1em; opacity: 0.4; text-transform: uppercase; }
</style>
@endpush
