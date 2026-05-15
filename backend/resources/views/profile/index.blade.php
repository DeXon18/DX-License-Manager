@extends('layouts.app')

@section('title', 'Mi Perfil — DX License Manager')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Configuración de Perfil</h1>
        <p class="page-sub">Gestiona tus datos personales y credenciales de acceso.</p>
    </div>
</div>

<div style="max-width: 650px;">
    @if(session('success'))
        <div class="badge badge-success mb-4 w-100 p-3" style="justify-content: flex-start;">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        </div>
    @endif

    <div class="card" style="--accent: var(--accent);">
        <div class="card-header">
            <span class="card-title">Datos Personales</span>
        </div>
        
        <div style="padding: 32px;">
            <form action="{{ route('profile.update') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="name">NOMBRE COMPLETO</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required>
                    @error('name') <p class="date-sub" style="margin-top: 4px;">{{ $message }}</p> @enderror
                </div>

                <div class="form-group">
                    <label for="email">EMAIL INSTITUCIONAL</label>
                    <input type="email" name="email" id="email" class="font-mono" value="{{ old('email', $user->email) }}" required>
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

                    <div class="form-group">
                        <label for="current_password" style="font-size: 11px;">CONTRASEÑA ACTUAL</label>
                        <input type="password" name="current_password" id="current_password" class="font-mono" style="padding: 10px 14px;">
                        @error('current_password') <p class="date-sub" style="margin-top: 4px;">{{ $message }}</p> @enderror
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div class="form-group" style="margin-bottom: 0;">
                            <label for="new_password" style="font-size: 11px;">NUEVA CONTRASEÑA</label>
                            <input type="password" name="new_password" id="new_password" class="font-mono" style="padding: 10px 14px;">
                            @error('new_password') <p class="date-sub" style="margin-top: 4px;">{{ $message }}</p> @enderror
                        </div>
                        <div class="form-group" style="margin-bottom: 0;">
                            <label for="new_password_confirmation" style="font-size: 11px;">REPETIR NUEVA</label>
                            <input type="password" name="new_password_confirmation" id="new_password_confirmation" class="font-mono" style="padding: 10px 14px;">
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
