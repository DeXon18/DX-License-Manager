<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\File;

class SelectiveMaintenance
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $maintenanceFile = storage_path('framework/maintenance_selective');

        if (File::exists($maintenanceFile)) {
            $user = $request->user();

            // Si es admin, permitimos el paso pero marcamos una variable global para el aviso
            if ($user && $user->hasRole('admin')) {
                view()->share('maintenance_active', true);
                return $next($request);
            }

            // Si es una petición de login o estática, permitir para que el admin pueda entrar
            if ($request->is('login') || $request->is('api/auth/*') || $request->is('assets/*')) {
                return $next($request);
            }

            // Para el resto, abortamos con 503
            abort(503);
        }

        return $next($request);
    }
}
