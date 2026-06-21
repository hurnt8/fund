@extends('layouts.dashboard')
@section('title','Modifier — '.$template->name)
@section('page_title','Modifier le modèle')

@section('content')

<div class="d-flex align-items-start justify-content-between flex-wrap gap-3 page-hdr">
  <div>
    <h4>Modifier — {{ $template->name }}</h4>
    <p>Les modifications s'appliquent aux prochaines générations de contrats</p>
  </div>
  <div class="d-flex gap-2">
    <a href="{{ route('admin.contract-templates.preview',$template) }}" class="btn-ghost btn-sm-pro" target="_blank">
      <i class="fas fa-eye"></i> Aperçu
    </a>
    <a href="{{ route('admin.contract-templates.index') }}" class="btn-ghost btn-sm-pro">
      <i class="fas fa-arrow-left"></i> Retour
    </a>
  </div>
</div>

@if($errors->any())
<div class="flash flash-err mb-4"><i class="fas fa-exclamation-triangle"></i> {{ $errors->first() }}</div>
@endif

<div class="row g-4">

  <div class="col-xl-8">
    @php $initMode = old('template_type', $template->template_type === 'docx' ? 'docx' : 'html'); @endphp
    <form action="{{ route('admin.contract-templates.update',$template) }}" method="POST"
          id="tplForm" enctype="multipart/form-data"
          x-data="{ mode: '{{ $initMode }}' }"
          x-init="$watch('mode', v => document.getElementById('templateTypeInput').value = v)">
    @csrf @method('PUT')
    <input type="hidden" name="template_type" id="templateTypeInput" value="{{ $initMode }}">

    {{-- Informations --}}
    <div class="card-pro mb-4">
      <div class="card-pro-hdr">
        <div class="card-pro-title"><span class="icon-dot"></span>Informations du modèle</div>
      </div>
      <div class="card-pro-body">
        <div class="row g-3">
          <div class="col-sm-8">
            <label class="form-label-pro">Nom du modèle *</label>
            <input type="text" name="name" class="form-control-pro"
                   value="{{ old('name',$template->name) }}" required>
          </div>
          <div class="col-sm-4">
            <label class="form-label-pro">Langue</label>
            <select name="locale" class="form-control-pro">
              <option value="">— Toutes langues —</option>
              <option value="fr" {{ old('locale',$template->locale)==='fr'?'selected':'' }}>🇫🇷 Français</option>
              <option value="en" {{ old('locale',$template->locale)==='en'?'selected':'' }}>🇬🇧 English</option>
              <option value="pl" {{ old('locale',$template->locale)==='pl'?'selected':'' }}>🇵🇱 Polski</option>
              <option value="es" {{ old('locale',$template->locale)==='es'?'selected':'' }}>🇪🇸 Español</option>
            </select>
          </div>
          <div class="col-12">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="is_default" id="is_default" value="1"
                     {{ old('is_default',$template->is_default)?'checked':'' }}>
              <label class="form-check-label" for="is_default"
                     style="font-size:.8125rem;font-weight:500;color:var(--c-navy)">
                Modèle par défaut
              </label>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- Attribution aux administrateurs (super-admin uniquement) --}}
    @if(auth()->user()->hasRole('super-admin') && $admins->isNotEmpty())
    <div class="card-pro mb-4">
      <div class="card-pro-hdr">
        <div class="card-pro-title"><span class="icon-dot"></span>Administrateurs autorisés</div>
        <span style="font-size:.75rem;color:var(--c-muted)">Laissez vide = accessible à tous les admins</span>
      </div>
      <div class="card-pro-body">
        <p style="font-size:.8rem;color:var(--c-muted);margin-bottom:.75rem">
          Sélectionnez les administrateurs qui peuvent utiliser ce modèle de contrat.
        </p>
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:.5rem">
          @foreach($admins as $adm)
          <label style="display:flex;align-items:center;gap:.5rem;padding:.5rem .75rem;
                        border:1.5px solid var(--c-border);border-radius:var(--radius-sm);
                        cursor:pointer;font-size:.8125rem;transition:var(--transition)"
                 onmouseover="this.style.borderColor='var(--c-gold)'"
                 onmouseout="this.style.borderColor='var(--c-border)'">
            <input type="checkbox" name="assigned_admins[]" value="{{ $adm->id }}"
                   {{ in_array($adm->id, $assignedIds) ? 'checked' : '' }}
                   style="accent-color:var(--c-navy);width:15px;height:15px;cursor:pointer">
            <div>
              <div style="font-weight:500;color:var(--c-text)">{{ $adm->name }}</div>
              <div style="font-size:.7rem;color:var(--c-muted)">{{ $adm->email }}</div>
            </div>
          </label>
          @endforeach
        </div>
      </div>
    </div>
    @endif

    {{-- Contenu --}}
    <div class="card-pro mb-4">
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
        <textarea name="content" id="contractEditor"
                  style="width:100%;min-height:500px;font-family:'Courier New',monospace;font-size:.78rem;
                         line-height:1.7;padding:1.25rem;
                         border:1.5px solid var(--c-border);border-radius:var(--radius-sm);
                         background:#1A2332;color:#E2E8F0;resize:vertical"
                  onfocus="this.style.borderColor='var(--c-gold)'"
                  onblur="this.style.borderColor='var(--c-border)'"
                  >{{ old('content',$template->content) }}</textarea>
      </div>

      {{-- Mode DOCX --}}
      <div x-show="mode==='docx'" style="padding:1.25rem">

        {{-- Fichier DOCX actuel --}}
        @if($template->template_type === 'docx' && $template->docx_path)
        <div style="display:flex;align-items:center;gap:.75rem;background:var(--c-bg);
                    border:1px solid var(--c-border);border-radius:var(--radius-sm);
                    padding:.75rem 1rem;margin-bottom:1rem">
          <i class="fas fa-file-word" style="color:#2B579A;font-size:1.25rem;flex-shrink:0"></i>
          <div style="flex:1;min-width:0">
            <div style="font-size:.8125rem;font-weight:600;color:var(--c-navy)">Fichier DOCX actuel</div>
            <div style="font-size:.72rem;color:var(--c-muted);word-break:break-all">
              {{ basename($template->docx_path) }}
            </div>
          </div>
          <span class="badge-status bs-green" style="flex-shrink:0">Actif</span>
        </div>

        {{-- Balises détectées --}}
        @if($template->detected_tags)
        <div style="margin-bottom:1rem">
          <div style="font-size:.75rem;font-weight:700;color:var(--c-navy);margin-bottom:.5rem">
            <i class="fas fa-tags me-1" style="color:var(--c-gold)"></i>
            {{ count($template->detected_tags) }} balise(s) détectée(s) dans ce DOCX
          </div>
          <div style="display:flex;flex-wrap:wrap;gap:.375rem">
            @foreach($template->detected_tags as $tag)
            @php $known = array_key_exists($tag, $variables); @endphp
            <span style="font-size:.72rem;font-family:monospace;font-weight:700;
                         padding:.2rem .55rem;border-radius:5px;
                         background:{{ $known ? '#F0FDF4' : '#FFF7ED' }};
                         border:1px solid {{ $known ? '#86EFAC' : '#FED7AA' }};
                         color:{{ $known ? '#166534' : '#92400E' }}">
              {{ $tag }}
              @if(!$known)<i class="fas fa-exclamation-triangle ms-1" title="Balise non reconnue"></i>@endif
            </span>
            @endforeach
          </div>
          <p style="font-size:.7rem;color:var(--c-muted);margin-top:.5rem">
            <span style="color:#166534">■</span> Reconnue &nbsp;
            <span style="color:#92400E">■</span> Inconnue (sera laissée telle quelle)
          </p>
        </div>
        @endif
        @endif

        <label class="form-label-pro">
          {{ $template->template_type === 'docx' ? 'Remplacer par un nouveau fichier DOCX' : 'Fichier DOCX balisé' }}
        </label>
        <input type="file" name="docx_file" accept=".docx"
               class="form-control-pro" style="padding:.5rem .875rem;cursor:pointer">
        <p style="font-size:.72rem;color:var(--c-muted);margin-top:.35rem">
          <i class="fas fa-shield-alt me-1"></i>Format .docx uniquement · Max 10 Mo · Prérequis PDF : LibreOffice + LIBREOFFICE_BIN dans .env
        </p>
      </div>
    </div>

    {{-- Images du modèle --}}
    <div class="card-pro mb-4">
      <div class="card-pro-hdr">
        <div class="card-pro-title"><span class="icon-dot"></span>Images du modèle <span style="font-size:.7rem;color:var(--c-muted);font-weight:400">(filigrane, logos, cachets, signatures)</span></div>
      </div>
      <div class="card-pro-body">
        <div class="row g-3">
          @php
          $imgInputs = [
            'watermark'       => ['label' => 'Filigrane',        'icon' => 'fa-tint',     'col' => 'watermark_path'],
            'logo_left'       => ['label' => 'Logo gauche',      'icon' => 'fa-image',    'col' => 'logo_left_path'],
            'logo_right'      => ['label' => 'Logo droit',       'icon' => 'fa-image',    'col' => 'logo_right_path'],
            'stamp'           => ['label' => 'Cachet',           'icon' => 'fa-stamp',    'col' => 'stamp_path'],
            'signature_admin' => ['label' => 'Sig. société',     'icon' => 'fa-pen-nib',  'col' => 'signature_admin_path'],
            'signature_agent' => ['label' => 'Sig. agent',       'icon' => 'fa-pen-nib',  'col' => 'signature_agent_path'],
          ];
          @endphp
          @foreach($imgInputs as $field => $cfg)
          @php $existing = $template->{$cfg['col']}; @endphp
          <div class="col-md-4">
            <div style="border:1.5px dashed var(--c-border);border-radius:var(--radius-sm);padding:.875rem;text-align:center;position:relative;transition:border-color .2s"
                 onmouseover="this.style.borderColor='var(--c-gold)'" onmouseout="this.style.borderColor='var(--c-border)'">
              <div style="font-size:1rem;color:var(--c-muted);margin-bottom:.3rem"><i class="fas {{ $cfg['icon'] }}"></i></div>
              <div style="font-size:.7rem;font-weight:700;color:var(--c-navy);margin-bottom:.5rem">{{ $cfg['label'] }}</div>

              @if($existing)
              <div id="prev_{{ $field }}" style="margin-bottom:.5rem">
                <img src="{{ asset('storage/'.$existing) }}" style="max-height:52px;max-width:100%;object-fit:contain;border-radius:4px">
                <div style="margin-top:.3rem">
                  <label style="font-size:.62rem;color:#DC2626;cursor:pointer;display:inline-flex;align-items:center;gap:.25rem">
                    <input type="checkbox" name="remove_{{ $field }}" value="1"> Supprimer
                  </label>
                </div>
              </div>
              @else
              <div id="prev_{{ $field }}" style="margin-bottom:.5rem;display:none">
                <img style="max-height:52px;max-width:100%;object-fit:contain;border-radius:4px">
              </div>
              @endif

              <label style="display:inline-flex;align-items:center;gap:.3rem;padding:.25rem .65rem;background:var(--c-bg);border:1px solid var(--c-border);border-radius:5px;font-size:.68rem;cursor:pointer;color:var(--c-navy);font-weight:600">
                <i class="fas fa-{{ $existing ? 'sync' : 'upload' }}" style="font-size:.62rem"></i>
                {{ $existing ? 'Remplacer' : 'Choisir' }}
                <input type="file" name="{{ $field }}" accept="image/*" style="display:none"
                       onchange="previewImg(this,'prev_{{ $field }}')">
              </label>
            </div>
          </div>
          @endforeach
        </div>
      </div>
    </div>

    <div class="d-flex justify-content-end gap-3">
      <a href="{{ route('admin.contract-templates.index') }}" class="btn-ghost">Annuler</a>
      <button type="submit" class="btn-navy"><i class="fas fa-save"></i> Enregistrer les modifications</button>
    </div>
    </form>
  </div>

  {{-- Référence variables --}}
  <div class="col-xl-4">
    <div class="card-pro" style="position:sticky;top:80px">
      <div class="card-pro-hdr">
        <div class="card-pro-title"><span class="icon-dot"></span>Balises disponibles</div>
      </div>
      <div style="max-height:calc(100vh - 200px);overflow-y:auto;padding:.75rem">
        @foreach($variables as $var=>$desc)
        <div style="display:flex;align-items:center;gap:.5rem;padding:.5rem .625rem;
                    border-radius:var(--radius-sm);margin-bottom:.25rem;
                    border:1px solid var(--c-border);background:var(--c-bg);transition:var(--transition)"
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
  if (!ta) return;
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
    const img = div.querySelector('img');
    if (img) { img.src = e.target.result; div.style.display = 'block'; }
  };
  reader.readAsDataURL(input.files[0]);
}
</script>
@endpush
