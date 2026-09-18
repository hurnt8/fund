<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="theme-color" content="#061A14">
<link rel="icon" type="image/svg+xml" href="/images/icon-192.svg">
<title>Mot de passe oublié — Aurenza Capital</title>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
:root{
  --bg:   #061A14;
  --inp:  #103428;
  --brass: #DCBE87;
  --brass2:#C6A15B;
  --text: #FFFFFF;
  --sub:  rgba(255,255,255,.52);
  --muted:rgba(255,255,255,.28);
  --bdr:  rgba(255,255,255,.09);
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
html,body{
  height:100%;background:var(--bg);color:var(--text);
  font-family:'Outfit',system-ui,sans-serif;font-size:15px;
  -webkit-font-smoothing:antialiased;
}
body{min-height:100vh;overflow-x:hidden}
a{text-decoration:none;color:inherit}

/* Background orbs */
.bg-orbs{position:fixed;inset:0;pointer-events:none;z-index:0;overflow:hidden}
.orb{position:absolute;border-radius:50%;filter:blur(90px)}
.orb-1{width:480px;height:480px;top:-10%;right:-8%;background:radial-gradient(circle,rgba(220,190,135,.1) 0%,transparent 65%);animation:orbf 10s ease-in-out infinite alternate}
.orb-2{width:360px;height:360px;bottom:-15%;left:-8%;background:radial-gradient(circle,rgba(220,190,135,.06) 0%,transparent 65%);animation:orbf 14s ease-in-out infinite alternate-reverse}
@keyframes orbf{from{transform:scale(1)}to{transform:scale(1.1) translate(2%,3%)}}

/* Top bar */
.topbar{
  position:relative;z-index:10;
  display:flex;align-items:center;
  padding:.9rem 1.5rem;
  padding-top:calc(.9rem + env(safe-area-inset-top,0px));
}
.topbar__back{
  display:inline-flex;align-items:center;gap:.45rem;
  font-size:.78rem;font-weight:500;color:var(--sub);transition:color .18s;
}
.topbar__back:hover{color:var(--text)}
.topbar__back i{font-size:.65rem}

/* Page shell */
.page-shell{display:flex;flex-direction:column;min-height:100vh}
.page-wrap{
  position:relative;z-index:1;flex:1;
  display:flex;align-items:center;justify-content:center;
  padding:1.5rem 1.25rem 2rem;
}

/* Card */
.card{width:100%;max-width:400px;text-align:center}

/* Logo */
.logo-box{
  width:74px;height:74px;border-radius:22px;
  background:linear-gradient(135deg,var(--brass),var(--brass2));
  display:flex;align-items:center;justify-content:center;
  margin:0 auto 1.5rem;
  box-shadow:0 0 36px rgba(220,190,135,.3);
}
.logo-box img{height:40px;object-fit:contain;filter:brightness(0) invert(1)}
.logo-box span{font-family:'Outfit',sans-serif;font-size:2rem;font-weight:800;color:#061A14;line-height:1}

/* Icon badge */
.icon-badge{
  width:72px;height:72px;border-radius:50%;
  background:rgba(220,190,135,.1);border:1.5px solid rgba(220,190,135,.25);
  display:flex;align-items:center;justify-content:center;
  margin:0 auto 1.5rem;
  box-shadow:0 0 28px rgba(220,190,135,.15);
}
.icon-badge i{font-size:1.75rem;color:var(--brass)}

/* Heading */
.card-title{font-family:'Outfit',sans-serif;font-size:1.625rem;font-weight:800;color:var(--text);margin-bottom:.45rem}
.card-sub{font-size:.82rem;color:var(--sub);line-height:1.65;margin-bottom:1.875rem;max-width:320px;margin-left:auto;margin-right:auto}

/* Success state */
.success-box{
  background:rgba(74,222,128,.08);border:1.5px solid rgba(74,222,128,.2);
  border-radius:14px;padding:1.5rem 1.25rem;margin-bottom:1.5rem;
}
.success-box i{font-size:2rem;color:#4ade80;margin-bottom:.875rem;display:block}
.success-box p{font-size:.84rem;color:rgba(255,255,255,.7);line-height:1.65}
.success-box strong{color:#4ade80;font-weight:600}

/* Error */
.ferr{
  display:flex;align-items:flex-start;gap:.55rem;
  background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.2);
  border-left:3px solid #ef4444;border-radius:10px;
  padding:.7rem .9rem;font-size:.79rem;color:#FCA5A5;margin-bottom:1.125rem;text-align:left;
}
.ferr i{margin-top:.1rem;flex-shrink:0}

/* Field */
.fgrp{margin-bottom:1rem;text-align:left}
.flabel{display:block;font-size:.75rem;font-weight:600;color:rgba(255,255,255,.5);margin-bottom:.4rem}
.frel{position:relative}
.ficon{position:absolute;left:.95rem;top:50%;transform:translateY(-50%);color:rgba(255,255,255,.3);font-size:.75rem;pointer-events:none;transition:color .18s}
.finput{
  width:100%;padding:.85rem 1rem .85rem 2.6rem;
  background:var(--inp);border:1.5px solid rgba(255,255,255,.08);border-radius:12px;
  font-size:.88rem;font-family:'Outfit',sans-serif;color:var(--text);
  outline:none;transition:border-color .2s,box-shadow .2s,background .2s;
}
.finput::placeholder{color:rgba(255,255,255,.2)}
.finput:focus{border-color:var(--brass);background:#123A2C;box-shadow:0 0 0 3.5px rgba(220,190,135,.15)}
.finput:focus ~ .ficon,.frel:focus-within .ficon{color:var(--brass)}
.finput.err{border-color:#ef4444}

/* Cyan pill button */
.fbtn{
  width:100%;padding:.92rem 1.5rem;border:none;border-radius:999px;
  font-size:.97rem;font-weight:700;font-family:'Outfit',sans-serif;cursor:pointer;
  display:flex;align-items:center;justify-content:center;gap:.625rem;
  background:linear-gradient(90deg,var(--brass) 0%,var(--brass2) 100%);
  color:#061A14;letter-spacing:.01em;
  box-shadow:0 6px 28px rgba(220,190,135,.35),0 2px 8px rgba(0,0,0,.3);
  transition:filter .2s,box-shadow .2s,transform .1s;margin-top:.25rem;
}
.fbtn:hover{filter:brightness(1.08);box-shadow:0 8px 36px rgba(220,190,135,.5)}
.fbtn:active{transform:scale(.975)}
.fbtn:disabled{opacity:.6;cursor:not-allowed;filter:none}

/* Back to login */
.back-link{margin-top:1.375rem;font-size:.78rem;color:var(--muted)}
.back-link a{color:var(--brass);font-weight:600;transition:opacity .18s}
.back-link a:hover{opacity:.75}

/* Footer */
.pg-foot{
  padding:.75rem 1.5rem 1.25rem;text-align:center;
  padding-bottom:calc(1.25rem + env(safe-area-inset-bottom,0px));
  font-size:.68rem;color:rgba(255,255,255,.22);
  position:relative;z-index:1;
}
.pg-foot a{color:rgba(255,255,255,.28)}.pg-foot a:hover{color:rgba(255,255,255,.55)}

/* Entrance */
@keyframes fadeUp{from{opacity:0;transform:translateY(14px)}to{opacity:1;transform:translateY(0)}}
.icon-badge{animation:fadeUp .4s ease .05s both}
.card-title {animation:fadeUp .4s ease .1s  both}
.card-sub   {animation:fadeUp .4s ease .15s both}
.fgrp       {animation:fadeUp .4s ease .18s both}
.fbtn       {animation:fadeUp .4s ease .22s both}
.back-link  {animation:fadeUp .4s ease .26s both}
.success-box{animation:fadeUp .4s ease .05s both}
</style>
</head>
<body>

<div class="bg-orbs" aria-hidden="true">
  <div class="orb orb-1"></div>
  <div class="orb orb-2"></div>
</div>

<div class="page-shell">

  <div class="topbar">
    <a href="/login" class="topbar__back">
      <i class="fas fa-arrow-left"></i> Retour à la connexion
    </a>
  </div>

  <div class="page-wrap">
    <div class="card">

      {{-- Icon --}}
      <div class="icon-badge">
        <i class="fas fa-lock-open"></i>
      </div>

      <h1 class="card-title">Mot de passe oublié ?</h1>
      <p class="card-sub">Entrez votre adresse email. Nous vous enverrons un lien pour réinitialiser votre mot de passe.</p>

      @if(session('sent'))

        {{-- Success --}}
        <div class="success-box">
          <i class="fas fa-circle-check"></i>
          <p>Lien envoyé ! Vérifiez votre boîte mail<br><strong>et suivez les instructions.</strong></p>
        </div>
        <a href="/login" class="fbtn" style="display:flex">
          <i class="fas fa-arrow-left"></i> Retour à la connexion
        </a>

      @else

        {{-- Error --}}
        @if($errors->has('email'))
        <div class="ferr">
          <i class="fas fa-circle-exclamation"></i>
          <span>{{ $errors->first('email') }}</span>
        </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}"
              onsubmit="this.querySelector('button[type=submit]').disabled=true">
          @csrf

          <div class="fgrp">
            <label class="flabel" for="email">Adresse email</label>
            <div class="frel">
              <i class="fas fa-envelope ficon"></i>
              <input type="email" id="email" name="email"
                     class="finput {{ $errors->has('email') ? 'err' : '' }}"
                     value="{{ old('email') }}"
                     placeholder="votre@email.com"
                     autocomplete="email" required>
            </div>
          </div>

          <button type="submit" class="fbtn">
            <i class="fas fa-paper-plane"></i>
            Envoyer le lien
          </button>
        </form>

        <div class="back-link">
          Vous vous souvenez ? <a href="/login">Se connecter</a>
        </div>

      @endif

    </div>
  </div>

  <div class="pg-foot">
    &copy; {{ date('Y') }} Aurenza Capital &nbsp;·&nbsp;
    <a href="{{ url('/fr/terms') }}">CGU</a> &nbsp;·&nbsp;
    <a href="{{ url('/fr/privacy') }}">Confidentialité</a>
  </div>

</div>
</body>
</html>
