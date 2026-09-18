@extends('layouts.app')
@section('title', __('menu.faq'))

@section('content')
@php $locale = app()->getLocale(); @endphp

{{-- Page hero --}}
<div class="page-hero">
    <div class="container-sm">
        <div class="page-hero__content">
            <h1 class="page-hero__title">@lang('menu.faq')</h1>
            <ul class="page-hero__breadcrumb">
                <li><a href="{{ route('home', ['locale' => $locale]) }}">@lang('menu.home')</a></li>
                <li class="sep"><i class="fas fa-chevron-right"></i></li>
                <li>@lang('menu.faq')</li>
            </ul>
        </div>
    </div>
</div>

{{-- FAQ --}}
<section class="py-24 bg-white">
    <div class="container-sm">

        <div class="text-center mb-10">
            <div class="section-label justify-content-center">@lang('menu.faq')</div>
            <h2 class="section-title">{{ __('faq.section_title') }}</h2>
        </div>

        @php
        $types = [
            'personal_loan' => 'fas fa-user-tie',
            'home_loan'     => 'fas fa-home',
            'auto_loan'     => 'fas fa-car',
            'business_loan' => 'fas fa-briefcase',
            'study_loan'    => 'fas fa-graduation-cap',
            'bike_loan'     => 'fas fa-bicycle',
        ];
        // On ne garde que les catégories réellement traduites.
        $categories = [];
        foreach ($types as $type => $icon) {
            $faqs = __('loan.' . $type . '.details.faqs');
            if (is_array($faqs) && isset($faqs['question1'])) {
                $categories[$type] = ['icon' => $icon, 'faqs' => $faqs];
            }
        }
        $first = array_key_first($categories);
        @endphp

        @if ($first)
        <div class="faq-wrap wow fadeInUp" data-wow-duration="900ms"
             x-data="{ active: '{{ $first }}', open: 1 }">

            <div class="faq-tabs">
                @foreach ($categories as $type => $cat)
                <button type="button" class="faq-tab" :class="active === '{{ $type }}' ? 'is-active' : ''"
                        @click="active = '{{ $type }}'; open = 1">
                    <i class="{{ $cat['icon'] }}"></i> {{ __('loan.' . $type . '.section_title') }}
                </button>
                @endforeach
            </div>

            @foreach ($categories as $type => $cat)
            <div class="faq-list" x-show="active === '{{ $type }}'" x-cloak>
                @for ($q = 1; $q <= 3; $q++)
                @if (isset($cat['faqs']['question' . $q]))
                <div class="faq-row">
                    <button type="button"
                            class="faq-btn"
                            :class="open === {{ $q }} ? 'is-open' : ''"
                            @click="open = (open === {{ $q }}) ? null : {{ $q }}">
                        <span>{{ $cat['faqs']['question' . $q] }}</span>
                        <span class="faq-btn__icon">
                            <i class="fas fa-chevron-down"></i>
                        </span>
                    </button>
                    <div class="faq-body"
                         x-show="open === {{ $q }}"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 -translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 -translate-y-1">
                        {{ $cat['faqs']['answer' . $q] }}
                    </div>
                </div>
                @endif
                @endfor
            </div>
            @endforeach

        </div>
        @endif

    </div>
</section>

{{-- CTA --}}
<section class="cta-banner">
    <div class="container-sm">
        <div class="row align-items-center gutter-y-30">
            <div class="col-lg-8 wow fadeInLeft" data-wow-duration="900ms">
                <div class="section-label" style="color:var(--gold);">Support</div>
                <h2 class="section-title section-title--white mb-2">
                    {{ __('faq.cta_title') }}
                </h2>
                <p class="section-sub section-sub--white">
                    {{ __('faq.cta_text') }}
                </p>
            </div>
            <div class="col-lg-4 text-lg-end wow fadeInRight" data-wow-duration="900ms" data-wow-delay="100ms">
                <a href="{{ route('contact', ['locale' => $locale]) }}" class="btn-primary btn-primary--lg">
                    <i class="fas fa-envelope"></i> @lang('menu.contact')
                </a>
            </div>
        </div>
    </div>
</section>

@endsection
