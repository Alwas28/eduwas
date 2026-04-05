@extends('layouts.instruktur')
@section('title', 'Peserta — ' . ($kelas->mataKuliah?->nama ?? $kelas->kode_display))
@section('page-title', 'Peserta Kelas')

@push('styles')
<style>
/* ── Layout ── */
.peserta-layout { display:grid; grid-template-columns:320px 1fr; gap:20px; align-items:start; }
@media(max-width:900px){ .peserta-layout { grid-template-columns:1fr; } }

/* ── Card ── */
.p-card { background:var(--surface); border:1px solid var(--border); border-radius:18px; overflow:hidden; }
.p-card-head { padding:14px 18px; border-bottom:1px solid var(--border); display:flex; align-items:center; justify-content:space-between; gap:8px; flex-wrap:wrap; }
.p-card-body { padding:18px; }

/* ── QR container ── */
#qr-canvas { display:block; margin:0 auto; border-radius:12px; }

/* ── Search input ── */
.srch-input { width:100%; padding:9px 12px 9px 36px; border-radius:10px; border:1px solid var(--border); background:var(--surface2); color:var(--text); font-size:13px; outline:none; transition:border-color .15s; }
.srch-input:focus { border-color:var(--ac); }
.srch-wrap { position:relative; }
.srch-wrap i { position:absolute; left:11px; top:50%; transform:translateY(-50%); color:var(--muted); font-size:12px; pointer-events:none; }

/* ── Result dropdown ── */
#search-results { position:absolute; top:calc(100% + 4px); left:0; right:0; background:var(--surface); border:1px solid var(--border); border-radius:12px; z-index:50; box-shadow:0 8px 24px rgba(0,0,0,.18); overflow:hidden; }
.result-item { display:flex; align-items:center; gap:10px; padding:9px 12px; cursor:pointer; transition:background .12s; }
.result-item:hover { background:var(--surface2); }

/* ── Peserta table ── */
.pt-table { width:100%; border-collapse:collapse; }
.pt-table th { padding:9px 12px; text-align:left; font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:var(--muted); border-bottom:1px solid var(--border); }
.pt-table td { padding:10px 12px; border-bottom:1px solid var(--border); font-size:13px; color:var(--text); vertical-align:middle; }
.pt-table tr:last-child td { border-bottom:none; }
.pt-table tr:hover td { background:var(--surface2); }

/* ── Avatar ── */
.av { width:32px; height:32px; border-radius:9px; display:grid; place-items:center; font-size:11px; font-weight:700; flex-shrink:0; }

/* ── Buttons ── */
.btn-primary { padding:8px 18px; border-radius:10px; font-size:13px; font-weight:600; color:#fff; border:none; cursor:pointer; background:var(--ac); transition:opacity .15s; }
.btn-primary:hover { opacity:.85; }
.btn-primary:disabled { opacity:.5; cursor:not-allowed; }
.btn-danger-sm { width:28px; height:28px; border-radius:8px; border:none; background:rgba(239,68,68,.1); color:#f87171; cursor:pointer; display:grid; place-items:center; transition:opacity .15s; }
.btn-danger-sm:hover { opacity:.75; }

/* ── Badge ── */
.badge { display:inline-flex; align-items:center; gap:4px; padding:2px 8px; border-radius:20px; font-size:10px; font-weight:700; }
.b-aktif { background:rgba(16,185,129,.12); color:#34d399; }

/* ── Toast ── */
#toast { position:fixed; bottom:24px; left:50%; transform:translateX(-50%) translateY(80px); background:var(--surface); border:1px solid var(--border); border-radius:14px; padding:10px 20px; font-size:13px; font-weight:600; color:var(--text); box-shadow:0 8px 24px rgba(0,0,0,.25); transition:transform .3s,opacity .3s; opacity:0; z-index:300; white-space:nowrap; }
#toast.show { transform:translateX(-50%) translateY(0); opacity:1; }

/* ── Scan modal ── */
#scan-video { width:100%; border-radius:12px; aspect-ratio:1; object-fit:cover; background:#000; }
</style>
@endpush

@section('content')
@php
$mk     = $kelas->mataKuliah;
$pa     = $kelas->periodeAkademik;
$csrf   = csrf_token();
@endphp

<div class="space-y-5 animate-fadeUp">

  {{-- Breadcrumb --}}
  <div class="flex items-center gap-2 text-[12px]" style="color:var(--muted)">
    <a href="{{ route('instruktur.kelas.index') }}" class="a-text hover:underline">Kelas</a>
    <i class="fa-solid fa-chevron-right text-[10px]"></i>
    <span style="color:var(--text)">{{ $mk?->nama ?? $kelas->kode_display }}</span>
    <i class="fa-solid fa-chevron-right text-[10px]"></i>
    <span style="color:var(--text)">Peserta</span>
  </div>

  <div class="peserta-layout">

    {{-- ── Sidebar ── --}}
    <div class="space-y-4">

      {{-- Info kelas --}}
      <div class="p-card">
        <div class="h-1 a-grad"></div>
        <div class="p-card-body space-y-3">
          <div>
            <div class="font-mono font-bold text-[11px] a-text mb-1">{{ $kelas->kode_display }}</div>
            <div class="font-display font-bold text-[16px]" style="color:var(--text)">{{ $mk?->nama ?? '—' }}</div>
            @if($pa)
              <div class="text-[12px] mt-0.5" style="color:var(--muted)">{{ $pa->nama }}</div>
            @endif
          </div>
          <div class="flex items-center gap-2 text-[12px]" style="color:var(--muted)">
            <i class="fa-solid fa-users"></i>
            <span id="peserta-count">{{ $enrollments->count() }}</span> peserta
            @if($kelas->kapasitas)
              <span>/ {{ $kelas->kapasitas }}</span>
            @endif
          </div>
        </div>
      </div>

      {{-- QR Code Enroll --}}
      <div class="p-card">
        <div class="p-card-head">
          <span class="font-display font-semibold text-[13px]" style="color:var(--text)">
            <i class="fa-solid fa-qrcode mr-1.5 a-text"></i>QR Code Enroll
          </span>
          <button onclick="downloadQr()" class="text-[11px] font-semibold a-text hover:underline">
            <i class="fa-solid fa-download mr-1"></i>Unduh
          </button>
        </div>
        <div class="p-card-body text-center space-y-3">
          <div id="qr-svg-wrap" style="display:inline-block;padding:12px;background:#fff;border-radius:12px;line-height:0">
            {!! $qrSvg !!}
          </div>
          <p class="text-[11px]" style="color:var(--muted)">
            Mahasiswa scan QR ini untuk bergabung ke kelas
          </p>
          <div class="text-[10px] font-mono px-3 py-2 rounded-lg break-all" style="background:var(--surface2);color:var(--muted)">
            {{ $joinUrl }}
          </div>
        </div>
      </div>

      {{-- Tambah manual --}}
      <div class="p-card">
        <div class="p-card-head">
          <span class="font-display font-semibold text-[13px]" style="color:var(--text)">
            <i class="fa-solid fa-user-plus mr-1.5 a-text"></i>Tambah Mahasiswa
          </span>
        </div>
        <div class="p-card-body space-y-3">
          <div class="srch-wrap" style="position:relative">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" id="search-input" class="srch-input"
                   placeholder="Ketik NIM atau nama…"
                   autocomplete="off"
                   oninput="onSearchInput(this.value)">
            <div id="search-results" style="display:none"></div>
          </div>
          {{-- Selected mahasiswa preview --}}
          <div id="selected-preview" style="display:none;padding:8px 12px;border-radius:10px;border:1px solid var(--ac);background:var(--surface2);display:none">
            <div class="flex items-center gap-2">
              <div class="av a-bg-lt a-text" style="width:28px;height:28px;border-radius:7px;font-size:9px" id="sel-initial">?</div>
              <div style="flex:1;min-width:0">
                <div style="font-weight:600;font-size:12px;color:var(--text)" id="sel-nama">—</div>
                <div style="font-size:10px;color:var(--muted)" id="sel-nim">—</div>
              </div>
              <button onclick="clearSelected()" style="background:none;border:none;color:var(--muted);cursor:pointer;padding:2px 4px;font-size:12px" title="Batalkan pilihan">
                <i class="fa-solid fa-xmark"></i>
              </button>
            </div>
          </div>
          <button onclick="doEnrollSelected()" class="btn-primary w-full" id="btn-enroll" disabled>
            <i class="fa-solid fa-plus mr-1.5"></i>Tambah ke Kelas
          </button>
        </div>
      </div>

    </div>

    {{-- ── Daftar Peserta ── --}}
    <div class="p-card">
      <div class="p-card-head">
        <span class="font-display font-semibold text-[14px]" style="color:var(--text)">
          <i class="fa-solid fa-users mr-1.5 a-text"></i>Daftar Peserta
        </span>
        <span class="text-[12px]" style="color:var(--muted)">
          <span id="peserta-count-2">{{ $enrollments->count() }}</span> mahasiswa terdaftar
        </span>
      </div>

      {{-- Filter search in table --}}
      <div style="padding:12px 18px;border-bottom:1px solid var(--border)">
        <div class="srch-wrap">
          <i class="fa-solid fa-magnifying-glass"></i>
          <input type="text" class="srch-input" placeholder="Filter peserta…" oninput="filterTable(this.value)">
        </div>
      </div>

      <div style="overflow-x:auto">
        <table class="pt-table" id="peserta-table">
          <thead>
            <tr>
              <th>#</th>
              <th>Mahasiswa</th>
              <th>NIM</th>
              <th>Status</th>
              <th>Bergabung</th>
              <th></th>
            </tr>
          </thead>
          <tbody id="peserta-tbody">
            @forelse($enrollments as $i => $enr)
            <tr id="enr-row-{{ $enr->id }}" data-nama="{{ strtolower($enr->mahasiswa?->nama ?? '') }}" data-nim="{{ $enr->mahasiswa?->nim ?? '' }}">
              <td style="color:var(--muted);font-size:11px">{{ $i + 1 }}</td>
              <td>
                <div class="flex items-center gap-2">
                  <div class="av a-bg-lt a-text">{{ strtoupper(substr($enr->mahasiswa?->nama ?? '?', 0, 1)) }}</div>
                  <span class="font-semibold text-[13px]">{{ $enr->mahasiswa?->nama ?? '—' }}</span>
                </div>
              </td>
              <td class="font-mono text-[12px]" style="color:var(--muted)">{{ $enr->mahasiswa?->nim ?? '—' }}</td>
              <td><span class="badge b-aktif"><i class="fa-solid fa-circle text-[7px]"></i>{{ $enr->status }}</span></td>
              <td class="text-[12px]" style="color:var(--muted)">{{ $enr->enrolled_at?->format('d M Y') ?? '—' }}</td>
              <td>
                <button onclick="doUnenroll({{ $enr->id }}, '{{ addslashes($enr->mahasiswa?->nama ?? '') }}')"
                        class="btn-danger-sm" title="Keluarkan">
                  <i class="fa-solid fa-user-minus text-[10px]"></i>
                </button>
              </td>
            </tr>
            @empty
            <tr id="empty-row">
              <td colspan="6" class="text-center py-12" style="color:var(--muted)">
                <i class="fa-solid fa-user-slash text-[24px] opacity-20 block mb-3"></i>
                <div class="text-[13px] font-semibold" style="color:var(--text)">Belum ada peserta</div>
                <p class="text-[12px] mt-1">Tambahkan mahasiswa atau bagikan QR code di atas.</p>
              </td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

  </div>
</div>

<div id="toast"></div>
@endsection

@push('scripts')
<script>
const CSRF            = '{{ $csrf }}';
const JOIN_URL        = @js($joinUrl);
const ROUTE_SEARCH    = '{{ route("instruktur.kelas.peserta.search", $kelas->id) }}';
const ROUTE_ENROLL_ID = '{{ route("instruktur.kelas.peserta.enroll-by-id", $kelas->id) }}';
const ROUTE_UNENROLL  = (id) => `{{ url("instruktur/kelas/{$kelas->id}/peserta") }}/${id}`;

function downloadQr() {
  const wrap = document.getElementById('qr-svg-wrap');
  const svgEl = wrap?.querySelector('svg');
  if (!svgEl) return;

  // Serialize SVG → blob URL → draw on canvas → download PNG
  const svgData = new XMLSerializer().serializeToString(svgEl);
  const blob = new Blob([svgData], { type: 'image/svg+xml;charset=utf-8' });
  const url  = URL.createObjectURL(blob);
  const img  = new Image();
  img.onload = () => {
    const c = document.createElement('canvas');
    c.width = c.height = 240;
    const ctx = c.getContext('2d');
    ctx.fillStyle = '#ffffff';
    ctx.fillRect(0, 0, 240, 240);
    ctx.drawImage(img, 0, 0, 240, 240);
    URL.revokeObjectURL(url);
    const a = document.createElement('a');
    a.download = 'qr-enroll-{{ $kelas->kode_display }}.png';
    a.href = c.toDataURL('image/png');
    a.click();
  };
  img.src = url;
}

/* ── Toast ── */
let _tt;
function showToast(msg, err = false) {
  const el = document.getElementById('toast');
  el.textContent = msg;
  el.style.borderColor = err ? 'rgba(239,68,68,.4)' : 'var(--border)';
  el.style.color = err ? '#fca5a5' : 'var(--text)';
  el.classList.add('show');
  clearTimeout(_tt);
  _tt = setTimeout(() => el.classList.remove('show'), 3200);
}

/* ── Search (live dropdown, TIDAK auto-enroll) ── */
let _searchTimer;
let _selectedMhs = null; // { id, nama, nim }

function onSearchInput(val) {
  clearTimeout(_searchTimer);
  hideDropdown();
  if (!val.trim()) return;
  _searchTimer = setTimeout(() => liveSearch(val.trim()), 350);
}

async function liveSearch(q) {
  try {
    const r = await fetch(`${ROUTE_SEARCH}?q=${encodeURIComponent(q)}`, {
      headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
    });
    const results = await r.json();
    if (!Array.isArray(results) || results.length === 0) {
      showDropdownMsg('Mahasiswa tidak ditemukan atau sudah terdaftar.');
      return;
    }
    showDropdown(results);
  } catch { hideDropdown(); }
}

function showDropdown(results) {
  const box = document.getElementById('search-results');
  box.innerHTML = '';
  results.forEach(m => {
    const div = document.createElement('div');
    div.className = 'result-item';
    div.innerHTML = `
      <div class="av a-bg-lt a-text" style="width:28px;height:28px;border-radius:7px;font-size:9px">${(m.nama||'?').charAt(0).toUpperCase()}</div>
      <div style="flex:1;min-width:0">
        <div style="font-weight:600;font-size:12px;color:var(--text)">${escH(m.nama)}</div>
        <div style="font-size:10px;color:var(--muted)">${escH(m.nim)}</div>
      </div>`;
    div.onclick = () => selectMahasiswa(m);
    box.appendChild(div);
  });
  box.style.display = '';
}

function showDropdownMsg(msg) {
  const box = document.getElementById('search-results');
  box.innerHTML = `<div style="padding:10px 14px;font-size:12px;color:var(--muted)">${escH(msg)}</div>`;
  box.style.display = '';
}

function hideDropdown() {
  document.getElementById('search-results').style.display = 'none';
}

function selectMahasiswa(m) {
  _selectedMhs = m;
  hideDropdown();
  document.getElementById('search-input').value = '';

  document.getElementById('sel-initial').textContent = (m.nama || '?').charAt(0).toUpperCase();
  document.getElementById('sel-nama').textContent = m.nama;
  document.getElementById('sel-nim').textContent  = m.nim;
  document.getElementById('selected-preview').style.display = '';
  document.getElementById('btn-enroll').disabled = false;
}

function clearSelected() {
  _selectedMhs = null;
  document.getElementById('selected-preview').style.display = 'none';
  document.getElementById('btn-enroll').disabled = true;
  document.getElementById('search-input').focus();
}

async function doEnrollSelected() {
  if (!_selectedMhs) return;
  const btn = document.getElementById('btn-enroll');
  btn.disabled = true;
  const fd = new FormData();
  fd.append('mahasiswa_id', _selectedMhs.id);
  try {
    const r = await fetch(ROUTE_ENROLL_ID, {
      method: 'POST',
      headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
      body: fd,
    });
    const j = await r.json();
    if (r.ok && j.enrollment) {
      appendRow(j.enrollment);
      showToast(j.message);
      clearSelected();
    } else {
      showToast(j.message || 'Gagal mendaftarkan.', true);
      btn.disabled = false;
    }
  } catch {
    showToast('Koneksi gagal.', true);
    btn.disabled = false;
  }
}

/* ── Append new row ── */
let _rowCounter = document.querySelectorAll('#peserta-tbody tr[id^="enr-row-"]').length;

function appendRow(enr) {
  document.getElementById('empty-row')?.remove();
  _rowCounter++;
  const tbody = document.getElementById('peserta-tbody');
  const tr = document.createElement('tr');
  tr.id = 'enr-row-' + enr.id;
  tr.setAttribute('data-nama', (enr.nama || '').toLowerCase());
  tr.setAttribute('data-nim', enr.nim || '');
  tr.innerHTML = `
    <td style="color:var(--muted);font-size:11px">${_rowCounter}</td>
    <td>
      <div class="flex items-center gap-2">
        <div class="av a-bg-lt a-text">${(enr.nama||'?').charAt(0).toUpperCase()}</div>
        <span class="font-semibold text-[13px]">${escH(enr.nama)}</span>
      </div>
    </td>
    <td class="font-mono text-[12px]" style="color:var(--muted)">${escH(enr.nim)}</td>
    <td><span class="badge b-aktif"><i class="fa-solid fa-circle text-[7px]"></i>${escH(enr.status)}</span></td>
    <td class="text-[12px]" style="color:var(--muted)">Baru saja</td>
    <td>
      <button onclick="doUnenroll(${enr.id}, '${escH(enr.nama)}')" class="btn-danger-sm" title="Keluarkan">
        <i class="fa-solid fa-user-minus text-[10px]"></i>
      </button>
    </td>`;
  tbody.appendChild(tr);
  updateCount(1);
}

/* ── Unenroll ── */
async function doUnenroll(enrollmentId, nama) {
  if (!confirm(`Keluarkan ${nama} dari kelas ini?`)) return;
  try {
    const fd = new FormData(); fd.append('_method', 'DELETE');
    const r = await fetch(ROUTE_UNENROLL(enrollmentId), {
      method: 'POST',
      headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
      body: fd,
    });
    const j = await r.json();
    if (r.ok) {
      document.getElementById('enr-row-' + enrollmentId)?.remove();
      updateCount(-1);
      showToast(j.message);
      if (!document.querySelectorAll('#peserta-tbody tr[id^="enr-row-"]').length) {
        document.getElementById('peserta-tbody').innerHTML = `
          <tr id="empty-row">
            <td colspan="6" class="text-center py-12" style="color:var(--muted)">
              <i class="fa-solid fa-user-slash text-[24px] opacity-20 block mb-3"></i>
              <div class="text-[13px] font-semibold" style="color:var(--text)">Belum ada peserta</div>
            </td>
          </tr>`;
      }
    } else {
      showToast(j.message || 'Gagal mengeluarkan.', true);
    }
  } catch { showToast('Koneksi gagal.', true); }
}

/* ── Filter table ── */
function filterTable(q) {
  const term = q.toLowerCase();
  document.querySelectorAll('#peserta-tbody tr[id^="enr-row-"]').forEach(tr => {
    const match = tr.dataset.nama.includes(term) || tr.dataset.nim.includes(term);
    tr.style.display = match ? '' : 'none';
  });
}

/* ── Count update ── */
function updateCount(delta) {
  const cnt1 = document.getElementById('peserta-count');
  const cnt2 = document.getElementById('peserta-count-2');
  const cur = parseInt(cnt1?.textContent || '0') + delta;
  if (cnt1) cnt1.textContent = cur;
  if (cnt2) cnt2.textContent = cur;
}

/* ── Close dropdown on outside click ── */
document.addEventListener('click', e => {
  if (!e.target.closest('.srch-wrap')) {
    document.getElementById('search-results').style.display = 'none';
  }
});

function escH(s) {
  return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
</script>
@endpush
