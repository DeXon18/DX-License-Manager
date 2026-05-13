@extends('layouts.app')

@section('title', 'Docker Fleet Monitor')

@section('content')
<div class="page-header" style="border-bottom: 1px solid var(--border); padding-bottom: 16px; margin-bottom: 24px;">
    <div style="display: flex; justify-content: space-between; align-items: flex-end; width: 100%;">
        <div>
            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                <a href="{{ route('admin.system.index') }}" style="color: var(--muted); transition: color 0.2s;" onmouseover="this.style.color='var(--accent)'" onmouseout="this.style.color='var(--muted)'">
                    <i class="fa-solid fa-chevron-left" style="font-size: 14px;"></i>
                </a>
                <span style="font-family: 'Inter', sans-serif; font-size: 11px; font-weight: 600; color: var(--muted); text-transform: uppercase; letter-spacing: 0.1em;">Infraestructura</span>
            </div>
            <h1 style="margin: 0; font-family: 'Inter', sans-serif; font-size: 1.602rem; font-weight: 700; color: var(--primary); letter-spacing: -0.02em;">
                <i class="fa-solid fa-layer-group" style="font-size: 20px; margin-right: 10px; color: var(--accent); opacity: 0.8;"></i>Docker Fleet Monitor
            </h1>
        </div>
        
        <div style="display: flex; align-items: center; gap: 12px;">
            <div style="background: var(--surface); border: 1px solid var(--border); padding: 6px 12px; border-radius: 6px; display: flex; align-items: center; gap: 8px;">
                <div class="dot-live"></div>
                <span style="font-family: 'IBM Plex Mono', monospace; font-size: 11px; font-weight: 700; color: var(--primary);">{{ config('app.env') === 'production' ? 'PROD_SYSTEM' : 'BETA_STAGING' }}</span>
            </div>
            <button onclick="window.location.reload()" class="btn-noc-standard">
                <i class="fa-solid fa-rotate"></i>
                <span>Sincronizar</span>
            </button>
        </div>
    </div>
</div>

<div class="dashboard-container" x-data="dockerActions()">
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(420px, 1fr)); gap: 20px;">
        @foreach($containers as $container)
            <div class="docker-card-final">
                {{-- Card Top Section --}}
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px;">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div class="service-logo-box">
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
                                <i class="fa-solid fa-box-archive" style="font-size: 20px; color: var(--secondary);"></i>
                            @endif
                        </div>
                        <div>
                            <div style="display: flex; align-items: center; gap: 6px;">
                                <span style="font-family: 'Inter', sans-serif; font-size: 15px; font-weight: 700; color: var(--primary);">{{ $container['service'] }}</span>
                                <div style="width: 6px; height: 6px; border-radius: 50%; background: {{ $container['is_running'] ? 'var(--success)' : 'var(--danger)' }}; box-shadow: 0 0 4px {{ $container['is_running'] ? 'var(--success)' : 'var(--danger)' }};"></div>
                            </div>
                            <div style="font-family: 'IBM Plex Mono', monospace; font-size: 10px; color: var(--muted); opacity: 0.8;">{{ $container['name'] }}</div>
                        </div>
                    </div>
                    <div class="status-pill {{ $container['is_running'] ? 'up' : 'down' }}">
                        {{ $container['is_running'] ? ($container['is_healthy'] ? 'HEALTHY' : 'RUNNING') : 'OFFLINE' }}
                    </div>
                </div>

                {{-- Metrics Box --}}
                <div class="metrics-box-final">
                    <div class="cpu-gauge-box">
                        <svg viewBox="0 0 100 100" class="gauge-ring">
                            <circle class="gauge-base" cx="50" cy="50" r="40"></circle>
                            <circle class="gauge-active" cx="50" cy="50" r="40" 
                                    style="stroke-dasharray: {{ (floatval($container['cpu']) / 100) * 251 }}, 251; stroke: {{ floatval($container['cpu']) > 80 ? 'var(--danger)' : 'var(--accent)' }};"></circle>
                        </svg>
                        <div class="gauge-info">
                            <span class="val-perc">{{ $container['cpu'] }}</span>
                            <span class="val-label">CPU</span>
                        </div>
                    </div>

                    <div class="ram-meter-box-final">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                            <span style="font-size: 9px; font-weight: 800; color: var(--muted); text-transform: uppercase; letter-spacing: 0.05em;">Memory</span>
                            <span style="font-family: 'IBM Plex Mono', monospace; font-size: 13px; font-weight: 700; color: var(--primary);">{{ $container['mem_perc'] }}</span>
                        </div>
                        <div class="progress-track">
                            <div class="progress-fill {{ floatval($container['mem_perc']) > 85 ? 'warn' : '' }}" style="width: {{ floatval($container['mem_perc']) }}%"></div>
                        </div>
                        <div style="font-family: 'IBM Plex Mono', monospace; font-size: 10px; color: var(--muted); margin-top: 4px; text-align: right;">
                            {{ $container['mem_usage'] }}
                        </div>
                    </div>
                </div>

                {{-- Footer --}}
                <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--border-subtle); padding-top: 12px; margin-top: auto;">
                    <div style="display: flex; flex-direction: column; gap: 2px;">
                        <span style="font-family: 'IBM Plex Mono', monospace; font-size: 9px; color: var(--muted); opacity: 0.6; max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                            <i class="fa-solid fa-code-commit" style="font-size: 8px; margin-right: 4px;"></i>{{ $container['image'] }}
                        </span>
                        <span style="font-size: 10px; font-weight: 600; color: var(--muted);">
                            <i class="fa-solid fa-clock" style="font-size: 8px; margin-right: 4px;"></i>{{ $container['status_raw'] }}
                        </span>
                    </div>
                    <button @click="execute('{{ route('admin.system.restart-container', ['name' => $container['name']]) }}', 'Reiniciar: {{ $container['name'] }}')" 
                            class="btn-restart-action" title="Restart Service">
                        <i class="fa-solid fa-power-off"></i>
                    </button>
                </div>
            </div>
        @endforeach
    </div>
</div>

<style>
    .docker-card-final {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 16px;
        display: flex;
        flex-direction: column;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .docker-card-final:hover {
        box-shadow: var(--elevation-2);
        border-color: var(--accent-border);
    }

    .service-logo-box {
        width: 40px;
        height: 40px;
        background: var(--raised);
        border: 1px solid var(--border);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .status-pill {
        font-size: 9px;
        font-weight: 800;
        padding: 3px 8px;
        border-radius: 4px;
        letter-spacing: 0.05em;
    }
    .status-pill.up { background: rgba(63, 185, 80, 0.1); color: var(--success); border: 1px solid var(--success-border); }
    .status-pill.down { background: rgba(224, 82, 82, 0.1); color: var(--danger); border: 1px solid var(--danger-border); }

    .metrics-box-final {
        display: grid;
        grid-template-columns: 74px 1fr;
        gap: 20px;
        align-items: center;
        padding: 12px 16px;
        background: var(--raised);
        border-radius: 10px;
        margin-bottom: 12px;
        border: 1px solid var(--border-subtle);
    }

    .cpu-gauge-box {
        position: relative;
        width: 74px;
        height: 74px;
    }
    .gauge-ring { transform: rotate(-90deg); }
    .gauge-base { fill: none; stroke: var(--border); stroke-width: 10; }
    .gauge-active { fill: none; stroke-width: 10; stroke-linecap: round; transition: stroke-dasharray 1s ease; }
    .gauge-info {
        position: absolute;
        top: 50%; left: 50%;
        transform: translate(-50%, -50%);
        text-align: center;
        display: flex;
        flex-direction: column;
        line-height: 1;
    }
    .val-perc { font-family: 'IBM Plex Mono', monospace; font-size: 12px; font-weight: 800; color: var(--primary); }
    .val-label { font-size: 7px; font-weight: 800; color: var(--muted); text-transform: uppercase; margin-top: 2px; }

    .progress-track { height: 6px; background: var(--border); border-radius: 3px; overflow: hidden; }
    .progress-fill { height: 100%; background: var(--success); transition: width 1s ease; }
    .progress-fill.warn { background: var(--danger); }

    .btn-restart-action {
        background: var(--danger-bg);
        border: 1px solid var(--danger-border);
        color: var(--danger);
        width: 32px; height: 32px;
        border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        cursor: pointer; transition: all 0.2s;
    }
    .btn-restart-action:hover { background: var(--danger); color: white; }

    .btn-noc-standard {
        background: var(--accent);
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 6px;
        font-family: 'Inter', sans-serif;
        font-size: 12px;
        font-weight: 600;
        display: flex; align-items: center; gap: 8px;
        cursor: pointer; transition: all 0.2s;
    }
    .btn-noc-standard:hover { background: var(--accent-hover); transform: translateY(-1px); }

    .dot-live { width: 6px; height: 6px; background: var(--success); border-radius: 50%; box-shadow: 0 0 6px var(--success); animation: pulse 2s infinite; }
    @keyframes pulse { 0% { opacity: 1; transform: scale(1); } 50% { opacity: 0.6; transform: scale(1.1); } 100% { opacity: 1; transform: scale(1); } }
</style>

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
