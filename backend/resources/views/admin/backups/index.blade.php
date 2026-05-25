@extends('layouts.app')

@section('title', 'Gestión de Backups')

@section('content')
<div class="dx-v2-page-header">
    <div>
        <div class="breadcrumb">
            <a href="{{ route('admin.backups.index') }}">Administración</a>
            <span class="separator">/</span>
            <span class="current">Backups</span>
        </div>
        <h1 class="page-title">Copias de <span>Seguridad</span></h1>
        <p class="page-subtitle">Historial completo, descargas y gestión de espacio en disco para copias de seguridad.</p>
    </div>
</div>
<div class="dashboard-container" x-data="backupManager()">
    <div class="card">
        <div class="card-header dx-v2-backups-card-header">
            <div class="dx-v2-backups-header-left">
                <span class="card-title">Historial de Copias</span>
                <span class="dx-v2-backups-header-badge">Retención: 30 Días</span>
            </div>
            <div class="dx-v2-backups-header-right">
                <div class="dx-v2-backups-storage-panel">
                    <span class="dx-v2-backups-storage-label">Espacio Ocupado</span>
                    <span class="dx-v2-backups-storage-value">{{ $totalSize }}</span>
                </div>
                <button onclick="executeBackup()" class="btn btn-primary dx-v2-backups-btn-run" id="btn-backup">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="dx-v2-backups-btn-icon"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3"/></svg>
                    Generar Copia
                </button>
            </div>
        </div>
        <div class="dx-v2-backups-table-container">
            <table class="dx-v2-backups-table">
                <thead class="dx-v2-backups-table-thead">
                    <tr>
                        <th class="dx-v2-backups-table-th">Fecha de Creación</th>
                        <th class="dx-v2-backups-table-th">Origen</th>
                        <th class="dx-v2-backups-table-th">Entorno</th>
                        <th class="dx-v2-backups-table-th">Tamaño</th>
                        <th class="dx-v2-backups-table-th">Nombre de Archivo</th>
                        <th class="dx-v2-backups-table-th-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($backups as $backup)
                        <tr class="dx-v2-backups-table-tr">
                            <td class="dx-v2-backups-table-td">
                                <div class="dx-v2-backups-date-group">
                                    <span class="dx-v2-backups-date-primary">{{ \Carbon\Carbon::parse($backup['date'])->format('d M, Y') }}</span>
                                    <span class="dx-v2-backups-date-secondary">{{ \Carbon\Carbon::parse($backup['date'])->format('H:i:s') }}</span>
                                </div>
                            </td>
                            <td class="dx-v2-backups-table-td">
                                <span class="dx-v2-backups-badge-type {{ $backup['type'] === 'SISTEMA' ? 'system' : 'manual' }}">{{ $backup['type'] }}</span>
                            </td>
                            <td class="dx-v2-backups-table-td">
                                <span class="dx-v2-backups-badge-env {{ $backup['env'] === 'PROD' ? 'prod' : 'beta' }}">{{ $backup['env'] }}</span>
                            </td>
                            <td class="dx-v2-backups-table-td dx-v2-backups-file-size">{{ $backup['size'] }}</td>
                            <td class="dx-v2-backups-table-td dx-v2-backups-file-name">{{ $backup['name'] }}</td>
                            <td class="dx-v2-backups-table-td-right">
                                <div class="dx-v2-backups-actions-group">
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
                            <td colspan="6" class="dx-v2-backups-empty-td">
                                <div class="dx-v2-backups-empty-icon">
                                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3"/></svg>
                                </div>
                                <span class="dx-v2-backups-empty-text">No se han detectado archivos de backup en el servidor.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card dx-v2-backups-scheduling-card">
        <div class="card-header">
            <span class="card-title">Configuración de Programación</span>
        </div>
        <div class="dx-v2-backups-scheduling-body">
            <div class="dx-v2-backups-scheduling-layout">
                <div class="dx-v2-backups-scheduling-box">
                    <h4 class="dx-v2-backups-scheduling-title">Cron Job Activo</h4>
                    <p class="dx-v2-backups-scheduling-desc">El sistema realiza una copia de seguridad completa automáticamente cada día a las <b>03:00 AM</b>. Los archivos se rotan tras 30 días para optimizar el espacio.</p>
                </div>
                <div class="dx-v2-backups-countdown-container">
                    <div class="dx-v2-backups-countdown-label">Próxima ejecución</div>
                    <div class="dx-v2-backups-countdown-value" x-text="timeRemaining">Calculando...</div>
                    <div class="dx-v2-backups-progress-bar">
                        <div :style="{ width: progressPercent + '%' }" class="dx-v2-backups-progress-fill"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Restauración -->
    <template x-if="showRestoreModal">
        <div class="dx-v2-backups-modal-overlay">
            <div class="card dx-v2-backups-modal-card">
                <div class="card-header dx-v2-backups-modal-header">
                    <span class="card-title dx-v2-backups-modal-title"><i class="fas fa-exclamation-triangle me-2"></i> Restaurar Base de Datos</span>
                </div>
                <div class="dx-v2-backups-modal-body">
                    <p class="dx-v2-backups-modal-msg">
                        Estás a punto de restaurar la base de datos usando el archivo:
                        <br><b class="font-mono dx-v2-backups-modal-file" x-text="selectedFile"></b>
                    </p>
                    <div class="dx-v2-backups-modal-warning-box">
                        <p class="dx-v2-backups-modal-warning-text">
                            ⚠️ ATENCIÓN: Esta acción sobrescribirá TODOS los datos actuales de forma permanente. No se puede deshacer.
                        </p>
                    </div>
                    
                    <div class="form-group">
                        <label class="dx-v2-backups-modal-label">ESCRIBE "RESTAURAR" PARA CONFIRMAR</label>
                        <input type="text" x-model="confirmText" class="font-mono dx-v2-backups-modal-input" placeholder="RESTAURAR">
                    </div>

                    <div class="dx-v2-backups-modal-actions">
                        <button @click="showRestoreModal = false" class="btn-secondary dx-v2-backups-modal-btn">Cancelar</button>
                        <button @click="executeRestore" 
                                class="btn-danger dx-v2-backups-modal-btn" 
                                :disabled="confirmText !== 'RESTAURAR' || restoring">
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
                
                // Progreso: 08:00 a 08:00 son 24h. 
                // Calculamos cuánto ha pasado desde las 08:00 anteriores.
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
