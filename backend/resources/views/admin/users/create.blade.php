@extends('layouts.app')

@section('title', 'Nuevo Usuario — DX License Manager')

@section('content')
<div class="page-header">
    <div class="dx-v2-users-breadcrumb-wrapper">
        <a href="{{ route('admin.users.index') }}" class="dx-v2-users-breadcrumb-link">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="page-title">
                <i class="fas fa-user-plus dx-v2-users-title-icon"></i> Nuevo Usuario
            </h1>
            <p class="dx-v2-users-subtitle">Crea una nueva cuenta de acceso al portal.</p>
        </div>
    </div>
</div>

<div class="dx-v2-users-form-container">
    <div class="card">
        <div class="card-header">
            <span class="card-title">Configuración de Perfil</span>
        </div>
        
        <div class="dx-v2-users-form-body">
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="name">NOMBRE COMPLETO</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" placeholder="Ej: Juan Pérez" required autofocus>
                    @error('name') <p class="date-sub">{{ $message }}</p> @enderror
                </div>

                <div class="form-group">
                    <label for="email">EMAIL INSTITUCIONAL</label>
                    <input type="email" name="email" id="email" class="font-mono" value="{{ old('email') }}" placeholder="usuario@dxpro.es" required>
                    @error('email') <p class="date-sub">{{ $message }}</p> @enderror
                </div>

                <div class="dx-v2-users-form-grid">
                    <div class="form-group">
                        <label for="password">CONTRASEÑA (OPCIONAL)</label>
                        <input type="password" name="password" id="password" class="font-mono" placeholder="Vacío = Aleatoria">
                        @error('password') <p class="date-sub">{{ $message }}</p> @enderror
                    </div>
                    <div class="form-group">
                        <label for="password_confirmation">CONFIRMAR</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="font-mono" placeholder="Opcional">
                    </div>
                </div>

                <div class="form-group">
                    <label for="role_id">ROL EN EL SISTEMA</label>
                    <select name="role_id" id="role_id" class="dx-v2-users-select" required>
                        <option value="" disabled selected>Selecciona un rol...</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                {{ $role->name }} — {{ $role->description }}
                            </option>
                        @endforeach
                    </select>
                    @error('role_id') <p class="date-sub">{{ $message }}</p> @enderror
                </div>

                <div class="form-group" x-data="{ active: true }">
                    <div class="dx-v2-users-switch-box">
                        <div>
                            <div class="dx-v2-users-switch-title">Usuario Activo</div>
                            <div class="dx-v2-users-switch-info">Permite el inicio de sesión inmediato</div>
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
                    <button type="submit" class="btn-primary dx-v2-users-btn-submit">
                        <i class="fas fa-save me-2"></i> Crear Usuario
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <div class="dx-v2-users-banner">
        <i class="fas fa-paper-plane dx-v2-users-banner-icon"></i>
        <div>
            El sistema enviará automáticamente un email de bienvenida con las credenciales de acceso al finalizar el alta.
        </div>
    </div>
</div>
@endsection
