@extends('layouts.app')

@section('title', 'Importación de Datos')

@section('content')
<div style="max-width: 1100px; margin: 0 auto; padding: var(--spacing-8) 0;">
    <!-- HEADER — h1 (1.602rem) -->
    <div style="margin-bottom: var(--spacing-10);">
        <h1 style="font-family: 'Inter', sans-serif; font-size: 1.602rem; font-weight: 700; color: var(--text-main); letter-spacing: -0.02em; margin-bottom: var(--spacing-2);">
            Importación de Datos Semanal
        </h1>
        <p style="font-family: 'Inter', sans-serif; font-size: 0.889rem; color: var(--text-muted); line-height: 1.65;">
            Actualización del repositorio de contratos y clientes. Proceso de sincronización de estado y vigencia.
        </p>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 320px; gap: var(--spacing-8); align-items: start;">
        
        <!-- PANEL DE CARGA — Surface (#161B22) -->
        <div style="background: #161B22; border: 1px solid #30363D; border-radius: 10px; overflow: hidden;">
            <div style="padding: var(--spacing-5) var(--spacing-6); border-bottom: 1px solid #30363D; background: #21262D;">
                <span style="font-family: 'Inter', sans-serif; font-size: 0.694rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.06em; color: var(--text-muted);">
                    Cargar Repositorio (CSV/TXT)
                </span>
            </div>
            
            <div style="padding: var(--spacing-8);">
                @if(session('success'))
                    <div style="margin-bottom: var(--spacing-6); padding: 10px 14px; background: #0D2818; border: 1px solid #1A5C2A; border-radius: 6px; color: #3FB950; font-family: 'Inter', sans-serif; font-size: 0.889rem; display: flex; align-items: center; gap: 10px;">
                        <span>✓</span> {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div style="margin-bottom: var(--spacing-6); padding: 10px 14px; background: #2D0F0F; border: 1px solid #5C1A1A; border-radius: 6px; color: #E05252; font-family: 'Inter', sans-serif; font-size: 0.889rem; display: flex; align-items: center; gap: 10px;">
                        <span>✕</span> {{ session('error') }}
                    </div>
                @endif

                <form action="{{ route('admin.import.store') }}" method="POST" enctype="multipart/form-data" x-data="{ dragging: false }">
                    @csrf
                    <div 
                        @dragover.prevent="dragging = true" 
                        @dragleave.prevent="dragging = false" 
                        @drop.prevent="dragging = false; $refs.fileInput.files = $event.dataTransfer.files"
                        :style="dragging ? 'border-color: #388BFD; background: #0D1B2E;' : 'border-color: #30363D; background: #0D1117;'"
                        style="border: 1px dashed #30363D; border-radius: 6px; padding: var(--spacing-12); text-align: center; cursor: pointer; transition: background 0.2s ease;"
                        @click="$refs.fileInput.click()"
                    >
                        <input type="file" name="csv_file" x-ref="fileInput" accept=".csv,.txt" required style="display: none;">
                        
                        <div style="font-family: 'Inter', sans-serif; font-size: 0.889rem; color: var(--text-muted); margin-bottom: var(--spacing-4);">
                            Arrastra el archivo o haz clic para seleccionar
                        </div>
                        
                        <div style="font-family: 'IBM Plex Mono', monospace; font-size: 0.8125rem; color: #388BFD; padding: 4px 10px; background: rgba(56, 139, 253, 0.1); border-radius: 4px; display: inline-block;">
                            formato_esperado.csv
                        </div>
                    </div>

                    <div style="margin-top: var(--spacing-8); display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-family: 'IBM Plex Mono', monospace; font-size: 0.79rem; color: var(--text-muted);">
                            SEP: [ ; ] | ENC: UTF-8
                        </span>
                        <button type="submit" class="btn btn-primary" style="background: #388BFD; color: #FFFFFF; border: none; padding: 8px 14px; border-radius: 6px; font-family: 'Inter', sans-serif; font-size: 0.694rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.06em; cursor: pointer;">
                            Iniciar Carga
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- PANEL DE ESPECIFICACIÓN — Sidebar de Referencia -->
        <div style="display: flex; flex-direction: column; gap: var(--spacing-6);">
            <div style="background: #161B22; border: 1px solid #30363D; border-radius: 10px; padding: var(--spacing-6);">
                <h2 style="font-family: 'Inter', sans-serif; font-size: 0.694rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.06em; color: var(--text-muted); margin-bottom: var(--spacing-5);">
                    Estructura Técnica
                </h2>
                <div style="display: flex; flex-direction: column; gap: var(--spacing-4);">
                    <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #21262D; padding-bottom: 8px;">
                        <span style="font-family: 'Inter', sans-serif; font-size: 0.79rem; color: var(--text-main);">Col 1</span>
                        <span style="font-family: 'IBM Plex Mono', monospace; font-size: 0.8125rem; color: #388BFD;">Contraheader</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #21262D; padding-bottom: 8px;">
                        <span style="font-family: 'Inter', sans-serif; font-size: 0.79rem; color: var(--text-main);">Col 3</span>
                        <span style="font-family: 'IBM Plex Mono', monospace; font-size: 0.8125rem; color: #388BFD;">Client_Name</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #21262D; padding-bottom: 8px;">
                        <span style="font-family: 'Inter', sans-serif; font-size: 0.79rem; color: var(--text-main);">Col 6</span>
                        <span style="font-family: 'IBM Plex Mono', monospace; font-size: 0.8125rem; color: #388BFD;">End_Date</span>
                    </div>
                </div>
            </div>

            <!-- ADVERTENCIA — Semantic Danger -->
            <div style="background: #2D0F00; border: 1px solid #5A3E00; border-radius: 10px; padding: var(--spacing-5);">
                <h4 style="font-family: 'Inter', sans-serif; font-size: 0.79rem; font-weight: 600; color: #D29922; margin-bottom: 4px; display: flex; align-items: center; gap: 8px;">
                    <span>▲</span> Integridad de Datos
                </h4>
                <p style="font-family: 'Inter', sans-serif; font-size: 0.79rem; color: #D29922; opacity: 0.8; line-height: 1.5; margin: 0;">
                    Cualquier contrato activo ausente en la carga será movido a <span style="font-family: 'IBM Plex Mono', monospace; font-weight: 700;">BAJA</span> automáticamente.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
