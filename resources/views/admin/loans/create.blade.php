@extends('layouts.dashboard')
@section('title','Nouvelle demande')
@section('page_title','Nouvelle demande de prêt')

@section('content')

<div class="d-flex align-items-start justify-content-between flex-wrap gap-3 page-hdr">
  <div>
    <h4>Nouvelle demande de prêt</h4>
    <p>Renseignez les informations client et les paramètres du financement</p>
  </div>
  <a href="{{ route('admin.loans.index') }}" class="btn-ghost btn-sm-pro">
    <i class="fas fa-arrow-left"></i> Retour
  </a>
</div>

@if($errors->any())
<div class="flash flash-err mb-4"><i class="fas fa-exclamation-triangle"></i> {{ $errors->first() }}</div>
@endif

<form action="{{ route('admin.loans.store') }}" method="POST" x-data="loanForm()" x-init="calc()">
@csrf
<div class="row g-4">

  {{-- ══ COLONNE GAUCHE ══ --}}
  <div class="col-xl-5">

    {{-- Section : Client --}}
    <div class="card-pro mb-4">
      <div class="card-pro-hdr">
        <div class="card-pro-title"><span class="icon-dot"></span>Client</div>
        <div style="display:flex;gap:.5rem">
          <button type="button"
            @click="clientMode='existing'"
            :class="clientMode==='existing'?'btn-navy btn-sm-pro':'btn-ghost btn-sm-pro'">
            Existant
          </button>
          <button type="button"
            @click="clientMode='new'"
            :class="clientMode==='new'?'btn-navy btn-sm-pro':'btn-ghost btn-sm-pro'">
            Nouveau
          </button>
        </div>
      </div>
      <div class="card-pro-body">
        <input type="hidden" name="client_mode" :value="clientMode">

        {{-- Client existant --}}
        <div x-show="clientMode==='existing'" x-cloak>
          <label class="form-label-pro">Sélectionner un client *</label>
          <select name="client_id" class="form-control-pro" x-on:change="loadClient($event)">
            <option value="">— Choisir —</option>
            @foreach($myClients as $c)
            <option value="{{ $c->id }}"
              data-currency="{{ $c->currency }}"
              data-locale="{{ $c->locale }}"
              {{ old('client_id')==$c->id?'selected':'' }}>
              {{ $c->name }} — {{ $c->email }}
            </option>
            @endforeach
          </select>
        </div>

        {{-- Nouveau client --}}
        <div x-show="clientMode==='new'" x-cloak>
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label-pro">Nom complet *</label>
              <input type="text" name="client_name" class="form-control-pro" value="{{ old('client_name') }}" placeholder="Jean Dupont">
            </div>
            <div class="col-12">
              <label class="form-label-pro">Email *</label>
              <input type="email" name="client_email" class="form-control-pro" value="{{ old('client_email') }}" placeholder="jean@exemple.com">
            </div>
            <div class="col-6">
              <label class="form-label-pro">Téléphone</label>
              <input type="text" name="client_phone" class="form-control-pro" value="{{ old('client_phone') }}">
            </div>
            <div class="col-6">
              <label class="form-label-pro">Date de naissance</label>
              <input type="date" name="client_birth_date" class="form-control-pro" value="{{ old('client_birth_date') }}">
            </div>
            <div class="col-12">
              <label class="form-label-pro">Adresse</label>
              <input type="text" name="client_address" class="form-control-pro" value="{{ old('client_address') }}" placeholder="Rue, ville, pays">
            </div>
            {{-- Pièce d'identité --}}
            <div class="col-12">
              <div style="height:1px;background:var(--c-border);margin:.25rem 0"></div>
              <p style="font-size:.72rem;font-weight:700;color:var(--c-gold);text-transform:uppercase;letter-spacing:.06em;margin:.5rem 0 .625rem">
                <i class="fas fa-id-card" style="margin-right:.35rem"></i>Pièce d'identité
              </p>
            </div>
            <div class="col-7">
              <label class="form-label-pro">Type de pièce</label>
              <select name="client_id_type" class="form-control-pro">
                <option value="">— Non renseigné</option>
                <option value="passeport"       {{ old('client_id_type')==='passeport'       ?'selected':'' }}>Passeport</option>
                <option value="cni"             {{ old('client_id_type')==='cni'             ?'selected':'' }}>Carte nationale d'identité</option>
                <option value="permis_conduire" {{ old('client_id_type')==='permis_conduire' ?'selected':'' }}>Permis de conduire</option>
                <option value="titre_sejour"    {{ old('client_id_type')==='titre_sejour'    ?'selected':'' }}>Titre de séjour</option>
                <option value="autre"           {{ old('client_id_type')==='autre'           ?'selected':'' }}>Autre document</option>
              </select>
            </div>
            <div class="col-5">
              <label class="form-label-pro">Numéro</label>
              <input type="text" name="client_id_number" class="form-control-pro"
                     value="{{ old('client_id_number') }}" placeholder="Ex : AB123456">
            </div>
            {{-- /Pièce d'identité --}}
            <div class="col-6">
              <label class="form-label-pro">Devise</label>
              <select name="client_currency" class="form-control-pro" x-model="currency">
                @foreach($currencies as $cur)
                <option value="{{ $cur }}" {{ old('client_currency','EUR')===$cur?'selected':'' }}>{{ $cur }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-6">
              <label class="form-label-pro">Langue du contrat</label>
              <select name="client_locale" class="form-control-pro">
                <option value="fr" {{ old('client_locale','fr')==='fr'?'selected':'' }}>🇫🇷 Français</option>
                <option value="pl" {{ old('client_locale')==='pl'?'selected':'' }}>🇵🇱 Polski</option>
                <option value="en" {{ old('client_locale')==='en'?'selected':'' }}>🇬🇧 English</option>
                <option value="es" {{ old('client_locale')==='es'?'selected':'' }}>🇪🇸 Español</option>
              </select>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- Section : Conditions --}}
    <div class="card-pro">
      <div class="card-pro-hdr">
        <div class="card-pro-title"><span class="icon-dot"></span>Conditions contractuelles</div>
      </div>
      <div class="card-pro-body">
        <div class="row g-3">
          <div class="col-12">
            <label class="form-label-pro">Frais administratifs</label>
            <div class="d-flex gap-2">
              <input type="number" name="admin_fees" class="form-control-pro" value="{{ old('admin_fees') }}" step="0.01" min="0" placeholder="0.00" style="flex:1">
              <div style="padding:.6rem .875rem;background:var(--c-bg);border:1.5px solid var(--c-border);border-radius:var(--radius-sm);font-weight:700;font-size:.8125rem;color:var(--c-navy);flex-shrink:0" x-text="currency">EUR</div>
            </div>
          </div>
          <div class="col-12">
            <label class="form-label-pro">Compte de règlement</label>
            <input type="text" name="bank_account" class="form-control-pro" value="{{ old('bank_account') }}" placeholder="IBAN ou référence">
            <p class="form-help">Communiqué au client après signature du contrat</p>
          </div>
          <div class="col-12">
            <label class="form-label-pro">Conditions particulières</label>
            <textarea name="special_conditions" class="form-control-pro" rows="3" placeholder="Clauses spécifiques à ce dossier…">{{ old('special_conditions') }}</textarea>
          </div>
          <div class="col-12">
            <label class="form-label-pro">Modèle de contrat</label>
            <select name="contract_template_id" class="form-control-pro">
              <option value="">— Modèle par défaut —</option>
              @foreach($templates as $tpl)
              <option value="{{ $tpl->id }}" {{ old('contract_template_id')==$tpl->id?'selected':'' }}>
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

    {{-- Paramètres --}}
    <div class="card-pro mb-4">
      <div class="card-pro-hdr">
        <div class="card-pro-title"><span class="icon-dot"></span>Paramètres du financement</div>
      </div>
      <div class="card-pro-body">
        <div class="row g-3">
          <div class="col-sm-6">
            <label class="form-label-pro">Montant *</label>
            <div class="d-flex gap-2">
              <input type="number" name="amount" class="form-control-pro" value="{{ old('amount') }}"
                     step="100" min="100" placeholder="5 000"
                     x-model.number="amount" @input="calc()" style="flex:1">
              <select name="currency" class="form-control-pro" style="width:90px;flex-shrink:0" x-model="currency">
                @foreach($currencies as $cur)
                <option value="{{ $cur }}" {{ old('currency','EUR')===$cur?'selected':'' }}>{{ $cur }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="col-sm-6">
            <label class="form-label-pro">Durée (mois) *</label>
            <input type="number" name="darly" class="form-control-pro" value="{{ old('darly') }}"
                   min="1" max="360" placeholder="12" x-model.number="duration" @input="calc()">
          </div>
          <div class="col-sm-6">
            <label class="form-label-pro">Taux d'intérêt annuel</label>
            <div class="d-flex gap-2 align-items-center">
              <input type="text" class="form-control-pro" value="5 %" readonly
                     style="background:var(--c-bg);cursor:not-allowed;flex:1">
            </div>
            <p class="form-help">Taux fixe — automatiquement appliqué</p>
          </div>
          <div class="col-sm-6">
            <label class="form-label-pro">Date de première échéance</label>
            <input type="date" name="start_date" class="form-control-pro"
                   value="{{ old('start_date',now()->format('Y-m-d')) }}">
          </div>
          <div class="col-12">
            <label class="form-label-pro">Objet du prêt</label>
            <input type="text" name="objet" class="form-control-pro" value="{{ old('objet') }}"
                   placeholder="Ex : Financement personnel, acquisition immobilière…">
          </div>
          <div class="col-12">
            <label class="form-label-pro">Remarques internes</label>
            <textarea name="subject" class="form-control-pro" rows="2"
                      placeholder="Notes visibles uniquement par les administrateurs">{{ old('subject') }}</textarea>
          </div>
        </div>
      </div>
    </div>

    {{-- Simulation en temps réel --}}
    <div class="summary-box mb-4" x-show="amount > 0 && duration > 0">
      <div style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:rgba(255,255,255,.35);margin-bottom:1rem">
        Simulation automatique
      </div>
      <div class="row g-3">
        <div class="col-sm-4">
          <div class="summary-box__label">Mensualité estimée</div>
          <div class="summary-box__val" x-text="fmt(monthly)+' '+currency">—</div>
        </div>
        <div class="col-sm-4">
          <div class="summary-box__label">Coût total des intérêts</div>
          <div class="summary-box__val" style="color:rgba(255,255,255,.7)" x-text="fmt(totalCost)+' '+currency">—</div>
        </div>
        <div class="col-sm-4">
          <div class="summary-box__label">Total à rembourser</div>
          <div class="summary-box__val" style="color:rgba(255,255,255,.7)" x-text="fmt(totalInterest)+' '+currency">—</div>
        </div>
      </div>
    </div>

    {{-- Aperçu échéancier --}}
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
      <a href="{{ route('admin.loans.index') }}" class="btn-ghost">Annuler</a>
      <button type="submit" class="btn-navy">
        <i class="fas fa-save"></i> Créer le dossier &amp; générer le contrat
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
    amount:{{ old('amount',0) }}, duration:{{ old('darly',12) }}, rate:5,
    currency:'{{ old('currency','EUR') }}', clientMode:'{{ old('client_mode','existing') }}',
    monthly:0, totalCost:0, totalInterest:0, schedule:[],
    loadClient(e){const o=e.target.selectedOptions[0];if(o&&o.dataset.currency)this.currency=o.dataset.currency;},
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
