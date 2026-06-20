<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->route('locale')
            ?? $request->query('lang')
            ?? session('locale');

        if (in_array($locale, ['fr', 'en', 'pl', 'es'])) {
            App::setLocale($locale);
            session(['locale' => $locale]);
        }

        return $next($request);
    }
}
