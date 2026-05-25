@extends('layouts.app')

@section('content')
<div class="dx-v2-page-header">
    <div>
        <div class="breadcrumb">
            <a href="{{ route('admin.import.index') }}">Importación</a>
            <span class="separator">/</span>
            <span class="current">Logs</span>
        </div>
        <h1 class="page-title">Historial de <span>Sincronización</span></h1>
        <p class="page-subtitle">Seguimiento de integridad de datos y avisos de normalización.</p>
    </div>
</div>

<div class="card">
    <table class="table text-sm">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Archivo</th>
                <th>Estado</th>
                <th class="text-center">Total Filas</th>
                <th class="text-center">Procesadas</th>
                <th class="text-center">Errores</th>
                <th class="text-center">Avisos</th>
                <th class="text-right">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($logs as $log)
            <tr>
                <td class="font-mono text-xs">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                <td class="font-bold">{{ $log->filename }}</td>
                <td>
                    @if($log->status === 'success')
                        <span class="badge badge-success">Éxito</span>
                    @elseif($log->status === 'partial')
                        <span class="badge dx-v2-import-badge-warn">Parcial</span>
                    @elseif($log->status === 'processing')
                        <span class="badge badge-info">Procesando</span>
                    @else
                        <span class="badge badge-danger">Fallido</span>
                    @endif
                </td>
                <td class="text-center font-bold">{{ $log->total_rows ?? 0 }}</td>
                <td class="text-center font-bold text-accent">{{ $log->processed_rows ?? 0 }}</td>
                <td class="text-center">
                    @if(count($log->errors ?? []) > 0)
                        <span class="text-danger font-bold">{{ count($log->errors) }}</span>
                    @else
                        <span class="muted">0</span>
                    @endif
                </td>
                <td class="text-center">
                    @if(count($log->warnings ?? []) > 0)
                        <span class="text-warn font-bold">{{ count($log->warnings) }}</span>
                    @else
                        <span class="muted">0</span>
                    @endif
                </td>
                <td class="text-right">
                    <div class="dx-v2-import-btn-icon-row">
                        <a href="{{ route('admin.import.logs.show', $log) }}" class="dx-v2-import-btn-icon">
                            <i class="fa-solid fa-eye"></i>
                        </a>
                        <form action="{{ route('admin.import.logs.destroy', $log) }}" method="POST" onsubmit="return confirm('¿Eliminar este log?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="dx-v2-import-btn-icon text-danger">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center py-12 muted">No hay registros de importación todavía.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    
    @if($logs->hasPages())
    <div class="px-5 py-4 border-t border-white/5">
        {{ $logs->links() }}
    </div>
    @endif
</div>
@endsection


