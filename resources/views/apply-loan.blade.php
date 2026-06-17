@extends('layouts.app')
@section('title', __('menu.loan'))

@push('styles')
<style>
/* ── Indicateur d'étapes (barre du haut) ── */
.stepper { display:flex; align-items:flex-start; margin-bottom:2rem; }
.stepper__item { flex:1; display:flex; flex-direction:column; align-items:center; gap:.5rem; position:relative; z-index:1; }
.stepper__circle {
    width:46px; height:46px; border-radius:50%; border:3px solid transparent;
    display:flex; align-items:center; justify-content:center;
    font-weight:800; font-size:1rem; transition:all .35s;
}
.stepper__circle--idle   { background:#f3f4f6; border-color:#e5e7eb; color:#9ca3af; }
.stepper__circle--active { background:var(--gold); border-color:var(--gold); color:#fff; box-shadow:0 0 0 5px rgba(212,175,55,.18); }
.stepper__circle--done   { background:var(--navy); border-color:var(--navy); color:#fff; }
.stepper__label { font-size:.74rem; font-weight:600; text-align:center; line-height:1.3; max-width:110px; }
.stepper__label--idle   { color:#9ca3af; }
.stepper__label--active { color:var(--gold); }
.stepper__label--done   { color:var(--navy); }
.stepper__connector { position:absolute; top:23px; left:calc(50% + 24px); right:calc(-50% + 24px); height:3px; background:#e5e7eb; border-radius:2px; transition:background .35s; z-index:-1; }
.stepper__connector--done { background:var(--navy); }

/* ── Sous-étapes numérotées ── */
.substep { margin-bottom:1.75rem; }
.substep__header { display:flex; align-items:flex-start; gap:.75rem; margin-bottom:.65rem; }
.substep__num {
    width:30px; height:30px; border-radius:50%; flex-shrink:0;
    background:var(--gold); color:#fff; font-size:.82rem; font-weight:800;
    display:flex; align-items:center; justify-content:center;
    box-shadow:0 2px 8px rgba(212,175,55,.3);
}
.substep__title { font-size:.9rem; font-weight:700; color:var(--navy); line-height:1.4; padding-top:.15rem; }
.substep__hint  { font-size:.76rem; color:#9ca3af; line-height:1.5; margin:.1rem 0 0 2.45rem; }

/* ── Boutons devise ── */
.currency-btn {
    display:flex; align-items:center; gap:.5rem;
    padding:.55rem 1rem; border-radius:10px; border:2px solid #e5e7eb;
    background:#fff; cursor:pointer; transition:all .2s; user-select:none;
    white-space:nowrap;
}
.currency-btn:hover { border-color:var(--gold); }
.currency-btn.active { background:var(--navy); border-color:var(--navy); color:#fff; box-shadow:0 3px 12px rgba(10,37,76,.2); }
.currency-btn__flag { font-size:1.2rem; line-height:1; }
.currency-btn__name { font-size:.8rem; font-weight:700; line-height:1.1; }
.currency-btn__sym  { font-size:.72rem; opacity:.65; }

/* ── Cartes devis prédéfinis ── */
.quote-card {
    border:2px solid #e5e7eb; border-radius:14px; padding:1.25rem .9rem;
    cursor:pointer; transition:all .25s; text-align:center; background:#fff;
    height:100%; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:.35rem;
    position:relative;
}
.quote-card:hover { border-color:var(--gold); box-shadow:0 4px 18px rgba(212,175,55,.2); transform:translateY(-2px); }
.quote-card.selected { border-color:var(--navy); background:var(--navy); color:#fff; box-shadow:0 6px 24px rgba(10,37,76,.22); transform:translateY(-2px); }
.quote-card__check {
    position:absolute; top:8px; right:8px; width:22px; height:22px;
    border-radius:50%; background:var(--gold); color:#fff;
    display:flex; align-items:center; justify-content:center; font-size:.65rem;
}
.quote-card__amount  { font-size:1.3rem; font-weight:800; font-family:'Playfair Display',serif; }
.quote-card__dur     { font-size:.72rem; opacity:.6; margin-top:.1rem; }
.quote-card__monthly { font-size:.9rem; font-weight:700; color:var(--gold); margin-top:.25rem; }
.quote-card.selected .quote-card__monthly { color:#FFD700; }
.quote-card__rate    { font-size:.65rem; font-weight:600; background:rgba(212,175,55,.12); color:var(--gold); border-radius:4px; padding:.1rem .4rem; margin-top:.2rem; }
.quote-card.selected .quote-card__rate   { background:rgba(255,215,0,.2); color:#FFD700; }

/* ── Lien "autre montant" ── */
.custom-toggle {
    display:inline-flex; align-items:center; gap:.4rem;
    color:var(--navy); font-size:.8rem; font-weight:600;
    border:none; background:none; cursor:pointer; padding:.4rem 0;
    text-decoration:underline; text-underline-offset:3px; opacity:.7;
}
.custom-toggle:hover { opacity:1; color:var(--gold); }

/* ── Chips sélection custom ── */
.chip-group { display:flex; flex-wrap:wrap; gap:.4rem; }
.chip {
    padding:.38rem .85rem; border-radius:999px; border:2px solid #d1d5db;
    background:#fff; color:#374151; font-size:.82rem; font-weight:600;
    cursor:pointer; transition:all .2s; white-space:nowrap;
}
.chip:hover { border-color:var(--gold); color:var(--gold); }
.chip.active { background:var(--navy); color:#fff; border-color:var(--navy); }
.chip.active-gold { background:var(--gold); color:#fff; border-color:var(--gold); }

/* ── Résumé devis ── */
.quote-result {
    background:linear-gradient(135deg,var(--navy) 0%,#183560 100%);
    border-radius:14px; padding:1.25rem 1.4rem; color:#fff;
}
.quote-result__row { display:flex; flex-wrap:wrap; gap:1rem; justify-content:space-between; margin-bottom:.85rem; }
.quote-result__item { text-align:center; flex:1; min-width:90px; }
.quote-result__label { font-size:.65rem; text-transform:uppercase; letter-spacing:.08em; color:rgba(255,255,255,.5); display:block; margin-bottom:.25rem; }
.quote-result__value { font-size:1.05rem; font-weight:800; color:#fff; }
.quote-result__value.gold { color:var(--gold); font-size:1.3rem; }
.quote-result__sep { width:1px; height:36px; background:rgba(255,255,255,.15); }

/* ── Recap banner ── */
.recap-banner {
    background:linear-gradient(135deg,var(--navy) 0%,#183560 100%);
    border-radius:12px; padding:1rem 1.25rem;
    display:flex; align-items:center; justify-content:space-around; flex-wrap:wrap; gap:.6rem;
}
.recap-banner__item { text-align:center; }
.recap-banner__label { font-size:.63rem; text-transform:uppercase; letter-spacing:.07em; color:rgba(255,255,255,.5); display:block; }
.recap-banner__value { font-size:.95rem; font-weight:700; color:#fff; }
.recap-banner__value.gold { color:var(--gold); font-size:1.15rem; }

/* ── Raisons sidebar ── */
.reason-item { display:flex; gap:.8rem; padding:.85rem 0; }
.reason-item + .reason-item { border-top:1px solid #f0f0f0; }
.reason-icon { width:40px; height:40px; border-radius:10px; background:rgba(212,175,55,.1); color:var(--gold); display:flex; align-items:center; justify-content:center; font-size:.95rem; flex-shrink:0; margin-top:.1rem; }
.reason-title { font-size:.86rem; font-weight:700; color:var(--navy); margin-bottom:.2rem; }
.reason-desc  { font-size:.77rem; color:#6b7280; line-height:1.5; margin:0; }

[x-cloak] { display:none !important; }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('loanForm', () => ({
        step: 1,
        selCurrency: 'EUR',
        selPresetId: null,
        showCustom:  false,
        selAmount:   null,
        customAmt:   '',
        showAmt:     false,
        selDuration: null,
        customDur:   '',
        showDur:     false,
        rate: 5,

        /* Textes localisés */
        monthsLabel: "{{ __('message.months') }}",
        monthAbbr:   "{{ __('message.month_abbr') }}",
        locale:      "{{ str_replace('_','-',app()->getLocale()) }}",

        currencies: [
            { code:'EUR', symbol:'€',  flag:'🇪🇺', name:'Euro'       },
            { code:'PLN', symbol:'zł', flag:'🇵🇱', name:'Złoty'      },
            { code:'USD', symbol:'$',  flag:'🇺🇸', name:'Dollar US'  },
            { code:'BRL', symbol:'R$', flag:'🇧🇷', name:'Réal (BRL)' },
            { code:'MXN', symbol:'$',  flag:'🇲🇽', name:'Peso (MXN)' },
        ],

        presetsByCurrency: {
            EUR:[{id:1,amount:5000,duration:24},{id:2,amount:10000,duration:36},{id:3,amount:20000,duration:48},{id:4,amount:50000,duration:60}],
            PLN:[{id:1,amount:20000,duration:24},{id:2,amount:50000,duration:36},{id:3,amount:100000,duration:48},{id:4,amount:250000,duration:60}],
            USD:[{id:1,amount:5000,duration:24},{id:2,amount:10000,duration:36},{id:3,amount:25000,duration:48},{id:4,amount:60000,duration:60}],
            BRL:[{id:1,amount:25000,duration:24},{id:2,amount:50000,duration:36},{id:3,amount:100000,duration:48},{id:4,amount:250000,duration:60}],
            MXN:[{id:1,amount:100000,duration:24},{id:2,amount:200000,duration:36},{id:3,amount:500000,duration:48},{id:4,amount:1000000,duration:60}],
        },
        amountsByCurrency: {
            EUR:[1000,3000,8000,15000,30000,75000],
            PLN:[5000,15000,30000,60000,120000,300000],
            USD:[1000,3000,8000,15000,30000,75000],
            BRL:[5000,15000,30000,60000,120000,300000],
            MXN:[20000,50000,100000,250000,500000,1000000],
        },

        get presets()  { return this.presetsByCurrency[this.selCurrency] || this.presetsByCurrency['EUR']; },
        get amounts()  { return this.amountsByCurrency[this.selCurrency] || this.amountsByCurrency['EUR']; },
        get currency() { return this.currencies.find(c => c.code === this.selCurrency) || this.currencies[0]; },

        get amount() {
            if (this.selPresetId !== null) {
                const p = this.presets.find(p => p.id === this.selPresetId);
                if (p) return p.amount;
            }
            return this.showAmt ? parseFloat(this.customAmt) : this.selAmount;
        },
        get duration() {
            if (this.selPresetId !== null) {
                const p = this.presets.find(p => p.id === this.selPresetId);
                if (p) return p.duration;
            }
            return this.showDur ? parseInt(this.customDur) : this.selDuration;
        },
        get monthly() {
            const p = parseFloat(this.amount), n = parseInt(this.duration);
            const r = this.rate / 100 / 12;
            if (!p || !n || p <= 0 || n <= 0 || isNaN(p) || isNaN(n)) return null;
            return (p * r * Math.pow(1+r,n)) / (Math.pow(1+r,n) - 1);
        },
        get total()     { return this.monthly ? this.monthly * parseInt(this.duration) : null; },
        get interests() { return (this.total && this.amount) ? this.total - parseFloat(this.amount) : null; },
        get canProceed(){ return this.monthly !== null; },

        fmt(v, dec=2) {
            if (v === null || v === undefined || isNaN(v)) return '—';
            try {
                return new Intl.NumberFormat(this.locale, {
                    style:'currency', currency:this.selCurrency,
                    minimumFractionDigits:dec, maximumFractionDigits:dec,
                }).format(v);
            } catch(e) { return v.toFixed(dec) + ' ' + this.selCurrency; }
        },
        fmtAmt(v) {
            if (!v) return '—';
            try {
                return new Intl.NumberFormat(this.locale, {
                    style:'currency', currency:this.selCurrency,
                    minimumFractionDigits:0, maximumFractionDigits:0,
                }).format(v);
            } catch(e) { return v + ' ' + this.selCurrency; }
        },
        monthlyForPreset(preset) {
            const p = preset.amount, n = preset.duration, r = this.rate / 100 / 12;
            return (p * r * Math.pow(1+r,n)) / (Math.pow(1+r,n) - 1);
        },

        setCurrency(code) {
            if (this.selCurrency === code) return;
            this.selCurrency = code;
            this.selPresetId = null; this.selAmount = null;
            this.showAmt = false; this.customAmt = '';
            this.showCustom = false;
        },
        pickPreset(preset) {
            this.selPresetId = preset.id;
            this.selAmount = null; this.showAmt = false; this.customAmt = '';
            this.selDuration = null; this.showDur = false; this.customDur = '';
            this.showCustom = false;
        },
        pickAmount(v) {
            this.selPresetId = null; this.selAmount = v;
            this.showAmt = false; this.customAmt = '';
        },
        pickDuration(v) {
            this.selPresetId = null; this.selDuration = v;
            this.showDur = false; this.customDur = '';
        },
        openCustomAmt() {
            this.selPresetId = null; this.showAmt = true; this.selAmount = null;
            this.$nextTick(() => this.$refs.customAmtInput?.focus());
        },
        openCustomDur() {
            this.selPresetId = null; this.showDur = true; this.selDuration = null;
            this.$nextTick(() => this.$refs.customDurInput?.focus());
        },
        goToStep2() {
            if (!this.canProceed) return;
            this.step = 2;
            this.$nextTick(() => {
                document.getElementById('step2-section')?.scrollIntoView({ behavior:'smooth', block:'start' });
            });
        },
        goBack() {
            this.step = 1;
            this.$nextTick(() => {
                document.getElementById('step1-section')?.scrollIntoView({ behavior:'smooth', block:'start' });
            });
        },
    }));
});
</script>
@endpush

@section('content')
@php $locale = app()->getLocale(); @endphp

<div class="page-hero">
    <div class="container">
        <div class="page-hero__content">
            <h1 class="page-hero__title">@lang('menu.loan')</h1>
            <ul class="page-hero__breadcrumb">
                <li><a href="{{ route('home', ['locale' => $locale]) }}">@lang('menu.home')</a></li>
                <li class="sep"><i class="fas fa-chevron-right"></i></li>
                <li>@lang('menu.loan')</li>
            </ul>
        </div>
    </div>
</div>

<section class="py-24 bg-white">
    <div class="container">
        <div class="row g-4 align-items-start">

            {{-- ══════════════════════ COLONNE PRINCIPALE ══════════════════════ --}}
            <div class="col-lg-8" x-data="loanForm">

                {{-- Barre de progression (étapes 1 → 2) --}}
                <div class="stepper">
                    <div class="stepper__item">
                        <div class="stepper__connector"
                             :class="step > 1 ? 'stepper__connector--done' : ''"></div>
                        <div class="stepper__circle"
                             :class="step > 1 ? 'stepper__circle--done' : 'stepper__circle--active'">
                            <template x-if="step > 1"><i class="fas fa-check" style="font-size:.8rem;"></i></template>
                            <template x-if="step === 1"><span>1</span></template>
                        </div>
                        <span class="stepper__label"
                              :class="step > 1 ? 'stepper__label--done' : 'stepper__label--active'">
                            @lang('loan.label_choose')
                        </span>
                    </div>
                    <div class="stepper__item">
                        <div class="stepper__circle"
                             :class="step >= 2 ? 'stepper__circle--active' : 'stepper__circle--idle'">
                            <span>2</span>
                        </div>
                        <span class="stepper__label"
                              :class="step >= 2 ? 'stepper__label--active' : 'stepper__label--idle'">
                            @lang('loan.form_title')
                        </span>
                    </div>
                </div>

                {{-- ════════════════════ ÉTAPE 1 — CHOIX DU DEVIS ════════════════════ --}}
                <div id="step1-section" x-show="step === 1">
                    <div class="form-card wow fadeInLeft" data-wow-duration="700ms"
                         style="border-top:4px solid var(--gold); padding-bottom:1.5rem;">

                        {{-- En-tête --}}
                        <div class="d-flex align-items-start justify-content-between flex-wrap gap-2 mb-4">
                            <div>
                                <div class="section-label mb-1">@lang('loan.quote_step_label')</div>
                                <h3 style="font-family:'Playfair Display',serif;color:var(--navy);font-size:1.3rem;font-weight:700;margin:0 0 .2rem;">
                                    @lang('loan.quote_step_title')
                                </h3>
                                <p style="font-size:.8rem;color:#6b7280;margin:0;">@lang('loan.quote_step_desc')</p>
                            </div>
                            <div style="display:inline-flex;align-items:center;gap:.4rem;background:var(--navy);color:var(--gold);padding:.4rem 1rem;border-radius:999px;font-weight:800;font-size:.88rem;white-space:nowrap;">
                                <i class="fas fa-lock" style="font-size:.72rem;"></i>
                                @lang('loan.label_rate') : 5 %
                            </div>
                        </div>

                        {{-- ─── SOUS-ÉTAPE ① Devise ─── --}}
                        <div class="substep">
                            <div class="substep__header">
                                <div class="substep__num">①</div>
                                <div>
                                    <div class="substep__title">@lang('loan.label_currency')</div>
                                </div>
                            </div>
                            <div class="substep__hint">@lang('loan.currency_hint')</div>
                            <div class="d-flex flex-wrap gap-2 mt-2">
                                <template x-for="c in currencies" :key="c.code">
                                    <button type="button" class="currency-btn"
                                            :class="selCurrency === c.code ? 'active' : ''"
                                            @click="setCurrency(c.code)">
                                        <span class="currency-btn__flag" x-text="c.flag"></span>
                                        <div>
                                            <div class="currency-btn__name" x-text="c.name"></div>
                                            <div class="currency-btn__sym" x-text="c.code + ' ' + c.symbol"></div>
                                        </div>
                                    </button>
                                </template>
                            </div>
                        </div>

                        {{-- ─── SOUS-ÉTAPE ② Choix du devis ─── --}}
                        <div class="substep">
                            <div class="substep__header">
                                <div class="substep__num">②</div>
                                <div>
                                    <div class="substep__title">@lang('loan.preset_label')</div>
                                </div>
                            </div>
                            <div class="substep__hint">@lang('loan.preset_hint')</div>

                            {{-- Cartes devis --}}
                            <div class="row g-2 mt-2">
                                <template x-for="preset in presets" :key="preset.id">
                                    <div class="col-6 col-md-3">
                                        <div class="quote-card"
                                             :class="selPresetId === preset.id ? 'selected' : ''"
                                             @click="pickPreset(preset)">
                                            <div class="quote-card__check" x-show="selPresetId === preset.id">
                                                <i class="fas fa-check"></i>
                                            </div>
                                            <div class="quote-card__amount"
                                                 x-text="fmtAmt(preset.amount)"></div>
                                            <div class="quote-card__dur"
                                                 x-text="preset.duration + ' ' + monthsLabel"></div>
                                            <div class="quote-card__monthly"
                                                 x-text="fmt(monthlyForPreset(preset)) + ' / ' + monthAbbr"></div>
                                            <div class="quote-card__rate">5 %</div>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            {{-- Lien pour saisir un montant personnalisé --}}
                            <div class="mt-3">
                                <button type="button" class="custom-toggle"
                                        @click="showCustom = !showCustom; selPresetId = null;">
                                    <i class="fas" :class="showCustom ? 'fa-chevron-up' : 'fa-pencil-alt'"></i>
                                    <span x-text="showCustom ? '@lang('loan.custom_hide')' : '@lang('loan.custom_show')'"></span>
                                </button>
                            </div>

                            {{-- Panneau montant/durée personnalisés (réduit par défaut) --}}
                            <div x-show="showCustom" x-cloak
                                 x-transition:enter="transition ease-out duration-250"
                                 x-transition:enter-start="opacity-0 -translate-y-2"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 style="background:#fafafa;border:1px solid #e5e7eb;border-radius:10px;padding:1rem;margin-top:.75rem;">

                                <div class="mb-3">
                                    <div style="font-size:.78rem;font-weight:700;color:var(--navy);margin-bottom:.5rem;">
                                        <i class="fas fa-hand-holding-usd" style="color:var(--gold);margin-right:.3rem;"></i>
                                        @lang('loan.label_amount') (<span x-text="currency.code"></span>)
                                    </div>
                                    <div class="chip-group">
                                        <template x-for="v in amounts" :key="v">
                                            <button type="button" class="chip"
                                                    :class="selAmount === v && !showAmt && selPresetId === null ? 'active' : ''"
                                                    @click="pickAmount(v)"
                                                    x-text="fmtAmt(v)"></button>
                                        </template>
                                        <button type="button" class="chip"
                                                :class="showAmt ? 'active-gold' : ''"
                                                @click="openCustomAmt()">
                                            <i class="fas fa-pen" style="font-size:.7rem;margin-right:.25rem;"></i>
                                            @lang('loan.label_other')
                                        </button>
                                    </div>
                                    <div x-show="showAmt" x-cloak class="mt-2 d-flex align-items-center gap-2">
                                        <input type="number" x-ref="customAmtInput" x-model="customAmt"
                                               min="100" step="100" class="form-control" style="max-width:180px;"
                                               placeholder="Ex : 35 000">
                                        <span style="font-weight:700;color:var(--navy);" x-text="currency.symbol"></span>
                                    </div>
                                </div>

                                <div>
                                    <div style="font-size:.78rem;font-weight:700;color:var(--navy);margin-bottom:.5rem;">
                                        <i class="fas fa-calendar-alt" style="color:var(--gold);margin-right:.3rem;"></i>
                                        @lang('loan.label_darly')
                                    </div>
                                    <div class="chip-group">
                                        <template x-for="d in [12,24,36,48,60,84]" :key="d">
                                            <button type="button" class="chip"
                                                    :class="selDuration === d && !showDur && selPresetId === null ? 'active' : ''"
                                                    @click="pickDuration(d)"
                                                    x-text="d + ' ' + monthsLabel"></button>
                                        </template>
                                        <button type="button" class="chip"
                                                :class="showDur ? 'active-gold' : ''"
                                                @click="openCustomDur()">
                                            <i class="fas fa-pen" style="font-size:.7rem;margin-right:.25rem;"></i>
                                            @lang('loan.label_other')
                                        </button>
                                    </div>
                                    <div x-show="showDur" x-cloak class="mt-2 d-flex align-items-center gap-2">
                                        <input type="number" x-ref="customDurInput" x-model="customDur"
                                               min="1" max="360" class="form-control" style="max-width:140px;"
                                               placeholder="Ex : 72">
                                        <span style="color:#6b7280;font-size:.82rem;" x-text="monthsLabel"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ─── SOUS-ÉTAPE ③ Résumé + validation (apparaît quand sélection faite) ─── --}}
                        <div class="substep" x-show="canProceed" x-cloak
                             x-transition:enter="transition ease-out duration-350"
                             x-transition:enter-start="opacity-0 translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0">
                            <div class="substep__header">
                                <div class="substep__num" style="background:var(--navy);">③</div>
                                <div>
                                    <div class="substep__title">@lang('loan.quote_summary_title')</div>
                                </div>
                            </div>
                            <div class="substep__hint">@lang('loan.quote_summary_hint')</div>

                            <div class="quote-result mt-2">
                                <div class="quote-result__row">
                                    <div class="quote-result__item">
                                        <span class="quote-result__label">@lang('loan.quote_monthly')</span>
                                        <span class="quote-result__value gold" x-text="fmt(monthly)">—</span>
                                    </div>
                                    <div class="quote-result__sep d-none d-sm-block"></div>
                                    <div class="quote-result__item">
                                        <span class="quote-result__label">@lang('loan.quote_total')</span>
                                        <span class="quote-result__value" x-text="fmt(total)">—</span>
                                    </div>
                                    <div class="quote-result__sep d-none d-sm-block"></div>
                                    <div class="quote-result__item">
                                        <span class="quote-result__label">@lang('loan.quote_interest')</span>
                                        <span class="quote-result__value" style="color:rgba(255,255,255,.6);" x-text="fmt(interests)">—</span>
                                    </div>
                                </div>
                                <p style="font-size:.7rem;color:rgba(255,255,255,.4);margin:0 0 1rem;">
                                    <i class="fas fa-info-circle" style="margin-right:.3rem;"></i>
                                    @lang('loan.quote_hint')
                                </p>
                                <button type="button" @click="goToStep2()"
                                        class="btn-primary btn-primary--lg w-100 justify-content-center">
                                    <i class="fas fa-check-circle"></i>
                                    @lang('loan.label_choose')
                                    <i class="fas fa-arrow-right" style="font-size:.8rem;"></i>
                                </button>
                            </div>
                        </div>

                    </div>
                </div>{{-- /step1 --}}

                {{-- ════════════════════ ÉTAPE 2 — INFORMATIONS PERSONNELLES ════════════════════ --}}
                <div id="step2-section"
                     x-show="step === 2" x-cloak
                     x-transition:enter="transition ease-out duration-400"
                     x-transition:enter-start="opacity-0 translate-y-4"
                     x-transition:enter-end="opacity-100 translate-y-0">

                    {{-- Bouton retour --}}
                    <button type="button" @click="goBack()"
                            style="display:inline-flex;align-items:center;gap:.4rem;background:none;border:none;
                                   color:var(--navy);font-size:.82rem;font-weight:600;cursor:pointer;
                                   padding:0;margin-bottom:1rem;text-decoration:underline;text-underline-offset:3px;">
                        <i class="fas fa-arrow-left" style="font-size:.72rem;"></i>
                        @lang('loan.back_to_quote')
                    </button>

                    {{-- Bandeau récapitulatif --}}
                    <div class="recap-banner mb-3">
                        <div class="recap-banner__item">
                            <span class="recap-banner__label">@lang('loan.label_amount')</span>
                            <span class="recap-banner__value" x-text="fmtAmt(amount)">—</span>
                        </div>
                        <div style="width:1px;height:30px;background:rgba(255,255,255,.15);"></div>
                        <div class="recap-banner__item">
                            <span class="recap-banner__label">@lang('loan.label_darly')</span>
                            <span class="recap-banner__value" x-text="duration + ' ' + monthsLabel">—</span>
                        </div>
                        <div style="width:1px;height:30px;background:rgba(255,255,255,.15);"></div>
                        <div class="recap-banner__item">
                            <span class="recap-banner__label">@lang('loan.label_rate')</span>
                            <span class="recap-banner__value">5 %</span>
                        </div>
                        <div style="width:1px;height:30px;background:rgba(255,255,255,.15);"></div>
                        <div class="recap-banner__item">
                            <span class="recap-banner__label">@lang('loan.quote_monthly')</span>
                            <span class="recap-banner__value gold" x-text="fmt(monthly)">—</span>
                        </div>
                    </div>

                    {{-- Conditions d'éligibilité --}}
                    <div class="mb-3" style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;padding:.85rem 1.1rem;">
                        <div style="font-weight:700;color:#166534;font-size:.83rem;margin-bottom:.3rem;">
                            <i class="fas fa-shield-alt" style="margin-right:.4rem;"></i>
                            @lang('message.loan_conditions_title')
                        </div>
                        <p style="margin:0;color:#166534;font-size:.79rem;line-height:1.55;">
                            @lang('message.loan_conditions_text')
                        </p>
                    </div>

                    {{-- Formulaire --}}
                    <div class="form-card">
                        <div class="section-label mb-1">@lang('loan.form_title')</div>
                        <h2 class="section-title mb-1" style="font-size:1.15rem;">@lang('loan.form_description')</h2>
                        <p style="font-size:.8rem;color:#6b7280;margin-bottom:1.5rem;">@lang('loan.form_hint')</p>

                        @if (session('success'))
                            <div class="alert alert-success mb-3">{{ session('success') }}</div>
                        @endif
                        @if ($errors->any())
                            <div class="alert alert-danger mb-3">
                                @foreach ($errors->all() as $e)<div>{{ $e }}</div>@endforeach
                            </div>
                        @endif

                        <form method="POST" action="{{ route('loan.request') }}">
                            @csrf
                            <input type="hidden" name="locale"   value="{{ app()->getLocale() }}">
                            <input type="hidden" name="amount"   :value="amount">
                            <input type="hidden" name="darly"    :value="duration">
                            <input type="hidden" name="currency" :value="selCurrency">

                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label>@lang('loan.label_name') <span style="color:var(--gold);">*</span></label>
                                        <input type="text" name="name" class="form-control"
                                               value="{{ old('name') }}"
                                               placeholder="@lang('loan.placeholder_name')" required>
                                        @error('name')<span class="form-error">{{ $message }}</span>@enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>@lang('loan.label_email') <span style="color:var(--gold);">*</span></label>
                                        <input type="email" name="email" class="form-control"
                                               value="{{ old('email') }}"
                                               placeholder="@lang('loan.placeholder_email')" required>
                                        @error('email')<span class="form-error">{{ $message }}</span>@enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>@lang('loan.label_phone') <span style="color:var(--gold);">*</span></label>
                                        <input type="text" name="phone" class="form-control"
                                               value="{{ old('phone') }}"
                                               placeholder="@lang('loan.placeholder_phone')" required>
                                        @error('phone')<span class="form-error">{{ $message }}</span>@enderror
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label>@lang('contact.subject') <span style="color:var(--gold);">*</span></label>
                                        <select name="subject" class="form-control" required>
                                            <option value="">— @lang('contact.subject') —</option>
                                            <option value="Prêt personnel"  {{ old('subject')=='Prêt personnel'  ?'selected':'' }}>@lang('menu.personal')</option>
                                            <option value="Prêt immobilier" {{ old('subject')=='Prêt immobilier' ?'selected':'' }}>@lang('menu.home_loan')</option>
                                            <option value="Prêt commercial" {{ old('subject')=='Prêt commercial' ?'selected':'' }}>@lang('menu.business')</option>
                                            <option value="Prêt étudiant"   {{ old('subject')=='Prêt étudiant'   ?'selected':'' }}>@lang('menu.study')</option>
                                            <option value="Prêt auto"       {{ old('subject')=='Prêt auto'       ?'selected':'' }}>@lang('menu.auto')</option>
                                            <option value="Prêt vélo"       {{ old('subject')=='Prêt vélo'       ?'selected':'' }}>@lang('menu.bike')</option>
                                        </select>
                                        @error('subject')<span class="form-error">{{ $message }}</span>@enderror
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label>@lang('loan.label_objet')
                                            <span style="font-size:.73rem;color:#9ca3af;font-weight:400;">({{ __('message.optional') }})</span>
                                        </label>
                                        <textarea name="objet" class="form-control" rows="3"
                                                  placeholder="@lang('loan.placeholder_objet')">{{ old('objet') }}</textarea>
                                    </div>
                                </div>
                                <div class="col-12 mt-1">
                                    <button type="submit" class="btn-primary btn-primary--lg w-100 justify-content-center">
                                        <i class="fas fa-paper-plane"></i>
                                        @lang('loan.button')
                                    </button>
                                    <p style="font-size:.72rem;color:#9ca3af;text-align:center;margin-top:.6rem;">
                                        <i class="fas fa-lock" style="margin-right:.3rem;"></i>
                                        @lang('loan.form_security')
                                    </p>
                                </div>
                            </div>
                        </form>
                    </div>

                </div>{{-- /step2 --}}

            </div>{{-- /col-lg-8 --}}

            {{-- ══════════════════════ SIDEBAR ══════════════════════ --}}
            <div class="col-lg-4 wow fadeInRight" data-wow-duration="900ms" data-wow-delay="150ms">
                <div style="position:sticky;top:110px;" class="service-sidebar">

                    <div class="contact-widget">
                        <div class="contact-widget__icon"><i class="fas fa-phone-alt"></i></div>
                        <h4>@lang('contact.phone_title')</h4>
                        <p>@lang('loan.sidebar_hours')</p>
                        <a href="tel:+34613853614" class="contact-widget__phone">+34 613 85 36 14</a>
                        <a href="{{ route('contact', ['locale' => $locale]) }}"
                           class="btn-outline w-100 justify-content-center mt-2">
                            <i class="fas fa-envelope"></i> @lang('menu.contact')
                        </a>
                    </div>

                    <div class="service-sidebar__widget mt-3">
                        <h3 class="service-sidebar__title">@lang('home.loan_reasons.sectitle')</h3>
                        @php $icons = ['fa-car','fa-layer-group','fa-home']; @endphp
                        @foreach ([1,2,3] as $r)
                        <div class="reason-item">
                            <div class="reason-icon"><i class="fas {{ $icons[$r-1] }}"></i></div>
                            <div>
                                <div class="reason-title">@lang('home.loan_reasons.reasons.title' . $r)</div>
                                <p class="reason-desc">@lang('home.loan_reasons.reasons.desc' . $r)</p>
                            </div>
                        </div>
                        @endforeach
                        <a href="{{ route('home', ['locale' => $locale]) }}#services"
                           class="btn-outline w-100 justify-content-center mt-2" style="font-size:.82rem;">
                            <i class="fas fa-list"></i> @lang('home.loan_reasons.btn_text')
                        </a>
                    </div>

                </div>
            </div>

        </div>
    </div>
</section>
@endsection
