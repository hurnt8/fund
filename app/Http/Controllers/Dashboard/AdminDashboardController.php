<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\LoanRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $adminId = Auth::id();

        $base = LoanRequest::where('admin_id', $adminId);

        $pendingStatuses  = [LoanRequest::STATUS_PENDING];
        $activeStatuses   = [LoanRequest::STATUS_VALIDATED, LoanRequest::STATUS_CONTRACT_SENT, LoanRequest::STATUS_CONTRACT_SIGNED];
        $finalizedStatus  = [LoanRequest::STATUS_FINALIZED];
        $rejectedStatus   = [LoanRequest::STATUS_REJECTED];

        $stats = [
            'total_loans'     => (clone $base)->count(),
            'pending_loans'   => (clone $base)->whereIn('status', $pendingStatuses)->count(),
            'active_loans'    => (clone $base)->whereIn('status', $activeStatuses)->count(),
            'finalized_loans' => (clone $base)->whereIn('status', $finalizedStatus)->count(),
            'rejected_loans'  => (clone $base)->whereIn('status', $rejectedStatus)->count(),
            'total_amount'    => (clone $base)->sum('amount'),
            'month_loans'     => (clone $base)
                                    ->whereMonth('created_at', now()->month)
                                    ->whereYear('created_at',  now()->year)
                                    ->count(),
            'my_clients'      => User::where('type', 'client')
                                    ->where(function ($q) use ($adminId) {
                                        $q->where('created_by', $adminId)
                                          ->orWhereHas('clientLoans', fn ($q2) => $q2->where('admin_id', $adminId));
                                    })
                                    ->count(),
        ];

        $recentLoans = LoanRequest::where('admin_id', $adminId)
            ->with('client')
            ->latest()
            ->take(8)
            ->get();

        $recentClients = User::where('type', 'client')
            ->where(function ($q) use ($adminId) {
                $q->where('created_by', $adminId)
                  ->orWhereHas('clientLoans', fn ($q2) => $q2->where('admin_id', $adminId));
            })
            ->latest()
            ->take(6)
            ->get();

        return view('dashboard.admin.index', compact('stats', 'recentLoans', 'recentClients'));
    }

    public function updateLoanStatus(LoanRequest $loan, $status)
    {
        abort_unless(in_array($status, LoanRequest::STATUSES), 422);
        $loan->update(['status' => $status]);
        return back()->with('success', 'Statut mis à jour.');
    }
}
