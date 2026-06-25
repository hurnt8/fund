@extends('layouts.dashboard')
@section('title','Demandes de prêt')
@section('page_title','Demandes de prêt')

@push('styles')
<style>
/* ─────────────────────────────────────────
   LOANS INDEX — préfixe li-
   ───────────────────────────────────────── */

/* ── Header ── */
.li-header{display:flex;align-items:center;justify-content:space-between;gap:1rem;flex-wrap:wrap;margin-bottom:1.5rem}
.li-header-title{font-size:1.25rem;font-weight:800;color:var(--c-navy);margin:0 0 .2rem}
.li-header-sub{font-size:.8rem;color:var(--c-muted);margin:0}

/* ── Status quick-filters ── */
.li-filters-strip{display:flex;gap:.5rem;flex-wrap:wrap;margin-bottom:1.25rem}
.li-chip{display:inline-flex;align-items:center;gap:.4rem;padding:.375rem .875rem;border-radius:999px;font-size:.76rem;font-weight:600;border:1.5px solid var(--c-border);background:#fff;color:var(--c-muted);cursor:pointer;text-decoration:none;transition:.15s;white-space:nowrap}
.li-chip:hover{border-color:var(--c-navy);color:var(--c-navy)}
.li-chip.active{background:var(--c-navy);border-color:var(--c-navy);color:#fff}
.li-chip-count{font-size:.65rem;padding:.1rem .35rem;border-radius:10px;background:rgba(0,0,0,.1);font-weight:800;min-width:18px;text-align:center}
.li-chip.active .li-chip-count{background:rgba(255,255,255,.2)}

/* Couleurs chips par statut */
.li-chip[data-s="pending"]:not(.active):hover{border-color:#d97706;color:#d97706}
.li-chip[data-s="pending"].active{background:#d97706;border-color:#d97706}
.li-chip[data-s="validated"]:not(.active):hover{border-color:#2563eb;color:#2563eb}
.li-chip[data-s="validated"].active{background:#2563eb;border-color:#2563eb}
.li-chip[data-s="contract_sent"]:not(.active):hover{border-color:#0891b2;color:#0891b2}
.li-chip[data-s="contract_sent"].active{background:#0891b2;border-color:#0891b2}
.li-chip[data-s="contract_signed"]:not(.active):hover{border-color:#7c3aed;color:#7c3aed}
.li-chip[data-s="contract_signed"].active{background:#7c3aed;border-color:#7c3aed}
.li-chip[data-s="finalized"]:not(.active):hover{border-color:#059669;color:#059669}
.li-chip[data-s="finalized"].active{background:#059669;border-color:#059669}
.li-chip[data-s="rejected"]:not(.active):hover{border-color:#dc2626;color:#dc2626}
.li-chip[data-s="rejected"].active{background:#dc2626;border-color:#dc2626}

/* ── Barre de recherche ── */
.li-search-bar{background:#fff;border:1px solid var(--c-border);border-radius:12px;padding:.75rem 1.125rem;display:flex;gap:.625rem;align-items:center;margin-bottom:1.25rem;flex-wrap:wrap}
.li-search-input{flex:1;min-width:200px;padding:.5rem .75rem;border:1.5px solid var(--c-border);border-radius:8px;font-size:.83rem;color:var(--c-navy);background:#f8f9fa;outline:none;transition:.15s;font-family:inherit}
.li-search-input:focus{border-color:var(--c-gold);background:#fff;box-shadow:0 0 0 3px rgba(200,169,81,.1)}
.li-search-input::placeholder{color:#c4cadc}

/* ── Table card ── */
.li-table-card{background:#fff;border:1px solid var(--c-border);border-radius:14px;overflow:hidden}
.li-table-scroll{overflow-x:auto}

/* ── Table header info ── */
.li-table-info{display:flex;align-items:center;justify-content:space-between;padding:.75rem 1.25rem;border-bottom:1px solid var(--c-border);background:#fafbfc;flex-wrap:wrap;gap:.5rem}
.li-table-count{font-size:.78rem;color:var(--c-muted);font-weight:500}
.li-table-count strong{color:var(--c-navy)}

/* ── Table ── */
table.li-tbl{width:100%;border-collapse:collapse}
table.li-tbl thead th{padding:.75rem 1rem;font-size:.68rem;font-weight:700;color:var(--c-muted);text-transform:uppercase;letter-spacing:.07em;background:#fafbfc;border-bottom:1px solid var(--c-border);white-space:nowrap;text-align:left}
table.li-tbl tbody td{padding:.875rem 1rem;font-size:.82rem;color:var(--c-text);border-bottom:1px solid #f3f4f6;vertical-align:middle}
table.li-tbl tbody tr:last-child td{border-bottom:0}
table.li-tbl tbody tr:hover td{background:#f8faff}
table.li-tbl tbody tr:hover td:first-child{border-left-color:var(--c-gold)}

/* Left accent on hover */
table.li-tbl tbody td:first-child{border-left:3px solid transparent;transition:border-color .15s}

/* ── Cellules ── */
.li-ref{font-family:monospace;font-weight:800;font-size:.82rem;color:var(--c-navy);letter-spacing:.03em;text-decoration:none;transition:.15s}
.li-ref:hover{color:var(--c-gold)}
.li-pdf-dot{display:inline-flex;align-items:center;justify-content:center;width:16px;height:16px;border-radius:50%;background:#FFF1F2;border:1px solid #FECDD3;margin-left:.35rem;vertical-align:middle}
.li-pdf-dot i{font-size:.55rem;color:#dc2626}

.li-client-avatar{width:30px;height:30px;border-radius:8px;background:linear-gradient(135deg,var(--c-navy),#1a3a6c);display:flex;align-items:center;justify-content:center;font-weight:800;font-size:.72rem;color:var(--c-gold);flex-shrink:0}
.li-client-name{font-weight:600;color:var(--c-navy);font-size:.82rem}
.li-client-sub{font-size:.7rem;color:var(--c-muted);margin-top:.05rem}

.li-amount{font-weight:800;color:var(--c-navy);font-size:.875rem}
.li-amount-sub{font-size:.7rem;color:var(--c-muted);margin-top:.1rem}

.li-monthly{font-weight:700;color:var(--c-navy)}
.li-monthly-sub{font-size:.7rem;color:var(--c-muted)}

.li-date{font-size:.75rem;color:var(--c-muted);white-space:nowrap}
.li-date-rel{font-size:.65rem;color:var(--c-muted);margin-top:.1rem}

.li-act{display:flex;gap:.3rem;justify-content:flex-end}

/* ── Empty state ── */
.li-empty{padding:4rem 1rem;text-align:center}
.li-empty-icon{font-size:2.5rem;color:var(--c-muted);opacity:.25;display:block;margin-bottom:.875rem}
.li-empty-title{font-size:.9375rem;font-weight:700;color:var(--c-navy);margin-bottom:.3rem}
.li-empty-sub{font-size:.8125rem;color:var(--c-muted);margin-bottom:1.25rem}

/* ── Pagination ── */
.li-pagi{padding:.875rem 1.25rem;border-top:1px solid var(--c-border);display:flex;align-items:center;justify-content:between;gap:1rem}
</style>
@endpush

@section('content')

@php
$statusChips = [
    ''               => ['Tous',            $stats['total'],          'fa-layer-group'],
    'draft'          => ['Brouillons',      $stats['draft'],          'fa-pen'],
    'pending'        => ['En attente',      $stats['pending'],        'fa-hourglass-half'],
    'validated'      => ['Validées',        $stats['validated'],      'fa-check-circle'],
    'contract_sent'  => ['Contrat envoyé',  $stats['contract_sent'],  'fa-paper-plane'],
    'contract_signed'=> ['Contrat signé',   $stats['contract_signed'],'fa-file-signature'],
    'finalized'      => ['Finalisées',      $stats['finalized'],      'fa-flag-checkered'],
    'rejected'       => ['Rejetées',        $stats['rejected'],       'fa-ban'],
];
$currentStatus = request('status','');
@endphp

@if(session('success'))
<div class="flash flash-ok"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
@endif
@if(session('error'))
<div class="flash flash-err"><i class="fas fa-exclamation-triangle"></i> {{ session('error') }}</div>
@endif

{{-- ── HEADER ── --}}
<div class="li-header">
  <div>
    <div class="li-header-title">Demandes de prêt</div>
    <p class="li-header-sub">
      <strong style="color:var(--c-navy)">{{ $stats['total'] }}</strong> dossier(s) au total
      @if($stats['pending']) · <span style="color:#d97706;font-weight:600">{{ $stats['pending'] }} en attente</span>@endif
      @if($stats['contract_sent']) · <span style="color:#0891b2;font-weight:600">{{ $stats['contract_sent'] }} contrat(s) envoyé(s)</span>@endif
    </p>
  </div>
  <a href="{{ route('admin.loans.create') }}" class="btn-navy">
    <i class="fas fa-plus"></i> Nouvelle demande
  </a>
</div>

{{-- ── CHIPS DE STATUT (filtre rapide) ── --}}
<div class="li-filters-strip">
  @foreach($statusChips as $val => [$lbl, $count, $ico])
  @if($count > 0 || $val === '')
  <a href="{{ route('admin.loans.index', array_merge(request()->except('status','page'), $val ? ['status'=>$val] : [])) }}"
     class="li-chip {{ $currentStatus === $val ? 'active' : '' }}"
     data-s="{{ $val }}">
    <i class="fas {{ $ico }}" style="font-size:.7rem"></i>
    {{ $lbl }}
    <span class="li-chip-count">{{ $count }}</span>
  </a>
  @endif
  @endforeach
</div>

{{-- ── BARRE DE RECHERCHE ── --}}
<form method="GET" action="{{ route('admin.loans.index') }}" class="li-search-bar">
  @if($currentStatus)
  <input type="hidden" name="status" value="{{ $currentStatus }}">
  @endif
  <i class="fas fa-search" style="color:var(--c-muted);font-size:.85rem;flex-shrink:0"></i>
  <input type="text" name="search" class="li-search-input"
         placeholder="Référence, nom du client, email…"
         value="{{ request('search') }}"
         autocomplete="off">
  @if($isSuperAdmin && $admins->isNotEmpty())
  <select name="admin_id" class="li-search-input" style="flex:0;min-width:160px;cursor:pointer">
    <option value="">— Tous les admins —</option>
    @foreach($admins as $a)
    <option value="{{ $a->id }}" {{ request('admin_id') == $a->id ? 'selected' : '' }}>
      {{ $a->name }}
    </option>
    @endforeach
  </select>
  @endif
  <button type="submit" class="btn-navy btn-sm-pro">
    <i class="fas fa-search"></i> Rechercher
  </button>
  @if(request()->anyFilled(['search','status','admin_id']))
  <a href="{{ route('admin.loans.index') }}" class="btn-ghost btn-sm-pro">
    <i class="fas fa-times"></i> Effacer
  </a>
  @endif
</form>

{{-- ── TABLE ── --}}
<div class="li-table-card">

  <div class="li-table-info">
    <div class="li-table-count">
      @if(request()->anyFilled(['search','status']))
        <strong>{{ $loans->total() }}</strong> résultat(s) trouvé(s)
        @if(request('search')) pour « <em>{{ request('search') }}</em> » @endif
        @if(request('status')) — statut <em>{{ $statusChips[request('status')][0] ?? request('status') }}</em> @endif
      @else
        <strong>{{ $loans->total() }}</strong> dossier(s) au total
      @endif
    </div>
    <div style="font-size:.72rem;color:var(--c-muted)">
      Page {{ $loans->currentPage() }} / {{ $loans->lastPage() }}
    </div>
  </div>

  <div class="li-table-scroll">
    <table class="li-tbl">
      <thead>
        <tr>
          <th>Référence</th>
          <th>Client</th>
          @if($isSuperAdmin)<th>Admin</th>@endif
          <th>Financement</th>
          <th>Mensualité</th>
          <th>Statut</th>
          <th>Créé le</th>
          <th style="text-align:right">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($loans as $loan)
        <tr>

          {{-- Référence --}}
          <td>
            <a href="{{ route('admin.loans.show',$loan) }}" class="li-ref">
              {{ $loan->reference }}
            </a>
            @if($loan->contract_pdf_path)
            <span class="li-pdf-dot" title="PDF uploadé">
              <i class="fas fa-file-pdf"></i>
            </span>
            @endif
            @if($loan->archive_ref)
            <div style="font-size:.65rem;color:var(--c-muted);margin-top:.2rem;font-family:monospace">
              {{ $loan->archive_ref }}
            </div>
            @endif
          </td>

          {{-- Client --}}
          <td>
            <div style="display:flex;align-items:center;gap:.625rem">
              <div class="li-client-avatar">{{ strtoupper(substr($loan->name,0,1)) }}</div>
              <div>
                <div class="li-client-name">{{ $loan->name }}</div>
                <div class="li-client-sub">{{ $loan->email }}</div>
              </div>
            </div>
          </td>

          {{-- Admin (super-admin uniquement) --}}
          @if($isSuperAdmin)
          <td>
            @if($loan->admin)
            <div style="display:flex;align-items:center;gap:.4rem">
              <div style="width:26px;height:26px;border-radius:6px;background:linear-gradient(135deg,#4f46e5,#7c3aed);display:flex;align-items:center;justify-content:center;font-weight:800;font-size:.65rem;color:#fff;flex-shrink:0">
                {{ strtoupper(substr($loan->admin->name,0,1)) }}
              </div>
              <div>
                <div style="font-size:.78rem;font-weight:600;color:var(--c-navy)">{{ $loan->admin->name }}</div>
              </div>
            </div>
            @else
            <span style="font-size:.72rem;color:var(--c-muted)">—</span>
            @endif
          </td>
          @endif

          {{-- Financement --}}
          <td>
            <div class="li-amount">{{ number_format($loan->amount,0,',',' ') }} <span style="font-size:.72rem;font-weight:500;color:var(--c-muted)">{{ $loan->currency }}</span></div>
            <div class="li-amount-sub">
              <span style="background:#EFF6FF;color:#1d4ed8;font-size:.63rem;font-weight:700;padding:.1rem .35rem;border-radius:4px">{{ $loan->darly }} mois</span>
              <span style="margin-left:.3rem;color:#6b7280">{{ $loan->interest_rate }}%</span>
            </div>
          </td>

          {{-- Mensualité --}}
          <td>
            <div class="li-monthly">{{ number_format($loan->monthly_payment,2,',',' ') }}</div>
            <div class="li-monthly-sub">{{ $loan->currency }} / mois</div>
          </td>

          {{-- Statut --}}
          <td>
            <span class="badge-status bs-{{ $loan->statusColor() }}">{{ $loan->statusLabel() }}</span>
            @if($loan->sent_at)
            <div style="font-size:.65rem;color:var(--c-muted);margin-top:.2rem">
              Envoyé {{ $loan->sent_at->format('d/m/Y') }}
            </div>
            @endif
          </td>

          {{-- Date --}}
          <td>
            <div class="li-date">{{ $loan->created_at->format('d/m/Y') }}</div>
            <div class="li-date-rel">{{ $loan->created_at->diffForHumans() }}</div>
          </td>

          {{-- Actions --}}
          <td>
            <div class="li-act">
              <a href="{{ route('admin.loans.show',$loan) }}"
                 class="btn-icon btn-icon-primary" title="Voir le dossier">
                <i class="fas fa-eye"></i>
              </a>
              @if($loan->isEditable())
              <a href="{{ route('admin.loans.edit',$loan) }}"
                 class="btn-icon" title="Modifier">
                <i class="fas fa-pen"></i>
              </a>
              @endif
              <a href="{{ route('admin.loans.contract',$loan) }}"
                 class="btn-icon" title="Contrat">
                <i class="fas fa-file-contract"></i>
              </a>
              @if($loan->canBeValidated() && $loan->contract_pdf_path)
              <form action="{{ route('admin.loans.validate',$loan) }}" method="POST"
                    onsubmit="return confirm('Valider et envoyer le contrat à {{ $loan->email }} ?')">
                @csrf
                <button class="btn-icon btn-icon-success" title="Valider &amp; Envoyer">
                  <i class="fas fa-paper-plane"></i>
                </button>
              </form>
              @endif
            </div>
          </td>

        </tr>
        @empty
        <tr>
          <td colspan="{{ $isSuperAdmin ? 8 : 7 }}">
            <div class="li-empty">
              <i class="fas fa-folder-open li-empty-icon"></i>
              <div class="li-empty-title">Aucune demande trouvée</div>
              <div class="li-empty-sub">
                @if(request()->anyFilled(['search','status']))
                  Aucun résultat pour ces critères. <a href="{{ route('admin.loans.index') }}" style="color:var(--c-navy);font-weight:600">Effacer les filtres</a>
                @else
                  Aucune demande de prêt pour le moment.
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
  <div class="li-pagi">
    {{ $loans->appends(request()->query())->links() }}
  </div>
  @endif

</div>

@endsection
