@extends('layouts.app')

@section('content')
<div class="header-actions">
    <h1>Gestión de Clientes</h1>
    <div class="search-box">
        <form action="{{ route('clients.index') }}" method="GET">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar cliente..." class="input">
            <button type="submit" class="btn primary">Buscar</button>
        </form>
    </div>
</div>

<div class="card p-0">
    <table class="table">
        <thead>
            <tr>
                <th>Cliente</th>
                <th>Contratos Activos</th>
                <th>Estado Global</th>
                <th class="text-right">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($clients as $client)
            <tr>
                <td class="font-bold">{{ $client->name }}</td>
                <td class="font-mono text-center">{{ $client->contracts_count }}</td>
                <td>
                    @if($client->contracts_count > 0)
                        <span class="badge success">Activo</span>
                    @else
                        <span class="badge neutral">Sin Contratos</span>
                    @endif
                </td>
                <td class="text-right">
                    <a href="{{ route('clients.show', $client) }}" class="btn secondary sm">Ver Perfil</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="pagination">
    {{ $clients->links() }}
</div>
@endsection
