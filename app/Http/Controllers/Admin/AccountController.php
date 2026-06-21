<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccountMovement;
use App\Models\ClientNotification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AccountController extends Controller
{
    public function index()
    {
        $auth    = Auth::user();
        $isSuperAdmin = $auth->hasRole('super-admin');

        $query = User::where('type', 'client')->withCount('clientLoans');

        if (! $isSuperAdmin) {
            $adminId = $auth->id;
            $query->where(function ($q) use ($adminId) {
                $q->where('created_by', $adminId)
                  ->orWhereHas('clientLoans', fn ($q2) => $q2->where('admin_id', $adminId));
            });
        }

        $clients = $query->orderBy('name')->get();

        return view('admin.accounts.index', compact('clients', 'isSuperAdmin'));
    }

    public function show(User $account)
    {
        $this->authorizeAccount($account);

        $movements = AccountMovement::where('user_id', $account->id)
            ->with('admin:id,name')
            ->latest()
            ->paginate(20);

        return view('admin.accounts.show', compact('account', 'movements'));
    }

    public function credit(Request $request, User $account)
    {
        $this->authorizeAccount($account);

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01|max:9999999',
            'note'   => 'nullable|string|max:255',
        ]);

        $cur = $account->currency ?? config('credixa.default_currency');

        DB::transaction(function () use ($account, $validated, $cur) {
            $before = (float) $account->balance;
            $account->increment('balance', $validated['amount']);

            AccountMovement::create([
                'user_id'        => $account->id,
                'admin_id'       => Auth::id(),
                'type'           => 'credit',
                'amount'         => $validated['amount'],
                'currency'       => $cur,
                'balance_before' => $before,
                'balance_after'  => $before + $validated['amount'],
                'note'           => $validated['note'] ?? null,
            ]);

            ClientNotification::forUser(
                $account->id,
                'system',
                'Compte crédité',
                '+' . number_format($validated['amount'], 2, ',', ' ') . ' ' . $cur
                    . ($validated['note'] ? ' — ' . $validated['note'] : ''),
                ['amount' => $validated['amount'], 'currency' => $cur]
            );
        });

        return back()->with('success', 'Compte crédité de ' . number_format($validated['amount'], 2, ',', ' ') . ' ' . $cur . '.');
    }

    public function debit(Request $request, User $account)
    {
        $this->authorizeAccount($account);

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01|max:9999999',
            'note'   => 'nullable|string|max:255',
        ]);

        $cur    = $account->currency ?? config('credixa.default_currency');
        $before = (float) $account->balance;

        DB::transaction(function () use ($account, $validated, $before, $cur) {
            $account->decrement('balance', $validated['amount']);

            AccountMovement::create([
                'user_id'        => $account->id,
                'admin_id'       => Auth::id(),
                'type'           => 'debit',
                'amount'         => $validated['amount'],
                'currency'       => $cur,
                'balance_before' => $before,
                'balance_after'  => $before - $validated['amount'],
                'note'           => $validated['note'] ?? null,
            ]);

            ClientNotification::forUser(
                $account->id,
                'system',
                'Mouvement sur votre compte',
                '-' . number_format($validated['amount'], 2, ',', ' ') . ' ' . $cur
                    . ($validated['note'] ? ' — ' . $validated['note'] : ''),
                ['amount' => $validated['amount'], 'currency' => $cur]
            );
        });

        return back()->with('success', 'Compte débité de ' . number_format($validated['amount'], 2, ',', ' ') . ' ' . ($account->currency ?? config('credixa.default_currency')) . '.');
    }

    private function authorizeAccount(User $client): void
    {
        $auth = Auth::user();
        if ($auth->hasRole('super-admin')) return;

        $adminId   = $auth->id;
        $isManaged = $client->created_by === $adminId
            || $client->clientLoans()->where('admin_id', $adminId)->exists();

        abort_unless($isManaged, 403, 'Accès non autorisé à ce compte.');
    }
}
