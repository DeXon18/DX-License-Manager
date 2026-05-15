# 🛡️ DX License Manager — NOC Pro Edition

<p align="center">
  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="200" alt="Laravel 11">
  <br>
  <strong>Portal Empresarial de Gestión de Licencias & Auditoría IA</strong>
  <br>
  <em>Filosofía Impeccable — Minimalismo funcional de alta precisión.</em>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Version-1.0--alpha-388BFD?style=for-the-badge" alt="Version">
  <img src="https://img.shields.io/badge/Stack-Laravel_11-FF2D20?style=for-the-badge&logo=laravel" alt="Laravel">
  <img src="https://img.shields.io/badge/Engine-AI_Auditor-009999?style=for-the-badge" alt="AI Engine">
  <img src="https://img.shields.io/badge/UI-NOC_Pro-success?style=for-the-badge" alt="UI Style">
</p>

---

## 📖 Visión General

**DX License Manager** no es una herramienta de marketing; es un **Centro de Operaciones de Red (NOC)** diseñado para la gestión técnica de activos de software Siemens PLM y Moldex3D. Su arquitectura permite centralizar contratos, auditar licencias reales mediante Inteligencia Artificial y predecir renovaciones críticas en una interfaz de alta densidad.

### 🏛️ Pilares Tecnológicos
- **Core**: Laravel 11 (PHP 8.3) en arquitectura de alta disponibilidad.
- **Intelligence**: Fallback Chain de modelos IA (Gemini 3.1 Flash ↔ DeepSeek R1).
- **Automation**: Orquestación de flujos mediante **n8n**.
- **Real-time**: Caché y mensajería con **Redis**.
- **UI/UX**: Estética **NOC Pro** nativa en modo oscuro con tipografía de precisión (IBM Plex Mono).

---

## 🚀 Módulos Principales

### 🧠 AI License Auditor (Siemens & Moldex3D)
El motor de auditoría procesa archivos `.lic` y `.mac` extrayendo metadatos complejos sin intervención humana.
- **Siemens NX Suite**: Soporte total para daemons `ugslmd`, `cdlmd` y `saltd`.
- **Moldex3D**: Extracción de Machine IDs y métricas de asientos.
- **Privacy First**: Los archivos auditados por IA nunca se guardan físicamente; solo persistimos los metadatos.

### 📅 Renewal Planner (Predictive)
Un ecosistema unificado que sincroniza el inventario real de licencias con los contratos contractuales (CSV).
- **Status Identity**: Colores dinámicos basados en `identities.json`.
- **Timeline Control**: Seguimiento de estados (Pendiente, Ofertado, Procesado) con sistema de **Undo**.

### 📊 System NOC Control
Dashboard de telemetría total para administradores.
- **Monitor Docker**: Estado de salud de contenedores y consumo de recursos.
- **AI Health**: Verificación de latencia y estado de los proveedores IA.
- **Security Audit**: Trazabilidad completa de sesiones y acciones administrativas.

---

## 🎨 Design System — Vendor Branding

El portal adapta su identidad visual dinámicamente según el vendor seleccionado, manteniendo la coherencia de marca definida en `DESIGN.md`:

| Vendor | Primary Color | Hover State | Identity Key |
| :--- | :--- | :--- | :--- |
| **Siemens** | `#009999` | `#007A7A` | `accent` |
| **Moldex3D** | `#ED1C24` | `#C41520` | `danger` |

---

## 🛠️ Estructura del Proyecto

```bash
├── backend/          # Aplicación Laravel 11 (Core)
├── infra/            # Stack Docker (Beta / Prod)
├── management/       # Gobernanza (Roadmap, Backlog, Changelog)
├── scripts/          # Automatización y Backups
└── obsidian/         # Documentación técnica y flujos n8n
```

---

## 📦 Despliegue Rápido

El proyecto está dockerizado para garantizar la paridad entre entornos.

```bash
# Levantar entorno Beta (LXC 600)
docker compose -f infra/docker-compose.beta.yml up -d

# Limpieza y optimización
php artisan optimize:clear
php artisan migrate --force
```

---

## 🛡️ Seguridad & Gobernanza

- **Auth**: Autenticación blindada mediante **JWT** con rotación de tokens cada 15 min.
- **RBAC**: Control de acceso por roles (`admin`, `technician`, `viewer`).
- **Data Protection**: Backups automáticos de MariaDB con rotación de 7 días.

---

## 👨‍💻 Desarrollo

Toda la lógica de desarrollo sigue las directrices de `AGENTS.md` y `DESIGN.md`. El historial detallado de hitos se encuentra en `management/CHANGELOG.md`.

---
<p align="center">
  <em>Desarrollado con precisión técnica por el equipo de DX Management.</em>
</p>
