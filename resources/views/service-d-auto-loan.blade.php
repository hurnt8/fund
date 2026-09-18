@extends('layouts.app')
@section('title', __('menu.auto'))
@section('content')
@php $menuKey = 'auto'; $loanKey = 'auto_loan'; $icon = 'fas fa-car'; @endphp
@include('partials.service-content')
@endsection
