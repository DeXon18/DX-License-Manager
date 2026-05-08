@extends('layouts.app')

@section('content')
<div class="page-header">
    <div class="breadcrumb">
        <a href="{{ route('admin.import.index') }}">Importación</a>
        <span class="muted">/</span>
        <span class="current">Historial de Logs</span>
    </div>
    <h1 class="page-title">Historial de Importaciones</h1>
    <p class="page-sub">Seguimiento de integridad de datos y avisos de normalización.</p>
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
                        <span class="badge badge-warn">Parcial</span>
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
                        <span class="text-warn font-bold" style="color: #f59e0b;">{{ count($log->warnings) }}</span>
                    @else
                        <span class="muted">0</span>
                    @endif
                </td>
                <td class="text-right">
                    <div style="display: flex; justify-content: flex-end; gap: 8px;">
                        <a href="{{ route('admin.import.logs.show', $log) }}" class="btn-icon">
                            <i class="fa-solid fa-eye"></i>
                        </a>
                        <form action="{{ route('admin.import.logs.destroy', $log) }}" method="POST" onsubmit="return confirm('¿Eliminar este log?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-icon text-danger">
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

@push('styles')
<style>
    .badge-warn { background: rgba(245, 158, 11, 0.1); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.2); }
    .btn-icon {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid var(--border);
        color: var(--muted);
        width: 32px;
        height: 32px;
        display: flex; align-items: center; justify-content: center;
        border-radius: 4px; cursor: pointer; transition: all 0.2s;
    }
    .btn-icon:hover { background: var(--border); color: var(--text); }
    .btn-icon.text-danger:hover {
        background: rgba(239, 68, 68, 0.1); color: #ef4444; border-color: rgba(239, 68, 68, 0.2);
    }
</style>
@endpush
