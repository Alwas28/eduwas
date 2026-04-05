@extends('layouts.mahasiswa')
@section('title', 'Kompilasi Final — ' . $kelompok->nama_kelompok)
@section('page-title', 'Kompilasi Final')

@push('styles')
<style>
/* ── Layout ── */
.final-layout {
  display:grid;
  grid-template-columns:300px 1fr;
  gap:20px;
  align-items:start;
  min-width:0;
}
@media(max-width:860px){
  .final-layout { grid-template-columns:1fr; }
}

/* ── Card ── */
.page-card {
  background:var(--surface); border:1px solid var(--border); border-radius:18px; overflow:hidden;
  min-width:0;
}
.card-head {
  padding:13px 18px; border-bottom:1px solid var(--border);
  display:flex; align-items:center; justify-content:space-between; gap:8px; flex-wrap:wrap;
}
.card-body { padding:16px 18px; }

/* ── Badge ── */
.badge {
  display:inline-flex; align-items:center; gap:4px;
  padding:3px 9px; border-radius:20px; font-size:10px; font-weight:700;
}
.b-submitted { background:rgba(16,185,129,.12); color:#34d399; }
.b-belum     { background:rgba(100,116,139,.12); color:#94a3b8; }
.b-aktif     { background:rgba(16,185,129,.12); color:#34d399; }
.b-draft     { background:rgba(100,116,139,.12); color:#94a3b8; }
.b-selesai   { background:rgba(59,130,246,.12);  color:#60a5fa; }

/* ── Anggota accordion cards ── */
.anggota-card {
  border:1px solid var(--border); border-radius:12px; overflow:hidden; margin-bottom:10px;
}
.anggota-card-head {
  padding:10px 14px; display:flex; align-items:center; gap:10px;
  cursor:pointer; user-select:none; transition:background .12s;
}
.anggota-card-head:hover { background:var(--surface2); }
.av { width:32px; height:32px; border-radius:10px; display:grid; place-items:center; font-size:11px; font-weight:700; flex-shrink:0; }
.anggota-konten-box {
  border-top:1px solid var(--border); padding:14px 16px;
  font-size:13px; line-height:1.7; color:var(--text);
  overflow-x:auto;
}
.anggota-konten-box img { max-width:100%; height:auto; border-radius:8px; border:1px solid var(--border); margin:.3em 0; display:block; }
.anggota-konten-box h2 { font-size:1.15em; font-weight:700; margin:.5em 0 .25em; }
.anggota-konten-box h3 { font-size:1em; font-weight:700; margin:.4em 0 .2em; }
.anggota-konten-box ul,
.anggota-konten-box ol { padding-left:1.3em; margin:.2em 0; }
.anggota-konten-box blockquote { border-left:3px solid var(--ac); padding-left:10px; color:var(--muted); font-style:italic; }
.empty-konten { color:var(--muted); font-style:italic; font-size:12px; }

/* ── PDF upload area ── */
.upload-zone {
  border:2px dashed var(--border); border-radius:14px;
  padding:36px 20px; text-align:center;
  cursor:pointer; transition:border-color .2s, background .2s;
}
.upload-zone:hover,
.upload-zone.drag-over {
  border-color:var(--ac);
  background:rgba(var(--ac-rgb),.04);
}
.upload-zone input[type=file] { display:none; }

/* ── PDF preview card ── */
.pdf-preview {
  display:flex; align-items:center; gap:14px;
  padding:14px 16px; border:1px solid var(--border); border-radius:14px;
  background:var(--surface2);
}
.pdf-icon {
  width:44px; height:44px; border-radius:12px; flex-shrink:0;
  display:grid; place-items:center; font-size:20px;
  background:rgba(239,68,68,.1); color:#f87171;
}
.pdf-info { flex:1; min-width:0; }
.pdf-name { font-weight:600; font-size:13px; color:var(--text); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.pdf-size { font-size:11px; color:var(--muted); margin-top:2px; }

/* ── Buttons ── */
.btn-primary { padding:9px 20px; border-radius:11px; font-size:13px; font-weight:600; color:#fff; border:none; cursor:pointer; background:var(--ac); transition:opacity .15s; }
.btn-primary:hover { opacity:.85; }
.btn-primary:disabled { opacity:.5; cursor:not-allowed; }
.btn-ghost   { padding:9px 16px; border-radius:11px; font-size:13px; font-weight:600; background:var(--surface2); color:var(--muted); border:none; cursor:pointer; transition:opacity .15s; display:inline-flex; align-items:center; gap:6px; }
.btn-ghost:hover { opacity:.75; }
.btn-danger  { padding:9px 16px; border-radius:11px; font-size:13px; font-weight:600; background:rgba(239,68,68,.1); color:#f87171; border:none; cursor:pointer; transition:opacity .15s; }
.btn-danger:hover { opacity:.75; }

/* ── Progress bar ── */
.upload-progress {
  height:4px; border-radius:2px; background:var(--border); overflow:hidden; margin-top:10px;
}
.upload-progress-bar {
  height:100%; border-radius:2px; background:var(--ac);
  width:0; transition:width .2s;
}

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
$tugas    = $kelompok->tugas;
$kelas    = $tugas->kelas;
$mk       = $kelas->mataKuliah;
$periode  = $kelas->periodeAkademik;
$isSubmitted = $kelompok->status_submit === 'submitted';
$isClosed    = $tugas->status === 'selesai';
$isOverdue   = $tugas->deadline && $tugas->deadline->isPast() && !$isClosed;
$csrf        = csrf_token();
@endphp

<div class="space-y-5 animate-fadeUp" style="min-width:0;overflow-x:hidden">

  {{-- Breadcrumb --}}
  <div class="flex items-center gap-2 text-[12px] flex-wrap" style="color:var(--muted)">
    <a href="{{ route('mahasiswa.tugas.index') }}" class="a-text hover:underline">Tugas</a>
    <i class="fa-solid fa-chevron-right text-[10px]"></i>
    <a href="{{ route('mahasiswa.tugas.kelompok.show', $kelompok->id) }}" class="a-text hover:underline truncate max-w-[140px]">{{ $kelompok->nama_kelompok }}</a>
    <i class="fa-solid fa-chevron-right text-[10px]"></i>
    <span style="color:var(--text)">Kompilasi Final</span>
  </div>

  {{-- Alert submitted --}}
  @if($isSubmitted)
  <div class="flex items-start gap-3 px-5 py-4 rounded-2xl"
       style="background:rgba(16,185,129,.08);border:1px solid rgba(16,185,129,.2)">
    <i class="fa-solid fa-circle-check text-[18px] flex-shrink-0 mt-0.5" style="color:#34d399"></i>
    <div class="min-w-0">
      <div class="font-semibold text-[13px]" style="color:#34d399">Tugas Telah Dikumpulkan</div>
      <div class="text-[12px]" style="color:var(--muted)">
        {{ $kelompok->submitted_at?->format('d M Y, H:i') }}
        @if($kelompok->pdf_path)
          &bull; <a href="{{ Storage::url($kelompok->pdf_path) }}" target="_blank" class="a-text underline">Unduh PDF</a>
        @endif
      </div>
    </div>
  </div>
  @endif

  <div class="final-layout">

    {{-- ── Sidebar: Info + Tugas Anggota ─────────────────────── --}}
    <div class="space-y-4 min-w-0">

      {{-- Info tugas --}}
      <div class="page-card">
        <div class="h-1 a-grad"></div>
        <div class="card-body space-y-3">
          <div class="flex flex-wrap gap-2">
            <span class="badge b-{{ $tugas->status }}">
              <i class="fa-solid fa-circle text-[7px]"></i>{{ ucfirst($tugas->status) }}
            </span>
            <span class="badge {{ $isSubmitted ? 'b-submitted' : 'b-belum' }}">
              <i class="fa-solid fa-{{ $isSubmitted ? 'check' : 'clock' }} text-[9px]"></i>
              {{ $isSubmitted ? 'Dikumpulkan' : 'Belum dikumpulkan' }}
            </span>
          </div>
          <div>
            <div class="text-[10px] font-bold uppercase tracking-wider mb-0.5" style="color:var(--muted)">Tugas</div>
            <div class="font-semibold text-[13px]" style="color:var(--text)">{{ $tugas->judul }}</div>
          </div>
          <div>
            <div class="text-[10px] font-bold uppercase tracking-wider mb-0.5" style="color:var(--muted)">Mata Kuliah</div>
            <div class="text-[12px]" style="color:var(--text)">{{ $mk?->nama ?? '—' }}</div>
          </div>
          @if($tugas->deadline)
          <div>
            <div class="text-[10px] font-bold uppercase tracking-wider mb-0.5" style="color:var(--muted)">Deadline</div>
            <span class="badge {{ $isOverdue ? '' : 'b-belum' }}"
                  style="{{ $isOverdue ? 'background:rgba(244,63,94,.12);color:#fb7185' : '' }}">
              <i class="fa-regular fa-clock"></i>{{ $tugas->deadline->format('d M Y, H:i') }}
              @if($isOverdue) · Lewat @endif
            </span>
          </div>
          @endif
          @if($kelompok->nilai_kelompok !== null)
          <div>
            <div class="text-[10px] font-bold uppercase tracking-wider mb-1" style="color:var(--muted)">Nilai Kelompok</div>
            <div class="font-display font-bold text-[26px] a-text">{{ $kelompok->nilai_kelompok }}</div>
            @if($kelompok->catatan_kelompok)
              <p class="text-[11px] mt-1" style="color:var(--sub)">{{ $kelompok->catatan_kelompok }}</p>
            @endif
          </div>
          @endif
        </div>
      </div>

      {{-- Tugas per anggota (collapsible) --}}
      <div class="page-card">
        <div class="card-head">
          <span class="font-display font-semibold text-[14px]" style="color:var(--text)">
            <i class="fa-solid fa-users mr-1.5 a-text"></i>Tugas Anggota
          </span>
          <span class="text-[11px] font-bold px-2 py-0.5 rounded-full a-bg-lt a-text">
            {{ $kelompok->anggota->count() }}
          </span>
        </div>
        <div class="p-3 space-y-2">
          @forelse($kelompok->anggota as $ang)
          <div class="anggota-card">
            <div class="anggota-card-head" onclick="toggleAnggota({{ $ang->id }})">
              <div class="av a-bg-lt a-text">{{ strtoupper(substr($ang->mahasiswa?->nama ?? '?', 0, 1)) }}</div>
              <div class="flex-1 min-w-0">
                <div class="font-semibold text-[12px] truncate" style="color:var(--text)">
                  {{ $ang->mahasiswa?->nama ?? '—' }}
                  @if($ang->mahasiswa_id === $kelompok->ketua_mahasiswa_id)
                    <span class="text-[9px] font-bold px-1.5 py-0.5 rounded ml-1"
                          style="background:rgba(245,158,11,.14);color:#fbbf24">Ketua</span>
                  @endif
                </div>
                @if($ang->topik)
                  <div class="text-[10px] truncate a-text">{{ $ang->topik }}</div>
                @endif
              </div>
              <div class="flex items-center gap-2 flex-shrink-0">
                <span class="badge {{ $ang->status_submit === 'submitted' ? 'b-submitted' : 'b-belum' }}">
                  {{ $ang->status_submit === 'submitted' ? '&#10003;' : '—' }}
                </span>
                <i id="chevron-{{ $ang->id }}" class="fa-solid fa-chevron-down text-[10px]"
                   style="color:var(--muted);transition:transform .2s"></i>
              </div>
            </div>
            <div id="konten-{{ $ang->id }}" style="display:none">
              <div class="anggota-konten-box">
                @if($ang->konten)
                  {!! $ang->konten !!}
                @else
                  <span class="empty-konten">Anggota belum mengirim konten.</span>
                @endif
              </div>
            </div>
          </div>
          @empty
          <div class="text-center py-5 text-[12px]" style="color:var(--muted)">Belum ada anggota.</div>
          @endforelse
        </div>
      </div>

    </div>

    {{-- ── Upload PDF Final ────────────────────────────────────── --}}
    <div class="space-y-4 min-w-0">
      <div class="page-card">
        <div class="card-head">
          <span class="font-display font-semibold text-[14px]" style="color:var(--text)">
            <i class="fa-solid fa-file-pdf mr-1.5" style="color:#f87171"></i>Dokumen Final (PDF)
          </span>
        </div>
        <div class="card-body space-y-4">

          @if($isSubmitted)
            {{-- Sudah dikumpulkan: tampilkan info PDF --}}
            <div class="pdf-preview">
              <div class="pdf-icon"><i class="fa-solid fa-file-pdf"></i></div>
              <div class="pdf-info">
                <div class="pdf-name">Dokumen Final — {{ $kelompok->nama_kelompok }}</div>
                <div class="pdf-size">Dikumpulkan {{ $kelompok->submitted_at?->format('d M Y, H:i') }}</div>
              </div>
              @if($kelompok->pdf_path)
                <a href="{{ Storage::url($kelompok->pdf_path) }}" target="_blank" class="btn-ghost flex-shrink-0">
                  <i class="fa-solid fa-download text-[11px]"></i>Unduh
                </a>
              @endif
            </div>
            @if(!$isClosed)
              <div class="flex justify-end">
                <button onclick="doUnsubmit()" class="btn-danger">
                  <i class="fa-solid fa-rotate-left mr-1.5"></i>Tarik Kembali
                </button>
              </div>
            @endif

          @elseif($isClosed)
            <div class="py-6 text-center text-[13px]" style="color:var(--muted)">
              <i class="fa-solid fa-lock text-[22px] opacity-20 block mb-2"></i>
              Tugas telah ditutup.
            </div>

          @else
            {{-- Belum dikumpulkan: upload zone --}}
            <div id="upload-zone" class="upload-zone" onclick="document.getElementById('pdf-inp').click()"
                 ondragover="onDragOver(event)" ondragleave="onDragLeave(event)" ondrop="onDrop(event)">
              <input type="file" id="pdf-inp" accept="application/pdf" onchange="onFileSelect(this)">
              <i class="fa-solid fa-cloud-arrow-up text-[30px] mb-3 block" style="color:var(--ac);opacity:.7"></i>
              <div class="font-semibold text-[14px] mb-1" style="color:var(--text)">
                Klik atau seret file PDF ke sini
              </div>
              <div class="text-[12px]" style="color:var(--muted)">Maks. 20 MB · Format PDF</div>
            </div>

            {{-- Preview file terpilih --}}
            <div id="pdf-selected" class="pdf-preview" style="display:none">
              <div class="pdf-icon"><i class="fa-solid fa-file-pdf"></i></div>
              <div class="pdf-info">
                <div class="pdf-name" id="pdf-filename">—</div>
                <div class="pdf-size" id="pdf-filesize">—</div>
              </div>
              <button onclick="clearFile()" class="flex-shrink-0 w-7 h-7 rounded-lg grid place-items-center text-[11px] bg-rose-500/10 text-rose-400 hover:opacity-75">
                <i class="fa-solid fa-xmark"></i>
              </button>
            </div>

            {{-- Upload progress --}}
            <div id="upload-progress-wrap" style="display:none">
              <div class="flex items-center justify-between text-[11px] mb-1" style="color:var(--muted)">
                <span><i class="fa-solid fa-spinner fa-spin mr-1"></i>Mengunggah…</span>
                <span id="upload-pct">0%</span>
              </div>
              <div class="upload-progress">
                <div class="upload-progress-bar" id="upload-bar"></div>
              </div>
            </div>

            <div class="flex justify-end pt-1">
              <button onclick="doSubmit()" class="btn-primary" id="submit-btn" disabled>
                <i class="fa-solid fa-paper-plane mr-1.5"></i>Kumpulkan ke Instruktur
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
const CSRF         = '{{ $csrf }}';
const ROUTE_SUBMIT = '{{ route('mahasiswa.tugas.kelompok.final.submit', $kelompok->id) }}';
const ROUTE_UNSUB  = '{{ route('mahasiswa.tugas.kelompok.final.unsubmit', $kelompok->id) }}';

let _selectedFile = null;

// ── Toast ────────────────────────────────────────────────────────
let _tt;
function toast(msg, isErr = false) {
  const el = document.getElementById('toast');
  el.textContent = msg;
  el.style.borderColor = isErr ? 'rgba(239,68,68,.4)' : 'var(--border)';
  el.style.color = isErr ? '#fca5a5' : 'var(--text)';
  el.classList.add('show');
  clearTimeout(_tt);
  _tt = setTimeout(() => el.classList.remove('show'), 3200);
}

// ── Accordion anggota ─────────────────────────────────────────────
function toggleAnggota(id) {
  const box     = document.getElementById('konten-' + id);
  const chevron = document.getElementById('chevron-' + id);
  const open    = box.style.display === 'none';
  box.style.display = open ? 'block' : 'none';
  chevron.style.transform = open ? 'rotate(180deg)' : '';
}

// ── Drag & drop ───────────────────────────────────────────────────
function onDragOver(e) {
  e.preventDefault();
  document.getElementById('upload-zone').classList.add('drag-over');
}
function onDragLeave(e) {
  document.getElementById('upload-zone').classList.remove('drag-over');
}
function onDrop(e) {
  e.preventDefault();
  document.getElementById('upload-zone').classList.remove('drag-over');
  const file = e.dataTransfer.files[0];
  if (file) setFile(file);
}

// ── File select ───────────────────────────────────────────────────
function onFileSelect(input) {
  const file = input.files[0];
  if (file) setFile(file);
  input.value = '';
}

function setFile(file) {
  if (file.type !== 'application/pdf') {
    toast('Hanya file PDF yang diizinkan.', true); return;
  }
  if (file.size > 20 * 1024 * 1024) {
    toast('Ukuran file maks. 20 MB.', true); return;
  }
  _selectedFile = file;

  document.getElementById('pdf-filename').textContent = file.name;
  document.getElementById('pdf-filesize').textContent = formatBytes(file.size);
  document.getElementById('pdf-selected').style.display = 'flex';
  document.getElementById('upload-zone').style.display   = 'none';
  document.getElementById('submit-btn').disabled = false;
}

function clearFile() {
  _selectedFile = null;
  document.getElementById('pdf-selected').style.display = 'none';
  document.getElementById('upload-zone').style.display   = '';
  document.getElementById('submit-btn').disabled = true;
}

function formatBytes(b) {
  if (b < 1024) return b + ' B';
  if (b < 1024 * 1024) return (b / 1024).toFixed(1) + ' KB';
  return (b / (1024 * 1024)).toFixed(2) + ' MB';
}

// ── Submit (upload PDF via XHR for progress) ─────────────────────
function doSubmit() {
  if (!_selectedFile) { toast('Pilih file PDF terlebih dahulu.', true); return; }
  if (!confirm('Kumpulkan dokumen final ke instruktur?')) return;

  const btn   = document.getElementById('submit-btn');
  const wrap  = document.getElementById('upload-progress-wrap');
  const bar   = document.getElementById('upload-bar');
  const pct   = document.getElementById('upload-pct');
  btn.disabled = true;
  wrap.style.display = '';

  const fd = new FormData();
  fd.append('pdf', _selectedFile);

  const xhr = new XMLHttpRequest();
  xhr.upload.addEventListener('progress', e => {
    if (e.lengthComputable) {
      const p = Math.round(e.loaded / e.total * 100);
      bar.style.width = p + '%';
      pct.textContent = p + '%';
    }
  });
  xhr.addEventListener('load', () => {
    wrap.style.display = 'none';
    try {
      const j = JSON.parse(xhr.responseText);
      if (xhr.status >= 200 && xhr.status < 300) {
        toast('Tugas berhasil dikumpulkan!');
        setTimeout(() => location.reload(), 1200);
      } else {
        toast(j.message || 'Gagal mengumpulkan.', true);
        btn.disabled = false;
      }
    } catch { toast('Respons tidak valid.', true); btn.disabled = false; }
  });
  xhr.addEventListener('error', () => {
    wrap.style.display = 'none';
    toast('Koneksi gagal. Coba lagi.', true);
    btn.disabled = false;
  });

  xhr.open('POST', ROUTE_SUBMIT);
  xhr.setRequestHeader('X-CSRF-TOKEN', CSRF);
  xhr.setRequestHeader('Accept', 'application/json');
  xhr.send(fd);
}

// ── Unsubmit ──────────────────────────────────────────────────────
async function doUnsubmit() {
  if (!confirm('Tarik kembali pengumpulan? File PDF akan dihapus.')) return;
  try {
    const r = await fetch(ROUTE_UNSUB, {
      method: 'DELETE',
      headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
    });
    const j = await r.json();
    if (r.ok) { toast('Pengumpulan dibatalkan.'); setTimeout(() => location.reload(), 1200); }
    else toast(j.message || 'Gagal.', true);
  } catch { toast('Koneksi gagal.', true); }
}
</script>
@endpush
