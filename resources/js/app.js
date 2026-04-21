import '../css/app.css'
import { createInertiaApp, router } from '@inertiajs/vue3'
import { createSSRApp, h } from 'vue'
import BaseLayout from './Components/Layouts/Base.vue'
import { ZiggyVue } from 'ziggy-js'
import { Ziggy } from './ziggy'
import { initFlowbite } from 'flowbite'

createInertiaApp({
    layout: () => BaseLayout,
    resolve: name => {
        const pages = import.meta.glob('./Pages/**/*.vue', { eager: true })
        return pages[`./Pages/${name}.vue`]
    },

    setup({ el, App, props, plugin }) {
        createSSRApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue, Ziggy)
            .mount(el)

        initFlowbite()
        router.on('navigate', () => initFlowbite())
    },

    progress: { color: '#E62028', delay: 250 },
    defaults: {
        form: { recentlySuccessfulDuration: 5000 },
        prefetch: { cacheFor: '2m', hoverDelay: 150 },
        visitOptions: (href, options) => ({ preserveScroll: true, ...options }),
    },
})