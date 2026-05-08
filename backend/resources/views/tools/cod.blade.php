@extends('layouts.app')

@section('title', 'Generador de COD - DX Portal')

@section('content')
<div class="page-header">
    <div class="breadcrumb">Portal › Herramientas › Siemens › Generador COD</div>
    <h1 class="page-title">Solicitud de Cambio de Licencia</h1>
    <p class="page-sub">Generación de Certificado de Cese (COD) oficial de Siemens Digital Industries Software</p>
</div>

<div x-data="codGenerator()" class="cod-container">
    <form id="codForm" @submit.prevent="generate('store')" class="cod-card shadow-premium">
        @csrf
        
        <!-- Header del Formulario -->
        <div class="cod-card-header">
            <div class="flex items-center gap-3">
                <div class="header-icon">
                    <i class="fa-solid fa-file-signature"></i>
                </div>
                <h2 class="header-title">Certificado de Cese</h2>
            </div>
            <div class="header-line"></div>
        </div>

        <div class="cod-card-body">
            <!-- SECCIÓN: DATOS DE LA EMPRESA -->
            <div class="form-section">
                <div class="section-title">
                    <i class="fa-solid fa-building"></i>
                    <span>Datos de la Empresa</span>
                </div>

                <div class="fields-container">
                    <!-- Fila 1: Sold To (Ancho completo) -->
                    <div class="field-row">
                        <div class="input-wrap">
                            <i class="fa-solid fa-shield-halved"></i>
                            <input type="text" x-model="formData.Data_SoldTo" class="gui-input" placeholder="Número de licencia (Sold To)" required maxlength="10">
                        </div>
                    </div>

                    <!-- Fila 2: Solicitante y Empresa (50/50) -->
                    <div class="columns-2 mt-4">
                        <div class="field-row">
                            <div class="input-wrap">
                                <i class="fa-solid fa-user"></i>
                                <input type="text" x-model="formData.Data_Solicitante" class="gui-input" placeholder="Solicitante" required>
                            </div>
                        </div>
                        <div class="field-row" x-data="{ showSuggestions: false }" @click.away="showSuggestions = false">
                            <div class="input-wrap">
                                <i class="fa-solid fa-globe"></i>
                                <input type="text" 
                                       x-model="formData.Data_Empresa" 
                                       @input="showSuggestions = true; formData.client_id = null"
                                       @focus="showSuggestions = true"
                                       class="gui-input" 
                                       placeholder="Empresa" 
                                       required 
                                       autocomplete="off">
                                
                                <!-- Sugerencias de Empresa -->
                                <div x-show="showSuggestions && filteredClients().length > 0" 
                                     class="suggestions-dropdown"
                                     x-transition:enter="fade-in"
                                     style="display: none;">
                                    <template x-for="client in filteredClients()" :key="client.id">
                                        <div class="suggestion-item" @click="selectClient(client); showSuggestions = false">
                                            <div class="suggestion-name" x-text="client.name"></div>
                                            <div class="suggestion-meta" x-text="client.inventory_daemons.length > 0 ? 'Vínculo DX Detectado' : 'Cliente Registrado'"></div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECCIÓN: TIPO DE SOLICITUD -->
            <div class="form-section">
                <div class="section-title">
                    <i class="fa-solid fa-diagram-project"></i>
                    <span>Tipo de Solicitud</span>
                </div>
                
                <div class="segmented-wrapper">
                    <div class="segmented-large relative">
                        <!-- Indicador deslizante -->
                        <div class="active-indicator" 
                             :style="{
                                 width: '33.33%',
                                 left: formData.docType === 'Change_Full' ? '0%' : (formData.docType === 'Change_Composite' ? '33.33%' : '66.66%')
                             }">
                        </div>

                        <button type="button" :class="formData.docType === 'Change_Full' ? 'active' : ''" @click="formData.docType = 'Change_Full'">
                            <i class="fa-solid fa-right-left"></i>
                            <span>Cambio Completo</span>
                        </button>
                        <button type="button" :class="formData.docType === 'Change_Composite' ? 'active' : ''" @click="formData.docType = 'Change_Composite'">
                            <i class="fa-solid fa-fingerprint"></i>
                            <span>Cambio de Composite</span>
                        </button>
                        <button type="button" :class="formData.docType === 'Change_NodeLocked' ? 'active' : ''" @click="formData.docType = 'Change_NodeLocked'">
                            <i class="fa-solid fa-network-wired"></i>
                            <span>Cambio NodeLocked</span>
                        </button>
                    </div>
                </div>

                <!-- Descripción del tipo seleccionado -->
                <div class="type-description-box">
                    <div x-show="formData.docType === 'Change_Full'" x-transition:enter="fade-in">
                        <i class="fa-solid fa-circle-info"></i>
                        <span>Cambio total del servidor: implica nuevo <strong>Hostname</strong> y nuevo identificador <strong>Composite</strong>.</span>
                    </div>
                    <div x-show="formData.docType === 'Change_Composite'" x-transition:enter="fade-in">
                        <i class="fa-solid fa-circle-info"></i>
                        <span>El Hostname se mantiene igual, pero el identificador <strong>Composite</strong> del hardware ha cambiado.</span>
                    </div>
                    <div x-show="formData.docType === 'Change_NodeLocked'" x-transition:enter="fade-in">
                        <i class="fa-solid fa-circle-info"></i>
                        <span>Licencias bloqueadas a máquina (<strong>MAC</strong>) que no dependen de un servidor central.</span>
                    </div>
                </div>
            </div>

            <!-- SECCIÓN: MÁQUINAS (PARALELO) -->
            <div class="columns-2" style="gap: 24px; margin-top: 24px;">
                <!-- Máquina Actual -->
                <div class="form-section">
                    <div class="section-title">
                        <i class="fa-solid fa-desktop"></i>
                        <span>Máquina Actual</span>
                    </div>
                    <div class="fields-stack">
                        <div class="field-row">
                            <div class="input-wrap">
                                <i class="fa-solid fa-terminal"></i>
                                <input type="text" x-model="formData.Hostname_Old" class="gui-input" placeholder="Hostname" :required="formData.docType !== 'Change_NodeLocked'" :disabled="formData.docType === 'Change_NodeLocked'">
                            </div>
                        </div>
                        <div class="field-row">
                            <div class="input-wrap">
                                <i class="fa-solid fa-code"></i>
                                <input type="text" x-model="formData.Composite_Old" class="gui-input" placeholder="Composite" :required="formData.docType !== 'Change_NodeLocked'" :disabled="formData.docType === 'Change_NodeLocked'" maxlength="12">
                            </div>
                        </div>
                        <div class="field-row">
                            <div class="input-wrap">
                                <i class="fa-solid fa-id-card"></i>
                                <input type="text" x-model="formData.MAC_Old" class="gui-input" placeholder="HostID (MAC)" :required="formData.docType !== 'Change_Composite'" :disabled="formData.docType === 'Change_Composite'" maxlength="12">
                            </div>
                        </div>
                        
                        <!-- MACs Adicionales -->
                        <template x-for="(mac, index) in formData.MAC_Old_Extra" :key="index">
                            <div class="field-row">
                                <div class="input-wrap">
                                    <i class="fa-solid fa-id-card opacity-50"></i>
                                    <input type="text" x-model="formData.MAC_Old_Extra[index]" class="gui-input" placeholder="MAC Extra" maxlength="12">
                                    <button type="button" class="remove-btn" @click="removeMacPair(index)" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--muted); cursor: pointer;">&times;</button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Nueva Máquina -->
                <div class="form-section">
                    <div class="section-title">
                        <i class="fa-solid fa-tower-broadcast"></i>
                        <span>Nueva Máquina</span>
                    </div>
                    <div class="fields-stack">
                        <div class="field-row">
                            <div class="input-wrap">
                                <i class="fa-solid fa-terminal"></i>
                                <input type="text" x-model="formData.Hostname_New" class="gui-input" placeholder="Hostname" :required="formData.docType !== 'Change_NodeLocked'" :disabled="formData.docType === 'Change_NodeLocked'">
                            </div>
                        </div>
                        <div class="field-row">
                            <div class="input-wrap">
                                <i class="fa-solid fa-code"></i>
                                <input type="text" x-model="formData.Composite_New" class="gui-input" placeholder="Composite" :required="formData.docType !== 'Change_NodeLocked'" :disabled="formData.docType === 'Change_NodeLocked'" maxlength="12">
                            </div>
                        </div>
                        <div class="field-row">
                            <div class="input-wrap">
                                <i class="fa-solid fa-id-card"></i>
                                <input type="text" x-model="formData.MAC_New" class="gui-input" placeholder="HostID (MAC)" :required="formData.docType !== 'Change_Composite'" :disabled="formData.docType === 'Change_Composite'" maxlength="12">
                            </div>
                        </div>

                        <!-- MACs Adicionales -->
                        <template x-for="(mac, index) in formData.MAC_New_Extra" :key="index">
                            <div class="field-row">
                                <div class="input-wrap">
                                    <i class="fa-solid fa-id-card opacity-50"></i>
                                    <input type="text" x-model="formData.MAC_New_Extra[index]" class="gui-input" placeholder="Nueva MAC Extra" maxlength="12">
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Botón Añadir MACs (Solo NodeLocked) -->
            <div class="flex justify-center mt-2" x-show="formData.docType === 'Change_NodeLocked'">
                <button type="button" class="btn-add-mac" @click="addMacPair()">
                    <i class="fa-solid fa-plus"></i> Añadir par de MACs
                </button>
            </div>
        </div>

        <!-- FOOTER: CONTROLES Y ACCIONES -->
        <div class="cod-card-footer">
            <div class="footer-left">
                <!-- Idioma -->
                <div class="segmented-wrapper small">
                    <div class="segmented-small relative">
                        <div class="active-indicator-small" 
                             :style="{
                                 width: 'calc(50% - 4px)',
                                 left: formData.Language === 'Spanish' ? '2px' : 'calc(50% + 2px)'
                             }">
                        </div>
                        <button type="button" :class="formData.Language === 'Spanish' ? 'active' : ''" @click="formData.Language = 'Spanish'">Castellano</button>
                        <button type="button" :class="formData.Language === 'English' ? 'active' : ''" @click="formData.Language = 'English'">Inglés</button>
                    </div>
                </div>

                <!-- S.O. -->
                <div class="segmented-wrapper small">
                    <div class="segmented-small relative">
                        <div class="active-indicator-small" 
                             :style="{
                                 width: 'calc(50% - 4px)',
                                 left: formData.os === 'WINDOWS' ? '2px' : 'calc(50% + 2px)'
                             }">
                        </div>
                        <button type="button" :class="formData.os === 'WINDOWS' ? 'active' : ''" @click="formData.os = 'WINDOWS'">
                            <i class="fa-brands fa-windows"></i> Windows
                        </button>
                        <button type="button" :class="formData.os === 'LINUX' ? 'active' : ''" @click="formData.os = 'LINUX'">
                            <i class="fa-brands fa-linux"></i> Linux
                        </button>
                    </div>
                </div>
            </div>

            <div class="footer-right">
                <button type="button" class="btn-cod-clear" @click="resetForm()">
                    <i class="fa-solid fa-eraser"></i> Limpiar
                </button>
                <button type="submit" class="btn-cod-generate" :disabled="isGenerating">
                    <span x-show="!isGenerating"><i class="fa-solid fa-file-pdf"></i> Generar PDF</span>
                    <span x-show="isGenerating"><i class="fa-solid fa-spinner fa-spin"></i> Generando...</span>
                </button>
            </div>
        </div>
    </form>

    <!-- Overlay de Carga / Previsualización -->
    <div class="preview-overlay" x-show="showPreview" x-cloak>
        <div class="preview-modal shadow-premium">
            <div class="preview-header">
                <h3>Previsualización del Certificado</h3>
                <button type="button" @click="showPreview = false">&times;</button>
            </div>
            <div class="preview-body">
                <iframe :src="previewUrl" frameborder="0"></iframe>
            </div>
            <div class="preview-footer">
                <button type="button" class="btn-secondary" @click="showPreview = false">Cerrar</button>
                <button type="button" class="btn-primary" @click="generate('store')">Confirmar y Guardar</button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .cod-container {
        max-width: 1000px;
        margin: 0 auto;
        padding-bottom: 60px;
    }
    .cod-card {
        background: var(--card-bg);
        border: 1px solid var(--border);
        border-radius: 16px;
        overflow: hidden;
    }
    .cod-card-header {
        padding: 32px 40px 16px;
    }
    .header-icon {
        width: 40px;
        height: 40px;
        background: rgba(var(--accent-rgb), 0.1);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--accent);
        font-size: 18px;
    }
    .header-title {
        font-size: 24px;
        font-weight: 800;
        letter-spacing: -0.02em;
        color: var(--text);
    }
    .header-line {
        height: 1px;
        background: linear-gradient(to right, var(--accent), transparent);
        margin-top: 16px;
        opacity: 0.3;
    }
    .cod-card-body {
        padding: 24px 40px;
    }
    .form-section {
        margin-bottom: 32px;
    }
    .section-header {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 14px;
        font-weight: 700;
        color: var(--accent);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 20px;
    }
    .section-header i { font-size: 12px; }

    :root {
        --accent-rgb: 56, 139, 253;
    }

    /* Estilos Específicos COD */
    .cod-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        overflow: hidden;
    }
    
    .cod-card-header {
        background: var(--accent-muted);
        padding: 24px 32px;
        border-bottom: 1px solid var(--border);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .form-section {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 24px;
    }

    .vinculo-container {
        margin-bottom: 24px;
    }

    .select-vinculo {
        background: var(--accent-muted) !important;
        border: 1px dashed var(--accent) !important;
        color: var(--accent) !important;
        font-weight: 500;
    }

    .input-wrap i {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--muted);
        font-size: 14px;
        pointer-events: none;
        z-index: 5;
    }

    .fields-stack {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .mt-4 { margin-top: 16px; }
    .opacity-50 { opacity: 0.5; }

    /* Estilos Autocompletado */
    .suggestions-dropdown {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: var(--surface);
        border: 1px solid var(--accent);
        border-radius: 12px;
        margin-top: 8px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        z-index: 1000;
        overflow: hidden;
    }
    .suggestion-item {
        padding: 12px 16px;
        cursor: pointer;
        transition: all 0.2s;
        border-bottom: 1px solid var(--border);
    }
    .suggestion-item:last-child { border-bottom: none; }
    .suggestion-item:hover {
        background: rgba(var(--accent-rgb), 0.1);
    }
    .suggestion-name {
        font-size: 14px;
        font-weight: 600;
        color: var(--primary);
    }
    .suggestion-meta {
        font-size: 11px;
        color: var(--accent);
        margin-top: 2px;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    /* Segmented Control Premium con Animación */
    .segmented-wrapper {
        background: var(--raised);
        padding: 4px;
        border-radius: 14px;
        border: 1px solid var(--border);
        margin-top: 12px;
    }

    .segmented-large {
        display: flex;
        position: relative;
        z-index: 1;
    }

    .segmented-large button {
        flex: 1;
        background: none !important;
        border: none !important;
        padding: 10px 12px;
        color: var(--muted);
        display: flex;
        flex-direction: row;
        align-items: center;
        justify-content: center;
        gap: 10px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: color 0.3s;
        z-index: 2;
    }

    .segmented-large button i {
        font-size: 14px;
        margin-bottom: 0;
    }

    .segmented-large button.active {
        color: var(--accent);
    }

    .active-indicator {
        position: absolute;
        height: 100%;
        background: var(--accent-muted);
        border: 1px solid var(--accent-border);
        border-radius: 10px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        z-index: 1;
    }

    /* Caja de Descripción */
    .type-description-box {
        margin-top: 20px;
        padding: 16px 20px;
        background: var(--accent-muted);
        border-left: 3px solid var(--accent);
        border-radius: 0 8px 8px 0;
        font-size: 13px;
        color: var(--secondary);
        line-height: 1.5;
    }

    .type-description-box i {
        margin-right: 8px;
        color: var(--accent);
    }

    .type-description-box strong {
        color: var(--accent);
        font-weight: 700;
    }

    .input-with-icon input:focus, .input-with-icon select:focus {
        border-color: var(--accent);
        background: rgba(var(--accent-rgb), 0.02);
        box-shadow: 0 0 0 4px rgba(var(--accent-rgb), 0.1);
    }
    .input-with-icon select option {
        background: var(--card-bg);
        color: var(--text);
    }
    .input-with-icon input:disabled {
        opacity: 0.3;
        cursor: not-allowed;
    }
    .input-with-icon.extra input {
        background: rgba(0,0,0,0.05);
        border-style: dashed;
    }

    .remove-btn {
        position: absolute;
        right: 12px;
        width: 24px;
        height: 24px;
        border: none;
        background: rgba(239, 68, 68, 0.1);
        color: #ef4444;
        border-radius: 6px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Footer & Actions */
    .cod-card-footer {
        padding: 32px 40px;
        background: var(--accent-muted);
        border-top: 1px solid var(--border);
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 24px;
    }

    .footer-left {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .footer-right {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .segmented-wrapper.small {
        padding: 3px;
        border-radius: 10px;
        background: var(--surface);
        border: 1px solid var(--border);
    }

    .segmented-small {
        display: flex;
        position: relative;
        z-index: 1;
        min-width: 180px;
        width: 100%;
    }

    .segmented-small button {
        flex: 1;
        background: none !important;
        border: none !important;
        padding: 6px 12px;
        color: var(--muted);
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        cursor: pointer;
        transition: all 0.3s ease;
        z-index: 2;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .segmented-small button i { font-size: 12px; }

    .segmented-small button.active {
        color: var(--accent);
    }

    .active-indicator-small {
        position: absolute;
        top: 2px;
        bottom: 2px;
        background: rgba(var(--accent-rgb), 0.05);
        border: 1px solid var(--accent);
        box-shadow: 0 0 10px rgba(var(--accent-rgb), 0.1);
        border-radius: 6px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        z-index: 1;
    }

    .btn-cod-generate {
        background: var(--accent);
        color: white;
        border: none;
        padding: 12px 28px;
        border-radius: 12px;
        font-weight: 700;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.2s;
        box-shadow: 0 4px 15px rgba(var(--accent-rgb), 0.3);
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .btn-cod-generate:hover:not(:disabled) {
        background: var(--accent-hover);
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(var(--accent-rgb), 0.4);
    }

    .btn-cod-generate:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .btn-cod-clear {
        background: transparent;
        color: var(--muted);
        border: 1px solid var(--border);
        padding: 11px 20px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 13px;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-cod-clear:hover {
        background: var(--raised);
        color: var(--danger);
        border-color: var(--danger-border);
    }

    /* Botón Añadir MAC */
    .btn-add-mac {
        padding: 8px 16px;
        background: var(--surface);
        border: 1px dashed var(--accent);
        color: var(--accent);
        border-radius: 8px;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .btn-add-mac:hover {
        background: var(--accent-muted);
        border-style: solid;
    }

    /* Preview Modal */
    .preview-overlay {
        position: fixed; inset: 0; background: rgba(0,0,0,0.8);
        backdrop-filter: blur(10px); display: flex; align-items: center; justify-content: center;
        z-index: 2000; padding: 40px;
    }
    .preview-modal {
        background: var(--surface); width: 100%; max-width: 900px;
        height: 100%; border-radius: 20px; overflow: hidden; display: flex; flex-direction: column;
        border: 1px solid var(--border);
    }
    .preview-header { padding: 20px 32px; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; }
    .preview-header h3 { font-size: 18px; font-weight: 800; }
    .preview-header button { background: none; border: none; color: var(--muted); font-size: 24px; cursor: pointer; }
    .preview-body { flex: 1; background: #525659; }
    .preview-body iframe { width: 100%; height: 100%; }
    .preview-footer { padding: 20px 32px; border-top: 1px solid var(--border); display: flex; justify-content: flex-end; gap: 16px; }

    [x-cloak] { display: none !important; }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(5px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .fade-in {
        animation: fadeIn 0.3s ease forwards;
    }
</style>
@endpush

@push('scripts')
<script>
function codGenerator() {
    return {
        clients: @json($clients),
        isGenerating: false,
        showPreview: false,
        previewUrl: '',
        formData: {
            client_id: '{{ $selectedClient ? $selectedClient->id : "" }}',
            docType: 'Change_Full',
            Language: 'Spanish',
            os: 'WINDOWS',
            Data_SoldTo: '',
            Data_Solicitante: '',
            Data_Empresa: '{{ $selectedClient ? $selectedClient->name : "" }}',
            Hostname_Old: '',
            Composite_Old: '',
            MAC_Old: '',
            Hostname_New: '',
            Composite_New: '',
            MAC_New: '',
            MAC_Old_Extra: [],
            MAC_New_Extra: []
        },

        filteredClients() {
            if (!this.formData.Data_Empresa) return [];
            const search = this.formData.Data_Empresa.toLowerCase();
            return this.clients.filter(c => c.name.toLowerCase().includes(search)).slice(0, 5);
        },

        selectClient(client) {
            this.formData.client_id = client.id;
            this.formData.Data_Empresa = client.name;
            
            if (client.inventory_daemons && client.inventory_daemons.length > 0) {
                const main = client.inventory_daemons[0];
                this.formData.Data_SoldTo = main.sold_to || '';
                this.formData.Hostname_Old = main.hostname || '';
                this.formData.Composite_Old = main.composite || '';
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
                docType: 'Change_Full',
                Language: 'Spanish',
                os: 'WINDOWS',
                Data_SoldTo: '',
                Data_Solicitante: '',
                Data_Empresa: '',
                Hostname_Old: '',
                Composite_Old: '',
                MAC_Old: '',
                Hostname_New: '',
                Composite_New: '',
                MAC_New: '',
                MAC_Old_Extra: [],
                MAC_New_Extra: []
            };
        },

        async generate(mode) {
            this.isGenerating = true;
            try {
                const route = mode === 'preview' ? '{{ route("tools.cod.preview") }}' : '{{ route("tools.cod.store") }}';
                const response = await fetch(route, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(this.formData)
                });
                
                if (mode === 'preview') {
                    const blob = await response.blob();
                    this.previewUrl = URL.createObjectURL(blob);
                    this.showPreview = true;
                } else {
                    const result = await response.json();
                    if (result.success) {
                        window.location.href = result.download_url;
                    } else {
                        alert('Error: ' + result.message);
                    }
                }
            } catch (e) {
                alert('Error en el sistema de generación.');
            } finally {
                this.isGenerating = false;
            }
        }
    }
}
</script>
@endpush
@endsection
