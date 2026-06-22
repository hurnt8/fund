@extends('layouts.dashboard')
@section('title', 'Contrat — ' . $loan->reference)
@section('page_title', 'Contrat')

@push('styles')
<style>
/* ── Layout ── */
.contract-page{display:grid;grid-template-columns:320px 1fr;gap:1.25rem;align-items:start}
@media(max-width:1024px){.contract-page{grid-template-columns:1fr}}

/* ── Sidebar ── */
.contract-sidebar{display:flex;flex-direction:column;gap:1rem}

/* ── Send banner ── */
.send-banner{background:linear-gradient(135deg,#0E1F3D,#14374F);border-radius:14px;padding:1.375rem 1.25rem;color:#fff}
.send-banner-title{font-size:.9375rem;font-weight:800;margin-bottom:.25rem}
.send-banner-sub{font-size:.75rem;color:rgba(255,255,255,.5);margin-bottom:1.125rem;line-height:1.5}
.send-banner-btn{width:100%;padding:.8rem;border:none;border-radius:10px;background:linear-gradient(90deg,#22A396,#167A6C);color:#fff;font-size:.875rem;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:.5rem;transition:opacity .15s}
.send-banner-btn:hover{opacity:.88}
.send-banner-btn:disabled{opacity:.45;cursor:not-allowed}
.send-banner-info{display:flex;align-items:center;gap:.5rem;margin-top:.875rem;font-size:.72rem;color:rgba(255,255,255,.4);line-height:1.4}

/* ── Meta card ── */
.meta-row{display:flex;align-items:center;justify-content:space-between;padding:.5rem 0;border-bottom:1px solid var(--c-border)}
.meta-row:last-child{border-bottom:0}
.meta-lbl{font-size:.72rem;color:var(--c-muted);font-weight:500}
.meta-val{font-size:.8rem;font-weight:600;color:var(--c-navy);text-align:right}

/* ── PDF viewer ── */
.pdf-wrap{background:var(--c-card,#fff);border:1px solid var(--c-border);border-radius:14px;overflow:hidden}
.pdf-toolbar{display:flex;align-items:center;justify-content:space-between;padding:.75rem 1.125rem;border-bottom:1px solid var(--c-border);gap:.75rem;flex-wrap:wrap}
.pdf-toolbar-title{font-size:.8125rem;font-weight:700;color:var(--c-navy);display:flex;align-items:center;gap:.5rem}
.pdf-toolbar-actions{display:flex;gap:.5rem}
.pdf-frame{width:100%;height:78vh;border:none;display:block;background:#525659}

/* ── Tabs (editor/vars) ── */
.contract-tabs{background:var(--c-card,#fff);border:1px solid var(--c-border);border-radius:14px;overflow:hidden;margin-top:1.25rem}
.tabs-row{display:flex;border-bottom:1px solid var(--c-border);background:var(--c-bg,#f8f9fa)}
.tab-pill{padding:.625rem 1.125rem;font-size:.8rem;font-weight:600;color:var(--c-muted);border:none;background:none;cursor:pointer;border-bottom:2px solid transparent;margin-bottom:-1px;transition:color .15s,border-color .15s}
.tab-pill.active{color:var(--c-navy);border-bottom-color:var(--c-navy)}
.tab-content{display:none;padding:1.25rem}
.tab-content.active{display:block}

/* ── Misc ── */
.lang-badge{display:inline-flex;align-items:center;gap:.375rem;background:rgba(245,158,11,.1);border:1px solid rgba(245,158,11,.25);border-radius:999px;padding:.25rem .625rem;font-size:.72rem;font-weight:700;color:#92400e}
</style>
@endpush

@section('content')

{{-- Header --}}
<div class="d-flex align-items-start justify-content-between flex-wrap gap-3 page-hdr" style="margin-bottom:1.25rem">
  <div>
    <div style="font-size:1.0625rem;font-weight:800;color:var(--c-navy)">
      Contrat — <span style="font-family:monospace;color:var(--c-gold)">{{ $loan->reference }}</span>
    </div>
    <div style="font-size:.78rem;color:var(--c-muted);margin-top:.2rem">
      {{ $loan->name }} · {{ $loan->email }}
      <span class="lang-badge" style="margin-left:.5rem">
        <i class="fas fa-globe"></i> {{ strtoupper($loan->contract_language ?? 'FR') }}
      </span>
    </div>
  </div>
  <a href="{{ route('admin.loans.show', $loan) }}" class="btn-ghost btn-sm-pro">
    <i class="fas fa-arrow-left"></i> Retour au dossier
  </a>
</div>

@if(session('success'))
<div class="flash flash-ok mb-4"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
@endif

<div class="contract-page">

  {{-- ── Sidebar ── --}}
  <div class="contract-sidebar">

    {{-- Bannière d'envoi --}}
    <div class="send-banner">
      <div class="send-banner-title">
        <i class="fas fa-paper-plane" style="color:#2ECBB7;margin-right:.375rem"></i>
        Envoyer le contrat
      </div>
      <div class="send-banner-sub">
        Le PDF sera généré en <strong style="color:#fff">{{ strtoupper($loan->contract_language ?? 'FR') }}</strong>
        et envoyé avec le tableau d'amortissement à&nbsp;:
        <span style="color:#2ECBB7;font-weight:600">{{ $loan->email }}</span>
      </div>

      @if($loan->canBeValidated())
      <form action="{{ route('admin.loans.validate', $loan) }}" method="POST"
            onsubmit="return confirmSend()">
        @csrf
        <button type="submit" class="send-banner-btn" id="sendBtn">
          <i class="fas fa-paper-plane"></i>
          Valider &amp; Envoyer au client
        </button>
      </form>
      @elseif($loan->status === 'contract_sent')
      <div style="background:rgba(34,197,94,.15);border:1px solid rgba(34,197,94,.3);border-radius:10px;padding:.75rem .875rem;display:flex;align-items:center;gap:.625rem">
        <i class="fas fa-check-circle" style="color:#4ade80"></i>
        <span style="font-size:.8rem;color:#4ade80;font-weight:600">Contrat envoyé le {{ $loan->sent_at?->format('d/m/Y') }}</span>
      </div>
      @elseif($loan->status === 'contract_signed')
      <div style="background:rgba(139,92,246,.15);border:1px solid rgba(139,92,246,.3);border-radius:10px;padding:.75rem .875rem;display:flex;align-items:center;gap:.625rem">
        <i class="fas fa-file-signature" style="color:#a78bfa"></i>
        <span style="font-size:.8rem;color:#a78bfa;font-weight:600">Contrat signé reçu</span>
      </div>
      @elseif($loan->status === 'finalized')
      <div style="background:rgba(34,197,94,.15);border:1px solid rgba(34,197,94,.3);border-radius:10px;padding:.75rem .875rem;display:flex;align-items:center;gap:.625rem">
        <i class="fas fa-check-double" style="color:#4ade80"></i>
        <span style="font-size:.8rem;color:#4ade80;font-weight:600">Dossier finalisé</span>
      </div>
      @else
      <button class="send-banner-btn" disabled>
        <i class="fas fa-lock"></i>
        Dossier {{ $loan->statusLabel() }}
      </button>
      @endif

      <div class="send-banner-info">
        <i class="fas fa-info-circle"></i>
        <span>L'envoi déclenche aussi une notification in-app pour le client.</span>
      </div>
    </div>

    {{-- Récapitulatif dossier --}}
    <div class="card-pro">
      <div class="card-pro-hdr">
        <div class="card-pro-title"><span class="icon-dot"></span>Récapitulatif</div>
      </div>
      <div class="card-pro-body" style="padding:.625rem 1rem">
        @foreach([
          ['Référence',       $loan->reference],
          ['Client',          $loan->name],
          ['Montant',         number_format($loan->amount, 2, ',', ' ') . ' ' . $loan->currency],
          ['Durée',           $loan->darly . ' mois'],
          ['Mensualité',      number_format($loan->monthly_payment, 2, ',', ' ') . ' ' . $loan->currency],
          ['Taux',            $loan->interest_rate . ' %'],
          ['Statut',          $loan->statusLabel()],
        ] as [$lbl, $val])
        <div class="meta-row">
          <span class="meta-lbl">{{ $lbl }}</span>
          <span class="meta-val">{{ $val }}</span>
        </div>
        @endforeach
      </div>
    </div>

    {{-- Téléchargement direct --}}
    <div class="card-pro">
      <div class="card-pro-hdr">
        <div class="card-pro-title"><span class="icon-dot"></span>Téléchargements</div>
      </div>
      <div class="card-pro-body" style="display:flex;flex-direction:column;gap:.5rem">
        <a href="{{ route('admin.loans.contract.pdf', $loan) }}"
           target="_blank"
           class="btn-ghost btn-sm-pro"
           style="justify-content:center;text-align:center">
          <i class="fas fa-file-pdf" style="color:#ef4444"></i> Ouvrir le PDF contrat
        </a>
      </div>
    </div>

  </div>

  {{-- ── Zone principale ── --}}
  <div>

    {{-- Visionneuse PDF --}}
    <div class="pdf-wrap">
      <div class="pdf-toolbar">
        <div class="pdf-toolbar-title">
          <i class="fas fa-file-pdf" style="color:#ef4444"></i>
          Aperçu PDF — version client
          <span class="lang-badge">{{ strtoupper($loan->contract_language ?? 'FR') }}</span>
        </div>
        <div class="pdf-toolbar-actions">
          <a href="{{ route('admin.loans.contract.pdf', $loan) }}"
             target="_blank"
             class="btn-ghost btn-sm-pro">
            <i class="fas fa-external-link-alt"></i> Ouvrir dans un onglet
          </a>
          <a href="{{ route('admin.loans.contract.pdf', $loan) }}"
             download="Contrat_{{ $loan->reference }}.pdf"
             class="btn-ghost btn-sm-pro">
            <i class="fas fa-download"></i> Télécharger
          </a>
        </div>
      </div>
      <iframe
        src="{{ route('admin.loans.contract.pdf', $loan) }}"
        class="pdf-frame"
        title="Aperçu contrat {{ $loan->reference }}"
        loading="lazy"
      ></iframe>
    </div>

    {{-- Tabs : Éditeur + Variables --}}
    <div class="contract-tabs">
      <div class="tabs-row">
        <button class="tab-pill active" onclick="switchTab('editor', this)">
          <i class="fas fa-code"></i> Éditeur HTML
        </button>
        <button class="tab-pill" onclick="switchTab('vars', this)">
          <i class="fas fa-brackets-curly"></i> Variables disponibles
        </button>
      </div>

      {{-- Éditeur --}}
      <div id="tab-editor" class="tab-content active">
        <div class="flash flash-warn" style="margin-bottom:1rem">
          <i class="fas fa-exclamation-triangle"></i>
          HTML accepté. Les variables <code style="background:rgba(0,0,0,.06);padding:.1rem .3rem;border-radius:4px">{variable}</code>
          sont remplacées automatiquement à l'envoi. Après sauvegarde, rechargez la page pour voir le PDF mis à jour.
        </div>
        <form action="{{ route('admin.loans.contract.update', $loan) }}" method="POST">
          @csrf
          <textarea name="contract_content"
                    style="width:100%;min-height:480px;font-family:'Courier New',monospace;font-size:.78rem;
                           line-height:1.6;padding:1rem;border:1.5px solid var(--c-border);border-radius:8px;
                           resize:vertical;background:#1A2332;color:#e2e8f0"
          >{{ $loan->contract_content }}</textarea>
          <div class="d-flex justify-content-end mt-3">
            <button type="submit" class="btn-navy">
              <i class="fas fa-save"></i> Sauvegarder
            </button>
          </div>
        </form>
      </div>

      {{-- Variables --}}
      <div id="tab-vars" class="tab-content">
        <p style="font-size:.8125rem;color:var(--c-muted);margin-bottom:1.25rem">
          Cliquez sur <i class="fas fa-copy"></i> pour copier une variable dans le presse-papier.
        </p>
        <div class="row g-2">
          @foreach([
            ['{nom_client}','Nom complet du client','person'],
            ['{email_client}','Adresse email du client','envelope'],
            ['{adresse_client}','Adresse postale du client','map-marker-alt'],
            ['{date_naissance}','Date de naissance du client','birthday-cake'],
            ['{reference}','Référence du dossier CR-YYYY-XXXX','hashtag'],
            ['{archive}','Référence d\'archivage interne','archive'],
            ['{montant}','Montant du prêt formaté','euro-sign'],
            ['{devise}','Devise (EUR, PLN, GBP…)','coins'],
            ['{duree}','Durée en nombre de mois','calendar-alt'],
            ['{mensualite}','Mensualité calculée','redo'],
            ['{taux}','Taux d\'intérêt annuel en %','percent'],
            ['{total_remboursement}','Total à rembourser capital+intérêts','calculator'],
            ['{cout_credit}','Coût total des intérêts','chart-line'],
            ['{frais_admin}','Montant des frais administratifs','file-invoice'],
            ['{agent_suivi}','Nom de l\'administrateur responsable','user-tie'],
            ['{date}','Date de validation du contrat','calendar-check'],
            ['{date_debut}','Date de première échéance','calendar'],
            ['{objet}','Objet ou motif du prêt','tag'],
            ['{conditions_speciales}','Clauses et conditions particulières','file-alt'],
          ] as [$var, $desc, $icon])
          <div class="col-md-6 col-lg-4">
            <div style="display:flex;align-items:center;gap:.75rem;padding:.625rem .875rem;
                        background:var(--c-bg);border:1.5px solid var(--c-border);border-radius:8px;
                        transition:border-color .15s"
                 onmouseover="this.style.borderColor='var(--c-gold)'"
                 onmouseout="this.style.borderColor='var(--c-border)'">
              <div style="width:30px;height:30px;border-radius:7px;background:#EEF2FF;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <i class="fas fa-{{ $icon }}" style="font-size:.72rem;color:var(--c-navy)"></i>
              </div>
              <div style="flex:1;min-width:0">
                <code style="font-size:.7rem;color:var(--c-gold-d,#b45309);font-weight:700;display:block">{{ $var }}</code>
                <span style="font-size:.67rem;color:var(--c-muted)">{{ $desc }}</span>
              </div>
              <button type="button"
                      onclick="navigator.clipboard.writeText('{{ $var }}').then(()=>{this.innerHTML='<i class=\'fas fa-check\' style=\'color:#22c55e\'></i>';setTimeout(()=>this.innerHTML='<i class=\'fas fa-copy\'></i>',1500)})"
                      style="width:26px;height:26px;border:none;background:none;color:var(--c-muted);cursor:pointer;border-radius:6px;flex-shrink:0"
                      title="Copier">
                <i class="fas fa-copy"></i>
              </button>
            </div>
          </div>
          @endforeach
        </div>
      </div>

    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
function switchTab(id, btn) {
  document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
  document.querySelectorAll('.tab-pill').forEach(b => b.classList.remove('active'));
  document.getElementById('tab-' + id).classList.add('active');
  btn.classList.add('active');
}

function confirmSend() {
  const ok = confirm(
    'Envoyer le contrat par email à {{ $loan->email }} ?\n\n' +
    'Un PDF en {{ strtoupper($loan->contract_language ?? "FR") }} sera généré et joint au message, ' +
    'avec le tableau d\'amortissement.'
  );
  if (ok) {
    const btn = document.getElementById('sendBtn');
    if (btn) {
      btn.disabled = true;
      btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Envoi en cours…';
    }
  }
  return ok;
}
</script>
@endpush
