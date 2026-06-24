<!DOCTYPE html>
<html lang="{{ $locale ?? 'fr' }}">
<head>
<meta charset="UTF-8">
<style>
@page {
    margin: 18mm 22mm 20mm 22mm;
}
body {
    font-family: "DejaVu Serif", "Times New Roman", Times, Georgia, serif;
    font-size: 11pt;
    color: #000;
    line-height: 1.75;
    margin: 0;
    padding: 0;
}
.page { padding: 0; }

/* ── Filigrane image ── */
.crx-wm {
    position: fixed; top: 0; left: 0; width: 100%; height: 100%;
    pointer-events: none; z-index: -1; opacity: 0.09;
    background-size: 50%; background-position: center; background-repeat: no-repeat;
}
/* ── Filigrane texte (sans image) ── */
.crx-wm-text {
    position: fixed; top: 42%; left: 0; width: 100%; text-align: center;
    font-size: 55pt; font-weight: bold; color: rgba(0,0,0,0.04);
    pointer-events: none; z-index: -1;
    font-family: "DejaVu Serif", serif; letter-spacing: 8px;
}

/* ── En-tête principal ── */
.header-wrap {
    border-bottom: 1.5pt solid #000;
    margin-bottom: 18pt;
    padding-bottom: 10pt;
}
.header-logo-row {
    display: table;
    width: 100%;
    margin-bottom: 4pt;
}
.header-logo-left {
    display: table-cell;
    width: 18%;
    vertical-align: middle;
    text-align: left;
}
.header-logo-center {
    display: table-cell;
    width: 64%;
    vertical-align: middle;
    text-align: center;
}
.header-logo-right {
    display: table-cell;
    width: 18%;
    vertical-align: middle;
    text-align: right;
}
.header-title {
    font-size: 14pt;
    font-weight: bold;
    color: #000;
    letter-spacing: 1.5px;
    text-transform: uppercase;
    text-align: center;
    line-height: 1.3;
}
.header-country {
    font-size: 9.5pt;
    font-weight: bold;
    color: #000;
    text-align: center;
    white-space: pre-line;
    line-height: 1.5;
    margin-top: 8pt;
    margin-bottom: 8pt;
}
.header-ref {
    text-align: center;
    font-size: 10pt;
    margin-top: 6pt;
    line-height: 1.6;
}
.header-ref-label {
    font-weight: bold;
}

/* ── Titres de section (ENTRE LES PARTIES, INFORMATIONS...) ── */
.section-title {
    font-size: 10.5pt;
    font-weight: bold;
    color: #CC4400;
    text-decoration: underline;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    margin: 16pt 0 9pt;
}

/* ── Blocs parties ── */
.parties-block {
    margin-bottom: 9pt;
    font-size: 10.5pt;
    line-height: 1.75;
}
.party-label {
    font-weight: bold;
}

/* ── Table de financement ── */
.finance-table {
    width: 100%;
    border-collapse: collapse;
    margin: 8pt 0 16pt;
    font-size: 10.5pt;
}
.finance-table td {
    padding: 4pt 6pt;
    border: none;
    vertical-align: top;
}
.finance-table td:first-child {
    font-weight: bold;
    width: 62%;
}
.finance-table td:last-child {
    font-weight: bold;
}
.finance-table tr:nth-child(odd) td {
    background: rgba(0,0,0,0.025);
}

/* ── Articles ── */
.article {
    margin-bottom: 13pt;
}
.article-title {
    font-weight: bold;
    font-size: 10.5pt;
    color: #CC4400;
    margin-bottom: 5pt;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.article-body {
    font-size: 10.5pt;
    color: #000;
    line-height: 1.75;
    white-space: pre-line;
}

/* ── Bloc signature ── */
.signature-block {
    margin-top: 28pt;
    border-top: 1pt solid #000;
    padding-top: 10pt;
}
.sig-title {
    text-align: center;
    font-weight: bold;
    font-size: 11pt;
    text-transform: uppercase;
    letter-spacing: 2px;
    margin-bottom: 18pt;
}
.sig-row {
    display: table;
    width: 100%;
}
.sig-cell {
    display: table-cell;
    width: 33.33%;
    text-align: center;
    padding: 0 6pt;
    vertical-align: bottom;
}
.sig-col-header {
    font-weight: bold;
    font-size: 8.5pt;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 6pt;
    color: #000;
}
.sig-img-wrap {
    min-height: 52px;
    display: block;
}
.sig-line {
    border-top: 0.5pt solid #555;
    margin-top: 8pt;
    padding-top: 4pt;
}
.sig-name {
    font-size: 9pt;
    color: #000;
    font-style: italic;
}
.sig-date-line {
    font-size: 10.5pt;
    margin-bottom: 20pt;
}
</style>
</head>
<body>

{{-- ── Filigrane ── --}}
@isset($images)
  @if(!empty($images['watermark_path']))
    <div class="crx-wm" style="background-image:url('{{ $images['watermark_path'] }}')"></div>
  @else
    <div class="crx-wm-text">CONFIDENTIEL</div>
  @endif
@endisset

<div class="page">

{{-- ══════════════════════════════════════════ --}}
{{-- EN-TÊTE : logos + titre + pays + référence --}}
{{-- ══════════════════════════════════════════ --}}
<div class="header-wrap">

  {{-- Ligne logos + titre --}}
  <div class="header-logo-row">
    <div class="header-logo-left">
      @isset($images)
        @if(!empty($images['logo_left_path']))
          <img src="{{ $images['logo_left_path'] }}"
               style="max-height:62px;max-width:140px;object-fit:contain;">
        @endif
      @endisset
    </div>

    <div class="header-logo-center">
      <div class="header-title">{{ $t['title'] }}</div>
    </div>

    <div class="header-logo-right">
      @isset($images)
        @if(!empty($images['logo_right_path']))
          <img src="{{ $images['logo_right_path'] }}"
               style="max-height:62px;max-width:140px;object-fit:contain;">
        @endif
      @endisset
    </div>
  </div>

  {{-- Pays / institution (multilignes) --}}
  <div class="header-country">{{ $header }}</div>

  {{-- Référence --}}
  <div class="header-ref">
    <span class="header-ref-label">{{ $t['ref_label'] }} :</span><br>
    {{ $vars['{reference}'] }} / {{ $t['archive_label'] }} : {{ $vars['{archive}'] }}
  </div>

</div>

{{-- ══════════════════════════════════════════ --}}
{{-- ENTRE LES PARTIES                          --}}
{{-- ══════════════════════════════════════════ --}}
<div class="section-title">{{ $t['between'] }} :</div>

<div class="parties-block">
  <span class="party-label">{{ $t['lender_label'] }}</span>
  <strong> {{ $vars['{societe}'] }}</strong>,
  {{ $t['lender_desc'] }}
</div>

<div class="parties-block">
  <span class="party-label">{{ $t['borrower_label'] }}</span>,
  <strong>{{ $vars['{nom_client}'] }}</strong>,
  {{ str_replace(array_keys($vars), array_values($vars), $t['borrower_desc']) }}
</div>

<div class="parties-block">
  <span class="party-label">{{ $t['agent_label'] }}</span>,
  <strong>{{ $vars['{agent_suivi}'] }}</strong>,
  {{ $t['agent_desc'] }}
</div>

{{-- ══════════════════════════════════════════ --}}
{{-- INFORMATIONS DU FINANCEMENT                --}}
{{-- ══════════════════════════════════════════ --}}
<div class="section-title">{{ $t['finance_title'] }} :</div>

<table class="finance-table">
  <tr>
    <td>{{ $t['amount_label'] }} :</td>
    <td>{{ $vars['{montant}'] }} {{ $vars['{devise}'] }}</td>
  </tr>
  <tr>
    <td>{{ $t['duration_label'] }} :</td>
    <td>{{ $vars['{duree}'] }} {{ $t['months'] }}</td>
  </tr>
  <tr>
    <td>{{ $t['monthly_label'] }} :</td>
    <td>{{ $vars['{mensualite}'] }} {{ $vars['{devise}'] }}</td>
  </tr>
  <tr>
    <td>{{ $t['rate_label'] }} :</td>
    <td>{{ $vars['{taux}'] }} %</td>
  </tr>
  <tr>
    <td>{{ $t['fees_label'] }} :</td>
    <td>{{ $vars['{frais_admin}'] }}{{ ($vars['{frais_admin}'] ?? '—') !== '—' ? ' '.$vars['{devise}'] : '' }}</td>
  </tr>
  @if(!empty($vars['{compte_bancaire}']) && $vars['{compte_bancaire}'] !== '—')
  <tr>
    <td>{{ $t['bank_label'] }} :</td>
    <td>{{ $vars['{compte_bancaire}'] }}</td>
  </tr>
  @endif
</table>

{{-- ══════════════════════════════════════════ --}}
{{-- ARTICLES 1–8                               --}}
{{-- ══════════════════════════════════════════ --}}
@foreach([
  ['art1_title','art1_body'],
  ['art2_title','art2_body'],
  ['art3_title','art3_body'],
  ['art4_title','art4_body'],
  ['art5_title','art5_body'],
  ['art6_title','art6_body'],
  ['art7_title','art7_body'],
  ['art8_title','art8_body'],
] as [$titleKey, $bodyKey])
@if(isset($t[$titleKey]) && isset($t[$bodyKey]))
<div class="article">
  <div class="article-title">{{ $t[$titleKey] }}</div>
  <div class="article-body">{{ str_replace(array_keys($vars), array_values($vars), $t[$bodyKey]) }}</div>
</div>
@endif
@endforeach

{{-- ══════════════════════════════════════════ --}}
{{-- SIGNATURES                                 --}}
{{-- ══════════════════════════════════════════ --}}
<div class="signature-block">

  <div class="sig-date-line">{{ $t['made_at'] }} : <strong>{{ $vars['{date}'] }}</strong></div>

  <div class="sig-title">{{ $t['sig_title'] ?? 'SIGNATURES' }}</div>

  <div class="sig-row">

    {{-- Société prêteuse --}}
    <div class="sig-cell">
      <div class="sig-col-header">{{ $t['sig_lender'] }}</div>
      <div class="sig-img-wrap">
        @isset($images)
          @if(!empty($images['signature_admin_path']))
            <img src="{{ $images['signature_admin_path'] }}"
                 style="max-height:52px;max-width:120px;object-fit:contain;display:block;margin:0 auto">
          @endif
        @endisset
      </div>
      <div class="sig-line">
        <div class="sig-name">{{ $vars['{societe}'] }}</div>
      </div>
    </div>

    {{-- Responsable / Agent --}}
    <div class="sig-cell">
      <div class="sig-col-header">{{ $t['sig_agent'] }}</div>
      <div class="sig-img-wrap">
        @isset($images)
          @if(!empty($images['stamp_path']))
            <img src="{{ $images['stamp_path'] }}"
                 style="max-height:60px;max-width:130px;object-fit:contain;display:block;margin:0 auto">
          @endif
        @endisset
      </div>
      <div class="sig-line">
        <div class="sig-name">{{ $vars['{agent_suivi}'] }}</div>
      </div>
    </div>

    {{-- Emprunteur --}}
    <div class="sig-cell">
      <div class="sig-col-header">{{ $t['sig_borrower'] }}</div>
      <div class="sig-img-wrap">
        @isset($images)
          @if(!empty($images['signature_agent_path']))
            <img src="{{ $images['signature_agent_path'] }}"
                 style="max-height:52px;max-width:120px;object-fit:contain;display:block;margin:0 auto">
          @endif
        @endisset
      </div>
      <div class="sig-line">
        <div class="sig-name">{{ $vars['{nom_client}'] }}</div>
      </div>
    </div>

  </div>
</div>

</div>
</body>
</html>
