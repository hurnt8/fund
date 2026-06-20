@extends('layouts.dashboard')
@section('title','Contrat — '.$loan->reference)
@section('page_title','Contrat')

@section('content')

<div class="d-flex align-items-start justify-content-between flex-wrap gap-3 page-hdr">
  <div>
    <h4>Contrat — <span style="font-family:monospace;color:var(--c-gold)">{{ $loan->reference }}</span></h4>
    <p>Aperçu en français · Traduit automatiquement dans la langue du client à l'envoi</p>
  </div>
  <div class="d-flex gap-2 flex-wrap">
    <a href="{{ route('admin.loans.show',$loan) }}" class="btn-ghost btn-sm-pro">
      <i class="fas fa-arrow-left"></i> Retour au dossier
    </a>
    @if($loan->canBeValidated())
    <form action="{{ route('admin.loans.validate',$loan) }}" method="POST"
          onsubmit="return confirm('Envoyer le contrat signé par email au client ?')">
      @csrf
      <button class="btn-navy btn-sm-pro"><i class="fas fa-paper-plane"></i> Valider &amp; Envoyer</button>
    </form>
    @endif
  </div>
</div>

{{-- Info langue --}}
<div class="flash flash-warn mb-4">
  <i class="fas fa-globe"></i>
  Cet aperçu est toujours généré en <strong>Français</strong>.
  À l'envoi, le contrat sera automatiquement traduit en
  <strong>{{ strtoupper($loan->contract_language ?? 'FR') }}</strong> pour le client {{ $loan->name }}.
</div>

{{-- Tabs --}}
<div class="tabs-pro" id="contractTabList">
  <button class="tab-btn active" onclick="showTab('preview',this)">
    <i class="fas fa-eye" style="margin-right:.4rem"></i> Aperçu contrat
  </button>
  <button class="tab-btn" onclick="showTab('editor',this)">
    <i class="fas fa-code" style="margin-right:.4rem"></i> Éditeur HTML
  </button>
  <button class="tab-btn" onclick="showTab('vars',this)">
    <i class="fas fa-brackets-curly" style="margin-right:.4rem"></i> Variables disponibles
  </button>
</div>

{{-- APERÇU --}}
<div id="tab-preview">
  <div class="card-pro">
    <div class="card-pro-hdr">
      <div class="card-pro-title">
        <span class="icon-dot"></span>
        Prévisualisation — {{ $loan->name }} · {{ $loan->reference }}
      </div>
      <span class="badge-status bs-gray">Lecture seule</span>
    </div>
    <div style="padding:2rem;max-height:72vh;overflow-y:auto;background:#FAFBFC">
      <div style="max-width:800px;margin:0 auto;background:#fff;padding:2.5rem;box-shadow:0 2px 16px rgba(0,0,0,.08);border-radius:8px;font-family:Georgia,serif;font-size:.9rem;line-height:1.7;color:#222">
        {!! $previewHtml !!}
      </div>
    </div>
  </div>
</div>

{{-- ÉDITEUR --}}
<div id="tab-editor" style="display:none">
  <div class="card-pro">
    <div class="card-pro-hdr">
      <div class="card-pro-title"><span class="icon-dot"></span>Éditeur du contenu brut</div>
    </div>
    <div style="padding:1.25rem">
      <div class="flash flash-warn mb-3" style="margin-bottom:1rem">
        <i class="fas fa-exclamation-triangle"></i>
        HTML accepté. Les variables <code style="background:rgba(0,0,0,.06);padding:.1rem .3rem;border-radius:4px">{variable}</code>
        sont remplacées automatiquement à l'envoi.
      </div>
      <form action="{{ route('admin.loans.contract.update',$loan) }}" method="POST">
        @csrf
        <textarea name="contract_content"
                  style="width:100%;min-height:520px;font-family:'Courier New',monospace;font-size:.78rem;line-height:1.6;
                         padding:1rem;border:1.5px solid var(--c-border);border-radius:var(--radius-sm);
                         resize:vertical;color:var(--c-text);background:#1A2332;color:#e2e8f0"
                  >{{ $loan->contract_content }}</textarea>
        <div class="d-flex justify-content-end mt-3">
          <button type="submit" class="btn-navy">
            <i class="fas fa-save"></i> Sauvegarder les modifications
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- VARIABLES --}}
<div id="tab-vars" style="display:none">
  <div class="card-pro">
    <div class="card-pro-hdr">
      <div class="card-pro-title"><span class="icon-dot"></span>Variables de substitution</div>
    </div>
    <div style="padding:1.25rem">
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
        ] as [$var,$desc,$icon])
        <div class="col-md-6 col-lg-4">
          <div style="display:flex;align-items:center;gap:.75rem;padding:.75rem 1rem;
                      background:var(--c-bg);border:1.5px solid var(--c-border);border-radius:var(--radius-sm);
                      transition:var(--transition)"
               onmouseover="this.style.borderColor='var(--c-gold)'"
               onmouseout="this.style.borderColor='var(--c-border)'">
            <div style="width:32px;height:32px;border-radius:8px;background:#EEF2FF;display:flex;align-items:center;justify-content:center;flex-shrink:0">
              <i class="fas fa-{{ $icon }}" style="font-size:.78rem;color:var(--c-navy)"></i>
            </div>
            <div style="flex:1;min-width:0">
              <code style="font-size:.72rem;color:var(--c-gold-d);font-weight:700;display:block">{{ $var }}</code>
              <span style="font-size:.7rem;color:var(--c-muted)">{{ $desc }}</span>
            </div>
            <button type="button"
                    onclick="navigator.clipboard.writeText('{{ $var }}').then(()=>{this.innerHTML='<i class=\'fas fa-check\' style=\'color:var(--c-green)\'></i>';setTimeout(()=>this.innerHTML='<i class=\'fas fa-copy\'></i>',1500)})"
                    style="width:28px;height:28px;border:none;background:none;color:var(--c-muted);cursor:pointer;border-radius:6px;flex-shrink:0"
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

@endsection

@push('scripts')
<script>
function showTab(id, btn) {
  document.querySelectorAll('#tab-preview,#tab-editor,#tab-vars').forEach(el => el.style.display='none');
  document.getElementById('tab-'+id).style.display='';
  document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
}
</script>
@endpush
