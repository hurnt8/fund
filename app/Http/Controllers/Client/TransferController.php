<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Transfer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

        if ($amount > (float) $user->balance) {
            return back()->withErrors(['amount' => __('app.transfer_insufficient')])->withInput();
        }

        DB::transaction(function () use ($user, $validated, $amount) {
            $transfer = Transfer::create([
                'user_id'          => $user->id,
                'reference'        => Transfer::generateReference(),
                'type'             => 'send',
                'amount'           => $amount,
                'currency'         => $user->currency ?? 'EUR',
                'beneficiary_name' => $validated['beneficiary_name'],
                'beneficiary_iban' => $validated['beneficiary_iban'],
                'note'             => $validated['note'] ?? null,
                'status'           => 'completed',
                'processed_at'     => now(),
            ]);

            $user->decrement('balance', $amount);

            session(['last_transfer_id' => $transfer->id]);
        });

        return redirect()->route('client.app.transfer.confirmation');
    }

    public function receive()
    {
        $user = Auth::user();
        return view('client.app.transfer.receive', compact('user'));
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
