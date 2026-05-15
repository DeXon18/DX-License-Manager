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
        <div style="position: absolute; top: -5px; right: -15px; color: var(--success); opacity: 0.08; pointer-events: none; transform: rotate(-15deg);">
            <svg width="84" height="84" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10"/><path d="m9 12 2 2 4-4"/></svg>
        </div>
        <span class="stat-label">Licencias Activas</span>
        <span class="stat-value success">{{ number_format($metrics['total']) }}</span>
        <span class="stat-meta">Inventario Total Audidato</span>
    </div>

    {{-- Urgentes --}}
    <div class="stat-card danger">
        <div style="position: absolute; top: -5px; right: -15px; color: var(--danger); opacity: 0.08; pointer-events: none; transform: rotate(-15deg);">
            <svg width="84" height="84" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M8.5 14.5A2.5 2.5 0 0 0 11 12c0-1.38-.5-2-1-3-1.072-2.143-.224-4.054 2-6 .5 2.5 2 4.9 4 6.5 2 1.6 3 3.5 3 5.5a7 7 0 1 1-14 0c0-1.153.433-2.294 1-3a2.5 2.5 0 0 0 2.5 2.5z"/></svg>
        </div>
        <span class="stat-label">Urgentes / Caducadas</span>
        <span class="stat-value danger">{{ number_format($metrics['critical']) }}</span>
        <span class="stat-meta">0–7 días · Acción inmediata</span>
    </div>

    {{-- Próximos --}}
    <div class="stat-card warn">
        <div style="position: absolute; top: -5px; right: -15px; color: var(--warning); opacity: 0.08; pointer-events: none; transform: rotate(-15deg);">
            <svg width="84" height="84" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M10 2h4"/><path d="M12 14v-4"/><path d="M4 13a8 8 0 0 1 8-7 8 8 0 1 1-5.3 14L4 17.6"/><path d="M9 17H4v5"/></svg>
        </div>
        <span class="stat-label">Próximos Vencimientos</span>
        <span class="stat-value warn">{{ number_format($metrics['upcoming']) }}</span>
        <span class="stat-meta">Vencimiento en 8–30 días</span>
    </div>

    {{-- Seguimiento --}}
    <div class="stat-card accent">
        <div style="position: absolute; top: -5px; right: -15px; color: var(--accent); opacity: 0.08; pointer-events: none; transform: rotate(-15deg);">
            <svg width="84" height="84" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
        </div>
        <span class="stat-label">En Seguimiento</span>
        <span class="stat-value accent">{{ number_format($metrics['monitoring']) }}</span>
        <span class="stat-meta">Vencimiento en 31–90 días</span>
    </div>
</div>

{{-- Buscador Global Express --}}
<div class="card" style="margin-bottom: 24px; background: var(--surface); border: 1px solid var(--border); box-shadow: 0 4px 20px rgba(0,0,0,0.15);">
    <div style="padding: 16px 24px;">
        <form action="{{ route('clients.index') }}" method="GET" style="display: flex; gap: 12px; align-items: center;">
            <div style="flex: 1; position: relative;">
                <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: var(--muted); font-size: 14px;"></i>
                <input type="text" name="search" placeholder="Buscador Global Express: Sold-To, Cliente o Machine ID..." 
                    style="width: 100%; padding: 14px 14px 14px 45px; background: var(--raised); border: 1px solid var(--border); border-radius: 8px; color: var(--primary); font-size: 14px; outline: none; transition: border-color 0.2s;"
                    onfocus="this.style.borderColor='var(--accent)'" onblur="this.style.borderColor='var(--border)'">
            </div>
            <button type="submit" class="btn btn-primary" style="padding: 14px 24px; border-radius: 8px; font-weight: 600; display: flex; align-items: center; gap: 8px;">
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
                    <th style="white-space: nowrap;">Vendor · Sold-To</th>
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
                            <a href="{{ route('clients.show', $license->daemon->client->id ?? 0) }}" style="text-decoration: none; color: inherit;">
                                <strong>{{ $license->daemon->client->name ?? 'Desconocido' }}</strong>
                            </a>
                        </td>
                        <td style="white-space: nowrap;">
                            <span style="text-transform: uppercase; font-weight: 800; font-size: 10px; color: {{ $vendor == 'siemens' ? 'var(--siemens)' : 'var(--moldex)' }}; letter-spacing: 0.05em;">
                                {{ $vendor }}
                            </span>
                            <span style="color: var(--muted); margin: 0 4px; font-size: 10px;">·</span>
                            <span style="font-size: 11px; color: var(--secondary); font-family: 'IBM Plex Mono', monospace;">
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
                        <td colspan="4" style="text-align: center; padding: 40px; color: var(--muted);">
                            <div style="font-size: 24px; margin-bottom: 10px;">🛡️</div>
                            Todo bajo control. No hay vencimientos en los próximos 90 días.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="display: flex; flex-direction: column; gap: 16px;">
        <div class="card">
            <div class="card-header">
                <span class="card-title">Acciones rápidas</span>
            </div>
            <div class="quick-actions">
                <a class="action-btn" href="{{ route('renewal-planner.index') }}" style="position: relative;">
                    @if($renewalsThisMonth > 0)
                        <span style="position: absolute; top: -8px; right: -8px; background: var(--danger); color: white; padding: 2px 8px; border-radius: 20px; font-size: 11px; font-weight: 800; box-shadow: 0 2px 10px rgba(0,0,0,0.5); z-index: 10;">
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
            <div style="padding: 12px; display: grid; grid-template-columns: 1fr; gap: 8px;">
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
                        $color = match($data['color'] ?? 'gris') {
                            'azul claro' => '#388bfd',
                            'azul intenso' => '#1d6ae8',
                            'morado' => '#a855f7',
                            'amarillo' => '#eab308',
                            'naranja' => '#f97316',
                            'verde' => '#238636',
                            'rojo apagado' => '#da3633',
                            'gris' => 'var(--muted)',
                            default => 'var(--muted)'
                        };
                    @endphp
                    <div style="display: flex; align-items: center; justify-content: space-between; padding: 8px 12px; background: var(--raised); border: 1px solid var(--border); border-radius: 6px;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div style="width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; background: rgba(255,255,255,0.05); border-radius: 4px;">
                                <i class="{{ $data['icon'] ?? 'fa-solid fa-question' }}" style="color: {{ $color }}; font-size: 12px;"></i>
                            </div>
                            <span style="font-size: 12px; font-weight: 500; color: var(--secondary);">{{ $data['label'] ?? $dbKey }}</span>
                        </div>
                        <span style="font-family: var(--font-mono); font-size: 13px; font-weight: 600; color: var(--primary);">
                            {{ $data['count'] }}
                        </span>
                    </div>
                @endforeach
            </div>
            <div class="card-header" style="border-top: 1px solid var(--border); border-bottom: none; background: transparent; padding: 10px 20px;">
                <span style="font-size: 11px; color: var(--muted); text-transform: uppercase; letter-spacing: 0.05em;">Total Contratos</span>
                <span style="font-family: var(--font-mono); font-size: 13px; font-weight: 700; color: var(--accent);">{{ array_sum($contractCounts) }}</span>
            </div>
        </div>
    </div>
</div>
@endsection
