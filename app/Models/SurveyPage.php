<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveyPage extends Model
{
    use HasFactory;

    protected $fillable = [
        'survey_id',
        'title',
        'description',
        'page_number',
        'is_active',
        'page_settings',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'page_settings' => 'array',
    ];

    public function survey()
    {
        return $this->belongsTo(Survey::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function getQuestions()
    {
        return $this->survey->questions()
            ->where('page_number', $this->page_number)
            ->orderBy('order')
            ->get();
    }
}