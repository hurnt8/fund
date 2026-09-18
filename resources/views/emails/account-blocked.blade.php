@php $locale = $user->locale ?? 'fr'; @endphp
<x-email-layout
    :title="__('auth.account_blocked_email_title', [], $locale)"
    subtitle="Aurenza Capital"
    accent="red"
    :footerNote="__('auth.account_blocked_email_footer', [], $locale)"
>

  <p class="greeting">
    {{ $user->name }},<br>
    {{ __('auth.account_blocked_email_intro', [], $locale) }}
  </p>

  <div class="alert alert-danger">
    <strong>{{ __('auth.account_blocked_email_reason_title', [], $locale) }}</strong>
    <p>{{ __('auth.account_blocked_email_reason_body', [], $locale) }}</p>
  </div>

  <div class="btn-wrap">
    <a href="{{ $unblockUrl }}" class="btn">
      {{ __('auth.account_blocked_email_btn', [], $locale) }}
    </a>
  </div>

  <p class="url-fallback">
    {{ __('auth.account_blocked_email_fallback', [], $locale) }}<br>
    <a href="{{ $unblockUrl }}">{{ $unblockUrl }}</a>
  </p>

  <div class="alert alert-warn">
    <strong>{{ __('auth.otp_email_notice_title', [], $locale) }}</strong>
    <p>{{ __('auth.account_blocked_email_notice', [], $locale) }}</p>
  </div>

</x-email-layout>
