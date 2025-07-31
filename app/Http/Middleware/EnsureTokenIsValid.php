<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureTokenIsValid
{
    public function handle(Request $request, Closure $next)
    {
        $requiredToken = 'testtoken123';

        $authHeader = $request->header('Authorization');
        
        if (!$authHeader || $authHeader !== 'Bearer ' . $requiredToken) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}
