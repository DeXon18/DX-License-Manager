@extends('layouts.app')

@section('content')
<div class="page-header">
    <div class="header-actions">
        <div>
            <h1 class="page-title">Gestión de Clientes</h1>
            <p class="page-sub text-sm">Visualización y búsqueda de cuentas del ecosistema.</p>
        </div>
        <div class="search-box mt-4" style="width: 450px; display: flex; align-items: center; gap: 15px;">
            <form action="{{ route('clients.index') }}" method="GET" style="position: relative; flex: 1; max-width: 400px;">
                <svg style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--muted); width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <input type="text" name="search" value="{{ request('search') }}" 
                    placeholder="Nombre del cliente..." class="gui-input" style="padding-left: 36px;"
                    x-on:input.debounce.500ms="$el.closest('form').submit()">
            </form>

            <div class="filter-actions" style="display: flex; align-items: center; gap: 12px;">
                @php 
                    $hasInv = session('client_has_inventory', false); 
                    $currentVendor = session('client_inventory_vendor', 'all');
                @endphp
                
                <div class="inventory-filter-group" style="margin-left: auto;">
                    <div class="premium-segmented-control">
                        <!-- OFF -->
                        <a href="{{ route('clients.index', array_merge(request()->except('has_inventory'), ['clear_inventory' => 1])) }}" 
                           class="seg-item {{ !$hasInv ? 'active off' : '' }}" title="Desactivar filtros">
                            <div class="seg-icon"><i class="fa-solid fa-ban"></i></div>
                            <span class="seg-text">OFF</span>
                        </a>
                        
                        <!-- ALL -->
                        <a href="{{ route('clients.index', array_merge(request()->all(), ['has_inventory' => 1, 'vendor_filter' => 'all'])) }}" 
                           class="seg-item {{ $hasInv && $currentVendor === 'all' ? 'active all' : '' }}" title="Todos los vendors">
                            <div class="seg-icon"><i class="fa-solid fa-layer-group"></i></div>
                            <span class="seg-text">ALL</span>
                        </a>

                        <!-- SIEMENS -->
                        <a href="{{ route('clients.index', array_merge(request()->all(), ['has_inventory' => 1, 'vendor_filter' => 'siemens'])) }}" 
                           class="seg-item {{ $hasInv && $currentVendor === 'siemens' ? 'active siemens' : '' }}" title="Solo Siemens">
                            <div class="seg-icon"><i class="fa-solid fa-microchip"></i></div>
                            <span class="seg-text">Siemens</span>
                        </a>

                        <!-- MOLDEX -->
                        <a href="{{ route('clients.index', array_merge(request()->all(), ['has_inventory' => 1, 'vendor_filter' => 'moldex'])) }}" 
                           class="seg-item {{ $hasInv && $currentVendor === 'moldex' ? 'active moldex' : '' }}" title="Solo Moldex3D">
                            <div class="seg-icon"><i class="fa-solid fa-cube"></i></div>
                            <span class="seg-text">Moldex</span>
                        </a>
                    </div>
                </div>

                <style>
                    .inventory-filter-group {
                        display: flex;
                        align-items: center;
                    }
                    .premium-segmented-control {
                        display: flex;
                        background: rgba(255, 255, 255, 0.02);
                        border: 1px solid var(--border);
                        border-radius: 10px;
                        padding: 3px;
                        gap: 3px;
                        box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);
                        backdrop-filter: blur(10px);
                    }
                    .seg-item {
                        display: flex;
                        align-items: center;
                        gap: 8px;
                        padding: 6px 14px;
                        border-radius: 8px;
                        color: var(--muted);
                        text-decoration: none;
                        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                        border: 1px solid transparent;
                    }
                    .seg-icon {
                        font-size: 11px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        transition: transform 0.3s ease;
                    }
                    .seg-text {
                        font-size: 10px;
                        font-weight: 700;
                        text-transform: uppercase;
                        letter-spacing: 0.05em;
                    }
                    
                    .seg-item:hover {
                        color: var(--text);
                        background: rgba(255, 255, 255, 0.05);
                    }
                    .seg-item:hover .seg-icon {
                        transform: translateY(-1px);
                    }

                    .seg-item.active {
                        background: var(--surface);
                        color: var(--primary);
                        border-color: var(--border);
                        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
                    }

                    /* Active States — Color Coding */
                    .seg-item.active.off {
                        border-color: rgba(255, 255, 255, 0.1);
                        background: rgba(255, 255, 255, 0.05);
                        color: var(--muted);
                    }
                    .seg-item.active.all {
                        color: var(--accent);
                        background: var(--accent-muted);
                        border-color: rgba(0, 153, 153, 0.3);
                    }
                    .seg-item.active.siemens {
                        color: var(--siemens-dark);
                        background: var(--siemens-muted);
                        border-color: var(--siemens-border);
                    }
                    .seg-item.active.moldex {
                        color: var(--moldex-dark);
                        background: var(--moldex-muted);
                        border-color: var(--moldex-border);
                    }

                    /* Vendors color definitions */
                    :root {
                        --siemens-muted: rgba(0, 153, 153, 0.1);
                        --siemens-dark: #009999;
                        --siemens-border: rgba(0, 153, 153, 0.3);
                        --moldex-muted: rgba(237, 28, 36, 0.1);
                        --moldex-dark: #ED1C24;
                        --moldex-border: rgba(237, 28, 36, 0.3);
                    }

                    [data-theme="dark"] {
                        --siemens-dark: #2AA198;
                        --siemens-muted: rgba(42, 161, 152, 0.1);
                        --siemens-border: rgba(42, 161, 152, 0.4);
                        --moldex-dark: #E05252;
                        --moldex-muted: rgba(224, 82, 82, 0.1);
                        --moldex-border: rgba(224, 82, 82, 0.4);
                    }
                </style>
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
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <div class="font-bold">{{ $client->name }}</div>
                        @if($client->inventory_daemons_count > 0)
                            <span title="Licencias Detectadas" style="color: var(--warning); display: flex; align-items: center;" class="pulse-soft">
                                <i class="fa-solid fa-database" style="font-size: 10px;"></i>
                            </span>
                        @endif
                    </div>
                </td>
                <td class="text-center">
                    <div style="display: flex; flex-direction: column; align-items: center; gap: 4px;">
                        @if($client->siemens_daemons_count > 0)
                            <span class="badge font-mono" style="background: var(--siemens-muted); color: var(--siemens-dark); border: 1px solid var(--siemens-border); padding: 2px 8px; font-weight: 700; width: 100px; display: flex; justify-content: space-between;">
                                <span>{{ $client->siemens_daemons_count }}</span>
                                <span style="font-size: 8px; opacity: 0.8; font-weight: 400;">Siemens</span>
                            </span>
                        @endif
                        @if($client->moldex_daemons_count > 0)
                            <span class="badge font-mono" style="background: var(--moldex-muted); color: var(--moldex-dark); border: 1px solid var(--moldex-border); padding: 2px 8px; font-weight: 700; width: 100px; display: flex; justify-content: space-between;">
                                <span>{{ $client->moldex_daemons_count }}</span>
                                <span style="font-size: 8px; opacity: 0.8; font-weight: 400;">Moldex3D</span>
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
                    <a href="{{ route('clients.show', $client) }}" class="btn-secondary sm" style="padding: 5px 12px; font-size: 12px;">Ver Perfil</a>
                </td>
            </tr>

            @empty
            <tr>
                <td colspan="4" class="text-center py-12 muted">
                    No se encontraron clientes que coincidan con la búsqueda.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div style="padding: 15px 20px; border-top: 1px solid var(--border); background: rgba(255,255,255,0.01);">
        {{ $clients->appends(request()->query())->links('vendor.pagination.dx-jump') }}
    </div>
</div>
@endsection
