@extends('layouts.instruktur')
@section('title', 'Kelola Materi — ' . $pokokBahasan->judul)
@section('page-title', 'Kelola Materi')

@push('styles')
<style>
/* ── Layout ── */
.pb-page { display:grid; grid-template-columns:1fr 380px; gap:20px; align-items:start; }
@media(max-width:900px){ .pb-page{grid-template-columns:1fr} }

/* ── Cards ── */
.page-card { background:var(--surface); border:1px solid var(--border); border-radius:18px; overflow:hidden; }
.card-head  { padding:14px 18px; border-bottom:1px solid var(--border); display:flex; align-items:center; justify-content:space-between; gap:10px; }
.card-body  { padding:16px 18px; }

/* ── Sortable materi list ── */
.materi-sort-item { display:flex; align-items:center; gap:10px; padding:10px 14px; border-radius:12px; border:1px solid var(--border); background:var(--surface2); margin-bottom:8px; cursor:default; transition:box-shadow .15s, border-color .15s; }
.materi-sort-item.sortable-ghost { opacity:.4; }
.materi-sort-item.sortable-chosen { box-shadow:0 8px 24px rgba(0,0,0,.25); border-color:var(--ac); }
.drag-handle { cursor:grab; color:var(--muted); font-size:12px; padding:2px 4px; }
.drag-handle:active { cursor:grabbing; }
.tipe-icon { width:30px; height:30px; border-radius:9px; display:grid; place-items:center; font-size:12px; flex-shrink:0; }
.tc-dokumen { background:rgba(59,130,246,.15);  color:#60a5fa; }
.tc-video   { background:rgba(244,63,94,.15);   color:#fb7185; }
.tc-link    { background:rgba(139,92,246,.15);  color:#a78bfa; }
.tc-teks    { background:rgba(245,158,11,.15);  color:#fbbf24; }

/* ── Toggle switch ── */
.toggle-switch { position:relative; display:inline-block; width:38px; height:22px; flex-shrink:0; }
.toggle-switch input { opacity:0; width:0; height:0; position:absolute; }
.toggle-track { position:absolute; inset:0; border-radius:11px; background:var(--border); transition:background .2s; cursor:pointer; }
.toggle-thumb { position:absolute; width:16px; height:16px; top:3px; left:3px; border-radius:50%; background:#fff; transition:transform .2s; pointer-events:none; }
.toggle-switch input:checked ~ .toggle-track { background:var(--ac); }
.toggle-switch input:checked ~ .toggle-track .toggle-thumb { transform:translateX(16px); }
.toggle-switch input:disabled ~ .toggle-track { opacity:.5; cursor:not-allowed; }

/* ── Tab buttons ── */
.tab-btn { flex:1; padding:8px 12px; border-radius:10px; font-size:12px; font-weight:600; border:none; cursor:pointer; transition:all .15s; }
.tab-btn.active { background:var(--ac); color:#fff; }
.tab-btn.inactive { background:var(--surface2); color:var(--muted); }
.tab-btn.inactive:hover { color:var(--text); }

/* ── Form fields ── */
.field-label { display:block; font-size:11.5px; font-weight:600; color:var(--muted); margin-bottom:5px; }
.field-input { width:100%; padding:8px 12px; border-radius:10px; border:1px solid var(--border); background:var(--surface2); color:var(--text); font-size:13px; outline:none; transition:border-color .15s; }
.field-input:focus { border-color:var(--ac); }
select.field-input { cursor:pointer; }

/* ── Multi-item rows ── */
.multi-item { display:flex; align-items:flex-start; gap:8px; padding:10px 12px; border-radius:12px; border:1px solid var(--border); background:var(--surface2); margin-bottom:8px; }
.multi-item-body { flex:1; min-width:0; display:flex; flex-direction:column; gap:6px; }

/* ── Drop zone ── */
.drop-zone { position:relative; border:2px dashed var(--border); border-radius:12px; transition:border-color .2s, background .2s; cursor:pointer; overflow:hidden; }
.drop-zone.drag-over { border-color:var(--ac); background:var(--ac-lt2); }

/* ── Btn ── */
.btn-primary { padding:9px 20px; border-radius:11px; font-size:13px; font-weight:600; color:#fff; background:var(--ac); border:none; cursor:pointer; transition:opacity .15s; }
.btn-primary:hover { opacity:.85; }
.btn-primary:disabled { opacity:.5; cursor:not-allowed; }
.btn-ghost { padding:9px 14px; border-radius:11px; font-size:13px; font-weight:600; background:var(--surface2); color:var(--muted); border:none; cursor:pointer; transition:opacity .15s; }
.btn-ghost:hover { opacity:.75; }
.btn-add-row { display:flex; align-items:center; gap:6px; width:100%; padding:8px 12px; border-radius:10px; border:1px dashed var(--border); font-size:12px; font-weight:600; color:var(--muted); background:transparent; cursor:pointer; transition:all .15s; }
.btn-add-row:hover { border-color:var(--ac); color:var(--ac); }

/* ── Save order bar ── */
#save-order-bar { display:none; position:fixed; bottom:24px; left:50%; transform:translateX(-50%); background:var(--surface); border:1px solid var(--border); border-radius:14px; padding:10px 16px; box-shadow:0 8px 32px rgba(0,0,0,.35); z-index:100; align-items:center; gap:12px; }
#save-order-bar.visible { display:flex; }

/* ── Modal ── */
.modal-header { display:flex; align-items:center; justify-content:space-between; padding:16px 20px 12px; border-bottom:1px solid var(--border); }
.modal-title  { font-family:'Clash Display',sans-serif; font-weight:700; font-size:16px; color:var(--text); }
.modal-close  { width:28px; height:28px; border-radius:8px; background:var(--surface2); color:var(--muted); border:none; cursor:pointer; display:grid; place-items:center; }
.modal-body   { padding:16px 20px; }
.modal-footer { display:flex; justify-content:flex-end; gap:8px; padding:12px 20px 16px; border-top:1px solid var(--border); }

/* ── FAB Speed Dial ── */
.fab-backdrop { position:fixed; inset:0; z-index:498; background:rgba(0,0,0,.25); opacity:0; pointer-events:none; transition:opacity .2s; }
.fab-backdrop.show { opacity:1; pointer-events:all; }
.fab-root { position:fixed; right:24px; bottom:32px; z-index:499; display:flex; flex-direction:column; align-items:flex-end; gap:12px; }
.fab-items { display:flex; flex-direction:column; align-items:flex-end; gap:10px; }
.fab-item { display:flex; align-items:center; gap:10px; opacity:0; transform:translateY(14px) scale(.8); pointer-events:none; transition:opacity .18s var(--d,0s), transform .18s var(--d,0s); }
.fab-root.open .fab-item { opacity:1; transform:none; pointer-events:all; }
.fab-lbl { font-size:12px; font-weight:600; color:var(--text); background:var(--surface2); border:1px solid var(--border); padding:5px 12px; border-radius:20px; box-shadow:0 2px 10px rgba(0,0,0,.2); white-space:nowrap; user-select:none; }
.fab-sub { width:44px; height:44px; border-radius:50%; display:grid; place-items:center; border:none; cursor:pointer; font-size:16px; color:#fff; box-shadow:0 3px 10px rgba(0,0,0,.3); position:relative; flex-shrink:0; transition:transform .15s; }
.fab-sub:hover { transform:scale(1.1); }
.fab-sub-dsk { background:var(--ac); }
.fab-sub-ai  { background:#4f46e5; }
.fab-main { width:52px; height:52px; border-radius:50%; display:grid; place-items:center; border:none; cursor:pointer; font-size:20px; color:#fff; background:var(--ac); box-shadow:0 4px 16px rgba(0,0,0,.35); flex-shrink:0; transition:box-shadow .2s; }
.fab-main:hover { box-shadow:0 6px 22px rgba(0,0,0,.45); }
.fab-main-icon { display:block; transition:transform .3s cubic-bezier(.34,1.56,.64,1); }
.fab-root.open .fab-main-icon { transform:rotate(45deg); }
.fab-badge { position:absolute; top:-5px; right:-5px; min-width:18px; height:18px; border-radius:9px; padding:0 4px; background:#ef4444; color:#fff; font-size:10px; font-weight:700; display:flex; align-items:center; justify-content:center; border:2px solid var(--surface); }

/* ── Drawers ── */
.pb-overlay { position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:900; opacity:0; pointer-events:none; transition:opacity .2s; }
.pb-overlay.show { opacity:1; pointer-events:all; }
.pb-drawer { position:fixed; right:0; top:0; bottom:0; width:min(400px,100vw); background:var(--surface); border-left:1px solid var(--border); z-index:901; display:flex; flex-direction:column; transform:translateX(100%); transition:transform .28s cubic-bezier(.4,0,.2,1); }
.pb-drawer.show { transform:translateX(0); }
.pb-dh { display:flex; align-items:center; gap:10px; padding:13px 16px; border-bottom:1px solid var(--border); flex-shrink:0; flex-wrap:wrap; }
.pb-close { width:28px; height:28px; border-radius:8px; border:1px solid var(--border); background:transparent; color:var(--muted); cursor:pointer; display:grid; place-items:center; flex-shrink:0; }
.pb-close:hover { background:var(--surface2); }
.pb-sel { font-size:11px; padding:4px 8px; border-radius:8px; border:1px solid var(--border); background:var(--surface2); color:var(--text); cursor:pointer; }
.pb-msgs { flex:1; overflow-y:auto; padding:12px 14px; display:flex; flex-direction:column; gap:10px; }
.pb-foot { padding:10px 14px; border-top:1px solid var(--border); display:flex; gap:8px; flex-shrink:0; }
.pb-inp { flex:1; resize:none; min-height:40px; max-height:100px; padding:8px 11px; border-radius:10px; border:1px solid var(--border); background:var(--surface2); color:var(--text); font-size:12px; line-height:1.45; font-family:inherit; outline:none; transition:border-color .2s; }
.pb-inp:focus { border-color:var(--ac); }
.pb-send { width:36px; height:36px; border-radius:10px; border:none; color:#fff; cursor:pointer; display:grid; place-items:center; font-size:13px; align-self:flex-end; transition:opacity .15s; }
.pb-send:disabled { opacity:.4; cursor:default; }
.pb-chat-row { display:flex; gap:8px; align-items:flex-start; }
.pb-chat-row.own { flex-direction:row-reverse; }
.pb-avatar { width:26px; height:26px; border-radius:50%; display:grid; place-items:center; font-size:9px; font-weight:700; color:#fff; flex-shrink:0; margin-top:2px; }
.pb-body { min-width:0; flex:1; }
.pb-chat-row.own .pb-body { text-align:right; }
.pb-bubble { display:inline-block; max-width:100%; padding:8px 12px; border-radius:12px; font-size:12px; line-height:1.55; white-space:pre-wrap; word-break:break-word; overflow-wrap:anywhere; }
.pb-chat-row:not(.own) .pb-bubble { background:var(--surface2); color:var(--text); border-bottom-left-radius:3px; }
.pb-chat-row.own .pb-bubble { background:var(--ac); color:#fff; border-bottom-right-radius:3px; }
.pb-chat-row.bot .pb-bubble { background:rgba(99,102,241,.1); border:1px solid rgba(99,102,241,.2); color:var(--text); border-bottom-left-radius:3px; }
.pb-meta { font-size:10px; color:var(--muted); margin-top:3px; }
.pb-itag { font-size:9px; font-weight:700; padding:1px 5px; border-radius:4px; background:rgba(245,158,11,.15); color:#f59e0b; margin-left:3px; }
.pb-del { opacity:0; background:none; border:none; cursor:pointer; color:var(--muted); font-size:10px; padding:2px 4px; border-radius:4px; transition:opacity .15s; }
.pb-chat-row:hover .pb-del { opacity:1; }
.pb-del:hover { color:#ef4444; }
.pb-empty { flex:1; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:8px; color:var(--muted); font-size:12px; text-align:center; padding:24px; }
.tdots { display:inline-flex; gap:3px; align-items:center; }
.tdots span { width:5px; height:5px; border-radius:50%; background:var(--muted); animation:tdot .9s infinite; }
.tdots span:nth-child(2){animation-delay:.2s} .tdots span:nth-child(3){animation-delay:.4s}
@keyframes tdot{0%,80%,100%{transform:scale(.7);opacity:.5}40%{transform:scale(1);opacity:1}}
</style>
@endpush

@section('content')
<div class="space-y-4 animate-fadeUp">

  {{-- Breadcrumb --}}
  <div class="flex items-center gap-2 text-[12px]" style="color:var(--muted)">
    <a href="{{ route('instruktur.materi.index', ['mk_id' => $mataKuliah->id]) }}" class="a-text hover:underline">Materi Ajar</a>
    <i class="fa-solid fa-chevron-right text-[10px]"></i>
    <span style="color:var(--text)">{{ $mataKuliah->kode }}</span>
    <i class="fa-solid fa-chevron-right text-[10px]"></i>
    <span style="color:var(--text)">Pertemuan {{ $pokokBahasan->pertemuan }}</span>
  </div>

  {{-- PB Info --}}
  <div class="flex items-start gap-4 flex-wrap">
    <div class="w-10 h-10 rounded-xl grid place-items-center font-display font-bold text-[16px] a-bg-lt a-text flex-shrink-0">
      {{ $pokokBahasan->pertemuan }}
    </div>
    <div class="flex-1 min-w-0">
      <h2 class="font-display font-bold text-[19px]" style="color:var(--text)">{{ $pokokBahasan->judul }}</h2>
      @if($pokokBahasan->deskripsi)
        <p class="text-[12px] mt-0.5" style="color:var(--muted)">{{ $pokokBahasan->deskripsi }}</p>
      @endif
      <div class="flex items-center gap-3 mt-1 text-[11px]" style="color:var(--muted)">
        <span><i class="fa-solid fa-book-open mr-1"></i>{{ $mataKuliah->kode }} — {{ $mataKuliah->nama }}</span>
        <span><i class="fa-solid fa-layer-group mr-1"></i>{{ $materi->count() }} materi</span>
      </div>
    </div>
    {{-- Rangkuman toggle --}}
    <div class="flex items-center gap-3 flex-shrink-0 px-4 py-2.5 rounded-xl"
         style="background:var(--surface2);border:1px solid var(--border)">
      <div class="text-right">
        <div class="text-[12px] font-semibold" style="color:var(--text)">Rangkuman Mahasiswa</div>
        <div class="text-[11px]" style="color:var(--muted)" id="rangkuman-status-txt">
          {{ $pokokBahasan->rangkuman_aktif ? 'Aktif — mahasiswa wajib merangkum' : 'Nonaktif' }}
        </div>
      </div>
      <label class="toggle-switch" title="Aktifkan/nonaktifkan rangkuman mahasiswa">
        <input type="checkbox" id="toggle-rangkuman"
               {{ $pokokBahasan->rangkuman_aktif ? 'checked' : '' }}
               onchange="togglePbRangkuman(this)">
        <div class="toggle-track"><div class="toggle-thumb"></div></div>
      </label>
    </div>
  </div>

  <div class="pb-page">

    {{-- LEFT: urutan materi --}}
    <div class="space-y-4">

      {{-- Sortable list --}}
      <div class="page-card">
        <div class="card-head">
          <div>
            <div class="text-[13px] font-semibold" style="color:var(--text)">Urutan Tampil ke Mahasiswa</div>
            <div class="text-[11px] mt-0.5" style="color:var(--muted)">Drag untuk mengubah urutan</div>
          </div>
          <div class="flex items-center gap-2">
            <a href="{{ route('instruktur.pokok-bahasan.rekap', $pokokBahasan->id) }}"
               class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[11px] font-semibold transition-opacity hover:opacity-80"
               style="background:rgba(139,92,246,.15);color:#a78bfa">
              <i class="fa-solid fa-chart-bar"></i>
              <span class="hidden sm:inline">Rekap Aktivitas</span>
            </a>
            <a href="{{ route('instruktur.pokok-bahasan.preview', $pokokBahasan->id) }}"
               class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[11px] font-semibold transition-opacity hover:opacity-80"
               style="background:rgba(16,185,129,.15);color:#34d399">
              <i class="fa-solid fa-eye"></i>
              <span class="hidden sm:inline">Preview Siswa</span>
            </a>
            <button id="btn-save-order" onclick="saveOrder()" class="btn-primary text-[12px] py-1.5 px-3 hidden">
              <i class="fa-solid fa-floppy-disk mr-1.5"></i>Simpan Urutan
            </button>
          </div>
        </div>
        <div class="card-body">
          @if($materi->isEmpty())
            <div class="text-center py-8" style="color:var(--muted)">
              <i class="fa-solid fa-inbox text-[22px] mb-2 block opacity-50"></i>
              <div class="text-[12px]">Belum ada materi. Tambahkan dari panel kanan.</div>
            </div>
          @else
            <div id="sortable-list">
              @foreach($materi as $m)
              <div class="materi-sort-item" data-id="{{ $m->id }}">
                <span class="drag-handle"><i class="fa-solid fa-grip-vertical"></i></span>
                <div class="tipe-icon {{ match($m->tipe) { 'dokumen'=>'tc-dokumen','video'=>'tc-video','link'=>'tc-link','teks'=>'tc-teks',default=>'a-bg-lt a-text' } }}">
                  <i class="fa-solid {{ $m->tipeIcon() }}"></i>
                </div>
                <div class="flex-1 min-w-0">
                  <div class="text-[13px] font-semibold truncate" style="color:var(--text)">{{ $m->judul }}</div>
                  <div class="text-[10px] mt-0.5" style="color:var(--muted)">{{ $m->tipeLabel() }}
                    @if($m->nama_file) &bull; {{ $m->ukuranHuman() }}@endif
                  </div>
                </div>
                <label class="toggle-switch" title="{{ $m->status === 'Aktif' ? 'Aktif' : 'Draft' }}">
                  <input type="checkbox" {{ $m->status === 'Aktif' ? 'checked' : '' }}
                         onchange="toggleMateri({{ $m->id }}, this)">
                  <div class="toggle-track"><div class="toggle-thumb"></div></div>
                </label>
                <div class="flex items-center gap-1 flex-shrink-0">
                  <button onclick="openEditInPanel({{ $m->id }})"
                          class="w-7 h-7 rounded-lg grid place-items-center text-[11px] hover:opacity-75"
                          style="background:var(--surface);color:var(--muted);border:1px solid var(--border)" title="Edit">
                    <i class="fa-solid fa-pen"></i>
                  </button>
                  <button onclick="deleteMateri({{ $m->id }}, '{{ addslashes($m->judul) }}')"
                          class="w-7 h-7 rounded-lg grid place-items-center text-[11px] bg-red-500/10 text-red-400 hover:opacity-75"
                          title="Hapus">
                    <i class="fa-solid fa-trash"></i>
                  </button>
                </div>
              </div>
              @endforeach
            </div>
          @endif
        </div>
      </div>

    </div>

    {{-- RIGHT: tambah materi --}}
    <div class="space-y-4">

      {{-- Tipe tabs --}}
      <div class="page-card" id="right-panel-card">
        <div class="card-head" style="flex-direction:column;align-items:stretch;gap:8px">
          <div class="flex items-center justify-between">
            <span id="panel-title" class="text-[13px] font-semibold" style="color:var(--text)">Tambah Materi</span>
          </div>
          {{-- Edit mode banner (hidden by default) --}}
          <div id="edit-mode-banner" class="hidden items-center gap-2 px-3 py-2 rounded-lg"
               style="background:rgba(var(--ac-rgb),.1);border:1px solid rgba(var(--ac-rgb),.3)">
            <i class="fa-solid fa-pen a-text text-[11px] flex-shrink-0"></i>
            <span id="edit-mode-label" class="text-[12px] font-semibold a-text flex-1 min-w-0 truncate"></span>
            <button onclick="cancelEdit()" class="text-[11px] font-semibold flex-shrink-0 hover:opacity-75"
                    style="color:var(--muted)">
              <i class="fa-solid fa-xmark mr-1"></i>Batal
            </button>
          </div>
        </div>
        <div class="card-body space-y-4">

          {{-- Tab buttons --}}
          <div class="flex gap-2">
            <button onclick="switchTab('teks')"   id="tab-teks"   class="tab-btn inactive"><i class="fa-solid fa-align-left mr-1.5"></i>Teks</button>
            <button onclick="switchTab('dokumen')" id="tab-dokumen" class="tab-btn active"><i class="fa-solid fa-file-lines mr-1.5"></i>Dokumen</button>
            <button onclick="switchTab('link')"   id="tab-link"   class="tab-btn inactive"><i class="fa-solid fa-link mr-1.5"></i>Link</button>
          </div>

          {{-- ── TAB: TEKS ── --}}
          <div id="panel-teks" class="hidden space-y-3">
            <div>
              <label class="field-label">Judul <span class="text-red-400">*</span></label>
              <input type="text" id="teks-judul" maxlength="200" placeholder="Judul materi teks..." class="field-input">
            </div>
            <div>
              <label class="field-label">Konten <span class="text-red-400">*</span></label>
              <textarea id="teks-konten" rows="8" maxlength="20000" placeholder="Tulis isi materi di sini..."
                        class="field-input resize-y" style="font-family:inherit"></textarea>
            </div>
            <div>
              <label class="field-label">Deskripsi singkat <span style="color:var(--muted)" class="text-[11px]">opsional</span></label>
              <input type="text" id="teks-deskripsi" maxlength="500" placeholder="Deskripsi..." class="field-input">
            </div>
            <div class="flex items-center justify-between">
              <label class="field-label mb-0">Status</label>
              <select id="teks-status" class="field-input" style="width:auto">
                <option value="Draft">Draft (tersembunyi)</option>
                <option value="Aktif">Aktif (terlihat)</option>
              </select>
            </div>
            <button onclick="submitTeks()" class="btn-primary w-full" id="btn-teks">
              <span id="btn-teks-text"><i class="fa-solid fa-plus mr-1.5"></i>Tambah Materi Teks</span>
              <span id="btn-teks-spin" class="hidden"><i class="fa-solid fa-spinner fa-spin mr-1.5"></i>Menyimpan...</span>
            </button>
          </div>

          {{-- ── TAB: DOKUMEN ── --}}
          <div id="panel-dokumen" class="space-y-3">
            <div>
              <label class="field-label">Status untuk semua dokumen</label>
              <select id="dok-status" class="field-input">
                <option value="Draft">Draft (tersembunyi)</option>
                <option value="Aktif">Aktif (terlihat)</option>
              </select>
            </div>
            <div>
              <label class="field-label">Dokumen <span class="text-red-400">*</span></label>
              <div id="dok-list" class="space-y-2">
                {{-- injected by JS --}}
              </div>
              <button type="button" id="btn-add-dok" onclick="addDokRow()" class="btn-add-row mt-2">
                <i class="fa-solid fa-plus text-[10px]"></i>Tambah Dokumen Lagi
              </button>
            </div>
            <button onclick="submitDokumen()" class="btn-primary w-full" id="btn-dok">
              <span id="btn-dok-text"><i class="fa-solid fa-upload mr-1.5"></i>Upload Dokumen</span>
              <span id="btn-dok-spin" class="hidden"><i class="fa-solid fa-spinner fa-spin mr-1.5"></i>Mengupload...</span>
            </button>
          </div>

          {{-- ── TAB: LINK ── --}}
          <div id="panel-link" class="hidden space-y-3">
            <div>
              <label class="field-label">Status untuk semua link</label>
              <select id="lnk-status" class="field-input">
                <option value="Draft">Draft (tersembunyi)</option>
                <option value="Aktif">Aktif (terlihat)</option>
              </select>
            </div>
            <div>
              <label class="field-label">Link <span class="text-red-400">*</span></label>
              <div id="lnk-list" class="space-y-2">
                {{-- injected by JS --}}
              </div>
              <button type="button" id="btn-add-lnk" onclick="addLinkRow()" class="btn-add-row mt-2">
                <i class="fa-solid fa-plus text-[10px]"></i>Tambah Link Lagi
              </button>
            </div>
            <button onclick="submitLink()" class="btn-primary w-full" id="btn-lnk">
              <span id="btn-lnk-text"><i class="fa-solid fa-plus mr-1.5"></i>Simpan Link</span>
              <span id="btn-lnk-spin" class="hidden"><i class="fa-solid fa-spinner fa-spin mr-1.5"></i>Menyimpan...</span>
            </button>
          </div>

        </div>
      </div>

    </div>

  </div>

</div>



{{-- FAB Speed Dial --}}
<div class="fab-backdrop" id="fab-backdrop" onclick="closeFab()"></div>
<div class="fab-root" id="fab-root">
  <div class="fab-items">
    <div class="fab-item" style="--d:.08s">
      <span class="fab-lbl">Tanya AI</span>
      <button class="fab-sub fab-sub-ai" onclick="fabOpenAi()" title="Tanya AI">
        <i class="fa-solid fa-robot"></i>
      </button>
    </div>
    <div class="fab-item" style="--d:.04s">
      <span class="fab-lbl">Diskusi</span>
      <button class="fab-sub fab-sub-dsk" onclick="fabOpenDiskusi()" title="Diskusi">
        <i class="fa-solid fa-comments"></i>
        <span class="fab-badge hidden" id="fab-badge">0</span>
      </button>
    </div>
  </div>
  <button class="fab-main" onclick="toggleFab()" title="Diskusi & AI">
    <i class="fa-solid fa-plus fab-main-icon"></i>
  </button>
</div>

{{-- Shared overlay --}}
<div class="pb-overlay" id="pb-overlay" onclick="pbCloseAll()"></div>

{{-- Diskusi Drawer --}}
<div class="pb-drawer" id="pb-dsk-drawer">
  <div class="pb-dh">
    <button class="pb-close" onclick="pbCloseAll()"><i class="fa-solid fa-xmark text-[11px]"></i></button>
    <i class="fa-solid fa-comments flex-shrink-0" style="color:var(--ac)"></i>
    <span class="text-[13px] font-semibold flex-shrink-0">Diskusi — {{ $pokokBahasan->judul }}</span>
    <select class="pb-sel flex-shrink-0" id="pb-kelas-sel" onchange="pbLoadDiskusi()"></select>
  </div>
  <div class="pb-msgs" id="pb-dsk-msgs">
    <div class="pb-empty"><i class="fa-solid fa-circle-notch fa-spin"></i></div>
  </div>
  <div class="pb-foot">
    <textarea class="pb-inp" id="pb-dsk-inp" placeholder="Tulis pesan…" rows="1"
              onkeydown="if(event.key==='Enter'&&!event.shiftKey){event.preventDefault();pbSendDiskusi();}"></textarea>
    <button class="pb-send" id="pb-dsk-send" onclick="pbSendDiskusi()" style="background:var(--ac)">
      <i class="fa-solid fa-paper-plane"></i>
    </button>
  </div>
</div>

{{-- AI Chat Drawer --}}
<div class="pb-drawer" id="pb-ai-drawer">
  <div class="pb-dh">
    <button class="pb-close" onclick="pbCloseAll()"><i class="fa-solid fa-xmark text-[11px]"></i></button>
    <div class="w-7 h-7 rounded-lg grid place-items-center flex-shrink-0" style="background:rgba(99,102,241,.12)">
      <i class="fa-solid fa-robot text-[12px]" style="color:#818cf8"></i>
    </div>
    <span class="text-[13px] font-semibold flex-shrink-0">Tanya AI</span>
    <select class="pb-sel flex-1" id="pb-ai-materi-sel" onchange="pbOnAiMateriChange()" style="min-width:0"></select>
  </div>
  <div class="pb-msgs" id="pb-ai-msgs">
    <div class="pb-empty" id="pb-ai-welcome">
      <div class="w-12 h-12 rounded-2xl grid place-items-center" style="background:rgba(99,102,241,.12)">
        <i class="fa-solid fa-robot text-[22px]" style="color:#818cf8"></i>
      </div>
      <p>Pilih materi lalu tanyakan apa saja.<br>AI siap membantu Anda.</p>
    </div>
  </div>
  <div class="pb-foot">
    <textarea class="pb-inp" id="pb-ai-inp" placeholder="Tanya tentang materi…" rows="1"
              onkeydown="if(event.key==='Enter'&&!event.shiftKey){event.preventDefault();pbSendAi();}"></textarea>
    <button class="pb-send" id="pb-ai-send" onclick="pbSendAi()" style="background:#4f46e5">
      <i class="fa-solid fa-paper-plane"></i>
    </button>
  </div>
</div>

@endsection

@push('scripts')
{{-- SortableJS CDN --}}
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
<script>
const CSRF  = document.querySelector('meta[name="csrf-token"]').content;
const PB_ID = {{ $pokokBahasan->id }};
const MK_ID = {{ $mataKuliah->id }};
const ROUTES = {
  store:   '{{ route("instruktur.materi.store") }}',
  update:  id => `{{ url("instruktur/materi") }}/${id}`,
  destroy: id => `{{ url("instruktur/materi") }}/${id}`,
  toggle:  id => `{{ url("instruktur/materi") }}/${id}/toggle`,
  reorder: '{{ route("instruktur.materi.reorder") }}',
};

/* ══ SORTABLE ══════════════════════════════════════════════════ */
let sortEl = document.getElementById('sortable-list');
function initSortable(el) {
  Sortable.create(el, {
    handle: '.drag-handle', animation: 150,
    ghostClass: 'sortable-ghost', chosenClass: 'sortable-chosen',
    onEnd() { document.getElementById('btn-save-order')?.classList.remove('hidden'); },
  });
}
if (sortEl) initSortable(sortEl);

async function saveOrder() {
  const items = [...sortEl.querySelectorAll('.materi-sort-item')].map(el => +el.dataset.id);
  const btn = document.getElementById('btn-save-order');
  btn.disabled = true;
  btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1.5"></i>Menyimpan...';
  try {
    const r = await fetch(ROUTES.reorder, {
      method:'POST',
      headers:{'X-CSRF-TOKEN':CSRF,'Accept':'application/json','Content-Type':'application/json'},
      body: JSON.stringify({items}),
    });
    const j = await r.json();
    showToast(r.ok ? 'success' : 'error', j.message || 'Gagal menyimpan urutan.');
    if (r.ok) { btn.classList.add('hidden'); }
  } catch { showToast('error','Terjadi kesalahan.'); }
  finally { btn.disabled=false; btn.innerHTML='<i class="fa-solid fa-floppy-disk mr-1.5"></i>Simpan Urutan'; }
}

/* ══ TABS ══════════════════════════════════════════════════════ */
function switchTab(tab) {
  ['teks','dokumen','link'].forEach(t => {
    document.getElementById('panel-'+t).classList.toggle('hidden', t !== tab);
    const btn = document.getElementById('tab-'+t);
    btn.classList.toggle('active',   t === tab);
    btn.classList.toggle('inactive', t !== tab);
  });
}

/* ══ DOKUMEN ROWS ══════════════════════════════════════════════ */
let dokIdx = 0;
function addDokRow(prefill) {
  const id = ++dokIdx;
  const isEdit  = !!prefill?.editMode;
  const curFile = prefill?.currentFile || '';
  const allowDl = prefill?.allow_download !== false; // default true
  const div = document.createElement('div');
  div.className = 'multi-item';
  div.id = 'dok-row-'+id;
  div.innerHTML = `
    <div class="multi-item-body">
      <input type="text" placeholder="Judul dokumen..." maxlength="200"
             class="field-input" id="dok-judul-${id}" value="${escHtml(prefill?.judul||'')}">
      <label class="drop-zone p-3 text-center"
             ondragover="event.preventDefault();this.classList.add('drag-over')"
             ondragleave="this.classList.remove('drag-over')"
             ondrop="handleDrop(event,'dok-file-${id}','dok-fname-${id}')">
        <i class="fa-solid fa-cloud-arrow-up a-text text-[16px] mb-1 block pointer-events-none"></i>
        ${isEdit && curFile
          ? `<div class="text-[11px] pointer-events-none" style="color:var(--muted)">File saat ini: <strong>${escHtml(curFile)}</strong></div>
             <div class="text-[10px] pointer-events-none mt-0.5" style="color:var(--muted)">Pilih file baru untuk mengganti (opsional)</div>`
          : `<div class="text-[11px] pointer-events-none" style="color:var(--muted)">Klik atau drag file</div>`}
        <div id="dok-fname-${id}" class="text-[11px] font-semibold a-text mt-1 hidden pointer-events-none"></div>
        <input type="file" id="dok-file-${id}"
               accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.zip,.png,.jpg,.jpeg"
               style="position:absolute;opacity:0;inset:0;width:100%;height:100%;cursor:pointer"
               onchange="showFname(this,'dok-fname-${id}')">
      </label>
      <label class="flex items-center gap-2 text-[11px] cursor-pointer select-none mt-1" style="color:var(--muted)">
        <input type="checkbox" id="dok-allow-dl-${id}" ${allowDl ? 'checked' : ''}
               class="w-3.5 h-3.5 rounded accent-indigo-500">
        <span>Izinkan mahasiswa mengunduh file ini</span>
      </label>
    </div>
    ${(!isEdit && id > 1) ? `<button type="button" onclick="document.getElementById('dok-row-${id}').remove()"
      class="w-7 h-7 rounded-lg grid place-items-center text-[11px] bg-red-500/10 text-red-400 hover:opacity-75 flex-shrink-0 mt-1">
      <i class="fa-solid fa-xmark"></i></button>` : ''}
  `;
  document.getElementById('dok-list').appendChild(div);
}

/* ══ LINK ROWS ═════════════════════════════════════════════════ */
let lnkIdx = 0;
function addLinkRow(prefill) {
  const id = ++lnkIdx;
  const isEdit = !!prefill?.editMode;
  const initType = prefill?.type || 'link';
  const div = document.createElement('div');
  div.className = 'multi-item';
  div.id = 'lnk-row-'+id;
  div.innerHTML = `
    <div class="multi-item-body">
      <input type="text" placeholder="Judul link..." maxlength="200"
             class="field-input" id="lnk-judul-${id}" value="${escHtml(prefill?.judul||'')}">
      <input type="url" placeholder="https://..." maxlength="1000"
             class="field-input" id="lnk-url-${id}" value="${escHtml(prefill?.url||'')}">
      <div class="flex gap-2 mt-0.5">
        <button type="button" onclick="setLinkType('${id}','link')" id="lnk-type-link-${id}"
                class="lnk-type flex-1 py-1 rounded-lg text-[11px] font-semibold border transition-all"
                style="border-color:var(--border);color:var(--muted)" data-type="link">
          <i class="fa-solid fa-link mr-1"></i>Tautan Biasa
        </button>
        <button type="button" onclick="setLinkType('${id}','video')" id="lnk-type-video-${id}"
                class="lnk-type flex-1 py-1 rounded-lg text-[11px] font-semibold border transition-all"
                style="border-color:var(--border);color:var(--muted)" data-type="video">
          <i class="fa-solid fa-circle-play mr-1"></i>Video
        </button>
      </div>
      <input type="hidden" id="lnk-type-${id}" value="${initType}">
    </div>
    ${(!isEdit && id > 1) ? `<button type="button" onclick="document.getElementById('lnk-row-${id}').remove()"
      class="w-7 h-7 rounded-lg grid place-items-center text-[11px] bg-red-500/10 text-red-400 hover:opacity-75 flex-shrink-0 mt-1">
      <i class="fa-solid fa-xmark"></i></button>` : ''}
  `;
  document.getElementById('lnk-list').appendChild(div);
  setLinkType(id, initType);
}

function setLinkType(id, type) {
  document.getElementById('lnk-type-'+id).value = type;
  ['link','video'].forEach(t => {
    const btn = document.getElementById('lnk-type-'+t+'-'+id);
    if (!btn) return;
    if (t === type) {
      btn.className = btn.className.replace(/tc-\w+/g,'') + ' tc-' + (t==='video'?'video':'link');
      btn.style.borderColor = ''; btn.style.color = '';
    } else {
      btn.className = btn.className.replace(/tc-\w+/g,'');
      btn.style.borderColor = 'var(--border)'; btn.style.color = 'var(--muted)';
    }
  });
}

/* ══ FILE HELPERS ══════════════════════════════════════════════ */
function showFname(input, infoId) {
  const el = document.getElementById(infoId);
  if (input.files[0]) { el.textContent = input.files[0].name; el.classList.remove('hidden'); }
}
function handleDrop(e, inputId, infoId) {
  e.preventDefault(); e.currentTarget.classList.remove('drag-over');
  const file = e.dataTransfer.files[0]; if (!file) return;
  const dt = new DataTransfer(); dt.items.add(file);
  const inp = document.getElementById(inputId); inp.files = dt.files;
  showFname(inp, infoId);
}

/* ══ allMateri — local cache ═══════════════════════════════════ */
@php
$allMateriData = $materi->map(fn($m) => [
    'id'        => $m->id,
    'judul'     => $m->judul,
    'deskripsi' => $m->deskripsi,
    'tipe'      => $m->tipe,
    'tipe_label'=> $m->tipeLabel(),
    'url'       => $m->url,
    'konten'    => $m->konten,
    'nama_file' => $m->nama_file,
    'ukuran'    => $m->ukuranHuman(),
    'urutan'         => $m->urutan,
    'status'         => $m->status,
    'allow_download' => (bool) $m->allow_download,
])->values();
@endphp
const allMateri = @json($allMateriData);

/* ══ EDIT MODE ════════════════════════════════════════════════ */
let editId = null;

function openEditInPanel(id) {
  const m = allMateri.find(x => x.id === id);
  if (!m) { showToast('error','Data tidak ditemukan. Coba refresh halaman.'); return; }
  editId = id;

  const tabName = (m.tipe === 'video') ? 'link' : m.tipe;
  switchTab(tabName);

  if (m.tipe === 'teks') {
    document.getElementById('teks-judul').value    = m.judul    || '';
    document.getElementById('teks-konten').value   = m.konten   || '';
    document.getElementById('teks-deskripsi').value= m.deskripsi|| '';
    document.getElementById('teks-status').value   = m.status   || 'Draft';
  } else if (m.tipe === 'dokumen') {
    document.getElementById('dok-list').innerHTML = '';
    dokIdx = 0;
    addDokRow({judul: m.judul, editMode: true, currentFile: m.nama_file, allow_download: m.allow_download});
    document.getElementById('dok-status').value = m.status || 'Draft';
    document.getElementById('btn-add-dok').classList.add('hidden');
  } else {
    document.getElementById('lnk-list').innerHTML = '';
    lnkIdx = 0;
    addLinkRow({judul: m.judul, url: m.url, type: m.tipe, editMode: true});
    document.getElementById('lnk-status').value = m.status || 'Draft';
    document.getElementById('btn-add-lnk').classList.add('hidden');
  }

  // Show edit banner
  document.getElementById('edit-mode-label').textContent = m.judul;
  const banner = document.getElementById('edit-mode-banner');
  banner.classList.remove('hidden');
  banner.style.display = 'flex';
  document.getElementById('panel-title').textContent = 'Edit Materi';

  // Update submit button labels
  const icon = '<i class="fa-solid fa-floppy-disk mr-1.5"></i>';
  document.getElementById('btn-teks-text').innerHTML = icon + 'Simpan Perubahan';
  document.getElementById('btn-dok-text').innerHTML  = icon + 'Simpan Perubahan';
  document.getElementById('btn-lnk-text').innerHTML  = icon + 'Simpan Perubahan';

  // Scroll right panel into view
  document.getElementById('right-panel-card').scrollIntoView({behavior:'smooth', block:'nearest'});
}

function cancelEdit() {
  editId = null;
  document.getElementById('edit-mode-banner').classList.add('hidden');
  document.getElementById('edit-mode-banner').style.display = '';
  document.getElementById('panel-title').textContent = 'Tambah Materi';

  // Restore submit button labels
  document.getElementById('btn-teks-text').innerHTML = '<i class="fa-solid fa-plus mr-1.5"></i>Tambah Materi Teks';
  document.getElementById('btn-dok-text').innerHTML  = '<i class="fa-solid fa-upload mr-1.5"></i>Upload Dokumen';
  document.getElementById('btn-lnk-text').innerHTML  = '<i class="fa-solid fa-plus mr-1.5"></i>Simpan Link';

  // Show add-more buttons
  document.getElementById('btn-add-dok').classList.remove('hidden');
  document.getElementById('btn-add-lnk').classList.remove('hidden');

  // Reset forms
  document.getElementById('dok-list').innerHTML = ''; dokIdx = 0; addDokRow();
  document.getElementById('lnk-list').innerHTML = ''; lnkIdx = 0; addLinkRow();
  document.getElementById('teks-judul').value = '';
  document.getElementById('teks-konten').value = '';
  document.getElementById('teks-deskripsi').value = '';
}

async function runEdit(fd, type) {
  fd.append('_method', 'PUT');
  fd.append('tipe', type);
  setLoad('btn-' + (type === 'teks' ? 'teks' : type === 'dokumen' ? 'dok' : 'lnk'), true);
  try {
    const r = await fetch(ROUTES.update(editId), {
      method:'POST', headers:{'X-CSRF-TOKEN':CSRF,'Accept':'application/json'}, body:fd,
    });
    const j = await r.json();
    if (!r.ok) { showToast('error', firstError(j)); return; }
    showToast('success', j.message);
    updateSortRow(j.materi);
    const idx = allMateri.findIndex(x => x.id === editId);
    if (idx >= 0) allMateri[idx] = {...allMateri[idx], ...j.materi};
    cancelEdit();
  } catch { showToast('error','Terjadi kesalahan.'); }
  finally { setLoad('btn-' + (type === 'teks' ? 'teks' : type === 'dokumen' ? 'dok' : 'lnk'), false); }
}

function updateSortRow(m) {
  const row = sortEl?.querySelector(`[data-id="${m.id}"]`);
  if (!row) return;
  const tipeColor = {dokumen:'tc-dokumen',video:'tc-video',link:'tc-link',teks:'tc-teks'}[m.tipe] || 'a-bg-lt a-text';
  const tipeIcon  = {dokumen:'fa-file-lines',video:'fa-circle-play',link:'fa-link',teks:'fa-align-left'}[m.tipe] || 'fa-file';
  row.querySelector('.font-semibold').textContent = m.judul;
  const iconEl = row.querySelector('.tipe-icon');
  if (iconEl) { iconEl.className = `tipe-icon ${tipeColor}`; iconEl.innerHTML = `<i class="fa-solid ${tipeIcon}"></i>`; }
}

/* ══ SUBMIT: TEKS ═════════════════════════════════════════════ */
async function submitTeks() {
  const judul  = document.getElementById('teks-judul').value.trim();
  const konten = document.getElementById('teks-konten').value.trim();
  if (!judul || !konten) { showToast('error','Judul dan konten wajib diisi.'); return; }

  const fd = new FormData();
  fd.append('judul', judul);
  fd.append('konten', konten);
  fd.append('deskripsi', document.getElementById('teks-deskripsi').value);
  fd.append('status', document.getElementById('teks-status').value);

  if (editId !== null) { return runEdit(fd, 'teks'); }

  fd.append('mata_kuliah_id', MK_ID);
  fd.append('pokok_bahasan_id', PB_ID);
  fd.append('tipe', 'teks');
  setLoad('btn-teks', true);
  try {
    const r = await postJSON(ROUTES.store, fd);
    const j = await r.json();
    if (!r.ok) { showToast('error', firstError(j)); return; }
    showToast('success', j.message);
    insertSortRow(j.materi);
    allMateri.push(j.materi);
    document.getElementById('teks-judul').value = '';
    document.getElementById('teks-konten').value = '';
    document.getElementById('teks-deskripsi').value = '';
  } catch { showToast('error','Terjadi kesalahan.'); }
  finally { setLoad('btn-teks', false); }
}

/* ══ SUBMIT: DOKUMEN ══════════════════════════════════════════ */
async function submitDokumen() {
  const rows = document.querySelectorAll('#dok-list .multi-item');
  if (!rows.length) { showToast('error','Tambahkan minimal 1 dokumen.'); return; }

  // Edit mode: single row, file optional
  if (editId !== null) {
    const id  = rows[0].id.replace('dok-row-','');
    const judul = document.getElementById('dok-judul-'+id).value.trim();
    if (!judul) { showToast('error','Judul wajib diisi.'); return; }
    const fd = new FormData();
    fd.append('judul', judul);
    fd.append('status', document.getElementById('dok-status').value);
    fd.append('allow_download', document.getElementById('dok-allow-dl-'+id)?.checked ? '1' : '0');
    const file = document.getElementById('dok-file-'+id)?.files[0];
    if (file) fd.append('file', file);
    return runEdit(fd, 'dokumen');
  }

  // Create mode: validate all rows have file
  let hasError = false;
  rows.forEach(row => {
    const id = row.id.replace('dok-row-','');
    if (!document.getElementById('dok-judul-'+id).value.trim()) hasError = true;
    if (!document.getElementById('dok-file-'+id).files[0])      hasError = true;
  });
  if (hasError) { showToast('error','Setiap dokumen harus memiliki judul dan file.'); return; }

  setLoad('btn-dok', true);
  const status = document.getElementById('dok-status').value;
  let success = 0;
  for (const row of rows) {
    const id = row.id.replace('dok-row-','');
    const fd = new FormData();
    fd.append('mata_kuliah_id', MK_ID); fd.append('pokok_bahasan_id', PB_ID);
    fd.append('tipe', 'dokumen');
    fd.append('judul', document.getElementById('dok-judul-'+id).value.trim());
    fd.append('status', status);
    fd.append('allow_download', document.getElementById('dok-allow-dl-'+id)?.checked ? '1' : '0');
    fd.append('file', document.getElementById('dok-file-'+id).files[0]);
    try {
      const r = await postJSON(ROUTES.store, fd);
      const j = await r.json();
      if (r.ok) { insertSortRow(j.materi); allMateri.push(j.materi); success++; }
      else showToast('error', firstError(j));
    } catch { showToast('error','Upload gagal.'); }
  }
  setLoad('btn-dok', false);
  if (success > 0) {
    showToast('success', `${success} dokumen berhasil diupload.`);
    document.getElementById('dok-list').innerHTML = ''; dokIdx = 0; addDokRow();
  }
}

/* ══ SUBMIT: LINK ════════════════════════════════════════════ */
async function submitLink() {
  const rows = document.querySelectorAll('#lnk-list .multi-item');
  if (!rows.length) { showToast('error','Tambahkan minimal 1 link.'); return; }

  // Edit mode: single row
  if (editId !== null) {
    const id  = rows[0].id.replace('lnk-row-','');
    const judul = document.getElementById('lnk-judul-'+id).value.trim();
    const url   = document.getElementById('lnk-url-'+id).value.trim();
    if (!judul || !url) { showToast('error','Judul dan URL wajib diisi.'); return; }
    const fd = new FormData();
    fd.append('judul', judul); fd.append('url', url);
    fd.append('status', document.getElementById('lnk-status').value);
    return runEdit(fd, document.getElementById('lnk-type-'+id).value);
  }

  let hasError = false;
  rows.forEach(row => {
    const id = row.id.replace('lnk-row-','');
    if (!document.getElementById('lnk-judul-'+id).value.trim()) hasError = true;
    if (!document.getElementById('lnk-url-'+id).value.trim())   hasError = true;
  });
  if (hasError) { showToast('error','Setiap link harus memiliki judul dan URL.'); return; }

  setLoad('btn-lnk', true);
  const status = document.getElementById('lnk-status').value;
  let success = 0;
  for (const row of rows) {
    const id   = row.id.replace('lnk-row-','');
    const tipe = document.getElementById('lnk-type-'+id).value;
    const fd = new FormData();
    fd.append('mata_kuliah_id', MK_ID); fd.append('pokok_bahasan_id', PB_ID);
    fd.append('tipe', tipe);
    fd.append('judul', document.getElementById('lnk-judul-'+id).value.trim());
    fd.append('url', document.getElementById('lnk-url-'+id).value.trim());
    fd.append('status', status);
    try {
      const r = await postJSON(ROUTES.store, fd);
      const j = await r.json();
      if (r.ok) { insertSortRow(j.materi); allMateri.push(j.materi); success++; }
      else showToast('error', firstError(j));
    } catch { showToast('error','Gagal menyimpan link.'); }
  }
  setLoad('btn-lnk', false);
  if (success > 0) {
    showToast('success', `${success} link berhasil ditambahkan.`);
    document.getElementById('lnk-list').innerHTML = ''; lnkIdx = 0; addLinkRow();
  }
}

/* ══ INSERT DOM ROW ═══════════════════════════════════════════ */
function insertSortRow(m) {
  // If sortable list doesn't exist yet (page was empty), create it
  if (!sortEl) {
    const cardBody = document.querySelector('.card-body');
    const emptyEl  = cardBody?.querySelector('.text-center');
    if (!cardBody) return;
    sortEl = document.createElement('div');
    sortEl.id = 'sortable-list';
    if (emptyEl) emptyEl.replaceWith(sortEl);
    else cardBody.appendChild(sortEl);
    initSortable(sortEl);
  }
  // Remove inline empty-state if somehow still present
  sortEl.querySelector('.text-center')?.remove();

  const tipeColor = {dokumen:'tc-dokumen',video:'tc-video',link:'tc-link',teks:'tc-teks'}[m.tipe] || 'a-bg-lt a-text';
  const tipeIcon  = {dokumen:'fa-file-lines',video:'fa-circle-play',link:'fa-link',teks:'fa-align-left'}[m.tipe] || 'fa-file';
  const div = document.createElement('div');
  div.className = 'materi-sort-item'; div.dataset.id = m.id;
  div.innerHTML = `
    <span class="drag-handle"><i class="fa-solid fa-grip-vertical"></i></span>
    <div class="tipe-icon ${tipeColor}"><i class="fa-solid ${tipeIcon}"></i></div>
    <div class="flex-1 min-w-0">
      <div class="text-[13px] font-semibold truncate" style="color:var(--text)">${escHtml(m.judul)}</div>
      <div class="text-[10px] mt-0.5" style="color:var(--muted)">${escHtml(m.tipe_label||m.tipe)}</div>
    </div>
    <label class="toggle-switch">
      <input type="checkbox" ${m.status==='Aktif'?'checked':''} onchange="toggleMateri(${m.id}, this)">
      <div class="toggle-track"><div class="toggle-thumb"></div></div>
    </label>
    <div class="flex items-center gap-1 flex-shrink-0">
      <button onclick="openEditInPanel(${m.id})"
              class="w-7 h-7 rounded-lg grid place-items-center text-[11px] hover:opacity-75"
              style="background:var(--surface);color:var(--muted);border:1px solid var(--border)">
        <i class="fa-solid fa-pen"></i>
      </button>
      <button onclick="deleteMateri(${m.id},'${escHtml(m.judul).replace(/'/g,"\\'")}') "
              class="w-7 h-7 rounded-lg grid place-items-center text-[11px] bg-red-500/10 text-red-400 hover:opacity-75">
        <i class="fa-solid fa-trash"></i>
      </button>
    </div>`;
  sortEl.appendChild(div);
}

/* ══ TOGGLE ═══════════════════════════════════════════════════ */
async function toggleMateri(id, checkbox) {
  checkbox.disabled = true;
  try {
    const r = await fetch(ROUTES.toggle(id), { method:'PATCH', headers:{'X-CSRF-TOKEN':CSRF,'Accept':'application/json'} });
    const j = await r.json();
    if (!r.ok) { checkbox.checked = !checkbox.checked; showToast('error', j.message||'Gagal'); return; }
    checkbox.checked = j.status === 'Aktif';
    showToast('success', j.message);
  } catch { checkbox.checked = !checkbox.checked; showToast('error','Terjadi kesalahan.'); }
  finally { checkbox.disabled = false; }
}

/* ══ DELETE ═══════════════════════════════════════════════════ */
async function deleteMateri(id, judul) {
  if (!confirm(`Hapus materi "${judul}"?`)) return;
  const fd = new FormData(); fd.append('_method','DELETE');
  try {
    const r = await fetch(ROUTES.destroy(id), { method:'POST', headers:{'X-CSRF-TOKEN':CSRF,'Accept':'application/json'}, body:fd });
    const j = await r.json();
    if (!r.ok) { showToast('error', j.message||'Gagal'); return; }
    showToast('success', j.message);
    sortEl?.querySelector(`[data-id="${id}"]`)?.remove();
    const idx = allMateri.findIndex(x => x.id === id);
    if (idx >= 0) allMateri.splice(idx, 1);
    if (editId === id) cancelEdit();
  } catch { showToast('error','Terjadi kesalahan.'); }
}

/* ══ HELPERS ═════════════════════════════════════════════════ */
function postJSON(url, fd) {
  return fetch(url, { method:'POST', headers:{'X-CSRF-TOKEN':CSRF,'Accept':'application/json'}, body:fd });
}
function setLoad(id, on) {
  const btn = document.getElementById(id);
  if (btn) btn.disabled = on;
  document.getElementById(id+'-text')?.classList.toggle('hidden', on);
  document.getElementById(id+'-spin')?.classList.toggle('hidden', !on);
}
function firstError(j) {
  return j.message || Object.values(j.errors||{})[0]?.[0] || 'Terjadi kesalahan.';
}
function escHtml(s) {
  return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

/* ══ INIT ════════════════════════════════════════════════════ */
switchTab('dokumen');
addDokRow();
addLinkRow();

/* ══ FAB + DISKUSI + AI ══════════════════════════════════════ */
const PB_KELAS  = @json($kelasList);
const DSK_SEEN  = 'lms_dsk_seen_instruktur_pb_{{ $pokokBahasan->id }}';
let   pbOthersCount = 0; // others_count untuk PB ini
const pbAiHist  = {}; // materiId → messages[]
let   fabIsOpen = false;
let   _pbAiId   = null; // materiId aktif di drawer AI

// ── Selector helpers ─────────────────────────────────────────
function pbBuildMateriSel(selId, onChangeFn) {
  const sel = document.getElementById(selId);
  sel.innerHTML = '';
  allMateri.forEach(m => {
    const o = document.createElement('option');
    o.value = m.id;
    o.textContent = m.judul.length > 30 ? m.judul.substring(0,30)+'…' : m.judul;
    sel.appendChild(o);
  });
  if (onChangeFn) sel.onchange = onChangeFn;
}
function pbBuildKelasSel() {
  const sel = document.getElementById('pb-kelas-sel');
  sel.innerHTML = '';
  if (!PB_KELAS.length) { sel.style.display='none'; return; }
  PB_KELAS.forEach(k => {
    const o = document.createElement('option');
    o.value = k.id;
    o.textContent = k.kode_seksi ? 'Seksi '+k.kode_seksi : 'Kelas '+k.id;
    sel.appendChild(o);
  });
  sel.style.display = PB_KELAS.length > 1 ? '' : 'none';
}

// ── FAB ──────────────────────────────────────────────────────
function toggleFab() {
  fabIsOpen = !fabIsOpen;
  document.getElementById('fab-root').classList.toggle('open', fabIsOpen);
  document.getElementById('fab-backdrop').classList.toggle('show', fabIsOpen);
}
function closeFab() {
  fabIsOpen = false;
  document.getElementById('fab-root').classList.remove('open');
  document.getElementById('fab-backdrop').classList.remove('show');
}
function fabOpenDiskusi() { closeFab(); pbOpenDiskusi(); }
function fabOpenAi()      { closeFab(); pbOpenAi(); }

// ── Overlay / drawer open-close ───────────────────────────────
function pbCloseAll() {
  document.getElementById('pb-overlay').classList.remove('show');
  document.getElementById('pb-dsk-drawer').classList.remove('show');
  document.getElementById('pb-ai-drawer').classList.remove('show');
  pbDskStopFast();
}
document.addEventListener('keydown', e => { if (e.key==='Escape') { pbCloseAll(); closeFab(); } });

// ── Rangkuman PB toggle ──────────────────────────────────────
async function togglePbRangkuman(checkbox) {
  checkbox.disabled = true;
  try {
    const r = await fetch('{{ route('instruktur.pokok-bahasan.toggle-rangkuman', $pokokBahasan->id) }}', {
      method: 'PATCH',
      headers: {
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || '',
      },
    });
    const j = await r.json();
    if (!r.ok) throw new Error(j.message || 'Gagal');
    const txt = document.getElementById('rangkuman-status-txt');
    if (txt) txt.textContent = j.rangkuman_aktif
      ? 'Aktif — mahasiswa wajib merangkum'
      : 'Nonaktif';
    showToast('success', j.message);
  } catch {
    checkbox.checked = !checkbox.checked; // revert
    showToast('error', 'Gagal mengubah pengaturan rangkuman.');
  } finally {
    checkbox.disabled = false;
  }
}

// ── DISKUSI polling ──────────────────────────────────────────
window._hasDskLocalPoll = true;
let _pbDskLastId   = 0;
let _pbDskFastTimer = null;
let _pbDskSlowTimer = null;

// ── Browser notifications ─────────────────────────────────────
(function initNotifPermission() {
  if (!('Notification' in window) || Notification.permission !== 'default') return;
  const ask = () => { Notification.requestPermission(); document.removeEventListener('click', ask); };
  document.addEventListener('click', ask, { once: true });
})();

function pbDskShowNotif(messages, onClickFn) {
  if (!('Notification' in window) || Notification.permission !== 'granted') return;
  const others = messages.filter(d => !d.is_own);
  if (!others.length) return;
  const last  = others[others.length - 1];
  const extra = others.length > 1 ? ` (+${others.length - 1} lainnya)` : '';
  const body  = `${last.name}: ${last.pesan.substring(0, 80)}${last.pesan.length > 80 ? '…' : ''}${extra}`;
  const n = new Notification('💬 Diskusi — {{ $pokokBahasan->judul }}', {
    body,
    icon: '{{ asset("favicon.ico") }}',
    tag: 'lms-diskusi-pb-{{ $pokokBahasan->id }}',
    renotify: true,
  });
  n.onclick = () => { window.focus(); n.close(); onClickFn?.(); };
  setTimeout(() => n.close(), 12000);
}

function pbDskPlaySound() {
  try {
    const ctx = new (window.AudioContext || window.webkitAudioContext)();
    const o = ctx.createOscillator(), g = ctx.createGain();
    o.connect(g); g.connect(ctx.destination);
    o.type = 'sine';
    o.frequency.setValueAtTime(880, ctx.currentTime);
    o.frequency.exponentialRampToValueAtTime(660, ctx.currentTime + 0.12);
    g.gain.setValueAtTime(0.1, ctx.currentTime);
    g.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.28);
    o.start(); o.stop(ctx.currentTime + 0.28);
  } catch {}
}

function pbDskStartFast() {
  pbDskStopFast();
  _pbDskFastTimer = setInterval(pbDskPollFast, 4000);
}
function pbDskStopFast() {
  if (_pbDskFastTimer) { clearInterval(_pbDskFastTimer); _pbDskFastTimer = null; }
}
function pbDskStartSlow() {
  if (_pbDskSlowTimer) return;
  _pbDskSlowTimer = setInterval(pbDskPollSlow, 30000);
}

async function pbDskPollFast() {
  const dskVisible = document.getElementById('pb-dsk-drawer')?.classList.contains('show');
  if (!dskVisible) return;
  const kelas = pbGetKelasId();
  if (!kelas) return;
  try {
    const r = await fetch(`/diskusi/pb/${PB_ID}?kelas_id=${kelas}&after_id=${_pbDskLastId}`, {
      headers: { 'Accept': 'application/json' }
    });
    if (!r.ok) return;
    const j = await r.json();
    pbOthersCount = j.others_count ?? pbOthersCount;
    pbRefreshBadge();
    if (!j.diskusi?.length) return;

    const msgs = document.getElementById('pb-dsk-msgs');
    if (!msgs) return;
    msgs.querySelector('.pb-empty')?.remove();
    let hasNew = false;
    j.diskusi.forEach(d => {
      if (d.id > _pbDskLastId) _pbDskLastId = d.id;
      if (!document.getElementById(`pbdm-${d.id}`)) {
        msgs.insertAdjacentHTML('beforeend', pbDskRow(d));
        if (!d.is_own) hasNew = true;
      }
    });
    window.gdskSyncLastId?.(_pbDskLastId);
    msgs.scrollTop = msgs.scrollHeight;
    if (hasNew) { pbMarkSeen(); pbDskPlaySound(); pbDskShowNotif(j.diskusi, null); injectDskNavNotif(j.diskusi, null); }
  } catch {}
}

async function pbDskPollSlow() {
  const dskVisible = document.getElementById('pb-dsk-drawer')?.classList.contains('show');
  if (dskVisible) return;
  const kelas = PB_KELAS[0]?.id;
  if (!kelas) return;
  try {
    const prev = pbOthersCount;
    const r = await fetch(`/diskusi/pb/${PB_ID}?kelas_id=${kelas}&after_id=${_pbDskLastId}`, {
      headers: { 'Accept': 'application/json' }
    });
    if (!r.ok) return;
    const j = await r.json();
    pbOthersCount = j.others_count ?? pbOthersCount;
    if (j.diskusi?.length) j.diskusi.forEach(d => { if (d.id > _pbDskLastId) _pbDskLastId = d.id; });
    window.gdskSyncLastId?.(_pbDskLastId);
    pbRefreshBadge();
    if (pbOthersCount > prev) {
      pbDskPlaySound();
      pbDskShowNotif(j.diskusi, () => pbOpenDiskusi());
      injectDskNavNotif(j.diskusi, () => pbOpenDiskusi());
    }
  } catch {}
}

// ── DISKUSI ──────────────────────────────────────────────────
function pbOpenDiskusi() {
  pbBuildKelasSel();
  document.getElementById('pb-ai-drawer').classList.remove('show');
  document.getElementById('pb-dsk-inp').value = '';
  document.getElementById('pb-overlay').classList.add('show');
  document.getElementById('pb-dsk-drawer').classList.add('show');
  pbLoadDiskusi();
  pbDskStartFast();
}

function pbGetKelasId() { return document.getElementById('pb-kelas-sel').value || null; }

async function pbLoadDiskusi() {
  const kelas = pbGetKelasId();
  const msgs  = document.getElementById('pb-dsk-msgs');
  if (!kelas) {
    msgs.innerHTML = '<div class="pb-empty">Pilih kelas terlebih dahulu.</div>';
    return;
  }
  msgs.innerHTML = '<div class="pb-empty"><i class="fa-solid fa-circle-notch fa-spin mr-1"></i>Memuat diskusi…</div>';
  try {
    const r = await fetch(`/diskusi/pb/${PB_ID}?kelas_id=${kelas}`, {
      headers:{ 'Accept':'application/json','X-Requested-With':'XMLHttpRequest' }
    });
    const j = await r.json();
    pbRenderDiskusi(j.diskusi || []);
    pbOthersCount = j.others_count ?? 0;
    pbMarkSeen();
    if (j.diskusi?.length) { _pbDskLastId = j.diskusi.reduce((m, d) => Math.max(m, d.id), _pbDskLastId); window.gdskSyncLastId?.(_pbDskLastId); }
  } catch {
    msgs.innerHTML = '<div class="pb-empty" style="color:#f87171">Gagal memuat diskusi.</div>';
  }
}

function pbRenderDiskusi(list) {
  const msgs = document.getElementById('pb-dsk-msgs');
  if (!list.length) {
    msgs.innerHTML = `<div class="pb-empty">
      <i class="fa-regular fa-comment-dots text-[22px] opacity-30 block mb-1"></i>
      Belum ada diskusi. Mulailah percakapan!
    </div>`;
    return;
  }
  msgs.innerHTML = list.map(d => pbDskRow(d)).join('');
  msgs.scrollTop = msgs.scrollHeight;
}

function pbDskRow(d) {
  const ownCls = d.is_own ? 'own' : '';
  const itag   = d.is_instruktur ? `<span class="pb-itag">Instruktur</span>` : '';
  const del    = `<button class="pb-del" onclick="pbDelDiskusi(${d.id})"><i class="fa-solid fa-trash text-[9px]"></i></button>`;
  return `<div class="pb-chat-row ${ownCls}" id="pbdm-${d.id}">
    <div class="pb-avatar" style="background:${d.color}">${d.initials}</div>
    <div class="pb-body">
      <div class="pb-meta">${escHtml(d.name)}${itag}</div>
      <div class="pb-bubble">${escHtml(d.pesan)}</div>
      <div class="pb-meta">${escHtml(d.waktu)} ${del}</div>
    </div>
  </div>`;
}

async function pbSendDiskusi() {
  const kelas = pbGetKelasId();
  const inp   = document.getElementById('pb-dsk-inp');
  const pesan = inp.value.trim();
  if (!pesan || !kelas) return;
  const btn = document.getElementById('pb-dsk-send');
  btn.disabled = inp.disabled = true;
  try {
    const fd = new FormData();
    fd.append('kelas_id', kelas); fd.append('pesan', pesan);
    const r = await fetch(`/diskusi/pb/${PB_ID}`, {
      method:'POST', headers:{'Accept':'application/json','X-CSRF-TOKEN':CSRF}, body:fd
    });
    const j = await r.json();
    if (r.ok && j.diskusi) {
      inp.value = '';
      const msgs = document.getElementById('pb-dsk-msgs');
      msgs.querySelector('.pb-empty')?.remove();
      msgs.insertAdjacentHTML('beforeend', pbDskRow(j.diskusi));
      msgs.scrollTop = msgs.scrollHeight;
      pbMarkSeen();
    }
  } catch {}
  btn.disabled = inp.disabled = false;
  inp.focus();
}

async function pbDelDiskusi(id) {
  if (!confirm('Hapus pesan ini?')) return;
  try {
    const row  = document.getElementById(`pbdm-${id}`);
    const own  = row?.classList.contains('own');
    const r    = await fetch(`/diskusi/${id}`, {
      method:'DELETE', headers:{'Accept':'application/json','X-CSRF-TOKEN':CSRF}
    });
    if (r.ok) {
      row?.remove();
      if (!document.querySelector('#pb-dsk-msgs .pb-chat-row'))
        document.getElementById('pb-dsk-msgs').innerHTML = `<div class="pb-empty">
          <i class="fa-regular fa-comment-dots text-[22px] opacity-30 block mb-1"></i>Belum ada diskusi.
        </div>`;
      if (!own) { pbOthersCount = Math.max(0, pbOthersCount - 1); pbMarkSeen(); }
    }
  } catch {}
}

// ── Unread badge ─────────────────────────────────────────────
function pbGetSeen() { return parseInt(localStorage.getItem(DSK_SEEN) || '0'); }
function pbMarkSeen() {
  localStorage.setItem(DSK_SEEN, String(pbOthersCount));
  pbRefreshBadge();
}
function pbRefreshBadge() {
  const unread = Math.max(0, pbOthersCount - pbGetSeen());
  const badge = document.getElementById('fab-badge');
  if (!badge) return;
  if (unread>0){ badge.textContent=unread>99?'99+':unread; badge.classList.remove('hidden'); }
  else badge.classList.add('hidden');
}

// Initial badge fetch + start background slow poll
(async () => {
  const kelas = PB_KELAS[0]?.id;
  if (!kelas) return;
  try {
    const r = await fetch(`/diskusi/pb/${PB_ID}?kelas_id=${kelas}`,{headers:{'Accept':'application/json'}});
    if (r.ok) {
      const j = await r.json();
      pbOthersCount = j.others_count ?? 0;
      if (j.diskusi?.length) { _pbDskLastId = j.diskusi.reduce((m, d) => Math.max(m, d.id), 0); window.gdskSyncLastId?.(_pbDskLastId); }
    }
  } catch {}
  pbRefreshBadge();
  pbDskStartSlow();
})();

// ── TANYA AI ─────────────────────────────────────────────────
function pbOpenAi() {
  pbBuildMateriSel('pb-ai-materi-sel', pbOnAiMateriChange);
  const first = allMateri[0];
  if (first) { _pbAiId = first.id; pbShowAiHistory(first.id); }
  document.getElementById('pb-dsk-drawer').classList.remove('show');
  document.getElementById('pb-overlay').classList.add('show');
  document.getElementById('pb-ai-drawer').classList.add('show');
  document.getElementById('pb-ai-inp').focus();
}

function pbOnAiMateriChange() {
  _pbAiId = parseInt(document.getElementById('pb-ai-materi-sel').value) || null;
  pbShowAiHistory(_pbAiId);
}

function pbShowAiHistory(id) {
  const msgsEl = document.getElementById('pb-ai-msgs');
  if (!id || !pbAiHist[id]?.length) {
    msgsEl.innerHTML = `<div class="pb-empty" id="pb-ai-welcome">
      <div class="w-12 h-12 rounded-2xl grid place-items-center" style="background:rgba(99,102,241,.12)">
        <i class="fa-solid fa-robot text-[22px]" style="color:#818cf8"></i>
      </div>
      <p>Tanyakan apa saja tentang materi ini.</p>
    </div>`;
  } else {
    msgsEl.innerHTML = pbAiHist[id].map(pbAiRow).join('');
    msgsEl.scrollTop = msgsEl.scrollHeight;
  }
}

function pbAiRow(msg) {
  if (msg.role==='user') return `<div class="pb-chat-row own">
    <div class="pb-avatar" style="background:var(--ac)">Sy</div>
    <div class="pb-body"><div class="pb-bubble">${escHtml(msg.content)}</div></div>
  </div>`;
  return `<div class="pb-chat-row bot">
    <div class="pb-avatar" style="background:#4f46e5"><i class="fa-solid fa-robot text-[9px]"></i></div>
    <div class="pb-body"><div class="pb-bubble">${pbFmtAi(msg.content)}</div></div>
  </div>`;
}

function pbFmtAi(t) {
  return escHtml(t)
    .replace(/\*\*(.+?)\*\*/g,'<strong>$1</strong>')
    .replace(/`(.+?)`/g,'<code style="background:rgba(99,102,241,.15);padding:1px 5px;border-radius:4px;font-size:11px">$1</code>')
    .replace(/\n/g,'<br>');
}

async function pbSendAi() {
  const inp     = document.getElementById('pb-ai-inp');
  const content = inp.value.trim();
  if (!content || !_pbAiId) return;
  const btn = document.getElementById('pb-ai-send');
  btn.disabled = true; inp.value = '';

  if (!pbAiHist[_pbAiId]) pbAiHist[_pbAiId] = [];
  pbAiHist[_pbAiId].push({role:'user',content});

  const msgsEl = document.getElementById('pb-ai-msgs');
  msgsEl.querySelector('#pb-ai-welcome')?.remove();
  msgsEl.insertAdjacentHTML('beforeend', pbAiRow({role:'user',content}));

  const tid = 'pbt-'+Date.now();
  msgsEl.insertAdjacentHTML('beforeend',`<div class="pb-chat-row bot" id="${tid}">
    <div class="pb-avatar" style="background:#4f46e5"><i class="fa-solid fa-robot text-[9px]"></i></div>
    <div class="pb-body"><div class="pb-bubble"><span class="tdots"><span></span><span></span><span></span></span></div></div>
  </div>`);
  msgsEl.scrollTop = msgsEl.scrollHeight;

  try {
    const fd = new FormData();
    pbAiHist[_pbAiId].slice(-10).forEach((m,i)=>{
      fd.append(`messages[${i}][role]`,m.role);
      fd.append(`messages[${i}][content]`,m.content);
    });
    const r = await fetch(`/instruktur/materi/${_pbAiId}/ai-chat`,{
      method:'POST',headers:{'Accept':'application/json','X-CSRF-TOKEN':CSRF},body:fd
    });
    const j = await r.json();
    document.getElementById(tid)?.remove();
    if (!r.ok) {
      msgsEl.insertAdjacentHTML('beforeend',`<div class="pb-chat-row bot">
        <div class="pb-avatar" style="background:#ef4444"><i class="fa-solid fa-exclamation text-[9px]"></i></div>
        <div class="pb-body"><div class="pb-bubble" style="color:#fca5a5">${escHtml(j.error||'Gagal menghubungi AI.')}</div></div>
      </div>`);
      pbAiHist[_pbAiId].pop();
    } else {
      pbAiHist[_pbAiId].push({role:'assistant',content:j.reply});
      msgsEl.insertAdjacentHTML('beforeend', pbAiRow({role:'assistant',content:j.reply}));
    }
  } catch {
    document.getElementById(tid)?.remove();
    msgsEl.insertAdjacentHTML('beforeend',`<div class="pb-chat-row bot">
      <div class="pb-avatar" style="background:#ef4444"><i class="fa-solid fa-exclamation text-[9px]"></i></div>
      <div class="pb-body"><div class="pb-bubble" style="color:#fca5a5">Koneksi gagal. Coba lagi.</div></div>
    </div>`);
    pbAiHist[_pbAiId]?.pop();
  } finally {
    btn.disabled = false;
    msgsEl.scrollTop = msgsEl.scrollHeight;
    inp.focus();
  }
}
</script>
@endpush
