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
use App\Http\Controllers\Admin\DatabaseMonitorController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth.jwt'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/clientes', [ClientController::class, 'index'])->name('clients.index');
    Route::get('/clientes/unificadas', [ClientController::class, 'unified'])->name('clients.unified');
    Route::get('/clientes/{client}', [ClientController::class, 'show'])->name('clients.show');
    
    // Chatbot de Asistencia IA Web (Fase 25)
    Route::post('/chatbot/query', [\App\Http\Controllers\Api\ChatbotController::class, 'query'])->middleware('throttle:30,1')->name('chatbot.query');
    
    // Perfil de Usuario
    Route::get('/perfil', [ProfileController::class, 'index'])->name('profile.index');
    Route::post('/perfil', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/perfil/tour-seen', [ProfileController::class, 'markTourSeen'])->name('profile.tour-seen');

    // Planificador de Renovaciones (Fase 14)
    Route::get('/planificador', [\App\Http\Controllers\RenewalPlannerController::class, 'index'])->name('renewal-planner.index');
    Route::post('/planificador', [\App\Http\Controllers\RenewalPlannerController::class, 'store'])->name('renewal-planner.store');
    Route::delete('/planificador', [\App\Http\Controllers\RenewalPlannerController::class, 'destroy'])->name('renewal-planner.destroy');
    Route::get('/planificador/download/{file}', [\App\Http\Controllers\RenewalPlannerController::class, 'downloadFile'])->name('renewal-planner.download-file');
    
    Route::get('/changelog', [SystemController::class, 'changelog'])->name('system.changelog');
    Route::view('/privacidad-ia', 'pages.ai-privacy')->name('pages.ai-privacy');
    
    // Contactar Soporte IT
    Route::get('/soporte', [\App\Http\Controllers\SupportController::class, 'index'])->name('support.contact');
    Route::post('/soporte', [\App\Http\Controllers\SupportController::class, 'send'])->name('support.send');
    
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

    Route::get('/herramientas/imputacion-horas', [\App\Http\Controllers\TimeTrackingController::class, 'index'])->name('tools.time-tracking.index');
    Route::get('/herramientas/imputacion-horas/search', [\App\Http\Controllers\TimeTrackingController::class, 'search'])->name('tools.time-tracking.search');

    
    Route::post('/clientes/{client}/contactos', [ContactController::class, 'store'])->middleware('role:admin|technician')->name('contacts.store');
    Route::put('/clientes/{client}/contactos/{contact}', [ContactController::class, 'update'])->middleware('role:admin|technician')->name('contacts.update');
    Route::delete('/clientes/{client}/contactos/{contact}', [ContactController::class, 'destroy'])->middleware('role:admin|technician')->name('contacts.destroy');

    Route::post('/clientes/{client}/enterprise-cloud-accounts', [\App\Http\Controllers\EnterpriseCloudAccountController::class, 'store'])->middleware('role:admin|technician')->name('enterprise-cloud-accounts.store');
    Route::put('/clientes/{client}/enterprise-cloud-accounts/{enterpriseCloudAccount}', [\App\Http\Controllers\EnterpriseCloudAccountController::class, 'update'])->middleware('role:admin|technician')->name('enterprise-cloud-accounts.update');
    Route::delete('/clientes/{client}/enterprise-cloud-accounts/{enterpriseCloudAccount}', [\App\Http\Controllers\EnterpriseCloudAccountController::class, 'destroy'])->middleware('role:admin|technician')->name('enterprise-cloud-accounts.destroy');

    Route::delete('/inventory/daemon/{daemon}', [\App\Http\Controllers\InventoryController::class, 'destroyDaemon'])->middleware('role:admin|technician')->name('inventory.daemon.destroy');
    Route::post('/inventory/daemon/{daemon}/toggle-status', [\App\Http\Controllers\InventoryController::class, 'toggleDaemonStatus'])->middleware('role:admin|technician')->name('inventory.daemon.toggle-status');
    
    Route::delete('/inventory/product/{product}', [\App\Http\Controllers\InventoryController::class, 'destroyProduct'])->middleware('role:admin|technician')->name('inventory.product.destroy');
    Route::post('/inventory/product/{product}/toggle-status', [\App\Http\Controllers\InventoryController::class, 'toggleProductStatus'])->middleware('role:admin|technician')->name('inventory.product.toggle-status');

    if (config('app.env') !== 'beta') {
        Route::prefix('reports')->name('reports.')->middleware('role:admin|technician')->group(function () {
            Route::get('/', [\App\Http\Controllers\ReportController::class, 'index'])->name('index');
            Route::get('/client/{client}/download', [\App\Http\Controllers\ReportController::class, 'downloadClientReport'])->name('client.download');
        });
    }

    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {
        Route::get('/import', [ImportController::class, 'index'])->name('import.index');
        Route::post('/import', [ImportController::class, 'store'])->name('import.store');
        
        Route::get('/import/status/{log}', [ImportController::class, 'status'])->name('import.status');
        Route::post('/import/cancel/{log}', [ImportController::class, 'cancel'])->name('import.cancel');
        Route::get('/import/active', [ImportController::class, 'active'])->name('import.active');
        Route::get('/import/logs', [\App\Http\Controllers\Admin\ImportLogController::class, 'index'])->name('import.logs.index');
        Route::get('/import/logs/{log}', [\App\Http\Controllers\Admin\ImportLogController::class, 'show'])->name('import.logs.show');
        Route::delete('/import/logs/{log}', [\App\Http\Controllers\Admin\ImportLogController::class, 'destroy'])->name('import.logs.destroy');

        Route::get('/normalization', [\App\Http\Controllers\Admin\NormalizationController::class, 'index'])->name('normalization.index');
        Route::post('/normalization/unify', [\App\Http\Controllers\Admin\NormalizationController::class, 'unify'])->name('normalization.unify');
        Route::post('/normalization/dismiss', [\App\Http\Controllers\Admin\NormalizationController::class, 'dismiss'])->name('normalization.dismiss');
        Route::post('/normalization/analyze-ai', [\App\Http\Controllers\Admin\NormalizationController::class, 'analyzeAi'])->name('normalization.analyze-ai');
        Route::post('/normalization/force-scan', [\App\Http\Controllers\Admin\NormalizationController::class, 'forceScan'])->name('normalization.force-scan');

        Route::get('/system', [SystemDashboardController::class, 'index'])->name('system.index');
        Route::get('/system/docker', [SystemDashboardController::class, 'docker'])->name('system.docker');
        Route::get('/system/ai-costs', [\App\Http\Controllers\Admin\AiAuditCostController::class, 'index'])->name('system.ai-costs');
        
        // Monitor de Procesamiento Asíncrono
        Route::get('/queue-monitor', [\App\Http\Controllers\Admin\QueueMonitorController::class, 'index'])->name('queue-monitor.index');
        Route::get('/queue-monitor/logs', [\App\Http\Controllers\Admin\QueueMonitorController::class, 'logs'])->name('queue-monitor.logs');

        // Visor de Base de Datos
        Route::get('/system/database', [DatabaseMonitorController::class, 'index'])->name('system.database');
        
        Route::prefix('system/actions')->name('system.')->group(function () {
            Route::post('/clear-cache', [SystemActionController::class, 'clearCache'])->name('clear-cache');
            Route::post('/restart-queues', [SystemActionController::class, 'restartQueues'])->name('restart-queues');
            Route::post('/backup-db', [SystemActionController::class, 'backupDatabase'])->name('backup-db');
            Route::post('/toggle-maintenance', [SystemActionController::class, 'toggleMaintenance'])->name('toggle-maintenance');
            Route::post('/test-telegram', [SystemActionController::class, 'testTelegram'])->name('test-telegram');
            Route::post('/send-weekly-alerts', [SystemActionController::class, 'sendWeeklyAlerts'])->name('send-weekly-alerts');
            Route::post('/restart-container', [SystemActionController::class, 'restartContainer'])->name('restart-container');
            Route::get('/download-backup/{filename}', [SystemActionController::class, 'downloadBackup'])->name('download-backup');
            Route::delete('/delete-backup/{filename}', [SystemActionController::class, 'deleteBackup'])->name('delete-backup');
        });

        // Routing de IA (OpenRouter Hub)
        Route::prefix('system/ai-routing')->name('system.ai-routing.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\AiModelController::class, 'index'])->name('index');
            Route::post('/models', [\App\Http\Controllers\Admin\AiModelController::class, 'storeModel'])->name('models.store');
            Route::post('/models/{aiModel}/toggle', [\App\Http\Controllers\Admin\AiModelController::class, 'toggleModel'])->name('models.toggle');
            Route::put('/routes/{task_name}', [\App\Http\Controllers\Admin\AiModelController::class, 'updateRoute'])->name('routes.update');
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
        Route::post('/audit/clear/activity', [\App\Http\Controllers\Admin\AuditLogController::class, 'clearActivity'])->name('audit.clear.activity');
        Route::post('/audit/clear/system', [\App\Http\Controllers\Admin\AuditLogController::class, 'clearSystem'])->name('audit.clear.system');
        Route::post('/audit/clear/email', [\App\Http\Controllers\Admin\AuditLogController::class, 'clearEmail'])->name('audit.clear.email');

        // Gestión del Repositorio de Licencias
        Route::get('/repository', [\App\Http\Controllers\Admin\LicenseRepositoryController::class, 'index'])->name('repository.index');
        Route::post('/repository/generate', [\App\Http\Controllers\Admin\LicenseRepositoryController::class, 'generate'])->name('repository.generate');
        Route::get('/repository/{archive}/download', [\App\Http\Controllers\Admin\LicenseRepositoryController::class, 'download'])->name('repository.download');
        Route::delete('/repository/{archive}', [\App\Http\Controllers\Admin\LicenseRepositoryController::class, 'destroy'])->name('repository.destroy');

        // Gestión de Recursos y Enlaces
        Route::middleware('role:admin|technician|staff')->group(function () {
            Route::post('/resources', [\App\Http\Controllers\Admin\ResourceController::class, 'store'])->name('resources.store');
            Route::put('/resources/{resource}', [\App\Http\Controllers\Admin\ResourceController::class, 'update'])->name('resources.update');
            Route::delete('/resources/{resource}', [\App\Http\Controllers\Admin\ResourceController::class, 'destroy'])->name('resources.destroy');
        });

        // Fase 13: Gestión de Alertas y Notificaciones
        Route::get('/alerts', [\App\Http\Controllers\Admin\AlertController::class, 'index'])->name('alerts.index');
        Route::post('/alerts/update', [\App\Http\Controllers\Admin\AlertController::class, 'update'])->name('alerts.update');
        Route::post('/alerts/toggle', [\App\Http\Controllers\Admin\AlertController::class, 'toggle'])->name('alerts.toggle');
    });
});
