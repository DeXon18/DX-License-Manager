<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'DX License Manager') }} - @yield('title', 'Portal')</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/img/favicon.png') }}">

    <!-- Theme Initialization -->
    <script>
        if (localStorage.getItem('theme') === 'light') {
            document.documentElement.setAttribute('data-theme', 'light');
        } else {
            document.documentElement.setAttribute('data-theme', 'dark');
            localStorage.setItem('theme', 'dark');
        }
    </script>

    <!-- Fonts & Styles -->
    <link rel="stylesheet" href="{{ asset('assets/css/dx-v2-main.css?v=' . time()) }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Alpine.js (Legacy mode - no build step) -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @stack('styles')
</head>
<body x-data="{ 
    darkMode: localStorage.getItem('theme') !== 'light',
    sidebarOpen: true,
    toggleTheme() {
        this.darkMode = !this.darkMode;
        const theme = this.darkMode ? 'dark' : 'light';
        localStorage.setItem('theme', theme);
        document.documentElement.setAttribute('data-theme', theme);
    }
}" @keyup.slash.window="sidebarOpen = !sidebarOpen">
    
    @if($maintenance_active ?? false)
    <div class="dx-v2-maintenance-banner">
        <span>⚠️</span>
        MODO MANTENIMIENTO ACTIVO - El portal no es accesible para usuarios estándar.
        <a href="{{ route('admin.system.index') }}">Gestionar</a>
    </div>
    @endif
    
    <header class="{{ ($maintenance_active ?? false) ? 'dx-v2-maintenance-header' : '' }}">
        <div class="header-inner">
            <a class="dx-lockup" href="{{ url('/') }}">
                <div class="dx-mark">
                    <span>DX</span>
                </div>
                <div class="dx-wordmark">
                    <span class="dx-name">License Manager</span>
                    <span class="dx-sub">by DXPro</span>
                </div>
            </a>
            <nav class="nav-links">
                <a class="nav-link {{ request()->is('/') ? 'active' : '' }}" href="{{ url('/') }}">Inicio</a>
                <a class="nav-link {{ request()->routeIs('clients.*') ? 'active' : '' }}" href="{{ route('clients.index') }}">Clientes</a>
                <a class="nav-link {{ request()->routeIs('tools.*') ? 'active' : '' }}" href="{{ route('tools.index') }}">Herramientas</a>
                <a class="nav-link {{ request()->is('admin/*') ? 'active' : '' }}" href="{{ route('admin.system.index') }}">Administración</a>
            </nav>
            <div class="nav-right">
                <div class="theme-toggle" @click="toggleTheme()">
                    <span class="toggle-icon">☀️</span>
                    <div class="toggle-track"><div class="toggle-knob" :style="darkMode ? 'left: 20px' : 'left: 2px'"></div></div>
                    <span class="toggle-icon">🌙</span>
                </div>
                <a href="{{ route('profile.index') }}" class="user-btn" style="text-decoration: none;">
                    <div class="avatar">{{ substr(Auth::user()->name ?? 'U', 0, 1) }}</div>
                    <span class="user-name">{{ Auth::user()->name ?? 'Usuario Demo' }}</span>
                </a>
            </div>
        </div>
    </header>

    <div class="layout">
        <aside class="sidebar" x-show="sidebarOpen" x-transition>
            <div class="sidebar-section">
                <div class="sidebar-heading">Mi Cuenta</div>
                <a class="sidebar-item {{ request()->routeIs('profile.*') ? 'active' : '' }}" href="{{ route('profile.index') }}">
                    <span class="sidebar-icon">👤</span> Mi Perfil
                </a>
                <form action="{{ route('logout') }}" method="POST" style="display: none;" id="logout-form">@csrf</form>
                <a class="sidebar-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <span class="sidebar-icon">🚪</span> Cerrar Sesión
                </a>
            </div>

            <div class="sidebar-section">
                <div class="sidebar-heading">Navegación</div>
                <a class="sidebar-item {{ request()->is('/') ? 'active' : '' }}" href="{{ url('/') }}">
                    <span class="sidebar-icon">📊</span> Dashboard
                </a>
                <a class="sidebar-item {{ request()->routeIs('clients.*') ? 'active' : '' }}" href="{{ route('clients.index') }}">
                    <span class="sidebar-icon">👥</span> Clientes
                </a>
                <a class="sidebar-item {{ request()->routeIs('renewal-planner.*') ? 'active' : '' }}" href="{{ route('renewal-planner.index') }}">
                    <span class="sidebar-icon">📅</span> Planificador
                </a>
                <a class="sidebar-item" href="#">
                    <span class="sidebar-icon">🛡️</span> Auditoría IA
                </a>
            </div>
            
            <div class="sidebar-section">
                <div class="sidebar-heading">Herramientas</div>
                <a class="sidebar-item {{ request()->routeIs('tools.*') ? 'active' : '' }}" href="{{ route('tools.index') }}">
                    <span class="sidebar-icon">🛠️</span> Hub de Utilidades
                </a>
            </div>

            @if(Auth::user() && Auth::user()->hasRole('admin'))
            <div class="sidebar-section">
                <div class="sidebar-heading">Administración</div>
                <a class="sidebar-item {{ request()->routeIs('admin.system.index') ? 'active' : '' }}" href="{{ route('admin.system.index') }}">
                    <span class="sidebar-icon">⚙️</span> Telemetría Global
                </a>
                <a class="sidebar-item {{ request()->routeIs('admin.backups.*') ? 'active' : '' }}" href="{{ route('admin.backups.index') }}">
                    <span class="sidebar-icon">💾</span> Copias de Seguridad
                </a>
                <a class="sidebar-item {{ request()->routeIs('admin.audit.*') ? 'active' : '' }}" href="{{ route('admin.audit.index') }}">
                    <span class="sidebar-icon">📋</span> Auditoría y Logs
                </a>
                <a class="sidebar-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                    <span class="sidebar-icon">🔑</span> Usuarios y Acceso
                </a>
                <a class="sidebar-item {{ request()->routeIs('admin.repository.index') ? 'active' : '' }}" href="{{ route('admin.repository.index') }}">
                    <span class="sidebar-icon">📂</span> Repositorio Semanal
                </a>
                <a class="sidebar-item {{ request()->routeIs('admin.import.*') || request()->routeIs('admin.normalization.*') ? 'active' : '' }}" href="{{ route('admin.import.index') }}">
                    <span class="sidebar-icon">📥</span> Importación & Datos
                </a>
                <a class="sidebar-item {{ request()->routeIs('admin.alerts.index') ? 'active' : '' }}" href="{{ route('admin.alerts.index') }}">
                    <span class="sidebar-icon">🔔</span> Notificaciones
                </a>
            </div>
            @endif
        </aside>

        <main class="content">
            @yield('content')
        </main>
    </div>

    @include('layouts.partials.footer')
    @include('layouts.partials.toasts')

    @stack('scripts')
    <script>
        // Atajo global Ctrl + Espacio para buscar
        window.addEventListener('keydown', (e) => {
            if (e.ctrlKey && e.code === 'Space') {
                const searchInput = document.querySelector('input[name="search"]');
                if (searchInput) {
                    e.preventDefault();
                    searchInput.focus();
                }
            }
        });

        // Mantener foco en el buscador si hay una búsqueda activa tras recargar
        document.addEventListener('DOMContentLoaded', () => {
            const searchInput = document.querySelector('input[name="search"]');
            if (searchInput && searchInput.value) {
                searchInput.focus();
                const val = searchInput.value;
                searchInput.value = '';
                searchInput.value = val;
            }
        });
    </script>
</body>
</html>
