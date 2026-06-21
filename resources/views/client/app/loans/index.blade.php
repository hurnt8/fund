@extends('layouts.client-app')
@section('title', __('app.nav_loans') . ' — Credixa')
@section('page_title', __('app.nav_loans'))
@section('back_btn', true)
@section('back_url', route('client.app.home'))

@section('topbar_action')
<div class="ca-topbar__action" style="font-size:.75rem;font-weight:700;color:var(--ca-teal-l)">
  {{ $loans->count() }}
</div>
@endsection

@section('content')

@if($loans->isEmpty())
<div class="ca-empty" style="padding-top:3rem">
  <div class="ca-empty__icon"><i class="fas fa-file-contract"></i></div>
  <div class="ca-empty__title">{{ __('app.no_loans_title') }}</div>
  <div class="ca-empty__body">{{ __('app.no_loans_body') }}</div>
  <div class="ca-btn-wrap">
    <a href="{{ route('client.app.home') }}" class="ca-btn ca-btn--ghost">
      <i class="fas fa-arrow-left"></i> {{ __('app.nav_home') }}
    </a>
  </div>
</div>
@else

{{-- Stats --}}
@php
  $byStatus = $loans->groupBy('status');
  $finalized = $byStatus->get('finalized', collect())->count();
  $active    = $loans->whereIn('status', ['contract_sent','contract_signed'])->count();
  $pending   = $loans->whereIn('status', ['draft','pending','validated'])->count();
@endphp
<div class="ca-stats-row" style="margin-top:.75rem">
  <div class="ca-stat-chip">
    <div class="ca-stat-chip__val">{{ $loans->count() }}</div>
    <div class="ca-stat-chip__lbl">{{ __('app.stat_total') }}</div>
  </div>
  <div class="ca-stat-chip">
    <div class="ca-stat-chip__val" style="color:var(--ca-teal-l)">{{ $active }}</div>
    <div class="ca-stat-chip__lbl">{{ __('app.stat_active') }}</div>
  </div>
  <div class="ca-stat-chip">
    <div class="ca-stat-chip__val" style="color:var(--ca-gold-l)">{{ $finalized }}</div>
    <div class="ca-stat-chip__lbl">{{ __('app.stat_finalized') }}</div>
  </div>
</div>

<div style="height:.75rem"></div>

<div class="ca-loan-list">
  @foreach($loans as $loan)
  @php
    $steps = ['draft','pending','validated','contract_sent','contract_signed','finalized'];
    $idx   = array_search($loan->status, $steps);
    $pct   = $idx !== false ? round(($idx+1)/count($steps)*100) : 0;
    $badgeClass = match($loan->status){
        'draft'           => 'ca-badge--draft',
        'pending'         => 'ca-badge--pending',
        'validated'       => 'ca-badge--valid',
        'contract_sent'   => 'ca-badge--sent',
        'contract_signed' => 'ca-badge--signed',
        'finalized'       => 'ca-badge--final',
        'rejected'        => 'ca-badge--rejected',
        default           => 'ca-badge--draft',
    };
    $accentColor = match($loan->status){
        'finalized'       => 'var(--ca-gold)',
        'contract_signed' => 'var(--ca-positive)',
        'contract_sent'   => 'var(--ca-blue)',
        'rejected'        => 'var(--ca-negative)',
        default           => 'var(--ca-amber)',
    };
  @endphp
  <a href="{{ route('client.app.loans.show', $loan) }}" class="ca-loan-card" style="--lc-color:{{ $accentColor }}">
    <div class="ca-loan-card__accent"></div>
    <div class="ca-loan-card__body">
      <div class="ca-loan-card__row">
        <span class="ca-loan-card__ref">{{ $loan->reference }}</span>
        <span class="ca-badge {{ $badgeClass }}">{{ $loan->statusLabel() }}</span>
      </div>
      <div class="ca-loan-card__amount">
        {{ number_format($loan->amount, 0, ',', ' ') }}
        <span>{{ $loan->currency }}</span>
      </div>
      <div class="ca-loan-card__meta" style="margin-top:.5rem">
        <span class="ca-loan-card__meta-item">
          {{ __('app.monthly') }}: <strong>{{ number_format($loan->monthly_payment, 0, ',', ' ') }} {{ $loan->currency }}</strong>
        </span>
        <span class="ca-loan-card__meta-item">
          {{ $loan->darly }} {{ __('app.months') }}
        </span>
        <span class="ca-loan-card__meta-item">
          {{ $loan->interest_rate }}%
        </span>
      </div>
      @if($loan->status !== 'rejected')
      <div class="ca-loan-card__progress-bar">
        <div class="ca-loan-card__progress-fill" style="width:{{ $pct }}%"></div>
      </div>
      @endif
    </div>
  </a>
  @endforeach
</div>

<div style="height:1rem"></div>
@endif
@endsection
