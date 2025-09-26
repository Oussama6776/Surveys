<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'survey_id',
        'question_text',
        'question_type_id',
        'is_required',
        'order',
        'page_number',
        'help_text',
        'validation_rules',
        'conditional_logic',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'validation_rules' => 'array',
        'conditional_logic' => 'array',
    ];

    public function survey()
    {
        return $this->belongsTo(Survey::class);
    }

    public function type()
    {
        return $this->belongsTo(QuestionType::class, 'question_type_id');
    }

    public function options()
    {
        return $this->hasMany(QuestionOption::class);
    }

    public function answers()
    {
        return $this->hasMany(Answer::class);
    }

    public function conditions()
    {
        return $this->hasMany(QuestionCondition::class);
    }

    public function rating()
    {
        return $this->hasOne(QuestionRating::class);
    }

    public function ranking()
    {
        return $this->hasOne(QuestionRanking::class);
    }

    public function location()
    {
        return $this->hasOne(QuestionLocation::class);
    }

    public function files()
    {
        return $this->hasMany(SurveyFile::class);
    }

    public function getTypeName()
    {
        return $this->type->name ?? 'Unknown';
    }

    public function isConditional()
    {
        return $this->conditions()->where('is_active', true)->exists();
    }

    public function shouldShow($previousAnswers = [])
    {
        if (!$this->isConditional()) {
            return true;
        }

        foreach ($this->conditions as $condition) {
            $dependsOnAnswer = $previousAnswers[$condition->depends_on_question_id] ?? null;
            
            if ($condition->evaluate($dependsOnAnswer)) {
                return $condition->action === 'show';
            }
        }

        return true; // Default to show if no conditions match
    }

    public function getValidationRules()
    {
        $rules = [];

        if ($this->is_required) {
            $rules[] = 'required';
        }

        // Add type-specific validation
        switch ($this->getTypeName()) {
            case 'Email':
                $rules[] = 'email';
                break;
            case 'URL':
                $rules[] = 'url';
                break;
            case 'Number':
                $rules[] = 'numeric';
                break;
            case 'Phone':
                $rules[] = 'regex:/^[\+]?[1-9][\d]{0,15}$/';
                break;
        }

        // Add custom validation rules
        if ($this->validation_rules) {
            $rules = array_merge($rules, $this->validation_rules);
        }

        return $rules;
    }
}
