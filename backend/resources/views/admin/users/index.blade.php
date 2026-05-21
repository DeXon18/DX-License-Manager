@extends('layouts.app')

@section('title', 'Gestión de Usuarios — DX License Manager')

@section('content')
<div class="page-header">
    <div class="dx-v2-users-header-layout">
        <div>
            <h1 class="page-title">
                <i class="fas fa-users dx-v2-users-title-icon"></i> Gestión de Usuarios
            </h1>
            <p class="dx-v2-users-subtitle">Administra el acceso y roles del personal del portal.</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="btn-primary">
            <i class="fas fa-plus me-1"></i> Nuevo Usuario
        </a>
    </div>
</div>


<div class="card">
    <div class="card-header">
        <form action="{{ route('admin.users.index') }}" method="GET" class="dx-v2-users-filter-bar">
            <div class="dx-v2-users-search-wrapper">
                <i class="fas fa-search dx-v2-users-search-icon"></i>
                <input type="text" name="search" class="dx-v2-form-input dx-v2-users-search-input" 
                       placeholder="Buscar por nombre o email..." value="{{ request('search') }}">
            </div>
            <select name="role" class="dx-v2-form-select dx-v2-users-filter-select" onchange="this.form.submit()">
                <option value="">Todos los roles</option>
                @foreach($roles as $role)
                    <option value="{{ $role->slug }}" {{ request('role') == $role->slug ? 'selected' : '' }}>
                        {{ $role->name }}
                    </option>
                @endforeach
            </select>
            <a href="{{ route('admin.users.index') }}" class="btn-secondary dx-v2-users-btn-clear">
                Limpiar
            </a>
        </form>
    </div>

    <table class="table dx-v2-users-table">
        <thead>
            <tr>
                <th>Usuario</th>
                <th>Rol</th>
                <th>Estado</th>
                <th>Acceso</th>
                <th style="text-align: right;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
                <tr>
                    <td>
                        <div class="dx-v2-users-col-user">
                            <div class="avatar dx-v2-users-avatar">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <div class="dx-v2-users-name-box">
                                <div class="dx-v2-users-name">{{ $user->name }}</div>
                                <div class="dx-v2-users-email">{{ $user->email }}</div>
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
                        <div class="dx-v2-users-status-row">
                            <button type="button" 
                                    class="switch {{ $user->is_active ? 'on' : 'off' }}" 
                                    onclick="toggleStatus({{ $user->id }}, this)"
                                    {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                            </button>
                            <span class="dx-v2-users-status-label">
                                {{ $user->is_active ? 'ACTIVO' : 'INACTIVO' }}
                            </span>
                        </div>
                    </td>
                    <td>
                        @php
                            $lastActive = \Illuminate\Support\Facades\Redis::get("user:active:{$user->id}");
                        @endphp
                        @if($lastActive)
                            <span class="dx-v2-users-online-badge online">
                                <span class="dx-v2-users-dot online"></span> ONLINE
                            </span>
                        @else
                            <span class="dx-v2-users-online-badge offline">
                                <span class="dx-v2-users-dot"></span> OFFLINE
                            </span>
                        @endif
                    </td>
                    <td style="text-align: right;">
                        <div class="dx-v2-users-actions">
                            <a href="{{ route('admin.users.edit', $user) }}" class="btn-secondary dx-v2-users-actions-btn">
                                <i class="fas fa-edit"></i>
                            </a>
                            @if($user->id !== auth()->id())
                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" style="display: inline;"
                                      onsubmit="return confirm('¿Estás seguro de eliminar este usuario? Esta acción no se puede deshacer.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-secondary dx-v2-users-actions-btn danger">
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
