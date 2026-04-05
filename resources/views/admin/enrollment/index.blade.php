@extends('layouts.admin')
@section('title','Enrollment')
@section('page-title','Enrollment')

@push('styles')
@include('admin.partials.datatable-styles')
<style>
.mhs-list { max-height: 200px; overflow-y: auto; scrollbar-width: thin; scrollbar-color: var(--scrollbar) transparent; }
.mhs-list::-webkit-scrollbar { width: 4px; }
.mhs-list::-webkit-scrollbar-thumb { background: var(--scrollbar); border-radius: 99px; }
.mhs-item { display: flex; align-items: center; gap: 10px; padding: 7px 10px; border-radius: 10px; cursor: pointer; transition: background .12s; }
.mhs-item:hover { background: var(--surface2); }
.mhs-item input[type=checkbox] { accent-color: var(--ac); width: 15px; height: 15px; flex-shrink: 0; }
.mhs-item.hidden-opt { display: none; }
.grade-badge { display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; border-radius: 8px; font-size: 12px; font-weight: 700; }
</style>
@endpush

@section('content')
<div id="toast-container" class="toast-wrap"></div>

{{-- Header --}}
<div class="flex items-center justify-between animate-fadeUp">
  <div>
    <h2 class="font-display font-bold text-[20px]" style="color:var(--text)">Enrollment</h2>
    <p class="text-[13px] mt-0.5" style="color:var(--muted)">Pendaftaran mahasiswa ke kelas mata kuliah</p>
  </div>
  @canaccess('tambah.enrollment')
  <button onclick="openModal('modal-create')"
    class="flex items-center gap-2 px-4 py-2 rounded-xl text-[13px] font-semibold text-white a-grad shadow">
    <i class="fa-solid fa-plus text-[11px]"></i> Tambah Enrollment
  </button>
  @endcanaccess
</div>

{{-- Stat Cards --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 animate-fadeUp d1">
  <div class="rounded-2xl p-5 border flex items-center gap-4" style="background:var(--surface);border-color:var(--border)">
    <div class="a-bg-lt a-text w-11 h-11 rounded-xl grid place-items-center text-lg flex-shrink-0"><i class="fa-solid fa-user-plus"></i></div>
    <div>
      <div class="font-display text-[26px] font-bold" style="color:var(--text)">{{ $stats['total'] }}</div>
      <div class="text-[12px]" style="color:var(--muted)">Total Enrollment</div>
    </div>
  </div>
  <div class="rounded-2xl p-5 border flex items-center gap-4" style="background:var(--surface);border-color:var(--border)">
    <div class="bg-emerald-500/15 text-emerald-400 w-11 h-11 rounded-xl grid place-items-center text-lg flex-shrink-0"><i class="fa-solid fa-circle-check"></i></div>
    <div>
      <div class="font-display text-[26px] font-bold" style="color:var(--text)">{{ $stats['aktif'] }}</div>
      <div class="text-[12px]" style="color:var(--muted)">Aktif</div>
    </div>
  </div>
  <div class="rounded-2xl p-5 border flex items-center gap-4" style="background:var(--surface);border-color:var(--border)">
    <div class="bg-rose-500/15 text-rose-400 w-11 h-11 rounded-xl grid place-items-center text-lg flex-shrink-0"><i class="fa-solid fa-user-xmark"></i></div>
    <div>
      <div class="font-display text-[26px] font-bold" style="color:var(--text)">{{ $stats['dropout'] }}</div>
      <div class="text-[12px]" style="color:var(--muted)">Dropout</div>
    </div>
  </div>
  <div class="rounded-2xl p-5 border flex items-center gap-4" style="background:var(--surface);border-color:var(--border)">
    <div class="bg-blue-500/15 text-blue-400 w-11 h-11 rounded-xl grid place-items-center text-lg flex-shrink-0"><i class="fa-solid fa-graduation-cap"></i></div>
    <div>
      <div class="font-display text-[26px] font-bold" style="color:var(--text)">{{ $stats['lulus'] }}</div>
      <div class="text-[12px]" style="color:var(--muted)">Lulus</div>
    </div>
  </div>
</div>

{{-- Filter Bar --}}
<form method="GET" action="{{ route('admin.enrollment.index') }}" class="animate-fadeUp d2">
  <div class="rounded-2xl border p-4 flex flex-wrap items-end gap-3" style="background:var(--surface);border-color:var(--border)">
    <div class="flex-1 min-w-[160px]">
      <label class="f-label mb-1">Periode</label>
      <select name="periode_id" id="filter-periode" class="f-input" onchange="this.form.submit()">
        <option value="">Semua Periode</option>
        @foreach($periodes as $p)
          <option value="{{ $p->id }}" {{ request('periode_id') == $p->id ? 'selected' : '' }}>
            {{ $p->nama }} {{ $p->status === 'Aktif' ? '★' : '' }}
          </option>
        @endforeach
      </select>
    </div>
    <div class="flex-1 min-w-[180px]">
      <label class="f-label mb-1">Kelas</label>
      <select name="kelas_id" id="filter-kelas" class="f-input" onchange="this.form.submit()">
        <option value="">Semua Kelas</option>
        @foreach($allKelas as $kel)
          @php
            $kdp = $kel->mataKuliah?->kode ?? '?';
            if ($kel->kode_seksi) $kdp .= '-'.$kel->kode_seksi;
          @endphp
          <option value="{{ $kel->id }}"
            data-periode="{{ $kel->periode_akademik_id }}"
            {{ request('kelas_id') == $kel->id ? 'selected' : '' }}>
            {{ $kdp }} — {{ $kel->mataKuliah?->nama ?? '?' }}
          </option>
        @endforeach
      </select>
    </div>
    <div class="min-w-[130px]">
      <label class="f-label mb-1">Status</label>
      <select name="status" class="f-input" onchange="this.form.submit()">
        <option value="">Semua Status</option>
        <option value="Aktif"   {{ request('status') === 'Aktif'   ? 'selected' : '' }}>Aktif</option>
        <option value="Dropout" {{ request('status') === 'Dropout' ? 'selected' : '' }}>Dropout</option>
        <option value="Lulus"   {{ request('status') === 'Lulus'   ? 'selected' : '' }}>Lulus</option>
      </select>
    </div>
    @if(request()->hasAny(['periode_id','kelas_id','status']))
    <a href="{{ route('admin.enrollment.index') }}"
      class="px-4 py-2 rounded-xl border text-[13px] font-semibold flex items-center gap-1.5"
      style="border-color:var(--border);color:var(--sub)"
      onmouseover="this.style.background='var(--surface2)'" onmouseout="this.style.background='transparent'">
      <i class="fa-solid fa-xmark text-[11px]"></i> Reset
    </a>
    @endif
  </div>
</form>

{{-- Table --}}
<div class="rounded-2xl overflow-hidden border animate-fadeUp d3" style="background:var(--surface);border-color:var(--border)">
  <div class="flex items-center justify-between px-5 py-4 border-b" style="border-color:var(--border)">
    <span class="font-display font-semibold text-[15px]" style="color:var(--text)">Daftar Enrollment</span>
    <span class="text-[12px] px-2.5 py-1 rounded-full a-bg-lt a-text font-semibold">{{ $enrollments->count() }} peserta</span>
  </div>
  <div class="p-5">
    <table id="enrollment-table" class="w-full" style="width:100%">
      <thead>
        <tr>
          <th>#</th>
          <th>Mahasiswa</th>
          <th>Kelas</th>
          <th>Mata Kuliah</th>
          <th>Periode</th>
          <th>Tgl Daftar</th>
          <th>Nilai</th>
          <th>Status</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        @foreach($enrollments as $i => $enr)
        @php
          $kdp = $enr->kelas->mataKuliah?->kode ?? '?';
          if ($enr->kelas->kode_seksi) $kdp .= '-'.$enr->kelas->kode_seksi;
        @endphp
        <tr>
          <td class="text-center" style="color:var(--muted);width:48px">{{ $i + 1 }}</td>
          <td>
            <div>
              <div class="font-semibold text-[13.5px]" style="color:var(--text)">{{ $enr->mahasiswa->nama }}</div>
              <code class="text-[11px] px-1.5 py-0.5 rounded a-bg-lt a-text" style="font-family:monospace">{{ $enr->mahasiswa->nim }}</code>
            </div>
          </td>
          <td>
            <code class="text-[12px] font-bold px-2 py-0.5 rounded-lg a-bg-lt a-text" style="font-family:monospace">{{ $kdp }}</code>
          </td>
          <td>
            <div>
              <div class="text-[13px] font-medium" style="color:var(--text)">{{ $enr->kelas->mataKuliah?->nama ?? '—' }}</div>
              @if($enr->kelas->mataKuliah?->jurusan)
                <div class="text-[11px]" style="color:var(--muted)">{{ $enr->kelas->mataKuliah->jurusan->nama }}</div>
              @endif
            </div>
          </td>
          <td>
            @if($enr->kelas->periodeAkademik)
              <div class="text-[12.5px]" style="color:var(--text)">{{ $enr->kelas->periodeAkademik->tahun_ajaran }}</div>
              <div class="text-[11px]" style="color:var(--muted)">{{ $enr->kelas->periodeAkademik->semester }}</div>
            @else
              <span style="color:var(--muted)">—</span>
            @endif
          </td>
          <td>
            <span class="text-[12.5px]" style="color:var(--text)">
              {{ $enr->enrolled_at?->format('d M Y') ?? '—' }}
            </span>
          </td>
          <td class="text-center">
            @if($enr->nilai_akhir !== null)
              @php
                $grade = $enr->grade;
                $gc = match($grade) {
                  'A' => 'bg-emerald-500/15 text-emerald-400',
                  'B' => 'bg-blue-500/15 text-blue-400',
                  'C' => 'bg-amber-500/15 text-amber-400',
                  'D' => 'bg-orange-500/15 text-orange-400',
                  default => 'bg-rose-500/15 text-rose-400',
                };
              @endphp
              <div class="flex flex-col items-center gap-0.5">
                <span class="grade-badge {{ $gc }}">{{ $grade }}</span>
                <span class="text-[10px]" style="color:var(--muted)">{{ number_format((float)$enr->nilai_akhir, 1) }}</span>
              </div>
            @else
              <span style="color:var(--muted)" class="text-[12px]">—</span>
            @endif
          </td>
          <td>
            @php
              $sc = match($enr->status) {
                'Aktif'   => 'bg-emerald-500/15 text-emerald-400',
                'Dropout' => 'bg-rose-500/15 text-rose-400',
                'Lulus'   => 'bg-blue-500/15 text-blue-400',
                default   => 'bg-slate-500/15 text-slate-400',
              };
            @endphp
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold {{ $sc }}">
              <i class="fa-solid fa-circle text-[7px]"></i> {{ $enr->status }}
            </span>
          </td>
          <td>
            <div class="flex items-center gap-2">
              @canaccess('edit.enrollment')
              <button onclick="openEdit({{ $enr->id }},{{ json_encode($enr->mahasiswa->nama) }},{{ json_encode($kdp) }},{{ json_encode($enr->status) }},{{ $enr->nilai_akhir ?? 'null' }},{{ json_encode($enr->catatan) }})"
                class="w-8 h-8 rounded-lg grid place-items-center text-[12px] border transition-colors"
                style="border-color:var(--border);color:var(--sub)"
                onmouseover="this.style.borderColor='var(--ac)';this.style.color='var(--ac)'"
                onmouseout="this.style.borderColor='var(--border)';this.style.color='var(--sub)'">
                <i class="fa-solid fa-pen"></i>
              </button>
              @endcanaccess
              @canaccess('hapus.enrollment')
              <button onclick="openDelete({{ $enr->id }},{{ json_encode($enr->mahasiswa->nama) }},{{ json_encode($kdp) }})"
                class="w-8 h-8 rounded-lg grid place-items-center text-[12px] border transition-colors"
                style="border-color:var(--border);color:var(--sub)"
                onmouseover="this.style.borderColor='#f87171';this.style.color='#f87171'"
                onmouseout="this.style.borderColor='var(--border)';this.style.color='var(--sub)'">
                <i class="fa-solid fa-trash"></i>
              </button>
              @endcanaccess
            </div>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>

{{-- MODAL TAMBAH --}}
<div id="modal-create" class="modal-backdrop">
  <div class="modal-box" style="max-width:560px">
    <div class="flex items-center justify-between px-6 py-5 border-b" style="border-color:var(--border)">
      <div class="flex items-center gap-3">
        <div class="a-bg-lt a-text w-10 h-10 rounded-xl grid place-items-center"><i class="fa-solid fa-user-plus"></i></div>
        <h3 class="font-display font-bold text-[16px]" style="color:var(--text)">Tambah Enrollment</h3>
      </div>
      <button onclick="closeModal('modal-create')" class="w-8 h-8 rounded-lg grid place-items-center" style="color:var(--muted)"
        onmouseover="this.style.background='var(--surface2)'" onmouseout="this.style.background='transparent'">
        <i class="fa-solid fa-xmark"></i>
      </button>
    </div>
    <form id="form-create" class="px-6 py-5 space-y-4 overflow-y-auto" style="max-height:74vh">

      {{-- Kelas --}}
      <div>
        <label class="f-label">Kelas <span class="text-red-400">*</span></label>
        @if($allKelas->isEmpty())
          <div class="rounded-lg p-3 text-[12.5px] border" style="background:var(--surface2);border-color:var(--border);color:var(--muted)">
            <i class="fa-solid fa-circle-exclamation mr-1.5 text-amber-400"></i>
            Belum ada kelas. <a href="{{ route('admin.kelas.index') }}" class="a-text underline">Buat kelas dulu</a>.
          </div>
        @else
          <select name="kelas_id" id="create-kelas" class="f-input" onchange="filterMahasiswaByJurusan(this.value)">
            <option value="">— Pilih Kelas —</option>
            @foreach($allKelas as $kel)
              @php
                $kdp = $kel->mataKuliah?->kode ?? '?';
                if ($kel->kode_seksi) $kdp .= '-'.$kel->kode_seksi;
              @endphp
              <option value="{{ $kel->id }}"
                data-jurusan="{{ $kel->mataKuliah?->jurusan_id ?? '' }}"
                data-periode="{{ $kel->periodeAkademik?->nama ?? '' }}">
                {{ $kdp }} — {{ $kel->mataKuliah?->nama ?? '?' }}
                ({{ $kel->periodeAkademik?->tahun_ajaran ?? '?' }} {{ $kel->periodeAkademik?->semester ?? '' }})
              </option>
            @endforeach
          </select>
        @endif
        <p class="f-error hidden" id="err-create-kelas_id"></p>
      </div>

      {{-- Mahasiswa (checkbox list) --}}
      <div>
        <div class="flex items-center justify-between mb-1.5">
          <label class="f-label mb-0">Mahasiswa <span class="text-red-400">*</span></label>
          <span id="create-mhs-hint" class="text-[11px]" style="color:var(--muted)">Pilih kelas dulu untuk filter</span>
        </div>

        {{-- Search mahasiswa --}}
        <div class="flex items-center gap-2 rounded-lg px-3 py-2 mb-2 border" style="background:var(--surface2);border-color:var(--border)">
          <i class="fa-solid fa-magnifying-glass text-[12px]" style="color:var(--muted)"></i>
          <input type="text" id="create-mhs-search" placeholder="Cari nama atau NIM..."
            class="bg-transparent outline-none text-[13px] w-full" style="color:var(--text)"
            oninput="searchMahasiswa('create', this.value)">
        </div>

        @if($mahasiswa->isEmpty())
          <div class="rounded-lg p-3 text-[12.5px] border" style="background:var(--surface2);border-color:var(--border);color:var(--muted)">
            <i class="fa-solid fa-circle-exclamation mr-1.5 text-amber-400"></i>
            Belum ada mahasiswa aktif. <a href="{{ route('admin.peserta.index') }}" class="a-text underline">Tambah dulu</a>.
          </div>
        @else
          <div class="mhs-list rounded-xl border p-2" style="border-color:var(--border)" id="create-mhs-list">
            @foreach($mahasiswa as $mhs)
            <label class="mhs-item" data-name="{{ strtolower($mhs->nama) }}" data-nim="{{ strtolower($mhs->nim) }}" data-jurusan="{{ $mhs->jurusan_id ?? '' }}">
              <input type="checkbox" class="create-mhs-cb" value="{{ $mhs->id }}">
              <div class="flex-1 min-w-0">
                <div class="text-[13px] font-medium truncate" style="color:var(--text)">{{ $mhs->nama }}</div>
                <div class="text-[11px]" style="color:var(--muted)">
                  {{ $mhs->nim }}
                  @if($mhs->jurusan && $mhs->jurusan->id) · {{ $mhs->jurusan->nama }} @endif
                  @if($mhs->angkatan) · {{ $mhs->angkatan }} @endif
                </div>
              </div>
            </label>
            @endforeach
          </div>
          <p class="f-hint" id="create-mhs-count"></p>
        @endif
        <p class="f-error hidden" id="err-create-mahasiswa_id"></p>
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="f-label">Status <span class="text-red-400">*</span></label>
          <select name="status" class="f-input">
            <option value="Aktif" selected>Aktif</option>
            <option value="Dropout">Dropout</option>
            <option value="Lulus">Lulus</option>
          </select>
          <p class="f-error hidden" id="err-create-status"></p>
        </div>
        <div>
          <label class="f-label">Tanggal Daftar</label>
          <input type="date" name="enrolled_at" id="create-enrolled-at" class="f-input" value="{{ now()->toDateString() }}">
          <p class="f-error hidden" id="err-create-enrolled_at"></p>
        </div>
      </div>

      <div class="flex gap-3 pt-2">
        <button type="button" onclick="closeModal('modal-create')" class="flex-1 px-4 py-2 rounded-xl border text-[13px] font-semibold"
          style="border-color:var(--border);color:var(--sub)"
          onmouseover="this.style.background='var(--surface2)'" onmouseout="this.style.background='transparent'">Batal</button>
        <button type="submit" id="btn-create" class="flex-1 px-4 py-2 rounded-xl text-[13px] font-semibold text-white a-grad shadow">
          <i class="fa-solid fa-floppy-disk mr-1.5 text-[11px]"></i>Daftarkan
        </button>
      </div>
    </form>
  </div>
</div>

{{-- MODAL EDIT --}}
<div id="modal-edit" class="modal-backdrop">
  <div class="modal-box" style="max-width:440px">
    <div class="flex items-center justify-between px-6 py-5 border-b" style="border-color:var(--border)">
      <div class="flex items-center gap-3">
        <div class="a-bg-lt a-text w-10 h-10 rounded-xl grid place-items-center"><i class="fa-solid fa-pen"></i></div>
        <div>
          <h3 class="font-display font-bold text-[15px]" style="color:var(--text)">Edit Enrollment</h3>
          <p class="text-[11.5px]" id="edit-subtitle" style="color:var(--muted)"></p>
        </div>
      </div>
      <button onclick="closeModal('modal-edit')" class="w-8 h-8 rounded-lg grid place-items-center" style="color:var(--muted)"
        onmouseover="this.style.background='var(--surface2)'" onmouseout="this.style.background='transparent'">
        <i class="fa-solid fa-xmark"></i>
      </button>
    </div>
    <form id="form-edit" class="px-6 py-5 space-y-4">
      <input type="hidden" id="edit-id">

      <div>
        <label class="f-label">Status <span class="text-red-400">*</span></label>
        <select name="status" id="edit-status" class="f-input">
          <option value="Aktif">Aktif</option>
          <option value="Dropout">Dropout</option>
          <option value="Lulus">Lulus</option>
        </select>
        <p class="f-error hidden" id="err-edit-status"></p>
      </div>

      <div>
        <label class="f-label">Nilai Akhir <span class="text-[11px] font-normal" style="color:var(--muted)">(0 – 100)</span></label>
        <div class="relative">
          <input type="number" name="nilai_akhir" id="edit-nilai" class="f-input pr-16"
            placeholder="Kosongkan jika belum ada nilai" min="0" max="100" step="0.01"
            oninput="updateGradePreview(this.value)">
          <span id="grade-preview" class="absolute right-3 top-1/2 -translate-y-1/2 grade-badge text-[11px]"
            style="display:none"></span>
        </div>
        <p class="f-error hidden" id="err-edit-nilai_akhir"></p>
      </div>

      <div>
        <label class="f-label">Catatan</label>
        <textarea name="catatan" id="edit-catatan" class="f-input" rows="2"
          placeholder="Catatan tambahan (opsional)"></textarea>
        <p class="f-error hidden" id="err-edit-catatan"></p>
      </div>

      <div class="flex gap-3 pt-2">
        <button type="button" onclick="closeModal('modal-edit')" class="flex-1 px-4 py-2 rounded-xl border text-[13px] font-semibold"
          style="border-color:var(--border);color:var(--sub)"
          onmouseover="this.style.background='var(--surface2)'" onmouseout="this.style.background='transparent'">Batal</button>
        <button type="submit" id="btn-edit" class="flex-1 px-4 py-2 rounded-xl text-[13px] font-semibold text-white a-grad shadow">
          <i class="fa-solid fa-floppy-disk mr-1.5 text-[11px]"></i>Simpan
        </button>
      </div>
    </form>
  </div>
</div>

{{-- MODAL HAPUS --}}
<div id="modal-delete" class="modal-backdrop">
  <div class="modal-box modal-sm">
    <div class="p-6 text-center">
      <div class="bg-rose-500/15 text-rose-400 w-14 h-14 rounded-2xl grid place-items-center text-2xl mx-auto mb-4"><i class="fa-solid fa-user-minus"></i></div>
      <h3 class="font-display font-bold text-[16px] mb-1" style="color:var(--text)">Keluarkan dari Kelas?</h3>
      <p class="text-[12.5px]" style="color:var(--muted)">
        <strong id="delete-nama" style="color:var(--text)"></strong> akan dikeluarkan dari kelas
        <strong id="delete-kelas" style="color:var(--text)"></strong>.
      </p>
    </div>
    <div class="flex gap-3 px-6 pb-6">
      <button onclick="closeModal('modal-delete')" class="flex-1 px-4 py-2 rounded-xl border text-[13px] font-semibold"
        style="border-color:var(--border);color:var(--sub)"
        onmouseover="this.style.background='var(--surface2)'" onmouseout="this.style.background='transparent'">Batal</button>
      <button id="btn-delete" onclick="doDelete()" class="flex-1 px-4 py-2 rounded-xl text-[13px] font-semibold text-white" style="background:#f87171">
        <i class="fa-solid fa-user-minus mr-1.5 text-[11px]"></i>Keluarkan
      </button>
    </div>
  </div>
</div>
@endsection

@push('scripts')
@include('admin.partials.datatable-scripts')
<script>
$(()=>$('#enrollment-table').DataTable({
  language:{...DT_LANG, searchPlaceholder:'Cari mahasiswa, kelas...'},
  columnDefs:[
    {orderable:false, targets:[0,8]},
    {className:'text-center', targets:[0,6]},
  ],
  pageLength:20, dom:DT_DOM,
}));

// ── Filter kelas by periode ──
document.getElementById('filter-periode')?.addEventListener('change', function(){
  const pid = this.value;
  const sel = document.getElementById('filter-kelas');
  sel.querySelectorAll('option').forEach(opt => {
    if (!opt.value) return;
    opt.style.display = (!pid || opt.dataset.periode === pid) ? '' : 'none';
  });
  sel.value = '';
  this.form.submit();
});

// ── Filter mahasiswa by jurusan + already-enrolled exclusion ──
const kelasData = @json($allKelas->map(fn($k) => [
  'id'         => $k->id,
  'jurusan_id' => $k->mataKuliah?->jurusan_id,
]));
const enrolledMap = @json($enrolledMap);

function filterMahasiswaByJurusan(kelasId){
  const kel = kelasData.find(k => k.id == kelasId);
  const jurusanId = kel ? kel.jurusan_id : null;
  const enrolledIds = kelasId ? (enrolledMap[kelasId] || []).map(Number) : [];
  const items = document.querySelectorAll('#create-mhs-list .mhs-item');
  let visible = 0;
  items.forEach(item => {
    const mhsId = parseInt(item.querySelector('input').value);
    const alreadyEnrolled = enrolledIds.includes(mhsId);
    const jurusanMatch = !jurusanId || !item.dataset.jurusan || item.dataset.jurusan == jurusanId;
    const hide = !jurusanMatch || alreadyEnrolled;
    item.classList.toggle('hidden-opt', hide);
    if (!hide) visible++;
  });
  document.getElementById('create-mhs-hint').textContent =
    kelasId ? `Menampilkan ${visible} mahasiswa tersedia (belum terdaftar)` : 'Pilih kelas dulu untuk filter';
  document.getElementById('create-mhs-count').textContent = `${visible} mahasiswa tersedia`;
  // uncheck hidden ones
  items.forEach(item => {
    if (item.classList.contains('hidden-opt')) item.querySelector('input').checked = false;
  });
  document.getElementById('create-mhs-search').value = '';
}

function searchMahasiswa(prefix, q){
  q = q.toLowerCase();
  document.querySelectorAll(`#${prefix}-mhs-list .mhs-item`).forEach(item => {
    if (item.classList.contains('hidden-opt')) return;
    const match = item.dataset.name.includes(q) || item.dataset.nim.includes(q);
    item.style.display = match ? '' : 'none';
  });
}

// ── Grade preview in edit modal ──
const GRADE_COLORS = {
  A:{'bg':'rgba(16,185,129,.15)','color':'#34d399'},
  B:{'bg':'rgba(59,130,246,.15)','color':'#60a5fa'},
  C:{'bg':'rgba(245,158,11,.15)','color':'#fbbf24'},
  D:{'bg':'rgba(249,115,22,.15)','color':'#fb923c'},
  E:{'bg':'rgba(239,68,68,.15)','color':'#f87171'},
};
function getGrade(n){ if(n>=85)return'A';if(n>=75)return'B';if(n>=65)return'C';if(n>=55)return'D';return'E'; }
function updateGradePreview(val){
  const el = document.getElementById('grade-preview');
  if(val === '' || val === null){ el.style.display='none'; return; }
  const g = getGrade(parseFloat(val));
  const c = GRADE_COLORS[g];
  el.textContent = g;
  el.style.background = c.bg;
  el.style.color = c.color;
  el.style.display = 'inline-flex';
}

// ── CREATE (multiple mahasiswa) ──
document.getElementById('form-create').addEventListener('submit', function(e){
  e.preventDefault();
  clearErrors('create');
  const kelasId   = document.getElementById('create-kelas').value;
  const checkedCbs = [...document.querySelectorAll('.create-mhs-cb:checked')];

  if (!kelasId) { showFieldError('create-kelas_id','Pilih kelas terlebih dahulu.'); return; }
  if (checkedCbs.length === 0) { showFieldError('create-mahasiswa_id','Pilih minimal 1 mahasiswa.'); return; }

  setLoading('btn-create', true);

  // Enroll satu per satu secara sequential
  const status     = document.querySelector('#form-create select[name=status]').value;
  const enrolledAt = document.getElementById('create-enrolled-at').value;
  const total      = checkedCbs.length;
  let done = 0, failed = 0;

  const doNext = (idx) => {
    if (idx >= checkedCbs.length) {
      setLoading('btn-create', false);
      closeModal('modal-create');
      if (failed === 0) {
        showToast('success', `${done} mahasiswa berhasil didaftarkan.`);
      } else {
        showToast('error', `${done} berhasil, ${failed} gagal (mungkin sudah terdaftar).`);
      }
      setTimeout(() => location.reload(), 1400);
      return;
    }
    const body = new URLSearchParams();
    body.append('kelas_id', kelasId);
    body.append('mahasiswa_id', checkedCbs[idx].value);
    body.append('status', status);
    body.append('enrolled_at', enrolledAt);

    fetch('/admin/enrollment', {
      method:'POST',
      headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'},
      body,
    })
    .then(async r => { if(r.ok) done++; else failed++; })
    .catch(() => failed++)
    .finally(() => doNext(idx + 1));
  };
  doNext(0);
});

function showFieldError(id, msg){
  const el = document.getElementById(`err-${id}`);
  if(el){ el.textContent=msg; el.classList.remove('hidden'); }
}

// ── EDIT ──
let deleteId = null;
function openEdit(id, nama, kelas, status, nilai, catatan){
  document.getElementById('edit-id').value = id;
  document.getElementById('edit-subtitle').textContent = `${nama} · Kelas ${kelas}`;
  document.getElementById('edit-status').value = status;
  document.getElementById('edit-nilai').value = nilai ?? '';
  document.getElementById('edit-catatan').value = catatan || '';
  updateGradePreview(nilai);
  clearErrors('edit');
  openModal('modal-edit');
}

document.getElementById('form-edit').addEventListener('submit', function(e){
  e.preventDefault();
  clearErrors('edit');
  const id = document.getElementById('edit-id').value;
  setLoading('btn-edit', true);
  const body = new URLSearchParams({_method:'PUT'});
  body.append('status', document.getElementById('edit-status').value);
  body.append('nilai_akhir', document.getElementById('edit-nilai').value);
  body.append('catatan', document.getElementById('edit-catatan').value);

  fetch(`/admin/enrollment/${id}`, {
    method:'POST',
    headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'},
    body,
  })
  .then(async r=>({ok:r.ok,status:r.status,data:await r.json()}))
  .then(({ok,status,data})=>{
    if(!ok&&status===422){showErrors('edit',data.errors);return;}
    closeModal('modal-edit');
    showToast(ok?'success':'error',data.message);
    if(ok) setTimeout(()=>location.reload(),1200);
  })
  .catch(()=>showToast('error','Gagal terhubung ke server.'))
  .finally(()=>setLoading('btn-edit',false));
});

// ── DELETE ──
function openDelete(id, nama, kelas){
  deleteId = id;
  document.getElementById('delete-nama').textContent = nama;
  document.getElementById('delete-kelas').textContent = kelas;
  openModal('modal-delete');
}
function doDelete(){
  setLoading('btn-delete', true);
  fetch(`/admin/enrollment/${deleteId}`, {
    method:'POST',
    headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'},
    body: new URLSearchParams({_method:'DELETE'}),
  })
  .then(async r=>({ok:r.ok,data:await r.json()}))
  .then(({ok,data})=>{
    closeModal('modal-delete');
    showToast(ok?'success':'error',data.message);
    if(ok) setTimeout(()=>location.reload(),1200);
  })
  .catch(()=>showToast('error','Gagal terhubung ke server.'))
  .finally(()=>setLoading('btn-delete',false));
}

function clearErrors(prefix){
  ['kelas_id','mahasiswa_id','status','nilai_akhir','catatan','enrolled_at'].forEach(f=>{
    const el=document.getElementById(`err-${prefix}-${f}`);
    if(el){el.textContent='';el.classList.add('hidden');}
  });
}

@if(session('success'))document.addEventListener('DOMContentLoaded',()=>showToast('success','{{ session("success") }}'));@endif
@if(session('error'))document.addEventListener('DOMContentLoaded',()=>showToast('error','{{ session("error") }}'));@endif
</script>
@endpush
