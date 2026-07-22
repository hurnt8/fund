@extends('layouts.dashboard')
@section('title', 'Comptes clients — Credixa')
@section('page_title', 'Comptes clients')

@section('content')
@php
  $defaultCur = config('credixa.default_currency');
@endphp

{{-- Page header ── --}}
<div class="page-hdr-row">
  <div class="page-hdr">
    <div style="display:flex;align-items:center;gap:.75rem;flex-wrap:wrap">
      <h1>Comptes clients</h1>
      @if($isSuperAdmin)
        <span class="badge-status bs-violet"><i class="fas fa-shield-alt" style="font-size:.6rem"></i> Vue globale</span>
      @endif
    </div>
    <p>{{ $clients->total() }} compte{{ $clients->total() > 1 ? 's' : '' }} géré{{ $clients->total() > 1 ? 's' : '' }}</p>
  </div>
</div>

{{-- KPI ── --}}
<div class="metrics-grid">
  <div class="metric-card">
    <div class="metric-card__icon mi-navy"><i class="fas fa-users"></i></div>
    <div class="metric-card__val">{{ $clients->total() }}</div>
    <div class="metric-card__lbl">Comptes total</div>
    <div class="metric-card__accent" style="background:var(--c-navy)"></div>
  </div>
  <div class="metric-card">
    <div class="metric-card__icon mi-green"><i class="fas fa-arrow-trend-up"></i></div>
    <div class="metric-card__val" style="color:var(--c-green)">{{ $positiveCount }}</div>
    <div class="metric-card__lbl">Solde positif</div>
    <div class="metric-card__accent" style="background:var(--c-green)"></div>
  </div>
  <div class="metric-card">
    <div class="metric-card__icon mi-red"><i class="fas fa-arrow-trend-down"></i></div>
    <div class="metric-card__val" style="color:var(--c-red)">{{ $negativeCount }}</div>
    <div class="metric-card__lbl">Solde négatif</div>
    <div class="metric-card__accent" style="background:var(--c-red)"></div>
  </div>
  <div class="metric-card">
    <div class="metric-card__icon mi-gold"><i class="fas fa-coins"></i></div>
    <div class="metric-card__val" style="font-size:1.25rem">{{ number_format($totalBalance, 0, ',', ' ') }}</div>
    <div class="metric-card__lbl">Solde consolidé ({{ $defaultCur }})</div>
    <div class="metric-card__accent" style="background:var(--c-gold)"></div>
  </div>
</div>

{{-- Search + sort bar ── --}}
<form method="GET" action="{{ route('admin.accounts.index') }}" class="filter-bar" style="margin-bottom:1.25rem">
  <div style="position:relative;flex:1;min-width:180px">
    <i class="fas fa-search" style="position:absolute;left:.75rem;top:50%;transform:translateY(-50%);color:var(--c-muted);font-size:.75rem;pointer-events:none"></i>
    <input type="text" name="search" placeholder="Nom, e-mail, IBAN…" class="form-control-pro" style="padding-left:2.25rem" value="{{ request('search') }}">
  </div>
  <button type="submit" class="btn-navy btn-sm-pro"><i class="fas fa-search"></i> Chercher</button>
  @if(request()->filled('search'))
  <a href="{{ route('admin.accounts.index') }}" class="btn-ghost btn-sm-pro"><i class="fas fa-times"></i> Réinitialiser</a>
  @endif
  <button type="button" id="sortAlpha" class="btn-ghost btn-sm-pro" onclick="sortTable('name')"><i class="fas fa-arrow-down-a-z"></i> A – Z</button>
  <button type="button" id="sortBal"   class="btn-ghost btn-sm-pro" onclick="sortTable('balance')"><i class="fas fa-arrow-down-9-1"></i> Solde</button>
</form>

{{-- Table ── --}}
<div class="card-pro">
  @if($clients->isEmpty())
    <div style="text-align:center;padding:5rem 2rem;color:var(--c-muted)">
      <i class="fas fa-wallet" style="font-size:2.5rem;display:block;margin-bottom:1rem;opacity:.2"></i>
      <p style="font-size:.9375rem;font-weight:600">Aucun compte client à gérer.</p>
    </div>
  @else
  <div class="table-responsive-pro">
    <table class="pro-table" id="accTable">
      <thead>
        <tr>
          <th style="width:52px"></th>
          <th>Client</th>
          <th>Solde actuel</th>
          <th>IBAN</th>
          <th>Dossiers</th>
          <th style="text-align:right">Action</th>
        </tr>
      </thead>
      <tbody id="accBody">
        @foreach($clients as $client)
        @php
          $bal      = (float) $client->balance;
          $cur      = $client->currency ?? $defaultCur;
          $balColor = $bal > 0 ? 'var(--c-green)' : ($bal < 0 ? 'var(--c-red)' : 'var(--c-muted)');
        @endphp
        <tr data-name="{{ strtolower($client->name) }}"
            data-email="{{ strtolower($client->email) }}"
            data-balance="{{ $bal }}"
            data-iban="{{ strtolower($client->bank_account ?? '') }}">
          <td data-label="">
            <div style="width:38px;height:38px;border-radius:50%;flex-shrink:0;
              background:linear-gradient(135deg,var(--c-navy),var(--c-navy-3));
              display:flex;align-items:center;justify-content:center;
              color:var(--c-gold);font-size:.875rem;font-weight:800">
              {{ strtoupper(substr($client->name, 0, 1)) }}
            </div>
          </td>
          <td data-label="Client">
            <div class="cell-name">{{ $client->name }}</div>
            <div class="cell-sub">{{ $client->email }}</div>
            @if($client->phone)
            <div class="cell-sub">{{ $client->phone }}</div>
            @endif
          </td>
          <td data-label="Solde actuel">
            <span style="font-family:'Space Grotesk',sans-serif;font-size:.9375rem;font-weight:800;color:{{ $balColor }}">
              {{ number_format($bal, 2, ',', ' ') }}
            </span>
            <span style="font-size:.75rem;color:var(--c-muted);margin-left:.25rem">{{ $cur }}</span>
          </td>
          <td data-label="IBAN" class="cell-mono">{{ $client->bank_account ? \Str::limit($client->bank_account, 22) : '—' }}</td>
          <td data-label="Dossiers">
            <span class="badge-status bs-gray">
              {{ $client->client_loans_count }} dossier{{ $client->client_loans_count > 1 ? 's' : '' }}
            </span>
          </td>
          <td data-label="Action" style="text-align:right">
            <a href="{{ route('admin.accounts.show', $client) }}" class="btn-gold btn-sm-pro">
              <i class="fas fa-arrow-right"></i> Gérer
            </a>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  @if($clients->hasPages())
  <div style="padding:.875rem 1.25rem;border-top:1px solid var(--c-border)">
    <x-pagination :paginator="$clients" />
  </div>
  @endif
  @endif
</div>

<script>
let _dir = {};
function sortTable(key) {
  const tbody = document.getElementById('accBody');
  const rows  = [...tbody.querySelectorAll('tr')];
  _dir[key]   = _dir[key] === 'asc' ? 'desc' : 'asc';
  rows.sort((a, b) => {
    const va = key === 'balance' ? parseFloat(a.dataset.balance) : a.dataset.name;
    const vb = key === 'balance' ? parseFloat(b.dataset.balance) : b.dataset.name;
    return (va < vb ? -1 : va > vb ? 1 : 0) * (_dir[key] === 'asc' ? 1 : -1);
  });
  rows.forEach(r => tbody.appendChild(r));
  document.querySelectorAll('#sortAlpha,#sortBal').forEach(b => b.style.borderColor = '');
  document.getElementById(key === 'balance' ? 'sortBal' : 'sortAlpha').style.borderColor = 'var(--c-gold)';
}
</script>

@endsection
