@extends('layouts.app')

@section('title', 'Importación de Datos')

@section('content')
<div class="content-header" style="margin-bottom: 40px; animation: fadeInDown 0.5s ease-out;">
    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 8px;">
        <div style="width: 32px; height: 32px; background: var(--accent-gradient); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.2rem;">
            📂
        </div>
        <h1 style="font-size: 2rem; font-weight: 700; letter-spacing: -0.02em; margin: 0;">Importación Semanal</h1>
    </div>
    <p style="color: var(--text-muted); font-size: 1.05rem; max-width: 600px;">
        Actualiza el catálogo de contratos y clientes mediante la ingesta masiva de datos estructurados.
    </p>
</div>

<div style="display: grid; grid-template-columns: 1.5fr 1fr; gap: 30px; animation: fadeIn 0.8s ease-out;">
    <!-- Columna Principal: Carga -->
    <div class="card" style="background: var(--card-bg); border: 1px solid var(--border); overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
        <div style="padding: 24px; border-bottom: 1px solid var(--border); background: rgba(255,255,255,0.02);">
            <h3 style="font-size: 1.1rem; font-weight: 600; margin: 0;">Cargar Nuevo Repositorio</h3>
        </div>
        
        <div style="padding: 32px;">
            @if(session('success'))
                <div style="margin-bottom: 24px; padding: 16px; border-radius: 12px; background: rgba(16, 185, 129, 0.1); border: 1px solid #10b981; color: #10b981; display: flex; align-items: center; gap: 12px;">
                    <span style="font-size: 1.2rem;">✅</span>
                    <span style="font-weight: 500;">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div style="margin-bottom: 24px; padding: 16px; border-radius: 12px; background: rgba(239, 68, 68, 0.1); border: 1px solid #ef4444; color: #ef4444; display: flex; align-items: center; gap: 12px;">
                    <span style="font-size: 1.2rem;">⚠️</span>
                    <span style="font-weight: 500;">{{ session('error') }}</span>
                </div>
            @endif

            <form action="{{ route('admin.import.store') }}" method="POST" enctype="multipart/form-data" x-data="{ dragging: false }">
                @csrf
                <div 
                    @dragover.prevent="dragging = true" 
                    @dragleave.prevent="dragging = false" 
                    @drop.prevent="dragging = false; $refs.fileInput.files = $event.dataTransfer.files"
                    :style="dragging ? 'border-color: var(--accent); background: rgba(59, 130, 246, 0.05);' : ''"
                    style="position: relative; border: 2px dashed var(--border); border-radius: 16px; padding: 48px; text-align: center; transition: all 0.3s ease; cursor: pointer; background: rgba(0,0,0,0.1);"
                    @click="$refs.fileInput.click()"
                >
                    <input type="file" name="csv_file" x-ref="fileInput" accept=".csv,.txt" required style="display: none;">
                    
                    <div style="font-size: 3rem; margin-bottom: 16px;">📄</div>
                    <h4 style="font-size: 1.2rem; font-weight: 600; margin-bottom: 8px;">Arrastra tu CSV aquí</h4>
                    <p style="color: var(--text-muted); font-size: 0.95rem; margin-bottom: 0;">O haz clic para seleccionar un archivo de tu equipo</p>
                    
                    <div style="margin-top: 20px; display: inline-block; padding: 6px 12px; border-radius: 20px; background: var(--border); font-size: 0.8rem; font-weight: 600; color: var(--text-muted);">
                        Soporta .CSV y .TXT (Sep: ;)
                    </div>
                </div>

                <div style="margin-top: 32px; display: flex; justify-content: space-between; align-items: center;">
                    <div style="display: flex; align-items: center; gap: 8px; color: var(--text-muted); font-size: 0.85rem;">
                        <span style="color: #60a5fa;">ℹ️</span>
                        Se detectará automáticamente la codificación UTF-8.
                    </div>
                    <button type="submit" class="btn btn-primary" style="padding: 12px 32px; font-weight: 600; border-radius: 10px; box-shadow: var(--accent-shadow);">
                        Iniciar Procesamiento
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Columna Lateral: Info -->
    <div style="display: flex; flex-direction: column; gap: 20px;">
        <div class="card" style="background: rgba(59, 130, 246, 0.03); border: 1px solid rgba(59, 130, 246, 0.15);">
            <div style="padding: 20px; border-bottom: 1px dashed rgba(59, 130, 246, 0.2);">
                <h3 style="font-size: 1rem; font-weight: 600; color: #60a5fa; margin: 0; display: flex; align-items: center; gap: 8px;">
                    📋 Guía de Estructura
                </h3>
            </div>
            <div style="padding: 20px;">
                <div style="display: flex; flex-direction: column; gap: 16px;">
                    <div style="display: flex; gap: 12px;">
                        <div style="min-width: 24px; height: 24px; border-radius: 6px; background: rgba(59, 130, 246, 0.1); color: #60a5fa; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: 700;">1</div>
                        <div>
                            <div style="font-weight: 600; font-size: 0.9rem; margin-bottom: 2px;">Contraheader</div>
                            <div style="font-size: 0.85rem; color: var(--text-muted);">ID único del contrato (Ej: CONH...)</div>
                        </div>
                    </div>
                    <div style="display: flex; gap: 12px;">
                        <div style="min-width: 24px; height: 24px; border-radius: 6px; background: rgba(59, 130, 246, 0.1); color: #60a5fa; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: 700;">3</div>
                        <div>
                            <div style="font-weight: 600; font-size: 0.9rem; margin-bottom: 2px;">Cliente</div>
                            <div style="font-size: 0.85rem; color: var(--text-muted);">Nombre fiscal (Title Case automático)</div>
                        </div>
                    </div>
                    <div style="display: flex; gap: 12px;">
                        <div style="min-width: 24px; height: 24px; border-radius: 6px; background: rgba(59, 130, 246, 0.1); color: #60a5fa; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: 700;">6</div>
                        <div>
                            <div style="font-weight: 600; font-size: 0.9rem; margin-bottom: 2px;">Fecha Fin</div>
                            <div style="font-size: 0.85rem; color: var(--text-muted);">Formato día/mes/año</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card" style="background: rgba(245, 158, 11, 0.03); border: 1px solid rgba(245, 158, 11, 0.15);">
            <div style="padding: 16px; display: flex; gap: 12px; align-items: flex-start;">
                <span style="font-size: 1.2rem;">💡</span>
                <div>
                    <h4 style="font-size: 0.9rem; font-weight: 600; color: #fbbf24; margin-bottom: 4px;">Atención a las Bajas</h4>
                    <p style="font-size: 0.85rem; color: var(--text-muted); margin: 0; line-height: 1.4;">
                        Los contratos marcados como activos que no figuren en este archivo serán movidos al estado <strong>Baja</strong> de forma irreversible.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    @keyframes fadeInDown {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endsection
