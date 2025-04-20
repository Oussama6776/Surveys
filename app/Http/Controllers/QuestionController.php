<?php

namespace App\Http\Controllers;

use App\Models\Survey;
use App\Models\Question;
use App\Models\QuestionType;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
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

        return redirect()->route('surveys.show', $survey)
            ->with('success', 'Question added successfully.');
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
        $questionTypes = QuestionType::all();
        return view('questions.edit', compact('survey', 'question', 'questionTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Survey $survey, Question $question)
    {
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
            ->with('success', 'Question updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Survey $survey, Question $question)
    {
        $question->delete();
        return redirect()->route('surveys.show', $survey)
            ->with('success', 'Question deleted successfully.');
    }

    public function reorder(Request $request, Survey $survey)
    {
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
