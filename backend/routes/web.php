<?php

use App\Http\Controllers\Admin\ImportController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth.jwt'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/clientes', [ClientController::class, 'index'])->name('clients.index');
    Route::get('/clientes/{client}', [ClientController::class, 'show'])->name('clients.show');
    
    Route::post('/clientes/{client}/contactos', [ContactController::class, 'store'])->name('contacts.store');
    Route::put('/clientes/{client}/contactos/{contact}', [ContactController::class, 'update'])->name('contacts.update');
    Route::delete('/clientes/{client}/contactos/{contact}', [ContactController::class, 'destroy'])->name('contacts.destroy');

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/import', [ImportController::class, 'index'])->name('import.index');
        Route::post('/import', [ImportController::class, 'store'])->name('import.store');
    });
});
