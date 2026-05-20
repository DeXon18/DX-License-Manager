@extends('layouts.app')

@section('title', 'Bandeja de Normalización — DX License Manager')

@section('content')

@if($errors->any())
    <div class="badge badge-danger" style="width: 100%; padding: 12px; margin-bottom: 24px; text-transform: none; border-radius: 4px; flex-direction: column; align-items: flex-start; gap: 8px;">
        <div style="display: flex; align-items: center;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="margin-right: 8px;"><path d="M18 6L6 18M6 6l12 12"/></svg>
            <strong>Errores de validación:</strong>
        </div>
        <ul style="margin-left: 24px; font-size: 11px;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="page-header">
    <div class="breadcrumb" style="margin-bottom: 8px;">
        <a href="{{ route('admin.import.index') }}" style="color: var(--accent); text-decoration: none;">Importación</a>
        <span style="margin: 0 8px; opacity: 0.3;">/</span>
        <span style="opacity: 0.5;">Bandeja de Normalización</span>
    </div>
    <h1 class="welcome">Bandeja de <span>Normalización</span></h1>
    <p class="welcome-sub">Gestión de identidades y duplicados detectados por el motor de sincronización</p>
</div>

<div class="stats-row" style="margin-bottom: 24px;">
    <div class="stat-card">
        <span class="stat-label">Sospechas de Importación</span>
        <span class="stat-value {{ collect($findings)->where('type', 'suspicion')->count() > 0 ? 'warn' : '' }}">
            {{ collect($findings)->where('type', 'suspicion')->count() }}
        </span>
        <span class="stat-meta">Warnings de CSV y licencias</span>
    </div>
    <div class="stat-card">
        <span class="stat-label">Duplicados en Base de Datos</span>
        <span class="stat-value {{ count($scannedDuplicates) > 0 ? 'warn' : '' }}" style="color: var(--accent);">
            {{ count($scannedDuplicates) }}
        </span>
        <span class="stat-meta">Detectados por similitud léxica</span>
    </div>
</div>

<div x-data="{ 
    tab: localStorage.getItem('activeNormTab') || 'warnings',
    scanning: false,
    scanningText: 'Iniciando escáner de base de datos...',
    setTab(name) {
        this.tab = name;
        localStorage.setItem('activeNormTab', name);
    },
    triggerScan() {
        this.scanning = true;
        document.getElementById('dx-force-scan-form').submit();
    }
}">
    <!-- Selector de Pestañas Premium -->
    <div class="dx-normalization-tabs">
        <button class="dx-normalization-tab-link" :class="{ 'active': tab === 'warnings' }" @click="setTab('warnings')">
            <i class="fa-solid fa-bell"></i>
            Sospechas de Importación
            @if(count($findings) > 0)
                <span class="badge badge-danger sm" style="font-size: 8px; padding: 2px 5px; margin-left: 4px;">{{ count($findings) }}</span>
            @endif
        </button>
        <button class="dx-normalization-tab-link" :class="{ 'active': tab === 'scanner' }" @click="setTab('scanner')">
            <i class="fa-solid fa-magnifying-glass"></i>
            Escáner de Duplicados (IA)
            @if(count($scannedDuplicates) > 0)
                <span class="badge badge-warn sm" style="font-size: 8px; padding: 2px 5px; margin-left: 4px; background: rgba(245, 158, 11, 0.15); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.3);">{{ count($scannedDuplicates) }}</span>
            @endif
        </button>
        <button class="dx-normalization-tab-link" :class="{ 'active': tab === 'manual' }" @click="setTab('manual')">
            <i class="fa-solid fa-compress"></i>
            Unificación Manual Libre
        </button>
    </div>

    <!-- Pestaña 1: Sospechas de Importación -->
    <div x-show="tab === 'warnings'" x-transition>
        <div class="card">
            <div class="card-header">
                <span class="card-title">Análisis de Identidades por Importación</span>
            </div>
            
            @if(count($findings) > 0)
                <table>
                    <thead>
                        <tr>
                            <th style="width: 30%;">Hallazgo</th>
                            <th style="width: 40%;">Propuesta del Motor</th>
                            <th>Origen</th>
                            <th style="text-align: right;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($findings as $finding)
                            @php
                                $isAi = false;
                                $provider = 'IA';
                                $confidence = null;
                                $reason = null;

                                if (preg_match('/se parece un ([\d.]+)%\s*\((\w+)\) a/i', $finding['full_message'], $matches)) {
                                    $isAi = true;
                                    $confidence = $matches[1];
                                    $provider = $matches[2];
                                }
                                
                                if (preg_match('/Razón:\s*(.*?)\s*Se ha creado/i', $finding['full_message'], $matches)) {
                                    $reason = $matches[1];
                                }
                            @endphp
                            <tr>
                                <td>
                                    <div style="font-weight: 700; color: var(--primary);">{{ $finding['detected_name'] }}</div>
                                    @if($isAi)
                                        <span class="dx-v2-normalization-badge-ai">🤖 SUGERENCIA {{ $provider }} ({{ $confidence }}%)</span>
                                    @elseif($finding['type'] === 'suspicion')
                                        <span class="badge badge-warn" style="font-size: 8px; padding: 2px 4px; margin-top: 4px;">SOSPECHA DUPLICADO</span>
                                    @else
                                        <span class="badge badge-success" style="font-size: 8px; padding: 2px 4px; margin-top: 4px;">NUEVA IDENTIDAD</span>
                                    @endif
                                </td>
                                <td>
                                    @if($finding['type'] === 'suspicion')
                                        <div style="display: flex; align-items: center; gap: 8px;">
                                            <i class="fa-solid fa-arrow-right-long opacity-30"></i>
                                            <span style="font-weight: 700; color: var(--accent); font-size: 14px;">{{ $finding['suggested_name'] }}</span>
                                        </div>
                                        @if($isAi)
                                            <div class="dx-v2-normalization-reason-box">
                                                <span class="dx-v2-normalization-provider-pill">{{ $provider }}</span>
                                                <span><strong>Explicación:</strong> {{ $reason ?? 'Identificado semánticamente.' }}</span>
                                            </div>
                                        @else
                                            <div style="font-size: 11px; color: var(--muted); margin-top: 4px;">
                                                El motor sugiere unificar bajo este cliente existente.
                                            </div>
                                        @endif
                                    @else
                                        <div style="font-size: 11px; color: var(--muted);">
                                            Cliente desconocido hasta ahora. Se ha creado una ficha nueva.
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 6px; margin-bottom: 4px;">
                                        @if($finding['source_type'] === 'Auditoría')
                                            <span class="badge badge-ai" style="font-size: 7px; padding: 1px 4px; background: var(--raised); color: var(--accent); border: 1px solid var(--accent-transparent);">LICENCIA</span>
                                        @else
                                            <span class="badge" style="font-size: 7px; padding: 1px 4px; background: var(--raised); color: var(--primary); border: 1px solid rgba(255,255,255,0.1);">CSV</span>
                                        @endif
                                        <div class="date-main" style="margin-bottom: 0;">{{ $finding['filename'] }}</div>
                                    </div>
                                    <div class="date-sub">{{ $finding['date']->format('d/m/Y H:i') }}</div>
                                </td>
                                <td style="text-align: right;">
                                    @if($finding['type'] === 'suspicion')
                                        <div style="display: flex; justify-content: flex-end; gap: 8px;">
                                            <form action="{{ route('admin.normalization.unify') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="detected_name" value="{{ $finding['detected_name'] }}">
                                                <input type="hidden" name="suggested_name" value="{{ $finding['suggested_name'] }}">
                                                <button type="submit" class="btn-primary" style="padding: 6px 12px; font-size: 11px;">
                                                    UNIFICAR
                                                </button>
                                            </form>
                                            
                                            <form action="{{ route('admin.normalization.dismiss') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="detected_name" value="{{ $finding['detected_name'] }}">
                                                <button type="submit" class="btn-secondary" style="padding: 6px 12px; font-size: 11px; background: var(--raised);">
                                                    DESCARTAR
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 8px;">
                                            <span class="muted" style="font-size: 9px; font-weight: 700; text-transform: uppercase; opacity: 0.5;">Automatizado (Nuevo)</span>
                                            
                                            <form action="{{ route('admin.normalization.unify') }}" method="POST" style="display: flex; align-items: center; gap: 6px;">
                                                @csrf
                                                <input type="hidden" name="detected_name" value="{{ $finding['detected_name'] }}">
                                                <input type="text" 
                                                       name="suggested_name" 
                                                       list="all-clients-list" 
                                                       placeholder="Unificar con..." 
                                                       required 
                                                       style="padding: 4px 8px; font-size: 11px; background: rgba(0,0,0,0.25); border: 1px solid rgba(255,255,255,0.1); border-radius: 4px; color: var(--primary); width: 160px; outline: none;">
                                                <button type="submit" class="btn-primary" style="padding: 4px 8px; font-size: 10px; font-weight: 800; border-radius: 4px; background: linear-gradient(135deg, #007aff 0%, #0056b3 100%); border: 1px solid rgba(0,122,255,0.4); text-transform: uppercase;">
                                                    FORZAR
                                                </button>
                                            </form>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div style="padding: 60px; text-align: center;">
                    <div style="font-size: 40px; margin-bottom: 20px;">🎉</div>
                    <div style="font-weight: 700; color: var(--primary); font-size: 16px;">Todo en orden</div>
                    <p style="color: var(--muted); font-size: 13px; margin-top: 8px; max-width: 400px; margin-left: auto; margin-right: auto;">
                        No hay sospechas de duplicados pendientes de revisión por importaciones.
                    </p>
                </div>
            @endif
        </div>

        <div class="card" style="margin-top: 24px; border-left: 4px solid var(--accent);">
            <div style="padding: 20px;">
                <h3 style="font-size: 14px; font-weight: 700; color: var(--primary); margin-bottom: 8px;">¿Cómo funciona la unificación?</h3>
                <p style="font-size: 12px; color: var(--secondary); line-height: 1.6;">
                    Al pulsar <strong>Unificar</strong>, el sistema realizará las siguientes acciones de forma atómica:
                </p>
                <ul style="font-size: 12px; color: var(--secondary); margin-top: 12px; list-style-type: disc; padding-left: 20px; line-height: 1.8;">
                    <li>Registra el nombre duplicado como un alias permanente del cliente real.</li>
                    <li>Transfiere automáticamente todos los contratos, licencias y contactos del duplicado al cliente real.</li>
                    <li>Elimina el registro redundante del cliente duplicado para mantener la base de datos limpia.</li>
                    <li>Cualquier importación futura con ese nombre se mapeará directamente al cliente real de forma transparente.</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Pestaña 2: Escáner de Duplicados (IA) -->
    <div x-show="tab === 'scanner'" x-cloak x-transition>
        <div class="card">
            <div class="card-header" style="border-bottom: 1px solid var(--border); padding-bottom: 16px; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <span class="card-title">Análisis de Similitud en Base de Datos</span>
                    <p style="font-size: 11px; color: var(--muted); margin-top: 4px;">Comparación léxica de todos los clientes activos. Ordenados de forma descendente por coincidencia porcentual.</p>
                </div>
                <form id="dx-force-scan-form" action="{{ route('admin.normalization.force-scan') }}" method="POST" style="margin: 0;">
                    @csrf
                    <button type="button" @click="triggerScan()" class="btn-primary" style="padding: 8px 16px; font-size: 11px; display: flex; align-items: center; gap: 8px; border: none; cursor: pointer; border-radius: 4px;">
                        <i class="fa-solid fa-arrows-rotate"></i>
                        Escanear Ahora
                    </button>
                </form>
            </div>
            
            @if(count($scannedDuplicates) > 0)
                <div class="dx-dup-grid">
                    @foreach($scannedDuplicates as $dup)
                        <div class="dx-dup-card" x-data="{ 
                            loading: false, 
                            result: null, 
                            error: null,
                            runAiCheck() {
                                this.loading = true;
                                this.result = null;
                                this.error = null;
                                fetch('{{ route('admin.normalization.analyze-ai') }}', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    },
                                    body: JSON.stringify({
                                        client1: '{{ $dup['duplicate']->name }}',
                                        client2: '{{ $dup['target']->name }}'
                                    })
                                })
                                .then(res => {
                                    if (!res.ok) throw new Error('Error en el servidor de IA.');
                                    return res.json();
                                })
                                .then(data => {
                                    this.result = data;
                                })
                                .catch(err => {
                                    this.error = err.message;
                                })
                                .finally(() => {
                                    this.loading = false;
                                });
                            }
                        }">
                            <!-- Cabecera -->
                            <div class="dx-dup-header">
                                <span class="dx-dup-header-title">
                                    <i class="fa-solid fa-circle-nodes" style="color: var(--accent);"></i>
                                    Pareja Sospechosa
                                </span>
                                <span class="dx-similarity-badge {{ $dup['similarity'] >= 85 ? 'high' : 'medium' }}">
                                    <i class="fa-solid fa-chart-simple"></i>
                                    {{ $dup['similarity'] }}% Similitud
                                </span>
                            </div>

                            <!-- Comparación -->
                            <div class="dx-dup-comparison">
                                <div class="dx-dup-client-block">
                                    <div class="dx-dup-client-role">Duplicado (Origen)</div>
                                    <div class="dx-dup-client-name" title="{{ $dup['duplicate']->name }}">{{ $dup['duplicate']->name }}</div>
                                    <div class="dx-dup-client-id">ID: {{ $dup['duplicate']->id }} · Creado {{ $dup['duplicate']->created_at->format('d/m/Y') }}</div>
                                </div>
                                <div class="dx-dup-arrow">
                                    <i class="fa-solid fa-arrow-right-long"></i>
                                </div>
                                <div class="dx-dup-client-block">
                                    <div class="dx-dup-client-role" style="color: var(--accent);">Real (Destino)</div>
                                    <div class="dx-dup-client-name" title="{{ $dup['target']->name }}">{{ $dup['target']->name }}</div>
                                    <div class="dx-dup-client-id">ID: {{ $dup['target']->id }} · Creado {{ $dup['target']->created_at->format('d/m/Y') }}</div>
                                </div>
                            </div>

                            <!-- Spinner de Carga AJAX -->
                            <div class="dx-ia-spinner-container" x-show="loading" x-cloak>
                                <div class="dx-ia-spinner"></div>
                                <span>Consultando orquestador de IA multi-proveedor...</span>
                            </div>

                            <!-- Error AJAX -->
                            <div class="badge badge-danger" x-show="error" x-cloak style="font-size: 11px; padding: 8px 12px; border-radius: 4px; margin-top: 4px;">
                                <i class="fa-solid fa-triangle-exclamation" style="margin-right: 6px;"></i>
                                <span x-text="error"></span>
                            </div>

                            <!-- Panel Resultados de IA -->
                            <div class="dx-ia-result-panel" x-show="result" x-cloak>
                                <div class="dx-ia-result-header">
                                    <span class="dx-ia-result-title">
                                        <i class="fa-solid fa-robot"></i>
                                        <span x-text="(result.matched || result.is_duplicate) ? '🤖 CONCORDANCIA RECOMENDADA' : '❌ DESACUERDO SEMÁNTICO'"></span>
                                    </span>
                                    <span class="dx-ia-confidence-pill" x-text="Math.round(result.confidence * 100) + '% Confianza'"></span>
                                </div>
                                <p class="dx-ia-explanation" x-text="result.reason"></p>
                                <div style="display: flex; gap: 8px; align-items: center; margin-top: 4px;">
                                    <span class="dx-ia-provider-tag" x-text="result.provider"></span>
                                    <template x-if="result.matched || result.is_duplicate">
                                        <span style="font-size: 9px; color: var(--dx-v2-success-base); font-weight: bold; text-transform: uppercase;">
                                            <i class="fa-solid fa-circle-check"></i> Fusión aconsejada
                                        </span>
                                    </template>
                                </div>
                            </div>

                            <!-- Acciones -->
                            <div class="dx-dup-actions">
                                <button type="button" class="dx-btn-ia-check" @click="runAiCheck()" x-show="!loading && !result">
                                    <i class="fa-solid fa-brain"></i>
                                    Verificar con IA
                                </button>
                                
                                <form action="{{ route('admin.normalization.unify') }}" method="POST" style="margin: 0;">
                                    @csrf
                                    <input type="hidden" name="detected_name" value="{{ $dup['duplicate']->name }} (ID: {{ $dup['duplicate']->id }})">
                                    <input type="hidden" name="suggested_name" value="{{ $dup['target']->name }} (ID: {{ $dup['target']->id }})">
                                    <button type="submit" class="btn-primary" style="padding: 6px 12px; font-size: 11px;">
                                        UNIFICAR
                                    </button>
                                </form>

                                <form action="{{ route('admin.normalization.dismiss') }}" method="POST" style="margin: 0;">
                                    @csrf
                                    <input type="hidden" name="detected_name" value="{{ $dup['duplicate']->name }}">
                                    <button type="submit" class="btn-secondary" style="padding: 6px 12px; font-size: 11px; background: var(--raised);">
                                        IGNORAR
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div style="padding: 60px; text-align: center;">
                    <div style="font-size: 40px; margin-bottom: 20px;">🛡️</div>
                    <div style="font-weight: 700; color: var(--primary); font-size: 16px;">Base de datos impecable</div>
                    <p style="color: var(--muted); font-size: 13px; margin-top: 8px; max-width: 420px; margin-left: auto; margin-right: auto;">
                        El algoritmo de similitud léxica no ha encontrado nombres de clientes redundantes o sospechosos en el repositorio actual.
                    </p>
                </div>
            @endif
        </div>
    </div>

    <!-- Pestaña 3: Unificación Manual Libre -->
    <div x-show="tab === 'manual'" x-cloak x-transition>
        <div class="card" style="border-left: 4px solid var(--accent);">
            <div class="card-header" style="display: flex; justify-between: space-between; align-items: center; border-bottom: 1px solid var(--border); padding-bottom: 16px;">
                <span class="card-title" style="display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-compress" style="color: var(--accent); font-size: 14px;"></i>
                    Fusión Forzada de Cuentas
                </span>
                <span class="badge badge-neutral" style="font-size: 9px; font-weight: 700; text-transform: uppercase; padding: 2px 6px;">Herramienta Avanzada</span>
            </div>
            <div style="padding: 20px;">
                <p style="font-size: 12px; color: var(--muted); margin-bottom: 20px; line-height: 1.5;">
                    Permite fusionar manualmente dos clientes existentes de la base de datos que no hayan sido vinculados por el motor automático (ej: <em>Tag Automotive</em>).
                </p>
                <form action="{{ route('admin.normalization.unify') }}" method="POST" style="display: flex; flex-wrap: wrap; gap: 16px; align-items: flex-end;">
                    @csrf
                    <div style="flex: 1; min-width: 240px;">
                        <label style="display: block; font-size: 10px; font-weight: 700; color: var(--muted); margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.05em;">Cliente Duplicado (Origen / Se eliminará)</label>
                        <input type="text" 
                               name="detected_name" 
                               list="all-clients-list" 
                               placeholder="Ej: Tag Automotive (Nifco...)" 
                               required 
                               style="width: 100%; padding: 10px 14px; font-size: 12px; background: rgba(0,0,0,0.25); border: 1px solid var(--border); border-radius: 6px; color: var(--primary); outline: none; transition: border-color 0.2s;">
                    </div>
                    <div style="display: flex; align-items: center; justify-content: center; height: 42px; padding-bottom: 8px;">
                        <i class="fa-solid fa-right-long opacity-40" style="font-size: 14px; color: var(--muted);"></i>
                    </div>
                    <div style="flex: 1; min-width: 240px;">
                        <label style="display: block; font-size: 10px; font-weight: 700; color: var(--muted); margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.05em;">Cliente Real (Destino / Se conservará)</label>
                        <input type="text" 
                               name="suggested_name" 
                               list="all-clients-list" 
                               placeholder="Ej: Tag Automotive S.L. (ID: 123)" 
                               required 
                               style="width: 100%; padding: 10px 14px; font-size: 12px; background: rgba(0,0,0,0.25); border: 1px solid var(--border); border-radius: 6px; color: var(--primary); outline: none; transition: border-color 0.2s;">
                    </div>
                    <div>
                        <button type="submit" class="btn-primary" style="padding: 10px 24px; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; border-radius: 6px; height: 42px; display: flex; align-items: center; gap: 8px; cursor: pointer; border: none;">
                            <i class="fa-solid fa-compress"></i>
                            Unificar Atómicamente
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal de Carga de Escaneo Completo -->
    <div x-show="scanning" 
         x-cloak 
         style="position: fixed !important; top: 0 !important; left: 0 !important; width: 100vw !important; height: 100vh !important; background: rgba(10, 11, 18, 0.95) !important; backdrop-filter: blur(12px) !important; display: flex !important; align-items: center !important; justify-content: center !important; z-index: 999999 !important; animation: dxFadeIn 0.3s ease-out;"
         class="dx-scan-modal-overlay">
        <div style="background: #111320; border: 1px solid rgba(145, 113, 255, 0.2); border-radius: 12px; padding: 40px; text-align: center; width: 100%; max-width: 450px; box-shadow: 0 20px 50px rgba(0,0,0,0.85);"
             class="dx-scan-modal-content">
            <div style="position: relative; width: 80px; height: 80px; margin: 0 auto 24px auto;">
                <!-- Glowing loader -->
                <div style="position: absolute; inset: 0; border: 4px solid rgba(145, 113, 255, 0.1); border-top-color: #a78bfa; border-radius: 50%; animation: dxSpin 1.2s cubic-bezier(0.5, 0, 0.5, 1) infinite;"></div>
                <i class="fa-solid fa-brain" style="font-size: 32px; color: #a78bfa; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);"></i>
            </div>
            <h3 style="font-size: 16px; font-weight: 800; color: #fff; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 8px;">Escaneo de Base de Datos</h3>
            <p style="font-size: 12px; color: var(--dx-v2-text-muted); opacity: 0.8; margin-bottom: 24px;">Analizando la base de clientes en tiempo real...</p>
            
            <div style="background: rgba(0,0,0,0.3); border: 1px solid var(--dx-v2-border-base); border-radius: 8px; padding: 14px 16px; min-height: 48px; display: flex; align-items: center; justify-content: center;">
                <span x-text="scanningText" style="font-family: var(--font-mono); font-size: 11px; color: #a78bfa; text-align: center; line-height: 1.4;"></span>
            </div>
        </div>
    </div>
</div>

<datalist id="all-clients-list">
    @foreach($allClients as $client)
        <option value="{{ $client->name }} (ID: {{ $client->id }})">
    @endforeach
</datalist>
@endsection
