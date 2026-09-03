<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceJsonMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Allow public prescription verification to serve HTML web views to browsers
        if ($request->is('*public/prescriptions*') && ($request->query('format') === 'html' || str_contains($request->header('Accept', ''), 'text/html'))) {
            return $next($request);
        }

        $request->headers->set('Accept', 'application/json');
        $request->headers->set('Content-Type', 'application/json');

        return $next($request);
    }
}
