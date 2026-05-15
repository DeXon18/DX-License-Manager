<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect('/login');
        }

        // Hierarchy of roles (optional, but good for simple RBAC)
        // admin > technician > staff > viewer
        $roles = [
            'admin' => 4,
            'technician' => 3,
            'staff' => 2,
            'viewer' => 1,
        ];

        $userRoleLevel = $roles[$user->role->slug ?? 'viewer'] ?? 0;
        $requiredRoleLevel = $roles[$role] ?? 0;

        if ($userRoleLevel < $requiredRoleLevel) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'No tienes permisos para realizar esta acción.'], 403);
            }
            abort(403, 'No tienes permisos para acceder a esta sección.');
        }

        return $next($request);
    }
}
