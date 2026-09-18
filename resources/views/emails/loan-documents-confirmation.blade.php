@php
$docTypes = [
    'fr' => ['passport'=>'Passeport','id_card'=>'Carte d\'identité','residence_permit'=>'Titre de séjour','driving_license'=>'Permis de conduire'],
    'en' => ['passport'=>'Passport','id_card'=>'Identity card','residence_permit'=>'Residence permit','driving_license'=>'Driving license'],
    'es' => ['passport'=>'Pasaporte','id_card'=>'Documento de identidad','residence_permit'=>'Permiso de residencia','driving_license'=>'Permiso de conducir'],
    'pl' => ['passport'=>'Paszport','id_card'=>'Dowód osobisty','residence_permit'=>'Zezwolenie na pobyt','driving_license'=>'Prawo jazdy'],
];
$texts = [
    'fr' => [
        'title'     => 'Documents reçus',
        'sub'       => 'Aurenza Capital',
        'greeting'  => 'Bonjour '.$data['name'].',',
        'body'      => 'Nous avons bien reçu vos documents (adresse et pièce d\'identité). Notre équipe les examinera et vous donnera un retour dans les <strong>24 heures</strong>.',
        'lbl_name'  => 'Nom',
        'lbl_doc'   => 'Type de document',
        'lbl_addr'  => 'Adresse',
        'footer'    => 'Nous vous remercions de votre confiance et restons à votre disposition pour toute question.',
        'noreply'   => 'Cet email a été envoyé depuis une adresse no-reply. Veuillez ne pas répondre directement.',
        'closing'   => 'Cordialement,',
        'team'      => 'L\'équipe Aurenza Capital',
    ],
    'en' => [
        'title'     => 'Documents received',
        'sub'       => 'Aurenza Capital',
        'greeting'  => 'Hello '.$data['name'].',',
        'body'      => 'We have received your documents (address and identity document). Our team will review them and get back to you within <strong>24 hours</strong>.',
        'lbl_name'  => 'Name',
        'lbl_doc'   => 'Document type',
        'lbl_addr'  => 'Address',
        'footer'    => 'Thank you for your trust. We remain available for any questions.',
        'noreply'   => 'This email was sent from a no-reply address. Please do not reply directly.',
        'closing'   => 'Best regards,',
        'team'      => 'The Aurenza Capital team',
    ],
    'es' => [
        'title'     => 'Documentos recibidos',
        'sub'       => 'Aurenza Capital',
        'greeting'  => 'Hola '.$data['name'].',',
        'body'      => 'Hemos recibido sus documentos (dirección y documento de identidad). Nuestro equipo los revisará y le dará una respuesta en <strong>24 horas</strong>.',
        'lbl_name'  => 'Nombre',
        'lbl_doc'   => 'Tipo de documento',
        'lbl_addr'  => 'Dirección',
        'footer'    => 'Gracias por su confianza. Quedamos a su disposición para cualquier pregunta.',
        'noreply'   => 'Este email fue enviado desde una dirección de no respuesta. No responda directamente.',
        'closing'   => 'Atentamente,',
        'team'      => 'El equipo Aurenza Capital',
    ],
    'pl' => [
        'title'     => 'Dokumenty odebrane',
        'sub'       => 'Aurenza Capital',
        'greeting'  => 'Witaj '.$data['name'].',',
        'body'      => 'Otrzymaliśmy Twoje dokumenty (adres i dokument tożsamości). Nasz zespół je przejrzy i skontaktuje się z Tobą w ciągu <strong>24 godzin</strong>.',
        'lbl_name'  => 'Imię i nazwisko',
        'lbl_doc'   => 'Typ dokumentu',
        'lbl_addr'  => 'Adres',
        'footer'    => 'Dziękujemy za zaufanie. Jesteśmy do Twojej dyspozycji w razie pytań.',
        'noreply'   => 'Ten email został wysłany z adresu no-reply. Prosimy nie odpowiadać bezpośrednio.',
        'closing'   => 'Z poważaniem,',
        'team'      => 'Zespół Aurenza Capital',
    ],
];
$t       = $texts[$lang] ?? $texts['fr'];
$docType = $docTypes[$lang][$data['doc_type'] ?? ''] ?? ($data['doc_type'] ?? '—');
@endphp

<x-email-layout
    :title="$t['title']"
    :subtitle="$t['sub']"
    accent="green"
    :footerNote="$t['noreply']"
>

  <p class="greeting">{{ $t['greeting'] }}</p>

  <p class="body-text">{!! $t['body'] !!}</p>

  <div class="panel">
    <div class="panel-row">
      <span class="panel-lbl">{{ $t['lbl_name'] }}</span>
      <span class="panel-val">{{ $data['name'] }}</span>
    </div>
    <div class="panel-row">
      <span class="panel-lbl">{{ $t['lbl_doc'] }}</span>
      <span class="panel-val">{{ $docType }}</span>
    </div>
    <div class="panel-row">
      <span class="panel-lbl">{{ $t['lbl_addr'] }}</span>
      <span class="panel-val" style="white-space:pre-line">{{ $data['address'] }}</span>
    </div>
  </div>

  <p class="body-text">{{ $t['footer'] }}</p>

  <p class="closing">
    {{ $t['closing'] }}<br>
    <strong>{{ $t['team'] }}</strong>
  </p>

</x-email-layout>
