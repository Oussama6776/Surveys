<?php

namespace App\Http\Controllers;

use App\Models\Survey;
use App\Models\Question;
use App\Models\QuestionType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class QuestionController extends Controller
{
    private function authorizeSurveyModification(Survey $survey)
    {
        $user = auth()->user();
        if (!$user) {
            abort(403, 'Vous devez être connecté pour modifier ce sondage.');
        }

        // Si la colonne user_id existe et que le sondage n'a pas encore de propriétaire,
        // l'utilisateur courant devient propriétaire à la première modification.
        if (Schema::hasColumn('surveys', 'user_id') && is_null($survey->user_id)) {
            $survey->user_id = $user->id;
            $survey->save();
        }

        if (!$user->canModifySurvey($survey)) {
            abort(403, 'Vous n\'êtes pas autorisé à modifier ce sondage.');
        }
        // N'appliquer la contrainte de brouillon que si la colonne status existe
        if (Schema::hasColumn('surveys', 'status')) {
            if ($survey->status !== 'draft') {
                abort(400, 'Le sondage doit être au statut brouillon pour être modifié.');
            }
        }
    }

    private function ensureQuestionBelongsToSurvey(Survey $survey, Question $question): void
    {
        if ($question->survey_id !== $survey->id) {
            abort(404);
        }
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Survey $survey)
    {
        $questionTypes = QuestionType::all();
        return view('questions.create', compact('survey', 'questionTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Survey $survey)
    {
        $this->authorizeSurveyModification($survey);
        // Détermine l'action de navigation souhaitée après enregistrement
        $nextAction = $request->input('next_action'); // valeurs attendues: 'next' ou 'finish'

        // Supporte une seule question ou un lot de questions via questions[]
        if ($request->has('questions') && is_array($request->questions)) {
            $data = $request->validate([
                'questions' => 'required|array|min:1',
                'questions.*.question_text' => 'required|string|max:255',
                'questions.*.question_type_id' => 'required|exists:question_types,id',
                'questions.*.is_required' => 'nullable|boolean',
                'questions.*.options' => 'nullable|array',
                'questions.*.options.*' => 'required_with:questions.*.options|string|max:255',
            ]);

            foreach ($data['questions'] as $q) {
                $question = $survey->questions()->create([
                    'question_text' => $q['question_text'],
                    'question_type_id' => $q['question_type_id'],
                    'is_required' => (bool)($q['is_required'] ?? false),
                ]);
                if (isset($q['options']) && in_array($q['question_type_id'], [2,3])) {
                    foreach ($q['options'] as $optionText) {
                        $question->options()->create(['option_text' => $optionText]);
                    }
                }
            }
        } else {
            $validated = $request->validate([
                'question_text' => 'required|string|max:255',
                'question_type_id' => 'required|exists:question_types,id',
                'is_required' => 'nullable|boolean',
                'options' => 'required_if:question_type_id,2,3|array|min:2',
                'options.*' => 'required|string|max:255',
            ]);

            $question = $survey->questions()->create([
                'question_text' => $validated['question_text'],
                'question_type_id' => $validated['question_type_id'],
                'is_required' => $request->boolean('is_required'),
            ]);

            if (in_array($validated['question_type_id'], [2, 3]) && isset($validated['options'])) {
                foreach ($validated['options'] as $optionText) {
                    $question->options()->create(['option_text' => $optionText]);
                }
            }
        }

        // Redirection selon le flux demandé
        if ($nextAction === 'next') {
            return redirect()->route('questions.create', $survey)
                ->with('success', 'Question ajoutée. Vous pouvez en ajouter une autre.');
        }

        return redirect()->route('surveys.show', $survey)
            ->with('success', 'Question(s) ajoutée(s) avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Survey $survey, Question $question)
    {
        $this->authorizeSurveyModification($survey);
        $this->ensureQuestionBelongsToSurvey($survey, $question);
        $questionTypes = QuestionType::all();
        return view('questions.edit', compact('survey', 'question', 'questionTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Survey $survey, Question $question)
    {
        $this->authorizeSurveyModification($survey);
        $this->ensureQuestionBelongsToSurvey($survey, $question);
        $validated = $request->validate([
            'question_text' => 'required|string|max:255',
            'question_type_id' => 'required|exists:question_types,id',
            'is_required' => 'nullable|boolean',
            'options' => 'required_if:question_type_id,2,3|array|min:2',
            'options.*' => 'required|string|max:255',
        ]);

        $question->update([
            'question_text' => $validated['question_text'],
            'question_type_id' => $validated['question_type_id'],
            'is_required' => $request->boolean('is_required'),
        ]);

        if (in_array($validated['question_type_id'], [2, 3]) && isset($validated['options'])) {
            $question->options()->delete();
            foreach ($validated['options'] as $optionText) {
                $question->options()->create(['option_text' => $optionText]);
            }
        } else {
            $question->options()->delete();
        }

        return redirect()->route('surveys.show', $survey)
            ->with('success', 'Question modifiée avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Survey $survey, Question $question)
    {
        $this->authorizeSurveyModification($survey);
        $this->ensureQuestionBelongsToSurvey($survey, $question);
        $question->delete();
        return redirect()->route('surveys.show', $survey)
            ->with('success', 'Question supprimée avec succès.');
    }

    public function reorder(Request $request, Survey $survey)
    {
        $this->authorizeSurveyModification($survey);
        $validated = $request->validate([
            'questions' => 'required|array',
            'questions.*' => 'exists:questions,id'
        ]);

        foreach ($validated['questions'] as $index => $id) {
            Question::where('id', $id)->update(['order' => $index + 1]);
        }

        return response()->json(['message' => 'Questions reordered successfully.']);
    }
}
