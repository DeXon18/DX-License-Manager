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

<div style="max-width: 1200px; margin: 0 auto; padding-bottom: 60px;">
    <form action="{{ route('admin.users.store') }}" method="POST">
        @csrf

        <!-- BLOQUE SUPERIOR: Perfil y Seguridad (2 Columnas) -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(450px, 1fr)); gap: 24px; margin-bottom: 24px;">
            
            <!-- Tarjeta de Perfil -->
            <div class="card" style="height: 100%;">
                <div class="card-header">
                    <span class="card-title"><i class="fas fa-id-card me-2" style="color: var(--primary);"></i> Perfil de Usuario</span>
                </div>
                <div class="dx-v2-users-form-body">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="dx-v2-form-group">
                            <label for="name" class="dx-v2-form-label">NOMBRE COMPLETO</label>
                            <input type="text" name="name" id="name" class="dx-v2-form-input" value="{{ old('name') }}" required>
                            @error('name') <p class="date-sub">{{ $message }}</p> @enderror
                        </div>

                        <div class="dx-v2-form-group">
                            <label for="email" class="dx-v2-form-label">EMAIL INSTITUCIONAL</label>
                            <input type="email" name="email" id="email" class="dx-v2-form-input font-mono" value="{{ old('email') }}" required>
                            @error('email') <p class="date-sub">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    
                    <div class="dx-v2-form-group" x-data="{ active: true }" style="margin-top: 8px; margin-bottom: 0; padding-top: 16px; border-top: 1px solid var(--border);">
                        <div class="dx-v2-users-switch-box" style="padding: 0; border: none; background: transparent;">
                            <div>
                                <div class="dx-v2-users-switch-title" style="font-size: 14px;">Estado de la Cuenta</div>
                                <div class="dx-v2-users-switch-info" style="font-size: 12px;">Define si el usuario tiene permitido iniciar sesión en el portal.</div>
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
                </div>
            </div>

            <!-- Tarjeta de Seguridad -->
            <div class="card" style="height: 100%;">
                <div class="card-header">
                    <span class="card-title"><i class="fas fa-key me-2" style="color: var(--primary);"></i> Seguridad Inicial</span>
                </div>
                <div class="dx-v2-users-form-body" style="display: flex; flex-direction: column; justify-content: center;">
                    <p class="dx-v2-users-security-desc" style="margin-top: 0; margin-bottom: 24px; font-size: 13px;">Define la contraseña de acceso temporal. El usuario podrá cambiarla desde su panel de perfil posteriormente.</p>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="dx-v2-form-group" style="margin-bottom: 0;">
                            <label for="password" class="dx-v2-form-label">CONTRASEÑA TEMPORAL</label>
                            <input type="password" name="password" id="password" class="dx-v2-form-input font-mono" required placeholder="••••••••">
                            @error('password') <p class="date-sub">{{ $message }}</p> @enderror
                        </div>
                        <div class="dx-v2-form-group" style="margin-bottom: 0;">
                            <label for="password_confirmation" class="dx-v2-form-label">CONFIRMAR CONTRASEÑA</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="dx-v2-form-input font-mono" required placeholder="••••••••">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- BLOQUE INFERIOR: Control de Acceso y Permisos -->
        <div class="card">
            <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border); padding-bottom: 16px;">
                <span class="card-title"><i class="fas fa-shield-check me-2" style="color: var(--primary);"></i> Nivel de Acceso y Permisos Granulares</span>
            </div>
            
            <div class="dx-v2-users-form-body">
                <!-- Selector de Rol Principal -->
                <div style="max-width: 400px; margin-bottom: 32px; padding: 16px; background: var(--surface-light); border-radius: 8px; border: 1px solid var(--border);">
                    <label for="role_id" class="dx-v2-form-label" style="font-weight: 600; color: var(--text-dark);">ROL PRINCIPAL EN EL SISTEMA</label>
                    <select name="role_id" id="role_id" class="dx-v2-form-select" required style="margin-top: 8px; background-color: var(--bg);">
                        <option value="" disabled selected>Selecciona un rol...</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                {{ ucfirst($role->name) }}
                            </option>
                        @endforeach
                    </select>
                    @error('role_id') <p class="date-sub">{{ $message }}</p> @enderror
                    <p style="font-size: 12px; color: var(--muted); margin-top: 8px; margin-bottom: 0;">Define la interfaz base y hereda los permisos estándar para ese rol.</p>
                </div>

                <!-- Matriz de Permisos (4 Columnas) -->
                <div>
                    <h4 style="font-size: 14px; font-weight: 600; color: var(--text-dark); margin-bottom: 4px;">Permisos Individuales Adicionales</h4>
                    <p style="font-size: 13px; color: var(--muted); margin-bottom: 24px;">Otorga privilegios extra sobre módulos específicos. Las herramientas habilitadas aparecerán en el menú lateral del usuario.</p>
                    
                    @php
                        $permissionCategories = [
                            'Gestión y Operaciones' => [
                                'manage inventory' => 'Inventario de Licencias',
                                'manage clients' => 'Gestión de Clientes',
                                'manage cloud accounts' => 'Cuentas Cloud',
                                'manage contacts' => 'Contactos de Clientes',
                                'manage alerts' => 'Configurar Alertas',
                                'manage resources' => 'Recursos y Enlaces',
                                'access renewal planner' => 'Planificador Renovaciones',
                            ],
                            'Herramientas' => [
                                'access nx suite tool' => 'Herramienta: NX Suite',
                                'access star ccm tool' => 'Herramienta: STAR-CCM+',
                                'access heeds tool' => 'Herramienta: HEEDS',
                                'access moldex3d tool' => 'Herramienta: Moldex3D',
                                'access cod tool' => 'Certificados (COD)',
                                'access time tracking tool' => 'Imputación de Horas',
                            ],
                            'Visualización' => [
                                'view clients' => 'Ver Fichas Clientes',
                                'view inventory' => 'Ver Inventario Global',
                                'view reports' => 'Ver Analítica y Reportes'
                            ],
                            'Sistemas (Admin)' => [
                                'manage system' => 'Telemetría del Sistema',
                                'manage users' => 'Gestión de Usuarios',
                                'manage backups' => 'Copias de Seguridad',
                                'manage ai' => 'Modelos y Rutas IA',
                                'manage imports' => 'Importación de Datos',
                                'manage normalization' => 'Normalización BD',
                            ]
                        ];
                    @endphp

                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 24px;">
                        @foreach($permissionCategories as $categoryName => $perms)
                            <div style="border: 1px solid var(--border); border-radius: 8px; overflow: hidden;">
                                <div style="background: var(--surface-light); padding: 10px 16px; border-bottom: 1px solid var(--border); font-size: 12px; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px;">
                                    {{ $categoryName }}
                                </div>
                                <div style="padding: 8px 0; background: var(--bg);">
                                    @foreach($perms as $permName => $humanLabel)
                                        @php 
                                            $isChecked = (is_array(old('permissions')) && in_array($permName, old('permissions')));
                                        @endphp
                                        <label style="display: flex; align-items: center; justify-content: space-between; padding: 8px 16px; cursor: pointer; transition: background 0.2s;" onmouseover="this.style.background='var(--surface-hover)'" onmouseout="this.style.background='transparent'">
                                            <span style="font-size: 13px; color: {{ $isChecked ? 'var(--text-dark)' : 'var(--text-muted)' }}; font-weight: {{ $isChecked ? '500' : '400' }}; transition: all 0.2s;">{{ $humanLabel }}</span>
                                            <input type="checkbox" name="permissions[]" value="{{ $permName }}" {{ $isChecked ? 'checked' : '' }} style="accent-color: var(--primary); width: 16px; height: 16px; cursor: pointer;">
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div style="margin-top: 40px; display: flex; justify-content: flex-end; padding-top: 24px; border-top: 1px solid var(--border);">
                    <a href="{{ route('admin.users.index') }}" class="btn-secondary" style="margin-right: 12px; padding: 10px 24px; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; border-radius: 6px; font-size: 14px; font-weight: 500;">Cancelar</a>
                    <button type="submit" class="btn-primary" style="padding: 10px 32px; font-size: 14px;">
                        <i class="fas fa-user-plus me-2"></i> Crear Usuario
                    </button>
                </div>
            </div>
        </div>

    </form>
</div>
    
    <div class="dx-v2-users-banner">
        <i class="fas fa-paper-plane dx-v2-users-banner-icon"></i>
        <div>
            El sistema enviará automáticamente un email de bienvenida con las credenciales de acceso al finalizar el alta.
        </div>
    </div>
</div>
@endsection
