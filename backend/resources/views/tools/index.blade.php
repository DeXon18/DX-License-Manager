@extends('layouts.app')

@section('title', 'Herramientas')

@section('content')
<div class="page-header">
    <div class="breadcrumb">Portal › Herramientas</div>
    <h1 class="page-title">Herramientas de Licencia</h1>
    <p class="page-sub">Motores de transformación y utilidades avanzadas de licencias</p>
</div>

<!-- SIEMENS -->
@if(isset($features['Siemens']))
<div class="vendor-section vendor-siemens-group">
    <div class="vendor-header">
        <span class="vendor-label siemens">Siemens PLM</span>
        <span class="vendor-desc">Ecosistema de Digital Industries Software y Gestión PLM</span>
        <div class="vendor-line"></div>
    </div>
    <div class="tools-grid">
        @foreach($features['Siemens']->whereIn('key', ['siemens_nx_suite', 'siemens_star_ccm', 'siemens_heeds']) as $tool)
            <a class="tool-card {{ !$tool->is_active ? 'tool-disabled' : '' }}" 
               style="--card-accent: {{ $tool->key == 'siemens_nx_suite' ? '#c2570a' : ($tool->key == 'siemens_star_ccm' ? '#0369a1' : '#7e22ce') }}"
               href="{{ $tool->is_active ? ($tool->key == 'siemens_nx_suite' ? route('tools.nx-suite.index') : ($tool->key == 'siemens_star_ccm' ? route('tools.star-ccm.index') : ($tool->key == 'siemens_heeds' ? route('tools.heeds.index') : '#'))) : 'javascript:void(0)' }}">

                <div class="tool-card-header">
                    <div class="tool-card-header-left">
                        <div class="tool-icon-fallback" style="background: {{ $tool->key == 'siemens_nx_suite' ? 'rgba(194,87,10,0.08)' : ($tool->key == 'siemens_star_ccm' ? 'rgba(3,105,161,0.08)' : 'rgba(126,34,206,0.08)') }}; color: {{ $tool->key == 'siemens_nx_suite' ? '#c2570a' : ($tool->key == 'siemens_star_ccm' ? '#0369a1' : '#7e22ce') }}">
                            @if($tool->key == 'siemens_nx_suite')
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/></svg>
                            @elseif($tool->key == 'siemens_star_ccm')
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                            @else
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14M4.93 4.93a10 10 0 0 0 0 14.14"/></svg>
                            @endif
                        </div>
                        <div class="tool-name">{{ $tool->label }}</div>
                    </div>
                    @if($tool->is_active)
                        <span class="tool-badge badge-ai">AI</span>
                    @else
                        <span class="tool-badge badge-neutral">Próximamente</span>
                    @endif
                </div>
                <div>
                    <div class="tool-desc">{{ $tool->description }}</div>
                </div>
                <div class="tool-footer">
                    <span class="tool-meta-item">Vendor: {{ strtoupper($tool->key == 'siemens_nx_suite' ? 'SALTD' : ($tool->key == 'siemens_star_ccm' ? 'CDLMD' : 'RCTECH')) }}</span>
                    <span class="tool-cta">{{ $tool->is_active ? 'Abrir →' : 'Bloqueado' }}</span>
                </div>
            </a>
        @endforeach
    </div>
</div>

<!-- DOCUMENTOS -->
<div class="vendor-section">
    <div class="vendor-header">
        <span class="vendor-label docs">Documentos · Recursos</span>
        <span class="vendor-desc">Documentación oficial y acceso a portales externos</span>
        <div class="vendor-line"></div>
    </div>
    <div class="tools-grid two-col">
        @foreach($features['Siemens']->whereIn('key', ['siemens_cod', 'siemens_recursos']) as $tool)
            <a class="tool-card {{ !$tool->is_active ? 'tool-disabled' : '' }}" 
               style="--card-accent: {{ $tool->key == 'siemens_cod' ? '#0284c7' : '#6d28d9' }}"
               href="{{ $tool->is_active ? ($tool->key == 'siemens_cod' ? route('tools.cod.index') : route('tools.siemens.resources')) : 'javascript:void(0)' }}">
                <div class="tool-card-header">
                    <div class="tool-card-header-left">
                        <div class="tool-icon-fallback" style="background: {{ $tool->key == 'siemens_cod' ? 'rgba(2,132,199,0.08)' : 'rgba(109,40,217,0.08)' }}; color: {{ $tool->key == 'siemens_cod' ? '#0284c7' : '#6d28d9' }}">
                            @if($tool->key == 'siemens_cod')
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                            @else
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
                            @endif
                        </div>
                        <div class="tool-name">{{ $tool->label }}</div>
                    </div>
                    @if($tool->key == 'siemens_cod')
                        <span class="tool-badge badge-doc">Documento oficial</span>
                    @endif
                    @if(!$tool->is_active)
                        <span class="tool-badge badge-neutral">Próximamente</span>
                    @endif
                </div>
                <div>
                    <div class="tool-desc">{{ $tool->description }}</div>
                </div>
                <div class="tool-footer">
                    <span class="tool-meta-item">{{ $tool->key == 'siemens_cod' ? 'PDF oficial Siemens · Trazabilidad activa' : 'Portales · Documentación · Descargas' }}</span>
                    <span class="tool-cta">{{ $tool->is_active ? ($tool->key == 'siemens_cod' ? 'Generar documento →' : 'Ver todos los recursos →') : 'Bloqueado' }}</span>
                </div>
            </a>
        @endforeach
    </div>
</div>
@endif

<!-- MOLDEX3D -->
@if(isset($features['Moldex3D']))
<div class="vendor-section vendor-moldex-group">
    <div class="vendor-header">
        <span class="vendor-label moldex">Moldex<span class="accent">3D</span></span>
        <span class="vendor-desc">Simulación Predictiva para el Diseño y Fabricación de Plásticos</span>
        <div class="vendor-line"></div>
    </div>
    <div class="tools-grid three-col">
        @foreach($features['Moldex3D'] as $tool)
            <a class="tool-card {{ !$tool->is_active ? 'tool-disabled' : '' }}" 
               style="--card-accent: var(--moldex)"
               href="{{ $tool->is_active ? ($tool->key == 'moldex3d_auditor' ? route('tools.moldex3d.index') : route('tools.moldex3d.resources')) : 'javascript:void(0)' }}">
                <div class="tool-card-header">
                    <div class="tool-card-header-left">
                        <div class="tool-icon-fallback" style="background: rgba(237,28,36,0.08); color: #ED1C24">
                            @if($tool->key == 'moldex3d_auditor')
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20"/><path d="m4.93 4.93 14.14 14.14"/><path d="M2 12h20"/><path d="m19.07 4.93-14.14 14.14"/></svg>
                            @else
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
                            @endif
                        </div>
                        <div class="tool-name">
                            @if($tool->vendor == 'Moldex3D' && $tool->key == 'moldex3d_auditor')
                                Moldex<span class="accent">3D</span>
                            @else
                                {{ $tool->label }}
                            @endif
                        </div>
                    </div>
                    @if(!$tool->is_active)
                        <span class="tool-badge badge-neutral">Próximamente</span>
                    @endif
                </div>
                <div>
                    <div class="tool-desc">{{ $tool->description }}</div>
                </div>
                <div class="tool-footer">
                    <span class="tool-meta-item">{{ $tool->key == 'moldex3d_auditor' ? 'Compatible con .mac · Parser local' : 'Portales · Documentación · Descargas' }}</span>
                    <span class="tool-cta">{{ $tool->is_active ? 'Abrir →' : 'Bloqueado' }}</span>
                </div>
            </a>
        @endforeach
        
        <div class="tool-card-add">
            <div class="add-icon">+</div>
            <div class="add-label">Añadir utilidad Moldex3D</div>
        </div>
    </div>
</div>
@endif

@endsection

@push('styles')
<style>
    .tool-disabled {
        opacity: 0.6;
        cursor: not-allowed;
        filter: grayscale(0.5);
    }
    .tool-disabled:hover {
        transform: none !important;
        box-shadow: none !important;
    }
    .three-col {
        grid-template-columns: repeat(3, 1fr) !important;
    }
    .moldex-logo, .moldex .accent, .vendor-moldex-group .tool-name .accent { font-weight: 800; }
    .moldex-logo, .vendor-label.moldex, .vendor-moldex-group .tool-name { color: var(--moldex) !important; }
    .vendor-siemens-group .tool-name { color: var(--vendor-siemens, #009999) !important; }
    .accent { color: #f58220 !important; }
</style>
@endpush
