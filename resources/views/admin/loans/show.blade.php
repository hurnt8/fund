@extends('layouts.dashboard')
@section('title', $loan->reference)
@section('page_title', 'Dossier — ' . $loan->reference)

@push('styles')
<style>
/* ── Hero ── */
.loan-hero{background:linear-gradient(135deg,var(--c-navy) 0%,var(--c-navy-2,#1a2d4d) 100%);border-radius:16px;padding:1.75rem 2rem;margin-bottom:1.5rem;position:relative;overflow:hidden}
.loan-hero::before{content:'';position:absolute;inset:0;background:url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");pointer-events:none}
.loan-hero-top{display:flex;align-items:flex-start;justify-content:space-between;gap:1rem;flex-wrap:wrap}
.loan-hero-ref{font-family:monospace;font-size:1.5rem;font-weight:900;color:#fff;letter-spacing:.04em;line-height:1}
.loan-hero-meta{font-size:.78rem;color:rgba(255,255,255,.45);margin-top:.35rem}
.loan-hero-client{display:flex;align-items:center;gap:.75rem;margin-top:1.25rem}
.loan-hero-avatar{width:44px;height:44px;border-radius:50%;background:rgba(255,255,255,.1);border:2px solid rgba(255,255,255,.15);display:flex;align-items:center;justify-content:center;font-weight:800;font-size:1.1rem;color:var(--c-gold,#f0c040);flex-shrink:0}
.loan-hero-name{font-size:.9375rem;font-weight:700;color:#fff}
.loan-hero-email{font-size:.75rem;color:rgba(255,255,255,.45)}
.loan-hero-actions{display:flex;gap:.5rem;flex-wrap:wrap;align-self:flex-start}

/* ── Progress steps ── */
.steps-wrap{background:var(--c-card,#fff);border:1px solid var(--c-border);border-radius:12px;padding:1.125rem 1.5rem;margin-bottom:1.5rem;overflow-x:auto}
.steps-bar{display:flex;align-items:center;min-width:max-content;gap:0}
.step-item{display:flex;flex-direction:column;align-items:center;position:relative;flex:1;min-width:80px}
.step-item:not(:last-child)::after{content:'';position:absolute;top:14px;left:calc(50% + 14px);right:calc(-50% + 14px);height:2px;background:var(--c-border)}
.step-item:not(:last-child).done::after,.step-item:not(:last-child).current::after{background:var(--c-navy)}
.step-dot{width:28px;height:28px;border-radius:50%;border:2px solid var(--c-border);background:var(--c-bg,#f8f9fa);color:var(--c-muted);font-size:.72rem;font-weight:700;display:flex;align-items:center;justify-content:center;position:relative;z-index:1;transition:all .2s}
.step-dot.done{background:var(--c-navy);border-color:var(--c-navy);color:#fff}
.step-dot.current{background:var(--c-gold,#f0c040);border-color:var(--c-gold,#f0c040);color:var(--c-navy);box-shadow:0 0 0 4px rgba(240,192,64,.18)}
.step-label{font-size:.68rem;color:var(--c-muted);margin-top:.35rem;font-weight:500;text-align:center;white-space:nowrap}
.step-label.done{color:var(--c-navy);font-weight:600}
.step-label.current{color:var(--c-navy);font-weight:700}

/* ── KPI cards ── */
.kpi-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:1rem;margin-bottom:1.5rem}
@media(max-width:768px){.kpi-grid{grid-template-columns:repeat(2,1fr)}}
.kpi-card{background:var(--c-card,#fff);border:1px solid var(--c-border);border-radius:12px;padding:1rem 1.125rem;display:flex;align-items:center;gap:.875rem}
.kpi-icon{width:40px;height:40px;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:.9rem}
.kpi-lbl{font-size:.7rem;color:var(--c-muted);margin-bottom:.2rem}
.kpi-val{font-size:1rem;font-weight:800;color:var(--c-navy);line-height:1.1}
.kpi-sub{font-size:.7rem;color:var(--c-muted);margin-top:.1rem}

/* ── Layout ── */
.loan-grid{display:grid;grid-template-columns:1fr 1.6fr;gap:1.25rem;margin-bottom:1.25rem}
@media(max-width:900px){.loan-grid{grid-template-columns:1fr}}

/* ── Actions card ── */
.actions-card{background:var(--c-card,#fff);border:1px solid var(--c-border);border-radius:12px;overflow:hidden;margin-bottom:1.25rem}
.actions-card-hdr{padding:.875rem 1.25rem;border-bottom:1px solid var(--c-border);display:flex;align-items:center;gap:.5rem}
.actions-card-hdr-title{font-size:.8125rem;font-weight:700;color:var(--c-navy)}
.actions-card-body{padding:1rem 1.25rem}
.action-btn-group{display:flex;flex-wrap:wrap;gap:.5rem;margin-bottom:1rem}
.action-btn-group:last-child{margin-bottom:0}
.action-divider{border:0;border-top:1px solid var(--c-border);margin:.75rem 0}
.status-form{display:flex;gap:.5rem;align-items:center}
.status-form select{flex:1}

/* ── Detail rows ── */
.detail-grid{display:grid;grid-template-columns:1fr 1fr;gap:.625rem}
@media(max-width:400px){.detail-grid{grid-template-columns:1fr}}
.detail-item{padding:.5rem .625rem;border-radius:8px;background:var(--c-bg,#f8f9fa)}
.detail-lbl{font-size:.68rem;color:var(--c-muted);margin-bottom:.15rem;font-weight:500;text-transform:uppercase;letter-spacing:.05em}
.detail-val{font-size:.8125rem;font-weight:600;color:var(--c-navy)}

/* ── Amortization toggle ── */
.amort-toggle-btn{background:none;border:none;color:var(--c-navy);font-size:.8rem;font-weight:600;cursor:pointer;display:flex;align-items:center;gap:.375rem;padding:0}
.amort-toggle-btn i{transition:transform .25s}
.amort-toggle-btn.open i{transform:rotate(180deg)}

/* ── History ── */
.history-item{display:flex;align-items:flex-start;gap:.875rem;padding:.875rem 1.25rem;border-bottom:1px solid var(--c-border)}
.history-item:last-child{border-bottom:0}
.history-icon{width:34px;height:34px;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:.75rem}
.history-action{font-size:.8125rem;font-weight:600;color:var(--c-navy)}
.history-meta{font-size:.73rem;color:var(--c-muted);margin-top:.15rem}

/* ── Alert banner ── */
.loan-rejected-banner{background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.2);border-left:4px solid #ef4444;border-radius:12px;padding:1rem 1.25rem;display:flex;align-items:center;gap:.75rem;margin-bottom:1.5rem;color:#dc2626;font-size:.875rem;font-weight:600}
</style>
@endpush

@section('content')

@php
$allSteps = [
    'draft'           => 'Brouillon',
    'pending'         => 'En attente',
    'validated'       => 'Validée',
    'contract_sent'   => 'Contrat envoyé',
    'contract_signed' => 'Signé',
    'finalized'       => 'Finalisée',
];
$stepKeys   = array_keys($allSteps);
$currentIdx = array_search($loan->status, $stepKeys);
$historyIconMap = [
    'created'           => ['fa-plus-circle', '#22c55e', 'rgba(34,197,94,.1)'],
    'updated'           => ['fa-pen', '#3b82f6', 'rgba(59,130,246,.1)'],
    'validated_and_sent'=> ['fa-paper-plane', '#14b8a6', 'rgba(20,184,166,.1)'],
    'signed_received'   => ['fa-file-signature', '#8b5cf6', 'rgba(139,92,246,.1)'],
    'status_changed'    => ['fa-exchange-alt', '#f59e0b', 'rgba(245,158,11,.1)'],
    'contract_edited'   => ['fa-edit', '#3b82f6', 'rgba(59,130,246,.1)'],
];
$historyLabels = [
    'created'           => 'Dossier créé',
    'updated'           => 'Dossier modifié',
    'validated_and_sent'=> 'Contrat validé et envoyé',
    'signed_received'   => 'Contrat signé reçu',
    'status_changed'    => 'Statut modifié',
    'contract_edited'   => 'Contrat édité',
];
@endphp

{{-- Hero --}}
<div class="loan-hero">
  <div class="loan-hero-top">
    <div>
      <div class="loan-hero-ref">{{ $loan->reference }}</div>
      <div class="loan-hero-meta">
        Créé le {{ $loan->created_at->format('d/m/Y') }}
        @if($loan->validated_at) · Validé le {{ $loan->validated_at->format('d/m/Y') }} @endif
        @if($loan->sent_at) · Contrat envoyé le {{ $loan->sent_at->format('d/m/Y') }} @endif
      </div>
    </div>
    <div class="loan-hero-actions">
      <a href="{{ route('admin.loans.index') }}" class="btn-ghost btn-sm-pro" style="border-color:rgba(255,255,255,.2);color:rgba(255,255,255,.7)">
        <i class="fas fa-arrow-left"></i> Retour
      </a>
      @if($loan->isEditable())
      <a href="{{ route('admin.loans.edit', $loan) }}" class="btn-ghost btn-sm-pro" style="border-color:rgba(255,255,255,.2);color:rgba(255,255,255,.7)">
        <i class="fas fa-pen"></i> Modifier
      </a>
      @endif
      <a href="{{ route('admin.loans.contract', $loan) }}" class="btn-ghost btn-sm-pro" style="border-color:rgba(255,255,255,.2);color:rgba(255,255,255,.7)">
        <i class="fas fa-file-contract"></i> Contrat
      </a>
    </div>
  </div>

  <div class="loan-hero-client">
    <div class="loan-hero-avatar">{{ strtoupper(substr($loan->name, 0, 1)) }}</div>
    <div>
      <div class="loan-hero-name">{{ $loan->name }}</div>
      <div class="loan-hero-email">{{ $loan->email }}@if($loan->phone) · {{ $loan->phone }}@endif</div>
    </div>
    <div style="margin-left:auto">
      <span class="badge-status bs-{{ $loan->statusColor() }}" style="font-size:.8rem;padding:.4rem .875rem">{{ $loan->statusLabel() }}</span>
    </div>
  </div>
</div>

{{-- Progression --}}
@if($loan->status !== 'rejected')
<div class="steps-wrap">
  <div class="steps-bar">
    @foreach($allSteps as $key => $label)
    @php
      $i    = array_search($key, $stepKeys);
      $done = $currentIdx !== false && $i <= $currentIdx;
      $cur  = $loan->status === $key;
    @endphp
    <div class="step-item {{ $done ? 'done' : '' }} {{ $cur ? 'current' : '' }}">
      <div class="step-dot {{ $cur ? 'current' : ($done ? 'done' : '') }}">
        @if($done && !$cur)<i class="fas fa-check" style="font-size:.6rem"></i>
        @else {{ $i + 1 }}
        @endif
      </div>
      <div class="step-label {{ $done ? 'done' : '' }} {{ $cur ? 'current' : '' }}">{{ $label }}</div>
    </div>
    @endforeach
  </div>
</div>
@else
<div class="loan-rejected-banner">
  <i class="fas fa-ban"></i>
  Cette demande a été <strong style="margin-left:.25rem">rejetée</strong>.
</div>
@endif

{{-- KPI Cards --}}
<div class="kpi-grid">
  <div class="kpi-card">
    <div class="kpi-icon" style="background:rgba(var(--c-navy-rgb,14,30,64),.08)">
      <i class="fas fa-coins" style="color:var(--c-gold,#f0c040)"></i>
    </div>
    <div>
      <div class="kpi-lbl">Montant accordé</div>
      <div class="kpi-val">{{ number_format($loan->amount, 0, ',', ' ') }}</div>
      <div class="kpi-sub">{{ $loan->currency }}</div>
    </div>
  </div>
  <div class="kpi-card">
    <div class="kpi-icon" style="background:rgba(59,130,246,.1)">
      <i class="fas fa-calendar-alt" style="color:#3b82f6"></i>
    </div>
    <div>
      <div class="kpi-lbl">Mensualité</div>
      <div class="kpi-val">{{ number_format($loan->monthly_payment, 2, ',', ' ') }}</div>
      <div class="kpi-sub">{{ $loan->currency }} / mois</div>
    </div>
  </div>
  <div class="kpi-card">
    <div class="kpi-icon" style="background:rgba(34,197,94,.1)">
      <i class="fas fa-chart-line" style="color:#22c55e"></i>
    </div>
    <div>
      <div class="kpi-lbl">Total à rembourser</div>
      <div class="kpi-val">{{ number_format($loan->total_with_interest, 0, ',', ' ') }}</div>
      <div class="kpi-sub">{{ $loan->currency }}</div>
    </div>
  </div>
  <div class="kpi-card">
    <div class="kpi-icon" style="background:rgba(245,158,11,.1)">
      <i class="fas fa-percent" style="color:#f59e0b"></i>
    </div>
    <div>
      <div class="kpi-lbl">Taux · Durée</div>
      <div class="kpi-val">{{ $loan->interest_rate }} %</div>
      <div class="kpi-sub">{{ $loan->darly }} mois</div>
    </div>
  </div>
</div>

{{-- Main grid: left = client + actions | right = details --}}
<div class="loan-grid">

  {{-- Colonne gauche --}}
  <div style="display:flex;flex-direction:column;gap:1.25rem">

    {{-- Client --}}
    <div class="card-pro">
      <div class="card-pro-hdr">
        <div class="card-pro-title"><span class="icon-dot"></span>Client</div>
        </div>
      <div class="card-pro-body">
        @foreach([
          ['fa-map-marker-alt', $loan->address ?? '—'],
          ['fa-language',       strtoupper($loan->contract_language ?? 'FR') . ' — ' . ($loan->currency ?? '—')],
          ['fa-birthday-cake',  $loan->client?->birth_date?->format('d/m/Y') ?? '—'],
          ['fa-id-card',        $loan->client?->id_type ? $loan->client->id_type . ' · ' . ($loan->client->id_number ?? '') : '—'],
        ] as [$ico, $val])
        <div class="d-flex align-items-start gap-2 mb-2">
          <i class="fas {{ $ico }}" style="color:var(--c-gold);width:14px;margin-top:.15rem;font-size:.78rem;flex-shrink:0"></i>
          <div style="font-size:.8125rem;color:var(--c-navy);font-weight:500">{{ $val }}</div>
        </div>
        @endforeach
        @if($loan->bank_account)
        <div class="d-flex align-items-start gap-2 mb-2">
          <i class="fas fa-university" style="color:var(--c-gold);width:14px;margin-top:.15rem;font-size:.78rem;flex-shrink:0"></i>
          <div style="font-size:.78rem;color:var(--c-muted);font-family:monospace">{{ $loan->bank_account }}</div>
        </div>
        @endif
      </div>
    </div>

    {{-- Actions --}}
    <div class="actions-card">
      <div class="actions-card-hdr">
        <i class="fas fa-bolt" style="color:var(--c-gold);font-size:.75rem"></i>
        <span class="actions-card-hdr-title">Actions</span>
      </div>
      <div class="actions-card-body">

        {{-- Boutons contextuels selon statut --}}
        <div class="action-btn-group">
          @if($loan->canBeValidated())
          <form action="{{ route('admin.loans.validate', $loan) }}" method="POST"
                onsubmit="return confirm('Valider et envoyer le contrat par email au client ?')">
            @csrf
            <button class="btn-navy btn-sm-pro">
              <i class="fas fa-paper-plane"></i> Valider &amp; Envoyer le contrat
            </button>
          </form>
          @endif

          @if($loan->status === 'contract_sent')
          <form action="{{ route('admin.loans.signed', $loan) }}" method="POST"
                onsubmit="return confirm('Confirmer la réception du contrat signé ?')">
            @csrf
            <button class="btn-navy btn-sm-pro" style="background:var(--c-green,#22c55e);border-color:var(--c-green,#22c55e)">
              <i class="fas fa-file-signature"></i> Contrat signé reçu
            </button>
          </form>
          @endif
        </div>

        <hr class="action-divider">

        {{-- Changer statut --}}
        <div style="font-size:.72rem;color:var(--c-muted);font-weight:600;text-transform:uppercase;letter-spacing:.06em;margin-bottom:.625rem">
          Modifier le statut
        </div>
        <form action="{{ route('admin.loans.status', $loan) }}" method="POST" class="status-form">
          @csrf @method('PATCH')
          <select name="status" class="form-control-pro">
            @foreach(\App\Models\LoanRequest::STATUSES as $s)
            <option value="{{ $s }}" {{ $loan->status === $s ? 'selected' : '' }}>
              {{ [
                'draft'           => 'Brouillon',
                'pending'         => 'En attente',
                'validated'       => 'Validée',
                'contract_sent'   => 'Contrat envoyé',
                'contract_signed' => 'Contrat signé',
                'finalized'       => 'Finalisée',
                'rejected'        => 'Rejetée',
              ][$s] ?? ucfirst($s) }}
            </option>
            @endforeach
          </select>
          <button type="submit" class="btn-navy" style="padding:.6rem .875rem;flex-shrink:0" title="Enregistrer le statut">
            <i class="fas fa-save"></i>
          </button>
        </form>

        @if($loan->status === 'draft')
        <p style="font-size:.72rem;color:var(--c-muted);margin-top:.625rem;margin-bottom:0">
          <i class="fas fa-info-circle"></i> Passer en <em>En attente</em> pour initier le processus de validation.
        </p>
        @elseif($loan->status === 'pending')
        <p style="font-size:.72rem;color:var(--c-muted);margin-top:.625rem;margin-bottom:0">
          <i class="fas fa-info-circle"></i> Passer en <em>Validée</em> pour envoyer un email de confirmation au client.
        </p>
        @elseif($loan->status === 'validated')
        <p style="font-size:.72rem;color:var(--c-muted);margin-top:.625rem;margin-bottom:0">
          <i class="fas fa-info-circle"></i> Cliquez <em>Valider &amp; Envoyer le contrat</em> pour envoyer le PDF au client.
        </p>
        @endif
      </div>
    </div>

  </div>

  {{-- Colonne droite --}}
  <div style="display:flex;flex-direction:column;gap:1.25rem">

    {{-- Paramètres du financement --}}
    <div class="card-pro">
      <div class="card-pro-hdr">
        <div class="card-pro-title"><span class="icon-dot"></span>Paramètres du financement</div>
      </div>
      <div class="card-pro-body">
        <div class="detail-grid">
          @foreach([
            ['Référence',       $loan->reference],
            ['Archive',         $loan->archive_ref ?? '—'],
            ['Date de début',   $loan->start_date?->format('d/m/Y') ?? '—'],
            ['Date validation', $loan->validated_at?->format('d/m/Y') ?? '—'],
            ['Contrat envoyé',  $loan->sent_at?->format('d/m/Y') ?? '—'],
            ['Contrat signé',   $loan->signed_received_at?->format('d/m/Y') ?? '—'],
            ['Taux d\'intérêt', $loan->interest_rate . ' %'],
            ['Frais admin.',    $loan->admin_fees ? number_format($loan->admin_fees, 2, ',', ' ') . ' ' . $loan->currency : '—'],
            ['Coût du crédit',  number_format($loan->total_cost, 2, ',', ' ') . ' ' . $loan->currency],
            ['Agent suivi',     $loan->agent_suivi ?? '—'],
          ] as [$lbl, $val])
          <div class="detail-item">
            <div class="detail-lbl">{{ $lbl }}</div>
            <div class="detail-val">{{ $val }}</div>
          </div>
          @endforeach
        </div>

        @if($loan->objet)
        <div style="margin-top:.875rem;padding:.625rem .75rem;background:var(--c-bg);border-radius:8px">
          <div class="detail-lbl">Objet</div>
          <div style="font-size:.8125rem;color:var(--c-navy);margin-top:.2rem">{{ $loan->objet }}</div>
        </div>
        @endif

        @if($loan->special_conditions)
        <div style="margin-top:.625rem;padding:.625rem .75rem;background:rgba(245,158,11,.06);border:1px solid rgba(245,158,11,.2);border-radius:8px">
          <div class="detail-lbl" style="color:#92400e">Conditions particulières</div>
          <div style="font-size:.8125rem;color:var(--c-navy);margin-top:.2rem">{{ $loan->special_conditions }}</div>
        </div>
        @endif
      </div>
    </div>

    {{-- Sujet / Notes --}}
    @if($loan->subject)
    <div class="card-pro">
      <div class="card-pro-hdr">
        <div class="card-pro-title"><span class="icon-dot"></span>Description du dossier</div>
      </div>
      <div class="card-pro-body">
        <p style="font-size:.8375rem;color:var(--c-text);line-height:1.6;margin:0">{{ $loan->subject }}</p>
      </div>
    </div>
    @endif

  </div>
</div>

{{-- Tableau d'amortissement --}}
@if($loan->amortization_schedule)
<div class="card-pro mb-4">
  <div class="card-pro-hdr">
    <div class="card-pro-title"><span class="icon-dot"></span>Tableau d'amortissement</div>
    <button type="button" class="amort-toggle-btn" id="amortToggle" onclick="toggleAmort()">
      <span id="amortToggleText">Afficher</span>
      <i class="fas fa-chevron-down"></i>
    </button>
  </div>
  <div id="amortBody" style="display:none;max-height:380px;overflow-y:auto">
    <table class="pro-table w-100">
      <thead style="position:sticky;top:0">
        <tr>
          <th>N°</th>
          <th>Mensualité</th>
          <th>Capital</th>
          <th>Intérêts</th>
          <th>Solde restant</th>
        </tr>
      </thead>
      <tbody>
        @foreach($loan->amortization_schedule as $row)
        <tr>
          <td style="color:var(--c-muted);font-size:.78rem">{{ $row['month'] }}</td>
          <td style="font-weight:600">{{ number_format($row['payment'], 2, ',', ' ') }} {{ $loan->currency }}</td>
          <td>{{ number_format($row['principal'], 2, ',', ' ') }} {{ $loan->currency }}</td>
          <td style="color:var(--c-red,#ef4444)">{{ number_format($row['interest'], 2, ',', ' ') }} {{ $loan->currency }}</td>
          <td style="color:var(--c-muted)">{{ number_format($row['balance'], 2, ',', ' ') }} {{ $loan->currency }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  <div class="card-pro-body" style="padding:.625rem 1.25rem;border-top:1px solid var(--c-border);background:var(--c-bg)">
    <span style="font-size:.75rem;color:var(--c-muted)">{{ count($loan->amortization_schedule) }} échéances · Total intérêts : {{ number_format($loan->total_cost, 2, ',', ' ') }} {{ $loan->currency }}</span>
  </div>
</div>
@endif

{{-- Historique --}}
@if($loan->history->count())
<div class="card-pro">
  <div class="card-pro-hdr">
    <div class="card-pro-title"><span class="icon-dot"></span>Journal d'activité</div>
    <span style="font-size:.75rem;color:var(--c-muted)">{{ $loan->history->count() }} entrées</span>
  </div>
  <div style="padding:0">
    @foreach($loan->history->sortByDesc('created_at') as $h)
    @php
      [$hIco, $hColor, $hBg] = $historyIconMap[$h->action] ?? ['fa-history', 'var(--c-muted)', 'var(--c-bg)'];
      $hLabel = $historyLabels[$h->action] ?? ucfirst(str_replace('_', ' ', $h->action));
    @endphp
    <div class="history-item">
      <div class="history-icon" style="background:{{ $hBg }};color:{{ $hColor }}">
        <i class="fas {{ $hIco }}"></i>
      </div>
      <div style="flex:1;min-width:0">
        <div class="history-action">{{ $hLabel }}</div>
        <div class="history-meta">
          {{ $h->admin?->name ?? 'Système' }} · {{ $h->created_at->format('d/m/Y à H:i') }}
          @if($h->new_value && isset($h->new_value['status']))
          · <span style="font-style:italic">→ {{ [
              'draft'           => 'Brouillon',
              'pending'         => 'En attente',
              'validated'       => 'Validée',
              'contract_sent'   => 'Contrat envoyé',
              'contract_signed' => 'Contrat signé',
              'finalized'       => 'Finalisée',
              'rejected'        => 'Rejetée',
            ][$h->new_value['status']] ?? $h->new_value['status'] }}</span>
          @endif
        </div>
      </div>
      <div style="font-size:.72rem;color:var(--c-muted);white-space:nowrap">
        {{ $h->created_at->diffForHumans() }}
      </div>
    </div>
    @endforeach
  </div>
</div>
@endif

@endsection

@push('scripts')
<script>
function toggleAmort() {
  const body = document.getElementById('amortBody');
  const btn  = document.getElementById('amortToggle');
  const txt  = document.getElementById('amortToggleText');
  const open = body.style.display === 'none';
  body.style.display = open ? 'block' : 'none';
  txt.textContent = open ? 'Masquer' : 'Afficher';
  btn.classList.toggle('open', open);
}
</script>
@endpush
