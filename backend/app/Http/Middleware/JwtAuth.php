<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Services\Auth\JwtService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class JwtAuth
{
    protected JwtService $jwtService;

    public function __construct(JwtService $jwtService)
    {
        $this->jwtService = $jwtService;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->cookie('jwt_token') ?? $request->bearerToken();

        if (!$token) {
            return redirect('/login');
        }

        // Check if token is blacklisted
        if (\Illuminate\Support\Facades\Redis::zscore('jwt_blacklist', $token)) {
            return redirect('/login')->withErrors(['session' => 'Sesión revocada. Por favor, inicie sesión de nuevo.']);
        }

        $decoded = $this->jwtService->decode($token);

        if (!$decoded || !isset($decoded['sub'])) {
            return redirect('/login')->withErrors(['session' => 'Sesión inválida o expirada.']);
        }

        $user = User::find($decoded['sub']);

        if (!$user || !$user->is_active) {
            return redirect('/login')->withErrors(['session' => 'Usuario no encontrado o inactivo.']);
        }

        // Authenticate user for the current request
        Auth::login($user);

        // Track active user in Redis (15 min TTL)
        \Illuminate\Support\Facades\Redis::set("user:active:{$user->id}", now()->toIso8601String(), 'EX', 900);

        return $next($request);
    }
}
