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
    <div class="dx-v2-page-header-actions dx-v2-unified-actions">
        <a href="{{ route('clients.index') }}" class="btn-secondary">
            <i class="fa-solid fa-arrow-left"></i> Volver al Directorio
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
                        <span class="badge dx-v2-clients-vendor-badge siemens dx-v2-unified-daemon-badge">
                            {{ strtoupper($daemon->daemon) }}
                        </span>
                    @else
                        <span class="badge dx-v2-clients-vendor-badge moldex dx-v2-unified-daemon-badge">
                            {{ strtoupper($daemon->daemon) }}
                        </span>
                    @endif
                </td>
                <td class="text-center font-mono body-sm dx-v2-unified-sold-to">
                    {{ $daemon->sold_to }}
                </td>
                <td>
                    <div class="dx-v2-unified-install-list">
                        @foreach($daemon->additional_sold_tos as $soldTo)
                            <div class="dx-v2-unified-badge">
                                <i class="fa-solid fa-link"></i>
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
