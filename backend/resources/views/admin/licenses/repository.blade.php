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
        <div class="card dx-v2-lic-repo-card-mb">
            <div class="card-header dx-v2-lic-repo-header-row">
                <span class="card-title">Historial de Repositorios</span>
                <div class="dx-v2-lic-repo-btn-group">
                    <form action="{{ route('admin.repository.generate') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn-secondary dx-v2-lic-repo-btn-sm">
                            <i class="fa-solid fa-sync-alt mr-2"></i> SOLO GENERAR
                        </button>
                    </form>
                    <form action="{{ route('admin.repository.generate') }}" method="POST">
                        @csrf
                        <input type="hidden" name="send" value="1">
                        <button type="submit" class="btn-primary dx-v2-lic-repo-btn-sm">
                            <i class="fa-solid fa-paper-plane mr-2"></i> GENERAR Y ENVIAR
                        </button>
                    </form>
                </div>
            </div>
            
            <div class="dx-v2-lic-repo-body">


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
                                        <span class="dx-v2-lic-repo-year-label">{{ $archive->year }}</span>
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
                                        <div class="dx-v2-lic-repo-file-row">
                                            <i class="fa-solid fa-file-archive dx-v2-lic-repo-file-icon"></i>
                                            <span class="dx-v2-lic-repo-file-name">{{ $archive->filename }}</span>
                                        </div>
                                    </td>
                                    <td class="font-mono">{{ $archive->files_count }}</td>
                                    <td>
                                        @php $clientsCount = count($archive->clients_summary ?? []); @endphp
                                        <span class="dx-v2-lic-repo-summary-text" title="{{ implode(', ', array_keys($archive->clients_summary ?? [])) }}">
                                            {{ $clientsCount }} clientes
                                        </span>
                                    </td>
                                    <td class="dx-v2-lic-repo-date">{{ $archive->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="text-right">
                                        <div class="dx-v2-lic-repo-actions-row">
                                            <a href="{{ route('admin.repository.download', $archive) }}" class="btn-secondary dx-v2-lic-repo-btn-action" title="Descargar ZIP">
                                                <i class="fa-solid fa-download"></i>
                                            </a>
                                            <form action="{{ route('admin.repository.destroy', $archive) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este archivo del historial?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-secondary dx-v2-lic-repo-btn-action danger" title="Eliminar del historial">
                                                    <i class="fa-solid fa-trash-can"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <th colspan="7" class="text-center dx-v2-lic-repo-empty-container">
                                        <div class="dx-v2-lic-repo-empty-inner">
                                            <div class="dx-v2-lic-repo-empty-icon">📂</div>
                                            <div class="dx-v2-lic-repo-empty-title">No hay archivos en el repositorio</div>
                                            <div class="dx-v2-lic-repo-empty-desc">Los archivos aparecerán aquí tras el proceso del lunes.</div>
                                        </div>
                                    </th>
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
        <div class="card dx-v2-lic-repo-sidebar-card">
            <div class="dx-v2-lic-repo-sidebar-header">
                <i class="fa-solid fa-circle-info"></i>
                <span class="dx-v2-lic-repo-sidebar-title">Información ATS</span>
            </div>
            <div class="dx-v2-lic-repo-sidebar-text">
                Cada lunes a las 07:00, el sistema empaqueta las licencias de la semana anterior y envía el reporte a soporte.
            </div>
            <div class="dx-v2-lic-repo-sidebar-footer">
                <strong>Destinatario:</strong><br>
                Soporte@ats-global.com
            </div>
        </div>
    </div>
</div>
@endsection
