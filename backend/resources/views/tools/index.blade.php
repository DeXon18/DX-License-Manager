@extends('layouts.app')

@section('title', 'Herramientas')

@section('content')
<div class="dx-v2-page-header">
    <div>
        <div class="breadcrumb">
            <a href="{{ route('tools.index') }}">Portal</a>
            <span class="separator">/</span>
            <span class="current">Herramientas</span>
        </div>
        <h1 class="page-title">Utilidades y <span>Herramientas</span></h1>
        <p class="page-subtitle">Motores de transformación y utilidades avanzadas de licencias</p>
    </div>
</div>

<!-- SIEMENS -->
@if(isset($features['Siemens']))
<div class="dx-v2-tools-vendor-section">
    <div class="dx-v2-tools-vendor-header">
        <span class="dx-v2-tools-vendor-label siemens">Siemens PLM</span>
        <span class="dx-v2-tools-vendor-desc">Ecosistema de Digital Industries Software y Gestión PLM</span>
        <div class="dx-v2-tools-vendor-line"></div>
    </div>
    <div class="dx-v2-tools-grid dx-v2-tools-grid-3">
        @foreach($features['Siemens']->whereIn('key', ['siemens_nx_suite', 'siemens_star_ccm', 'siemens_heeds']) as $tool)
            @php
                $accentColor = match($tool->key) {
                    'siemens_nx_suite' => '#c2570a',
                    'siemens_star_ccm' => '#0369a1',
                    'siemens_heeds' => '#7e22ce',
                    default => '#009999'
                };
                $iconBg = match($tool->key) {
                    'siemens_nx_suite' => 'rgba(194,87,10,0.08)',
                    'siemens_star_ccm' => 'rgba(3,105,161,0.08)',
                    'siemens_heeds' => 'rgba(126,34,206,0.08)',
                    default => 'rgba(0,153,153,0.08)'
                };
            @endphp
            <a class="dx-v2-tools-card {{ !$tool->is_active ? 'dx-v2-tools-card-disabled' : '' }}" 
               style="--card-accent: {{ $accentColor }}; --icon-bg: {{ $iconBg }};"
               href="{{ $tool->is_active ? ($tool->key == 'siemens_nx_suite' ? route('tools.nx-suite.index') : ($tool->key == 'siemens_star_ccm' ? route('tools.star-ccm.index') : ($tool->key == 'siemens_heeds' ? route('tools.heeds.index') : '#'))) : 'javascript:void(0)' }}">

                <div class="dx-v2-tools-card-header">
                    <div class="dx-v2-tools-card-header-left">
                        <div class="dx-v2-tools-icon-box">
                            @if($tool->key == 'siemens_nx_suite')
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m21 16-9 5-9-5V8l9-5 9 5v8z"/><path d="M12 21v-9"/><path d="m21 8-9 4-9-4"/></svg>
                            @elseif($tool->key == 'siemens_star_ccm')
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12h10"/><path d="M9 4v16"/><path d="m3 9 3 3-3 3"/><path d="M22 12h-5"/><path d="m17 9-3 3 3 3"/></svg>
                            @else
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12h18"/><path d="M3 6h18"/><path d="M3 18h18"/><circle cx="7" cy="12" r="2"/><circle cx="12" cy="6" r="2"/><circle cx="17" cy="18" r="2"/></svg>
                            @endif
                        </div>
                        <div class="dx-v2-tools-card-name siemens-name">{{ $tool->label }}</div>
                    </div>
                    @if($tool->is_active)
                        <span class="dx-v2-tools-badge ai">AI</span>
                    @else
                        <span class="dx-v2-tools-badge upcoming">Próximamente</span>
                    @endif
                </div>
                <div>
                    <div class="dx-v2-tools-card-desc">{{ $tool->description }}</div>
                </div>
                <div class="dx-v2-tools-card-footer">
                    <span class="dx-v2-tools-card-meta">Vendor: {{ strtoupper($tool->key == 'siemens_nx_suite' ? 'SALTD' : ($tool->key == 'siemens_star_ccm' ? 'CDLMD' : 'RCTECH')) }}</span>
                    <span class="dx-v2-tools-card-cta">{{ $tool->is_active ? 'Abrir →' : 'Bloqueado' }}</span>
                </div>
            </a>
        @endforeach
    </div>
</div>

<!-- DOCUMENTOS -->
<div class="dx-v2-tools-vendor-section">
    <div class="dx-v2-tools-vendor-header">
        <span class="dx-v2-tools-vendor-label docs">Documentos · Recursos</span>
        <span class="dx-v2-tools-vendor-desc">Documentación oficial y acceso a portales externos</span>
        <div class="dx-v2-tools-vendor-line"></div>
    </div>
    <div class="dx-v2-tools-grid dx-v2-tools-grid-2">
        @foreach($features['Siemens']->whereIn('key', ['siemens_cod', 'siemens_recursos']) as $tool)
            @php
                $accentColor = ($tool->key == 'siemens_cod' ? '#0284c7' : '#6d28d9');
                $iconBg = ($tool->key == 'siemens_cod' ? 'rgba(2,132,199,0.08)' : 'rgba(109,40,217,0.08)');
            @endphp
            <a class="dx-v2-tools-card {{ !$tool->is_active ? 'dx-v2-tools-card-disabled' : '' }}" 
               style="--card-accent: {{ $accentColor }}; --icon-bg: {{ $iconBg }};"
               href="{{ $tool->is_active ? ($tool->key == 'siemens_cod' ? route('tools.cod.index') : route('tools.siemens.resources')) : 'javascript:void(0)' }}">
                <div class="dx-v2-tools-card-header">
                    <div class="dx-v2-tools-card-header-left">
                        <div class="dx-v2-tools-icon-box">
                            @if($tool->key == 'siemens_cod')
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                            @else
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
                            @endif
                        </div>
                        <div class="dx-v2-tools-card-name">{{ $tool->label }}</div>
                    </div>
                    @if($tool->key == 'siemens_cod')
                        <span class="dx-v2-tools-badge doc">Documento oficial</span>
                    @endif
                    @if(!$tool->is_active)
                        <span class="dx-v2-tools-badge upcoming">Próximamente</span>
                    @endif
                </div>
                <div>
                    <div class="dx-v2-tools-card-desc">{{ $tool->description }}</div>
                </div>
                <div class="dx-v2-tools-card-footer">
                    <span class="dx-v2-tools-card-meta">{{ $tool->key == 'siemens_cod' ? 'PDF oficial Siemens · Trazabilidad activa' : 'Portales · Documentación · Descargas' }}</span>
                    <span class="dx-v2-tools-card-cta">{{ $tool->is_active ? ($tool->key == 'siemens_cod' ? 'Generar documento →' : 'Ver todos los recursos →') : 'Bloqueado' }}</span>
                </div>
            </a>
        @endforeach
    </div>
</div>
@endif

<!-- MOLDEX3D -->
@if(isset($features['Moldex3D']))
<div class="dx-v2-tools-vendor-section">
    <div class="dx-v2-tools-vendor-header">
        <span class="dx-v2-tools-vendor-label moldex">Moldex<span class="accent">3D</span></span>
        <span class="dx-v2-tools-vendor-desc">Simulación Predictiva para el Diseño y Fabricación de Plásticos</span>
        <div class="dx-v2-tools-vendor-line"></div>
    </div>
    <div class="dx-v2-tools-grid dx-v2-tools-grid-3">
        @foreach($features['Moldex3D'] as $tool)
            <a class="dx-v2-tools-card {{ !$tool->is_active ? 'dx-v2-tools-card-disabled' : '' }}" 
               style="--card-accent: var(--dx-v2-moldex); --icon-bg: rgba(237, 28, 36, 0.08);"
               href="{{ $tool->is_active ? ($tool->key == 'moldex3d_auditor' ? route('tools.moldex3d.index') : route('tools.moldex3d.resources')) : 'javascript:void(0)' }}">
                <div class="dx-v2-tools-card-header">
                    <div class="dx-v2-tools-card-header-left">
                        <div class="dx-v2-tools-icon-box" style="--card-accent: #ED1C24;">
                            @if($tool->key == 'moldex3d_auditor')
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12h20"/><path d="M20 12c0-4.4-3.6-8-8-8s-8 3.6-8 8 3.6 8 8 8 8-3.6 8-8Z"/><path d="M12 2v2"/><path d="M12 20v2"/><path d="m4.9 4.9 1.4 1.4"/><path d="m17.7 17.7 1.4 1.4"/><path d="M2 12h2"/><path d="M20 12h2"/><path d="m4.9 19.1 1.4-1.4"/><path d="m17.7 6.3 1.4-1.4"/></svg>
                            @else
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
                            @endif
                        </div>
                        <div class="dx-v2-tools-card-name moldex-name">
                            @if($tool->vendor == 'Moldex3D' && $tool->key == 'moldex3d_auditor')
                                Moldex<span class="accent">3D</span>
                            @else
                                {{ $tool->label }}
                            @endif
                        </div>
                    </div>
                    @if(!$tool->is_active)
                        <span class="dx-v2-tools-badge upcoming">Próximamente</span>
                    @endif
                </div>
                <div>
                    <div class="dx-v2-tools-card-desc">{{ $tool->description }}</div>
                </div>
                <div class="dx-v2-tools-card-footer">
                    <span class="dx-v2-tools-card-meta">{{ $tool->key == 'moldex3d_auditor' ? 'Compatible con .mac · Parser local' : 'Portales · Documentación · Descargas' }}</span>
                    <span class="dx-v2-tools-card-cta">{{ $tool->is_active ? 'Abrir →' : 'Bloqueado' }}</span>
                </div>
            </a>
        @endforeach
        
        <div class="dx-v2-tools-card-add">
            <div class="dx-v2-tools-card-add-icon">+</div>
            <div class="dx-v2-tools-card-add-label">Añadir utilidad Moldex3D</div>
        </div>
    </div>
</div>
@endif

@endsection
