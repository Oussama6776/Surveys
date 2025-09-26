<?php

namespace App\Http\Controllers;

use App\Models\Survey;
use Illuminate\View\View;

class StatsController extends Controller
{
    public function show(Survey $survey): View
    {
        $this->authorize('view', $survey);
        $survey->load('questions.options','responses.details');
        return view('stats.show', compact('survey'));
    }
}

