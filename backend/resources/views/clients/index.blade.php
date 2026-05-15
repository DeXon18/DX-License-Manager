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
                @php $hasInv = session('client_has_inventory', false); @endphp
                
                <div class="premium-switch-container">
                    <a href="{{ $hasInv ? route('clients.index', array_merge(request()->except('has_inventory'), ['clear_inventory' => 1])) : route('clients.index', array_merge(request()->all(), ['has_inventory' => 1])) }}" 
                       class="premium-switch {{ $hasInv ? 'active' : '' }}"
                       title="Filtrar clientes con inventario activo">
                        <div class="switch-track">
                            <div class="switch-knob">
                                <i class="fa-solid fa-sliders"></i>
                            </div>
                        </div>
                        <span class="switch-text">Solo con Licencias</span>
                    </a>
                </div>

                <style>
                    .premium-switch-container {
                        display: flex;
                        align-items: center;
                    }
                    .premium-switch {
                        display: flex;
                        align-items: center;
                        gap: 12px;
                        text-decoration: none;
                        padding: 6px 16px 6px 6px;
                        background: var(--surface);
                        border: 1px solid var(--border);
                        border-radius: 6px;
                        transition: all 0.2s ease;
                        user-select: none;
                    }
                    .switch-track {
                        width: 40px;
                        height: 20px;
                        background: var(--bg);
                        border-radius: 4px;
                        position: relative;
                        border: 1px solid var(--border);
                        transition: all 0.2s ease;
                        overflow: hidden;
                    }
                    .switch-knob {
                        position: absolute;
                        top: 2px;
                        left: 2px;
                        width: 14px;
                        height: 14px;
                        background: var(--surface);
                        border-radius: 3px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        box-shadow: 0 1px 2px rgba(0,0,0,0.1);
                        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
                        border: 1px solid var(--border);
                    }
                    .switch-knob i {
                        font-size: 8px;
                        color: var(--muted);
                        transition: all 0.2s ease;
                    }
                    .switch-text {
                        font-size: 11px;
                        font-weight: 700;
                        color: var(--muted);
                        text-transform: uppercase;
                        letter-spacing: 0.05em;
                        transition: all 0.2s ease;
                    }

                    .premium-switch.active {
                        border-color: var(--accent);
                        background: var(--surface);
                    }
                    .premium-switch.active .switch-track {
                        background: var(--accent);
                        border-color: var(--accent);
                    }
                    .premium-switch.active .switch-knob {
                        transform: translateX(20px);
                        background: white;
                        border-color: white;
                    }
                    .premium-switch.active .switch-knob i {
                        color: var(--accent);
                    }
                    .premium-switch.active .switch-text {
                        color: var(--primary);
                    }

                    .premium-switch:hover {
                        border-color: var(--accent);
                        background: var(--bg);
                    }

                    /* Vendor colors */
                    :root {
                        --siemens-muted: rgba(0, 153, 153, 0.1);
                        --siemens-dark: #008080;
                        --siemens-border: rgba(0, 153, 153, 0.2);
                        
                        --moldex-muted: rgba(237, 28, 36, 0.1);
                        --moldex-dark: #C4121A;
                        --moldex-border: rgba(237, 28, 36, 0.2);
                    }

                    [data-theme="dark"] {
                        --siemens-muted: rgba(42, 161, 152, 0.1);
                        --siemens-dark: #2AA198;
                        --siemens-border: rgba(42, 161, 152, 0.3);

                        --moldex-muted: rgba(224, 82, 82, 0.1);
                        --moldex-dark: #E05252;
                        --moldex-border: rgba(224, 82, 82, 0.3);
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
