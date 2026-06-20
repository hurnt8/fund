@extends('layouts.dashboard')
@section('title', 'Administration')
@section('page_title', 'Tableau de bord Admin')

@section('sidebar')
<span class="ds__section">Principal</span>
<a href="{{ route('admin.dashboard') }}" class="ds__link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
    <i class="fas fa-th-large"></i> Tableau de bord
</a>
<a href="{{ route('admin.users') }}" class="ds__link {{ request()->routeIs('admin.users') ? 'active' : '' }}">
    <i class="fas fa-users"></i> Utilisateurs
</a>
<span class="ds__section" style="margin-top:.75rem;">Demandes de prêt</span>
<a href="{{ route('admin.dashboard') }}" class="ds__link">
    <i class="fas fa-file-invoice-dollar"></i> Toutes les demandes
</a>
<span class="ds__section" style="margin-top:.75rem;">Compte</span>
<a href="{{ route('home', ['locale' => app()->getLocale()]) }}" class="ds__link">
    <i class="fas fa-globe"></i> Retour au site
</a>
@endsection

@section('content')
<div style="margin-bottom:1.5rem;">
    <h2 style="font-size:1.1875rem;font-weight:800;color:#0B1A2E;margin-bottom:.25rem;">Bienvenue, {{ Auth::user()->name }}</h2>
    <p style="font-size:.8375rem;color:#6b7280;">Vue d'ensemble des activités Credixa.</p>
</div>

{{-- Stats --}}
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="stat-c">
            <div class="stat-c__icon ic--blue"><i class="fas fa-file-alt"></i></div>
            <div><div class="stat-c__val">{{ $stats['total_loans'] }}</div><div class="stat-c__lbl">Total demandes</div></div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-c">
            <div class="stat-c__icon ic--gold"><i class="fas fa-clock"></i></div>
            <div><div class="stat-c__val">{{ $stats['pending_loans'] }}</div><div class="stat-c__lbl">En attente</div></div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-c">
            <div class="stat-c__icon ic--green"><i class="fas fa-check-circle"></i></div>
            <div><div class="stat-c__val">{{ $stats['approved_loans'] }}</div><div class="stat-c__lbl">Approuvées</div></div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-c">
            <div class="stat-c__icon ic--navy"><i class="fas fa-users"></i></div>
            <div><div class="stat-c__val">{{ $stats['total_clients'] }}</div><div class="stat-c__lbl">Clients</div></div>
        </div>
    </div>
</div>

{{-- Loan requests table --}}
<div class="dc">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.25rem;flex-wrap:wrap;gap:.75rem;">
        <h3 style="font-size:1rem;font-weight:700;color:#0B1A2E;margin:0;">Demandes récentes</h3>
        <span style="font-size:.775rem;color:#6b7280;">20 dernières demandes</span>
    </div>

    @if($loans->isEmpty())
    <div style="text-align:center;padding:2.5rem 1rem;color:#9ca3af;">
        <i class="fas fa-inbox" style="font-size:2rem;margin-bottom:.75rem;display:block;"></i>
        Aucune demande enregistrée.
    </div>
    @else
    <div style="overflow-x:auto;">
        <table class="dt">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Client</th>
                    <th>Email</th>
                    <th>Montant</th>
                    <th>Objet</th>
                    <th>Statut</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($loans as $loan)
                @php
                    $status = $loan->status ?? 'pending';
                    $statusMap   = ['pending'=>'En attente','approved'=>'Approuvé','rejected'=>'Refusé','review'=>'En révision'];
                    $statusClass = ['pending'=>'sb--pending','approved'=>'sb--approved','rejected'=>'sb--rejected','review'=>'sb--review'];
                @endphp
                <tr>
                    <td style="color:#9ca3af;font-size:.75rem;">#{{ $loan->id }}</td>
                    <td style="font-weight:600;">{{ $loan->name }}</td>
                    <td style="font-size:.775rem;color:#6b7280;">{{ $loan->email }}</td>
                    <td>{{ number_format($loan->amount, 0, ',', ' ') }} €</td>
                    <td>{{ Str::limit($loan->objet, 25) }}</td>
                    <td><span class="sb {{ $statusClass[$status] ?? 'sb--default' }}">{{ $statusMap[$status] ?? ucfirst($status) }}</span></td>
                    <td style="color:#9ca3af;font-size:.775rem;">{{ $loan->created_at->format('d/m/Y') }}</td>
                    <td>
                        <div style="display:flex;gap:.375rem;flex-wrap:nowrap;">
                            @if($status !== 'approved')
                            <form action="{{ route('admin.loans.status', [$loan->id, 'approved']) }}" method="POST">
                                @csrf @method('PATCH')
                                <button type="submit" class="b-sm b-ok" title="Approuver"><i class="fas fa-check"></i></button>
                            </form>
                            @endif
                            @if($status !== 'rejected')
                            <form action="{{ route('admin.loans.status', [$loan->id, 'rejected']) }}" method="POST">
                                @csrf @method('PATCH')
                                <button type="submit" class="b-sm b-del" title="Refuser"><i class="fas fa-times"></i></button>
                            </form>
                            @endif
                            @if($status !== 'review')
                            <form action="{{ route('admin.loans.status', [$loan->id, 'review']) }}" method="POST">
                                @csrf @method('PATCH')
                                <button type="submit" class="b-sm b-edit" title="En révision"><i class="fas fa-search"></i></button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>
@endsection
