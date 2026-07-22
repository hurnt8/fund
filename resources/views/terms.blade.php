@extends('layouts.app')
@section('title', __('menu.terms'))

@section('content')
@php $locale = app()->getLocale(); @endphp

<div class="page-hero">
    <div class="container">
        <div class="page-hero__content">
            <h1 class="page-hero__title">@lang('menu.terms')</h1>
            <ul class="page-hero__breadcrumb">
                <li><a href="{{ route('home', ['locale' => $locale]) }}">@lang('menu.home')</a></li>
                <li class="sep"><i class="fas fa-chevron-right"></i></li>
                <li>@lang('menu.terms')</li>
            </ul>
        </div>
    </div>
</div>

<section class="py-24 bg-white">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <div class="legal-content">
                    @foreach (__('terms.sections') as $section)
                    <h2>{{ $section['title'] }}</h2>
                    <p>{!! $section['content'] !!}</p>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
