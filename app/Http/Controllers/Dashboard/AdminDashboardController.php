<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\LoanRequest;
use App\Models\User;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $loans = LoanRequest::latest()->take(20)->get();

        $stats = [
            'total_loans'    => LoanRequest::count(),
            'pending_loans'  => LoanRequest::whereIn('status', [null, 'pending'])->count(),
            'approved_loans' => LoanRequest::where('status', 'approved')->count(),
            'total_clients'  => User::where('type', 'client')->count(),
        ];

        return view('dashboard.admin.index', compact('loans', 'stats'));
    }

    public function updateLoanStatus(LoanRequest $loan, $status)
    {
        abort_unless(in_array($status, ['pending', 'approved', 'rejected', 'review']), 422);
        $loan->update(['status' => $status]);
        return back()->with('success', 'Statut mis à jour.');
    }
}
