<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = auth()->user();
        if (!$user || !$user->currentAccessToken()) {
            return response()->error([], 'Unauthorized', 403);
        }
        foreach ($roles as $role) {
            if ($user->isRole($role) && $user->tokenCan('role-' . $role)) {
                return $next($request);
            }
        }
        return response()->error([], 'Unauthorized', 403);
    }
}
