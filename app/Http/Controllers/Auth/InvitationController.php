<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class InvitationController extends Controller
{
    public function show(string $token)
    {
        $user = User::where('invitation_token', $token)->firstOrFail();

        return view('auth.invitation', compact('user', 'token'));
    }

    public function activate(Request $request, string $token)
    {
        $user = User::where('invitation_token', $token)->firstOrFail();

        $request->validate([
            'password'              => 'required|min:8|confirmed',
            'password_confirmation' => 'required',
        ]);

        $user->update([
            'password'         => Hash::make($request->password),
            'invitation_token' => null,
            'email_verified_at'=> now(),
        ]);

        Auth::login($user);

        if ($user->type === 'staff') {
            return redirect()->route('admin.dashboard')
                             ->with('success', 'Compte activé avec succès. Bienvenue !');
        }

        return redirect()->route('client.dashboard')
                         ->with('success', 'Compte activé avec succès. Bienvenue !');
    }
}
