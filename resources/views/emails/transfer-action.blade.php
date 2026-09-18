@php
$ref    = $transfer->reference;
$amount = number_format($transfer->amount, 2, ',', ' ') . ' ' . $transfer->currency;
$bene   = $transfer->beneficiary_name;

$texts = [
    'approved' => [
        'fr' => ['title'=>'Virement validé','sub'=>'Confirmation de traitement','greeting'=>'Bonjour','intro'=>'Votre virement a été <strong>validé</strong> et traité avec succès.','lbl_ref'=>'Référence','lbl_amount'=>'Montant','lbl_bene'=>'Bénéficiaire','lbl_iban'=>'IBAN','lbl_note'=>'Note','body'=>'Le montant a été débité de votre compte. Pour toute question, contactez votre conseiller.','closing'=>'Cordialement,','team'=>"L'équipe AURENZA CAPITAL INVESTI"],
        'en' => ['title'=>'Transfer approved','sub'=>'Processing confirmation','greeting'=>'Hello','intro'=>'Your transfer has been <strong>approved</strong> and processed successfully.','lbl_ref'=>'Reference','lbl_amount'=>'Amount','lbl_bene'=>'Beneficiary','lbl_iban'=>'IBAN','lbl_note'=>'Note','body'=>'The amount has been debited from your account. For any questions, contact your advisor.','closing'=>'Best regards,','team'=>'The AURENZA CAPITAL INVESTI team'],
        'es' => ['title'=>'Transferencia aprobada','sub'=>'Confirmación de procesamiento','greeting'=>'Hola','intro'=>'Su transferencia ha sido <strong>aprobada</strong> y procesada con éxito.','lbl_ref'=>'Referencia','lbl_amount'=>'Importe','lbl_bene'=>'Beneficiario','lbl_iban'=>'IBAN','lbl_note'=>'Nota','body'=>'El importe ha sido debitado de su cuenta. Para cualquier consulta, contacte a su asesor.','closing'=>'Atentamente,','team'=>'El equipo AURENZA CAPITAL INVESTI'],
        'pl' => ['title'=>'Przelew zatwierdzony','sub'=>'Potwierdzenie przetworzenia','greeting'=>'Witaj','intro'=>'Twój przelew został <strong>zatwierdzony</strong> i przetworzony pomyślnie.','lbl_ref'=>'Referencja','lbl_amount'=>'Kwota','lbl_bene'=>'Beneficjent','lbl_iban'=>'IBAN','lbl_note'=>'Uwaga','body'=>'Kwota została pobrana z Twojego konta. W razie pytań skontaktuj się z doradcą.','closing'=>'Z poważaniem,','team'=>'Zespół AURENZA CAPITAL INVESTI'],
    ],
    'rejected' => [
        'fr' => ['title'=>'Virement rejeté','sub'=>'Information sur votre virement','greeting'=>'Bonjour','intro'=>'Nous avons le regret de vous informer que votre virement a été <strong>rejeté</strong>.','lbl_ref'=>'Référence','lbl_amount'=>'Montant','lbl_bene'=>'Bénéficiaire','lbl_iban'=>'IBAN','lbl_note'=>'Motif','body'=>'Le montant de <strong>'.$amount.'</strong> a été <strong>recrédité</strong> sur votre compte. Pour soumettre à nouveau ou obtenir plus d\'informations, contactez votre conseiller.','closing'=>'Cordialement,','team'=>"L'équipe AURENZA CAPITAL INVESTI"],
        'en' => ['title'=>'Transfer rejected','sub'=>'Information about your transfer','greeting'=>'Hello','intro'=>'We regret to inform you that your transfer has been <strong>rejected</strong>.','lbl_ref'=>'Reference','lbl_amount'=>'Amount','lbl_bene'=>'Beneficiary','lbl_iban'=>'IBAN','lbl_note'=>'Reason','body'=>'The amount of <strong>'.$amount.'</strong> has been <strong>credited back</strong> to your account. To resubmit or for more information, contact your advisor.','closing'=>'Best regards,','team'=>'The AURENZA CAPITAL INVESTI team'],
        'es' => ['title'=>'Transferencia rechazada','sub'=>'Información sobre su transferencia','greeting'=>'Hola','intro'=>'Lamentamos informarle que su transferencia ha sido <strong>rechazada</strong>.','lbl_ref'=>'Referencia','lbl_amount'=>'Importe','lbl_bene'=>'Beneficiario','lbl_iban'=>'IBAN','lbl_note'=>'Motivo','body'=>'El importe de <strong>'.$amount.'</strong> ha sido <strong>devuelto</strong> a su cuenta. Para reenviar o más información, contacte a su asesor.','closing'=>'Atentamente,','team'=>'El equipo AURENZA CAPITAL INVESTI'],
        'pl' => ['title'=>'Przelew odrzucony','sub'=>'Informacja o Twoim przelewie','greeting'=>'Witaj','intro'=>'Z przykrością informujemy, że Twój przelew został <strong>odrzucony</strong>.','lbl_ref'=>'Referencja','lbl_amount'=>'Kwota','lbl_bene'=>'Beneficjent','lbl_iban'=>'IBAN','lbl_note'=>'Powód','body'=>'Kwota <strong>'.$amount.'</strong> została <strong>zwrócona</strong> na Twoje konto. Aby ponownie przesłać lub uzyskać więcej informacji, skontaktuj się z doradcą.','closing'=>'Z poważaniem,','team'=>'Zespół AURENZA CAPITAL INVESTI'],
    ],
    'fee_required' => [
        'fr' => ['title'=>'Frais requis','sub'=>'Action requise pour votre virement','greeting'=>'Bonjour','intro'=>'Votre virement est en attente de règlement de <strong>frais de traitement</strong>.','lbl_ref'=>'Référence virement','lbl_amount'=>'Montant virement','lbl_bene'=>'Bénéficiaire','lbl_iban'=>'IBAN','lbl_note'=>'Frais à régler','body'=>'Connectez-vous à votre espace client pour consulter la facture. Une fois les frais réglés, votre virement sera traité.','closing'=>'Cordialement,','team'=>"L'équipe AURENZA CAPITAL INVESTI"],
        'en' => ['title'=>'Fees required','sub'=>'Action required for your transfer','greeting'=>'Hello','intro'=>'Your transfer is pending settlement of <strong>processing fees</strong>.','lbl_ref'=>'Transfer reference','lbl_amount'=>'Transfer amount','lbl_bene'=>'Beneficiary','lbl_iban'=>'IBAN','lbl_note'=>'Fees to settle','body'=>'Log in to your client space to view the invoice. Once the fees are paid, your transfer will be processed.','closing'=>'Best regards,','team'=>'The AURENZA CAPITAL INVESTI team'],
        'es' => ['title'=>'Comisiones requeridas','sub'=>'Acción requerida para su transferencia','greeting'=>'Hola','intro'=>'Su transferencia está pendiente del pago de <strong>comisiones de procesamiento</strong>.','lbl_ref'=>'Referencia transferencia','lbl_amount'=>'Importe transferencia','lbl_bene'=>'Beneficiario','lbl_iban'=>'IBAN','lbl_note'=>'Comisiones a pagar','body'=>'Inicie sesión en su espacio cliente para consultar la factura. Una vez pagadas las comisiones, su transferencia será procesada.','closing'=>'Atentamente,','team'=>'El equipo AURENZA CAPITAL INVESTI'],
        'pl' => ['title'=>'Wymagane opłaty','sub'=>'Wymagane działanie dla Twojego przelewu','greeting'=>'Witaj','intro'=>'Twój przelew oczekuje na uregulowanie <strong>opłat za przetwarzanie</strong>.','lbl_ref'=>'Referencja przelewu','lbl_amount'=>'Kwota przelewu','lbl_bene'=>'Beneficjent','lbl_iban'=>'IBAN','lbl_note'=>'Opłaty do uregulowania','body'=>'Zaloguj się do swojego obszaru klienta, aby zobaczyć fakturę. Po opłaceniu zostanie przetworzony.','closing'=>'Z poważaniem,','team'=>'Zespół AURENZA CAPITAL INVESTI'],
    ],
];

$t      = $texts[$action][$locale] ?? $texts[$action]['fr'];
$accent = match($action) { 'approved' => 'green', 'rejected' => 'red', default => 'orange' };
@endphp

<x-email-layout
    :title="$t['title']"
    :subtitle="$t['sub']"
    :accent="$accent"
>

  <p class="greeting">
    {{ $t['greeting'] }} {{ $transfer->user->name }},
  </p>

  <p class="body-text">{!! $t['intro'] !!}</p>

  <div class="panel">
    <div class="panel-row">
      <span class="panel-lbl">{{ $t['lbl_ref'] }}</span>
      <span class="panel-val">{{ $ref }}</span>
    </div>
    <div class="panel-row">
      <span class="panel-lbl">{{ $t['lbl_amount'] }}</span>
      <span class="panel-val accent">{{ $amount }}</span>
    </div>
    <div class="panel-row">
      <span class="panel-lbl">{{ $t['lbl_bene'] }}</span>
      <span class="panel-val">{{ $bene }}</span>
    </div>
    @if($transfer->beneficiary_iban)
    <div class="panel-row">
      <span class="panel-lbl">{{ $t['lbl_iban'] }}</span>
      <span class="panel-val" style="font-family:monospace;font-size:.82rem">{{ $transfer->beneficiary_iban }}</span>
    </div>
    @endif
    @if($action === 'fee_required' && $transfer->invoice)
    <div class="panel-row">
      <span class="panel-lbl">{{ $t['lbl_note'] }}</span>
      <span class="panel-val accent">{{ number_format($transfer->invoice->total, 2, ',', ' ') }} {{ $transfer->invoice->currency }}</span>
    </div>
    @if($transfer->invoice->reference)
    <div class="panel-row">
      <span class="panel-lbl">Réf. facture</span>
      <span class="panel-val">{{ $transfer->invoice->reference }}</span>
    </div>
    @endif
    @elseif($transfer->admin_note)
    <div class="panel-row">
      <span class="panel-lbl">{{ $t['lbl_note'] }}</span>
      <span class="panel-val">{{ $transfer->admin_note }}</span>
    </div>
    @endif
  </div>

  <p class="body-text">{!! $t['body'] !!}</p>

  <p class="closing">
    {{ $t['closing'] }}<br>
    <strong>{{ $t['team'] }}</strong>
  </p>

</x-email-layout>
