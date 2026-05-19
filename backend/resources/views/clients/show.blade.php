@extends('layouts.app')

@section('content')
<div class="page-header">
    <div class="breadcrumb">
        <a href="{{ route('clients.index') }}">Clientes</a>
        <span class="muted">/</span>
        <span class="current">{{ $client->name }}</span>
    </div>
    <div class="dx-v2-clients-header-split">
        <div>
            <h1 class="page-title">{{ $client->name }}</h1>
            <p class="page-sub">Perfil de cuenta y gestión de activos del ecosistema.</p>
        </div>
        <div class="dx-v2-clients-badge-container">
            @if($client->siemens_daemons_count > 0)
                <div class="badge dx-v2-clients-header-badge siemens">
                    <span class="dx-v2-clients-header-badge-value">{{ $client->siemens_daemons_count }}</span>
                    <span class="dx-v2-clients-header-badge-label">Siemens</span>
                </div>
            @endif
            @if($client->moldex_daemons_count > 0)
                <div class="badge dx-v2-clients-header-badge moldex">
                    <span class="dx-v2-clients-header-badge-value">{{ $client->moldex_daemons_count }}</span>
                    <span class="dx-v2-clients-header-badge-label">Moldex3D</span>
                </div>
            @endif
        </div>
    </div>
</div>

<div x-data="{ 
    tab: localStorage.getItem('activeTab') || '{{ request('tab', 'contracts') }}',
    auditDetail: null,
    setTab(name) {
        this.tab = name;
        localStorage.setItem('activeTab', name);
    }
}" class="client-profile">
    <div class="dx-v2-clients-tabs">
        <button class="dx-v2-clients-tab-link" :class="{ 'active': tab === 'contracts' }" @click="setTab('contracts')">Contratos</button>
        <button class="dx-v2-clients-tab-link" :class="{ 'active': tab === 'licenses' }" @click="setTab('licenses')">Licencias</button>
        <button class="dx-v2-clients-tab-link" :class="{ 'active': tab === 'contacts' }" @click="setTab('contacts')">Contactos</button>
        <button class="dx-v2-clients-tab-link" :class="{ 'active': tab === 'certificates' }" @click="setTab('certificates')">Certificados</button>
        <button class="dx-v2-clients-tab-link" :class="{ 'active': tab === 'renewals' }" @click="setTab('renewals')">Renovaciones</button>
    </div>

    <!-- Contratos Tab -->
    <div x-show="tab === 'contracts'" class="tab-content">
        <div class="card">
            <div class="dx-v2-ui-table-wrapper">
                <table class="dx-v2-ui-table">
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
                                    <span class="vendor-chip"><span class="dx-v2-clients-vendor-dot siemens"></span> Siemens</span>
                                @elseif($contract->vendor->name == 'Moldex3D')
                                    <span class="vendor-chip"><span class="dx-v2-clients-vendor-dot moldex"></span> Moldex3D</span>
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
                                    <i class="{{ $data['icon'] }} dx-v2-clients-status-icon"></i>
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
            </div>

            <!-- Card Footer Legend -->
            <div class="dx-v2-clients-legend-wrapper">
                <div class="dx-v2-clients-legend-header">
                    <i class="fa-solid fa-circle-info"></i>
                    <span class="dx-v2-clients-legend-header-title">Leyenda de Estados del Ecosistema</span>
                </div>
                <div class="dx-v2-clients-legend-grid">
                    <div class="dx-v2-clients-legend-item">
                        <span class="badge badge-info"><i class="fa-solid fa-file-signature"></i> Ofertado</span>
                    </div>
                    <div class="dx-v2-clients-legend-item">
                        <span class="badge badge-primary"><i class="fa-solid fa-handshake"></i> Negociación</span>
                    </div>
                    <div class="dx-v2-clients-legend-item">
                        <span class="badge badge-accent"><i class="fa-solid fa-circle-check"></i> Aceptado</span>
                    </div>
                    <div class="dx-v2-clients-legend-item">
                        <span class="badge badge-warn"><i class="fa-solid fa-gears"></i> Procesado</span>
                    </div>
                    <div class="dx-v2-clients-legend-item">
                        <span class="badge badge-warning"><i class="fa-solid fa-file-invoice-dollar"></i> Facturado</span>
                    </div>
                    <div class="dx-v2-clients-legend-item">
                        <span class="badge badge-success"><i class="fa-solid fa-lock"></i> Cerrado</span>
                    </div>
                    <div class="dx-v2-clients-legend-item">
                        <span class="badge badge-danger"><i class="fa-solid fa-circle-xmark"></i> Baja</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Licencias Tab (Inventario Activo) -->
    <div x-show="tab === 'licenses'" x-cloak>
        <div class="dx-v2-clients-inv-container">
            @forelse($inventoryBySoldTo as $soldTo => $daemons)
                <div class="dx-v2-clients-soldto-block">
                    <div class="dx-v2-clients-soldto-header">
                        <div class="dx-v2-clients-soldto-badge-wrapper">
                            <div class="dx-v2-clients-soldto-icon"><i class="fa-solid fa-id-card"></i></div>
                            <div>
                                <span class="tech-label">{{ $daemons->first()->vendor === 'moldex' ? 'Customer ID' : 'Sold-To Account' }}</span>
                                <div class="dx-v2-clients-soldto-id">{{ $soldTo }}</div>
                            </div>
                        </div>
                        <div class="tech-label dx-v2-clients-soldto-header-right">Active Inventory</div>
                    </div>

                    @foreach($daemons as $daemon)
                        <div class="dx-v2-clients-daemon-card {{ $daemon->vendor }} {{ !empty($daemon->additional_sold_tos) ? 'unified-card' : '' }}">
                            @if(!empty($daemon->additional_sold_tos))
                                <div class="dx-v2-clients-daemon-watermark">
                                    <i class="fa-solid fa-network-wired"></i>
                                </div>
                            @endif
                            <div class="dx-v2-clients-daemon-header">
                                <div class="dx-v2-clients-daemon-header-col">
                                    <span class="tech-label">{{ $daemon->vendor === 'moldex' ? 'Plataforma' : 'Daemon' }}</span>
                                    <div class="dx-v2-clients-daemon-logo-wrap">
                                        @if($daemon->vendor === 'moldex')
                                            <span class="tech-value dx-v2-clients-daemon-name moldex-logo">Moldex<span class="accent">3D</span></span>
                                        @else
                                            <span class="tech-value dx-v2-clients-daemon-name">{{ $daemon->daemon }}</span>
                                            <span class="dx-v2-clients-daemon-badge {{ $daemon->vendor }}">{{ ucfirst($daemon->vendor) }}</span>
                                        @endif

                                </div>
                            </div>

                            @if(!empty($daemon->additional_sold_tos))
                                <div class="dx-v2-clients-daemon-unified-row">
                                    <div class="dx-v2-clients-unified-list">
                                        @foreach($daemon->additional_sold_tos as $extraSt)
                                            <span class="dx-v2-clients-unified-item">
                                                <i class="fa-solid fa-link"></i>
                                                {{ $extraSt }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                                <div class="dx-v2-clients-daemon-header-col grow">
                                    @if($daemon->type === 'dongle')
                                        <span class="tech-label">Hardware Key / Dongle</span>
                                        <div class="dx-v2-clients-hardware-id-wrap">
                                            <i class="fa-solid fa-key dx-v2-clients-hardware-icon"></i>
                                            <span class="tech-value">{{ $daemon->hardware_id }}</span>
                                        </div>
                                    @elseif($daemon->vendor === 'moldex')
                                        <span class="tech-label">Servidor / Hostname</span>
                                        <span class="tech-value uppercase">{{ $daemon->hostname ?? 'N/A' }}</span>
                                        <span class="tech-label dx-v2-clients-sub-label">Machine ID: {{ $daemon->hardware_id }}</span>
                                    @else
                                        <span class="tech-label">Server Hostname</span>
                                        <span class="tech-value uppercase">{{ $daemon->hostname ?? 'N/A' }}</span>
                                        <span class="tech-label dx-v2-clients-sub-label">ID: {{ $daemon->composite ?? '—' }}</span>
                                    @endif
                                </div>

                                <div class="dx-v2-clients-daemon-header-col width-120">
                                    <span class="tech-label">Configuración</span>
                                    <div class="dx-v2-clients-daemon-logo-wrap">
                                        <span class="dx-v2-clients-daemon-badge type">{{ $daemon->type }}</span>
                                        @if($daemon->version)
                                            <span class="dx-v2-clients-daemon-badge version">v{{ $daemon->version }}</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="dx-v2-clients-daemon-header-col">
                                    <form action="{{ route('inventory.daemon.destroy', $daemon) }}" method="POST" onsubmit="return confirm('¿Eliminar bloque?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="dx-v2-clients-btn-action"><i class="fa-solid fa-trash-can"></i></button>
                                    </form>
                                </div>
                            </div>

                            <table class="dx-v2-clients-inv-table">
                                <thead>
                                    <tr>
                                        <th>Producto</th>
                                        <th>Descripción Técnica</th>
                                        <th>Host ID (MAC)</th>
                                        <th class="text-center">Cant.</th>
                                        <th>Expiración</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($daemon->products as $product)
                                        <tr class="dx-v2-clients-product-row {{ $product->status !== 'active' ? 'inactive' : '' }}">
                                            <td class="dx-v2-clients-product-code">{{ $product->product_code }}</td>
                                            <td>{{ $product->description }}</td>
                                            <td class="dx-v2-clients-host-mono">{{ $product->node_locked_host_id ?? '—' }}</td>
                                            <td class="text-center">
                                                <div class="dx-v2-clients-qty-badge">{{ $product->quantity }}</div>
                                            </td>
                                            <td>
                                                @php
                                                    $isExpired = $product->expiration_date?->isPast();
                                                    $statusClass = $isExpired ? 'expired' : (!$product->expiration_date ? 'permanent' : 'default');
                                                @endphp
                                                <span class="dx-v2-clients-expiry-status {{ $statusClass }}">
                                                    {{ $product->expiration_date ? $product->expiration_date->format('d/m/Y') : 'PERMANENTE' }}
                                                </span>
                                            </td>
                                            <td class="text-right">
                                                <form action="{{ route('inventory.product.destroy', $product) }}" method="POST">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="dx-v2-clients-btn-action delete-action">
                                                        <i class="fa-solid fa-trash"></i>
                                                    </button>
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
                <div class="dx-v2-clients-empty-box">
                    <i class="fa-solid fa-microchip"></i>
                    <div class="tech-label dx-v2-clients-empty-text">Sin datos de inventario</div>
                </div>
            @endforelse

            @if($client->auditResults->count() > 0)
                <details class="dx-v2-clients-history-details">
                    <summary class="dx-v2-clients-history-toggle">
                        <div class="dx-v2-clients-history-header">
                            <i class="fa-solid fa-clock-rotate-left"></i>
                            <span class="dx-v2-clients-tech-label">Historial de archivos originales</span>
                        </div>
                        <i class="fa-solid fa-chevron-down toggle-icon"></i>
                    </summary>
                    <div class="dx-v2-clients-history-content">
                        <table class="dx-v2-clients-inv-table">
                            <tbody>
                                @foreach($client->auditResults as $result)
                                    <tr>
                                        <td class="dx-v2-clients-product-code white-text">{{ $result->sold_to ?? 'N/A' }}</td>
                                        <td class="opacity-50">{{ $result->created_at->format('d/m/Y H:i') }}</td>
                                        <td class="dx-v2-clients-tech-label siemens-color">{{ $result->results['vendor_daemon'] ?? '—' }}</td>
                                        <td class="text-right">
                                            <button class="dx-v2-clients-btn-action" @click="auditDetail = @js($result); $dispatch('open-audit-modal')">
                                                <i class="fa-solid fa-eye"></i>
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
    <div x-show="tab === 'certificates'" class="tab-content" x-cloak>
        <div class="card p-0">
            <div class="card-header flex justify-between items-center px-5 py-4">
                <h3 class="text-sm font-bold uppercase tracking-wider">Certificados de Cese (COD)</h3>
                <a href="{{ route('tools.cod.index', ['client_id' => $client->id]) }}" class="btn-primary sm">
                    <i class="fa-solid fa-plus mr-2"></i> Nuevo COD
                </a>
            </div>
            <div class="dx-v2-ui-table-wrapper">
                <table class="dx-v2-ui-table">
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
                        <td class="text-right">
                            <div class="dx-v2-clients-contacts-actions">
                                <a href="{{ route('tools.cod.download', ['uuid' => $cod->uuid]) }}" class="dx-v2-clients-btn-action-tool" title="Original">
                                    <i class="fa-solid fa-file-pdf dx-v2-clients-pdf-icon"></i>
                                </a>
                                
                                @if($cod->signed_file_path)
                                    <a href="{{ route('tools.cod.download-signed', ['uuid' => $cod->uuid]) }}" class="dx-v2-clients-btn-action-tool signed" title="Firmado">
                                        <i class="fa-solid fa-file-signature text-accent"></i>
                                    </a>
                                @else
                                    <form action="{{ url('/herramientas/cod/' . $cod->uuid . '/upload-signed') }}" method="POST" enctype="multipart/form-data" class="display-contents">
                                        @csrf
                                        <label class="dx-v2-clients-btn-action-tool upload" title="Subir Firmado">
                                            <i class="fa-solid fa-cloud-upload text-blue"></i>
                                            <input type="file" name="signed_file" class="hidden" accept=".pdf" onchange="this.form.submit()">
                                        </label>
                                    </form>
                                @endif
 
                                <form action="{{ route('tools.cod.destroy', ['uuid' => $cod->uuid]) }}" method="POST" onsubmit="return confirm('¿Eliminar permanente?')" class="display-contents">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="dx-v2-clients-btn-action-tool delete" title="Eliminar">
                                        <i class="fa-solid fa-trash-can text-danger"></i>
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
    </div>

    <!-- Contactos Tab -->
    <div x-show="tab === 'contacts'" class="tab-content" x-cloak>
        <div class="card p-0">
            <div class="card-header flex justify-between items-center px-5 py-4">
                <h3 class="text-sm font-bold uppercase tracking-wider">Personas de Contacto</h3>
                <button class="dx-v2-ui-btn dx-v2-ui-btn-primary" @click="$dispatch('open-contact-modal')">
                    <i class="fa-solid fa-plus mr-2"></i> Nuevo Contacto
                </button>
            </div>
            <div class="dx-v2-ui-table-wrapper">
                <table class="dx-v2-ui-table">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Cargo</th>
                        <th class="text-center">Alertas</th>
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
                        <td class="text-center">
                            @if($contact->receives_alerts)
                                <span class="badge badge-success sm" title="Recibe reporte semanal">
                                    <i class="fa-solid fa-bell mr-1"></i> Sí
                                </span>
                            @else
                                <span class="badge badge-muted sm opacity-50">No</span>
                            @endif
                        </td>
                        <td class="text-right max-w-100">
                            <div class="dx-v2-clients-contacts-actions">
                                <button class="dx-v2-clients-btn-action-tool" 
                                    @click="$dispatch('open-contact-modal', { 
                                        id: {{ $contact->id }}, 
                                        name: '{{ $contact->name }}', 
                                        email: '{{ $contact->email }}', 
                                        position: '{{ $contact->position }}',
                                        phone: '{{ $contact->phone }}',
                                        receives_alerts: {{ $contact->receives_alerts ? 'true' : 'false' }}
                                    })">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                                <form action="{{ route('contacts.destroy', [$client, $contact]) }}" method="POST" onsubmit="return confirm('¿Eliminar este contacto?')" class="display-inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="dx-v2-clients-btn-action-tool delete">
                                        <i class="fa-solid fa-trash-can text-danger"></i>
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
    </div>

    <!-- Renovaciones Tab (Fase 14) -->
    <div x-show="tab === 'renewals'" class="tab-content" x-cloak>
        <div class="card p-0">
            <div class="card-header flex justify-between items-center px-5 py-4">
                <h3 class="text-sm font-bold uppercase tracking-wider">Historial de Renovaciones Mensuales</h3>
            </div>
            <div class="dx-v2-ui-table-wrapper">
                <table class="dx-v2-ui-table">
                <thead>
                    <tr>
                        <th>Mes / Ciclo</th>
                        <th>Fecha de Envío</th>
                        <th>Responsable</th>
                        <th class="text-right">Notas</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($client->renewalLogs()->orderBy('year', 'desc')->orderBy('month', 'desc')->get() as $log)
                    <tr>
                        <td class="font-bold">
                            {{ Carbon\Carbon::create(2024, $log->month, 1)->translatedFormat('F') }} {{ $log->year }}
                        </td>
                        <td class="muted">{{ $log->sent_at ? $log->sent_at->format('d/m/Y H:i') : '—' }}</td>
                        <td>
                            <div class="flex items-center gap-2">
                                <div class="dx-v2-clients-avatar-xs">
                                    {{ substr($log->user->name ?? 'U', 0, 1) }}
                                </div>
                                <span>{{ $log->user->name ?? 'Sistema' }}</span>
                            </div>
                        </td>
                        <td class="text-right">
                            <span class="muted text-xs">{{ $log->notes ?: '—' }}</span>
                        </td>
                    </tr>
@empty
                    <tr>
                        <td colspan="4" class="text-center py-12 muted">
                            No se han registrado renovaciones enviadas para este cliente.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            </div>
        </div>
    </div>

    <!-- Contact Modal -->
    <template x-teleport="body">
        <div x-data="{ 
                open: false, 
                editMode: false,
                action: '{{ route('contacts.store', $client) }}',
                form: { id: '', name: '', email: '', position: '', phone: '', receives_alerts: false }
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
                    form = { id: '', name: '', email: '', position: '', phone: '', receives_alerts: false };
                }
            "
            class="dx-v2-ui-modal-overlay"
            x-cloak
        >
            <div class="dx-v2-ui-modal-content" @click.outside="open = false">
                <div class="dx-v2-ui-modal-header">
                    <h3 class="dx-v2-ui-modal-title" x-text="editMode ? 'Editar Contacto' : 'Nuevo Contacto'"></h3>
                    <button type="button" @click="open = false" class="dx-v2-ui-modal-close">&times;</button>
                </div>
                <form :action="action" method="POST">
                    @csrf
                    <template x-if="editMode">
                        <input type="hidden" name="_method" value="PUT">
                    </template>
                    
                    <div class="dx-v2-ui-modal-body space-y-5">
                        <div class="grid grid-cols-2 gap-6">
                            <div class="dx-v2-form-group" style="margin-bottom: 0;">
                                <label class="dx-v2-form-label">Nombre Completo</label>
                                <input type="text" name="name" x-model="form.name" required class="dx-v2-form-input w-full" placeholder="Ej. Juan Pérez">
                            </div>
                            <div class="dx-v2-form-group" style="margin-bottom: 0;">
                                <label class="dx-v2-form-label">Email Corporativo</label>
                                <input type="email" name="email" x-model="form.email" required class="dx-v2-form-input w-full" placeholder="email@empresa.com">
                            </div>
                        </div>
                        <div class="dx-v2-form-group" style="margin-bottom: 0;">
                            <label class="dx-v2-form-label">Cargo / Departamento</label>
                            <input type="text" name="position" x-model="form.position" class="dx-v2-form-input w-full" placeholder="Ej. IT Manager">
                        </div>
                        <div class="dx-v2-form-group" style="margin-bottom: 0;">
                            <label class="dx-v2-form-label">Teléfono (Opcional)</label>
                            <input type="text" name="phone" x-model="form.phone" class="dx-v2-form-input w-full" placeholder="+34 ...">
                        </div>
                        <div class="dx-v2-form-group" style="margin-bottom: 0; padding-top: 8px;">
                            <label class="dx-v2-form-checkbox-wrapper">
                                <input type="checkbox" name="receives_alerts" value="1" x-model="form.receives_alerts" class="dx-v2-form-checkbox">
                                <span class="dx-v2-form-label" style="text-transform: none; font-size: 13px; color: var(--dx-v2-primary) !important; cursor: pointer;">Recibir reportes semanales de caducidad</span>
                            </label>
                        </div>
                    </div>
                    
                    <div class="dx-v2-ui-modal-footer">
                        <button type="button" @click="open = false" class="dx-v2-ui-btn dx-v2-ui-btn-secondary">Cancelar</button>
                        <button type="submit" class="dx-v2-ui-btn dx-v2-ui-btn-primary" x-text="editMode ? 'Guardar Cambios' : 'Crear Contacto'"></button>
                    </div>
                </form>
            </div>
        </div>
    </template>

    <!-- Audit Detail Modal -->
    <template x-teleport="body">
        <div x-data="{ open: false }"
            x-show="open"
            @open-audit-modal.window="open = true"
            class="dx-v2-ui-modal-overlay high-z-index"
            x-cloak
        >
            <div class="dx-v2-ui-modal-content wide" @click.outside="open = false">
                <div class="dx-v2-ui-modal-header no-border no-padding-bottom">
                    <div class="flex items-center gap-4">
                        <div class="dx-v2-clients-audit-icon-box">
                            <i class="fa-solid fa-shield-halved"></i>
                        </div>
                        <div>
                            <h3 class="dx-v2-ui-modal-title text-white" style="margin-bottom: 4px !important;">Detalle de Auditoría Siemens</h3>
                            <span class="text-xs muted uppercase tracking-widest font-bold">Analizado por Motor FallbackChain v2.1</span>
                        </div>
                    </div>
                    <button type="button" @click="open = false" class="dx-v2-ui-modal-close">&times;</button>
                </div>

                <div class="dx-v2-ui-modal-body p-8">
                    <template x-if="auditDetail">
                        <div>
                            <!-- Top Info Cards -->
                            <div class="dx-v2-clients-audit-header-grid">
                                <div class="dx-v2-clients-audit-info-card">
                                    <span class="label">Account / Sold-To</span>
                                    <span class="value text-white" x-text="auditDetail?.sold_to || 'N/A'"></span>
                                </div>
                                <div class="dx-v2-clients-audit-info-card">
                                    <span class="label">Ecosistema / Daemon</span>
                                    <div class="flex items-center gap-2">
                                        <span class="value daemon-color font-mono" x-text="auditDetail?.results?.daemon || 'ugslmd'"></span>
                                        <span class="badge badge-accent sm">SIEMENS</span>
                                    </div>
                                </div>
                                <div class="dx-v2-clients-audit-info-card col-span-2">
                                    <span class="label">Servidor / Hostname</span>
                                    <div class="flex items-baseline gap-3">
                                        <span class="value hostname-color font-mono" x-text="auditDetail?.results?.hostname || 'PENDIENTE'"></span>
                                        <span class="text-xs font-mono text-accent" x-text="auditDetail?.results?.composite ? 'Composite: ' + auditDetail.results.composite : ''"></span>
                                    </div>
                                </div>
                            </div>
        
                            <!-- Unified Sold-Tos -->
                            <div class="dx-v2-clients-unified-box mt-6" x-show="auditDetail?.results?.additional_sold_tos?.length">
                                <div class="flex items-center gap-3">
                                    <i class="fa-solid fa-link text-warn text-xs"></i>
                                    <span class="label">Sold-Tos Unificados:</span>
                                    <div class="flex flex-wrap gap-2">
                                        <template x-for="st in auditDetail?.results?.additional_sold_tos">
                                            <span class="badge badge-muted sm" x-text="st"></span>
                                        </template>
                                    </div>
                                </div>
                            </div>
        
                            <!-- Products Table -->
                            <div class="mt-10">
                                <h4 class="dx-v2-clients-section-title">Desglose de Productos y Expiración</h4>
                                <div class="audit-table-wrapper mt-4">
                                    <table class="dx-v2-clients-audit-table">
                                        <thead>
                                            <tr>
                                                <th>Producto</th>
                                                <th>Descripción</th>
                                                <th class="text-center">Cant.</th>
                                                <th>Expiración</th>
                                                <th class="w-40"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <template x-for="product in (auditDetail?.results?.products || [])">
                                                <tr>
                                                    <td class="font-bold font-mono text-sm text-white" x-text="product.product_code || product.name"></td>
                                                    <td class="muted text-xs" x-text="product.description || '—'"></td>
                                                    <td class="text-center">
                                                        <span class="dx-v2-clients-qty-badge" x-text="product.quantity || product.qty"></span>
                                                    </td>
                                                    <td>
                                                        <span :class="{
                                                            'dx-v2-clients-expiry-badge': true,
                                                            'upcoming': (product.expiration_date || product.expiry || '').includes('2026')
                                                        }">
                                                            <span x-text="product.expiration_date || product.expiry || 'Permanent'"></span>
                                                            <template x-if="(product.expiration_date || product.expiry || '').includes('2026')">
                                                                <span class="upcoming-indicator">(Próxima)</span>
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
                    </template>
                </div>
                
                <div class="dx-v2-ui-modal-footer transparent no-border no-padding-top">
                    <button type="button" @click="open = false" class="dx-v2-ui-btn dx-v2-ui-btn-secondary">Cerrar Detalle</button>
                    <button type="button" class="dx-v2-ui-btn dx-v2-ui-btn-primary">
                        <i class="fa-solid fa-file-export mr-2"></i> Exportar Reporte
                    </button>
                </div>
            </div>
        </div>
    </template>

    <!-- Modal de subida eliminado en favor de subida directa por simplicidad y robustez -->
</div>
@endsection


