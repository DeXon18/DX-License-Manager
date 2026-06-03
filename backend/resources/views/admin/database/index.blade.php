@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/modules/dx-v2-sys-dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/modules/dx-v2-db-monitor.css') }}">
@endpush

@section('content')
<div class="dx-v2-page-header" x-data="dbMonitor()">
    <div>
        <div class="breadcrumb">
            <a href="{{ route('admin.system.index') }}">Infraestructura</a>
            <span class="separator">/</span>
            <span class="current">Database Monitor</span>
        </div>
        <h1 class="page-title">Database Monitor <span>System</span></h1>
        <p class="page-subtitle">Auditoría en tiempo real de MariaDB - Entorno: <span class="text-white">{{ $dbName }}</span></p>
    </div>
    <div class="dx-v2-page-header-actions">
        <button @click="refreshPage()" class="dx-v2-btn-primary" style="display: flex; align-items: center; gap: 8px;">
            <i class="fas fa-sync-alt" :class="{'fa-spin': isRefreshing}"></i> Refrescar
        </button>
    </div>
</div>

<div class="dx-v2-db-monitor-container" x-data="dbMonitor()">
    <!-- METRICS GRID -->
    <div class="dx-v2-sys-dash-stats-grid">
        <!-- Tamaño Total -->
        <div class="dx-v2-sys-dash-stat-card">
            <div class="dx-v2-sys-dash-stat-card-watermark">
                <svg width="84" height="84" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M4 6c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2s-.9-2-2-2H6c-1.1 0-2 .9-2 2zM4 12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2s-.9-2-2-2H6c-1.1 0-2 .9-2 2zM4 18c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2s-.9-2-2-2H6c-1.1 0-2 .9-2 2z"/></svg>
            </div>
            <div class="dx-v2-sys-dash-stat-card-title">
                TAMAÑO TOTAL
            </div>
            <div class="dx-v2-sys-dash-stat-card-value">
                {{ number_format($totalSizeMb, 2) }} <span class="percent-unit">MB</span>
            </div>
            <div class="dx-v2-sys-dash-stat-card-meta-mono">
                Espacio en disco (Datos + Índices)
            </div>
        </div>

        <!-- Conexiones Activas -->
        <div class="dx-v2-sys-dash-stat-card">
            <div class="dx-v2-sys-dash-stat-card-watermark">
                <svg width="84" height="84" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 20V10M18 20V4M6 20v-4"/></svg>
            </div>
            <div class="dx-v2-sys-dash-stat-card-title">
                CONEXIONES ACTIVAS
            </div>
            <div class="dx-v2-sys-dash-stat-card-value {{ ($status['Threads_connected'] ?? 0) > 50 ? 'danger-status' : 'success-status' }}">
                {{ $status['Threads_connected'] ?? '0' }}
            </div>
            <div class="dx-v2-sys-dash-stat-card-meta-mono">
                Hilos conectados actualmente
            </div>
        </div>

        <!-- Tablas Totales -->
        <div class="dx-v2-sys-dash-stat-card">
            <div class="dx-v2-sys-dash-stat-card-watermark">
                <svg width="84" height="84" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="3" y1="15" x2="21" y2="15"/><line x1="9" y1="3" x2="9" y2="21"/><line x1="15" y1="3" x2="15" y2="21"/></svg>
            </div>
            <div class="dx-v2-sys-dash-stat-card-title">
                TOTAL TABLAS
            </div>
            <div class="dx-v2-sys-dash-stat-card-value">
                {{ count($tables) }}
            </div>
            <div class="dx-v2-sys-dash-stat-card-meta-mono">
                En el esquema actual
            </div>
        </div>

        <!-- Versión -->
        <div class="dx-v2-sys-dash-stat-card">
            <div class="dx-v2-sys-dash-stat-card-watermark">
                <svg width="84" height="84" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
            </div>
            <div class="dx-v2-sys-dash-stat-card-title">
                VERSIÓN MARIADB
            </div>
            <div class="dx-v2-sys-dash-stat-card-value" style="font-size: 1.5rem;">
                {{ Str::limit($version, 15) }}
            </div>
            <div class="dx-v2-sys-dash-stat-card-meta-mono" title="{{ $version }}">
                Motor de base de datos
            </div>
        </div>
    </div>

    <!-- MAIN TABLE SECTION -->
    <div class="dx-v2-db-table-container">
        <div class="dx-v2-db-table-header">
            <div class="dx-v2-db-table-title">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                Detalle de Tablas
            </div>
            <div class="dx-v2-sys-dash-stat-card-meta-mono" style="opacity: 0.7;">
                <span class="dx-v2-sys-dash-stat-card-traffic-live-dot"></span> Uptime: {{ number_format(($status['Uptime'] ?? 0) / 3600, 1) }}h
            </div>
        </div>
        
        @if(count($tables) > 0)
        <table class="dx-v2-db-table">
            <thead>
                <tr>
                    <th>Nombre de Tabla</th>
                    <th>Filas (Aprox)</th>
                    <th>Tamaño (MB)</th>
                    <th>Proporción</th>
                    <th>Fecha de Creación</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tables as $table)
                <tr>
                    <td style="font-weight: 500; display: flex; align-items: center; gap: 8px;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="opacity: 0.5"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/></svg>
                        {{ $table->name }}
                    </td>
                    <td class="dx-v2-mono" style="opacity: 0.8;">
                        {{ number_format($table->rows) }}
                    </td>
                    <td class="dx-v2-mono" style="opacity: 0.8;">
                        {{ number_format($table->size_mb, 2) }}
                    </td>
                    <td>
                        <div class="dx-v2-db-size-bar-wrapper">
                            <div class="dx-v2-db-size-bar-bg">
                                <div class="dx-v2-db-size-bar-fill" 
                                     style="width: {{ $totalSizeMb > 0 ? min(100, ($table->size_mb / $totalSizeMb) * 100) : 0 }}%">
                                </div>
                            </div>
                            <span class="dx-v2-mono" style="opacity: 0.6;">
                                {{ $totalSizeMb > 0 ? number_format(($table->size_mb / $totalSizeMb) * 100, 1) : 0 }}%
                            </span>
                        </div>
                    </td>
                    <td style="opacity: 0.6; font-size: 12px;">
                        {{ \Carbon\Carbon::parse($table->created_at)->format('Y-m-d H:i') }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="dx-v2-db-empty">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin: 0 auto 12px; opacity: 0.4;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
            <p>No se encontraron tablas en la base de datos.</p>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('dbMonitor', () => ({
            isRefreshing: false,
            
            refreshPage() {
                this.isRefreshing = true;
                window.location.reload();
            }
        }));
    });
</script>
@endpush
@endsection
