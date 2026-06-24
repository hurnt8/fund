@extends('layouts.dashboard')
@section('title','Nouveau modèle de contrat')
@section('page_title','Nouveau modèle de contrat')

@section('content')

<div class="d-flex align-items-start justify-content-between flex-wrap gap-3 page-hdr">
  <div>
    <h4>Nouveau modèle de contrat</h4>
    <p>Créez le modèle, puis uploadez votre fichier <strong>.docx</strong> depuis la page de modification.</p>
  </div>
  <a href="{{ route('admin.contract-templates.index') }}" class="btn-ghost btn-sm-pro">
    <i class="fas fa-arrow-left"></i> Retour
  </a>
</div>

@if($errors->any())
<div class="flash flash-err mb-4"><i class="fas fa-exclamation-triangle"></i> {{ $errors->first() }}</div>
@endif

<div style="max-width:640px;margin:0 auto">

  <form action="{{ route('admin.contract-templates.store') }}" method="POST">
  @csrf

  <div class="card-pro mb-4">
    <div class="card-pro-hdr">
      <div class="card-pro-title"><span class="icon-dot"></span>Informations</div>
    </div>
    <div class="card-pro-body">
      <div class="row g-3">
        <div class="col-sm-8">
          <label class="form-label-pro">Nom du modèle *</label>
          <input type="text" name="name" class="form-control-pro"
                 value="{{ old('name') }}" placeholder="Ex: Contrat Standard — Pologne" required>
        </div>
        <div class="col-sm-4">
          <label class="form-label-pro">Langue</label>
          <select name="locale" class="form-control-pro">
            <option value="">Automatique</option>
            @foreach(['fr'=>'Français','en'=>'English','pl'=>'Polski','es'=>'Español'] as $code=>$lbl)
            <option value="{{ $code }}" {{ old('locale')==$code?'selected':'' }}>{{ $lbl }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-12">
          <label style="display:flex;align-items:center;gap:.5rem;cursor:pointer;font-size:.85rem">
            <input type="checkbox" name="is_default" value="1" {{ old('is_default')?'checked':'' }}
                   style="width:16px;height:16px;accent-color:var(--c-navy)">
            <span>Définir comme modèle par défaut</span>
          </label>
        </div>
      </div>
    </div>
  </div>

  {{-- Notice DOCX --}}
  <div style="display:flex;align-items:flex-start;gap:.875rem;padding:1rem 1.125rem;
              background:#EFF6FF;border:1px solid #BFDBFE;border-radius:10px;margin-bottom:1.5rem">
    <i class="fas fa-file-word" style="color:#1D4ED8;font-size:1.25rem;margin-top:.05rem;flex-shrink:0"></i>
    <div>
      <div style="font-size:.8375rem;font-weight:700;color:#1D4ED8;margin-bottom:.3rem">
        Étape suivante : uploader votre fichier .docx
      </div>
      <div style="font-size:.75rem;color:#1e40af;line-height:1.6">
        Après la création, ouvrez ce modèle en modification pour uploader votre fichier Word.
        Le système détectera automatiquement toutes les variables <code>{balise}</code>
        présentes dans le document et les remplacera avec les données du dossier de prêt.
      </div>
    </div>
  </div>

  <div style="display:flex;gap:.75rem;justify-content:flex-end">
    <a href="{{ route('admin.contract-templates.index') }}" class="btn-ghost">Annuler</a>
    <button type="submit" class="btn-navy">
      <i class="fas fa-save me-1"></i>Créer et configurer le DOCX
    </button>
  </div>
  </form>

</div>

@endsection
