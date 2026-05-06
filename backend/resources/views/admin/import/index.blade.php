@extends('layouts.app')

@section('title', 'Importación CSV')

@section('content')
<div class="content-header" style="margin-bottom: 30px;">
    <h1 style="font-size: 1.8rem; font-weight: 600; margin-bottom: 8px;">Importación de Datos Semanal</h1>
    <p style="color: var(--text-muted);">Sube el archivo CSV exportado para actualizar la base de datos de contratos y clientes.</p>
</div>

<div style="max-width: 800px;">
    <div class="card">
        <div class="card-header">
            <h3 style="font-size: 1.1rem; font-weight: 500;">Cargar Archivo CSV</h3>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div style="margin-bottom: 20px; padding: 15px; border-radius: 8px; background: rgba(16, 185, 129, 0.1); border: 1px solid #10b981; color: #10b981;">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div style="margin-bottom: 20px; padding: 15px; border-radius: 8px; background: rgba(239, 68, 68, 0.1); border: 1px solid #ef4444; color: #ef4444;">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div style="margin-bottom: 20px; padding: 15px; border-radius: 8px; background: rgba(239, 68, 68, 0.1); border: 1px solid #ef4444; color: #ef4444;">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.import.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div style="margin-bottom: 24px;">
                    <label for="csv_file" style="display: block; margin-bottom: 10px; font-weight: 500; font-size: 0.9rem;">Seleccionar archivo .csv</label>
                    <input type="file" name="csv_file" id="csv_file" accept=".csv,.txt" required 
                           style="width: 100%; padding: 12px; border: 1px solid var(--border); border-radius: 8px; background: var(--bg-body); color: var(--text-main);">
                    <p style="margin-top: 10px; font-size: 0.85rem; color: var(--text-muted);">
                        <span style="color: var(--accent);">Nota:</span> El archivo debe usar el separador <strong>";" (punto y coma)</strong> y seguir el orden de columnas estándar.
                    </p>
                </div>

                <div style="display: flex; justify-content: flex-end;">
                    <button type="submit" class="btn btn-primary" style="padding: 10px 24px; font-weight: 500;">
                        Iniciar Importación
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card" style="margin-top: 30px; background: rgba(59, 130, 246, 0.05); border: 1px solid rgba(59, 130, 246, 0.2);">
        <div class="card-header">
            <h3 style="font-size: 1rem; font-weight: 500; color: #60a5fa;">Estructura del Archivo</h3>
        </div>
        <div class="card-body">
            <ul style="line-height: 1.8; color: var(--text-muted); font-size: 0.9rem; list-style-type: none; padding: 0;">
                <li>🔹 <strong>Columna 1:</strong> Contraheader (CONH...) - Identificador único.</li>
                <li>🔹 <strong>Columna 3:</strong> Nombre del Cliente - Se normaliza a Title Case.</li>
                <li>🔹 <strong>Columna 6:</strong> Fecha Fin - Formato DD/MM/AAAA.</li>
                <li>🔹 <strong>Columna 7:</strong> Estado del Contrato.</li>
                <li style="margin-top: 10px; padding-top: 10px; border-top: 1px dashed var(--border);">
                    ⚠️ Los contratos activos que no figuren en el archivo serán marcados como <strong>Baja</strong>.
                </li>
            </ul>
        </div>
    </div>
</div>
@endsection
