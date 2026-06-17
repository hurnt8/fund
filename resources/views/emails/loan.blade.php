<x-mail::message>
# {{ __('message.loan_admin_subject') }}

{{ __('message.loan_admin_intro') }}

---

**{{ __('loan.label_name') }} :** {{ $data['name'] }}

**{{ __('loan.label_email') }} :** {{ $data['email'] }}

**{{ __('loan.label_phone') }} :** {{ $data['phone'] }}

**{{ __('loan.label_amount') }} :** {{ number_format($data['amount'], 0, ',', ' ') }} {{ $data['currency'] ?? 'EUR' }}

**{{ __('loan.label_darly') }} :** {{ $data['darly'] }} {{ __('message.months') }}

**{{ __('contact.subject') }} :** {{ $data['subject'] }}

@if (!empty($data['objet']))
---

**{{ __('loan.label_objet') }} :** {{ $data['objet'] }}
@endif

</x-mail::message>
