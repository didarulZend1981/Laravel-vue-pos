import { createApp, h } from "vue";
import { createInertiaApp, router } from "@inertiajs/vue3";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { ZiggyVue } from "../../vendor/tightenco/ziggy";

// Plugins
import NProgress from "nprogress";
import Toastify from "toastify-js";
import "toastify-js/src/toastify.css";

// Components
import Vue3EasyDataTable from "vue3-easy-data-table";
import "vue3-easy-data-table/dist/style.css";

// Configuration
const progressConfig = {
    delay: 250,
    color: "#ff4500",
    includeCSS: false,
    showSpinner: false,
};

// Initialize Inertia App
createInertiaApp({
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob("./Pages/**/*.vue")
        ),

    setup({ el, App, props, plugin }) {
        const app = createApp({
            render: () => h(App, props),
        });

        // Register plugins
        app.use(plugin).use(ZiggyVue).use(Toastify);

        // Register global components
        app.component("EasyDataTable", Vue3EasyDataTable);

        // Mount the app
        app.mount(el);

        return app;
    },

    progress: progressConfig,
});

// Router event handlers
const setupRouterEvents = () => {
    router.on("start", () => NProgress.start());

    router.on("progress", (event) => {
        if (event.detail.progress?.percentage) {
            NProgress.set(event.detail.progress.percentage / 100);
        }
    });

    router.on("finish", () => NProgress.done());
};

// Initialize router events
setupRouterEvents();
