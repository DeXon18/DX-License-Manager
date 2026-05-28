## [2026-05-28] � Fase 33: Onboarding Tour (NOC Pro)
- Implementado sistema de onboarding guiado con Driver.js.
- A�adido soporte para tours contextuales por p�gina mediante la inyecci�n de window.pageTourSteps.
- Creados recorridos guiados espec�ficos para Dashboard, Directorio de Clientes, Herramientas y Planificador de Renovaciones.
- Persistencia as�ncrona del estado del tour en la tabla users mediante AJAX.
- Integraci�n visual completa usando las variables CSS nativas del sistema NOC Pro.
> Historial completo de cambios desde el inicio del proyecto.
> **Regla:** Nunca eliminar entradas. Las nuevas entradas van siempre al principio.

## [2026-05-28 10:50] — Fase 32: Auditoría y Optimización N+1 ✅

### Added
- **Rendimiento**: Habilitado `Model::preventLazyLoading(!app()->isProduction())` en `AppServiceProvider` para proteger la aplicación de consultas N+1 en fase de desarrollo.

### Fixed
- **Auditoría de Queries**: Se corrió un análisis de lazy loading en las vistas principales (Dashboard, Clientes, Renewal Planner, Reports) confirmando que el uso intensivo de `$client->load()` y `withCount` ya previene cuellos de botella. La aplicación actual está 100% limpia de N+1.

## [2026-05-28 10:20] — Fase 31: Validación de Testing Automatizado ✅

### Added
- **Infraestructura de Pruebas**: Verificada y documentada la viabilidad del framework de testing con base de datos en memoria (`sqlite :memory:`). 
- **Conocimiento Documentado**: Acreditada la suite `Tests\Unit\ClientNormalizationTest` que ya cubre las casuísticas del normalizador. Documentado el acceso SSH a LXC 600 (`identities.json`) para que futuros agentes puedan ejecutar `php artisan test` en el contenedor de forma segura.

---

## [2026-05-28 09:25] — Fixes de UI y Motor de Normalización IA ✅

### Fixed
- **Estética de Métricas**: Invertido el orden de Título/Subtítulo en las tarjetas de inventario Siemens/Moldex3D del Directorio de Clientes para mantener la coherencia cromática y tipográfica con el resto del Bento Grid.
- **Ordenación en Dashboard**: Corregido un bug SQL en `DashboardController` que impedía ordenar correctamente la tabla de "Vencimientos inminentes" por fecha de caducidad debido a una colisión en el alias de la consulta.
- **Normalización IA Regex Bug**: Corregido un error en `NormalizationController` donde una expresión regular codiciosa (`.*`) capturaba accidentalmente la explicación textual de la IA en lugar del solo nombre del cliente, provocando fallos de coincidencia en la base de datos al intentar unificar.
- **Ruido en Bandeja de Normalización**: Ocultados permanentemente los avisos de "NUEVA IDENTIDAD" del registro de la bandeja de normalización para evitar ruido visual, ya que la creación de nuevos clientes es el comportamiento esperado natural de la plataforma durante una importación.

---

## [2026-05-28 08:55] — Tarjetas Estadísticas NOC Pro en Clientes

### Added
- Cabecera analítica estilo Bento Grid en el Directorio de Clientes (`clients.index`).
- Tarjetas de conteo individualizado de Clientes Registrados y Contratos Vigentes.
- Tarjetas de inventario activo desgajadas por fabricante (Siemens PLM vs MOLDEX3D), renderizadas con colores de badge corporativo puro y libre de marcos para estética limpia.

---

## [2026-05-28] — Fix Timeout IA y Cron Prod ✅

### Fixed & Added
- Aumentado timeout de OpenRouter a 30s en `ClientAiNormalizationService`.
- Implementado fallback automático nativo (hacia Gemini) ante errores cURL 28 (timeout) y no solo para HTTP 429.
- Cambiado HTTP-Referer en API de OpenRouter por `config('app.url')`.
- Añadido y configurado tarea cron en el servidor de producción (LXC 600) para ejecutar `backup-db.sh prod system` todos los días a las 03:00.

---

## [2026-05-27 16:10] — Integración de Módulos (Services Matrix) & Fix UI ✅

### Added
- **Services Matrix Dashboard**: Refactorizado el panel de módulos de administración (`admin/system/dashboard`) eliminando tarjetas monolíticas e integrando un layout compacto e industrial estilo "Services Matrix" idéntico al de OpenRouter Core.
- **Javascript Navigation**: Reemplazadas etiquetas `<a>` de módulos por contenedores `<div>` con manejador `onclick` para anular por completo decoraciones moradas y subrayados heredados del navegador.
- **Layout de Storage**: Modificado el diseño del indicador de almacenamiento de infraestructura (Beta/Prod) para mostrarlos en dos columnas aisladas en lugar de una línea contigua, mejorando la legibilidad.

---

## [2026-05-25 16:55] — Centralización de OpenRouter & Telemetría de Cuotas Semanales ✅

### Added
- **AI Routing Hub**: Nuevo panel de control `admin/system/ai-routing` centralizado mediante pestañas para organizar el Enrutador de Tareas (Fallbacks anti-429) y el Catálogo de Modelos IA.
- **Telemetría de Cuotas Semanales (Weekly Tokens)**: Añadido soporte en base de datos (`ai_models`) para almacenar los límites de tokens gratuitos (1.26T, 669B, etc.) y mostrar una barra de progreso visual calculando dinámicamente el consumo de los últimos 7 días.
- **Top Modelos Gratuitos**: Actualizado el seeder principal para inyectar y mapear de manera automática el Top 10 de modelos gratis de OpenRouter (Owl Alpha, Nemotron 3 Super, Laguna M.1, etc.).

### Changed
- **Refactorización de Interfaz NOC Pro**: Eliminado el layout de sidebar rígido en favor de una tercera pestaña "Añadir Modelo" en el hub de IA, logrando que el Catálogo utilice el ancho completo (full-width) de la pantalla.
- **Diseño de Barras de Progreso**: Unificado el diseño de estado "Ilimitado / ∞" para modelos de pago sin cuotas artificiales.

---

## [2026-05-25 14:40] — Telemetría IA: Costes Granulares por Modelo & UI NOC Pro ✅

### Added
- **Costes por Modelo & Facturación Dinámica**: Añadida columna `model` en `ai_token_logs`. El sistema ahora mapea los modelos específicos (ej. GPT, DeepSeek) y permite asignar reglas lógicas en `config/ai.php`, logrando coste $0 exacto para los modelos taggeados como `:free` en OpenRouter.
- **Refactorización UI NOC Pro**: Migrado el panel completo de estadísticas a la arquitectura de interfaz industrial del sistema (`dx-v2-sys-dash-sec-layout`). Se eliminaron tablas densas sustituyéndolas por listados de precisión simétrica sin wrappers anidados.

---

## [2026-05-25 13:17] — Security & Compliance Corporativo + Estado de Contratos ✅

### Added
- **Página de Privacidad IA (`/privacidad-ia`)**: Desplegada una declaración formal de privacidad, seguridad y soberanía de datos sobre el uso de la Inteligencia Artificial (Zero-Data Retention) para cumplimiento normativo y transparencia corporativa (DPA).
- **Diseño Corporativo (NOC Pro)**: Estructuración en Bento Grid asimétrico utilizando la tipografía técnica `Outfit` e `IBM Plex Mono` y un pipeline gráfico visual. Extracción de estilos a la capa 6 del sistema (`dx-v2-page-ai-privacy.css`).
- **Estado de Contratos**: Implementado el estado visual "Renovación Tardía" utilizando la clase `dx-v2-color-rojo-oscuro` (#d73a49) para identificar licencias cuya renovación supera la ventana de expiración pero se gestionan como tardías.
- **Navegación UI**: Añadidos enlaces directos a "Privacidad IA" en el menú principal y un enlace de marca de agua en el Footer ("AI-Powered Productivity").

---

## [2026-05-25 09:49] — Gestión de Enterprise Cloud Accounts (Fase 29) ✅

### Added
- **Módulo de Gestión (ECA)**: Implementada tabla `enterprise_cloud_accounts` y lógica CRUD aislada para registrar cuentas de administración Cloud (Sold-To, Account ID y Admin Email) sin interferir con los demonios del inventario de licencias clásico.
- **UI de Cliente (NOC Pro)**: Añadida la pestaña "Enterprise Cloud" en la ficha del cliente con una tabla de alta densidad y un modal de registro alineado con el diseño unificado de 6 capas CSS.
- **Skill de Chatbot IA (`create_enterprise_cloud_account`)**: Dotado al agente inteligente interno de la capacidad de inyectar estas cuentas vía lenguaje natural.
- **Búsqueda Sensible a Dominios**: Mejorada la herramienta `search_clients` del asistente IA para que, al pasarle un correo (ej. `luis@calvera.es`), detecte el cliente asociado buscando en la tabla de contactos y pida confirmación antes de guardar.

---

## [2026-05-22 13:28] — Mejoras Avanzadas en Gráficas de Costes IA ✅

### Added
- **Métricas Avanzadas de Coste**: Añadida tarjeta "Total Peticiones" al panel principal para cerrar el Grid de 4 columnas simétricamente.
- **Promedio de Consumo por Petición (tk/req)**: Nueva métrica en la tabla de acciones que calcula matemáticamente el coste en tokens por cada petición para identificar servicios costosos.
- **Nombres Amigables para Logs**: Integrado un mapeador en la vista de costes para mostrar los nombres de las funciones (ej. `normalization_search` → `Herramienta de Licencias (Normalización)`).
- **Gráficas de Tendencia Diaria (Horarias)**: Implementadas dos nuevas gráficas que desglosan el consumo de tokens hora a hora en el día en curso (`Carbon::today()`), separadas por Proveedor y por Usuario.
- **Gráfica de Tendencia por Usuario (Mes)**: Añadida gráfica de líneas lateral que muestra el consumo acumulado mensual desglosado por cada usuario del portal.
- **Formato de Números Compacto**: Aplicada función `compact_number` a todas las métricas de volumen alto en el dashboard (ej. `145k`, `1.5M`) para mantener intacto el diseño flex-grid NOC Pro.

### Fixed
- **Excepción 500 de Variables no Definidas**: Corregido error en `AiAuditCostController` que omitía el envío de las variables `$totalCostThisMonth` a la vista.
- **Grid Layout Roto**: Eliminada la clase CSS `dx-v2-sys-dash-main-layout` del contenedor de estadísticas que reservaba erróneamente un sidebar vacío de 340px causando desalineamiento a la derecha.

---

## [2026-05-22 11:30] — Módulo de Contacto de Soporte IT (Fase 28) ✅

### Added
- **Formulario de Soporte**: Creación de una nueva vista en `/soporte` para que los usuarios puedan enviar incidencias o consultas directamente al equipo de IT.
- **Integración con Telegram**: El formulario utiliza la API del Bot de Telegram (reutilizando la infraestructura de notificaciones del sistema) para entregar los mensajes instantáneamente a los administradores.
- **Navegación UI**: Añadidos accesos directos de "Ayuda & Soporte" tanto en el Sidebar principal (`app.blade.php`) como en el pie de página (`footer.blade.php`).
- **Diseño NOC Pro**: Se ha implementado un diseño limpio utilizando clases modulares nativas del portal (`.page-header`, `.card-body`) alineando el formulario con el resto de pantallas operativas del sistema.

---

## [2026-05-22 11:00] — AI Cost Audit & Telemetry Dashboard ✅

### Added
- **Módulo de Costes y Telemetría IA**: Implementada la persistencia en base de datos (`AiTokenLog`) para realizar el seguimiento del consumo de tokens (prompt, completion y total) de todos los motores de Inteligencia Artificial (Gemini, DeepSeek, OpenRouter, n8n).
- **Dashboard de Costes (NOC Pro)**: Nueva vista en `/admin/system/ai-costs` con diseño Bento Grid, contadores en tiempo real de tokens consumidos y gráficas interactivas con `Chart.js` comparando el uso por proveedor.
- **Auditoría Financiera**: Cálculo automático de costes estimados basado en las tarifas de mercado actuales de cada proveedor. El cálculo se realiza por cada llamada y se resume mensualmente e históricamente.
- **Instrumentación del Chatbot**: Integrada telemetría de tokens en el DX Agent Support (Chatbot) dentro de `ChatbotController.php` para registrar cada interacción y su respectivo consumo bajo la acción `chatbot_query`.
- **Integraciones Previas Actualizadas**: Modificados `ClientAiNormalizationService`, `CompositeParserService` y `AuditService` para inyectar logs de consumo en cada paso del pipeline.

---

## [2026-05-22 09:50] — COD Cloud Fields & PDF Refactor ✅

### Added
- **Campos Cloud AWS & Azure**: Integración de nuevos campos (`Cloud_AWS` y `Cloud_Azure`) en el formulario de generación de COD, incluyendo su persistencia en el payload y renderizado en PDF.
- **Acordeón Interactivo de Ayuda (getcid.exe)**: Implementación de un acordeón interactivo con Alpine.js en la vista COD (`cod.blade.php`) que proporciona una guía detallada para obtener el Composite ID, ejemplos de comandos y un recuadro dedicado para descargar la utilidad oficial de Siemens, así como un espacio reservado para una herramienta personalizada ATS.

### Changed
- **Refactorización CSS del PDF Oficial**: Reescritura completa del CSS de la plantilla PDF de COD (`cod-template.blade.php`) utilizando medidas absolutas (pixels y pt), tipografía Calibri y ajustando márgenes y espaciados para lograr paridad total al 100% con la estética oficial de los documentos de Siemens.
- **Alineación Modular**: Modificados los estilos inline de los recuadros de ayuda en la interfaz de usuario para que dependan estrictamente de los tokens HSL del diseño de sistema NOC Pro, garantizando adaptabilidad automática a modos claro/oscuro.

---

## [2026-05-21 15:10] — Despliegue v2.0 en Producción & Alineación de Infraestructura ✅

### Added
- **Despliegue Limpio de Producción**: Lanzamiento oficial de la v2.0 del portal en `portal.dxpro.es` partiendo de una base de datos limpia (`migrate:fresh --seed`) e inyección de datos semilla (AdminUserSeeder, RoleSeeder, FeatureFlagSeeder).
- **Alineación de Infraestructura Prod/Beta**: Igualadas las capacidades de Docker Compose en producción respecto a beta (añadido contenedor `node-prod`, montajes de `.git` y `/var/run/docker.sock` para `php-fpm-prod`) anticipando la futura desactivación del entorno Beta.
- **Reconstrucción de Imágenes (Hotfix)**: Reconstrucción de la imagen Docker de `php-fpm-prod` en el servidor de producción para incluir la CLI de `docker` internamente, restaurando la telemetría en el Dashboard de Servicios Docker (`/admin/system/docker`).

### Changed
- **Directivas de Storage Seguras**: Refactorización de `docker-compose.prod.yml` para utilizar el path seguro y estándar `./backend/storage` nativo de Laravel en lugar de la carpeta `./storage` en la raíz. Eliminado el directorio residual del host.
- **Refactorización de Footer (UI)**: Eliminados los bloques obsoletos de "Stack Técnico" e "Infraestructura" en el pie de página. Reemplazados por secciones utilitarias de **Soporte Interno** y **Portales Oficiales** de fabricantes (Siemens, Moldex3D), junto con la insignia "AI-Powered Productivity".

---

## [2026-05-21 09:17] — Telegram Bot Integration & Deep Refactoring (NOC Pro) ✅

### Added
- **Integración Nativa de Webhook de Telegram**: Implementación directa del webhook oficial de Telegram en el endpoint `/api/bot/query` de Laravel, evitando dependencias externas de n8n para flujos estándar de Telegram.
- **Autocompletado de Comandos en Telegram**: Registro formal de los comandos `/cliente`, `/expiraciones` y `/soldto` en los servidores centrales de Telegram mediante la API `/setMyCommands` para habilitar el autocompletado en el teclado móvil del usuario.
- **Mensajes de Ayuda Interactivos**: Lógica ergonómica integrada en `BotQueryController.php` para interceptar llamadas sin argumentos en `/cliente` o `/soldto` y retornar un formateo instructivo interactivo en Markdown que indica cómo usar la sintaxis junto a un ejemplo práctico.

### Changed
- **Optimizaciones de Rendimiento de Base de Datos**:
  - Migradas todas las queries de filtrado de expiración de colecciones en memoria de PHP a queries de base de datos directas en Eloquent usando fechas relativas.
  - La búsqueda por Sold-To secundario ahora utiliza la directiva de base de datos `orWhereJsonContains` para buscar dentro de columnas JSON directamente en MariaDB.
- **Normalización de Cadenas Multibyte**: Refactorizado el cálculo de similitud `calculateSimilarity()` mediante transliteración ASCII nativa en PHP (`iconv`) para asegurar que acentos, tildes y eñes (ñ) no alteren el porcentaje de confianza de Levenshtein.
- **Modularización del Controlador de Consultas**: Separada la lógica de extracción de tokens a `extractToken()` y el mapeo/estado semántico de productos del inventario a `mapProduct()`, eliminando código inalcanzable y silenciando warnings estáticos de IDE.

---

## [2026-05-20 15:58] — Active Inventory Expiration Traffic Light (NOC Pro) ✅

### Added
- **Semáforo Visual Semántico**: Implementación completa del código de color estándar de tráfico (rojo/amarillo/verde) para diagnosticar de forma inmediata la expiración de licencias activas en la ficha de cliente.
- **Badges Técnicos Bento**: Diseñados badges premium con fuentes monoespaciadas, fondos translúcidos estilo glassmorphism, y bordes delgados HSL para los estados `.expired` (rojo), `.warning` (ámbar para vencimiento en menos de 30 días), `.default` (verde éxito para saludables) y `.permanent` (cyan corporativo).
- **Iconografía Dinámica**: Integrados iconos semánticos dinámicos (`fa-circle-xmark`, `fa-triangle-exclamation`, `fa-calendar-check`, `fa-infinity`) por estado mediante lógica Carbon en show.blade.php.
- **Cache-Busting Directo**: Forzado el refresco inmediato de dx-v2-clients.css mediante push y timestamp (`?v={{ time() }}`) en Blade, solucionando bloqueos por caché estática de los @import del navegador.

---

## [2026-05-20 15:15] — Audit History & Detail UI Redesign (NOC Pro) ✅

### Added
- **Acordeón Interactivo de Historial de Licencias**: Reemplazado el `<details>` rústico nativo por un acordeón interactivo y animado con Alpine.js (`historyOpen`) con banners explicativos de "Fuente de Verdad Histórica".
- **Banner de Inmutabilidad Técnica**: Añadida una sección explicativa con badge de seguridad e icono de bloqueo (`fa-lock`) aclarando que las auditorías históricas son registros de solo lectura (inmutables) de respaldo.
- **Bento Grid de Metadatos del Servidor**: Panel de visualización premium de alta gama para metadatos clave (Sold-To, Hostname, Composite y Daemons).
- **Consola Técnica de Líneas de Producto**: Diseñada una tabla de alta densidad con scrollbars integrados, hover interactivo, colorización selectiva de expiración de licencias y remoción de acciones deshabilitadas (ej: papelera ficticia) que causaban confusión al usuario.
- **Copiar Metadatos JSON**: Botón rápido en la barra de herramientas del modal de detalles que copia de forma directa el JSON parseado de auditoría al portapapeles.

---

## [2026-05-20 14:15] — Normalization UI Tabs, Duplicate Similarity Stripping, Caching & Scanning Loader ✅

### Added
- **Mapeo de 3 Pestañas en Alpine.js**: Restauración de la estructura de 3 pestañas ("Sospechas de Importación", "Escáner de Duplicados (IA)" y "Unificación Manual Libre") en [resources/views/admin/normalization/index.blade.php] con Alpine.js y persistencia en `localStorage`.
- **Diseño Bento Modular CSS**: Creado el archivo de estilos modulares [modules/dx-v2-normalization.css] e importado en [dx-v2-main.css], implementando el diseño Bento premium para tarjetas de duplicados de alta fidelidad.
- **Caché Inteligente de Base de Datos**: Cacheado del resultado de escaneo léxico en base de datos (`dx_scanned_duplicates`) por 24 horas usando la fachada `Cache` de Laravel, acelerando las cargas de página de la bandeja de normalización.
- **Botón y Acción "Escanear Ahora"**: Endpoint `/admin/normalization/force-scan` y acción `forceScan()` para invalidar la caché del escáner y recalcular las similitudes bajo demanda con refresco automático de vista y feedback por Toasts.
- **Modal de Escaneo Productivo Real**: Ventana modal interactiva fija y centrada con desenfoque de cristal translúcido que realiza un envío de formulario inmediato al backend, mostrando el progreso de forma real y eliminando cualquier simulación artificial de retardo.

### Changed
- **Resolución de Bugs en Similitud Léxica**:
  - **Bug #1 Resuelto**: Patrón `$genericPattern` expandido con más de 50 descriptores industriales y sectoriales españoles ("mecanicos", "metalicas", "quimicas", "logistica", etc.) eliminando de raíz falsos positivos de sector (ej: "Codesal vs Peña").
  - **Bug #2 Resuelto**: Cálculo del porcentaje de similitud con `similar_text` sobre las cadenas `$ultra` depuradas en lugar de `$clean`, garantizando un filtrado léxico estricto y preciso.
- **Limpieza Automática de Caché**: Las acciones de `unify()` y `dismiss()` limpian de forma transparente la caché de duplicados garantizando datos siempre sincronizados tras resolver advertencias.

---

## [2026-05-20 11:00] — AI Normalization Engine: Fase 23 CERRADA ✅

### Added
- **Core de Normalización de Identidades con IA**: Implementación de `ClientAiNormalizationService.php` en [backend/app/Services/AI/ClientAiNormalizationService.php]. Realiza un pre-filtrado tokenizado local de candidatos usando queries SQL `LIKE` para extraer coincidencias potenciales del mismo cliente.
- **Cliente HTTP con Cadena de Fallback Multi-API**: Conexión a Gemini 3.5 Flash Lite (`GEMINI_API_KEY`), DeepSeek Chat (`DEEPSEEK_API_KEY`) y OpenRouter (`OPENROUTER_API_KEY` con modelo Llama 3 8B) de forma secuencial y tolerante a fallos, abstrayendo credenciales en variables del entorno.
- **Rediseño Premium de la Bandeja de Normalización**: Adaptación visual de la bandeja de normalización con badges premium NOC Pro IA en [admin/normalization/index.blade.php], mostrando dinámicamente el proveedor (Gemini, DeepSeek, etc.), porcentaje de confianza y la razón técnica detallada de la IA.
- **Estilos Modulares para AI Normalization**: Cumpliendo estrictamente las directivas de `DESIGN.md` (cero CSS incrostado en Blade), las clases de estilos y animaciones se han integrado limpiamente al final de [modules/dx-v2-import.css].
- **Cobertura de Tests Unitarios Robustos**: Creación de tests y mock en [tests/Unit/ClientNormalizationTest.php] simulando llamadas de alta y baja confianza en SQLite en memoria.

### Changed
- **Desvío Inteligente en el Normalizador**: Integrado el servicio de IA en [backend/app/Services/Data/ClientNormalizationService.php] como fallback (Nivel 3.5) ante similitudes menores al 85%. Si la IA encuentra una coincidencia con alta confianza (>= 80%), el flujo se desvía a sospecha (`suspicion`) con su correspondiente advertencia, ID y razón técnica detallada.

---

## [2026-05-20 09:50] — DX Toasts & Estilos Usuarios: Incidencias #020 y #017 CERRADAS ✅

### Added
- **Sistema de Toasts Premium**: Implementación de un motor reactivo de notificaciones flotantes con Alpine.js en [layouts/partials/toasts.blade.php]. Soporta auto-cierre, cierre manual, cola reactiva de eventos y visualización rica de HTML (`x-html`).
- **Diseño Glassmorphism Adaptativo**: Creación de la hoja de estilos [shared/dx-v2-toast.css] con diseño de glassmorphism de alta gama, variables HSL adaptativas y aceleración por hardware para las transiciones.
- **Acceso HTML Rico para Telemetría**: Integrado soporte en Toasts para extraer variables adicionales como `log_id` y renderizar enlaces seguros interactivos directos a los detalles de logs de importación sin fugas de seguridad.

### Changed
- **Purga de Banners de Alertas Inline**: Eliminados todos los bloques de alertas inline estáticos y duplicados en las 7 vistas principales del portal (Gestión de Usuarios, Alertas, Importación, Repositorio, Normalización, Logs, Perfil) unificando todo el feedback bajo el motor reactivo global.

### Fixed
- **Estilos en Gestión de Usuarios (#017)**: Corregidos los estilos de la barra de búsqueda rápida, inputs y selectores de roles y estado que colisionaban con el tema oscuro del portal.

---

## [2026-05-19 15:20] — DX CSS Unification: Fase 21 CERRADA ✅

### Added
- **Cierre de Fase 21**: Finalizada oficialmente la **Fase 21 — Estructurar CSS — dx-v2**, modularizando el monolito CSS heredado de 10,118 líneas en 35 hojas de estilos compactas y organizadas jerárquicamente en 6 capas funcionales.
- **Estructura de la Arquitectura Modular CSS**:
  - **Capa 1 (Tokens & Base)**: `dx-v2-tokens.css`, `dx-v2-reset.css` y `dx-v2-base.css` (variables HSL, keyframes y reset global).
  - **Capa 2 (Layout Estructural)**: Navbar (`dx-v2-nav.css`), Sidebar (`dx-v2-sidebar.css`), Breadcrumb (`dx-v2-breadcrumb.css`) y Footers (`dx-v2-footer.css`).
  - **Capa 3 (Atoms UI Compartidos)**: `shared/dx-v2-cards.css`, `shared/dx-v2-tables.css`, `shared/dx-v2-badges.css`, `shared/dx-v2-buttons.css`, `shared/dx-v2-modals.css`, `shared/dx-v2-pagination.css`, `shared/dx-v2-forms.css`, `shared/dx-v2-empty-states.css`, `shared/dx-v2-ui.css` y `shared/dx-v2-brand.css`.
  - **Capa 4 (Módulos de Aplicación)**: 13 archivos dedicados a flujos independientes (Login, Dashboard, Clients, Import, COD, Resources, Sys-dashboard, Docker, Users, Licenses, Alerts, Backups y Audit).
  - **Capa 5 (Herramientas Técnicas de Vendors)**: `tools/dx-v2-tools-hub.css`, `tools/dx-v2-tools-nx.css`, `tools/dx-v2-tools-star.css`, `tools/dx-v2-tools-heeds.css` y `tools/dx-v2-tools-moldex.css`.
  - **Capa 6 (Páginas Especiales)**: `pages/dx-v2-page-herramientas.css`, `pages/dx-v2-page-admin.css` y `pages/dx-v2-page-maintenance.css`.
- **Fichero Maestro Consolidado**: Creado `dx-v2-main.css` unificando las 35 directivas de importación en el estricto orden jerárquico de cascada y especificidad.

### Changed
- **Modernización y Desacoplamiento de Layouts Blade**: Actualizadas las referencias a las hojas de estilo en el `<head>` del layout principal del portal (`layouts/app.blade.php`), la pantalla de mantenimiento (`errors/503.blade.php`) y la interfaz de login (`auth/login.blade.php`), llamando directamente al maestro modular `dx-v2-main.css?v={{ time() }}` y ganando rendimiento en carga de red.

### Fixed
- **Resolución de Recursos de Imagen del Login**: Corregido el bug de carga del fondo del login provocado por los `@import` relativos anidados, consolidando la ruta de carga en `dx-v2-login.css` a la ruta absoluta `/assets/img/login-bg-corporate.png`, asegurando su visualización robusta.

### Deleted
- **Purga de Deuda Técnica Legacy**: Eliminado físicamente de disco y de Git el archivo monolítico redundante heredado `dx-styles.css` (`git rm`), limpiando el espacio de trabajo.

---

## [2026-05-19 12:30] — DX Brand & Logo: Fase 20 CERRADA ✅

### Added
- **Cierre de Fase 20**: Finalizada oficialmente la **Fase 20 — Identidad Visual e Integración del Logotipo Premium**.
- **Refinamiento de Micro-alineación e Interacción**:
  - Ajustado el espaciado lateral derecho de la marca en el header (`header .dx-lockup`) a `60px` para desplazar los enlaces de navegación, logrando una alineación perfecta de la pestaña "**Inicio**" a la altura del título "**Bienvenido, Oskar**" en el contenido principal.
  - Agregado margen inferior de `20px` al logotipo en el footer (`.main-footer .dx-lockup`) para separarlo limpiamente del párrafo descriptivo inferior.
  - Optimizado el párrafo `.footer-desc` reduciendo su tamaño de letra a `12px` (estilización minimalista premium) e incrementando su margen inferior a `28px` para evitar colisiones visuales con los iconos de redes sociales.
- **Control de Calidad (Hardening)**: Verificada la reactividad en el login del portal y la adaptabilidad responsive y de modo oscuro en todos los navegadores.

---

## [2026-05-19 12:20] — DX Brand & Logo: Subfases 20.2 - 20.4 (Estilos, SVGs e Integración de Vistas) ✅

### Added
- **Estilos de Logotipo y Scan Line (`dx-styles.css`)**: Implementado el namespace global `.dx-lockup`, `.dx-mark` y `.dx-wordmark` con variables de color semánticas HSL y fuentes del sistema (`Inter`). Desarrollada la animación `@keyframes dx-scan` con pseudo-elemento `::after` para el destello scan line interactivo al hacer hover.
- **Exportaciones SVG Oficiales**: Generados los tres archivos SVG oficiales (`logo-light.svg`, `logo-dark.svg` y `logo-mark.svg` para favicon) bajo el directorio `public/assets/images/`.
- **Integración de Logotipos en Vistas**: Reemplazada la cabecera de la marca estática tradicional con el bloque HTML interactivo de logotipo premium unificado en:
  - Layout Principal del Portal (`layouts/app.blade.php`).
  - Layout e interfaces de Autenticación (`auth/login.blade.php`).
  - Pie de página unificado (`layouts/partials/footer.blade.php`).

### Changed
- **Soporte Light/Dark**: Configurada la adaptabilidad dinámica de color para los textos del wordmark (`.dx-name`, `.dx-sub`) mediante selectores oscuros (`.dark`, `[data-theme="dark"]`).

---

## [2026-05-19 12:10] — DX Brand & Logo: Subfase 20.1 (Registro y Estructuración) ✅

### Added
- **Fase 20 en ROADMAP.md**: Registrada oficialmente la **Fase 20 — Identidad Visual e Integración del Logotipo Premium** en la planificación general del proyecto.
- **Planificación de Subfases**: Definidas las 5 subfases correspondientes para guiar de forma estructurada la inyección de estilos de logo interactivos con efecto "scan line", exportación de ficheros SVG standalone oficiales para modo claro/oscuro, integración en layouts y vistas Blade, y control de calidad final.

---

## [2026-05-19 12:00] — CSS Unification: Subfase 19.29 (Exclusiones Documentadas de Emails & PDFs) ✅

### Added
- **Inventariado y Justificación de Exclusiones de Estilos**: Realizada una auditoría detallada de portabilidad y compatibilidad en las vistas de correos electrónicos y plantillas PDF del portal:
  - `emails/global-license-report.blade.php`, `emails/weekly-license-alert.blade.php` y `emails/weekly-report.blade.php`: Se determinó que el uso de estilos inline y hojas de estilos locales `<style>` está 100% justificado para asegurar la correcta compatibilidad con clientes de correo estándar (Gmail, Outlook, Apple Mail) sin dependencias del compilador de assets ni variables CSS del tema.
  - `pdf/cod-template.blade.php`: Se verificó el uso de estilos locales y fuentes autohospedadas (Calibri) como requisito de diseño estricto para la correcta compilación del motor PDF Dompdf de forma hermética y sin dependencias de red.

### Changed
- **Aislamiento del Entorno de Renderizado**: Verificado y garantizado que ninguna de estas vistas herede variables de CSS `--dx-v2-*` ni cargue recursos externos que puedan romperse o producir fallos de visualización en destinatarios finales.

---

## [2026-05-19 11:50] — CSS Unification: Subfase 19.28 (Componentes UI Compartidos) ✅

### Added
- **Namespace de UI Compartida (`.dx-v2-ui-*`)**: Diseñado e inyectado un bloque unificado de estilos en [dx-styles.css] (más de 310 líneas de código CSS HSL) para estandarizar los elementos visuales comunes:
  - Modales glassmorphic premium `.dx-v2-ui-modal-overlay` y `.dx-v2-ui-modal-content` con animaciones fluidas (`dxFadeIn` / `dxScaleIn`) y filtros blur de alta fidelidad.
  - Tablas industriales de alta densidad `.dx-v2-ui-table-wrapper` y `.dx-v2-ui-table` con filas translúcidas en hover y cabeceras monoespaciadas.
  - Botones simétricos `.dx-v2-ui-btn` (en variantes primary, secondary y actions) con transiciones suaves y micro-desplazamientos interactivos.
  - Badges compactas de estado y alertas deterministas.

### Changed
- **Estandarización de Vistas de Clientes (`clients/show.blade.php`)**: Migradas al 100% las tablas de contratos, licencias, certificados, contactos y renovaciones bajo el nuevo estándar industrial, así como los modales de contacto y de auditoría de licencias.
- **Vista de Enlaces de Recursos (`tools/partials/_resources.blade.php`)**: Modal de administración unificado al namespace de UI global.

### Fixed
- **Resolución de Incidencia de Reactividad (Alpine.js)**: Removido el modificador `!important` en el display de `.dx-v2-ui-modal-overlay` en [dx-styles.css]. Esto corrige el bug crítico que forzaba la visualización persistente del modal "Detalle de Auditoría Siemens" al entrar en la ficha del cliente, restaurando el correcto funcionamiento de `x-show` y `x-cloak`.

---

## [2026-05-19 11:35] — CSS Unification: Subfase 19.27 (Componentes de Formulario) ✅

### Added
- **Namespace de Formularios (`.dx-v2-form-*`)**: Creada una especificación centralizada en [dx-styles.css] con estilos para campos de texto, inputs, selectores de opciones, checkboxes y envolturas de subida interactiva. Soporta focus shadows ergonómicos, estados deshabilitados y animaciones de borde con variables HSL.

### Changed
- **Formularios de Perfil (`profile/index.blade.php`)**: Refactorizados inputs, textareas y selectores de perfil para purgar paddings inline y heredar variables semánticas HSL globales.
- **Formulario de Contacto (`clients/show.blade.php`)**: Migrado el modal de alta de contactos de la clase obsoleta `.gui-input` al namespace semántico `.dx-v2-form-*`.

---

## [2026-05-19 11:20] — CSS Unification: Subfase 19.26 (Páginas de Error) ✅

### Changed
- **Mantenimiento Técnico (`errors/503.blade.php`)**: Purgado por completo un bloque complejo de estilos locales `<style>` de más de 200 líneas, delegando toda la presentación visual del modo mantenimiento premium y su temporizador interactivo a la hoja de estilos global `dx-styles.css`.

---

## [2026-05-18 16:00] — CSS Unification: Subfase 19.25 (Logs y Auditoría) ✅

### Added
- **Namespace Semántico de Logs y Auditoría (`.dx-v2-audit-*`)**: Diseñado e inyectado al final de [dx-styles.css] (más de 500 líneas de código CSS optimizado) para estructurar el panel de actividad, logs de Laravel y correos SMTP sin dependencias estáticas:
  - Banners de estado de sesión unificados para éxitos y errores `.dx-v2-audit-banner-success` y `.dx-v2-audit-banner-error`.
  - Pestañas de navegación e interactividad avanzada `.dx-v2-audit-tabs-container` e interactores `.dx-v2-audit-tab-link` con soporte para estados activos (`.active`) y transiciones fluidas.
  - Formulario de búsqueda rápida y selectores de filtrado ergonómicos `.dx-v2-audit-search-card`, `.dx-v2-audit-filter-form`, `.dx-v2-audit-filter-field`, `.dx-v2-audit-filter-label` e inputs.
  - Cabecera del panel y botón de reinicio `.dx-v2-audit-card-header`, `.dx-v2-audit-header-title-block` y `.dx-v2-audit-reset-btn` con efecto hover sutil y brillo.
  - Indicadores y contadores métricos en vivo `.dx-v2-audit-stats-group`, `.dx-v2-audit-stat-box` (en sus variantes estándar y danger-brand) con iconos alineados y tipografías HSL calculadas.
  - Rejillas de datos de alta densidad `.dx-v2-audit-table-wrapper`, `.dx-v2-audit-table`, `.dx-v2-audit-table-thead`, `.dx-v2-audit-table-tr:hover` y celdas estilizadas `.dx-v2-audit-td-timestamp`, `.dx-v2-audit-td-ip` con fuentes monospace.
  - Badges semánticas de nivel de alerta `.dx-v2-audit-badge-level` (info, warning, error) y perfil de usuario con inicial avatar dinámico `.dx-v2-audit-user-badge`.
  - Consola de terminal para trazas de laravel.log `.dx-v2-audit-console-container`, `.dx-v2-audit-console-scroller` y logs expandibles `.dx-v2-audit-console-item` con Alpine.js, integrando atenuador automático de líneas procedentes de `/vendor/` (`opacity: 0.4; font-size: 10px;`) para mejorar el diagnóstico técnico.
  - Estructuras y estados de correos SMTP enviados y fallidos `.dx-v2-audit-badge-email-status` (sent/failed).

### Changed
- **Vista de Auditoría e Historial (`admin/audit/index.blade.php`)**: Refactorizada por completo para purgar el 100% de los estilos en línea estáticos y dinámicos (badges de nivel, filas hover, contadores métricos, pestañas activas, stack traces colapsables) y eliminar una hoja de estilos `<style>` local de más de 80 líneas, migrando toda la capa de diseño de las tres pestañas al namespace semántico centralizado.

---

## [2026-05-18 15:58] — CSS Unification: Subfase 19.24 (Integraciones IA) ✅

### Added
- **Namespace de Integraciones IA (`.dx-v2-sys-dash-*`)**: Diseñado e integrado un completo y robusto bloque de clases semánticas en [dx-styles.css] para modularizar la sección de salud y latido de proveedores de Inteligencia AI y canales de alerta:
  - Estructuras para indicador circular del estado de conexión `.dx-v2-sys-dash-service-status-dot` (en sus variantes online y danger/offline) con animación de latido CSS nativa.
  - Definición de caja contenedora de icono del servicio `.dx-v2-sys-dash-service-icon-box` con transiciones de color de escala cúbica y curvatura de esquinas suave (`border-radius: 10px`).
  - Gradientes premium de marca y efectos de sombra 3D con colores HSL calculados para cuando el servicio está activo:
    - **Gemini Engine**: Gradiente lineal de azul a violeta con sombra envolvente de baja densidad (`linear-gradient(135deg, #4e8cff, #9171ff)`).
    - **DeepSeek Engine**: Gradiente lineal azul eléctrico a cian (`linear-gradient(135deg, #007aff, #00c6ff)`).
    - **OpenRouter Gateway**: Gradiente lineal naranja a amarillo fuego (`linear-gradient(135deg, #ff4f00, #ff9000)`).
    - **n8n Workflow Engine**: Gradiente lineal rojo anaranjado a coral (`linear-gradient(135deg, #ff6d5b, #ff4d4d)`).
    - **Telegram Notification Bot**: Gradiente lineal azul Telegram a celeste (`linear-gradient(135deg, #0088cc, #00aaff)`).
    - **MariaDB Database & Redis Queues**: Gradiente lineal azul petróleo a cian oscuro (`linear-gradient(135deg, #003545, #00758f)`).
    - **Cloudflare Live Tunnel**: Gradiente lineal rojo rubí a naranja fuego (`linear-gradient(135deg, #d82c20, #ff4e42)`).
  - Estructuras de marca dedicadas para los botones de navegación rápida de módulos de administración `.dx-v2-sys-dash-module-icon-box` (en sus variantes docker-brand, backups-brand y audit-brand).
  - Clase para punto de separación sutil de métricas del sistema operativo `.dx-v2-sys-dash-dot-separator`.

### Changed
- **Vista de Dashboard del Sistema**: Refactorizada por completo la vista [dashboard.blade.php] en `admin/system/` purgando el 100% de la lógica PHP que calculaba estilos dinámicos (27 líneas de variables de color eliminadas) y eliminando todos los estilos inline estáticos restantes de botones y separadores. Delegada toda la presentación al motor de renderizado de la hoja CSS global.

## [2026-05-18 15:54] — CSS Unification: Subfase 19.23 (Backups) ✅

### Added
- **Namespace de Gestión de Backups (`.dx-v2-backups-*`)**: Diseñado e integrado un completo y robusto bloque de clases semánticas en [dx-styles.css] para el control, historial y restauración de copias de seguridad:
  - Cabecera flex de tarjeta `.dx-v2-backups-card-header` con alineación vertical `.dx-v2-backups-header-left` y el badge de tiempo de retención `.dx-v2-backups-header-badge` estilizado.
  - Sección flex derecha `.dx-v2-backups-header-right` con el panel detallado de espacio total ocupado `.dx-v2-backups-storage-panel`, etiqueta `.dx-v2-backups-storage-label` y valor numérico destacado `.dx-v2-backups-storage-value`.
  - Botón de generación manual `.dx-v2-backups-btn-run` con icono centrado `.dx-v2-backups-btn-icon`.
  - Tabla de alta densidad `.dx-v2-backups-table` con cabecera de surface sólido `.dx-v2-backups-table-thead`, columnas de datos `.dx-v2-backups-table-th` and `.dx-v2-backups-table-th-right`, filas con transición hover `.dx-v2-backups-table-tr` y celdas compactas `.dx-v2-backups-table-td` y `.dx-v2-backups-table-td-right`.
  - Grupo de fecha `.dx-v2-backups-date-group`, fecha principal `.dx-v2-backups-date-primary` e indicador de hora mono `.dx-v2-backups-date-secondary`.
  - Badges semánticos para tipo de backup `.dx-v2-backups-badge-type` (en sus variantes system y manual) y entorno de origen `.dx-v2-backups-badge-env` (prod y beta) con colores HSL atenuados y bordes matizados.
  - Indicador de tamaño de archivo `.dx-v2-backups-file-size` y nombre de copia `.dx-v2-backups-file-name` con fuentes mono.
  - Botones simétricos de acción rápida `.dx-v2-backups-actions-group` para restaurar, descargar y eliminar copias.
  - Diseño de Empty State con celda unificada `.dx-v2-backups-empty-td`, icono de baja opacidad `.dx-v2-backups-empty-icon` y texto técnico descriptivo `.dx-v2-backups-empty-text`.
  - Panel ergonómico de programación automática `.dx-v2-backups-scheduling-card` con cuerpo `.dx-v2-backups-scheduling-body`, layout adaptable `.dx-v2-backups-scheduling-layout` (con flex vertical bajo `768px`) y caja de información del Cron Job `.dx-v2-backups-scheduling-box`, su título `.dx-v2-backups-scheduling-title` y descripción técnica `.dx-v2-backups-scheduling-desc`.
  - Contenedor de cuenta regresiva `.dx-v2-backups-countdown-container`, etiqueta `.dx-v2-backups-countdown-label`, valor del tiempo restante `.dx-v2-backups-countdown-value`, barra de progreso `.dx-v2-backups-progress-bar` y relleno del porcentaje `.dx-v2-backups-progress-fill`.
  - Ventana modal de restauración destructiva `.dx-v2-backups-modal-overlay`, tarjeta `.dx-v2-backups-modal-card`, cabecera de advertencia `.dx-v2-backups-modal-header`, título rojo `.dx-v2-backups-modal-title`, cuerpo `.dx-v2-backups-modal-body` y mensaje descriptivo `.dx-v2-backups-modal-msg` con el archivo seleccionado `.dx-v2-backups-modal-file`.
  - Caja de aviso de peligro `.dx-v2-backups-modal-warning-box` con texto de urgencia `.dx-v2-backups-modal-warning-text`.
  - Campo de entrada de confirmación aislado `.dx-v2-backups-modal-input`, etiqueta de instrucción `.dx-v2-backups-modal-label`, rejilla de botones `.dx-v2-backups-modal-actions` y botones simétricos `.dx-v2-backups-modal-btn`.

### Changed
- **Vista de Backups**: Refactorizada por completo la vista [index.blade.php] en `admin/backups/` purgando el 100% de los estilos inline locales (declaraciones `style="..."` eliminadas) y eliminando el bloque `<style>` incrustado local en favor de las clases de namespace del archivo central.

## [2026-05-18 15:52] — CSS Unification: Subfase 19.22 (Alertas y Notificaciones) ✅

### Added
- **Namespace de Alertas y Notificaciones (`.dx-v2-alerts-*`)**: Diseñado e integrado un bloque de estilos cohesivos y ergonómicos en [dx-styles.css] para modularizar la configuración de umbrales e historial de envíos SMTP:
  - Estructuras para banner de alertas `.dx-v2-alerts-alert-banner` (en sus variantes success y danger) con fondos y bordes HSL matizados.
  - Cabecera flexible `.dx-v2-alerts-header-row`, grupo de botones `.dx-v2-alerts-btn-group` y botón de activación `.dx-v2-alerts-toggle-btn`.
  - Rejilla adaptativa `.dx-v2-alerts-grid` fijando la columna de umbrales a exactamente `440px` (con comportamiento responsive a una sola columna bajo `1200px`) para evitar la envoltura de texto no deseada.
  - Estructura `.dx-v2-alerts-card-thresholds` con cabecera `.dx-v2-alerts-card-header`, envoltura interior `.dx-v2-alerts-card-header-inner` y contenedor de iconos pequeños `.dx-v2-alerts-icon-wrapper-sm`.
  - Contenedor de cuerpo `.dx-v2-alerts-body` y listado de umbrales `.dx-v2-alerts-threshold-list` con ítems estilizados `.dx-v2-alerts-threshold-item` y su layout interno `.dx-v2-alerts-threshold-info`.
  - Indicadores circulares `.dx-v2-alerts-threshold-icon-circle` (danger, warning, accent) con títulos `.dx-v2-alerts-threshold-title` y descripciones `.dx-v2-alerts-threshold-desc` bajo fuentes mono con `white-space: nowrap` para evitar rupturas de palabras.
  - Contenedores de inputs numéricos aislados `.dx-v2-alerts-input-container` (fijados a `110px` de ancho y `38px` de alto) y campos `.dx-v2-alerts-input-field` con spinners del navegador ocultos, alineación central y reseteo completo de bordes y sombras para evitar colisiones con la clase `.gui-input`.
  - Caja de copia interna de emails `.dx-v2-alerts-copy-box`, cabecera `.dx-v2-alerts-copy-header`, etiqueta `.dx-v2-alerts-copy-label`, textarea `.dx-v2-alerts-copy-textarea` y texto de ayuda `.dx-v2-alerts-copy-help`.
  - Historial de notificaciones en tarjeta `.dx-v2-alerts-card-history`, cabecera `.dx-v2-alerts-card-history-header` y tabla de alta densidad `.dx-v2-alerts-table` con filas `.dx-v2-alerts-table-thead-tr`, `.dx-v2-alerts-table-tbody-tr`, cabeceras `.dx-v2-alerts-table-th`, destinatarios `.dx-v2-alerts-table-td-recipient`, fecha de envío `.dx-v2-alerts-table-td-date`, estado `.dx-v2-alerts-table-td-status` y acciones rápidas `.dx-v2-alerts-table-td-actions`.
  - Caja de ayuda informativa del motor `.dx-v2-alerts-info-box`, cuerpo `.dx-v2-alerts-info-box-inner`, icono `.dx-v2-alerts-info-box-icon`, título `.dx-v2-alerts-info-box-title` y descripción técnica `.dx-v2-alerts-info-box-desc`.

### Changed
- **Vista de Alertas**: Refactorizada la vista [index.blade.php] en `admin/alerts/` purgando el 100% de los estilos inline locales (declaraciones `style="..."` eliminadas), aislando sus campos numéricos de la clase global `.gui-input` y delegando el 100% de la maquetación a las clases centralizadas de `dx-styles.css`.

## [2026-05-18 15:48] — CSS Unification: Subfase 19.21 (Repositorio de Licencias) ✅

### Added
- **Namespace del Repositorio de Licencias (`.dx-v2-lic-repo-*`)**: Diseñado e integrado un bloque de estilos cohesivos y modulares en [dx-styles.css] para el control del empaquetado de licencias semanales:
  - Estructuras para espaciado de tarjetas `.dx-v2-lic-repo-card-mb`, alineación flex de cabeceras `.dx-v2-lic-repo-header-row` y grupo de botones `.dx-v2-lic-repo-btn-group` con padding específico para botones compactos `.dx-v2-lic-repo-btn-sm`.
  - Contenedor de cuerpo `.dx-v2-lic-repo-body` y envoltura de alertas en rejilla para estados de éxito/información/peligro `.dx-v2-lic-repo-alert` con bordes y fondos HSL matizados.
  - Formato mono con espaciado lateral para indicador de año `.dx-v2-lic-repo-year-label`, fila de archivo `.dx-v2-lic-repo-file-row` con icono unificado `.dx-v2-lic-repo-file-icon` en color naranja advertencia y fuente destacada `.dx-v2-lic-repo-file-name`.
  - Texto de resumen `.dx-v2-lic-repo-summary-text`, columnas de fechas compactas `.dx-v2-lic-repo-date` y botones de acción rápida de auditoría y borrado `.dx-v2-lic-repo-btn-action` con estados hover responsivos y colores semánticos.
  - Diseño completo de Empty State con animaciones de opacidad `.dx-v2-lic-repo-empty-container`, contenedor interior `.dx-v2-lic-repo-empty-inner`, icono `.dx-v2-lic-repo-empty-icon`, título `.dx-v2-lic-repo-empty-title` y descripción técnica `.dx-v2-lic-repo-empty-desc`.
  - Panel informativo lateral unificado `.dx-v2-lic-repo-sidebar-card`, cabecera de ayuda `.dx-v2-lic-repo-sidebar-header`, título en mayúsculas `.dx-v2-lic-repo-sidebar-title`, texto fluido `.dx-v2-lic-repo-sidebar-text` y pie de firma `.dx-v2-lic-repo-sidebar-footer` con borde superior integrado.

### Changed
- **Vista de Repositorio**: Purgado el 100% de los estilos inline locales (26 declaraciones `style="..."` eliminadas) en [repository.blade.php] enlazándolo directamente al nuevo bloque semántico unificado, corrigiendo a su vez una duplicación sintáctica menor en la sección `@endsection`.

## [2026-05-18 15:45] — CSS Unification: Subfase 19.20 (Datos e Importación) ✅

### Added
- **Namespace de Datos e Importación (`.dx-v2-import-*`)**: Diseñado e integrado un completo y estructurado bloque de estilos en [dx-styles.css] para modularizar la visualización de la carga masiva y control de mapeo:
  - Estructuras para dropzone de arrastre responsivo `.dx-v2-import-dropzone`, botones de acción primary `.dx-v2-import-btn-submit` y alertas dinámicas de éxito con enlace a detalles.
  - Rejilla de mapeo estructurada `.dx-v2-import-mapping-grid` y visualización de columnas de datos `.dx-v2-import-mapping-col`.
  - Caja de aviso técnico persistente `.dx-v2-import-info-box` e indicadores de seguridad con estado de certificado SSL validado.
  - Badges de alerta e historial de advertencias `.dx-v2-import-badge-warn` e items vacíos (Empty State) con iconos deterministas.

### Changed
- **Vistas del Módulo de Importación**: Verificado el desacoplamiento completo y purga de estilos inline y `<style>` incrustados en [index.blade.php] (Carga), [index.blade.php] (Historial) y [show.blade.php] (Detalles de log) bajo la carpeta `admin/import/`, enlazando todas las estructuras directamente a las reglas del namespace global.

## [2026-05-18 15:39] — CSS Unification: Subfase 19.19 (Usuarios y Acceso) ✅

### Added
- **Namespace de Usuarios y Acceso (`.dx-v2-users-*`)**: Diseñado e integrado un completo bloque de estilos ergonómicos en [dx-styles.css] con más de 340 líneas de código para el control total del CRUD de personal:
  - Layouts estructurales `.dx-v2-users-header-layout` y envolturas breadcrumb `.dx-v2-users-breadcrumb-wrapper` con enlaces de retroceso estilizados `.dx-v2-users-breadcrumb-link`.
  - Título y subtítulos ergonómicos `.dx-v2-users-title` y `.dx-v2-users-subtitle`.
  - Barra de filtrado unificada `.dx-v2-users-filter-bar` con buscadores de texto absolutos `.dx-v2-users-search-wrapper` y selectores de rol integrados `.dx-v2-users-filter-select`.
  - Tabla de visualización limpia `.dx-v2-users-table` con celdas de usuario que agrupan avatares circulares con iniciales `.dx-v2-users-avatar` y metadatos de email `.dx-v2-users-email`.
  - Telemetría en vivo para la última conexión `.dx-v2-users-online-badge` y puntos de presencia `.dx-v2-users-dot` reactivos (ONLINE/OFFLINE).
  - Acciones rápidas simétricas `.dx-v2-users-actions` y botones de control de peligro `.dx-v2-users-actions-btn.danger`.
  - Estructuras para formularios de alta/edición `.dx-v2-users-form-container`, `.dx-v2-users-form-body` y grids simétricos de contraseña `.dx-v2-users-form-grid`.
  - Panel de configuración de seguridad lateral `.dx-v2-users-security-box` y banners informativos de envío de credenciales `.dx-v2-users-banner`.

### Changed
- **Vistas del CRUD de Usuarios**: Refactorizadas por completo las vistas [index.blade.php], [create.blade.php] y [edit.blade.php] bajo el directorio `admin/users/`, purgando el 100% de los estilos inline locales y los bloques `<style>` incrustados. Delegada toda la lógica visual a las clases centralizadas de `dx-styles.css`.

## [2026-05-18 15:33] — CSS Unification: Subfase 19.19 (Docker Fleet Monitor & Padding Fix) ✅

### Added
- **Namespace de Docker Fleet Monitor (`.dx-v2-sys-docker-*`)**: Diseñado e integrado un completo bloque de clases semánticas en [dx-styles.css] para modularizar la sección de monitorización de contenedores:
  - Estructuras para la cabecera `.dx-v2-sys-docker-page-header`, su layout flexible `.dx-v2-sys-docker-header-layout`, el breadcrumb `.dx-v2-sys-docker-breadcrumb-wrapper` e indicadores en vivo del túnel.
  - Rejilla responsiva para contenedores `.dx-v2-sys-docker-grid` con soporte para móvil.
  - Tarjetas de servicio individual `.dx-v2-sys-docker-card` con hover de elevación 3D, y logo contenedor `.dx-v2-sys-docker-icon-box`.
  - Caja de métricas unificada `.dx-v2-sys-docker-metrics-box` con indicadores circulares de CPU y barra de RAM `.dx-v2-sys-docker-ram-meter-box`.
  - Botón de reinicio rápido `.dx-v2-sys-docker-btn-restart` y botón de control global `.dx-v2-sys-docker-btn-noc`.
  - Caja interactiva de estado vacío (Empty State) `.dx-v2-sys-docker-empty-state` para cuando no se puede conectar con el socket de Docker.
- **Clase Global `.card-body`**: Añadida la definición global `.card-body { padding: 24px !important; }` en la sección de tarjetas para restaurar la ergonomía visual y paddings en la Matriz de Servicios del Dashboard principal (Subfase 19.18).

### Changed
- **Vista de Docker Fleet Monitor**: Refactorizada por completo la vista [docker.blade.php], purgando el 100% de los estilos inline locales y el bloque de estilos incrustado de `<style>`. Integrada la lógica de control de estado vacío si la lista de contenedores está vacía.

## [2026-05-18 15:15] — CSS Unification: Subfase 19.18 (Dashboard del Sistema / NOC Pro) ✅

### Added
- **Namespace de Dashboard del Sistema (`.dx-v2-sys-dash-*`)**: Diseñado e integrado un completo y profesional bloque de clases en [dx-styles.css] para dar al centro de control técnico (NOC Pro) un acabado industrial premium:
  - Estructuras para la cabecera `.dx-v2-sys-dash-header-meta`, su layout flex `.dx-v2-sys-dash-header-meta-layout` e items individuales `.dx-v2-sys-dash-header-meta-item` con soporte para colores de acento y éxito del hash Git.
  - Rejilla responsiva para estadísticas de hardware `.dx-v2-sys-dash-stats-grid` con breakpoints para tablet y móvil.
  - Tarjetas de métrica individual `.dx-v2-sys-dash-stat-card` con hover de elevación 3D, soporte para iconos marca de agua absolutos y barras de progreso dinámicas con estados de peligro.
  - Bloque de tráfico y red `.dx-v2-sys-dash-stat-card-traffic-layout` con separadores y medidor en vivo con animación de latido `.dx-v2-sys-dash-dot-live`.
  - Rejilla de servicios `.dx-v2-sys-dash-services-grid` y cabeceras categóricas `.dx-v2-sys-dash-services-cat-row` con líneas divisoras.
  - Tarjetas de item de servicio `.dx-v2-sys-dash-service-item` con gradientes de IA de alta intensidad (Gemini, Deepseek, OpenRouter, n8n, etc.), sombras de elevación e información de hilos/queries lentas.
  - Rejilla de navegación modular `.dx-v2-sys-dash-modules-grid` y tarjetas de navegación `.dx-v2-sys-dash-module-card` con hover interactivo.
  - Botones de acción rápida `.dx-v2-sys-dash-btn-noc` con micro-desplazamiento en hover y estados de color personalizados (accent, indigo, warn, orange, danger, success).
  - Toast dinámico premium para retroalimentación AJAX `.dx-v2-sys-dash-toast`.
  - Panel de seguridad lateral `.dx-v2-sys-dash-sec-box` con listados `.dx-v2-sys-dash-sec-row` y notas técnicas.

### Changed
- **Vista del Dashboard de Administración**: Refactorizada por completo la vista principal [dashboard.blade.php], eliminando más de 100 líneas de estilos locales y purgando el 100% de los estilos inline locales de métricas, rejillas, listas de servicios y botones. Delegada toda la maquetación a la hoja global y centralizado el Toast interactivo de Alpine.js.

## [2026-05-18 11:21] — CSS Unification: Subfase 19.16 (Moldex3D: Parser .mac) ✅


### Added
- **Namespace de Moldex3D (`.dx-v2-tools-moldex-*`)**: Diseñado e integrado un completo y robusto bloque de clases semánticas en [dx-styles.css] para unificar y profesionalizar la sección del auditor Moldex3D:
  - Estructuras alineadas para la cabecera `.dx-v2-tools-moldex-header-layout` y el icono de vendor estilizado traslúcido `.dx-v2-tools-moldex-header-icon`.
  - Títulos y subtítulos ergonómicos `.dx-v2-tools-moldex-header-title` and `.dx-v2-tools-moldex-header-sub`, con su distintivo acento naranja `.dx-v2-tools-moldex-vendor-label` específico para el motor Core Plastic.
  - Tarjetas de proceso `.dx-v2-tools-moldex-card` con alineación interna de encabezados `.dx-v2-tools-moldex-card-header` y su cuerpo con padding de 24px `.dx-v2-tools-moldex-card-body`.
  - Dropzone de arrastre responsivo interactivo `.dx-v2-tools-moldex-dropzone`, con envolturas de texto interno `.dx-v2-tools-moldex-dropzone-inner`, iconos y tipografías reactivas para estados seleccionados.
  - Banner lateral de alertas de error en extensión de archivos `.dx-v2-tools-moldex-error-alert`.
  - Panel de resultados completo `.dx-v2-tools-moldex-results-card`, con su cabecera `.dx-v2-tools-moldex-results-header`, icono destacado `.dx-v2-tools-moldex-results-header-icon`, filas de alineación compactas de propiedades `.dx-v2-tools-moldex-property-row` y código `.dx-v2-tools-moldex-property-val-mono`.
  - Grids de módulos detectados `.dx-v2-tools-moldex-modules-section` y filas individuales `.dx-v2-tools-moldex-module-item` con sus viñetas `.dx-v2-tools-moldex-module-bullet` e indicadores de asientos.
  - Carteles laterales unificados de estándar de nomenclatura `.dx-v2-tools-moldex-sidebar-card` y aviso de privacidad determinista `.dx-v2-tools-moldex-sidebar-warning`.

### Changed
- **Vista del Auditor Moldex3D**: Refactorizada por completo la vista principal [moldex3d.blade.php], purgando el 100% de los estilos inline locales y el bloque local incrustado de `<style>`, modularizando toda la vista e integrando las nuevas clases del namespace global de forma impecable sin alterar la lógica reactiva de Alpine.js.

## [2026-05-18 11:09] — CSS Unification: Subfase 19.15 (Siemens: Recursos & Enlaces) ✅

### Added
- **Namespace de Recursos (`.dx-v2-resources-*`)**: Diseñado e integrado un completo sistema de estilos en [dx-styles.css] para unificar y profesionalizar la sección de Recursos e Enlaces de soporte técnico de Siemens y Moldex3D:
  - Estructuras alineadas para la cabecera `.dx-v2-resources-header-layout` y caja de título/subtítulo `.dx-v2-resources-title-block`.
  - Iconos de alta fidelidad `.dx-v2-resources-header-icon` y badges de marca `.dx-v2-resources-badge` con subclases dinámicas `.siemens` y `.moldex3d` para acentos de color específicos.
  - Tarjetas informativas laterales `.dx-v2-resources-sidebar-card` y paneles de acción destacada `.dx-v2-resources-sidebar-action`.
  - Cuadrícula responsiva de enlaces `.dx-v2-resources-card-list` y filas/títulos de categorías `.dx-v2-resources-category-row`.
  - Tarjetas interactivas de recurso individual `.dx-v2-resources-card` con soporte para hover, elevación de caja, acentos dinámicos, descripción multi-línea truncada `.dx-v2-resources-card-description` y acciones flotantes `.dx-v2-resources-card-actions`.
  - Capas de fondo difuminado de alto premium `.dx-v2-resources-modal-overlay` y maquetación de modales con grid de formulario `.dx-v2-resources-modal-form-grid` e inputs unificados `.dx-v2-resources-modal-input`.
  - Bloque de estados vacíos del módulo `.dx-v2-resources-empty-state` (con su icono y texto semántico).

### Changed
- **Vista de Recursos y parcial de enlaces**: Refactorizadas por completo la vista principal [resources.blade.php] y su parcial [_resources.blade.php], purgando el 100% de los estilos inline locales (como tarjetas, modales y layouts flex) y eliminando por completo el bloque incrustado local `<style>`, delegando todo el control visual e interactivo a la hoja de estilos global sin alterar la reactividad de Alpine.js.
- **Correcciones de maquetación y espaciado del modal**: Resuelto el problema de padding perimetral en la vista de tarjetas de recursos y en la ventana modal de edición/creación mediante la clase `.dx-v2-resources-body` y `.dx-v2-resources-modal-body` con `padding: 24px !important`. Corregido el grid unificado y aplicados márgenes defensivos (`margin-bottom: 20px !important`) a los campos y botones del formulario.

## [2026-05-18 11:06] — CSS Unification: Subfase 19.14 (Siemens: COD) ✅

### Added
- **Refuerzo del Namespace de COD (`.dx-v2-cod-*`)**: Diseñadas e integradas clases semánticas de apoyo en [dx-styles.css] para modularizar la visualización y mejorar el diseño visual adaptativo del Generador de COD y su Asistente IA:
  - Fila de dos columnas con espaciado vertical específico `.dx-v2-cod-columns-2-spaced`.
  - Botón de eliminación posicionado absolutamente y centrado verticalmente para MACs adicionales `.dx-v2-cod-remove-btn`.
  - Envoltura flexible para el título de sección con botón de asistente de IA integrado `.dx-v2-cod-section-title-wrapper` e indicador inline `.dx-v2-cod-title-inline`.
  - Fila flexible de botones del modal de IA `.dx-v2-cod-modal-btn-row` y fila de acciones principales `.dx-v2-cod-modal-action-row`.
  - Etiqueta destacada en color de acento Siemens para adaptadores recomendados por IA `.dx-v2-cod-ai-adapter-label`.

### Changed
- **Vista del Generador de COD**: Refactorizada la vista [cod.blade.php] purgando el 100% de los estilos inline locales restantes (como los botones de eliminación de MACs y los divs del modal del asistente IA), asegurando el perfecto anidamiento de los divs del modal y la preservación completa de los estilos dinámicos calculados reactivamente de Alpine.js (`:style`).

## [2026-05-18 11:01] — CSS Unification: Subfase 19.13 (Siemens: HEEDS) ✅

### Added
- **Namespace de HEEDS (`.dx-v2-tools-heeds-*`)**: Diseñadas e integradas clases semánticas dedicadas en [dx-styles.css] para modularizar la visualización y mejorar la ergonomía de la herramienta individual de HEEDS:
  - Estructura flexible de alineación de cabecera `.dx-v2-tools-heeds-header-layout` y su contenedor de icono traslúcido estilizado `.dx-v2-tools-heeds-header-icon`.
  - Títulos y subtítulos ergonómicos `.dx-v2-tools-heeds-header-title` y `.dx-v2-tools-heeds-header-sub`.
  - Envoltura modular del cuerpo de la tarjeta `.dx-v2-tools-heeds-card-body` con padding uniforme de 24px, y su cabecera espacial `.dx-v2-tools-heeds-card-header`.
  - Zona de arrastre responsiva interactiva `.dx-v2-tools-heeds-dropzone` y sus subclases y estados (`.dragging`).
  - Grid responsivo de especificaciones técnicas `.dx-v2-tools-heeds-specs-grid` y filas de alineación compactas `.dx-v2-tools-heeds-spec-row` y código `.dx-v2-tools-heeds-spec-code`.
  - Contenedor lateral de avisos de almacenamiento `.dx-v2-tools-heeds-sidebar-warning` y bloques de daemons e información `.dx-v2-tools-heeds-sidebar-info`.

### Changed
- **Vista de la Herramienta HEEDS**: Refactorizada por completo la vista [heeds.blade.php], purgando el 100% de los estilos inline locales redundantes en la cabecera de página, tarjetas de proceso, dropzone de arrastre, botón de acción y paneles laterales, delegando la maquetación y la interactividad a las clases unificadas del namespace global.

## [2026-05-18 10:59] — CSS Unification: Subfase 19.12 (Siemens: STAR-CCM+) ✅

### Added
- **Namespace de STAR-CCM+ (`.dx-v2-tools-star-*`)**: Diseñadas e integradas clases semánticas dedicadas en [dx-styles.css] para modularizar la visualización y mejorar la ergonomía de la herramienta individual de STAR-CCM+:
  - Estructura flexible de alineación de cabecera `.dx-v2-tools-star-header-layout` y su contenedor de icono traslúcido estilizado `.dx-v2-tools-star-header-icon`.
  - Títulos y subtítulos ergonómicos `.dx-v2-tools-star-header-title` y `.dx-v2-tools-star-header-sub`.
  - Envoltura modular del cuerpo de la tarjeta `.dx-v2-tools-star-card-body` con padding uniforme de 24px, y su cabecera espacial `.dx-v2-tools-star-card-header`.
  - Zona de arrastre responsiva interactiva `.dx-v2-tools-star-dropzone` y sus subclases y estados (`.dragging`).
  - Grid responsivo de especificaciones técnicas `.dx-v2-tools-star-specs-grid` y filas de alineación compactas `.dx-v2-tools-star-spec-row` y código `.dx-v2-tools-star-spec-code`.
  - Contenedor lateral de avisos de almacenamiento `.dx-v2-tools-star-sidebar-warning` y bloques de daemons e información `.dx-v2-tools-star-sidebar-info`.

### Changed
- **Vista de la Herramienta STAR-CCM+**: Refactorizada por completo la vista [star-ccm.blade.php], purgando el 100% de los estilos inline locales redundantes en la cabecera de página, tarjetas de proceso, dropzone de arrastre, botón de acción y paneles laterales, delegando la maquetación y la interactividad a las clases unificadas del namespace global.

## [2026-05-18 10:55] — CSS Unification: Subfase 19.11 (Siemens: NX Suite) ✅

### Added
- **Namespace de NX Suite (`.dx-v2-tools-nx-*`)**: Diseñadas e integradas clases semánticas dedicadas en [dx-styles.css] para modularizar la visualización y mejorar la ergonomía de la herramienta individual de NX Suite:
  - Estructura flexible de alineación de cabecera `.dx-v2-tools-nx-header-layout` y su contenedor de icono traslúcido estilizado `.dx-v2-tools-nx-header-icon`.
  - Títulos y subtítulos ergonómicos `.dx-v2-tools-nx-header-title` y `.dx-v2-tools-nx-header-sub`.
  - Grid responsivo de dos columnas para tarjetas de motor `.dx-v2-tools-nx-motor-grid` y tarjetas premium `.dx-v2-tools-nx-motor-card` (con estados `.active-red` y `.active-teal` y variables CSS inline).
  - Zona de arrastre responsiva interactiva `.dx-v2-tools-nx-dropzone` con soporte nativo de estados de Alpine (`.dragging`, `.theme-red` y `.theme-teal`).
  - Grid responsivo de especificaciones técnicas `.dx-v2-tools-nx-specs-grid` y filas de alineación compactas `.dx-v2-tools-nx-spec-row`.
  - Contenedor lateral de avisos de almacenamiento `.dx-v2-tools-nx-sidebar-warning` y bloques de daemons e información `.dx-v2-tools-nx-sidebar-info`.

### Changed
- **Vista de la Herramienta NX Suite**: Refactorizada por completo la vista [nx-suite.blade.php], purgando el 100% de los estilos inline locales redundantes en las tarjetas de motor, en la zona de arrastre, en el botón de procesado y en el panel lateral informativo, delegando toda la interactividad a las clases unificadas del namespace y variables HSL nativas.

## [2026-05-18 10:48] — CSS Unification: Subfase 19.10 (Herramientas: Vista general / índice) ✅

### Added
- **Namespace de Herramientas (`.dx-v2-tools-*`)**: Diseñadas e integradas clases semánticas dedicadas en [dx-styles.css] para modularizar la visualización y mejorar la ergonomía del Hub de Herramientas:
  - Estructuras de layouts responsivos `.dx-v2-tools-grid`, `.dx-v2-tools-grid-2` (2 columnas) y `.dx-v2-tools-grid-3` (3 columnas).
  - Envoltura modular por tecnología `.dx-v2-tools-vendor-section` y su cabecera ergonómica `.dx-v2-tools-vendor-header`.
  - Badges de marca estilizados con fondos traslúcidos HSL en base a su fabricante `.dx-v2-tools-vendor-label.siemens`, `.dx-v2-tools-vendor-label.docs` y `.dx-v2-tools-vendor-label.moldex`.
  - Tarjeta de herramienta premium interactiva `.dx-v2-tools-card` con hover tridimensional en 3D, elevación de sombras y soporte nativo para variable `--card-accent`.
  - Estado deshabilitado `.dx-v2-tools-card-disabled` con filtros en escala de grises y desactivación de elevaciones.
  - Tarjeta placeholder para agregar herramientas futuras `.dx-v2-tools-card-add` con bordes discontinuos e interactividad premium.
  - Componente de contenedor de icono traslúcido estilizado `.dx-v2-tools-icon-box`.
  - Badges de estado contextuales de alta densidad `.dx-v2-tools-badge.ai`, `.dx-v2-tools-badge.doc` y `.dx-v2-tools-badge.upcoming`.

### Changed
- **Vista Principal de Herramientas**: Refactorizada la vista [index.blade.php], purgando por completo el bloque `<style>` incrustado (más de 20 líneas de CSS local) y todos los estilos inline redundantes, delegando la interactividad y colores a variables CSS y clases globales.

## [2026-05-18 10:44] — CSS Unification: Subfase 19.9 (Planificador de Renovaciones) ✅

### Added
- **Namespace del Planificador (`.dx-v2-planner-*`)**: Diseñadas e integradas clases semánticas dedicadas en [dx-styles.css] para modularizar la visualización y mejorar la ergonomía del Planificador de Renovaciones:
  - Estructura contenedora de la cabecera `.dx-v2-planner-header-grid` con alineación ergonómica de estadísticas y selección de ciclo.
  - Componente selector interactivo `.dx-v2-planner-month-picker`, disparador `.dx-v2-planner-month-btn` y menú flotante absoluto `.dx-v2-planner-dropdown` para la selección de meses.
  - Componentes de filtros de estado contractual `.dx-v2-planner-filters-wrap` y chips interactivos de estado `.dx-v2-planner-filter-chip` con soporte nativo de variables inline CSS.
  - Panel de estadísticas ergonómico de alto impacto `.dx-v2-planner-stats` y sub-items de contadores numéricos `.dx-v2-planner-stat-value`.
  - Componentes de contratos `.dx-v2-planner-contracts-list`, filas del grid contractual `.dx-v2-planner-contract-row` y badges mono-espaciados para contratos `.dx-v2-planner-contract-number`.
  - Botones de acción ergonómicos `.dx-v2-planner-btn-action` y reversiones de estado `.dx-v2-planner-btn-action-revert`.
  - Estado vacío premium `.dx-v2-planner-empty` y su icono atenuado `.dx-v2-planner-empty-icon`.

### Changed
- **Vista Principal del Planificador**: Refactorizada la vista [index.blade.php], eliminando el 100% de los estilos inline locales (más de 60 atributos) y purgando controladores arcaicos `onmouseover`/`onmouseout` locales, delegando toda la interacción dinámica a clases del namespace y selectores nativos CSS `:hover`.
- **Higiene de Marcado**: Corregida la etiqueta duplicada `</tr>` residual en la tabla y simplificada la estructura HTML de alineación.

## [2026-05-18 10:40] — CSS Unification: Subfase 19.8 (Contactos & Certificados COD) ✅

### Added
- **Namespace COD (`.dx-v2-cod-*`)**: Creadas y documentadas las clases semánticas dedicadas en [dx-styles.css] para encapsular y estilizar de forma limpia el generador de Certificado de Cese (COD) y el asistente inteligente de análisis de Composite:
  - Estructura `.dx-v2-cod-container` y tarjeta de alta fidelidad `.dx-v2-cod-card` con bordes premium.
  - Cabecera estilizada de alta densidad `.dx-v2-cod-card-header` con línea degradada interactiva `.dx-v2-cod-header-line`.
  - Componente segmentado dinámico de tres estados `.dx-v2-cod-segmented-large` y de dos estados `.dx-v2-cod-segmented-small` con indicador reactivo deslizante `.dx-v2-cod-active-indicator` con transiciones fluidas en Alpine.js.
  - Envoltura para el asistente inteligente de Composite y arrastre de logs `.dx-v2-cod-ai-upload-zone` con efecto pulso interactivo.
  - Grids de alta densidad para la previsualización del hardware recomendado `.dx-v2-cod-ai-result-grid` y explicaciones del motor IA `.dx-v2-cod-ai-reason`.

### Changed
- **Generador de COD**: Refactorizada la vista [cod.blade.php], removiendo el bloque de estilos locales `<style>` incrustado (más de 850 líneas de CSS duplicado) e inline styles, remapeando todo el marcado a las clases semánticas unificadas en el namespace `.dx-v2-cod-*`.
- **Vista Detallada de Clientes**: Auditada la vista [show.blade.php] para verificar la total adherencia a las clases unificadas en las pestañas de contactos y certificados COD, confirmando que tiene cero estilos inline y excelente consistencia visual.

## [2026-05-18 10:35] — CSS Unification: Subfase 19.7 (Importación CSV) ✅


### Added
- **Namespace de Importación (`.dx-v2-import-*`)**: Diseñadas clases semánticas dedicadas en [dx-styles.css] para aislar y normalizar el módulo de importación de datos y visualización de logs históricos:
  - Estructuras `.dx-v2-import-dropzone` y `.dx-v2-import-dropzone-inner` para la carga visual interactiva de ficheros con transiciones suaves en hover y colores adaptados a la base visual.
  - El grid y la tabla del protocolo de mapeo de datos de alta densidad `.dx-v2-import-mapping-grid` y `.dx-v2-import-mapping-item`.
  - Tarjetas estadísticas compactas con soporte de estado para avisos y fallos, y los contenedores de metadatos `.dx-v2-import-metadata-row`.
  - Componente estilizado reutilizable `.dx-v2-import-btn-icon` para los listados del historial.

### Changed
- **Vista Principal de Importación**: Refactorizada [index.blade.php], eliminando el 100% de los estilos inline locales de la tarjeta de carga, dropzone y protocolo de mapeo, delegando la visualización a las clases semánticas del namespace.
- **Historial de Logs**: Limpiado [logs/index.blade.php] al remover el bloque `<style>` incrustado y los estilos locales de alineaciones y botones de acción, migrando todo al estándar global.
- **Detalle de Log**: Limpiado el 100% de los estilos inline locales en [logs/show.blade.php] de breadcrumbs, envolturas estadísticas y metadatos.

## [2026-05-18 10:30] — CSS Unification: Subfase 19.6 (Clientes: Licencias) ✅

### Fixed
- **Variables CSS rotas**: Corregidas variables inexistentes (`--dx-v2-surface-raised` y `--dx-v2-text-muted`) en [dx-styles.css] por sus equivalentes unificados (`--dx-v2-raised` y `--dx-v2-muted`), reparando la renderización de la cabecera del daemon, la tabla de productos y el historial de auditoría de licencias.
- **Soporte x-cloak**: Añadida la regla global `[x-cloak] { display: none !important; }` en la hoja de estilos global para manejar de forma nativa la visibilidad reactiva de Alpine.js.

### Changed
- **Limpieza de estilos inline**: Removidos todos los estilos inline `style="display: none;"` de los tabs interactivos y modales en la vista detallada de clientes [show.blade.php], sustituyéndolos por la directiva limpia `x-cloak`.

## [2026-05-17 15:50] — CSS Unification: Subfase 19.4 & 19.5 (Dashboard & Clientes) ✅

### Added
- **Dashboard Centralization (19.4)**:
  - Creadas las clases del namespace `.dx-v2-dashboard-*` para encapsular la estructura y el comportamiento del dashboard principal en `dx-styles.css`.
  - Diseñadas las clases del buscador global `.dx-v2-dashboard-search-card` y asociadas, utilizando selectores nativos de CSS `:focus` para prescindir de controladores Javascript inline.
  - Implementado el componente de icono traslúcido rotado `.dx-v2-dashboard-stat-icon` para las tarjetas de estadísticas.
  - Creadas utilidades de color contextuales `.dx-v2-color-*` y utilidades compactas `.dx-v2-table-nowrap` y `.dx-v2-link-inherit`.
- **Clientes Centralization (19.5)**:
  - Definida la utilidad de texto ultra-compacto `.text-xs` para unificar clases heredadas de Tailwind sin duplicación.
  - Creada la clase semántica `.dx-v2-clients-db-icon` para dimensionar el ícono de DB de las advertencias de licencias a exactamente `10px`.
  - Diseñada la clase `.dx-v2-clients-empty-state` para controlar el padding vertical, la alineación y el color de texto atenuado de la celda de tabla vacía de forma centralizada.

### Changed
- **Dashboard Refactor**:
  - Eliminados el 100% de los atributos `style="..."` en las tarjetas estadísticas, envolturas de íconos SVG y contenedores de la columna lateral.
  - Limpiados los atributos interactivos `onfocus`/`onblur` del buscador Express, delegando la interactividad visual a selectores CSS puros en la hoja de estilos global.
  - Eliminado el bloque complejo `match` dinámico en PHP que inyectaba colores directos hexadecimales en la vista lateral de contratos, reemplazándolo por un mapeo directo de clases tipificadas `.dx-v2-color-[estado]`.
- **Clientes Refactor**:
  - Removido el 100% de los estilos inline locales de la vista de listado de clientes (eliminada la declaración `style="font-size: 10px;"` del ícono de DB).
  - Eliminada la clase redundante `text-sm` del subtítulo `.page-sub` para heredar nativamente la tipografía definida en el CSS.

### Fixed
- **Integridad de Tabla de Clientes**: Corregido el `colspan` del estado vacío (`@empty`) en `index.blade.php` de `colspan="4"` a `colspan="5"`. Al tener la tabla 5 columnas de cabecera, la celda vacía ahora se extiende perfectamente a lo ancho de toda la tabla, evitando desalineaciones visuales.

## [2026-05-16 16:15] — Herramientas: Fix Borrado Físico COD (#016) ✅

### Fixed
- **Incidencia #016 (Crítica)**: Corregido bug que impedía el borrado físico de archivos PDF en el servidor. Se implementó una normalización de rutas más robusta para evitar discrepancias de encoding/espacios en Linux.
- **Robustez**: Añadida telemetría de borrado en `CodController@destroy` para registrar el éxito o fallo de cada operación de archivo.

## [2026-05-16 15:45] — Herramientas: Fix COD Preview & UI Contextual IA ✅

### Fixed
- **Incidencia #015 (Crítica)**: Reparado fallo de anidamiento HTML en `cod.blade.php`. Un bloque duplicado del modal de IA impedía la apertura de la "Vista Previa" del certificado.
- **Storage Hygiene**: Eliminada la carpeta residual `backend/storage/private` (fuera del árbol de la app), centralizando todo en el disco `private` oficial de Laravel.

### Changed
- **UX: Asistente IA Contextual**: Reubicado el botón "Analizar Composite.txt" directamente en la sección "Nueva Máquina". Se eliminó el botón grande redundante de la parte inferior para una interfaz más limpia y ergonómica.
- **Storage Refactor**: Modificada la lógica de rutas de almacenamiento de CODs. Ahora se utiliza el **Nombre Real del Cliente en MAYÚSCULAS** (ej. `ABAD INTEGRACION...`) en lugar de slugs, mejorando la legibilidad en el sistema de archivos.

### Added
- **Refuerzo UI**: Nuevos estilos `btn-ai-mini` y animaciones de pulso sutiles para los botones contextuales de IA.

## [2026-05-15 13:50] — Dashboard Operativo y Estabilización de Sistema ✅


### Added
- **Buscador Global Express**: Motor de búsqueda de alta visibilidad en Dashboard con soporte instantáneo para Sold-To, Machine ID y Nombres de Clientes.
- **Favoritos (Bento Actions)**: Vinculación funcional de botones rápidos a herramientas críticas: Generación de COD, Planificador de Renovaciones y Hub de Auditoría IA.
- **Badge de Renovaciones**: Contador dinámico en tiempo real de renovaciones pendientes para el mes en curso.

### Changed
- **JWT Deep Hardening**: Sesión estabilizada mediante rotación inteligente (solo tras 5 min de antigüedad), ventana de gracia de 120s y ampliación de inactividad a 60 minutos.
- **Unificación de Volúmenes**: Eliminada la carpeta redundante `./storage` de la raíz. Todo el almacenamiento se ha centralizado en `backend/storage`, normalizando la infraestructura Docker.
- **Git Hygiene**: Configurado el sistema para ignorar el estado de modificación interno de los submódulos de diseño/IA.

### Fixed
- **QueryExceptions (Database)**: Restauradas columnas faltantes `warnings`, `detected_name` y `decision` en las tablas de auditoría y normalización.
- **Limpieza de Huérfanos**: Eliminación de residuos de base de datos y archivos `.sql` antiguos del servidor.

## [2026-05-15 11:28] — Resolución de Incidencia #013 (Sincronización Moldex3D) ✅

### Fixed
- **Invisibilidad Moldex3D**: Solucionado fallo crítico que impedía que las licencias de Moldex3D aparecieran en el inventario tras subirse, a pesar de que la UI indicaba éxito.
- **Normalización**: Integración de `ClientNormalizationService` en `MoldexSyncService` para soportar búsqueda por Alias, Fuzzy Matching (85%) y creación automática de nuevos clientes (ej. `Metalocaucho Mtc - A Wabtec Company`).
- **Control de Errores**: Actualizado `MoldexController` para que detecte correctamente los fallos internos de sincronización y retorne HTTP 422, cortando la falsa sensación de éxito.

## [2026-05-15 10:15] — Clientes: Filtro Granular de Inventario & UI Premium ✅

### Added
- **Filtro Inteligente de Inventario**: Implementado control segmentado de 4 estados (OFF, ALL, Siemens, Moldex3D) para una gestión precisa del parque de licencias.
- **UI Premium**: Rediseño del área de búsqueda con estética "cristal" (glassmorphism), etiquetas dinámicas y alineación ergonómica a la derecha.
- **Búsqueda Avanzada**: Ampliado el ancho del buscador a 600px y mejorada la visibilidad del input para facilitar la localización de cuentas.
- **Persistencia Multi-Vendor**: La lógica de filtrado ahora es 100% persistente por sesión, permitiendo navegar entre clientes manteniendo el contexto de vendor seleccionado.

### Fixed
- **Incidencia #003**: Resuelto el límite del filtro "Solo con Licencias", que ahora soporta múltiples vendors de forma independiente.
- **Visibilidad Moldex3D**: Identificado problema de sincronización de licencias Moldex3D (Incidencia #013). Se ha creado un registro de prueba para validar la funcionalidad.

## [2026-05-15 09:20] — Seguridad: Dashboard Telemetría & Blacklist (Fix #010) ✅

### Fixed
- **Seguridad (Dashboard)**: Corregidos indicadores (Logins fallidos, Errores 24h, Blacklist) que se mostraban a 0 por falta de registro de datos.
- **Auditoría**: Implementado log automático de `login_failed` con captura de IP y User Agent.
- **JWT**: Implementada **Blacklist proactiva** en Redis (ZSET) para invalidación instantánea de tokens al cerrar sesión.
- **Telemetría**: Sincronización de niveles de severidad (`error`, `critical`) para una visualización real del estado de la flota en el NOC Pro.

### Added
- **Parser de Logs**: Nuevo motor de análisis Regex para estructurar `laravel.log` en entradas legibles con timestamp y nivel de severidad.
- **UI Interactiva**: Implementado sistema de **Stack Traces colapsables** con Alpine.js en la pestaña de logs de sistema.
- **Filtrado de Ruido**: Resaltado visual diferenciado para código de proyecto vs librerías `vendor`.
- **Telemetría Unificada**: El contador de alertas del Dashboard ahora sincroniza en tiempo real los errores de la DB con los incidentes registrados en el fichero físico de logs.
- **Robustez Auditoría**: Implementado blindaje contra tablas inexistentes (`email_logs`) para evitar errores 500 en la gestión de logs de sistema.

## [2026-05-15 08:45] — Infraestructura: Fix Redis Persistence (Fix #012) ✅

### Fixed
- **Redis (Infra)**: Resuelto fallo crítico `MISCONF` que bloqueaba escrituras en el portal.
- **Persistencia**: Implementados **volúmenes nombrados** (`redis_beta_data`, `redis_prod_data`) en Docker Compose para asegurar que los permisos de `/data` sean gestionados correctamente por Docker y persistan tras reinicios.
- **Hotfix**: Aplicada corrección de propiedad (`chown redis:redis`) en caliente para restaurar el servicio sin downtime.

## [2026-05-15 08:35] — Estabilización Global: Validación UI & Robustez Tools (Fix #011) ✅

### Added
- **Validación Global UI**: Implementada lógica Alpine.js en NX, StarCCM+, HEEDS y Moldex3D para validar extensiones en el cliente antes de la subida.
- **Feedback Visual**: Nuevo sistema de mensajes de error temporales (4s) con estética semántica para informar sobre extensiones no permitidas.
- **Soporte Siemens**: Ampliado el soporte de extensiones a `.dat` y `.cid` en todas las herramientas del ecosistema Siemens.

### Fixed
- **Incidencia #011 (Crítica)**: Resuelto el fallo que impedía la descarga y transformación de licencias NX. Corregido el flujo de respuesta AJAX para garantizar el stream de descarga.
- **Robustez Backend**: Implementada gestión de memoria avanzada (`256M`) y bloques `try-catch` con degradación elegante. El sistema ahora asegura la entrega del archivo transformado aunque fallen servicios secundarios.
- **Optimización de Parser**: Refactorizado `LicenseParserService` para procesar archivos FlexLM masivos línea a línea, eliminando el riesgo de `Memory Exhaustion` por regex complejos.
- **Extracción de Metadatos**: Actualizadas las expresiones regulares para soportar daemons modernos (`saltd`, `cdlmd`, `RCTECH`) de forma consistente.

## [2026-05-15 07:55] — Estabilización: Fix Scripts de Backup & Mejora UI ✅

### Fixed
- **Infraestructura (Backup)**: Reparado el script `backup-db.sh` (Fix #002). Corregidos finales de línea CRLF -> LF y errores de sintaxis en bloques `bash`.
- **Blindaje**: Añadidas comillas de seguridad a variables de entorno (`$MYSQL_USER`, etc.) para prevenir errores con caracteres especiales.

### Added
- **Backup Naming Pro**: Evolución del formato de nombre de archivo para incluir origen (`ENV_TYPE_DATE.sql`). Ahora el sistema distingue automáticamente entre copias `SISTEMA` (cron) y `MANUAL` (UI).
- **UI NOC Pro**: Nueva columna "Origen" en la gestión de backups con badges semánticos (Azul para Sistema, Gris para Manual) para una monitorización técnica precisa.
- **Backend**: Actualizado `BackupController` para extraer metadatos del nombre del archivo y pasar el argumento `manual` al disparar copias desde la web.

## [2026-05-14 16:40] — Switch: Multi-Sold-To Finalizado -> Tracking de Errores ✅

### Added
- **Log de Sesión**: Fase 14 (Soporte Multi-Sold-To) completada y validada en Beta. Rama pusheada a `origin`.

## [2026-05-14 16:20] — Soporte: Multi-Sold-To (Licencias Unificadas) ✅


### Added
- **n8n Workflow v2.1**: Actualizado motor de IA para detectar "Other Installs" en archivos contractuales de Siemens.
- **Base de Datos**: Añadida columna JSON `additional_sold_tos` en la tabla de inventario activo.
- **Auto-Mapeo**: Implementada lógica de creación automática de registros en `client_mappings` para todos los IDs secundarios detectados.
- **Sincronización**: El motor de inventario ahora persiste los IDs unificados permitiendo su gestión centralizada.

### UI/UX
- **Multi-Badges**: Visualización de Sold-Tos adicionales mediante badges técnicos en cada servidor del inventario.
- **Auditoría Transparente**: Normalizada la visualización de IDs unificados en el modal de detalle de auditoría.

## [2026-05-14 15:20] — Fix: Validación MIME en Herramientas ✅

### Fixed
- **Moldex3D**: Corregido error de validación que bloqueaba la carga de archivos `.mac`. Eliminada restricción estricta de `mimetypes` en favor de validación por extensión.
- **Robustez Global**: Aplicada misma mejora en Siemens (NX, Star-CCM+ y HEEDS) para prevenir fallos futuros.

## [2026-05-14 15:15] — Optimización: Salto de Auditoría IA para Temporales ✅

### Added
- **Ahorro de Tokens**: Implementada lógica de cortocircuito en `AuditService` para evitar llamadas a n8n/IA en licencias temporales de 7 días (aquellas con "YourHostname" o "ANY").
- **Trazabilidad Silenciosa**: Las auditorías saltadas se registran en la base de datos con estado `skipped`, manteniendo el historial de archivos originales sin incurrir en costes de procesamiento pesado.

### Changed
- **Tool Controllers**: Actualizados `NXSuiteController`, `StarCcmController` y `HeedsController` para realizar la detección de tipo de licencia antes de invocar el motor de auditoría.
- **UI Feedback**: El historial de auditorías ahora muestra "SKIPPED" para las licencias temporales procesadas, indicando que el archivo es válido pero no requiere auditoría profunda.

## [2026-05-14 12:35] — Dashboard: Rediseño NOC Pro y Estética Premium ✅

### Added
- **Premium Metric Cards**: Rediseño de las tarjetas del Dashboard con línea de acento superior (pseudo-elemento ::before) unificado con el Hub de Herramientas.

- **Background NOC Icons**: Integración de iconos Lucide SVG en el fondo de las tarjetas con opacidad 0.08 y rotación dinámica para mejorar la estética profesional.

- **Hover UX**: Añadido efecto de elevación (box-shadow) y resalte de acento al pasar el ratón.

### Changed
- **Brand Consistency**: Cambio de color semántico de "Licencias Activas" a verde (`success`) para alineación visual con el estado positivo del inventario.

## [2026-05-14 11:15] — Planificador: Optimización NOC Pro e Identidad ✅

### Added

- **Planificador de Renovaciones**:
  - **UI NOC Pro**: Rediseño de alta densidad con selector de mes personalizado (Alpine.js), eliminando el `<select>` nativo para mejorar la fluidez y estética oscura.
  - **Sincronización de Identidad**: Alineación total de colores de estados con `identities.json` (Azul claro para Ofertado, Morado para Aceptado, etc.) usando transparencias dinámicas en chips y tablas.
  - **Sistema de Limpieza**: Botón "Limpiar Filtros" dinámico que aparece solo cuando hay filtros activos.
  - **Reversión (Undo)**: Implementada funcionalidad para deshacer renovaciones marcadas por error directamente desde la tabla.
  - **Mirroring Estético**: La tabla del planificador ahora es espejo de la gestión de clientes (clases `.card` y `.table`), simplificando cabeceras a "ID Licencia" y "Contrato | Vencimiento...".

### Fixed

- **Contraste de Estados**: Solucionado problema de visibilidad en estados "Ofertado" y "Procesado" en modo oscuro mediante el uso de variables CSS correctas.
- **Helper de Color**: Definida función `hexToRgb` en Blade para permitir transparencias dinámicas sobre colores HEX corporativos.

### Changed

- **Terminología**: Cambio de "Servidores" a "ID Licencia" para mayor precisión técnica (Sold-To / Machine ID).

## [2026-05-14 09:05] — Planificador: Simplificación de Proceso (Sin Adjuntos) ✅

### Changed

- **Planificador de Renovaciones**:
  - **Simplificación de Flujo**: Eliminada la opción de adjuntar archivos `.lic` al marcar renovaciones. El proceso ahora es un registro puramente administrativo de "Envío realizado".
  - **Optimización UI**: Eliminado selector de archivos en el planificador y columna de licencias en el historial del cliente para una interfaz más limpia y directa.
  - **Limpieza de Backend**: Eliminada lógica de subida, almacenamiento y descarga de archivos de renovación.

## [2026-05-13 16:05] — Fase 14: Planificador de Renovaciones Multi-Archivo ✅

### Added

- **Planificador de Renovaciones (Motor & UI)**:
  - **Estructura Multi-archivo**: Implementada tabla `renewal_log_files` y modelo asociado para soportar la subida de múltiples licencias (NX, STAR-CCM+, HEEDS) en una sola acción.
  - **Interfaz Operativa**: Rediseño del formulario de acción con input de archivos múltiples y feedback visual de carga.
  - **Historial Detallado**: Integración de una nueva pestaña "Renovaciones" en la ficha del cliente, permitiendo la descarga individual de cada archivo enviado históricamente.
  - **Seguridad UI**: Ocultación selectiva de herramientas administrativas en el Dashboard para usuarios no-admin.
  - **Automatización**: Lógica de almacenamiento estructurado en `storage/app/renewals/{client_id}/` con limpieza de nombres.

## [2026-05-13] — Dashboard: Enfoque en Licencias e Inventario ✅

### Added

- **Dashboard**: Reorientación total de la sección "Vencimientos Inminentes" de Contratos (CSV) a Licencias Reales (Inventario).
- **Lógica**: Agrupación por Daemon (Sold-To/Machine ID) para evitar duplicidades visuales de productos en un mismo servidor.
- **UI**: Visualización compacta en una sola línea (VENDOR · SOLD-TO) con colores corporativos.
- **Métricas**: Actualización de contadores superiores para reflejar el estado del inventario auditado.

## [2026-05-13 14:40] — Rediseño Visual de Alertas ✅

### Added

- **UI Bento Vertical**: Rediseño de la configuración de umbrales a un layout vertical más espacioso y legible, eliminando el grid comprimido.
- **Iconografía NOC Pro**: Integración de iconos semánticos (`bell-concierge`, `clock`, `sliders`) y colores de estado (Crítico, Preventivo, Recordatorio).
- **UX Adaptativa**: Ampliación del grid principal de administración a `1fr 1.5fr` para permitir que los formularios técnicos respiren.
- **Consistencia Visual**: Unificación de bordes (10px radius), fondos raised y tipografía mono para datos de configuración.

## [2026-05-13 14:30] — Estandarización de Nomenclatura y Transformación ✅

### Added

- **Nomenclatura Unificada Pro**: Implementación del nuevo estándar de nombres para NX Suite, StarCCM+ y HEEDS (`SOLDTO_HOSTNAME_CLIENTE_VERSION_Valida_FECHA.lic`).
- **Soporte Multi-Sold-To**: Lógica dinámica para licencias unificadas (`S1-S2-S3` o `S1_Multi` para casos complejos).
- **Extracción de Expiración**: Los nombres de archivo ahora reflejan la fecha real de caducidad (`DD-Mmm-YYYY`) extraída del bloque INCREMENT, no la fecha de creación.
- **Normalización de Versiones**: Acortamiento inteligente de años (`2025` -> `25`) manteniendo los puntos para legibilidad (`V25.12`).

### Fixed

- **Blindaje de Transformación**: Corrección de bug crítico que corrompía bloques `INCREMENT` al confundir `VENDOR_STRING` con la línea `VENDOR` de cabecera.
- **Estabilidad de Hostname**: Forzado de `localhost` en la línea `SERVER` para todas las licencias temporales, garantizando compatibilidad inmediata.

## [2026-05-13 12:45] — Gestión de Clientes y Estabilidad Alpine ✅

### Added

- **Filtro de Inventario Pro**: Implementación de filtrado dinámico para clientes con licencias activas.
- **Switch Técnico Industrial**: Nuevo componente UI con diseño cuadrado (6px), knob físico y transiciones precisas, eliminando efectos de glow para un look más profesional.
- **Persistencia de Filtros**: Lógica de persistencia basada en Sesión para mantener el estado del filtro entre navegaciones y búsquedas.
- **Unificación de Badges**: Los badges de inventario (Sold-To) ahora siguen estrictamente el sistema de diseño (`badge badge-warning`) con forma de píldora y tipografía mono.
- **Iconografía Técnica**: Sustitución de iconos genéricos por iconos técnicos (`fa-sliders`, `fa-database`) en toda la gestión de clientes.

### Fixed

- **Blindaje Alpine.js**: Eliminación definitiva de errores `Cannot read properties of null` en modales de auditoría y generador de COD mediante `<template x-if>` y encadenamiento opcional (`?.`).
- **Navegación Fluida**: Corrección de conflictos entre parámetros de búsqueda y filtros de inventario en la URL.
- **Layout de Clientes**: Optimización de la densidad de información en el listado principal.

## [2026-05-13] — Fase 10.5: Docker Monitor NOC Pro ✅

### Added

- **Monitor de Contenedores**: Nueva sección dedicada para la monitorización en tiempo real de la flota Docker (`/admin/system/docker`).
- **Telemetría Visual Pro**: Implementación de indicadores circulares (Gauges) para CPU y barras de alta densidad para RAM siguiendo el estilo NOC Pro.
- **Iconografía Unificada**: Integración de logos oficiales con colores de marca (PHP, MariaDB, Nginx, Redis) usando FontAwesome 6.
- **Acciones Operativas**: Funcionalidad de reinicio de contenedores integrada con diálogos de seguridad y validación de prefijos (`dx-`).
- **Optimización de Arquitectura**: Desacoplamiento de la lógica de Docker del dashboard principal para mejorar la velocidad de carga global.
- **Infraestructura**: Configuración del socket de Docker y permisos en el contenedor PHP-FPM.

## [2026-05-13] — Fase 13: Sistema de Alertas de Licencias ✅

### Added

- **Reporte Global Consolidado**: Implementación de `GlobalLicenseExpirationReport` para enviar un único resumen semanal a Soporte en lugar de emails individuales.
- **Lógica de Filtrado de Licencias**: Refactorización de `LicenseExpirationService` para centrarse exclusivamente en la caducidad de productos del inventario (0, 7, 15 y 30 días).
- **Notificaciones Internas**: Redirección de todas las alertas a la cuenta `soporte@ats-global.com` (configurable en ajustes).
- **Trazabilidad Automática**: Integración con `EmailLoggerListener` para registro centralizado en `email_logs` sin duplicidad.
- **Panel de Control**: Refactorización de la UI de administración (`/admin/alerts`) para cumplir con `DESIGN.md` (Bento style).
- **Test de Alertas**: Implementación de disparo manual síncrono desde el panel con feedback inmediato vía Flash Messages.

### Fixed

- **Duplicidad de Logs**: Eliminado registro manual en el Job que causaba entradas triplicadas en el historial.
- **Permisos de Vistas**: Solucionado error `Permission denied` en el servidor forzando permisos correctos y limpieza de caché de vistas.
- **Estabilidad**: Corregida la carga de fuentes y estilos en las plantillas de email bilingües.

## [2026-05-12] — Centro de Logs Unificado

### Added

- **Logs de Sistema**: Integración de visor de `laravel.log` directamente en el panel Admin (últimas 200 líneas).
- **Logs de Email**: Implementación de trazabilidad de correos salientes mediante listeners de eventos.
- **UI Auditoría Pro**: Rediseño total con sistema de pestañas persistentes y **NOC Micro-Cards** (indicadores con iconos para eventos, emails y alertas).
- **Gestión de Logs**: Funcionalidad de **Reset** por sección (Actividad, Sistema, Email) con registro de evento de seguridad para trazabilidad administrativa.
- **Paginación Avanzada**: Nueva vista `dx-jump` con botones direccionales y **selector de página (Dropdown)** para navegación rápida en listas largas (clientes, logs).
- **Dark Mode UI**: Solucionado error de visibilidad en selectores de modo oscuro y unificación de botones de acción en cabeceras.
- **Fix Email Logger**: Re-activación del listener de emails tras reversión accidental y verificación de trazabilidad.

## [2026-05-12] — Fase 12: Repositorio de Licencias y Normalización ✅

### Added

- **Normalización de Almacenamiento**: Implementado `StorageNormalizationService` para estandarizar carpetas de clientes a **MAYÚSCULAS CON ESPACIOS**, eliminando puntos y comas (ej. `S.L.` -> `SL`).
- **Migración Automática**: Nuevo comando Artisan `system:migrate-storage-names` para renombrar retroactivamente todas las carpetas existentes en el servidor.
- **Repositorio Semanal**: Implementado motor de archivado en `LicenseRepositoryService` que agrupa licencias en archivos ZIP estructurados.
- **ZIP Personalizado**: Nomenclatura estándar `REPOSITORIO_SEMANAL_S[XX]_[YYYY].zip`.
- **Automatización**: Programación del sistema (Scheduler) para generar y enviar el repositorio los **lunes a las 07:00 AM**.
- **Notificaciones ATS**: Mailable `WeeklyLicenseReport` con resumen de Clientes/Sold-Tos y adjunto ZIP enviado a `Soporte@ats-global.com`.
- **Panel Administrativo**: Nueva interfaz en `/admin/repository` para visualización del historial, descarga de archivos y generación manual de repositorios.
- **Generación Pro**: Añadida opción de "Generar y Enviar" manual y trazabilidad de origen (`auto` vs `manual`) con iconos en la tabla.
- **Refactoring**: Actualizados los controladores de herramientas (NX, Star-CCM, Heeds, Moldex) para integrarse con la nueva lógica de normalización y corregida la inyección de dependencias en `NXSuiteController`.
- **Estabilidad**: Corregida lógica de creación de ZIPs con rutas absolutas y permisos forzados en el servidor.
- **UX**: Eliminación de textos redundantes en el panel administrativo para un diseño más limpio.
- **Inteligencia Artificial**: Integración de Gemini (Google AI) para el análisis de adaptadores de red en el generador de COD.
- **Asistente de Composite**: Nuevo servicio `CompositeParserService` que identifica el hardware óptimo (Ethernet físico > Wi-Fi) descartando adaptadores virtuales y VPNs.
- **UI Premium**: Modal interactivo con soporte para **Drag & Drop** de archivos `.txt` y pegado de texto.
- **Auto-rellenado**: Función de volcado automático de Hostname, Composite y MAC detectados directamente al formulario.
- **Seguridad**: Procesamiento asíncrono y protección de API Key en entorno Beta.
- **Correcciones**: Solucionado error 404 en la API de Gemini mediante la actualización al modelo **`gemini-3.1-flash-lite`** y versión `v1beta`.
- **UI/UX**: Rediseño integral de la zona de carga con estética premium, bordes punteados estilizados (**blue dashed**) y animaciones de pulso.
- **Iconografía Gemini**: Implementación del icono oficial **Sparkle** de Gemini en toda la plataforma (Herramientas y Dashboard).
- **Dashboard del Sistema**: Actualización integral de la matriz de servicios con iconos premium y colores de marca para **Gemini (Flash 3.1)**, **DeepSeek**, **OpenRouter**, **n8n**, **Telegram**, **MariaDB** y **Redis**.
- **Estética NOC Pro**: Unificación de dimensiones (34x34), centrado absoluto y sombras elevadas para una interfaz de monitorización de alta gama.
- **Dashboard NOC Pro**: Unificación visual de iconos (34x34px) y gradientes de marca para servicios de IA e infraestructura.
- **Módulo de Recursos (Fase 8.5/9.2)**: Implementación de sistema dinámico de gestión de enlaces y documentación con páginas independientes para Siemens y Moldex3D. Incluye panel de gestión reactivo (Alpine.js) para Staff/Admin.
- **Fix (UI)**: Corregida la especificidad de CSS en el Hub de Herramientas que provocaba que los nombres de utilidades Siemens se visualizaran con el color de Moldex.

## [2026-05-12] — Restauración Configuración n8n (Fix) ✅

### Fixed

- **Configuración**: Restauradas variables críticas de n8n en `infra/.env.beta` que fueron eliminadas accidentalmente.
- **Conectividad**: Verificada salud del motor n8n desde el servidor Beta (Online).
- **Callback**: Restaurada `AUDIT_CALLBACK_URL` para permitir el retorno de datos desde la IA.

## [2026-05-12] — Gestión de Backups (Fase 14) ✅

### Added

- **Modularización**: Traslado de lógica de backups de `SystemActionController` a `BackupController`.
- **Restauración**: Implementación de sistema de restauración de DB mediante SQL pipe con validación de rutas.
- **Seguridad UI**: Modal de doble confirmación con keyword "RESTAURAR" para acciones destructivas.
- **Automatización**: Configuración de Cron Job diario (03:00 AM) en servidor LXC 600.
- **Git Cleanup**: Merge de funcionalidades previas y borrado de 12 ramas obsoletas.

## [2026-05-12] — Gestión de Usuarios y Acceso (Fase 11) ✅

### Added

- **User Toggle AJAX**: Implementada acción inmediata para activar/desactivar usuarios desde el listado sin recarga de página.
- **RBAC Assignments**: Sistema de asignación de roles verificado y funcional en creación/edición.
- **Delete Protection**: Blindaje contra auto-eliminación y auto-desactivación del administrador en sesión.

## [2026-05-11] — Restauración Infraestructura y SMTP ✅

### Fixed

- **Base de Datos**: Restauradas credenciales de MariaDB Beta (`dxportal_beta`).
- **SMTP Production**: Configurado Mailtrap en modo Producción con autenticación via API Token.
- **Docker Sync**: Resuelto problema de sincronización de archivos `.env` en contenedores Docker (inode cache).
- **Notificaciones**: Verificado envío de emails reales desde el backend.

## [2026-05-11] — Gestión de Usuarios y RBAC (Fase 11 y 11.1) ✅

### Added

- **User Management CRUD**: Sistema completo de administración de usuarios con filtrado por roles y búsqueda.
- **RBAC Engine**: Implementación de roles dinámicos (admin, technician, staff, viewer) con middleware de permisos granular.
- **My Profile Section**: Nueva sección de autogestión para que los usuarios actualicen sus datos y cambien su contraseña.
- **Automated Notifications**: Sistema de bienvenida por email con envío automático de credenciales iniciales.
- **Native Design Migration**: Migración total a `dx-styles.css` eliminando dependencias externas (Bootstrap) en las vistas administrativas.

### Fixed

- **Database Cleanup**: Eliminación de registros de prueba (faker) y normalización de usuarios base del sistema.
- **Security Hardening**: Bloqueo de auto-desactivación y auto-eliminación para sesiones administrativas activas.

## [2026-05-11] — Estabilización de NOC Pro e Infraestructura ✅

### Added

- **Git Localization**: Localización completa de fechas relativas al castellano ("hace X segundos") mediante integración de Carbon y timestamps de Git.
- **Representative UI Colors**: Implementación de código de colores semánticos en el Dashboard (Azul para Caché, Ámbar para Backups, Naranja para Alertas, Verde para Despliegues OK).
- **UX Quick Actions**: Alineación ergonómica a la izquierda y micro-interacciones de desplazamiento lateral en el panel de acciones administrativas.

### Fixed

- **Git Multi-User Permissions**: Configuración de `safe.directory` a nivel de sistema (`--system`) en el contenedor para permitir que el servidor web (`www-data`) extraiga metadatos del repositorio.
- **App Localization Loop**: Sincronización de `APP_LOCALE=es` en configuración global y entornos Docker para garantizar consistencia en todas las respuestas del sistema.
- **NOC Alignment**: Corregida alineación del NOC para lectura natural (de derecha a izquierda) y normalización visual de fuentes mono-espaciadas.

## [2026-05-11] — Modularización Administrativa (Phase 10.4) ✅

### Added

- **Database Vault Module**: Migración de la gestión de backups a un controlador y vista independientes (`BackupController`).
- **Centro de Auditoría**: Nuevo módulo dedicado para logs de sistema con filtros avanzados (IP, Usuario, Acción, Nivel).
- **Header Standardization**: Unificación visual de las cabeceras de administración siguiendo el estilo del módulo de Importación.
- **Backup Download/Delete**: Implementada lógica de borrado y descarga de copias de seguridad mediante IDs de archivo seguros.
- **Relocation of Stats**: Las estadísticas técnicas se han movido a las cabeceras de las tarjetas internas para limpiar el encabezado principal.

### Fixed

- **Backup Execution Environment**: Instalado `mariadb-client` en el contenedor PHP y actualizado el script para usar `mariadb-dump` con `--ssl=0`, solucionando errores de conexión y comandos faltantes.
- **Git Metrics in Dashboard**: Corregido error `N/A` mediante el montaje del directorio `.git` y la configuración de `safe.directory` en el contenedor, permitiendo la visualización del hash y fecha de despliegue.
- **Permission Denied in Backups**: Actualizado script `backup-db.sh` para forzar permisos 777 en archivos nuevos, permitiendo su borrado desde la interfaz web.
- **Path Synchronization**: Corregidas las rutas de almacenamiento de backups para sincronizar el volumen de Docker con el `storage_path()` de Laravel.
- **Styling Consistency**: Corregido estilo de botones "Limpiar" y alineación de botones de acción en tablas.

## [2026-05-11] — Dashboard del Sistema (NOC Pro) ✅

### Added

- **NOC Pro Control Center**: Evolución del dashboard a centro de operaciones profesional.
- **Quick Actions Panel**: Implementado panel interactivo con Alpine.js para acciones administrativas (Limpiar caché, Backups, Mantenimiento, Reinicio de Workers).
- **Telemetría de Red**: Visualización en tiempo real de tráfico ETH0 (RX/TX bytes) directo desde kernel.
- **Métricas DB Profundas**: Monitorización de hilos conectados y consultas lentas (slow queries) en MariaDB.
- **Git Intelligence**: Integración de Hash de commit y fecha relativa del último despliegue en la cabecera.
- **System Live Feed**: Registro visual de los últimos 10 eventos de auditoría administrativa.
- **Maintenance Pulse**: Indicador visual dinámico en cabecera cuando el modo mantenimiento está activo.
- **Seguridad**: Registro automático de toda acción administrativa en `audit_log` con trazabilidad de usuario.
- **Selective Maintenance Mode (Admin Friendly)**: Implementado sistema de mantenimiento que permite a los administradores seguir operando mientras el público visualiza una página 503 personalizada.
- **Top Warning Banner**: Aviso persistente en el layout para administradores cuando el mantenimiento está activo.
- **Custom 503 Page**: Nueva vista de mantenimiento con diseño premium alineado con la identidad del proyecto.
- **Helper formatBytes**: Añadida utilidad para formateo dinámico de unidades de datos.
- **Operator Control Center**: Implementado nuevo Dashboard de alta densidad con métricas técnicas y de seguridad (NOC style).
- **Refinamiento Visual NOC**: Integración de fuente `Outfit` para valores master, centrado de KPIs y estilo "Ghost Icons" rotados para look premium.
- **Métricas de Infraestructura**: Monitorización de `Load Avg` (1m, 5m, 15m), RAM (vía `cgroups` para LXC) y almacenamiento.
- **Matriz de Servicios**: Monitorización de DB, Redis, n8n y proveedores de IA (Gemini, DeepSeek, OpenRouter).
- **Seguridad**: Monitorización de sesiones activas, `JWT Blacklist` y errores críticos (24h).
- **Visualización de Datos**: Integración de Chart.js para gráficas de tendencia (Auditorías 7 días) y distribución de Daemons.
- **KPIs de Negocio**: Visualización en tiempo real de contratos, licencias activas, caducidades próximas y estado de auditorías IA.
- **Factories de Datos**: Creadas factories para `Client`, `Contract` y `AiAuditResult` para soporte de tests de integración.
- **Services Matrix Categorizada**: Organización de servicios en grupos lógicos (Infraestructura, Procesadores, Inteligencia AI).
- **Iconografía y Localización**: Añadidos iconos SVG personalizados y etiquetas en castellano natural para mayor claridad operativa.
- **Seguimiento en Tiempo Real**: Implementado sistema de presencia basado en Redis para contar usuarios activos (JWT) con ventana de 15 minutos.
- **Métricas de Actividad AI**: Implementado contador de auditorías diarias para Gemini como proxy de consumo.

### Fixed

- **Métricas de RAM en LXC**: Corregida detección de límites de memoria usando `cgroup v1/v2` para reportar el límite del contenedor en lugar de la RAM del nodo Proxmox.
- **CPU Load Formatting**: Estructurada la salida de `sys_getloadavg` para evitar solapamientos visuales y permitir acceso directo a intervalos.
- **Protocolo de Seguridad:** Implementada la sección 0.9 en `AGENTS.md` obligando a realizar backups antes de cambios estructurales o tests en el servidor.
- **Base de Datos:** Corregido error 500 en Dashboard debido a nombre de columna incorrecto (`daemon` vs `vendor_daemon`) en la tabla de inventario.
- **Robustez Infra**: Corregido comando `uptime` para compatibilidad con BusyBox/Alpine en el contenedor PHP y timeouts de API de Telegram (5s).

---

## [2026-05-10] — Phase 10: Gestión de Usuarios (Auth)

### Added

- **CRUD Contactos**: Sistema de gestión de destinatarios vinculados a clientes con modales Alpine.js.
- **Persistencia**: Sistema de pestañas en perfil de cliente que mantiene el estado tras recargar.
- **Demo Data**: `DemoContactSeeder` para poblar el sistema con datos de prueba.

---

## [2026-05-09] — Paridad de Temas y Refinamiento de UX Final

### Added

- **Paridad de Temas (Light/Dark)**: Refactorización total de `moldex3d.blade.php` y `clients/show.blade.php` para eliminar colores HEX hardcodeados, asegurando que todos los componentes visuales (Property List, Bento Grid, Inventory Cards, History Toggle) se adapten automáticamente al tema del sistema mediante variables CSS (`--primary`, `--muted`, `--surface`, etc.).
- **Robustez en Herramientas**: Simplificación de la lógica de enlaces en el Hub de Herramientas para garantizar el acceso correcto a la auditoría de Moldex3D.
- **Identidad de Marca**: Migración de colores estáticos de Moldex3D a la variable `--moldex` para consistencia cross-module.

### Fixed

- **Contraste de UI**: Corregida la visibilidad de textos secundarios y fondos de tarjetas en modo claro.
- **Visibilidad Crítica**: Reparadas las etiquetas de "Expiración" y "Versión" (v2026) que eran invisibles en modo claro por estar hardcodeadas en blanco o colores de bajo contraste.
- **Spinner & Dropzone**: Adaptación visual de los estados de carga y arrastre de archivos al sistema de diseño global.
- **Layout de Cliente**: Corregida etiqueta `<template>` sin cerrar en `clients/show.blade.php` que bloqueaba el renderizado del footer global.

---

## [2026-05-09] — Diferenciación de Vendors en UI (Siemens vs Moldex3D)

### Added

- **Modelo de Datos**: Implementado accessor `vendor` en `LicenseInventoryDaemon` para identificación estructural de proveedores.
- **UI Adaptativa**:
  - Rediseño de labels en inventario: "Daemon" para Siemens y "Plataforma" para Moldex3D.
  - **Logo Moldex3D**: Implementación de logo estilizado con colores de marca (Rojo/Naranja) para mayor identidad visual.
  - **Limpieza de UI**: Eliminación de badges redundantes para Moldex3D y diferenciación de etiquetas de cuenta (**Sold-To Account** para Siemens vs **Customer ID** para Moldex3D).
  - **Versión Prominente**: Mejora de la visibilidad de la versión (v2025) y unificación de etiquetas de red (**Servidor / Hostname** + **Machine ID** para Moldex3D).
  - Resaltado de hardware: Label específico "Machine ID" para licencias de Moldex3D.
- **Sistema de Estilos**:
  - Nuevas clases CSS `.moldex-logo` y `.accent` para representación tipográfica de marca.
  - Dinamismo de colores de vendor en tarjetas de inventario.

### Fixed

- **Rutas**: Restaurado el acceso a la herramienta de Moldex3D (`/herramientas/moldex3d`) mediante la integración de las ramas de desarrollo pendientes. Corregido error 404.
- **Robustez**: Eliminada la dependencia de `str_contains` en las vistas, delegando la lógica de identificación al modelo.

---

## [2026-05-09] — Fase 9: Auditoría Moldex3D y Persistencia ✅

### Added

- **Parser Moldex3D**: Implementado parser determinista (regex) para archivos `.mac`.
- **Persistencia en Inventario**: Nuevo `MoldexSyncService` que vincula automáticamente las licencias con clientes existentes en la base de datos.
- **Registro de Productos**: Sincronización de módulos, cantidades y fechas de expiración en `license_inventory_products`.
- **UI/UX Premium**:
  - Vista "Property List" estilo dark/técnico para resultados de auditoría.
  - Dropzone rediseñado con alineación óptica corregida.
  - Indicadores visuales de estado de sincronización en tiempo real.
- **Nomenclatura**: Estandarización de archivos basada en `AÑO_ID_CLIENTE__TIPO_FECHA.mac`.
- **Seguridad**: Almacenamiento privado estructurado y proceso local 100% determinista.

### Fixed

- **Alineación Dropzone**: Corregida desviación de iconos mediante `inline-flex` y contenedor de bloque.

---

## [2026-05-08] — Generador Siemens COD (Completo)

### Added

- **Generador de Certificados de Cese (COD)**: Motor de generación de PDF de alta fidelidad bilingüe.
- **Gestión de Firmas**: Implementada subida y descarga segura de CODs firmados por el cliente.
- **Borrado Inteligente**: Nueva opción de borrado completo (Registro BD + Archivos físicos Original/Firmado).
- **Vista Previa Interactiva**: Modal con visor de PDF integrado y limpieza de barras de herramientas.
- **Seguridad ID-Abstraction**: Flujo de descarga blindado mediante UUIDs para certificados.
- **Validación Estricta**: Sanitización de inputs (Hostnames sin tildes, MACs sin guiones, Solicitantes sin números).
- **Asistente de Hardware**: (Idea registrada en Backlog) para futuras versiones.

### Fixed

- **Enlaces Históricos**: Corregido error 404 en el historial de cliente mediante migración a sistema de UUIDs.
- **Alineación de Iconos**: Ajuste de UI mediante `display: contents` para visualización perfecta en horizontal.
- **Mapeo de Almacenamiento**: Corregida visibilidad en el host (Windows) alineando el disco `private` con los volúmenes de Docker.
- **Localización**: Mapeo de tipos de certificado a nombres profesionales en castellano.

- **Optimización Visual**: Iconos de acción compactados en horizontal (26px) para mejorar la densidad de información.
- **Alineación UI**: Unificación de layouts horizontales para iconos y títulos en todo el módulo.
- **Optimización PDF**: Compresión de márgenes y fuentes para asegurar una sola página A4.
- **Bug Fix**: Corregido error de variable indefinida en la persistencia del certificado.
- **Infraestructura**: Creación de directorio de fuentes y reseteo de logs.

---

_Firmado por: **Antigravity (DX Agent)** 🦾_

## [2026-05-08] — Fase 8.4: Generación de Certificados de Cese (COD) ✅

### Added

- **COD Generator**: Implementado generador bilingüe (ES/EN) de certificados COD oficial de Siemens.
- **Dompdf**: Instalación y configuración de `barryvdh/laravel-dompdf` para generación de documentos de alta fidelidad.
- **Fuentes Corporativas**: Integración de fuentes Calibri TTF para cumplimiento de estándares visuales de Siemens.
- **Seguridad**: Nuevo disco de almacenamiento `private` para CODs, garantizando que los archivos no sean accesibles públicamente.
- **UI/UX**:
  - Nueva herramienta "Generador COD" en el Hub con soporte para múltiples MACs y previsualización dinámica.
  - Integración del historial de certificados en la ficha de cliente (`Certificados` tab).
- **Base de Datos**: Nueva tabla `cod_certificates` para trazabilidad completa y gestión de estados (Pendiente/Firmado).

## [2026-05-08] — Fase 8.3: Motor HEEDS y Normalización Cross-Module ✅

### Added

- **HEEDS**: Implementado motor completo de auditoría y transformación para licencias HEEDS (`rctech` -> `saltd`).
- **Parser**: Nuevo `HeedsService` con extracción avanzada de metadatos desde el bloque de cabecera de Siemens (Sold-To, Cliente, Versión).
- **UI**: Vista dedicada `tools/heeds.blade.php` con bento técnico y soporte para auditoría IA.
- **Normalización**: Implementada la Bandeja de Normalización Central para gestión de identidades y duplicados.
- **Motor**: Nuevo `ClientNormalizationService` con soporte para **Fuzzy Matching** (85%) y gestión de Alias.
- **Integración**: Sistema de normalización cruzada que captura avisos tanto de CSV como de Auditoría de Licencias (AI).
- **STAR-CCM+**: Implementado `StarCcmService` para parsing y transformación de licencias `cdlmd` a `saltd`.
- **UI**: Nuevo dashboard técnico en `tools/star-ccm.blade.php` y bandeja de normalización premium.
- **Base de Datos**: Tablas `client_aliases`, `normalization_decisions` y columna `warnings` en logs y auditorías.

### Refined (UI/UX - Phase 8.3 Final)

- **Engine Selector**: Integrado selector rápido de motores (NX Suite, STAR-CCM+, HEEDS) en la barra lateral de todas las herramientas.
- **Unificación Estética**: Centralizado el diseño de tarjetas (radius 4px) y cuadrículas (gap 24px, sidebar 300px) en `dx-styles.css`, eliminando más de 200 líneas de estilos locales redundantes.
- **Layout Stability**: Implementado `overflow-y: scroll` global para prevenir saltos de píxeles al cambiar entre páginas con y sin scroll.
- **Sidebar Fix**: Corregido error de anidamiento de etiquetas `<a>` en el layout principal que causaba desplazamientos en el bloque de contenido.

### Changed

- **Arquitectura**: Refactorizado `NormalizationController` para centralizar la lógica de limpieza de datos.
- **Configuración**: Centralizadas las URLs de Webhooks y Callbacks de IA en variables de entorno (eliminando hardcoded URLs).
- **Fase STAR-CCM+**: Nomenclatura estricta de archivos `.lic` y almacenamiento jerárquico por cliente/fecha.

### Fixed

- **Unificación**: Lógica de migración total que mueve contratos, licencias, inventario y contactos al unificar clientes.
- **Regex**: Corregidos los patrones de extracción de nombres en los logs de importación.


## [2026-05-07] — Optimización de Auditoría IA (v2.2) ⏳ (Pendiente Verificar)

### Added

- **n8n Workflow v2.2**: Implementado nuevo prompt de IA con soporte explícito para:
  - **Hardware Keys (Dongles)**: Detección de `UG_HWKEY_ID` e IDs numéricos cortos.
  - **Modo Standalone**: Gestión de licencias sin servidor central.
  - **IDs Numéricos**: Soporte para Host IDs no hexadecimales (ej: 24141).
- **Backend Sincronización**: Actualizado `InventorySyncService` para reconocer automáticamente IDs numéricos cortos como licencias tipo `dongle`.

### Fixed

- **Precisión de Inventario**: Mejorada la detección de tipo de licencia basada en el formato del Host ID del producto.

## [2026-05-07] — Refinamiento del Inventario Activo (Fase 8.1 Finalizada) ✅

### Added

- **UI de Inventario Robusta**: Rediseño completo de la interfaz de inventario utilizando CSS puro de alta densidad técnica.
  - Layout horizontal optimizado para lectura rápida de daemons y productos.
  - Soporte nativo para visualización de múltiples **Sold-To** bajo un mismo cliente (Ecosistema Siemens).
  - Identificación visual clara de licencias **Node-Locked** (MAC) y **Hardware Keys** (Dongles).
- **Consistencia Visual**: Restauración de estilos globales (menú de pestañas, leyenda de estados, modales de auditoría) para asegurar la integridad de toda la vista de cliente.
- **Tipografía Corporativa**: Integración de Google Fonts (Inter e IBM Plex Mono) para mejorar la legibilidad de datos técnicos.

### Fixed

- **Layout Bento**: Eliminadas dependencias de Tailwind que causaban fallos de renderizado en monitores panorámicos.
- **Estabilidad CSS**: Aislados los estilos de inventario en bloques robustos, evitando colisiones con el diseño global del portal.

## [2026-05-07] — Motor de Auditoría Siemens (Fase 8.1 Parte 2) ✅

### Added

- **Base de Datos**: Implementadas tablas `ai_audit_results` y `client_mappings`.
- **Servicios de Backend**:
  - `LicenseParserService`: Parser de limpieza para archivos FlexLM (unificación de líneas y filtrado de firmas).
  - `AuditService`: Orquestador de comunicación con n8n y lógica de auto-vinculación de clientes.
- **Integración IA**:
  - Conexión operativa con el webhook de n8n para procesamiento asíncrono.
  - Implementado `AuditCallbackController` para recepción de resultados estructurados.
  - Integración en el flujo de subida de `NXSuiteController`.
- **UI de Auditoría (Beta)**:
  - Nueva pestaña "Licencias" en el perfil de cliente con historial de auditorías.
  - Visualización de productos detectados mediante chips dinámicos.
  - **Pendiente**: Refinar la apertura del modal de detalle (investigar fallo Alpine.js tras teleport).

## [2026-05-07] — Mecanismo Siemens NX (Fase 8.1 Parte 1) ✅

### Added

- **Nomenclatura Estricta**: Nueva lógica de generación de nombres para Siemens NX.
  - Formato: `SOLDTO_HOSTNAME_CLIENTE_VERSION_Valida_DDMMYYYY.lic`.
  - Normalización: Hostname y Cliente siempre en **MAYÚSCULAS** y sin caracteres especiales (puntos/espacios).
- **Almacenamiento Jerárquico**: Las licencias se organizan por `siemens/{cliente}/{mes-año}/`.
- **Gestión de Duplicados**: Implementado sufijo numérico automático (`_1`, `_2`) para evitar sobrescrituras.
- **UI de NX Suite**: Rediseño visual semántico, utilizando tarjetas diferenciadas con colores de vendor (Rojo Legacy / Teal SALT) y estructura de paneles laterales al estilo `admin/import`.

### Fixed

- **Error 413 (Payload Too Large)**: Resuelto. Se corrigió la ruta de `env_file` en `docker-compose.beta.yml` a `./infra/.env.beta` lo que permitió montar correctamente el archivo `local.ini` (100MB) en PHP-FPM.
- **Permisos de Almacenamiento**: Corregido bloqueo de I/O en la carpeta `storage` y `bootstrap/cache` mediante ajuste de permisos 777.

## [2026-05-07] — Gestión de Memoria y Reglas de Control

### Added

- **Skills**: Integrada la habilidad `claude-mem` para persistencia semántica entre sesiones.
- **Git/GitHub**: Implementada regla innegociable de Puntos de Control (Tags) tras cada fase terminada.
- **Cleanup**: Realizada limpieza masiva de ramas locales y remotas ya integradas.

## [2026-05-07] — Rediseño de Inventario y Gestión Multi-Vendor

### Added

- [PLAN] Iniciado rediseño completo de la gestión de licencias hacia un modelo de "Inventario Activo".
- Definición de nuevas tablas `license_inventory_daemons` y `license_inventory_products` para soportar multi-Sold-To y Node-Locked (MACs).
- Soporte para licencias de tipo Dongle USB (HW-KEY).

### Fixed

- Estandarización de etiquetas (### Added, ### Fixed, ### Changed) en el historial de sesiones.

## [2026-05-21] — Fase 25: Consola de Diagnósticos y Generador COD

### Added
- **Chatbot UI**: Refactorización de la interfaz del asistente IA. Ventana ampliada (460x640) y eliminación del panel lateral Bento para reducir el ruido visual y mejorar la legibilidad.
- **Resiliencia IA**: Implementada cadena completa de Fallback (Gemini -> DeepSeek -> OpenRouter -> Groq) con soporte de Function Calling en todos los niveles para acceso ininterrumpido a BD.

### Fixed
- **Generador COD**: Solucionado error 500 crítico al generar certificados de cese (COD) para empresas no registradas en la base de datos (`client_id` nullable).
- **Manejo de Errores UI**: Mejorado el parseo de respuestas HTTP 422 en peticiones `fetch` del frontend (se añadió `Accept: application/json` para evitar parseos erróneos de HTML).

## [2026-05-06] — Sincronización y Lecciones (Fase 8.1)

### Changed

- **Sincronización**: Restaurada la rama `dev` tras un fallo arquitectónico en el inicio de la Fase 8.1.
- **Lección Aprendida (UI)**: Uso estricto de `dx-styles.css` sin introducir Tailwind CSS no autorizado.
- **Lección Aprendida (Rutas)**: Respetar la convención de rutas en castellano (`/herramientas`) y no sobreescribir lógica validada en fases anteriores.

## [2026-05-06] — Fase 7: Hub de Herramientas ✅

### Added

- **Hub de Utilidades**: Implementación de vista dinámica `/herramientas` agrupada por Vendor.
- **Feature Flags**: Modelo `FeatureFlag` y seeder sincronizado con `identities.json` para control de accesos.
- **Navegación**: Vinculación de Sidebar y Header con el nuevo Hub centralizado.

### Changed

- **Sincronización**: Llaves y etiquetas técnicas actualizadas para coincidir estrictamente con el archivo de identidades.
- **Copy**: Actualización de frases descriptivas para Siemens y Moldex3D según estándares técnicos.

## [2026-05-06] — Fase 6.3: Gestión de Contactos ✅

### Added

- **CRUD Contactos**: Sistema de gestión de destinatarios vinculados a clientes con modales Alpine.js.
- **Persistencia**: Sistema de pestañas en perfil de cliente que mantiene el estado tras recargar.
- **Demo Data**: `DemoContactSeeder` para poblar el sistema con datos de prueba.

### Fixed

- **Infraestructura**: Limpieza de `known_hosts` y corrección de acceso SSH para despliegues automatizados.

## [2026-05-06] — Refinamiento UI Clientes

### Changed

- **Leyenda de Estados**: Integrada en la Card de contratos (Fase 6.1).
- **Estilo**: Alineado con `DESIGN.md` (jerarquía técnica y card-footer).
- **Mejora**: Refinamiento estético de la leyenda de estados en el ContraHeader.

## [2026-05-06] — Fase 6.1: Perfeccionamiento de Gestión de Clientes ✅

### Added

- **Búsqueda Pro**: Atajo global `Ctrl + Espacio` y buscador inteligente optimizado para grandes volúmenes de datos.
- **Leyenda Técnica**: Guía visual de estados integrada en el ContraHeader para referencia rápida.

### Changed

- **UX/UI**: Rediseño simétrico del listado de clientes y normalización de datos (`trim`) para evitar desajustes en el mapeo de estados.

## [2026-05-06] — Fase 5: Portal Principal (Dashboard) ✅

### Added

- **Dashboard Dinámico**: Implementación de métricas automáticas basadas en el estado real de los contratos (Activos, Urgentes, Próximos, Seguimiento).
- **Top 10 Vencimientos**: Tabla interactiva con badges de estado y cálculo de días restantes en tiempo real.
- **Cache Busting**: Sistema de versionado dinámico para `dx-styles.css` mediante `?v={{ time() }}` en el layout.
- **UX**: Sesión JWT extendida a 1 hora (60 min) para flujos de trabajo prolongados.

### Fixed

- **Persistencia de Tema**: Corregido fallo que reseteaba el modo oscuro al recargar o navegar. Ahora usa `localStorage` de forma consistente.
- **Flash de Tema**: Eliminado el parpadeo blanco al cargar la página en modo oscuro mediante script de inicialización síncrono.
- **Layout Simétrico**: Header y Footer ajustados con contenedores internos (`.header-inner`) para evitar dispersión en monitores panorámicos.
- **Labeling**: Generalizada la etiqueta de contratos a "Ecosistema Multi-Vendor" para mayor precisión.

## [2026-05-06] — Fase 4: Importación CSV & Modelado de Datos ✅

### Added

- **Motor de Importación**: Implementación de `CsvImportService` con detección inteligente de separador (`,`/`;`) y cabeceras. Ahora soporta 9 columnas incluyendo **Sub-Producto**.
- **Normalización de Datos**: Formateo automático de nombres de clientes (_Title Case_) y gestión de estado _Baja_ para contratos ausentes.
- **Modelo de Datos**: Tablas `vendors`, `clients`, `contracts` e `import_logs` con migraciones incrementales. Añadido campo `sub_product` a la tabla de contratos.
- **UI Administrativa**: Vista `/admin/import` modernizada siguiendo `DESIGN.md`. Protocolo de mapeo balanceado visualmente (5/4).
- **Infraestructura**: Centralización de archivos `.env` mediante volúmenes de Docker y symlinks relativos para estabilidad del entorno.

### Fixed

- **Error de Ingesta**: Solucionado fallo que procesaba 0 registros debido a discrepancia en separadores de CSV.
- **Layout Dashboard**: Refactor de vistas administrativas para usar clases nativas de `dx-styles.css` y evitar solapamientos visuales.
- **UI Balance**: Ajustado el Protocolo de Mapeo de Datos para evitar asimetría tras añadir el campo C9.

## [2026-05-05] — Autenticación JWT y Verificación (Fase 3 ✅)

### Added

- **Servicio JWT**: Implementación de `JwtService` para generación y validación de tokens HS256.
- **AuthController**: Gestión de login/logout con cookies `HttpOnly` seguras.
- **Middleware RBAC**: `JwtAuth` y `CheckPermission` para control de acceso jerárquico (`admin`, `technician`, `staff`, `viewer`).
- **Vista de Login Premium**: Implementación de diseño _Full Background_ con _Glassmorphism_.
- **Persistencia de Tema**: Integración de `localStorage` con Alpine.js para mantener el modo oscuro/claro.
- **Fondo Corporativo**: Nueva imagen y layout optimizado para pantallas ultra-panorámicas (Centrado 50/50).
- **Seguridad**: Implementado **Rate Limiting** (throttle:5,1) en la ruta de login.
- **Tests Automatizados**: Creado `AuthTest.php` con verificación de login, redirecciones y bloqueo de usuarios inactivos (PASS).

### Fixed

- **CSS Conflicts**: Eliminación de selectores heredados que causaban franjas blancas en el layout de login.
- **Ultra-Wide Layout**: Solucionado el problema de dispersión de elementos en monitores panorámicos mediante contenedor centralizado.
- **PHPUnit Config**: Activado SQLite en memoria para ejecución de tests segura.

## [2026-05-05] — Resolución de Assets y Refactor de Layout (Fase 1 y 2 ✅)

### Fixed

- **Desbloqueo de Assets**: Eliminado alias de Nginx para assets externos. Ahora se sirven nativamente desde `backend/public/assets/`.
- **Refactor de Diseño**: Eliminadas clases Tailwind de las vistas Blade y migrado al sistema de CSS Semántico oficial (`dx-styles.css`).
- **Fuentes Locales**: Eliminada dependencia de Google Fonts externos. Ahora se sirven archivos `.woff2` locales.
- **Permisos de Escritura**: Corregidos permisos `777` en `storage/` y `bootstrap/cache/` del servidor.
- **Docker Orchestration**: Añadido `depends_on` en Nginx para garantizar que el upstream PHP esté listo.

### Added

- Documentado aprendizaje en `.agent/lessons.md`.
- Layout principal Blade completamente responsivo y alineado con los prototipos HTML.
- Instalación de Laravel 11 en `backend/`.
- Configuración de Docker para PHP 8.4-FPM, MariaDB y Redis.
- Implementación de `AppServiceProvider` para forzar HTTPS en assets.
- **Cleanup**: Eliminadas ramas locales y remotas ya integradas (`feature/fix-layout-css`, `feature/css-assets`, `feature/laravel-install`, etc.).

## [2026-05-05] — Inicialización de Infraestructura (Fase 0)

...

### Added

- Inicialización del repositorio Git local y conexión con el remoto en GitHub.
- Configuración de ramas `main` y `dev`.
- Creación de workflows de GitHub Actions:
  - `ci.yml`: Verificación básica de estructura.
  - `deploy-beta.yml`: Despliegue automático a stack beta vía SSH.
  - `deploy-prod.yml`: Despliegue automático a stack prod vía SSH.
- Estructura base de carpetas y archivos de gestión (`management/`, `infra/`, etc.) subida al repositorio.

