<x-mail::message>
# {{ __('message.loan_confirm_greeting', ['name' => $data['name']]) }}

{{ __('message.loan_confirm_body', ['amount' => number_format($data['amount'], 0, ',', ' '), 'duration' => $data['darly']]) }}

{{ __('message.loan_confirm_footer') }}

**{{ __('message.loan_confirm_signature') }}**

<x-mail::subcopy>
{{ __('message.loan_confirm_noreply') }}
</x-mail::subcopy>
</x-mail::message>
