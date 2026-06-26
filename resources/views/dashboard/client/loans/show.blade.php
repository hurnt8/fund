@extends('layouts.dashboard')
@section('title','Dossier '.$loan->reference.' — Credixa')
@section('page_title','Dossier '.$loan->reference)

@push('styles')
<style>
  body { background: #0D1F35 !important; }
  .main-wrap { background: #0D1F35; }
  .content-area { background: transparent; }
  .topbar { background: #112237 !important; border-bottom-color: rgba(200,169,81,.14) !important; }
  .topbar-title { color: #E8EDF5 !important; }
  .topbar-badge { background: #162D47 !important; border-color: rgba(255,255,255,.1) !important; color: #B8C8D8 !important; }
  .topbar-avatar { background: linear-gradient(135deg,#1D3A5C,#0B2E4E) !important; color: #C8A951 !important; }

  /* ── Tableau d'amortissement — responsive mobile ── */
  .cl-amort-wrap { overflow-x: auto; overflow-y: auto; max-height: 380px; -webkit-overflow-scrolling: touch; }

  @media(max-width:640px) {
    /* Mode carte pour cl-table */
    .cl-table { display: block; min-width: 0 !important; }
    .cl-table thead { display: none; }
    .cl-table tbody { display: block; }
    .cl-table tbody tr {
      display: block;
      background: rgba(22,45,71,.7);
      border: 1px solid rgba(200,169,81,.14);
      border-radius: 9px;
      padding: .75rem;
      margin-bottom: .625rem;
    }
    .cl-table tbody td {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: .3rem 0;
      border-bottom: 1px solid rgba(255,255,255,.05);
      font-size: .8rem;
      gap: .5rem;
    }
    .cl-table tbody td:last-child { border-bottom: none; }
    .cl-table tbody td[data-label]::before {
      content: attr(data-label);
      font-size: .65rem;
      font-weight: 700;
      color: var(--cl-muted, #6B88A4);
      text-transform: uppercase;
      letter-spacing: .05em;
      flex-shrink: 0;
      white-space: nowrap;
    }
    /* Panel head wrap */
    .cl-panel__head { flex-wrap: wrap; gap: .5rem; }
    /* Steps : scroll horizontal sur mobile */
    .cl-steps { padding-bottom: .5rem; }
    /* Row g-4 : réduire gap sur mobile */
    .row.g-4 { --bs-gutter-y: 1rem; }
  }

  @media(max-width:480px) {
    .cl-data-row { flex-wrap: wrap; gap: .2rem; }
    .cl-data-row__val { text-align: left; flex: 1 1 100%; }
    .cl-track-label { font-size: .65rem; }
    .cl-track-val   { font-size: .78rem; }
  }
</style>
@endpush

@section('content')
@php
  $steps = [
    'draft'           => 'Brouillon',
    'pending'         => 'En attente',
    'validated'       => 'Validée',
    'contract_sent'   => 'Contrat envoyé',
    'contract_signed' => 'Contrat signé',
    'finalized'       => 'Finalisée',
  ];
  $stepKeys   = array_keys($steps);
  $currentIdx = array_search($loan->status, $stepKeys);

  $badgeClass = match($loan->status) {
      'draft'           => 'cl-badge--draft',
      'pending'         => 'cl-badge--pending',
      'validated'       => 'cl-badge--validated',
      'contract_sent'   => 'cl-badge--sent',
      'contract_signed' => 'cl-badge--signed',
      'finalized'       => 'cl-badge--finalized',
      'rejected'        => 'cl-badge--rejected',
      default           => 'cl-badge--draft',
  };

  $principal = (float) $loan->amount;
  $total     = (float) $loan->total_with_interest;
  $interest  = max(0, $total - $principal);
@endphp
<div class="cl-scope">

{{-- ── Page header ──────────────────────────────────────────── --}}
<div class="d-flex align-items-start justify-content-between flex-wrap gap-3 mb-4">
  <div>
    <div class="d-flex align-items-center gap-3 mb-1 flex-wrap">
      <span style="font-family:'Space Grotesk',monospace;font-size:1rem;font-weight:700;color:var(--cl-gold)">
        {{ $loan->reference }}
      </span>
      <span class="cl-badge {{ $badgeClass }}">{{ $loan->statusLabel() }}</span>
    </div>
    <div style="font-size:.8rem;color:var(--cl-muted)">
      {{ number_format($loan->amount, 2, ',', ' ') }} {{ $loan->currency }} ·
      {{ $loan->darly }} mois · {{ $loan->interest_rate }}% · ouvert le {{ $loan->created_at->format('d/m/Y') }}
    </div>
  </div>
  <a href="{{ route('client.loans') }}" class="cl-btn cl-btn--ghost">
    <i class="fas fa-arrow-left"></i> Mes dossiers
  </a>
</div>

{{-- ── Alerts ───────────────────────────────────────────────── --}}
@if($loan->status === 'contract_sent')
<div class="cl-alert cl-alert--warn mb-4">
  <div class="cl-alert__icon"><i class="fas fa-envelope"></i></div>
  <div>
    <div class="cl-alert__title">Contrat en attente de signature</div>
    Nous vous avons envoyé votre contrat le {{ $loan->sent_at?->format('d/m/Y') }}.
    Veuillez le signer et nous le retourner par email.
  </div>
</div>
@endif

@if($loan->status === 'finalized')
<div class="cl-alert cl-alert--green mb-4">
  <div class="cl-alert__icon"><i class="fas fa-check-circle"></i></div>
  <div>
    <div class="cl-alert__title">Financement accordé</div>
    Le montant de {{ number_format($loan->amount, 2, ',', ' ') }} {{ $loan->currency }}
    a été versé sur votre compte.
  </div>
</div>
@endif

@if($loan->status === 'rejected')
<div class="cl-alert cl-alert--error mb-4">
  <div class="cl-alert__icon"><i class="fas fa-ban"></i></div>
  <div>
    <div class="cl-alert__title">Demande non acceptée</div>
    Contactez votre conseiller Credixa pour plus d'informations.
  </div>
</div>
@endif

{{-- ── Timeline ─────────────────────────────────────────────── --}}
@if($loan->status !== 'rejected')
<div class="cl-panel mb-4">
  <div class="cl-panel__head">
    <div class="cl-panel__title">
      <span class="cl-panel__dot"></span> Avancement du dossier
    </div>
    @php
      $pct = $currentIdx !== false ? round(($currentIdx + 1) / count($stepKeys) * 100) : 0;
    @endphp
    <span style="font-size:.72rem;color:var(--cl-gold-2);font-weight:700">{{ $pct }}%</span>
  </div>
  <div class="cl-panel__body" style="padding:1.5rem 1.25rem">
    <div class="cl-steps">
      @foreach($steps as $key => $label)
      @php
        $i    = array_search($key, $stepKeys);
        $done = $currentIdx !== false && $i <= $currentIdx;
        $cur  = $loan->status === $key;
      @endphp
      <div class="cl-step {{ $cur ? 'current' : ($done ? 'done' : '') }}">
        <div class="cl-step__dot">
          @if($done && !$cur)
            <i class="fas fa-check" style="font-size:.55rem"></i>
          @else
            {{ $i + 1 }}
          @endif
        </div>
        <div class="cl-step__label">{{ $label }}</div>
      </div>
      @endforeach
    </div>
  </div>
</div>
@endif

{{-- ── Main content ─────────────────────────────────────────── --}}
<div class="row g-4">

  {{-- Financement --}}
  <div class="col-lg-7">
    <div class="cl-panel h-100">
      <div class="cl-panel__head">
        <div class="cl-panel__title">
          <span class="cl-panel__dot"></span> Détails du financement
        </div>
      </div>
      <div class="cl-panel__body">

        {{-- Hero amounts --}}
        <div class="row g-3 mb-4">
          <div class="col-6">
            <div style="background:var(--cl-surface-2);border-radius:12px;padding:1rem;border:1px solid var(--cl-border)">
              <div style="font-size:.65rem;color:var(--cl-muted);text-transform:uppercase;letter-spacing:.07em;margin-bottom:.4rem">
                Montant accordé
              </div>
              <div style="font-family:'Space Grotesk',sans-serif;font-size:1.5rem;font-weight:700;color:var(--cl-text);line-height:1">
                {{ number_format($loan->amount, 2, ',', ' ') }}
                <span style="font-size:.8rem;color:var(--cl-gold);font-weight:600">{{ $loan->currency }}</span>
              </div>
            </div>
          </div>
          <div class="col-6">
            <div style="background:var(--cl-surface-2);border-radius:12px;padding:1rem;border:1px solid var(--cl-border)">
              <div style="font-size:.65rem;color:var(--cl-muted);text-transform:uppercase;letter-spacing:.07em;margin-bottom:.4rem">
                Mensualité
              </div>
              <div style="font-family:'Space Grotesk',sans-serif;font-size:1.5rem;font-weight:700;color:var(--cl-gold-2);line-height:1">
                {{ number_format($loan->monthly_payment, 2, ',', ' ') }}
                <span style="font-size:.8rem;font-weight:600">{{ $loan->currency }}</span>
              </div>
            </div>
          </div>
        </div>

        {{-- Data rows --}}
        @foreach([
          ['Durée totale', $loan->darly.' mois'],
          ["Taux d'intérêt", $loan->interest_rate.' %'],
          ['Total à rembourser', number_format($loan->total_with_interest, 2, ',', ' ').' '.$loan->currency],
          ['Coût total du crédit', number_format($loan->total_cost ?? 0, 2, ',', ' ').' '.$loan->currency],
          ['Frais administratifs', $loan->admin_fees ? number_format($loan->admin_fees, 2, ',', ' ').' '.$loan->currency : '—'],
          ['Première échéance', $loan->start_date?->format('d/m/Y') ?? '—'],
          ['Objet', $loan->objet ?? '—'],
        ] as [$lbl, $val])
        <div class="cl-data-row">
          <span class="cl-data-row__label">{{ $lbl }}</span>
          <span class="cl-data-row__val">{{ $val }}</span>
        </div>
        @endforeach

        @if($loan->special_conditions)
        <div style="margin-top:1rem;background:var(--cl-surface-2);border-radius:10px;padding:.875rem;border:1px solid var(--cl-border)">
          <div style="font-size:.68rem;color:var(--cl-muted);text-transform:uppercase;letter-spacing:.07em;margin-bottom:.4rem">
            Conditions particulières
          </div>
          <div style="font-size:.82rem;color:var(--cl-text-2)">{{ $loan->special_conditions }}</div>
        </div>
        @endif
      </div>
    </div>
  </div>

  {{-- Sidebar: chart + tracking --}}
  <div class="col-lg-5 d-flex flex-column gap-4">

    {{-- Doughnut chart --}}
    @if($total > 0)
    <div class="cl-panel">
      <div class="cl-panel__head">
        <div class="cl-panel__title">
          <span class="cl-panel__dot"></span> Répartition capital / intérêts
        </div>
      </div>
      <div class="cl-panel__body">
        <div class="cl-chart-wrap" style="max-height:220px">
          <canvas id="amortChart" style="max-height:220px"></canvas>
          <div class="cl-chart-center">
            <div class="cl-chart-center__val">
              {{ number_format($principal / max(1, $total) * 100, 0) }}%
            </div>
            <div class="cl-chart-center__lbl">Capital</div>
          </div>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem;margin-top:1.25rem">
          <div style="background:rgba(29,58,92,.4);border-radius:9px;padding:.75rem;border:1px solid rgba(29,58,92,.6)">
            <div style="font-size:.65rem;color:var(--cl-muted);margin-bottom:.2rem">Capital</div>
            <div style="font-weight:700;color:var(--cl-text-2);font-size:.85rem">
              {{ number_format($principal, 2, ',', ' ') }} {{ $loan->currency }}
            </div>
          </div>
          <div style="background:rgba(200,169,81,.08);border-radius:9px;padding:.75rem;border:1px solid rgba(200,169,81,.2)">
            <div style="font-size:.65rem;color:var(--cl-muted);margin-bottom:.2rem">Intérêts</div>
            <div style="font-weight:700;color:var(--cl-gold-2);font-size:.85rem">
              {{ number_format($interest, 2, ',', ' ') }} {{ $loan->currency }}
            </div>
          </div>
        </div>
      </div>
    </div>
    @endif

    {{-- Suivi dossier --}}
    <div class="cl-panel">
      <div class="cl-panel__head">
        <div class="cl-panel__title">
          <span class="cl-panel__dot"></span> Suivi du dossier
        </div>
      </div>
      <div class="cl-panel__body" style="padding:1rem 1.25rem">
        @foreach([
          ['fa-hashtag',         'Référence',              $loan->reference],
          ['fa-calendar-plus',   'Date de demande',        $loan->created_at->format('d/m/Y')],
          ['fa-check-double',    'Date de validation',     $loan->validated_at?->format('d/m/Y') ?? 'En attente'],
          ['fa-envelope-open',   'Contrat envoyé le',      $loan->sent_at?->format('d/m/Y') ?? '—'],
          ['fa-file-check',      'Contrat signé reçu',     $loan->signed_received_at?->format('d/m/Y') ?? '—'],
          ['fa-user-tie',        'Votre conseiller',       $loan->admin?->name ?? '—'],
        ] as [$icon, $label, $value])
        <div class="cl-track-item">
          <div class="cl-track-icon"><i class="fas {{ $icon }}"></i></div>
          <div>
            <div class="cl-track-label">{{ $label }}</div>
            <div class="cl-track-val">{{ $value }}</div>
          </div>
        </div>
        @endforeach
      </div>
    </div>

  </div>
</div>

{{-- ── Amortization table ───────────────────────────────────── --}}
@if($loan->amortization_schedule && $loan->status !== 'draft')
<div class="cl-panel mt-4">
  <div class="cl-panel__head">
    <div class="cl-panel__title">
      <span class="cl-panel__dot"></span> Tableau d'amortissement
    </div>
    <span style="font-size:.72rem;color:var(--cl-muted)">
      {{ count($loan->amortization_schedule) }} échéances · {{ $loan->darly }} mois
    </span>
  </div>
  <div class="cl-amort-wrap">
    <table class="cl-table">
      <thead>
        <tr>
          <th>N°</th>
          <th>Mensualité</th>
          <th>Capital remboursé</th>
          <th>Intérêts payés</th>
          <th>Capital restant</th>
        </tr>
      </thead>
      <tbody>
        @foreach($loan->amortization_schedule as $row)
        <tr>
          <td data-label="N°" class="td-muted">{{ $row['month'] }}</td>
          <td data-label="Mensualité" class="td-bold">{{ number_format($row['payment'], 2, ',', ' ') }} {{ $loan->currency }}</td>
          <td data-label="Capital remb." class="td-green">{{ number_format($row['principal'], 2, ',', ' ') }} {{ $loan->currency }}</td>
          <td data-label="Intérêts" class="td-red">{{ number_format($row['interest'], 2, ',', ' ') }} {{ $loan->currency }}</td>
          <td data-label="Solde restant" class="td-muted">{{ number_format($row['balance'], 2, ',', ' ') }} {{ $loan->currency }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endif

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  if (typeof buildAmortChart !== 'undefined') {
    buildAmortChart(
      'amortChart',
      {{ $principal }},
      {{ $interest }},
      '{{ $loan->currency }}'
    );
  }
});
</script>
@endpush
