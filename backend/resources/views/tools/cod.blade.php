@extends('layouts.app')

@section('title', 'Generador de COD - DX Portal')

@section('content')
<div class="page-header">
    <div class="breadcrumb">Portal › Herramientas › Siemens › Generador COD</div>
    <h1 class="page-title">Solicitud de Cambio de Licencia</h1>
    <p class="page-sub">Generación de Certificado de Cese (COD) oficial de Siemens Digital Industries Software</p>
</div>

<div x-data="codGenerator()" class="cod-container">
    <div id="codForm" class="cod-card shadow-premium">
        @csrf
        
        <!-- Header del Formulario -->
        <div class="cod-card-header">
            <div class="header-title-block">
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
                                <input type="text" 
                                       x-model="formData.Data_Solicitante" 
                                       @input="formData.Data_Solicitante = formData.Data_Solicitante.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '')"
                                       class="gui-input" 
                                       placeholder="Solicitante" 
                                       required>
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
                                <input type="text" 
                                       x-model="formData.Hostname_Old" 
                                       @input="formData.Hostname_Old = formData.Hostname_Old.replace(/[^a-zA-Z0-9-]/g, '')"
                                       class="gui-input" 
                                       placeholder="Hostname" 
                                       :required="formData.docType !== 'Change_NodeLocked'" 
                                       :disabled="formData.docType === 'Change_NodeLocked'">
                            </div>
                        </div>
                        <div class="field-row">
                            <div class="input-wrap">
                                <i class="fa-solid fa-code"></i>
                                <input type="text" 
                                       x-model="formData.Composite_Old" 
                                       @input="formData.Composite_Old = formData.Composite_Old.replace(/[^a-zA-Z0-9]/g, '')"
                                       class="gui-input" 
                                       placeholder="Composite" 
                                       :required="formData.docType !== 'Change_NodeLocked'" 
                                       :disabled="formData.docType === 'Change_NodeLocked'" 
                                       maxlength="12">
                            </div>
                        </div>
                        <div class="field-row">
                            <div class="input-wrap">
                                <i class="fa-solid fa-id-card"></i>
                                <input type="text" 
                                       x-model="formData.MAC_Old" 
                                       @input="formData.MAC_Old = formData.MAC_Old.replace(/[^a-zA-Z0-9]/g, '')"
                                       class="gui-input" 
                                       placeholder="HostID (MAC sin guiones)" 
                                       :required="formData.docType !== 'Change_Composite'" 
                                       :disabled="formData.docType === 'Change_Composite'" 
                                       maxlength="12">
                            </div>
                        </div>
                        
                        <!-- MACs Adicionales -->
                        <template x-for="(mac, index) in formData.MAC_Old_Extra" :key="index">
                            <div class="field-row">
                                <div class="input-wrap">
                                    <i class="fa-solid fa-id-card opacity-50"></i>
                                    <input type="text" 
                                           x-model="formData.MAC_Old_Extra[index]" 
                                           @input="formData.MAC_Old_Extra[index] = formData.MAC_Old_Extra[index].replace(/[^a-zA-Z0-9]/g, '')"
                                           class="gui-input" 
                                           placeholder="MAC Extra (sin guiones)" 
                                           maxlength="12">
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
                                <input type="text" 
                                       x-model="formData.Hostname_New" 
                                       @input="formData.Hostname_New = formData.Hostname_New.replace(/[^a-zA-Z0-9-]/g, '')"
                                       class="gui-input" 
                                       placeholder="Hostname" 
                                       :required="formData.docType !== 'Change_NodeLocked'" 
                                       :disabled="formData.docType === 'Change_NodeLocked'">
                            </div>
                        </div>
                        <div class="field-row">
                            <div class="input-wrap">
                                <i class="fa-solid fa-code"></i>
                                <input type="text" 
                                       x-model="formData.Composite_New" 
                                       @input="formData.Composite_New = formData.Composite_New.replace(/[^a-zA-Z0-9]/g, '')"
                                       class="gui-input" 
                                       placeholder="Composite" 
                                       :required="formData.docType !== 'Change_NodeLocked'" 
                                       :disabled="formData.docType === 'Change_NodeLocked'" 
                                       maxlength="12">
                            </div>
                        </div>
                        <div class="field-row">
                            <div class="input-wrap">
                                <i class="fa-solid fa-id-card"></i>
                                <input type="text" 
                                       x-model="formData.MAC_New" 
                                       @input="formData.MAC_New = formData.MAC_New.replace(/[^a-zA-Z0-9]/g, '')"
                                       class="gui-input" 
                                       placeholder="HostID (MAC sin guiones)" 
                                       :required="formData.docType !== 'Change_Composite'" 
                                       :disabled="formData.docType === 'Change_Composite'" 
                                       maxlength="12">
                            </div>
                        </div>

                        <!-- MACs Adicionales -->
                        <template x-for="(mac, index) in formData.MAC_New_Extra" :key="index">
                            <div class="field-row">
                                <div class="input-wrap">
                                    <i class="fa-solid fa-id-card opacity-50"></i>
                                    <input type="text" 
                                           x-model="formData.MAC_New_Extra[index]" 
                                           @input="formData.MAC_New_Extra[index] = formData.MAC_New_Extra[index].replace(/[^a-zA-Z0-9]/g, '')"
                                           class="gui-input" 
                                           placeholder="Nueva MAC Extra (sin guiones)" 
                                           maxlength="12">
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

            <!-- ASISTENTE IA (Añadido) -->
            <div style="margin-top: 40px; padding-top: 30px; border-top: 1px dashed var(--border); display: flex; justify-content: center;">
                <button type="button" class="btn-ai-assist shadow-premium" @click="openAiModal()">
                    <div class="ai-icon-pulse">
                        <i class="fa-solid fa-microchip-ai"></i>
                    </div>
                    <div style="display: flex; flex-direction: column; align-items: flex-start; gap: 2px;">
                        <span style="font-size: 11px; font-weight: 800; letter-spacing: 0.05em; text-transform: uppercase;">Asistente de Identificadores</span>
                        <span style="font-size: 10px; opacity: 0.7;">Analizar output de hardware con Gemini AI</span>
                    </div>
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
                                 width: 'calc(50% - 6px)',
                                 left: formData.Language === 'Spanish' ? '3px' : 'calc(50% + 3px)'
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
                                 width: 'calc(50% - 6px)',
                                 left: formData.os === 'WINDOWS' ? '3px' : 'calc(50% + 3px)'
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
                <!-- Limpiar (Estilo Selector) -->
                <div class="btn-wrapper-tech">
                    <button type="button" class="btn-tech-base btn-cod-clear" @click="resetForm()">
                        <i class="fa-solid fa-eraser"></i> Limpiar
                    </button>
                </div>

                <!-- Generar (Abre Preview) -->
                <button type="button" class="btn-cod-generate" @click="openPreview()" :disabled="isGenerating">
                    <template x-if="!isGenerating">
                        <i class="fa-solid fa-eye"></i>
                    </template>
                    <template x-if="!isGenerating">
                        <span>Vista Previa</span>
                    </template>
                    <template x-if="isGenerating">
                        <i class="fa-solid fa-spinner fa-spin"></i>
                    </template>
                    <template x-if="isGenerating">
                        <span>Procesando...</span>
                    </template>
                </button>
            </div>
        </div>
    </div>

    <!-- Modal de Asistente IA -->
    <div class="preview-overlay" x-show="showAiModal" x-transition x-cloak>
        <div class="ai-modal shadow-premium" @click.away="showAiModal = false">
            <div class="preview-header" style="background: var(--accent-muted); border-bottom: 1px solid var(--accent-border);">
                <div class="preview-title-container">
                    <i class="fa-solid fa-microchip-ai text-accent"></i>
                    <span class="preview-title">Asistente Inteligente de Composite</span>
                </div>
                <button type="button" class="btn-close-minimal" @click="showAiModal = false">
                    <i class="fa-solid fa-xmark"></i>
                    <span>Cerrar</span>
                </button>
            </div>
    <!-- Overlay de Previsualización Limpia -->
    <div class="preview-overlay" x-show="showPreview" x-transition x-cloak>
        <div class="preview-modal shadow-premium" @click.away="showPreview = false">
            <div class="preview-header">
                <div class="preview-title-container">
                    <i class="fa-solid fa-file-pdf text-accent"></i>
                    <span class="preview-title">Vista Previa</span>
                </div>
                <button type="button" class="btn-close-minimal" @click="showPreview = false">
                    <i class="fa-solid fa-xmark"></i>
                    <span>Cerrar</span>
                </button>
            </div>
            <div class="preview-body">
                <div x-show="isPreviewLoading" class="preview-loader">
                    <i class="fa-solid fa-circle-notch fa-spin"></i>
                    <span>Cargando documento...</span>
                </div>
                <iframe x-show="!isPreviewLoading" :src="previewUrl" frameborder="0"></iframe>
            </div>
            <div class="preview-footer">
                <button type="button" class="btn-tech-base" @click="showPreview = false">
                    <i class="fa-solid fa-arrow-left"></i> <span>Volver a Editar</span>
                </button>
                <button type="button" class="btn-cod-generate" @click="generate('store')" :disabled="isSaving">
                    <template x-if="!isSaving">
                        <i class="fa-solid fa-file-signature"></i>
                    </template>
                    <template x-if="!isSaving">
                        <span>Confirmar y Descargar</span>
                    </template>
                    <template x-if="isSaving">
                        <i class="fa-solid fa-spinner fa-spin"></i>
                    </template>
                    <template x-if="isSaving">
                        <span>Guardando...</span>
                    </template>
                </button>
            </div>
        </div>
    </div>

    <!-- Modal de Asistente IA -->
    <div class="preview-overlay" x-show="showAiModal" x-transition x-cloak>
        <div class="ai-modal shadow-premium" @click.away="showAiModal = false">
            <div class="preview-header" style="background: var(--accent-muted); border-bottom: 1px solid var(--accent-border);">
                <div class="preview-title-container">
                    <i class="fa-solid fa-microchip-ai text-accent"></i>
                    <span class="preview-title">Asistente Inteligente de Composite</span>
                </div>
                <button type="button" class="btn-close-minimal" @click="showAiModal = false">
                    <i class="fa-solid fa-xmark"></i>
                    <span>Cerrar</span>
                </button>
            </div>
            <div class="ai-modal-body">
                <div x-show="!aiResult" x-transition>
                    <p class="ai-hint">
                        <i class="fa-solid fa-wand-magic-sparkles mr-2 text-accent"></i>
                        Pega el listado de adaptadores o arrastra el archivo <strong>composite.txt</strong> para un análisis automático.
                    </p>
                    
                    <div class="ai-upload-zone mb-4" 
                         @click="$refs.fileInput.click()" 
                         @dragover.prevent="isDragging = true" 
                         @dragleave.prevent="isDragging = false" 
                         @drop.prevent="isDragging = false; handleDrop($event)"
                         :class="{ 'active': isDragging, 'has-file': fileName }">
                        
                        <input type="file" x-ref="fileInput" class="hidden" @change="handleFileUpload($event)" accept=".txt">
                        
                        <template x-if="!fileName">
                            <div class="flex flex-col items-center justify-center gap-6 w-full">
                                <div class="upload-icon-wrapper">
                                    <i class="fa-solid fa-file-circle-plus"></i>
                                </div>
                                <div class="upload-text-content">
                                    <span class="upload-main-text text-lg">Haz clic o arrastra el archivo composite.txt</span>
                                    <span class="upload-sub-text">Sube el log de adaptadores para análisis instantáneo</span>
                                </div>
                            </div>
                        </template>

                        <template x-if="fileName">
                            <div class="flex flex-col items-center gap-3 fade-in w-full">
                                <div class="upload-icon-wrapper success">
                                    <i class="fa-solid fa-file-circle-check"></i>
                                </div>
                                <div class="flex flex-col items-center gap-1">
                                    <span class="font-bold text-primary" x-text="fileName"></span>
                                    <span class="text-xs text-muted">Archivo listo para procesar</span>
                                </div>
                                <button type="button" @click.stop="clearFile()" class="btn-clear-file">
                                    <i class="fa-solid fa-trash-can"></i>
                                    <span>Quitar archivo</span>
                                </button>
                            </div>
                        </template>

                        <div class="upload-progress-bar" x-show="isAiProcessing">
                            <div class="upload-progress-inner"></div>
                        </div>
                    </div>

                    <textarea x-model="aiInput" 
                              class="ai-textarea font-mono" 
                              placeholder="O pega el texto aquí..."></textarea>
                    
                    <div style="display: flex; justify-content: flex-end; margin-top: 16px;">
                        <button type="button" class="btn-cod-generate" @click="processAi()" :disabled="isAiProcessing || !aiInput">
                            <span x-show="!isAiProcessing">Analizar con Gemini</span>
                            <span x-show="isAiProcessing"><i class="fa-solid fa-spinner fa-spin me-2"></i> Procesando...</span>
                        </button>
                    </div>
                </div>

                <div x-show="aiResult" x-transition>
                    <div class="ai-result-box">
                        <div class="ai-result-header">
                            <i class="fa-solid fa-robot"></i>
                            <span>Hardware Recomendado</span>
                        </div>
                        <div class="ai-result-grid">
                            <div class="ai-result-item">
                                <label>Hostname</label>
                                <span x-text="aiResult.hostname || 'N/A'"></span>
                            </div>
                            <div class="ai-result-item">
                                <label>Composite</label>
                                <span class="font-mono" x-text="aiResult.composite || 'N/A'"></span>
                            </div>
                            <div class="ai-result-item">
                                <label>MAC Address</label>
                                <span class="font-mono" x-text="aiResult.mac || 'N/A'"></span>
                            </div>
                        </div>
                        <div class="ai-result-footer">
                            <div style="font-size: 11px; font-weight: 600; color: var(--accent); margin-bottom: 4px;">Adaptador: <span x-text="aiResult.adapter" style="color: var(--text);"></span></div>
                            <p class="ai-reason" style="font-size: 11px; color: var(--muted); margin: 0; line-height: 1.4;">
                                <i class="fa-solid fa-info-circle me-1"></i> <span x-text="aiResult.reason"></span>
                            </p>
                        </div>
                    </div>

                    <div style="display: flex; justify-content: space-between; margin-top: 24px; gap: 12px;">
                        <button type="button" class="btn-secondary-tech" @click="aiResult = null">
                            <i class="fa-solid fa-arrow-left"></i> <span>Volver a Analizar</span>
                        </button>
                        <button type="button" class="btn-cod-generate" @click="applyAiResult()">
                            <i class="fa-solid fa-bolt"></i> <span>Aplicar a Nueva Máquina</span>
                        </button>
                    </div>
                </div>
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
        width: 56px;
        height: 56px;
        background: rgba(var(--accent-rgb), 0.1);
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: var(--accent);
        flex-shrink: 0;
    }
    .header-title-block {
        display: flex;
        flex-direction: row !important;
        align-items: center;
        gap: 16px;
    }
    .header-title {
        font-size: 24px;
        font-weight: 800;
        letter-spacing: -0.02em;
        color: var(--text);
        margin: 0;
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
    /* Footer & Actions Refined */
    .cod-card-footer {
        padding: 32px 40px;
        background: var(--accent-muted);
        border-top: 1px solid var(--border);
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
    }

    .footer-left, .footer-right {
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .segmented-wrapper.small, .btn-wrapper-tech {
        height: 40px;
        padding: 2px;
        border-radius: 9px;
        background: var(--surface);
        border: 1px solid var(--border);
        display: flex;
        align-items: center;
    }

    .segmented-small {
        display: flex;
        position: relative;
        z-index: 1;
        min-width: 240px;
        height: 100%;
        width: 100%;
    }

    .segmented-small button, .btn-tech-base {
        flex: 1;
        height: 100%;
        background: none !important;
        border: none !important;
        padding: 0 12px;
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
        white-space: nowrap;
    }

    .segmented-small button i, .btn-tech-base i { 
        font-size: 13px; 
        width: 16px;
        display: flex;
        justify-content: center;
    }

    .segmented-small button.active {
        color: var(--accent);
    }

    .active-indicator-small {
        position: absolute;
        top: 0;
        bottom: 0;
        background: rgba(var(--accent-rgb), 0.05);
        border: 1px solid var(--accent);
        box-shadow: 0 0 12px rgba(var(--accent-rgb), 0.1);
        border-radius: 7px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        z-index: 1;
    }

    /* Botones de Acción Estilo Tech */
    .btn-cod-clear {
        color: var(--muted);
    }
    .btn-cod-clear:hover {
        color: var(--danger);
    }

    .btn-cod-generate {
        height: 40px;
        background: var(--accent);
        color: white;
        border: 1px solid var(--accent);
        padding: 0 24px;
        border-radius: 9px;
        font-weight: 800;
        font-size: 10px;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        cursor: pointer;
        transition: all 0.2s;
        box-shadow: 0 4px 12px rgba(var(--accent-rgb), 0.3);
        display: flex;
        flex-direction: row !important;
        align-items: center;
        justify-content: center;
        gap: 10px;
        white-space: nowrap !important;
    }

    .btn-cod-generate i {
        font-size: 13px;
        width: 16px;
        display: flex;
        justify-content: center;
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

    /* Preview Modal - Dark & Clean (Ajuste Final) */
    .preview-overlay {
        position: fixed; inset: 0; background: rgba(0, 0, 0, 0.85);
        backdrop-filter: blur(10px); display: flex; align-items: center; justify-content: center;
        z-index: 2000; padding: 20px;
    }
    .preview-modal {
        background: var(--surface); width: 100%; max-width: 1200px;
        height: 92vh; border-radius: 16px; overflow: hidden; display: flex; flex-direction: column;
        box-shadow: 0 25px 70px rgba(0,0,0,0.5); border: 1px solid var(--border);
    }
    .preview-header { 
        padding: 16px 28px; 
        background: var(--surface);
        border-bottom: 1px solid var(--border); 
        display: flex; 
        flex-direction: row !important;
        justify-content: space-between; 
        align-items: center; 
    }
    .preview-title-container {
        display: flex;
        flex-direction: row !important;
        align-items: center;
        gap: 12px;
    }
    .preview-title { font-size: 15px; font-weight: 700; color: var(--text); }
    
    .btn-close-minimal {
        display: flex; align-items: center; gap: 8px; padding: 8px 16px;
        background: var(--raised); color: var(--muted); border: 1px solid var(--border); border-radius: 8px;
        font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; cursor: pointer; transition: all 0.2s;
    }
    .btn-close-minimal:hover { background: var(--danger-muted); color: var(--danger); border-color: var(--danger-muted); }

    .preview-body { flex: 1; background: #262626; position: relative; }
    .preview-body iframe { width: 100%; height: 100%; border: none; }
    
    .preview-loader {
        position: absolute; inset: 0; display: flex; flex-direction: column; align-items: center; justify-content: center;
        gap: 16px; color: var(--text); background: #262626;
    }
    .preview-loader i { font-size: 32px; color: var(--accent); }

    .preview-footer { 
        padding: 20px 32px; 
        background: var(--surface);
        border-top: 1px solid var(--border); 
        display: flex; 
        justify-content: space-between; 
        align-items: center; 
    }

    .btn-secondary-tech {
        height: 40px; padding: 0 20px; border-radius: 9px; border: 1px solid var(--border);
        background: var(--surface); color: var(--muted); font-size: 10px; font-weight: 800;
        text-transform: uppercase; letter-spacing: 0.08em; cursor: pointer; transition: all 0.2s;
        display: flex; align-items: center; gap: 8px;
    }
    .btn-secondary-tech:hover { border-color: var(--accent); color: var(--accent); }

    [x-cloak] { display: none !important; }

    /* AI Assistant Styles */
    .btn-ai-assist {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 12px 24px;
        background: var(--surface);
        border: 1px solid var(--accent);
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        color: var(--text);
        text-align: left;
    }
    .btn-ai-assist:hover {
        background: var(--accent-muted);
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(var(--accent-rgb), 0.2);
    }
    .ai-icon-pulse {
        width: 36px;
        height: 36px;
        background: var(--accent);
        color: white;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        position: relative;
    }
    .ai-icon-pulse::after {
        content: '';
        position: absolute;
        inset: -4px;
        border: 2px solid var(--accent);
        border-radius: 12px;
        opacity: 0.3;
        animation: pulse-ai 2s infinite;
    }
    @keyframes pulse-ai {
        0% { transform: scale(1); opacity: 0.3; }
        50% { transform: scale(1.1); opacity: 0.1; }
        100% { transform: scale(1); opacity: 0.3; }
    }

    .ai-upload-zone {
        border: 2px dashed var(--accent);
        border-radius: 20px;
        padding: 48px 32px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        background: rgba(var(--accent-rgb), 0.05);
        margin-bottom: 24px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 20px;
        position: relative;
        overflow: hidden;
        min-height: 240px;
    }
    .ai-upload-zone::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, rgba(var(--accent-rgb), 0.1), transparent);
        opacity: 0;
        transition: opacity 0.3s;
    }
    .ai-upload-zone:hover::before, .ai-upload-zone.active::before {
        opacity: 1;
    }
    .ai-upload-zone:hover, .ai-upload-zone.active {
        border-color: var(--accent);
        transform: translateY(-2px);
        box-shadow: 0 12px 30px rgba(0,0,0,0.15);
    }
    .upload-icon-wrapper {
        width: 80px;
        height: 80px;
        margin: 0 auto;
        background: var(--surface);
        border: 2px solid var(--border);
        border-radius: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 32px;
        color: var(--accent);
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        z-index: 1;
        position: relative;
    }
    .upload-icon-wrapper::after {
        content: '';
        position: absolute;
        inset: -10px;
        background: var(--accent);
        opacity: 0.1;
        border-radius: 30px;
        z-index: -1;
        animation: icon-pulse 3s infinite;
    }
    @keyframes icon-pulse {
        0% { transform: scale(1); opacity: 0.1; }
        50% { transform: scale(1.15); opacity: 0.05; }
        100% { transform: scale(1); opacity: 0.1; }
    }
    .ai-upload-zone:hover .upload-icon-wrapper {
        background: var(--accent);
        color: white;
        border-color: var(--accent);
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(var(--accent-rgb), 0.3);
    }
    .upload-text-content {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
        text-align: center;
        width: 100%;
        z-index: 1;
    }
    .upload-main-text {
        font-size: 14px;
        font-weight: 700;
        color: var(--primary);
        letter-spacing: -0.01em;
    }
    .upload-sub-text {
        font-size: 11px;
        color: var(--muted);
    }
    .upload-file-info {
        margin-top: 8px;
        padding: 6px 14px;
        background: var(--accent-muted);
        border: 1px solid var(--accent-border);
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        color: var(--accent);
        display: flex;
        align-items: center;
        gap: 8px;
        z-index: 1;
    }

    .ai-upload-zone input[type="file"] {
        display: none !important;
    }
    .upload-icon-wrapper.success {
        background: rgba(var(--success-rgb), 0.1);
        color: var(--success);
        border-color: var(--success);
        box-shadow: 0 0 20px rgba(var(--success-rgb), 0.2);
    }
    .ai-upload-zone.has-file {
        border-style: solid;
        background: rgba(var(--success-rgb), 0.02);
        border-color: rgba(var(--success-rgb), 0.3);
    }
    .ai-upload-zone.active {
        border-color: var(--accent);
        background: rgba(var(--accent-rgb), 0.08);
        transform: scale(1.01);
    }
    .upload-progress-bar {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 3px;
        background: rgba(var(--accent-rgb), 0.1);
    }
    .upload-progress-inner {
        height: 100%;
        background: var(--accent);
        width: 30%;
        animation: progress-slide 2s infinite linear;
    }
    .btn-clear-file {
        margin-top: 12px;
        background: rgba(var(--danger-rgb), 0.1);
        color: var(--danger);
        border: 1px solid rgba(var(--danger-rgb), 0.2);
        padding: 6px 14px;
        border-radius: 8px;
        font-size: 11px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
        transition: all 0.2s;
    }
    .btn-clear-file:hover {
        background: var(--danger);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(var(--danger-rgb), 0.3);
    }

    .ai-modal {
        background: var(--surface);
        width: 100%;
        max-width: 600px;
        border-radius: 16px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        box-shadow: 0 25px 70px rgba(0,0,0,0.5);
        border: 1px solid var(--border);
    }
    .ai-modal-body { padding: 24px 32px 32px; }
    .ai-hint { 
        font-size: 14px; 
        color: var(--muted); 
        margin-bottom: 24px; 
        line-height: 1.6; 
        padding: 12px 16px;
        background: rgba(var(--accent-rgb), 0.05);
        border-left: 3px solid var(--accent);
        border-radius: 4px 12px 12px 4px;
    }
    .ai-textarea {
        width: 100%;
        height: 200px;
        background: rgba(0,0,0,0.2);
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 16px;
        color: var(--primary);
        font-size: 12px;
        line-height: 1.6;
        resize: none;
        outline: none;
    }
    .ai-textarea:focus { border-color: var(--accent); }

    .ai-result-box {
        background: rgba(var(--accent-rgb), 0.03);
        border: 1px solid var(--accent-border);
        border-radius: 16px;
        padding: 24px;
        box-shadow: inset 0 0 40px rgba(var(--accent-rgb), 0.05);
    }
    .ai-result-header {
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 15px;
        font-weight: 800;
        color: var(--primary);
        margin-bottom: 24px;
    }
    .ai-result-header i {
        width: 32px;
        height: 32px;
        background: var(--accent);
        color: white;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
    }
    .ai-result-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
        margin-bottom: 24px;
    }
    .ai-result-item { 
        display: flex; 
        flex-direction: column; 
        gap: 6px;
        padding: 12px;
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 12px;
    }
    .ai-result-item label { 
        font-size: 10px; 
        font-weight: 800; 
        text-transform: uppercase; 
        color: var(--muted); 
        letter-spacing: 0.08em; 
    }
    .ai-result-item span { 
        font-size: 14px; 
        color: var(--primary); 
        font-weight: 700;
        font-family: var(--font-mono);
    }
    .ai-result-footer {
        padding-top: 20px;
        border-top: 1px dashed var(--accent-border);
    }
    .ai-reason {
        font-size: 13px;
        color: var(--muted);
        line-height: 1.6;
        font-style: italic;
    }

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
        isSaving: false,
        isPreviewLoading: false,
        showPreview: false,
        previewUrl: '',

        // AI Assistant State
        showAiModal: false,
        isAiProcessing: false,
        aiInput: '',
        aiResult: null,
        isDragging: false,
        fileName: '',

        init() {
            this.$watch('formData.docType', value => {
                if (value !== 'Change_NodeLocked') {
                    this.formData.MAC_Old_Extra = [];
                    this.formData.MAC_New_Extra = [];
                }
            });
        },

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

        openPreview() {
            if (!this.formData.Data_SoldTo || !this.formData.Data_Solicitante || !this.formData.Data_Empresa) {
                alert('Por favor, rellene los campos obligatorios antes de previsualizar.');
                return;
            }
            
            this.isGenerating = true;
            this.showPreview = true;
            this.isPreviewLoading = true;

            fetch('{{ route("tools.cod.preview") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(this.formData)
            })
            .then(response => response.blob())
            .then(blob => {
                if (this.previewUrl) URL.revokeObjectURL(this.previewUrl);
                // Añadimos parámetros para ocultar toolbar y navpanes (thumbnails)
                const url = URL.createObjectURL(blob);
                this.previewUrl = url + '#toolbar=0&navpanes=0&scrollbar=1';
                this.isPreviewLoading = false;
                this.isGenerating = false;
            })
            .catch(error => {
                console.error('Error rendering preview:', error);
                this.showPreview = false;
                this.isGenerating = false;
                alert('Error al generar la vista previa. Revise los datos.');
            });
        },

        generate(action) {
            this.isSaving = true;

            fetch('{{ route("tools.cod.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(this.formData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.showPreview = false;
                    window.location.href = data.download_url;
                } else {
                    alert(data.message || 'Error en el sistema de generación.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error crítico en la comunicación con el servidor.');
            })
            .finally(() => {
                this.isSaving = false;
            });
        },

        // AI Assistant Methods
        openAiModal() {
            this.showAiModal = true;
            this.aiInput = '';
            this.aiResult = null;
        },

        processAi() {
            if (!this.aiInput) return;
            this.isAiProcessing = true;

            fetch('{{ route("tools.cod.parse-composite") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ text: this.aiInput })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.aiResult = data.data;
                } else {
                    alert(data.message || 'Error al procesar con Gemini.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error crítico en la comunicación con el servidor.');
            })
            .finally(() => {
                this.isAiProcessing = false;
            });
        },

        applyAiResult() {
            if (!this.aiResult) return;

            if (this.aiResult.hostname) {
                this.formData.Hostname_New = this.aiResult.hostname;
            }
            if (this.aiResult.composite) {
                this.formData.Composite_New = this.aiResult.composite;
            }
            if (this.aiResult.mac) {
                this.formData.MAC_New = this.aiResult.mac;
            }

            this.showAiModal = false;
            
            // Efecto visual de resaltado en los campos destino (opcional)
            // Aquí podríamos disparar un evento o similar
        },

        clearFile() {
            this.fileName = '';
            this.aiInput = '';
            if (this.$refs.fileInput) this.$refs.fileInput.value = '';
        },

        handleFileUpload(event) {
            const file = event.target.files[0];
            if (!file) return;
            this.readFile(file);
        },

        handleDrop(event) {
            event.target.classList.remove('active');
            const file = event.dataTransfer.files[0];
            if (!file) return;
            this.readFile(file);
        },

        readFile(file) {
            if (!file.name.endsWith('.txt')) {
                alert('Por favor, sube solo archivos .txt');
                return;
            }

            this.fileName = file.name;
            const reader = new FileReader();
            reader.onload = (e) => {
                this.aiInput = e.target.result;
            };
            reader.readAsText(file);
        }
    }
}
</script>
@endpush
@endsection
