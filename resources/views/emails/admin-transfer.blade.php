@component('mail::message')
# Nouveau virement en attente

**{{ $client->name }}** vient de soumettre un virement nécessitant votre validation.

@component('mail::table')
| Champ | Valeur |
|:------|:-------|
| Référence | {{ $transfer->reference }} |
| Montant | {{ $transfer->currency }} {{ number_format($transfer->amount, 2, ',', ' ') }} |
| Bénéficiaire | {{ $transfer->beneficiary_name }} |
| IBAN | {{ $transfer->beneficiary_iban }} |
| Soumis le | {{ $transfer->created_at->format('d/m/Y à H:i') }} |
@if($transfer->note)
| Note | {{ $transfer->note }} |
@endif
@endcomponent

@component('mail::button', ['url' => url('/admin/transfers'), 'color' => 'primary'])
Valider le virement
@endcomponent

Cordialement,<br>
**Credixa**
@endcomponent
