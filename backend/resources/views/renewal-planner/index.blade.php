@extends('layouts.app')

@section('title', 'Planificador de Renovaciones — DX Portal')

@section('content')
<div class="page-header" style="margin-bottom: 16px;">
    <div>
        <h1 class="welcome">Planificador de <span>Renovaciones</span></h1>
        <p class="welcome-sub">Gestión cíclica de licencias · {{ Carbon\Carbon::create(2024, $month, 1)->translatedFormat('F') }}</p>
    </div>
</div>

    <div class="stats-grid" style="display: grid; grid-template-columns: auto 1fr auto; gap: 30px; align-items: center; background: var(--bg-dark); padding: 20px 30px; border-radius: 12px; border: 1px solid var(--border); margin-bottom: 25px; box-shadow: 0 4px 20px rgba(0,0,0,0.2);">
        <!-- Selector de Mes (Custom Dropdown) -->
        <div style="display: flex; align-items: center; gap: 15px; padding-right: 25px; border-right: 1px solid var(--border);" 
             x-data="{ open: false, selected: {{ $month }}, selectedName: '{{ strtoupper(\Carbon\Carbon::create(2024, $month, 1)->translatedFormat('F')) }}' }">
            <i class="fa-solid fa-calendar-days" style="color: var(--accent); font-size: 18px;"></i>
            
            <div style="position: relative;">
                <button @click="open = !open" @click.away="open = false" type="button" 
                        style="background: transparent; border: none; color: var(--primary); font-size: 18px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.1em; cursor: pointer; outline: none; display: flex; align-items: center; gap: 10px; padding: 5px 0;">
                    <span x-text="selectedName"></span>
                    <i class="fa-solid fa-chevron-down" style="font-size: 12px; transition: transform 0.2s;" :style="open ? 'transform: rotate(180deg)' : ''"></i>
                </button>

                <!-- Menú Desplegable -->
                <div x-show="open" 
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="opacity-0 transform scale-95"
                     x-transition:enter-end="opacity-100 transform scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="opacity-100 transform scale-100"
                     x-transition:leave-end="opacity-0 transform scale-95"
                     style="position: absolute; top: 100%; left: 0; z-index: 50; margin-top: 10px; width: 200px; background: var(--surface); border: 1px solid var(--border); border-radius: 8px; box-shadow: var(--elevation-2); overflow: hidden; display: none;"
                     :style="{ display: open ? 'block' : 'none' }">
                    <div style="max-height: 300px; overflow-y: auto; padding: 5px;">
                        @foreach(range(1, 12) as $m)
                            @php $mName = strtoupper(\Carbon\Carbon::create(2024, $m, 1)->translatedFormat('F')); @endphp
                            <div @click="selected = {{ $m }}; selectedName = '{{ $mName }}'; open = false; $nextTick(() => $refs.monthForm.submit())" 
                                 style="padding: 10px 15px; font-size: 11px; font-weight: 700; color: {{ $month == $m ? 'var(--accent)' : 'var(--secondary)' }}; cursor: pointer; border-radius: 4px; transition: all 0.2s; text-transform: uppercase; letter-spacing: 0.05em;"
                                 onmouseover="this.style.background='var(--raised)'; this.style.color='var(--primary)'"
                                 onmouseout="this.style.background='transparent'; this.style.color='{{ $month == $m ? 'var(--accent)' : 'var(--secondary)' }}'">
                                {{ $mName }}
                                @if($month == $m)
                                    <i class="fa-solid fa-check" style="float: right; margin-top: 2px;"></i>
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
        <div style="display: flex; flex-wrap: wrap; gap: 8px; align-items: center;">
            <span style="font-size: 10px; font-weight: 800; color: var(--muted); text-transform: uppercase; letter-spacing: 0.1em; margin-right: 8px;">Filtrar por:</span>
            <form action="{{ route('renewal-planner.index') }}" method="GET" id="filter-form" style="display: flex; flex-wrap: wrap; gap: 6px;">
                <input type="hidden" name="month" value="{{ $month }}">
                @foreach($availableStatuses as $status)
                    @php
                        $isSelected = in_array($status, $selectedStatuses);
                        $colorClass = match(trim($status)) {
                            'Ofertado' => 'info',
                            'En negociación' => 'primary',
                            'Aceptado por el cliente' => 'accent',
                            'Procesado (M) - Pte fact.' => 'warn',
                            'Facturado - Pte proc. (M)' => 'warning',
                            'Cerrado' => 'success',
                            'Baja' => 'danger',
                            default => 'muted'
                        };
                        $colorVar = "var(--$colorClass)";
                    @endphp
                    <label style="cursor: pointer; transition: all 0.2s;">
                        <input type="checkbox" name="statuses[]" value="{{ $status }}" {{ $isSelected ? 'checked' : '' }} onchange="this.form.submit()" style="display: none;">
                        <span style="
                            display: inline-block;
                            padding: 4px 12px;
                            border-radius: 20px;
                            font-size: 10px;
                            font-weight: 700;
                            border: 1px solid {{ $isSelected ? $colorVar : 'var(--border)' }};
                            background: {{ $isSelected ? "rgba($colorVar, 0.1)" : 'transparent' }};
                            color: {{ $isSelected ? $colorVar : 'var(--muted)' }};
                            transition: all 0.2s ease;
                            white-space: nowrap;
                        " onmouseover="this.style.borderColor='{{ $colorVar }}'; this.style.color='{{ $colorVar }}'" onmouseout="if(!{{ $isSelected ? 'true' : 'false' }}){ this.style.borderColor='var(--border)'; this.style.color='var(--muted)'; }">
                            {{ $status ?: 'Sin estado' }}
                        </span>
                    </label>
                @endforeach
            </form>
        </div>

        <!-- Estadísticas -->
        <div style="display: flex; gap: 40px; padding-left: 25px; border-left: 1px solid var(--border);">
            <div style="text-align: right;">
                <div style="font-size: 9px; font-weight: 800; color: var(--muted); text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 2px;">Pendientes</div>
                <div style="font-size: 28px; font-weight: 900; color: var(--primary); font-family: var(--font-mono); line-height: 1;">
                    {{ count($pendingRenewals) - count($completedLogs) }}
                </div>
            </div>
            <div style="text-align: right;">
                <div style="font-size: 9px; font-weight: 800; color: var(--success); text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 2px;">Completados</div>
                <div style="font-size: 28px; font-weight: 900; color: var(--success); font-family: var(--font-mono); line-height: 1;">
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
                    <th style="width: 140px;" class="text-center">Servidores</th>
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
                            <div class="font-bold" style="font-size: 13px;">
                                {{ $client->name ?? 'Desconocido' }}
                                @if($isCompleted)
                                    <i class="fa-solid fa-circle-check" style="margin-left: 6px; color: var(--success); font-size: 11px;"></i>
                                @endif
                            </div>
                            <div class="muted" style="font-size: 10px; margin-top: 4px; font-style: italic;">
                                {{ $contracts->count() }} contrato{{ $contracts->count() > 1 ? 's' : '' }} detectado{{ $contracts->count() > 1 ? 's' : '' }}
                            </div>
                        </td>
                        <td style="vertical-align: top;" class="text-center">
                            <div style="display: flex; flex-direction: column; gap: 4px; align-items: center;">
                                @forelse($client->inventoryDaemons as $daemon)
                                    @php $isSiemens = ($daemon->vendor === 'siemens'); @endphp
                                    <div style="display: flex; align-items: center; background: var(--bg); border: 1px solid var(--border); border-radius: 3px; padding: 1px 5px; gap: 5px;">
                                        <span style="font-size: 7px; font-weight: 900; color: {{ $isSiemens ? 'var(--siemens)' : 'var(--moldex)' }}; text-transform: uppercase;">
                                            {{ $daemon->vendor }}
                                        </span>
                                        <span class="font-mono" style="font-size: 10px; color: var(--secondary);">{{ $daemon->sold_to }}</span>
                                    </div>
                                @empty
                                    <span class="muted text-xs">—</span>
                                @endforelse
                            </div>
                        </td>
                        <td style="vertical-align: top;">
                            <div style="display: flex; flex-direction: column; gap: 8px;">
                                @foreach($contracts as $contract)
                                    @php
                                        $status = trim($contract->status ?: 'vacio');
                                        $statusMap = [
                                            'vacio' => ['label' => 'Sin estado', 'class' => 'badge-muted'],
                                            'Ofertado' => ['label' => 'Ofertado', 'class' => 'badge-info'],
                                            'En negociación' => ['label' => 'Negociación', 'class' => 'badge-primary'],
                                            'Aceptado por el cliente' => ['label' => 'Aceptado', 'class' => 'badge-accent'],
                                            'Procesado (M) - Pte fact.' => ['label' => 'Procesado', 'class' => 'badge-warn'],
                                            'Facturado - Pte proc. (M)' => ['label' => 'Facturado', 'class' => 'badge-warning'],
                                            'Cerrado' => ['label' => 'Cerrado', 'class' => 'badge-success'],
                                            'Baja' => ['label' => 'Baja', 'class' => 'badge-danger'],
                                        ];
                                        $data = $statusMap[$status] ?? ['label' => $status, 'class' => 'badge-muted'];
                                    @endphp
                                    <div style="display: grid; grid-template-columns: 85px 80px 100px 1fr; gap: 12px; align-items: center;">
                                        <span class="font-mono" style="font-size: 9px; font-weight: 800; color: var(--accent); background: var(--bg); padding: 1px 4px; border-radius: 3px; border: 1px solid var(--border); text-align: center;">
                                            {{ $contract->contract_number }}
                                        </span>
                                        <span class="font-mono" style="font-size: 10px; color: var(--secondary); font-weight: 600;">
                                            {{ \Carbon\Carbon::parse($contract->end_date)->format('d/m/Y') }}
                                        </span>
                                        <span class="badge {{ $data['class'] }}" style="font-size: 8px; padding: 1px 6px; white-space: nowrap; justify-self: start;">
                                            {{ $data['label'] }}
                                        </span>
                                        <span class="muted" style="font-size: 10px; font-style: italic; line-height: 1.2; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $contract->comment }}">
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
                                    
                                    <button type="submit" class="action-btn" title="Marcar como enviado" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; background: transparent; border: 1px solid var(--border); border-radius: 6px; transition: all 0.2s; cursor: pointer; color: var(--accent);">
                                        <i class="fa-solid fa-check" style="font-size: 14px;"></i>
                                    </button>
                                </form>
                            @else
                                <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 4px;">
                                    <span class="badge badge-success" style="font-size: 8px; padding: 2px 8px; text-transform: uppercase;">OK</span>
                                    <form action="{{ route('renewal-planner.destroy') }}" method="POST" onsubmit="return confirm('¿Revertir estado a pendiente?')">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="client_id" value="{{ $clientId }}">
                                        <input type="hidden" name="month" value="{{ $month }}">
                                        <button type="submit" style="background: transparent; border: none; color: var(--danger); cursor: pointer; padding: 2px; font-size: 11px; opacity: 0.5;" title="Deshacer">
                                            <i class="fa-solid fa-rotate-left"></i>
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </td>
                    </tr>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 60px; color: var(--muted);">
                            <div style="font-size: 40px; margin-bottom: 20px;">🛡️</div>
                            No hay renovaciones pendientes para este mes.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
