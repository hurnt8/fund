@php
$texts = [
    'fr' => [
        'title'      => 'Demande de prêt reçue',
        'sub'        => 'Aurenza Capital',
        'greeting'   => 'Bonjour '.$data['name'].',',
        'body'       => 'Nous avons bien reçu votre demande de prêt d\'un montant de <strong>'.number_format($data['amount'], 0, ',', ' ').' '.($data['currency'] ?? 'EUR').'</strong> sur <strong>'.$data['darly'].' mois</strong>. Elle est actuellement en cours de traitement par notre équipe.',
        'cond_title' => 'Conditions d\'éligibilité',
        'cond_body'  => 'Pour obtenir un prêt, il faut avoir au moins 18 ans, percevoir un revenu mensuel stable et pouvoir rembourser selon les conditions fixées.',
        'btn_intro'  => 'Pour finaliser votre dossier, veuillez cliquer sur le bouton ci-dessous afin de nous transmettre votre adresse complète et une copie de votre pièce d\'identité.',
        'btn_label'  => 'Compléter ma demande',
        'footer'     => 'Nous vous contacterons dans les plus brefs délais. Merci de nous avoir fait confiance.',
        'noreply'    => 'Cet email a été envoyé depuis une adresse no-reply. Veuillez ne pas répondre directement.',
        'closing'    => 'Cordialement,',
        'team'       => 'L\'équipe Aurenza Capital',
    ],
    'en' => [
        'title'      => 'Loan application received',
        'sub'        => 'Aurenza Capital',
        'greeting'   => 'Hello '.$data['name'].',',
        'body'       => 'We have received your loan request for <strong>'.number_format($data['amount'], 0, ',', ' ').' '.($data['currency'] ?? 'EUR').'</strong> over <strong>'.$data['darly'].' months</strong>. It is currently being processed by our team.',
        'cond_title' => 'Eligibility conditions',
        'cond_body'  => 'To obtain a loan, you must be at least 18 years old, have a stable monthly income, and be able to repay according to the set conditions.',
        'btn_intro'  => 'To finalise your application, please click the button below to send us your full address and a copy of your ID.',
        'btn_label'  => 'Complete my application',
        'footer'     => 'We will contact you as soon as possible. Thank you for trusting us.',
        'noreply'    => 'This email was sent from a no-reply address. Please do not reply directly.',
        'closing'    => 'Best regards,',
        'team'       => 'The Aurenza Capital team',
    ],
    'es' => [
        'title'      => 'Solicitud de préstamo recibida',
        'sub'        => 'Aurenza Capital',
        'greeting'   => 'Hola '.$data['name'].',',
        'body'       => 'Hemos recibido su solicitud de préstamo por <strong>'.number_format($data['amount'], 0, ',', ' ').' '.($data['currency'] ?? 'EUR').'</strong> a <strong>'.$data['darly'].' meses</strong>. Actualmente está siendo procesada por nuestro equipo.',
        'cond_title' => 'Condiciones de elegibilidad',
        'cond_body'  => 'Para obtener un préstamo, debe tener al menos 18 años, percibir ingresos mensuales estables y poder reembolsar según las condiciones establecidas.',
        'btn_intro'  => 'Para finalizar su solicitud, haga clic en el botón para enviarnos su dirección completa y una copia de su documento de identidad.',
        'btn_label'  => 'Completar mi solicitud',
        'footer'     => 'Nos pondremos en contacto con usted lo antes posible. Gracias por su confianza.',
        'noreply'    => 'Este email fue enviado desde una dirección de no respuesta. No responda directamente.',
        'closing'    => 'Atentamente,',
        'team'       => 'El equipo Aurenza Capital',
    ],
    'pl' => [
        'title'      => 'Wniosek o pożyczkę otrzymany',
        'sub'        => 'Aurenza Capital',
        'greeting'   => 'Witaj '.$data['name'].',',
        'body'       => 'Otrzymaliśmy Twój wniosek o pożyczkę na kwotę <strong>'.number_format($data['amount'], 0, ',', ' ').' '.($data['currency'] ?? 'EUR').'</strong> na <strong>'.$data['darly'].' miesięcy</strong>. Jest on aktualnie przetwarzany przez nasz zespół.',
        'cond_title' => 'Warunki kwalifikowalności',
        'cond_body'  => 'Aby uzyskać pożyczkę, należy mieć co najmniej 18 lat, osiągać stałe miesięczne dochody i móc spłacać zgodnie z ustalonymi warunkami.',
        'btn_intro'  => 'Aby sfinalizować wniosek, kliknij poniższy przycisk, aby przesłać swój pełny adres i kopię dokumentu tożsamości.',
        'btn_label'  => 'Uzupełnij mój wniosek',
        'footer'     => 'Skontaktujemy się z Tobą jak najszybciej. Dziękujemy za zaufanie.',
        'noreply'    => 'Ten email został wysłany z adresu no-reply. Prosimy nie odpowiadać bezpośrednio.',
        'closing'    => 'Z poważaniem,',
        'team'       => 'Zespół Aurenza Capital',
    ],
];
$t = $texts[$lang] ?? $texts['fr'];
@endphp

<x-email-layout
    :title="$t['title']"
    :subtitle="$t['sub']"
    accent="brand"
    :footerNote="$t['noreply']"
>

  <p class="greeting">{{ $t['greeting'] }}</p>

  <p class="body-text">{!! $t['body'] !!}</p>

  <div class="alert alert-info">
    <strong>{{ $t['cond_title'] }}</strong>
    <p>{{ $t['cond_body'] }}</p>
  </div>

  <p class="body-text">{{ $t['btn_intro'] }}</p>

  <div class="btn-wrap">
    <a href="{{ $data['complete_url'] }}" class="btn">{{ $t['btn_label'] }}</a>
  </div>

  <p class="body-text">{{ $t['footer'] }}</p>

  <p class="closing">
    {{ $t['closing'] }}<br>
    <strong>{{ $t['team'] }}</strong>
  </p>

</x-email-layout>
