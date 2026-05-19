<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mantenimiento — DX License Manager</title>
    <link rel="stylesheet" href="/assets/css/dx-v2-main.css">
</head>
<body class="dx-v2-maint-body">

    <div class="dx-v2-maint-wordmark">DX License Manager</div>

    <div class="dx-v2-maint-card">

        <!-- Header -->
        <div class="dx-v2-maint-card-header">
            <div class="dx-v2-maint-badge">
                <span class="dx-v2-maint-badge-dot"></span>
                Parada técnica en curso
            </div>
            <h1 class="dx-v2-maint-title">Mantenimiento</h1>
            <p class="dx-v2-maint-description">
                Realizando mejoras críticas en la infraestructura del portal.
                Volveremos a estar operativos en breve.
            </p>
        </div>

        <!-- System status -->
        <div class="dx-v2-maint-panel">
            <div class="dx-v2-maint-panel-label">Estado del sistema</div>
            <div class="dx-v2-maint-status-list">

                <div class="dx-v2-maint-status-row">
                    <span class="dx-v2-maint-status-name">API Core</span>
                    <span class="dx-v2-maint-status-badge ok">
                        <span class="dx-v2-maint-status-dot"></span>Online
                    </span>
                </div>

                <div class="dx-v2-maint-status-row">
                    <span class="dx-v2-maint-status-name">DB Cluster</span>
                    <span class="dx-v2-maint-status-badge warn">
                        <span class="dx-v2-maint-status-dot"></span>Migración
                    </span>
                </div>

                <div class="dx-v2-maint-status-row">
                    <span class="dx-v2-maint-status-name">Portal Web</span>
                    <span class="dx-v2-maint-status-badge off">
                        <span class="dx-v2-maint-status-dot"></span>Parado
                    </span>
                </div>

                <div class="dx-v2-maint-status-row">
                    <span class="dx-v2-maint-status-name">License Srv</span>
                    <span class="dx-v2-maint-status-badge ok">
                        <span class="dx-v2-maint-status-dot"></span>Online
                    </span>
                </div>

            </div>
        </div>

        <!-- Footer note -->
        <div class="dx-v2-maint-footer-note">
            <span>DX License Manager</span>
            <span>v0 · Fase 0</span>
        </div>

    </div>

</body>
</html>