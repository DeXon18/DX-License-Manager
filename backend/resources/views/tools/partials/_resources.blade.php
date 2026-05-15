<div id="resources" class="card" style="margin-top: 24px;" x-data="resourceManager('{{ $vendor }}')">
    <div class="card-header" style="justify-content: space-between;">
        <span class="card-title">Recursos y Enlaces de Referencia</span>
        @if(auth()->user()->role->slug !== 'viewer')
            <button @click="openCreateModal()" class="btn-primary" style="padding: 4px 12px; font-size: 11px;">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="margin-right: 4px;"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                AÑADIR ENLACE
            </button>
        @endif
    </div>

    <div style="padding: 24px;">
        @if($resources->isEmpty())
            <div style="text-align: center; padding: 40px; border: 1px dashed var(--border); border-radius: 8px; background: rgba(0,0,0,0.02);">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="var(--muted)" stroke-width="1.5" style="margin-bottom: 12px; opacity: 0.5;"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
                <div style="font-size: 13px; color: var(--muted);">No hay recursos registrados todavía.</div>
            </div>
        @else
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
                @foreach(['official' => 'Documentación Oficial', 'internal' => 'Recursos Internos', 'utility' => 'Herramientas Útiles', 'support' => 'Soporte Técnico'] as $cat => $title)
                    @if(isset($resources[$cat]))
                        <div style="grid-column: 1 / -1; margin-top: 10px; first-of-type:margin-top: 0;">
                            <div style="font-size: 11px; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid var(--border-subtle); padding-bottom: 6px; margin-bottom: 12px;">
                                {{ $title }}
                            </div>
                        </div>
                        @foreach($resources[$cat] as $res)
                            <div class="resource-card" style="display: flex; gap: 16px; padding: 16px; border: 1px solid var(--border-subtle); border-radius: 10px; background: var(--bg-subtle); transition: all 0.2s; position: relative;">
                                <div style="width: 40px; height: 40px; border-radius: 8px; background: var(--bg); border: 1px solid var(--border); display: flex; align-items: center; justify-content: center; color: var(--vendor-{{ $vendor }}, #009999); flex-shrink: 0;">
                                    @if($res->icon === 'book')
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                                    @elseif($res->icon === 'shield')
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                                    @elseif($res->icon === 'utility')
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
                                    @else
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
                                    @endif
                                </div>
                                <div style="flex: 1; min-width: 0;">
                                    <a href="{{ $res->url }}" target="_blank" style="font-size: 14px; font-weight: 600; color: var(--primary); display: block; margin-bottom: 2px; text-decoration: none;" class="hover-underline">
                                        {{ $res->label }}
                                    </a>
                                    <div style="font-size: 11px; color: var(--muted); line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                        {{ $res->description ?? 'Sin descripción disponible.' }}
                                    </div>
                                </div>
                                @if(auth()->user()->role->slug !== 'viewer')
                                    <div style="position: absolute; top: 8px; right: 8px; display: flex; gap: 4px; opacity: 0;" class="card-actions">
                                        <button @click="editResource({{ json_encode($res) }})" style="width: 24px; height: 24px; border-radius: 4px; border: none; background: rgba(0,0,0,0.05); color: var(--muted); display: flex; align-items: center; justify-content: center; cursor: pointer;">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                        </button>
                                        <button @click="deleteResource('{{ $res->id }}')" style="width: 24px; height: 24px; border-radius: 4px; border: none; background: rgba(211,47,47,0.1); color: #D32F2F; display: flex; align-items: center; justify-content: center; cursor: pointer;">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                        </button>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    @endif
                @endforeach
            </div>
        @endif
    </div>

    <!-- Modal Manager (Alpine.js) -->
    <template x-if="showModal">
        <div class="modal-overlay" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 1000; backdrop-filter: blur(4px);">
            <div class="modal-content card" style="width: 100%; max-width: 500px; margin: 20px;">
                <div class="card-header" style="justify-content: space-between;">
                    <span class="card-title" x-text="editingId ? 'Editar Recurso' : 'Nuevo Recurso'"></span>
                    <button @click="showModal = false" style="background: none; border: none; color: var(--muted); cursor: pointer;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                </div>
                <div style="padding: 24px;">
                    <form @submit.prevent="saveResource()">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                            <div>
                                <label class="label-sm">Categoría</label>
                                <select x-model="form.category" class="input-sm" style="width: 100%;">
                                    <option value="official">Oficial</option>
                                    <option value="internal">Interno</option>
                                    <option value="utility">Utilidad</option>
                                    <option value="support">Soporte</option>
                                </select>
                            </div>
                            <div>
                                <label class="label-sm">Icono</label>
                                <select x-model="form.icon" class="input-sm" style="width: 100%;">
                                    <option value="link">Enlace (Globo)</option>
                                    <option value="book">Libro (Doc)</option>
                                    <option value="shield">Escudo (Seguridad)</option>
                                    <option value="utility">Llave (Herramienta)</option>
                                </select>
                            </div>
                        </div>

                        <div style="margin-bottom: 16px;">
                            <label class="label-sm">Título del Recurso</label>
                            <input type="text" x-model="form.label" class="input-sm" style="width: 100%;" placeholder="Ej: Manual de Instalación NX">
                        </div>

                        <div style="margin-bottom: 16px;">
                            <label class="label-sm">URL (Enlace)</label>
                            <input type="url" x-model="form.url" class="input-sm" style="width: 100%;" placeholder="https://...">
                        </div>

                        <div style="margin-bottom: 24px;">
                            <label class="label-sm">Descripción Corta</label>
                            <textarea x-model="form.description" class="input-sm" style="width: 100%; height: 60px; padding: 8px;" placeholder="Breve detalle sobre qué contiene este enlace..."></textarea>
                        </div>

                        <div style="display: flex; justify-content: flex-end; gap: 12px;">
                            <button type="button" @click="showModal = false" class="btn-secondary" style="padding: 8px 16px;">CANCELAR</button>
                            <button type="submit" class="btn-primary" style="padding: 8px 24px;" x-text="loading ? 'Guardando...' : 'GUARDAR RECURSO'"></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </template>
</div>

<style>
    .resource-card:hover {
        border-color: var(--vendor-{{ $vendor }}, #009999) !important;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }
    .resource-card:hover .card-actions {
        opacity: 1 !important;
    }
    .hover-underline:hover {
        text-decoration: underline !important;
        color: var(--vendor-{{ $vendor }}, #009999) !important;
    }
    .label-sm {
        display: block;
        font-size: 11px;
        font-weight: 700;
        color: var(--muted);
        text-transform: uppercase;
        margin-bottom: 6px;
    }
    .input-sm {
        background: var(--bg);
        border: 1px solid var(--border);
        color: var(--primary);
        border-radius: 6px;
        font-size: 13px;
        padding: 8px 12px;
        transition: all 0.2s;
    }
    .input-sm:focus {
        border-color: var(--vendor-{{ $vendor }}, #009999);
        outline: none;
        box-shadow: 0 0 0 3px rgba(0,153,153,0.1);
    }
</style>

<script>
function resourceManager(vendor) {
    return {
        showModal: false,
        loading: false,
        editingId: null,
        vendor: vendor,
        form: {
            vendor: vendor,
            category: 'official',
            label: '',
            url: '',
            description: '',
            icon: 'link',
            order: 0
        },
        openCreateModal() {
            this.editingId = null;
            this.form = {
                vendor: this.vendor,
                category: 'official',
                label: '',
                url: '',
                description: '',
                icon: 'link',
                order: 0
            };
            this.showModal = true;
        },
        editResource(res) {
            this.editingId = res.id;
            this.form = { ...res };
            this.showModal = true;
        },
        async saveResource() {
            this.loading = true;
            try {
                const url = this.editingId ? `/admin/resources/${this.editingId}` : '/admin/resources';
                const method = this.editingId ? 'PUT' : 'POST';
                
                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(this.form)
                });

                const result = await response.json();
                if (result.success) {
                    window.location.reload();
                } else {
                    alert(result.error || 'Error al guardar el recurso');
                }
            } catch (error) {
                console.error(error);
                alert('Error de conexión');
            } finally {
                this.loading = false;
            }
        },
        async deleteResource(id) {
            if (!confirm('¿Estás seguro de eliminar este recurso?')) return;
            
            try {
                const response = await fetch(`/admin/resources/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                const result = await response.json();
                if (result.success) {
                    window.location.reload();
                }
            } catch (error) {
                console.error(error);
            }
        }
    }
}
</script>
