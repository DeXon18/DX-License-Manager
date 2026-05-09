@extends('layouts.app')

@section('title', 'Moldex3D Auditor')

@section('content')
<div class="page-header">
    <div class="breadcrumb">
        <a href="{{ url('/') }}">Portal</a> ›
        <a href="{{ route('tools.index') }}">Herramientas</a> ›
        Moldex3D
    </div>
    <div style="display: flex; align-items: center; gap: 12px; margin-top: 8px;">
        <div class="tool-icon-fallback" style="background: rgba(245, 158, 11, 0.1); color: #F59E0B; width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20"/><path d="m4.93 4.93 14.14 14.14"/><path d="M2 12h20"/><path d="m19.07 4.93-14.14 14.14"/></svg>
        </div>
        <div>
            <h1 class="page-title" style="margin: 0;">Moldex3D Auditor <span class="vendor-label" style="font-size: 10px; padding: 2px 6px; margin-left: 8px; background: rgba(245, 158, 11, 0.1); color: #F59E0B; border: 1px solid rgba(245, 158, 11, 0.2); border-radius: 4px; font-weight: 700; text-transform: uppercase;">CORE PLASTIC</span></h1>
            <p class="page-sub" style="margin: 0;">Análisis determinista de archivos .mac y normalización de inventario</p>
        </div>
    </div>
</div>

<div class="grid-main" x-data="moldexAuditor()" x-init="init()">
    <div class="main-panel">
        <!-- Dropzone Card -->
        <div class="card" style="margin-bottom: 24px;">
            <div class="card-header" style="justify-content: space-between;">
                <span class="card-title">Carga de Licencia Moldex3D</span>
                <span class="badge" style="background: rgba(245, 158, 11, 0.1); color: #F59E0B; border: 1px solid rgba(245, 158, 11, 0.2);">PARSER DETERMINISTA</span>
            </div>
            
            <div style="padding: 24px;">
                <form @submit.prevent="uploadFile" enctype="multipart/form-data">
                    <div class="dropzone" 
                         :class="[isDragging ? 'dragging' : '', 'theme-amber']"
                         @dragover.prevent="isDragging = true"
                         @dragleave.prevent="isDragging = false"
                         @drop.prevent="handleDrop"
                         @click="$refs.fileInput.click()"
                         style="height: 180px; border-style: dashed; border-width: 1px; border-color: var(--border); border-radius: 8px; margin-bottom: 20px; display: flex; flex-direction: column; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s; background: var(--surface);">
                        
                        <div style="display: flex; flex-direction: column; align-items: center; gap: 16px;" x-show="!loading">
                            <div style="background: rgba(245, 158, 11, 0.1); width: 48px; height: 48px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #F59E0B;">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                            </div>
                            
                            <div style="text-align: center;">
                                <div x-show="!fileName" style="font-size: 14px; font-weight: 500; color: var(--primary);">Seleccione archivo .mac</div>
                                <div x-show="fileName" style="font-size: 14px; font-weight: 600; color: #F59E0B;" x-text="fileName"></div>
                                <div style="font-size: 12px; color: var(--muted); margin-top: 4px;">Arrastre aquí o haga clic para buscar</div>
                            </div>
                        </div>

                        <div x-show="loading" style="display: flex; flex-direction: column; align-items: center; gap: 12px;">
                            <div class="spinner-amber"></div>
                            <div style="font-size: 13px; font-weight: 500; color: var(--primary);">Analizando estructura...</div>
                        </div>

                        <input type="file" name="license_file" x-ref="fileInput" style="display: none;" @change="handleFileChange">
                    </div>

                    <div style="display: flex; justify-content: flex-end; gap: 12px;">
                        <button type="submit" 
                                :disabled="!fileName || loading"
                                class="btn-primary" 
                                style="background: #F59E0B; border-color: #F59E0B; padding: 10px 24px; font-weight: 700; letter-spacing: 0.5px; display: flex; align-items: center; border-radius: 6px;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 8px;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                            AUDITAR Y GUARDAR
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Bento Grid Results (Hidden by default) -->
        <div x-show="result" x-transition style="margin-top: 12px;">
            <div style="font-size: 11px; font-weight: 700; color: var(--muted); text-transform: uppercase; margin-bottom: 12px; display: flex; align-items: center; gap: 8px;">
                <span style="width: 8px; height: 8px; border-radius: 50%; background: #10B981;"></span>
                Resultado de Auditoría Local
            </div>
            
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); grid-template-rows: auto auto; gap: 12px;">
                <!-- Card Cliente -->
                <div class="card" style="grid-column: span 2; padding: 20px;">
                    <div style="font-size: 10px; font-weight: 700; color: var(--muted); uppercase; tracking: 0.05em; margin-bottom: 8px;">ENTIDAD / CLIENTE</div>
                    <div style="font-size: 20px; font-weight: 700; color: var(--primary); margin-bottom: 4px;" x-text="result.metadata.customer_name"></div>
                    <div style="display: flex; gap: 16px; margin-top: 12px;">
                        <div>
                            <div style="font-size: 10px; color: var(--muted);">ID CLIENTE</div>
                            <div style="font-size: 13px; font-weight: 600; font-family: var(--font-mono);" x-text="result.metadata.customer_id"></div>
                        </div>
                        <div>
                            <div style="font-size: 10px; color: var(--muted);">PAÍS</div>
                            <div style="font-size: 13px; font-weight: 600;" x-text="result.metadata.country || 'N/A'"></div>
                        </div>
                    </div>
                </div>

                <!-- Card Machine ID -->
                <div class="card" style="padding: 20px; border-left: 4px solid #F59E0B;">
                    <div style="font-size: 10px; font-weight: 700; color: var(--muted); uppercase; tracking: 0.05em; margin-bottom: 8px;">MACHINE ID</div>
                    <div style="font-size: 18px; font-weight: 700; color: var(--primary); font-family: var(--font-mono); word-break: break-all;" x-text="result.metadata.machine_id"></div>
                    <div style="margin-top: 12px; display: flex; align-items: center; gap: 6px;">
                        <span class="badge" style="background: rgba(16, 185, 129, 0.1); color: #10B981; border: none; font-size: 9px;">VÁLIDO</span>
                    </div>
                </div>

                <!-- Card Productos -->
                <div class="card" style="grid-column: span 3; padding: 0;">
                    <div class="card-header" style="border-bottom: 1px solid var(--border-subtle); padding: 12px 20px;">
                        <span style="font-size: 11px; font-weight: 700; color: var(--muted); uppercase;">MÓDULOS ACTIVOS (INCREMENTS)</span>
                    </div>
                    <div style="max-height: 200px; overflow-y: auto; padding: 12px 20px;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="border-bottom: 1px solid var(--border-subtle);">
                                    <th style="text-align: left; padding: 8px 0; font-size: 10px; color: var(--muted);">CÓDIGO</th>
                                    <th style="text-align: left; padding: 8px 0; font-size: 10px; color: var(--muted);">PRODUCTO</th>
                                    <th style="text-align: center; padding: 8px 0; font-size: 10px; color: var(--muted);">CADUCIDAD</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="prod in result.metadata.products" :key="prod.code">
                                    <tr style="border-bottom: 1px solid var(--border-subtle);">
                                        <td style="padding: 10px 0; font-size: 12px; font-weight: 600; font-family: var(--font-mono); color: #F59E0B;" x-text="prod.code"></td>
                                        <td style="padding: 10px 0; font-size: 12px; color: var(--primary);" x-text="prod.name"></td>
                                        <td style="padding: 10px 0; text-align: center;">
                                            <span style="font-family: var(--font-mono); font-size: 11px; font-weight: 600;" 
                                                  :style="isExpired(prod.expiration) ? 'color: var(--danger);' : 'color: var(--success);'"
                                                  x-text="formatDate(prod.expiration)"></span>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div style="margin-top: 16px; background: var(--success-bg); border: 1px solid var(--success-border); padding: 16px; border-radius: 8px; display: flex; align-items: center; justify-content: space-between;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="background: var(--success); color: white; width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                    </div>
                    <div>
                        <div style="font-size: 13px; font-weight: 600; color: var(--success);">Archivo procesado y almacenado correctamente en el servidor</div>
                    </div>
                </div>
                <div style="display: flex; gap: 8px;">
                    <button @click="reset" class="btn-secondary" style="font-size: 11px; padding: 6px 12px;">NUEVA AUDITORÍA</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar Informativo -->
    <div class="sidebar-panel">
        <div style="background: var(--surface); border: 1px solid var(--border); padding: 20px; border-radius: 10px; margin-bottom: 16px;">
            <div style="font-size: 11px; font-weight: 700; color: var(--muted); text-transform: uppercase; margin-bottom: 16px;">Estándar de Nomenclatura</div>
            <div style="display: flex; flex-direction: column; gap: 12px;">
                <div style="background: var(--bg); padding: 10px; border-radius: 6px; font-family: var(--font-mono); font-size: 11px; color: var(--secondary); border-left: 3px solid #F59E0B;">
                    AÑO_ID_[PAIS]CLIENTE__TIPO_FECHA.mac
                </div>
                <p style="font-size: 11px; color: var(--muted); line-height: 1.5;">
                    El sistema renombra automáticamente el archivo basado en el contenido extraído del bloque de comentarios.
                </p>
            </div>
        </div>

        <div style="background: #FFFBEB; border: 1px solid #FDE68A; padding: 16px; border-radius: 10px;">
            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px; color: #B45309;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                <span style="font-size: 12px; font-weight: 700; text-transform: uppercase;">Aviso de Privacidad</span>
            </div>
            <p style="font-size: 12px; color: #92400E; line-height: 1.6; margin: 0;">
                Este parser es <strong>local y determinista</strong>. El contenido de la licencia no se envía a ningún motor de IA externo. Los metadatos se almacenan en la base de datos segura del portal.
            </p>
        </div>

        <div style="margin-top: 16px; padding: 16px; border: 1px solid var(--border-subtle); border-radius: 10px; background: var(--surface);">
            <div style="font-size: 11px; color: var(--muted); line-height: 1.6;">
                <strong>Soporte Técnico:</strong><br>
                Si el parser no detecta el Machine ID, verifique que el archivo no esté corrupto o que el bloque de comentarios <code># [Customer Information]</code> esté presente al inicio.
            </div>
        </div>
    </div>
</div>

<style>
    .spinner-amber {
        width: 24px;
        height: 24px;
        border: 3px solid rgba(245, 158, 11, 0.1);
        border-top: 3px solid #F59E0B;
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
    }
    @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }

    .dropzone.theme-amber:hover {
        border-color: #F59E0B !important;
        background: rgba(245, 158, 11, 0.02) !important;
    }
    .dropzone.dragging.theme-amber {
        border-color: #F59E0B !important;
        background: rgba(245, 158, 11, 0.05) !important;
        transform: scale(1.01);
    }
</style>

<script>
function moldexAuditor() {
    return {
        isDragging: false,
        fileName: '',
        loading: false,
        result: null,

        init() {
            console.log('Moldex3D Auditor initialized');
        },

        handleFileChange(e) {
            if (e.target.files.length > 0) {
                this.fileName = e.target.files[0].name;
            }
        },

        handleDrop(e) {
            this.isDragging = false;
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                this.$refs.fileInput.files = files;
                this.fileName = files[0].name;
            }
        },

        async uploadFile() {
            if (!this.fileName) return;

            this.loading = true;
            this.result = null;

            const formData = new FormData();
            formData.append('license_file', this.$refs.fileInput.files[0]);
            formData.append('_token', '{{ csrf_token() }}');

            try {
                const response = await fetch('{{ route("tools.moldex3d.process") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    this.result = data;
                } else {
                    alert('Error procesando el archivo: ' + (data.message || 'Error desconocido'));
                }
            } catch (error) {
                console.error(error);
                alert('Error de conexión con el servidor');
            } finally {
                this.loading = false;
            }
        },

        reset() {
            this.result = null;
            this.fileName = '';
            this.$refs.fileInput.value = '';
        },

        formatDate(dateStr) {
            if (!dateStr) return 'N/A';
            if (dateStr === 'permanent') return 'PERMANENTE';
            // YYYYMMDD to YYYY-MM-DD
            return dateStr.substring(0, 4) + '-' + dateStr.substring(4, 6) + '-' + dateStr.substring(6, 8);
        },

        isExpired(dateStr) {
            if (!dateStr || dateStr === 'permanent') return false;
            const year = parseInt(dateStr.substring(0, 4));
            const month = parseInt(dateStr.substring(4, 6)) - 1;
            const day = parseInt(dateStr.substring(6, 8));
            const expDate = new Date(year, month, day);
            return expDate < new Date();
        }
    }
}
</script>
@endsection
