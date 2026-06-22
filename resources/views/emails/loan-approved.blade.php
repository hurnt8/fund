@php
$texts = [
    'fr' => [
        'title'    => 'Demande N°'.$loan->reference.' approuvée',
        'sub'      => 'Bonne nouvelle !',
        'greeting' => 'Madame / Monsieur '.$loan->name.',',
        'intro'    => 'Nous avons le plaisir de vous informer que votre demande de financement a été <strong>approuvée</strong> par notre équipe.',
        'next'     => 'Prochaine étape',
        'next_body'=> 'Notre équipe prépare votre contrat de prêt. Vous le recevrez très prochainement par email avec les instructions pour la signature.',
        'summary'  => 'RÉCAPITULATIF',
        'lbl_ref'  => 'Référence dossier',
        'lbl_amt'  => 'Montant accordé',
        'lbl_dur'  => 'Durée',
        'lbl_mo'   => 'mois',
        'lbl_pay'  => 'Mensualité estimée',
        'lbl_rate' => 'Taux annuel',
        'closing'  => 'Cordialement,',
        'team'     => 'L\'équipe CREDIXA INVESTI',
    ],
    'en' => [
        'title'    => 'Application N°'.$loan->reference.' approved',
        'sub'      => 'Great news!',
        'greeting' => 'Dear '.$loan->name.',',
        'intro'    => 'We are pleased to inform you that your financing application has been <strong>approved</strong> by our team.',
        'next'     => 'Next step',
        'next_body'=> 'Our team is preparing your loan contract. You will receive it shortly by email with signing instructions.',
        'summary'  => 'SUMMARY',
        'lbl_ref'  => 'File reference',
        'lbl_amt'  => 'Amount approved',
        'lbl_dur'  => 'Duration',
        'lbl_mo'   => 'months',
        'lbl_pay'  => 'Estimated monthly payment',
        'lbl_rate' => 'Annual rate',
        'closing'  => 'Yours sincerely,',
        'team'     => 'The CREDIXA INVESTI team',
    ],
    'es' => [
        'title'    => 'Solicitud N°'.$loan->reference.' aprobada',
        'sub'      => '¡Buenas noticias!',
        'greeting' => 'Estimado/a '.$loan->name.',',
        'intro'    => 'Nos complace informarle que su solicitud de financiación ha sido <strong>aprobada</strong> por nuestro equipo.',
        'next'     => 'Próximo paso',
        'next_body'=> 'Nuestro equipo está preparando su contrato de préstamo. Lo recibirá en breve por correo electrónico con las instrucciones de firma.',
        'summary'  => 'RESUMEN',
        'lbl_ref'  => 'Referencia',
        'lbl_amt'  => 'Importe aprobado',
        'lbl_dur'  => 'Duración',
        'lbl_mo'   => 'meses',
        'lbl_pay'  => 'Cuota mensual estimada',
        'lbl_rate' => 'Tasa anual',
        'closing'  => 'Atentamente,',
        'team'     => 'El equipo CREDIXA INVESTI',
    ],
    'pl' => [
        'title'    => 'Wniosek nr '.$loan->reference.' zatwierdzony',
        'sub'      => 'Świetne wieści!',
        'greeting' => 'Szanowny/a '.$loan->name.',',
        'intro'    => 'Z przyjemnością informujemy, że Państwa wniosek o finansowanie został <strong>zatwierdzony</strong> przez nasz zespół.',
        'next'     => 'Następny krok',
        'next_body'=> 'Nasz zespół przygotowuje umowę pożyczki. Wkrótce otrzymają Państwo ją pocztą elektroniczną wraz z instrukcją podpisania.',
        'summary'  => 'PODSUMOWANIE',
        'lbl_ref'  => 'Numer referencyjny',
        'lbl_amt'  => 'Zatwierdzona kwota',
        'lbl_dur'  => 'Okres',
        'lbl_mo'   => 'miesięcy',
        'lbl_pay'  => 'Szacowana miesięczna rata',
        'lbl_rate' => 'Stopa roczna',
        'closing'  => 'Z poważaniem,',
        'team'     => 'Zespół CREDIXA INVESTI',
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
      <span class="panel-lbl">{{ $t['lbl_amt'] }}</span>
      <span class="panel-val accent">{{ number_format($loan->amount, 2, ',', ' ') }} {{ $loan->currency }}</span>
    </div>
    <div class="panel-row">
      <span class="panel-lbl">{{ $t['lbl_dur'] }}</span>
      <span class="panel-val">{{ $loan->darly }} {{ $t['lbl_mo'] }}</span>
    </div>
    <div class="panel-row">
      <span class="panel-lbl">{{ $t['lbl_pay'] }}</span>
      <span class="panel-val">{{ number_format($loan->monthly_payment, 2, ',', ' ') }} {{ $loan->currency }}</span>
    </div>
    <div class="panel-row">
      <span class="panel-lbl">{{ $t['lbl_rate'] }}</span>
      <span class="panel-val">{{ $loan->interest_rate }} %</span>
    </div>
  </div>

  <div class="alert alert-info">
    <strong>{{ $t['next'] }}</strong>
    <p>{{ $t['next_body'] }}</p>
  </div>

  <p class="closing">
    {{ $t['closing'] }}<br>
    <strong>{{ $t['team'] }}</strong>
  </p>

</x-email-layout>
