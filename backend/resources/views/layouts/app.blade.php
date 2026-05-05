<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'DX License Manager') }} - @yield('title', 'Portal')</title>

    <!-- Fonts & Styles -->
    <link rel="stylesheet" href="{{ asset('assets/css/dx-styles.css') }}">
    
    <!-- Alpine.js (Legacy mode - no build step) -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @stack('styles')
</head>
<body x-data="{ darkMode: true, sidebarOpen: true }" :data-theme="darkMode ? 'dark' : 'light'">
    
    <header>
        <a class="brand" href="{{ url('/') }}">
            <div class="brand-mark">DX</div>
            <span class="brand-name">DX Control Center</span>
        </a>
        <nav class="nav-links">
            <a class="nav-link {{ request()->is('/') ? 'active' : '' }}" href="{{ url('/') }}">Inicio</a>
            <a class="nav-link" href="#">Clientes</a>
            <a class="nav-link" href="#">Herramientas</a>
            <a class="nav-link" href="#">Administración</a>
        </nav>
        <div class="nav-right">
            <div class="theme-toggle" @click="darkMode = !darkMode">
                <span class="toggle-icon">☀️</span>
                <div class="toggle-track"><div class="toggle-knob" :style="darkMode ? 'left: 20px' : 'left: 2px'"></div></div>
                <span class="toggle-icon">🌙</span>
            </div>
            <div class="user-btn">
                <div class="avatar">{{ substr(Auth::user()->name ?? 'U', 0, 1) }}</div>
                <span class="user-name">{{ Auth::user()->name ?? 'Usuario Demo' }}</span>
            </div>
        </div>
    </header>

    <div class="layout">
        <aside class="sidebar" x-show="sidebarOpen" x-transition>
            <div class="sidebar-section">
                <div class="sidebar-heading">Navegación</div>
                <a class="sidebar-item {{ request()->is('/') ? 'active' : '' }}" href="{{ url('/') }}">
                    <span class="sidebar-icon">📊</span> Dashboard
                </a>
                <a class="sidebar-item" href="#">
                    <span class="sidebar-icon">👥</span> Clientes
                </a>
                <a class="sidebar-item" href="#">
                    <span class="sidebar-icon">🛡️</span> Auditoría IA
                </a>
            </div>
            
            <div class="sidebar-section">
                <div class="sidebar-heading">Herramientas</div>
                <a class="sidebar-item" href="#">
                    <span class="sidebar-icon">🔧</span> Solicitudes
                </a>
                <a class="sidebar-item" href="#">
                    <span class="sidebar-icon">📂</span> Repositorio
                </a>
            </div>
        </aside>

        <main class="content">
            @yield('content')
        </main>
    </div>

    <footer>
        <span>&copy; {{ date('Y') }} DX License Manager — Soporte AYS</span>
        <div class="footer-status">
            <div class="dot-live"></div>
            Sistema Online · v2.0.0-beta.2
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
