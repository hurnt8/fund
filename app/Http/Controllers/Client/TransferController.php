<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Mail\AdminTransferMail;
use App\Models\AdminNotification;
use App\Models\Transfer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class TransferController extends Controller
{
    public function hub()
    {
        $user      = Auth::user();
        $transfers = Transfer::where('user_id', $user->id)->latest()->limit(5)->get();
        return view('client.app.transfer.index', compact('user', 'transfers'));
    }

    public function sendForm()
    {
        $user = Auth::user();

        if ((float) $user->balance < 0) {
            return redirect()->route('client.app.transfers')
                ->withErrors(['blocked' => __('app.transfer_negative_balance')]);
        }

        return view('client.app.transfer.send', compact('user'));
    }

    public function sendProcess(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'amount'           => 'required|numeric|min:1',
            'beneficiary_name' => 'required|string|max:100',
            'beneficiary_iban' => 'required|string|max:50',
            'note'             => 'nullable|string|max:255',
        ]);

        $amount = (float) $validated['amount'];

        if ((float) $user->balance < 0) {
            return back()->withErrors(['amount' => __('app.transfer_negative_balance')])->withInput();
        }

        // Vérification pré-requête (UI feedback rapide, pas de garantie)
        if ($amount > (float) $user->balance) {
            return back()->withErrors(['amount' => __('app.transfer_insufficient')])->withInput();
        }

        try {
            DB::transaction(function () use ($user, $validated, $amount) {
                // Verrou pessimiste : re-vérifie le solde à l'intérieur de la transaction
                // pour éviter le double-débit en cas de requêtes concurrentes
                $fresh = User::lockForUpdate()->find($user->id);

                if ($amount > (float) $fresh->balance) {
                    throw new \DomainException(__('app.transfer_insufficient'));
                }

                $transfer = Transfer::create([
                    'user_id'          => $fresh->id,
                    'reference'        => Transfer::generateReference(),
                    'type'             => 'send',
                    'amount'           => $amount,
                    'currency'         => $fresh->currency ?? config('credixa.default_currency'),
                    'beneficiary_name' => $validated['beneficiary_name'],
                    'beneficiary_iban' => $validated['beneficiary_iban'],
                    'note'             => $validated['note'] ?? null,
                    'status'           => Transfer::STATUS_PENDING,
                ]);

                // Fonds réservés immédiatement — remboursés si rejet admin
                $fresh->decrement('balance', $amount);

                session(['last_transfer_id' => $transfer->id]);

                // Notifier les admins responsables
                $this->notifyAdmins($fresh, $transfer);
            });
        } catch (\DomainException $e) {
            return back()->withErrors(['amount' => $e->getMessage()])->withInput();
        }

        return redirect()->route('client.app.transfer.confirmation');
    }

    public function receive()
    {
        $user = Auth::user();
        return view('client.app.transfer.receive', compact('user'));
    }

    private function notifyAdmins(User $client, Transfer $transfer): void
    {
        $adminIds = collect();

        if ($client->created_by) {
            $adminIds->push($client->created_by);
        }
        $loanAdminId = $client->clientLoans()->whereNotNull('admin_id')->value('admin_id');
        if ($loanAdminId) $adminIds->push($loanAdminId);
        $adminIds = $adminIds->unique();

        if ($adminIds->isEmpty()) {
            $adminIds = User::role('super-admin')->pluck('id');
        }

        $body = 'Virement de ' . number_format($transfer->amount, 2, ',', ' ') . ' '
            . $transfer->currency . ' vers ' . $transfer->beneficiary_name;

        foreach ($adminIds as $adminId) {
            AdminNotification::forAdmin($adminId, 'transfer', 'Virement en attente — ' . $client->name, $body, [
                'transfer_id' => $transfer->id,
                'client_id'   => $client->id,
            ]);

            $admin = User::find($adminId);
            if ($admin) {
                Mail::to($admin->email)->send(new AdminTransferMail($client, $transfer));
            }
        }
    }

    public function confirmation()
    {
        $user     = Auth::user();
        $transfer = null;

        if ($id = session('last_transfer_id')) {
            $transfer = Transfer::where('user_id', $user->id)->find($id);
        }

        return view('client.app.transfer.confirmation', compact('user', 'transfer'));
    }
}
