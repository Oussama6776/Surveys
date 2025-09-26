<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = auth()->user();
        
        // Vérifier les permissions
        if (!$user->hasAnyRole(['super_admin', 'admin']) && !$user->hasPermission('users.read')) {
            abort(403, 'Vous n\'avez pas la permission d\'accéder à cette page.');
        }
        
        $users = User::with('roles')->paginate(15);
        return view('users.index', compact('users'));
    }

    public function show(User $user)
    {
        $user->load('roles', 'surveys');
        return view('users.show', compact('user'));
    }

    public function create()
    {
        $user = auth()->user();
        
        // Vérifier les permissions
        if (!$user->hasAnyRole(['super_admin', 'admin']) && !$user->hasPermission('users.create')) {
            abort(403, 'Vous n\'avez pas la permission de créer des utilisateurs.');
        }
        
        $roles = Role::all();
        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        
        // Vérifier les permissions
        if (!$user->hasAnyRole(['super_admin', 'admin']) && !$user->hasPermission('users.create')) {
            abort(403, 'Vous n\'avez pas la permission de créer des utilisateurs.');
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'roles' => 'array',
            'roles.*' => 'exists:roles,id'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'email_verified_at' => now(),
        ]);

        if ($request->has('roles')) {
            $user->roles()->attach($request->roles);
        }

        return redirect()->route('users.index')
            ->with('success', 'Utilisateur créé avec succès.');
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        $userRoles = $user->roles->pluck('id')->toArray();
        return view('users.edit', compact('user', 'roles', 'userRoles'));
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
                Rule::unique('users')->ignore($user->id)
            ],
            'password' => 'nullable|string|min:8|confirmed',
            'roles' => 'array',
            'roles.*' => 'exists:roles,id'
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        if ($request->has('roles')) {
            $user->roles()->sync($request->roles);
        }

        return redirect()->route('users.index')
            ->with('success', 'Utilisateur mis à jour avec succès.');
    }

    public function destroy(User $user)
    {
        // Empêcher la suppression de l'utilisateur connecté
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')
                ->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'Utilisateur supprimé avec succès.');
    }

    public function assignRole(Request $request, User $user)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id'
        ]);

        $role = Role::find($request->role_id);
        
        if (!$user->hasRole($role->name)) {
            $user->assignRole($role->name);
            return response()->json([
                'success' => true,
                'message' => "Rôle '{$role->display_name}' attribué avec succès."
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => "L'utilisateur a déjà ce rôle."
        ]);
    }

    public function removeRole(User $user, Role $role)
    {
        if ($user->hasRole($role->name)) {
            $user->removeRole($role->name);
            return response()->json([
                'success' => true,
                'message' => "Rôle '{$role->display_name}' retiré avec succès."
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => "L'utilisateur n'a pas ce rôle."
        ]);
    }

    public function profile()
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login');
        }
        
        $user->load('roles', 'surveys');
        return view('users.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        
        if (!$user) {
            return redirect()->route('login');
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id)
            ],
            'current_password' => 'required_with:password',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        // Vérifier le mot de passe actuel si un nouveau est fourni
        if ($request->filled('password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Le mot de passe actuel est incorrect.']);
            }
        }

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        return redirect()->route('profile')
            ->with('success', 'Profil mis à jour avec succès.');
    }

    public function getUserRoles(User $user)
    {
        $roles = Role::all();
        $userRoles = $user->roles->pluck('id')->toArray();
        
        $rolesData = $roles->map(function($role) use ($userRoles) {
            return [
                'id' => $role->id,
                'name' => $role->name,
                'display_name' => $role->display_name,
                'description' => $role->description,
                'assigned' => in_array($role->id, $userRoles)
            ];
        });

        return response()->json([
            'success' => true,
            'roles' => $rolesData
        ]);
    }

    public function updateUserRoles(Request $request, User $user)
    {
        $request->validate([
            'roles' => 'array',
            'roles.*' => 'exists:roles,id'
        ]);

        $user->roles()->sync($request->roles ?? []);

        return response()->json([
            'success' => true,
            'message' => 'Rôles mis à jour avec succès.'
        ]);
    }

    /**
     * Reset user password
     */
    public function resetPassword(Request $request, User $user)
    {
        $request->validate([
            'password' => 'required|string|min:8'
        ]);

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Mot de passe réinitialisé avec succès.'
        ]);
    }
}
