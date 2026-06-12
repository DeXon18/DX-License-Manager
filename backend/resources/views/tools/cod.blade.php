@extends('layouts.app')

@section('title', 'Generador de COD - DX Portal')

@section('content')
<div class="page-header">
    <div class="breadcrumb">Portal › Herramientas › Siemens › Generador COD</div>
    <h1 class="page-title">Solicitud de Cambio de Licencia</h1>
    <p class="page-sub">Generación de Certificado de Cese (COD) oficial de Siemens Digital Industries Software</p>
</div>

<div x-data="codGenerator()" class="dx-v2-cod-container">
    <div id="codForm" class="dx-v2-cod-card shadow-premium">
        @csrf
        
        <!-- Header del Formulario -->
        <div class="dx-v2-cod-card-header">
            <div class="dx-v2-cod-header-title-block">
                <div class="dx-v2-cod-header-icon">
                    <i class="fa-solid fa-file-signature"></i>
                </div>
                <h2 class="dx-v2-cod-header-title">Certificado de Cese</h2>
            </div>
            <div class="dx-v2-cod-header-line"></div>
        </div>

        <div class="dx-v2-cod-card-body">
            <!-- SECCIÓN: DATOS DE LA EMPRESA -->
            <div class="dx-v2-cod-form-section">
                <div class="dx-v2-cod-section-title">
                    <i class="fa-solid fa-building"></i>
                    <span>Datos de la Empresa</span>
                </div>

                <div class="dx-v2-cod-fields-container">
                    <!-- Fila 1: Sold To (Ancho completo) -->
                    <div class="dx-v2-cod-field-row">
                        <div class="dx-v2-cod-input-wrap">
                            <i class="fa-solid fa-shield-halved"></i>
                            <input type="text" x-model="formData.Data_SoldTo" class="dx-v2-form-input" placeholder="Número de licencia (Sold To)" required maxlength="10">
                        </div>
                    </div>

                    <!-- Fila 2: Solicitante y Empresa (50/50) -->
                    <div class="dx-v2-cod-columns-2 mt-4">
                        <div class="dx-v2-cod-field-row">
                            <div class="dx-v2-cod-input-wrap">
                                <i class="fa-solid fa-user"></i>
                                <input type="text" 
                                       x-model="formData.Data_Solicitante" 
                                       @input="formData.Data_Solicitante = formData.Data_Solicitante.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '')"
                                       class="dx-v2-form-input" 
                                       placeholder="Solicitante" 
                                       required>
                            </div>
                        </div>
                        <div class="dx-v2-cod-field-row" x-data="{ showSuggestions: false }" @click.away="showSuggestions = false">
                            <div class="dx-v2-cod-input-wrap">
                                <i class="fa-solid fa-globe"></i>
                                <input type="text" 
                                       x-model="formData.Data_Empresa" 
                                       @input="showSuggestions = true; formData.client_id = null"
                                       @focus="showSuggestions = true"
                                       class="dx-v2-form-input" 
                                       placeholder="Empresa" 
                                       required 
                                       autocomplete="off">
                                
                                <!-- Sugerencias de Empresa -->
                                <div x-show="showSuggestions && filteredClients().length > 0" 
                                     class="dx-v2-cod-suggestions-dropdown"
                                     x-transition:enter="fade-in"
                                     style="display: none;">
                                    <template x-for="client in filteredClients()" :key="client.id">
                                        <div class="dx-v2-cod-suggestion-item" @click="selectClient(client); showSuggestions = false">
                                            <div class="dx-v2-cod-suggestion-name" x-text="client.name"></div>
                                            <div class="dx-v2-cod-suggestion-meta" x-text="client.inventory_daemons.length > 0 ? 'Vínculo DX Detectado' : 'Cliente Registrado'"></div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECCIÓN: TIPO DE SOLICITUD -->
            <div class="dx-v2-cod-form-section">
                <div class="dx-v2-cod-section-title">
                    <i class="fa-solid fa-diagram-project"></i>
                    <span>Tipo de Solicitud</span>
                </div>
                
                <div class="dx-v2-cod-segmented-wrapper">
                    <div class="dx-v2-cod-segmented-large relative">
                        <!-- Indicador deslizante -->
                        <div class="dx-v2-cod-active-indicator" 
                             :style="{
                                 width: '25%',
                                 left: formData.docType === 'Change_Full' ? '0%' : (formData.docType === 'Change_Composite' ? '25%' : (formData.docType === 'Change_NodeLocked' ? '50%' : '75%'))
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
                        <button type="button" :class="formData.docType === 'Change_Cloud' ? 'active' : ''" @click="formData.docType = 'Change_Cloud'">
                            <i class="fa-solid fa-cloud"></i>
                            <span>Cambio Cloud</span>
                        </button>
                    </div>
                </div>

                <!-- Descripción del tipo seleccionado -->
                <div class="dx-v2-cod-type-description-box">
                    <div x-show="formData.docType === 'Change_Full'" x-transition:enter="fade-in">
                        <i class="fa-solid fa-circle-info"></i>
                        <span>Cambio total del servidor: implica nuevo <strong>Hostname</strong>, nuevo <strong>Composite</strong> y nuevo <strong>LM Host (MAC)</strong>.</span>
                    </div>
                    <div x-show="formData.docType === 'Change_Composite'" x-transition:enter="fade-in">
                        <i class="fa-solid fa-circle-info"></i>
                        <span>El Hostname se mantiene igual, pero el identificador <strong>Composite</strong> del hardware ha cambiado.</span>
                    </div>
                    <div x-show="formData.docType === 'Change_NodeLocked'" x-transition:enter="fade-in">
                        <i class="fa-solid fa-circle-info"></i>
                        <span>Licencias bloqueadas a máquina (<strong>MAC</strong>) que no dependen de un servidor central.</span>
                    </div>
                    <div x-show="formData.docType === 'Change_Cloud'" x-transition:enter="fade-in">
                        <i class="fa-solid fa-circle-info"></i>
                        <span>Migración o cambio de servidor hacia entornos Cloud (AWS/Azure). El <strong>Hostname</strong> es obligatorio.</span>
                    </div>
                </div>
            </div>

            <!-- SECCIÓN: MÁQUINAS (PARALELO) -->
            <div class="dx-v2-cod-columns-2-spaced">
                <!-- Máquina Actual -->
                <div class="dx-v2-cod-form-section">
                    <div class="dx-v2-cod-section-title" style="min-height: 42px; display: flex; align-items: center;">
                        <i class="fa-solid fa-desktop"></i>
                        <span>Máquina Actual</span>
                    </div>
                    <div class="dx-v2-cod-fields-stack">
                        <div class="dx-v2-cod-field-row">
                            <div class="dx-v2-cod-input-wrap">
                                <i class="fa-solid fa-terminal"></i>
                                <input type="text" 
                                       x-model="formData.Hostname_Old" 
                                       @input="formData.Hostname_Old = formData.Hostname_Old.replace(/[^a-zA-Z0-9-]/g, '')"
                                       class="dx-v2-form-input" 
                                       placeholder="Hostname" 
                                       :required="formData.docType !== 'Change_NodeLocked'" 
                                       :disabled="formData.docType === 'Change_NodeLocked'">
                            </div>
                        </div>
                        <div class="dx-v2-cod-field-row">
                            <div class="dx-v2-cod-input-wrap">
                                <i class="fa-solid fa-code"></i>
                                <input type="text" 
                                       x-model="formData.Composite_Old" 
                                       @input="formData.Composite_Old = formData.Composite_Old.replace(/[^a-zA-Z0-9]/g, '')"
                                       class="dx-v2-form-input" 
                                       placeholder="Composite" 
                                       :required="['Change_Composite', 'Change_Full'].includes(formData.docType)" 
                                       :disabled="['Change_NodeLocked', 'Change_Cloud'].includes(formData.docType)" 
                                       maxlength="12">
                            </div>
                        </div>
                        <div class="dx-v2-cod-field-row">
                            <div class="dx-v2-cod-input-wrap">
                                <i class="fa-solid fa-id-card"></i>
                                <input type="text" 
                                       x-model="formData.MAC_Old" 
                                       @input="formData.MAC_Old = formData.MAC_Old.replace(/[^a-zA-Z0-9]/g, '')"
                                       class="dx-v2-form-input" 
                                       placeholder="LM Host (MAC) (sin guiones)" 
                                       :required="['Change_NodeLocked', 'Change_Full'].includes(formData.docType)" 
                                       :disabled="['Change_Composite', 'Change_Cloud'].includes(formData.docType)" 
                                       maxlength="12">
                            </div>
                        </div>
                        <div class="dx-v2-cod-field-row">
                            <div class="dx-v2-cod-input-wrap">
                                <i class="fa-brands fa-aws"></i>
                                <input type="text" 
                                       x-model="formData.Cloud_AWS_Old" 
                                       class="dx-v2-form-input" 
                                       placeholder="Cloud AWS (Opcional)" 
                                       :disabled="formData.docType !== 'Change_Cloud'">
                            </div>
                        </div>
                        <div class="dx-v2-cod-field-row">
                            <div class="dx-v2-cod-input-wrap">
                                <i class="fa-brands fa-microsoft"></i>
                                <input type="text" 
                                       x-model="formData.Cloud_Azure_Old" 
                                       class="dx-v2-form-input" 
                                       placeholder="Cloud Azure (Opcional)" 
                                       :disabled="formData.docType !== 'Change_Cloud'">
                            </div>
                        </div>
                        
                        <!-- MACs Adicionales -->
                        <template x-for="(mac, index) in formData.MAC_Old_Extra" :key="index">
                            <div class="dx-v2-cod-field-row">
                                <div class="dx-v2-cod-input-wrap">
                                    <i class="fa-solid fa-id-card opacity-50"></i>
                                    <input type="text" 
                                           x-model="formData.MAC_Old_Extra[index]" 
                                           @input="formData.MAC_Old_Extra[index] = formData.MAC_Old_Extra[index].replace(/[^a-zA-Z0-9]/g, '')"
                                           class="dx-v2-form-input" 
                                           placeholder="LM Host (MAC) Extra (sin guiones)" 
                                           maxlength="12">
                                    <button type="button" class="dx-v2-cod-remove-btn" @click="removeMacPair(index)">&times;</button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Nueva Máquina -->
                <div class="dx-v2-cod-form-section">
                    <div class="dx-v2-cod-section-title dx-v2-cod-section-title-wrapper" style="min-height: 42px; display: flex; align-items: center; justify-content: space-between;">
                        <div class="dx-v2-cod-title-inline" style="display: flex; align-items: center; gap: 8px;">
                            <i class="fa-solid fa-tower-broadcast"></i>
                            <span>Nueva Máquina</span>
                        </div>
                        <button type="button" class="dx-v2-cod-btn-ai-mini shadow-sm" @click="openAiModal()">
                            <div class="dx-v2-cod-ai-icon-pulse-mini">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M12 2C12 2 12.6 7.4 14.5 9.5C16.6 11.4 22 12 22 12C22 12 16.6 12.6 14.5 14.5C12.6 16.6 12 22 12 22C12 22 11.4 16.6 9.5 14.5C7.4 12.6 2 12 2 12C2 12 7.4 11.4 9.5 9.5C11.4 7.4 12 2 12 2Z" fill="currentColor"/>
                                </svg>
                            </div>
                            <span>Analizar Composite.txt</span>
                        </button>
                    </div>
                    <div class="dx-v2-cod-fields-stack">
                        <div class="dx-v2-cod-field-row">
                            <div class="dx-v2-cod-input-wrap">
                                <i class="fa-solid fa-terminal"></i>
                                <input type="text" 
                                       x-model="formData.Hostname_New" 
                                       @input="formData.Hostname_New = formData.Hostname_New.replace(/[^a-zA-Z0-9-]/g, '')"
                                       class="dx-v2-form-input" 
                                       placeholder="Hostname" 
                                       :required="formData.docType !== 'Change_NodeLocked'" 
                                       :disabled="formData.docType === 'Change_NodeLocked'">
                            </div>
                        </div>
                        <div class="dx-v2-cod-field-row">
                            <div class="dx-v2-cod-input-wrap">
                                <i class="fa-solid fa-code"></i>
                                <input type="text" 
                                       x-model="formData.Composite_New" 
                                       @input="formData.Composite_New = formData.Composite_New.replace(/[^a-zA-Z0-9]/g, '')"
                                       class="dx-v2-form-input" 
                                       placeholder="Composite" 
                                       :required="['Change_Composite', 'Change_Full', 'Change_Cloud'].includes(formData.docType)" 
                                       :disabled="formData.docType === 'Change_NodeLocked'" 
                                       maxlength="12">
                            </div>
                        </div>
                        <div class="dx-v2-cod-field-row">
                            <div class="dx-v2-cod-input-wrap">
                                <i class="fa-solid fa-id-card"></i>
                                <input type="text" 
                                       x-model="formData.MAC_New" 
                                       @input="formData.MAC_New = formData.MAC_New.replace(/[^a-zA-Z0-9]/g, '')"
                                       class="dx-v2-form-input" 
                                       placeholder="LM Host (MAC) (sin guiones)" 
                                       :required="['Change_NodeLocked', 'Change_Full'].includes(formData.docType)" 
                                       :disabled="['Change_Composite', 'Change_Cloud'].includes(formData.docType)" 
                                       maxlength="12">
                            </div>
                        </div>
                        <div class="dx-v2-cod-field-row">
                            <div class="dx-v2-cod-input-wrap">
                                <i class="fa-brands fa-aws"></i>
                                <input type="text" 
                                       x-model="formData.Cloud_AWS_New" 
                                       class="dx-v2-form-input" 
                                       placeholder="Nuevo Cloud AWS (Opcional)" 
                                       :disabled="formData.docType !== 'Change_Cloud'">
                            </div>
                        </div>
                        <div class="dx-v2-cod-field-row">
                            <div class="dx-v2-cod-input-wrap">
                                <i class="fa-brands fa-microsoft"></i>
                                <input type="text" 
                                       x-model="formData.Cloud_Azure_New" 
                                       class="dx-v2-form-input" 
                                       placeholder="Nuevo Cloud Azure (Opcional)" 
                                       :disabled="formData.docType !== 'Change_Cloud'">
                            </div>
                        </div>

                        <!-- MACs Adicionales -->
                        <template x-for="(mac, index) in formData.MAC_New_Extra" :key="index">
                            <div class="dx-v2-cod-field-row">
                                <div class="dx-v2-cod-input-wrap">
                                    <i class="fa-solid fa-id-card opacity-50"></i>
                                    <input type="text" 
                                           x-model="formData.MAC_New_Extra[index]" 
                                           @input="formData.MAC_New_Extra[index] = formData.MAC_New_Extra[index].replace(/[^a-zA-Z0-9]/g, '')"
                                           class="dx-v2-form-input" 
                                           placeholder="Nuevo LM Host (MAC) Extra (sin guiones)" 
                                           maxlength="12">
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Botón Añadir MACs (NodeLocked y Full) -->
            <div class="flex justify-center mt-2" x-show="['Change_NodeLocked', 'Change_Full'].includes(formData.docType)">
                <button type="button" class="dx-v2-cod-btn-add-mac" @click="addMacPair()">
                    <i class="fa-solid fa-plus"></i> Añadir par de MACs
                </button>
            </div>
        </div>

        <!-- FOOTER: CONTROLES Y ACCIONES -->
        <div class="dx-v2-cod-card-footer">
            <div class="dx-v2-cod-footer-left">
                <!-- Idioma -->
                <div class="dx-v2-cod-segmented-wrapper small">
                    <div class="dx-v2-cod-segmented-small relative">
                        <div class="dx-v2-cod-active-indicator-small" 
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
                <div class="dx-v2-cod-segmented-wrapper small">
                    <div class="dx-v2-cod-segmented-small relative">
                        <div class="dx-v2-cod-active-indicator-small" 
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

            <div class="dx-v2-cod-footer-right">
                <!-- Limpiar (Estilo Selector) -->
                <div class="dx-v2-cod-btn-wrapper-tech">
                    <button type="button" class="dx-v2-cod-btn-tech-base dx-v2-cod-btn-clear" @click="resetForm()">
                        <i class="fa-solid fa-eraser"></i> Limpiar
                    </button>
                </div>

                <!-- Generar (Abre Preview) -->
                <button type="button" class="dx-v2-cod-btn-generate" @click="openPreview()" :disabled="isGenerating">
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

    <!-- Guía getcid.exe (Acordeón) -->
    <div x-data="{ open: false }" class="dx-v2-cod-card" style="margin-top: 24px; padding: 0; overflow: hidden; background-color: var(--surface, #161B22); border: 1px solid var(--border, #30363D); border-radius: 10px;">
        <button type="button" @click="open = !open" style="display: flex; justify-content: space-between; align-items: center; width: 100%; padding: 16px 24px; background: transparent; border: none; cursor: pointer; text-align: left; color: var(--primary, #E6EDF3);">
            <div style="display: flex; align-items: center; gap: 12px;">
                <i class="fa-solid fa-book-journal-whills" style="color: var(--accent, #388BFD); font-size: 16px;"></i>
                <h3 style="margin: 0; font-size: 14px; font-weight: 600;">Guía: Obtención de Identificadores (getcid.exe)</h3>
            </div>
            <i class="fa-solid fa-chevron-down" style="color: var(--muted, #8B949E); transition: transform 0.3s;" :style="open ? 'transform: rotate(180deg);' : ''"></i>
        </button>
        <div x-show="open" x-collapse>
            <div style="padding: 24px; border-top: 1px solid var(--border, #30363D);">
                <p style="font-size: 13px; color: var(--secondary, #CDD9E5); margin-bottom: 16px; line-height: 1.6; margin-top: 0;">
                    Para obtener el <strong>Composite ID</strong> correcto en versiones modernas, es necesario usar la utilidad <code style="background-color: var(--raised, #21262D); padding: 2px 6px; border-radius: 4px; font-family: monospace;">getcid.exe</code>.
                </p>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px; margin-bottom: 24px;">
                    <!-- Caja de Descarga Siemens -->
                    <div style="background-color: var(--raised, #21262D); padding: 16px; border-radius: 6px; border: 1px solid var(--border, #30363D);">
                        <div style="color: var(--primary, #E6EDF3); font-weight: 600; font-size: 13px; margin-bottom: 6px; display: flex; align-items: center;">
                            <i class="fa-solid fa-building" style="width: 24px; color: var(--muted, #8B949E);"></i> Oficial Siemens (Support Center)
                        </div>
                        <div style="font-size: 12px; color: var(--secondary, #CDD9E5); margin-bottom: 10px;">
                            Descarga la utilidad oficial desde el portal de soporte de Siemens.
                        </div>
                        <a href="https://support.sw.siemens.com/es-ES/product/1586485382/downloads" target="_blank" style="color: var(--accent, #388BFD); text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; margin-bottom: 8px; font-size: 13px;">
                            <i class="fa-solid fa-download" style="margin-right: 6px;"></i> Siemens License Server v5.2.0 (FNP 11.19.8.4)
                        </a>
                        <div style="font-size: 12px; color: var(--muted, #8B949E);">Busca el archivo: <code style="background-color: var(--surface, #161B22); padding: 2px 6px; border-radius: 4px; font-family: monospace; border: 1px solid var(--border, #30363D);">slsi_hostid_utils-v5.2.0.0.zip</code></div>
                    </div>
                    
                    <!-- Placeholder ATS -->
                    <div style="background-color: var(--surface, #161B22); padding: 16px; border-radius: 6px; border: 1px dashed var(--border, #30363D);">
                        <div style="color: var(--primary, #E6EDF3); font-weight: 600; font-size: 13px; margin-bottom: 6px; display: flex; align-items: center;">
                            <i class="fa-solid fa-screwdriver-wrench" style="width: 24px; color: var(--muted, #8B949E);"></i> Utilidad Personalizada (ATS)
                        </div>
                        <div style="font-size: 12px; color: var(--secondary, #CDD9E5); margin-bottom: 10px;">
                            (Espacio reservado para añadir el enlace a la utilidad personalizada)
                        </div>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px;">
                    <!-- Caja 1 -->
                    <div style="background-color: var(--raised, #21262D); padding: 16px; border-radius: 6px; border: 1px solid var(--border, #30363D);">
                        <div style="color: var(--accent, #388BFD); font-weight: 600; font-size: 13px; margin-bottom: 8px; display: flex; align-items: center;">
                            <i class="fa-solid fa-terminal" style="width: 24px;"></i> Todos los IDs (Recomendado)
                        </div>
                        <div style="font-family: monospace; font-size: 12px; color: var(--secondary, #CDD9E5); margin-bottom: 8px; opacity: 0.8; user-select: all;">.\getcid.exe -allcomposite</div>
                        <div style="font-family: monospace; font-size: 12px; color: var(--success, #3FB950); line-height: 1.6;">COMPOSITE=37B5ED1AC61D<br>COMPOSITE=8AFD1291ABCD</div>
                    </div>
                    
                    <!-- Caja 2 -->
                    <div style="background-color: var(--raised, #21262D); padding: 16px; border-radius: 6px; border: 1px solid var(--border, #30363D);">
                        <div style="color: var(--accent, #388BFD); font-weight: 600; font-size: 13px; margin-bottom: 8px; display: flex; align-items: center;">
                            <i class="fa-brands fa-aws" style="width: 24px;"></i> Entorno Cloud (Auto)
                        </div>
                        <div style="font-family: monospace; font-size: 12px; color: var(--secondary, #CDD9E5); margin-bottom: 8px; opacity: 0.8; user-select: all;">.\getcid.exe -cloud</div>
                        <div style="font-family: monospace; font-size: 12px; color: var(--success, #3FB950); line-height: 1.6;">CLOUD_PROVIDER=AWS<br>AMZN_ID=i-000143790d2c...<br>COMPOSITE=37B5ED1AC61D</div>
                    </div>

                    <!-- Caja 3 -->
                    <div style="background-color: var(--raised, #21262D); padding: 16px; border-radius: 6px; border: 1px solid var(--border, #30363D);">
                        <div style="color: var(--accent, #388BFD); font-weight: 600; font-size: 13px; margin-bottom: 8px; display: flex; align-items: center;">
                            <i class="fa-brands fa-microsoft" style="width: 24px;"></i> Microsoft Azure
                        </div>
                        <div style="font-family: monospace; font-size: 12px; color: var(--secondary, #CDD9E5); margin-bottom: 8px; opacity: 0.8; user-select: all;">.\getcid.exe -azure</div>
                        <div style="font-family: monospace; font-size: 12px; color: var(--success, #3FB950); line-height: 1.6;">AZURE_ID=7f3a1b8e9cdd...<br>COMPOSITE=8AFD1291ABCD</div>
                    </div>

                    <!-- Caja 4 -->
                    <div style="background-color: var(--raised, #21262D); padding: 16px; border-radius: 6px; border: 1px solid var(--border, #30363D);">
                        <div style="color: var(--accent, #388BFD); font-weight: 600; font-size: 13px; margin-bottom: 8px; display: flex; align-items: center;">
                            <i class="fa-solid fa-server" style="width: 24px;"></i> VMware / Hyper-V
                        </div>
                        <div style="font-family: monospace; font-size: 12px; color: var(--secondary, #CDD9E5); margin-bottom: 8px; opacity: 0.8; user-select: all;">.\getcid.exe -vmware</div>
                        <div style="font-family: monospace; font-size: 12px; color: var(--success, #3FB950); line-height: 1.6;">VMWARE_UUID=4201a8c0...<br>COMPOSITE=AA11BB22CC33</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Overlay de Previsualización Limpia -->
    <div class="dx-v2-cod-preview-overlay" x-show="showPreview" x-transition x-cloak>
        <div class="dx-v2-cod-preview-modal shadow-premium" @click.away="showPreview = false">
            <div class="dx-v2-cod-preview-header">
                <div class="dx-v2-cod-preview-title-container">
                    <i class="fa-solid fa-file-pdf text-accent"></i>
                    <span class="dx-v2-cod-preview-title">Vista Previa</span>
                </div>
                <button type="button" class="dx-v2-cod-btn-close-minimal" @click="showPreview = false">
                    <i class="fa-solid fa-xmark"></i>
                    <span>Cerrar</span>
                </button>
            </div>
            <div class="dx-v2-cod-preview-body">
                <div x-show="isPreviewLoading" class="dx-v2-cod-preview-loader">
                    <i class="fa-solid fa-circle-notch fa-spin"></i>
                    <span>Cargando documento...</span>
                </div>
                <iframe x-show="!isPreviewLoading" :src="previewUrl" frameborder="0"></iframe>
            </div>
            <div class="dx-v2-cod-preview-footer">
                <button type="button" class="dx-v2-cod-btn-tech-base" @click="showPreview = false">
                    <i class="fa-solid fa-arrow-left"></i> <span>Volver a Editar</span>
                </button>
                <button type="button" class="dx-v2-cod-btn-generate" @click="generate('store')" :disabled="isSaving">
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
    <div class="dx-v2-cod-preview-overlay" x-show="showAiModal" x-transition x-cloak>
        <div class="dx-v2-cod-ai-modal shadow-premium" @click.away="showAiModal = false">
            <div class="dx-v2-cod-preview-header">
                <div class="dx-v2-cod-preview-title-container">
                    <i class="fa-solid fa-microchip-ai text-accent"></i>
                    <span class="dx-v2-cod-preview-title">Asistente Inteligente de Composite</span>
                </div>
                <button type="button" class="dx-v2-cod-btn-close-minimal" @click="showAiModal = false">
                    <i class="fa-solid fa-xmark"></i>
                    <span>Cerrar</span>
                </button>
            </div>
            <div class="dx-v2-cod-ai-modal-body">
                <div x-show="!aiResult" x-transition>
                    <p class="dx-v2-cod-ai-hint">
                        <i class="fa-solid fa-wand-magic-sparkles mr-2 text-accent"></i>
                        Pega el listado de adaptadores o arrastra el archivo <strong>composite.txt</strong> para un análisis automático.
                    </p>
                    
                    <div class="dx-v2-cod-ai-upload-zone mb-4" 
                         @click="$refs.fileInput.click()" 
                         @dragover.prevent="isDragging = true" 
                         @dragleave.prevent="isDragging = false" 
                         @drop.prevent="isDragging = false; handleDrop($event)"
                         :class="{ 'active': isDragging, 'has-file': fileName }">
                        
                        <input type="file" x-ref="fileInput" class="hidden" @change="handleFileUpload($event)" accept=".txt">
                        
                        <template x-if="!fileName">
                            <div class="flex flex-col items-center justify-center gap-6 w-full">
                                <div class="dx-v2-cod-upload-icon-wrapper">
                                    <i class="fa-solid fa-file-circle-plus"></i>
                                </div>
                                <div class="dx-v2-cod-upload-text-content">
                                    <span class="dx-v2-cod-upload-main-text text-lg">Haz clic o arrastra el archivo composite.txt</span>
                                    <span class="dx-v2-cod-upload-sub-text">Sube el log de adaptadores para análisis instantáneo</span>
                                </div>
                            </div>
                        </template>

                        <template x-if="fileName">
                            <div class="flex flex-col items-center gap-3 fade-in w-full">
                                <div class="dx-v2-cod-upload-icon-wrapper success">
                                    <i class="fa-solid fa-file-circle-check"></i>
                                </div>
                                <div class="flex flex-col items-center gap-1">
                                    <span class="font-bold text-primary" x-text="fileName"></span>
                                    <span class="text-xs text-muted">Archivo listo para procesar</span>
                                </div>
                                <button type="button" @click.stop="clearFile()" class="dx-v2-cod-btn-clear-file">
                                    <i class="fa-solid fa-trash-can"></i>
                                    <span>Quitar archivo</span>
                                </button>
                            </div>
                        </template>

                        <div class="dx-v2-cod-upload-progress-bar" x-show="isAiProcessing">
                            <div class="dx-v2-cod-upload-progress-inner"></div>
                        </div>
                    </div>

                    <textarea x-model="aiInput" 
                              class="dx-v2-cod-ai-textarea font-mono" 
                              placeholder="O pega el texto aquí..."></textarea>
                    
                    <div class="dx-v2-cod-modal-btn-row">
                        <button type="button" class="dx-v2-cod-btn-generate" @click="processAi()" :disabled="isAiProcessing || !aiInput">
                            <span x-show="!isAiProcessing">Analizar con Gemini</span>
                            <span x-show="isAiProcessing"><i class="fa-solid fa-spinner fa-spin me-2"></i> Procesando...</span>
                        </button>
                    </div>
                </div>
 
                <div x-show="aiResult" x-transition>
                    <div class="dx-v2-cod-ai-result-box">
                        <div class="dx-v2-cod-ai-result-header">
                            <div class="dx-v2-cod-ai-icon-pulse-mini">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M12 2C12 2 12.6 7.4 14.5 9.5C16.6 11.4 22 12 22 12C22 12 16.6 12.6 14.5 14.5C12.6 16.6 12 22 12 22C12 22 11.4 16.6 9.5 14.5C7.4 12.6 2 12 2 12C2 12 7.4 11.4 9.5 9.5C11.4 7.4 12 2 12 2Z" fill="currentColor"/>
                                </svg>
                            </div>
                            <span>Hardware Recomendado</span>
                        </div>
                        <div class="dx-v2-cod-ai-result-grid">
                            <div class="dx-v2-cod-ai-result-item">
                                <label>Hostname</label>
                                <span x-text="aiResult?.hostname || 'N/A'"></span>
                            </div>
                            <div class="dx-v2-cod-ai-result-item">
                                <label>Composite</label>
                                <span class="font-mono" x-text="aiResult?.composite || 'N/A'"></span>
                            </div>
                            <div class="dx-v2-cod-ai-result-item">
                                <label>MAC Address</label>
                                <span class="font-mono" x-text="aiResult?.mac || 'N/A'"></span>
                            </div>
                        </div>
                        <div class="dx-v2-cod-ai-result-footer">
                            <div class="dx-v2-cod-ai-adapter-label">Adaptador: <span x-text="aiResult?.adapter" class="text-primary"></span></div>
                            <p class="dx-v2-cod-ai-reason">
                                <i class="fa-solid fa-info-circle me-1"></i> <span x-text="aiResult?.reason"></span>
                            </p>
                        </div>
                    </div>
 
                    <div class="dx-v2-cod-modal-action-row">
                        <button type="button" class="dx-v2-cod-btn-secondary-tech" @click="aiResult = null">
                            <i class="fa-solid fa-arrow-left"></i> <span>Volver a Analizar</span>
                        </button>
                        <button type="button" class="dx-v2-cod-btn-generate" @click="applyAiResult()">
                            <i class="fa-solid fa-bolt"></i> <span>Aplicar a Nueva Máquina</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

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
            Cloud_AWS_Old: '',
            Cloud_Azure_Old: '',
            Hostname_New: '',
            Composite_New: '',
            MAC_New: '',
            Cloud_AWS_New: '',
            Cloud_Azure_New: '',
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
                Cloud_AWS_Old: '',
                Cloud_Azure_Old: '',
                Hostname_New: '',
                Composite_New: '',
                MAC_New: '',
                Cloud_AWS_New: '',
                Cloud_Azure_New: '',
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
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(this.formData)
            })
            .then(async response => {
                if (!response.ok) {
                    const err = await response.json();
                    throw new Error(err.message || 'Error de validación');
                }
                return response.blob();
            })
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
                alert('Error al generar la vista previa: ' + error.message);
            });
        },

        generate(action) {
            this.isSaving = true;

            fetch('{{ route("tools.cod.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(this.formData)
            })
            .then(async response => {
                const data = await response.json();
                if (!response.ok) {
                    throw new Error(data.message || 'Error de validación');
                }
                return data;
            })
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
                alert('Error al procesar la solicitud: ' + error.message);
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
