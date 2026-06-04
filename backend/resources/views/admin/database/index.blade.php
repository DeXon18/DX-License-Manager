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
        <button @click="refreshPage()" class="btn-primary sm dx-v2-db-btn-refresh">
            <i class="fas fa-sync-alt" :class="{'fa-spin': isRefreshing}"></i> Refrescar
        </button>
    </div>
</div>

<div class="dx-v2-db-monitor-container" x-data="dbMonitor()">
    <!-- METRICS GRID -->
    <div class="stats-row">
        <!-- Tamaño Total -->
        <div class="stat-card accent dx-v2-db-stat-card-centered">
            <div class="stat-label">TAMAÑO TOTAL</div>
            <div class="stat-value accent">
                {{ number_format($totalSizeMb, 2) }}<span style="font-size: 1.2rem; margin-left: 4px;">MB</span>
            </div>
            <div class="stat-meta">Espacio en disco</div>
            <i class="fa-solid fa-database dx-v2-dashboard-stat-icon"></i>
        </div>

        <!-- Conexiones Activas -->
        <div class="stat-card {{ ($status['Threads_connected'] ?? 0) > 50 ? 'danger' : 'success' }} dx-v2-db-stat-card-centered">
            <div class="stat-label">CONEXIONES ACTIVAS</div>
            <div class="stat-value {{ ($status['Threads_connected'] ?? 0) > 50 ? 'danger' : 'success' }}">
                {{ $status['Threads_connected'] ?? '0' }}
            </div>
            <div class="stat-meta">Hilos conectados actualmente</div>
            <i class="fa-solid fa-network-wired dx-v2-dashboard-stat-icon"></i>
        </div>

        <!-- Tablas Totales -->
        <div class="stat-card warn dx-v2-db-stat-card-centered">
            <div class="stat-label">TOTAL TABLAS</div>
            <div class="stat-value warn">
                {{ count($tables) }}
            </div>
            <div class="stat-meta">En el esquema actual</div>
            <i class="fa-solid fa-table-list dx-v2-dashboard-stat-icon"></i>
        </div>

        <!-- Versión -->
        <div class="stat-card success dx-v2-db-stat-card-centered">
            <div class="stat-label">VERSIÓN MARIADB</div>
            <div class="stat-value success" style="font-size: 1.6rem; letter-spacing: -0.5px;">
                {{ explode('-', $version)[0] }}
            </div>
            <div class="stat-meta" title="{{ $version }}">Motor de base de datos</div>
            <i class="fa-solid fa-server dx-v2-dashboard-stat-icon"></i>
        </div>
    </div>

    <!-- MAIN TABLE SECTION -->
    <div class="card">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
            <span style="font-family: var(--font-mono, monospace); font-weight: 600; text-transform: uppercase; color: var(--dx-v2-muted); display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-table-list"></i> Detalle de Tablas
            </span>
            <div class="dx-v2-sys-docker-env-badge">
                <div class="dx-v2-sys-docker-dot-live"></div>
                <span class="dx-v2-sys-docker-env-text">UPTIME: {{ number_format(($status['Uptime'] ?? 0) / 3600, 1) }}h</span>
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
                    <td class="dx-v2-db-table-name-col">
                        <svg class="dx-v2-db-icon-muted" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/></svg>
                        {{ $table->name }}
                    </td>
                    <td class="dx-v2-mono dx-v2-db-meta-text-lg">
                        {{ number_format($table->rows) }}
                    </td>
                    <td class="dx-v2-mono dx-v2-db-meta-text-lg">
                        {{ number_format($table->size_mb, 2) }}
                    </td>
                    <td>
                        <div class="dx-v2-db-size-bar-wrapper">
                            <div class="dx-v2-db-size-bar-bg">
                                <div class="dx-v2-db-size-bar-fill" 
                                     style="width: {{ $totalSizeMb > 0 ? min(100, ($table->size_mb / $totalSizeMb) * 100) : 0 }}%">
                                </div>
                            </div>
                            <span class="dx-v2-mono dx-v2-db-meta-text-md">
                                {{ $totalSizeMb > 0 ? number_format(($table->size_mb / $totalSizeMb) * 100, 1) : 0 }}%
                            </span>
                        </div>
                    </td>
                    <td class="dx-v2-db-meta-text-md">
                        {{ \Carbon\Carbon::parse($table->created_at)->format('Y-m-d H:i') }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="dx-v2-db-empty">
            <svg class="dx-v2-db-empty-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
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
