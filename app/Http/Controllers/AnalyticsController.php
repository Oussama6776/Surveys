<?php

namespace App\Http\Controllers;

use App\Models\Survey;
use App\Models\Response;
use App\Models\Answer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class AnalyticsController extends Controller
{
    public function index()
    {
        $surveys = Survey::withCount('responses')->latest()->get();
        return view('analytics.index', compact('surveys'));
    }

    public function show(Survey $survey)
    {
        $survey->load(['questions.type', 'questions.options', 'responses.answers']);
        
        $analytics = $this->generateAnalytics($survey);
        
        return view('analytics.show', compact('survey', 'analytics'));
    }

    public function exportPdf(Survey $survey)
    {
        $survey->load(['questions.type', 'questions.options', 'responses.answers']);
        $analytics = $this->generateAnalytics($survey);
        
        $pdf = Pdf::loadView('analytics.pdf', compact('survey', 'analytics'));
        
        return $pdf->download("survey_analytics_{$survey->id}.pdf");
    }

    public function exportExcel(Survey $survey)
    {
        $survey->load(['questions.type', 'questions.options', 'responses.answers']);
        $analytics = $this->generateAnalytics($survey);
        
        // Implementation for Excel export would go here
        // For now, return CSV
        return $this->exportCsv($survey, $analytics);
    }

    private function generateAnalytics(Survey $survey)
    {
        $analytics = [
            'overview' => $this->getOverviewStats($survey),
            'response_trends' => $this->getResponseTrends($survey),
            'question_analytics' => $this->getQuestionAnalytics($survey),
            'demographics' => $this->getDemographics($survey),
            'completion_rates' => $this->getCompletionRates($survey),
        ];

        return $analytics;
    }

    private function getOverviewStats(Survey $survey)
    {
        $totalResponses = $survey->responses()->count();
        $totalQuestions = $survey->questions()->count();
        $completionRate = $totalResponses > 0 ? 
            ($survey->responses()->whereNotNull('submitted_at')->count() / $totalResponses) * 100 : 0;
        
        $avgCompletionTime = $survey->responses()
            ->whereNotNull('submitted_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, created_at, submitted_at)) as avg_time')
            ->value('avg_time') ?? 0;

        return [
            'total_responses' => $totalResponses,
            'total_questions' => $totalQuestions,
            'completion_rate' => round($completionRate, 2),
            'avg_completion_time' => round($avgCompletionTime, 2),
            'response_rate' => $this->calculateResponseRate($survey),
        ];
    }

    private function getResponseTrends(Survey $survey)
    {
        $trends = $survey->responses()
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return $trends;
    }

    private function getQuestionAnalytics(Survey $survey)
    {
        $questionAnalytics = [];

        foreach ($survey->questions as $question) {
            $analytics = [
                'question_id' => $question->id,
                'question_text' => $question->question_text,
                'type' => $question->type->name,
                'response_count' => $question->answers()->count(),
                'skip_rate' => $this->calculateSkipRate($question),
            ];

            // Add type-specific analytics
            switch ($question->type->name) {
                case 'Multiple Choice':
                case 'Checkbox':
                    $analytics['option_distribution'] = $this->getOptionDistribution($question);
                    break;
                case 'Rating Scale':
                case 'Star Rating':
                    $analytics['rating_stats'] = $this->getRatingStats($question);
                    break;
                case 'Text':
                case 'Textarea':
                    $analytics['word_cloud'] = $this->generateWordCloud($question);
                    break;
            }

            $questionAnalytics[] = $analytics;
        }

        return $questionAnalytics;
    }

    private function getDemographics(Survey $survey)
    {
        // This would analyze demographic data if collected
        return [
            'device_types' => $this->getDeviceTypes($survey),
            'browsers' => $this->getBrowsers($survey),
            'locations' => $this->getLocations($survey),
        ];
    }

    private function getCompletionRates(Survey $survey)
    {
        $totalQuestions = $survey->questions()->count();
        $completionRates = [];

        foreach ($survey->questions as $question) {
            $answeredCount = $question->answers()->count();
            $totalResponses = $survey->responses()->count();
            $completionRate = $totalResponses > 0 ? ($answeredCount / $totalResponses) * 100 : 0;

            $completionRates[] = [
                'question_id' => $question->id,
                'question_text' => $question->question_text,
                'completion_rate' => round($completionRate, 2),
            ];
        }

        return $completionRates;
    }

    private function calculateResponseRate(Survey $survey)
    {
        // This would calculate based on unique visitors vs responses
        // For now, return a placeholder
        return 0;
    }

    private function calculateSkipRate(Question $question)
    {
        $totalResponses = $question->survey->responses()->count();
        $answeredCount = $question->answers()->count();
        
        if ($totalResponses == 0) return 0;
        
        return round((($totalResponses - $answeredCount) / $totalResponses) * 100, 2);
    }

    private function getOptionDistribution(Question $question)
    {
        return $question->options()
            ->withCount('answers')
            ->get()
            ->map(function ($option) {
                return [
                    'option_text' => $option->option_text,
                    'count' => $option->answers_count,
                    'percentage' => 0, // Calculate percentage
                ];
            });
    }

    private function getRatingStats(Question $question)
    {
        $ratings = $question->answers()
            ->selectRaw('answer, COUNT(*) as count')
            ->groupBy('answer')
            ->orderBy('answer')
            ->get();

        $total = $ratings->sum('count');
        $avg = $ratings->avg('answer');
        $min = $ratings->min('answer');
        $max = $ratings->max('answer');

        return [
            'average' => round($avg, 2),
            'min' => $min,
            'max' => $max,
            'distribution' => $ratings->map(function ($rating) use ($total) {
                return [
                    'rating' => $rating->answer,
                    'count' => $rating->count,
                    'percentage' => $total > 0 ? round(($rating->count / $total) * 100, 2) : 0,
                ];
            }),
        ];
    }

    private function generateWordCloud(Question $question)
    {
        $answers = $question->answers()
            ->whereNotNull('answer')
            ->pluck('answer')
            ->join(' ');

        // Simple word frequency analysis
        $words = str_word_count(strtolower($answers), 1);
        $wordCounts = array_count_values($words);
        arsort($wordCounts);

        return array_slice($wordCounts, 0, 50, true);
    }

    private function getDeviceTypes(Survey $survey)
    {
        // This would analyze user agent strings
        return [];
    }

    private function getBrowsers(Survey $survey)
    {
        // This would analyze user agent strings
        return [];
    }

    private function getLocations(Survey $survey)
    {
        // This would analyze IP addresses or location data
        return [];
    }

    private function exportCsv(Survey $survey, $analytics)
    {
        $filename = "survey_analytics_{$survey->id}.csv";
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($survey, $analytics) {
            $file = fopen('php://output', 'w');
            
            // Write overview data
            fputcsv($file, ['Survey Analytics Overview']);
            fputcsv($file, ['Total Responses', $analytics['overview']['total_responses']]);
            fputcsv($file, ['Completion Rate', $analytics['overview']['completion_rate'] . '%']);
            fputcsv($file, ['Average Completion Time', $analytics['overview']['avg_completion_time'] . ' minutes']);
            fputcsv($file, []);
            
            // Write question analytics
            fputcsv($file, ['Question Analytics']);
            fputcsv($file, ['Question', 'Type', 'Response Count', 'Skip Rate']);
            
            foreach ($analytics['question_analytics'] as $qa) {
                fputcsv($file, [
                    $qa['question_text'],
                    $qa['type'],
                    $qa['response_count'],
                    $qa['skip_rate'] . '%'
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}