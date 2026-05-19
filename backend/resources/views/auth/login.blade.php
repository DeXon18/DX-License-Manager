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
<body class="dx-v2-login-page" 
    x-data="{ 
        theme: localStorage.getItem('theme') || 'light',
        toggle() {
            this.theme = this.theme === 'dark' ? 'light' : 'dark';
            localStorage.setItem('theme', this.theme);
        }
    }" 
    x-init="$watch('theme', val => localStorage.setItem('theme', val))"
    :data-theme="theme">

<div class="dx-v2-login-container">
    <div class="dx-v2-login-left">
        <div class="dx-v2-login-inner-col">
            <div class="dx-v2-login-brand">
                <div class="dx-v2-login-brand-mark">DX</div>
                <span class="dx-v2-login-brand-name">DX License Manager</span>
            </div>

            <div class="left-content">
                <h1 class="dx-v2-login-tagline">Gestión inteligente de <span>licencias</span> de software industrial</h1>
                <p class="dx-v2-login-desc">Portal centralizado para el seguimiento, auditoría y renovación de licencias Siemens PLM y Moldex3D.</p>
            </div>

            <div class="left-footer">
                <span>© 2026 DX License Manager</span>
                <div class="dx-v2-login-theme-switch" @click="toggle()" :class="theme === 'dark' ? 'is-dark' : ''">
                    <div class="dx-v2-login-switch-track">
                        <div class="dx-v2-login-switch-knob">
                            <span x-show="theme === 'light'">☀️</span>
                            <span x-show="theme === 'dark'">🌙</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="dx-v2-login-right">
        <div class="dx-v2-login-inner-col">
            <div class="dx-v2-login-box">
                <div class="dx-v2-login-version-badge"><div class="dot"></div>v2.7 · Beta</div>
                <h2 class="dx-v2-login-title">Acceso al portal</h2>
                <p class="dx-v2-login-sub">Introduce tus credenciales para continuar</p>

                <form action="{{ url('/login') }}" method="POST">
                    @csrf

                    @if ($errors->any())
                        <div class="dx-v2-login-error">
                            @foreach ($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif

                    <div class="dx-v2-form-group">
                        <label for="email" class="dx-v2-form-label">Correo electrónico</label>
                        <input type="email" name="email" id="email" class="dx-v2-form-input" value="{{ old('email') }}" placeholder="usuario@dxpro.es" required autofocus>
                    </div>
                    <div class="dx-v2-form-group">
                        <label for="password" class="dx-v2-form-label">Contraseña</label>
                        <input type="password" name="password" id="password" class="dx-v2-form-input" placeholder="••••••••" required>
                    </div>
                    <button type="submit" class="dx-v2-login-btn">Iniciar sesión</button>
                </form>

                <hr class="dx-v2-login-divider">
                <div class="dx-v2-login-footer">Acceso restringido · Solo personal autorizado</div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
