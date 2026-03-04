<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Localization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $supportedLanguages = ['en', 'id'];

        $locale = session('locale')
            ?? $request->cookie('language')
            ?? config('app.locale');

        if (in_array($locale, $supportedLanguages)) {
            app()->setLocale($locale);
        }

        return $next($request);
    }
}
