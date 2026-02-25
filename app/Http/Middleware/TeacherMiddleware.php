<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TeacherMiddleware
{
    /**
     * Handle an incoming request.
     * Allows TEACHER, VP, and ADMIN
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || !in_array(auth()->user()->role, ['TEACHER', 'VP', 'ADMIN'])) {
            return redirect()->route('wrongway');
        }

        return $next($request);
    }
}
