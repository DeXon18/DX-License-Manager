@extends('layouts.app')

@section('title', 'Planificador de Renovaciones — DX Portal')

@section('content')
<div class="page-header">
    <div>
        <h1 class="welcome">Planificador de <span>Renovaciones</span></h1>
        <p class="welcome-sub">Control mensual de licencias periódicas y actualizaciones de contrato</p>
    </div>
</div>

<div class="card" style="margin-bottom: 24px;">
    <div style="padding: 16px; display: flex; align-items: center; justify-content: space-between; background: var(--raised);">
        <div style="display: flex; gap: 10px; align-items: center;">
            <span style="font-size: 13px; font-weight: 600; color: var(--secondary);">Filtrar por Mes:</span>
            <form action="{{ route('renewal-planner.index') }}" method="GET" id="monthFilterForm" style="display: flex; gap: 8px;">
                <select name="month" onchange="document.getElementById('monthFilterForm').submit()" 
                    style="background: var(--bg); border: 1px solid var(--border); color: var(--primary); padding: 4px 12px; border-radius: 4px; font-size: 13px; font-weight: 500;">
                    @foreach(range(1, 12) as $m)
                        @php $mDate = Carbon\Carbon::create(2024, $m, 1); @endphp
                        <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                            {{ $mDate->translatedFormat('F') }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>
        <div style="display: flex; gap: 20px;">
            <div style="text-align: right;">
                <span style="display: block; font-size: 10px; color: var(--muted); text-transform: uppercase;">Pendientes</span>
                <span style="font-family: var(--font-mono); font-size: 18px; font-weight: 700; color: var(--warn);">
                    {{ count($pendingRenewals) - count($completedLogs) }}
                </span>
            </div>
            <div style="text-align: right;">
                <span style="display: block; font-size: 10px; color: var(--muted); text-transform: uppercase;">Completados</span>
                <span style="font-family: var(--font-mono); font-size: 18px; font-weight: 700; color: var(--success);">
                    {{ count($completedLogs) }}
                </span>
            </div>
        </div>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th style="width: 300px;">Cliente / Contratos</th>
                    <th>Sold-Tos Asociados</th>
                    <th style="width: 150px;">Vencimiento</th>
                    <th style="width: 120px; text-align: center;">Acción</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pendingRenewals as $clientId => $contracts)
                    @php 
                        $client = $contracts->first()->client;
                        $isCompleted = in_array($clientId, $completedLogs);
                    @endphp
                    <tr style="{{ $isCompleted ? 'opacity: 0.6; background: rgba(0,255,0,0.02);' : '' }}">
                        <td>
                            <div style="font-weight: 700; font-size: 14px; color: var(--primary);">
                                {{ $client->name ?? 'Desconocido' }}
                                @if($isCompleted)
                                    <span style="margin-left: 8px; font-size: 10px; color: var(--success); text-transform: uppercase; letter-spacing: 0.1em;">✅ Completado</span>
                                @endif
                            </div>
                            <div style="font-size: 11px; color: var(--muted); margin-top: 4px;">
                                {{ $contracts->count() }} Contrato(s) en este mes
                            </div>
                        </td>
                        <td>
                            <div style="display: flex; flex-wrap: wrap; gap: 4px;">
                                @forelse($client->inventoryDaemons as $daemon)
                                    <span class="badge" style="background: var(--surface); color: var(--secondary); font-size: 10px; border: 1px solid var(--border);">
                                        {{ $daemon->sold_to }} ({{ $daemon->vendor }})
                                    </span>
                                @empty
                                    <span style="font-size: 11px; color: var(--muted); font-style: italic;">Sin inventario auditado</span>
                                @endforelse
                            </div>
                        </td>
                        <td>
                            @foreach($contracts as $contract)
                                <div style="font-size: 12px; font-family: var(--font-mono); margin-bottom: 2px;">
                                    {{ \Carbon\Carbon::parse($contract->end_date)->format('d/m/Y') }}
                                </div>
                            @endforeach
                        </td>
                        <td style="text-align: center;">
                            @if(!$isCompleted)
                                <form action="{{ route('renewal-planner.store') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="client_id" value="{{ $clientId }}">
                                    <input type="hidden" name="month" value="{{ $month }}">
                                    <button type="submit" class="action-btn" style="padding: 6px 12px; height: auto; width: 100%; justify-content: center; background: var(--bg); border: 1px solid var(--accent);">
                                        <span class="action-btn-label" style="font-size: 11px; font-weight: 700; color: var(--accent);">MARCAR ENVIADO</span>
                                    </button>
                                </form>
                            @else
                                <div style="color: var(--success); font-size: 12px; font-weight: 600;">
                                    PROCESADO
                                </div>
                            @endif
                        </td>
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
