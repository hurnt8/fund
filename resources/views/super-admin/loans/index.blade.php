@extends('layouts.dashboard')
@section('title','Toutes les demandes')
@section('page_title','Vue globale — Demandes de prêt')

@section('content')

<div class="page-hdr-row">
  <div class="page-hdr">
    <h1>Toutes les demandes de prêt</h1>
    <p>Supervision globale · Tous les administrateurs · Toutes les agences</p>
  </div>
  <div class="page-hdr-actions">
    <a href="{{ route('super-admin.loans.create') }}" class="btn-navy btn-sm-pro">
      <i class="fas fa-plus"></i> Nouvelle demande
    </a>
  </div>
</div>

{{-- KPI --}}
<div class="row g-3 mb-4">
  @php $kpis = [
    ['Total','total','fa-layer-group','mi-navy'],
    ['Brouillons','draft','fa-pen','mi-gray'],
    ['En attente','pending','fa-hourglass-half','mi-amber'],
    ['Validées','validated','fa-check-circle','mi-blue'],
    ['Finalisées','finalized','fa-flag-checkered','mi-green'],
    ['Rejetées','rejected','fa-ban','mi-red'],
  ]; @endphp
  @foreach($kpis as [$l,$k,$i,$c])
  <div class="col-6 col-md-4 col-lg-2">
    <div class="metric-card">
      <div class="metric-card__icon {{ $c }}"><i class="fas {{ $i }}"></i></div>
      <div class="metric-card__val">{{ $stats[$k] }}</div>
      <div class="metric-card__lbl">{{ $l }}</div>
    </div>
  </div>
  @endforeach
</div>

{{-- Filtres --}}
<div class="filter-bar">
  <form method="GET" class="d-flex flex-wrap gap-2 align-items-center w-100">
    <input type="text" name="search" placeholder="Référence, nom, email…"
           value="{{ request('search') }}" style="min-width:180px;flex:1">
    <select name="admin_id" style="min-width:180px">
      <option value="">Tous les admins</option>
      @foreach($admins as $a)
      <option value="{{ $a->id }}" {{ request('admin_id')==$a->id?'selected':'' }}>{{ $a->name }}</option>
      @endforeach
    </select>
    <select name="status" style="min-width:160px">
      <option value="">Tous les statuts</option>
      @foreach(\App\Models\LoanRequest::STATUSES as $s)
      <option value="{{ $s }}" {{ request('status')===$s?'selected':'' }}>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
      @endforeach
    </select>
    <button type="submit" class="btn-navy btn-sm-pro">
      <i class="fas fa-filter"></i> Filtrer
    </button>
    @if(request()->anyFilled(['search','admin_id','status']))
    <a href="{{ route('super-admin.loans.index') }}" class="btn-ghost btn-sm-pro">
      <i class="fas fa-times"></i> Réinitialiser
    </a>
    @endif
  </form>
</div>

<div class="card-pro">
  <div class="table-responsive-pro">
    <table class="pro-table">
      <thead>
        <tr>
          <th>Référence</th><th>Client</th><th>Admin responsable</th>
          <th>Montant</th><th>Mensualité</th><th>Statut</th><th>Date</th><th style="text-align:right"></th>
        </tr>
      </thead>
      <tbody>
        @forelse($loans as $loan)
        <tr>
          <td data-label="Référence"><span class="cell-mono">{{ $loan->reference }}</span></td>
          <td data-label="Client">
            <div class="cell-name">{{ $loan->name }}</div>
            <div class="cell-sub">{{ $loan->email }}</div>
          </td>
          <td data-label="Admin">
            <div style="display:flex;align-items:center;gap:.5rem">
              <div style="width:26px;height:26px;border-radius:50%;background:var(--c-bg);border:1.5px solid var(--c-border);display:flex;align-items:center;justify-content:center;font-size:.7rem;font-weight:700;color:var(--c-navy)">
                {{ strtoupper(substr($loan->admin?->name??'?',0,1)) }}
              </div>
              <span style="font-size:.8125rem">{{ $loan->admin?->name ?? '—' }}</span>
            </div>
          </td>
          <td data-label="Montant" class="cell-amount">{{ number_format($loan->amount,2,',',' ') }} <span style="font-size:.75rem;color:var(--c-muted)">{{ $loan->currency }}</span></td>
          <td data-label="Mensualité" style="font-weight:600">{{ number_format($loan->monthly_payment,2,',',' ') }} {{ $loan->currency }}</td>
          <td data-label="Statut"><span class="badge-status bs-{{ $loan->statusColor() }}">{{ $loan->statusLabel() }}</span></td>
          <td data-label="Date" style="color:var(--c-muted);font-size:.78rem">{{ $loan->created_at->format('d/m/Y') }}</td>
          <td data-label="Actions" style="text-align:right">
            <div style="display:inline-flex;gap:.25rem">
              <a href="{{ route('super-admin.loans.show',$loan) }}" class="btn-icon btn-icon-primary" title="Voir le dossier">
                <i class="fas fa-eye"></i>
              </a>
              <a href="{{ route('super-admin.loans.edit',$loan) }}" class="btn-icon" title="Modifier">
                <i class="fas fa-pen"></i>
              </a>
              <a href="{{ route('super-admin.loans.contract',$loan) }}" class="btn-icon" title="Contrat" style="color:var(--c-gold)">
                <i class="fas fa-file-contract"></i>
              </a>
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="8">
            <div style="text-align:center;padding:3rem;color:var(--c-muted)">
              <i class="fas fa-search" style="font-size:2.5rem;opacity:.2;display:block;margin-bottom:.75rem"></i>
              Aucune demande ne correspond aux critères de recherche.
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
