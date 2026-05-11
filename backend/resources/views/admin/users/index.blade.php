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
                                    <div class="avatar-sm me-3 bg-secondary rounded-circle d-flex align-items-center justify-content-center text-white">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold text-white">{{ $user->name }}</div>
                                        <div class="text-secondary small">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge rounded-pill 
                                    @if($user->isAdmin()) bg-danger-subtle text-danger border border-danger 
                                    @elseif($user->isTechnician()) bg-primary-subtle text-primary border border-primary
                                    @elseif($user->isStaff()) bg-info-subtle text-info border border-info
                                    @else bg-secondary-subtle text-secondary border border-secondary @endif"
                                    style="font-size: 0.75rem; letter-spacing: 0.025em;">
                                    {{ $user->role->name ?? 'Sin Rol' }}
                                </span>
                            </td>
                            <td x-data="{ active: {{ $user->is_active ? 'true' : 'false' }} }">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" :checked="active" 
                                           @change="toggleStatus({{ $user->id }}, $el)"
                                           {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                                    <span class="ms-1 small" :class="active ? 'text-success' : 'text-danger'" 
                                          x-text="active ? 'Activo' : 'Inactivo'"></span>
                                </div>
                            </td>
                            <td class="text-secondary small">
                                @php
                                    $lastActive = \Illuminate\Support\Facades\Redis::get("user:active:{$user->id}");
                                @endphp
                                @if($lastActive)
                                    <span class="text-success">En línea</span>
                                @else
                                    <span class="text-muted">Desconectado</span>
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
    const active = element.checked;
    
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
            element.checked = !active;
        } else {
            // Toast notification or feedback
            console.log('Status updated:', data.is_active);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        element.checked = !active;
        alert('Error al actualizar el estado.');
    });
}
</script>

<style>
    .avatar-sm { width: 32px; height: 32px; font-size: 0.875rem; }
    .bg-danger-subtle { background-color: rgba(220, 53, 69, 0.1) !important; }
    .bg-primary-subtle { background-color: rgba(13, 110, 253, 0.1) !important; }
    .bg-info-subtle { background-color: rgba(13, 202, 240, 0.1) !important; }
    .bg-secondary-subtle { background-color: rgba(108, 117, 125, 0.1) !important; }
    .form-check-input:checked { background-color: var(--success); border-color: var(--success); }
</style>
@endsection
