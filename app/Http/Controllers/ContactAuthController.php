<?php

namespace App\Http\Controllers;

use App\Models\Survey;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ContactAuthController extends Controller
{
    public function showLoginForm(Survey $survey)
    {
        // Pour les sondages publics, pas besoin de connexion contact
        if ($survey->is_public) {
            return redirect()->route('surveys.respond', $survey);
        }
        return view('contacts.login', compact('survey'));
    }

    public function login(Request $request, Survey $survey)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $email = strtolower($request->email);
        $preferredContactId = $request->session()->get('invite_contact_' . $survey->id);

        // 1) Si l'utilisateur vient d'un lien d'invitation, essayer d'abord ce contact précis
        if ($preferredContactId) {
            $preferred = Contact::where('id', $preferredContactId)
                ->where('survey_id', $survey->id)
                ->first();
            if ($preferred && $preferred->email === $email && $preferred->password && Hash::check($request->password, $preferred->password)) {
                $request->session()->put('contact_authenticated_' . $survey->id, $preferred->id);
                // Nettoyer l'indicateur d'invitation après succès
                $request->session()->forget('invite_contact_' . $survey->id);
                return redirect()->route('surveys.respond', $survey)
                    ->with('success', 'Connecté en tant que contact.');
            }
        }

        // 2) Sinon, chercher parmi toutes les entrées (multi-groupes) pour cet email dans ce sondage
        $matches = Contact::where('survey_id', $survey->id)
            ->where('email', $email)
            ->get();
        foreach ($matches as $c) {
            if ($c->password && Hash::check($request->password, $c->password)) {
                $request->session()->put('contact_authenticated_' . $survey->id, $c->id);
                $request->session()->forget('invite_contact_' . $survey->id);
                return redirect()->route('surveys.respond', $survey)
                    ->with('success', 'Connecté en tant que contact.');
            }
        }

        // Échec: identifiants invalides
        return back()->withErrors(['email' => "Identifiants invalides pour ce sondage."])->withInput();
    }

    public function logout(Request $request, Survey $survey)
    {
        $request->session()->forget('contact_authenticated_' . $survey->id);
        return redirect()->route('surveys.respond', $survey)
            ->with('success', 'Déconnecté.');
    }

    public function accessByToken(string $token)
    {
        // Garde de schéma
        if (!\Illuminate\Support\Facades\Schema::hasTable('survey_invite_tokens')) {
            abort(404);
        }
        $hash = hash('sha256', $token);
        $record = \App\Models\SurveyInviteToken::with(['survey', 'contact'])
            ->where('token', $hash)
            ->first();
        if (!$record) {
            return redirect()->route('surveys.public.index')->with('error', 'Lien d\'invitation invalide.');
        }
        // Vérification de signature liée au sondage (si fournie)
        $sid = request('sid');
        $sig = request('sig');
        if (!empty($sid) || !empty($sig)) {
            if ((int)$sid !== (int)$record->survey_id) {
                return redirect()->route('surveys.public.index')->with('error', 'Lien d\'invitation invalide (sondage).');
            }
            $expected = hash_hmac('sha256', $token . '|' . $sid, config('app.key'));
            if (!hash_equals($expected, (string)$sig)) {
                return redirect()->route('surveys.public.index')->with('error', 'Lien d\'invitation invalide (signature).');
            }
        }

        $survey = $record->survey;
        $contact = $record->contact;
        if (!$survey || !$contact) {
            return redirect()->route('surveys.public.index')->with('error', 'Lien d\'invitation invalide.');
        }

        // Valider que le sondage est toujours disponible (actif + fenêtre temporelle)
        if (method_exists($survey, 'isActive') && !$survey->isActive()) {
            return redirect()->route('surveys.public.index')->with('error', 'Ce lien n\'est plus valide car le sondage est clôturé ou inactif.');
        }

        // Tracer l'utilisation (dernier clic sur le lien)
        $record->update(['used_at' => now()]);

        // Mémoriser le contact ciblé par le lien pour privilégier cette entrée à la connexion
        session(['invite_contact_' . $survey->id => $contact->id]);

        // Ne pas connecter automatiquement: rediriger vers la page de connexion contact avec l'email pré-rempli
        $loginUrl = route('contacts.login.form', ['survey' => $survey->id, 'email' => $contact->email]);
        return redirect()->to($loginUrl);
    }
}
