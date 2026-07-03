@extends('layouts.dashboard')
@section('title', $loan->reference)
@section('page_title', 'Dossier ' . $loan->reference)

@push('styles')
<style>
/* ─────────────────────────────────────────
   LOAN DETAIL — préfixe ld- (pas de conflit)
   ───────────────────────────────────────── */

/* ── Header compact ── */
.ld-header{background:#fff;border:1px solid var(--c-border);border-radius:14px;padding:1.25rem 1.5rem;margin-bottom:1.25rem;display:flex;align-items:center;gap:1rem;flex-wrap:wrap}
.ld-avatar{width:48px;height:48px;border-radius:12px;background:linear-gradient(135deg,var(--c-navy),#1a3a6c);display:flex;align-items:center;justify-content:center;font-weight:800;font-size:1.1rem;color:var(--c-gold);flex-shrink:0}
.ld-title-block{flex:1;min-width:0}
.ld-ref{font-family:monospace;font-size:1.1rem;font-weight:900;color:var(--c-navy);line-height:1}
.ld-sub{font-size:.78rem;color:var(--c-muted);margin-top:.2rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.ld-header-actions{display:flex;gap:.5rem;flex-wrap:wrap;flex-shrink:0}

/* ── KPI strip ── */
.ld-kpi-strip{display:grid;grid-template-columns:repeat(4,1fr);gap:1rem;margin-bottom:1.25rem}
@media(max-width:900px){.ld-kpi-strip{grid-template-columns:repeat(2,1fr)}}
.ld-kpi{background:#fff;border:1px solid var(--c-border);border-radius:12px;padding:.875rem 1.125rem;display:flex;gap:.875rem;align-items:center}
.ld-kpi-ico{width:38px;height:38px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:.85rem;flex-shrink:0}
.ld-kpi-lbl{font-size:.68rem;color:var(--c-muted);font-weight:600;text-transform:uppercase;letter-spacing:.04em}
.ld-kpi-val{font-size:1rem;font-weight:800;color:var(--c-navy);line-height:1.15;margin-top:.15rem}
.ld-kpi-sub{font-size:.68rem;color:var(--c-muted);margin-top:.1rem}

/* ── Stepper ── */
.ld-stepper{background:#fff;border:1px solid var(--c-border);border-radius:12px;padding:1rem 1.5rem;margin-bottom:1.25rem;overflow-x:auto}
.ld-steps{display:flex;align-items:center;min-width:max-content}
.ld-step{display:flex;flex-direction:column;align-items:center;position:relative;flex:1;min-width:90px}
.ld-step:not(:last-child)::after{content:'';position:absolute;top:15px;left:calc(50% + 16px);right:calc(-50% + 16px);height:2px;background:var(--c-border);z-index:0}
.ld-step.is-done:not(:last-child)::after{background:var(--c-navy)}
.ld-step-dot{width:30px;height:30px;border-radius:50%;border:2px solid var(--c-border);background:#f8f9fa;color:var(--c-muted);font-size:.7rem;font-weight:700;display:flex;align-items:center;justify-content:center;position:relative;z-index:1;transition:.2s}
.ld-step.is-done .ld-step-dot{background:var(--c-navy);border-color:var(--c-navy);color:#fff}
.ld-step.is-current .ld-step-dot{background:var(--c-gold);border-color:var(--c-gold);color:var(--c-navy);box-shadow:0 0 0 5px rgba(200,169,81,.15)}
.ld-step-lbl{font-size:.65rem;color:var(--c-muted);margin-top:.4rem;text-align:center;line-height:1.3;max-width:78px}
.ld-step.is-done .ld-step-lbl,.ld-step.is-current .ld-step-lbl{color:var(--c-navy);font-weight:700}

/* ── Rejected banner ── */
.ld-rejected{background:#FEF2F2;border:1px solid #FECACA;border-left:4px solid var(--c-red);border-radius:12px;padding:.875rem 1.125rem;display:flex;align-items:center;gap:.75rem;color:#991B1B;font-size:.85rem;font-weight:600;margin-bottom:1.25rem}

/* ── Main layout ── */
.ld-layout{display:grid;grid-template-columns:300px 1fr;gap:1.25rem;align-items:start}
@media(max-width:1100px){.ld-layout{grid-template-columns:1fr}}

/* ── Sticky panel ── */
.ld-panel{display:flex;flex-direction:column;gap:1rem;position:sticky;top:calc(var(--topbar-h,64px) + 1rem)}
@media(max-width:1100px){.ld-panel{position:static}}

/* ── Panel card ── */
.ld-pcard{background:#fff;border:1px solid var(--c-border);border-radius:12px;overflow:hidden}
.ld-pcard-hdr{padding:.7rem 1rem;border-bottom:1px solid var(--c-border);background:#fafbfc;display:flex;align-items:center;gap:.5rem}
.ld-pcard-ico{width:22px;height:22px;border-radius:6px;display:flex;align-items:center;justify-content:center;font-size:.65rem;flex-shrink:0}
.ld-pcard-title{font-size:.72rem;font-weight:800;color:var(--c-navy);text-transform:uppercase;letter-spacing:.06em;flex:1}
.ld-pcard-body{padding:.875rem 1rem;display:flex;flex-direction:column;gap:.625rem}

/* ── Info rows inside panel ── */
.ld-irow{display:flex;gap:.5rem;align-items:flex-start;padding:.35rem .5rem;border-radius:7px;transition:.15s}
.ld-irow:hover{background:#f8f9fa}
.ld-irow-ico{width:26px;height:26px;border-radius:6px;background:#f1f4f9;display:flex;align-items:center;justify-content:center;font-size:.63rem;flex-shrink:0;margin-top:.05rem}
.ld-irow-lbl{font-size:.62rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--c-muted);line-height:1}
.ld-irow-val{font-size:.8rem;font-weight:600;color:var(--c-navy);margin-top:.1rem;word-break:break-word}

/* ── Admin card ── */
.ld-admin-current{display:flex;align-items:center;gap:.625rem;padding:.625rem .75rem;background:rgba(124,58,237,.04);border:1px solid rgba(124,58,237,.15);border-radius:8px;margin-bottom:.5rem}
.ld-admin-avatar{width:34px;height:34px;border-radius:50%;background:rgba(124,58,237,.12);display:flex;align-items:center;justify-content:center;font-weight:800;font-size:.8rem;color:#7c3aed;flex-shrink:0}

/* ── Action buttons ── */
.ld-btn-full{width:100%;justify-content:center;font-size:.82rem}
.ld-warn-box{background:#FFFBEB;border:1px solid #FDE68A;border-left:3px solid #F59E0B;border-radius:8px;padding:.625rem .75rem;font-size:.78rem;color:#78350F;display:flex;gap:.5rem;align-items:flex-start}

/* ── Status select row ── */
.ld-status-row{display:flex;gap:.4rem}
.ld-status-row select{flex:1;font-size:.8rem}
.ld-status-row button{padding:.5rem .7rem;flex-shrink:0}

/* ── Divider ── */
.ld-divider{border:0;border-top:1px solid var(--c-border);margin:.25rem 0}

/* ── Tabs ── */
.ld-tabs{display:flex;border-bottom:2px solid var(--c-border);gap:.125rem;margin-bottom:1.25rem;overflow-x:auto}
.ld-tab-btn{padding:.625rem 1.125rem;font-size:.82rem;font-weight:600;color:var(--c-muted);border:none;background:none;border-bottom:2.5px solid transparent;margin-bottom:-2px;cursor:pointer;transition:.15s;white-space:nowrap;border-radius:6px 6px 0 0;display:flex;align-items:center;gap:.4rem;font-family:inherit}
.ld-tab-btn:hover{color:var(--c-navy);background:rgba(0,0,0,.025)}
.ld-tab-btn.active{color:var(--c-navy);border-bottom-color:var(--c-gold)}
.ld-tab-btn .ld-tab-badge{font-size:.6rem;padding:.1rem .4rem;border-radius:10px;background:#e5e7eb;color:#6b7280;font-weight:700}
.ld-tab-btn.active .ld-tab-badge{background:rgba(200,169,81,.15);color:var(--c-gold-d,#a88830)}
.ld-tab-pane{display:none}.ld-tab-pane.active{display:block}

/* ── Detail grid ── */
.ld-detail-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:.625rem}
@media(max-width:700px){.ld-detail-grid{grid-template-columns:1fr 1fr}}
.ld-detail-item{padding:.5rem .75rem;border-radius:8px;background:#f8f9fa;border:1px solid transparent;transition:.15s}
.ld-detail-item:hover{border-color:var(--c-border);background:#fff}
.ld-detail-lbl{font-size:.62rem;color:var(--c-muted);margin-bottom:.15rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em}
.ld-detail-val{font-size:.82rem;font-weight:600;color:var(--c-navy)}

/* ── Document card ── */
.ld-doc-grid{display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1rem}
@media(max-width:640px){.ld-doc-grid{grid-template-columns:1fr}}
.ld-pdf-file{display:flex;align-items:center;gap:.625rem;padding:.625rem .875rem;background:#FFF1F2;border:1px solid #FECDD3;border-radius:8px}
.ld-pdf-file-name{font-size:.8rem;font-weight:700;color:#9F1239;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;flex:1;min-width:0}
.ld-pdf-file-sub{font-size:.65rem;color:#be123c;margin-top:.1rem}

/* ── Upload zone ── */
.ld-upload-area{border:1.5px dashed var(--c-border);border-radius:8px;padding:.875rem;text-align:center;transition:.15s;cursor:pointer;background:#fafbfc}
.ld-upload-area:hover{border-color:var(--c-gold);background:#fffdf5}

/* ── History timeline ── */
.ld-timeline{padding:0 1.25rem}
.ld-tl-item{display:flex;gap:.875rem;padding:.875rem 0;position:relative}
.ld-tl-item:not(:last-child)::after{content:'';position:absolute;left:16px;top:54px;bottom:-4px;width:2px;background:var(--c-border)}
.ld-tl-dot{width:34px;height:34px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.72rem;flex-shrink:0;position:relative;z-index:1}
.ld-tl-action{font-size:.82rem;font-weight:700;color:var(--c-navy)}
.ld-tl-meta{font-size:.72rem;color:var(--c-muted);margin-top:.15rem}
.ld-tl-time{font-size:.68rem;color:var(--c-muted);white-space:nowrap;flex-shrink:0;margin-top:.2rem}

/* ── Amortization ── */
.ld-amort-wrap{max-height:360px;overflow-y:auto;overflow-x:auto;border-top:1px solid var(--c-border)}

/* ── DOCX table ── */
.ld-docx-table th,.ld-docx-table td{padding:.65rem .875rem;font-size:.78rem}

/* ─── Responsive Mobile ─── */
@media(max-width:640px){
  .ld-header-actions{width:100%}
  .ld-header-actions a,.ld-header-actions button{flex:1;justify-content:center}
}
@media(max-width:575px){
  .ld-kpi-strip{grid-template-columns:1fr 1fr}
  .ld-kpi{flex-direction:column;gap:.375rem;text-align:center;padding:.75rem}
  .ld-kpi-ico{margin:0 auto;width:32px;height:32px;font-size:.72rem}
  .ld-kpi-val{font-size:.925rem}
  .ld-kpi-sub{font-size:.6rem}
  .ld-stepper{padding:.75rem 1rem}
  .ld-step{min-width:68px}
  .ld-step-lbl{font-size:.58rem;max-width:58px}
  .ld-pcard-hdr{flex-wrap:wrap;gap:.375rem}
  .ld-status-row{flex-wrap:wrap}
  .ld-status-row select{min-width:0;width:100%}
  .ld-status-row button{width:100%;justify-content:center}
}
@media(max-width:400px){
  .ld-detail-grid{grid-template-columns:1fr}
  .ld-kpi-strip{grid-template-columns:1fr}
}
</style>
@endpush

@section('content')
@php
$statusLabels = [
    'draft'           => 'Brouillon',
    'pending'         => 'En attente',
    'validated'       => 'Validée',
    'contract_sent'   => 'Contrat envoyé',
    'contract_signed' => 'Contrat signé',
    'finalized'       => 'Finalisée',
    'rejected'        => 'Rejetée',
];
$allSteps  = ['draft'=>'Brouillon','pending'=>'En attente','validated'=>'Validée','contract_sent'=>'Contrat envoyé','contract_signed'=>'Contrat signé','finalized'=>'Finalisée'];
$stepKeys  = array_keys($allSteps);
$curIdx    = array_search($loan->status, $stepKeys);

$hIcoMap  = [
    'created'           => ['fa-plus-circle',   '#22c55e','rgba(34,197,94,.12)'],
    'updated'           => ['fa-pen',            '#3b82f6','rgba(59,130,246,.12)'],
    'validated_and_sent'=> ['fa-paper-plane',    '#14b8a6','rgba(20,184,166,.12)'],
    'signed_received'   => ['fa-file-signature', '#8b5cf6','rgba(139,92,246,.12)'],
    'status_changed'    => ['fa-exchange-alt',   '#f59e0b','rgba(245,158,11,.12)'],
    'contract_edited'   => ['fa-edit',           '#3b82f6','rgba(59,130,246,.12)'],
    'pdf_uploaded'      => ['fa-file-pdf',       '#dc2626','rgba(220,38,38,.12)'],
    'admin_assigned'    => ['fa-user-shield',    '#7c3aed','rgba(124,58,237,.12)'],
];
$hLblMap  = [
    'created'           => 'Dossier créé',
    'updated'           => 'Dossier modifié',
    'validated_and_sent'=> 'Contrat validé et envoyé',
    'signed_received'   => 'Contrat signé reçu',
    'status_changed'    => 'Statut modifié',
    'contract_edited'   => 'Contrat édité',
    'pdf_uploaded'      => 'PDF uploadé',
    'admin_assigned'    => 'Dossier réaffecté',
];
$tpl = $loan->contractTemplate;
@endphp

{{-- ── HEADER ── --}}
<div class="ld-header">
  <div class="ld-avatar">{{ strtoupper(substr($loan->name,0,1)) }}</div>

  <div class="ld-title-block">
    <div class="ld-ref">{{ $loan->reference }}</div>
    <div class="ld-sub">
      {{ $loan->name }}
      @if($loan->email) · {{ $loan->email }}@endif
      @if($loan->phone) · {{ $loan->phone }}@endif
    </div>
  </div>

  <span class="badge-status bs-{{ $loan->statusColor() }}" style="font-size:.75rem;padding:.35rem .875rem">
    {{ $loan->statusLabel() }}
  </span>

  <div class="ld-header-actions">
    <a href="{{ route('admin.loans.index') }}" class="btn-ghost btn-sm-pro">
      <i class="fas fa-arrow-left"></i> Retour
    </a>
    @if($loan->isEditable())
    <a href="{{ route('admin.loans.edit', $loan) }}" class="btn-ghost btn-sm-pro">
      <i class="fas fa-pen"></i> Modifier
    </a>
    @endif
    <a href="{{ route('admin.loans.contract', $loan) }}" class="btn-navy btn-sm-pro">
      <i class="fas fa-file-contract"></i> Contrat
    </a>
  </div>
</div>

{{-- ── KPI STRIP ── --}}
<div class="ld-kpi-strip">
  <div class="ld-kpi">
    <div class="ld-kpi-ico" style="background:#FEF9EC;color:var(--c-gold-d,#a88830)">
      <i class="fas fa-coins"></i>
    </div>
    <div>
      <div class="ld-kpi-lbl">Montant</div>
      <div class="ld-kpi-val">{{ number_format($loan->amount,0,',',' ') }}</div>
      <div class="ld-kpi-sub">{{ $loan->currency }}</div>
    </div>
  </div>
  <div class="ld-kpi">
    <div class="ld-kpi-ico" style="background:#EFF6FF;color:var(--c-blue,#2563eb)">
      <i class="fas fa-calendar-check"></i>
    </div>
    <div>
      <div class="ld-kpi-lbl">Mensualité</div>
      <div class="ld-kpi-val">{{ number_format($loan->monthly_payment,2,',',' ') }}</div>
      <div class="ld-kpi-sub">{{ $loan->currency }} / mois</div>
    </div>
  </div>
  <div class="ld-kpi">
    <div class="ld-kpi-ico" style="background:#ECFDF5;color:var(--c-green,#059669)">
      <i class="fas fa-chart-line"></i>
    </div>
    <div>
      <div class="ld-kpi-lbl">Total à rembourser</div>
      <div class="ld-kpi-val">{{ number_format($loan->total_with_interest,0,',',' ') }}</div>
      <div class="ld-kpi-sub">{{ $loan->currency }}</div>
    </div>
  </div>
  <div class="ld-kpi">
    <div class="ld-kpi-ico" style="background:#FEF3C7;color:var(--c-amber,#d97706)">
      <i class="fas fa-percent"></i>
    </div>
    <div>
      <div class="ld-kpi-lbl">Taux / Durée</div>
      <div class="ld-kpi-val">{{ $loan->interest_rate }} %</div>
      <div class="ld-kpi-sub">{{ $loan->darly }} mois</div>
    </div>
  </div>
</div>

{{-- ── STEPPER / REJETÉ ── --}}
@if($loan->status === 'rejected')
<div class="ld-rejected">
  <i class="fas fa-ban"></i>
  Cette demande a été <strong style="margin-left:.3rem">rejetée</strong>.
</div>
@else
<div class="ld-stepper">
  <div class="ld-steps">
    @foreach($allSteps as $key => $label)
    @php $i = array_search($key,$stepKeys); $done = $curIdx!==false && $i<=$curIdx; $cur = $loan->status===$key; @endphp
    <div class="ld-step {{ $done?'is-done':'' }} {{ $cur?'is-current':'' }}">
      <div class="ld-step-dot">
        @if($done && !$cur)<i class="fas fa-check" style="font-size:.55rem"></i>@else{{ $i+1 }}@endif
      </div>
      <div class="ld-step-lbl">{{ $label }}</div>
    </div>
    @endforeach
  </div>
</div>
@endif

{{-- ── LAYOUT PRINCIPAL ── --}}
<div class="ld-layout">

  {{-- ════ PANEL GAUCHE (sticky) ════ --}}
  <div class="ld-panel">

    {{-- Client --}}
    <div class="ld-pcard">
      <div class="ld-pcard-hdr">
        <div class="ld-pcard-ico" style="background:#FEF9EC;color:var(--c-gold-d)"><i class="fas fa-user"></i></div>
        <span class="ld-pcard-title">Client</span>
      </div>
      <div class="ld-pcard-body">
        @foreach([
          ['fa-envelope',       '#3b82f6', 'Email',              $loan->email],
          ['fa-phone',          '#22c55e', 'Téléphone',          $loan->phone ?? '—'],
          ['fa-map-marker-alt', '#ef4444', 'Adresse',            $loan->address ?? '—'],
          ['fa-language',       '#8b5cf6', 'Langue / Devise',    strtoupper($loan->contract_language ?? 'FR').' — '.($loan->currency ?? '—')],
        ] as [$ico,$col,$lbl,$val])
        <div class="ld-irow">
          <div class="ld-irow-ico"><i class="fas {{ $ico }}" style="color:{{ $col }}"></i></div>
          <div>
            <div class="ld-irow-lbl">{{ $lbl }}</div>
            <div class="ld-irow-val">{{ $val }}</div>
          </div>
        </div>
        @endforeach
        @if($loan->client?->birth_date)
        <div class="ld-irow">
          <div class="ld-irow-ico"><i class="fas fa-birthday-cake" style="color:#f59e0b"></i></div>
          <div>
            <div class="ld-irow-lbl">Né(e) le</div>
            <div class="ld-irow-val">{{ $loan->client->birth_date->format('d/m/Y') }}</div>
          </div>
        </div>
        @endif
        @if($loan->bank_account)
        <div class="ld-irow">
          <div class="ld-irow-ico"><i class="fas fa-university" style="color:var(--c-gold)"></i></div>
          <div>
            <div class="ld-irow-lbl">Compte bancaire</div>
            <div class="ld-irow-val" style="font-family:monospace;font-size:.74rem">{{ $loan->bank_account }}</div>
          </div>
        </div>
        @endif
      </div>
    </div>

    {{-- Admin assigné (super-admin seulement) --}}
    @if($isSuperAdmin)
    <div class="ld-pcard" style="border-color:rgba(124,58,237,.25)">
      <div class="ld-pcard-hdr" style="background:rgba(124,58,237,.04);border-bottom-color:rgba(124,58,237,.15)">
        <div class="ld-pcard-ico" style="background:rgba(124,58,237,.12);color:#7c3aed"><i class="fas fa-user-shield"></i></div>
        <span class="ld-pcard-title" style="color:#7c3aed">Admin assigné</span>
        <span style="font-size:.62rem;padding:.1rem .45rem;border-radius:20px;background:rgba(124,58,237,.1);color:#7c3aed;font-weight:800;border:1px solid rgba(124,58,237,.2)">SUPER ADMIN</span>
      </div>
      <div class="ld-pcard-body">
        <div class="ld-admin-current">
          <div class="ld-admin-avatar">{{ strtoupper(substr($loan->admin?->name ?? 'A',0,1)) }}</div>
          <div>
            <div style="font-size:.82rem;font-weight:700;color:var(--c-navy)">{{ $loan->admin?->name ?? 'Non assigné' }}</div>
            <div style="font-size:.68rem;color:var(--c-muted)">Responsable actuel</div>
          </div>
        </div>
        <form action="{{ route('admin.loans.assign-admin', $loan) }}" method="POST">
          @csrf @method('PATCH')
          <div style="font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--c-muted);margin-bottom:.35rem">Réaffecter à</div>
          <div class="ld-status-row">
            <select name="admin_id" class="form-control-pro">
              <option value="">— Choisir un admin</option>
              @foreach($admins as $adm)
              <option value="{{ $adm->id }}" {{ $loan->admin_id==$adm->id?'selected':'' }}>{{ $adm->name }}</option>
              @endforeach
            </select>
            <button type="submit" class="btn-navy" style="background:#7c3aed;border-color:#7c3aed"
                    onclick="return confirm('Confirmer la réaffectation ?')" title="Confirmer">
              <i class="fas fa-check"></i>
            </button>
          </div>
        </form>
      </div>
    </div>
    @endif

    {{-- Actions --}}
    <div class="ld-pcard">
      <div class="ld-pcard-hdr">
        <div class="ld-pcard-ico" style="background:#FEF9EC;color:var(--c-gold-d)"><i class="fas fa-bolt"></i></div>
        <span class="ld-pcard-title">Actions</span>
      </div>
      <div class="ld-pcard-body">

        @if($loan->canBeValidated())
          @if($loan->contract_pdf_path)
          <form action="{{ route('admin.loans.validate', $loan) }}" method="POST"
                onsubmit="return confirm('Valider et envoyer le contrat par email ?')">
            @csrf
            <button class="btn-navy ld-btn-full">
              <i class="fas fa-paper-plane"></i> Valider &amp; Envoyer le contrat
            </button>
          </form>
          @else
          <div class="ld-warn-box">
            <i class="fas fa-lock" style="color:#F59E0B;flex-shrink:0;margin-top:.1rem"></i>
            <span>Uploadez d'abord le <strong>PDF du contrat</strong> (onglet Documents) pour débloquer la validation.</span>
          </div>
          @endif
        @endif

        @if($loan->status === 'contract_sent')
        <form action="{{ route('admin.loans.signed', $loan) }}" method="POST"
              onsubmit="return confirm('Confirmer la réception du contrat signé ?')">
          @csrf
          <button class="btn-navy ld-btn-full" style="background:var(--c-green);border-color:var(--c-green)">
            <i class="fas fa-file-signature"></i> Contrat signé reçu
          </button>
        </form>
        @endif

        @if($loan->status === 'contract_sent')
        <form action="{{ route('admin.loans.contract.pdf.resend',$loan) }}" method="POST"
              onsubmit="return confirm('Renvoyer le contrat à {{ $loan->email }} ?')">
          @csrf
          <button type="submit" class="btn-ghost btn-sm-pro ld-btn-full">
            <i class="fas fa-redo" style="color:#dc2626"></i> Renvoyer l'email
          </button>
        </form>
        @endif

        <hr class="ld-divider">

        {{-- ── Assurance ── --}}
        <div style="font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#16a34a;margin-bottom:.4rem;display:flex;align-items:center;gap:.35rem">
          <i class="fas fa-shield-alt" style="font-size:.65rem"></i> Assurance emprunteur
        </div>

        @if($loan->insuranceTemplate?->hasDocxTemplate())
        {{-- Template DOCX → télécharger DOCX --}}
        <a href="{{ route('admin.loans.insurance.docx', $loan) }}"
           class="btn-navy ld-btn-full" style="background:#16a34a;border-color:#16a34a;display:flex;align-items:center;justify-content:center;gap:.45rem;text-decoration:none">
          <i class="fas fa-file-word"></i> Télécharger DOCX assurance
        </a>
        @else
        {{-- Template intégré ou HTML → générer PDF --}}
        <form action="{{ route('admin.loans.insurance.pdf.generate', $loan) }}" method="POST"
              onsubmit="return confirm('Générer l\'attestation d\'assurance ?')">
          @csrf
          <button type="submit" class="btn-navy ld-btn-full" style="background:#16a34a;border-color:#16a34a">
            <i class="fas fa-magic"></i> {{ $loan->insurance_pdf_path ? 'Régénérer l\'attestation' : 'Générer l\'attestation' }}
          </button>
        </form>
        @endif

        @if($loan->insurance_pdf_path)
        <form action="{{ route('admin.loans.insurance.send', $loan) }}" method="POST"
              onsubmit="return confirm('Envoyer l\'attestation par email à {{ $loan->email }} ?')">
          @csrf
          <button type="submit" class="btn-navy ld-btn-full" style="background:#059669;border-color:#059669;margin-top:.4rem">
            <i class="fas fa-paper-plane"></i> Envoyer par email
          </button>
        </form>
        @endif

        <hr class="ld-divider">

        <div style="font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--c-muted);margin-bottom:.35rem">Changer le statut</div>
        <form action="{{ route('admin.loans.status', $loan) }}" method="POST">
          @csrf @method('PATCH')
          <div class="ld-status-row">
            <select name="status" class="form-control-pro">
              @foreach(\App\Models\LoanRequest::STATUSES as $s)
              <option value="{{ $s }}" {{ $loan->status===$s?'selected':'' }}>
                {{ $statusLabels[$s] ?? ucfirst($s) }}
              </option>
              @endforeach
            </select>
            <button type="submit" class="btn-navy" title="Enregistrer"><i class="fas fa-save"></i></button>
          </div>
        </form>

      </div>
    </div>

  </div>{{-- /ld-panel --}}

  {{-- ════ CONTENU PRINCIPAL avec TABS ════ --}}
  <div style="min-width:0">

    {{-- Onglets --}}
    <div class="ld-tabs" id="ldTabs">
      <button class="ld-tab-btn active" onclick="ldTab(this,'tab-details')">
        <i class="fas fa-info-circle"></i> Détails du dossier
      </button>
      <button class="ld-tab-btn" onclick="ldTab(this,'tab-docs')">
        <i class="fas fa-folder-open"></i> Documents
        @if($loan->contract_pdf_path)
        <span class="ld-tab-badge"><i class="fas fa-check"></i></span>
        @else
        <span class="ld-tab-badge" style="background:#FEF3C7;color:#92400E">!</span>
        @endif
      </button>
      <button class="ld-tab-btn" onclick="ldTab(this,'tab-history')">
        <i class="fas fa-history"></i> Historique
        @if($loan->history->count())
        <span class="ld-tab-badge">{{ $loan->history->count() }}</span>
        @endif
      </button>
      @if($loan->amortization_schedule)
      <button class="ld-tab-btn" onclick="ldTab(this,'tab-amort')">
        <i class="fas fa-table"></i> Amortissement
        <span class="ld-tab-badge">{{ count($loan->amortization_schedule) }}</span>
      </button>
      @endif
    </div>

    {{-- ── TAB : DÉTAILS ── --}}
    <div class="ld-tab-pane active" id="tab-details">
      <div class="card-pro">
        <div class="card-pro-hdr">
          <div class="card-pro-title"><span class="icon-dot"></span>Paramètres du financement</div>
        </div>
        <div class="card-pro-body">
          <div class="ld-detail-grid">
            @foreach([
              ['Référence',        $loan->reference],
              ['N° Archive',       $loan->archive_ref ?? '—'],
              ['Date de début',    $loan->start_date?->format('d/m/Y') ?? '—'],
              ['Date validation',  $loan->validated_at?->format('d/m/Y') ?? '—'],
              ['Contrat envoyé',   $loan->sent_at?->format('d/m/Y') ?? '—'],
              ['Contrat signé',    $loan->signed_received_at?->format('d/m/Y') ?? '—'],
              ['Taux d\'intérêt',  $loan->interest_rate.' %'],
              ['Frais admin.',     $loan->admin_fees ? number_format($loan->admin_fees,2,',',' ').' '.$loan->currency : '—'],
              ['Coût du crédit',   number_format($loan->total_cost,2,',',' ').' '.$loan->currency],
              ['Agent suivi',      $loan->agent_suivi ?? '—'],
              ['Directeur',        $loan->directeur ?? '—'],
              ['Langue contrat',   strtoupper($loan->contract_language ?? 'FR')],
            ] as [$lbl,$val])
            <div class="ld-detail-item">
              <div class="ld-detail-lbl">{{ $lbl }}</div>
              <div class="ld-detail-val">{{ $val }}</div>
            </div>
            @endforeach
          </div>

          @if($loan->objet || $loan->special_conditions || $loan->subject)
          <div style="margin-top:1rem;display:flex;flex-direction:column;gap:.5rem">
            @if($loan->objet)
            <div style="padding:.625rem .875rem;background:#f8f9fa;border-radius:8px">
              <div class="ld-detail-lbl">Objet du prêt</div>
              <div style="font-size:.82rem;color:var(--c-navy);margin-top:.15rem;line-height:1.6">{{ $loan->objet }}</div>
            </div>
            @endif
            @if($loan->special_conditions)
            <div style="padding:.625rem .875rem;background:#FFFBEB;border:1px solid #FDE68A;border-radius:8px">
              <div class="ld-detail-lbl" style="color:#92400E">Conditions particulières</div>
              <div style="font-size:.82rem;color:var(--c-navy);margin-top:.15rem;line-height:1.6">{{ $loan->special_conditions }}</div>
            </div>
            @endif
            @if($loan->subject)
            <div style="padding:.625rem .875rem;background:#f8f9fa;border-radius:8px">
              <div class="ld-detail-lbl">Description</div>
              <div style="font-size:.82rem;color:var(--c-text);margin-top:.15rem;line-height:1.65">{{ $loan->subject }}</div>
            </div>
            @endif
          </div>
          @endif
        </div>
      </div>
    </div>

    {{-- ── TAB : DOCUMENTS ── --}}
    <div class="ld-tab-pane" id="tab-docs">

      <div class="ld-doc-grid">

        {{-- PDF du contrat --}}
        <div class="card-pro">
          <div class="card-pro-hdr">
            <div class="card-pro-title">
              <span class="icon-dot" style="background:#dc2626"></span>PDF du contrat signé
            </div>
            @if($loan->contract_pdf_path)
            <a href="{{ route('admin.loans.contract.pdf',$loan) }}" class="btn-ghost btn-sm-pro" target="_blank">
              <i class="fas fa-eye"></i>
            </a>
            @endif
          </div>
          <div class="card-pro-body" style="display:flex;flex-direction:column;gap:.875rem">

            @if($loan->contract_pdf_path)
            <div class="ld-pdf-file">
              <i class="fas fa-file-pdf" style="color:#dc2626;font-size:1.3rem;flex-shrink:0"></i>
              <div style="flex:1;min-width:0">
                <div class="ld-pdf-file-name">{{ $loan->reference }}.pdf</div>
                <div class="ld-pdf-file-sub"><i class="fas fa-check-circle"></i> Joint à l'email de validation</div>
              </div>
              <a href="{{ route('admin.loans.contract.pdf',$loan) }}" class="btn-ghost btn-sm-pro" style="padding:.3rem .5rem" target="_blank" title="Télécharger">
                <i class="fas fa-download"></i>
              </a>
            </div>
            @else
            <div class="ld-warn-box">
              <i class="fas fa-exclamation-triangle" style="color:#F59E0B;flex-shrink:0"></i>
              <span><strong>Aucun PDF.</strong> Uploadez le contrat pour débloquer la validation.</span>
            </div>
            @endif

            <form action="{{ route('admin.loans.contract.pdf.upload',$loan) }}" method="POST" enctype="multipart/form-data">
              @csrf
              <div class="ld-upload-area" onclick="this.querySelector('input').click()">
                <i class="fas fa-cloud-upload-alt" style="color:var(--c-gold);font-size:1.5rem;display:block;margin-bottom:.4rem"></i>
                <div style="font-size:.78rem;font-weight:600;color:var(--c-navy)">{{ $loan->contract_pdf_path ? 'Remplacer le PDF' : 'Uploader le contrat PDF' }}</div>
                <div style="font-size:.68rem;color:var(--c-muted);margin-top:.2rem">PDF · max 20 Mo</div>
                <input type="file" name="contract_pdf" accept=".pdf" required
                       style="display:none" onchange="this.closest('form').submit()">
              </div>
              @error('contract_pdf')
              <div style="font-size:.72rem;color:#dc2626;margin-top:.3rem"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
              @enderror
            </form>

          </div>
        </div>

        {{-- Modèle de contrat --}}
        <div class="card-pro">
          <div class="card-pro-hdr">
            <div class="card-pro-title"><span class="icon-dot"></span>Modèle DOCX</div>
            @if($tpl)
            <a href="{{ route('admin.contract-templates.edit',$tpl) }}" class="btn-ghost btn-sm-pro">
              <i class="fas fa-pen"></i>
            </a>
            @endif
          </div>
          <div class="card-pro-body">
            @if($tpl)
            <div style="font-weight:700;color:var(--c-navy);font-size:.875rem;margin-bottom:.5rem">{{ $tpl->name }}</div>
            <div style="display:flex;gap:.3rem;flex-wrap:wrap;margin-bottom:1rem">
              @if($tpl->is_default)
              <span class="badge-status bs-amber" style="font-size:.6rem">Par défaut</span>
              @endif
              @if($tpl->hasDocxTemplate())
              <span style="font-size:.62rem;padding:.15rem .45rem;border-radius:5px;background:#ECFDF5;color:#166534;border:1px solid #86EFAC;font-weight:700">
                <i class="fas fa-file-word"></i> DOCX v{{ $tpl->docx_version }}
              </span>
              @else
              <span style="font-size:.62rem;padding:.15rem .45rem;border-radius:5px;background:#FEF9C3;color:#713F12;border:1px solid #FDE047">Pas de DOCX</span>
              @endif
            </div>
            @if($tpl->hasDocxTemplate())
            <a href="{{ route('admin.loans.contract.docx',$loan) }}" class="btn-navy btn-sm-pro ld-btn-full">
              <i class="fas fa-file-word"></i> Générer &amp; Télécharger DOCX
            </a>
            @else
            <a href="{{ route('admin.contract-templates.edit',$tpl) }}" class="btn-ghost btn-sm-pro ld-btn-full">
              <i class="fas fa-upload"></i> Uploader un template DOCX
            </a>
            @endif
            @if($tpl->hasDocxTemplate() && count($tpl->docx_detected_vars ?? []) > 0)
            <div style="margin-top:.75rem;padding:.5rem .625rem;background:#f8f9fa;border:1px solid var(--c-border);border-radius:7px">
              <div style="font-size:.66rem;color:var(--c-muted);margin-bottom:.35rem"><i class="fas fa-tags" style="color:var(--c-gold)"></i> {{ count($tpl->docx_detected_vars) }} variable(s)</div>
              <div style="display:flex;flex-wrap:wrap;gap:.2rem">
                @foreach($tpl->docx_detected_vars as $v)
                <code style="font-size:.6rem;padding:.05rem .25rem;border-radius:3px;background:#fff;border:1px solid var(--c-border);color:var(--c-navy)">{{"{"}}{{ $v }}{{"}"}}</code>
                @endforeach
              </div>
            </div>
            @endif
            @else
            <div class="ld-warn-box">
              <i class="fas fa-exclamation-triangle" style="color:#F59E0B;flex-shrink:0"></i>
              <span>Aucun modèle assigné. <a href="{{ route('admin.loans.edit',$loan) }}" style="color:#78350F;font-weight:700">Assigner →</a></span>
            </div>
            @endif
          </div>
        </div>

      </div>{{-- /ld-doc-grid --}}

      {{-- ── SECTION ASSURANCE EMPRUNTEUR ── --}}
      <div style="display:flex;align-items:center;gap:.75rem;margin:1.75rem 0 1rem">
        <div style="display:flex;align-items:center;gap:.5rem;background:#F0FDF4;border:1px solid #A7F3D0;border-radius:8px;padding:.35rem .75rem;flex-shrink:0">
          <i class="fas fa-shield-alt" style="color:#16a34a;font-size:.8rem"></i>
          <span style="font-size:.72rem;font-weight:800;text-transform:uppercase;letter-spacing:.08em;color:#065F46">Assurance emprunteur · CG-A340G</span>
        </div>
        <div style="flex:1;height:1.5px;background:linear-gradient(to right,#A7F3D0,transparent)"></div>
      </div>

      <div class="ld-doc-grid">

        {{-- Attestation d'assurance --}}
        <div class="card-pro">
          <div class="card-pro-hdr">
            <div class="card-pro-title">
              <span class="icon-dot" style="background:#16a34a"></span>Attestation d'assurance PDF
            </div>
            @if($loan->insurance_pdf_path)
            <a href="{{ route('admin.loans.insurance.pdf',$loan) }}" class="btn-ghost btn-sm-pro" target="_blank">
              <i class="fas fa-eye"></i>
            </a>
            @endif
          </div>
          <div class="card-pro-body" style="display:flex;flex-direction:column;gap:.875rem">

            @if($loan->insurance_pdf_path)
            <div class="ld-pdf-file" style="background:#F0FDF4;border:1px solid #A7F3D0">
              <i class="fas fa-shield-alt" style="color:#16a34a;font-size:1.3rem;flex-shrink:0"></i>
              <div style="flex:1;min-width:0">
                <div class="ld-pdf-file-name" style="color:#065F46">{{ $loan->reference }}_assurance.pdf</div>
                <div class="ld-pdf-file-sub" style="color:#047857"><i class="fas fa-check-circle"></i> Attestation CG-A340G disponible</div>
              </div>
              <a href="{{ route('admin.loans.insurance.pdf',$loan) }}" class="btn-ghost btn-sm-pro" style="padding:.3rem .5rem" target="_blank">
                <i class="fas fa-download"></i>
              </a>
            </div>
            @else
            <div class="ld-warn-box" style="background:#F0FDF4;border-color:#A7F3D0">
              <i class="fas fa-shield-alt" style="color:#16a34a;flex-shrink:0"></i>
              <span style="color:#065F46">Aucune attestation. Cliquez sur <strong>Générer</strong> pour créer le PDF.</span>
            </div>
            @endif

            {{-- Générer : DOCX si template DOCX, sinon PDF intégré --}}
            @if($loan->insuranceTemplate?->hasDocxTemplate())
            <a href="{{ route('admin.loans.insurance.docx', $loan) }}"
               class="btn-navy btn-sm-pro ld-btn-full" style="background:#16a34a;border-color:#16a34a;display:flex;align-items:center;justify-content:center;gap:.4rem;text-decoration:none">
              <i class="fas fa-file-word"></i> Télécharger DOCX assurance
            </a>
            <div style="font-size:.68rem;color:var(--c-muted);margin-top:.35rem;display:flex;align-items:flex-start;gap:.3rem">
              <i class="fas fa-info-circle" style="flex-shrink:0;margin-top:.1rem;color:#16a34a"></i>
              <span>Ouvrez le DOCX, finalisez-le, exportez en PDF, puis uploadez ci-dessous.</span>
            </div>
            @else
            <form action="{{ route('admin.loans.insurance.pdf.generate', $loan) }}" method="POST"
                  onsubmit="return confirm('Générer l\'attestation d\'assurance pour ce dossier ?')">
              @csrf
              <button type="submit" class="btn-navy btn-sm-pro ld-btn-full" style="background:#16a34a;border-color:#16a34a">
                <i class="fas fa-magic"></i> {{ $loan->insurance_pdf_path ? 'Régénérer l\'attestation' : 'Générer l\'attestation PDF' }}
              </button>
            </form>
            @endif

            {{-- Upload manuel --}}
            <form action="{{ route('admin.loans.insurance.pdf.upload',$loan) }}" method="POST" enctype="multipart/form-data">
              @csrf
              <div class="ld-upload-area" onclick="this.querySelector('input').click()"
                   style="{{ $loan->insurance_pdf_path ? 'border-color:#A7F3D0' : '' }}">
                <i class="fas fa-cloud-upload-alt" style="color:#16a34a;font-size:1.5rem;display:block;margin-bottom:.4rem"></i>
                <div style="font-size:.78rem;font-weight:600;color:var(--c-navy)">{{ $loan->insurance_pdf_path ? 'Remplacer le PDF' : 'Uploader un PDF existant' }}</div>
                <div style="font-size:.68rem;color:var(--c-muted);margin-top:.2rem">PDF · max 20 Mo · CG-A340G</div>
                <input type="file" name="insurance_pdf" accept=".pdf" required
                       style="display:none" onchange="this.closest('form').submit()">
              </div>
              @error('insurance_pdf')
              <div style="font-size:.72rem;color:#dc2626;margin-top:.3rem"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
              @enderror
            </form>

          </div>
        </div>

        {{-- Modèle + envoi email --}}
        <div class="card-pro">
          <div class="card-pro-hdr">
            <div class="card-pro-title"><span class="icon-dot" style="background:#16a34a"></span>Modèle &amp; envoi</div>
            <a href="{{ route('admin.loans.edit',$loan) }}" class="btn-ghost btn-sm-pro">
              <i class="fas fa-pen"></i>
            </a>
          </div>
          <div class="card-pro-body">

            @if($loan->insuranceTemplate)
            <div style="font-weight:700;color:var(--c-navy);font-size:.875rem;margin-bottom:.5rem">{{ $loan->insuranceTemplate->name }}</div>
            <div style="display:flex;gap:.3rem;flex-wrap:wrap;margin-bottom:1rem">
              <span class="badge-status" style="background:#F0FDF4;color:#15803d;border:1px solid #A7F3D0;font-size:.6rem">Modèle personnalisé</span>
            </div>
            @else
            <div style="font-weight:700;color:var(--c-navy);font-size:.875rem;margin-bottom:.25rem">Attestation CG-A340G</div>
            <div style="font-size:.72rem;color:var(--c-muted);margin-bottom:1rem">Modèle intégré (par défaut)</div>
            @endif

            @if($loan->frais_assurance || $loan->date_fin_assurance)
            <div style="padding:.625rem .75rem;background:#F0FDF4;border:1px solid #A7F3D0;border-radius:8px;margin-bottom:1rem">
              @if($loan->frais_assurance)
              <div style="display:flex;justify-content:space-between;padding:.2rem 0;font-size:.8rem">
                <span style="color:var(--c-muted)">Frais d'assurance</span>
                <span style="font-weight:700;color:#065F46">{{ number_format($loan->frais_assurance,2,',',' ') }} {{ $loan->currency }}</span>
              </div>
              @endif
              @if($loan->date_fin_assurance)
              <div style="display:flex;justify-content:space-between;padding:.2rem 0;font-size:.8rem">
                <span style="color:var(--c-muted)">Date de fin</span>
                <span style="font-weight:700;color:#065F46">{{ $loan->date_fin_assurance->format('d/m/Y') }}</span>
              </div>
              @endif
            </div>
            @endif

            {{-- Envoyer par email --}}
            @if($loan->insurance_pdf_path)
            <form action="{{ route('admin.loans.insurance.send', $loan) }}" method="POST"
                  onsubmit="return confirm('Envoyer l\'attestation d\'assurance à {{ $loan->email }} ?')">
              @csrf
              <button type="submit" class="btn-navy btn-sm-pro ld-btn-full" style="background:#059669;border-color:#059669">
                <i class="fas fa-paper-plane"></i> Envoyer à {{ $loan->email }}
              </button>
            </form>
            <div style="font-size:.68rem;color:var(--c-muted);margin-top:.5rem;display:flex;align-items:flex-start;gap:.35rem">
              <i class="fas fa-globe" style="flex-shrink:0;margin-top:.15rem;color:#16a34a"></i>
              <span>Email envoyé en <strong>{{ strtoupper($loan->contract_language ?? 'FR') }}</strong> (langue du dossier)</span>
            </div>
            @else
            <div class="ld-warn-box" style="background:#F0FDF4;border-color:#A7F3D0">
              <i class="fas fa-lock" style="color:#16a34a;flex-shrink:0;margin-top:.1rem"></i>
              <span style="color:#065F46">Générez d'abord l'attestation PDF pour débloquer l'envoi par email.</span>
            </div>
            @endif

          </div>
        </div>

      </div>{{-- /ld-doc-grid assurance --}}

      {{-- DOCX générés --}}
      @if($generatedDocs->count())
      <div class="card-pro">
        <div class="card-pro-hdr">
          <div class="card-pro-title">
            <span class="icon-dot" style="background:#1D4ED8"></span>
            Documents générés
            <span style="font-size:.65rem;padding:.1rem .45rem;border-radius:10px;background:#DBEAFE;color:#1D4ED8;font-weight:700;margin-left:.25rem">{{ $generatedDocs->count() }}</span>
          </div>
          <a href="{{ route('admin.loans.contract.docx',$loan) }}" class="btn-navy btn-sm-pro">
            <i class="fas fa-plus"></i> Régénérer
          </a>
        </div>
        <div class="table-responsive-pro">
          <table class="pro-table ld-docx-table" style="width:100%">
            <thead>
              <tr><th>Date</th><th>Modèle</th><th>Ver.</th><th>Langue</th><th>Par</th><th style="width:48px"></th></tr>
            </thead>
            <tbody>
              @foreach($generatedDocs as $doc)
              @php $exists = file_exists(storage_path('app/'.$doc->docx_path)); @endphp
              <tr style="{{ !$exists?'opacity:.5':'' }}">
                <td data-label="Date" style="white-space:nowrap">{{ $doc->created_at->format('d/m/Y H:i') }}</td>
                <td data-label="Modèle">{{ $doc->contractTemplate?->name ?? '—' }}</td>
                <td data-label="Version"><span style="font-size:.62rem;padding:.1rem .35rem;border-radius:8px;background:#DBEAFE;color:#1D4ED8;font-weight:700">v{{ $doc->template_version }}</span></td>
                <td data-label="Langue" style="font-weight:700">{{ strtoupper($doc->locale) }}</td>
                <td data-label="Par">{{ $doc->generatedBy?->name ?? '—' }}</td>
                <td data-label="">
                  @if($exists)
                  <a href="{{ route('admin.loans.contract.docx',$loan) }}" class="btn-icon" title="Télécharger"><i class="fas fa-download"></i></a>
                  @else
                  <span style="color:#dc2626" title="Fichier manquant"><i class="fas fa-exclamation-circle"></i></span>
                  @endif
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
      @endif

    </div>{{-- /tab-docs --}}

    {{-- ── TAB : HISTORIQUE ── --}}
    <div class="ld-tab-pane" id="tab-history">
      <div class="card-pro">
        <div class="card-pro-hdr">
          <div class="card-pro-title"><span class="icon-dot"></span>Journal d'activité</div>
          <span style="font-size:.75rem;color:var(--c-muted)">{{ $loan->history->count() }} événements</span>
        </div>
        <div class="ld-timeline">
          @forelse($loan->history->sortByDesc('created_at') as $h)
          @php
            [$hIco,$hCol,$hBg] = $hIcoMap[$h->action] ?? ['fa-history','var(--c-muted)','#f3f4f6'];
            $hLbl = $hLblMap[$h->action] ?? ucfirst(str_replace('_',' ',$h->action));
          @endphp
          <div class="ld-tl-item">
            <div class="ld-tl-dot" style="background:{{ $hBg }};color:{{ $hCol }}">
              <i class="fas {{ $hIco }}"></i>
            </div>
            <div style="flex:1;min-width:0">
              <div class="ld-tl-action">{{ $hLbl }}</div>
              <div class="ld-tl-meta">
                <span style="font-weight:600">{{ $h->admin?->name ?? 'Système' }}</span>
                · {{ $h->created_at->format('d/m/Y à H:i') }}
                @if($h->new_value && isset($h->new_value['status']))
                · <em style="color:var(--c-navy)">→ {{ $statusLabels[$h->new_value['status']] ?? $h->new_value['status'] }}</em>
                @endif
                @if($h->new_value && isset($h->new_value['admin_name']))
                · <em style="color:#7c3aed">→ {{ $h->new_value['admin_name'] }}</em>
                @endif
              </div>
            </div>
            <div class="ld-tl-time">{{ $h->created_at->diffForHumans() }}</div>
          </div>
          @empty
          <div style="padding:2rem;text-align:center;color:var(--c-muted);font-size:.85rem">
            <i class="fas fa-history" style="font-size:1.75rem;display:block;margin-bottom:.5rem;opacity:.3"></i>
            Aucun événement enregistré
          </div>
          @endforelse
        </div>
      </div>
    </div>{{-- /tab-history --}}

    {{-- ── TAB : AMORTISSEMENT ── --}}
    @if($loan->amortization_schedule)
    <div class="ld-tab-pane" id="tab-amort">
      <div class="card-pro">
        <div class="card-pro-hdr">
          <div class="card-pro-title">
            <span class="icon-dot"></span>Tableau d'amortissement
            <span style="font-size:.75rem;color:var(--c-muted);font-weight:400;margin-left:.375rem">
              · {{ count($loan->amortization_schedule) }} échéances
              · Intérêts totaux : {{ number_format($loan->total_cost,2,',',' ') }} {{ $loan->currency }}
            </span>
          </div>
        </div>
        <div class="ld-amort-wrap">
          <table class="pro-table" style="width:100%">
            <thead style="position:sticky;top:0;z-index:1;background:#fafbfc">
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
                <td data-label="N°" style="color:var(--c-muted);font-size:.75rem">{{ $row['month'] }}</td>
                <td data-label="Mensualité" style="font-weight:700">{{ number_format($row['payment'],2,',',' ') }} {{ $loan->currency }}</td>
                <td data-label="Capital">{{ number_format($row['principal'],2,',',' ') }} {{ $loan->currency }}</td>
                <td data-label="Intérêts" style="color:#ef4444">{{ number_format($row['interest'],2,',',' ') }} {{ $loan->currency }}</td>
                <td data-label="Solde restant" style="color:var(--c-muted)">{{ number_format($row['balance'],2,',',' ') }} {{ $loan->currency }}</td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
    @endif

  </div>{{-- /contenu tabs --}}
</div>{{-- /ld-layout --}}

@endsection

@push('scripts')
<script>
function ldTab(btn, paneId) {
  document.querySelectorAll('.ld-tab-btn').forEach(b => b.classList.remove('active'));
  document.querySelectorAll('.ld-tab-pane').forEach(p => p.classList.remove('active'));
  btn.classList.add('active');
  document.getElementById(paneId).classList.add('active');
}

// Upload auto-submit feedback
document.querySelectorAll('.ld-upload-area input[type="file"]').forEach(function(inp) {
  inp.addEventListener('change', function() {
    const area = this.closest('.ld-upload-area');
    if (this.files[0]) {
      area.querySelector('div:first-of-type') && (area.querySelector('i').style.color = '#22c55e');
      area.querySelectorAll('div')[0].textContent = this.files[0].name;
      area.querySelectorAll('div')[1].textContent = 'Envoi en cours...';
      this.closest('form').submit();
    }
  });
});
</script>
@endpush
