@extends('layouts.mahasiswa')
@section('title', 'Kerjakan Tugas — ' . ($anggota->topik ?? $anggota->kelompok->nama_kelompok))
@section('page-title', 'Kerjakan Tugas')

@push('styles')
<style>
/* ── Layout ── */
.submit-layout {
  display:grid;
  grid-template-columns:300px 1fr;
  gap:20px;
  align-items:start;
  min-width:0;
}
.submit-layout > * { min-width:0; }
@media(max-width:900px){
  .submit-layout { grid-template-columns:1fr; }
}

/* ── Card ── */
.page-card {
  background:var(--surface); border:1px solid var(--border); border-radius:18px; overflow:hidden;
}
.card-head {
  padding:14px 18px; border-bottom:1px solid var(--border);
  display:flex; align-items:center; justify-content:space-between; gap:8px; flex-wrap:wrap;
}
.card-body { padding:18px; }

/* ── Badge ── */
.badge {
  display:inline-flex; align-items:center; gap:4px;
  padding:3px 9px; border-radius:20px; font-size:10px; font-weight:700;
}
.b-submitted { background:rgba(16,185,129,.12); color:#34d399; }
.b-belum     { background:rgba(100,116,139,.12); color:#94a3b8; }
.b-aktif     { background:rgba(16,185,129,.12); color:#34d399; }
.b-draft     { background:rgba(100,116,139,.12); color:#94a3b8; }
.b-selesai   { background:rgba(59,130,246,.12); color:#60a5fa; }
.b-overdue   { background:rgba(244,63,94,.12); color:#fb7185; }
.b-soon      { background:rgba(245,158,11,.12); color:#fbbf24; }

/* ── Info rows ── */
.info-row { display:flex; flex-direction:column; gap:3px; }
.info-label { font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:var(--muted); }
.info-val   { font-size:13px; font-weight:600; color:var(--text); }

/* ── Topik box ── */
.topik-box {
  padding:12px 14px; border-radius:12px;
  background:rgba(99,102,241,.08); border:1px solid rgba(99,102,241,.18);
}

/* ── Toolbar ── */
.editor-toolbar {
  display:flex; flex-wrap:wrap; gap:4px;
  padding:10px 14px; border-bottom:1px solid var(--border);
  background:var(--surface2);
}
.tb-btn {
  width:30px; height:30px; border-radius:8px; border:none; cursor:pointer;
  background:transparent; color:var(--muted); font-size:13px;
  display:grid; place-items:center; transition:background .12s, color .12s;
}
.tb-btn:hover  { background:var(--border); color:var(--text); }
.tb-btn.active { background:rgba(var(--ac-rgb),.15); color:var(--ac); }
.tb-sep { width:1px; background:var(--border); margin:4px 2px; align-self:stretch; }

/* ── Editor ── */
#editor {
  min-height:380px; padding:18px 20px;
  outline:none; font-size:14px; line-height:1.7;
  color:var(--text); overflow-y:auto;
}
#editor:empty::before {
  content: attr(data-placeholder);
  color:var(--muted); pointer-events:none;
}
#editor h2 { font-size:1.25em; font-weight:700; margin:.6em 0 .3em; }
#editor h3 { font-size:1.05em; font-weight:700; margin:.5em 0 .25em; }
#editor ul, #editor ol { padding-left:1.4em; margin:.3em 0; }
#editor blockquote {
  border-left:3px solid var(--ac); padding-left:12px; margin-left:0;
  color:var(--muted); font-style:italic;
}
#editor code {
  background:var(--surface2); border:1px solid var(--border);
  padding:1px 6px; border-radius:5px; font-size:.88em; font-family:monospace;
}
#editor pre {
  background:var(--surface2); border:1px solid var(--border);
  padding:12px; border-radius:10px; overflow-x:auto;
}
#editor pre code { background:none; border:none; padding:0; }
#editor img {
  max-width:100%; height:auto; border-radius:10px;
  border:1px solid var(--border); margin:.4em 0; display:block;
}

/* ── Save status ── */
#save-status {
  font-size:11px; color:var(--muted);
  display:flex; align-items:center; gap:5px;
}

/* ── Buttons ── */
.btn-primary {
  padding:9px 20px; border-radius:11px; font-size:13px; font-weight:600;
  color:#fff; border:none; cursor:pointer; background:var(--ac); transition:opacity .15s;
}
.btn-primary:hover { opacity:.85; }
.btn-primary:disabled { opacity:.5; cursor:not-allowed; }
.btn-ghost {
  padding:9px 16px; border-radius:11px; font-size:13px; font-weight:600;
  background:var(--surface2); color:var(--muted); border:none; cursor:pointer; transition:opacity .15s;
}
.btn-ghost:hover { opacity:.75; }
.btn-danger {
  padding:9px 16px; border-radius:11px; font-size:13px; font-weight:600;
  background:rgba(239,68,68,.1); color:#f87171; border:none; cursor:pointer; transition:opacity .15s;
}
.btn-danger:hover { opacity:.75; }

/* ── Toast ── */
#toast {
  position:fixed; bottom:24px; left:50%; transform:translateX(-50%) translateY(80px);
  background:var(--surface); border:1px solid var(--border); border-radius:14px;
  padding:10px 20px; font-size:13px; font-weight:600; color:var(--text);
  box-shadow:0 8px 24px rgba(0,0,0,.25); transition:transform .3s, opacity .3s;
  opacity:0; z-index:200; white-space:nowrap;
}
#toast.show { transform:translateX(-50%) translateY(0); opacity:1; }
</style>
@endpush

@section('content')
@php
$kelompok = $anggota->kelompok;
$tugas    = $kelompok->tugas;
$kelas    = $tugas->kelas;
$mk       = $kelas->mataKuliah;
$periode  = $kelas->periodeAkademik;
$ketua    = $kelompok->ketua;
$isSubmitted = $anggota->status_submit === 'submitted';
$isClosed    = $tugas->status === 'selesai';
$isOverdue   = $tugas->deadline && $tugas->deadline->isPast() && !$isClosed;
$csrf        = csrf_token();
@endphp

<div class="space-y-5 animate-fadeUp" style="overflow-x:hidden">

  {{-- Breadcrumb --}}
  <div class="flex items-center gap-2 text-[12px]" style="color:var(--muted)">
    <a href="{{ route('mahasiswa.tugas.index') }}" class="a-text hover:underline">Tugas</a>
    <i class="fa-solid fa-chevron-right text-[10px]"></i>
    <span style="color:var(--text)">{{ $anggota->topik ?? $kelompok->nama_kelompok }}</span>
  </div>

  <div class="submit-layout">

    {{-- ── Sidebar info ────────────────────────────────────────── --}}
    <div class="space-y-4">

      {{-- Tugas info --}}
      <div class="page-card">
        <div class="h-1" style="background:linear-gradient(90deg,#10b981,#06b6d4)"></div>
        <div class="card-body space-y-4">
          <div class="flex flex-wrap gap-2">
            <span class="badge b-{{ $tugas->status }}">
              <i class="fa-solid fa-circle text-[7px]"></i>{{ ucfirst($tugas->status) }}
            </span>
            <span class="badge {{ $isSubmitted ? 'b-submitted' : 'b-belum' }}">
              <i class="fa-solid fa-{{ $isSubmitted ? 'check' : 'clock' }} text-[9px]"></i>
              {{ $isSubmitted ? 'Dikumpulkan' : 'Belum dikumpulkan' }}
            </span>
          </div>

          <div class="info-row">
            <span class="info-label">Tugas</span>
            <span class="info-val">{{ $tugas->judul }}</span>
          </div>

          <div class="info-row">
            <span class="info-label">Kelompok</span>
            <span class="info-val">{{ $kelompok->nama_kelompok }}</span>
          </div>

          @if($ketua)
          <div class="info-row">
            <span class="info-label">Ketua</span>
            <div class="flex items-center gap-1.5 mt-0.5">
              <div class="w-5 h-5 rounded-lg grid place-items-center text-[8px] font-bold flex-shrink-0"
                   style="background:rgba(245,158,11,.14);color:#fbbf24">
                {{ strtoupper(substr($ketua->nama ?? '?', 0, 1)) }}
              </div>
              <span class="text-[12px]" style="color:var(--text)">{{ $ketua->nama }}</span>
            </div>
          </div>
          @endif

          <div class="info-row">
            <span class="info-label">Mata Kuliah</span>
            <span class="info-val text-[12px]">{{ $mk?->nama ?? '—' }}</span>
          </div>

          @if($periode)
          <div class="info-row">
            <span class="info-label">Periode</span>
            <span class="info-val text-[12px]">{{ $periode->nama }}</span>
          </div>
          @endif

          @if($tugas->deadline)
          <div class="info-row">
            <span class="info-label">Deadline</span>
            <span class="badge {{ $isOverdue ? 'b-overdue' : ($tugas->deadline->diffInHours() <= 48 && !$isClosed ? 'b-soon' : 'b-belum') }}">
              <i class="fa-regular fa-clock"></i>
              {{ $tugas->deadline->format('d M Y, H:i') }}
              @if($isOverdue) · Lewat @endif
            </span>
          </div>
          @endif

          @if($tugas->deskripsi)
          <div class="info-row">
            <span class="info-label">Deskripsi Tugas</span>
            <p class="text-[12px] leading-relaxed mt-0.5" style="color:var(--sub)">{{ $tugas->deskripsi }}</p>
          </div>
          @endif
        </div>
      </div>

      {{-- Topik --}}
      @if($anggota->topik)
      <div class="page-card">
        <div class="card-body">
          <div class="info-label mb-2"><i class="fa-solid fa-tag mr-1"></i>Topikmu</div>
          <div class="topik-box text-[13px] font-semibold" style="color:#818cf8">
            {{ $anggota->topik }}
          </div>
        </div>
      </div>
      @endif

      {{-- Submitted at --}}
      @if($isSubmitted && $anggota->submitted_at)
      <div class="page-card">
        <div class="card-body">
          <div class="info-label mb-1"><i class="fa-solid fa-circle-check mr-1" style="color:#34d399"></i>Dikumpulkan pada</div>
          <div class="text-[13px] font-semibold" style="color:var(--text)">
            {{ $anggota->submitted_at->format('d M Y, H:i') }}
          </div>
        </div>
      </div>
      @endif

      {{-- Nilai & catatan (jika sudah dinilai) --}}
      @if($anggota->nilai !== null)
      <div class="page-card">
        <div class="card-body space-y-3">
          <div class="info-row">
            <span class="info-label"><i class="fa-solid fa-star mr-1" style="color:#fbbf24"></i>Nilai</span>
            <span class="font-display font-bold text-[28px]" style="color:var(--text)">{{ $anggota->nilai }}</span>
          </div>
          @if($anggota->catatan_instruktur)
          <div class="info-row">
            <span class="info-label">Catatan Instruktur</span>
            <p class="text-[12px] leading-relaxed mt-0.5" style="color:var(--sub)">{{ $anggota->catatan_instruktur }}</p>
          </div>
          @endif
        </div>
      </div>
      @endif

    </div>

    {{-- ── Editor ──────────────────────────────────────────────── --}}
    <div class="page-card" style="overflow:visible">
      <div class="card-head">
        <span class="font-display font-semibold text-[14px]" style="color:var(--text)">
          <i class="fa-solid fa-pen-to-square mr-1.5 a-text"></i>
          {{ $isSubmitted ? 'Konten Tugasmu' : 'Tulis Tugasmu' }}
        </span>
        <div id="save-status">
          <i class="fa-solid fa-cloud text-[10px]"></i>
          <span id="save-text">Belum disimpan</span>
        </div>
      </div>

      {{-- Toolbar --}}
      @if(!$isSubmitted && !$isClosed)
      <div class="editor-toolbar">
        <button class="tb-btn" onclick="execCmd('bold')" title="Bold"><i class="fa-solid fa-bold"></i></button>
        <button class="tb-btn" onclick="execCmd('italic')" title="Italic"><i class="fa-solid fa-italic"></i></button>
        <button class="tb-btn" onclick="execCmd('underline')" title="Underline"><i class="fa-solid fa-underline"></i></button>
        <div class="tb-sep"></div>
        <button class="tb-btn" onclick="execCmd('formatBlock','<h2>')" title="Heading 2"><i class="fa-solid fa-heading"></i></button>
        <button class="tb-btn" onclick="execCmd('formatBlock','<h3>')" title="Heading 3"><span style="font-size:11px;font-weight:700">H3</span></button>
        <button class="tb-btn" onclick="execCmd('formatBlock','<p>')" title="Paragraf"><i class="fa-solid fa-paragraph"></i></button>
        <div class="tb-sep"></div>
        <button class="tb-btn" onclick="execCmd('insertUnorderedList')" title="Bullet list"><i class="fa-solid fa-list-ul"></i></button>
        <button class="tb-btn" onclick="execCmd('insertOrderedList')" title="Numbered list"><i class="fa-solid fa-list-ol"></i></button>
        <div class="tb-sep"></div>
        <button class="tb-btn" onclick="execCmd('formatBlock','<blockquote>')" title="Kutipan"><i class="fa-solid fa-quote-right"></i></button>
        <div class="tb-sep"></div>
        <button class="tb-btn" onclick="execCmd('undo')" title="Undo"><i class="fa-solid fa-rotate-left"></i></button>
        <button class="tb-btn" onclick="execCmd('redo')" title="Redo"><i class="fa-solid fa-rotate-right"></i></button>
        <div class="tb-sep"></div>
        <button class="tb-btn" onclick="triggerImageUpload()" title="Sisipkan Gambar" id="img-btn">
          <i class="fa-solid fa-image"></i>
        </button>
        <input type="file" id="img-file-inp" accept="image/*" style="display:none" onchange="handleImageUpload(this)">
      </div>
      @endif

      {{-- Image upload progress --}}
      <div id="img-progress" style="display:none;padding:6px 18px;background:var(--surface2);font-size:11px;color:var(--muted)">
        <i class="fa-solid fa-spinner fa-spin mr-1.5"></i>Mengunggah gambar…
      </div>

      {{-- Content editable --}}
      <div id="editor"
           contenteditable="{{ $isSubmitted || $isClosed ? 'false' : 'true' }}"
           data-placeholder="Mulai menulis konten tugasmu di sini…"
           style="{{ $isSubmitted || $isClosed ? 'opacity:.85;cursor:default;' : '' }}"
      >{!! $anggota->konten ?? '' !!}</div>

      {{-- Footer actions --}}
      <div style="padding:14px 18px;border-top:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;gap:10px;flex-wrap:wrap">
        <div>
          @if($isSubmitted && !$isClosed)
            <button onclick="doUnsubmit()" class="btn-danger">
              <i class="fa-solid fa-rotate-left mr-1.5"></i>Tarik Kembali
            </button>
          @elseif(!$isSubmitted && !$isClosed)
            <button onclick="saveDraft()" class="btn-ghost">
              <i class="fa-solid fa-floppy-disk mr-1.5"></i>Simpan Draft
            </button>
          @endif
        </div>
        <div>
          @if(!$isSubmitted && !$isClosed)
            <button onclick="doSubmit()" class="btn-primary" id="submit-btn">
              <i class="fa-solid fa-paper-plane mr-1.5"></i>Kumpulkan Tugas
            </button>
          @elseif($isClosed)
            <span class="text-[12px]" style="color:var(--muted)">
              <i class="fa-solid fa-lock mr-1"></i>Tugas telah ditutup
            </span>
          @endif
        </div>
      </div>
    </div>

  </div>
</div>

<div id="toast"></div>
@endsection

@push('scripts')
<script>
const CSRF       = '{{ $csrf }}';
const IS_SUBMITTED = {{ $isSubmitted ? 'true' : 'false' }};
const IS_CLOSED    = {{ $isClosed   ? 'true' : 'false' }};
const ROUTE_KONTEN      = '{{ route('mahasiswa.tugas.anggota.konten', $anggota->id) }}';
const ROUTE_SUBMIT      = '{{ route('mahasiswa.tugas.anggota.submit', $anggota->id) }}';
const ROUTE_UNSUB       = '{{ route('mahasiswa.tugas.anggota.unsubmit', $anggota->id) }}';
const ROUTE_UPLOAD_IMG  = '{{ route('mahasiswa.tugas.upload-gambar') }}';

// ── Toast ────────────────────────────────────────────────────────
let _toastTimer;
function toast(msg, isErr = false) {
  const el = document.getElementById('toast');
  el.textContent = msg;
  el.style.borderColor = isErr ? 'rgba(239,68,68,.4)' : 'var(--border)';
  el.style.color = isErr ? '#fca5a5' : 'var(--text)';
  el.classList.add('show');
  clearTimeout(_toastTimer);
  _toastTimer = setTimeout(() => el.classList.remove('show'), 3000);
}

// ── Toolbar ───────────────────────────────────────────────────────
function execCmd(cmd, val = null) {
  document.execCommand(cmd, false, val);
  document.getElementById('editor').focus();
}

// ── Image upload ─────────────────────────────────────────────────
let _savedRange = null;

function triggerImageUpload() {
  // Simpan posisi kursor sebelum file picker membuka
  const sel = window.getSelection();
  if (sel && sel.rangeCount) {
    _savedRange = sel.getRangeAt(0).cloneRange();
  }
  document.getElementById('img-file-inp').click();
}

async function handleImageUpload(input) {
  const file = input.files[0];
  if (!file) return;
  input.value = ''; // reset agar bisa upload file sama lagi

  const progress = document.getElementById('img-progress');
  const btn = document.getElementById('img-btn');
  btn.disabled = true;
  progress.style.display = 'block';

  try {
    const fd = new FormData();
    fd.append('gambar', file);
    const r = await fetch(ROUTE_UPLOAD_IMG, {
      method: 'POST',
      headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
      body: fd,
    });
    const j = await r.json();
    if (!r.ok) { toast(j.message || 'Gagal mengunggah gambar.', true); return; }

    // Insert gambar di posisi kursor
    const img = document.createElement('img');
    img.src = j.url;
    img.alt = file.name;

    const editorEl = document.getElementById('editor');
    editorEl.focus();

    const sel = window.getSelection();
    if (_savedRange) {
      sel.removeAllRanges();
      sel.addRange(_savedRange);
    }

    if (sel && sel.rangeCount) {
      const range = sel.getRangeAt(0);
      range.deleteContents();
      range.insertNode(img);
      // Pindahkan kursor setelah gambar
      range.setStartAfter(img);
      range.collapse(true);
      sel.removeAllRanges();
      sel.addRange(range);
    } else {
      editorEl.appendChild(img);
    }

    _savedRange = null;
    // Trigger auto-save
    editorEl.dispatchEvent(new Event('input'));
    toast('Gambar berhasil disisipkan.');
  } catch {
    toast('Gagal mengunggah gambar.', true);
  } finally {
    btn.disabled = false;
    progress.style.display = 'none';
  }
}

// ── Auto-save (draft) ─────────────────────────────────────────────
let _saveTimer;
let _dirty = false;

const editor    = document.getElementById('editor');
const saveText  = document.getElementById('save-text');

if (!IS_SUBMITTED && !IS_CLOSED) {
  editor.addEventListener('input', () => {
    _dirty = true;
    saveText.textContent = 'Belum disimpan…';
    clearTimeout(_saveTimer);
    _saveTimer = setTimeout(autoSave, 2500);
  });
}

async function autoSave() {
  if (!_dirty || IS_SUBMITTED || IS_CLOSED) return;
  const konten = editor.innerHTML;
  try {
    const fd = new FormData();
    fd.append('konten', konten);
    fd.append('_method', 'PATCH');
    const r = await fetch(ROUTE_KONTEN, {
      method: 'POST',
      headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
      body: fd,
    });
    if (r.ok) {
      _dirty = false;
      saveText.textContent = 'Tersimpan ' + new Date().toLocaleTimeString('id-ID', {hour:'2-digit',minute:'2-digit'});
    }
  } catch {}
}

// ── Save draft (manual) ───────────────────────────────────────────
async function saveDraft() {
  clearTimeout(_saveTimer);
  await autoSave();
  if (!_dirty) toast('Draft disimpan.');
}

// ── Submit ────────────────────────────────────────────────────────
async function doSubmit() {
  if (!confirm('Kumpulkan tugas sekarang? Pastikan kontenmu sudah lengkap.')) return;

  const btn = document.getElementById('submit-btn');
  btn.disabled = true;

  const konten = editor.innerHTML;
  const fd = new FormData();
  fd.append('konten', konten);

  try {
    const r = await fetch(ROUTE_SUBMIT, {
      method: 'POST',
      headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
      body: fd,
    });
    const j = await r.json();
    if (r.ok) {
      toast('Tugas berhasil dikumpulkan!');
      setTimeout(() => location.reload(), 1200);
    } else {
      toast(j.message || 'Gagal mengumpulkan.', true);
      btn.disabled = false;
    }
  } catch {
    toast('Koneksi gagal. Coba lagi.', true);
    btn.disabled = false;
  }
}

// ── Unsubmit ──────────────────────────────────────────────────────
async function doUnsubmit() {
  if (!confirm('Tarik kembali pengumpulan? Kamu bisa mengedit lagi setelah ini.')) return;

  try {
    const r = await fetch(ROUTE_UNSUB, {
      method: 'DELETE',
      headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
    });
    const j = await r.json();
    if (r.ok) {
      toast('Pengumpulan dibatalkan.');
      setTimeout(() => location.reload(), 1200);
    } else {
      toast(j.message || 'Gagal menarik kembali.', true);
    }
  } catch {
    toast('Koneksi gagal. Coba lagi.', true);
  }
}
</script>
@endpush
