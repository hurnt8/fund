<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Mail\OtpMail;
use App\Models\AccountMovement;
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
        // Brouillons non visibles dans le compte client
        $loans = LoanRequest::where('client_id', $user->id)
            ->where('status', '!=', LoanRequest::STATUS_DRAFT)
            ->latest()->get();

        $activeLoans  = $loans->whereIn('status', [
            LoanRequest::STATUS_CONTRACT_SENT,
            LoanRequest::STATUS_CONTRACT_SIGNED,
            LoanRequest::STATUS_FINALIZED,
        ])->values();

        $pendingLoans = $loans->whereIn('status', [
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
        // Les brouillons ne sont pas visibles dans l'espace client
        $loans = LoanRequest::where('client_id', $user->id)
            ->where('status', '!=', LoanRequest::STATUS_DRAFT)
            ->latest()->get();

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
        // Les dossiers rejetés ne sont pas comptabilisés dans les analytiques
        $loans = LoanRequest::where('client_id', $user->id)
            ->where('status', '!=', LoanRequest::STATUS_REJECTED)
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

        // Virements envoyés validés
        $totalPaid = Transfer::where('user_id', $user->id)
            ->where('type', 'send')
            ->where('status', Transfer::STATUS_COMPLETED)
            ->sum('amount');

        // Virements en attente de validation
        $pendingTransfers = Transfer::where('user_id', $user->id)
            ->where('type', 'send')
            ->whereIn('status', [Transfer::STATUS_PENDING, Transfer::STATUS_FEE_REQUIRED])
            ->get();

        $pendingAmount = $pendingTransfers->sum('amount');

        // Total crédits reçus sur le compte (admin + prêts finalisés)
        $totalReceived = AccountMovement::where('user_id', $user->id)
            ->where('type', 'credit')
            ->sum('amount');

        return view('client.app.analytics', compact(
            'user', 'loans', 'monthlyData',
            'totalPaid', 'totalReceived',
            'pendingTransfers', 'pendingAmount'
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

    // ── Account movements ────────────────────────────────────────────────────

    public function movements()
    {
        $user = Auth::user();

        // Admin credit/debit operations
        $adminMvts = AccountMovement::where('user_id', $user->id)
            ->with('admin:id,name')
            ->get()
            ->map(fn ($m) => (object) [
                'source'       => 'account',
                'type'         => $m->type,
                'amount'       => (float) $m->amount,
                'currency'     => $m->currency,
                'label'        => $m->type === 'credit' ? 'Crédit compte' : 'Débit compte',
                'sub'          => $m->note ?? ($m->admin?->name ?? 'Système'),
                'balance_after' => (float) $m->balance_after,
                'has_balance'  => true,
                'status'       => 'completed',
                'created_at'   => $m->created_at,
            ]);

        // Client transfers (send = debit, receive = credit)
        $transfers = Transfer::where('user_id', $user->id)
            ->whereIn('status', [
                Transfer::STATUS_PENDING,
                Transfer::STATUS_COMPLETED,
                Transfer::STATUS_FEE_REQUIRED,
                Transfer::STATUS_REJECTED,
            ])
            ->get()
            ->map(fn ($t) => (object) [
                'source'       => 'transfer',
                'type'         => $t->type === 'send' ? 'debit' : 'credit',
                'amount'       => (float) $t->amount,
                'currency'     => $t->currency,
                'label'        => $t->type === 'send'
                    ? 'Virement → ' . $t->beneficiary_name
                    : 'Virement reçu',
                'sub'          => $t->reference . ($t->note ? ' — ' . $t->note : ''),
                'balance_after' => null,
                'has_balance'  => false,
                'status'       => $t->status,
                'created_at'   => $t->created_at,
            ]);

        $merged = $adminMvts->merge($transfers)
            ->sortByDesc('created_at')
            ->values();

        return view('client.app.movements', compact('user', 'merged'));
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
const CACHE = 'credixa-v6';
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

    /* Ignorer chrome-extension://, moz-extension://, etc. */
    if (!url.protocol.startsWith('http')) return;

    if (url.pathname.startsWith('/build/assets/')) {
        e.respondWith(
            caches.open(CACHE).then(c =>
                c.match(e.request).then(cached => {
                    if (cached) return cached;
                    return fetch(e.request).then(resp => {
                        if (resp.ok) {
                            const clone = resp.clone();
                            c.put(e.request, clone);
                        }
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
