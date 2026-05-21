<footer class="main-footer">
    <div class="footer-container">
        <div class="footer-grid">
            <!-- Columna 1: Marca -->
            <div class="footer-col brand-col">
                <div class="dx-lockup">
                    <div class="dx-mark">
                        <span>DX</span>
                    </div>
                    <div class="dx-wordmark">
                        <span class="dx-name">License Manager</span>
                        <span class="dx-sub">by DXPro</span>
                    </div>
                </div>
                <p class="footer-desc">
                    Gestión inteligente y auditoría avanzada de licencias de software industrial para el ecosistema Siemens PLM y Moldex3D.
                </p>
                <div class="footer-social">
                    <a href="#" title="Documentación"><i class="fa-solid fa-book"></i></a>
                    <a href="https://www.linkedin.com/in/oskar-blazquez/" title="LinkedIn"><i class="fa-brands fa-linkedin-in"></i></a>
                    <a href="https://github.com/DeXon18" title="GitHub"><i class="fa-brands fa-github"></i></a>
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

            <!-- Columna 3: Soporte AYS -->
            <div class="footer-col tech-col">
                <h4 class="footer-title">Soporte Interno</h4>
                <ul class="footer-links">
                    <li><a href="#"><i class="fa-solid fa-book-open"></i> Guías del Portal</a></li>
                    <li><a href="#"><i class="fa-solid fa-envelope"></i> Contactar Soporte IT</a></li>
                    <li><a href="#"><i class="fa-solid fa-key"></i> Solicitar Lic. Temporal</a></li>
                    <li><a href="#"><i class="fa-solid fa-circle-question"></i> FAQ / Ayuda</a></li>
                </ul>
            </div>

            <!-- Columna 4: Portales Oficiales -->
            <div class="footer-col infra-col">
                <h4 class="footer-title">Portales Oficiales</h4>
                <ul class="footer-links">
                    <li><a href="https://support.sw.siemens.com" target="_blank" rel="noopener"><i class="fa-solid fa-arrow-up-right-from-square" style="font-size: 0.85em;"></i> Siemens Support Center</a></li>
                    <li><a href="https://account.sw.siemens.com" target="_blank" rel="noopener"><i class="fa-solid fa-arrow-up-right-from-square" style="font-size: 0.85em;"></i> Siemens WebKey</a></li>
                    <li><a href="https://www.moldex3d.com/support/" target="_blank" rel="noopener"><i class="fa-solid fa-arrow-up-right-from-square" style="font-size: 0.85em;"></i> Moldex3D Support</a></li>
                    <li><a href="https://community.sw.siemens.com" target="_blank" rel="noopener"><i class="fa-solid fa-arrow-up-right-from-square" style="font-size: 0.85em;"></i> Siemens Community</a></li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <div class="footer-copyright">
                &copy; {{ date('Y') }} DX License Manager. Todos los derechos reservados.
            </div>
            <div class="footer-meta">
                <span style="color: #059669; font-weight: 600; letter-spacing: 0.5px; display: inline-flex; align-items: center; gap: 5px;">
                    <i class="fa-solid fa-shield-halved" style="font-size: 0.85em;"></i> AI-Powered Productivity
                </span>
                <span style="margin: 0 4px; opacity: 0.5;">—</span>
                <span>Desarrollado con <i class="fa-solid fa-heart dx-v2-heart"></i> por Antigravity (DX Agent)</span>
            </div>
    </div>
</footer>
