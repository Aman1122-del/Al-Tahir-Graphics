<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        
        // Check if user has admin role or is_admin flag
        if ($user->is_admin || (method_exists($user, 'hasRole') && $user->hasRole(['admin', 'support']))) {
            return $next($request);
        }

        abort(403, 'Unauthorized access.');
    }
}
