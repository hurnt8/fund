<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\UserInvitationMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class UserManagementController extends Controller
{
    /** Roles an admin (non-super-admin) is allowed to assign */
    private const ADMIN_ALLOWED_ROLES = ['client', 'admin'];

    public function index(Request $request)
    {
        $authUser     = Auth::user();
        $isSuperAdmin = $authUser->hasRole('super-admin');

        $query = User::with('roles')->latest();

        // Admins only see clients they created themselves
        if (! $isSuperAdmin) {
            $query->where('type', 'client')
                  ->where('created_by', $authUser->id);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('name', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%"));
        }
        if ($isSuperAdmin && $request->filled('type')) {
            $query->where('type', $request->type);
        }

        $users = $query->paginate(15)->appends($request->query());
        $roles = $isSuperAdmin
            ? Role::all()
            : Role::whereIn('name', self::ADMIN_ALLOWED_ROLES)->get();

        $stats = [
            'total'  => User::when(! $isSuperAdmin, fn($q) => $q->where('type', 'client')->where('created_by', $authUser->id))->count(),
            'client' => User::where('type', 'client')
                            ->when(! $isSuperAdmin, fn($q) => $q->where('created_by', $authUser->id))
                            ->count(),
            'staff'  => $isSuperAdmin ? User::where('type', 'staff')->count() : 0,
        ];

        return view('admin.users.index', compact('users', 'roles', 'stats', 'isSuperAdmin'));
    }

    public function store(Request $request)
    {
        $authUser     = Auth::user();
        $isSuperAdmin = $authUser->hasRole('super-admin');

        $data = $request->validate([
            'name'       => 'required|string|max:100',
            'email'      => 'required|email|unique:users,email',
            'type'       => 'required|in:client,staff',
            'gender'     => 'nullable|in:M,F,N',
            'role'       => 'required|string|exists:roles,name',
            'phone'      => 'nullable|string|max:50',
            'address'    => 'nullable|string|max:500',
            'birth_date' => 'nullable|date',
            'id_type'    => 'nullable|string|max:30',
            'id_number'  => 'nullable|string|max:60',
            'currency'   => 'nullable|string|max:10',
            'locale'     => 'nullable|in:fr,en,pl,es',
        ]);

        // Non-super-admins cannot assign the super-admin role
        if (! $isSuperAdmin && ! in_array($data['role'], self::ADMIN_ALLOWED_ROLES)) {
            return back()->with('error', 'Vous n\'êtes pas autorisé à assigner ce rôle.');
        }

        $token = Str::random(64);

        $user = User::create([
            'name'             => $data['name'],
            'email'            => $data['email'],
            'password'         => Hash::make(Str::random(32)),
            'type'             => $data['type'],
            'gender'           => $data['gender'] ?? 'N',
            'created_by'       => $authUser->id,
            'invitation_token' => $token,
            'phone'            => $data['phone'] ?? null,
            'address'          => $data['address'] ?? null,
            'birth_date'       => $data['birth_date'] ?? null,
            'id_type'          => $data['id_type'] ?? null,
            'id_number'        => $data['id_number'] ?? null,
            'currency'         => $data['currency'] ?? 'EUR',
            'locale'           => $data['locale'] ?? 'fr',
        ]);

        $user->assignRole($data['role']);

        $activationUrl = route('invitation.activate', ['token' => $token]);

        try {
            Mail::to($user->email)->send(new UserInvitationMail($user, $activationUrl));
            return back()->with('success', "Invitation envoyée à {$user->email}. {$user->name} recevra un email pour activer son compte.");
        } catch (\Exception) {
            return back()->with('success', "Client {$user->name} créé. L'email d'invitation n'a pas pu être envoyé (vérifiez la config mail). Lien d'activation : {$activationUrl}");
        }
    }

    public function update(Request $request, User $user)
    {
        $isSuperAdmin = Auth::user()->hasRole('super-admin');

        $data = $request->validate([
            'name'       => 'required|string|max:100',
            'email'      => "required|email|unique:users,email,{$user->id}",
            'type'       => 'required|in:client,staff',
            'role'       => 'required|string|exists:roles,name',
            'phone'      => 'nullable|string|max:50',
            'address'    => 'nullable|string|max:500',
            'birth_date' => 'nullable|date',
            'id_type'    => 'nullable|string|max:30',
            'id_number'  => 'nullable|string|max:60',
            'currency'   => 'nullable|string|max:10',
            'locale'     => 'nullable|in:fr,en,pl,es',
        ]);

        if (! $isSuperAdmin && ! in_array($data['role'], self::ADMIN_ALLOWED_ROLES)) {
            return back()->with('error', 'Vous n\'êtes pas autorisé à assigner ce rôle.');
        }

        $user->update([
            'name'       => $data['name'],
            'email'      => $data['email'],
            'type'       => $data['type'],
            'phone'      => $data['phone'] ?? $user->phone,
            'address'    => $data['address'] ?? $user->address,
            'birth_date' => $data['birth_date'] ?? $user->birth_date,
            'id_type'    => $data['id_type'] ?? $user->id_type,
            'id_number'  => $data['id_number'] ?? $user->id_number,
            'currency'   => $data['currency'] ?? $user->currency,
            'locale'     => $data['locale'] ?? $user->locale,
        ]);

        $user->syncRoles([$data['role']]);

        return back()->with('success', "Utilisateur {$user->name} mis à jour.");
    }

    public function resendInvitation(User $user)
    {
        if (! $user->invitation_token) {
            return back()->with('error', 'Ce compte est déjà activé.');
        }

        $activationUrl = route('invitation.activate', ['token' => $user->invitation_token]);

        try {
            Mail::to($user->email)->send(new UserInvitationMail($user, $activationUrl));
            return back()->with('success', "Invitation renvoyée à {$user->email}.");
        } catch (\Exception $e) {
            return back()->with('error', "Erreur d'envoi : " . $e->getMessage());
        }
    }

    public function destroy(User $user)
    {
        if ($user->hasRole('super-admin')) {
            return back()->with('error', 'Impossible de supprimer un super-administrateur.');
        }
        $user->delete();
        return back()->with('success', 'Utilisateur supprimé.');
    }
}
