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
    <div class="dx-v2-sys-dash-stats-grid" style="margin-bottom: 24px; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));">
        <div class="dx-v2-sys-dash-stat-card" style="flex-direction: row; align-items: center; justify-content: flex-start; text-align: left; padding: 20px;">
            <div class="dx-v2-queue-stat-icon-wrapper blue" style="margin-right: 16px; font-size: 20px;">
                <i class="fa-solid fa-layer-group"></i>
            </div>
            <div>
                <div class="dx-v2-sys-dash-stat-card-title" style="margin-bottom: 4px;">Estado del Worker</div>
                <div class="dx-v2-sys-dash-stat-card-value" style="font-size: 20px; display: flex; align-items: center; gap: 8px;">
                    <span class="dx-v2-sys-docker-card-dot running"></span> ACTIVO
                </div>
            </div>
        </div>
        <div class="dx-v2-sys-dash-stat-card" style="flex-direction: row; align-items: center; justify-content: flex-start; text-align: left; padding: 20px;">
            <div class="dx-v2-queue-stat-icon-wrapper green" style="margin-right: 16px; font-size: 20px;">
                <i class="fa-solid fa-bolt"></i>
            </div>
            <div>
                <div class="dx-v2-sys-dash-stat-card-title" style="margin-bottom: 4px;">Conexión</div>
                <div class="dx-v2-sys-dash-stat-card-value" style="font-size: 20px;">Redis</div>
            </div>
        </div>
        <div class="dx-v2-sys-dash-stat-card" style="flex-direction: row; align-items: center; justify-content: flex-start; text-align: left; padding: 20px;">
            <div class="dx-v2-queue-stat-icon-wrapper indigo" style="margin-right: 16px; font-size: 20px;">
                <i class="fa-brands fa-php"></i>
            </div>
            <div>
                <div class="dx-v2-sys-dash-stat-card-title" style="margin-bottom: 4px;">Daemon</div>
                <div class="dx-v2-sys-dash-stat-card-value" style="font-size: 16px; font-family: var(--font-mono);">php artisan queue:work</div>
            </div>
        </div>
    </div>

    <!-- Terminal -->
    <div class="dx-v2-queue-terminal-wrapper">
        <div class="dx-v2-queue-terminal-header">
            <div class="dx-v2-queue-terminal-title">
                <i class="fa-solid fa-terminal"></i>
                <span>Live Output: dx-queue-{{ config('app.env') === 'production' ? 'prod' : 'beta' }}</span>
            </div>
            <div class="dx-v2-queue-terminal-controls">
                <label class="dx-v2-queue-terminal-checkbox">
                    <input type="checkbox" x-model="autoScroll">
                    Auto-scroll
                </label>
                <div class="dx-v2-queue-terminal-indicator" :class="isPolling ? 'active' : 'inactive'"></div>
                <button @click="togglePolling()" class="dx-v2-queue-terminal-btn">
                    <i class="fa-solid" :class="isPolling ? 'fa-pause' : 'fa-play'"></i>
                    <span x-text="isPolling ? 'Pausar' : 'Reanudar'"></span>
                </button>
            </div>
        </div>
        <div id="terminal-output" class="dx-v2-queue-terminal-body" x-ref="terminal" x-html="formatLogs(logs)">
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
