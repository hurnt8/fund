@extends('layouts.client-app')
@section('title', __('app.analytics_title') . ' — Credixa')
@section('page_title', __('app.analytics_title'))
@section('back_btn', true)
@section('back_url', route('client.app.home'))

@section('content')

@php
  $currency = Auth::user()->currency ?? 'EUR';
  $totalSchedule = $loans->sum(fn($l) => (float) $l->total_with_interest);
  $totalCapital  = $loans->sum(fn($l) => (float) $l->amount);
  $totalInterest = max(0, $totalSchedule - $totalCapital);
  $loanCount     = $loans->count();
  $hasData       = $totalSchedule > 0;

  // Labels + valeurs pour le chart en donut
  $donutData   = [$totalCapital, $totalInterest, (float) $totalPaid];
  $donutColors = ['rgba(27,138,122,.75)', 'rgba(200,169,81,.75)', 'rgba(255,90,90,.75)'];
  $donutLabels = [__('app.chart_capital'), __('app.chart_interest'), __('app.chart_transfers')];

  // Top 10 mensualites pour le line chart
  $lineLabels = array_keys(array_slice($monthlyData, 0, 10));
  $lineValues = array_values(array_slice($monthlyData, 0, 10));
@endphp

{{-- Period tabs --}}
<div x-data="{ period: 'month' }" style="margin-top:.75rem">

  <div class="ca-period-tabs">
    @foreach(['day'=>__('app.period_day'),'week'=>__('app.period_week'),'month'=>__('app.period_month'),'year'=>__('app.period_year')] as $p => $label)
    <button class="ca-period-tab" :class="period==='{{ $p }}' ? 'active' : ''"
            @click="period='{{ $p }}'" type="button">
      {{ $label }}
    </button>
    @endforeach
  </div>

  {{-- Hero total --}}
  <div class="ca-chart-hero">
    <div class="ca-chart-hero__amount">
      {{ $currency }} {{ number_format($totalSchedule, 2, ',', ' ') }}
    </div>
    <div class="ca-chart-hero__label">{{ __('app.total_schedule') }}</div>
  </div>

  {{-- Doughnut chart --}}
  @if($hasData)
  <div class="ca-chart-container" style="padding:0 1.25rem">
    <canvas id="analyticsDonut" style="max-height:220px"></canvas>
    <div class="ca-chart-center-label">
      <div class="ca-chart-center-label__val">{{ $loanCount }}</div>
      <div class="ca-chart-center-label__sub">{{ __('app.categories') }}</div>
    </div>
  </div>
  @endif

  {{-- Stats cards --}}
  <div class="ca-stats-row" style="margin-top:.75rem">
    <div class="ca-stat-chip">
      <div class="ca-stat-chip__val" style="font-size:1rem;color:var(--ca-teal-l)">
        {{ $currency }} {{ number_format($totalReceived, 0, ',', ' ') }}
      </div>
      <div class="ca-stat-chip__lbl">{{ __('app.total_received') }}</div>
    </div>
    <div class="ca-stat-chip">
      <div class="ca-stat-chip__val" style="font-size:1rem;color:var(--ca-negative)">
        {{ $currency }} {{ number_format($totalPaid, 0, ',', ' ') }}
      </div>
      <div class="ca-stat-chip__lbl">{{ __('app.total_sent') }}</div>
    </div>
    <div class="ca-stat-chip">
      <div class="ca-stat-chip__val" style="font-size:1rem;color:var(--ca-gold-l)">
        {{ $currency }} {{ number_format($totalInterest, 0, ',', ' ') }}
      </div>
      <div class="ca-stat-chip__lbl">Interets</div>
    </div>
  </div>

  {{-- Top categories --}}
  <div class="ca-section" style="margin-top:.5rem">
    <span class="ca-section__title">{{ __('app.top_categories') }}</span>
  </div>

  <div class="ca-txn-list">

    {{-- Prets --}}
    @php $loanTotal = $totalCapital; $maxVal = max($loanTotal, (float)$totalPaid, $totalInterest, 1); @endphp
    <div class="ca-category-item">
      <div class="ca-category-icon" style="background:rgba(27,138,122,.15);color:var(--ca-teal-l)">
        <i class="fas fa-file-contract"></i>
      </div>
      <div class="ca-category-info">
        <div class="ca-category-name">{{ __('app.cat_loans') }}</div>
        <div class="ca-category-bar">
          <div class="ca-category-fill" style="--cat-color:var(--ca-teal-l);width:{{ min(100, $loanTotal/$maxVal*100) }}%"></div>
        </div>
      </div>
      <div class="ca-category-amt">{{ number_format($loanTotal, 0, ',', ' ') }}</div>
    </div>

    {{-- Interets --}}
    <div class="ca-category-item">
      <div class="ca-category-icon" style="background:rgba(200,169,81,.15);color:var(--ca-gold-l)">
        <i class="fas fa-percent"></i>
      </div>
      <div class="ca-category-info">
        <div class="ca-category-name">{{ __('app.cat_fees') }}</div>
        <div class="ca-category-bar">
          <div class="ca-category-fill" style="--cat-color:var(--ca-gold-l);width:{{ min(100, $totalInterest/$maxVal*100) }}%"></div>
        </div>
      </div>
      <div class="ca-category-amt">{{ number_format($totalInterest, 0, ',', ' ') }}</div>
    </div>

    {{-- Virements --}}
    <div class="ca-category-item">
      <div class="ca-category-icon" style="background:rgba(255,90,90,.1);color:var(--ca-negative)">
        <i class="fas fa-right-left"></i>
      </div>
      <div class="ca-category-info">
        <div class="ca-category-name">{{ __('app.cat_transfers') }}</div>
        <div class="ca-category-bar">
          <div class="ca-category-fill" style="--cat-color:var(--ca-negative);width:{{ min(100, (float)$totalPaid/$maxVal*100) }}%"></div>
        </div>
      </div>
      <div class="ca-category-amt">{{ number_format($totalPaid, 0, ',', ' ') }}</div>
    </div>

  </div>

  {{-- Line chart echeancier --}}
  @if(count($lineLabels) > 1)
  <div class="ca-section" style="margin-top:.75rem">
    <span class="ca-section__title">{{ __('app.monthly_schedule') }}</span>
  </div>
  <div style="padding:0 1.25rem 1rem;background:var(--ca-bg3);margin:0 1.25rem;border-radius:var(--ca-radius-md);border:1px solid var(--ca-border)">
    <canvas id="analyticsLine" style="max-height:160px;margin-top:1rem"></canvas>
  </div>
  @endif

</div>

<div style="height:1rem"></div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  @if($hasData)
  buildDoughnutChart(
    'analyticsDonut',
    {!! json_encode($donutData) !!},
    {!! json_encode($donutColors) !!},
    {!! json_encode($donutLabels) !!}
  );
  @endif

  @if(count($lineLabels) > 1)
  buildLineChart(
    'analyticsLine',
    {!! json_encode($lineLabels) !!},
    {!! json_encode($lineValues) !!},
    '{{ $currency }}'
  );
  @endif
});
</script>
@endpush
