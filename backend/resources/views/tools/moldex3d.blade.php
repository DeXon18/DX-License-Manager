@extends('layouts.app')
 
@section('title', 'Moldex3D Auditor')
 
@section('content')
<div class="page-header">
    <div class="breadcrumb">
        <a href="{{ url('/') }}">Portal</a> ›
        <a href="{{ route('tools.index') }}">Herramientas</a> ›
        Moldex3D
    </div>
    <div class="dx-v2-tools-moldex-header-layout">
        <div class="dx-v2-tools-moldex-header-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20"/><path d="m4.93 4.93 14.14 14.14"/><path d="M2 12h20"/><path d="m19.07 4.93-14.14 14.14"/></svg>
        </div>
        <div>
            <h1 class="dx-v2-tools-moldex-header-title">Moldex3D <span class="dx-v2-tools-moldex-vendor-label">CORE PLASTIC</span></h1>
            <p class="dx-v2-tools-moldex-header-sub">Análisis determinista de archivos .mac y normalización de inventario</p>
        </div>
    </div>
</div>
 
<div class="grid-main" x-data="moldexAuditor()" x-init="init()">
    <div class="main-panel">
        <!-- Dropzone Card -->
        <div class="card dx-v2-tools-moldex-card">
            <div class="card-header dx-v2-tools-moldex-card-header">
                <span class="card-title">Carga de Licencia Moldex3D</span>
                <span class="dx-v2-tools-moldex-badge">PARSER DETERMINISTA</span>
            </div>
            
            <div class="dx-v2-tools-moldex-card-body">
                <!-- Error Message Alert -->
                <template x-if="errorMessage">
                    <div x-transition class="dx-v2-tools-moldex-error-alert">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        <span x-text="errorMessage"></span>
                    </div>
                </template>
 
                <form @submit.prevent="uploadFile" enctype="multipart/form-data">
                    <div class="dx-v2-tools-moldex-dropzone dropzone" 
                         :class="[isDragging ? 'dragging' : '', 'theme-amber']"
                         @dragover.prevent="isDragging = true"
                         @dragleave.prevent="isDragging = false"
                         @drop.prevent="handleDrop"
                         @click="$refs.fileInput.click()">
                        
                        <div class="dx-v2-tools-moldex-dropzone-inner" x-show="!loading">
                            <div class="dx-v2-tools-moldex-dropzone-icon-box">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                            </div>
                            
                            <div style="text-align: center;">
                                <div x-show="!fileName" class="dx-v2-tools-moldex-dropzone-text-primary">Seleccione archivo .mac</div>
                                <div x-show="fileName" class="dx-v2-tools-moldex-dropzone-text-selected" x-text="fileName"></div>
                                <div class="dx-v2-tools-moldex-dropzone-text-secondary">Arrastre aquí o haga clic para buscar</div>
                            </div>
                        </div>
 
                        <div x-show="loading" style="display: flex; flex-direction: column; align-items: center; gap: 12px;">
                            <div class="spinner-amber"></div>
                            <div class="dx-v2-tools-moldex-dropzone-text-primary">Analizando estructura...</div>
                        </div>
 
                        <input type="file" name="license_file" x-ref="fileInput" style="display: none;" @change="handleFileChange">
                    </div>
 
                    <div class="dx-v2-tools-moldex-btn-row">
                        <button type="submit" 
                                :disabled="!fileName || loading"
                                class="dx-v2-tools-moldex-btn-submit">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 8px;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                            AUDITAR Y GUARDAR
                        </button>
                    </div>
                </form>
            </div>
        </div>
 
        <!-- Resultados del Análisis (Rediseño Estilo Lista) -->
        <div x-show="result" x-transition class="dx-v2-tools-moldex-results-wrapper">
            <div class="dx-v2-tools-moldex-results-card card">
                <!-- Header con Icono Check -->
                <div class="dx-v2-tools-moldex-results-header">
                    <div class="dx-v2-tools-moldex-results-header-icon">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    </div>
                    <span class="dx-v2-tools-moldex-results-header-text">Resultados del Análisis</span>
                </div>
 
                <div class="dx-v2-tools-moldex-card-body">
                    <!-- Tabla de Propiedades -->
                    <div class="dx-v2-tools-moldex-property-list">
                        
                        <!-- Archivo -->
                        <div class="dx-v2-tools-moldex-property-row">
                            <span class="dx-v2-tools-moldex-property-label">Archivo analizado</span>
                            <span class="dx-v2-tools-moldex-property-val-mono" x-text="result?.filename"></span>
                        </div>
 
                        <!-- Customer ID -->
                        <div class="dx-v2-tools-moldex-property-row">
                            <span class="dx-v2-tools-moldex-property-label">Customer ID</span>
                            <span class="dx-v2-tools-moldex-property-val-mono-bold" x-text="result?.metadata?.customer_id || 'N/A'"></span>
                        </div>
 
                        <!-- Nombre Cliente -->
                        <div class="dx-v2-tools-moldex-property-row">
                            <span class="dx-v2-tools-moldex-property-label">Nombre de Cliente</span>
                            <span class="dx-v2-tools-moldex-property-val-bold" x-text="result?.metadata?.customer_name"></span>
                        </div>
 
                        <!-- Hostname -->
                        <div class="dx-v2-tools-moldex-property-row">
                            <span class="dx-v2-tools-moldex-property-label">Servidor / Hostname</span>
                            <span class="dx-v2-tools-moldex-property-val-mono-bold" x-text="result?.metadata?.hostname || 'N/A'"></span>
                        </div>
 
                        <!-- Machine ID -->
                        <div class="dx-v2-tools-moldex-property-row">
                            <span class="dx-v2-tools-moldex-property-label">Machine ID (MAC)</span>
                            <span class="dx-v2-tools-moldex-property-val-mono-bold" x-text="result?.metadata?.machine_id"></span>
                        </div>
 
                        <!-- Modo Licencia -->
                        <div class="dx-v2-tools-moldex-property-row">
                            <span class="dx-v2-tools-moldex-property-label">Modo de Licencia</span>
                            <span class="dx-v2-tools-moldex-property-val-bold" x-text="result?.metadata?.license_mode"></span>
                        </div>
 
                        <!-- Versión -->
                        <div class="dx-v2-tools-moldex-property-row">
                            <span class="dx-v2-tools-moldex-property-label">Versión</span>
                            <span class="dx-v2-tools-moldex-version-chip" x-text="'v' + (result?.metadata?.version || '0')"></span>
                        </div>
 
                        <!-- Fecha Expiración -->
                        <div class="dx-v2-tools-moldex-property-row">
                            <span class="dx-v2-tools-moldex-property-label">Fecha de Expiración</span>
                            <span :style="isExpired(result?.metadata?.expiration) ? 'color: var(--danger)' : 'color: var(--primary)'" style="font-size: 13px; font-weight: 700; font-family: var(--font-mono);" x-text="formatDate(result?.metadata?.expiration)"></span>
                        </div>
 
                        <!-- Estado Sincronización -->
                        <div class="dx-v2-tools-moldex-property-row">
                            <span class="dx-v2-tools-moldex-property-label">Sincronización de Inventario</span>
                            <div class="dx-v2-tools-moldex-sync-column">
                                <div x-show="result?.inventory && result?.inventory?.synced" class="dx-v2-tools-moldex-sync-status-ok">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                    COMPLETADA
                                </div>
                                <div x-show="result?.inventory && !result?.inventory?.synced" class="dx-v2-tools-moldex-sync-status-pending">
                                    PENDIENTE
                                </div>
                                <span x-show="result?.inventory && result?.inventory?.client_name" class="dx-v2-tools-moldex-sync-subtitle" x-text="result?.inventory?.client_name"></span>
                                <span x-show="result?.inventory && result?.inventory?.error" class="dx-v2-tools-moldex-sync-error" x-text="result?.inventory?.error"></span>
                            </div>
                        </div>
 
                        <!-- Módulos Detectados -->
                        <div class="dx-v2-tools-moldex-modules-section">
                            <div class="dx-v2-tools-moldex-modules-title">Módulos y Licencias Detectadas</div>
                            
                            <div class="dx-v2-tools-moldex-modules-list">
                                <template x-for="prod in (result?.metadata?.products || [])" :key="prod.code">
                                    <div class="dx-v2-tools-moldex-module-item">
                                        <div class="dx-v2-tools-moldex-module-left">
                                            <div class="dx-v2-tools-moldex-module-bullet">
                                                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="4"><polyline points="20 6 9 17 4 12"/></svg>
                                            </div>
                                            <div class="dx-v2-tools-moldex-module-meta">
                                                <span class="dx-v2-tools-moldex-module-name" x-text="prod.name"></span>
                                                <span x-show="prod.code !== prod.name" class="dx-v2-tools-moldex-module-code" x-text="prod.code"></span>
                                            </div>
                                        </div>
                                        <div class="dx-v2-tools-moldex-module-seats" x-text="prod.quantity"></div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
 
            <div class="dx-v2-tools-moldex-success-banner">
                <div class="dx-v2-tools-moldex-success-banner-left">
                    <div class="dx-v2-tools-moldex-success-banner-icon">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                    </div>
                    <div>
                        <div class="dx-v2-tools-moldex-success-banner-text" x-text="result?.inventory && result?.inventory?.synced ? 'Licencia auditada y sincronizada con el inventario del cliente' : 'Archivo procesado y almacenado correctamente en el servidor'"></div>
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
        <div class="dx-v2-tools-moldex-sidebar-card">
            <div class="dx-v2-tools-moldex-sidebar-title">Estándar de Nomenclatura</div>
            <div class="dx-v2-tools-moldex-sidebar-layout">
                <div class="dx-v2-tools-moldex-sidebar-code-box">
                    AÑO_ID_[PAIS]CLIENTE__TIPO_FECHA.mac
                </div>
                <p class="dx-v2-tools-moldex-sidebar-desc">
                    El sistema renombra automáticamente el archivo basado en el contenido extraído del bloque de comentarios.
                </p>
            </div>
        </div>
 
        <div class="dx-v2-tools-moldex-sidebar-warning">
            <div class="dx-v2-tools-moldex-sidebar-warning-header">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                <span class="dx-v2-tools-moldex-sidebar-warning-title">Aviso de Privacidad</span>
            </div>
            <p class="dx-v2-tools-moldex-sidebar-warning-text">
                Este parser es <strong>local y determinista</strong>. El contenido de la licencia no se envía a ningún motor de IA externo. Los metadatos se almacenan en la base de datos segura del portal.
            </p>
        </div>
 
        <div class="dx-v2-tools-moldex-sidebar-info">
            <div class="dx-v2-tools-moldex-sidebar-info-text">
                <strong>Soporte Técnico:</strong><br>
                Si el parser no detecta el Machine ID, verifique que el archivo no esté corrupto o que el bloque de comentarios <code># [Customer Information]</code> esté presente al inicio.
            </div>
        </div>
    </div>
</div>
 
<script>
function moldexAuditor() {
    return {
        isDragging: false,
        fileName: '',
        loading: false,
        result: null,
        errorMessage: '',
        allowedExtensions: ['mac', 'txt'],
 
        init() {
            console.log('Moldex3D Auditor initialized');
        },
 
        validateFile(file) {
            if (!file) return false;
            const ext = file.name.split('.').pop().toLowerCase();
            if (!this.allowedExtensions.includes(ext)) {
                this.errorMessage = 'Extensión .' + ext + ' no permitida. Use .mac o .txt';
                this.fileName = '';
                this.$refs.fileInput.value = '';
                setTimeout(() => { this.errorMessage = ''; }, 4000);
                return false;
            }
            this.errorMessage = '';
            return true;
        },
 
        handleFileChange(e) {
            if (e.target.files.length > 0) {
                const file = e.target.files[0];
                if (this.validateFile(file)) {
                    this.fileName = file.name;
                }
            }
        },
 
        handleDrop(e) {
            this.isDragging = false;
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                if (this.validateFile(files[0])) {
                    this.$refs.fileInput.files = files;
                    this.fileName = files[0].name;
                }
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
            // YYYYMMDD to DD/MM/YYYY
            return dateStr.substring(6, 8) + '/' + dateStr.substring(4, 6) + '/' + dateStr.substring(0, 4);
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
