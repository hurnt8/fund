<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\TransferActionMail;
use App\Models\ClientNotification;
use App\Models\Invoice;
use App\Models\Transfer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class TransferValidationController extends Controller
{
    public function index(Request $request)
    {
        $auth        = Auth::user();
        $isSuperAdmin = $auth->hasRole('super-admin');

        $query = Transfer::with(['user:id,name,email,currency'])
            ->where('type', 'send');

        if (! $isSuperAdmin) {
            $adminId = $auth->id;
            $query->whereHas('user', function ($q) use ($adminId) {
                $q->where('created_by', $adminId)
                  ->orWhereHas('clientLoans', fn ($q2) => $q2->where('admin_id', $adminId));
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            $query->whereIn('status', [Transfer::STATUS_PENDING, Transfer::STATUS_FEE_REQUIRED]);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('reference', 'like', "%$s%")
                  ->orWhereHas('user', fn ($q2) => $q2->where('name', 'like', "%$s%")->orWhere('email', 'like', "%$s%"));
            });
        }

        $transfers = $query->latest()->paginate(20)->appends($request->query());

        $stats = [
            'pending'      => $this->baseQuery($isSuperAdmin)->where('status', Transfer::STATUS_PENDING)->count(),
            'fee_required' => $this->baseQuery($isSuperAdmin)->where('status', Transfer::STATUS_FEE_REQUIRED)->count(),
            'completed'    => $this->baseQuery($isSuperAdmin)->where('status', Transfer::STATUS_COMPLETED)->count(),
            'rejected'     => $this->baseQuery($isSuperAdmin)->where('status', Transfer::STATUS_REJECTED)->count(),
        ];

        return view('admin.transfers.index', compact('transfers', 'stats', 'isSuperAdmin'));
    }

    public function approve(Request $request, Transfer $transfer)
    {
        $this->authorizeTransfer($transfer);
        abort_unless(in_array($transfer->status, [Transfer::STATUS_PENDING, Transfer::STATUS_FEE_REQUIRED]), 422, 'Statut invalide.');

        $request->validate(['admin_note' => 'nullable|string|max:500']);

        DB::transaction(function () use ($transfer, $request) {
            $transfer->update([
                'status'       => Transfer::STATUS_COMPLETED,
                'admin_id'     => Auth::id(),
                'admin_note'   => $request->admin_note,
                'processed_at' => now(),
            ]);
        });

        $client = $transfer->user;
        $cur    = $transfer->currency;
        $amount = number_format($transfer->amount, 2, ',', ' ');

        ClientNotification::forUser(
            $client->id,
            'transfer',
            'Virement validé',
            "Votre virement {$transfer->reference} de {$amount} {$cur} vers {$transfer->beneficiary_name} a été validé.",
            ['transfer_id' => $transfer->id, 'reference' => $transfer->reference]
        );

        try {
            Mail::to($client->email)
                ->locale($client->locale ?? 'fr')
                ->send(new TransferActionMail($transfer, 'approved'));
        } catch (\Throwable $e) {
            Log::error('TransferActionMail (approved) failed: ' . $e->getMessage());
        }

        return back()->with('success', "Virement {$transfer->reference} validé.");
    }

    public function reject(Request $request, Transfer $transfer)
    {
        $this->authorizeTransfer($transfer);
        abort_unless(in_array($transfer->status, [Transfer::STATUS_PENDING, Transfer::STATUS_FEE_REQUIRED]), 422, 'Statut invalide.');

        $request->validate([
            'admin_note' => 'nullable|string|max:500',
        ]);

        $client = $transfer->user;
        $amount = (float) $transfer->amount;
        $cur    = $transfer->currency;

        DB::transaction(function () use ($transfer, $request, $client, $amount) {
            $transfer->update([
                'status'       => Transfer::STATUS_REJECTED,
                'admin_id'     => Auth::id(),
                'admin_note'   => $request->admin_note,
                'processed_at' => now(),
            ]);

            // Rembourser les fonds réservés
            $client->increment('balance', $amount);
        });

        ClientNotification::forUser(
            $client->id,
            'transfer',
            'Virement rejeté',
            "Votre virement {$transfer->reference} de " . number_format($amount, 2, ',', ' ') . " {$cur} a été rejeté. Vos fonds ont été recrédités."
                . ($request->admin_note ? ' Motif : ' . $request->admin_note : ''),
            ['transfer_id' => $transfer->id, 'reference' => $transfer->reference]
        );

        try {
            Mail::to($client->email)
                ->locale($client->locale ?? 'fr')
                ->send(new TransferActionMail($transfer, 'rejected'));
        } catch (\Throwable $e) {
            Log::error('TransferActionMail (rejected) failed: ' . $e->getMessage());
        }

        return back()->with('success', "Virement {$transfer->reference} rejeté — solde recrédité.");
    }

    public function invoice(Request $request, Transfer $transfer)
    {
        $this->authorizeTransfer($transfer);
        abort_unless($transfer->status === Transfer::STATUS_PENDING, 422, 'Ce virement n\'est pas en attente.');

        $data = $request->validate([
            'fee_amount'  => 'required|numeric|min:0.01|max:9999999',
            'description' => 'nullable|string|max:500',
        ]);

        $client   = $transfer->user;
        $currency = $transfer->currency;

        DB::transaction(function () use ($transfer, $data, $client, $currency, $request) {
            $desc = $data['description'] ?: 'Frais de traitement pour le virement ' . $transfer->reference;

            $invoice = Invoice::create([
                'reference'   => Invoice::generateReference(),
                'admin_id'    => Auth::id(),
                'client_id'   => $client->id,
                'currency'    => $currency,
                'subtotal'    => $data['fee_amount'],
                'tax_rate'    => 0,
                'tax_amount'  => 0,
                'total'       => $data['fee_amount'],
                'status'      => Invoice::STATUS_SENT,
                'issue_date'  => now()->toDateString(),
                'due_date'    => now()->addDays(7)->toDateString(),
                'description' => $desc,
                'note'        => 'Facture liée au virement ' . $transfer->reference,
                'items'       => [[
                    'description' => $desc,
                    'quantity'    => 1,
                    'unit_price'  => $data['fee_amount'],
                    'total'       => $data['fee_amount'],
                ]],
                'sent_at'     => now(),
            ]);

            $transfer->update([
                'status'     => Transfer::STATUS_FEE_REQUIRED,
                'admin_id'   => Auth::id(),
                'invoice_id' => $invoice->id,
                'admin_note' => 'Frais requis — facture ' . $invoice->reference,
            ]);
        });

        $invoice = Invoice::where('client_id', $client->id)->latest()->first();

        ClientNotification::forUser(
            $client->id,
            'system',
            'Frais requis pour votre virement',
            "Des frais de " . number_format($data['fee_amount'], 2, ',', ' ') . " {$currency} sont requis pour traiter votre virement {$transfer->reference}. Consultez votre espace facturation.",
            ['transfer_id' => $transfer->id, 'reference' => $transfer->reference]
        );

        try {
            Mail::to($client->email)
                ->locale($client->locale ?? 'fr')
                ->send(new TransferActionMail($transfer->fresh(['invoice']), 'fee_required'));
        } catch (\Throwable $e) {
            Log::error('TransferActionMail (fee_required) failed: ' . $e->getMessage());
        }

        return back()->with('success', "Facture de frais créée et envoyée au client pour le virement {$transfer->reference}.");
    }

    private function baseQuery(bool $isSuperAdmin): \Illuminate\Database\Eloquent\Builder
    {
        $query = Transfer::where('type', 'send');

        if (! $isSuperAdmin) {
            $adminId = Auth::id();
            $query->whereHas('user', function ($q) use ($adminId) {
                $q->where('created_by', $adminId)
                  ->orWhereHas('clientLoans', fn ($q2) => $q2->where('admin_id', $adminId));
            });
        }

        return $query;
    }

    private function authorizeTransfer(Transfer $transfer): void
    {
        $auth = Auth::user();
        if ($auth->hasRole('super-admin')) return;

        $adminId   = $auth->id;
        $client    = $transfer->user;
        $isManaged = $client->created_by === $adminId
            || $client->clientLoans()->where('admin_id', $adminId)->exists();

        abort_unless($isManaged, 403, 'Accès non autorisé à ce virement.');
    }
}
