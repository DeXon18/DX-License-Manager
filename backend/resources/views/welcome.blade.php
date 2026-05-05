@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Breadcrumbs -->
    <nav class="flex text-sm text-muted mb-6">
        <span class="hover:text-primary cursor-pointer">Inicio</span>
        <span class="mx-2">/</span>
        <span class="text-foreground font-medium">Dashboard</span>
    </nav>

    <!-- Welcome Header -->
    <div class="flex justify-between items-end mb-8">
        <div>
            <h1 class="text-3xl font-bold tracking-tight mb-2">Bienvenido, {{ explode(' ', Auth::user()->name ?? 'Técnico')[0] }}</h1>
            <p class="text-muted">Aquí tienes el resumen del estado de las licencias para hoy.</p>
        </div>
        <div class="flex gap-3">
            <button class="px-4 py-2 bg-secondary border border-border rounded-lg text-sm font-medium hover:bg-primary/5 transition-all">
                Exportar Reporte
            </button>
            <button class="px-4 py-2 bg-primary text-primary-foreground rounded-lg text-sm font-medium hover:opacity-90 transition-all shadow-lg shadow-primary/20">
                + Nueva Auditoría
            </button>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-secondary p-6 rounded-2xl border border-border">
            <div class="text-muted text-sm font-medium mb-4">Licencias Activas</div>
            <div class="flex items-end justify-between">
                <div class="text-3xl font-bold">124</div>
                <div class="text-green-500 text-xs font-bold bg-green-500/10 px-2 py-1 rounded-full">+12%</div>
            </div>
        </div>
        <div class="bg-secondary p-6 rounded-2xl border border-border">
            <div class="text-muted text-sm font-medium mb-4">Caducidades (30 días)</div>
            <div class="flex items-end justify-between">
                <div class="text-3xl font-bold">8</div>
                <div class="text-yellow-500 text-xs font-bold bg-yellow-500/10 px-2 py-1 rounded-full">Alerta</div>
            </div>
        </div>
        <div class="bg-secondary p-6 rounded-2xl border border-border">
            <div class="text-muted text-sm font-medium mb-4">Auditorías IA (Hoy)</div>
            <div class="flex items-end justify-between">
                <div class="text-3xl font-bold">42</div>
                <div class="text-primary text-xs font-bold bg-primary/10 px-2 py-1 rounded-full">Normal</div>
            </div>
        </div>
        <div class="bg-secondary p-6 rounded-2xl border border-border">
            <div class="text-muted text-sm font-medium mb-4">Clientes en Sistema</div>
            <div class="flex items-end justify-between">
                <div class="text-3xl font-bold">56</div>
                <div class="text-muted text-xs font-bold bg-muted/10 px-2 py-1 rounded-full">Total</div>
            </div>
        </div>
    </div>

    <!-- Demo Content Area -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-secondary rounded-2xl border border-border p-8 min-h-[400px] flex flex-col items-center justify-center text-center">
            <div class="w-16 h-16 bg-surface rounded-full flex items-center justify-center text-3xl mb-4 border border-border">🏗️</div>
            <h3 class="text-xl font-bold mb-2">Módulo de Gráficos en Construcción</h3>
            <p class="text-muted max-w-sm">Estamos portando el sistema de visualización de datos a Laravel 11. Pronto verás aquí los reportes interactivos.</p>
        </div>
        <div class="bg-secondary rounded-2xl border border-border p-8">
            <h3 class="text-lg font-bold mb-6">Actividad Reciente</h3>
            <div class="space-y-6">
                <div class="flex gap-4">
                    <div class="w-2 h-2 rounded-full bg-primary mt-2"></div>
                    <div>
                        <div class="text-sm font-medium">Auditoría completada</div>
                        <div class="text-xs text-muted">Cliente: Industrias X — Hace 5m</div>
                    </div>
                </div>
                <div class="flex gap-4">
                    <div class="w-2 h-2 rounded-full bg-green-500 mt-2"></div>
                    <div>
                        <div class="text-sm font-medium">Licencia renovada</div>
                        <div class="text-xs text-muted">Contrato: #AX-2024 — Hace 2h</div>
                    </div>
                </div>
                <div class="flex gap-4">
                    <div class="w-2 h-2 rounded-full bg-yellow-500 mt-2"></div>
                    <div>
                        <div class="text-sm font-medium">Alerta de caducidad</div>
                        <div class="text-xs text-muted">NX Core — 15 días restantes</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
