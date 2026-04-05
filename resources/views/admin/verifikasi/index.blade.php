@extends('layouts.admin')

@section('title', 'Verifikasi Email')

@section('content')
<div class="p-6 space-y-5">

  {{-- Header --}}
  <div class="flex items-center justify-between flex-wrap gap-3">
    <div>
      <h1 class="text-xl font-bold t-text font-display">Verifikasi Email</h1>
      <p class="text-xs t-muted mt-0.5">Monitor dan verifikasi akun pengguna secara manual</p>
    </div>
    <div class="flex items-center gap-2">
      {{-- Realtime indicator --}}
      <div class="flex items-center gap-1.5 text-xs t-muted border t-border rounded-lg px-3 py-1.5">
        <span id="rt-dot" class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse inline-block"></span>
        <span id="rt-label">Live</span>
      </div>
      {{-- Refresh interval --}}
      <select id="sel-interval" class="text-xs t-text t-surf2 border t-border rounded-lg px-2 py-1.5 outline-none cursor-pointer">
        <option value="5000">Refresh 5d</option>
        <option value="10000">Refresh 10d</option>
        <option value="30000">Refresh 30d</option>
        <option value="0">Pause</option>
      </select>
    </div>
  </div>

  {{-- Stats strip --}}
  <div class="grid grid-cols-2 sm:grid-cols-4 gap-3" id="stats-strip">
    <div class="t-surf border t-border rounded-xl p-4">
      <div class="text-xs t-muted mb-1">Total Pengguna</div>
      <div class="text-2xl font-bold t-text" id="stat-total">—</div>
    </div>
    <div class="t-surf border t-border rounded-xl p-4">
      <div class="text-xs t-muted mb-1">Belum Verifikasi</div>
      <div class="text-2xl font-bold" style="color:#f59e0b" id="stat-unverified">—</div>
    </div>
    <div class="t-surf border t-border rounded-xl p-4">
      <div class="text-xs t-muted mb-1">Sudah Verifikasi</div>
      <div class="text-2xl font-bold" style="color:#10b981" id="stat-verified">—</div>
    </div>
    <div class="t-surf border t-border rounded-xl p-4">
      <div class="text-xs t-muted mb-1">PIN Kedaluwarsa</div>
      <div class="text-2xl font-bold" style="color:#f87171" id="stat-expired">—</div>
    </div>
  </div>

  {{-- Filter + Search --}}
  <div class="t-surf border t-border rounded-xl p-4 flex flex-wrap items-center gap-3">
    <div class="flex rounded-lg overflow-hidden border t-border">
      <button onclick="setFilter('unverified')" id="btn-unverified"
        class="filter-btn px-4 py-1.5 text-xs font-semibold transition-all active-filter">
        <i class="fas fa-clock mr-1.5"></i>Belum Terverifikasi
      </button>
      <button onclick="setFilter('verified')" id="btn-verified"
        class="filter-btn px-4 py-1.5 text-xs font-semibold transition-all inactive-filter">
        <i class="fas fa-circle-check mr-1.5"></i>Sudah Terverifikasi
      </button>
      <button onclick="setFilter('all')" id="btn-all"
        class="filter-btn px-4 py-1.5 text-xs font-semibold transition-all inactive-filter">
        <i class="fas fa-list mr-1.5"></i>Semua
      </button>
    </div>
    <div class="flex-1 min-w-[200px]">
      <div class="relative">
        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 t-muted text-xs"></i>
        <input id="search-input" type="text" placeholder="Cari nama atau email…"
          class="w-full t-surf2 border t-border t-text rounded-lg pl-8 pr-3 py-1.5 text-xs outline-none focus:border-[var(--ac)]"
          oninput="onSearch(this.value)">
      </div>
    </div>
    <div class="text-xs t-muted" id="result-info">—</div>
  </div>

  {{-- Table --}}
  <div class="t-surf border t-border rounded-xl overflow-hidden">
    <table class="w-full text-sm">
      <thead>
        <tr class="t-surf2 border-b t-border">
          <th class="px-4 py-3 text-left text-xs font-semibold t-muted uppercase tracking-wider">Pengguna</th>
          <th class="px-4 py-3 text-left text-xs font-semibold t-muted uppercase tracking-wider hidden sm:table-cell">NIM</th>
          <th class="px-4 py-3 text-left text-xs font-semibold t-muted uppercase tracking-wider hidden md:table-cell">Daftar</th>
          <th class="px-4 py-3 text-center text-xs font-semibold t-muted uppercase tracking-wider">PIN / Status</th>
          <th class="px-4 py-3 text-right text-xs font-semibold t-muted uppercase tracking-wider">Aksi</th>
        </tr>
      </thead>
      <tbody id="tbl-body">
        <tr>
          <td colspan="5" class="px-4 py-12 text-center t-muted text-sm">
            <i class="fas fa-spinner fa-spin text-xl mb-2 block"></i>Memuat data…
          </td>
        </tr>
      </tbody>
    </table>
  </div>

  {{-- Pagination --}}
  <div id="pagination" class="flex items-center justify-between flex-wrap gap-3" style="display:none!important"></div>

</div>

{{-- Toast --}}
<div id="toast"
  class="fixed top-5 right-5 z-50 px-4 py-3 rounded-xl text-sm font-medium shadow-lg
         flex items-center gap-3 transition-all duration-300 -translate-y-4 opacity-0 pointer-events-none"
  style="min-width:260px">
  <i id="toast-icon" class="text-base"></i>
  <span id="toast-msg"></span>
</div>

<style>
.active-filter{background:var(--ac);color:#fff}
.inactive-filter{background:transparent;color:var(--sub)}
.inactive-filter:hover{background:var(--ac-lt);color:var(--ac)}
.pin-badge{
  display:inline-flex;align-items:center;gap:6px;
  background:var(--surface2);border:1px solid var(--border);
  border-radius:8px;padding:4px 10px;
  font-family:'Courier New',monospace;font-size:15px;font-weight:700;
  letter-spacing:3px;color:#10b981;
}
.pin-badge.expired{color:#f87171;border-color:rgba(248,113,113,.3)}
.btn-action{
  display:inline-flex;align-items:center;gap:5px;
  padding:5px 12px;border-radius:8px;font-size:11.5px;font-weight:600;
  border:none;cursor:pointer;transition:all .15s;font-family:inherit;
}
.btn-verify{background:linear-gradient(135deg,#10b981,#06b6d4);color:#fff}
.btn-verify:hover{opacity:.85;transform:translateY(-1px)}
.btn-resend{background:var(--surface2);border:1px solid var(--border);color:var(--sub)}
.btn-resend:hover{border-color:var(--ac);color:var(--ac)}
.btn-action:disabled{opacity:.5;cursor:not-allowed;transform:none!important}
</style>

<script>
const CSRF   = document.querySelector('meta[name="csrf-token"]').content;
const apiUrl = '{{ route('admin.verifikasi.data') }}';

let currentFilter = 'unverified';
let currentPage   = 1;
let searchVal     = '';
let searchTimer   = null;
let pollTimer     = null;
let pollInterval  = 5000;

/* ── Init ── */
fetchStats();
fetchData();
startPoll();

/* ── Polling ── */
function startPoll() {
  clearInterval(pollTimer);
  if (pollInterval > 0) {
    pollTimer = setInterval(() => { fetchStats(); fetchData(false); }, pollInterval);
  }
}

document.getElementById('sel-interval').addEventListener('change', function () {
  pollInterval = parseInt(this.value);
  const dot   = document.getElementById('rt-dot');
  const label = document.getElementById('rt-label');
  if (pollInterval === 0) {
    dot.classList.remove('animate-pulse', 'bg-emerald-500');
    dot.classList.add('bg-gray-500');
    label.textContent = 'Paused';
  } else {
    dot.classList.add('animate-pulse', 'bg-emerald-500');
    dot.classList.remove('bg-gray-500');
    label.textContent = 'Live';
  }
  startPoll();
});

/* ── Fetch stats (all filters combined) ── */
async function fetchStats() {
  try {
    const [resAll, resUn, resVer] = await Promise.all([
      fetch(apiUrl + '?filter=all&per_page=1'),
      fetch(apiUrl + '?filter=unverified&per_page=1'),
      fetch(apiUrl + '?filter=verified&per_page=1'),
    ]);
    const [all, un, ver] = await Promise.all([resAll.json(), resUn.json(), resVer.json()]);
    document.getElementById('stat-total').textContent     = all.total ?? '—';
    document.getElementById('stat-unverified').textContent = un.total ?? '—';
    document.getElementById('stat-verified').textContent   = ver.total ?? '—';
    // Count expired pins from unverified list
    const expired = (un.data ?? []).filter(u => u.pin_expired && u.pin).length;
    document.getElementById('stat-expired').textContent = expired;
  } catch {}
}

/* ── Fetch table data ── */
async function fetchData(showLoader = true) {
  const tbody = document.getElementById('tbl-body');
  if (showLoader) {
    tbody.innerHTML = `<tr><td colspan="5" class="px-4 py-12 text-center t-muted text-sm">
      <i class="fas fa-spinner fa-spin text-xl mb-2 block"></i>Memuat data…</td></tr>`;
  }

  const params = new URLSearchParams({
    filter: currentFilter, page: currentPage, search: searchVal
  });

  try {
    const res  = await fetch(apiUrl + '?' + params);
    const json = await res.json();
    renderTable(json.data ?? []);
    renderPagination(json.current_page, json.last_page, json.total);
    document.getElementById('result-info').textContent =
      json.total + ' pengguna ditemukan';
  } catch {
    tbody.innerHTML = `<tr><td colspan="5" class="px-4 py-10 text-center" style="color:#f87171">
      Gagal memuat data.</td></tr>`;
  }
}

/* ── Render table rows ── */
function renderTable(rows) {
  const tbody = document.getElementById('tbl-body');
  if (!rows.length) {
    tbody.innerHTML = `<tr><td colspan="5" class="px-4 py-12 text-center t-muted text-sm">
      <i class="fas fa-inbox text-2xl mb-2 block opacity-40"></i>Tidak ada data</td></tr>`;
    return;
  }

  tbody.innerHTML = rows.map(u => {
    const pinCell = u.verified
      ? `<span class="inline-flex items-center gap-1.5 text-xs px-2 py-1 rounded-full"
           style="background:rgba(16,185,129,.12);color:#10b981">
           <i class="fas fa-circle-check text-[10px]"></i>Terverifikasi
           <span class="t-muted font-normal">${u.verified_at}</span>
         </span>`
      : u.pin
        ? `<div>
             <div class="pin-badge ${u.pin_expired ? 'expired' : ''}">${u.pin}</div>
             <div class="text-[10.5px] mt-1 ${u.pin_expired ? '' : 't-muted'}">
               ${u.pin_expired
                 ? '<span style="color:#f87171"><i class="fas fa-triangle-exclamation mr-1"></i>PIN kedaluwarsa</span>'
                 : '<i class="fas fa-clock mr-1"></i>Kedaluwarsa ' + u.pin_expires}
             </div>
           </div>`
        : `<span class="text-xs t-muted italic">Belum ada PIN</span>`;

    const actionCell = u.verified
      ? `<span class="text-xs t-muted italic">—</span>`
      : `<div class="flex items-center justify-end gap-2">
           <button class="btn-action btn-resend" onclick="doResend(${u.id}, this)"
             title="Kirim ulang PIN">
             <i class="fas fa-paper-plane"></i> Kirim PIN
           </button>
           <button class="btn-action btn-verify" onclick="doVerify(${u.id}, this)"
             title="Verifikasi manual oleh admin">
             <i class="fas fa-shield-check"></i> Verifikasi
           </button>
         </div>`;

    return `<tr class="border-b t-border hover:bg-white/[.02] transition-colors">
      <td class="px-4 py-3">
        <div class="flex items-center gap-3">
          <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0 text-white"
            style="background:linear-gradient(135deg,var(--ac),var(--ac2))">
            ${initials(u.name)}
          </div>
          <div>
            <div class="text-sm font-medium t-text">${esc(u.name)}</div>
            <div class="text-xs t-muted">${esc(u.email)}</div>
          </div>
        </div>
      </td>
      <td class="px-4 py-3 text-xs t-muted hidden sm:table-cell">${u.nim ?? '—'}</td>
      <td class="px-4 py-3 text-xs t-muted hidden md:table-cell">${u.registered}</td>
      <td class="px-4 py-3 text-center">${pinCell}</td>
      <td class="px-4 py-3 text-right">${actionCell}</td>
    </tr>`;
  }).join('');
}

/* ── Pagination ── */
function renderPagination(current, last, total) {
  const el = document.getElementById('pagination');
  if (last <= 1) { el.style.setProperty('display', 'none', 'important'); return; }
  el.style.removeProperty('display');

  let btns = '';
  for (let i = 1; i <= last; i++) {
    btns += `<button onclick="goPage(${i})"
      class="w-8 h-8 rounded-lg text-xs font-semibold transition-all
             ${i === current ? 'btn-verify' : 'btn-resend btn-action'}">
      ${i}</button>`;
  }

  el.innerHTML = `
    <div class="text-xs t-muted">Halaman ${current} dari ${last} (${total} data)</div>
    <div class="flex gap-1.5">${btns}</div>`;
}

function goPage(p) { currentPage = p; fetchData(); }

/* ── Actions ── */
async function doVerify(id, btn) {
  if (!confirm('Verifikasi akun ini sekarang?')) return;
  btn.disabled = true;
  btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
  try {
    const res = await fetch(`/admin/verifikasi/${id}/verify`, {
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json' },
    });
    const json = await res.json();
    if (res.ok) {
      toast(json.message, 'success');
      fetchStats();
      fetchData(false);
    } else {
      toast(json.message, 'error');
      btn.disabled = false;
      btn.innerHTML = '<i class="fas fa-shield-check"></i> Verifikasi';
    }
  } catch {
    toast('Terjadi kesalahan.', 'error');
    btn.disabled = false;
  }
}

async function doResend(id, btn) {
  btn.disabled = true;
  btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
  try {
    const res  = await fetch(`/admin/verifikasi/${id}/resend`, {
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': CSRF },
    });
    const json = await res.json();
    toast(json.message, res.ok ? 'success' : 'error');
    if (res.ok) setTimeout(() => { fetchData(false); }, 1500);
  } catch {
    toast('Terjadi kesalahan.', 'error');
  } finally {
    btn.disabled = false;
    btn.innerHTML = '<i class="fas fa-paper-plane"></i> Kirim PIN';
  }
}

/* ── Filter / search ── */
function setFilter(f) {
  currentFilter = f;
  currentPage   = 1;
  ['unverified','verified','all'].forEach(k => {
    document.getElementById('btn-' + k).className =
      'filter-btn px-4 py-1.5 text-xs font-semibold transition-all ' +
      (k === f ? 'active-filter' : 'inactive-filter');
  });
  fetchStats();
  fetchData();
}

function onSearch(val) {
  clearTimeout(searchTimer);
  searchTimer = setTimeout(() => {
    searchVal   = val;
    currentPage = 1;
    fetchData();
  }, 350);
}

/* ── Helpers ── */
function initials(name) {
  const w = name.trim().split(' ');
  return w.length >= 2
    ? (w[0][0] + w[1][0]).toUpperCase()
    : name.slice(0, 2).toUpperCase();
}

function esc(str) {
  return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}

function toast(msg, type = 'success') {
  const el   = document.getElementById('toast');
  const icon = document.getElementById('toast-icon');
  const txt  = document.getElementById('toast-msg');

  const styles = {
    success: { bg: 'rgba(16,185,129,.15)', border: 'rgba(16,185,129,.3)', color: '#10b981', icon: 'fas fa-circle-check' },
    error:   { bg: 'rgba(248,113,113,.15)', border: 'rgba(248,113,113,.3)', color: '#f87171', icon: 'fas fa-circle-exclamation' },
  };
  const s = styles[type] || styles.success;

  el.style.background   = s.bg;
  el.style.border       = `1px solid ${s.border}`;
  el.style.color        = s.color;
  icon.className        = s.icon;
  txt.textContent       = msg;

  el.classList.remove('-translate-y-4', 'opacity-0', 'pointer-events-none');
  el.classList.add('translate-y-0', 'opacity-100');

  setTimeout(() => {
    el.classList.add('-translate-y-4', 'opacity-0', 'pointer-events-none');
    el.classList.remove('translate-y-0', 'opacity-100');
  }, 3500);
}
</script>
@endsection
