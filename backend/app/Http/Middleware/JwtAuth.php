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
        $graceTime = \Illuminate\Support\Facades\Redis::zscore('jwt_blacklist', $token);
        
        if ($graceTime) {
            if ($graceTime < time()) {
                \Illuminate\Support\Facades\Log::warning("JWT: Sesión revocada por gracia expirada", [
                    'token_prefix' => substr($token, 0, 10),
                    'grace_time' => $graceTime,
                    'current_time' => time(),
                    'diff' => time() - $graceTime
                ]);
                return redirect('/login')->withErrors(['session' => 'Sesión revocada o expirada. Por favor, inicie sesión de nuevo.']);
            }
            $decoded = $this->jwtService->decode($token);
        } else {
            $decoded = $this->jwtService->decode($token);
        }

        if (!$decoded || !isset($decoded['sub'])) {
            return redirect('/login')->withErrors(['session' => 'Sesión inválida o expirada.']);
        }

        $user = User::find($decoded['sub']);

        if (!$user || !$user->is_active) {
            return redirect('/login')->withErrors(['session' => 'Usuario no encontrado o inactivo.']);
        }

        // Authenticate user
        Auth::login($user);

        // Track active user in Redis (15 min TTL)
        \Illuminate\Support\Facades\Redis::set("user:active:{$user->id}", now()->toIso8601String(), 'EX', 900);

        $response = $next($request);

        // SMART ROTATION: Solo rotar si el token tiene más de 5 minutos (evitar spam de tokens)
        $iat = $decoded['iat'] ?? 0;
        $shouldRotate = (time() - $iat) > 300; // 5 minutos

        if (!$graceTime && $shouldRotate) {
            $newToken = $this->jwtService->generate([
                'sub' => $user->id,
                'name' => $user->name,
                'role' => $decoded['role'] ?? 'viewer',
            ]);

            // Blacklistar el viejo con ventana de 120s (más permisivo)
            \Illuminate\Support\Facades\Redis::zadd('jwt_blacklist', time() + 120, $token);

            // Adjuntar nueva cookie (60 min ahora)
            $response->withCookie(cookie('jwt_token', $newToken, 60, null, null, true, true, false, 'Strict'));
        }

        return $response;
    }
}
