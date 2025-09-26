<?php

namespace App\Http\Controllers;

use App\Http\Requests\ParticipationRequest;
use App\Models\Response;
use App\Models\ResponseDetail;
use App\Models\Survey;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class ParticipationController extends Controller
{
    public function show(string $public_token): View
    {
        $survey = Survey::where('public_token', $public_token)->with('questions.options')->firstOrFail();
        return view('participation.show', compact('survey'));
    }

    public function submit(ParticipationRequest $request, string $public_token): RedirectResponse
    {
        $survey = Survey::where('public_token', $public_token)->firstOrFail();

        // Unicité (simplifiée) : 1 réponse par IP
        if (Response::where('survey_id',$survey->id)->where('participant_ip', request()->ip())->whereNotNull('submitted_at')->exists()) {
            return back()->withErrors('Vous avez déjà participé à ce sondage.');
        }

        DB::transaction(function() use ($request, $survey) {
            $resp = Response::create([
                'survey_id' => $survey->id,
                'participant_token' => $survey->public_token,
                'participant_ip' => request()->ip(),
                'submitted_at' => now(),
            ]);
            foreach ($request->input('answers', []) as $question_id => $value) {
                if (is_array($value)) {
                    foreach ($value as $v) {
                        ResponseDetail::create([
                            'response_id' => $resp->id,
                            'question_id' => $question_id,
                            'value' => is_numeric($v) ? null : $v,
                            'option_id' => is_numeric($v) ? (int)$v : null,
                        ]);
                    }
                } else {
                    ResponseDetail::create([
                        'response_id' => $resp->id,
                        'question_id' => $question_id,
                        'value' => $value,
                    ]);
                }
            }
        });

        return redirect()->route('participation.thanks');
    }

    public function thanks(): View
    {
        return view('participation.thanks');
    }
}

