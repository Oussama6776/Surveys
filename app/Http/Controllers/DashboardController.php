<?php

namespace App\Http\Controllers;

use App\Models\Survey;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Si l'utilisateur est admin ou super admin, afficher le dashboard admin
        if ($user && $user->hasAnyRole(['super_admin', 'admin'])) {
            return view('admin.dashboard');
        }
        
        // Sinon, afficher le dashboard normal
        $surveys = Survey::withCount('responses')
            ->latest()
            ->take(6)
            ->get();

        return view('dashboard', compact('surveys'));
    }
}
