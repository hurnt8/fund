@component('mail::message')

# Bonjour {{ $invoice->client->name }},

Vous avez reçu une nouvelle facture de la part de **CREDIXA INVESTI**.

---

**DÉTAILS DE LA FACTURE**

| | |
|---|---|
| **Référence** | {{ $invoice->reference }} |
| **Date d'émission** | {{ $invoice->issue_date->format('d/m/Y') }} |
@if($invoice->due_date)
| **Date d'échéance** | {{ $invoice->due_date->format('d/m/Y') }} |
@endif
| **Montant total** | **{{ number_format($invoice->total, 2, ',', ' ') }} {{ $invoice->currency }}** |

---

@if(count($invoice->items ?? []))
**DÉTAIL DES PRESTATIONS**

| Description | Qté | Prix unit. | Total |
|---|---|---|---|
@foreach($invoice->items as $item)
| {{ $item['description'] ?? '—' }} | {{ $item['quantity'] ?? 1 }} | {{ number_format((float)($item['unit_price'] ?? 0), 2, ',', ' ') }} | {{ number_format((float)($item['total'] ?? 0), 2, ',', ' ') }} {{ $invoice->currency }} |
@endforeach

@if($invoice->tax_rate > 0)
| | | **Sous-total** | {{ number_format($invoice->subtotal, 2, ',', ' ') }} {{ $invoice->currency }} |
| | | **TVA ({{ $invoice->tax_rate }}%)** | {{ number_format($invoice->tax_amount, 2, ',', ' ') }} {{ $invoice->currency }} |
| | | **Total TTC** | **{{ number_format($invoice->total, 2, ',', ' ') }} {{ $invoice->currency }}** |
@endif

---
@endif

@if($invoice->description)
{{ $invoice->description }}

---
@endif

@if($invoice->note)
> **Note :** {{ $invoice->note }}
@endif

Pour toute question concernant cette facture, veuillez contacter votre conseiller CREDIXA INVESTI.

Cordialement,

**L'équipe CREDIXA INVESTI**

@endcomponent
