<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
  body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #1a1a2e; line-height: 1.7; margin: 0; padding: 0; }
  .page { padding: 30px 40px; }
  .header { text-align: center; border-bottom: 3px solid #0B1A2E; padding-bottom: 14px; margin-bottom: 20px; }
  .header-country { font-size: 9px; color: #555; white-space: pre-line; margin-bottom: 8px; }
  .header-title { font-size: 18px; font-weight: bold; color: #0B1A2E; letter-spacing: 1px; margin-bottom: 6px; }
  .header-ref { font-size: 9px; color: #444; }
  .section-title { font-size: 10px; font-weight: bold; color: #C8A951; text-transform: uppercase; letter-spacing: 1px; border-bottom: 1px solid #e0c97a; margin: 18px 0 8px; padding-bottom: 3px; }
  .parties-block { margin-bottom: 10px; }
  .party-label { font-weight: bold; color: #0B1A2E; }
  .finance-table { width: 100%; border-collapse: collapse; margin: 10px 0 16px; }
  .finance-table td { padding: 6px 10px; border: 1px solid #dde; font-size: 10.5px; }
  .finance-table td:first-child { background: #f5f7fb; font-weight: bold; width: 55%; color: #0B1A2E; }
  .finance-table td:last-child { font-weight: bold; color: #C8A951; font-size: 12px; }
  .article { margin-bottom: 14px; }
  .article-title { font-weight: bold; color: #0B1A2E; font-size: 10.5px; margin-bottom: 5px; }
  .article-body { white-space: pre-line; font-size: 10px; color: #333; line-height: 1.75; }
  .signature-block { margin-top: 30px; border-top: 2px solid #0B1A2E; padding-top: 14px; }
  .sig-date { margin-bottom: 20px; font-size: 10px; }
  .sig-row { display: table; width: 100%; }
  .sig-cell { display: table-cell; width: 33%; text-align: center; padding: 10px 5px; }
  .sig-label { font-weight: bold; font-size: 9px; text-transform: uppercase; color: #0B1A2E; border-top: 1px solid #ccc; padding-top: 5px; margin-top: 40px; }
  .logo-area { text-align: center; margin-bottom: 10px; }
  .company-name { font-size: 13px; font-weight: bold; color: #0B1A2E; letter-spacing: 2px; }
  .crx-wm{position:fixed;top:0;left:0;width:100%;height:100%;pointer-events:none;z-index:0;opacity:.08;background-size:40%;background-position:center;background-repeat:no-repeat;}
</style>
</head>
<body>
@isset($tpl)
  @if($tpl->watermark_path)
    <div class="crx-wm" style="background-image:url('{{ asset('storage/'.$tpl->watermark_path) }}')"></div>
  @endif
@endisset
<div class="page">
@isset($tpl)
  @if($tpl->logo_left_path || $tpl->logo_right_path)
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;padding-bottom:8px;border-bottom:1px solid #e0e0e0;">
      @if($tpl->logo_left_path)<img src="{{ asset('storage/'.$tpl->logo_left_path) }}" style="max-height:55px;max-width:150px;object-fit:contain;">@else<span></span>@endif
      @if($tpl->logo_right_path)<img src="{{ asset('storage/'.$tpl->logo_right_path) }}" style="max-height:55px;max-width:150px;object-fit:contain;">@else<span></span>@endif
    </div>
  @endif
@endisset

  {{-- EN-TÊTE --}}
  <div class="header">
    <div class="header-country">{{ $header }}</div>
    <div class="header-title">{{ $t['title'] }}</div>
    <div class="header-ref">
      {{ $t['ref_label'] }} : {{ $vars['{reference}'] }}
      &nbsp;&nbsp;|&nbsp;&nbsp;
      {{ $t['archive_label'] }} : {{ $vars['{archive}'] }}
    </div>
  </div>

  {{-- PARTIES --}}
  <div class="section-title">{{ $t['between'] }}</div>

  <div class="parties-block">
    <span class="party-label">{{ $t['lender_label'] }}</span>
    <span class="company-name"> {{ $vars['{societe}'] }}</span>,
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

  {{-- INFORMATIONS FINANCEMENT --}}
  <div class="section-title">{{ $t['finance_title'] }}</div>
  <table class="finance-table">
    <tr>
      <td>{{ $t['amount_label'] }}</td>
      <td>{{ $vars['{montant}'] }} {{ $vars['{devise}'] }}</td>
    </tr>
    <tr>
      <td>{{ $t['duration_label'] }}</td>
      <td>{{ $vars['{duree}'] }} {{ $t['months'] }}</td>
    </tr>
    <tr>
      <td>{{ $t['monthly_label'] }}</td>
      <td>{{ $vars['{mensualite}'] }} {{ $vars['{devise}'] }}</td>
    </tr>
    <tr>
      <td>{{ $t['rate_label'] }}</td>
      <td>{{ $vars['{taux}'] }} %</td>
    </tr>
    <tr>
      <td>{{ $t['fees_label'] }}</td>
      <td>{{ $vars['{frais_admin}'] }} {{ $vars['{frais_admin}'] !== '—' ? $vars['{devise}'] : '' }}</td>
    </tr>
  </table>

  {{-- ARTICLES --}}
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
  <div class="article">
    <div class="article-title">{{ $t[$titleKey] }}</div>
    <div class="article-body">{{ str_replace(array_keys($vars), array_values($vars), $t[$bodyKey]) }}</div>
  </div>
  @endforeach

  {{-- SIGNATURE --}}
  <div class="signature-block">
    <div class="sig-date">{{ $t['made_at'] }} : {{ $vars['{date}'] }}</div>
    <div class="sig-row">
      <div class="sig-cell">
        @isset($tpl) @if($tpl->signature_admin_path)<img src="{{ asset('storage/'.$tpl->signature_admin_path) }}" style="max-height:50px;object-fit:contain;display:block;margin:0 auto 6px;">@endif @endisset
        <div class="sig-label">{{ $t['sig_lender'] }}</div>
      </div>
      <div class="sig-cell">
        @isset($tpl) @if($tpl->stamp_path)<img src="{{ asset('storage/'.$tpl->stamp_path) }}" style="max-height:60px;object-fit:contain;display:block;margin:0 auto 6px;">@endif @endisset
        <div class="sig-label">{{ $t['sig_agent'] }}</div>
      </div>
      <div class="sig-cell">
        @isset($tpl) @if($tpl->signature_agent_path)<img src="{{ asset('storage/'.$tpl->signature_agent_path) }}" style="max-height:50px;object-fit:contain;display:block;margin:0 auto 6px;">@endif @endisset
        <div class="sig-label">{{ $t['sig_borrower'] }}</div>
      </div>
    </div>
  </div>

@isset($tpl)
  @if($tpl->logo_left_path || $tpl->logo_right_path){{-- logos already at top --}}@endif
@endisset

</div>
</body>
</html>
