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
        {{-- Server KPI --}}
        <div class="stat-card">
            <div class="stat-label">System Load</div>
            <div class="stat-value font-mono">{{ $metrics['os']['load'] }}</div>
            <div class="stat-meta">Uptime: {{ Str::limit($metrics['os']['uptime'], 20) }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Memory Usage</div>
            <div class="stat-value">{{ $metrics['hardware']['memory']['percent'] }}%</div>
            <div style="height: 4px; background: var(--border); border-radius: 2px; margin: 8px 0; overflow: hidden;">
                <div style="width: {{ $metrics['hardware']['memory']['percent'] }}%; height: 100%; background: {{ $metrics['hardware']['memory']['percent'] > 80 ? 'var(--danger)' : 'var(--accent)' }};"></div>
            </div>
            <div class="stat-meta">{{ $metrics['hardware']['memory']['used'] }} / {{ $metrics['hardware']['memory']['total'] }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Storage (Root)</div>
            <div class="stat-value">{{ $metrics['hardware']['disk']['percent'] }}%</div>
            <div style="height: 4px; background: var(--border); border-radius: 2px; margin: 8px 0; overflow: hidden;">
                <div style="width: {{ $metrics['hardware']['disk']['percent'] }}%; height: 100%; background: {{ $metrics['hardware']['disk']['percent'] > 90 ? 'var(--danger)' : 'var(--accent)' }};"></div>
            </div>
            <div class="stat-meta">{{ $metrics['hardware']['disk']['used'] }} / {{ $metrics['hardware']['disk']['total'] }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Active Sessions</div>
            <div class="stat-value">{{ $metrics['security']['active_sessions'] }}</div>
            <div class="stat-meta">JWT Blacklist: {{ $metrics['security']['blacklist_count'] }}</div>
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
