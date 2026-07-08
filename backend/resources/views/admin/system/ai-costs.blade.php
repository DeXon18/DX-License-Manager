@extends('layouts.app')

@section('title', 'Costes y Telemetría de IA')

@section('content')
<div class="dx-v2-page-header">
    <div>
        <div class="breadcrumb">
            <a href="{{ route('admin.system.index') }}">Infraestructura</a>
            <span class="separator">/</span>
            <span class="current">IA Telemetría</span>
        </div>
        <h1 class="page-title">Costes y Telemetría de <span>IA</span></h1>
        <p class="page-subtitle">Monitorización de uso, tokens y estimación de costes de Inteligencia Artificial.</p>
    </div>
    <div class="dx-v2-page-header-actions">
        <div class="dx-v2-sys-dash-header-meta-layout" style="display: flex; gap: 12px; font-size: 12px; font-family: var(--font-mono, monospace);">
            <div class="dx-v2-sys-dash-header-meta-item" style="display: flex; align-items: center; gap: 6px;">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="accent-color"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
                <span class="accent-color" style="opacity: 0.8;">Auditoría IA Local</span>
            </div>
        </div>
    </div>
</div>

<!-- Extra Fonts for Admin NOC -->
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@600;700&display=swap" rel="stylesheet">

<div class="dashboard-container">
    
    <div class="dx-v2-sys-dash-stats-grid">
        {{-- Total Tokens (Month) --}}
        <div class="dx-v2-sys-dash-stat-card" style="height: 100%; display: flex; flex-direction: column;">
            <div class="dx-v2-sys-dash-stat-card-watermark">
                <svg width="84" height="84" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><path d="M12 8v4l3 3"/></svg>
            </div>
            <div class="dx-v2-sys-dash-stat-card-title">TOTAL TOKENS (30 DÍAS)</div>
            <div class="dx-v2-sys-dash-stat-card-value accent-color">
                {{ number_format($totalTokensThisMonth, 0, ',', '.') }}
            </div>
            <div class="dx-v2-sys-dash-stat-card-meta-mono" style="margin-top: auto; min-height: 44px; display: flex; align-items: center;">
                <div style="width: 100%;">Prmpt: {{ number_format($promptTokensThisMonth, 0, ',', '.') }} <span class="dx-v2-sys-dash-dot-separator">·</span> Cmpl: {{ number_format($completionTokensThisMonth, 0, ',', '.') }}</div>
            </div>
        </div>

        {{-- Coste Total (Mes) --}}
        <div class="dx-v2-sys-dash-stat-card" style="height: 100%; display: flex; flex-direction: column;">
            <div class="dx-v2-sys-dash-stat-card-watermark">
                <svg width="84" height="84" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
            </div>
            <div class="dx-v2-sys-dash-stat-card-title">COSTE ESTIMADO (30 DÍAS)</div>
            <div class="dx-v2-sys-dash-stat-card-value success-color">
                ${{ number_format($totalCostThisMonth, 4, ',', '.') }}
            </div>
            <div class="dx-v2-sys-dash-stat-card-meta-mono" style="margin-top: auto; min-height: 44px; display: flex; align-items: center;">
                <div style="width: 100%;">Facturación basada en tokens consumidos</div>
            </div>
        </div>

        {{-- Coste Histórico --}}
        <div class="dx-v2-sys-dash-stat-card" style="height: 100%; display: flex; flex-direction: column;">
            <div class="dx-v2-sys-dash-stat-card-watermark">
                <svg width="84" height="84" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 3v18h18"/><path d="m19 9-5 5-4-4-3 3"/></svg>
            </div>
            <div class="dx-v2-sys-dash-stat-card-title">COSTE HISTÓRICO</div>
            <div class="dx-v2-sys-dash-stat-card-value success-color" style="opacity: 0.8;">
                ${{ number_format($totalCostAllTime, 4, ',', '.') }}
            </div>
            <div class="dx-v2-sys-dash-stat-card-meta-mono" style="margin-top: auto; min-height: 44px; display: flex; align-items: center;">
                <div style="width: 100%;">Acumulado desde inicio del sistema</div>
            </div>
        </div>
        {{-- Total Peticiones (Mes) --}}
        <div class="dx-v2-sys-dash-stat-card" style="height: 100%; display: flex; flex-direction: column;">
            <div class="dx-v2-sys-dash-stat-card-watermark">
                <svg width="84" height="84" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
            </div>
            <div class="dx-v2-sys-dash-stat-card-title">TOTAL PETICIONES (30 DÍAS)</div>
            <div class="dx-v2-sys-dash-stat-card-value accent-color" style="color: var(--dx-v2-accent);">
                {{ number_format($providerStats->sum('requests_count'), 0, ',', '.') }}
            </div>
            <div class="dx-v2-sys-dash-stat-card-meta-mono" style="margin-top: auto; min-height: 44px; display: flex; align-items: center;">
                <div style="width: 100%;">Llamadas a las APIs de IA</div>
            </div>
        </div>
    </div>

    <div style="margin-top: 1.5rem;">
        <div class="dx-v2-sys-dash-main-col">

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1.5rem;">
                @php
                    if (!function_exists('compact_number')) {
                        function compact_number($num) {
                            if ($num >= 1000000) return round($num / 1000000, 1) . 'M';
                            if ($num >= 1000) return round($num / 1000, 1) . 'k';
                            return number_format($num, 0, ',', '.');
                        }
                    }
                @endphp
                {{-- Uso por Proveedor --}}
                <div class="card">
                    <div class="card-header">
                        <span class="card-title">Distribución por Proveedor (30 Días)</span>
                    </div>
                    <div class="dx-v2-sys-dash-sec-box">
                        <div class="dx-v2-sys-dash-sec-layout">
                            @forelse($providerStats as $stat)
                                <div class="dx-v2-sys-dash-sec-row {{ $loop->last ? 'no-border' : '' }}">
                                    <span class="dx-v2-sys-dash-sec-label" style="text-transform: capitalize;">{{ $stat->provider }}</span>
                                    <span class="dx-v2-sys-dash-sec-value">
                                        {{ compact_number($stat->total_tokens) }} <span style="font-size: 0.65rem; color: var(--dx-v2-muted); font-weight: normal; font-family: 'Outfit', sans-serif;">TK</span>
                                        <span style="color: var(--dx-v2-border-base); margin: 0 4px; font-weight: normal;">|</span>
                                        {{ compact_number($stat->requests_count) }} <span style="font-size: 0.65rem; color: var(--dx-v2-muted); font-weight: normal; font-family: 'Outfit', sans-serif;">RQ</span>
                                    </span>
                                </div>
                            @empty
                                <div class="dx-v2-sys-dash-sec-row no-border">
                                    <span class="dx-v2-sys-dash-sec-label text-muted">Sin datos en 30 días</span>
                                    <span class="dx-v2-sys-dash-sec-value">-</span>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Uso por Acción --}}
                <div class="card">
                    <div class="card-header">
                        <span class="card-title">Distribución por Acción (30 Días)</span>
                    </div>
                    <div class="dx-v2-sys-dash-sec-box">
                        <div class="dx-v2-sys-dash-sec-layout">
                            @php
                                $actionNames = [
                                    'normalization_search' => 'Norm. Búsqueda',
                                    'normalization_pair'   => 'Norm. Duplicados',
                                    'license_audit'        => 'Auditoría .lic',
                                    'composite_parse'      => 'Análisis COD',
                                    'chatbot_query'        => 'Chatbot Asistente',
                                    'cost_calculation'     => 'Cálculo Costes',
                                    'cod_processor'        => 'Procesador Antiguo',
                                ];
                            @endphp
                            @forelse($actionStats as $stat)
                                <div class="dx-v2-sys-dash-sec-row {{ $loop->last ? 'no-border' : '' }}">
                                    <span class="dx-v2-sys-dash-sec-label">
                                        {{ $actionNames[$stat->action] ?? str_replace('_', ' ', Str::title($stat->action)) }}
                                    </span>
                                    <span class="dx-v2-sys-dash-sec-value">
                                        {{ compact_number($stat->total_tokens) }} <span style="font-size: 0.65rem; color: var(--dx-v2-muted); font-weight: normal; font-family: 'Outfit', sans-serif;">TK</span>
                                        <span style="color: var(--dx-v2-border-base); margin: 0 4px; font-weight: normal;">|</span>
                                        {{ compact_number($stat->requests_count) }} <span style="font-size: 0.65rem; color: var(--dx-v2-muted); font-weight: normal; font-family: 'Outfit', sans-serif;">RQ</span>
                                    </span>
                                </div>
                            @empty
                                <div class="dx-v2-sys-dash-sec-row no-border">
                                    <span class="dx-v2-sys-dash-sec-label text-muted">Sin datos en 30 días</span>
                                    <span class="dx-v2-sys-dash-sec-value">-</span>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Uso por Usuario --}}
                <div class="card">
                    <div class="card-header">
                        <span class="card-title">Distribución por Usuario (30 Días)</span>
                    </div>
                    <div class="dx-v2-sys-dash-sec-box">
                        <div class="dx-v2-sys-dash-sec-layout">
                            @forelse($userStats as $stat)
                                <div class="dx-v2-sys-dash-sec-row {{ $loop->last ? 'no-border' : '' }}">
                                    <span class="dx-v2-sys-dash-sec-label">{{ $stat->user->name ?? 'Sistema' }}</span>
                                    <span class="dx-v2-sys-dash-sec-value">
                                        {{ compact_number($stat->total_tokens) }} <span style="font-size: 0.65rem; color: var(--dx-v2-muted); font-weight: normal; font-family: 'Outfit', sans-serif;">TK</span>
                                        <span style="color: var(--dx-v2-border-base); margin: 0 4px; font-weight: normal;">|</span>
                                        {{ compact_number($stat->requests_count) }} <span style="font-size: 0.65rem; color: var(--dx-v2-muted); font-weight: normal; font-family: 'Outfit', sans-serif;">RQ</span>
                                    </span>
                                </div>
                            @empty
                                <div class="dx-v2-sys-dash-sec-row no-border">
                                    <span class="dx-v2-sys-dash-sec-label text-muted">Sin datos en 30 días</span>
                                    <span class="dx-v2-sys-dash-sec-value">-</span>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            {{-- Gráficas de Consumo (Últimos 7 días) --}}
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-top: 1.5rem;">
                <div class="card">
                    <div class="card-header">
                        <span class="card-title">Tendencia por Proveedor (Semana)</span>
                    </div>
                    <div class="card-body">
                        <canvas id="weeklyTokensChart" height="120"></canvas>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <span class="card-title">Tendencia por Usuario (Semana)</span>
                    </div>
                    <div class="card-body">
                        <canvas id="weeklyUserTokensChart" height="120"></canvas>
                    </div>
                </div>
            </div>

            {{-- Gráficas de Consumo Mensual --}}
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-top: 1.5rem;">
                <div class="card">
                    <div class="card-header">
                        <span class="card-title">Tendencia por Proveedor (30 Días)</span>
                    </div>
                    <div class="card-body">
                        <canvas id="dailyTokensChart" height="120"></canvas>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <span class="card-title">Tendencia por Usuario (30 Días)</span>
                    </div>
                    <div class="card-body">
                        <canvas id="dailyUserTokensChart" height="120"></canvas>
                    </div>
                </div>
            </div>

            {{-- Estadísticas de Fallos --}}
            <div class="card" style="margin-top: 1.5rem; border: 1px solid var(--dx-v2-danger-border);">
                <div class="card-header" style="background: rgba(220, 53, 69, 0.1);">
                    <span class="card-title" style="color: #e74c3c;"><i class="fa-solid fa-triangle-exclamation" style="margin-right: 8px;"></i>Telemetría de Fallos y Errores (30 Días)</span>
                </div>
                <div class="dx-v2-audit-table-wrapper">
                    <table class="dx-v2-audit-table">
                        <thead class="dx-v2-audit-table-thead">
                            <tr>
                                <th class="dx-v2-audit-table-th width-200">Modelo Afectado</th>
                                <th class="dx-v2-audit-table-th">Motivo o Mensaje de Error</th>
                                <th class="dx-v2-audit-table-th text-center width-100">Frecuencia</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($failureStats as $stat)
                                <tr class="dx-v2-audit-table-tr">
                                    <td class="dx-v2-audit-table-td" style="font-family: var(--font-mono, monospace); font-size: 0.85rem;">{{ $stat->model }}</td>
                                    <td class="dx-v2-audit-table-td" style="color: #e74c3c; font-size: 0.85rem;">{{ $stat->error_message }}</td>
                                    <td class="dx-v2-audit-table-td text-center" style="font-weight: 600;">
                                        <span class="badge" style="background: rgba(220, 53, 69, 0.1); color: #e74c3c;">{{ $stat->error_count }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="dx-v2-audit-table-td" style="padding: 20px; text-align: center; color: var(--dx-v2-muted);">
                                        <i class="fa-solid fa-check-circle" style="color: var(--dx-v2-success); margin-right: 6px;"></i> No se han registrado fallos en este periodo.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Historial Reciente --}}
            <div class="card" style="margin-top: 1.5rem;">
                <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                    <span class="card-title">Log de Peticiones</span>
                    <div style="font-size: 0.75rem; color: var(--dx-v2-muted); display: flex; gap: 12px; font-family: 'Outfit', sans-serif;">
                        <span><i class="fa-brands fa-google" style="color: #4285F4; margin-right: 4px;"></i> Google</span>
                        <span><i class="fa-brands fa-meta" style="color: #0668E1; margin-right: 4px;"></i> Meta</span>
                        <span><i class="fa-solid fa-brain" style="color: #4A90E2; margin-right: 4px;"></i> DeepSeek</span>
                        <span><i class="fa-solid fa-microchip text-muted" style="margin-right: 4px;"></i> Otros</span>
                    </div>
                </div>
                <div class="dx-v2-audit-table-wrapper">
                    <table class="dx-v2-audit-table">
                        <thead class="dx-v2-audit-table-thead">
                            <tr>
                                <th class="dx-v2-audit-table-th width-120">Fecha</th>
                                <th class="dx-v2-audit-table-th width-150">Acción</th>
                                <th class="dx-v2-audit-table-th width-130">Proveedor</th>
                                <th class="dx-v2-audit-table-th text-center width-60" title="Modelo IA">Mod.</th>
                                <th class="dx-v2-audit-table-th text-right width-100">Prompt</th>
                                <th class="dx-v2-audit-table-th text-right width-100">Completion</th>
                                <th class="dx-v2-audit-table-th text-right width-100">Total</th>
                                <th class="dx-v2-audit-table-th text-right width-120">Coste Est.</th>
                                <th class="dx-v2-audit-table-th width-130">Usuario</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($logs as $log)
                                <tr class="dx-v2-audit-table-tr">
                                    <td class="dx-v2-audit-table-td dx-v2-audit-td-timestamp">{{ $log->created_at->format('d M H:i:s') }}</td>
                                    <td class="dx-v2-audit-table-td">{{ $log->action }}</td>
                                    <td class="dx-v2-audit-table-td"><span class="badge dx-v2-audit-badge-level info">{{ strtoupper($log->provider) }}</span></td>
                                    <td class="dx-v2-audit-table-td text-center">
                                        @php
                                            $modelLower = strtolower($log->model ?? '');
                                            $modelIcon = 'fa-solid fa-minus text-muted';
                                            $modelColor = '';
                                            $isFree = true; // default assume free or calculate
                                            $matchedModel = null;

                                            // Attempt to match with DB models
                                            if (isset($modelsFromDb) && isset($modelsFromDb[$log->model])) {
                                                $matchedModel = $modelsFromDb[$log->model];
                                            } elseif (isset($modelsFromDb)) {
                                                $matchedModel = $modelsFromDb->first(function($dbModel) use ($log) {
                                                    $shortName = explode('/', $dbModel->openrouter_id)[1] ?? $dbModel->openrouter_id;
                                                    return str_contains($dbModel->openrouter_id, $log->model) || str_contains($log->model, $shortName);
                                                });
                                            }

                                            if ($matchedModel) {
                                                $isFree = $matchedModel->is_free;
                                                $calcCost = ($log->prompt_tokens / 1000000 * $matchedModel->price_prompt) + ($log->completion_tokens / 1000000 * $matchedModel->price_completion);
                                            } else {
                                                $calcCost = 0; // Legacy or unmatched
                                            }

                                            if (str_contains($modelLower, 'gemini') || str_contains($modelLower, 'gemma')) {
                                                $modelIcon = 'fa-brands fa-google';
                                                $modelColor = 'color: #4285F4;';
                                            } elseif (str_contains($modelLower, 'llama')) {
                                                $modelIcon = 'fa-brands fa-meta';
                                                $modelColor = 'color: #0668E1;';
                                            } elseif (str_contains($modelLower, 'deepseek')) {
                                                $modelIcon = 'fa-solid fa-brain';
                                                $modelColor = 'color: #4A90E2;';
                                            } elseif ($modelLower !== '') {
                                                $modelIcon = 'fa-solid fa-microchip text-muted';
                                            }
                                        @endphp
                                        <div style="display: flex; align-items: center; justify-content: center; gap: 6px;">
                                            <i class="{{ $modelIcon }}" style="font-size: 1rem; {{ $modelColor }}" title="{{ $log->model ?? 'N/A' }}"></i>
                                            @if($matchedModel)
                                                @if($isFree)
                                                    <span style="background: var(--dx-v2-success-bg); color: var(--dx-v2-success); border: 1px solid var(--dx-v2-success-border); padding: 1px 4px; border-radius: 4px; font-size: 9px; font-weight: 700;">FREE</span>
                                                @else
                                                    <span style="background: var(--dx-v2-warning-bg); color: var(--dx-v2-warning); border: 1px solid var(--dx-v2-warning-border); padding: 1px 4px; border-radius: 4px; font-size: 9px; font-weight: 700;">PRO</span>
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                    <td class="dx-v2-audit-table-td text-right">{{ number_format($log->prompt_tokens, 0, ',', '.') }}</td>
                                    <td class="dx-v2-audit-table-td text-right">{{ number_format($log->completion_tokens, 0, ',', '.') }}</td>
                                    <td class="dx-v2-audit-table-td text-right accent-color" style="font-weight:600;">{{ number_format($log->total_tokens, 0, ',', '.') }}</td>
                                    <td class="dx-v2-audit-table-td text-right text-success">${{ number_format($calcCost, 6, ',', '.') }}</td>
                                    <td class="dx-v2-audit-table-td">
                                        <div class="dx-v2-audit-user-badge" style="justify-content: flex-start;">
                                            <span class="dx-v2-audit-user-name">{{ $log->user->name ?? 'Sistema' }}</span>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="dx-v2-audit-table-td" style="padding: 40px; text-align: center; color: var(--muted);">No hay registros de IA disponibles.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($logs->hasPages())
                    <div class="card-body border-top" style="padding-top: 1rem; padding-bottom: 1rem;">
                        {{ $logs->links('vendor.pagination.dx-jump') }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Gráfica por Proveedor
    const chartData = @json($chartData);
    if (chartData && chartData.dates.length > 0) {
        const ctx = document.getElementById('dailyTokensChart').getContext('2d');
        const colors = {
            'gemini': '#8e44ad',
            'deepseek': '#4a90e2',
            'openrouter': '#f39c12',
            'n8n': '#2ecc71',
            'default': '#e74c3c'
        };

        const datasets = chartData.providers.map(provider => {
            const color = colors[provider] || colors['default'];
            const data = chartData.dates.map(date => chartData.stats[date][provider] || 0);

            return {
                label: provider.toUpperCase(),
                data: data,
                borderColor: color,
                backgroundColor: color + '22',
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
            data: { labels: chartData.dates, datasets: datasets },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: true, labels: { color: '#8a94a6', font: { family: 'Outfit', size: 12 }, usePointStyle: true } },
                    tooltip: { backgroundColor: '#1c1e26', titleColor: '#8a94a6', bodyColor: '#ffffff', borderColor: '#2b2e38', borderWidth: 1, padding: 10, mode: 'index', intersect: false }
                },
                interaction: { mode: 'nearest', axis: 'x', intersect: false },
                scales: {
                    x: { grid: { display: false, drawBorder: false }, ticks: { color: '#8a94a6', font: { family: 'Outfit', size: 11 } } },
                    y: { grid: { color: '#2b2e38', drawBorder: false }, ticks: { color: '#8a94a6', font: { family: 'Outfit', size: 11 }, maxTicksLimit: 5 } }
                }
            }
        });
    }

    // 2. Gráfica por Usuario
    const userChartData = @json($userChartData);
    if (userChartData && userChartData.dates.length > 0) {
        const ctxUser = document.getElementById('dailyUserTokensChart').getContext('2d');
        const userColors = ['#1abc9c', '#9b59b6', '#34495e', '#f1c40f', '#e67e22', '#e74c3c', '#95a5a6'];

        const userDatasets = userChartData.users.map((user, index) => {
            const color = userColors[index % userColors.length];
            const data = userChartData.dates.map(date => userChartData.stats[date][user] || 0);

            return {
                label: user,
                data: data,
                borderColor: color,
                backgroundColor: color + '22',
                borderWidth: 2,
                pointBackgroundColor: '#1c1e26',
                pointBorderColor: color,
                pointBorderWidth: 2,
                pointRadius: 4,
                fill: true,
                tension: 0.4
            };
        });

        new Chart(ctxUser, {
            type: 'line',
            data: { labels: userChartData.dates, datasets: userDatasets },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: true, labels: { color: '#8a94a6', font: { family: 'Outfit', size: 12 }, usePointStyle: true } },
                    tooltip: { backgroundColor: '#1c1e26', titleColor: '#8a94a6', bodyColor: '#ffffff', borderColor: '#2b2e38', borderWidth: 1, padding: 10, mode: 'index', intersect: false }
                },
                interaction: { mode: 'nearest', axis: 'x', intersect: false },
                scales: {
                    x: { grid: { display: false, drawBorder: false }, ticks: { color: '#8a94a6', font: { family: 'Outfit', size: 11 } } },
                    y: { grid: { color: '#2b2e38', drawBorder: false }, ticks: { color: '#8a94a6', font: { family: 'Outfit', size: 11 }, maxTicksLimit: 5 } }
                }
            }
        });
    }
    // 3. Gráfica por Proveedor (Últimos 7 días)
    const weeklyChartData = @json($weeklyChartData);
    if (weeklyChartData && weeklyChartData.dates.length > 0) {
        const ctxWeekly = document.getElementById('weeklyTokensChart').getContext('2d');
        const colors = {
            'gemini': '#8e44ad',
            'deepseek': '#4a90e2',
            'openrouter': '#f39c12',
            'n8n': '#2ecc71',
            'default': '#e74c3c'
        };

        const weeklyDatasets = weeklyChartData.providers.map(provider => {
            const color = colors[provider] || colors['default'];
            const data = weeklyChartData.dates.map(date => weeklyChartData.stats[date][provider] || 0);

            return {
                label: provider.toUpperCase(),
                data: data,
                borderColor: color,
                backgroundColor: color + '22',
                borderWidth: 2,
                pointBackgroundColor: '#1c1e26',
                pointBorderColor: color,
                pointBorderWidth: 2,
                pointRadius: 4,
                fill: true,
                tension: 0.4
            };
        });

        new Chart(ctxWeekly, {
            type: 'line',
            data: { labels: weeklyChartData.dates, datasets: weeklyDatasets },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: true, labels: { color: '#8a94a6', font: { family: 'Outfit', size: 12 }, usePointStyle: true } },
                    tooltip: { backgroundColor: '#1c1e26', titleColor: '#8a94a6', bodyColor: '#ffffff', borderColor: '#2b2e38', borderWidth: 1, padding: 10, mode: 'index', intersect: false }
                },
                interaction: { mode: 'nearest', axis: 'x', intersect: false },
                scales: {
                    x: { grid: { display: false, drawBorder: false }, ticks: { color: '#8a94a6', font: { family: 'Outfit', size: 11 } } },
                    y: { grid: { color: '#2b2e38', drawBorder: false }, ticks: { color: '#8a94a6', font: { family: 'Outfit', size: 11 }, maxTicksLimit: 5 } }
                }
            }
        });
    }

    // 4. Gráfica por Usuario (Últimos 7 días)
    const weeklyUserChartData = @json($weeklyUserChartData);
    if (weeklyUserChartData && weeklyUserChartData.dates.length > 0) {
        const ctxUserWeekly = document.getElementById('weeklyUserTokensChart').getContext('2d');
        const userColors = ['#1abc9c', '#9b59b6', '#34495e', '#f1c40f', '#e67e22', '#e74c3c', '#95a5a6'];

        const weeklyUserDatasets = weeklyUserChartData.users.map((user, index) => {
            const color = userColors[index % userColors.length];
            const data = weeklyUserChartData.dates.map(date => weeklyUserChartData.stats[date][user] || 0);

            return {
                label: user,
                data: data,
                borderColor: color,
                backgroundColor: color + '22',
                borderWidth: 2,
                pointBackgroundColor: '#1c1e26',
                pointBorderColor: color,
                pointBorderWidth: 2,
                pointRadius: 4,
                fill: true,
                tension: 0.4
            };
        });

        new Chart(ctxUserWeekly, {
            type: 'line',
            data: { labels: weeklyUserChartData.dates, datasets: weeklyUserDatasets },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: true, labels: { color: '#8a94a6', font: { family: 'Outfit', size: 12 }, usePointStyle: true } },
                    tooltip: { backgroundColor: '#1c1e26', titleColor: '#8a94a6', bodyColor: '#ffffff', borderColor: '#2b2e38', borderWidth: 1, padding: 10, mode: 'index', intersect: false }
                },
                interaction: { mode: 'nearest', axis: 'x', intersect: false },
                scales: {
                    x: { grid: { display: false, drawBorder: false }, ticks: { color: '#8a94a6', font: { family: 'Outfit', size: 11 } } },
                    y: { grid: { color: '#2b2e38', drawBorder: false }, ticks: { color: '#8a94a6', font: { family: 'Outfit', size: 11 }, maxTicksLimit: 5 } }
                }
            }
        });
    }
});
</script>
@endsection
