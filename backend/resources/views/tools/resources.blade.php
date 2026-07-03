@extends('layouts.app')

@section('title', 'Recursos ' . ucfirst($vendor))

@section('content')
<div class="page-header">
    <div class="breadcrumb">
        <a href="{{ url('/') }}">Portal</a> ›
        <a href="{{ route('tools.index') }}">Herramientas</a> ›
        Recursos {{ ucfirst($vendor) }}
    </div>
    <div class="dx-v2-resources-header-layout">
        <div class="dx-v2-resources-header-icon {{ $vendor }}">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
        </div>
        <div class="dx-v2-resources-title-block">
            <h1 class="dx-v2-resources-title">
                Recursos y Enlaces 
                <span class="dx-v2-resources-badge {{ $vendor }}">{{ ucfirst($vendor) }}</span>
            </h1>
            <p class="dx-v2-resources-subtitle">Documentación oficial, portales de soporte y guías internas</p>
        </div>
    </div>
</div>

<div class="grid-main">
    <div class="main-panel">
        @include('tools.partials._resources', ['vendor' => $vendor, 'resources' => $resources])
    </div>

    <div class="sidebar-panel">
        <div class="dx-v2-resources-sidebar-card">
            <div class="dx-v2-resources-sidebar-title">Información</div>
            <p class="dx-v2-resources-sidebar-text">
                Esta sección centraliza todos los recursos necesarios para la gestión técnica de licencias {{ ucfirst($vendor) }}. 
                Si echas en falta algún enlace, contacta con un Administrador o Técnico para añadirlo.
            </p>
        </div>

        @if(!auth()->user()->hasRole('viewer'))
            <div class="dx-v2-resources-sidebar-action">
                <div class="dx-v2-resources-sidebar-action-header">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    <span class="dx-v2-resources-sidebar-action-title">Modo Edición</span>
                </div>
                <p class="dx-v2-resources-sidebar-action-text">
                    Como tienes permisos de {{ ucfirst(auth()->user()->roles->first()?->name ?? 'edición') }}, puedes gestionar los enlaces pasando el ratón sobre cada tarjeta.
                </p>
            </div>
        @endif
    </div>
</div>
@endsection
