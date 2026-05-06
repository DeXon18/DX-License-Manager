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
            <div class="card-section-title">
                <span>📋 Estructura Técnica</span>
            </div>
            <div class="producto-list">
                <div class="producto-item">
                    <div class="producto-check" style="background: var(--accent-muted); color: var(--accent);">1</div>
                    <span style="font-family: var(--font-mono); font-size: 12px;">Contraheader</span>
                </div>
                <div class="producto-item">
                    <div class="producto-check" style="background: var(--accent-muted); color: var(--accent);">3</div>
                    <span style="font-family: var(--font-mono); font-size: 12px;">Client_Name</span>
                </div>
                <div class="producto-item">
                    <div class="producto-check" style="background: var(--accent-muted); color: var(--accent);">6</div>
                    <span style="font-family: var(--font-mono); font-size: 12px;">End_Date</span>
                </div>
            </div>
        </div>

        <div class="info-card" style="border-color: var(--warn-border); background: var(--warn-bg);">
            <div class="card-section-title" style="color: var(--warning);">
                <span>▲ Integridad de Datos</span>
            </div>
            <div class="info-card-body" style="color: var(--warning); opacity: 0.9;">
                Los contratos activos ausentes en la carga serán movidos a <strong>BAJA</strong> automáticamente.
            </div>
        </div>
    </div>
</div>
@endsection
