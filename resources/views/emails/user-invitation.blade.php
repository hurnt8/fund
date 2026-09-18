@php
$titles = [
    'fr' => 'Activez votre compte',
    'en' => 'Activate your account',
    'es' => 'Active su cuenta',
    'pl' => 'Aktywuj swoje konto',
];
$subs = [
    'fr' => 'Aurenza Capital — Espace client',
    'en' => 'Aurenza Capital — Client space',
    'es' => 'Aurenza Capital — Área de clientes',
    'pl' => 'Aurenza Capital — Obszar klienta',
];
$notices = [
    'fr' => 'Si vous n\'êtes pas à l\'origine de cette création de compte, vous pouvez ignorer cet email.',
    'en' => 'If you did not request this account creation, you can ignore this email.',
    'es' => 'Si usted no solicitó la creación de esta cuenta, puede ignorar este email.',
    'pl' => 'Jeśli nie prosiłeś/aś o utworzenie tego konta, możesz zignorować ten email.',
];
$title  = $titles[$locale]  ?? $titles['fr'];
$sub    = $subs[$locale]    ?? $subs['fr'];
$notice = $notices[$locale] ?? $notices['fr'];
@endphp

<x-email-layout
    :title="$title"
    :subtitle="$sub"
    accent="brand"
    :footerNote="$notice"
>

  <p class="greeting">{{ $greeting }}</p>

  <p class="body-text">{{ $resolved['{INTRO_CORPS}'] }}</p>

  <p class="body-text">{{ $resolved['{CORPS_ACTION}'] }}</p>

  <div class="btn-wrap">
    <a href="{{ $activationUrl }}" class="btn">{{ $btnLabel }}</a>
  </div>

  <p class="url-fallback">
    @php
    $fallbacks = ['fr'=>'Si le bouton ne fonctionne pas, copiez ce lien :','en'=>'If the button does not work, copy this link:','es'=>'Si el botón no funciona, copie este enlace:','pl'=>'Jeśli przycisk nie działa, skopiuj ten link:'];
    @endphp
    {{ $fallbacks[$locale] ?? $fallbacks['fr'] }}<br>
    <a href="{{ $activationUrl }}">{{ $activationUrl }}</a>
  </p>

  <div class="alert alert-warn">
    <p>{{ $resolved['{NOTICE_PERSONNEL}'] }}</p>
  </div>

  <p class="closing">
    {{ $resolved['{FORMULE_POLITESSE}'] }},<br>
    <strong>{{ $resolved['{EQUIPE}'] }}</strong>
  </p>

</x-email-layout>
