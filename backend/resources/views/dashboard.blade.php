@extends('layouts.app')

@section('title', 'Dashboard — DX License Manager')

@section('content')
<div class="page-header">
    <h1 class="welcome">Bienvenido, <span>{{ auth()->user()->name ?? 'Usuario' }}</span></h1>
    <p class="welcome-sub">Estado actual del ecosistema · Última actualización: {{ now()->diffForHumans() }}</p>
</div>

<div class="stats-row">
    <div class="stat-card">
        <span class="stat-label">Licencias Activas</span>
        <span class="stat-value accent">{{ number_format($metrics['total']) }}</span>
        <span class="stat-meta">Inventario Total Audidato</span>
    </div>
    <div class="stat-card danger">
        <span class="stat-label">Urgentes / Caducadas</span>
        <span class="stat-value danger">{{ number_format($metrics['critical']) }}</span>
        <span class="stat-meta">0–7 días · Acción inmediata</span>
    </div>
    <div class="stat-card warn">
        <span class="stat-label">Próximos Vencimientos</span>
        <span class="stat-value warn">{{ number_format($metrics['upcoming']) }}</span>
        <span class="stat-meta">Vencimiento en 8–30 días</span>
    </div>
    <div class="stat-card" style="border-bottom-color: var(--accent);">
        <span class="stat-label">En Seguimiento</span>
        <span class="stat-value" style="color: var(--accent);">{{ number_format($metrics['monitoring']) }}</span>
        <span class="stat-meta">Vencimiento en 31–90 días</span>
    </div>
</div>

<div class="grid-2">
    <div class="card">
        <div class="card-header">
            <span class="card-title">Vencimientos inminentes (Licencias)</span>
            <a class="card-action" href="{{ route('admin.normalization.index') }}">Ver inventario →</a>
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

    <div class="card">
        <div class="card-header">
            <span class="card-title">Acciones rápidas</span>
        </div>
        <div class="quick-actions">
            <a class="action-btn" href="{{ route('admin.import.index') }}">
                <div class="action-btn-icon">📥</div>
                <div class="action-btn-label">Importar CSV</div>
                <div class="action-btn-sub">Actualizar base instalada</div>
            </a>
            <a class="action-btn" href="#">
                <div class="action-btn-icon">🔧</div>
                <div class="action-btn-label">Herramientas</div>
                <div class="action-btn-sub">NX, STAR-CCM+, HEEDS</div>
            </a>
            <a class="action-btn" href="#">
                <div class="action-btn-icon">👥</div>
                <div class="action-btn-label">Clientes</div>
                <div class="action-btn-sub">Ver base instalada</div>
            </a>
            <a class="action-btn" href="#">
                <div class="action-btn-icon">📋</div>
                <div class="action-btn-label">Solicitar cambio</div>
                <div class="action-btn-sub">Documento Siemens</div>
            </a>
        </div>
    </div>
</div>
@endsection
