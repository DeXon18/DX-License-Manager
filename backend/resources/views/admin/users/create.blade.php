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

                <div class="dx-v2-form-group">
                    <label for="name" class="dx-v2-form-label">NOMBRE COMPLETO</label>
                    <input type="text" name="name" id="name" class="dx-v2-form-input" value="{{ old('name') }}" placeholder="Ej: Juan Pérez" required autofocus>
                    @error('name') <p class="date-sub">{{ $message }}</p> @enderror
                </div>

                <div class="dx-v2-form-group">
                    <label for="email" class="dx-v2-form-label">EMAIL INSTITUCIONAL</label>
                    <input type="email" name="email" id="email" class="dx-v2-form-input font-mono" value="{{ old('email') }}" placeholder="usuario@dxpro.es" required>
                    @error('email') <p class="date-sub">{{ $message }}</p> @enderror
                </div>

                <div class="dx-v2-users-form-grid">
                    <div class="dx-v2-form-group">
                        <label for="password" class="dx-v2-form-label">CONTRASEÑA (OPCIONAL)</label>
                        <input type="password" name="password" id="password" class="dx-v2-form-input font-mono" placeholder="Vacío = Aleatoria">
                        @error('password') <p class="date-sub">{{ $message }}</p> @enderror
                    </div>
                    <div class="dx-v2-form-group">
                        <label for="password_confirmation" class="dx-v2-form-label">CONFIRMAR</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="dx-v2-form-input font-mono" placeholder="Opcional">
                    </div>
                </div>

                <div class="dx-v2-form-group">
                    <label for="role_id" class="dx-v2-form-label">ROL EN EL SISTEMA</label>
                    <select name="role_id" id="role_id" class="dx-v2-form-select" required>
                        <option value="" disabled selected>Selecciona un rol...</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                {{ ucfirst($role->name) }}
                            </option>
                        @endforeach
                    </select>
                    @error('role_id') <p class="date-sub">{{ $message }}</p> @enderror
                </div>

                <div class="dx-v2-users-security-box" style="margin-top: 24px;">
                    <div class="dx-v2-users-security-title" style="color: var(--primary);">
                        <i class="fas fa-sliders-h me-2"></i>Permisos Especiales (Individuales)
                    </div>
                    <p class="dx-v2-users-security-desc" style="margin-top: 0; margin-bottom: 16px;">
                        Selecciona permisos adicionales para este usuario. Estos se sumarán a los que ya le otorga su rol principal.
                    </p>
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 12px;">
                        @foreach($permissions as $permission)
                            <label style="display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--text-dark); cursor: pointer; padding: 6px; border-radius: 4px; transition: background 0.2s;" onmouseover="this.style.background='var(--surface-hover)'" onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="permissions[]" value="{{ $permission->name }}" {{ is_array(old('permissions')) && in_array($permission->name, old('permissions')) ? 'checked' : '' }} style="accent-color: var(--primary); width: 16px; height: 16px;">
                                <span>{{ ucwords(str_replace('_', ' ', $permission->name)) }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="dx-v2-form-group" x-data="{ active: true }" style="margin-top: 24px;">
                    <div class="dx-v2-users-switch-box">
                        <div>
                            <div class="dx-v2-users-switch-title">Usuario Activo</div>
                            <div class="dx-v2-users-switch-info">Permite el inicio de sesión inmediato</div>
                        </div>
                        <input type="hidden" name="is_active" :value="active ? 1 : 0">
                        <button type="button" 
                                class="dx-v2-form-switch" 
                                :class="active ? 'active' : ''"
                                @click="active = !active">
                            <span class="dx-v2-form-switch-dot"></span>
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
