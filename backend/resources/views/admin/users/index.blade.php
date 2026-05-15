@extends('layouts.app')

@section('title', 'Gestión de Usuarios — DX License Manager')

@section('content')
<div class="page-header">
    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
        <div>
            <h1 class="page-title">Gestión de Usuarios</h1>
            <p class="page-sub">Administra el acceso y roles del personal del portal.</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="btn-primary">
            <i class="fas fa-plus me-1"></i> Nuevo Usuario
        </a>
    </div>
</div>

@if(session('success'))
    <div class="badge badge-success mb-4 w-100 p-3" style="justify-content: flex-start;">
        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
    </div>
@endif

<div class="card" style="--accent: var(--accent);">
    <div class="card-header">
        <form action="{{ route('admin.users.index') }}" method="GET" style="display: flex; gap: 12px; width: 100%;">
            <div style="flex: 1; position: relative;">
                <i class="fas fa-search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--muted); font-size: 12px;"></i>
                <input type="text" name="search" class="gui-input" style="padding-left: 34px; width: 100%;" 
                       placeholder="Buscar por nombre o email..." value="{{ request('search') }}">
            </div>
            <select name="role" class="gui-input" style="width: 200px; padding-left: 12px;" onchange="this.form.submit()">
                <option value="">Todos los roles</option>
                @foreach($roles as $role)
                    <option value="{{ $role->slug }}" {{ request('role') == $role->slug ? 'selected' : '' }}>
                        {{ $role->name }}
                    </option>
                @endforeach
            </select>
            <a href="{{ route('admin.users.index') }}" class="btn-secondary" style="padding: 8px 16px;">
                Limpiar
            </a>
        </form>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th style="padding-left: 24px;">Usuario</th>
                <th>Rol</th>
                <th>Estado</th>
                <th>Acceso</th>
                <th style="text-align: right; padding-right: 24px;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
                <tr>
                    <td style="padding-left: 24px;">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div class="avatar">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <div>
                                <div style="font-weight: 600; color: var(--primary);">{{ $user->name }}</div>
                                <div class="font-mono" style="font-size: 11px; color: var(--muted);">{{ $user->email }}</div>
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
                    <td>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <button type="button" 
                                    class="switch {{ $user->is_active ? 'on' : 'off' }}" 
                                    onclick="toggleStatus({{ $user->id }}, this)"
                                    {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                            </button>
                            <span class="font-mono" style="font-size: 10px; color: var(--secondary);">
                                {{ $user->is_active ? 'ACTIVO' : 'INACTIVO' }}
                            </span>
                        </div>
                    </td>
                    <td>
                        @php
                            $lastActive = \Illuminate\Support\Facades\Redis::get("user:active:{$user->id}");
                        @endphp
                        @if($lastActive)
                            <span class="font-mono" style="font-size: 10px; color: var(--success);">
                                <span class="dot online" style="width: 6px; height: 6px; margin-right: 4px;"></span> ONLINE
                            </span>
                        @else
                            <span class="font-mono" style="font-size: 10px; color: var(--muted);">OFFLINE</span>
                        @endif
                    </td>
                    <td style="text-align: right; padding-right: 24px;">
                        <div style="display: flex; gap: 6px; justify-content: flex-end;">
                            <a href="{{ route('admin.users.edit', $user) }}" class="btn-secondary" style="padding: 6px 10px; font-size: 11px;">
                                <i class="fas fa-edit"></i>
                            </a>
                            @if($user->id !== auth()->id())
                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" style="display: inline;"
                                      onsubmit="return confirm('¿Estás seguro de eliminar este usuario? Esta acción no se puede deshacer.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-secondary" style="padding: 6px 10px; font-size: 11px; color: var(--danger) !important;">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 60px 0;">
                        <div style="font-size: 32px; opacity: 0.1; margin-bottom: 12px;">👥</div>
                        <p style="color: var(--muted); font-size: 13px;">No se encontraron usuarios.</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if($users->hasPages())
        <div style="padding: 16px 24px; border-top: 1px solid var(--border);">
            {{ $users->links() }}
        </div>
    @endif
</div>

<script>
function toggleStatus(userId, element) {
    const wasActive = element.classList.contains('on');
    
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
            if (data.is_active) {
                element.classList.remove('off');
                element.classList.add('on');
                element.nextElementSibling.innerText = 'ACTIVO';
            } else {
                element.classList.remove('on');
                element.classList.add('off');
                element.nextElementSibling.innerText = 'INACTIVO';
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al actualizar el estado.');
    });
}
</script>
@endsection
