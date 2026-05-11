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
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 12px;">
                    {{-- Categorized Services --}}
                    @foreach($metrics['services'] as $category => $items)
                        <div style="grid-column: 1 / -1; margin-top: {{ $loop->first ? '0' : '12px' }}; margin-bottom: 6px;">
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <div style="color: var(--muted); opacity: 0.6;">
                                    @if($category === 'Infrastructure')
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="2" y="2" width="20" height="8" rx="2" ry="2"></rect><rect x="2" y="14" width="20" height="8" rx="2" ry="2"></rect><line x1="6" y1="6" x2="6.01" y2="6"></line><line x1="6" y1="18" x2="6.01" y2="18"></line></svg>
                                    @elseif($category === 'Processors')
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline></svg>
                                    @else
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z"/></svg>
                                    @endif
                                </div>
                                <span style="font-size: 10px; font-weight: 800; color: var(--muted); text-transform: uppercase; letter-spacing: 0.1em; white-space: nowrap;">
                                    {{ $category === 'Infrastructure' ? 'Infraestructura' : ($category === 'Processors' ? 'Procesadores' : 'Inteligencia AI') }}
                                </span>
                                <div style="height: 1px; background: var(--border-subtle); flex: 1; opacity: 0.5;"></div>
                            </div>
                        </div>
                        @foreach($items as $id => $info)
                            <div style="padding: 12px; border-radius: 10px; background: rgba(255,255,255,0.02); border: 1px solid var(--border); display: flex; align-items: flex-start; gap: 12px; position: relative; overflow: hidden; transition: all 0.3s ease;">
                                <div style="padding: 8px; border-radius: 8px; background: {{ $info['status'] === 'online' ? 'rgba(16, 185, 129, 0.1)' : 'rgba(239, 68, 68, 0.1)' }}; color: {{ $info['status'] === 'online' ? 'var(--success)' : 'var(--danger)' }};">
                                    @if($info['icon'] === 'database')
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/><path d="M3 12c0 1.66 4 3 9 3s9-1.34 9-3"/></svg>
                                    @elseif($info['icon'] === 'bolt')
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M13 2 3 14h9l-1 8 10-12h-9l1-8z"/></svg>
                                    @elseif($info['icon'] === 'cpu')
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="4" y="4" width="16" height="16" rx="2"/><rect x="9" y="9" width="6" height="6"/><path d="M15 2v2M15 20v2M2 15h2M2 9h2M20 15h2M20 9h2M9 2v2M9 20v2"/></svg>
                                    @elseif($info['icon'] === 'bell')
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg>
                                    @elseif($info['icon'] === 'sparkles')
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z"/><path d="M5 3v4M19 17v4M3 5h4M17 19h4"/></svg>
                                    @elseif($info['icon'] === 'brain')
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9.5 2A2.5 2.5 0 0 1 12 4.5v15a2.5 2.5 0 0 1-4.96.44 2.5 2.5 0 0 1-2.96-3.08 2.5 2.5 0 0 1-.34-5.58 2.5 2.5 0 0 1 1.32-4.24 2.5 2.5 0 0 1 4.44-1.98Z"/><path d="M14.5 2A2.5 2.5 0 0 0 12 4.5v15a2.5 2.5 0 0 0 4.96.44 2.5 2.5 0 0 0 2.96-3.08 2.5 2.5 0 0 0 .34-5.58 2.5 2.5 0 0 0-1.32-4.24 2.5 2.5 0 0 0-4.44-1.98Z"/></svg>
                                    @elseif($info['icon'] === 'globe')
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"/><path d="M2 12h20"/></svg>
                                    @endif
                                </div>
                                <div style="flex: 1;">
                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 3px;">
                                        <span style="font-size: 10px; font-weight: 700; color: var(--primary); text-transform: uppercase; letter-spacing: 0.05em;">{{ $info['label'] }}</span>
                                        <span class="dot {{ $info['status'] === 'online' ? 'online' : 'danger' }}"></span>
                                    </div>
                                    <div style="font-size: 12px; font-weight: 500; color: var(--primary);">{{ $info['message'] }}</div>
                                    @if(isset($info['details']))
                                        <div style="font-size: 9px; font-family: 'IBM Plex Mono', monospace; color: var(--muted); margin-top: 3px; opacity: 0.8;">{{ $info['details'] }}</div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @endforeach
                </div>
            </div>
        </div>

        {{-- System Intelligence --}}
        <div style="display: flex; flex-direction: column; gap: 24px;">
            <div class="card">
                <div class="card-header">
                    <span class="card-title">Seguridad y Tráfico</span>
                </div>
                <div style="padding: 24px;">
                    <div style="display: flex; flex-direction: column; gap: 16px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 12px; border-bottom: 1px solid var(--border);">
                            <span style="color: var(--muted); font-size: 13px;">Errores Críticos (24h)</span>
                            <span class="font-mono" style="color: {{ $metrics['errors_24h'] > 0 ? 'var(--danger)' : 'var(--success)' }}; font-weight: 700; font-size: 18px;">{{ $metrics['errors_24h'] }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 12px; border-bottom: 1px solid var(--border);">
                            <span style="color: var(--muted); font-size: 13px;">Logins Fallidos (24h)</span>
                            <span class="font-mono" style="color: {{ $metrics['security']['failed_logins_24h'] > 0 ? 'var(--warning)' : 'var(--primary)' }}; font-weight: 700; font-size: 18px;">{{ $metrics['security']['failed_logins_24h'] }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 12px; border-bottom: 1px solid var(--border);">
                            <span style="color: var(--muted); font-size: 13px;">Usuarios Online (15m)</span>
                            <span class="font-mono" style="color: var(--primary); font-weight: 700; font-size: 18px;">{{ $metrics['security']['active_sessions'] }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="color: var(--muted); font-size: 13px;">Entorno PHP</span>
                            <span class="font-mono" style="font-size: 12px; color: var(--primary);">v{{ $metrics['os']['php_version'] }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card" style="border: 1px dashed var(--accent); background: rgba(56, 139, 253, 0.03);">
                <div style="padding: 20px;">
                    <div style="font-size: 11px; font-weight: 700; color: var(--accent); text-transform: uppercase; margin-bottom: 10px; display: flex; align-items: center; gap: 8px;">
                        <span class="dot-live" style="width: 8px; height: 8px;"></span>
                        Pulso del Motor de Auditoría
                    </div>
                    <div style="font-size: 12px; color: var(--muted); line-height: 1.6;">
                        Latido del sistema operativo. Workers escuchando en colas Redis <code style="color: var(--primary); background: rgba(0,0,0,0.3); padding: 2px 4px; border-radius: 3px;">queues:default</code>.
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
