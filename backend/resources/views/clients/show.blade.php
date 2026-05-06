@extends('layouts.app')

@section('content')
<div class="header-actions">
    <div class="breadcrumb">
        <a href="{{ route('clients.index') }}">Clientes</a> / <span>{{ $client->name }}</span>
    </div>
    <h1>{{ $client->name }}</h1>
</div>

<div x-data="{ tab: 'contracts' }" class="client-profile">
    <div class="tabs">
        <button class="tab-link" :class="{ 'active': tab === 'contracts' }" @click="tab = 'contracts'">Contratos</button>
        <button class="tab-link" :class="{ 'active': tab === 'licenses' }" @click="tab = 'licenses'">Licencias</button>
        <button class="tab-link" :class="{ 'active': tab === 'contacts' }" @click="tab = 'contacts'">Contactos</button>
        <button class="tab-link" :class="{ 'active': tab === 'certificates' }" @click="tab = 'certificates'">Certificados</button>
    </div>

    <!-- Contratos Tab -->
    <div x-show="tab === 'contracts'" class="tab-content">
        <div class="card p-0">
            <table class="table">
                <thead>
                    <tr>
                        <th>ContraHeader</th>
                        <th>Vendor</th>
                        <th>Producto</th>
                        <th>Fin Contrato</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($client->contracts as $contract)
                    <tr>
                        <td class="font-mono">{{ $contract->contract_number }}</td>
                        <td>{{ $contract->vendor->name ?? 'N/A' }}</td>
                        <td>{{ $contract->type_product }}</td>
                        <td class="font-mono">{{ $contract->end_date->format('d/m/Y') }}</td>
                        <td>
                            @php
                                $statusClass = match($contract->status) {
                                    'Cerrado' => 'success',
                                    'Baja' => 'danger',
                                    default => 'warning'
                                };
                            @endphp
                            <span class="badge {{ $statusClass }}">{{ $contract->status }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-8 muted">No hay contratos registrados.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Licencias Tab (Placeholder) -->
    <div x-show="tab === 'licenses'" class="tab-content" style="display: none;">
        <div class="card text-center py-16">
            <div class="sidebar-icon" style="font-size: 3rem; margin-bottom: 1rem;">🔑</div>
            <h3>Gestión de Licencias (Sold-To)</h3>
            <p class="muted">Esta funcionalidad estará disponible en la <strong>Fase 6.2 / 8.1</strong>.</p>
            <div class="badge neutral mt-4">Pendiente de Fase correspondiente</div>
        </div>
    </div>

    <!-- Contactos Tab -->
    <div x-show="tab === 'contacts'" class="tab-content" style="display: none;">
        <div class="header-actions">
            <h2>Contactos de Referencia</h2>
            @if(Auth::user()->role !== 'viewer')
            <button class="btn primary sm">Añadir Contacto</button>
            @endif
        </div>
        <div class="grid cols-2 gap-4">
            @forelse($client->contacts as $contact)
            <div class="card">
                <div class="font-bold">{{ $contact->name }}</div>
                <div class="body-sm muted">{{ $contact->position ?? 'Sin cargo' }}</div>
                <div class="mt-4 font-mono body-sm">
                    <div>📧 {{ $contact->email }}</div>
                    @if($contact->phone)
                    <div>📞 {{ $contact->phone }}</div>
                    @endif
                </div>
            </div>
            @empty
            <div class="card col-span-2 text-center py-8 muted">
                No hay contactos registrados para este cliente.
            </div>
            @endforelse
        </div>
    </div>

    <!-- Certificados Tab (Placeholder) -->
    <div x-show="tab === 'certificates'" class="tab-content" style="display: none;">
        <div class="card text-center py-16">
            <div class="sidebar-icon" style="font-size: 3rem; margin-bottom: 1rem;">📜</div>
            <h3>Certificados de Cese (CODs)</h3>
            <p class="muted">El histórico y gestión de certificados firmados estará disponible en la <strong>Fase 8.4</strong>.</p>
            <div class="badge neutral mt-4">Pendiente de Fase correspondiente</div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .tabs { display: flex; border-bottom: 1px solid var(--border); margin-bottom: 24px; gap: 8px; }
    .tab-link { 
        padding: 12px 20px; border: none; background: none; cursor: pointer;
        color: var(--muted); font-weight: 600; border-bottom: 2px solid transparent;
        transition: all 0.2s;
    }
    .tab-link:hover { color: var(--primary); }
    .tab-link.active { color: var(--accent); border-bottom-color: var(--accent); }
    .header-actions h2 { margin: 0; }
</style>
@endpush
