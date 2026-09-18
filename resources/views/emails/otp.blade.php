@php $locale = $user->locale ?? 'fr'; @endphp
<x-email-layout
    :title="__('auth.otp_email_title', [], $locale)"
    subtitle="Aurenza Capital"
    accent="brand"
    :footerNote="__('auth.otp_email_footer', [], $locale)"
>

  <p class="greeting">
    {{ $user->name }},<br>
    {{ __('auth.otp_email_intro', [], $locale) }}
  </p>

  <p class="body-text" style="font-size:.77rem;font-weight:700;text-transform:uppercase;letter-spacing:.09em;color:rgba(240,245,255,.38);margin-bottom:.5rem">
    {{ __('auth.otp_email_code_label', [], $locale) }}
  </p>

  <div class="code-box">
    <div class="code-digits">{{ $otp }}</div>
    <div class="code-expiry">⏱ {{ __('auth.otp_email_expiry', [], $locale) }}</div>
  </div>

  <div class="alert alert-warn">
    <strong>{{ __('auth.otp_email_notice_title', [], $locale) }}</strong>
    <p>{{ __('auth.otp_email_notice_body', [], $locale) }}</p>
  </div>

</x-email-layout>
