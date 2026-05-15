<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mantenimiento — DX License Manager</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
        /* ── Design tokens: dark mode ─────────────────────── */
        :root {
            --bg:              #0D1117;
            --surface:         #161B22;
            --raised:          #21262D;
            --border:          #30363D;
            --border-subtle:   #21262D;
            --primary:         #E6EDF3;
            --secondary:       #CDD9E5;
            --muted:           #8B949E;
            --accent:          #388BFD;
            --success:         #3FB950;
            --success-bg:      #0D2818;
            --success-border:  #1A5C2A;
            --warning:         #D29922;
            --warning-bg:      #2D1F00;
            --warning-border:  #5A3E00;
            --danger:          #E05252;
            --danger-bg:       #2D0F0F;
            --danger-border:   #5C1A1A;

            /* elevation dark-1 */
            --shadow-1: 0 1px 2px rgba(0,0,0,.30);
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: Inter, system-ui, sans-serif;
            font-size: 0.889rem;
            line-height: 1.65;
            background: var(--bg);
            color: var(--primary);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 32px 16px;
        }

        /* ── Wordmark ─────────────────────────────────────── */
        .wordmark {
            font-family: 'IBM Plex Mono', monospace;
            font-size: 0.694rem;
            font-weight: 500;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: var(--muted);
            margin-bottom: 24px;
            text-align: center;
        }

        /* ── Card ─────────────────────────────────────────── */
        .card {
            width: 100%;
            max-width: 460px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 10px;
            box-shadow: var(--shadow-1);
            overflow: hidden;
        }

        /* ── Card header ──────────────────────────────────── */
        .card-header {
            padding: 24px 24px 20px;
            border-bottom: 1px solid var(--border);
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: var(--warning-bg);
            color: var(--warning);
            border: 1px solid var(--warning-border);
            border-radius: 9999px;
            padding: 3px 8px;
            font-size: 0.694rem;
            font-weight: 600;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            margin-bottom: 16px;
        }

        .badge-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--warning);
            flex-shrink: 0;
            animation: livePulse 1.8s ease-in-out infinite;
        }

        @keyframes livePulse {
            0%, 100% { opacity: 1; }
            50%       { opacity: 0.3; }
        }

        h1 {
            font-size: 1.602rem;
            font-weight: 700;
            line-height: 1.2;
            letter-spacing: -0.02em;
            color: var(--primary);
            margin-bottom: 8px;
        }

        .description {
            font-size: 0.889rem;
            color: var(--muted);
            line-height: 1.65;
        }

        /* ── System status panel ──────────────────────────── */
        .panel {
            padding: 20px 24px;
        }

        .panel-label {
            font-size: 0.694rem;
            font-weight: 600;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: var(--muted);
            margin-bottom: 12px;
        }

        .status-list {
            border: 1px solid var(--border);
            border-radius: 6px;
            overflow: hidden;
        }

        .status-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 12px;
            background: var(--surface);
            border-bottom: 1px solid var(--border-subtle);
        }

        .status-row:last-child {
            border-bottom: none;
        }

        .status-name {
            font-family: 'IBM Plex Mono', monospace;
            font-size: 0.8125rem;
            color: var(--secondary);
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.694rem;
            font-weight: 600;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            border-radius: 9999px;
            padding: 3px 8px;
            border: 1px solid;
        }

        .status-badge.ok {
            background: var(--success-bg);
            color: var(--success);
            border-color: var(--success-border);
        }

        .status-badge.warn {
            background: var(--warning-bg);
            color: var(--warning);
            border-color: var(--warning-border);
        }

        .status-badge.off {
            background: var(--raised);
            color: var(--muted);
            border-color: var(--border);
        }

        .status-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .ok   .status-dot { background: var(--success); animation: livePulse 2.2s ease-in-out infinite; }
        .warn .status-dot { background: var(--warning); animation: livePulse 1.6s ease-in-out infinite; }
        .off  .status-dot { background: var(--muted); }

        /* ── Footer ───────────────────────────────────────── */
        .footer-note {
            padding: 14px 24px;
            border-top: 1px solid var(--border);
            background: var(--raised);
            font-size: 0.79rem;
            color: var(--muted);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
        }

        .footer-note span {
            font-family: 'IBM Plex Mono', monospace;
            font-size: 0.79rem;
            color: var(--muted);
        }
    </style>
</head>
<body>

    <div class="wordmark">DX License Manager</div>

    <div class="card">

        <!-- Header -->
        <div class="card-header">
            <div class="badge">
                <span class="badge-dot"></span>
                Parada técnica en curso
            </div>
            <h1>Mantenimiento</h1>
            <p class="description">
                Realizando mejoras críticas en la infraestructura del portal.
                Volveremos a estar operativos en breve.
            </p>
        </div>

        <!-- System status -->
        <div class="panel">
            <div class="panel-label">Estado del sistema</div>
            <div class="status-list">

                <div class="status-row">
                    <span class="status-name">API Core</span>
                    <span class="status-badge ok">
                        <span class="status-dot"></span>Online
                    </span>
                </div>

                <div class="status-row">
                    <span class="status-name">DB Cluster</span>
                    <span class="status-badge warn">
                        <span class="status-dot"></span>Migración
                    </span>
                </div>

                <div class="status-row">
                    <span class="status-name">Portal Web</span>
                    <span class="status-badge off">
                        <span class="status-dot"></span>Parado
                    </span>
                </div>

                <div class="status-row">
                    <span class="status-name">License Srv</span>
                    <span class="status-badge ok">
                        <span class="status-dot"></span>Online
                    </span>
                </div>

            </div>
        </div>

        <!-- Footer note -->
        <div class="footer-note">
            <span>DX License Manager</span>
            <span>v0 · Fase 0</span>
        </div>

    </div>

</body>
</html>