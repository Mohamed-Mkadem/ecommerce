import '../css/app.css';
import '../css/icons.css';
import './bootstrap';
import 'flatpickr/dist/flatpickr.min.css'
import "vue-toastification/dist/index.css";
import Toast from "vue-toastification";
import { getToastOptions } from './Utils/toast';
import { createInertiaApp, Link, Head } from '@inertiajs/vue3';
import { createApp, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import { i18nVue } from 'laravel-vue-i18n';
import { createPinia } from 'pinia'
import { Modal, ModalLink, renderApp } from '@inertiaui/modal-vue'
import OrderLayout from './Layouts/OrderLayout.vue'
import AdminLayout from './Layouts/AdminLayout.vue'
import FrontEndLayout from './Layouts/FrontEndLayout.vue'

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';
const pinia = createPinia()
createInertiaApp({
    title: (title) => `${title} - Boughanmi Patisserie`,
    resolve: (name) => {
        const pages = import.meta.glob('./Pages/**/*.vue', { eager: true })
        let page = pages[`./Pages/${name}.vue`]
        if (name.startsWith('Admin')) {
            page.default.layout = AdminLayout
        } else if (name.startsWith('FrontEnd')) {
            page.default.layout = FrontEndLayout

        } else if (name.startsWith('Order')) {
            page.default.layout = OrderLayout

        }
        else {
            page.default.layout = false
        }
        return page
    },
    setup({ el, App, props, plugin }) {
        return createApp({ render: renderApp(App, props) })
            .component('Link', Link)
            .component('Head', Head)
            .component('Modal', Modal)
            .component('ModalLink', ModalLink)
            .use(Toast, getToastOptions())
            .use(pinia)
            .use(plugin)
            .use(ZiggyVue)
            .use(i18nVue, {
                resolve: async lang => {
                    const langs = import.meta.glob('../../lang/*.json');
                    return await langs[`../../lang/${lang}.json`]();
                }
            })
            .mount(el);
    },


    progress: false
});
