@extends('layouts.app')

@section('title', 'Editar Usuario — DX License Manager')

@section('content')
<div class="page-header">
    <div class="dx-v2-users-breadcrumb-wrapper">
        <a href="{{ route('admin.users.index') }}" class="dx-v2-users-breadcrumb-link">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="page-title">
                <i class="fas fa-user-edit dx-v2-users-title-icon"></i> Editar Usuario
            </h1>
            <p class="dx-v2-users-subtitle">Modifica los permisos y datos de {{ $user->name }}.</p>
        </div>
    </div>
</div>

<div style="max-width: 1400px; margin: 0 auto;">
    <form action="{{ route('admin.users.update', $user) }}" method="POST">
        @csrf
        @method('PUT')

        <div style="display: grid; grid-template-columns: 380px 1fr; gap: 24px; align-items: start;">
            
            <!-- COLUMNA IZQUIERDA: Perfil y Seguridad -->
            <div style="display: flex; flex-direction: column; gap: 24px;">
                
                <!-- Tarjeta de Perfil -->
                <div class="card">
                    <div class="card-header">
                        <span class="card-title"><i class="fas fa-id-card me-2"></i> Perfil de Usuario</span>
                    </div>
                    <div class="dx-v2-users-form-body">
                        <div class="dx-v2-form-group">
                            <label for="name" class="dx-v2-form-label">NOMBRE COMPLETO</label>
                            <input type="text" name="name" id="name" class="dx-v2-form-input" value="{{ old('name', $user->name) }}" required>
                            @error('name') <p class="date-sub">{{ $message }}</p> @enderror
                        </div>

                        <div class="dx-v2-form-group">
                            <label for="email" class="dx-v2-form-label">EMAIL INSTITUCIONAL</label>
                            <input type="email" name="email" id="email" class="dx-v2-form-input font-mono" value="{{ old('email', $user->email) }}" required>
                            @error('email') <p class="date-sub">{{ $message }}</p> @enderror
                        </div>
                        
                        <div class="dx-v2-form-group" x-data="{ active: {{ $user->is_active ? 'true' : 'false' }} }" style="margin-bottom: 0;">
                            <div class="dx-v2-users-switch-box">
                                <div>
                                    <div class="dx-v2-users-switch-title">Usuario Activo</div>
                                    <div class="dx-v2-users-switch-info">Define si puede entrar al portal</div>
                                </div>
                                @if($user->id !== auth()->id())
                                    <input type="hidden" name="is_active" :value="active ? 1 : 0">
                                    <button type="button" 
                                            class="dx-v2-form-switch" 
                                            :class="active ? 'active' : ''"
                                            @click="active = !active">
                                        <span class="dx-v2-form-switch-dot"></span>
                                    </button>
                                @else
                                    <input type="hidden" name="is_active" value="1">
                                    <span class="badge badge-success">SIEMPRE ACTIVO</span>
                                @endif
                            </div>
                            @if($user->id === auth()->id())
                                <div style="font-size: 11px; color: var(--muted); margin-top: 8px; font-style: italic;">
                                    <i class="fas fa-shield-alt me-1"></i> Por seguridad no puedes desactivar tu propia cuenta.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Tarjeta de Seguridad -->
                <div class="card">
                    <div class="card-header">
                        <span class="card-title"><i class="fas fa-key me-2"></i> Seguridad (Opcional)</span>
                    </div>
                    <div class="dx-v2-users-form-body">
                        <p class="dx-v2-users-security-desc" style="margin-top: 0; margin-bottom: 16px;">Deja en blanco para mantener la contraseña actual.</p>
                        <div class="dx-v2-form-group">
                            <label for="password" class="dx-v2-form-label">NUEVA CONTRASEÑA</label>
                            <input type="password" name="password" id="password" class="dx-v2-form-input font-mono">
                            @error('password') <p class="date-sub">{{ $message }}</p> @enderror
                        </div>
                        <div class="dx-v2-form-group" style="margin-bottom: 0;">
                            <label for="password_confirmation" class="dx-v2-form-label">CONFIRMAR CONTRASEÑA</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="dx-v2-form-input font-mono">
                        </div>
                    </div>
                </div>

            </div>

            <!-- COLUMNA DERECHA: Rol y Permisos -->
            <div style="display: flex; flex-direction: column; gap: 24px;">
                <div class="card">
                    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                        <span class="card-title"><i class="fas fa-shield-alt me-2"></i> Control de Acceso</span>
                        <button type="submit" class="btn-primary dx-v2-users-btn-submit" style="margin: 0; padding: 6px 16px; font-size: 13px;">
                            <i class="fas fa-save me-2"></i> Guardar Cambios
                        </button>
                    </div>
                    <div class="dx-v2-users-form-body">
                        <div class="dx-v2-form-group">
                            <label for="role_id" class="dx-v2-form-label">ROL PRINCIPAL EN EL SISTEMA</label>
                            <select name="role_id" id="role_id" class="dx-v2-form-select" required>
                                @php $currentRoleId = $user->roles->first()->id ?? null; @endphp
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}" {{ old('role_id', $currentRoleId) == $role->id ? 'selected' : '' }}>
                                        {{ ucfirst($role->name) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('role_id') <p class="date-sub">{{ $message }}</p> @enderror
                            <p class="dx-v2-users-security-desc" style="margin-top: 8px;">El rol principal define la interfaz base y los permisos globales del usuario.</p>
                        </div>

                        <div class="dx-v2-users-security-box" style="margin-top: 32px; border-color: var(--border);">
                            <div class="dx-v2-users-security-title" style="color: var(--primary);">
                                <i class="fas fa-sliders-h me-2"></i> Permisos Especiales (Individuales)
                            </div>
                            <p class="dx-v2-users-security-desc" style="margin-top: 0; margin-bottom: 24px;">
                                Otorga acceso granular a módulos específicos. Estos permisos se sumarán a los que ya le otorga su rol principal.
                            </p>
                            <div style="display: flex; flex-direction: column; gap: 24px;">
                                @php
                                    $permissionCategories = [
                                        'Administración' => [
                                            'manage system' => 'Telemetría y Sistema',
                                            'manage users' => 'Gestión de Usuarios',
                                            'manage backups' => 'Copias de Seguridad',
                                            'manage ai' => 'Modelos y Rutas IA',
                                            'manage imports' => 'Importación de Datos',
                                            'manage normalization' => 'Normalización de BD',
                                        ],
                                        'Gestión y Operaciones' => [
                                            'manage inventory' => 'Inventario de Licencias',
                                            'manage clients' => 'Gestión de Clientes',
                                            'manage cloud accounts' => 'Cuentas Cloud',
                                            'manage contacts' => 'Contactos de Clientes',
                                            'manage alerts' => 'Configuración de Alertas',
                                            'manage resources' => 'Recursos y Enlaces',
                                        ],
                                        'Herramientas' => [
                                            'access nx suite tool' => 'Herramienta: NX Suite',
                                            'access star ccm tool' => 'Herramienta: STAR-CCM+',
                                            'access heeds tool' => 'Herramienta: HEEDS',
                                            'access moldex3d tool' => 'Herramienta: Moldex3D',
                                            'access cod tool' => 'Herramienta: Certificados (COD)',
                                            'access time tracking tool' => 'Imputación de Horas',
                                            'access renewal planner' => 'Planificador de Renovaciones',
                                        ],
                                        'Visualización Básica' => [
                                            'view clients' => 'Ver Fichas de Clientes',
                                            'view inventory' => 'Ver Inventario Global',
                                            'view reports' => 'Ver Reportes y Analítica'
                                        ]
                                    ];
                                @endphp

                                @foreach($permissionCategories as $categoryName => $perms)
                                    <div>
                                        <h4 style="font-size: 13px; font-weight: 600; color: var(--text-muted); margin-bottom: 12px; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid var(--border); padding-bottom: 8px;">{{ $categoryName }}</h4>
                                        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 12px; background: var(--surface-light); padding: 16px; border-radius: 8px; border: 1px solid var(--border);">
                                            @foreach($perms as $permName => $humanLabel)
                                                <label style="display: flex; align-items: center; gap: 8px; font-size: 14px; color: var(--text-dark); cursor: pointer; padding: 6px; border-radius: 4px; transition: background 0.2s;" onmouseover="this.style.background='var(--background)'" onmouseout="this.style.background='transparent'">
                                                    <input type="checkbox" name="permissions[]" value="{{ $permName }}" {{ (is_array(old('permissions')) && in_array($permName, old('permissions'))) || (!old('permissions') && $user->hasDirectPermission($permName)) ? 'checked' : '' }} style="accent-color: var(--primary); width: 16px; height: 16px;">
                                                    <span>{{ $humanLabel }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </form>
</div>
@endsection
