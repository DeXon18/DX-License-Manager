<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'Inter', sans-serif; color: #1a202c; line-height: 1.6; margin: 0; padding: 20px; background-color: #f7fafc; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; padding: 30px; border-radius: 8px; border: 1px solid #e2e8f0; }
        .header { border-bottom: 2px solid #009999; padding-bottom: 15px; margin-bottom: 25px; }
        .header h1 { font-size: 18px; color: #0d1117; margin: 0; text-transform: uppercase; letter-spacing: 0.05em; }
        .client-name { color: #009999; font-weight: bold; }
        
        .section { margin-bottom: 30px; }
        .section-title { font-size: 14px; font-weight: bold; text-transform: uppercase; margin-bottom: 10px; padding-bottom: 5px; border-bottom: 1px solid #edf2f7; }
        
        .alerta { color: #b91c1c; border-left: 4px solid #b91c1c; padding-left: 10px; }
        .aviso { color: #b45309; border-left: 4px solid #b45309; padding-left: 10px; }
        .recordatorio { color: #1d4ed8; border-left: 4px solid #1d4ed8; padding-left: 10px; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 13px; }
        th { text-align: left; color: #718096; text-transform: uppercase; font-size: 11px; padding: 8px; border-bottom: 1px solid #edf2f7; }
        td { padding: 8px; border-bottom: 1px solid #f7fafc; }
        .mono { font-family: 'IBM Plex Mono', monospace; }
        
        .footer { margin-top: 40px; font-size: 12px; color: #718096; border-top: 1px solid #edf2f7; padding-top: 20px; }
        .btn { display: inline-block; padding: 10px 20px; background-color: #388bfd; color: #ffffff; text-decoration: none; border-radius: 6px; font-weight: bold; font-size: 13px; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>DX License Manager</h1>
        </div>
        
        <p>Hola,</p>
        <p>Este es el reporte semanal de caducidad de licencias para <span class="client-name">{{ $client->name }}</span>.</p>
        
        @if($expiring['alerta']->isNotEmpty())
        <div class="section">
            <div class="section-title alerta">⚠️ Alerta Crítica (0-7 días)</div>
            <table>
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Sold-To</th>
                        <th>Vence</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($expiring['alerta'] as $item)
                    <tr>
                        <td><strong>{{ $item->product_code }}</strong><br><small>{{ $item->description }}</small></td>
                        <td class="mono">{{ $item->daemon->sold_to }}</td>
                        <td class="mono">{{ $item->expiration_date->format('d/m/Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        @if($expiring['aviso']->isNotEmpty())
        <div class="section">
            <div class="section-title aviso">🔔 Aviso de Próxima Caducidad (7-15 días)</div>
            <table>
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Sold-To</th>
                        <th>Vence</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($expiring['aviso'] as $item)
                    <tr>
                        <td><strong>{{ $item->product_code }}</strong><br><small>{{ $item->description }}</small></td>
                        <td class="mono">{{ $item->daemon->sold_to }}</td>
                        <td class="mono">{{ $item->expiration_date->format('d/m/Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        @if($expiring['recordatorio']->isNotEmpty())
        <div class="section">
            <div class="section-title recordatorio">📝 Recordatorio (15-30 días)</div>
            <table>
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Sold-To</th>
                        <th>Vence</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($expiring['recordatorio'] as $item)
                    <tr>
                        <td><strong>{{ $item->product_code }}</strong><br><small>{{ $item->description }}</small></td>
                        <td class="mono">{{ $item->daemon->sold_to }}</td>
                        <td class="mono">{{ $item->expiration_date->format('d/m/Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        <p>Por favor, revisen estas licencias para asegurar la continuidad del servicio.</p>
        
        <a href="{{ url('/') }}" class="btn">Acceder al Portal</a>
        
        <div class="footer">
            <p>Este es un correo automático generado por el portal DX License Manager.<br>
            Soporte AYS — Departamento Técnico</p>
        </div>
    </div>
</body>
</html>
