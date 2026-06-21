@extends('layouts.client-app')
@section('title', __('app.send_title') . ' — Credixa')
@section('page_title', __('app.send_title'))
@section('back_btn', true)
@section('back_url', route('client.app.home'))

@section('content')

@php $balance = (float) $user->balance; @endphp

<div x-data="keypad('')" style="display:flex;flex-direction:column;min-height:calc(100dvh - 130px)">

  {{-- Beneficiaire --}}
  <div class="ca-send-recipient" style="margin-top:.75rem">
    <div class="ca-send-recipient__avatar">
      <i class="fas fa-user"></i>
    </div>
    <div style="flex:1">
      <div class="ca-send-recipient__name">{{ __('app.send_to') }}</div>
      <div class="ca-send-recipient__sub">{{ __('app.send_recipient_hint') }}</div>
    </div>
  </div>

  {{-- Formulaire champs --}}
  <form method="POST" action="{{ route('client.app.transfer.send.process') }}" id="sendForm">
    @csrf

    <div class="ca-form" style="margin-top:.5rem">
      @error('amount')
      <div class="ca-flash ca-flash--err" style="margin:0 0 .75rem"><i class="fas fa-exclamation-triangle"></i> {{ $message }}</div>
      @enderror

      <div class="ca-form-group">
        <label class="ca-form-label">{{ __('app.send_name') }}</label>
        <input type="text" name="beneficiary_name" class="ca-form-input"
               placeholder="Jean Dupont" value="{{ old('beneficiary_name') }}" required>
      </div>
      <div class="ca-form-group">
        <label class="ca-form-label">{{ __('app.send_iban') }}</label>
        <input type="text" name="beneficiary_iban" class="ca-form-input"
               placeholder="FR76 XXXX XXXX XXXX XXXX XXXX XXX" value="{{ old('beneficiary_iban') }}" required>
      </div>
    </div>

    {{-- Affichage montant --}}
    <div class="ca-amount-display">
      <div class="ca-amount-display__val">
        <sup>{{ $user->currency ?? 'EUR' }}</sup><span x-text="display">0</span>
      </div>
      <div class="ca-amount-display__available">
        {{ __('app.available') }}:
        <strong>{{ number_format($balance, 2, ',', ' ') }} {{ $user->currency ?? 'EUR' }}</strong>
      </div>
    </div>

    {{-- Note --}}
    <textarea name="note" class="ca-note-field" rows="1"
              placeholder="{{ __('app.send_note') }}">{{ old('note') }}</textarea>

    {{-- Input cache pour le montant --}}
    <input type="hidden" name="amount" :value="numericValue">

    {{-- Clavier numerique --}}
    <div class="ca-keypad">
      @foreach(['1','2','3','4','5','6','7','8','9','.','0','del'] as $k)
      @if($k === 'del')
        <button type="button" class="ca-key ca-key--del" @click="press('del')">
          <i class="fas fa-delete-left"></i>
        </button>
      @else
        <button type="button" class="ca-key" @click="press('{{ $k }}')">{{ $k }}</button>
      @endif
      @endforeach
    </div>

    {{-- Bouton envoyer --}}
    <div class="ca-btn-wrap">
      <button type="submit" class="ca-btn ca-btn--primary"
              :disabled="numericValue <= 0 || numericValue > {{ $balance }}"
              :style="numericValue <= 0 ? 'opacity:.5;pointer-events:none' : ''">
        <i class="fas fa-paper-plane"></i>
        {{ __('app.send_btn') }}
        <span x-show="numericValue > 0">
          — <span x-text="display"></span> {{ $user->currency ?? 'EUR' }}
        </span>
      </button>
    </div>

  </form>
</div>

@endsection
