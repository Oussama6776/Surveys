<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Survey;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SurveyApiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index', 'show', 'publicShow']);
    }

    public function index(Request $request): JsonResponse
    {
        $query = Survey::with(['theme', 'questions.type'])
            ->where('is_public', true)
            ->where('status', 'active');

        // Filter by theme
        if ($request->has('theme_id')) {
            $query->where('theme_id', $request->theme_id);
        }

        // Search by title or description
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $surveys = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $surveys->items(),
            'pagination' => [
                'current_page' => $surveys->currentPage(),
                'last_page' => $surveys->lastPage(),
                'per_page' => $surveys->perPage(),
                'total' => $surveys->total(),
            ]
        ]);
    }

    public function show(Survey $survey): JsonResponse
    {
        if (!$survey->is_public) {
            return response()->json([
                'success' => false,
                'message' => 'Survey not found or not public.'
            ], 404);
        }

        $survey->load(['theme', 'questions.type', 'questions.options']);

        return response()->json([
            'success' => true,
            'data' => $survey
        ]);
    }

    public function publicShow(Survey $survey): JsonResponse
    {
        if (!$survey->isActive() || !$survey->is_public) {
            return response()->json([
                'success' => false,
                'message' => 'Survey is not available.'
            ], 404);
        }

        $survey->load(['theme', 'questions.type', 'questions.options']);

        // Check access code if required
        if ($survey->requires_access_code) {
            $accessCode = request()->header('X-Access-Code');
            if (!$accessCode || !$survey->accessCodes()->where('access_code', $accessCode)->where('is_active', true)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access code required.',
                    'requires_access_code' => true
                ], 403);
            }
        }

        return response()->json([
            'success' => true,
            'data' => $survey
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'theme_id' => 'nullable|exists:survey_themes,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'is_public' => 'boolean',
            'allow_multiple_responses' => 'boolean',
            'max_responses' => 'nullable|integer|min:1',
            'show_progress_bar' => 'boolean',
            'is_multi_page' => 'boolean',
            'questions_per_page' => 'nullable|integer|min:1',
        ]);

        $survey = auth()->user()->surveys()->create($validated);

        return response()->json([
            'success' => true,
            'data' => $survey,
            'message' => 'Survey created successfully.'
        ], 201);
    }

    public function update(Request $request, Survey $survey): JsonResponse
    {
        // Check ownership or permissions
        if ($survey->user_id !== auth()->id() && !auth()->user()->hasPermission('surveys.update')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized.'
            ], 403);
        }

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'theme_id' => 'nullable|exists:survey_themes,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'is_public' => 'boolean',
            'allow_multiple_responses' => 'boolean',
            'max_responses' => 'nullable|integer|min:1',
            'show_progress_bar' => 'boolean',
            'is_multi_page' => 'boolean',
            'questions_per_page' => 'nullable|integer|min:1',
            'status' => 'sometimes|in:draft,active,paused,completed',
        ]);

        $survey->update($validated);

        return response()->json([
            'success' => true,
            'data' => $survey,
            'message' => 'Survey updated successfully.'
        ]);
    }

    public function destroy(Survey $survey): JsonResponse
    {
        // Check ownership or permissions
        if ($survey->user_id !== auth()->id() && !auth()->user()->hasPermission('surveys.delete')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized.'
            ], 403);
        }

        $survey->delete();

        return response()->json([
            'success' => true,
            'message' => 'Survey deleted successfully.'
        ]);
    }

    public function publish(Survey $survey): JsonResponse
    {
        // Check ownership or permissions
        if ($survey->user_id !== auth()->id() && !auth()->user()->hasPermission('surveys.publish')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized.'
            ], 403);
        }

        $survey->update(['status' => 'active']);

        return response()->json([
            'success' => true,
            'data' => $survey,
            'message' => 'Survey published successfully.'
        ]);
    }

    public function unpublish(Survey $survey): JsonResponse
    {
        // Check ownership or permissions
        if ($survey->user_id !== auth()->id() && !auth()->user()->hasPermission('surveys.publish')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized.'
            ], 403);
        }

        $survey->update(['status' => 'draft']);

        return response()->json([
            'success' => true,
            'data' => $survey,
            'message' => 'Survey unpublished successfully.'
        ]);
    }

    public function duplicate(Survey $survey): JsonResponse
    {
        // Check ownership or permissions
        if ($survey->user_id !== auth()->id() && !auth()->user()->hasPermission('surveys.create')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized.'
            ], 403);
        }

        $newSurvey = $survey->replicate();
        $newSurvey->title = $survey->title . ' (Copy)';
        $newSurvey->status = 'draft';
        $newSurvey->user_id = auth()->id();
        $newSurvey->save();

        // Duplicate questions
        foreach ($survey->questions as $question) {
            $newQuestion = $question->replicate();
            $newQuestion->survey_id = $newSurvey->id;
            $newQuestion->save();

            // Duplicate options
            foreach ($question->options as $option) {
                $newOption = $option->replicate();
                $newOption->question_id = $newQuestion->id;
                $newOption->save();
            }
        }

        return response()->json([
            'success' => true,
            'data' => $newSurvey,
            'message' => 'Survey duplicated successfully.'
        ], 201);
    }

    public function stats(Survey $survey): JsonResponse
    {
        // Check ownership or permissions
        if ($survey->user_id !== auth()->id() && !auth()->user()->hasPermission('surveys.read')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized.'
            ], 403);
        }

        $stats = [
            'total_responses' => $survey->responses()->count(),
            'completion_rate' => $survey->getProgressPercentage(),
            'questions_count' => $survey->questions()->count(),
            'created_at' => $survey->created_at,
            'last_response_at' => $survey->responses()->latest()->first()?->created_at,
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}