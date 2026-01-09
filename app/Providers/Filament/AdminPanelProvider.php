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
use App\Filament\Pages\DayDetail;
use App\Filament\Pages\CashRegister;
use App\Filament\Pages\Sales;
use App\Filament\Resources\Roles\RoleResource;
use App\Filament\Resources\UserResource;
use App\Filament\Resources\ProductResource;
use Filament\Navigation\NavigationGroup;
use App\Filament\Resources\SettingResource;
use App\Filament\Resources\TableResource;
use App\Filament\Resources\CashMovementResource;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->login(Login::class)
            ->topNavigation()
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->maxContentWidth('full')
            ->navigation(function (NavigationBuilder $builder): NavigationBuilder {
                return $builder->items([
                    NavigationItem::make('Reportes')
                        ->url(fn(): string => Dashboard::getUrl())
                        ->icon('heroicon-o-chart-bar'),
                    NavigationItem::make('Caja')
                        ->url(fn() => CashRegister::getUrl())
                        ->icon('heroicon-o-banknotes'),
                    // ->visible(fn() => auth()->user()->hasRole(getAdministrativeRoles())),
                    // NavigationItem::make('Caja')
                    //     ->url(fn(): string => Till::getUrl())
                    //     ->icon('heroicon-o-banknotes')
                    //     ->visible(fn() => auth()->user()->hasRole(getAdministrativeRoles())),
                    // NavigationItem::make('Vende')
                    //     ->url(fn(): string => Sales::getUrl())
                    //     ->icon('heroicon-o-shopping-cart')
                    //     ->visible(fn() => auth()->user()->hasRole(getAdministrativeRoles())),
                    // NavigationItem::make('Agenda')
                    //     ->url(fn(): string => Calendar::getUrl())
                    //     ->icon('heroicon-o-calendar'),
                    // NavigationItem::make('Pagos')
                    //     ->url(fn() => ManagePayments::getUrl())
                    //     ->icon('heroicon-o-credit-card')
                    //     ->visible(fn() => !auth()->user()->hasRole(getAdministrativeRoles())),
                ])->groups([
                    NavigationGroup::make()->label('Ventas y Reportes')
                        ->items([
                            NavigationItem::make('Caja')
                                ->url(fn() => CashRegister::getUrl())
                                ->icon('heroicon-o-banknotes'),
                            NavigationItem::make('Ventas')
                                ->url(fn() => Sales::getUrl())
                                ->icon('heroicon-o-shopping-cart'),
                            NavigationItem::make('Detalle del Día')
                                ->url(fn() => DayDetail::getUrl())
                                ->icon('heroicon-o-calendar'),
                            NavigationItem::make('Movimientos de Caja')
                                ->url(fn() => CashMovementResource::getUrl())
                                ->icon('heroicon-o-arrow-path'),
                        ]),
                    NavigationGroup::make()->label('Negocio')
                        ->icon('heroicon-o-rectangle-stack')
                        ->items([
                            // NavigationItem::make('Mi Negocio')
                            //     ->url(fn() => ComboResource::getUrl())
                            //     ->visible(fn() => auth()->user()->hasRole(getAdministrativeRoles())),
                            //NavigationItem::make('Servicios')
                            //             ->url(fn() => ServiceResource::getUrl())
                            //             ->visible(fn() => auth()->user()->hasRole(getAdministrativeRoles())),
                            //         NavigationItem::make('Categorías')
                            //             ->url(fn() => ServicesCategoryResource::getUrl())
                            //             ->visible(fn() => auth()->user()->hasRole(getAdministrativeRoles())),
                            //         NavigationItem::make('Productos')
                            //             ->url(fn() => ProductResource::getUrl())
                            //             ->visible(fn() => auth()->user()->hasRole(getAdministrativeRoles())),
                            NavigationItem::make('Proveedores')
                                ->url(fn() => SupplierResource::getUrl()),
                            NavigationItem::make('Inventario')
                                ->url(fn() => ProductResource::getUrl()),
                            NavigationItem::make('Configuraciones')
                                ->url(fn() => SettingResource::getUrl()),
                            NavigationItem::make('Mesas')
                                ->url(fn() => TableResource::getUrl()),
                            //->visible(fn() => auth()->user()->hasRole(getAdministrativeRoles()))
                        ]),
                    NavigationGroup::make()->label('Usuarios')
                        ->icon('heroicon-o-user-group')
                        ->items([
                            NavigationItem::make('Usuarios')
                                ->url(fn() => UserResource::getUrl()),
                            //->visible(fn() => auth()->user()->hasRole(getAdministrativeRoles())),
                            // NavigationItem::make('Colaboradores')
                            //     ->url(fn() => StaffResource::getUrl()),
                            //     //->visible(fn() => auth()->user()->hasRole(getAdministrativeRoles())),
                            // NavigationItem::make('Clientes')
                            //     ->url(fn() => CustomerResource::getUrl()),
                            //     //->visible(fn() => auth()->user()->hasRole(getAdministrativeRoles())),
                            NavigationItem::make('Roles')
                                ->url(fn() => RoleResource::getUrl())
                            //->visible(fn() => auth()->user()->hasRole(getAdministrativeRoles())),
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
            ]);
    }
}
