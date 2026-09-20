@component('mail::message')

@php
$texts = [
    'fr' => [
        'greeting'     => 'Madame / Monsieur ' . $loan->name . ',',
        'intro'        => 'Nous avons bien enregistré votre dossier de financement auprès de **AURENZA CAPITAL**. Vous trouverez ci-joint votre contrat de prêt ainsi que le tableau d\'amortissement détaillant vos remboursements.',
        'summary'      => 'RÉSUMÉ DE VOTRE FINANCEMENT',
        'ref'          => 'Référence dossier',
        'amount'       => 'Montant accordé',
        'duration'     => 'Durée',
        'months'       => 'mois',
        'monthly'      => 'Mensualité',
        'rate'         => 'Taux d\'intérêt',
        'fees'         => 'Frais administratifs',
        'start'        => 'Première échéance',
        'docs_title'   => 'DOCUMENTS JOINTS',
        'doc_contract' => '📄 **Contrat_{{ ref }}.pdf** — Votre contrat de prêt à lire attentivement',
        'doc_amort'    => '📊 **Tableau_Amortissement_{{ ref }}.pdf** — Échéancier mensuel complet',
        'next_title'   => 'PROCHAINES ÉTAPES',
        'next_1'       => '1. Lisez attentivement le contrat joint.',
        'next_2'       => '2. Signez-le et renvoyez-le à notre équipe par email.',
        'next_3'       => '3. Une fois reçu, les coordonnées bancaires vous seront communiquées pour le versement des fonds.',
        'closing'      => 'Nous restons à votre disposition pour toute question.',
        'team'         => 'L\'équipe AURENZA CAPITAL',
    ],
    'pl' => [
        'greeting'     => 'Szanowny/a ' . $loan->name . ',',
        'intro'        => 'Zarejestrowaliśmy Państwa wniosek o finansowanie w **AURENZA CAPITAL**. W załączeniu przesyłamy umowę pożyczki oraz harmonogram spłat.',
        'summary'      => 'PODSUMOWANIE FINANSOWANIA',
        'ref'          => 'Numer referencyjny',
        'amount'       => 'Przyznana kwota',
        'duration'     => 'Okres',
        'months'       => 'miesięcy',
        'monthly'      => 'Miesięczna rata',
        'rate'         => 'Stopa procentowa',
        'fees'         => 'Opłaty administracyjne',
        'start'        => 'Pierwsza rata',
        'docs_title'   => 'ZAŁĄCZONE DOKUMENTY',
        'doc_contract' => '📄 **Umowa_{{ ref }}.pdf** — Umowa pożyczki do dokładnego przeczytania',
        'doc_amort'    => '📊 **Harmonogram_{{ ref }}.pdf** — Pełny miesięczny harmonogram spłat',
        'next_title'   => 'KOLEJNE KROKI',
        'next_1'       => '1. Prosimy o dokładne zapoznanie się z załączoną umową.',
        'next_2'       => '2. Proszę ją podpisać i odesłać do naszego zespołu.',
        'next_3'       => '3. Po otrzymaniu podpisanej umowy przekażemy dane bankowe do wypłaty środków.',
        'closing'      => 'Pozostajemy do Państwa dyspozycji w razie jakichkolwiek pytań.',
        'team'         => 'Zespół AURENZA CAPITAL',
    ],
    'en' => [
        'greeting'     => 'Dear ' . $loan->name . ',',
        'intro'        => 'Your financing file has been registered with **AURENZA CAPITAL**. Please find attached your loan agreement and the amortization schedule detailing your monthly repayments.',
        'summary'      => 'YOUR FINANCING SUMMARY',
        'ref'          => 'File reference',
        'amount'       => 'Amount granted',
        'duration'     => 'Duration',
        'months'       => 'months',
        'monthly'      => 'Monthly payment',
        'rate'         => 'Interest rate',
        'fees'         => 'Administrative fees',
        'start'        => 'First payment date',
        'docs_title'   => 'ATTACHED DOCUMENTS',
        'doc_contract' => '📄 **Contract_{{ ref }}.pdf** — Your loan agreement, please read carefully',
        'doc_amort'    => '📊 **AmortizationSchedule_{{ ref }}.pdf** — Complete monthly payment schedule',
        'next_title'   => 'NEXT STEPS',
        'next_1'       => '1. Read the attached contract carefully.',
        'next_2'       => '2. Sign it and return it to our team by email.',
        'next_3'       => '3. Once received, our team will send you the bank details for fund transfer.',
        'closing'      => 'We remain at your disposal for any questions.',
        'team'         => 'The AURENZA CAPITAL team',
    ],
    'es' => [
        'greeting'     => 'Estimado/a ' . $loan->name . ',',
        'intro'        => 'Hemos registrado su expediente de financiación en **AURENZA CAPITAL**. Adjuntamos su contrato de préstamo y el cuadro de amortización con el detalle de sus pagos mensuales.',
        'summary'      => 'RESUMEN DE SU FINANCIAMIENTO',
        'ref'          => 'Referencia del expediente',
        'amount'       => 'Importe concedido',
        'duration'     => 'Duración',
        'months'       => 'meses',
        'monthly'      => 'Cuota mensual',
        'rate'         => 'Tipo de interés',
        'fees'         => 'Gastos administrativos',
        'start'        => 'Primera cuota',
        'docs_title'   => 'DOCUMENTOS ADJUNTOS',
        'doc_contract' => '📄 **Contrato_{{ ref }}.pdf** — Su contrato de préstamo, léalo detenidamente',
        'doc_amort'    => '📊 **CuadroAmortizacion_{{ ref }}.pdf** — Calendario mensual completo de pagos',
        'next_title'   => 'PRÓXIMOS PASOS',
        'next_1'       => '1. Lea detenidamente el contrato adjunto.',
        'next_2'       => '2. Fírmelo y envíelo a nuestro equipo por correo electrónico.',
        'next_3'       => '3. Una vez recibido, le comunicaremos los datos bancarios para la transferencia de fondos.',
        'closing'      => 'Quedamos a su disposición para cualquier consulta.',
        'team'         => 'El equipo AURENZA CAPITAL',
    ],
];
$t   = $texts[$locale] ?? $texts['fr'];
$ref = $loan->reference;
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
@if($loan->start_date)
| **{{ $t['start'] }}** | {{ $loan->start_date->format('d/m/Y') }} |
@endif

---

**{{ $t['docs_title'] }}**

{!! str_replace('{{ ref }}', $ref, $t['doc_contract']) !!}

{!! str_replace('{{ ref }}', $ref, $t['doc_amort']) !!}

---

> **{{ $t['next_title'] }}**
>
> {{ $t['next_1'] }}
>
> {{ $t['next_2'] }}
>
> {{ $t['next_3'] }}

{{ $t['closing'] }}

**{{ $t['team'] }}**

@endcomponent
