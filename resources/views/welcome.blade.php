@extends('layouts.app')
@section('title', __('menu.home'))
@section('body_class', 'is-home')

@section('content')
@php $locale = app()->getLocale(); @endphp

{{-- ============================================================
     HERO + CAROUSEL
============================================================ --}}
<section class="hero-section" x-data="{
    slides: [
        '{{ asset('assets/images/backgrounds/main-slider-bg-3-1.jpg') }}',
        '{{ asset('assets/images/backgrounds/main-slider-bg-3-2.jpg') }}',
        '{{ asset('assets/images/backgrounds/main-slider-bg-3-3.jpg') }}'
    ],
    current: 0,
    init() {
        setInterval(() => { this.current = (this.current + 1) % this.slides.length }, 5000)
    }
}" x-init="init()">
    <!-- Slide backgrounds (cross-fade, statiques pour éviter le flash blanc Alpine defer) -->
    <div class="hero-slide-bg"
         style="background-image:url('{{ asset('assets/images/backgrounds/main-slider-bg-1-1.jpg') }}'); opacity:1;"
         :style="{ opacity: current === 0 ? 1 : 0 }"></div>
    <div class="hero-slide-bg"
         style="background-image:url('{{ asset('assets/images/backgrounds/main-slider-bg-3-2.jpg') }}'); opacity:0;"
         :style="{ opacity: current === 1 ? 1 : 0 }"></div>
    <div class="hero-slide-bg"
         style="background-image:url('{{ asset('assets/images/backgrounds/main-slider-bg-3-3.jpg') }}'); opacity:0;"
         :style="{ opacity: current === 2 ? 1 : 0 }"></div>

    <!-- Dark overlay -->
    <div class="hero-overlay"></div>

    <!-- Prev / Next arrows -->
    <button @click="current = (current - 1 + 3) % 3"
            class="hero-arrow hero-arrow--prev" aria-label="Previous">
        <i class="fas fa-chevron-left"></i>
    </button>
    <button @click="current = (current + 1) % 3"
            class="hero-arrow hero-arrow--next" aria-label="Next">
        <i class="fas fa-chevron-right"></i>
    </button>

    <!-- Dot indicators (statiques) -->
    <div class="hero-dots">
        <button @click="current = 0" :class="current === 0 ? 'hero-dot active' : 'hero-dot'" aria-label="Slide 1"></button>
        <button @click="current = 1" :class="current === 1 ? 'hero-dot active' : 'hero-dot'" aria-label="Slide 2"></button>
        <button @click="current = 2" :class="current === 2 ? 'hero-dot active' : 'hero-dot'" aria-label="Slide 3"></button>
    </div>

    <div class="container-xl px-6 hero-inner w-full" style="position:relative;z-index:2;">
        <div class="row align-items-center gutter-y-50">

            {{-- ===== COLONNE GAUCHE : ACCROCHE ===== --}}
            <div class="col-lg-6 wow fadeInLeft" data-wow-duration="900ms">

                {{-- Badge preuve sociale --}}
                <div class="hero-badge mb-3">
                    <span class="hero-badge__dot"></span>
                    {{ __('home.slide_1.title') }}
                </div>

                {{-- Titre principal --}}
                <h1 class="font-serif text-white mb-3"
                    style="font-size:clamp(2rem,4.5vw,3.375rem);font-weight:800;line-height:1.12;">
                    {{ __('home.slide_1.text1') }}
                    <span style="color:var(--gold);display:block;margin-top:.15em;">{{ __('home.slide_1.text2') }}</span>
                </h1>

                {{-- Accroche sous-titre --}}
                <p class="mb-4" style="color:rgba(255,255,255,.75);font-size:1rem;line-height:1.8;max-width:520px;">
                    {{ __('home.hero_subtitle') }}
                </p>

                {{-- Pilules bénéfices --}}
                <div class="hero-pills d-flex flex-wrap gap-2 mb-4">
                    @foreach([
                        ['fas fa-bolt',       __('home.infos.item1')],
                        ['fas fa-coins',      __('home.infos.item3')],
                        ['fas fa-sync-alt',   __('home.infos.item4')],
                    ] as $pill)
                    <span style="display:inline-flex;align-items:center;gap:.35rem;background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.14);border-radius:999px;padding:.3rem .85rem;color:rgba(255,255,255,.85);font-size:.78rem;font-weight:500;">
                        <i class="{{ $pill[0] }}" style="color:var(--gold);font-size:.65rem;"></i>
                        {{ $pill[1] }}
                    </span>
                    @endforeach
                </div>

                {{-- Boutons CTA --}}
                <div class="d-flex flex-wrap gap-3">
                    <a href="{{ route('loan', ['locale' => $locale]) }}" class="btn-primary btn-primary--lg">
                        <i class="fas fa-file-signature"></i>
                        @lang('menu.loan')
                    </a>
                    <a href="#simulate" class="btn-outline-white">
                        <i class="fas fa-calculator"></i>
                        @lang('menu.simulate')
                    </a>
                </div>

                {{-- Barre de confiance --}}
                <div class="hero-trust-bar d-flex flex-wrap align-items-center gap-3"
                     style="margin-top:1.25rem;padding-top:1.25rem;border-top:1px solid rgba(255,255,255,.1);">
                    @foreach ([
                        ['fas fa-star',        __('home.customer_satisfaction_rate'), '4.9/5'],
                        ['fas fa-bolt',        __('home.average_approval_time'),      '24h'],
                        ['fas fa-users',       __('home.member'),                     '8 500+'],
                    ] as $trust)
                    <div class="d-flex align-items-center gap-2">
                        <div style="width:32px;height:32px;background:rgba(200,169,81,.18);border-radius:8px;display:flex;align-items:center;justify-content:center;color:var(--gold);font-size:.7rem;flex-shrink:0;">
                            <i class="{{ $trust[0] }}"></i>
                        </div>
                        <div>
                            <div style="font-size:.9rem;font-weight:800;color:#fff;line-height:1;">{{ $trust[2] }}</div>
                            <div style="font-size:.65rem;color:rgba(255,255,255,.45);letter-spacing:.05em;text-transform:uppercase;margin-top:1px;">{{ $trust[1] }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- ===== COLONNE DROITE : CHIFFRES CLÉS (desktop uniquement) ===== --}}
            <div class="col-lg-5 offset-lg-1 d-none d-lg-block wow fadeInRight" data-wow-duration="900ms" data-wow-delay="200ms">
                <div class="hero-trust">
                    {{-- Carte 1 : clients --}}
                    <div class="hero-trust__card">
                        <div class="hero-trust__icon"><i class="fas fa-users"></i></div>
                        <div>
                            <div class="hero-trust__label">{{ __('home.customer_satisfaction_rate') }}</div>
                            <div class="hero-trust__value">8 500+</div>
                        </div>
                    </div>
                    {{-- Carte 2 : montant max --}}
                    <div class="hero-trust__card">
                        <div class="hero-trust__icon"><i class="fas fa-euro-sign"></i></div>
                        <div>
                            <div class="hero-trust__label">{{ __('home.total_loan_amount_granted') }}</div>
                            <div class="hero-trust__value">95 000 €</div>
                        </div>
                    </div>
                    {{-- Carte 3 : expérience --}}
                    <div class="hero-trust__card">
                        <div class="hero-trust__icon"><i class="fas fa-history"></i></div>
                        <div>
                            <div class="hero-trust__label">{{ __('home.about.exptitle') }}</div>
                            <div class="hero-trust__value">5</div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Scroll indicator (left-aligned to avoid dots overlap) -->
    <div style="position:absolute;bottom:2rem;left:2rem;z-index:3;text-align:center;">
        <a href="#services-strip" style="display:flex;flex-direction:column;align-items:center;gap:.5rem;color:rgba(255,255,255,.35);font-size:.65rem;letter-spacing:.1em;text-transform:uppercase;">
            <span>Scroll</span>
            <i class="fas fa-chevron-down" style="animation:bounce 1.8s ease infinite;"></i>
        </a>
    </div>
</section>

<style>@keyframes bounce{0%,100%{transform:translateY(0)}50%{transform:translateY(6px)}}</style>

{{-- ============================================================
     SERVICE NAV STRIP
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
     ABOUT
============================================================ --}}
@push('styles')
<style>
/* ── About section ── */
.about-engage-card {
    display:flex; gap:1rem; padding:1rem 1.25rem;
    background:var(--cream); border-radius:12px;
    border-left:3px solid var(--gold); margin-bottom:.75rem;
}
.about-engage-icon {
    width:42px; height:42px; flex-shrink:0; border-radius:10px;
    background:var(--gold-pale); display:flex; align-items:center;
    justify-content:center; color:var(--gold-dark); font-size:1rem;
}
.about-engage-title { font-size:.875rem; font-weight:800; color:var(--navy); margin-bottom:.2rem; }
.about-engage-desc  { font-size:.78rem; color:#6b7280; margin:0; line-height:1.55; }

.about-loan-grid {
    display:grid; grid-template-columns:1fr 1fr; gap:.4rem .75rem; margin-bottom:1.25rem;
}
.about-loan-item {
    display:flex; align-items:center; gap:.55rem;
    font-size:.82rem; font-weight:600; color:var(--navy); padding:.4rem 0;
    border-bottom:1px solid #f3f4f6;
}
.about-loan-item i { color:var(--gold); width:16px; text-align:center; font-size:.8rem; }

.about-partner-bar {
    display:flex; flex-wrap:wrap; align-items:center; gap:.55rem;
    padding:.75rem 1rem; background:#f7f8fa; border-radius:10px;
    border:1px solid #eaecf0; margin-bottom:1.5rem;
}
.about-partner-bar__lbl { font-size:.6rem; font-weight:800; text-transform:uppercase; letter-spacing:.12em; color:#9ca3af; margin-right:.2rem; white-space:nowrap; }
.about-partner-bar img  { height:20px; width:auto; opacity:.5; filter:grayscale(1); transition:opacity .25s,filter .25s; }
.about-partner-bar img:hover { opacity:1; filter:grayscale(0); }
</style>
@endpush

<section class="py-24 bg-white" id="about">
    <div class="container">
        <div class="row gutter-y-60 align-items-center">

            {{-- ── Image ── --}}
            <div class="col-lg-6 wow fadeInLeft" data-wow-duration="1000ms">
                <div class="about-image-wrap">
                    <img src="{{ asset('assets/images/about/about-3-1.jpg') }}"
                         alt="Credixa — conseiller financier" class="about-image-main">
                    <img src="{{ asset('assets/images/about/about-3-3.jpg') }}"
                         alt="Conseiller avec clients" class="about-image-secondary"
                         style="width:38%;right:1rem;bottom:1rem;">
                    <div class="about-badge">
                        <span class="about-badge__number">5</span>
                        <span class="about-badge__label">{{ __('home.about.exptitle') }}</span>
                    </div>
                </div>
            </div>

            {{-- ── Contenu ── --}}
            <div class="col-lg-6 wow fadeInRight" data-wow-duration="1000ms" data-wow-delay="150ms">

                <div class="section-label">{{ __('home.about.sectagline') }}</div>
                <h2 class="section-title">{{ __('home.about.sectitle') }}</h2>

                <p style="color:var(--gray-500);font-size:.9375rem;line-height:1.8;margin-bottom:1.5rem;">
                    {{ __('home.about.text2') }}
                </p>

                {{-- 3 engagements clés --}}
                <div class="about-engage-card">
                    <div class="about-engage-icon"><i class="fas fa-shield-alt"></i></div>
                    <div>
                        <div class="about-engage-title">{{ __('home.about.engage1_title') }}</div>
                        <p class="about-engage-desc">{{ __('home.about.engage1_desc') }}</p>
                    </div>
                </div>
                <div class="about-engage-card">
                    <div class="about-engage-icon"><i class="fas fa-bolt"></i></div>
                    <div>
                        <div class="about-engage-title">{{ __('home.about.engage2_title') }}</div>
                        <p class="about-engage-desc">{{ __('home.about.engage2_desc') }}</p>
                    </div>
                </div>
                <div class="about-engage-card" style="margin-bottom:1.5rem;">
                    <div class="about-engage-icon"><i class="fas fa-globe"></i></div>
                    <div>
                        <div class="about-engage-title">{{ __('home.about.engage3_title') }}</div>
                        <p class="about-engage-desc">{{ __('home.about.engage3_desc') }}</p>
                    </div>
                </div>

                {{-- Types de prêts --}}
                <div style="font-size:.65rem;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:var(--navy);margin-bottom:.6rem;">
                    <i class="fas fa-tags" style="color:var(--gold);margin-right:.35rem;"></i>@lang('home.discover_our_loan_services')
                </div>
                <div class="about-loan-grid">
                    <div class="about-loan-item"><i class="fas fa-user-tie"></i> @lang('home.personal_loan')</div>
                    <div class="about-loan-item"><i class="fas fa-home"></i> @lang('home.mortgage_loan')</div>
                    <div class="about-loan-item"><i class="fas fa-car"></i> @lang('home.auto_loan')</div>
                    <div class="about-loan-item"><i class="fas fa-graduation-cap"></i> @lang('home.student_loan')</div>
                    <div class="about-loan-item"><i class="fas fa-briefcase"></i> @lang('home.business_loan')</div>
                    <div class="about-loan-item"><i class="fas fa-credit-card"></i> @lang('home.microcredit')</div>
                </div>

                {{-- Partenaires bancaires --}}
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
     SERVICES GRID
============================================================ --}}
<section class="py-24" style="background:var(--cream);" id="services">
    <div class="container">
        <div class="row align-items-end mb-12">
            <div class="col-lg-8">
                <div class="section-label">{{ __('home.services.sectagline') }}</div>
                <h2 class="section-title mb-0">{{ __('home.services.sectitle') }}</h2>
            </div>
            <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
                <a href="{{ route('services', ['locale' => $locale]) }}" class="btn-outline">
                    @lang('menu.services') <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>

        <div class="row g-4 gutter-y-30">
            @php
            $services = [
                ['route' => 'services.personal', 'img' => 'service-3-1.jpg', 'label' => 'menu.personal',  'icon' => 'fas fa-user-tie'],
                ['route' => 'services.study',    'img' => 'service-3-2.jpg', 'label' => 'menu.study',     'icon' => 'fas fa-graduation-cap'],
                ['route' => 'services.home',     'img' => 'service-3-3.jpg', 'label' => 'menu.home_loan', 'icon' => 'fas fa-home'],
                ['route' => 'services.business', 'img' => 'service-3-4.jpg', 'label' => 'menu.business',  'icon' => 'fas fa-briefcase'],
                ['route' => 'services.auto',     'img' => 'service-3-5.jpg', 'label' => 'menu.auto',      'icon' => 'fas fa-car'],
                ['route' => 'services.bike',     'img' => 'service-3-6.jpg', 'label' => 'menu.bike',      'icon' => 'fas fa-bicycle'],
            ];
            @endphp
            @foreach ($services as $i => $svc)
            <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-duration="800ms" data-wow-delay="{{ $i * 80 }}ms">
                <div class="service-card">
                    <div class="service-card__image">
                        <img src="{{ asset('assets/images/services/' . $svc['img']) }}" alt="@lang($svc['label'])">
                        <div class="service-card__image__overlay"></div>
                    </div>
                    <div class="service-card__body">
                        <div class="service-card__icon"><i class="{{ $svc['icon'] }}"></i></div>
                        <h3 class="service-card__title">
                            <a href="{{ route($svc['route'], ['locale' => $locale]) }}">@lang($svc['label'])</a>
                        </h3>
                        <p class="service-card__desc">{{ __('home.services.sectagline') }}</p>
                        <a href="{{ route($svc['route'], ['locale' => $locale]) }}" class="service-card__link">
                            @lang('menu.read_more') <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ============================================================
     HOW IT WORKS
============================================================ --}}
<section class="py-24 bg-white">
    <div class="container">
        <div class="text-center mb-14">
            <div class="section-label justify-content-center">{{ __('home.works.sectagline') }}</div>
            <h2 class="section-title section-title">{{ __('home.works.sectitle') }}</h2>
        </div>
        <div class="row g-4 gutter-y-30">
            @foreach ([1,2,3,4] as $s)
            <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-duration="800ms" data-wow-delay="{{ ($s-1)*80 }}ms">
                <div class="card-glass step-card">
                    <div class="step-card__number">0{{ $s }}</div>
                    <div class="step-card__icon">
                        <img src="{{ asset('assets/images/working-process/working-process-1-' . $s . '.png') }}" alt="">
                    </div>
                    <h3 class="step-card__title">{{ __('home.works.step' . $s . '.title') }}</h3>
                    <p class="step-card__desc">{{ __('home.works.step' . $s . '.desc') }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ============================================================
     LOAN CALCULATOR
============================================================ --}}
<section class="calc-section py-24" id="simulate">
    <div class="container">
        <div class="row gutter-y-50 align-items-center">
            <div class="col-lg-5 wow fadeInLeft" data-wow-duration="900ms">
                <div class="section-label" style="color:var(--gold);">{{ __('home.works.sectagline') }}</div>
                <h2 class="section-title section-title--white">{{ __('home.loan_reasons.sectitle') }}</h2>
                <p class="section-sub section-sub--white mb-8">{{ __('home.about.text2') }}</p>

                @foreach ([1,2,3] as $r)
                <div class="d-flex align-items-start gap-3 mb-4">
                    <div style="width:36px;height:36px;background:rgba(200,169,81,.15);border-radius:50%;display:flex;align-items:center;justify-content:center;color:var(--gold);flex-shrink:0;">
                        <i class="fas fa-check"></i>
                    </div>
                    <div>
                        <h4 style="font-family:'Playfair Display',serif;font-size:1rem;font-weight:700;color:#fff;margin:0 0 .25rem;">
                            {{ __('home.loan_reasons.reasons.title' . $r) }}
                        </h4>
                        <p style="font-size:.875rem;color:rgba(255,255,255,.55);margin:0;line-height:1.65;">
                            {{ __('home.loan_reasons.reasons.desc' . $r) }}
                        </p>
                    </div>
                </div>
                @endforeach

                <div class="mt-6">
                    <a href="{{ route('loan', ['locale' => $locale]) }}" class="btn-primary btn-primary--lg">
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
     STATS
============================================================ --}}
<section style="background:var(--navy);">
    <div class="container">
        <div class="row gutter-y-0">
            @php
            $stats = [
                ['stop'=>'2500', 'suffix'=>'+', 'prefix'=>'',  'label'=> __('home.customer_satisfaction_rate')],
                ['stop'=>'95',   'suffix'=>'k', 'prefix'=>'€', 'label'=> __('home.total_loan_amount_granted')],
                ['stop'=>'24',   'suffix'=>'h', 'prefix'=>'',  'label'=> __('home.average_approval_time')],
                ['stop'=>'5',    'suffix'=>'+', 'prefix'=>'',  'label'=> __('home.years_experience')],
            ];
            @endphp
            @foreach ($stats as $i => $stat)
            <div class="col-lg-3 col-sm-6 wow fadeInUp" data-wow-duration="800ms" data-wow-delay="{{ $i*80 }}ms">
                <div class="stat-item {{ $i < 3 ? 'stat-item--sep' : '' }}">
                    <div class="stat-item__number">
                        @if($stat['prefix'])<span style="font-size:.6em;margin-right:2px;">{{ $stat['prefix'] }}</span>@endif
                        <span class="count-text" data-stop="{{ $stat['stop'] }}" data-speed="1500">{{ $stat['stop'] }}</span>
                        @if($stat['suffix'])<span style="font-size:.6em;margin-left:2px;">{{ $stat['suffix'] }}</span>@endif
                    </div>
                    <div class="stat-item__label">{{ $stat['label'] }}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ============================================================
     BANQUES PARTENAIRES — après les stats (signal de confiance)
============================================================ --}}
@push('styles')
<style>
.partners-strip { display:flex; flex-wrap:wrap; align-items:center; justify-content:center; gap:1.1rem; }
.partner-logo {
    display:flex; align-items:center; justify-content:center;
    padding:.8rem 1.5rem; min-width:120px; height:66px;
    background:#fff; border:1.5px solid #e5e7eb; border-radius:12px;
    filter:grayscale(1); opacity:.5;
    transition:filter .3s ease, opacity .3s ease, border-color .3s ease, box-shadow .3s ease;
    cursor:default;
}
.partner-logo:hover {
    filter:grayscale(0); opacity:1;
    border-color:var(--gold); box-shadow:0 4px 22px rgba(200,169,81,.2);
}
@media (max-width:576px) {
    .partner-logo { min-width:100px; padding:.65rem 1rem; height:56px; }
    .partners-strip { gap:.65rem; }
}
</style>
@endpush

<section class="py-10" style="background:#f7f8fa;border-top:1px solid #eaecf0;border-bottom:1px solid #eaecf0;">
    <div class="container">
        <p class="text-center" style="font-size:.68rem;font-weight:800;text-transform:uppercase;letter-spacing:.12em;color:#9ca3af;margin-bottom:1.4rem;">
            @lang('home.partners_label')
        </p>
        <div class="partners-strip">
            <div class="partner-logo"><img src="{{ asset('images/partners/bnpparibas.svg') }}" alt="BNP Paribas" style="height:36px;width:auto;"></div>
            <div class="partner-logo"><img src="{{ asset('images/partners/santander.svg') }}" alt="Santander" style="height:36px;width:auto;"></div>
            <div class="partner-logo"><img src="{{ asset('images/partners/pko.svg') }}" alt="PKO Bank Polski" style="height:36px;width:auto;"></div>
            <div class="partner-logo"><img src="{{ asset('images/partners/revolut.svg') }}" alt="Revolut" style="height:36px;width:auto;"></div>
            <div class="partner-logo"><img src="{{ asset('images/partners/bbva.svg') }}" alt="BBVA" style="height:36px;width:auto;"></div>
        </div>
    </div>
</section>

{{-- ============================================================
     TESTIMONIALS — Swiper carousel
============================================================ --}}
@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
<style>
.testimonials-swiper { padding-bottom: 3rem !important; overflow: hidden; isolation: isolate; }
@media (min-width: 768px) { .testimonials-swiper { overflow: visible; } }
.testimonials-swiper .swiper-wrapper { align-items: stretch; }
.testimonials-swiper .swiper-slide { height: auto; display: flex; }
.testimonials-swiper .swiper-slide .testimonial-card { flex: 1; display: flex; flex-direction: column; }
.testimonials-swiper .swiper-slide .testimonial-card__quote { flex: 1; }
.testimonials-swiper .swiper-pagination { bottom: 0; }
.testimonials-swiper .swiper-pagination-bullet { background: var(--navy); opacity: .3; width: 8px; height: 8px; transition: all .3s; }
.testimonials-swiper .swiper-pagination-bullet-active { background: var(--gold); opacity: 1; width: 24px; border-radius: 4px; }
</style>
@endpush

<section class="py-24" style="background:var(--cream);" id="testimonials">
    <div class="container">
        <div class="text-center mb-14">
            <div class="section-label justify-content-center">{{ __('home.testimonials_title') }}</div>
            <h2 class="section-title">{{ __('home.testimonials_title') }}</h2>
        </div>

        <div class="swiper testimonials-swiper">
            <div class="swiper-wrapper">
                @foreach (range(1, 6) as $i)
                @php $t = __('home.testimonial_' . $i); @endphp
                <div class="swiper-slide">
                    <div class="testimonial-card" style="width:100%;">
                        <p class="testimonial-card__quote">{{ $t['quote'] }}</p>
                        <div class="testimonial-card__author">
                            <div style="width:44px;height:44px;border-radius:50%;background:var(--navy);display:flex;align-items:center;justify-content:center;color:var(--gold);font-weight:700;font-size:1.125rem;flex-shrink:0;">
                                {{ strtoupper(substr($t['name'], 0, 1)) }}
                            </div>
                            <div>
                                <p class="testimonial-card__name">{{ $t['name'] }}</p>
                                @if (!empty($t['location']))
                                <p style="font-size:.75rem;color:#999;margin:0 0 .2rem;">{{ $t['location'] }}</p>
                                @endif
                                <div class="testimonial-card__stars">
                                    @for($s=0;$s<5;$s++)<i class="fas fa-star"></i>@endfor
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="swiper-pagination"></div>
        </div>
    </div>
</section>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
new Swiper('.testimonials-swiper', {
    loop: true,
    speed: 700,
    autoplay: {
        delay: 4500,
        disableOnInteraction: false,
        pauseOnMouseEnter: true,
    },
    slidesPerView: 1,
    spaceBetween: 24,
    pagination: {
        el: '.swiper-pagination',
        clickable: true,
    },
    breakpoints: {
        640:  { slidesPerView: 1, spaceBetween: 20 },
        768:  { slidesPerView: 2, spaceBetween: 24 },
        1024: { slidesPerView: 3, spaceBetween: 30 },
    },
});
</script>
@endpush

{{-- ============================================================
     CTA BANNER
============================================================ --}}
<section class="cta-banner">
    <div class="container">
        <div class="row align-items-center gutter-y-30">
            <div class="col-lg-7 wow fadeInLeft" data-wow-duration="900ms">
                <div class="section-label" style="color:var(--gold);">@lang('menu.newsletter_title')</div>
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

        <hr style="border-color:rgba(255,255,255,.08);margin:3rem 0;">

        <div class="row align-items-center gutter-y-20">
            <div class="col-lg-4 wow fadeInLeft" data-wow-duration="900ms">
                <p style="color:rgba(255,255,255,.6);font-size:.9375rem;margin:0;">@lang('menu.newsletter_title')</p>
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
