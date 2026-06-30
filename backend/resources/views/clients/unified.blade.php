@extends('layouts.app')

@section('content')
<div class="dx-v2-page-header">
    <div>
        <div class="breadcrumb">
            <a href="{{ route('clients.index') }}">Inventario</a>
            <span class="separator">/</span>
            <a href="{{ route('clients.index') }}">Directorio</a>
            <span class="separator">/</span>
            <span class="current">Licencias Unificadas</span>
        </div>
        <h1 class="page-title">Licencias <span>Unificadas</span></h1>
        <p class="page-subtitle">Auditoría de clientes con licencias que agrupan múltiples Sold-Tos (Other Installs).</p>
    </div>
    <div class="dx-v2-page-header-actions" style="align-items: center;">
        <a href="{{ route('clients.index') }}" class="btn-secondary">
            <i class="fa-solid fa-arrow-left" style="margin-right: 8px;"></i> Volver al Directorio
        </a>
    </div>
</div>

<div class="card">
    <table class="table">
        <thead>
            <tr>
                <th>Cliente</th>
                <th class="text-center">Daemon</th>
                <th class="text-center">Sold-To Principal</th>
                <th>Sold-Tos Unificados (Other Installs)</th>
                <th class="text-right">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($daemons as $daemon)
            <tr>
                <td>
                    <div class="dx-v2-clients-flex-align">
                        <div class="font-bold">{{ $daemon->client->name ?? 'Cliente Desconocido' }}</div>
                    </div>
                </td>
                <td class="text-center">
                    @if($daemon->vendor === 'siemens')
                        <span class="badge dx-v2-clients-vendor-badge siemens" style="display: inline-flex; justify-content: center;">
                            <span class="dx-v2-clients-badge-sub" style="margin:0;">{{ strtoupper($daemon->daemon) }}</span>
                        </span>
                    @else
                        <span class="badge dx-v2-clients-vendor-badge moldex" style="display: inline-flex; justify-content: center;">
                            <span class="dx-v2-clients-badge-sub" style="margin:0;">{{ strtoupper($daemon->daemon) }}</span>
                        </span>
                    @endif
                </td>
                <td class="text-center font-mono body-sm" style="color: var(--dx-v2-accent);">
                    {{ $daemon->sold_to }}
                </td>
                <td>
                    <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                        @foreach($daemon->additional_sold_tos as $soldTo)
                            <div class="dx-v2-unified-badge" style="padding: 4px 8px; font-size: 12px; display: flex; align-items: center; gap: 6px; cursor: default;">
                                <i class="fa-solid fa-layer-group" style="opacity: 0.7;"></i>
                                <span class="font-mono">{{ $soldTo }}</span>
                            </div>
                        @endforeach
                    </div>
                </td>
                <td class="text-right">
                    @if($daemon->client)
                        <a href="{{ route('clients.show', $daemon->client) }}" class="btn-secondary sm dx-v2-clients-btn-profile">Ver Ficha</a>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="dx-v2-clients-empty-state">
                    No se encontraron licencias unificadas en el inventario.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    
    @if($daemons->hasPages())
    <div class="dx-v2-clients-pagination">
        {{ $daemons->links('vendor.pagination.dx-jump') }}
    </div>
    @endif
</div>
@endsection
