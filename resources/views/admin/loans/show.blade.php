@extends('layouts.dashboard')
@section('title',$loan->reference)
@section('page_title','Dossier — '.$loan->reference)

@section('content')

{{-- En-tête --}}
<div class="d-flex align-items-start justify-content-between flex-wrap gap-3 page-hdr">
  <div>
    <div class="d-flex align-items-center gap-3 mb-1">
      <span style="font-family:monospace;font-size:1.125rem;font-weight:800;color:var(--c-navy)">{{ $loan->reference }}</span>
      <span class="badge-status bs-{{ $loan->statusColor() }}">{{ $loan->statusLabel() }}</span>
    </div>
    <p>Dossier de {{ $loan->name }} · Créé le {{ $loan->created_at->format('d/m/Y') }}</p>
  </div>
  <div class="d-flex gap-2 flex-wrap">
    <a href="{{ route('admin.loans.index') }}" class="btn-ghost btn-sm-pro">
      <i class="fas fa-arrow-left"></i> Retour
    </a>
    @if($loan->isEditable())
    <a href="{{ route('admin.loans.edit',$loan) }}" class="btn-ghost btn-sm-pro">
      <i class="fas fa-pen"></i> Modifier
    </a>
    @endif
    <a href="{{ route('admin.loans.contract',$loan) }}" class="btn-ghost btn-sm-pro">
      <i class="fas fa-file-contract"></i> Contrat
    </a>
    @if($loan->canBeValidated())
    <form action="{{ route('admin.loans.validate',$loan) }}" method="POST"
          onsubmit="return confirm('Valider et envoyer le contrat par email au client ?')">
      @csrf
      <button class="btn-navy btn-sm-pro"><i class="fas fa-paper-plane"></i> Valider &amp; Envoyer</button>
    </form>
    @endif
    @if($loan->status === 'contract_sent')
    <form action="{{ route('admin.loans.signed',$loan) }}" method="POST"
          onsubmit="return confirm('Confirmer la réception du contrat signé ?')">
      @csrf
      <button class="btn-navy btn-sm-pro" style="background:var(--c-green)">
        <i class="fas fa-check"></i> Contrat signé reçu
      </button>
    </form>
    @endif
  </div>
</div>

{{-- Barre de progression statut --}}
@php
$allSteps = [
  'draft'           => 'Brouillon',
  'pending'         => 'En attente',
  'validated'       => 'Validée',
  'contract_sent'   => 'Contrat envoyé',
  'contract_signed' => 'Contrat signé',
  'finalized'       => 'Finalisée',
];
$stepKeys   = array_keys($allSteps);
$currentIdx = array_search($loan->status,$stepKeys);
@endphp
@if($loan->status !== 'rejected')
<div class="card-pro mb-4">
  <div class="card-pro-body">
    <div class="steps-bar">
      @foreach($allSteps as $key=>$label)
      @php $i=array_search($key,$stepKeys); $done=$currentIdx!==false&&$i<=$currentIdx; $cur=$loan->status===$key; @endphp
      <div class="step-item">
        <div class="step-dot {{ $cur?'current':($done?'done':'') }}">
          @if($done && !$cur)<i class="fas fa-check" style="font-size:.6rem"></i>
          @else {{ $i+1 }}
          @endif
        </div>
        <div class="step-label {{ $done?'done':'' }}">{{ $label }}</div>
      </div>
      @endforeach
    </div>
  </div>
</div>
@else
<div class="flash flash-err mb-4"><i class="fas fa-ban"></i> Cette demande a été <strong>rejetée</strong>.</div>
@endif

<div class="row g-4">

  {{-- Colonne gauche : client + statut --}}
  <div class="col-lg-4 d-flex flex-column gap-4">

    {{-- Client --}}
    <div class="card-pro">
      <div class="card-pro-hdr">
        <div class="card-pro-title"><span class="icon-dot"></span>Informations client</div>
      </div>
      <div class="card-pro-body">
        <div style="display:flex;align-items:center;gap:.875rem;margin-bottom:1.25rem">
          <div style="width:48px;height:48px;border-radius:50%;background:linear-gradient(135deg,var(--c-navy),var(--c-navy-3));display:flex;align-items:center;justify-content:center;color:var(--c-gold);font-weight:800;font-size:1.125rem;flex-shrink:0">
            {{ strtoupper(substr($loan->name,0,1)) }}
          </div>
          <div>
            <div style="font-weight:700;color:var(--c-navy)">{{ $loan->name }}</div>
            <div style="font-size:.78rem;color:var(--c-muted)">{{ $loan->email }}</div>
          </div>
        </div>
        @foreach([
          ['fa-phone','Téléphone',$loan->phone??'—'],
          ['fa-map-marker-alt','Adresse',$loan->address??'—'],
          ['fa-language','Langue contrat',strtoupper($loan->contract_language??'FR')],
          ['fa-birthday-cake','Naissance',$loan->client?->birth_date?->format('d/m/Y')??'—'],
        ] as [$ico,$lbl,$val])
        <div class="d-flex align-items-start gap-2 mb-2">
          <i class="fas {{ $ico }}" style="color:var(--c-gold);width:14px;margin-top:.15rem;font-size:.78rem;flex-shrink:0"></i>
          <div>
            <div style="font-size:.7rem;color:var(--c-muted)">{{ $lbl }}</div>
            <div style="font-size:.8125rem;font-weight:500;color:var(--c-navy)">{{ $val }}</div>
          </div>
        </div>
        @endforeach
      </div>
    </div>

    {{-- Modifier statut --}}
    <div class="card-pro">
      <div class="card-pro-hdr">
        <div class="card-pro-title"><span class="icon-dot"></span>Changer le statut</div>
      </div>
      <div class="card-pro-body">
        <form action="{{ route('admin.loans.status',$loan) }}" method="POST" class="d-flex gap-2">
          @csrf @method('PATCH')
          <select name="status" class="form-control-pro" style="flex:1">
            @foreach(\App\Models\LoanRequest::STATUSES as $s)
            <option value="{{ $s }}" {{ $loan->status===$s?'selected':'' }}>
              {{ ucfirst(str_replace('_',' ',$s)) }}
            </option>
            @endforeach
          </select>
          <button type="submit" class="btn-navy" style="padding:.6rem .875rem;flex-shrink:0">
            <i class="fas fa-save"></i>
          </button>
        </form>
      </div>
    </div>

  </div>

  {{-- Colonne droite : financement --}}
  <div class="col-lg-8">

    {{-- Summary box --}}
    <div class="summary-box mb-4">
      <div class="row g-3">
        @foreach([
          ['Montant accordé',number_format($loan->amount,2,',',' ').' '.$loan->currency],
          ['Mensualité',number_format($loan->monthly_payment,2,',',' ').' '.$loan->currency],
          ['Total à rembourser',number_format($loan->total_with_interest,2,',',' ').' '.$loan->currency],
          ['Coût du crédit',number_format($loan->total_cost,2,',',' ').' '.$loan->currency],
        ] as [$l,$v])
        <div class="col-6 col-sm-3">
          <div class="summary-box__label">{{ $l }}</div>
          <div class="summary-box__val">{{ $v }}</div>
        </div>
        @endforeach
      </div>
    </div>

    {{-- Détails --}}
    <div class="card-pro mb-4">
      <div class="card-pro-hdr">
        <div class="card-pro-title"><span class="icon-dot"></span>Paramètres du financement</div>
      </div>
      <div class="card-pro-body">
        <div class="row g-3">
          @foreach([
            ['Référence','reference'],['Archive','archive_ref'],
            ['Durée','darly'],['Taux d\'intérêt','interest_rate'],
            ['Frais administratifs','admin_fees'],['Devise','currency'],
            ['Date de début','start_date'],['Date validation','validated_at'],
            ['Contrat envoyé','sent_at'],['Objet','objet'],
          ] as [$label,$field])
          <div class="col-6 col-sm-4">
            <div style="font-size:.7rem;color:var(--c-muted);margin-bottom:.2rem">{{ $label }}</div>
            <div style="font-size:.8375rem;font-weight:600;color:var(--c-navy)">
              @if($field==='darly') {{ $loan->darly }} mois
              @elseif($field==='interest_rate') {{ $loan->interest_rate }} %
              @elseif($field==='admin_fees') {{ $loan->admin_fees ? number_format($loan->admin_fees,2,',',' ').' '.$loan->currency : '—' }}
              @elseif(in_array($field,['start_date','validated_at','sent_at'])) {{ $loan->$field?->format('d/m/Y') ?? '—' }}
              @else {{ $loan->$field ?? '—' }}
              @endif
            </div>
          </div>
          @endforeach
        </div>
        @if($loan->special_conditions)
        <hr style="border:0;border-top:1px solid var(--c-border);margin:1rem 0">
        <div style="font-size:.7rem;color:var(--c-muted);margin-bottom:.35rem">Conditions particulières</div>
        <div style="font-size:.8125rem;color:var(--c-text)">{{ $loan->special_conditions }}</div>
        @endif
      </div>
    </div>

  </div>
</div>

{{-- Échéancier --}}
@if($loan->amortization_schedule)
<div class="card-pro mb-4">
  <div class="card-pro-hdr">
    <div class="card-pro-title"><span class="icon-dot"></span>Tableau d'amortissement</div>
    <span style="font-size:.75rem;color:var(--c-muted)">{{ count($loan->amortization_schedule) }} échéances</span>
  </div>
  <div style="max-height:340px;overflow-y:auto">
    <table class="pro-table w-100">
      <thead style="position:sticky;top:0">
        <tr>
          <th>N°</th><th>Mensualité</th><th>Capital remboursé</th><th>Intérêts</th><th>Solde restant</th>
        </tr>
      </thead>
      <tbody>
        @foreach($loan->amortization_schedule as $row)
        <tr>
          <td style="color:var(--c-muted);font-size:.78rem">{{ $row['month'] }}</td>
          <td style="font-weight:600">{{ number_format($row['payment'],2,',',' ') }} {{ $loan->currency }}</td>
          <td>{{ number_format($row['principal'],2,',',' ') }} {{ $loan->currency }}</td>
          <td style="color:var(--c-red)">{{ number_format($row['interest'],2,',',' ') }} {{ $loan->currency }}</td>
          <td style="color:var(--c-muted)">{{ number_format($row['balance'],2,',',' ') }} {{ $loan->currency }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endif

{{-- Historique --}}
@if($loan->history->count())
<div class="card-pro">
  <div class="card-pro-hdr">
    <div class="card-pro-title"><span class="icon-dot"></span>Journal d'activité</div>
  </div>
  <div class="card-pro-body" style="padding:0">
    @foreach($loan->history as $h)
    <div style="display:flex;align-items:flex-start;gap:1rem;padding:.875rem 1.25rem;border-bottom:1px solid var(--c-border)">
      <div style="width:32px;height:32px;border-radius:50%;background:var(--c-bg);display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:.75rem;color:var(--c-muted)">
        <i class="fas fa-history"></i>
      </div>
      <div style="flex:1;min-width:0">
        <div style="font-size:.8125rem;font-weight:600;color:var(--c-navy)">{{ $h->action }}</div>
        <div style="font-size:.75rem;color:var(--c-muted)">{{ $h->admin?->name ?? 'Système' }} · {{ $h->created_at->format('d/m/Y à H:i') }}</div>
      </div>
    </div>
    @endforeach
  </div>
</div>
@endif

@endsection
