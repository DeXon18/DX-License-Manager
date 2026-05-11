@extends('layouts.app')

@section('title', 'Editar Usuario — DX License Manager')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary me-3">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h1 class="h3 mb-0 text-white">Editar Usuario</h1>
            </div>

            <div class="card bg-dark border-secondary">
                <div class="card-body p-4">
                    <form action="{{ route('admin.users.update', $user) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label text-secondary small text-uppercase fw-bold">Nombre Completo</label>
                            <input type="text" name="name" id="name" class="form-control bg-transparent border-secondary text-white @error('name') is-invalid @enderror" 
                                   value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label text-secondary small text-uppercase fw-bold">Email Institucional</label>
                            <input type="email" name="email" id="email" class="form-control bg-transparent border-secondary text-white @error('email') is-invalid @enderror" 
                                   value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="p-3 bg-secondary bg-opacity-10 rounded mb-3 border border-secondary">
                            <p class="text-secondary small mb-2 text-uppercase fw-bold"><i class="fas fa-key me-2"></i>Cambiar Contraseña</p>
                            <p class="text-muted extra-small mb-3">Deja estos campos en blanco si no deseas cambiar la contraseña actual.</p>
                            
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="password" class="form-label text-secondary small">Nueva Contraseña</label>
                                    <input type="password" name="password" id="password" class="form-control form-control-sm bg-transparent border-secondary text-white @error('password') is-invalid @enderror">
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="password_confirmation" class="form-label text-secondary small">Confirmar</label>
                                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control form-control-sm bg-transparent border-secondary text-white">
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="role_id" class="form-label text-secondary small text-uppercase fw-bold">Rol en el Sistema</label>
                            <select name="role_id" id="role_id" class="form-select bg-transparent border-secondary text-white @error('role_id') is-invalid @enderror" required>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                                        {{ $role->name }} — {{ $role->description }}
                                    </option>
                                @endforeach
                            </select>
                            @error('role_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" 
                                       {{ old('is_active', $user->is_active) ? 'checked' : '' }}
                                       {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                                <label class="form-check-label text-secondary" for="is_active">Usuario Activo</label>
                                @if($user->id === auth()->id())
                                    <div class="text-muted extra-small mt-1">No puedes desactivar tu propia cuenta administrativa.</div>
                                @endif
                            </div>
                        </div>

                        <div class="d-grid pt-2">
                            <button type="submit" class="btn btn-primary py-2">
                                <i class="fas fa-save me-2"></i> Actualizar Usuario
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .form-control:focus, .form-select:focus {
        background-color: rgba(255,255,255,0.02);
        border-color: var(--accent);
        box-shadow: 0 0 0 0.25rem rgba(var(--accent-rgb), 0.15);
        color: white;
    }
    .extra-small { font-size: 0.7rem; }
</style>
@endsection
