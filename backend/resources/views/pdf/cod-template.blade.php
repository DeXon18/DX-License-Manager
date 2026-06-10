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
            font-size: 11px;
            padding: 36px 51px 0 51px;
            color: #000;
        }

        .header-logo {
            text-align: right;
            margin-top: 0;
            margin-bottom: 25px; /* 36 top + 45 logo + 25 margin = 106px para el address */
        }

        .header-logo img {
            width: 230px;
        }

        .siemens-address {
            font-weight: bold;
            line-height: 1.3;
            margin-bottom: 10px;
            font-size: 11px;
        }

        h1 {
            text-align: center;
            font-size: 14px;
            text-transform: uppercase;
            margin: 30px 0 20px 0;
        }

        p {
            font-size: 12px;
            line-height: 1.5;
            text-align: justify;
            margin-bottom: 8px;
        }

        .data-row {
            font-size: 12px;
            margin-bottom: 0;
            line-height: 1.3;
        }

        .sold-to-row {
            margin-bottom: 15px;
        }

        .data-label {
            font-weight: bold;
        }

        .signature-section {
            margin-top: 30px;
            font-size: 12px;
            page-break-inside: avoid;
        }

        .signature-line {
            margin-top: 36px;
            border-top: 1px solid #000;
            width: 171px;
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
    <p style="margin-bottom: 20px;">{{ $texts['cessation_p2'] }}</p>

    <div class="data-row sold-to-row">
        <span class="data-label">{{ $texts['sold_to'] }}:</span> {{ $data['Data_SoldTo'] }}
    </div>

    @if(($data['docType'] ?? '') === 'Change_Full' || ($data['docType'] ?? '') === 'Change_NodeLocked')
        <div class="data-row">
            <span class="data-label">{{ $texts['host_id'] }}:</span> 
            <span class="case-sensitive">{{ $data['MAC_Old'] ?? '' }}</span>
        </div>
    @endif

    @if(($data['docType'] ?? '') === 'Change_Full' || ($data['docType'] ?? '') === 'Change_Composite' || ($data['docType'] ?? '') === 'Change_Cloud')
        @if(($data['docType'] ?? '') !== 'Change_Cloud')
            <div class="data-row">
                <span class="data-label">{{ $texts['composite_id'] }}:</span> 
                <span class="case-sensitive">{{ $data['Composite_Old'] ?? '' }}</span>
            </div>
        @endif
        <div class="data-row">
            <span class="data-label">{{ $texts['hostname'] }}:</span> 
            <span class="case-sensitive">{{ $data['Hostname_Old'] ?? '' }}</span>
        </div>
        @if(!empty($data['Cloud_AWS_Old']))
            <div class="data-row">
                <span class="data-label">{{ $texts['cloud_aws'] }}:</span> 
                <span class="case-sensitive">{{ $data['Cloud_AWS_Old'] }}</span>
            </div>
        @endif
        @if(!empty($data['Cloud_Azure_Old']))
            <div class="data-row">
                <span class="data-label">{{ $texts['cloud_azure'] }}:</span> 
                <span class="case-sensitive">{{ $data['Cloud_Azure_Old'] }}</span>
            </div>
        @endif
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

    <p style="margin-bottom: 20px;">{{ $texts['company_p1'] }}</p>

    @if(($data['docType'] ?? '') === 'Change_Full' || ($data['docType'] ?? '') === 'Change_NodeLocked')
        <div class="data-row">
            <span class="data-label">{{ $texts['host_id_new'] }}:</span> 
            <span class="case-sensitive">{{ $data['MAC_New'] ?? '' }}</span>
        </div>
    @endif

    @if(($data['docType'] ?? '') === 'Change_Full' || ($data['docType'] ?? '') === 'Change_Composite' || ($data['docType'] ?? '') === 'Change_Cloud')
        <div class="data-row">
            <span class="data-label">{{ $texts['composite_id'] }}:</span> 
            <span class="case-sensitive">{{ $data['Composite_New'] ?? '' }}</span>
        </div>
        <div class="data-row">
            <span class="data-label">{{ $texts['hostname'] }}:</span> 
            <span class="case-sensitive">{{ $data['Hostname_New'] ?? '' }}</span>
        </div>
        @if(!empty($data['Cloud_AWS_New']))
            <div class="data-row">
                <span class="data-label">{{ $texts['cloud_aws'] }}:</span> 
                <span class="case-sensitive">{{ $data['Cloud_AWS_New'] }}</span>
            </div>
        @endif
        @if(!empty($data['Cloud_Azure_New']))
            <div class="data-row">
                <span class="data-label">{{ $texts['cloud_azure'] }}:</span> 
                <span class="case-sensitive">{{ $data['Cloud_Azure_New'] }}</span>
            </div>
        @endif
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

        <div style="margin-top: 50px;">
            {{ $texts['signature'] }}
        </div>
        <div class="signature-line"></div>
    </div>
</body>
</html>
