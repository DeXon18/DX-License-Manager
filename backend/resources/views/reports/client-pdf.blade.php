<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Licencias - {{ $client->name }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            font-size: 12px;
            line-height: 1.5;
            margin: 0;
            padding: 0;
        }
        
        .header {
            border-bottom: 2px solid #0056b3;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .logo-placeholder {
            /* 
               AQUÍ VA EL LOGO:
               Cuando el usuario tenga el logo, puede cambiar este src por el base64 o ruta pública.
               Ejemplo: <img src="{{ public_path('assets/img/logo-empresa.png') }}" width="150">
            */
            width: 150px;
            height: 50px;
            background-color: #f0f0f0;
            border: 1px dashed #999;
            text-align: center;
            line-height: 50px;
            color: #666;
            font-size: 10px;
            display: inline-block;
        }

        .report-title {
            float: right;
            text-align: right;
        }

        .report-title h1 {
            margin: 0;
            color: #0056b3;
            font-size: 24px;
        }

        .report-title p {
            margin: 5px 0 0 0;
            color: #666;
        }

        .clear {
            clear: both;
        }

        .section-title {
            background-color: #f4f6f9;
            padding: 8px 12px;
            border-left: 4px solid #0056b3;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 15px;
            margin-top: 30px;
        }

        .summary-box {
            display: inline-block;
            width: 30%;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            padding: 15px;
            margin-right: 2%;
            box-sizing: border-box;
            text-align: center;
            border-radius: 4px;
        }

        .summary-box:last-child {
            margin-right: 0;
        }

        .summary-box .number {
            font-size: 24px;
            font-weight: bold;
            color: #0056b3;
            display: block;
            margin-bottom: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            border: 1px solid #e0e0e0;
            padding: 8px 10px;
            text-align: left;
            font-size: 11px;
        }

        th {
            background-color: #f4f6f9;
            color: #333;
            font-weight: bold;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .badge {
            padding: 3px 6px;
            border-radius: 3px;
            font-size: 9px;
            text-transform: uppercase;
        }

        .badge-active { background-color: #e6f4ea; color: #1e8e3e; }
        .badge-expired { background-color: #fce8e6; color: #d93025; }

        .footer {
            position: fixed;
            bottom: -30px;
            left: 0;
            right: 0;
            height: 30px;
            text-align: center;
            font-size: 10px;
            color: #999;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }

        .page-number:after {
            content: counter(page);
        }
    </style>
</head>
<body>

    <div class="header">
        <div class="logo-placeholder">
            [LOGO EMPRESA]
        </div>
        <div class="report-title">
            <h1>Reporte de Licencias</h1>
            <p><strong>Cliente:</strong> {{ $client->name }}</p>
            <p><strong>Fecha Generación:</strong> {{ date('d/m/Y') }}</p>
        </div>
        <div class="clear"></div>
    </div>

    <!-- Summary -->
    <div style="margin-bottom: 20px;">
        <div class="summary-box">
            <span class="number">{{ $total_active_seats }}</span>
            Asientos Activos (.lic)
        </div>
        <div class="summary-box">
            <span class="number">{{ $total_daemons }}</span>
            Servidores (Daemons)
        </div>
        <div class="summary-box" style="margin-right: 0;">
            <span class="number">{{ count($contracts) }}</span>
            Contratos Comerciales
        </div>
    </div>

    <!-- Top Products for this client -->
    <div class="section-title">Resumen de Productos Asignados</div>
    @if(count($grouped_products) > 0)
    <table>
        <thead>
            <tr>
                <th>Producto (Feature)</th>
                <th class="text-center">Asientos Activos</th>
                <th>Próximas Expiraciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($grouped_products as $code => $data)
            <tr>
                <td><strong>{{ $code }}</strong></td>
                <td class="text-center">{{ $data['quantity'] }}</td>
                <td>
                    @if(count($data['expirations']) > 0)
                        {{ implode(', ', array_map(function($date) { return \Carbon\Carbon::parse($date)->format('d/m/Y'); }, $data['expirations']->toArray())) }}
                    @else
                        Permanente
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <p>No se encontraron productos activos registrados en servidores de licencias.</p>
    @endif

    <!-- Contracts -->
    <div class="section-title">Detalle de Contratos (CSV)</div>
    @if(count($contracts) > 0)
    <table>
        <thead>
            <tr>
                <th>Número de Contrato</th>
                <th>Producto Base</th>
                <th>Centro de Coste</th>
                <th>Fecha de Fin</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($contracts as $contract)
            <tr>
                <td>{{ $contract->contract_number }}</td>
                <td>{{ $contract->type_product ?: '-' }}</td>
                <td>{{ $contract->cost_center ?: '-' }}</td>
                <td>{{ $contract->end_date ? \Carbon\Carbon::parse($contract->end_date)->format('d/m/Y') : '-' }}</td>
                <td>
                    @if($contract->status == 'activo')
                        <span class="badge badge-active">Activo</span>
                    @else
                        <span class="badge badge-expired">{{ $contract->status }}</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <p>No se encontraron contratos registrados para este cliente.</p>
    @endif

    <!-- Inventory Details -->
    <div class="section-title">Inventario Técnico (Daemons & Hosts)</div>
    @if(count($client->inventoryDaemons) > 0)
        @foreach($client->inventoryDaemons as $daemon)
            <div style="background-color: #fbfbfb; border: 1px solid #eee; padding: 10px; margin-bottom: 15px;">
                <p style="margin: 0 0 10px 0; font-size: 12px;">
                    <strong>Servidor:</strong> {{ $daemon->hostname ?? 'Desconocido' }} | 
                    <strong>ID Físico:</strong> {{ $daemon->composite_id ?? ($daemon->mac_address ?? 'Cualquiera') }} | 
                    <strong>Vendor:</strong> {{ $daemon->vendor_daemon }}
                </p>
                
                @if(count($daemon->products) > 0)
                    <table>
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th class="text-center">Cant.</th>
                                <th>MAC (Node-locked)</th>
                                <th>Expiración</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($daemon->products as $product)
                            <tr>
                                <td>{{ $product->product_code }}</td>
                                <td class="text-center">{{ $product->quantity }}</td>
                                <td>{{ $product->node_locked_host_id ?: '-' }}</td>
                                <td>{{ $product->expiration_date ? \Carbon\Carbon::parse($product->expiration_date)->format('d/m/Y') : 'Permanente' }}</td>
                                <td>
                                    @if($product->status == 'active')
                                        <span class="badge badge-active">Activo</span>
                                    @else
                                        <span class="badge badge-expired">Expirado</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p style="margin:0; color:#999; font-style:italic;">Sin licencias asignadas.</p>
                @endif
            </div>
        @endforeach
    @else
    <p>No hay servidores de licencias registrados.</p>
    @endif

    <div class="footer">
        Generado por DX License Manager &copy; {{ date('Y') }} | Página <span class="page-number"></span>
    </div>

</body>
</html>
