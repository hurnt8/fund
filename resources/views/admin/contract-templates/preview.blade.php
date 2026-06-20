<!DOCTYPE html>
<html lang="{{ $locale ?? 'fr' }}">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Aperçu — {{ $template->name }}</title>
<meta name="csrf-token" content="{{ csrf_token() }}">

<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

/* ── Barre fixe ─────────────────────────────────────────────────────── */
#pv-bar {
  position: fixed; top: 0; left: 0; right: 0; z-index: 99999;
  height: 50px; background: #0B1A2E;
  display: flex; align-items: center; gap: .75rem; padding: 0 1.25rem;
  box-shadow: 0 1px 0 rgba(255,255,255,.07), 0 4px 24px rgba(0,0,0,.5);
  font-family: Inter, -apple-system, BlinkMacSystemFont, sans-serif;
}
#pv-bar .pv-badge {
  flex-shrink: 0; background: #C8A951; color: #0B1A2E;
  font-size: .58rem; font-weight: 800; letter-spacing: .09em; text-transform: uppercase;
  padding: .18rem .48rem; border-radius: 3px;
}
#pv-bar .pv-type {
  flex-shrink: 0; background: rgba(255,255,255,.08); color: rgba(255,255,255,.5);
  font-size: .58rem; font-weight: 700; letter-spacing: .07em; text-transform: uppercase;
  padding: .16rem .44rem; border-radius: 3px; border: 1px solid rgba(255,255,255,.14);
}
#pv-bar .pv-type-html { background: rgba(59,130,246,.15); color: #93c5fd; border-color: rgba(59,130,246,.25); }
#pv-bar .pv-name {
  font-size: .78rem; font-weight: 600; color: #fff;
  white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 200px; flex-shrink: 1;
}
#pv-bar .pv-div { width: 1px; height: 18px; background: rgba(255,255,255,.12); flex-shrink: 0; }
#pv-bar .pv-spacer { flex: 1; }
.pv-langs { display: flex; gap: 2px; align-items: center; }
.pv-lang {
  padding: .25rem .5rem; border-radius: 4px; font-size: .67rem; font-weight: 700;
  letter-spacing: .05em; text-transform: uppercase; text-decoration: none;
  color: rgba(255,255,255,.45); transition: all .12s; border: 1px solid transparent;
}
.pv-lang:hover { color: #fff; background: rgba(255,255,255,.09); }
.pv-lang-active { background: #C8A951 !important; color: #0B1A2E !important; border-color: #C8A951 !important; }
.pv-gens { display: flex; border: 1px solid rgba(255,255,255,.15); border-radius: 5px; overflow: hidden; }
.pv-gen {
  padding: .26rem .6rem; font-size: .78rem; text-decoration: none;
  color: rgba(255,255,255,.4); background: transparent; transition: all .12s;
}
.pv-gen:hover { background: rgba(255,255,255,.1); color: #fff; }
.pv-gen-active { background: rgba(255,255,255,.14) !important; color: #fff !important; }
.pv-act {
  display: inline-flex; align-items: center; gap: .3rem; padding: .3rem .72rem;
  border-radius: 5px; font-size: .7rem; font-weight: 600; font-family: inherit;
  text-decoration: none; cursor: pointer; white-space: nowrap; border: 1px solid transparent; transition: all .14s;
}
.pv-act-gold  { background: #C8A951; color: #0B1A2E; border-color: #C8A951; }
.pv-act-gold:hover { background: #d4b55f; border-color: #d4b55f; }
.pv-act-close { background: transparent; color: rgba(255,255,255,.55); border-color: rgba(255,255,255,.18); }
.pv-act-close:hover { background: rgba(255,255,255,.08); color: #fff; }

/* ── Layout global ──────────────────────────────────────────────────── */
body {
  font-family: 'DejaVu Sans', Arial, sans-serif;
  height: 100vh; overflow: hidden; background: #0F172A;
  display: flex; flex-direction: column;
}
.pv-layout {
  display: flex;
  height: calc(100vh - 50px);
  margin-top: 50px;
  overflow: hidden;
}

/* ── Sidebar gauche ─────────────────────────────────────────────────── */
.pv-sidebar {
  width: 280px; flex-shrink: 0;
  height: 100%; display: flex; flex-direction: column;
  background: #0F172A; border-right: 1px solid rgba(255,255,255,.07);
  overflow: hidden;
}
.sb-header {
  padding: 10px 12px; border-bottom: 1px solid rgba(255,255,255,.07); flex-shrink: 0;
}
.sb-header-title {
  font-size: .72rem; font-weight: 700; color: #CBD5E1; margin-bottom: 2px;
  font-family: Inter, sans-serif;
}
.sb-header-sub { font-size: .62rem; color: rgba(255,255,255,.28); }
.sb-search {
  padding: 8px 10px; border-bottom: 1px solid rgba(255,255,255,.06); flex-shrink: 0;
}
.sb-search input {
  width: 100%; background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.11);
  border-radius: 6px; padding: 6px 10px; color: #CBD5E1;
  font-size: .72rem; outline: none; font-family: Inter, sans-serif;
  transition: border-color .15s;
}
.sb-search input:focus { border-color: rgba(200,169,81,.5); }
.sb-search input::placeholder { color: rgba(255,255,255,.22); }
.sb-list { flex: 1; overflow-y: auto; padding: 4px 0 8px; }
.sb-list::-webkit-scrollbar { width: 4px; }
.sb-list::-webkit-scrollbar-track { background: transparent; }
.sb-list::-webkit-scrollbar-thumb { background: rgba(255,255,255,.1); border-radius: 2px; }

.sb-cat-title {
  font-size: .59rem; font-weight: 700; text-transform: uppercase; letter-spacing: .08em;
  color: rgba(255,255,255,.25); padding: 10px 12px 3px;
  font-family: Inter, sans-serif;
}
.sb-item {
  padding: 5px 8px 5px 12px; display: flex; align-items: center; gap: 6px;
  cursor: pointer; border-radius: 5px; margin: 1px 5px;
  transition: background .1s;
}
.sb-item:hover { background: rgba(255,255,255,.06); }
.sb-item-info { flex: 1; min-width: 0; }
.sb-item code {
  font-size: .67rem; color: #C8A951; font-weight: 700; display: block;
  white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.sb-item span {
  font-size: .6rem; color: rgba(255,255,255,.27); display: block;
  white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.sb-item.is-unknown code { color: #FB923C; }
.sb-insert-btn {
  flex-shrink: 0; width: 20px; height: 20px; background: none;
  border: 1px solid rgba(200,169,81,.2); border-radius: 4px;
  color: rgba(200,169,81,.4); cursor: pointer; font-size: .7rem;
  display: flex; align-items: center; justify-content: center; transition: all .12s;
  opacity: 0;
}
.sb-item:hover .sb-insert-btn { opacity: 1; color: #C8A951; border-color: rgba(200,169,81,.55); }
.sb-insert-btn:hover { background: rgba(200,169,81,.14) !important; }

.sb-footer {
  padding: 10px 10px; border-top: 1px solid rgba(255,255,255,.07); flex-shrink: 0;
  display: flex; flex-direction: column; gap: 6px;
}
.sb-status {
  text-align: center; font-size: .62rem; font-family: Inter, sans-serif;
  color: rgba(255,255,255,.27); min-height: 14px; transition: color .2s;
}
.btn-sb-save {
  width: 100%; padding: .42rem .875rem; background: #C8A951; color: #0B1A2E;
  border: none; border-radius: 6px; font-size: .73rem; font-weight: 700;
  cursor: pointer; display: flex; align-items: center; justify-content: center; gap: .4rem;
  font-family: Inter, sans-serif; transition: background .14s;
}
.btn-sb-save:hover { background: #d4b55f; }
.btn-sb-save:disabled { opacity: .5; cursor: not-allowed; }
.btn-sb-dl {
  width: 100%; padding: .38rem .875rem; background: rgba(255,255,255,.05);
  color: rgba(255,255,255,.6); border: 1px solid rgba(255,255,255,.11);
  border-radius: 6px; font-size: .7rem; font-weight: 600;
  cursor: pointer; display: flex; align-items: center; justify-content: center; gap: .4rem;
  font-family: Inter, sans-serif; text-decoration: none; transition: all .14s;
}
.btn-sb-dl:hover { background: rgba(255,255,255,.1); color: #fff; }
.sb-docx-hint {
  font-size: .6rem; color: rgba(255,255,255,.2); text-align: center; line-height: 1.45;
}

/* ── Zone principale ─────────────────────────────────────────────────── */
.pv-main {
  flex: 1; height: 100%; overflow: hidden; background: #D9DCE5;
  display: flex; flex-direction: column;
}
.pv-main-scroll {
  flex: 1; overflow-y: auto;
}
.pv-main-scroll::-webkit-scrollbar { width: 6px; }
.pv-main-scroll::-webkit-scrollbar-track { background: rgba(0,0,0,.1); }
.pv-main-scroll::-webkit-scrollbar-thumb { background: rgba(0,0,0,.2); border-radius: 3px; }
.pv-wrap { padding: 24px 20px 60px; display: flex; justify-content: center; }

/* ── Feuille A4 (HTML templates) ────────────────────────────────────── */
.pv-sheet {
  background: #fff; width: 794px; max-width: 100%; min-height: 1122px;
  position: relative;
  box-shadow: 0 0 0 1px rgba(0,0,0,.08), 0 2px 6px rgba(0,0,0,.06), 0 12px 48px rgba(0,0,0,.15);
}
.pv-watermark {
  position: absolute; inset: 0; pointer-events: none; z-index: 0;
  background-size: contain; background-position: center; background-repeat: no-repeat; opacity: .10;
}
.pv-logo-left, .pv-logo-right {
  position: absolute; top: 30px; z-index: 2; max-height: 70px; max-width: 130px; object-fit: contain;
}
.pv-logo-left { left: 36px; } .pv-logo-right { right: 36px; }
.pv-content {
  position: relative; z-index: 1; padding: 52px 58px 60px; outline: none;
  min-height: 900px;
}
.pv-content[contenteditable="true"]:focus-within {
  box-shadow: inset 0 0 0 2px rgba(200,169,81,.18);
}

/* ── Chips balises ──────────────────────────────────────────────────── */
.balise-chip {
  display: inline; background: rgba(200,169,81,.11); color: #B8941E;
  border: 1px solid rgba(200,169,81,.32); border-radius: 3px;
  padding: 0 4px 1px; font-family: 'Courier New', monospace;
  font-size: .82em; font-weight: 700; cursor: default; user-select: none; white-space: nowrap;
}

/* ── CSS contrat (HTML) ─────────────────────────────────────────────── */
.header { text-align: center; border-bottom: 3px solid #0B1A2E; padding-bottom: 14px; margin-bottom: 22px; padding-top: 8px; }
.header-country { font-size: 9px; color: #555; white-space: pre-line; margin-bottom: 9px; line-height: 1.65; }
.header-title { font-size: 18px; font-weight: 700; color: #0B1A2E; letter-spacing: 1.2px; margin-bottom: 6px; }
.header-ref { font-size: 9px; color: #555; }
.section-title { font-size: 10px; font-weight: 700; color: #C8A951; text-transform: uppercase; letter-spacing: 1.2px; border-bottom: 1px solid #e0c97a; margin: 20px 0 10px; padding-bottom: 3px; }
.parties-block { margin-bottom: 11px; font-size: 11px; line-height: 1.78; }
.party-label { font-weight: 700; color: #0B1A2E; }
.finance-table { width: 100%; border-collapse: collapse; margin: 10px 0 18px; }
.finance-table td { padding: 6px 11px; border: 1px solid #E2E6F0; font-size: 10.5px; }
.finance-table td:first-child { background: #F6F8FC; font-weight: 700; width: 56%; color: #0B1A2E; }
.finance-table td:last-child { font-weight: 700; color: #C8A951; font-size: 11.5px; }
.article { margin-bottom: 14px; }
.article-title { font-weight: 700; color: #0B1A2E; font-size: 10.5px; margin-bottom: 5px; }
.article-body { white-space: pre-line; font-size: 10.5px; color: #2A2A40; line-height: 1.82; }
.signature-block { margin-top: 34px; border-top: 2px solid #0B1A2E; padding-top: 14px; }
.sig-date { margin-bottom: 22px; font-size: 10px; }
.sig-row { display: table; width: 100%; }
.sig-cell { display: table-cell; width: 33%; text-align: center; padding: 10px 6px; }
.sig-label { font-weight: 700; font-size: 9px; text-transform: uppercase; color: #0B1A2E; border-top: 1px solid #ccc; padding-top: 5px; margin-top: 52px; }
.sig-img { max-height: 52px; max-width: 120px; object-fit: contain; display: block; margin: 0 auto 6px; }
.stamp-img { max-height: 80px; max-width: 80px; object-fit: contain; display: block; margin: 16px auto 0; }
.company-name { font-size: 13px; font-weight: 700; color: #0B1A2E; letter-spacing: 2px; }

/* ── Barre d'outils formatage DOCX ─────────────────────────────────── */
.docx-toolbar {
  flex-shrink: 0;
  background: #1E293B; border-bottom: 1px solid rgba(255,255,255,.09);
  display: flex; align-items: center; gap: 2px; padding: 5px 10px; flex-wrap: wrap;
  z-index: 50;
}
.tb-sep { width: 1px; height: 18px; background: rgba(255,255,255,.12); margin: 0 3px; flex-shrink: 0; }
.tb-btn {
  width: 28px; height: 26px; background: transparent; border: 1px solid transparent;
  border-radius: 4px; color: rgba(255,255,255,.65); cursor: pointer;
  display: flex; align-items: center; justify-content: center;
  font-size: .75rem; font-weight: 700; font-family: Georgia, serif;
  transition: all .1s; padding: 0; flex-shrink: 0;
}
.tb-btn:hover  { background: rgba(255,255,255,.1); border-color: rgba(255,255,255,.15); color: #fff; }
.tb-btn.active { background: rgba(200,169,81,.2); border-color: rgba(200,169,81,.45); color: #C8A951; }
.tb-btn svg { width: 13px; height: 13px; flex-shrink: 0; fill: none; stroke: currentColor; stroke-width: 2; }
.tb-select {
  height: 26px; background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.12);
  border-radius: 4px; color: rgba(255,255,255,.75); font-size: .67rem; font-family: Inter, sans-serif;
  padding: 0 6px; cursor: pointer; outline: none; min-width: 54px;
}
.tb-select:hover { border-color: rgba(255,255,255,.25); }
.tb-select:focus { border-color: rgba(200,169,81,.4); }
.tb-select option { background: #1E293B; }
.tb-color-wrap {
  position: relative; height: 26px; width: 30px; flex-shrink: 0;
  border: 1px solid rgba(255,255,255,.12); border-radius: 4px;
  cursor: pointer; display: flex; flex-direction: column;
  align-items: center; justify-content: center; gap: 0; overflow: hidden;
  background: transparent;
}
.tb-color-wrap:hover { border-color: rgba(255,255,255,.3); }
.tb-color-wrap input[type=color] {
  opacity: 0; position: absolute; inset: 0; width: 100%; height: 100%;
  cursor: pointer; border: none; padding: 0;
}
.tb-color-letter { font-size: .78rem; font-weight: 900; color: rgba(255,255,255,.7); pointer-events: none; line-height: 1; font-family: Georgia, serif; }
.tb-color-bar { width: 70%; height: 3px; border-radius: 1px; pointer-events: none; margin-top: 1px; }

/* ── DOCX pages A4 ──────────────────────────────────────────────────── */
.docx-pages-wrap {
  display: flex; flex-direction: column; align-items: center; gap: 28px;
  width: 100%; padding: 20px 0 40px;
}
.docx-a4-page {
  background: #fff;
  width: 794px; max-width: 100%; min-height: 1123px;
  box-shadow: 0 0 0 1px rgba(0,0,0,.1), 0 4px 14px rgba(0,0,0,.1), 0 16px 60px rgba(0,0,0,.2);
  position: relative;
  display: flex; flex-direction: column;
  overflow: hidden;
}
.docx-content {
  flex: 1;
  /* A4 marges étroites Word : 1.27 cm = 48px à 96 dpi */
  padding: 48px;
  outline: none;
  /* font-family injectée dynamiquement depuis le DOCX (detectDefaultFont) */
  font-family: 'DejaVu Sans', Arial, sans-serif;
  font-size: 11pt;
  line-height: 1.5;
  color: #000;
  position: relative;
  min-height: calc(1123px - 36px);
  word-break: break-word;
}
.docx-content:focus { outline: none; }
/* Highlight active editing page */
.docx-content[contenteditable="true"]:focus {
  box-shadow: inset 0 0 0 2px rgba(200,169,81,.3);
}
/* List rendering */
.docx-content ul,
.docx-content ol { padding-left: 2em; margin: .3em 0; }
.docx-content ul { list-style-type: disc; }
.docx-content ol { list-style-type: decimal; }
.docx-content ul ul  { list-style-type: circle; }
.docx-content ul ul ul { list-style-type: square; }
.docx-content ol ol  { list-style-type: lower-alpha; }
.docx-content li { margin: .15em 0; }
/* phpoffice paragraph spacing */
.docx-content p { margin: 0; padding: 0; }
/* Tables from phpoffice/LibreOffice */
.docx-content table { border-collapse: collapse; width: 100%; margin: .5em 0; }
.docx-content td, .docx-content th { border: 1px solid #ccc; padding: 4px 6px; }
/* LibreOffice font elements */
.docx-content font { display: inline; }
/* LibreOffice divs (sections) */
.docx-content div { display: block; }
/* Preserve text alignment from LO */
.docx-content p[align="center"] { text-align: center; }
.docx-content p[align="right"]  { text-align: right; }
.docx-content p[align="left"]   { text-align: left; }
.docx-content p[align="justify"]{ text-align: justify; }
/* All images in DOCX pages are draggable */
.docx-a4-page img:not([data-docx-auto]) { cursor: grab; user-select: none; }
.docx-a4-page img:not([data-docx-auto])[style*="position:absolute"] {
  box-shadow: 0 0 0 1.5px rgba(200,169,81,.5), 0 2px 12px rgba(0,0,0,.2);
}
/* DOCX overlay images — positionnées directement dans .docx-a4-page */
.docx-a4-page > img.crx-auto-img {
  cursor: grab;
  transition: box-shadow .1s;
}
.docx-a4-page > img.crx-auto-img:hover,
.docx-a4-page > img.crx-auto-img.crx-selected {
  box-shadow: 0 0 0 2px #C8A951, 0 2px 14px rgba(0,0,0,.3);
  outline: none;
  z-index: 20 !important;
}

.docx-page-num {
  height: 32px; display: flex; align-items: center; justify-content: center;
  font-size: .6rem; color: #94a3b8; font-family: Inter, sans-serif;
  border-top: 1px solid #e2e8f0; background: #f8fafc; flex-shrink: 0;
}

/* ── Barre de contrôle image flottante ──────────────────────────── */
#imgCtrl {
  position: fixed; z-index: 99500; display: none;
  align-items: center; gap: 5px; padding: 5px 8px;
  background: #1E293B; border: 1px solid rgba(255,255,255,.18);
  border-radius: 7px; box-shadow: 0 6px 24px rgba(0,0,0,.5);
  font-family: Inter, sans-serif; pointer-events: all;
}
#imgCtrl .ic-label {
  font-size: .58rem; color: rgba(255,255,255,.35); white-space: nowrap;
}
#imgCtrl .ic-sep { width:1px; height:18px; background:rgba(255,255,255,.12); margin:0 2px; flex-shrink:0; }
#imgCtrl input[type=range] {
  -webkit-appearance: none; width: 72px; height: 4px; border-radius: 2px;
  background: rgba(255,255,255,.18); outline: none; cursor: pointer;
}
#imgCtrl input[type=range]::-webkit-slider-thumb {
  -webkit-appearance: none; width: 12px; height: 12px; border-radius: 50%;
  background: #C8A951; cursor: pointer;
}
#imgCtrl .ic-val {
  font-size: .6rem; color: rgba(255,255,255,.55); min-width: 28px; text-align: right;
}
#imgCtrl button {
  width: 24px; height: 22px; background: transparent;
  border: 1px solid rgba(255,255,255,.12); border-radius: 4px;
  color: rgba(255,255,255,.6); cursor: pointer; font-size: .72rem;
  display: flex; align-items: center; justify-content: center; flex-shrink: 0;
  transition: all .1s;
}
#imgCtrl button:hover { background: rgba(255,255,255,.1); color: #fff; border-color: rgba(255,255,255,.3); }
#imgCtrl button.ic-del { border-color: rgba(251,113,133,.25); color: #F87171; }
#imgCtrl button.ic-del:hover { background: rgba(251,113,133,.12); }

.docx-edit-banner {
  background: rgba(200,169,81,.08); border: 1px solid rgba(200,169,81,.22);
  border-radius: 5px; padding: 6px 10px; margin: 12px 0 4px;
  font-size: .62rem; color: #C8A951; display: flex; align-items: center; gap: 6px;
}
.docx-spinner-overlay {
  display: flex; flex-direction: column; align-items: center; gap: 12px;
  padding: 60px 0; color: #64748B; font-size: .75rem; font-family: Inter, sans-serif;
}
@keyframes spin { to { transform: rotate(360deg); } }
.docx-spinner-overlay svg { animation: spin 1s linear infinite; }

/* ── Boutons de mode (HTML) ─────────────────────────────────────────── */
.mode-toggle {
  position: fixed; bottom: 20px; right: 20px; z-index: 9000;
  display: flex; gap: 5px; align-items: center;
  background: rgba(11,26,46,.88); border: 1px solid rgba(255,255,255,.13);
  border-radius: 50px; padding: 4px 5px; backdrop-filter: blur(8px);
  box-shadow: 0 4px 20px rgba(0,0,0,.35);
}
.mode-btn {
  padding: .3rem .75rem; border-radius: 50px; font-size: .67rem; font-weight: 700;
  font-family: Inter, sans-serif; cursor: pointer;
  border: none; background: transparent; color: rgba(255,255,255,.45); transition: all .15s;
}
.mode-btn.active { background: #C8A951; color: #0B1A2E; }
.mode-btn:not(.active):hover { color: #fff; }
</style>
</head>
<body>

{{-- Barre de navigation --}}
{!! $barHtml !!}

<div class="pv-layout">

  {{-- ── SIDEBAR GAUCHE ─────────────────────────────────────────────── --}}
  <aside class="pv-sidebar">

    @if($isDocx)
    {{-- DOCX : header avec compteur de pages + balises --}}
    <div class="sb-header">
      <div class="sb-header-title">Document DOCX</div>
      <div class="sb-header-sub" id="docxPageCount" style="font-size:.65rem;color:#C8A951;margin-bottom:3px">
        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="animation:spin 1s linear infinite;vertical-align:middle">
          <path d="M21 12a9 9 0 1 1-6.219-8.56"/>
        </svg>
        Chargement…
      </div>
      <div class="sb-header-sub">{{ count($detectedBalises ?? []) }} balise(s) dans le fichier</div>
    </div>
    <div class="sb-list">
      @forelse($detectedBalises ?? [] as $tag => $desc)
      <div class="sb-item {{ $desc ? '' : 'is-unknown' }}">
        <div class="sb-item-info">
          <code>{{ $tag }}</code>
          <span>{{ $desc ?? 'Balise personnalisée (non reconnue)' }}</span>
        </div>
        <span style="font-size:.6rem;font-weight:700;color:{{ $desc ? '#4ADE80' : '#FB923C' }};flex-shrink:0">
          {{ $desc ? '✓' : '?' }}
        </span>
      </div>
      @empty
      <div style="padding:20px 12px;text-align:center;font-size:.68rem;color:rgba(255,255,255,.25)">
        Aucune balise détectée
      </div>
      @endforelse
    </div>
    <div class="sb-footer">
      <div class="sb-status" id="docxSaveStatus"></div>
      <div id="docxEditBadge" style="display:none" class="docx-edit-banner">
        ✏ Version éditée active
      </div>
      <button class="btn-sb-save" id="docxSaveBtn" onclick="saveDocxEdit()">
        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
          <polyline points="17 21 17 13 7 13 7 21"/>
          <polyline points="7 3 7 8 15 8"/>
        </svg>
        Enregistrer les modifications
      </button>
      <button id="docxResetBtn" onclick="resetDocxToOriginal()"
              style="width:100%;padding:.35rem;background:none;border:1px solid rgba(255,255,255,.1);border-radius:5px;color:rgba(255,255,255,.38);font-size:.65rem;cursor:pointer;font-family:Inter,sans-serif;transition:all .12s;margin-top:2px"
              onmouseover="this.style.color='rgba(255,255,255,.7)';this.style.borderColor='rgba(255,255,255,.25)'"
              onmouseout="this.style.color='rgba(255,255,255,.38)';this.style.borderColor='rgba(255,255,255,.1)'">
        ↺ Revenir au DOCX original
      </button>
      <a href="{{ ($downloadUrl ?? '#') . '?locale=' . $locale . '&gender=' . ($isFem ? 'F' : 'M') }}" class="btn-sb-dl">
        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
          <polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/>
        </svg>
        Télécharger le DOCX
      </a>
    </div>

    @else
    {{-- HTML : liste complète des balises disponibles + recherche --}}
    <div class="sb-search">
      <input type="text" id="baliseSearch" placeholder="Rechercher une balise…"
             oninput="filterBalises(this.value)">
    </div>
    <div class="sb-list" id="sidebarList">
      @foreach($categorizedVars ?? [] as $catName => $catVars)
      <div class="sb-cat-group">
        <div class="sb-cat-title">{{ $catName }}</div>
        @foreach($catVars as $var => $desc)
        <div class="sb-item"
             data-search="{{ strtolower($var . ' ' . $desc) }}"
             onclick="insertBalise('{{ $var }}')">
          <div class="sb-item-info">
            <code>{{ $var }}</code>
            <span>{{ $desc }}</span>
          </div>
          <button class="sb-insert-btn" title="Insérer au curseur"
                  onclick="event.stopPropagation(); insertBalise('{{ $var }}')">⊕</button>
        </div>
        @endforeach
      </div>
      @endforeach
    </div>
    <div class="sb-footer">
      <div class="sb-status" id="saveStatus">Cliquez sur le texte du document pour éditer</div>
      <button class="btn-sb-save" id="saveBtn" onclick="saveContent()">
        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
          <polyline points="17 21 17 13 7 13 7 21"/>
          <polyline points="7 3 7 8 15 8"/>
        </svg>
        Enregistrer (Ctrl+S)
      </button>
    </div>
    @endif

  </aside>

  {{-- ── ZONE PRINCIPALE ─────────────────────────────────────────────── --}}
  <main class="pv-main">

    @if($isDocx)
    {{-- ── Barre d'outils Word (DOCX uniquement) ─────────────────────── --}}
    <div class="docx-toolbar" id="docxToolbar">

      {{-- Police / Taille --}}
      <select class="tb-select" id="tbFontFamily" style="min-width:90px" title="Police" onchange="fmtFont(this.value)">
        <option value="">— Police —</option>
        <option value="'Calibri', sans-serif">Calibri</option>
        <option value="'Times New Roman', serif">Times New Roman</option>
        <option value="Arial, sans-serif">Arial</option>
        <option value="'Courier New', monospace">Courier New</option>
        <option value="Georgia, serif">Georgia</option>
        <option value="Verdana, sans-serif">Verdana</option>
        <option value="'Agency FB', sans-serif">Agency FB</option>
      </select>
      <select class="tb-select" id="tbFontSize" title="Taille" onchange="fmtFontSize(this.value)">
        <option value="">Taille</option>
        <option value="8pt">8</option>
        <option value="9pt">9</option>
        <option value="10pt">10</option>
        <option value="11pt">11</option>
        <option value="12pt">12</option>
        <option value="14pt">14</option>
        <option value="16pt">16</option>
        <option value="18pt">18</option>
        <option value="20pt">20</option>
        <option value="24pt">24</option>
        <option value="28pt">28</option>
        <option value="36pt">36</option>
        <option value="48pt">48</option>
      </select>

      <span class="tb-sep"></span>

      {{-- Gras / Italique / Souligné / Barré --}}
      <button class="tb-btn" id="tb-bold"          title="Gras (Ctrl+B)"         onclick="fmtCmd('bold')">
        <b style="font-family:Georgia,serif;font-size:.8rem">G</b>
      </button>
      <button class="tb-btn" id="tb-italic"        title="Italique (Ctrl+I)"     onclick="fmtCmd('italic')">
        <i style="font-family:Georgia,serif;font-size:.8rem">I</i>
      </button>
      <button class="tb-btn" id="tb-underline"     title="Souligné (Ctrl+U)"     onclick="fmtCmd('underline')">
        <span style="text-decoration:underline;font-family:Georgia,serif;font-size:.8rem">S</span>
      </button>
      <button class="tb-btn" id="tb-strikeThrough" title="Barré"                 onclick="fmtCmd('strikeThrough')">
        <span style="text-decoration:line-through;font-size:.8rem">B</span>
      </button>

      <span class="tb-sep"></span>

      {{-- Couleur texte / surlignage --}}
      <div class="tb-color-wrap" title="Couleur du texte">
        <span class="tb-color-letter">A</span>
        <div class="tb-color-bar" id="tbColorBar" style="background:#e11d48"></div>
        <input type="color" id="tbTextColor" value="#e11d48" onchange="fmtColor('foreColor', this.value)" oninput="document.getElementById('tbColorBar').style.background=this.value">
      </div>
      <div class="tb-color-wrap" title="Couleur de surlignage" style="background:rgba(250,230,60,.08)">
        <span class="tb-color-letter" style="font-size:.6rem;color:rgba(255,220,60,.8)">H</span>
        <div class="tb-color-bar" id="tbHlBar" style="background:#fde047"></div>
        <input type="color" id="tbHlColor" value="#fde047" onchange="fmtColor('hiliteColor', this.value)" oninput="document.getElementById('tbHlBar').style.background=this.value">
      </div>

      <span class="tb-sep"></span>

      {{-- Alignements --}}
      <button class="tb-btn" id="tb-justifyLeft"   title="Aligner à gauche" onclick="fmtCmd('justifyLeft')">
        <svg viewBox="0 0 24 24"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="15" y2="12"/><line x1="3" y1="18" x2="18" y2="18"/></svg>
      </button>
      <button class="tb-btn" id="tb-justifyCenter" title="Centrer"          onclick="fmtCmd('justifyCenter')">
        <svg viewBox="0 0 24 24"><line x1="3" y1="6" x2="21" y2="6"/><line x1="6" y1="12" x2="18" y2="12"/><line x1="4" y1="18" x2="20" y2="18"/></svg>
      </button>
      <button class="tb-btn" id="tb-justifyRight"  title="Aligner à droite" onclick="fmtCmd('justifyRight')">
        <svg viewBox="0 0 24 24"><line x1="3" y1="6" x2="21" y2="6"/><line x1="9" y1="12" x2="21" y2="12"/><line x1="6" y1="18" x2="21" y2="18"/></svg>
      </button>
      <button class="tb-btn" id="tb-justifyFull"   title="Justifier"        onclick="fmtCmd('justifyFull')">
        <svg viewBox="0 0 24 24"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
      </button>

      <span class="tb-sep"></span>

      {{-- Listes --}}
      <button class="tb-btn" id="tb-ul" title="Liste à puces" onclick="fmtList('ul')">
        <svg viewBox="0 0 24 24"><line x1="9" y1="6" x2="20" y2="6"/><line x1="9" y1="12" x2="20" y2="12"/><line x1="9" y1="18" x2="20" y2="18"/><circle cx="4" cy="6" r="1.5" fill="currentColor" stroke="none"/><circle cx="4" cy="12" r="1.5" fill="currentColor" stroke="none"/><circle cx="4" cy="18" r="1.5" fill="currentColor" stroke="none"/></svg>
      </button>
      <button class="tb-btn" id="tb-ol" title="Liste numérotée" onclick="fmtList('ol')">
        <svg viewBox="0 0 24 24"><line x1="10" y1="6" x2="21" y2="6"/><line x1="10" y1="12" x2="21" y2="12"/><line x1="10" y1="18" x2="21" y2="18"/><text x="2" y="8.5" font-size="7" fill="currentColor" stroke="none" font-family="sans-serif">1.</text><text x="2" y="14" font-size="7" fill="currentColor" stroke="none" font-family="sans-serif">2.</text><text x="2" y="19.5" font-size="7" fill="currentColor" stroke="none" font-family="sans-serif">3.</text></svg>
      </button>

      {{-- Indentation --}}
      <button class="tb-btn" title="Augmenter le retrait" onclick="fmtCmd('indent')">
        <svg viewBox="0 0 24 24"><line x1="3" y1="6" x2="21" y2="6"/><line x1="9" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/><polyline points="3 9 7 12 3 15"/></svg>
      </button>
      <button class="tb-btn" title="Diminuer le retrait" onclick="fmtCmd('outdent')">
        <svg viewBox="0 0 24 24"><line x1="3" y1="6" x2="21" y2="6"/><line x1="9" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/><polyline points="7 9 3 12 7 15"/></svg>
      </button>

      <span class="tb-sep"></span>

      {{-- Supprimer la mise en forme --}}
      <button class="tb-btn" title="Supprimer la mise en forme" onclick="fmtCmd('removeFormat')">
        <svg viewBox="0 0 24 24"><polyline points="4 7 4 4 20 4 20 7"/><line x1="9" y1="20" x2="15" y2="20"/><line x1="12" y1="4" x2="12" y2="20"/><line x1="18" y1="18" x2="22" y2="22"/></svg>
      </button>

      <span class="tb-sep"></span>

      {{-- Insérer une image --}}
      <button class="tb-btn" title="Insérer une image" onclick="document.getElementById('docxImgInput').click()" style="width:auto;padding:0 7px;gap:4px;font-family:Inter,sans-serif;font-size:.67rem;font-weight:600;color:rgba(255,255,255,.7)">
        <svg viewBox="0 0 24 24" style="width:12px;height:12px"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5" fill="currentColor" stroke="none"/><polyline points="21 15 16 10 5 21"/></svg>
        Image
      </button>

      {{-- Insérer un filigrane --}}
      <button class="tb-btn" title="Définir le filigrane de la page" onclick="document.getElementById('docxWmInput').click()" style="width:auto;padding:0 7px;gap:4px;font-family:Inter,sans-serif;font-size:.67rem;font-weight:600;color:rgba(255,255,255,.55)">
        <svg viewBox="0 0 24 24" style="width:12px;height:12px"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
        Filigrane
      </button>

      {{-- Supprimer le filigrane --}}
      <button class="tb-btn" id="tbRemoveWm" title="Supprimer le filigrane" onclick="removeWatermark()" style="display:none;width:auto;padding:0 6px;font-family:Inter,sans-serif;font-size:.62rem;color:rgba(251,146,60,.8)">
        ✕ Filigrane
      </button>

      {{-- Inputs fichier (cachés) --}}
      <input type="file" id="docxImgInput" accept="image/*" style="display:none">
      <input type="file" id="docxWmInput"  accept="image/*" style="display:none">

    </div>
    @endif

    <div class="pv-main-scroll">
      <div class="pv-wrap">

        @if($isDocx)
        {{-- DOCX : pages A4 directement éditables --}}
        <div class="docx-pages-wrap" id="docxPagesWrap">
          <div class="docx-spinner-overlay" id="docxSpinner">
            <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="#C8A951" stroke-width="2">
              <path d="M21 12a9 9 0 1 1-6.219-8.56"/>
            </svg>
            Rendu du document en cours…
          </div>
        </div>

        @else
        {{-- HTML : feuille A4 directement éditable --}}
        @php
          $imgs      = $imgs ?? [];
          $hasLogos  = ($imgs['logo_left_path'] ?? null) || ($imgs['logo_right_path'] ?? null);
          $hasSigs   = ($imgs['signature_admin_path'] ?? null)
                    || ($imgs['signature_agent_path'] ?? null)
                    || ($imgs['stamp_path'] ?? null);
        @endphp
        <div class="pv-sheet">

          @if($imgs['watermark_path'] ?? null)
          <div class="pv-watermark" style="background-image:url('{{ $imgs['watermark_path'] }}')"></div>
          @endif

          @if($imgs['logo_left_path'] ?? null)
          <img src="{{ $imgs['logo_left_path'] }}" class="pv-logo-left" alt="">
          @endif
          @if($imgs['logo_right_path'] ?? null)
          <img src="{{ $imgs['logo_right_path'] }}" class="pv-logo-right" alt="">
          @endif

          <div class="pv-content"
               id="contentEditor"
               contenteditable="true"
               spellcheck="false"
               style="{{ $hasLogos ? 'padding-top:112px' : '' }}">
            {!! $editableContent ?? '' !!}
          </div>

          @if($hasSigs)
          <div style="padding: 0 58px 40px; display:flex; justify-content:space-around; align-items:flex-end; flex-wrap:wrap; gap:8px; margin-top:12px">
            @if($imgs['signature_admin_path'] ?? null)
            <div style="text-align:center">
              <img src="{{ $imgs['signature_admin_path'] }}" class="sig-img" alt="">
              <div style="font-size:8px;color:#888;text-transform:uppercase;letter-spacing:.04em;margin-top:4px">Signature société</div>
            </div>
            @endif
            @if($imgs['stamp_path'] ?? null)
            <div style="text-align:center">
              <img src="{{ $imgs['stamp_path'] }}" class="stamp-img" alt="">
            </div>
            @endif
            @if($imgs['signature_agent_path'] ?? null)
            <div style="text-align:center">
              <img src="{{ $imgs['signature_agent_path'] }}" class="sig-img" alt="">
              <div style="font-size:8px;color:#888;text-transform:uppercase;letter-spacing:.04em;margin-top:4px">Signature agent</div>
            </div>
            @endif
          </div>
          @endif

        </div>
        @endif

      </div>
    </div>
  </main>
</div>

{{-- Barre de contrôle image flottante (DOCX) --}}
@if($isDocx)
<div id="imgCtrl">
  <span class="ic-label">Image</span>
  <div class="ic-sep"></div>
  <input type="range" id="icWidthRange" min="10" max="100" value="100" step="5"
         oninput="imgSetWidth(this.value)">
  <span class="ic-val" id="icWidthVal">100%</span>
  <div class="ic-sep"></div>
  <button onclick="imgAlign('left')"   title="Aligner à gauche">⇐</button>
  <button onclick="imgAlign('center')" title="Centrer">⊞</button>
  <button onclick="imgAlign('right')"  title="Aligner à droite">⇒</button>
  <div class="ic-sep"></div>
  <button class="ic-del" onclick="imgDelete()" title="Supprimer l'image">✕</button>
</div>
@endif

{{-- Boutons mode Édition / Aperçu (HTML uniquement) --}}
@if(!$isDocx)
<div class="mode-toggle">
  <button class="mode-btn active" id="btnEdit"    onclick="setMode('edit')">✎ Édition</button>
  <button class="mode-btn"        id="btnPreview" onclick="setMode('preview')">👁 Aperçu</button>
</div>
@endif

<script>
const IS_DOCX = @json($isDocx);

@if($isDocx)
/* ═══════════════════════════════════════════════════════════════
   DOCX : rendu paginé inline, édition directe, save / reset
   ═══════════════════════════════════════════════════════════════ */
const DOCX_FRAME_BASE = @json($docxFrameBase ?? '');
const SAVE_URL   = @json(route('admin.contract-templates.save-content', $template));
const RESET_URL  = @json(route('admin.contract-templates.reset-docx-edit', $template));
const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

let currentLocale  = @json($locale ?? 'fr');
let currentGender  = @json($isFem ? 'F' : 'M');
let docxCurrentCSS = '';
let activeEditor   = null; // currently focused .docx-content
let savedRange     = null; // last non-collapsed selection inside an editor
let userWmSrc      = null; // base64 of current user watermark

// Always track the selection so toolbar commands can restore it
document.addEventListener('selectionchange', () => {
  const sel = window.getSelection();
  if (!sel || sel.isCollapsed || !activeEditor) return;
  if (activeEditor.contains(sel.anchorNode)) {
    try { savedRange = sel.getRangeAt(0).cloneRange(); } catch(_) {}
  }
});

// Inject scoped DOCX styles into <head> once
const docxStyleEl = document.createElement('style');
docxStyleEl.id = 'docxScopedStyles';
document.head.appendChild(docxStyleEl);

/* ── Charger et rendre les pages ─────────────────────────────── */
async function loadDocxPages() {
  const spinner = document.getElementById('docxSpinner');
  const wrap    = document.getElementById('docxPagesWrap');

  spinner.style.display = 'flex';
  wrap.querySelectorAll('.docx-a4-page').forEach(p => p.remove());

  const url = DOCX_FRAME_BASE + '?locale=' + currentLocale + '&gender=' + currentGender;

  let data;
  try {
    const res = await fetch(url, { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN } });
    if (!res.ok) throw new Error('HTTP ' + res.status);
    data = await res.json();
  } catch (err) {
    spinner.innerHTML = '<span style="color:#F87171;font-family:Inter,sans-serif;font-size:.75rem">Erreur : ' + err.message + '</span>';
    return;
  }

  docxCurrentCSS = data.styles || '';
  docxStyleEl.textContent = docxCurrentCSS;

  const badge = document.getElementById('docxEditBadge');
  if (badge) badge.style.display = data.hasEdit ? 'flex' : 'none';

  const pages = data.pages || [''];
  pages.forEach((pageHtml, i) => {
    const pageDiv = document.createElement('div');
    pageDiv.className = 'docx-a4-page';

    const content = document.createElement('div');
    content.className = 'docx-content';
    content.contentEditable = 'true';
    content.spellcheck = false;
    content.innerHTML = pageHtml;

    // Move overlays out of content so they're positioned relative to the A4 page
    // .crx-auto-img = each DOCX image (direct, full position:absolute)
    // .crx-watermark = DOCX header watermark
    content.querySelectorAll('.crx-auto-img, .crx-watermark').forEach(el => {
      pageDiv.appendChild(el);
    });

    // Restore user-placed absolute images saved at page level (crx-user-abs)
    content.querySelectorAll('.crx-user-abs').forEach(el => {
      el.querySelectorAll('img').forEach(img => { img.style.cursor = 'grab'; });
      pageDiv.appendChild(el);
    });

    // Track active editor for toolbar state
    content.addEventListener('focus', () => { activeEditor = content; updateToolbarState(); });
    content.addEventListener('keyup', updateToolbarState);
    content.addEventListener('mouseup', updateToolbarState);

    const numDiv = document.createElement('div');
    numDiv.className = 'docx-page-num';
    numDiv.textContent = 'Page ' + (i + 1) + ' / ' + pages.length;

    pageDiv.appendChild(content);
    pageDiv.appendChild(numDiv);
    wrap.appendChild(pageDiv);
  });

  const pc = document.getElementById('docxPageCount');
  if (pc) {
    const n = data.pageCount;
    pc.innerHTML = '<svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="#4ADE80" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg> '
                 + n + ' page' + (n > 1 ? 's' : '') + ' détectée' + (n > 1 ? 's' : '');
    pc.style.color = '#CBD5E1';
  }

  spinner.style.display = 'none';

  // Distribuer les images DOCX vers leur page correcte (data-docx-page=N)
  // Toutes les images sont injectées dans le body et atterrissent sur la page 0 ;
  // on les déplace ici vers les pages suivantes si nécessaire.
  redistributeAutoImages();

  // Auto-split pages that overflow A4 height (LibreOffice generates no page break markers)
  autoSplitOverflowPages();

  // Restore user watermark from saved CSS (persisted as .crx-wm-user rule with base64)
  const wmRuleMatch = docxCurrentCSS.match(/\.crx-wm-user\s*\{[^}]*url\(["']?(data:[^"')]+)["']?\)/);
  if (wmRuleMatch && wmRuleMatch[1]) {
    userWmSrc = wmRuleMatch[1];
    applyUserWatermark();
    const rmBtn = document.getElementById('tbRemoveWm');
    if (rmBtn) rmBtn.style.display = '';
  } else if (userWmSrc) {
    // Watermark set in same session but not yet saved
    applyUserWatermark();
  }
}

/**
 * Déplace les .crx-auto-img depuis la page 0 vers leur page cible (data-docx-page=N).
 * Doit être appelé APRÈS que toutes les pages sont dans le DOM.
 */
function redistributeAutoImages() {
  const wrap = document.getElementById('docxPagesWrap');
  if (!wrap) return;
  const allPages = [...wrap.querySelectorAll('.docx-a4-page')];
  if (allPages.length <= 1) return;

  // All auto images land on page 0 initially (injected at body start)
  [...allPages[0].querySelectorAll('.crx-auto-img')].forEach(img => {
    const targetIdx = parseInt(img.dataset.docxPage || '0');
    if (targetIdx > 0 && targetIdx < allPages.length) {
      allPages[targetIdx].appendChild(img);
    }
  });
}

/**
 * Pour l'HTML LibreOffice (pas de sauts de page côté serveur), détecte les pages
 * qui dépassent la hauteur A4 (1123px) et les découpe visuellement en plusieurs divs.
 * Les images .crx-auto-img (direct children de pageDiv) sont redistribuées par top.
 */
function autoSplitOverflowPages() {
  const PAGE_H = 1123;
  const wrap   = document.getElementById('docxPagesWrap');
  if (!wrap) return;
  const pages  = [...wrap.querySelectorAll('.docx-a4-page')];

  pages.forEach(pageDiv => {
    const content = pageDiv.querySelector('.docx-content');
    if (!content) return;

    requestAnimationFrame(() => {
      const totalH = content.scrollHeight;
      if (totalH <= PAGE_H + 80) return; // tolérance 80px

      // Snapshot des images DOCX positionnées (direct children de pageDiv)
      const autoImgs  = [...pageDiv.querySelectorAll('.crx-auto-img')];
      const watermark = pageDiv.querySelector('.crx-watermark');
      const userWm    = pageDiv.querySelector('.crx-wm-user');

      const allNodes = [...content.childNodes];
      content.innerHTML = '';

      const makePageDiv = () => {
        const pg = document.createElement('div');
        pg.className = 'docx-a4-page';
        if (watermark) pg.appendChild(watermark.cloneNode(true));
        if (userWm)    pg.appendChild(userWm.cloneNode(true));
        const c = document.createElement('div');
        c.className = 'docx-content';
        c.contentEditable = 'true';
        c.spellcheck = false;
        c.addEventListener('focus', () => { activeEditor = c; updateToolbarState(); });
        c.addEventListener('keyup', updateToolbarState);
        c.addEventListener('mouseup', updateToolbarState);
        pg.appendChild(c);
        const num = document.createElement('div');
        num.className = 'docx-page-num';
        pg.appendChild(num);
        return { pg, c };
      };

      let cur = { pg: pageDiv, c: content };
      let pageCount = 1;
      const newPages = [cur];

      allNodes.forEach(node => {
        const clone = node.cloneNode ? node.cloneNode(true) : document.createTextNode(node.textContent);
        cur.c.appendChild(clone);
        if (cur.c.scrollHeight > PAGE_H - 30) {
          if (cur.c.childNodes.length > 1) {
            cur.c.removeChild(cur.c.lastChild);
            const np = makePageDiv();
            wrap.appendChild(np.pg);
            np.c.appendChild(clone);
            cur = np;
            pageCount++;
            newPages.push(cur);
          }
        }
      });

      // Redistribuer les .crx-auto-img selon leur top absolu
      // Chaque image a déjà position:absolute + left/top/width/height complets
      autoImgs.forEach(img => {
        const topPx = parseInt(img.style.top || '0');
        const pi    = Math.min(Math.floor(topPx / PAGE_H), pageCount - 1);
        const targetPg = newPages[pi];
        if (!targetPg) return;
        const ic = img.cloneNode(true);
        ic.style.top = (topPx - pi * PAGE_H) + 'px';
        targetPg.pg.appendChild(ic);
        img.remove();
      });

      // Mettre à jour les numéros de page
      const allPgs = wrap.querySelectorAll('.docx-a4-page');
      allPgs.forEach((p, i) => {
        const num = p.querySelector('.docx-page-num');
        if (num) num.textContent = 'Page ' + (i + 1) + ' / ' + allPgs.length;
      });

      // Mettre à jour le compteur sidebar
      const pc = document.getElementById('docxPageCount');
      if (pc) {
        const n = allPgs.length;
        pc.innerHTML = '<svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="#4ADE80" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg> '
                     + n + ' page' + (n > 1 ? 's' : '') + ' détectée' + (n > 1 ? 's' : '');
        pc.style.color = '#CBD5E1';
      }
    });
  });
}

loadDocxPages();

/* ── Interception clics langue / genre ───────────────────────── */
document.querySelectorAll('.pv-lang, .pv-gen').forEach(a => {
  a.addEventListener('click', e => {
    e.preventDefault();
    const url = new URL(a.href);
    const lc  = url.searchParams.get('locale');
    const g   = url.searchParams.get('gender');
    if (lc) currentLocale = lc;
    if (g)  currentGender = g;

    document.querySelectorAll('.pv-lang').forEach(el => el.classList.remove('pv-lang-active'));
    document.querySelectorAll('.pv-gen').forEach(el => el.classList.remove('pv-gen-active'));
    if (lc) document.querySelector('.pv-lang[data-lc="' + lc + '"]')?.classList.add('pv-lang-active');
    if (g)  document.querySelector('.pv-gen[data-g="' + g + '"]')?.classList.add('pv-gen-active');

    loadDocxPages();
  });
});

/* ── Barre d'outils : formatage ──────────────────────────────── */
function fmtCmd(cmd) {
  restoreFocus();
  document.execCommand(cmd, false, null);
  updateToolbarState();
}

function fmtColor(cmd, value) {
  if (!activeEditor || !savedRange) return;
  activeEditor.focus();
  const sel = window.getSelection();
  sel.removeAllRanges();
  sel.addRange(savedRange.cloneRange());

  if (cmd === 'foreColor') {
    // execCommand('foreColor') emits <font color="..."> which CSS can override.
    // Use a real <span style="color:..."> for reliable rendering.
    const range = sel.getRangeAt(0);
    if (!range.collapsed) {
      const span = document.createElement('span');
      span.style.color = value;
      try {
        range.surroundContents(span);
      } catch(_) {
        // surroundContents fails across block boundaries — fall back
        document.execCommand('foreColor', false, value);
      }
      sel.removeAllRanges();
      return;
    }
  }
  document.execCommand(cmd, false, value);
}

function fmtFont(value) {
  if (!value) return;
  restoreFocus();
  // execCommand fontName doesn't support multi-word fonts well — use span instead
  const sel = window.getSelection();
  if (sel && sel.rangeCount > 0 && !sel.isCollapsed) {
    document.execCommand('fontName', false, value.split(',')[0].trim().replace(/'/g, ''));
  }
}

function fmtFontSize(value) {
  if (!value) return;
  restoreFocus();
  // Convert pt values to execCommand size 1-7 approximation
  const ptMap = { '8pt':1, '9pt':1, '10pt':2, '11pt':2, '12pt':3, '14pt':3, '16pt':4, '18pt':4, '20pt':5, '24pt':5, '28pt':6, '36pt':6, '48pt':7 };
  const sz = ptMap[value] || 3;
  document.execCommand('fontSize', false, sz);
  // After execCommand wraps in <font size="N">, replace size attr with pt style
  if (activeEditor) {
    activeEditor.querySelectorAll('font[size]').forEach(f => {
      const ptVal = value;
      f.removeAttribute('size');
      f.style.fontSize = ptVal;
    });
  }
}

function fmtList(type) {
  restoreFocus();
  const cmd = (type === 'ul') ? 'insertUnorderedList' : 'insertOrderedList';
  document.execCommand(cmd, false, null);
  updateToolbarState();
}

// Prevent toolbar button clicks from stealing focus (except SELECT/INPUT which need it)
document.getElementById('docxToolbar')?.addEventListener('mousedown', e => {
  if (e.target.tagName !== 'SELECT' && e.target.tagName !== 'INPUT') {
    e.preventDefault(); // keeps editor focus + selection intact
  }
});

function restoreFocus() {
  if (!activeEditor) return;
  activeEditor.focus();
  if (savedRange) {
    const sel = window.getSelection();
    sel.removeAllRanges();
    sel.addRange(savedRange);
  }
}

/* ── Mettre à jour l'état des boutons selon la sélection ──────── */
function updateToolbarState() {
  const cmds = ['bold','italic','underline','strikeThrough',
                'justifyLeft','justifyCenter','justifyRight','justifyFull',
                'insertUnorderedList','insertOrderedList'];
  cmds.forEach(cmd => {
    const el = document.getElementById('tb-' + cmd)
            || document.getElementById('tb-' + cmd.toLowerCase())
            || document.getElementById('tb-ul') && cmd === 'insertUnorderedList' ? document.getElementById('tb-ul') : null
            || document.getElementById('tb-ol') && cmd === 'insertOrderedList'   ? document.getElementById('tb-ol') : null;
    if (!el) return;
    try {
      el.classList.toggle('active', document.queryCommandState(cmd));
    } catch(_) {}
  });
  // Special IDs
  const ulBtn = document.getElementById('tb-ul');
  const olBtn = document.getElementById('tb-ol');
  try { if (ulBtn) ulBtn.classList.toggle('active', document.queryCommandState('insertUnorderedList')); } catch(_) {}
  try { if (olBtn) olBtn.classList.toggle('active', document.queryCommandState('insertOrderedList')); }  catch(_) {}
}

/* ── Insérer une image au curseur ───────────────────────────── */
document.getElementById('docxImgInput')?.addEventListener('change', function (e) {
  const file = e.target.files[0];
  if (!file) return;
  if (file.size > 3 * 1024 * 1024) {
    alert('Image trop grande (max 3 Mo). Compressez-la avant insertion.');
    e.target.value = '';
    return;
  }
  const reader = new FileReader();
  reader.onload = function (ev) {
    const target = activeEditor || document.querySelector('.docx-content');
    if (!target) return;
    target.focus();
    if (savedRange && activeEditor && activeEditor.contains(savedRange.startContainer)) {
      const sel = window.getSelection();
      sel.removeAllRanges();
      sel.addRange(savedRange.cloneRange());
    }
    // Insert as resizable img — no data-docx-auto so it IS saved
    document.execCommand('insertHTML', false,
      `<img src="${ev.target.result}" alt="" style="max-width:100%;height:auto;display:block;margin:6px auto;cursor:pointer">`
    );
  };
  reader.readAsDataURL(file);
  e.target.value = '';
});

/* ── Filigrane utilisateur ──────────────────────────────────── */
document.getElementById('docxWmInput')?.addEventListener('change', function (e) {
  const file = e.target.files[0];
  if (!file) return;
  if (file.size > 5 * 1024 * 1024) {
    alert('Filigrane trop lourd (max 5 Mo).');
    e.target.value = '';
    return;
  }
  const reader = new FileReader();
  reader.onload = function (ev) {
    userWmSrc = ev.target.result;
    applyUserWatermark();
    document.getElementById('tbRemoveWm').style.display = '';
  };
  reader.readAsDataURL(file);
  e.target.value = '';
});

function applyUserWatermark() {
  document.querySelectorAll('.docx-a4-page').forEach(page => {
    let wm = page.querySelector('.crx-wm-user');
    if (!wm) {
      wm = document.createElement('div');
      wm.className = 'crx-wm-user';
      wm.style.cssText = [
        'position:absolute;inset:0;pointer-events:none;z-index:9',
        'background:center/contain no-repeat',
        'opacity:.12'
      ].join(';');
      page.insertBefore(wm, page.querySelector('.docx-content'));
    }
    wm.style.backgroundImage = `url("${userWmSrc}")`;
  });
}

function removeWatermark() {
  userWmSrc = null;
  document.querySelectorAll('.crx-wm-user').forEach(el => el.remove());
  document.getElementById('tbRemoveWm').style.display = 'none';
}

/* ── Ctrl+B / I / U in editor ────────────────────────────────── */
document.addEventListener('keydown', e => {
  if ((e.ctrlKey || e.metaKey) && e.key === 's') { e.preventDefault(); saveDocxEdit(); return; }
  if ((e.ctrlKey || e.metaKey) && activeEditor) {
    const map = { b:'bold', i:'italic', u:'underline' };
    if (map[e.key]) { e.preventDefault(); fmtCmd(map[e.key]); }
  }
});

/* ── Enregistrer les modifications ───────────────────────────── */
async function saveDocxEdit() {
  const btn    = document.getElementById('docxSaveBtn');
  const status = document.getElementById('docxSaveStatus');
  const pages  = [...document.querySelectorAll('.docx-content')];
  if (!pages.length) return;

  // Strip DOCX overlay rules (re-injected from DOCX on load) and data: URIs from CSS.
  // .crx-wm-user is NOT stripped — it carries the user-set watermark base64.
  const strippedCss = docxCurrentCSS
    .replace(/url\(["']?data:[^)]*["']?\)/g, 'url("")')
    .replace(/\.crx-(?:watermark|textboxes)\s*\{[^}]*\}/g, '')
    .replace(/body\s*>\s*\*\s*\{[^}]*\}/g, '');

  // Persist user watermark as a CSS rule with its full base64 (added AFTER stripping).
  const userWmCss = userWmSrc
    ? '\n.crx-wm-user{position:absolute;inset:0;pointer-events:none;z-index:9;'
      + 'background:url("' + userWmSrc + '") center/contain no-repeat;opacity:.12}'
    : '';

  // Strip only auto-injected images and overlay divs. Keep user images.
  // Also capture user images that were dragged outside .docx-content (position:absolute at page level).
  const bodyContent = pages.map(p => {
    const pageDiv = p.closest('.docx-a4-page');
    const clone = p.cloneNode(true);
    clone.querySelectorAll('img[data-docx-auto], .crx-auto-img').forEach(img => img.remove());
    clone.querySelectorAll('.crx-watermark, .crx-wm-user').forEach(el => el.remove());

    let html = clone.innerHTML;

    // Collect user images moved to page level during drag (not inside .docx-content)
    if (pageDiv) {
      const absImgs = [...pageDiv.querySelectorAll('img:not([data-docx-auto])')].filter(img =>
        img.style.position === 'absolute' && !p.contains(img)
      );
      if (absImgs.length) {
        const wrap = document.createElement('div');
        wrap.className = 'crx-user-abs';
        wrap.setAttribute('style', 'position:absolute;inset:0;pointer-events:none;z-index:20');
        absImgs.forEach(img => wrap.appendChild(img.cloneNode(true)));
        html += wrap.outerHTML;
      }
    }

    return html;
  }).join('\n<br style="page-break-before:always;clear:both">\n');

  const fullHtml = '<html><head><meta charset="UTF-8"><style>'
                 + strippedCss + userWmCss
                 + '</style></head><body>'
                 + bodyContent
                 + '</body></html>';

  btn.disabled = true;
  status.textContent = 'Enregistrement…';
  status.style.color = 'rgba(255,255,255,.5)';

  try {
    const res = await fetch(SAVE_URL, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
      body: JSON.stringify({ content: fullHtml }),
    });
    if (!res.ok) throw new Error('HTTP ' + res.status);
    const data = await res.json();
    if (!data.ok) throw new Error('Réponse serveur invalide');

    status.textContent = '✓ Enregistré avec succès';
    status.style.color = '#4ADE80';
    const badge = document.getElementById('docxEditBadge');
    if (badge) badge.style.display = 'flex';
    setTimeout(() => { status.textContent = ''; status.style.color = ''; }, 3500);
  } catch (err) {
    status.textContent = '✗ ' + err.message;
    status.style.color = '#F87171';
  } finally {
    btn.disabled = false;
  }
}

/* ── Revenir au DOCX original ────────────────────────────────── */
async function resetDocxToOriginal() {
  if (!confirm('Supprimer les modifications et revenir au rendu DOCX original ?')) return;
  const status = document.getElementById('docxSaveStatus');
  status.textContent = 'Réinitialisation…';
  status.style.color = 'rgba(255,255,255,.5)';
  try {
    const res = await fetch(RESET_URL, {
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
    });
    if (!res.ok) throw new Error('HTTP ' + res.status);
    const data = await res.json();
    if (!data.ok) throw new Error('Erreur serveur');

    status.textContent = '✓ DOCX original restauré';
    status.style.color = '#4ADE80';
    const badge = document.getElementById('docxEditBadge');
    if (badge) badge.style.display = 'none';
    setTimeout(() => { status.textContent = ''; status.style.color = ''; loadDocxPages(); }, 1600);
  } catch (err) {
    status.textContent = '✗ ' + err.message;
    status.style.color = '#F87171';
  }
}

/* ── Contrôle image : clic → barre flottante ─────────────────── */
let selectedImg = null;

document.addEventListener('click', e => {
  const bar = document.getElementById('imgCtrl');
  if (!bar) return;

  const img = e.target.closest('img');
  // Show control bar for user images (inside .docx-content) AND DOCX overlay images (.crx-auto-img)
  if (img && img.closest('.docx-a4-page') && (img.closest('.docx-content') || img.classList.contains('crx-auto-img'))) {
    e.preventDefault();
    e.stopPropagation();
    document.querySelectorAll('.crx-selected').forEach(el => el.classList.remove('crx-selected'));
    img.classList.add('crx-selected');
    selectedImg = img;
    imgShowBar(img);
  } else if (!e.target.closest('#imgCtrl')) {
    imgHideBar();
  }
});

function imgShowBar(img) {
  const bar  = document.getElementById('imgCtrl');
  const rect = img.getBoundingClientRect();

  // For overlay images (crx-auto-img), width is in px; for content images, use % of parent
  let pct;
  if (img.classList.contains('crx-auto-img')) {
    const pageDiv = img.closest('.docx-a4-page');
    const pageW   = pageDiv ? pageDiv.getBoundingClientRect().width : 794;
    pct = Math.max(5, Math.min(100, Math.round(img.offsetWidth / pageW * 100)));
  } else {
    const parentW = img.parentElement?.getBoundingClientRect().width || 698;
    const curW    = img.style.width
      ? (img.style.width.endsWith('%') ? parseInt(img.style.width) : Math.round(img.offsetWidth / parentW * 100))
      : Math.round(img.offsetWidth / parentW * 100);
    pct = Math.max(10, Math.min(100, curW || 100));
  }

  document.getElementById('icWidthRange').value = pct;
  document.getElementById('icWidthVal').textContent = pct + '%';

  // Hide alignment buttons for overlay images (absolute position, no float)
  const isOverlay = img.classList.contains('crx-auto-img');
  bar.querySelectorAll('button[onclick^="imgAlign"]').forEach(btn => {
    btn.style.display = isOverlay ? 'none' : '';
  });
  bar.querySelectorAll('.ic-sep').forEach((sep, i) => {
    // Hide the separator before alignment buttons (2nd separator) for overlay images
    if (i === 1) sep.style.display = isOverlay ? 'none' : '';
  });

  // Position bar above the image, clamped to viewport
  const barW = isOverlay ? 220 : 300;
  let left = rect.left + (rect.width / 2) - (barW / 2);
  left = Math.max(8, Math.min(window.innerWidth - barW - 8, left));
  let top = rect.top - 44;
  if (top < 8) top = rect.bottom + 8;

  bar.style.left    = left + 'px';
  bar.style.top     = top  + 'px';
  bar.style.display = 'flex';
}

function imgHideBar() {
  const bar = document.getElementById('imgCtrl');
  if (bar) bar.style.display = 'none';
  document.querySelectorAll('.crx-selected').forEach(el => el.classList.remove('crx-selected'));
  selectedImg = null;
}

function imgSetWidth(pct) {
  document.getElementById('icWidthVal').textContent = pct + '%';
  if (!selectedImg) return;
  if (selectedImg.classList.contains('crx-auto-img')) {
    // Overlay image: resize in px relative to page width
    const pageDiv = selectedImg.closest('.docx-a4-page');
    const pageW   = pageDiv ? pageDiv.getBoundingClientRect().width : 794;
    const newW    = Math.round(pageW * pct / 100);
    const ratio   = selectedImg.naturalHeight / (selectedImg.naturalWidth || 1);
    selectedImg.style.width  = newW + 'px';
    selectedImg.style.height = Math.round(newW * ratio) + 'px';
  } else {
    selectedImg.style.width  = pct + '%';
    selectedImg.style.height = 'auto';
  }
  requestAnimationFrame(() => { if (selectedImg) imgShowBar(selectedImg); });
}

function imgAlign(pos) {
  if (!selectedImg) return;
  selectedImg.style.display     = 'block';
  selectedImg.style.float       = 'none';
  selectedImg.style.marginLeft  = 'auto';
  selectedImg.style.marginRight = 'auto';
  if (pos === 'left') {
    selectedImg.style.float       = 'left';
    selectedImg.style.marginLeft  = '0';
    selectedImg.style.marginRight = '12px';
  } else if (pos === 'right') {
    selectedImg.style.float       = 'right';
    selectedImg.style.marginLeft  = '12px';
    selectedImg.style.marginRight = '0';
  }
  requestAnimationFrame(() => { if (selectedImg) imgShowBar(selectedImg); });
}

function imgDelete() {
  if (!selectedImg) return;
  const msg = selectedImg.classList.contains('crx-auto-img')
    ? 'Masquer cette image du document ?\n(Elle sera restaurée si vous rechargez le rendu DOCX original.)'
    : 'Supprimer cette image ?';
  if (confirm(msg)) {
    selectedImg.remove();
    imgHideBar();
  }
}

// Update bar position on scroll
document.querySelector('.pv-main-scroll')?.addEventListener('scroll', () => {
  if (selectedImg) imgShowBar(selectedImg);
}, { passive: true });

/* ── Déplacer une image par glisser-déposer ──────────────────── */
let dragImg   = null;
let dragPage  = null;
let dragOffX  = 0;
let dragOffY  = 0;
let dragCursorOrig = '';

// Attach drag handlers to all images in the editor (including dynamically inserted ones)
document.getElementById('docxPagesWrap').addEventListener('pointerdown', e => {
  const img = e.target.closest('img');
  if (!img || !img.closest('.docx-a4-page')) return;
  if (e.button !== 0) return;

  dragPage = img.closest('.docx-a4-page');
  if (!dragPage) return;

  e.preventDefault();
  e.stopPropagation();

  const pageRect = dragPage.getBoundingClientRect();
  const imgRect  = img.getBoundingClientRect();

  // If image is inline (inside .docx-content), lift it to page-level absolute positioning
  if (!img.style.position || img.style.position === 'static' || img.style.position === '') {
    img.style.position = 'absolute';
    img.style.left     = Math.round(imgRect.left - pageRect.left) + 'px';
    img.style.top      = Math.round(imgRect.top  - pageRect.top)  + 'px';
    img.style.zIndex   = '20';
    img.style.margin   = '0';
    img.style.float    = 'none';
    img.style.maxWidth = 'none';
    dragPage.appendChild(img); // move out of content flow
  }

  dragOffX = e.clientX - img.getBoundingClientRect().left;
  dragOffY = e.clientY - img.getBoundingClientRect().top;
  dragImg  = img;

  dragCursorOrig   = document.body.style.cursor;
  document.body.style.cursor = 'grabbing';
  img.style.cursor = 'grabbing';
  img.style.opacity = '.85';

  dragImg.setPointerCapture(e.pointerId);
}, { passive: false });

document.getElementById('docxPagesWrap').addEventListener('pointermove', e => {
  if (!dragImg || !dragPage) return;
  const pageRect = dragPage.getBoundingClientRect();
  const newLeft  = Math.round(e.clientX - pageRect.left - dragOffX);
  const newTop   = Math.round(e.clientY - pageRect.top  - dragOffY);
  dragImg.style.left = newLeft + 'px';
  dragImg.style.top  = newTop  + 'px';
  // Keep toolbar in sync while dragging
  if (selectedImg === dragImg) imgShowBar(dragImg);
});

document.getElementById('docxPagesWrap').addEventListener('pointerup', e => {
  if (!dragImg) return;
  dragImg.style.opacity = '';
  dragImg.style.cursor  = 'grab';
  document.body.style.cursor = dragCursorOrig;
  // Show control bar on release and mark as selected
  document.querySelectorAll('.crx-selected').forEach(el => el.classList.remove('crx-selected'));
  dragImg.classList.add('crx-selected');
  selectedImg = dragImg;
  imgShowBar(dragImg);
  dragImg  = null;
  dragPage = null;
});

@else
/* ═══════════════════════════════════════════════════════════════
   HTML : éditeur contenteditable avec chips + modes édition/aperçu
   ═══════════════════════════════════════════════════════════════ */
const SAVE_URL       = @json($saveUrl ?? '');
const CSRF_TOKEN     = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
const ORIGINAL_HTML  = @json($rawContent ?? '');
const SAMPLE_VARS    = @json($sampleVars ?? []);

let currentMode     = 'edit';
let savedEditState  = null;

function templateToEditable(html) {
  return html.replace(/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/g,
    '<span class="balise-chip" contenteditable="false">{$1}</span>');
}

function extractRawContent() {
  const editor = document.getElementById('contentEditor');
  const clone  = editor.cloneNode(true);
  clone.querySelectorAll('.balise-chip').forEach(chip => {
    chip.replaceWith(document.createTextNode(chip.textContent));
  });
  clone.removeAttribute('contenteditable');
  clone.querySelectorAll('[contenteditable]').forEach(el => el.removeAttribute('contenteditable'));
  return clone.innerHTML;
}

function insertBalise(balise) {
  const editor = document.getElementById('contentEditor');
  if (!editor) return;
  if (currentMode !== 'edit') setMode('edit');
  editor.focus();
  const chipHtml = '<span class="balise-chip" contenteditable="false">' + balise + '</span>';
  const sel      = window.getSelection();
  if (sel && sel.rangeCount > 0) {
    const range = sel.getRangeAt(0);
    if (editor.contains(range.commonAncestorContainer)) {
      range.deleteContents();
      const tmp  = document.createElement('div');
      tmp.innerHTML = chipHtml;
      const node = tmp.firstChild;
      range.insertNode(node);
      const newRange = document.createRange();
      newRange.setStartAfter(node);
      newRange.collapse(true);
      sel.removeAllRanges();
      sel.addRange(newRange);
      return;
    }
  }
  document.execCommand('insertHTML', false, chipHtml);
}

function setMode(mode) {
  const editor     = document.getElementById('contentEditor');
  const btnEdit    = document.getElementById('btnEdit');
  const btnPreview = document.getElementById('btnPreview');

  if (mode === 'preview') {
    savedEditState = extractRawContent();
    let preview = savedEditState;
    for (const [key, val] of Object.entries(SAMPLE_VARS)) {
      preview = preview.split(key).join(String(val ?? ''));
    }
    editor.innerHTML = preview;
    editor.contentEditable = 'false';
    btnEdit.classList.remove('active');
    btnPreview.classList.add('active');
    document.getElementById('saveStatus').textContent = 'Aperçu avec données exemples';
  } else {
    const content = savedEditState || ORIGINAL_HTML;
    editor.innerHTML = templateToEditable(content);
    editor.contentEditable = 'true';
    savedEditState = null;
    btnEdit.classList.add('active');
    btnPreview.classList.remove('active');
    document.getElementById('saveStatus').textContent = 'Cliquez sur le texte pour éditer';
  }
  currentMode = mode;
}

function filterBalises(query) {
  const q = query.toLowerCase().trim();
  document.querySelectorAll('#sidebarList .sb-item').forEach(item => {
    item.style.display = (!q || item.dataset.search?.includes(q)) ? '' : 'none';
  });
  document.querySelectorAll('#sidebarList .sb-cat-group').forEach(cat => {
    const hasVisible = [...cat.querySelectorAll('.sb-item')].some(i => i.style.display !== 'none');
    cat.style.display = hasVisible ? '' : 'none';
    cat.querySelector('.sb-cat-title').style.display = hasVisible ? '' : 'none';
  });
}

async function saveContent() {
  const btn    = document.getElementById('saveBtn');
  const status = document.getElementById('saveStatus');
  if (currentMode === 'preview') setMode('edit');
  const content = extractRawContent();
  btn.disabled = true;
  status.textContent = 'Enregistrement…';
  status.style.color = 'rgba(255,255,255,.5)';
  try {
    const res = await fetch(SAVE_URL, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
      body: JSON.stringify({ content }),
    });
    if (!res.ok) throw new Error('HTTP ' + res.status);
    const data = await res.json();
    if (data.ok) {
      status.textContent = '✓ Enregistré avec succès';
      status.style.color = '#4ADE80';
      setTimeout(() => { status.textContent = 'Cliquez sur le texte pour éditer'; status.style.color = ''; }, 3500);
    } else throw new Error('Réponse serveur invalide');
  } catch (err) {
    status.textContent = '✗ ' + err.message;
    status.style.color = '#F87171';
  } finally {
    btn.disabled = false;
  }
}

document.addEventListener('keydown', e => {
  if ((e.ctrlKey || e.metaKey) && e.key === 's') { e.preventDefault(); saveContent(); }
});
@endif
</script>

</body>
</html>
