<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->remember)) {
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'));
        }

        // Fallback: si aucun utilisateur n'existe avec cet email, tenter l'authentification contact pour un sondage fourni
        $userModel = \App\Models\User::where('email', $request->email)->first();
        if (!$userModel && Schema::hasTable('contacts')) {
            $surveyId = $request->input('survey') ?? $request->input('survey_id');
            if ($surveyId) {
                $email = strtolower($request->email);
                $preferredId = $request->session()->get('invite_contact_' . $surveyId);
                if ($preferredId) {
                    $pref = \App\Models\Contact::where('id', $preferredId)
                        ->when(Schema::hasColumn('contacts', 'survey_id'), fn($q) => $q->where('survey_id', $surveyId))
                        ->first();
                    if ($pref && $pref->email === $email && $pref->password && Hash::check($request->password, $pref->password)) {
                        $request->session()->put('contact_authenticated_' . $surveyId, $pref->id);
                        $request->session()->forget('invite_contact_' . $surveyId);
                        $survey = \App\Models\Survey::find($surveyId);
                        if ($survey) {
                            return redirect()->intended(route('surveys.respond', $survey));
                        }
                    }
                }

                $contacts = \App\Models\Contact::query()
                    ->when(Schema::hasColumn('contacts', 'survey_id'), fn($q) => $q->where('survey_id', $surveyId))
                    ->where('email', $email)
                    ->get();
                foreach ($contacts as $c) {
                    if ($c->password && Hash::check($request->password, $c->password)) {
                        $request->session()->put('contact_authenticated_' . $surveyId, $c->id);
                        $request->session()->forget('invite_contact_' . $surveyId);
                        $survey = \App\Models\Survey::find($surveyId);
                        if ($survey) {
                            return redirect()->intended(route('surveys.respond', $survey));
                        }
                    }
                }
            }
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
} 
