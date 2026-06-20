<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\LoanRequest;
use App\Models\User;
use Spatie\Permission\Models\Role;

class SuperAdminDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users'    => User::count(),
            'total_clients'  => User::where('type', 'client')->count(),
            'total_staff'    => User::where('type', 'staff')->count(),
            'total_loans'    => LoanRequest::count(),
            'pending_loans'  => LoanRequest::whereIn('status', [null, 'pending'])->count(),
            'approved_loans' => LoanRequest::where('status', 'approved')->count(),
            'rejected_loans' => LoanRequest::where('status', 'rejected')->count(),
            'review_loans'   => LoanRequest::where('status', 'review')->count(),
            'total_amount'   => LoanRequest::sum('amount'),
            'month_loans'    => LoanRequest::whereMonth('created_at', now()->month)
                                           ->whereYear('created_at', now()->year)->count(),
        ];

        $recentUsers = User::latest()->take(8)->get();
        $recentLoans = LoanRequest::latest()->take(8)->get();

        return view('dashboard.super-admin.index', compact('stats', 'recentUsers', 'recentLoans'));
    }

    public function roles()
    {
        $roles = Role::withCount('users')->get();
        $users = User::with('roles')->latest()->paginate(20);
        return view('dashboard.super-admin.roles', compact('roles', 'users'));
    }

    public function assignRole(User $user)
    {
        request()->validate(['role' => 'required|string|exists:roles,name']);
        $role = request('role');

        if ($role === 'super-admin' || $role === 'admin') {
            $user->update(['type' => 'staff']);
        } else {
            $user->update(['type' => 'client']);
        }

        $user->syncRoles([$role]);
        return back()->with('success', "Rôle « {$role} » attribué à {$user->name}.");
    }
}
