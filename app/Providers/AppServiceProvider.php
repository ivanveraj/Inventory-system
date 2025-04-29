<?php

namespace App\Providers;

use Filament\Support\Facades\FilamentView;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        FilamentView::registerRenderHook('panels::body.end', fn(): string => Blade::render("@vite('resources/js/app.js')"));
        FilamentView::registerRenderHook('panels::styles.after', fn(): string => Blade::render("@vite('resources/sass/app.scss')"));
        FilamentView::registerRenderHook('panels::styles.after', fn(): string => Blade::render("@vite('resources/css/app.css')"));
    }
}
