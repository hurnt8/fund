@php
$gender = $loan->client?->gender ?? 'N';

$greetings = [
    'fr' => ['M' => 'Monsieur '.$loan->name.',',  'F' => 'Madame '.$loan->name.',',   'N' => 'Madame, Monsieur '.$loan->name.','],
    'en' => ['M' => 'Dear Mr. '.$loan->name.',',  'F' => 'Dear Ms. '.$loan->name.',', 'N' => 'Dear '.$loan->name.','],
    'es' => ['M' => 'Estimado Sr. '.$loan->name.',', 'F' => 'Estimada Sra. '.$loan->name.',', 'N' => 'Estimado/a '.$loan->name.','],
    'pl' => ['M' => 'Szanowny Panie '.$loan->name.',', 'F' => 'Szanowna Pani '.$loan->name.',', 'N' => 'Szanowny/a Panie/Pani '.$loan->name.','],
];

$texts = [
    'fr' => [
        'title'    => 'Attestation d\'assurance emprunteur — N°'.$loan->reference,
        'sub'      => 'Assurance CG-A340G',
        'greeting' => $greetings['fr'][$gender],
        'intro'    => 'Veuillez trouver ci-joint votre <strong>attestation d\'assurance emprunteur</strong> (référence <strong>'.$loan->reference.'</strong>) établie par AURENZA CAPITAL INVESTI dans le cadre de votre dossier de financement.',
        'summary'  => 'RÉCAPITULATIF DE VOTRE ASSURANCE',
        'lbl_ref'  => 'Référence dossier',
        'lbl_montant'  => 'Montant assuré',
        'lbl_duree'    => 'Durée',
        'lbl_months'   => 'mois',
        'lbl_frais'    => 'Frais d\'assurance',
        'lbl_fin'      => 'Date de fin d\'assurance',
        'attach_note'  => 'Votre attestation d\'assurance CG-A340G est jointe à cet email au format PDF.',
        'contact'      => 'Pour toute question relative à votre assurance, n\'hésitez pas à contacter votre conseiller.',
        'closing'      => 'Cordialement,',
        'team'         => 'L\'équipe AURENZA CAPITAL INVESTI',
    ],
    'en' => [
        'title'    => 'Borrower insurance certificate — N°'.$loan->reference,
        'sub'      => 'Insurance CG-A340G',
        'greeting' => $greetings['en'][$gender],
        'intro'    => 'Please find attached your <strong>borrower insurance certificate</strong> (reference <strong>'.$loan->reference.'</strong>) issued by AURENZA CAPITAL INVESTI in connection with your financing application.',
        'summary'  => 'YOUR INSURANCE SUMMARY',
        'lbl_ref'  => 'Dossier reference',
        'lbl_montant'  => 'Insured amount',
        'lbl_duree'    => 'Duration',
        'lbl_months'   => 'months',
        'lbl_frais'    => 'Insurance fees',
        'lbl_fin'      => 'Insurance end date',
        'attach_note'  => 'Your CG-A340G insurance certificate is attached to this email in PDF format.',
        'contact'      => 'For any questions regarding your insurance, please do not hesitate to contact your advisor.',
        'closing'      => 'Best regards,',
        'team'         => 'The AURENZA CAPITAL INVESTI Team',
    ],
    'es' => [
        'title'    => 'Certificado de seguro de prestatario — N°'.$loan->reference,
        'sub'      => 'Seguro CG-A340G',
        'greeting' => $greetings['es'][$gender],
        'intro'    => 'Encontrará adjunto su <strong>certificado de seguro de prestatario</strong> (referencia <strong>'.$loan->reference.'</strong>) emitido por AURENZA CAPITAL INVESTI en el marco de su solicitud de financiación.',
        'summary'  => 'RESUMEN DE SU SEGURO',
        'lbl_ref'  => 'Referencia del expediente',
        'lbl_montant'  => 'Importe asegurado',
        'lbl_duree'    => 'Duración',
        'lbl_months'   => 'meses',
        'lbl_frais'    => 'Gastos de seguro',
        'lbl_fin'      => 'Fecha de vencimiento del seguro',
        'attach_note'  => 'Su certificado de seguro CG-A340G se adjunta a este correo en formato PDF.',
        'contact'      => 'Para cualquier pregunta relativa a su seguro, no dude en ponerse en contacto con su asesor.',
        'closing'      => 'Atentamente,',
        'team'         => 'El equipo AURENZA CAPITAL INVESTI',
    ],
    'pl' => [
        'title'    => 'Zaświadczenie ubezpieczenia kredytobiorcy — nr '.$loan->reference,
        'sub'      => 'Ubezpieczenie CG-A340G',
        'greeting' => $greetings['pl'][$gender],
        'intro'    => 'W załączeniu przesyłamy <strong>zaświadczenie ubezpieczenia kredytobiorcy</strong> (numer referencyjny <strong>'.$loan->reference.'</strong>) wystawione przez AURENZA CAPITAL INVESTI w ramach Państwa wniosku kredytowego.',
        'summary'  => 'PODSUMOWANIE UBEZPIECZENIA',
        'lbl_ref'  => 'Numer referencyjny',
        'lbl_montant'  => 'Kwota ubezpieczona',
        'lbl_duree'    => 'Okres',
        'lbl_months'   => 'miesięcy',
        'lbl_frais'    => 'Składka ubezpieczeniowa',
        'lbl_fin'      => 'Data końca ubezpieczenia',
        'attach_note'  => 'Zaświadczenie ubezpieczenia CG-A340G jest załączone do tej wiadomości w formacie PDF.',
        'contact'      => 'W razie pytań dotyczących ubezpieczenia prosimy o kontakt z doradcą.',
        'closing'      => 'Z poważaniem,',
        'team'         => 'Zespół AURENZA CAPITAL INVESTI',
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
      <span class="panel-lbl">{{ $t['lbl_montant'] }}</span>
      <span class="panel-val accent">{{ number_format($loan->amount, 2, ',', ' ') }} {{ $loan->currency }}</span>
    </div>
    <div class="panel-row">
      <span class="panel-lbl">{{ $t['lbl_duree'] }}</span>
      <span class="panel-val">{{ $loan->darly }} {{ $t['lbl_months'] }}</span>
    </div>
    @if($loan->frais_assurance)
    <div class="panel-row">
      <span class="panel-lbl">{{ $t['lbl_frais'] }}</span>
      <span class="panel-val">{{ number_format($loan->frais_assurance, 2, ',', ' ') }} {{ $loan->currency }}</span>
    </div>
    @endif
    @if($loan->date_fin_assurance)
    <div class="panel-row">
      <span class="panel-lbl">{{ $t['lbl_fin'] }}</span>
      <span class="panel-val">{{ $loan->date_fin_assurance->format('d/m/Y') }}</span>
    </div>
    @endif
  </div>

  <div class="alert alert-info">
    <p>{!! $t['attach_note'] !!}</p>
  </div>

  <p class="body-text">{{ $t['contact'] }}</p>

  <p class="closing">
    {{ $t['closing'] }}<br>
    <strong>{{ $t['team'] }}</strong>
  </p>

</x-email-layout>
