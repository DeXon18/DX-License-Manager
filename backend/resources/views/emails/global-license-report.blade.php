<!DOCTYPE html>
<html lang="es" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="x-apple-disable-message-reformatting">
    <title>DX License Manager — Reporte Global de Caducidad</title>
    <!--[if mso]>
    <noscript>
        <xml>
            <o:OfficeDocumentSettings>
                <o:PixelsPerInch>96</o:PixelsPerInch>
            </o:OfficeDocumentSettings>
        </xml>
    </noscript>
    <![endif]-->
    <style>
        /* Reset */
        body, table, td, p, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; border-collapse: collapse; }
        img { -ms-interpolation-mode: bicubic; border: 0; outline: none; }
        /* Responsive */
        @media screen and (max-width: 600px) {
            .email-wrapper { width: 100% !important; }
            .client-block { width: 100% !important; }
        }
    </style>
</head>
<body style="margin: 0; padding: 0; background-color: #f0f4f8; font-family: Arial, Helvetica, sans-serif;">

<!--[if mso | IE]><table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color:#f0f4f8;"><tr><td><![endif]-->

<!-- Wrapper -->
<table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f0f4f8;">
    <tr>
        <td align="center" style="padding: 30px 15px;">

            <!-- Email container -->
            <table class="email-wrapper" role="presentation" border="0" cellpadding="0" cellspacing="0" width="620" style="background-color: #ffffff; border: 1px solid #dde3ea;">

                <!-- ===== HEADER ===== -->
                <tr>
                    <td style="background-color: #007a7a; padding: 0;">
                        <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                            <tr>
                                <td style="padding: 24px 30px;">
                                    <!-- Logo / Title row -->
                                    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                                        <tr>
                                            <td>
                                                <p style="margin: 0; font-family: Arial, Helvetica, sans-serif; font-size: 11px; font-weight: bold; letter-spacing: 0.12em; text-transform: uppercase; color: #a8e6e6;">DX LICENSE MANAGER</p>
                                                <p style="margin: 6px 0 0 0; font-family: Georgia, 'Times New Roman', serif; font-size: 22px; font-weight: bold; color: #ffffff; line-height: 1.2;">Reporte Global de Caducidad</p>
                                            </td>
                                            <td align="right" valign="middle">
                                                <p style="margin: 0; font-family: Arial, Helvetica, sans-serif; font-size: 11px; color: #a8e6e6; white-space: nowrap;">Lunes · 07:30 AM</p>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <!-- Accent bar -->
                            <tr>
                                <td style="background-color: #005f5f; height: 4px; font-size: 0; line-height: 0;">&nbsp;</td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <!-- ===== INTRO ===== -->
                <tr>
                    <td style="padding: 24px 30px 10px 30px;">
                        <p style="margin: 0; font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #3d4d5c; line-height: 1.6;">
                            Hola,<br><br>
                            A continuación se detallan las licencias que requieren atención esta semana, agrupadas por cliente y nivel de urgencia.
                        </p>
                    </td>
                </tr>

                <!-- ===== CLIENT BLOCKS ===== -->
                @foreach($reportData as $data)
                <tr>
                    <td style="padding: 12px 30px;">

                        <!-- Client block wrapper -->
                        <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="border: 1px solid #dde3ea;">

                            <!-- Client header -->
                            <tr>
                                <td bgcolor="#eaf6f6" style="background-color: #eaf6f6; padding: 12px 18px; border-bottom: 2px solid #009999;">
                                    <p style="margin: 0; font-family: Arial, Helvetica, sans-serif; font-size: 15px; font-weight: bold; color: #007a7a;">
                                        {{ $data['client']->name }}
                                    </p>
                                </td>
                            </tr>

                            <tr>
                                <td style="padding: 16px 18px;">

                                    {{-- ---- ALERTA CRÍTICA ---- --}}
                                    @if($data['expiring']['alerta']->isNotEmpty())
                                    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom: 18px;">
                                        <tr>
                                            <td style="padding: 0 0 8px 0; border-bottom: 1px solid #fecaca;">
                                                <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                                                    <tr>
                                                        <td style="background-color: #fee2e2; padding: 3px 8px;">
                                                            <p style="margin: 0; font-family: Arial, Helvetica, sans-serif; font-size: 11px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.08em; color: #b91c1c;">&#9888; Alerta Crítica — 0 a 7 días</p>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding-top: 8px;">
                                                <!-- Data table -->
                                                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                                                    <tr>
                                                        <td style="font-family: Arial, Helvetica, sans-serif; font-size: 10px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.06em; color: #9ca3af; padding: 4px 8px 4px 0; width: 40%;">Producto</td>
                                                        <td style="font-family: Arial, Helvetica, sans-serif; font-size: 10px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.06em; color: #9ca3af; padding: 4px 8px; width: 40%;">Sold-To / Host ID</td>
                                                        <td style="font-family: Arial, Helvetica, sans-serif; font-size: 10px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.06em; color: #9ca3af; padding: 4px 0 4px 8px; width: 20%;">Vence</td>
                                                    </tr>
                                                    @foreach($data['expiring']['alerta'] as $item)
                                                    <tr style="background-color: #fff5f5;">
                                                        <td style="font-family: Arial, Helvetica, sans-serif; font-size: 13px; font-weight: bold; color: #1a202c; padding: 7px 8px 7px 0; border-top: 1px solid #fee2e2;">{{ $item->product_code }}</td>
                                                        <td style="font-family: 'Courier New', Courier, monospace; font-size: 11px; color: #4b5563; padding: 7px 8px; border-top: 1px solid #fee2e2;">
                                                            {{ $item->daemon->sold_to }}<br>
                                                            <span style="color: #9ca3af; font-size: 10px;">{{ $item->node_locked_host_id ?? 'Floating' }}</span>
                                                        </td>
                                                        <td style="font-family: 'Courier New', Courier, monospace; font-size: 12px; color: #b91c1c; font-weight: bold; padding: 7px 0 7px 8px; border-top: 1px solid #fee2e2; white-space: nowrap;">{{ $item->expiration_date->format('d/m/Y') }}</td>
                                                    </tr>
                                                    @endforeach
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                    @endif

                                    {{-- ---- AVISO ---- --}}
                                    @if($data['expiring']['aviso']->isNotEmpty())
                                    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom: 18px;">
                                        <tr>
                                            <td style="padding: 0 0 8px 0; border-bottom: 1px solid #fed7aa;">
                                                <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                                                    <tr>
                                                        <td style="background-color: #ffedd5; padding: 3px 8px;">
                                                            <p style="margin: 0; font-family: Arial, Helvetica, sans-serif; font-size: 11px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.08em; color: #b45309;">&#128276; Aviso — 7 a 15 días</p>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding-top: 8px;">
                                                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                                                    <tr>
                                                        <td style="font-family: Arial, Helvetica, sans-serif; font-size: 10px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.06em; color: #9ca3af; padding: 4px 8px 4px 0; width: 40%;">Producto</td>
                                                        <td style="font-family: Arial, Helvetica, sans-serif; font-size: 10px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.06em; color: #9ca3af; padding: 4px 8px; width: 40%;">Sold-To / Host ID</td>
                                                        <td style="font-family: Arial, Helvetica, sans-serif; font-size: 10px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.06em; color: #9ca3af; padding: 4px 0 4px 8px; width: 20%;">Vence</td>
                                                    </tr>
                                                    @foreach($data['expiring']['aviso'] as $item)
                                                    <tr style="background-color: #fffbf5;">
                                                        <td style="font-family: Arial, Helvetica, sans-serif; font-size: 13px; font-weight: bold; color: #1a202c; padding: 7px 8px 7px 0; border-top: 1px solid #fed7aa;">{{ $item->product_code }}</td>
                                                        <td style="font-family: 'Courier New', Courier, monospace; font-size: 11px; color: #4b5563; padding: 7px 8px; border-top: 1px solid #fed7aa;">
                                                            {{ $item->daemon->sold_to }}<br>
                                                            <span style="color: #9ca3af; font-size: 10px;">{{ $item->node_locked_host_id ?? 'Floating' }}</span>
                                                        </td>
                                                        <td style="font-family: 'Courier New', Courier, monospace; font-size: 12px; color: #b45309; font-weight: bold; padding: 7px 0 7px 8px; border-top: 1px solid #fed7aa; white-space: nowrap;">{{ $item->expiration_date->format('d/m/Y') }}</td>
                                                    </tr>
                                                    @endforeach
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                    @endif

                                    {{-- ---- RECORDATORIO ---- --}}
                                    @if($data['expiring']['recordatorio']->isNotEmpty())
                                    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                                        <tr>
                                            <td style="padding: 0 0 8px 0; border-bottom: 1px solid #bfdbfe;">
                                                <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                                                    <tr>
                                                        <td style="background-color: #dbeafe; padding: 3px 8px;">
                                                            <p style="margin: 0; font-family: Arial, Helvetica, sans-serif; font-size: 11px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.08em; color: #1d4ed8;">&#128203; Recordatorio — 15 a 30 días</p>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding-top: 8px;">
                                                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                                                    <tr>
                                                        <td style="font-family: Arial, Helvetica, sans-serif; font-size: 10px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.06em; color: #9ca3af; padding: 4px 8px 4px 0; width: 40%;">Producto</td>
                                                        <td style="font-family: Arial, Helvetica, sans-serif; font-size: 10px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.06em; color: #9ca3af; padding: 4px 8px; width: 40%;">Sold-To / Host ID</td>
                                                        <td style="font-family: Arial, Helvetica, sans-serif; font-size: 10px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.06em; color: #9ca3af; padding: 4px 0 4px 8px; width: 20%;">Vence</td>
                                                    </tr>
                                                    @foreach($data['expiring']['recordatorio'] as $item)
                                                    <tr style="background-color: #f8faff;">
                                                        <td style="font-family: Arial, Helvetica, sans-serif; font-size: 13px; font-weight: bold; color: #1a202c; padding: 7px 8px 7px 0; border-top: 1px solid #dbeafe;">{{ $item->product_code }}</td>
                                                        <td style="font-family: 'Courier New', Courier, monospace; font-size: 11px; color: #4b5563; padding: 7px 8px; border-top: 1px solid #dbeafe;">
                                                            {{ $item->daemon->sold_to }}<br>
                                                            <span style="color: #9ca3af; font-size: 10px;">{{ $item->node_locked_host_id ?? 'Floating' }}</span>
                                                        </td>
                                                        <td style="font-family: 'Courier New', Courier, monospace; font-size: 12px; color: #1d4ed8; font-weight: bold; padding: 7px 0 7px 8px; border-top: 1px solid #dbeafe; white-space: nowrap;">{{ $item->expiration_date->format('d/m/Y') }}</td>
                                                    </tr>
                                                    @endforeach
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                    @endif

                                </td>
                            </tr>
                        </table>
                        <!-- /client block -->

                    </td>
                </tr>
                @endforeach

                <!-- ===== CTA BUTTON ===== -->
                <tr>
                    <td align="center" style="padding: 24px 30px 28px 30px;">
                        <!--[if mso]>
                        <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word"
                            href="{{ url('/admin/alerts') }}"
                            style="height:44px; v-text-anchor:middle; width:260px;"
                            arcsize="0%"
                            fillcolor="#007a7a"
                            strokecolor="#007a7a">
                            <w:anchorlock/>
                            <center style="color:#ffffff; font-family:Arial,sans-serif; font-size:14px; font-weight:bold;">
                                Gestionar Alertas en el Portal
                            </center>
                        </v:roundrect>
                        <![endif]-->
                        <!--[if !mso]><!-->
                        <a href="{{ url('/admin/alerts') }}"
                           style="display: inline-block; background-color: #007a7a; color: #ffffff; font-family: Arial, Helvetica, sans-serif; font-size: 14px; font-weight: bold; text-decoration: none; padding: 12px 32px; mso-hide: all;">
                            Gestionar Alertas en el Portal
                        </a>
                        <!--<![endif]-->
                    </td>
                </tr>

                <!-- ===== FOOTER ===== -->
                <tr>
                    <td bgcolor="#f0f4f8" style="background-color: #f0f4f8; padding: 18px 30px; border-top: 1px solid #dde3ea;">
                        <p style="margin: 0; font-family: Arial, Helvetica, sans-serif; font-size: 11px; color: #8a97a6; text-align: center; line-height: 1.7;">
                            Este reporte se genera automáticamente cada lunes a las 07:30 AM.<br>
                            &copy; 2026 <strong style="color: #6b7a8a;">DX License Manager</strong> &middot; Soporte Técnico AYS
                        </p>
                    </td>
                </tr>

            </table>
            <!-- /email container -->

        </td>
    </tr>
</table>

<!--[if mso | IE]></td></tr></table><![endif]-->

</body>
</html>