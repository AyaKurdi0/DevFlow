<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckPermissions
{
    public function handle(Request $request, Closure $next, $permissions)
    {
        if (!Auth::check())
        {
            return response()->json([
                'message' => 'Unauthenticated.'
            ], 401);
        }

        if (!Auth::user()->can($permissions))
        {
            return response()->json([
                'message' => 'Forbidden, You do not have the required permissions.'
            ], 403);
        }
        return $next($request);
    }
}
