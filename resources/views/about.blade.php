@extends('layouts.app')
@section('title', __('menu.about'))

@section('content')
@php $locale = app()->getLocale(); @endphp

{{-- Page hero --}}
<div class="page-hero">
    <div class="container">
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
    <div class="container">
        <div class="row g-4 gutter-y-60 align-items-center">
            <div class="col-lg-6 wow fadeInLeft" data-wow-duration="900ms">
                <div class="about-image-wrap">
                    <img src="{{ asset('assets/images/about/about-1-1.jpg') }}"
                         alt="Credixa" class="about-image-main">
                    <img src="{{ asset('assets/images/about/about-1-2.jpg') }}"
                         alt="" class="about-image-secondary">
                    <div class="about-badge">
                        <span class="about-badge__number">34+</span>
                        <span class="about-badge__label">{{ __('home.about.exptitle') }}</span>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 wow fadeInRight" data-wow-duration="900ms" data-wow-delay="150ms">
                <div class="section-label">{{ __('home.about.sectagline') }}</div>
                <h2 class="section-title">{{ __('home.about.sectitle') }}</h2>

                <div class="about-highlight mb-4">
                    <i class="fas fa-coins about-highlight__icon"></i>
                    <p class="about-highlight__text">{{ __('home.about.text1') }}</p>
                </div>
                <p class="mb-6" style="color:var(--gray-500);font-size:.9375rem;line-height:1.8;">{{ __('home.about.text2') }}</p>

                <div class="row g-3 mb-6">
                    @foreach ([
                        ['fas fa-hand-holding-usd', __('home.about.check1')],
                        ['fas fa-check-circle',     __('home.about.check2')],
                        ['fas fa-headset',          __('home.loan_reasons.reasons.title1')],
                        ['fas fa-bolt',             __('home.loan_reasons.reasons.title2')],
                    ] as $feat)
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center gap-3 p-3" style="background:var(--cream);border-radius:var(--radius-sm);border-left:3px solid var(--gold);">
                            <div style="width:36px;height:36px;background:var(--gold-pale);border-radius:50%;display:flex;align-items:center;justify-content:center;color:var(--gold-dark);flex-shrink:0;">
                                <i class="{{ $feat[0] }}"></i>
                            </div>
                            <span style="font-size:.875rem;font-weight:600;color:var(--navy);">{{ $feat[1] }}</span>
                        </div>
                    </div>
                    @endforeach
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
    <div class="container">
        <div class="row">
            @php
            $stats = [
                ['stop'=>'8500','suffix'=>'+','prefix'=>'', 'label'=> __('home.customer_satisfaction_rate')],
                ['stop'=>'95',  'suffix'=>'k','prefix'=>'€','label'=> __('home.total_loan_amount_granted')],
                ['stop'=>'99',  'suffix'=>'%','prefix'=>'', 'label'=> __('home.average_approval_time')],
                ['stop'=>'550', 'suffix'=>'+','prefix'=>'', 'label'=> __('home.member')],
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
    <div class="container">
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
                    <h3 style="font-family:'Playfair Display',serif;font-size:1.125rem;font-weight:700;color:var(--navy);margin-bottom:.625rem;">
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
