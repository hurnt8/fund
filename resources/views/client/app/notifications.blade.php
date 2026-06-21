@extends('layouts.client-app')
@section('title', __('app.notifications_title') . ' — Credixa')
@section('page_title', __('app.notifications_title'))
@section('back_btn', true)
@section('back_url', route('client.app.home'))

@section('topbar_action')
@if($notifications->isNotEmpty())
<form method="POST" action="{{ route('client.app.notifications.read-all') }}">
  @csrf
  <button type="submit" style="background:none;border:none;font-size:.78rem;font-weight:600;color:var(--ca-teal-l);cursor:pointer;padding:.5rem .25rem;font-family:inherit">
    {{ __('app.mark_all_read') }}
  </button>
</form>
@else
<div style="width:70px"></div>
@endif
@endsection

@push('styles')
<style>
.nx-list { padding:.5rem 1.25rem 1.5rem; display:flex; flex-direction:column; gap:.5rem }

.nx-item {
  display:flex; align-items:flex-start; gap:.875rem;
  background:var(--ca-bg2);
  border:1px solid var(--ca-border);
  border-radius:16px;
  padding:.9rem 1rem;
  position:relative;
  transition:background var(--ca-transition);
}
.nx-item.nx-unread {
  background:var(--ca-bg3);
  border-color:rgba(27,138,122,.22);
}
.nx-unread-dot {
  position:absolute;top:.875rem;right:.9rem;
  width:8px;height:8px;border-radius:50%;
  background:var(--ca-teal-l);
  box-shadow:0 0 6px rgba(27,138,122,.5);
}

.nx-ico {
  width:44px;height:44px;border-radius:50%;flex-shrink:0;
  display:flex;align-items:center;justify-content:center;
  font-size:.95rem;
}
.nx-ico--transfer  { background:rgba(27,138,122,.15); color:var(--ca-teal-l) }
.nx-ico--loan      { background:rgba(200,169,81,.15);  color:var(--ca-gold-l) }
.nx-ico--system    { background:rgba(74,158,255,.15);  color:var(--ca-blue) }

.nx-body { flex:1;min-width:0 }
.nx-title {
  font-size:.875rem;font-weight:700;color:var(--ca-text);
  margin-bottom:.2rem;
}
.nx-text {
  font-size:.78rem;color:var(--ca-text-2);line-height:1.5;
  margin-bottom:.3rem;
}
.nx-time { font-size:.68rem;color:var(--ca-text-3) }

.nx-empty {
  display:flex;flex-direction:column;align-items:center;
  padding:4rem 1rem 2rem;text-align:center;
}
.nx-empty__ico {
  width:72px;height:72px;border-radius:50%;
  background:var(--ca-bg3);border:1px solid var(--ca-border);
  display:flex;align-items:center;justify-content:center;
  font-size:1.75rem;color:var(--ca-text-3);margin-bottom:1rem;
}
.nx-empty__title { font-size:.95rem;font-weight:700;color:var(--ca-text-2);margin-bottom:.4rem }
.nx-empty__sub   { font-size:.8rem;color:var(--ca-text-3) }

.nx-date-sep {
  font-size:.7rem;font-weight:700;text-transform:uppercase;
  letter-spacing:.09em;color:var(--ca-text-3);
  padding:.75rem 0 .25rem;
}
</style>
@endpush

@section('content')

@if($notifications->isEmpty())
<div class="nx-empty">
  <div class="nx-empty__ico"><i class="fas fa-bell-slash"></i></div>
  <div class="nx-empty__title">{{ __('app.notifications_empty') }}</div>
  <div class="nx-empty__sub">{{ __('app.notifications_empty_sub') }}</div>
</div>
@else

<div class="nx-list">
@php
  $prevDate = null;
  $iconMap  = [
    'transfer'    => ['ico' => 'nx-ico--transfer', 'fa' => 'fas fa-paper-plane'],
    'loan_update' => ['ico' => 'nx-ico--loan',     'fa' => 'fas fa-file-contract'],
    'system'      => ['ico' => 'nx-ico--system',   'fa' => 'fas fa-bell'],
  ];
@endphp

@foreach($notifications as $n)
@php
  $date    = $n->created_at->format('d/m/Y');
  $unread  = is_null($n->read_at);
  $map     = $iconMap[$n->type] ?? $iconMap['system'];
  $diff    = $n->created_at->diffInMinutes(now());
  if ($diff < 1)       $timeStr = __('app.notif_just_now');
  elseif ($diff < 60)  $timeStr = str_replace(':n', (int)$diff, __('app.notif_minutes_ago'));
  elseif ($diff < 1440) $timeStr = str_replace(':n', (int)($diff/60), __('app.notif_hours_ago'));
  else                 $timeStr = str_replace(':n', (int)($diff/1440), __('app.notif_days_ago'));
@endphp

@if($date !== $prevDate)
  @if(!$loop->first)<div style="height:.25rem"></div>@endif
  <div class="nx-date-sep">
    @if($n->created_at->isToday()) Aujourd'hui @else {{ $n->created_at->format('d/m/Y') }} @endif
  </div>
  @php $prevDate = $date; @endphp
@endif

<div class="nx-item {{ $unread ? 'nx-unread' : '' }}">
  @if($unread)<div class="nx-unread-dot"></div>@endif
  <div class="nx-ico {{ $map['ico'] }}">
    <i class="{{ $map['fa'] }}"></i>
  </div>
  <div class="nx-body">
    <div class="nx-title">{{ $n->title }}</div>
    <div class="nx-text">{{ $n->body }}</div>
    <div class="nx-time"><i class="fas fa-clock" style="font-size:.6rem;margin-right:.3rem"></i>{{ $timeStr }}</div>
  </div>
</div>

@endforeach
</div>

@endif

@endsection
