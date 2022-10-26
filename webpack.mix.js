const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */
 
 mix.js('resources/js/app.js', 'public/js')
     .postCss('resources/css/app.css', 'public/css', [
         require('tailwindcss'),
     ]);
/*  mix.styles([
    'resources/css/revslider.css',
    'resources/css/overworld.css',
    'resources/css/overworld-modules.css',
    'resources/css/overworld-icons-dripicons.css',
    'resources/css/overworld-icons-elegant-icons.css',
    'resources/css/overworld-icons-font-awesone.css',
    'resources/css/overworld-icons-ion-icons.css',
    'resources/css/overworld-icons-linea-icons.css',
    'resources/css/overworld-icons-linear-icons.css',
    'resources/css/overworld-icons-simple-line.css',
    'resources/css/mediaelementplayers.css',
    'resources/css/mediaelement-style.css',
    'resources/css/overworld-modules-responsive.css',
    'resources/css/owl.carousel.min.css',
    'resources/css/cropper.min.css',
    'resources/css/jquery-ui.min.css',
    'resources/css/bootstrap.css',
    'resources/css/app.css'
], 'public/css/app.css'); */







if (mix.inProduction()) {
    mix.version();
}
