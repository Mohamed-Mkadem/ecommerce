import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import vue from "@vitejs/plugin-vue";
import i18n from "laravel-vue-i18n/vite";
import { fileURLToPath, URL } from "node:url";

export default defineConfig({
    assetsInclude: ["**/*.JPG", "**/*.jpg"],
    plugins: [
        laravel({
            input: ["resources/js/app.js", "resources/css/app.css"],
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        i18n(),
    ],
    resolve: {
        alias: {
            "@": fileURLToPath(new URL("./resources", import.meta.url)),
        },
    },
});
