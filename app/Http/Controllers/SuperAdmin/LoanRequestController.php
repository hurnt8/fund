<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\LoanRequest;
use App\Models\User;
use Illuminate\Http\Request;

class LoanRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = LoanRequest::with(['client', 'admin']);

        if ($request->filled('admin_id')) {
            $query->where('admin_id', $request->admin_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q
                ->where('name', 'like', "%$s%")
                ->orWhere('email', 'like', "%$s%")
                ->orWhere('reference', 'like', "%$s%")
            );
        }

        $loans  = $query->latest()->paginate(20)->withQueryString();
        $admins = User::where('type', 'staff')->orderBy('name')->get();

        $stats = [
            'total'             => LoanRequest::count(),
            'draft'             => LoanRequest::where('status', 'draft')->count(),
            'pending'           => LoanRequest::where('status', 'pending')->count(),
            'validated'         => LoanRequest::where('status', 'validated')->count(),
            'contract_sent'     => LoanRequest::where('status', 'contract_sent')->count(),
            'contract_signed'   => LoanRequest::where('status', 'contract_signed')->count(),
            'finalized'         => LoanRequest::where('status', 'finalized')->count(),
            'rejected'          => LoanRequest::where('status', 'rejected')->count(),
        ];

        return view('super-admin.loans.index', compact('loans', 'admins', 'stats'));
    }

    public function show(LoanRequest $loan)
    {
        $loan->load(['client', 'admin', 'history.admin', 'contractTemplate']);
        $generatedDocs = $loan->generatedDocuments()->with('generatedBy')->get();
        $isSuperAdmin  = true;
        $admins        = User::where('type', 'staff')
            ->whereHas('roles', fn($q) => $q->whereIn('name', ['admin', 'super-admin']))
            ->orderBy('name')->get();
        return view('admin.loans.show', compact('loan', 'generatedDocs', 'isSuperAdmin', 'admins'));
    }
}
