<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Product\ProductController;
use App\Http\Controllers\Rol\RolController;
use App\Http\Controllers\Sale\SaleController;
use App\Http\Controllers\Settings\SettingsController;
use App\Http\Controllers\Table\TableController;
use App\Http\Controllers\User\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/* Route::get('/', function () {
    return view('layouts.admin.base');
}); */

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {

    Route::get('/', [DashboardController::class, 'dashboard'])->name('dashboard');
        Route::get('create', [UserController::class, 'create'])->name('user.create')->middleware('ProtectRoutes:2');
        Route::post('store', [UserController::class, 'store'])->name('user.store')->middleware('ProtectRoutes:2');
        Route::get('show/{id?}', [UserController::class, 'show'])->name('user.show')->middleware('ProtectRoutes:2');
        Route::post('archive', [UserController::class, 'archive'])->name('user.archive')->middleware('ProtectRoutes:2');
        Route::get('assign/{id_user?}', [UserController::class, 'assignRol'])->name('user.assign_rol')->middleware('ProtectRoutes:2');
        Route::post('changeRol', [UserController::class, 'changeRol'])->name('user.change_rol')->middleware('ProtectRoutes:2');
    });

    Route::group(['prefix' => 'rol'], function () {
        Route::get('list', [RolController::class, 'datatable'])->name('roles.datatables')->middleware('ProtectRoutes:3');
        Route::get('create', [RolController::class, 'create'])->name('rol.create')->middleware('ProtectRoutes:3');
        Route::post('store', [RolController::class, 'store'])->name('rol.store')->middleware('ProtectRoutes:3');
        Route::get('show/{rol_id?}', [RolController::class, 'show'])->name('rol.show')->middleware('ProtectRoutes:3');
        Route::post('update', [RolController::class, 'update'])->name('rol.update')->middleware('ProtectRoutes:3');
        Route::post('archive', [RolController::class, 'archive'])->name('rol.archive')->middleware('ProtectRoutes:3');
        Route::get('permissionRol/{rol_id?}', [RolController::class, 'managePermissionsRol'])->name('rol.managePermissionsRol')->middleware('ProtectRoutes:3');
        Route::post('updatePermissionRol', [RolController::class, 'updatePermissionRol'])->name('rol.permissionRol')->middleware('ProtectRoutes:3');
        Route::get('/', [RolController::class, 'index'])->name('roles.index')->middleware('ProtectRoutes:3');
    });

    Route::group(['prefix' => 'product'], function () {
        Route::get('create', [ProductController::class, 'create'])->name('product.create')->middleware('ProtectRoutes:4');
        Route::post('store', [ProductController::class, 'store'])->name('product.store')->middleware('ProtectRoutes:4');
        Route::get('show/{id?}', [ProductController::class, 'show'])->name('product.show')->middleware('ProtectRoutes:4');
        Route::post('update', [ProductController::class, 'update'])->name('product.update')->middleware('ProtectRoutes:4');
        Route::post('archive', [ProductController::class, 'archive'])->name('product.archive')->middleware('ProtectRoutes:4');
        Route::get('addStock/{id?}', [ProductController::class, 'addStock'])->name('product.addStock')->middleware('ProtectRoutes:4');
        Route::post('saveStock', [ProductController::class, 'saveStock'])->name('product.saveStock')->middleware('ProtectRoutes:4');

        Route::get('deleteStock/{id?}', [ProductController::class, 'deleteStock'])->name('product.deleteStock')->middleware('ProtectRoutes:4');
        Route::post('saveDeleteStock', [ProductController::class, 'saveDeleteStock'])->name('product.saveDeleteStock')->middleware('ProtectRoutes:4');

        Route::get('list', [ProductController::class, 'list'])->name('products.list')->middleware('ProtectRoutes:4');
        Route::get('/', [ProductController::class, 'index'])->name('products.index')->middleware('ProtectRoutes:4');
    });

    Route::group(['prefix' => 'table'], function () {
        Route::get('create', [TableController::class, 'create'])->name('table.create')->middleware('ProtectRoutes:5');
        Route::post('store', [TableController::class, 'store'])->name('table.store')->middleware('ProtectRoutes:5');
        Route::get('show/{id?}', [TableController::class, 'show'])->name('table.show')->middleware('ProtectRoutes:5');
        Route::post('update', [TableController::class, 'update'])->name('table.update')->middleware('ProtectRoutes:5');
        Route::post('archive', [TableController::class, 'archive'])->name('table.archive')->middleware('ProtectRoutes:5');
        Route::get('list', [TableController::class, 'list'])->name('tables.list')->middleware('ProtectRoutes:5');
        Route::get('/', [TableController::class, 'index'])->name('tables.index')->middleware('ProtectRoutes:5');
    });

    Route::group(['prefix' => 'sales'], function () {
        Route::post('startTime', [SaleController::class, 'startTime'])->name('sale.startTime');
        Route::post('newSaleGeneral', [SaleController::class, 'newSaleGeneral'])->name('sale.newSaleGeneral');
        Route::post('addProduct', [SaleController::class, 'addProduct'])->name('sale.addProduct');
        Route::post('deleteExtra', [SaleController::class, 'deleteExtra'])->name('sale.deleteExtra');
        Route::post('accountPayment', [SaleController::class, 'accountPayment'])->name('sale.accountPayment');
        Route::get('initDay', [SaleController::class, 'initDay'])->name('sale.initDay');
        Route::get('finishDay', [SaleController::class, 'finishDay'])->name('sale.finishDay');
        Route::get('changeNameClient', [SaleController::class, 'changeNameClient'])->name('sale.changeNameClient');
        Route::get('changeAmountExtra', [SaleController::class, 'changeAmountExtra'])->name('sale.changeAmountExtra');
        Route::get('detail/{sale_id?}', [SaleController::class, 'detail'])->name('sale.detail');
        Route::get('products', [SaleController::class, 'products'])->name('sale.products');
        Route::get('dataGeneral', [SaleController::class, 'dataGeneral'])->name('sale.dataGeneral');
        Route::get('generalSale', [SaleController::class, 'generalSale'])->name('sales.generalSale');
        Route::get('tablesSales', [SaleController::class, 'tablesSales'])->name('sales.tablesSales');
        Route::get('histoyDetail/{history_id?}', [SaleController::class, 'histoyDetail'])->name('sale.histoyDetail');

        Route::get('plusExtra', [SaleController::class, 'plusExtra'])->name('sale.plusExtra');
        Route::get('minExtra', [SaleController::class, 'minExtra'])->name('sale.minExtra');

        Route::get('/', [SaleController::class, 'index'])->name('sales.index');
    });

    Route::group(['prefix' => 'history'], function () {
        Route::get('detail_sales', [SaleController::class, 'detail_sales'])->name('sales.detail_sale');
        Route::get('inventoryDiscount', [ProductController::class, 'inventoryDiscount'])->name('history.inventoryDiscount');
    });


    Route::group(['prefix' => 'settings'], function () {
        Route::get('/', [SettingsController::class, 'index'])->name('settings.index')->middleware('ProtectRoutes:1');
        Route::post('general', [SettingsController::class, 'general'])->name('settings.general')->middleware('ProtectRoutes:1');
    });
});
