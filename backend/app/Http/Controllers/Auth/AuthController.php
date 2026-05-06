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
            return back()->withErrors([
                'email' => 'Las credenciales no coinciden con nuestros registros.',
            ])->withInput();
        }

        if (!$user->is_active) {
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
    public function logout()
    {
        $cookie = Cookie::forget('jwt_token');
        return redirect('/login')->withCookie($cookie);
    }
}
