@php
/**
 * Page d'activation de compte — multilingue + sensible au genre
 * Langue & genre lus depuis $user->locale et $user->gender
 */
$locale = $user->locale ?? 'fr';
$gender = $user->gender ?? 'N';

$texts = [
    'fr' => [
        'title'       => 'Activation de compte — Credixa Invest',
        'greeting'    => [
            'M' => 'Cher Monsieur',
            'F' => 'Chère Madame',
            'N' => 'Bonjour',
        ],
        'subtitle'    => 'Définissez votre mot de passe pour activer votre accès',
        'info_title'  => 'Votre compte Credixa Invest',
        'info_body'   => 'a été créé par votre conseiller. Choisissez un mot de passe sécurisé pour accéder à votre espace personnel.',
        'email_label' => 'Adresse email',
        'pw_label'    => 'Nouveau mot de passe',
        'pw_ph'       => 'Minimum 8 caractères',
        'cpw_label'   => 'Confirmer le mot de passe',
        'cpw_ph'      => 'Répéter le mot de passe',
        'btn'         => 'Activer mon compte',
        'login_text'  => 'Vous avez déjà un compte ?',
        'login_link'  => 'Se connecter',
        'str_ph'      => 'Saisissez un mot de passe',
        'strengths'   => ['', 'Très faible', 'Faible', 'Moyen', 'Fort', 'Très fort'],
    ],
    'en' => [
        'title'       => 'Account Activation — Credixa Invest',
        'greeting'    => [
            'M' => 'Dear Mr.',
            'F' => 'Dear Ms.',
            'N' => 'Hello',
        ],
        'subtitle'    => 'Set your password to activate your account access',
        'info_title'  => 'Your Credixa Invest account',
        'info_body'   => 'was created by your advisor. Choose a secure password to access your personal space.',
        'email_label' => 'Email address',
        'pw_label'    => 'New password',
        'pw_ph'       => 'At least 8 characters',
        'cpw_label'   => 'Confirm password',
        'cpw_ph'      => 'Repeat password',
        'btn'         => 'Activate my account',
        'login_text'  => 'Already have an account?',
        'login_link'  => 'Sign in',
        'str_ph'      => 'Enter a password',
        'strengths'   => ['', 'Very weak', 'Weak', 'Fair', 'Strong', 'Very strong'],
    ],
    'es' => [
        'title'       => 'Activación de cuenta — Credixa Invest',
        'greeting'    => [
            'M' => 'Estimado Sr.',
            'F' => 'Estimada Sra.',
            'N' => 'Hola',
        ],
        'subtitle'    => 'Establezca su contraseña para activar su cuenta',
        'info_title'  => 'Su cuenta de Credixa Invest',
        'info_body'   => 'fue creada por su asesor. Elija una contraseña segura para acceder a su espacio personal.',
        'email_label' => 'Correo electrónico',
        'pw_label'    => 'Nueva contraseña',
        'pw_ph'       => 'Mínimo 8 caracteres',
        'cpw_label'   => 'Confirmar contraseña',
        'cpw_ph'      => 'Repetir contraseña',
        'btn'         => 'Activar mi cuenta',
        'login_text'  => '¿Ya tiene una cuenta?',
        'login_link'  => 'Iniciar sesión',
        'str_ph'      => 'Introduzca una contraseña',
        'strengths'   => ['', 'Muy débil', 'Débil', 'Regular', 'Fuerte', 'Muy fuerte'],
    ],
    'pl' => [
        'title'       => 'Aktywacja konta — Credixa Invest',
        'greeting'    => [
            'M' => 'Szanowny Panie',
            'F' => 'Szanowna Pani',
            'N' => 'Witaj',
        ],
        'subtitle'    => 'Ustaw hasło, aby aktywować dostęp do konta',
        'info_title'  => 'Twoje konto Credixa Invest',
        'info_body'   => 'zostało utworzone przez Twojego doradcę. Wybierz bezpieczne hasło, aby uzyskać dostęp do swojego osobistego obszaru.',
        'email_label' => 'Adres e-mail',
        'pw_label'    => 'Nowe hasło',
        'pw_ph'       => 'Minimum 8 znaków',
        'cpw_label'   => 'Potwierdź hasło',
        'cpw_ph'      => 'Powtórz hasło',
        'btn'         => 'Aktywuj moje konto',
        'login_text'  => 'Masz już konto?',
        'login_link'  => 'Zaloguj się',
        'str_ph'      => 'Wpisz hasło',
        'strengths'   => ['', 'Bardzo słabe', 'Słabe', 'Średnie', 'Silne', 'Bardzo silne'],
    ],
];

$t        = $texts[$locale]         ?? $texts['fr'];
$greeting = $t['greeting'][$gender] ?? $t['greeting']['N'];
$lastName = explode(' ', $user->name, 2)[1] ?? $user->name;
// Pour 'N', on affiche juste le prénom ; pour M/F on ajoute le nom de famille
$salutationName = ($gender === 'N') ? explode(' ', $user->name)[0] : $user->name;
@endphp
<!DOCTYPE html>
<html lang="{{ $locale }}">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $t['title'] }}</title>
<link rel="icon" href="{{ asset('assets/images/favicons/favicon.png') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('assets/vendors/fontawesome/css/all.min.css') }}">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Inter',sans-serif;background:#F1F4F9;color:#1A2332;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:1.5rem}
.card{background:#fff;border-radius:16px;box-shadow:0 4px 32px rgba(0,0,0,.1);width:100%;max-width:460px;overflow:hidden}
.card-top{background:linear-gradient(135deg,#0B1A2E 0%,#1a3a5c 100%);padding:2rem 2rem 1.875rem;text-align:center}
.card-top img{height:38px;display:block;margin:0 auto 1.375rem}
.avatar{width:58px;height:58px;border-radius:50%;background:linear-gradient(135deg,#C8A951,#A88830);display:flex;align-items:center;justify-content:center;font-size:1.375rem;font-weight:800;color:#0B1A2E;margin:0 auto 1rem;box-shadow:0 2px 14px rgba(200,169,81,.4)}
.card-top h1{font-size:1.0625rem;font-weight:800;color:#fff;margin-bottom:.375rem;line-height:1.35}
.card-top p{font-size:.78rem;color:rgba(255,255,255,.48);line-height:1.5}
.card-body{padding:1.875rem 2rem}
.info-box{display:flex;gap:.75rem;align-items:flex-start;background:#EFF6FF;border:1px solid #BFDBFE;border-radius:10px;padding:.875rem 1rem;margin-bottom:1.5rem}
.info-box i{color:#2563EB;font-size:.9rem;flex-shrink:0;margin-top:.15rem}
.info-box p{font-size:.775rem;color:#1E40AF;line-height:1.55}
.info-box strong{font-weight:700}
label{display:block;font-size:.78rem;font-weight:600;color:#0B1A2E;margin-bottom:.375rem}
.field{margin-bottom:1rem}
.input-wrap{position:relative}
.input-wrap input{width:100%;padding:.65rem 2.5rem .65rem .875rem;border:1.5px solid #E4E8F0;border-radius:9px;font-size:.8375rem;color:#1A2332;font-family:'Inter',sans-serif;transition:border-color .2s,box-shadow .2s;background:#fff;outline:none;appearance:none}
.input-wrap input:focus{border-color:#C8A951;box-shadow:0 0 0 3px rgba(200,169,81,.13)}
.input-wrap input[readonly]{background:#F8FAFF;color:#6B7280;cursor:not-allowed}
.eye-btn{position:absolute;right:.75rem;top:50%;transform:translateY(-50%);background:none;border:none;color:#9CA3AF;cursor:pointer;font-size:.78rem;padding:.25rem;display:flex;align-items:center}
.eye-btn:hover{color:#374151}
.strength-bar{height:4px;border-radius:99px;background:#E4E8F0;margin-top:.35rem;overflow:hidden}
.strength-fill{height:100%;border-radius:99px;transition:width .3s,background .3s;width:0%}
.strength-txt{font-size:.68rem;color:#9CA3AF;margin-top:.3rem;min-height:1em}
.err-box{display:flex;gap:.5rem;align-items:flex-start;background:#FEF2F2;border:1px solid #FECACA;border-radius:9px;padding:.75rem 1rem;margin-bottom:1.125rem;font-size:.78rem;color:#991B1B}
.btn{display:flex;align-items:center;justify-content:center;gap:.5rem;width:100%;padding:.75rem;background:#0B1A2E;color:#fff;border:none;border-radius:9px;font-size:.875rem;font-weight:700;font-family:'Inter',sans-serif;cursor:pointer;transition:background .2s;margin-top:1.375rem}
.btn:hover{background:#112240}
.btn:active{transform:scale(.99)}
.foot{text-align:center;font-size:.72rem;color:#9CA3AF;margin-top:1.125rem}
.foot a{color:#0B1A2E;font-weight:600;text-decoration:none}
.foot a:hover{text-decoration:underline}
@media(max-width:500px){.card-body{padding:1.375rem 1.25rem}.card-top{padding:1.5rem 1.25rem 1.375rem}}
</style>
</head>
<body>
<div class="card">

  {{-- En-tête --}}
  <div class="card-top">
    <img src="{{ asset('assets/images/logo new.png') }}" alt="Credixa Invest">
    <div class="avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
    <h1>{{ $greeting }}, {{ $salutationName }} !</h1>
    <p>{{ $t['subtitle'] }}</p>
  </div>

  {{-- Corps --}}
  <div class="card-body">

    <div class="info-box">
      <i class="fas fa-envelope-open-text"></i>
      <p>
        <strong>{{ $t['info_title'] }}</strong>
        {{ $t['info_body'] }}
      </p>
    </div>

    @if($errors->any())
    <div class="err-box">
      <i class="fas fa-exclamation-circle" style="margin-top:.1rem;flex-shrink:0"></i>
      <span>{{ $errors->first() }}</span>
    </div>
    @endif

    <form method="POST" action="{{ route('invitation.activate', $token) }}">
      @csrf

      {{-- Email (lecture seule) --}}
      <div class="field">
        <label>{{ $t['email_label'] }}</label>
        <div class="input-wrap">
          <input type="email" value="{{ $user->email }}" readonly>
          <span class="eye-btn" style="cursor:default"><i class="fas fa-lock"></i></span>
        </div>
      </div>

      {{-- Mot de passe --}}
      <div class="field">
        <label for="password">{{ $t['pw_label'] }}</label>
        <div class="input-wrap">
          <input type="password" id="password" name="password" required
                 placeholder="{{ $t['pw_ph'] }}" autocomplete="new-password"
                 oninput="checkStrength(this.value)">
          <button type="button" class="eye-btn" onclick="togglePw('password',this)">
            <i class="fas fa-eye"></i>
          </button>
        </div>
        <div class="strength-bar"><div class="strength-fill" id="sFill"></div></div>
        <div class="strength-txt" id="sTxt">{{ $t['str_ph'] }}</div>
      </div>

      {{-- Confirmation --}}
      <div class="field" style="margin-bottom:0">
        <label for="password_confirmation">{{ $t['cpw_label'] }}</label>
        <div class="input-wrap">
          <input type="password" id="password_confirmation" name="password_confirmation"
                 required placeholder="{{ $t['cpw_ph'] }}" autocomplete="new-password">
          <button type="button" class="eye-btn" onclick="togglePw('password_confirmation',this)">
            <i class="fas fa-eye"></i>
          </button>
        </div>
      </div>

      <button type="submit" class="btn">
        <i class="fas fa-unlock-alt"></i> {{ $t['btn'] }}
      </button>
    </form>

    <div class="foot">
      {{ $t['login_text'] }}
      <a href="{{ $user->type === 'staff' ? route('staff.login') : route('login') }}">
        {{ $t['login_link'] }}
      </a>
    </div>

  </div>
</div>

<script>
const strengths = @json($t['strengths']);
const strPh = @json($t['str_ph']);
const colors = ['#E4E8F0','#EF4444','#F97316','#EAB308','#22C55E','#16A34A'];
const widths  = ['0%','25%','50%','75%','90%','100%'];

function togglePw(id, btn) {
  const el   = document.getElementById(id);
  const icon = btn.querySelector('i');
  if (el.type === 'password') { el.type='text'; icon.className='fas fa-eye-slash'; }
  else                        { el.type='password'; icon.className='fas fa-eye'; }
}

function checkStrength(pw) {
  const fill = document.getElementById('sFill');
  const txt  = document.getElementById('sTxt');
  if (!pw) { fill.style.width='0%'; txt.textContent=strPh; txt.style.color='#9CA3AF'; return; }
  let s = 0;
  if (pw.length >= 8)           s++;
  if (pw.length >= 12)          s++;
  if (/[A-Z]/.test(pw))         s++;
  if (/[0-9]/.test(pw))         s++;
  if (/[^A-Za-z0-9]/.test(pw))  s++;
  s = Math.min(s, 5);
  fill.style.width      = widths[s];
  fill.style.background = colors[s];
  txt.textContent       = strengths[s] || strPh;
  txt.style.color       = s > 2 ? colors[s] : '#9CA3AF';
}
</script>
</body>
</html>
