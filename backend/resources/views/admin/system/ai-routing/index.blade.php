@extends('layouts.app')

@section('title', 'AI Routing Hub')

@section('content')
<div class="dx-v2-page-header">
    <div>
        <div class="breadcrumb">
            <a href="{{ route('admin.system.index') }}">System Control</a>
            <span class="separator">/</span>
            <span class="current">AI Routing Hub</span>
        </div>
        <h1 class="page-title">AI Routing <span>Hub</span></h1>
        <p class="page-subtitle">Centralización de OpenRouter: Enrutador de tareas, asignación primaria y fallback (Anti-Rate-Limit).</p>
    </div>
</div>

<div class="grid-main" x-data="{ activeTab: 'router' }">
    <!-- Panel Izquierdo: Contenido Principal con Pestañas -->
    <div class="main-panel">
        @if(session('success'))
            <div class="card" style="margin-bottom: 24px; border-color: var(--dx-v2-success-border); background: var(--dx-v2-success-bg);">
                <div class="card-body" style="padding: 12px 16px !important; color: var(--dx-v2-success); font-weight: 500;">
                    {{ session('success') }}
                </div>
            </div>
        @endif

        <!-- Segmented Control / Tabs Header -->
        <div style="display: flex; gap: 8px; margin-bottom: 24px; border-bottom: 1px solid var(--dx-v2-border); padding-bottom: 8px;">
            <button @click="activeTab = 'router'" 
                    class="btn-secondary" 
                    :style="activeTab === 'router' ? 'background: var(--dx-v2-accent); color: #fff !important; border-color: var(--dx-v2-accent);' : 'border: none; background: transparent;'">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 3h5v5M4 20L21 3M21 16v5h-5M15 15l6 6M4 4l5 5"/></svg>
                Enrutador de Tareas
            </button>
            <button @click="activeTab = 'catalog'" 
                    class="btn-secondary" 
                    :style="activeTab === 'catalog' ? 'background: var(--dx-v2-accent); color: #fff !important; border-color: var(--dx-v2-accent);' : 'border: none; background: transparent;'">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>
                Catálogo de Modelos IA
            </button>
        </div>

        <!-- TAB: ROUTER -->
        <div x-show="activeTab === 'router'">
            <div class="card" style="--accent: var(--dx-v2-accent-base);">
                <div class="card-header">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span class="card-title" style="color: var(--dx-v2-primary);">Configuración de Rutas</span>
                    </div>
                </div>
                <div class="card-body">
                    <p style="font-size: 13px; color: var(--dx-v2-muted); margin-top: -8px; margin-bottom: 24px; font-family: var(--dx-v2-font-sans);">
                        Mapea cada módulo del portal a un modelo de IA específico. Si el Primary da Error 429 (Rate Limit agotado), saltará automáticamente al Fallback.
                    </p>

                    <div style="display: flex; flex-direction: column; gap: 16px;">
                        @foreach($routes as $route)
                            <form action="{{ route('admin.system.ai-routing.routes.update', $route->task_name) }}" method="POST" class="card" style="background: var(--dx-v2-surface);">
                                @csrf
                                @method('PUT')
                                <div class="card-header" style="background: transparent;">
                                    <span class="card-title" style="font-size: 14px;">{{ strtoupper($route->task_name) }}</span>
                                    <button type="submit" class="btn-primary" style="padding: 6px 12px; font-size: 12px;">Guardar Ruta</button>
                                </div>
                                <div class="card-body" style="padding: 16px !important;">
                                    <div style="font-size: 12px; color: var(--dx-v2-muted); margin-bottom: 16px; font-family: var(--dx-v2-font-sans);">{{ $route->description }}</div>
                                    
                                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                                        <div class="dx-v2-form-group" style="margin-bottom: 0 !important;">
                                            <label class="dx-v2-form-label" style="color: var(--dx-v2-success) !important;">Primary Model</label>
                                            <select name="primary_model_id" class="dx-v2-form-select">
                                                @foreach($models as $m)
                                                    <option value="{{ $m->id }}" {{ $route->primary_model_id == $m->id ? 'selected' : '' }}>
                                                        {{ $m->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        
                                        <div class="dx-v2-form-group" style="margin-bottom: 0 !important;">
                                            <label class="dx-v2-form-label" style="color: var(--dx-v2-warning) !important;">Fallback Model (Anti-429)</label>
                                            <select name="fallback_model_id" class="dx-v2-form-select">
                                                <option value="">-- Ninguno (Falla si el primario cae) --</option>
                                                @foreach($models as $m)
                                                    <option value="{{ $m->id }}" {{ $route->fallback_model_id == $m->id ? 'selected' : '' }}>
                                                        {{ $m->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB: CATALOG -->
        <div x-show="activeTab === 'catalog'" style="display: none;">
            <div class="card">
                <div class="card-header">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span class="card-title">Listado de Modelos</span>
                    </div>
                </div>
                
                <div style="overflow-x: auto;">
                    <table>
                        <thead>
                            <tr>
                                <th>Estado</th>
                                <th>Modelo</th>
                                <th>OpenRouter ID</th>
                                <th>Tipo</th>
                                <th style="text-align: right;">Cuota Semanal</th>
                                <th style="text-align: right;">P. Prompt / Comp.</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($models as $m)
                                <tr style="opacity: {{ $m->is_active ? '1' : '0.5' }};">
                                    <td style="width: 50px; text-align: center;">
                                        <form action="{{ route('admin.system.ai-routing.models.toggle', $m->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" style="background: none; border: none; cursor: pointer; color: {{ $m->is_active ? 'var(--dx-v2-success)' : 'var(--dx-v2-muted)' }};">
                                                @if($m->is_active)
                                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                                @else
                                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                                                @endif
                                            </button>
                                        </form>
                                    </td>
                                    <td><strong style="color: var(--dx-v2-primary);">{{ $m->name }}</strong></td>
                                    <td style="font-family: var(--dx-v2-font-mono, monospace); font-size: 12px; color: var(--dx-v2-muted);">{{ $m->openrouter_id }}</td>
                                    <td>
                                        @if($m->is_free)
                                            <span style="background: var(--dx-v2-success-bg); color: var(--dx-v2-success); border: 1px solid var(--dx-v2-success-border); padding: 2px 6px; border-radius: 4px; font-size: 10px; font-weight: 700;">FREE</span>
                                        @else
                                            <span style="background: var(--dx-v2-warning-bg); color: var(--dx-v2-warning); border: 1px solid var(--dx-v2-warning-border); padding: 2px 6px; border-radius: 4px; font-size: 10px; font-weight: 700;">PRO</span>
                                        @endif
                                    </td>
                                    <td style="text-align: right;">
                                        @if($m->weekly_tokens_limit)
                                            @php
                                                $limit = $m->weekly_tokens_limit;
                                                $usage = $m->weekly_usage ?? 0;
                                                $percent = $limit > 0 ? min(100, round(($usage / $limit) * 100)) : 0;
                                                
                                                // Formateo del límite
                                                $limitStr = $limit >= 1000000000000 ? round($limit/1000000000000, 2) . 'T' : 
                                                            ($limit >= 1000000000 ? round($limit/1000000000, 1) . 'B' : 
                                                            round($limit/1000000, 1) . 'M');
                                                
                                                // Formateo del uso
                                                $usageStr = $usage >= 1000000000000 ? round($usage/1000000000000, 2) . 'T' : 
                                                            ($usage >= 1000000000 ? round($usage/1000000000, 1) . 'B' : 
                                                            ($usage >= 1000000 ? round($usage/1000000, 1) . 'M' : number_format($usage, 0, ',', '.')));
                                                            
                                                $barColor = $percent > 90 ? 'var(--dx-v2-danger)' : ($percent > 75 ? 'var(--dx-v2-warning)' : 'var(--dx-v2-success)');
                                            @endphp
                                            <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 4px;">
                                                <div style="font-size: 11px; font-family: var(--dx-v2-font-mono, monospace); color: var(--dx-v2-muted);">
                                                    <strong style="color: var(--dx-v2-primary);">{{ $usageStr }}</strong> / {{ $limitStr }}
                                                </div>
                                                <div style="width: 100px; height: 4px; background: var(--dx-v2-border); border-radius: 2px; overflow: hidden;">
                                                    <div style="width: {{ $percent }}%; height: 100%; background: {{ $barColor }};"></div>
                                                </div>
                                            </div>
                                        @else
                                            <span style="font-size: 11px; color: var(--dx-v2-muted);">Ilimitada</span>
                                        @endif
                                    </td>
                                    <td style="text-align: right; font-family: var(--dx-v2-font-mono, monospace); font-size: 12px; color: var(--dx-v2-muted);">
                                        ${{ rtrim(rtrim(number_format($m->price_prompt, 6), '0'), '.') }} / ${{ rtrim(rtrim(number_format($m->price_completion, 6), '0'), '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Panel Derecho: Modelos Disponibles -->
    <div class="sidebar-panel">
        <div class="card">
            <div class="card-header">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="accent-color"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                    <span class="card-title">Añadir Modelo</span>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.system.ai-routing.models.store') }}" method="POST">
                    @csrf
                    <div class="dx-v2-form-group">
                        <label class="dx-v2-form-label">OpenRouter ID</label>
                        <input type="text" name="openrouter_id" class="dx-v2-form-input" placeholder="Ej. deepseek/deepseek-v4-flash" required>
                    </div>
                    <div class="dx-v2-form-group">
                        <label class="dx-v2-form-label">Nombre Amigable</label>
                        <input type="text" name="name" class="dx-v2-form-input" placeholder="Ej. DeepSeek V4 Flash" required>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                        <div class="dx-v2-form-group">
                            <label class="dx-v2-form-label">P. Prompt</label>
                            <input type="number" step="0.000001" name="price_prompt" class="dx-v2-form-input" value="0" required>
                        </div>
                        <div class="dx-v2-form-group">
                            <label class="dx-v2-form-label">P. Completion</label>
                            <input type="number" step="0.000001" name="price_completion" class="dx-v2-form-input" value="0" required>
                        </div>
                    </div>
                    <div class="dx-v2-form-group" style="margin-top: 8px;">
                        <label class="dx-v2-form-checkbox-wrapper">
                            <input type="checkbox" name="is_free" value="1" id="is_free" class="dx-v2-form-checkbox" checked>
                            <span style="font-family: var(--dx-v2-font-sans); font-size: 13px; color: var(--dx-v2-secondary);">Es un modelo gratuito (:free)</span>
                        </label>
                    </div>
                    <button type="submit" class="btn-primary" style="width: 100%; margin-top: 16px;">Guardar Modelo</button>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection
