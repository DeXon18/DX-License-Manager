@extends('layouts.app')

@section('title', 'Repositorio de Licencias')

@section('content')
<div class="content-header">
    <div class="header-left">
        <div class="header-breadcrumb">Administración / Licencias</div>
        <h1 class="header-title">Repositorio Semanal</h1>
    </div>
    <div class="header-actions">
        <form action="{{ route('admin.repository.generate') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-primary">
                <i class="fa-solid fa-sync-alt mr-2"></i> Generar Manualmente
            </button>
        </form>
    </div>
</div>

<div class="dx-card">
    <div class="card-header">
        <h2 class="card-title">Historial de Repositorios</h2>
    </div>
    
    <div class="card-body">
        <p class="mb-4 text-muted">
            Los archivos se generan automáticamente todos los <strong>lunes a las 07:00 AM</strong>. 
            Cada archivo contiene las licencias procesadas durante la semana anterior.
        </p>

        @if(session('success'))
            <div class="alert alert-success mb-4">
                <i class="fa-solid fa-check-circle mr-2"></i> {{ session('success') }}
            </div>
        @endif

        @if(session('info'))
            <div class="alert alert-info mb-4">
                <i class="fa-solid fa-info-circle mr-2"></i> {{ session('info') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger mb-4">
                <i class="fa-solid fa-exclamation-triangle mr-2"></i> {{ session('error') }}
            </div>
        @endif

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Periodo</th>
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
                                <span class="badge badge-info">Semana {{ $archive->week_number }}</span>
                                <span class="text-muted ml-1">{{ $archive->year }}</span>
                            </td>
                            <td>
                                <i class="fa-solid fa-file-archive text-warning mr-2"></i>
                                <strong>{{ $archive->filename }}</strong>
                            </td>
                            <td>{{ $archive->files_count }}</td>
                            <td>
                                @php $clientsCount = count($archive->clients_summary ?? []); @endphp
                                <span class="text-muted" title="{{ implode(', ', array_keys($archive->clients_summary ?? [])) }}">
                                    {{ $clientsCount }} clientes
                                </span>
                            </td>
                            <td>{{ $archive->created_at->format('d/m/Y H:i') }}</td>
                            <td class="text-right">
                                <a href="{{ route('admin.repository.download', $archive) }}" class="btn btn-sm btn-secondary">
                                    <i class="fa-solid fa-download"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-8">
                                <div class="empty-state">
                                    <div class="empty-icon">📂</div>
                                    <div class="empty-title">No hay archivos en el repositorio</div>
                                    <div class="empty-desc">Los archivos aparecerán aquí una vez que se genere el primer reporte semanal.</div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
