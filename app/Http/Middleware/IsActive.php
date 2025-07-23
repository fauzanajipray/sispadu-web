<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    // public function handle(Request $request, Closure $next, $guard = null): Response
    // {
    //     if (auth()->user->is_active) {
    //         return response()->json(['message' => 'Success.']);
    //     } else {
    //         return response()->json(['message' => 'Unauthenticated.'], 401);
    //     }
    //     return $next($request);
    // }

    public function handle(Request $request, Closure $next, $guard = null): Response
    {
        $user = auth()->user();

        if ($user && $user->is_active) {
            return $next($request); // teruskan request kalau aktif
        }

        return response()->json(['message' => 'Unauthenticated.'], 401);
    }
}
