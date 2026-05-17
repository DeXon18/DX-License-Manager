<!DOCTYPE html>
<html lang="es" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DX License Manager — Acceso</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/img/favicon.png') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/dx-styles.css') }}?v={{ time() }}">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="login-page" 
    x-data="{ 
        theme: localStorage.getItem('theme') || 'light',
        toggle() {
            this.theme = this.theme === 'dark' ? 'light' : 'dark';
            localStorage.setItem('theme', this.theme);
        }
    }" 
    x-init="$watch('theme', val => localStorage.setItem('theme', val))"
    :data-theme="theme">

<div class="login-container">
    <div class="left">
        <div class="inner-col">
            <div class="brand">
                <div class="brand-mark">DX</div>
                <span class="brand-name">DX License Manager</span>
            </div>

            <div class="left-content">
                <h1 class="tagline">Gestión inteligente de <span>licencias</span> de software industrial</h1>
                <p class="desc">Portal centralizado para el seguimiento, auditoría y renovación de licencias Siemens PLM y Moldex3D.</p>
            </div>

            <div class="left-footer">
                <span>© 2026 DX License Manager</span>
                <div class="theme-switch" @click="toggle()" :class="theme === 'dark' ? 'is-dark' : ''">
                    <div class="switch-track">
                        <div class="switch-knob">
                            <span x-show="theme === 'light'">☀️</span>
                            <span x-show="theme === 'dark'">🌙</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="right">
        <div class="inner-col">
            <div class="login-box">
                <div class="version-badge"><div class="dot"></div>v2.7 · Beta</div>
                <h2 class="login-title">Acceso al portal</h2>
                <p class="login-sub">Introduce tus credenciales para continuar</p>

                <form action="{{ url('/login') }}" method="POST">
                    @csrf

                    @if ($errors->any())
                        <div style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2); color: #ef4444; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 14px;">
                            @foreach ($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif

                    <div class="form-group">
                        <label for="email">Correo electrónico</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" placeholder="usuario@dxpro.es" required autofocus>
                    </div>
                    <div class="form-group">
                        <label for="password">Contraseña</label>
                        <input type="password" name="password" id="password" placeholder="••••••••" required>
                    </div>
                    <button type="submit" class="btn-login">Iniciar sesión</button>
                </form>

                <hr class="divider">
                <div class="login-footer">Acceso restringido · Solo personal autorizado</div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
