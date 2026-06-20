@extends('layouts.dashboard')
@section('title', 'Mon espace')
@section('page_title', 'Mon espace')

@section('sidebar')
<span class="ds__section">Navigation</span>
<a href="{{ route('client.dashboard') }}" class="ds__link {{ request()->routeIs('client.dashboard') ? 'active' : '' }}">
    <i class="fas fa-th-large"></i> Tableau de bord
</a>
<a href="{{ route('loan', ['locale' => app()->getLocale()]) }}" class="ds__link">
    <i class="fas fa-file-signature"></i> Nouvelle demande
</a>
<span class="ds__section" style="margin-top:.75rem;">Compte</span>
<a href="#" class="ds__link">
    <i class="fas fa-user-circle"></i> Mon profil
</a>
<a href="{{ route('home', ['locale' => app()->getLocale()]) }}" class="ds__link">
    <i class="fas fa-globe"></i> Retour au site
</a>
@endsection

@section('content')
<div style="margin-bottom:1.5rem;">
    <h2 style="font-size:1.1875rem;font-weight:800;color:#0B1A2E;margin-bottom:.25rem;">Bonjour, {{ Auth::user()->name }}</h2>
    <p style="font-size:.8375rem;color:#6b7280;">Voici le résumé de vos demandes de prêt.</p>
</div>

{{-- Stats --}}
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="stat-c">
            <div class="stat-c__icon ic--blue"><i class="fas fa-file-alt"></i></div>
            <div><div class="stat-c__val">{{ $stats['total'] }}</div><div class="stat-c__lbl">Total demandes</div></div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-c">
            <div class="stat-c__icon ic--gold"><i class="fas fa-clock"></i></div>
            <div><div class="stat-c__val">{{ $stats['pending'] }}</div><div class="stat-c__lbl">En attente</div></div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-c">
            <div class="stat-c__icon ic--green"><i class="fas fa-check-circle"></i></div>
            <div><div class="stat-c__val">{{ $stats['approved'] }}</div><div class="stat-c__lbl">Approuvées</div></div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-c">
            <div class="stat-c__icon ic--red"><i class="fas fa-times-circle"></i></div>
            <div><div class="stat-c__val">{{ $stats['rejected'] }}</div><div class="stat-c__lbl">Refusées</div></div>
        </div>
    </div>
</div>

{{-- Loan list --}}
<div class="dc">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.25rem;flex-wrap:wrap;gap:.75rem;">
        <h3 style="font-size:1rem;font-weight:700;color:#0B1A2E;margin:0;">Mes demandes</h3>
        <a href="{{ route('loan', ['locale' => app()->getLocale()]) }}" class="b-gold b-sm">
            <i class="fas fa-plus"></i> Nouvelle demande
        </a>
    </div>

    @if($loans->isEmpty())
    <div style="text-align:center;padding:3rem 1rem;">
        <div style="width:64px;height:64px;background:#f3f4f6;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
            <i class="fas fa-file-alt" style="font-size:1.5rem;color:#9ca3af;"></i>
        </div>
        <p style="font-size:.9375rem;font-weight:600;color:#374151;margin-bottom:.375rem;">Aucune demande</p>
        <p style="font-size:.8125rem;color:#6b7280;margin-bottom:1.25rem;">Vous n'avez pas encore soumis de demande de prêt.</p>
        <a href="{{ route('loan', ['locale' => app()->getLocale()]) }}" class="b-navy">
            <i class="fas fa-file-signature"></i> Faire une demande
        </a>
    </div>
    @else
    <div style="overflow-x:auto;">
        <table class="dt">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Objet</th>
                    <th>Montant</th>
                    <th>Durée</th>
                    <th>Statut</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($loans as $loan)
                @php
                    $status = $loan->status ?? 'pending';
                    $statusMap = ['pending'=>'En attente','approved'=>'Approuvé','rejected'=>'Refusé','review'=>'En révision'];
                    $statusClass = ['pending'=>'sb--pending','approved'=>'sb--approved','rejected'=>'sb--rejected','review'=>'sb--review'];
                @endphp
                <tr>
                    <td style="color:#9ca3af;font-size:.75rem;">#{{ $loan->id }}</td>
                    <td style="font-weight:600;">{{ Str::limit($loan->objet, 30) }}</td>
                    <td>{{ number_format($loan->amount, 0, ',', ' ') }} €</td>
                    <td>{{ $loan->darly }} mois</td>
                    <td><span class="sb {{ $statusClass[$status] ?? 'sb--default' }}">{{ $statusMap[$status] ?? ucfirst($status) }}</span></td>
                    <td style="color:#9ca3af;font-size:.775rem;">{{ $loan->created_at->format('d/m/Y') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>
@endsection
