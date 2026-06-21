@extends('layouts.client-app')
@section('title', 'Support — Credixa')
@section('page_title', 'Support')
@section('back_btn', true)
@section('back_url', route('client.app.home'))

@push('styles')
<style>
.chat-wrap{display:flex;flex-direction:column;height:calc(100dvh - 116px)}

/* Header */
.chat-hdr{display:flex;align-items:center;gap:.75rem;padding:.625rem 1.25rem;
  border-bottom:1px solid var(--ca-border-2);background:var(--ca-bg)}
.chat-hdr__ico{width:40px;height:40px;border-radius:50%;flex-shrink:0;
  background:linear-gradient(145deg,rgba(27,138,122,.35),rgba(27,138,122,.12));
  border:1.5px solid rgba(27,138,122,.3);
  display:flex;align-items:center;justify-content:center;font-size:1rem;color:var(--ca-teal-l)}
.chat-hdr__name{font-size:.875rem;font-weight:700;color:var(--ca-text)}
.chat-hdr__dot{width:7px;height:7px;border-radius:50%;background:#4ade80;
  display:inline-block;margin-right:.3rem;animation:blink 2s infinite}
@keyframes blink{0%,100%{opacity:1}50%{opacity:.4}}
.chat-hdr__status{font-size:.68rem;color:var(--ca-teal-l)}

/* Messages */
.chat-msgs{flex:1;overflow-y:auto;padding:.75rem 1.25rem;
  display:flex;flex-direction:column;gap:.5rem}
.chat-msgs::-webkit-scrollbar{width:2px}
.chat-msgs::-webkit-scrollbar-thumb{background:var(--ca-border);border-radius:99px}

.chat-date{text-align:center;font-size:.63rem;font-weight:700;text-transform:uppercase;
  letter-spacing:.08em;color:var(--ca-text-3);margin:.5rem 0 .25rem;display:flex;align-items:center;gap:.5rem}
.chat-date::before,.chat-date::after{content:'';flex:1;height:1px;background:var(--ca-border-2)}

.bw{display:flex;flex-direction:column}
.bw--me{align-items:flex-end}
.bw--them{align-items:flex-start}

.bubble{max-width:80%;padding:.625rem .875rem;border-radius:16px;
  font-size:.8125rem;line-height:1.55;word-break:break-word}
.bw--me   .bubble{background:var(--ca-teal-l);color:#051a13;border-bottom-right-radius:3px}
.bw--them .bubble{background:var(--ca-bg3);color:var(--ca-text);
  border:1px solid var(--ca-border);border-bottom-left-radius:3px}
/* IA bot bubble */
.bw--bot .bubble{background:linear-gradient(135deg,rgba(27,138,122,.12),rgba(27,138,122,.06));
  color:var(--ca-text);border:1.5px solid rgba(27,138,122,.25);border-bottom-left-radius:3px}
.bot-badge{display:inline-flex;align-items:center;gap:.3rem;font-size:.6rem;font-weight:700;
  text-transform:uppercase;letter-spacing:.06em;color:var(--ca-teal-l);margin-bottom:.2rem;opacity:.8}
.bot-badge i{font-size:.55rem}

.bubble img{max-width:220px;border-radius:10px;display:block;cursor:pointer;margin-top:.25rem}
.bubble audio{width:200px;margin-top:.25rem}
.bubble-meta{font-size:.6rem;color:var(--ca-text-3);margin-top:.15rem;padding:0 .2rem}

.chat-empty{flex:1;display:flex;flex-direction:column;align-items:center;
  justify-content:center;text-align:center;opacity:.4}
.chat-empty i{font-size:2.25rem;margin-bottom:.625rem;color:var(--ca-text-3)}
.chat-empty p{font-size:.8rem;color:var(--ca-text-3);line-height:1.5}

/* Preview zone */
.chat-preview{display:none;padding:.5rem 1.25rem;border-top:1px solid var(--ca-border-2);
  background:var(--ca-bg2)}
.chat-preview__inner{position:relative;display:inline-block}
.chat-preview img,.chat-preview audio{max-height:80px;border-radius:8px}
.chat-preview__rm{position:absolute;top:-6px;right:-6px;width:20px;height:20px;
  border-radius:50%;background:#f87171;border:none;cursor:pointer;
  display:flex;align-items:center;justify-content:center;font-size:.55rem;color:#fff}

/* Input bar */
.chat-bar{display:flex;align-items:flex-end;gap:.5rem;
  padding:.5rem .875rem .75rem;border-top:1px solid var(--ca-border-2);background:var(--ca-bg)}
.chat-input{flex:1;background:var(--ca-bg2);border:1.5px solid var(--ca-border);
  border-radius:20px;padding:.55rem .9rem;font-size:.8125rem;color:var(--ca-text);
  font-family:inherit;resize:none;outline:none;max-height:100px;overflow-y:auto;
  transition:.15s;line-height:1.5}
.chat-input:focus{border-color:var(--ca-teal-l)}
.chat-icon-btn{width:38px;height:38px;border-radius:50%;border:1.5px solid var(--ca-border);
  background:var(--ca-bg2);cursor:pointer;display:flex;align-items:center;
  justify-content:center;font-size:.875rem;color:var(--ca-text-3);flex-shrink:0;transition:.15s}
.chat-icon-btn:hover{border-color:var(--ca-teal-l);color:var(--ca-teal-l)}
.chat-send{width:38px;height:38px;border-radius:50%;border:none;
  background:var(--ca-teal-l);cursor:pointer;display:flex;align-items:center;
  justify-content:center;font-size:.875rem;color:#051a13;flex-shrink:0;
  opacity:.4;transition:.15s}
.chat-send.active{opacity:1}
.chat-send.active:active{transform:scale(.92)}

/* Image lightbox */
.lightbox{display:none;position:fixed;inset:0;background:rgba(0,0,0,.85);z-index:9999;
  align-items:center;justify-content:center}
.lightbox.open{display:flex}
.lightbox img{max-width:90vw;max-height:90vh;border-radius:12px}
.lightbox__close{position:absolute;top:16px;right:16px;width:36px;height:36px;
  border-radius:50%;background:rgba(255,255,255,.15);border:none;cursor:pointer;
  color:#fff;font-size:1rem;display:flex;align-items:center;justify-content:center}
</style>
@endpush

@section('content')

@php $csrfToken = csrf_token(); @endphp

<div class="chat-wrap">

  {{-- Header ── --}}
  <div class="chat-hdr">
    <div class="chat-hdr__ico"><i class="fas fa-headset"></i></div>
    <div>
      <div class="chat-hdr__name">Conseiller Credixa</div>
      <div class="chat-hdr__status">
        <span class="chat-hdr__dot"></span>Support en ligne
      </div>
    </div>
  </div>

  {{-- Messages ── --}}
  <div class="chat-msgs" id="chatMsgs">
    @if($chatData->isEmpty())
    <div class="chat-empty" id="chatEmpty">
      <i class="fas fa-comments"></i>
      <p>Aucun message pour l'instant.<br>Envoyez un message à votre conseiller.</p>
    </div>
    @else
    <div id="chatEmpty" style="display:none"></div>
    @endif
    <div id="bubblesContainer">{{-- JS renders here --}}</div>
  </div>

  {{-- File preview ── --}}
  <div class="chat-preview" id="chatPreview">
    <div class="chat-preview__inner" id="previewInner"></div>
  </div>

  {{-- Input bar ── --}}
  <div class="chat-bar">
    <input type="file" id="fileInput" accept="image/*" style="display:none">
    <button class="chat-icon-btn" id="imageBtn" title="Envoyer une image" onclick="document.getElementById('fileInput').click()">
      <i class="fas fa-image"></i>
    </button>
    <textarea class="chat-input" id="chatInput" rows="1"
      placeholder="Votre message…"
      oninput="this.style.height='auto';this.style.height=Math.min(this.scrollHeight,100)+'px';updateSend()"></textarea>
    <button class="chat-send" id="sendBtn" onclick="sendMessage()">
      <i class="fas fa-paper-plane"></i>
    </button>
  </div>

</div>

{{-- Lightbox ── --}}
<div class="lightbox" id="lightbox" onclick="closeLightbox()">
  <button class="lightbox__close" onclick="closeLightbox()"><i class="fas fa-times"></i></button>
  <img id="lightboxImg" src="" alt="">
</div>

<script>
const CSRF      = '{{ $csrfToken }}';
const POLL_URL  = '{{ route("client.app.support.poll") }}';
const SEND_URL  = '{{ route("client.app.support.store") }}';
let lastId       = {{ $lastId }};
let pendingFile  = null;
let pollTimer;
const rendered   = new Set();

/* ── Initial render ── */
const initData = @json($chatData);
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
  document.getElementById('chatEmpty').style.display = 'none';

  const wrap = document.getElementById('bubblesContainer');
  const isMe  = msg.sender_type === 'client';
  const isBot = msg.is_bot === true;

  if (msg.date_key !== lastDateKey) {
    lastDateKey = msg.date_key;
    const sep = document.createElement('div');
    sep.className = 'chat-date';
    sep.textContent = msg.date_label;
    wrap.appendChild(sep);
  }

  const bw = document.createElement('div');
  bw.className = `bw ${isMe ? 'bw--me' : (isBot ? 'bw--bot' : 'bw--them')}`;
  if (animate) bw.style.animation = 'fadeIn .25s ease';

  let inner = '';
  if (msg.body) inner += `<div>${escHtml(msg.body)}</div>`;
  if (msg.file_type === 'image' && msg.file_url)
    inner += `<img src="${msg.file_url}" loading="lazy" onclick="openLightbox('${msg.file_url}')">`;

  const badge = isBot
    ? `<div class="bot-badge"><i class="fas fa-robot"></i> Assistant IA</div>` : '';
  const meta  = isMe ? msg.time : (isBot ? `Assistant Credixa · ${msg.time}` : `Conseiller · ${msg.time}`);

  bw.innerHTML = `
    ${badge}
    <div class="bubble">${inner}</div>
    <div class="bubble-meta">${meta}</div>`;
  wrap.appendChild(bw);
}

function escHtml(s) {
  return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/\n/g,'<br>');
}

/* ── Send ── */
async function sendMessage() {
  const input = document.getElementById('chatInput');
  const body  = input.value.trim();
  if (!body && !pendingFile) return;

  const fd = new FormData();
  fd.append('_token', CSRF);
  if (body)        fd.append('body', body);
  if (pendingFile) fd.append('file', pendingFile);

  input.value = '';
  input.style.height = 'auto';
  clearPreview();
  updateSend();

  try {
    const r = await fetch(SEND_URL, { method:'POST', body: fd });
    const d = await r.json();
    if (d.message) { renderBubble(d.message, true); scrollBottom(); }
  } catch(e) {}
}

/* ── File input ── */
document.getElementById('fileInput').addEventListener('change', function() {
  const f = this.files[0];
  if (!f) return;
  setPendingFile(f);
  this.value = '';
});

function setPendingFile(file) {
  pendingFile = file;
  const inner = document.getElementById('previewInner');
  const url   = URL.createObjectURL(file);
  inner.innerHTML = `<img src="${url}" style="max-height:80px;border-radius:8px">`;
  inner.innerHTML += `<button class="chat-preview__rm" onclick="clearPreview()"><i class="fas fa-times"></i></button>`;
  document.getElementById('chatPreview').style.display = 'block';
  updateSend();
}

function clearPreview() {
  pendingFile = null;
  document.getElementById('previewInner').innerHTML = '';
  document.getElementById('chatPreview').style.display = 'none';
  updateSend();
}

/* ── Helpers ── */
function updateSend() {
  const has = document.getElementById('chatInput').value.trim() || pendingFile;
  document.getElementById('sendBtn').classList.toggle('active', !!has);
}

function scrollBottom() {
  const el = document.getElementById('chatMsgs');
  el.scrollTop = el.scrollHeight;
}

/* ── Enter = send ── */
document.getElementById('chatInput').addEventListener('keydown', e => {
  if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); sendMessage(); }
});

/* ── Lightbox ── */
function openLightbox(src) {
  document.getElementById('lightboxImg').src = src;
  document.getElementById('lightbox').classList.add('open');
}
function closeLightbox() {
  document.getElementById('lightbox').classList.remove('open');
}

/* ── Cleanup ── */
document.addEventListener('visibilitychange', () => {
  if (document.hidden) clearInterval(pollTimer);
  else startPolling();
});
</script>

@endsection
