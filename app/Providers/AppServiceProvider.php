<?php

namespace App\Providers;

use Filament\Support\Facades\FilamentView;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Forms\Components\Toggle;

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
        // Configurar acciones de edición globalmente para todas las tablas de Filament
        EditAction::configureUsing(function (EditAction $action) {
            $action->hiddenLabel()->icon('heroicon-s-pencil')->tooltip('Editar');
        }, isImportant: true);

        DeleteAction::configureUsing(function (DeleteAction $action) {
            $action->hiddenLabel()->icon('heroicon-s-trash')->tooltip('Eliminar');
        }, isImportant: true);

        ToggleColumn::configureUsing(function (ToggleColumn $column) {
            $column->sortable()
                ->onIcon('heroicon-s-check')->offIcon('heroicon-s-x-mark')
                ->onColor('success')->offColor('danger')->alignCenter();
        }, isImportant: true);

        Toggle::configureUsing(function (Toggle $toggle) {
            $toggle
                ->onIcon('heroicon-s-check')->offIcon('heroicon-s-x-mark')
                ->onColor('success')->offColor('danger')->inline(false);
        }, isImportant: true);

        FilamentView::registerRenderHook('panels::body.end', fn(): string => Blade::render("@vite('resources/js/app.js')"));
        FilamentView::registerRenderHook('panels::styles.after', fn(): string => Blade::render("@vite('resources/sass/app.scss')"));
        FilamentView::registerRenderHook('panels::styles.after', fn(): string => Blade::render("@vite('resources/css/app.css')"));
    }
}
