<x-mail::message>
# {{ __('message.docs_confirm_greeting', ['name' => $data['name']]) }}

{{ __('message.docs_confirm_body') }}

<x-mail::panel>
**{{ __('message.docs_name') }} :** {{ $data['name'] }}

**{{ __('message.docs_doc_type') }} :** {{ __('message.doc_type_' . $data['doc_type']) }}

**{{ __('message.docs_address') }} :**
{{ $data['address'] }}
</x-mail::panel>

{{ __('message.docs_confirm_footer') }}

**{{ __('message.docs_confirm_signature') }}**

<x-mail::subcopy>
{{ __('message.loan_confirm_noreply') }}
</x-mail::subcopy>
</x-mail::message>
