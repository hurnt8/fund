@extends('layouts.app')
@section('title', __('menu.privacy'))

@section('content')
@php $locale = app()->getLocale(); @endphp

<div class="page-hero">
    <div class="container">
        <div class="page-hero__content">
            <h1 class="page-hero__title">@lang('menu.privacy')</h1>
            <ul class="page-hero__breadcrumb">
                <li><a href="{{ route('home', ['locale' => $locale]) }}">@lang('menu.home')</a></li>
                <li class="sep"><i class="fas fa-chevron-right"></i></li>
                <li>@lang('menu.privacy')</li>
            </ul>
        </div>
    </div>
</div>

<section class="py-24 bg-white">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <div class="legal-content">
                    <h2>{{ __('privacy.introduction_title') }}</h2>
                    <p>{{ __('privacy.introduction_text') }}</p>

                    <h2>{{ __('privacy.information_collection_title') }}</h2>
                    <p>{{ __('privacy.information_collection_text') }}</p>
                    <ul>
                        @foreach (__('privacy.information_collection_list') as $item)
                        <li>{{ $item }}</li>
                        @endforeach
                    </ul>

                    <h2>{{ __('privacy.information_use_title') }}</h2>
                    <p>{{ __('privacy.information_use_text') }}</p>
                    <ul>
                        @foreach (__('privacy.information_use_list') as $item)
                        <li>{{ $item }}</li>
                        @endforeach
                    </ul>

                    <h2>{{ __('privacy.information_sharing_title') }}</h2>
                    <p>{{ __('privacy.information_sharing_text') }}</p>
                    <ul>
                        @foreach (__('privacy.information_sharing_list') as $item)
                        <li>{{ $item }}</li>
                        @endforeach
                    </ul>

                    <h2>{{ __('privacy.information_security_title') }}</h2>
                    <p>{{ __('privacy.information_security_text') }}</p>

                    <h2>{{ __('privacy.your_rights_title') }}</h2>
                    <p>{{ __('privacy.your_rights_text') }}</p>
                    <ul>
                        @foreach (__('privacy.your_rights_list') as $item)
                        <li>{{ $item }}</li>
                        @endforeach
                    </ul>
                    <p>{{ __('privacy.your_rights_contact') }}</p>

                    <h2>{{ __('privacy.policy_updates_title') }}</h2>
                    <p>{{ __('privacy.policy_updates_text') }}</p>

                    <h2>{{ __('privacy.contact_title') }}</h2>
                    <p>{{ __('privacy.contact_text') }}</p>
                    <p>
                        @foreach (__('privacy.contact_details') as $line)
                        {{ $line }}<br>
                        @endforeach
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
