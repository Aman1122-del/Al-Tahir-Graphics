<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateChatId
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $chatId = $request->route('chat');
        
        // If chatId is null or empty, return error
        if (!$chatId) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Chat ID is required',
                    'message' => 'Please provide a valid chat ID'
                ], 400);
            }
            
            return redirect()->back()->with('error', 'Chat ID is required. Please select a valid chat.');
        }
        
        // If chatId is not numeric, return error
        if (!is_numeric($chatId)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Invalid Chat ID',
                    'message' => 'Chat ID must be a valid number'
                ], 400);
            }
            
            return redirect()->back()->with('error', 'Invalid Chat ID. Please select a valid chat.');
        }
        
        return $next($request);
    }
}
