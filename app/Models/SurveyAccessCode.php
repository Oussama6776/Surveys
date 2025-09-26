<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SurveyAccessCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'survey_id',
        'access_code',
        'name',
        'description',
        'max_uses',
        'used_count',
        'expires_at',
        'is_active',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function survey()
    {
        return $this->belongsTo(Survey::class);
    }

    public static function generateCode($length = 8)
    {
        do {
            $code = Str::upper(Str::random($length));
        } while (self::where('access_code', $code)->exists());

        return $code;
    }

    public function isValid()
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->expires_at && now()->isAfter($this->expires_at)) {
            return false;
        }

        if ($this->max_uses && $this->used_count >= $this->max_uses) {
            return false;
        }

        return true;
    }

    public function use()
    {
        if ($this->isValid()) {
            $this->increment('used_count');
            return true;
        }

        return false;
    }

    public function getRemainingUses()
    {
        if (!$this->max_uses) {
            return null; // Unlimited
        }

        return max(0, $this->max_uses - $this->used_count);
    }
}