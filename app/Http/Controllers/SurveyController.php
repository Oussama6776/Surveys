<?php

namespace App\Http\Controllers;

use App\Models\Survey;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;

class SurveyController extends Controller
{
    private function authorizeSurveyEdit(Survey $survey)
    {
        $user = auth()->user();
        if (!$user) {
            abort(403, 'Vous devez être connecté.');
        }
        if (!$user->canModifySurvey($survey)) {
            abort(403, 'Vous n\'êtes pas autorisé à modifier ce sondage.');
        }
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $surveys = Survey::withCount('responses')->latest()->paginate(10);
        return view('surveys.index', compact('surveys'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('surveys.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'visibility' => 'required|in:public,private',
            'requires_access_code' => 'boolean',
            'allow_multiple_responses' => 'boolean',
            'max_responses' => 'nullable|integer|min:1',
            'show_progress_bar' => 'boolean',
            'randomize_questions' => 'boolean',
            'theme_id' => 'nullable|exists:survey_themes,id',
        ]);

        // Déterminer si le sondage est public ou privé
        $isPublic = $validated['visibility'] === 'public';
        $requiresAccessCode = $validated['visibility'] === 'private' && $request->has('requires_access_code');

        // Créer le sondage avec les paramètres de visibilité
        $survey = Survey::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'is_public' => $isPublic,
            'requires_access_code' => $requiresAccessCode,
            'allow_multiple_responses' => $request->has('allow_multiple_responses'),
            'max_responses' => $validated['max_responses'],
            'show_progress_bar' => $request->has('show_progress_bar'),
            'randomize_questions' => $request->has('randomize_questions'),
            'theme_id' => $validated['theme_id'],
            'user_id' => auth()->id(),
            'status' => 'draft',
        ]);

        // Générer un code d'accès si nécessaire
        if ($requiresAccessCode) {
            $accessCode = \App\Models\SurveyAccessCode::create([
                'survey_id' => $survey->id,
                'access_code' => \App\Models\SurveyAccessCode::generateCode(8),
                'expires_at' => now()->addDays(30),
                'max_uses' => null,
                'used_count' => 0,
            ]);
        }

        return redirect()->route('questions.create', $survey)
            ->with('success', 'Sondage créé avec succès. Ajoutez maintenant vos premières questions.');
    }

    /**
     * Show the access code form for private surveys
     */
    public function showAccessCodeForm(Survey $survey)
    {
        if (!$survey->requires_access_code) {
            return redirect()->route('surveys.show', $survey);
        }

        return view('surveys.access-code', compact('survey'));
    }

    /**
     * Verify access code for private surveys
     */
    public function verifyAccessCode(Request $request, Survey $survey)
    {
        $request->validate([
            'access_code' => 'required|string|size:8'
        ]);

        $accessCode = \App\Models\SurveyAccessCode::where('survey_id', $survey->id)
            ->where('access_code', strtoupper($request->access_code))
            ->where('expires_at', '>', now())
            ->first();

        if (!$accessCode) {
            return back()->withErrors([
                'access_code' => 'Code d\'accès invalide ou expiré.'
            ]);
        }

        // Stocker le code d'accès en session
        $request->session()->put('survey_access_code_' . $survey->id, $accessCode->access_code);

        return redirect()->route('surveys.show', $survey)
            ->with('success', 'Code d\'accès validé avec succès.');
    }

    /**
     * Show the survey for responding
     */
    public function respond(Survey $survey)
    {
        // Vérifier si l'utilisateur peut accéder au sondage
        if (!$this->canAccessSurvey($survey)) {
            if ($survey->requires_access_code) {
                return redirect()->route('surveys.access-code', $survey)
                    ->with('error', 'Ce sondage nécessite un code d\'accès.');
            } else {
                // Pour les sondages privés sans code, proposer la connexion contact
                return redirect()->route('contacts.login.form', $survey)
                    ->with('error', 'Veuillez vous connecter avec vos identifiants de contact.');
            }
        }

        // Vérifier si le sondage est actif
        if ($survey->status !== 'active') {
            return redirect()->back()
                ->with('error', 'Ce sondage n\'est pas encore actif.');
        }

        // Vérifier les dates
        if ($survey->start_date && now()->lt($survey->start_date)) {
            return redirect()->back()
                ->with('error', 'Ce sondage n\'est pas encore ouvert.');
        }

        if ($survey->end_date && now()->gt($survey->end_date)) {
            return redirect()->back()
                ->with('error', 'Ce sondage est fermé.');
        }

        // Charger les questions avec leurs options
        $survey->load(['questions.type', 'questions.options']);

        // Pré-remplir si un contact a déjà répondu
        $existingAnswers = [];
        $isEditing = false;
        $contactId = session('contact_authenticated_' . $survey->id);
        if (!empty($contactId) && Schema::hasColumn('responses', 'contact_id')) {
            $existing = $survey->responses()->where('contact_id', $contactId)->with('answers')->first();
            if ($existing) {
                $existingAnswers = $existing->answers->pluck('answer', 'question_id')->toArray();
                $isEditing = true;
            }
        }

        return view('surveys.respond', compact('survey', 'existingAnswers', 'isEditing'));
    }

    /**
     * Check if user can access the survey
     */
    private function canAccessSurvey(Survey $survey)
    {
        // Le créateur du sondage peut toujours y accéder
        if (auth()->check() && $survey->user_id === auth()->id()) {
            return true;
        }

        // Les admins peuvent accéder à tous les sondages
        if (auth()->check() && auth()->user()->hasAnyRole(['super_admin', 'admin'])) {
            return true;
        }

        // Les viewers ne peuvent accéder qu'au sondage qui leur est assigné (via contacts)
        if (auth()->check() && auth()->user()->hasRole('survey_viewer')) {
            return \App\Models\Contact::where('survey_id', $survey->id)
                ->where('user_id', auth()->id())
                ->exists();
        }

        // Sondage public - accessible à tous
        if ($survey->is_public) {
            return true;
        }

        // Authentification contact (session par sondage)
        $contactSessionKey = 'contact_authenticated_' . $survey->id;
        if (session()->has($contactSessionKey) && session($contactSessionKey)) {
            return true;
        }

        // Sondage privé - nécessite une authentification
        if (!auth()->check()) {
            return false;
        }

        // Vérifier le code d'accès si nécessaire
        if ($survey->requires_access_code) {
            $accessCode = session('survey_access_code_' . $survey->id);
            
            if (!$accessCode) {
                return false;
            }

            $validCode = \App\Models\SurveyAccessCode::where('survey_id', $survey->id)
                ->where('access_code', $accessCode)
                ->where('expires_at', '>', now())
                ->first();

            if (!$validCode) {
                session()->forget('survey_access_code_' . $survey->id);
                return false;
            }
        }

        return true;
    }

    /**
     * Publish a survey
     */
    public function publish(Survey $survey)
    {
        // Vérifier les permissions
        if (!auth()->user()->canAccessSurvey($survey) || !auth()->user()->canModifySurvey($survey)) {
            return response()->json([
                'success' => false,
                'message' => 'Vous n\'avez pas la permission de publier ce sondage.'
            ], 403);
        }

        // Vérifier qu'il y a au moins une question
        if ($survey->questions->count() === 0) {
            return response()->json([
                'success' => false,
                'message' => 'Vous devez ajouter au moins une question avant de publier le sondage.'
            ], 400);
        }

        // Publier le sondage
        $survey->update(['status' => 'active']);

        return response()->json([
            'success' => true,
            'message' => 'Sondage publié avec succès !',
            'status' => 'active'
        ]);
    }

    /**
     * Unpublish a survey
     */
    public function unpublish(Survey $survey)
    {
        // Vérifier les permissions
        if (!auth()->user()->canAccessSurvey($survey) || !auth()->user()->canModifySurvey($survey)) {
            return response()->json([
                'success' => false,
                'message' => 'Vous n\'avez pas la permission de dépublier ce sondage.'
            ], 403);
        }

        // Dépublier le sondage
        $survey->update(['status' => 'draft']);

        return response()->json([
            'success' => true,
            'message' => 'Sondage dépublié avec succès !',
            'status' => 'draft'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Survey $survey)
    {
        // Restreindre l'accès des viewers au sondage qui leur est assigné uniquement
        if (auth()->check() && auth()->user()->hasRole('survey_viewer')) {
            $allowed = \App\Models\Contact::where('survey_id', $survey->id)
                ->where('user_id', auth()->id())
                ->exists();
            if (!$allowed) {
                abort(403, 'Accès refusé à ce sondage.');
            }
        }

        $survey->load('questions.type', 'questions.options');
        // Groupes de contacts (visibles uniquement par le créateur courant) avec garde de schéma
        $contactGroups = collect();
        if (\Illuminate\Support\Facades\Schema::hasTable('contact_groups')) {
            $contactGroups = \App\Models\ContactGroup::where('survey_id', $survey->id)
                ->when(auth()->check(), fn($q) => $q->where('created_by', auth()->id()))
                ->orderBy('name')
                ->get();
        }
        return view('surveys.show', compact('survey', 'contactGroups'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Survey $survey)
    {
        $this->authorizeSurveyEdit($survey);
        return view('surveys.edit', compact('survey'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Survey $survey)
    {
        $this->authorizeSurveyEdit($survey);

        // Autoriser la modification des informations générales uniquement avant publication
        if (\Illuminate\Support\Facades\Schema::hasColumn('surveys', 'status') && $survey->status !== 'draft') {
            return redirect()->back()->with('error', 'Le sondage doit être au statut brouillon pour être modifié.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
        ]);

        $survey->update($validated);

        return redirect()->route('surveys.show', $survey)
            ->with('success', 'Survey updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Survey $survey)
    {
        $this->authorizeSurveyEdit($survey);
        $survey->delete();
        return redirect()->route('surveys.index')
            ->with('success', 'Survey deleted successfully.');
    }

    public function results(Survey $survey)
    {
        $survey->load(['questions.responses', 'responses']);
        return view('surveys.results', compact('survey'));
    }

    public function publicShow(Survey $survey)
    {
        // Temporarily bypass active check for testing
        // if (!$survey->isActive()) {
        //     return redirect()->route('surveys.index')
        //         ->with('error', 'This survey is not currently active.');
        // }

        $survey->load('questions.type', 'questions.options');
        return view('survey.public.show', compact('survey'));
    }
}
