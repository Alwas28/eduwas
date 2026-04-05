@extends('layouts.instruktur')
@section('title', 'Pengawas Ujian')
@section('page-title', 'Pengawas Ujian')

@section('content')
<div class="p-5 space-y-5">

  {{-- Header --}}
  <div class="flex items-center justify-between flex-wrap gap-3">
    <div>
      <h1 class="text-xl font-bold t-text font-display">{{ $ujian->judul }}</h1>
      <p class="text-xs t-muted mt-0.5">
        {{ $ujian->kelas->mataKuliah->nama ?? '' }} &bull;
        {{ $ujian->waktu_mulai->format('H:i') }} — {{ $ujian->waktu_selesai->format('H:i') }}
      </p>
    </div>
    <div class="flex items-center gap-2">
      <div class="flex items-center gap-1.5 text-xs t-muted border t-border rounded-lg px-3 py-1.5">
        <span id="rt-dot" class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse inline-block"></span>
        <span>Live Monitoring</span>
      </div>
      <a href="{{ route('instruktur.ujian.index') }}"
        class="text-xs t-muted border t-border rounded-lg px-3 py-1.5 hover:t-sub transition-colors">
        <i class="fas fa-arrow-left mr-1"></i> Kembali
      </a>
    </div>
  </div>

  {{-- Stats --}}
  <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
    @foreach([
      ['total',        'Total Peserta',     '#94a3b8','fas fa-users'],
      ['mengerjakan',  'Mengerjakan',       '#f59e0b','fas fa-pen-to-square'],
      ['selesai',      'Selesai',           '#10b981','fas fa-circle-check'],
      ['belum_mulai',  'Belum Mulai',       '#64748b','fas fa-clock'],
      ['pelanggaran',  'Total Pelanggaran', '#f87171','fas fa-triangle-exclamation'],
    ] as [$key, $label, $color, $icon])
    <div class="t-surf border t-border rounded-xl p-4">
      <div class="flex items-center gap-2 mb-2">
        <i class="{{ $icon }} text-xs" style="color:{{ $color }}"></i>
        <span class="text-xs t-muted">{{ $label }}</span>
      </div>
      <div class="text-2xl font-bold" style="color:{{ $color }}" id="stat-{{ str_replace('_','-',$key) }}">—</div>
    </div>
    @endforeach
  </div>

  {{-- Alert log --}}
  <div class="t-surf border t-border rounded-xl overflow-hidden" id="log-card">
    <div class="flex items-center justify-between px-4 py-3 border-b t-border">
      <span class="text-sm font-semibold t-text flex items-center gap-2">
        <i class="fas fa-bell text-xs" style="color:#f59e0b"></i> Log Pelanggaran Real-time
      </span>
      <button onclick="clearLog()" class="text-xs t-muted hover:t-sub transition-colors">Hapus Log</button>
    </div>
    <div id="alert-log" style="height:130px;overflow-y:auto;padding:8px 12px;">
      <p id="log-empty" class="text-xs t-muted italic text-center mt-8">Belum ada pelanggaran...</p>
    </div>
  </div>

  {{-- Table --}}
  <div class="t-surf border t-border rounded-xl overflow-hidden">
    <div class="px-4 py-3 border-b t-border flex items-center gap-3 flex-wrap">
      <span class="text-sm font-semibold t-text">Daftar Peserta</span>
      <div class="relative ml-auto">
        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-xs t-muted"></i>
        <input id="search" type="text" placeholder="Cari nama/NIM…" oninput="filterTable(this.value)"
          class="text-xs t-text t-surf2 border t-border rounded-lg pl-7 pr-3 py-1.5 outline-none focus:border-[var(--ac)] w-48">
      </div>
    </div>
    <div style="overflow-x:auto">
      <table class="w-full text-sm">
        <thead>
          <tr class="t-surf2 border-b t-border">
            <th class="px-4 py-3 text-left text-xs font-semibold t-muted uppercase tracking-wider">Peserta</th>
            <th class="px-4 py-3 text-center text-xs font-semibold t-muted uppercase tracking-wider">Status</th>
            <th class="px-4 py-3 text-center text-xs font-semibold t-muted uppercase tracking-wider">Pelanggaran</th>
            <th class="px-4 py-3 text-center text-xs font-semibold t-muted uppercase tracking-wider hidden md:table-cell">Sisa Waktu</th>
            <th class="px-4 py-3 text-center text-xs font-semibold t-muted uppercase tracking-wider hidden lg:table-cell">Ping</th>
            <th class="px-4 py-3 text-center text-xs font-semibold t-muted uppercase tracking-wider">Aksi</th>
          </tr>
        </thead>
        <tbody id="tbl-body">
          <tr><td colspan="6" class="px-4 py-10 text-center t-muted text-sm">
            <i class="fas fa-spinner fa-spin text-xl mb-2 block"></i>Memuat...
          </td></tr>
        </tbody>
      </table>
    </div>
  </div>

</div>

{{-- ── CONFIRM MODAL (custom pengganti confirm()) ── --}}
<div id="confirm-modal"
  style="display:none;position:fixed;inset:0;z-index:10000;background:rgba(0,0,0,.7);backdrop-filter:blur(4px);
         align-items:center;justify-content:center;padding:24px;">
  <div style="
    background:#111827;border:1.5px solid #1e2a42;border-radius:18px;
    max-width:420px;width:100%;padding:28px;text-align:center;
    box-shadow:0 0 40px rgba(0,0,0,.5);
    animation:pop .2s cubic-bezier(.34,1.56,.64,1) both;
  ">
    <div id="cm-icon" style="font-size:40px;margin-bottom:12px">⚠️</div>
    <div id="cm-title" style="font-size:16px;font-weight:700;color:#e2e8f0;margin-bottom:8px">Konfirmasi</div>
    <div id="cm-body" style="font-size:13.5px;color:#64748b;line-height:1.65;margin-bottom:24px"></div>
    <div style="display:flex;gap:10px;justify-content:center">
      <button id="cm-cancel"
        style="flex:1;padding:10px;border-radius:10px;border:1px solid #252d42;background:transparent;
               color:#94a3b8;font-family:inherit;font-size:13px;font-weight:600;cursor:pointer;transition:all .15s"
        onmouseover="this.style.borderColor='#475569';this.style.color='#e2e8f0'"
        onmouseout="this.style.borderColor='#252d42';this.style.color='#94a3b8'">
        Batal
      </button>
      <button id="cm-ok"
        style="flex:1;padding:10px;border-radius:10px;border:none;
               background:linear-gradient(135deg,#f59e0b,#ef4444);
               color:#fff;font-family:inherit;font-size:13px;font-weight:700;cursor:pointer;transition:opacity .15s"
        onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
        Ya, Lanjutkan
      </button>
    </div>
  </div>
</div>

{{-- ── BIG VIOLATION ALERT MODAL ── --}}
<div id="viol-modal"
  style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,.75);backdrop-filter:blur(6px);
         align-items:center;justify-content:center;padding:24px;">
  <div class="vm-card" style="
    background:#111827;border:2px solid rgba(248,113,113,.6);border-radius:20px;
    max-width:480px;width:100%;padding:32px;text-align:center;
    box-shadow:0 0 60px rgba(248,113,113,.25);
    animation:pop .25s cubic-bezier(.34,1.56,.64,1) both;
  ">
    <div style="font-size:48px;margin-bottom:12px">🚨</div>
    <div style="font-size:11px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:#f87171;margin-bottom:6px">
      Pelanggaran Terdeteksi
    </div>
    <div id="vm-nama" style="font-size:22px;font-weight:700;color:#e2e8f0;margin-bottom:4px">—</div>
    <div id="vm-nim" style="font-size:13px;color:#64748b;margin-bottom:20px">—</div>
    <div id="vm-tipe"
      style="display:inline-flex;align-items:center;gap:8px;background:rgba(248,113,113,.12);
             border:1px solid rgba(248,113,113,.3);border-radius:10px;
             padding:10px 20px;font-size:14px;font-weight:700;color:#fca5a5;margin-bottom:8px">
    </div>
    <div id="vm-catatan" style="font-size:12px;color:#64748b;margin-bottom:8px"></div>
    <div id="vm-waktu" style="font-size:11px;color:#475569;margin-bottom:16px"></div>
    <div style="display:flex;gap:10px;justify-content:center;margin-bottom:12px">
      <button onclick="closeViolModal()"
        style="flex:1;padding:11px;border-radius:10px;border:1px solid #252d42;background:transparent;
               color:#94a3b8;font-family:inherit;font-size:13px;font-weight:600;cursor:pointer;transition:all .15s"
        onmouseover="this.style.borderColor='var(--ac)';this.style.color='var(--ac)'"
        onmouseout="this.style.borderColor='#252d42';this.style.color='#94a3b8'">
        Tutup
      </button>
      <button onclick="closeViolModal()"
        style="flex:1;padding:11px;border-radius:10px;border:none;
               background:linear-gradient(135deg,#10b981,#06b6d4);
               color:#fff;font-family:inherit;font-size:13px;font-weight:700;cursor:pointer">
        Oke, Lanjutkan Pantau
      </button>
    </div>
    <div id="vm-countdown" style="font-size:11px;color:#475569"></div>
  </div>
</div>

@push('styles')
<style>
@keyframes pop{from{opacity:0;transform:scale(.85)}to{opacity:1;transform:scale(1)}}
@keyframes shake{0%,100%{transform:translateX(0)}20%,60%{transform:translateX(-6px)}40%,80%{transform:translateX(6px)}}
.shake{animation:shake .4s ease}
</style>
@endpush

<script>
const CSRF    = document.querySelector('meta[name="csrf-token"]').content;
const dataUrl = '{{ route('instruktur.ujian.pengawas.data', $ujian) }}';
const resetBaseUrl = '/instruktur/ujian/{{ $ujian->id }}/reset-sesi/';

let serverTime     = null;
let allRows        = [];
let seenViolKeys   = new Set(); // client-side dedup, cegah alert berulang
let countdownTimer = null;

// ── Audio beep ─────────────────────────────────────────────────
function playBeep(freq = 880, dur = 0.3, vol = 0.5) {
  try {
    const ctx  = new (window.AudioContext || window.webkitAudioContext)();
    const osc  = ctx.createOscillator();
    const gain = ctx.createGain();
    osc.connect(gain); gain.connect(ctx.destination);
    osc.frequency.value = freq;
    gain.gain.setValueAtTime(vol, ctx.currentTime);
    gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + dur);
    osc.start(); osc.stop(ctx.currentTime + dur);
  } catch {}
}
function alarmSound() {
  playBeep(880, 0.25, 0.6);
  setTimeout(() => playBeep(660, 0.2, 0.5), 300);
  setTimeout(() => playBeep(880, 0.25, 0.6), 600);
}

// ── Fetch & poll ───────────────────────────────────────────────
async function fetchData() {
  const params = new URLSearchParams({ since: serverTime || '' });
  try {
    const res  = await fetch(dataUrl + '?' + params);
    if (!res.ok) return;
    const json = await res.json();

    serverTime = json.server_time;
    allRows    = json.rows;
    updateStats(json.stats);
    renderTable(allRows, document.getElementById('search').value);

    if (json.new_violations?.length) {
      // Filter: hanya tampilkan yang belum pernah ditampilkan (dedup client-side)
      const fresh = json.new_violations.filter(v => {
        const key = `${v.mahasiswa_id}|${v.tipe}|${v.waktu}`;
        if (seenViolKeys.has(key)) return false;
        seenViolKeys.add(key);
        return true;
      });

      if (fresh.length) {
        fresh.forEach(v => addLogItem(v));
        alarmSound();
        flashLogCard();
        // Selalu tampilkan violation terbaru, ganti jika modal sudah terbuka
        showViolation(fresh[fresh.length - 1]);
      }
    }
  } catch {}
}

function updateStats(s) {
  const map = {
    'total': 'stat-total', 'mengerjakan': 'stat-mengerjakan',
    'selesai': 'stat-selesai', 'belum_mulai': 'stat-belum-mulai',
    'pelanggaran': 'stat-pelanggaran',
  };
  Object.entries(map).forEach(([k, id]) => {
    const el = document.getElementById(id);
    if (el && s[k] !== undefined) el.textContent = s[k];
  });
}

// ── Table ───────────────────────────────────────────────────────
function renderTable(rows, search = '') {
  const tbody = document.getElementById('tbl-body');
  const q = search.toLowerCase();
  const filtered = q
    ? rows.filter(r => r.nama.toLowerCase().includes(q) || (r.nim||'').includes(q))
    : rows;

  if (!filtered.length) {
    tbody.innerHTML = `<tr><td colspan="6" class="px-4 py-10 text-center t-muted text-sm">Tidak ada data</td></tr>`;
    return;
  }

  tbody.innerHTML = filtered.map(r => {
    const statusHtml = {
      mengerjakan: `<span class="text-xs px-2 py-0.5 rounded-full" style="background:rgba(245,158,11,.12);color:#f59e0b"><i class="fas fa-circle mr-1" style="font-size:6px"></i>Mengerjakan</span>`,
      selesai:     `<span class="text-xs px-2 py-0.5 rounded-full" style="background:rgba(16,185,129,.12);color:#10b981"><i class="fas fa-check mr-1"></i>Selesai ${r.submitted_at||''}</span>`,
      belum_mulai: `<span class="text-xs px-2 py-0.5 rounded-full" style="background:rgba(100,116,139,.12);color:#64748b"><i class="fas fa-clock mr-1"></i>Belum Mulai</span>`,
    }[r.status] || '';

    const viol      = r.jml_pelanggaran;
    const violBg    = viol > 0 ? 'rgba(248,113,113,.12)' : 'transparent';
    const violColor = viol > 0 ? '#f87171' : 'var(--muted)';

    const sisaFmt = r.sisa_detik > 0
      ? `${String(Math.floor(r.sisa_detik/60)).padStart(2,'0')}:${String(r.sisa_detik%60).padStart(2,'0')}`
      : '—';

    const resetBtn = r.sesi_id
      ? `<button onclick="resetSesi(${r.id},'${esc(r.nama)}')"
           class="text-xs px-2 py-1 rounded-lg border transition-all"
           style="border-color:#252d42;color:#94a3b8"
           onmouseover="this.style.borderColor='#f59e0b';this.style.color='#f59e0b'"
           onmouseout="this.style.borderColor='#252d42';this.style.color='#94a3b8'">
           <i class="fas fa-rotate-right mr-1"></i>Ujian Ulang
         </button>`
      : '<span class="text-xs t-muted">—</span>';

    return `<tr class="border-b t-border hover:bg-white/[.02] transition-colors" id="row-${r.id}">
      <td class="px-4 py-3">
        <div class="font-medium t-text text-sm">${esc(r.nama)}</div>
        <div class="text-xs t-muted">${r.nim||''}</div>
      </td>
      <td class="px-4 py-3 text-center">${statusHtml}</td>
      <td class="px-4 py-3 text-center">
        <span class="text-sm font-bold px-2 py-0.5 rounded-lg" style="background:${violBg};color:${violColor}">
          ${viol}x
        </span>
      </td>
      <td class="px-4 py-3 text-center hidden md:table-cell">
        <span class="text-xs font-mono t-muted">${sisaFmt}</span>
      </td>
      <td class="px-4 py-3 text-center hidden lg:table-cell">
        <span class="text-xs t-muted">${r.last_ping||'—'}</span>
      </td>
      <td class="px-4 py-3 text-center">${resetBtn}</td>
    </tr>`;
  }).join('');
}

function filterTable(val) { renderTable(allRows, val); }

// ── Big violation modal ────────────────────────────────────────
function showViolation(v) {
  // Reset countdown timer (ganti alert jika sudah terbuka)
  clearInterval(countdownTimer);

  document.getElementById('vm-nama').textContent    = v.mahasiswa_nama || '—';
  document.getElementById('vm-nim').textContent     = v.mahasiswa_nim  || '—';
  document.getElementById('vm-waktu').textContent   = `Terjadi pukul ${v.waktu}`;
  document.getElementById('vm-catatan').textContent = v.catatan || '';

  const tipeLabel = {
    tab_switch:        '🔀 Berpindah Tab / Halaman',
    window_blur:       '🖥️ Beralih ke Window Lain',
    copy_attempt:      '📋 Mencoba Menyalin Soal',
    keyboard_shortcut: '⌨️ Shortcut Terlarang',
    devtools:          '🔧 DevTools Dibuka',
    right_click:       '🖱️ Klik Kanan',
  }[v.tipe] || v.tipe;

  document.getElementById('vm-tipe').innerHTML = `<i class="fas fa-triangle-exclamation mr-2"></i>${tipeLabel}`;

  const modal = document.getElementById('viol-modal');
  modal.style.display = 'flex';

  // Shake animation
  const card = modal.querySelector('.vm-card');
  card.classList.remove('shake');
  void card.offsetWidth; // reflow
  card.classList.add('shake');

  // Highlight baris mahasiswa di tabel
  document.querySelectorAll('tr[id^="row-"]').forEach(el => el.style.background = '');
  const row = document.getElementById('row-' + v.mahasiswa_id);
  if (row) {
    row.style.background = 'rgba(248,113,113,.08)';
    row.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
  }

  // Auto-dismiss 10 detik dengan countdown
  let sec = 10;
  const cdEl = document.getElementById('vm-countdown');
  cdEl.textContent = `Menutup otomatis dalam ${sec}s`;

  countdownTimer = setInterval(() => {
    sec--;
    cdEl.textContent = sec > 0 ? `Menutup otomatis dalam ${sec}s` : '';
    if (sec <= 0) {
      clearInterval(countdownTimer);
      closeViolModal();
    }
  }, 1000);
}

function closeViolModal() {
  clearInterval(countdownTimer);
  document.getElementById('viol-modal').style.display = 'none';
  document.getElementById('vm-countdown').textContent = '';
  document.querySelectorAll('tr[id^="row-"]').forEach(el => el.style.background = '');
}

// ── Log ─────────────────────────────────────────────────────────
function addLogItem(v) {
  const log = document.getElementById('alert-log');
  document.getElementById('log-empty')?.remove();

  const tipeLabel = {
    tab_switch:        'Berpindah Tab',
    window_blur:       'Beralih Window',
    copy_attempt:      'Copy Soal',
    keyboard_shortcut: 'Shortcut Terlarang',
    devtools:          'DevTools Dibuka',
  }[v.tipe] || v.tipe;

  const item = document.createElement('div');
  item.style.cssText = 'display:flex;align-items:flex-start;gap:8px;padding:6px 0;border-bottom:1px solid var(--border)';
  item.innerHTML = `
    <i class="fas fa-triangle-exclamation flex-shrink-0" style="color:#f87171;font-size:11px;margin-top:2px"></i>
    <div style="flex:1;min-width:0">
      <span style="font-size:12px;font-weight:600;color:var(--text)">${esc(v.mahasiswa_nama||'')}</span>
      <span style="font-size:11px;color:#f87171;margin-left:6px">${tipeLabel}</span>
      <span style="font-size:10.5px;color:var(--muted);margin-left:6px">[${v.waktu}]</span>
    </div>`;
  log.insertBefore(item, log.firstChild);
}

function clearLog() {
  document.getElementById('alert-log').innerHTML =
    '<p id="log-empty" class="text-xs t-muted italic text-center mt-8">Belum ada pelanggaran...</p>';
}

function flashLogCard() {
  const card = document.getElementById('log-card');
  card.style.borderColor = 'rgba(248,113,113,.6)';
  card.style.boxShadow   = '0 0 20px rgba(248,113,113,.2)';
  setTimeout(() => { card.style.borderColor = ''; card.style.boxShadow = ''; }, 2500);
}

// ── Custom confirm dialog ────────────────────────────────────────
function customConfirm({ icon = '⚠️', title = 'Konfirmasi', body = '', okLabel = 'Ya, Lanjutkan', okStyle = '' } = {}) {
  return new Promise(resolve => {
    document.getElementById('cm-icon').textContent  = icon;
    document.getElementById('cm-title').textContent = title;
    document.getElementById('cm-body').innerHTML    = body;
    if (okLabel) document.getElementById('cm-ok').textContent = okLabel;
    if (okStyle) document.getElementById('cm-ok').style.background = okStyle;

    const modal = document.getElementById('confirm-modal');
    modal.style.display = 'flex';

    const ok     = document.getElementById('cm-ok');
    const cancel = document.getElementById('cm-cancel');

    function cleanup(result) {
      modal.style.display = 'none';
      ok.replaceWith(ok.cloneNode(true));     // hapus listener lama
      cancel.replaceWith(cancel.cloneNode(true));
      resolve(result);
    }

    document.getElementById('cm-ok').addEventListener('click', () => cleanup(true),  { once: true });
    document.getElementById('cm-cancel').addEventListener('click', () => cleanup(false), { once: true });
  });
}

// ── Reset sesi (ujian ulang) ────────────────────────────────────
async function resetSesi(mhsId, nama) {
  const ok = await customConfirm({
    icon: '🔄',
    title: 'Ujian Ulang',
    body: `Berikan kesempatan ujian ulang kepada:<br><strong style="color:#e2e8f0;font-size:15px">${esc(nama)}</strong><br><br><span style="color:#f87171;font-size:12px">Sesi dan jawaban sebelumnya akan dihapus permanen.</span>`,
    okLabel: 'Ya, Reset Sesi',
    okStyle: 'linear-gradient(135deg,#f59e0b,#ef4444)',
  });
  if (!ok) return;

  try {
    const res  = await fetch(resetBaseUrl + mhsId, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': CSRF,
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
    });
    const json = await res.json();
    if (res.ok && json.ok) {
      showToast(json.message || 'Sesi berhasil direset.', 'success');
      fetchData();
    } else {
      showToast(json.message || 'Gagal mereset sesi.', 'error');
    }
  } catch {
    showToast('Terjadi kesalahan jaringan.', 'error');
  }
}

// ── Toast ───────────────────────────────────────────────────────
function showToast(msg, type = 'success') {
  let t = document.getElementById('__toast');
  if (!t) {
    t = document.createElement('div');
    t.id = '__toast';
    t.style.cssText = 'position:fixed;top:20px;right:20px;z-index:99999;padding:12px 18px;border-radius:12px;font-size:13px;font-weight:600;display:flex;align-items:center;gap:8px;transition:all .3s;box-shadow:0 8px 24px rgba(0,0,0,.3)';
    document.body.appendChild(t);
  }
  const s = type === 'success'
    ? { bg:'rgba(16,185,129,.15)', border:'rgba(16,185,129,.3)', color:'#10b981', icon:'fas fa-circle-check' }
    : { bg:'rgba(248,113,113,.15)', border:'rgba(248,113,113,.3)', color:'#f87171', icon:'fas fa-circle-exclamation' };
  t.style.background = s.bg;
  t.style.border     = `1px solid ${s.border}`;
  t.style.color      = s.color;
  t.innerHTML        = `<i class="${s.icon}"></i>${msg}`;
  t.style.opacity    = '1';
  t.style.transform  = 'translateY(0)';
  clearTimeout(t._timer);
  t._timer = setTimeout(() => { t.style.opacity='0'; t.style.transform='translateY(-8px)'; }, 3500);
}

function esc(s) {
  return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}

// Start polling
fetchData();
setInterval(fetchData, 3000);
</script>
@endsection
