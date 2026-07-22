@extends('layouts.app')
@section('title', __('menu.loan'))

@push('styles')
<style>
/* ── Devise ── */
.currency-btn {
    display:flex; align-items:center; gap:.5rem;
    padding:.5rem .9rem; border-radius:10px; border:2px solid #e5e7eb;
    background:#fff; cursor:pointer; transition:all .18s; user-select:none; white-space:nowrap;
}
.currency-btn:hover { border-color:var(--gold); }
.currency-btn.active { background:var(--navy); border-color:var(--navy); color:#fff; box-shadow:0 3px 12px rgba(10,37,76,.2); }
.currency-btn__flag { font-size:1.1rem; line-height:1; }
.currency-btn__name { font-size:.79rem; font-weight:700; line-height:1.1; }
.currency-btn__sym  { font-size:.7rem; opacity:.65; }

/* ── Chips ── */
.chip-group { display:flex; flex-wrap:wrap; gap:.4rem; }
.chip {
    padding:.38rem .8rem; border-radius:999px; border:2px solid #d1d5db;
    background:#fff; color:#374151; font-size:.82rem; font-weight:600;
    cursor:pointer; transition:all .18s; white-space:nowrap;
}
.chip:hover { border-color:var(--gold); color:var(--gold); }
.chip.active { background:var(--navy); color:#fff; border-color:var(--navy); }

/* ── Champ libre montant/durée ── */
.free-input-row {
    display:flex; align-items:center; gap:.6rem;
    margin-top:.65rem; padding:.55rem .85rem;
    background:#f5f7fb; border:1.5px dashed #c8d3e8; border-radius:10px;
}
.free-input-row label { font-size:.76rem; font-weight:600; color:#6b7280; white-space:nowrap; margin:0; }
.free-input-row input {
    flex:1; border:none; background:transparent; font-size:.92rem; font-weight:700;
    color:var(--navy); outline:none; min-width:0;
}
.free-input-row input::placeholder { font-weight:400; color:#b0bec5; }
.free-input-row .sym { font-size:.88rem; font-weight:800; color:var(--navy); opacity:.7; }

/* ── Résumé devis ── */
.quote-result {
    background:linear-gradient(135deg,var(--navy) 0%,#183560 100%);
    border-radius:14px; padding:1.1rem 1.3rem; color:#fff;
}
.quote-result__row { display:flex; flex-wrap:wrap; gap:.8rem; justify-content:space-between; margin-bottom:.75rem; }
.quote-result__item { text-align:center; flex:1; min-width:80px; }
.quote-result__label { font-size:.62rem; text-transform:uppercase; letter-spacing:.08em; color:rgba(255,255,255,.5); display:block; margin-bottom:.2rem; }
.quote-result__value { font-size:1rem; font-weight:800; color:#fff; }
.quote-result__value.gold { color:var(--gold); font-size:1.25rem; }
.quote-result__sep { width:1px; height:32px; background:rgba(255,255,255,.15); }

/* ── Séparateurs de section ── */
.form-section { margin-bottom:1.5rem; }
.form-section-title {
    font-size:.7rem; font-weight:800; text-transform:uppercase; letter-spacing:.07em;
    color:var(--navy); margin-bottom:.55rem; display:flex; align-items:center; gap:.5rem;
}
.form-section-title i { color:var(--gold); }
.form-section-title::after { content:''; flex:1; height:1px; background:#eaecf0; }

/* ── Sidebar raisons ── */
.reason-item { display:flex; gap:.75rem; padding:.8rem 0; }
.reason-item + .reason-item { border-top:1px solid #f0f0f0; }
.reason-icon { width:38px; height:38px; border-radius:9px; background:rgba(212,175,55,.1); color:var(--gold); display:flex; align-items:center; justify-content:center; font-size:.9rem; flex-shrink:0; }
.reason-title { font-size:.84rem; font-weight:700; color:var(--navy); margin-bottom:.15rem; }
.reason-desc  { font-size:.75rem; color:#6b7280; line-height:1.45; margin:0; }

[x-cloak] { display:none !important; }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('loanForm', () => ({
        selCurrency: 'EUR',
        selAmount:   null,
        customAmt:   '',
        selDuration: null,
        customDur:   '',
        rate: 5,

        monthsLabel: "{{ __('message.months') }}",
        monthAbbr:   "{{ __('message.month_abbr') }}",
        locale:      "{{ str_replace('_','-',app()->getLocale()) }}",

        currencies: [
            { code:'EUR', symbol:'€',  flag:'🇪🇺', name:'Euro'             },
            { code:'PLN', symbol:'zł', flag:'🇵🇱', name:'Złoty (PLN)'      },
            { code:'USD', symbol:'$',  flag:'🇺🇸', name:'Dollar (USD)'     },
            { code:'MXN', symbol:'$',  flag:'🇲🇽', name:'Mexican Peso'    },
            { code:'BRL', symbol:'R$', flag:'🇧🇷', name:'Brazilian Real'   },
            { code:'CRC', symbol:'₡',  flag:'🇨🇷', name:'Colón (Costa Rica)'},
        ],

        amountsByCurrency: {
            EUR:[1000,3000,5000,10000,20000,50000,75000,95000],
            PLN:[5000,10000,20000,50000,100000,200000,350000,500000],
            USD:[1000,3000,5000,10000,25000,50000,75000,100000],
            MXN:[20000,50000,100000,250000,500000,1000000,1500000,2000000],
            BRL:[5000,10000,25000,50000,100000,250000,400000,500000],
            CRC:[600000,1500000,3000000,6000000,15000000,30000000,45000000,55000000],
        },

        get amounts()  { return this.amountsByCurrency[this.selCurrency] || this.amountsByCurrency['EUR']; },
        get currency() { return this.currencies.find(c => c.code === this.selCurrency) || this.currencies[0]; },

        get amount() {
            const c = parseFloat(this.customAmt);
            return (!isNaN(c) && c > 0) ? c : this.selAmount;
        },
        get duration() {
            const c = parseInt(this.customDur);
            return (!isNaN(c) && c > 0) ? c : this.selDuration;
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

        setCurrency(code) {
            if (this.selCurrency === code) return;
            this.selCurrency = code;
            this.selAmount = null; this.customAmt = '';
        },
        pickAmount(v)   { this.selAmount = v; this.customAmt = ''; },
        pickDuration(v) { this.selDuration = v; this.customDur = ''; },
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

            {{-- ══════════ FORMULAIRE PRINCIPAL ══════════ --}}
            <div class="col-lg-8" x-data="loanForm">
                <div class="form-card wow fadeInLeft" data-wow-duration="700ms"
                     style="border-top:4px solid var(--gold);">

                    {{-- En-tête --}}
                    <div class="d-flex align-items-start justify-content-between flex-wrap gap-2 mb-4">
                        <div>
                            <div class="section-label mb-1">@lang('loan.quote_step_label')</div>
                            <h3 style="font-family:'Playfair Display',serif;color:var(--navy);font-size:1.25rem;font-weight:700;margin:0 0 .15rem;">
                                @lang('loan.quote_step_title')
                            </h3>
                            <p style="font-size:.78rem;color:#6b7280;margin:0;">@lang('loan.quote_step_desc')</p>
                        </div>
                        <div style="display:inline-flex;align-items:center;gap:.4rem;background:var(--navy);color:var(--gold);padding:.35rem .9rem;border-radius:999px;font-weight:800;font-size:.82rem;white-space:nowrap;flex-shrink:0;">
                            <i class="fas fa-lock" style="font-size:.68rem;"></i>
                            @lang('loan.label_rate') : 5 %
                        </div>
                    </div>

                    @if (session('success'))
                    {{-- ══ PANNEAU DE CONFIRMATION ══ --}}
                    <div style="text-align:center;padding:1.5rem .5rem 2rem;">
                        <div style="width:70px;height:70px;border-radius:50%;background:linear-gradient(135deg,#d1fae5,#a7f3d0);margin:0 auto 1.2rem;display:flex;align-items:center;justify-content:center;box-shadow:0 6px 20px rgba(5,150,105,.2);">
                            <i class="fas fa-check" style="font-size:1.8rem;color:#059669;"></i>
                        </div>
                        <h3 style="color:var(--navy);font-size:1.2rem;font-weight:800;margin-bottom:.6rem;">
                            {{ session('success') }}
                        </h3>
                        <p style="color:#6b7280;font-size:.88rem;max-width:420px;margin:0 auto 1.75rem;line-height:1.6;">
                            @lang('message.loan_complete_intro')
                        </p>
                        <div class="d-flex flex-wrap justify-content-center gap-3">
                            <a href="{{ route('home', ['locale' => $locale]) }}" class="btn-outline">
                                <i class="fas fa-home"></i> @lang('menu.home')
                            </a>
                            <a href="{{ route('loan', ['locale' => $locale]) }}" class="btn-primary">
                                <i class="fas fa-plus"></i> @lang('menu.loan')
                            </a>
                        </div>
                    </div>
                    @else

                    @if ($errors->any())
                        <div class="alert alert-danger mb-4">
                            @foreach ($errors->all() as $e)<div>{{ $e }}</div>@endforeach
                        </div>
                    @endif

                    {{-- ── ① Devise ── --}}
                    <div class="form-section">
                        <div class="form-section-title">
                            <i class="fas fa-globe"></i> @lang('loan.label_currency')
                        </div>
                        <div class="d-flex flex-wrap gap-2">
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

                    {{-- ── ② Montant ── --}}
                    <div class="form-section">
                        <div class="form-section-title">
                            <i class="fas fa-coins"></i> @lang('loan.label_amount')
                        </div>
                        <p style="font-size:.8rem;color:#6b7280;margin-bottom:.6rem;">
                            @lang('loan.preset_hint')
                        </p>
                        <div class="chip-group">
                            <template x-for="v in amounts" :key="v">
                                <button type="button" class="chip"
                                        :class="selAmount === v && customAmt === '' ? 'active' : ''"
                                        @click="pickAmount(v)"
                                        x-text="fmtAmt(v)"></button>
                            </template>
                        </div>
                        {{-- Champ libre toujours visible --}}
                        <div class="free-input-row mt-2">
                            <label>
                                <i class="fas fa-keyboard" style="margin-right:.3rem;color:var(--gold);"></i>
                                @lang('loan.label_other') :
                            </label>
                            <input type="number" x-model="customAmt" @input="selAmount = null"
                                   min="1000" step="100"
                                   placeholder="{{ __('loan.placeholder_amount') }}">
                            <span class="sym" x-text="currency.symbol"></span>
                        </div>
                    </div>

                    {{-- ── ③ Durée ── --}}
                    <div class="form-section">
                        <div class="form-section-title">
                            <i class="fas fa-calendar-alt"></i> @lang('loan.label_darly')
                        </div>
                        <p style="font-size:.8rem;color:#6b7280;margin-bottom:.6rem;">
                            @lang('loan.or_custom')
                        </p>
                        <div class="chip-group">
                            <template x-for="d in [12,24,36,48,60,84,120]" :key="d">
                                <button type="button" class="chip"
                                        :class="selDuration === d && customDur === '' ? 'active' : ''"
                                        @click="pickDuration(d)"
                                        x-text="d + ' ' + monthsLabel"></button>
                            </template>
                        </div>
                        {{-- Champ libre toujours visible --}}
                        <div class="free-input-row mt-2">
                            <label>
                                <i class="fas fa-keyboard" style="margin-right:.3rem;color:var(--gold);"></i>
                                @lang('loan.label_other') :
                            </label>
                            <input type="number" x-model="customDur" @input="selDuration = null"
                                   min="1" max="360" placeholder="e.g. 72">
                            <span class="sym" x-text="monthsLabel"></span>
                        </div>
                    </div>

                    {{-- ── Résumé devis (apparaît dès que montant + durée sont renseignés) ── --}}
                    <div x-show="canProceed" x-cloak x-transition
                         class="form-section">
                        <div class="form-section-title">
                            <i class="fas fa-calculator"></i> @lang('loan.quote_summary_title')
                        </div>
                        <div class="quote-result">
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
                            <p style="font-size:.68rem;color:rgba(255,255,255,.4);margin:0;">
                                <i class="fas fa-info-circle" style="margin-right:.25rem;"></i>@lang('loan.quote_hint')
                            </p>
                        </div>
                    </div>

                    <hr style="border-color:#eaecf0;margin:0 0 1.5rem;">

                    {{-- ── Formulaire coordonnées ── --}}
                    <div class="form-section-title" style="margin-bottom:1rem;">
                        <i class="fas fa-user"></i> @lang('loan.form_title')
                    </div>
                    <p style="font-size:.79rem;color:#6b7280;margin-bottom:1.2rem;">@lang('loan.form_hint')</p>

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
                                        <option value="Personal loan"  {{ old('subject')=='Personal loan'  ?'selected':'' }}>@lang('menu.personal')</option>
                                        <option value="Home loan" {{ old('subject')=='Home loan' ?'selected':'' }}>@lang('menu.home_loan')</option>
                                        <option value="Business loan" {{ old('subject')=='Business loan' ?'selected':'' }}>@lang('menu.business')</option>
                                        <option value="Study loan"   {{ old('subject')=='Study loan'   ?'selected':'' }}>@lang('menu.study')</option>
                                        <option value="Auto loan"       {{ old('subject')=='Auto loan'       ?'selected':'' }}>@lang('menu.auto')</option>
                                        <option value="Bike loan"       {{ old('subject')=='Bike loan'       ?'selected':'' }}>@lang('menu.bike')</option>
                                    </select>
                                    @error('subject')<span class="form-error">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label>@lang('loan.label_objet')
                                        <span style="font-size:.72rem;color:#9ca3af;font-weight:400;">({{ __('message.optional') }})</span>
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
                                <p style="font-size:.71rem;color:#9ca3af;text-align:center;margin-top:.55rem;">
                                    <i class="fas fa-lock" style="margin-right:.3rem;"></i>
                                    @lang('loan.form_security')
                                </p>
                            </div>
                        </div>
                    </form>

                    @endif {{-- /session('success') --}}

                </div>
            </div>{{-- /col-lg-8 --}}

            {{-- ══════════ SIDEBAR ══════════ --}}
            <div class="col-lg-4 wow fadeInRight" data-wow-duration="900ms" data-wow-delay="150ms">
                <div style="position:sticky;top:110px;" class="service-sidebar">

                    <div class="contact-widget">
                        <div class="contact-widget__icon"><i class="fas fa-phone-alt"></i></div>
                        <h4>@lang('contact.phone_title')</h4>
                        <p>@lang('loan.sidebar_hours')</p>
                        <a href="tel:+34613853614" class="contact-widget__phone">+31 6 57341120</a>
                        <a href="{{ route('contact', ['locale' => $locale]) }}"
                           class="btn-outline w-100 justify-content-center mt-2">
                            <i class="fas fa-envelope"></i> @lang('menu.contact')
                        </a>
                    </div>

                    <div class="service-sidebar__widget mt-3">
                        <h3 class="service-sidebar__title">@lang('home.loan_reasons.sectitle')</h3>
                        @php
                            $reasonIcons = [
                                1 => 'fa-car',
                                2 => 'fa-layer-group',
                                3 => 'fa-home',
                                4 => 'fa-graduation-cap',
                                5 => 'fa-plane',
                                6 => 'fa-heart',
                                7 => 'fa-stethoscope',
                                8 => 'fa-briefcase',
                            ];
                        @endphp
                        @foreach ($reasonIcons as $r => $icon)
                        <div class="reason-item">
                            <div class="reason-icon"><i class="fas {{ $icon }}"></i></div>
                            <div>
                                <div class="reason-title">@lang('home.loan_reasons.reasons.title' . $r)</div>
                                <p class="reason-desc">@lang('home.loan_reasons.reasons.desc' . $r)</p>
                            </div>
                        </div>
                        @endforeach
                    </div>

                </div>
            </div>

        </div>
    </div>
</section>

{{-- Bande partenaires (signal de confiance) --}}
<div style="background:#f7f8fa;border-top:1px solid #eaecf0;padding:1.5rem 0 1.75rem;">
    <div class="container">
        <p style="text-align:center;font-size:.65rem;font-weight:800;text-transform:uppercase;letter-spacing:.14em;color:#b0b8c5;margin:0 0 1rem;">
            @lang('home.partners_label')
        </p>
        <div style="display:flex;flex-wrap:wrap;align-items:center;justify-content:center;gap:.75rem;">
            @foreach([
                ['bnpparibas','BNP Paribas'],
                ['santander','Santander'],
                ['pko','PKO Bank Polski'],
                ['revolut','Revolut'],
                ['bbva','BBVA'],
            ] as $p)
            <div style="display:flex;align-items:center;justify-content:center;padding:.55rem 1.25rem;height:52px;background:#fff;border:1.5px solid #e5e7eb;border-radius:10px;filter:grayscale(1);opacity:.45;transition:filter .25s,opacity .25s;">
                <img src="{{ asset('images/partners/' . $p[0] . '.svg') }}" alt="{{ $p[1] }}" style="height:30px;width:auto;">
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
