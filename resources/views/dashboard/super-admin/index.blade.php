@extends('layouts.dashboard')
@section('title', 'Super Administration')
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
  position: absolute;
  top: -40px; right: -40px;
  width: 220px; height: 220px;
  border-radius: 50%;
  background: rgba(200,169,81,.07);
  pointer-events: none;
}
.sa-banner::after {
  content: '';
  position: absolute;
  bottom: -60px; right: 80px;
  width: 160px; height: 160px;
  border-radius: 50%;
  background: rgba(200,169,81,.04);
  pointer-events: none;
}
.sa-banner__eyebrow {
  font-size: .62rem; font-weight: 700; letter-spacing: .1em;
  text-transform: uppercase; color: var(--c-gold); margin-bottom: .35rem;
}
.sa-banner__title {
  font-size: 1.2rem; font-weight: 800; color: #fff; margin: 0 0 .3rem; line-height: 1.2;
}
.sa-banner__sub { font-size: .78rem; color: rgba(255,255,255,.45); }
.sa-banner__kpis {
  display: flex; gap: 2rem; flex-shrink: 0;
}
.sa-banner__kpi { text-align: center; position: relative; z-index: 1; }
.sa-banner__kpi-val {
  display: block; font-size: 1.6rem; font-weight: 800; color: var(--c-gold); line-height: 1;
}
.sa-banner__kpi-lbl {
  display: block; font-size: .65rem; color: rgba(255,255,255,.4); margin-top: .3rem; white-space: nowrap;
}
.sa-banner__divider { width: 1px; background: rgba(255,255,255,.1); align-self: stretch; }

/* ── Metric grid ─────────────────────────────────── */
.metric-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
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
.metric-card__body { flex: 1; min-width: 0; }
.metric-card__val { font-size: 1.7rem; font-weight: 800; color: var(--c-navy); line-height: 1; }
.metric-card__lbl { font-size: .72rem; color: var(--c-muted); font-weight: 500; margin-top: .25rem; }
.metric-card__trend {
  font-size: .65rem; margin-top: .5rem; display: inline-flex; align-items: center; gap: .3rem;
  padding: .15rem .45rem; border-radius: 999px; font-weight: 600;
}
.metric-card__accent {
  position: absolute; top: 0; right: 0;
  width: 90px; height: 90px; border-radius: 0 var(--radius) 0 90px; opacity: .06;
}

/* ── Status bar chart ────────────────────────────── */
.status-bar-wrap { display: flex; flex-direction: column; gap: .75rem; }
.status-bar-row { display: flex; align-items: center; gap: .875rem; }
.status-bar-label { font-size: .75rem; font-weight: 600; color: var(--c-text); width: 80px; flex-shrink: 0; }
.status-bar-track {
  flex: 1; height: 8px; background: var(--c-bg); border-radius: 999px; overflow: hidden;
}
.status-bar-fill { height: 100%; border-radius: 999px; transition: width .6s ease; }
.status-bar-count { font-size: .72rem; font-weight: 700; color: var(--c-navy); width: 28px; text-align: right; flex-shrink: 0; }

/* ── User list ───────────────────────────────────── */
.user-row {
  display: flex; align-items: center; gap: .875rem;
  padding: .75rem 0;
  border-bottom: 1px solid var(--c-border);
}
.user-row:last-child { border-bottom: none; }
.user-avatar {
  width: 36px; height: 36px; border-radius: 50%; flex-shrink: 0;
  display: flex; align-items: center; justify-content: center;
  font-weight: 800; font-size: .78rem; color: var(--c-navy);
}
.user-info { flex: 1; min-width: 0; }
.user-name { font-size: .825rem; font-weight: 600; color: var(--c-navy); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.user-email { font-size: .7rem; color: var(--c-muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.user-since { font-size: .68rem; color: var(--c-muted); flex-shrink: 0; }

/* ── Loan list ───────────────────────────────────── */
.loan-row {
  display: flex; align-items: center; gap: .875rem;
  padding: .75rem 0; border-bottom: 1px solid var(--c-border);
}
.loan-row:last-child { border-bottom: none; }
.loan-icon {
  width: 36px; height: 36px; border-radius: var(--radius-sm); flex-shrink: 0;
  display: flex; align-items: center; justify-content: center; font-size: .8rem;
}
.loan-info { flex: 1; min-width: 0; }
.loan-name { font-size: .825rem; font-weight: 600; color: var(--c-navy); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.loan-date { font-size: .7rem; color: var(--c-muted); }
.loan-amount { font-size: .875rem; font-weight: 800; color: var(--c-navy); flex-shrink: 0; white-space: nowrap; }

/* ── Donut ring (CSS only) ───────────────────────── */
.ring-wrap { display: flex; align-items: center; gap: 1.5rem; }
.ring-svg { flex-shrink: 0; }
.ring-legend { flex: 1; display: flex; flex-direction: column; gap: .5rem; }
.ring-legend-item { display: flex; align-items: center; gap: .5rem; font-size: .75rem; }
.ring-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
.ring-lbl { flex: 1; color: var(--c-text); }
.ring-pct { font-weight: 700; color: var(--c-navy); }

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
  $total  = max($stats['total_loans'], 1);
  $pct_p  = round($stats['pending_loans']  / $total * 100);
  $pct_a  = round($stats['approved_loans'] / $total * 100);
  $pct_r  = round($stats['rejected_loans'] / $total * 100);
  $pct_rv = round($stats['review_loans']   / $total * 100);
@endphp

{{-- ── BANNER ────────────────────────────────────────────────────── --}}
<div class="sa-banner">
  <div style="position:relative;z-index:1">
    <div class="sa-banner__eyebrow">Credixa Invest — Panneau Super Admin</div>
    <div class="sa-banner__title">Vue d'ensemble du système</div>
    <div class="sa-banner__sub">{{ now()->isoFormat('dddd D MMMM YYYY') }}</div>
  </div>
  <div class="sa-banner__kpis" style="position:relative;z-index:1">
    <div class="sa-banner__kpi">
      <span class="sa-banner__kpi-val">{{ $stats['total_users'] }}</span>
      <span class="sa-banner__kpi-lbl">Utilisateurs</span>
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

  {{-- Utilisateurs --}}
  <div class="metric-card">
    <div class="metric-card__icon mi-navy"><i class="fas fa-users"></i></div>
    <div class="metric-card__body">
      <div class="metric-card__val">{{ $stats['total_users'] }}</div>
      <div class="metric-card__lbl">Utilisateurs totaux</div>
      <div style="display:flex;gap:.5rem;margin-top:.5rem;flex-wrap:wrap">
        <span class="badge-status bs-blue">{{ $stats['total_clients'] }} clients</span>
        <span class="badge-status bs-violet">{{ $stats['total_staff'] }} staff</span>
      </div>
    </div>
    <div class="metric-card__accent" style="background:var(--c-navy)"></div>
  </div>

  {{-- Demandes totales --}}
  <div class="metric-card">
    <div class="metric-card__icon mi-gold"><i class="fas fa-file-invoice-dollar"></i></div>
    <div class="metric-card__body">
      <div class="metric-card__val">{{ $stats['total_loans'] }}</div>
      <div class="metric-card__lbl">Demandes de prêt</div>
      <span class="metric-card__trend" style="background:var(--c-amber-l);color:var(--c-amber)">
        <i class="fas fa-clock" style="font-size:.58rem"></i>
        {{ $stats['month_loans'] }} ce mois
      </span>
    </div>
    <div class="metric-card__accent" style="background:var(--c-gold)"></div>
  </div>

  {{-- En attente --}}
  <div class="metric-card">
    <div class="metric-card__icon mi-amber"><i class="fas fa-hourglass-half"></i></div>
    <div class="metric-card__body">
      <div class="metric-card__val">{{ $stats['pending_loans'] }}</div>
      <div class="metric-card__lbl">En attente de traitement</div>
      @if($stats['pending_loans'] > 0)
      <span class="metric-card__trend" style="background:#FEF3C7;color:#D97706">
        <i class="fas fa-exclamation-triangle" style="font-size:.58rem"></i>
        Nécessitent une action
      </span>
      @else
      <span class="metric-card__trend" style="background:var(--c-green-l);color:var(--c-green)">
        <i class="fas fa-check" style="font-size:.58rem"></i>
        File d'attente vide
      </span>
      @endif
    </div>
    <div class="metric-card__accent" style="background:var(--c-amber)"></div>
  </div>

  {{-- Approuvées --}}
  <div class="metric-card">
    <div class="metric-card__icon mi-green"><i class="fas fa-check-circle"></i></div>
    <div class="metric-card__body">
      <div class="metric-card__val">{{ $stats['approved_loans'] }}</div>
      <div class="metric-card__lbl">Demandes approuvées</div>
      <span class="metric-card__trend" style="background:var(--c-green-l);color:var(--c-green)">
        {{ $pct_a }}% du total
      </span>
    </div>
    <div class="metric-card__accent" style="background:var(--c-green)"></div>
  </div>

  {{-- En révision --}}
  <div class="metric-card">
    <div class="metric-card__icon mi-blue"><i class="fas fa-search"></i></div>
    <div class="metric-card__body">
      <div class="metric-card__val">{{ $stats['review_loans'] }}</div>
      <div class="metric-card__lbl">En cours d'analyse</div>
      <span class="metric-card__trend" style="background:var(--c-blue-l);color:var(--c-blue)">
        {{ $pct_rv }}% du total
      </span>
    </div>
    <div class="metric-card__accent" style="background:var(--c-blue)"></div>
  </div>

  {{-- Refusées --}}
  <div class="metric-card">
    <div class="metric-card__icon mi-red"><i class="fas fa-times-circle"></i></div>
    <div class="metric-card__body">
      <div class="metric-card__val">{{ $stats['rejected_loans'] }}</div>
      <div class="metric-card__lbl">Demandes refusées</div>
      <span class="metric-card__trend" style="background:var(--c-red-l);color:var(--c-red)">
        {{ $pct_r }}% du total
      </span>
    </div>
    <div class="metric-card__accent" style="background:var(--c-red)"></div>
  </div>

</div>

{{-- ── ROW 2 : Répartition + Utilisateurs récents ────────────────── --}}
<div class="row g-4 mb-4">

  {{-- Répartition des statuts --}}
  <div class="col-lg-5">
    <div class="card-pro h-100">
      <div class="card-pro-hdr">
        <div class="card-pro-title">
          <span class="icon-dot"></span>Répartition des demandes
        </div>
        <span class="badge-status bs-gray">{{ $stats['total_loans'] }} total</span>
      </div>
      <div class="card-pro-body">

        @if($stats['total_loans'] === 0)
        <div style="text-align:center;padding:2rem;color:var(--c-muted)">
          <i class="fas fa-inbox" style="font-size:2rem;margin-bottom:.75rem;display:block;opacity:.3"></i>
          Aucune demande enregistrée
        </div>
        @else

        {{-- Donut ring SVG --}}
        @php
          $cx=60; $cy=60; $r=46; $circ=2*3.14159*$r;
          $colors = ['#2563EB','#059669','#D97706','#DC2626'];
          $vals   = [$stats['pending_loans'],$stats['approved_loans'],$stats['review_loans'],$stats['rejected_loans']];
          $labels = ['En attente','Approuvées','En révision','Refusées'];
          $offset = 0;
          $segments = [];
          foreach ($vals as $i => $v) {
            $len = $total > 0 ? ($v / $total) * $circ : 0;
            $segments[] = ['color'=>$colors[$i],'dash'=>$len,'offset'=>$circ-$offset,'val'=>$v,'label'=>$labels[$i],'pct'=>$total>0?round($v/$total*100):0];
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

  {{-- Derniers utilisateurs --}}
  <div class="col-lg-7">
    <div class="card-pro h-100">
      <div class="card-pro-hdr">
        <div class="card-pro-title">
          <span class="icon-dot"></span>Derniers utilisateurs inscrits
        </div>
        <a href="{{ route('admin.users') }}" class="view-all">
          Voir tout <i class="fas fa-arrow-right" style="font-size:.6rem"></i>
        </a>
      </div>
      <div class="card-pro-body" style="padding:.75rem 1.25rem">
        @forelse($recentUsers as $u)
        @php
          $avatarColors = ['#2563EB','#059669','#D97706','#7C3AED','#DC2626','#0D9488','#C8A951','#0B1A2E'];
          $avatarBg = $avatarColors[crc32($u->email) % count($avatarColors)];
        @endphp
        <div class="user-row">
          <div class="user-avatar" style="background:{{ $avatarBg }}20;color:{{ $avatarBg }}">
            {{ strtoupper(mb_substr($u->name, 0, 1)) }}
          </div>
          <div class="user-info">
            <div class="user-name">{{ $u->name }}</div>
            <div class="user-email">{{ $u->email }}</div>
          </div>
          @if($u->getRoleNames()->first())
          @php
            $rn = $u->getRoleNames()->first();
            $rc = ['super-admin'=>'bs-dark','admin'=>'bs-amber','client'=>'bs-blue'][$rn] ?? 'bs-gray';
          @endphp
          <span class="badge-status {{ $rc }}">{{ ucfirst(str_replace('-',' ',$rn)) }}</span>
          @else
          <span class="badge-status bs-gray">—</span>
          @endif
          <span class="user-since">{{ $u->created_at->diffForHumans(null, true) }}</span>
        </div>
        @empty
        <div style="text-align:center;padding:2rem;color:var(--c-muted);font-size:.8rem">
          Aucun utilisateur
        </div>
        @endforelse
      </div>
    </div>
  </div>

</div>

{{-- ── ROW 3 : Dernières demandes ──────────────────────────────────── --}}
<div class="card-pro">
  <div class="card-pro-hdr">
    <div class="card-pro-title">
      <span class="icon-dot"></span>Dernières demandes de prêt
    </div>
    <a href="{{ route('super-admin.loans.index') }}" class="view-all">
      Voir toutes les demandes <i class="fas fa-arrow-right" style="font-size:.6rem"></i>
    </a>
  </div>
  <div class="table-responsive-pro">
    <table class="pro-table">
      <thead>
        <tr>
          <th>#</th>
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
          $st = $loan->status ?? 'pending';
          $stMap = ['pending'=>['lbl'=>'En attente','cls'=>'bs-amber'],'approved'=>['lbl'=>'Approuvé','cls'=>'bs-green'],'rejected'=>['lbl'=>'Refusé','cls'=>'bs-red'],'review'=>['lbl'=>'En révision','cls'=>'bs-blue'],'signed'=>['lbl'=>'Signé','cls'=>'bs-emerald']];
          $stInfo = $stMap[$st] ?? ['lbl'=>ucfirst($st),'cls'=>'bs-gray'];
        @endphp
        <tr>
          <td data-label="#" class="cell-mono">#{{ $loan->id }}</td>
          <td data-label="Client">
            <div class="cell-name">{{ $loan->name }}</div>
            <div class="cell-sub">{{ $loan->email }}</div>
          </td>
          <td data-label="Montant" class="cell-amount" style="color:var(--c-navy)">
            {{ number_format($loan->amount ?? 0, 0, ',', ' ') }}&nbsp;€
          </td>
          <td data-label="Objet" style="max-width:160px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;font-size:.78rem;color:var(--c-muted)">
            {{ Str::limit($loan->objet ?? '—', 28) }}
          </td>
          <td data-label="Statut"><span class="badge-status {{ $stInfo['cls'] }}">{{ $stInfo['lbl'] }}</span></td>
          <td data-label="Date" style="font-size:.75rem;color:var(--c-muted);white-space:nowrap">
            {{ $loan->created_at->format('d/m/Y') }}
          </td>
          <td data-label="">
            <a href="{{ route('super-admin.loans.show', $loan) }}" class="btn-icon btn-icon-primary" title="Voir le dossier">
              <i class="fas fa-eye"></i>
            </a>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="7" style="text-align:center;padding:2.5rem;color:var(--c-muted)">
            <i class="fas fa-inbox" style="font-size:1.5rem;display:block;margin-bottom:.5rem;opacity:.3"></i>
            Aucune demande enregistrée
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

@endsection
