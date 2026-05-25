@extends('layouts.app')

@section('title', 'Mi Perfil — DX License Manager')

@section('content')
<div class="dx-v2-page-header">
    <div>
        <div class="breadcrumb">
            <a href="{{ route('profile.index') }}">Usuario</a>
            <span class="separator">/</span>
            <span class="current">Perfil</span>
        </div>
        <h1 class="page-title">Configuración de <span>Perfil</span></h1>
        <p class="page-subtitle">Gestiona tus datos personales y credenciales de acceso.</p>
    </div>
</div>

<div style="max-width: 650px;">

    <div class="card dx-v2-profile-card" style="--accent: var(--accent);">
        <div class="card-header">
            <span class="card-title">Datos Personales</span>
        </div>
        
        <div style="padding: 32px;">
            <form action="{{ route('profile.update') }}" method="POST">
                @csrf

                <div class="dx-v2-form-group">
                    <label for="name" class="dx-v2-form-label">NOMBRE COMPLETO</label>
                    <input type="text" name="name" id="name" class="dx-v2-form-input" value="{{ old('name', $user->name) }}" required>
                    @error('name') <p class="date-sub" style="margin-top: 4px;">{{ $message }}</p> @enderror
                </div>

                <div class="dx-v2-form-group">
                    <label for="email" class="dx-v2-form-label">EMAIL INSTITUCIONAL</label>
                    <input type="email" name="email" id="email" class="dx-v2-form-input font-mono" value="{{ old('email', $user->email) }}" required>
                    @error('email') <p class="date-sub" style="margin-top: 4px;">{{ $message }}</p> @enderror
                </div>

                <div class="form-group">
                    <label>ROL ASIGNADO</label>
                    <div style="padding: 12px; background: var(--bg); border: 1px solid var(--border); border-radius: 8px; color: var(--muted); font-size: 14px;">
                        <span class="badge 
                            @if($user->isAdmin()) badge-danger 
                            @elseif($user->isTechnician()) badge-primary
                            @elseif($user->isStaff()) badge-info
                            @else badge-muted @endif" style="margin-right: 8px;">
                            {{ $user->role->name }}
                        </span>
                        {{ $user->role->description }}
                    </div>
                </div>

                <div style="padding: 24px; background: var(--bg); border: 1px solid var(--border); border-radius: 12px; margin-top: 40px; border-left: 4px solid var(--accent);">
                    <div style="font-weight: 600; font-size: 12px; color: var(--primary); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 20px;">
                        <i class="fas fa-shield-alt me-2"></i>Seguridad y Contraseña
                    </div>

                    <div class="dx-v2-form-group">
                        <label for="current_password" class="dx-v2-form-label">CONTRASEÑA ACTUAL</label>
                        <input type="password" name="current_password" id="current_password" class="dx-v2-form-input font-mono">
                        @error('current_password') <p class="date-sub" style="margin-top: 4px;">{{ $message }}</p> @enderror
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div class="dx-v2-form-group" style="margin-bottom: 0;">
                            <label for="new_password" class="dx-v2-form-label">NUEVA CONTRASEÑA</label>
                            <input type="password" name="new_password" id="new_password" class="dx-v2-form-input font-mono">
                            @error('new_password') <p class="date-sub" style="margin-top: 4px;">{{ $message }}</p> @enderror
                        </div>
                        <div class="dx-v2-form-group" style="margin-bottom: 0;">
                            <label for="new_password_confirmation" class="dx-v2-form-label">REPETIR NUEVA</label>
                            <input type="password" name="new_password_confirmation" id="new_password_confirmation" class="dx-v2-form-input font-mono">
                        </div>
                    </div>
                    <p style="font-size: 11px; color: var(--muted); margin-top: 16px; font-style: italic;">
                        Completa estos campos solo si deseas cambiar tu clave actual.
                    </p>
                </div>

                <div style="margin-top: 40px; display: flex; justify-content: flex-end;">
                    <button type="submit" class="btn-primary" style="padding: 12px 32px;">
                        <i class="fas fa-save me-2"></i> Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
