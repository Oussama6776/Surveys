<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Response extends Model
{
    use HasFactory;

    protected $fillable = [
        'survey_id',
        'contact_id',
        'submitted_at',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($response) {
            $response->submitted_at = now();
        });
    }

    public function survey()
    {
        return $this->belongsTo(Survey::class);
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    public function answers()
    {
        return $this->hasMany(Answer::class);
    }
}
