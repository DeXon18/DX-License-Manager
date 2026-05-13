@extends('layouts.app')

@section('title', 'Configuración de Alertas')

@section('content')
@if(session('success'))
<div style="background: var(--success-bg); color: var(--success); border: 1px solid var(--success-border); padding: 12px 20px; border-radius: 8px; margin-bottom: 24px; display: flex; align-items: center; gap: 12px;">
    <i class="fa-solid fa-circle-check"></i>
    <span>{{ session('success') }}</span>
</div>
@endif

@if(session('error'))
<div style="background: var(--danger-bg); color: var(--danger); border: 1px solid var(--danger-border); padding: 12px 20px; border-radius: 8px; margin-bottom: 24px; display: flex; align-items: center; gap: 12px;">
    <i class="fa-solid fa-circle-xmark"></i>
    <span>{{ session('error') }}</span>
</div>
@endif

<div class="page-header" style="display: flex; justify-content: space-between; align-items: flex-start;">
    <div>
        <h1 class="page-title">Alertas y Notificaciones</h1>
        <p class="page-sub">Gestión de umbrales y reporte semanal de caducidad de licencias.</p>
    </div>
    <div style="display: flex; gap: 12px;">
        <form action="{{ route('admin.alerts.toggle') }}" method="POST">
            @csrf
            <button type="submit" class="btn {{ $settings->is_active ? 'badge-success' : 'badge-danger' }}" style="padding: 8px 16px; border-radius: 6px; font-weight: 600; cursor: pointer; border: none;">
                <i class="fa-solid fa-power-off" style="margin-right: 8px;"></i>
                {{ $settings->is_active ? 'Sistema Activo' : 'Sistema Desactivado' }}
            </button>
        </form>
        <form action="{{ route('admin.system.send-weekly-alerts') }}" method="POST">
            @csrf
            <button type="submit" class="btn-primary">
                <i class="fa-solid fa-paper-plane" style="margin-right: 8px;"></i>
                Test Envío Manual
            </button>
        </form>
    </div>
</div>

<div style="display: grid; grid-template-columns: 380px 1fr; gap: 24px; align-items: start;">
    <!-- Configuración de Umbrales -->
    <div class="card" style="--accent: var(--accent)">
        <div class="card-header">
            <span class="card-title">Configuración de Umbrales</span>
        </div>
        <div style="padding: 24px;">
            <form action="{{ route('admin.alerts.update') }}" method="POST">
                @csrf
                <div style="display: flex; flex-direction: column; gap: 20px;">
                    <div>
                        <label class="card-title" style="display: block; margin-bottom: 8px;">Alerta Crítica (Días)</label>
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <span class="badge badge-danger" style="width: 50px; text-align: center;">0-7</span>
                            <input type="number" name="threshold_alerta" value="{{ $settings->threshold_alerta }}" class="gui-input" style="flex: 1;" required>
                        </div>
                    </div>
                    
                    <div>
                        <label class="card-title" style="display: block; margin-bottom: 8px;">Aviso Preventivo (Días)</label>
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <span class="badge badge-warning" style="width: 50px; text-align: center;">7-15</span>
                            <input type="number" name="threshold_aviso" value="{{ $settings->threshold_aviso }}" class="gui-input" style="flex: 1;" required>
                        </div>
                    </div>

                    <div>
                        <label class="card-title" style="display: block; margin-bottom: 8px;">Recordatorio (Días)</label>
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <span class="badge badge-primary" style="width: 50px; text-align: center;">15-30</span>
                            <input type="number" name="threshold_recordatorio" value="{{ $settings->threshold_recordatorio }}" class="gui-input" style="flex: 1;" required>
                        </div>
                    </div>

                    <div style="padding-top: 16px; border-top: 1px solid var(--border);">
                        <label class="card-title" style="display: block; margin-bottom: 8px;">Copia Interna (Emails)</label>
                        <textarea name="internal_copy_emails" rows="3" class="gui-input" style="width: 100%; font-family: var(--mono); font-size: 11px;" placeholder="email1@example.com, email2@example.com">{{ $settings->internal_copy_emails }}</textarea>
                        <p class="page-sub" style="margin-top: 6px; font-size: 10px;">Separar por comas para múltiples destinatarios.</p>
                    </div>

                    <div style="padding-top: 8px;">
                        <button type="submit" class="btn-primary" style="width: 100%; justify-content: center;">
                            Guardar Cambios
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Historial de Envíos -->
    <div class="card" style="--accent: var(--siemens)">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
            <span class="card-title">Historial de Notificaciones</span>
            <span class="badge badge-muted" style="font-size: 10px;">{{ $logs->total() }} envíos</span>
        </div>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: var(--raised);">
                    <th style="text-align: left; padding: 12px 20px; font-size: 11px; color: var(--muted); text-transform: uppercase; letter-spacing: 0.05em;">Destinatario</th>
                    <th style="text-align: left; padding: 12px 20px; font-size: 11px; color: var(--muted); text-transform: uppercase; letter-spacing: 0.05em;">Fecha Envío</th>
                    <th style="text-align: center; padding: 12px 20px; font-size: 11px; color: var(--muted); text-transform: uppercase; letter-spacing: 0.05em;">Estado</th>
                    <th style="text-align: right; padding: 12px 20px; font-size: 11px; color: var(--muted); text-transform: uppercase; letter-spacing: 0.05em;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                <tr style="border-bottom: 1px solid var(--border-subtle);">
                    <td style="padding: 14px 20px; font-weight: 500;">{{ $log->recipient }}</td>
                    <td style="padding: 14px 20px; font-family: var(--mono); font-size: 12px; color: var(--secondary);">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                    <td style="padding: 14px 20px; text-align: center;">
                        @if($log->status == 'sent')
                            <span class="badge badge-success" style="font-size: 10px;">ENVIADO</span>
                        @else
                            <span class="badge badge-danger" style="font-size: 10px;" title="{{ $log->error_message }}">FALLO</span>
                        @endif
                    </td>
                    <td style="padding: 14px 20px; text-align: right;">
                        <button class="btn-secondary" style="padding: 4px 8px; font-size: 11px;" title="Ver detalle">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="padding: 40px; text-align: center; color: var(--muted); font-style: italic;">
                        No se han registrado envíos de alertas todavía.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        
        @if($logs->hasPages())
        <div style="padding: 16px 24px; border-top: 1px solid var(--border);">
            {{ $logs->links() }}
        </div>
        @endif
    </div>
</div>

<div class="card" style="margin-top: 24px; border-left: 4px solid var(--accent); background: var(--accent-muted);">
    <div style="display: flex; align-items: flex-start; gap: 16px; padding: 20px;">
        <div style="background: var(--surface); color: var(--accent); width: 40px; height: 40px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 18px; border: 1px solid var(--accent-border);">
            <i class="fa-solid fa-info-circle"></i>
        </div>
        <div>
            <h3 style="font-size: 14px; font-weight: 600; color: var(--primary); margin-bottom: 4px;">Información del Motor de Alertas</h3>
            <p style="font-size: 13px; color: var(--secondary); line-height: 1.5; max-width: 800px;">
                El sistema procesa automáticamente las licencias del inventario cada **lunes a las 07:30 AM**. 
                Se envían correos únicamente a los contactos que tienen habilitada la opción "Recibe Alertas" en su ficha de cliente. 
                Las licencias se agrupan por cliente y nivel de urgencia según los umbrales configurados arriba.
            </p>
        </div>
    </div>
</div>
@endsection
