<!-- Chatbot de Asistencia IA Web (Fase 25) -->
<link rel="stylesheet" href="{{ asset('assets/css/modules/dx-v2-chatbot.css?v=' . time()) }}">
<div x-data="dxChatbot()" x-init="init()" class="dx-chatbot-container">
    
    <!-- Botón Disparador Flotante -->
    <button @click="toggle()" class="dx-chatbot-trigger" title="Asistente de Soporte IA" aria-label="Asistente de Soporte IA">
        <!-- Icono de Mensaje SVG -->
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width: 20px; height: 20px;">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
        </svg>
    </button>

    <!-- Ventana del Chat -->
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95 translate-y-10"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 translate-y-10"
         class="dx-chatbot-window"
         :class="bentoExpanded ? 'dx-chatbot-bento-expanded' : ''"
         style="display: none;">
        
        <div class="dx-chatbot-inner-wrapper">
            
            <!-- Panel de Chat Principal (Izquierda) -->
            <div class="dx-chatbot-main-pane">
                <!-- Cabecera -->
                <div class="dx-chatbot-header">
                    <div class="dx-chatbot-title-container">
                        <h4 class="dx-chatbot-title">Antigravity Support</h4>
                        <div class="dx-chatbot-status">
                            <span class="dx-chatbot-status-dot"></span>
                            <span>IA Online</span>
                        </div>
                    </div>
                    
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <!-- Botón de Expansión Bento Console -->
                        <button @click="toggleBento()" 
                                class="dx-chatbot-bento-toggle" 
                                :class="bentoExpanded ? 'active' : ''"
                                title="Consola Bento" 
                                aria-label="Consola Bento">
                            <!-- Icono Bento Grid SVG -->
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 14px; height: 14px;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                            </svg>
                        </button>

                        <!-- Botón de Limpiar Historial -->
                        <button @click="clearChat()" class="dx-chatbot-close-btn" title="Limpiar Conversación" aria-label="Limpiar Conversación">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 14px; height: 14px;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                        
                        <!-- Botón de Minimizar -->
                        <button @click="toggle()" class="dx-chatbot-close-btn" title="Minimizar Chat" aria-label="Minimizar Chat">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" style="width: 14px; height: 14px;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Cuerpo de Mensajes -->
                <div x-ref="chatBody" class="dx-chatbot-body">
                    <template x-for="(msg, index) in messages" :key="index">
                        <div class="dx-chatbot-message-wrapper" :class="msg.role === 'user' ? 'user' : 'assistant'">
                            <div class="dx-chatbot-message" x-html="parseMarkdown(msg.content)"></div>
                            <!-- Meta info -->
                            <div class="dx-chatbot-meta" x-text="msg.role === 'user' ? 'Técnico' : (msg.provider ? 'Antigravity IA (' + msg.provider + ')' : 'Asistente')"></div>
                        </div>
                    </template>

                    <!-- Animación de Carga (Escribiendo...) -->
                    <div x-show="loading" class="dx-chatbot-message-wrapper assistant" style="display: none;">
                        <div class="dx-chatbot-message" style="padding: 10px 14px;">
                            <div class="dx-chatbot-typing-container">
                                <span class="dx-chatbot-typing-dot"></span>
                                <span class="dx-chatbot-typing-dot"></span>
                                <span class="dx-chatbot-typing-dot"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer / Input Form -->
                <div class="dx-chatbot-footer">
                    <form @submit.prevent="sendMessage()" class="dx-chatbot-input-form">
                        <input x-model="input" 
                               type="text" 
                               placeholder="Pregunta a la IA sobre licencias, clientes..." 
                               class="dx-chatbot-input"
                               :disabled="loading"
                               required />
                        <button type="submit" class="dx-chatbot-send-btn" :disabled="loading || !input.trim()">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 16px; height: 16px;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Panel Bento (Derecha) -->
            <div x-show="bentoExpanded" class="dx-chatbot-bento-pane" style="display: none;" x-transition>
                <div class="dx-chatbot-bento-header">
                    <span class="dx-chatbot-bento-title">Consola Bento & Telemetría IA</span>
                    <span style="font-size: 0.65rem; color: var(--dx-v2-muted); font-family: var(--dx-v2-font-mono);">
                        Fase 25 — Control de Diagnósticos
                    </span>
                </div>

                <div class="dx-chatbot-bento-content">
                    <!-- Mensaje vacío si no hay datos de herramientas -->
                    <div x-show="!hasBentoData()" style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%; color: var(--dx-v2-muted); text-align: center; gap: 12px; padding: 24px;">
                        <!-- Icono Dashboard -->
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 48px; height: 48px; opacity: 0.5;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
                        </svg>
                        <div style="font-size: 0.8rem; font-weight: 600; color: var(--dx-v2-primary);">Consola Inactiva</div>
                        <p style="font-size: 0.72rem; margin: 0; max-width: 320px;">
                            Pregúntale al chatbot sobre un resumen ejecutivo o pide diagnósticos para activar los módulos visuales interactivos aquí.
                        </p>
                    </div>

                    <!-- Contenido Bento Grid Activo -->
                    <div x-show="hasBentoData()" class="dx-bento-grid">
                        
                        <!-- Telemetría de Tokens de Gemini -->
                        <div class="dx-bento-card dx-col-span-3">
                            <div class="dx-bento-card-title">Telemetría de Turno (Tokens)</div>
                            <div style="display: flex; flex-direction: column; gap: 4px; margin-top: 4px;">
                                <div class="dx-telemetry-item">
                                    <span class="dx-telemetry-label">Prompt Tokens</span>
                                    <span class="dx-telemetry-val" x-text="usage.prompt_tokens || '0'"></span>
                                </div>
                                <div class="dx-telemetry-item">
                                    <span class="dx-telemetry-label">Response Tokens</span>
                                    <span class="dx-telemetry-val" x-text="usage.response_tokens || '0'"></span>
                                </div>
                                <div class="dx-telemetry-item" style="border-bottom: none; padding-bottom: 0;">
                                    <span class="dx-telemetry-label" style="font-weight: 700;">Total Tokens</span>
                                    <span class="dx-telemetry-val" style="color: var(--dx-v2-accent-hover);" x-text="usage.total_tokens || '0'"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Telemetría de Mutaciones -->
                        <div class="dx-bento-card dx-col-span-3" style="justify-content: space-between;">
                            <div>
                                <div class="dx-bento-card-title">Mutaciones de Sesión</div>
                                <div class="dx-bento-card-value" style="margin-top: 8px;">
                                    <span x-text="mutationsCount"></span><span style="font-size: 0.9rem; color: var(--dx-v2-muted); font-weight: 500;"> / 5</span>
                                </div>
                            </div>
                            <div class="dx-bento-card-desc" style="font-size: 0.65rem;">
                                Protección anti-bucles activa para la base de datos.
                             </div>
                        </div>

                        <!-- Resumen Ejecutivo (Bento Summary) -->
                        <template x-if="bentoData.get_dashboard_summary">
                            <div class="dx-bento-card dx-col-span-6" style="gap: 8px;">
                                <div class="dx-bento-card-title">Resumen del Servidor de Licencias</div>
                                <div class="dx-bento-grid">
                                    <div class="dx-bento-card dx-col-span-2" style="border: none; padding: 0; box-shadow: none;">
                                        <span class="dx-bento-card-title" style="font-size: 0.58rem;">Clientes Totales</span>
                                        <span class="dx-bento-card-value" x-text="bentoData.get_dashboard_summary.total_clients"></span>
                                    </div>
                                    <div class="dx-bento-card dx-col-span-2" style="border: none; padding: 0; box-shadow: none;">
                                        <span class="dx-bento-card-title" style="font-size: 0.58rem;">Críticas (30d)</span>
                                        <span class="dx-bento-card-value" style="color: #d29922;" x-text="bentoData.get_dashboard_summary.critical_licenses_30_days"></span>
                                    </div>
                                    <div class="dx-bento-card dx-col-span-2" style="border: none; padding: 0; box-shadow: none;">
                                        <span class="dx-bento-card-title" style="font-size: 0.58rem;">Expiradas</span>
                                        <span class="dx-bento-card-value" style="color: #f85149;" x-text="bentoData.get_dashboard_summary.expired_licenses"></span>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <!-- Lista de Clientes Huérfanos -->
                        <template x-if="bentoData.list_clients_without_contacts">
                            <div class="dx-bento-card dx-col-span-6">
                                <div class="dx-bento-card-title">Clientes Sin Contacto Asociado</div>
                                <div style="max-height: 150px; overflow-y: auto; margin-top: 6px;">
                                    <table class="dx-bento-table">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Nombre del Cliente</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <template x-for="cl in bentoData.list_clients_without_contacts" :key="cl.id">
                                                <tr>
                                                    <td style="font-family: var(--dx-v2-font-mono); font-weight: 700;" x-text="cl.id"></td>
                                                    <td x-text="cl.name"></td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </template>

                        <!-- Detalle de Contrato (get_contract_details) -->
                        <template x-if="bentoData.get_contract_details">
                            <div class="dx-bento-card dx-col-span-6">
                                <div class="dx-bento-card-title">Detalles de Contrato</div>
                                <div style="display: flex; flex-direction: column; gap: var(--dx-v2-spacing-2); margin-top: var(--dx-v2-spacing-1);">
                                    <div style="display: flex; justify-content: space-between;">
                                        <span style="font-size: 0.72rem; font-weight: bold; color: var(--dx-v2-primary);" x-text="bentoData.get_contract_details.contract_number"></span>
                                        <span class="dx-status-badge" :class="bentoData.get_contract_details.expired ? 'expired' : 'healthy'" x-text="bentoData.get_contract_details.expired ? 'Vencido' : 'Activo'"></span>
                                    </div>
                                    <div class="dx-telemetry-item">
                                        <span class="dx-telemetry-label">Cliente</span>
                                        <span style="color: var(--dx-v2-primary); font-weight: 600;" x-text="bentoData.get_contract_details.client_name"></span>
                                    </div>
                                    <div class="dx-telemetry-item">
                                        <span class="dx-telemetry-label">Fecha de Expiración</span>
                                        <span style="color: var(--dx-v2-primary);" x-text="bentoData.get_contract_details.end_date"></span>
                                    </div>
                                    <div class="dx-telemetry-item" style="border-bottom: none;">
                                        <span class="dx-telemetry-label">Fabricante / Daemon</span>
                                        <span style="color: var(--dx-v2-primary);" x-text="bentoData.get_contract_details.vendor"></span>
                                    </div>
                                </div>
                            </div>
                        </template>

                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    function dxChatbot() {
        return {
            open: false,
            input: '',
            loading: false,
            messages: [],
            bentoExpanded: false,
            bentoData: {},
            mutationsCount: 0,
            usage: {
                prompt_tokens: 0,
                response_tokens: 0,
                total_tokens: 0
            },
            
            welcomeMessage: {
                role: 'assistant',
                content: '¡Hola! Escribo desde **Antigravity**. Soy tu asistente técnico de soporte inteligente.\n\nPuedo ayudarte a:\n- Buscar e inspeccionar fichas completas de **clientes y licencias**.\n- Diagnosticar qué productos expiran próximamente en tu inventario.\n- Localizar servidores de licencias por **Composite o MAC**.\n- Añadir nuevos contactos de forma automatizada **pegando un correo electrónico**.\n\n¿En qué puedo asistirte hoy?'
            },

            init() {
                // Recuperar historial de sessionStorage si existe
                const history = sessionStorage.getItem('dx_chatbot_history');
                if (history) {
                    try {
                        this.messages = JSON.parse(history);
                    } catch (e) {
                        this.messages = [this.welcomeMessage];
                    }
                } else {
                    this.messages = [this.welcomeMessage];
                }

                // Recuperar datos de bento y telemetría de sessionStorage
                const bento = sessionStorage.getItem('dx_chatbot_bento');
                if (bento) {
                    try { this.bentoData = JSON.parse(bento); } catch(e) {}
                }
                const usage = sessionStorage.getItem('dx_chatbot_usage');
                if (usage) {
                    try { this.usage = JSON.parse(usage); } catch(e) {}
                }
                const mutations = sessionStorage.getItem('dx_chatbot_mutations');
                if (mutations) {
                    this.mutationsCount = parseInt(mutations, 10) || 0;
                }
                const bentoExpandedVal = sessionStorage.getItem('dx_chatbot_bento_expanded');
                if (bentoExpandedVal) {
                    this.bentoExpanded = bentoExpandedVal === 'true';
                }
                
                // Asegurar scroll correcto al abrir
                this.$watch('open', value => {
                    if (value) {
                        this.$nextTick(() => this.scrollToBottom());
                    }
                });
            },

            toggle() {
                this.open = !this.open;
            },

            toggleBento() {
                this.bentoExpanded = !this.bentoExpanded;
                sessionStorage.setItem('dx_chatbot_bento_expanded', this.bentoExpanded);
            },

            hasBentoData() {
                return this.bentoData && (
                    this.bentoData.get_dashboard_summary ||
                    this.bentoData.list_clients_without_contacts ||
                    this.bentoData.get_contract_details
                );
            },

            clearChat() {
                if (confirm('¿Confirmas que deseas vaciar el historial de la conversación actual?')) {
                    this.messages = [this.welcomeMessage];
                    this.bentoData = {};
                    this.usage = { prompt_tokens: 0, response_tokens: 0, total_tokens: 0 };
                    this.mutationsCount = 0;
                    this.bentoExpanded = false;
                    
                    sessionStorage.removeItem('dx_chatbot_history');
                    sessionStorage.removeItem('dx_chatbot_bento');
                    sessionStorage.removeItem('dx_chatbot_usage');
                    sessionStorage.removeItem('dx_chatbot_mutations');
                    sessionStorage.removeItem('dx_chatbot_bento_expanded');
                    
                    this.$nextTick(() => this.scrollToBottom());
                }
            },

            async sendMessage() {
                const text = this.input.trim();
                if (!text || this.loading) return;

                // Añadir mensaje del usuario al chat
                this.messages.push({
                    role: 'user',
                    content: text
                });
                
                this.input = '';
                this.loading = true;
                sessionStorage.setItem('dx_chatbot_history', JSON.stringify(this.messages));
                
                this.$nextTick(() => this.scrollToBottom());

                try {
                    // Mapear historial para la llamada API
                    const apiMessages = this.messages.map(m => ({
                        role: m.role,
                        content: m.content
                    }));

                    const response = await fetch('{{ route("chatbot.query") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            messages: apiMessages
                        })
                    });

                    if (!response.ok) {
                        const errData = await response.json();
                        throw new Error(errData.message || 'Error en el servidor');
                    }

                    const data = await response.json();
                    
                    // Añadir respuesta de la IA
                    this.messages.push({
                        role: 'assistant',
                        content: data.message,
                        provider: data.provider
                    });

                    // Cargar datos de herramientas de Bento si existen
                    if (data.data) {
                        this.bentoData = { ...this.bentoData, ...data.data };
                        sessionStorage.setItem('dx_chatbot_bento', JSON.stringify(this.bentoData));
                        
                        // Si nos devuelven datos críticos del dashboard o contratos, expandimos la consola Bento!
                        if (data.data.get_dashboard_summary || data.data.list_clients_without_contacts || data.data.get_contract_details) {
                            this.bentoExpanded = true;
                            sessionStorage.setItem('dx_chatbot_bento_expanded', 'true');
                        }
                    }

                    // Cargar telemetría de tokens
                    if (data.usage_metadata) {
                        this.usage.prompt_tokens = data.usage_metadata.promptTokenCount || 0;
                        this.usage.response_tokens = data.usage_metadata.candidatesTokenCount || 0;
                        this.usage.total_tokens = data.usage_metadata.totalTokenCount || 0;
                        sessionStorage.setItem('dx_chatbot_usage', JSON.stringify(this.usage));
                    }

                    // Incrementar mutaciones locales si hubo creación/edición de contacto
                    if (data.data && (data.data.create_contact || data.data.update_contact)) {
                        this.mutationsCount = Math.min(this.mutationsCount + 1, 5);
                        sessionStorage.setItem('dx_chatbot_mutations', this.mutationsCount);
                    }

                } catch (error) {
                    console.error('Error enviando consulta al chatbot:', error);
                    this.messages.push({
                        role: 'assistant',
                        content: '⚠️ **Error de Comunicación**\n\nNo he podido conectar con el servidor de IA del portal. Por favor, verifica tu sesión o reintenta en unos segundos.'
                    });
                } finally {
                    this.loading = false;
                    sessionStorage.setItem('dx_chatbot_history', JSON.stringify(this.messages));
                    this.$nextTick(() => this.scrollToBottom());
                }
            },

            scrollToBottom() {
                const body = this.$refs.chatBody;
                if (body) {
                    body.scrollTop = body.scrollHeight;
                }
            },

            parseMarkdown(text) {
                if (!text) return '';

                // Helper: aplica formato inline (bold, italic, code)
                const applyInline = (str) => {
                    return str
                        .replace(/`([^`]+)`/g, '<code>$1</code>')
                        .replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>')
                        .replace(/\*(.+?)\*/g, '<em>$1</em>')
                        .replace(/_(.+?)_/g, '<em>$1</em>');
                };

                // Escapar HTML (excepto lo que generemos nosotros)
                const esc = (s) => s
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;');

                let lines  = text.split('\n');
                let result = [];

                let inTable    = false, tableHtml = '', tableHasBody = false;
                let inList     = false, listHtml  = '', listType     = '';

                const flushTable = () => {
                    if (!inTable) return;
                    tableHtml += '</tbody></table></div>';
                    result.push(tableHtml);
                    tableHtml = ''; inTable = false; tableHasBody = false;
                };

                const flushList = () => {
                    if (!inList) return;
                    listHtml += `</${listType}>`;
                    result.push(listHtml);
                    listHtml = ''; inList = false; listType = '';
                };

                for (let i = 0; i < lines.length; i++) {
                    const raw     = lines[i];
                    const trimmed = raw.trim();

                    // ── TABLA ──
                    if (trimmed.startsWith('|') && trimmed.endsWith('|')) {
                        flushList();
                        const cells = trimmed.split('|')
                            .map(c => c.trim())
                            .filter((_, idx, arr) => idx > 0 && idx < arr.length - 1);

                        // Fila separadora :---|:---:
                        if (cells.every(c => /^:?-+:?$/.test(c))) {
                            if (inTable && !tableHasBody) {
                                tableHtml += '</thead><tbody>';
                                tableHasBody = true;
                            }
                            continue;
                        }

                        if (!inTable) {
                            inTable = true;
                            tableHasBody = false;
                            tableHtml = '<div class="dx-chat-table-wrap"><table class="dx-chat-table"><thead><tr>';
                            for (const cell of cells) {
                                tableHtml += `<th>${applyInline(esc(cell))}</th>`;
                            }
                            tableHtml += '</tr>';
                            continue;
                        }

                        tableHtml += '<tr>';
                        for (const cell of cells) {
                            tableHtml += `<td>${applyInline(esc(cell))}</td>`;
                        }
                        tableHtml += '</tr>';
                        continue;
                    } else {
                        flushTable();
                    }

                    // ── ENCABEZADOS ──
                    const hMatch = trimmed.match(/^(#{1,4})\s+(.*)/);
                    if (hMatch) {
                        flushList();
                        const lvl  = hMatch[1].length;
                        const htxt = applyInline(esc(hMatch[2]));
                        result.push(`<div class="dx-chat-h${lvl}">${htxt}</div>`);
                        continue;
                    }

                    // ── LISTA BULLET ──
                    const ulMatch = trimmed.match(/^[-*]\s+(.*)/);
                    if (ulMatch) {
                        if (!inList || listType !== 'ul') {
                            flushList();
                            inList = true; listType = 'ul';
                            listHtml = '<ul class="dx-chat-list">';
                        }
                        listHtml += `<li>${applyInline(esc(ulMatch[1]))}</li>`;
                        continue;
                    }

                    // ── LISTA NUMERADA ──
                    const olMatch = trimmed.match(/^\d+\.\s+(.*)/);
                    if (olMatch) {
                        if (!inList || listType !== 'ol') {
                            flushList();
                            inList = true; listType = 'ol';
                            listHtml = '<ol class="dx-chat-list">';
                        }
                        listHtml += `<li>${applyInline(esc(olMatch[1]))}</li>`;
                        continue;
                    }

                    // ── SEPARADOR HORIZONTAL ──
                    if (/^[-*_]{3,}$/.test(trimmed)) {
                        flushList();
                        result.push('<hr class="dx-chat-hr">');
                        continue;
                    }

                    // ── LÍNEA VACÍA ──
                    if (trimmed === '') {
                        flushList();
                        result.push('__BLANK__');
                        continue;
                    }

                    // ── TEXTO NORMAL ──
                    flushList();
                    result.push(applyInline(esc(trimmed)));
                }

                flushTable();
                flushList();

                // Unir todo
                return result.map((line, idx) => {
                    if (line === '__BLANK__') return '<br>';
                    if (line.startsWith('<div class="dx-chat-h') ||
                        line.startsWith('<div class="dx-chat-table-wrap') ||
                        line.startsWith('<ul') || line.startsWith('<ol') ||
                        line.startsWith('<hr')) {
                        return line;
                    }
                    return line + '<br>';
                }).join('').replace(/(<br>)+$/, '');
            }
        };
    }
</script>
