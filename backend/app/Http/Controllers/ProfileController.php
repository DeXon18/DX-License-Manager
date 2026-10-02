<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        return view('profile.index', compact('user'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'current_password' => 'required_with:new_password',
            'new_password' => 'nullable|string|min:8|confirmed',
        ]);

        // Verificar contraseña actual si se intenta cambiar
        if ($request->filled('new_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'La contraseña actual no es correcta.']);
            }
            $user->password = Hash::make($request->new_password);

            // Invalida todos los tokens emitidos antes de este momento en Redis (AUTH-01)
            $now = time();
            \Illuminate\Support\Facades\Redis::set("user:pwd_changed:{$user->id}", $now, 'EX', 28800);

            // Generar nuevo token inmediatamente para la sesión actual
            $jwtService = app(\App\Services\Auth\JwtService::class);
            $freshToken = $jwtService->generate([
                'sub' => $user->id,
                'name' => $user->name,
                'role' => $user->roles->first()->name ?? 'viewer',
            ], 480);
            $cookie = cookie('jwt_token', $freshToken, 480, null, null, true, true, false, 'Strict');

            $user->name = $request->name;
            $user->email = $request->email;
            $user->save();

            return back()->with('success', 'Perfil y contraseña actualizados correctamente. Otras sesiones han sido revocadas.')->withCookie($cookie);
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();

        return back()->with('success', 'Perfil actualizado correctamente.');
    }

    public function markTourSeen(Request $request)
    {
        $user = auth()->user();
        $user->has_seen_tour = true;
        $user->save();

        return response()->json(['success' => true]);
    }
}
