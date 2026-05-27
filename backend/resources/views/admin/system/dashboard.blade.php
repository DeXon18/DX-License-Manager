@extends('layouts.app')

@section('title', 'System Control Center')

@section('content')
<div class="dx-v2-page-header">
    <div>
        <div class="breadcrumb">
            <a href="{{ route('admin.system.index') }}">Infraestructura</a>
            <span class="separator">/</span>
            <span class="current">System Control</span>
        </div>
        <h1 class="page-title">System Control <span>Center</span></h1>
        <p class="page-subtitle">Monitorización general y estado de los servicios del portal.</p>
    </div>
    <div class="dx-v2-page-header-actions">
        <div class="dx-v2-sys-dash-header-meta-layout" style="display: flex; gap: 12px; font-size: 12px; font-family: var(--font-mono, monospace);">
            <div class="dx-v2-sys-dash-header-meta-item" style="display: flex; align-items: center; gap: 6px;">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="accent-color"><path d="M15 22v-4a4.8 4.8 0 0 0-1-3.5c3 0 6-2 6-5.5.08-1.25-.27-2.48-1-3.5.28-1.15.28-2.35 0-3.5 0 0-1 0-3 1.5-2.64-.5-5.36-.5-8 0C6 2 5 2 5 2c-.3 1.15-.3 2.35 0 3.5A5.403 5.403 0 0 0 4 9c0 3.5 3 5.5 6 5.5-.39.49-.68 1.05-.85 1.65-.17.6-.22 1.23-.15 1.85v4"/><path d="M9 18c-4.51 2-5-2-7-2"/></svg>
                <span class="accent-color" style="opacity: 0.8;">{{ $metrics['git']['hash'] }}</span>
            </div>
            <div class="dx-v2-sys-dash-header-meta-item" style="display: flex; align-items: center; gap: 6px;">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="success-color"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                <span class="success-color" style="opacity: 0.8;">{{ $metrics['git']['date'] }}</span>
            </div>
        </div>
    </div>
</div>
<!-- Extra Fonts for Admin NOC -->
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@600;700&display=swap" rel="stylesheet">

<div class="dashboard-container">
    <div class="dx-v2-sys-dash-stats-grid">
        {{-- System Load --}}
        <div class="dx-v2-sys-dash-stat-card">
            <div class="dx-v2-sys-dash-stat-card-watermark">
                <svg width="84" height="84" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="4" y="4" width="16" height="16" rx="2"/><rect x="9" y="9" width="6" height="6"/><path d="M15 2v2M15 20v2M2 15h2M2 9h2M20 15h2M20 9h2M9 2v2M9 20v2"/></svg>
            </div>
            <div class="dx-v2-sys-dash-stat-card-title">
                CPU LOAD (1M)
            </div>
            <div class="dx-v2-sys-dash-stat-card-value">
                {{ $metrics['os']['load']['1m'] }}
            </div>
            <div class="dx-v2-sys-dash-stat-card-meta-mono">
                5M: {{ $metrics['os']['load']['5m'] }} <span class="dx-v2-sys-dash-dot-separator">·</span> 15M: {{ $metrics['os']['load']['15m'] }}
            </div>
        </div>

        {{-- Memory Usage --}}
        <div class="dx-v2-sys-dash-stat-card">
            <div class="dx-v2-sys-dash-stat-card-watermark">
                <svg width="84" height="84" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M6 19v2M9 19v2M12 19v2M15 19v2M18 19v2M21 15V9a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2ZM6 5V3M9 5V3M12 5V3M15 5V3M18 5V3"/></svg>
            </div>
            <div class="dx-v2-sys-dash-stat-card-title">
                RAM MEMORY
            </div>
            <div class="dx-v2-sys-dash-stat-card-value {{ $metrics['hardware']['memory']['percent'] > 85 ? 'danger-status' : '' }}">
                {{ $metrics['hardware']['memory']['percent'] }}<span class="percent-unit">%</span>
            </div>
            <div class="dx-v2-sys-dash-stat-card-progress-footer">
                <div class="dx-v2-sys-dash-stat-card-progress-bar">
                    <div class="dx-v2-sys-dash-stat-card-progress-bar-inner" style="width: {{ $metrics['hardware']['memory']['percent'] }}%;"></div>
                </div>
                <div class="dx-v2-sys-dash-stat-card-progress-text">
                    <span>{{ $metrics['hardware']['memory']['used'] }}</span>
                    <span class="separator">/</span>
                    <span>{{ $metrics['hardware']['memory']['total'] }}</span>
                </div>
            </div>
        </div>

        {{-- Storage --}}
        <div class="dx-v2-sys-dash-stat-card">
            <div class="dx-v2-sys-dash-stat-card-watermark">
                <svg width="84" height="84" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M22 12H2M5.45 5.11 2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11zM6 16h.01M10 16h.01"/></svg>
            </div>
            <div class="dx-v2-sys-dash-stat-card-title">
                STORAGE (BETA + PROD)
            </div>
            <div class="dx-v2-sys-dash-stat-card-value">
                {{ $metrics['hardware']['disk']['folders']['total'] }}
            </div>
            <div class="dx-v2-sys-dash-stat-card-meta-mono">
                BETA: {{ $metrics['hardware']['disk']['folders']['beta'] }} <span class="dx-v2-sys-dash-dot-separator">·</span> PROD: {{ $metrics['hardware']['disk']['folders']['prod'] }}
            </div>
        </div>

        {{-- Network & Presence --}}
        <div class="dx-v2-sys-dash-stat-card">
            <div class="dx-v2-sys-dash-stat-card-watermark">
                <svg width="84" height="84" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10M9 12l2 2 4-4"/></svg>
            </div>
            <div class="dx-v2-sys-dash-stat-card-title">
                TRAFFIC & PRESENCE
            </div>
            <div class="dx-v2-sys-dash-stat-card-traffic-layout">
                <div class="dx-v2-sys-dash-stat-card-traffic-col-left">
                    <div class="dx-v2-sys-dash-stat-card-traffic-sub-label">Network I/O</div>
                    <div class="dx-v2-sys-dash-stat-card-traffic-value">
                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="7 13 12 18 17 13"/><polyline points="7 6 12 11 17 6"/></svg>
                        {{ $metrics['hardware']['network']['rx'] }}
                    </div>
                    <div class="dx-v2-sys-dash-stat-card-traffic-value">
                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="17 11 12 6 7 11"/><polyline points="17 18 12 13 7 18"/></svg>
                        {{ $metrics['hardware']['network']['tx'] }}
                    </div>
                </div>
                <div class="dx-v2-sys-dash-stat-card-traffic-separator"></div>
                <div class="dx-v2-sys-dash-stat-card-traffic-col-right">
                    <div class="dx-v2-sys-dash-stat-card-traffic-sub-label">Users Online</div>
                    <div class="dx-v2-sys-dash-stat-card-value success-status">
                        {{ $metrics['security']['active_sessions'] }}
                    </div>
                </div>
            </div>
            <div class="dx-v2-sys-dash-stat-card-traffic-footer">
                <span class="dx-v2-sys-dash-stat-card-traffic-footer-label">ETH0 STATISTICS</span>
                <span class="dx-v2-sys-dash-stat-card-traffic-footer-live">
                    <span class="dx-v2-sys-dash-dot-live"></span>
                    TUNNEL OK
                </span>
            </div>
        </div>
    </div>

    <div class="dx-v2-sys-dash-main-layout">
        <div class="dx-v2-sys-dash-main-col">
            {{-- Services Matrix --}}
            <div class="card">
                <div class="card-header">
                    <span class="card-title">Services Matrix</span>
                </div>
                <div class="card-body">
                    <div class="dx-v2-sys-dash-services-grid">
                        {{-- Categorized Services --}}
                        @foreach($metrics['services'] as $category => $items)
                            <div class="dx-v2-sys-dash-services-cat-row {{ $loop->first ? 'first-cat' : '' }}">
                                <div class="dx-v2-sys-dash-services-cat-wrapper">
                                    <div class="dx-v2-sys-dash-services-cat-icon">
                                        @if($category === 'Infrastructure')
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="2" y="2" width="20" height="8" rx="2" ry="2"></rect><rect x="2" y="14" width="20" height="8" rx="2" ry="2"></rect><line x1="6" y1="6" x2="6.01" y2="6"></line><line x1="6" y1="18" x2="6.01" y2="18"></line></svg>
                                        @elseif($category === 'Processors')
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline></svg>
                                        @else
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z"/></svg>
                                        @endif
                                    </div>
                                    <span class="dx-v2-sys-dash-services-cat-title">
                                        {{ $category === 'Infrastructure' ? 'Infraestructura' : ($category === 'Processors' ? 'Procesadores' : 'Inteligencia AI') }}
                                    </span>
                                    <div class="dx-v2-sys-dash-services-cat-line"></div>
                                </div>
                            </div>
                            @foreach($items as $id => $info)
                                <div class="dx-v2-sys-dash-service-item">
                                    <div class="dx-v2-sys-dash-service-icon-box {{ $info['status'] }} {{ $info['icon'] }}">
                                        @if($info['icon'] === 'database')
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/><path d="M3 12c0 1.66 4 3 9 3s9-1.34 9-3"/></svg>
                                        @elseif($info['icon'] === 'bolt')
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M13 2 3 14h9l-1 8 10-12h-9l1-8z"/></svg>
                                        @elseif($info['icon'] === 'cpu')
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="4" y="4" width="16" height="16" rx="2"/><rect x="9" y="9" width="6" height="6"/><path d="M15 2v2M15 20v2M2 15h2M2 9h2M20 15h2M20 9h2M9 2v2M9 20v2"/></svg>
                                        @elseif($info['icon'] === 'bell')
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg>
                                        @elseif($info['icon'] === 'gemini')
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M12 2C12 2 12.6 7.4 14.5 9.5C16.6 11.4 22 12 22 12C22 12 16.6 12.6 14.5 14.5C12.6 16.6 12 22 12 22C12 22 11.4 16.6 9.5 14.5C7.4 12.6 2 12 2 12C2 12 7.4 11.4 9.5 9.5C11.4 7.4 12 2 12 2Z" fill="currentColor"/>
                                            </svg>
                                        @elseif($info['icon'] === 'deepseek')
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                                <path d="M12 4.5V19.5M4.5 12H19.5M8 8L16 16M16 8L8 16" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        @elseif($info['icon'] === 'openrouter' || $info['icon'] === 'cloud')
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                                <circle cx="12" cy="12" r="9"/>
                                                <path d="M3.6 9h16.8M3.6 15h16.8"/>
                                                <path d="M11.5 3a17 17 0 0 0 0 18M12.5 3a17 17 0 0 1 0 18"/>
                                            </svg>
                                        @elseif($info['icon'] === 'route')
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                <circle cx="6" cy="19" r="3"/>
                                                <path d="M9 19h8.5a3.5 3.5 0 0 0 0-7h-11a3.5 3.5 0 0 1 0-7H15"/>
                                                <circle cx="18" cy="5" r="3"/>
                                            </svg>
                                        @elseif($info['icon'] === 'n8n')
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                <circle cx="12" cy="12" r="3"/>
                                                <circle cx="19" cy="5" r="3"/>
                                                <circle cx="5" cy="19" r="3"/>
                                                <path d="M14.5 9.5 16.5 7.5M7.5 16.5 9.5 14.5"/>
                                            </svg>
                                        @elseif($info['icon'] === 'telegram')
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                <line x1="22" y1="2" x2="11" y2="13"></line>
                                                <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                                            </svg>
                                        @endif
                                    </div>
                                    <div class="dx-v2-sys-dash-service-text-box">
                                        <div class="dx-v2-sys-dash-service-title-row">
                                            <span class="dx-v2-sys-dash-service-label">{{ $info['label'] }}</span>
                                            <span class="dx-v2-sys-dash-service-status-dot {{ $info['status'] === 'online' ? 'online' : 'danger' }}"></span>
                                        </div>
                                        <div class="dx-v2-sys-dash-service-msg">{{ $info['message'] }}</div>
                                        @if(isset($info['details']))
                                            <div class="dx-v2-sys-dash-service-details-mono">{{ $info['details'] }}</div>
                                        @endif
                                        @if(isset($info['extra']))
                                            <div class="dx-v2-sys-dash-service-extra-row">
                                                <div title="Active Conn" class="dx-v2-sys-dash-service-extra-item">
                                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                                                    {{ $info['extra']['threads'] }}
                                                </div>
                                                <div title="Slow Queries" class="dx-v2-sys-dash-service-extra-item {{ $info['extra']['slow_queries'] > 0 ? 'warning' : '' }}">
                                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
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
            <div class="dx-v2-sys-dash-modules-grid">
                <a href="{{ route('admin.system.docker') }}" class="dx-v2-sys-dash-module-card">
                    <div class="dx-v2-sys-dash-module-header">
                        <div class="dx-v2-sys-dash-module-icon-box docker-brand">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
                        </div>
                        <span class="dx-v2-sys-dash-module-title">Docker Monitor</span>
                    </div>
                    <p class="dx-v2-sys-dash-module-desc">Salud de contenedores, telemetría de CPU/RAM y gestión de servicios en tiempo real.</p>
                </a>
                
                <a href="{{ route('admin.backups.index') }}" class="dx-v2-sys-dash-module-card">
                    <div class="dx-v2-sys-dash-module-header">
                        <div class="dx-v2-sys-dash-module-icon-box backups-brand">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3"/></svg>
                        </div>
                        <span class="dx-v2-sys-dash-module-title">Gestión de Backups</span>
                    </div>
                    <p class="dx-v2-sys-dash-module-desc">Historial completo, descargas y gestión de espacio en disco para copias de seguridad.</p>
                </a>
                
                <a href="{{ route('admin.audit.index') }}" class="dx-v2-sys-dash-module-card">
                    <div class="dx-v2-sys-dash-module-header">
                        <div class="dx-v2-sys-dash-module-icon-box audit-brand">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                        </div>
                        <span class="dx-v2-sys-dash-module-title">Auditoría y Logs</span>
                    </div>
                    <p class="dx-v2-sys-dash-module-desc">Trazabilidad total con filtros avanzados por usuario, acción, nivel e IP.</p>
                </a>
                
                <a href="{{ route('admin.system.ai-costs') }}" class="dx-v2-sys-dash-module-card">
                    <div class="dx-v2-sys-dash-module-header">
                        <div class="dx-v2-sys-dash-module-icon-box ai-brand" style="color: var(--dx-v2-accent);">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
                        </div>
                        <span class="dx-v2-sys-dash-module-title">Costes IA</span>
                    </div>
                    <p class="dx-v2-sys-dash-module-desc">Monitorización de tokens, telemetría y costes de motores IA (Gemini, DeepSeek).</p>
                </a>

                <a href="{{ route('admin.system.ai-routing.index') }}" class="dx-v2-sys-dash-module-card">
                    <div class="dx-v2-sys-dash-module-header">
                        <div class="dx-v2-sys-dash-module-icon-box" style="color: #10B981; background: rgba(16,185,129,0.1);">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="6" cy="19" r="3"/>
                                <path d="M9 19h8.5a3.5 3.5 0 0 0 0-7h-11a3.5 3.5 0 0 1 0-7H15"/>
                                <circle cx="18" cy="5" r="3"/>
                            </svg>
                        </div>
                        <span class="dx-v2-sys-dash-module-title">AI Routing Hub</span>
                    </div>
                    <p class="dx-v2-sys-dash-module-desc">Gestor de OpenRouter: Enrutador de tareas, asignación primaria y sistema de fallback.</p>
                </a>
            </div>
        </div>

        {{-- System Sidebar --}}
        <div class="dx-v2-sys-dash-sidebar">
            {{-- Quick Actions --}}
            <div class="card" x-data="systemActions()">
                <div class="card-header">
                    <span class="card-title">Acciones Rápidas</span>
                </div>
                <div class="dx-v2-sys-dash-sidebar-actions-box">
                    <button @click="execute('{{ route('admin.system.clear-cache') }}', 'Limpiar caché')" class="dx-v2-sys-dash-btn-noc accent-btn" :disabled="loading">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M10 11v6M14 11v6"/></svg>
                        <span>Limpiar Caché</span>
                    </button>
                    <button @click="execute('{{ route('admin.system.restart-queues') }}', 'Reiniciar workers')" class="dx-v2-sys-dash-btn-noc indigo-btn" :disabled="loading">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M23 4v6h-6M1 20v-6h6M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/></svg>
                        <span>Reiniciar Workers</span>
                    </button>
                    <button @click="execute('{{ route('admin.backups.run') }}', 'Generar backup')" class="dx-v2-sys-dash-btn-noc warn-btn" :disabled="loading">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3"/></svg>
                        <span>Backup MariaDB</span>
                    </button>
                    
                    <button @click="execute('{{ route('admin.system.toggle-maintenance') }}', 'Modo mantenimiento')" class="dx-v2-sys-dash-btn-noc" :disabled="loading" :class="metrics['os']['maintenance'] ? 'success-btn' : 'danger-btn'">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        <span>{{ $metrics['os']['maintenance'] ? 'Desactivar Mantenimiento' : 'Modo Mantenimiento' }}</span>
                    </button>
                    <button @click="execute('{{ route('admin.system.test-telegram') }}', 'Test Telegram')" class="dx-v2-sys-dash-btn-noc orange-btn" :disabled="loading">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m22 2-7 20-4-9-9-4Z"/><path d="M22 2 11 13"/></svg>
                        <span>Probar Alertas</span>
                    </button>
                </div>
            </div>

            {{-- Security & Traffic Stats --}}
            <div class="card">
                <div class="card-header">
                    <span class="card-title">Seguridad y Tráfico</span>
                </div>
                <div class="dx-v2-sys-dash-sec-box">
                    <div class="dx-v2-sys-dash-sec-layout">
                        <div class="dx-v2-sys-dash-sec-row">
                            <span class="dx-v2-sys-dash-sec-label">Errores Críticos (24h)</span>
                            <span class="dx-v2-sys-dash-sec-value {{ $metrics['errors_24h'] > 0 ? 'danger-text' : '' }}">{{ $metrics['errors_24h'] }}</span>
                        </div>
                        <div class="dx-v2-sys-dash-sec-row">
                            <span class="dx-v2-sys-dash-sec-label">Logins Fallidos (24h)</span>
                            <span class="dx-v2-sys-dash-sec-value {{ $metrics['security']['failed_logins_24h'] > 0 ? 'warn-text' : '' }}">{{ $metrics['security']['failed_logins_24h'] }}</span>
                        </div>
                        <div class="dx-v2-sys-dash-sec-row">
                            <span class="dx-v2-sys-dash-sec-label">JWT Blacklist</span>
                            <span class="dx-v2-sys-dash-sec-value">{{ $metrics['security']['blacklist_count'] }}</span>
                        </div>
                        <div class="dx-v2-sys-dash-sec-row no-border">
                            <span class="dx-v2-sys-dash-sec-label">Entorno PHP</span>
                            <span class="dx-v2-sys-dash-sec-value mono-text">v{{ $metrics['os']['php_version'] }}</span>
                        </div>
                    </div>
                    <div class="dx-v2-sys-dash-sec-footer-note">
                        Latido del sistema operativo. Workers escuchando en colas Redis <code class="dx-v2-sys-dash-sec-footer-code">queues:default</code>.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

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
                        // Notificación simple con clase premium centralizada
                        const toast = document.createElement('div');
                        toast.className = 'dx-v2-sys-dash-toast';
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
