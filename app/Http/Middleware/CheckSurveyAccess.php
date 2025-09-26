<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Survey;
use App\Models\SurveyAccessCode;

class CheckSurveyAccess
{
    public function handle(Request $request, Closure $next)
    {
        $survey = $request->route('survey');
        
        if (!$survey) {
            return redirect()->route('surveys.index')
                ->with('error', 'Sondage non trouvé.');
        }

        // Vérifier si l'utilisateur peut accéder au sondage
        if (!$this->canAccessSurvey($survey, $request)) {
            if ($survey->requires_access_code) {
                return redirect()->route('surveys.access-code', $survey)
                    ->with('error', 'Ce sondage nécessite un code d\'accès.');
            } else {
                return redirect()->route('contacts.login.form', $survey)
                    ->with('error', 'Veuillez vous connecter avec vos identifiants de contact.');
            }
        }

        return $next($request);
    }

    private function canAccessSurvey(Survey $survey, Request $request)
    {
        // Le créateur du sondage peut toujours y accéder
        if (Auth::check() && $survey->user_id === Auth::id()) {
            return true;
        }

        // Les admins peuvent accéder à tous les sondages
        if (Auth::check() && Auth::user()->hasAnyRole(['super_admin', 'admin'])) {
            return true;
        }

        // Les viewers ne peuvent accéder qu'aux sondages qui leur sont explicitement assignés
        if (Auth::check() && Auth::user()->hasRole('survey_viewer')) {
            return \App\Models\Contact::where('survey_id', $survey->id)
                ->where('user_id', Auth::id())
                ->exists();
        }

        // Sondage public - accessible à tous
        if ($survey->is_public) {
            return true;
        }

        // Connexion contact (session)
        $contactSessionKey = 'contact_authenticated_' . $survey->id;
        if ($request->session()->has($contactSessionKey) && $request->session()->get($contactSessionKey)) {
            return true;
        }

        // Sondage privé - nécessite une authentification
        if (!Auth::check()) {
            return false;
        }

        // Vérifier le code d'accès si nécessaire
        if ($survey->requires_access_code) {
            $accessCode = $request->session()->get('survey_access_code_' . $survey->id);
            
            if (!$accessCode) {
                return false;
            }

            $validCode = SurveyAccessCode::where('survey_id', $survey->id)
                ->where('access_code', $accessCode)
                ->where('expires_at', '>', now())
                ->first();

            if (!$validCode) {
                $request->session()->forget('survey_access_code_' . $survey->id);
                return false;
            }
        }

        return true;
    }
}
