@extends('layouts.instruktur')
@section('title', 'Ujian')
@section('page-title', 'Ujian')

@push('styles')
<style>
/* ── Layout ── */
.uj-layout { display:grid; grid-template-columns:280px 1fr; gap:20px; align-items:start; }
@media(max-width:900px){ .uj-layout { grid-template-columns:1fr; } }

/* ── Sidebar kelas ── */
.kelas-item { display:flex; align-items:flex-start; gap:10px; padding:10px 12px; border-radius:11px; cursor:pointer; transition:all .15s; border:1px solid transparent; text-decoration:none; color:inherit; }
.kelas-item:hover { background:var(--surface2); }
.kelas-item.active { background:var(--ac); border-color:var(--ac); color:#fff; }
.kelas-item.active .kelas-meta { color:rgba(255,255,255,.75); }
.kelas-icon { width:36px; height:36px; border-radius:10px; display:grid; place-items:center; font-size:13px; flex-shrink:0; margin-top:1px; }

/* ── Ujian card ── */
.ujian-card { background:var(--surface); border:1px solid var(--border); border-radius:14px; overflow:hidden; transition:border-color .15s; }
.ujian-card:hover { border-color:var(--ac); }
.ujian-head { padding:14px 16px 10px; }
.ujian-title { font-size:15px; font-weight:600; color:var(--text); margin:0 0 6px; }
.ujian-meta-row { display:flex; flex-wrap:wrap; gap:6px; align-items:center; }
.ujian-body { padding:10px 16px 14px; border-top:1px solid var(--border); display:grid; grid-template-columns:repeat(auto-fill,minmax(140px,1fr)); gap:8px; }
.ujian-stat { display:flex; align-items:center; gap:6px; font-size:12.5px; color:var(--sub); }
.ujian-actions { padding:10px 16px; border-top:1px solid var(--border); display:flex; gap:8px; justify-content:flex-end; }

/* ── Status badges ── */
.bdg-draft   { background:rgba(100,116,139,.12); color:#94a3b8; }
.bdg-aktif   { background:rgba(16,185,129,.12);  color:#10b981; }
.bdg-selesai { background:rgba(99,102,241,.12);  color:#818cf8; }
.bdg-essay   { background:rgba(245,158,11,.12);  color:#f59e0b; }
.bdg-pg      { background:rgba(6,182,212,.12);   color:#22d3ee; }
.bdg-acak    { background:rgba(168,85,247,.12);  color:#c084fc; }

/* ── Modal ── */
.modal-backdrop { position:fixed; inset:0; background:rgba(0,0,0,.6); z-index:100; display:flex; align-items:center; justify-content:center; padding:16px; backdrop-filter:blur(2px); }
.modal-box { background:var(--surface); border:1px solid var(--border); border-radius:22px; width:100%; max-width:680px; max-height:92vh; display:flex; flex-direction:column; overflow:hidden; box-shadow:0 24px 60px rgba(0,0,0,.25); }
.modal-head { padding:18px 22px 14px; display:flex; align-items:center; gap:12px; flex-shrink:0; }
.modal-head-icon { width:38px; height:38px; border-radius:11px; background:rgba(var(--ac-rgb,99,102,241),.12); display:grid; place-items:center; font-size:15px; flex-shrink:0; }
.modal-head-title { flex:1; font-size:16px; font-weight:700; color:var(--text); }
.modal-head-sub { font-size:12px; color:var(--muted); margin-top:1px; }
.modal-close { width:32px; height:32px; border-radius:8px; border:none; background:var(--surface2); color:var(--muted); cursor:pointer; display:grid; place-items:center; font-size:13px; transition:all .15s; flex-shrink:0; }
.modal-close:hover { background:var(--border); color:var(--text); }
.modal-divider { height:1px; background:var(--border); flex-shrink:0; }
.modal-body { padding:20px 22px; overflow-y:auto; flex:1; }
.modal-foot { padding:14px 22px; border-top:1px solid var(--border); display:flex; gap:8px; justify-content:flex-end; flex-shrink:0; background:var(--surface); }

/* ── Form section blocks ── */
.fs { border:1px solid var(--border); border-radius:14px; overflow:hidden; margin-bottom:14px; }
.fs-head { padding:12px 16px; display:flex; align-items:center; justify-content:space-between; background:var(--surface2); }
.fs-head-left { display:flex; align-items:center; gap:10px; }
.fs-icon { width:30px; height:30px; border-radius:8px; display:grid; place-items:center; font-size:12px; flex-shrink:0; }
.fs-title { font-size:13px; font-weight:700; color:var(--text); }
.fs-body { padding:16px; }
.fs-divider { height:1px; background:var(--border); margin:12px 0; }

/* ── Grid ── */
.fg2 { display:grid; grid-template-columns:1fr 1fr; gap:12px; }
.fg3 { display:grid; grid-template-columns:1fr 1fr 1fr; gap:12px; }
@media(max-width:560px){ .fg2,.fg3 { grid-template-columns:1fr; } }

/* ── Override f-input border-radius for suffix combos ── */
.f-input { border-radius:10px; }

/* ── Input with suffix ── */
.input-suffix-wrap { display:flex; }
.input-suffix-wrap .f-input { flex:1; border-radius:10px 0 0 10px !important; border-right:none !important; }
.input-suffix-wrap .suffix { padding:0 12px; background:var(--surface2); border:1px solid var(--border); border-left:none; border-radius:0 10px 10px 0; display:flex; align-items:center; font-size:12.5px; color:var(--muted); font-weight:500; white-space:nowrap; }

/* ── Toggle switch ── */
.sw { position:relative; display:inline-flex; align-items:center; gap:8px; cursor:pointer; user-select:none; }
.sw input { position:absolute; opacity:0; width:0; height:0; }
.sw-track { width:40px; height:22px; border-radius:11px; background:var(--border); transition:background .2s; flex-shrink:0; position:relative; }
.sw input:checked ~ .sw-track { background:var(--ac); }
.sw-track::after { content:''; position:absolute; top:3px; left:3px; width:16px; height:16px; border-radius:50%; background:#fff; transition:transform .2s; box-shadow:0 1px 3px rgba(0,0,0,.25); }
.sw input:checked ~ .sw-track::after { transform:translateX(18px); }
.sw-label { font-size:13.5px; color:var(--text); font-weight:500; }
.sw-sub { font-size:11.5px; color:var(--muted); margin-top:1px; }

/* ── Section header toggle ── */
.section-toggle-wrap { display:flex; align-items:center; justify-content:space-between; }
.section-toggle-label { font-size:13.5px; font-weight:600; color:var(--text); }
.section-toggle-sub { font-size:11.5px; color:var(--muted); }

/* ── Soal picker ── */
.soal-picker-wrap { border:1px solid var(--border); border-radius:10px; overflow:hidden; }
.soal-picker-toolbar { padding:8px 12px; background:var(--surface2); display:flex; align-items:center; justify-content:space-between; border-bottom:1px solid var(--border); }
.soal-picker-toolbar-label { font-size:11.5px; font-weight:600; color:var(--muted); }
.soal-picker-list { max-height:200px; overflow-y:auto; }
.soal-pick-item { display:flex; align-items:flex-start; gap:10px; padding:9px 12px; border-bottom:1px solid var(--border); cursor:pointer; transition:background .1s; }
.soal-pick-item:last-child { border-bottom:none; }
.soal-pick-item:hover { background:var(--surface2); }
.soal-pick-item input[type=checkbox] { margin-top:3px; flex-shrink:0; accent-color:var(--ac); }
.soal-pick-num { width:22px; height:22px; border-radius:6px; display:grid; place-items:center; font-size:10px; font-weight:700; flex-shrink:0; }
.pick-pertanyaan { font-size:13px; color:var(--text); line-height:1.4; }
.pick-meta { font-size:11px; color:var(--muted); margin-top:2px; }

/* ── Selected summary bar ── */
.sel-bar { display:flex; align-items:center; justify-content:space-between; padding:8px 12px; border-radius:9px; margin-top:8px; font-size:12.5px; }
.sel-bar.has-sel { background:rgba(var(--ac-rgb,99,102,241),.08); border:1px solid rgba(var(--ac-rgb,99,102,241),.18); }
.sel-bar.no-sel  { background:var(--surface2); border:1px solid var(--border); color:var(--muted); }

/* ── Count display ── */
.count-chip { display:inline-flex; align-items:center; gap:4px; font-size:11.5px; font-weight:600; padding:2px 8px; border-radius:6px; }

/* ── Rich text editor (deskripsi) ── */
.rte-wrap { border:1px solid var(--border); border-radius:10px; overflow:hidden; background:var(--surface2); transition:border-color .15s; }
.rte-wrap:focus-within { border-color:var(--ac); }
.rte-toolbar { display:flex; flex-wrap:wrap; gap:2px; padding:7px 9px; border-bottom:1px solid var(--border); background:var(--surface2); }
.rte-btn { width:28px; height:28px; border-radius:7px; border:none; cursor:pointer; background:transparent; color:var(--muted); font-size:12px; display:grid; place-items:center; transition:background .12s,color .12s; flex-shrink:0; }
.rte-btn:hover { background:var(--border); color:var(--text); }
.rte-btn.active { background:var(--ac-lt); color:var(--ac); }
.rte-sep { width:1px; background:var(--border); margin:3px 2px; align-self:stretch; }
.rte-body { min-height:90px; max-height:180px; overflow-y:auto; padding:12px 14px; outline:none; font-size:13px; line-height:1.7; color:var(--text); }
.rte-body:empty::before { content:attr(data-placeholder); color:var(--muted); pointer-events:none; }
.rte-body b,.rte-body strong { font-weight:700; }
.rte-body i,.rte-body em { font-style:italic; }
.rte-body u { text-decoration:underline; }
.rte-body ul { list-style:disc; padding-left:1.4em; margin:.2em 0; }
.rte-body ol { list-style:decimal; padding-left:1.4em; margin:.2em 0; }
.rte-body a { color:var(--ac); text-decoration:underline; }

/* ── Empty state ── */
.empty-state { text-align:center; padding:60px 20px; color:var(--muted); }
.empty-state i { font-size:40px; margin-bottom:12px; opacity:.4; }
</style>
@endpush

@section('content')
<div class="uj-layout">

    {{-- ── Sidebar kelas ─────────────────────────────────────────── --}}
    <div>
        <div class="card" style="padding:16px;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
                <span style="font-size:13px;font-weight:700;color:var(--text);">Kelas</span>
                <span class="count-chip" style="background:var(--surface2);color:var(--muted);">{{ $kelasList->count() }}</span>
            </div>

            @forelse($kelasList as $k)
            <a href="{{ route('instruktur.ujian.index', ['kelas' => $k->id]) }}"
               class="kelas-item {{ $kelas && $kelas->id == $k->id ? 'active' : '' }}">
                <div class="kelas-icon {{ $kelas && $kelas->id == $k->id ? '' : 'bg-surface2 text-muted' }}"
                     style="{{ $kelas && $kelas->id == $k->id ? 'background:rgba(255,255,255,.2);color:#fff;' : '' }}">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
                <div style="min-width:0;">
                    <div style="font-size:13px;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                        {{ $k->mataKuliah->nama }}
                    </div>
                    <div class="kelas-meta" style="font-size:11px;color:var(--muted);margin-top:1px;">
                        {{ $k->kodeDisplay }} · {{ $k->periodeAkademik->nama ?? '-' }}
                    </div>
                </div>
            </a>
            @empty
            <p style="font-size:13px;color:var(--muted);padding:8px 0;">Belum ada kelas.</p>
            @endforelse
        </div>
    </div>

    {{-- ── Main panel ──────────────────────────────────────────── --}}
    <div>
        @if($kelas)
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;flex-wrap:wrap;gap:10px;">
            <div>
                <h2 style="font-size:16px;font-weight:700;color:var(--text);margin:0;">
                    {{ $kelas->mataKuliah->nama }}
                </h2>
                <p style="font-size:12.5px;color:var(--muted);margin:2px 0 0;">
                    {{ $kelas->kodeDisplay }} · {{ $kelas->periodeAkademik->nama ?? '-' }}
                </p>
            </div>
            <button class="btn btn-primary btn-sm" onclick="openCreate()">
                <i class="fas fa-plus"></i> Buat Ujian
            </button>
        </div>

        @forelse($ujianList as $u)
        @php
            $essayPool = $u->soalPool->where('tipe','essay');
            $pgPool    = $u->soalPool->where('tipe','pilihan_ganda');
            $tampilEssay = $u->ada_essay ? ($u->jumlah_soal_essay ?? $essayPool->count()) : 0;
            $tampilPg    = $u->ada_pg    ? ($u->jumlah_soal_pg    ?? $pgPool->count())    : 0;
        @endphp
        <div class="ujian-card" style="margin-bottom:12px;">
            <div class="ujian-head">
                <div style="display:flex;align-items:flex-start;gap:10px;">
                    <div style="flex:1;min-width:0;">
                        <p class="ujian-title">{{ $u->judul }}</p>
                        <div class="ujian-meta-row">
                            <span class="badge bdg-{{ $u->status }}">{{ $u->status_label }}</span>
                            @if($u->ada_essay)
                            <span class="badge bdg-essay">Essay</span>
                            @endif
                            @if($u->ada_pg)
                            <span class="badge bdg-pg">Pilihan Ganda</span>
                            @endif
                            @if($u->acak_soal_essay || $u->acak_soal_pg)
                            <span class="badge bdg-acak"><i class="fas fa-random fa-xs me-1"></i>Diacak</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="ujian-body">
                <div class="ujian-stat">
                    <i class="fas fa-calendar-alt" style="color:var(--ac);width:14px;"></i>
                    <span>{{ $u->waktu_mulai->format('d M Y, H:i') }}</span>
                </div>
                <div class="ujian-stat">
                    <i class="fas fa-calendar-check" style="color:#10b981;width:14px;"></i>
                    <span>{{ $u->waktu_selesai->format('d M Y, H:i') }}</span>
                </div>
                <div class="ujian-stat">
                    <i class="fas fa-clock" style="color:#f59e0b;width:14px;"></i>
                    <span>{{ $u->durasi }} menit</span>
                </div>
                @if($u->ada_essay)
                <div class="ujian-stat">
                    <i class="fas fa-pen-alt" style="color:#f59e0b;width:14px;"></i>
                    <span>{{ $tampilEssay }} / {{ $essayPool->count() }} soal essay</span>
                </div>
                @endif
                @if($u->ada_pg)
                <div class="ujian-stat">
                    <i class="fas fa-list-ul" style="color:#22d3ee;width:14px;"></i>
                    <span>{{ $tampilPg }} / {{ $pgPool->count() }} soal PG</span>
                </div>
                @endif
            </div>
            <div class="ujian-actions">
                @if($u->status === 'aktif')
                <a href="{{ route('instruktur.ujian.pengawas', $u->id) }}"
                    class="btn btn-sm"
                    style="background:rgba(139,92,246,.15);color:#a78bfa;border:1px solid rgba(139,92,246,.3);">
                    <i class="fas fa-eye"></i> Pengawas
                </a>
                @endif
                <a href="{{ route('instruktur.ujian.penilaian', $u->id) }}"
                    class="btn btn-sm"
                    style="background:rgba(16,185,129,.12);color:#10b981;border:1px solid rgba(16,185,129,.25);">
                    <i class="fas fa-list-check"></i> Penilaian
                </a>
                <button class="btn btn-ghost btn-sm" onclick="openEdit({{ $u->id }})">
                    <i class="fas fa-edit"></i> Edit
                </button>
                <button class="btn btn-ghost btn-sm text-danger" onclick="deleteUjian({{ $u->id }}, '{{ addslashes($u->judul) }}')">
                    <i class="fas fa-trash"></i> Hapus
                </button>
            </div>
        </div>
        @empty
        <div class="empty-state card">
            <i class="fas fa-file-alt d-block mx-auto"></i>
            <p style="font-weight:600;margin:0;">Belum ada ujian</p>
            <p style="font-size:13px;margin:4px 0 0;">Klik "Buat Ujian" untuk membuat ujian baru.</p>
        </div>
        @endforelse

        @else
        <div class="empty-state card">
            <i class="fas fa-chalkboard-teacher d-block mx-auto"></i>
            <p style="font-weight:600;margin:0;">Pilih kelas</p>
            <p style="font-size:13px;margin:4px 0 0;">Pilih kelas dari daftar di sebelah kiri.</p>
        </div>
        @endif
    </div>
</div>

{{-- ════════════════════════════════════════════
     Modal Buat / Edit Ujian
════════════════════════════════════════════ --}}
<div id="modal-ujian" class="modal-backdrop" style="display:none;" onclick="if(event.target===this)closeModal()">
    <div class="modal-box">

        {{-- Header --}}
        <div class="modal-head">
            <div class="modal-head-icon" id="modal-head-icon" style="color:var(--ac);">
                <i class="fas fa-plus"></i>
            </div>
            <div style="flex:1;">
                <div class="modal-head-title" id="modal-title">Buat Ujian</div>
                <div class="modal-head-sub" id="modal-head-sub">Isi detail ujian dan pilih soal dari bank soal</div>
            </div>
            <button class="modal-close" onclick="closeModal()"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-divider"></div>

        <div class="modal-body">
            <form id="form-ujian" onsubmit="submitUjian(event)">
                <input type="hidden" id="ujian-id">
                <input type="hidden" id="ujian-kelas-id" value="{{ $kelas?->id }}">

                {{-- ① Informasi Dasar --}}
                <div class="fs">
                    <div class="fs-head">
                        <div class="fs-head-left">
                            <div class="fs-icon" style="background:rgba(99,102,241,.12);color:#818cf8;">
                                <i class="fas fa-info-circle"></i>
                            </div>
                            <span class="fs-title">Informasi Ujian</span>
                        </div>
                    </div>
                    <div class="fs-body">
                        <div style="margin-bottom:12px;">
                            <label class="f-label">Judul Ujian <span style="color:#ef4444;">*</span></label>
                            <input type="text" id="f-judul" class="f-input" placeholder="Contoh: UTS Pemrograman Web" required>
                        </div>
                        <div>
                            <label class="f-label">Deskripsi / Petunjuk <span style="color:var(--muted);font-weight:400;text-transform:none;font-size:11px;">(opsional)</span></label>
                            {{-- Rich text editor --}}
                            <div class="rte-wrap">
                                <div class="rte-toolbar">
                                    <button type="button" class="rte-btn" title="Bold" onclick="rteCmd('bold')"><i class="fas fa-bold"></i></button>
                                    <button type="button" class="rte-btn" title="Italic" onclick="rteCmd('italic')"><i class="fas fa-italic"></i></button>
                                    <button type="button" class="rte-btn" title="Underline" onclick="rteCmd('underline')"><i class="fas fa-underline"></i></button>
                                    <div class="rte-sep"></div>
                                    <button type="button" class="rte-btn" title="Bullet list" onclick="rteCmd('insertUnorderedList')"><i class="fas fa-list-ul"></i></button>
                                    <button type="button" class="rte-btn" title="Numbered list" onclick="rteCmd('insertOrderedList')"><i class="fas fa-list-ol"></i></button>
                                    <div class="rte-sep"></div>
                                    <button type="button" class="rte-btn" title="Hapus format" onclick="rteCmd('removeFormat')"><i class="fas fa-remove-format"></i></button>
                                </div>
                                <div id="f-deskripsi-editor" class="rte-body" contenteditable="true"
                                     data-placeholder="Tulis petunjuk pengerjaan untuk mahasiswa…"></div>
                            </div>
                            <input type="hidden" id="f-deskripsi">
                        </div>
                    </div>
                </div>

                {{-- ② Waktu, Durasi & Status --}}
                <div class="fs">
                    <div class="fs-head">
                        <div class="fs-head-left">
                            <div class="fs-icon" style="background:rgba(245,158,11,.12);color:#f59e0b;">
                                <i class="fas fa-clock"></i>
                            </div>
                            <span class="fs-title">Waktu & Status</span>
                        </div>
                    </div>
                    <div class="fs-body">
                        <div class="fg2" style="margin-bottom:12px;">
                            <div>
                                <label class="f-label">Waktu Mulai <span style="color:#ef4444;">*</span></label>
                                <input type="datetime-local" id="f-waktu-mulai" class="f-input" required>
                            </div>
                            <div>
                                <label class="f-label">Waktu Selesai <span style="color:#ef4444;">*</span></label>
                                <input type="datetime-local" id="f-waktu-selesai" class="f-input" required>
                            </div>
                        </div>
                        <div class="fg2">
                            <div>
                                <label class="f-label">Durasi Pengerjaan <span style="color:#ef4444;">*</span></label>
                                <div class="input-suffix-wrap">
                                    <input type="number" id="f-durasi" class="f-input" min="1" max="480" placeholder="90" required>
                                    <span class="suffix">menit</span>
                                </div>
                                <small style="font-size:11px;color:var(--muted);">Waktu dihitung sejak mahasiswa mulai (maks 480)</small>
                            </div>
                            <div>
                                <label class="f-label">Status Ujian</label>
                                <select id="f-status" class="f-input">
                                    <option value="draft">🔒 Draft — Belum terlihat</option>
                                    <option value="aktif">✅ Aktif — Mahasiswa bisa mengerjakan</option>
                                    <option value="selesai">🏁 Selesai</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ③ Soal Essay --}}
                <div class="fs" id="section-essay">
                    <div class="fs-head">
                        <div class="fs-head-left">
                            <div class="fs-icon" style="background:rgba(245,158,11,.12);color:#f59e0b;">
                                <i class="fas fa-pen-alt"></i>
                            </div>
                            <div>
                                <div class="fs-title">Soal Essay</div>
                            </div>
                        </div>
                        <label class="sw">
                            <input type="checkbox" id="f-ada-essay" onchange="toggleEssayPanel()">
                            <span class="sw-track"></span>
                            <span class="sw-label" style="font-size:12.5px;color:var(--muted);">Sertakan</span>
                        </label>
                    </div>

                    <div id="panel-essay" style="display:none;">
                        <div class="fs-body" style="padding-top:14px;">

                            {{-- Soal Picker --}}
                            <label class="f-label" style="margin-bottom:6px;">Pilih Soal dari Bank Soal</label>
                            <div class="soal-picker-wrap">
                                <div class="soal-picker-toolbar">
                                    <span class="soal-picker-toolbar-label" id="essay-toolbar-label">Memuat…</span>
                                    <div style="display:flex;gap:6px;">
                                        <button type="button" class="btn btn-ghost btn-sm" style="font-size:11px;padding:2px 8px;" onclick="selectAll('essay-soal-list','essay')">Pilih Semua</button>
                                        <button type="button" class="btn btn-ghost btn-sm" style="font-size:11px;padding:2px 8px;" onclick="clearAll('essay-soal-list','essay')">Hapus Semua</button>
                                    </div>
                                </div>
                                <div id="essay-soal-list" class="soal-picker-list">
                                    <div style="padding:20px;text-align:center;color:var(--muted);font-size:13px;">
                                        <i class="fas fa-spinner fa-spin"></i> Memuat soal…
                                    </div>
                                </div>
                            </div>

                            {{-- Summary & Jumlah --}}
                            <div id="essay-sel-bar" class="sel-bar no-sel" style="margin-top:8px;">
                                <span id="essay-sel-text" style="color:var(--muted);">Belum ada soal dipilih</span>
                            </div>

                            <div class="fs-divider"></div>

                            <div class="fg2">
                                <div>
                                    <label class="f-label">Tampilkan Berapa Soal ke Mahasiswa?</label>
                                    <div class="input-suffix-wrap">
                                        <input type="number" id="f-jumlah-essay" class="f-input" min="1" placeholder="Semua">
                                        <span class="suffix" id="essay-max-suffix">/ —</span>
                                    </div>
                                    <small style="font-size:11px;color:var(--muted);">Kosongkan = tampilkan semua soal yang dipilih</small>
                                </div>
                                <div style="display:flex;flex-direction:column;justify-content:flex-end;padding-bottom:4px;">
                                    <label class="sw" style="align-items:flex-start;gap:10px;">
                                        <input type="checkbox" id="f-acak-essay" style="margin-top:2px;">
                                        <span class="sw-track" style="margin-top:2px;flex-shrink:0;"></span>
                                        <div>
                                            <div class="sw-label">Acak urutan soal</div>
                                            <div class="sw-sub">Setiap mahasiswa dapat urutan berbeda</div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ④ Soal Pilihan Ganda --}}
                <div class="fs" id="section-pg" style="margin-bottom:0;">
                    <div class="fs-head">
                        <div class="fs-head-left">
                            <div class="fs-icon" style="background:rgba(6,182,212,.12);color:#22d3ee;">
                                <i class="fas fa-list-ul"></i>
                            </div>
                            <div>
                                <div class="fs-title">Soal Pilihan Ganda</div>
                            </div>
                        </div>
                        <label class="sw">
                            <input type="checkbox" id="f-ada-pg" onchange="togglePgPanel()">
                            <span class="sw-track"></span>
                            <span class="sw-label" style="font-size:12.5px;color:var(--muted);">Sertakan</span>
                        </label>
                    </div>

                    <div id="panel-pg" style="display:none;">
                        <div class="fs-body" style="padding-top:14px;">

                            {{-- Soal Picker --}}
                            <label class="f-label" style="margin-bottom:6px;">Pilih Soal dari Bank Soal</label>
                            <div class="soal-picker-wrap">
                                <div class="soal-picker-toolbar">
                                    <span class="soal-picker-toolbar-label" id="pg-toolbar-label">Memuat…</span>
                                    <div style="display:flex;gap:6px;">
                                        <button type="button" class="btn btn-ghost btn-sm" style="font-size:11px;padding:2px 8px;" onclick="selectAll('pg-soal-list','pg')">Pilih Semua</button>
                                        <button type="button" class="btn btn-ghost btn-sm" style="font-size:11px;padding:2px 8px;" onclick="clearAll('pg-soal-list','pg')">Hapus Semua</button>
                                    </div>
                                </div>
                                <div id="pg-soal-list" class="soal-picker-list">
                                    <div style="padding:20px;text-align:center;color:var(--muted);font-size:13px;">
                                        <i class="fas fa-spinner fa-spin"></i> Memuat soal…
                                    </div>
                                </div>
                            </div>

                            {{-- Summary & Jumlah --}}
                            <div id="pg-sel-bar" class="sel-bar no-sel" style="margin-top:8px;">
                                <span id="pg-sel-text" style="color:var(--muted);">Belum ada soal dipilih</span>
                            </div>

                            <div class="fs-divider"></div>

                            <div class="fg2" style="margin-bottom:12px;">
                                <div>
                                    <label class="f-label">Tampilkan Berapa Soal ke Mahasiswa?</label>
                                    <div class="input-suffix-wrap">
                                        <input type="number" id="f-jumlah-pg" class="f-input" min="1" placeholder="Semua">
                                        <span class="suffix" id="pg-max-suffix">/ —</span>
                                    </div>
                                    <small style="font-size:11px;color:var(--muted);">Kosongkan = tampilkan semua soal yang dipilih</small>
                                </div>
                                <div style="display:flex;flex-direction:column;justify-content:flex-end;padding-bottom:4px;">
                                    <label class="sw" style="align-items:flex-start;gap:10px;">
                                        <input type="checkbox" id="f-acak-pg" style="margin-top:2px;">
                                        <span class="sw-track" style="margin-top:2px;flex-shrink:0;"></span>
                                        <div>
                                            <div class="sw-label">Acak urutan soal</div>
                                            <div class="sw-sub">Setiap mahasiswa dapat urutan soal PG berbeda</div>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <div style="background:var(--surface2);border:1px solid var(--border);border-radius:10px;padding:12px 14px;">
                                <label class="sw" style="align-items:flex-start;gap:10px;width:100%;">
                                    <input type="checkbox" id="f-acak-pilihan" style="margin-top:2px;">
                                    <span class="sw-track" style="margin-top:2px;flex-shrink:0;"></span>
                                    <div>
                                        <div class="sw-label">Acak urutan pilihan jawaban (A/B/C/D)</div>
                                        <div class="sw-sub">Urutan pilihan A/B/C/D akan diacak berbeda untuk setiap mahasiswa, sehingga jawaban benar tidak selalu di posisi yang sama</div>
                                    </div>
                                </label>
                            </div>

                        </div>
                    </div>
                </div>

            </form>
        </div>

        <div class="modal-foot">
            <button class="btn btn-ghost btn-sm" onclick="closeModal()" style="min-width:80px;">Batal</button>
            <button class="btn btn-primary btn-sm" id="btn-submit-ujian" onclick="submitUjian(event)" style="min-width:110px;">
                <i class="fas fa-save"></i> Simpan Ujian
            </button>
        </div>
    </div>
</div>

{{-- Toast --}}
<div id="toast" style="position:fixed;bottom:24px;right:24px;z-index:999;display:none;">
    <div id="toast-inner" style="padding:10px 18px;border-radius:12px;font-size:13.5px;font-weight:600;color:#fff;box-shadow:0 4px 16px rgba(0,0,0,.25);"></div>
</div>
@endsection

@push('scripts')
<script>
const SOAL_URL = "{{ route('instruktur.ujian.soal-by-kelas') }}";
const STORE_URL = "{{ route('instruktur.ujian.store') }}";
const CSRF     = "{{ csrf_token() }}";

let allEssaySoal = [];
let allPgSoal    = [];
let modalMode    = 'create'; // 'create' | 'edit'

/* ── Toast ───────────────────────────────────── */
function showToast(msg, ok = true) {
    const t = document.getElementById('toast');
    const i = document.getElementById('toast-inner');
    i.textContent = msg;
    i.style.background = ok ? '#10b981' : '#ef4444';
    t.style.display = 'block';
    clearTimeout(t._timer);
    t._timer = setTimeout(() => t.style.display = 'none', 3000);
}

/* ── Modal open/close ────────────────────────── */
function openCreate() {
    modalMode = 'create';
    document.getElementById('modal-title').textContent = 'Buat Ujian';
    document.getElementById('modal-head-sub').textContent = 'Isi detail ujian dan pilih soal dari bank soal';
    document.getElementById('modal-head-icon').innerHTML = '<i class="fas fa-plus"></i>';
    document.getElementById('ujian-id').value = '';
    document.getElementById('f-judul').value = '';
    setRteHtml('');
    document.getElementById('f-waktu-mulai').value = '';
    document.getElementById('f-waktu-selesai').value = '';
    document.getElementById('f-durasi').value = '';
    document.getElementById('f-status').value = 'draft';
    document.getElementById('f-ada-essay').checked = false;
    document.getElementById('f-ada-pg').checked = false;
    document.getElementById('f-jumlah-essay').value = '';
    document.getElementById('f-acak-essay').checked = false;
    document.getElementById('f-jumlah-pg').value = '';
    document.getElementById('f-acak-pg').checked = false;
    document.getElementById('f-acak-pilihan').checked = false;
    document.getElementById('panel-essay').style.display = 'none';
    document.getElementById('panel-pg').style.display = 'none';
    document.getElementById('modal-ujian').style.display = 'flex';
    loadSoal();
}

async function openEdit(id) {
    modalMode = 'edit';
    document.getElementById('modal-title').textContent = 'Edit Ujian';
    document.getElementById('modal-head-sub').textContent = 'Perbarui detail, soal, dan pengaturan ujian';
    document.getElementById('modal-head-icon').innerHTML = '<i class="fas fa-edit"></i>';
    document.getElementById('modal-ujian').style.display = 'flex';

    // Load soal pool first
    await loadSoal();

    try {
        const res = await fetch(`/instruktur/ujian/${id}`, {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }
        });
        const u = await res.json();

        document.getElementById('ujian-id').value = u.id;
        document.getElementById('f-judul').value = u.judul;
        setRteHtml(u.deskripsi ?? '');
        document.getElementById('f-waktu-mulai').value = u.waktu_mulai?.replace(' ', 'T').slice(0, 16) ?? '';
        document.getElementById('f-waktu-selesai').value = u.waktu_selesai?.replace(' ', 'T').slice(0, 16) ?? '';
        document.getElementById('f-durasi').value = u.durasi;
        document.getElementById('f-status').value = u.status;

        // Essay
        const selectedEssayIds = (u.soal_pool ?? []).filter(s => s.tipe === 'essay').map(s => s.id);
        document.getElementById('f-ada-essay').checked = !!u.ada_essay;
        document.getElementById('f-jumlah-essay').value = u.jumlah_soal_essay ?? '';
        document.getElementById('f-acak-essay').checked = !!u.acak_soal_essay;
        if (u.ada_essay) {
            document.getElementById('panel-essay').style.display = 'block';
            selectedEssayIds.forEach(sid => {
                const cb = document.querySelector(`#essay-soal-list input[data-id="${sid}"]`);
                if (cb) cb.checked = true;
            });
            updateEssayCount();
        }

        // PG
        const selectedPgIds = (u.soal_pool ?? []).filter(s => s.tipe === 'pilihan_ganda').map(s => s.id);
        document.getElementById('f-ada-pg').checked = !!u.ada_pg;
        document.getElementById('f-jumlah-pg').value = u.jumlah_soal_pg ?? '';
        document.getElementById('f-acak-pg').checked = !!u.acak_soal_pg;
        document.getElementById('f-acak-pilihan').checked = !!u.acak_pilihan_pg;
        if (u.ada_pg) {
            document.getElementById('panel-pg').style.display = 'block';
            selectedPgIds.forEach(sid => {
                const cb = document.querySelector(`#pg-soal-list input[data-id="${sid}"]`);
                if (cb) cb.checked = true;
            });
            updatePgCount();
        }
    } catch(e) {
        showToast('Gagal memuat data ujian.', false);
    }
}

function closeModal() {
    document.getElementById('modal-ujian').style.display = 'none';
}

/* ── Load soal dari bank (AJAX) ─────────────── */
async function loadSoal() {
    const kelasId = document.getElementById('ujian-kelas-id').value;
    if (!kelasId) return;

    const loading = '<div style="padding:24px;text-align:center;color:var(--muted);font-size:13px;"><i class="fas fa-spinner fa-spin"></i> Memuat soal…</div>';
    document.getElementById('essay-soal-list').innerHTML = loading;
    document.getElementById('pg-soal-list').innerHTML    = loading;
    document.getElementById('essay-toolbar-label').textContent = 'Memuat…';
    document.getElementById('pg-toolbar-label').textContent    = 'Memuat…';

    try {
        const res  = await fetch(`${SOAL_URL}?kelas_id=${kelasId}`, { headers: { 'Accept': 'application/json' } });
        const data = await res.json();

        allEssaySoal = data.filter(s => s.tipe === 'essay');
        allPgSoal    = data.filter(s => s.tipe === 'pilihan_ganda');

        renderSoalPicker('essay-soal-list', allEssaySoal, 'essay');
        renderSoalPicker('pg-soal-list', allPgSoal, 'pg');
        updateEssayCount();
        updatePgCount();
    } catch(e) {
        const err = '<div style="padding:14px;font-size:13px;color:#ef4444;text-align:center;"><i class="fas fa-exclamation-triangle"></i> Gagal memuat soal.</div>';
        document.getElementById('essay-soal-list').innerHTML = err;
        document.getElementById('pg-soal-list').innerHTML    = err;
    }
}

const diffColor = { mudah:'#10b981', sedang:'#f59e0b', sulit:'#ef4444' };
const diffBg    = { mudah:'rgba(16,185,129,.1)', sedang:'rgba(245,158,11,.1)', sulit:'rgba(239,68,68,.1)' };

function renderSoalPicker(listId, soalArr, prefix) {
    const el      = document.getElementById(listId);
    const toolbar = document.getElementById(`${prefix}-toolbar-label`);
    if (!soalArr.length) {
        el.innerHTML = `<div style="padding:24px;text-align:center;color:var(--muted);font-size:13px;">
            <i class="fas fa-inbox" style="font-size:24px;opacity:.3;display:block;margin-bottom:8px;"></i>
            Belum ada soal ${prefix === 'essay' ? 'essay' : 'pilihan ganda'} di bank soal.
        </div>`;
        toolbar.textContent = '0 soal tersedia';
        return;
    }
    toolbar.textContent = `${soalArr.length} soal tersedia`;
    const updateFn = prefix === 'essay' ? 'updateEssayCount()' : 'updatePgCount()';
    el.innerHTML = soalArr.map((s, i) => `
        <label class="soal-pick-item">
            <input type="checkbox" data-id="${s.id}" data-tipe="${s.tipe}" onchange="${updateFn}">
            <div class="soal-pick-num" style="background:${diffBg[s.tingkat_kesulitan]};color:${diffColor[s.tingkat_kesulitan]};">${i+1}</div>
            <div style="flex:1;min-width:0;">
                <div class="pick-pertanyaan">${escH(s.pertanyaan)}</div>
                <div class="pick-meta">
                    <span style="color:${diffColor[s.tingkat_kesulitan]};font-weight:600;">${ucFirst(s.tingkat_kesulitan)}</span>
                    &nbsp;·&nbsp; Bobot ${s.bobot}
                    ${s.tipe === 'pilihan_ganda' && s.pilihan ? `&nbsp;·&nbsp; ${s.pilihan.length} opsi` : ''}
                </div>
            </div>
        </label>
    `).join('');
}

function getCheckedIds(listId) {
    return Array.from(document.querySelectorAll(`#${listId} input[type=checkbox]:checked`)).map(cb => +cb.dataset.id);
}

function selectAll(listId, prefix) {
    document.querySelectorAll(`#${listId} input[type=checkbox]`).forEach(cb => cb.checked = true);
    prefix === 'essay' ? updateEssayCount() : updatePgCount();
}
function clearAll(listId, prefix) {
    document.querySelectorAll(`#${listId} input[type=checkbox]`).forEach(cb => cb.checked = false);
    prefix === 'essay' ? updateEssayCount() : updatePgCount();
}

function updateSelBar(prefix, checked, total) {
    const bar    = document.getElementById(`${prefix}-sel-bar`);
    const text   = document.getElementById(`${prefix}-sel-text`);
    const suffix = document.getElementById(`${prefix}-max-suffix`);
    if (checked === 0) {
        bar.className  = 'sel-bar no-sel';
        text.style.color = 'var(--muted)';
        text.innerHTML = 'Belum ada soal dipilih';
    } else {
        bar.className  = 'sel-bar has-sel';
        text.style.color = 'var(--ac)';
        text.innerHTML = `<i class="fas fa-check-circle" style="margin-right:5px;"></i><strong>${checked}</strong> soal dipilih`;
    }
    if (suffix) suffix.textContent = checked ? `/ ${checked}` : '/ —';
}

function updateEssayCount() {
    const checked = getCheckedIds('essay-soal-list').length;
    updateSelBar('essay', checked, allEssaySoal.length);
    const inp = document.getElementById('f-jumlah-essay');
    if (inp.value && +inp.value > checked) inp.value = checked || '';
}

function updatePgCount() {
    const checked = getCheckedIds('pg-soal-list').length;
    updateSelBar('pg', checked, allPgSoal.length);
    const inp = document.getElementById('f-jumlah-pg');
    if (inp.value && +inp.value > checked) inp.value = checked || '';
}

/* ── Toggle panels ───────────────────────────── */
function toggleEssayPanel() {
    const on = document.getElementById('f-ada-essay').checked;
    document.getElementById('panel-essay').style.display = on ? 'block' : 'none';
}
function togglePgPanel() {
    const on = document.getElementById('f-ada-pg').checked;
    document.getElementById('panel-pg').style.display = on ? 'block' : 'none';
}

/* ── Submit ──────────────────────────────────── */
async function submitUjian(e) {
    e.preventDefault();

    const adaEssay = document.getElementById('f-ada-essay').checked;
    const adaPg    = document.getElementById('f-ada-pg').checked;

    if (!adaEssay && !adaPg) {
        showToast('Pilih setidaknya satu jenis soal.', false);
        return;
    }

    const essayIds = adaEssay ? getCheckedIds('essay-soal-list') : [];
    const pgIds    = adaPg    ? getCheckedIds('pg-soal-list')    : [];

    if (adaEssay && essayIds.length === 0) {
        showToast('Pilih minimal 1 soal essay dari bank soal.', false);
        return;
    }
    if (adaPg && pgIds.length === 0) {
        showToast('Pilih minimal 1 soal pilihan ganda dari bank soal.', false);
        return;
    }

    const jumlahEssay = document.getElementById('f-jumlah-essay').value;
    const jumlahPg    = document.getElementById('f-jumlah-pg').value;

    if (adaEssay && jumlahEssay && +jumlahEssay > essayIds.length) {
        showToast(`Jumlah soal essay (${jumlahEssay}) melebihi soal yang dipilih (${essayIds.length}).`, false);
        return;
    }
    if (adaPg && jumlahPg && +jumlahPg > pgIds.length) {
        showToast(`Jumlah soal PG (${jumlahPg}) melebihi soal yang dipilih (${pgIds.length}).`, false);
        return;
    }

    const id = document.getElementById('ujian-id').value;
    const isEdit = !!id;

    const body = new FormData();
    body.append('_token', CSRF);
    if (isEdit) body.append('_method', 'PUT');
    body.append('kelas_id',          document.getElementById('ujian-kelas-id').value);
    body.append('judul',             document.getElementById('f-judul').value);
    body.append('deskripsi',         getRteHtml());
    body.append('waktu_mulai',       document.getElementById('f-waktu-mulai').value);
    body.append('waktu_selesai',     document.getElementById('f-waktu-selesai').value);
    body.append('durasi',            document.getElementById('f-durasi').value);
    body.append('status',            document.getElementById('f-status').value);
    body.append('ada_essay',         adaEssay ? '1' : '0');
    body.append('ada_pg',            adaPg    ? '1' : '0');
    if (jumlahEssay) body.append('jumlah_soal_essay', jumlahEssay);
    if (jumlahPg)    body.append('jumlah_soal_pg', jumlahPg);
    body.append('acak_soal_essay',  document.getElementById('f-acak-essay').checked   ? '1' : '0');
    body.append('acak_soal_pg',     document.getElementById('f-acak-pg').checked      ? '1' : '0');
    body.append('acak_pilihan_pg',  document.getElementById('f-acak-pilihan').checked  ? '1' : '0');
    essayIds.forEach(sid => body.append('soal_essay_ids[]', sid));
    pgIds.forEach(sid    => body.append('soal_pg_ids[]', sid));

    const url = isEdit ? `/instruktur/ujian/${id}` : '/instruktur/ujian';
    const btn = document.getElementById('btn-submit-ujian');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan…';

    try {
        const res = await fetch(url, { method: 'POST', body });
        const json = await res.json();
        if (!res.ok) throw new Error(json.message ?? 'Gagal menyimpan.');
        showToast(json.message);
        closeModal();
        setTimeout(() => location.reload(), 800);
    } catch(err) {
        showToast(err.message, false);
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-save"></i> Simpan';
    }
}

/* ── Delete ──────────────────────────────────── */
async function deleteUjian(id, judul) {
    if (!confirm(`Hapus ujian "${judul}"? Semua data sesi dan jawaban mahasiswa akan ikut terhapus.`)) return;
    try {
        const res = await fetch(`/instruktur/ujian/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
        });
        const json = await res.json();
        if (!res.ok) throw new Error(json.message ?? 'Gagal menghapus.');
        showToast(json.message);
        setTimeout(() => location.reload(), 800);
    } catch(err) {
        showToast(err.message, false);
    }
}

/* ── Rich text editor ─────────────────────────── */
function rteCmd(cmd) {
    document.getElementById('f-deskripsi-editor').focus();
    document.execCommand(cmd, false, null);
    updateRteToolbar();
}
function updateRteToolbar() {
    document.querySelectorAll('.rte-btn[title]').forEach(btn => {
        const title = btn.getAttribute('title').toLowerCase();
        const map = { bold:'bold', italic:'italic', underline:'underline' };
        if (map[title]) {
            btn.classList.toggle('active', document.queryCommandState(map[title]));
        }
    });
}
document.addEventListener('selectionchange', updateRteToolbar);

function getRteHtml() {
    return document.getElementById('f-deskripsi-editor').innerHTML.trim();
}
function setRteHtml(html) {
    document.getElementById('f-deskripsi-editor').innerHTML = html || '';
}

/* ── Helpers ─────────────────────────────────── */
function escH(s) {
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
function ucFirst(s) { return s ? s[0].toUpperCase() + s.slice(1) : ''; }

// Keyboard close
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });
</script>
@endpush
