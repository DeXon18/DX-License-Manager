@extends('layouts.app')

@php
    if (!function_exists('hexToRgb')) {
        function hexToRgb($hex) {
            $hex = str_replace("#", "", $hex);
            if(strlen($hex) == 3) {
                $r = hexdec(substr($hex,0,1).substr($hex,0,1));
                $g = hexdec(substr($hex,1,1).substr($hex,1,1));
                $b = hexdec(substr($hex,2,1).substr($hex,2,1));
            } else {
                $r = hexdec(substr($hex,0,2));
                $g = hexdec(substr($hex,2,2));
                $b = hexdec(substr($hex,4,2));
            }
            return "$r, $g, $b";
        }
    }
@endphp

@section('title', 'Planificador de Renovaciones — DX Portal')

@section('content')
<div class="page-header">
    <div>
        <h1 class="welcome">Planificador de <span>Renovaciones</span></h1>
        <p class="welcome-sub">Gestión cíclica de licencias · {{ Carbon\Carbon::create(2024, $month, 1)->translatedFormat('F') }}</p>
    </div>
</div>

    <div class="dx-v2-planner-header-grid">
        <!-- Selector de Mes (Custom Dropdown) -->
        <div class="dx-v2-planner-month-picker" 
             x-data="{ open: false, selected: {{ $month }}, selectedName: '{{ strtoupper(\Carbon\Carbon::create(2024, $month, 1)->translatedFormat('F')) }}' }">
            <i class="fa-solid fa-calendar-days dx-v2-planner-month-picker-icon"></i>
            
            <div>
                <button @click="open = !open" @click.away="open = false" type="button" class="dx-v2-planner-month-btn">
                    <span x-text="selectedName"></span>
                    <i class="fa-solid fa-chevron-down" :style="open ? 'transform: rotate(180deg)' : ''"></i>
                </button>

                <!-- Menú Desplegable -->
                <div x-show="open" 
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="opacity-0 transform scale-95"
                     x-transition:enter-end="opacity-100 transform scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="opacity-100 transform scale-100"
                     x-transition:leave-end="opacity-0 transform scale-95"
                     class="dx-v2-planner-dropdown"
                     :style="{ display: open ? 'block' : 'none' }">
                    <div class="dx-v2-planner-dropdown-container">
                        @foreach(range(1, 12) as $m)
                            @php $mName = strtoupper(\Carbon\Carbon::create(2024, $m, 1)->translatedFormat('F')); @endphp
                            <div @click="selected = {{ $m }}; selectedName = '{{ $mName }}'; open = false; $nextTick(() => $refs.monthForm.submit())" 
                                 class="dx-v2-planner-dropdown-item {{ $month == $m ? 'active' : '' }}">
                                <span x-text="'{{ $mName }}'"></span>
                                @if($month == $m)
                                    <i class="fa-solid fa-check"></i>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <form action="{{ route('renewal-planner.index') }}" method="GET" x-ref="monthForm" style="display: none;">
                <input type="hidden" name="month" :value="selected">
                @foreach($selectedStatuses as $s)
                    <input type="hidden" name="statuses[]" value="{{ $s }}">
                @endforeach
            </form>
        </div>

        <!-- Filtros de Estado -->
        <div class="dx-v2-planner-filters-wrap">
            <span class="dx-v2-planner-filter-label">Filtrar por:</span>
            <form action="{{ route('renewal-planner.index') }}" method="GET" id="filter-form" class="dx-v2-planner-filter-form">
                <input type="hidden" name="month" value="{{ $month }}">
                @foreach($availableStatuses as $status)
                    @php
                        $isSelected = in_array($status, $selectedStatuses);
                        $color = match(trim($status)) {
                            'Ofertado' => '#58a6ff',
                            'En negociación' => '#388bfd',
                            'Aceptado por el cliente' => '#bc71f8',
                            'Procesado (M) - Pte fact.' => '#d29922',
                            'Facturado - Pte proc. (M)' => '#db6d28',
                            'Cerrado' => '#3fb950',
                            'Baja' => '#e05252',
                            default => '#8b949e'
                        };
                    @endphp
                    <label style="cursor: pointer; transition: all 0.2s;">
                        <input type="checkbox" name="statuses[]" value="{{ $status }}" {{ $isSelected ? 'checked' : '' }} onchange="this.form.submit()" style="display: none;">
                        <span class="dx-v2-planner-filter-chip {{ $isSelected ? 'active' : '' }}"
                              style="--filter-color: {{ $color }}; --filter-bg-active: rgba({{ hexToRgb($color) }}, 0.15);">
                            {{ $status ?: 'Sin estado' }}
                        </span>
                    </label>
                @endforeach

                @if(count($selectedStatuses) > 0)
                    <a href="{{ route('renewal-planner.index', ['month' => $month]) }}" class="dx-v2-planner-filter-clear">
                        <i class="fa-solid fa-trash-can"></i>
                        <span>LIMPIAR</span>
                    </a>
                @endif
            </form>
        </div>

        <!-- Estadísticas -->
        <div class="dx-v2-planner-stats">
            <div class="dx-v2-planner-stat-item">
                <div class="dx-v2-planner-stat-label">Pendientes</div>
                <div class="dx-v2-planner-stat-value">
                    {{ count($pendingRenewals) - count($completedLogs) }}
                </div>
            </div>
            <div class="dx-v2-planner-stat-item" style="--stat-color: var(--dx-v2-success);">
                <div class="dx-v2-planner-stat-label">Completados</div>
                <div class="dx-v2-planner-stat-value">
                    {{ count($completedLogs) }}
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <table class="table">
            <thead>
                <tr>
                    <th style="width: 250px;">Cliente</th>
                    <th style="width: 140px;" class="text-center">ID Licencia</th>
                    <th>Contrato | Vencimiento | Estado | Comentario</th>
                    <th style="width: 80px;" class="text-right">Acción</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pendingRenewals as $clientId => $contracts)
                    @php 
                        $client = $contracts->first()->client;
                        $isCompleted = in_array($clientId, $completedLogs);
                    @endphp
                    <tr class="{{ $isCompleted ? 'opacity-50' : '' }}">
                        <td style="vertical-align: top;">
                            <div class="dx-v2-planner-client-title">
                                {{ $client->name ?? 'Desconocido' }}
                                @if($isCompleted)
                                    <i class="fa-solid fa-circle-check"></i>
                                @endif
                            </div>
                            <div class="dx-v2-planner-client-subtitle">
                                {{ $contracts->count() }} contrato{{ $contracts->count() > 1 ? 's' : '' }} detectado{{ $contracts->count() > 1 ? 's' : '' }}
                            </div>
                        </td>
                        <td style="vertical-align: top;" class="text-center">
                            <div class="dx-v2-planner-daemons-stack">
                                @forelse($client->inventoryDaemons as $daemon)
                                    @php $isSiemens = ($daemon->vendor === 'siemens'); @endphp
                                    <div class="dx-v2-planner-vendor-badge">
                                        <span class="dx-v2-planner-vendor-label {{ $isSiemens ? 'siemens' : 'moldex3d' }}">
                                            {{ $daemon->vendor }}
                                        </span>
                                        <span class="dx-v2-planner-vendor-value">{{ $daemon->sold_to }}</span>
                                    </div>
                                @empty
                                    <span class="muted text-xs">—</span>
                                @endforelse
                            </div>
                        </td>
                        <td style="vertical-align: top;">
                            <div class="dx-v2-planner-contracts-list">
                                @foreach($contracts as $contract)
                                    @php
                                        $status = trim($contract->status ?: 'vacio');
                                        $statusData = match($status) {
                                            'Ofertado' => ['label' => 'Ofertado', 'color' => '#58a6ff'],
                                            'En negociación' => ['label' => 'Negociación', 'color' => '#388bfd'],
                                            'Aceptado por el cliente' => ['label' => 'Aceptado', 'color' => '#bc71f8'],
                                            'Procesado (M) - Pte fact.' => ['label' => 'Procesado', 'color' => '#d29922'],
                                            'Facturado - Pte proc. (M)' => ['label' => 'Facturado', 'color' => '#db6d28'],
                                            'Cerrado' => ['label' => 'Cerrado', 'color' => '#3fb950'],
                                            'Baja' => ['label' => 'Baja', 'color' => '#e05252'],
                                            default => ['label' => $status ?: 'Sin estado', 'color' => '#8b949e'],
                                        };
                                    @endphp
                                    <div class="dx-v2-planner-contract-row">
                                        <span class="dx-v2-planner-contract-number">
                                            {{ $contract->contract_number }}
                                        </span>
                                        <span class="dx-v2-planner-contract-date">
                                            {{ \Carbon\Carbon::parse($contract->end_date)->format('d/m/Y') }}
                                        </span>
                                        <span class="badge" style="font-size: 8px; padding: 1px 6px; white-space: nowrap; justify-self: start; background: rgba({{ hexToRgb($statusData['color']) }}, 0.15); color: {{ $statusData['color'] }}; border: 1px solid rgba({{ hexToRgb($statusData['color']) }}, 0.3);">
                                            {{ $statusData['label'] }}
                                        </span>
                                        <span class="dx-v2-planner-contract-comment" title="{{ $contract->comment }}">
                                            {{ $contract->comment ?: '—' }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </td>
                        <td style="vertical-align: middle;" class="text-right">
                            @if(!$isCompleted)
                                <form action="{{ route('renewal-planner.store') }}" method="POST" id="form-{{ $clientId }}">
                                    @csrf
                                    <input type="hidden" name="client_id" value="{{ $clientId }}">
                                    <input type="hidden" name="month" value="{{ $month }}">
                                    
                                    <button type="submit" class="dx-v2-planner-btn-action" title="Marcar como enviado">
                                        <i class="fa-solid fa-check"></i>
                                    </button>
                                </form>
                            @else
                                <div class="dx-v2-planner-completed-status">
                                    <span class="badge badge-success" style="font-size: 8px; padding: 2px 8px; text-transform: uppercase;">OK</span>
                                    <form action="{{ route('renewal-planner.destroy') }}" method="POST" onsubmit="return confirm('¿Revertir estado a pendiente?')">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="client_id" value="{{ $clientId }}">
                                        <input type="hidden" name="month" value="{{ $month }}">
                                        <button type="submit" class="dx-v2-planner-btn-action-revert" title="Deshacer">
                                            <i class="fa-solid fa-rotate-left"></i>
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">
                            <div class="dx-v2-planner-empty">
                                <div class="dx-v2-planner-empty-icon">🛡️</div>
                                <span>No hay renovaciones pendientes para este mes.</span>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
