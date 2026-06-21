@extends('layouts.client-app')
@section('title', 'Mes dossiers — Credixa')
@section('page_title', 'Mes dossiers')
@section('back_btn', true)
@section('back_url', route('client.app.home'))

@section('topbar_action')
<span style="font-size:.8rem;font-weight:800;color:var(--ca-teal-l);min-width:28px;text-align:center">
  {{ $loans->count() }}
</span>
@endsection

@push('styles')
<style>
/* ── Summary strip ── */
.dos-strip{
  display:grid;grid-template-columns:repeat(4,1fr);
  gap:.5rem;margin:.75rem 1.25rem 0;
}
.dos-chip{
  background:var(--ca-bg2);border:1px solid var(--ca-border);
  border-radius:14px;padding:.625rem .5rem;text-align:center;
}
.dos-chip__val{font-family:'Space Grotesk',sans-serif;font-size:1.125rem;font-weight:900;color:var(--ca-text);line-height:1}
.dos-chip__lbl{font-size:.6rem;color:var(--ca-text-3);margin-top:.25rem;font-weight:600;text-transform:uppercase;letter-spacing:.04em}

/* ── Filter pills ── */
.dos-filters{
  display:flex;gap:.5rem;padding:.875rem 1.25rem .375rem;
  overflow-x:auto;-webkit-overflow-scrolling:touch;scrollbar-width:none;
}
.dos-filters::-webkit-scrollbar{display:none}
.dos-pill{
  display:inline-flex;align-items:center;gap:.375rem;
  padding:.35rem .875rem;border-radius:999px;
  font-size:.72rem;font-weight:700;white-space:nowrap;
  border:1.5px solid var(--ca-border);
  color:var(--ca-text-3);background:var(--ca-bg2);cursor:pointer;
  transition:.15s;
}
.dos-pill.active{background:rgba(27,138,122,.12);border-color:rgba(27,138,122,.3);color:var(--ca-teal-l)}

/* ── Loan cards ── */
.dos-list{display:flex;flex-direction:column;gap:.625rem;padding:0 1.25rem}

.dos-card{
  display:block;text-decoration:none;
  background:var(--ca-bg2);border:1px solid var(--ca-border);
  border-radius:18px;overflow:hidden;
  transition:.18s;position:relative;
}
.dos-card:active{transform:scale(.99)}

/* colored top bar */
.dos-card__bar{height:3px;width:100%}

.dos-card__inner{padding:1rem 1.125rem .875rem}

/* row 1: ref + badge */
.dos-card__top{display:flex;align-items:center;justify-content:space-between;margin-bottom:.625rem}
.dos-card__ref{font-size:.7rem;font-weight:700;color:var(--ca-text-3);font-family:monospace;letter-spacing:.04em}

/* Status badges */
.dos-badge{
  display:inline-flex;align-items:center;gap:.3rem;
  font-size:.64rem;font-weight:800;text-transform:uppercase;letter-spacing:.05em;
  padding:.18rem .6rem;border-radius:999px;
}
.dos-badge--draft   {background:rgba(148,163,184,.12);color:#94a3b8}
.dos-badge--pending {background:rgba(245,158,11,.12);color:#f59e0b}
.dos-badge--valid   {background:rgba(27,138,122,.12);color:var(--ca-teal-l)}
.dos-badge--sent    {background:rgba(96,165,250,.12);color:#60a5fa}
.dos-badge--signed  {background:rgba(139,92,246,.12);color:#a78bfa}
.dos-badge--final   {background:rgba(200,169,81,.14);color:var(--ca-gold-l)}
.dos-badge--rejected{background:rgba(248,113,113,.1);color:#f87171}

/* Amount */
.dos-card__amount{
  font-family:'Space Grotesk',sans-serif;
  font-size:1.5rem;font-weight:900;color:var(--ca-text);
  line-height:1;margin-bottom:.5rem;
}
.dos-card__amount span{font-size:.8rem;font-weight:600;color:var(--ca-text-3);margin-left:.3rem}

/* Meta row */
.dos-card__meta{
  display:flex;align-items:center;gap:.875rem;
  flex-wrap:wrap;margin-bottom:.75rem;
}
.dos-card__meta-item{
  display:flex;align-items:center;gap:.3rem;
  font-size:.72rem;color:var(--ca-text-3);
}
.dos-card__meta-item strong{color:var(--ca-text-2);font-weight:700}

/* Progress bar */
.dos-prog-wrap{height:4px;background:var(--ca-border);border-radius:999px;overflow:hidden}
.dos-prog-fill{height:100%;border-radius:999px;transition:width .4s}

/* ── Empty ── */
.dos-empty{text-align:center;padding:4rem 2rem 2rem}
.dos-empty__ico{font-size:2.5rem;opacity:.2;margin-bottom:1rem}
.dos-empty__title{font-size:1rem;font-weight:700;color:var(--ca-text-2);margin-bottom:.4rem}
.dos-empty__sub{font-size:.8rem;color:var(--ca-text-3);line-height:1.5}
</style>
@endpush

@section('content')

@if($loans->isEmpty())
<div class="dos-empty">
  <div class="dos-empty__ico"><i class="fas fa-folder-open"></i></div>
  <div class="dos-empty__title">{{ __('app.no_loans_title') }}</div>
  <div class="dos-empty__sub">{{ __('app.no_loans_body') }}</div>
  <div class="ca-btn-wrap" style="margin-top:1.5rem">
    <a href="{{ route('client.app.home') }}" class="ca-btn ca-btn--ghost">
      <i class="fas fa-arrow-left"></i> {{ __('app.nav_home') }}
    </a>
  </div>
</div>
@else

@php
  $finalized = $loans->where('status','finalized')->count();
  $active    = $loans->whereIn('status',['contract_sent','contract_signed'])->count();
  $pending   = $loans->whereIn('status',['pending','validated'])->count();
  $rejected  = $loans->where('status','rejected')->count();
@endphp

{{-- Summary strip ── --}}
<div class="dos-strip">
  <div class="dos-chip">
    <div class="dos-chip__val">{{ $loans->count() }}</div>
    <div class="dos-chip__lbl">Total</div>
  </div>
  <div class="dos-chip">
    <div class="dos-chip__val" style="color:var(--ca-teal-l)">{{ $active }}</div>
    <div class="dos-chip__lbl">Actifs</div>
  </div>
  <div class="dos-chip">
    <div class="dos-chip__val" style="color:var(--ca-gold-l)">{{ $finalized }}</div>
    <div class="dos-chip__lbl">Finalisés</div>
  </div>
  <div class="dos-chip">
    <div class="dos-chip__val" style="color:#f87171">{{ $rejected }}</div>
    <div class="dos-chip__lbl">Rejetés</div>
  </div>
</div>

{{-- Filter pills ── --}}
<div class="dos-filters" x-data="{f:'all'}">
  <button class="dos-pill" :class="f==='all'?'active':''" @click="f='all';filterDos('all')">Tous ({{ $loans->count() }})</button>
  @if($pending)   <button class="dos-pill" :class="f==='pending'?'active':''"  @click="f='pending';filterDos('pending')">En cours ({{ $pending }})</button> @endif
  @if($active)    <button class="dos-pill" :class="f==='active'?'active':''"   @click="f='active';filterDos('active')">Contrat ({{ $active }})</button> @endif
  @if($finalized) <button class="dos-pill" :class="f==='finalized'?'active':''" @click="f='finalized';filterDos('finalized')">Finalisés ({{ $finalized }})</button> @endif
  @if($rejected)  <button class="dos-pill" :class="f==='rejected'?'active':''" @click="f='rejected';filterDos('rejected')">Rejetés ({{ $rejected }})</button> @endif
</div>

{{-- Cards ── --}}
<div class="dos-list" style="margin-top:.375rem;padding-bottom:1.5rem">
@foreach($loans as $loan)
@php
  $steps = ['draft','pending','validated','contract_sent','contract_signed','finalized'];
  $idx   = array_search($loan->status, $steps);
  $pct   = $idx !== false ? round(($idx+1)/count($steps)*100) : 0;

  [$barColor,$badgeCls,$filterGroup] = match($loan->status){
    'draft'           => ['#94a3b8','dos-badge--draft',   'pending'],
    'pending'         => ['#f59e0b','dos-badge--pending',  'pending'],
    'validated'       => ['#2BBAA8','dos-badge--valid',    'pending'],
    'contract_sent'   => ['#60a5fa','dos-badge--sent',     'active'],
    'contract_signed' => ['#a78bfa','dos-badge--signed',   'active'],
    'finalized'       => ['#C8A951','dos-badge--final',    'finalized'],
    'rejected'        => ['#f87171','dos-badge--rejected', 'rejected'],
    default           => ['#94a3b8','dos-badge--draft',    'pending'],
  };
  $statusLabel = $loan->statusLabel();
@endphp
<a href="{{ route('client.app.loans.show', $loan) }}" class="dos-card"
   data-group="{{ $filterGroup }}">
  <div class="dos-card__bar" style="background:{{ $barColor }}"></div>
  <div class="dos-card__inner">
    <div class="dos-card__top">
      <span class="dos-card__ref">{{ $loan->reference }}</span>
      <span class="dos-badge {{ $badgeCls }}">
        <i class="fas fa-circle" style="font-size:.4rem"></i>
        {{ $statusLabel }}
      </span>
    </div>
    <div class="dos-card__amount">
      {{ number_format($loan->amount, 0, ',', ' ') }}
      <span>{{ $loan->currency }}</span>
    </div>
    <div class="dos-card__meta">
      <div class="dos-card__meta-item">
        <i class="fas fa-calendar-day" style="font-size:.65rem"></i>
        <strong>{{ $loan->darly }} mois</strong>
      </div>
      <div class="dos-card__meta-item">
        <i class="fas fa-percent" style="font-size:.65rem"></i>
        <strong>{{ $loan->interest_rate }} %</strong>
      </div>
      <div class="dos-card__meta-item">
        <i class="fas fa-receipt" style="font-size:.65rem"></i>
        <strong>{{ number_format($loan->monthly_payment, 0, ',', ' ') }} {{ $loan->currency }}/mois</strong>
      </div>
    </div>
    @if($loan->status !== 'rejected')
    <div class="dos-prog-wrap">
      <div class="dos-prog-fill" style="width:{{ $pct }}%;background:{{ $barColor }}"></div>
    </div>
    @endif
  </div>
</a>
@endforeach
</div>

@push('scripts')
<script>
function filterDos(group) {
  document.querySelectorAll('.dos-card').forEach(el => {
    el.style.display = (group === 'all' || el.dataset.group === group) ? '' : 'none';
  });
}
</script>
@endpush

@endif
@endsection
