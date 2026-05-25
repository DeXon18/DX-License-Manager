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
            <div class="dx-v2-sys-dash-stat-card-title">TOTAL TOKENS (MES)</div>
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
            <div class="dx-v2-sys-dash-stat-card-title">COSTE ESTIMADO (MES)</div>
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
            <div class="dx-v2-sys-dash-stat-card-title">TOTAL PETICIONES (MES)</div>
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
                        <span class="card-title">Distribución por Proveedor (Mes)</span>
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
                                    <span class="dx-v2-sys-dash-sec-label text-muted">Sin datos este mes</span>
                                    <span class="dx-v2-sys-dash-sec-value">-</span>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Uso por Acción --}}
                <div class="card">
                    <div class="card-header">
                        <span class="card-title">Distribución por Acción (Mes)</span>
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
                                    <span class="dx-v2-sys-dash-sec-label text-muted">Sin datos este mes</span>
                                    <span class="dx-v2-sys-dash-sec-value">-</span>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Uso por Usuario --}}
                <div class="card">
                    <div class="card-header">
                        <span class="card-title">Distribución por Usuario (Mes)</span>
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
                                    <span class="dx-v2-sys-dash-sec-label text-muted">Sin datos este mes</span>
                                    <span class="dx-v2-sys-dash-sec-value">-</span>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            {{-- Gráficas de Consumo Diario (Hoy) --}}
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-top: 1.5rem;">
                <div class="card">
                    <div class="card-header">
                        <span class="card-title">Tendencia por Proveedor (Día)</span>
                    </div>
                    <div class="card-body">
                        <canvas id="hourlyTokensChart" height="120"></canvas>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <span class="card-title">Tendencia por Usuario (Día)</span>
                    </div>
                    <div class="card-body">
                        <canvas id="hourlyUserTokensChart" height="120"></canvas>
                    </div>
                </div>
            </div>

            {{-- Gráficas de Consumo Mensual --}}
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-top: 1.5rem;">
                <div class="card">
                    <div class="card-header">
                        <span class="card-title">Tendencia por Proveedor (Mes)</span>
                    </div>
                    <div class="card-body">
                        <canvas id="dailyTokensChart" height="120"></canvas>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <span class="card-title">Tendencia por Usuario (Mes)</span>
                    </div>
                    <div class="card-body">
                        <canvas id="dailyUserTokensChart" height="120"></canvas>
                    </div>
                </div>
            </div>

            {{-- Historial Reciente --}}
            <div class="card" style="margin-top: 1.5rem;">
                <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                    <span class="card-title">Log de Peticiones</span>
                    <div style="font-size: 0.75rem; color: var(--dx-v2-muted); display: flex; gap: 12px; font-family: 'Outfit', sans-serif;">
                        <span><i class="fa-brands fa-google" style="color: #4285F4; margin-right: 4px;"></i> Gemini</span>
                        <span><i class="fa-solid fa-brain" style="color: #4A90E2; margin-right: 4px;"></i> DeepSeek</span>
                        <span><i class="fa-solid fa-minus text-muted" style="margin-right: 4px;"></i> N/A</span>
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
                                            if (str_contains($modelLower, 'gemini')) {
                                                $modelIcon = 'fa-brands fa-google';
                                                $modelColor = 'color: #4285F4;';
                                            } elseif (str_contains($modelLower, 'deepseek')) {
                                                $modelIcon = 'fa-solid fa-brain';
                                                $modelColor = 'color: #4A90E2;';
                                            } elseif ($modelLower !== '') {
                                                $modelIcon = 'fa-solid fa-microchip text-muted';
                                            }
                                        @endphp
                                        <i class="{{ $modelIcon }}" style="font-size: 1rem; {{ $modelColor }}" title="{{ $log->model ?? 'N/A' }}"></i>
                                    </td>
                                    <td class="dx-v2-audit-table-td text-right">{{ number_format($log->prompt_tokens, 0, ',', '.') }}</td>
                                    <td class="dx-v2-audit-table-td text-right">{{ number_format($log->completion_tokens, 0, ',', '.') }}</td>
                                    <td class="dx-v2-audit-table-td text-right accent-color" style="font-weight:600;">{{ number_format($log->total_tokens, 0, ',', '.') }}</td>
                                    <td class="dx-v2-audit-table-td text-right text-success">${{ number_format($log->estimated_cost, 6, ',', '.') }}</td>
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
    // 3. Gráfica Horaria por Proveedor
    const hourlyChartData = @json($hourlyChartData);
    if (hourlyChartData && hourlyChartData.hours.length > 0) {
        const ctxHourly = document.getElementById('hourlyTokensChart').getContext('2d');
        const colors = {
            'gemini': '#8e44ad',
            'deepseek': '#4a90e2',
            'openrouter': '#f39c12',
            'n8n': '#2ecc71',
            'default': '#e74c3c'
        };

        const hourlyDatasets = hourlyChartData.providers.map(provider => {
            const color = colors[provider] || colors['default'];
            const data = hourlyChartData.hours.map(hour => hourlyChartData.stats[hour][provider] || 0);

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

        new Chart(ctxHourly, {
            type: 'line',
            data: { labels: hourlyChartData.hours, datasets: hourlyDatasets },
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

    // 4. Gráfica Horaria por Usuario
    const hourlyUserChartData = @json($hourlyUserChartData);
    if (hourlyUserChartData && hourlyUserChartData.hours.length > 0) {
        const ctxUserHourly = document.getElementById('hourlyUserTokensChart').getContext('2d');
        const userColors = ['#1abc9c', '#9b59b6', '#34495e', '#f1c40f', '#e67e22', '#e74c3c', '#95a5a6'];

        const hourlyUserDatasets = hourlyUserChartData.users.map((user, index) => {
            const color = userColors[index % userColors.length];
            const data = hourlyUserChartData.hours.map(hour => hourlyUserChartData.stats[hour][user] || 0);

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

        new Chart(ctxUserHourly, {
            type: 'line',
            data: { labels: hourlyUserChartData.hours, datasets: hourlyUserDatasets },
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
