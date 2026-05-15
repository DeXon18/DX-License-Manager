@extends('layouts.app')

@section('title', 'Editar Usuario — DX License Manager')

@section('content')
<div class="page-header">
    <div style="display: flex; align-items: center; gap: 16px;">
        <a href="{{ route('admin.users.index') }}" class="btn-secondary" style="padding: 8px 12px;">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="page-title">Editar Usuario</h1>
            <p class="page-sub">Modifica los permisos y datos de {{ $user->name }}.</p>
        </div>
    </div>
</div>

<div style="max-width: 600px;">
    <div class="card" style="--accent: var(--accent);">
        <div class="card-header">
            <span class="card-title">Perfil de Usuario</span>
        </div>
        
        <div style="padding: 32px;">
            <form action="{{ route('admin.users.update', $user) }}" method="POST">
                @csrf
                @method('PUT')

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

                <div style="padding: 20px; background: var(--bg); border: 1px solid var(--border); border-radius: 8px; margin-bottom: 24px;">
                    <div style="font-weight: 600; font-size: 11px; color: var(--muted); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 16px;">
                        <i class="fas fa-key me-2"></i>Seguridad (Opcional)
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div class="form-group" style="margin-bottom: 0;">
                            <label for="password" style="font-size: 11px;">NUEVA CONTRASEÑA</label>
                            <input type="password" name="password" id="password" class="font-mono" style="padding: 8px 12px;">
                            @error('password') <p class="date-sub" style="margin-top: 4px;">{{ $message }}</p> @enderror
                        </div>
                        <div class="form-group" style="margin-bottom: 0;">
                            <label for="password_confirmation" style="font-size: 11px;">CONFIRMAR</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="font-mono" style="padding: 8px 12px;">
                        </div>
                    </div>
                    <p style="font-size: 10px; color: var(--muted); margin-top: 12px;">Deja en blanco para mantener la contraseña actual.</p>
                </div>

                <div class="form-group">
                    <label for="role_id">ROL EN EL SISTEMA</label>
                    <select name="role_id" id="role_id" style="width: 100%; padding: 12px; background: var(--bg); border: 1px solid var(--border); border-radius: 8px; color: var(--primary); font-family: var(--font-sans);" required>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                                {{ $role->name }} — {{ $role->description }}
                            </option>
                        @endforeach
                    </select>
                    @error('role_id') <p class="date-sub" style="margin-top: 4px;">{{ $message }}</p> @enderror
                </div>

                <div class="form-group" x-data="{ active: {{ $user->is_active ? 'true' : 'false' }} }">
                    <div style="display: flex; align-items: center; justify-content: space-between; padding: 16px; background: var(--bg); border: 1px solid var(--border); border-radius: 8px;">
                        <div>
                            <div style="font-weight: 600; font-size: 13px; color: var(--primary);">Usuario Activo</div>
                            <div style="font-size: 11px; color: var(--muted);">Define si el usuario puede entrar al portal</div>
                        </div>
                        @if($user->id !== auth()->id())
                            <input type="hidden" name="is_active" :value="active ? 1 : 0">
                            <button type="button" 
                                    class="switch" 
                                    :class="active ? 'on' : 'off'"
                                    @click="active = !active">
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

                <div style="margin-top: 32px;">
                    <button type="submit" class="btn-primary" style="width: 100%; padding: 14px;">
                        <i class="fas fa-sync-alt me-2"></i> Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
