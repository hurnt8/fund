@extends('layouts.app')
@section('title', __('menu.home_loan'))
@section('content')
@php $menuKey = 'home_loan'; $loanKey = 'home_loan'; $image = 'service-d-1-1.jpg'; @endphp
@include('partials.service-content')
@endsection
