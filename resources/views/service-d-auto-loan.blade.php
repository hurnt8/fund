@extends('layouts.app')
@section('title', __('menu.auto'))
@section('content')
@php $menuKey = 'auto'; $loanKey = 'auto_loan'; $image = 'service-d-1-2.jpg'; @endphp
@include('partials.service-content')
@endsection
