@extends('layouts.client-app')
@section('title', __('app.title'))

{{-- ── Custom topbar ─────────────────────────────────────────────── --}}
@section('topbar')
<header class="ca-topbar ca-topbar--home h-topbar">
  {{-- Left: avatar + greeting --}}
  <div class="h-header__left">
    <div class="h-avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
    <div>
      <div class="h-greeting">{{ __('app.welcome_back') }}</div>
      <div class="h-name">{{ Str::words($user->name, 2, '') }}</div>
    </div>
  </div>

  {{-- Right: lang + bell --}}
  <div class="h-header__actions">
    <div x-data="langMenu()" style="position:relative">
      <button class="h-topbtn" @click="toggle()" title="{{ __('app.language') }}">
        <i class="fas fa-globe"></i>
      </button>
      <div x-show="open" @click.outside="close()" x-transition
           style="position:absolute;right:0;top:48px;background:var(--ca-bg4);border:1px solid var(--ca-border);border-radius:14px;min-width:144px;overflow:hidden;z-index:500;box-shadow:0 12px 40px rgba(0,0,0,.45)">
        @foreach(['fr'=>'Français','en'=>'English','pl'=>'Polski','es'=>'Español'] as $lc => $label)
        <form method="POST" action="{{ route('client.app.locale') }}">
          @csrf<input type="hidden" name="locale" value="{{ $lc }}">
          <button type="submit" style="width:100%;padding:.6rem 1rem;background:none;border:none;color:{{ app()->getLocale()===$lc?'var(--ca-teal-l)':'var(--ca-text-2)' }};font-size:.82rem;text-align:left;cursor:pointer;font-family:inherit;font-weight:{{ app()->getLocale()===$lc?'700':'400' }}">
            {{ $label }}
          </button>
        </form>
        @endforeach
      </div>
    </div>
    <a href="{{ route('client.app.notifications') }}" class="h-topbtn" style="position:relative;text-decoration:none" aria-label="{{ __('app.notifications_title') }}">
      <i class="fas fa-bell"></i>
      <span class="h-notif-dot" id="notif-dot" style="{{ ($unreadCount ?? 0) > 0 ? '' : 'display:none' }}"></span>
    </a>
  </div>
</header>
@endsection

@push('styles')
<style>
/* ── Topbar home ── */
.h-topbar{
  padding: .875rem 1.25rem !important;
  padding-top: calc(.875rem + env(safe-area-inset-top,0px)) !important;
  display: flex !important;
  align-items: center !important;
  justify-content: space-between !important;
  min-height: unset !important;
}
.h-header__left  { display:flex;align-items:center;gap:.75rem }
.h-header__actions{ display:flex;gap:.5rem }
.h-avatar{
  width:46px;height:46px;border-radius:50%;flex-shrink:0;
  background:linear-gradient(135deg,var(--ca-teal),#0A2040);
  border:2.5px solid rgba(27,138,122,.45);
  box-shadow:0 0 0 4px rgba(27,138,122,.1);
  display:flex;align-items:center;justify-content:center;
  font-weight:800;font-size:1.1rem;color:#fff;
}
.h-greeting{ font-size:.7rem;color:var(--ca-text-3);margin-bottom:.05rem }
.h-name{ font-size:.98rem;font-weight:700;color:var(--ca-text) }
.h-topbtn{
  width:40px;height:40px;border-radius:50%;
  background:var(--ca-bg3);border:1px solid var(--ca-border);
  display:flex;align-items:center;justify-content:center;
  color:var(--ca-text-2);font-size:.88rem;cursor:pointer;
  transition:var(--ca-transition);
}
.h-topbtn:hover{ background:var(--ca-bg4);color:var(--ca-text) }
.h-notif-dot{
  position:absolute;top:8px;right:8px;
  width:8px;height:8px;border-radius:50%;
  background:var(--ca-negative);
  border:2px solid var(--ca-bg);
}

/* ── Balance card ── */
.h-card{
  margin:.625rem 1.25rem 0;
  border-radius:24px;
  background:linear-gradient(145deg,#1B527A 0%,#0D2E54 40%,#071828 100%);
  padding:1.375rem 1.5rem 1.25rem;
  position:relative;overflow:hidden;
  box-shadow:0 20px 56px rgba(0,0,0,.55),0 0 0 1px rgba(255,255,255,.07);
}
.h-card::before{
  content:'';position:absolute;top:-80px;right:-80px;
  width:260px;height:260px;border-radius:50%;
  background:radial-gradient(circle,rgba(13,207,220,.1) 0%,transparent 65%);
  pointer-events:none;
}
.h-card::after{
  content:'';position:absolute;bottom:-80px;left:-50px;
  width:220px;height:220px;border-radius:50%;
  background:radial-gradient(circle,rgba(200,169,81,.09) 0%,transparent 65%);
  pointer-events:none;
}
/* Card top row */
.h-card__top{
  display:flex;align-items:center;justify-content:space-between;
  margin-bottom:1.125rem;
}
.h-card__brand{
  font-family:'Space Grotesk',sans-serif;
  font-size:.65rem;font-weight:800;
  letter-spacing:.14em;text-transform:uppercase;
  color:rgba(255,255,255,.5);
}
.h-card__chip{
  width:34px;height:26px;border-radius:5px;
  background:linear-gradient(135deg,#D4B96A,#C8A951,#A88830);
  box-shadow:0 2px 8px rgba(0,0,0,.35);
  position:relative;overflow:hidden;
}
.h-card__chip::before{
  content:'';position:absolute;top:50%;left:0;right:0;
  height:1px;background:rgba(0,0,0,.2);transform:translateY(-50%);
}
.h-card__chip::after{
  content:'';position:absolute;left:50%;top:0;bottom:0;
  width:1px;background:rgba(0,0,0,.18);transform:translateX(-50%);
}
/* Balance */
.h-balance-row{
  display:flex;align-items:center;gap:.625rem;
  margin-bottom:.3rem;
}
.h-balance-lbl{
  font-size:.63rem;font-weight:600;text-transform:uppercase;
  letter-spacing:.1em;color:rgba(255,255,255,.42);
}
.h-eye{
  background:none;border:none;padding:0;
  color:rgba(255,255,255,.38);cursor:pointer;font-size:.78rem;
  display:inline-flex;align-items:center;transition:color .18s;
}
.h-eye:hover{color:rgba(255,255,255,.75)}
.h-balance{
  font-family:'Space Grotesk',sans-serif;
  font-size:2.125rem;font-weight:800;
  color:#fff;letter-spacing:-.03em;line-height:1;
  margin-bottom:1.125rem;
}
.h-balance sup{
  font-size:.95rem;font-weight:600;
  vertical-align:super;margin-right:.2rem;
  color:var(--ca-gold-l);
}
.h-balance--hidden{
  font-size:1.5rem;letter-spacing:.35em;
  color:rgba(255,255,255,.28);margin-bottom:1.125rem;
}
/* Card bottom */
.h-card__bottom{
  display:flex;align-items:flex-end;justify-content:space-between;
  position:relative;z-index:1;
}
.h-card__name{
  font-family:'Space Grotesk',sans-serif;
  font-size:.78rem;font-weight:700;
  color:rgba(255,255,255,.75);
  text-transform:uppercase;letter-spacing:.06em;
  margin-bottom:.18rem;
}
.h-card__num{
  font-family:monospace;font-size:.7rem;
  color:rgba(255,255,255,.38);letter-spacing:.15em;
}
.h-card__badge{
  background:rgba(200,169,81,.18);
  border:1px solid rgba(200,169,81,.38);
  border-radius:999px;
  padding:.28rem .75rem;
  font-size:.65rem;font-weight:700;
  color:var(--ca-gold-l);letter-spacing:.05em;
  display:flex;align-items:center;gap:.3rem;
}
/* Circles decoration (Visa-like) */
.h-card__circles{
  position:absolute;bottom:1.125rem;right:4.5rem;
  display:flex;pointer-events:none;
}
.h-card__circ{
  width:34px;height:34px;border-radius:50%;opacity:.35;
}
.h-card__circ:first-child{ background:var(--ca-gold);margin-right:-14px }
.h-card__circ:last-child { background:var(--ca-gold-l) }

/* ── Quick actions ── */
.h-actions{
  display:grid;grid-template-columns:repeat(4,1fr);
  gap:.5rem;
  padding:1.25rem 1.25rem .25rem;
}
.h-action{
  display:flex;flex-direction:column;align-items:center;
  gap:.5rem;text-decoration:none;cursor:pointer;
}
.h-action__ico{
  width:56px;height:56px;border-radius:18px;
  display:flex;align-items:center;justify-content:center;
  font-size:1.1rem;
  transition:transform .14s,box-shadow .14s;
}
.h-action:active .h-action__ico{ transform:scale(.91) }
.h-action__ico--teal  { background:rgba(27,138,122,.2);  border:1px solid rgba(27,138,122,.35);  color:var(--ca-teal-l);    box-shadow:0 4px 14px rgba(27,138,122,.18) }
.h-action__ico--green { background:rgba(0,200,150,.15);  border:1px solid rgba(0,200,150,.3);    color:var(--ca-positive);  box-shadow:0 4px 14px rgba(0,200,150,.16) }
.h-action__ico--blue  { background:rgba(74,158,255,.15); border:1px solid rgba(74,158,255,.3);   color:var(--ca-blue);      box-shadow:0 4px 14px rgba(74,158,255,.14) }
.h-action__ico--purple{ background:rgba(139,92,246,.15); border:1px solid rgba(139,92,246,.3);   color:var(--ca-purple);    box-shadow:0 4px 14px rgba(139,92,246,.14) }
.h-action__lbl{
  font-size:.68rem;font-weight:600;
  color:var(--ca-text-2);text-align:center;line-height:1.2;
}

/* ── Stats mini-cards ── */
.h-stats{
  display:grid;grid-template-columns:repeat(3,1fr);
  gap:.625rem;
  padding:.875rem 1.25rem 0;
}
.h-stat{
  background:var(--ca-bg2);
  border:1px solid var(--ca-border);
  border-radius:16px;
  padding:.875rem .75rem .75rem;
  text-align:center;position:relative;overflow:hidden;
}
.h-stat::before{
  content:'';position:absolute;top:0;left:0;right:0;
  height:3px;border-radius:16px 16px 0 0;
}
.h-stat--def::before { background:linear-gradient(90deg,var(--ca-text-3),var(--ca-bg4)) }
.h-stat--teal::before{ background:linear-gradient(90deg,var(--ca-teal-l),#0D8A7A) }
.h-stat--amb::before { background:linear-gradient(90deg,var(--ca-amber),#C87800) }
.h-stat__num{
  font-family:'Space Grotesk',sans-serif;
  font-size:1.75rem;font-weight:800;
  line-height:1;margin-bottom:.3rem;
}
.h-stat--def  .h-stat__num{ color:var(--ca-text) }
.h-stat--teal .h-stat__num{ color:var(--ca-teal-l) }
.h-stat--amb  .h-stat__num{ color:var(--ca-amber) }
.h-stat__lbl{
  font-size:.63rem;font-weight:600;
  text-transform:uppercase;letter-spacing:.07em;
  color:var(--ca-text-3);
}

/* ── Section header ── */
.h-section{
  display:flex;align-items:center;justify-content:space-between;
  padding:1.25rem 1.25rem .625rem;
}
.h-section__title{ font-size:.85rem;font-weight:700;color:var(--ca-text) }
.h-section__link{
  font-size:.75rem;font-weight:600;color:var(--ca-teal-l);
  display:inline-flex;align-items:center;gap:.3rem;
  transition:opacity .18s;
}
.h-section__link:hover{ opacity:.75 }

/* ── Transaction cards ── */
.h-txn-list{ padding:0 1.25rem;display:flex;flex-direction:column;gap:.5rem }
.h-txn{
  display:flex;align-items:center;gap:.875rem;
  background:var(--ca-bg2);
  border:1px solid var(--ca-border);
  border-radius:16px;
  padding:.875rem 1rem;
  text-decoration:none;
  transition:background var(--ca-transition),transform .1s;
}
.h-txn:active{ transform:scale(.99) }
.h-txn:hover { background:var(--ca-bg3) }
.h-txn__ico{
  width:44px;height:44px;border-radius:50%;
  display:flex;align-items:center;justify-content:center;
  font-size:.95rem;flex-shrink:0;
}
.h-txn__info{ flex:1;min-width:0 }
.h-txn__title{
  font-size:.875rem;font-weight:600;color:var(--ca-text);
  overflow:hidden;text-overflow:ellipsis;white-space:nowrap;
}
.h-txn__sub{
  font-size:.72rem;color:var(--ca-text-3);
  margin-top:.18rem;display:flex;align-items:center;gap:.35rem;
}
.h-txn__right{ text-align:right;flex-shrink:0 }
.h-txn__amount{
  font-family:'Space Grotesk',sans-serif;
  font-size:.95rem;font-weight:700;
}
.h-txn__amount--pos{ color:var(--ca-positive) }
.h-txn__amount--neg{ color:var(--ca-negative) }
.h-txn__amount--neu{ color:var(--ca-text) }
.h-txn__date{ font-size:.65rem;color:var(--ca-text-3);margin-top:.18rem }

/* ── Badge inside transactions ── */
.h-badge{
  display:inline-flex;align-items:center;
  padding:.15rem .55rem;border-radius:999px;
  font-size:.62rem;font-weight:700;
  text-transform:uppercase;letter-spacing:.04em;
}
.h-badge--loan   { background:rgba(27,138,122,.18); color:var(--ca-teal-l) }
.h-badge--pending{ background:rgba(245,158,11,.15); color:var(--ca-amber) }
.h-badge--signed { background:rgba(74,158,255,.15); color:var(--ca-blue) }
.h-badge--final  { background:rgba(200,169,81,.15); color:var(--ca-gold-l) }

/* ── Empty state ── */
.h-empty{
  display:flex;flex-direction:column;align-items:center;
  padding:2rem 1rem;text-align:center;
}
.h-empty__ico{
  width:58px;height:58px;border-radius:50%;
  background:var(--ca-bg3);border:1px solid var(--ca-border);
  display:flex;align-items:center;justify-content:center;
  font-size:1.25rem;color:var(--ca-text-3);margin-bottom:.875rem;
}
.h-empty__title{ font-size:.875rem;font-weight:600;color:var(--ca-text-2);margin-bottom:.35rem }
.h-empty__sub  { font-size:.75rem;color:var(--ca-text-3) }

/* ── Pending alert banner ── */
.h-alert{
  margin:.25rem 1.25rem 0;
  background:rgba(245,158,11,.08);
  border:1px solid rgba(245,158,11,.22);
  border-left:3px solid var(--ca-amber);
  border-radius:14px;
  padding:.75rem 1rem;
  display:flex;align-items:center;gap:.625rem;
}
.h-alert i{ color:var(--ca-amber);font-size:.9rem;flex-shrink:0 }
.h-alert__text{ font-size:.78rem;color:rgba(255,255,255,.75);line-height:1.5 }
.h-alert__text strong{ color:var(--ca-amber);font-weight:700 }

/* ── Stagger entrance animations ── */
@keyframes hIn{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:translateY(0)}}
.h-card     { animation:hIn .4s ease .05s both }
.h-actions  { animation:hIn .4s ease .12s both }
.h-stats    { animation:hIn .4s ease .18s both }
.h-section  { animation:hIn .4s ease .22s both }
.h-txn-list { animation:hIn .4s ease .26s both }
</style>
@endpush

@section('content')
<div style="padding-bottom:1.5rem">

{{-- ── Balance card ─────────────────────────────────────────────── --}}
<div class="h-card" x-data="{ shown: true }">

  {{-- Circles decoration --}}
  <div class="h-card__circles" aria-hidden="true">
    <div class="h-card__circ"></div>
    <div class="h-card__circ"></div>
  </div>

  {{-- Top row: brand + chip --}}
  <div class="h-card__top">
    <div class="h-card__brand">
      <i class="fas fa-landmark" style="font-size:.6rem;margin-right:.3rem"></i>
      CREDIXA &nbsp;·&nbsp; {{ __('app.account_num') }}
    </div>
    <div class="h-card__chip" aria-hidden="true"></div>
  </div>

  {{-- Balance label + toggle --}}
  <div class="h-balance-row">
    <span class="h-balance-lbl">{{ __('app.balance') }}</span>
    <button class="h-eye" @click="shown = !shown" :aria-label="shown ? 'Masquer' : 'Afficher'">
      <i :class="shown ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
    </button>
  </div>

  {{-- Amount --}}
  <div class="h-balance" x-show="shown" x-transition>
    <sup>{{ $user->currency ?? config('credixa.default_currency') }}</sup>{{ number_format((float)$user->balance, 2, ',', ' ') }}
  </div>
  <div class="h-balance--hidden" x-show="!shown" aria-hidden="true">
    &bull;&bull;&bull;&bull;&bull;&bull;
  </div>

  {{-- Bottom row: name + card number | currency badge --}}
  <div class="h-card__bottom">
    <div>
      <div class="h-card__name">{{ Str::upper(Str::words($user->name, 2, '')) }}</div>
      <div class="h-card__num">&bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull; {{ str_pad(substr($user->id, -4), 4, '0', STR_PAD_LEFT) }}</div>
    </div>
    <div class="h-card__badge">
      <i class="fas fa-shield-halved" style="font-size:.6rem"></i>
      {{ $user->currency ?? config('credixa.default_currency') }}
    </div>
  </div>
</div>

{{-- ── Quick Actions ─────────────────────────────────────────────── --}}
<div class="h-actions">
  <a href="{{ route('client.app.transfer.send') }}" class="h-action">
    <div class="h-action__ico h-action__ico--teal">
      <i class="fas fa-paper-plane"></i>
    </div>
    <span class="h-action__lbl">{{ __('app.action_send') }}</span>
  </a>
 <a href="{{ route('client.app.transfer.receive') }}" class="h-action">
    <div class="h-action__ico h-action__ico--green">
      <i class="fas fa-download"></i>
    </div>
    <span class="h-action__lbl">{{ __('app.action_receive') }}</span>
  </a>
  <a href="{{ route('client.app.loans') }}" class="h-action">
    <div class="h-action__ico h-action__ico--blue">
      <i class="fas fa-folder-open"></i>
    </div>
    <span class="h-action__lbl">{{ __('app.action_loans') }}</span>
  </a>
  <a href="{{ route('client.app.analytics') }}" class="h-action">
    <div class="h-action__ico h-action__ico--purple">
      <i class="fas fa-chart-pie"></i>
    </div>
    <span class="h-action__lbl">{{ __('app.action_analytics') }}</span>
  </a>
  <a href="{{ route('client.app.movements') }}" class="h-action">
    <div class="h-action__ico" style="background:rgba(200,169,81,.15);border:1px solid rgba(200,169,81,.3);color:var(--ca-gold-l)">
      <i class="fas fa-list-ul"></i>
    </div>
    <span class="h-action__lbl">Mouvements</span>
  </a>
</div>

{{-- ── Pending alert ─────────────────────────────────────────────── --}}
@if($pendingLoans->isNotEmpty())
<div class="h-alert">
  <i class="fas fa-hourglass-half"></i>
  <div class="h-alert__text">
    <strong>{{ $pendingLoans->count() }} {{ __('app.stat_pending') }}</strong>
    — {{ __('app.pending_loans') }}
  </div>
</div>
@endif

{{-- ── Recent activity ───────────────────────────────────────────── --}}
<div class="h-section">
  <span class="h-section__title">{{ __('app.recent_transactions') }}</span>
  <a href="{{ route('client.app.movements') }}" class="h-section__link">
    {{ __('app.see_all') }} <i class="fas fa-chevron-right" style="font-size:.6rem"></i>
  </a>
</div>

<div class="h-txn-list">

  @foreach($activeLoans->take(3) as $loan)
  @php
    $badgeCls = match($loan->status){
      'finalized'       => 'h-badge--final',
      'contract_signed' => 'h-badge--signed',
      default           => 'h-badge--loan'
    };
  @endphp
  <a href="{{ route('client.app.loans.show', $loan) }}" class="h-txn">
    <div class="h-txn__ico" style="background:rgba(27,138,122,.15)">
      <i class="fas fa-file-contract" style="color:var(--ca-teal-l)"></i>
    </div>
    <div class="h-txn__info">
      <div class="h-txn__title">{{ $loan->reference }}</div>
      <div class="h-txn__sub">
        <span class="h-badge {{ $badgeCls }}">{{ $loan->statusLabel() }}</span>
      </div>
    </div>
    <div class="h-txn__right">
      <div class="h-txn__amount h-txn__amount--pos">+{{ number_format($loan->amount, 0, ',', ' ') }}</div>
      <div class="h-txn__date">{{ $loan->currency }}</div>
    </div>
  </a>
  @endforeach

  @foreach($recentTransfers->take(2) as $trf)
  @php
    $isPending = in_array($trf->status, [\App\Models\Transfer::STATUS_PENDING, \App\Models\Transfer::STATUS_FEE_REQUIRED]);
  @endphp
  <a href="{{ route('client.app.movements') }}" class="h-txn">
    <div class="h-txn__ico" style="background:rgba(255,90,90,.1)">
      <i class="fas fa-paper-plane" style="color:var(--ca-negative)"></i>
    </div>
    <div class="h-txn__info">
      <div class="h-txn__title">{{ $trf->beneficiary_name }}</div>
      <div class="h-txn__sub">
        {{ $trf->reference }}
        @if($isPending)
          &nbsp;<span style="font-size:.6rem;background:rgba(245,158,11,.2);color:#f59e0b;padding:.1rem .4rem;border-radius:999px;font-weight:700">En attente</span>
        @endif
      </div>
    </div>
    <div class="h-txn__right">
      <div class="h-txn__amount h-txn__amount--neg">-{{ number_format($trf->amount, 0, ',', ' ') }}</div>
      <div class="h-txn__date">{{ $trf->created_at->format('d/m · H:i') }}</div>
    </div>
  </a>
  @endforeach

  @if($activeLoans->isEmpty() && $recentTransfers->isEmpty())
  <div class="h-txn-list">
    <div class="h-empty">
      <div class="h-empty__ico"><i class="fas fa-receipt"></i></div>
      <div class="h-empty__title">{{ __('app.no_activity') }}</div>
      <div class="h-empty__sub">{{ __('app.no_activity_hint', [], app()->getLocale()) ?? 'Vos transactions apparaîtront ici.' }}</div>
    </div>
  </div>
  @endif

</div>

{{-- ── Pending loans ─────────────────────────────────────────────── --}}
@if($pendingLoans->isNotEmpty())
<div class="h-section" style="animation-delay:.3s">
  <span class="h-section__title">{{ __('app.pending_loans') }}</span>
</div>
<div class="h-txn-list">
  @foreach($pendingLoans->take(2) as $loan)
  <a href="{{ route('client.app.loans.show', $loan) }}" class="h-txn">
    <div class="h-txn__ico" style="background:rgba(245,158,11,.1)">
      <i class="fas fa-hourglass-half" style="color:var(--ca-amber)"></i>
    </div>
    <div class="h-txn__info">
      <div class="h-txn__title">{{ $loan->reference }}</div>
      <div class="h-txn__sub">
        <span class="h-badge h-badge--pending">{{ $loan->statusLabel() }}</span>
      </div>
    </div>
    <div class="h-txn__right">
      <div class="h-txn__amount h-txn__amount--neu">{{ number_format($loan->amount, 0, ',', ' ') }}</div>
      <div class="h-txn__date">{{ $loan->currency }}</div>
    </div>
  </a>
  @endforeach
</div>
@endif

</div>
@endsection
