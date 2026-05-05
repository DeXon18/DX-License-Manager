<!DOCTYPE html>
<html lang="es" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DX Control Center — Acceso</title>
    <link rel="stylesheet" href="{{ asset('assets/css/fonts.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('assets/css/dx-styles.css') }}?v={{ time() }}">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="login-page" x-data="{ theme: 'light' }" :data-theme="theme">

<div class="left">
    <div class="brand">
        <div class="brand-mark">DX</div>
        <span class="brand-name">DX Control Center</span>
    </div>

    <div class="left-content">
        <h1 class="tagline">Gestión inteligente de <span>licencias</span> de software industrial</h1>
        <p class="desc">Portal centralizado para el seguimiento, auditoría y renovación de licencias Siemens PLM y Moldex3D.</p>
        <div class="stats">
            <div class="stat-item"><span class="stat-num">527</span><span class="stat-label">Contratos</span></div>
            <div class="stat-item"><span class="stat-num">336</span><span class="stat-label">Clientes</span></div>
            <div class="stat-item"><span class="stat-num">5</span><span class="stat-label">Herramientas</span></div>
        </div>
    </div>

    <div class="left-footer">
        <span>© 2026 DX Control Center</span>
        <div class="theme-toggle" @click="theme = (theme === 'dark' ? 'light' : 'dark')">
            <span class="toggle-icon">☀️</span>
            <div class="toggle-track">
                <div class="toggle-knob" :style="theme === 'dark' ? 'transform: translateX(20px)' : 'transform: translateX(0)'"></div>
            </div>
            <span class="toggle-icon">🌙</span>
        </div>
    </div>
</div>

<div class="right">
    <div class="login-box">
        <div class="version-badge"><div class="dot"></div>v2.2 · Beta</div>
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

</body>
</html>
