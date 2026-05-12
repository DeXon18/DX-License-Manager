<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; color: #333; line-height: 1.6; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #eee; }
        .header { background: #f8f9fa; padding: 15px; text-align: center; border-bottom: 3px solid #005a87; }
        .content { padding: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        th { background: #f4f4f4; }
        .footer { font-size: 12px; color: #777; margin-top: 30px; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2 style="margin:0; color:#005a87;">DX License Manager</h2>
            <p style="margin:5px 0 0;">Reporte Semanal de Repositorio</p>
        </div>
        
        <div class="content">
            <p>Hola Equipo de Soporte,</p>
            <p>Se ha generado automáticamente el repositorio de licencias correspondiente a la <strong>Semana {{ $archive->week_number }}</strong> del año <strong>{{ $archive->year }}</strong>.</p>
            
            <p><strong>Detalles del archivo:</strong></p>
            <ul>
                <li>Nombre: <code>{{ $archive->filename }}</code></li>
                <li>Total de licencias: {{ $archive->files_count }}</li>
            </ul>

            <h3>Resumen de Clientes Procesados</h3>
            <table>
                <thead>
                    <tr>
                        <th>Cliente</th>
                        <th>Cant. Archivos</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($archive->clients_summary as $client => $count)
                    <tr>
                        <td>{{ $client }}</td>
                        <td>{{ $count }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <p>El archivo ZIP completo se adjunta a este correo para su conveniencia.</p>
        </div>

        <div class="footer">
            <p>Este es un correo automático generado por el sistema DX License Manager.</p>
        </div>
    </div>
</body>
</html>
