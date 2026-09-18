@extends('layouts.app')
@section('title', __('menu.about'))

@section('content')
@php $locale = app()->getLocale(); @endphp

{{-- Page hero --}}
<div class="page-hero">
    <div class="container-sm">
        <div class="page-hero__content">
            <h1 class="page-hero__title">@lang('menu.about')</h1>
            <ul class="page-hero__breadcrumb">
                <li><a href="{{ route('home', ['locale' => $locale]) }}">@lang('menu.home')</a></li>
                <li class="sep"><i class="fas fa-chevron-right"></i></li>
                <li>@lang('menu.about')</li>
            </ul>
        </div>
    </div>
</div>

{{-- About intro --}}
<section class="py-24 bg-white">
    <div class="container-sm">
        <div class="row g-4 gutter-y-60 align-items-center">
            <div class="col-lg-6 wow fadeInLeft" data-wow-duration="900ms">
                <div class="about-visual">
                    <img src="{{ asset('assets/images/aurenza/bureaux-couloir.jpg') }}"
                         alt="Aurenza Capital" class="about-visual__img">
                    <div class="about-visual__ribbon">
                        <span class="about-visual__ribbon-num">5</span>
                        <span class="about-visual__ribbon-label">{{ __('home.about.exptitle') }}</span>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 wow fadeInRight" data-wow-duration="900ms" data-wow-delay="150ms">
                <div class="section-label">{{ __('home.about.sectagline') }}</div>
                <h2 class="section-title">{{ __('home.about.sectitle') }}</h2>

                <p style="color:var(--gray-500);font-size:.9375rem;line-height:1.8;margin-bottom:1.5rem;">
                    {{ __('home.about.text2') }}
                </p>

                {{-- 3 engagements clés --}}
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

                {{-- Types de prêts proposés --}}
                <div style="margin-bottom:.5rem;font-size:.68rem;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:var(--navy);">
                    <i class="fas fa-tags" style="color:var(--gold);margin-right:.35rem;"></i>@lang('home.discover_our_loan_services')
                </div>
                <div style="display:flex;flex-wrap:wrap;gap:.4rem;margin-bottom:1.5rem;">
                    @foreach([
                        ['fas fa-user-tie',       'home.personal_loan'],
                        ['fas fa-home',           'home.mortgage_loan'],
                        ['fas fa-car',            'home.auto_loan'],
                        ['fas fa-graduation-cap', 'home.student_loan'],
                        ['fas fa-briefcase',      'home.business_loan'],
                        ['fas fa-credit-card',    'home.microcredit'],
                    ] as $t)
                    <span style="display:inline-flex;align-items:center;gap:.35rem;padding:.3rem .75rem;border-radius:999px;background:var(--cream);border:1px solid #e2ddd0;font-size:.75rem;font-weight:700;color:var(--navy);">
                        <i class="{{ $t[0] }}" style="color:var(--gold-dark);font-size:.7rem;"></i> @lang($t[1])
                    </span>
                    @endforeach
                </div>

                {{-- Partenaires --}}
                <div style="padding:.85rem 1.1rem;background:#f7f8fa;border:1px solid #eaecf0;border-radius:12px;margin-bottom:1.5rem;">
                    <div style="font-size:.65rem;font-weight:800;text-transform:uppercase;letter-spacing:.12em;color:#9ca3af;margin-bottom:.75rem;">@lang('home.partners_title')</div>
                    <div style="display:flex;flex-wrap:wrap;align-items:center;gap:.65rem;">
                        <img src="{{ asset('images/partners/bnpparibas.svg') }}" alt="BNP Paribas" style="height:22px;width:auto;opacity:.55;filter:grayscale(1);">
                        <img src="{{ asset('images/partners/santander.svg') }}" alt="Santander" style="height:22px;width:auto;opacity:.55;filter:grayscale(1);">
                        <img src="{{ asset('images/partners/pko.svg') }}" alt="PKO Bank Polski" style="height:22px;width:auto;opacity:.55;filter:grayscale(1);">
                        <img src="{{ asset('images/partners/revolut.svg') }}" alt="Revolut" style="height:22px;width:auto;opacity:.55;filter:grayscale(1);">
                        <img src="{{ asset('images/partners/bbva.svg') }}" alt="BBVA" style="height:22px;width:auto;opacity:.55;filter:grayscale(1);">
                    </div>
                </div>

                <a href="{{ route('loan', ['locale' => $locale]) }}" class="btn-primary btn-primary--lg">
                    <i class="fas fa-file-signature"></i> @lang('menu.loan')
                </a>
            </div>
        </div>
    </div>
</section>

{{-- Stats --}}
<section style="background:var(--navy);">
    <div class="container-sm">
        <div class="row">
            @php
            $stats = [
                ['stop'=>'2500','suffix'=>'+','prefix'=>'', 'label'=> __('home.customer_satisfaction_rate')],
                ['stop'=>'95',  'suffix'=>'k','prefix'=>'€','label'=> __('home.total_loan_amount_granted')],
                ['stop'=>'24',  'suffix'=>'h','prefix'=>'', 'label'=> __('home.average_approval_time')],
                ['stop'=>'5',   'suffix'=>'+','prefix'=>'', 'label'=> __('home.years_experience')],
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

{{-- Why choose us --}}
<section class="py-24" style="background:var(--cream);">
    <div class="container-sm">
        <div class="text-center mb-14">
            <div class="section-label justify-content-center">{{ __('home.loan_reasons.sectagline') }}</div>
            <h2 class="section-title">{{ __('home.loan_reasons.sectitle') }}</h2>
        </div>
        <div class="row g-4 gutter-y-30">
            @foreach ([1,2,3] as $r)
            <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-duration="800ms" data-wow-delay="{{ ($r-1)*80 }}ms">
                <div class="card-glass p-8" style="padding:2rem;">
                    <div style="width:52px;height:52px;background:var(--gold-pale);border-radius:var(--radius-sm);display:flex;align-items:center;justify-content:center;color:var(--gold-dark);font-size:1.25rem;margin-bottom:1.25rem;">
                        <i class="fas fa-{{ $r===1 ? 'shield-alt' : ($r===2 ? 'bolt' : 'headset') }}"></i>
                    </div>
                    <h3 style="font-family:'Outfit',sans-serif;font-size:1.125rem;font-weight:700;color:var(--navy);margin-bottom:.625rem;">
                        {{ __('home.loan_reasons.reasons.title' . $r) }}
                    </h3>
                    <p style="font-size:.875rem;color:var(--gray-500);line-height:1.75;margin:0;">
                        {{ __('home.loan_reasons.reasons.desc' . $r) }}
                    </p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

@endsection
