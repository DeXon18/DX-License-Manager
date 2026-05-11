@extends('layouts.app')

@section('title', 'Gestión de Backups')

@section('header')
    <div class="page-header">
        <nav class="breadcrumb" aria-label="Breadcrumb">
            <a href="{{ route('dashboard') }}">Portal</a>
            <span>/</span>
            <a href="{{ route('admin.system.index') }}">Admin</a>
            <span>/</span>
            <span class="font-bold">Copias de Seguridad</span>
        </nav>
        <div style="display: flex; justify-content: space-between; align-items: flex-end; width: 100%;">
            <div>
                <h1 class="page-title">Database Vault</h1>
                <p class="page-sub">Gestión histórica y manual de copias de seguridad de MariaDB.</p>
            </div>
            <div style="display: flex; gap: 12px;">
                <div class="stat-mini">
                    <span class="label">Espacio Ocupado</span>
                    <span class="value">{{ $totalSize }}</span>
                </div>
                <button onclick="executeBackup()" class="btn btn-primary" id="btn-backup">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3"/></svg>
                    Generar Copia Ahora
                </button>
            </div>
        </div>
    </div>
@endsection

@section('content')
<div class="dashboard-container" x-data="backupManager()">
    <div class="card">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
            <span class="card-title">Historial de Copias</span>
            <span style="font-size: 0.6rem; color: var(--muted); border: 1px solid var(--border-subtle); padding: 2px 8px; border-radius: 4px; text-transform: uppercase; letter-spacing: 0.05em;">Retención: 30 Días</span>
        </div>
        <div style="padding: 0; overflow: hidden;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead style="background: rgba(255,255,255,0.02); border-bottom: 1px solid var(--border);">
                    <tr>
                        <th style="padding: 12px 20px; text-align: left; color: var(--muted); font-size: 11px; font-weight: 700; text-transform: uppercase;">Fecha de Creación</th>
                        <th style="padding: 12px 20px; text-align: left; color: var(--muted); font-size: 11px; font-weight: 700; text-transform: uppercase;">Entorno</th>
                        <th style="padding: 12px 20px; text-align: left; color: var(--muted); font-size: 11px; font-weight: 700; text-transform: uppercase;">Tamaño</th>
                        <th style="padding: 12px 20px; text-align: left; color: var(--muted); font-size: 11px; font-weight: 700; text-transform: uppercase;">Nombre de Archivo</th>
                        <th style="padding: 12px 20px; text-align: right; color: var(--muted); font-size: 11px; font-weight: 700; text-transform: uppercase;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($backups as $backup)
                        <tr style="border-bottom: 1px solid var(--border-subtle); transition: background 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.01)'" onmouseout="this.style.background='transparent'">
                            <td style="padding: 14px 20px;">
                                <div style="display: flex; flex-direction: column;">
                                    <span style="color: var(--primary); font-weight: 600; font-size: 13px;">{{ \Carbon\Carbon::parse($backup['date'])->format('d M, Y') }}</span>
                                    <span style="color: var(--muted); font-size: 10px; font-family: 'IBM Plex Mono';">{{ \Carbon\Carbon::parse($backup['date'])->format('H:i:s') }}</span>
                                </div>
                            </td>
                            <td style="padding: 14px 20px;">
                                <span style="padding: 3px 8px; border-radius: 4px; background: {{ $backup['env'] === 'PROD' ? 'rgba(16, 185, 129, 0.1)' : 'rgba(67, 97, 238, 0.1)' }}; color: {{ $backup['env'] === 'PROD' ? 'var(--success)' : 'var(--accent)' }}; font-size: 9px; font-weight: 800; letter-spacing: 0.05em;">{{ $backup['env'] }}</span>
                            </td>
                            <td style="padding: 14px 20px; font-family: 'IBM Plex Mono', monospace; font-size: 12px; color: var(--primary);">{{ $backup['size'] }}</td>
                            <td style="padding: 14px 20px; color: var(--muted); font-size: 11px;">{{ $backup['name'] }}</td>
                            <td style="padding: 14px 20px; text-align: right;">
                                <div style="display: flex; gap: 10px; justify-content: flex-end;">
                                    <a href="{{ route('admin.system.download-backup', ['filename' => $backup['name']]) }}" class="btn-action" title="Descargar copia">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3"/></svg>
                                    </a>
                                    <button @click="deleteBackup('{{ $backup['name'] }}')" class="btn-action danger" title="Eliminar copia">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M10 11v6M14 11v6"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="padding: 40px; text-align: center;">
                                <div style="opacity: 0.3; margin-bottom: 12px;">
                                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3"/></svg>
                                </div>
                                <span style="color: var(--muted);">No se han detectado archivos de backup en el servidor.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card" style="margin-top: 24px;">
        <div class="card-header">
            <span class="card-title">Configuración de Programación</span>
        </div>
        <div style="padding: 24px;">
            <div style="display: flex; align-items: flex-start; gap: 20px;">
                <div style="padding: 15px; border-radius: 10px; background: rgba(67, 97, 238, 0.05); border: 1px solid rgba(67, 97, 238, 0.2); color: var(--accent); flex: 1;">
                    <h4 style="margin: 0 0 8px 0; font-size: 14px; font-weight: 700;">Cron Job Activo</h4>
                    <p style="margin: 0; font-size: 12px; line-height: 1.6; opacity: 0.9;">El sistema realiza una copia de seguridad completa automáticamente cada día a las <b>03:00 AM</b>. Los archivos se rotan tras 30 días para optimizar el espacio.</p>
                </div>
                <div style="width: 280px; display: flex; flex-direction: column; gap: 10px;">
                    <div style="font-size: 11px; color: var(--muted); text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;">Próxima ejecución</div>
                    <div style="font-size: 18px; font-weight: 700; color: var(--primary);">Mañana, 03:00</div>
                    <div style="height: 4px; background: var(--border-subtle); border-radius: 2px; margin-top: 4px; overflow: hidden;">
                        <div style="width: 65%; height: 100%; background: var(--accent);"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .stat-mini {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        justify-content: center;
        padding: 0 15px;
        border-right: 1px solid var(--border-subtle);
    }
    .stat-mini .label {
        font-size: 9px;
        color: var(--muted);
        text-transform: uppercase;
        font-weight: 700;
        letter-spacing: 0.05em;
    }
    .stat-mini .value {
        font-size: 16px;
        font-weight: 700;
        color: var(--primary);
        font-family: 'Outfit', sans-serif;
    }
    .btn-action {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        background: rgba(255,255,255,0.03);
        border: 1px solid var(--border);
        color: var(--muted);
        transition: all 0.2s;
        cursor: pointer;
        text-decoration: none;
    }
    .btn-action:hover {
        background: rgba(67, 97, 238, 0.1);
        color: var(--accent);
        border-color: var(--accent);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(67, 97, 238, 0.15);
    }
    .btn-action.danger:hover {
        background: rgba(239, 68, 68, 0.1);
        color: var(--danger);
        border-color: var(--danger);
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.15);
    }
</style>

<script>
    function backupManager() {
        return {
            deleteBackup(filename) {
                if (!confirm(`¿Estás seguro de que deseas ELIMINAR permanentemente la copia: ${filename}?`)) return;
                
                fetch(`{{ url('admin/system/actions/delete-backup') }}/${filename}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => alert('Error al eliminar backup'));
            }
        }
    }

    function executeBackup() {
        const btn = document.getElementById('btn-backup');
        if (!confirm('¿Deseas iniciar una copia de seguridad manual ahora?')) return;
        
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Procesando...';
        
        fetch('{{ route('admin.system.backup-db') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                alert('Error: ' + data.message);
                btn.disabled = false;
                btn.innerHTML = 'Generar Copia Ahora';
            }
        })
        .catch(error => {
            alert('Error crítico de red');
            btn.disabled = false;
            btn.innerHTML = 'Generar Copia Ahora';
        });
    }
</script>
@endsection
