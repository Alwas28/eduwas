@extends('layouts.mahasiswa')
@section('title', 'Tugas Individu — ' . $tugas->judul)
@section('page-title', 'Kerjakan Tugas')

@push('styles')
<style>
/* ── Layout ── */
.ind-layout {
  display:grid;
  grid-template-columns:300px 1fr;
  gap:20px;
  align-items:start;
  min-width:0;
  overflow-x:hidden;
}
.ind-layout > * { min-width:0; }
@media(max-width:880px) {
  .ind-layout { grid-template-columns:1fr; }
}

/* ── Card ── */
.page-card { background:var(--surface); border:1px solid var(--border); border-radius:18px; overflow:hidden; }
.card-head { padding:14px 18px; border-bottom:1px solid var(--border); display:flex; align-items:center; justify-content:space-between; gap:8px; flex-wrap:wrap; }
.card-body { padding:18px; }

/* ── Badge ── */
.badge { display:inline-flex; align-items:center; gap:4px; padding:3px 9px; border-radius:20px; font-size:10px; font-weight:700; }
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

/* ── Soal ── */
.soal-body {
  padding:18px 20px; font-size:13.5px; line-height:1.8; color:var(--text);
}
.soal-body h2 { font-size:1.15em; font-weight:700; margin:.5em 0 .25em; }
.soal-body h3 { font-size:1em; font-weight:700; margin:.4em 0 .2em; }
.soal-body ul, .soal-body ol { padding-left:1.3em; margin:.25em 0; }
.soal-body blockquote { border-left:3px solid var(--ac); padding-left:10px; color:var(--muted); font-style:italic; }
.soal-body img { max-width:100%; height:auto; border-radius:10px; border:1px solid var(--border); margin:.4em 0; display:block; }

/* ── Upload zone ── */
.upload-zone {
  border:2px dashed var(--border); border-radius:16px;
  padding:40px 24px; text-align:center; cursor:pointer;
  transition:border-color .2s, background .2s;
}
.upload-zone:hover, .upload-zone.drag-over {
  border-color:var(--ac);
  background:rgba(var(--ac-rgb),.04);
}

/* ── File card ── */
.file-card {
  display:flex; align-items:center; gap:12px;
  padding:14px 16px; border-radius:14px;
  border:1px solid rgba(16,185,129,.2);
  background:rgba(16,185,129,.05);
}

/* ── Progress bar ── */
.progress-bar-wrap {
  height:6px; border-radius:3px; background:var(--border); overflow:hidden; margin-top:10px;
}
.progress-bar-fill {
  height:100%; border-radius:3px; background:var(--ac);
  transition:width .3s; width:0%;
}

/* ── Buttons ── */
.btn-primary { padding:9px 20px; border-radius:11px; font-size:13px; font-weight:600; color:#fff; border:none; cursor:pointer; background:var(--ac); transition:opacity .15s; }
.btn-primary:hover { opacity:.85; }
.btn-primary:disabled { opacity:.5; cursor:not-allowed; }
.btn-danger { padding:9px 16px; border-radius:11px; font-size:13px; font-weight:600; background:rgba(239,68,68,.1); color:#f87171; border:none; cursor:pointer; transition:opacity .15s; }
.btn-danger:hover { opacity:.75; }

/* ── Toast ── */
#toast {
  position:fixed; bottom:24px; left:50%; transform:translateX(-50%) translateY(80px);
  background:var(--surface); border:1px solid var(--border); border-radius:14px;
  padding:10px 20px; font-size:13px; font-weight:600; color:var(--text);
  box-shadow:0 8px 24px rgba(0,0,0,.25); transition:transform .3s,opacity .3s;
  opacity:0; z-index:200; white-space:nowrap;
}
#toast.show { transform:translateX(-50%) translateY(0); opacity:1; }
</style>
@endpush

@section('content')
@php
$kelas       = $tugas->kelas;
$mk          = $kelas->mataKuliah;
$periode     = $kelas->periodeAkademik;
$instruktur  = $tugas->instruktur;
$isSubmitted = $submission?->status_submit === 'submitted';
$isClosed    = $tugas->status === 'selesai';
$isOverdue   = $tugas->deadline && $tugas->deadline->isPast() && !$isClosed;
$isGraded    = $submission && $submission->nilai !== null;
$canSubmit   = !$isSubmitted && !$isClosed && !$isOverdue;
$canUnsubmit = $isSubmitted && !$isClosed && !$isGraded;
$csrf        = csrf_token();
$pdfUrl      = $submission?->pdf_path ? \Illuminate\Support\Facades\Storage::url($submission->pdf_path) : null;
@endphp

<div class="space-y-5 animate-fadeUp" style="overflow-x:hidden">

  {{-- Breadcrumb --}}
  <div class="flex items-center gap-2 text-[12px]" style="color:var(--muted)">
    <a href="{{ route('mahasiswa.tugas.index') }}" class="a-text hover:underline">Tugas</a>
    <i class="fa-solid fa-chevron-right text-[10px]"></i>
    <span style="color:var(--text)">{{ $tugas->judul }}</span>
  </div>

  <div class="ind-layout">

    {{-- ── Sidebar ── --}}
    <div class="space-y-4">

      {{-- Tugas info --}}
      <div class="page-card">
        <div class="h-1" style="background:linear-gradient(90deg,#6366f1,#8b5cf6)"></div>
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
            <span class="info-label">Deskripsi</span>
            <p class="text-[12px] leading-relaxed mt-0.5" style="color:var(--sub)">{{ $tugas->deskripsi }}</p>
          </div>
          @endif

        </div>
      </div>

      {{-- Submitted at --}}
      @if($isSubmitted && $submission->submitted_at)
      <div class="page-card">
        <div class="card-body">
          <div class="info-label mb-1"><i class="fa-solid fa-circle-check mr-1" style="color:#34d399"></i>Dikumpulkan pada</div>
          <div class="text-[13px] font-semibold" style="color:var(--text)">
            {{ $submission->submitted_at->format('d M Y, H:i') }}
          </div>
        </div>
      </div>
      @endif

      {{-- Nilai & catatan --}}
      @if($submission && $submission->nilai !== null)
      <div class="page-card">
        <div class="card-body space-y-3">
          <div class="info-row">
            <span class="info-label"><i class="fa-solid fa-star mr-1" style="color:#fbbf24"></i>Nilai</span>
            <span class="font-display font-bold text-[30px]" style="color:var(--text)">{{ $submission->nilai }}</span>
          </div>
          @if($submission->catatan_instruktur)
          <div class="info-row">
            <span class="info-label">Catatan Instruktur</span>
            <p class="text-[12px] leading-relaxed mt-0.5 p-3 rounded-xl" style="color:var(--sub);background:var(--surface2)">{{ $submission->catatan_instruktur }}</p>
          </div>
          @endif
        </div>
      </div>
      @endif

    </div>

    {{-- ── Main: Soal + Upload ── --}}
    <div class="space-y-4">

      {{-- Soal --}}
      @if($tugas->soal)
      <div class="page-card">
        <div class="card-head">
          <span class="font-display font-semibold text-[14px]" style="color:var(--text)">
            <i class="fa-solid fa-clipboard-question mr-1.5 a-text"></i>Soal
          </span>
        </div>
        <div class="soal-body">{!! $tugas->soal !!}</div>
      </div>
      @endif

      {{-- Upload PDF --}}
      <div class="page-card">
        <div class="card-head">
          <span class="font-display font-semibold text-[14px]" style="color:var(--text)">
            <i class="fa-solid fa-file-arrow-up mr-1.5 a-text"></i>
            {{ $isSubmitted ? 'Jawaban Dikumpulkan' : 'Upload Jawaban (PDF)' }}
          </span>
        </div>
        <div class="card-body">

          @if($isClosed && !$isSubmitted)
            <div class="text-center py-8" style="color:var(--muted)">
              <i class="fa-solid fa-lock text-[28px] opacity-20 block mb-3"></i>
              <div class="text-[13px] font-semibold" style="color:var(--text)">Tugas telah ditutup</div>
              <p class="text-[12px] mt-1">Pengumpulan sudah tidak bisa dilakukan.</p>
            </div>

          @elseif($isOverdue && !$isSubmitted)
            <div class="text-center py-8" style="color:var(--muted)">
              <i class="fa-solid fa-clock text-[28px] opacity-20 block mb-3" style="color:#fb7185"></i>
              <div class="text-[13px] font-semibold" style="color:#f87171">Deadline telah lewat</div>
              <p class="text-[12px] mt-1">Kamu tidak bisa mengumpulkan tugas setelah deadline.</p>
            </div>

          @elseif($isSubmitted)
            {{-- Submitted state --}}
            <div class="file-card">
              <div class="w-10 h-10 rounded-xl grid place-items-center flex-shrink-0" style="background:rgba(239,68,68,.1)">
                <i class="fa-solid fa-file-pdf text-[18px]" style="color:#f87171"></i>
              </div>
              <div class="flex-1 min-w-0">
                <div class="text-[13px] font-semibold truncate" style="color:var(--text)">Jawaban PDF</div>
                <div class="text-[11px]" style="color:var(--muted)">Sudah dikumpulkan</div>
              </div>
              @if($pdfUrl)
                <a href="{{ $pdfUrl }}" target="_blank"
                   class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[11px] font-semibold a-text a-bg-lt hover:opacity-80 transition-opacity flex-shrink-0">
                  <i class="fa-solid fa-eye text-[9px]"></i>Lihat
                </a>
              @endif
            </div>
            @if($canUnsubmit)
              <button onclick="doUnsubmit()" class="btn-danger mt-4">
                <i class="fa-solid fa-rotate-left mr-1.5"></i>Tarik Kembali
              </button>
            @elseif($isGraded)
              <p class="text-[11px] mt-3" style="color:var(--muted)">
                <i class="fa-solid fa-lock mr-1"></i>Tugas sudah dinilai, tidak bisa ditarik kembali.
              </p>
            @endif

          @else
            {{-- Upload zone --}}
            <div id="upload-zone" class="upload-zone"
                 onclick="document.getElementById('pdf-file-inp').click()"
                 ondragover="onDragOver(event)" ondragleave="onDragLeave(event)" ondrop="onDrop(event)">
              <i class="fa-solid fa-cloud-arrow-up text-[32px] a-text opacity-60 block mb-3"></i>
              <div class="text-[14px] font-semibold mb-1" style="color:var(--text)">Klik atau seret file PDF</div>
              <div class="text-[12px]" style="color:var(--muted)">Format PDF · Maks 20 MB</div>
            </div>
            <input type="file" id="pdf-file-inp" accept=".pdf" style="display:none" onchange="setFile(this.files[0])">

            {{-- Selected file preview --}}
            <div id="file-preview" style="display:none;margin-top:14px">
              <div class="file-card">
                <div class="w-10 h-10 rounded-xl grid place-items-center flex-shrink-0" style="background:rgba(239,68,68,.1)">
                  <i class="fa-solid fa-file-pdf text-[18px]" style="color:#f87171"></i>
                </div>
                <div class="flex-1 min-w-0">
                  <div id="file-name" class="text-[13px] font-semibold truncate" style="color:var(--text)"></div>
                  <div id="file-size" class="text-[11px]" style="color:var(--muted)"></div>
                </div>
                <button onclick="clearFile()" style="width:26px;height:26px;border-radius:7px;border:none;background:var(--surface2);color:var(--muted);cursor:pointer;flex-shrink:0">
                  <i class="fa-solid fa-xmark text-[10px]"></i>
                </button>
              </div>
              <div class="progress-bar-wrap" id="progress-wrap" style="display:none">
                <div class="progress-bar-fill" id="progress-fill"></div>
              </div>
            </div>

            <div style="margin-top:16px;display:flex;justify-content:flex-end">
              <button onclick="doSubmit()" class="btn-primary" id="submit-btn" disabled>
                <i class="fa-solid fa-paper-plane mr-1.5"></i>Kumpulkan Jawaban
              </button>
            </div>
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
const CSRF          = '{{ $csrf }}';
const IS_SUBMITTED  = {{ $isSubmitted ? 'true' : 'false' }};
const ROUTE_SUBMIT  = '{{ route('mahasiswa.tugas.individu.submit', $tugas->id) }}';
const ROUTE_UNSUB   = '{{ route('mahasiswa.tugas.individu.unsubmit', $tugas->id) }}';

let _selectedFile = null;

/* ── Toast ── */
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

/* ── Drag & drop ── */
function onDragOver(e) { e.preventDefault(); document.getElementById('upload-zone').classList.add('drag-over'); }
function onDragLeave()  { document.getElementById('upload-zone').classList.remove('drag-over'); }
function onDrop(e) {
  e.preventDefault();
  document.getElementById('upload-zone').classList.remove('drag-over');
  const file = e.dataTransfer.files[0];
  if (file) setFile(file);
}

/* ── File selection ── */
function setFile(file) {
  if (!file) return;
  if (file.type !== 'application/pdf') { toast('Hanya file PDF yang diterima.', true); return; }
  if (file.size > 20 * 1024 * 1024)   { toast('Ukuran file maksimal 20 MB.', true); return; }

  _selectedFile = file;
  document.getElementById('file-name').textContent = file.name;
  document.getElementById('file-size').textContent = formatBytes(file.size);
  document.getElementById('file-preview').style.display = '';
  document.getElementById('submit-btn').disabled = false;
}

function clearFile() {
  _selectedFile = null;
  document.getElementById('pdf-file-inp').value = '';
  document.getElementById('file-preview').style.display = 'none';
  document.getElementById('submit-btn').disabled = true;
}

function formatBytes(b) {
  if (b < 1024) return b + ' B';
  if (b < 1024 * 1024) return (b / 1024).toFixed(1) + ' KB';
  return (b / 1024 / 1024).toFixed(2) + ' MB';
}

/* ── Submit ── */
function doSubmit() {
  if (!_selectedFile) return;
  if (!confirm('Kumpulkan jawaban sekarang? Pastikan file sudah benar.')) return;

  const btn  = document.getElementById('submit-btn');
  const fill = document.getElementById('progress-fill');
  const wrap = document.getElementById('progress-wrap');
  btn.disabled = true;
  wrap.style.display = '';
  fill.style.width   = '0%';

  const fd = new FormData();
  fd.append('pdf', _selectedFile);

  const xhr = new XMLHttpRequest();
  xhr.open('POST', ROUTE_SUBMIT);
  xhr.setRequestHeader('X-CSRF-TOKEN', CSRF);
  xhr.setRequestHeader('Accept', 'application/json');

  xhr.upload.onprogress = e => {
    if (e.lengthComputable) fill.style.width = (e.loaded / e.total * 100) + '%';
  };

  xhr.onload = () => {
    try {
      const j = JSON.parse(xhr.responseText);
      if (xhr.status >= 200 && xhr.status < 300) {
        fill.style.width = '100%';
        toast('Jawaban berhasil dikumpulkan!');
        setTimeout(() => location.reload(), 1200);
      } else {
        toast(j.message || 'Gagal mengumpulkan.', true);
        btn.disabled = false;
        wrap.style.display = 'none';
      }
    } catch { toast('Respons server tidak valid.', true); btn.disabled = false; }
  };

  xhr.onerror = () => { toast('Koneksi gagal. Coba lagi.', true); btn.disabled = false; };
  xhr.send(fd);
}

/* ── Unsubmit ── */
async function doUnsubmit() {
  if (!confirm('Tarik kembali pengumpulan? Kamu harus upload ulang setelah ini.')) return;
  try {
    const r = await fetch(ROUTE_UNSUB, {
      method: 'DELETE',
      headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
    });
    const j = await r.json();
    if (r.ok) { toast('Pengumpulan dibatalkan.'); setTimeout(() => location.reload(), 1200); }
    else toast(j.message || 'Gagal menarik kembali.', true);
  } catch { toast('Koneksi gagal.', true); }
}
</script>
@endpush
