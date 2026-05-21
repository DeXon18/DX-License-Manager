@extends('layouts.app')

@section('title', 'Docker Fleet Monitor')

@section('content')
<div class="dx-v2-sys-docker-page-header">
    <div class="dx-v2-sys-docker-header-layout">
        <div>
            <div class="dx-v2-sys-docker-breadcrumb-wrapper">
                <a href="{{ route('admin.system.index') }}" class="dx-v2-sys-docker-breadcrumb-link">
                    <i class="fa-solid fa-chevron-left"></i>
                </a>
                <span class="dx-v2-sys-docker-breadcrumb-text">Infraestructura</span>
            </div>
            <h1 class="dx-v2-sys-docker-title">
                <i class="fa-solid fa-layer-group dx-v2-sys-docker-title-icon"></i>Docker Fleet Monitor
            </h1>
        </div>
        
        <div class="dx-v2-sys-docker-header-right">
            <div class="dx-v2-sys-docker-env-badge">
                <div class="dx-v2-sys-docker-dot-live"></div>
                <span class="dx-v2-sys-docker-env-text">{{ config('app.env') === 'production' ? 'PROD_SYSTEM' : 'BETA_STAGING' }}</span>
            </div>
            <button onclick="window.location.reload()" class="dx-v2-sys-docker-btn-noc">
                <i class="fa-solid fa-rotate"></i>
                <span>Sincronizar</span>
            </button>
        </div>
    </div>
</div>

<div class="dashboard-container" x-data="dockerActions()">
    @if(empty($containers))
        <div class="dx-v2-sys-docker-empty-state">
            <div class="dx-v2-sys-docker-empty-icon-box">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
            </div>
            <span class="dx-v2-sys-docker-empty-title">Servicios Docker No Detectados</span>
            <p class="dx-v2-sys-docker-empty-desc">No se pudo establecer conexión con el socket de Docker Daemon (`/var/run/docker.sock`) o no hay contenedores de la aplicación configurados en este entorno.</p>
        </div>
    @else
        <div class="dx-v2-sys-docker-grid">
            @foreach($containers as $container)
                <div class="dx-v2-sys-docker-card">
                    {{-- Card Top Section --}}
                    <div class="dx-v2-sys-docker-card-header">
                        <div class="dx-v2-sys-docker-card-title-row">
                            <div class="dx-v2-sys-docker-icon-box">
                                @php
                                    $service = strtolower($container['service']);
                                @endphp
                                @if(str_contains($service, 'php'))
                                    <i class="fa-brands fa-php" style="font-size: 24px; color: #777BB4;"></i>
                                @elseif(str_contains($service, 'maria') || str_contains($service, 'db'))
                                    <i class="fa-solid fa-database" style="font-size: 20px; color: #003545;"></i>
                                @elseif(str_contains($service, 'nginx'))
                                    <i class="fa-solid fa-server" style="font-size: 20px; color: #009639;"></i>
                                @elseif(str_contains($service, 'node'))
                                    <i class="fa-brands fa-node-js" style="font-size: 24px; color: #339933;"></i>
                                @elseif(str_contains($service, 'redis'))
                                    <i class="fa-solid fa-cube" style="font-size: 20px; color: #DC382D;"></i>
                                @else
                                    <i class="fa-solid fa-box-archive" style="font-size: 20px; color: var(--dx-v2-muted);"></i>
                                @endif
                            </div>
                            <div>
                                <div class="dx-v2-sys-docker-card-title-inner">
                                    <span class="dx-v2-sys-docker-card-title">{{ $container['service'] }}</span>
                                    <div class="dx-v2-sys-docker-card-dot {{ $container['is_running'] ? 'running' : 'stopped' }}"></div>
                                </div>
                                <div class="dx-v2-sys-docker-card-subtitle">{{ $container['name'] }}</div>
                            </div>
                        </div>
                        <div class="dx-v2-sys-docker-status-pill {{ $container['is_running'] ? 'up' : 'down' }}">
                            {{ $container['is_running'] ? ($container['is_healthy'] ? 'HEALTHY' : 'RUNNING') : 'OFFLINE' }}
                        </div>
                    </div>

                    {{-- Metrics Box --}}
                    <div class="dx-v2-sys-docker-metrics-box">
                        <div class="dx-v2-sys-docker-cpu-gauge-box">
                            <svg viewBox="0 0 100 100" class="dx-v2-sys-docker-gauge-ring">
                                <circle class="dx-v2-sys-docker-gauge-base" cx="50" cy="50" r="40"></circle>
                                <circle class="dx-v2-sys-docker-gauge-active" cx="50" cy="50" r="40" 
                                        style="stroke-dasharray: {{ (floatval($container['cpu']) / 100) * 251 }}, 251; stroke: {{ floatval($container['cpu']) > 80 ? 'var(--dx-v2-danger, #cf222e)' : 'var(--dx-v2-accent-base)' }};"></circle>
                            </svg>
                            <div class="dx-v2-sys-docker-gauge-info">
                                <span class="dx-v2-sys-docker-val-perc">{{ $container['cpu'] }}</span>
                                <span class="dx-v2-sys-docker-val-label">CPU</span>
                            </div>
                        </div>

                        <div class="dx-v2-sys-docker-ram-meter-box">
                            <div class="dx-v2-sys-docker-ram-header">
                                <span class="dx-v2-sys-docker-ram-label">Memory</span>
                                <span class="dx-v2-sys-docker-ram-value">{{ $container['mem_perc'] }}</span>
                            </div>
                            <div class="dx-v2-sys-docker-progress-track">
                                <div class="dx-v2-sys-docker-progress-fill {{ floatval($container['mem_perc']) > 85 ? 'warn' : '' }}" style="width: {{ floatval($container['mem_perc']) }}%"></div>
                            </div>
                            <div class="dx-v2-sys-docker-ram-usage">
                                {{ $container['mem_usage'] }}
                            </div>
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="dx-v2-sys-docker-card-footer">
                        <div class="dx-v2-sys-docker-card-footer-left">
                            <span class="dx-v2-sys-docker-card-footer-image">
                                <i class="fa-solid fa-code-commit"></i>{{ $container['image'] }}
                            </span>
                            <span class="dx-v2-sys-docker-card-footer-time">
                                <i class="fa-solid fa-clock"></i>{{ $container['status_raw'] }}
                            </span>
                        </div>
                        <button @click="execute('{{ route('admin.system.restart-container', ['name' => $container['name']]) }}', 'Reiniciar: {{ $container['name'] }}')" 
                                class="dx-v2-sys-docker-btn-restart" title="Restart Service">
                            <i class="fa-solid fa-power-off"></i>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

<script>
    function dockerActions() {
        return {
            loading: false,
            execute(url, actionName) {
                if (!confirm(`⚠️ ALERTA DE SISTEMA\n\n¿Estás seguro de que deseas REINICIAR este contenedor?\n${actionName}\n\nEsta acción interrumpirá temporalmente el servicio.`)) return;
                this.loading = true;
                fetch(url, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json', 'Content-Type': 'application/json' }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) { alert('✅ ' + data.message); window.location.reload(); }
                    else { alert('❌ Error: ' + data.message); }
                })
                .catch(error => { alert('❌ Error crítico de comunicación'); console.error(error); })
                .finally(() => { this.loading = false; });
            }
        }
    }
</script>
@endsection
