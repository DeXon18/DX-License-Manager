@extends('layouts.app')

@section('title', 'Recursos ' . ucfirst($vendor))

@section('content')
<div class="page-header">
    <div class="breadcrumb">
        <a href="{{ url('/') }}">Portal</a> ›
        <a href="{{ route('tools.index') }}">Herramientas</a> ›
        Recursos {{ ucfirst($vendor) }}
    </div>
    <div style="display: flex; align-items: center; gap: 12px; margin-top: 8px;">
        <div class="tool-icon-fallback" style="background: {{ $vendor === 'siemens' ? 'var(--vendor-siemens-dark-muted, rgba(0,153,153,0.1))' : 'var(--warning-bg)' }}; color: {{ $vendor === 'siemens' ? 'var(--vendor-siemens, #009999)' : 'var(--warning)' }}; width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
        </div>
        <div>
            <h1 class="page-title" style="margin: 0;">Recursos y Enlaces <span class="vendor-label {{ $vendor }}" style="font-size: 10px; padding: 2px 6px; margin-left: 8px;">{{ ucfirst($vendor) }}</span></h1>
            <p class="page-sub" style="margin: 0;">Documentación oficial, portales de soporte y guías internas</p>
        </div>
    </div>
</div>

<div class="grid-main">
    <div class="main-panel">
        @include('tools.partials._resources', ['vendor' => $vendor, 'resources' => $resources])
    </div>

    <div class="sidebar-panel">
        <div style="background: var(--surface); border: 1px solid var(--border); padding: 20px; border-radius: 10px; margin-bottom: 16px;">
            <div style="font-size: 11px; font-weight: 700; color: var(--muted); text-transform: uppercase; margin-bottom: 12px;">Información</div>
            <p style="font-size: 12px; color: var(--secondary); line-height: 1.6; margin: 0;">
                Esta sección centraliza todos los recursos necesarios para la gestión técnica de licencias {{ ucfirst($vendor) }}. 
                Si echas en falta algún enlace, contacta con un Administrador o Técnico para añadirlo.
            </p>
        </div>

        @if(auth()->user()->role->slug !== 'viewer')
            <div style="background: var(--bg-subtle); border: 1px solid var(--border-subtle); padding: 16px; border-radius: 10px;">
                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px; color: var(--primary);">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    <span style="font-size: 12px; font-weight: 700;">Modo Edición</span>
                </div>
                <p style="font-size: 11px; color: var(--muted); line-height: 1.4;">
                    Como tienes permisos de {{ auth()->user()->role->label }}, puedes gestionar los enlaces pasando el ratón sobre cada tarjeta.
                </p>
            </div>
        @endif
    </div>
</div>
@endsection
