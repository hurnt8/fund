@extends('layouts.app')
@section('title', __('menu.personal'))
@section('content')
@php $menuKey = 'personal'; $loanKey = 'personal_loan'; $icon = 'fas fa-user-tie'; @endphp
@include('partials.service-content')
@endsection
