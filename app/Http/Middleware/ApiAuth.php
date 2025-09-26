<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        // Check for API token in header
        $token = $request->header('X-API-Key');
        
        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'API key required.'
            ], 401);
        }

        // Validate API token (you can implement your own logic here)
        if ($token !== config('app.api_key')) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid API key.'
            ], 401);
        }

        return $next($request);
    }
}