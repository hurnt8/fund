@extends('layouts.app')
@section('title', __('menu.contact'))

@section('content')
@php $locale = app()->getLocale(); @endphp

{{-- Page hero --}}
<div class="page-hero">
    <div class="container-sm">
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

<section class="py-24 bg-white">
    <div class="container-sm">
        <div class="row g-4 gutter-y-40 align-items-start">

            {{-- Contact form --}}
            <div class="col-lg-7 wow fadeInLeft" data-wow-duration="900ms">
                <div class="form-card">
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
                                        <option value="Personal loan"  {{ old('subject')=='Personal loan'  ?'selected':'' }}>@lang('menu.personal')</option>
                                        <option value="Home loan" {{ old('subject')=='Home loan' ?'selected':'' }}>@lang('menu.home_loan')</option>
                                        <option value="Business loan" {{ old('subject')=='Business loan' ?'selected':'' }}>@lang('menu.business')</option>
                                        <option value="Study loan"   {{ old('subject')=='Study loan'   ?'selected':'' }}>@lang('menu.study')</option>
                                        <option value="Auto loan"       {{ old('subject')=='Auto loan'       ?'selected':'' }}>@lang('menu.auto')</option>
                                        <option value="Bike loan"       {{ old('subject')=='Bike loan'       ?'selected':'' }}>@lang('menu.bike')</option>
                                        <option value="Other"           {{ old('subject')=='Other'           ?'selected':'' }}>Other</option>
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

            {{-- Coordonnées --}}
            <div class="col-lg-5 wow fadeInRight" data-wow-duration="900ms" data-wow-delay="150ms">
                <div class="contact-cards">

                    <div class="contact-card">
                        <div class="contact-card__icon"><i class="fas fa-map-marker-alt"></i></div>
                        <div>
                            <p class="contact-card__title">{{ __('contact.address_title') }}</p>
                            <p class="contact-card__value">{{ __('contact.address_desc') }}</p>
                        </div>
                    </div>

                    <div class="contact-card">
                        <div class="contact-card__icon"><i class="fas fa-phone-alt"></i></div>
                        <div>
                            <p class="contact-card__title">{{ __('contact.phone_title') }}</p>
                            <p class="contact-card__value"><a href="tel:+34613853614">{{ __('contact.phone_desc') }}</a></p>
                        </div>
                    </div>

                    <div class="contact-card">
                        <div class="contact-card__icon"><i class="fas fa-envelope"></i></div>
                        <div>
                            <p class="contact-card__title">{{ __('contact.mail_title') }}</p>
                            <p class="contact-card__value"><a href="mailto:contact@aurenzacapital.com">{{ __('contact.mail_desc') }}</a></p>
                        </div>
                    </div>

                    <div class="contact-card">
                        <div class="contact-card__icon"><i class="fas fa-building"></i></div>
                        <div>
                            <p class="contact-card__title">Aurenza Capital</p>
                            <p class="contact-card__value" style="font-weight:500;color:var(--gray-500);font-size:.875rem;">
                                {{ __('contact.detail_desc') }}
                            </p>
                        </div>
                    </div>

                    <div class="contact-cards__social">
                        <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                        <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    </div>

                </div>
            </div>

        </div>
    </div>
</section>

@endsection
