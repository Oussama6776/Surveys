<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // Assigner un rôle par défaut au nouvel utilisateur
        // Par défaut: 'survey_creator' (pour pouvoir créer des sondages et questions)
        $defaultRole = Role::where('name', 'survey_creator')->first()
            ?: Role::where('name', 'client')->first();
        if ($defaultRole) {
            $user->assignRole($defaultRole);
        }

        Auth::login($user);

        return redirect(route('dashboard'));
    }
} 
