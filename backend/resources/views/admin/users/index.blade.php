@extends('layouts.app')

@section('title', 'Gestión de Usuarios — DX License Manager')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 text-white">Gestión de Usuarios</h1>
            <p class="text-secondary small mb-0">Administra el acceso y roles del personal del portal.</p>
        </div>
        <div>
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i> Nuevo Usuario
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card bg-dark border-secondary">
        <div class="card-header border-secondary bg-transparent py-3">
            <form action="{{ route('admin.users.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-transparent border-secondary text-secondary">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" name="search" class="form-control bg-transparent border-secondary text-white" 
                               placeholder="Buscar por nombre o email..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="role" class="form-select form-select-sm bg-transparent border-secondary text-white" onchange="this.form.submit()">
                        <option value="">Todos los roles</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->slug }}" {{ request('role') == $role->slug ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary w-100">
                        Limpiar
                    </a>
                </div>
            </form>
        </div>
        <div class="table-responsive">
            <table class="table table-dark table-hover mb-0 align-middle">
                <thead>
                    <tr class="text-secondary small text-uppercase">
                        <th class="ps-4 border-secondary">Usuario</th>
                        <th class="border-secondary">Rol</th>
                        <th class="border-secondary">Estado</th>
                        <th class="border-secondary">Último Acceso</th>
                        <th class="text-end pe-4 border-secondary">Acciones</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    @forelse($users as $user)
                        <tr>
                            <td class="ps-4 py-3">
                                <div class="d-flex align-items-center">
                                    <div class="avatar me-3">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold text-white">{{ $user->name }}</div>
                                        <div class="text-secondary small font-mono">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge 
                                    @if($user->isAdmin()) badge-danger 
                                    @elseif($user->isTechnician()) badge-primary
                                    @elseif($user->isStaff()) badge-info
                                    @else badge-muted @endif">
                                    {{ $user->role->name ?? 'Sin Rol' }}
                                </span>
                            </td>
                            <td x-data="{ active: {{ $user->is_active ? 'true' : 'false' }} }">
                                <button type="button" 
                                        class="switch" 
                                        :class="active ? 'on' : 'off'"
                                        @click="toggleStatus({{ $user->id }}, $el)"
                                        {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                                </button>
                                <span class="ms-2 small text-secondary" x-text="active ? 'Activo' : 'Inactivo'"></span>
                            </td>
                            <td class="text-secondary small">
                                @php
                                    $lastActive = \Illuminate\Support\Facades\Redis::get("user:active:{$user->id}");
                                @endphp
                                @if($lastActive)
                                    <span class="text-success font-mono">ONLINE</span>
                                @else
                                    <span class="text-muted font-mono">OFFLINE</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-outline-light border-secondary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if($user->id !== auth()->id())
                                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('¿Estás seguro de eliminar este usuario? Esta acción no se puede deshacer.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger border-secondary">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-secondary">
                                <i class="fas fa-users fa-3x mb-3 opacity-25"></i>
                                <p>No se encontraron usuarios con los criterios de búsqueda.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($users->hasPages())
            <div class="card-footer border-secondary bg-transparent">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>

<script>
function toggleStatus(userId, element) {
    const isActive = element.classList.contains('on');
    
    fetch(`/admin/users/${userId}/toggle`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            alert(data.error);
        } else {
            // Update switch state
            if (data.is_active) {
                element.classList.remove('off');
                element.classList.add('on');
                element.nextElementSibling.innerText = 'Activo';
            } else {
                element.classList.remove('on');
                element.classList.add('off');
                element.nextElementSibling.innerText = 'Inactivo';
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al actualizar el estado.');
    });
}
</script>

<style>
    .form-check-input:checked { background-color: var(--success); border-color: var(--success); }
</style>
@endsection
