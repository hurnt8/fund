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

        $loans = LoanRequest::where('email', $user->email)
            ->latest()
            ->get();

        $stats = [
            'total'    => $loans->count(),
            'pending'  => $loans->whereIn('status', [null, 'pending'])->count(),
            'approved' => $loans->where('status', 'approved')->count(),
            'rejected' => $loans->where('status', 'rejected')->count(),
        ];

        return view('dashboard.client.index', compact('loans', 'stats'));
    }
}
