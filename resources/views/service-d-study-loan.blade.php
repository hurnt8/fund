@extends('layouts.app')
@section('title', __('menu.study'))
@section('content')
@php $menuKey = 'study'; $loanKey = 'study_loan'; $image = 'service-d-1-5.jpg'; @endphp
@include('partials.service-content')
@endsection
