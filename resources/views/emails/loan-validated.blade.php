@php
$texts = [
    'fr' => [
        'title'        => 'Demande N°'.$loan->reference.' validée',
        'sub'          => 'Financement accordé',
        'greeting'     => 'Madame / Monsieur '.$loan->name.',',
        'intro'        => 'Nous avons le plaisir de vous informer que votre demande de financement a été <strong>validée</strong> par CREDIXA INVESTI.',
        'summary'      => 'RÉSUMÉ DU FINANCEMENT',
        'lbl_ref'      => 'Référence dossier',
        'lbl_amount'   => 'Montant accordé',
        'lbl_duration' => 'Durée',
        'lbl_months'   => 'mois',
        'lbl_monthly'  => 'Mensualité',
        'lbl_rate'     => 'Taux d\'intérêt',
        'lbl_fees'     => 'Frais administratifs',
        'action_title' => 'ACTION REQUISE',
        'action_body'  => 'Veuillez <strong>signer le contrat joint</strong> et le retourner par email à l\'adresse officielle de CREDIXA INVESTI.',
        'note'         => 'Les coordonnées du compte de règlement et les modalités de versement vous seront communiquées par notre équipe suite à la réception de votre contrat signé.',
        'closing'      => 'Cordialement,',
        'team'         => 'L\'équipe CREDIXA INVESTI',
    ],
    'en' => [
        'title'        => 'Application N°'.$loan->reference.' approved',
        'sub'          => 'Financing granted',
        'greeting'     => 'Dear '.$loan->name.',',
        'intro'        => 'We are pleased to inform you that your financing application has been <strong>approved</strong> by CREDIXA INVESTI.',
        'summary'      => 'FINANCING SUMMARY',
        'lbl_ref'      => 'File reference',
        'lbl_amount'   => 'Amount granted',
        'lbl_duration' => 'Duration',
        'lbl_months'   => 'months',
        'lbl_monthly'  => 'Monthly payment',
        'lbl_rate'     => 'Interest rate',
        'lbl_fees'     => 'Administrative fees',
        'action_title' => 'ACTION REQUIRED',
        'action_body'  => 'Please <strong>sign the attached contract</strong> and return it to the official CREDIXA INVESTI email address.',
        'note'         => 'Payment account details will be communicated by our team upon receipt of your signed contract.',
        'closing'      => 'Yours sincerely,',
        'team'         => 'The CREDIXA INVESTI team',
    ],
    'es' => [
        'title'        => 'Solicitud N°'.$loan->reference.' validada',
        'sub'          => 'Financiación concedida',
        'greeting'     => 'Estimado/a '.$loan->name.',',
        'intro'        => 'Nos complace informarle que su solicitud de financiación ha sido <strong>validada</strong> por CREDIXA INVESTI.',
        'summary'      => 'RESUMEN DEL FINANCIAMIENTO',
        'lbl_ref'      => 'Referencia del expediente',
        'lbl_amount'   => 'Importe concedido',
        'lbl_duration' => 'Duración',
        'lbl_months'   => 'meses',
        'lbl_monthly'  => 'Cuota mensual',
        'lbl_rate'     => 'Tipo de interés',
        'lbl_fees'     => 'Gastos administrativos',
        'action_title' => 'ACCIÓN REQUERIDA',
        'action_body'  => 'Por favor, <strong>firme el contrato adjunto</strong> y devuélvalo al correo oficial de CREDIXA INVESTI.',
        'note'         => 'Los datos de la cuenta de pago le serán comunicados por nuestro equipo tras la recepción de su contrato firmado.',
        'closing'      => 'Atentamente,',
        'team'         => 'El equipo CREDIXA INVESTI',
    ],
    'pl' => [
        'title'        => 'Wniosek nr '.$loan->reference.' zatwierdzony',
        'sub'          => 'Finansowanie przyznane',
        'greeting'     => 'Szanowny/a '.$loan->name.',',
        'intro'        => 'Z przyjemnością informujemy, że Państwa wniosek o finansowanie został <strong>zatwierdzony</strong> przez CREDIXA INVESTI.',
        'summary'      => 'PODSUMOWANIE FINANSOWANIA',
        'lbl_ref'      => 'Numer referencyjny',
        'lbl_amount'   => 'Przyznana kwota',
        'lbl_duration' => 'Okres',
        'lbl_months'   => 'miesięcy',
        'lbl_monthly'  => 'Miesięczna rata',
        'lbl_rate'     => 'Stopa procentowa',
        'lbl_fees'     => 'Opłaty administracyjne',
        'action_title' => 'WYMAGANE DZIAŁANIE',
        'action_body'  => 'Prosimy o <strong>podpisanie załączonej umowy</strong> i odesłanie jej na oficjalny adres email CREDIXA INVESTI.',
        'note'         => 'Dane rachunku bankowego zostaną przekazane przez nasz zespół po otrzymaniu podpisanej umowy.',
        'closing'      => 'Z poważaniem,',
        'team'         => 'Zespół CREDIXA INVESTI',
    ],
];
$t = $texts[$locale] ?? $texts['fr'];
@endphp

<x-email-layout
    :title="$t['title']"
    :subtitle="$t['sub']"
    accent="green"
>

  <p class="greeting">{{ $t['greeting'] }}</p>

  <p class="body-text">{!! $t['intro'] !!}</p>

  <div class="panel">
    <div class="panel-row">
      <span class="panel-lbl">{{ $t['lbl_ref'] }}</span>
      <span class="panel-val">{{ $loan->reference }}</span>
    </div>
    <div class="panel-row">
      <span class="panel-lbl">{{ $t['lbl_amount'] }}</span>
      <span class="panel-val accent">{{ number_format($loan->amount, 2, ',', ' ') }} {{ $loan->currency }}</span>
    </div>
    <div class="panel-row">
      <span class="panel-lbl">{{ $t['lbl_duration'] }}</span>
      <span class="panel-val">{{ $loan->darly }} {{ $t['lbl_months'] }}</span>
    </div>
    <div class="panel-row">
      <span class="panel-lbl">{{ $t['lbl_monthly'] }}</span>
      <span class="panel-val">{{ number_format($loan->monthly_payment, 2, ',', ' ') }} {{ $loan->currency }}</span>
    </div>
    <div class="panel-row">
      <span class="panel-lbl">{{ $t['lbl_rate'] }}</span>
      <span class="panel-val">{{ $loan->interest_rate }} %</span>
    </div>
    @if($loan->admin_fees)
    <div class="panel-row">
      <span class="panel-lbl">{{ $t['lbl_fees'] }}</span>
      <span class="panel-val">{{ number_format($loan->admin_fees, 2, ',', ' ') }} {{ $loan->currency }}</span>
    </div>
    @endif
  </div>

  <div class="alert alert-info">
    <strong>{{ $t['action_title'] }}</strong>
    <p>{!! $t['action_body'] !!}</p>
  </div>

  <p class="body-text">{{ $t['note'] }}</p>

  <p class="closing">
    {{ $t['closing'] }}<br>
    <strong>{{ $t['team'] }}</strong>
  </p>

</x-email-layout>
