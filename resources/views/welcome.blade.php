@extends('layouts.app')
@section('title', __('menu.home'))

@section('content')
@php $locale = app()->getLocale(); @endphp

{{-- ============================================================
     HÉROS SCINDÉ — texte éditorial à gauche, photo à droite.
     Pas de carrousel : la page s'ouvre sur un plan fixe.
============================================================ --}}
<section class="hero-split">
    <div class="container-sm">
        <div class="hero-split__grid">

            <div class="hero-split__text wow fadeInUp" data-wow-duration="800ms">
                <div class="hero-eyebrow">{{ __('home.slide_1.title') }}</div>

                <h1 class="hero-split__title">
                    {{ __('home.slide_1.text1') }}
                    <em>{{ __('home.slide_1.text2') }}</em>
                </h1>

                <p class="hero-split__lede">{{ __('home.hero_subtitle') }}</p>

                <div class="hero-split__actions">
                    <a href="{{ route('loan', ['locale' => $locale]) }}" class="btn-primary btn-primary--lg">
                        <i class="fas fa-file-signature"></i>
                        @lang('menu.loan')
                    </a>
                    <a href="#simulate" class="btn-outline btn-outline--lg">
                        <i class="fas fa-calculator"></i>
                        @lang('menu.simulate')
                    </a>
                </div>

                <div class="hero-facts">
                    @foreach ([
                        ['4.9/5',  __('home.customer_satisfaction_rate')],
                        ['24h',    __('home.average_approval_time')],
                        ['8 500+', __('home.member')],
                        ['5+',     __('home.years_experience')],
                    ] as $fact)
                    <div class="hero-fact">
                        <span class="hero-fact__num">{{ $fact[0] }}</span>
                        <span class="hero-fact__label">{{ $fact[1] }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="hero-split__media wow fadeIn" data-wow-duration="1100ms">
                <img src="{{ asset('assets/images/aurenza/hero-siege.jpg') }}"
                     alt="{{ __('home.about.sectitle') }}">
                <div class="hero-split__stamp">
                    <b>95 000 €</b>
                    <span>{{ __('home.total_loan_amount_granted') }}</span>
                </div>
            </div>

        </div>
    </div>
</section>

{{-- ============================================================
     ACCÈS RAPIDE AUX SOLUTIONS
============================================================ --}}
@php
$serviceNav = [
    ['route' => 'services.personal', 'icon' => 'fas fa-user-tie',      'label' => 'menu.personal'],
    ['route' => 'services.home',     'icon' => 'fas fa-home',           'label' => 'menu.home_loan'],
    ['route' => 'services.auto',     'icon' => 'fas fa-car',            'label' => 'menu.auto'],
    ['route' => 'services.business', 'icon' => 'fas fa-briefcase',      'label' => 'menu.business'],
    ['route' => 'services.study',    'icon' => 'fas fa-graduation-cap', 'label' => 'menu.study'],
    ['route' => 'services.bike',     'icon' => 'fas fa-bicycle',        'label' => 'menu.bike'],
];
@endphp
<div class="service-nav-strip" id="services-strip">
    <div class="service-nav-strip__inner">
        @foreach ($serviceNav as $nav)
        <a href="{{ route($nav['route'], ['locale' => $locale]) }}" class="service-nav-strip__item">
            <div class="service-nav-strip__icon"><i class="{{ $nav['icon'] }}"></i></div>
            <span class="service-nav-strip__label">@lang($nav['label'])</span>
        </a>
        @endforeach
    </div>
</div>

{{-- ============================================================
     À PROPOS
============================================================ --}}
@push('styles')
<style>
.about-loan-grid { display:grid; grid-template-columns:1fr 1fr; gap:.4rem .75rem; margin-bottom:1.25rem; }
.about-loan-item {
    display:flex; align-items:center; gap:.55rem;
    font-size:.82rem; font-weight:600; color:var(--forest); padding:.45rem 0;
    border-bottom:1px solid var(--gray-100);
}
.about-loan-item i { color:var(--brass); width:16px; text-align:center; font-size:.8rem; }

.about-partner-bar {
    display:flex; flex-wrap:wrap; align-items:center; gap:.55rem;
    padding:.8rem 1rem; background:var(--ivory);
    border:1px solid var(--gray-200); margin-bottom:1.5rem;
}
.about-partner-bar__lbl { font-size:.6rem; font-weight:800; text-transform:uppercase; letter-spacing:.12em; color:var(--gray-400); margin-right:.2rem; white-space:nowrap; }
.about-partner-bar img  { height:20px; width:auto; opacity:.5; filter:grayscale(1); transition:opacity .25s,filter .25s; }
.about-partner-bar img:hover { opacity:1; filter:grayscale(0); }

.about-stack { display:grid; gap:1rem; }
.about-stack img { width:100%; display:block; object-fit:cover; }
.about-stack__tall { height:340px; }
.about-stack__wide { height:220px; }
</style>
@endpush

<section class="py-24 bg-white" id="about">
    <div class="container-sm">
        <div class="row gutter-y-60 align-items-center">

            <div class="col-lg-6 wow fadeInLeft" data-wow-duration="1000ms">
                <div class="about-stack">
                    <img src="{{ asset('assets/images/aurenza/bureaux-couloir.jpg') }}"
                         alt="{{ __('home.about.sectitle') }}" class="about-stack__tall" loading="lazy">
                    <img src="{{ asset('assets/images/aurenza/bureaux-reunion.jpg') }}"
                         alt="" class="about-stack__wide" loading="lazy">
                </div>
            </div>

            <div class="col-lg-6 wow fadeInRight" data-wow-duration="1000ms" data-wow-delay="150ms">

                <div class="rule-label">{{ __('home.about.sectagline') }}</div>
                <h2 class="section-title">{{ __('home.about.sectitle') }}</h2>

                <p style="color:var(--gray-500);font-size:.9375rem;line-height:1.8;margin-bottom:1rem;">
                    {{ __('home.about.text2') }}
                </p>

                <div style="margin-bottom:1.5rem;">
                    @foreach ([
                        ['fas fa-shield-alt', 'engage1'],
                        ['fas fa-bolt',       'engage2'],
                        ['fas fa-globe',      'engage3'],
                    ] as $eng)
                    <div class="about-point">
                        <div class="about-point__icon"><i class="{{ $eng[0] }}"></i></div>
                        <div>
                            <div class="about-point__title">{{ __('home.about.' . $eng[1] . '_title') }}</div>
                            <p class="about-point__desc">{{ __('home.about.' . $eng[1] . '_desc') }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div style="font-size:.65rem;font-weight:800;text-transform:uppercase;letter-spacing:.12em;color:var(--forest);margin-bottom:.6rem;">
                    @lang('home.discover_our_loan_services')
                </div>
                <div class="about-loan-grid">
                    <div class="about-loan-item"><i class="fas fa-user-tie"></i> @lang('home.personal_loan')</div>
                    <div class="about-loan-item"><i class="fas fa-home"></i> @lang('home.mortgage_loan')</div>
                    <div class="about-loan-item"><i class="fas fa-car"></i> @lang('home.auto_loan')</div>
                    <div class="about-loan-item"><i class="fas fa-graduation-cap"></i> @lang('home.student_loan')</div>
                    <div class="about-loan-item"><i class="fas fa-briefcase"></i> @lang('home.business_loan')</div>
                    <div class="about-loan-item"><i class="fas fa-credit-card"></i> @lang('home.microcredit')</div>
                </div>

                <div class="about-partner-bar">
                    <span class="about-partner-bar__lbl">@lang('home.partners_label') :</span>
                    <img src="{{ asset('images/partners/bnpparibas.svg') }}" alt="BNP Paribas">
                    <img src="{{ asset('images/partners/santander.svg') }}" alt="Santander">
                    <img src="{{ asset('images/partners/pko.svg') }}" alt="PKO Bank Polski">
                    <img src="{{ asset('images/partners/revolut.svg') }}" alt="Revolut">
                    <img src="{{ asset('images/partners/bbva.svg') }}" alt="BBVA">
                </div>

                <div class="d-flex flex-wrap gap-3">
                    <a href="{{ route('loan', ['locale' => $locale]) }}" class="btn-primary btn-primary--lg">
                        <i class="fas fa-file-signature"></i> @lang('menu.loan')
                    </a>
                    <a href="{{ route('about', ['locale' => $locale]) }}" class="btn-outline">
                        @lang('menu.about') <i class="fas fa-arrow-right"></i>
                    </a>
                </div>

            </div>

        </div>
    </div>
</section>

{{-- ============================================================
     SOLUTIONS — grille à filets, sans cartes flottantes
============================================================ --}}
<section class="py-24" style="background:var(--ivory);" id="services">
    <div class="container-sm">
        <div class="row align-items-end mb-10">
            <div class="col-lg-8">
                <div class="rule-label">{{ __('home.services.sectagline') }}</div>
                <h2 class="section-title mb-0">{{ __('home.services.sectitle') }}</h2>
            </div>
            <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
                <a href="{{ route('services', ['locale' => $locale]) }}" class="btn-outline">
                    @lang('menu.services') <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>

        @php
        $services = [
            ['route' => 'services.personal', 'label' => 'menu.personal',  'type' => 'personal_loan', 'icon' => 'fas fa-user-tie'],
            ['route' => 'services.study',    'label' => 'menu.study',     'type' => 'study_loan',    'icon' => 'fas fa-graduation-cap'],
            ['route' => 'services.home',     'label' => 'menu.home_loan', 'type' => 'home_loan',     'icon' => 'fas fa-home'],
            ['route' => 'services.business', 'label' => 'menu.business',  'type' => 'business_loan', 'icon' => 'fas fa-briefcase'],
            ['route' => 'services.auto',     'label' => 'menu.auto',      'type' => 'auto_loan',     'icon' => 'fas fa-car'],
            ['route' => 'services.bike',     'label' => 'menu.bike',      'type' => 'bike_loan',     'icon' => 'fas fa-bicycle'],
        ];
        @endphp

        <div class="offer-grid">
            @foreach ($services as $i => $svc)
            <a href="{{ route($svc['route'], ['locale' => $locale]) }}" class="offer-cell wow fadeInUp"
               data-wow-duration="700ms" data-wow-delay="{{ $i * 60 }}ms">
                <i class="{{ $svc['icon'] }} offer-cell__icon"></i>
                <h3 class="offer-cell__title">@lang($svc['label'])</h3>
                <p class="offer-cell__desc">{{ Str::limit(__('loan.' . $svc['type'] . '.description'), 110) }}</p>
                <span class="offer-cell__more">@lang('menu.read_more') <i class="fas fa-arrow-right"></i></span>
            </a>
            @endforeach
        </div>
    </div>
</section>

{{-- ============================================================
     DÉMARCHE — liste éditoriale numérotée
============================================================ --}}
<section class="py-24 bg-white">
    <div class="container-sm">
        <div class="row align-items-end mb-10">
            <div class="col-lg-7">
                <div class="rule-label">{{ __('home.works.sectagline') }}</div>
                <h2 class="section-title mb-0">{{ __('home.works.sectitle') }}</h2>
            </div>
        </div>

        <div class="steps-list">
            @foreach ([1,2,3,4] as $s)
            <div class="step-row wow fadeInUp" data-wow-duration="700ms" data-wow-delay="{{ ($s-1)*70 }}ms">
                <div class="step-row__num">0{{ $s }}</div>
                <h3 class="step-row__title">{{ __('home.works.step' . $s . '.title') }}</h3>
                <p class="step-row__desc">{{ __('home.works.step' . $s . '.desc') }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ============================================================
     SIMULATION
============================================================ --}}
<section class="calc-section py-24" id="simulate">
    <div class="container-sm">
        <div class="row gutter-y-50 align-items-center">
            <div class="col-lg-5 wow fadeInLeft" data-wow-duration="900ms">
                <div class="rule-label" style="color:var(--brass-light);">{{ __('home.works.sectagline') }}</div>
                <h2 class="section-title section-title--white">{{ __('home.loan_reasons.sectitle') }}</h2>
                <p class="section-sub section-sub--white mb-8">{{ __('home.about.text2') }}</p>

                @foreach ([1,2,3] as $r)
                <div class="d-flex align-items-start gap-3 mb-4">
                    <div style="width:30px;height:30px;border:1px solid rgba(198,161,91,.5);display:flex;align-items:center;justify-content:center;color:var(--brass-light);flex-shrink:0;font-size:.7rem;">
                        <i class="fas fa-check"></i>
                    </div>
                    <div>
                        <h4 style="font-family:var(--font-display);font-size:1.0625rem;font-weight:600;color:#fff;margin:0 0 .25rem;">
                            {{ __('home.loan_reasons.reasons.title' . $r) }}
                        </h4>
                        <p style="font-size:.875rem;color:rgba(255,255,255,.55);margin:0;line-height:1.65;">
                            {{ __('home.loan_reasons.reasons.desc' . $r) }}
                        </p>
                    </div>
                </div>
                @endforeach

                <div class="mt-6">
                    <a href="{{ route('loan', ['locale' => $locale]) }}" class="btn-outline-white">
                        <i class="fas fa-file-signature"></i> @lang('menu.loan')
                    </a>
                </div>
            </div>

            <div class="col-lg-6 offset-lg-1 wow fadeInRight" data-wow-duration="900ms" data-wow-delay="150ms">
                @include('partials.simulate')
            </div>
        </div>
    </div>
</section>

{{-- ============================================================
     CHIFFRES
============================================================ --}}
<section class="figure-band">
    <div class="container-sm">
        <div class="figure-band__grid">
            @php
            $stats = [
                ['2500+', __('home.customer_satisfaction_rate')],
                ['€95k',  __('home.total_loan_amount_granted')],
                ['24h',   __('home.average_approval_time')],
                ['5+',    __('home.years_experience')],
            ];
            @endphp
            @foreach ($stats as $i => $stat)
            <div class="figure-cell wow fadeInUp" data-wow-duration="700ms" data-wow-delay="{{ $i*70 }}ms">
                <span class="figure-cell__num">{{ $stat[0] }}</span>
                <span class="figure-cell__label">{{ $stat[1] }}</span>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ============================================================
     PARTENAIRES
============================================================ --}}
@push('styles')
<style>
.partners-strip { display:flex; flex-wrap:wrap; align-items:center; justify-content:center; gap:0; border-top:1px solid var(--gray-200); }
.partner-logo {
    display:flex; align-items:center; justify-content:center;
    padding:1.25rem 2rem; min-width:150px; height:82px; flex:1;
    border-right:1px solid var(--gray-200); border-bottom:1px solid var(--gray-200);
    filter:grayscale(1); opacity:.45; transition:filter .3s ease, opacity .3s ease;
}
.partner-logo:last-child { border-right:none; }
.partner-logo:hover { filter:grayscale(0); opacity:1; }
@media (max-width:576px) { .partner-logo { min-width:50%; padding:.9rem 1rem; height:64px; } }
</style>
@endpush

<section class="py-10 bg-white">
    <div class="container-sm">
        <p class="text-center" style="font-size:.66rem;font-weight:800;text-transform:uppercase;letter-spacing:.16em;color:var(--gray-400);margin-bottom:1.5rem;">
            @lang('home.partners_label')
        </p>
        <div class="partners-strip">
            <div class="partner-logo"><img src="{{ asset('images/partners/bnpparibas.svg') }}" alt="BNP Paribas" style="height:32px;width:auto;"></div>
            <div class="partner-logo"><img src="{{ asset('images/partners/santander.svg') }}" alt="Santander" style="height:32px;width:auto;"></div>
            <div class="partner-logo"><img src="{{ asset('images/partners/pko.svg') }}" alt="PKO Bank Polski" style="height:32px;width:auto;"></div>
            <div class="partner-logo"><img src="{{ asset('images/partners/revolut.svg') }}" alt="Revolut" style="height:32px;width:auto;"></div>
            <div class="partner-logo"><img src="{{ asset('images/partners/bbva.svg') }}" alt="BBVA" style="height:32px;width:auto;"></div>
        </div>
    </div>
</section>

{{-- ============================================================
     TÉMOIGNAGES — grille statique, sans défilement automatique
============================================================ --}}
<section class="py-24" style="background:var(--ivory);" id="testimonials">
    <div class="container-sm">
        <div class="mb-10">
            <div class="rule-label">{{ __('home.testimonials_title') }}</div>
            <h2 class="section-title mb-0">{{ __('home.testimonials_title') }}</h2>
        </div>

        <div class="quote-grid">
            @foreach (range(1, 6) as $i)
            @php $t = __('home.testimonial_' . $i); @endphp
            <div class="quote-cell wow fadeInUp" data-wow-duration="700ms" data-wow-delay="{{ ($i-1)*60 }}ms">
                <span class="quote-cell__mark">&ldquo;</span>
                <p class="quote-cell__text">{{ $t['quote'] }}</p>
                <p class="quote-cell__who">{{ $t['name'] }}</p>
                @if (!empty($t['location']))
                <p class="quote-cell__when">{{ $t['location'] }}</p>
                @endif
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ============================================================
     APPEL À L'ACTION
============================================================ --}}
<section class="cta-banner">
    <div class="container-sm">
        <div class="row align-items-center gutter-y-30">
            <div class="col-lg-7 wow fadeInLeft" data-wow-duration="900ms">
                <div class="rule-label" style="color:var(--brass-light);">@lang('menu.newsletter_title')</div>
                <h2 class="section-title section-title--white mb-0">@lang('home.loan_reasons.sectitle')</h2>
            </div>
            <div class="col-lg-5 text-lg-end wow fadeInRight" data-wow-duration="900ms" data-wow-delay="150ms">
                <div class="d-flex flex-wrap justify-content-lg-end gap-3">
                    <a href="{{ route('loan',    ['locale' => $locale]) }}" class="btn-primary btn-primary--lg">
                        <i class="fas fa-file-signature"></i> @lang('menu.loan')
                    </a>
                    <a href="{{ route('contact', ['locale' => $locale]) }}" class="btn-outline-white">
                        <i class="fas fa-envelope"></i> @lang('menu.contact')
                    </a>
                </div>
            </div>
        </div>

        <hr style="border-color:rgba(251,249,244,.1);margin:3rem 0;">

        <div class="row align-items-center gutter-y-20">
            <div class="col-lg-4 wow fadeInLeft" data-wow-duration="900ms">
                <p style="color:rgba(251,249,244,.6);font-size:.9375rem;margin:0;">@lang('menu.newsletter_title')</p>
            </div>
            <div class="col-lg-8 text-lg-end wow fadeInRight" data-wow-duration="900ms" data-wow-delay="100ms">
                <form action="{{ route('subscribe.send') }}" method="POST" class="newsletter-form d-inline-flex">
                    @csrf
                    <input type="email" name="email" placeholder="@lang('menu.email_placeholder')" required>
                    <button type="submit" class="btn-primary">@lang('menu.subscribe')</button>
                </form>
            </div>
        </div>
    </div>
</section>

@endsection
