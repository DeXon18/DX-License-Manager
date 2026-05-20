@extends('layouts.app')

@section('title', 'Configuración de Alertas')

@section('content')
@if(session('success'))
<div class="dx-v2-alerts-alert-banner success">
    <i class="fa-solid fa-circle-check"></i>
    <span>{{ session('success') }}</span>
</div>
@endif

@if(session('error'))
<div class="dx-v2-alerts-alert-banner danger">
    <i class="fa-solid fa-circle-xmark"></i>
    <span>{{ session('error') }}</span>
</div>
@endif

<div class="page-header dx-v2-alerts-header-row">
    <div>
        <h1 class="page-title">Alertas y Notificaciones</h1>
        <p class="page-sub">Gestión de umbrales y reporte semanal de caducidad de licencias.</p>
    </div>
    <div class="dx-v2-alerts-btn-group">
        <form action="{{ route('admin.alerts.toggle') }}" method="POST">
            @csrf
            <button type="submit" class="btn {{ $settings->is_active ? 'badge-success' : 'badge-danger' }} dx-v2-alerts-toggle-btn">
                <i class="fa-solid fa-power-off mr-2"></i>
                {{ $settings->is_active ? 'Sistema Activo' : 'Sistema Desactivado' }}
            </button>
        </form>
        <form action="{{ route('admin.system.send-weekly-alerts') }}" method="POST">
            @csrf
            <button type="submit" class="btn-primary">
                <i class="fa-solid fa-paper-plane mr-2"></i>
                Test Envío Manual
            </button>
        </form>
    </div>
</div>

<div class="dx-v2-alerts-grid">
    <!-- Configuración de Umbrales -->
    <div class="card dx-v2-alerts-card-thresholds">
        <div class="card-header dx-v2-alerts-card-header">
            <div class="dx-v2-alerts-card-header-inner">
                <div class="dx-v2-alerts-icon-wrapper-sm">
                    <i class="fa-solid fa-bell-concierge"></i>
                </div>
                <span class="card-title">Configuración de Umbrales</span>
            </div>
        </div>
        <div class="dx-v2-alerts-body">
            <form action="{{ route('admin.alerts.update') }}" method="POST">
                @csrf
                <div class="dx-v2-alerts-threshold-list">
                    <!-- Crítica -->
                    <div class="dx-v2-alerts-threshold-item">
                        <div class="dx-v2-alerts-threshold-info">
                            <div class="dx-v2-alerts-threshold-icon-circle danger">
                                <i class="fa-solid fa-circle-exclamation"></i>
                            </div>
                            <div>
                                <h4 class="dx-v2-alerts-threshold-title">ALERTA CRÍTICA</h4>
                                <p class="dx-v2-alerts-threshold-desc">Rango actual: 0 - {{ $settings->threshold_alerta }} días</p>
                            </div>
                        </div>
                        <div class="dx-v2-alerts-input-container">
                            <input type="number" name="threshold_alerta" value="{{ $settings->threshold_alerta }}" class="dx-v2-alerts-input-field" required>
                            <span class="dx-v2-alerts-input-unit">DÍAS</span>
                        </div>
                    </div>
                    
                    <!-- Preventiva -->
                    <div class="dx-v2-alerts-threshold-item">
                        <div class="dx-v2-alerts-threshold-info">
                            <div class="dx-v2-alerts-threshold-icon-circle warning">
                                <i class="fa-solid fa-triangle-exclamation"></i>
                            </div>
                            <div>
                                <h4 class="dx-v2-alerts-threshold-title">AVISO PREVENTIVO</h4>
                                <p class="dx-v2-alerts-threshold-desc">Rango: {{ $settings->threshold_alerta + 1 }} - {{ $settings->threshold_aviso }} días</p>
                            </div>
                        </div>
                        <div class="dx-v2-alerts-input-container">
                            <input type="number" name="threshold_aviso" value="{{ $settings->threshold_aviso }}" class="dx-v2-alerts-input-field" required>
                            <span class="dx-v2-alerts-input-unit">DÍAS</span>
                        </div>
                    </div>

                    <!-- Recordatorio -->
                    <div class="dx-v2-alerts-threshold-item">
                        <div class="dx-v2-alerts-threshold-info">
                            <div class="dx-v2-alerts-threshold-icon-circle accent">
                                <i class="fa-solid fa-clock"></i>
                            </div>
                            <div>
                                <h4 class="dx-v2-alerts-threshold-title">RECORDATORIO</h4>
                                <p class="dx-v2-alerts-threshold-desc">Rango: {{ $settings->threshold_aviso + 1 }} - {{ $settings->threshold_recordatorio }} días</p>
                            </div>
                        </div>
                        <div class="dx-v2-alerts-input-container">
                            <input type="number" name="threshold_recordatorio" value="{{ $settings->threshold_recordatorio }}" class="dx-v2-alerts-input-field" required>
                            <span class="dx-v2-alerts-input-unit">DÍAS</span>
                        </div>
                    </div>
                </div>

                <div class="dx-v2-alerts-copy-box">
                    <div class="dx-v2-alerts-copy-header">
                        <label class="label dx-v2-alerts-copy-label">
                            <i class="fa-solid fa-envelope-open-text mr-2 text-accent"></i> COPIA INTERNA (EMAILS)
                        </label>
                    </div>
                    <textarea name="internal_copy_emails" rows="2" class="dx-v2-form-textarea dx-v2-alerts-copy-textarea" placeholder="email1@example.com, email2@example.com">{{ $settings->internal_copy_emails }}</textarea>
                    <p class="page-sub dx-v2-alerts-copy-help">
                        <i class="fa-solid fa-circle-info text-accent"></i> Separar por comas para múltiples destinatarios.
                    </p>
                </div>

                <div class="dx-v2-alerts-submit-wrapper">
                    <button type="submit" class="btn-primary dx-v2-alerts-submit-btn">
                        <i class="fa-solid fa-save mr-2"></i> Guardar Configuración
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Historial de Envíos -->
    <div class="card dx-v2-alerts-card-history">
        <div class="card-header dx-v2-alerts-card-history-header">
            <span class="card-title">Historial de Notificaciones</span>
            <span class="badge badge-muted">{{ $logs->total() }} envíos</span>
        </div>
        <table class="dx-v2-alerts-table">
            <thead>
                <tr class="dx-v2-alerts-table-thead-tr">
                    <th class="dx-v2-alerts-table-th">Destinatario</th>
                    <th class="dx-v2-alerts-table-th">Fecha Envío</th>
                    <th class="dx-v2-alerts-table-th text-center">Estado</th>
                    <th class="dx-v2-alerts-table-th text-right">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                <tr class="dx-v2-alerts-table-tbody-tr">
                    <td class="dx-v2-alerts-table-td-recipient">{{ $log->recipient }}</td>
                    <td class="dx-v2-alerts-table-td-date">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                    <td class="dx-v2-alerts-table-td-status">
                        @if($log->status == 'sent')
                            <span class="badge badge-success">ENVIADO</span>
                        @else
                            <span class="badge badge-danger" title="{{ $log->error_message }}">FALLO</span>
                        @endif
                    </td>
                    <td class="dx-v2-alerts-table-td-actions">
                        <button class="btn-secondary dx-v2-alerts-table-action-btn" title="Ver detalle">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="dx-v2-alerts-table-empty-td">
                        No se han registrado envíos de alertas todavía.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        
        @if($logs->hasPages())
        <div class="dx-v2-alerts-pagination-wrapper">
            {{ $logs->links() }}
        </div>
        @endif
    </div>
</div>

<div class="card dx-v2-alerts-info-box">
    <div class="dx-v2-alerts-info-box-inner">
        <div class="dx-v2-alerts-info-box-icon">
            <i class="fa-solid fa-info-circle"></i>
        </div>
        <div>
            <h3 class="dx-v2-alerts-info-box-title">Información del Motor de Alertas</h3>
            <p class="dx-v2-alerts-info-box-desc">
                El sistema procesa automáticamente las licencias del inventario cada **lunes a las 07:30 AM**. 
                Se envían correos únicamente a los contactos que tienen habilitada la opción "Recibe Alertas" en su ficha de cliente. 
                Las licencias se agrupan por cliente y nivel de urgencia según los umbrales configurados arriba.
            </p>
        </div>
    </div>
</div>
@endsection
