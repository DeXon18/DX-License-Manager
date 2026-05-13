@extends('layouts.app')

@section('title', 'Configuración de Alertas — DX License Manager')

@section('content')
<div class="admin-header mb-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-primary">Alertas y Notificaciones</h1>
            <p class="text-sm text-secondary">Gestión de umbrales y reporte semanal de caducidad de licencias.</p>
        </div>
        <div class="flex gap-3">
            <form action="{{ route('admin.alerts.toggle') }}" method="POST">
                @csrf
                <button type="submit" class="btn {{ $settings->is_active ? 'btn-success' : 'btn-danger' }}">
                    <i class="fa-solid fa-power-off mr-2"></i>
                    {{ $settings->is_active ? 'Sistema Activo' : 'Sistema Desactivado' }}
                </button>
            </form>
            <form action="/admin/system/actions/dx:send-weekly-alerts" method="POST">
                @csrf
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-paper-plane mr-2"></i>
                    Test Envío Manual
                </button>
            </form>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Configuración de Umbrales -->
    <div class="lg:col-span-1">
        <div class="card p-6 h-full">
            <h2 class="text-lg font-semibold mb-4 border-bottom pb-2">Umbrales de Alerta</h2>
            <form action="{{ route('admin.alerts.update') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div class="form-group">
                        <label class="label mb-1">Alerta Crítica (Días)</label>
                        <div class="flex items-center gap-3">
                            <span class="badge badge-danger">0-7</span>
                            <input type="number" name="threshold_alerta" value="{{ $settings->threshold_alerta }}" class="form-control" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="label mb-1">Aviso (Días)</label>
                        <div class="flex items-center gap-3">
                            <span class="badge badge-warning">7-15</span>
                            <input type="number" name="threshold_aviso" value="{{ $settings->threshold_aviso }}" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="label mb-1">Recordatorio (Días)</label>
                        <div class="flex items-center gap-3">
                            <span class="badge badge-primary">15-30</span>
                            <input type="number" name="threshold_recordatorio" value="{{ $settings->threshold_recordatorio }}" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-group pt-2 border-top">
                        <label class="label mb-1">Copia Interna (Emails)</label>
                        <textarea name="internal_copy_emails" rows="3" class="form-control text-xs font-mono" placeholder="email1@example.com, email2@example.com">{{ $settings->internal_copy_emails }}</textarea>
                        <p class="text-[0.65rem] text-muted mt-1">Separar por comas para múltiples destinatarios.</p>
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="btn btn-primary w-full justify-center">
                            Guardar Configuración
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Historial de Envíos -->
    <div class="lg:col-span-2">
        <div class="card p-6 h-full">
            <h2 class="text-lg font-semibold mb-4 border-bottom pb-2 flex justify-between items-center">
                Historial de Notificaciones
                <span class="badge badge-neutral text-xs">{{ $logs->total() }} registros</span>
            </h2>
            
            <div class="table-responsive">
                <table class="table w-full text-sm">
                    <thead>
                        <tr class="bg-raised text-muted uppercase text-[0.65rem] font-bold">
                            <th class="p-3 text-left">Destinatario</th>
                            <th class="p-3 text-left">Fecha</th>
                            <th class="p-3 text-center">Estado</th>
                            <th class="p-3 text-right">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                        <tr class="border-bottom hover:bg-raised transition-colors">
                            <td class="p-3 font-medium">{{ $log->recipient }}</td>
                            <td class="p-3 font-mono text-xs">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                            <td class="p-3 text-center">
                                @if($log->status == 'sent')
                                    <span class="badge badge-success">Enviado</span>
                                @else
                                    <span class="badge badge-danger" title="{{ $log->error_message }}">Fallo</span>
                                @endif
                            </td>
                            <td class="p-3 text-right">
                                <button class="text-accent hover:text-accent-hover">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="p-6 text-center text-muted italic">
                                No se han registrado envíos de alertas todavía.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4">
                {{ $logs->links() }}
            </div>
        </div>
    </div>
</div>

<div class="mt-6 card p-6 border-left-accent">
    <div class="flex items-start gap-4">
        <div class="p-3 rounded-md bg-accent-muted text-accent">
            <i class="fa-solid fa-info-circle text-lg"></i>
        </div>
        <div>
            <h3 class="font-bold text-primary">Información del Sistema</h3>
            <p class="text-sm text-secondary max-w-3xl">
                El motor de alertas procesa únicamente las licencias del inventario activo. El envío se realiza de forma automática todos los **lunes a las 07:30 AM** a todos los contactos de clientes que tengan activado el check "Recibe Alertas" en su ficha correspondiente.
            </p>
        </div>
    </div>
</div>
@endsection
