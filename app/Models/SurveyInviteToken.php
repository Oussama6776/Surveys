<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveyInviteToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'survey_id',
        'contact_id',
        'token',
        'expires_at',
        'used_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
    ];

    public function survey()
    {
        return $this->belongsTo(Survey::class);
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }
}

