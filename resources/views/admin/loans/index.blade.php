@extends('layouts.dashboard')
@section('title','Demandes de prêt')
@section('page_title','Demandes de prêt')

@section('content')

{{-- En-tête --}}
<div class="d-flex align-items-start justify-content-between flex-wrap gap-3 page-hdr">
  <div>
    <h4>Demandes de prêt</h4>
    <p>Gérez et suivez tous vos dossiers clients</p>
  </div>
  <a href="{{ route('admin.loans.create') }}" class="btn-navy">
    <i class="fas fa-plus"></i> Nouvelle demande
  </a>
</div>

{{-- KPI --}}
<div class="row g-3 mb-4">
  @php
  $kpis = [
    ['label'=>'Total dossiers',  'val'=>$stats['total'],     'icon'=>'fa-layer-group',        'cls'=>'mi-navy'],
    ['label'=>'Brouillons',      'val'=>$stats['draft'],     'icon'=>'fa-pen',                'cls'=>'mi-gray'],
    ['label'=>'En attente',      'val'=>$stats['pending'],   'icon'=>'fa-hourglass-half',     'cls'=>'mi-amber'],
    ['label'=>'Validées',        'val'=>$stats['validated'], 'icon'=>'fa-check-circle',       'cls'=>'mi-blue'],
    ['label'=>'Finalisées',      'val'=>$stats['finalized'], 'icon'=>'fa-flag-checkered',     'cls'=>'mi-green'],
  ];
  @endphp
  @foreach($kpis as $k)
  <div class="col-6 col-sm-4 col-lg">
    <div class="metric-card">
      <div class="metric-card__icon {{ $k['cls'] }}"><i class="fas {{ $k['icon'] }}"></i></div>
      <div class="metric-card__val">{{ $k['val'] }}</div>
      <div class="metric-card__lbl">{{ $k['label'] }}</div>
    </div>
  </div>
  @endforeach
</div>

{{-- Filtres --}}
<div class="filter-bar">
  <form method="GET" class="d-flex flex-wrap gap-2 align-items-center w-100">
    <input type="text" name="search" placeholder="&#xf002;  Référence, nom, email…"
           value="{{ request('search') }}" style="min-width:220px;flex:1">
    <select name="status" style="min-width:180px">
      <option value="">Tous les statuts</option>
      @foreach(\App\Models\LoanRequest::STATUSES as $s)
      <option value="{{ $s }}" {{ request('status')===$s?'selected':'' }}>
        {{ ucfirst(str_replace('_',' ',$s)) }}
      </option>
      @endforeach
    </select>
    <button type="submit" class="btn-navy btn-sm-pro">
      <i class="fas fa-filter"></i> Filtrer
    </button>
    @if(request()->anyFilled(['search','status']))
    <a href="{{ route('admin.loans.index') }}" class="btn-ghost btn-sm-pro">
      <i class="fas fa-times"></i> Réinitialiser
    </a>
    @endif
  </form>
</div>

{{-- Tableau --}}
<div class="card-pro">
  <div class="table-responsive">
    <table class="pro-table w-100">
      <thead>
        <tr>
          <th>Référence</th>
          <th>Client</th>
          <th>Montant</th>
          <th>Durée</th>
          <th>Mensualité</th>
          <th>Statut</th>
          <th>Créé le</th>
          <th style="text-align:right">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($loans as $loan)
        <tr>
          <td><span class="cell-mono">{{ $loan->reference }}</span></td>
          <td>
            <div class="cell-name">{{ $loan->name }}</div>
            <div class="cell-sub">{{ $loan->email }}</div>
          </td>
          <td class="cell-amount">{{ number_format($loan->amount,2,',',' ') }} <span style="font-size:.75rem;color:var(--c-muted);font-weight:500">{{ $loan->currency }}</span></td>
          <td>{{ $loan->darly }} mois</td>
          <td style="font-weight:600;color:var(--c-navy)">{{ number_format($loan->monthly_payment,2,',',' ') }} {{ $loan->currency }}</td>
          <td>
            <span class="badge-status bs-{{ $loan->statusColor() }}">{{ $loan->statusLabel() }}</span>
          </td>
          <td style="color:var(--c-muted);font-size:.78rem">{{ $loan->created_at->format('d/m/Y') }}</td>
          <td>
            <div class="d-flex gap-1 justify-content-end">
              <a href="{{ route('admin.loans.show',$loan) }}" class="btn-icon btn-icon-primary" title="Voir le dossier">
                <i class="fas fa-eye"></i>
              </a>
              @if($loan->isEditable())
              <a href="{{ route('admin.loans.edit',$loan) }}" class="btn-icon" title="Modifier">
                <i class="fas fa-pen"></i>
              </a>
              @endif
              <a href="{{ route('admin.loans.contract',$loan) }}" class="btn-icon" title="Contrat">
                <i class="fas fa-file-contract"></i>
              </a>
              @if($loan->canBeValidated())
              <form action="{{ route('admin.loans.validate',$loan) }}" method="POST"
                    onsubmit="return confirm('Valider et envoyer le contrat ?')">
                @csrf
                <button class="btn-icon btn-icon-success" title="Valider & Envoyer">
                  <i class="fas fa-paper-plane"></i>
                </button>
              </form>
              @endif
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="8">
            <div style="text-align:center;padding:3rem 1rem;color:var(--c-muted)">
              <i class="fas fa-folder-open" style="font-size:2.5rem;opacity:.25;display:block;margin-bottom:.75rem"></i>
              Aucune demande trouvée.<br>
              <a href="{{ route('admin.loans.create') }}" style="color:var(--c-gold);font-weight:600">Créer votre première demande →</a>
            </div>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($loans->hasPages())
  <div style="padding:.875rem 1.25rem;border-top:1px solid var(--c-border)">
    {{ $loans->links() }}
  </div>
  @endif
</div>

@endsection
