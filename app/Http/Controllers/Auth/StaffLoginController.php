<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StaffLoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return $this->redirectAuthenticated(Auth::user());
        }
        return view('auth.login-staff');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();

            if ($user->type !== 'staff') {
                Auth::logout();
                return back()->withErrors(['email' => 'Accès refusé. Ce portail est réservé au personnel autorisé.']);
            }

            if (!$user->hasAnyRole(['admin', 'super-admin'])) {
                Auth::logout();
                return back()->withErrors(['email' => 'Votre compte ne dispose pas des droits nécessaires.']);
            }

            $request->session()->regenerate();
            return $this->redirectAuthenticated($user);
        }

        return back()->withErrors(['email' => 'Identifiants incorrects.'])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('staff.login');
    }

    private function redirectAuthenticated($user)
    {
        if ($user->hasRole('super-admin')) {
            return redirect()->route('super-admin.dashboard');
        }
        return redirect()->route('admin.dashboard');
    }
}
