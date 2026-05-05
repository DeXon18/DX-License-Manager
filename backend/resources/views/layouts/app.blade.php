<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
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
<body class="bg-surface text-foreground font-sans antialiased" x-data="{ sidebarOpen: true, darkMode: true }" :class="{ 'dark': darkMode }">
    
    <div class="flex h-screen overflow-hidden">
        
        <!-- Sidebar -->
        <aside class="w-64 bg-secondary border-r border-border flex-shrink-0 transition-all duration-300" :class="{ '-ml-64': !sidebarOpen }">
            <div class="p-6 flex flex-col h-full">
                <div class="flex items-center gap-3 mb-10">
                    <div class="w-8 h-8 bg-primary rounded flex items-center justify-center text-primary-foreground font-bold">DX</div>
                    <span class="text-xl font-bold tracking-tight">Control Center</span>
                </div>

                <nav class="flex-1 space-y-2">
                    <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg bg-primary/10 text-primary font-medium">
                        <span class="w-5 h-5">📊</span> Dashboard
                    </a>
                    <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-primary/5 text-muted transition-colors">
                        <span class="w-5 h-5">👥</span> Clientes
                    </a>
                    <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-primary/5 text-muted transition-colors">
                        <span class="w-5 h-5">🛡️</span> Auditoría IA
                    </a>
                </nav>

                <div class="pt-6 border-t border-border mt-auto">
                    <button @click="darkMode = !darkMode" class="w-full flex items-center justify-between px-4 py-2 rounded-lg bg-surface border border-border hover:bg-primary/5 transition-all">
                        <span class="text-sm font-medium" x-text="darkMode ? 'Modo Oscuro' : 'Modo Claro'"></span>
                        <span x-text="darkMode ? '🌙' : '☀️'"></span>
                    </button>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 flex flex-col overflow-hidden bg-surface">
            
            <!-- Header -->
            <header class="h-16 bg-secondary border-b border-border flex items-center justify-between px-8">
                <button @click="sidebarOpen = !sidebarOpen" class="p-2 rounded-lg hover:bg-primary/5 text-muted">
                    <span class="text-xl">☰</span>
                </button>

                <div class="flex items-center gap-4">
                    <div class="flex flex-col items-end mr-2">
                        <span class="text-sm font-semibold">{{ Auth::user()->name ?? 'Usuario Demo' }}</span>
                        <span class="text-xs text-muted">Administrador</span>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-primary/20 border border-primary/30 flex items-center justify-center text-primary font-bold">
                        {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <div class="flex-1 overflow-y-auto p-8">
                @yield('content')
            </div>

            <!-- Footer -->
            <footer class="h-12 bg-secondary border-t border-border flex items-center justify-between px-8 text-xs text-muted">
                <div>&copy; {{ date('Y') }} DX License Manager — Soporte AYS</div>
                <div class="flex items-center gap-4">
                    <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-green-500"></span> Sistema Online</span>
                    <span>v2.0.0-beta.2</span>
                </div>
            </footer>
        </main>
    </div>

    @stack('scripts')
</body>
</html>
