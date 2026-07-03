@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/tools/dx-v2-tools-time-tracking.css?v=' . time()) }}">
@endpush

@section('content')
<div x-data="timeTracking()">
    <div class="dx-v2-page-header" style="flex-direction: column; align-items: flex-start; gap: 16px;">
        <div style="width: 100%;">
            <div class="breadcrumb">
                <a href="{{ route('tools.index') }}">Herramientas</a>
                <span class="separator">/</span>
                <span class="current">Imputación de Horas</span>
            </div>
            <h1 class="page-title">Buscador de <span>Proyectos</span></h1>
            <p class="page-subtitle">Búsqueda de clientes para la correcta imputación de horas de soporte.</p>
        </div>

        <div class="search-box dx-v2-clients-search-box" style="margin: 0; width: 100%; justify-content: flex-start;">
            <div class="dx-v2-clients-search-form" style="max-width: 500px; width: 100%;">
                <svg class="dx-v2-clients-search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <input type="text" x-model="searchQuery"
                    placeholder="Buscar clientes por nombre o identificador..."
                    class="dx-v2-form-input dx-v2-clients-search-input"
                    autocomplete="off">
            </div>
        </div>
    </div>

    <div class="dx-v2-sys-dash-stats-grid" style="margin-bottom: 24px;">
        <div class="dx-v2-sys-dash-stat-card">
            <div class="dx-v2-sys-dash-stat-card-watermark">
                <svg width="84" height="84" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            </div>
            <div class="dx-v2-sys-dash-stat-card-title">SIMCENTER / AMESIM</div>
            <div class="dx-v2-sys-dash-stat-card-value" style="color: #0284c7;" x-text="countPP">0</div>
            <div class="dx-v2-sys-dash-stat-card-meta-mono">PROYECTO: 100052480</div>
        </div>

        <div class="dx-v2-sys-dash-stat-card">
            <div class="dx-v2-sys-dash-stat-card-watermark">
                <svg width="84" height="84" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
            <div class="dx-v2-sys-dash-stat-card-title">TEAMCENTER</div>
            <div class="dx-v2-sys-dash-stat-card-value" style="color: #16a34a;" x-text="countPD">0</div>
            <div class="dx-v2-sys-dash-stat-card-meta-mono">PROYECTO: 100052479</div>
        </div>

        <div class="dx-v2-sys-dash-stat-card">
            <div class="dx-v2-sys-dash-stat-card-watermark">
                <svg width="84" height="84" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10M9 12l2 2 4-4"/></svg>
            </div>
            <div class="dx-v2-sys-dash-stat-card-title">CAD / CAM</div>
            <div class="dx-v2-sys-dash-stat-card-value" style="color: #ca8a04;" x-text="countCA">0</div>
            <div class="dx-v2-sys-dash-stat-card-meta-mono">PROYECTO: 100052478</div>
        </div>

        <div class="dx-v2-sys-dash-stat-card">
            <div class="dx-v2-sys-dash-stat-card-watermark">
                <svg width="84" height="84" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
            </div>
            <div class="dx-v2-sys-dash-stat-card-title">SOPORTE GENERAL</div>
            <div class="dx-v2-sys-dash-stat-card-value" style="color: var(--muted);" x-text="countSoporte">0</div>
            <div class="dx-v2-sys-dash-stat-card-meta-mono">RESTO / INDEFINIDO</div>
        </div>
    </div>

    <div class="card">
        <div class="table-responsive" style="overflow-x: auto;">
            <table class="table">
                <thead>
                    <tr>
                        <th style="white-space: nowrap; width: 1%;">COST CENTER</th>
                        <th>CLIENTE</th>
                        <th class="text-right" style="white-space: nowrap; width: 1%;">SUB PRODUCTO</th>
                        <th class="text-center" style="white-space: nowrap; width: 1%;">FECHA FIN</th>
                        <th class="text-center" style="white-space: nowrap; width: 1%;">PROYECTO</th>
                        <th style="width: 1%;"></th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="(item, index) in filteredResults" :key="index">
                        <tr class="tt-row" :class="'tt-row-' + item.imputation_code">
                            <td style="color: var(--muted); font-family: monospace; white-space: nowrap;">
                                <span x-text="item.cost_center || '-'"></span>
                            </td>
                            <td>
                                <div class="font-bold text-sm" style="color: var(--text);" x-text="item.client_name"></div>
                            </td>
                            <td class="text-right text-muted" style="white-space: nowrap;" x-text="item.sub_product || '-'"></td>
                            <td class="text-right" style="white-space: nowrap;" x-text="item.end_date"></td>
                            <td class="text-right" style="white-space: nowrap;">
                                <span class="imputation-badge" 
                                      :class="'imputation-' + item.imputation_code" 
                                      x-text="item.imputation_name">
                                </span>
                            </td>
                            <td class="text-center" style="white-space: nowrap; width: 1%; vertical-align: middle;">
                                <div x-data="{ copied: false }">
                                    <template x-if="item.imputation_code !== 'SOPORTE'">
                                        <button @click="
                                                    navigator.clipboard.writeText('Por favor, registra las horas trabajadas en el proyecto ' + item.imputation_code);
                                                    copied = true;
                                                    setTimeout(() => copied = false, 2000);
                                                "
                                                style="background: none; border: none; cursor: pointer; padding: 4px; color: var(--text); opacity: 0.6; transition: all 0.2s; outline: none;"
                                                onmouseover="this.style.opacity='1'; this.style.color='var(--text)'"
                                                onmouseout="this.style.opacity='0.6'; this.style.color='var(--text)'"
                                                title="Copiar texto para reporte">
                                            <i class="fa-solid fa-check text-success" x-show="copied" x-cloak style="opacity: 1;"></i>
                                            <i class="fa-regular fa-copy" x-show="!copied"></i>
                                        </button>
                                    </template>
                                    <template x-if="item.imputation_code === 'SOPORTE'">
                                        <button disabled
                                                style="background: none; border: none; padding: 4px; color: var(--text); opacity: 0.15; cursor: not-allowed;"
                                                title="No copiable">
                                            <i class="fa-regular fa-copy"></i>
                                        </button>
                                    </template>
                                </div>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="!loading && filteredResults.length === 0" x-cloak>
                        <td colspan="6" class="text-center p-8 text-muted">
                            <i class="fa-solid fa-inbox fa-3x mb-3" style="opacity: 0.2;"></i>
                            <p>No se encontraron contratos para la búsqueda.</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('timeTracking', () => ({
            searchQuery: '',
            allResults: [],
            loading: true,

            async init() {
                try {
                    const response = await fetch(`{{ route('tools.time-tracking.search') }}`);
                    const data = await response.json();
                    this.allResults = data;
                } catch (error) {
                    console.error('Error fetching data:', error);
                    this.allResults = [];
                } finally {
                    this.loading = false;
                }
            },

            get filteredResults() {
                if (this.searchQuery.trim() === '') {
                    return this.allResults;
                }
                const query = this.searchQuery.toLowerCase();
                return this.allResults.filter(item => {
                    return (item.client_name && item.client_name.toLowerCase().includes(query)) ||
                           (item.cost_center && item.cost_center.toLowerCase().includes(query)) ||
                           (item.imputation_code && item.imputation_code.toLowerCase().includes(query));
                });
            },

            get countPP() {
                return this.filteredResults.filter(item => item.imputation_code === '100052480').length;
            },

            get countPD() {
                return this.filteredResults.filter(item => item.imputation_code === '100052479').length;
            },

            get countCA() {
                return this.filteredResults.filter(item => item.imputation_code === '100052478').length;
            },

            get countSoporte() {
                return this.filteredResults.filter(item => item.imputation_code === 'SOPORTE').length;
            }
        }));
    });
</script>
@endpush
