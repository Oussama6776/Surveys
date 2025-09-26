<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionRanking extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_id',
        'ranking_options',
        'min_rankings',
        'max_rankings',
        'allow_ties',
        'ranking_direction',
    ];

    protected $casts = [
        'ranking_options' => 'array',
        'allow_ties' => 'boolean',
    ];

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function getOptions()
    {
        return $this->ranking_options ?? [];
    }

    public function getMaxRankings()
    {
        return $this->max_rankings ?? count($this->getOptions());
    }

    public function getMinRankings()
    {
        return $this->min_rankings ?? 1;
    }

    public function validateRanking($ranking)
    {
        $options = $this->getOptions();
        $minRankings = $this->getMinRankings();
        $maxRankings = $this->getMaxRankings();

        // Check if ranking is an array
        if (!is_array($ranking)) {
            return false;
        }

        // Check minimum and maximum rankings
        if (count($ranking) < $minRankings || count($ranking) > $maxRankings) {
            return false;
        }

        // Check if all ranked items are valid options
        foreach ($ranking as $item) {
            if (!in_array($item, $options)) {
                return false;
            }
        }

        // Check for duplicates if ties are not allowed
        if (!$this->allow_ties && count($ranking) !== count(array_unique($ranking))) {
            return false;
        }

        return true;
    }
}