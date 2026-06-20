<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\LoanRequest;
use Illuminate\Support\Facades\Auth;

class LoanRequestController extends Controller
{
    public function index()
    {
        $user  = Auth::user();
        $loans = LoanRequest::where('client_id', $user->id)
                            ->orWhere('email', $user->email)
                            ->latest()
                            ->paginate(10);

        $stats = [
            'total'          => LoanRequest::where('client_id', $user->id)->count(),
            'pending'        => LoanRequest::where('client_id', $user->id)->whereIn('status', ['draft','pending'])->count(),
            'contract_sent'  => LoanRequest::where('client_id', $user->id)->where('status', 'contract_sent')->count(),
            'finalized'      => LoanRequest::where('client_id', $user->id)->where('status', 'finalized')->count(),
        ];

        return view('dashboard.client.loans.index', compact('loans', 'stats'));
    }

    public function show(LoanRequest $loan)
    {
        $user = Auth::user();
        abort_unless(
            $loan->client_id === $user->id || $loan->email === $user->email,
            403
        );
        $loan->load(['admin', 'history']);
        return view('dashboard.client.loans.show', compact('loan'));
    }
}
