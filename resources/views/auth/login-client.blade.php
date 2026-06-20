<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ __('auth.client_login_title') }} | Credixa</title>
<link rel="icon" href="{{ asset('assets/images/favicons/favicon.png') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Playfair+Display:wght@700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('assets/vendors/bootstrap/css/bootstrap.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/vendors/fontawesome/css/all.min.css') }}">
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<style>
:root{
  --navy:#0B1A2E;--nm:#162540;--nl:#1E3A5F;
  --gold:#C8A951;--gd:#A88830;--gp:#F5E9C8;
}
html,body{height:100%;margin:0;padding:0}
body{font-family:'Inter',sans-serif;background:#fff;min-height:100vh;display:flex;flex-direction:column}

/* ════ LEFT PANEL ════ */
.auth-left{
  background:linear-gradient(160deg,var(--navy) 0%,var(--nm) 55%,#0d2545 100%);
  min-height:100vh; padding:2.5rem 3rem;
  display:flex;flex-direction:column;justify-content:space-between;
  position:relative;overflow:hidden;
}
.auth-left::before{
  content:'';position:absolute;top:-140px;right:-140px;
  width:420px;height:420px;border-radius:50%;
  background:radial-gradient(circle,rgba(200,169,81,.1) 0%,transparent 68%);
  pointer-events:none;
}
.auth-left::after{
  content:'';position:absolute;bottom:-80px;left:-80px;
  width:280px;height:280px;border-radius:50%;
  background:radial-gradient(circle,rgba(200,169,81,.06) 0%,transparent 70%);
  pointer-events:none;
}
.auth-left__logo img{height:40px}
.auth-left__body{position:relative;z-index:1}
.auth-left__tag{
  display:inline-flex;align-items:center;gap:.45rem;
  background:rgba(200,169,81,.1);border:1px solid rgba(200,169,81,.22);
  border-radius:999px;padding:.3rem .875rem;
  font-size:.7rem;font-weight:700;color:var(--gold);
  text-transform:uppercase;letter-spacing:.08em;margin-bottom:1.5rem;
}
.auth-left__title{
  font-family:'Playfair Display',serif;font-size:2.25rem;font-weight:800;
  color:#fff;line-height:1.2;margin-bottom:.875rem;
}
.auth-left__title span{color:var(--gold)}
.auth-left__sub{font-size:.875rem;color:rgba(255,255,255,.48);line-height:1.8;margin-bottom:2rem;max-width:340px}

/* Stats grid */
.auth-stats{display:grid;grid-template-columns:1fr 1fr;gap:.75rem;margin-bottom:2rem}
.auth-stat{
  background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.08);
  border-radius:12px;padding:.875rem 1rem;
}
.auth-stat__val{font-size:1.375rem;font-weight:800;color:#fff;line-height:1}
.auth-stat__val em{font-style:normal;color:var(--gold);font-size:.85em}
.auth-stat__lbl{font-size:.68rem;color:rgba(255,255,255,.38);margin-top:.2rem}

/* Feature pills */
.auth-features{display:flex;flex-wrap:wrap;gap:.5rem}
.auth-feat{
  display:inline-flex;align-items:center;gap:.375rem;
  background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.09);
  border-radius:999px;padding:.3rem .75rem;
  font-size:.7rem;color:rgba(255,255,255,.5);
}
.auth-feat i{color:var(--gold);font-size:.6rem}
.auth-left__copy{font-size:.7rem;color:rgba(255,255,255,.2);position:relative;z-index:1}
.auth-left__copy a{color:rgba(255,255,255,.3);text-decoration:none}
.auth-left__copy a:hover{color:rgba(255,255,255,.55)}

/* ════ RIGHT PANEL ════ */
.auth-right{
  background:#fff;display:flex;flex-direction:column;min-height:100vh;
}

/* Top bar */
.auth-topbar{
  display:flex;align-items:center;justify-content:space-between;
  padding:1.25rem 2rem;border-bottom:1px solid #f0f2f5;flex-shrink:0;
}
.auth-topbar__back{
  display:inline-flex;align-items:center;gap:.45rem;
  font-size:.8rem;color:#6b7280;text-decoration:none;font-weight:500;transition:color .18s;
}
.auth-topbar__back:hover{color:var(--navy)}
.auth-topbar__logo img{height:34px}

/* ── Lang switcher ── */
.ls{position:relative}
.ls__btn{
  display:flex;align-items:center;gap:.5rem;cursor:pointer;
  background:#f8f9fb;border:1.5px solid #e8eaf0;border-radius:9px;
  padding:.4rem .85rem;font-size:.8rem;font-weight:600;color:var(--navy);
  transition:all .18s;
}
.ls__btn:hover{border-color:var(--gold);background:var(--gp)}
.ls__btn img{width:20px;height:14px;object-fit:cover;border-radius:2px}
.ls__chevron{font-size:.55rem;transition:transform .2s}
.ls__menu{
  position:absolute;right:0;top:calc(100% + .5rem);
  background:#fff;border:1.5px solid #e8eaf0;border-radius:12px;
  box-shadow:0 10px 40px rgba(0,0,0,.12);padding:.375rem;
  min-width:160px;z-index:1000;
}
.ls__opt{
  display:flex;align-items:center;gap:.625rem;
  padding:.5rem .75rem;border-radius:8px;
  font-size:.8rem;font-weight:600;color:#374151;
  text-decoration:none;transition:all .15s;
}
.ls__opt:hover{background:#f3f4f6;color:var(--navy)}
.ls__opt img{width:20px;height:14px;object-fit:cover;border-radius:2px}
.ls__opt.is-cur{background:var(--gp);color:var(--gd)}
.ls__opt.is-cur i{color:var(--gd)}

/* ── Form area ── */
.auth-form-wrap{
  flex:1;display:flex;align-items:center;justify-content:center;
  padding:2rem;
}
.auth-form-inner{width:100%;max-width:400px}

/* Heading */
.form-eyebrow{
  font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.1em;
  color:var(--gd);display:flex;align-items:center;gap:.5rem;margin-bottom:.625rem;
}
.form-eyebrow::before{content:'';width:22px;height:2px;background:var(--gold);border-radius:2px}
.form-title{
  font-family:'Playfair Display',serif;font-size:1.875rem;font-weight:800;
  color:var(--navy);line-height:1.15;margin-bottom:.375rem;
}
.form-sub{font-size:.8125rem;color:#6b7280;margin-bottom:1.625rem}

/* Error */
.auth-error{
  display:flex;align-items:center;gap:.5rem;
  background:#fef2f2;border:1px solid #fecaca;border-left:3px solid #ef4444;
  border-radius:9px;padding:.7rem .9rem;font-size:.8rem;color:#991b1b;
  margin-bottom:1rem;
}

/* Fields */
.f-group{margin-bottom:1.125rem}
.f-label{display:block;font-size:.8rem;font-weight:600;color:#374151;margin-bottom:.375rem}
.f-wrap{position:relative}
.f-icon{
  position:absolute;left:.9rem;top:50%;transform:translateY(-50%);
  color:#9ca3af;font-size:.75rem;pointer-events:none;z-index:2;
}
.f-input{
  width:100%;padding:.7rem .9rem .7rem 2.5rem;
  border:1.5px solid #e5e7eb;border-radius:9px;
  font-size:.875rem;font-family:'Inter',sans-serif;color:#111827;
  outline:none;transition:border-color .2s,box-shadow .2s;background:#fff;
}
.f-input:focus{border-color:var(--navy);box-shadow:0 0 0 3px rgba(11,26,46,.08)}
.f-input.is-err{border-color:#ef4444;box-shadow:0 0 0 3px rgba(239,68,68,.08)}
.f-eye{
  position:absolute;right:.875rem;top:50%;transform:translateY(-50%);
  background:none;border:none;color:#9ca3af;cursor:pointer;
  font-size:.75rem;padding:.25rem;display:flex;align-items:center;
}
.f-eye:hover{color:var(--navy)}

/* Options row */
.f-options{display:flex;align-items:center;justify-content:space-between;margin-bottom:1.375rem}
.f-check{display:flex;align-items:center;gap:.45rem}
.f-check input{width:14px;height:14px;accent-color:var(--navy);cursor:pointer;flex-shrink:0}
.f-check label{font-size:.78rem;color:#6b7280;cursor:pointer;user-select:none}

/* Submit */
.btn-auth{
  width:100%;padding:.8rem;border:none;border-radius:10px;
  font-size:.9rem;font-weight:700;font-family:'Inter',sans-serif;cursor:pointer;
  display:flex;align-items:center;justify-content:center;gap:.5rem;
  background:var(--navy);color:#fff;
  transition:background .2s,transform .12s;letter-spacing:.01em;
}
.btn-auth:hover{background:var(--nm)}
.btn-auth:active{transform:scale(.98)}

/* Footer */
.auth-footer{
  padding:.875rem 2rem 1.25rem;text-align:center;
  font-size:.72rem;color:#9ca3af;flex-shrink:0;
}
.auth-footer a{color:#6b7280;text-decoration:none;transition:color .15s}
.auth-footer a:hover{color:var(--navy)}

/* ════ RESPONSIVE ════ */
@media (max-width:991.98px){
  .auth-left{display:none!important}
  .auth-right{min-height:100vh}
  .auth-topbar{padding:1rem 1.25rem}
  .auth-topbar__logo{display:block}
  .auth-form-wrap{padding:1.5rem 1.25rem}
}
@media (max-width:575.98px){
  .auth-form-inner{max-width:100%}
  .form-title{font-size:1.5rem}
  .auth-form-wrap{padding:1.25rem 1rem}
  .auth-topbar{padding:.875rem 1rem}
}
</style>
</head>
<body>
<div class="container-fluid p-0" style="min-height:100vh">
<div class="row g-0" style="min-height:100vh">

  {{-- ── LEFT PANEL ── --}}
  <div class="col-lg-5 d-none d-lg-flex">
    <div class="auth-left w-100">

      <div class="auth-left__logo">
        <a href="{{ url('/') }}"><img src="{{ asset('assets/images/logo new.png') }}" alt="Credixa"></a>
      </div>

      <div class="auth-left__body">
        <div class="auth-left__tag"><i class="fas fa-shield-alt"></i> {{ __('auth.feature_certified') }}</div>
        <h2 class="auth-left__title">{!! __('auth.client_brand_title') !!}</h2>
        <p class="auth-left__sub">{{ __('auth.client_brand_sub') }}</p>

        <div class="auth-stats">
          <div class="auth-stat">
            <div class="auth-stat__val">8 500<em>+</em></div>
            <div class="auth-stat__lbl">{{ __('auth.stat_clients') }}</div>
          </div>
          <div class="auth-stat">
            <div class="auth-stat__val">95<em>k€</em></div>
            <div class="auth-stat__lbl">{{ __('auth.stat_amount') }}</div>
          </div>
          <div class="auth-stat">
            <div class="auth-stat__val">24<em>h</em></div>
            <div class="auth-stat__lbl">{{ __('auth.stat_time') }}</div>
          </div>
          <div class="auth-stat">
            <div class="auth-stat__val">5<em>+</em></div>
            <div class="auth-stat__lbl">{{ __('auth.stat_years') }}</div>
          </div>
        </div>

        <div class="auth-features">
          <span class="auth-feat"><i class="fas fa-lock"></i> {{ __('auth.feature_secure') }}</span>
          <span class="auth-feat"><i class="fas fa-globe"></i> {{ __('auth.feature_currencies') }}</span>
          <span class="auth-feat"><i class="fas fa-certificate"></i> {{ __('auth.feature_certified') }}</span>
          <span class="auth-feat"><i class="fas fa-clock"></i> {{ __('auth.feature_fast') }}</span>
        </div>
      </div>

      <div class="auth-left__copy">
        &copy; {{ date('Y') }} Credixa Invest &nbsp;·&nbsp;
        <a href="{{ url('/fr/terms') }}">CGU</a> &nbsp;·&nbsp;
        <a href="{{ url('/fr/privacy') }}">Confidentialité</a>
      </div>
    </div>
  </div>

  {{-- ── RIGHT PANEL ── --}}
  <div class="col-12 col-lg-7">
    <div class="auth-right">

      {{-- Top bar --}}
      <div class="auth-topbar">
        <a href="{{ url('/') }}" class="auth-topbar__back">
          <i class="fas fa-arrow-left"></i> {{ __('auth.back_site') }}
        </a>

        <a href="{{ url('/') }}" class="auth-topbar__logo d-lg-none">
          <img src="{{ asset('assets/images/logo new.png') }}" alt="Credixa">
        </a>

        {{-- Language switcher --}}
        @php
          $cur = app()->getLocale();
          $langs = [
            'fr' => ['Français', 'png'],
            'en' => ['English',  'png'],
            'pl' => ['Polski',   'svg'],
            'es' => ['Español',  'png'],
          ];
        @endphp
        <div class="ls" x-data="{ open: false }">
          <button class="ls__btn" type="button"
                  @click="open = !open" @click.outside="open = false">
            <img src="{{ asset('images/' . $cur . '.' . $langs[$cur][1]) }}" alt="{{ strtoupper($cur) }}">
            <span>{{ strtoupper($cur) }}</span>
            <i class="fas fa-chevron-down ls__chevron"
               :style="open ? 'transform:rotate(180deg)' : 'transform:rotate(0deg)'"></i>
          </button>
          <div class="ls__menu" x-show="open" x-transition style="display:none">
            @foreach($langs as $code => [$label, $ext])
            <a href="{{ route('lang.switch', $code) }}"
               class="ls__opt {{ $cur === $code ? 'is-cur' : '' }}">
              <img src="{{ asset('images/' . $code . '.' . $ext) }}" alt="{{ $code }}">
              {{ $label }}
              @if($cur === $code)
                <i class="fas fa-check ms-auto" style="font-size:.6rem"></i>
              @endif
            </a>
            @endforeach
          </div>
        </div>
      </div>

      {{-- Form --}}
      <div class="auth-form-wrap">
        <div class="auth-form-inner">

          <div class="form-eyebrow">{{ __('auth.client_login_title') }}</div>
          <h1 class="form-title">{{ __('auth.submit') }}</h1>
          <p class="form-sub">{{ __('auth.client_login_sub') }}</p>

          @if($errors->any())
          <div class="auth-error">
            <i class="fas fa-exclamation-circle flex-shrink-0"></i>
            {{ $errors->first() }}
          </div>
          @endif

          <form action="{{ route('login.submit') }}" method="POST" novalidate>
            @csrf

            <div class="f-group">
              <label class="f-label" for="email">{{ __('auth.email') }}</label>
              <div class="f-wrap">
                <i class="fas fa-envelope f-icon"></i>
                <input type="email" id="email" name="email"
                       class="f-input {{ $errors->has('email') ? 'is-err' : '' }}"
                       value="{{ old('email') }}"
                       placeholder="{{ __('auth.email_ph') }}"
                       autocomplete="email" required>
              </div>
            </div>

            <div class="f-group">
              <label class="f-label" for="password">{{ __('auth.password_label') }}</label>
              <div class="f-wrap">
                <i class="fas fa-lock f-icon"></i>
                <input type="password" id="password" name="password"
                       class="f-input {{ $errors->has('password') ? 'is-err' : '' }}"
                       placeholder="••••••••"
                       autocomplete="current-password" required>
                <button type="button" class="f-eye" onclick="tglPwd('password','eye1')">
                  <i class="fas fa-eye" id="eye1"></i>
                </button>
              </div>
            </div>

            <div class="f-options">
              <div class="f-check">
                <input type="checkbox" id="remember" name="remember">
                <label for="remember">{{ __('auth.remember') }}</label>
              </div>
            </div>

            <button type="submit" class="btn-auth">
              <i class="fas fa-sign-in-alt"></i>
              {{ __('auth.submit') }}
            </button>
          </form>

        </div>
      </div>

      <div class="auth-footer">
        &copy; {{ date('Y') }} Credixa Invest &nbsp;·&nbsp;
        <a href="{{ url('/fr/terms') }}">CGU</a> &nbsp;·&nbsp;
        <a href="{{ url('/fr/privacy') }}">Confidentialité</a>
      </div>
    </div>
  </div>

</div><!-- .row -->
</div><!-- .container-fluid -->

<script>
function tglPwd(id, ico) {
  const f = document.getElementById(id), i = document.getElementById(ico);
  f.type = f.type === 'password' ? 'text' : 'password';
  i.classList.toggle('fa-eye'); i.classList.toggle('fa-eye-slash');
}
</script>
</body>
</html>
