@extends('layouts.mahasiswa')
@section('title', 'Kelas Saya')
@section('page-title', 'Kelas Saya')

@section('content')
<div class="space-y-5">

  {{-- Stats row --}}
  @php
    $total   = $enrollments->count();
    $aktif   = $enrollments->where('status', 'Aktif')->count();
    $lulus   = $enrollments->where('status', 'Lulus')->count();
    $rataRata = $enrollments->whereNotNull('nilai_akhir')->avg('nilai_akhir');

    $statsCards = [
      ['fa-door-open',      'Total Kelas',    $total,   'a-bg-lt a-text',                                    ''],
      ['fa-circle-play',    'Sedang Aktif',   $aktif,   'bg-emerald-500/15 text-emerald-400',                ''],
      ['fa-graduation-cap', 'Selesai/Lulus',  $lulus,   'bg-blue-500/15 text-blue-400',                      ''],
      ['fa-star-half-stroke','Rata-rata Nilai',$rataRata !== null ? number_format($rataRata,1) : '—', 'bg-amber-500/15 text-amber-400',''],
    ];
  @endphp
  <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 animate-fadeUp">
    @foreach($statsCards as [$icon, $label, $val, $cls])
    <div class="rounded-2xl border p-4 flex items-center gap-3" style="background:var(--surface);border-color:var(--border)">
      <div class="w-10 h-10 rounded-xl grid place-items-center flex-shrink-0 {{ $cls }}">
        <i class="fa-solid {{ $icon }} text-[14px]"></i>
      </div>
      <div class="min-w-0">
        <div class="font-display text-[22px] font-bold leading-none" style="color:var(--text)">{{ $val }}</div>
        <div class="text-[11px] mt-0.5" style="color:var(--muted)">{{ $label }}</div>
      </div>
    </div>
    @endforeach
  </div>

  {{-- Scan QR / Join button --}}
  <div class="animate-fadeUp d1 flex justify-end">
    <button onclick="openQrModal()"
      class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-[13px] font-semibold text-white a-grad shadow transition-opacity hover:opacity-85">
      <i class="fa-solid fa-qrcode text-[13px]"></i>Scan QR untuk Bergabung
    </button>
  </div>

  {{-- Filter + Search bar --}}
  <div class="animate-fadeUp d1 flex flex-col sm:flex-row gap-3 items-start sm:items-center justify-between">

    {{-- Tab filter --}}
    <div class="flex items-center gap-1 p-1 rounded-xl" style="background:var(--surface);border:1px solid var(--border)">
      @php
        $tabs = [
          'semua'  => 'Semua',
          'aktif'  => 'Aktif',
          'lulus'  => 'Selesai',
          'dropout'=> 'Dropout',
        ];
      @endphp
      @foreach($tabs as $key => $label)
      <button type="button" data-tab="{{ $key }}"
        onclick="setTab('{{ $key }}')"
        class="tab-btn px-3 py-1.5 rounded-lg text-[12.5px] font-semibold transition-all {{ $key === 'semua' ? 'tab-active' : 'tab-inactive' }}">
        {{ $label }}
        <span class="tab-count-{{ $key }} ml-1 text-[11px]"></span>
      </button>
      @endforeach
    </div>

    {{-- Periode filter --}}
    <select id="filter-periode" onchange="filterKelas()"
      class="f-input text-[13px] py-2 pr-8 min-w-[180px]" style="width:auto">
      <option value="">Semua Periode</option>
      @foreach($periodeList as $p)
        <option value="{{ $p->id }}" {{ $periodeAktif && $p->id === $periodeAktif->id ? 'selected' : '' }}>
          {{ $p->nama }}{{ $p->status === 'Aktif' ? ' (Aktif)' : '' }}
        </option>
      @endforeach
    </select>
  </div>

  {{-- Empty state --}}
  @if($enrollments->isEmpty())
  <div class="animate-fadeUp d2 rounded-2xl border py-16 text-center" style="background:var(--surface);border-color:var(--border)">
    <div class="a-bg-lt a-text w-16 h-16 rounded-2xl grid place-items-center text-2xl mx-auto mb-4">
      <i class="fa-solid fa-door-open"></i>
    </div>
    <p class="font-display font-semibold text-[16px] mb-1" style="color:var(--text)">Belum Ada Kelas</p>
    <p class="text-[13px]" style="color:var(--muted)">Anda belum terdaftar di kelas manapun.</p>
  </div>
  @else

  {{-- No result state (hidden by default) --}}
  <div id="empty-filter" class="hidden animate-fadeUp d2 rounded-2xl border py-12 text-center" style="background:var(--surface);border-color:var(--border)">
    <div class="a-bg-lt a-text w-14 h-14 rounded-2xl grid place-items-center text-xl mx-auto mb-3">
      <i class="fa-solid fa-filter-circle-xmark"></i>
    </div>
    <p class="text-[13px]" style="color:var(--muted)">Tidak ada kelas yang sesuai filter.</p>
  </div>

  {{-- Kelas grid --}}
  <div id="kelas-grid" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
    @foreach($enrollments as $enrollment)
    @php
      $kelas   = $enrollment->kelas;
      $mk      = $kelas->mataKuliah;
      $periode = $kelas->periodeAkademik;
      $grade   = $enrollment->grade;

      $statusCls = match($enrollment->status) {
        'Aktif'   => 'bg-emerald-500/15 text-emerald-400',
        'Lulus'   => 'bg-blue-500/15 text-blue-400',
        'Dropout' => 'bg-rose-500/15 text-rose-400',
        default   => 'bg-slate-500/15 text-slate-400',
      };
      $gradeCls = match($grade) {
        'A' => 'bg-emerald-500/15 text-emerald-400',
        'B' => 'bg-blue-500/15 text-blue-400',
        'C' => 'bg-amber-500/15 text-amber-400',
        'D' => 'bg-orange-500/15 text-orange-400',
        'E' => 'bg-rose-500/15 text-rose-400',
        default => '',
      };
      $isPeriodeAktif = $periodeAktif && $kelas->periode_akademik_id === $periodeAktif->id;
    @endphp
    <div class="kelas-card animate-fadeUp d2 rounded-2xl border overflow-hidden flex flex-col transition-all duration-200"
      style="background:var(--surface);border-color:var(--border)"
      data-status="{{ strtolower($enrollment->status) }}"
      data-periode="{{ $kelas->periode_akademik_id }}"
      onmouseover="this.style.borderColor='var(--ac)';this.style.transform='translateY(-2px)'"
      onmouseout="this.style.borderColor='var(--border)';this.style.transform='translateY(0)'">

      {{-- Card header gradient --}}
      <div class="h-1.5 a-grad"></div>

      <div class="p-5 flex flex-col flex-1 gap-4">

        {{-- Top row: kode + badges --}}
        <div class="flex items-start justify-between gap-2">
          <div class="flex items-center gap-2 flex-wrap">
            <span class="font-mono font-bold text-[12px] px-2.5 py-1 rounded-lg a-bg-lt a-text">
              {{ $kelas->kodeDisplay }}
            </span>
            @if($isPeriodeAktif)
            <span class="text-[10.5px] font-bold px-2 py-0.5 rounded-full"
              style="background:rgba(16,185,129,.12);color:#10b981">
              <i class="fa-solid fa-circle text-[7px] mr-0.5"></i>Aktif
            </span>
            @endif
          </div>
          <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold flex-shrink-0 {{ $statusCls }}">
            <i class="fa-solid fa-circle text-[7px]"></i>{{ $enrollment->status }}
          </span>
        </div>

        {{-- Mata kuliah info --}}
        <div class="flex-1">
          <h3 class="font-display font-bold text-[15px] leading-snug mb-1" style="color:var(--text)">
            {{ $mk?->nama ?? '—' }}
          </h3>
          <p class="text-[12px]" style="color:var(--muted)">
            <i class="fa-solid fa-calendar-days mr-1.5"></i>{{ $periode?->nama ?? '—' }}
          </p>
        </div>

        {{-- Instruktur --}}
        @if($kelas->instruktur->isNotEmpty())
        <div class="flex items-center gap-2 flex-wrap">
          @foreach($kelas->instruktur->take(2) as $ins)
          <div class="flex items-center gap-1.5">
            <div class="w-6 h-6 rounded-full a-grad grid place-items-center text-[9px] text-white font-bold flex-shrink-0">
              {{ strtoupper(substr($ins->nama, 0, 1)) }}
            </div>
            <span class="text-[12px]" style="color:var(--sub)">{{ $ins->nama }}</span>
          </div>
          @endforeach
          @if($kelas->instruktur->count() > 2)
          <span class="text-[11px]" style="color:var(--muted)">+{{ $kelas->instruktur->count() - 2 }} lainnya</span>
          @endif
        </div>
        @endif

        {{-- Divider --}}
        <div style="border-top:1px solid var(--border)"></div>

        {{-- Lihat Materi link --}}
        <a href="{{ route('mahasiswa.kelas.show', $kelas->id) }}"
           class="flex items-center justify-center gap-1.5 py-1 text-[12px] font-semibold a-text hover:opacity-70 transition-opacity">
          <i class="fa-solid fa-book-open text-[11px]"></i>Lihat Materi Kuliah
        </a>

        <div style="border-top:1px solid var(--border)"></div>

        {{-- Bottom: SKS + Nilai --}}
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-3">
            <div class="text-center">
              <div class="font-display font-bold text-[18px]" style="color:var(--text)">{{ $mk?->sks ?? '—' }}</div>
              <div class="text-[10px] uppercase tracking-wide" style="color:var(--muted)">SKS</div>
            </div>
          </div>

          {{-- Nilai & grade --}}
          @if($enrollment->nilai_akhir !== null)
          <div class="flex items-center gap-2">
            <div class="text-right">
              <div class="font-display font-bold text-[18px]" style="color:var(--text)">
                {{ number_format($enrollment->nilai_akhir, 1) }}
              </div>
              <div class="text-[10px] uppercase tracking-wide" style="color:var(--muted)">Nilai</div>
            </div>
            @if($grade)
            <div class="w-9 h-9 rounded-xl grid place-items-center font-display font-bold text-[15px] {{ $gradeCls }}">
              {{ $grade }}
            </div>
            @endif
          </div>
          @else
          <div class="text-right">
            <div class="text-[12px]" style="color:var(--muted)">
              <i class="fa-regular fa-clock mr-1"></i>Belum dinilai
            </div>
            {{-- Progress bar placeholder --}}
            <div class="mt-1.5 w-28 h-1.5 rounded-full overflow-hidden" style="background:var(--surface2)">
              <div class="h-full a-grad rounded-full" style="width:0%"></div>
            </div>
          </div>
          @endif
        </div>

      </div>
    </div>
    @endforeach
  </div>

  @endif

</div>
@endsection

@push('scripts')
<script>
const cards     = Array.from(document.querySelectorAll('.kelas-card'));
const emptyEl   = document.getElementById('empty-filter');
let currentTab  = 'semua';

// Count per status for tab badges
const counts = { semua: cards.length, aktif: 0, lulus: 0, dropout: 0 };
cards.forEach(c => {
  const s = c.dataset.status;
  if (counts[s] !== undefined) counts[s]++;
});
Object.keys(counts).forEach(k => {
  const el = document.querySelector('.tab-count-' + k);
  if (el) el.textContent = counts[k] > 0 ? '(' + counts[k] + ')' : '';
});

function setTab(tab) {
  currentTab = tab;
  document.querySelectorAll('.tab-btn').forEach(btn => {
    const active = btn.dataset.tab === tab;
    btn.classList.toggle('tab-active', active);
    btn.classList.toggle('tab-inactive', !active);
  });
  filterKelas();
}

function filterKelas() {
  const periode = document.getElementById('filter-periode')?.value ?? '';
  let visible = 0;
  cards.forEach(card => {
    const statusMatch  = currentTab === 'semua' || card.dataset.status === currentTab;
    const periodeMatch = !periode || card.dataset.periode === periode;
    const show = statusMatch && periodeMatch;
    card.classList.toggle('hidden', !show);
    if (show) visible++;
  });
  if (emptyEl) emptyEl.classList.toggle('hidden', visible > 0);
}

// Tab styles
const style = document.createElement('style');
style.textContent = `
  .tab-active  { background:var(--ac); color:#fff; }
  .tab-inactive{ color:var(--muted); }
  .tab-inactive:hover { color:var(--text); }
`;
document.head.appendChild(style);

// Init: run filter on load (periode aktif pre-selected)
filterKelas();

// ── QR Scan ─────────────────────────────────────────────────────────────────
const JOIN_URL = "{{ route('mahasiswa.kelas.join') }}";
const CSRF     = "{{ csrf_token() }}";

let qrStream = null;
let qrFound  = false;
let qrAnim   = null;

// Prefer native BarcodeDetector; fallback ke jsQR (self-hosted)
let qrDetector = null;
try {
  if ('BarcodeDetector' in window) {
    qrDetector = new BarcodeDetector({ formats: ['qr_code'] });
  }
} catch {}

let activeTab = 'camera';

function openQrModal() {
  qrFound = false;
  document.getElementById('qr-modal').style.display = 'flex';
  setQrStatus('', '');
  switchQrTab('camera');
}

function closeQrModal() {
  stopCamera();
  document.getElementById('qr-modal').style.display = 'none';
  // Reset upload panel
  resetUpload();
}

function switchQrTab(tab) {
  activeTab = tab;
  const isCamera = tab === 'camera';

  document.getElementById('panel-camera').style.display = isCamera ? '' : 'none';
  document.getElementById('panel-upload').style.display = isCamera ? 'none' : '';

  const tCam = document.getElementById('tab-camera');
  const tUp  = document.getElementById('tab-upload');
  tCam.style.background = isCamera ? '#fff' : 'transparent';
  tCam.style.color      = isCamera ? '#111' : 'rgba(255,255,255,.6)';
  tUp.style.background  = !isCamera ? '#fff' : 'transparent';
  tUp.style.color       = !isCamera ? '#111' : 'rgba(255,255,255,.6)';

  setQrStatus('', '');

  if (isCamera) {
    qrFound = false;
    const line = document.getElementById('qr-scan-line');
    if (line) line.style.animationPlayState = 'running';
    startCamera();
  } else {
    stopCamera();
    resetUpload();
  }
}

function startCamera() {
  const video   = document.getElementById('qr-video');
  const offEl   = document.getElementById('camera-off');
  offEl.style.display = 'none';
  navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } })
    .then(stream => {
      qrStream = stream;
      video.srcObject = stream;
      video.play();
      requestAnimationFrame(scanFrame);
    })
    .catch(() => {
      offEl.style.display = 'flex';
      setQrStatus('Kamera tidak bisa diakses. Gunakan tab Upload Gambar.', 'error');
    });
}

function stopCamera() {
  if (qrStream) { qrStream.getTracks().forEach(t => t.stop()); qrStream = null; }
  if (qrAnim)   { cancelAnimationFrame(qrAnim); qrAnim = null; }
  const video = document.getElementById('qr-video');
  video.srcObject = null;
}

let _lastScanTime = 0;

async function scanFrame() {
  if (qrFound || !qrStream) return;
  const video = document.getElementById('qr-video');

  if (video.readyState !== video.HAVE_ENOUGH_DATA || video.paused) {
    qrAnim = requestAnimationFrame(scanFrame);
    return;
  }

  // Throttle: scan tiap 200ms agar tidak terlalu berat
  const now = performance.now();
  if (now - _lastScanTime < 200) {
    qrAnim = requestAnimationFrame(scanFrame);
    return;
  }
  _lastScanTime = now;

  // Selalu gambar ke canvas dulu (lebih reliable dari detect(video) langsung)
  const canvas = document.getElementById('qr-canvas');
  const vw = video.videoWidth, vh = video.videoHeight;
  if (!vw || !vh) { qrAnim = requestAnimationFrame(scanFrame); return; }

  canvas.width  = vw;
  canvas.height = vh;
  const ctx = canvas.getContext('2d');
  ctx.drawImage(video, 0, 0, vw, vh);

  let found = false;

  // 1. Coba BarcodeDetector via ImageData → createImageBitmap
  if (qrDetector) {
    try {
      const bitmap = await createImageBitmap(canvas);
      const codes  = await qrDetector.detect(bitmap);
      bitmap.close();
      if (codes.length > 0) { handleQrResult(codes[0].rawValue); return; }
      found = true; // detector tersedia, hasil kosong — lanjut
    } catch {}
  }

  // 2. jsQR fallback
  if (!found && typeof jsQR !== 'undefined') {
    const imageData = ctx.getImageData(0, 0, vw, vh);
    const code = jsQR(imageData.data, imageData.width, imageData.height, { inversionAttempts: 'attemptBoth' });
    if (code) { handleQrResult(code.data); return; }
  }

  qrAnim = requestAnimationFrame(scanFrame);
}

function handleQrResult(text) {
  text = (text || '').trim();
  let token = null;
  // Coba parse sebagai URL dulu
  try {
    const u = new URL(text);
    token = u.searchParams.get('token');
  } catch {}
  // Jika bukan URL, coba ambil dari query string saja
  if (!token && text.includes('token=')) {
    const m = text.match(/[?&]token=([^&]+)/);
    if (m) token = decodeURIComponent(m[1]);
  }
  // Jika masih kosong, anggap seluruh teks adalah token (UUID)
  if (!token) {
    const uuidRe = /^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i;
    if (uuidRe.test(text)) token = text;
  }
  if (!token) {
    setQrStatus('QR tidak valid. Coba lagi.', 'error');
    return;
  }
  qrFound = true;
  stopCamera();
  setQrStatus('QR terdeteksi. Mendaftar ke kelas…', 'loading');

  fetch(JOIN_URL, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
    body: JSON.stringify({ token }),
  })
  .then(r => r.json().then(d => ({ ok: r.ok, d })))
  .then(({ ok, d }) => {
    if (ok || d.already) {
      setQrStatus(d.message, 'success');
      if (!d.already) setTimeout(() => location.reload(), 1800);
    } else {
      setQrStatus(d.message || 'Gagal bergabung ke kelas.', 'error');
      qrFound = false;
    }
  })
  .catch(() => setQrStatus('Terjadi kesalahan. Coba lagi.', 'error'));
}

function setQrStatus(msg, type) {
  const el = document.getElementById('qr-status');
  el.textContent = msg;
  el.style.color = type === 'error' ? '#f87171' : type === 'success' ? '#34d399' : 'rgba(255,255,255,.55)';
  const line = document.getElementById('qr-scan-line');
  if (line) line.style.animationPlayState = (type === 'success' || type === 'error') ? 'paused' : 'running';
}

// ── Upload QR ────────────────────────────────────────────────────────────────
function handleQrFileDrop(e) {
  const file = e.dataTransfer.files[0];
  if (file) decodeQrFromFile(file);
}

function handleQrUpload(input) {
  const file = input.files[0];
  if (file) decodeQrFromFile(file);
  input.value = ''; // reset so same file can be re-selected
}

function decodeQrFromFile(file) {
  if (!file.type.startsWith('image/')) {
    setQrStatus('File harus berupa gambar.', 'error');
    return;
  }
  setQrStatus('Membaca gambar…', '');

  const reader = new FileReader();
  reader.onload = (e) => {
    const img = new Image();
    img.onload = () => {
      // Show preview
      document.getElementById('upload-placeholder').style.display = 'none';
      const prev = document.getElementById('upload-preview');
      document.getElementById('upload-img').src = e.target.result;
      prev.style.display = '';

      // Draw to canvas and decode
      const canvas = document.getElementById('qr-canvas');
      canvas.width  = img.width;
      canvas.height = img.height;
      const ctx = canvas.getContext('2d');
      ctx.drawImage(img, 0, 0);
      const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);

      let decoded = null;

      // Try BarcodeDetector first
      if (qrDetector) {
        qrDetector.detect(img).then(codes => {
          if (codes.length > 0) {
            handleQrResult(codes[0].rawValue);
          } else {
            // Fallback ke jsQR
            decoded = jsQRFallback(imageData);
            if (decoded) handleQrResult(decoded);
            else setQrStatus('QR tidak terbaca. Coba foto lebih jelas atau dekat.', 'error');
          }
        }).catch(() => {
          decoded = jsQRFallback(imageData);
          if (decoded) handleQrResult(decoded);
          else setQrStatus('QR tidak terbaca. Coba foto lebih jelas atau dekat.', 'error');
        });
      } else {
        decoded = jsQRFallback(imageData);
        if (decoded) handleQrResult(decoded);
        else setQrStatus('QR tidak terbaca. Coba foto lebih jelas atau dekat.', 'error');
      }
    };
    img.src = e.target.result;
  };
  reader.readAsDataURL(file);
}

function jsQRFallback(imageData) {
  if (typeof jsQR === 'undefined') return null;
  const code = jsQR(imageData.data, imageData.width, imageData.height, { inversionAttempts: 'attemptBoth' });
  return code ? code.data : null;
}

function resetUpload() {
  document.getElementById('upload-placeholder').style.display = '';
  document.getElementById('upload-preview').style.display = 'none';
  document.getElementById('upload-img').src = '';
  document.getElementById('qr-file-input').value = '';
}
</script>

{{-- QR Scan Modal --}}
<style>
@keyframes scan-line {
  0%   { top: 8%; opacity: 1; }
  48%  { opacity: 1; }
  50%  { top: 88%; opacity: .6; }
  52%  { opacity: 1; }
  100% { top: 8%; opacity: 1; }
}
.qr-scan-line {
  position: absolute;
  left: 0; right: 0;
  height: 2px;
  background: linear-gradient(90deg, transparent, #34d399, #34d399, transparent);
  box-shadow: 0 0 8px 2px rgba(52,211,153,.7);
  animation: scan-line 2s linear infinite;
}
.qr-corner {
  position: absolute;
  width: 22px; height: 22px;
}
.qr-corner::before, .qr-corner::after {
  content: '';
  position: absolute;
  background: #fff;
  border-radius: 2px;
}
/* top-left */
.qr-corner.tl::before { top:0; left:0; width:100%; height:3px; }
.qr-corner.tl::after  { top:0; left:0; width:3px; height:100%; }
/* top-right */
.qr-corner.tr::before { top:0; right:0; width:100%; height:3px; }
.qr-corner.tr::after  { top:0; right:0; width:3px; height:100%; }
/* bottom-left */
.qr-corner.bl::before { bottom:0; left:0; width:100%; height:3px; }
.qr-corner.bl::after  { bottom:0; left:0; width:3px; height:100%; }
/* bottom-right */
.qr-corner.br::before { bottom:0; right:0; width:100%; height:3px; }
.qr-corner.br::after  { bottom:0; right:0; width:3px; height:100%; }
</style>

<div id="qr-modal" class="fixed inset-0 z-50 items-center justify-center"
  style="display:none;background:rgba(0,0,0,.88)" onclick="if(event.target===this)closeQrModal()">

  <div style="width:100%;max-width:380px;margin:0 auto;padding:0 16px">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-4">
      <h3 class="font-display font-bold text-[16px] text-white">
        <i class="fa-solid fa-qrcode mr-2" style="color:#34d399"></i>Gabung Kelas via QR
      </h3>
      <button onclick="closeQrModal()"
        class="w-9 h-9 rounded-xl grid place-items-center transition-opacity hover:opacity-70"
        style="background:rgba(255,255,255,.12);color:#fff">
        <i class="fa-solid fa-xmark"></i>
      </button>
    </div>

    {{-- Tab switcher --}}
    <div style="display:flex;gap:6px;margin-bottom:14px;background:rgba(255,255,255,.08);border-radius:12px;padding:4px">
      <button id="tab-camera" onclick="switchQrTab('camera')"
        style="flex:1;padding:7px;border:none;border-radius:9px;font-size:12px;font-weight:600;cursor:pointer;transition:all .15s;background:#fff;color:#111">
        <i class="fa-solid fa-camera mr-1.5"></i>Kamera
      </button>
      <button id="tab-upload" onclick="switchQrTab('upload')"
        style="flex:1;padding:7px;border:none;border-radius:9px;font-size:12px;font-weight:600;cursor:pointer;transition:all .15s;background:transparent;color:rgba(255,255,255,.6)">
        <i class="fa-solid fa-image mr-1.5"></i>Upload Gambar
      </button>
    </div>

    {{-- TAB: Kamera --}}
    <div id="panel-camera">
      <div style="position:relative;border-radius:20px;overflow:hidden;background:#111;aspect-ratio:1">
        <video id="qr-video" style="width:100%;height:100%;object-fit:cover;display:block" playsinline muted></video>

        {{-- Overlay gelap 4 sisi --}}
        <div style="position:absolute;inset:0 0 auto 0;height:12%;background:rgba(0,0,0,.55)"></div>
        <div style="position:absolute;inset:auto 0 0 0;height:12%;background:rgba(0,0,0,.55)"></div>
        <div style="position:absolute;top:12%;bottom:12%;left:0;width:12%;background:rgba(0,0,0,.55)"></div>
        <div style="position:absolute;top:12%;bottom:12%;right:0;width:12%;background:rgba(0,0,0,.55)"></div>

        {{-- Frame kotak scan --}}
        <div style="position:absolute;top:12%;left:12%;right:12%;bottom:12%;border:1.5px solid rgba(255,255,255,.3);border-radius:12px;pointer-events:none">
          <div class="qr-corner tl" style="top:-1px;left:-1px"></div>
          <div class="qr-corner tr" style="top:-1px;right:-1px"></div>
          <div class="qr-corner bl" style="bottom:-1px;left:-1px"></div>
          <div class="qr-corner br" style="bottom:-1px;right:-1px"></div>
          <div class="qr-scan-line" id="qr-scan-line"></div>
        </div>

        {{-- Camera off state --}}
        <div id="camera-off" style="display:none;position:absolute;inset:0;background:#111;align-items:center;justify-content:center;flex-direction:column;gap:8px;color:rgba(255,255,255,.4)">
          <i class="fa-solid fa-video-slash text-[32px]"></i>
          <span style="font-size:12px">Kamera tidak aktif</span>
        </div>
      </div>
      <p style="text-align:center;font-size:11.5px;color:rgba(255,255,255,.4);margin-top:8px">
        Arahkan kamera ke QR code dari instruktur
      </p>
    </div>

    {{-- TAB: Upload --}}
    <div id="panel-upload" style="display:none">
      <input type="file" id="qr-file-input" accept="image/*" style="display:none" onchange="handleQrUpload(this)">

      <div id="upload-drop-zone" onclick="document.getElementById('qr-file-input').click()"
        style="aspect-ratio:1;border:2px dashed rgba(255,255,255,.25);border-radius:20px;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:12px;cursor:pointer;transition:border-color .15s;background:rgba(255,255,255,.04)"
        ondragover="event.preventDefault();this.style.borderColor='#34d399'"
        ondragleave="this.style.borderColor='rgba(255,255,255,.25)'"
        ondrop="event.preventDefault();this.style.borderColor='rgba(255,255,255,.25)';handleQrFileDrop(event)">
        <div id="upload-preview" style="display:none;width:100%;height:100%;position:relative">
          <img id="upload-img" style="width:100%;height:100%;object-fit:contain;border-radius:18px">
        </div>
        <div id="upload-placeholder">
          <i class="fa-solid fa-image text-[40px]" style="color:rgba(255,255,255,.25)"></i>
          <p style="font-size:13px;font-weight:600;color:rgba(255,255,255,.5);text-align:center">Tap untuk pilih foto QR code</p>
          <p style="font-size:11px;color:rgba(255,255,255,.3);text-align:center">atau drag & drop gambar di sini</p>
        </div>
      </div>

      <button onclick="document.getElementById('qr-file-input').click()"
        style="width:100%;margin-top:10px;padding:10px;border-radius:12px;border:none;background:rgba(52,211,153,.15);color:#34d399;font-size:13px;font-weight:600;cursor:pointer">
        <i class="fa-solid fa-folder-open mr-1.5"></i>Pilih Gambar QR
      </button>
    </div>

    <canvas id="qr-canvas" style="display:none"></canvas>

    {{-- Status --}}
    <div id="qr-status" style="min-height:38px;margin-top:12px;text-align:center;font-size:13px;font-weight:500;color:rgba(255,255,255,.55);padding:0 4px"></div>

    <button onclick="closeQrModal()"
      style="width:100%;padding:10px;border-radius:12px;border:none;background:rgba(255,255,255,.08);color:rgba(255,255,255,.7);font-size:13px;font-weight:600;cursor:pointer;margin-top:4px">
      Batal
    </button>

  </div>
</div>

<script src="{{ asset('js/jsqr.js') }}"></script>
@endpush
