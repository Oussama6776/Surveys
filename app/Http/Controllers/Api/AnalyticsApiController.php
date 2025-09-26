<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Survey;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AnalyticsApiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function overview(Survey $survey): JsonResponse
    {
        if ($survey->user_id !== auth()->id() && !auth()->user()->hasPermission('analytics.read')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        $totalResponses = $survey->responses()->count();
        $totalQuestions = $survey->questions()->count();
        $completionRate = $totalResponses > 0 ? 
            ($survey->responses()->whereNotNull('submitted_at')->count() / $totalResponses) * 100 : 0;

        $overview = [
            'total_responses' => $totalResponses,
            'total_questions' => $totalQuestions,
            'completion_rate' => round($completionRate, 2),
            'created_at' => $survey->created_at,
            'last_response_at' => $survey->responses()->latest()->first()?->created_at,
        ];

        return response()->json(['success' => true, 'data' => $overview]);
    }

    public function questionAnalytics(Survey $survey): JsonResponse
    {
        if ($survey->user_id !== auth()->id() && !auth()->user()->hasPermission('analytics.read')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        $questionAnalytics = [];
        foreach ($survey->questions as $question) {
            $analytics = [
                'question_id' => $question->id,
                'question_text' => $question->question_text,
                'type' => $question->type->name,
                'response_count' => $question->answers()->count(),
                'skip_rate' => $this->calculateSkipRate($question),
            ];
            $questionAnalytics[] = $analytics;
        }

        return response()->json(['success' => true, 'data' => $questionAnalytics]);
    }

    private function calculateSkipRate($question)
    {
        $totalResponses = $question->survey->responses()->count();
        $answeredCount = $question->answers()->count();
        return $totalResponses == 0 ? 0 : round((($totalResponses - $answeredCount) / $totalResponses) * 100, 2);
    }
}