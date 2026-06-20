@extends('layouts.dashboard')
@section('title','Modifier '.$loan->reference)
@section('page_title','Modifier le dossier')

@section('content')

<div class="d-flex align-items-start justify-content-between flex-wrap gap-3 page-hdr">
  <div>
    <h4>Modifier — <span style="font-family:monospace;color:var(--c-gold)">{{ $loan->reference }}</span></h4>
    <p>Les calculs et le contrat seront automatiquement régénérés à la sauvegarde</p>
  </div>
  <a href="{{ route('admin.loans.show',$loan) }}" class="btn-ghost btn-sm-pro">
    <i class="fas fa-arrow-left"></i> Retour
  </a>
</div>

@if($errors->any())
<div class="flash flash-err mb-4"><i class="fas fa-exclamation-triangle"></i> {{ $errors->first() }}</div>
@endif

<form action="{{ route('admin.loans.update',$loan) }}" method="POST" x-data="loanForm()" x-init="calc()">
@csrf @method('PUT')
<div class="row g-4">

  {{-- ══ COLONNE GAUCHE ══ --}}
  <div class="col-xl-5">

    {{-- Client (lecture seule) --}}
    <div class="card-pro mb-4">
      <div class="card-pro-hdr">
        <div class="card-pro-title"><span class="icon-dot"></span>Client</div>
        <span class="badge-status bs-gray">Non modifiable</span>
      </div>
      <div class="card-pro-body">
        <div style="display:flex;align-items:center;gap:.875rem;margin-bottom:1rem">
          <div style="width:44px;height:44px;border-radius:50%;background:linear-gradient(135deg,var(--c-navy),var(--c-navy-3));display:flex;align-items:center;justify-content:center;color:var(--c-gold);font-weight:800;font-size:1rem;flex-shrink:0">
            {{ strtoupper(substr($loan->name,0,1)) }}
          </div>
          <div>
            <div style="font-weight:700;color:var(--c-navy)">{{ $loan->name }}</div>
            <div style="font-size:.78rem;color:var(--c-muted)">{{ $loan->email }}</div>
          </div>
        </div>
        <div class="d-flex gap-2 align-items-center" style="padding:.625rem .875rem;background:var(--c-bg);border-radius:var(--radius-sm);font-size:.78rem;color:var(--c-muted)">
          <i class="fas fa-info-circle" style="color:var(--c-blue)"></i>
          Pour changer le client, supprimez ce dossier et créez-en un nouveau.
        </div>
      </div>
    </div>

    {{-- Conditions --}}
    <div class="card-pro">
      <div class="card-pro-hdr">
        <div class="card-pro-title"><span class="icon-dot"></span>Conditions contractuelles</div>
      </div>
      <div class="card-pro-body">
        <div class="row g-3">
          <div class="col-12">
            <label class="form-label-pro">Frais administratifs</label>
            <div class="d-flex gap-2">
              <input type="number" name="admin_fees" class="form-control-pro"
                     value="{{ old('admin_fees',$loan->admin_fees) }}" step="0.01" min="0" style="flex:1">
              <div style="padding:.6rem .875rem;background:var(--c-bg);border:1.5px solid var(--c-border);border-radius:var(--radius-sm);font-weight:700;font-size:.8125rem;color:var(--c-navy);flex-shrink:0" x-text="currency">{{ $loan->currency }}</div>
            </div>
          </div>
          <div class="col-12">
            <label class="form-label-pro">Compte de règlement</label>
            <input type="text" name="bank_account" class="form-control-pro"
                   value="{{ old('bank_account',$loan->bank_account) }}">
            <p class="form-help">Communiqué au client après signature</p>
          </div>
          <div class="col-12">
            <label class="form-label-pro">Conditions particulières</label>
            <textarea name="special_conditions" class="form-control-pro" rows="3">{{ old('special_conditions',$loan->special_conditions) }}</textarea>
          </div>
          <div class="col-12">
            <label class="form-label-pro">Modèle de contrat</label>
            <select name="contract_template_id" class="form-control-pro">
              <option value="">— Modèle par défaut —</option>
              @foreach($templates as $tpl)
              <option value="{{ $tpl->id }}" {{ old('contract_template_id',$loan->contract_template_id)==$tpl->id?'selected':'' }}>
                {{ $tpl->name }} {{ $tpl->is_default?'✓':'' }}
              </option>
              @endforeach
            </select>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- ══ COLONNE DROITE ══ --}}
  <div class="col-xl-7">

    <div class="card-pro mb-4">
      <div class="card-pro-hdr">
        <div class="card-pro-title"><span class="icon-dot"></span>Paramètres du financement</div>
      </div>
      <div class="card-pro-body">
        <div class="row g-3">
          <div class="col-sm-6">
            <label class="form-label-pro">Montant *</label>
            <div class="d-flex gap-2">
              <input type="number" name="amount" class="form-control-pro"
                     value="{{ old('amount',$loan->amount) }}" step="100" min="100"
                     x-model.number="amount" @input="calc()" style="flex:1">
              <select name="currency" class="form-control-pro" style="width:90px;flex-shrink:0" x-model="currency">
                @foreach($currencies as $cur)
                <option value="{{ $cur }}" {{ old('currency',$loan->currency)===$cur?'selected':'' }}>{{ $cur }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="col-sm-6">
            <label class="form-label-pro">Durée (mois) *</label>
            <input type="number" name="darly" class="form-control-pro"
                   value="{{ old('darly',$loan->darly) }}" min="1" max="360"
                   x-model.number="duration" @input="calc()">
          </div>
          <div class="col-sm-6">
            <label class="form-label-pro">Taux d'intérêt annuel</label>
            <input type="text" class="form-control-pro" value="5 %" readonly style="background:var(--c-bg)">
          </div>
          <div class="col-sm-6">
            <label class="form-label-pro">Date de première échéance</label>
            <input type="date" name="start_date" class="form-control-pro"
                   value="{{ old('start_date',$loan->start_date?->format('Y-m-d')) }}">
          </div>
          <div class="col-12">
            <label class="form-label-pro">Objet du prêt</label>
            <input type="text" name="objet" class="form-control-pro" value="{{ old('objet',$loan->objet) }}">
          </div>
          <div class="col-12">
            <label class="form-label-pro">Remarques internes</label>
            <textarea name="subject" class="form-control-pro" rows="2">{{ old('subject',$loan->subject) }}</textarea>
          </div>
        </div>
      </div>
    </div>

    {{-- Simulation --}}
    <div class="summary-box mb-4">
      <div style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:rgba(255,255,255,.35);margin-bottom:1rem">
        Simulation mise à jour
      </div>
      <div class="row g-3">
        <div class="col-sm-4">
          <div class="summary-box__label">Mensualité</div>
          <div class="summary-box__val" x-text="fmt(monthly)+' '+currency">—</div>
        </div>
        <div class="col-sm-4">
          <div class="summary-box__label">Coût crédit</div>
          <div class="summary-box__val" style="color:rgba(255,255,255,.7)" x-text="fmt(totalCost)+' '+currency">—</div>
        </div>
        <div class="col-sm-4">
          <div class="summary-box__label">Total remboursement</div>
          <div class="summary-box__val" style="color:rgba(255,255,255,.7)" x-text="fmt(totalInterest)+' '+currency">—</div>
        </div>
      </div>
    </div>

    <div class="card-pro mb-4" x-show="schedule.length > 0">
      <div class="card-pro-hdr">
        <div class="card-pro-title"><span class="icon-dot"></span>Aperçu des 5 premières échéances</div>
      </div>
      <table class="pro-table w-100">
        <thead>
          <tr><th>Mois</th><th>Mensualité</th><th>Capital</th><th>Intérêts</th><th>Solde restant</th></tr>
        </thead>
        <tbody>
          <template x-for="row in schedule.slice(0,5)" :key="row.month">
            <tr>
              <td x-text="row.month" style="color:var(--c-muted)"></td>
              <td x-text="fmt(row.payment)+' '+currency" style="font-weight:600"></td>
              <td x-text="fmt(row.principal)+' '+currency"></td>
              <td x-text="fmt(row.interest)+' '+currency" style="color:var(--c-red)"></td>
              <td x-text="fmt(row.balance)+' '+currency" style="color:var(--c-muted)"></td>
            </tr>
          </template>
        </tbody>
      </table>
    </div>

    <div class="d-flex justify-content-end gap-3">
      <a href="{{ route('admin.loans.show',$loan) }}" class="btn-ghost">Annuler</a>
      <button type="submit" class="btn-navy">
        <i class="fas fa-save"></i> Enregistrer &amp; régénérer le contrat
      </button>
    </div>
  </div>
</div>
</form>
@endsection

@push('scripts')
<script>
function loanForm(){
  return {
    amount:{{ old('amount',$loan->amount??0) }}, duration:{{ old('darly',$loan->darly??12) }},
    rate:5, currency:'{{ old('currency',$loan->currency??'EUR') }}',
    monthly:0, totalCost:0, totalInterest:0, schedule:[],
    calc(){
      if(!this.amount||!this.duration){this.monthly=0;return;}
      const r=this.rate/100/12,n=this.duration,P=this.amount;
      const m=r===0?P/n:(P*r*Math.pow(1+r,n))/(Math.pow(1+r,n)-1);
      this.monthly=Math.round(m*100)/100;
      this.totalInterest=Math.round(m*n*100)/100;
      this.totalCost=Math.round((this.totalInterest-P)*100)/100;
      let bal=P;this.schedule=[];
      for(let i=1;i<=n;i++){
        const int=Math.round(bal*r*100)/100;
        const prin=Math.round((m-int)*100)/100;
        bal=Math.max(0,Math.round((bal-prin)*100)/100);
        this.schedule.push({month:i,payment:m,principal:prin,interest:int,balance:bal});
      }
    },
    fmt(v){return new Intl.NumberFormat('fr-FR',{minimumFractionDigits:2,maximumFractionDigits:2}).format(v||0);}
  }
}
</script>
@endpush
