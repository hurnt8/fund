<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\OtpMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;

class OtpController extends Controller
{
    private const OTP_TTL      = 600;  // 10 minutes
    private const MAX_ATTEMPTS = 5;
    private const MAX_RESENDS  = 3;

    public function show(Request $request)
    {
        $userId = $request->session()->get('otp_user_id');
        if (!$userId) {
            return redirect('/login');
        }

        $user = User::find($userId);
        if (!$user) {
            $request->session()->forget(['otp_user_id', 'otp_remember']);
            return redirect('/login');
        }

        $masked = $this->maskEmail($user->email);

        return view('auth.otp-verify', compact('masked'));
    }

    public function verify(Request $request)
    {
        $userId = $request->session()->get('otp_user_id');
        if (!$userId) {
            return redirect('/login');
        }

        $request->validate(['code' => 'required|string|digits:6']);

        $user = User::find($userId);
        if (!$user) {
            return redirect('/login');
        }

        $attemptKey = 'otp_attempts_' . $userId;
        if (RateLimiter::tooManyAttempts($attemptKey, self::MAX_ATTEMPTS)) {
            $seconds = RateLimiter::availableIn($attemptKey);
            return back()->withErrors(['code' => __('auth.otp_too_many', ['seconds' => $seconds])]);
        }

        $cached = Cache::get('otp_' . $userId);

        if (!$cached || !Hash::check($request->input('code'), $cached)) {
            RateLimiter::hit($attemptKey, self::OTP_TTL);
            $remaining = self::MAX_ATTEMPTS - RateLimiter::attempts($attemptKey);
            return back()->withErrors(['code' => __('auth.otp_invalid', ['remaining' => max(0, $remaining)])]);
        }

        // Valid — clean up all OTP data
        Cache::forget('otp_' . $userId);
        RateLimiter::clear($attemptKey);
        RateLimiter::clear('otp_resend_' . $userId);

        $remember = (bool) $request->session()->pull('otp_remember', false);
        $request->session()->forget('otp_user_id');

        Auth::login($user, $remember);
        $request->session()->regenerate();

        return redirect()->route('client.app.home');
    }

    public function resend(Request $request)
    {
        $userId = $request->session()->get('otp_user_id');
        if (!$userId) {
            return response()->json(['error' => __('auth.otp_session_expired')], 422);
        }

        $user = User::find($userId);
        if (!$user) {
            return response()->json(['error' => __('auth.otp_session_expired')], 422);
        }

        $resendKey = 'otp_resend_' . $userId;
        if (RateLimiter::tooManyAttempts($resendKey, self::MAX_RESENDS)) {
            return response()->json(['error' => __('auth.otp_resend_limit')], 429);
        }
        RateLimiter::hit($resendKey, self::OTP_TTL);

        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        Cache::put('otp_' . $userId, Hash::make($otp), self::OTP_TTL);
        Cache::forget('otp_attempts_' . $userId);

        try {
            Mail::to($user->email)->send(new OtpMail($otp, $user));
        } catch (\Throwable) {
            // Mail failure — do not reveal code, just inform user
            return response()->json(['error' => __('auth.otp_send_failed')], 500);
        }

        return response()->json(['message' => __('auth.otp_resend_success'), 'expires_in' => 120]);
    }

    private function maskEmail(string $email): string
    {
        [$local, $domain] = explode('@', $email, 2);
        $visible = min(3, strlen($local));
        return substr($local, 0, $visible) . str_repeat('*', max(0, strlen($local) - $visible)) . '@' . $domain;
    }
}
