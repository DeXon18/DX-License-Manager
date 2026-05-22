@extends('layouts.app')
@section('title', 'Soporte IT')

@section('content')
<div class="page-header">
    <div class="header-actions">
        <div>
            <h1 class="page-title"><i class="fa-solid fa-headset" style="color: var(--dx-v2-accent-base, #388bfd); margin-right: 12px;"></i> Contactar Soporte IT</h1>
            <p class="page-sub">Envía un mensaje directo al equipo de soporte para resolver incidencias o reportar errores en el portal.</p>
        </div>
    </div>
</div>

<div class="card" style="max-width: 800px;">
    <div class="card-body">
        @if(session('success'))
            <div class="dx-v2-alert dx-v2-alert-success dx-mb-16">
                <i class="fa-solid fa-check-circle"></i>
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="dx-v2-alert dx-v2-alert-danger dx-mb-16">
                <i class="fa-solid fa-circle-exclamation"></i>
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('support.send') }}" method="POST">
            @csrf
            <div class="dx-v2-form-group">
                <label class="dx-v2-form-label">Asunto</label>
                <input type="text" name="subject" class="dx-v2-form-input" required maxlength="100" placeholder="Ej: Problema al unificar clientes" value="{{ old('subject') }}">
                @error('subject')
                    <div style="color: var(--dx-v2-danger, #e05252); font-size: 0.85rem; margin-top: 0.25rem;">{{ $message }}</div>
                @enderror
            </div>

            <div class="dx-v2-form-group">
                <label class="dx-v2-form-label">Mensaje</label>
                <textarea name="message" class="dx-v2-form-textarea" required rows="6" maxlength="2000" placeholder="Describe tu incidencia o consulta con el mayor detalle posible...">{{ old('message') }}</textarea>
                @error('message')
                    <div style="color: var(--dx-v2-danger, #e05252); font-size: 0.85rem; margin-top: 0.25rem;">{{ $message }}</div>
                @enderror
            </div>

            <div style="margin-top: 1.5rem; display: flex; justify-content: flex-end;">
                <button type="submit" class="btn-primary">
                    <i class="fa-regular fa-paper-plane"></i> Enviar Mensaje a Oskar
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
