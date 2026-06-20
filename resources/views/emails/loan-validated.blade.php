@component('mail::message')

@php
$texts = [
    'fr' => [
        'greeting'    => 'Madame / Monsieur ' . $loan->name . ',',
        'intro'       => 'Nous avons le plaisir de vous informer que votre demande de financement a été **validée** par CREDIXA INVESTI.',
        'summary'     => 'RÉSUMÉ DU FINANCEMENT',
        'ref'         => 'Référence dossier',
        'amount'      => 'Montant accordé',
        'duration'    => 'Durée',
        'months'      => 'mois',
        'monthly'     => 'Mensualité',
        'rate'        => 'Taux d\'intérêt',
        'fees'        => 'Frais administratifs',
        'action'      => 'ACTION REQUISE',
        'sign_instr'  => 'Veuillez **signer le contrat joint** et le retourner par email à l\'adresse officielle de CREDIXA INVESTI.',
        'account_note'=> 'Les coordonnées du compte de règlement et les modalités de versement vous seront communiquées par notre équipe suite à la réception de votre contrat signé.',
        'closing'     => 'Cordialement,',
        'team'        => 'L\'équipe CREDIXA INVESTI',
    ],
    'pl' => [
        'greeting'    => 'Szanowny/a ' . $loan->name . ',',
        'intro'       => 'Z przyjemnością informujemy, że Państwa wniosek o finansowanie został **zatwierdzony** przez CREDIXA INVESTI.',
        'summary'     => 'PODSUMOWANIE FINANSOWANIA',
        'ref'         => 'Numer referencyjny',
        'amount'      => 'Przyznana kwota',
        'duration'    => 'Okres',
        'months'      => 'miesięcy',
        'monthly'     => 'Miesięczna rata',
        'rate'        => 'Stopa procentowa',
        'fees'        => 'Opłaty administracyjne',
        'action'      => 'WYMAGANE DZIAŁANIE',
        'sign_instr'  => 'Prosimy o **podpisanie załączonej umowy** i odesłanie jej na oficjalny adres email CREDIXA INVESTI.',
        'account_note'=> 'Dane rachunku bankowego zostaną przekazane przez nasz zespół po otrzymaniu podpisanej umowy.',
        'closing'     => 'Z poważaniem,',
        'team'        => 'Zespół CREDIXA INVESTI',
    ],
    'en' => [
        'greeting'    => 'Dear ' . $loan->name . ',',
        'intro'       => 'We are pleased to inform you that your financing application has been **approved** by CREDIXA INVESTI.',
        'summary'     => 'FINANCING SUMMARY',
        'ref'         => 'File reference',
        'amount'      => 'Amount granted',
        'duration'    => 'Duration',
        'months'      => 'months',
        'monthly'     => 'Monthly payment',
        'rate'        => 'Interest rate',
        'fees'        => 'Administrative fees',
        'action'      => 'ACTION REQUIRED',
        'sign_instr'  => 'Please **sign the attached contract** and return it to the official CREDIXA INVESTI email address.',
        'account_note'=> 'Payment account details will be communicated by our team upon receipt of your signed contract.',
        'closing'     => 'Yours sincerely,',
        'team'        => 'The CREDIXA INVESTI team',
    ],
    'es' => [
        'greeting'    => 'Estimado/a ' . $loan->name . ',',
        'intro'       => 'Nos complace informarle que su solicitud de financiación ha sido **validada** por CREDIXA INVESTI.',
        'summary'     => 'RESUMEN DEL FINANCIAMIENTO',
        'ref'         => 'Referencia del expediente',
        'amount'      => 'Importe concedido',
        'duration'    => 'Duración',
        'months'      => 'meses',
        'monthly'     => 'Cuota mensual',
        'rate'        => 'Tipo de interés',
        'fees'        => 'Gastos administrativos',
        'action'      => 'ACCIÓN REQUERIDA',
        'sign_instr'  => 'Por favor, **firme el contrato adjunto** y devuélvalo al correo oficial de CREDIXA INVESTI.',
        'account_note'=> 'Los datos de la cuenta de pago le serán comunicados por nuestro equipo tras la recepción de su contrato firmado.',
        'closing'     => 'Atentamente,',
        'team'        => 'El equipo CREDIXA INVESTI',
    ],
];
$t = $texts[$locale] ?? $texts['fr'];
@endphp

# {{ $t['greeting'] }}

{{ $t['intro'] }}

---

**{{ $t['summary'] }}**

| | |
|---|---|
| **{{ $t['ref'] }}** | {{ $loan->reference }} |
| **{{ $t['amount'] }}** | {{ number_format($loan->amount, 2, ',', ' ') }} {{ $loan->currency }} |
| **{{ $t['duration'] }}** | {{ $loan->darly }} {{ $t['months'] }} |
| **{{ $t['monthly'] }}** | {{ number_format($loan->monthly_payment, 2, ',', ' ') }} {{ $loan->currency }} |
| **{{ $t['rate'] }}** | {{ $loan->interest_rate }} % |
@if($loan->admin_fees)
| **{{ $t['fees'] }}** | {{ number_format($loan->admin_fees, 2, ',', ' ') }} {{ $loan->currency }} |
@endif

---

> **{{ $t['action'] }}**
>
> {{ $t['sign_instr'] }}

{{ $t['account_note'] }}

---

{{ $t['closing'] }}

**{{ $t['team'] }}**

@endcomponent
