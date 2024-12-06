<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleCheckMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next , ... $roles): Response
    {
        if (Auth::check()) {
          
            if (in_array(Auth::user()->roles, $roles)) {
                return $next($request);
            }
        }

        //response for api
        return response()->json([
            'message' => 'You are not authorized to access this resource',
        ], 403);

        //
        return abort(403, 'Unauthorized action.');
    }
}
