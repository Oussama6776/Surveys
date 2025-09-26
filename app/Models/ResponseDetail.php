<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResponseDetail extends Model
{
    use HasFactory;

    protected $fillable = ['response_id','question_id','value','option_id'];

    public function response(): BelongsTo { return $this->belongsTo(Response::class); }
    public function question(): BelongsTo { return $this->belongsTo(Question::class); }
}

