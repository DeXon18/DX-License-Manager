@extends('layouts.app')

@section('content')
<div class="page-header">
    <div class="header-actions">
        <div>
            <h1 class="page-title">Gestión de Clientes</h1>
            <p class="page-sub text-sm">Visualización y búsqueda de cuentas del ecosistema.</p>
        </div>
        <div class="search-box mt-4" style="width: 450px; display: flex; align-items: center; gap: 15px;">
            <form action="{{ route('clients.index') }}" method="GET" class="input-wrap" style="flex: 1;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="left: 12px; opacity: 0.5;">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
                <input type="text" name="search" value="{{ request('search') }}" 
                    placeholder="Nombre del cliente..." class="gui-input" style="padding-left: 36px;"
                    x-on:input.debounce.500ms="$el.closest('form').submit()">
                @if(request('has_inventory'))
                    <input type="hidden" name="has_inventory" value="1">
                @endif
            </form>

            <div style="display: flex; align-items: center; gap: 8px;">
                <label class="text-xs muted" style="white-space: nowrap; cursor: pointer;" x-data="{ active: {{ request('has_inventory') ? 'true' : 'false' }} }" x-on:click="window.location.href = active ? '{{ route('clients.index', array_merge(request()->except('has_inventory'))) }}' : '{{ route('clients.index', array_merge(request()->all(), ['has_inventory' => 1])) }}'">
                    <span :class="active ? 'text-primary font-bold' : ''">Con Licencias</span>
                    <div style="width: 34px; height: 18px; border-radius: 20px; position: relative; transition: all 0.3s;" :style="active ? 'background: var(--primary);' : 'background: var(--surface); border: 1px solid var(--border);'">
                        <div style="width: 14px; height: 14px; background: white; border-radius: 50%; position: absolute; top: 2px; transition: all 0.3s;" :style="active ? 'left: 18px;' : 'left: 2px;'"></div>
                    </div>
                </label>
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
                                <i class="fa-solid fa-certificate" style="font-size: 10px;"></i>
                            </span>
                        @endif
                    </div>
                </td>
                <td class="text-center">
                    @if($client->inventory_daemons_count > 0)
                        <span class="body-sm font-mono" style="background: rgba(255, 170, 0, 0.1); color: var(--warning); padding: 2px 8px; border-radius: 4px; border: 1px solid rgba(255, 170, 0, 0.2);">
                            {{ $client->inventory_daemons_count }} <span class="text-xs" style="opacity: 0.6;">Sold-To</span>
                        </span>
                    @else
                        <span class="muted text-xs">—</span>
                    @endif
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
