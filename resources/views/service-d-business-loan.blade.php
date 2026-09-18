@extends('layouts.app')
@section('title', __('menu.business'))
@section('content')
@php $menuKey = 'business'; $loanKey = 'business_loan'; $icon = 'fas fa-briefcase'; @endphp
@include('partials.service-content')
@endsection
