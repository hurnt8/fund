<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        // Appelé par le Service Worker sans accès au token CSRF (pushsubscriptionchange)
        'app/push/subscribe',
        'app/push/unsubscribe',
    ];
}
