import './bootstrap';
import '../css/app.css';
import 'flowbite';
import 'floating-vue/dist/style.css'

import { createApp, h, DefineComponent } from 'vue';
import {createInertiaApp, router} from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import FloatingVue from 'floating-vue'
// import { ZiggyVue } from '../../vendor/tightenco/ziggy';

import Layout from './Layouts/Layout.vue';

const appName = import.meta.env.VITE_APP_NAME || 'Curriculum Mapping Tool';

router.on('invalid', (event) => {
  if (event.detail.response.status === 403) event.preventDefault();
});

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => {
        const page = resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob<DefineComponent>('./Pages/**/*.vue'),
        );

        page.then((module) => {
            if (!name.startsWith('Auth/')) {
                module.default.layout = module.default.layout || Layout;
            }
        });

        return page;
    },
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(FloatingVue)
            // .use(ZiggyVue);

        app.config.globalProperties.route = route; // not using the ZiggyVue plugin because deploying it in the GitHub action will be a bit problematic (I will have to install php and do a composer install)
        app.mount(el);
    },
    progress: {
        color: '#dc2626', // bg-red-600
    },
});
