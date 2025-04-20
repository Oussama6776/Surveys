<?php

namespace App\Http\Controllers;

use App\Models\Survey;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $surveys = Survey::withCount('responses')
            ->latest()
            ->take(6)
            ->get();

        return view('dashboard', compact('surveys'));
    }
}
