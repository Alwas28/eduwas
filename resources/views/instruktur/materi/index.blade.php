@extends('layouts.instruktur')
@section('title', 'Materi Ajar')
@section('page-title', 'Materi Ajar')

@push('styles')
<style>
/* ── Two-panel layout ── */
.materi-layout { display:flex; gap:0; height:calc(100vh - 130px); overflow:hidden; border-radius:18px; border:1px solid var(--border); background:var(--surface); }
.materi-sidebar { width:260px; flex-shrink:0; border-right:1px solid var(--border); display:flex; flex-direction:column; }
.materi-main   { flex:1; overflow-y:auto; display:flex; flex-direction:column; }

/* ── MK list ── */
.kelas-list { flex:1; overflow-y:auto; padding:10px; }
.kelas-item { display:flex; align-items:center; gap:10px; padding:9px 11px; border-radius:11px; cursor:pointer; transition:background .15s; border:1px solid transparent; text-decoration:none; margin-bottom:4px; }
.kelas-item:hover  { background:var(--surface2); }
.kelas-item.active { background:var(--ac-lt); border-color:rgba(var(--ac-rgb),.35); }
.kelas-item.active .ki-kode { color:var(--ac); }
.ki-kode { font-weight:700; font-size:13px; color:var(--text); }
.ki-nama { font-size:11px; color:var(--muted); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.ki-badge { font-size:10px; font-weight:600; padding:2px 7px; border-radius:20px; white-space:nowrap; }

/* ── Pokok Bahasan row ── */
.pb-item { border:1px solid var(--border); border-radius:14px; margin-bottom:8px; overflow:hidden; transition:border-color .15s; }
.pb-item:hover { border-color:rgba(var(--ac-rgb),.35); }
.pb-header { display:flex; align-items:center; gap:10px; padding:11px 14px; }
.pb-num { width:32px; height:32px; border-radius:10px; display:grid; place-items:center; font-size:12px; font-weight:700; background:var(--ac-lt); color:var(--ac); flex-shrink:0; }

/* ── Materi row ── */
.materi-row { display:flex; align-items:center; gap:10px; padding:10px 16px; border-bottom:1px solid var(--border); transition:background .12s; }
.materi-row:last-child { border-bottom:none; }
.materi-row:hover { background:var(--surface2); }
.materi-tipe-icon { width:30px; height:30px; border-radius:9px; display:grid; place-items:center; font-size:12px; flex-shrink:0; }

/* ── Toggle switch ── */
.toggle-switch { position:relative; display:inline-block; width:38px; height:22px; flex-shrink:0; }
.toggle-switch input { opacity:0; width:0; height:0; position:absolute; }
.toggle-track { position:absolute; inset:0; border-radius:11px; background:var(--border); transition:background .2s; cursor:pointer; }
.toggle-thumb { position:absolute; width:16px; height:16px; top:3px; left:3px; border-radius:50%; background:#fff; transition:transform .2s; pointer-events:none; }
.toggle-switch input:checked ~ .toggle-track { background:var(--ac); }
.toggle-switch input:checked ~ .toggle-track .toggle-thumb { transform:translateX(16px); }
.toggle-switch input:disabled ~ .toggle-track { opacity:.5; cursor:not-allowed; }

/* ── RPS card ── */
.rps-card { background:var(--surface2); border-radius:14px; border:1px solid var(--border); padding:14px 16px; display:flex; align-items:center; gap:12px; }

/* ── File drop zone ── */
.drop-zone { border:2px dashed var(--border); border-radius:12px; transition:border-color .2s, background .2s; }
.drop-zone.drag-over { border-color:var(--ac); background:var(--ac-lt2); }

/* ── Tipe colors ── */
.tc-dokumen { background:rgba(59,130,246,.15);  color:#60a5fa; }
.tc-video   { background:rgba(244,63,94,.15);   color:#fb7185; }
.tc-link    { background:rgba(139,92,246,.15);  color:#a78bfa; }
.tc-teks    { background:rgba(245,158,11,.15);  color:#fbbf24; }

/* ── Modal utilities ── */
.modal-header { display:flex; align-items:center; justify-content:space-between; padding:18px 20px 14px; border-bottom:1px solid var(--border); }
.modal-title  { font-family:'Clash Display',sans-serif; font-weight:700; font-size:16px; color:var(--text); }
.modal-close  { width:30px; height:30px; border-radius:8px; border:none; background:var(--surface2); color:var(--muted); cursor:pointer; display:grid; place-items:center; transition:opacity .15s; }
.modal-close:hover { opacity:.75; }
.modal-body   { padding:18px 20px; }
.modal-footer { display:flex; justify-content:flex-end; gap:8px; padding:14px 20px 18px; border-top:1px solid var(--border); }
.btn-primary  { padding:8px 18px; border-radius:10px; font-size:13px; font-weight:600; color:#fff; border:none; cursor:pointer; transition:opacity .15s; }
.btn-primary  { background:var(--ac); }
.btn-primary:hover { opacity:.85; }
.btn-primary:disabled { opacity:.5; cursor:not-allowed; }
.btn-ghost    { padding:8px 14px; border-radius:10px; font-size:13px; font-weight:600; background:var(--surface2); color:var(--muted); border:none; cursor:pointer; transition:opacity .15s; }
.btn-ghost:hover { opacity:.75; }
.field-label  { display:block; font-size:11.5px; font-weight:600; color:var(--muted); margin-bottom:5px; }
.field-input  { width:100%; padding:8px 12px; border-radius:10px; border:1px solid var(--border); background:var(--surface2); color:var(--text); font-size:13px; outline:none; transition:border-color .15s; }
.field-input:focus { border-color:var(--ac); }
select.field-input { cursor:pointer; }

/* ── Scrollbar thin ── */
.materi-main::-webkit-scrollbar,
.kelas-list::-webkit-scrollbar { width:4px; }
.materi-main::-webkit-scrollbar-track,
.kelas-list::-webkit-scrollbar-track { background:transparent; }
.materi-main::-webkit-scrollbar-thumb,
.kelas-list::-webkit-scrollbar-thumb { background:var(--border); border-radius:2px; }

/* ── Stat chips ── */
.stat-chip { display:flex; align-items:center; gap:6px; padding:5px 12px; border-radius:10px; font-size:12px; font-weight:600; }

/* ── Skeleton empty ── */
.empty-pb { display:flex; flex-direction:column; align-items:center; justify-content:center; padding:60px 20px; gap:12px; }

/* ── Mobile: horizontal MK tabs ── */
@media (max-width: 767px) {
  .materi-layout {
    flex-direction: column;
    height: auto !important;
    overflow: visible;
    border-radius: 14px;
  }
  .materi-main {
    overflow-y: visible;
    height: auto;
  }
  .materi-sidebar {
    width: 100%;
    border-right: none;
    border-bottom: 1px solid var(--border);
    flex-direction: column;
    max-height: none;
  }
  .sidebar-label { display: none; } /* hide "Mata Kuliah" heading */
  .kelas-list {
    display: flex;
    flex-direction: row;
    overflow-x: auto;
    overflow-y: hidden;
    padding: 6px 8px;
    gap: 6px;
    scrollbar-width: none;
    -webkit-overflow-scrolling: touch;
  }
  .kelas-list::-webkit-scrollbar { display: none; }
  .kelas-item {
    flex-shrink: 0;
    flex-direction: row;
    align-items: center;
    margin-bottom: 0;
    padding: 5px 10px;
    gap: 6px;
    border-radius: 10px;
    white-space: nowrap;
  }
  .kelas-item .w-8.h-8 { width: 22px; height: 22px; font-size: 8px; flex-shrink: 0; }
  .ki-kode { font-size: 11px; }
  .ki-nama { display: none; }
  .kelas-item .min-w-0 { flex: none; min-width: 0; }
}
@media (min-width: 768px) {
  .mob-hint-text { display: none; }
}
</style>
@endpush

@section('content')
<div class="space-y-4 animate-fadeUp">

  {{-- ── HEADER & STATS ──────────────────────────────────────── --}}
  <div class="flex flex-wrap items-center justify-between gap-3">
    <div>
      <h2 class="font-display font-bold text-[20px]" style="color:var(--text)">Materi Ajar</h2>
      <p class="text-[13px] mt-0.5" style="color:var(--muted)">Kelola bahan ajar setiap kelas</p>
    </div>
    <div class="flex items-center gap-2 flex-wrap">
      @php
        $chips = [
          ['fa-layer-group',  $stats['total'],   'a-bg-lt a-text',                    'Total'],
          ['fa-circle-check', $stats['aktif'],   'bg-emerald-500/15 text-emerald-400','Aktif'],
          ['fa-pencil',       $stats['draft'],   'bg-amber-500/15 text-amber-400',    'Draft'],
          ['fa-file-lines',   $stats['dokumen'], 'bg-blue-500/15 text-blue-400',      'Dok'],
        ];
      @endphp
      @foreach($chips as [$ic,$vl,$cls,$lb])
      <div class="stat-chip {{ $cls }}" style="border:1px solid var(--border)">
        <i class="fa-solid {{ $ic }} text-[11px]"></i>
        <span>{{ $vl }} {{ $lb }}</span>
      </div>
      @endforeach
    </div>
  </div>

  {{-- ── TWO-PANEL LAYOUT ─────────────────────────────────────── --}}
  <div class="materi-layout">

    {{-- LEFT: mata kuliah list --}}
    <div class="materi-sidebar">
      <div class="sidebar-label px-4 py-3 border-b" style="border-color:var(--border)">
        <p class="text-[11px] font-semibold uppercase tracking-widest" style="color:var(--muted)">Mata Kuliah</p>
      </div>
      <div class="kelas-list">
        @forelse($mataKuliahList as $mk)
          @php $isActive = $selectedMk?->id === $mk->id; @endphp
          <a href="{{ route('instruktur.materi.index', ['mk_id' => $mk->id]) }}"
             class="kelas-item {{ $isActive ? 'active' : '' }}">
            <div class="w-8 h-8 rounded-lg grid place-items-center flex-shrink-0 text-[11px] font-bold a-bg-lt a-text">
              {{ substr($mk->kode ?? '?', 0, 3) }}
            </div>
            <div class="min-w-0 flex-1">
              <div class="ki-kode">{{ $mk->kode }}</div>
              <div class="ki-nama">{{ $mk->nama }}</div>
            </div>
          </a>
        @empty
          <div class="text-center py-8 text-[12px]" style="color:var(--muted)">
            <i class="fa-solid fa-inbox text-2xl mb-2 block"></i>
            Belum ada mata kuliah
          </div>
        @endforelse
      </div>
    </div>

    {{-- RIGHT: content --}}
    <div class="materi-main">
      @if($selectedMk)

        {{-- Top bar --}}
        <div class="px-5 py-3 border-b flex items-center justify-between gap-3 flex-wrap" style="border-color:var(--border);background:var(--surface)">
          <div class="min-w-0">
            <span class="font-display font-bold text-[15px]" style="color:var(--text)">{{ $selectedMk->kode }}</span>
            <span class="text-[12px] ml-2" style="color:var(--muted)">{{ $selectedMk->nama }}</span>
            <span class="text-[11px] ml-1 px-2 py-0.5 rounded-full bg-blue-500/15 text-blue-400 font-semibold">{{ $selectedMk->sks }} SKS</span>
          </div>
          <button onclick="openModalPB()"
                  class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-[12px] font-semibold a-bg-lt a-text border hover:opacity-80 transition-opacity"
                  style="border-color:rgba(var(--ac-rgb),.35)">
            <i class="fa-solid fa-plus text-[10px]"></i>
            Tambah Pertemuan
          </button>
        </div>

        <div class="p-5 space-y-4">

          {{-- RPS section: one row per kelas --}}
          @foreach($kelasList as $k)
          <div class="rps-card">
            <div class="w-9 h-9 rounded-xl grid place-items-center flex-shrink-0 bg-violet-500/15 text-violet-400 text-[14px]">
              <i class="fa-solid fa-scroll"></i>
            </div>
            <div class="flex-1 min-w-0">
              <div class="flex items-center gap-2">
                <div class="text-[13px] font-semibold" style="color:var(--text)">RPS</div>
                <span class="text-[10px] px-2 py-0.5 rounded-full bg-amber-500/15 text-amber-400 font-semibold">{{ $k->periodeAkademik?->nama ?? $k->kode_display }}</span>
              </div>
              @if($k->rps_path)
                <div class="text-[11px] mt-0.5" style="color:var(--muted)">
                  {{ $k->rps_nama_file }} &bull; {{ $k->rpsUkuranHuman() }}
                </div>
              @else
                <div class="text-[11px] mt-0.5" style="color:var(--muted)">Belum ada RPS &mdash; opsional</div>
              @endif
            </div>
            <div class="flex items-center gap-2 flex-shrink-0">
              @if($k->rps_path)
                <a href="{{ $k->rpsUrl() }}" target="_blank"
                   class="px-3 py-1.5 rounded-lg text-[11px] font-semibold bg-violet-500/15 text-violet-400 hover:opacity-80 transition-opacity">
                  <i class="fa-solid fa-eye mr-1"></i>Lihat
                </a>
                <button onclick="deleteRps({{ $k->id }})"
                        class="px-3 py-1.5 rounded-lg text-[11px] font-semibold bg-red-500/10 text-red-400 hover:opacity-80 transition-opacity">
                  <i class="fa-solid fa-trash mr-1"></i>Hapus
                </button>
              @endif
              <label class="px-3 py-1.5 rounded-lg text-[11px] font-semibold border cursor-pointer hover:opacity-80 transition-opacity a-text" style="border-color:rgba(var(--ac-rgb),.35);background:var(--ac-lt)">
                <i class="fa-solid fa-upload mr-1"></i>{{ $k->rps_path ? 'Ganti' : 'Upload RPS' }}
                <input type="file" class="hidden" accept=".pdf,.doc,.docx"
                       onchange="uploadRps({{ $k->id }}, this)">
              </label>
            </div>
          </div>
          @endforeach

          {{-- Pokok Bahasan list --}}
          @if($pokokBahasanList->isEmpty())
            <div class="empty-pb" style="color:var(--muted)">
              <div class="w-14 h-14 rounded-2xl grid place-items-center a-bg-lt a-text text-[22px] mx-auto">
                <i class="fa-solid fa-list-ul"></i>
              </div>
              <div class="text-[14px] font-semibold" style="color:var(--text)">Belum ada Pokok Bahasan</div>
              <p class="text-[12px] text-center max-w-xs">Tambahkan pokok bahasan untuk setiap pertemuan, lalu isi dengan materi ajar.</p>
              <button onclick="openModalPB()"
                      class="mt-1 px-5 py-2 rounded-xl text-[12px] font-semibold text-white transition-opacity hover:opacity-85"
                      style="background:var(--ac)">
                <i class="fa-solid fa-plus mr-1.5"></i>Tambah Pertemuan
              </button>
            </div>
          @else
            <div id="pb-list" class="space-y-2.5">
              @foreach($pokokBahasanList as $pb)
              <div class="pb-item" id="pb-{{ $pb->id }}">
                <div class="pb-header">
                  <div class="pb-num">{{ $pb->pertemuan }}</div>
                  <div class="flex-1 min-w-0">
                    <div class="text-[13px] font-semibold" style="color:var(--text)">{{ $pb->judul }}</div>
                    @if($pb->deskripsi)
                      <div class="text-[11px] mt-0.5 truncate" style="color:var(--muted)">{{ $pb->deskripsi }}</div>
                    @endif
                  </div>
                  <div class="flex items-center gap-1.5 flex-shrink-0">
                    <a href="{{ route('instruktur.pokok-bahasan.materi', $pb->id) }}"
                       class="w-7 h-7 rounded-lg grid place-items-center text-[11px] a-bg-lt a-text hover:opacity-75 transition-opacity"
                       title="Kelola Materi">
                      <i class="fa-solid fa-folder-open"></i>
                    </a>
                    <button onclick="openEditPB({{ $pb->id }}, '{{ addslashes($pb->judul) }}', {{ $pb->pertemuan }}, '{{ addslashes($pb->deskripsi ?? '') }}', {{ $pb->urutan }})"
                            class="w-7 h-7 rounded-lg grid place-items-center text-[11px] hover:opacity-75 transition-opacity"
                            style="background:var(--surface2);color:var(--muted)" title="Edit">
                      <i class="fa-solid fa-pen"></i>
                    </button>
                    <button onclick="deletePB({{ $pb->id }}, '{{ addslashes($pb->judul) }}')"
                            class="w-7 h-7 rounded-lg grid place-items-center text-[11px] hover:opacity-75 transition-opacity bg-red-500/10 text-red-400"
                            title="Hapus">
                      <i class="fa-solid fa-trash"></i>
                    </button>
                  </div>
                </div>
              </div>
              @endforeach
            </div>

            <div class="flex justify-center pt-2">
              <button onclick="openModalPB()"
                      class="flex items-center gap-2 px-4 py-2 rounded-xl text-[12px] font-semibold border hover:opacity-80 transition-opacity"
                      style="border-color:var(--border);color:var(--muted)">
                <i class="fa-solid fa-plus text-[10px]"></i>
                Tambah Pertemuan
              </button>
            </div>
          @endif

        </div>
      @else
        <div class="empty-pb flex-1" style="color:var(--muted)">
          <div class="w-14 h-14 rounded-2xl grid place-items-center a-bg-lt a-text text-[22px] mx-auto">
            <i class="fa-solid fa-book-open"></i>
          </div>
          <div class="text-[14px] font-semibold" style="color:var(--text)">Pilih Mata Kuliah</div>
          <p class="text-[12px] text-center">Pilih mata kuliah dari daftar di sebelah kiri untuk mengelola materi ajar.</p>
        </div>
      @endif
    </div>

  </div>{{-- end materi-layout --}}

</div>{{-- end space-y-4 --}}

{{-- ════════════════════════════════════════════════════════ --}}
{{--  MODALS                                                  --}}
{{-- ════════════════════════════════════════════════════════ --}}

{{-- Modal: Create Pokok Bahasan --}}
<div id="modal-pb-create" class="modal-backdrop">
  <div class="modal-box" style="max-width:460px">
    <div class="modal-header">
      <h3 class="modal-title">Tambah Pertemuan</h3>
      <button onclick="closeModal('modal-pb-create')" class="modal-close"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <form id="form-pb-create" onsubmit="submitPBCreate(event)">
      <div class="modal-body space-y-3">
        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="field-label">Pertemuan Ke-</label>
            <input type="number" id="pb-create-pertemuan" name="pertemuan" min="1" max="99"
                   value="{{ $nextPertemuan }}"
                   class="field-input" required>
          </div>
          <div>
            <label class="field-label">Urutan</label>
            <input type="number" id="pb-create-urutan" name="urutan" min="0" max="999"
                   placeholder="Auto"
                   class="field-input">
          </div>
        </div>
        <div>
          <label class="field-label">Judul Pokok Bahasan <span class="text-red-400">*</span></label>
          <input type="text" id="pb-create-judul" name="judul" maxlength="200"
                 placeholder="cth. Pengantar Pemrograman Berorientasi Objek"
                 class="field-input" required>
        </div>
        <div>
          <label class="field-label">Deskripsi <span style="color:var(--muted)" class="text-[11px]">opsional</span></label>
          <textarea id="pb-create-deskripsi" name="deskripsi" rows="2" maxlength="500"
                    placeholder="Ringkasan singkat topik pertemuan..."
                    class="field-input resize-none"></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" onclick="closeModal('modal-pb-create')" class="btn-ghost">Batal</button>
        <button type="submit" class="btn-primary" id="btn-pb-create">
          <span id="btn-pb-create-text"><i class="fa-solid fa-plus mr-1.5"></i>Tambah</span>
          <span id="btn-pb-create-spin" class="hidden"><i class="fa-solid fa-spinner fa-spin mr-1.5"></i>Menyimpan...</span>
        </button>
      </div>
    </form>
  </div>
</div>

{{-- Modal: Edit Pokok Bahasan --}}
<div id="modal-pb-edit" class="modal-backdrop">
  <div class="modal-box" style="max-width:460px">
    <div class="modal-header">
      <h3 class="modal-title">Edit Pokok Bahasan</h3>
      <button onclick="closeModal('modal-pb-edit')" class="modal-close"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <form id="form-pb-edit" onsubmit="submitPBEdit(event)">
      <input type="hidden" id="pb-edit-id">
      <div class="modal-body space-y-3">
        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="field-label">Pertemuan Ke-</label>
            <input type="number" id="pb-edit-pertemuan" name="pertemuan" min="1" max="99"
                   class="field-input" required>
          </div>
          <div>
            <label class="field-label">Urutan</label>
            <input type="number" id="pb-edit-urutan" name="urutan" min="0" max="999"
                   class="field-input">
          </div>
        </div>
        <div>
          <label class="field-label">Judul Pokok Bahasan <span class="text-red-400">*</span></label>
          <input type="text" id="pb-edit-judul" name="judul" maxlength="200"
                 class="field-input" required>
        </div>
        <div>
          <label class="field-label">Deskripsi</label>
          <textarea id="pb-edit-deskripsi" name="deskripsi" rows="2" maxlength="500"
                    class="field-input resize-none"></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" onclick="closeModal('modal-pb-edit')" class="btn-ghost">Batal</button>
        <button type="submit" class="btn-primary" id="btn-pb-edit">
          <span id="btn-pb-edit-text"><i class="fa-solid fa-floppy-disk mr-1.5"></i>Simpan</span>
          <span id="btn-pb-edit-spin" class="hidden"><i class="fa-solid fa-spinner fa-spin mr-1.5"></i>Menyimpan...</span>
        </button>
      </div>
    </form>
  </div>
</div>


@endsection

@push('scripts')
<script>
const CSRF    = document.querySelector('meta[name="csrf-token"]').content;
const ROUTES  = {
  pbStore:   '{{ route("instruktur.pokok-bahasan.store") }}',
  pbUpdate:  id => `{{ url("instruktur/pokok-bahasan") }}/${id}`,
  pbDestroy: id => `{{ url("instruktur/pokok-bahasan") }}/${id}`,
  rpsUpload: id => `{{ url("instruktur/rps") }}/${id}`,
  rpsDelete: id => `{{ url("instruktur/rps") }}/${id}`,
};

/* ── Modal helpers ── */
function openModal(id) { document.getElementById(id).classList.add('open'); }
function closeModal(id){ document.getElementById(id).classList.remove('open'); }

/* ── Spinner helpers ── */
function setLoading(btnId, loading) {
  document.getElementById(btnId+'-text').classList.toggle('hidden', loading);
  document.getElementById(btnId+'-spin').classList.toggle('hidden', !loading);
  document.getElementById(btnId).disabled = loading;
}

/* ════ POKOK BAHASAN ════ */

function openModalPB() {
  @if($selectedMk)
  document.getElementById('pb-create-pertemuan').value = {{ $nextPertemuan }};
  document.getElementById('pb-create-judul').value = '';
  document.getElementById('pb-create-deskripsi').value = '';
  document.getElementById('pb-create-urutan').value = '';
  openModal('modal-pb-create');
  setTimeout(()=>document.getElementById('pb-create-judul').focus(), 100);
  @else
  showToast('error','Pilih mata kuliah terlebih dahulu.');
  @endif
}

function openEditPB(id, judul, pertemuan, deskripsi, urutan) {
  document.getElementById('pb-edit-id').value = id;
  document.getElementById('pb-edit-judul').value = judul;
  document.getElementById('pb-edit-pertemuan').value = pertemuan;
  document.getElementById('pb-edit-deskripsi').value = deskripsi;
  document.getElementById('pb-edit-urutan').value = urutan;
  openModal('modal-pb-edit');
}

async function submitPBCreate(e) {
  e.preventDefault();
  setLoading('btn-pb-create', true);
  const fd = new FormData(e.target);
  fd.append('mata_kuliah_id', {{ $selectedMk?->id ?? 0 }});
  try {
    const r = await fetch(ROUTES.pbStore, {
      method: 'POST',
      headers: {'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json'},
      body: fd,
    });
    const j = await r.json();
    if (!r.ok) { showToast('error', j.message || 'Gagal menyimpan.'); return; }
    showToast('success', j.message);
    closeModal('modal-pb-create');
    location.reload();
  } catch { showToast('error','Terjadi kesalahan.'); }
  finally { setLoading('btn-pb-create', false); }
}

async function submitPBEdit(e) {
  e.preventDefault();
  setLoading('btn-pb-edit', true);
  const id = document.getElementById('pb-edit-id').value;
  const fd = new FormData(e.target);
  fd.append('_method', 'PUT');
  try {
    const r = await fetch(ROUTES.pbUpdate(id), {
      method: 'POST',
      headers: {'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json'},
      body: fd,
    });
    const j = await r.json();
    if (!r.ok) { showToast('error', j.message || 'Gagal menyimpan.'); return; }
    showToast('success', j.message);
    closeModal('modal-pb-edit');
    location.reload();
  } catch { showToast('error','Terjadi kesalahan.'); }
  finally { setLoading('btn-pb-edit', false); }
}

async function deletePB(id, judul) {
  if (!confirm(`Hapus pokok bahasan "${judul}"?\n\nSemua materi di dalamnya harus dihapus dulu.`)) return;
  try {
    const fd = new FormData();
    fd.append('_method', 'DELETE');
    const r = await fetch(ROUTES.pbDestroy(id), {
      method: 'POST',
      headers: {'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json'},
      body: fd,
    });
    const j = await r.json();
    if (!r.ok) { showToast('error', j.message || 'Gagal menghapus.'); return; }
    showToast('success', j.message);
    document.getElementById('pb-'+id)?.remove();
  } catch { showToast('error','Terjadi kesalahan.'); }
}


/* ════ RPS ════ */
async function uploadRps(kelasId, input) {
  if (!input.files[0]) return;
  const fd = new FormData();
  fd.append('rps', input.files[0]);
  const btn = input.closest('label');
  btn.style.opacity = '.5';
  btn.style.pointerEvents = 'none';
  try {
    const r = await fetch(ROUTES.rpsUpload(kelasId), {
      method: 'POST',
      headers: {'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json'},
      body: fd,
    });
    const j = await r.json();
    if (!r.ok) { showToast('error', j.message || 'Gagal upload RPS.'); return; }
    showToast('success', j.message);
    location.reload();
  } catch { showToast('error','Terjadi kesalahan.'); }
  finally { btn.style.opacity=''; btn.style.pointerEvents=''; input.value=''; }
}

async function deleteRps(kelasId) {
  if (!confirm('Hapus file RPS?')) return;
  try {
    const fd = new FormData();
    fd.append('_method', 'DELETE');
    const r = await fetch(ROUTES.rpsDelete(kelasId), {
      method: 'POST',
      headers: {'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json'},
      body: fd,
    });
    const j = await r.json();
    if (!r.ok) { showToast('error', j.message || 'Gagal.'); return; }
    showToast('success', j.message);
    location.reload();
  } catch { showToast('error','Terjadi kesalahan.'); }
}

/* ── Close modal on backdrop click ── */
document.querySelectorAll('.modal-backdrop').forEach(el => {
  el.addEventListener('click', e => { if (e.target === el) el.classList.remove('open'); });
});
</script>
@endpush
