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
                ...refreshPaths,
                "app/Filament/**",
                "app/Forms/Components/**",
                "app/Livewire/**",
                "app/**",
                "app/Infolists/Components/**",
                "app/Providers/Filament/**",
                "app/Tables/Columns/**",
                "resources/Views/**",
                "resources/Views/Livewire/**",
                "resources/views/Livewire/**",
            ],
        }),
        tailwindcss(),
    ],
});
