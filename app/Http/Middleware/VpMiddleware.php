<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VpMiddleware
{
    /**
     * Handle an incoming request.
     * Allows VP and ADMIN
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || !in_array(auth()->user()->role, ['VP', 'ADMIN'])) {
            return redirect()->route('wrongway');
        }

        return $next($request);
    }
}
