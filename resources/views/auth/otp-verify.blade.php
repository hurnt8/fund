<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="apple-mobile-web-app-title" content="Credixa">
<meta name="theme-color" content="#080C18">
<link rel="manifest" href="{{ route('pwa.manifest') }}">
<link rel="apple-touch-icon" href="/images/icon-192.svg">
<link rel="icon" type="image/svg+xml" href="/images/icon-192.svg">
<title>{{ __('auth.otp_title') }} — Credixa</title>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Space+Grotesk:wght@500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<style>
:root{
  --bg:   #080C18;
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
@keyframes ldbar{0%{width:0}40%{width:60%}100%{width:92%}}
.ld-logo{
  width:76px;height:76px;border-radius:22px;
  background:linear-gradient(135deg,var(--cyan),var(--cyan2));
  display:flex;align-items:center;justify-content:center;
  margin-bottom:1.5rem;position:relative;
  box-shadow:0 0 40px rgba(13,207,220,.35);
}
.ld-logo span{
  font-family:'Space Grotesk',sans-serif;font-size:2rem;font-weight:800;color:#080C18;
}
.ld-ring{
  position:absolute;inset:-8px;border-radius:30px;
  border:2px solid rgba(13,207,220,.2);border-top-color:var(--cyan);
  animation:spin .9s linear infinite;
}
@keyframes spin{to{transform:rotate(360deg)}}
.ld-lbl{font-size:.8rem;font-weight:600;color:var(--sub);letter-spacing:.06em}
.ld-dots{display:flex;gap:.4rem;margin-top:.875rem}
.ld-dot{width:6px;height:6px;border-radius:50%;background:var(--cyan);animation:ldp 1.2s ease-in-out infinite}
.ld-dot:nth-child(2){animation-delay:.18s}.ld-dot:nth-child(3){animation-delay:.36s}
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
  display:flex;align-items:center;justify-content:center;
  padding:.9rem 1.5rem;
  padding-top:calc(.9rem + env(safe-area-inset-top,0px));
}
.topbar__back{
  position:absolute;left:1.5rem;
  display:inline-flex;align-items:center;gap:.45rem;
  font-size:.78rem;font-weight:500;color:var(--sub);transition:color .18s;
}
.topbar__back:hover{color:var(--text)}
.topbar__back i{font-size:.65rem}
.topbar__logo img{height:28px;object-fit:contain}

/* ── Page shell ── */
.page-shell{display:flex;flex-direction:column;min-height:100vh}
.page-wrap{
  position:relative;z-index:1;flex:1;
  display:flex;flex-direction:column;align-items:center;
  padding:1.25rem 1.25rem 0;
}

/* ── Card ── */
.card{width:100%;max-width:400px;text-align:center}

/* Logo */
.logo-box{
  width:70px;height:70px;border-radius:20px;
  background:linear-gradient(135deg,var(--cyan),var(--cyan2));
  display:flex;align-items:center;justify-content:center;
  margin:0 auto 1.375rem;
  box-shadow:0 0 32px rgba(13,207,220,.28);
}
.logo-box img{height:38px;object-fit:contain;filter:brightness(0) invert(1)}
.logo-box span{font-family:'Space Grotesk',sans-serif;font-size:1.875rem;font-weight:800;color:#080C18;line-height:1}

/* Heading */
.card-title{
  font-family:'Space Grotesk',sans-serif;
  font-size:1.625rem;font-weight:800;color:var(--text);margin-bottom:.4rem;
}
.card-sub{font-size:.82rem;color:var(--sub);line-height:1.65;margin-bottom:1.75rem}
.card-sub strong{color:var(--cyan);font-weight:600}

/* Error */
.oerr{
  display:flex;align-items:flex-start;gap:.55rem;
  background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.2);
  border-left:3px solid #ef4444;border-radius:10px;
  padding:.7rem .9rem;font-size:.79rem;color:#FCA5A5;
  margin-bottom:1.25rem;text-align:left;
}
.oerr i{margin-top:.1rem;flex-shrink:0}

/* ── OTP digit circles ── */
.odigits{
  display:flex;gap:.5rem;justify-content:center;
  margin-bottom:1.625rem;
}
.odigit{
  width:50px;height:50px;border-radius:50%;
  background:var(--inp);
  border:2px solid rgba(255,255,255,.1);
  display:flex;align-items:center;justify-content:center;
  font-family:'Space Grotesk',sans-serif;font-size:1.375rem;font-weight:800;
  color:var(--text);
  transition:border-color .2s,background .2s,box-shadow .2s;
  flex-shrink:0;
}
.odigit.filled{
  border-color:var(--cyan);
  background:rgba(13,207,220,.12);
  box-shadow:0 0 0 3px rgba(13,207,220,.15);
  color:var(--cyan);
}
.odigit.active{
  border-color:var(--cyan);
  background:rgba(13,207,220,.08);
  box-shadow:0 0 0 4px rgba(13,207,220,.2),0 0 16px rgba(13,207,220,.2);
}
.odigit.active::after{
  content:'|';font-size:1rem;color:var(--cyan);
  animation:blink .8s step-end infinite;
}
@keyframes blink{0%,100%{opacity:1}50%{opacity:0}}
@media(max-width:380px){
  .odigit{width:42px;height:42px;font-size:1.125rem}
  .odigits{gap:.35rem}
}

/* Resend / timer */
.resend-row{margin-bottom:1.5rem;font-size:.8rem;color:var(--sub);min-height:2.25rem}
.resend-timer strong{color:var(--cyan);font-weight:700}
.resend-btn{
  background:none;border:none;cursor:pointer;padding:0;
  font-size:.8rem;font-weight:600;color:var(--cyan);
  transition:opacity .18s;
}
.resend-btn:hover{opacity:.75}
.resend-btn:disabled{opacity:.4;cursor:not-allowed}
.resend-msg{font-size:.76rem;font-weight:600;margin-top:.35rem;display:block}
.resend-msg.ok{color:#4ade80}.resend-msg.fail{color:#f87171}

/* Cyan pill button */
.obtn{
  width:100%;padding:.92rem 1.5rem;border:none;border-radius:999px;
  font-size:.97rem;font-weight:700;font-family:'Inter',sans-serif;
  cursor:pointer;display:flex;align-items:center;justify-content:center;gap:.625rem;
  background:linear-gradient(90deg,var(--cyan) 0%,var(--cyan2) 100%);
  color:#080C18;letter-spacing:.01em;
  box-shadow:0 6px 28px rgba(13,207,220,.35),0 2px 8px rgba(0,0,0,.3);
  transition:filter .2s,box-shadow .2s,transform .1s;
  margin-bottom:.25rem;
}
.obtn:hover{
  filter:brightness(1.08);
  box-shadow:0 8px 36px rgba(13,207,220,.5),0 2px 10px rgba(0,0,0,.35);
}
.obtn:active{transform:scale(.975)}
.obtn:disabled{opacity:.55;cursor:not-allowed;filter:none}
.btn-spinner{
  display:inline-block;width:18px;height:18px;border-radius:50%;
  border:2.5px solid rgba(8,12,24,.3);border-top-color:#080C18;
  animation:spin .65s linear infinite;
}

/* Don't receive link */
.no-otp{font-size:.76rem;color:var(--muted);margin-top:1rem}
.no-otp a,.no-otp button{
  background:none;border:none;padding:0;cursor:pointer;
  color:var(--cyan);font-size:.76rem;font-weight:600;transition:opacity .18s;
}
.no-otp a:hover,.no-otp button:hover{opacity:.75}

/* ── Numeric keypad ── */
.keypad-wrap{
  width:100%;max-width:400px;
  padding:1.375rem 1.5rem 0;
}
.keypad{
  display:grid;grid-template-columns:repeat(3,1fr);
  gap:.625rem;
}
.kbtn{
  aspect-ratio:1;border-radius:50%;
  background:rgba(255,255,255,.05);
  border:1.5px solid rgba(255,255,255,.08);
  color:var(--text);
  display:flex;flex-direction:column;align-items:center;justify-content:center;
  cursor:pointer;
  transition:background .12s,border-color .12s,transform .08s,box-shadow .12s;
  -webkit-tap-highlight-color:transparent;
  user-select:none;-webkit-user-select:none;
  position:relative;overflow:hidden;
}
.kbtn:active{
  transform:scale(.9);
  background:rgba(13,207,220,.12);
  border-color:rgba(13,207,220,.4);
  box-shadow:0 0 12px rgba(13,207,220,.2);
}
.kbtn:disabled{opacity:.35;cursor:not-allowed}
.knum{
  font-family:'Space Grotesk',sans-serif;
  font-size:1.25rem;font-weight:700;line-height:1;
}
.ksub{
  font-size:.42rem;font-weight:500;letter-spacing:.1em;
  color:var(--muted);font-family:'Inter',sans-serif;
  text-transform:uppercase;
}
.kbtn-del{
  background:transparent;border-color:transparent;
}
.kbtn-del i{font-size:1.1rem;color:var(--sub)}
.kbtn-del:active{
  background:rgba(255,255,255,.06);border-color:var(--bdr);
}
.kbtn-empty{pointer-events:none;background:none;border:none}

/* Spacer bottom */
.pg-foot{
  padding:.875rem 1.5rem 1.25rem;text-align:center;
  padding-bottom:calc(1.25rem + env(safe-area-inset-bottom,0px));
  font-size:.68rem;color:rgba(255,255,255,.22);
  position:relative;z-index:1;
}
.pg-foot a{color:rgba(255,255,255,.28)}.pg-foot a:hover{color:rgba(255,255,255,.55)}

/* Entrance */
@keyframes fadeUp{from{opacity:0;transform:translateY(14px)}to{opacity:1;transform:translateY(0)}}
.logo-box  {animation:fadeUp .4s ease .05s both}
.card-title{animation:fadeUp .4s ease .1s  both}
.card-sub  {animation:fadeUp .4s ease .15s both}
.odigits   {animation:fadeUp .4s ease .18s both}
.resend-row{animation:fadeUp .4s ease .22s both}
.obtn      {animation:fadeUp .4s ease .25s both}
.no-otp    {animation:fadeUp .4s ease .28s both}
.keypad-wrap{animation:fadeUp .4s ease .3s  both}
</style>
</head>
<body>

{{-- Loading overlay --}}
<div id="ld" role="status">
  <div class="ld-bar"></div>
  <div class="ld-logo"><span>C</span><div class="ld-ring"></div></div>
  <p class="ld-lbl">Vérification…</p>
  <div class="ld-dots">
    <div class="ld-dot"></div><div class="ld-dot"></div><div class="ld-dot"></div>
  </div>
</div>

{{-- Background --}}
<div class="bg-orbs" aria-hidden="true">
  <div class="orb orb-1"></div>
  <div class="orb orb-2"></div>
</div>

<div class="page-shell" x-data="otpApp()" x-init="startTimer()">

  {{-- Top bar --}}
  <div class="topbar">
    <a href="/login" class="topbar__back">
      <i class="fas fa-arrow-left"></i> {{ __('auth.otp_back') }}
    </a>
    <a href="{{ url('/') }}" class="topbar__logo">
      <img src="{{ asset('assets/images/logo new.png') }}"
           onerror="this.style.display='none'" alt="Credixa">
    </a>
  </div>

  {{-- Content --}}
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
      <h1 class="card-title">{{ __('auth.otp_heading') }}</h1>
      <p class="card-sub">
        {{ __('auth.otp_subtitle') }}<br>
        <strong>{{ $masked }}</strong>
      </p>

      {{-- Error --}}
      @if($errors->has('code'))
      <div class="oerr">
        <i class="fas fa-circle-exclamation"></i>
        <span>{{ $errors->first('code') }}</span>
      </div>
      @endif

      {{-- OTP form --}}
      <form id="otp-form" action="{{ route('otp.verify') }}" method="POST" novalidate>
        @csrf
        <input type="hidden" name="code" :value="digits.join('')">

        {{-- Digit circles --}}
        <div class="odigits">
          <template x-for="(d, i) in digits" :key="i">
            <div class="odigit"
                 :class="{filled: d !== '', active: d === '' && i === activeIndex}">
              <span x-text="d" x-show="d !== ''"></span>
              <span x-show="d === '' && i === activeIndex">|</span>
            </div>
          </template>
        </div>

        {{-- Hidden real input for physical keyboard --}}
        <input id="otp-real" type="tel" inputmode="numeric" pattern="[0-9]*"
               maxlength="6" autocomplete="one-time-code"
               style="position:absolute;opacity:0;width:1px;height:1px;pointer-events:none"
               @input="onRealInput($event)" @keydown="onRealKey($event)">

        {{-- Timer --}}
        <div class="resend-row">
          <span class="resend-timer" x-show="timeLeft > 0">
            {{ __('auth.otp_resend_in') }} <strong x-text="fmtTime()"></strong>
          </span>
          <span x-show="timeLeft <= 0 && !resending">
            {{ __('auth.otp_resend') }}
          </span>
          <span x-show="resendMsg" class="resend-msg" :class="resendOk ? 'ok' : 'fail'" x-text="resendMsg"></span>
        </div>

        {{-- Verify button --}}
        <button type="submit" class="obtn"
                :disabled="digits.join('').length < 6 || submitting">
          <span x-show="!submitting"><i class="fas fa-check"></i> {{ __('auth.otp_verify_btn') }}</span>
          <span x-show="submitting" class="btn-spinner"></span>
        </button>
      </form>

      {{-- Resend / don't receive --}}
      <div class="no-otp">
        {{ __('auth.otp_resend') . ' ?' }}
        <button type="button" @click="resend()" :disabled="timeLeft > 0 || resending">
          <span x-show="!resending">{{ __('auth.otp_resend') }}</span>
          <span x-show="resending"><i class="fas fa-circle-notch fa-spin"></i></span>
        </button>
      </div>

    </div>

    {{-- Keypad --}}
    <div class="keypad-wrap">
      <div class="keypad">
        @foreach([['1',''],['2','ABC'],['3','DEF'],['4','GHI'],['5','JKL'],['6','MNO'],['7','PQRS'],['8','TUV'],['9','WXYZ']] as [$n,$s])
        <button type="button" class="kbtn"
                @click="press('{{ $n }}')"
                :disabled="digits.join('').length >= 6 || submitting">
          <span class="knum">{{ $n }}</span>
          @if($s)<span class="ksub">{{ $s }}</span>@endif
        </button>
        @endforeach
        {{-- Row 4: backspace | 0 | empty --}}
        <button type="button" class="kbtn kbtn-del"
                @click="del()" :disabled="submitting">
          <i class="fas fa-delete-left"></i>
        </button>
        <button type="button" class="kbtn"
                @click="press('0')"
                :disabled="digits.join('').length >= 6 || submitting">
          <span class="knum">0</span>
        </button>
        <div class="kbtn kbtn-empty"></div>
      </div>
    </div>

  </div>

  <div class="pg-foot">
    &copy; {{ date('Y') }} Credixa Invest &nbsp;·&nbsp;
    <a href="{{ url('/fr/terms') }}">CGU</a> &nbsp;·&nbsp;
    <a href="{{ url('/fr/privacy') }}">Confidentialité</a>
  </div>

</div>

<script>
function otpApp() {
  return {
    digits:      ['','','','','',''],
    activeIndex: 0,
    timeLeft:    120,
    timer:       null,
    resending:   false,
    submitting:  false,
    resendMsg:   '',
    resendOk:    true,

    startTimer() {
      clearInterval(this.timer);
      this.timeLeft = 120;
      this.timer = setInterval(() => { if (this.timeLeft > 0) this.timeLeft--; }, 1000);
      this.$nextTick(() => {
        var ri = document.getElementById('otp-real');
        if (ri) ri.focus();
      });
    },

    fmtTime() {
      var m = Math.floor(this.timeLeft / 60);
      var s = this.timeLeft % 60;
      return (m < 10 ? '0'+m : m) + ':' + (s < 10 ? '0'+s : s);
    },

    press(n) {
      if (this.activeIndex >= 6 || this.submitting) return;
      this.digits[this.activeIndex] = n;
      this.digits = [...this.digits];
      this.activeIndex = Math.min(this.activeIndex + 1, 6);
      if (this.activeIndex === 6) {
        this.$nextTick(() => { if (!this.submitting) this.doSubmit(); });
      }
    },

    del() {
      if (this.submitting) return;
      var idx = this.activeIndex > 0 && this.digits[this.activeIndex] === ''
                ? this.activeIndex - 1 : this.activeIndex;
      if (idx < 0) return;
      this.digits[idx] = '';
      this.digits = [...this.digits];
      this.activeIndex = Math.max(idx, 0);
    },

    doSubmit() {
      if (this.digits.join('').length < 6 || this.submitting) return;
      this.submitting = true;
      document.getElementById('ld').classList.add('on');
      document.getElementById('otp-form').submit();
    },

    onRealInput(e) {
      var val = (e.target.value || '').replace(/\D/g,'').slice(0,6);
      e.target.value = val;
      for (var i = 0; i < 6; i++) this.digits[i] = val[i] || '';
      this.digits = [...this.digits];
      this.activeIndex = Math.min(val.length, 6);
      if (val.length === 6) this.$nextTick(() => this.doSubmit());
    },

    onRealKey(e) {
      if (e.key === 'Backspace') { e.preventDefault(); this.del(); }
    },

    async resend() {
      if (this.resending || this.timeLeft > 0) return;
      this.resending = true;
      this.resendMsg = '';
      try {
        var resp = await fetch('{{ route("otp.resend") }}', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
          },
        });
        var data = await resp.json();
        if (resp.ok) {
          this.resendOk  = true;
          this.resendMsg = data.message || '{{ __("auth.otp_resend_success") }}';
          this.digits    = ['','','','','',''];
          this.activeIndex = 0;
          this.startTimer();
        } else {
          this.resendOk  = false;
          this.resendMsg = data.error || '{{ __("auth.otp_send_failed") }}';
        }
      } catch(err) {
        this.resendOk  = false;
        this.resendMsg = '{{ __("auth.otp_send_failed") }}';
      }
      this.resending = false;
      var self = this;
      setTimeout(function(){ self.resendMsg = ''; }, 4000);
    },
  };
}

if ('serviceWorker' in navigator) {
  navigator.serviceWorker.register('/sw.js').catch(function(){});
}
</script>
</body>
</html>
