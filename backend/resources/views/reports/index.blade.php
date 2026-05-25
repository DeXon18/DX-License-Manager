@extends('layouts.app')

@section('title', 'Analítica de Licencias')

@push('styles')
@endpush

@section('content')
<div class="dx-v2-page-header">
    <div>
        <div class="breadcrumb">
            <a href="{{ route('reports.index') }}">Operaciones</a>
            <span class="separator">/</span>
            <span class="current">Reportes</span>
        </div>
        <h1 class="page-title">Analítica y <span>Reportes</span></h1>
        <p class="page-subtitle">Distribución de software, caducidades y reportes por cliente</p>
    </div>
</div>

<!-- Extra Fonts for Admin NOC -->
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@600;700&display=swap" rel="stylesheet">

<div class="dashboard-container">
    <div class="reports-grid">
        <!-- Top Products Chart -->
        <div class="card">
            <div class="card-header">
                <span class="card-title"><i class="fa-solid fa-ranking-star"></i> Top 10 Productos Asignados</span>
            </div>
            <div class="card-body">
                <div class="chart-container" style="position: relative; height: 300px; width: 100%;">
                    <canvas id="topProductsChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Expirations Timeline Chart -->
        <div class="card">
            <div class="card-header">
                <span class="card-title"><i class="fa-solid fa-timeline"></i> Curva Expiraciones (12M)</span>
            </div>
            <div class="card-body">
                <div class="chart-container" style="position: relative; height: 300px; width: 100%;">
                    <canvas id="expirationsChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="reports-grid">
        <!-- Client Report Generator -->
        <div class="card" x-data="reportGenerator()">
            <div class="card-header">
                <span class="card-title"><i class="fa-regular fa-file-pdf"></i> Generador de Reporte</span>
            </div>
            <div class="card-body">
                <p style="color: var(--dx-v2-muted); font-size: 0.85rem; margin-bottom: 20px;">
                    Selecciona un cliente para exportar su informe de licencias activas y contratos en formato PDF.
                </p>
                
                <div style="margin-bottom: 20px; position: relative;" x-data="{ showSuggestions: false }" @click.away="showSuggestions = false">
                    <div style="position: relative;">
                        <input type="text" 
                               x-model="searchQuery" 
                               @input="showSuggestions = true; selectedClientId = null"
                               @focus="showSuggestions = true"
                               class="dx-v2-form-input" 
                               placeholder="Escribe para buscar un cliente..." 
                               autocomplete="off"
                               style="padding-right: 36px;">
                        <i class="fa-solid fa-search" style="position: absolute; right: 14px; top: 13px; color: var(--dx-v2-muted);"></i>
                        
                        <!-- Sugerencias de Empresa -->
                        <div x-show="showSuggestions && filteredClients().length > 0" 
                             style="position: absolute; top: 100%; left: 0; right: 0; background: var(--dx-v2-surface); border: 1px solid var(--dx-v2-border); border-radius: 6px; margin-top: 4px; max-height: 250px; overflow-y: auto; z-index: 50; box-shadow: 0 4px 12px rgba(0,0,0,0.2); display: none;">
                            <template x-for="client in filteredClients()" :key="client.id">
                                <div style="padding: 12px 14px; cursor: pointer; border-bottom: 1px solid var(--dx-v2-border);" 
                                     @click="selectClient(client); showSuggestions = false"
                                     onmouseover="this.style.background='var(--dx-v2-raised)'"
                                     onmouseout="this.style.background='transparent'">
                                    <div style="font-size: 0.85rem; color: var(--dx-v2-primary); font-weight: 500;" x-text="client.name"></div>
                                    <div style="font-size: 0.75rem; color: var(--dx-v2-accent); margin-top: 2px;">
                                        <i class="fa-solid fa-check-circle" style="font-size: 10px;"></i> Licencias Activas
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <div style="display: flex; justify-content: flex-end;">
                    <button type="button" @click="downloadReport()" class="dx-v2-sys-dash-btn-noc accent-btn" :style="selectedClientId ? '' : 'pointer-events: none; opacity: 0.5;'">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3"/></svg>
                        <span>Descargar PDF</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Top Clients -->
        <div class="card">
            <div class="card-header">
                <span class="card-title"><i class="fa-solid fa-users"></i> Top 5 Clientes (Por Licencias)</span>
            </div>
            <div class="card-body" style="padding: 0;">
                <table class="table dx-v2-table" style="margin: 0; width: 100%;">
                    <thead>
                        <tr>
                            <th>Cliente</th>
                            <th class="text-right">Asientos</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($topClients as $tc)
                        <tr>
                            <td>{{ $tc->name }}</td>
                            <td class="text-right">
                                <span class="badge" style="background: var(--dx-v2-raised); color: var(--dx-v2-primary); border: 1px solid var(--dx-v2-border);">{{ number_format($tc->total_seats) }}</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('reportGenerator', () => ({
        clients: @json($clients),
        searchQuery: '',
        selectedClientId: null,

        filteredClients() {
            if (!this.searchQuery) return [];
            const search = this.searchQuery.toLowerCase();
            return this.clients.filter(c => c.name.toLowerCase().includes(search)).slice(0, 6);
        },

        selectClient(client) {
            this.selectedClientId = client.id;
            this.searchQuery = client.name;
        },

        downloadReport() {
            if (!this.selectedClientId) return;
            window.location.href = `/reports/client/${this.selectedClientId}/pdf`;
        }
    }))
})
</script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 2. Chart Configurations
    const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
    const textColor = isDark ? '#a1a1aa' : '#64748b';
    const gridColor = isDark ? '#27272a' : '#f1f5f9';

    Chart.defaults.color = textColor;
    Chart.defaults.font.family = "'Inter', sans-serif";

    // Top Products Data
    const topProductsRaw = @json($topProducts);
    const topLabels = topProductsRaw.map(p => p.product_code);
    const topData = topProductsRaw.map(p => p.total_quantity);

    new Chart(document.getElementById('topProductsChart'), {
        type: 'bar',
        data: {
            labels: topLabels,
            datasets: [{
                label: 'Asientos Activos',
                data: topData,
                backgroundColor: 'rgba(56, 189, 248, 0.8)', // light blue
                borderRadius: 4,
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: isDark ? '#18181b' : '#ffffff',
                    titleColor: isDark ? '#ffffff' : '#0f172a',
                    bodyColor: isDark ? '#a1a1aa' : '#64748b',
                    borderColor: gridColor,
                    borderWidth: 1,
                    padding: 12
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: gridColor },
                    ticks: { precision: 0 }
                },
                x: {
                    grid: { display: false },
                    ticks: {
                        maxRotation: 45,
                        minRotation: 45
                    }
                }
            }
        }
    });

    // Expirations Timeline Data
    const expirationsRaw = @json($expirationsTimeline);
    const expLabels = expirationsRaw.map(e => e.label);
    const expData = expirationsRaw.map(e => e.count);

    new Chart(document.getElementById('expirationsChart'), {
        type: 'line',
        data: {
            labels: expLabels,
            datasets: [{
                label: 'Licencias a Expirar',
                data: expData,
                borderColor: '#f43f5e', // rose
                backgroundColor: 'rgba(244, 63, 94, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#f43f5e',
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: isDark ? '#18181b' : '#ffffff',
                    titleColor: isDark ? '#ffffff' : '#0f172a',
                    bodyColor: isDark ? '#a1a1aa' : '#64748b',
                    borderColor: gridColor,
                    borderWidth: 1,
                    padding: 12
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: gridColor },
                    ticks: { precision: 0 }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });
});
</script>
@endpush
