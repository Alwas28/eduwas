<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ $ujian->judul }} — EduLearn Ujian</title>
<link href="https://fonts.googleapis.com/css2?family=Clash+Display:wght@400;600;700&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
@vite(['resources/css/app.css', 'resources/js/app.js'])
<style>
*{box-sizing:border-box;margin:0;padding:0}
:root{
  --bg:#0a0e1a;--surface:#111827;--surface2:#161d2e;
  --border:#1e2a42;--text:#e2e8f0;--muted:#64748b;--sub:#94a3b8;
  --ac:#10b981;--ac2:#06b6d4;--ac-rgb:16,185,129;--ac-lt:rgba(16,185,129,.14);
}
body{
  font-family:'Plus Jakarta Sans',sans-serif;
  background:var(--bg);color:var(--text);min-height:100vh;
  /* Anti-screenshot: user-select off */
  -webkit-user-select:none;-moz-user-select:none;user-select:none;
  -webkit-touch-callout:none;
}
h1,h2,h3{font-family:'Clash Display',sans-serif}

/* Watermark */
#watermark{
  position:fixed;inset:0;pointer-events:none;z-index:1;
  display:flex;flex-direction:column;
  overflow:hidden;opacity:.04;
}
.wm-row{display:flex;gap:80px;padding:40px 0;white-space:nowrap;font-size:14px;font-weight:700;color:#fff;letter-spacing:2px}
.wm-row:nth-child(even){margin-left:160px}

/* Header bar */
#exam-header{
  position:fixed;top:0;left:0;right:0;z-index:50;
  background:rgba(17,24,39,.95);backdrop-filter:blur(16px);
  border-bottom:1px solid var(--border);
  height:60px;display:flex;align-items:center;padding:0 20px;gap:16px;
}
.exam-title{font-size:14px;font-weight:700;color:var(--text);flex:1;min-width:0;
  white-space:nowrap;overflow:hidden;text-overflow:ellipsis}

/* Timer */
#timer-wrap{display:flex;align-items:center;gap:8px;flex-shrink:0}
#timer{font-family:'Clash Display',monospace;font-size:22px;font-weight:700;color:var(--ac);min-width:80px;text-align:center}
#timer.warn{color:#f59e0b;animation:pulse 1s infinite}
#timer.danger{color:#f87171;animation:pulse .5s infinite}
@keyframes pulse{0%,100%{opacity:1}50%{opacity:.5}}

/* Violation badge */
#violation-badge{
  display:flex;align-items:center;gap:5px;
  background:rgba(248,113,113,.12);border:1px solid rgba(248,113,113,.25);
  border-radius:8px;padding:4px 10px;font-size:11px;font-weight:700;color:#f87171;
}

/* Submit button */
#btn-submit-final{
  display:flex;align-items:center;gap:6px;
  padding:8px 18px;border-radius:10px;font-size:13px;font-weight:700;
  background:linear-gradient(135deg,var(--ac),var(--ac2));color:#fff;
  border:none;cursor:pointer;transition:opacity .15s;flex-shrink:0;
}
#btn-submit-final:hover{opacity:.88}

/* Main content */
#exam-body{margin-top:60px;padding:24px 16px 80px;max-width:860px;margin-left:auto;margin-right:auto;position:relative;z-index:2}

/* Question card */
.q-card{
  background:var(--surface);border:1px solid var(--border);
  border-radius:16px;margin-bottom:16px;overflow:hidden;
  transition:border-color .15s;
}
.q-card.answered{border-color:rgba(16,185,129,.35)}
.q-head{
  padding:14px 18px;display:flex;align-items:flex-start;gap:12px;
  border-bottom:1px solid var(--border);
}
.q-num{
  width:28px;height:28px;border-radius:8px;flex-shrink:0;
  background:var(--ac-lt);color:var(--ac);font-size:12px;font-weight:700;
  display:grid;place-items:center;
}
.q-num.essay-num{background:rgba(139,92,246,.12);color:#a78bfa}
.q-text{font-size:14px;line-height:1.7;color:var(--text)}
.q-body{padding:16px 18px}

/* PG Options */
.pg-option{
  display:flex;align-items:flex-start;gap:10px;
  padding:10px 14px;border-radius:10px;border:1.5px solid var(--border);
  cursor:pointer;margin-bottom:8px;transition:all .15s;
}
.pg-option:hover{border-color:var(--ac);background:var(--ac-lt)}
.pg-option.selected{border-color:var(--ac);background:var(--ac-lt)}
.pg-option input[type=radio]{display:none}
.pg-opt-letter{
  width:24px;height:24px;border-radius:6px;background:var(--surface2);
  border:1.5px solid var(--border);flex-shrink:0;display:grid;place-items:center;
  font-size:11px;font-weight:700;color:var(--muted);transition:all .15s;
}
.pg-option.selected .pg-opt-letter{background:var(--ac);border-color:var(--ac);color:#fff}
.pg-opt-text{font-size:13.5px;line-height:1.65;color:var(--text);padding-top:2px}

/* Essay textarea */
.essay-area{
  width:100%;background:var(--surface2);border:1.5px solid var(--border);
  color:var(--text);border-radius:10px;padding:12px 14px;font-size:13.5px;
  font-family:inherit;outline:none;resize:vertical;min-height:140px;
  transition:border-color .15s;
}
.essay-area:focus{border-color:var(--ac)}

/* Bottom summary bar */
#summary-bar{
  position:fixed;bottom:0;left:0;right:0;z-index:50;
  background:rgba(17,24,39,.97);border-top:1px solid var(--border);
  padding:10px 20px;display:flex;align-items:center;gap:12px;flex-wrap:wrap;
}
.sum-dot{width:10px;height:10px;border-radius:50%;display:inline-block;flex-shrink:0}
.sum-dot.answered{background:var(--ac)}
.sum-dot.unanswered{background:var(--surface2);border:1.5px solid var(--border)}
#save-status{font-size:11px;color:var(--muted);margin-left:auto}

/* Violation overlay */
#violation-overlay{
  position:fixed;inset:0;z-index:200;display:none;
  background:rgba(0,0,0,.85);backdrop-filter:blur(8px);
  flex-direction:column;align-items:center;justify-content:center;gap:16px;
}
#violation-overlay.show{display:flex}
.viol-box{
  background:var(--surface);border:1.5px solid rgba(248,113,113,.5);
  border-radius:20px;padding:32px;max-width:400px;width:90%;text-align:center;
}

/* Time-up overlay */
#timeup-overlay{
  position:fixed;inset:0;z-index:300;display:none;
  background:rgba(0,0,0,.9);backdrop-filter:blur(8px);
  flex-direction:column;align-items:center;justify-content:center;gap:16px;
}
#timeup-overlay.show{display:flex}

/* Disable print */
@media print{body{display:none!important}}
</style>
</head>
<body>

{{-- Watermark --}}
<div id="watermark" aria-hidden="true">
  @for($r = 0; $r < 12; $r++)
  <div class="wm-row">
    @for($c = 0; $c < 6; $c++)
    <span>{{ auth()->user()->name }} &bull; {{ auth()->user()->email }}</span>
    @endfor
  </div>
  @endfor
</div>

{{-- Header --}}
<div id="exam-header">
  <div style="width:32px;height:32px;border-radius:9px;background:linear-gradient(135deg,var(--ac),var(--ac2));display:grid;place-items:center;color:#fff;font-size:14px;flex-shrink:0">
    <i class="fas fa-file-pen"></i>
  </div>
  <div class="exam-title">{{ $ujian->judul }}</div>

  <div id="timer-wrap">
    <i class="fas fa-clock" style="color:var(--muted);font-size:13px"></i>
    <div id="timer">00:00</div>
  </div>

  <div id="violation-badge">
    <i class="fas fa-triangle-exclamation"></i>
    <span id="viol-count">0</span> Pelanggaran
  </div>

  <button id="btn-submit-final" onclick="confirmSubmit()">
    <i class="fas fa-paper-plane"></i> Kumpulkan
  </button>
</div>

{{-- Exam body --}}
<div id="exam-body">

  {{-- AI Monitor notice --}}
  <div style="background:rgba(139,92,246,.08);border:1px solid rgba(139,92,246,.2);border-radius:12px;padding:12px 16px;margin-bottom:20px;display:flex;align-items:center;gap:10px;">
    <i class="fas fa-robot" style="color:#a78bfa;font-size:14px;flex-shrink:0"></i>
    <span style="font-size:12.5px;color:rgba(167,139,250,.9)">
      Ujian ini diawasi secara real-time oleh AI dan pengawas. Tetap jaga integritas akademik Anda.
    </span>
    <span style="margin-left:auto;font-size:11px;color:rgba(167,139,250,.6);white-space:nowrap">
      <i class="fas fa-circle animate-pulse" style="font-size:7px;color:#a78bfa"></i> Live
    </span>
  </div>

  {{-- Questions --}}
  @foreach($soalList as $i => $soal)
  @php
    $jawaban = $jawabanMap->get($soal->id);
    $letters = ['A','B','C','D','E','F'];
  @endphp
  <div class="q-card {{ $jawaban ? 'answered' : '' }}" id="qcard-{{ $soal->id }}" data-soal="{{ $soal->id }}">
    <div class="q-head">
      <div class="q-num {{ $soal->tipe === 'essay' ? 'essay-num' : '' }}">{{ $i+1 }}</div>
      <div>
        <div class="q-text">{!! $soal->pertanyaan !!}</div>
        <div style="margin-top:6px;font-size:11px;color:var(--muted)">
          {{ $soal->tipe === 'essay' ? 'Essay' : 'Pilihan Ganda' }} &bull;
          Bobot {{ $soal->bobot ?? 1 }}
        </div>
      </div>
    </div>
    <div class="q-body">
      @if($soal->tipe === 'pilihan_ganda')
        @php
          $orderedPilihan = collect($soal->pilihan_order)->map(fn($idx) => $soal->pilihan->values()->get($idx))->filter();
          $savedPg = $jawaban?->jawaban_pg;
        @endphp
        @foreach($orderedPilihan as $pi => $p)
        <label class="pg-option {{ $savedPg === $pi ? 'selected' : '' }}"
          id="opt-{{ $soal->id }}-{{ $pi }}"
          onclick="selectPg({{ $soal->id }}, {{ $pi }}, this)">
          <input type="radio" name="pg_{{ $soal->id }}" value="{{ $pi }}"
            {{ $savedPg === $pi ? 'checked' : '' }}>
          <div class="pg-opt-letter">{{ $letters[$pi] ?? $pi+1 }}</div>
          <div class="pg-opt-text">{{ $p->teks ?? '' }}</div>
        </label>
        @endforeach
      @else
        <textarea class="essay-area" id="essay-{{ $soal->id }}"
          placeholder="Tulis jawaban Anda di sini…"
          oninput="scheduleEssaySave({{ $soal->id }}, this.value)"
          >{{ $jawaban?->jawaban_essay ?? '' }}</textarea>
      @endif
    </div>
  </div>
  @endforeach

</div>

{{-- Bottom summary bar --}}
<div id="summary-bar">
  <div style="font-size:11.5px;font-weight:700;color:var(--muted);white-space:nowrap">
    Soal terjawab:
  </div>
  <div id="dots-wrap" style="display:flex;gap:5px;flex-wrap:wrap;flex:1">
    @foreach($soalList as $i => $soal)
    <div class="sum-dot {{ $jawabanMap->get($soal->id) ? 'answered' : 'unanswered' }}"
      id="dot-{{ $soal->id }}" title="Soal {{ $i+1 }}"></div>
    @endforeach
  </div>
  <div id="save-status" style="font-size:11px;color:var(--muted);margin-left:auto;"><i class="fas fa-info-circle"></i> Jawaban disimpan saat dikumpulkan</div>
</div>

{{-- Violation overlay --}}
<div id="violation-overlay">
  <div class="viol-box">
    <div style="font-size:36px;margin-bottom:12px">⚠️</div>
    <h3 style="font-size:18px;font-weight:700;color:#f87171;margin-bottom:8px">Peringatan!</h3>
    <p id="viol-msg" style="font-size:13px;color:var(--sub);line-height:1.7;margin-bottom:20px"></p>
    <div style="font-size:12px;color:var(--muted);margin-bottom:20px">
      Pelanggaran ini dicatat dan dilaporkan ke pengawas.
      Pelanggaran ke-<strong id="viol-num" style="color:#f87171">1</strong>
    </div>
    <button onclick="dismissViolation()"
      style="width:100%;padding:10px;border-radius:10px;border:none;cursor:pointer;font-family:inherit;font-size:13px;font-weight:700;background:linear-gradient(135deg,var(--ac),var(--ac2));color:#fff">
      Kembali ke Ujian
    </button>
  </div>
</div>

{{-- Time up overlay --}}
<div id="timeup-overlay">
  <div class="viol-box" style="border-color:rgba(245,158,11,.5)">
    <div style="font-size:36px;margin-bottom:12px">⏰</div>
    <h3 style="font-size:18px;font-weight:700;color:#f59e0b;margin-bottom:8px">Waktu Habis!</h3>
    <p style="font-size:13px;color:var(--sub);line-height:1.7;margin-bottom:20px">
      Waktu ujian telah berakhir. Jawaban Anda akan dikumpulkan secara otomatis.
    </p>
    <button id="btn-timeup-submit"
      style="width:100%;padding:10px;border-radius:10px;border:none;cursor:pointer;font-family:inherit;font-size:13px;font-weight:700;background:linear-gradient(135deg,#f59e0b,#ef4444);color:#fff">
      <i class="fas fa-spinner fa-spin"></i> Mengumpulkan jawaban…
    </button>
  </div>
</div>

<script>
const CSRF    = document.querySelector('meta[name="csrf-token"]').content;
const UJIAN_ID = {{ $ujian->id }};
const SESI_ID  = {{ $sesi->id }};
const SISA_SEK = {{ $sisaDetik }};

const urlViolation = '{{ route('mahasiswa.ujian.violation', $ujian) }}';
const urlSubmit    = '{{ route('mahasiswa.ujian.submit', $ujian) }}';
const urlKeepAlive = '{{ route('mahasiswa.ujian.keep-alive') }}';
const urlSelesai   = '{{ route('mahasiswa.ujian.selesai', $ujian) }}';

// ── State ──────────────────────────────────────────────────────
let violationCount = {{ $sesi->pelanggaran ?? 0 }};
let submitting     = false;
let isSubmitted    = false;

// ── Anti-cheat init ────────────────────────────────────────────

// Disable right-click
document.addEventListener('contextmenu', e => e.preventDefault());

// Disable drag
document.addEventListener('dragstart', e => e.preventDefault());

// Block keyboard shortcuts
document.addEventListener('keydown', function(e) {
  const blocked = [
    e.ctrlKey && ['c','a','s','p','u'].includes(e.key.toLowerCase()),
    e.metaKey && ['c','a','s','p'].includes(e.key.toLowerCase()),
    e.key === 'F12',
    e.key === 'PrintScreen',
    e.key === 'F5',
    e.ctrlKey && e.key === 'r',
    e.ctrlKey && e.shiftKey && ['i','j','c'].includes(e.key.toLowerCase()),
  ];
  if (blocked.some(Boolean)) {
    e.preventDefault();
    e.stopPropagation();
    if (e.key !== 'F5' && !(e.ctrlKey && e.key === 'r')) {
      recordViolation('keyboard_shortcut', `Key: ${e.key}`);
    }
    return false;
  }
});

// Tab switch / visibility change
document.addEventListener('visibilitychange', function() {
  if (document.hidden && !isSubmitted) {
    recordViolation('tab_switch', 'Halaman disembunyikan / berpindah tab');
    showViolationOverlay('Anda terdeteksi berpindah tab atau menyembunyikan halaman ujian.');
  }
});

// Window blur (focus lost to another window)
let blurTimer;
window.addEventListener('blur', function() {
  if (isSubmitted) return;
  blurTimer = setTimeout(() => {
    recordViolation('window_blur', 'Window kehilangan fokus');
    showViolationOverlay('Anda terdeteksi beralih ke aplikasi/window lain.');
  }, 300);
});
window.addEventListener('focus', function() {
  clearTimeout(blurTimer);
});

// Prevent beforeunload (refresh/close)
window.addEventListener('beforeunload', function(e) {
  if (!isSubmitted) {
    e.preventDefault();
    e.returnValue = 'Ujian sedang berlangsung. Yakin ingin keluar?';
    return e.returnValue;
  }
});

// Detect DevTools (size change trick)
let devToolsOpen = false;
setInterval(() => {
  if (window.outerWidth - window.innerWidth > 160 || window.outerHeight - window.innerHeight > 160) {
    if (!devToolsOpen) {
      devToolsOpen = true;
      recordViolation('devtools', 'DevTools terdeteksi terbuka');
    }
  } else {
    devToolsOpen = false;
  }
}, 2000);

// Copy attempt
document.addEventListener('copy', e => {
  e.preventDefault();
  recordViolation('copy_attempt', 'Mencoba menyalin konten');
});
document.addEventListener('cut', e => e.preventDefault());

// ── Timer ───────────────────────────────────────────────────────
let remainingSeconds = SISA_SEK;
const timerEl = document.getElementById('timer');

function formatTime(s) {
  if (s < 0) s = 0;
  const h = Math.floor(s / 3600);
  const m = Math.floor((s % 3600) / 60);
  const sec = s % 60;
  if (h > 0) return `${pad(h)}:${pad(m)}:${pad(sec)}`;
  return `${pad(m)}:${pad(sec)}`;
}
function pad(n) { return String(n).padStart(2, '0'); }

const timerInterval = setInterval(() => {
  remainingSeconds--;
  timerEl.textContent = formatTime(remainingSeconds);

  if (remainingSeconds <= 300 && remainingSeconds > 60) {
    timerEl.className = 'warn';
  } else if (remainingSeconds <= 60) {
    timerEl.className = 'danger';
  }

  if (remainingSeconds <= 0) {
    clearInterval(timerInterval);
    showTimeUp();
  }
}, 1000);

timerEl.textContent = formatTime(remainingSeconds);

// ── Session keep-alive every 4 minutes ─────────────────────────
setInterval(async () => {
  try {
    await fetch(urlKeepAlive, {
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json' },
      body: JSON.stringify({ ujian_id: UJIAN_ID }),
    });
    // Refresh CSRF token from meta (Laravel refreshes it on keepAlive)
    const newToken = document.cookie.match(/XSRF-TOKEN=([^;]+)/)?.[1];
    if (newToken) document.querySelector('meta[name="csrf-token"]').content = decodeURIComponent(newToken);
  } catch {}
}, 4 * 60 * 1000);

// ── Answers ─────────────────────────────────────────────────────
function selectPg(soalId, idx, label) {
  // Remove selected from siblings
  document.querySelectorAll(`[id^="opt-${soalId}-"]`).forEach(el => el.classList.remove('selected'));
  label.classList.add('selected');
  label.querySelector('input').checked = true;
  markAnswered(soalId);
}

function scheduleEssaySave(soalId, val) {
  markAnswered(soalId, !!val.trim());
}


function markAnswered(soalId, answered = true) {
  const card = document.getElementById('qcard-' + soalId);
  const dot  = document.getElementById('dot-' + soalId);
  if (answered) {
    card?.classList.add('answered');
    dot?.classList.replace('unanswered', 'answered');
  } else {
    card?.classList.remove('answered');
    dot?.classList.replace('answered', 'unanswered');
  }
}

// ── Violation ───────────────────────────────────────────────────
async function recordViolation(tipe, catatan) {
  if (isSubmitted) return;
  try {
    const res  = await fetch(urlViolation, {
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json' },
      body: JSON.stringify({ tipe, catatan }),
    });
    const json = await res.json();
    if (json.ok) {
      violationCount = json.total;
      document.getElementById('viol-count').textContent = violationCount;
    }
  } catch {}
}

function showViolationOverlay(msg) {
  if (isSubmitted) return;
  document.getElementById('viol-msg').textContent = msg;
  document.getElementById('viol-num').textContent = violationCount;
  document.getElementById('violation-overlay').classList.add('show');
}

function dismissViolation() {
  document.getElementById('violation-overlay').classList.remove('show');
  window.focus();
}

// ── Submit ──────────────────────────────────────────────────────
function confirmSubmit() {
  const answered   = document.querySelectorAll('.q-card.answered').length;
  const total      = document.querySelectorAll('.q-card').length;
  const unanswered = total - answered;

  let msg = `Anda akan mengumpulkan ujian.\n✅ Terjawab: ${answered}/${total}`;
  if (unanswered > 0) msg += `\n⚠️  Belum dijawab: ${unanswered} soal`;
  msg += '\n\nLanjutkan?';

  if (confirm(msg)) doSubmit();
}

async function doSubmit() {
  if (submitting || isSubmitted) return;
  submitting = true;

  // Collect all essay answers
  const jawaban = {};
  document.querySelectorAll('textarea[id^="essay-"]').forEach(ta => {
    const soalId = ta.id.replace('essay-', '');
    if (!jawaban[soalId]) jawaban[soalId] = {};
    jawaban[soalId].essay = ta.value;
  });
  document.querySelectorAll('input[type=radio]:checked').forEach(r => {
    const soalId = r.name.replace('pg_', '');
    if (!jawaban[soalId]) jawaban[soalId] = {};
    jawaban[soalId].pg = parseInt(r.value);
  });

  try {
    clearInterval(timerInterval);
    const res  = await fetch(urlSubmit, {
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json' },
      body: JSON.stringify({ jawaban }),
    });
    const json = await res.json();
    if (json.ok) {
      isSubmitted = true;
      window.location.href = json.redirect || urlSelesai;
    }
  } catch {
    submitting = false;
    alert('Terjadi kesalahan saat mengumpulkan. Coba lagi.');
  }
}

function showTimeUp() {
  document.getElementById('timeup-overlay').classList.add('show');
  doSubmit();
}

document.getElementById('btn-timeup-submit').addEventListener('click', doSubmit);
</script>
</body>
</html>
