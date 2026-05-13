@extends('layouts.app')

@section('title', 'Planificador de Renovaciones — DX Portal')

@section('content')
<div class="page-header" style="margin-bottom: 16px;">
    <div>
        <h1 class="welcome">Planificador de <span>Renovaciones</span></h1>
        <p class="welcome-sub">Gestión cíclica de licencias · {{ Carbon\Carbon::create(2024, $month, 1)->translatedFormat('F') }}</p>
    </div>
</div>

<div class="card" style="margin-bottom: 24px;">
    <div style="padding: 12px 20px; display: flex; align-items: center; justify-content: space-between; background: var(--raised); border-bottom: 1px solid var(--border);">
        <div style="display: flex; gap: 15px; align-items: center;">
            <div style="display: flex; align-items: center; gap: 8px; background: var(--bg); padding: 4px 10px; border-radius: 6px; border: 1px solid var(--border);">
                <i class="fa-regular fa-calendar-check" style="color: var(--accent); font-size: 13px;"></i>
                <form action="{{ route('renewal-planner.index') }}" method="GET" id="monthFilterForm">
                    <select name="month" onchange="document.getElementById('monthFilterForm').submit()" 
                        style="background: transparent; border: none; color: var(--primary); font-size: 13px; font-weight: 700; cursor: pointer; padding-right: 5px; outline: none;">
                        @foreach(range(1, 12) as $m)
                            @php $mDate = Carbon\Carbon::create(2024, $m, 1); @endphp
                            <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                {{ strtoupper($mDate->translatedFormat('F')) }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>
        </div>
        <div style="display: flex; gap: 24px;">
            <div style="display: flex; flex-direction: column; align-items: flex-end;">
                <span style="font-size: 10px; color: var(--muted); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 2px;">Pendientes</span>
                <span style="font-family: var(--font-mono); font-size: 16px; font-weight: 800; color: var(--warn);">
                    {{ count($pendingRenewals) - count($completedLogs) }}
                </span>
            </div>
            <div style="display: flex; flex-direction: column; align-items: flex-end;">
                <span style="font-size: 10px; color: var(--muted); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 2px;">Completados</span>
                <span style="font-family: var(--font-mono); font-size: 16px; font-weight: 800; color: var(--success);">
                    {{ count($completedLogs) }}
                </span>
            </div>
        </div>
    </div>

    <div class="table-container">
        <table style="border-collapse: separate; border-spacing: 0;">
            <thead>
                <tr>
                    <th style="padding: 12px 20px;">Cliente / Contratos</th>
                    <th style="padding: 12px 20px;">Servidores (Sold-To)</th>
                    <th style="padding: 12px 20px; width: 140px;">Vencimiento</th>
                    <th style="padding: 12px 20px; width: 130px; text-align: center;">Acción</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pendingRenewals as $clientId => $contracts)
                    @php 
                        $client = $contracts->first()->client;
                        $isCompleted = in_array($clientId, $completedLogs);
                    @endphp
                    <tr style="{{ $isCompleted ? 'opacity: 0.5; background: rgba(0,255,0,0.01);' : '' }}">
                        <td style="padding: 10px 20px;">
                            <div style="font-weight: 700; font-size: 13px; color: {{ $isCompleted ? 'var(--muted)' : 'var(--primary)' }};">
                                {{ $client->name ?? 'Desconocido' }}
                                @if($isCompleted)
                                    <i class="fa-solid fa-circle-check" style="margin-left: 6px; color: var(--success); font-size: 11px;"></i>
                                @endif
                            </div>
                            <div style="font-size: 10px; color: var(--muted); margin-top: 2px;">
                                {{ $contracts->count() }} contrato(s) detectado(s)
                            </div>
                        </td>
                        <td style="padding: 10px 20px;">
                            <div style="display: flex; flex-wrap: wrap; gap: 6px;">
                                @forelse($client->inventoryDaemons as $daemon)
                                    @php $isSiemens = ($daemon->vendor === 'siemens'); @endphp
                                    <div style="display: flex; align-items: center; background: var(--bg); border: 1px solid var(--border); border-radius: 4px; padding: 2px 6px; gap: 6px;">
                                        <span style="font-size: 9px; font-weight: 800; color: {{ $isSiemens ? 'var(--siemens)' : 'var(--moldex)' }}; text-transform: uppercase;">
                                            {{ $daemon->vendor }}
                                        </span>
                                        <span style="font-family: var(--font-mono); font-size: 11px; color: var(--secondary);">{{ $daemon->sold_to }}</span>
                                    </div>
                                @empty
                                    <span style="font-size: 11px; color: var(--muted); font-style: italic;">— Sin inventario auditado —</span>
                                @endforelse
                            </div>
                        </td>
                        <td style="padding: 10px 20px;">
                            @foreach($contracts as $contract)
                                <div style="font-size: 12px; font-family: var(--font-mono); color: var(--secondary); margin-bottom: 2px;">
                                    {{ \Carbon\Carbon::parse($contract->end_date)->format('d/m/Y') }}
                                </div>
                            @endforeach
                        </td>
                        <td style="padding: 10px 20px; text-align: center;">
                            @if(!$isCompleted)
                                <form action="{{ route('renewal-planner.store') }}" method="POST" enctype="multipart/form-data" id="form-{{ $clientId }}" style="display: flex; flex-direction: column; gap: 4px;">
                                    @csrf
                                    <input type="hidden" name="client_id" value="{{ $clientId }}">
                                    <input type="hidden" name="month" value="{{ $month }}">
                                    
                                    <!-- Selector de Archivo Minimalista -->
                                    <label class="file-upload-label" style="display: flex; align-items: center; justify-content: center; gap: 6px; padding: 4px; border: 1px dashed var(--border); border-radius: 4px; cursor: pointer; transition: all 0.2s;" title="Adjuntar Licencia .lic">
                                        <i class="fa-solid fa-paperclip" style="font-size: 10px; color: var(--muted);"></i>
                                        <span style="font-size: 9px; color: var(--muted); text-transform: uppercase;">Adjuntar .lic</span>
                                        <input type="file" name="license_file" style="display: none;" onchange="this.parentElement.style.borderColor = 'var(--success)'; this.parentElement.querySelector('span').innerText = 'Archivo Listo'; this.parentElement.querySelector('span').style.color = 'var(--success)';">
                                    </label>

                                    <button type="submit" class="action-btn" style="padding: 5px 10px; height: auto; width: 100%; justify-content: center; background: transparent; border: 1px solid var(--accent); border-radius: 4px; transition: all 0.2s;">
                                        <span style="font-size: 10px; font-weight: 800; color: var(--accent); letter-spacing: 0.05em;">MARCAR ENVIADO</span>
                                    </button>
                                </form>
                            @else
                                <span style="font-size: 10px; font-weight: 800; color: var(--success); text-transform: uppercase; letter-spacing: 0.1em; background: rgba(0,255,0,0.05); padding: 4px 8px; border-radius: 4px; border: 1px solid rgba(0,255,0,0.2);">
                                    PROCESADO
                                </span>
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
