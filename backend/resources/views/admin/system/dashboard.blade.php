@extends('layouts.app')

@section('title', 'System Control Center')

@section('content')
<div class="page-header">
    <div style="text-align: left; font-family: 'IBM Plex Mono', monospace; font-size: 10px; color: var(--muted); opacity: 0.8; padding-bottom: 5px;">
        <div style="display: flex; align-items: center; gap: 15px; justify-content: flex-start;">
            <div style="display: flex; align-items: center; gap: 6px;">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: var(--accent);"><path d="M15 22v-4a4.8 4.8 0 0 0-1-3.5c3 0 6-2 6-5.5.08-1.25-.27-2.48-1-3.5.28-1.15.28-2.35 0-3.5 0 0-1 0-3 1.5-2.64-.5-5.36-.5-8 0C6 2 5 2 5 2c-.3 1.15-.3 2.35 0 3.5A5.403 5.403 0 0 0 4 9c0 3.5 3 5.5 6 5.5-.39.49-.68 1.05-.85 1.65-.17.6-.22 1.23-.15 1.85v4"/><path d="M9 18c-4.51 2-5-2-7-2"/></svg>
                <span style="color: var(--accent); font-weight: 600;">{{ $metrics['git']['hash'] }}</span>
            </div>
            <div style="display: flex; align-items: center; gap: 6px;">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: var(--success);"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                <span style="color: var(--success);">{{ $metrics['git']['date'] }}</span>
            </div>
        </div>
    </div>
</div>
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

        {{-- Network & Presence --}}
        <div class="stat-card" style="padding: 20px; background: var(--surface); border: 1px solid var(--border); border-radius: 10px; display: flex; flex-direction: column; align-items: center; text-align: center; position: relative; overflow: hidden;">
            <div style="position: absolute; top: -5px; right: -15px; color: var(--primary); opacity: 0.05; pointer-events: none; transform: rotate(-15deg);">
                <svg width="84" height="84" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10M9 12l2 2 4-4"/></svg>
            </div>
            <div style="font-size: 0.65rem; font-weight: 700; text-transform: uppercase; tracking: 0.06em; color: var(--muted); margin-bottom: 16px; width: 100%; display: flex; justify-content: flex-start;">
                TRAFFIC & PRESENCE
            </div>
            <div style="display: flex; width: 100%; justify-content: space-between; align-items: center; margin: 8px 0; z-index: 1;">
                <div style="text-align: left;">
                    <div style="font-size: 0.6rem; color: var(--muted); text-transform: uppercase; margin-bottom: 4px;">Network I/O</div>
                    <div style="font-size: 1.1rem; font-weight: 700; font-family: 'Outfit', sans-serif; color: var(--primary); display: flex; align-items: center; gap: 4px;">
                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="7 13 12 18 17 13"/><polyline points="7 6 12 11 17 6"/></svg>
                        {{ $metrics['hardware']['network']['rx'] }}
                    </div>
                    <div style="font-size: 1.1rem; font-weight: 700; font-family: 'Outfit', sans-serif; color: var(--primary); display: flex; align-items: center; gap: 4px;">
                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="17 11 12 6 7 11"/><polyline points="17 18 12 13 7 18"/></svg>
                        {{ $metrics['hardware']['network']['tx'] }}
                    </div>
                </div>
                <div style="width: 1px; height: 40px; background: var(--border-subtle); opacity: 0.5;"></div>
                <div style="text-align: right;">
                    <div style="font-size: 0.6rem; color: var(--muted); text-transform: uppercase; margin-bottom: 4px;">Users Online</div>
                    <div style="font-size: 2.2rem; font-weight: 700; font-family: 'Outfit', sans-serif; color: var(--success); line-height: 1;">
                        {{ $metrics['security']['active_sessions'] }}
                    </div>
                </div>
            </div>
            <div style="margin-top: auto; width: 100%; border-top: 1px solid var(--border-subtle); padding-top: 12px; display: flex; justify-content: space-between; align-items: center; z-index: 1;">
                <span style="font-size: 0.72rem; font-family: 'IBM Plex Mono', monospace; color: var(--muted);">ETH0 STATISTICS</span>
                <span style="font-size: 0.72rem; font-family: 'IBM Plex Mono', monospace; color: var(--muted); display: flex; align-items: center; gap: 4px;">
                    <span style="width: 6px; height: 6px; border-radius: 50%; background: var(--success);"></span>
                    TUNNEL OK
                </span>
            </div>
        </div>
    </div>    <div class="grid-main" style="display: grid; grid-template-columns: 1fr 340px; gap: 24px; margin-top: 24px;">
        <div style="display: flex; flex-direction: column;">
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
                                        @if(isset($info['extra']))
                                            <div style="display: flex; gap: 8px; margin-top: 6px; border-top: 1px solid var(--border-subtle); padding-top: 4px;">
                                                <div title="Active Conn" style="font-size: 8px; color: var(--muted); display: flex; align-items: center; gap: 2px;">
                                                    <svg width="8" height="8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                                                    {{ $info['extra']['threads'] }}
                                                </div>
                                                <div title="Slow Queries" style="font-size: 8px; color: {{ $info['extra']['slow_queries'] > 0 ? 'var(--warning)' : 'var(--muted)' }}; display: flex; align-items: center; gap: 2px;">
                                                    <svg width="8" height="8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                                    {{ $info['extra']['slow_queries'] }}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Dedicated Modules Navigation --}}
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 24px;">
                <a href="{{ route('admin.backups.index') }}" class="module-card">
                    <div class="module-icon" style="background: rgba(67, 97, 238, 0.1); color: var(--accent);">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3"/></svg>
                    </div>
                    <div class="module-info">
                        <span class="module-title">Gestión de Backups</span>
                        <p class="module-desc">Historial completo, descargas y gestión de espacio en disco para copias de seguridad.</p>
                    </div>
                </a>
                <a href="{{ route('admin.audit.index') }}" class="module-card">
                    <div class="module-icon" style="background: rgba(16, 185, 129, 0.1); color: var(--success);">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                    </div>
                    <div class="module-info">
                        <span class="module-title">Auditoría y Logs</span>
                        <p class="module-desc">Trazabilidad total con filtros avanzados por usuario, acción, nivel e IP.</p>
                    </div>
                </a>
            </div>
        </div>

        {{-- System Sidebar --}}
        <div style="display: flex; flex-direction: column; gap: 24px;">
            {{-- Quick Actions --}}
            <div class="card" x-data="systemActions()">
                <div class="card-header">
                    <span class="card-title">Acciones Rápidas</span>
                </div>
                <div style="padding: 20px; display: flex; flex-direction: column; gap: 10px;">
                    <button @click="execute('{{ route('admin.system.clear-cache') }}', 'Limpiar caché')" class="btn-noc" :disabled="loading" style="justify-content: flex-start; color: var(--accent); border-color: rgba(67, 97, 238, 0.15);">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-left: 5px;"><path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M10 11v6M14 11v6"/></svg>
                        <span style="margin-left: 5px;">Limpiar Caché</span>
                    </button>
                    <button @click="execute('{{ route('admin.system.restart-queues') }}', 'Reiniciar workers')" class="btn-noc" :disabled="loading" style="justify-content: flex-start; color: #818cf8; border-color: rgba(129, 140, 248, 0.15);">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-left: 5px;"><path d="M23 4v6h-6M1 20v-6h6M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/></svg>
                        <span style="margin-left: 5px;">Reiniciar Workers</span>
                    </button>
                    <button @click="execute('{{ route('admin.backups.run') }}', 'Generar backup')" class="btn-noc" :disabled="loading" style="justify-content: flex-start; color: var(--warning); border-color: rgba(245, 158, 11, 0.15);">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-left: 5px;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3"/></svg>
                        <span style="margin-left: 5px;">Backup MariaDB</span>
                    </button>
                    
                    <button @click="execute('{{ route('admin.system.toggle-maintenance') }}', 'Modo mantenimiento')" class="btn-noc" :disabled="loading" :class="metrics['os']['maintenance'] ? 'btn-success' : 'btn-danger'" style="justify-content: flex-start;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-left: 5px;"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        <span style="margin-left: 5px;">{{ $metrics['os']['maintenance'] ? 'Desactivar Manto.' : 'Modo Mantenimiento' }}</span>
                    </button>
                    <button @click="execute('{{ route('admin.system.test-telegram') }}', 'Test Telegram')" class="btn-noc" :disabled="loading" style="justify-content: flex-start; color: #fb923c; border-color: rgba(251, 146, 60, 0.15);">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-left: 5px;"><path d="m22 2-7 20-4-9-9-4Z"/><path d="M22 2 11 13"/></svg>
                        <span style="margin-left: 5px;">Probar Alertas</span>
                    </button>
                </div>
            </div>

            {{-- Security & Traffic Stats --}}
            <div class="card">
                <div class="card-header">
                    <span class="card-title">Seguridad y Tráfico</span>
                </div>
                <div style="padding: 24px;">
                    <div style="display: flex; flex-direction: column; gap: 16px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 12px; border-bottom: 1px solid var(--border);">
                            <span style="color: var(--muted); font-size: 13px;">Errores Críticos (24h)</span>
                            <span style="font-family: 'Outfit'; font-size: 18px; font-weight: 700; color: {{ $metrics['errors_24h'] > 0 ? 'var(--danger)' : 'var(--primary)' }};">{{ $metrics['errors_24h'] }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 12px; border-bottom: 1px solid var(--border);">
                            <span style="color: var(--muted); font-size: 13px;">Logins Fallidos (24h)</span>
                            <span style="font-family: 'Outfit'; font-size: 18px; font-weight: 700; color: {{ $metrics['security']['failed_logins_24h'] > 0 ? 'var(--warning)' : 'var(--primary)' }};">{{ $metrics['security']['failed_logins_24h'] }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 12px; border-bottom: 1px solid var(--border);">
                            <span style="color: var(--muted); font-size: 13px;">JWT Blacklist</span>
                            <span style="font-family: 'Outfit'; font-size: 18px; font-weight: 700; color: var(--primary);">{{ $metrics['security']['blacklist_count'] }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="color: var(--muted); font-size: 13px;">Entorno PHP</span>
                            <span style="font-family: 'IBM Plex Mono'; font-size: 12px; color: var(--primary); font-weight: 600;">v{{ $metrics['os']['php_version'] }}</span>
                        </div>
                    </div>
                    <div style="font-size: 11px; color: var(--muted); line-height: 1.6; margin-top: 16px; padding-top: 16px; border-top: 1px dashed var(--border-subtle);">
                        Latido del sistema operativo. Workers escuchando en colas Redis <code style="color: var(--primary); background: rgba(0,0,0,0.3); padding: 2px 4px; border-radius: 3px;">queues:default</code>.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .btn-noc {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: 100%;
        padding: 10px;
        border-radius: 8px;
        background: rgba(255,255,255,0.03);
        border: 1px solid var(--border);
        color: var(--primary);
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
    }
    .btn-noc:hover:not(:disabled) {
        background: rgba(255,255,255,0.06);
        border-color: currentColor;
        transform: translateX(4px);
    }
    .btn-noc:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
    .btn-danger {
        background: rgba(239, 68, 68, 0.05) !important;
        color: var(--danger) !important;
        border-color: rgba(239, 68, 68, 0.2) !important;
    }
    .btn-success {
        background: rgba(16, 185, 129, 0.05) !important;
        color: var(--success) !important;
        border-color: rgba(16, 185, 129, 0.2) !important;
    }
    @keyframes pulse {
        0% { opacity: 1; }
        50% { opacity: 0.5; }
        100% { opacity: 1; }
    }
    .dot-live {
        width: 8px;
        height: 8px;
        background: var(--success);
        border-radius: 50%;
        box-shadow: 0 0 8px var(--success);
        animation: pulse 2s infinite;
    }
    .btn-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 26px;
        height: 26px;
        border-radius: 6px;
        background: rgba(255,255,255,0.03);
        border: 1px solid var(--border);
        color: var(--muted);
        transition: all 0.2s;
        cursor: pointer;
    }
    .btn-icon:hover {
        background: rgba(67, 97, 238, 0.1);
        color: var(--accent);
        border-color: var(--accent);
        transform: translateY(-1px);
    }
    .btn-icon.danger:hover {
        background: rgba(239, 68, 68, 0.1);
        color: var(--danger);
        border-color: var(--danger);
    }

    /* Module Cards */
    .module-card {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 20px;
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 12px;
        text-decoration: none;
        transition: all 0.3s ease;
    }
    .module-card:hover {
        transform: translateY(-4px);
        border-color: var(--accent);
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        background: rgba(255,255,255,0.02);
    }
    .module-icon {
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
    }
    .module-title {
        display: block;
        font-size: 15px;
        font-weight: 700;
        color: var(--primary);
        margin-bottom: 4px;
        font-family: 'Outfit', sans-serif;
    }
    .module-desc {
        margin: 0;
        font-size: 11px;
        color: var(--muted);
        line-height: 1.4;
    }
</style>

<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script>
    function systemActions() {
        return {
            loading: false,
            metrics: @json($metrics),
            execute(url, actionName) {
                if (!confirm(`¿Estás seguro de que deseas ejecutar: ${actionName}?`)) return;
                
                this.loading = true;
                fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Notificación simple
                        const toast = document.createElement('div');
                        toast.style.position = 'fixed';
                        toast.style.bottom = '20px';
                        toast.style.right = '20px';
                        toast.style.background = 'var(--surface)';
                        toast.style.border = '1px solid var(--success)';
                        toast.style.color = 'var(--primary)';
                        toast.style.padding = '12px 20px';
                        toast.style.borderRadius = '8px';
                        toast.style.zIndex = '9999';
                        toast.innerText = data.message;
                        document.body.appendChild(toast);
                        
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    alert('Error crítico de red o servidor');
                    console.error(error);
                })
                .finally(() => {
                    this.loading = false;
                });
            }
        }
    }
</script>
@endsection
