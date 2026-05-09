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
                        <td class="font-bold">{{ $contract->end_date ? $contract->end_date->format('d/m/Y') : '—' }}</td>
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

    <!-- Licencias Tab (Inventario Activo) -->
    <div x-show="tab === 'licenses'" style="display: none;">
        <div class="inv-container">
            @forelse($inventoryBySoldTo as $soldTo => $daemons)
                <div class="sold-to-block">
                    <div class="sold-to-header">
                        <div class="sold-to-badge-wrapper">
                            <div class="sold-to-icon"><i class="fa-solid fa-id-card"></i></div>
                            <div>
                                <span class="tech-label">{{ $daemons->first()->vendor === 'moldex' ? 'Customer ID' : 'Sold-To Account' }}</span>
                                <div class="sold-to-id">{{ $soldTo }}</div>
                            </div>
                        </div>
                        <div class="tech-label" style="opacity: 0.3; letter-spacing: 0.4em;">Active Inventory</div>
                    </div>

                    @foreach($daemons as $daemon)
                        <div class="daemon-card {{ $daemon->vendor }}">
                            <div class="daemon-header">
                                <div class="header-col">
                                    <span class="tech-label">{{ $daemon->vendor === 'moldex' ? 'Plataforma' : 'Daemon' }}</span>
                                    <div style="display: flex; align-items: center;">
                                        @if($daemon->vendor === 'moldex')
                                            <span class="tech-value daemon-name moldex-logo">Moldex<span class="accent">3D</span></span>
                                        @else
                                            <span class="tech-value daemon-name">{{ $daemon->daemon }}</span>
                                            <span class="inv-badge badge-{{ $daemon->vendor }}">{{ ucfirst($daemon->vendor) }}</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="header-col grow">
                                    @if($daemon->type === 'dongle')
                                        <span class="tech-label">Hardware Key / Dongle</span>
                                        <div style="display: flex; align-items: center; gap: 8px;">
                                            <i class="fa-solid fa-key" style="font-size: 12px; opacity: 0.3;"></i>
                                            <span class="tech-value">{{ $daemon->hardware_id }}</span>
                                        </div>
                                    @elseif($daemon->vendor === 'moldex')
                                        <span class="tech-label">Servidor / Hostname</span>
                                        <span class="tech-value uppercase">{{ $daemon->hostname ?? 'N/A' }}</span>
                                        <span class="tech-label" style="font-size: 8px; opacity: 0.4; margin-top: 2px;">Machine ID: {{ $daemon->hardware_id }}</span>
                                    @else
                                        <span class="tech-label">Server Hostname</span>
                                        <span class="tech-value uppercase">{{ $daemon->hostname ?? 'N/A' }}</span>
                                        <span class="tech-label" style="font-size: 8px; opacity: 0.4; margin-top: 2px;">ID: {{ $daemon->composite ?? '—' }}</span>
                                    @endif
                                </div>

                                <div class="header-col" style="min-width: 120px;">
                                    <span class="tech-label">Configuración</span>
                                    <div style="display: flex; align-items: center; gap: 6px;">
                                        <span class="inv-badge badge-type">{{ $daemon->type }}</span>
                                        @if($daemon->version)
                                            <span class="inv-badge" style="background: rgba(255,255,255,0.05); color: #fff; border: 1px solid rgba(255,255,255,0.1);">v{{ $daemon->version }}</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="header-col">
                                    <form action="{{ route('inventory.daemon.destroy', $daemon) }}" method="POST" onsubmit="return confirm('¿Eliminar bloque?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn-action"><i class="fa-solid fa-trash-can"></i></button>
                                    </form>
                                </div>
                            </div>

                            <table class="inv-table">
                                <thead>
                                    <tr>
                                        <th>Producto</th>
                                        <th>Descripción Técnica</th>
                                        <th>Host ID (MAC)</th>
                                        <th style="text-align: center;">Cant.</th>
                                        <th>Expiración</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($daemon->products as $product)
                                        <tr style="{{ $product->status !== 'active' ? 'opacity: 0.3;' : '' }}">
                                            <td class="product-code">{{ $product->product_code }}</td>
                                            <td>{{ $product->description }}</td>
                                            <td class="host-id-mono">{{ $product->node_locked_host_id ?? '—' }}</td>
                                            <td style="text-align: center;"><div class="qty-badge">{{ $product->quantity }}</div></td>
                                            <td>
                                                @php
                                                    $isExpired = $product->expiration_date?->isPast();
                                                    $color = $isExpired ? '#ef4444' : (!$product->expiration_date ? '#009999' : 'rgba(255,255,255,0.4)');
                                                @endphp
                                                <span style="font-family: var(--font-mono); font-weight: 700; color: {{ $color }};">
                                                    {{ $product->expiration_date ? $product->expiration_date->format('d/m/Y') : 'PERMANENTE' }}
                                                </span>
                                            </td>
                                            <td style="text-align: right;">
                                                <form action="{{ route('inventory.product.destroy', $product) }}" method="POST">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn-action" style="border:none; width:20px; height:20px;"><i class="fa-solid fa-trash" style="font-size: 9px;"></i></button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endforeach
                </div>
            @empty
                <div style="text-align: center; padding: 60px; border: 2px dashed #30363d; border-radius: 12px; opacity: 0.5;">
                    <i class="fa-solid fa-microchip" style="font-size: 40px; margin-bottom: 20px; color: #388bfd;"></i>
                    <div class="tech-label" style="font-size: 12px;">Sin datos de inventario</div>
                </div>
            @endforelse

            @if($client->auditResults->count() > 0)
                <details>
                    <summary class="history-toggle">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <i class="fa-solid fa-clock-rotate-left" style="opacity: 0.4;"></i>
                            <span class="tech-label">Historial de archivos originales</span>
                        </div>
                        <i class="fa-solid fa-chevron-down" style="opacity: 0.3;"></i>
                    </summary>
                    <div style="padding: 20px; background: rgba(0,0,0,0.1); border: 1px solid #30363d; border-top: none; border-radius: 0 0 12px 12px;">
                        <table class="inv-table">
                            <tbody>
                                @foreach($client->auditResults as $result)
                                    <tr>
                                        <td class="product-code" style="color: #fff;">{{ $result->sold_to ?? 'N/A' }}</td>
                                        <td style="opacity: 0.5;">{{ $result->created_at->format('d/m/Y H:i') }}</td>
                                        <td class="tech-label" style="color: #009999;">{{ $result->results['vendor_daemon'] ?? '—' }}</td>
                                        <td style="text-align: right;">
                                            <button class="btn-action" @click="auditDetail = @js($result); $dispatch('open-audit-modal')">
                                                <i class="fa-solid fa-eye" style="font-size: 10px;"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </details>
            @endif
        </div>
    </div>

    <!-- Certificados Tab (Fase 8.4) -->
    <div x-show="tab === 'certificates'" class="tab-content" style="display: none;">
        <div class="card p-0">
            <div class="card-header flex justify-between items-center px-5 py-4">
                <h3 class="text-sm font-bold uppercase tracking-wider">Certificados de Cese (COD)</h3>
                <a href="{{ route('tools.cod.index', ['client_id' => $client->id]) }}" class="btn-primary sm">
                    <i class="fa-solid fa-plus mr-2"></i> Nuevo COD
                </a>
            </div>
            <table class="table text-sm">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Sold-To</th>
                        <th>Tipo</th>
                        <th>Estado</th>
                        <th class="text-right">Documento</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($client->codCertificates as $cod)
                    <tr>
                        <td>{{ $cod->created_at->format('d/m/Y H:i') }}</td>
                        <td class="font-mono text-xs">{{ $cod->sold_to }}</td>
                        <td>
                            @php
                                $typeLabels = [
                                    'CHANGE_FULL' => 'Cambio Completo',
                                    'CHANGE_COMPOSITE' => 'Cambio de Composite',
                                    'CHANGE_NODELOCKED' => 'Cambio Node Locked'
                                ];
                                $label = $typeLabels[$cod->type] ?? $cod->type;
                            @endphp
                            <span class="badge badge-muted">{{ $label }}</span>
                        </td>
                        <td>
                            @if($cod->status === 'PENDING')
                                <span class="badge badge-warn">Pte. Firma</span>
                            @else
                                <span class="badge badge-success">Firmado</span>
                            @endif
                        </td>
                        <td class="text-right" style="width: 140px; white-space: nowrap;">
                            <div class="inline-flex items-center gap-1.5 flex-nowrap">
                                <a href="{{ route('tools.cod.download', ['uuid' => $cod->uuid]) }}" class="btn-action-tool" title="Original">
                                    <i class="fa-solid fa-file-pdf text-red-500/80"></i>
                                </a>
                                
                                @if($cod->signed_file_path)
                                    <a href="{{ route('tools.cod.download-signed', ['uuid' => $cod->uuid]) }}" class="btn-action-tool signed" title="Firmado">
                                        <i class="fa-solid fa-file-signature"></i>
                                    </a>
                                @else
                                    <form action="{{ url('/herramientas/cod/' . $cod->uuid . '/upload-signed') }}" method="POST" enctype="multipart/form-data" style="display: contents;">
                                        @csrf
                                        <label class="btn-action-tool upload cursor-pointer" title="Subir Firmado">
                                            <i class="fa-solid fa-cloud-upload"></i>
                                            <input type="file" name="signed_file" class="hidden" accept=".pdf" onchange="this.form.submit()">
                                        </label>
                                    </form>
                                @endif

                                <form action="{{ route('tools.cod.destroy', ['uuid' => $cod->uuid]) }}" method="POST" onsubmit="return confirm('¿Eliminar permanente?')" style="display: contents;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-action-tool delete" title="Eliminar">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-12 muted">
                            No se han generado certificados COD para este cliente.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
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

    <!-- Modal de subida eliminado en favor de subida directa por simplicidad y robustez -->
</div>
@endsection

@push('styles')
<style>
    /* DX INVENTORY SYSTEM — ROBUST RECONSTRUCTION */
    /* ORIGINAL STYLES RESTORED */
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

    .table.text-sm td { padding: 8px 20px; vertical-align: middle; }
    .badge-muted { 
        background: rgba(255, 255, 255, 0.05); 
        color: var(--muted); 
        border: 1px solid rgba(255, 255, 255, 0.1);
        font-size: 9px;
        padding: 2px 8px;
    }

    .btn-icon {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid var(--border);
        color: var(--muted);
        width: 32px;
        height: 32px;
        display: flex; align-items: center; justify-content: center;
        border-radius: 4px; cursor: pointer; transition: all 0.2s;
    }
    .btn-icon:hover { background: var(--border); color: var(--text); }
    .btn-icon.text-danger:hover {
        background: rgba(239, 68, 68, 0.1); color: #ef4444; border-color: rgba(239, 68, 68, 0.2);
    }

    /* Tool Action Buttons (Compact & Premium) */
    .btn-action-tool {
        width: 28px;
        height: 28px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 6px;
        color: var(--muted);
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
        font-size: 11px;
    }
    .btn-action-tool:hover {
        background: rgba(255, 255, 255, 0.08);
        border-color: rgba(255, 255, 255, 0.2);
        color: #fff;
        transform: translateY(-1px);
    }
    .btn-action-tool.signed { color: var(--accent); border-color: rgba(0, 153, 153, 0.2); }
    .btn-action-tool.signed:hover { background: rgba(0, 153, 153, 0.1); border-color: var(--accent); }
    
    .btn-action-tool.upload { color: #388bfd; }
    .btn-action-tool.upload:hover { background: rgba(56, 139, 253, 0.1); border-color: #388bfd; }

    .btn-action-tool.delete:hover {
        background: rgba(239, 68, 68, 0.1);
        border-color: #ef4444;
        color: #ef4444;
    }
    
    .text-red-500\/80 { color: rgba(239, 68, 68, 0.8); }

    /* Legend Styles */
    .card-footer-legend {
        background: var(--bg);
        border-top: 1px solid var(--border);
        padding: 16px 20px;
    }
    .legend-header {
        display: flex; align-items: center; gap: 8px; margin-bottom: 12px; color: var(--muted);
    }
    .legend-header i { font-size: 10px; }
    .legend-header span {
        font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em;
    }
    .legend-grid { display: flex; flex-wrap: wrap; gap: 12px 24px; }
    .legend-item { display: flex; align-items: center; gap: 8px; }

    /* Upload Zone Styles */
    .upload-zone {
        border: 2px dashed var(--border);
        background: rgba(255, 255, 255, 0.02);
        border-radius: 12px;
        padding: 40px 20px;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    .upload-zone:hover {
        background: rgba(255, 255, 255, 0.05);
        border-color: var(--accent);
    }
    .upload-zone i { font-size: 40px; margin-bottom: 16px; color: var(--accent); opacity: 0.5; }
    .hidden { display: none; }
    .legend-item .badge { font-size: 9px; padding: 1px 6px; }
    .legend-label {
        font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: var(--muted);
    }

    /* Modal Styles */
    .modal-overlay {
        position: fixed; inset: 0; background: rgba(0, 0, 0, 0.75);
        backdrop-filter: blur(4px); display: flex; align-items: center; justify-content: center;
        z-index: 1000; padding: 20px;
    }
    .modal-content {
        background: var(--card-bg); border: 1px solid var(--border);
        border-radius: 8px; width: 100%; max-width: 550px;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.5); overflow: hidden;
    }
    .modal-header {
        padding: 16px 20px; border-bottom: 1px solid var(--border);
        display: flex; justify-content: space-between; align-items: center;
        background: rgba(255, 255, 255, 0.02);
    }
    .modal-header h3 { font-size: 14px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; }
    .close-btn { background: none; border: none; color: var(--muted); font-size: 20px; cursor: pointer; padding: 4px; line-height: 1; }
    .close-btn:hover { color: var(--text); }
    .modal-body { padding: 24px; }
    .input-group { margin-bottom: 20px; }
    .input-group label { display: block; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--muted); margin-bottom: 8px; }
    .modal-footer {
        padding: 16px 24px; border-top: 1px solid var(--border);
        display: flex; justify-content: end; gap: 12px;
        background: rgba(255, 255, 255, 0.02);
    }

    .cod-upload-overlay {
        position: fixed; inset: 0; background: rgba(0, 0, 0, 0.85);
        backdrop-filter: blur(8px); display: flex; align-items: center; justify-content: center;
        z-index: 9999; padding: 20px;
    }

    /* Product Chips & Badges */
    .product-chip {
        display: inline-flex; align-items: center; background: rgba(var(--accent-rgb, 0, 122, 255), 0.05);
        color: var(--accent); border: 1px solid rgba(var(--accent-rgb, 0, 122, 255), 0.1);
        padding: 2px 6px; border-radius: 4px; font-size: 10px; font-weight: 600; white-space: nowrap;
    }
    .product-chip.muted { background: rgba(255, 255, 255, 0.03); color: var(--muted); border-color: var(--border); }
    
    .expiry-badge { background: rgba(255,255,255,0.03); padding: 6px 12px; border-radius: 6px; font-size: 11px; font-weight: 600; color: var(--text); }
    .expiry-badge.upcoming { background: rgba(245, 158, 11, 0.1); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.2); }
    
    .badge-siemens { background: #009999; color: #fff; border: 1px solid rgba(255,255,255,0.2); }
    .badge-moldex { background: #ed1c24; color: #fff; border: 1px solid rgba(255,255,255,0.2); }
    .badge-info { background: rgba(var(--accent-rgb), 0.1); color: var(--accent); border: 1px solid rgba(var(--accent-rgb), 0.2); }
    .badge-warn { background: rgba(245, 158, 11, 0.1); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.2); }

    /* Audit Detail Modal Styles */
    .audit-modal { box-shadow: 0 0 50px rgba(0,0,0,0.8), 0 0 0 1px rgba(255,255,255,0.05); }
    .audit-icon-box {
        width: 48px; height: 48px; background: linear-gradient(135deg, var(--accent), #1a73e8);
        border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 20px;
    }
    .audit-header-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; }
    .audit-info-card {
        background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05);
        padding: 16px 20px; border-radius: 12px; display: flex; flex-direction: column; gap: 6px;
    }
    .audit-info-card .label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; color: var(--muted); }
    .audit-info-card .value { font-size: 18px; font-weight: 800; letter-spacing: -0.02em; }
    .audit-info-card .value.daemon { color: var(--accent); font-family: var(--font-mono); font-size: 16px; }
    .audit-info-card .value.hostname { color: #fff; font-family: var(--font-mono); }
    
    .unified-box { background: rgba(245, 158, 11, 0.03); border: 1px dashed rgba(245, 158, 11, 0.2); padding: 12px 20px; border-radius: 10px; }
    .unified-box .label { font-size: 10px; font-weight: 700; text-transform: uppercase; color: #f59e0b; margin-right: 8px; }
    
    .section-title { font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--muted); display: flex; align-items: center; gap: 10px; }
    .section-title::after { content: ''; flex: 1; height: 1px; background: rgba(255,255,255,0.05); }

    .audit-table { width: 100%; border-collapse: separate; border-spacing: 0 4px; }
    .audit-table th { text-align: left; padding: 12px 16px; font-size: 10px; text-transform: uppercase; color: var(--muted); font-weight: 700; }
    .audit-table td { padding: 14px 16px; background: rgba(255,255,255,0.01); border-top: 1px solid rgba(255,255,255,0.03); border-bottom: 1px solid rgba(255,255,255,0.03); }
    .audit-table td:first-child { border-left: 1px solid rgba(255,255,255,0.03); border-radius: 8px 0 0 8px; }
    .audit-table td:last-child { border-right: 1px solid rgba(255,255,255,0.03); border-radius: 0 8px 8px 0; }

    /* DX INVENTORY SYSTEM — ROBUST RECONSTRUCTION */
    .inv-container { display: flex; flex-direction: column; gap: 32px; margin-top: 16px; }
    
    .sold-to-block { margin-bottom: 40px; animation: fadeIn 0.4s ease-out; }
    
    .sold-to-header {
        display: flex; align-items: center; justify-content: space-between;
        padding-bottom: 12px; border-bottom: 1px solid rgba(255,255,255,0.05); margin-bottom: 20px;
    }

    .sold-to-badge-wrapper { display: flex; align-items: center; gap: 16px; }
    
    .sold-to-icon {
        width: 44px; height: 44px; background: rgba(56, 139, 253, 0.1); border: 1px solid rgba(56, 139, 253, 0.2);
        border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #388bfd; font-size: 18px;
    }

    .sold-to-id { font-family: var(--font-mono); font-size: 22px; font-weight: 800; color: #fff; letter-spacing: -0.01em; }

    .daemon-card {
        background: #0d1117; border: 1px solid #30363d; border-radius: 12px; overflow: hidden;
        margin-bottom: 24px; box-shadow: 0 4px 20px rgba(0,0,0,0.3);
    }
    
    .daemon-card.siemens { border-left: 4px solid #009999; }
    .daemon-card.moldex { border-left: 4px solid #ed1c24; }

    /* Header Layout */
    .daemon-header {
        display: flex; flex-direction: row; align-items: center; padding: 20px 24px;
        background: linear-gradient(to right, rgba(255,255,255,0.02), transparent);
        border-bottom: 1px solid rgba(255,255,255,0.03); gap: 40px;
    }

    .header-col { display: flex; flex-direction: column; gap: 4px; }
    .header-col.grow { flex-grow: 1; }

    .tech-label { font-size: 9px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.12em; color: #8b949e; display: block; }
    .tech-value { font-family: var(--font-mono); font-size: 16px; font-weight: 700; color: #fff; line-height: 1.2; }
    .daemon-name { color: #009999; font-size: 20px; }
    
    .moldex-logo { color: #ed1c24 !important; font-weight: 800; }
    .moldex-logo .accent { color: #f58220 !important; }

    .inv-badge { display: inline-flex; padding: 2px 6px; border-radius: 4px; font-size: 9px; font-weight: 900; text-transform: uppercase; }
    .badge-siemens { background: #009999; color: #fff; margin-left: 8px; }
    .badge-type { background: rgba(56, 139, 253, 0.1); color: #388bfd; border: 1px solid rgba(56, 139, 253, 0.2); }

    /* Table System */
    .inv-table { width: 100%; border-collapse: collapse; }
    .inv-table th {
        background: rgba(0,0,0,0.2); padding: 10px 24px; text-align: left; font-size: 9px; font-weight: 800;
        text-transform: uppercase; color: #8b949e; border-bottom: 1px solid #30363d;
    }
    .inv-table td { padding: 12px 24px; border-bottom: 1px solid rgba(255,255,255,0.02); font-size: 13px; vertical-align: middle; color: rgba(255,255,255,0.8); }
    .inv-table tr:hover td { background: rgba(255,255,255,0.01); }

    .product-code { font-family: var(--font-mono); font-weight: 700; color: #58a6ff; }
    .host-id-mono { font-family: var(--font-mono); font-size: 11px; color: rgba(255,255,255,0.25); }
    
    .qty-badge {
        display: inline-flex; align-items: center; justify-content: center;
        width: 28px; height: 22px; background: rgba(255,255,255,0.05);
        border: 1px solid rgba(255,255,255,0.1); border-radius: 4px;
        font-size: 11px; font-weight: 800; color: #fff;
    }

    .btn-action {
        width: 28px; height: 28px; border-radius: 6px; border: 1px solid rgba(255,255,255,0.05);
        background: transparent; color: rgba(255,255,255,0.2); cursor: pointer;
        display: flex; align-items: center; justify-content: center; transition: all 0.2s;
    }
    .btn-action:hover { background: rgba(239, 68, 68, 0.1); color: #ef4444; border-color: #ef4444; }

    @keyframes fadeIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }

    .history-toggle {
        display: flex; align-items: center; justify-content: space-between;
        padding: 14px 20px; background: #161b22; border: 1px solid #30363d;
        border-radius: 8px; cursor: pointer; margin-top: 40px;
    }
    .history-toggle:hover { border-color: #444; }
</style>
@endpush
