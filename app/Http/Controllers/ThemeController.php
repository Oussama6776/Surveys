<?php

namespace App\Http\Controllers;

use App\Models\SurveyTheme;
use Illuminate\Http\Request;

class ThemeController extends Controller
{
    public function index()
    {
        $themes = SurveyTheme::where('is_active', true)->get();
        return view('themes.index', compact('themes'));
    }

    public function create()
    {
        return view('themes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:survey_themes',
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'primary_color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'secondary_color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'background_color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'text_color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'font_family' => 'required|string|max:255',
            'font_size' => 'required|integer|min:10|max:24',
            'custom_css' => 'nullable|string',
        ]);

        $validated['custom_css'] = $request->custom_css ? ['css' => $request->custom_css] : null;

        $theme = SurveyTheme::create($validated);

        return redirect()->route('themes.index')
            ->with('success', 'Theme created successfully.');
    }

    public function show(SurveyTheme $theme)
    {
        $theme->load('surveys');
        return view('themes.show', compact('theme'));
    }

    public function edit(SurveyTheme $theme)
    {
        return view('themes.edit', compact('theme'));
    }

    public function update(Request $request, SurveyTheme $theme)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:survey_themes,name,' . $theme->id,
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'primary_color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'secondary_color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'background_color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'text_color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'font_family' => 'required|string|max:255',
            'font_size' => 'required|integer|min:10|max:24',
            'custom_css' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['custom_css'] = $request->custom_css ? ['css' => $request->custom_css] : null;

        $theme->update($validated);

        return redirect()->route('themes.index')
            ->with('success', 'Theme updated successfully.');
    }

    public function destroy(SurveyTheme $theme)
    {
        // Check if theme is being used by any surveys
        if ($theme->surveys()->count() > 0) {
            return redirect()->route('themes.index')
                ->with('error', 'Cannot delete theme that is being used by surveys.');
        }

        $theme->delete();

        return redirect()->route('themes.index')
            ->with('success', 'Theme deleted successfully.');
    }

    public function preview(SurveyTheme $theme)
    {
        return view('themes.preview', compact('theme'));
    }

    public function duplicate(SurveyTheme $theme)
    {
        $newTheme = $theme->replicate();
        $newTheme->name = $theme->name . '_copy';
        $newTheme->display_name = $theme->display_name . ' (Copy)';
        $newTheme->save();

        return redirect()->route('themes.edit', $newTheme)
            ->with('success', 'Theme duplicated successfully.');
    }

    public function export(SurveyTheme $theme)
    {
        $themeData = [
            'name' => $theme->name,
            'display_name' => $theme->display_name,
            'description' => $theme->description,
            'primary_color' => $theme->primary_color,
            'secondary_color' => $theme->secondary_color,
            'background_color' => $theme->background_color,
            'text_color' => $theme->text_color,
            'font_family' => $theme->font_family,
            'font_size' => $theme->font_size,
            'custom_css' => $theme->custom_css,
        ];

        $filename = "theme_{$theme->name}.json";
        
        return response()->json($themeData)
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }

    public function import(Request $request)
    {
        $request->validate([
            'theme_file' => 'required|file|mimes:json',
        ]);

        $file = $request->file('theme_file');
        $content = file_get_contents($file->getPathname());
        $themeData = json_decode($content, true);

        if (!$themeData) {
            return redirect()->route('themes.index')
                ->with('error', 'Invalid theme file format.');
        }

        // Validate required fields
        $requiredFields = ['name', 'display_name', 'primary_color', 'secondary_color', 'background_color', 'text_color', 'font_family', 'font_size'];
        foreach ($requiredFields as $field) {
            if (!isset($themeData[$field])) {
                return redirect()->route('themes.index')
                    ->with('error', "Missing required field: {$field}");
            }
        }

        // Check if theme name already exists
        if (SurveyTheme::where('name', $themeData['name'])->exists()) {
            $themeData['name'] = $themeData['name'] . '_imported_' . time();
        }

        $theme = SurveyTheme::create($themeData);

        return redirect()->route('themes.edit', $theme)
            ->with('success', 'Theme imported successfully.');
    }
}