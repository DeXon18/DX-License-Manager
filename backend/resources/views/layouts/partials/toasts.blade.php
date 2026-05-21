<div x-data="toastManager()" 
     @dx-toast.window="addToast($event.detail)"
     class="dx-v2-toast-container"
     aria-live="polite">
    <template x-for="toast in toasts" :key="toast.id">
        <div class="dx-v2-toast" 
             :class="'dx-v2-toast-' + toast.type" 
             x-show="toast.visible" 
             x-transition:enter="dx-v2-toast-enter" 
             x-transition:enter-start="dx-v2-toast-enter-start"
             x-transition:enter-end="dx-v2-toast-enter-end"
             x-transition:leave="dx-v2-toast-leave"
             x-transition:leave-start="dx-v2-toast-leave-start"
             x-transition:leave-end="dx-v2-toast-leave-end"
             role="alert">
            <span class="dx-v2-toast-icon">
                <template x-if="toast.type === 'success'"><i class="fas fa-check-circle"></i></template>
                <template x-if="toast.type === 'error'"><i class="fas fa-exclamation-circle"></i></template>
                <template x-if="toast.type === 'warning'"><i class="fas fa-exclamation-triangle"></i></template>
                <template x-if="toast.type === 'info'"><i class="fas fa-info-circle"></i></template>
            </span>
            <div class="dx-v2-toast-content">
                <p class="dx-v2-toast-message" x-html="toast.message"></p>
            </div>
            <button type="button" class="dx-v2-toast-close" @click="removeToast(toast.id)" aria-label="Cerrar notificación">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </template>
</div>

<script>
function toastManager() {
    return {
        toasts: [],
        init() {
            // Helper global para JS/AJAX
            window.dxToast = (message, type = 'success', duration = 4000) => {
                this.addToast({ message, type, duration });
            };

            // Capturar sesiones iniciales del backend de Laravel al cargar
            @if(session('success'))
                @if(session('log_id'))
                    this.addToast({ 
                        message: @json(session('success')) + ' <a href="' + @json(route('admin.import.logs.show', session('log_id'))) + '" style="color: var(--dx-v2-accent-base, #58a6ff); font-weight: 700; margin-left: 8px; text-decoration: underline; pointer-events: auto;">VER DETALLES</a>', 
                        type: 'success' 
                    });
                @else
                    this.addToast({ message: @json(session('success')), type: 'success' });
                @endif
            @endif
            @if(session('error'))
                this.addToast({ message: @json(session('error')), type: 'error', duration: 6000 });
            @endif
            @if(session('warning'))
                this.addToast({ message: @json(session('warning')), type: 'warning', duration: 5000 });
            @endif
            @if(session('info'))
                this.addToast({ message: @json(session('info')), type: 'info' });
            @endif
        },
        addToast(detail) {
            const id = 'dx-toast-' + Date.now() + '-' + Math.random().toString(36).substr(2, 9);
            const type = detail.type || 'success';
            const message = detail.message || '';
            const duration = detail.duration !== undefined ? detail.duration : 4000;

            this.toasts.push({ id, type, message, visible: true });

            if (duration > 0) {
                setTimeout(() => {
                    this.removeToast(id);
                }, duration);
            }
        },
        removeToast(id) {
            const index = this.toasts.findIndex(t => t.id === id);
            if (index !== -1) {
                this.toasts[index].visible = false;
                setTimeout(() => {
                    this.toasts = this.toasts.filter(t => t.id !== id);
                }, 300); // Duración de la transición de salida
            }
        }
    };
}
</script>
