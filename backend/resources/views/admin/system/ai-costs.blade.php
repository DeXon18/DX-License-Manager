@extends('layouts.app')

@section('title', 'Costes y Telemetría de IA')

@section('content')
<div class="page-header">
    <div class="dx-v2-sys-dash-header-meta">
        <div class="dx-v2-sys-dash-header-meta-layout">
            <div class="dx-v2-sys-dash-header-meta-item">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="accent-color"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
                <span class="accent-color">Auditoría IA Local</span>
            </div>
        </div>
    </div>
</div>

<!-- Extra Fonts for Admin NOC -->
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@600;700&display=swap" rel="stylesheet">

<div class="dashboard-container">
    
    <div class="dx-v2-sys-dash-stats-grid">
        {{-- Total Tokens (Month) --}}
        <div class="dx-v2-sys-dash-stat-card">
            <div class="dx-v2-sys-dash-stat-card-watermark">
                <svg width="84" height="84" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><path d="M12 8v4l3 3"/></svg>
            </div>
            <div class="dx-v2-sys-dash-stat-card-title">TOTAL TOKENS (MES)</div>
            <div class="dx-v2-sys-dash-stat-card-value accent-color">
                {{ number_format($totalTokensThisMonth, 0, ',', '.') }}
            </div>
            <div class="dx-v2-sys-dash-stat-card-meta-mono">
                Prmpt: {{ number_format($promptTokensThisMonth, 0, ',', '.') }} <span class="dx-v2-sys-dash-dot-separator">·</span> Cmpl: {{ number_format($completionTokensThisMonth, 0, ',', '.') }}
            </div>
        </div>

        {{-- Coste Total (Mes) --}}
        <div class="dx-v2-sys-dash-stat-card">
            <div class="dx-v2-sys-dash-stat-card-watermark">
                <svg width="84" height="84" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
            </div>
            <div class="dx-v2-sys-dash-stat-card-title">COSTE ESTIMADO (MES)</div>
            <div class="dx-v2-sys-dash-stat-card-value success-color">
                ${{ number_format($totalCostThisMonth, 4, ',', '.') }}
            </div>
            <div class="dx-v2-sys-dash-stat-card-meta-mono">
                Facturación basada en tokens consumidos
            </div>
        </div>

        {{-- Coste Histórico --}}
        <div class="dx-v2-sys-dash-stat-card">
            <div class="dx-v2-sys-dash-stat-card-watermark">
                <svg width="84" height="84" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 3v18h18"/><path d="m19 9-5 5-4-4-3 3"/></svg>
            </div>
            <div class="dx-v2-sys-dash-stat-card-title">COSTE HISTÓRICO</div>
            <div class="dx-v2-sys-dash-stat-card-value success-color" style="opacity: 0.8;">
                ${{ number_format($totalCostAllTime, 4, ',', '.') }}
            </div>
            <div class="dx-v2-sys-dash-stat-card-meta-mono">
                Acumulado desde inicio del sistema
            </div>
        </div>
        {{-- Total Peticiones (Mes) --}}
        <div class="dx-v2-sys-dash-stat-card">
            <div class="dx-v2-sys-dash-stat-card-watermark">
                <svg width="84" height="84" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
            </div>
            <div class="dx-v2-sys-dash-stat-card-title">TOTAL PETICIONES (MES)</div>
            <div class="dx-v2-sys-dash-stat-card-value accent-color" style="color: var(--dx-v2-accent);">
                {{ number_format($providerStats->sum('requests_count'), 0, ',', '.') }}
            </div>
            <div class="dx-v2-sys-dash-stat-card-meta-mono">
                Llamadas a las APIs de IA
            </div>
        </div>
    </div>

    <div style="margin-top: 1.5rem;">
        <div class="dx-v2-sys-dash-main-col">

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                {{-- Uso por Proveedor --}}
                <div class="card">
                    <div class="card-header">
                        <span class="card-title">Distribución por Proveedor (Mes)</span>
                    </div>
                    <div class="card-body">
                        <div class="dx-v2-sys-dash-sec-box" style="margin:0; border:none; padding:0;">
                            <div class="dx-v2-sys-dash-sec-layout">
                                @forelse($providerStats as $stat)
                                    <div class="dx-v2-sys-dash-sec-row {{ $loop->last ? 'no-border' : '' }}">
                                        <span class="dx-v2-sys-dash-sec-label" style="text-transform: capitalize; font-weight: 600; color: var(--dx-v2-primary-base);">{{ $stat->provider }}</span>
                                        <div style="display: flex; align-items: center; gap: 16px; text-align: right;">
                                            <div style="display: flex; flex-direction: column; align-items: flex-end;">
                                                <span class="dx-v2-sys-dash-sec-value" style="font-size: 1.1rem; line-height: 1;">{{ number_format($stat->total_tokens, 0, ',', '.') }}</span>
                                                <span style="font-size: 0.65rem; color: var(--dx-v2-muted); text-transform: uppercase; letter-spacing: 0.05em; font-weight: 700; margin-top: 4px;">Tokens</span>
                                            </div>
                                            <div style="width: 1px; height: 28px; background: var(--dx-v2-border-base); opacity: 0.6;"></div>
                                            <div style="display: flex; flex-direction: column; align-items: flex-end; min-width: 48px;">
                                                <span class="dx-v2-sys-dash-sec-value" style="font-size: 1.1rem; line-height: 1; color: var(--dx-v2-text-secondary);">{{ $stat->requests_count }}</span>
                                                <span style="font-size: 0.65rem; color: var(--dx-v2-muted); text-transform: uppercase; letter-spacing: 0.05em; font-weight: 700; margin-top: 4px;">Reqs</span>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="dx-v2-sys-dash-sec-row no-border">
                                        <span class="dx-v2-sys-dash-sec-label" style="color: var(--dx-v2-text-muted);">Sin datos</span>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Uso por Acción --}}
                <div class="card">
                    <div class="card-header">
                        <span class="card-title">Distribución por Acción (Mes)</span>
                    </div>
                    <div class="card-body">
                        <div class="dx-v2-sys-dash-sec-box" style="margin:0; border:none; padding:0;">
                            <div class="dx-v2-sys-dash-sec-layout">
                                @php
                                    $actionNames = [
                                        'normalization_search' => 'Normalización (Búsqueda)',
                                        'normalization_pair'   => 'Normalización (Escáner de Duplicados)',
                                        'license_audit'        => 'Auditoría de Licencias (.lic)',
                                        'composite_parse'      => 'Análisis Composite.txt (COD)',
                                        'chatbot_query'        => 'Chatbot Asistente',
                                        'cost_calculation'     => 'Cálculo de Costes',
                                        'cod_processor'        => 'Procesador COD (Antiguo)',
                                    ];
                                @endphp
                                @forelse($actionStats as $stat)
                                    <div class="dx-v2-sys-dash-sec-row {{ $loop->last ? 'no-border' : '' }}" style="align-items: center;">
                                        <div style="display: flex; flex-direction: column;">
                                            <span class="dx-v2-sys-dash-sec-label" style="font-weight: 600; color: var(--dx-v2-primary-base);">
                                                {{ $actionNames[$stat->action] ?? str_replace('_', ' ', Str::title($stat->action)) }}
                                            </span>
                                            <span style="font-size: 0.72rem; font-family: var(--font-mono); color: var(--dx-v2-muted); margin-top: 4px;">
                                                ~{{ number_format(round($stat->total_tokens / max(1, $stat->requests_count)), 0, ',', '.') }} tk/req
                                            </span>
                                        </div>
                                        <div style="display: flex; align-items: center; gap: 16px; text-align: right;">
                                            <div style="display: flex; flex-direction: column; align-items: flex-end;">
                                                <span class="dx-v2-sys-dash-sec-value" style="font-size: 1.1rem; line-height: 1;">{{ number_format($stat->total_tokens, 0, ',', '.') }}</span>
                                                <span style="font-size: 0.65rem; color: var(--dx-v2-muted); text-transform: uppercase; letter-spacing: 0.05em; font-weight: 700; margin-top: 4px;">Tokens</span>
                                            </div>
                                            <div style="width: 1px; height: 28px; background: var(--dx-v2-border-base); opacity: 0.6;"></div>
                                            <div style="display: flex; flex-direction: column; align-items: flex-end; min-width: 48px;">
                                                <span class="dx-v2-sys-dash-sec-value" style="font-size: 1.1rem; line-height: 1; color: var(--dx-v2-text-secondary);">{{ $stat->requests_count }}</span>
                                                <span style="font-size: 0.65rem; color: var(--dx-v2-muted); text-transform: uppercase; letter-spacing: 0.05em; font-weight: 700; margin-top: 4px;">Reqs</span>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="dx-v2-sys-dash-sec-row no-border">
                                        <span class="dx-v2-sys-dash-sec-label text-muted">Sin datos este mes</span>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Gráfica de Consumo Diario --}}
            <div class="card" style="margin-top: 1.5rem;">
                <div class="card-header">
                    <span class="card-title">Consumo de Tokens (Mes Actual)</span>
                </div>
                <div class="card-body">
                    <canvas id="dailyTokensChart" height="80"></canvas>
                </div>
            </div>

            {{-- Historial Reciente --}}
            <div class="card" style="margin-top: 1.5rem;">
                <div class="card-header">
                    <span class="card-title">Log de Peticiones</span>
                </div>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>FECHA</th>
                                <th>ACCIÓN</th>
                                <th>PROVEEDOR</th>
                                <th class="text-right">PROMPT</th>
                                <th class="text-right">COMPLETION</th>
                                <th class="text-right">TOTAL</th>
                                <th class="text-right">COSTE EST.</th>
                                <th>USUARIO</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($logs as $log)
                                <tr>
                                    <td><span class="dx-v2-sys-dash-sec-footer-code">{{ $log->created_at->format('d M H:i:s') }}</span></td>
                                    <td>{{ $log->action }}</td>
                                    <td><span class="badge" style="background: var(--dx-v2-glass-border); color: var(--dx-v2-text-primary);">{{ $log->provider }}</span></td>
                                    <td class="text-right">{{ number_format($log->prompt_tokens, 0, ',', '.') }}</td>
                                    <td class="text-right">{{ number_format($log->completion_tokens, 0, ',', '.') }}</td>
                                    <td class="text-right accent-color" style="font-family: 'Outfit', sans-serif; font-weight:600;">{{ number_format($log->total_tokens, 0, ',', '.') }}</td>
                                    <td class="text-right text-success" style="font-family: 'Outfit', sans-serif;">${{ number_format($log->estimated_cost, 6, ',', '.') }}</td>
                                    <td>{{ $log->user->name ?? 'Sistema' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted" style="padding: 2rem;">No hay registros de IA disponibles.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($logs->hasPages())
                    <div class="card-body border-top">
                        {{ $logs->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const chartData = @json($chartData);
    
    if (!chartData || chartData.dates.length === 0) return;

    const ctx = document.getElementById('dailyTokensChart').getContext('2d');
    
    const colors = {
        'gemini': '#8e44ad', // Morado
        'deepseek': '#4a90e2', // Azul eléctrico
        'openrouter': '#f39c12', // Naranja
        'n8n': '#2ecc71', // Verde
        'default': '#e74c3c'
    };

    const datasets = chartData.providers.map(provider => {
        const color = colors[provider] || colors['default'];
        
        const data = chartData.dates.map(date => {
            return chartData.stats[date][provider] || 0;
        });

        return {
            label: provider.toUpperCase(),
            data: data,
            borderColor: color,
            backgroundColor: color + '22', // Transparente
            borderWidth: 2,
            pointBackgroundColor: '#1c1e26',
            pointBorderColor: color,
            pointBorderWidth: 2,
            pointRadius: 4,
            fill: true,
            tension: 0.4
        };
    });

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartData.dates,
            datasets: datasets
        },
        options: {
            responsive: true,
            plugins: {
                legend: { 
                    display: true,
                    labels: { color: '#8a94a6', font: { family: 'Outfit', size: 12 }, usePointStyle: true }
                },
                tooltip: {
                    backgroundColor: '#1c1e26',
                    titleColor: '#8a94a6',
                    bodyColor: '#ffffff',
                    borderColor: '#2b2e38',
                    borderWidth: 1,
                    padding: 10,
                    mode: 'index',
                    intersect: false
                }
            },
            interaction: { mode: 'nearest', axis: 'x', intersect: false },
            scales: {
                x: {
                    grid: { display: false, drawBorder: false },
                    ticks: { color: '#8a94a6', font: { family: 'Outfit', size: 11 } },
                    stacked: false
                },
                y: {
                    grid: { color: '#2b2e38', drawBorder: false },
                    ticks: { color: '#8a94a6', font: { family: 'Outfit', size: 11 }, maxTicksLimit: 5 },
                    stacked: false
                }
            }
        }
    });
});
</script>
@endsection
