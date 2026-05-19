@extends('layouts.app')

@section('title', 'Importación de Datos')

@section('content')
<div class="page-header">
    <div class="page-header-info">
        <h1 class="page-title">Gestión de Importación</h1>
        <p class="page-sub">Actualización masiva de contratos y sincronización de estados desde repositorio externo.</p>
    </div>
</div>

<div class="grid-main">
    <div class="main-panel">
        <!-- Bloque 1: Carga -->
        <div class="card dx-v2-import-card-mb">
            <div class="card-header flex justify-between items-center">
                <span class="card-title">Carga de Datos (CSV/TXT)</span>
                <span class="badge badge-ai">PROCESO AUTOMÁTICO</span>
            </div>
            
            <div class="dx-v2-import-metadata-container">
                @if(session('success'))
                    <div class="dx-v2-import-success-alert">
                        <div class="dx-v2-import-success-alert-left">
                            <svg class="dx-v2-import-success-alert-svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                            {{ session('success') }}
                        </div>
                        @if(session('log_id'))
                            <a href="{{ route('admin.import.logs.show', session('log_id')) }}" class="dx-v2-import-success-alert-btn">
                                VER DETALLES
                            </a>
                        @endif
                    </div>
                @endif

                <form action="{{ route('admin.import.store') }}" method="POST" enctype="multipart/form-data" id="import-form">
                    @csrf
                    <div class="dropzone dx-v2-import-dropzone" id="dropzone" onclick="document.getElementById('file-input').click()">
                        <div class="dx-v2-import-dropzone-inner">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                            <div class="dropzone-text dx-v2-import-dropzone-title">Arrastre archivo aquí o haga clic para seleccionar</div>
                            <div id="file-name-display" class="dropzone-subtext dx-v2-import-dropzone-subtext">Soporta .csv y .txt (UTF-8)</div>
                        </div>
                        <input type="file" name="csv_file" id="file-input" style="display: none;" onchange="updateFileName(this)">
                    </div>

                    <div class="dx-v2-import-submit-row">
                        <button type="submit" class="btn-primary dx-v2-import-btn-submit">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 8px;"><polyline points="16 16 12 12 8 16"/><line x1="12" y1="12" x2="12" y2="21"/><path d="M20.39 18.39A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.3"/><polyline points="16 16 12 12 8 16"/></svg>
                            PROCESAR ARCHIVO
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Bloque 2: Mapeo -->
        <div class="card">
            <div class="card-header">
                <span class="card-title">Protocolo de Mapeo de Datos</span>
            </div>
            <div class="dx-v2-import-metadata-container">
                <div class="dx-v2-import-mapping-grid">
                    <!-- Columna Izquierda -->
                    <div class="dx-v2-import-mapping-col">
                        @foreach([
                            ['C1', 'Contraheader', 'Identificador único del contrato (CONH)'],
                            ['C2', 'Centro Coste', 'Referencia contable del cliente'],
                            ['C3', 'Cliente', 'Nombre fiscal de la empresa'],
                            ['C4', 'Vendor', 'Proveedor origen (Siemens/Moldex)'],
                            ['C5', 'Producto', 'Módulo o paquete de software']
                        ] as $item)
                        <div class="dx-v2-import-mapping-item">
                            <code class="dx-v2-import-mapping-code">{{ $item[0] }}</code>
                            <div class="dx-v2-import-mapping-content">
                                <div class="dx-v2-import-mapping-title">{{ $item[1] }}</div>
                                <div class="dx-v2-import-mapping-desc">{{ $item[2] }}</div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <!-- Columna Derecha -->
                    <div class="dx-v2-import-mapping-col">
                        @foreach([
                            ['C6', 'Sub-Producto', 'Variante o nivel del producto'],
                            ['C7', 'Vencimiento', 'Fecha efectiva de caducidad (DD/MM/AAAA)'],
                            ['C8', 'Estado', 'Situación actual en origen'],
                            ['C9', 'Comentarios', 'Observaciones técnicas adicionales']
                        ] as $item)
                        <div class="dx-v2-import-mapping-item">
                            <code class="dx-v2-import-mapping-code">{{ $item[0] }}</code>
                            <div class="dx-v2-import-mapping-content">
                                <div class="dx-v2-import-mapping-title">{{ $item[1] }}</div>
                                <div class="dx-v2-import-mapping-desc">{{ $item[2] }}</div>
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
        <div class="dx-v2-import-info-box">
            <div class="dx-v2-import-info-header">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                <span class="dx-v2-import-info-title">Aviso de Sincronización</span>
            </div>
            <div class="dx-v2-import-info-text">
                La carga es **destructiva** para estados obsoletos. Contratos activos ausentes en este archivo serán marcados automáticamente como <span class="dx-v2-import-info-text-danger">BAJA</span>.
            </div>
        </div>

        <div class="dx-v2-import-sidebar-links">
            <a href="{{ route('admin.import.logs.index') }}" class="btn-secondary dx-v2-import-sidebar-btn">
                <i class="fa-solid fa-clock-rotate-left"></i>
                Ver Historial de Logs
            </a>
        </div>

        <div class="dx-v2-import-sidebar-links">
            <a href="{{ route('admin.normalization.index') }}" class="btn-primary dx-v2-import-sidebar-btn">
                <i class="fa-solid fa-wand-magic-sparkles"></i>
                Bandeja de Normalización
            </a>
        </div>

        <div class="dx-v2-import-req-box">
            <div class="dx-v2-import-req-text">
                <strong>Requisitos Técnicos:</strong><br>
                - Encoding: UTF-8 (Sin BOM)<br>
                - Separador: Automático (`,` o `;`)
            </div>
        </div>
    </div>
</div>

<script>
function updateFileName(input) {
    const display = document.getElementById('file-name-display');
    if (input.files && input.files.length > 0) {
        display.textContent = 'Seleccionado: ' + input.files[0].name;
        display.style.color = 'var(--accent)';
        display.style.opacity = '1';
    }
}
</script>
@endsection
