<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientLoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return $this->redirectAuthenticated(Auth::user());
        }
        return view('auth.login-client');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();

            if ($user->type !== 'client') {
                Auth::logout();
                return back()->withErrors(['email' => 'Ce portail est réservé aux clients.']);
            }

            $request->session()->regenerate();
            return $this->redirectAuthenticated($user);
        }

        return back()->withErrors(['email' => 'Email ou mot de passe incorrect.'])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        $isStaff = Auth::check() && Auth::user()->type === 'staff';

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route($isStaff ? 'staff.login' : 'login');
    }

    private function redirectAuthenticated($user)
    {
        if ($user->hasRole('super-admin')) {
            return redirect()->route('super-admin.dashboard');
        }
        if ($user->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('client.dashboard');
    }
}
