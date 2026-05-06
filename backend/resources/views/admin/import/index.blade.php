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

                <form action="{{ route('admin.import.store') }}" method="POST" enctype="multipart/form-data" id="import-form">
                    @csrf
                    <div class="dropzone" id="dropzone" onclick="document.getElementById('file-input').click()">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="color: var(--accent); margin-bottom: 16px;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                        <div class="dropzone-text">Arrastra el archivo CSV aquí o <span>haz clic para buscar</span></div>
                        <div class="dropzone-subtext">Soporta formatos .csv y .txt con codificación UTF-8</div>
                        <input type="file" name="csv_file" id="file-input" style="display: none;" onchange="this.form.submit()">
                    </div>
                </form>

                <!-- Guía Técnica Expandida -->
                <div class="info-card" style="margin-top: 24px;">
                    <div class="info-card-title">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                        <span>Especificación Técnica del Repositorio</span>
                    </div>
                    
                    <div class="info-card-body" style="margin-top: 16px;">
                        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 16px;">
                            <div style="display: flex; gap: 10px; align-items: flex-start;">
                                <span class="badge badge-ai" style="min-width: 42px; justify-content: center;">C1</span>
                                <div>
                                    <div style="font-size: 12px; font-weight: 600; color: var(--primary);">Contraheader</div>
                                    <div style="font-size: 10px; color: var(--muted);">ID Contrato (CONH...)</div>
                                </div>
                            </div>
                            <div style="display: flex; gap: 10px; align-items: flex-start;">
                                <span class="badge badge-ai" style="min-width: 42px; justify-content: center;">C2</span>
                                <div>
                                    <div style="font-size: 12px; font-weight: 600; color: var(--primary);">Centro Coste</div>
                                    <div style="font-size: 10px; color: var(--muted);">Referencia interna</div>
                                </div>
                            </div>
                            <div style="display: flex; gap: 10px; align-items: flex-start;">
                                <span class="badge badge-ai" style="min-width: 42px; justify-content: center;">C3</span>
                                <div>
                                    <div style="font-size: 12px; font-weight: 600; color: var(--primary);">Cliente</div>
                                    <div style="font-size: 10px; color: var(--muted);">Nombre de la empresa</div>
                                </div>
                            </div>
                            <div style="display: flex; gap: 10px; align-items: flex-start;">
                                <span class="badge badge-ai" style="min-width: 42px; justify-content: center;">C4</span>
                                <div>
                                    <div style="font-size: 12px; font-weight: 600; color: var(--primary);">Vendor</div>
                                    <div style="font-size: 10px; color: var(--muted);">Fabricante (Siemens...)</div>
                                </div>
                            </div>
                            <div style="display: flex; gap: 10px; align-items: flex-start;">
                                <span class="badge badge-ai" style="min-width: 42px; justify-content: center;">C5</span>
                                <div>
                                    <div style="font-size: 12px; font-weight: 600; color: var(--primary);">Producto</div>
                                    <div style="font-size: 10px; color: var(--muted);">Tipo de licencia</div>
                                </div>
                            </div>
                            <div style="display: flex; gap: 10px; align-items: flex-start;">
                                <span class="badge badge-ai" style="min-width: 42px; justify-content: center;">C6</span>
                                <div>
                                    <div style="font-size: 12px; font-weight: 600; color: var(--primary);">Vencimiento</div>
                                    <div style="font-size: 10px; color: var(--muted);">DD/MM/AAAA</div>
                                </div>
                            </div>
                            <div style="display: flex; gap: 10px; align-items: flex-start;">
                                <span class="badge badge-ai" style="min-width: 42px; justify-content: center;">C7</span>
                                <div>
                                    <div style="font-size: 12px; font-weight: 600; color: var(--primary);">Estado</div>
                                    <div style="font-size: 10px; color: var(--muted);">Situación contrato</div>
                                </div>
                            </div>
                            <div style="display: flex; gap: 10px; align-items: flex-start;">
                                <span class="badge badge-ai" style="min-width: 42px; justify-content: center;">C8</span>
                                <div>
                                    <div style="font-size: 12px; font-weight: 600; color: var(--primary);">Comentarios</div>
                                    <div style="font-size: 10px; color: var(--muted);">Notas adicionales</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Columna Lateral -->
    <div class="sidebar-panel">
        <div class="info-card" style="border-left: 3px solid var(--warning); background: var(--warning-bg);">
            <div class="info-card-title" style="color: var(--warning); margin-bottom: 8px;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                <span>Integridad de Datos</span>
            </div>
            <div class="info-card-body" style="font-size: 12px; color: var(--secondary); line-height: 1.5;">
                Los contratos activos que <strong>no figuren</strong> en la carga actual serán marcados como <span class="badge badge-danger" style="font-size: 9px; padding: 1px 4px;">BAJA</span> automáticamente.
            </div>
        </div>
    </div>
</div>
@endsection
