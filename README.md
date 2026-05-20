# 🛡️ DX License Manager — NOC Pro Edition

<p align="center">
  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="200" alt="Laravel 11">
  <br>
  <strong>Portal Empresarial de Gestión de Licencias & Auditoría IA</strong>
  <br>
  <em>Filosofía Impeccable — Minimalismo funcional de alta densidad para entornos de red.</em>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Version-1.1--beta-388BFD?style=for-the-badge" alt="Version">
  <img src="https://img.shields.io/badge/Stack-Laravel_11_|_PHP_8.3-FF2D20?style=for-the-badge&logo=laravel" alt="Laravel">
  <img src="https://img.shields.io/badge/Engine-AI_Gemini_Flash-9171ff?style=for-the-badge" alt="AI Gemini Engine">
  <img src="https://img.shields.io/badge/CSS-Modular_35_Files-007aff?style=for-the-badge" alt="CSS Modular architecture">
</p>

---

## 📖 Visión General

**DX License Manager** es un **Centro de Operaciones de Red (NOC)** de nivel empresarial diseñado para la gestión técnica, auditoría y control de licencias industriales **Siemens PLM** y **Moldex3D**. 

A través de una interfaz industrial de alta densidad visual y un motor de inteligencia artificial robusto, el portal unifica la gobernanza de activos, predice ciclos de renovación, automatiza auditorías complejas y monitoriza la infraestructura crítica en tiempo real.

---

## 🏛️ Pilares Tecnológicos & Arquitectura

*   **Núcleo de Aplicación:** Laravel 11.x (PHP 8.3) estructurado bajo buenas prácticas defensivas y optimización de consultas.
*   **Inteligencia Artificial (AI Agent):** Pipeline con cortocircuito inteligente de tokens. Conexión nativa con **Google Gemini (Flash 3.1 & Lite)** para composite matching y **n8n Workflow Engine** con fallback dinámico (DeepSeek R1 / OpenRouter) para análisis contractuales y auditorías de FlexLM.
*   **Reactividad Ligera:** **Alpine.js** inyectado en vistas Blade para evitar la carga de pesadas dependencias de Javascript, manteniendo la interacción fluida.
*   **Mensajería y Cola de Trabajo:** **Redis** integrado para invalidación instantánea de tokens revocados (ZSET blacklist) y procesamiento asíncrono de telemetría de auditorías.
*   **Base de Datos Relacional:** **MariaDB** con almacenamiento normalizado de aliases de clientes, Sold-Tos y Machine IDs.

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

| Vendor | Primary Color | Hover State | CSS Class | Propósito Principal |
| :--- | :--- | :--- | :--- | :--- |
| **Siemens PLM** | `#009999` (Teal) | `#007A7A` | `.theme-teal` / `.accent` | Entornos NX Suite, STAR-CCM+, HEEDS, COD |
| **Moldex3D** | `#ED1C24` (Red) | `#C41520` | `.theme-red` / `.danger` | Auditoría de licencias Moldex3D (`.mac`) |

---

## 🚀 Módulos Funcionales

### 1. AI License Auditor
Auditoría y extracción automatizada de metadatos en archivos de licencia FlexLM (`.lic`) y Moldex (`.mac`).
*   **Normalización Estricta:** Detección de Hostnames, COMPOSITE, Machine IDs, deamons (`ugslmd`, `cdlmd`, `RCTECH`, `saltd`) e incrementos de asientos.
*   **Cortocircuito Temporal:** Ahorro proactivo de tokens bloqueando la llamada a la IA en licencias temporales de 7 días (aquellas con *ANY* o *YourHostname*).
*   **Gobernanza de Privacidad:** Los archivos `.lic` subidos por los clientes **nunca** se guardan físicamente en el servidor (`file_path` = `NULL` en base de datos). Solo se persisten los metadatos de auditoría estructurados.

### 2. Generador de Certificados de Cese (COD)
Asistente avanzado para el cese y migración de servidores de licencias de clientes.
*   **Composite Analyzer (AI):** Drag & Drop del fichero `Composite.txt` analizado en vivo mediante Gemini. Identifica interfaces físicas (Ethernet) descartando adaptadores virtuales y VPNs de forma automática.
*   **Preview & PDF Engine:** Generación fluida con previsualización HTML interactiva y motor de compilación PDF hermético con fuentes autohospedadas.

### 3. Planificador de Renovaciones (Predictive Planner)
Ecosistema administrativo que sincroniza las licencias instaladas reales (Inventario) con los contratos contractuales activos (CSV).
*   **Filtros Multistate:** Segmentación rápida por estado contractual (Ofertado, Aceptado, Procesado) mapeados a `identities.json`.
*   **Historial AJAX & Undo:** Capacidad para deshacer y revertir renovaciones marcadas por error sin recargar la página.

### 4. System NOC Dashboard & Fleet Monitor
Centro de telemetría e infraestructura para administradores de sistemas.
*   **Docker Fleet Monitor:** Monitorización en vivo de CPU (Gauges circulares), memoria RAM e interactores de reinicio con validación segura de prefijos de contenedor (`dx-`).
*   **AI Health & Latency:** Latido en vivo con visualización de estado (Online/Offline) para Gemini, DeepSeek, OpenRouter, n8n y Telegram.
*   **Centro de Logs Unificado:** Visor y parser regex estructurado para `laravel.log` (con stack traces colapsables Alpine.js) y log de auditoría de correos salientes (SMTP Mailtrap).

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

El portal está completamente empaquetado mediante Docker para asegurar la paridad de entornos.

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

# Entrar al contenedor PHP del entorno Beta
docker exec -it dx-php-beta sh

# Acciones recomendadas tras deploy o actualización
php artisan optimize:clear
php artisan migrate --force
php artisan db:seed --class=AdminUserSeeder
```

---

## 🛡️ Protocolo de Desarrollo & Seguridad (AGENTS.md)

Para mantener la calidad y blindaje del código, cualquier interacción de desarrollo sigue directrices estrictas:
*   **Modo Estricto:** Prohibido realizar cambios o ejecutar scripts sin plan previo aprobado explícitamente por Oskar.
*   **Blindaje de Descargas:** Las licencias se recuperan de `storage/licenses/` mediante IDs abstractos de base de datos (`/licenses/download?id=[UUID]`). Nunca se pasan rutas directas en las URLs.
*   **Validación de Cambios:** Revisión obligatoria de los logs de los contenedores PHP (`docker compose logs dx-php-beta`) antes de realizar cualquier commit.
*   **Gobernanza Git:** El desarrollo se realiza en ramas de funcionalidad (`feature/`, `fix/`) que nacen y regresan exclusivamente a la rama `dev`.

---
<p align="center">
  <em>Desarrollado con máxima robustez industrial y coherencia visual por el equipo de DX Management.</em>
</p>
