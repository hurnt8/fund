@extends('layouts.client-app')
@section('title', __('app.receive_title') . ' — Credixa')
@section('page_title', __('app.receive_title'))
@section('back_btn', true)
@section('back_url', route('client.app.home'))

@section('content')

@php
  $iban = $user->bank_account ?? 'Non renseigne';
  $bic  = $user->bic ?? 'CREDIXAFR';
@endphp

{{-- Sous-titre --}}
<div style="padding:.875rem 1.25rem 0;font-size:.82rem;color:var(--ca-text-3)">
  {{ __('app.receive_subtitle') }}
</div>

{{-- Avatar --}}
<div class="ca-profile-header" style="padding:1.25rem 1.25rem .75rem">
  <div class="ca-profile-avatar" style="width:68px;height:68px;font-size:1.5rem">
    {{ strtoupper(substr($user->name, 0, 1)) }}
  </div>
  <div class="ca-profile-name" style="font-size:.95rem">{{ $user->name }}</div>
  <div class="ca-profile-since" style="color:var(--ca-teal-l);font-size:.75rem">
    {{ __('app.premium_member') }}
  </div>
</div>

{{-- IBAN --}}
<div class="ca-iban-card">
  <div style="font-size:.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--ca-text-3)">
    {{ __('app.receive_iban') }}
  </div>
  <div class="ca-iban-value">{{ $iban }}</div>
  <button class="ca-copy-btn" onclick="copyToClipboard('{{ $iban }}', this)">
    <i class="fas fa-copy"></i> {{ __('app.copy_iban') }}
  </button>
</div>

{{-- BIC / Titulaire / Banque --}}
<div class="ca-detail-card">
  @foreach([
    ['fa-building-columns', __('app.receive_bic'),  $bic],
    ['fa-user',             __('app.receive_name'), $user->name],
    ['fa-landmark',         __('app.receive_bank'), 'Credixa Financial'],
    ['fa-envelope',         'Email',                $user->email],
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

{{-- Partager --}}
<div class="ca-btn-wrap">
  <button class="ca-btn ca-btn--primary"
          onclick="if(navigator.share){navigator.share({title:'Coordonnees Credixa',text:'IBAN: {{ $iban }}\nBIC: {{ $bic }}\nTitulaire: {{ $user->name }}'}).catch(()=>{})}">
    <i class="fas fa-share-nodes"></i> {{ __('app.share_details') }}
  </button>
</div>

<div class="ca-divider">{{ __('app.or') }}</div>

<div class="ca-btn-wrap" style="padding-top:0">
  <a href="{{ route('client.app.home') }}" class="ca-btn ca-btn--ghost">
    <i class="fas fa-arrow-left"></i> {{ __('app.back_home') }}
  </a>
</div>

@endsection
