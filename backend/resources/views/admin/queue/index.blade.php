@extends('layouts.app')

@section('title', 'Monitor de Colas Asíncronas')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/modules/dx-v2-queue-monitor.css') }}">
@endpush

@section('content')
<div class="dx-v2-page-header">
    <div>
        <div class="breadcrumb">
            <a href="{{ route('admin.system.index') }}">Infraestructura</a>
            <span class="separator">/</span>
            <span class="current">Queue Monitor</span>
        </div>
        <h1 class="page-title">Procesamiento <span>Asíncrono</span></h1>
        <p class="page-subtitle">Monitorización en tiempo real de los workers de Laravel Queue y trabajos en segundo plano.</p>
    </div>
    <div class="dx-v2-queue-header-actions">
        <div class="dx-v2-sys-docker-env-badge" style="margin-right: 12px;">
            <div class="dx-v2-sys-docker-dot-live"></div>
            <span class="dx-v2-sys-docker-env-text">WORKER ONLINE</span>
        </div>
        <button onclick="window.location.reload()" class="btn-primary sm dx-v2-queue-btn-reload">
            <i class="fa-solid fa-rotate"></i>
            <span>Recargar</span>
        </button>
    </div>
</div>

<div class="dashboard-container" x-data="queueMonitor()">
    <!-- Stats Row -->
    <div class="stats-row">
        <div class="stat-card success">
            <div class="stat-label">ESTADO WORKER</div>
            <div class="stat-value success">ACTIVO</div>
            <div class="stat-meta">ONLINE</div>
            <i class="fa-solid fa-layer-group dx-v2-dashboard-stat-icon"></i>
        </div>

        <div class="stat-card danger">
            <div class="stat-label">TRABAJOS FALLIDOS</div>
            <div class="stat-value danger">0</div>
            <div class="stat-meta">Requieren atención</div>
            <i class="fa-solid fa-triangle-exclamation dx-v2-dashboard-stat-icon"></i>
        </div>

        <div class="stat-card warn">
            <div class="stat-label">DAEMON PROCESO</div>
            <div class="stat-value warn" style="font-size: 24px;">queue:work</div>
            <div class="stat-meta">php artisan</div>
            <i class="fa-brands fa-php dx-v2-dashboard-stat-icon"></i>
        </div>

        <div class="stat-card accent">
            <div class="stat-label">CONEXIÓN</div>
            <div class="stat-value accent">Redis</div>
            <div class="stat-meta">BROKER PRINCIPAL</div>
            <i class="fa-solid fa-bolt dx-v2-dashboard-stat-icon"></i>
        </div>
    </div>

    <!-- Terminal -->
    <div class="card">
        <div class="card-header dx-v2-queue-terminal-header-override">
            <span class="dx-v2-queue-terminal-title">
                <i class="fa-solid fa-terminal"></i> 
                LIVE OUTPUT: DX-QUEUE-{{ strtoupper(config('app.env') === 'production' ? 'PROD' : 'BETA') }}
            </span>
            <div class="dx-v2-queue-terminal-controls">
                <label class="dx-v2-queue-terminal-checkbox">
                    <input type="checkbox" x-model="autoScroll">
                    Auto-scroll
                </label>
                <div class="dx-v2-queue-terminal-indicator" :class="isPolling ? 'active' : 'inactive'"></div>
                <button @click="togglePolling()" class="btn-secondary sm dx-v2-queue-terminal-btn-override" :class="isPolling ? 'is-polling' : 'is-paused'">
                    <i class="fa-solid" :class="isPolling ? 'fa-pause' : 'fa-play'"></i>
                    <span x-text="isPolling ? 'Pausar' : 'Reanudar'"></span>
                </button>
            </div>
        </div>
        <div id="terminal-output" class="dx-v2-queue-terminal-body card-body" x-ref="terminal" x-html="formatLogs(logs)">
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('queueMonitor', () => ({
            logs: 'Cargando logs del contenedor...',
            isPolling: true,
            autoScroll: true,
            intervalId: null,

            init() {
                this.fetchLogs();
                this.startPolling();
            },

            startPolling() {
                this.isPolling = true;
                this.intervalId = setInterval(() => {
                    this.fetchLogs();
                }, 3000);
            },

            stopPolling() {
                this.isPolling = false;
                if (this.intervalId) {
                    clearInterval(this.intervalId);
                    this.intervalId = null;
                }
            },

            togglePolling() {
                if (this.isPolling) {
                    this.stopPolling();
                } else {
                    this.startPolling();
                }
            },

            fetchLogs() {
                fetch('{{ route('admin.queue-monitor.logs') }}', {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.logs) {
                        this.logs = data.logs;
                        if (this.autoScroll) {
                            this.$nextTick(() => {
                                const terminal = this.$refs.terminal;
                                terminal.scrollTop = terminal.scrollHeight;
                            });
                        }
                    }
                })
                .catch(err => {
                    console.error('Error fetching logs:', err);
                    this.logs = "Error de conexión al obtener los logs.\n" + this.logs;
                    this.stopPolling();
                });
            },

            formatLogs(rawText) {
                if (!rawText) return '';
                
                // Colorización básica
                let text = rawText.replace(/</g, '&lt;').replace(/>/g, '&gt;');
                
                // Colorear tags de Laravel usando clases CSS
                text = text.replace(/\[([\d\-\s:]+)\]/g, '<span class="dx-v2-queue-log-time">[$1]</span>');
                text = text.replace(/INFO/g, '<span class="dx-v2-queue-log-info">INFO</span>');
                text = text.replace(/ERROR/g, '<span class="dx-v2-queue-log-error">ERROR</span>');
                text = text.replace(/FAIL/g, '<span class="dx-v2-queue-log-error">FAIL</span>');
                text = text.replace(/PROCESSING/g, '<span class="dx-v2-queue-log-processing">PROCESSING</span>');
                text = text.replace(/DONE/g, '<span class="dx-v2-queue-log-done">DONE</span>');
                text = text.replace(/FAILED/g, '<span class="dx-v2-queue-log-error">FAILED</span>');
                
                return text;
            }
        }));
    });
</script>
@endsection
