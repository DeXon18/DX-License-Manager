<!DOCTYPE html>
<html lang="{{ strtolower($data['Language'] ?? 'spanish') }}">
<head>
    <meta charset="UTF-8">
    <title>{{ $texts['document_title'] }}</title>
    <style>
        @font-face {
            font-family: 'Calibri';
            src: url("{{ resource_path('fonts/calibri/calibri.ttf') }}") format("truetype");
            font-weight: normal;
            font-style: normal;
        }
        @font-face {
            font-family: 'Calibri';
            src: url("{{ resource_path('fonts/calibri/calibrib.ttf') }}") format("truetype");
            font-weight: bold;
            font-style: normal;
        }
        @font-face {
            font-family: 'Calibri';
            src: url("{{ resource_path('fonts/calibri/calibrii.ttf') }}") format("truetype");
            font-weight: normal;
            font-style: italic;
        }
        @font-face {
            font-family: 'Calibri';
            src: url("{{ resource_path('fonts/calibri/calibriz.ttf') }}") format("truetype");
            font-weight: bold;
            font-style: italic;
        }

        body {
            font-family: 'Calibri', sans-serif;
            font-size: 12px;
            padding: 0 40px;
            color: #000;
        }

        .header-logo {
            text-align: right;
            margin-top: 20px;
            margin-bottom: 20px;
        }

        .header-logo img {
            width: 280px;
        }

        .siemens-address {
            font-weight: bold;
            line-height: 1.2;
            margin-bottom: 30px;
        }

        h1 {
            text-align: center;
            font-size: 17px;
            text-transform: uppercase;
            margin: 40px 0 20px 0;
        }

        p {
            font-size: 16px;
            line-height: 1.4;
            text-align: justify;
            margin-bottom: 15px;
        }

        .data-row {
            font-size: 16px;
            margin-bottom: 5px;
        }

        .data-label {
            font-weight: bold;
        }

        .signature-section {
            margin-top: 50px;
            font-size: 16px;
        }

        .signature-line {
            margin-top: 40px;
            border-top: 1px solid #000;
            width: 300px;
        }

        /* Case sensitivity for Linux */
        .case-sensitive {
            text-transform: {{ ($data['os'] ?? 'WINDOWS') === 'LINUX' ? 'none' : 'uppercase' }};
        }
    </style>
</head>
<body>
    <div class="header-logo">
        <img src="{{ public_path('assets/images/siemens-logo-pdf.png') }}" alt="Siemens Logo">
    </div>

    <div class="siemens-address">
        SIEMENS INDUSTRY SOFTWARE, S.L.<br>
        Lluís Muntadas 5-5ª planta<br>
        08940 Cornellà de Llobregat. (Barcelona)<br>
        Tel. : 93-900 35 41 19 / 900878880<br>
        Fax.: 93-510 22 85
    </div>

    <h1>{{ $texts['cessation_cert'] }}</h1>

    <p>{{ $texts['cessation_p1'] }}</p>
    <p>{{ $texts['cessation_p2'] }}</p>

    <div class="data-row">
        <span class="data-label">{{ $texts['sold_to'] }}:</span> {{ $data['Data_SoldTo'] }}
    </div>

    @if(($data['docType'] ?? '') === 'Change_Full' || ($data['docType'] ?? '') === 'Change_NodeLocked')
        <div class="data-row">
            <span class="data-label">{{ $texts['host_id'] }}:</span> 
            <span class="case-sensitive">{{ $data['MAC_Old'] ?? '' }}</span>
        </div>
    @endif

    @if(($data['docType'] ?? '') === 'Change_Full' || ($data['docType'] ?? '') === 'Change_Composite')
        <div class="data-row">
            <span class="data-label">{{ $texts['composite_id'] }}:</span> 
            <span class="case-sensitive">{{ $data['Composite_Old'] ?? '' }}</span>
        </div>
        <div class="data-row">
            <span class="data-label">{{ $texts['hostname'] }}:</span> 
            <span class="case-sensitive">{{ $data['Hostname_Old'] ?? '' }}</span>
        </div>
    @endif

    @if(isset($data['MAC_Old_Extra']) && is_array($data['MAC_Old_Extra']))
        @foreach($data['MAC_Old_Extra'] as $index => $mac)
            @if(!empty($mac))
                <div class="data-row">
                    <span class="data-label">{{ $texts['host_id'] }} {{ $index + 2 }}:</span> 
                    <span class="case-sensitive">{{ $mac }}</span>
                </div>
            @endif
        @endforeach
    @endif

    <h1>{{ $texts['company_cert'] }}</h1>

    <p>{{ $texts['company_p1'] }}</p>

    @if(($data['docType'] ?? '') === 'Change_Full' || ($data['docType'] ?? '') === 'Change_NodeLocked')
        <div class="data-row">
            <span class="data-label">{{ $texts['host_id_new'] }}:</span> 
            <span class="case-sensitive">{{ $data['MAC_New'] ?? '' }}</span>
        </div>
    @endif

    @if(($data['docType'] ?? '') === 'Change_Full' || ($data['docType'] ?? '') === 'Change_Composite')
        <div class="data-row">
            <span class="data-label">{{ $texts['composite_id'] }}:</span> 
            <span class="case-sensitive">{{ $data['Composite_New'] ?? '' }}</span>
        </div>
        <div class="data-row">
            <span class="data-label">{{ $texts['hostname'] }}:</span> 
            <span class="case-sensitive">{{ $data['Hostname_New'] ?? '' }}</span>
        </div>
    @endif

    @if(isset($data['MAC_New_Extra']) && is_array($data['MAC_New_Extra']))
        @foreach($data['MAC_New_Extra'] as $index => $mac)
            @if(!empty($mac))
                <div class="data-row">
                    <span class="data-label">{{ $texts['host_id_new'] }} {{ $index + 2 }}:</span> 
                    <span class="case-sensitive">{{ $mac }}</span>
                </div>
            @endif
        @endforeach
    @endif

    <div class="signature-section">
        <div class="data-row">
            <span class="data-label">{{ $texts['applicant'] }}:</span> {{ $data['Data_Solicitante'] }}
        </div>
        <div class="data-row">
            <span class="data-label">{{ $texts['company'] }}:</span> {{ $data['Data_Empresa'] }}
        </div>
        <div class="data-row">
            <span class="data-label">{{ $texts['date'] }}:</span> {{ $fecha }}
        </div>

        <div style="margin-top: 30px;">
            {{ $texts['signature'] }}
        </div>
        <div class="signature-line"></div>
    </div>
</body>
</html>
