@extends('layouts.client-app')
@section('title', __('app.title'))

{{-- Topbar home personnalise --}}
@section('topbar')
<header class="ca-topbar ca-topbar--home" style="padding-top:calc(.75rem + var(--ca-safe-top))">
  <div class="ca-home-header" style="flex:1">
    <div class="ca-home-header__left">
      <div class="ca-avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
      <div>
        <div class="ca-home-header__greeting">{{ __('app.welcome_back') }}</div>
        <div class="ca-home-header__name">{{ Str::words($user->name, 2, '') }}</div>
      </div>
    </div>
    <div style="display:flex;gap:.5rem">
      <div x-data="langMenu()" style="position:relative">
        <button class="ca-topbar__action" @click="toggle()" title="{{ __('app.language') }}">
          <i class="fas fa-globe"></i>
        </button>
        <div x-show="open" @click.outside="close()" x-transition
             style="position:absolute;right:0;top:46px;background:var(--ca-bg4);border:1px solid var(--ca-border);border-radius:var(--ca-radius-md);min-width:140px;overflow:hidden;z-index:500;box-shadow:0 8px 32px rgba(0,0,0,.4)">
          @foreach(['fr'=>'Francais','en'=>'English','pl'=>'Polski','es'=>'Espanol'] as $lc => $label)
          <form method="POST" action="{{ route('client.app.locale') }}">
            @csrf
            <input type="hidden" name="locale" value="{{ $lc }}">
            <button type="submit" style="width:100%;padding:.6rem 1rem;background:none;border:none;color:{{ app()->getLocale()===$lc ? 'var(--ca-teal-l)' : 'var(--ca-text-2)' }};font-size:.82rem;text-align:left;cursor:pointer;font-family:inherit;font-weight:{{ app()->getLocale()===$lc ? '700' : '400' }}">
              {{ $label }}
            </button>
          </form>
          @endforeach
        </div>
      </div>
      <div class="ca-topbar__action"><i class="fas fa-bell"></i></div>
    </div>
  </div>
</header>
@endsection

@section('content')

{{-- ── Premium Balance Card ──────────────────────────────────── --}}
<div class="ca-premium-card" x-data="{ shown: true }">
  <div class="ca-balance-toggle" @click="shown = !shown">
    <i :class="shown ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
  </div>
  <div class="ca-card-label">
    <i class="fas fa-landmark" style="font-size:.8rem"></i>
    CREDIXA &nbsp;·&nbsp; {{ __('app.account_num') }}
  </div>
  <div class="ca-card-name">{{ $user->name }}</div>
  <div class="ca-card-balance-label">{{ __('app.balance') }}</div>
  <div class="ca-card-balance" x-show="shown" x-transition>
    <sup>{{ $user->currency ?? 'EUR' }}</sup>{{ number_format((float)$user->balance, 2, ',', ' ') }}
  </div>
  <div class="ca-card-balance" x-show="!shown" style="letter-spacing:.25em;color:rgba(255,255,255,.3)">
    &bull; &bull; &bull; &bull; &bull; &bull;
  </div>
  <div class="ca-card-footer">
    <span class="ca-card-dots">&bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull; {{ str_pad(substr($user->id, -4), 4, '0', STR_PAD_LEFT) }}</span>
    <span class="ca-card-currency">
      <i class="fas fa-shield-alt" style="font-size:.65rem"></i>
      {{ $user->currency ?? 'EUR' }}
    </span>
  </div>
</div>

{{-- ── Stats chips ───────────────────────────────────────────── --}}
<div class="ca-stats-row">
  <div class="ca-stat-chip">
    <div class="ca-stat-chip__val">{{ $loans->count() }}</div>
    <div class="ca-stat-chip__lbl">{{ __('app.stat_total') }}</div>
  </div>
  <div class="ca-stat-chip">
    <div class="ca-stat-chip__val" style="color:var(--ca-teal-l)">{{ $activeLoans->count() }}</div>
    <div class="ca-stat-chip__lbl">{{ __('app.stat_active') }}</div>
  </div>
  <div class="ca-stat-chip">
    <div class="ca-stat-chip__val" style="color:var(--ca-amber)">{{ $pendingLoans->count() }}</div>
    <div class="ca-stat-chip__lbl">{{ __('app.stat_pending') }}</div>
  </div>
</div>

{{-- ── Quick Actions ─────────────────────────────────────────── --}}
<div class="ca-quick-actions">
  <div class="ca-quick-actions__title">{{ __('app.quick_actions') }}</div>
  <div class="ca-qa-grid">
    <a href="{{ route('client.app.transfer.send') }}" class="ca-qa-item">
      <div class="ca-qa-icon ca-qa-icon--teal"><i class="fas fa-paper-plane"></i></div>
      <span class="ca-qa-label">{{ __('app.action_send') }}</span>
    </a>
    <a href="{{ route('client.app.transfer.receive') }}" class="ca-qa-item">
      <div class="ca-qa-icon ca-qa-icon--gold"><i class="fas fa-arrow-down"></i></div>
      <span class="ca-qa-label">{{ __('app.action_receive') }}</span>
    </a>
    <a href="{{ route('client.app.loans') }}" class="ca-qa-item">
      <div class="ca-qa-icon ca-qa-icon--blue"><i class="fas fa-file-contract"></i></div>
      <span class="ca-qa-label">{{ __('app.action_loans') }}</span>
    </a>
    <a href="{{ route('client.app.analytics') }}" class="ca-qa-item">
      <div class="ca-qa-icon ca-qa-icon--purple"><i class="fas fa-chart-pie"></i></div>
      <span class="ca-qa-label">{{ __('app.action_analytics') }}</span>
    </a>
  </div>
</div>

{{-- ── Transactions recentes ─────────────────────────────────── --}}
<div class="ca-section">
  <span class="ca-section__title">{{ __('app.recent_transactions') }}</span>
  <a href="{{ route('client.app.loans') }}" class="ca-section__link">{{ __('app.see_all') }}</a>
</div>

<div class="ca-txn-list">
  @foreach($activeLoans->take(3) as $loan)
  @php $badgeClass = match($loan->status){ 'finalized'=>'ca-badge--final','contract_signed'=>'ca-badge--signed',default=>'ca-badge--sent' }; @endphp
  <a href="{{ route('client.app.loans.show', $loan) }}" class="ca-txn-item" style="text-decoration:none">
    <div class="ca-txn-icon" style="background:rgba(27,138,122,.15)">
      <i class="fas fa-file-contract" style="color:var(--ca-teal-l)"></i>
    </div>
    <div class="ca-txn-info">
      <div class="ca-txn-title">{{ $loan->reference }}</div>
      <div class="ca-txn-sub"><span class="ca-badge {{ $badgeClass }}">{{ $loan->statusLabel() }}</span></div>
    </div>
    <div>
      <div class="ca-txn-amount positive">+{{ number_format($loan->amount,0,',',' ') }}</div>
      <div class="ca-txn-date">{{ $loan->currency }}</div>
    </div>
  </a>
  @endforeach

  @foreach($recentTransfers->take(2) as $trf)
  <div class="ca-txn-item">
    <div class="ca-txn-icon" style="background:rgba(255,90,90,.1)">
      <i class="fas fa-paper-plane" style="color:var(--ca-negative)"></i>
    </div>
    <div class="ca-txn-info">
      <div class="ca-txn-title">{{ $trf->beneficiary_name }}</div>
      <div class="ca-txn-sub">{{ $trf->reference }}</div>
    </div>
    <div>
      <div class="ca-txn-amount negative">-{{ number_format($trf->amount,0,',',' ') }}</div>
      <div class="ca-txn-date">{{ $trf->processed_at?->format('d/m H:i') }}</div>
    </div>
  </div>
  @endforeach

  @if($activeLoans->isEmpty() && $recentTransfers->isEmpty())
  <div class="ca-empty" style="padding:1.5rem 0">
    <div class="ca-empty__icon" style="width:56px;height:56px;font-size:1.25rem"><i class="fas fa-receipt"></i></div>
    <div class="ca-empty__title" style="font-size:.9rem">{{ __('app.no_activity') }}</div>
  </div>
  @endif
</div>

@if($pendingLoans->isNotEmpty())
<div class="ca-section">
  <span class="ca-section__title">{{ __('app.pending_loans') }}</span>
</div>
<div class="ca-txn-list">
  @foreach($pendingLoans->take(2) as $loan)
  <a href="{{ route('client.app.loans.show', $loan) }}" class="ca-txn-item" style="text-decoration:none">
    <div class="ca-txn-icon" style="background:rgba(245,158,11,.1)">
      <i class="fas fa-hourglass-half" style="color:var(--ca-amber)"></i>
    </div>
    <div class="ca-txn-info">
      <div class="ca-txn-title">{{ $loan->reference }}</div>
      <div class="ca-txn-sub"><span class="ca-badge ca-badge--pending">{{ $loan->statusLabel() }}</span></div>
    </div>
    <div>
      <div class="ca-txn-amount neutral">{{ number_format($loan->amount,0,',',' ') }}</div>
      <div class="ca-txn-date">{{ $loan->currency }}</div>
    </div>
  </a>
  @endforeach
</div>
@endif

<div style="height:1rem"></div>
@endsection
