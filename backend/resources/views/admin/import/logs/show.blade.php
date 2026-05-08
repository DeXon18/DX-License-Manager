@extends('layouts.app')

@section('content')
<div class="page-header">
    <div class="breadcrumb">
        <a href="{{ route('admin.import.logs.index') }}">Historial de Logs</a>
        <span class="muted">/</span>
        <span class="current">Detalle de Importación</span>
    </div>
    <div class="flex justify-between items-center">
        <div>
            <h1 class="page-title">{{ $log->filename }}</h1>
            <p class="page-sub">Importado el {{ $log->created_at->format('d/m/Y \a \l\a\s H:i') }}</p>
        </div>
        <div class="flex gap-3">
            <span class="badge {{ $log->status === 'success' ? 'badge-success' : ($log->status === 'partial' ? 'badge-warn' : 'badge-danger') }}">
                {{ strtoupper($log->status) }}
            </span>
        </div>
    </div>
</div>

<div class="grid grid-cols-4 gap-6 mb-8">
    <div class="card p-5 text-center">
        <div class="tech-label mb-2">Total Filas</div>
        <div class="text-3xl font-bold">{{ $log->total_rows }}</div>
    </div>
    <div class="card p-5 text-center">
        <div class="tech-label mb-2">Procesadas OK</div>
        <div class="text-3xl font-bold text-accent">{{ $log->processed_rows }}</div>
    </div>
    <div class="card p-5 text-center">
        <div class="tech-label mb-2 text-danger">Errores</div>
        <div class="text-3xl font-bold {{ count($log->errors ?? []) > 0 ? 'text-danger' : 'muted' }}">
            {{ count($log->errors ?? []) }}
        </div>
    </div>
    <div class="card p-5 text-center">
        <div class="tech-label mb-2 text-warn">Avisos</div>
        <div class="text-3xl font-bold {{ count($log->warnings ?? []) > 0 ? 'text-warn' : 'muted' }}">
            {{ count($log->warnings ?? []) }}
        </div>
    </div>
</div>

<div class="grid grid-cols-1 gap-8">
    <!-- Errores Críticos -->
    @if(count($log->errors ?? []) > 0)
    <div class="card">
        <div class="card-header px-5 py-4 flex items-center gap-3">
            <i class="fa-solid fa-circle-xmark text-danger"></i>
            <h3 class="text-sm font-bold uppercase tracking-wider">Errores Críticos (No procesados)</h3>
        </div>
        <div class="p-0">
            <table class="table text-sm">
                <tbody>
                    @foreach($log->errors as $error)
                    <tr>
                        <td class="text-danger">
                            <i class="fa-solid fa-triangle-exclamation mr-2 opacity-50"></i>
                            {{ $error }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Avisos de Normalización (Sospechas) -->
    @if(count($log->warnings ?? []) > 0)
    <div class="card">
        <div class="card-header px-5 py-4 flex items-center gap-3 border-b border-white/5">
            <i class="fa-solid fa-wand-magic-sparkles text-warn"></i>
            <h3 class="text-sm font-bold uppercase tracking-wider">Avisos de Normalización e Inteligencia</h3>
        </div>
        <div class="p-0">
            <table class="table text-sm">
                <thead>
                    <tr>
                        <th class="px-5 py-3">Mensaje del Sistema</th>
                        <th class="text-right px-5 py-3">Acciones Sugeridas</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($log->warnings as $warning)
                    <tr>
                        <td class="px-5 py-4">
                            @if(str_contains($warning, 'sospecha') || str_contains($warning, 'parece'))
                                <div class="flex items-start gap-3">
                                    <span class="mt-1 flex-shrink-0 w-2 h-2 rounded-full bg-warn shadow-[0_0_8px_rgba(245,158,11,0.5)]"></span>
                                    <div>
                                        <span class="text-warn font-bold block mb-1">Sospecha de Duplicado Detectada</span>
                                        <span class="text-sm opacity-80">{{ $warning }}</span>
                                    </div>
                                </div>
                            @else
                                <div class="flex items-start gap-3">
                                    <span class="mt-1 flex-shrink-0 w-2 h-2 rounded-full bg-accent"></span>
                                    <div>
                                        <span class="text-accent font-bold block mb-1">Nuevo Cliente Registrado</span>
                                        <span class="text-sm opacity-80">{{ $warning }}</span>
                                    </div>
                                </div>
                            @endif
                        </td>
                        <td class="text-right px-5 py-4">
                            @if(str_contains($warning, 'sospecha') || str_contains($warning, 'parece'))
                                <div class="flex justify-end gap-2">
                                    <button class="btn-primary sm" onclick="alert('Funcionalidad de unificación en desarrollo (Paso 3 del plan)')">
                                        <i class="fa-solid fa-link mr-1"></i> Unificar
                                    </button>
                                    <button class="btn-secondary sm">
                                        <i class="fa-solid fa-check mr-1"></i> Ignorar
                                    </button>
                                </div>
                            @else
                                <span class="badge badge-muted">Automático</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection

@push('styles')
<style>
    .text-warn { color: #f59e0b; }
    .bg-warn { background-color: #f59e0b; }
    .badge-warn { background: rgba(245, 158, 11, 0.1); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.2); }
    
    .tech-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; opacity: 0.5; }
    
    .card-header { background: rgba(255, 255, 255, 0.02); }
    
    .btn-primary.sm { padding: 4px 12px; font-size: 11px; }
    .btn-secondary.sm { padding: 4px 12px; font-size: 11px; }
</style>
@endpush
