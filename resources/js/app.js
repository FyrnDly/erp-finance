import '../css/app.css'
import { createInertiaApp, router } from '@inertiajs/vue3'
import BaseLayout from './Components/Layouts/Base.vue'
import { ZiggyVue } from 'ziggy-js'
import { Ziggy } from './ziggy'
import { initFlowbite } from 'flowbite'

createInertiaApp({
    layout: () => BaseLayout,
    withApp(app, { ssr }) {
        app.use(ZiggyVue, Ziggy)

        if (!ssr) {
            initFlowbite()
            router.on('navigate', () => {
                initFlowbite()
            })
        }
    },

    progress: { color: '#E62028', delay: 250 },

    defaults: {
        form: { recentlySuccessfulDuration: 5000 },
        prefetch: { cacheFor: '2m', hoverDelay: 150 },
        visitOptions: (href, options) => ({ preserveScroll: true, ...options }),
    },
})