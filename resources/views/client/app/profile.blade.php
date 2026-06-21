@extends('layouts.client-app')
@section('title', __('app.profile_title') . ' — Credixa')
@section('page_title', __('app.profile_title'))
@section('back_btn', true)
@section('back_url', route('client.app.home'))

@section('content')

{{-- Header profil --}}
<div class="ca-profile-header">
  <div class="ca-profile-avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
  <div class="ca-profile-name">{{ $user->name }}</div>
  <div class="ca-profile-badge">
    <i class="fas fa-crown" style="font-size:.65rem"></i>
    {{ __('app.premium_member') }}
  </div>
  <div class="ca-profile-since">
    {{ __('app.member_since') }} {{ $user->created_at->format('F Y') }}
  </div>
</div>

{{-- Balance rapide --}}
<div style="margin:0 1.25rem .875rem;background:linear-gradient(135deg,#1B4976,#0D2E52);border-radius:var(--ca-radius-md);padding:1rem 1.25rem;border:1px solid rgba(255,255,255,.08)">
  <div style="font-size:.65rem;color:rgba(255,255,255,.45);text-transform:uppercase;letter-spacing:.07em;margin-bottom:.25rem">{{ __('app.balance') }}</div>
  <div style="font-family:'Space Grotesk',sans-serif;font-size:1.5rem;font-weight:800;color:#fff">
    {{ $user->currency ?? 'EUR' }} {{ number_format((float)$user->balance, 2, ',', ' ') }}
  </div>
</div>

{{-- Parametres du compte --}}
<div class="ca-settings-group">
  <div class="ca-settings-label">{{ __('app.account_settings') }}</div>
  <div class="ca-settings-list">
    <div class="ca-settings-item">
      <div class="ca-settings-item__icon" style="background:rgba(27,138,122,.18);color:var(--ca-teal-l)">
        <i class="fas fa-user"></i>
      </div>
      <div class="ca-settings-item__text">
        <div class="ca-settings-item__label">{{ __('app.personal_info') }}</div>
        <div class="ca-settings-item__sub">{{ $user->email }}</div>
      </div>
      <div class="ca-settings-item__right"><i class="fas fa-chevron-right"></i></div>
    </div>
    <div class="ca-settings-item">
      <div class="ca-settings-item__icon" style="background:rgba(200,169,81,.18);color:var(--ca-gold-l)">
        <i class="fas fa-credit-card"></i>
      </div>
      <div class="ca-settings-item__text">
        <div class="ca-settings-item__label">{{ __('app.payment_methods') }}</div>
        <div class="ca-settings-item__sub">{{ $user->bank_account ? Str::limit($user->bank_account, 22) : __('app.not_configured') }}</div>
      </div>
      <div class="ca-settings-item__right"><i class="fas fa-chevron-right"></i></div>
    </div>
  </div>
</div>

{{-- Securite et preferences --}}
<div class="ca-settings-group">
  <div class="ca-settings-label">{{ __('app.security') }}</div>
  <div class="ca-settings-list">

    {{-- Notifications --}}
    <div class="ca-settings-item">
      <div class="ca-settings-item__icon" style="background:rgba(74,158,255,.18);color:var(--ca-blue)">
        <i class="fas fa-bell"></i>
      </div>
      <div class="ca-settings-item__text">
        <div class="ca-settings-item__label">{{ __('app.notifications') }}</div>
      </div>
      <div class="ca-settings-item__right">
        <label class="ca-toggle" x-data="togglePref(true)">
          <input type="checkbox" :checked="on" @change="toggle()">
          <div class="ca-toggle__track"></div>
          <div class="ca-toggle__thumb"></div>
        </label>
      </div>
    </div>

    {{-- Dark / Light mode --}}
    <div class="ca-settings-item" x-data="themeToggle()">
      <div class="ca-settings-item__icon" style="background:rgba(139,92,246,.18);color:var(--ca-purple)">
        <i class="fas fa-moon" x-show="isDark"></i>
        <i class="fas fa-sun"  x-show="!isDark"></i>
      </div>
      <div class="ca-settings-item__text">
        <div class="ca-settings-item__label">{{ __('app.dark_mode') }}</div>
        <div class="ca-settings-item__sub" x-text="isDark ? 'Dark' : 'Light'"></div>
      </div>
      <div class="ca-settings-item__right">
        <label class="ca-toggle">
          <input type="checkbox" :checked="isDark" @change="toggle()">
          <div class="ca-toggle__track"></div>
          <div class="ca-toggle__thumb"></div>
        </label>
      </div>
    </div>

    {{-- Langue --}}
    <div class="ca-settings-item" x-data="langMenu()" @click.stop>
      <div class="ca-settings-item__icon" style="background:rgba(245,158,11,.18);color:var(--ca-amber)">
        <i class="fas fa-globe"></i>
      </div>
      <div class="ca-settings-item__text">
        <div class="ca-settings-item__label">{{ __('app.language_pref') }}</div>
        <div class="ca-settings-item__sub">{{ ['fr'=>'Francais','en'=>'English','pl'=>'Polski','es'=>'Espanol'][app()->getLocale()] ?? app()->getLocale() }}</div>
      </div>
      <div class="ca-settings-item__right" @click="toggle()">
        <i class="fas fa-chevron-right"></i>
      </div>
      <div x-show="open" @click.outside="close()" x-transition
           style="position:fixed;bottom:calc(var(--ca-nav-h) + 1rem);left:1.25rem;right:1.25rem;background:var(--ca-bg4);border:1px solid var(--ca-border);border-radius:var(--ca-radius-md);overflow:hidden;z-index:600;box-shadow:0 -8px 32px rgba(0,0,0,.4)">
        @foreach(['fr'=>'Francais','en'=>'English','pl'=>'Polski','es'=>'Espanol'] as $lc => $label)
        <form method="POST" action="{{ route('client.app.locale') }}">
          @csrf
          <input type="hidden" name="locale" value="{{ $lc }}">
          <button type="submit"
                  style="width:100%;padding:.875rem 1.25rem;background:none;border:none;border-bottom:1px solid var(--ca-border-2);color:{{ app()->getLocale()===$lc ? 'var(--ca-teal-l)' : 'var(--ca-text-2)' }};font-size:.875rem;text-align:left;cursor:pointer;font-family:inherit;font-weight:{{ app()->getLocale()===$lc ? '700' : '400' }};display:flex;justify-content:space-between;align-items:center">
            {{ $label }}
            @if(app()->getLocale() === $lc)
            <i class="fas fa-check" style="color:var(--ca-teal-l)"></i>
            @endif
          </button>
        </form>
        @endforeach
      </div>
    </div>

  </div>
</div>

{{-- Deconnexion --}}
<div class="ca-settings-group">
  <div class="ca-settings-list">
    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button type="submit" class="ca-settings-item" style="width:100%;background:none;border:none;cursor:pointer">
        <div class="ca-settings-item__icon" style="background:rgba(255,90,90,.15);color:var(--ca-negative)">
          <i class="fas fa-sign-out-alt"></i>
        </div>
        <div class="ca-settings-item__text">
          <div class="ca-settings-item__label" style="color:var(--ca-negative)">{{ __('app.logout') }}</div>
        </div>
      </button>
    </form>
  </div>
</div>

{{-- Version --}}
<div style="text-align:center;padding:1.5rem;font-size:.7rem;color:var(--ca-text-3)">
  Credixa Mobile &nbsp;&bull;&nbsp; v2.0.0
</div>

@endsection
