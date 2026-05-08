<?php

use App\Http\Controllers\Admin\ImportController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ToolController;
use App\Http\Controllers\Tools\NXSuiteController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth.jwt'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/clientes', [ClientController::class, 'index'])->name('clients.index');
    Route::get('/clientes/{client}', [ClientController::class, 'show'])->name('clients.show');
    
    Route::get('/herramientas', [ToolController::class, 'index'])->name('tools.index');
    Route::get('/herramientas/nx-suite', [NXSuiteController::class, 'index'])->name('tools.nx-suite.index');
    Route::post('/herramientas/nx-suite', [NXSuiteController::class, 'process'])->name('tools.nx-suite.process');

    Route::get('/herramientas/star-ccm', [\App\Http\Controllers\Tools\StarCcmController::class, 'index'])->name('tools.star-ccm.index');
    Route::post('/herramientas/star-ccm', [\App\Http\Controllers\Tools\StarCcmController::class, 'process'])->name('tools.star-ccm.process');

    
    Route::post('/clientes/{client}/contactos', [ContactController::class, 'store'])->name('contacts.store');
    Route::put('/clientes/{client}/contactos/{contact}', [ContactController::class, 'update'])->name('contacts.update');
    Route::delete('/clientes/{client}/contactos/{contact}', [ContactController::class, 'destroy'])->name('contacts.destroy');

    Route::delete('/inventory/daemon/{daemon}', [\App\Http\Controllers\InventoryController::class, 'destroyDaemon'])->name('inventory.daemon.destroy');
    Route::delete('/inventory/product/{product}', [\App\Http\Controllers\InventoryController::class, 'destroyProduct'])->name('inventory.product.destroy');

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/import', [ImportController::class, 'index'])->name('import.index');
        Route::post('/import', [ImportController::class, 'store'])->name('import.store');
    });
});
