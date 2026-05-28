@extends('layouts.app')

@section('title', 'Dashboard — DX License Manager')

@section('content')
<div class="page-header">
    <h1 class="welcome">Bienvenido, <span>{{ auth()->user()->name ?? 'Usuario' }}</span></h1>
    <p class="welcome-sub">Estado actual del ecosistema · Última actualización: {{ now()->diffForHumans() }}</p>
</div>

<div class="stats-row">
    {{-- Licencias Activas --}}
    <div class="stat-card success">
        <div class="dx-v2-dashboard-stat-icon">
            <svg width="84" height="84" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10"/><path d="m9 12 2 2 4-4"/></svg>
        </div>
        <span class="stat-label">Licencias Activas</span>
        <span class="stat-value success">{{ number_format($metrics['total']) }}</span>
        <span class="stat-meta">Inventario Total Audidato</span>
    </div>

    {{-- Urgentes --}}
    <div class="stat-card danger">
        <div class="dx-v2-dashboard-stat-icon">
            <svg width="84" height="84" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M8.5 14.5A2.5 2.5 0 0 0 11 12c0-1.38-.5-2-1-3-1.072-2.143-.224-4.054 2-6 .5 2.5 2 4.9 4 6.5 2 1.6 3 3.5 3 5.5a7 7 0 1 1-14 0c0-1.153.433-2.294 1-3a2.5 2.5 0 0 0 2.5 2.5z"/></svg>
        </div>
        <span class="stat-label">Urgentes / Caducadas</span>
        <span class="stat-value danger">{{ number_format($metrics['critical']) }}</span>
        <span class="stat-meta">0–7 días · Acción inmediata</span>
    </div>

    {{-- Próximos --}}
    <div class="stat-card warn">
        <div class="dx-v2-dashboard-stat-icon">
            <svg width="84" height="84" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M10 2h4"/><path d="M12 14v-4"/><path d="M4 13a8 8 0 0 1 8-7 8 8 0 1 1-5.3 14L4 17.6"/><path d="M9 17H4v5"/></svg>
        </div>
        <span class="stat-label">Próximos Vencimientos</span>
        <span class="stat-value warn">{{ number_format($metrics['upcoming']) }}</span>
        <span class="stat-meta">Vencimiento en 8–30 días</span>
    </div>

    {{-- Seguimiento --}}
    <div class="stat-card accent">
        <div class="dx-v2-dashboard-stat-icon">
            <svg width="84" height="84" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
        </div>
        <span class="stat-label">En Seguimiento</span>
        <span class="stat-value accent">{{ number_format($metrics['monitoring']) }}</span>
        <span class="stat-meta">Vencimiento en 31–90 días</span>
    </div>
</div>

{{-- Buscador Global Express --}}
<div class="card dx-v2-dashboard-search-card">
    <div class="dx-v2-dashboard-search-body">
        <form action="{{ route('clients.index') }}" method="GET" class="dx-v2-dashboard-search-form">
            <div class="dx-v2-dashboard-search-input-wrap">
                <i class="fa-solid fa-magnifying-glass dx-v2-dashboard-search-icon"></i>
                <input type="text" name="search" placeholder="Buscador Global Express: Sold-To, Cliente o Machine ID..." class="dx-v2-dashboard-search-input">
            </div>
            <button type="submit" class="btn btn-primary dx-v2-dashboard-search-submit">
                <i class="fa-solid fa-bolt"></i> Localizar
            </button>
        </form>
    </div>
</div>

<div class="grid-2">
    <div class="card">
        <div class="card-header">
            <span class="card-title">Vencimientos inminentes (Licencias)</span>
            @if(auth()->user()->isAdmin())
                <a class="card-action" href="{{ route('admin.normalization.index') }}">Ver inventario →</a>
            @endif
        </div>
        <table>
            <thead>
                <tr>
                    <th>Cliente</th>
                    <th class="dx-v2-table-nowrap">Vendor · Sold-To</th>
                    <th>Caducidad</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @forelse($upcomingExpirations as $license)
                    @php
                        $daysLeft = now()->startOfDay()->diffInDays($license->expiration_date, false);
                        $badgeClass = 'badge-success';
                        $statusLabel = 'Vigente';
                        $dateSubClass = 'muted';

                        if ($daysLeft < 0) {
                            $badgeClass = 'badge-danger';
                            $statusLabel = 'Caducado';
                            $dateSubClass = 'danger';
                            $diffText = 'Hace ' . abs($daysLeft) . ' días';
                        } elseif ($daysLeft <= 7) {
                            $badgeClass = 'badge-danger';
                            $statusLabel = 'Crítico';
                            $dateSubClass = 'danger';
                            $diffText = $daysLeft == 0 ? 'Hoy' : ($daysLeft == 1 ? 'Mañana' : "En $daysLeft días");
                        } elseif ($daysLeft <= 30) {
                            $badgeClass = 'badge-warn';
                            $statusLabel = 'Próximo';
                            $dateSubClass = 'warn';
                            $diffText = "En $daysLeft días";
                        } elseif ($daysLeft <= 90) {
                            $badgeClass = 'badge-ai';
                            $statusLabel = 'Seguimiento';
                            $dateSubClass = 'accent';
                            $diffText = "En $daysLeft días";
                        } else {
                            $diffText = "En $daysLeft días";
                        }

                        $vendor = $license->daemon->vendor ?? 'siemens';
                        $soldTo = $license->daemon->sold_to ?? 'N/A';
                    @endphp
                    <tr>
                        <td>
                            <a href="{{ route('clients.show', $license->daemon->client->id ?? 0) }}" class="dx-v2-link-inherit">
                                <strong>{{ $license->daemon->client->name ?? 'Desconocido' }}</strong>
                            </a>
                        </td>
                        <td class="dx-v2-table-nowrap">
                            <span class="dx-v2-dashboard-vendor-text {{ $vendor }}">
                                {{ $vendor }}
                            </span>
                            <span class="dx-v2-dashboard-table-dot">·</span>
                            <span class="dx-v2-dashboard-table-soldto">
                                {{ $soldTo }}
                            </span>
                        </td>
                        <td>
                            <div class="date-main">{{ $license->expiration_date ? $license->expiration_date->format('d/m/Y') : '—' }}</div>
                            <div class="date-sub {{ $dateSubClass }}">{{ $diffText }}</div>
                        </td>
                        <td><span class="badge {{ $badgeClass }}">{{ $statusLabel }}</span></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="dx-v2-dashboard-empty-td">
                            <div class="dx-v2-dashboard-empty-icon">🛡️</div>
                            Todo bajo control. No hay vencimientos en los próximos 90 días.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="dx-v2-dashboard-right-col">
        <div class="card">
            <div class="card-header">
                <span class="card-title">Acciones rápidas</span>
            </div>
            <div class="quick-actions">
                <a class="action-btn dx-v2-dashboard-action-btn-rel" href="{{ route('renewal-planner.index') }}">
                    @if($renewalsThisMonth > 0)
                        <span class="dx-v2-dashboard-badge-count">
                            {{ $renewalsThisMonth }}
                        </span>
                    @endif
                    <div class="action-btn-icon">📅</div>
                    <div class="action-btn-label">Renovaciones</div>
                    <div class="action-btn-sub">{{ $renewalsThisMonth > 0 ? 'Tareas pendientes este mes' : 'Todo al día este mes' }}</div>
                </a>
                <a class="action-btn" href="{{ route('tools.cod.index') }}">
                    <div class="action-btn-icon">📄</div>
                    <div class="action-btn-label">Generar COD</div>
                    <div class="action-btn-sub">Certificado de Cese Siemens</div>
                </a>
                <a class="action-btn" href="{{ route('tools.index') }}">
                    <div class="action-btn-icon">🔧</div>
                    <div class="action-btn-label">Auditoría IA</div>
                    <div class="action-btn-sub">Subir NX, STAR-CCM, Moldex</div>
                </a>
                <a class="action-btn" href="{{ route('clients.index') }}">
                    <div class="action-btn-icon">👥</div>
                    <div class="action-btn-label">Base Clientes</div>
                    <div class="action-btn-sub">Buscar y gestionar cuentas</div>
                </a>
            </div>
        </div>

        <!-- Card: Gestión de Contratos -->
        <div class="card">
            <div class="card-header">
                <span class="card-title">Gestión de Contratos</span>
            </div>
            <div class="dx-v2-dashboard-contracts-grid">
                @php
                    // Preparamos los estados combinando la config (ordenada) con los datos de la BD
                    $displayStatuses = [];
                    foreach($contractStatuses as $key => $config) {
                        $dbKey = ($key === 'vacio') ? "" : $key;
                        $count = $contractCounts[$dbKey] ?? 0;
                        if ($count > 0) {
                            $displayStatuses[$dbKey] = array_merge($config, ['count' => $count]);
                        }
                    }
                    // Añadimos estados que estén en la BD pero NO en la config (extraños)
                    foreach($contractCounts as $dbKey => $count) {
                        if ($count > 0 && !isset($displayStatuses[$dbKey])) {
                            $displayStatuses[$dbKey] = [
                                'label' => $dbKey ?: 'Sin estado',
                                'color' => 'gris',
                                'icon' => 'fa-regular fa-circle-question',
                                'count' => $count
                            ];
                        }
                    }
                @endphp

                @foreach($displayStatuses as $dbKey => $data)
                    @php
                        $colorClass = 'dx-v2-color-' . str_replace(' ', '-', $data['color'] ?? 'gris');
                    @endphp
                    <div class="dx-v2-dashboard-contract-item">
                        <div class="dx-v2-dashboard-contract-left">
                            <div class="dx-v2-dashboard-contract-icon-box">
                                <i class="{{ $data['icon'] ?? 'fa-solid fa-question' }} {{ $colorClass }}"></i>
                            </div>
                            <span class="dx-v2-dashboard-contract-label">{{ $data['label'] ?? $dbKey }}</span>
                        </div>
                        <span class="dx-v2-dashboard-contract-count">
                            {{ $data['count'] }}
                        </span>
                    </div>
                @endforeach
            </div>
            <div class="dx-v2-dashboard-contract-footer">
                <span class="dx-v2-dashboard-contract-footer-label">Total Contratos</span>
                <span class="dx-v2-dashboard-contract-footer-value">{{ array_sum($contractCounts) }}</span>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Tour Contextual específico para el Dashboard
    window.pageTourSteps = [
        {
            element: '.sidebar',
            popover: {
                title: 'Navegación Principal',
                description: 'Desde aquí puedes saltar a cualquier módulo del sistema: Clientes, Planificador, Reportes o Herramientas.',
                side: 'right',
                align: 'start'
            }
        },
        {
            element: '.stat-card.success',
            popover: {
                title: 'Licencias Activas',
                description: 'El volumen total de licencias vigentes que estamos auditando y controlando en este momento.',
                side: 'bottom',
                align: 'center'
            }
        },
        {
            element: '.stat-card.danger',
            popover: {
                title: 'Urgencias (0–7 días)',
                description: '¡Atención prioritaria! Licencias caducadas o que vencen esta misma semana. Requieren acción inmediata.',
                side: 'bottom',
                align: 'center'
            }
        },
        {
            element: '.stat-card.warn',
            popover: {
                title: 'Próximos Vencimientos (8–30 días)',
                description: 'Licencias que caducarán este mes. Ideal para ir preparando renovaciones con margen.',
                side: 'bottom',
                align: 'center'
            }
        },
        {
            element: '.stat-card.accent',
            popover: {
                title: 'En Seguimiento (31–90 días)',
                description: 'Licencias a medio plazo. Útil para hacer previsiones a trimestre vencido.',
                side: 'bottom',
                align: 'center'
            }
        },
        {
            element: '.dx-v2-dashboard-search-card',
            popover: {
                title: 'Buscador Global Express',
                description: 'Encuentra al instante cualquier cliente introduciendo su nombre, su código Sold-To o su Machine ID.',
                side: 'bottom',
                align: 'start'
            }
        },
        {
            element: '.dx-chatbot-trigger',
            popover: {
                title: 'Asistente IA Integrado',
                description: 'Si tienes dudas sobre el inventario o necesitas analizar datos, puedes preguntarle al Chatbot inteligente en cualquier momento.',
                side: 'left',
                align: 'end'
            }
        }
    ];
</script>
@endpush
