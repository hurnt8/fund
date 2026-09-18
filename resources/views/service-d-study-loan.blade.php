@extends('layouts.app')
@section('title', __('menu.study'))
@section('content')
@php $menuKey = 'study'; $loanKey = 'study_loan'; $icon = 'fas fa-graduation-cap'; @endphp
@include('partials.service-content')
@endsection
