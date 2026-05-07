@extends('layouts.app')

@section('title', 'NX Suite')

@section('content')
<div class="page-header">
    <div class="breadcrumb">
        <a href="{{ url('/') }}">Portal</a> ›
        <a href="{{ route('tools.index') }}">Herramientas</a> ›
        NX Suite
    </div>
    <div style="display: flex; align-items: center; gap: 12px; margin-top: 8px;">
        <div class="tool-icon-fallback" style="background: var(--vendor-siemens-dark-muted, rgba(0,153,153,0.1)); color: var(--vendor-siemens, #009999); width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><path d="M9 3v18"/><path d="M15 3v18"/></svg>
        </div>
        <div>
            <h1 class="page-title" style="margin: 0;">NX Suite <span class="vendor-label siemens" style="font-size: 10px; padding: 2px 6px; margin-left: 8px;">Siemens PLM</span></h1>
            <p class="page-sub" style="margin: 0;">Ecosistema de Digital Industries Software y Gestión PLM</p>
        </div>
    </div>
</div>

<div class="grid-main" x-data="{ motor: 'salt', fileName: '', isDragging: false }">
    <div class="main-panel">
        <!-- Bloque 1: Carga -->
        <div class="card" style="margin-bottom: 24px;">
            <div class="card-header" style="justify-content: space-between;">
                <span class="card-title">Transformación de Licencia (NX)</span>
                <span class="badge" style="background: rgba(0,153,153,0.1); color: var(--vendor-siemens, #009999); border: 1px solid rgba(0,153,153,0.2);">PROCESO AUTOMÁTICO</span>
            </div>
            
            <div style="padding: 24px;">
                <div style="margin-bottom: 20px;">
                    <div style="font-size: 11px; font-weight: 600; color: var(--muted); text-transform: uppercase; margin-bottom: 12px;">Selección de Motor Destino</div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <!-- Legacy -->
                        <div @click="motor = 'legacy'" 
                             class="motor-card"
                             :class="motor === 'legacy' ? 'active-red' : ''"
                             style="padding: 16px; border: 1px solid var(--border); border-radius: 6px; cursor: pointer; display: flex; align-items: center; gap: 12px; transition: all 0.2s; background: var(--bg);">
                            <div class="motor-icon" style="transition: color 0.2s;">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
                            </div>
                            <div>
                                <div style="font-size: 13px; font-weight: 600; color: var(--primary);">Hasta NX 2206</div>
                                <div style="font-size: 11px; color: var(--muted); font-family: var(--font-mono);">ugslmd (Legacy)</div>
                            </div>
                        </div>

                        <!-- SALT -->
                        <div @click="motor = 'salt'" 
                             class="motor-card"
                             :class="motor === 'salt' ? 'active-teal' : ''"
                             style="padding: 16px; border: 1px solid var(--border); border-radius: 6px; cursor: pointer; display: flex; align-items: center; gap: 12px; transition: all 0.2s; background: var(--bg);">
                            <div class="motor-icon" style="transition: color 0.2s;">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                            </div>
                            <div>
                                <div style="font-size: 13px; font-weight: 600; color: var(--primary);">Desde NX 2212</div>
                                <div style="font-size: 11px; color: var(--muted); font-family: var(--font-mono);">saltd (Estándar)</div>
                            </div>
                        </div>
                    </div>
                </div>

                <form action="{{ route('tools.nx-suite.process') }}" method="POST" enctype="multipart/form-data" id="nx-form">
                    @csrf
                    <input type="hidden" name="motor" :value="motor">
                    
                    <div class="dropzone" 
                         id="dropzone"
                         :class="[isDragging ? 'dragging' : '', motor === 'legacy' ? 'theme-red' : 'theme-teal']"
                         @dragover.prevent="isDragging = true"
                         @dragleave.prevent="isDragging = false"
                         @drop.prevent="isDragging = false; $refs.fileInput.files = $event.dataTransfer.files; fileName = $refs.fileInput.files[0].name;"
                         @click="$refs.fileInput.click()"
                         style="height: 160px; border-style: dashed; border-width: 1px; border-color: var(--border); border-radius: 4px; margin-bottom: 20px; display: flex; flex-direction: column; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s; background: transparent;">
                        
                        <div style="display: flex; flex-direction: column; align-items: center; gap: 12px;">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" :stroke="motor === 'legacy' ? '#D32F2F' : 'var(--vendor-siemens, #009999)'" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                            
                            <template x-if="!fileName">
                                <div class="dropzone-text" style="font-size: 14px; color: var(--primary);">Arrastre archivo aquí o haga clic para seleccionar</div>
                            </template>
                            <template x-if="fileName">
                                <div class="dropzone-text" style="font-size: 14px; font-weight: 500;" :style="motor === 'legacy' ? 'color: #D32F2F;' : 'color: var(--vendor-siemens, #009999);'" x-text="'Seleccionado: ' + fileName"></div>
                            </template>
                            
                            <div class="dropzone-subtext" style="font-size: 11px; opacity: 0.6; color: var(--secondary);">Soporta .lic, .txt, .dat y .cid</div>
                        </div>
                        <input type="file" name="license_file" x-ref="fileInput" id="file-input" style="display: none;" @change="if($event.target.files.length > 0) fileName = $event.target.files[0].name;">
                    </div>

                    <div style="display: flex; justify-content: flex-end;">
                        <button type="submit" class="btn-primary" :style="motor === 'legacy' ? 'background: #D32F2F; border-color: #D32F2F;' : 'background: var(--vendor-siemens, #009999); border-color: var(--vendor-siemens, #009999);'" style="padding: 10px 24px; font-weight: 600; letter-spacing: 0.5px; display: flex; align-items: center;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 8px;"><polyline points="16 16 12 12 8 16"/><line x1="12" y1="12" x2="12" y2="21"/><path d="M20.39 18.39A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.3"/><polyline points="16 16 12 12 8 16"/></svg>
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
            <div style="padding: 24px;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px;">
                    <!-- Columna Izquierda -->
                    <div style="display: flex; flex-direction: column; gap: 12px;">
                        <div style="font-size: 11px; font-weight: 600; color: var(--muted); text-transform: uppercase; margin-bottom: 4px;">Productos Compatibles</div>
                        @foreach([
                            ['P1', 'Designcenter', 'Licencias base de diseño NX'],
                            ['P2', 'Teamcenter', 'Gestión de ciclo de vida PLM'],
                            ['P3', 'Simcenter 3D', 'Simulación avanzada FEA/CFD'],
                            ['P4', 'Amesim', 'Simulación de sistemas mecatrónicos']
                        ] as $item)
                        <div style="display: flex; align-items: center; border-bottom: 1px solid var(--border-subtle); padding-bottom: 8px;">
                            <code style="min-width: 40px; font-family: var(--font-mono); font-size: 10px; color: var(--vendor-siemens, #009999);">{{ $item[0] }}</code>
                            <div style="flex: 1;">
                                <div style="font-size: 12px; font-weight: 500; color: var(--primary);">{{ $item[1] }}</div>
                                <div style="font-size: 10px; color: var(--muted);">{{ $item[2] }}</div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <!-- Columna Derecha -->
                    <div style="display: flex; flex-direction: column; gap: 12px;">
                        <div style="font-size: 11px; font-weight: 600; color: var(--muted); text-transform: uppercase; margin-bottom: 4px;">Operaciones Automáticas</div>
                        @foreach([
                            ['F1', 'Modificación Hostname', 'Reemplazo por localhost o ANY'],
                            ['F2', 'Normalización', 'Ajuste de formato a saltd o ugslmd'],
                            ['F3', 'Extracción Meta', 'Captura de Sold-To y Fechas'],
                            ['F4', 'Indexación', 'Clasificación en repositorio cliente']
                        ] as $item)
                        <div style="display: flex; align-items: center; border-bottom: 1px solid var(--border-subtle); padding-bottom: 8px;">
                            <code style="min-width: 40px; font-family: var(--font-mono); font-size: 10px; color: var(--vendor-siemens, #009999);">{{ $item[0] }}</code>
                            <div style="flex: 1;">
                                <div style="font-size: 12px; font-weight: 500; color: var(--primary);">{{ $item[1] }}</div>
                                <div style="font-size: 10px; color: var(--muted);">{{ $item[2] }}</div>
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
        <div style="background: var(--warning-bg); border: 1px solid var(--warn-border); padding: 16px; border-radius: 4px;">
            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px; color: var(--warning);">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                <span style="font-size: 12px; font-weight: 700; text-transform: uppercase;">Aviso de Almacenamiento</span>
            </div>
            <div style="font-size: 12px; color: var(--secondary); line-height: 1.6;">
                Las licencias <strong>Temporales</strong> solo se transforman para descarga inmediata. <span style="color: var(--danger); font-weight: 600;">NO</span> se guardarán en el servidor ni afectarán el inventario.
            </div>
        </div>

        <div style="margin-top: 16px; padding: 16px; border: 1px solid var(--border-subtle); border-radius: 4px; background: var(--card-bg);">
            <div style="font-size: 11px; color: var(--muted); line-height: 1.5;">
                <strong>Motores de Licencia:</strong><br>
                - <strong style="color: #D32F2F;">ugslmd:</strong> Daemon clásico. Utilizado en Siemens NX 2206 y versiones anteriores.<br>
                - <strong style="color: var(--vendor-siemens, #009999);">saltd:</strong> Nuevo estándar SALT. Requerido para Siemens NX 2212 en adelante.
            </div>
        </div>

        <div style="margin-top: 16px; padding: 16px; border: 1px solid var(--border-subtle); border-radius: 4px; background: var(--card-bg);">
            <div style="font-size: 11px; color: var(--muted); line-height: 1.5;">
                <strong>Comportamiento Estándar:</strong><br>
                - <strong>Contractuales:</strong> Guardado automático.<br>
                - <strong>Unificadas:</strong> Reemplazo total de cliente.<br>
                - <strong>Dongles:</strong> Captura de HOSTID/COMPOSITE.
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .grid-main {
        display: grid;
        grid-template-columns: 1fr 300px;
        gap: 24px;
        margin-top: 24px;
    }
    
    .card {
        background: var(--card-bg);
        border: 1px solid var(--border);
        border-radius: 4px;
    }
    .card-header {
        padding: 16px 24px;
        border-bottom: 1px solid var(--border);
        display: flex;
        align-items: center;
        background: var(--bg);
        border-radius: 4px 4px 0 0;
    }
    .card-title {
        font-size: 14px;
        font-weight: 600;
        color: var(--primary);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .badge {
        font-size: 10px;
        font-weight: 700;
        padding: 4px 8px;
        border-radius: 4px;
        text-transform: uppercase;
    }
    
    .motor-card:hover {
        background: var(--raised) !important;
        border-color: var(--border-hover) !important;
    }
    
    .motor-card.active-red {
        border-color: #D32F2F !important;
        background: rgba(211,47,47,0.05) !important;
    }
    .motor-card.active-red .motor-icon {
        color: #D32F2F;
    }
    
    .motor-card.active-teal {
        border-color: var(--vendor-siemens, #009999) !important;
        background: var(--vendor-siemens-dark-muted, rgba(0,153,153,0.05)) !important;
    }
    .motor-card.active-teal .motor-icon {
        color: var(--vendor-siemens, #009999);
    }
    
    .motor-icon {
        color: var(--text-3);
    }

    .dropzone {
        transition: border-color 0.2s, background 0.2s, transform 0.2s;
    }
    .dropzone.dragging.theme-red {
        border-color: #D32F2F !important;
        background: rgba(211,47,47,0.02) !important;
    }
    .dropzone.dragging.theme-teal {
        border-color: var(--vendor-siemens, #009999) !important;
        background: var(--vendor-siemens-dark-muted, rgba(0,153,153,0.02)) !important;
    }
    
    .btn-primary {
        color: white;
        border: 1px solid;
        border-radius: 4px;
        cursor: pointer;
        transition: opacity 0.2s;
    }
    .btn-primary:hover {
        opacity: 0.9;
    }
</style>
@endpush
