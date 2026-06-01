# 🛡️ DX License Manager — NOC Pro Edition

<p align="center">
  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="200" alt="Laravel 11">
  <br>
  <strong>Portal Empresarial de Gestión de Licencias & Auditoría IA</strong>
  <br>
  <em>Filosofía Impeccable — Minimalismo funcional de alta densidad para entornos de red.</em>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Version-2.8.0-388BFD?style=for-the-badge" alt="Version">
  <img src="https://img.shields.io/badge/Stack-Laravel_11_|_PHP_8.4-FF2D20?style=for-the-badge&logo=laravel" alt="Laravel">
  <img src="https://img.shields.io/badge/Engine-AI_Gemini_Flash-9171ff?style=for-the-badge" alt="AI Gemini Engine">
  <img src="https://img.shields.io/badge/CSS-Modular_35_Files-007aff?style=for-the-badge" alt="CSS Modular architecture">
</p>

---

## 📖 Visión General

**DX License Manager** es un **Centro de Operaciones de Red (NOC)** de nivel empresarial diseñado para la gestión técnica, auditoría y control de licencias industriales **Siemens PLM** y **Moldex3D**.

A través de una interfaz industrial de alta densidad visual y un motor de inteligencia artificial robusto, el portal unifica la gobernanza de activos, predice ciclos de renovación, automatiza auditorías complejas y monitoriza la infraestructura crítica en tiempo real.

---

## 🏛️ Pilares Tecnológicos & Arquitectura

La plataforma está construida sobre una arquitectura modular de alta disponibilidad, priorizando la resiliencia operativa y la eficiencia en el procesamiento de datos:

- **Núcleo de Orquestación (Backend):** Desarrollado en **Laravel 11.x** (sobre PHP 8.4). El framework opera con patrones de diseño defensivos, inyección de dependencias estricta y Eloquent ORM altamente optimizado mediante *Eager Loading* y *Query Caching*.
- **Motor de Inteligencia Artificial & NLU:** Pipeline cognitivo híbrido con **Cortocircuito Heurístico** (para evasión de gasto innecesario de tokens). Integra **Google Gemini** de forma nativa para análisis conversacional, composite matching y normalización en tiempo real; y despliega **n8n Workflow Engine** como orquestador asíncrono impulsando modelos alternativos (DeepSeek R1 / OpenRouter) para las auditorías masivas de FlexLM, asegurando alta disponibilidad.
- **Blindaje y Zero-Trust Security:** Arquitectura basada en el principio de mínimo privilegio mediante un sistema RBAC granular. Incorpora mitigación nativa CSRF/XSS, inyección de cabeceras de seguridad estrictas (CSP Nivel 3, HSTS) y verificación criptográfica HMAC asimétrica para la validación de webhooks externos.
- **Micro-Frontend Reactivo:** Despliegue de vistas SSR (Server-Side Rendering) a través de Blade, enriquecidas dinámicamente con **Alpine.js**. Esto elimina la sobrecarga de Virtual DOMs (React/Vue), garantizando una huella de memoria nula en el cliente y latencia ultra-baja (incluyendo sistemas de Onboarding contextual on-the-fly mediante Driver.js).
- **State Management & Colas Distribuidas:** Clúster **Redis** actuando como capa de caché de alta velocidad y Broker de Mensajería. Gestiona colas de trabajos asíncronos para la telemetría, y mantiene un sistema de listas negras (ZSET) para invalidación criptográfica instantánea de tokens JWT revocados.
- **Capa de Persistencia:** Base de datos **MariaDB** con modelado estrictamente normalizado e índices optimizados, diseñada para la trazabilidad inmutable de *Machine IDs*, *Sold-Tos* y mapeo de identidades de clientes corporativos.

---

## 🎨 Sistema de Diseño & Arquitectura CSS (DX-V2)

El portal implementa una **arquitectura CSS modular de 6 capas** centralizada en `resources/css/` y compilada en `dx-v2-main.css`. Cuenta con un tema oscuro industrial premium basado en colores **HSL adaptativos**, transiciones aceleradas por hardware y glassmorphism.

```
Capa 1: Tokens & Base    ───► dx-v2-tokens.css · dx-v2-reset.css · dx-v2-base.css (Variables HSL, Keyframes)
Capa 2: Layout           ───► Estructural (dx-v2-nav.css · dx-v2-sidebar.css · dx-v2-breadcrumb.css · footer.css)
Capa 3: Atoms UI         ───► Componentes compartidos (cards.css · tables.css · badges.css · modals.css · toast.css)
Capa 4: App Modules      ───► Estilos de vistas (login.css · dashboard.css · clients.css · users.css · logs.css)
Capa 5: Technical Tools  ───► Herramientas de vendors (tools-hub.css · nx.css · star.css · heeds.css · moldex.css)
Capa 6: Special Pages    ───► Vistas independientes (page-herramientas.css · admin.css · page-maintenance.css)
```

### 🏷️ Identidad de Marca por Vendor

El portal adapta dinámicamente sus acentos de color de manera ergonómica según el fabricante seleccionado:

| Vendor          | Primary Color    | Hover State | CSS Class                 | Propósito Principal                      |
| :-------------- | :--------------- | :---------- | :------------------------ | :--------------------------------------- |
| **Siemens PLM** | `#009999` (Teal) | `#007A7A`   | `.theme-teal` / `.accent` | Entornos NX Suite, STAR-CCM+, HEEDS, COD |
| **Moldex3D**    | `#ED1C24` (Red)  | `#C41520`   | `.theme-red` / `.danger`  | Auditoría de licencias Moldex3D (`.mac`) |

---

## 🚀 Módulos Funcionales

### 1. AI License Auditor

Auditoría y extracción automatizada de metadatos en archivos de licencia FlexLM (`.lic`) y Moldex (`.mac`).

- **Normalización Estricta:** Detección de Hostnames, COMPOSITE, Machine IDs, deamons (`ugslmd`, `cdlmd`, `RCTECH`, `saltd`) e incrementos de asientos.
- **Cortocircuito Temporal:** Ahorro proactivo de tokens bloqueando la llamada a la IA en licencias temporales de 7 días (aquellas con _ANY_ o _YourHostname_).
- **Gobernanza de Privacidad:** Los archivos `.lic` subidos por los clientes **nunca** se guardan físicamente en el servidor (`file_path` = `NULL` en base de datos). Solo se persisten los metadatos de auditoría estructurados.

### 2. Generador de Certificados de Cese (COD)

Asistente avanzado para el cese y migración de servidores de licencias de clientes.

- **Composite Analyzer (AI):** Drag & Drop del fichero `Composite.txt` analizado en vivo mediante Gemini. Identifica interfaces físicas (Ethernet) descartando adaptadores virtuales y VPNs de forma automática.
- **Preview & PDF Engine:** Generación fluida con previsualización HTML interactiva y motor de compilación PDF hermético con fuentes autohospedadas.

### 3. Planificador de Renovaciones & Clientes

Ecosistema administrativo que sincroniza las licencias instaladas reales (Inventario) con los contratos contractuales activos (CSV).

- **Gestor Multi-Site:** Identificación consolidada de clientes con múltiples Sold-Tos e instalaciones distribuidas con estética industrial limpia ("Gold Thread watermark").
- **Traffic-Light Alerts:** Indicadores semánticos en tiempo real sobre la salud y caducidad de los contratos.

### 4. System NOC Dashboard & Fleet Monitor

Centro de telemetría e infraestructura para administradores de sistemas y monitorización de consumos.

- **Docker Fleet Monitor:** Monitorización en vivo de CPU (Gauges circulares), memoria RAM e interactores de reinicio de la red de contenedores Beta y Prod.
- **AI Hub & Token Economics:** Panel financiero que desglosa en tiempo real los tokens consumidos (Gemini/OpenRouter), calculando costes dinámicos por petición y asegurando cuotas máximas.
- **Centro de Logs Unificado:** Visor y parser regex estructurado para `laravel.log` y auditoría de Webhooks bidireccionales de Telegram y n8n.

---

## 🛠️ Estructura de Directorios

```
├── .agent/               # Memoria persistente, instrucciones y workflows del AI Agent
│   ├── memory/           # Logs e historiales (ACTIVE_CONTEXT.md)
│   └── workflows/        # Protocolos operativos (sync, end-session)
├── backend/              # Núcleo del portal (Laravel 11 App)
│   ├── app/              # Controladores, Modelos, Middleware, Servicios de IA
│   ├── config/           # Configuraciones (auth, base de datos, gemini)
│   ├── public/           # Ficheros públicos y hojas modulares compiladas de CSS
│   ├── resources/        # Vistas Blade, traducciones y componentes Alpine.js
│   └── routes/           # Enrutamiento del portal (web, api)
├── infra/                # Archivos de infraestructura (docker-compose, Nginx configs)
├── management/           # Gobernanza de desarrollo (ROADMAP, BACKLOG, CHANGELOG, ERRORS)
└── scripts/              # Herramientas de copias de seguridad de DB MariaDB y automatizaciones
```

---

## 📦 Despliegue Rápido (Beta / Prod)

El portal está completamente empaquetado mediante Docker para asegurar la paridad estricta entre entornos (Beta y Producción están totalmente desacoplados a nivel de red y volúmenes).

### Configuración Inicial

1. Clonar el repositorio y acceder a la carpeta del proyecto.
2. Sincronizar las variables de entorno:
   ```bash
   cp infra/.env.beta.example infra/.env.beta
   cp backend/.env.example backend/.env
   ```

### Despliegue del Stack Docker

```bash
# Levantar el entorno Beta
docker compose --project-directory . -f infra/docker-compose.beta.yml up -d

# Levantar el entorno de Producción
docker compose --project-directory . -f infra/docker-compose.prod.yml up -d

# Acciones recomendadas tras deploy o actualización
docker exec -it dx-php-beta php artisan optimize:clear
docker exec -it dx-php-beta php artisan migrate --force
```

---

## 🛡️ Protocolo de Desarrollo & Seguridad (AGENTS.md)

Para mantener la calidad y blindaje del código, cualquier interacción de desarrollo sigue directrices estrictas:

- **Modo Estricto:** Prohibido realizar cambios o ejecutar scripts sin plan previo aprobado explícitamente por Oskar.
- **Blindaje de Descargas:** Las licencias se recuperan de `storage/licenses/` mediante IDs abstractos de base de datos (`/licenses/download?id=[UUID]`). Nunca se pasan rutas directas en las URLs.
- **Auditoría IA Privada:** El contenido en bruto de las licencias y metadatos sensibles nunca se almacena; se parsea en RAM y se elimina tras el análisis.
- **Gobernanza Git:** El desarrollo se realiza en ramas de funcionalidad (`feature/`, `fix/`) que nacen y regresan exclusivamente a la rama `dev`.

---

<p align="center">
  <em>Desarrollado con máxima robustez industrial y coherencia visual por el equipo de DX Management.</em>
</p>
