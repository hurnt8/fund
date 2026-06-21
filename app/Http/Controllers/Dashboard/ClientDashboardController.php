<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\LoanRequest;
use Illuminate\Support\Facades\Auth;

class ClientDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $loans = LoanRequest::where('client_id', $user->id)
            ->latest()
            ->get();

        $stats = [
            'total'     => $loans->count(),
            'pending'   => $loans->whereIn('status', ['draft', 'pending', 'validated'])->count(),
            'active'    => $loans->whereIn('status', ['contract_sent', 'contract_signed'])->count(),
            'finalized' => $loans->where('status', 'finalized')->count(),
            'rejected'  => $loans->where('status', 'rejected')->count(),
        ];

        return view('dashboard.client.index', compact('loans', 'stats'));
    }
}
