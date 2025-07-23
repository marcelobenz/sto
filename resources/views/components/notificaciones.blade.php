<div x-data="notificacionesComponent()" x-init="init()" class="absolute top-4 right-40 z-50">
    <button @click="toggleDropdown"
        class="relative bg-blue-600 text-white px-3 py-2 rounded-full hover:bg-blue-700 focus:outline-none">
        <i class="fas fa-bell"></i>
        <span x-show="unreadCount > 0" x-text="unreadCount"
            class="absolute -top-1 -right-1 bg-red-500 text-xs rounded-full px-1.5 py-0.5 text-white"></span>
    </button>

    <!-- Dropdown -->
    <div x-show="showDropdown" @click.outside="showDropdown = false"
        class="absolute right-0 mt-2 w-80 bg-white border border-gray-300 rounded-lg shadow-lg max-h-96 overflow-y-auto">
        <div class="p-4 space-y-2 text-sm text-gray-700" x-html="renderedContent"></div>
    </div>

    <script>
        function notificacionesComponent() {
            return {
                showDropdown: false,
                notificaciones: [],
                unreadCount: 0,
                renderedContent: '',
                toggleDropdown() {
                    this.showDropdown = !this.showDropdown;
                    if (this.showDropdown) this.render();
                },
                render() {
                    if (this.notificaciones.length === 0) {
                        this.renderedContent = '<p class="text-center text-gray-400">Sin notificaciones</p>';
                        this.unreadCount = 0;
                        return;
                    }

                    this.unreadCount = this.notificaciones.filter(n => !n.leida).length;

                    this.renderedContent = this.notificaciones.map(n => `
                        <div class="p-2 rounded ${n.leida ? 'bg-gray-100' : 'bg-blue-50'}">
                            <span class="block">${n.mensaje}</span>
                        </div>
                    `).join('');
                },
                async init() {
                    // Simulación (reemplazar con llamada al backend)
                    this.notificaciones = [
                        { id: 1, mensaje: 'Tienes una nueva solicitud.', leida: false },
                        { id: 2, mensaje: 'El estado del trámite ha cambiado.', leida: false },
                        { id: 3, mensaje: 'Recordatorio: falta adjuntar un archivo.', leida: true },
                    ];

                    this.render();
                }
            }
        }
    </script>
</div>
