@extends('layouts.app')
@section('title', __('menu.contact'))

@section('content')
@php $locale = app()->getLocale(); @endphp

{{-- Page hero --}}
<div class="page-hero">
    <div class="container">
        <div class="page-hero__content">
            <h1 class="page-hero__title">@lang('menu.contact')</h1>
            <ul class="page-hero__breadcrumb">
                <li><a href="{{ route('home', ['locale' => $locale]) }}">@lang('menu.home')</a></li>
                <li class="sep"><i class="fas fa-chevron-right"></i></li>
                <li>@lang('menu.contact')</li>
            </ul>
        </div>
    </div>
</div>

{{-- Info bar --}}
<div class="contact-info-bar">
    <div class="container">
        <div class="row g-3 gutter-y-20">
            @foreach ([
                ['fas fa-map-marker-alt', __('contact.address_title'), __('contact.address_desc'), null],
                ['fas fa-phone-alt',      __('contact.phone_title'),   __('contact.phone_desc'),   'tel:+34613853614'],
                ['fas fa-envelope',       __('contact.mail_title'),    __('contact.mail_desc'),    'mailto:contact@credixa.eu'],
            ] as $i => $info)
            <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-duration="800ms" data-wow-delay="{{ $i*80 }}ms">
                <div class="contact-info-card">
                    <div class="contact-info-card__icon"><i class="{{ $info[0] }}"></i></div>
                    <div>
                        <p class="contact-info-card__title">{{ $info[1] }}</p>
                        @if($info[3])
                        <p class="contact-info-card__value"><a href="{{ $info[3] }}">{{ $info[2] }}</a></p>
                        @else
                        <p class="contact-info-card__value">{{ $info[2] }}</p>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- Form + Image panel --}}
<section class="py-24 bg-white">
    <div class="container">
        <div class="row g-4 gutter-y-40 align-items-stretch">

            {{-- Image panel --}}
            <div class="col-lg-5 wow fadeInLeft" data-wow-duration="900ms">
                <div class="contact-image-panel">
                    <img src="{{ asset('assets/images/resources/contact-1-1.jpg') }}"
                         alt="Contact Credixa" class="contact-image-panel__img">
                    <div class="contact-image-panel__info">
                        <div class="contact-panel__company">
                            <h3>Credixa</h3>
                            <p>{{ __('contact.detail_desc') }}</p>
                        </div>
                        <div class="contact-panel__item">
                            <div class="contact-panel__item-icon"><i class="fas fa-map-marker-alt"></i></div>
                            <div>
                                <span class="contact-panel__item-label">{{ __('contact.address_title') }}</span>
                                <span class="contact-panel__item-value">{{ __('contact.address_desc') }}</span>
                            </div>
                        </div>
                        <div class="contact-panel__item">
                            <div class="contact-panel__item-icon"><i class="fas fa-phone-alt"></i></div>
                            <div>
                                <span class="contact-panel__item-label">{{ __('contact.phone_title') }}</span>
                                <a href="tel:+34613853614" class="contact-panel__item-value">{{ __('contact.phone_desc') }}</a>
                            </div>
                        </div>
                        <div class="contact-panel__item">
                            <div class="contact-panel__item-icon"><i class="fas fa-envelope"></i></div>
                            <div>
                                <span class="contact-panel__item-label">{{ __('contact.mail_title') }}</span>
                                <a href="mailto:contact@credixa.eu" class="contact-panel__item-value">{{ __('contact.mail_desc') }}</a>
                            </div>
                        </div>
                        <div class="contact-panel__social">
                            <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                            <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                            <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Contact form --}}
            <div class="col-lg-7 wow fadeInRight" data-wow-duration="900ms" data-wow-delay="150ms">
                <div class="form-card h-100">
                    <div class="section-label mb-2">{{ __('contact.form_title') }}</div>
                    <h2 class="section-title mb-6">{{ __('contact.detail_title') }}</h2>

                    @if (session('success'))
                        <div class="alert alert-success">{{ __('message.success_contact') }}</div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger">{{ __('message.error') }}</div>
                    @endif

                    <form method="POST" action="{{ route('contact.send') }}">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('contact.placeholder_name') }} *</label>
                                    <input type="text" name="name" class="form-control" value="{{ old('name') }}"
                                           placeholder="{{ __('contact.placeholder_name') }}" required>
                                    @error('name')<span class="form-error">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('contact.placeholder_email') }} *</label>
                                    <input type="email" name="email" class="form-control" value="{{ old('email') }}"
                                           placeholder="{{ __('contact.placeholder_email') }}" required>
                                    @error('email')<span class="form-error">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label>{{ __('contact.subject') }} *</label>
                                    <select name="subject" class="form-control" required>
                                        <option value="">— {{ __('contact.subject') }} —</option>
                                        <option value="Prêt personnel"  {{ old('subject')=='Prêt personnel'  ?'selected':'' }}>@lang('menu.personal')</option>
                                        <option value="Prêt immobilier" {{ old('subject')=='Prêt immobilier' ?'selected':'' }}>@lang('menu.home_loan')</option>
                                        <option value="Prêt commercial" {{ old('subject')=='Prêt commercial' ?'selected':'' }}>@lang('menu.business')</option>
                                        <option value="Prêt étudiant"   {{ old('subject')=='Prêt étudiant'   ?'selected':'' }}>@lang('menu.study')</option>
                                        <option value="Prêt auto"       {{ old('subject')=='Prêt auto'       ?'selected':'' }}>@lang('menu.auto')</option>
                                        <option value="Prêt moto"       {{ old('subject')=='Prêt moto'       ?'selected':'' }}>@lang('menu.bike')</option>
                                        <option value="Autre"           {{ old('subject')=='Autre'           ?'selected':'' }}>Autre</option>
                                    </select>
                                    @error('subject')<span class="form-error">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label>Message *</label>
                                    <textarea name="message" class="form-control" rows="6"
                                              placeholder="{{ __('contact.placeholder_message') }}" required>{{ old('message') }}</textarea>
                                    @error('message')<span class="form-error">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn-primary btn-primary--lg w-100 justify-content-center">
                                    <i class="fas fa-paper-plane"></i>
                                    {{ __('contact.button') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</section>

@endsection
