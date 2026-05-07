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

<div x-data="{ 
    tab: localStorage.getItem('activeTab') || '{{ request('tab', 'contracts') }}',
    auditDetail: null,
    setTab(name) {
        this.tab = name;
        localStorage.setItem('activeTab', name);
    }
}" class="client-profile">
    <div class="tabs">
        <button class="tab-link" :class="{ 'active': tab === 'contracts' }" @click="setTab('contracts')">Contratos</button>
        <button class="tab-link" :class="{ 'active': tab === 'licenses' }" @click="setTab('licenses')">Licencias</button>
        <button class="tab-link" :class="{ 'active': tab === 'contacts' }" @click="setTab('contacts')">Contactos</button>
        <button class="tab-link" :class="{ 'active': tab === 'certificates' }" @click="setTab('certificates')">Certificados</button>
    </div>

    <!-- Contratos Tab -->
    <div x-show="tab === 'contracts'" class="tab-content">
        <div class="card">
            <table class="table text-sm">
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
                        <td class="font-bold">{{ $contract->contract_number }}</td>
                        <td class="font-bold">
                            @if($contract->vendor->name == 'Siemens')
                                <span class="vendor-chip"><span class="vendor-dot" style="background:var(--siemens)"></span> Siemens</span>
                            @elseif($contract->vendor->name == 'Moldex3D')
                                <span class="vendor-chip"><span class="vendor-dot" style="background:var(--moldex)"></span> Moldex3D</span>
                            @else
                                {{ $contract->vendor->name }}
                            @endif
                        </td>
                        <td class="font-bold">{{ $contract->type_product }}</td>
                        <td class="font-bold">{{ $contract->end_date->format('d/m/Y') }}</td>
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
                    </div>
                    <div class="legend-item">
                        <span class="badge badge-primary"><i class="fa-solid fa-handshake"></i> Negociación</span>
                    </div>
                    <div class="legend-item">
                        <span class="badge badge-accent"><i class="fa-solid fa-circle-check"></i> Aceptado</span>
                    </div>
                    <div class="legend-item">
                        <span class="badge badge-warn"><i class="fa-solid fa-gears"></i> Procesado</span>
                    </div>
                    <div class="legend-item">
                        <span class="badge badge-warning"><i class="fa-solid fa-file-invoice-dollar"></i> Facturado</span>
                    </div>
                    <div class="legend-item">
                        <span class="badge badge-success"><i class="fa-solid fa-lock"></i> Cerrado</span>
                    </div>
                    <div class="legend-item">
                        <span class="badge badge-danger"><i class="fa-solid fa-circle-xmark"></i> Baja</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Licencias Tab (Motor IA Activado) -->
    <div x-show="tab === 'licenses'" class="tab-content" style="display: none;">
        @if($client->auditResults->count() > 0)
            <div class="card">
                <div class="card-header flex justify-between items-center">
                    <h3 class="text-sm font-bold uppercase tracking-wider">Historial de Auditorías IA</h3>
                    <span class="badge badge-accent">{{ $client->auditResults->count() }} Archivos</span>
                </div>
                <table class="table text-sm">
                    <thead>
                        <tr>
                            <th>Sold-To</th>
                            <th>Fecha</th>
                            <th>Estado</th>
                            <th>Productos Detectados</th>
                            <th class="text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($client->auditResults as $result)
                        <tr>
                            <td class="font-bold">{{ $result->sold_to ?? 'Pendiente' }}</td>
                            <td>{{ $result->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                @if($result->status === 'completed')
                                    <span class="badge badge-success">Auditado</span>
                                @elseif($result->status === 'processing')
                                    <span class="badge badge-warn animate-pulse">Procesando...</span>
                                @else
                                    <span class="badge badge-danger">Error</span>
                                @endif
                            </td>
                            <td>
                                @if($result->status === 'completed' && isset($result->results['products']))
                                    <div class="flex flex-wrap gap-1">
                                        @foreach(array_slice($result->results['products'], 0, 3) as $product)
                                            <span class="product-chip">
                                                {{ $product['product_code'] ?? ($product['name'] ?? 'N/A') }} ({{ $product['quantity'] ?? ($product['qty'] ?? 0) }})
                                            </span>
                                        @endforeach
                                        @if(count($result->results['products']) > 3)
                                            <span class="product-chip muted">+{{ count($result->results['products']) - 3 }} más</span>
                                        @endif
                                    </div>
                                @else
                                    <span class="muted">—</span>
                                @endif
                            </td>
                            <td class="text-right">
                                <button class="btn-icon" title="Ver Detalle Auditoría" @click="auditDetail = @js($result); $dispatch('open-audit-modal')">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="card text-center py-20 border-dashed opacity-80" style="border: 2px dashed var(--border); background: transparent;">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full mb-6" style="background: rgba(var(--accent-rgb, 0, 122, 255), 0.1); margin: 0 auto 24px;">
                    <i class="fa-solid fa-microchip text-accent" style="font-size: 24px; color: var(--accent);"></i>
                </div>
                <h3 class="text-xl font-bold tracking-tight">Gestión de Licencias</h3>
                <p class="muted mt-3 max-w-md mx-auto" style="margin-left: auto; margin-right: auto;">
                    No se han encontrado auditorías realizadas para este cliente. 
                    Suba una licencia en <strong>Herramientas > NX Suite</strong> para iniciar el proceso.
                </p>
                <div class="flex justify-center gap-3 mt-8" style="display: flex; justify-content: center; gap: 12px; margin-top: 32px;">
                    <span class="badge badge-accent">Motor Activo</span>
                    <span class="badge badge-muted">Fase 8.1 — Siemens Suite</span>
                </div>
            </div>
        @endif
    </div>

    <!-- Certificados Tab (Pendiente Fase 8.4) -->
    <div x-show="tab === 'certificates'" class="tab-content" style="display: none;">
        <div class="card text-center py-20 border-dashed opacity-80" style="border: 2px dashed var(--border); background: transparent;">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full mb-6" style="background: rgba(var(--accent-rgb, 0, 122, 255), 0.1); margin: 0 auto 24px;">
                <i class="fa-solid fa-file-contract text-accent" style="font-size: 24px; color: var(--accent);"></i>
            </div>
            <h3 class="text-xl font-bold tracking-tight">Certificados de Cese (COD)</h3>
            <p class="muted mt-3 max-w-md mx-auto" style="margin-left: auto; margin-right: auto;">
                El historial de certificados firmados y la generación de nuevos documentos 
                se activará con el módulo de gestión documental de la <strong>Fase 8.4</strong>.
            </p>
            <div class="flex justify-center gap-3 mt-8" style="display: flex; justify-content: center; gap: 12px; margin-top: 32px;">
                <span class="badge badge-accent">En Planificación</span>
                <span class="badge badge-muted">Fase 8.4 — Siemens COD</span>
            </div>
        </div>
    </div>

    <!-- Contactos Tab -->
    <div x-show="tab === 'contacts'" class="tab-content" style="display: none;">
        <div class="card p-0">
            <div class="card-header flex justify-between items-center px-5 py-4">
                <h3 class="text-sm font-bold uppercase tracking-wider">Personas de Contacto</h3>
                <button class="btn-primary sm" @click="$dispatch('open-contact-modal')">
                    <i class="fa-solid fa-plus mr-2"></i> Nuevo Contacto
                </button>
            </div>
            <table class="table text-sm">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Cargo</th>
                        <th class="text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($client->contacts as $contact)
                    <tr>
                        <td class="font-bold">{{ $contact->name }}</td>
                        <td>{{ $contact->email }}</td>
                        <td>
                            @if($contact->position)
                                <span class="badge badge-muted">{{ $contact->position }}</span>
                            @else
                                <span class="muted">—</span>
                            @endif
                        </td>
                        <td class="text-right" style="width: 100px;">
                            <div style="display: flex; justify-content: flex-end; align-items: center; gap: 4px; white-space: nowrap;">
                                <button class="btn-icon" 
                                    @click="$dispatch('open-contact-modal', { 
                                        id: {{ $contact->id }}, 
                                        name: '{{ $contact->name }}', 
                                        email: '{{ $contact->email }}', 
                                        position: '{{ $contact->position }}',
                                        phone: '{{ $contact->phone }}'
                                    })">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                                <form action="{{ route('contacts.destroy', [$client, $contact]) }}" method="POST" onsubmit="return confirm('¿Eliminar este contacto?')" style="display: inline-block; margin: 0;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-icon text-danger">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-12 muted">
                            No hay contactos registrados para este cliente.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Contact Modal -->
    <template x-teleport="body">
        <div x-data="{ 
                open: false, 
                editMode: false,
                action: '{{ route('contacts.store', $client) }}',
                form: { id: '', name: '', email: '', position: '', phone: '' }
            }"
            x-show="open"
            @open-contact-modal.window="
                open = true; 
                if($event.detail.id) {
                    editMode = true;
                    action = '{{ url('/clientes/' . $client->id . '/contactos') }}/' + $event.detail.id;
                    form = $event.detail;
                } else {
                    editMode = false;
                    action = '{{ route('contacts.store', $client) }}';
                    form = { id: '', name: '', email: '', position: '', phone: '' };
                }
            "
            class="modal-overlay"
            style="display: none;"
        >
            <div class="modal-content" @click.outside="open = false">
                <div class="modal-header">
                    <h3 x-text="editMode ? 'Editar Contacto' : 'Nuevo Contacto'"></h3>
                    <button @click="open = false" class="close-btn">&times;</button>
                </div>
                <form :action="action" method="POST">
                    @csrf
                    <template x-if="editMode">
                        <input type="hidden" name="_method" value="PUT">
                    </template>
                    
                    <div class="modal-body space-y-5">
                        <div class="grid grid-cols-2 gap-6">
                            <div class="input-group">
                                <label>Nombre Completo</label>
                                <input type="text" name="name" x-model="form.name" required class="gui-input w-full" placeholder="Ej. Juan Pérez">
                            </div>
                            <div class="input-group">
                                <label>Email Corporativo</label>
                                <input type="email" name="email" x-model="form.email" required class="gui-input w-full" placeholder="email@empresa.com">
                            </div>
                        </div>
                        <div class="input-group">
                            <label>Cargo / Departamento</label>
                            <input type="text" name="position" x-model="form.position" class="gui-input w-full" placeholder="Ej. IT Manager">
                        </div>
                        <div class="input-group">
                            <label>Teléfono (Opcional)</label>
                            <input type="text" name="phone" x-model="form.phone" class="gui-input w-full" placeholder="+34 ...">
                        </div>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" @click="open = false" class="btn-secondary">Cancelar</button>
                        <button type="submit" class="btn-primary" x-text="editMode ? 'Guardar Cambios' : 'Crear Contacto'"></button>
                    </div>
                </form>
            </div>
        </div>
    <!-- Audit Detail Modal -->
    <template x-teleport="body">
        <div x-data="{ open: false }"
            x-show="open"
            @open-audit-modal.window="open = true"
            class="modal-overlay"
            style="z-index: 1100;"
            x-cloak
        >
            <div class="modal-content audit-modal" @click.outside="open = false" style="max-width: 900px; background: #0f111a; border-color: #1e2235;">
                <div class="modal-header" style="border-bottom: none; padding-bottom: 0;">
                    <div class="flex items-center gap-4">
                        <div class="audit-icon-box">
                            <i class="fa-solid fa-shield-halved"></i>
                        </div>
                        <div>
                            <h3 style="margin-bottom: 4px; color: #fff;">Detalle de Auditoría Siemens</h3>
                            <span class="text-xs muted uppercase tracking-widest font-bold">Analizado por Motor FallbackChain v2.1</span>
                        </div>
                    </div>
                    <button @click="open = false" class="close-btn">&times;</button>
                </div>

                <div class="modal-body p-8" x-show="auditDetail">
                    <!-- Top Info Cards -->
                    <div class="audit-header-grid">
                        <div class="audit-info-card">
                            <span class="label">Account / Sold-To</span>
                            <span class="value" x-text="auditDetail.sold_to || 'N/A'"></span>
                        </div>
                        <div class="audit-info-card">
                            <span class="label">Ecosistema / Daemon</span>
                            <div class="flex items-center gap-2">
                                <span class="value daemon" x-text="auditDetail.results?.daemon || 'ugslmd'"></span>
                                <span class="badge badge-accent sm">SIEMENS</span>
                            </div>
                        </div>
                        <div class="audit-info-card" style="grid-column: span 2;">
                            <span class="label">Servidor / Hostname</span>
                            <div class="flex items-baseline gap-3">
                                <span class="value hostname" x-text="auditDetail.results?.hostname || 'PENDIENTE'"></span>
                                <span class="text-xs font-mono" style="color: var(--accent)" x-text="auditDetail.results?.composite ? 'Composite: ' + auditDetail.results.composite : ''"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Unified Sold-Tos -->
                    <div class="unified-box mt-6" x-show="auditDetail.results?.unified_sold_tos?.length">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-link text-warn" style="font-size: 10px;"></i>
                            <span class="label">Sold-Tos Unificados:</span>
                            <div class="flex flex-wrap gap-2">
                                <template x-for="st in auditDetail.results?.unified_sold_tos">
                                    <span class="badge badge-muted sm" x-text="st"></span>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Products Table -->
                    <div class="mt-10">
                        <h4 class="section-title">Desglose de Productos y Expiración</h4>
                        <div class="audit-table-wrapper mt-4">
                            <table class="audit-table">
                                <thead>
                                    <tr>
                                        <th>Producto</th>
                                        <th>Descripción</th>
                                        <th class="text-center">Cant.</th>
                                        <th>Expiración</th>
                                        <th style="width: 40px;"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="product in (auditDetail.results?.products || [])">
                                        <tr>
                                            <td class="font-bold font-mono text-sm" x-text="product.product_code || product.name" style="color: #fff;"></td>
                                            <td class="muted text-xs" x-text="product.description || '—'"></td>
                                            <td class="text-center">
                                                <span class="qty-badge" x-text="product.quantity || product.qty"></span>
                                            </td>
                                            <td>
                                                <span :class="{
                                                    'expiry-badge': true,
                                                    'upcoming': (product.expiration_date || product.expiry || '').includes('2026')
                                                }">
                                                    <span x-text="product.expiration_date || product.expiry || 'Permanent'"></span>
                                                    <template x-if="(product.expiration_date || product.expiry || '').includes('2026')">
                                                        <span class="text-[9px] uppercase font-bold ml-1">(Próxima)</span>
                                                    </template>
                                                </span>
                                            </td>
                                            <td><i class="fa-solid fa-trash-can text-xs opacity-20"></i></td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer" style="background: transparent; border: none; padding-top: 0;">
                    <button type="button" @click="open = false" class="btn-secondary">Cerrar Detalle</button>
                    <button type="button" class="btn-primary">
                        <i class="fa-solid fa-file-export mr-2"></i> Exportar Reporte
                    </button>
                </div>
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

    /* Table Density */
    .table.text-sm td { padding: 8px 20px; vertical-align: middle; }
    .badge-muted { 
        background: rgba(255, 255, 255, 0.05); 
        color: var(--muted); 
        border: 1px solid rgba(255, 255, 255, 0.1);
        font-size: 9px;
        padding: 2px 8px;
    }

    /* Icon Buttons */
    .btn-icon {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid var(--border);
        color: var(--muted);
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 4px;
        cursor: pointer;
        transition: all 0.2s;
    }
    .btn-icon:hover { 
        background: var(--border);
        color: var(--text);
    }
    .btn-icon.text-danger:hover {
        background: rgba(239, 68, 68, 0.1);
        color: #ef4444;
        border-color: rgba(239, 68, 68, 0.2);
    }

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
    /* Modal Styles */
    .modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.75);
        backdrop-filter: blur(4px);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
        padding: 20px;
    }
    .modal-content {
        background: var(--card-bg);
        border: 1px solid var(--border);
        border-radius: 8px;
        width: 100%;
        max-width: 550px;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.5);
        overflow: hidden;
    }
    .modal-header {
        padding: 16px 20px;
        border-bottom: 1px solid var(--border);
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: rgba(255, 255, 255, 0.02);
    }
    .modal-header h3 {
        font-size: 14px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    .close-btn {
        background: none;
        border: none;
        color: var(--muted);
        font-size: 20px;
        cursor: pointer;
        padding: 4px;
        line-height: 1;
    }
    .close-btn:hover { color: var(--text); }
    
    .modal-body { padding: 24px; }
    .input-group { margin-bottom: 20px; }
    .input-group:last-child { margin-bottom: 0; }
    .input-group label {
        display: block;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--muted);
        margin-bottom: 8px;
    }
    .modal-footer {
        padding: 16px 24px;
        border-top: 1px solid var(--border);
        display: flex;
        justify-content: end;
        gap: 12px;
        background: rgba(255, 255, 255, 0.02);
    }

    /* Product Chips */
    .product-chip {
        display: inline-flex;
        align-items: center;
        background: rgba(var(--accent-rgb, 0, 122, 255), 0.05);
        color: var(--accent);
        border: 1px solid rgba(var(--accent-rgb, 0, 122, 255), 0.1);
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 10px;
        font-weight: 600;
        white-space: nowrap;
    }
    .product-chip.muted {
        background: rgba(255, 255, 255, 0.03);
        color: var(--muted);
        border-color: var(--border);
    }
    /* Audit Modal Styles */
    .audit-modal {
        box-shadow: 0 0 50px rgba(0,0,0,0.8), 0 0 0 1px rgba(255,255,255,0.05);
    }
    .audit-icon-box {
        width: 48px;
        height: 48px;
        background: linear-gradient(135deg, var(--accent), #1a73e8);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 20px;
        box-shadow: 0 8px 16px rgba(var(--accent-rgb), 0.3);
    }
    .audit-header-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
    }
    .audit-info-card {
        background: rgba(255,255,255,0.02);
        border: 1px solid rgba(255,255,255,0.05);
        padding: 16px 20px;
        border-radius: 12px;
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    .audit-info-card .label {
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: var(--muted);
    }
    .audit-info-card .value {
        font-size: 18px;
        font-weight: 800;
        letter-spacing: -0.02em;
    }
    .audit-info-card .value.daemon { color: var(--accent); font-family: var(--font-mono); font-size: 16px; }
    .audit-info-card .value.hostname { color: #fff; font-family: var(--font-mono); }

    .unified-box {
        background: rgba(245, 158, 11, 0.03);
        border: 1px dashed rgba(245, 158, 11, 0.2);
        padding: 12px 20px;
        border-radius: 10px;
    }
    .unified-box .label {
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        color: #f59e0b;
        margin-right: 8px;
    }

    .section-title {
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--muted);
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .section-title::after {
        content: '';
        flex: 1;
        height: 1px;
        background: rgba(255,255,255,0.05);
    }

    .audit-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 4px;
    }
    .audit-table th {
        text-align: left;
        padding: 12px 16px;
        font-size: 10px;
        text-transform: uppercase;
        color: var(--muted);
        font-weight: 700;
    }
    .audit-table td {
        padding: 14px 16px;
        background: rgba(255,255,255,0.01);
        border-top: 1px solid rgba(255,255,255,0.03);
        border-bottom: 1px solid rgba(255,255,255,0.03);
    }
    .audit-table td:first-child { border-left: 1px solid rgba(255,255,255,0.03); border-radius: 8px 0 0 8px; }
    .audit-table td:last-child { border-right: 1px solid rgba(255,255,255,0.03); border-radius: 0 8px 8px 0; }

    .qty-badge {
        background: #1e2235;
        color: #fff;
        padding: 4px 10px;
        border-radius: 20px;
        font-weight: 800;
        font-size: 12px;
        box-shadow: inset 0 2px 4px rgba(0,0,0,0.3);
    }
    .expiry-badge {
        background: rgba(255,255,255,0.03);
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 600;
        color: var(--text);
    }
    .expiry-badge.upcoming {
        background: rgba(245, 158, 11, 0.1);
        color: #f59e0b;
        border: 1px solid rgba(245, 158, 11, 0.2);
    }
</style>
@endpush
