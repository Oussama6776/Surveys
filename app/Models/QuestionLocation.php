<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionLocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_id',
        'location_type',
        'default_latitude',
        'default_longitude',
        'default_zoom',
        'allowed_countries',
        'restricted_areas',
        'require_precise_location',
        'show_map',
        'allow_search',
    ];

    protected $casts = [
        'allowed_countries' => 'array',
        'restricted_areas' => 'array',
        'require_precise_location' => 'boolean',
        'show_map' => 'boolean',
        'allow_search' => 'boolean',
    ];

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function getDefaultLocation()
    {
        if ($this->default_latitude && $this->default_longitude) {
            return [
                'lat' => $this->default_latitude,
                'lng' => $this->default_longitude,
                'zoom' => $this->default_zoom
            ];
        }

        return null;
    }

    public function isLocationAllowed($latitude, $longitude)
    {
        // Check if location is in restricted areas
        if ($this->restricted_areas) {
            foreach ($this->restricted_areas as $area) {
                if ($this->isPointInArea($latitude, $longitude, $area)) {
                    return false;
                }
            }
        }

        return true;
    }

    private function isPointInArea($lat, $lng, $area)
    {
        // Simple bounding box check
        if (isset($area['bounds'])) {
            $bounds = $area['bounds'];
            return $lat >= $bounds['south'] && $lat <= $bounds['north'] &&
                   $lng >= $bounds['west'] && $lng <= $bounds['east'];
        }

        return false;
    }

    public function getMapConfig()
    {
        return [
            'center' => $this->getDefaultLocation(),
            'zoom' => $this->default_zoom,
            'showMap' => $this->show_map,
            'allowSearch' => $this->allow_search,
            'allowedCountries' => $this->allowed_countries,
            'requirePreciseLocation' => $this->require_precise_location,
        ];
    }
}