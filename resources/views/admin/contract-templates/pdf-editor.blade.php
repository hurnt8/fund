@extends('layouts.dashboard')
@section('title','PDF — '.$template->name)
@section('page_title','Template PDF')

@push('head')
<style>
#pdfEditorGrid{display:grid;grid-template-columns:1fr 320px;gap:1rem;height:calc(100vh - 148px);min-height:500px}
#pdfFrame{width:100%;height:100%;border:0;border-radius:var(--radius-sm);background:#3a3a3c}
#sidebar{overflow-y:auto;display:flex;flex-direction:column;gap:.875rem}
.scard{background:var(--c-card);border:1px solid var(--c-border);border-radius:var(--radius-sm);padding:.875rem}
.scard h6{font-size:.72rem;font-weight:700;color:var(--c-navy);text-transform:uppercase;letter-spacing:.04em;margin:0 0 .65rem}
#uploadZone{border:2px dashed var(--c-border);border-radius:var(--radius-sm);padding:1.75rem 1rem;text-align:center;cursor:pointer;transition:border-color .2s,background .2s}
#uploadZone:hover,.drag-over{border-color:var(--c-gold)!important;background:rgba(202,138,4,.05)!important}
.tag-pill{display:inline-flex;align-items:center;gap:.3rem;padding:.2rem .55rem;border-radius:5px;font-size:.7rem;font-family:monospace;font-weight:700;background:#F0FDF4;border:1px solid #86EFAC;color:#166534;margin:.2rem .1rem}
.tag-pill.unknown{background:#FFF7ED;border-color:#FED7AA;color:#92400E}
.step-num{width:22px;height:22px;border-radius:50%;background:var(--c-navy);color:#fff;font-size:.65rem;font-weight:700;display:inline-flex;align-items:center;justify-content:center;flex-shrink:0}
#toast{position:fixed;bottom:1.5rem;right:1.5rem;z-index:9999;display:none;padding:.6rem 1.1rem;border-radius:8px;font-size:.8rem;font-weight:600;box-shadow:0 4px 20px rgba(0,0,0,.25)}
</style>
@endpush

@section('content')

<div class="d-flex align-items-start justify-content-between flex-wrap gap-3 page-hdr" style="margin-bottom:.75rem">
  <div>
    <h4 style="margin:0">Template PDF — {{ $template->name }}</h4>
    <p style="margin:.2rem 0 0;font-size:.78rem;color:var(--c-muted)">
      Uploadez votre PDF contenant des <code>{balises}</code> — elles seront détectées et remplacées automatiquement.
    </p>
  </div>
  <div class="d-flex gap-2">
    @if($template->pdf_tpl_path)
    <a href="{{ route('admin.contract-templates.preview-pdf', $template) }}" target="_blank"
       class="btn-ghost btn-sm-pro"><i class="fas fa-eye"></i> Aperçu avec données demo</a>
    @endif
    <a href="{{ route('admin.contract-templates.edit', $template) }}" class="btn-ghost btn-sm-pro">
      <i class="fas fa-arrow-left"></i> Retour
    </a>
  </div>
</div>

<div id="pdfEditorGrid">

  {{-- ── Gauche : PDF viewer ──────────────────────────────────────────── --}}
  <div style="display:flex;flex-direction:column;gap:.5rem">
    @if($template->pdf_tpl_path)
    <iframe id="pdfFrame"
            src="{{ route('admin.contract-templates.serve-pdf', $template) }}#toolbar=1&view=FitH"
            title="PDF Template">
    </iframe>
    @else
    <div id="uploadZone" style="flex:1;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:.75rem"
         onclick="document.getElementById('pdfFileInput').click()"
         ondragover="event.preventDefault();this.classList.add('drag-over')"
         ondragleave="this.classList.remove('drag-over')"
         ondrop="handleDrop(event)">
      <i class="fas fa-file-pdf" style="font-size:3rem;color:#dc2626;opacity:.6"></i>
      <div style="font-size:.95rem;font-weight:700;color:var(--c-navy)">Glissez votre PDF ici</div>
      <div style="font-size:.75rem;color:var(--c-muted)">ou cliquez pour sélectionner · Max 20 Mo</div>
    </div>
    @endif
    <input type="file" id="pdfFileInput" accept="application/pdf" style="display:none"
           onchange="uploadPdf(this.files[0])">
  </div>

  {{-- ── Droite : Sidebar ────────────────────────────────────────────── --}}
  <div id="sidebar">

    {{-- Comment ça marche --}}
    <div class="scard">
      <h6><i class="fas fa-magic me-1" style="color:var(--c-gold)"></i>Comment ça marche</h6>
      <ol style="padding-left:1.1rem;margin:0;font-size:.75rem;color:var(--c-muted);line-height:2">
        <li>Créez votre PDF avec des <code style="color:var(--c-gold-d)">{balises}</code> dans le texte</li>
        <li>Uploadez le PDF ci-contre</li>
        <li>Les balises sont détectées <strong>automatiquement</strong></li>
        <li>Au moment du contrat, les valeurs du client remplacent les balises</li>
      </ol>
    </div>

    {{-- Upload / Remplacer --}}
    <div class="scard">
      <h6><i class="fas fa-upload me-1"></i>
        {{ $template->pdf_tpl_path ? 'Remplacer le PDF' : 'Uploader le PDF template' }}
      </h6>
      @if($template->pdf_tpl_path)
      <div style="font-size:.72rem;color:var(--c-muted);margin-bottom:.65rem;word-break:break-all">
        <i class="fas fa-file-pdf me-1" style="color:#dc2626"></i>{{ basename($template->pdf_tpl_path) }}
      </div>
      @endif
      <div id="uploadZone2" onclick="document.getElementById('pdfFileInput').click()"
           ondragover="event.preventDefault();this.classList.add('drag-over')"
           ondragleave="this.classList.remove('drag-over')"
           ondrop="handleDrop(event)"
           style="border:1.5px dashed var(--c-border);border-radius:6px;padding:.75rem;text-align:center;cursor:pointer;font-size:.75rem;color:var(--c-muted);transition:border-color .2s,background .2s">
        <i class="fas fa-cloud-upload-alt me-1"></i>
        {{ $template->pdf_tpl_path ? 'Glissez un nouveau PDF pour remplacer' : 'Glissez votre PDF ici' }}
      </div>
      <div id="uploadProgress" style="display:none;margin-top:.5rem">
        <div style="height:4px;background:var(--c-bg);border-radius:2px;overflow:hidden">
          <div id="progressBar" style="height:100%;background:var(--c-gold);width:0%;transition:width .3s"></div>
        </div>
        <div id="uploadStatus" style="font-size:.7rem;color:var(--c-muted);margin-top:.3rem;text-align:center"></div>
      </div>
    </div>

    {{-- Balises détectées --}}
    <div class="scard" style="flex:1;overflow:hidden;display:flex;flex-direction:column">
      <h6>
        <i class="fas fa-tags me-1" style="color:var(--c-gold)"></i>
        Balises détectées
        @if(!empty($detectedWithDesc))
        <span style="font-weight:400;color:var(--c-muted);text-transform:none;font-size:.68rem;margin-left:.3rem">
          ({{ count($detectedWithDesc) }})
        </span>
        @endif
      </h6>

      <div id="tagsContainer" style="flex:1;overflow-y:auto">
        @if(empty($detectedWithDesc))
          @if($template->pdf_tpl_path)
          <div style="text-align:center;padding:1rem;font-size:.75rem;color:var(--c-muted)">
            <i class="fas fa-search" style="font-size:1.25rem;display:block;margin-bottom:.4rem"></i>
            Aucune balise <code>{variable}</code> détectée.<br>
            <span style="font-size:.7rem">Assurez-vous que votre PDF contient des balises du type <code>{nom_client}</code></span>
          </div>
          @else
          <div style="text-align:center;padding:1rem;font-size:.75rem;color:var(--c-muted)">
            <i class="fas fa-arrow-up" style="display:block;margin-bottom:.4rem"></i>
            Uploadez d'abord un PDF template
          </div>
          @endif
        @else
          <div style="margin-bottom:.5rem">
            @foreach($detectedWithDesc as $tag => $desc)
            <div style="display:flex;align-items:flex-start;gap:.5rem;padding:.4rem .5rem;border-radius:5px;border:1px solid var(--c-border);margin-bottom:.3rem;background:var(--c-bg)">
              <span class="tag-pill {{ $desc ? '' : 'unknown' }}" style="flex-shrink:0">{{ $tag }}</span>
              <span style="font-size:.68rem;color:var(--c-muted);line-height:1.4;padding-top:.1rem">
                @if($desc)
                  {{ $desc }}
                @else
                  <i class="fas fa-exclamation-triangle me-1" style="color:#f59e0b"></i>Balise personnalisée
                @endif
              </span>
            </div>
            @endforeach
          </div>
          <button onclick="rescanTags()" style="width:100%;padding:.3rem;font-size:.7rem;border:1px solid var(--c-border);border-radius:5px;background:var(--c-bg);color:var(--c-navy);cursor:pointer">
            <i class="fas fa-sync-alt me-1"></i>Rescanner le PDF
          </button>
        @endif
      </div>
    </div>

    {{-- Variables disponibles (référence) --}}
    <div class="scard">
      <h6><i class="fas fa-list me-1"></i>Balises standard disponibles</h6>
      <div style="max-height:180px;overflow-y:auto">
        @foreach($variables as $var => $desc)
        <div style="display:flex;align-items:center;gap:.4rem;padding:.3rem .4rem;border-radius:4px;cursor:pointer;transition:background .15s"
             onmouseover="this.style.background='var(--c-bg)'" onmouseout="this.style.background=''"
             onclick="copyTag('{{ $var }}')" title="Copier {{ $var }}">
          <code style="font-size:.65rem;color:var(--c-gold-d);font-weight:700;flex-shrink:0">{{ $var }}</code>
          <span style="font-size:.62rem;color:var(--c-muted);overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $desc }}</span>
        </div>
        @endforeach
      </div>
      <p style="font-size:.62rem;color:var(--c-muted);margin:.5rem 0 0">
        <i class="fas fa-hand-pointer me-1"></i>Cliquez pour copier la balise dans le presse-papier
      </p>
    </div>

  </div>
</div>

<div id="toast"></div>

@endsection

@push('scripts')
<script>
const UPLOAD_URL = '{{ route('admin.contract-templates.upload-pdf', $template) }}';
const RESCAN_URL = '{{ route('admin.contract-templates.rescan-pdf-tags', $template) }}';
const CSRF       = '{{ csrf_token() }}';

async function uploadPdf(file) {
  if (!file) return;
  if (file.type !== 'application/pdf') { toast('Format PDF requis.', 'err'); return; }
  if (file.size > 20 * 1024 * 1024)   { toast('Fichier trop grand (max 20 Mo).', 'err'); return; }

  showProgress(true, 'Upload en cours…');

  const fd = new FormData();
  fd.append('pdf_file', file);
  fd.append('_token', CSRF);

  try {
    const res  = await fetch(UPLOAD_URL, { method: 'POST', body: fd });
    const data = await res.json();

    if (data.ok) {
      toast(data.message, 'ok');
      showProgress(false);
      setTimeout(() => location.reload(), 900);
    } else {
      toast(data.error || 'Erreur lors de l\'upload.', 'err');
      showProgress(false);
    }
  } catch (e) {
    toast('Erreur réseau : ' + e.message, 'err');
    showProgress(false);
  }
}

async function rescanTags() {
  toast('Rescan en cours…', 'info');
  try {
    const res  = await fetch(RESCAN_URL, {
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
    });
    const data = await res.json();
    if (data.ok) {
      toast(data.count + ' balise(s) détectée(s). Rechargement…', 'ok');
      setTimeout(() => location.reload(), 800);
    } else {
      toast(data.error || 'Erreur rescan.', 'err');
    }
  } catch(e) { toast('Erreur: ' + e.message, 'err'); }
}

function handleDrop(e) {
  e.preventDefault();
  document.querySelectorAll('.drag-over').forEach(el => el.classList.remove('drag-over'));
  const file = e.dataTransfer.files[0];
  if (file) uploadPdf(file);
}

function copyTag(tag) {
  navigator.clipboard?.writeText(tag).then(() => toast(tag + ' copié !', 'ok'));
}

function showProgress(show, msg) {
  const wrap = document.getElementById('uploadProgress');
  const bar  = document.getElementById('progressBar');
  const lbl  = document.getElementById('uploadStatus');
  if (!wrap) return;
  wrap.style.display = show ? 'block' : 'none';
  if (msg) lbl.textContent = msg;
  if (show) {
    bar.style.width = '0%';
    let pct = 0;
    const iv = setInterval(() => {
      pct = Math.min(pct + 8, 85);
      bar.style.width = pct + '%';
      if (pct >= 85) clearInterval(iv);
    }, 200);
  } else {
    bar.style.width = '100%';
  }
}

function toast(msg, type) {
  const t = document.getElementById('toast');
  t.textContent = msg;
  t.style.background = type==='ok'?'#166534':type==='err'?'#991b1b':type==='warn'?'#92400e':'#1d4ed8';
  t.style.color = '#fff';
  t.style.display = 'block';
  clearTimeout(t._t);
  t._t = setTimeout(()=>t.style.display='none', 3000);
}
</script>
@endpush
