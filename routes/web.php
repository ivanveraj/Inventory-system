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

// Ruta para imprimir recibos
Route::get('/receipt/print', function () {
    $data = session('receipt_data', []);
    if (empty($data)) {
        abort(404, 'Recibo no encontrado');
    }
    session()->forget('receipt_data');
    return view('receipts.print', compact('data'));
})->name('receipt.print')->middleware('auth');
