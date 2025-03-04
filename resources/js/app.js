
import './bootstrap';
import 'bootstrap/dist/css/bootstrap.min.css';
import '../css/app.css';
import '@fortawesome/fontawesome-free/css/all.min.css';


import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';

createInertiaApp({
    title: (title) => `${title}`,
    //tự động load trang Vue => không cân fimport thủ công
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        //h là hàm render component
        return createApp({ render: () => h(App, props) })
            .use(plugin) //kết nối Inertia với Vue
            .use(ZiggyVue) //giúp vue sử dụng route laravel dễ dàng
            .mount(el); //gắn ứng udngj vào DOM
    },
    progress: {
        color: '#4B5563',
    },
});
