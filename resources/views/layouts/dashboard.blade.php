<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>@yield('title','Dashboard') — Credixa Invest</title>
<link rel="icon" href="{{ asset('assets/images/favicons/favicon.png') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('assets/vendors/bootstrap/css/bootstrap.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/vendors/fontawesome/css/all.min.css') }}">
<style>
/* ═══════════════════════════════════════════════
   CREDIXA INVEST — DESIGN SYSTEM v2
   ═══════════════════════════════════════════════ */
:root {
  --c-navy:       #0B1A2E;
  --c-navy-2:     #112240;
  --c-navy-3:     #1a3a5c;
  --c-gold:       #C8A951;
  --c-gold-d:     #A88830;
  --c-gold-l:     #f0e0a0;
  --c-bg:         #F1F4F9;
  --c-surface:    #FFFFFF;
  --c-border:     #E4E8F0;
  --c-text:       #1A2332;
  --c-muted:      #6B7280;
  --c-green:      #059669;
  --c-green-l:    #D1FAE5;
  --c-red:        #DC2626;
  --c-red-l:      #FEE2E2;
  --c-amber:      #D97706;
  --c-amber-l:    #FEF3C7;
  --c-blue:       #2563EB;
  --c-blue-l:     #DBEAFE;
  --c-violet:     #7C3AED;
  --c-violet-l:   #EDE9FE;
  --sidebar-w:    260px;
  --topbar-h:     64px;
  --radius:       12px;
  --radius-sm:    8px;
  --shadow:       0 1px 3px rgba(0,0,0,.06), 0 4px 16px rgba(0,0,0,.07);
  --shadow-sm:    0 1px 2px rgba(0,0,0,.05);
  --transition:   all .2s ease;
}

*, *::before, *::after { box-sizing:border-box; margin:0; padding:0; }
body { font-family:'Inter',sans-serif; background:var(--c-bg); color:var(--c-text); font-size:.875rem; line-height:1.6; min-height:100vh; -webkit-font-smoothing:antialiased; }
a { text-decoration:none; }

/* ─── SCROLLBAR ─── */
::-webkit-scrollbar { width:5px; height:5px; }
::-webkit-scrollbar-track { background:transparent; }
::-webkit-scrollbar-thumb { background:rgba(0,0,0,.15); border-radius:99px; }

/* ══════════════════
   SIDEBAR
   ══════════════════ */
.sidebar {
  position:fixed; top:0; left:0; width:var(--sidebar-w); height:100vh;
  background:var(--c-navy);
  display:flex; flex-direction:column; z-index:300;
  overflow-y:auto; transition:transform .3s cubic-bezier(.4,0,.2,1);
}
.sidebar-brand {
  padding:1.25rem 1.5rem;
  border-bottom:1px solid rgba(255,255,255,.06);
  flex-shrink:0;
}
.sidebar-brand img { height:34px; width:auto; }

.sidebar-user {
  margin:1rem 1rem .25rem;
  padding:.875rem 1rem;
  background:rgba(255,255,255,.05);
  border-radius:var(--radius-sm);
  display:flex; align-items:center; gap:.75rem;
}
.sidebar-user__avatar {
  width:36px; height:36px; border-radius:50%;
  background:linear-gradient(135deg, var(--c-gold), var(--c-gold-d));
  display:flex; align-items:center; justify-content:center;
  font-weight:800; font-size:.8125rem; color:var(--c-navy); flex-shrink:0;
}
.sidebar-user__name { font-size:.8125rem; font-weight:600; color:#fff; line-height:1.3; }
.sidebar-user__role { font-size:.7rem; color:rgba(255,255,255,.4); margin-top:.1rem; }

.sidebar-nav { padding:.5rem 0; flex:1; }
.sidebar-label {
  display:block;
  padding:.625rem 1.5rem .3rem;
  font-size:.6rem; font-weight:700; letter-spacing:.1em;
  text-transform:uppercase; color:rgba(255,255,255,.22);
  margin-top:.25rem;
}
.sidebar-link {
  display:flex; align-items:center; gap:.75rem;
  padding:.6rem 1.25rem .6rem 1.5rem;
  color:rgba(255,255,255,.55);
  font-size:.8375rem; font-weight:500;
  border-left:3px solid transparent;
  transition:var(--transition);
  position:relative;
}
.sidebar-link .icon { width:18px; text-align:center; font-size:.8rem; flex-shrink:0; }
.sidebar-link:hover { color:#fff; background:rgba(255,255,255,.04); border-left-color:rgba(255,255,255,.15); }
.sidebar-link.active { color:var(--c-gold); background:rgba(200,169,81,.1); border-left-color:var(--c-gold); font-weight:600; }
.sidebar-link.active .icon { color:var(--c-gold); }

.sidebar-footer {
  padding:1rem 1.5rem;
  border-top:1px solid rgba(255,255,255,.06);
  flex-shrink:0;
}
.sidebar-logout {
  display:flex; align-items:center; gap:.75rem;
  width:100%; background:none; border:none;
  color:rgba(255,255,255,.4); font-size:.8125rem; font-weight:500;
  cursor:pointer; padding:.5rem 0; transition:color .2s;
  font-family:inherit;
}
.sidebar-logout:hover { color:#f87171; }
.sidebar-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:299; }
.sidebar-overlay.show { display:block; }

/* ══════════════════
   MAIN WRAPPER
   ══════════════════ */
.main-wrap { margin-left:var(--sidebar-w); min-height:100vh; display:flex; flex-direction:column; }

/* ══════════════════
   TOPBAR
   ══════════════════ */
.topbar {
  height:var(--topbar-h);
  background:var(--c-surface);
  border-bottom:1px solid var(--c-border);
  display:flex; align-items:center; justify-content:space-between;
  padding:0 1.75rem;
  position:sticky; top:0; z-index:200;
  box-shadow:0 1px 0 rgba(0,0,0,.04);
}
.topbar-left { display:flex; align-items:center; gap:1rem; }
.topbar-toggle { background:none; border:none; color:var(--c-muted); font-size:1rem; cursor:pointer; display:none; padding:.25rem; }
.topbar-title { font-size:.9375rem; font-weight:700; color:var(--c-navy); }
.topbar-right { display:flex; align-items:center; gap:1rem; }
.topbar-badge {
  width:36px; height:36px; border-radius:var(--radius-sm);
  border:1.5px solid var(--c-border); background:var(--c-surface);
  display:flex; align-items:center; justify-content:center;
  color:var(--c-muted); font-size:.875rem; cursor:pointer; transition:var(--transition);
}
.topbar-badge:hover { background:var(--c-bg); color:var(--c-navy); }
.topbar-avatar {
  width:36px; height:36px; border-radius:50%;
  background:linear-gradient(135deg,var(--c-navy),var(--c-navy-3));
  display:flex; align-items:center; justify-content:center;
  color:var(--c-gold); font-weight:800; font-size:.8125rem;
}

/* ══════════════════
   CONTENT AREA
   ══════════════════ */
.content-area { padding:1.75rem; flex:1; }

/* ══════════════════
   PAGE HEADER
   ══════════════════ */
.page-hdr { margin-bottom:1.75rem; }
.page-hdr h1, .page-hdr h2, .page-hdr h3, .page-hdr h4 {
  font-size:1.125rem; font-weight:800; color:var(--c-navy); margin:0 0 .25rem;
}
.page-hdr p { font-size:.8125rem; color:var(--c-muted); margin:0; }

/* ══════════════════
   CARDS
   ══════════════════ */
.card-pro {
  background:var(--c-surface);
  border-radius:var(--radius);
  border:1px solid var(--c-border);
  box-shadow:var(--shadow-sm);
  overflow:hidden;
}
.card-pro-hdr {
  padding:.9375rem 1.25rem;
  border-bottom:1px solid var(--c-border);
  display:flex; align-items:center; justify-content:space-between;
}
.card-pro-title {
  font-size:.8125rem; font-weight:700; color:var(--c-navy);
  display:flex; align-items:center; gap:.5rem;
}
.card-pro-title .icon-dot {
  width:6px; height:6px; border-radius:50%; background:var(--c-gold); flex-shrink:0;
}
.card-pro-body { padding:1.25rem; }

/* ══════════════════
   METRIC CARDS
   ══════════════════ */
.metric-card {
  background:var(--c-surface);
  border-radius:var(--radius);
  border:1px solid var(--c-border);
  padding:1.25rem 1.375rem;
  box-shadow:var(--shadow-sm);
  position:relative; overflow:hidden;
  transition:var(--transition);
}
.metric-card:hover { box-shadow:var(--shadow); transform:translateY(-1px); }
.metric-card__icon {
  width:44px; height:44px; border-radius:10px;
  display:flex; align-items:center; justify-content:center;
  font-size:1rem; margin-bottom:.875rem;
}
.metric-card__val { font-size:1.75rem; font-weight:800; color:var(--c-navy); line-height:1; margin-bottom:.25rem; }
.metric-card__lbl { font-size:.75rem; color:var(--c-muted); font-weight:500; }
.metric-card__accent {
  position:absolute; top:0; right:0;
  width:80px; height:80px; border-radius:0 var(--radius) 0 80px;
  opacity:.06;
}
/* Icon color variants */
.mi-navy  { background:#EEF2FF; color:var(--c-navy); }
.mi-gold  { background:#FEF9EC; color:var(--c-gold-d); }
.mi-green { background:var(--c-green-l); color:var(--c-green); }
.mi-red   { background:var(--c-red-l); color:var(--c-red); }
.mi-blue  { background:var(--c-blue-l); color:var(--c-blue); }
.mi-violet{ background:var(--c-violet-l); color:var(--c-violet); }
.mi-amber { background:var(--c-amber-l); color:var(--c-amber); }
.mi-gray  { background:#F3F4F6; color:#6B7280; }

/* ══════════════════
   STATUS BADGES
   ══════════════════ */
.badge-status {
  display:inline-flex; align-items:center; gap:.35rem;
  padding:.25rem .7rem;
  border-radius:999px; font-size:.7rem; font-weight:600;
  white-space:nowrap;
}
.badge-status::before {
  content:''; width:5px; height:5px; border-radius:50%; flex-shrink:0;
  background:currentColor;
}
.bs-gray    { background:#F3F4F6; color:#6B7280; }
.bs-amber   { background:var(--c-amber-l); color:var(--c-amber); }
.bs-blue    { background:var(--c-blue-l); color:var(--c-blue); }
.bs-violet  { background:var(--c-violet-l); color:var(--c-violet); }
.bs-green   { background:var(--c-green-l); color:var(--c-green); }
.bs-emerald { background:#D1FAE5; color:#065F46; }
.bs-red     { background:var(--c-red-l); color:var(--c-red); }
/* Map de statusColor() → classe */
.bs-secondary { background:#F3F4F6; color:#6B7280; }
.bs-warning   { background:var(--c-amber-l); color:var(--c-amber); }
.bs-info      { background:var(--c-blue-l); color:var(--c-blue); }
.bs-primary   { background:var(--c-violet-l); color:var(--c-violet); }
.bs-success   { background:var(--c-green-l); color:var(--c-green); }
.bs-dark      { background:#1F2937; color:#F9FAFB; }
.bs-danger    { background:var(--c-red-l); color:var(--c-red); }
/* Backward compat sb- classes */
.sb-secondary,.sb--default { background:#F3F4F6; color:#6B7280; }
.sb-warning,.sb--pending   { background:var(--c-amber-l); color:var(--c-amber); }
.sb-info,.sb--review       { background:var(--c-blue-l); color:var(--c-blue); }
.sb-primary                { background:var(--c-violet-l); color:var(--c-violet); }
.sb-success,.sb--approved  { background:var(--c-green-l); color:var(--c-green); }
.sb-dark                   { background:#1F2937; color:#F9FAFB; }
.sb-danger,.sb--rejected   { background:var(--c-red-l); color:var(--c-red); }
.sb { display:inline-block; padding:.25rem .65rem; border-radius:999px; font-size:.7rem; font-weight:600; white-space:nowrap; }

/* ══════════════════
   PROFESSIONAL TABLE
   ══════════════════ */
.pro-table { width:100%; border-collapse:collapse; }
.pro-table thead th {
  padding:.75rem 1rem;
  font-size:.7rem; font-weight:700; color:var(--c-muted);
  text-transform:uppercase; letter-spacing:.06em;
  background:#FAFBFC;
  border-bottom:1px solid var(--c-border);
  text-align:left; white-space:nowrap;
}
.pro-table tbody td {
  padding:.875rem 1rem;
  font-size:.8375rem; color:var(--c-text);
  border-bottom:1px solid #F3F4F6;
  vertical-align:middle;
}
.pro-table tbody tr:last-child td { border-bottom:none; }
.pro-table tbody tr:hover td { background:#F8FAFF; }
.pro-table .cell-mono { font-family:'Courier New',monospace; font-weight:700; font-size:.78rem; color:var(--c-navy); }
.pro-table .cell-name { font-weight:600; color:var(--c-navy); }
.pro-table .cell-sub  { font-size:.75rem; color:var(--c-muted); margin-top:.15rem; }
.pro-table .cell-amount { font-weight:700; font-size:.9rem; }

/* ══════════════════
   BUTTONS
   ══════════════════ */
.btn-navy {
  display:inline-flex; align-items:center; gap:.4rem;
  background:var(--c-navy); color:#fff;
  border:none; border-radius:var(--radius-sm);
  padding:.525rem 1.125rem; font-size:.8375rem; font-weight:600;
  cursor:pointer; transition:var(--transition); font-family:inherit;
  text-decoration:none;
}
.btn-navy:hover { background:var(--c-navy-2); color:#fff; }
.btn-navy:active { transform:scale(.98); }

.btn-gold {
  display:inline-flex; align-items:center; gap:.4rem;
  background:var(--c-gold); color:var(--c-navy);
  border:none; border-radius:var(--radius-sm);
  padding:.525rem 1.125rem; font-size:.8375rem; font-weight:700;
  cursor:pointer; transition:var(--transition); font-family:inherit;
  text-decoration:none;
}
.btn-gold:hover { background:var(--c-gold-d); color:#fff; }

.btn-ghost {
  display:inline-flex; align-items:center; gap:.4rem;
  background:transparent; color:var(--c-text);
  border:1.5px solid var(--c-border); border-radius:var(--radius-sm);
  padding:.5rem 1rem; font-size:.8375rem; font-weight:500;
  cursor:pointer; transition:var(--transition); font-family:inherit;
  text-decoration:none;
}
.btn-ghost:hover { background:var(--c-bg); border-color:#CBD5E1; color:var(--c-navy); }

.btn-icon {
  display:inline-flex; align-items:center; justify-content:center;
  width:32px; height:32px; border-radius:var(--radius-sm);
  border:1.5px solid var(--c-border); background:var(--c-surface);
  color:var(--c-muted); font-size:.8rem;
  cursor:pointer; transition:var(--transition); text-decoration:none;
}
.btn-icon:hover { background:var(--c-bg); color:var(--c-navy); border-color:#94A3B8; }
.btn-icon-danger:hover { background:var(--c-red-l); color:var(--c-red); border-color:var(--c-red); }
.btn-icon-primary:hover { background:var(--c-blue-l); color:var(--c-blue); border-color:var(--c-blue); }
.btn-icon-success:hover { background:var(--c-green-l); color:var(--c-green); border-color:var(--c-green); }

.btn-sm-pro { padding:.375rem .75rem; font-size:.78rem; }
/* Bootstrap compat */
.b-navy { background:var(--c-navy); color:#fff; border:none; border-radius:var(--radius-sm); padding:.525rem 1.125rem; font-size:.8375rem; font-weight:600; cursor:pointer; display:inline-flex; align-items:center; gap:.4rem; text-decoration:none; transition:var(--transition); }
.b-navy:hover { background:var(--c-navy-2); color:#fff; }
.b-gold { background:var(--c-gold); color:var(--c-navy); border:none; border-radius:var(--radius-sm); padding:.525rem 1.125rem; font-size:.8375rem; font-weight:700; cursor:pointer; display:inline-flex; align-items:center; gap:.4rem; text-decoration:none; transition:var(--transition); }
.btn-xs { padding:.2rem .5rem; font-size:.75rem; border-radius:6px; }

/* ══════════════════
   FORM CONTROLS
   ══════════════════ */
.form-label-pro { font-size:.78rem; font-weight:600; color:var(--c-navy); display:block; margin-bottom:.375rem; }
.form-control-pro {
  width:100%; padding:.6rem .875rem;
  background:var(--c-surface); border:1.5px solid var(--c-border);
  border-radius:var(--radius-sm); font-size:.8375rem; color:var(--c-text);
  font-family:inherit; transition:var(--transition);
  appearance:none;
}
.form-control-pro:focus { outline:none; border-color:var(--c-gold); box-shadow:0 0 0 3px rgba(200,169,81,.12); }
.form-control-pro::placeholder { color:#C4CADC; }
.form-help { font-size:.73rem; color:var(--c-muted); margin-top:.3rem; }

.form-section { margin-bottom:1.5rem; }
.form-section-title {
  font-size:.7rem; font-weight:700; text-transform:uppercase;
  letter-spacing:.08em; color:var(--c-muted);
  padding:.5rem 0 .75rem; border-bottom:1px solid var(--c-border);
  margin-bottom:1rem;
}

/* ══════════════════
   SUMMARY BOX
   ══════════════════ */
.summary-box {
  background:linear-gradient(135deg, var(--c-navy) 0%, var(--c-navy-3) 100%);
  border-radius:var(--radius); padding:1.5rem;
  color:#fff;
}
.summary-box__label { font-size:.72rem; color:rgba(255,255,255,.55); font-weight:500; margin-bottom:.25rem; }
.summary-box__val { font-size:1.375rem; font-weight:800; color:var(--c-gold); }
.summary-box__sub { font-size:.73rem; color:rgba(255,255,255,.45); margin-top:.2rem; }

/* ══════════════════
   TIMELINE / STEPS
   ══════════════════ */
.steps-bar { display:flex; align-items:center; gap:0; }
.step-item { flex:1; display:flex; flex-direction:column; align-items:center; position:relative; }
.step-item::before {
  content:''; position:absolute; top:14px; left:50%; right:-50%;
  height:2px; background:var(--c-border); z-index:0;
}
.step-item:last-child::before { display:none; }
.step-dot {
  width:28px; height:28px; border-radius:50%;
  display:flex; align-items:center; justify-content:center;
  font-size:.72rem; font-weight:700; position:relative; z-index:1;
  background:var(--c-border); color:var(--c-muted);
  border:2px solid var(--c-border);
  transition:var(--transition);
}
.step-dot.done { background:var(--c-gold); color:var(--c-navy); border-color:var(--c-gold); }
.step-dot.current { background:var(--c-navy); color:var(--c-gold); border-color:var(--c-gold); }
.step-label { font-size:.62rem; text-align:center; color:var(--c-muted); margin-top:.4rem; max-width:65px; line-height:1.3; }
.step-label.done,.step-label.current { color:var(--c-navy); font-weight:600; }

/* ══════════════════
   FLASH MESSAGES
   ══════════════════ */
.flash { border-radius:var(--radius-sm); padding:.875rem 1.125rem; margin-bottom:1.25rem; display:flex; align-items:center; gap:.75rem; font-size:.8375rem; font-weight:500; }
.flash-ok  { background:#ECFDF5; color:#065F46; border:1px solid #A7F3D0; }
.flash-err { background:#FEF2F2; color:#991B1B; border:1px solid #FECACA; }
.flash-warn{ background:#FFFBEB; color:#92400E; border:1px solid #FDE68A; }
/* backward compat */
.da.da--ok  { background:#ECFDF5; color:#065F46; }
.da.da--err { background:#FEF2F2; color:#991B1B; }
.da { padding:.875rem 1.125rem; border-radius:var(--radius-sm); font-size:.8375rem; margin-bottom:1.25rem; display:flex; align-items:center; gap:.75rem; }

/* ══════════════════
   FILTER BAR
   ══════════════════ */
.filter-bar {
  background:var(--c-surface); border-radius:var(--radius);
  border:1px solid var(--c-border); padding:.875rem 1.125rem;
  margin-bottom:1.25rem;
  display:flex; flex-wrap:wrap; gap:.625rem; align-items:center;
}
.filter-bar input, .filter-bar select {
  height:36px; border:1.5px solid var(--c-border);
  border-radius:var(--radius-sm); padding:0 .75rem;
  font-size:.8125rem; color:var(--c-text);
  background:var(--c-bg); font-family:inherit;
  transition:var(--transition); min-width:0;
}
.filter-bar input:focus, .filter-bar select:focus {
  outline:none; border-color:var(--c-gold); background:var(--c-surface);
  box-shadow:0 0 0 3px rgba(200,169,81,.1);
}
.filter-bar input::placeholder { color:#C4CADC; }

/* ══════════════════
   TABS
   ══════════════════ */
.tabs-pro { display:flex; border-bottom:2px solid var(--c-border); margin-bottom:1.5rem; gap:.25rem; }
.tab-btn {
  padding:.625rem 1.125rem; font-size:.8125rem; font-weight:600;
  color:var(--c-muted); border:none; background:none;
  border-bottom:2px solid transparent; margin-bottom:-2px;
  cursor:pointer; transition:var(--transition); border-radius:var(--radius-sm) var(--radius-sm) 0 0;
}
.tab-btn:hover { color:var(--c-navy); background:rgba(0,0,0,.02); }
.tab-btn.active { color:var(--c-navy); border-bottom-color:var(--c-gold); }

/* ══════════════════
   STAT CARD (legacy)
   ══════════════════ */
.stat-c { background:var(--c-surface); border-radius:var(--radius); border:1px solid var(--c-border); padding:1.25rem; box-shadow:var(--shadow-sm); display:flex; align-items:center; gap:1rem; }
.stat-c__icon { width:48px; height:48px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:1.125rem; flex-shrink:0; }
.ic--blue   { background:#EFF6FF; color:var(--c-blue); }
.ic--gold   { background:#FEF9EC; color:var(--c-gold-d); }
.ic--green  { background:var(--c-green-l); color:var(--c-green); }
.ic--red    { background:var(--c-red-l); color:var(--c-red); }
.ic--navy   { background:#EEF2FF; color:var(--c-navy); }
.ic--purple { background:var(--c-violet-l); color:var(--c-violet); }
.ic--gray   { background:#F3F4F6; color:#6B7280; }
.ic--teal   { background:#F0FDFA; color:#0D9488; }
.stat-c__val { font-size:1.5rem; font-weight:800; color:var(--c-navy); line-height:1; }
.stat-c__lbl { font-size:.75rem; color:var(--c-muted); margin-top:.2rem; }

/* ══════════════════
   MOBILE
   ══════════════════ */
@media(max-width:991px) {
  .sidebar { transform:translateX(-100%); }
  .sidebar.open { transform:translateX(0); }
  .main-wrap { margin-left:0; }
  .topbar-toggle { display:flex; }
  .content-area { padding:1.25rem; }
}
@media(max-width:575px) {
  .content-area { padding:1rem; }
  .metric-card__val { font-size:1.4rem; }
}
</style>
@stack('styles')
</head>
<body>

<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

<!-- ═══════════════════ SIDEBAR ═══════════════════ -->
<aside class="sidebar" id="sidebar">

  <div class="sidebar-brand">
    <a href="{{ route('home',['locale'=>app()->getLocale()]) }}">
      <img src="{{ asset('assets/images/logo new.png') }}" alt="Credixa Invest">
    </a>
  </div>

  @auth
  <div class="sidebar-user">
    <div class="sidebar-user__avatar">{{ strtoupper(substr(Auth::user()->name,0,1)) }}</div>
    <div>
      <div class="sidebar-user__name">{{ Str::limit(Auth::user()->name,20) }}</div>
      <div class="sidebar-user__role">{{ ucfirst(str_replace('-',' ',Auth::user()->getRoleNames()->first()??'')) }}</div>
    </div>
  </div>
  @endauth

  <nav class="sidebar-nav">
  @auth

    {{-- ══ CLIENT ══ --}}
    @if(Auth::user()->hasRole('client'))
      <span class="sidebar-label">Navigation</span>
      <a href="{{ route('client.dashboard') }}"
         class="sidebar-link {{ request()->routeIs('client.dashboard') ? 'active':'' }}">
        <i class="fas fa-th-large icon"></i> Tableau de bord
      </a>
      <a href="{{ route('client.loans') }}"
         class="sidebar-link {{ request()->routeIs('client.loans*') ? 'active':'' }}">
        <i class="fas fa-file-invoice-dollar icon"></i> Mes demandes
      </a>
      <span class="sidebar-label">Compte</span>
      <a href="{{ route('home',['locale'=>app()->getLocale()]) }}" class="sidebar-link">
        <i class="fas fa-globe icon"></i> Retour au site
      </a>

    {{-- ══ SUPER-ADMIN ══ --}}
    @elseif(Auth::user()->hasRole('super-admin'))
      <span class="sidebar-label">Tableau de bord</span>
      <a href="{{ route('super-admin.dashboard') }}"
         class="sidebar-link {{ request()->routeIs('super-admin.dashboard') ? 'active':'' }}">
        <i class="fas fa-chart-pie icon"></i> Vue d'ensemble
      </a>

      <span class="sidebar-label">Prêts &amp; Contrats</span>
      <a href="{{ route('super-admin.loans.index') }}"
         class="sidebar-link {{ request()->routeIs('super-admin.loans*') ? 'active':'' }}">
        <i class="fas fa-file-invoice-dollar icon"></i> Toutes les demandes
      </a>
      <a href="{{ route('admin.contract-templates.index') }}"
         class="sidebar-link {{ request()->routeIs('admin.contract-templates*') ? 'active':'' }}">
        <i class="fas fa-file-signature icon"></i> Modèles de contrats
      </a>

      <span class="sidebar-label">Administration</span>
      <a href="{{ route('super-admin.roles') }}"
         class="sidebar-link {{ request()->routeIs('super-admin.roles') ? 'active':'' }}">
        <i class="fas fa-shield-alt icon"></i> Rôles &amp; Permissions
      </a>
      <a href="{{ route('admin.users') }}"
         class="sidebar-link {{ request()->routeIs('admin.users') ? 'active':'' }}">
        <i class="fas fa-users icon"></i> Utilisateurs
      </a>

      <span class="sidebar-label">Compte</span>
      <a href="{{ route('home',['locale'=>app()->getLocale()]) }}" class="sidebar-link">
        <i class="fas fa-globe icon"></i> Retour au site
      </a>

    {{-- ══ ADMIN ══ --}}
    @elseif(Auth::user()->hasRole('admin'))
      <span class="sidebar-label">Tableau de bord</span>
      <a href="{{ route('admin.dashboard') }}"
         class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active':'' }}">
        <i class="fas fa-chart-pie icon"></i> Vue d'ensemble
      </a>

      <span class="sidebar-label">Prêts &amp; Contrats</span>
      <a href="{{ route('admin.loans.index') }}"
         class="sidebar-link {{ request()->routeIs('admin.loans*') ? 'active':'' }}">
        <i class="fas fa-file-invoice-dollar icon"></i> Demandes de prêt
      </a>
      <a href="{{ route('admin.contract-templates.index') }}"
         class="sidebar-link {{ request()->routeIs('admin.contract-templates*') ? 'active':'' }}">
        <i class="fas fa-file-signature icon"></i> Modèles de contrats
      </a>

      <span class="sidebar-label">Gestion</span>
      <a href="{{ route('admin.users') }}"
         class="sidebar-link {{ request()->routeIs('admin.users') ? 'active':'' }}">
        <i class="fas fa-users icon"></i> Clients &amp; Utilisateurs
      </a>

      <span class="sidebar-label">Compte</span>
      <a href="{{ route('home',['locale'=>app()->getLocale()]) }}" class="sidebar-link">
        <i class="fas fa-globe icon"></i> Retour au site
      </a>
    @endif

  @endauth
  </nav>

  <div class="sidebar-footer">
    @if(Auth::check() && Auth::user()->type === 'staff')
    <form action="{{ route('staff.logout') }}" method="POST">
    @else
    <form action="{{ route('logout') }}" method="POST">
    @endif
      @csrf
      <button type="submit" class="sidebar-logout">
        <i class="fas fa-sign-out-alt"></i> Déconnexion
      </button>
    </form>
  </div>
</aside>

<!-- ═══════════════════ MAIN ═══════════════════ -->
<div class="main-wrap">

  <header class="topbar">
    <div class="topbar-left">
      <button class="topbar-toggle" onclick="openSidebar()"><i class="fas fa-bars"></i></button>
      <span class="topbar-title">@yield('page_title','Dashboard')</span>
    </div>
    <div class="topbar-right">
      <div class="topbar-badge" title="Notifications">
        <i class="fas fa-bell"></i>
      </div>
      <div class="topbar-avatar" title="{{ Auth::user()->name ?? '' }}">
        {{ strtoupper(substr(Auth::user()->name??'U',0,1)) }}
      </div>
    </div>
  </header>

  <main class="content-area">

    @if(session('success'))
    <div class="flash flash-ok"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="flash flash-err"><i class="fas fa-exclamation-triangle"></i> {{ session('error') }}</div>
    @endif

    @yield('content')
  </main>
</div>

<script src="{{ asset('assets/vendors/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script>
function openSidebar()  { document.getElementById('sidebar').classList.add('open'); document.getElementById('sidebarOverlay').classList.add('show'); }
function closeSidebar() { document.getElementById('sidebar').classList.remove('open'); document.getElementById('sidebarOverlay').classList.remove('show'); }
</script>
@stack('scripts')
</body>
</html>
