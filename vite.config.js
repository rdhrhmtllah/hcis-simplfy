import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import path from "path";
import vue from "@vitejs/plugin-vue";
export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/css/app.css",
                "resources/css/shell.css",
                "resources/js/app.js",
                "resources/js/shell.js",
            ],
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
    ],
    resolve: {
        alias: {
            // TAMBAHKAN BARIS INI:
            // Memaksa menggunakan versi Vue yang memiliki compiler
            vue: "vue/dist/vue.esm-bundler.js",

            // Alias lain yang mungkin sudah ada
            "@": path.resolve(__dirname, "./src"),
        },
    },
});
