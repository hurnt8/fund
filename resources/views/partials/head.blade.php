<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', __('menu.home')) | Credixa</title>
    <meta name="description" content="{{ __('menu.footer_desc') }}">
    <link rel="canonical" href="{{ url()->current() }}">
    @foreach (['fr', 'en', 'pl', 'es'] as $l)
    <link rel="alternate" hreflang="{{ $l }}" href="{{ url($l) }}">
    @endforeach
    <link rel="icon" href="{{ asset('assets/images/favicons/favicon.png') }}">

    <!-- Fonts: Inter + Playfair Display -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,400;0,600;0,700;0,800;1,400;1,600&display=swap" rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    navy:      { DEFAULT:'#0B1A2E', mid:'#162540', light:'#1E3A5F', deep:'#060E1C' },
                    gold:      { DEFAULT:'#C8A951', light:'#E8CB72', pale:'#F5E9C8', dark:'#A88830' },
                    cream:     { DEFAULT:'#F7F3EC', light:'#FDFBF8' },
                },
                fontFamily: {
                    sans:  ['Inter','ui-sans-serif','system-ui','sans-serif'],
                    serif: ['Playfair Display','Georgia','serif'],
                },
                boxShadow: {
                    'card':  '0 1px 3px rgba(11,26,46,.06), 0 4px 16px rgba(11,26,46,.08)',
                    'card-hover': '0 4px 8px rgba(11,26,46,.08), 0 16px 40px rgba(11,26,46,.12)',
                    'gold':  '0 4px 24px rgba(200,169,81,.30)',
                    'nav':   '0 1px 0 rgba(11,26,46,.08)',
                },
                animation: {
                    'fade-in-up': 'fadeInUp .6s ease forwards',
                    'fade-in':    'fadeIn .5s ease forwards',
                    'pulse-slow': 'pulse 3s cubic-bezier(.4,0,.6,1) infinite',
                },
                keyframes: {
                    fadeInUp: { '0%':{ opacity:'0', transform:'translateY(24px)' }, '100%':{ opacity:'1', transform:'translateY(0)' } },
                    fadeIn:   { '0%':{ opacity:'0' }, '100%':{ opacity:'1' } },
                }
            }
        }
    }
    </script>

    <!-- Vendor: Bootstrap (grid + dropdown) -->
    <link rel="stylesheet" href="{{ asset('assets/vendors/bootstrap/css/bootstrap.min.css') }}">
    <!-- Vendor: Icons -->
    <link rel="stylesheet" href="{{ asset('assets/vendors/fontawesome/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/easilon-icons/style.css') }}">
    <!-- Vendor: noUiSlider (loan calculator) -->
    <link rel="stylesheet" href="{{ asset('assets/vendors/nouislider/nouislider.min.css') }}">

    <!-- Design system -->
    <link rel="stylesheet" href="{{ asset('assets/css/royal.css') }}">

    @stack('styles')
</head>
<body class="font-sans antialiased bg-white text-gray-900 @yield('body_class')" x-data="{ mobileOpen: false }">
