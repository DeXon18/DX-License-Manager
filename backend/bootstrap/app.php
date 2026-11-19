<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            \App\Http\Middleware\SelectiveMaintenance::class,
        ]);
        
        $middleware->alias([
            'auth.jwt' => \App\Http\Middleware\JwtAuth::class,
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);
    })
    ->withSchedule(function (\Illuminate\Console\Scheduling\Schedule $schedule) {
        $schedule->command('licenses:archive-weekly')->mondays()->at('07:00');
        $schedule->command('auth:jwt-cleanup')->daily();
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\HttpException $e, \Illuminate\Http\Request $request) {
            if ($e->getStatusCode() !== 419) {
                return null;
            }

            if ($request->expectsJson()) {
                return response()->json(['message' => 'Tu sesión ha expirado. Recarga la página.'], 419);
            }

            if ($request->hasSession()) {
                $request->session()->regenerateToken();
            }

            return redirect()->route('login')
                ->withInput($request->except('password'))
                ->withErrors(['email' => 'La sesión del formulario había expirado. Por favor, introduce tus credenciales de nuevo.']);
        });
    })->create();
