@extends('layouts.app')

@section('title', 'Gestor NCmatic — Herramientas')

@section('content')
<div class="page-header">
    <div class="breadcrumb">
        <a href="{{ url('/') }}">Portal</a> ›
        <a href="{{ route('tools.index') }}">Herramientas</a> ›
        NCmatic
    </div>
    <div class="dx-v2-tools-ncmatic-header-layout">
        <div class="dx-v2-tools-ncmatic-header-icon">
            <i class="fa-solid fa-key"></i>
        </div>
        <div>
            <h1 class="dx-v2-tools-ncmatic-header-title">Gestión de Licencias NCmatic <span class="dx-v2-tools-ncmatic-vendor-label">SOLUTION PARTNER</span></h1>
            <p class="dx-v2-tools-ncmatic-header-sub">Asignación, registro y seguimiento de licencias asociadas a números de serie por cliente</p>
        </div>
    </div>
</div>

<div class="grid-main" x-data="{
    showForm: {{ $selectedClientId ? 'true' : 'false' }},
    editMode: false,
    form: {
        id: '',
        client_id: '{{ $selectedClientId ?? '' }}',
        serial_number: '',
        license_type: 'MNTO',
        expiration_date: '',
        status: 'active',
        notes: ''
    },
    toggleForm(data = null) {
        if (data) {
            this.editMode = true;
            this.form = { ...data };
            this.showForm = true;
        } else {
            if (this.showForm && !this.editMode) {
                this.showForm = false;
            } else {
                this.editMode = false;
                this.form = {
                    id: '',
                    client_id: '{{ $selectedClientId ?? '' }}',
                    serial_number: '',
                    license_type: 'MNTO',
                    expiration_date: '',
                    status: 'active',
                    notes: ''
                };
                this.showForm = true;
            }
        }
    },
    resetForm() {
        this.editMode = false;
        this.form = {
            id: '',
            client_id: '{{ $selectedClientId ?? '' }}',
            serial_number: '',
            license_type: 'MNTO',
            expiration_date: '',
            status: 'active',
            notes: ''
        };
        this.showForm = false;
    }
}">

    <div class="main-panel">
        <!-- Panel de Registro / Edición de Licencia NCmatic -->
        <div class="card dx-v2-tools-ncmatic-card">
            <div class="card-header dx-v2-tools-ncmatic-card-header">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-key" style="color: var(--dx-v2-vendor-ncmatic);"></i>
                    <span class="card-title" x-text="editMode ? 'Edición de Licencia NCmatic' : 'Registro de Nueva Licencia NCmatic'"></span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="dx-v2-tools-ncmatic-badge" x-text="editMode ? 'MODO EDICIÓN' : 'ASIGNACIÓN DE SERIE'"></span>
                    <button type="button" @click="toggleForm()" class="dx-v2-ui-btn dx-v2-ui-btn-ghost">
                        <i class="fa-solid" :class="showForm ? 'fa-chevron-up' : 'fa-plus'"></i>
                        <span x-text="showForm ? ' Ocultar' : ' Nueva Licencia'"></span>
                    </button>
                </div>
            </div>

            <div class="card-body" x-show="showForm" x-collapse>
                <form action="{{ route('tools.ncmatic.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id" :value="form.id">

                    <div class="dx-v2-tools-ncmatic-form-grid">
                        <div class="dx-v2-form-group">
                            <label class="dx-v2-form-label">Cliente Asociado *</label>
                            <select name="client_id" x-model="form.client_id" required class="dx-v2-form-select">
                                <option value="">-- Seleccionar Cliente --</option>
                                @foreach($clients as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="dx-v2-form-group">
                            <label class="dx-v2-form-label">Número de Serie / Clave *</label>
                            <input type="text" name="serial_number" x-model="form.serial_number" required placeholder="Ej: NCM-2026-99482X" class="dx-v2-form-input mono">
                        </div>

                        <div class="dx-v2-form-group">
                            <label class="dx-v2-form-label">Tipo / Modalidad</label>
                            <select name="license_type" x-model="form.license_type" class="dx-v2-form-select">
                                <option value="MNTO">NCMATIC MNTO (Mantenimiento)</option>
                                <option value="ALQ">NCMATIC ALQ (Alquiler)</option>
                                <option value="PERMANENT">NCmatic Permanente</option>
                                <option value="TRIAL">NCmatic Prueba / Demostración</option>
                            </select>
                        </div>
                    </div>

                    <div class="dx-v2-tools-ncmatic-form-grid-secondary">
                        <div class="dx-v2-form-group">
                            <label class="dx-v2-form-label">Fecha de Expiración</label>
                            <input type="date" name="expiration_date" x-model="form.expiration_date" class="dx-v2-form-input mono">
                        </div>

                        <div class="dx-v2-form-group">
                            <label class="dx-v2-form-label">Estado de la Licencia</label>
                            <select name="status" x-model="form.status" class="dx-v2-form-select">
                                <option value="active">Activo</option>
                                <option value="dropped">Baja</option>
                                <option value="expired">Expirado</option>
                            </select>
                        </div>

                        <div class="dx-v2-form-group" style="grid-column: span 2;">
                            <label class="dx-v2-form-label">Notas / Observaciones</label>
                            <input type="text" name="notes" x-model="form.notes" placeholder="Detalles de instalación, contrato o versión..." class="dx-v2-form-input">
                        </div>
                    </div>

                    <div class="dx-v2-tools-ncmatic-form-actions">
                        <button type="button" @click="resetForm()" class="dx-v2-ui-btn dx-v2-ui-btn-ghost">Cancelar</button>
                        <button type="submit" class="dx-v2-ui-btn dx-v2-tools-ncmatic-btn-submit">
                            <i class="fa-solid fa-floppy-disk mr-2"></i>
                            <span x-text="editMode ? 'Actualizar Licencia NCmatic' : 'Guardar y Asignar Licencia'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Filtros de búsqueda -->
        <div class="card mb-4">
            <div class="card-body">
                <form action="{{ route('tools.ncmatic.index') }}" method="GET" class="dx-v2-tools-ncmatic-search-bar">
                    <div class="dx-v2-tools-ncmatic-search-input-wrap">
                        <i class="fa-solid fa-magnifying-glass dx-v2-tools-ncmatic-search-icon"></i>
                        <input type="text" name="search" value="{{ $search }}" placeholder="Buscar por número de serie, cliente o notas..." class="dx-v2-form-input dx-v2-tools-ncmatic-search-input">
                    </div>

                    <div class="dx-v2-tools-ncmatic-select-wrap">
                        <select name="client_id" class="dx-v2-form-select" onchange="this.form.submit()">
                            <option value="">-- Todos los Clientes --</option>
                            @foreach($clients as $c)
                                <option value="{{ $c->id }}" {{ $selectedClientId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="dx-v2-ui-btn dx-v2-ui-btn-secondary">
                        <i class="fa-solid fa-magnifying-glass"></i> Buscar
                    </button>

                    @if($search || $selectedClientId)
                        <a href="{{ route('tools.ncmatic.index') }}" class="dx-v2-ui-btn dx-v2-ui-btn-ghost">
                            <i class="fa-solid fa-xmark"></i> Limpiar
                        </a>
                    @endif
                </form>
            </div>
        </div>

        <!-- Tabla de Licencias NCmatic -->
        <div class="card dx-v2-tools-ncmatic-card">
            <div class="card-header dx-v2-tools-ncmatic-card-header">
                <span class="card-title">Inventario de Licencias NCmatic</span>
                <span class="dx-v2-tools-ncmatic-badge">REGISTROS EXISTENTES</span>
            </div>
            <div class="dx-v2-ui-table-wrapper">
                <table class="dx-v2-ui-table">
                    <thead>
                        <tr>
                            <th>Cliente</th>
                            <th>Número de Serie</th>
                            <th>Tipo / Modalidad</th>
                            <th>Vencimiento</th>
                            <th>Estado</th>
                            <th>Notas</th>
                            <th class="text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($licenses as $lic)
                            <tr>
                                <td class="font-bold">
                                    <a href="{{ route('clients.show', $lic->client_id) }}" class="dx-v2-link">
                                        {{ $lic->client->name ?? 'Cliente Desconocido' }}
                                    </a>
                                </td>
                                <td>
                                    <span class="mono">{{ $lic->serial_number }}</span>
                                </td>
                                <td>
                                    @php
                                        $typeBadgeClass = match($lic->license_type) {
                                            'MNTO' => 'badge-info',
                                            'ALQ' => 'badge-warn',
                                            'PERMANENT' => 'badge-success',
                                            default => 'badge-muted'
                                        };
                                    @endphp
                                    <span class="badge {{ $typeBadgeClass }}">{{ $lic->license_type }}</span>
                                </td>
                                <td>
                                    @if($lic->expiration_date)
                                        <span class="{{ $lic->expiration_date->isPast() ? 'badge badge-danger' : 'mono' }}">
                                            {{ $lic->expiration_date->format('d/m/Y') }}
                                        </span>
                                    @else
                                        <span class="badge badge-muted">Permanente</span>
                                    @endif
                                </td>
                                <td>
                                    @if($lic->status === 'active')
                                        <span class="badge badge-success">Activo</span>
                                    @elseif($lic->status === 'dropped')
                                        <span class="badge badge-danger">Baja</span>
                                    @else
                                        <span class="badge badge-warn">Expirado</span>
                                    @endif
                                </td>
                                <td class="body-sm" title="{{ $lic->notes }}">
                                    {{ $lic->notes ?: '—' }}
                                </td>
                                <td class="text-right">
                                    <div class="dx-v2-clients-contacts-actions">
                                        <button @click="toggleForm({
                                            id: {{ $lic->id }},
                                            client_id: {{ $lic->client_id }},
                                            serial_number: '{{ addslashes($lic->serial_number) }}',
                                            license_type: '{{ $lic->license_type }}',
                                            expiration_date: '{{ $lic->expiration_date ? $lic->expiration_date->format('Y-m-d') : '' }}',
                                            status: '{{ $lic->status }}',
                                            notes: '{{ addslashes($lic->notes) }}'
                                        })" class="dx-v2-clients-btn-action-tool" title="Editar">
                                            <i class="fa-solid fa-pen text-accent"></i>
                                        </button>

                                        <form action="{{ route('tools.ncmatic.destroy', $lic) }}" method="POST" onsubmit="return confirm('¿Eliminar esta licencia NCmatic?')" class="display-contents">
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
                                <td colspan="7" class="text-center py-12 muted">
                                    No hay licencias de NCmatic registradas en el sistema.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($licenses->hasPages())
                <div class="card-footer">
                    {{ $licenses->links('vendor.pagination.dx-jump') }}
                </div>
            @endif
        </div>
    </div>

    <!-- Sidebar Panel Informativo -->
    <div class="sidebar-panel">
        <div class="dx-v2-tools-ncmatic-sidebar-card">
            <div class="dx-v2-tools-ncmatic-sidebar-title">Identificación por Serie</div>
            <div class="dx-v2-tools-ncmatic-sidebar-layout">
                <div class="dx-v2-tools-ncmatic-sidebar-code-box">
                    NCM-AÑO-XXXXXX
                </div>
                <p class="dx-v2-tools-ncmatic-sidebar-desc">
                    Las licencias de NCmatic se gestionan de forma individual por puesto mediante la clave única de número de serie asignada a la empresa del cliente.
                </p>
            </div>
        </div>

        <div class="dx-v2-tools-ncmatic-sidebar-info">
            <div class="dx-v2-tools-ncmatic-sidebar-info-header">
                <i class="fa-solid fa-circle-info"></i>
                <span class="dx-v2-tools-ncmatic-sidebar-info-title">Modalidades Soportadas</span>
            </div>
            <p class="dx-v2-tools-ncmatic-sidebar-info-text">
                <strong>MNTO:</strong> Mantenimiento anual.<br>
                <strong>ALQ:</strong> Licencia temporal de alquiler.<br>
                <strong>PERMANENT:</strong> Licencia vitalicia.<br>
                <strong>TRIAL:</strong> Licencia de demostración.
            </p>
        </div>
    </div>
</div>
@endsection
