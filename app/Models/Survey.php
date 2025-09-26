<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'start_date',
        'end_date',
        'theme_id',
        'custom_styling',
        'is_public',
        'requires_access_code',
        'allow_multiple_responses',
        'max_responses',
        'max_responses_per_user',
        'show_progress_bar',
        'randomize_questions',
        'randomize_options',
        'allow_back_navigation',
        'save_progress',
        'is_multi_page',
        'questions_per_page',
        'completion_message',
        'redirect_url',
        'send_completion_email',
        'completion_email_subject',
        'completion_email_template',
        'track_analytics',
        'collect_ip_address',
        'collect_user_agent',
        'status',
        'metadata',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'custom_styling' => 'array',
        'is_public' => 'boolean',
        'requires_access_code' => 'boolean',
        'allow_multiple_responses' => 'boolean',
        'show_progress_bar' => 'boolean',
        'randomize_questions' => 'boolean',
        'randomize_options' => 'boolean',
        'allow_back_navigation' => 'boolean',
        'save_progress' => 'boolean',
        'is_multi_page' => 'boolean',
        'send_completion_email' => 'boolean',
        'track_analytics' => 'boolean',
        'collect_ip_address' => 'boolean',
        'collect_user_agent' => 'boolean',
        'metadata' => 'array',
    ];

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function responses()
    {
        return $this->hasMany(Response::class);
    }

    public function theme()
    {
        return $this->belongsTo(SurveyTheme::class, 'theme_id');
    }

    public function pages()
    {
        return $this->hasMany(SurveyPage::class);
    }

    public function accessCodes()
    {
        return $this->hasMany(SurveyAccessCode::class);
    }

    public function webhooks()
    {
        return $this->hasMany(Webhook::class);
    }

    public function files()
    {
        return $this->hasMany(SurveyFile::class);
    }

    public function contactGroups()
    {
        return $this->hasMany(ContactGroup::class);
    }

    public function isActive()
    {
        if ($this->status !== 'active') {
            return false;
        }

        if ($this->start_date && now()->isBefore($this->start_date)) {
            return false;
        }

        if ($this->end_date && now()->isAfter($this->end_date)) {
            return false;
        }

        return true;
    }

    public function canAcceptResponses()
    {
        if (!$this->isActive()) {
            return false;
        }

        if ($this->max_responses && $this->responses()->count() >= $this->max_responses) {
            return false;
        }

        return true;
    }

    public function getProgressPercentage()
    {
        if ($this->max_responses) {
            return min(100, ($this->responses()->count() / $this->max_responses) * 100);
        }

        return 0;
    }

    public function getTotalPages()
    {
        if ($this->is_multi_page) {
            return ceil($this->questions()->count() / $this->questions_per_page);
        }

        return 1;
    }

    public function getQuestionsForPage($pageNumber)
    {
        if (!$this->is_multi_page) {
            return $this->questions()->orderBy('order')->get();
        }

        $offset = ($pageNumber - 1) * $this->questions_per_page;
        return $this->questions()
            ->orderBy('order')
            ->offset($offset)
            ->limit($this->questions_per_page)
            ->get();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
