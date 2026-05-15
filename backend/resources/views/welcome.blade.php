@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="page-header">
    <div class="breadcrumb">
        <a href="#">Inicio</a>
        <span>/</span>
        <span>Dashboard</span>
    </div>
    <h1 class="welcome">Bienvenido, <span>{{ explode(' ', Auth::user()->name ?? 'Técnico')[0] }}</span></h1>
    <p class="welcome-sub">Estado actual del ecosistema · Última actualización: hoy</p>
</div>

<div class="stats-row">
    <div class="stat-card">
        <span class="stat-label">Licencias Activas</span>
        <span class="stat-value accent">124</span>
        <span class="stat-meta">+12% este mes</span>
    </div>
    <div class="stat-card warn">
        <span class="stat-label">Caducidades (30 días)</span>
        <span class="stat-value warn">8</span>
        <span class="stat-meta">Acción necesaria</span>
    </div>
    <div class="stat-card">
        <span class="stat-label">Auditorías IA (Hoy)</span>
        <span class="stat-value accent">42</span>
        <span class="stat-meta">Rendimiento normal</span>
    </div>
    <div class="stat-card">
        <span class="stat-label">Clientes en Sistema</span>
        <span class="stat-value">56</span>
        <span class="stat-meta">Base instalada total</span>
    </div>
</div>

<div class="grid-2">
    <!-- Demo Content Area -->
    <div class="card" style="--accent: var(--siemens)">
        <div class="card-header">
            <span class="card-title">Módulo de Gráficos</span>
        </div>
        <div class="resultado-placeholder">
            <div class="resultado-placeholder-icon">🏗️</div>
            <p><strong>Módulo en Construcción</strong></p>
            <p class="welcome-sub">Estamos portando el sistema de visualización de datos a Laravel 11. Pronto verás aquí los reportes interactivos.</p>
        </div>
    </div>

    <!-- Actividad Reciente -->
    <div class="card">
        <div class="card-header">
            <span class="card-title">Actividad Reciente</span>
        </div>
        <div style="padding: 16px;">
            <div class="producto-list">
                <div class="producto-item">
                    <div class="dot online"></div>
                    <div>
                        <strong>Auditoría completada</strong>
                        <div class="page-sub">Cliente: Industrias X — Hace 5m</div>
                    </div>
                </div>
                <div class="producto-item">
                    <div class="dot online" style="background: var(--success)"></div>
                    <div>
                        <strong>Licencia renovada</strong>
                        <div class="page-sub">Contrato: #AX-2024 — Hace 2h</div>
                    </div>
                </div>
                <div class="producto-item">
                    <div class="dot online" style="background: var(--warning)"></div>
                    <div>
                        <strong>Alerta de caducidad</strong>
                        <div class="page-sub">NX Core — 15 días restantes</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
