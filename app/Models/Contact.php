<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'survey_id',
        'contact_group_id',
        'created_by',
        'nom',
        'prenom',
        'email',
        'password',
        'user_id',
        'status_envoi',
        'date_envoi',
        'last_error',
    ];

    protected $casts = [
        'date_envoi' => 'datetime',
    ];

    public function survey()
    {
        return $this->belongsTo(Survey::class);
    }

    public function group()
    {
        return $this->belongsTo(ContactGroup::class, 'contact_group_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
