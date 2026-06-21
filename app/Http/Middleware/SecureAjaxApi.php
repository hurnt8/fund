<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Protège les endpoints AJAX internes contre tout accès externe ou non authentifié.
 * - Retourne JSON 401 si non connecté (jamais une redirection vers /login)
 * - Retourne JSON 403 si le rôle ne correspond pas
 * - Bloque si la requête n'est pas identifiée comme AJAX interne
 */
class SecureAjaxApi
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // 1. Non authentifié → 401 JSON
        if (! Auth::check()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        // 2. Vérification de rôle si précisé
        if (! empty($roles)) {
            $user = Auth::user();
            $hasRole = false;
            foreach ($roles as $role) {
                if ($user->hasRole($role)) {
                    $hasRole = true;
                    break;
                }
            }
            if (! $hasRole) {
                return response()->json(['error' => 'Forbidden.'], 403);
            }
        }

        // 3. Vérifier que la requête provient de l'application (header ou Accept JSON)
        $isAjax    = $request->header('X-Requested-With') === 'XMLHttpRequest';
        $wantsJson = $request->expectsJson();
        $sameHost  = $this->isSameHost($request);

        if (! $isAjax && ! $wantsJson) {
            return response()->json(['error' => 'Invalid request type.'], 400);
        }

        if (! $sameHost) {
            return response()->json(['error' => 'Cross-origin access denied.'], 403);
        }

        return $next($request);
    }

    private function isSameHost(Request $request): bool
    {
        $origin  = $request->header('Origin', '');
        $referer = $request->header('Referer', '');
        $appHost = parse_url(config('app.url'), PHP_URL_HOST);

        if ($origin) {
            return parse_url($origin, PHP_URL_HOST) === $appHost;
        }
        if ($referer) {
            return parse_url($referer, PHP_URL_HOST) === $appHost;
        }

        // Pas d'Origin ni Referer → autorisé seulement si même hôte dans la requête
        return $request->getHost() === $appHost;
    }
}
