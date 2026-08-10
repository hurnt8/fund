<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetClientLocale
{
    private const SUPPORTED = ['fr', 'en', 'pl', 'es', 'ro', 'hr'];

    public function handle(Request $request, Closure $next): Response
    {
        App::setLocale($this->resolve($request));
        return $next($request);
    }

    private function resolve(Request $request): string
    {
        // 1. Langue du profil client (base de données)
        $user = $request->user();
        if ($user && in_array($user->locale, self::SUPPORTED, true)) {
            return $user->locale;
        }

        // 2. Header Accept-Language du navigateur
        foreach (explode(',', $request->header('Accept-Language', '')) as $part) {
            $code = strtolower(substr(trim($part), 0, 2));
            if (in_array($code, self::SUPPORTED, true)) {
                return $code;
            }
        }

        return 'fr';
    }
}
