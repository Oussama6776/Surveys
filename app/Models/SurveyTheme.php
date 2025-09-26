<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveyTheme extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'display_name',
        'description',
        'primary_color',
        'secondary_color',
        'background_color',
        'text_color',
        'font_family',
        'font_size',
        'custom_css',
        'is_active',
    ];

    protected $casts = [
        'custom_css' => 'array',
        'is_active' => 'boolean',
    ];

    public function surveys()
    {
        return $this->hasMany(Survey::class, 'theme_id');
    }

    public function getCssVariables()
    {
        return [
            '--primary-color' => $this->primary_color,
            '--secondary-color' => $this->secondary_color,
            '--background-color' => $this->background_color,
            '--text-color' => $this->text_color,
            '--font-family' => $this->font_family,
            '--font-size' => $this->font_size . 'px',
        ];
    }

    public function getInlineStyles()
    {
        $variables = $this->getCssVariables();
        $css = '';
        
        foreach ($variables as $property => $value) {
            $css .= "{$property}: {$value}; ";
        }

        if ($this->custom_css) {
            $css .= $this->custom_css['css'] ?? '';
        }

        return $css;
    }
}