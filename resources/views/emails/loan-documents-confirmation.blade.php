<x-mail::message>
# {{ __('message.docs_confirm_greeting', ['name' => $data['name']]) }}

{{ __('message.docs_confirm_body') }}

{{ __('message.docs_confirm_footer') }}

**{{ __('message.docs_confirm_signature') }}**

<x-mail::subcopy>
{{ __('message.loan_confirm_noreply') }}
</x-mail::subcopy>
</x-mail::message>
