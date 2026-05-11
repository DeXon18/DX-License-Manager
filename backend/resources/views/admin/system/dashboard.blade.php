@extends('layouts.app')

@section('title', 'Dashboard del Sistema')

@section('content')
<div class="system-container">
    <div class="page-header">
        <div class="header-content">
            <h1 class="page-title">Salud del Sistema</h1>
            <p class="page-sub">Monitoreo en tiempo real de infraestructura y servicios críticos.</p>
        </div>
        <div class="header-actions">
            <div class="tech-badge">
                <i class="fa-solid fa-microchip"></i>
                LXC-600 / DX-PORTAL
            </div>
        </div>
    </div>

    <div class="bento-grid-system">
        <!-- Tarjeta: Hardware Overview -->
        <div class="bento-card hardware-card">
            <div class="card-header">
                <i class="fa-solid fa-server"></i>
                Recursos del Servidor
            </div>
            <div class="metrics-row">
                <div class="metric-item">
                    <span class="metric-label">Uptime</span>
                    <span class="metric-value">{{ $metrics['os']['uptime'] }}</span>
                </div>
                <div class="metric-item">
                    <span class="metric-label">Carga (Load)</span>
                    <span class="metric-value">{{ $metrics['os']['load'] }}</span>
                </div>
            </div>
            <div class="resource-meters">
                <div class="meter-group">
                    <div class="meter-label">
                        <span>Memoria RAM</span>
                        <span>{{ $metrics['hardware']['memory']['used'] }} / {{ $metrics['hardware']['memory']['total'] }}</span>
                    </div>
                    <div class="meter-bar-bg">
                        <div class="meter-bar-fill {{ $metrics['hardware']['memory']['percent'] > 80 ? 'danger' : ($metrics['hardware']['memory']['percent'] > 60 ? 'warning' : 'success') }}" 
                             style="width: {{ $metrics['hardware']['memory']['percent'] }}%"></div>
                    </div>
                    <div class="meter-footer">{{ $metrics['hardware']['memory']['percent'] }}% en uso</div>
                </div>

                <div class="meter-group">
                    <div class="meter-label">
                        <span>Disco Principal</span>
                        <span>{{ $metrics['hardware']['disk']['used'] }} / {{ $metrics['hardware']['disk']['total'] }}</span>
                    </div>
                    <div class="meter-bar-bg">
                        <div class="meter-bar-fill {{ $metrics['hardware']['disk']['percent'] > 90 ? 'danger' : ($metrics['hardware']['disk']['percent'] > 75 ? 'warning' : 'success') }}" 
                             style="width: {{ $metrics['hardware']['disk']['percent'] }}%"></div>
                    </div>
                    <div class="meter-footer">{{ $metrics['hardware']['disk']['percent'] }}% en uso</div>
                </div>
            </div>
        </div>

        <!-- Tarjeta: Servicios Críticos -->
        <div class="bento-card services-card">
            <div class="card-header">
                <i class="fa-solid fa-shield-halved"></i>
                Estado de Servicios
            </div>
            <div class="services-list">
                @foreach($metrics['services'] as $key => $service)
                    <div class="service-item">
                        <div class="service-info">
                            <span class="service-name">{{ ucfirst($key) }}</span>
                            <span class="service-msg">{{ $service['message'] }}</span>
                        </div>
                        <div class="service-status">
                            <div class="led-indicator led-{{ $service['status'] }}"></div>
                            <span class="status-label">{{ strtoupper($service['status']) }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Tarjeta: Entorno de Ejecución -->
        <div class="bento-card env-card">
            <div class="card-header">
                <i class="fa-solid fa-code"></i>
                Runtime Environment
            </div>
            <div class="env-info">
                <div class="env-item">
                    <span class="env-label">Versión PHP</span>
                    <span class="env-value">{{ $metrics['os']['php_version'] }}</span>
                </div>
                <div class="env-item">
                    <span class="env-label">Sistema Operativo</span>
                    <span class="env-value">{{ $metrics['os']['name'] }}</span>
                </div>
                <div class="env-item">
                    <span class="env-label">Timezone</span>
                    <span class="env-value">{{ config('app.timezone') }}</span>
                </div>
                <div class="env-item">
                    <span class="env-label">Entorno</span>
                    <span class="env-value badge-env">{{ config('app.env') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.system-container { padding-bottom: 60px; }
.bento-grid-system {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    grid-template-rows: auto auto;
    gap: 24px;
    margin-top: 20px;
}

.bento-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 24px;
    box-shadow: var(--shadow-sm);
}

.card-header {
    font-size: 14px;
    font-weight: 700;
    color: var(--accent);
    text-transform: uppercase;
    letter-spacing: 0.1em;
    margin-bottom: 24px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.card-header i { color: var(--primary); }

/* Hardware Metrics */
.metrics-row { display: flex; gap: 30px; margin-bottom: 30px; }
.metric-item { display: flex; flex-direction: column; gap: 4px; }
.metric-label { font-size: 11px; color: var(--muted); text-transform: uppercase; font-weight: 600; }
.metric-value { font-family: 'IBM Plex Mono', monospace; font-size: 16px; font-weight: 700; color: var(--text); }

.resource-meters { display: flex; flex-direction: column; gap: 24px; }
.meter-group { display: flex; flex-direction: column; gap: 8px; }
.meter-label { display: flex; justify-content: space-between; font-size: 13px; font-weight: 600; }
.meter-bar-bg { height: 8px; background: var(--bg); border-radius: 4px; overflow: hidden; }
.meter-bar-fill { height: 100%; border-radius: 4px; transition: width 0.5s ease; }

.meter-bar-fill.success { background: #10b981; box-shadow: 0 0 10px rgba(16, 185, 129, 0.3); }
.meter-bar-fill.warning { background: #f59e0b; box-shadow: 0 0 10px rgba(245, 158, 11, 0.3); }
.meter-bar-fill.danger { background: #ef4444; box-shadow: 0 0 10px rgba(239, 68, 68, 0.3); }

.meter-footer { font-size: 11px; color: var(--muted); font-weight: 500; }

/* Services List */
.services-list { display: flex; flex-direction: column; gap: 16px; }
.service-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 16px;
    background: var(--bg);
    border: 1px solid var(--border);
    border-radius: 8px;
}

.service-info { display: flex; flex-direction: column; gap: 2px; }
.service-name { font-size: 14px; font-weight: 700; color: var(--text); }
.service-msg { font-size: 11px; color: var(--muted); }

.service-status { display: flex; align-items: center; gap: 12px; }
.status-label { font-family: 'IBM Plex Mono', monospace; font-size: 10px; font-weight: 800; letter-spacing: 0.05em; }

/* LED Indicators */
.led-indicator {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    position: relative;
}

.led-online { background: #10b981; box-shadow: 0 0 8px #10b981; }
.led-offline { background: #ef4444; box-shadow: 0 0 8px #ef4444; }
.led-degraded { background: #f59e0b; box-shadow: 0 0 8px #f59e0b; }

/* Environment Info */
.env-info { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; }
.env-item { display: flex; flex-direction: column; gap: 6px; }
.env-label { font-size: 11px; color: var(--muted); text-transform: uppercase; font-weight: 600; }
.env-value { font-size: 13px; font-weight: 700; color: var(--text); }
.badge-env {
    background: var(--primary);
    color: white;
    padding: 2px 8px;
    border-radius: 4px;
    font-size: 11px;
    width: fit-content;
    text-transform: uppercase;
}

.hardware-card { grid-row: span 2; }

@media (max-width: 1024px) {
    .bento-grid-system { grid-template-columns: 1fr; }
    .hardware-card { grid-row: auto; }
}
</style>
@endsection
