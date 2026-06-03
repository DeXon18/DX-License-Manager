@extends('layouts.app')

@section('title', 'Monitor de Colas Asíncronas')

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
    <div class="dx-v2-page-header-actions" style="display: flex; align-items: center;">
        <div class="dx-v2-sys-docker-env-badge" style="margin-right: 12px;">
            <div class="dx-v2-sys-docker-dot-live"></div>
            <span class="dx-v2-sys-docker-env-text">WORKER ONLINE</span>
        </div>
        <button onclick="window.location.reload()" class="btn-primary sm" style="margin: 0; padding: 6px 14px; font-size: 13px;">
            <i class="fa-solid fa-rotate"></i>
            <span style="margin-left: 6px;">Recargar</span>
        </button>
    </div>
</div>

<div class="dashboard-container" x-data="queueMonitor()">
    <!-- Stats Row -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 24px;">
        <div style="background: var(--dx-v2-bg-card); border: 1px solid var(--dx-v2-border); border-radius: 8px; padding: 20px; display: flex; align-items: center;">
            <div style="background: rgba(30, 144, 255, 0.1); width: 48px; height: 48px; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-right: 16px;">
                <i class="fa-solid fa-layer-group" style="font-size: 20px; color: #1E90FF;"></i>
            </div>
            <div>
                <div style="color: var(--dx-v2-muted); font-size: 13px; font-weight: 500; text-transform: uppercase; letter-spacing: 0.5px;">Estado del Worker</div>
                <div style="color: var(--dx-v2-text); font-size: 20px; font-weight: 600; display: flex; align-items: center; gap: 8px;">
                    <span class="dx-v2-sys-docker-card-dot running"></span> ACTIVO
                </div>
            </div>
        </div>
        <div style="background: var(--dx-v2-bg-card); border: 1px solid var(--dx-v2-border); border-radius: 8px; padding: 20px; display: flex; align-items: center;">
            <div style="background: rgba(0, 150, 57, 0.1); width: 48px; height: 48px; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-right: 16px;">
                <i class="fa-solid fa-bolt" style="font-size: 20px; color: #009639;"></i>
            </div>
            <div>
                <div style="color: var(--dx-v2-muted); font-size: 13px; font-weight: 500; text-transform: uppercase; letter-spacing: 0.5px;">Conexión</div>
                <div style="color: var(--dx-v2-text); font-size: 20px; font-weight: 600;">Redis</div>
            </div>
        </div>
        <div style="background: var(--dx-v2-bg-card); border: 1px solid var(--dx-v2-border); border-radius: 8px; padding: 20px; display: flex; align-items: center;">
            <div style="background: rgba(119, 123, 180, 0.1); width: 48px; height: 48px; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-right: 16px;">
                <i class="fa-brands fa-php" style="font-size: 20px; color: #777BB4;"></i>
            </div>
            <div>
                <div style="color: var(--dx-v2-muted); font-size: 13px; font-weight: 500; text-transform: uppercase; letter-spacing: 0.5px;">Daemon</div>
                <div style="color: var(--dx-v2-text); font-size: 20px; font-weight: 600;">php artisan queue:work</div>
            </div>
        </div>
    </div>

    <!-- Terminal -->
    <div style="background: #0d1117; border: 1px solid #30363d; border-radius: 8px; overflow: hidden; display: flex; flex-direction: column; height: 600px;">
        <div style="background: #161b22; padding: 10px 16px; border-bottom: 1px solid #30363d; display: flex; justify-content: space-between; align-items: center;">
            <div style="display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-terminal" style="color: #8b949e;"></i>
                <span style="color: #c9d1d9; font-size: 14px; font-weight: 600; font-family: ui-monospace, SFMono-Regular, Consolas, monospace;">Live Output: dx-queue-{{ config('app.env') === 'production' ? 'prod' : 'beta' }}</span>
            </div>
            <div style="display: flex; align-items: center; gap: 12px;">
                <label style="display: flex; align-items: center; gap: 6px; color: #8b949e; font-size: 12px; cursor: pointer;">
                    <input type="checkbox" x-model="autoScroll" style="accent-color: #238636;">
                    Auto-scroll
                </label>
                <div x-show="isPolling" style="width: 8px; height: 8px; background: #238636; border-radius: 50%; box-shadow: 0 0 8px #238636;"></div>
                <div x-show="!isPolling" style="width: 8px; height: 8px; background: #cf222e; border-radius: 50%;"></div>
                <button @click="togglePolling()" style="background: transparent; border: 1px solid #30363d; color: #c9d1d9; padding: 4px 8px; border-radius: 4px; font-size: 12px; cursor: pointer; display: flex; align-items: center; gap: 6px;">
                    <i class="fa-solid" :class="isPolling ? 'fa-pause' : 'fa-play'"></i>
                    <span x-text="isPolling ? 'Pausar' : 'Reanudar'"></span>
                </button>
            </div>
        </div>
        <div id="terminal-output" style="padding: 16px; overflow-y: auto; flex: 1; font-family: ui-monospace, SFMono-Regular, Consolas, monospace; font-size: 13px; line-height: 1.5; color: #c9d1d9; white-space: pre-wrap;" x-ref="terminal" x-html="formatLogs(logs)">
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
                
                // Colorear tags de Laravel
                text = text.replace(/\[([\d\-\s:]+)\]/g, '<span style="color: #8b949e;">[$1]</span>');
                text = text.replace(/INFO/g, '<span style="color: #3fb950; font-weight: bold;">INFO</span>');
                text = text.replace(/ERROR/g, '<span style="color: #f85149; font-weight: bold;">ERROR</span>');
                text = text.replace(/FAIL/g, '<span style="color: #f85149; font-weight: bold;">FAIL</span>');
                text = text.replace(/PROCESSING/g, '<span style="color: #d2a8ff; font-weight: bold;">PROCESSING</span>');
                text = text.replace(/DONE/g, '<span style="color: #3fb950; font-weight: bold;">DONE</span>');
                text = text.replace(/FAILED/g, '<span style="color: #f85149; font-weight: bold;">FAILED</span>');
                
                return text;
            }
        }));
    });
</script>
@endsection
