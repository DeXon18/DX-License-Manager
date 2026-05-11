@extends('layouts.app')

@section('title', 'Auditoría y Logs')

@section('header')
    <div class="page-header">
        <nav class="breadcrumb" aria-label="Breadcrumb">
            <a href="{{ route('dashboard') }}">Portal</a>
            <span>/</span>
            <a href="{{ route('admin.system.index') }}">Admin</a>
            <span>/</span>
            <span class="font-bold">Auditoría y Logs</span>
        </nav>
        <div style="display: flex; justify-content: space-between; align-items: flex-end; width: 100%;">
            <div>
                <h1 class="page-title">Centro de Observabilidad</h1>
                <p class="page-sub">Trazabilidad completa de acciones, errores y auditorías de IA.</p>
            </div>
            <div style="display: flex; gap: 12px;">
                <div class="stat-mini">
                    <span class="label">Eventos (24h)</span>
                    <span class="value">{{ $stats['total_24h'] }}</span>
                </div>
                <div class="stat-mini">
                    <span class="label">Alertas/Errores</span>
                    <span class="value" style="color: var(--danger);">{{ $stats['errors_24h'] }}</span>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
<div class="dashboard-container">
    {{-- Filtros --}}
    <div class="card" style="margin-bottom: 24px;">
        <div style="padding: 20px;">
            <form action="{{ route('admin.audit.index') }}" method="GET" style="display: flex; gap: 15px; align-items: flex-end;">
                <div style="flex: 1;">
                    <label style="display: block; font-size: 10px; font-weight: 700; color: var(--muted); text-transform: uppercase; margin-bottom: 6px;">Búsqueda rápida</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Usuario, descripción..." class="form-control" style="width: 100%;">
                </div>
                <div style="width: 180px;">
                    <label style="display: block; font-size: 10px; font-weight: 700; color: var(--muted); text-transform: uppercase; margin-bottom: 6px;">Nivel</label>
                    <select name="level" class="form-control" style="width: 100%;">
                        <option value="">Todos los niveles</option>
                        <option value="info" {{ request('level') == 'info' ? 'selected' : '' }}>Info</option>
                        <option value="warning" {{ request('level') == 'warning' ? 'selected' : '' }}>Warning</option>
                        <option value="error" {{ request('level') == 'error' ? 'selected' : '' }}>Error</option>
                    </select>
                </div>
                <div style="width: 180px;">
                    <label style="display: block; font-size: 10px; font-weight: 700; color: var(--muted); text-transform: uppercase; margin-bottom: 6px;">Tipo de Acción</label>
                    <input type="text" name="action" value="{{ request('action') }}" placeholder="Ej: db_backup" class="form-control" style="width: 100%;">
                </div>
                <button type="submit" class="btn btn-primary">Filtrar</button>
                <a href="{{ route('admin.audit.index') }}" class="btn" style="border: 1px solid var(--border);">Limpiar</a>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
            <span class="card-title">Timeline de Actividad</span>
            <div style="display: flex; gap: 8px;">
                <span class="badge" style="background: rgba(67, 97, 238, 0.1); color: var(--accent);">{{ $stats['critical_actions'] }} Acciones Críticas</span>
            </div>
        </div>
        <div style="padding: 0; overflow: hidden;">
            <table style="width: 100%; border-collapse: collapse; font-size: 12px;">
                <thead style="background: rgba(255,255,255,0.02); border-bottom: 1px solid var(--border);">
                    <tr>
                        <th style="padding: 12px 20px; text-align: left; color: var(--muted); font-weight: 700; text-transform: uppercase; width: 150px;">Timestamp</th>
                        <th style="padding: 12px 20px; text-align: left; color: var(--muted); font-weight: 700; text-transform: uppercase; width: 130px;">Usuario</th>
                        <th style="padding: 12px 20px; text-align: left; color: var(--muted); font-weight: 700; text-transform: uppercase; width: 150px;">Acción / Nivel</th>
                        <th style="padding: 12px 20px; text-align: left; color: var(--muted); font-weight: 700; text-transform: uppercase;">Descripción</th>
                        <th style="padding: 12px 20px; text-align: left; color: var(--muted); font-weight: 700; text-transform: uppercase; width: 120px;">IP Address</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr style="border-bottom: 1px solid var(--border-subtle); transition: background 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.01)'" onmouseout="this.style.background='transparent'">
                            <td style="padding: 14px 20px; font-family: 'IBM Plex Mono'; font-size: 11px; color: var(--muted);">
                                {{ \Carbon\Carbon::parse($log->created_at)->format('d/m/Y H:i:s') }}
                            </td>
                            <td style="padding: 14px 20px;">
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <div style="width: 24px; height: 24px; border-radius: 50%; background: var(--border-subtle); display: flex; align-items: center; justify-content: center; font-size: 10px; font-weight: 800; color: var(--primary);">
                                        {{ substr($log->user_name ?? 'S', 0, 1) }}
                                    </div>
                                    <span style="font-weight: 600; color: var(--primary);">{{ $log->user_name ?? 'Sistema' }}</span>
                                </div>
                            </td>
                            <td style="padding: 14px 20px;">
                                <div style="display: flex; flex-direction: column; gap: 4px;">
                                    <span style="font-weight: 800; color: var(--accent); font-size: 10px; text-transform: uppercase; letter-spacing: 0.05em;">{{ $log->action }}</span>
                                    <span class="badge" style="
                                        background: {{ $log->level === 'error' ? 'rgba(239, 68, 68, 0.1)' : ($log->level === 'warning' ? 'rgba(245, 158, 11, 0.1)' : 'rgba(16, 185, 129, 0.1)') }};
                                        color: {{ $log->level === 'error' ? 'var(--danger)' : ($log->level === 'warning' ? 'var(--warning)' : 'var(--success)') }};
                                        font-size: 8px; width: fit-content; border: none; padding: 1px 6px;
                                    ">{{ strtoupper($log->level) }}</span>
                                </div>
                            </td>
                            <td style="padding: 14px 20px; color: var(--primary); line-height: 1.4;">
                                {{ $log->description }}
                            </td>
                            <td style="padding: 14px 20px; font-family: 'IBM Plex Mono'; font-size: 10px; color: var(--muted);">
                                {{ $log->ip_address }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="padding: 40px; text-align: center; color: var(--muted);">No hay registros que coincidan con los filtros.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="padding: 15px 20px; border-top: 1px solid var(--border); background: rgba(255,255,255,0.01);">
            {{ $logs->links() }}
        </div>
    </div>
</div>

<style>
    .stat-mini {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        justify-content: center;
        padding: 0 15px;
        border-right: 1px solid var(--border-subtle);
    }
    .stat-mini .label {
        font-size: 9px;
        color: var(--muted);
        text-transform: uppercase;
        font-weight: 700;
        letter-spacing: 0.05em;
    }
    .stat-mini .value {
        font-size: 16px;
        font-weight: 700;
        color: var(--primary);
        font-family: 'Outfit', sans-serif;
    }
    .form-control {
        background: rgba(255,255,255,0.03);
        border: 1px solid var(--border);
        color: var(--primary);
        padding: 8px 12px;
        border-radius: 6px;
        font-size: 13px;
        transition: all 0.2s;
    }
    .form-control:focus {
        outline: none;
        border-color: var(--accent);
        background: rgba(255,255,255,0.05);
    }
    .pagination {
        display: flex;
        list-style: none;
        gap: 5px;
        margin: 0;
        padding: 0;
    }
    .page-item .page-link {
        padding: 5px 10px;
        border-radius: 4px;
        background: rgba(255,255,255,0.03);
        border: 1px solid var(--border);
        color: var(--muted);
        text-decoration: none;
        font-size: 11px;
    }
    .page-item.active .page-link {
        background: var(--accent);
        color: white;
        border-color: var(--accent);
    }
</style>
@endsection
