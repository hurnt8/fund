@extends('layouts.dashboard')
@section('title', 'Support — ' . $client->name . ' — Credixa')
@section('page_title', 'Support · ' . $client->name)

@push('styles')
<style>
/* ── Layout ── */
.sp-shell {
  display:flex; flex-direction:column;
  height:calc(100vh - 140px); min-height:460px;
  border-radius:var(--radius); overflow:hidden;
  border:1.5px solid var(--c-border);
  box-shadow:var(--shadow-sm);
}

/* ── Header ── */
.sp-hdr {
  display:flex; align-items:center; gap:.875rem;
  padding:.875rem 1.25rem;
  background:var(--c-surface);
  border-bottom:1.5px solid var(--c-border);
  flex-shrink:0;
}
.sp-avatar {
  width:44px; height:44px; border-radius:50%; flex-shrink:0;
  background:linear-gradient(135deg,var(--c-navy),var(--c-navy-3));
  display:flex; align-items:center; justify-content:center;
  font-weight:800; font-size:.9375rem; color:var(--c-gold);
}
.sp-hdr-name  { font-size:.9rem; font-weight:700; color:var(--c-navy); }
.sp-hdr-email { font-size:.7rem; color:var(--c-muted); }
.sp-hdr-actions { margin-left:auto; display:flex; gap:.5rem; align-items:center; }

/* ── Messages area ── */
.sp-msgs {
  flex:1; overflow-y:auto;
  padding:1.125rem 1.25rem;
  background:var(--c-bg);
  display:flex; flex-direction:column; gap:.375rem;
}
.sp-msgs::-webkit-scrollbar { width:4px; }
.sp-msgs::-webkit-scrollbar-thumb { background:var(--c-border); border-radius:99px; }

/* Date separator */
.chat-date {
  text-align:center; font-size:.6rem; font-weight:700;
  text-transform:uppercase; letter-spacing:.09em; color:var(--c-muted);
  margin:.625rem 0 .375rem;
  display:flex; align-items:center; gap:.5rem;
}
.chat-date::before,.chat-date::after { content:''; flex:1; height:1px; background:var(--c-border); }

/* Bubble wrapper */
.bw { display:flex; flex-direction:column; }
.bw--me   { align-items:flex-end; }
.bw--them { align-items:flex-start; }

/* Bubbles */
.bubble {
  max-width:68%; padding:.625rem .9375rem;
  border-radius:14px; font-size:.8375rem; line-height:1.6;
  word-break:break-word;
}
.bw--me   .bubble {
  background:var(--c-navy); color:#fff;
  border-bottom-right-radius:3px;
}
.bw--them .bubble {
  background:var(--c-surface); color:var(--c-text);
  border:1.5px solid var(--c-border);
  border-bottom-left-radius:3px;
}
/* IA bot bubble — shown to admin with violet tint so they know it's auto-generated */
.bw--bot .bubble {
  background:linear-gradient(135deg,rgba(109,40,217,.07),rgba(109,40,217,.03));
  color:var(--c-text);
  border:1.5px solid rgba(109,40,217,.2);
  border-bottom-left-radius:3px;
}
.bot-badge {
  display:inline-flex; align-items:center; gap:.3rem;
  font-size:.6rem; font-weight:700; text-transform:uppercase;
  letter-spacing:.06em; color:#7c3aed; margin-bottom:.2rem;
}
.bot-badge i { font-size:.55rem; }
.bubble img {
  max-width:230px; border-radius:10px;
  display:block; cursor:zoom-in; margin-top:.375rem;
  transition:.15s; border:1.5px solid rgba(0,0,0,.07);
}
.bubble img:hover { opacity:.9; }
.bubble-meta {
  font-size:.6rem; color:var(--c-muted);
  margin-top:.125rem; padding:0 .25rem;
}
.bw--me .bubble-meta { text-align:right; }

/* Empty state */
.sp-empty {
  flex:1; display:flex; flex-direction:column;
  align-items:center; justify-content:center; text-align:center; opacity:.35;
}
.sp-empty i { font-size:2.5rem; color:var(--c-muted); margin-bottom:.75rem; }
.sp-empty p { font-size:.8rem; color:var(--c-muted); line-height:1.5; }

/* ── Image preview strip ── */
.sp-preview {
  display:none;
  padding:.5rem 1.25rem;
  background:var(--c-surface);
  border-top:1.5px solid var(--c-border);
  flex-shrink:0;
}
.sp-preview__inner { position:relative; display:inline-block; }
.sp-preview img { max-height:72px; border-radius:8px; border:1.5px solid var(--c-border); }
.sp-preview__rm {
  position:absolute; top:-6px; right:-6px;
  width:20px; height:20px; border-radius:50%;
  background:#ef4444; border:2px solid var(--c-surface);
  cursor:pointer; display:flex; align-items:center;
  justify-content:center; font-size:.5rem; color:#fff;
}

/* ── Input bar ── */
.sp-bar {
  display:flex; align-items:flex-end; gap:.625rem;
  padding:.75rem 1.25rem;
  background:var(--c-surface);
  border-top:1.5px solid var(--c-border);
  flex-shrink:0;
}
.sp-input {
  flex:1; background:var(--c-bg);
  border:1.5px solid var(--c-border); border-radius:var(--radius-sm);
  padding:.55rem .9rem; font-size:.8375rem; color:var(--c-text);
  font-family:inherit; resize:none; outline:none;
  max-height:100px; overflow-y:auto; line-height:1.5; transition:.2s;
}
.sp-input::placeholder { color:var(--c-muted); }
.sp-input:focus { border-color:var(--c-gold); background:var(--c-surface); }

.sp-img-btn {
  width:38px; height:38px; border-radius:var(--radius-sm);
  border:1.5px solid var(--c-border); background:var(--c-bg);
  cursor:pointer; display:flex; align-items:center;
  justify-content:center; font-size:.875rem;
  color:var(--c-muted); flex-shrink:0; transition:.15s;
}
.sp-img-btn:hover { border-color:var(--c-gold); color:var(--c-gold); background:rgba(200,169,81,.06); }

.sp-send {
  display:inline-flex; align-items:center; gap:.5rem;
  padding:.55rem 1.125rem; border-radius:var(--radius-sm);
  border:none; background:var(--c-navy); color:var(--c-gold);
  font-size:.8125rem; font-weight:700; cursor:pointer;
  transition:.15s; opacity:.4; flex-shrink:0; white-space:nowrap;
}
.sp-send.active          { opacity:1; }
.sp-send.active:hover    { background:var(--c-navy-3); }
.sp-send.sending         { opacity:.6; cursor:wait; }

/* ── Lightbox ── */
.lightbox {
  display:none; position:fixed; inset:0;
  background:rgba(5,15,35,.9); z-index:9999;
  align-items:center; justify-content:center;
  backdrop-filter:blur(4px);
}
.lightbox.open { display:flex; }
.lightbox img  { max-width:90vw; max-height:90vh; border-radius:12px; box-shadow:0 8px 48px rgba(0,0,0,.5); }
.lightbox__close {
  position:absolute; top:18px; right:18px;
  width:38px; height:38px; border-radius:50%;
  background:rgba(255,255,255,.12); border:1.5px solid rgba(255,255,255,.2);
  cursor:pointer; color:#fff; font-size:.9375rem;
  display:flex; align-items:center; justify-content:center; transition:.15s;
}
.lightbox__close:hover { background:rgba(255,255,255,.22); }

@keyframes fadeUp { from { opacity:0; transform:translateY(8px); } to { opacity:1; transform:translateY(0); } }

@media(max-width:640px) {
  .sp-shell { height:calc(100dvh - 120px); min-height:300px; }
  .sp-hdr { padding:.625rem 1rem; }
  .sp-hdr-actions .btn-ghost { padding:.35rem .6rem; font-size:.75rem; }
  .sp-hdr-actions .btn-ghost span { display:none; }
  .sp-msgs { padding:.75rem 1rem; }
  .bubble { max-width:85%; font-size:.8rem; padding:.5rem .75rem; }
  .sp-bar { padding:.5rem .75rem; gap:.375rem; }
  .sp-send span { display:none; }
  .sp-input { font-size:.8125rem; }
}
@media(max-width:400px) {
  .sp-shell { height:calc(100dvh - 110px); }
  .sp-hdr-name { font-size:.8125rem; }
  .sp-hdr-email { font-size:.65rem; }
}
</style>
@endpush

@section('content')
@php
  $initials  = strtoupper(substr($client->name, 0, 2));
  $csrfToken = csrf_token();
@endphp

{{-- Back link ── --}}
<a href="{{ route('admin.support.index') }}"
   style="display:inline-flex;align-items:center;gap:.5rem;font-size:.8125rem;font-weight:600;
          color:var(--c-muted);text-decoration:none;margin-bottom:1.125rem;transition:.15s"
   onmouseover="this.style.color='var(--c-gold)'" onmouseout="this.style.color='var(--c-muted)'">
  <i class="fas fa-chevron-left" style="font-size:.7rem"></i> Toutes les conversations
</a>

<div class="sp-shell">

  {{-- Header ── --}}
  <div class="sp-hdr">
    <div class="sp-avatar">{{ $initials }}</div>
    <div>
      <div class="sp-hdr-name">{{ $client->name }}</div>
      <div class="sp-hdr-email">{{ $client->email }}@if($client->phone) · {{ $client->phone }}@endif</div>
    </div>
    <div class="sp-hdr-actions">
      <a href="{{ route('admin.accounts.show', $client) }}" class="btn-ghost btn-sm-pro">
        <i class="fas fa-wallet"></i> Compte
      </a>
      @can('view', \App\Models\Loan::class)
      <a href="{{ route('admin.loans.index', ['client' => $client->id]) }}" class="btn-ghost btn-sm-pro">
        <i class="fas fa-folder-open"></i> Dossiers
      </a>
      @endcan
    </div>
  </div>

  {{-- Messages ── --}}
  <div class="sp-msgs" id="spMsgs">
    @if($chatData->isEmpty())
    <div class="sp-empty" id="spEmpty">
      <i class="fas fa-comments"></i>
      <p>Aucun message dans cette conversation.<br>Démarrez l'échange ci-dessous.</p>
    </div>
    @else
    <div id="spEmpty" style="display:none"></div>
    @endif
    <div id="bubblesContainer"></div>
  </div>

  {{-- Image preview ── --}}
  <div class="sp-preview" id="spPreview">
    <div class="sp-preview__inner" id="previewInner"></div>
  </div>

  {{-- Input bar ── --}}
  <div class="sp-bar">
    <input type="file" id="fileInput" accept="image/*" style="display:none">
    <button class="sp-img-btn" title="Joindre une image"
      onclick="document.getElementById('fileInput').click()">
      <i class="fas fa-image"></i>
    </button>
    <textarea class="sp-input" id="spInput" rows="1"
      placeholder="Répondre à {{ $client->name }}…"
      oninput="this.style.height='auto';this.style.height=Math.min(this.scrollHeight,100)+'px';updateSend()">
    </textarea>
    <button class="sp-send" id="sendBtn" onclick="sendMessage()">
      <i class="fas fa-paper-plane"></i> Envoyer
    </button>
  </div>

</div>

{{-- Lightbox ── --}}
<div class="lightbox" id="lightbox" onclick="closeLightbox()">
  <button class="lightbox__close" onclick="closeLightbox()"><i class="fas fa-times"></i></button>
  <img id="lightboxImg" src="" alt="">
</div>

@push('scripts')
<script>
const CSRF      = '{{ $csrfToken }}';
const POLL_URL  = '{{ route("admin.support.poll", $client) }}';
const SEND_URL  = '{{ route("admin.support.store", $client) }}';
const CLIENT_NM = '{{ addslashes($client->name) }}';
let lastId      = {{ $lastId }};
let pendingFile = null;
let pollTimer;
const rendered  = new Set();

/* ── Initial render ── */
const initData  = @json($chatData);
let lastDateKey = null;
initData.forEach(m => renderBubble(m, false));
scrollBottom();
startPolling();

/* ── Polling ── */
function startPolling() {
  pollTimer = setInterval(async () => {
    try {
      const r = await fetch(`${POLL_URL}?after=${lastId}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      });
      const d = await r.json();
      if (d.messages && d.messages.length) {
        d.messages.forEach(m => renderBubble(m, true));
        scrollBottom();
      }
    } catch(e) {}
  }, 3000);
}

/* ── Render bubble ── */
function renderBubble(msg, animate) {
  if (rendered.has(msg.id)) return;
  rendered.add(msg.id);
  if (msg.id > lastId) lastId = msg.id;
  document.getElementById('spEmpty').style.display = 'none';

  const wrap  = document.getElementById('bubblesContainer');
  const isMe  = msg.sender_type === 'admin' && !msg.is_bot;
  const isBot = msg.is_bot === true;

  if (msg.date_key !== lastDateKey) {
    lastDateKey = msg.date_key;
    const sep = document.createElement('div');
    sep.className   = 'chat-date';
    sep.textContent = msg.date_label;
    wrap.appendChild(sep);
  }

  const bw = document.createElement('div');
  bw.className = `bw ${isMe ? 'bw--me' : (isBot ? 'bw--bot' : 'bw--them')}`;
  if (animate) bw.style.animation = 'fadeUp .2s ease';

  let inner = '';
  if (msg.body) inner += `<div>${escHtml(msg.body)}</div>`;
  if (msg.file_type === 'image' && msg.file_url)
    inner += `<img src="${msg.file_url}" loading="lazy" onclick="openLightbox('${escAttr(msg.file_url)}')">`;

  const badge = isBot
    ? `<div class="bot-badge"><i class="fas fa-robot"></i> Réponse IA automatique</div>` : '';
  const sender = isMe ? 'Vous' : (isBot ? 'Assistant IA' : CLIENT_NM);

  bw.innerHTML = `
    ${badge}
    <div class="bubble">${inner}</div>
    <div class="bubble-meta">${sender} · ${msg.time}</div>`;
  wrap.appendChild(bw);
}

function escHtml(s) {
  return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/\n/g,'<br>');
}
function escAttr(s) { return s.replace(/"/g,'&quot;'); }

/* ── Send ── */
async function sendMessage() {
  const input = document.getElementById('spInput');
  const body  = input.value.trim();
  if (!body && !pendingFile) return;

  const btn = document.getElementById('sendBtn');
  btn.classList.remove('active');
  btn.classList.add('sending');
  btn.disabled = true;

  const fd = new FormData();
  fd.append('_token', CSRF);
  if (body)        fd.append('body', body);
  if (pendingFile) fd.append('file', pendingFile);

  input.value = '';
  input.style.height = 'auto';
  clearPreview();

  try {
    const r = await fetch(SEND_URL, {
      method: 'POST',
      headers: { 'X-Requested-With': 'XMLHttpRequest' },
      body: fd
    });
    const d = await r.json();
    if (d.message) { renderBubble(d.message, true); scrollBottom(); }
  } catch(e) {}

  btn.classList.remove('sending');
  btn.disabled = false;
  updateSend();
}

/* ── File input ── */
document.getElementById('fileInput').addEventListener('change', function () {
  const f = this.files[0];
  if (!f) return;
  pendingFile = f;
  const inner = document.getElementById('previewInner');
  const url   = URL.createObjectURL(f);
  inner.innerHTML = `<img src="${url}">
    <button class="sp-preview__rm" onclick="clearPreview()" type="button">
      <i class="fas fa-times"></i>
    </button>`;
  document.getElementById('spPreview').style.display = 'block';
  updateSend();
  this.value = '';
});

function clearPreview() {
  pendingFile = null;
  document.getElementById('previewInner').innerHTML = '';
  document.getElementById('spPreview').style.display = 'none';
  updateSend();
}

/* ── Helpers ── */
function updateSend() {
  const has = document.getElementById('spInput').value.trim() || pendingFile;
  document.getElementById('sendBtn').classList.toggle('active', !!has);
}

function scrollBottom() {
  const el = document.getElementById('spMsgs');
  el.scrollTop = el.scrollHeight;
}

document.getElementById('spInput').addEventListener('keydown', e => {
  if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); sendMessage(); }
});

/* ── Lightbox ── */
function openLightbox(src) {
  document.getElementById('lightboxImg').src = src;
  document.getElementById('lightbox').classList.add('open');
}
function closeLightbox() {
  document.getElementById('lightbox').classList.remove('open');
  document.getElementById('lightboxImg').src = '';
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeLightbox(); });

/* ── Pause polling when tab hidden ── */
document.addEventListener('visibilitychange', () => {
  if (document.hidden) clearInterval(pollTimer);
  else startPolling();
});
</script>
@endpush

@endsection
