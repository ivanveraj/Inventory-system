import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/views/filament/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    // Usar important para que las clases de Tailwind tengan prioridad
    // cuando se carguen en páginas específicas de Filament
    important: true,
    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },
    plugins: [],
};
