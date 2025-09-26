<?php

namespace App\Http\Controllers;

use App\Models\Survey;
use App\Models\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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
            DB::beginTransaction();

            $normalized = [];
            $contactId = session('contact_authenticated_' . $survey->id);

            if ($request->has('responses')) {
                // Original payload format: responses[] = [{question_id, answer}]
                $validated = $request->validate([
                    'responses' => 'required|array',
                    'responses.*.question_id' => 'required|exists:questions,id',
                    'responses.*.answer' => 'required',
                ]);
                $normalized = $validated['responses'];
            } else {
                // Accept form format: answers[question_id] = value|array
                $answers = $request->input('answers');
                if (!is_array($answers)) {
                    return redirect()->back()->with('error', 'Invalid submission format.')->withInput();
                }

                $questionIds = $survey->questions()->pluck('id')->toArray();
                foreach ($answers as $qid => $value) {
                    // Only accept answers for questions of this survey
                    if (!in_array((int)$qid, $questionIds, true)) continue;
                    $normalized[] = [
                        'question_id' => (int)$qid,
                        'answer' => $value,
                    ];
                }

                if (empty($normalized)) {
                    return redirect()->back()->with('error', 'Veuillez répondre au moins à une question.')->withInput();
                }
            }

            // Build base data
            $baseData = ['submitted_at' => now()];
            try {
                $baseData['ip_address'] = $request->ip();
                $baseData['user_agent'] = $request->userAgent();
            } catch (\Throwable $e) {}

            // If a contact is authenticated for this survey, update existing response or create one
            $response = null;
            if (!empty($contactId) && Schema::hasColumn('responses', 'contact_id')) {
                $response = $survey->responses()->where('contact_id', $contactId)->first();
                if ($response) {
                    $response->fill($baseData);
                    $response->save();
                    // Replace answers with new ones
                    $response->answers()->delete();
                } else {
                    $payload = array_merge($baseData, ['contact_id' => $contactId]);
                    $response = $survey->responses()->create($payload);
                }
            } else {
                // Anonymous/public submission or column not yet migrated
                $response = $survey->responses()->create($baseData);
            }

            foreach ($normalized as $item) {
                $response->answers()->create([
                    'question_id' => $item['question_id'],
                    'answer' => is_array($item['answer']) ? json_encode($item['answer']) : $item['answer'],
                ]);
            }

            DB::commit();

            return redirect()->route('survey.thank-you')
                ->with('success', 'Merci, votre réponse a été enregistrée.');
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
