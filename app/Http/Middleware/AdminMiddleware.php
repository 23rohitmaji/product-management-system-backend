<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if ($user && $user->role === 'admin') {
            return $next($request);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Only admins are allowed to access this route',
        ], 403);
    }
}
