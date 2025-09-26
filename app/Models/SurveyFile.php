<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class SurveyFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'survey_id',
        'response_id',
        'question_id',
        'original_name',
        'file_name',
        'file_path',
        'mime_type',
        'file_size',
        'file_type',
        'is_public',
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    public function survey()
    {
        return $this->belongsTo(Survey::class);
    }

    public function response()
    {
        return $this->belongsTo(Response::class);
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function getUrl()
    {
        if ($this->is_public) {
            return Storage::url($this->file_path);
        }

        return route('files.download', $this->id);
    }

    public function getFileTypeIcon()
    {
        switch ($this->file_type) {
            case 'image':
                return 'fas fa-image';
            case 'document':
                return 'fas fa-file-alt';
            case 'video':
                return 'fas fa-video';
            case 'audio':
                return 'fas fa-music';
            default:
                return 'fas fa-file';
        }
    }

    public function getFormattedSize()
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function delete()
    {
        // Delete the physical file
        if (Storage::exists($this->file_path)) {
            Storage::delete($this->file_path);
        }

        return parent::delete();
    }
}