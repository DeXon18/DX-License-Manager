@extends('layouts.app')

@section('title', 'HEEDS')

@section('content')
<div class="page-header">
    <div class="breadcrumb">
        <a href="{{ url('/') }}">Portal</a> ›
        <a href="{{ route('tools.index') }}">Herramientas</a> ›
        HEEDS
    </div>
    <div class="dx-v2-tools-heeds-header-layout">
        <div class="dx-v2-tools-heeds-header-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>
        </div>
        <div>
            <h1 class="dx-v2-tools-heeds-header-title">HEEDS <span class="vendor-label siemens">Siemens Digital Industries</span></h1>
            <p class="dx-v2-tools-heeds-header-sub">Software de exploración y optimización de diseño multidisciplinar</p>
        </div>
    </div>
</div>

<div class="grid-main" x-data="{ 
        fileName: '', 
        isDragging: false,
        errorMessage: '',
        allowedExtensions: ['lic', 'txt', 'dat', 'cid'],
        validateAndSubmit(e) {
            const file = this.$refs.fileInput.files[0];
            if (!file) return;
            
            const ext = file.name.split('.').pop().toLowerCase();
            if (!this.allowedExtensions.includes(ext)) {
                this.errorMessage = 'Extensión .' + ext + ' no permitida. Use .lic, .txt, .dat o .cid';
                this.fileName = '';
                this.$refs.fileInput.value = '';
                setTimeout(() => { this.errorMessage = ''; }, 4000);
                return;
            }
            this.$el.submit();
        }
    }">
    <div class="main-panel">
        <!-- Bloque 1: Carga -->
        <div class="card mb-4">
            <div class="card-header dx-v2-tools-heeds-card-header">
                <span class="card-title">Procesamiento de Licencia (rctech → saltd)</span>
                <span class="badge" style="background: rgba(0,153,153,0.1); color: var(--vendor-siemens, #009999); border: 1px solid rgba(0,153,153,0.2);">MOTOR SALT (29000)</span>
            </div>
            
            <div class="dx-v2-tools-heeds-card-body">
                <!-- Error Message Alert -->
                <template x-if="errorMessage">
                    <div x-transition 
                         style="margin-bottom: 16px; padding: 12px 16px; background: rgba(211, 47, 47, 0.1); border-left: 4px solid #D32F2F; color: #D32F2F; font-size: 13px; font-weight: 600; border-radius: 4px; display: flex; align-items: center; gap: 10px;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        <span x-text="errorMessage"></span>
                    </div>
                </template>

                <form action="{{ route('tools.heeds.process') }}" method="POST" enctype="multipart/form-data" id="heeds-form" @submit.prevent="validateAndSubmit">
                    @csrf
                    
                    <div class="dx-v2-tools-heeds-dropzone" 
                         id="dropzone"
                         :class="isDragging ? 'dragging' : ''"
                         @dragover.prevent="isDragging = true"
                         @dragleave.prevent="isDragging = false"
                         @drop.prevent="isDragging = false; $refs.fileInput.files = $event.dataTransfer.files; fileName = $refs.fileInput.files[0].name; validateAndSubmit();"
                         @click="$refs.fileInput.click()">
                        
                        <div class="dx-v2-tools-heeds-dropzone-content">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="var(--vendor-siemens, #009999)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                            
                            <template x-if="!fileName">
                                <div class="dx-v2-tools-heeds-dropzone-title">Arrastre archivo .lic aquí o haga clic</div>
                            </template>
                            <template x-if="fileName">
                                <div class="dx-v2-tools-heeds-dropzone-title" style="color: var(--vendor-siemens, #009999) !important;" x-text="'Seleccionado: ' + fileName"></div>
                            </template>
                            
                            <div class="dx-v2-tools-heeds-dropzone-subtitle">Daemon detectado: rctech</div>
                        </div>
                        <input type="file" name="license_file" x-ref="fileInput" id="file-input" style="display: none;" @change="if($event.target.files.length > 0) fileName = $event.target.files[0].name;">
                    </div>

                    <div class="dx-v2-tools-heeds-btn-container">
                        <button type="submit" class="btn-primary" style="background: var(--vendor-siemens, #009999); border-color: var(--vendor-siemens, #009999); padding: 10px 24px; font-weight: 600; letter-spacing: 0.5px; display: flex; align-items: center; gap: 8px;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="16 16 12 12 8 16"/><line x1="12" y1="12" x2="12" y2="21"/><path d="M20.39 18.39A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.3"/><polyline points="16 16 12 12 8 16"/></svg>
                            PROCESAR Y AUDITAR
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Bloque 2: Detalles del Proceso -->
        <div class="card">
            <div class="card-header">
                <span class="card-title">Especificaciones HEEDS</span>
            </div>
            <div class="dx-v2-tools-heeds-card-body">
                <div class="dx-v2-tools-heeds-specs-grid">
                    <div class="dx-v2-tools-heeds-specs-column">
                        <div class="dx-v2-tools-heeds-specs-title">Auditoría IA (RCTECH)</div>
                        @foreach([
                            ['A1', 'Parser de Cabecera', 'Extracción precisa desde Siemens Comment Block'],
                            ['A2', 'Vendor String', 'Soporte fallback para sold-to/cliente'],
                            ['A3', 'Integridad', 'Validación de fecha de expiración en INCREMENT']
                        ] as $item)
                        <div class="dx-v2-tools-heeds-spec-row">
                            <code class="dx-v2-tools-heeds-spec-code">{{ $item[0] }}</code>
                            <div class="dx-v2-tools-heeds-spec-details">
                                <div class="dx-v2-tools-heeds-spec-name">{{ $item[1] }}</div>
                                <div class="dx-v2-tools-heeds-spec-desc">{{ $item[2] }}</div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="dx-v2-tools-heeds-specs-column">
                        <div class="dx-v2-tools-heeds-specs-title">Nomenclatura HEEDS</div>
                        @foreach([
                            ['N1', 'Versión V', 'Inclusión automática de versión mayor'],
                            ['N2', 'Tag Valida', 'Identificación de estado contractual'],
                            ['N3', 'Tag TEMP', 'Identificación de licencias temporales']
                        ] as $item)
                        <div class="dx-v2-tools-heeds-spec-row">
                            <code class="dx-v2-tools-heeds-spec-code">{{ $item[0] }}</code>
                            <div class="dx-v2-tools-heeds-spec-details">
                                <div class="dx-v2-tools-heeds-spec-name">{{ $item[1] }}</div>
                                <div class="dx-v2-tools-heeds-spec-desc">{{ $item[2] }}</div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Panel Informativo -->
    <div class="sidebar-panel">
        @include('tools.partials._engine_selector')

        <div class="dx-v2-tools-heeds-sidebar-warning">
            <div class="dx-v2-tools-heeds-sidebar-warning-header">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                <span class="dx-v2-tools-heeds-sidebar-warning-title">Aviso de Almacenamiento</span>
            </div>
            <div class="dx-v2-tools-heeds-sidebar-warning-desc">
                Las licencias <strong>Temporales</strong> solo se transforman para descarga inmediata. <strong>NO</strong> se guardarán en el servidor ni afectarán el inventario.
            </div>
        </div>

        <div class="dx-v2-tools-heeds-sidebar-info">
            <div class="dx-v2-tools-heeds-sidebar-info-text">
                <strong>Transformación SALT (HEEDS):</strong><br>
                - <strong>SERVER PORT:</strong> 29000<br>
                - <strong>VENDOR PORT:</strong> 29001<br>
                - <strong>Daemon:</strong> saltd (desde rctech)
            </div>
        </div>
    </div>
</div>
@endsection
