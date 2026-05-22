@extends('layouts.app')
@section('title', 'Soporte IT')

@section('content')
<div class="dx-page-header" style="max-width: 600px; margin: 0 auto; padding-top: 2rem;">
    <div class="dx-page-title">
        <i class="fa-solid fa-headset dx-title-icon" style="color: var(--primary-color);"></i>
        <h1>Contactar Soporte IT</h1>
    </div>
    <p class="dx-page-desc">Envía un mensaje directo al equipo de soporte para resolver incidencias o reportar errores en el portal.</p>
</div>

<div class="dx-card dx-p-24" style="max-width: 600px; margin: 0 auto; margin-top: 2rem;">
    @if(session('success'))
        <div class="dx-alert dx-alert-success dx-mb-16">
            <i class="fa-solid fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="dx-alert dx-alert-danger dx-mb-16">
            <i class="fa-solid fa-circle-exclamation"></i>
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('support.send') }}" method="POST" class="dx-form">
        @csrf
        <div class="dx-form-group">
            <label class="dx-label">Asunto</label>
            <input type="text" name="subject" class="dx-input" required maxlength="100" placeholder="Ej: Problema al unificar clientes" value="{{ old('subject') }}">
            @error('subject')
                <div class="dx-error-text" style="color: var(--danger-color); font-size: 0.85rem; margin-top: 0.25rem;">{{ $message }}</div>
            @enderror
        </div>

        <div class="dx-form-group">
            <label class="dx-label">Mensaje</label>
            <textarea name="message" class="dx-input" required rows="6" maxlength="2000" placeholder="Describe tu incidencia o consulta con el mayor detalle posible...">{{ old('message') }}</textarea>
            @error('message')
                <div class="dx-error-text" style="color: var(--danger-color); font-size: 0.85rem; margin-top: 0.25rem;">{{ $message }}</div>
            @enderror
        </div>

        <div class="dx-form-actions" style="margin-top: 1.5rem; display: flex; justify-content: flex-end;">
            <button type="submit" class="dx-btn dx-btn-primary">
                <i class="fa-regular fa-paper-plane" style="margin-right: 8px;"></i> Enviar Mensaje a Oskar
            </button>
        </div>
    </form>
</div>
@endsection
