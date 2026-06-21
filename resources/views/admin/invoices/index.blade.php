@extends('layouts.dashboard')
@section('title', 'Factures — Credixa')
@section('page_title', 'Factures')

@section('content')
@php $totalRevenue = $stats['paid_amount'] ?? 0; @endphp

{{-- Page header ── --}}
<div class="page-hdr">
  <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.75rem">
    <h1>Factures</h1>
    <a href="{{ route('admin.invoices.create') }}" class="btn-gold">
      <i class="fas fa-plus"></i> Nouvelle facture
    </a>
  </div>
  <p>Gestion et suivi de la facturation clients</p>
</div>

@if(session('success'))
<div class="flash flash-ok" style="margin-bottom:1.25rem"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
@endif

{{-- KPI ── --}}
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:1rem;margin-bottom:1.75rem">
  <div class="metric-card">
    <div class="metric-card__icon mi-navy"><i class="fas fa-file-invoice"></i></div>
    <div class="metric-card__val">{{ $stats['total'] }}</div>
    <div class="metric-card__lbl">Total</div>
    <div class="metric-card__accent" style="background:var(--c-navy)"></div>
  </div>
  <div class="metric-card">
    <div class="metric-card__icon mi-gray"><i class="fas fa-pencil-ruler"></i></div>
    <div class="metric-card__val" style="color:var(--c-muted)">{{ $stats['draft'] }}</div>
    <div class="metric-card__lbl">Brouillons</div>
    <div class="metric-card__accent" style="background:var(--c-muted)"></div>
  </div>
  <div class="metric-card">
    <div class="metric-card__icon mi-blue"><i class="fas fa-paper-plane"></i></div>
    <div class="metric-card__val" style="color:var(--c-blue)">{{ $stats['sent'] }}</div>
    <div class="metric-card__lbl">Envoyées</div>
    <div class="metric-card__accent" style="background:var(--c-blue)"></div>
  </div>
  <div class="metric-card">
    <div class="metric-card__icon mi-green"><i class="fas fa-circle-check"></i></div>
    <div class="metric-card__val" style="color:var(--c-green)">{{ $stats['paid'] }}</div>
    <div class="metric-card__lbl">Payées</div>
    <div class="metric-card__accent" style="background:var(--c-green)"></div>
  </div>
  <div class="metric-card">
    <div class="metric-card__icon mi-gold"><i class="fas fa-coins"></i></div>
    <div class="metric-card__val" style="font-size:1.25rem">{{ number_format($totalRevenue, 0, ',', ' ') }}</div>
    <div class="metric-card__lbl">Revenu encaissé</div>
    <div class="metric-card__accent" style="background:var(--c-gold)"></div>
  </div>
</div>

{{-- Filters ── --}}
<div class="card-pro" style="margin-bottom:1.25rem">
  <div class="card-pro-body" style="padding:.75rem 1.25rem">
    <form method="GET" style="display:flex;gap:.625rem;flex-wrap:wrap;align-items:center">
      <div style="position:relative;flex:1;min-width:200px">
        <i class="fas fa-search" style="position:absolute;left:.75rem;top:50%;transform:translateY(-50%);color:var(--c-muted);font-size:.75rem;pointer-events:none"></i>
        <input type="text" name="search" value="{{ request('search') }}"
          placeholder="Référence, client…" class="form-control-pro" style="padding-left:2.25rem">
      </div>
      <select name="status" class="form-control-pro" style="width:auto;min-width:160px" onchange="this.form.submit()">
        <option value="">Tous les statuts</option>
        @foreach(['draft'=>'Brouillon','sent'=>'Envoyée','paid'=>'Payée','cancelled'=>'Annulée'] as $v => $l)
        <option value="{{ $v }}" {{ request('status') === $v ? 'selected':'' }}>{{ $l }}</option>
        @endforeach
      </select>
      <button type="submit" class="btn-navy btn-sm-pro"><i class="fas fa-search"></i> Filtrer</button>
      @if(request()->hasAny(['search','status']))
      <a href="{{ route('admin.invoices.index') }}" class="btn-ghost btn-sm-pro">
        <i class="fas fa-times"></i> Réinitialiser
      </a>
      @endif
    </form>
  </div>
</div>

{{-- Table ── --}}
<div class="card-pro">
  @if($invoices->isEmpty())
    <div style="text-align:center;padding:5rem 2rem;color:var(--c-muted)">
      <i class="fas fa-file-invoice" style="font-size:2.5rem;display:block;margin-bottom:1rem;opacity:.2"></i>
      <p style="font-size:.9375rem;font-weight:600">Aucune facture pour le moment.</p>
    </div>
  @else
  <div style="overflow-x:auto">
    <table class="pro-table">
      <thead>
        <tr>
          <th>Référence</th>
          <th>Client</th>
          <th>Émission</th>
          <th>Échéance</th>
          <th>Montant TTC</th>
          <th>Statut</th>
          <th style="text-align:right">Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach($invoices as $inv)
        @php
          $isOverdue  = $inv->due_date && $inv->due_date->isPast() && !$inv->isPaid() && !$inv->isCancelled();
          $badgeClass = match($inv->statusColor()) {
            'gray'      => 'bs-gray',
            'blue'      => 'bs-blue',
            'info'      => 'bs-blue',
            'green'     => 'bs-green',
            'success'   => 'bs-green',
            'red'       => 'bs-red',
            'danger'    => 'bs-red',
            default     => 'bs-gray',
          };
        @endphp
        <tr>
          <td class="cell-mono">{{ $inv->reference }}</td>
          <td>
            <div class="cell-name">{{ $inv->client->name }}</div>
            <div class="cell-sub">{{ $inv->client->email }}</div>
          </td>
          <td style="white-space:nowrap;color:var(--c-muted);font-size:.8125rem">
            {{ $inv->issue_date->format('d/m/Y') }}
          </td>
          <td style="white-space:nowrap">
            @if($inv->due_date)
              <span style="{{ $isOverdue ? 'color:var(--c-red);font-weight:700' : 'color:var(--c-muted)' }};font-size:.8125rem">
                {{ $inv->due_date->format('d/m/Y') }}
              </span>
              @if($isOverdue)
                <span class="badge-status bs-red" style="font-size:.6rem;padding:.1rem .45rem;margin-left:.35rem">En retard</span>
              @endif
            @else
              <span style="color:var(--c-muted)">—</span>
            @endif
          </td>
          <td>
            <span style="font-family:'Space Grotesk',sans-serif;font-weight:800;font-size:.9375rem;color:var(--c-navy)">
              {{ number_format($inv->total, 2, ',', ' ') }}
            </span>
            <span style="font-size:.75rem;color:var(--c-muted)"> {{ $inv->currency }}</span>
          </td>
          <td>
            <span class="badge-status {{ $badgeClass }}">{{ $inv->statusLabel() }}</span>
          </td>
          <td style="text-align:right">
            <div style="display:inline-flex;gap:.375rem">
              <a href="{{ route('admin.invoices.show', $inv) }}" class="btn-icon btn-icon-primary" title="Voir">
                <i class="fas fa-eye"></i>
              </a>
              @if($inv->isDraft())
              <a href="{{ route('admin.invoices.edit', $inv) }}" class="btn-icon" title="Modifier">
                <i class="fas fa-pen"></i>
              </a>
              @endif
            </div>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  @if($invoices->hasPages())
  <div style="padding:1rem 1.25rem;border-top:1px solid var(--c-border)">
    {{ $invoices->links() }}
  </div>
  @endif
  @endif
</div>

@endsection
