@extends('layouts.instruktur')
@section('title', 'Tugas')
@section('page-title', 'Tugas')

@push('styles')
<style>
/* ── Two-panel layout (same as materi) ── */
.materi-layout  { display:flex; gap:0; height:calc(100vh - 130px); overflow:hidden; border-radius:18px; border:1px solid var(--border); background:var(--surface); }
.materi-sidebar { width:260px; flex-shrink:0; border-right:1px solid var(--border); display:flex; flex-direction:column; }
.materi-main    { flex:1; overflow-y:auto; display:flex; flex-direction:column; }

/* ── Kelas list ── */
.kelas-list { flex:1; overflow-y:auto; padding:10px; }
.kelas-item { display:flex; align-items:center; gap:10px; padding:9px 11px; border-radius:11px; cursor:pointer; transition:background .15s; border:1px solid transparent; text-decoration:none; margin-bottom:4px; }
.kelas-item:hover  { background:var(--surface2); }
.kelas-item.active { background:var(--ac-lt); border-color:rgba(var(--ac-rgb),.35); }
.kelas-item.active .ki-kode { color:var(--ac); }
.ki-kode { font-weight:700; font-size:13px; color:var(--text); }
.ki-nama { font-size:11px; color:var(--muted); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }

/* ── Tabs ── */
.tab-bar  { display:flex; gap:2px; padding:10px 16px 0; border-bottom:1px solid var(--border); }
.tab-item { padding:8px 14px; font-size:12.5px; font-weight:600; color:var(--muted); border-bottom:2px solid transparent; cursor:pointer; transition:color .15s,border-color .15s; white-space:nowrap; }
.tab-item:hover  { color:var(--text); }
.tab-item.active { color:var(--ac); border-bottom-color:var(--ac); }

/* ── Accordion tugas card ── */
.tugas-card { border:1px solid var(--border); border-radius:14px; margin-bottom:10px; overflow:hidden; transition:border-color .15s; }
.tugas-card:hover { border-color:rgba(var(--ac-rgb),.3); }
.tugas-head { display:flex; align-items:center; gap:10px; padding:12px 14px; cursor:pointer; user-select:none; }
.tugas-head:hover { background:var(--surface2); }
.tugas-body { display:none; border-top:1px solid var(--border); padding:14px; }
.tugas-body.open { display:block; }

/* ── Status badges ── */
.badge-draft   { background:rgba(100,116,139,.15); color:#94a3b8; }
.badge-aktif   { background:rgba(16,185,129,.15);  color:#34d399; }
.badge-selesai { background:rgba(59,130,246,.15);  color:#60a5fa; }
.badge-overdue { background:rgba(244,63,94,.15);   color:#fb7185; }

/* ── Kelompok cards ── */
.kelompok-grid  { display:grid; grid-template-columns:repeat(auto-fill,minmax(200px,1fr)); gap:10px; }
.kelompok-card  { border:1px solid var(--border); border-radius:14px; padding:12px; display:flex; flex-direction:column; gap:8px; transition:border-color .15s; }
.kelompok-card:hover { border-color:rgba(var(--ac-rgb),.3); }

/* ── Avatar mini ── */
.av-mini { width:22px; height:22px; border-radius:7px; display:grid; place-items:center; font-size:9px; font-weight:700; flex-shrink:0; }

/* ── Modal utilities ── */
.modal-header { display:flex; align-items:center; justify-content:space-between; padding:18px 20px 14px; border-bottom:1px solid var(--border); }
.modal-title  { font-family:'Clash Display',sans-serif; font-weight:700; font-size:16px; color:var(--text); }
.modal-close  { width:30px; height:30px; border-radius:8px; border:none; background:var(--surface2); color:var(--muted); cursor:pointer; display:grid; place-items:center; transition:opacity .15s; }
.modal-close:hover { opacity:.75; }
.modal-body   { padding:18px 20px; }
.modal-footer { display:flex; justify-content:flex-end; gap:8px; padding:14px 20px 18px; border-top:1px solid var(--border); }
.btn-primary  { padding:8px 18px; border-radius:10px; font-size:13px; font-weight:600; color:#fff; border:none; cursor:pointer; transition:opacity .15s; background:var(--ac); }
.btn-primary:hover { opacity:.85; }
.btn-primary:disabled { opacity:.5; cursor:not-allowed; }
.btn-ghost    { padding:8px 14px; border-radius:10px; font-size:13px; font-weight:600; background:var(--surface2); color:var(--muted); border:none; cursor:pointer; transition:opacity .15s; }
.btn-ghost:hover { opacity:.75; }
.field-label  { display:block; font-size:11.5px; font-weight:600; color:var(--muted); margin-bottom:5px; }
.field-input  { width:100%; padding:8px 12px; border-radius:10px; border:1px solid var(--border); background:var(--surface2); color:var(--text); font-size:13px; outline:none; transition:border-color .15s; }
.field-input:focus { border-color:var(--ac); }
select.field-input { cursor:pointer; }

/* ── Sub-tabs (modal submission) ── */
.sub-tab-btn {
  padding:11px 16px; font-size:12px; font-weight:600; color:var(--muted);
  border:none; background:none; cursor:pointer; border-bottom:2px solid transparent;
  margin-bottom:-1px; transition:color .15s, border-color .15s; flex-shrink:0;
}
.sub-tab-btn.active { color:var(--ac); border-bottom-color:var(--ac); }

/* ── Sub submission konten styles ── */
#sub-final-content h2, #sub-anggota-konten h2 { font-size:1.15em; font-weight:700; margin:.5em 0 .25em; }
#sub-final-content h3, #sub-anggota-konten h3 { font-size:1em; font-weight:700; margin:.4em 0 .2em; }
#sub-final-content ul, #sub-final-content ol,
#sub-anggota-konten ul, #sub-anggota-konten ol { padding-left:1.3em; margin:.25em 0; }
#sub-final-content blockquote, #sub-anggota-konten blockquote { border-left:3px solid var(--ac); padding-left:10px; color:var(--muted); font-style:italic; }
#sub-final-content img, #sub-anggota-konten img { max-width:100%; height:auto; border-radius:8px; border:1px solid var(--border); margin:.3em 0; display:block; }

/* ── Badge (used in modal) ── */
.badge { display:inline-flex; align-items:center; gap:4px; padding:3px 9px; border-radius:20px; font-size:10px; font-weight:700; }
.b-submitted { background:rgba(16,185,129,.12); color:#34d399; }
.b-belum     { background:rgba(100,116,139,.12); color:#94a3b8; }

/* ── Grade input ── */
.grade-inp {
  padding:7px 11px; border-radius:9px; border:1px solid var(--border);
  background:var(--surface2); color:var(--text); font-size:12px;
  outline:none; transition:border-color .15s; width:100%;
}
.grade-inp:focus { border-color:var(--ac); }
textarea.grade-inp { resize:vertical; min-height:55px; font-family:inherit; }

/* ── Anggota block in modal ── */
.sub-anggota-block { border:1px solid var(--border); border-radius:12px; overflow:hidden; margin-bottom:12px; }
.sub-anggota-head { display:flex; align-items:center; gap:10px; padding:11px 14px; cursor:pointer; transition:background .12s; }
.sub-anggota-head:hover { background:var(--surface2); }
.sub-av { width:30px; height:30px; border-radius:9px; display:grid; place-items:center; font-size:10px; font-weight:700; flex-shrink:0; }
.sub-konten-box { padding:14px 16px; border-top:1px solid var(--border); font-size:13px; line-height:1.7; color:var(--text); }

/* ── AI grade button ── */
.btn-ai { padding:6px 14px; border-radius:9px; font-size:11.5px; font-weight:600; border:none; cursor:pointer; transition:opacity .15s; background:rgba(99,102,241,.12); color:#818cf8; }
.btn-ai:hover { opacity:.8; }
.btn-ai:disabled { opacity:.4; cursor:not-allowed; }

/* ── Soal editor toolbar ── */
.soal-toolbar { display:flex; flex-wrap:wrap; gap:3px; padding:8px 10px; border-bottom:1px solid var(--border); background:var(--surface2); }
.soal-tb-btn { width:28px; height:28px; border-radius:7px; border:none; cursor:pointer; background:transparent; color:var(--muted); font-size:12px; display:grid; place-items:center; transition:background .12s,color .12s; }
.soal-tb-btn:hover { background:var(--border); color:var(--text); }
.soal-tb-sep { width:1px; background:var(--border); margin:3px 2px; align-self:stretch; }
#soal-editor { min-height:180px; padding:14px 16px; outline:none; font-size:13px; line-height:1.7; color:var(--text); overflow-y:auto; }
#soal-editor:empty::before { content:attr(data-placeholder); color:var(--muted); pointer-events:none; }
#soal-editor h2 { font-size:1.15em; font-weight:700; margin:.5em 0 .25em; }
#soal-editor h3 { font-size:1em; font-weight:700; margin:.4em 0 .2em; }
#soal-editor ul,#soal-editor ol { padding-left:1.3em; margin:.25em 0; }
#soal-editor blockquote { border-left:3px solid var(--ac); padding-left:10px; color:var(--muted); font-style:italic; }
#soal-editor img { max-width:100%; height:auto; border-radius:8px; border:1px solid var(--border); margin:.3em 0; display:block; }

/* ── Individu submission table ── */
.ind-table { width:100%; border-collapse:collapse; font-size:12px; }
.ind-table th { padding:8px 10px; text-align:left; font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:var(--muted); border-bottom:1px solid var(--border); }
.ind-table td { padding:9px 10px; border-bottom:1px solid var(--border); vertical-align:middle; color:var(--text); }
.ind-table tr:last-child td { border-bottom:none; }
.ind-table tr:hover td { background:var(--surface2); }
.ind-nilai-inp { width:70px; padding:5px 8px; border-radius:8px; border:1px solid var(--border); background:var(--surface2); color:var(--text); font-size:12px; outline:none; text-align:center; }
.ind-nilai-inp:focus { border-color:var(--ac); }
.ind-catatan-inp { width:100%; padding:5px 8px; border-radius:8px; border:1px solid var(--border); background:var(--surface2); color:var(--text); font-size:11px; outline:none; resize:none; font-family:inherit; }
.ind-catatan-inp:focus { border-color:var(--ac); }

/* ── Empty state ── */
.empty-state { display:flex; flex-direction:column; align-items:center; justify-content:center; padding:60px 20px; gap:12px; }

/* ── Scrollbar ── */
.materi-main::-webkit-scrollbar,
.kelas-list::-webkit-scrollbar { width:4px; }
.materi-main::-webkit-scrollbar-track,
.kelas-list::-webkit-scrollbar-track { background:transparent; }
.materi-main::-webkit-scrollbar-thumb,
.kelas-list::-webkit-scrollbar-thumb { background:var(--border); border-radius:2px; }

/* ── Mobile ── */
@media (max-width: 767px) {
  .materi-layout { flex-direction:column; height:auto !important; overflow:visible; border-radius:14px; }
  .materi-main   { overflow-y:visible; height:auto; }
  .materi-sidebar { width:100%; border-right:none; border-bottom:1px solid var(--border); flex-direction:column; max-height:none; }
  .sidebar-label { display:none; }
  .kelas-list { display:flex; flex-direction:row; overflow-x:auto; overflow-y:hidden; padding:6px 8px; gap:6px; scrollbar-width:none; -webkit-overflow-scrolling:touch; }
  .kelas-list::-webkit-scrollbar { display:none; }
  .kelas-item { flex-shrink:0; flex-direction:row; align-items:center; margin-bottom:0; padding:5px 10px; gap:6px; border-radius:10px; white-space:nowrap; }
  .kelas-item .w-8.h-8 { width:22px; height:22px; font-size:8px; flex-shrink:0; }
  .ki-kode { font-size:11px; }
  .ki-nama { display:none; }
  .kelompok-grid { grid-template-columns:1fr 1fr; }
}
</style>
@endpush

@section('content')
<div class="space-y-4 animate-fadeUp">

  {{-- Header --}}
  <div class="flex flex-wrap items-center justify-between gap-3">
    <div>
      <h2 class="font-display font-bold text-[20px]" style="color:var(--text)">Tugas</h2>
      <p class="text-[13px] mt-0.5" style="color:var(--muted)">Kelola tugas kelompok dan individu setiap kelas</p>
    </div>
  </div>

  {{-- Two-panel layout --}}
  <div class="materi-layout">

    {{-- LEFT: kelas list --}}
    <div class="materi-sidebar">
      <div class="sidebar-label px-4 py-3 border-b" style="border-color:var(--border)">
        <p class="text-[11px] font-semibold uppercase tracking-widest" style="color:var(--muted)">Kelas</p>
      </div>
      <div class="kelas-list">
        @forelse($kelasList as $kelas)
          @php $isActive = $selectedKelas?->id === $kelas->id; @endphp
          <a href="{{ route('instruktur.tugas.index', ['kelas_id' => $kelas->id]) }}"
             class="kelas-item {{ $isActive ? 'active' : '' }}">
            <div class="w-8 h-8 rounded-lg grid place-items-center flex-shrink-0 text-[10px] font-bold a-bg-lt a-text">
              {{ substr($kelas->mataKuliah?->kode ?? '?', 0, 3) }}
            </div>
            <div class="min-w-0 flex-1">
              <div class="ki-kode">{{ $kelas->mataKuliah?->kode ?? $kelas->kode_kelas ?? '—' }}</div>
              <div class="ki-nama">
                {{ $kelas->mataKuliah?->nama ?? '—' }}
                @if($kelas->periodeAkademik)
                  &bull; {{ $kelas->periodeAkademik->nama }}
                @endif
              </div>
            </div>
          </a>
        @empty
          <div class="text-center py-8 text-[12px]" style="color:var(--muted)">
            <i class="fa-solid fa-inbox text-2xl mb-2 block"></i>
            Belum ada kelas
          </div>
        @endforelse
      </div>
    </div>

    {{-- RIGHT: main content --}}
    <div class="materi-main">
      @if($selectedKelas)

        {{-- Top bar --}}
        <div class="px-5 py-3 border-b flex items-center justify-between gap-3 flex-wrap" style="border-color:var(--border);background:var(--surface)">
          <div class="min-w-0">
            <span class="font-display font-bold text-[15px]" style="color:var(--text)">
              {{ $selectedKelas->mataKuliah?->kode ?? '—' }}
            </span>
            <span class="text-[12px] ml-2" style="color:var(--muted)">
              {{ $selectedKelas->mataKuliah?->nama ?? '—' }}
            </span>
            @if($selectedKelas->periodeAkademik)
              <span class="text-[11px] ml-1 px-2 py-0.5 rounded-full bg-blue-500/15 text-blue-400 font-semibold">
                {{ $selectedKelas->periodeAkademik->nama }}
              </span>
            @endif
          </div>
          <button onclick="openTugasModal()"
                  class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-[12px] font-semibold a-bg-lt a-text border hover:opacity-80 transition-opacity"
                  style="border-color:rgba(var(--ac-rgb),.35)">
            <i class="fa-solid fa-plus text-[10px]"></i>
            Buat Tugas
          </button>
        </div>

        {{-- Tabs --}}
        <div class="tab-bar">
          <div class="tab-item active" id="tab-kelompok" onclick="switchTab('kelompok')">
            <i class="fa-solid fa-users mr-1.5 text-[11px]"></i>Tugas Kelompok
          </div>
          <div class="tab-item" id="tab-individu" onclick="switchTab('individu')">
            <i class="fa-solid fa-user mr-1.5 text-[11px]"></i>Tugas Individu
          </div>
        </div>

        {{-- Tab: Kelompok --}}
        <div id="content-kelompok" class="p-5 space-y-3">
          @if($tugasList->where('tipe', 'kelompok')->isEmpty())
            <div class="empty-state" style="color:var(--muted)">
              <div class="w-14 h-14 rounded-2xl grid place-items-center a-bg-lt a-text text-[22px] mx-auto">
                <i class="fa-solid fa-users"></i>
              </div>
              <div class="text-[14px] font-semibold" style="color:var(--text)">Belum ada Tugas Kelompok</div>
              <p class="text-[12px] text-center max-w-xs">Buat tugas kelompok untuk kelas ini dan atur pembagian kelompoknya.</p>
              <button onclick="openTugasModal()"
                      class="mt-1 px-5 py-2 rounded-xl text-[12px] font-semibold text-white transition-opacity hover:opacity-85"
                      style="background:var(--ac)">
                <i class="fa-solid fa-plus mr-1.5"></i>Buat Tugas
              </button>
            </div>
          @else
            @foreach($tugasList->where('tipe', 'kelompok') as $tugas)
              @php
                $isOverdue = $tugas->deadline && $tugas->deadline->isPast() && $tugas->status !== 'selesai';
                $kelompokList = $tugas->kelompok;
              @endphp
              <div class="tugas-card" id="tugas-card-{{ $tugas->id }}">
                {{-- Accordion Head --}}
                <div class="tugas-head" onclick="toggleTugas({{ $tugas->id }})">
                  {{-- chevron --}}
                  <div class="w-6 h-6 flex items-center justify-center flex-shrink-0" id="chevron-{{ $tugas->id }}" style="color:var(--muted)">
                    <i class="fa-solid fa-chevron-right text-[10px] transition-transform duration-200"></i>
                  </div>
                  {{-- info --}}
                  <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                      <span class="text-[13.5px] font-semibold truncate" style="color:var(--text)">{{ $tugas->judul }}</span>
                      {{-- status --}}
                      <span class="text-[10px] font-bold px-2 py-0.5 rounded-full badge-{{ $tugas->status }}">
                        {{ ucfirst($tugas->status) }}
                      </span>
                      {{-- deadline --}}
                      @if($tugas->deadline)
                        <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full {{ $isOverdue ? 'badge-overdue' : 'bg-slate-500/15 text-slate-400' }}">
                          <i class="fa-regular fa-clock mr-1"></i>{{ $tugas->deadline->format('d M Y, H:i') }}
                          @if($isOverdue) &bull; Lewat @endif
                        </span>
                      @endif
                    </div>
                    <div class="text-[11px] mt-0.5" style="color:var(--muted)">
                      {{ $kelompokList->count() }} kelompok
                      @if($tugas->deskripsi)
                        &bull; {{ Str::limit($tugas->deskripsi, 60) }}
                      @endif
                    </div>
                  </div>
                  {{-- actions --}}
                  <div class="flex items-center gap-1.5 flex-shrink-0" onclick="event.stopPropagation()">
                    <button onclick="openTugasModal({{ $tugas->id }}, '{{ addslashes($tugas->judul) }}', '{{ addslashes($tugas->deskripsi ?? '') }}', '{{ $tugas->deadline?->format('Y-m-d\TH:i') ?? '' }}', '{{ $tugas->status }}', {{ $selectedKelas->id }})"
                            class="w-7 h-7 rounded-lg grid place-items-center text-[11px] hover:opacity-75 transition-opacity"
                            style="background:var(--surface2);color:var(--muted)" title="Edit">
                      <i class="fa-solid fa-pen"></i>
                    </button>
                    <button onclick="deleteTugas({{ $tugas->id }}, '{{ addslashes($tugas->judul) }}')"
                            class="w-7 h-7 rounded-lg grid place-items-center text-[11px] hover:opacity-75 transition-opacity bg-red-500/10 text-red-400"
                            title="Hapus">
                      <i class="fa-solid fa-trash"></i>
                    </button>
                  </div>
                </div>

                {{-- Accordion Body --}}
                <div class="tugas-body" id="tugas-body-{{ $tugas->id }}">
                  <div class="kelompok-grid" id="kelompok-grid-{{ $tugas->id }}">
                    @foreach($kelompokList as $kelompok)
                      <div class="kelompok-card" id="kelompok-card-{{ $kelompok->id }}">
                        <div class="flex items-center justify-between gap-2">
                          <div class="text-[12.5px] font-semibold truncate" style="color:var(--text)">
                            {{ $kelompok->nama_kelompok }}
                          </div>
                          <div class="flex items-center gap-1 flex-shrink-0">
                            <button onclick="openKelompokModal({{ $tugas->id }}, {{ $kelompok->id }}, '{{ addslashes($kelompok->nama_kelompok) }}', {{ $kelompok->ketua_mahasiswa_id ?? 'null' }})"
                                    class="w-6 h-6 rounded-lg grid place-items-center text-[10px] hover:opacity-75 transition-opacity"
                                    style="background:var(--surface2);color:var(--muted)" title="Edit kelompok">
                              <i class="fa-solid fa-pen"></i>
                            </button>
                            <button onclick="deleteKelompok({{ $tugas->id }}, {{ $kelompok->id }}, '{{ addslashes($kelompok->nama_kelompok) }}')"
                                    class="w-6 h-6 rounded-lg grid place-items-center text-[10px] hover:opacity-75 transition-opacity bg-red-500/10 text-red-400"
                                    title="Hapus kelompok">
                              <i class="fa-solid fa-trash"></i>
                            </button>
                          </div>
                        </div>
                        {{-- Ketua --}}
                        <div class="flex items-center gap-2">
                          @if($kelompok->ketua)
                            <div class="av-mini a-bg-lt a-text">
                              {{ strtoupper(substr($kelompok->ketua->nama ?? '?', 0, 1)) }}
                            </div>
                            <span class="text-[11px] truncate" style="color:var(--muted)">
                              {{ $kelompok->ketua->nama }}
                            </span>
                          @else
                            <div class="av-mini" style="background:var(--surface2);color:var(--muted)">
                              <i class="fa-solid fa-user text-[8px]"></i>
                            </div>
                            <span class="text-[11px]" style="color:var(--muted)">Belum ditentukan</span>
                          @endif
                        </div>
                        {{-- Anggota count + submission status --}}
                        <div class="flex items-center gap-2 flex-wrap">
                          <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full" style="background:var(--surface2);color:var(--muted)">
                            <i class="fa-solid fa-users text-[9px] mr-1"></i>{{ $kelompok->anggota_count }} anggota
                          </span>
                          @if($kelompok->status_submit === 'submitted')
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full" style="background:rgba(16,185,129,.12);color:#34d399">
                              <i class="fa-solid fa-check text-[8px] mr-0.5"></i>Dikumpulkan
                            </span>
                          @endif
                        </div>
                        {{-- Tombol lihat & nilai --}}
                        <button onclick="openSubmission({{ $tugas->id }}, {{ $kelompok->id }})"
                                class="w-full flex items-center justify-center gap-1.5 py-1.5 rounded-lg text-[11px] font-semibold transition-opacity hover:opacity-80"
                                style="background:rgba(var(--ac-rgb),.1);color:var(--ac)">
                          <i class="fa-solid fa-{{ $kelompok->nilai_kelompok !== null ? 'star' : 'eye' }} text-[9px]"></i>
                          {{ $kelompok->nilai_kelompok !== null ? 'Nilai: ' . $kelompok->nilai_kelompok : 'Lihat & Nilai' }}
                        </button>
                      </div>
                    @endforeach

                    {{-- Add kelompok button --}}
                    <div class="kelompok-card items-center justify-center cursor-pointer hover:border-[rgba(var(--ac-rgb),.4)] transition-colors"
                         style="border-style:dashed;min-height:90px"
                         onclick="openKelompokModal({{ $tugas->id }})">
                      <div class="flex flex-col items-center gap-2" style="color:var(--muted)">
                        <div class="w-8 h-8 rounded-lg grid place-items-center a-bg-lt a-text text-[13px]">
                          <i class="fa-solid fa-plus"></i>
                        </div>
                        <span class="text-[11px] font-semibold">Tambah Kelompok</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            @endforeach
          @endif
        </div>

        {{-- Tab: Individu --}}
        <div id="content-individu" class="p-5 space-y-3 hidden">
          @if($tugasList->where('tipe', 'individu')->isEmpty())
            <div class="empty-state" style="color:var(--muted)">
              <div class="w-14 h-14 rounded-2xl grid place-items-center a-bg-lt a-text text-[22px] mx-auto">
                <i class="fa-solid fa-user-pen"></i>
              </div>
              <div class="text-[14px] font-semibold" style="color:var(--text)">Belum ada Tugas Individu</div>
              <p class="text-[12px] text-center max-w-xs">Buat tugas individu untuk kelas ini, semua mahasiswa akan bisa mengerjakan dan mengumpulkan PDF.</p>
              <button onclick="openTugasModal(null,null,null,null,null,null,'individu')"
                      class="mt-1 px-5 py-2 rounded-xl text-[12px] font-semibold text-white transition-opacity hover:opacity-85"
                      style="background:var(--ac)">
                <i class="fa-solid fa-plus mr-1.5"></i>Buat Tugas Individu
              </button>
            </div>
          @else
            @foreach($tugasList->where('tipe', 'individu') as $tugas)
              @php
                $isOverdue = $tugas->deadline && $tugas->deadline->isPast() && $tugas->status !== 'selesai';
              @endphp
              <div class="tugas-card" id="tugas-card-{{ $tugas->id }}">
                <div class="tugas-head" onclick="toggleTugas({{ $tugas->id }})">
                  <div class="w-6 h-6 flex items-center justify-center flex-shrink-0" id="chevron-{{ $tugas->id }}" style="color:var(--muted)">
                    <i class="fa-solid fa-chevron-right text-[10px] transition-transform duration-200"></i>
                  </div>
                  <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                      <span class="text-[13.5px] font-semibold truncate" style="color:var(--text)">{{ $tugas->judul }}</span>
                      <span class="text-[10px] font-bold px-2 py-0.5 rounded-full badge-{{ $tugas->status }}">{{ ucfirst($tugas->status) }}</span>
                      @if($tugas->deadline)
                        <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full {{ $isOverdue ? 'badge-overdue' : 'bg-slate-500/15 text-slate-400' }}">
                          <i class="fa-regular fa-clock mr-1"></i>{{ $tugas->deadline->format('d M Y, H:i') }}
                          @if($isOverdue) &bull; Lewat @endif
                        </span>
                      @endif
                    </div>
                    <div class="text-[11px] mt-0.5" style="color:var(--muted)">
                      Tugas Individu &bull; {{ $mahasiswaList->count() }} mahasiswa
                      @if($tugas->deskripsi) &bull; {{ Str::limit($tugas->deskripsi, 60) }} @endif
                    </div>
                  </div>
                  <div class="flex items-center gap-1.5 flex-shrink-0" onclick="event.stopPropagation()">
                    <button onclick="openTugasModal({{ $tugas->id }}, '{{ addslashes($tugas->judul) }}', '{{ addslashes($tugas->deskripsi ?? '') }}', '{{ $tugas->deadline?->format('Y-m-d\TH:i') ?? '' }}', '{{ $tugas->status }}', {{ $selectedKelas->id }}, 'individu', @js($tugas->soal))"
                            class="w-7 h-7 rounded-lg grid place-items-center text-[11px] hover:opacity-75 transition-opacity"
                            style="background:var(--surface2);color:var(--muted)" title="Edit">
                      <i class="fa-solid fa-pen"></i>
                    </button>
                    <button onclick="deleteTugas({{ $tugas->id }}, '{{ addslashes($tugas->judul) }}')"
                            class="w-7 h-7 rounded-lg grid place-items-center text-[11px] hover:opacity-75 transition-opacity bg-red-500/10 text-red-400"
                            title="Hapus">
                      <i class="fa-solid fa-trash"></i>
                    </button>
                  </div>
                </div>
                <div class="tugas-body" id="tugas-body-{{ $tugas->id }}">
                  <button onclick="openIndividuSubmissions({{ $tugas->id }})"
                          class="flex items-center gap-2 px-4 py-2 rounded-xl text-[12px] font-semibold transition-opacity hover:opacity-80"
                          style="background:rgba(var(--ac-rgb),.1);color:var(--ac)">
                    <i class="fa-solid fa-list-check text-[11px]"></i>
                    Lihat Submission & Nilai
                  </button>
                </div>
              </div>
            @endforeach
          @endif
        </div>

      @else
        {{-- No kelas selected --}}
        <div class="empty-state flex-1" style="color:var(--muted)">
          <div class="w-14 h-14 rounded-2xl grid place-items-center a-bg-lt a-text text-[22px] mx-auto">
            <i class="fa-solid fa-chalkboard"></i>
          </div>
          <div class="text-[14px] font-semibold" style="color:var(--text)">Pilih Kelas</div>
          <p class="text-[12px] text-center">Pilih kelas dari daftar di sebelah kiri untuk mengelola tugas.</p>
        </div>
      @endif
    </div>

  </div>{{-- end materi-layout --}}

</div>{{-- end space-y-4 --}}


{{-- ════════════════════════════════════════════════════════════ --}}
{{--  MODAL: Buat / Edit Tugas                                    --}}
{{-- ════════════════════════════════════════════════════════════ --}}
<div id="modal-tugas" class="modal-backdrop">
  <div class="modal-box" style="max-width:640px;max-height:92vh;display:flex;flex-direction:column;overflow:hidden;padding:0">
    <div class="modal-header" style="flex-shrink:0">
      <h3 class="modal-title" id="modal-tugas-title">Buat Tugas</h3>
      <button onclick="closeModal('modal-tugas')" class="modal-close"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div style="flex:1;overflow-y:auto;padding:18px 20px" class="space-y-3">
      <input type="hidden" id="tugas-id">
      <input type="hidden" id="tugas-kelas-id" value="{{ $selectedKelas?->id }}">

      {{-- Tipe --}}
      <div id="tipe-row">
        <label class="field-label">Tipe Tugas <span class="text-red-400">*</span></label>
        <div style="display:flex;gap:8px">
          <label style="flex:1;display:flex;align-items:center;gap:8px;padding:9px 12px;border-radius:10px;border:1px solid var(--border);cursor:pointer;transition:border-color .15s" id="tipe-kelompok-label">
            <input type="radio" name="tugas-tipe" value="kelompok" id="tipe-kelompok" checked onchange="onTipeChange()" style="accent-color:var(--ac)">
            <span style="font-size:12px;font-weight:600;color:var(--text)"><i class="fa-solid fa-users mr-1 a-text"></i>Kelompok</span>
          </label>
          <label style="flex:1;display:flex;align-items:center;gap:8px;padding:9px 12px;border-radius:10px;border:1px solid var(--border);cursor:pointer;transition:border-color .15s" id="tipe-individu-label">
            <input type="radio" name="tugas-tipe" value="individu" id="tipe-individu" onchange="onTipeChange()" style="accent-color:var(--ac)">
            <span style="font-size:12px;font-weight:600;color:var(--text)"><i class="fa-solid fa-user mr-1 a-text"></i>Individu</span>
          </label>
        </div>
      </div>

      <div>
        <label class="field-label">Judul Tugas <span class="text-red-400">*</span></label>
        <input type="text" id="tugas-judul" maxlength="200" placeholder="cth. Tugas Analisis Sistem" class="field-input">
      </div>
      <div>
        <label class="field-label">Deskripsi <span class="text-[11px]" style="color:var(--muted)">opsional</span></label>
        <textarea id="tugas-deskripsi" rows="2" maxlength="2000"
                  placeholder="Penjelasan singkat tentang tugas ini..."
                  class="field-input resize-none"></textarea>
      </div>

      {{-- Soal (hanya untuk individu) --}}
      <div id="soal-section" style="display:none">
        <label class="field-label">Soal / Pertanyaan <span class="text-red-400">*</span></label>
        <div style="border:1px solid var(--border);border-radius:12px;overflow:hidden">
          <div class="soal-toolbar">
            <button class="soal-tb-btn" onclick="soalCmd('bold')" title="Bold"><i class="fa-solid fa-bold"></i></button>
            <button class="soal-tb-btn" onclick="soalCmd('italic')" title="Italic"><i class="fa-solid fa-italic"></i></button>
            <button class="soal-tb-btn" onclick="soalCmd('underline')" title="Underline"><i class="fa-solid fa-underline"></i></button>
            <div class="soal-tb-sep"></div>
            <button class="soal-tb-btn" onclick="soalCmd('formatBlock','<h2>')" title="Heading"><i class="fa-solid fa-heading"></i></button>
            <button class="soal-tb-btn" onclick="soalCmd('insertOrderedList')" title="Nomor"><i class="fa-solid fa-list-ol"></i></button>
            <button class="soal-tb-btn" onclick="soalCmd('insertUnorderedList')" title="Poin"><i class="fa-solid fa-list-ul"></i></button>
            <div class="soal-tb-sep"></div>
            <button class="soal-tb-btn" onclick="triggerSoalImg()" title="Sisipkan Gambar" id="soal-img-btn">
              <i class="fa-solid fa-image"></i>
            </button>
            <input type="file" id="soal-img-inp" accept="image/*" style="display:none" onchange="handleSoalImg(this)">
          </div>
          <div id="soal-editor" contenteditable="true" data-placeholder="Tulis soal / pertanyaan tugas di sini…"></div>
        </div>
        <div id="soal-img-progress" style="display:none;padding:4px 12px;font-size:11px;color:var(--muted)">
          <i class="fa-solid fa-spinner fa-spin mr-1"></i>Mengunggah gambar…
        </div>
      </div>

      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="field-label">Deadline <span class="text-[11px]" style="color:var(--muted)">opsional</span></label>
          <input type="datetime-local" id="tugas-deadline" class="field-input">
        </div>
        <div>
          <label class="field-label">Status <span class="text-red-400">*</span></label>
          <select id="tugas-status" class="field-input">
            <option value="draft">Draft</option>
            <option value="aktif">Aktif</option>
            <option value="selesai" style="display:none">Selesai</option>
          </select>
        </div>
      </div>
    </div>
    <div class="modal-footer" style="flex-shrink:0">
      <button type="button" onclick="closeModal('modal-tugas')" class="btn-ghost">Batal</button>
      <button type="button" onclick="saveTugas()" class="btn-primary" id="btn-save-tugas">
        <span id="btn-save-tugas-text"><i class="fa-solid fa-floppy-disk mr-1.5"></i>Simpan</span>
        <span id="btn-save-tugas-spin" class="hidden"><i class="fa-solid fa-spinner fa-spin mr-1.5"></i>Menyimpan...</span>
      </button>
    </div>
  </div>
</div>


{{-- ════════════════════════════════════════════════════════════ --}}
{{--  MODAL: Buat / Edit Kelompok                                  --}}
{{-- ════════════════════════════════════════════════════════════ --}}
<div id="modal-kelompok" class="modal-backdrop">
  <div class="modal-box" style="max-width:420px">
    <div class="modal-header">
      <h3 class="modal-title" id="modal-kelompok-title">Tambah Kelompok</h3>
      <button onclick="closeModal('modal-kelompok')" class="modal-close"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="modal-body space-y-3">
      <input type="hidden" id="kelompok-id">
      <input type="hidden" id="kelompok-tugas-id">
      <div>
        <label class="field-label">Nama Kelompok <span class="text-red-400">*</span></label>
        <input type="text" id="kelompok-nama" maxlength="100" placeholder="cth. Kelompok A"
               class="field-input">
      </div>
      <div>
        <label class="field-label">Ketua <span class="text-[11px]" style="color:var(--muted)">opsional</span></label>
        <select id="kelompok-ketua" class="field-input">
          <option value="">— Belum ditentukan —</option>
          @foreach($mahasiswaList as $mhs)
            <option value="{{ $mhs->id }}">{{ $mhs->nama }} ({{ $mhs->nim ?? '—' }})</option>
          @endforeach
        </select>
      </div>
    </div>
    <div class="modal-footer">
      <button type="button" onclick="closeModal('modal-kelompok')" class="btn-ghost">Batal</button>
      <button type="button" onclick="saveKelompok()" class="btn-primary" id="btn-save-kelompok">
        <span id="btn-save-kelompok-text"><i class="fa-solid fa-floppy-disk mr-1.5"></i>Simpan</span>
        <span id="btn-save-kelompok-spin" class="hidden"><i class="fa-solid fa-spinner fa-spin mr-1.5"></i>Menyimpan...</span>
      </button>
    </div>
  </div>
</div>

{{-- ═══════════════════════════════════════════════════ --}}
{{-- MODAL: Lihat Submission + Penilaian (instruktur)   --}}
{{-- ═══════════════════════════════════════════════════ --}}
<div id="modal-submission" class="modal-backdrop">
  <div class="modal-box" style="max-width:860px;height:90vh;display:flex;flex-direction:column;padding:0;overflow:hidden">

    {{-- Header --}}
    <div class="modal-header" style="flex-shrink:0">
      <div>
        <h3 class="modal-title" id="sub-modal-title">—</h3>
        <div id="sub-modal-meta" class="text-[11px] mt-0.5" style="color:var(--muted)"></div>
      </div>
      <div class="flex items-center gap-2">
        <span id="sub-modal-status-badge" class="badge" style="display:none"></span>
        <button onclick="closeModal('modal-submission')" class="modal-close">
          <i class="fa-solid fa-xmark"></i>
        </button>
      </div>
    </div>

    {{-- Tabs --}}
    <div style="display:flex;border-bottom:1px solid var(--border);flex-shrink:0">
      <button class="sub-tab-btn active" onclick="subTab('final',this)" id="sub-tab-final">
        <i class="fa-solid fa-layer-group mr-1.5"></i>Dokumen Final
      </button>
      <button class="sub-tab-btn" onclick="subTab('anggota',this)" id="sub-tab-anggota">
        <i class="fa-solid fa-users mr-1.5"></i>Per Anggota
      </button>
      <button class="sub-tab-btn" onclick="subTab('nilai',this)" id="sub-tab-nilai">
        <i class="fa-solid fa-star mr-1.5"></i>Penilaian
      </button>
    </div>

    {{-- Body --}}
    <div style="flex:1;overflow-y:auto">

      {{-- Loader --}}
      <div id="sub-loader" class="py-16 text-center" style="color:var(--muted)">
        <i class="fa-solid fa-spinner fa-spin text-[24px] block mb-3"></i>
        <div class="text-[13px]">Memuat data…</div>
      </div>

      {{-- Tab: Dokumen Final --}}
      <div id="sub-panel-final" style="display:none">
        <div id="sub-final-content" style="padding:20px 24px;font-size:13.5px;line-height:1.75;color:var(--text)"></div>
      </div>

      {{-- Tab: Per Anggota --}}
      <div id="sub-panel-anggota" style="display:none;padding:16px"></div>

      {{-- Tab: Penilaian --}}
      <div id="sub-panel-nilai" style="display:none;padding:20px 24px">
        <div id="sub-nilai-form"></div>
        <div class="flex justify-end mt-5">
          <button onclick="saveGrade()" id="btn-save-grade" class="btn-primary">
            <i class="fa-solid fa-floppy-disk mr-1.5"></i>Simpan Penilaian
          </button>
        </div>
      </div>

    </div>
  </div>
</div>

{{-- ════════════════════════════════════════════════════════════ --}}
{{--  MODAL: Submissions Tugas Individu                           --}}
{{-- ════════════════════════════════════════════════════════════ --}}
<div id="modal-individu" class="modal-backdrop">
  <div class="modal-box" style="max-width:920px;height:92vh;display:flex;flex-direction:column;padding:0;overflow:hidden">

    <div class="modal-header" style="flex-shrink:0">
      <div>
        <h3 class="modal-title" id="ind-modal-title">—</h3>
        <div id="ind-modal-meta" class="text-[11px] mt-0.5" style="color:var(--muted)"></div>
      </div>
      <div class="flex items-center gap-2">
        <button id="btn-ai-grade-all" onclick="runAiGradeAll()" class="btn-ai" style="display:none">
          <i class="fa-solid fa-wand-magic-sparkles mr-1.5"></i>Nilai Semua dengan AI
        </button>
        <button onclick="saveAllGrades()" id="btn-save-all-grades" class="btn-primary" style="display:none;font-size:12px;padding:6px 14px">
          <i class="fa-solid fa-floppy-disk mr-1.5"></i>Simpan Penilaian
        </button>
        <button onclick="closeModal('modal-individu')" class="modal-close"><i class="fa-solid fa-xmark"></i></button>
      </div>
    </div>

    {{-- Soal panel --}}
    <div id="ind-soal-panel" style="display:none;padding:14px 20px;border-bottom:1px solid var(--border);background:var(--surface2);flex-shrink:0">
      <div class="text-[10px] font-bold uppercase tracking-widest mb-2" style="color:var(--muted)"><i class="fa-solid fa-clipboard-question mr-1 a-text"></i>Soal</div>
      <div id="ind-soal-content" style="font-size:12.5px;line-height:1.7;color:var(--text);max-height:120px;overflow-y:auto"></div>
    </div>

    {{-- Loader --}}
    <div id="ind-loader" style="flex:1;display:flex;align-items:center;justify-content:center;color:var(--muted)">
      <div class="text-center"><i class="fa-solid fa-spinner fa-spin text-[24px] block mb-3"></i><div class="text-[13px]">Memuat data…</div></div>
    </div>

    {{-- Table --}}
    <div id="ind-table-wrap" style="flex:1;overflow-y:auto;display:none;padding:16px">
      <div id="ind-catatan-umum-box" style="display:none;margin-bottom:12px;padding:10px 14px;border-radius:10px;background:rgba(245,158,11,.08);border:1px solid rgba(245,158,11,.2)">
        <div class="text-[10px] font-bold uppercase mb-1" style="color:#fbbf24"><i class="fa-solid fa-triangle-exclamation mr-1"></i>Catatan AI</div>
        <div id="ind-catatan-umum" style="font-size:12px;color:var(--text)"></div>
      </div>
      <table class="ind-table">
        <thead>
          <tr>
            <th>#</th>
            <th>Mahasiswa</th>
            <th>Status</th>
            <th>PDF</th>
            <th style="width:80px">Nilai</th>
            <th>Catatan</th>
          </tr>
        </thead>
        <tbody id="ind-tbody"></tbody>
      </table>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

const ROUTES = {
  tugasStore:              '{{ route("instruktur.tugas.store") }}',
  tugasUpdate:             id => `{{ url("instruktur/tugas") }}/${id}`,
  tugasDestroy:            id => `{{ url("instruktur/tugas") }}/${id}`,
  kelompokStore:           tugasId => `{{ url("instruktur/tugas") }}/${tugasId}/kelompok`,
  kelompokUpdate:          (tugasId, id) => `{{ url("instruktur/tugas") }}/${tugasId}/kelompok/${id}`,
  kelompokDestroy:         (tugasId, id) => `{{ url("instruktur/tugas") }}/${tugasId}/kelompok/${id}`,
  submission:              (tugasId, id) => `{{ url("instruktur/tugas") }}/${tugasId}/kelompok/${id}/submission`,
  grade:                   (tugasId, id) => `{{ url("instruktur/tugas") }}/${tugasId}/kelompok/${id}/grade`,
  aiGrade:                 (tugasId, id) => `{{ url("instruktur/tugas") }}/${tugasId}/kelompok/${id}/ai-grade`,
  uploadSoalGambar:        '{{ route("instruktur.tugas.upload-soal-gambar") }}',
  individuSubmissions:     tugasId => `{{ url("instruktur/tugas") }}/${tugasId}/individu/submissions`,
  gradeAll:                tugasId => `{{ url("instruktur/tugas") }}/${tugasId}/individu/grade-all`,
  aiGradeIndividu:         tugasId => `{{ url("instruktur/tugas") }}/${tugasId}/individu/ai-grade`,
};

/* ══════════════════════════════════════════════════════════════ */
/*  SUBMISSION MODAL                                              */
/* ══════════════════════════════════════════════════════════════ */
let _subTugasId = null, _subKelompokId = null, _subData = null;

async function openSubmission(tugasId, kelompokId) {
  _subTugasId = tugasId;
  _subKelompokId = kelompokId;
  _subData = null;

  // Reset UI
  document.getElementById('sub-loader').style.display = '';
  ['final','anggota','nilai'].forEach(t => document.getElementById('sub-panel-' + t).style.display = 'none');
  document.querySelectorAll('.sub-tab-btn').forEach(b => b.classList.remove('active'));
  document.getElementById('sub-tab-final').classList.add('active');
  document.getElementById('sub-modal-title').textContent = 'Memuat…';
  document.getElementById('sub-modal-meta').textContent = '';
  document.getElementById('sub-modal-status-badge').style.display = 'none';

  openModal('modal-submission');

  try {
    const r = await fetch(ROUTES.submission(tugasId, kelompokId), {
      headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
    });
    if (!r.ok) throw new Error('HTTP ' + r.status);
    _subData = await r.json();
    renderSubmission(_subData);
  } catch(e) {
    document.getElementById('sub-loader').innerHTML =
      `<div class="py-12 text-center text-[13px]" style="color:var(--muted)">Gagal memuat data: ${e.message}</div>`;
  }
}

function renderSubmission(d) {
  const k = d.kelompok, t = d.tugas;

  // Header
  document.getElementById('sub-modal-title').textContent = k.nama_kelompok;
  document.getElementById('sub-modal-meta').textContent  = t.judul + (k.ketua_nama ? ' · Ketua: ' + k.ketua_nama : '');

  const badge = document.getElementById('sub-modal-status-badge');
  badge.style.display = '';
  badge.className = 'badge ' + (k.status_submit === 'submitted' ? 'b-submitted' : 'b-belum');
  badge.innerHTML = `<i class="fa-solid fa-${k.status_submit === 'submitted' ? 'check' : 'clock'} text-[8px]"></i>${k.status_submit === 'submitted' ? 'Dikumpulkan' : 'Belum dikumpulkan'}`;

  // Tab: Dokumen Final
  const finalBox = document.getElementById('sub-final-content');
  if (k.konten_final) {
    let html = `<div id="sub-anggota-konten" style="padding:0">${k.konten_final}</div>`;
    if (k.pdf_url) {
      html += `<div style="margin-top:16px"><a href="${k.pdf_url}" target="_blank" class="btn-ghost" style="display:inline-flex;align-items:center;gap:6px;font-size:12px"><i class="fa-solid fa-file-pdf" style="color:#f87171"></i>Unduh PDF</a></div>`;
    }
    finalBox.innerHTML = html;
  } else {
    finalBox.innerHTML = `<div class="py-10 text-center text-[13px]" style="color:var(--muted)"><i class="fa-solid fa-file-circle-xmark text-[24px] opacity-20 block mb-3"></i>Belum ada dokumen final.</div>`;
  }

  // Tab: Per Anggota
  const angPanel = document.getElementById('sub-panel-anggota');
  angPanel.innerHTML = '';
  if (!d.anggota.length) {
    angPanel.innerHTML = `<div class="py-8 text-center text-[12px]" style="color:var(--muted)">Belum ada anggota.</div>`;
  } else {
    d.anggota.forEach(a => {
      const div = document.createElement('div');
      div.className = 'sub-anggota-block';
      div.innerHTML = `
        <div class="sub-anggota-head" onclick="toggleSubBlock('sb-${a.id}','sc-${a.id}')">
          <div class="sub-av a-bg-lt a-text">${(a.nama||'?').charAt(0).toUpperCase()}</div>
          <div style="flex:1;min-width:0">
            <div style="font-weight:600;font-size:12px;color:var(--text)">${escH(a.nama)}${a.is_ketua ? '<span style="font-size:9px;font-weight:700;padding:1px 6px;border-radius:4px;background:rgba(245,158,11,.14);color:#fbbf24;margin-left:5px">Ketua</span>' : ''}</div>
            ${a.topik ? `<div style="font-size:10px;color:var(--ac)">${escH(a.topik)}</div>` : ''}
          </div>
          <div style="display:flex;align-items:center;gap:6px;flex-shrink:0">
            <span class="badge ${a.status_submit === 'submitted' ? 'b-submitted' : 'b-belum'}">${a.status_submit === 'submitted' ? '&#10003; Dikumpulkan' : 'Belum'}</span>
            <i id="sc-${a.id}" class="fa-solid fa-chevron-down" style="font-size:10px;color:var(--muted);transition:transform .2s"></i>
          </div>
        </div>
        <div id="sb-${a.id}" style="display:none">
          <div class="sub-konten-box">${a.konten || '<em style="color:var(--muted)">Belum ada konten.</em>'}</div>
        </div>`;
      angPanel.appendChild(div);
    });
  }

  // Tab: Penilaian
  renderNilaiForm(d);

  // Tampilkan
  document.getElementById('sub-loader').style.display = 'none';
  document.getElementById('sub-panel-final').style.display = '';
}

function renderNilaiForm(d) {
  const k = d.kelompok;
  let html = `
    <div style="border:1px solid var(--border);border-radius:12px;padding:15px 16px;margin-bottom:16px">
      <div style="font-weight:700;font-size:13px;color:var(--text);margin-bottom:12px"><i class="fa-solid fa-users mr-1.5 a-text"></i>Nilai Kelompok</div>
      <div style="display:grid;grid-template-columns:110px 1fr;gap:10px;align-items:start">
        <div>
          <label class="field-label">Nilai (0–100)</label>
          <input type="number" id="g-kelompok-nilai" min="0" max="100" class="grade-inp" style="max-width:110px" value="${k.nilai_kelompok ?? ''}">
        </div>
        <div>
          <label class="field-label">Catatan</label>
          <textarea id="g-kelompok-catatan" class="grade-inp">${escH(k.catatan_kelompok ?? '')}</textarea>
        </div>
      </div>
    </div>
    <div style="font-weight:700;font-size:13px;color:var(--text);margin-bottom:10px"><i class="fa-solid fa-user mr-1.5 a-text"></i>Nilai Individu</div>`;

  d.anggota.forEach(a => {
    html += `
    <div style="border:1px solid var(--border);border-radius:12px;padding:13px 15px;margin-bottom:10px">
      <div style="display:flex;align-items:center;gap:8px;margin-bottom:10px">
        <div class="sub-av a-bg-lt a-text">${(a.nama||'?').charAt(0).toUpperCase()}</div>
        <div style="flex:1;min-width:0">
          <div style="font-weight:600;font-size:12px;color:var(--text)">${escH(a.nama)}${a.is_ketua ? '<span style="font-size:9px;font-weight:700;padding:1px 5px;border-radius:4px;background:rgba(245,158,11,.14);color:#fbbf24;margin-left:4px">Ketua</span>' : ''}</div>
          ${a.topik ? `<div style="font-size:10px;color:var(--ac)">${escH(a.topik)}</div>` : ''}
        </div>
        ${a.konten ? `<button class="btn-ai" onclick="runAiGrade(${a.id}, this)" title="Nilai dengan AI"><i class="fa-solid fa-wand-magic-sparkles mr-1"></i>AI Grade</button>` : ''}
      </div>
      <input type="hidden" class="g-ang-id" value="${a.id}">
      <div style="display:grid;grid-template-columns:110px 1fr;gap:8px;align-items:start">
        <div>
          <label class="field-label">Nilai (0–100)</label>
          <input type="number" class="grade-inp g-ang-nilai" id="g-ang-nilai-${a.id}" min="0" max="100" value="${a.nilai ?? ''}">
        </div>
        <div>
          <label class="field-label">Catatan</label>
          <textarea class="grade-inp g-ang-catatan" id="g-ang-catatan-${a.id}">${escH(a.catatan ?? '')}</textarea>
        </div>
      </div>
    </div>`;
  });

  document.getElementById('sub-nilai-form').innerHTML = html;
}

function subTab(name, btn) {
  ['final','anggota','nilai'].forEach(t => {
    document.getElementById('sub-panel-' + t).style.display = t === name ? '' : 'none';
  });
  document.querySelectorAll('.sub-tab-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
}

function toggleSubBlock(blockId, chevId) {
  const box  = document.getElementById(blockId);
  const chev = document.getElementById(chevId);
  const open = box.style.display === 'none';
  box.style.display = open ? 'block' : 'none';
  chev.style.transform = open ? 'rotate(180deg)' : '';
}

/* ── Save grade ── */
async function saveGrade() {
  if (!_subData) return;
  const btn = document.getElementById('btn-save-grade');
  btn.disabled = true;

  const anggotaItems = [];
  document.querySelectorAll('.g-ang-id').forEach(el => {
    const id = parseInt(el.value);
    anggotaItems.push({
      id,
      nilai:   document.getElementById('g-ang-nilai-' + id)?.value !== ''
                 ? parseInt(document.getElementById('g-ang-nilai-' + id).value) : null,
      catatan: document.getElementById('g-ang-catatan-' + id)?.value || null,
    });
  });

  const payload = {
    nilai_kelompok:   document.getElementById('g-kelompok-nilai')?.value !== ''
                        ? parseInt(document.getElementById('g-kelompok-nilai').value) : null,
    catatan_kelompok: document.getElementById('g-kelompok-catatan')?.value || null,
    anggota: anggotaItems,
  };

  try {
    const r = await fetch(ROUTES.grade(_subTugasId, _subKelompokId), {
      method: 'POST',
      headers: { 'Accept':'application/json','Content-Type':'application/json','X-CSRF-TOKEN': CSRF },
      body: JSON.stringify(payload),
    });
    const j = await r.json();
    showToast(r.ok ? 'success' : 'error', j.message || (r.ok ? 'Tersimpan.' : 'Gagal.'));
  } catch { showToast('error', 'Koneksi gagal.'); }
  finally { btn.disabled = false; }
}

/* ── AI Grade ── */
async function runAiGrade(anggotaId, btn) {
  btn.disabled = true;
  btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1"></i>Menilai…';

  try {
    const fd = new FormData();
    fd.append('anggota_id', anggotaId);
    const r = await fetch(ROUTES.aiGrade(_subTugasId, _subKelompokId), {
      method: 'POST',
      headers: { 'Accept':'application/json','X-CSRF-TOKEN': CSRF },
      body: fd,
    });
    const j = await r.json();
    if (!r.ok) { showToast('error', j.error || 'AI gagal.'); return; }

    // Isi hasil ke form
    const nilaiInp   = document.getElementById('g-ang-nilai-'   + anggotaId);
    const catatanInp = document.getElementById('g-ang-catatan-' + anggotaId);
    if (nilaiInp)   nilaiInp.value   = j.nilai;
    if (catatanInp) catatanInp.value = j.catatan;

    showToast('success', `AI: Nilai ${j.nilai} — berhasil diisi.`);
  } catch { showToast('error', 'Koneksi gagal.'); }
  finally {
    btn.disabled = false;
    btn.innerHTML = '<i class="fa-solid fa-wand-magic-sparkles mr-1"></i>AI Grade';
  }
}

function escH(s) {
  return String(s || '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

/* ── Spinner helpers ── */
function setLoading(btnId, loading) {
  const el = document.getElementById(btnId);
  const txt = document.getElementById(btnId + '-text');
  const spn = document.getElementById(btnId + '-spin');
  if (txt) txt.classList.toggle('hidden', loading);
  if (spn) spn.classList.toggle('hidden', !loading);
  if (el)  el.disabled = loading;
}

/* ══════════════════════════════════ */
/*  TABS                              */
/* ══════════════════════════════════ */
function switchTab(tab) {
  ['kelompok', 'individu'].forEach(t => {
    document.getElementById('tab-' + t)?.classList.toggle('active', t === tab);
    const c = document.getElementById('content-' + t);
    if (c) c.classList.toggle('hidden', t !== tab);
  });
}

/* ══════════════════════════════════ */
/*  ACCORDION                         */
/* ══════════════════════════════════ */
function toggleTugas(id) {
  const body    = document.getElementById('tugas-body-' + id);
  const chevron = document.querySelector('#chevron-' + id + ' i');
  if (!body) return;
  const isOpen = body.classList.toggle('open');
  if (chevron) chevron.style.transform = isOpen ? 'rotate(90deg)' : '';
}

/* ══════════════════════════════════ */
/*  TUGAS MODAL                       */
/* ══════════════════════════════════ */
/* ── Tipe change ── */
function onTipeChange() {
  const isIndividu = document.getElementById('tipe-individu').checked;
  document.getElementById('soal-section').style.display = isIndividu ? '' : 'none';
  document.getElementById('modal-tugas-title').textContent =
    isIndividu ? 'Buat Tugas Individu' : 'Buat Tugas Kelompok';
}

/* ── Soal editor commands ── */
function soalCmd(cmd, val = null) {
  document.execCommand(cmd, false, val);
  document.getElementById('soal-editor').focus();
}

let _soalSavedRange = null;
function triggerSoalImg() {
  const sel = window.getSelection();
  if (sel && sel.rangeCount) _soalSavedRange = sel.getRangeAt(0).cloneRange();
  document.getElementById('soal-img-inp').click();
}
async function handleSoalImg(input) {
  const file = input.files[0]; if (!file) return;
  input.value = '';
  const progress = document.getElementById('soal-img-progress');
  const btn = document.getElementById('soal-img-btn');
  btn.disabled = true; progress.style.display = 'block';
  try {
    const fd = new FormData(); fd.append('gambar', file);
    const r = await fetch(ROUTES.uploadSoalGambar, {
      method:'POST', headers:{'Accept':'application/json','X-CSRF-TOKEN':CSRF}, body:fd,
    });
    const j = await r.json();
    if (!r.ok) { showToast('error', j.message || 'Gagal mengunggah.'); return; }
    const img = document.createElement('img'); img.src = j.url; img.alt = file.name;
    const ed = document.getElementById('soal-editor'); ed.focus();
    const sel = window.getSelection();
    if (_soalSavedRange) { sel.removeAllRanges(); sel.addRange(_soalSavedRange); }
    if (sel && sel.rangeCount) {
      const range = sel.getRangeAt(0); range.deleteContents(); range.insertNode(img);
      range.setStartAfter(img); range.collapse(true); sel.removeAllRanges(); sel.addRange(range);
    } else { ed.appendChild(img); }
    _soalSavedRange = null;
    showToast('success', 'Gambar disisipkan.');
  } catch { showToast('error', 'Gagal mengunggah gambar.'); }
  finally { btn.disabled = false; progress.style.display = 'none'; }
}

function openTugasModal(tugasId = null, judul = '', deskripsi = '', deadline = '', status = 'draft', kelasId = null, tipe = null, soal = '') {
  document.getElementById('tugas-id').value        = tugasId ?? '';
  document.getElementById('tugas-judul').value     = judul || '';
  document.getElementById('tugas-deskripsi').value = deskripsi || '';
  document.getElementById('tugas-deadline').value  = deadline || '';
  document.getElementById('tugas-status').value    = status || 'draft';
  if (kelasId) document.getElementById('tugas-kelas-id').value = kelasId;

  // Determine effective tipe
  const effectiveTipe  = tipe || 'kelompok';
  const isIndividu     = effectiveTipe === 'individu';
  // When tipe is explicitly forced (from tab button) or editing, hide the tipe row
  // so user can't accidentally switch. Show it only when creating without a forced tipe.
  const tipeLocked = !!tugasId || tipe !== null;
  document.getElementById('tipe-row').style.display = tipeLocked ? 'none' : '';
  document.getElementById('tipe-kelompok').checked  = !isIndividu;
  document.getElementById('tipe-individu').checked  = isIndividu;
  document.getElementById('tipe-kelompok').disabled = tipeLocked;
  document.getElementById('tipe-individu').disabled = tipeLocked;
  document.getElementById('soal-section').style.display = isIndividu ? '' : 'none';

  // Soal editor
  document.getElementById('soal-editor').innerHTML = soal || '';

  // selesai option
  const selOpt = document.querySelector('#tugas-status option[value="selesai"]');
  if (selOpt) selOpt.style.display = tugasId ? '' : 'none';

  document.getElementById('modal-tugas-title').textContent = tugasId
    ? 'Edit Tugas'
    : (isIndividu ? 'Buat Tugas Individu' : 'Buat Tugas Kelompok');
  openModal('modal-tugas');
  setTimeout(() => document.getElementById('tugas-judul').focus(), 100);
}

async function saveTugas() {
  const id      = document.getElementById('tugas-id').value;
  const judul   = document.getElementById('tugas-judul').value.trim();
  const kelasId = document.getElementById('tugas-kelas-id').value;
  const tipe    = document.querySelector('input[name="tugas-tipe"]:checked')?.value || 'kelompok';

  if (!judul) { showToast('error', 'Judul tugas wajib diisi.'); return; }
  if (!kelasId) { showToast('error', 'Kelas tidak ditemukan.'); return; }

  if (tipe === 'individu') {
    const soalEl = document.getElementById('soal-editor');
    if (!soalEl.textContent.trim()) { showToast('error', 'Soal tugas individu wajib diisi.'); return; }
  }

  setLoading('btn-save-tugas', true);

  const body = new FormData();
  body.append('judul',     judul);
  body.append('deskripsi', document.getElementById('tugas-deskripsi').value);
  body.append('kelas_id',  kelasId);
  body.append('deadline',  document.getElementById('tugas-deadline').value);
  body.append('status',    document.getElementById('tugas-status').value);
  body.append('tipe',      tipe);
  if (tipe === 'individu') {
    body.append('soal', document.getElementById('soal-editor').innerHTML);
  }
  if (id) body.append('_method', 'PUT');

  try {
    const url = id ? ROUTES.tugasUpdate(id) : ROUTES.tugasStore;
    const r   = await fetch(url, {
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
      body,
    });
    const j = await r.json();
    if (!r.ok) { showToast('error', j.message || 'Gagal menyimpan.'); return; }
    showToast('success', j.message);
    closeModal('modal-tugas');
    location.reload();
  } catch { showToast('error', 'Terjadi kesalahan.'); }
  finally  { setLoading('btn-save-tugas', false); }
}

async function deleteTugas(id, judul) {
  if (!confirm(`Hapus tugas "${judul}"?\n\nSemua kelompok di dalamnya juga akan dihapus.`)) return;
  try {
    const fd = new FormData();
    fd.append('_method', 'DELETE');
    const r = await fetch(ROUTES.tugasDestroy(id), {
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
      body: fd,
    });
    const j = await r.json();
    if (!r.ok) { showToast('error', j.message || 'Gagal menghapus.'); return; }
    showToast('success', j.message);
    document.getElementById('tugas-card-' + id)?.remove();
  } catch { showToast('error', 'Terjadi kesalahan.'); }
}

/* ══════════════════════════════════ */
/*  KELOMPOK MODAL                    */
/* ══════════════════════════════════ */
function openKelompokModal(tugasId, kelompokId = null, nama = '', ketuaId = null) {
  document.getElementById('kelompok-tugas-id').value = tugasId;
  document.getElementById('kelompok-id').value       = kelompokId ?? '';
  document.getElementById('kelompok-nama').value     = nama;
  document.getElementById('kelompok-ketua').value    = ketuaId ?? '';

  document.getElementById('modal-kelompok-title').textContent = kelompokId ? 'Edit Kelompok' : 'Tambah Kelompok';

  openModal('modal-kelompok');
  setTimeout(() => document.getElementById('kelompok-nama').focus(), 100);
}

async function saveKelompok() {
  const tugasId    = document.getElementById('kelompok-tugas-id').value;
  const kelompokId = document.getElementById('kelompok-id').value;
  const nama       = document.getElementById('kelompok-nama').value.trim();

  if (!nama) { showToast('error', 'Nama kelompok wajib diisi.'); return; }

  setLoading('btn-save-kelompok', true);

  const body = new FormData();
  body.append('nama_kelompok', nama);
  const ketuaVal = document.getElementById('kelompok-ketua').value;
  if (ketuaVal) body.append('ketua_mahasiswa_id', ketuaVal);
  if (kelompokId) body.append('_method', 'PUT');

  try {
    const url = kelompokId
      ? ROUTES.kelompokUpdate(tugasId, kelompokId)
      : ROUTES.kelompokStore(tugasId);
    const r = await fetch(url, {
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
      body,
    });
    const j = await r.json();
    if (!r.ok) { showToast('error', j.message || 'Gagal menyimpan.'); return; }
    showToast('success', j.message);
    closeModal('modal-kelompok');
    location.reload();
  } catch { showToast('error', 'Terjadi kesalahan.'); }
  finally  { setLoading('btn-save-kelompok', false); }
}

async function deleteKelompok(tugasId, kelompokId, nama) {
  if (!confirm(`Hapus kelompok "${nama}"?`)) return;
  try {
    const fd = new FormData();
    fd.append('_method', 'DELETE');
    const r = await fetch(ROUTES.kelompokDestroy(tugasId, kelompokId), {
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
      body: fd,
    });
    const j = await r.json();
    if (!r.ok) { showToast('error', j.message || 'Gagal menghapus.'); return; }
    showToast('success', j.message);
    document.getElementById('kelompok-card-' + kelompokId)?.remove();
  } catch { showToast('error', 'Terjadi kesalahan.'); }
}

/* ── Close modal on backdrop click ── */
document.querySelectorAll('.modal-backdrop').forEach(el => {
  el.addEventListener('click', e => { if (e.target === el) el.classList.remove('open'); });
});

/* ══════════════════════════════════════════════════════════════ */
/*  INDIVIDU SUBMISSIONS MODAL                                    */
/* ══════════════════════════════════════════════════════════════ */
let _indTugasId  = null;
let _indData     = null;

async function openIndividuSubmissions(tugasId) {
  _indTugasId = tugasId;
  _indData    = null;

  // Reset
  document.getElementById('ind-loader').style.display = 'flex';
  document.getElementById('ind-table-wrap').style.display = 'none';
  document.getElementById('ind-soal-panel').style.display = 'none';
  document.getElementById('btn-ai-grade-all').style.display = 'none';
  document.getElementById('btn-save-all-grades').style.display = 'none';
  document.getElementById('ind-modal-title').textContent = 'Memuat…';
  document.getElementById('ind-modal-meta').textContent = '';
  document.getElementById('ind-catatan-umum-box').style.display = 'none';
  document.getElementById('ind-tbody').innerHTML = '';

  openModal('modal-individu');

  try {
    const r = await fetch(ROUTES.individuSubmissions(tugasId), {
      headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
    });
    if (!r.ok) throw new Error('HTTP ' + r.status);
    _indData = await r.json();
    renderIndividu(_indData);
  } catch(e) {
    document.getElementById('ind-loader').innerHTML =
      `<div class="text-center text-[13px]" style="color:var(--muted)">Gagal memuat: ${escH(e.message)}</div>`;
  }
}

function renderIndividu(d) {
  document.getElementById('ind-modal-title').textContent = d.tugas.judul;
  document.getElementById('ind-modal-meta').textContent  =
    `${d.submitted_count} dari ${d.total} mahasiswa dikumpulkan`;

  // Soal
  if (d.tugas.soal) {
    document.getElementById('ind-soal-content').innerHTML = d.tugas.soal;
    document.getElementById('ind-soal-panel').style.display = '';
  }

  // Render table rows
  const tbody = document.getElementById('ind-tbody');
  tbody.innerHTML = '';
  d.submissions.forEach((s, i) => {
    const tr = document.createElement('tr');
    tr.setAttribute('data-mhs-id', s.mahasiswa_id);
    const statusBadge = s.status_submit === 'submitted'
      ? `<span class="badge b-submitted"><i class="fa-solid fa-check text-[8px]"></i>Dikumpulkan</span>${s.submitted_at ? `<div style="font-size:9px;color:var(--muted);margin-top:2px">${escH(s.submitted_at)}</div>` : ''}`
      : `<span class="badge b-belum">Belum</span>`;
    const pdfLink = s.pdf_url
      ? `<a href="${escH(s.pdf_url)}" target="_blank" style="color:var(--ac);font-size:11px;display:inline-flex;align-items:center;gap:4px"><i class="fa-solid fa-file-pdf" style="color:#f87171"></i>Lihat</a>`
      : `<span style="color:var(--muted);font-size:11px">—</span>`;
    const flagDuplikat = s.flag_duplikat ? `<span style="font-size:9px;font-weight:700;padding:1px 5px;border-radius:4px;background:rgba(239,68,68,.15);color:#f87171;margin-left:4px">Duplikat</span>` : '';

    tr.innerHTML = `
      <td style="color:var(--muted);font-size:11px">${i + 1}</td>
      <td>
        <div style="font-weight:600;font-size:12px">${escH(s.nama)}${flagDuplikat}</div>
        <div style="font-size:10px;color:var(--muted)">${escH(s.nim)}</div>
      </td>
      <td>${statusBadge}</td>
      <td>${pdfLink}</td>
      <td>
        <input type="number" class="ind-nilai-inp" id="ind-nilai-${s.mahasiswa_id}"
               min="0" max="100" value="${s.nilai ?? ''}" placeholder="—"
               ${s.status_submit !== 'submitted' ? 'disabled style="opacity:.4"' : ''}>
      </td>
      <td>
        <textarea class="ind-catatan-inp" id="ind-catatan-${s.mahasiswa_id}"
                  rows="2" placeholder="${s.catatan_ai ? 'AI: ' + escH(s.catatan_ai.substring(0,60)) + '…' : 'Catatan…'}"
                  ${s.status_submit !== 'submitted' ? 'disabled style="opacity:.4"' : ''}>${escH(s.catatan_instruktur ?? '')}</textarea>
      </td>`;
    tbody.appendChild(tr);
  });

  document.getElementById('ind-loader').style.display = 'none';
  document.getElementById('ind-table-wrap').style.display = '';
  document.getElementById('btn-save-all-grades').style.display = '';
  if (d.submitted_count > 0) {
    document.getElementById('btn-ai-grade-all').style.display = '';
  }
}

async function saveAllGrades() {
  if (!_indData) return;
  const btn = document.getElementById('btn-save-all-grades');
  btn.disabled = true;

  const grades = _indData.submissions.map(s => ({
    mahasiswa_id: s.mahasiswa_id,
    nilai:   document.getElementById('ind-nilai-'   + s.mahasiswa_id)?.value !== ''
               ? parseInt(document.getElementById('ind-nilai-' + s.mahasiswa_id).value) : null,
    catatan: document.getElementById('ind-catatan-' + s.mahasiswa_id)?.value || null,
  })).filter(g => g.nilai !== null || g.catatan);

  if (!grades.length) { showToast('error', 'Belum ada nilai yang diisi.'); btn.disabled = false; return; }

  try {
    const r = await fetch(ROUTES.gradeAll(_indTugasId), {
      method: 'POST',
      headers: { 'Accept':'application/json','Content-Type':'application/json','X-CSRF-TOKEN': CSRF },
      body: JSON.stringify({ grades }),
    });
    const j = await r.json();
    showToast(r.ok ? 'success' : 'error', j.message || (r.ok ? 'Tersimpan.' : 'Gagal.'));
  } catch { showToast('error', 'Koneksi gagal.'); }
  finally { btn.disabled = false; }
}

async function runAiGradeAll() {
  const btn = document.getElementById('btn-ai-grade-all');
  btn.disabled = true;
  btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1.5"></i>Menilai…';

  try {
    const r = await fetch(ROUTES.aiGradeIndividu(_indTugasId), {
      method: 'POST',
      headers: { 'Accept':'application/json','X-CSRF-TOKEN': CSRF },
    });
    const j = await r.json();
    if (!r.ok) { showToast('error', j.error || 'AI gagal.'); return; }

    // Isi nilai & catatan dari AI ke form
    j.grades.forEach(g => {
      const nilaiEl   = document.getElementById('ind-nilai-'   + g.mahasiswa_id);
      const catatanEl = document.getElementById('ind-catatan-' + g.mahasiswa_id);
      if (nilaiEl && !nilaiEl.disabled)   nilaiEl.value   = g.nilai;
      if (catatanEl && !catatanEl.disabled) catatanEl.value = g.catatan_ai || '';
      // Flag duplikat
      if (g.flag_duplikat) {
        const row = document.querySelector(`tr[data-mhs-id="${g.mahasiswa_id}"]`);
        if (row) {
          const nameCell = row.querySelector('td:nth-child(2) div:first-child');
          if (nameCell && !nameCell.querySelector('.duplikat-flag')) {
            nameCell.insertAdjacentHTML('beforeend',
              `<span class="duplikat-flag" style="font-size:9px;font-weight:700;padding:1px 5px;border-radius:4px;background:rgba(239,68,68,.15);color:#f87171;margin-left:4px">Duplikat</span>`);
          }
        }
      }
    });

    // Catatan umum
    if (j.catatan_umum) {
      document.getElementById('ind-catatan-umum').textContent = j.catatan_umum;
      document.getElementById('ind-catatan-umum-box').style.display = '';
    }

    showToast('success', `AI selesai menilai ${j.grades.length} mahasiswa.`);
  } catch { showToast('error', 'Koneksi gagal.'); }
  finally {
    btn.disabled = false;
    btn.innerHTML = '<i class="fa-solid fa-wand-magic-sparkles mr-1.5"></i>Nilai Semua dengan AI';
  }
}
</script>
@endpush
