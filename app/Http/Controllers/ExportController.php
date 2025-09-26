<?php

namespace App\Http\Controllers;

use App\Models\Survey;

class ExportController extends Controller
{
    public function csv(Survey $survey)
    {
        $this->authorize('export', $survey);
        $survey->load('questions','responses.details');
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="survey_'.$survey->id.'.csv"',
        ];
        return response()->stream(function() use ($survey) {
            $out = fopen('php://output', 'w');
            $labels = ['response_id','submitted_at'];
            foreach ($survey->questions as $q) $labels[] = $q->label;
            fputcsv($out, $labels);

            foreach ($survey->responses->whereNotNull('submitted_at') as $r) {
                $row = [$r->id, $r->submitted_at];
                foreach ($survey->questions as $q) {
                    $d = $r->details->firstWhere('question_id', $q->id);
                    $row[] = $d?->value ?? '';
                }
                fputcsv($out, $row);
            }
            fclose($out);
        }, 200, $headers);
    }
}

