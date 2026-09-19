@extends('layouts.dashboard')
@section('title','Modèles de contrats')
@section('page_title','Modèles de contrats')

@section('content')

<div class="d-flex align-items-start justify-content-between flex-wrap gap-3 page-hdr">
  <div>
    <h4>Modèles de contrats</h4>
    <p>Templates utilisés pour la génération automatique des contrats — PDF (HTML) et DOCX Word.</p>
  </div>
  <a href="{{ route('admin.contract-templates.create') }}" class="btn-navy">
    <i class="fas fa-plus"></i> Nouveau modèle
  </a>
</div>

@if(session('success'))
<div class="flash flash-ok mb-4"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
@endif

@if(!$templates->count())
<div class="card-pro" style="text-align:center;padding:4rem 2rem">
  <i class="fas fa-file-signature" style="font-size:3rem;color:var(--c-gold);opacity:.35;display:block;margin-bottom:1rem"></i>
  <p style="font-weight:600;color:var(--c-navy);font-size:1rem;margin-bottom:.35rem">Aucun modèle disponible</p>
  <p style="color:var(--c-muted);font-size:.8375rem;margin-bottom:1.25rem">
    Créez votre premier template pour personnaliser les contrats générés.
  </p>
  <a href="{{ route('admin.contract-templates.create') }}" class="btn-navy">
    <i class="fas fa-plus"></i> Créer un modèle
  </a>
</div>
@else
<div class="row g-4">
  @foreach($templates as $t)
  @php
    $hasDocx  = $t->hasDocxTemplate();
    $docxVars = $t->docx_detected_vars ?? [];
  @endphp
  <div class="col-md-6 col-xl-4">
    <div class="card-pro h-100 d-flex flex-column"
         style="{{ $t->is_default ? 'border-color:var(--c-gold);box-shadow:0 0 0 1px var(--c-gold)' : '' }}">

      {{-- En-tête --}}
      <div class="card-pro-hdr" style="flex-shrink:0">
        <div style="display:flex;align-items:center;gap:.75rem;min-width:0;flex:1">
          <div style="width:38px;height:38px;border-radius:9px;background:{{ $t->is_default?'var(--c-gold)':'#EEF2FF' }};display:flex;align-items:center;justify-content:center;flex-shrink:0">
            <i class="fas fa-file-contract" style="color:var(--c-navy);font-size:.875rem"></i>
          </div>
          <div style="min-width:0;flex:1">
            <div style="font-weight:700;color:var(--c-navy);font-size:.875rem;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">
              {{ $t->name }}
            </div>
            <div style="display:flex;align-items:center;gap:.35rem;margin-top:.25rem;flex-wrap:wrap">
              @if($t->is_default)
              <span class="badge-status bs-amber" style="font-size:.62rem">Par défaut</span>
              @endif
              {{-- Badge DOCX --}}
              @if($hasDocx)
              <span style="font-size:.62rem;padding:.1rem .4rem;border-radius:4px;font-weight:700;
                           background:#EFF6FF;color:#1D4ED8;border:1px solid #1D4ED833">
                <i class="fas fa-file-word" style="margin-right:.2rem"></i>DOCX v{{ $t->docx_version }}
              </span>
              @endif
              @if($t->locale)
              <span style="font-size:.62rem;color:var(--c-muted)">{{ strtoupper($t->locale) }}</span>
              @endif
            </div>
          </div>
        </div>
      </div>

      {{-- Corps --}}
      <div class="card-pro-body" style="flex:1;display:flex;flex-direction:column;gap:.6rem">

        {{-- Méta --}}
        <div style="font-size:.73rem;color:var(--c-muted)">
          <i class="fas fa-user me-1"></i> {{ $t->creator?->name ?? 'Système' }}
          <span style="margin:0 .4rem">·</span>
          <i class="fas fa-calendar me-1"></i> {{ $t->created_at->format('d/m/Y') }}
        </div>

        @if(auth()->user()->hasRole('super-admin') && isset($t->assignedAdmins))
          @php $assigned = $t->assignedAdmins; @endphp
          <div style="font-size:.72rem;color:var(--c-muted)">
            @if($assigned->isEmpty())
              <i class="fas fa-users me-1"></i> <em>Tous les admins</em>
            @else
              <i class="fas fa-user-check me-1" style="color:var(--c-navy)"></i>
              {{ $assigned->pluck('name')->join(', ') }}
            @endif
          </div>
        @endif

        {{-- Statut DOCX --}}
        @if($hasDocx)
        <div style="padding:.55rem .75rem;background:#F0FDF4;border:1px solid #86EFAC;border-radius:8px">
          <div style="font-size:.7rem;font-weight:700;color:#166534;margin-bottom:.3rem">
            <i class="fas fa-file-word me-1"></i>Template DOCX actif — v{{ $t->docx_version }}
          </div>
          @if(count($docxVars) > 0)
          <div style="display:flex;flex-wrap:wrap;gap:.2rem">
            @foreach(array_slice($docxVars, 0, 8) as $var)
            <code style="font-size:.6rem;padding:.08rem .3rem;border-radius:3px;
                         background:#fff;border:1px solid #86EFAC;color:#166534">{{"{"}}{{ $var }}{{"}"}}</code>
            @endforeach
            @if(count($docxVars) > 8)
            <span style="font-size:.62rem;color:#166534">+{{ count($docxVars)-8 }}</span>
            @endif
          </div>
          @endif
        </div>
        @else
        <div style="padding:.45rem .75rem;background:#FEF9C3;border:1px solid #FDE047;
                    border-radius:8px;font-size:.7rem;color:#713F12">
          <i class="fas fa-exclamation-triangle me-1"></i>
          Aucun template DOCX — uploadez un fichier .docx dans la configuration
        </div>
        @endif

      </div>

      {{-- Actions --}}
      <div style="padding:.875rem 1.25rem;border-top:1px solid var(--c-border);
                  display:flex;gap:.4rem;flex-wrap:wrap;align-items:center">
        @if($hasDocx)
        <a href="{{ route('admin.contract-templates.docx.download', $t) }}"
           class="btn-ghost btn-sm-pro" title="Télécharger le template DOCX">
          <i class="fas fa-download"></i>
        </a>
        <a href="{{ route('admin.contract-templates.docx.preview', $t) }}"
           class="btn-ghost btn-sm-pro" target="_blank" title="Aperçu DOCX avec données demo">
          <i class="fas fa-file-word"></i>
        </a>
        @endif
        <a href="{{ route('admin.contract-templates.edit',$t) }}"
           class="btn-navy btn-sm-pro" style="flex:1;justify-content:center;min-width:70px">
          <i class="fas fa-pen"></i> Modifier
        </a>
        @if(!$t->is_default)
        <form action="{{ route('admin.contract-templates.destroy',$t) }}" method="POST"
              data-confirm="Supprimer ce modèle de contrat ?" data-confirm-danger>
          @csrf @method('DELETE')
          <button class="btn-icon btn-icon-danger" title="Supprimer">
            <i class="fas fa-trash"></i>
          </button>
        </form>
        @endif
      </div>

    </div>
  </div>
  @endforeach
</div>
@endif

<div style="margin-top:1.25rem">
  <x-pagination :paginator="$templates" />
</div>

@endsection
