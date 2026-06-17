import './bootstrap';
import '../css/app.css';
import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import FloatingVue from 'floating-vue';
import 'floating-vue/dist/style.css';
import { plugin, defaultConfig } from '@formkit/vue';
import '@formkit/themes/genesis';
import 'vue-select/dist/vue-select.css';
import ElementPlus from 'element-plus';
import 'element-plus/dist/index.css';
import axios from 'axios';

axios.interceptors.response.use(
    (response) => response,
    (error) => {
        const status = error?.response?.status;

        if (status === 401 || status === 419) {
            // Kalau session kedaluwarsa / csrf expired → keluar dari shell dan reload ke login
            window.location.replace('/login');
        }

        return Promise.reject(error);
    },
);

createInertiaApp({
    progress: {
        color: '#4f46e5',
        delay: 150,
        includeCSS: true,
        showSpinner: false,
    },
    resolve: async (name) => {
        const page = await resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue'));

        return page;
    },
    setup({ el, App, props, plugin: inertiaPlugin }) {
        createApp({ render: () => h(App, props) })
            .use(inertiaPlugin)
            .use(FloatingVue)
            .use(plugin, defaultConfig)
            .use(ElementPlus)
            .mount(el);
    },
});
