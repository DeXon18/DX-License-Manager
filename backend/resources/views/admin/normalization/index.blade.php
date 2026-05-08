@extends('layouts.app')

@section('title', 'Bandeja de Normalización — DX Control Center')

@section('content')
<div class="page-header">
    <div class="breadcrumb" style="margin-bottom: 8px;">
        <a href="{{ route('admin.import.index') }}" style="color: var(--accent); text-decoration: none;">Importación</a>
        <span style="margin: 0 8px; opacity: 0.3;">/</span>
        <span style="opacity: 0.5;">Bandeja de Normalización</span>
    </div>
    <h1 class="welcome">Bandeja de <span>Normalización</span></h1>
    <p class="welcome-sub">Gestión de identidades y duplicados detectados por el motor de sincronización</p>
</div>

<div class="stats-row">
    <div class="stat-card">
        <span class="stat-label">Sospechas Pendientes</span>
        <span class="stat-value {{ collect($findings)->where('type', 'suspicion')->count() > 0 ? 'warn' : '' }}">
            {{ collect($findings)->where('type', 'suspicion')->count() }}
        </span>
        <span class="stat-meta">Requieren unificación manual</span>
    </div>
    <div class="stat-card">
        <span class="stat-label">Nuevas Identidades</span>
        <span class="stat-value" style="color: var(--accent);">
            {{ collect($findings)->where('type', 'new')->count() }}
        </span>
        <span class="stat-meta">Registrados automáticamente</span>
    </div>
</div>

<div class="card" style="margin-top: 24px;">
    <div class="card-header">
        <span class="card-title">Análisis de Identidades</span>
    </div>
    
    @if(count($findings) > 0)
        <table>
            <thead>
                <tr>
                    <th style="width: 30%;">Hallazgo</th>
                    <th style="width: 40%;">Propuesta del Motor</th>
                    <th>Origen</th>
                    <th style="text-align: right;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($findings as $finding)
                    <tr>
                        <td>
                            <div style="font-weight: 700; color: var(--primary);">{{ $finding['detected_name'] }}</div>
                            @if($finding['type'] === 'suspicion')
                                <span class="badge badge-warn" style="font-size: 8px; padding: 2px 4px; margin-top: 4px;">SOSPECHA DUPLICADO</span>
                            @else
                                <span class="badge badge-success" style="font-size: 8px; padding: 2px 4px; margin-top: 4px;">NUEVA IDENTIDAD</span>
                            @endif
                        </td>
                        <td>
                            @if($finding['type'] === 'suspicion')
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <i class="fa-solid fa-arrow-right-long opacity-30"></i>
                                    <span style="font-weight: 700; color: var(--accent); font-size: 14px;">{{ $finding['suggested_name'] }}</span>
                                </div>
                                <div style="font-size: 11px; color: var(--muted); margin-top: 4px;">
                                    El motor sugiere unificar bajo este cliente existente.
                                </div>
                            @else
                                <div style="font-size: 11px; color: var(--muted);">
                                    Cliente desconocido hasta ahora. Se ha creado una ficha nueva.
                                </div>
                            @endif
                        </td>
                        <td>
                            <div class="date-main">{{ $finding['filename'] }}</div>
                            <div class="date-sub">{{ $finding['date']->format('d/m/Y H:i') }}</div>
                        </td>
                        <td style="text-align: right;">
                            @if($finding['type'] === 'suspicion')
                                <div style="display: flex; justify-content: flex-end; gap: 8px;">
                                    <form action="{{ route('admin.normalization.unify') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="detected_name" value="{{ $finding['detected_name'] }}">
                                        <input type="hidden" name="suggested_name" value="{{ $finding['suggested_name'] }}">
                                        <button type="submit" class="btn-primary" style="padding: 6px 12px; font-size: 11px;">
                                            UNIFICAR
                                        </button>
                                    </form>
                                    
                                    <form action="{{ route('admin.normalization.dismiss') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="full_message" value="{{ $finding['full_message'] }}">
                                        <button type="submit" class="btn-secondary" style="padding: 6px 12px; font-size: 11px; background: var(--raised);">
                                            DESCARTAR
                                        </button>
                                    </form>
                                </div>
                            @else
                                <span class="muted" style="font-size: 10px; font-weight: 700; text-transform: uppercase; opacity: 0.5;">Automatizado</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div style="padding: 60px; text-align: center;">
            <div style="font-size: 40px; margin-bottom: 20px;">🎉</div>
            <div style="font-weight: 700; color: var(--primary); font-size: 16px;">Todo en orden</div>
            <p style="color: var(--muted); font-size: 13px; margin-top: 8px; max-width: 400px; margin-left: auto; margin-right: auto;">
                No hay sospechas de duplicados pendientes de revisión. El motor de normalización está trabajando con una precisión del 100%.
            </p>
        </div>
    @endif
</div>

<div class="card" style="margin-top: 24px; border-left: 4px solid var(--accent);">
    <div style="padding: 20px;">
        <h3 style="font-size: 14px; font-weight: 700; color: var(--primary); margin-bottom: 8px;">¿Cómo funciona la unificación?</h3>
        <p style="font-size: 12px; color: var(--secondary); line-height: 1.6;">
            Al pulsar <strong>Unificar</strong>, el sistema realizará las siguientes acciones de forma atómica:
        </p>
        <ul style="font-size: 12px; color: var(--secondary); margin-top: 12px; list-style-type: disc; padding-left: 20px; line-height: 1.8;">
            <li>Registra <strong>{{ $findings[0]['detected_name'] ?? 'el nombre detectado' }}</strong> como un alias permanente de <strong>{{ $findings[0]['suggested_name'] ?? 'el cliente sugerido' }}</strong>.</li>
            <li>Transfiere automáticamente todos los contratos existentes del nombre detectado al cliente real.</li>
            <li>Elimina el registro redundante del cliente detectado para mantener la base de datos limpia.</li>
            <li>A partir de ahora, cualquier importación futura con ese nombre se mapeará directamente al cliente real sin intervención manual.</li>
        </ul>
    </div>
</div>
@endsection
