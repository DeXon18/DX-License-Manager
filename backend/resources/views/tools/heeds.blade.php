@extends('layouts.app')

@section('title', 'HEEDS')

@section('content')
<div class="page-header">
    <div class="breadcrumb">
        <a href="{{ url('/') }}">Portal</a> ›
        <a href="{{ route('tools.index') }}">Herramientas</a> ›
        HEEDS
    </div>
    <div style="display: flex; align-items: center; gap: 12px; margin-top: 8px;">
        <div class="tool-icon-fallback" style="background: var(--vendor-siemens-dark-muted, rgba(0,153,153,0.1)); color: var(--vendor-siemens, #009999); width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="22" y1="12" x2="18" y2="12"/><line x1="6" y1="12" x2="2" y2="12"/><line x1="12" y1="6" x2="12" y2="2"/><line x1="12" y1="22" x2="12" y2="18"/></svg>
        </div>
        <div>
            <h1 class="page-title" style="margin: 0; font-family: var(--font-sans); letter-spacing: -0.02em;">HEEDS Suite <span class="vendor-label siemens" style="font-size: 10px; padding: 2px 6px; margin-left: 8px;">Siemens Digital Industries</span></h1>
            <p class="page-sub" style="margin: 0; opacity: 0.8;">Motor de exploración y optimización de diseño multidisciplinar</p>
        </div>
    </div>
</div>

<div class="grid-main" x-data="{ fileName: '', isDragging: false }">
    <div class="main-panel">
        <!-- Bloque 1: Carga -->
        <div class="card" style="margin-bottom: 24px;">
            <div class="card-header" style="justify-content: space-between;">
                <span class="card-title">Procesamiento de Licencia (rctech → saltd)</span>
                <span class="badge" style="background: rgba(0,153,153,0.1); color: var(--vendor-siemens, #009999); border: 1px solid rgba(0,153,153,0.2);">MOTOR SALT (29000)</span>
            </div>
            
            <div style="padding: 24px;">
                <form action="{{ route('tools.heeds.process') }}" method="POST" enctype="multipart/form-data" id="heeds-form">
                    @csrf
                    
                    <div class="dropzone" 
                         id="dropzone"
                         :class="isDragging ? 'dragging theme-teal' : ''"
                         @dragover.prevent="isDragging = true"
                         @dragleave.prevent="isDragging = false"
                         @drop.prevent="isDragging = false; $refs.fileInput.files = $event.dataTransfer.files; fileName = $refs.fileInput.files[0].name;"
                         @click="$refs.fileInput.click()"
                         style="height: 160px; border-style: dashed; border-width: 1px; border-color: var(--border); border-radius: 4px; margin-bottom: 20px; display: flex; flex-direction: column; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s; background: transparent;">
                        
                        <div style="display: flex; flex-direction: column; align-items: center; gap: 12px;">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="var(--vendor-siemens, #009999)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                            
                            <template x-if="!fileName">
                                <div class="dropzone-text" style="font-size: 14px; color: var(--primary);">Arrastre archivo .lic aquí o haga clic</div>
                            </template>
                            <template x-if="fileName">
                                <div class="dropzone-text" style="font-size: 14px; font-weight: 500; color: var(--vendor-siemens, #009999);" x-text="'Seleccionado: ' + fileName"></div>
                            </template>
                            
                            <div class="dropzone-subtext" style="font-size: 11px; opacity: 0.6; color: var(--secondary); font-family: var(--font-mono);">[ DAEMON: rctech ]</div>
                        </div>
                        <input type="file" name="license_file" x-ref="fileInput" id="file-input" style="display: none;" @change="if($event.target.files.length > 0) fileName = $event.target.files[0].name;">
                    </div>

                    <div style="display: flex; justify-content: flex-end;">
                        <button type="submit" class="btn-primary" style="background: var(--vendor-siemens, #009999); border-color: var(--vendor-siemens, #009999); padding: 10px 24px; font-weight: 600; letter-spacing: 0.5px; display: flex; align-items: center;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 8px;"><polyline points="16 16 12 12 8 16"/><line x1="12" y1="12" x2="12" y2="21"/><path d="M20.39 18.39A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.3"/><polyline points="16 16 12 12 8 16"/></svg>
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
            <div style="padding: 24px;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px;">
                    <div style="display: flex; flex-direction: column; gap: 12px;">
                        <div style="font-size: 11px; font-weight: 600; color: var(--muted); text-transform: uppercase; margin-bottom: 4px;">Auditoría IA (RCTECH)</div>
                        @foreach([
                            ['A1', 'Parser de Cabecera', 'Extracción precisa desde Siemens Comment Block'],
                            ['A2', 'Vendor String', 'Soporte fallback para sold-to/cliente'],
                            ['A3', 'Integridad', 'Validación de fecha de expiración en INCREMENT']
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
                    <div style="display: flex; flex-direction: column; gap: 12px;">
                        <div style="font-size: 11px; font-weight: 600; color: var(--muted); text-transform: uppercase; margin-bottom: 4px;">Nomenclatura HEEDS</div>
                        @foreach([
                            ['N1', 'Versión V', 'Inclusión automática de versión mayor'],
                            ['N2', 'Tag Valida', 'Identificación de estado contractual'],
                            ['N3', 'Tag TEMP', 'Identificación de licencias temporales']
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
            <div style="font-size: 11px; color: var(--muted); line-height: 1.5; font-family: var(--font-mono);">
                <strong style="font-family: var(--font-sans); color: var(--primary);">TRANSFORMACIÓN SALT (HEEDS)</strong><br>
                <span style="opacity: 0.8;">> SERVER PORT:</span> <span style="color: var(--vendor-siemens);">29000</span><br>
                <span style="opacity: 0.8;">> VENDOR PORT:</span> <span style="color: var(--vendor-siemens);">29001</span><br>
                <span style="opacity: 0.8;">> DAEMON:</span> <span style="color: var(--vendor-siemens);">saltd</span> <span style="opacity: 0.5;">(ex rctech)</span>
            </div>
        </div>
    </div>
</div>

<!-- Bloque 3: Historial Reciente -->
<div class="card" style="margin-top: 24px;">
    <div class="card-header" style="justify-content: space-between;">
        <span class="card-title">Auditorías Recientes HEEDS</span>
        <div style="display: flex; align-items: center; gap: 8px;">
            <div style="width: 8px; height: 8px; background: var(--success); border-radius: 50%; animate: pulse 2s infinite;"></div>
            <span style="font-size: 10px; font-weight: 600; color: var(--muted); text-transform: uppercase;">Motor de Auditoría Live</span>
        </div>
    </div>
    <div style="padding: 0;">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="padding-left: 24px;">SOLD-TO</th>
                    <th>CLIENTE</th>
                    <th>VERSIÓN</th>
                    <th>ESTADO IA</th>
                    <th style="text-align: right; padding-right: 24px;">FECHA</th>
                </tr>
            </thead>
            <tbody>
                <tr style="opacity: 0.5;">
                    <td colspan="5" style="text-align: center; padding: 40px; font-size: 12px; color: var(--muted);">
                        No hay procesamientos recientes en esta sesión. Las licencias contractuales aparecerán en el inventario del cliente.
                    </td>
                </tr>
            </tbody>
        </table>
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

    .dropzone.dragging.theme-teal {
        border-color: var(--vendor-siemens, #009999) !important;
        background: var(--vendor-siemens-dark-muted, rgba(0,153,153,0.02)) !important;
    }
</style>
@endpush
