@extends('layouts.client-app')
@section('title', $loan->reference . ' — Credixa')
@section('page_title', $loan->reference)
@section('back_btn', true)
@section('back_url', route('client.app.loans'))

@php
  $steps = [
    'draft'           => __('app.status_draft'),
    'pending'         => __('app.status_pending'),
    'validated'       => __('app.status_validated'),
    'contract_sent'   => __('app.status_sent'),
    'contract_signed' => __('app.status_signed'),
    'finalized'       => __('app.status_finalized'),
  ];
  $stepKeys   = array_keys($steps);
  $currentIdx = array_search($loan->status, $stepKeys);
  $badgeClass = match($loan->status){
      'draft'=>'ca-badge--draft','pending'=>'ca-badge--pending','validated'=>'ca-badge--valid',
      'contract_sent'=>'ca-badge--sent','contract_signed'=>'ca-badge--signed',
      'finalized'=>'ca-badge--final','rejected'=>'ca-badge--rejected',default=>'ca-badge--draft',
  };
@endphp

@section('topbar_action')
<span class="ca-badge {{ $badgeClass }}" style="font-size:.65rem">{{ $loan->statusLabel() }}</span>
@endsection

@section('content')

{{-- Alerts --}}
@if($loan->status === 'contract_sent')
<div class="ca-alert ca-alert--warn" style="margin-top:.75rem">
  <div class="ca-alert__icon"><i class="fas fa-envelope"></i></div>
  <div>
    <div class="ca-alert__title">{{ __('app.status_sent') }}</div>
    {{ __('app.loan_alert_sent_body', ['date' => $loan->sent_at?->format('d/m/Y') ?? '—']) }}
  </div>
</div>
@endif
@if($loan->status === 'finalized')
<div class="ca-alert ca-alert--success" style="margin-top:.75rem">
  <div class="ca-alert__icon"><i class="fas fa-check-circle"></i></div>
  <div>
    <div class="ca-alert__title">{{ __('app.loan_alert_fin_title') }}</div>
    {{ __('app.loan_alert_fin_body', ['amount' => number_format($loan->amount,2,',',' '), 'currency' => $loan->currency]) }}
  </div>
</div>
@endif
@if($loan->status === 'rejected')
<div class="ca-alert ca-alert--danger" style="margin-top:.75rem">
  <div class="ca-alert__icon"><i class="fas fa-ban"></i></div>
  <div>
    <div class="ca-alert__title">{{ __('app.loan_alert_rej_title') }}</div>
    {{ __('app.loan_alert_rej_body') }}
  </div>
</div>
@endif

{{-- ── Balance card --}}
<div class="ca-premium-card" style="margin-top:.75rem;min-height:auto">
  <div class="ca-card-label">
    <i class="fas fa-file-contract" style="font-size:.8rem"></i>
    {{ __('app.loan_card_label') }}
  </div>
  <div class="ca-card-name">{{ $loan->objet ?? $loan->reference }}</div>
  <div class="ca-card-balance-label">{{ __('app.loan_amount') }}</div>
  <div class="ca-card-balance" style="font-size:1.75rem">
    <sup>{{ $loan->currency }}</sup>{{ number_format($loan->amount, 2, ',', ' ') }}
  </div>
  <div class="ca-card-footer" style="margin-top:.875rem">
    <span style="font-size:.78rem;color:rgba(255,255,255,.5)">
      {{ __('app.monthly') }}: <strong style="color:var(--ca-gold-l)">{{ number_format($loan->monthly_payment,2,',',' ') }} {{ $loan->currency }}</strong>
    </span>
    <span style="font-size:.72rem;color:rgba(255,255,255,.4)">{{ $loan->darly }} {{ __('app.months') }}</span>
  </div>
</div>

{{-- ── Timeline --}}
@if($loan->status !== 'rejected')
<div style="margin:.875rem 0 .5rem;padding:0 1.25rem">
  <div style="font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--ca-text-3);margin-bottom:.875rem">
    {{ __('app.progress') }}
  </div>
</div>
<div class="ca-steps">
  @foreach($steps as $key => $label)
  @php
    $i = array_search($key, $stepKeys);
    $done = $currentIdx !== false && $i <= $currentIdx;
    $cur  = $loan->status === $key;
  @endphp
  <div class="ca-step {{ $cur ? 'current' : ($done ? 'done' : '') }}">
    <div class="ca-step__dot">
      @if($done && !$cur)<i class="fas fa-check" style="font-size:.5rem"></i>
      @else {{ $i+1 }}
      @endif
    </div>
    <div class="ca-step__label">{{ $label }}</div>
  </div>
  @endforeach
</div>
@endif

{{-- ── Controles dossier --}}
<div style="margin:.875rem 0 .25rem;padding:0 1.25rem">
  <div style="font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--ca-text-3)">
    {{ strtoupper(__('app.controls')) }}
  </div>
</div>
<div class="ca-controls">
  <a href="{{ route('client.app.transfer.send') }}" class="ca-control-btn">
    <div class="ca-control-btn__icon" style="background:rgba(27,138,122,.18);color:var(--ca-teal-l)">
      <i class="fas fa-paper-plane"></i>
    </div>
    <div class="ca-control-btn__label">{{ __('app.action_send') }}</div>
    <div class="ca-control-btn__sub">{{ __('app.action_transfer') }}</div>
  </a>
  <a href="{{ route('client.app.analytics') }}" class="ca-control-btn">
    <div class="ca-control-btn__icon" style="background:rgba(139,92,246,.18);color:var(--ca-purple)">
      <i class="fas fa-chart-pie"></i>
    </div>
    <div class="ca-control-btn__label">{{ __('app.action_analytics') }}</div>
    <div class="ca-control-btn__sub">{{ __('app.schedule') }}</div>
  </a>
</div>

{{-- ── Details du financement --}}
<div class="ca-detail-card">
  <div class="ca-detail-card__header">
    <span class="ca-detail-card__title">{{ __('app.loan_details') }}</span>
  </div>
  @foreach([
    ['fa-coins',         __('app.loan_capital'),   number_format($principal,2,',',' ').' '.$loan->currency],
    ['fa-percent',       __('app.rate'),            $loan->interest_rate.' %'],
    ['fa-chart-line',    __('app.loan_interest'),   number_format($interest,2,',',' ').' '.$loan->currency],
    ['fa-calculator',    __('app.loan_total'),      number_format($loan->total_with_interest,2,',',' ').' '.$loan->currency],
    ['fa-receipt',       __('app.loan_fees'),       $loan->admin_fees ? number_format($loan->admin_fees,2,',',' ').' '.$loan->currency : '—'],
    ['fa-calendar-day',  __('app.loan_start'),      $loan->start_date?->format('d/m/Y') ?? '—'],
    ['fa-tag',           __('app.loan_object'),     $loan->objet ?? '—'],
  ] as [$icon, $label, $val])
  <div class="ca-detail-row">
    <div class="ca-detail-row__left">
      <div class="ca-detail-row__icon"><i class="fas {{ $icon }}"></i></div>
      <span class="ca-detail-row__label">{{ $label }}</span>
    </div>
    <span class="ca-detail-row__val">{{ $val }}</span>
  </div>
  @endforeach
</div>

{{-- ── Suivi --}}
<div class="ca-detail-card">
  <div class="ca-detail-card__header">
    <span class="ca-detail-card__title">{{ __('app.loan_tracking') }}</span>
  </div>
  @foreach([
    ['fa-hashtag',       __('app.loan_ref'),       $loan->reference],
    ['fa-calendar-plus', __('app.loan_opened'),    $loan->created_at->format('d/m/Y')],
    ['fa-check-double',  __('app.loan_validated'), $loan->validated_at?->format('d/m/Y') ?? '—'],
    ['fa-envelope-open', __('app.loan_sent'),      $loan->sent_at?->format('d/m/Y') ?? '—'],
    ['fa-file-check',    __('app.loan_signed'),    $loan->signed_received_at?->format('d/m/Y') ?? '—'],
    ['fa-user-tie',      __('app.loan_advisor'),   $loan->admin?->name ?? '—'],
  ] as [$icon, $label, $val])
  <div class="ca-detail-row">
    <div class="ca-detail-row__left">
      <div class="ca-detail-row__icon"><i class="fas {{ $icon }}"></i></div>
      <span class="ca-detail-row__label">{{ $label }}</span>
    </div>
    <span class="ca-detail-row__val">{{ $val }}</span>
  </div>
  @endforeach
</div>

{{-- ── Amortissement --}}
@if($loan->amortization_schedule && $loan->status !== 'draft')
<div style="margin:.5rem 1.25rem .5rem;font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--ca-text-3)">
  {{ __('app.amortization') }} — {{ count($loan->amortization_schedule) }} {{ __('app.installments') }}
</div>
<div class="ca-table-wrap" style="max-height:260px;overflow-y:auto">
  <table class="ca-table">
    <thead>
      <tr>
        <th>{{ __('app.amort_num') }}</th>
        <th>{{ __('app.monthly') }}</th>
        <th>{{ __('app.loan_capital') }}</th>
        <th>{{ __('app.loan_interest') }}</th>
        <th>{{ __('app.amort_remaining') }}</th>
      </tr>
    </thead>
    <tbody>
      @foreach($loan->amortization_schedule as $row)
      <tr>
        <td class="td-muted">{{ $row['month'] }}</td>
        <td class="td-bold">{{ number_format($row['payment'],2,',',' ') }}</td>
        <td class="td-pos">{{ number_format($row['principal'],2,',',' ') }}</td>
        <td class="td-neg">{{ number_format($row['interest'],2,',',' ') }}</td>
        <td class="td-muted">{{ number_format($row['balance'],2,',',' ') }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>
@endif

<div style="height:1rem"></div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  if (typeof buildDoughnutChart !== 'undefined' && {{ $total }} > 0) {
    buildDoughnutChart(
      'loanDoughnut',
      [{{ $principal }}, {{ $interest }}],
      ['rgba(27,138,122,.7)', 'rgba(200,169,81,.7)'],
      ['{{ __("app.chart_capital") }}', '{{ __("app.chart_interest") }}']
    );
  }
});
</script>
@endpush
