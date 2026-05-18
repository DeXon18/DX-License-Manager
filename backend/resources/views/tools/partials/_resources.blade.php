<div id="resources" class="card" x-data="resourceManager('{{ $vendor }}')">
    <div class="card-header">
        <span class="card-title">Recursos y Enlaces de Referencia</span>
        @if(auth()->user()->role->slug !== 'viewer')
            <button @click="openCreateModal()" class="btn-primary">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                AÑADIR ENLACE
            </button>
        @endif
    </div>

    <div class="dx-v2-resources-body">
        @if($resources->isEmpty())
            <div class="dx-v2-resources-empty-state">
                <svg class="dx-v2-resources-empty-state-icon" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
                <div class="dx-v2-resources-empty-state-text">No hay recursos registrados todavía.</div>
            </div>
        @else
            <div class="dx-v2-resources-card-list">
                @foreach(['official' => 'Documentación Oficial', 'internal' => 'Recursos Internos', 'utility' => 'Herramientas Útiles', 'support' => 'Soporte Técnico'] as $cat => $title)
                    @if(isset($resources[$cat]))
                        <div class="dx-v2-resources-category-row">
                            <div class="dx-v2-resources-category-title">
                                {{ $title }}
                            </div>
                        </div>
                        @foreach($resources[$cat] as $res)
                            <div class="dx-v2-resources-card {{ $vendor }}">
                                <div class="dx-v2-resources-card-icon {{ $vendor }}">
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
                                <div class="dx-v2-resources-card-content">
                                    <a href="{{ $res->url }}" target="_blank" class="dx-v2-resources-card-link {{ $vendor }}">
                                        {{ $res->label }}
                                    </a>
                                    <div class="dx-v2-resources-card-description">
                                        {{ $res->description ?? 'Sin descripción disponible.' }}
                                    </div>
                                </div>
                                @if(auth()->user()->role->slug !== 'viewer')
                                    <div class="dx-v2-resources-card-actions">
                                        <button @click="editResource({{ json_encode($res) }})" class="dx-v2-resources-action-btn edit" title="Editar">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                        </button>
                                        <button @click="deleteResource('{{ $res->id }}')" class="dx-v2-resources-action-btn delete" title="Eliminar">
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
        <div class="dx-v2-resources-modal-overlay">
            <div class="dx-v2-resources-modal card">
                <div class="card-header">
                    <span class="card-title" x-text="editingId ? 'Editar Recurso' : 'Nuevo Recurso'"></span>
                    <button @click="showModal = false" class="btn-close-x">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                </div>
                <div class="dx-v2-resources-modal-body">
                    <form @submit.prevent="saveResource()">
                        <div class="dx-v2-resources-modal-form-grid">
                            <div>
                                <label class="dx-v2-resources-modal-label">Categoría</label>
                                <select x-model="form.category" class="dx-v2-resources-modal-input {{ $vendor }}">
                                    <option value="official">Oficial</option>
                                    <option value="internal">Interno</option>
                                    <option value="utility">Utilidad</option>
                                    <option value="support">Soporte</option>
                                </select>
                            </div>
                            <div>
                                <label class="dx-v2-resources-modal-label">Icono</label>
                                <select x-model="form.icon" class="dx-v2-resources-modal-input {{ $vendor }}">
                                    <option value="link">Enlace (Globo)</option>
                                    <option value="book">Libro (Doc)</option>
                                    <option value="shield">Escudo (Seguridad)</option>
                                    <option value="utility">Llave (Herramienta)</option>
                                </select>
                            </div>
                        </div>

                        <div class="dx-v2-resources-modal-form-row">
                            <label class="dx-v2-resources-modal-label">Título del Recurso</label>
                            <input type="text" x-model="form.label" class="dx-v2-resources-modal-input {{ $vendor }}" placeholder="Ej: Manual de Instalación NX">
                        </div>

                        <div class="dx-v2-resources-modal-form-row">
                            <label class="dx-v2-resources-modal-label">URL (Enlace)</label>
                            <input type="url" x-model="form.url" class="dx-v2-resources-modal-input {{ $vendor }}" placeholder="https://...">
                        </div>

                        <div class="dx-v2-resources-modal-form-row-large">
                            <label class="dx-v2-resources-modal-label">Descripción Corta</label>
                            <textarea x-model="form.description" class="dx-v2-resources-modal-input {{ $vendor }}" placeholder="Breve detalle sobre qué contiene este enlace..."></textarea>
                        </div>

                        <div class="dx-v2-resources-modal-footer">
                            <button type="button" @click="showModal = false" class="btn-secondary">CANCELAR</button>
                            <button type="submit" class="btn-primary" x-text="loading ? 'Guardando...' : 'GUARDAR RECURSO'"></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </template>
</div>

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
