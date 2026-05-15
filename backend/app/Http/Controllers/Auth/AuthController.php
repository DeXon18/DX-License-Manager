<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Auth\JwtService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cookie;

class AuthController extends Controller
{
    protected JwtService $jwtService;

    public function __construct(JwtService $jwtService)
    {
        $this->jwtService = $jwtService;
    }

    /**
     * Show the login form.
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Handle login request.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            \Illuminate\Support\Facades\DB::table('audit_logs')->insert([
                'user_id' => $user ? $user->id : null,
                'action' => 'login_failed',
                'description' => 'Intento de inicio de sesión fallido para: ' . $credentials['email'],
                'level' => 'warning',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'created_at' => now()
            ]);

            return back()->withErrors([
                'email' => 'Las credenciales no coinciden con nuestros registros.',
            ])->withInput();
        }

        if (!$user->is_active) {
            \Illuminate\Support\Facades\DB::table('audit_logs')->insert([
                'user_id' => $user->id,
                'action' => 'login_failed',
                'description' => 'Intento de acceso con cuenta desactivada: ' . $credentials['email'],
                'level' => 'warning',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'created_at' => now()
            ]);

            return back()->withErrors([
                'email' => 'Tu cuenta ha sido desactivada.',
            ])->withInput();
        }

        // Generate Token
        $token = $this->jwtService->generate([
            'sub' => $user->id,
            'name' => $user->name,
            'role' => $user->role->slug ?? 'viewer',
        ]);

        // Store in HttpOnly Cookie (60 minutes)
        $cookie = Cookie::make('jwt_token', $token, 60, null, null, true, true, false, 'Strict');

        return redirect()->intended('/')->withCookie($cookie);
    }

    /**
     * Handle logout.
     */
    public function logout(Request $request)
    {
        $token = $request->cookie('jwt_token') ?? $request->bearerToken();
        
        if ($token) {
            // Añadir a blacklist en Redis (ZSET con timestamp de expiración)
            // Usamos 60 min por defecto o calculamos del token
            $decoded = $this->jwtService->decode($token);
            $exp = $decoded['exp'] ?? (time() + 3600);
            
            \Illuminate\Support\Facades\Redis::zadd('jwt_blacklist', $exp, $token);
            
            // Registro de auditoría
            \Illuminate\Support\Facades\DB::table('audit_logs')->insert([
                'user_id' => auth()->id(),
                'action' => 'logout',
                'description' => 'Cierre de sesión manual.',
                'level' => 'info',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'created_at' => now()
            ]);
        }

        $cookie = Cookie::forget('jwt_token');
        return redirect('/login')->withCookie($cookie);
    }
}
