<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\LoanRequest;
use App\Models\Transfer;
use Illuminate\Support\Facades\Auth;

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

        return view('client.app.home', compact(
            'user', 'loans', 'activeLoans', 'pendingLoans', 'recentTransfers'
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

        // Agreger les paiements mensuels par mois
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

    public function updateProfile(\Illuminate\Http\Request $request)
    {
        $user = Auth::user();
        $validated = $request->validate([
            'locale' => 'nullable|in:fr,en,pl,es',
            'phone'  => 'nullable|string|max:30',
        ]);

        if (!empty($validated['locale'])) {
            $user->update(['locale' => $validated['locale']]);
        }

        return back()->with('success', __('app.profile_saved'));
    }

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

    // Hashed assets (/build/assets/…) → cache-first (immutable filenames)
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

    // HTML navigation → network-first (always get fresh HTML with current asset hashes)
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
