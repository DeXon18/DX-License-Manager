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
        <div class="tool-icon-fallback" style="background: rgba(194,87,10,0.08); color: #c2570a; width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/></svg>
        </div>
        <div>
            <h1 class="page-title" style="margin: 0;">NX Suite <span class="vendor-label siemens" style="font-size: 10px; padding: 2px 6px; margin-left: 8px;">Siemens PLM</span></h1>
            <p class="page-sub" style="margin: 0;">Ecosistema de Digital Industries Software y Gestión PLM</p>
        </div>
    </div>
</div>

<div class="grid-main" x-data="{ motor: 'salt', fileName: '', isDragging: false }">
    <div style="display: flex; flex-direction: column; gap: 20px;">
        
        <!-- MOTOR SELECTOR -->
        <div class="motor-selector" style="display: flex; gap: 12px; background: var(--card-bg); padding: 6px; border-radius: 12px; border: 1px solid var(--border); width: fit-content;">
            <button type="button" 
                    @click="motor = 'legacy'" 
                    :class="motor === 'legacy' ? 'motor-btn active' : 'motor-btn'"
                    style="display: flex; align-items: center; gap: 8px; padding: 8px 16px; border-radius: 8px; border: none; cursor: pointer; font-family: inherit; font-size: 13px; transition: all 0.2s;">
                <div class="motor-dot" :style="motor === 'legacy' ? 'background: #c2570a' : 'background: var(--text-3)'" style="width: 8px; height: 8px; border-radius: 50%;"></div>
                Modo Legacy (ugslmd)
            </button>
            <button type="button" 
                    @click="motor = 'salt'" 
                    :class="motor === 'salt' ? 'motor-btn active' : 'motor-btn'"
                    style="display: flex; align-items: center; gap: 8px; padding: 8px 16px; border-radius: 8px; border: none; cursor: pointer; font-family: inherit; font-size: 13px; transition: all 0.2s;">
                <div class="motor-dot" :style="motor === 'salt' ? 'background: #c2570a' : 'background: var(--text-3)'" style="width: 8px; height: 8px; border-radius: 50%;"></div>
                Motor SALT (saltd)
            </button>
        </div>

        <form action="{{ route('tools.nx-suite.process') }}" method="POST" enctype="multipart/form-data" id="nx-form">
            @csrf
            <input type="hidden" name="motor" :value="motor">
            
            <div class="dropzone" 
                 :class="isDragging ? 'dragging' : ''"
                 @dragover.prevent="isDragging = true"
                 @dragleave.prevent="isDragging = false"
                 @drop.prevent="isDragging = false; $refs.fileInput.files = $event.dataTransfer.files; fileName = $refs.fileInput.files[0].name; $el.closest('form').submit()"
                 @click="$refs.fileInput.click()"
                 style="border: 2px dashed var(--border); border-radius: 16px; padding: 40px; text-align: center; cursor: pointer; transition: all 0.2s; background: var(--card-bg);">
                
                <input type="file" name="license_file" x-ref="fileInput" @change="fileName = $event.target.files[0].name; $el.closest('form').submit()" style="display: none;">
                
                <div style="font-size: 32px; margin-bottom: 12px;">📦</div>
                <div style="font-weight: 500; color: var(--text-1); margin-bottom: 4px;">
                    <template x-if="!fileName">
                        <span>Arrastra tu archivo .lic aquí o haz clic para seleccionar</span>
                    </template>
                    <template x-if="fileName">
                        <span x-text="fileName" style="color: #c2570a;"></span>
                    </template>
                </div>
                <div style="font-size: 12px; color: var(--text-3);">La transformación se iniciará automáticamente al cargar el archivo</div>
                
                <div style="display: flex; gap: 8px; justify-content: center; margin-top: 16px;">
                    <span style="font-size: 10px; padding: 2px 8px; border-radius: 4px; background: var(--bg); border: 1px solid var(--border); color: var(--text-2);">.lic</span>
                    <span style="font-size: 10px; padding: 2px 8px; border-radius: 4px; background: var(--bg); border: 1px solid var(--border); color: var(--text-2);">.txt</span>
                    <span style="font-size: 10px; padding: 2px 8px; border-radius: 4px; background: var(--bg); border: 1px solid var(--border); color: var(--text-2);">.dat</span>
                    <span style="font-size: 10px; padding: 2px 8px; border-radius: 4px; background: var(--bg); border: 1px solid var(--border); color: var(--text-2);">.cid</span>
                </div>
            </div>
        </form>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
            <div class="card" style="padding: 16px;">
                <div style="display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 500; color: var(--text-1); margin-bottom: 12px;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 7H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"/></svg>
                    Productos compatibles
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px;">
                    <div style="display: flex; align-items: center; gap: 6px; font-size: 12px; color: var(--text-2);"><span style="color: #10b981;">✓</span> Designcenter</div>
                    <div style="display: flex; align-items: center; gap: 6px; font-size: 12px; color: var(--text-2);"><span style="color: #10b981;">✓</span> Teamcenter</div>
                    <div style="display: flex; align-items: center; gap: 6px; font-size: 12px; color: var(--text-2);"><span style="color: #10b981;">✓</span> Simcenter 3D</div>
                    <div style="display: flex; align-items: center; gap: 6px; font-size: 12px; color: var(--text-2);"><span style="color: #10b981;">✓</span> Amesim</div>
                </div>
            </div>

            <div class="card" style="padding: 16px;">
                <div style="display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 500; color: var(--text-1); margin-bottom: 12px;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                    Funciones automáticas
                </div>
                <ul style="margin: 0; padding: 0; list-style: none; display: flex; flex-direction: column; gap: 6px;">
                    <li style="font-size: 12px; color: var(--text-2); display: flex; align-items: flex-start; gap: 6px;"><span style="color: var(--text-3);">›</span> Reemplazo de HOSTNAME por localhost (Temporales)</li>
                    <li style="font-size: 12px; color: var(--text-2); display: flex; align-items: flex-start; gap: 6px;"><span style="color: var(--text-3);">›</span> Normalización del daemon según motor</li>
                    <li style="font-size: 12px; color: var(--text-2); display: flex; align-items: flex-start; gap: 6px;"><span style="color: var(--text-3);">›</span> Clasificación y guardado en repositorio</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="sidebar-panel" style="display: flex; flex-direction: column; gap: 16px;">
        <div class="card" style="padding: 16px;">
            <div style="display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 500; color: var(--text-1); margin-bottom: 8px;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                Motor Activo
            </div>
            <div style="font-family: 'IBM Plex Mono', monospace; font-size: 11px; padding: 8px 10px; background: var(--bg); border: 1px solid var(--border); border-radius: 6px; color: #c2570a; margin-bottom: 8px;" x-text="motor === 'salt' ? 'saltd — Motor SALT (Estándar)' : 'ugslmd — Modo Legacy'">
            </div>
            <p style="font-size: 11px; color: var(--text-3); margin: 0;">Selecciona el motor según la versión del servidor de licencias del cliente.</p>
        </div>

        <div class="card" style="padding: 16px;">
            <div style="display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 500; color: var(--text-1); margin-bottom: 8px;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                Mecanismo de Almacenamiento
            </div>
            <p style="font-size: 12px; color: var(--text-2); margin-bottom: 8px;">
                Las licencias <strong>Contractuales, Unificadas y Dongles</strong> se almacenan automáticamente en el repositorio del cliente.
            </p>
            <div style="background: rgba(194,87,10,0.05); padding: 8px; border-radius: 6px; border-left: 3px solid #c2570a; font-size: 11px; color: var(--text-2);">
                Las licencias <strong>Temporales</strong> solo se transforman para descarga inmediata; no se guardan en el servidor.
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .grid-main {
        display: grid;
        grid-template-columns: 1fr 280px;
        gap: 24px;
        margin-top: 24px;
    }
    .motor-btn {
        background: transparent;
        color: var(--text-2);
    }
    .motor-btn:hover {
        background: rgba(194,87,10,0.05);
    }
    .motor-btn.active {
        background: #c2570a;
        color: white;
        box-shadow: 0 4px 12px rgba(194,87,10,0.2);
    }
    .motor-btn.active .motor-dot {
        background: white !important;
    }
    .dropzone.dragging {
        border-color: #c2570a !important;
        background: rgba(194,87,10,0.02) !important;
        transform: scale(1.01);
    }
    .card {
        background: var(--card-bg);
        border: 1px solid var(--border);
        border-radius: 12px;
    }
</style>
@endpush
