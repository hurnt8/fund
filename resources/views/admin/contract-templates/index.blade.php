@extends('layouts.dashboard')
@section('title','Modèles de contrats')
@section('page_title','Modèles de contrats')

@section('content')

<div class="d-flex align-items-start justify-content-between flex-wrap gap-3 page-hdr">
  <div>
    <h4>Modèles de contrats</h4>
    <p>Templates utilisés pour la génération automatique des contrats PDF</p>
  </div>
  <a href="{{ route('admin.contract-templates.create') }}" class="btn-navy">
    <i class="fas fa-plus"></i> Nouveau modèle
  </a>
</div>

@if(!$templates->count())
<div class="card-pro" style="text-align:center;padding:4rem 2rem">
  <i class="fas fa-file-signature" style="font-size:3rem;color:var(--c-gold);opacity:.35;display:block;margin-bottom:1rem"></i>
  <p style="font-weight:600;color:var(--c-navy);font-size:1rem;margin-bottom:.35rem">Aucun modèle disponible</p>
  <p style="color:var(--c-muted);font-size:.8375rem;margin-bottom:1.25rem">Créez votre premier template pour générer des contrats personnalisés.</p>
  <a href="{{ route('admin.contract-templates.create') }}" class="btn-navy">
    <i class="fas fa-plus"></i> Créer un modèle
  </a>
</div>
@else
<div class="row g-4">
  @foreach($templates as $t)
  <div class="col-md-6 col-xl-4">
    <div class="card-pro h-100 d-flex flex-column"
         style="{{ $t->is_default ? 'border-color:var(--c-gold);box-shadow:0 0 0 1px var(--c-gold)' : '' }}">
      <div class="card-pro-hdr" style="flex-shrink:0">
        <div style="display:flex;align-items:center;gap:.75rem;min-width:0">
          <div style="width:38px;height:38px;border-radius:9px;background:{{ $t->is_default?'var(--c-gold)':'#EEF2FF' }};display:flex;align-items:center;justify-content:center;flex-shrink:0">
            <i class="fas fa-file-contract" style="color:{{ $t->is_default?'var(--c-navy)':'var(--c-navy)' }};font-size:.875rem"></i>
          </div>
          <div style="min-width:0">
            <div style="font-weight:700;color:var(--c-navy);font-size:.875rem;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $t->name }}</div>
            @if($t->is_default)
            <span class="badge-status bs-amber" style="font-size:.65rem">Modèle par défaut</span>
            @endif
          </div>
        </div>
      </div>
      <div class="card-pro-body" style="flex:1">
        <div style="font-size:.78rem;color:var(--c-muted);margin-bottom:.5rem">
          <i class="fas fa-user me-1"></i> {{ $t->creator?->name ?? 'Système' }}
        </div>
        <div style="font-size:.78rem;color:var(--c-muted)">
          <i class="fas fa-calendar me-1"></i> Créé le {{ $t->created_at->format('d/m/Y') }}
        </div>
        @if(auth()->user()->hasRole('super-admin') && isset($t->assignedAdmins))
          @php $assigned = $t->assignedAdmins; @endphp
          <div style="margin-top:.5rem;font-size:.75rem;color:var(--c-muted)">
            @if($assigned->isEmpty())
              <i class="fas fa-users me-1"></i> <em>Tous les admins</em>
            @else
              <i class="fas fa-user-check me-1" style="color:var(--c-navy)"></i>
              {{ $assigned->pluck('name')->join(', ') }}
            @endif
          </div>
        @endif
        <div style="margin-top:1rem;padding:.75rem;background:var(--c-bg);border-radius:var(--radius-sm);font-size:.72rem;color:var(--c-muted);font-family:monospace;overflow:hidden;max-height:54px">
          {{ Str::limit(strip_tags($t->content),120) }}
        </div>
      </div>
      <div style="padding:.875rem 1.25rem;border-top:1px solid var(--c-border);display:flex;gap:.5rem">
        <a href="{{ route('admin.contract-templates.preview',$t) }}"
           class="btn-ghost btn-sm-pro" style="flex:1;justify-content:center" target="_blank">
          <i class="fas fa-eye"></i> Aperçu
        </a>
        <a href="{{ route('admin.contract-templates.edit',$t) }}"
           class="btn-navy btn-sm-pro" style="flex:1;justify-content:center">
          <i class="fas fa-pen"></i> Modifier
        </a>
        @if(!$t->is_default)
        <form action="{{ route('admin.contract-templates.destroy',$t) }}" method="POST"
              onsubmit="return confirm('Supprimer ce modèle de contrat ?')">
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

@endsection
