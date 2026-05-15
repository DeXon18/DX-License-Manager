@extends('layouts.app')

@section('title', 'Nuevo Usuario — DX License Manager')

@section('content')
<div class="page-header">
    <div style="display: flex; align-items: center; gap: 16px;">
        <a href="{{ route('admin.users.index') }}" class="btn-secondary" style="padding: 8px 12px;">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="page-title">Nuevo Usuario</h1>
            <p class="page-sub">Crea una nueva cuenta de acceso al portal.</p>
        </div>
    </div>
</div>

<div style="max-width: 600px;">
    <div class="card" style="--accent: var(--accent);">
        <div class="card-header">
            <span class="card-title">Configuración de Perfil</span>
        </div>
        
        <div style="padding: 32px;">
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="name">NOMBRE COMPLETO</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" placeholder="Ej: Juan Pérez" required autofocus>
                    @error('name') <p class="date-sub" style="margin-top: 4px;">{{ $message }}</p> @enderror
                </div>

                <div class="form-group">
                    <label for="email">EMAIL INSTITUCIONAL</label>
                    <input type="email" name="email" id="email" class="font-mono" value="{{ old('email') }}" placeholder="usuario@dxpro.es" required>
                    @error('email') <p class="date-sub" style="margin-top: 4px;">{{ $message }}</p> @enderror
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label for="password">CONTRASEÑA (OPCIONAL)</label>
                        <input type="password" name="password" id="password" class="font-mono" placeholder="Vacío = Aleatoria">
                        @error('password') <p class="date-sub" style="margin-top: 4px;">{{ $message }}</p> @enderror
                    </div>
                    <div class="form-group">
                        <label for="password_confirmation">CONFIRMAR</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="font-mono" placeholder="Opcional">
                    </div>
                </div>

                <div class="form-group">
                    <label for="role_id">ROL EN EL SISTEMA</label>
                    <select name="role_id" id="role_id" class="gui-select" style="width: 100%; padding: 12px; background: var(--bg); border: 1px solid var(--border); border-radius: 8px; color: var(--primary); font-family: var(--font-sans);" required>
                        <option value="" disabled selected>Selecciona un rol...</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                {{ $role->name }} — {{ $role->description }}
                            </option>
                        @endforeach
                    </select>
                    @error('role_id') <p class="date-sub" style="margin-top: 4px;">{{ $message }}</p> @enderror
                </div>

                <div class="form-group" x-data="{ active: true }">
                    <div style="display: flex; align-items: center; justify-content: space-between; padding: 16px; background: var(--bg); border: 1px solid var(--border); border-radius: 8px;">
                        <div>
                            <div style="font-weight: 600; font-size: 13px; color: var(--primary);">Usuario Activo</div>
                            <div style="font-size: 11px; color: var(--muted);">Permite el inicio de sesión inmediato</div>
                        </div>
                        <input type="hidden" name="is_active" :value="active ? 1 : 0">
                        <button type="button" 
                                class="switch" 
                                :class="active ? 'on' : 'off'"
                                @click="active = !active">
                        </button>
                    </div>
                </div>

                <div style="margin-top: 32px;">
                    <button type="submit" class="btn-primary" style="width: 100%; padding: 14px;">
                        <i class="fas fa-save me-2"></i> Crear Usuario
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <div class="no-ai-banner" style="margin-top: 24px; border-left-color: var(--success);">
        <i class="fas fa-paper-plane" style="color: var(--success); margin-right: 12px; font-size: 14px;"></i>
        <div>
            El sistema enviará automáticamente un email de bienvenida con las credenciales de acceso al finalizar el alta.
        </div>
    </div>
</div>
@endsection
