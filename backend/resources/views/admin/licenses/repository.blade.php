@extends('layouts.app')

@section('title', 'Repositorio de Licencias')

@section('content')
<div class="page-header">
    <div class="page-header-info">
        <div class="breadcrumb">Administración / Licencias</div>
        <h1 class="page-title">Repositorio Semanal</h1>
        <p class="page-sub">Historial de archivos comprimidos y reporte de licencias procesadas.</p>
    </div>
</div>

<div class="grid-main">
    <div class="main-panel">
        <div class="card" style="margin-bottom: 24px;">
            <div class="card-header" style="justify-content: space-between;">
                <span class="card-title">Historial de Repositorios</span>
                <div style="display: flex; gap: 8px;">
                    <form action="{{ route('admin.repository.generate') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn-secondary" style="padding: 6px 14px; font-size: 11px;">
                            <i class="fa-solid fa-sync-alt mr-2"></i> SOLO GENERAR
                        </button>
                    </form>
                    <form action="{{ route('admin.repository.generate') }}" method="POST">
                        @csrf
                        <input type="hidden" name="send" value="1">
                        <button type="submit" class="btn-primary" style="padding: 6px 14px; font-size: 11px;">
                            <i class="fa-solid fa-paper-plane mr-2"></i> GENERAR Y ENVIAR
                        </button>
                    </form>
                </div>
            </div>
            
            <div style="padding: 24px;">
                @if(session('success'))
                    <div class="badge badge-success" style="width: 100%; padding: 12px; margin-bottom: 20px; justify-content: flex-start; text-transform: none; border-radius: 4px;">
                        <i class="fa-solid fa-check-circle mr-2"></i> {{ session('success') }}
                    </div>
                @endif

                @if(session('info'))
                    <div class="badge badge-info" style="width: 100%; padding: 12px; margin-bottom: 20px; justify-content: flex-start; text-transform: none; border-radius: 4px;">
                        <i class="fa-solid fa-info-circle mr-2"></i> {{ session('info') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="badge badge-danger" style="width: 100%; padding: 12px; margin-bottom: 20px; justify-content: flex-start; text-transform: none; border-radius: 4px;">
                        <i class="fa-solid fa-exclamation-triangle mr-2"></i> {{ session('error') }}
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Periodo</th>
                                <th>Origen</th>
                                <th>Archivo</th>
                                <th>Licencias</th>
                                <th>Clientes</th>
                                <th>Generado</th>
                                <th class="text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($archives as $archive)
                                <tr>
                                    <td>
                                        <span class="badge badge-primary">S{{ $archive->week_number }}</span>
                                        <span class="font-mono muted" style="font-size: 11px; margin-left: 4px;">{{ $archive->year }}</span>
                                    </td>
                                    <td>
                                        @if($archive->origin === 'manual')
                                            <span class="badge badge-accent" title="Generado manualmente por usuario">
                                                <i class="fa-solid fa-user"></i>
                                            </span>
                                        @else
                                            <span class="badge badge-muted" title="Generado automáticamente por sistema">
                                                <i class="fa-solid fa-robot"></i>
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div style="display: flex; align-items: center; gap: 8px;">
                                            <i class="fa-solid fa-file-archive" style="color: var(--warning);"></i>
                                            <span class="font-mono" style="font-weight: 600;">{{ $archive->filename }}</span>
                                        </div>
                                    </td>
                                    <td class="font-mono">{{ $archive->files_count }}</td>
                                    <td>
                                        @php $clientsCount = count($archive->clients_summary ?? []); @endphp
                                        <span class="muted" style="font-size: 12px;" title="{{ implode(', ', array_keys($archive->clients_summary ?? [])) }}">
                                            {{ $clientsCount }} clientes
                                        </span>
                                    </td>
                                    <td class="font-mono" style="font-size: 12px;">{{ $archive->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="text-right">
                                        <div style="display: flex; justify-content: flex-end; gap: 8px;">
                                            <a href="{{ route('admin.repository.download', $archive) }}" class="btn-secondary" style="padding: 6px 10px;" title="Descargar ZIP">
                                                <i class="fa-solid fa-download"></i>
                                            </a>
                                            <form action="{{ route('admin.repository.destroy', $archive) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este archivo del historial?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-secondary" style="padding: 6px 10px; color: var(--danger) !important;" title="Eliminar del historial">
                                                    <i class="fa-solid fa-trash-can"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <th colspan="7" class="text-center" style="padding: 60px 0;">
                                        <div style="opacity: 0.5;">
                                            <div style="font-size: 32px; margin-bottom: 12px;">📂</div>
                                            <div style="font-weight: 600; font-size: 14px;">No hay archivos en el repositorio</div>
                                            <div style="font-size: 12px;">Los archivos aparecerán aquí tras el proceso del lunes.</div>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Panel Lateral Informativo -->
    <div class="sidebar-panel">
        <div class="card" style="padding: 20px;">
            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px; color: var(--accent);">
                <i class="fa-solid fa-circle-info"></i>
                <span style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">Información ATS</span>
            </div>
            <div style="font-size: 12px; color: var(--secondary); line-height: 1.6;">
                Cada lunes a las 07:00, el sistema empaqueta las licencias de la semana anterior y envía el reporte a soporte.
            </div>
            <div style="margin-top: 16px; padding-top: 16px; border-top: 1px solid var(--border-subtle);">
                <div style="font-size: 11px; color: var(--muted);">
                    <strong>Destinatario:</strong><br>
                    Soporte@ats-global.com
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
