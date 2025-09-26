<?php

namespace App\Http\Controllers;

use App\Models\SurveyFile;
use App\Models\Survey;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileController extends Controller
{
    public function upload(Request $request, Survey $survey, Question $question = null)
    {
        $request->validate([
            'file' => 'required|file|max:10240', // 10MB max
            'file_type' => 'required|string|in:image,document,video,audio,other',
        ]);

        $file = $request->file('file');
        $fileType = $request->input('file_type');
        
        // Validate file type based on category
        $allowedMimes = $this->getAllowedMimes($fileType);
        if (!in_array($file->getMimeType(), $allowedMimes)) {
            return response()->json([
                'error' => 'Invalid file type for the selected category.'
            ], 422);
        }

        // Generate unique filename
        $extension = $file->getClientOriginalExtension();
        $filename = Str::uuid() . '.' . $extension;
        $path = "surveys/{$survey->id}/files/{$filename}";

        // Store file
        $stored = Storage::putFileAs("surveys/{$survey->id}/files", $file, $filename);

        if (!$stored) {
            return response()->json([
                'error' => 'Failed to upload file.'
            ], 500);
        }

        // Save file record
        $surveyFile = SurveyFile::create([
            'survey_id' => $survey->id,
            'question_id' => $question?->id,
            'original_name' => $file->getClientOriginalName(),
            'file_name' => $filename,
            'file_path' => $path,
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'file_type' => $fileType,
            'is_public' => $request->boolean('is_public', false),
        ]);

        return response()->json([
            'success' => true,
            'file' => [
                'id' => $surveyFile->id,
                'original_name' => $surveyFile->original_name,
                'file_size' => $surveyFile->getFormattedSize(),
                'url' => $surveyFile->getUrl(),
                'icon' => $surveyFile->getFileTypeIcon(),
            ]
        ]);
    }

    public function download(SurveyFile $file)
    {
        if (!Storage::exists($file->file_path)) {
            abort(404, 'File not found.');
        }

        return Storage::download($file->file_path, $file->original_name);
    }

    public function view(SurveyFile $file)
    {
        if (!Storage::exists($file->file_path)) {
            abort(404, 'File not found.');
        }

        // Check if file is public or user has permission
        if (!$file->is_public && !auth()->check()) {
            abort(403, 'Access denied.');
        }

        $mimeType = $file->mime_type;
        $content = Storage::get($file->file_path);

        return response($content)
            ->header('Content-Type', $mimeType)
            ->header('Content-Disposition', 'inline; filename="' . $file->original_name . '"');
    }

    public function delete(SurveyFile $file)
    {
        // Check permissions
        if (!auth()->user()->hasPermission('surveys.update')) {
            abort(403, 'Unauthorized action.');
        }

        $file->delete();

        return response()->json([
            'success' => true,
            'message' => 'File deleted successfully.'
        ]);
    }

    public function index(Survey $survey)
    {
        $files = $survey->files()->latest()->paginate(20);
        return view('files.index', compact('survey', 'files'));
    }

    public function show(SurveyFile $file)
    {
        $file->load(['survey', 'question', 'response']);
        return view('files.show', compact('file'));
    }

    public function bulkDelete(Request $request, Survey $survey)
    {
        $request->validate([
            'file_ids' => 'required|array',
            'file_ids.*' => 'exists:survey_files,id',
        ]);

        $files = SurveyFile::whereIn('id', $request->file_ids)
            ->where('survey_id', $survey->id)
            ->get();

        foreach ($files as $file) {
            $file->delete();
        }

        return redirect()->route('files.index', $survey)
            ->with('success', 'Selected files deleted successfully.');
    }

    public function export(Survey $survey)
    {
        $files = $survey->files()->get();
        
        $csvData = [];
        $csvData[] = ['ID', 'Original Name', 'File Type', 'Size', 'Upload Date', 'Question'];
        
        foreach ($files as $file) {
            $csvData[] = [
                $file->id,
                $file->original_name,
                $file->file_type,
                $file->getFormattedSize(),
                $file->created_at->format('Y-m-d H:i:s'),
                $file->question?->question_text ?? 'N/A',
            ];
        }

        $filename = "survey_files_{$survey->id}.csv";
        
        $callback = function() use ($csvData) {
            $file = fopen('php://output', 'w');
            foreach ($csvData as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    private function getAllowedMimes($fileType)
    {
        switch ($fileType) {
            case 'image':
                return ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'];
            case 'document':
                return [
                    'application/pdf',
                    'application/msword',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'application/vnd.ms-excel',
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'text/plain',
                    'text/csv'
                ];
            case 'video':
                return ['video/mp4', 'video/avi', 'video/mov', 'video/wmv', 'video/webm'];
            case 'audio':
                return ['audio/mp3', 'audio/wav', 'audio/ogg', 'audio/mpeg'];
            default:
                return ['*']; // Allow all types for 'other'
        }
    }
}