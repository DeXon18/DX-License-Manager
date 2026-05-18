@extends('layouts.app')

@section('title', 'Detalle de Importación — DX License Manager')

@section('content')
<div class="page-header">
    <div class="breadcrumb dx-v2-import-breadcrumb-row">
        <a href="{{ route('admin.import.logs.index') }}" class="dx-v2-import-breadcrumb-link">Historial de Logs</a>
        <span class="dx-v2-import-breadcrumb-separator">/</span>
        <span class="dx-v2-import-breadcrumb-current">{{ $log->filename }}</span>
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
    <div class="stat-card accent">
        <span class="stat-label">Procesadas OK</span>
        <span class="stat-value dx-v2-import-stat-value-accent">{{ number_format($log->processed_rows) }}</span>
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
            <span class="badge {{ $log->status === 'success' ? 'badge-success' : 'dx-v2-import-badge-warn' }}">
                {{ strtoupper($log->status) }}
            </span>
        </div>
        
        @if(count($log->errors ?? []) > 0 || count($log->warnings ?? []) > 0)
            <table>
                <thead>
                    <tr>
                        <th class="dx-v2-import-table-col-width-80">Nivel</th>
                        <th>Mensaje / Detección</th>
                        <th class="dx-v2-import-table-text-right">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($log->errors ?? [] as $error)
                        <tr>
                            <td><span class="badge badge-danger">ERROR</span></td>
                            <td class="dx-v2-import-table-code-cell danger">
                                {{ $error }}
                            </td>
                            <td class="dx-v2-import-table-text-right"><span class="muted text-xs">DESCARTADO</span></td>
                        </tr>
                    @endforeach

                    @foreach($log->warnings ?? [] as $warning)
                        <tr>
                            <td><span class="badge dx-v2-import-badge-warn">AVISO</span></td>
                            <td class="dx-v2-import-table-code-cell">
                                {{ $warning }}
                            </td>
                            <td class="dx-v2-import-table-text-right">
                                @if(str_contains($warning, 'sospecha'))
                                    <button class="btn-primary sm">UNIFICAR</button>
                                @else
                                    <span class="muted text-xs">OK</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="dx-v2-import-empty-box">
                <div class="dx-v2-import-empty-icon">✅</div>
                <div class="dx-v2-import-empty-title">Integridad de Datos Garantizada</div>
                <div class="dx-v2-import-empty-desc">No se han detectado inconsistencias ni errores en esta importación.</div>
            </div>
        @endif
    </div>

    <div class="card">
        <div class="card-header">
            <span class="card-title">Metadatos del Sistema</span>
        </div>
        <div class="dx-v2-import-metadata-container">
            <div class="dx-v2-import-metadata-col">
                <div class="dx-v2-import-metadata-row">
                    <span class="dx-v2-import-metadata-label">Job ID</span>
                    <span class="dx-v2-import-metadata-value">#{{ str_pad($log->id, 6, '0', STR_PAD_LEFT) }}</span>
                </div>
                <div class="dx-v2-import-metadata-row">
                    <span class="dx-v2-import-metadata-label">Encoding</span>
                    <span class="dx-v2-import-metadata-value">UTF-8 (Auto-detect)</span>
                </div>
                <div class="dx-v2-import-metadata-row">
                    <span class="dx-v2-import-metadata-label">Algoritmo</span>
                    <span class="dx-v2-import-metadata-value">Levenshtein 85% Match</span>
                </div>
                <div class="dx-v2-import-metadata-row">
                    <span class="dx-v2-import-metadata-label">Instancia</span>
                    <span class="dx-v2-import-metadata-value">dx-php-beta</span>
                </div>
                <div class="dx-v2-import-metadata-row no-border">
                    <span class="dx-v2-import-metadata-label">Seguridad</span>
                    <span class="dx-v2-import-metadata-value success">SSL Validated</span>
                </div>
            </div>
            
            <div class="dx-v2-import-ia-box">
                <div class="dx-v2-import-ia-title">Auditoría IA</div>
                <div class="dx-v2-import-ia-text">
                    El motor de normalización ha vinculado automáticamente los clientes basándose en el historial de alias y similitud fonética.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
