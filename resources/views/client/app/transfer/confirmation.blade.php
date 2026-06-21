@extends('layouts.client-app')
@section('title', __('app.confirm_title') . ' — Credixa')
@section('page_title', __('app.confirm_title'))

@section('topbar_action')
<a href="{{ route('client.app.home') }}" class="ca-topbar__action" style="color:var(--ca-text-3)">
  <i class="fas fa-times"></i>
</a>
@endsection

@section('content')

<div class="ca-confirm-wrap">

  {{-- Cercle de succes --}}
  <div class="ca-confirm-circle">
    <i class="fas fa-check"></i>
  </div>

  <div class="ca-confirm-title">{{ __('app.confirm_success') }}</div>
  <div class="ca-confirm-body">{{ __('app.confirm_body') }}</div>

  @if($transfer)
  <div class="ca-confirm-amount-label">{{ __('app.confirm_total') }}</div>
  <div class="ca-confirm-amount">
    {{ $transfer->currency }} {{ number_format($transfer->amount, 2, ',', ' ') }}
  </div>

  <div class="ca-confirm-details">
    <div class="ca-confirm-row">
      <span class="ca-confirm-row__label">{{ __('app.confirm_recipient') }}</span>
      <span class="ca-confirm-row__val">{{ $transfer->beneficiary_name }}</span>
    </div>
    <div class="ca-confirm-row">
      <span class="ca-confirm-row__label">IBAN</span>
      <span class="ca-confirm-row__val" style="font-family:monospace;font-size:.72rem">
        {{ Str::limit($transfer->beneficiary_iban, 20) }}
      </span>
    </div>
    <div class="ca-confirm-row">
      <span class="ca-confirm-row__label">{{ __('app.confirm_date') }}</span>
      <span class="ca-confirm-row__val">{{ $transfer->processed_at?->format('d/m/Y — H:i') }}</span>
    </div>
    <div class="ca-confirm-row">
      <span class="ca-confirm-row__label">{{ __('app.confirm_txn') }}</span>
      <span class="ca-confirm-row__val" style="font-family:monospace;color:var(--ca-gold-l)">
        {{ $transfer->reference }}
      </span>
    </div>
    <div class="ca-confirm-row">
      <span class="ca-confirm-row__label">{{ __('app.confirm_method') }}</span>
      <span class="ca-confirm-row__val">Credixa — {{ strtoupper($transfer->currency) }}</span>
    </div>
    @if($transfer->note)
    <div class="ca-confirm-row">
      <span class="ca-confirm-row__label">Note</span>
      <span class="ca-confirm-row__val">{{ $transfer->note }}</span>
    </div>
    @endif
  </div>

  <div class="ca-download-link" onclick="window.print()">
    <i class="fas fa-download"></i> {{ __('app.download_receipt') }}
  </div>
  @endif

</div>

<div class="ca-btn-wrap">
  <a href="{{ route('client.app.home') }}" class="ca-btn ca-btn--primary">
    <i class="fas fa-home"></i> {{ __('app.back_home') }}
  </a>
</div>

<div style="text-align:center;padding:.5rem 1.25rem 1rem">
  <a href="{{ route('client.app.transfer.send') }}"
     style="font-size:.82rem;color:var(--ca-teal-l);font-weight:600">
    {{ __('app.another_transfer') }}
  </a>
</div>

@endsection
