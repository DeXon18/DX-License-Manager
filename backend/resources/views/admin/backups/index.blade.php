@extends('layouts.app')

@section('title', 'Gestión de Backups')

@section('content')
<div class="page-header">
    <div class="page-header-info">
        <h1 class="page-title">Gestión de Backups</h1>
        <p class="page-sub">Historial completo, descargas y gestión de espacio en disco para copias de seguridad.</p>
    </div>
</div>
<div class="dashboard-container" x-data="backupManager()">
    <div class="card">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
            <div style="display: flex; align-items: center; gap: 15px;">
                <span class="card-title">Historial de Copias</span>
                <span style="font-size: 0.6rem; color: var(--muted); border: 1px solid var(--border-subtle); padding: 2px 8px; border-radius: 4px; text-transform: uppercase; letter-spacing: 0.05em;">Retención: 30 Días</span>
            </div>
            <div style="display: flex; gap: 15px; align-items: center;">
                <div style="display: flex; flex-direction: column; align-items: flex-end; padding-right: 15px; border-right: 1px solid var(--border-subtle);">
                    <span style="font-size: 9px; color: var(--muted); text-transform: uppercase; font-weight: 700;">Espacio Ocupado</span>
                    <span style="font-size: 14px; font-weight: 700; color: var(--primary);">{{ $totalSize }}</span>
                </div>
                <button onclick="executeBackup()" class="btn btn-primary" id="btn-backup" style="height: 32px; font-size: 11px; padding: 0 12px;">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="margin-right: 6px;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3"/></svg>
                    Generar Copia
                </button>
            </div>
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
                                <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                    <button @click="confirmRestore('{{ $backup['name'] }}')" class="btn-action warning" title="Restaurar base de datos">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M23 4v6h-6M1 20v-6h6M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/></svg>
                                    </button>
                                    <a href="{{ route('admin.backups.download', ['filename' => $backup['name']]) }}" class="btn-action download" title="Descargar copia">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3"/></svg>
                                    </a>
                                    <button @click="deleteBackup('{{ $backup['name'] }}')" class="btn-action danger" title="Eliminar copia">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M10 11v6M14 11v6"/></svg>
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
                    <div style="font-size: 18px; font-weight: 700; color: var(--primary);" x-text="timeRemaining">Calculando...</div>
                    <div style="height: 4px; background: var(--border-subtle); border-radius: 2px; margin-top: 4px; overflow: hidden;">
                        <div :style="{ width: progressPercent + '%' }" style="height: 100%; background: var(--accent); transition: width 1s ease-in-out;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Restauración -->
    <template x-if="showRestoreModal">
        <div style="position: fixed; inset: 0; background: rgba(0,0,0,0.8); backdrop-filter: blur(4px); z-index: 1000; display: flex; align-items: center; justify-content: center; padding: 20px;">
            <div class="card" style="max-width: 450px; width: 100%; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5);">
                <div class="card-header" style="background: rgba(239, 68, 68, 0.05); border-bottom: 1px solid rgba(239, 68, 68, 0.1);">
                    <span class="card-title" style="color: var(--danger);"><i class="fas fa-exclamation-triangle me-2"></i> Restaurar Base de Datos</span>
                </div>
                <div style="padding: 24px;">
                    <p style="color: var(--primary); font-size: 14px; margin-bottom: 16px;">
                        Estás a punto de restaurar la base de datos usando el archivo:
                        <br><b class="font-mono" style="color: var(--accent); display: block; margin-top: 8px;" x-text="selectedFile"></b>
                    </p>
                    <div style="background: rgba(239, 68, 68, 0.05); border-left: 3px solid var(--danger); padding: 12px; border-radius: 4px; margin-bottom: 24px;">
                        <p style="color: var(--danger); font-size: 11px; font-weight: 600; margin: 0;">
                            ⚠️ ATENCIÓN: Esta acción sobrescribirá TODOS los datos actuales de forma permanente. No se puede deshacer.
                        </p>
                    </div>
                    
                    <div class="form-group">
                        <label style="font-size: 10px; color: var(--muted); margin-bottom: 8px;">ESCRIBE "RESTAURAR" PARA CONFIRMAR</label>
                        <input type="text" x-model="confirmText" class="font-mono" placeholder="RESTAURAR" style="width: 100%; text-align: center; letter-spacing: 0.1em;">
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-top: 24px;">
                        <button @click="showRestoreModal = false" class="btn-secondary" style="padding: 10px;">Cancelar</button>
                        <button @click="executeRestore" 
                                class="btn-danger" 
                                :disabled="confirmText !== 'RESTAURAR' || restoring"
                                style="padding: 10px;">
                            <span x-show="!restoring">Iniciar Restauración</span>
                            <span x-show="restoring"><i class="fas fa-spinner fa-spin"></i> Procesando...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>

<style>
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
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
        text-decoration: none;
        padding: 0;
        outline: none;
    }
    .btn-action:hover {
        background: rgba(255, 255, 255, 0.08);
        color: var(--primary);
        border-color: var(--border-subtle);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    }
    .btn-action.warning:hover {
        background: rgba(245, 158, 11, 0.1);
        color: var(--warning);
        border-color: var(--warning);
        box-shadow: 0 4px 12px rgba(245, 158, 11, 0.15);
    }
    .btn-action.danger:hover {
        background: rgba(239, 68, 68, 0.1);
        color: var(--danger);
        border-color: var(--danger);
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.15);
    }
    .btn-action.download:hover {
        background: rgba(67, 97, 238, 0.1);
        color: var(--accent);
        border-color: var(--accent);
        box-shadow: 0 4px 12px rgba(67, 97, 238, 0.15);
    }
    .btn-danger {
        background: var(--danger);
        color: white;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
    }
    .btn-danger:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
    .btn-danger:not(:disabled):hover {
        filter: brightness(1.1);
        transform: translateY(-1px);
    }
</style>

<script>
    function backupManager() {
        return {
            showRestoreModal: false,
            selectedFile: '',
            confirmText: '',
            restoring: false,
            timeRemaining: '',
            progressPercent: 0,

            init() {
                this.updateCountdown();
                setInterval(() => this.updateCountdown(), 60000);
            },

            updateCountdown() {
                const now = new Date();
                const next = new Date();
                next.setHours(3, 0, 0, 0);
                
                if (now >= next) {
                    next.setDate(next.getDate() + 1);
                }

                const diff = next - now;
                const hours = Math.floor(diff / (1000 * 60 * 60));
                const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                
                this.timeRemaining = `en ${hours}h ${minutes}m`;
                
                // Progreso: 03:00 a 03:00 son 24h. 
                // Calculamos cuánto ha pasado desde las 03:00 anteriores.
                const last = new Date(next);
                last.setDate(last.getDate() - 1);
                const total = next - last;
                const elapsed = now - last;
                this.progressPercent = Math.min(Math.max(Math.round((elapsed / total) * 100), 0), 100);
            },

            confirmRestore(filename) {
                this.selectedFile = filename;
                this.confirmText = '';
                this.showRestoreModal = true;
            },

            executeRestore() {
                if (this.confirmText !== 'RESTAURAR') return;
                this.restoring = true;

                fetch(`{{ url('admin/backups') }}/${this.selectedFile}/restore`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Base de datos restaurada con éxito. El sistema se recargará.');
                        window.location.reload();
                    } else {
                        alert('Error: ' + data.message);
                        this.restoring = false;
                    }
                })
                .catch(error => {
                    alert('Error crítico durante la restauración');
                    this.restoring = false;
                });
            },

            deleteBackup(filename) {
                if (!confirm(`¿Estás seguro de que deseas ELIMINAR permanentemente la copia: ${filename}?`)) return;
                
                fetch(`{{ url('admin/backups') }}/${filename}`, {
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
        
        fetch('{{ route('admin.backups.run') }}', {
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
                btn.innerHTML = 'Generar Copia';
            }
        })
        .catch(error => {
            alert('Error crítico de red');
            btn.disabled = false;
            btn.innerHTML = 'Generar Copia';
        });
    }
</script>
@endsection
