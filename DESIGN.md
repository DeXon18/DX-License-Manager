---
version: alpha
name: DX License Manager
description: Portal empresarial interno de gestión de licencias con auditoría IA. Filosofía impeccable — minimalismo funcional de alta precisión. Dashboard B2B, no producto de marketing.

# ─── VENDOR COLORS ───────────────────────────────────────────
vendor:
  siemens: "#009999"
  siemens-hover: "#007A7A"
  siemens-muted: "#E6F7F7"
  siemens-border: "#99D6D6"
  siemens-dark: "#2AA198"
  siemens-dark-muted: "rgba(0,122,122,0.15)"
  siemens-dark-border: "rgba(0,122,122,0.3)"
  moldex: "#ED1C24"
  moldex-hover: "#C41520"
  moldex-muted: "#FEF0F0"
  moldex-border: "#F9A8AB"
  moldex-dark: "#E05252"
  moldex-dark-muted: "rgba(185,28,28,0.12)"
  moldex-dark-border: "rgba(185,28,28,0.25)"

# ─── LIGHT MODE ──────────────────────────────────────────────
colors:
  primary: "#0D1117"
  secondary: "#374151"
  accent: "#388BFD"
  accent-hover: "#1D6AE8"
  accent-muted: "#EFF6FF"
  accent-border: "#BFDBFE"
  bg: "#F7F8FA"
  surface: "#FFFFFF"
  raised: "#F0F2F5"
  border: "#DDE1E7"
  border-subtle: "#EAECEF"
  on-accent: "#FFFFFF"
  muted: "#6B7280"
  success: "#15803D"
  success-bg: "#F0FDF4"
  success-border: "#BBF7D0"
  warning: "#B45309"
  warning-bg: "#FFFBEB"
  warning-border: "#FDE68A"
  danger: "#B91C1C"
  danger-bg: "#FEF2F2"
  danger-border: "#FECACA"

# ─── DARK MODE (diseñado independientemente, no inversión) ───
colors-dark:
  primary: "#E6EDF3"
  secondary: "#CDD9E5"
  accent: "#388BFD"
  accent-hover: "#58A6FF"
  accent-muted: "#0D1B2E"
  accent-border: "#1F4B8E"
  bg: "#0D1117"
  surface: "#161B22"
  raised: "#21262D"
  border: "#30363D"
  border-subtle: "#21262D"
  on-accent: "#FFFFFF"
  muted: "#8B949E"
  success: "#3FB950"
  success-bg: "#0D2818"
  success-border: "#1A5C2A"
  warning: "#D29922"
  warning-bg: "#2D1F00"
  warning-border: "#5A3E00"
  danger: "#E05252"
  danger-bg: "#2D0F0F"
  danger-border: "#5C1A1A"

# ─── TIPOGRAFÍA — escala modular ratio 1.266 ─────────────────
typography:
  display:
    fontFamily: Inter, system-ui, sans-serif
    fontSize: 2rem
    fontWeight: 700
    lineHeight: 1.15
    letterSpacing: -0.03em
  h1:
    fontFamily: Inter, system-ui, sans-serif
    fontSize: 1.602rem
    fontWeight: 700
    lineHeight: 1.2
    letterSpacing: -0.02em
  h2:
    fontFamily: Inter, system-ui, sans-serif
    fontSize: 1.266rem
    fontWeight: 600
    lineHeight: 1.3
    letterSpacing: -0.01em
  h3:
    fontFamily: Inter, system-ui, sans-serif
    fontSize: 1rem
    fontWeight: 600
    lineHeight: 1.4
  body:
    fontFamily: Inter, system-ui, sans-serif
    fontSize: 0.889rem
    fontWeight: 400
    lineHeight: 1.65
  body-sm:
    fontFamily: Inter, system-ui, sans-serif
    fontSize: 0.79rem
    fontWeight: 400
    lineHeight: 1.6
  label:
    fontFamily: Inter, system-ui, sans-serif
    fontSize: 0.694rem
    fontWeight: 600
    lineHeight: 1
    letterSpacing: 0.06em
  mono:
    fontFamily: IBM Plex Mono, Fira Code, monospace
    fontSize: 0.8125rem
    fontWeight: 400
    lineHeight: 1.7

# ─── ESPACIADO — sistema 4pt ──────────────────────────────────
spacing:
  1: 4px
  2: 8px
  3: 12px
  4: 16px
  5: 20px
  6: 24px
  8: 32px
  10: 40px
  12: 48px
  16: 64px

# ─── RADIOS ───────────────────────────────────────────────────
rounded:
  sm: 4px
  md: 6px
  lg: 10px
  xl: 16px
  full: 9999px

# ─── ELEVACIÓN ────────────────────────────────────────────────
elevation:
  0: none
  1: 0 1px 2px rgba(0,0,0,0.05)
  2: 0 2px 8px rgba(0,0,0,0.08)
  3: 0 8px 24px rgba(0,0,0,0.12)

elevation-dark:
  0: none
  1: 0 1px 2px rgba(0,0,0,0.30)
  2: 0 2px 8px rgba(0,0,0,0.40)
  3: 0 8px 24px rgba(0,0,0,0.60)

# ─── Z-INDEX ──────────────────────────────────────────────────
z-index:
  base: 0
  raised: 10
  dropdown: 20
  sticky: 40
  modal: 100
  toast: 1000

# ─── COMPONENTES ──────────────────────────────────────────────
components:
  button-primary:
    backgroundColor: "{colors.accent}"
    textColor: "{colors.on-accent}"
    typography: "{typography.label}"
    rounded: "{rounded.md}"
    padding: 8px 14px
  button-primary-hover:
    backgroundColor: "{colors.accent-hover}"
    textColor: "{colors.on-accent}"
  button-secondary:
    backgroundColor: "{colors.surface}"
    textColor: "{colors.primary}"
    rounded: "{rounded.md}"
    padding: 8px 14px
  button-ghost:
    backgroundColor: "transparent"
    textColor: "{colors.accent}"
    rounded: "{rounded.md}"
    padding: 8px 14px
  button-danger:
    backgroundColor: "{colors.danger}"
    textColor: "{colors.on-accent}"
    rounded: "{rounded.md}"
    padding: 8px 14px
  card:
    backgroundColor: "{colors.surface}"
    textColor: "{colors.primary}"
    rounded: "{rounded.lg}"
    padding: 20px 24px
  metric-card:
    backgroundColor: "{colors.surface}"
    textColor: "{colors.primary}"
    rounded: "{rounded.lg}"
    padding: 16px
  badge-success:
    backgroundColor: "{colors.success-bg}"
    textColor: "{colors.success}"
    rounded: "{rounded.full}"
    padding: 3px 8px
  badge-warning:
    backgroundColor: "{colors.warning-bg}"
    textColor: "{colors.warning}"
    rounded: "{rounded.full}"
    padding: 3px 8px
  badge-danger:
    backgroundColor: "{colors.danger-bg}"
    textColor: "{colors.danger}"
    rounded: "{rounded.full}"
    padding: 3px 8px
  badge-neutral:
    backgroundColor: "{colors.raised}"
    textColor: "{colors.muted}"
    rounded: "{rounded.full}"
    padding: 3px 8px
  input:
    backgroundColor: "{colors.surface}"
    textColor: "{colors.primary}"
    rounded: "{rounded.md}"
    padding: 8px 12px
  nav-item:
    backgroundColor: "transparent"
    textColor: "{colors.secondary}"
    rounded: "{rounded.md}"
    padding: 6px 10px
  nav-item-active:
    backgroundColor: "{colors.accent-muted}"
    textColor: "{colors.accent}"
    rounded: "{rounded.md}"
    padding: 6px 10px
  alert-success:
    backgroundColor: "{colors.success-bg}"
    textColor: "{colors.success}"
    rounded: "{rounded.md}"
    padding: 10px 14px
  alert-warning:
    backgroundColor: "{colors.warning-bg}"
    textColor: "{colors.warning}"
    rounded: "{rounded.md}"
    padding: 10px 14px
  alert-danger:
    backgroundColor: "{colors.danger-bg}"
    textColor: "{colors.danger}"
    rounded: "{rounded.md}"
    padding: 10px 14px
---

## ⚠️ Referencia Visual — infra/html/

Los archivos en `infra/html/` son **prototipos estáticos de consulta** — no son las vistas finales del portal.

| Archivo                       | Referencia para                          |
| :---------------------------- | :--------------------------------------- |
| `index.html`                  | Página de mantenimiento / Fase 0         |
| `01-login.html`               | Vista de login                           |
| `02-inicio.html`              | Dashboard principal                      |
| `03-herramientas.html`        | Hub de herramientas                      |
| `04-admin.html`               | Centro de mando admin                    |
| `tool-designcenter.html`      | Herramienta Designcenter & TC            |
| `tool-heeds.html`             | Herramienta HEEDS Suite                  |
| `tool-moldex.html`            | Herramienta Auditor Moldex3D             |
| `tool-solicitar-cambio.html`  | Herramienta Solicitar Cambio de Licencia |
| `tool-starccm.html`           | Herramienta STAR-CCM+                    |
| `dx-styles.css`               | Variables CSS y estilos base compartidos |

**Cuando se implemente cada vista en Laravel:**
- Las vistas van en `backend/resources/views/`
- Replicar el HTML estático correspondiente en Blade — sin improvisar
- Los archivos de `infra/html/` permanecen como referencia permanente — no se eliminan

---

## Overview

**Minimalismo funcional de alta precisión.** El DX License Manager es una herramienta interna — no un producto de marketing. Cada elemento visual existe porque cumple una función concreta. La ausencia de decoración es una decisión de diseño, no una omisión.

El portal gestiona licencias de software empresarial (Siemens, Moldex3D, COD) y resultados de auditoría IA. Los usuarios son técnicos e ingenieros — valoran la densidad de información, la claridad y la velocidad sobre la estética llamativa.

**Referencia de estilo:** Dashboard SaaS B2B interno. Linear, Vercel, GitHub. No un portal de marketing.
**Skills aplicadas:** impeccable (anti-AI-slop, escala modular, jerarquía precisa) + ui-ux-pro-max (4pt spacing, z-index scale, dark mode independiente, elevación consistente).

## Vendor Colors

Colores de marca por vendor — usados en bordes de acento de cards, badges y cualquier elemento que identifique el origen de una licencia.

| Token                     | Light       | Dark        | Uso                                          |
| :------------------------ | :---------- | :---------- | :------------------------------------------- |
| `vendor-siemens`          | `#009999`   | `#2AA198`   | Borde superior de card, badge activo         |
| `vendor-siemens-hover`    | `#007A7A`   | —           | Estado hover sobre elementos Siemens         |
| `vendor-siemens-muted`    | `#E6F7F7`   | `rgba(0,122,122,0.15)` | Fondo de badge, fondo de sección  |
| `vendor-siemens-border`   | `#99D6D6`   | `rgba(0,122,122,0.30)` | Borde de badge, separadores       |
| `vendor-moldex`           | `#ED1C24`   | `#E05252`   | Borde superior de card, badge activo         |
| `vendor-moldex-hover`     | `#C41520`   | —           | Estado hover sobre elementos Moldex3D        |
| `vendor-moldex-muted`     | `#FEF0F0`   | `rgba(185,28,28,0.12)` | Fondo de badge, fondo de sección  |
| `vendor-moldex-border`    | `#F9A8AB`   | `rgba(185,28,28,0.25)` | Borde de badge, separadores       |

**Regla:** Los colores de vendor nunca sustituyen al accent azul en acciones. Son exclusivamente para identificación visual de origen — no para botones, links ni foco.

## Colors

Dos paletas — light y dark — **diseñadas independientemente**. El dark no es inversión del light: tiene sus propios valores de contraste verificados por separado, inspirado en el color system de GitHub.

### Light mode

- **Primary (`#0D1117`):** Casi negro. Textos de primer nivel, headings.
- **Secondary (`#4B5563`):** Gris medio. Textos secundarios, metadatos.
- **Accent (`#1D4ED8`):** Azul institución. El único color de acción — botones primarios, links, foco, estado activo. Una sola aparición por vista.
- **BG (`#F7F8FA`):** Fondo general. Neutro frío, no blanco puro.
- **Surface (`#FFFFFF`):** Cards, paneles, modales. Contrasta con BG.
- **Raised (`#F0F2F5`):** Tercer nivel — thead de tablas, hover de filas, nav activo.
- **Border (`#DDE1E7`):** Divisores, bordes de cards e inputs.
- **Muted (`#9CA3AF`):** Placeholders, iconos inactivos, labels de columna.
- **Success / Warning / Danger:** Cada uno con tres tokens (text, bg, border) para WCAG AA garantizado.

### Dark mode

- **BG (`#0D1117`):** GitHub-style. No negro puro — evita el contraste excesivo y la fatiga visual.
- **Surface (`#161B22`):** Cards y paneles, primer nivel sobre BG.
- **Raised (`#21262D`):** Segundo nivel — thead, hover, activo.
- **Accent (`#388BFD`):** Más luminoso que en light para mantener ratio de contraste en oscuro.
- Los colores semánticos (success/warning/danger) tienen fondos muy oscuros propios — nunca reutilizar los del light mode.

**Regla crítica:** El accent solo aparece en **un elemento por vista**. Si hay dos azules visibles, uno está mal.

## Typography

**Inter** como fuente del sistema. Elegida por su carácter industrial-técnico (IBM, fit perfecto con Siemens/Moldex3D), excelente legibilidad en alta densidad de información, y por no ser un default invisible de AI (Inter, Roboto, Arial).

**IBM Plex Mono** para todos los datos técnicos: fechas ISO, file paths, IDs de licencia, versiones de software, hashes.

Escala modular con ratio **1.266** (cuarta perfecta musical) — tamaños derivados matemáticamente, no arbitrarios:

```
display  → 2rem      = base × ratio⁴
h1       → 1.602rem  = base × ratio³
h2       → 1.266rem  = base × ratio²
h3       → 1rem      = base
body     → 0.889rem  = base ÷ ratio
body-sm  → 0.79rem
label    → 0.694rem  = base ÷ ratio²
```

Jerarquía por **peso + tamaño + tracking** — nunca solo por color. Dos pesos en el sistema: 400 regular y 600/700 semibold/bold.

**Uso por nivel:**

- `display` — Solo en hero de dashboard si aplica
- `h1` — Título de sección principal (una por vista)
- `h2` — Subtítulo de panel o tabla
- `h3` — Card title, ítem destacado en lista
- `body` — Contenido de tablas, descripciones
- `body-sm` — Metadatos, fechas en prosa, contadores
- `label` — Cabeceras de columna, etiquetas de campo (siempre uppercase + tracking)
- `mono` — Fechas ISO, file paths, IDs, versiones, cualquier dato técnico

## Layout

Sidebar fijo **240px** + área de contenido fluida. Sin header global — la navegación lateral provee el contexto.

```
┌──────────┬────────────────────────────────┐
│  Sidebar │  Header (h1 + acción primaria) │
│  240px   ├────────────────────────────────┤
│          │  Contenido — max-w-6xl         │
│  Nav     │  (tabla / cards / formulario)  │
│  items   │                                │
└──────────┴────────────────────────────────┘
```

- Contenido: `max-w-6xl` con `px-8` (spacing 8 = 32px)
- Entre secciones: `spacing.8` (32px)
- Interno de cards: `spacing.6` (24px)
- Gap entre cards en grid: `spacing.3` (12px)
- Grid de métricas: `repeat(auto-fit, minmax(140px, 1fr))` — 4 col desktop, 2 tablet, 1 móvil
- Todo el spacing en múltiplos de 4px — sin valores arbitrarios

## Elevation & Depth

Escala en **4 niveles consistentes**. Sin valores de shadow inventados fuera de esta escala.

| Nivel        | Uso                                                           |
| :----------- | :------------------------------------------------------------ |
| 0 — flat     | Superficies planas: thead, bg-raised, elementos sin elevación |
| 1 — cards    | Cards, inputs, nav sidebar                                    |
| 2 — dropdown | Dropdowns, tooltips, popovers                                 |
| 3 — modal    | Modales, drawers, overlays                                    |

En dark mode la opacidad de las sombras aumenta (×5-7) porque son menos perceptibles sobre fondos oscuros.

Sin `backdrop-filter: blur`. Sin glassmorphism. El contraste entre `bg` y `surface` ya genera la profundidad necesaria.

## Shapes

- `sm` (4px) — Badges de estado
- `md` (6px) — Botones, inputs, alerts, dropdowns
- `lg` (10px) — Cards, paneles, tabla container
- `xl` (16px) — Modales, drawers
- `full` (9999px) — Badges únicamente

Sin `border-radius: 0` en elementos interactivos. Sin radius en bordes de un solo lado.

## Components

### Botones

**Un solo primary por vista.** Jerarquía estricta:

1. **Primary** — accent, acción principal de la vista
2. **Secondary** — surface + border, acciones secundarias (máx. 2)
3. **Ghost** — transparente + accent border, acciones de navegación
4. **Danger** — solo en modales de confirmación destructiva, nunca inline

Tamaños: base `8px 14px` · small `5px 10px`. Sin tamaños intermedios arbitrarios.

### Metric Cards (Dashboard)

```
┌──────────────────────────┐
│ LABEL UPPERCASE (0.65rem)│  ← color muted
│ 247         (1.602rem·7) │  ← tracking -0.03em
│ +12 este mes (0.72rem)   │  ← color semántico
└──────────────────────────┘
```

Elevación nivel 1. Background `surface` con border `border`.

### Tabla de Licencias

- `thead`: background `raised`, label typography, color muted, elevación 0
- `tbody tr`: body typography, hover `raised`
- Columna Estado: badge centrado
- Datos técnicos (fechas, paths, versiones): mono typography
- Acciones de fila: visibles solo en hover, con label

### Inputs y Formularios

- Label siempre encima en label typography — nunca placeholder como label
- Error: mensaje inline debajo en danger color, body-sm
- Focus ring: `box-shadow: 0 0 0 3px` accent al 15% opacidad
- Submit al final del formulario, alineado a la derecha

### Alertas del Sistema

Tres variantes semánticas con bg, text y border propios (no shared).
Estructura: icono pequeño (✓ ▲ ✕) + texto. Sin titles — el color comunica la severidad.
Usadas para: confirmación de auditoría IA, avisos de licencias, fallos del FallbackChain (Gemini → Deepseek → OpenRouter).

## Do's and Don'ts

### ✅ Hacer

- Inter para UI, IBM Plex Mono para datos técnicos — siempre
- Un solo accent azul por vista
- Datos técnicos en mono: fechas ISO, paths, IDs, versiones
- Spacing en múltiplos de 4px — sin valores arbitrarios
- Escala de elevación fija — sin shadow values inventados
- Z-index del scale definido — sin valores arbitrarios
- Confirmar acciones destructivas en modal antes de ejecutar
- Estados vacíos con mensaje útil + CTA
- Paginación en todas las tablas
- Dark mode verificado independientemente — no asumir que el light funciona en oscuro

### ❌ No hacer

- Outfit, Roboto, Arial, system-ui como fuente principal (defaults genéricos o no corporativos)
- Gradientes, glassmorphism, backdrop-filter blur, dark glows, bounce easing
- Más de un botón primary por vista
- Dos elementos accent visibles simultáneamente
- Iconos sin label en acciones no universales
- Texto centrado en bloques de más de 2 líneas
- Colores fuera de las paletas light/dark definidas
- Tamaños de fuente fuera de la escala modular
- Spacing fuera de la escala 4pt
- `border-radius: 0` en elementos interactivos
- Animaciones de entrada innecesarias
- Emojis en navegación, iconos de sistema o controles de UI

## ─── ADMIN COMMAND CENTER SPEC ───────────────────────────────

Especificación técnica para el Dashboard de alta densidad y paneles de monitorización.

### Bento Cards (Contenedores)

- **Background:** `surface` (`#161B22` en dark)
- **Border:** `border` (`#30363D` en dark)
- **Radius:** `rounded-[10px]` (lg)
- **Shadow:** `shadow-sm`
- **Inner Padding:** `p-5` (20px)

### Tipografía Técnica (Jerarquía)

- **Labels (Categoría):** `text-[0.65rem] font-bold uppercase tracking-[0.06em] text-muted`
- **Valores Master:** `text-[1.602rem] font-bold text-primary font-mono tracking-[-0.03em]`
- **Valores Detail:** `text-[0.79rem] text-secondary font-mono`

### Semántica de Estados (Sin Brillos)

- **Status OK:** `text-success bg-success-bg border-success-border`
- **Status WARN:** `text-warning bg-warning-bg border-warning-border`
- **Status ERROR:** `text-danger bg-danger-bg border-danger-border`
- **Live Indicator:** `h-2 w-2 rounded-full bg-success animate-pulse`

### Botones y Acciones

- **Primary Action (Max 1):** `bg-accent hover:bg-accent-hover text-on-accent rounded-[6px] text-[0.694rem] font-bold uppercase tracking-[0.06em]`
- **Secondary Action:** `bg-raised border-border text-secondary hover:text-primary rounded-[6px]`
- **Icon Accent Box:** `p-2 rounded-[6px] bg-accent-muted text-accent`
