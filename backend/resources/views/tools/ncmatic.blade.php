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
    showForm: true,
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
        // Do not hide the form, just clear it
    }
}">

    <div class="main-panel">
        <!-- Panel de Registro / Edición de Licencia NCmatic -->
        <div class="card dx-v2-tools-ncmatic-card">
            <div class="card-header dx-v2-tools-ncmatic-card-header">
                <div class="dx-v2-tools-ncmatic-card-title-group">
                    <i class="fa-solid fa-key dx-v2-tools-ncmatic-card-icon"></i>
                    <span class="card-title" x-text="editMode ? 'Edición de Licencia NCmatic' : 'Registro de Nueva Licencia NCmatic'"></span>
                </div>
                <div class="dx-v2-tools-ncmatic-card-actions">
                    <span class="dx-v2-tools-ncmatic-badge" x-show="editMode">MODO EDICIÓN</span>
                    <button type="button" @click="resetForm()" class="btn-ghost" x-show="editMode">
                        <i class="fa-solid fa-plus"></i>
                        <span> Nueva Licencia</span>
                    </button>
                </div>
            </div>

            <div class="card-body" x-show="showForm">
                <form id="ncmatic-form" action="{{ route('tools.ncmatic.store') }}" method="POST">
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
                        <button type="submit" class="btn-primary dx-v2-tools-ncmatic-btn-submit">
                            <i class="fa-solid fa-floppy-disk"></i>
                            <span x-text="editMode ? 'Actualizar Licencia NCmatic' : 'Guardar y Asignar Licencia'"></span>
                        </button>
                    </div>

                </form>
            </div>
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
