<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Mail\OtpMail;
use App\Models\ClientNotification;
use App\Models\LoanRequest;
use App\Models\Transfer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AppController extends Controller
{
    public function index()
    {
        $user  = Auth::user();
        $loans = LoanRequest::where('client_id', $user->id)->latest()->get();

        $activeLoans  = $loans->whereIn('status', [
            LoanRequest::STATUS_CONTRACT_SENT,
            LoanRequest::STATUS_CONTRACT_SIGNED,
            LoanRequest::STATUS_FINALIZED,
        ])->values();

        $pendingLoans = $loans->whereIn('status', [
            LoanRequest::STATUS_DRAFT,
            LoanRequest::STATUS_PENDING,
            LoanRequest::STATUS_VALIDATED,
        ])->values();

        $recentTransfers = Transfer::where('user_id', $user->id)
            ->latest()
            ->limit(5)
            ->get();

        $unreadCount = ClientNotification::where('user_id', $user->id)
            ->whereNull('read_at')
            ->count();

        return view('client.app.home', compact(
            'user', 'loans', 'activeLoans', 'pendingLoans', 'recentTransfers', 'unreadCount'
        ));
    }

    public function loans()
    {
        $user  = Auth::user();
        $loans = LoanRequest::where('client_id', $user->id)->latest()->get();

        return view('client.app.loans.index', compact('user', 'loans'));
    }

    public function loanShow(LoanRequest $loan)
    {
        $user = Auth::user();
        abort_unless($loan->client_id === $user->id, 403);

        $loan->load(['admin']);

        $principal = (float) $loan->amount;
        $total     = (float) $loan->total_with_interest;
        $interest  = max(0, $total - $principal);

        return view('client.app.loans.show', compact('user', 'loan', 'principal', 'interest', 'total'));
    }

    public function analytics()
    {
        $user  = Auth::user();
        $loans = LoanRequest::where('client_id', $user->id)
            ->whereNotNull('amortization_schedule')
            ->get();

        $monthlyData = [];
        foreach ($loans as $loan) {
            $schedule = $loan->amortization_schedule ?? [];
            foreach ($schedule as $row) {
                $key = 'M' . $row['month'];
                $monthlyData[$key] = ($monthlyData[$key] ?? 0) + (float) ($row['payment'] ?? 0);
            }
        }

        $totalPaid = Transfer::where('user_id', $user->id)
            ->where('type', 'send')
            ->where('status', 'completed')
            ->sum('amount');

        $totalReceived = (float) $user->balance;

        return view('client.app.analytics', compact(
            'user', 'loans', 'monthlyData', 'totalPaid', 'totalReceived'
        ));
    }

    public function profile()
    {
        $user      = Auth::user();
        $transfers = Transfer::where('user_id', $user->id)->latest()->limit(3)->get();

        return view('client.app.profile', compact('user', 'transfers'));
    }

    public function updateProfile(Request $request)
    {
        $user      = Auth::user();
        $validated = $request->validate([
            'locale' => 'nullable|in:fr,en,pl,es',
            'phone'  => 'nullable|string|max:30',
        ]);

        if (!empty($validated['locale'])) {
            $user->update(['locale' => $validated['locale']]);
        }

        return back()->with('success', __('app.profile_saved'));
    }

    // ── Notifications ────────────────────────────────────────────────────────

    public function notifications()
    {
        $user          = Auth::user();
        $notifications = ClientNotification::where('user_id', $user->id)
            ->latest()
            ->get();

        ClientNotification::where('user_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return view('client.app.notifications', compact('user', 'notifications'));
    }

    public function notificationRead(int $id)
    {
        $user = Auth::user();
        ClientNotification::where('id', $id)
            ->where('user_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['ok' => true]);
    }

    public function notificationReadAll()
    {
        $user = Auth::user();
        ClientNotification::where('user_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return back();
    }

    public function notificationCount()
    {
        $user    = Auth::user();
        $count   = ClientNotification::where('user_id', $user->id)->whereNull('read_at')->count();
        $newest  = ClientNotification::where('user_id', $user->id)->whereNull('read_at')->latest()->first();

        return response()->json([
            'count' => $count,
            'type'  => $newest?->type,
        ]);
    }

    // ── Edit profile ─────────────────────────────────────────────────────────

    public function paymentMethods()
    {
        $user = Auth::user();
        return view('client.app.payment-methods', compact('user'));
    }

    public function editProfile()
    {
        $user       = Auth::user();
        $pendingOtp = session()->has('profile_pending');

        return view('client.app.edit-profile', compact('user', 'pendingOtp'));
    }

    public function saveProfile(Request $request)
    {
        $user      = Auth::user();
        $validated = $request->validate([
            'name'    => 'required|string|max:100',
            'phone'   => 'nullable|string|max:30',
            'address' => 'nullable|string|max:255',
            'email'   => 'required|email|max:191',
        ]);

        // Toujours sauvegarder nom, tel, adresse immédiatement
        $user->update([
            'name'    => $validated['name'],
            'phone'   => $validated['phone'] ?? $user->phone,
            'address' => $validated['address'] ?? $user->address,
        ]);

        // OTP uniquement si l'email est vraiment différent
        $newEmail = strtolower(trim($validated['email']));
        if ($newEmail !== strtolower(trim($user->email))) {
            $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            Cache::put('profile_otp_' . $user->id, Hash::make($otp), 600);
            session(['profile_pending' => ['email' => $newEmail]]);
            try {
                Mail::to($user->email)->send(new OtpMail($otp, $user));
            } catch (\Throwable) {
                return redirect()->route('client.app.profile')->with('success', __('app.profile_saved'));
            }
            return redirect()->route('client.app.profile.edit')->with('otp_sent', true);
        }

        return redirect()->route('client.app.profile')->with('success', __('app.profile_saved'));
    }

    public function confirmProfileOtp(Request $request)
    {
        $user    = Auth::user();
        $otp     = $request->input('otp');
        $pending = session('profile_pending');
        $cached  = Cache::get('profile_otp_' . $user->id);

        if (!$pending || !$cached || !Hash::check($otp, $cached)) {
            return back()->withErrors(['otp' => __('auth.otp_invalid')]);
        }

        $user->update(['email' => $pending['email']]);
        Cache::forget('profile_otp_' . $user->id);
        session()->forget('profile_pending');

        return redirect()->route('client.app.profile')->with('success', __('app.profile_saved'));
    }

    // ── Change password (logged in) ──────────────────────────────────────────

    public function changePassword()
    {
        $user = Auth::user();
        return view('client.app.change-password', compact('user'));
    }

    public function savePassword(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|min:8|confirmed',
        ]);

        if (!Hash::check($request->input('current_password'), $user->password)) {
            return back()->withErrors(['current_password' => __('app.wrong_current_password')]);
        }

        $user->update(['password' => Hash::make($request->input('password'))]);

        return redirect()->route('client.app.profile')->with('success', __('app.password_changed'));
    }

    // ── PWA ─────────────────────────────────────────────────────────────────

    public function manifest()
    {
        $data = [
            'name'             => 'Credixa — Espace Client',
            'short_name'       => 'Credixa',
            'start_url'        => '/app',
            'scope'            => '/app',
            'display'          => 'standalone',
            'orientation'      => 'portrait',
            'background_color' => '#0A1628',
            'theme_color'      => '#C8A951',
            'lang'             => app()->getLocale(),
            'icons'            => [
                ['src' => '/images/icon-192.svg', 'sizes' => '192x192', 'type' => 'image/svg+xml', 'purpose' => 'any maskable'],
                ['src' => '/images/icon-512.svg', 'sizes' => '512x512', 'type' => 'image/svg+xml', 'purpose' => 'any maskable'],
            ],
        ];

        return response()->json($data, 200, [
            'Content-Type' => 'application/manifest+json',
        ]);
    }

    public function serviceWorker()
    {
        $js = <<<'JS'
const CACHE = 'credixa-v5';
const SHELL = ['/app', '/login'];

self.addEventListener('install', e => {
    e.waitUntil(
        caches.open(CACHE)
            .then(c => c.addAll(SHELL))
            .then(() => self.skipWaiting())
    );
});

self.addEventListener('activate', e => {
    e.waitUntil(
        caches.keys()
            .then(keys => Promise.all(keys.filter(k => k !== CACHE).map(k => caches.delete(k))))
            .then(() => self.clients.claim())
    );
});

self.addEventListener('fetch', e => {
    if (e.request.method !== 'GET') return;

    const url = new URL(e.request.url);

    if (url.pathname.startsWith('/build/assets/')) {
        e.respondWith(
            caches.open(CACHE).then(c =>
                c.match(e.request).then(cached => {
                    if (cached) return cached;
                    return fetch(e.request).then(resp => {
                        if (resp.ok) c.put(e.request, resp.clone());
                        return resp;
                    });
                })
            )
        );
        return;
    }

    e.respondWith(
        fetch(e.request)
            .then(resp => {
                if (resp.ok) {
                    const clone = resp.clone();
                    caches.open(CACHE).then(c => c.put(e.request, clone));
                }
                return resp;
            })
            .catch(() =>
                caches.match(e.request)
                    .then(cached => cached || caches.match('/app'))
            )
    );
});
JS;
        return response($js, 200, ['Content-Type' => 'application/javascript']);
    }
}
