@extends('layouts.app')
@section('title', __('menu.home_loan'))
@section('content')
@php $menuKey = 'home_loan'; $loanKey = 'home_loan'; $icon = 'fas fa-home'; @endphp
@include('partials.service-content')
@endsection
