import './bootstrap';
import Alpine from 'alpinejs';
import { createApp } from 'vue';
import { createVuetify } from 'vuetify';
import 'vuetify/styles';

import DetalleTramite from './components/DetalleTramite.vue';

window.Alpine = Alpine;
Alpine.start();

// 🔥 Configurar Vuetify correctamente
const vuetify = createVuetify();

const app = createApp({});
app.use(vuetify); // 🚀 Asegúrate de agregar Vuetify a Vue
app.component('detalle-tramite', DetalleTramite);

if (document.getElementById('app')) {
    app.mount('#app');
}
