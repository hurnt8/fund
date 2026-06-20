@extends('layouts.dashboard')
@section('title','Mes demandes de prêt')
@section('page_title','Mes demandes de prêt')

@section('content')

<div class="page-hdr">
  <h4>Mes demandes de prêt</h4>
  <p>Consultez et suivez l'avancement de vos dossiers en temps réel</p>
</div>

@if($loans->isEmpty())
<div style="background:var(--c-surface);border:2px dashed var(--c-border);border-radius:var(--radius);padding:4rem 2rem;text-align:center">
  <div style="width:72px;height:72px;border-radius:50%;background:linear-gradient(135deg,var(--c-navy),var(--c-navy-3));margin:0 auto 1.25rem;display:flex;align-items:center;justify-content:center">
    <i class="fas fa-file-invoice-dollar" style="font-size:1.75rem;color:var(--c-gold)"></i>
  </div>
  <h5 style="font-size:1rem;font-weight:700;color:var(--c-navy);margin-bottom:.5rem">Aucune demande de prêt</h5>
  <p style="color:var(--c-muted);font-size:.8375rem;max-width:380px;margin:0 auto">
    Vous n'avez pas encore de dossier de financement ouvert. Contactez votre conseiller Credixa pour initier une demande.
  </p>
</div>
@else

<div class="row g-4">
  @foreach($loans as $loan)
  <div class="col-md-6 col-xl-4">
    <div class="card-pro h-100 d-flex flex-column" style="transition:var(--transition)"
         onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='var(--shadow)'"
         onmouseout="this.style.transform='';this.style.boxShadow=''">

      {{-- Header --}}
      <div style="padding:1.25rem 1.25rem .875rem;border-bottom:1px solid var(--c-border);display:flex;align-items:center;justify-content:space-between;flex-shrink:0">
        <span style="font-family:monospace;font-weight:800;font-size:.875rem;color:var(--c-navy)">
          {{ $loan->reference }}
        </span>
        <span class="badge-status bs-{{ $loan->statusColor() }}">{{ $loan->statusLabel() }}</span>
      </div>

      {{-- Corps --}}
      <div style="padding:1.25rem;flex:1">

        {{-- Montant principal --}}
        <div style="margin-bottom:1.25rem">
          <div style="font-size:.72rem;color:var(--c-muted);margin-bottom:.2rem">Montant accordé</div>
          <div style="font-size:1.625rem;font-weight:800;color:var(--c-navy)">
            {{ number_format($loan->amount,2,',',' ') }}
            <span style="font-size:.875rem;color:var(--c-muted);font-weight:500">{{ $loan->currency }}</span>
          </div>
        </div>

        {{-- Grille infos --}}
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem;margin-bottom:1.25rem">
          @foreach([
            ['Mensualité', number_format($loan->monthly_payment,2,',',' ').' '.$loan->currency, true],
            ['Durée', $loan->darly.' mois', false],
            ['Taux', $loan->interest_rate.' %', false],
            ['Date', $loan->created_at->format('d/m/Y'), false],
          ] as [$l,$v,$accent])
          <div>
            <div style="font-size:.68rem;color:var(--c-muted);margin-bottom:.15rem">{{ $l }}</div>
            <div style="font-size:.8375rem;font-weight:700;color:{{ $accent?'var(--c-gold-d)':'var(--c-navy)' }}">{{ $v }}</div>
          </div>
          @endforeach
        </div>

        {{-- Progression --}}
        @php
          $steps = ['draft','pending','validated','contract_sent','contract_signed','finalized'];
          $idx   = array_search($loan->status,$steps);
          $pct   = $idx!==false ? round(($idx+1)/count($steps)*100) : 0;
        @endphp
        @if($loan->status !== 'rejected')
        <div>
          <div style="display:flex;justify-content:space-between;margin-bottom:.375rem">
            <span style="font-size:.7rem;color:var(--c-muted)">Progression du dossier</span>
            <span style="font-size:.7rem;font-weight:700;color:var(--c-navy)">{{ $pct }}%</span>
          </div>
          <div style="height:5px;background:var(--c-border);border-radius:99px;overflow:hidden">
            <div style="height:100%;width:{{ $pct }}%;background:linear-gradient(90deg,var(--c-navy),var(--c-gold));border-radius:99px;transition:width .6s ease"></div>
          </div>
        </div>
        @endif

        @if($loan->objet)
        <div style="margin-top:.875rem;font-size:.75rem;color:var(--c-muted)">
          <i class="fas fa-tag me-1"></i> {{ Str::limit($loan->objet,55) }}
        </div>
        @endif
      </div>

      {{-- Footer --}}
      <div style="padding:.875rem 1.25rem;border-top:1px solid var(--c-border);flex-shrink:0">
        <a href="{{ route('client.loans.show',$loan) }}" class="btn-navy" style="width:100%;justify-content:center">
          <i class="fas fa-eye"></i> Voir le dossier complet
        </a>
      </div>

    </div>
  </div>
  @endforeach
</div>

@endif
@endsection
