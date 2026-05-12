<?php

use App\Http\Controllers\Admin\ImportController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SystemController;
use App\Http\Controllers\ToolController;
use App\Http\Controllers\Tools\MoldexController;
use App\Http\Controllers\Tools\NXSuiteController;
use App\Http\Controllers\Admin\SystemDashboardController;
use App\Http\Controllers\Admin\SystemActionController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth.jwt'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/clientes', [ClientController::class, 'index'])->name('clients.index');
    Route::get('/clientes/{client}', [ClientController::class, 'show'])->name('clients.show');
    
    // Perfil de Usuario
    Route::get('/perfil', [ProfileController::class, 'index'])->name('profile.index');
    Route::post('/perfil', [ProfileController::class, 'update'])->name('profile.update');
    
    Route::get('/changelog', [SystemController::class, 'changelog'])->name('system.changelog');
    
    Route::get('/herramientas', [ToolController::class, 'index'])->name('tools.index');
    Route::get('/herramientas/nx-suite', [NXSuiteController::class, 'index'])->name('tools.nx-suite.index');
    Route::post('/herramientas/nx-suite', [NXSuiteController::class, 'process'])->name('tools.nx-suite.process');

    Route::get('/herramientas/star-ccm', [\App\Http\Controllers\Tools\StarCcmController::class, 'index'])->name('tools.star-ccm.index');
    Route::post('/herramientas/star-ccm', [\App\Http\Controllers\Tools\StarCcmController::class, 'process'])->name('tools.star-ccm.process');

    Route::get('/herramientas/heeds', [\App\Http\Controllers\Tools\HeedsController::class, 'index'])->name('tools.heeds.index');
    Route::post('/herramientas/heeds', [\App\Http\Controllers\Tools\HeedsController::class, 'process'])->name('tools.heeds.process');

    Route::get('/herramientas/moldex3d', [MoldexController::class, 'index'])->name('tools.moldex3d.index');
    Route::post('/herramientas/moldex3d', [MoldexController::class, 'process'])->name('tools.moldex3d.process');
    Route::get('/herramientas/moldex3d/recursos', [MoldexController::class, 'resources'])->name('tools.moldex3d.resources');

    Route::get('/herramientas/siemens/recursos', [NXSuiteController::class, 'resources'])->name('tools.siemens.resources');

    Route::get('/herramientas/cod', [\App\Http\Controllers\Tools\CodController::class, 'index'])->name('tools.cod.index');
    Route::post('/herramientas/cod/preview', [\App\Http\Controllers\Tools\CodController::class, 'preview'])->name('tools.cod.preview');
    Route::post('/herramientas/cod/parse-composite', [\App\Http\Controllers\Tools\CodController::class, 'parseComposite'])->name('tools.cod.parse-composite');
    Route::post('/herramientas/cod/store', [\App\Http\Controllers\Tools\CodController::class, 'store'])->name('tools.cod.store');
    Route::get('/herramientas/cod/download', [\App\Http\Controllers\Tools\CodController::class, 'download'])->name('tools.cod.download');
    Route::delete('/herramientas/cod/{uuid}', [\App\Http\Controllers\Tools\CodController::class, 'destroy'])->name('tools.cod.destroy');
    Route::post('/herramientas/cod/{uuid}/upload-signed', [\App\Http\Controllers\Tools\CodController::class, 'uploadSigned'])->name('tools.cod.upload-signed');
    Route::get('/herramientas/cod/download-signed', [\App\Http\Controllers\Tools\CodController::class, 'downloadSigned'])->name('tools.cod.download-signed');

    
    Route::post('/clientes/{client}/contactos', [ContactController::class, 'store'])->middleware('permission:technician')->name('contacts.store');
    Route::put('/clientes/{client}/contactos/{contact}', [ContactController::class, 'update'])->middleware('permission:technician')->name('contacts.update');
    Route::delete('/clientes/{client}/contactos/{contact}', [ContactController::class, 'destroy'])->middleware('permission:technician')->name('contacts.destroy');

    Route::delete('/inventory/daemon/{daemon}', [\App\Http\Controllers\InventoryController::class, 'destroyDaemon'])->middleware('permission:technician')->name('inventory.daemon.destroy');
    Route::delete('/inventory/product/{product}', [\App\Http\Controllers\InventoryController::class, 'destroyProduct'])->middleware('permission:technician')->name('inventory.product.destroy');

    Route::prefix('admin')->name('admin.')->middleware('permission:admin')->group(function () {
        Route::get('/import', [ImportController::class, 'index'])->name('import.index');
        Route::post('/import', [ImportController::class, 'store'])->name('import.store');
        
        Route::get('/import/logs', [\App\Http\Controllers\Admin\ImportLogController::class, 'index'])->name('import.logs.index');
        Route::get('/import/logs/{log}', [\App\Http\Controllers\Admin\ImportLogController::class, 'show'])->name('import.logs.show');
        Route::delete('/import/logs/{log}', [\App\Http\Controllers\Admin\ImportLogController::class, 'destroy'])->name('import.logs.destroy');

        Route::get('/normalization', [\App\Http\Controllers\Admin\NormalizationController::class, 'index'])->name('normalization.index');
        Route::post('/normalization/unify', [\App\Http\Controllers\Admin\NormalizationController::class, 'unify'])->name('normalization.unify');
        Route::post('/normalization/dismiss', [\App\Http\Controllers\Admin\NormalizationController::class, 'dismiss'])->name('normalization.dismiss');

        Route::get('/system', [SystemDashboardController::class, 'index'])->name('system.index');
        
        Route::prefix('system/actions')->name('system.')->group(function () {
            Route::post('/clear-cache', [SystemActionController::class, 'clearCache'])->name('clear-cache');
            Route::post('/restart-queues', [SystemActionController::class, 'restartQueues'])->name('restart-queues');
            Route::post('/backup-db', [SystemActionController::class, 'backupDatabase'])->name('backup-db');
            Route::post('/toggle-maintenance', [SystemActionController::class, 'toggleMaintenance'])->name('toggle-maintenance');
            Route::post('/test-telegram', [SystemActionController::class, 'testTelegram'])->name('test-telegram');
            Route::get('/download-backup/{filename}', [SystemActionController::class, 'downloadBackup'])->name('download-backup');
            Route::delete('/delete-backup/{filename}', [SystemActionController::class, 'deleteBackup'])->name('delete-backup');
        });

        // Gestión de Backups
        Route::prefix('backups')->name('backups.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\BackupController::class, 'index'])->name('index');
            Route::post('/run', [\App\Http\Controllers\Admin\BackupController::class, 'backup'])->name('run');
            Route::get('/download/{filename}', [\App\Http\Controllers\Admin\BackupController::class, 'download'])->name('download');
            Route::delete('/{filename}', [\App\Http\Controllers\Admin\BackupController::class, 'destroy'])->name('destroy');
            Route::post('/{filename}/restore', [\App\Http\Controllers\Admin\BackupController::class, 'restore'])->name('restore');
        });

        // Gestión de Usuarios
        Route::resource('/users', UserController::class)->names('users');
        Route::post('/users/{user}/toggle', [UserController::class, 'toggleStatus'])->name('users.toggle');

        Route::get('/audit', [\App\Http\Controllers\Admin\AuditLogController::class, 'index'])->name('audit.index');

        // Gestión del Repositorio de Licencias
        Route::get('/repository', [\App\Http\Controllers\Admin\LicenseRepositoryController::class, 'index'])->name('repository.index');
        Route::post('/repository/generate', [\App\Http\Controllers\Admin\LicenseRepositoryController::class, 'generate'])->name('repository.generate');
        Route::get('/repository/{archive}/download', [\App\Http\Controllers\Admin\LicenseRepositoryController::class, 'download'])->name('repository.download');
        Route::delete('/repository/{archive}', [\App\Http\Controllers\Admin\LicenseRepositoryController::class, 'destroy'])->name('repository.destroy');

        // Gestión de Recursos y Enlaces
        Route::middleware('permission:staff')->group(function () {
            Route::post('/resources', [\App\Http\Controllers\Admin\ResourceController::class, 'store'])->name('resources.store');
            Route::put('/resources/{resource}', [\App\Http\Controllers\Admin\ResourceController::class, 'update'])->name('resources.update');
            Route::delete('/resources/{resource}', [\App\Http\Controllers\Admin\ResourceController::class, 'destroy'])->name('resources.destroy');
        });
    });
});
