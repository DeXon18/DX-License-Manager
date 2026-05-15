@extends('layouts.app')

@section('title', 'Detalle de Importación — DX License Manager')

@section('content')
<div class="page-header">
    <div class="breadcrumb" style="margin-bottom: 8px;">
        <a href="{{ route('admin.import.logs.index') }}" style="color: var(--accent); text-decoration: none;">Historial de Logs</a>
        <span style="margin: 0 8px; opacity: 0.3;">/</span>
        <span style="opacity: 0.5;">{{ $log->filename }}</span>
    </div>
    <h1 class="welcome">Detalle de <span>Importación</span></h1>
    <p class="welcome-sub">Archivo: {{ $log->filename }} · Procesado el {{ $log->created_at->format('d/m/Y H:i') }}</p>
</div>

<div class="stats-row">
    <div class="stat-card">
        <span class="stat-label">Total Filas</span>
        <span class="stat-value">{{ number_format($log->total_rows) }}</span>
        <span class="stat-meta">Registros en el CSV</span>
    </div>
    <div class="stat-card" style="border-bottom-color: var(--accent);">
        <span class="stat-label">Procesadas OK</span>
        <span class="stat-value" style="color: var(--accent);">{{ number_format($log->processed_rows) }}</span>
        <span class="stat-meta">Sincronización exitosa</span>
    </div>
    <div class="stat-card {{ count($log->errors ?? []) > 0 ? 'danger' : '' }}">
        <span class="stat-label">Errores Críticos</span>
        <span class="stat-value {{ count($log->errors ?? []) > 0 ? 'danger' : '' }}">{{ count($log->errors ?? []) }}</span>
        <span class="stat-meta">Filas descartadas</span>
    </div>
    <div class="stat-card {{ count($log->warnings ?? []) > 0 ? 'warn' : '' }}">
        <span class="stat-label">Avisos IA</span>
        <span class="stat-value {{ count($log->warnings ?? []) > 0 ? 'warn' : '' }}">{{ count($log->warnings ?? []) }}</span>
        <span class="stat-meta">Sospechas normalización</span>
    </div>
</div>

<div class="grid-2">
    <div class="card">
        <div class="card-header">
            <span class="card-title">Resultado del Análisis</span>
            <span class="badge {{ $log->status === 'success' ? 'badge-success' : 'badge-warn' }}">
                {{ strtoupper($log->status) }}
            </span>
        </div>
        
        @if(count($log->errors ?? []) > 0 || count($log->warnings ?? []) > 0)
            <table>
                <thead>
                    <tr>
                        <th style="width: 80px;">Nivel</th>
                        <th>Mensaje / Detección</th>
                        <th style="text-align: right;">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($log->errors ?? [] as $error)
                        <tr>
                            <td><span class="badge badge-danger">ERROR</span></td>
                            <td style="font-family: 'IBM Plex Mono', monospace; font-size: 12px; color: var(--danger);">
                                {{ $error }}
                            </td>
                            <td style="text-align: right;"><span class="muted" style="font-size: 10px;">DESCARTADO</span></td>
                        </tr>
                    @endforeach

                    @foreach($log->warnings ?? [] as $warning)
                        <tr>
                            <td><span class="badge badge-warn">AVISO</span></td>
                            <td style="font-family: 'IBM Plex Mono', monospace; font-size: 12px;">
                                {{ $warning }}
                            </td>
                            <td style="text-align: right;">
                                @if(str_contains($warning, 'sospecha'))
                                    <button class="btn-primary" style="padding: 4px 8px; font-size: 10px;">UNIFICAR</button>
                                @else
                                    <span class="muted" style="font-size: 10px;">OK</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div style="padding: 40px; text-align: center;">
                <div style="font-size: 32px; margin-bottom: 16px;">✅</div>
                <div style="font-weight: 600; color: var(--primary);">Integridad de Datos Garantizada</div>
                <div style="font-size: 12px; color: var(--muted); margin-top: 4px;">No se han detectado inconsistencias ni errores en esta importación.</div>
            </div>
        @endif
    </div>

    <div class="card">
        <div class="card-header">
            <span class="card-title">Metadatos del Sistema</span>
        </div>
        <div style="padding: 20px;">
            <div style="display: flex; flex-direction: column; gap: 12px;">
                <div style="display: flex; justify-content: space-between; border-bottom: 1px solid var(--border-subtle); padding-bottom: 8px;">
                    <span style="font-size: 12px; color: var(--muted);">Job ID</span>
                    <span style="font-family: 'IBM Plex Mono', monospace; font-size: 12px;">#{{ str_pad($log->id, 6, '0', STR_PAD_LEFT) }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; border-bottom: 1px solid var(--border-subtle); padding-bottom: 8px;">
                    <span style="font-size: 12px; color: var(--muted);">Encoding</span>
                    <span style="font-family: 'IBM Plex Mono', monospace; font-size: 12px;">UTF-8 (Auto-detect)</span>
                </div>
                <div style="display: flex; justify-content: space-between; border-bottom: 1px solid var(--border-subtle); padding-bottom: 8px;">
                    <span style="font-size: 12px; color: var(--muted);">Algoritmo</span>
                    <span style="font-family: 'IBM Plex Mono', monospace; font-size: 12px;">Levenshtein 85% Match</span>
                </div>
                <div style="display: flex; justify-content: space-between; border-bottom: 1px solid var(--border-subtle); padding-bottom: 8px;">
                    <span style="font-size: 12px; color: var(--muted);">Instancia</span>
                    <span style="font-family: 'IBM Plex Mono', monospace; font-size: 12px;">dx-php-beta</span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="font-size: 12px; color: var(--muted);">Seguridad</span>
                    <span style="font-family: 'IBM Plex Mono', monospace; font-size: 12px; color: var(--success);">SSL Validated</span>
                </div>
            </div>
            
            <div style="margin-top: 24px; padding: 12px; background: var(--accent-muted); border-radius: 4px; border: 1px solid var(--accent-border);">
                <div style="font-size: 11px; font-weight: 700; color: var(--accent); text-transform: uppercase; margin-bottom: 4px;">Auditoría IA</div>
                <div style="font-size: 11px; color: var(--secondary); line-height: 1.4;">
                    El motor de normalización ha vinculado automáticamente los clientes basándose en el historial de alias y similitud fonética.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
