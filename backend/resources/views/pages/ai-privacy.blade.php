@extends('layouts.app')

@section('content')
<div class="dx-v2-page-header">
    <div>
        <div class="breadcrumb">
            <a href="/">Inicio</a>
            <span class="separator">/</span>
            <span class="current">Privacidad IA</span>
        </div>
        <h1 class="page-title">Arquitectura de <span>Inteligencia Artificial</span></h1>
        <p class="page-subtitle">Declaración de privacidad, seguridad y soberanía de datos (Zero-Data Retention).</p>
    </div>
</div>

<div class="dashboard-container">
    <div class="dx-v2-privacy-bento-grid">
        
        <!-- Hero: Compromiso y Cero Retención -->
        <div class="card dx-v2-privacy-hero-panel">
            <div class="dx-v2-privacy-hero-content">
                <h1 class="dx-v2-privacy-header-title">Arquitectura de Privacidad Zero-Data</h1>
                <p class="dx-v2-privacy-header-subtitle mb-16">
                    En <strong>DX License Manager</strong>, entendemos que la información gestionada sobre licencias, hardware industrial y datos de clientes es estrictamente confidencial. Por ello, nuestra integración de Inteligencia Artificial ha sido diseñada desde cero priorizando la <strong>seguridad perimetral</strong> y la <strong>protección absoluta de la propiedad intelectual</strong>.
                </p>
                <p class="dx-v2-privacy-header-subtitle">
                    Toda la información procesada se elimina de forma inmediata de sus servidores temporales tras procesar la petición. <strong>Ningún prompt, dato corporativo ni resultado generado es utilizado para entrenar los modelos base.</strong>
                </p>
            </div>
            <div class="dx-v2-privacy-hero-sla">
                <ul class="dx-v2-privacy-sla-list">
                    <li class="dx-v2-privacy-sla-item">
                        <span class="dx-v2-privacy-sla-label"><i class="fa-solid fa-server color-success"></i> Modelo Principal</span>
                        <span class="dx-v2-privacy-sla-value">deepseek-chat</span>
                    </li>
                    <li class="dx-v2-privacy-sla-item">
                        <span class="dx-v2-privacy-sla-label"><i class="fa-solid fa-network-wired color-accent"></i> Proveedor Fallback</span>
                        <span class="dx-v2-privacy-sla-value">OpenRouter</span>
                    </li>
                    <li class="dx-v2-privacy-sla-item">
                        <span class="dx-v2-privacy-sla-label"><i class="fa-solid fa-shield-halved color-success"></i> Política Entrenamiento</span>
                        <span class="dx-v2-privacy-sla-value color-success">Zero-Retention</span>
                    </li>
                    <li class="dx-v2-privacy-sla-item">
                        <span class="dx-v2-privacy-sla-label"><i class="fa-solid fa-earth-europe color-siemens"></i> Residencia Datos</span>
                        <span class="dx-v2-privacy-sla-value">EU / US (GDPR)</span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- 50% Panel: Aislamiento -->
        <div class="card dx-v2-privacy-half-panel">
            <h2 class="dx-v2-privacy-panel-title">
                <i class="fa-solid fa-database color-accent"></i> Aislamiento Estructural
            </h2>
            <p class="dx-v2-privacy-header-subtitle">
                La Inteligencia Artificial <strong>no tiene acceso directo ni persistente</strong> a nuestra base de datos. Opera en un entorno completamente aislado (<em>sandbox</em>). Nuestro backend (controlado internamente) inyecta únicamente el contexto efímero y estrictamente necesario para resolver cada petición técnica, evitando cualquier exposición masiva o volcado de registros de clientes, contratos o facturación.
            </p>
        </div>

        <!-- 50% Panel: Cifrado y Resiliencia -->
        <div class="card dx-v2-privacy-half-panel">
            <h2 class="dx-v2-privacy-panel-title">
                <i class="fa-solid fa-lock color-purple"></i> Cifrado Extremo a Extremo
            </h2>
            <p class="dx-v2-privacy-header-subtitle">
                Toda transferencia entre el sistema y las APIs Enterprise ocurre cifrada bajo <strong>TLS 1.2/1.3</strong>, impidiendo la interceptación o ataques <em>Man-in-the-Middle</em> de los metadatos corporativos en tránsito. Implementamos una cadena de seguridad anti-caídas (Fallback) que cambia de modelo automáticamente sin comprometer los datos si un proveedor falla.
            </p>
        </div>

        <!-- 100% Panel: Pipeline de Archivos -->
        <div class="card dx-v2-privacy-full-panel">
            <h2 class="dx-v2-privacy-panel-title">
                <i class="fa-solid fa-file-shield color-warning"></i> Protección de Archivos Físicos (.lic / .mac)
            </h2>
            <p class="dx-v2-privacy-header-subtitle mb-24">
                Los archivos originales de licencia <strong>jamás se transmiten enteros a la nube</strong>. Nuestra arquitectura híbrida funciona en dos capas mediante extracción local previa.
            </p>
            
            <div class="dx-v2-pipeline-container">
                <div class="dx-v2-pipeline-node">
                    <i class="fa-regular fa-file-code"></i> Archivo Original (.lic / .mac)
                </div>
                
                <div class="dx-v2-pipeline-arrow">
                    <i class="fa-solid fa-arrow-down"></i>
                    <span class="dx-v2-pipeline-arrow-label">Procesamiento Local</span>
                </div>
                
                <div class="dx-v2-pipeline-node dark">
                    <i class="fa-solid fa-microchip"></i> Servidor Interno (RegExp/PHP)
                </div>
                
                <div class="dx-v2-pipeline-arrow">
                    <i class="fa-solid fa-arrow-down"></i>
                    <span class="dx-v2-pipeline-arrow-label">Solo Metadatos (TLS 1.3)</span>
                </div>
                
                <div class="dx-v2-pipeline-node">
                    <i class="fa-solid fa-cloud"></i> Análisis Semántico IA
                </div>
            </div>
        </div>

        <!-- Tercios: Superficies de Contacto -->
        <div class="card dx-v2-privacy-third-panel">
            <h2 class="dx-v2-privacy-panel-title">
                <i class="fa-solid fa-magnifying-glass-chart color-accent"></i> Herramientas Auditoría
            </h2>
            <p class="dx-v2-privacy-header-subtitle mb-16" style="font-size: 13px;">Convierte archivos en inventario estructurado.</p>
            <ul class="dx-v2-surface-list">
                <li class="dx-v2-surface-item"><i class="fa-solid fa-check dx-v2-surface-icon-ok"></i> <span><strong>Recibe:</strong> Metadatos extraídos localmente — fechas, códigos, hostids.</span></li>
                <li class="dx-v2-surface-item"><i class="fa-solid fa-xmark dx-v2-surface-icon-no"></i> <span class="color-danger"><strong>NO recibe:</strong> El archivo original, datos de cliente, contratos, facturación.</span></li>
                <li class="dx-v2-surface-item"><i class="fa-solid fa-certificate color-accent" style="margin-top: 2px;"></i> <span class="color-muted"><strong>Garantía:</strong> Zero-Data Retention verificada contractualmente.</span></li>
            </ul>
        </div>

        <div class="card dx-v2-privacy-third-panel">
            <h2 class="dx-v2-privacy-panel-title">
                <i class="fa-solid fa-layer-group color-accent"></i> Normalización Clientes
            </h2>
            <p class="dx-v2-privacy-header-subtitle mb-16" style="font-size: 13px;">Detecta clientes duplicados y corrige errores tipográficos.</p>
            <ul class="dx-v2-surface-list">
                <li class="dx-v2-surface-item"><i class="fa-solid fa-check dx-v2-surface-icon-ok"></i> <span><strong>Recibe:</strong> Listas ciegas de nombres desvinculadas de facturación.</span></li>
                <li class="dx-v2-surface-item"><i class="fa-solid fa-xmark dx-v2-surface-icon-no"></i> <span class="color-danger"><strong>NO recibe:</strong> Sold-To, contratos, datos económicos.</span></li>
                <li class="dx-v2-surface-item"><i class="fa-solid fa-microchip color-accent" style="margin-top: 2px;"></i> <span class="color-muted"><strong>Método:</strong> Fuzzy Matching heurístico + validación semántica IA.</span></li>
            </ul>
        </div>

        <div class="card dx-v2-privacy-third-panel">
            <h2 class="dx-v2-privacy-panel-title">
                <i class="fa-solid fa-robot color-accent"></i> Asistente Virtual
            </h2>
            <p class="dx-v2-privacy-header-subtitle mb-16" style="font-size: 13px;">Responder consultas sobre inventario en lenguaje natural.</p>
            <ul class="dx-v2-surface-list">
                <li class="dx-v2-surface-item"><i class="fa-solid fa-check dx-v2-surface-icon-ok"></i> <span><strong>Recibe:</strong> Contexto efímero de la sesión activa.</span></li>
                <li class="dx-v2-surface-item"><i class="fa-solid fa-xmark dx-v2-surface-icon-no"></i> <span class="color-danger"><strong>NO recibe:</strong> Historial completo de BD, datos de otros usuarios.</span></li>
            </ul>
        </div>

        <!-- Compliance Banner -->
        <div class="dx-v2-privacy-compliance-banner">
            <i class="fa-solid fa-certificate color-accent" style="font-size: 24px;"></i>
            <div>
                <p>
                    <strong>Cumplimiento y Auditoría:</strong> Si el departamento de <strong>Compliance</strong> o <strong>Dirección IT</strong> requiere revisar los anexos de privacidad de los proveedores subyacentes, los DPA (<em>Data Processing Agreements</em>) están disponibles previa solicitud a la administración del sistema.
                </p>
            </div>
        </div>

    </div>
</div>
@endsection
