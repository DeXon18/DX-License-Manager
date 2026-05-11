He completado la implementación del **Mantenimiento Selectivo (Admin Friendly)**.

### Mejoras Implementadas:
1.  **Middleware de Filtrado**: Nuevo middleware `SelectiveMaintenance` que intercepta las peticiones y permite el acceso solo a administradores cuando el mantenimiento está activo.
2.  **Bypass para Administradores**: Ahora puedes pulsar el botón de mantenimiento y seguir navegando por todo el portal (incluyendo administración y herramientas) sin ser expulsado.
3.  **Aviso Visual Persistente**: Se ha añadido un banner superior de color ámbar que te recuerda que el mantenimiento está activo y que los usuarios estándar no pueden entrar.
4.  **Página 503 Premium**: Los usuarios no administradores verán una página de mantenimiento profesional con el logo del proyecto y un diseño minimalista.
5.  **Control Centralizado**: El botón del Dashboard ahora gestiona este nuevo modo selectivo, asegurando que nunca te vuelvas a quedar fuera del sistema.

### Verificación:
- **Admin**: Puede navegar libremente y ve el banner superior.
- **Público/Usuario**: Recibe un error 503 con la nueva vista personalizada.
- **Login**: Sigue siendo accesible para que los administradores puedan iniciar sesión incluso en mantenimiento.

Los cambios han sido commiteados y subidos a la rama `feature/system-noc-pro`.
