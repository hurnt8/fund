@php
$gender = $loan->client?->gender ?? 'N';

$greetings = [
    'fr' => [
        'M' => 'Monsieur '.$loan->name.',',
        'F' => 'Madame '.$loan->name.',',
        'N' => 'Madame, Monsieur '.$loan->name.',',
    ],
    'en' => [
        'M' => 'Dear Mr. '.$loan->name.',',
        'F' => 'Dear Ms. '.$loan->name.',',
        'N' => 'Dear '.$loan->name.',',
    ],
    'es' => [
        'M' => 'Estimado Sr. '.$loan->name.',',
        'F' => 'Estimada Sra. '.$loan->name.',',
        'N' => 'Estimado/a '.$loan->name.',',
    ],
    'pl' => [
        'M' => 'Szanowny Panie '.$loan->name.',',
        'F' => 'Szanowna Pani '.$loan->name.',',
        'N' => 'Szanowny/a Panie/Pani '.$loan->name.',',
    ],
];

$intros = [
    'fr' => [
        'M' => 'Nous avons le plaisir de vous informer que votre demande de financement a été <strong>validée</strong> par AURENZA CAPITAL INVESTI.',
        'F' => 'Nous avons le plaisir de vous informer que votre demande de financement a été <strong>validée</strong> par AURENZA CAPITAL INVESTI.',
        'N' => 'Nous avons le plaisir de vous informer que votre demande de financement a été <strong>validée</strong> par AURENZA CAPITAL INVESTI.',
    ],
    'en' => [
        'M' => 'We are pleased to inform you that your financing application has been <strong>approved</strong> by AURENZA CAPITAL INVESTI.',
        'F' => 'We are pleased to inform you that your financing application has been <strong>approved</strong> by AURENZA CAPITAL INVESTI.',
        'N' => 'We are pleased to inform you that your financing application has been <strong>approved</strong> by AURENZA CAPITAL INVESTI.',
    ],
    'es' => [
        'M' => 'Nos complace informarle que su solicitud de financiación ha sido <strong>validada</strong> por AURENZA CAPITAL INVESTI.',
        'F' => 'Nos complace informarla que su solicitud de financiación ha sido <strong>validada</strong> por AURENZA CAPITAL INVESTI.',
        'N' => 'Nos complace informarle/la que su solicitud de financiación ha sido <strong>validada</strong> por AURENZA CAPITAL INVESTI.',
    ],
    'pl' => [
        'M' => 'Z przyjemnością informujemy, że Pana wniosek o finansowanie został <strong>zatwierdzony</strong> przez AURENZA CAPITAL INVESTI.',
        'F' => 'Z przyjemnością informujemy, że Pani wniosek o finansowanie został <strong>zatwierdzony</strong> przez AURENZA CAPITAL INVESTI.',
        'N' => 'Z przyjemnością informujemy, że Państwa wniosek o finansowanie został <strong>zatwierdzony</strong> przez AURENZA CAPITAL INVESTI.',
    ],
];

$texts = [
    'fr' => [
        'title'         => 'Demande N°'.$loan->reference.' validée',
        'sub'           => 'Financement accordé',
        'greeting'      => $greetings['fr'][$gender],
        'intro'         => $intros['fr'][$gender],
        'summary'       => 'RÉSUMÉ DU FINANCEMENT',
        'lbl_ref'       => 'Référence dossier',
        'lbl_amount'    => 'Montant accordé',
        'lbl_duration'  => 'Durée',
        'lbl_months'    => 'mois',
        'lbl_monthly'   => 'Mensualité',
        'lbl_rate'      => 'Taux d\'intérêt',
        'lbl_fees'      => 'Frais administratifs',
        'action_title'  => 'ACTION REQUISE',
        'attachments'   => 'Vous trouverez en pièces jointes de ce message :',
        'attach_contract' => 'Votre contrat de financement (PDF)',
        'attach_table'    => 'Le tableau d\'amortissement (PDF)',
        'action_body'   => 'Veuillez <strong>signer le contrat</strong> et le retourner par email à :',
        'action_email'  => 'contact@aurenzacapital.com',
        'action_subject'=> 'en précisant en objet : <strong>Contrat signé — N°'.$loan->reference.' — '.$loan->name.'</strong>',
        'note'          => 'Les coordonnées du compte de règlement et les modalités de versement vous seront communiquées par notre équipe suite à la réception de votre contrat signé.',
        'closing'       => 'Cordialement,',
        'team'          => 'L\'équipe AURENZA CAPITAL INVESTI',
    ],
    'en' => [
        'title'         => 'Application N°'.$loan->reference.' approved',
        'sub'           => 'Financing granted',
        'greeting'      => $greetings['en'][$gender],
        'intro'         => $intros['en'][$gender],
        'summary'       => 'FINANCING SUMMARY',
        'lbl_ref'       => 'File reference',
        'lbl_amount'    => 'Amount granted',
        'lbl_duration'  => 'Duration',
        'lbl_months'    => 'months',
        'lbl_monthly'   => 'Monthly payment',
        'lbl_rate'      => 'Interest rate',
        'lbl_fees'      => 'Administrative fees',
        'action_title'  => 'ACTION REQUIRED',
        'attachments'   => 'Please find enclosed in this email:',
        'attach_contract' => 'Your financing contract (PDF)',
        'attach_table'    => 'The amortization schedule (PDF)',
        'action_body'   => 'Please <strong>sign the contract</strong> and return it by email to:',
        'action_email'  => 'contact@aurenzacapital.com',
        'action_subject'=> 'using the following subject: <strong>Signed contract — N°'.$loan->reference.' — '.$loan->name.'</strong>',
        'note'          => 'Payment account details and disbursement terms will be communicated by our team upon receipt of your signed contract.',
        'closing'       => 'Yours sincerely,',
        'team'          => 'The AURENZA CAPITAL INVESTI team',
    ],
    'es' => [
        'title'         => 'Solicitud N°'.$loan->reference.' validada',
        'sub'           => 'Financiación concedida',
        'greeting'      => $greetings['es'][$gender],
        'intro'         => $intros['es'][$gender],
        'summary'       => 'RESUMEN DEL FINANCIAMIENTO',
        'lbl_ref'       => 'Referencia del expediente',
        'lbl_amount'    => 'Importe concedido',
        'lbl_duration'  => 'Duración',
        'lbl_months'    => 'meses',
        'lbl_monthly'   => 'Cuota mensual',
        'lbl_rate'      => 'Tipo de interés',
        'lbl_fees'      => 'Gastos administrativos',
        'action_title'  => 'ACCIÓN REQUERIDA',
        'attachments'   => 'Encontrará adjuntos en este mensaje:',
        'attach_contract' => 'Su contrato de financiación (PDF)',
        'attach_table'    => 'El cuadro de amortización (PDF)',
        'action_body'   => 'Por favor, <strong>firme el contrato</strong> y devuélvalo por correo electrónico a:',
        'action_email'  => 'contact@aurenzacapital.com',
        'action_subject'=> 'indicando en el asunto: <strong>Contrato firmado — N°'.$loan->reference.' — '.$loan->name.'</strong>',
        'note'          => 'Los datos de la cuenta de pago y las modalidades de desembolso le serán comunicados por nuestro equipo tras la recepción de su contrato firmado.',
        'closing'       => 'Atentamente,',
        'team'          => 'El equipo AURENZA CAPITAL INVESTI',
    ],
    'pl' => [
        'title'         => 'Wniosek nr '.$loan->reference.' zatwierdzony',
        'sub'           => 'Finansowanie przyznane',
        'greeting'      => $greetings['pl'][$gender],
        'intro'         => $intros['pl'][$gender],
        'summary'       => 'PODSUMOWANIE FINANSOWANIA',
        'lbl_ref'       => 'Numer referencyjny',
        'lbl_amount'    => 'Przyznana kwota',
        'lbl_duration'  => 'Okres',
        'lbl_months'    => 'miesięcy',
        'lbl_monthly'   => 'Miesięczna rata',
        'lbl_rate'      => 'Stopa procentowa',
        'lbl_fees'      => 'Opłaty administracyjne',
        'action_title'  => 'WYMAGANE DZIALANIE',
        'attachments'   => 'W załaczeniu do tej wiadomosci znajda Panstwo:',
        'attach_contract' => 'Umowe finansowania (PDF)',
        'attach_table'    => 'Harmonogram splat (PDF)',
        'action_body'   => 'Prosimy o <strong>podpisanie umowy</strong> i odesl anie jej na adres e-mail:',
        'action_email'  => 'contact@aurenzacapital.com',
        'action_subject'=> 'podajac w temacie: <strong>Podpisana umowa — nr '.$loan->reference.' — '.$loan->name.'</strong>',
        'note'          => 'Dane rachunku bankowego oraz warunki wyplaty zostana przekazane przez nasz zespol po otrzymaniu podpisanej umowy.',
        'closing'       => 'Z powazaniem,',
        'team'          => 'Zespol AURENZA CAPITAL INVESTI',
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
    <p style="margin-top:.5rem">{!! $t['attachments'] !!}</p>
    <ul style="margin:.375rem 0 .75rem 1.25rem;padding:0">
      <li>{!! $t['attach_contract'] !!}</li>
      <li>{!! $t['attach_table'] !!}</li>
    </ul>
    <p>{!! $t['action_body'] !!}</p>
    <p style="margin:.25rem 0;font-size:1rem"><strong>{{ $t['action_email'] }}</strong></p>
    <p style="margin-top:.375rem;font-size:.875rem">{!! $t['action_subject'] !!}</p>
  </div>

  <p class="body-text">{{ $t['note'] }}</p>

  <p class="closing">
    {{ $t['closing'] }}<br>
    <strong>{{ $t['team'] }}</strong>
  </p>

</x-email-layout>
