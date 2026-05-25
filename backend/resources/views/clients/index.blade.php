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
