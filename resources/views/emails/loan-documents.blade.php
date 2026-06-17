<x-mail::message>
# {{ __('message.docs_subject') }}

{{ __('message.docs_intro') }}

---

**{{ __('message.docs_name') }} :** {{ $data['name'] }}

**{{ __('message.docs_email') }} :** {{ $data['email'] }}

**{{ __('message.docs_address') }} :**

{{ $data['address'] }}

---

**{{ __('message.docs_doc_type') }} :** {{ __('message.doc_type_' . $data['doc_type']) }}

**{{ __('message.docs_recto') }} :** ✅ ({{ __('message.docs_id_photo') }})

@if(count($attachments) > 1)
**{{ __('message.docs_verso') }} :** ✅ ({{ __('message.docs_id_photo') }})
@endif

</x-mail::message>
