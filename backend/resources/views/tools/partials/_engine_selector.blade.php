<div class="card" style="margin-bottom: 16px; background: var(--card-bg); border: 1px solid var(--border);">
    <div style="padding: 16px;">
        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 16px; color: var(--accent);">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M7 16l-4-4 4-4"/><path d="M17 8l4 4-4 4"/><path d="M3 12h18"/></svg>
            <span style="font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">Cambiar Motor</span>
        </div>

        <div style="display: flex; flex-direction: column; gap: 8px;">
            <!-- NX Suite -->
            <a href="{{ route('tools.nx-suite.index') }}" 
               class="engine-link {{ Route::is('tools.nx-suite.*') ? 'active' : '' }}"
               style="--engine-color: #c2570a; --engine-bg: rgba(194,87,10,0.1);">
                <div class="engine-icon-box">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/></svg>
                </div>
                <span class="engine-label">NX Suite</span>
            </a>

            <!-- STAR-CCM+ -->
            <a href="{{ route('tools.star-ccm.index') }}" 
               class="engine-link {{ Route::is('tools.star-ccm.*') ? 'active' : '' }}"
               style="--engine-color: #0369a1; --engine-bg: rgba(3,105,161,0.1);">
                <div class="engine-icon-box">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                </div>
                <span class="engine-label">STAR-CCM+</span>
            </a>

            <!-- HEEDS -->
            <a href="{{ route('tools.heeds.index') }}" 
               class="engine-link {{ Route::is('tools.heeds.*') ? 'active' : '' }}"
               style="--engine-color: #7e22ce; --engine-bg: rgba(126,34,206,0.1);">
                <div class="engine-icon-box">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="22" y1="12" x2="18" y2="12"/><line x1="6" y1="12" x2="2" y2="12"/><line x1="12" y1="6" x2="12" y2="2"/><line x1="12" y1="22" x2="12" y2="18"/></svg>
                </div>
                <span class="engine-label">HEEDS</span>
            </a>
        </div>
    </div>
</div>

<style>
    .engine-link {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 12px;
        border-radius: 8px;
        text-decoration: none;
        background: var(--bg);
        border: 1px solid var(--border-subtle);
        transition: all 0.2s ease;
    }
    .engine-link:hover {
        background: var(--engine-bg);
        border-color: var(--engine-color);
        transform: translateX(4px);
    }
    .engine-link.active {
        background: var(--engine-bg);
        border-color: var(--engine-color);
        pointer-events: none;
    }
    .engine-icon-box {
        width: 28px;
        height: 28px;
        background: var(--engine-bg);
        color: var(--engine-color);
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .engine-label {
        font-size: 13px;
        font-weight: 600;
        color: var(--primary);
    }
    .engine-link.active .engine-label {
        color: var(--engine-color);
    }
</style>
