<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', __('menu.home')) | Aurenza Capital</title>
    <meta name="description" content="{{ __('menu.footer_desc') }}">
    <link rel="canonical" href="{{ url()->current() }}">
    @foreach (['fr', 'en', 'pl', 'es', 'ro', 'hr', 'pt'] as $l)
    <link rel="alternate" hreflang="{{ $l }}" href="{{ url($l) }}">
    @endforeach
    <link rel="icon" type="image/svg+xml" href="{{ asset('assets/images/favicon-aurenza.svg') }}">
    <link rel="alternate icon" href="{{ asset('assets/images/favicons/favicon.png') }}">

    <!-- Fonts: Fraunces (titres) + Outfit (corps) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700;9..144,800&family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    forest:    { DEFAULT:'#0E3B2E', mid:'#14503D', light:'#1C6B51', deep:'#082A20' },
                    brass:     { DEFAULT:'#C6A15B', light:'#DCBE87', pale:'#F5EDDD', dark:'#9A7736' },
                    ivory:     { DEFAULT:'#FBF9F4', light:'#FEFDFB' },
                    ink:       '#1A1A17',
                    // Alias hérités : le balisage existant utilise navy/gold/cream
                    navy:      { DEFAULT:'#0E3B2E', mid:'#14503D', light:'#1C6B51', deep:'#082A20' },
                    gold:      { DEFAULT:'#C6A15B', light:'#DCBE87', pale:'#F5EDDD', dark:'#9A7736' },
                    cream:     { DEFAULT:'#FBF9F4', light:'#FEFDFB' },
                },
                fontFamily: {
                    sans:  ['Outfit','ui-sans-serif','system-ui','sans-serif'],
                    serif: ['Fraunces','Georgia','serif'],
                },
                boxShadow: {
                    'card':  '0 1px 3px rgba(14,59,46,.06), 0 4px 16px rgba(14,59,46,.08)',
                    'card-hover': '0 4px 8px rgba(14,59,46,.08), 0 16px 40px rgba(14,59,46,.12)',
                    'gold':  '0 4px 22px rgba(198,161,91,.30)',
                    'nav':   '0 1px 0 rgba(14,59,46,.08)',
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
