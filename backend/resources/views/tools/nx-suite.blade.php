@extends('layouts.app')

@section('title', 'NX Suite')

@section('content')
<div class="page-header">
    <div class="breadcrumb">
        <a href="{{ url('/') }}">Portal</a> ›
        <a href="{{ route('tools.index') }}">Herramientas</a> ›
        NX Suite
    </div>
    <div class="dx-v2-tools-nx-header-layout">
        <div class="dx-v2-tools-nx-header-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><path d="M9 3v18"/><path d="M15 3v18"/></svg>
        </div>
        <div>
            <h1 class="dx-v2-tools-nx-header-title">NX Suite <span class="vendor-label siemens">Siemens PLM</span></h1>
            <p class="dx-v2-tools-nx-header-sub">Ecosistema de Digital Industries Software y Gestión PLM</p>
        </div>
    </div>
</div>

<div class="grid-main" x-data="{ 
        motor: 'salt', 
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
            <div class="card-header dx-v2-tools-nx-card-header">
                <span class="card-title">Transformación de Licencia (NX)</span>
                <span class="badge" style="background: rgba(0,153,153,0.1); color: var(--vendor-siemens, #009999); border: 1px solid rgba(0,153,153,0.2);">PROCESO AUTOMÁTICO</span>
            </div>
            
            <div class="dx-v2-tools-nx-card-body">
                <!-- Error Message Alert -->
                <template x-if="errorMessage">
                    <div x-transition 
                         style="margin-bottom: 16px; padding: 12px 16px; background: rgba(211, 47, 47, 0.1); border-left: 4px solid #D32F2F; color: #D32F2F; font-size: 13px; font-weight: 600; border-radius: 4px; display: flex; align-items: center; gap: 10px;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        <span x-text="errorMessage"></span>
                    </div>
                </template>

                <div>
                    <div class="dx-v2-tools-nx-motor-section-title">Selección de Motor Destino</div>
                    
                    <div class="dx-v2-tools-nx-motor-grid">
                        <!-- Legacy -->
                        <div @click="motor = 'legacy'" 
                             class="dx-v2-tools-nx-motor-card"
                             :class="motor === 'legacy' ? 'active-red' : ''">
                            <div class="dx-v2-tools-nx-motor-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
                            </div>
                            <div>
                                <div style="font-size: 13px; font-weight: 600; color: var(--dx-v2-primary-base);">Hasta NX 2206</div>
                                <div style="font-size: 11px; color: var(--dx-v2-muted); font-family: var(--dx-v2-font-mono);">ugslmd (Legacy)</div>
                            </div>
                        </div>

                        <!-- SALT -->
                        <div @click="motor = 'salt'" 
                             class="dx-v2-tools-nx-motor-card"
                             :class="motor === 'salt' ? 'active-teal' : ''">
                            <div class="dx-v2-tools-nx-motor-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                            </div>
                            <div>
                                <div style="font-size: 13px; font-weight: 600; color: var(--dx-v2-primary-base);">Desde NX 2212</div>
                                <div style="font-size: 11px; color: var(--dx-v2-muted); font-family: var(--dx-v2-font-mono);">saltd (Estándar)</div>
                            </div>
                        </div>
                    </div>
                </div>

                <form action="{{ route('tools.nx-suite.process') }}" method="POST" enctype="multipart/form-data" id="nx-form" @submit.prevent="validateAndSubmit">
                    @csrf
                    <input type="hidden" name="motor" :value="motor">
                    
                    <div class="dx-v2-tools-nx-dropzone" 
                         id="dropzone"
                         :class="[isDragging ? 'dragging' : '', motor === 'legacy' ? 'theme-red' : 'theme-teal']"
                         @dragover.prevent="isDragging = true"
                         @dragleave.prevent="isDragging = false"
                         @drop.prevent="isDragging = false; $refs.fileInput.files = $event.dataTransfer.files; fileName = $refs.fileInput.files[0].name; validateAndSubmit();"
                         @click="$refs.fileInput.click()">
                        
                        <div class="dx-v2-tools-nx-dropzone-content">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" :stroke="motor === 'legacy' ? '#D32F2F' : 'var(--vendor-siemens, #009999)'" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                            
                            <template x-if="!fileName">
                                <div class="dx-v2-tools-nx-dropzone-title">Arrastre archivo aquí o haga clic para seleccionar</div>
                            </template>
                            <template x-if="fileName">
                                <div class="dx-v2-tools-nx-dropzone-title" :style="motor === 'legacy' ? 'color: #D32F2F !important;' : 'color: var(--vendor-siemens, #009999) !important;'" x-text="'Seleccionado: ' + fileName"></div>
                            </template>
                            
                            <div class="dx-v2-tools-nx-dropzone-subtitle">Soporta .lic, .txt, .dat y .cid</div>
                        </div>
                        <input type="file" name="license_file" x-ref="fileInput" id="file-input" style="display: none;" @change="if($event.target.files.length > 0) fileName = $event.target.files[0].name;">
                    </div>

                    <div class="dx-v2-tools-nx-btn-container">
                        <button type="submit" class="btn-primary" :style="motor === 'legacy' ? 'background: #D32F2F; border-color: #D32F2F;' : 'background: var(--vendor-siemens, #009999); border-color: var(--vendor-siemens, #009999);'" style="padding: 10px 24px; font-weight: 600; letter-spacing: 0.5px; display: flex; align-items: center; justify-content: center; gap: 8px;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="16 16 12 12 8 16"/><line x1="12" y1="12" x2="12" y2="21"/><path d="M20.39 18.39A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.3"/><polyline points="16 16 12 12 8 16"/></svg>
                            PROCESAR LICENCIA
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Bloque 2: Especificaciones -->
        <div class="card">
            <div class="card-header">
                <span class="card-title">Especificaciones de Transformación</span>
            </div>
            <div class="dx-v2-tools-nx-card-body">
                <div class="dx-v2-tools-nx-specs-grid">
                    <!-- Columna Izquierda -->
                    <div class="dx-v2-tools-nx-specs-column">
                        <div class="dx-v2-tools-nx-specs-title">Productos Compatibles</div>
                        @foreach([
                            ['P1', 'Designcenter', 'Licencias base de diseño NX'],
                            ['P2', 'Teamcenter', 'Gestión de ciclo de vida PLM'],
                            ['P3', 'Simcenter 3D', 'Simulación avanzada FEA/CFD'],
                            ['P4', 'Amesim', 'Simulación de sistemas mecatrónicos']
                        ] as $item)
                        <div class="dx-v2-tools-nx-spec-row">
                            <code class="dx-v2-tools-nx-spec-code">{{ $item[0] }}</code>
                            <div class="dx-v2-tools-nx-spec-details">
                                <div class="dx-v2-tools-nx-spec-name">{{ $item[1] }}</div>
                                <div class="dx-v2-tools-nx-spec-desc">{{ $item[2] }}</div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <!-- Columna Derecha -->
                    <div class="dx-v2-tools-nx-specs-column">
                        <div class="dx-v2-tools-nx-specs-title">Operaciones Automáticas</div>
                        @foreach([
                            ['F1', 'Modificación Hostname', 'Reemplazo por localhost o ANY'],
                            ['F2', 'Normalización', 'Ajuste de formato a saltd o ugslmd'],
                            ['F3', 'Extracción Meta', 'Captura de Sold-To y Fechas'],
                            ['F4', 'Indexación', 'Clasificación en repositorio cliente']
                        ] as $item)
                        <div class="dx-v2-tools-nx-spec-row">
                            <code class="dx-v2-tools-nx-spec-code">{{ $item[0] }}</code>
                            <div class="dx-v2-tools-nx-spec-details">
                                <div class="dx-v2-tools-nx-spec-name">{{ $item[1] }}</div>
                                <div class="dx-v2-tools-nx-spec-desc">{{ $item[2] }}</div>
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

        <div class="dx-v2-tools-nx-sidebar-warning">
            <div class="dx-v2-tools-nx-sidebar-warning-header">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                <span class="dx-v2-tools-nx-sidebar-warning-title">Aviso de Almacenamiento</span>
            </div>
            <div class="dx-v2-tools-nx-sidebar-warning-desc">
                Las licencias <strong>Temporales</strong> solo se transforman para descarga inmediata. <span class="text-danger font-weight-bold">NO</span> se guardarán en el servidor ni afectarán el inventario.
            </div>
        </div>

        <div class="dx-v2-tools-nx-sidebar-info">
            <div class="dx-v2-tools-nx-sidebar-info-text">
                <strong>Motores de Licencia:</strong><br>
                - <strong class="text-danger">ugslmd:</strong> Daemon clásico. Utilizado en Siemens NX 2206 y versiones anteriores.<br>
                - <strong style="color: var(--vendor-siemens, #009999);">saltd:</strong> Nuevo estándar SALT. Requerido para Siemens NX 2212 en adelante.
            </div>
        </div>

        <div class="dx-v2-tools-nx-sidebar-info">
            <div class="dx-v2-tools-nx-sidebar-info-text">
                <strong>Comportamiento Estándar:</strong><br>
                - <strong>Contractuales:</strong> Guardado automático.<br>
                - <strong>Unificadas:</strong> Reemplazo total de cliente.<br>
                - <strong>Dongles:</strong> Captura de HOSTID/COMPOSITE.
            </div>
        </div>
    </div>
</div>
@endsection
