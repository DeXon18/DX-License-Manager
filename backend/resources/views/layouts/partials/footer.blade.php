<footer class="main-footer">
    <div class="footer-container">
        <div class="footer-grid">
            <!-- Columna 1: Marca -->
            <div class="footer-col brand-col">
                <div class="footer-logo">
                    <div class="logo-box">DX</div>
                    <span class="logo-text">License Manager</span>
                </div>
                <p class="footer-desc">
                    Gestión inteligente y auditoría avanzada de licencias de software industrial para el ecosistema Siemens PLM y Moldex3D.
                </p>
                <div class="footer-social">
                    <a href="#" title="Documentación"><i class="fa-solid fa-book"></i></a>
                    <a href="#" title="Soporte"><i class="fa-solid fa-headset"></i></a>
                    <a href="#" title="GitHub"><i class="fa-brands fa-github"></i></a>
                </div>
            </div>

            <!-- Columna 2: Navegación -->
            <div class="footer-col nav-col">
                <h4 class="footer-title">Navegación</h4>
                <ul class="footer-links">
                    <li><a href="{{ url('/') }}"><i class="fa-solid fa-chart-line"></i> Dashboard</a></li>
                    <li><a href="{{ route('clients.index') }}"><i class="fa-solid fa-users"></i> Clientes</a></li>
                    <li><a href="{{ route('tools.index') }}"><i class="fa-solid fa-toolbox"></i> Herramientas</a></li>
                    <li><a href="#"><i class="fa-solid fa-shield-halved"></i> Auditoría IA</a></li>
                    <li><a href="{{ route('system.changelog') }}"><i class="fa-solid fa-clock-rotate-left"></i> Historial de Cambios</a></li>
                </ul>
            </div>

            <!-- Columna 3: Stack Técnico -->
            <div class="footer-col tech-col">
                <h4 class="footer-title">Stack Técnico</h4>
                <ul class="tech-list">
                    <li><span class="tech-label">Core:</span> PHP 8.2 / Laravel 11</li>
                    <li><span class="tech-label">DB:</span> MariaDB 10.11 LTS</li>
                    <li><span class="tech-label">UI:</span> Blade + Vanilla CSS</li>
                    <li><span class="tech-label">IA:</span> Gemini + DeepSeek</li>
                </ul>
            </div>

            <!-- Columna 4: Infraestructura -->
            <div class="footer-col infra-col">
                <h4 class="footer-title">Infraestructura</h4>
                <div class="infra-status">
                    <div class="infra-item">
                        <span class="status-dot online"></span>
                        <span class="infra-label">Entorno:</span>
                        <span class="infra-value">Beta Cluster</span>
                    </div>
                    <div class="infra-item">
                        <i class="fa-solid fa-server"></i>
                        <span class="infra-label">Nodo:</span>
                        <span class="infra-value">LXC 600 (srv-dxportal)</span>
                    </div>
                    <div class="infra-item">
                        <i class="fa-solid fa-network-wired"></i>
                        <span class="infra-label">IP:</span>
                        <span class="infra-value">192.168.50.60</span>
                    </div>
                    <div class="infra-item">
                        <i class="fa-solid fa-code-branch"></i>
                        <span class="infra-label">Versión:</span>
                        <span class="infra-value">v2.7.4-stable</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <div class="footer-copyright">
                &copy; {{ date('Y') }} DX License Manager. Todos los derechos reservados.
            </div>
            <div class="footer-meta">
                <span>Desarrollado con <i class="fa-solid fa-heart dx-v2-heart"></i> por Antigravity (DX Agent)</span>
            </div>
        </div>
    </div>
</footer>
