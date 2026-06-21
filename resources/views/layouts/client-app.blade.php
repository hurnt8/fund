<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="ltr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <meta name="mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
  <meta name="apple-mobile-web-app-title" content="Credixa">
  <meta name="theme-color" content="#060E1D">
  <meta name="description" content="Credixa — Espace client mobile">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>@yield('title', 'Credixa')</title>

  <link rel="manifest" href="{{ route('pwa.manifest') }}">
  <link rel="apple-touch-icon" href="/images/icon-192.svg">
  <link rel="icon" type="image/svg+xml" href="/images/icon-192.svg">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  @vite(['resources/css/client-app.css', 'resources/js/client-app.js'])

  {{-- Init theme AVANT le rendu pour éviter le flash blanc/noir --}}
  <script>
    (function(){
      var t = localStorage.getItem('credixa-theme') || 'dark';
      document.documentElement.dataset.theme = t;
    })();
  </script>
  <script>window._copiedLabel = '{{ __("app.copied") }}';</script>

  @stack('styles')
</head>
<body x-data>

{{-- ══ SHELL (scroll container — sans overflow:hidden) ══ --}}
<div class="ca-shell">

  {{-- ── TOPBAR ── --}}
  @hasSection('topbar')
    @yield('topbar')
  @else
  <header class="ca-topbar @yield('topbar_class')">
    @hasSection('back_btn')
    <a href="@yield('back_url', route('client.app.home'))" class="ca-topbar__back" aria-label="Retour">
      <i class="fas fa-arrow-left"></i>
    </a>
    @else
    <div style="width:38px"></div>
    @endif

    <span class="ca-topbar__title">@yield('page_title', 'Credixa')</span>

    @hasSection('topbar_action')
    @yield('topbar_action')
    @else
    <div style="width:38px"></div>
    @endif
  </header>
  @endif

  {{-- Flash messages --}}
  @if(session('success'))
  <div class="ca-flash ca-flash--ok" role="alert">
    <i class="fas fa-check-circle"></i> {{ session('success') }}
  </div>
  @endif
  @if(session('error'))
  <div class="ca-flash ca-flash--err" role="alert">
    <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
  </div>
  @endif

  {{-- ── CONTENU PRINCIPAL ── --}}
  <main class="ca-main" id="ca-main-content">
    @yield('content')
  </main>

</div>{{-- /.ca-shell --}}

{{-- ══════════════════════════════════════════════════════════════════
     BOTTOM NAVIGATION — hors du shell pour eviter le clip iOS Safari
     ══════════════════════════════════════════════════════════════════ --}}
<nav class="ca-nav" role="navigation" aria-label="{{ __('app.nav_label', [], app()->getLocale()) ?? 'Navigation' }}">

  {{-- Accueil --}}
  <a href="{{ route('client.app.home') }}"
     class="ca-nav-item {{ request()->routeIs('client.app.home') ? 'active' : '' }}"
     aria-label="{{ __('app.nav_home') }}">
    <i class="fas fa-house"></i>
    <span>{{ __('app.nav_home') }}</span>
  </a>

  {{-- Dossiers --}}
  <a href="{{ route('client.app.dossiers') }}"
     class="ca-nav-item {{ request()->routeIs('client.app.dossiers', 'client.app.loans*') ? 'active' : '' }}"
     aria-label="{{ __('app.nav_loans') }}">
    <i class="fas fa-folder-open"></i>
    <span>{{ __('app.nav_loans') }}</span>
  </a>

  {{-- Transfert — bouton FAB central --}}
  <a href="{{ route('client.app.transfers') }}"
     class="ca-nav-item ca-nav-item--center {{ request()->routeIs('client.app.transfer*', 'client.app.transfers') ? 'active' : '' }}"
     aria-label="{{ __('app.nav_transfer') }}">
    <div class="ca-nav-center-btn" aria-hidden="true">
      <i class="fas fa-right-left"></i>
    </div>
    <span>{{ __('app.nav_transfer') }}</span>
  </a>

  {{-- Analytique --}}
  <a href="{{ route('client.app.analytics') }}"
     class="ca-nav-item {{ request()->routeIs('client.app.analytics') ? 'active' : '' }}"
     aria-label="{{ __('app.nav_analytics') }}">
    <i class="fas fa-chart-pie"></i>
    <span>{{ __('app.nav_analytics') }}</span>
  </a>

  {{-- Profil --}}
  <a href="{{ route('client.app.profile') }}"
     class="ca-nav-item {{ request()->routeIs('client.app.profile') ? 'active' : '' }}"
     aria-label="{{ __('app.nav_profile') }}">
    <i class="fas fa-circle-user"></i>
    <span>{{ __('app.nav_profile') }}</span>
  </a>

</nav>

{{-- ══ BANNIERE PWA ══ --}}
<div class="ca-install-banner" id="ca-install-banner" role="complementary">
  <div style="width:42px;height:42px;border-radius:14px;background:rgba(200,169,81,.15);display:flex;align-items:center;justify-content:center;flex-shrink:0">
    <i class="fas fa-mobile-screen" style="color:var(--ca-gold-l);font-size:1.25rem"></i>
  </div>
  <div style="flex:1;min-width:0">
    <div style="font-size:.875rem;font-weight:700;color:var(--ca-text);margin-bottom:.15rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
      {{ __('app.install') }}
    </div>
    <div style="font-size:.72rem;color:var(--ca-text-3)">{{ __('app.install_hint') }}</div>
  </div>
  <div style="display:flex;gap:.5rem;flex-shrink:0">
    <button id="ca-install-btn"
            style="background:linear-gradient(135deg,var(--ca-teal-l),var(--ca-teal));color:#fff;border:none;padding:.45rem .9rem;border-radius:var(--ca-radius-sm);font-size:.8rem;font-weight:700;cursor:pointer;white-space:nowrap">
      {{ __('app.install_btn') }}
    </button>
    <button onclick="document.getElementById('ca-install-banner').style.display='none'"
            aria-label="Fermer"
            style="background:none;border:1px solid var(--ca-border);color:var(--ca-text-3);width:32px;height:32px;border-radius:50%;font-size:.75rem;cursor:pointer;display:flex;align-items:center;justify-content:center;flex-shrink:0">
      <i class="fas fa-xmark"></i>
    </button>
  </div>
</div>

@stack('scripts')
</body>
</html>
