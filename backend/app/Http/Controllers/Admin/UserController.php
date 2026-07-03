<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

use App\Notifications\NewUserCredentials;

use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('roles');

        if ($request->filled('role')) {
            $query->whereHas('roles', function ($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $users = $query->orderBy('name')->paginate(20);
        $roles = Role::all();

        return view('admin.users.index', compact('users', 'roles'));
    }

    public function create()
    {
        $roles = Role::all();
        $permissions = Permission::orderBy('name')->get();
        return view('admin.users.create', compact('roles', 'permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => $request->filled('password') ? 'required|string|min:8|confirmed' : 'nullable',
            'role_id' => 'required|exists:roles,id',
            'permissions' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        $password = $request->filled('password') 
            ? $request->password 
            : Str::random(12);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($password),
            'is_active' => $request->has('is_active'),
        ]);

        $user->assignRole($request->role_id);
        $user->syncPermissions($request->input('permissions', []));

        // Enviar notificación con credenciales
        $mailSent = true;
        try {
            $user->notify(new NewUserCredentials($password));
        } catch (\Exception $e) {
            $mailSent = false;
            \Log::error("Fallo al enviar correo de bienvenida: " . $e->getMessage());
        }

        $message = 'Usuario creado correctamente.';
        if (!$mailSent) {
            $message .= ' (Atención: No se pudo enviar el email de bienvenida. Revisa la configuración SMTP).';
        }

        return redirect()->route('admin.users.index')->with($mailSent ? 'success' : 'warning', $message);
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        $permissions = Permission::orderBy('name')->get();
        return view('admin.users.edit', compact('user', 'roles', 'permissions'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'password' => 'nullable|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
            'permissions' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'is_active' => $request->has('is_active'),
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);
        $user->syncRoles([$request->role_id]);
        $user->syncPermissions($request->input('permissions', []));

        return redirect()->route('admin.users.index')->with('success', 'Usuario actualizado correctamente.');
    }

    public function toggleStatus(User $user)
    {
        // No permitir desactivarse a sí mismo
        if ($user->id === auth()->id()) {
            return response()->json(['error' => 'No puedes desactivar tu propia cuenta.'], 403);
        }

        $user->is_active = !$user->is_active;
        $user->save();

        return response()->json([
            'success' => true,
            'is_active' => $user->is_active,
            'message' => 'Estado actualizado correctamente.'
        ]);
    }

    public function destroy(User $user)
    {
        // No permitir eliminarse a sí mismo
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')->with('error', 'No puedes eliminar tu propia cuenta.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Usuario eliminado correctamente.');
    }
}
