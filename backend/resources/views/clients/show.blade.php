@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/modules/dx-v2-clients.css?v=' . time()) }}">
@endpush

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
    historyOpen: false,
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
                <div class="dx-v2-clients-soldto-block" style="{{ $loop->last ? 'margin-bottom: 20px !important;' : '' }}">
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
                                                    $expiration = $product->expiration_date;
                                                    $isExpired = $expiration?->isPast();
                                                    $diffInDays = $expiration ? now()->diffInDays($expiration, false) : null;
                                                    
                                                    if ($isExpired) {
                                                        $statusClass = 'expired';
                                                        $icon = 'fa-solid fa-circle-xmark';
                                                    } elseif ($diffInDays !== null && $diffInDays >= 0 && $diffInDays <= 30) {
                                                        $statusClass = 'warning';
                                                        $icon = 'fa-solid fa-triangle-exclamation';
                                                    } elseif (!$expiration) {
                                                        $statusClass = 'permanent';
                                                        $icon = 'fa-solid fa-infinity';
                                                    } else {
                                                        $statusClass = 'default';
                                                        $icon = 'fa-solid fa-calendar-check';
                                                    }
                                                @endphp
                                                <span class="dx-v2-clients-expiry-status {{ $statusClass }}">
                                                    <i class="{{ $icon }}"></i>
                                                    {{ $expiration ? $expiration->format('d/m/Y') : 'PERMANENTE' }}
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
                <div class="dx-v2-clients-history-wrapper" style="margin-top: 8px !important;">
                    <!-- Acordeón Header Toggle -->
                    <div class="dx-v2-clients-history-toggle" 
                         @click="historyOpen = !historyOpen"
                         style="margin-top: 0;"
                         :style="historyOpen ? 'border-bottom-left-radius: 0; border-bottom-right-radius: 0;' : ''">
                        <div class="dx-v2-clients-history-toggle-left">
                            <i class="fa-solid fa-clock-rotate-left dx-v2-clients-history-toggle-icon" style="font-size: 16px;"></i>
                            <div style="text-align: left;">
                                <span style="display: block; font-size: 13px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; color: #fff; line-height: 1.2;">Historial de Archivos de Licencia Originales</span>
                                <span style="display: block; font-size: 10px; color: var(--muted); margin-top: 2px;">{{ $client->auditResults->count() }} archivos subidos en este cliente</span>
                            </div>
                        </div>
                        <i class="fa-solid fa-chevron-down dx-v2-clients-history-toggle-arrow" style="transition: transform 0.3s ease; font-size: 12px;" :style="historyOpen ? 'transform: rotate(180deg); color: var(--dx-v2-accent-base);' : ''"></i>
                    </div>

                    <!-- Contenido con Animación Alpine.js -->
                    <div class="dx-v2-clients-history-content" x-show="historyOpen" x-cloak x-transition style="display: none;">
                        <div>
                            <!-- Banner Explicativo de Propósito -->
                            <div style="background: rgba(56, 139, 253, 0.04); border: 1px solid rgba(56, 139, 253, 0.15); border-radius: 6px; padding: 12px 16px; margin-bottom: 20px; display: flex; gap: 12px; align-items: flex-start;">
                                <i class="fa-solid fa-circle-info" style="color: #388bfd; font-size: 14px; margin-top: 2px;"></i>
                                <div>
                                    <h5 style="font-size: 11px; font-weight: 800; color: #388bfd; text-transform: uppercase; letter-spacing: 0.05em; margin: 0 0 4px 0;">Fuente de Verdad Histórica (Solo Lectura)</h5>
                                    <p style="font-size: 11px; color: var(--muted); line-height: 1.5; margin: 0;">
                                        Estos registros representan los archivos físicos originales (`.lic` o `.mac`) que se cargaron en el sistema. Los datos extraídos fueron validados e importados al inventario activo actual del cliente. Úsalos como respaldo técnico de auditoría.
                                    </p>
                                </div>
                            </div>

                            <!-- Tabla de Auditorías de Alta Densidad -->
                            <table class="dx-v2-clients-inv-table">
                                <thead>
                                    <tr>
                                        <th style="width: 28%; font-size: 9px; padding: 10px 24px;">Ecosistema / Daemon</th>
                                        <th style="width: 20%; font-size: 9px; padding: 10px 24px;">Cuenta / Sold-To</th>
                                        <th style="width: 22%; font-size: 9px; padding: 10px 24px;">Fecha de Subida</th>
                                        <th style="width: 15%; font-size: 9px; padding: 10px 24px;">Servidor / Hostname</th>
                                        <th style="width: 15%; font-size: 9px; padding: 10px 24px; text-align: right;">Inspección</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($client->auditResults as $result)
                                        @php
                                            $daemonVal = $result->results['vendor_daemon'] ?? ($result->results['daemon'] ?? 'Desconocido');
                                            $isMoldex = str_contains(strtolower($daemonVal), 'moldex') || !isset($result->results['vendor_daemon']);
                                            $hostName = $result->results['hostname'] ?? 'N/A';
                                        @endphp
                                        <tr>
                                            <td style="padding: 12px 24px;">
                                                <div style="display: flex; align-items: center; gap: 8px;">
                                                    <span class="dx-v2-clients-vendor-badge {{ $isMoldex ? 'moldex' : 'siemens' }}" style="font-size: 8px; font-weight: 900; padding: 2px 6px; border-radius: 4px; width: auto; display: inline-block;">
                                                        {{ $isMoldex ? 'MOLDEX3D' : 'SIEMENS' }}
                                                    </span>
                                                    <span style="font-family: var(--font-mono); font-size: 11px; font-weight: 700; color: #a78bfa;">{{ $daemonVal }}</span>
                                                </div>
                                            </td>
                                            <td style="padding: 12px 24px;">
                                                <span style="font-family: var(--font-mono); font-size: 11px; font-weight: 700; color: #fff;">{{ $result->sold_to ?? 'N/A' }}</span>
                                            </td>
                                            <td style="padding: 12px 24px; font-size: 11px; color: var(--muted); white-space: nowrap;">
                                                <i class="fa-solid fa-calendar-days" style="color: var(--muted); opacity: 0.4; margin-right: 6px;"></i>
                                                {{ $result->created_at->format('d/m/Y H:i') }}
                                            </td>
                                            <td style="padding: 12px 24px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                <span style="font-family: var(--font-mono); font-size: 11px; color: var(--muted); text-transform: uppercase;">{{ $hostName }}</span>
                                            </td>
                                            <td style="padding: 12px 24px; text-align: right;">
                                                <button class="dx-v2-ui-btn dx-v2-ui-btn-secondary" 
                                                        @click="auditDetail = @js($result); $dispatch('open-audit-modal')"
                                                        style="padding: 4px 10px; font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; display: inline-flex; align-items: center; gap: 6px; white-space: nowrap; cursor: pointer;"
                                                        title="Inspeccionar archivo de licencia original">
                                                    <i class="fa-solid fa-eye" style="font-size: 10px;"></i>
                                                    Ver Auditoría
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
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
                                <label class="dx-v2-form-label" for="contact_name">Nombre Completo</label>
                                <input type="text" id="contact_name" name="name" x-model="form.name" required autocomplete="name" class="dx-v2-form-input w-full" placeholder="Ej. Juan Pérez">
                            </div>
                            <div class="dx-v2-form-group" style="margin-bottom: 0;">
                                <label class="dx-v2-form-label" for="contact_email">Email Corporativo</label>
                                <input type="email" id="contact_email" name="email" x-model="form.email" required autocomplete="email" class="dx-v2-form-input w-full" placeholder="email@empresa.com">
                            </div>
                        </div>
                        <div class="dx-v2-form-group" style="margin-bottom: 0;">
                            <label class="dx-v2-form-label" for="contact_position">Cargo / Departamento</label>
                            <input type="text" id="contact_position" name="position" x-model="form.position" autocomplete="organization-title" class="dx-v2-form-input w-full" placeholder="Ej. IT Manager">
                        </div>
                        <div class="dx-v2-form-group" style="margin-bottom: 0;">
                            <label class="dx-v2-form-label" for="contact_phone">Teléfono (Opcional)</label>
                            <input type="text" id="contact_phone" name="phone" x-model="form.phone" autocomplete="tel" class="dx-v2-form-input w-full" placeholder="+34 ...">
                        </div>
                        <div class="dx-v2-form-group" style="margin-bottom: 0; padding-top: 8px;">
                            <label class="dx-v2-form-checkbox-wrapper" for="contact_receives_alerts">
                                <input type="checkbox" id="contact_receives_alerts" name="receives_alerts" value="1" x-model="form.receives_alerts" class="dx-v2-form-checkbox">
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

    <!-- Audit Detail Modal (NOC Pro Inmutable Console) -->
    <template x-teleport="body">
        <div x-data="{ open: false }"
            x-show="open"
            @open-audit-modal.window="open = true"
            class="dx-v2-ui-modal-overlay high-z-index"
            x-cloak
        >
            <div class="dx-v2-ui-modal-content wide" @click.outside="open = false" style="background: #0d0f19; border: 1px solid var(--border); box-shadow: 0 20px 40px rgba(0,0,0,0.65);">
                <div class="dx-v2-ui-modal-header no-border no-padding-bottom" style="padding: 24px 32px 12px 32px; display: flex !important; align-items: center !important; justify-content: space-between !important; width: 100% !important; box-sizing: border-box !important;">
                    <div style="display: flex !important; align-items: center !important; gap: 16px !important;">
                        <div class="dx-v2-clients-audit-icon-box" style="background: rgba(167, 139, 250, 0.1); border: 1px solid rgba(167, 139, 250, 0.2); width: 44px; height: 44px; border-radius: 8px; display: flex !important; align-items: center !important; justify-content: center !important; color: #a78bfa; flex-shrink: 0 !important;">
                            <i class="fa-solid fa-file-invoice" style="font-size: 18px;"></i>
                        </div>
                        <div style="text-align: left !important;">
                            <h3 class="dx-v2-ui-modal-title text-white" style="margin: 0 0 4px 0 !important; line-height: 1.2 !important;" 
                                x-text="(auditDetail?.results?.vendor_daemon || auditDetail?.results?.daemon || '').toLowerCase().includes('moldex') ? 'Detalle de Auditoría Moldex3D' : 'Detalle de Auditoría Siemens'">
                                Detalle de Auditoría
                            </h3>
                            <span style="font-size: 9px; font-weight: 800; color: var(--muted); text-transform: uppercase; letter-spacing: 0.12em; display: block;">Inspección del Respaldo Físico Original</span>
                        </div>
                    </div>
                    <button type="button" @click="open = false" class="dx-v2-ui-modal-close" style="font-size: 28px !important; color: var(--muted) !important; background: transparent !important; border: none !important; cursor: pointer !important; padding: 0 !important; margin: 0 !important; width: 32px !important; height: 32px !important; display: flex !important; align-items: center !important; justify-content: center !important; align-self: center !important; line-height: 1 !important; transition: color 0.2s;" @mouseenter="$el.style.color = '#fff'" @mouseleave="$el.style.color = 'var(--muted)'">&times;</button>
                </div>

                <div class="dx-v2-ui-modal-body p-8" style="padding: 12px 32px 32px 32px;">
                    <template x-if="auditDetail">
                        <div>
                            <!-- Banner de Inmutabilidad Técnica -->
                            <div style="background: rgba(16, 185, 129, 0.04); border: 1px solid rgba(16, 185, 129, 0.15); border-radius: 6px; padding: 12px 16px; margin-bottom: 24px; display: flex; gap: 12px; align-items: center;">
                                <div style="width: 20px; height: 20px; border-radius: 50%; background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.2); display: flex; align-items: center; justify-content: center; color: #10b981; flex-shrink: 0;">
                                    <i class="fa-solid fa-lock" style="font-size: 10px;"></i>
                                </div>
                                <div style="flex-grow: 1;">
                                    <span style="font-size: 11px; font-weight: 800; color: #10b981; text-transform: uppercase; letter-spacing: 0.05em; display: inline-flex; align-items: center; gap: 6px;">
                                        Archivo de Licencia Inmutable 
                                        <span style="font-size: 8px; font-weight: 900; background: rgba(16, 185, 129, 0.15); color: #10b981; padding: 1px 4px; border-radius: 3px;">RESPALDO TÉCNICO</span>
                                    </span>
                                    <p style="font-size: 11px; color: var(--muted); margin: 2px 0 0 0; line-height: 1.4;">
                                        Este registro es una copia exacta e inmutable del archivo subido el 
                                        <span class="text-white font-bold" x-text="auditDetail ? new Date(auditDetail.created_at).toLocaleDateString('es-ES', {day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute:'2-digit'}) : ''"></span>. 
                                        Para modificar el inventario activo de producción actual, edita los bloques de licencias en la pestaña principal.
                                    </p>
                                </div>
                            </div>

                            <!-- Bento Grid de Metadatos del Servidor -->
                            <div class="dx-v2-clients-audit-header-grid" style="margin-bottom: 28px;">
                                <div class="dx-v2-clients-audit-info-card">
                                    <span class="label">ID Cuenta / Sold-To</span>
                                    <span class="value" x-text="auditDetail?.sold_to || 'N/A'"></span>
                                </div>
                                <div class="dx-v2-clients-audit-info-card">
                                    <span class="label">Ecosistema / Daemon</span>
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <span class="value daemon" x-text="auditDetail?.results?.vendor_daemon || auditDetail?.results?.daemon || 'ugslmd'"></span>
                                        <span class="dx-v2-clients-vendor-badge {{ $isMoldex ? 'moldex' : 'siemens' }}" :class="(auditDetail?.results?.vendor_daemon || auditDetail?.results?.daemon || '').toLowerCase().includes('moldex') ? 'moldex' : 'siemens'" style="font-size: 7px; padding: 2px 6px; font-weight: 800; width: auto; display: inline-block;" x-text="(auditDetail?.results?.vendor_daemon || auditDetail?.results?.daemon || '').toLowerCase().includes('moldex') ? 'MOLDEX3D' : 'SIEMENS'">SIEMENS</span>
                                    </div>
                                </div>
                                <div class="dx-v2-clients-audit-info-card span-2">
                                    <span class="label">Servidor / Hostname</span>
                                    <div style="display: flex; align-items: baseline; gap: 8px; flex-wrap: wrap;">
                                        <span class="value hostname" x-text="auditDetail?.results?.hostname || 'PENDIENTE'"></span>
                                        <span style="font-family: var(--font-mono); font-size: 10px; color: var(--muted);" x-text="auditDetail?.results?.composite ? '(COMPOSITE: ' + auditDetail.results.composite + ')' : (auditDetail?.results?.mac ? '(MACHINE ID: ' + auditDetail.results.mac + ')' : '')"></span>
                                    </div>
                                </div>
                            </div>
        
                            <!-- Unified Sold-Tos -->
                            <div class="dx-v2-clients-unified-box" x-show="auditDetail?.results?.additional_sold_tos?.length" style="background: rgba(245, 158, 11, 0.03); border: 1px solid rgba(245, 158, 11, 0.1); border-radius: 6px; padding: 12px 16px; margin-bottom: 28px; display: flex; align-items: center; gap: 12px;">
                                <i class="fa-solid fa-link text-warn" style="font-size: 12px;"></i>
                                <span style="font-size: 10px; font-weight: 800; color: var(--muted); text-transform: uppercase; letter-spacing: 0.05em;">Sold-Tos Unificados en esta Licencia:</span>
                                <div class="flex flex-wrap gap-2">
                                    <template x-for="st in auditDetail?.results?.additional_sold_tos">
                                        <span class="badge badge-muted" style="font-size: 9px; font-family: var(--font-mono); font-weight: 700; background: rgba(255,255,255,0.05); color: #fff; border: 1px solid var(--border);" x-text="st"></span>
                                    </template>
                                </div>
                            </div>
        
                            <!-- Products Table -->
                            <div>
                                <div class="flex justify-between items-center" style="margin-bottom: 12px;">
                                    <h4 style="font-size: 11px; font-weight: 800; color: #fff; text-transform: uppercase; letter-spacing: 0.05em; margin: 0;">Desglose de Líneas de Producto Originales</h4>
                                    <span style="font-size: 10px; color: var(--muted);" x-text="(auditDetail?.results?.products || []).length + ' productos en archivo'"></span>
                                </div>
                                <div class="dx-v2-ui-table-wrapper" style="border: 1px solid var(--border); border-radius: 6px; background: rgba(0,0,0,0.2); max-height: 280px; overflow-y: auto;">
                                    <table class="dx-v2-ui-table" style="margin: 0;">
                                        <thead>
                                            <tr>
                                                <th style="font-size: 9px; padding: 10px 16px;">Código de Producto</th>
                                                <th style="font-size: 9px; padding: 10px 16px;">Descripción Técnica del Módulo</th>
                                                <th style="font-size: 9px; padding: 10px 16px; text-align: center;">Asientos</th>
                                                <th style="font-size: 9px; padding: 10px 16px;">Fecha Expiración</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <template x-for="product in (auditDetail?.results?.products || [])">
                                                <tr style="transition: background 0.15s;">
                                                    <td style="padding: 10px 16px; vertical-align: middle; font-family: var(--font-mono); font-size: 12px; font-weight: 800; color: #fff;" x-text="product.product_code || product.name"></td>
                                                    <td style="padding: 10px 16px; vertical-align: middle; font-size: 11px; color: var(--muted);" x-text="product.description || '—'"></td>
                                                    <td style="padding: 10px 16px; vertical-align: middle; text-align: center;">
                                                        <span class="dx-v2-clients-qty-badge" style="font-size: 10px; font-family: var(--font-mono); font-weight: 700; background: rgba(255,255,255,0.08); padding: 2px 8px; border-radius: 4px; color: #fff;" x-text="product.quantity || product.qty"></span>
                                                    </td>
                                                    <td style="padding: 10px 16px; vertical-align: middle;">
                                                        <span :class="{
                                                            'dx-v2-clients-expiry-status': true,
                                                            'expired': (product.expiration_date || product.expiry || '').toLowerCase().includes('expired') || (product.expiration_date || product.expiry || '').includes('2024') || (product.expiration_date || product.expiry || '').includes('2025'),
                                                            'permanent': (product.expiration_date || product.expiry || '').toLowerCase().includes('permanent') || (product.expiration_date || product.expiry || '') === '',
                                                            'default': !(product.expiration_date || product.expiry || '').toLowerCase().includes('expired') && !(product.expiration_date || product.expiry || '').toLowerCase().includes('permanent')
                                                        }" style="font-size: 10px; font-weight: 700; padding: 2px 6px; border-radius: 4px;">
                                                            <span x-text="product.expiration_date || product.expiry || 'PERMANENTE'"></span>
                                                        </span>
                                                    </td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
                
                <div class="dx-v2-ui-modal-footer transparent no-border no-padding-top" style="padding: 12px 32px 24px 32px; display: flex; justify-content: flex-end; gap: 12px; background: rgba(0,0,0,0.15);">
                    <button type="button" @click="open = false" class="dx-v2-ui-btn dx-v2-ui-btn-secondary" style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">Cerrar Detalle</button>
                    <button type="button" class="dx-v2-ui-btn dx-v2-ui-btn-primary" 
                            @click="navigator.clipboard.writeText(JSON.stringify(auditDetail?.results, null, 4)); alert('Metadatos JSON copiados al portapapeles')"
                            style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; display: inline-flex; align-items: center; gap: 6px;">
                        <i class="fa-solid fa-copy"></i> Copiar Metadatos JSON
                    </button>
                </div>
            </div>
        </div>
    </template>

    <!-- Modal de subida eliminado en favor de subida directa por simplicidad y robustez -->
</div>
@endsection


