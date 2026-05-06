@extends('layouts.app')

@section('title', 'Importación de Datos')

@section('content')
<div class="page-header">
    <h1 class="page-title">Importación de Datos Semanal</h1>
    <p class="page-sub">Actualización del catálogo de contratos y clientes mediante ingesta masiva.</p>
</div>

<div class="grid-main">
    <!-- Columna Principal -->
    <div class="main-panel">
        <div class="card">
            <div class="card-header">
                <span class="card-title">Cargar Repositorio (CSV/TXT)</span>
            </div>
            
            <div style="padding: 24px;">
                @if(session('success'))
                    <div class="badge badge-success" style="width: 100%; padding: 12px; margin-bottom: 20px; justify-content: flex-start; text-transform: none;">
                        ✓ {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="badge badge-danger" style="width: 100%; padding: 12px; margin-bottom: 20px; justify-content: flex-start; text-transform: none;">
                        ✕ {{ session('error') }}
                    </div>
                @endif

                <form action="{{ route('admin.import.store') }}" method="POST" enctype="multipart/form-data" x-data="{ dragging: false }">
                    @csrf
                    <div class="dropzone" 
                        @dragover.prevent="dragging = true" 
                        @dragleave.prevent="dragging = false" 
                        @drop.prevent="dragging = false; $refs.fileInput.files = $event.dataTransfer.files"
                        :style="dragging ? 'border-color: var(--accent); background: var(--accent-muted);' : ''"
                        @click="$refs.fileInput.click()"
                    >
                        <input type="file" name="csv_file" x-ref="fileInput" accept=".csv,.txt" required style="display: none;">
                        
                        <div class="drop-icon">📂</div>
                        <div class="drop-title">Arrastra el archivo o haz clic para seleccionar</div>
                        <div class="drop-sub">Soporta formatos .CSV y .TXT con separador ";"</div>
                        
                        <div class="drop-tags">
                            <span class="drop-tag">UTF-8</span>
                            <span class="drop-tag">CONTRATOS</span>
                            <span class="drop-tag">CLIENTES</span>
                        </div>
                    </div>

                    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 24px;">
                        <div style="font-family: var(--font-mono); font-size: 11px; color: var(--muted);">
                            Encoding: AUTO-DETECT
                        </div>
                        <button type="submit" class="btn-primary">
                            <span>INICIAR CARGA</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Columna Lateral (Sidebar Panel) -->
    <div class="sidebar-panel">
        <div class="info-card">
            <div class="info-card-title">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><line x1="10" y1="9" x2="8" y2="9"/></svg>
                <span>Mapeo de Columnas</span>
            </div>
            
            <div class="producto-list" style="margin-top: 16px; gap: 12px;">
                <div class="producto-item" style="justify-content: space-between; border-bottom: 1px solid var(--border-subtle); padding-bottom: 8px;">
                    <span style="font-size: 13px; font-weight: 500; color: var(--secondary);">Contraheader</span>
                    <code style="font-family: var(--font-mono); font-size: 11px; background: var(--bg); padding: 2px 6px; border-radius: 4px; border: 1px solid var(--border);">COL_1</code>
                </div>
                <div class="producto-item" style="justify-content: space-between; border-bottom: 1px solid var(--border-subtle); padding-bottom: 8px;">
                    <span style="font-size: 13px; font-weight: 500; color: var(--secondary);">Client_Name</span>
                    <code style="font-family: var(--font-mono); font-size: 11px; background: var(--bg); padding: 2px 6px; border-radius: 4px; border: 1px solid var(--border);">COL_3</code>
                </div>
                <div class="producto-item" style="justify-content: space-between; border-bottom: 1px solid var(--border-subtle); padding-bottom: 8px;">
                    <span style="font-size: 13px; font-weight: 500; color: var(--secondary);">End_Date</span>
                    <code style="font-family: var(--font-mono); font-size: 11px; background: var(--bg); padding: 2px 6px; border-radius: 4px; border: 1px solid var(--border);">COL_6</code>
                </div>
            </div>

            <div style="margin-top: 16px; font-size: 11px; color: var(--muted); line-height: 1.4;">
                Nota: El sistema ignora automáticamente filas sin identificador CONH válido.
            </div>
        </div>

        <div class="info-card" style="border-left: 3px solid var(--warning); background: var(--warning-bg);">
            <div class="info-card-title" style="color: var(--warning); margin-bottom: 8px;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                <span>Sincronización de Estados</span>
            </div>
            <div class="info-card-body" style="font-size: 12px; color: var(--secondary); line-height: 1.5;">
                Los contratos activos que <strong>no figuren</strong> en el archivo cargado serán marcados como <span class="badge badge-danger" style="font-size: 9px; padding: 1px 4px;">BAJA</span> automáticamente para mantener la integridad del repositorio.
            </div>
        </div>
    </div>
</div>
@endsection
