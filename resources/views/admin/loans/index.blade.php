@extends('layouts.dashboard')
@section('title','Demandes de prêt')
@section('page_title','Demandes de prêt')

@push('styles')
<style>
.page-top{display:flex;align-items:flex-start;justify-content:space-between;gap:1rem;flex-wrap:wrap;margin-bottom:1.5rem}
.page-top h1{font-size:1.375rem;font-weight:800;color:var(--c-navy);margin:0 0 .2rem}
.page-top p{font-size:.8125rem;color:var(--c-muted);margin:0}

/* KPI strip */
.kpi-strip{display:grid;grid-template-columns:repeat(5,1fr);gap:1rem;margin-bottom:1.5rem}
@media(max-width:900px){.kpi-strip{grid-template-columns:repeat(3,1fr)}}
@media(max-width:540px){.kpi-strip{grid-template-columns:repeat(2,1fr)}}
.kpi-tile{background:var(--c-card,#fff);border:1px solid var(--c-border);border-radius:12px;
          padding:.875rem 1rem;display:flex;align-items:center;gap:.75rem}
.kpi-tile__icon{width:36px;height:36px;border-radius:9px;display:flex;align-items:center;
               justify-content:center;font-size:.8rem;flex-shrink:0}
.kpi-tile__val{font-size:1.25rem;font-weight:800;color:var(--c-navy);line-height:1}
.kpi-tile__lbl{font-size:.7rem;color:var(--c-muted);margin-top:.2rem}

/* Filter bar */
.fbar{background:var(--c-card,#fff);border:1px solid var(--c-border);border-radius:12px;
      padding:.875rem 1.25rem;display:flex;flex-wrap:wrap;gap:.625rem;align-items:center;margin-bottom:1.25rem}
.fbar input,.fbar select{font-size:.83rem;padding:.5rem .75rem;border:1.5px solid var(--c-border);
  border-radius:8px;color:var(--c-navy);background:#fff;outline:none;transition:border-color .15s}
.fbar input:focus,.fbar select:focus{border-color:var(--c-navy)}
.fbar input{min-width:220px;flex:1}
.fbar select{min-width:160px}

/* Table */
.tbl-wrap{background:var(--c-card,#fff);border:1px solid var(--c-border);border-radius:12px;overflow:hidden}
.tbl-scroll{overflow-x:auto}
table.dt{width:100%;border-collapse:collapse}
table.dt thead tr{background:var(--c-bg,#f8f9fa)}
table.dt th{font-size:.7rem;font-weight:700;color:var(--c-muted);text-transform:uppercase;
            letter-spacing:.06em;padding:.75rem 1rem;white-space:nowrap;border-bottom:1px solid var(--c-border)}
table.dt td{font-size:.8125rem;padding:.8125rem 1rem;border-bottom:1px solid var(--c-border);
            color:var(--c-navy);vertical-align:middle}
table.dt tbody tr:last-child td{border-bottom:0}
table.dt tbody tr:hover td{background:rgba(var(--c-navy-rgb,14,30,64),.025)}
.ref-mono{font-family:monospace;font-weight:700;font-size:.8rem;color:var(--c-navy);letter-spacing:.03em}
.client-name{font-weight:600;color:var(--c-navy);font-size:.8375rem}
.client-sub{font-size:.72rem;color:var(--c-muted);margin-top:.1rem}
.amount-val{font-weight:700;color:var(--c-navy)}
.amount-cur{font-size:.72rem;color:var(--c-muted);font-weight:500}
.dur-val{font-size:.8125rem;color:var(--c-navy)}
.act-cell{display:inline-flex;gap:.3rem}
.empty-state{text-align:center;padding:4rem 1rem}
.empty-icon{font-size:2.75rem;opacity:.2;display:block;margin-bottom:.875rem;color:var(--c-navy)}
.empty-title{font-size:.9375rem;font-weight:700;color:var(--c-navy);margin-bottom:.3rem}
.empty-sub{font-size:.8125rem;color:var(--c-muted);margin-bottom:1.25rem}

/* Pagination */
.pagi-wrap{padding:.875rem 1.25rem;border-top:1px solid var(--c-border)}
</style>
@endpush

@section('content')

<div class="page-top">
  <div>
    <h1>Demandes de prêt</h1>
    <p>{{ $stats['total'] }} dossier(s) · {{ $stats['pending'] }} en attente · {{ $stats['validated'] }} validé(s)</p>
  </div>
  <a href="{{ route('admin.loans.create') }}" class="btn-navy">
    <i class="fas fa-plus"></i> Nouvelle demande
  </a>
</div>

{{-- KPI --}}
<div class="kpi-strip">
  @php
  $tiles = [
    ['Tous',        $stats['total'],     '#EEF2FF','var(--c-navy)','fa-layer-group'],
    ['Brouillons',  $stats['draft'],     '#F8FAFC','var(--c-muted)','fa-pen'],
    ['En attente',  $stats['pending'],   '#FEF9C3','#b45309','fa-hourglass-half'],
    ['Validés',     $stats['validated'], '#EFF6FF','#1d4ed8','fa-check-circle'],
    ['Finalisés',   $stats['finalized'], '#F0FDF4','#166534','fa-flag-checkered'],
  ];
  @endphp
  @foreach($tiles as [$lbl,$val,$bg,$clr,$ico])
  <div class="kpi-tile">
    <div class="kpi-tile__icon" style="background:{{ $bg }}">
      <i class="fas {{ $ico }}" style="color:{{ $clr }}"></i>
    </div>
    <div>
      <div class="kpi-tile__val">{{ $val }}</div>
      <div class="kpi-tile__lbl">{{ $lbl }}</div>
    </div>
  </div>
  @endforeach
</div>

{{-- Filtres --}}
<form method="GET" class="fbar">
  <input type="text" name="search" placeholder="Référence, nom, email…" value="{{ request('search') }}">
  <select name="status">
    <option value="">Tous les statuts</option>
    @foreach([
      'draft'=>'Brouillon','pending'=>'En attente','validated'=>'Validée',
      'contract_sent'=>'Contrat envoyé','contract_signed'=>'Contrat signé',
      'finalized'=>'Finalisée','rejected'=>'Rejetée'
    ] as $val=>$label)
    <option value="{{ $val }}" {{ request('status')===$val?'selected':'' }}>{{ $label }}</option>
    @endforeach
  </select>
  <button type="submit" class="btn-navy btn-sm-pro"><i class="fas fa-filter"></i> Filtrer</button>
  @if(request()->anyFilled(['search','status']))
  <a href="{{ route('admin.loans.index') }}" class="btn-ghost btn-sm-pro">
    <i class="fas fa-times"></i> Effacer
  </a>
  @endif
</form>

{{-- Tableau --}}
<div class="tbl-wrap">
  <div class="tbl-scroll">
    <table class="dt">
      <thead>
        <tr>
          <th>Référence</th>
          <th>Client</th>
          <th>Montant</th>
          <th>Durée</th>
          <th>Mensualité</th>
          <th>Agent / Directeur</th>
          <th>Statut</th>
          <th>Date</th>
          <th style="text-align:right">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($loans as $loan)
        <tr>
          <td>
            <a href="{{ route('admin.loans.show',$loan) }}" class="ref-mono"
               style="text-decoration:none;color:var(--c-navy)">
              {{ $loan->reference }}
            </a>
            @if($loan->contract_pdf_path)
            <span title="PDF uploadé" style="margin-left:.3rem;font-size:.68rem;color:#dc2626">
              <i class="fas fa-file-pdf"></i>
            </span>
            @endif
          </td>
          <td>
            <div class="client-name">{{ $loan->name }}</div>
            <div class="client-sub">{{ $loan->email }}</div>
          </td>
          <td>
            <span class="amount-val">{{ number_format($loan->amount,0,',',' ') }}</span>
            <span class="amount-cur">{{ $loan->currency }}</span>
          </td>
          <td class="dur-val">{{ $loan->darly }} mois</td>
          <td>
            <span style="font-weight:600">{{ number_format($loan->monthly_payment,2,',',' ') }}</span>
            <span class="amount-cur">{{ $loan->currency }}</span>
          </td>
          <td>
            <div style="font-size:.78rem;color:var(--c-navy)">{{ $loan->agent_suivi ?: '—' }}</div>
            @if($loan->directeur)
            <div style="font-size:.72rem;color:var(--c-muted)">Dir. {{ $loan->directeur }}</div>
            @endif
          </td>
          <td>
            <span class="badge-status bs-{{ $loan->statusColor() }}">{{ $loan->statusLabel() }}</span>
          </td>
          <td style="color:var(--c-muted);font-size:.76rem;white-space:nowrap">
            {{ $loan->created_at->format('d/m/Y') }}
          </td>
          <td>
            <div class="act-cell" style="justify-content:flex-end">
              <a href="{{ route('admin.loans.show',$loan) }}" class="btn-icon btn-icon-primary" title="Voir">
                <i class="fas fa-eye"></i>
              </a>
              @if($loan->isEditable())
              <a href="{{ route('admin.loans.edit',$loan) }}" class="btn-icon" title="Modifier">
                <i class="fas fa-pen"></i>
              </a>
              @endif
              @if($loan->canBeValidated())
              <form action="{{ route('admin.loans.validate',$loan) }}" method="POST"
                    onsubmit="return confirm('Valider et envoyer le contrat ?')">
                @csrf
                <button class="btn-icon btn-icon-success" title="Valider & Envoyer">
                  <i class="fas fa-paper-plane"></i>
                </button>
              </form>
              @endif
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="9">
            <div class="empty-state">
              <i class="fas fa-folder-open empty-icon"></i>
              <div class="empty-title">Aucune demande trouvée</div>
              <div class="empty-sub">
                @if(request()->anyFilled(['search','status']))
                  Aucun résultat pour ces filtres.
                @else
                  Créez votre première demande de prêt.
                @endif
              </div>
              <a href="{{ route('admin.loans.create') }}" class="btn-navy btn-sm-pro">
                <i class="fas fa-plus"></i> Nouvelle demande
              </a>
            </div>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($loans->hasPages())
  <div class="pagi-wrap">{{ $loans->links() }}</div>
  @endif
</div>

@endsection
