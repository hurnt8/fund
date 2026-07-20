<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        // Pour les requêtes web classiques (formulaires), remplace la page
        // d'erreur brute de Laravel par une redirection avec un message flash,
        // pour toute exception qui n'est pas déjà bien gérée nativement
        // (validation, authentification, 404/403/405...). Les requêtes qui
        // attendent du JSON (API) ne sont pas concernées.
        $this->renderable(function (Throwable $e, $request) {
            if ($request->expectsJson()) {
                return null;
            }

            if ($e instanceof \Illuminate\Validation\ValidationException
                || $e instanceof \Illuminate\Auth\AuthenticationException
                || $e instanceof \Illuminate\Auth\Access\AuthorizationException) {
                return null;
            }

            if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface
                && in_array($e->getStatusCode(), [404, 403, 405], true)) {
                return null;
            }

            $message = match (true) {
                $e instanceof \Illuminate\Http\Exceptions\PostTooLargeException =>
                    'Le ou les fichiers envoyés sont trop volumineux pour être transmis. Réduisez leur taille et réessayez.',
                $e instanceof \Illuminate\Session\TokenMismatchException =>
                    'Votre session a expiré. Merci de renvoyer le formulaire.',
                default =>
                    'Une erreur est survenue lors du traitement de votre demande. Merci de vérifier les champs et de réessayer.',
            };

            return redirect()->back()
                ->withInput($request->except(['password', 'password_confirmation', 'current_password']))
                ->with('error', $message);
        });
    }
}
