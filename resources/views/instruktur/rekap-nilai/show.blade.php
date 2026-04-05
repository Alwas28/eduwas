@extends('layouts.instruktur')
@section('title', 'Rekap Nilai — ' . ($kelas->mataKuliah->nama ?? ''))
@section('page-title', 'Rekap Nilai')

@push('styles')
<style>
/* ── Info bar ── */
.info-bar{background:var(--surface);border:1px solid var(--border);border-radius:14px;padding:14px 18px;display:flex;align-items:center;gap:12px;flex-wrap:wrap}
.info-chip{display:inline-flex;align-items:center;gap:6px;font-size:12px;color:var(--sub);background:var(--surface2);border-radius:99px;padding:4px 12px;border:1px solid var(--border)}

/* ── Two-column layout ── */
.rekap-grid{display:grid;grid-template-columns:300px 1fr;gap:20px;align-items:start}
@media(max-width:900px){.rekap-grid{grid-template-columns:1fr}}

/* ── Setup panel ── */
.setup-panel{background:var(--surface);border:1px solid var(--border);border-radius:16px;overflow:hidden;position:sticky;top:80px}
.setup-header{padding:14px 16px;border-bottom:1px solid var(--border);font-size:13px;font-weight:700;color:var(--text)}
.setup-body{padding:14px 16px}
.komp-item{display:flex;align-items:center;gap:8px;padding:8px 10px;border-radius:10px;border:1px solid var(--border);margin-bottom:6px;background:var(--surface2)}
.komp-badge{font-size:10px;font-weight:700;padding:2px 8px;border-radius:99px}
.komp-badge-tugas{background:rgba(245,158,11,.15);color:#fbbf24}
.komp-badge-ujian{background:rgba(79,110,247,.15);color:#818cf8}
.btn-add{width:100%;padding:8px;border-radius:10px;font-size:12.5px;font-weight:600;border:1.5px dashed var(--border);color:var(--muted);cursor:pointer;transition:all .15s;background:transparent;margin-top:4px}
.btn-add:hover{border-color:var(--ac);color:var(--ac);background:var(--ac-lt)}

/* ── Rekap table ── */
.rekap-wrap{background:var(--surface);border:1px solid var(--border);border-radius:16px;overflow:hidden}
.rekap-head{padding:14px 18px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;gap:12px}
.rekap-table{width:100%;border-collapse:collapse}
.rekap-table th{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.7px;color:var(--muted);padding:10px 14px;text-align:left;background:var(--surface2);border-bottom:1px solid var(--border)}
.rekap-table td{font-size:13px;color:var(--text);padding:11px 14px;border-bottom:1px solid var(--border)}
.rekap-table tr:last-child td{border-bottom:none}
.rekap-table tr:hover td{background:var(--card-hover)}
.val-box{font-family:'Clash Display',sans-serif;font-size:16px;font-weight:700}
.val-null{color:var(--muted);font-family:inherit;font-size:13px}
.sesi-select{background:var(--surface2);border:1px solid var(--border);color:var(--text);border-radius:8px;padding:4px 8px;font-size:12px;font-family:inherit;outline:none;cursor:pointer}
.sesi-select:focus{border-color:var(--ac)}

/* ── Add komponen modal ── */
.modal-md{max-width:480px}
</style>
@endpush

@section('content')
@php
  $mk      = $kelas->mataKuliah;
  $periode = $kelas->periodeAkademik;
  $allInstr= $kelas->instruktur;
@endphp

<div class="space-y-4 animate-fadeUp">

{{-- ── Info Bar ── --}}
<div class="info-bar">
  <div class="flex-1 min-w-0">
    <div class="font-display font-bold text-[17px]" style="color:var(--text)">{{ $mk->nama ?? '—' }}</div>
    <div class="flex flex-wrap gap-2 mt-1.5">
      <span class="info-chip"><i class="fa-solid fa-calendar-days text-[10px]"></i>{{ $periode->nama ?? 'Tanpa Periode' }}</span>
      <span class="info-chip"><i class="fa-solid fa-code-branch text-[10px]"></i>{{ $kelas->kode_display }}</span>
      @if($mk) <span class="info-chip"><i class="fa-solid fa-layer-group text-[10px]"></i>{{ $mk->sks }} SKS</span> @endif
      <span class="info-chip"><i class="fa-solid fa-users text-[10px]"></i>{{ $enrollments->count() }} Mahasiswa</span>
      @foreach($allInstr as $ins)
        <span class="info-chip" style="background:var(--ac-lt);border-color:var(--ac)">
          <i class="fa-solid fa-user-tie a-text text-[10px]"></i>
          <span class="a-text font-semibold">{{ $ins->nama }}</span>
        </span>
      @endforeach
    </div>
  </div>
  <a href="{{ route('instruktur.rekap-nilai.index') }}" class="info-chip" style="text-decoration:none">
    <i class="fa-solid fa-arrow-left text-[10px]"></i>Kembali
  </a>
</div>

{{-- ── Main Grid ── --}}
<div class="rekap-grid">

  {{-- ── Setup Panel ── --}}
  <div class="setup-panel">
    <div class="setup-header">
      <i class="fa-solid fa-sliders mr-1.5 a-text"></i>Komponen Nilai Saya
    </div>
    <div class="setup-body">

      @if($komponen->isEmpty())
        <p class="text-[12.5px] text-center py-4" style="color:var(--muted)">Belum ada komponen.<br>Tambahkan tugas atau ujian.</p>
      @else
        {{-- Tugas components --}}
        @if($komponenTugas->isNotEmpty())
        <p class="text-[10.5px] font-bold uppercase tracking-wider mb-2" style="color:var(--muted)">Tugas</p>
        @foreach($komponenTugas as $komp)
        <div class="komp-item">
          <span class="komp-badge komp-badge-tugas">T</span>
          <span class="flex-1 text-[12.5px] truncate" style="color:var(--text)">{{ $komp->label }}</span>
          <form method="POST" action="{{ route('instruktur.rekap-nilai.komponen.destroy', [$kelas, $komp]) }}" onsubmit="return confirm('Hapus komponen ini?')">
            @csrf @method('DELETE')
            <button type="submit" class="text-red-400 hover:text-red-300 text-[12px] p-1"><i class="fa-solid fa-trash"></i></button>
          </form>
        </div>
        @endforeach
        @endif

        {{-- Ujian components --}}
        @if($komponenUjian->isNotEmpty())
        <p class="text-[10.5px] font-bold uppercase tracking-wider mb-2 mt-3" style="color:var(--muted)">Ujian</p>
        @foreach($komponenUjian as $komp)
        <div class="komp-item">
          <span class="komp-badge komp-badge-ujian">U</span>
          <span class="flex-1 text-[12.5px] truncate" style="color:var(--text)">{{ $komp->label }}</span>
          <form method="POST" action="{{ route('instruktur.rekap-nilai.komponen.destroy', [$kelas, $komp]) }}" onsubmit="return confirm('Hapus komponen ini?')">
            @csrf @method('DELETE')
            <button type="submit" class="text-red-400 hover:text-red-300 text-[12px] p-1"><i class="fa-solid fa-trash"></i></button>
          </form>
        </div>
        @endforeach
        @endif
      @endif

      {{-- Add buttons --}}
      @if($tugasOptions->isNotEmpty())
      <button class="btn-add mt-3" onclick="openModal('modal-add-tugas')">
        <i class="fa-solid fa-plus mr-1.5"></i>Tambah Komponen Tugas
      </button>
      @endif
      @if($ujianOptions->isNotEmpty())
      <button class="btn-add" onclick="openModal('modal-add-ujian')">
        <i class="fa-solid fa-plus mr-1.5"></i>Tambah Komponen Ujian
      </button>
      @endif

      @if($tugasOptions->isEmpty() && $ujianOptions->isEmpty())
        <p class="text-[12px] mt-3 text-center" style="color:var(--muted)">Belum ada tugas/ujian yang Anda buat di kelas ini.</p>
      @endif

    </div>
  </div>

  {{-- ── Rekap Table ── --}}
  <div class="rekap-wrap">
    <div class="rekap-head">
      <span class="font-semibold text-[14px]" style="color:var(--text)">Nilai Mahasiswa</span>
      @if($komponen->isEmpty())
        <span class="text-[12px]" style="color:var(--muted)">Setup komponen terlebih dahulu</span>
      @endif
    </div>

    @if($enrollments->isEmpty())
      <div class="text-center py-12" style="color:var(--muted)">
        <i class="fa-solid fa-users text-3xl block mb-2 opacity-30"></i>
        <p class="text-sm">Belum ada mahasiswa terdaftar.</p>
      </div>
    @else
    <div style="overflow-x:auto">
    <table class="rekap-table">
      <thead>
        <tr>
          <th style="min-width:200px">Mahasiswa</th>
          <th class="text-center">Nilai Tugas</th>
          @foreach($komponenUjian as $komp)
          <th class="text-center">{{ $komp->label }}</th>
          @endforeach
          @if($komponenUjian->isEmpty())
          <th class="text-center">Nilai Ujian</th>
          @endif
        </tr>
      </thead>
      <tbody>
        @foreach($enrollments as $enrollment)
        @php
          $mhs      = $enrollment->mahasiswa;
          $mhsId    = $mhs->id;
          $nd       = $nilaiData[$mhsId] ?? ['nilai_tugas' => null, 'nilai_ujian' => null, 'nilai_ujian_per_komp' => []];
        @endphp
        <tr>
          {{-- Nama --}}
          <td>
            <div class="font-medium text-[13px]" style="color:var(--text)">{{ $mhs->nama }}</div>
            <div class="text-[11px]" style="color:var(--muted)">{{ $mhs->nim }}</div>
          </td>

          {{-- Nilai Tugas --}}
          <td class="text-center">
            @if($komponenTugas->isEmpty())
              <span class="val-null">—</span>
            @elseif($nd['nilai_tugas'] !== null)
              <span class="val-box" style="color:{{ $nd['nilai_tugas'] >= 75 ? '#10b981' : ($nd['nilai_tugas'] >= 55 ? '#fbbf24' : '#f87171') }}">
                {{ number_format($nd['nilai_tugas'], 2) }}
              </span>
            @else
              <span class="val-null">Belum dinilai</span>
            @endif
          </td>

          {{-- Nilai per komponen ujian --}}
          @foreach($komponenUjian as $komp)
          @php
            $nilaiKomp  = $nd['nilai_ujian_per_komp'][$komp->id] ?? null;
            $sesiList   = $sesiAvailable[$komp->id][$mhsId] ?? collect();
            $pilihanRow = $pilihanMap[$komp->id][$mhsId] ?? null;
            $selectedSesiId = $pilihanRow?->ujian_sesi_id
              ?? ($sesiList->first()?->id);
          @endphp
          <td class="text-center">
            @if($nilaiKomp !== null)
              <div class="val-box mb-1" style="color:{{ $nilaiKomp >= 75 ? '#10b981' : ($nilaiKomp >= 55 ? '#fbbf24' : '#f87171') }}">
                {{ number_format($nilaiKomp, 1) }}
              </div>
            @endif
            @if($sesiList->count() > 1)
              {{-- Dropdown pilih sesi --}}
              <select class="sesi-select"
                onchange="simpanPilihan({{ $komp->id }}, {{ $mhsId }}, this.value, this)">
                @foreach($sesiList as $sesi)
                  <option value="{{ $sesi->id }}" {{ $sesi->id == $selectedSesiId ? 'selected' : '' }}>
                    {{ $sesi->ujian->judul }} ({{ $sesi->nilai ?? '—' }})
                  </option>
                @endforeach
              </select>
            @elseif($sesiList->count() === 1)
              <span class="text-[11px]" style="color:var(--muted)">{{ $sesiList->first()->ujian->judul }}</span>
            @elseif($nilaiKomp === null)
              <span class="val-null">—</span>
            @endif
          </td>
          @endforeach

          {{-- Kolom nilai ujian jika belum ada komponen ujian --}}
          @if($komponenUjian->isEmpty())
          <td class="text-center"><span class="val-null">—</span></td>
          @endif
        </tr>
        @endforeach
      </tbody>
    </table>
    </div>
    @endif
  </div>

</div>
</div>

{{-- ── Modal: Tambah Komponen Tugas ── --}}
<div id="modal-add-tugas" class="modal-backdrop">
  <div class="modal-box modal-md">
    <div class="flex items-center justify-between p-5 border-b" style="border-color:var(--border)">
      <h3 class="font-display font-bold text-[16px]" style="color:var(--text)">Tambah Komponen Tugas</h3>
      <button onclick="closeModal('modal-add-tugas')" class="text-[18px] leading-none" style="color:var(--muted)">&times;</button>
    </div>
    <form method="POST" action="{{ route('instruktur.rekap-nilai.komponen.store', $kelas) }}" class="p-5 space-y-4">
      @csrf
      <input type="hidden" name="tipe" value="tugas">
      <div>
        <label class="f-label">Pilih Tugas</label>
        <select name="sumber_id" class="f-input" required id="sel-tugas"
          onchange="document.getElementById('lbl-tugas').value = this.options[this.selectedIndex].dataset.label">
          <option value="">— Pilih tugas —</option>
          @foreach($tugasOptions as $t)
          <option value="{{ $t->id }}" data-label="{{ $t->judul }}">{{ $t->judul }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="f-label">Label Komponen</label>
        <input id="lbl-tugas" type="text" name="label" class="f-input" placeholder="contoh: Tugas Pra-UTS" required maxlength="100">
      </div>
      <div class="flex justify-end gap-3">
        <button type="button" onclick="closeModal('modal-add-tugas')" class="px-4 py-2 rounded-xl text-[13px] font-semibold" style="background:var(--surface2);color:var(--sub)">Batal</button>
        <button type="submit" class="px-5 py-2 rounded-xl text-[13px] font-semibold text-white a-grad">Tambahkan</button>
      </div>
    </form>
  </div>
</div>

{{-- ── Modal: Tambah Komponen Ujian ── --}}
<div id="modal-add-ujian" class="modal-backdrop">
  <div class="modal-box modal-md">
    <div class="flex items-center justify-between p-5 border-b" style="border-color:var(--border)">
      <h3 class="font-display font-bold text-[16px]" style="color:var(--text)">Tambah Komponen Ujian</h3>
      <button onclick="closeModal('modal-add-ujian')" class="text-[18px] leading-none" style="color:var(--muted)">&times;</button>
    </div>
    <form method="POST" action="{{ route('instruktur.rekap-nilai.komponen.store', $kelas) }}" class="p-5 space-y-4">
      @csrf
      <input type="hidden" name="tipe" value="ujian">
      <div>
        <label class="f-label">Pilih Ujian</label>
        <select name="sumber_id" class="f-input" required id="sel-ujian"
          onchange="document.getElementById('lbl-ujian').value = this.options[this.selectedIndex].dataset.label">
          <option value="">— Pilih ujian —</option>
          @foreach($ujianOptions as $u)
          <option value="{{ $u->id }}" data-label="{{ $u->judul }}">{{ $u->judul }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="f-label">Label Komponen</label>
        <input id="lbl-ujian" type="text" name="label" class="f-input" placeholder="contoh: UTS" required maxlength="100">
      </div>
      <div class="flex justify-end gap-3">
        <button type="button" onclick="closeModal('modal-add-ujian')" class="px-4 py-2 rounded-xl text-[13px] font-semibold" style="background:var(--surface2);color:var(--sub)">Batal</button>
        <button type="submit" class="px-5 py-2 rounded-xl text-[13px] font-semibold text-white a-grad">Tambahkan</button>
      </div>
    </form>
  </div>
</div>

@endsection

@push('scripts')
<script>
const urlPilihan = {
  @foreach($komponenUjian as $komp)
  {{ $komp->id }}: "{{ route('instruktur.rekap-nilai.pilihan.store', [$kelas, $komp]) }}",
  @endforeach
};
const csrf = document.querySelector('meta[name="csrf-token"]').content;

async function simpanPilihan(komponenId, mahasiswaId, sesiId, selectEl) {
  selectEl.disabled = true;
  try {
    const res = await fetch(urlPilihan[komponenId], {
      method: 'POST',
      headers: {'Content-Type':'application/json','X-CSRF-TOKEN':csrf,'Accept':'application/json'},
      body: JSON.stringify({ mahasiswa_id: mahasiswaId, ujian_sesi_id: sesiId }),
    });
    const data = await res.json();
    if (data.ok) {
      // Update nilai box in the same cell
      const td = selectEl.closest('td');
      let box = td.querySelector('.val-box');
      if (!box) { box = document.createElement('div'); box.className = 'val-box mb-1'; td.insertBefore(box, selectEl); }
      const n = data.nilai;
      box.textContent = n !== null ? parseFloat(n).toFixed(1) : '—';
      box.style.color = n >= 75 ? '#10b981' : (n >= 55 ? '#fbbf24' : '#f87171');
      showToast('success', 'Pilihan ujian disimpan.');
    } else {
      showToast('error', 'Gagal menyimpan.');
    }
  } catch(e) {
    showToast('error', 'Terjadi kesalahan.');
  } finally {
    selectEl.disabled = false;
  }
}

// Auto-fill label dari nama tugas/ujian yang dipilih
document.getElementById('sel-tugas')?.addEventListener('change', function() {
  const lbl = document.getElementById('lbl-tugas');
  if (!lbl.value) lbl.value = this.options[this.selectedIndex].dataset.label || '';
});
document.getElementById('sel-ujian')?.addEventListener('change', function() {
  const lbl = document.getElementById('lbl-ujian');
  if (!lbl.value) lbl.value = this.options[this.selectedIndex].dataset.label || '';
});

@if(session('success')) document.addEventListener('DOMContentLoaded', () => showToast('success', '{{ session("success") }}')); @endif
@if(session('error'))   document.addEventListener('DOMContentLoaded', () => showToast('error',   '{{ session("error") }}')); @endif
</script>
@endpush
