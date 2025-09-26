<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Survey;
use App\Models\Question;
use App\Models\Response;

class CheckOwnership
{
    public function handle(Request $request, Closure $next, $resource = null)
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        // Super admin a accès à tout
        if ($user->hasRole('super_admin')) {
            return $next($request);
        }

        // Vérifier la propriété selon le type de ressource
        switch ($resource) {
            case 'survey':
                $surveyId = $request->route('survey') ?? $request->route('id');
                if ($surveyId) {
                    $survey = Survey::find($surveyId);
                    if ($survey && $survey->user_id !== $user->id) {
                        // Vérifier si l'utilisateur a le droit de voir les enquêtes d'autres utilisateurs
                        if (!$user->hasPermission('surveys.read_all')) {
                            abort(403, 'Vous n\'avez pas accès à cette enquête.');
                        }
                    }
                }
                break;

            case 'question':
                $questionId = $request->route('question') ?? $request->route('id');
                if ($questionId) {
                    $question = Question::find($questionId);
                    if ($question && $question->survey->user_id !== $user->id) {
                        if (!$user->hasPermission('questions.read_all')) {
                            abort(403, 'Vous n\'avez pas accès à cette question.');
                        }
                    }
                }
                break;

            case 'response':
                $responseId = $request->route('response') ?? $request->route('id');
                if ($responseId) {
                    $response = Response::find($responseId);
                    if ($response && $response->survey->user_id !== $user->id) {
                        if (!$user->hasPermission('responses.read_all')) {
                            abort(403, 'Vous n\'avez pas accès à cette réponse.');
                        }
                    }
                }
                break;
        }

        return $next($request);
    }
}
