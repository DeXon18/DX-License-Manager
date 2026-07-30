@extends('layouts.app')

@section('title', 'Gestor NCmatic — Herramientas')

@section('content')
<div class="dx-v2-page-header">
    <div>
        <div class="breadcrumb">
            <a href="{{ route('tools.index') }}">Herramientas</a>
            <span class="separator">/</span>
            <span class="current">NCmatic</span>
        </div>
        <h1 class="page-title">Gestión de Licencias <span>NCmatic</span></h1>
        <p class="page-subtitle">Asignación y seguimiento de licencias por número de serie</p>
    </div>
    <div class="dx-v2-page-header-actions">
        <button class="dx-v2-ui-btn dx-v2-ui-btn-primary" @click="$dispatch('open-ncmatic-modal')">
            <i class="fa-solid fa-plus mr-2"></i> Nueva Licencia NCmatic
        </button>
    </div>
</div>

<div x-data="{
    modalOpen: false,
    editMode: false,
    form: {
        id: '',
        client_id: '{{ $selectedClientId ?? '' }}',
        serial_number: '',
        license_type: 'MNTO',
        seats: 1,
        expiration_date: '',
        status: 'active',
        notes: ''
    },
    openModal(data = null) {
        if (data) {
            this.editMode = true;
            this.form = { ...data };
        } else {
            this.editMode = false;
            this.form = {
                id: '',
                client_id: '{{ $selectedClientId ?? '' }}',
                serial_number: '',
                license_type: 'MNTO',
                seats: 1,
                expiration_date: '',
                status: 'active',
                notes: ''
            };
        }
        this.modalOpen = true;
    }
}" @open-ncmatic-modal.window="openModal($event.detail)">

    <!-- Filtros de búsqueda -->
    <div class="card mb-4">
        <div class="card-body" style="padding: 16px;">
            <form action="{{ route('tools.ncmatic.index') }}" method="GET" style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
                <div style="flex: 1; min-width: 280px; position: relative;">
                    <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--dx-v2-muted); font-size: 13px;"></i>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Buscar por número de serie, cliente o notas..." class="dx-v2-form-input" style="padding-left: 36px; width: 100%;">
                </div>

                <div style="width: 260px;">
                    <select name="client_id" class="dx-v2-form-select" style="width: 100%;" onchange="this.form.submit()">
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
    <div class="card">
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
                                <span class="mono" style="font-size: 13px; font-weight: 700;">{{ $lic->serial_number }}</span>
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
                                    <button @click="openModal({
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

    <!-- Modal Formulario NCmatic -->
    <div x-show="modalOpen" x-cloak class="modal-overlay">
        <div class="modal-content" @click.away="modalOpen = false">
            <div class="modal-header">
                <h3 class="card-title" x-text="editMode ? 'Editar Licencia NCmatic' : 'Nueva Licencia NCmatic'"></h3>
                <button @click="modalOpen = false" class="dx-v2-ui-btn dx-v2-ui-btn-ghost">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form action="{{ route('tools.ncmatic.store') }}" method="POST">
                @csrf
                <input type="hidden" name="id" :value="form.id">

                <div class="modal-body">
                    <div class="dx-v2-form-group mb-3">
                        <label class="dx-v2-form-label">Cliente *</label>
                        <select name="client_id" x-model="form.client_id" required class="dx-v2-form-select" style="width: 100%;">
                            <option value="">-- Seleccionar Cliente --</option>
                            @foreach($clients as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="dx-v2-form-group mb-3">
                        <label class="dx-v2-form-label">Número de Serie / Clave *</label>
                        <input type="text" name="serial_number" x-model="form.serial_number" required placeholder="Ej: NCM-2026-99482X" class="dx-v2-form-input mono" style="width: 100%;">
                    </div>

                    <div class="dx-v2-form-group mb-3">
                        <label class="dx-v2-form-label">Tipo / Modalidad</label>
                        <select name="license_type" x-model="form.license_type" class="dx-v2-form-select" style="width: 100%;">
                            <option value="MNTO">NCMATIC MNTO (Mantenimiento)</option>
                            <option value="ALQ">NCMATIC ALQ (Alquiler)</option>
                            <option value="PERMANENT">NCmatic Permanente</option>
                            <option value="TRIAL">NCmatic Prueba / Demostración</option>
                        </select>
                    </div>

                    <div class="grid-2-col mb-3">
                        <div class="dx-v2-form-group">
                            <label class="dx-v2-form-label">Fecha de Expiración</label>
                            <input type="date" name="expiration_date" x-model="form.expiration_date" class="dx-v2-form-input" style="width: 100%;">
                        </div>
                        <div class="dx-v2-form-group">
                            <label class="dx-v2-form-label">Estado</label>
                            <select name="status" x-model="form.status" class="dx-v2-form-select" style="width: 100%;">
                                <option value="active">Activo</option>
                                <option value="dropped">Baja</option>
                                <option value="expired">Expirado</option>
                            </select>
                        </div>
                    </div>

                    <div class="dx-v2-form-group">
                        <label class="dx-v2-form-label">Notas / Observaciones</label>
                        <textarea name="notes" x-model="form.notes" rows="3" placeholder="Información sobre la versión, instalación o contrato..." class="dx-v2-form-textarea" style="width: 100%;"></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" @click="modalOpen = false" class="dx-v2-ui-btn dx-v2-ui-btn-ghost">Cancelar</button>
                    <button type="submit" class="dx-v2-ui-btn dx-v2-ui-btn-primary">Guardar Licencia</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
