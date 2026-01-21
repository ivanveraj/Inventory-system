<?php

namespace App\Providers\Filament;

use App\Filament\Resources\Suppliers\SupplierResource;
use App\Filament\Auth\Login;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Actions\Action;
use App\Filament\Pages\Profile;
use Filament\Navigation\NavigationBuilder;
use Filament\Navigation\NavigationItem;
use App\Filament\Pages\Dashboard;
use App\Filament\Pages\CashRegister;
use App\Filament\Pages\Sales;
use App\Filament\Resources\Roles\RoleResource;
use App\Filament\Resources\UserResource;
use App\Filament\Resources\Products\ProductResource;
use Filament\Navigation\NavigationGroup;
use App\Filament\Resources\Settings\SettingResource;
use App\Filament\Resources\TableResource;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Blade;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel->id('admin')
            ->default()
            ->path('admin')
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->login(Login::class)
            ->maxContentWidth('full')
            ->topNavigation()
            ->databaseNotifications()
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->navigation(function (NavigationBuilder $builder): NavigationBuilder {
                return $builder->items([
                    NavigationItem::make('Reportes')
                        ->url(fn(): string => Dashboard::getUrl())
                        ->icon('heroicon-o-chart-bar'),
                    NavigationItem::make('Caja')
                        ->url(fn() => CashRegister::getUrl())
                        ->icon('heroicon-o-banknotes'),
                    NavigationItem::make('Ventas')
                        ->url(fn() => Sales::getUrl())
                        ->icon('heroicon-o-shopping-cart'),
                ])->groups([
                    NavigationGroup::make()->label('Negocio')
                        ->icon('heroicon-o-rectangle-stack')
                        ->items([
                            NavigationItem::make('Proveedores')
                                ->url(fn() => SupplierResource::getUrl()),
                            NavigationItem::make('Inventario')
                                ->url(fn() => ProductResource::getUrl()),
                            NavigationItem::make('Configuraciones')
                                ->url(fn() => SettingResource::getUrl()),
                            NavigationItem::make('Mesas')
                                ->url(fn() => TableResource::getUrl()),
                        ]),
                    NavigationGroup::make()->label('Usuarios')
                        ->icon('heroicon-o-user-group')
                        ->items([
                            NavigationItem::make('Usuarios')
                                ->url(fn() => UserResource::getUrl()),
                            NavigationItem::make('Roles')
                                ->url(fn() => RoleResource::getUrl())
                        ]),
                ]);
            })
            ->userMenuItems([
                'profile' => fn(Action $action) => $action->label('Perfil')
                    ->url(Profile::getUrl()),
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->renderHook(
                PanelsRenderHook::BODY_END,
                fn () => Blade::render('
                    <script>
                        document.addEventListener("livewire:initialized", () => {
                            Livewire.on("printReceipt", (data) => {
                                window.open(data.url, "_blank", "width=300,height=600");
                            });
                        });
                    </script>
                ')
            );
    }
}
