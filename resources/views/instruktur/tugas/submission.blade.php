@extends('layouts.instruktur')
@section('title', 'Penilaian — ' . $kelompok->nama_kelompok)
@section('page-title', 'Penilaian Tugas Kelompok')

@push('styles')
<style>
/* ── Layout ── */
.review-layout {
  display:grid;
  grid-template-columns:300px 1fr;
  gap:20px;
  align-items:start;
}
@media(max-width:940px){ .review-layout { grid-template-columns:1fr; } }

/* ── Card ── */
.page-card {
  background:var(--surface); border:1px solid var(--border); border-radius:18px; overflow:hidden;
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

/* ── Info rows ── */
.info-row { display:flex; flex-direction:column; gap:2px; }
.info-label { font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:var(--muted); }
.info-val   { font-size:13px; font-weight:600; color:var(--text); }

/* ── Tabs ── */
.tab-bar { display:flex; border-bottom:1px solid var(--border); }
.tab-btn {
  padding:11px 18px; font-size:12px; font-weight:600; color:var(--muted);
  border:none; background:none; cursor:pointer; border-bottom:2px solid transparent;
  margin-bottom:-1px; transition:color .15s, border-color .15s;
}
.tab-btn.active { color:var(--ac); border-bottom-color:var(--ac); }

/* ── Konten viewer ── */
.konten-viewer {
  padding:18px 20px; min-height:200px;
  font-size:13.5px; line-height:1.75; color:var(--text);
}
.konten-viewer h2 { font-size:1.2em; font-weight:700; margin:.5em 0 .25em; }
.konten-viewer h3 { font-size:1em; font-weight:700; margin:.4em 0 .2em; }
.konten-viewer ul,
.konten-viewer ol { padding-left:1.3em; margin:.25em 0; }
.konten-viewer blockquote { border-left:3px solid var(--ac); padding-left:10px; color:var(--muted); font-style:italic; }
.konten-viewer img { max-width:100%; height:auto; border-radius:10px; border:1px solid var(--border); margin:.4em 0; display:block; }
.no-konten { color:var(--muted); font-style:italic; font-size:13px; }

/* ── Anggota tab content ── */
.anggota-card {
  border:1px solid var(--border); border-radius:12px; overflow:hidden; margin-bottom:14px;
}
.anggota-card-head {
  display:flex; align-items:center; gap:10px; padding:12px 14px;
  border-bottom:1px solid var(--border); background:var(--surface2);
  cursor:pointer;
}
.av { width:32px; height:32px; border-radius:10px; display:grid; place-items:center; font-size:11px; font-weight:700; flex-shrink:0; }
.anggota-body { padding:14px; }

/* ── Grade inputs ── */
.grade-row { display:grid; grid-template-columns:100px 1fr; gap:10px; align-items:start; }
.grade-inp {
  width:100%; padding:7px 12px; border-radius:10px;
  border:1px solid var(--border); background:var(--surface2); color:var(--text);
  font-size:13px; outline:none; transition:border-color .15s;
}
.grade-inp:focus { border-color:var(--ac); }
textarea.grade-inp { resize:vertical; min-height:60px; font-family:inherit; }

/* ── Kelompok grade section ── */
.grade-section {
  border:1px solid var(--border); border-radius:14px; padding:16px 18px; margin-bottom:16px;
}

/* ── Buttons ── */
.btn-primary { padding:9px 22px; border-radius:11px; font-size:13px; font-weight:600; color:#fff; border:none; cursor:pointer; background:var(--ac); transition:opacity .15s; }
.btn-primary:hover { opacity:.85; }
.btn-primary:disabled { opacity:.5; cursor:not-allowed; }
.btn-ghost   { padding:9px 16px; border-radius:11px; font-size:13px; font-weight:600; background:var(--surface2); color:var(--muted); border:none; cursor:pointer; transition:opacity .15s; }
.btn-ghost:hover { opacity:.75; }

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
$csrf    = csrf_token();
@endphp

<div class="space-y-5 animate-fadeUp">

  {{-- Breadcrumb --}}
  <div class="flex items-center gap-2 text-[12px]" style="color:var(--muted)">
    <a href="{{ route('instruktur.tugas.index') }}" class="a-text hover:underline">Tugas</a>
    <i class="fa-solid fa-chevron-right text-[10px]"></i>
    <span style="color:var(--text)">{{ $kelompok->nama_kelompok }}</span>
  </div>

  <div class="review-layout">

    {{-- ── Sidebar ─────────────────────────────────────────────── --}}
    <div class="space-y-4">
      <div class="page-card">
        <div class="h-1 a-grad"></div>
        <div class="card-body space-y-4">
          <div class="flex flex-wrap gap-2">
            <span class="badge {{ $kelompok->status_submit === 'submitted' ? 'b-submitted' : 'b-belum' }}">
              <i class="fa-solid fa-{{ $kelompok->status_submit === 'submitted' ? 'check' : 'clock' }} text-[9px]"></i>
              {{ $kelompok->status_submit === 'submitted' ? 'Dikumpulkan' : 'Belum dikumpulkan' }}
            </span>
            <span class="badge b-{{ $tugas->status }}">{{ ucfirst($tugas->status) }}</span>
          </div>

          <div class="info-row">
            <span class="info-label">Kelompok</span>
            <span class="info-val">{{ $kelompok->nama_kelompok }}</span>
          </div>
          <div class="info-row">
            <span class="info-label">Tugas</span>
            <span class="info-val text-[12px]">{{ $tugas->judul }}</span>
          </div>
          <div class="info-row">
            <span class="info-label">Mata Kuliah</span>
            <span class="info-val text-[12px]">{{ $mk?->nama ?? '—' }}</span>
          </div>
          @if($kelompok->ketua)
          <div class="info-row">
            <span class="info-label">Ketua</span>
            <span class="info-val text-[12px]">{{ $kelompok->ketua->nama }}</span>
          </div>
          @endif
          @if($tugas->deadline)
          <div class="info-row">
            <span class="info-label">Deadline</span>
            <span class="text-[12px]" style="color:var(--text)">{{ $tugas->deadline->format('d M Y, H:i') }}</span>
          </div>
          @endif
          @if($kelompok->submitted_at)
          <div class="info-row">
            <span class="info-label">Waktu Pengumpulan</span>
            <span class="text-[12px]" style="color:var(--text)">{{ $kelompok->submitted_at->format('d M Y, H:i') }}</span>
          </div>
          @endif
          @if($kelompok->pdf_path)
          <a href="{{ Storage::url($kelompok->pdf_path) }}" target="_blank"
             class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-[12px] font-semibold transition-opacity hover:opacity-80"
             style="background:rgba(239,68,68,.1);color:#f87171">
            <i class="fa-solid fa-file-pdf"></i>Unduh PDF Tugas
          </a>
          @endif
        </div>
      </div>

      {{-- Nilai kelompok (summary setelah dinilai) --}}
      @if($kelompok->nilai_kelompok !== null)
      <div class="page-card">
        <div class="card-body">
          <div class="info-label mb-1"><i class="fa-solid fa-star mr-1" style="color:#fbbf24"></i>Nilai Kelompok</div>
          <div class="font-display font-bold text-[30px] a-text">{{ $kelompok->nilai_kelompok }}</div>
          @if($kelompok->catatan_kelompok)
          <p class="text-[12px] mt-1" style="color:var(--sub)">{{ $kelompok->catatan_kelompok }}</p>
          @endif
        </div>
      </div>
      @endif
    </div>

    {{-- ── Main: submission + grading ─────────────────────────── --}}
    <div class="space-y-5">

      {{-- Tabs: Dokumen Final | Per Anggota | Penilaian --}}
      <div class="page-card">
        <div class="tab-bar">
          <button class="tab-btn active" onclick="switchTab('final', this)">
            <i class="fa-solid fa-layer-group mr-1.5"></i>Dokumen Final
          </button>
          <button class="tab-btn" onclick="switchTab('anggota', this)">
            <i class="fa-solid fa-users mr-1.5"></i>Tugas per Anggota
            <span class="ml-1 text-[10px] font-bold px-1.5 py-0.5 rounded-full a-bg-lt a-text">{{ $kelompok->anggota->count() }}</span>
          </button>
          <button class="tab-btn" onclick="switchTab('nilai', this)">
            <i class="fa-solid fa-star mr-1.5"></i>Penilaian
          </button>
        </div>

        {{-- Tab: Dokumen Final --}}
        <div id="tab-final">
          @if($kelompok->konten_final)
            <div class="konten-viewer">{!! $kelompok->konten_final !!}</div>
          @else
            <div class="konten-viewer no-konten py-12 text-center">
              <i class="fa-solid fa-file-circle-xmark text-[26px] opacity-20 block mb-3"></i>
              Belum ada dokumen final yang dikumpulkan.
            </div>
          @endif
        </div>

        {{-- Tab: Per Anggota --}}
        <div id="tab-anggota" style="display:none;padding:16px">
          @forelse($kelompok->anggota as $ang)
          <div class="anggota-card">
            <div class="anggota-card-head" onclick="toggleBlock({{ $ang->id }})">
              <div class="av a-bg-lt a-text">{{ strtoupper(substr($ang->mahasiswa?->nama ?? '?', 0, 1)) }}</div>
              <div class="flex-1 min-w-0">
                <div class="font-semibold text-[12px]" style="color:var(--text)">
                  {{ $ang->mahasiswa?->nama ?? '—' }}
                  @if($ang->mahasiswa_id === $kelompok->ketua_mahasiswa_id)
                    <span class="text-[9px] font-bold px-1.5 py-0.5 rounded ml-1"
                          style="background:rgba(245,158,11,.14);color:#fbbf24">Ketua</span>
                  @endif
                </div>
                @if($ang->topik)
                  <div class="text-[10px] a-text">{{ $ang->topik }}</div>
                @endif
              </div>
              <div class="flex items-center gap-2">
                <span class="badge {{ $ang->status_submit === 'submitted' ? 'b-submitted' : 'b-belum' }}">
                  {{ $ang->status_submit === 'submitted' ? 'Dikumpulkan' : 'Belum' }}
                </span>
                @if($ang->nilai !== null)
                  <span class="badge" style="background:rgba(245,158,11,.12);color:#fbbf24">
                    <i class="fa-solid fa-star text-[8px]"></i>{{ $ang->nilai }}
                  </span>
                @endif
                <i id="chev-{{ $ang->id }}" class="fa-solid fa-chevron-down text-[10px]" style="color:var(--muted);transition:transform .2s"></i>
              </div>
            </div>
            <div id="block-{{ $ang->id }}" style="display:none">
              <div class="konten-viewer">
                @if($ang->konten)
                  {!! $ang->konten !!}
                @else
                  <span class="no-konten">Anggota belum mengirim konten.</span>
                @endif
              </div>
            </div>
          </div>
          @empty
          <div class="py-8 text-center text-[12px]" style="color:var(--muted)">Belum ada anggota.</div>
          @endforelse
        </div>

        {{-- Tab: Penilaian --}}
        <div id="tab-nilai" style="display:none;padding:20px">
          @if($kelompok->status_submit !== 'submitted')
          <div class="flex items-center gap-3 px-4 py-3 rounded-xl mb-5"
               style="background:rgba(245,158,11,.08);border:1px solid rgba(245,158,11,.2)">
            <i class="fa-solid fa-triangle-exclamation" style="color:#fbbf24"></i>
            <p class="text-[12px]" style="color:var(--sub)">Kelompok belum mengumpulkan tugas. Penilaian bisa diberikan sewaktu-waktu.</p>
          </div>
          @endif

          {{-- Nilai Kelompok --}}
          <div class="grade-section">
            <div class="font-display font-semibold text-[14px] mb-4" style="color:var(--text)">
              <i class="fa-solid fa-users mr-1.5 a-text"></i>Nilai Kelompok
            </div>
            <div class="space-y-3">
              <div>
                <label class="info-label mb-1.5 block">Nilai (0–100)</label>
                <input type="number" id="nilai-kelompok" min="0" max="100" class="grade-inp" style="max-width:120px"
                       value="{{ $kelompok->nilai_kelompok ?? '' }}" placeholder="—">
              </div>
              <div>
                <label class="info-label mb-1.5 block">Catatan untuk Kelompok</label>
                <textarea id="catatan-kelompok" class="grade-inp" placeholder="Catatan penilaian kelompok…">{{ $kelompok->catatan_kelompok ?? '' }}</textarea>
              </div>
            </div>
          </div>

          {{-- Nilai per Anggota --}}
          <div class="font-display font-semibold text-[14px] mb-3" style="color:var(--text)">
            <i class="fa-solid fa-user mr-1.5 a-text"></i>Nilai Individu
          </div>

          @foreach($kelompok->anggota as $ang)
          <div class="grade-section" style="margin-bottom:12px">
            <div class="flex items-center gap-2 mb-3">
              <div class="av a-bg-lt a-text text-[10px]">{{ strtoupper(substr($ang->mahasiswa?->nama ?? '?', 0, 1)) }}</div>
              <div>
                <div class="font-semibold text-[13px]" style="color:var(--text)">
                  {{ $ang->mahasiswa?->nama ?? '—' }}
                  @if($ang->mahasiswa_id === $kelompok->ketua_mahasiswa_id)
                    <span class="text-[9px] font-bold px-1.5 py-0.5 rounded ml-1"
                          style="background:rgba(245,158,11,.14);color:#fbbf24">Ketua</span>
                  @endif
                </div>
                @if($ang->topik)
                  <div class="text-[10px] a-text">{{ $ang->topik }}</div>
                @endif
              </div>
              <span class="badge ml-auto {{ $ang->status_submit === 'submitted' ? 'b-submitted' : 'b-belum' }}">
                {{ $ang->status_submit === 'submitted' ? 'Dikumpulkan' : 'Belum' }}
              </span>
            </div>
            <input type="hidden" class="anggota-id" value="{{ $ang->id }}">
            <div class="grade-row">
              <div>
                <label class="info-label mb-1.5 block">Nilai (0–100)</label>
                <input type="number" class="grade-inp anggota-nilai" min="0" max="100"
                       value="{{ $ang->nilai ?? '' }}" placeholder="—">
              </div>
              <div>
                <label class="info-label mb-1.5 block">Catatan</label>
                <textarea class="grade-inp anggota-catatan" placeholder="Catatan untuk anggota ini…">{{ $ang->catatan_instruktur ?? '' }}</textarea>
              </div>
            </div>
          </div>
          @endforeach

          <div class="flex justify-end mt-4">
            <button onclick="saveGrade()" class="btn-primary" id="grade-btn">
              <i class="fa-solid fa-floppy-disk mr-1.5"></i>Simpan Penilaian
            </button>
          </div>
        </div>

      </div>
    </div>

  </div>
</div>

<div id="toast"></div>
@endsection

@push('scripts')
<script>
const CSRF        = '{{ $csrf }}';
const ROUTE_GRADE = '{{ route('instruktur.tugas.kelompok.grade', [$tugas->id, $kelompok->id]) }}';

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

// ── Tabs ─────────────────────────────────────────────────────────
function switchTab(name, btn) {
  ['final','anggota','nilai'].forEach(t => {
    document.getElementById('tab-' + t).style.display = t === name ? '' : 'none';
  });
  document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
}

// ── Accordion anggota ─────────────────────────────────────────────
function toggleBlock(id) {
  const box   = document.getElementById('block-' + id);
  const chev  = document.getElementById('chev-' + id);
  const open  = box.style.display === 'none';
  box.style.display = open ? 'block' : 'none';
  chev.style.transform = open ? 'rotate(180deg)' : '';
}

// ── Save grade ────────────────────────────────────────────────────
async function saveGrade() {
  const btn = document.getElementById('grade-btn');
  btn.disabled = true;

  // Kumpulkan data anggota
  const anggotaData = [];
  document.querySelectorAll('.anggota-id').forEach(hiddenInput => {
    const block   = hiddenInput.closest('.grade-section');
    const nilai   = block.querySelector('.anggota-nilai').value;
    const catatan = block.querySelector('.anggota-catatan').value;
    anggotaData.push({
      id:      parseInt(hiddenInput.value),
      nilai:   nilai !== '' ? parseInt(nilai) : null,
      catatan: catatan || null,
    });
  });

  const payload = {
    nilai_kelompok:   document.getElementById('nilai-kelompok').value !== ''
                        ? parseInt(document.getElementById('nilai-kelompok').value) : null,
    catatan_kelompok: document.getElementById('catatan-kelompok').value || null,
    anggota:          anggotaData,
  };

  try {
    const r = await fetch(ROUTE_GRADE, {
      method: 'POST',
      headers: {
        'Accept':       'application/json',
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': CSRF,
      },
      body: JSON.stringify(payload),
    });
    const j = await r.json();
    if (r.ok) { toast('Penilaian berhasil disimpan.'); }
    else       { toast(j.message || 'Gagal menyimpan.', true); }
  } catch {
    toast('Koneksi gagal. Coba lagi.', true);
  } finally {
    btn.disabled = false;
  }
}
</script>
@endpush
