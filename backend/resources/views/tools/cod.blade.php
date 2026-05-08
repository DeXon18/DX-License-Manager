@extends('layouts.app')

@section('title', 'Generador de COD - DX Portal')

@section('content')
<div class="container-fluid" x-data="codGenerator()">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-0 text-gray-800">Generador de Certificado de Cese (COD)</h1>
            <p class="text-muted">Generación de documentos oficiales Siemens para cambios de licencia.</p>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-file-signature me-2"></i>Nueva Solicitud
                    </h6>
                </div>
                <div class="card-body">
                    <form id="codForm" @submit.prevent="generate('store')">
                        @csrf
                        
                        <!-- Datos de la Empresa -->
                        <div class="section-title">
                            <i class="fas fa-building me-2"></i>Datos de la Empresa
                        </div>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="field-label">Cliente en Portal</label>
                                <select name="client_id" class="gui-input ps-2" x-model="formData.client_id" @change="updateFromClient()" required>
                                    <option value="">Seleccionar cliente...</option>
                                    @foreach($clients as $client)
                                        <option value="{{ $client->id }}">{{ $client->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="field-label">Sold-To / Licencia</label>
                                <input type="text" name="Data_SoldTo" class="gui-input ps-2" x-model="formData.Data_SoldTo" placeholder="Ej: 10303508" required maxlength="10">
                            </div>
                            <div class="col-md-6">
                                <label class="field-label">Solicitante</label>
                                <input type="text" name="Data_Solicitante" class="gui-input ps-2" x-model="formData.Data_Solicitante" placeholder="Nombre completo" required>
                            </div>
                            <div class="col-md-6">
                                <label class="field-label">Empresa (Tal cual en PDF)</label>
                                <input type="text" name="Data_Empresa" class="gui-input ps-2" x-model="formData.Data_Empresa" placeholder="Razón social" required>
                            </div>
                        </div>

                        <!-- Tipo de Solicitud e Idioma -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="field-label">Tipo de Cambio</label>
                                <div class="segmented">
                                    <button type="button" class="seg-btn" :class="formData.docType === 'Change_Full' ? 'active' : ''" @click="formData.docType = 'Change_Full'">
                                        <span>Completo</span>
                                    </button>
                                    <button type="button" class="seg-btn" :class="formData.docType === 'Change_Composite' ? 'active' : ''" @click="formData.docType = 'Change_Composite'">
                                        <span>Composite</span>
                                    </button>
                                    <button type="button" class="seg-btn" :class="formData.docType === 'Change_NodeLocked' ? 'active' : ''" @click="formData.docType = 'Change_NodeLocked'">
                                        <span>NodeLocked</span>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="field-label">Idioma PDF</label>
                                <div class="segmented">
                                    <button type="button" class="seg-btn" :class="formData.Language === 'Spanish' ? 'active' : ''" @click="formData.Language = 'Spanish'">
                                        <span>ES</span>
                                    </button>
                                    <button type="button" class="seg-btn" :class="formData.Language === 'English' ? 'active' : ''" @click="formData.Language = 'English'">
                                        <span>EN</span>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="field-label">S.O. Servidor</label>
                                <div class="segmented">
                                    <button type="button" class="seg-btn" :class="formData.os === 'WINDOWS' ? 'active' : ''" @click="formData.os = 'WINDOWS'">
                                        <i class="fab fa-windows me-1"></i>Win
                                    </button>
                                    <button type="button" class="seg-btn" :class="formData.os === 'LINUX' ? 'active' : ''" @click="formData.os = 'LINUX'">
                                        <i class="fab fa-linux me-1"></i>Lin
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Detalles de Máquinas -->
                        <div class="row g-4 mb-4">
                            <!-- Máquina Actual -->
                            <div class="col-md-6">
                                <div class="section-title">
                                    <i class="fas fa-server me-2"></i>Máquina Actual
                                </div>
                                <div class="mb-3">
                                    <label class="field-label">Hostname</label>
                                    <input type="text" name="Hostname_Old" class="gui-input ps-2" x-model="formData.Hostname_Old" :required="formData.docType !== 'Change_NodeLocked'" :disabled="formData.docType === 'Change_NodeLocked'">
                                </div>
                                <div class="mb-3">
                                    <label class="field-label">Composite ID</label>
                                    <input type="text" name="Composite_Old" class="gui-input ps-2" x-model="formData.Composite_Old" :required="formData.docType !== 'Change_NodeLocked'" :disabled="formData.docType === 'Change_NodeLocked'" maxlength="12">
                                </div>
                                <div class="mb-3">
                                    <label class="field-label">HostID (MAC)</label>
                                    <input type="text" name="MAC_Old" class="gui-input ps-2" x-model="formData.MAC_Old" :required="formData.docType !== 'Change_Composite'" :disabled="formData.docType === 'Change_Composite'" maxlength="12">
                                </div>

                                <!-- MACs Adicionales Actuales -->
                                <template x-for="(mac, index) in formData.MAC_Old_Extra" :key="index">
                                    <div class="input-group mb-2">
                                        <input type="text" :name="'MAC_Old_Extra['+index+']'" class="gui-input ps-2" x-model="formData.MAC_Old_Extra[index]" placeholder="MAC Adicional" maxlength="12">
                                        <button type="button" class="btn btn-outline-danger btn-sm" @click="removeMacPair(index)"><i class="fas fa-times"></i></button>
                                    </div>
                                </template>
                            </div>

                            <!-- Nueva Máquina -->
                            <div class="col-md-6">
                                <div class="section-title">
                                    <i class="fas fa-satellite-dish me-2"></i>Nueva Máquina
                                </div>
                                <div class="mb-3">
                                    <label class="field-label">Hostname</label>
                                    <input type="text" name="Hostname_New" class="gui-input ps-2" x-model="formData.Hostname_New" :required="formData.docType !== 'Change_NodeLocked'" :disabled="formData.docType === 'Change_NodeLocked'">
                                </div>
                                <div class="mb-3">
                                    <label class="field-label">Composite ID</label>
                                    <input type="text" name="Composite_New" class="gui-input ps-2" x-model="formData.Composite_New" :required="formData.docType !== 'Change_NodeLocked'" :disabled="formData.docType === 'Change_NodeLocked'" maxlength="12">
                                </div>
                                <div class="mb-3">
                                    <label class="field-label">HostID (MAC)</label>
                                    <input type="text" name="MAC_New" class="gui-input ps-2" x-model="formData.MAC_New" :required="formData.docType !== 'Change_Composite'" :disabled="formData.docType === 'Change_Composite'" maxlength="12">
                                </div>

                                <!-- MACs Adicionales Nuevas -->
                                <template x-for="(mac, index) in formData.MAC_New_Extra" :key="index">
                                    <div class="mb-2">
                                        <input type="text" :name="'MAC_New_Extra['+index+']'" class="gui-input ps-2" x-model="formData.MAC_New_Extra[index]" placeholder="Nueva MAC Adicional" maxlength="12">
                                    </div>
                                </template>
                            </div>
                        </div>

                        <div class="mb-4" x-show="formData.docType === 'Change_NodeLocked'">
                            <button type="button" class="btn btn-sm btn-outline-primary" @click="addMacPair()">
                                <i class="fas fa-plus me-2"></i>Añadir par de MACs
                            </button>
                        </div>

                        <div class="d-flex justify-content-between border-top pt-4">
                            <button type="button" class="btn btn-secondary" @click="resetForm()">
                                <i class="fas fa-eraser me-2"></i>Limpiar
                            </button>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-primary" @click="generate('preview')">
                                    <i class="fas fa-eye me-2"></i>Previsualizar
                                </button>
                                <button type="submit" class="btn btn-primary" :disabled="isGenerating" style="background: var(--siemens);">
                                    <span x-show="!isGenerating"><i class="fas fa-file-pdf me-2"></i>Generar y Guardar</span>
                                    <span x-show="isGenerating"><i class="fas fa-spinner fa-spin me-2"></i>Procesando...</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Instrucciones</h6>
                </div>
                <div class="card-body small text-muted">
                    <p>Este generador replica el <strong>Certificado de Cese oficial de Siemens</strong>.</p>
                    <ul class="ps-3">
                        <li><strong>Cambio Completo:</strong> Transfiere licencia fija completa (Composite + MAC).</li>
                        <li><strong>Cambio Composite:</strong> Solo afecta a la licencia flotante.</li>
                        <li><strong>NodeLocked:</strong> Solo cambia la MAC del equipo.</li>
                    </ul>
                    <div class="alert alert-info py-2 border-0">
                        <i class="fas fa-info-circle me-2"></i>El documento se guardará automáticamente en el repositorio del cliente.
                    </div>
                </div>
            </div>

            <!-- Previsualización Inline -->
            <div class="card shadow-sm border-0 sticky-top" style="top: 2rem;" x-show="showPreview">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Vista Previa</h6>
                    <button class="btn btn-sm btn-link text-muted" @click="showPreview = false"><i class="fas fa-times"></i></button>
                </div>
                <div class="card-body p-0" style="height: 500px;">
                    <iframe :src="previewUrl" width="100%" height="100%" frameborder="0"></iframe>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function codGenerator() {
    return {
        isGenerating: false,
        showPreview: false,
        previewUrl: '',
        formData: {
            client_id: '{{ $selectedClient ? $selectedClient->id : "" }}',
            Data_SoldTo: '',
            Data_Solicitante: '',
            Data_Empresa: '{{ $selectedClient ? $selectedClient->name : "" }}',
            docType: 'Change_Full',
            Language: 'Spanish',
            os: 'WINDOWS',
            Hostname_Old: '',
            Composite_Old: '',
            MAC_Old: '',
            Hostname_New: '',
            Composite_New: '',
            MAC_New: '',
            MAC_Old_Extra: [],
            MAC_New_Extra: []
        },

        init() {
            if (this.formData.client_id) {
                // Pre-fill logic if needed
            }
        },

        updateFromClient() {
            const clientSelect = document.querySelector('select[name="client_id"]');
            const clientName = clientSelect.options[clientSelect.selectedIndex].text;
            if (this.formData.client_id) {
                this.formData.Data_Empresa = clientName;
            }
        },

        addMacPair() {
            this.formData.MAC_Old_Extra.push('');
            this.formData.MAC_New_Extra.push('');
        },

        removeMacPair(index) {
            this.formData.MAC_Old_Extra.splice(index, 1);
            this.formData.MAC_New_Extra.splice(index, 1);
        },

        resetForm() {
            this.formData = {
                client_id: '',
                Data_SoldTo: '',
                Data_Solicitante: '',
                Data_Empresa: '',
                docType: 'Change_Full',
                Language: 'Spanish',
                os: 'WINDOWS',
                Hostname_Old: '',
                Composite_Old: '',
                MAC_Old: '',
                Hostname_New: '',
                Composite_New: '',
                MAC_New: '',
                MAC_Old_Extra: [],
                MAC_New_Extra: []
            };
            this.showPreview = false;
        },

        async generate(mode) {
            if (mode === 'preview') {
                this.isGenerating = true;
                const response = await fetch('{{ route("tools.cod.preview") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(this.formData)
                });
                
                const blob = await response.blob();
                this.previewUrl = URL.createObjectURL(blob);
                this.showPreview = true;
                this.isGenerating = false;
            } else {
                this.isGenerating = true;
                try {
                    const response = await fetch('{{ route("tools.cod.store") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(this.formData)
                    });
                    
                    const result = await response.json();
                    
                    if (result.success) {
                        alert(result.message);
                        window.location.href = result.download_url;
                    } else {
                        alert('Error: ' + result.message);
                    }
                } catch (e) {
                    alert('Error en la comunicación con el servidor.');
                }
                this.isGenerating = false;
            }
        }
    }
}
</script>
@endpush
@endsection
