<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Survey;
use App\Models\Response;
use App\Models\Answer;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ResponseApiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['store']);
    }

    public function index(Request $request, Survey $survey): JsonResponse
    {
        // Check permissions
        if ($survey->user_id !== auth()->id() && !auth()->user()->hasPermission('responses.read')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized.'
            ], 403);
        }

        $query = $survey->responses()->with('answers.question');

        // Filter by date range
        if ($request->has('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->has('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $responses = $query->latest()->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $responses->items(),
            'pagination' => [
                'current_page' => $responses->currentPage(),
                'last_page' => $responses->lastPage(),
                'per_page' => $responses->perPage(),
                'total' => $responses->total(),
            ]
        ]);
    }

    public function show(Survey $survey, Response $response): JsonResponse
    {
        // Check permissions
        if ($survey->user_id !== auth()->id() && !auth()->user()->hasPermission('responses.read')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized.'
            ], 403);
        }

        $response->load('answers.question');

        return response()->json([
            'success' => true,
            'data' => $response
        ]);
    }

    public function store(Request $request, Survey $survey): JsonResponse
    {
        // Check if survey can accept responses
        if (!$survey->canAcceptResponses()) {
            return response()->json([
                'success' => false,
                'message' => 'Survey is not accepting responses.'
            ], 403);
        }

        // Check access code if required
        if ($survey->requires_access_code) {
            $accessCode = $request->header('X-Access-Code') ?? $request->input('access_code');
            if (!$accessCode || !$survey->accessCodes()->where('access_code', $accessCode)->where('is_active', true)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Valid access code required.',
                    'requires_access_code' => true
                ], 403);
            }
        }

        $validated = $request->validate([
            'answers' => 'required|array',
            'answers.*.question_id' => 'required|exists:questions,id',
            'answers.*.answer' => 'required',
            'answers.*.file_ids' => 'nullable|array',
            'answers.*.file_ids.*' => 'exists:survey_files,id',
        ]);

        try {
            DB::beginTransaction();

            $response = $survey->responses()->create([
                'submitted_at' => now(),
            ]);

            foreach ($validated['answers'] as $answerData) {
                $question = $survey->questions()->find($answerData['question_id']);
                
                if (!$question) {
                    continue;
                }

                // Validate answer based on question type
                if (!$this->validateAnswer($question, $answerData['answer'])) {
                    throw new \Exception("Invalid answer for question: {$question->question_text}");
                }

                $answer = $response->answers()->create([
                    'question_id' => $answerData['question_id'],
                    'answer' => is_array($answerData['answer']) ? 
                        json_encode($answerData['answer']) : 
                        $answerData['answer'],
                ]);

                // Handle file attachments
                if (isset($answerData['file_ids']) && is_array($answerData['file_ids'])) {
                    foreach ($answerData['file_ids'] as $fileId) {
                        $file = \App\Models\SurveyFile::find($fileId);
                        if ($file && $file->survey_id === $survey->id) {
                            $file->update(['response_id' => $response->id]);
                        }
                    }
                }
            }

            DB::commit();

            // Trigger webhooks
            $this->triggerWebhooks($survey, 'response_submitted', [
                'response_id' => $response->id,
                'survey_id' => $survey->id,
                'submitted_at' => $response->submitted_at,
            ]);

            return response()->json([
                'success' => true,
                'data' => $response,
                'message' => 'Response submitted successfully.'
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit response: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, Survey $survey, Response $response): JsonResponse
    {
        // Check permissions
        if ($survey->user_id !== auth()->id() && !auth()->user()->hasPermission('responses.update')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized.'
            ], 403);
        }

        $validated = $request->validate([
            'answers' => 'required|array',
            'answers.*.question_id' => 'required|exists:questions,id',
            'answers.*.answer' => 'required',
        ]);

        try {
            DB::beginTransaction();

            // Delete existing answers
            $response->answers()->delete();

            // Create new answers
            foreach ($validated['answers'] as $answerData) {
                $response->answers()->create([
                    'question_id' => $answerData['question_id'],
                    'answer' => is_array($answerData['answer']) ? 
                        json_encode($answerData['answer']) : 
                        $answerData['answer'],
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $response,
                'message' => 'Response updated successfully.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update response: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Survey $survey, Response $response): JsonResponse
    {
        // Check permissions
        if ($survey->user_id !== auth()->id() && !auth()->user()->hasPermission('responses.delete')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized.'
            ], 403);
        }

        $response->delete();

        return response()->json([
            'success' => true,
            'message' => 'Response deleted successfully.'
        ]);
    }

    public function export(Survey $survey): JsonResponse
    {
        // Check permissions
        if ($survey->user_id !== auth()->id() && !auth()->user()->hasPermission('responses.export')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized.'
            ], 403);
        }

        $responses = $survey->responses()->with('answers.question')->get();
        
        $csvData = [];
        $headers = ['Response ID', 'Submitted At'];
        
        // Add question headers
        foreach ($survey->questions as $question) {
            $headers[] = $question->question_text;
        }
        $csvData[] = $headers;

        // Add response data
        foreach ($responses as $response) {
            $row = [$response->id, $response->submitted_at];
            
            foreach ($survey->questions as $question) {
                $answer = $response->answers->where('question_id', $question->id)->first();
                $row[] = $answer ? $answer->answer : '';
            }
            
            $csvData[] = $row;
        }

        $filename = "survey_responses_{$survey->id}.csv";
        
        return response()->json([
            'success' => true,
            'data' => $csvData,
            'filename' => $filename
        ]);
    }

    private function validateAnswer($question, $answer)
    {
        // Basic validation based on question type
        switch ($question->type->name) {
            case 'Email':
                return filter_var($answer, FILTER_VALIDATE_EMAIL) !== false;
            case 'URL':
                return filter_var($answer, FILTER_VALIDATE_URL) !== false;
            case 'Number':
                return is_numeric($answer);
            case 'Multiple Choice':
                return $question->options()->where('id', $answer)->exists();
            case 'Checkbox':
                if (is_array($answer)) {
                    foreach ($answer as $optionId) {
                        if (!$question->options()->where('id', $optionId)->exists()) {
                            return false;
                        }
                    }
                    return true;
                }
                return false;
            default:
                return !empty($answer);
        }
    }

    private function triggerWebhooks($survey, $eventType, $data)
    {
        $webhooks = $survey->webhooks()
            ->where('event_type', $eventType)
            ->where('is_active', true)
            ->get();

        foreach ($webhooks as $webhook) {
            $webhook->trigger($data);
        }
    }
}