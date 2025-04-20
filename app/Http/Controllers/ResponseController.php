<?php

namespace App\Http\Controllers;

use App\Models\Survey;
use App\Models\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ResponseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Survey $survey)
    {
        $responses = $survey->responses()
            ->with('answers.question')
            ->latest()
            ->paginate(10);

        return view('responses.index', compact('survey', 'responses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Survey $survey)
    {
        try {
            $validated = $request->validate([
                'responses' => 'required|array',
                'responses.*.question_id' => 'required|exists:questions,id',
                'responses.*.answer' => 'required',
            ]);

            DB::beginTransaction();

            $response = $survey->responses()->create([
                'submitted_at' => now(),
            ]);

            foreach ($validated['responses'] as $responseData) {
                $response->answers()->create([
                    'question_id' => $responseData['question_id'],
                    'answer' => is_array($responseData['answer']) ? json_encode($responseData['answer']) : $responseData['answer'],
                ]);
            }

            DB::commit();

            return redirect()->route('survey.thank-you')
                ->with('success', 'Thank you for completing the survey! Your response has been recorded.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'There was an error submitting your survey. Please try again.')
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Survey $survey, Response $response)
    {
        $response->load('answers.question');
        return view('responses.show', compact('survey', 'response'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Survey $survey, Response $response)
    {
        $response->delete();
        return redirect()->route('responses.index', $survey)
            ->with('success', 'Response deleted successfully.');
    }

    public function submit(Request $request, Survey $survey)
    {
        $validated = $request->validate([
            'answers' => 'required|array',
            'answers.*' => 'required_if:questions.*.is_required,true',
        ]);

        DB::transaction(function () use ($validated, $survey) {
            $response = $survey->responses()->create([
                'submitted_at' => now(),
            ]);

            foreach ($validated['answers'] as $questionId => $answer) {
                if (is_array($answer)) {
                    foreach ($answer as $optionId) {
                        $response->answers()->create([
                            'question_id' => $questionId,
                            'response_option_id' => $optionId,
                        ]);
                    }
                } else {
                    $response->answers()->create([
                        'question_id' => $questionId,
                        'text_answer' => $answer,
                    ]);
                }
            }
        });

        return redirect()->route('survey.public.show', $survey)
            ->with('success', 'Thank you for completing the survey!');
    }

    public function view(Survey $survey, Response $response)
    {
        $response->load('answers.question');
        return view('responses.view', compact('survey', 'response'));
    }

    public function export(Survey $survey)
    {
        return response()->streamDownload(function () use ($survey) {
            $handle = fopen('php://output', 'w');
            
            // Headers
            $headers = ['Submission Date'];
            foreach ($survey->questions as $question) {
                $headers[] = $question->question_text;
            }
            fputcsv($handle, $headers);

            // Data
            foreach ($survey->responses as $response) {
                $row = [$response->submitted_at];
                foreach ($survey->questions as $question) {
                    $answer = $response->answers->where('question_id', $question->id)->first();
                    $row[] = $answer ? ($answer->text_answer ?? $answer->option->option_text) : '';
                }
                fputcsv($handle, $row);
            }

            fclose($handle);
        }, $survey->title . '_responses.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }
}
