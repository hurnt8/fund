<x-mail::message>
# {{ __('message.loan_confirm_greeting', ['name' => $data['name']]) }}

{{ __('message.loan_confirm_body', [
    'amount'   => number_format($data['amount'], 0, ',', ' '),
    'currency' => $data['currency'] ?? 'EUR',
    'duration' => $data['darly'],
]) }}

---

**{{ __('message.loan_conditions_title') }}**

{{ __('message.loan_conditions_text') }}

---

{{ __('message.loan_complete_intro') }}

<x-mail::button :url="$data['complete_url']" color="primary">
{{ __('message.loan_complete_btn') }}
</x-mail::button>

{{ __('message.loan_confirm_footer') }}

**{{ __('message.loan_confirm_signature') }}**

<x-mail::subcopy>
{{ __('message.loan_confirm_noreply') }}
</x-mail::subcopy>
</x-mail::message>
