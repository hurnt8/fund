@component('mail::message')

@php
$texts = [
    'fr' => [
        'greeting'  => 'Madame / Monsieur ' . $loan->name . ',',
        'intro'     => 'Nous accusons bonne réception de votre contrat de prêt signé (Référence : **' . $loan->reference . '**).',
        'next'      => 'Notre équipe de gestion va procéder au traitement final de votre dossier. Les coordonnées du compte et les modalités de versement vous seront communiquées sous **24 à 48 heures**.',
        'closing'   => 'Cordialement,',
        'team'      => 'L\'équipe CREDIXA INVESTI',
    ],
    'pl' => [
        'greeting'  => 'Szanowny/a ' . $loan->name . ',',
        'intro'     => 'Potwierdzamy otrzymanie Państwa podpisanej umowy pożyczkowej (Nr referencyjny : **' . $loan->reference . '**).',
        'next'      => 'Nasz zespół przystąpi do ostatecznego rozpatrzenia Państwa wniosku. Dane rachunku bankowego zostaną przekazane w ciągu **24 do 48 godzin**.',
        'closing'   => 'Z poważaniem,',
        'team'      => 'Zespół CREDIXA INVESTI',
    ],
    'en' => [
        'greeting'  => 'Dear ' . $loan->name . ',',
        'intro'     => 'We confirm receipt of your signed loan contract (Reference: **' . $loan->reference . '**).',
        'next'      => 'Our management team will proceed with the final processing of your file. Payment account details will be communicated within **24 to 48 hours**.',
        'closing'   => 'Yours sincerely,',
        'team'      => 'The CREDIXA INVESTI team',
    ],
    'es' => [
        'greeting'  => 'Estimado/a ' . $loan->name . ',',
        'intro'     => 'Confirmamos la recepción de su contrato de préstamo firmado (Referencia: **' . $loan->reference . '**).',
        'next'      => 'Nuestro equipo de gestión procederá al tratamiento final de su expediente. Los datos de la cuenta de pago le serán comunicados en **24 a 48 horas**.',
        'closing'   => 'Atentamente,',
        'team'      => 'El equipo CREDIXA INVESTI',
    ],
];
$t = $texts[$locale] ?? $texts['fr'];
@endphp

# {{ $t['greeting'] }}

{{ $t['intro'] }}

{{ $t['next'] }}

---

{{ $t['closing'] }}

**{{ $t['team'] }}**

@endcomponent
