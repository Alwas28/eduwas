@extends('layouts.mahasiswa')
@section('title', $kelompok->nama_kelompok . ' — ' . $kelompok->tugas->judul)
@section('page-title', 'Kelola Kelompok')

@push('styles')
<style>
/* ── Page card ── */
.page-card {
  background:var(--surface); border:1px solid var(--border); border-radius:18px; overflow:hidden;
}
.card-head {
  padding:14px 20px; border-bottom:1px solid var(--border);
  display:flex; align-items:center; justify-content:space-between; gap:10px; flex-wrap:wrap;
}
.card-body { padding:18px 20px; }

/* ── Anggota table ── */
.anggota-table { width:100%; border-collapse:collapse; font-size:13px; }
.anggota-table th {
  padding:9px 14px; text-align:left; font-size:10px; font-weight:700;
  text-transform:uppercase; letter-spacing:.05em; color:var(--muted);
  background:var(--surface2); border-bottom:1px solid var(--border);
}
.anggota-table td { padding:11px 14px; border-bottom:1px solid var(--border); color:var(--text); }
.anggota-table tr:last-child td { border-bottom:none; }
.anggota-table tbody tr:hover td { background:var(--surface2); }

/* ── Avatar ── */
.av { width:30px; height:30px; border-radius:9px; display:grid; place-items:center; font-size:11px; font-weight:700; flex-shrink:0; }

/* ── Status badge ── */
.badge {
  display:inline-flex; align-items:center; gap:4px;
  padding:3px 8px; border-radius:20px; font-size:10px; font-weight:700;
}
.b-submitted { background:rgba(16,185,129,.12); color:#34d399; }
.b-belum     { background:rgba(100,116,139,.12); color:#94a3b8; }
.b-aktif     { background:rgba(16,185,129,.12); color:#34d399; }
.b-draft     { background:rgba(100,116,139,.12); color:#94a3b8; }
.b-selesai   { background:rgba(59,130,246,.12); color:#60a5fa; }

/* ── Topik input ── */
.topik-form { display:flex; align-items:center; gap:6px; min-width:0; }
.topik-inp {
  flex:1; min-width:0; padding:5px 10px; border-radius:9px; font-size:12px;
  border:1px solid var(--border); background:var(--surface); color:var(--text); outline:none;
  transition:border-color .15s;
}
.topik-inp:focus { border-color:var(--ac); }

/* ── Modal ── */
.modal-bg {
  position:fixed; inset:0; background:rgba(0,0,0,.55); z-index:100;
  display:flex; align-items:center; justify-content:center; padding:16px;
}
.modal-box {
  background:var(--surface); border:1px solid var(--border); border-radius:20px;
  width:100%; max-width:440px; overflow:hidden;
}
.modal-hd { display:flex; align-items:center; justify-content:space-between; padding:18px 20px 14px; border-bottom:1px solid var(--border); }
.modal-title { font-family:'Clash Display',sans-serif; font-weight:700; font-size:16px; color:var(--text); }
.modal-close { width:30px; height:30px; border-radius:8px; border:none; background:var(--surface2); color:var(--muted); cursor:pointer; display:grid; place-items:center; }
.modal-close:hover { opacity:.75; }
.modal-bd { padding:18px 20px; }
.modal-ft { display:flex; justify-content:flex-end; gap:8px; padding:14px 20px 18px; border-top:1px solid var(--border); }

.field-label { display:block; font-size:11.5px; font-weight:600; color:var(--muted); margin-bottom:5px; }
.field-input { width:100%; padding:8px 12px; border-radius:10px; border:1px solid var(--border); background:var(--surface2); color:var(--text); font-size:13px; outline:none; transition:border-color .15s; }
.field-input:focus { border-color:var(--ac); }
select.field-input { cursor:pointer; }

.btn-primary { padding:8px 18px; border-radius:10px; font-size:13px; font-weight:600; color:#fff; border:none; cursor:pointer; background:var(--ac); transition:opacity .15s; }
.btn-primary:hover { opacity:.85; }
.btn-primary:disabled { opacity:.5; cursor:not-allowed; }
.btn-ghost   { padding:8px 14px; border-radius:10px; font-size:13px; font-weight:600; background:var(--surface2); color:var(--muted); border:none; cursor:pointer; transition:opacity .15s; }
.btn-ghost:hover { opacity:.75; }

/* ── Konten modal body rich styles ── */
#konten-modal-body h2 { font-size:1.15em; font-weight:700; margin:.5em 0 .2em; }
#konten-modal-body h3 { font-size:1em; font-weight:700; margin:.4em 0 .2em; }
#konten-modal-body ul, #konten-modal-body ol { padding-left:1.3em; margin:.25em 0; }
#konten-modal-body blockquote { border-left:3px solid var(--ac); padding-left:10px; color:var(--muted); font-style:italic; }
#konten-modal-body img { max-width:100%; height:auto; border-radius:8px; border:1px solid var(--border); margin:.3em 0; display:block; }

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
$tugas   = $kelompok->tugas;
$kelas   = $tugas->kelas;
$mk      = $kelas->mataKuliah;
$periode = $kelas->periodeAkademik;
$isOverdue = $tugas->deadline && $tugas->deadline->isPast() && $tugas->status !== 'selesai';
$csrf    = csrf_token();
$isClosed = $tugas->status === 'selesai';
// Anggota tanpa ketua (ketua punya section tersendiri)
$anggotaList = $kelompok->anggota->where('mahasiswa_id', '!=', $kelompok->ketua_mahasiswa_id)->values();
@endphp

<div class="space-y-5 animate-fadeUp">

  {{-- Breadcrumb --}}
  <div class="flex items-center gap-2 text-[12px]" style="color:var(--muted)">
    <a href="{{ route('mahasiswa.tugas.index') }}" class="a-text hover:underline">Tugas</a>
    <i class="fa-solid fa-chevron-right text-[10px]"></i>
    <span style="color:var(--text)">{{ $kelompok->nama_kelompok }}</span>
  </div>

  {{-- Header card --}}
  <div class="page-card">
    <div class="h-1 a-grad"></div>
    <div class="p-5">
      <div class="flex items-start gap-4 flex-wrap justify-between">
        <div>
          <div class="flex items-center gap-2 flex-wrap mb-1">
            <span class="badge b-{{ $tugas->status }}">
              <i class="fa-solid fa-circle text-[7px]"></i>{{ ucfirst($tugas->status) }}
            </span>
            <span class="badge" style="background:rgba(245,158,11,.14);color:#fbbf24">
              <i class="fa-solid fa-crown text-[9px]"></i>Ketua
            </span>
            @if($tugas->deadline)
              <span class="badge {{ $isOverdue ? 'b-overdue' : '' }}"
                    style="{{ $isOverdue ? '' : 'background:rgba(100,116,139,.12);color:#94a3b8' }}">
                <i class="fa-regular fa-clock"></i>
                {{ $tugas->deadline->format('d M Y, H:i') }}
                @if($isOverdue) · Lewat @endif
              </span>
            @endif
          </div>
          <h1 class="font-display font-bold text-[20px] leading-snug" style="color:var(--text)">{{ $tugas->judul }}</h1>
          <div class="text-[12px] mt-1" style="color:var(--muted)">
            <i class="fa-solid fa-users mr-1"></i>{{ $kelompok->nama_kelompok }}
            &bull; <i class="fa-solid fa-book-open ml-1 mr-1"></i>{{ $mk?->nama ?? '—' }}
            @if($periode) &bull; {{ $periode->nama }} @endif
          </div>
          @if($tugas->deskripsi)
            <p class="text-[13px] mt-2" style="color:var(--sub)">{{ $tugas->deskripsi }}</p>
          @endif
        </div>
        <div class="flex items-center gap-2">
          <a href="{{ route('mahasiswa.tugas.kelompok.final', $kelompok->id) }}"
             class="flex items-center gap-1.5 px-4 py-2 rounded-xl text-[12px] font-semibold transition-opacity hover:opacity-85"
             style="background:rgba(99,102,241,.12);color:#818cf8">
            <i class="fa-solid fa-layer-group text-[11px]"></i>Kompilasi Final
            @if($kelompok->status_submit === 'submitted')
              <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 flex-shrink-0"></span>
            @endif
          </a>
          <button onclick="openAddModal()"
                  class="flex items-center gap-1.5 px-4 py-2 rounded-xl text-[12px] font-semibold text-white flex-shrink-0 transition-opacity hover:opacity-85"
                  style="background:var(--ac)">
            <i class="fa-solid fa-user-plus text-[11px]"></i>Tambah Anggota
          </button>
        </div>
      </div>
    </div>
  </div>

  {{-- ── Tugasku (Ketua) ─────────────────────────────────── --}}
  <div class="page-card">
    <div class="card-head">
      <div class="flex items-center gap-2">
        <span class="font-display font-semibold text-[15px]" style="color:var(--text)">
          <i class="fa-solid fa-crown mr-1.5" style="color:#fbbf24"></i>Tugasku (sebagai Ketua)
        </span>
        @if($ketuaEntry)
          <span class="badge {{ $ketuaEntry->status_submit === 'submitted' ? 'b-submitted' : 'b-belum' }}">
            <i class="fa-solid fa-{{ $ketuaEntry->status_submit === 'submitted' ? 'check' : 'clock' }} text-[8px]"></i>
            {{ $ketuaEntry->status_submit === 'submitted' ? 'Dikumpulkan' : 'Belum dikumpulkan' }}
          </span>
        @endif
      </div>
    </div>
    <div class="card-body">
      @if($ketuaEntry)
        {{-- Entry sudah ada, tampilkan info + link ke submit page --}}
        <div class="flex items-center justify-between gap-4 flex-wrap">
          <div class="space-y-1">
            @if($ketuaEntry->topik)
              <div class="text-[11px] font-semibold" style="color:var(--muted)">Topik</div>
              <div class="text-[13px] font-semibold px-3 py-1.5 rounded-lg"
                   style="background:rgba(99,102,241,.08);color:#818cf8">
                {{ $ketuaEntry->topik }}
              </div>
            @else
              <div class="text-[12px]" style="color:var(--muted)">Belum ada topik. Kamu bisa mengaturnya di halaman pengerjaan.</div>
            @endif
            @if($ketuaEntry->submitted_at)
              <div class="text-[11px] mt-1" style="color:var(--muted)">
                Dikumpulkan: {{ $ketuaEntry->submitted_at->format('d M Y, H:i') }}
              </div>
            @endif
          </div>
          <a href="{{ route('mahasiswa.tugas.anggota.submit.show', $ketuaEntry->id) }}"
             class="flex items-center gap-1.5 px-4 py-2 rounded-xl text-[12px] font-semibold text-white flex-shrink-0 transition-opacity hover:opacity-85"
             style="background:var(--ac)">
            <i class="fa-solid fa-pen-to-square text-[11px]"></i>
            {{ $ketuaEntry->status_submit === 'submitted' ? 'Lihat Tugasku' : 'Kerjakan Tugasku' }}
          </a>
        </div>
      @else
        {{-- Belum ada entry, tampilkan form buat entry --}}
        <div class="flex items-start gap-4 flex-wrap">
          <div class="flex-1 min-w-0">
            <p class="text-[13px] mb-3" style="color:var(--muted)">
              Kamu belum memiliki entry pengumpulan. Buat entry untuk mulai mengerjakan tugasmu sebagai ketua.
            </p>
            <div class="flex items-center gap-2 flex-wrap">
              <input type="text" id="ketua-topik-inp" placeholder="Topik (opsional)"
                     class="topik-inp" style="max-width:260px">
              <button onclick="createKetuaEntry()" id="create-ketua-btn"
                      class="flex items-center gap-1.5 px-4 py-2 rounded-xl text-[12px] font-semibold text-white flex-shrink-0 transition-opacity hover:opacity-85"
                      style="background:var(--ac)">
                <i class="fa-solid fa-plus text-[11px]"></i>Buat Entry Tugasku
              </button>
            </div>
            <div id="ketua-entry-err" class="hidden mt-2 text-[12px] px-3 py-1.5 rounded-lg"
                 style="background:rgba(239,68,68,.1);color:#fca5a5"></div>
          </div>
        </div>
      @endif
    </div>
  </div>

  {{-- Anggota list --}}
  <div class="page-card">
    <div class="card-head">
      <div>
        <span class="font-display font-semibold text-[15px]" style="color:var(--text)">Daftar Anggota</span>
        <span class="ml-2 text-[11px] font-bold px-2 py-0.5 rounded-full a-bg-lt a-text" id="jumlah-badge">
          {{ $anggotaList->count() }}
        </span>
      </div>
    </div>

    <div id="anggota-wrap">
      @if($anggotaList->isEmpty())
        <div id="empty-state" class="py-12 text-center" style="color:var(--muted)">
          <i class="fa-solid fa-users text-[28px] opacity-20 block mb-3"></i>
          <div class="text-[13px] font-semibold mb-1" style="color:var(--text)">Belum ada anggota</div>
          <p class="text-[12px]">Tambahkan anggota kelompok dan tetapkan topik untuk masing-masing.</p>
        </div>
      @endif
      <table class="anggota-table" id="anggota-table" {{ $anggotaList->isEmpty() ? 'style=display:none' : '' }}>
        <thead>
          <tr>
            <th>Anggota</th>
            <th>NIM</th>
            <th>Topik</th>
            <th>Status</th>
            <th></th>
          </tr>
        </thead>
        <tbody id="anggota-tbody">
          @foreach($anggotaList as $ang)
            <tr id="row-{{ $ang->id }}">
              <td>
                <div class="flex items-center gap-2">
                  <div class="av a-bg-lt a-text">{{ strtoupper(substr($ang->mahasiswa?->nama ?? '?', 0, 1)) }}</div>
                  <span class="font-semibold">{{ $ang->mahasiswa?->nama ?? '—' }}</span>
                </div>
              </td>
              <td class="text-[12px]" style="color:var(--muted)">{{ $ang->mahasiswa?->nim ?? '—' }}</td>
              <td>
                <div class="topik-form">
                  <input type="text" class="topik-inp" id="topik-inp-{{ $ang->id }}"
                         value="{{ $ang->topik ?? '' }}"
                         placeholder="Belum ada topik…"
                         onkeydown="if(event.key==='Enter'){saveTopik({{ $ang->id }}, {{ $kelompok->id }});event.preventDefault();}">
                  <button onclick="saveTopik({{ $ang->id }}, {{ $kelompok->id }})"
                          class="flex-shrink-0 px-2.5 py-1.5 rounded-lg text-[11px] font-semibold a-text a-bg-lt hover:opacity-80 transition-opacity">
                    Simpan
                  </button>
                </div>
              </td>
              <td>
                <span class="badge {{ $ang->status_submit === 'submitted' ? 'b-submitted' : 'b-belum' }}">
                  {{ $ang->status_submit === 'submitted' ? 'Dikumpulkan' : 'Belum' }}
                </span>
              </td>
              <td>
                <div class="flex items-center gap-1">
                  @if($ang->konten)
                  <button onclick="viewKonten('{{ addslashes($ang->mahasiswa?->nama ?? '') }}', '{{ addslashes($ang->topik ?? '') }}', {{ $ang->id }})"
                          class="w-7 h-7 rounded-lg grid place-items-center text-[11px] a-bg-lt a-text hover:opacity-75 transition-opacity"
                          title="Lihat konten">
                    <i class="fa-solid fa-eye"></i>
                  </button>
                  @endif
                  <button onclick="removeAnggota({{ $ang->id }}, {{ $kelompok->id }}, '{{ addslashes($ang->mahasiswa?->nama ?? '') }}')"
                          class="w-7 h-7 rounded-lg grid place-items-center text-[11px] bg-rose-500/10 text-rose-400 hover:opacity-75 transition-opacity">
                    <i class="fa-solid fa-trash"></i>
                  </button>
                </div>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>

</div>

{{-- Modal lihat konten anggota --}}
<div id="konten-modal" class="modal-bg" style="display:none">
  <div class="modal-box" style="max-width:680px;max-height:85vh;display:flex;flex-direction:column">
    <div class="modal-hd">
      <div>
        <div class="modal-title" id="konten-modal-name">—</div>
        <div id="konten-modal-topik" class="text-[11px] mt-0.5" style="color:#818cf8"></div>
      </div>
      <button class="modal-close" onclick="closeKontenModal()"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div id="konten-modal-body" class="modal-bd overflow-y-auto flex-1"
         style="font-size:13.5px;line-height:1.75;color:var(--text)"></div>
  </div>
</div>

{{-- Modal tambah anggota --}}
<div id="add-modal" class="modal-bg" style="display:none">
  <div class="modal-box">
    <div class="modal-hd">
      <span class="modal-title">Tambah Anggota</span>
      <button class="modal-close" onclick="closeAddModal()"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="modal-bd space-y-4">
      <div>
        <label class="field-label">Pilih Mahasiswa</label>
        <select id="add-mhs-select" class="field-input">
          <option value="">— Pilih mahasiswa —</option>
          @foreach($mahasiswaList as $mhs)
            <option value="{{ $mhs->id }}">{{ $mhs->nama }} ({{ $mhs->nim ?? '—' }})</option>
          @endforeach
        </select>
        @if($mahasiswaList->isEmpty())
          <p class="text-[11px] mt-1.5" style="color:var(--muted)">
            Semua mahasiswa aktif di kelas ini sudah menjadi anggota.
          </p>
        @endif
      </div>
      <div>
        <label class="field-label">Topik <span style="color:var(--muted)">(opsional)</span></label>
        <input type="text" id="add-topik-inp" class="field-input" placeholder="Contoh: Bab 3 — Sistem Operasi">
      </div>
      <div id="add-error" class="hidden text-[12px] px-3 py-2 rounded-lg"
           style="background:rgba(239,68,68,.1);color:#fca5a5"></div>
    </div>
    <div class="modal-ft">
      <button class="btn-ghost" onclick="closeAddModal()">Batal</button>
      <button class="btn-primary" id="add-btn" onclick="addAnggota()">
        <i class="fa-solid fa-user-plus mr-1.5"></i>Tambahkan
      </button>
    </div>
  </div>
</div>

<div id="toast"></div>
@endsection

@push('scripts')
<script>
const CSRF        = '{{ $csrf }}';
const KELOMPOK_ID = {{ $kelompok->id }};
// Konten anggota (pre-loaded)
const KONTEN_MAP = @json($anggotaList->keyBy('id')->map(fn($a) => ['konten' => $a->konten, 'nama' => $a->mahasiswa?->nama ?? '—', 'topik' => $a->topik ?? '']));
const ROUTE_ANGGOTA     = `/mahasiswa/tugas/kelompok/${KELOMPOK_ID}/anggota`;
const ROUTE_DEL_ANGGOTA = (id) => `/mahasiswa/tugas/kelompok/${KELOMPOK_ID}/anggota/${id}`;
const ROUTE_TOPIK       = (id) => `/mahasiswa/tugas/kelompok/${KELOMPOK_ID}/anggota/${id}/topik`;
const ROUTE_KETUA_SELF  = `/mahasiswa/tugas/kelompok/${KELOMPOK_ID}/self`;

// ── Lihat konten anggota ─────────────────────────────────────────
function viewKonten(nama, topik, id) {
  const data = KONTEN_MAP[id];
  document.getElementById('konten-modal-name').textContent = nama || (data?.nama ?? '—');
  const topikEl = document.getElementById('konten-modal-topik');
  const topikVal = topik || data?.topik || '';
  topikEl.textContent = topikVal ? 'Topik: ' + topikVal : '';
  topikEl.style.display = topikVal ? '' : 'none';
  document.getElementById('konten-modal-body').innerHTML = data?.konten || '<em style="color:var(--muted)">Belum ada konten.</em>';
  document.getElementById('konten-modal').style.display = 'flex';
}
function closeKontenModal() { document.getElementById('konten-modal').style.display = 'none'; }
document.getElementById('konten-modal').addEventListener('click', function(e) {
  if (e.target === this) closeKontenModal();
});

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

// ── Modal ─────────────────────────────────────────────────────────
function openAddModal()  { document.getElementById('add-modal').style.display = 'flex'; document.getElementById('add-error').classList.add('hidden'); }
function closeAddModal() { document.getElementById('add-modal').style.display = 'none'; }
document.getElementById('add-modal').addEventListener('click', function(e) {
  if (e.target === this) closeAddModal();
});

// ── Tambah anggota ────────────────────────────────────────────────
async function addAnggota() {
  const mhsId = document.getElementById('add-mhs-select').value;
  const topik = document.getElementById('add-topik-inp').value.trim();
  const errEl = document.getElementById('add-error');
  errEl.classList.add('hidden');

  if (!mhsId) { errEl.textContent = 'Pilih mahasiswa terlebih dahulu.'; errEl.classList.remove('hidden'); return; }

  const btn = document.getElementById('add-btn');
  btn.disabled = true;

  try {
    const fd = new FormData();
    fd.append('mahasiswa_id', mhsId);
    if (topik) fd.append('topik', topik);

    const r = await fetch(ROUTE_ANGGOTA, {
      method: 'POST',
      headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
      body: fd,
    });
    const j = await r.json();
    if (!r.ok) { errEl.textContent = j.message || 'Gagal menambahkan.'; errEl.classList.remove('hidden'); return; }

    // Append row ke tabel
    appendRow(j.anggota);

    // Hapus dari select
    const opt = document.querySelector(`#add-mhs-select option[value="${mhsId}"]`);
    opt?.remove();

    document.getElementById('add-topik-inp').value = '';
    document.getElementById('add-mhs-select').value = '';
    closeAddModal();
    toast('Anggota berhasil ditambahkan.');
    updateJumlah(1);
  } catch {
    errEl.textContent = 'Koneksi gagal. Coba lagi.'; errEl.classList.remove('hidden');
  } finally {
    btn.disabled = false;
  }
}

function appendRow(a) {
  const initial = (a.nama || '?').charAt(0).toUpperCase();
  const tbody = document.getElementById('anggota-tbody');
  const row = document.createElement('tr');
  row.id = `row-${a.id}`;
  row.innerHTML = `
    <td>
      <div class="flex items-center gap-2">
        <div class="av a-bg-lt a-text">${initial}</div>
        <span class="font-semibold">${escH(a.nama)}</span>
      </div>
    </td>
    <td class="text-[12px]" style="color:var(--muted)">${escH(a.nim)}</td>
    <td>
      <div class="topik-form">
        <input type="text" class="topik-inp" id="topik-inp-${a.id}"
               value="${escH(a.topik || '')}" placeholder="Belum ada topik…"
               onkeydown="if(event.key==='Enter'){saveTopik(${a.id},${KELOMPOK_ID});event.preventDefault();}">
        <button onclick="saveTopik(${a.id},${KELOMPOK_ID})"
                class="flex-shrink-0 px-2.5 py-1.5 rounded-lg text-[11px] font-semibold a-text a-bg-lt hover:opacity-80 transition-opacity">
          Simpan
        </button>
      </div>
    </td>
    <td><span class="badge b-belum">Belum</span></td>
    <td>
      <button onclick="removeAnggota(${a.id},${KELOMPOK_ID},'${escH(a.nama)}')"
              class="w-7 h-7 rounded-lg grid place-items-center text-[11px] bg-rose-500/10 text-rose-400 hover:opacity-75 transition-opacity">
        <i class="fa-solid fa-trash"></i>
      </button>
    </td>`;
  tbody.appendChild(row);

  // Tampilkan tabel, sembunyikan empty state
  document.getElementById('anggota-table')?.removeAttribute('style');
  document.getElementById('empty-state')?.remove();
}

// ── Simpan topik ──────────────────────────────────────────────────
async function saveTopik(anggotaId, kelompokId) {
  const inp  = document.getElementById(`topik-inp-${anggotaId}`);
  const topik = inp?.value.trim() ?? '';

  try {
    const fd = new FormData();
    fd.append('topik', topik);
    fd.append('_method', 'PATCH');
    const r = await fetch(ROUTE_TOPIK(anggotaId), {
      method: 'POST',
      headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
      body: fd,
    });
    if (r.ok) { toast('Topik disimpan.'); }
    else      { toast('Gagal menyimpan topik.', true); }
  } catch { toast('Koneksi gagal.', true); }
}

// ── Hapus anggota ─────────────────────────────────────────────────
async function removeAnggota(anggotaId, kelompokId, nama) {
  if (!confirm(`Hapus ${nama} dari kelompok ini?`)) return;
  try {
    const r = await fetch(ROUTE_DEL_ANGGOTA(anggotaId), {
      method: 'DELETE',
      headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
    });
    if (!r.ok) { toast('Gagal menghapus.', true); return; }
    document.getElementById(`row-${anggotaId}`)?.remove();
    toast(`${nama} dihapus dari kelompok.`);
    updateJumlah(-1);
    if (!document.querySelector('#anggota-tbody tr')) {
      document.getElementById('anggota-table').style.display = 'none';
      const wrap = document.getElementById('anggota-wrap');
      if (!document.getElementById('empty-state')) {
        wrap.insertAdjacentHTML('afterbegin', `
          <div id="empty-state" class="py-12 text-center" style="color:var(--muted)">
            <i class="fa-solid fa-users text-[28px] opacity-20 block mb-3"></i>
            <div class="text-[13px] font-semibold mb-1" style="color:var(--text)">Belum ada anggota</div>
            <p class="text-[12px]">Tambahkan anggota kelompok dan tetapkan topik untuk masing-masing.</p>
          </div>`);
      }
    }
  } catch { toast('Koneksi gagal.', true); }
}

function updateJumlah(delta) {
  const el = document.getElementById('jumlah-badge');
  if (!el) return;
  el.textContent = Math.max(0, (parseInt(el.textContent) || 0) + delta);
}

function escH(s) {
  return String(s || '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

// ── Buat entry ketua ───────────────────────────────────────────────
async function createKetuaEntry() {
  const topik = document.getElementById('ketua-topik-inp')?.value.trim() ?? '';
  const errEl = document.getElementById('ketua-entry-err');
  const btn   = document.getElementById('create-ketua-btn');
  errEl.classList.add('hidden');
  btn.disabled = true;

  try {
    const fd = new FormData();
    if (topik) fd.append('topik', topik);
    const r = await fetch(ROUTE_KETUA_SELF, {
      method: 'POST',
      headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
      body: fd,
    });
    const j = await r.json();
    if (r.ok) {
      // Redirect ke halaman pengerjaan
      window.location.href = `/mahasiswa/tugas/anggota/${j.id}/submit`;
    } else {
      errEl.textContent = j.message || 'Gagal membuat entry.';
      errEl.classList.remove('hidden');
      btn.disabled = false;
    }
  } catch {
    errEl.textContent = 'Koneksi gagal. Coba lagi.';
    errEl.classList.remove('hidden');
    btn.disabled = false;
  }
}
</script>
@endpush
