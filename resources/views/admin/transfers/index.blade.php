@extends('layouts.dashboard')
@section('title', 'Transferts — Credixa')
@section('page_title', 'Transferts clients')

@section('content')

{{-- Page header ── --}}
<div class="page-hdr">
  <div style="display:flex;align-items:center;gap:.75rem;flex-wrap:wrap">
    <h1>Transferts clients</h1>
    @if($isSuperAdmin)
      <span class="badge-status bs-violet"><i class="fas fa-shield-alt" style="font-size:.6rem"></i> Vue globale</span>
    @endif
  </div>
  <p>Validation et suivi des virements soumis par les clients</p>
</div>

{{-- KPI ── --}}
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:1rem;margin-bottom:1.75rem">
  <div class="metric-card">
    <div class="metric-card__icon mi-amber"><i class="fas fa-hourglass-half"></i></div>
    <div class="metric-card__val" style="color:var(--c-amber)">{{ $stats['pending'] }}</div>
    <div class="metric-card__lbl">En attente</div>
    <div class="metric-card__accent" style="background:var(--c-amber)"></div>
  </div>
  <div class="metric-card">
    <div class="metric-card__icon mi-blue"><i class="fas fa-file-invoice"></i></div>
    <div class="metric-card__val" style="color:var(--c-blue)">{{ $stats['fee_required'] }}</div>
    <div class="metric-card__lbl">Frais requis</div>
    <div class="metric-card__accent" style="background:var(--c-blue)"></div>
  </div>
  <div class="metric-card">
    <div class="metric-card__icon mi-green"><i class="fas fa-check-circle"></i></div>
    <div class="metric-card__val" style="color:var(--c-green)">{{ $stats['completed'] }}</div>
    <div class="metric-card__lbl">Validés</div>
    <div class="metric-card__accent" style="background:var(--c-green)"></div>
  </div>
  <div class="metric-card">
    <div class="metric-card__icon mi-red"><i class="fas fa-times-circle"></i></div>
    <div class="metric-card__val" style="color:var(--c-red)">{{ $stats['rejected'] }}</div>
    <div class="metric-card__lbl">Rejetés</div>
    <div class="metric-card__accent" style="background:var(--c-red)"></div>
  </div>
</div>

{{-- Filters ── --}}
<div class="card-pro" style="margin-bottom:1.25rem">
  <div class="card-pro-body" style="padding:.75rem 1.25rem">
    <form method="GET" style="display:flex;gap:.625rem;flex-wrap:wrap;align-items:center">
      <div style="position:relative;flex:1;min-width:200px">
        <i class="fas fa-search" style="position:absolute;left:.75rem;top:50%;transform:translateY(-50%);color:var(--c-muted);font-size:.75rem;pointer-events:none"></i>
        <input type="text" name="search" value="{{ request('search') }}"
          placeholder="Référence, client, bénéficiaire…" class="form-control-pro" style="padding-left:2.25rem">
      </div>
      <select name="status" class="form-control-pro" style="width:auto;min-width:180px" onchange="this.form.submit()">
        <option value="">En attente + frais</option>
        <option value="pending"       {{ request('status') === 'pending'       ? 'selected':'' }}>En attente</option>
        <option value="fee_required"  {{ request('status') === 'fee_required'  ? 'selected':'' }}>Frais requis</option>
        <option value="completed"     {{ request('status') === 'completed'     ? 'selected':'' }}>Validés</option>
        <option value="rejected"      {{ request('status') === 'rejected'      ? 'selected':'' }}>Rejetés</option>
      </select>
      <button type="submit" class="btn-navy btn-sm-pro"><i class="fas fa-search"></i> Filtrer</button>
      @if(request()->hasAny(['search','status']))
      <a href="{{ route('admin.transfers.index') }}" class="btn-ghost btn-sm-pro">
        <i class="fas fa-times"></i> Réinitialiser
      </a>
      @endif
    </form>
  </div>
</div>

@if(session('success'))
<div class="flash flash-ok" style="margin-bottom:1.25rem"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
@endif

{{-- Transfer list ── --}}
@if($transfers->isEmpty())
<div class="card-pro" style="text-align:center;padding:5rem 2rem">
  <i class="fas fa-exchange-alt" style="font-size:2.5rem;color:var(--c-muted);opacity:.2;display:block;margin-bottom:1rem"></i>
  <p style="font-size:.9375rem;font-weight:600;color:var(--c-muted)">Aucun transfert à afficher.</p>
  <p style="font-size:.8125rem;color:var(--c-muted);margin-top:.35rem">Modifiez les filtres ou attendez de nouveaux virements.</p>
</div>
@else

<div style="display:flex;flex-direction:column;gap:1rem">
@foreach($transfers as $trf)
@php
  $isPending = in_array($trf->status, [\App\Models\Transfer::STATUS_PENDING, \App\Models\Transfer::STATUS_FEE_REQUIRED]);
  $statusMap = [
    'pending'      => ['cls'=>'bs-amber',  'icon'=>'hourglass-half', 'bar'=>'var(--c-amber)'],
    'fee_required' => ['cls'=>'bs-blue',   'icon'=>'file-invoice',   'bar'=>'var(--c-blue)'],
    'completed'    => ['cls'=>'bs-green',  'icon'=>'check',          'bar'=>'var(--c-green)'],
    'rejected'     => ['cls'=>'bs-red',    'icon'=>'times',          'bar'=>'var(--c-red)'],
  ];
  $sm = $statusMap[$trf->status] ?? ['cls'=>'bs-gray','icon'=>'circle','bar'=>'var(--c-muted)'];
@endphp

<div class="card-pro" style="border-left:4px solid {{ $sm['bar'] }};{{ $trf->status === 'rejected' ? 'opacity:.8' : '' }}">

  {{-- Head ── --}}
  <div style="display:grid;grid-template-columns:auto 1fr auto auto;gap:1.25rem;align-items:center;padding:1rem 1.25rem;flex-wrap:wrap">

    {{-- Client ── --}}
    <div style="display:flex;align-items:center;gap:.75rem">
      <div style="width:40px;height:40px;border-radius:50%;flex-shrink:0;
        background:linear-gradient(135deg,var(--c-navy),var(--c-navy-3));
        display:flex;align-items:center;justify-content:center;
        color:var(--c-gold);font-weight:800;font-size:.875rem">
        {{ strtoupper(substr($trf->user->name, 0, 1)) }}
      </div>
      <div>
        <div class="cell-name">{{ $trf->user->name }}</div>
        <div class="cell-sub">{{ $trf->user->email }}</div>
        <div style="font-family:monospace;font-size:.72rem;font-weight:700;color:var(--c-navy);margin-top:.15rem">{{ $trf->reference }}</div>
      </div>
    </div>

    {{-- Beneficiary ── --}}
    <div>
      <div style="font-size:.67rem;text-transform:uppercase;letter-spacing:.06em;color:var(--c-muted);font-weight:700;margin-bottom:.2rem">Bénéficiaire</div>
      <div style="font-size:.875rem;font-weight:600;color:var(--c-navy)">{{ $trf->beneficiary_name ?? '—' }}</div>
      @if($trf->beneficiary_iban)
      <div class="cell-mono" style="font-size:.72rem">{{ $trf->beneficiary_iban }}</div>
      @endif
    </div>

    {{-- Amount ── --}}
    <div style="text-align:right">
      <div style="font-family:'Space Grotesk',sans-serif;font-size:1.25rem;font-weight:900;color:var(--c-navy)">
        {{ number_format($trf->amount, 2, ',', ' ') }}
        <span style="font-size:.75rem;font-weight:600;color:var(--c-muted)">{{ $trf->currency }}</span>
      </div>
      <div class="cell-sub">{{ $trf->created_at->format('d/m/Y · H:i') }}</div>
    </div>

    {{-- Status ── --}}
    <div style="text-align:right">
      <span class="badge-status {{ $sm['cls'] }}">
        <i class="fas fa-{{ $sm['icon'] }}" style="font-size:.6rem"></i>
        {{ $trf->statusLabel() }}
      </span>
      @if($trf->invoice)
      <div class="cell-sub" style="margin-top:.35rem">Facture : {{ $trf->invoice->reference }}</div>
      @endif
    </div>
  </div>

  {{-- Notes ── --}}
  @if($trf->note || $trf->admin_note)
  <div style="padding:.625rem 1.25rem 1rem;border-top:1px solid var(--c-border);display:flex;gap:2rem;flex-wrap:wrap">
    @if($trf->note)
    <div>
      <div style="font-size:.67rem;text-transform:uppercase;letter-spacing:.06em;color:var(--c-muted);font-weight:700;margin-bottom:.2rem">Note client</div>
      <div style="font-size:.8125rem;color:var(--c-text)">{{ $trf->note }}</div>
    </div>
    @endif
    @if($trf->admin_note)
    <div>
      <div style="font-size:.67rem;text-transform:uppercase;letter-spacing:.06em;color:var(--c-muted);font-weight:700;margin-bottom:.2rem">Note admin</div>
      <div style="font-size:.8125rem;color:var(--c-text)">{{ $trf->admin_note }}
        @if($trf->admin)<span style="color:var(--c-muted)"> · {{ $trf->admin->name }}</span>@endif
      </div>
    </div>
    @endif
  </div>
  @endif

  {{-- Actions ── --}}
  @if($isPending)
  <div style="padding:.75rem 1.25rem;background:var(--c-bg);border-top:1px solid var(--c-border);display:flex;gap:.5rem;flex-wrap:wrap;align-items:center">
    <button type="button" class="btn-navy btn-sm-pro" style="background:var(--c-green)"
      onclick="toggleForm('approve-{{ $trf->id }}',{{ $trf->id }})">
      <i class="fas fa-check"></i> Valider
    </button>
    <button type="button" class="btn-navy btn-sm-pro" style="background:var(--c-red)"
      onclick="toggleForm('reject-{{ $trf->id }}',{{ $trf->id }})">
      <i class="fas fa-times"></i> Rejeter
    </button>
    @if($trf->status === \App\Models\Transfer::STATUS_PENDING)
    <button type="button" class="btn-ghost btn-sm-pro"
      onclick="toggleForm('invoice-{{ $trf->id }}',{{ $trf->id }})">
      <i class="fas fa-file-invoice"></i> Facturer les frais
    </button>
    @endif
    <span style="margin-left:auto;font-size:.75rem;color:var(--c-muted)">
      <i class="fas fa-clock" style="margin-right:.3rem"></i>Soumis {{ $trf->created_at->diffForHumans() }}
    </span>
  </div>

  {{-- Approve form ── --}}
  <div class="trf-form" id="approve-{{ $trf->id }}" style="display:none;padding:1rem 1.25rem;border-top:1px solid var(--c-border);background:rgba(5,150,105,.03)">
    <form method="POST" action="{{ route('admin.transfers.approve', $trf) }}">
      @csrf
      <label class="form-label-pro">Note de validation (optionnel)</label>
      <input type="text" name="admin_note" class="form-control-pro" style="margin-bottom:.875rem"
        placeholder="Ex : virement traité — délai estimé 2 jours ouvrés" maxlength="500">
      <div style="display:flex;gap:.5rem">
        <button type="submit" class="btn-navy btn-sm-pro" style="background:var(--c-green)">
          <i class="fas fa-check"></i> Confirmer la validation
        </button>
        <button type="button" class="btn-ghost btn-sm-pro"
          onclick="toggleForm('approve-{{ $trf->id }}',{{ $trf->id }})">Annuler</button>
      </div>
    </form>
  </div>

  {{-- Reject form ── --}}
  <div class="trf-form" id="reject-{{ $trf->id }}" style="display:none;padding:1rem 1.25rem;border-top:1px solid var(--c-border);background:rgba(220,38,38,.03)">
    <form method="POST" action="{{ route('admin.transfers.reject', $trf) }}">
      @csrf
      <label class="form-label-pro">Motif du rejet (optionnel)</label>
      <input type="text" name="admin_note" class="form-control-pro" style="margin-bottom:.875rem"
        placeholder="Ex : IBAN invalide, KYC incomplet, limite atteinte…" maxlength="500">
      <div style="display:flex;gap:.5rem">
        <button type="submit" class="btn-navy btn-sm-pro" style="background:var(--c-red)"
          onclick="return confirm('Rejeter ce virement ? Les fonds seront recrédités au client.')">
          <i class="fas fa-times"></i> Confirmer le rejet
        </button>
        <button type="button" class="btn-ghost btn-sm-pro"
          onclick="toggleForm('reject-{{ $trf->id }}',{{ $trf->id }})">Annuler</button>
      </div>
    </form>
  </div>

  {{-- Invoice form ── --}}
  @if($trf->status === \App\Models\Transfer::STATUS_PENDING)
  <div class="trf-form" id="invoice-{{ $trf->id }}" style="display:none;padding:1rem 1.25rem;border-top:1px solid var(--c-border);background:rgba(37,99,235,.03)">
    <form method="POST" action="{{ route('admin.transfers.invoice', $trf) }}">
      @csrf
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem;margin-bottom:.875rem">
        <div>
          <label class="form-label-pro">Montant des frais *</label>
          <input type="number" name="fee_amount" class="form-control-pro" step="0.01" min="0.01" placeholder="0,00" required>
        </div>
        <div>
          <label class="form-label-pro">Description</label>
          <input type="text" name="description" class="form-control-pro"
            placeholder="Frais de traitement…" maxlength="500">
        </div>
      </div>
      <div style="display:flex;gap:.5rem">
        <button type="submit" class="btn-gold btn-sm-pro"
          onclick="return confirm('Créer la facture de frais et l\'envoyer au client ?')">
          <i class="fas fa-paper-plane"></i> Créer &amp; envoyer
        </button>
        <button type="button" class="btn-ghost btn-sm-pro"
          onclick="toggleForm('invoice-{{ $trf->id }}',{{ $trf->id }})">Annuler</button>
      </div>
    </form>
  </div>
  @endif

  @else
  <div style="padding:.625rem 1.25rem;border-top:1px solid var(--c-border);font-size:.78rem;color:var(--c-muted)">
    @if($trf->processed_at)
      <i class="fas fa-clock" style="margin-right:.35rem"></i>
      Traité le {{ $trf->processed_at->format('d/m/Y à H:i') }}
      @if($trf->admin) · par {{ $trf->admin->name }} @endif
    @endif
  </div>
  @endif

</div>
@endforeach
</div>

@if($transfers->hasPages())
<div style="margin-top:1.5rem">{{ $transfers->links() }}</div>
@endif
@endif

<script>
function toggleForm(id, trfId) {
  ['approve','reject','invoice'].forEach(t => {
    const el = document.getElementById(t + '-' + trfId);
    if (el && el.id !== id) el.style.display = 'none';
  });
  const el = document.getElementById(id);
  if (el) el.style.display = el.style.display === 'none' ? 'block' : 'none';
}
</script>

@endsection
