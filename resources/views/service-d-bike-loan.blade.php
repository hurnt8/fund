@extends('layouts.app')
@section('title', __('menu.bike'))
@section('content')
@php $menuKey = 'bike'; $loanKey = 'bike_loan'; $icon = 'fas fa-bicycle'; @endphp
@include('partials.service-content')
@endsection
