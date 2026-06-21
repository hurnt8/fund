@component('mail::message')

@php
$ref    = $transfer->reference;
$amount = number_format($transfer->amount, 2, ',', ' ') . ' ' . $transfer->currency;
$bene   = $transfer->beneficiary_name;
@endphp

@if($action === 'approved')
# Bonjour {{ $transfer->user->name }},

Votre virement a été **validé** et traité avec succès.

@component('mail::panel')
**Référence :** {{ $ref }}
**Montant :** {{ $amount }}
**Bénéficiaire :** {{ $bene }}
@if($transfer->beneficiary_iban)
**IBAN :** {{ $transfer->beneficiary_iban }}
@endif
@if($transfer->admin_note)
**Note :** {{ $transfer->admin_note }}
@endif
@endcomponent

Le montant a été débité de votre compte. Si vous avez des questions, contactez votre conseiller.

@elseif($action === 'rejected')
# Bonjour {{ $transfer->user->name }},

Nous avons le regret de vous informer que votre virement a été **rejeté**.

@component('mail::panel')
**Référence :** {{ $ref }}
**Montant :** {{ $amount }}
**Bénéficiaire :** {{ $bene }}
@if($transfer->admin_note)
**Motif :** {{ $transfer->admin_note }}
@endif
@endcomponent

Le montant de **{{ $amount }}** a été **recrédité** sur votre compte. Si vous souhaitez soumettre à nouveau ce virement ou obtenir plus d'informations, contactez votre conseiller.

@elseif($action === 'fee_required')
# Bonjour {{ $transfer->user->name }},

Votre virement est en attente de règlement de **frais de traitement**.

@component('mail::panel')
**Référence virement :** {{ $ref }}
**Montant virement :** {{ $amount }}
**Bénéficiaire :** {{ $bene }}
@if($transfer->invoice)
**Frais à régler :** {{ number_format($transfer->invoice->total, 2, ',', ' ') }} {{ $transfer->invoice->currency }}
**Référence facture :** {{ $transfer->invoice->reference }}
@if($transfer->invoice->due_date)
**Échéance :** {{ $transfer->invoice->due_date->format('d/m/Y') }}
@endif
@endif
@endcomponent

Connectez-vous à votre espace client pour consulter la facture. Une fois les frais réglés, votre virement sera traité.

@endif

Cordialement,

**L'équipe CREDIXA INVESTI**

@endcomponent
