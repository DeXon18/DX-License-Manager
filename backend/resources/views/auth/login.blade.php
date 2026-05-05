<!DOCTYPE html>
<html lang="es" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DX Control Center — Acceso</title>
    <link rel="stylesheet" href="{{ asset('assets/css/fonts.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('assets/css/dx-styles.css') }}?v={{ time() }}">
    <style>
        .login-page {
          display: flex; min-height: 100vh; font-family: 'Inter', sans-serif;
          background: var(--bg); color: var(--primary);
        }
        .login-page .left {
          flex: 1.2; display: flex; flex-direction: column; justify-content: space-between;
          padding: 60px; position: relative; overflow: hidden;
          background: linear-gradient(135deg, rgba(13, 17, 23, 0.95), rgba(13, 17, 23, 0.8)), 
                      url("{{ asset('assets/img/login-bg.png') }}");
          background-size: cover; background-position: center; color: #fff !important;
        }
        .login-page .right {
          flex: 1; display: flex; align-items: center; justify-content: center;
          background: var(--bg); padding: 40px;
        }
        .login-box {
          width: 100%; max-width: 400px; padding: 40px;
          background: var(--surface); border: 1px solid var(--border);
          border-radius: 16px; box-shadow: 0 10px 40px rgba(0,0,0,0.05);
        }
        .brand { display: flex; align-items: center; gap: 12px; }
        .brand-mark {
          background: var(--accent); color: #fff; width: 36px; height: 36px;
          border-radius: 8px; display: flex; align-items: center; justify-content: center;
          font-weight: 700; font-size: 14px;
        }
        .brand-name { font-size: 18px; font-weight: 600; }
        .tagline { font-size: 32px; font-weight: 700; margin: 40px 0 20px; line-height: 1.2; }
        .tagline span { color: var(--accent); }
        .desc { color: rgba(255,255,255,0.7); font-size: 16px; line-height: 1.6; max-width: 440px; }
        .stats { display: flex; gap: 40px; margin-top: 40px; }
        .stat-num { display: block; font-size: 24px; font-weight: 700; color: #fff; }
        .stat-label { font-size: 13px; color: rgba(255,255,255,0.5); text-transform: uppercase; letter-spacing: 0.05em; }
        .theme-toggle {
          display: flex; align-items: center; gap: 8px; cursor: pointer;
          padding: 6px 10px; background: rgba(255,255,255,0.1);
          border-radius: 20px; transition: background 0.2s;
        }
        .toggle-track {
          width: 40px; height: 20px; background: rgba(0,0,0,0.3);
          border-radius: 10px; position: relative; border: 1px solid rgba(255,255,255,0.1);
        }
        .toggle-knob {
          position: absolute; top: 2px; left: 2px; width: 14px; height: 14px;
          background: #fff; border-radius: 50%; transition: transform 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .toggle-icon { font-size: 14px; user-select: none; }
        .btn-login {
          width: 100%; padding: 12px; background: var(--accent); color: #fff;
          border: none; border-radius: 8px; font-size: 15px; font-weight: 600;
          cursor: pointer; transition: all 0.2s; margin-top: 12px;
        }
    </style>
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
        <div class="version-badge"><div class="dot"></div>v2.0 · Beta</div>
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
