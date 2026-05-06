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
        <div class="card" style="margin-bottom: 24px;">
            <div class="card-header" style="justify-content: space-between;">
                <span class="card-title">Carga de Datos (CSV/TXT)</span>
                <span class="badge badge-ai">PROCESO AUTOMÁTICO</span>
            </div>
            
            <div style="padding: 24px;">
                @if(session('success'))
                    <div class="badge badge-success" style="width: 100%; padding: 12px; margin-bottom: 24px; justify-content: flex-start; text-transform: none; border-radius: 4px;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="margin-right: 8px;"><polyline points="20 6 9 17 4 12"/></svg>
                        {{ session('success') }}
                    </div>
                @endif

                <form action="{{ route('admin.import.store') }}" method="POST" enctype="multipart/form-data" id="import-form">
                    @csrf
                    <div class="dropzone" id="dropzone" onclick="document.getElementById('file-input').click()" style="height: 160px; border-style: dashed; border-width: 1px; margin-bottom: 20px;">
                        <div style="display: flex; flex-direction: column; align-items: center; gap: 12px;">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                            <div class="dropzone-text" style="font-size: 14px;">Arrastre archivo aquí o haga clic para seleccionar</div>
                            <div id="file-name-display" class="dropzone-subtext" style="font-size: 11px; opacity: 0.6;">Soporta .csv y .txt (UTF-8)</div>
                        </div>
                        <input type="file" name="csv_file" id="file-input" style="display: none;" onchange="updateFileName(this)">
                    </div>

                    <div style="display: flex; justify-content: flex-end;">
                        <button type="submit" class="btn-primary" style="padding: 10px 24px; font-weight: 600; letter-spacing: 0.5px;">
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
            <div style="padding: 24px;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px;">
                    <!-- Columna Izquierda -->
                    <div style="display: flex; flex-direction: column; gap: 12px;">
                        @foreach([
                            ['C1', 'Contraheader', 'Identificador único del contrato (CONH)'],
                            ['C2', 'Centro Coste', 'Referencia contable del cliente'],
                            ['C3', 'Cliente', 'Nombre fiscal de la empresa'],
                            ['C4', 'Vendor', 'Proveedor origen (Siemens/Moldex)']
                        ] as $item)
                        <div style="display: flex; align-items: center; border-bottom: 1px solid var(--border-subtle); padding-bottom: 8px;">
                            <code style="min-width: 40px; font-family: var(--font-mono); font-size: 10px; color: var(--accent);">{{ $item[0] }}</code>
                            <div style="flex: 1;">
                                <div style="font-size: 12px; font-weight: 500; color: var(--primary);">{{ $item[1] }}</div>
                                <div style="font-size: 10px; color: var(--muted);">{{ $item[2] }}</div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <!-- Columna Derecha -->
                    <div style="display: flex; flex-direction: column; gap: 12px;">
                        @foreach([
                            ['C5', 'Producto', 'Módulo o paquete de software'],
                            ['C6', 'Vencimiento', 'Fecha efectiva de caducidad (DD/MM/AAAA)'],
                            ['C7', 'Estado', 'Situación actual en origen'],
                            ['C8', 'Comentarios', 'Observaciones técnicas adicionales']
                        ] as $item)
                        <div style="display: flex; align-items: center; border-bottom: 1px solid var(--border-subtle); padding-bottom: 8px;">
                            <code style="min-width: 40px; font-family: var(--font-mono); font-size: 10px; color: var(--accent);">{{ $item[0] }}</code>
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
                <span style="font-size: 12px; font-weight: 700; text-transform: uppercase;">Aviso de Sincronización</span>
            </div>
            <div style="font-size: 12px; color: var(--secondary); line-height: 1.6;">
                La carga es **destructiva** para estados obsoletos. Contratos activos ausentes en este archivo serán marcados automáticamente como <span style="color: var(--danger); font-weight: 600;">BAJA</span>.
            </div>
        </div>

        <div style="margin-top: 16px; padding: 16px; border: 1px solid var(--border-subtle); border-radius: 4px; background: var(--card-bg);">
            <div style="font-size: 11px; color: var(--muted); line-height: 1.5;">
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
