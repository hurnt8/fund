@extends('layouts.dashboard')
@section('title', 'Support clients — Credixa')
@section('page_title', 'Support clients')

@push('styles')
<style>
.conv-item {
  display:flex; align-items:center; gap:1rem;
  padding:1rem 1.25rem; text-decoration:none;
  border-bottom:1px solid var(--c-border);
  transition:.15s; position:relative;
}
.conv-item:last-child { border-bottom:none; }
.conv-item:hover { background:rgba(200,169,81,.04); }
.conv-item--unread { background:rgba(200,169,81,.035); }
.conv-item--unread .conv-name { font-weight:800; }
.conv-item--unread .conv-preview { color:var(--c-text); font-weight:500; }

.conv-avatar {
  width:46px; height:46px; border-radius:50%; flex-shrink:0;
  background:linear-gradient(135deg,var(--c-navy),var(--c-navy-3));
  display:flex; align-items:center; justify-content:center;
  font-size:1.0625rem; font-weight:800; color:var(--c-gold);
  position:relative;
}
.conv-avatar__dot {
  position:absolute; bottom:1px; right:1px;
  width:11px; height:11px; border-radius:50%;
  background:var(--c-gold); border:2px solid var(--c-surface);
}
.conv-body { flex:1; min-width:0; }
.conv-name { font-size:.875rem; font-weight:600; color:var(--c-navy); margin-bottom:.2rem; }
.conv-preview {
  font-size:.78rem; color:var(--c-muted);
  white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:480px;
}
.conv-meta { display:flex; flex-direction:column; align-items:flex-end; flex-shrink:0; gap:.35rem; }
.conv-time { font-size:.68rem; color:var(--c-muted); white-space:nowrap; }
.conv-badge {
  display:inline-flex; align-items:center; justify-content:center;
  min-width:20px; height:20px; padding:0 6px;
  background:var(--c-gold); color:var(--c-navy);
  font-size:.65rem; font-weight:900; border-radius:999px;
}
</style>
@endpush

@section('content')
@php
  $totalConvs  = $clients->count();
  $unreadConvs = $clients->filter(fn($c) => $c->unread_for_admin > 0)->count();
  $totalUnread = $clients->sum('unread_for_admin');
@endphp

{{-- Page header ── --}}
<div class="page-hdr-row">
  <div class="page-hdr">
    <div style="display:flex;align-items:center;gap:.75rem;flex-wrap:wrap">
      <h1>Support clients</h1>
      @if($totalUnread > 0)
        <span class="badge-status bs-amber">
          <i class="fas fa-bell" style="font-size:.6rem"></i>
          {{ $totalUnread }} non lu{{ $totalUnread > 1 ? 's':'' }}
        </span>
      @endif
    </div>
    <p>Messagerie directe avec vos clients</p>
  </div>
</div>

{{-- KPI ── --}}
<div class="metrics-grid-3">
  <div class="metric-card">
    <div class="metric-card__icon mi-navy"><i class="fas fa-comments"></i></div>
    <div class="metric-card__val">{{ $totalConvs }}</div>
    <div class="metric-card__lbl">Conversations</div>
    <div class="metric-card__accent" style="background:var(--c-navy)"></div>
  </div>
  <div class="metric-card">
    <div class="metric-card__icon mi-gold"><i class="fas fa-envelope-open-text"></i></div>
    <div class="metric-card__val" style="color:{{ $unreadConvs ? 'var(--c-gold)' : 'var(--c-muted)' }}">{{ $unreadConvs }}</div>
    <div class="metric-card__lbl">Conv. non lues</div>
    <div class="metric-card__accent" style="background:var(--c-gold)"></div>
  </div>
  <div class="metric-card">
    <div class="metric-card__icon mi-green"><i class="fas fa-check-double"></i></div>
    <div class="metric-card__val" style="color:var(--c-green)">{{ $totalConvs - $unreadConvs }}</div>
    <div class="metric-card__lbl">À jour</div>
    <div class="metric-card__accent" style="background:var(--c-green)"></div>
  </div>
</div>

{{-- Search + filter ── --}}
<div class="filter-bar" style="margin-bottom:1.25rem">
  <div style="position:relative;flex:1;min-width:200px">
    <i class="fas fa-search" style="position:absolute;left:.75rem;top:50%;transform:translateY(-50%);color:var(--c-muted);font-size:.75rem;pointer-events:none"></i>
    <input id="convSearch" type="text" placeholder="Rechercher un client…" class="form-control-pro" style="padding-left:2.25rem">
  </div>
  <button id="filterAll"    class="btn-navy btn-sm-pro" onclick="setFilter('all')">Tous</button>
  <button id="filterUnread" class="btn-ghost btn-sm-pro" onclick="setFilter('unread')">
    <i class="fas fa-circle" style="font-size:.45rem;color:var(--c-gold)"></i> Non lus
  </button>
</div>

{{-- Conversation list ── --}}
@if($clients->isEmpty())
<div class="card-pro" style="text-align:center;padding:5rem 2rem">
  <i class="fas fa-comments" style="font-size:2.75rem;color:var(--c-muted);opacity:.2;display:block;margin-bottom:1rem"></i>
  <p style="font-size:.9375rem;font-weight:700;color:var(--c-navy)">Aucune conversation</p>
  <p style="font-size:.8125rem;color:var(--c-muted);margin-top:.35rem">Vos clients n'ont pas encore envoyé de message.</p>
</div>
@else
<div class="card-pro" id="convList" style="padding:0;overflow:hidden">
  @foreach($clients as $client)
  @php
    $last    = $client->last_message;
    $unread  = $client->unread_for_admin;
    $preview = $last ? \Str::limit($last->body ?: '📎 Image', 65) : 'Aucun message';
    $time    = $last ? $last->created_at->diffForHumans() : '';
  @endphp
  <a href="{{ route('admin.support.show', $client) }}"
     class="conv-item {{ $unread ? 'conv-item--unread' : '' }}"
     data-name="{{ strtolower($client->name) }}"
     data-unread="{{ $unread ? '1' : '0' }}">

    <div class="conv-avatar">
      {{ strtoupper(substr($client->name, 0, 1)) }}
      @if($unread)
      <div class="conv-avatar__dot"></div>
      @endif
    </div>

    <div class="conv-body">
      <div class="conv-name">{{ $client->name }}</div>
      <div class="conv-preview">
        @if($last && $last->sender_type === 'admin')
          <span style="color:var(--c-gold-d);font-weight:600">Vous :</span>
        @endif
        {{ $preview }}
      </div>
    </div>

    <div class="conv-meta">
      <span class="conv-time">{{ $time }}</span>
      @if($unread)
      <span class="conv-badge">{{ $unread }}</span>
      @endif
    </div>
  </a>
  @endforeach
</div>
@endif

<script>
let _activeFilter = 'all';

document.getElementById('convSearch').addEventListener('input', function () {
  applyFilters(this.value.toLowerCase());
});

function setFilter(f) {
  _activeFilter = f;
  document.getElementById('filterAll').className    = f === 'all'    ? 'btn-navy btn-sm-pro'  : 'btn-ghost btn-sm-pro';
  document.getElementById('filterUnread').className = f === 'unread' ? 'btn-navy btn-sm-pro'  : 'btn-ghost btn-sm-pro';
  applyFilters(document.getElementById('convSearch').value.toLowerCase());
}

function applyFilters(q) {
  document.querySelectorAll('.conv-item').forEach(el => {
    const nameMatch   = el.dataset.name.includes(q);
    const unreadMatch = _activeFilter === 'unread' ? el.dataset.unread === '1' : true;
    el.style.display  = (nameMatch && unreadMatch) ? '' : 'none';
  });
}
</script>

@endsection
