<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;

class Webhook extends Model
{
    use HasFactory;

    protected $fillable = [
        'survey_id',
        'name',
        'url',
        'event_type',
        'http_method',
        'headers',
        'payload_template',
        'is_active',
        'retry_count',
        'timeout',
        'last_triggered_at',
    ];

    protected $casts = [
        'headers' => 'array',
        'payload_template' => 'array',
        'is_active' => 'boolean',
        'last_triggered_at' => 'datetime',
    ];

    public function survey()
    {
        return $this->belongsTo(Survey::class);
    }

    public function trigger($data = [])
    {
        if (!$this->is_active) {
            return false;
        }

        try {
            $payload = $this->buildPayload($data);
            $headers = array_merge([
                'Content-Type' => 'application/json',
                'User-Agent' => 'SurveyTool-Webhook/1.0',
            ], $this->headers ?? []);

            $response = Http::timeout($this->timeout)
                ->withHeaders($headers)
                ->{strtolower($this->http_method)}($this->url, $payload);

            $this->update(['last_triggered_at' => now()]);

            return $response->successful();
        } catch (\Exception $e) {
            \Log::error('Webhook failed: ' . $e->getMessage(), [
                'webhook_id' => $this->id,
                'url' => $this->url,
                'data' => $data
            ]);

            return false;
        }
    }

    private function buildPayload($data)
    {
        if ($this->payload_template) {
            return array_merge($this->payload_template, $data);
        }

        return array_merge([
            'event_type' => $this->event_type,
            'survey_id' => $this->survey_id,
            'timestamp' => now()->toISOString(),
        ], $data);
    }
}