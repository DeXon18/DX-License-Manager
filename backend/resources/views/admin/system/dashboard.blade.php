@extends('layouts.app')

@section('title', 'System Control Center')

@section('header')
    <div class="page-header">
        <nav class="breadcrumb" aria-label="Breadcrumb">
            <a href="{{ route('dashboard') }}">Portal</a>
            <span>/</span>
            <span class="muted">Admin</span>
            <span>/</span>
            <span class="font-bold">System Dashboard</span>
        </nav>
        <h1 class="page-title flex items-center gap-3">
            System Control Center
            <span class="dot-live" title="Live System"></span>
        </h1>
        <p class="page-sub">NOC: Monitorización de Infraestructura, Servicios y Seguridad en tiempo real.</p>
    </div>
@endsection

@section('content')
<!-- Extra Fonts for Admin NOC -->
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@600;700&display=swap" rel="stylesheet">

<div class="dashboard-container">    <div class="stats-row" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; margin-bottom: 24px;">
        {{-- System Load --}}
        <div class="stat-card" style="padding: 20px; background: var(--surface); border: 1px solid var(--border); border-radius: 10px; display: flex; flex-direction: column; align-items: center; text-align: center; position: relative; overflow: hidden;">
            <div style="position: absolute; top: -5px; right: -15px; color: var(--primary); opacity: 0.05; pointer-events: none; transform: rotate(-15deg);">
                <svg width="84" height="84" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="4" y="4" width="16" height="16" rx="2"/><rect x="9" y="9" width="6" height="6"/><path d="M15 2v2M15 20v2M2 15h2M2 9h2M20 15h2M20 9h2M9 2v2M9 20v2"/></svg>
            </div>
            <div style="font-size: 0.65rem; font-weight: 700; text-transform: uppercase; tracking: 0.06em; color: var(--muted); margin-bottom: 16px; width: 100%; display: flex; justify-content: flex-start;">
                CPU LOAD (1M)
            </div>
            <div style="font-size: 2.2rem; font-weight: 700; font-family: 'Outfit', sans-serif; tracking: -0.02em; color: var(--primary); line-height: 1; margin: 8px 0; z-index: 1;">
                {{ $metrics['os']['load']['1m'] }}
            </div>
            <div style="font-size: 0.72rem; font-family: 'IBM Plex Mono', monospace; color: var(--muted); margin-top: auto; width: 100%; border-top: 1px solid var(--border-subtle); padding-top: 12px; z-index: 1;">
                5M: {{ $metrics['os']['load']['5m'] }} <span style="opacity: 0.3; margin: 0 4px;">·</span> 15M: {{ $metrics['os']['load']['15m'] }}
            </div>
        </div>

        {{-- Memory Usage --}}
        <div class="stat-card" style="padding: 20px; background: var(--surface); border: 1px solid var(--border); border-radius: 10px; display: flex; flex-direction: column; align-items: center; text-align: center; position: relative; overflow: hidden;">
            <div style="position: absolute; top: -5px; right: -15px; color: var(--primary); opacity: 0.05; pointer-events: none; transform: rotate(-15deg);">
                <svg width="84" height="84" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M6 19v2M9 19v2M12 19v2M15 19v2M18 19v2M21 15V9a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2ZM6 5V3M9 5V3M12 5V3M15 5V3M18 5V3"/></svg>
            </div>
            <div style="font-size: 0.65rem; font-weight: 700; text-transform: uppercase; tracking: 0.06em; color: var(--muted); margin-bottom: 16px; width: 100%; display: flex; justify-content: flex-start;">
                RAM MEMORY
            </div>
            <div style="font-size: 2.2rem; font-weight: 700; font-family: 'Outfit', sans-serif; tracking: -0.02em; color: {{ $metrics['hardware']['memory']['percent'] > 85 ? 'var(--danger)' : 'var(--primary)' }}; line-height: 1; margin: 8px 0; z-index: 1;">
                {{ $metrics['hardware']['memory']['percent'] }}<span style="font-size: 1rem; opacity: 0.4; font-family: 'IBM Plex Mono'; margin-left: 2px;">%</span>
            </div>
            <div style="margin-top: auto; width: 100%; border-top: 1px solid var(--border-subtle); padding-top: 12px; z-index: 1;">
                <div style="height: 2px; background: var(--border-subtle); border-radius: 1px; margin-bottom: 10px; overflow: hidden;">
                    <div style="width: {{ $metrics['hardware']['memory']['percent'] }}%; height: 100%; background: var(--accent);"></div>
                </div>
                <div style="font-size: 0.72rem; font-family: 'IBM Plex Mono', monospace; color: var(--muted); display: flex; justify-content: center; gap: 8px;">
                    <span>{{ $metrics['hardware']['memory']['used'] }}</span>
                    <span style="opacity: 0.3;">/</span>
                    <span>{{ $metrics['hardware']['memory']['total'] }}</span>
                </div>
            </div>
        </div>

        {{-- Storage --}}
        <div class="stat-card" style="padding: 20px; background: var(--surface); border: 1px solid var(--border); border-radius: 10px; display: flex; flex-direction: column; align-items: center; text-align: center; position: relative; overflow: hidden;">
            <div style="position: absolute; top: -5px; right: -15px; color: var(--primary); opacity: 0.05; pointer-events: none; transform: rotate(-15deg);">
                <svg width="84" height="84" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M22 12H2M5.45 5.11 2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11zM6 16h.01M10 16h.01"/></svg>
            </div>
            <div style="font-size: 0.65rem; font-weight: 700; text-transform: uppercase; tracking: 0.06em; color: var(--muted); margin-bottom: 16px; width: 100%; display: flex; justify-content: flex-start;">
                DISK STORAGE
            </div>
            <div style="font-size: 2.2rem; font-weight: 700; font-family: 'Outfit', sans-serif; tracking: -0.02em; color: {{ $metrics['hardware']['disk']['percent'] > 90 ? 'var(--danger)' : 'var(--primary)' }}; line-height: 1; margin: 8px 0; z-index: 1;">
                {{ $metrics['hardware']['disk']['percent'] }}<span style="font-size: 1rem; opacity: 0.4; font-family: 'IBM Plex Mono'; margin-left: 2px;">%</span>
            </div>
            <div style="margin-top: auto; width: 100%; border-top: 1px solid var(--border-subtle); padding-top: 12px; z-index: 1;">
                <div style="height: 2px; background: var(--border-subtle); border-radius: 1px; margin-bottom: 10px; overflow: hidden;">
                    <div style="width: {{ $metrics['hardware']['disk']['percent'] }}%; height: 100%; background: var(--accent);"></div>
                </div>
                <div style="font-size: 0.72rem; font-family: 'IBM Plex Mono', monospace; color: var(--muted); display: flex; justify-content: center; gap: 8px;">
                    <span>{{ $metrics['hardware']['disk']['used'] }}</span>
                    <span style="opacity: 0.3;">/</span>
                    <span>{{ $metrics['hardware']['disk']['total'] }}</span>
                </div>
            </div>
        </div>

        {{-- Sessions --}}
        <div class="stat-card" style="padding: 20px; background: var(--surface); border: 1px solid var(--border); border-radius: 10px; display: flex; flex-direction: column; align-items: center; text-align: center; position: relative; overflow: hidden;">
            <div style="position: absolute; top: -5px; right: -15px; color: var(--primary); opacity: 0.05; pointer-events: none; transform: rotate(-15deg);">
                <svg width="84" height="84" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10M9 12l2 2 4-4"/></svg>
            </div>
            <div style="font-size: 0.65rem; font-weight: 700; text-transform: uppercase; tracking: 0.06em; color: var(--muted); margin-bottom: 16px; width: 100%; display: flex; justify-content: flex-start;">
                ACTIVE SESSIONS
            </div>
            <div style="font-size: 2.2rem; font-weight: 700; font-family: 'Outfit', sans-serif; tracking: -0.02em; color: var(--success); line-height: 1; margin: 8px 0; z-index: 1;">
                {{ $metrics['security']['active_sessions'] }}
            </div>
            <div style="margin-top: auto; width: 100%; border-top: 1px solid var(--border-subtle); padding-top: 12px; display: flex; justify-content: space-between; align-items: center; z-index: 1;">
                <div style="display: flex; align-items: center; gap: 6px;">
                    <span style="width: 6px; height: 6px; border-radius: 50%; background: var(--warning);"></span>
                    <span style="font-size: 0.65rem; font-weight: 700; text-transform: uppercase; tracking: 0.06em; color: var(--muted);">BLACKLIST</span>
                </div>
                <span style="font-size: 0.72rem; font-family: 'IBM Plex Mono', monospace; color: var(--primary);">{{ $metrics['security']['blacklist_count'] }}</span>
            </div>
        </div>
    </div>

    <div class="grid-main" style="display: grid; grid-template-columns: 1fr 340px; gap: 24px; margin-top: 24px;">
        {{-- Services Matrix --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title">Services Matrix</span>
            </div>
            <div style="padding: 24px;">
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 16px;">
                    {{-- Core Services --}}
                    @foreach($metrics['services'] as $name => $info)
                        <div style="padding: 16px; border-radius: 8px; background: var(--bg); border: 1px solid var(--border); display: flex; justify-content: space-between; align-items: flex-start; transition: border-color 0.2s;">
                            <div style="flex: 1;">
                                <div style="font-size: 10px; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px;">{{ $name }}</div>
                                <div style="font-size: 14px; font-weight: 600; color: var(--primary); margin-bottom: 6px;">{{ strtoupper($info['message']) }}</div>
                                @if(isset($info['details']))
                                    <div style="font-size: 11px; font-family: 'IBM Plex Mono', monospace; color: var(--muted); background: rgba(0,0,0,0.2); padding: 2px 6px; border-radius: 4px; display: inline-block;">{{ $info['details'] }}</div>
                                @endif
                            </div>
                            <div class="dot {{ $info['status'] === 'online' ? 'online' : 'danger' }}" style="margin-top: 4px;"></div>
                        </div>
                    @endforeach

                    {{-- AI Providers --}}
                    @foreach($metrics['api_providers'] as $name => $info)
                        <div style="padding: 16px; border-radius: 8px; background: var(--bg); border: 1px solid var(--border); display: flex; justify-content: space-between; align-items: flex-start;">
                            <div style="flex: 1;">
                                <div style="font-size: 10px; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px;">Provider: {{ $name }}</div>
                                <div style="font-size: 14px; font-weight: 600; color: var(--primary);">{{ strtoupper($info['message']) }}</div>
                            </div>
                            <div class="dot {{ $info['status'] === 'online' ? 'online' : 'danger' }}" style="margin-top: 4px;"></div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- System Intelligence --}}
        <div style="display: flex; flex-direction: column; gap: 24px;">
            <div class="card">
                <div class="card-header">
                    <span class="card-title">Security & Traffic</span>
                </div>
                <div style="padding: 24px;">
                    <div style="display: flex; flex-direction: column; gap: 16px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 12px; border-bottom: 1px solid var(--border);">
                            <span style="color: var(--muted); font-size: 13px;">Critical Errors (24h)</span>
                            <span class="font-mono" style="color: {{ $metrics['errors_24h'] > 0 ? 'var(--danger)' : 'var(--success)' }}; font-weight: 700; font-size: 18px;">{{ $metrics['errors_24h'] }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 12px; border-bottom: 1px solid var(--border);">
                            <span style="color: var(--muted); font-size: 13px;">Failed Logins (24h)</span>
                            <span class="font-mono" style="color: {{ $metrics['security']['failed_logins_24h'] > 0 ? 'var(--warning)' : 'var(--primary)' }}; font-weight: 700; font-size: 18px;">{{ $metrics['security']['failed_logins_24h'] }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="color: var(--muted); font-size: 13px;">Runtime Environment</span>
                            <span class="font-mono" style="font-size: 12px; color: var(--primary);">PHP {{ $metrics['os']['php_version'] }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card" style="border: 1px dashed var(--accent); background: rgba(56, 139, 253, 0.03);">
                <div style="padding: 20px;">
                    <div style="font-size: 11px; font-weight: 700; color: var(--accent); text-transform: uppercase; margin-bottom: 10px; display: flex; align-items: center; gap: 8px;">
                        <span class="dot-live" style="width: 8px; height: 8px;"></span>
                        Audit Engine Pulse
                    </div>
                    <div style="font-size: 12px; color: var(--muted); line-height: 1.6;">
                        System heartbeat operational. All workers listening on Redis <code style="color: var(--primary); background: rgba(0,0,0,0.3); padding: 2px 4px; border-radius: 3px;">queues:default</code>.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .dashboard-container { animation: fadeIn 0.4s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    .dot { width: 10px; height: 10px; border-radius: 50%; }
    .dot.online { background: var(--success); box-shadow: 0 0 8px var(--success); }
    .dot.warn { background: var(--warning); box-shadow: 0 0 8px var(--warning); }
    .dot.danger { background: var(--danger); box-shadow: 0 0 8px var(--danger); }
</style>
@endsection
