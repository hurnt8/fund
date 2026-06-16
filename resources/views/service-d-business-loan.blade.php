@extends('layouts.app')
@section('title', __('menu.business'))
@section('content')
@php $menuKey = 'business'; $loanKey = 'business_loan'; $image = 'service-d-1-4.jpg'; @endphp
@include('partials.service-content')
@endsection
