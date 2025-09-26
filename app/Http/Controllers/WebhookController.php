<?php

namespace App\Http\Controllers;

use App\Models\Webhook;
use App\Models\Survey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WebhookController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Survey $survey)
    {
        $webhooks = $survey->webhooks()->latest()->get();
        return view('webhooks.index', compact('survey', 'webhooks'));
    }

    public function create(Survey $survey)
    {
        $eventTypes = $this->getEventTypes();
        return view('webhooks.create', compact('survey', 'eventTypes'));
    }

    public function store(Request $request, Survey $survey)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|url|max:500',
            'event_type' => 'required|string|in:' . implode(',', array_keys($this->getEventTypes())),
            'http_method' => 'required|string|in:GET,POST,PUT,PATCH,DELETE',
            'headers' => 'nullable|array',
            'headers.*.key' => 'required_with:headers|string|max:255',
            'headers.*.value' => 'required_with:headers|string|max:500',
            'payload_template' => 'nullable|array',
            'retry_count' => 'integer|min:0|max:10',
            'timeout' => 'integer|min:5|max:300',
        ]);

        // Format headers
        $headers = [];
        if (isset($validated['headers'])) {
            foreach ($validated['headers'] as $header) {
                if (!empty($header['key']) && !empty($header['value'])) {
                    $headers[$header['key']] = $header['value'];
                }
            }
        }

        $webhook = $survey->webhooks()->create([
            'name' => $validated['name'],
            'url' => $validated['url'],
            'event_type' => $validated['event_type'],
            'http_method' => $validated['http_method'],
            'headers' => $headers,
            'payload_template' => $validated['payload_template'] ?? null,
            'retry_count' => $validated['retry_count'] ?? 3,
            'timeout' => $validated['timeout'] ?? 30,
        ]);

        return redirect()->route('webhooks.index', $survey)
            ->with('success', 'Webhook created successfully.');
    }

    public function show(Survey $survey, Webhook $webhook)
    {
        $webhook->load('survey');
        return view('webhooks.show', compact('survey', 'webhook'));
    }

    public function edit(Survey $survey, Webhook $webhook)
    {
        $eventTypes = $this->getEventTypes();
        return view('webhooks.edit', compact('survey', 'webhook', 'eventTypes'));
    }

    public function update(Request $request, Survey $survey, Webhook $webhook)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|url|max:500',
            'event_type' => 'required|string|in:' . implode(',', array_keys($this->getEventTypes())),
            'http_method' => 'required|string|in:GET,POST,PUT,PATCH,DELETE',
            'headers' => 'nullable|array',
            'headers.*.key' => 'required_with:headers|string|max:255',
            'headers.*.value' => 'required_with:headers|string|max:500',
            'payload_template' => 'nullable|array',
            'retry_count' => 'integer|min:0|max:10',
            'timeout' => 'integer|min:5|max:300',
            'is_active' => 'boolean',
        ]);

        // Format headers
        $headers = [];
        if (isset($validated['headers'])) {
            foreach ($validated['headers'] as $header) {
                if (!empty($header['key']) && !empty($header['value'])) {
                    $headers[$header['key']] = $header['value'];
                }
            }
        }

        $webhook->update([
            'name' => $validated['name'],
            'url' => $validated['url'],
            'event_type' => $validated['event_type'],
            'http_method' => $validated['http_method'],
            'headers' => $headers,
            'payload_template' => $validated['payload_template'] ?? null,
            'retry_count' => $validated['retry_count'] ?? 3,
            'timeout' => $validated['timeout'] ?? 30,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return redirect()->route('webhooks.index', $survey)
            ->with('success', 'Webhook updated successfully.');
    }

    public function destroy(Survey $survey, Webhook $webhook)
    {
        $webhook->delete();

        return redirect()->route('webhooks.index', $survey)
            ->with('success', 'Webhook deleted successfully.');
    }

    public function test(Survey $survey, Webhook $webhook)
    {
        $testData = [
            'test' => true,
            'survey_id' => $survey->id,
            'survey_title' => $survey->title,
            'webhook_name' => $webhook->name,
            'timestamp' => now()->toISOString(),
        ];

        $success = $webhook->trigger($testData);

        if ($success) {
            return redirect()->route('webhooks.show', [$survey, $webhook])
                ->with('success', 'Test webhook sent successfully.');
        } else {
            return redirect()->route('webhooks.show', [$survey, $webhook])
                ->with('error', 'Test webhook failed. Check the logs for details.');
        }
    }

    public function toggle(Survey $survey, Webhook $webhook)
    {
        $webhook->update(['is_active' => !$webhook->is_active]);

        $status = $webhook->is_active ? 'activated' : 'deactivated';
        
        return redirect()->route('webhooks.index', $survey)
            ->with('success', "Webhook {$status} successfully.");
    }

    public function logs(Survey $survey, Webhook $webhook)
    {
        // This would show webhook execution logs
        // For now, return a simple view
        return view('webhooks.logs', compact('survey', 'webhook'));
    }

    private function getEventTypes()
    {
        return [
            'response_submitted' => 'Response Submitted',
            'survey_completed' => 'Survey Completed',
            'survey_created' => 'Survey Created',
            'survey_updated' => 'Survey Updated',
            'survey_published' => 'Survey Published',
            'survey_closed' => 'Survey Closed',
            'response_deleted' => 'Response Deleted',
            'question_added' => 'Question Added',
            'question_updated' => 'Question Updated',
            'question_deleted' => 'Question Deleted',
        ];
    }
}