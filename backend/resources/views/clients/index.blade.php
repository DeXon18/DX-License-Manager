@extends('layouts.app')

@section('content')
<div class="dx-v2-page-header">
    <div>
        <div class="breadcrumb">
            <a href="{{ route('clients.index') }}">Inventario</a>
            <span class="separator">/</span>
            <span class="current">Directorio</span>
        </div>
        <h1 class="page-title">Gestión de <span>Clientes</span></h1>
        <p class="page-subtitle">Visualización y búsqueda de cuentas del ecosistema.</p>
    </div>
    <div class="dx-v2-page-header-actions" style="flex-direction: column; align-items: flex-end; gap: 8px;">
        <div class="search-box dx-v2-clients-search-box" style="margin: 0;">
            <form action="{{ route('clients.index') }}" method="GET" class="dx-v2-clients-search-form">
                <svg class="dx-v2-clients-search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <input type="text" name="search" value="{{ request('search') }}" 
                    placeholder="Buscar clientes por nombre o identificador..." class="dx-v2-form-input dx-v2-clients-search-input" 
                    x-on:input.debounce.500ms="$el.closest('form').submit()">
            </form>

            <div class="filter-actions dx-v2-clients-filter-actions">
                @php 
                    $hasInv = session('client_has_inventory', false); 
                    $currentVendor = session('client_inventory_vendor', 'all');
                @endphp
                
                <div class="inventory-filter-group">
                    <div class="dx-v2-clients-seg-control">
                        <!-- OFF -->
                        <a href="{{ route('clients.index', array_merge(request()->except('has_inventory'), ['clear_inventory' => 1])) }}" 
                           class="dx-v2-clients-seg-item {{ !$hasInv ? 'active off' : '' }}" title="Desactivar filtros">
                            <div class="dx-v2-clients-seg-icon"><i class="fa-solid fa-ban"></i></div>
                            <span class="dx-v2-clients-seg-text">OFF</span>
                        </a>
                        
                        <!-- ALL -->
                        <a href="{{ route('clients.index', array_merge(request()->all(), ['has_inventory' => 1, 'vendor_filter' => 'all'])) }}" 
                           class="dx-v2-clients-seg-item {{ $hasInv && $currentVendor === 'all' ? 'active all' : '' }}" title="Todos los vendors">
                            <div class="dx-v2-clients-seg-icon"><i class="fa-solid fa-layer-group"></i></div>
                            <span class="dx-v2-clients-seg-text">ALL</span>
                        </a>

                        <!-- SIEMENS -->
                        <a href="{{ route('clients.index', array_merge(request()->all(), ['has_inventory' => 1, 'vendor_filter' => 'siemens'])) }}" 
                           class="dx-v2-clients-seg-item {{ $hasInv && $currentVendor === 'siemens' ? 'active siemens' : '' }}" title="Solo Siemens">
                            <div class="dx-v2-clients-seg-icon"><i class="fa-solid fa-microchip"></i></div>
                            <span class="dx-v2-clients-seg-text">Siemens</span>
                        </a>

                        <!-- MOLDEX -->
                        <a href="{{ route('clients.index', array_merge(request()->all(), ['has_inventory' => 1, 'vendor_filter' => 'moldex'])) }}" 
                           class="dx-v2-clients-seg-item {{ $hasInv && $currentVendor === 'moldex' ? 'active moldex' : '' }}" title="Solo Moldex3D">
                            <div class="dx-v2-clients-seg-icon"><i class="fa-solid fa-cube"></i></div>
                            <span class="dx-v2-clients-seg-text">Moldex</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="dx-v2-sys-dash-stats-grid" style="margin-bottom: 24px;">
    {{-- Clientes Registrados --}}
    <div class="dx-v2-sys-dash-stat-card">
        <div class="dx-v2-sys-dash-stat-card-watermark">
            <svg width="84" height="84" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        </div>
        <div class="dx-v2-sys-dash-stat-card-title">
            CLIENTES REGISTRADOS
        </div>
        <div class="dx-v2-sys-dash-stat-card-value success-status">
            {{ $globalMetrics['total_clients'] }}
        </div>
        <div class="dx-v2-sys-dash-stat-card-meta-mono">
            Directorio Total
        </div>
    </div>

    {{-- Contratos --}}
    <div class="dx-v2-sys-dash-stat-card">
        <div class="dx-v2-sys-dash-stat-card-watermark">
            <svg width="84" height="84" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        </div>
        <div class="dx-v2-sys-dash-stat-card-title">
            CONTRATOS GESTIONADOS
        </div>
        <div class="dx-v2-sys-dash-stat-card-value" style="color: var(--dx-v2-accent);">
            {{ $globalMetrics['total_contracts'] }}
        </div>
        <div class="dx-v2-sys-dash-stat-card-meta-mono">
            Acuerdos en Sistema
        </div>
    </div>

    {{-- Activos Siemens --}}
    <div class="dx-v2-sys-dash-stat-card">
        <div class="dx-v2-sys-dash-stat-card-watermark">
            <svg width="84" height="84" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10M9 12l2 2 4-4"/></svg>
        </div>
        <div class="dx-v2-sys-dash-stat-card-title">
            LICENCIAS ACTIVAS
        </div>
        <div class="dx-v2-sys-dash-stat-card-value success-status">
            {{ $globalMetrics['siemens_licenses'] }}
        </div>
        <div class="dx-v2-sys-dash-stat-card-meta-mono" style="border-top: none; text-align: center; color: #00d8b6 !important; font-weight: 700 !important; font-size: 12px !important; letter-spacing: 1px; width: 100%;">
            SIEMENS PLM
        </div>
    </div>

    {{-- Activos Moldex --}}
    <div class="dx-v2-sys-dash-stat-card">
        <div class="dx-v2-sys-dash-stat-card-watermark">
            <svg width="84" height="84" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
        </div>
        <div class="dx-v2-sys-dash-stat-card-title">
            LICENCIAS ACTIVAS
        </div>
        <div class="dx-v2-sys-dash-stat-card-value" style="color: var(--dx-v2-accent, #6366f1) !important;">
            {{ $globalMetrics['moldex_licenses'] }}
        </div>
        <div class="dx-v2-sys-dash-stat-card-meta-mono" style="border-top: none; text-align: center; color: #ff5c5c !important; font-weight: 700 !important; font-size: 12px !important; letter-spacing: 1px; width: 100%;">
            MOLDEX3D
        </div>
    </div>
</div>

<div class="card">
    <table class="table">
        <thead>
            <tr>
                <th>Cliente</th>
                <th class="text-center">Inventario</th>
                <th class="text-center">Contratos</th>
                <th class="text-center">Estado</th>
                <th class="text-right">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($clients as $client)
            <tr>
                <td>
                    <div class="dx-v2-clients-flex-align">
                        <div class="font-bold">{{ $client->name }}</div>
                        @if($client->inventory_daemons_count > 0)
                            <span title="Licencias Detectadas" class="dx-v2-clients-pulse-warning pulse-soft">
                                <i class="fa-solid fa-database dx-v2-clients-db-icon"></i>
                            </span>
                        @endif
                    </div>
                </td>
                <td class="text-center">
                    <div class="dx-v2-clients-flex-column-center">
                        @if($client->siemens_daemons_count > 0)
                            <span class="badge dx-v2-clients-vendor-badge siemens">
                                <span>{{ $client->siemens_daemons_count }}</span>
                                <span class="dx-v2-clients-badge-sub">Siemens</span>
                            </span>
                        @endif
                        @if($client->moldex_daemons_count > 0)
                            <span class="badge dx-v2-clients-vendor-badge moldex">
                                <span>{{ $client->moldex_daemons_count }}</span>
                                <span class="dx-v2-clients-badge-sub">Moldex3D</span>
                            </span>
                        @endif
                        @if($client->inventory_daemons_count == 0)
                            <span class="muted text-xs">—</span>
                        @endif
                    </div>
                </td>
                <td class="text-center font-mono body-sm">
                    {{ $client->contracts_count }}
                </td>
                <td class="text-center">
                    @if($client->contracts_count > 0)
                        <span class="badge badge-success">Activo</span>
                    @else
                        <span class="badge badge-muted">Sin Contratos</span>
                    @endif
                </td>
                <td class="text-right">
                    <a href="{{ route('clients.show', $client) }}" class="btn-secondary sm dx-v2-clients-btn-profile">Ver Perfil</a>
                </td>
            </tr>

            @empty
            <tr>
                <td colspan="5" class="dx-v2-clients-empty-state">
                    No se encontraron clientes que coincidan con la búsqueda.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="dx-v2-clients-pagination">
        {{ $clients->appends(request()->query())->links('vendor.pagination.dx-jump') }}
    </div>
</div>
@endsection
