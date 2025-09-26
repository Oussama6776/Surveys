<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionRating extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_id',
        'min_value',
        'max_value',
        'min_label',
        'max_label',
        'rating_type',
        'show_labels',
        'allow_half_ratings',
    ];

    protected $casts = [
        'show_labels' => 'boolean',
        'allow_half_ratings' => 'boolean',
    ];

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function getRange()
    {
        return range($this->min_value, $this->max_value);
    }

    public function getStep()
    {
        return $this->allow_half_ratings ? 0.5 : 1;
    }

    public function getRatingOptions()
    {
        $options = [];
        $step = $this->getStep();
        
        for ($i = $this->min_value; $i <= $this->max_value; $i += $step) {
            $options[] = [
                'value' => $i,
                'label' => $this->getLabelForValue($i)
            ];
        }
        
        return $options;
    }

    private function getLabelForValue($value)
    {
        if ($value == $this->min_value && $this->min_label) {
            return $this->min_label;
        }
        
        if ($value == $this->max_value && $this->max_label) {
            return $this->max_label;
        }
        
        return $value;
    }
}