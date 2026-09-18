@extends('layouts.dashboard')
@section('title', 'Mon espace — Aurenza Capital')
@section('page_title', 'Mon espace')

@push('styles')
<style>
  body { background: #0A2A20 !important; }
  .main-wrap { background: #0A2A20; }
  .content-area { background: transparent; }
  .topbar { background: #0E3226 !important; border-bottom-color: rgba(198,161,91,.14) !important; }
  .topbar-title { color: #E9EFEA !important; }
  .topbar-badge { background: #143C2E !important; border-color: rgba(255,255,255,.1) !important; color: #BFCFC5 !important; }
  .topbar-avatar { background: linear-gradient(135deg,#1C4C3A,#0B4E2E) !important; color: #C6A15B !important; }
  .flash-ok  { background: rgba(16,185,129,.1); color: #6EE7B7; border-color: rgba(16,185,129,.25); }
  .flash-err { background: rgba(239,68,68,.1); color: #FCA5A5; border-color: rgba(239,68,68,.2); }
</style>
@endpush

@section('content')
<div class="cl-scope">

{{-- ── Hero balance ──────────────────────────────────────────── --}}
<div class="cl-hero">
  <div class="cl-hero__badge">
    <i class="fas fa-shield-check"></i> Espace sécurisé
  </div>

  <div class="d-flex align-items-start justify-content-between flex-wrap gap-4">
    <div>
      <div class="cl-hero__label">Bonjour, {{ Auth::user()->name }}</div>
      <div class="cl-hero__amount">
        {{ number_format((float) Auth::user()->balance, 2, ',', ' ') }}
        <span class="cl-hero__currency">{{ Auth::user()->currency ?? config('aurenza.default_currency') }}</span>
      </div>
      <div class="cl-hero__sub">
        <i class="fas fa-wallet me-1" style="color:var(--cl-gold)"></i>
        Solde disponible sur votre compte
      </div>
    </div>

    <div class="d-flex flex-column gap-2 text-end">
      <div style="font-size:.68rem;color:var(--cl-muted);text-transform:uppercase;letter-spacing:.07em">Dossiers actifs</div>
      <div style="font-family:'Outfit',sans-serif;font-size:2rem;font-weight:700;color:var(--cl-gold);line-height:1">
        {{ $stats['active'] + $stats['finalized'] }}
      </div>
      <div>
        <span class="cl-badge cl-badge--finalized">
          {{ $stats['finalized'] }} finalisé{{ $stats['finalized'] > 1 ? 's' : '' }}
        </span>
      </div>
    </div>
  </div>
</div>

{{-- ── Stats ────────────────────────────────────────────────── --}}
<div class="cl-stats">
  <div class="cl-stat" style="--stat-color:var(--cl-gold);--stat-color-bg:rgba(198,161,91,.12)">
    <div class="cl-stat__icon"><i class="fas fa-layer-group"></i></div>
    <div class="cl-stat__val">{{ $stats['total'] }}</div>
    <div class="cl-stat__lbl">Total dossiers</div>
  </div>
  <div class="cl-stat" style="--stat-color:var(--cl-amber);--stat-color-bg:rgba(245,158,11,.12)">
    <div class="cl-stat__icon"><i class="fas fa-hourglass-half"></i></div>
    <div class="cl-stat__val">{{ $stats['pending'] }}</div>
    <div class="cl-stat__lbl">En cours d'analyse</div>
  </div>
  <div class="cl-stat" style="--stat-color:var(--cl-blue);--stat-color-bg:rgba(59,130,246,.12)">
    <div class="cl-stat__icon"><i class="fas fa-file-contract"></i></div>
    <div class="cl-stat__val">{{ $stats['active'] }}</div>
    <div class="cl-stat__lbl">Contrats actifs</div>
  </div>
  <div class="cl-stat" style="--stat-color:var(--cl-green);--stat-color-bg:rgba(16,185,129,.12)">
    <div class="cl-stat__icon"><i class="fas fa-check-circle"></i></div>
    <div class="cl-stat__val">{{ $stats['finalized'] }}</div>
    <div class="cl-stat__lbl">Finalisés</div>
  </div>
</div>

{{-- ── Recent loans ─────────────────────────────────────────── --}}
@if($loans->isNotEmpty())
<div class="cl-section-title"><i class="fas fa-history me-1" style="color:var(--cl-gold)"></i> Derniers dossiers</div>

<div class="row g-3 mb-4">
  @foreach($loans->take(6) as $loan)
  @php
    $steps = ['draft','pending','validated','contract_sent','contract_signed','finalized'];
    $idx   = array_search($loan->status, $steps);
    $pct   = $idx !== false ? round(($idx + 1) / count($steps) * 100) : 0;
    $badgeClass = match($loan->status) {
        'draft'           => 'cl-badge--draft',
        'pending'         => 'cl-badge--pending',
        'validated'       => 'cl-badge--validated',
        'contract_sent'   => 'cl-badge--sent',
        'contract_signed' => 'cl-badge--signed',
        'finalized'       => 'cl-badge--finalized',
        'rejected'        => 'cl-badge--rejected',
        default           => 'cl-badge--draft',
    };
    $accentColor = match($loan->status) {
        'rejected'        => 'var(--cl-red)',
        'finalized'       => 'var(--cl-gold)',
        'contract_signed' => 'var(--cl-green)',
        'contract_sent'   => 'var(--cl-blue)',
        default           => 'var(--cl-amber)',
    };
  @endphp
  <div class="col-md-6 col-xl-4">
    <div class="cl-loan-card" style="--card-accent:{{ $accentColor }}">
      <div class="cl-loan-card__top"></div>

      <div class="cl-loan-card__head">
        <span class="cl-loan-card__ref">{{ $loan->reference }}</span>
        <span class="cl-badge {{ $badgeClass }}">{{ $loan->statusLabel() }}</span>
      </div>

      <div class="cl-loan-card__body">
        <div class="cl-loan-card__amount-label">Montant accordé</div>
        <div class="cl-loan-card__amount">
          {{ number_format($loan->amount, 0, ',', ' ') }}
          <span>{{ $loan->currency }}</span>
        </div>

        <div class="cl-loan-card__grid">
          <div>
            <div class="cl-loan-card__metric-label">Mensualité</div>
            <div class="cl-loan-card__metric-val accent">
              {{ number_format($loan->monthly_payment, 2, ',', ' ') }} {{ $loan->currency }}
            </div>
          </div>
          <div>
            <div class="cl-loan-card__metric-label">Durée</div>
            <div class="cl-loan-card__metric-val">{{ $loan->darly }} mois</div>
          </div>
          <div>
            <div class="cl-loan-card__metric-label">Taux</div>
            <div class="cl-loan-card__metric-val">{{ $loan->interest_rate }} %</div>
          </div>
          <div>
            <div class="cl-loan-card__metric-label">Ouverture</div>
            <div class="cl-loan-card__metric-val">{{ $loan->created_at->format('d/m/Y') }}</div>
          </div>
        </div>

        @if($loan->status !== 'rejected')
        <div class="cl-progress">
          <div class="cl-progress__header">
            <span class="cl-progress__label">Avancement</span>
            <span class="cl-progress__pct">{{ $pct }}%</span>
          </div>
          <div class="cl-progress__bar">
            <div class="cl-progress__fill" style="width:{{ $pct }}%"></div>
          </div>
        </div>
        @endif
      </div>

      <div class="cl-loan-card__foot">
        <a href="{{ route('client.loans.show', $loan) }}" class="cl-btn cl-btn--primary" style="width:100%">
          <i class="fas fa-eye"></i> Voir le dossier
        </a>
      </div>
    </div>
  </div>
  @endforeach
</div>

@if($loans->count() > 6)
<div class="text-center mt-2 mb-4">
  <a href="{{ route('client.loans') }}" class="cl-btn cl-btn--ghost">
    <i class="fas fa-list me-1"></i> Voir tous les dossiers ({{ $loans->count() }})
  </a>
</div>
@endif

@else
<div class="cl-empty">
  <div class="cl-empty__icon"><i class="fas fa-file-invoice-dollar"></i></div>
  <div class="cl-empty__title">Aucun dossier en cours</div>
  <div class="cl-empty__body">
    Vous n'avez pas encore de demande de financement.
    Contactez votre conseiller Aurenza Capital pour en initier une.
  </div>
  <a href="{{ route('home',['locale'=>app()->getLocale()]) }}" class="cl-btn cl-btn--gold">
    <i class="fas fa-globe me-1"></i> Retour au site
  </a>
</div>
@endif

</div>
@endsection
