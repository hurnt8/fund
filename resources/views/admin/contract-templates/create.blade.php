@extends('layouts.dashboard')
@section('title','Nouveau modèle de contrat')
@section('page_title','Nouveau modèle de contrat')

@section('content')

<div class="d-flex align-items-start justify-content-between flex-wrap gap-3 page-hdr">
  <div>
    <h4>Nouveau modèle de contrat</h4>
    <p>Créez un template réutilisable. Utilisez les variables <code style="background:var(--c-bg);padding:.1rem .35rem;border-radius:4px;font-size:.8rem">{variable}</code> pour les données dynamiques.</p>
  </div>
  <a href="{{ route('admin.contract-templates.index') }}" class="btn-ghost btn-sm-pro">
    <i class="fas fa-arrow-left"></i> Retour
  </a>
</div>

@if($errors->any())
<div class="flash flash-err mb-4"><i class="fas fa-exclamation-triangle"></i> {{ $errors->first() }}</div>
@endif

<div class="row g-4">

  {{-- Éditeur --}}
  <div class="col-xl-8">
    <form action="{{ route('admin.contract-templates.store') }}" method="POST" id="tplForm" enctype="multipart/form-data">
    @csrf
    <div class="card-pro mb-4">
      <div class="card-pro-hdr">
        <div class="card-pro-title"><span class="icon-dot"></span>Informations du modèle</div>
      </div>
      <div class="card-pro-body">
        <div class="row g-3">
          <div class="col-sm-8">
            <label class="form-label-pro">Nom du modèle *</label>
            <input type="text" name="name" class="form-control-pro"
                   value="{{ old('name') }}" placeholder="Ex: Contrat Standard Credixa Invest" required>
          </div>
          <div class="col-sm-4">
            <label class="form-label-pro">Langue</label>
            <select name="locale" class="form-control-pro">
              <option value="">— Toutes langues —</option>
              <option value="fr" {{ old('locale')==='fr'?'selected':'' }}>🇫🇷 Français</option>
              <option value="en" {{ old('locale')==='en'?'selected':'' }}>🇬🇧 English</option>
              <option value="pl" {{ old('locale')==='pl'?'selected':'' }}>🇵🇱 Polski</option>
              <option value="es" {{ old('locale')==='es'?'selected':'' }}>🇪🇸 Español</option>
            </select>
          </div>
          <div class="col-12 d-flex align-items-center gap-3">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="is_default" id="is_default" value="1"
                     {{ old('is_default')?'checked':'' }}>
              <label class="form-check-label" for="is_default" style="font-size:.8125rem;font-weight:500;color:var(--c-navy)">
                Modèle par défaut
              </label>
            </div>
          </div>
        </div>
      </div>
    </div>

    @php $initMode = old('template_type', 'html'); @endphp
    <input type="hidden" name="template_type" id="templateTypeInput" value="{{ $initMode }}">
    <div class="card-pro mb-4" x-data="{ mode: '{{ $initMode }}' }" x-init="$watch('mode', v => document.getElementById('templateTypeInput').value = v)">
      <div class="card-pro-hdr">
        <div class="card-pro-title"><span class="icon-dot"></span>Contenu du modèle</div>
        <div style="display:flex;gap:.5rem">
          <button type="button" @click="mode='html'"
            :class="mode==='html'?'btn-navy btn-sm-pro':'btn-ghost btn-sm-pro'">
            <i class="fas fa-code"></i> HTML / Texte
          </button>
          <button type="button" @click="mode='docx'"
            :class="mode==='docx'?'btn-navy btn-sm-pro':'btn-ghost btn-sm-pro'">
            <i class="fas fa-file-word"></i> Fichier DOCX
          </button>
        </div>
      </div>

      {{-- Mode HTML --}}
      <div x-show="mode==='html'" style="padding:1.25rem">
        <p style="font-size:.75rem;color:var(--c-muted);margin-bottom:.875rem">
          HTML accepté · Toutes les <code>{balises}</code> listées à droite sont substituées automatiquement selon la langue du client.
        </p>
        <textarea name="content" id="contractEditor"
                  style="width:100%;min-height:500px;font-family:'Courier New',monospace;font-size:.78rem;
                         line-height:1.7;padding:1.25rem;
                         border:1.5px solid var(--c-border);border-radius:var(--radius-sm);
                         background:#1A2332;color:#E2E8F0;resize:vertical;transition:border-color .2s"
                  onfocus="this.style.borderColor='var(--c-gold)'"
                  onblur="this.style.borderColor='var(--c-border)'"
                  >{{ old('content') }}</textarea>
      </div>

      {{-- Mode DOCX --}}
      <div x-show="mode==='docx'" style="padding:1.25rem">
        <div style="background:#EFF6FF;border:1px solid #BFDBFE;border-radius:var(--radius-sm);padding:.875rem 1rem;margin-bottom:1rem">
          <div style="font-size:.8rem;font-weight:700;color:#1D4ED8;margin-bottom:.35rem">
            <i class="fas fa-info-circle me-1"></i>Comment baliser votre DOCX
          </div>
          <ul style="font-size:.75rem;color:#1E40AF;margin:0;padding-left:1.25rem;line-height:1.7">
            <li>Insérez les balises <code>{nom_client}</code>, <code>{montant}</code>, <code>{numero_identite}</code>… directement dans le texte Word</li>
            <li>Tapez chaque balise <strong>d'un seul bloc</strong> (copier-coller recommandé pour éviter les coupures XML)</li>
            <li>Le système détecte automatiquement toutes les balises présentes après upload</li>
            <li>Prérequis PDF : <strong>LibreOffice</strong> installé + <code>LIBREOFFICE_BIN</code> dans <code>.env</code></li>
          </ul>
        </div>
        <label class="form-label-pro">Fichier DOCX balisé *</label>
        <input type="file" name="docx_file" accept=".docx"
               class="form-control-pro"
               style="padding:.5rem .875rem;cursor:pointer">
        <p style="font-size:.72rem;color:var(--c-muted);margin-top:.35rem">
          <i class="fas fa-shield-alt me-1"></i>Format .docx uniquement · Max 10 Mo
        </p>
      </div>
    </div>

    {{-- Images du modèle --}}
    <div class="card-pro mb-4">
      <div class="card-pro-hdr">
        <div class="card-pro-title"><span class="icon-dot"></span>Images du modèle <span style="font-size:.7rem;color:var(--c-muted);font-weight:400">(optionnel — pour templates HTML)</span></div>
      </div>
      <div class="card-pro-body">
        <div class="row g-3">
          @php
          $imgInputs = [
            'watermark'       => ['label' => 'Filigrane (fond de page)', 'icon' => 'fa-tint',      'hint' => 'PNG recommandé, fond transparent'],
            'logo_left'       => ['label' => 'Logo coin gauche',          'icon' => 'fa-image',     'hint' => 'Coin supérieur gauche de l\'en-tête'],
            'logo_right'      => ['label' => 'Logo coin droit',           'icon' => 'fa-image',     'hint' => 'Coin supérieur droit de l\'en-tête'],
            'stamp'           => ['label' => 'Cachet / tampon',           'icon' => 'fa-stamp',     'hint' => 'Apposé dans la zone de signature'],
            'signature_admin' => ['label' => 'Signature société',          'icon' => 'fa-pen-nib',  'hint' => 'PNG fond transparent'],
            'signature_agent' => ['label' => 'Signature agent',           'icon' => 'fa-pen-nib',  'hint' => 'PNG fond transparent'],
          ];
          @endphp
          @foreach($imgInputs as $field => $cfg)
          <div class="col-md-4">
            <div style="border:1.5px dashed var(--c-border);border-radius:var(--radius-sm);padding:.875rem;text-align:center;position:relative;transition:border-color .2s"
                 onmouseover="this.style.borderColor='var(--c-gold)'" onmouseout="this.style.borderColor='var(--c-border)'">
              <div style="font-size:1.25rem;color:var(--c-muted);margin-bottom:.4rem"><i class="fas {{ $cfg['icon'] }}"></i></div>
              <div style="font-size:.72rem;font-weight:700;color:var(--c-navy);margin-bottom:.2rem">{{ $cfg['label'] }}</div>
              <div style="font-size:.65rem;color:var(--c-muted);margin-bottom:.6rem">{{ $cfg['hint'] }}</div>
              <label style="display:inline-flex;align-items:center;gap:.35rem;padding:.28rem .7rem;background:var(--c-bg);border:1px solid var(--c-border);border-radius:5px;font-size:.7rem;cursor:pointer;color:var(--c-navy);font-weight:600">
                <i class="fas fa-upload" style="font-size:.65rem"></i> Choisir
                <input type="file" name="{{ $field }}" accept="image/*" style="display:none"
                       onchange="previewImg(this,'prev_{{ $field }}')">
              </label>
              <div id="prev_{{ $field }}" style="margin-top:.5rem;display:none">
                <img style="max-height:56px;max-width:100%;object-fit:contain;border-radius:4px">
              </div>
            </div>
          </div>
          @endforeach
        </div>
      </div>
    </div>

    <div class="d-flex justify-content-end gap-3">
      <a href="{{ route('admin.contract-templates.index') }}" class="btn-ghost">Annuler</a>
      <button type="submit" class="btn-navy"><i class="fas fa-save"></i> Créer le modèle</button>
    </div>
    </form>
  </div>

  {{-- Référence variables --}}
  <div class="col-xl-4">
    <div class="card-pro" style="position:sticky;top:80px">
      <div class="card-pro-hdr">
        <div class="card-pro-title"><span class="icon-dot"></span>Variables disponibles</div>
      </div>
      <div style="max-height:calc(100vh - 200px);overflow-y:auto;padding:.75rem">
        @foreach($variables as $var=>$desc)
        <div style="display:flex;align-items:center;gap:.5rem;padding:.5rem .625rem;
                    border-radius:var(--radius-sm);margin-bottom:.25rem;
                    border:1px solid var(--c-border);background:var(--c-bg);
                    transition:var(--transition)"
             onmouseover="this.style.borderColor='var(--c-gold)';this.style.background='#fff'"
             onmouseout="this.style.borderColor='var(--c-border)';this.style.background='var(--c-bg)'">
          <div style="flex:1;min-width:0">
            <code style="font-size:.7rem;color:var(--c-gold-d);font-weight:700;display:block">{{ $var }}</code>
            <span style="font-size:.68rem;color:var(--c-muted)">{{ $desc }}</span>
          </div>
          <button type="button" onclick="insertVar('{{ $var }}')"
                  style="width:26px;height:26px;border:none;background:none;color:var(--c-muted);cursor:pointer;font-size:.75rem;border-radius:5px;flex-shrink:0"
                  title="Insérer dans l'éditeur">
            <i class="fas fa-arrow-left"></i>
          </button>
        </div>
        @endforeach
      </div>
    </div>
  </div>

</div>
@endsection

@push('scripts')
<script>
function insertVar(text) {
  const ta = document.getElementById('contractEditor');
  const s = ta.selectionStart, e = ta.selectionEnd;
  ta.value = ta.value.slice(0,s) + text + ta.value.slice(e);
  ta.selectionStart = ta.selectionEnd = s + text.length;
  ta.focus();
}
function previewImg(input, divId) {
  const div = document.getElementById(divId);
  if (!input.files || !input.files[0]) return;
  const reader = new FileReader();
  reader.onload = e => {
    div.querySelector('img').src = e.target.result;
    div.style.display = 'block';
  };
  reader.readAsDataURL(input.files[0]);
}
</script>
@endpush
