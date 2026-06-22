@extends('layouts.dashboard')
@section('title', 'Espace Admin — Vue d\'ensemble')
@section('page_title', 'Vue d\'ensemble')

@push('styles')
<style>
/* ── Banner ──────────────────────────────────────── */
.sa-banner {
  background: linear-gradient(135deg, #0B1A2E 0%, #1a3a5c 55%, #0f2847 100%);
  border-radius: var(--radius);
  padding: 1.75rem 2rem;
  margin-bottom: 1.75rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1.5rem;
  flex-wrap: wrap;
  position: relative;
  overflow: hidden;
}
.sa-banner::before {
  content: '';
  position: absolute; top: -40px; right: -40px;
  width: 220px; height: 220px; border-radius: 50%;
  background: rgba(200,169,81,.07); pointer-events: none;
}
.sa-banner::after {
  content: '';
  position: absolute; bottom: -60px; right: 80px;
  width: 160px; height: 160px; border-radius: 50%;
  background: rgba(200,169,81,.04); pointer-events: none;
}
.sa-banner__eyebrow {
  font-size: .62rem; font-weight: 700; letter-spacing: .1em;
  text-transform: uppercase; color: var(--c-gold); margin-bottom: .35rem;
}
.sa-banner__title  { font-size: 1.2rem; font-weight: 800; color: #fff; margin: 0 0 .3rem; line-height: 1.2; }
.sa-banner__sub    { font-size: .78rem; color: rgba(255,255,255,.45); }
.sa-banner__kpis   { display: flex; gap: 2rem; flex-shrink: 0; }
.sa-banner__kpi    { text-align: center; position: relative; z-index: 1; }
.sa-banner__kpi-val { display: block; font-size: 1.6rem; font-weight: 800; color: var(--c-gold); line-height: 1; }
.sa-banner__kpi-lbl { display: block; font-size: .65rem; color: rgba(255,255,255,.4); margin-top: .3rem; white-space: nowrap; }
.sa-banner__divider { width: 1px; background: rgba(255,255,255,.1); align-self: stretch; }

/* ── Metric grid ─────────────────────────────────── */
.metric-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 1rem;
  margin-bottom: 1.5rem;
}
@media (max-width: 991px) { .metric-grid { grid-template-columns: repeat(2,1fr); } }
@media (max-width: 575px) { .metric-grid { grid-template-columns: 1fr; } }

.metric-card {
  background: var(--c-surface);
  border-radius: var(--radius);
  border: 1px solid var(--c-border);
  padding: 1.25rem 1.375rem;
  box-shadow: var(--shadow-sm);
  position: relative; overflow: hidden;
  transition: var(--transition);
  display: flex; align-items: flex-start; gap: 1rem;
}
.metric-card:hover { box-shadow: var(--shadow); transform: translateY(-1px); }
.metric-card__icon {
  width: 46px; height: 46px; border-radius: 10px; flex-shrink: 0;
  display: flex; align-items: center; justify-content: center; font-size: 1rem;
}
.metric-card__body  { flex: 1; min-width: 0; }
.metric-card__val   { font-size: 1.7rem; font-weight: 800; color: var(--c-navy); line-height: 1; }
.metric-card__lbl   { font-size: .72rem; color: var(--c-muted); font-weight: 500; margin-top: .25rem; }
.metric-card__trend {
  font-size: .65rem; margin-top: .5rem; display: inline-flex; align-items: center; gap: .3rem;
  padding: .15rem .45rem; border-radius: 999px; font-weight: 600;
}
.metric-card__accent {
  position: absolute; top: 0; right: 0;
  width: 90px; height: 90px; border-radius: 0 var(--radius) 0 90px; opacity: .06;
}

/* ── Status chart ────────────────────────────────── */
.status-bar-wrap { display: flex; flex-direction: column; gap: .75rem; }
.status-bar-row  { display: flex; align-items: center; gap: .875rem; }
.status-bar-label { font-size: .75rem; font-weight: 600; color: var(--c-text); width: 90px; flex-shrink: 0; }
.status-bar-track { flex: 1; height: 8px; background: var(--c-bg); border-radius: 999px; overflow: hidden; }
.status-bar-fill  { height: 100%; border-radius: 999px; transition: width .6s ease; }
.status-bar-count { font-size: .72rem; font-weight: 700; color: var(--c-navy); width: 28px; text-align: right; flex-shrink: 0; }

/* ── Donut ───────────────────────────────────────── */
.ring-wrap         { display: flex; align-items: center; gap: 1.5rem; }
.ring-svg          { flex-shrink: 0; }
.ring-legend       { flex: 1; display: flex; flex-direction: column; gap: .5rem; }
.ring-legend-item  { display: flex; align-items: center; gap: .5rem; font-size: .75rem; }
.ring-dot          { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
.ring-lbl          { flex: 1; color: var(--c-text); }
.ring-pct          { font-weight: 700; color: var(--c-navy); }

/* ── Client row ─────────────────────────────────── */
.user-row       { display: flex; align-items: center; gap: .875rem; padding: .75rem 0; border-bottom: 1px solid var(--c-border); }
.user-row:last-child { border-bottom: none; }
.user-avatar    { width: 38px; height: 38px; border-radius: 50%; flex-shrink: 0; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: .8rem; }
.user-info      { flex: 1; min-width: 0; }
.user-name      { font-size: .825rem; font-weight: 600; color: var(--c-navy); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.user-email     { font-size: .7rem; color: var(--c-muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.user-since     { font-size: .68rem; color: var(--c-muted); flex-shrink: 0; }

/* ── View all link ───────────────────────────────── */
.view-all {
  display: inline-flex; align-items: center; gap: .35rem;
  font-size: .72rem; font-weight: 600; color: var(--c-gold-d);
  text-decoration: none; transition: color .15s;
}
.view-all:hover { color: var(--c-navy); }
</style>
@endpush

@section('content')

@php
  $total    = max($stats['total_loans'], 1);
  $pct_p    = round($stats['pending_loans']   / $total * 100);
  $pct_a    = round($stats['active_loans']    / $total * 100);
  $pct_f    = round($stats['finalized_loans'] / $total * 100);
  $pct_r    = round($stats['rejected_loans']  / $total * 100);
@endphp

{{-- ── BANNER ────────────────────────────────────────────────────── --}}
<div class="sa-banner">
  <div style="position:relative;z-index:1">
    <div class="sa-banner__eyebrow">Credixa — Espace Administrateur</div>
    <div class="sa-banner__title">Bonjour, {{ Auth::user()->name }}</div>
    <div class="sa-banner__sub">{{ now()->isoFormat('dddd D MMMM YYYY') }}</div>
  </div>
  <div class="sa-banner__kpis" style="position:relative;z-index:1">
    <div class="sa-banner__kpi">
      <span class="sa-banner__kpi-val">{{ $stats['my_clients'] }}</span>
      <span class="sa-banner__kpi-lbl">Mes clients</span>
    </div>
    <div class="sa-banner__divider"></div>
    <div class="sa-banner__kpi">
      <span class="sa-banner__kpi-val">{{ $stats['month_loans'] }}</span>
      <span class="sa-banner__kpi-lbl">Dossiers ce mois</span>
    </div>
    <div class="sa-banner__divider"></div>
    <div class="sa-banner__kpi">
      <span class="sa-banner__kpi-val">{{ $stats['total_amount'] > 0 ? number_format($stats['total_amount'] / 1000, 0, ',', ' ') . 'k' : '0' }}</span>
      <span class="sa-banner__kpi-lbl">Volume total (€)</span>
    </div>
  </div>
</div>

{{-- ── METRIC CARDS ──────────────────────────────────────────────── --}}
<div class="metric-grid">

  <div class="metric-card">
    <div class="metric-card__icon mi-navy"><i class="fas fa-users"></i></div>
    <div class="metric-card__body">
      <div class="metric-card__val">{{ $stats['my_clients'] }}</div>
      <div class="metric-card__lbl">Mes clients</div>
      <span class="metric-card__trend" style="background:rgba(37,99,235,.08);color:#2563EB">
        <i class="fas fa-user-tie" style="font-size:.58rem"></i>
        Portefeuille clients
      </span>
    </div>
    <div class="metric-card__accent" style="background:var(--c-navy)"></div>
  </div>

  <div class="metric-card">
    <div class="metric-card__icon mi-gold"><i class="fas fa-file-invoice-dollar"></i></div>
    <div class="metric-card__body">
      <div class="metric-card__val">{{ $stats['total_loans'] }}</div>
      <div class="metric-card__lbl">Mes dossiers</div>
      <span class="metric-card__trend" style="background:rgba(217,119,6,.08);color:var(--c-amber)">
        <i class="fas fa-clock" style="font-size:.58rem"></i>
        {{ $stats['month_loans'] }} ce mois
      </span>
    </div>
    <div class="metric-card__accent" style="background:var(--c-gold)"></div>
  </div>

  <div class="metric-card">
    <div class="metric-card__icon mi-amber"><i class="fas fa-hourglass-half"></i></div>
    <div class="metric-card__body">
      <div class="metric-card__val">{{ $stats['pending_loans'] }}</div>
      <div class="metric-card__lbl">En attente</div>
      @if($stats['pending_loans'] > 0)
      <span class="metric-card__trend" style="background:#FEF3C7;color:#D97706">
        <i class="fas fa-exclamation-triangle" style="font-size:.58rem"></i>
        Action requise
      </span>
      @else
      <span class="metric-card__trend" style="background:var(--c-green-l);color:var(--c-green)">
        <i class="fas fa-check" style="font-size:.58rem"></i>
        File vide
      </span>
      @endif
    </div>
    <div class="metric-card__accent" style="background:var(--c-amber)"></div>
  </div>

  <div class="metric-card">
    <div class="metric-card__icon mi-green"><i class="fas fa-check-circle"></i></div>
    <div class="metric-card__body">
      <div class="metric-card__val">{{ $stats['finalized_loans'] }}</div>
      <div class="metric-card__lbl">Finalisés</div>
      <span class="metric-card__trend" style="background:var(--c-green-l);color:var(--c-green)">
        {{ $pct_f }}% du total
      </span>
    </div>
    <div class="metric-card__accent" style="background:var(--c-green)"></div>
  </div>

</div>

{{-- ── ROW 2 : Répartition + Clients récents ──────────────────────── --}}
<div class="row g-4 mb-4">

  {{-- Donut répartition --}}
  <div class="col-lg-5">
    <div class="card-pro h-100">
      <div class="card-pro-hdr">
        <div class="card-pro-title">
          <span class="icon-dot"></span>Répartition de mes dossiers
        </div>
        <span class="badge-status bs-gray">{{ $stats['total_loans'] }} total</span>
      </div>
      <div class="card-pro-body">

        @if($stats['total_loans'] === 0)
        <div style="text-align:center;padding:2rem;color:var(--c-muted)">
          <i class="fas fa-inbox" style="font-size:2rem;margin-bottom:.75rem;display:block;opacity:.3"></i>
          Aucun dossier assigné
        </div>
        @else

        @php
          $cx=60; $cy=60; $r=46; $circ=2*3.14159*$r;
          $colors  = ['#D97706','#2563EB','#059669','#DC2626'];
          $vals    = [$stats['pending_loans'], $stats['active_loans'], $stats['finalized_loans'], $stats['rejected_loans']];
          $labels  = ['En attente','Actifs','Finalisés','Refusés'];
          $offset  = 0;
          $segments = [];
          foreach ($vals as $i => $v) {
            $len = $total > 0 ? ($v / $total) * $circ : 0;
            $segments[] = [
              'color'  => $colors[$i],
              'dash'   => $len,
              'offset' => $circ - $offset,
              'val'    => $v,
              'label'  => $labels[$i],
              'pct'    => $total > 0 ? round($v / $total * 100) : 0,
            ];
            $offset += $len;
          }
        @endphp

        <div class="ring-wrap">
          <svg class="ring-svg" width="120" height="120" viewBox="0 0 120 120">
            <circle cx="{{ $cx }}" cy="{{ $cy }}" r="{{ $r }}" fill="none" stroke="#F3F4F6" stroke-width="14"/>
            @foreach($segments as $seg)
            @if($seg['val'] > 0)
            <circle cx="{{ $cx }}" cy="{{ $cy }}" r="{{ $r }}" fill="none"
              stroke="{{ $seg['color'] }}" stroke-width="14"
              stroke-dasharray="{{ $seg['dash'] }} {{ $circ - $seg['dash'] }}"
              stroke-dashoffset="{{ $seg['offset'] }}"
              stroke-linecap="round"
              transform="rotate(-90 {{ $cx }} {{ $cy }})"/>
            @endif
            @endforeach
            <text x="{{ $cx }}" y="{{ $cy - 4 }}" text-anchor="middle" font-size="16" font-weight="800" fill="#0B1A2E">{{ $stats['total_loans'] }}</text>
            <text x="{{ $cx }}" y="{{ $cy + 11 }}" text-anchor="middle" font-size="8" fill="#6B7280">dossiers</text>
          </svg>
          <div class="ring-legend">
            @foreach($segments as $seg)
            <div class="ring-legend-item">
              <div class="ring-dot" style="background:{{ $seg['color'] }}"></div>
              <span class="ring-lbl">{{ $seg['label'] }}</span>
              <span class="ring-pct">{{ $seg['val'] }}</span>
            </div>
            @endforeach
          </div>
        </div>

        <div style="margin-top:1.5rem" class="status-bar-wrap">
          @foreach($segments as $seg)
          <div class="status-bar-row">
            <span class="status-bar-label">{{ $seg['label'] }}</span>
            <div class="status-bar-track">
              <div class="status-bar-fill" style="width:{{ $seg['pct'] }}%;background:{{ $seg['color'] }}"></div>
            </div>
            <span class="status-bar-count">{{ $seg['val'] }}</span>
          </div>
          @endforeach
        </div>

        @endif
      </div>
    </div>
  </div>

  {{-- Mes clients récents --}}
  <div class="col-lg-7">
    <div class="card-pro h-100">
      <div class="card-pro-hdr">
        <div class="card-pro-title">
          <span class="icon-dot"></span>Mes clients récents
        </div>
        <a href="{{ route('admin.users') }}" class="view-all">
          Voir tout <i class="fas fa-arrow-right" style="font-size:.6rem"></i>
        </a>
      </div>
      <div class="card-pro-body" style="padding:.75rem 1.25rem">
        @forelse($recentClients as $client)
        @php
          $avatarColors = ['#2563EB','#059669','#D97706','#7C3AED','#DC2626','#0D9488','#C8A951','#0B1A2E'];
          $avatarBg = $avatarColors[crc32($client->email) % count($avatarColors)];
        @endphp
        <div class="user-row">
          <div class="user-avatar" style="background:{{ $avatarBg }}20;color:{{ $avatarBg }}">
            {{ strtoupper(mb_substr($client->name, 0, 1)) }}
          </div>
          <div class="user-info">
            <div class="user-name">{{ $client->name }}</div>
            <div class="user-email">{{ $client->email }}</div>
          </div>
          <span class="badge-status bs-blue">client</span>
          <span class="user-since">{{ $client->created_at->diffForHumans(null, true) }}</span>
        </div>
        @empty
        <div style="text-align:center;padding:2rem;color:var(--c-muted);font-size:.8rem">
          <i class="fas fa-users" style="font-size:1.5rem;display:block;margin-bottom:.5rem;opacity:.3"></i>
          Aucun client assigné
        </div>
        @endforelse
      </div>
    </div>
  </div>

</div>

{{-- ── ROW 3 : Mes derniers dossiers ───────────────────────────────── --}}
<div class="card-pro">
  <div class="card-pro-hdr">
    <div class="card-pro-title">
      <span class="icon-dot"></span>Mes derniers dossiers
    </div>
    <a href="{{ route('admin.loans.index') }}" class="view-all">
      Voir tous mes dossiers <i class="fas fa-arrow-right" style="font-size:.6rem"></i>
    </a>
  </div>
  <div class="table-responsive-pro">
    <table class="pro-table">
      <thead>
        <tr>
          <th>Réf.</th>
          <th>Client</th>
          <th>Montant</th>
          <th>Objet</th>
          <th>Statut</th>
          <th>Date</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        @forelse($recentLoans as $loan)
        @php
          $stMap = [
            'draft'           => ['lbl' => 'Brouillon',        'cls' => 'bs-gray'],
            'pending'         => ['lbl' => 'En attente',       'cls' => 'bs-amber'],
            'validated'       => ['lbl' => 'Validé',           'cls' => 'bs-blue'],
            'contract_sent'   => ['lbl' => 'Contrat envoyé',   'cls' => 'bs-violet'],
            'contract_signed' => ['lbl' => 'Contrat signé',    'cls' => 'bs-emerald'],
            'finalized'       => ['lbl' => 'Finalisé',         'cls' => 'bs-green'],
            'rejected'        => ['lbl' => 'Refusé',           'cls' => 'bs-red'],
          ];
          $stInfo = $stMap[$loan->status] ?? ['lbl' => ucfirst($loan->status ?? '—'), 'cls' => 'bs-gray'];
          $clientName = $loan->client?->name ?? $loan->name ?? '—';
          $clientEmail = $loan->client?->email ?? $loan->email ?? '';
        @endphp
        <tr>
          <td data-label="Réf." class="cell-mono">{{ $loan->reference ?? '#'.$loan->id }}</td>
          <td data-label="Client">
            <div class="cell-name">{{ $clientName }}</div>
            <div class="cell-sub">{{ $clientEmail }}</div>
          </td>
          <td data-label="Montant" class="cell-amount" style="color:var(--c-navy);font-weight:800">
            {{ number_format($loan->amount ?? 0, 0, ',', ' ') }}&nbsp;{{ $loan->currency ?? '€' }}
          </td>
          <td data-label="Objet" style="max-width:160px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;font-size:.78rem;color:var(--c-muted)">
            {{ Str::limit($loan->objet ?? '—', 28) }}
          </td>
          <td data-label="Statut"><span class="badge-status {{ $stInfo['cls'] }}">{{ $stInfo['lbl'] }}</span></td>
          <td data-label="Date" style="font-size:.75rem;color:var(--c-muted);white-space:nowrap">
            {{ $loan->created_at->format('d/m/Y') }}
          </td>
          <td data-label="">
            <a href="{{ route('admin.loans.show', $loan) }}" class="btn-icon btn-icon-primary" title="Voir le dossier">
              <i class="fas fa-eye"></i>
            </a>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="7" style="text-align:center;padding:2.5rem;color:var(--c-muted)">
            <i class="fas fa-inbox" style="font-size:1.5rem;display:block;margin-bottom:.5rem;opacity:.3"></i>
            Aucun dossier assigné
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

@endsection
