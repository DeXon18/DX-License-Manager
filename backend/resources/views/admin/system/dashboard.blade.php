@extends('layouts.app')

@section('title', 'System Control Center')

@section('header')
    <div class="flex items-center justify-between mb-8">
        <div>
            <nav class="flex mb-2 text-xs font-mono text-muted uppercase tracking-wider" aria-label="Breadcrumb">
                <ol class="flex items-center space-x-2">
                    <li><a href="{{ route('dashboard') }}" class="hover:text-accent">Portal</a></li>
                    <li><span class="px-1">/</span></li>
                    <li class="text-secondary">Admin</li>
                    <li><span class="px-1">/</span></li>
                    <li class="text-primary font-bold">System Dashboard</li>
                </ol>
            </nav>
            <h1 class="text-2xl font-bold tracking-tight text-primary flex items-center gap-3">
                <div class="p-2 rounded-lg bg-accent-muted text-accent">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                System Control Center
                <span class="flex h-2 w-2 rounded-full bg-success animate-pulse" title="Live System"></span>
            </h1>
        </div>
        <div class="flex gap-2">
            <button onclick="window.location.reload()" class="px-4 py-2 bg-raised border border-border text-secondary hover:text-primary rounded-md text-xs font-bold uppercase tracking-widest transition-all">
                Refresh Data
            </button>
        </div>
    </div>
@endsection

@section('content')
<div class="space-y-6">
    {{-- KPI Row --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Health Score --}}
        <div class="bg-surface border border-border rounded-lg p-5 shadow-sm">
            <div class="text-[0.65rem] font-bold uppercase tracking-[0.1em] text-muted mb-1">System Health Score</div>
            <div class="flex items-end justify-between">
                <div class="text-[1.8rem] font-bold text-primary font-mono tracking-tighter tabular-nums">98.2<span class="text-lg text-muted">%</span></div>
                <div class="text-success text-xs font-bold font-mono mb-1">STABLE</div>
            </div>
            <div class="mt-3 h-1 w-full bg-raised rounded-full overflow-hidden">
                <div class="h-full bg-success" style="width: 98.2%"></div>
            </div>
        </div>

        {{-- Total Licenses --}}
        <div class="bg-surface border border-border rounded-lg p-5 shadow-sm">
            <div class="text-[0.65rem] font-bold uppercase tracking-[0.1em] text-muted mb-1">Managed Licenses</div>
            <div class="text-[1.8rem] font-bold text-primary font-mono tracking-tighter tabular-nums">
                {{ $metrics['business']['total_contracts'] }}
            </div>
            <div class="flex items-center gap-2 mt-1">
                <span class="text-[0.7rem] text-secondary font-mono tracking-tight">{{ $metrics['business']['total_clients'] }} Clients</span>
                <span class="text-[0.7rem] text-warning font-bold font-mono">/ {{ $metrics['business']['expiring_soon'] }} Expiring</span>
            </div>
        </div>

        {{-- AI Audits --}}
        <div class="bg-surface border border-border rounded-lg p-5 shadow-sm">
            <div class="text-[0.65rem] font-bold uppercase tracking-[0.1em] text-muted mb-1">AI Audit Intelligence</div>
            <div class="text-[1.8rem] font-bold text-primary font-mono tracking-tighter tabular-nums">
                {{ $metrics['business']['total_audits'] }}
            </div>
            <div class="flex items-center gap-2 mt-1">
                @if($metrics['business']['pending_audits'] > 0)
                    <span class="flex h-1.5 w-1.5 rounded-full bg-warning animate-pulse"></span>
                    <span class="text-[0.7rem] text-warning font-bold font-mono">{{ $metrics['business']['pending_audits'] }} Processing</span>
                @else
                    <span class="text-[0.7rem] text-success font-bold font-mono">All Sincronized</span>
                @endif
                @if($metrics['business']['failed_audits'] > 0)
                    <span class="text-[0.7rem] text-danger font-bold font-mono">· {{ $metrics['business']['failed_audits'] }} Failed</span>
                @endif
            </div>
        </div>

        {{-- Infrastructure --}}
        <div class="bg-surface border border-border rounded-lg p-5 shadow-sm">
            <div class="text-[0.65rem] font-bold uppercase tracking-[0.1em] text-muted mb-1">Global Load Avg</div>
            <div class="text-[1.8rem] font-bold text-primary font-mono tracking-tighter tabular-nums">
                {{ $metrics['os']['load'] }}
            </div>
            <div class="text-[0.7rem] text-secondary font-mono mt-1">
                PHP {{ $metrics['os']['php_version'] }} · {{ $metrics['os']['name'] }}
            </div>
        </div>
    </div>

    {{-- Charts Row --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Trend Chart --}}
        <div class="lg:col-span-2 bg-surface border border-border rounded-lg p-5 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-bold uppercase tracking-wider text-secondary">Audit Activity Trend</h3>
                <span class="text-[0.65rem] font-mono text-muted">LAST 7 DAYS</span>
            </div>
            <div class="h-[280px]">
                <canvas id="trendChart"></canvas>
            </div>
        </div>

        {{-- Distribution Chart --}}
        <div class="bg-surface border border-border rounded-lg p-5 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-bold uppercase tracking-wider text-secondary">Vendor Daemons</h3>
                <span class="text-[0.65rem] font-mono text-muted">INVENTORY</span>
            </div>
            <div class="h-[280px]">
                <canvas id="distributionChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Hardware & Services Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Services Status --}}
        <div class="bg-surface border border-border rounded-lg p-5 shadow-sm">
            <h3 class="text-sm font-bold uppercase tracking-wider text-secondary mb-4">Runtime Services</h3>
            <div class="grid grid-cols-2 gap-3">
                @foreach($metrics['services'] as $name => $info)
                    <div class="p-3 rounded-md bg-raised border border-border flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-2 h-2 rounded-full {{ $info['status'] === 'online' ? 'bg-success' : ($info['status'] === 'degraded' ? 'bg-warning' : 'bg-danger') }}"></div>
                            <span class="text-xs font-bold uppercase tracking-tight text-primary">{{ $name }}</span>
                        </div>
                        <span class="text-[0.65rem] font-mono {{ $info['status'] === 'online' ? 'text-success' : ($info['status'] === 'degraded' ? 'text-warning' : 'text-danger') }}">
                            {{ strtoupper($info['message']) }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Resource Monitoring --}}
        <div class="bg-surface border border-border rounded-lg p-5 shadow-sm">
            <h3 class="text-sm font-bold uppercase tracking-wider text-secondary mb-4">Resource Utilization</h3>
            <div class="space-y-4">
                {{-- Memory --}}
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <label class="text-[0.65rem] font-bold uppercase tracking-widest text-muted">System Memory</label>
                        <span class="text-[0.7rem] font-mono text-secondary">{{ $metrics['hardware']['memory']['used'] }} / {{ $metrics['hardware']['memory']['total'] }}</span>
                    </div>
                    <div class="h-2 w-full bg-raised rounded-full overflow-hidden">
                        <div class="h-full {{ $metrics['hardware']['memory']['percent'] > 85 ? 'bg-danger' : ($metrics['hardware']['memory']['percent'] > 70 ? 'bg-warning' : 'bg-accent') }}" 
                             style="width: {{ $metrics['hardware']['memory']['percent'] }}%"></div>
                    </div>
                </div>

                {{-- Disk --}}
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <label class="text-[0.65rem] font-bold uppercase tracking-widest text-muted">Storage Partition</label>
                        <span class="text-[0.7rem] font-mono text-secondary">{{ $metrics['hardware']['memory']['used'] }} / {{ $metrics['hardware']['disk']['total'] }}</span>
                    </div>
                    <div class="h-2 w-full bg-raised rounded-full overflow-hidden">
                        <div class="h-full {{ $metrics['hardware']['disk']['percent'] > 90 ? 'bg-danger' : ($metrics['hardware']['disk']['percent'] > 75 ? 'bg-warning' : 'bg-accent') }}" 
                             style="width: {{ $metrics['hardware']['disk']['percent'] }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Colors from DESIGN.md
        const colors = {
            accent: '#388BFD',
            success: '#3FB950',
            warning: '#D29922',
            danger: '#E05252',
            siemens: '#009999',
            moldex: '#ED1C24',
            muted: '#8B949E'
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
                        grid: { color: 'rgba(139, 148, 158, 0.1)' },
                        ticks: { font: { family: 'IBM Plex Mono', size: 10 }, color: colors.muted }
                    },
                    x: { 
                        grid: { display: false },
                        ticks: { font: { family: 'IBM Plex Mono', size: 10 }, color: colors.muted }
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
                        colors.warning,
                        colors.muted
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
                        grid: { color: 'rgba(139, 148, 158, 0.1)' },
                        ticks: { font: { family: 'IBM Plex Mono', size: 10 }, color: colors.muted }
                    },
                    y: { 
                        grid: { display: false },
                        ticks: { font: { family: 'IBM Plex Mono', size: 10 }, color: colors.muted }
                    }
                }
            }
        });
    });
</script>
@endsection
