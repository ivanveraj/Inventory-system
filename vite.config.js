import { defineConfig } from "vite";

import laravel, { refreshPaths } from "laravel-vite-plugin";
import tailwindcss from "@tailwindcss/vite";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/css/app.css",
                "resources/sass/app.scss",
                "resources/js/app.js",
                "resources/css/filament/admin/theme.css",
            ],
            refresh: [
                "app/**",
                "bootstrap/**",
                "config/**",
                "database/**",
                "public/**",
                "resources/**",
                "routes/**",
            ],
        }),
        tailwindcss(),
    ],
});
