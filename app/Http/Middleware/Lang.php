<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Lang
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Set the locale based on the request
        if ($request->header('Accept-Language')) {
            $locale = $request->header('Accept-Language', 'vi');
            try {
                app()->setLocale($locale);
            } catch (\Exception $e) {
                
            }
        }
        return $next($request);
    }
}
