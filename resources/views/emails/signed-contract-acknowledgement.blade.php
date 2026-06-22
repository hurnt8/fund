@php
$texts = [
    'fr' => [
        'title'   => 'Contrat N°'.$loan->reference.' reçu',
        'sub'     => 'Accusé de réception',
        'greeting'=> 'Madame / Monsieur '.$loan->name.',',
        'intro'   => 'Nous accusons bonne réception de votre contrat de prêt signé (Référence : <strong>'.$loan->reference.'</strong>).',
        'next'    => 'Notre équipe de gestion va procéder au traitement final de votre dossier. Les coordonnées du compte et les modalités de versement vous seront communiquées sous <strong>24 à 48 heures</strong>.',
        'closing' => 'Cordialement,',
        'team'    => "L'équipe CREDIXA INVESTI",
    ],
    'en' => [
        'title'   => 'Contract N°'.$loan->reference.' received',
        'sub'     => 'Acknowledgement of receipt',
        'greeting'=> 'Dear '.$loan->name.',',
        'intro'   => 'We confirm receipt of your signed loan contract (Reference: <strong>'.$loan->reference.'</strong>).',
        'next'    => 'Our management team will proceed with the final processing of your file. Payment account details will be communicated within <strong>24 to 48 hours</strong>.',
        'closing' => 'Yours sincerely,',
        'team'    => 'The CREDIXA INVESTI team',
    ],
    'es' => [
        'title'   => 'Contrato N°'.$loan->reference.' recibido',
        'sub'     => 'Acuse de recibo',
        'greeting'=> 'Estimado/a '.$loan->name.',',
        'intro'   => 'Confirmamos la recepción de su contrato de préstamo firmado (Referencia: <strong>'.$loan->reference.'</strong>).',
        'next'    => 'Nuestro equipo de gestión procederá al tratamiento final de su expediente. Los datos de la cuenta de pago le serán comunicados en <strong>24 a 48 horas</strong>.',
        'closing' => 'Atentamente,',
        'team'    => 'El equipo CREDIXA INVESTI',
    ],
    'pl' => [
        'title'   => 'Umowa nr '.$loan->reference.' odebrana',
        'sub'     => 'Potwierdzenie odbioru',
        'greeting'=> 'Szanowny/a '.$loan->name.',',
        'intro'   => 'Potwierdzamy otrzymanie Państwa podpisanej umowy pożyczkowej (Nr referencyjny: <strong>'.$loan->reference.'</strong>).',
        'next'    => 'Nasz zespół przystąpi do ostatecznego rozpatrzenia Państwa wniosku. Dane rachunku bankowego zostaną przekazane w ciągu <strong>24 do 48 godzin</strong>.',
        'closing' => 'Z poważaniem,',
        'team'    => 'Zespół CREDIXA INVESTI',
    ],
];
$t = $texts[$locale] ?? $texts['fr'];
@endphp

<x-email-layout
    :title="$t['title']"
    :subtitle="$t['sub']"
    accent="teal"
>

  <p class="greeting">{{ $t['greeting'] }}</p>

  <p class="body-text">{!! $t['intro'] !!}</p>

  <div class="alert alert-info">
    <p>{!! $t['next'] !!}</p>
  </div>

  <p class="closing">
    {{ $t['closing'] }}<br>
    <strong>{{ $t['team'] }}</strong>
  </p>

</x-email-layout>
