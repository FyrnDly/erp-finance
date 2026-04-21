import { createInertiaApp } from '@inertiajs/vue3'
import createServer from '@inertiajs/vue3/server'
import { renderToString } from 'vue/server-renderer'
import { createSSRApp, h } from 'vue'
import { ZiggyVue } from 'ziggy-js'

createServer(page =>
    createInertiaApp({
        page,
        render: renderToString,
        title: (title) => `${title} - PT Anugerah Cahaya Chandra`,
        resolve: (name) => {
            const page = resolvePageComponent(
                `./Pages/${name}.vue`,
                import.meta.glob('./Pages/**/*.vue')
            );
            page.then((module) => {
                module.default.layout = module.default.layout || BaseLayout;
            });
            return page;
        },
        setup({ App, props, plugin }) {
            return createSSRApp({ render: () => h(App, props) })
                .use(plugin)
                .use(ZiggyVue, {
                    ...Ziggy,
                    location: new URL(props.initialPage.url, Ziggy.url),
                });
        },
    }),
)