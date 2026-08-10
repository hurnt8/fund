<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="apple-mobile-web-app-title" content="Credixa">
<meta name="theme-color" content="#0A1628">
<link rel="manifest" href="{{ route('pwa.manifest') }}">
<link rel="apple-touch-icon" sizes="180x180" href="/images/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="512x512" href="/images/icon-512.png">
<link rel="icon" type="image/png" sizes="192x192" href="/images/icon-192.png">
<link rel="shortcut icon" href="/images/icon-192.png" type="image/png">
<title>{{ __('auth.client_login_title') }} — Credixa</title>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Space+Grotesk:wght@500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<style>
:root{
  --bg:   #080C18;
  --bg2:  #0C1120;
  --card: #0E1626;
  --inp:  #141C2E;
  --cyan: #0DCFDC;
  --cyan2:#09B5C8;
  --text: #FFFFFF;
  --sub:  rgba(255,255,255,.52);
  --muted:rgba(255,255,255,.28);
  --bdr:  rgba(255,255,255,.09);
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
html,body{
  height:100%;background:var(--bg);color:var(--text);
  font-family:'Inter',system-ui,sans-serif;font-size:15px;
  -webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale;
}
body{min-height:100vh;overflow-x:hidden}
a{text-decoration:none;color:inherit}

/* ── Loading overlay ── */
#ld{
  position:fixed;inset:0;z-index:9999;background:var(--bg);
  display:flex;flex-direction:column;align-items:center;justify-content:center;
  opacity:0;pointer-events:none;transition:opacity .35s ease;
}
#ld.on{opacity:1;pointer-events:all}
.ld-bar{
  position:absolute;top:0;left:0;width:0;height:3px;
  background:linear-gradient(90deg,var(--cyan),var(--cyan2),var(--cyan));
  background-size:200% 100%;border-radius:0 3px 3px 0;
}
#ld.on .ld-bar{animation:ldbar 1.8s cubic-bezier(.4,0,.2,1) forwards}
@keyframes ldbar{
  0%{width:0;background-position:100% 0}
  40%{width:60%;background-position:60% 0}
  100%{width:92%;background-position:0 0}
}
.ld-logo{
  width:76px;height:76px;border-radius:22px;
  background:linear-gradient(135deg,var(--cyan),var(--cyan2));
  display:flex;align-items:center;justify-content:center;
  margin-bottom:1.5rem;position:relative;
  box-shadow:0 0 40px rgba(13,207,220,.35);
}
.ld-logo span{
  font-family:'Space Grotesk',sans-serif;font-size:2rem;
  font-weight:800;color:#080C18;
}
.ld-ring{
  position:absolute;inset:-8px;border-radius:30px;
  border:2px solid rgba(13,207,220,.2);border-top-color:var(--cyan);
  animation:spin .9s linear infinite;
}
@keyframes spin{to{transform:rotate(360deg)}}
.ld-lbl{font-size:.8rem;font-weight:600;color:var(--sub);letter-spacing:.06em}
.ld-dots{display:flex;gap:.4rem;margin-top:.875rem}
.ld-dot{
  width:6px;height:6px;border-radius:50%;background:var(--cyan);
  animation:ldp 1.2s ease-in-out infinite;
}
.ld-dot:nth-child(2){animation-delay:.18s}
.ld-dot:nth-child(3){animation-delay:.36s}
@keyframes ldp{0%,80%,100%{transform:scale(.6);opacity:.4}40%{transform:scale(1);opacity:1}}

/* ── Background ── */
.bg-orbs{position:fixed;inset:0;pointer-events:none;z-index:0;overflow:hidden}
.orb{position:absolute;border-radius:50%;filter:blur(90px)}
.orb-1{
  width:480px;height:480px;top:-10%;right:-8%;
  background:radial-gradient(circle,rgba(13,207,220,.1) 0%,transparent 65%);
  animation:orbf 10s ease-in-out infinite alternate;
}
.orb-2{
  width:360px;height:360px;bottom:-15%;left:-8%;
  background:radial-gradient(circle,rgba(13,207,220,.06) 0%,transparent 65%);
  animation:orbf 14s ease-in-out infinite alternate-reverse;
}
@keyframes orbf{from{transform:scale(1)}to{transform:scale(1.1) translate(2%,3%)}}

/* ── Top bar ── */
.topbar{
  position:relative;z-index:10;
  display:flex;align-items:center;justify-content:space-between;
  padding:.9rem 1.5rem;
  padding-top:calc(.9rem + env(safe-area-inset-top,0px));
}
.topbar__back{
  display:inline-flex;align-items:center;gap:.45rem;
  font-size:.78rem;font-weight:500;color:var(--sub);transition:color .18s;
}
.topbar__back:hover{color:var(--text)}
.topbar__back i{font-size:.65rem}

/* Language dropdown */
.ls{position:relative}
.ls__btn{
  display:flex;align-items:center;gap:.45rem;cursor:pointer;
  background:rgba(255,255,255,.06);border:1.5px solid var(--bdr);
  border-radius:8px;padding:.35rem .7rem;
  font-size:.76rem;font-weight:600;color:var(--text);
  transition:border-color .18s,background .18s;
}
.ls__btn:hover{border-color:rgba(13,207,220,.4);background:rgba(13,207,220,.06)}
.ls__btn img{width:18px;height:12px;object-fit:cover;border-radius:2px}
.ls__chevron{font-size:.48rem;transition:transform .2s;color:var(--muted)}
.ls__menu{
  position:absolute;right:0;top:calc(100% + .5rem);
  background:#0E1626;border:1.5px solid var(--bdr);
  border-radius:12px;box-shadow:0 16px 48px rgba(0,0,0,.55);
  padding:.35rem;min-width:148px;z-index:1000;
}
.ls__opt{
  display:flex;align-items:center;gap:.5rem;
  padding:.45rem .7rem;border-radius:8px;
  font-size:.78rem;font-weight:600;color:rgba(255,255,255,.65);
  transition:all .15s;
}
.ls__opt:hover{background:rgba(255,255,255,.06);color:#fff}
.ls__opt img{width:18px;height:12px;object-fit:cover;border-radius:2px}
.ls__opt.cur{background:rgba(13,207,220,.12);color:var(--cyan)}

/* ── Center wrapper ── */
.page-wrap{
  position:relative;z-index:1;
  flex:1;display:flex;align-items:center;justify-content:center;
  padding:1.5rem 1.25rem 2rem;
  min-height:0;
}
.page-shell{display:flex;flex-direction:column;min-height:100vh}

/* ── Auth card ── */
.card{width:100%;max-width:400px}

/* Logo */
.logo-box{
  width:74px;height:74px;border-radius:22px;
  background:linear-gradient(135deg,var(--cyan),var(--cyan2));
  display:flex;align-items:center;justify-content:center;
  margin:0 auto 1.5rem;
  box-shadow:0 0 36px rgba(13,207,220,.3);
}
.logo-box img{height:40px;object-fit:contain;filter:brightness(0) invert(1)}
.logo-box span{
  font-family:'Space Grotesk',sans-serif;font-size:2rem;
  font-weight:800;color:#080C18;line-height:1;
}

/* Heading */
.card-head{text-align:center;margin-bottom:2rem}
.card-title{
  font-family:'Space Grotesk',sans-serif;
  font-size:1.75rem;font-weight:800;color:var(--text);
  margin-bottom:.4rem;
}
.card-sub{font-size:.82rem;color:var(--sub);line-height:1.6}

/* Error */
.ferr{
  display:flex;align-items:flex-start;gap:.55rem;
  background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.2);
  border-left:3px solid #ef4444;border-radius:10px;
  padding:.7rem .9rem;font-size:.79rem;color:#FCA5A5;margin-bottom:1.125rem;
}
.ferr i{margin-top:.1rem;flex-shrink:0}

/* Field */
.fgrp{margin-bottom:.875rem}
.flabel{
  display:block;font-size:.75rem;font-weight:600;
  color:rgba(255,255,255,.5);margin-bottom:.4rem;letter-spacing:.01em;
}
.frel{position:relative}
.ficon{
  position:absolute;left:.95rem;top:50%;transform:translateY(-50%);
  color:rgba(255,255,255,.3);font-size:.75rem;pointer-events:none;z-index:1;
  transition:color .18s;
}
.finput{
  width:100%;padding:.85rem 1rem .85rem 2.6rem;
  background:var(--inp);
  border:1.5px solid rgba(255,255,255,.08);
  border-radius:12px;
  font-size:.88rem;font-family:'Inter',sans-serif;color:var(--text);
  outline:none;transition:border-color .2s,box-shadow .2s,background .2s;
}
.finput::placeholder{color:rgba(255,255,255,.2)}
.finput:focus{
  border-color:var(--cyan);background:#161E30;
  box-shadow:0 0 0 3.5px rgba(13,207,220,.15);
}
.finput:focus ~ .ficon,.frel:focus-within .ficon{color:var(--cyan)}
.finput.err{border-color:#ef4444;box-shadow:0 0 0 3px rgba(239,68,68,.13)}
.feye{
  position:absolute;right:.9rem;top:50%;transform:translateY(-50%);
  background:none;border:none;color:rgba(255,255,255,.28);cursor:pointer;
  font-size:.78rem;padding:.3rem;display:flex;align-items:center;
  transition:color .18s;
}
.feye:hover{color:rgba(255,255,255,.7)}

/* Checkbox row */
.frow{
  display:flex;align-items:center;justify-content:space-between;
  margin:1rem 0 1.5rem;
}
.fcheck{display:flex;align-items:center;gap:.45rem}
.fcheck input{
  width:16px;height:16px;accent-color:var(--cyan);
  cursor:pointer;flex-shrink:0;border-radius:4px;
}
.fcheck label{font-size:.78rem;color:var(--sub);cursor:pointer;-webkit-user-select:none;user-select:none}
.fforgot{font-size:.78rem;font-weight:600;color:var(--cyan);transition:opacity .18s}
.fforgot:hover{opacity:.75}

/* Cyan pill submit button */
.fbtn{
  width:100%;padding:.95rem 1.5rem;border:none;border-radius:999px;
  font-size:.97rem;font-weight:700;font-family:'Inter',sans-serif;
  cursor:pointer;display:flex;align-items:center;justify-content:center;gap:.625rem;
  background:linear-gradient(90deg,var(--cyan) 0%,var(--cyan2) 100%);
  color:#080C18;letter-spacing:.01em;
  box-shadow:0 6px 28px rgba(13,207,220,.35),0 2px 8px rgba(0,0,0,.3);
  transition:filter .2s,box-shadow .2s,transform .1s;
}
.fbtn:hover{
  filter:brightness(1.08);
  box-shadow:0 8px 36px rgba(13,207,220,.5),0 2px 10px rgba(0,0,0,.35);
}
.fbtn:active{transform:scale(.975)}
.fbtn:disabled{opacity:.6;cursor:not-allowed;filter:none}
.btn-spinner{
  display:inline-block;width:18px;height:18px;border-radius:50%;
  border:2.5px solid rgba(8,12,24,.3);border-top-color:#080C18;
  animation:spin .65s linear infinite;
}

/* Staff link */
.alt-link{
  text-align:center;margin-top:1.625rem;
  font-size:.76rem;color:var(--muted);
}
.alt-link a{color:rgba(13,207,220,.75);font-weight:600;transition:color .18s}
.alt-link a:hover{color:var(--cyan)}

/* ── Quick-login avatar block ── */
.qlogin{display:flex;flex-direction:column;align-items:center;gap:.6rem;margin-bottom:2rem}
.qavatar{
  width:72px;height:72px;border-radius:50%;
  background:linear-gradient(135deg,var(--cyan),var(--cyan2));
  display:flex;align-items:center;justify-content:center;
  font-family:'Space Grotesk',sans-serif;font-size:1.45rem;font-weight:800;
  color:#080C18;letter-spacing:.02em;
  box-shadow:0 0 0 4px rgba(13,207,220,.18);
}
.qname{font-size:.93rem;font-weight:700;color:var(--text)}
.qemail{font-size:.78rem;color:var(--sub)}
.qchange{
  display:inline-flex;align-items:center;gap:.35rem;
  font-size:.74rem;font-weight:600;color:var(--muted);
  border:1.5px solid var(--bdr);border-radius:8px;
  padding:.28rem .65rem;margin-top:.15rem;
  transition:color .18s,border-color .18s;
}
.qchange:hover{color:var(--sub);border-color:rgba(255,255,255,.18)}

/* Footer */
.pg-foot{
  padding:.75rem 1.5rem 1.25rem;text-align:center;
  padding-bottom:calc(1.25rem + env(safe-area-inset-bottom,0px));
  font-size:.68rem;color:rgba(255,255,255,.22);
  position:relative;z-index:1;
}
.pg-foot a{color:rgba(255,255,255,.3)}.pg-foot a:hover{color:rgba(255,255,255,.6)}

/* ── PWA Install banner ── */
#pwa-banner{
  display:none;
  position:relative;z-index:10;
  margin:0 1.25rem .5rem;
  background:linear-gradient(135deg,rgba(13,207,220,.12),rgba(13,207,220,.05));
  border:1.5px solid rgba(13,207,220,.25);
  border-radius:16px;
  padding:.875rem 1rem;
  animation:fadeUp .4s ease .4s both;
}
.pwa-inner{display:flex;align-items:center;gap:.75rem}
.pwa-icon{
  width:42px;height:42px;border-radius:12px;flex-shrink:0;
  background:linear-gradient(135deg,var(--cyan),var(--cyan2));
  display:flex;align-items:center;justify-content:center;
  box-shadow:0 4px 14px rgba(13,207,220,.3);
}
.pwa-icon img{width:26px;height:26px;object-fit:contain;border-radius:6px}
.pwa-text{flex:1;min-width:0}
.pwa-title{font-size:.82rem;font-weight:700;color:var(--text);margin-bottom:.1rem}
.pwa-sub{font-size:.7rem;color:var(--sub);line-height:1.4}
.pwa-btn{
  flex-shrink:0;
  background:linear-gradient(90deg,var(--cyan),var(--cyan2));
  color:#080C18;border:none;border-radius:999px;
  padding:.42rem .9rem;font-size:.75rem;font-weight:700;
  cursor:pointer;white-space:nowrap;font-family:'Inter',sans-serif;
  box-shadow:0 4px 14px rgba(13,207,220,.3);
  transition:filter .18s,transform .1s;
}
.pwa-btn:hover{filter:brightness(1.1)}
.pwa-btn:active{transform:scale(.96)}
.pwa-close{
  position:absolute;top:.5rem;right:.6rem;
  background:none;border:none;color:var(--muted);
  font-size:.62rem;cursor:pointer;padding:.2rem;
  transition:color .18s;
}
.pwa-close:hover{color:var(--sub)}

/* iOS instructions */
#pwa-ios{
  display:none;
  margin:0 1.25rem .5rem;
  background:rgba(13,207,220,.07);border:1.5px solid rgba(13,207,220,.2);
  border-radius:16px;padding:.875rem 1rem;
  font-size:.76rem;color:var(--sub);line-height:1.7;
  animation:fadeUp .4s ease .4s both;position:relative;z-index:10;
}
.ios-step{display:flex;align-items:center;gap:.5rem;margin-bottom:.25rem}
.ios-step:last-child{margin-bottom:0}
.ios-step i{color:var(--cyan);font-size:.8rem;flex-shrink:0;width:16px;text-align:center}

/* Entrance animation */
@keyframes fadeUp{from{opacity:0;transform:translateY(16px)}to{opacity:1;transform:translateY(0)}}
.logo-box  {animation:fadeUp .4s ease .05s both}
.card-head {animation:fadeUp .4s ease .12s both}
.fgrp:nth-child(1){animation:fadeUp .4s ease .18s both}
.fgrp:nth-child(2){animation:fadeUp .4s ease .22s both}
.frow      {animation:fadeUp .4s ease .26s both}
.fbtn      {animation:fadeUp .4s ease .3s  both}
.alt-link  {animation:fadeUp .4s ease .35s both}
</style>
</head>
<body>

{{-- Loading overlay --}}
<div id="ld" role="status" aria-label="Connexion en cours">
  <div class="ld-bar"></div>
  <div class="ld-logo">
    <img src="/images/icon-192.png" alt="Credixa"
         style="width:48px;height:48px;object-fit:contain;border-radius:10px">
    <div class="ld-ring"></div>
  </div>
  <p class="ld-lbl">Connexion en cours…</p>
  <div class="ld-dots">
    <div class="ld-dot"></div>
    <div class="ld-dot"></div>
    <div class="ld-dot"></div>
  </div>
</div>

{{-- Background orbs --}}
<div class="bg-orbs" aria-hidden="true">
  <div class="orb orb-1"></div>
  <div class="orb orb-2"></div>
</div>

<div class="page-shell">

  {{-- Top bar --}}
  <div class="topbar">
    <a href="{{ url('/') }}" class="topbar__back">
      <i class="fas fa-arrow-left"></i> {{ __('auth.back_site') }}
    </a>

    {{-- Language switcher --}}
    @php
      $cur   = app()->getLocale();
      $langs = ['fr'=>['Français','png'],'en'=>['English','png'],'pl'=>['Polski','svg'],'es'=>['Español','png'],'ro'=>['Română','svg'],'hr'=>['Hrvatski','png']];
    @endphp
    <div class="ls" x-data="{open:false}">
      <button class="ls__btn" type="button"
              @click="open=!open" @click.outside="open=false">
        <img src="{{ asset('images/'.$cur.'.'.$langs[$cur][1]) }}" alt="{{ strtoupper($cur) }}">
        <span>{{ strtoupper($cur) }}</span>
        <i class="fas fa-chevron-down ls__chevron" :style="open?'transform:rotate(180deg)':''"></i>
      </button>
      <div class="ls__menu" x-show="open" x-transition style="display:none">
        @foreach($langs as $code=>[$label,$ext])
        <a href="{{ route('lang.switch',$code) }}"
           class="ls__opt {{ $cur===$code ? 'cur' : '' }}">
          <img src="{{ asset('images/'.$code.'.'.$ext) }}" alt="{{ $code }}">
          {{ $label }}
          @if($cur===$code)
          <i class="fas fa-check" style="margin-left:auto;font-size:.55rem"></i>
          @endif
        </a>
        @endforeach
      </div>
    </div>
  </div>

  {{-- Main content --}}
  <div class="page-wrap">
    <div class="card">

      {{-- Logo --}}
      <div class="logo-box">
        <img src="{{ asset('assets/images/logo new.png') }}"
             onerror="this.style.display='none';this.nextElementSibling.style.display='block'"
             alt="Credixa">
        <span style="display:none">C</span>
      </div>

      {{-- Heading --}}
      <div class="card-head">
        <h1 class="card-title">{{ __('auth.submit') }}</h1>
        <p class="card-sub">{{ __('auth.client_login_sub') }}</p>
      </div>

      {{-- Unblock success --}}
      @if(session('unblock_success'))
      <div class="ferr" style="background:rgba(74,222,128,.1);border-color:rgba(74,222,128,.25);border-left-color:#4ade80;color:#86efac">
        <i class="fas fa-circle-check" style="color:#4ade80"></i>
        <span>{{ session('unblock_success') }}</span>
      </div>
      @endif

      {{-- Error --}}
      @if($errors->any())
      <div class="ferr">
        <i class="fas fa-circle-exclamation"></i>
        <span>{{ $errors->first() }}</span>
      </div>
      @endif

      {{-- Form --}}
      <form id="login-form" action="{{ route('login.submit') }}" method="POST" novalidate>
        @csrf

        @if($remembered ?? null)
          {{-- Quick login: avatar + only password --}}
          @php
            $initials = collect(explode(' ', $remembered['name']))->take(2)->map(fn($w) => strtoupper(mb_substr($w,0,1)))->implode('');
          @endphp
          <div class="qlogin">
            <div class="qavatar">{{ $initials }}</div>
            <div class="qname">{{ $remembered['name'] }}</div>
            <div class="qemail">{{ $remembered['email'] }}</div>
            <a href="{{ route('login.forget') }}" class="qchange">
              <i class="fas fa-repeat" style="font-size:.6rem"></i>
              {{ __('auth.change_account') }}
            </a>
          </div>
          <input type="hidden" name="identifier" value="{{ $remembered['email'] }}">
        @else
          {{-- Full login form --}}
          <div class="fgrp">
            <label class="flabel" for="identifier">{{ __('auth.identifier') }}</label>
            <div class="frel">
              <i class="fas fa-envelope ficon"></i>
              <input type="text" id="identifier" name="identifier"
                     class="finput {{ $errors->has('identifier') ? 'err' : '' }}"
                     value="{{ old('identifier') }}"
                     placeholder="{{ __('auth.identifier_ph') }}"
                     autocomplete="username" inputmode="email" required>
            </div>
          </div>
        @endif

        <div class="fgrp">
          <label class="flabel" for="password">{{ __('auth.password_label') }}</label>
          <div class="frel">
            <i class="fas fa-lock ficon"></i>
            <input type="password" id="password" name="password"
                   class="finput {{ $errors->has('password') ? 'err' : '' }}"
                   placeholder="••••••••"
                   autocomplete="current-password" required>
            <button type="button" class="feye" onclick="tglPwd(this)" aria-label="Afficher le mot de passe">
              <i class="fas fa-eye"></i>
            </button>
          </div>
        </div>

        <div class="frow">
          @if(!($remembered ?? null))
          <div class="fcheck">
            <input type="checkbox" id="remember" name="remember">
            <label for="remember">{{ __('auth.remember') }}</label>
          </div>
          @else
          <div></div>
          @endif
          <a href="{{ route('password.request') }}" class="fforgot">{{ __('auth.forgot_password') }}</a>
        </div>

        <button type="submit" id="submit-btn" class="fbtn">
          <span id="btn-text">{{ __('auth.submit') }}</span>
        </button>
      </form>

      {{-- Staff portal link --}}
      {{-- <div class="alt-link">
        {{ __('auth.or_staff') }}
        <a href="{{ route('staff.login') }}">{{ __('auth.staff_portal_link') }}</a>
      </div> --}}

    </div>
  </div>

  {{-- ── PWA Install — Android/Chrome ── --}}
  <div id="pwa-banner">
    <button class="pwa-close" onclick="pwaDismiss()" aria-label="Fermer">
      <i class="fas fa-xmark"></i>
    </button>
    <div class="pwa-inner">
      <div class="pwa-icon">
        <img src="/images/icon-192.png" alt="Credixa">
      </div>
      <div class="pwa-text">
        <div class="pwa-title">Installer l'application</div>
        <div class="pwa-sub">Accès rapide · Notifications · Mode hors-ligne</div>
      </div>
      <button class="pwa-btn" id="pwa-install-btn">
        <i class="fas fa-download" style="margin-right:.3rem"></i>Installer
      </button>
    </div>
  </div>

  {{-- ── PWA Install — iOS Safari ── --}}
  <div id="pwa-ios">
    <div style="font-weight:700;color:var(--text);margin-bottom:.5rem;font-size:.8rem">
      <i class="fas fa-mobile-screen" style="color:var(--cyan);margin-right:.4rem"></i>
      Installer l'app Credixa sur votre iPhone
    </div>
    <div class="ios-step">
      <i class="fas fa-arrow-up-from-bracket"></i>
      <span>Appuyez sur <strong style="color:var(--cyan)">Partager</strong> dans Safari</span>
    </div>
    <div class="ios-step">
      <i class="fas fa-plus-square"></i>
      <span>Choisissez <strong style="color:var(--cyan)">Sur l'écran d'accueil</strong></span>
    </div>
    <div class="ios-step">
      <i class="fas fa-check-circle"></i>
      <span>Appuyez sur <strong style="color:var(--cyan)">Ajouter</strong> — c'est fait !</span>
    </div>
    <button onclick="document.getElementById('pwa-ios').style.display='none'"
            style="margin-top:.6rem;background:none;border:none;color:var(--muted);font-size:.7rem;cursor:pointer;padding:0">
      <i class="fas fa-xmark" style="margin-right:.25rem"></i>Fermer
    </button>
  </div>

  <div class="pg-foot">
    &copy; {{ date('Y') }} Credixa Invest &nbsp;·&nbsp;
    <a href="{{ url('/fr/terms') }}">CGU</a> &nbsp;·&nbsp;
    <a href="{{ url('/fr/privacy') }}">Confidentialité</a>
  </div>

</div>

<script>
function tglPwd(btn) {
  var inp = btn.closest('.frel').querySelector('.finput');
  var ico = btn.querySelector('i');
  if (inp.type === 'password') {
    inp.type = 'text';
    ico.className = 'fas fa-eye-slash';
  } else {
    inp.type = 'password';
    ico.className = 'fas fa-eye';
  }
}

document.getElementById('login-form').addEventListener('submit', function() {
  var btn  = document.getElementById('submit-btn');
  var text = document.getElementById('btn-text');
  btn.disabled = true;
  text.innerHTML = '<span style="display:inline-block;width:18px;height:18px;border-radius:50%;border:2.5px solid rgba(8,12,24,.3);border-top-color:#080C18;animation:spin .65s linear infinite;vertical-align:middle"></span>';
  setTimeout(function() { document.getElementById('ld').classList.add('on'); }, 300);
});

if ('serviceWorker' in navigator) {
  navigator.serviceWorker.register('/sw.js').catch(function(){});
}

/* ── PWA Install ── */
(function () {
  const DISMISS_KEY = 'cxa_pwa_dismissed';

  // Ne pas montrer si déjà installé en standalone
  var isStandalone = window.matchMedia('(display-mode: standalone)').matches
                   || window.navigator.standalone === true;
  if (isStandalone) return;

  // Ne pas montrer si l'utilisateur a déjà fermé la bannière
  if (localStorage.getItem(DISMISS_KEY)) return;

  /* Android / Chrome — beforeinstallprompt */
  var deferredPrompt = null;
  window.addEventListener('beforeinstallprompt', function (e) {
    e.preventDefault();
    deferredPrompt = e;
    document.getElementById('pwa-banner').style.display = 'block';
  });

  document.getElementById('pwa-install-btn').addEventListener('click', async function () {
    if (!deferredPrompt) return;
    deferredPrompt.prompt();
    var choice = await deferredPrompt.userChoice;
    deferredPrompt = null;
    document.getElementById('pwa-banner').style.display = 'none';
    if (choice.outcome === 'accepted') {
      localStorage.setItem(DISMISS_KEY, '1');
    }
  });

  window.addEventListener('appinstalled', function () {
    document.getElementById('pwa-banner').style.display = 'none';
    localStorage.setItem(DISMISS_KEY, '1');
  });

  /* iOS Safari — pas de beforeinstallprompt */
  var isIOS = /iphone|ipad|ipod/i.test(navigator.userAgent);
  var isSafari = /^((?!chrome|android).)*safari/i.test(navigator.userAgent);
  if (isIOS && isSafari && !isStandalone) {
    setTimeout(function () {
      document.getElementById('pwa-ios').style.display = 'block';
    }, 1500);
  }
})();

window.pwaDismiss = function () {
  document.getElementById('pwa-banner').style.display = 'none';
  localStorage.setItem('cxa_pwa_dismissed', '1');
};
</script>
</body>
</html>
