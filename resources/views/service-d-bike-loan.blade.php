@extends('layouts.app')
@section('title', __('menu.bike'))
@section('content')
@php $menuKey = 'bike'; $loanKey = 'bike_loan'; $image = 'service-d-1-6.jpg'; @endphp
@include('partials.service-content')
@endsection
