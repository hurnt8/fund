<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
 * Toutes les routes API exigent un token Sanctum valide.
 * Aucune donnée n'est accessible sans authentification.
 */
Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {

    // Utilisateur courant
    Route::get('/user', function (Request $request) {
        $user = $request->user();
        return response()->json([
            'id'    => $user->id,
            'name'  => $user->name,
            'email' => $user->email,
            'role'  => $user->getRoleNames()->first(),
        ]);
    });

});

// Toute route non définie → 404 JSON (jamais de redirection)
Route::fallback(function () {
    return response()->json(['error' => 'Not found.'], 404);
});
