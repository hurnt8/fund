@props([
    'title'      => 'Aurenza Capital',
    'subtitle'   => 'Espace Client Sécurisé',
    'accent'     => 'brand',  // brand | green | orange | red
    'footerNote' => null,
])

@php
$palettes = [
    // Accent de marque : vert forêt + laiton (transactionnel courant)
    'brand'  => ['hdr'=>'linear-gradient(135deg,#082A20 0%,#14503D 100%)','icon'=>'linear-gradient(135deg,#C6A15B,#9A7736)','code'=>'#DCBE87','bdr'=>'rgba(198,161,91,.28)','btn'=>'#101A14'],
    'teal'   => ['hdr'=>'linear-gradient(135deg,#0B4525 0%,#0D6030 100%)','icon'=>'linear-gradient(135deg,#22A396,#167A6C)','code'=>'#2ECBB7','bdr'=>'rgba(34,163,150,.25)'],
    'green'  => ['hdr'=>'linear-gradient(135deg,#0A2B1A 0%,#0D3B22 100%)','icon'=>'linear-gradient(135deg,#16A34A,#15803D)','code'=>'#4ADE80','bdr'=>'rgba(74,222,128,.25)'],
    'orange' => ['hdr'=>'linear-gradient(135deg,#2B1800 0%,#3D2200 100%)','icon'=>'linear-gradient(135deg,#D97706,#B45309)','code'=>'#FBBF24','bdr'=>'rgba(251,191,36,.25)'],
    'red'    => ['hdr'=>'linear-gradient(135deg,#2D0A0A 0%,#4A1010 100%)','icon'=>'linear-gradient(135deg,#C0392B,#922B21)','code'=>'#F87171','bdr'=>'rgba(239,68,68,.25)'],
];
$p = $palettes[$accent] ?? $palettes['brand'];
@endphp
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>{{ $title }}</title>
<style>
body{margin:0;padding:0;background:#061A0D;font-family:'Segoe UI',Helvetica,Arial,sans-serif;-webkit-font-smoothing:antialiased}
img{border:0;outline:0}
table{border-collapse:collapse}
.wrap{max-width:580px;margin:32px auto;background:#0D381F;border-radius:20px;overflow:hidden;border:1px solid rgba(255,255,255,.08)}
/* ── Header ── */
.hdr{background:{{ $p['hdr'] }};padding:36px 44px 30px;text-align:center}
.logo-outer{width:76px;height:76px;border-radius:20px;margin:0 auto 18px;overflow:hidden;background:{{ $p['icon'] }};display:inline-flex;align-items:center;justify-content:center}
.logo-outer img{width:76px;height:76px;object-fit:cover;display:block}
.logo-letter{font-family:Georgia,'Times New Roman',serif;font-size:2.4rem;font-weight:900;color:#fff;line-height:1;display:none}
.hdr-title{font-size:1.3rem;font-weight:700;color:#F0F5FF;margin:0 0 5px;letter-spacing:-.01em}
.hdr-sub{font-size:.78rem;color:rgba(240,245,255,.45);margin:0;letter-spacing:.03em;text-transform:uppercase}
/* ── Body ── */
.body{padding:32px 44px}
p.greeting{font-size:.96rem;color:rgba(240,245,255,.78);margin:0 0 1.25rem;line-height:1.75}
p.body-text{font-size:.88rem;color:rgba(240,245,255,.62);line-height:1.8;margin:0 0 1.25rem}
/* ── Info panel ── */
.panel{background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.07);border-radius:13px;overflow:hidden;margin-bottom:1.5rem}
.panel-row{display:flex;justify-content:space-between;align-items:baseline;padding:9px 16px;border-bottom:1px solid rgba(255,255,255,.05);font-size:.84rem}
.panel-row:last-child{border-bottom:none}
.panel-lbl{color:rgba(240,245,255,.42);font-size:.79rem;padding-right:10px;white-space:nowrap}
.panel-val{color:#F0F5FF;font-weight:500;text-align:right}
.panel-val.accent{color:{{ $p['code'] }};font-weight:700}
/* ── Alert boxes ── */
.alert{border-radius:11px;padding:14px 18px;font-size:.83rem;line-height:1.7;margin-bottom:1.4rem}
.alert p{margin:0}
.alert strong{display:block;margin-bottom:3px;font-size:.85rem}
.alert-info   {background:rgba(34,163,150,.1); border:1px solid rgba(34,163,150,.2); border-left:3px solid #22A396;color:rgba(180,245,235,.8)}
.alert-warn   {background:rgba(251,191,36,.08);border:1px solid rgba(251,191,36,.2); border-left:3px solid #FBBF24;color:rgba(255,228,150,.8)}
.alert-danger {background:rgba(239,68,68,.1);  border:1px solid rgba(239,68,68,.2);  border-left:3px solid #ef4444;color:#FCA5A5}
.alert-success{background:rgba(74,222,128,.08);border:1px solid rgba(74,222,128,.2); border-left:3px solid #4ade80;color:rgba(150,240,180,.85)}
/* ── Button ── */
.btn-wrap{text-align:center;margin:1.6rem 0 1.2rem}
.btn{display:inline-block;padding:13px 36px;border-radius:999px;font-size:.94rem;font-weight:700;text-decoration:none;letter-spacing:.01em;color:{{ $p['btn'] ?? '#ffffff' }};background:{{ $p['icon'] }};box-shadow:0 4px 20px rgba(0,0,0,.45)}
/* ── Code box ── */
.code-box{background:rgba(255,255,255,.05);border:1px solid {{ $p['bdr'] }};border-radius:14px;padding:26px 20px;text-align:center;margin-bottom:1.5rem}
.code-digits{font-family:'Courier New',Courier,monospace;font-size:2.6rem;font-weight:900;letter-spacing:.35em;color:{{ $p['code'] }};text-shadow:0 0 24px {{ $p['code'] }}55}
.code-expiry{font-size:.77rem;color:rgba(240,245,255,.38);margin-top:9px}
/* ── URL fallback ── */
.url-fallback{font-size:.74rem;color:rgba(240,245,255,.35);text-align:center;line-height:1.75;margin-bottom:1.25rem;word-break:break-all}
.url-fallback a{color:rgba(34,163,150,.65);text-decoration:none}
/* ── Closing ── */
p.closing{font-size:.88rem;color:rgba(240,245,255,.52);margin:1.5rem 0 0;line-height:1.65}
p.closing strong{color:#F0F5FF;font-size:.92rem}
/* ── Divider ── */
.divider{height:1px;background:rgba(255,255,255,.06)}
/* ── Footer ── */
.footer{padding:18px 44px;text-align:center}
.footer p{font-size:.72rem;color:rgba(240,245,255,.25);line-height:1.7;margin:0}
.footer a{color:rgba(34,163,150,.6);text-decoration:none}
/* ── Responsive ── */
@media only screen and (max-width:600px){
  .wrap{margin:0;border-radius:0;border-left:none;border-right:none}
  .body,.hdr,.footer{padding:24px 22px!important}
  .code-digits{font-size:2rem;letter-spacing:.25em}
  .btn{padding:12px 26px;font-size:.88rem}
  .panel-row{flex-direction:column;gap:2px}
  .panel-val{text-align:left}
}
</style>
</head>
<body>
<div class="wrap">

  {{-- ── Header ── --}}
  <div class="hdr">
    <div class="logo-outer">
      <img src="{{ url('images/icon-192.png') }}" alt="Aurenza Capital" width="76" height="76">
    </div>
    <h1 class="hdr-title">{{ $title }}</h1>
    @if($subtitle)
    <p class="hdr-sub">{{ $subtitle }}</p>
    @endif
  </div>

  {{-- ── Content ── --}}
  <div class="body">
    {{ $slot }}
  </div>

  <div class="divider"></div>

  {{-- ── Footer ── --}}
  <div class="footer">
    <p>
      &copy; {{ date('Y') }} Aurenza Capital
      @if($footerNote)
      &nbsp;·&nbsp; {{ $footerNote }}
      @endif
    </p>
  </div>

</div>
</body>
</html>
