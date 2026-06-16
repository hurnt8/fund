@extends('layouts.app')
@section('title', __('menu.loan'))

@section('content')
@php $locale = app()->getLocale(); @endphp

{{-- Page hero --}}
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
        <div class="row g-4 gutter-y-40 align-items-start">

            {{-- Form --}}
            <div class="col-lg-8 wow fadeInLeft" data-wow-duration="900ms">
                <div class="form-card">
                    <div class="section-label mb-2">@lang('menu.loan')</div>
                    <h2 class="section-title mb-6">{{ __('loan.banner_title') }}</h2>

                    @if (session('success'))
                        <div class="alert alert-success">{{ __('message.success_loan') }}</div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger">{{ __('message.error_loan') }}</div>
                    @endif

                    <form method="POST" action="{{ route('loan.request') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="form-group">
                                    <label>{{ __('loan.label_name') }} *</label>
                                    <input type="text" name="name" class="form-control" value="{{ old('name') }}"
                                           placeholder="{{ __('loan.placeholder_name') }}" required>
                                    @error('name')<span class="form-error">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('loan.label_email') }} *</label>
                                    <input type="email" name="email" class="form-control" value="{{ old('email') }}"
                                           placeholder="{{ __('loan.placeholder_email') }}" required>
                                    @error('email')<span class="form-error">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('loan.label_phone') }} *</label>
                                    <input type="text" name="phone" class="form-control" value="{{ old('phone') }}"
                                           placeholder="{{ __('loan.placeholder_phone') }}" required>
                                    @error('phone')<span class="form-error">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('loan.label_address') }} *</label>
                                    <input type="text" name="address" class="form-control" value="{{ old('address') }}"
                                           placeholder="{{ __('loan.placeholder_address') }}" required>
                                    @error('address')<span class="form-error">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('loan.label_amount') }} *</label>
                                    <input type="number" name="amount" class="form-control" value="{{ old('amount') }}"
                                           placeholder="Ex: 25 000" min="1" step="0.01" required>
                                    @error('amount')<span class="form-error">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('loan.label_darly') }} *</label>
                                    <input type="number" name="darly" class="form-control" value="{{ old('darly') }}"
                                           placeholder="{{ __('loan.label_darly') }}" min="1" step="0.01" required>
                                    @error('darly')<span class="form-error">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('contact.subject') *</label>
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
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>NPI</label>
                                    <input type="text" name="npi" class="form-control" value="{{ old('npi') }}"
                                           placeholder="NPI (optionnel)">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Statut</label>
                                    <input type="text" name="status" class="form-control" value="{{ old('status') }}"
                                           placeholder="Salarié / Indépendant…">
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label>{{ __('loan.label_objet') }} *</label>
                                    <textarea name="objet" class="form-control" rows="4"
                                              placeholder="{{ __('loan.placeholder_objet') }}" required>{{ old('objet') }}</textarea>
                                    @error('objet')<span class="form-error">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label>Documents (PDF, JPG, PNG, DOC)</label>
                                    <input type="file" name="files[]" class="form-control" multiple
                                           accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                                    @error('files.*')<span class="form-error">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="col-12 mt-2">
                                <button type="submit" class="btn-primary btn-primary--lg w-100 justify-content-center">
                                    <i class="fas fa-paper-plane"></i>
                                    {{ __('loan.button') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="col-lg-4 wow fadeInRight" data-wow-duration="900ms" data-wow-delay="150ms">
                <div style="position:sticky;top:110px;" class="service-sidebar">

                    <div class="contact-widget">
                        <div class="contact-widget__icon"><i class="fas fa-phone-alt"></i></div>
                        <h4>@lang('contact.phone_title')</h4>
                        <p>Lun–Sam 8h00 – 18h00</p>
                        <a href="tel:+34613853614" class="contact-widget__phone">+34 613 85 36 14</a>
                        <a href="{{ route('contact', ['locale' => $locale]) }}" class="btn-outline w-100 justify-content-center">
                            <i class="fas fa-envelope"></i> @lang('menu.contact')
                        </a>
                    </div>

                    <div class="service-sidebar__widget">
                        <h3 class="service-sidebar__title">Pourquoi nous choisir ?</h3>
                        <ul class="advantage-list">
                            @foreach ([1,2,3] as $r)
                            <li>
                                <i class="fas fa-check advantage-list__icon"></i>
                                {{ __('home.loan_reasons.reasons.title' . $r) }}
                            </li>
                            @endforeach
                        </ul>
                    </div>

                </div>
            </div>

        </div>
    </div>
</section>

@endsection
