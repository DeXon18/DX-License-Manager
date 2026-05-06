@extends('layouts.app')

@section('content')
<div class="page-header">
    <div class="breadcrumb">
        <a href="{{ route('clients.index') }}">Clientes</a>
        <span class="muted">/</span>
        <span class="current">{{ $client->name }}</span>
    </div>
    <h1 class="page-title">{{ $client->name }}</h1>
    <p class="page-sub">Perfil de cuenta y gestión de activos del ecosistema.</p>
</div>

<div x-data="{ tab: 'contracts', modalOpen: false }" class="client-profile">
    <div class="tabs">
        <button class="tab-link" :class="{ 'active': tab === 'contracts' }" @click="tab = 'contracts'">Contratos</button>
        <button class="tab-link" :class="{ 'active': tab === 'licenses' }" @click="tab = 'licenses'">Licencias</button>
        <button class="tab-link" :class="{ 'active': tab === 'contacts' }" @click="tab = 'contacts'">Contactos</button>
        <button class="tab-link" :class="{ 'active': tab === 'certificates' }" @click="tab = 'certificates'">Certificados</button>
    </div>

    <!-- Contratos Tab -->
    <div x-show="tab === 'contracts'" class="tab-content">
        <div class="card">
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
                        <td>
                            @if($contract->vendor->name == 'Siemens')
                                <span class="vendor-chip"><span class="vendor-dot" style="background:var(--siemens)"></span> Siemens</span>
                            @elseif($contract->vendor->name == 'Moldex3D')
                                <span class="vendor-chip"><span class="vendor-dot" style="background:var(--moldex)"></span> Moldex3D</span>
                            @else
                                {{ $contract->vendor->name }}
                            @endif
                        </td>
                        <td>{{ $contract->type_product }}</td>
                        <td class="font-mono">{{ $contract->end_date->format('d/m/Y') }}</td>
                        <td>
                            @php
                                $status = trim($contract->status ?: 'vacio');
                                $statusMap = [
                                    'vacio' => ['label' => 'Sin estado', 'class' => 'badge-muted', 'icon' => 'fa-regular fa-circle-question'],
                                    'Ofertado' => ['label' => 'Ofertado', 'class' => 'badge-info', 'icon' => 'fa-solid fa-file-signature'],
                                    'En negociación' => ['label' => 'En negociación', 'class' => 'badge-primary', 'icon' => 'fa-solid fa-handshake'],
                                    'Aceptado por el cliente' => ['label' => 'Aceptado', 'class' => 'badge-accent', 'icon' => 'fa-solid fa-circle-check'],
                                    'Procesado (M) - Pte fact.' => ['label' => 'Procesado', 'class' => 'badge-warn', 'icon' => 'fa-solid fa-gears'],
                                    'Facturado - Pte proc. (M)' => ['label' => 'Facturado', 'class' => 'badge-warning', 'icon' => 'fa-solid fa-file-invoice-dollar'],
                                    'Cerrado' => ['label' => 'Cerrado', 'class' => 'badge-success', 'icon' => 'fa-solid fa-lock'],
                                    'Baja' => ['label' => 'Baja', 'class' => 'badge-danger', 'icon' => 'fa-solid fa-circle-xmark'],
                                ];
                                $data = $statusMap[$status] ?? $statusMap['vacio'];
                            @endphp
                            <span class="badge {{ $data['class'] }}">
                                <i class="{{ $data['icon'] }}" style="margin-right: 6px; font-size: 10px; opacity: 0.8;"></i>
                                {{ $data['label'] }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-12 muted">No hay contratos registrados para este cliente.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Card Footer Legend -->
            <div class="card-footer-legend">
                <div class="legend-header">
                    <i class="fa-solid fa-circle-info"></i>
                    <span>Leyenda de Estados del Ecosistema</span>
                </div>
                <div class="legend-grid">
                    <div class="legend-item">
                        <span class="badge badge-info"><i class="fa-solid fa-file-signature"></i> Ofertado</span>
                        <span class="legend-label">Propuesta Comercial</span>
                    </div>
                    <div class="legend-item">
                        <span class="badge badge-primary"><i class="fa-solid fa-handshake"></i> Negociación</span>
                        <span class="legend-label">En Trámite</span>
                    </div>
                    <div class="legend-item">
                        <span class="badge badge-accent"><i class="fa-solid fa-circle-check"></i> Aceptado</span>
                        <span class="legend-label">Validado</span>
                    </div>
                    <div class="legend-item">
                        <span class="badge badge-warn"><i class="fa-solid fa-gears"></i> Procesado</span>
                        <span class="legend-label">Pte. Facturación</span>
                    </div>
                    <div class="legend-item">
                        <span class="badge badge-warning"><i class="fa-solid fa-file-invoice-dollar"></i> Facturado</span>
                        <span class="legend-label">En Cobro</span>
                    </div>
                    <div class="legend-item">
                        <span class="badge badge-success"><i class="fa-solid fa-lock"></i> Cerrado</span>
                        <span class="legend-label">Contrato Activo</span>
                    </div>
                    <div class="legend-item">
                        <span class="badge badge-danger"><i class="fa-solid fa-circle-xmark"></i> Baja</span>
                        <span class="legend-label">Finalizado</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Licencias Tab (Placeholder) -->
    <div x-show="tab === 'licenses'" class="tab-content" style="display: none;">
        <div class="card text-center py-16">
            <div style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.5;">🔑</div>
            <h3 style="font-size: 16px; font-weight: 600;">Gestión de Licencias (Sold-To)</h3>
            <p class="muted mt-2">Esta funcionalidad estará disponible en la <strong>Fase 6.2 / 8.1</strong>.</p>
            <div class="badge badge-muted mt-6">Pendiente de Fase correspondiente</div>
        </div>
    </div>

    <!-- Contactos Tab -->
    <div x-show="tab === 'contacts'" class="tab-content" style="display: none;">
        <div class="header-actions mb-6">
            <h2 style="font-size: 16px; font-weight: 600;">Contactos de Referencia</h2>
            @if(Auth::user()->role !== 'viewer')
            <button class="btn-primary sm" @click="modalOpen = true">Añadir Contacto</button>
            @endif
        </div>
        <div class="grid cols-2 gap-4">
            @forelse($client->contacts as $contact)
            <div class="card p-5" style="position: relative;">
                @if(Auth::user()->role !== 'viewer')
                <form action="{{ route('contacts.destroy', $contact) }}" method="POST" style="position: absolute; top: 12px; right: 12px;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="muted" style="background: none; border: none; cursor: pointer; font-size: 14px;" onclick="return confirm('¿Eliminar contacto?')">×</button>
                </form>
                @endif
                <div class="font-bold">{{ $contact->name }}</div>
                <div class="body-sm muted">{{ $contact->position ?? 'Sin cargo' }}</div>
                <div class="mt-4 font-mono body-sm" style="display: flex; flex-direction: column; gap: 4px;">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span style="opacity: 0.5;">📧</span> {{ $contact->email }}
                    </div>
                    @if($contact->phone)
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span style="opacity: 0.5;">📞</span> {{ $contact->phone }}
                    </div>
                    @endif
                </div>
            </div>
            @empty
            <div class="card col-span-2 text-center py-12 muted">
                No hay contactos registrados para este cliente.
            </div>
            @endforelse
        </div>
    </div>

    <!-- Certificados Tab (Placeholder) -->
    <div x-show="tab === 'certificates'" class="tab-content" style="display: none;">
        <div class="card text-center py-16">
            <div style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.5;">📜</div>
            <h3 style="font-size: 16px; font-weight: 600;">Certificados de Cese (CODs)</h3>
            <p class="muted mt-2">El histórico y gestión de certificados firmados estará disponible en la <strong>Fase 8.4</strong>.</p>
            <div class="badge badge-muted mt-6">Pendiente de Fase correspondiente</div>
        </div>
    </div>

    <!-- Modal Añadir Contacto -->
    <template x-teleport="body">
        <div x-show="modalOpen" class="modal-overlay" style="display: none;">
            <div class="modal-content" @click.outside="modalOpen = false">
                <div class="modal-header">
                    <h3 style="font-size: 16px; font-weight: 600;">Añadir Nuevo Contacto</h3>
                    <button class="muted" @click="modalOpen = false" style="background:none; border:none; cursor:pointer; font-size:20px;">×</button>
                </div>
                <form action="{{ route('contacts.store', $client) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group mb-4">
                            <label>Nombre Completo <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="gui-input" placeholder="Ej. Juan Pérez" required style="padding-left: 12px;">
                        </div>
                        <div class="form-group mb-4">
                            <label>Email Corporativo <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="gui-input" placeholder="juan@empresa.com" required style="padding-left: 12px;">
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Cargo</label>
                                <input type="text" name="position" class="gui-input" placeholder="Ej. IT Manager" style="padding-left: 12px;">
                            </div>
                            <div class="form-group">
                                <label>Teléfono</label>
                                <input type="text" name="phone" class="gui-input" placeholder="+34..." style="padding-left: 12px;">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn-secondary sm" @click="modalOpen = false">Cancelar</button>
                        <button type="submit" class="btn-primary sm">Guardar Contacto</button>
                    </div>
                </form>
            </div>
        </div>
    </template>
</div>
@endsection

@push('styles')
<style>
    .tabs { display: flex; border-bottom: 1px solid var(--border); margin-bottom: 32px; gap: 8px; }
    .tab-link { 
        padding: 12px 20px; border: none; background: none; cursor: pointer;
        color: var(--muted); font-size: 13px; font-weight: 600; border-bottom: 2px solid transparent;
        transition: all 0.2s;
    }
    .tab-link:hover { color: var(--secondary); }
    .tab-link.active { color: var(--accent); border-bottom-color: var(--accent); }
    
    .client-profile .card { padding: 0; }
    .client-profile .card.p-5 { padding: 20px; }

    /* Legend Styles */
    .card-footer-legend {
        background: var(--bg);
        border-top: 1px solid var(--border);
        padding: 16px 20px;
    }
    .legend-header {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 12px;
        color: var(--muted);
    }
    .legend-header i { font-size: 10px; }
    .legend-header span {
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.1em;
    }
    .legend-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 12px 24px;
    }
    .legend-item {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .legend-item .badge {
        font-size: 9px;
        padding: 1px 6px;
    }
    .legend-label {
        font-size: 10px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--muted);
    }
</style>
@endpush
