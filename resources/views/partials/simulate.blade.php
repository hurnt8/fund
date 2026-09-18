@php $locale = app()->getLocale(); @endphp

<div class="simulate-card" x-data="{
    amount: 10000,
    duration: 36,
    rate: 2.5,
    get monthly() {
        const p = parseFloat(this.amount), n = parseInt(this.duration);
        const r = this.rate / 100 / 12;
        if (!p || !n || p <= 0 || n <= 0 || isNaN(p) || isNaN(n)) return null;
        return (p * r * Math.pow(1 + r, n)) / (Math.pow(1 + r, n) - 1);
    },
    get total() { return this.monthly ? this.monthly * parseInt(this.duration) : null; },
    fmt(v) {
        if (v === null || v === undefined || isNaN(v)) return '—';
        try {
            return new Intl.NumberFormat('{{ $locale }}', { maximumFractionDigits: 0 }).format(v) + ' €';
        } catch (e) { return Math.round(v) + ' €'; }
    },
}">
    <h3 class="simulate-card__title">{{ __('home.simulate.sectitle') }}</h3>

    <div class="form-group">
        <label>@lang('simulate.label_amount')</label>
        <div class="input-group-simple">
            <input type="number" class="form-control" x-model.number="amount" min="1000" max="500000" step="100">
            <span class="input-group-simple__sym">€</span>
        </div>
    </div>

    <div class="form-group mb-0">
        <label>@lang('simulate.label_duree')</label>
        <div class="input-group-simple">
            <input type="number" class="form-control" x-model.number="duration" min="1" max="360" step="1">
            <span class="input-group-simple__sym">@lang('simulate.table_month')</span>
        </div>
    </div>

    <div class="simulate-card__results">
        <div class="simulate-card__result simulate-card__result--highlight">
            <span>@lang('simulate.table_month')</span>
            <b x-text="fmt(monthly)">—</b>
        </div>
        <div class="simulate-card__result">
            <span>@lang('simulate.terms')</span>
            <b x-text="duration + ' ' + @js(__('simulate.table_month'))"></b>
        </div>
        <div class="simulate-card__result">
            <span>@lang('simulate.total')</span>
            <b x-text="fmt(total)">—</b>
        </div>
    </div>

    <a href="{{ route('loan', ['locale' => $locale]) }}" class="btn-primary simulate-card__btn">
        <i class="fas fa-file-signature"></i>
        @lang('menu.loan')
    </a>
</div>
