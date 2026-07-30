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
    <div class="card p-4 mb-4">
        <form action="{{ route('tools.ncmatic.index') }}" method="GET" class="flex gap-4 flex-wrap items-center">
            <div class="flex-1 min-w-200">
                <input type="text" name="search" value="{{ $search }}" placeholder="Buscar por número de serie, cliente o notas..." class="dx-v2-ui-input w-full">
            </div>

            <div class="w-250">
                <select name="client_id" class="dx-v2-ui-input w-full">
                    <option value="">-- Todos los Clientes --</option>
                    @foreach($clients as $c)
                        <option value="{{ $c->id }}" {{ $selectedClientId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="dx-v2-ui-btn dx-v2-ui-btn-secondary">
                <i class="fa-solid fa-magnifying-glass mr-2"></i> Buscar
            </button>

            @if($search || $selectedClientId)
                <a href="{{ route('tools.ncmatic.index') }}" class="dx-v2-ui-btn dx-v2-ui-btn-ghost">
                    <i class="fa-solid fa-xmark mr-1"></i> Limpiar
                </a>
            @endif
        </form>
    </div>

    <!-- Tabla de Licencias NCmatic -->
    <div class="card p-0">
        <div class="dx-v2-ui-table-wrapper">
            <table class="dx-v2-ui-table">
                <thead>
                    <tr>
                        <th>Cliente</th>
                        <th>Número de Serie</th>
                        <th>Tipo / Modalidad</th>
                        <th class="text-center">Asientos</th>
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
                                <a href="{{ route('clients.show', $lic->client_id) }}" class="text-accent hover:underline">
                                    {{ $lic->client->name ?? 'Cliente Desconocido' }}
                                </a>
                            </td>
                            <td>
                                <span class="font-mono bg-muted/20 px-2 py-1 rounded font-bold text-sm">{{ $lic->serial_number }}</span>
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
                            <td class="text-center font-bold">{{ $lic->seats }}</td>
                            <td>
                                @if($lic->expiration_date)
                                    <span class="{{ $lic->expiration_date->isPast() ? 'text-danger font-bold' : '' }}">
                                        {{ $lic->expiration_date->format('d/m/Y') }}
                                    </span>
                                @else
                                    <span class="muted">Permanente / Sin fecha</span>
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
                            <td class="text-sm muted max-w-200 truncate" title="{{ $lic->notes }}">
                                {{ $lic->notes ?: '—' }}
                            </td>
                            <td class="text-right">
                                <div class="flex justify-end gap-2">
                                    <button @click="openModal({
                                        id: {{ $lic->id }},
                                        client_id: {{ $lic->client_id }},
                                        serial_number: '{{ addslashes($lic->serial_number) }}',
                                        license_type: '{{ $lic->license_type }}',
                                        seats: {{ $lic->seats }},
                                        expiration_date: '{{ $lic->expiration_date ? $lic->expiration_date->format('Y-m-d') : '' }}',
                                        status: '{{ $lic->status }}',
                                        notes: '{{ addslashes($lic->notes) }}'
                                    })" class="dx-v2-clients-btn-action-tool" title="Editar">
                                        <i class="fa-solid fa-pen text-accent"></i>
                                    </button>

                                    <form action="{{ route('tools.ncmatic.destroy', $lic) }}" method="POST" onsubmit="return confirm('¿Eliminar esta licencia NCmatic?')" class="inline">
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
                            <td colspan="8" class="text-center py-12 muted">
                                No hay licencias de NCmatic registradas en el sistema.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($licenses->hasPages())
            <div class="p-4 border-t border-border">
                {{ $licenses->links('vendor.pagination.dx-jump') }}
            </div>
        @endif
    </div>

    <!-- Modal Formulario NCmatic -->
    <div x-show="modalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" style="background: rgba(0,0,0,0.6);">
        <div class="card w-full max-w-lg p-6 bg-surface shadow-2xl rounded-xl" @click.away="modalOpen = false">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold" x-text="editMode ? 'Editar Licencia NCmatic' : 'Nueva Licencia NCmatic'"></h3>
                <button @click="modalOpen = false" class="text-muted hover:text-foreground">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>

            <form action="{{ route('tools.ncmatic.store') }}" method="POST">
                @csrf
                <input type="hidden" name="id" :value="form.id">

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider mb-1">Cliente *</label>
                        <select name="client_id" x-model="form.client_id" required class="dx-v2-ui-input w-full">
                            <option value="">-- Seleccionar Cliente --</option>
                            @foreach($clients as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider mb-1">Número de Serie / Clave *</label>
                        <input type="text" name="serial_number" x-model="form.serial_number" required placeholder="Ej: NCM-2026-99482X" class="dx-v2-ui-input w-full font-mono">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider mb-1">Tipo / Modalidad</label>
                            <select name="license_type" x-model="form.license_type" class="dx-v2-ui-input w-full">
                                <option value="MNTO">NCMATIC MNTO (Mantenimiento)</option>
                                <option value="ALQ">NCMATIC ALQ (Alquiler)</option>
                                <option value="PERMANENT">NCmatic Permanente</option>
                                <option value="TRIAL">NCmatic Prueba / Demostración</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider mb-1">Puestos / Asientos</label>
                            <input type="number" name="seats" x-model="form.seats" min="1" required class="dx-v2-ui-input w-full">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider mb-1">Fecha de Expiración</label>
                            <input type="date" name="expiration_date" x-model="form.expiration_date" class="dx-v2-ui-input w-full">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider mb-1">Estado</label>
                            <select name="status" x-model="form.status" class="dx-v2-ui-input w-full">
                                <option value="active">Activo</option>
                                <option value="dropped">Baja</option>
                                <option value="expired">Expirado</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider mb-1">Notas / Observaciones</label>
                        <textarea name="notes" x-model="form.notes" rows="3" placeholder="Información sobre la versión, instalación o contrato..." class="dx-v2-ui-input w-full"></textarea>
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-border">
                    <button type="button" @click="modalOpen = false" class="dx-v2-ui-btn dx-v2-ui-btn-ghost">Cancelar</button>
                    <button type="submit" class="dx-v2-ui-btn dx-v2-ui-btn-primary">Guardar Licencia</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
