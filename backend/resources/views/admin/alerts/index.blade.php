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

<div style="display: grid; grid-template-columns: 1fr 1.5fr; gap: 24px; align-items: start;">
    <!-- Configuración de Umbrales -->
    <div class="card" style="--accent: var(--accent); overflow: hidden;">
        <div class="card-header" style="background: var(--raised); border-bottom: 1px solid var(--border);">
            <div style="display: flex; align-items: center; gap: 10px;">
                <div style="width: 32px; height: 32px; background: var(--accent-muted); color: var(--accent); border-radius: 6px; display: flex; align-items: center; justify-content: center;">
                    <i class="fa-solid fa-bell-concierge"></i>
                </div>
                <span class="card-title">Configuración de Umbrales</span>
            </div>
        </div>
        <div style="padding: 24px;">
            <form action="{{ route('admin.alerts.update') }}" method="POST">
                @csrf
                <div style="display: flex; flex-direction: column; gap: 16px; margin-bottom: 24px;">
                    <!-- Crítica -->
                    <div style="display: flex; align-items: center; justify-content: space-between; background: var(--bg); padding: 16px; border-radius: 10px; border: 1px solid var(--border-subtle);">
                        <div style="display: flex; align-items: center; gap: 16px;">
                            <div style="width: 40px; height: 40px; background: var(--danger-bg); color: var(--danger); border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 18px; border: 1px solid var(--danger-border);">
                                <i class="fa-solid fa-circle-exclamation"></i>
                            </div>
                            <div>
                                <h4 style="font-size: 13px; font-weight: 700; color: var(--primary); margin: 0;">ALERTA CRÍTICA</h4>
                                <p style="font-size: 11px; color: var(--muted); margin: 2px 0 0 0; font-family: var(--mono);">Rango actual: 0 - {{ $settings->threshold_alerta }} días</p>
                            </div>
                        </div>
                        <div style="display: flex; align-items: center; gap: 12px; background: var(--surface); padding: 6px 12px; border-radius: 8px; border: 1px solid var(--border);">
                            <input type="number" name="threshold_alerta" value="{{ $settings->threshold_alerta }}" class="gui-input" style="width: 60px; border: none; padding: 0; text-align: center; font-family: var(--mono); font-weight: 700; font-size: 16px; background: transparent;" required>
                            <span style="font-size: 10px; font-weight: 700; color: var(--muted); letter-spacing: 0.05em;">DÍAS</span>
                        </div>
                    </div>
                    
                    <!-- Preventiva -->
                    <div style="display: flex; align-items: center; justify-content: space-between; background: var(--bg); padding: 16px; border-radius: 10px; border: 1px solid var(--border-subtle);">
                        <div style="display: flex; align-items: center; gap: 16px;">
                            <div style="width: 40px; height: 40px; background: var(--warning-bg); color: var(--warning); border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 18px; border: 1px solid var(--warning-border);">
                                <i class="fa-solid fa-triangle-exclamation"></i>
                            </div>
                            <div>
                                <h4 style="font-size: 13px; font-weight: 700; color: var(--primary); margin: 0;">AVISO PREVENTIVO</h4>
                                <p style="font-size: 11px; color: var(--muted); margin: 2px 0 0 0; font-family: var(--mono);">Rango: {{ $settings->threshold_alerta + 1 }} - {{ $settings->threshold_aviso }} días</p>
                            </div>
                        </div>
                        <div style="display: flex; align-items: center; gap: 12px; background: var(--surface); padding: 6px 12px; border-radius: 8px; border: 1px solid var(--border);">
                            <input type="number" name="threshold_aviso" value="{{ $settings->threshold_aviso }}" class="gui-input" style="width: 60px; border: none; padding: 0; text-align: center; font-family: var(--mono); font-weight: 700; font-size: 16px; background: transparent;" required>
                            <span style="font-size: 10px; font-weight: 700; color: var(--muted); letter-spacing: 0.05em;">DÍAS</span>
                        </div>
                    </div>

                    <!-- Recordatorio -->
                    <div style="display: flex; align-items: center; justify-content: space-between; background: var(--bg); padding: 16px; border-radius: 10px; border: 1px solid var(--border-subtle);">
                        <div style="display: flex; align-items: center; gap: 16px;">
                            <div style="width: 40px; height: 40px; background: var(--accent-muted); color: var(--accent); border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 18px; border: 1px solid var(--accent-border);">
                                <i class="fa-solid fa-clock"></i>
                            </div>
                            <div>
                                <h4 style="font-size: 13px; font-weight: 700; color: var(--primary); margin: 0;">RECORDATORIO</h4>
                                <p style="font-size: 11px; color: var(--muted); margin: 2px 0 0 0; font-family: var(--mono);">Rango: {{ $settings->threshold_aviso + 1 }} - {{ $settings->threshold_recordatorio }} días</p>
                            </div>
                        </div>
                        <div style="display: flex; align-items: center; gap: 12px; background: var(--surface); padding: 6px 12px; border-radius: 8px; border: 1px solid var(--border);">
                            <input type="number" name="threshold_recordatorio" value="{{ $settings->threshold_recordatorio }}" class="gui-input" style="width: 60px; border: none; padding: 0; text-align: center; font-family: var(--mono); font-weight: 700; font-size: 16px; background: transparent;" required>
                            <span style="font-size: 10px; font-weight: 700; color: var(--muted); letter-spacing: 0.05em;">DÍAS</span>
                        </div>
                    </div>
                </div>

                <div style="background: var(--raised); padding: 20px; border-radius: 10px; border: 1px solid var(--border);">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                        <label class="label" style="font-weight: 700; color: var(--primary); font-size: 11px;">
                            <i class="fa-solid fa-envelope-open-text" style="margin-right: 6px; color: var(--accent);"></i> COPIA INTERNA (EMAILS)
                        </label>
                    </div>
                    <textarea name="internal_copy_emails" rows="2" class="gui-input" style="width: 100%; font-family: var(--mono); font-size: 12px; padding: 12px; background: var(--surface); border-radius: 8px;" placeholder="email1@example.com, email2@example.com">{{ $settings->internal_copy_emails }}</textarea>
                    <p class="page-sub" style="margin-top: 10px; font-size: 10px; display: flex; align-items: center; gap: 8px; color: var(--muted);">
                        <i class="fa-solid fa-circle-info" style="color: var(--accent);"></i> Separar por comas para múltiples destinatarios.
                    </p>
                </div>

                <div style="margin-top: 24px;">
                    <button type="submit" class="btn-primary" style="width: 100%; justify-content: center; height: 46px; font-size: 12px; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase; border-radius: 8px;">
                        <i class="fa-solid fa-save" style="margin-right: 10px;"></i> Guardar Configuración
                    </button>
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
