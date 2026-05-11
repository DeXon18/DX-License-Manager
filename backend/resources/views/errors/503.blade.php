<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mantenimiento - DX License Manager</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;600;800&family=Inter:wght@400;500&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #0f1115;
            --surface: #161920;
            --primary: #ffffff;
            --accent: #4361ee;
            --muted: #8b949e;
            --border: rgba(255,255,255,0.1);
        }
        body {
            margin: 0;
            padding: 0;
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            overflow: hidden;
        }
        .container {
            text-align: center;
            max-width: 500px;
            padding: 40px;
            background: var(--surface);
            border-radius: 24px;
            border: 1px solid var(--border);
            box-shadow: 0 20px 50px rgba(0,0,0,0.5);
            position: relative;
        }
        .icon {
            font-size: 64px;
            margin-bottom: 24px;
            display: inline-block;
            animation: pulse 2s infinite;
        }
        h1 {
            font-family: 'Outfit', sans-serif;
            font-size: 32px;
            font-weight: 800;
            margin: 0 0 16px 0;
            letter-spacing: -0.02em;
        }
        p {
            color: var(--muted);
            line-height: 1.6;
            margin: 0 0 32px 0;
            font-size: 16px;
        }
        .status {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            background: rgba(67, 97, 238, 0.1);
            color: var(--accent);
            border-radius: 100px;
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .dot {
            width: 8px;
            height: 8px;
            background: var(--accent);
            border-radius: 50%;
            box-shadow: 0 0 10px var(--accent);
        }
        @keyframes pulse {
            0% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.1); opacity: 0.8; }
            100% { transform: scale(1); opacity: 1; }
        }
        .brand {
            margin-top: 40px;
            font-family: 'Outfit', sans-serif;
            font-weight: 600;
            font-size: 14px;
            color: var(--muted);
            opacity: 0.5;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon">⚙️</div>
        <div style="margin-bottom: 24px;">
            <div class="status">
                <span class="dot"></span>
                Parada Técnica en curso
            </div>
        </div>
        <h1>Estamos en Mantenimiento</h1>
        <p>Estamos realizando mejoras críticas en la infraestructura del portal para ofrecerte un mejor servicio. Volveremos a estar operativos en breve.</p>
        
        <div class="brand">
            DX CONTROL CENTER
        </div>
    </div>
</body>
</html>
