<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'survey_id',
        'created_by',
        'name',
    ];

    public function survey()
    {
        return $this->belongsTo(Survey::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }
}

