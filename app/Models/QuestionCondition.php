<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionCondition extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_id',
        'depends_on_question_id',
        'condition_type',
        'condition_value',
        'action',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function dependsOnQuestion()
    {
        return $this->belongsTo(Question::class, 'depends_on_question_id');
    }

    public function evaluate($responseValue)
    {
        if (!$this->is_active) {
            return false;
        }

        switch ($this->condition_type) {
            case 'equals':
                return $responseValue == $this->condition_value;
            case 'not_equals':
                return $responseValue != $this->condition_value;
            case 'contains':
                return str_contains($responseValue, $this->condition_value);
            case 'greater_than':
                return $responseValue > $this->condition_value;
            case 'less_than':
                return $responseValue < $this->condition_value;
            case 'in':
                $values = json_decode($this->condition_value, true);
                return in_array($responseValue, $values);
            case 'not_in':
                $values = json_decode($this->condition_value, true);
                return !in_array($responseValue, $values);
            default:
                return false;
        }
    }
}