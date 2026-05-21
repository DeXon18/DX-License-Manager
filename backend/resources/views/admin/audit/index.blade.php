@extends('layouts.app')

@section('title', 'Auditoría y Logs')

@section('content')
<div class="page-header">
    <div class="page-header-info">
        <h1 class="page-title">Auditoría y Logs</h1>
        <p class="page-sub">Monitorización total del sistema: actividad, errores y comunicaciones.</p>
    </div>
</div>


{{-- Tabs de Navegación --}}
<div class="dx-v2-audit-tabs-container">
    <a href="{{ route('admin.audit.index', ['tab' => 'activity']) }}" class="dx-v2-audit-tab-link {{ $tab == 'activity' ? 'active' : '' }}">
        <i class="fa-solid fa-list-check"></i>Actividad
    </a>
    <a href="{{ route('admin.audit.index', ['tab' => 'system']) }}" class="dx-v2-audit-tab-link {{ $tab == 'system' ? 'active' : '' }}">
        <i class="fa-solid fa-terminal"></i>Logs Sistema
    </a>
    <a href="{{ route('admin.audit.index', ['tab' => 'email']) }}" class="dx-v2-audit-tab-link {{ $tab == 'email' ? 'active' : '' }}">
        <i class="fa-solid fa-envelope"></i>Logs Email
    </a>
</div>

<div class="dashboard-container">
    @if($tab == 'activity')
    <div class="card dx-v2-audit-search-card">
        <div class="dx-v2-audit-search-body">
            <form action="{{ route('admin.audit.index') }}" method="GET" class="dx-v2-audit-filter-form">
                <input type="hidden" name="tab" value="activity">
                <div class="dx-v2-audit-filter-field grow">
                    <label class="dx-v2-audit-filter-label">Búsqueda rápida</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Usuario, descripción..." class="form-control dx-v2-audit-filter-input">
                </div>
                <div class="dx-v2-audit-filter-field width-180">
                    <label class="dx-v2-audit-filter-label">Nivel</label>
                    <select name="level" class="form-control dx-v2-audit-filter-input">
                        <option value="">Todos los niveles</option>
                        <option value="info" {{ request('level') == 'info' ? 'selected' : '' }}>Info</option>
                        <option value="warning" {{ request('level') == 'warning' ? 'selected' : '' }}>Warning</option>
                        <option value="error" {{ request('level') == 'error' ? 'selected' : '' }}>Error</option>
                    </select>
                </div>
                <div class="dx-v2-audit-filter-field width-180">
                    <label class="dx-v2-audit-filter-label">Tipo de Acción</label>
                    <input type="text" name="action" value="{{ request('action') }}" placeholder="Ej: db_backup" class="form-control dx-v2-audit-filter-input">
                </div>
                <button type="submit" class="dx-v2-audit-btn-submit">Filtrar</button>
                <a href="{{ route('admin.audit.index', ['tab' => 'activity']) }}" class="dx-v2-audit-btn-clear">Limpiar</a>
            </form>
        </div>
    </div>
    @endif

    <div class="card">
        <div class="card-header dx-v2-audit-card-header">
            <div class="dx-v2-audit-header-title-block">
                <span class="card-title dx-v2-audit-card-title">
                    @if($tab == 'activity') Timeline de Actividad @elseif($tab == 'system') Lector de Fichero (laravel.log) @else Historial de Emails Enviados @endif
                </span>
                @if($tab == 'activity')
                <form action="{{ route('admin.audit.clear.activity') }}" method="POST" onsubmit="return confirm('¿Seguro que deseas vaciar el historial de actividad?')">
                    @csrf
                    <button type="submit" class="dx-v2-audit-reset-btn">Resetear</button>
                </form>
                @elseif($tab == 'system')
                <form action="{{ route('admin.audit.clear.system') }}" method="POST" onsubmit="return confirm('¿Seguro que deseas vaciar el fichero de log?')">
                    @csrf
                    <button type="submit" class="dx-v2-audit-reset-btn">Resetear</button>
                </form>
                @elseif($tab == 'email')
                <form action="{{ route('admin.audit.clear.email') }}" method="POST" onsubmit="return confirm('¿Seguro que deseas vaciar el historial de emails?')">
                    @csrf
                    <button type="submit" class="dx-v2-audit-reset-btn">Resetear</button>
                </form>
                @endif
            </div>
            <div class="dx-v2-audit-stats-group">
                <div class="dx-v2-audit-stat-box">
                    <i class="fa-solid fa-bolt dx-v2-audit-stat-icon primary"></i>
                    <div class="dx-v2-audit-stat-info">
                        <span class="dx-v2-audit-stat-label">Eventos</span>
                        <span class="dx-v2-audit-stat-value primary">{{ $stats['total_24h'] }}</span>
                    </div>
                </div>
                <div class="dx-v2-audit-stat-box">
                    <i class="fa-solid fa-paper-plane dx-v2-audit-stat-icon accent"></i>
                    <div class="dx-v2-audit-stat-info">
                        <span class="dx-v2-audit-stat-label">Emails</span>
                        <span class="dx-v2-audit-stat-value accent">{{ $stats['emails_24h'] }}</span>
                    </div>
                </div>
                <div class="dx-v2-audit-stat-box danger-brand">
                    <i class="fa-solid fa-triangle-exclamation dx-v2-audit-stat-icon danger"></i>
                    <div class="dx-v2-audit-stat-info">
                        <span class="dx-v2-audit-stat-label">Alertas</span>
                        <span class="dx-v2-audit-stat-value danger">{{ $stats['errors_24h'] }}</span>
                    </div>
                </div>
            </div>
        </div>

        @if($tab == 'activity')
        <div class="dx-v2-audit-table-wrapper">
            <table class="dx-v2-audit-table">
                <thead class="dx-v2-audit-table-thead">
                    <tr>
                        <th class="dx-v2-audit-table-th width-150">Timestamp</th>
                        <th class="dx-v2-audit-table-th width-130">Usuario</th>
                        <th class="dx-v2-audit-table-th width-150">Acción / Nivel</th>
                        <th class="dx-v2-audit-table-th">Descripción</th>
                        <th class="dx-v2-audit-table-th width-120">IP Address</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr class="dx-v2-audit-table-tr">
                            <td class="dx-v2-audit-table-td dx-v2-audit-td-timestamp">
                                {{ \Carbon\Carbon::parse($log->created_at)->format('d/m/Y H:i:s') }}
                            </td>
                            <td class="dx-v2-audit-table-td">
                                <div class="dx-v2-audit-user-badge">
                                    <div class="dx-v2-audit-user-avatar">
                                        {{ substr($log->user_name ?? 'S', 0, 1) }}
                                    </div>
                                    <span class="dx-v2-audit-user-name">{{ $log->user_name ?? 'Sistema' }}</span>
                                </div>
                            </td>
                            <td class="dx-v2-audit-table-td">
                                <div class="dx-v2-audit-action-group">
                                    <span class="dx-v2-audit-action-title">{{ $log->action }}</span>
                                    <span class="badge dx-v2-audit-badge-level {{ $log->level }}">{{ strtoupper($log->level) }}</span>
                                </div>
                            </td>
                            <td class="dx-v2-audit-table-td dx-v2-audit-td-desc">
                                {{ $log->description }}
                            </td>
                            <td class="dx-v2-audit-table-td dx-v2-audit-td-ip">
                                {{ $log->ip_address }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="dx-v2-audit-table-td" style="padding: 40px; text-align: center; color: var(--muted);">No hay registros de actividad.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="dx-v2-audit-pagination-wrapper">
            {{ $logs->links('vendor.pagination.dx-jump') }}
        </div>

        @elseif($tab == 'system')
        <div class="dx-v2-audit-console-container" x-data="{ expanded: null }">
            <div class="dx-v2-audit-console-scroller">
                @forelse($logs as $index => $log)
                    @php
                        $badgeClass = in_array($log['level'], ['error', 'critical', 'alert', 'emergency']) ? 'danger' : ($log['level'] === 'warning' ? 'warning' : 'info');
                    @endphp
                    <div class="dx-v2-audit-console-item" :class="expanded === {{ $index }} ? 'expanded' : ''">
                        
                        {{-- Cabecera del Log --}}
                        <div class="dx-v2-audit-console-item-header" @click="expanded = (expanded === {{ $index }} ? null : {{ $index }})">
                            <span class="dx-v2-audit-console-time">
                                {{ \Carbon\Carbon::parse($log['timestamp'])->format('H:i:s') }}
                            </span>
                            
                            <span class="badge dx-v2-audit-console-badge {{ $badgeClass }}">
                                {{ strtoupper($log['level']) }}
                            </span>

                            <div class="dx-v2-audit-console-msg-block">
                                <div class="dx-v2-audit-console-message">
                                    {{ $log['message'] }}
                                </div>
                                @if($log['stack_trace'])
                                    <div class="dx-v2-audit-console-toggle-trace">
                                        <i class="fa-solid" :class="expanded === {{ $index }} ? 'fa-chevron-down' : 'fa-chevron-right'"></i>
                                        <span>Detalles de la traza</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Cuerpo del Stack Trace --}}
                        @if($log['stack_trace'])
                        <div x-show="expanded === {{ $index }}" x-collapse class="dx-v2-audit-console-trace-wrapper">
                            <pre class="dx-v2-audit-console-trace-pre">@php
                                $lines = explode("\n", $log['stack_trace']);
                                foreach($lines as $line) {
                                    $isVendor = str_contains($line, '/vendor/') || str_contains($line, 'phar://');
                                    echo '<span style="' . ($isVendor ? 'opacity: 0.4; font-size: 10px;' : 'color: #d1d5db; font-weight: 500;') . '">' . e($line) . '</span>' . "\n";
                                }
                            @endphp</pre>
                        </div>
                        @endif
                    </div>
                @empty
                    <div style="padding: 40px; text-align: center; color: var(--muted); font-size: 13px;">
                        No hay registros en el fichero <code>laravel.log</code>.
                    </div>
                @endforelse
            </div>
        </div>
        <div class="dx-v2-audit-console-footer">
            <span class="dx-v2-audit-console-footer-text">Mostrando las últimas 200 líneas de <code>storage/logs/laravel.log</code></span>
            <div style="display: flex; gap: 10px;">
                <a href="{{ route('admin.audit.index', ['tab' => 'system']) }}" class="btn btn-primary" style="font-size: 10px; padding: 4px 10px;">Refrescar</a>
            </div>
        </div>

        @elseif($tab == 'email')
        <div class="dx-v2-audit-table-wrapper">
            <table class="dx-v2-audit-table">
                <thead class="dx-v2-audit-table-thead">
                    <tr>
                        <th class="dx-v2-audit-table-th width-150">Enviado el</th>
                        <th class="dx-v2-audit-table-th width-250">Destinatario</th>
                        <th class="dx-v2-audit-table-th">Asunto</th>
                        <th class="dx-v2-audit-table-th width-100">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr class="dx-v2-audit-table-tr">
                            <td class="dx-v2-audit-table-td dx-v2-audit-td-timestamp">
                                {{ \Carbon\Carbon::parse($log->created_at)->format('d/m/Y H:i:s') }}
                            </td>
                            <td class="dx-v2-audit-table-td dx-v2-audit-td-email-recipient">
                                {{ $log->recipient }}
                            </td>
                            <td class="dx-v2-audit-table-td dx-v2-audit-td-email-subject">
                                {{ $log->subject }}
                                @if($log->mailable_class)
                                    <div class="dx-v2-audit-td-email-class">{{ $log->mailable_class }}</div>
                                @endif
                            </td>
                            <td class="dx-v2-audit-table-td">
                                <span class="badge dx-v2-audit-badge-email-status {{ $log->status }}">{{ strtoupper($log->status) }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="dx-v2-audit-table-td" style="padding: 40px; text-align: center; color: var(--muted);">No hay registros de correos enviados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="dx-v2-audit-pagination-wrapper">
            {{ $logs->links('vendor.pagination.dx-jump') }}
        </div>
        @endif
    </div>
</div>
@endsection
