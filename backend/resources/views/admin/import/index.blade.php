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
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                <span>Guía de Importación</span>
            </div>
            
            <div class="info-card-body" style="margin-top: 12px;">
                <p style="font-size: 12px; margin-bottom: 16px; color: var(--muted);">El sistema procesa el archivo buscando estas columnas específicas:</p>
                
                <div style="display: flex; flex-direction: column; gap: 14px;">
                    <div style="display: flex; align-items: flex-start; gap: 10px;">
                        <span class="badge badge-ai" style="min-width: 45px; justify-content: center;">COL 1</span>
                        <div>
                            <div style="font-size: 13px; font-weight: 600; color: var(--primary);">Contraheader</div>
                            <div style="font-size: 11px; color: var(--muted);">ID del contrato (ej: CONH100...)</div>
                        </div>
                    </div>

                    <div style="display: flex; align-items: flex-start; gap: 10px;">
                        <span class="badge badge-ai" style="min-width: 45px; justify-content: center;">COL 3</span>
                        <div>
                            <div style="font-size: 13px; font-weight: 600; color: var(--primary);">Cliente</div>
                            <div style="font-size: 11px; color: var(--muted);">Nombre de la empresa</div>
                        </div>
                    </div>

                    <div style="display: flex; align-items: flex-start; gap: 10px;">
                        <span class="badge badge-ai" style="min-width: 45px; justify-content: center;">COL 6</span>
                        <div>
                            <div style="font-size: 13px; font-weight: 600; color: var(--primary);">Vencimiento</div>
                            <div style="font-size: 11px; color: var(--muted);">Fecha en formato DD/MM/AAAA</div>
                        </div>
                    </div>
                </div>
            </div>

            <div style="margin-top: 20px; padding-top: 12px; border-top: 1px solid var(--border-subtle); font-size: 11px; color: var(--muted); line-height: 1.4;">
                <strong>Sugerencia:</strong> Verifica que el archivo no tenga filas vacías al inicio y que el separador sea coherente.
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
