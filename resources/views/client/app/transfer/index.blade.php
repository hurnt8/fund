@extends('layouts.client-app')
@section('title', __('app.nav_transfer') . ' — Credixa')
@section('page_title', __('app.nav_transfer'))

@section('content')

@php $currency = $user->currency ?? 'EUR'; @endphp

{{-- Solde rapide --}}
<div style="margin:1rem 1.25rem .5rem;padding:1rem 1.25rem;background:linear-gradient(135deg,rgba(27,138,122,.15),rgba(27,138,122,.05));border:1px solid rgba(27,138,122,.25);border-radius:var(--ca-radius-md)">
  <div style="font-size:.65rem;text-transform:uppercase;letter-spacing:.08em;color:rgba(255,255,255,.4);margin-bottom:.3rem">
    {{ __('app.balance') }}
  </div>
  <div style="font-family:'Space Grotesk',sans-serif;font-size:1.6rem;font-weight:800;color:#fff">
    {{ $currency }} {{ number_format((float)$user->balance, 2, ',', ' ') }}
  </div>
  <div style="font-size:.72rem;color:rgba(255,255,255,.4);margin-top:.2rem">
    {{ __('app.available') }}
  </div>
</div>

{{-- Actions principales --}}
<div style="padding:0 1.25rem;display:grid;grid-template-columns:1fr 1fr;gap:.875rem;margin-top:1rem">

  {{-- Envoyer --}}
  <a href="{{ route('client.app.transfer.send') }}"
     style="display:flex;flex-direction:column;align-items:center;justify-content:center;gap:.625rem;padding:1.5rem 1rem;background:linear-gradient(145deg,rgba(27,138,122,.2),rgba(27,138,122,.08));border:1px solid rgba(27,138,122,.3);border-radius:var(--ca-radius-md);text-decoration:none;transition:all .2s ease">
    <div style="width:52px;height:52px;border-radius:50%;background:linear-gradient(145deg,#2BBAA8,#1B8A7A);display:flex;align-items:center;justify-content:center;font-size:1.3rem;color:#fff;box-shadow:0 4px 16px rgba(27,138,122,.4)">
      <i class="fas fa-paper-plane"></i>
    </div>
    <div style="font-size:.9rem;font-weight:700;color:var(--ca-text)">{{ __('app.action_send') }}</div>
    <div style="font-size:.72rem;color:var(--ca-text-3);text-align:center">{{ __('app.send_subtitle') }}</div>
  </a>

  {{-- Recevoir --}}
  <a href="{{ route('client.app.transfer.receive') }}"
     style="display:flex;flex-direction:column;align-items:center;justify-content:center;gap:.625rem;padding:1.5rem 1rem;background:linear-gradient(145deg,rgba(200,169,81,.15),rgba(200,169,81,.05));border:1px solid rgba(200,169,81,.25);border-radius:var(--ca-radius-md);text-decoration:none;transition:all .2s ease">
    <div style="width:52px;height:52px;border-radius:50%;background:linear-gradient(145deg,#D4AA55,#C8A951);display:flex;align-items:center;justify-content:center;font-size:1.3rem;color:#fff;box-shadow:0 4px 16px rgba(200,169,81,.35)">
      <i class="fas fa-arrow-down-to-line"></i>
    </div>
    <div style="font-size:.9rem;font-weight:700;color:var(--ca-text)">{{ __('app.action_receive') }}</div>
    <div style="font-size:.72rem;color:var(--ca-text-3);text-align:center">{{ __('app.receive_subtitle_short') }}</div>
  </a>

</div>

{{-- Historique transferts --}}
@if($transfers->isNotEmpty())
<div class="ca-section" style="margin-top:1.25rem">
  <span class="ca-section__title">{{ __('app.recent_transactions') }}</span>
</div>
<div class="ca-txn-list">
  @foreach($transfers as $t)
  <div class="ca-txn-item">
    <div class="ca-txn-icon" style="background:rgba(255,90,90,.1)">
      <i class="fas fa-paper-plane" style="color:var(--ca-negative)"></i>
    </div>
    <div class="ca-txn-info">
      <div class="ca-txn-title">{{ $t->beneficiary_name ?? '—' }}</div>
      <div class="ca-txn-sub" style="font-family:monospace;font-size:.68rem">{{ $t->reference }}</div>
    </div>
    <div style="text-align:right;flex-shrink:0">
      <div class="ca-txn-amount negative">-{{ number_format($t->amount, 2, ',', ' ') }}</div>
      <div class="ca-txn-date">{{ $t->processed_at?->format('d/m H:i') }}</div>
    </div>
  </div>
  @endforeach
</div>
@else
<div class="ca-empty" style="padding:2rem 0">
  <div class="ca-empty__icon"><i class="fas fa-right-left"></i></div>
  <div class="ca-empty__title">{{ __('app.no_activity') }}</div>
</div>
@endif

<div style="height:1.5rem"></div>
@endsection
