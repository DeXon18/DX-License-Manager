@extends('layouts.app')

@section('content')
<div class="page-header">
    <div class="header-actions">
        <div>
            <h1 class="page-title">Gestión de Clientes</h1>
            <p class="page-sub text-sm">Visualización y búsqueda de cuentas del ecosistema.</p>
        </div>
        <div class="search-box mt-4" style="width: 300px;">
            <form action="{{ route('clients.index') }}" method="GET" class="input-wrap">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="left: 12px; opacity: 0.5;">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
                <input type="text" name="search" value="{{ request('search') }}" 
                    placeholder="Nombre del cliente..." class="gui-input" style="padding-left: 36px;"
                    x-on:input.debounce.500ms="$el.closest('form').submit()">
            </form>
        </div>
    </div>
</div>

<div class="card">
    <table class="table">
        <thead>
            <tr>
                <th>Cliente</th>
                <th class="text-center">Contratos</th>
                <th class="text-center">Estado</th>
                <th class="text-right">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($clients as $client)
            <tr>
                <td>
                    <div class="font-bold">{{ $client->name }}</div>
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
</div>

<div class="pagination-container">
    {{ $clients->appends(request()->query())->links('vendor.pagination.dx-modern') }}
</div>
@endsection
