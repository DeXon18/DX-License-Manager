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
                </div><!-- footer -->
            </div><!-- main-pane -->
        </div><!-- inner-wrapper -->
    </div><!-- window -->
</div><!-- container -->

<script>
    function dxChatbot() {
        return {
            open: false,
            input: '',
            loading: false,
            messages: [],

            welcomeMessage: {
                role: 'assistant',
                content: '¡Hola! Escribo desde **Antigravity**. Soy tu asistente técnico de soporte inteligente.\n\nPuedo ayudarte a:\n- Buscar e inspeccionar fichas completas de **clientes y licencias**.\n- Diagnosticar qué productos expiran próximamente en tu inventario.\n- Localizar servidores de licencias por **Composite o MAC**.\n- Añadir nuevos contactos de forma automatizada **pegando un correo electrónico**.\n\n¿En qué puedo asistirte hoy?'
            },

            init() {
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

                this.$watch('open', value => {
                    if (value) {
                        this.$nextTick(() => this.scrollToBottom());
                    }
                });
            },

            toggle() {
                this.open = !this.open;
            },

            clearChat() {
                if (confirm('¿Confirmas que deseas vaciar el historial de la conversación actual?')) {
                    this.messages = [this.welcomeMessage];
                    sessionStorage.removeItem('dx_chatbot_history');
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

                    this.messages.push({
                        role: 'assistant',
                        content: data.message,
                        provider: data.provider
                    });

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
