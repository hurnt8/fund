@extends('layouts.app')
@section('title', __('menu.personal'))
@section('content')
@php $menuKey = 'personal'; $loanKey = 'personal_loan'; $image = 'service-d-1-3.jpg'; @endphp
@include('partials.service-content')
@endsection
