<?php

namespace App\Http\Controllers;

use App\Models\Survey;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SurveyController extends Controller
{
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
        ]);

        $survey = Survey::create($validated);

        return redirect()->route('surveys.show', $survey)
            ->with('success', 'Survey created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Survey $survey)
    {
        $survey->load('questions.type', 'questions.options');
        return view('surveys.show', compact('survey'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Survey $survey)
    {
        return view('surveys.edit', compact('survey'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Survey $survey)
    {
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
