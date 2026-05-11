@extends('layouts.app')

@section('title', 'Nuevo Usuario — DX License Manager')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary me-3">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h1 class="h3 mb-0 text-white">Nuevo Usuario</h1>
            </div>

            <div class="card bg-dark border-secondary">
                <div class="card-body p-4">
                    <form action="{{ route('admin.users.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label text-secondary small text-uppercase fw-bold">Nombre Completo</label>
                            <input type="text" name="name" id="name" class="form-control bg-transparent border-secondary text-white @error('name') is-invalid @enderror" 
                                   value="{{ old('name') }}" required autofocus>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label text-secondary small text-uppercase fw-bold">Email Institucional</label>
                            <input type="email" name="email" id="email" class="form-control bg-transparent border-secondary text-white @error('email') is-invalid @enderror" 
                                   value="{{ old('email') }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="password" class="form-label text-secondary small text-uppercase fw-bold">Contraseña</label>
                                <input type="password" name="password" id="password" class="form-control bg-transparent border-secondary text-white @error('password') is-invalid @enderror" required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label text-secondary small text-uppercase fw-bold">Confirmar</label>
                                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control bg-transparent border-secondary text-white" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="role_id" class="form-label text-secondary small text-uppercase fw-bold">Rol en el Sistema</label>
                            <select name="role_id" id="role_id" class="form-select bg-transparent border-secondary text-white @error('role_id') is-invalid @enderror" required>
                                <option value="" disabled selected>Selecciona un rol...</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
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
                                <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label text-secondary" for="is_active">Usuario Activo (permite el inicio de sesión)</label>
                            </div>
                        </div>

                        <div class="d-grid pt-2">
                            <button type="submit" class="btn btn-primary py-2">
                                <i class="fas fa-save me-2"></i> Crear Usuario
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="mt-4 p-3 bg-secondary bg-opacity-10 border border-secondary rounded text-secondary small">
                <i class="fas fa-info-circle me-2"></i>
                Se enviará una notificación de acceso al usuario una vez creada la cuenta (próximamente).
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
</style>
@endsection
