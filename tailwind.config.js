import preset from './vendor/filament/support/tailwind.config.preset'

export default {
    darkMode: 'class',
    presets: [preset],
    theme: {
        colors: {
            transparent: 'transparent',
            black: "#000",
            success: "#00D619",
            danger: "#FCC2C2",
            warning: "#FEFEB4",
            info: "#CCF0F7",
            primary: '#00b4d8',
            secondary: '#7e106e',
            footer: '#2A3342'
        }
    },
    content: [
        './app/**/*.php',
        './resources/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
        './vendor/archilex/filament-filter-sets/**/*.php',
    ],
}
