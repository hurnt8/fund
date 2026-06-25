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

        // Base réelle du serveur (host + port si non standard)
        $serverBase = $request->getSchemeAndHttpHost(); // ex: "http://127.0.0.1:8000"

        $baseOf = function (string $url): string {
            $scheme = parse_url($url, PHP_URL_SCHEME) ?? 'http';
            $host   = parse_url($url, PHP_URL_HOST)   ?? '';
            $port   = parse_url($url, PHP_URL_PORT);
            $base   = $scheme . '://' . $host;
            if ($port) {
                $base .= ':' . $port;
            }
            return $base;
        };

        if ($origin)  return $baseOf($origin)  === $serverBase;
        if ($referer) return $baseOf($referer) === $serverBase;

        // Pas d'Origin ni Referer → même serveur, autorisé
        return true;
    }
}
