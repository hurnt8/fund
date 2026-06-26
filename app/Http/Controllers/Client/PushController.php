<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\PushSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PushController extends Controller
{
    public function subscribe(Request $request)
    {
        $request->validate([
            'endpoint'   => 'required|string',
            'public_key' => 'required|string',
            'auth_token' => 'required|string',
        ]);

        $userId = Auth::id();

        // Si cet endpoint appartenait à un autre utilisateur, le libérer d'abord
        PushSubscription::where('endpoint', $request->endpoint)
            ->where('user_id', '!=', $userId)
            ->delete();

        PushSubscription::updateOrCreate(
            ['endpoint' => $request->endpoint],
            [
                'user_id'    => $userId,
                'public_key' => $request->public_key,
                'auth_token' => $request->auth_token,
            ]
        );

        // Mémoriser l'endpoint dans la session pour nettoyage à la déconnexion
        $request->session()->put('push_endpoint', $request->endpoint);

        return response()->json(['status' => 'ok']);
    }

    public function unsubscribe(Request $request)
    {
        $request->validate(['endpoint' => 'required|string']);

        PushSubscription::where('user_id', Auth::id())
            ->where('endpoint', $request->endpoint)
            ->delete();

        $request->session()->forget('push_endpoint');

        return response()->json(['status' => 'ok']);
    }
}
