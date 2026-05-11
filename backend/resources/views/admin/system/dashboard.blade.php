@extends('layouts.app')

@section('title', 'System Control Center')

@section('header')
    <div class="page-header">
        <nav class="breadcrumb" aria-label="Breadcrumb">
            <a href="{{ route('dashboard') }}">Portal</a>
            <span>/</span>
            <span class="muted">Admin</span>
            <span>/</span>
            <span class="font-bold">System Dashboard</span>
        </nav>
        <h1 class="page-title flex items-center gap-3">
            System Control Center
            <span class="dot-live" title="Live System"></span>
        </h1>
        <p class="page-sub">Panel de monitorización técnica y KPIs de negocio en tiempo real.</p>
    </div>
@endsection

@section('content')
<div class="dashboard-container">
    {{-- KPI Row --}}
    <div class="stats-row">
        {{-- Health Score --}}
        <div class="stat-card">
            <div class="stat-label">System Health Score</div>
            <div class="stat-value accent">98.2<span style="font-size: 14px; opacity: 0.5">%</span></div>
            <div class="stat-meta success">OPERATIONAL</div>
            <div class="storage-track mt-4" style="margin-top: 12px;">
                <div class="storage-fill" style="width: 98.2%"></div>
            </div>
        </div>

        {{-- Total Licenses --}}
        <div class="stat-card">
            <div class="stat-label">Managed Licenses</div>
            <div class="stat-value font-mono">
                {{ $metrics['business']['total_contracts'] }}
            </div>
            <div class="stat-meta font-mono">
                {{ $metrics['business']['total_clients'] }} Clients · <span class="badge badge-warn">{{ $metrics['business']['expiring_soon'] }} Expiring</span>
            </div>
        </div>

        {{-- AI Audits --}}
        <div class="stat-card">
            <div class="stat-label">AI Audit Intelligence</div>
            <div class="stat-value font-mono">
                {{ $metrics['business']['total_audits'] }}
            </div>
            <div class="stat-meta">
                @if($metrics['business']['pending_audits'] > 0)
                    <span class="badge badge-info">{{ $metrics['business']['pending_audits'] }} Processing</span>
                @else
                    <span class="badge badge-success">Synchronized</span>
                @endif
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Global Load Avg</div>
            <div class="stat-value font-mono">
                {{ $metrics['os']['load'] }}
            </div>
            <div class="stat-meta font-mono muted">
                PHP {{ $metrics['os']['php_version'] }} · {{ $metrics['os']['name'] }}
            </div>
        </div>
    </div>

    {{-- Charts Row --}}
    <div class="grid-main" style="display: grid; grid-template-columns: 1fr 340px; gap: 24px; margin-top: 24px;">
        {{-- Trend Chart --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title">Audit Activity Trend</span>
                <span class="badge badge-muted font-mono" style="font-size: 9px;">LAST 7 DAYS</span>
            </div>
            <div style="padding: 24px; height: 300px;">
                <canvas id="trendChart"></canvas>
            </div>
        </div>

        {{-- Distribution Chart --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title">Vendor Daemons</span>
            </div>
            <div style="padding: 24px; height: 300px;">
                <canvas id="distributionChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Hardware & Services Grid --}}
    <div class="grid-main" style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-top: 24px;">
        {{-- Services Status --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title">Runtime Services</span>
            </div>
            <div style="padding: 20px;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                    @foreach($metrics['services'] as $name => $info)
                        <div style="padding: 12px; border-radius: 6px; background: var(--bg); border: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; position: relative; overflow: hidden;">
                            <div style="flex: 1;">
                                <div style="font-size: 10px; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: 0.05em;">{{ $name }}</div>
                                <div style="font-size: 13px; font-weight: 600; color: var(--primary);">{{ strtoupper($info['message']) }}</div>
                                @if(isset($info['details']))
                                    <div style="font-size: 10px; font-family: 'IBM Plex Mono', monospace; color: var(--muted); margin-top: 4px;">{{ $info['details'] }}</div>
                                @endif
                            </div>
                            <div class="dot {{ $info['status'] === 'online' ? 'online' : ($info['status'] === 'degraded' ? 'warn' : 'danger') }}" style="flex-shrink: 0; margin-left: 12px;"></div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Resource Monitoring --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title">Resource Utilization</span>
            </div>
            <div style="padding: 20px;">
                {{-- Memory --}}
                <div class="mb-4">
                    <div style="display: flex; justify-content: space-between; font-size: 11px; margin-bottom: 6px;">
                        <span class="muted font-bold uppercase tracking-widest">System Memory</span>
                        <span class="font-mono">{{ $metrics['hardware']['memory']['used'] }} / {{ $metrics['hardware']['memory']['total'] }}</span>
                    </div>
                    <div class="storage-track">
                        <div class="storage-fill" style="width: {{ $metrics['hardware']['memory']['percent'] }}%; background: {{ $metrics['hardware']['memory']['percent'] > 80 ? 'var(--danger)' : 'var(--accent)' }}"></div>
                    </div>
                </div>

                {{-- Disk --}}
                <div class="mt-6">
                    <div style="display: flex; justify-content: space-between; font-size: 11px; margin-bottom: 6px;">
                        <span class="muted font-bold uppercase tracking-widest">Storage Partition</span>
                        <span class="font-mono">{{ $metrics['hardware']['disk']['used'] }} / {{ $metrics['hardware']['disk']['total'] }}</span>
                    </div>
                    <div class="storage-track">
                        <div class="storage-fill" style="width: {{ $metrics['hardware']['disk']['percent'] }}%; background: {{ $metrics['hardware']['disk']['percent'] > 90 ? 'var(--danger)' : 'var(--success)' }}"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
        const gridColor = isDark ? 'rgba(255, 255, 255, 0.05)' : 'rgba(0, 0, 0, 0.05)';
        const textColor = isDark ? '#8B949E' : '#6B7280';

        // Colors
        const colors = {
            accent: '#388BFD',
            success: '#3FB950',
            warning: '#D29922',
            danger: '#E05252',
            siemens: '#2AA198',
            moldex: '#E05252'
        };

        // Trend Chart
        new Chart(document.getElementById('trendChart'), {
            type: 'line',
            data: {
                labels: {!! json_encode($metrics['trends']['labels']) !!},
                datasets: [{
                    label: 'Audits',
                    data: {!! json_encode($metrics['trends']['data']) !!},
                    borderColor: colors.accent,
                    backgroundColor: colors.accent + '15',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 2,
                    pointRadius: 4,
                    pointBackgroundColor: colors.accent
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { 
                        beginAtZero: true, 
                        grid: { color: gridColor },
                        ticks: { font: { family: 'IBM Plex Mono', size: 10 }, color: textColor }
                    },
                    x: { 
                        grid: { display: false },
                        ticks: { font: { family: 'IBM Plex Mono', size: 10 }, color: textColor }
                    }
                }
            }
        });

        // Distribution Chart
        new Chart(document.getElementById('distributionChart'), {
            type: 'bar',
            data: {
                labels: {!! json_encode($metrics['distribution']['labels']) !!},
                datasets: [{
                    data: {!! json_encode($metrics['distribution']['values']) !!},
                    backgroundColor: [
                        colors.siemens,
                        colors.moldex,
                        colors.accent,
                        colors.warning
                    ],
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y',
                plugins: { legend: { display: false } },
                scales: {
                    x: { 
                        beginAtZero: true,
                        grid: { color: gridColor },
                        ticks: { font: { family: 'IBM Plex Mono', size: 10 }, color: textColor }
                    },
                    y: { 
                        grid: { display: false },
                        ticks: { font: { family: 'IBM Plex Mono', size: 10 }, color: textColor }
                    }
                }
            }
        });
    });
</script>

<style>
    .dashboard-container { animation: fadeIn 0.4s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    
    /* Fallback styles if grid-main is not behaving */
    .dashboard-container .card { margin-bottom: 0; }
    .dot { width: 10px; height: 10px; border-radius: 50%; }
    .dot.online { background: var(--success); box-shadow: 0 0 8px var(--success); }
    .dot.warn { background: var(--warning); box-shadow: 0 0 8px var(--warning); }
    .dot.danger { background: var(--danger); box-shadow: 0 0 8px var(--danger); }
</style>
@endsection
