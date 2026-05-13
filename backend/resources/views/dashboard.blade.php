@extends('layouts.app')

@section('title', 'Dashboard — DX License Manager')

@section('content')
<div class="page-header">
    <h1 class="welcome">Bienvenido, <span>{{ auth()->user()->name ?? 'Usuario' }}</span></h1>
    <p class="welcome-sub">Estado actual del ecosistema · Última actualización: {{ now()->diffForHumans() }}</p>
</div>

<div class="stats-row">
    <div class="stat-card">
        <span class="stat-label">Contratos Activos</span>
        <span class="stat-value accent">{{ number_format($metrics['total']) }}</span>
        <span class="stat-meta">Ecosistema Multi-Vendor</span>
    </div>
    <div class="stat-card danger">
        <span class="stat-label">Urgentes / Caducados</span>
        <span class="stat-value danger">{{ number_format($metrics['critical']) }}</span>
        <span class="stat-meta">0–7 días · Acción inmediata</span>
    </div>
    <div class="stat-card warn">
        <span class="stat-label">Próximos</span>
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
            <span class="card-title">Vencimientos inminentes</span>
            <a class="card-action" href="#">Ver todos →</a>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Cliente</th>
                    <th>Vendor · Contrato</th>
                    <th>Caducidad</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($upcomingExpirations as $contract)
                    @php
                        $daysLeft = now()->startOfDay()->diffInDays($contract->end_date, false);
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
                    @endphp
                    <tr>
                        <td><strong>{{ $contract->client->name ?? 'Desconocido' }}</strong></td>
                        <td>
                            <div class="vendor-chip">
                                <div class="vendor-dot" style="background: {{ str_contains(strtolower($contract->vendor->name ?? ''), 'siemens') ? 'var(--siemens)' : 'var(--moldex)' }}"></div>
                                {{ $contract->vendor->name ?? 'Vendor' }}
                            </div>
                            <div style="font-size:11px;color:var(--muted);font-family:'IBM Plex Mono',monospace">
                                {{ $contract->contract_number }}
                            </div>
                        </td>
                        <td>
                            <div class="date-main">{{ $contract->end_date ? $contract->end_date->format('d/m/Y') : '—' }}</div>
                            <div class="date-sub {{ $dateSubClass }}">{{ $diffText }}</div>
                        </td>
                        <td><span class="badge {{ $badgeClass }}">{{ $statusLabel }}</span></td>
                    </tr>
                @endforeach
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
