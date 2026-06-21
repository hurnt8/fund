<!DOCTYPE html>
<html lang="{{ $user->locale ?? 'fr' }}">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>{{ __('auth.otp_email_subject') }}</title>
<style>
body{margin:0;padding:0;background:#0A1628;font-family:'Segoe UI',Arial,sans-serif}
.wrap{max-width:560px;margin:40px auto;background:#0D1F38;border-radius:20px;overflow:hidden;border:1px solid rgba(255,255,255,.08)}
.header{background:linear-gradient(135deg,#0B2545 0%,#0D3060 100%);padding:40px 40px 32px;text-align:center}
.logo-box{width:70px;height:70px;border-radius:20px;background:linear-gradient(135deg,#22A396,#167A6C);margin:0 auto 20px;display:flex;align-items:center;justify-content:center;font-family:Georgia,serif;font-size:2rem;font-weight:900;color:#fff;line-height:1}
.h-title{font-size:1.375rem;font-weight:700;color:#F0F5FF;margin:0 0 6px}
.h-sub{font-size:.85rem;color:rgba(240,245,255,.5);margin:0}
.body{padding:36px 40px}
.greeting{font-size:.95rem;color:rgba(240,245,255,.7);margin-bottom:1.25rem}
.code-label{font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.09em;color:rgba(240,245,255,.4);margin-bottom:.75rem}
.code-box{background:rgba(255,255,255,.05);border:1px solid rgba(34,163,150,.25);border-radius:16px;padding:28px 20px;text-align:center;margin-bottom:1.5rem}
.code{font-family:'Courier New',monospace;font-size:2.75rem;font-weight:900;letter-spacing:.35em;color:#2ECBB7;text-shadow:0 0 24px rgba(46,203,183,.35)}
.expiry{font-size:.78rem;color:rgba(240,245,255,.4);margin-top:10px}
.notice{background:rgba(255,165,0,.08);border:1px solid rgba(255,165,0,.18);border-radius:10px;padding:14px 18px;font-size:.8rem;color:rgba(255,200,100,.75);line-height:1.6;margin-bottom:1.5rem}
.notice strong{color:rgba(255,200,100,.95)}
.footer-text{font-size:.75rem;color:rgba(240,245,255,.28);text-align:center;line-height:1.7}
.footer-text a{color:rgba(46,203,183,.7);text-decoration:none}
.divider{height:1px;background:rgba(255,255,255,.06);margin:0}
.footer{padding:24px 40px;text-align:center}
@media(max-width:600px){
  .body,.header,.footer{padding:24px 20px}
  .code{font-size:2.25rem;letter-spacing:.25em}
}
</style>
</head>
<body>
<div class="wrap">
  <div class="header">
    <div class="logo-box">C</div>
    <h1 class="h-title">{{ __('auth.otp_email_title') }}</h1>
    <p class="h-sub">Credixa — Espace Client Sécurisé</p>
  </div>

  <div class="body">
    <p class="greeting">
      {{ $user->name }},<br>
      {{ __('auth.otp_email_intro') }}
    </p>

    <div class="code-label">{{ __('auth.otp_email_code_label') }}</div>
    <div class="code-box">
      <div class="code">{{ $otp }}</div>
      <div class="expiry">⏱ {{ __('auth.otp_email_expiry') }}</div>
    </div>

    <div class="notice">
      <strong>{{ __('auth.otp_email_notice_title') }}</strong><br>
      {{ __('auth.otp_email_notice_body') }}
    </div>
  </div>

  <div class="divider"></div>

  <div class="footer">
    <p class="footer-text">
      &copy; {{ date('Y') }} Credixa Invest &nbsp;·&nbsp;
      {{ __('auth.otp_email_footer') }}
    </p>
  </div>
</div>
</body>
</html>
