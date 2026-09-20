@php
$texts = [
    'fr' => ['title'=>'Nouvelle facture','sub'=>'Espace facturation','greeting'=>'Bonjour','intro'=>'Vous avez reçu une nouvelle facture de la part de <strong>AURENZA CAPITAL</strong>.','lbl_ref'=>'Référence','lbl_issue'=>'Date d\'émission','lbl_due'=>'Date d\'échéance','lbl_total'=>'Montant total','lbl_desc'=>'Description','lbl_qty'=>'Qté','lbl_unit'=>'Prix unit.','lbl_line_total'=>'Total','lbl_sub'=>'Sous-total','lbl_vat'=>'TVA','lbl_ttc'=>'Total TTC','lbl_note'=>'Note','body'=>'Pour toute question concernant cette facture, veuillez contacter votre conseiller AURENZA CAPITAL.','closing'=>'Cordialement,','team'=>"L'équipe AURENZA CAPITAL"],
    'en' => ['title'=>'New invoice','sub'=>'Billing area','greeting'=>'Hello','intro'=>'You have received a new invoice from <strong>AURENZA CAPITAL</strong>.','lbl_ref'=>'Reference','lbl_issue'=>'Issue date','lbl_due'=>'Due date','lbl_total'=>'Total amount','lbl_desc'=>'Description','lbl_qty'=>'Qty','lbl_unit'=>'Unit price','lbl_line_total'=>'Total','lbl_sub'=>'Subtotal','lbl_vat'=>'VAT','lbl_ttc'=>'Total incl. VAT','lbl_note'=>'Note','body'=>'For any questions about this invoice, please contact your AURENZA CAPITAL advisor.','closing'=>'Best regards,','team'=>'The AURENZA CAPITAL team'],
    'es' => ['title'=>'Nueva factura','sub'=>'Área de facturación','greeting'=>'Hola','intro'=>'Ha recibido una nueva factura de <strong>AURENZA CAPITAL</strong>.','lbl_ref'=>'Referencia','lbl_issue'=>'Fecha de emisión','lbl_due'=>'Fecha de vencimiento','lbl_total'=>'Importe total','lbl_desc'=>'Descripción','lbl_qty'=>'Cant.','lbl_unit'=>'Precio unit.','lbl_line_total'=>'Total','lbl_sub'=>'Subtotal','lbl_vat'=>'IVA','lbl_ttc'=>'Total con IVA','lbl_note'=>'Nota','body'=>'Para cualquier consulta sobre esta factura, contacte a su asesor de AURENZA CAPITAL.','closing'=>'Atentamente,','team'=>'El equipo AURENZA CAPITAL'],
    'pl' => ['title'=>'Nowa faktura','sub'=>'Obszar rozliczeniowy','greeting'=>'Witaj','intro'=>'Otrzymałeś/aś nową fakturę od <strong>AURENZA CAPITAL</strong>.','lbl_ref'=>'Referencja','lbl_issue'=>'Data wystawienia','lbl_due'=>'Termin płatności','lbl_total'=>'Kwota całkowita','lbl_desc'=>'Opis','lbl_qty'=>'Ilość','lbl_unit'=>'Cena jedn.','lbl_line_total'=>'Razem','lbl_sub'=>'Suma częściowa','lbl_vat'=>'VAT','lbl_ttc'=>'Razem z VAT','lbl_note'=>'Uwaga','body'=>'W razie pytań dotyczących faktury, skontaktuj się ze swoim doradcą AURENZA CAPITAL.','closing'=>'Z poważaniem,','team'=>'Zespół AURENZA CAPITAL'],
];
$t = $texts[$locale] ?? $texts['fr'];
@endphp

<x-email-layout
    :title="$t['title'] . ' ' . $invoice->reference"
    :subtitle="$t['sub']"
    accent="orange"
>

  <p class="greeting">
    {{ $t['greeting'] }} {{ $invoice->client->name }},
  </p>

  <p class="body-text">{!! $t['intro'] !!}</p>

  {{-- Récapitulatif --}}
  <div class="panel">
    <div class="panel-row">
      <span class="panel-lbl">{{ $t['lbl_ref'] }}</span>
      <span class="panel-val">{{ $invoice->reference }}</span>
    </div>
    <div class="panel-row">
      <span class="panel-lbl">{{ $t['lbl_issue'] }}</span>
      <span class="panel-val">{{ $invoice->issue_date->format('d/m/Y') }}</span>
    </div>
    @if($invoice->due_date)
    <div class="panel-row">
      <span class="panel-lbl">{{ $t['lbl_due'] }}</span>
      <span class="panel-val">{{ $invoice->due_date->format('d/m/Y') }}</span>
    </div>
    @endif
    <div class="panel-row">
      <span class="panel-lbl">{{ $t['lbl_total'] }}</span>
      <span class="panel-val accent">{{ number_format($invoice->total, 2, ',', ' ') }} {{ $invoice->currency }}</span>
    </div>
  </div>

  {{-- Détail des lignes --}}
  @if(count($invoice->items ?? []))
  <div class="panel" style="margin-bottom:1.5rem">
    <div class="panel-row" style="background:rgba(255,255,255,.04)">
      <span class="panel-lbl" style="flex:2;color:rgba(240,245,255,.55);font-weight:600">{{ $t['lbl_desc'] }}</span>
      <span class="panel-lbl" style="text-align:center">{{ $t['lbl_qty'] }}</span>
      <span class="panel-lbl" style="text-align:right">{{ $t['lbl_unit'] }}</span>
      <span class="panel-lbl" style="text-align:right;min-width:80px">{{ $t['lbl_line_total'] }}</span>
    </div>
    @foreach($invoice->items as $item)
    <div class="panel-row">
      <span class="panel-val" style="flex:2;text-align:left">{{ $item['description'] ?? '—' }}</span>
      <span class="panel-val" style="text-align:center;min-width:40px">{{ $item['quantity'] ?? 1 }}</span>
      <span class="panel-val" style="text-align:right;min-width:80px">{{ number_format((float)($item['unit_price'] ?? 0), 2, ',', ' ') }}</span>
      <span class="panel-val accent" style="text-align:right;min-width:80px">{{ number_format((float)($item['total'] ?? 0), 2, ',', ' ') }} {{ $invoice->currency }}</span>
    </div>
    @endforeach
    @if($invoice->tax_rate > 0)
    <div class="panel-row"><span class="panel-lbl">{{ $t['lbl_sub'] }}</span><span class="panel-val">{{ number_format($invoice->subtotal, 2, ',', ' ') }} {{ $invoice->currency }}</span></div>
    <div class="panel-row"><span class="panel-lbl">{{ $t['lbl_vat'] }} ({{ $invoice->tax_rate }}%)</span><span class="panel-val">{{ number_format($invoice->tax_amount, 2, ',', ' ') }} {{ $invoice->currency }}</span></div>
    <div class="panel-row"><span class="panel-lbl" style="font-weight:700;color:#F0F5FF">{{ $t['lbl_ttc'] }}</span><span class="panel-val accent" style="font-size:.95rem">{{ number_format($invoice->total, 2, ',', ' ') }} {{ $invoice->currency }}</span></div>
    @endif
  </div>
  @endif

  @if($invoice->description)
  <p class="body-text">{{ $invoice->description }}</p>
  @endif

  @if($invoice->note)
  <div class="alert alert-info">
    <strong>{{ $t['lbl_note'] }}</strong>
    <p>{{ $invoice->note }}</p>
  </div>
  @endif

  <p class="body-text">{{ $t['body'] }}</p>

  <p class="closing">
    {{ $t['closing'] }}<br>
    <strong>{{ $t['team'] }}</strong>
  </p>

</x-email-layout>
