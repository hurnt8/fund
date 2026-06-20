@extends('layouts.dashboard')
@section('title','Dossier '.$loan->reference)
@section('page_title','Mon dossier')

@section('content')

<div class="d-flex align-items-start justify-content-between flex-wrap gap-3 page-hdr">
  <div>
    <div class="d-flex align-items-center gap-3 mb-1">
      <span style="font-family:monospace;font-size:1.125rem;font-weight:800;color:var(--c-navy)">{{ $loan->reference }}</span>
      <span class="badge-status bs-{{ $loan->statusColor() }}">{{ $loan->statusLabel() }}</span>
    </div>
    <p>Demande de {{ number_format($loan->amount,2,',',' ') }} {{ $loan->currency }} · {{ $loan->darly }} mois</p>
  </div>
  <a href="{{ route('client.loans') }}" class="btn-ghost btn-sm-pro">
    <i class="fas fa-arrow-left"></i> Mes demandes
  </a>
</div>

{{-- Alerte contrat envoyé --}}
@if($loan->status === 'contract_sent')
<div class="flash flash-warn mb-4">
  <i class="fas fa-envelope" style="font-size:1.125rem"></i>
  <div>
    <div style="font-weight:700">Votre contrat est en attente de signature</div>
    <div style="font-size:.8125rem;margin-top:.15rem">
      Nous vous l'avons envoyé par email le {{ $loan->sent_at?->format('d/m/Y') }}.
      Veuillez le lire, le signer, puis nous le retourner par email.
    </div>
  </div>
</div>
@endif

@if($loan->status === 'rejected')
<div class="flash flash-err mb-4">
  <i class="fas fa-ban" style="font-size:1.125rem"></i>
  <div>
    <div style="font-weight:700">Votre demande n'a pas pu être acceptée</div>
    <div style="font-size:.8125rem;margin-top:.15rem">Contactez votre conseiller Credixa pour plus d'informations.</div>
  </div>
</div>
@endif

{{-- Timeline --}}
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
$currentIdx = array_search($loan->status,$stepKeys);
@endphp

@if($loan->status !== 'rejected')
<div class="card-pro mb-4">
  <div class="card-pro-body" style="padding:1.5rem 1.25rem">
    <div class="steps-bar">
      @foreach($steps as $key=>$label)
      @php $i=array_search($key,$stepKeys); $done=$currentIdx!==false&&$i<=$currentIdx; $cur=$loan->status===$key; @endphp
      <div class="step-item">
        <div class="step-dot {{ $cur?'current':($done?'done':'') }}">
          @if($done&&!$cur)<i class="fas fa-check" style="font-size:.6rem"></i>
          @else {{ $i+1 }}
          @endif
        </div>
        <div class="step-label {{ $done?'done':'' }}">{{ $label }}</div>
      </div>
      @endforeach
    </div>
  </div>
</div>
@endif

<div class="row g-4">

  {{-- Financement --}}
  <div class="col-md-6">
    <div class="card-pro h-100">
      <div class="card-pro-hdr">
        <div class="card-pro-title"><span class="icon-dot"></span>Détails du financement</div>
      </div>
      <div class="card-pro-body">

        <div class="summary-box mb-3">
          <div class="row g-2">
            <div class="col-6">
              <div class="summary-box__label">Montant accordé</div>
              <div class="summary-box__val">{{ number_format($loan->amount,2,',',' ') }} {{ $loan->currency }}</div>
            </div>
            <div class="col-6">
              <div class="summary-box__label">Mensualité</div>
              <div class="summary-box__val">{{ number_format($loan->monthly_payment,2,',',' ') }} {{ $loan->currency }}</div>
            </div>
          </div>
        </div>

        @foreach([
          ['Durée totale', $loan->darly.' mois'],
          ['Taux d\'intérêt', $loan->interest_rate.' %'],
          ['Total à rembourser', number_format($loan->total_with_interest,2,',',' ').' '.$loan->currency],
          ['Coût du crédit', number_format($loan->total_cost??0,2,',',' ').' '.$loan->currency],
          ['Frais administratifs', $loan->admin_fees ? number_format($loan->admin_fees,2,',',' ').' '.$loan->currency : '—'],
          ['Première échéance', $loan->start_date?->format('d/m/Y') ?? '—'],
          ['Objet', $loan->objet ?? '—'],
        ] as [$l,$v])
        <div style="display:flex;justify-content:space-between;align-items:center;padding:.5rem 0;border-bottom:1px solid var(--c-border)">
          <span style="font-size:.8125rem;color:var(--c-muted)">{{ $l }}</span>
          <span style="font-size:.8375rem;font-weight:600;color:var(--c-navy)">{{ $v }}</span>
        </div>
        @endforeach
      </div>
    </div>
  </div>

  {{-- Suivi --}}
  <div class="col-md-6">
    <div class="card-pro h-100">
      <div class="card-pro-hdr">
        <div class="card-pro-title"><span class="icon-dot"></span>Suivi du dossier</div>
      </div>
      <div class="card-pro-body">
        @foreach([
          ['fa-hashtag','Référence',$loan->reference],
          ['fa-calendar-plus','Date de demande',$loan->created_at->format('d/m/Y')],
          ['fa-check-double','Date de validation',$loan->validated_at?->format('d/m/Y')??'En attente'],
          ['fa-envelope-open','Contrat envoyé le',$loan->sent_at?->format('d/m/Y')??'—'],
          ['fa-file-check','Contrat signé reçu',$loan->signed_received_at?->format('d/m/Y')??'—'],
          ['fa-user-tie','Votre conseiller',$loan->admin?->name??'—'],
        ] as [$i,$l,$v])
        <div style="display:flex;align-items:flex-start;gap:.875rem;padding:.75rem 0;border-bottom:1px solid var(--c-border)">
          <div style="width:32px;height:32px;border-radius:8px;background:var(--c-bg);display:flex;align-items:center;justify-content:center;flex-shrink:0">
            <i class="fas {{ $i }}" style="font-size:.78rem;color:var(--c-gold-d)"></i>
          </div>
          <div>
            <div style="font-size:.72rem;color:var(--c-muted)">{{ $l }}</div>
            <div style="font-size:.8375rem;font-weight:600;color:var(--c-navy)">{{ $v }}</div>
          </div>
        </div>
        @endforeach

        @if($loan->special_conditions)
        <div style="margin-top:1rem;padding:.875rem;background:var(--c-bg);border-radius:var(--radius-sm)">
          <div style="font-size:.72rem;color:var(--c-muted);margin-bottom:.35rem">Conditions particulières</div>
          <div style="font-size:.8125rem;color:var(--c-text)">{{ $loan->special_conditions }}</div>
        </div>
        @endif
      </div>
    </div>
  </div>
</div>

{{-- Tableau d'amortissement --}}
@if($loan->amortization_schedule && $loan->status !== 'draft')
<div class="card-pro mt-4">
  <div class="card-pro-hdr">
    <div class="card-pro-title"><span class="icon-dot"></span>Tableau d'amortissement</div>
    <span style="font-size:.75rem;color:var(--c-muted)">{{ count($loan->amortization_schedule) }} échéances · {{ $loan->darly }} mois</span>
  </div>
  <div style="max-height:380px;overflow-y:auto">
    <table class="pro-table w-100">
      <thead style="position:sticky;top:0">
        <tr>
          <th>N°</th><th>Mensualité</th><th>Capital remboursé</th><th>Intérêts payés</th><th>Capital restant</th>
        </tr>
      </thead>
      <tbody>
        @foreach($loan->amortization_schedule as $row)
        <tr>
          <td style="color:var(--c-muted);font-size:.78rem">{{ $row['month'] }}</td>
          <td style="font-weight:600">{{ number_format($row['payment'],2,',',' ') }} {{ $loan->currency }}</td>
          <td style="color:var(--c-green)">{{ number_format($row['principal'],2,',',' ') }} {{ $loan->currency }}</td>
          <td style="color:var(--c-red)">{{ number_format($row['interest'],2,',',' ') }} {{ $loan->currency }}</td>
          <td style="color:var(--c-muted)">{{ number_format($row['balance'],2,',',' ') }} {{ $loan->currency }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endif

@endsection
