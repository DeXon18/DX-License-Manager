@extends('layouts.app')

@section('title', 'Auditoría y Logs')

@section('content')
<div class="page-header">
    <div class="page-header-info">
        <h1 class="page-title">Auditoría y Logs</h1>
        <p class="page-sub">Monitorización total del sistema: actividad, errores y comunicaciones.</p>
    </div>
</div>

@if(session('success'))
    <div style="background: rgba(16, 185, 129, 0.1); border: 1px solid var(--success); color: var(--success); padding: 12px 20px; border-radius: 8px; margin-bottom: 24px; font-size: 13px; font-weight: 600; display: flex; align-items: center; gap: 10px;">
        <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div style="background: rgba(239, 68, 68, 0.1); border: 1px solid var(--danger); color: var(--danger); padding: 12px 20px; border-radius: 8px; margin-bottom: 24px; font-size: 13px; font-weight: 600; display: flex; align-items: center; gap: 10px;">
        <i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}
    </div>
@endif

{{-- Tabs de Navegación --}}
<div style="display: flex; gap: 10px; margin-bottom: 24px;">
    <a href="{{ route('admin.audit.index', ['tab' => 'activity']) }}" 
       style="padding: 10px 20px; border-radius: 8px; font-weight: 700; text-transform: uppercase; font-size: 11px; text-decoration: none; transition: all 0.2s; 
              background: {{ $tab == 'activity' ? 'var(--accent)' : 'rgba(255,255,255,0.03)' }}; 
              color: {{ $tab == 'activity' ? '#fff' : 'var(--muted)' }}; 
              border: 1px solid {{ $tab == 'activity' ? 'var(--accent)' : 'var(--border)' }};">
        <i class="fa-solid fa-list-check" style="margin-right: 8px;"></i>Actividad
    </a>
    <a href="{{ route('admin.audit.index', ['tab' => 'system']) }}" 
       style="padding: 10px 20px; border-radius: 8px; font-weight: 700; text-transform: uppercase; font-size: 11px; text-decoration: none; transition: all 0.2s; 
              background: {{ $tab == 'system' ? 'var(--accent)' : 'rgba(255,255,255,0.03)' }}; 
              color: {{ $tab == 'system' ? '#fff' : 'var(--muted)' }}; 
              border: 1px solid {{ $tab == 'system' ? 'var(--accent)' : 'var(--border)' }};">
        <i class="fa-solid fa-terminal" style="margin-right: 8px;"></i>Logs Sistema
    </a>
    <a href="{{ route('admin.audit.index', ['tab' => 'email']) }}" 
       style="padding: 10px 20px; border-radius: 8px; font-weight: 700; text-transform: uppercase; font-size: 11px; text-decoration: none; transition: all 0.2s; 
              background: {{ $tab == 'email' ? 'var(--accent)' : 'rgba(255,255,255,0.03)' }}; 
              color: {{ $tab == 'email' ? '#fff' : 'var(--muted)' }}; 
              border: 1px solid {{ $tab == 'email' ? 'var(--accent)' : 'var(--border)' }};">
        <i class="fa-solid fa-envelope" style="margin-right: 8px;"></i>Logs Email
    </a>
</div>

<div class="dashboard-container">
    @if($tab == 'activity')
    <div class="card" style="margin-bottom: 24px;">
        <div style="padding: 20px;">
            <form action="{{ route('admin.audit.index') }}" method="GET" style="display: flex; gap: 15px; align-items: flex-end;">
                <input type="hidden" name="tab" value="activity">
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
                <button type="submit" class="btn btn-primary" style="height: 38px;">Filtrar</button>
                <a href="{{ route('admin.audit.index', ['tab' => 'activity']) }}" class="btn-clear">Limpiar</a>
            </form>
        </div>
    </div>
    @endif

    <div class="card">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
            <div style="display: flex; align-items: center; gap: 15px;">
                <span class="card-title">
                    @if($tab == 'activity') Timeline de Actividad @elseif($tab == 'system') Lector de Fichero (laravel.log) @else Historial de Emails Enviados @endif
                </span>
                @if($tab == 'activity')
                <form action="{{ route('admin.audit.clear.activity') }}" method="POST" onsubmit="return confirm('¿Seguro que deseas vaciar el historial de actividad?')">
                    @csrf
                    <button type="submit" style="background: transparent; border: 1px solid var(--danger); color: var(--danger); font-size: 8px; padding: 2px 8px; border-radius: 4px; cursor: pointer; text-transform: uppercase; font-weight: 700;">Resetear</button>
                </form>
                @endif
            </div>
            <div style="display: flex; gap: 20px;">
                <div style="display: flex; flex-direction: column; align-items: flex-end;">
                    <span style="font-size: 9px; color: var(--muted); text-transform: uppercase; font-weight: 700;">Eventos (24h)</span>
                    <span style="font-size: 14px; font-weight: 700; color: var(--primary);">{{ $stats['total_24h'] }}</span>
                </div>
                <div style="display: flex; flex-direction: column; align-items: flex-end;">
                    <span style="font-size: 9px; color: var(--muted); text-transform: uppercase; font-weight: 700;">Emails (24h)</span>
                    <span style="font-size: 14px; font-weight: 700; color: var(--accent);">{{ $stats['emails_24h'] }}</span>
                </div>
                <div style="display: flex; flex-direction: column; align-items: flex-end;">
                    <span style="font-size: 9px; color: var(--muted); text-transform: uppercase; font-weight: 700;">Alertas</span>
                    <span style="font-size: 14px; font-weight: 700; color: var(--danger);">{{ $stats['errors_24h'] }}</span>
                </div>
            </div>
        </div>

        @if($tab == 'activity')
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
                            <td colspan="5" style="padding: 40px; text-align: center; color: var(--muted);">No hay registros de actividad.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="padding: 15px 20px; border-top: 1px solid var(--border); background: rgba(255,255,255,0.01);">
            {{ $logs->links() }}
        </div>

        @elseif($tab == 'system')
        <div style="padding: 20px; background: #000; overflow-x: auto;">
            <pre style="margin: 0; font-family: 'IBM Plex Mono'; font-size: 11px; color: #a9b7c6; line-height: 1.5; white-space: pre-wrap;">{{ $logs }}</pre>
        </div>
        <div style="padding: 12px 20px; border-top: 1px solid var(--border); background: rgba(255,255,255,0.01); display: flex; justify-content: space-between; align-items: center;">
            <span style="font-size: 10px; color: var(--muted);">Mostrando las últimas 200 líneas de <code>storage/logs/laravel.log</code></span>
            <div style="display: flex; gap: 10px;">
                <form action="{{ route('admin.audit.clear.system') }}" method="POST" onsubmit="return confirm('¿Seguro que deseas vaciar el fichero de log?')">
                    @csrf
                    <button type="submit" style="background: transparent; border: 1px solid var(--danger); color: var(--danger); font-size: 10px; padding: 4px 10px; border-radius: 6px; cursor: pointer; font-weight: 600;">Resetear Fichero</button>
                </form>
                <a href="{{ route('admin.audit.index', ['tab' => 'system']) }}" class="btn btn-primary" style="font-size: 10px; padding: 4px 10px;">Refrescar</a>
            </div>
        </div>

        @elseif($tab == 'email')
        <div style="padding: 0; overflow: hidden;">
            <table style="width: 100%; border-collapse: collapse; font-size: 12px;">
                <thead style="background: rgba(255,255,255,0.02); border-bottom: 1px solid var(--border);">
                    <tr>
                        <th style="padding: 12px 20px; text-align: left; color: var(--muted); font-weight: 700; text-transform: uppercase; width: 150px;">Enviado el</th>
                        <th style="padding: 12px 20px; text-align: left; color: var(--muted); font-weight: 700; text-transform: uppercase; width: 250px;">Destinatario</th>
                        <th style="padding: 12px 20px; text-align: left; color: var(--muted); font-weight: 700; text-transform: uppercase;">Asunto</th>
                        <th style="padding: 12px 20px; text-align: left; color: var(--muted); font-weight: 700; text-transform: uppercase; width: 100px;">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr style="border-bottom: 1px solid var(--border-subtle); transition: background 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.01)'" onmouseout="this.style.background='transparent'">
                            <td style="padding: 14px 20px; font-family: 'IBM Plex Mono'; font-size: 11px; color: var(--muted);">
                                {{ \Carbon\Carbon::parse($log->created_at)->format('d/m/Y H:i:s') }}
                            </td>
                            <td style="padding: 14px 20px; font-weight: 600; color: var(--primary);">
                                {{ $log->recipient }}
                            </td>
                            <td style="padding: 14px 20px; color: var(--primary);">
                                {{ $log->subject }}
                                @if($log->mailable_class)
                                    <div style="font-size: 10px; color: var(--muted); margin-top: 4px;">{{ $log->mailable_class }}</div>
                                @endif
                            </td>
                            <td style="padding: 14px 20px;">
                                <span class="badge" style="
                                    background: {{ $log->status === 'sent' ? 'rgba(16, 185, 129, 0.1)' : 'rgba(239, 68, 68, 0.1)' }};
                                    color: {{ $log->status === 'sent' ? 'var(--success)' : 'var(--danger)' }};
                                    font-size: 9px; width: fit-content; border: none;
                                ">{{ strtoupper($log->status) }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="padding: 40px; text-align: center; color: var(--muted);">No hay registros de correos enviados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="padding: 15px 20px; border-top: 1px solid var(--border); background: rgba(255,255,255,0.01); display: flex; justify-content: space-between; align-items: center;">
            <div>{{ $logs->links() }}</div>
            <form action="{{ route('admin.audit.clear.email') }}" method="POST" onsubmit="return confirm('¿Seguro que deseas vaciar el historial de emails?')">
                @csrf
                <button type="submit" style="background: transparent; border: 1px solid var(--danger); color: var(--danger); font-size: 10px; padding: 4px 10px; border-radius: 6px; cursor: pointer; font-weight: 600;">Resetear Historial</button>
            </form>
        </div>
        @endif
    </div>
</div>

<style>
    .btn-clear {
        height: 38px;
        display: flex;
        align-items: center;
        padding: 0 16px;
        border-radius: 8px;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid var(--border);
        color: var(--muted);
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s;
    }
    .btn-clear:hover {
        background: rgba(255, 255, 255, 0.1);
        color: var(--primary);
        border-color: var(--border-subtle);
    }
    .form-control {
        background: #12141a;
        border: 1px solid var(--border);
        color: var(--primary);
        padding: 8px 12px;
        border-radius: 6px;
        font-size: 13px;
        transition: all 0.2s;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%234b5563'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 10px center;
        background-size: 16px;
        padding-right: 35px;
    }
    .form-control option {
        background: #12141a;
        color: var(--primary);
    }
    .form-control:focus {
        outline: none;
        border-color: var(--accent);
        background: #161922;
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
