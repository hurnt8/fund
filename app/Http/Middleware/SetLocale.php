<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    private const SUPPORTED = ['en', 'pl', 'es'];

    public function handle(Request $request, Closure $next): Response
    {
        $locale = $this->resolve($request);
        App::setLocale($locale);
        return $next($request);
    }

    private function resolve(Request $request): string
    {
        // 1. Choix explicite : paramètre URL / query string / session
        $explicit = $request->route('locale')
            ?? $request->query('lang')
            ?? session('locale');

        if (in_array($explicit, self::SUPPORTED, true)) {
            session(['locale' => $explicit]);
            return $explicit;
        }

        // 2. Locale enregistrée en base (utilisateur connecté)
        $user = $request->user();
        if ($user && in_array($user->locale, self::SUPPORTED, true)) {
            return $user->locale;
        }

        // 3. Langue préférée du navigateur / appareil (Accept-Language)
        $header = $request->header('Accept-Language', '');
        foreach (explode(',', $header) as $part) {
            $tag  = trim(explode(';', $part)[0]);       // ex: "fr-FR" ou "en"
            $code = strtolower(substr($tag, 0, 2));
            if (in_array($code, self::SUPPORTED, true)) {
                return $code;
            }
        }

        // 4. Défaut
        return 'en';
    }
}
