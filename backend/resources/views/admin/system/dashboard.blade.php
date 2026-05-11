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
<div class="dashboard-container">
    <div class="stats-row">
        {{-- System Load --}}
        <div class="stat-card" style="position: relative; overflow: hidden;">
            <div style="position: absolute; top: 12px; right: 12px; opacity: 0.1; color: var(--accent);">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="4" width="16" height="16" rx="2"/><rect x="9" y="9" width="6" height="6"/><path d="M15 2v2"/><path d="M15 20v2"/><path d="M2 15h2"/><path d="M2 9h2"/><path d="M20 15h2"/><path d="M20 9h2"/><path d="M9 2v2"/><path d="M9 20v2"/></svg>
            </div>
            <div class="stat-label" style="display: flex; align-items: center; gap: 6px;">
                SYSTEM LOAD
            </div>
            <div class="stat-value font-mono" style="font-size: 20px; letter-spacing: -0.5px; color: var(--primary);">{{ $metrics['os']['load'] }}</div>
            <div class="stat-meta" style="font-size: 9px; color: var(--muted); border-top: 1px solid var(--border); margin-top: 10px; padding-top: 8px;">
                UPTIME: {{ Str::limit($metrics['os']['uptime'], 30) }}
            </div>
        </div>

        {{-- Memory Usage --}}
        <div class="stat-card" style="position: relative;">
            <div style="position: absolute; top: 12px; right: 12px; opacity: 0.1; color: var(--accent);">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 19v2"/><path d="M9 19v2"/><path d="M12 19v2"/><path d="M15 19v2"/><path d="M18 19v2"/><path d="M21 15V9a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2Z"/><path d="M6 5V3"/><path d="M9 5V3"/><path d="M12 5V3"/><path d="M15 5V3"/><path d="M18 5V3"/></svg>
            </div>
            <div class="stat-label">MEMORY USAGE</div>
            <div style="display: flex; align-items: baseline; gap: 4px;">
                <span class="stat-value" style="color: {{ $metrics['hardware']['memory']['percent'] > 85 ? 'var(--danger)' : 'var(--primary)' }};">{{ $metrics['hardware']['memory']['percent'] }}</span>
                <span style="font-size: 14px; color: var(--muted);">%</span>
            </div>
            <div style="height: 3px; background: var(--border); border-radius: 1.5px; margin: 12px 0; overflow: hidden;">
                <div style="width: {{ $metrics['hardware']['memory']['percent'] }}%; height: 100%; background: {{ $metrics['hardware']['memory']['percent'] > 80 ? 'linear-gradient(90deg, var(--warning), var(--danger))' : 'var(--accent)' }}; transition: width 1s ease-out;"></div>
            </div>
            <div class="stat-meta font-mono" style="font-size: 10px;">{{ $metrics['hardware']['memory']['used'] }} / {{ $metrics['hardware']['memory']['total'] }}</div>
        </div>

        {{-- Storage --}}
        <div class="stat-card" style="position: relative;">
            <div style="position: absolute; top: 12px; right: 12px; opacity: 0.1; color: var(--accent);">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12H2"/><path d="M5.45 5.11 2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z"/><path d="M6 16h.01"/><path d="M10 16h.01"/></svg>
            </div>
            <div class="stat-label">STORAGE (ROOT)</div>
            <div style="display: flex; align-items: baseline; gap: 4px;">
                <span class="stat-value" style="color: {{ $metrics['hardware']['disk']['percent'] > 90 ? 'var(--danger)' : 'var(--primary)' }};">{{ $metrics['hardware']['disk']['percent'] }}</span>
                <span style="font-size: 14px; color: var(--muted);">%</span>
            </div>
            <div style="height: 3px; background: var(--border); border-radius: 1.5px; margin: 12px 0; overflow: hidden;">
                <div style="width: {{ $metrics['hardware']['disk']['percent'] }}%; height: 100%; background: {{ $metrics['hardware']['disk']['percent'] > 90 ? 'var(--danger)' : 'var(--accent)' }}; transition: width 1s ease-out;"></div>
            </div>
            <div class="stat-meta font-mono" style="font-size: 10px;">{{ $metrics['hardware']['disk']['used'] }} / {{ $metrics['hardware']['disk']['total'] }}</div>
        </div>

        {{-- Sessions --}}
        <div class="stat-card" style="position: relative;">
            <div style="position: absolute; top: 12px; right: 12px; opacity: 0.1; color: var(--accent);">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10"/><path d="m9 12 2 2 4-4"/></svg>
            </div>
            <div class="stat-label">ACTIVE SESSIONS</div>
            <div class="stat-value" style="color: var(--success);">{{ $metrics['security']['active_sessions'] }}</div>
            <div class="stat-meta" style="margin-top: 14px; display: flex; align-items: center; gap: 8px;">
                <span style="width: 6px; height: 6px; border-radius: 50%; background: var(--warning); display: inline-block;"></span>
                <span style="font-size: 10px; color: var(--muted); text-transform: uppercase;">Blacklist: {{ $metrics['security']['blacklist_count'] }}</span>
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
