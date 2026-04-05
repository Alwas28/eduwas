@extends('layouts.mahasiswa')
@section('title', 'Pertemuan ' . $pokokBahasan->pertemuan . ' — ' . $pokokBahasan->judul)
@section('page-title', 'Baca Materi')

@push('styles')
<style>
/* ── Wrapper ── */
.read-wrap { max-width:860px; margin:0 auto; }

/* ── PB header card ── */
.pb-header-card {
  background:var(--surface); border:1px solid var(--border); border-radius:18px;
  padding:18px 22px; display:flex; align-items:flex-start; gap:14px;
}

/* ── Materi card ── */
.materi-card {
  background:var(--surface); border:1px solid var(--border); border-radius:18px;
  overflow:hidden; transition:border-color .2s;
}
.materi-card-head {
  display:flex; align-items:center; gap:12px;
  padding:14px 20px; border-bottom:1px solid var(--border);
}
.materi-card-body { padding:20px; }

/* ── Tipe badge ── */
.tipe-badge { display:inline-flex; align-items:center; gap:5px; padding:3px 9px; border-radius:20px; font-size:10px; font-weight:700; }
.tc-dokumen { background:rgba(59,130,246,.15);  color:#60a5fa; }
.tc-video   { background:rgba(244,63,94,.15);   color:#fb7185; }
.tc-link    { background:rgba(139,92,246,.15);  color:#a78bfa; }
.tc-teks    { background:rgba(245,158,11,.15);  color:#fbbf24; }

/* ── Progress per materi ── */
.materi-progress-bar {
  height:4px; border-radius:2px; overflow:hidden;
  background:var(--border); margin-top:6px;
}
.materi-progress-fill {
  height:100%; border-radius:2px; background:var(--ac);
  transition:width .5s ease;
}
.progress-label { font-size:10px; font-weight:700; color:var(--ac); }

/* ── Teks content ── */
.teks-content {
  font-size:14px; line-height:1.8; color:var(--text);
  white-space:pre-wrap; word-break:break-word;
}

/* ── PDF viewer ── */
.pdf-viewer-wrap {
  border-radius:12px; overflow:hidden;
  background:var(--surface2); border:1px solid var(--border);
}
.pdf-toolbar {
  display:flex; align-items:center; justify-content:space-between; gap:6px; flex-wrap:wrap;
  padding:8px 12px; background:var(--surface);
  border-bottom:1px solid var(--border); font-size:12px;
}
.pdf-toolbar-left  { display:flex; align-items:center; gap:6px; min-width:0; flex:1; }
.pdf-toolbar-right { display:flex; align-items:center; gap:4px; flex-shrink:0; }
.pdf-canvases {
  max-height:72vh; overflow-y:auto; padding:12px;
  display:flex; flex-direction:column; gap:8px;
  background:#525659;
  scroll-behavior:smooth;
}
.pdf-canvases canvas {
  display:block; width:100%; height:auto;
  border-radius:4px; box-shadow:0 2px 8px rgba(0,0,0,.4);
}
.pdf-loading {
  display:flex; flex-direction:column; align-items:center; justify-content:center;
  padding:60px 24px; gap:10px; color:var(--muted); font-size:13px;
}
.pdf-page-label { font-size:11px; color:var(--muted); white-space:nowrap; }

/* PDF fullscreen */
.pdf-viewer-wrap:fullscreen,
.pdf-viewer-wrap:-webkit-full-screen {
  border-radius:0; border:none;
  display:flex; flex-direction:column; background:#3a3a3a;
}
.pdf-viewer-wrap:fullscreen .pdf-toolbar,
.pdf-viewer-wrap:-webkit-full-screen .pdf-toolbar { flex-shrink:0; }
.pdf-viewer-wrap:fullscreen .pdf-canvases,
.pdf-viewer-wrap:-webkit-full-screen .pdf-canvases { flex:1; max-height:none; }

/* ── Zoom buttons ── */
.zoom-btn {
  display:inline-flex; align-items:center; justify-content:center;
  width:26px; height:26px; border-radius:7px; font-size:14px; font-weight:700;
  border:1px solid var(--border); background:var(--surface2); color:var(--text);
  cursor:pointer; transition:opacity .15s; line-height:1; padding:0;
}
.zoom-btn:hover:not(:disabled) { opacity:.7; }
.zoom-btn:disabled { opacity:.3; cursor:not-allowed; }
.zoom-label {
  font-size:11px; font-weight:700; min-width:38px; text-align:center;
  color:var(--text); user-select:none;
}

/* ── Toolbar button (PDF & video) ── */
.toolbar-btn {
  display:inline-flex; align-items:center; justify-content:center; gap:4px;
  padding:4px 9px; border-radius:8px; font-size:11px; font-weight:600;
  border:1px solid var(--border); background:var(--surface2);
  color:var(--muted); cursor:pointer; text-decoration:none;
  transition:opacity .15s, border-color .15s; white-space:nowrap;
}
.toolbar-btn:hover { opacity:.8; border-color:rgba(var(--ac-rgb),.4); color:var(--text); }

/* ── Separator in toolbar ── */
.toolbar-sep { width:1px; height:18px; background:var(--border); flex-shrink:0; }

/* ── File download card ── */
.file-card {
  display:flex; align-items:center; gap:12px; padding:14px 16px;
  border-radius:12px; border:1px solid var(--border); background:var(--surface2);
}

/* ── Link card ── */
.link-card {
  display:flex; align-items:center; gap:12px; padding:14px 16px;
  border-radius:12px; border:1px solid var(--border); background:var(--surface2);
  text-decoration:none; transition:border-color .15s;
}
.link-card:hover { border-color:rgba(var(--ac-rgb),.5); }

/* ── Video section ── */
.video-toolbar {
  display:flex; align-items:center; justify-content:flex-end; gap:6px; margin-bottom:8px;
}
.video-wrap {
  position:relative; width:100%; padding-top:56.25%;
  border-radius:12px; overflow:hidden; background:#000;
}
.video-wrap iframe { position:absolute; inset:0; width:100%; height:100%; border:none; }

/* Video fullscreen: remove aspect-ratio trick, fill screen */
.video-wrap:fullscreen,
.video-wrap:-webkit-full-screen {
  padding-top:0 !important;
  width:100vw; height:100vh;
  border-radius:0; background:#000;
}
.video-wrap:fullscreen iframe,
.video-wrap:-webkit-full-screen iframe { position:static; width:100%; height:100%; }

/* ── Done badge ── */
.done-badge {
  display:none; align-items:center; gap:4px;
  font-size:10px; font-weight:700; padding:2px 8px; border-radius:20px;
  background:rgba(16,185,129,.15); color:#10b981;
}
.done-badge.show { display:inline-flex; }

/* ── FAB Speed Dial ── */
.fab-backdrop {
  position:fixed; inset:0; z-index:498;
  background:rgba(0,0,0,.25); opacity:0;
  pointer-events:none; transition:opacity .2s;
}
.fab-backdrop.show { opacity:1; pointer-events:all; }

.fab-root {
  position:fixed; right:24px; bottom:32px; z-index:499;
  display:flex; flex-direction:column; align-items:flex-end; gap:12px;
  pointer-events:none;
}
/* sub-items container */
.fab-items {
  display:flex; flex-direction:column; align-items:flex-end; gap:10px;
}
.fab-item {
  display:flex; align-items:center; gap:10px;
  opacity:0; transform:translateY(14px) scale(.8);
  pointer-events:none;
  transition:opacity .18s var(--d,0s), transform .18s var(--d,0s);
}
.fab-root.open .fab-item {
  opacity:1; transform:none; pointer-events:all;
}
.fab-lbl {
  font-size:12px; font-weight:600; color:var(--text);
  background:var(--surface2); border:1px solid var(--border);
  padding:5px 12px; border-radius:20px;
  box-shadow:0 2px 10px rgba(0,0,0,.2);
  white-space:nowrap; user-select:none;
}
/* sub buttons */
.fab-sub {
  width:44px; height:44px; border-radius:50%;
  display:grid; place-items:center;
  border:none; cursor:pointer; font-size:16px; color:#fff;
  box-shadow:0 3px 10px rgba(0,0,0,.3); position:relative; flex-shrink:0;
  transition:transform .15s;
}
.fab-sub:hover { transform:scale(1.1); }
.fab-sub-dsk { background:var(--ac); }
.fab-sub-ai  { background:#4f46e5; }
/* main trigger */
.fab-main {
  width:52px; height:52px; border-radius:50%;
  display:grid; place-items:center;
  border:none; cursor:pointer; font-size:20px; color:#fff;
  background:var(--ac);
  box-shadow:0 4px 16px rgba(0,0,0,.35); flex-shrink:0;
  transition:box-shadow .2s;
  pointer-events:auto;
}
.fab-main:hover { box-shadow:0 6px 22px rgba(0,0,0,.45); }
.fab-main-icon { display:block; transition:transform .3s cubic-bezier(.34,1.56,.64,1); }
.fab-root.open .fab-main-icon { transform:rotate(45deg); }
/* badge */
.fab-badge {
  position:absolute; top:-5px; right:-5px;
  min-width:18px; height:18px; border-radius:9px; padding:0 4px;
  background:#ef4444; color:#fff; font-size:10px; font-weight:700;
  display:flex; align-items:center; justify-content:center;
  border:2px solid var(--surface);
}
/* instruktur tag in chat */
.instruktur-tag {
  font-size:9px; font-weight:700; padding:1px 6px; border-radius:4px;
  background:rgba(245,158,11,.15); color:#f59e0b;
  margin-left:4px; vertical-align:middle;
}

/* ── Reader avatars in card footer ── */
.readers-strip { display:flex; align-items:center; margin-left:auto; gap:2px; }
.r-avatar {
  width:22px; height:22px; border-radius:50%;
  display:grid; place-items-content:center; place-items:center;
  font-size:8px; font-weight:700; color:#fff;
  border:2px solid var(--surface); margin-left:-5px;
  flex-shrink:0; cursor:default;
}
.r-more { font-size:10px; color:var(--muted); margin-left:5px; white-space:nowrap; }

/* ── Drawer overlay + panel ── */
.drawer-overlay {
  position:fixed; inset:0; background:rgba(0,0,0,.5); z-index:999;
  opacity:0; pointer-events:none; transition:opacity .2s;
}
.drawer-overlay.open { opacity:1; pointer-events:all; }
.drawer {
  position:fixed; top:0; right:0; bottom:0;
  width:380px; max-width:100vw;
  background:var(--surface); border-left:1px solid var(--border);
  z-index:1000; display:flex; flex-direction:column;
  transform:translateX(100%); transition:transform .25s cubic-bezier(.4,0,.2,1);
}
.drawer.open { transform:translateX(0); }
.drawer-hd {
  display:flex; align-items:center; gap:10px;
  padding:14px 18px; border-bottom:1px solid var(--border); flex-shrink:0;
}
.drawer-hd h3 { flex:1; font-size:14px; font-weight:700; color:var(--text); margin:0; }
.drawer-close {
  width:28px; height:28px; display:grid; place-items:center;
  border-radius:8px; border:1px solid var(--border);
  background:var(--surface2); color:var(--muted); cursor:pointer; font-size:12px;
}
.drawer-close:hover { opacity:.7; }

.drawer-msgs {
  flex:1; overflow-y:auto; padding:14px;
  display:flex; flex-direction:column; gap:10px;
}
.chat-row { display:flex; gap:8px; align-items:flex-start; }
.chat-row.own { flex-direction:row-reverse; }
.c-avatar {
  width:26px; height:26px; border-radius:50%;
  display:grid; place-items:center; font-size:9px; font-weight:700;
  color:#fff; flex-shrink:0; margin-top:2px;
}
.c-bubble {
  display:inline-block; max-width:100%; padding:8px 12px; border-radius:12px;
  font-size:12px; line-height:1.55; white-space:pre-wrap;
  word-break:break-word; overflow-wrap:anywhere;
}
.chat-row:not(.own) .c-bubble {
  background:var(--surface2); color:var(--text); border-bottom-left-radius:3px;
}
.chat-row.own .c-bubble {
  background:var(--ac); color:#fff; border-bottom-right-radius:3px;
}
.chat-row.ai .c-bubble {
  background:rgba(99,102,241,.1); border:1px solid rgba(99,102,241,.2);
  color:var(--text); border-bottom-left-radius:3px;
}
.c-body { min-width:0; flex:1; }
.chat-row.own .c-body { text-align:right; }
.c-meta { font-size:10px; color:var(--muted); margin-top:3px; }
.chat-del {
  opacity:0; background:none; border:none; cursor:pointer;
  color:var(--muted); font-size:10px; padding:2px 4px; border-radius:4px;
  transition:opacity .15s;
}
.chat-row:hover .chat-del { opacity:1; }
.chat-del:hover { color:#ef4444; }

.drawer-empty {
  flex:1; display:flex; flex-direction:column; align-items:center; justify-content:center;
  gap:8px; color:var(--muted); font-size:12px; text-align:center; padding:24px;
}

.drawer-inp-wrap {
  padding:12px 14px; border-top:1px solid var(--border);
  display:flex; gap:8px; flex-shrink:0;
}
.drawer-inp {
  flex:1; resize:none; padding:8px 11px; border-radius:10px;
  border:1px solid var(--border); background:var(--surface2); color:var(--text);
  font-size:12px; line-height:1.5; outline:none; transition:border-color .2s;
  font-family:inherit;
}
.drawer-inp:focus { border-color:var(--ac); }
.drawer-send {
  width:36px; height:36px; display:grid; place-items:center;
  border-radius:10px; border:none; background:var(--ac); color:#fff;
  font-size:13px; cursor:pointer; flex-shrink:0; align-self:flex-end;
  transition:opacity .2s;
}
.drawer-send:hover:not(:disabled) { opacity:.8; }
.drawer-send:disabled { opacity:.4; cursor:default; }

/* ── Rangkuman section ── */
.rangkuman-section {
  background:var(--surface); border:1px solid var(--border); border-radius:18px;
  overflow:hidden; margin-top:-10px; border-top-left-radius:0; border-top-right-radius:0;
  border-top:none;
}
.rangkuman-head {
  display:flex; align-items:center; gap:10px;
  padding:12px 20px; background:rgba(99,102,241,.06); border-bottom:1px solid var(--border);
}
.rangkuman-body { padding:16px 20px; }
.rangkuman-textarea {
  width:100%; resize:vertical; min-height:100px; max-height:300px;
  padding:10px 13px; border-radius:10px; border:1px solid var(--border);
  background:var(--surface2); color:var(--text); font-size:13px; line-height:1.6;
  font-family:inherit; outline:none; transition:border-color .2s;
}
.rangkuman-textarea:focus { border-color:rgba(99,102,241,.6); }
.rangkuman-saved {
  font-size:13px; line-height:1.7; color:var(--text);
  white-space:pre-wrap; word-break:break-word;
  background:var(--surface2); border-radius:10px; padding:12px 14px;
  border:1px solid var(--border);
}
.rangkuman-meta { font-size:11px; color:var(--muted); margin-top:6px; }

/* typing indicator */
.typing-dots { display:inline-flex; gap:3px; align-items:center; padding:2px 0; }
.typing-dots span {
  width:6px; height:6px; border-radius:50%; background:currentColor; opacity:.4;
  animation:tdot 1.2s infinite;
}
.typing-dots span:nth-child(2) { animation-delay:.2s; }
.typing-dots span:nth-child(3) { animation-delay:.4s; }
@keyframes tdot {
  0%,60%,100% { transform:translateY(0); opacity:.4; }
  30% { transform:translateY(-5px); opacity:1; }
}
</style>
@endpush

@section('content')
@php $csrfToken = csrf_token(); @endphp
<div class="space-y-5 animate-fadeUp read-wrap pb-32">

  {{-- Breadcrumb --}}
  <div class="flex items-center gap-2 text-[12px]" style="color:var(--muted)">
    <a href="{{ route('mahasiswa.kelas.index') }}" class="a-text hover:underline">Kelas Saya</a>
    <i class="fa-solid fa-chevron-right text-[10px]"></i>
    <a href="{{ route('mahasiswa.kelas.show', $kelas->id) }}" class="a-text hover:underline">
      {{ $kelas->mataKuliah?->kode ?? '—' }}
    </a>
    <i class="fa-solid fa-chevron-right text-[10px]"></i>
    <span style="color:var(--text)">Pertemuan {{ $pokokBahasan->pertemuan }}</span>
  </div>

  {{-- PB header --}}
  <div class="pb-header-card">
    <div class="w-11 h-11 rounded-xl grid place-items-center font-display font-bold text-[17px] a-bg-lt a-text flex-shrink-0">
      {{ $pokokBahasan->pertemuan }}
    </div>
    <div class="flex-1 min-w-0">
      <h1 class="font-display font-bold text-[19px]" style="color:var(--text)">{{ $pokokBahasan->judul }}</h1>
      @if($pokokBahasan->deskripsi)
        <p class="text-[12px] mt-0.5" style="color:var(--muted)">{{ $pokokBahasan->deskripsi }}</p>
      @endif
      <div class="flex items-center gap-3 mt-2 text-[11px]" style="color:var(--muted)">
        <span><i class="fa-solid fa-book-open mr-1"></i>{{ $kelas->mataKuliah?->kode }} — {{ $kelas->mataKuliah?->nama }}</span>
        <span><i class="fa-solid fa-layer-group mr-1"></i>{{ $materi->count() }} materi</span>
      </div>
    </div>
    <a href="{{ route('mahasiswa.kelas.show', $kelas->id) }}"
       class="flex items-center gap-1.5 px-3 py-2 rounded-xl text-[12px] font-semibold border hover:opacity-80 transition-opacity flex-shrink-0"
       style="border-color:var(--border);color:var(--muted)">
      <i class="fa-solid fa-arrow-left text-[10px]"></i>Kembali
    </a>
  </div>

  {{-- Materi list --}}
  @if($materi->isEmpty())
    <div class="text-center py-16" style="color:var(--muted)">
      <i class="fa-solid fa-inbox text-[32px] opacity-30 block mb-3"></i>
      <div class="text-[14px] font-semibold" style="color:var(--text)">Belum ada materi</div>
      <p class="text-[12px] mt-1">Materi belum dipublikasikan oleh instruktur.</p>
    </div>
  @else
    <div class="space-y-5">
      @foreach($materi as $i => $m)
        @php
          $tipeClass = match($m->tipe) {
            'dokumen' => 'tc-dokumen', 'video' => 'tc-video',
            'link'    => 'tc-link',   'teks'  => 'tc-teks',
            default   => 'a-bg-lt a-text',
          };
          $tipeIcon = match($m->tipe) {
            'dokumen' => 'fa-file-lines', 'video' => 'fa-circle-play',
            'link'    => 'fa-link',       'teks'  => 'fa-align-left',
            default   => 'fa-file',
          };
          $ext   = $m->file_path ? strtolower(pathinfo($m->file_path, PATHINFO_EXTENSION)) : '';
          $isPdf = $ext === 'pdf';
          $savedProgress = $aksesMap[$m->id]->progress ?? 0;

          $videoId = null;
          if ($m->tipe === 'video' && $m->url) {
            preg_match('/(?:youtu\.be\/|youtube\.com\/(?:watch\?v=|embed\/|shorts\/))([A-Za-z0-9_-]{11})/', $m->url, $ytMatch);
            $videoId = $ytMatch[1] ?? null;
          }
        @endphp

        <div class="materi-card" id="materi-card-{{ $m->id }}"
             data-materi-id="{{ $m->id }}"
             data-type="{{ $m->tipe }}"
             data-saved-progress="{{ $savedProgress }}">

          {{-- Card header --}}
          <div class="materi-card-head">
            <div class="w-8 h-8 rounded-lg grid place-items-center text-[11px] font-bold flex-shrink-0 {{ $tipeClass }}">
              <i class="fa-solid {{ $tipeIcon }}"></i>
            </div>
            <div class="flex-1 min-w-0">
              <div class="flex items-center gap-2 flex-wrap">
                <span class="font-display font-semibold text-[14px]" style="color:var(--text)">{{ $m->judul }}</span>
                <span class="tipe-badge {{ $tipeClass }}">
                  <i class="fa-solid {{ $tipeIcon }}"></i>{{ $m->tipeLabel() }}
                </span>
                <span class="done-badge {{ $savedProgress >= 100 ? 'show' : '' }}" id="done-badge-{{ $m->id }}">
                  <i class="fa-solid fa-check text-[8px]"></i>Selesai
                </span>
              </div>
              @if($m->deskripsi)
                <p class="text-[11px] mt-0.5 truncate" style="color:var(--muted)">{{ $m->deskripsi }}</p>
              @endif
              <div class="flex items-center gap-2 mt-1.5">
                <div class="materi-progress-bar flex-1" style="max-width:180px">
                  <div class="materi-progress-fill" id="prog-bar-{{ $m->id }}" style="width:{{ $savedProgress }}%"></div>
                </div>
                <span class="progress-label" id="prog-label-{{ $m->id }}">{{ $savedProgress }}%</span>
              </div>
            </div>
            <span class="text-[11px] font-semibold flex-shrink-0 px-2 py-1 rounded-lg"
                  style="background:var(--surface2);color:var(--muted)">#{{ $i + 1 }}</span>
            <div class="readers-strip" id="readers-{{ $m->id }}"></div>
          </div>

          {{-- Card body --}}
          <div class="materi-card-body">

            {{-- ── TEKS ── --}}
            @if($m->tipe === 'teks')
              <div class="teks-content" id="teks-{{ $m->id }}" data-materi-id="{{ $m->id }}">{{ $m->konten }}</div>

            {{-- ── DOKUMEN ── --}}
            @elseif($m->tipe === 'dokumen')
              @if($m->file_path)
                @if($isPdf)
                  <div class="pdf-viewer-wrap" id="pdf-viewer-wrap-{{ $m->id }}" data-materi-id="{{ $m->id }}">

                    {{-- Toolbar: kiri = info file | kanan = zoom + fullscreen + aksi --}}
                    <div class="pdf-toolbar">
                      <div class="pdf-toolbar-left">
                        <i class="fa-solid fa-file-pdf text-red-400 flex-shrink-0"></i>
                        <span class="truncate" style="color:var(--text)">{{ $m->nama_file }}</span>
                        @if($m->ukuran_file)
                          <span class="flex-shrink-0" style="color:var(--muted)">• {{ $m->ukuranHuman() }}</span>
                        @endif
                        <span class="pdf-page-label flex-shrink-0" id="pdf-pages-{{ $m->id }}">Memuat…</span>
                      </div>

                      <div class="pdf-toolbar-right">
                        {{-- Zoom controls --}}
                        <button id="zoom-out-{{ $m->id }}" class="zoom-btn" disabled
                                onclick="zoomPdf({{ $m->id }}, -1)" title="Perkecil (−)">−</button>
                        <span class="zoom-label" id="pdf-zoom-{{ $m->id }}">100%</span>
                        <button id="zoom-in-{{ $m->id }}" class="zoom-btn"
                                onclick="zoomPdf({{ $m->id }}, +1)" title="Perbesar (+)">+</button>

                        <div class="toolbar-sep"></div>

                        {{-- Fullscreen --}}
                        <button id="fs-pdf-btn-{{ $m->id }}" class="toolbar-btn"
                                data-materi-id="{{ $m->id }}"
                                onclick="togglePdfFullscreen({{ $m->id }})"
                                title="Layar penuh">
                          <i class="fa-solid fa-expand"></i>
                          <span class="hidden sm:inline">Layar Penuh</span>
                        </button>

                        @if($m->allow_download)
                        {{-- Download --}}
                        <a href="{{ $m->fileUrl() }}" target="_blank" download
                           class="toolbar-btn" title="Download">
                          <i class="fa-solid fa-download"></i>
                        </a>
                        @endif
                      </div>
                    </div>

                    {{-- Canvas area --}}
                    <div class="pdf-canvases" id="pdf-canvases-{{ $m->id }}"
                         data-url="{{ $m->fileUrl() }}"
                         data-materi-id="{{ $m->id }}">
                      <div class="pdf-loading">
                        <i class="fa-solid fa-spinner fa-spin text-[24px]" style="color:var(--ac)"></i>
                        <span>Memuat PDF…</span>
                      </div>
                    </div>
                  </div>

                @else
                  {{-- Non-PDF file --}}
                  <div class="file-card">
                    <div class="w-10 h-10 rounded-xl grid place-items-center flex-shrink-0 tc-dokumen text-[15px]">
                      <i class="fa-solid fa-file-lines"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                      <div class="text-[13px] font-semibold truncate" style="color:var(--text)">{{ $m->nama_file }}</div>
                      @if($m->ukuran_file)
                        <div class="text-[11px] mt-0.5" style="color:var(--muted)">{{ $m->ukuranHuman() }}</div>
                      @endif
                    </div>
                    @if($m->allow_download)
                    <a href="{{ $m->fileUrl() }}" target="_blank" download
                       onclick="updateProgress({{ $m->id }}, 100)"
                       class="flex items-center gap-1.5 px-4 py-2 rounded-lg text-[12px] font-semibold text-white flex-shrink-0"
                       style="background:var(--ac)">
                      <i class="fa-solid fa-download"></i>Download
                    </a>
                    @endif
                  </div>
                @endif
              @else
                <div class="text-center py-6 text-[12px]" style="color:var(--muted)">
                  <i class="fa-solid fa-triangle-exclamation mb-2 block text-amber-400"></i>File belum tersedia.
                </div>
              @endif

            {{-- ── VIDEO ── --}}
            @elseif($m->tipe === 'video')
              @if($videoId)
                {{-- Video toolbar --}}
                <div class="video-toolbar">
                  <button id="fs-video-btn-{{ $m->id }}" class="toolbar-btn"
                          data-materi-id="{{ $m->id }}"
                          onclick="toggleVideoFullscreen({{ $m->id }})"
                          title="Layar penuh">
                    <i class="fa-solid fa-expand"></i>Layar Penuh
                  </button>
                  <a href="{{ $m->url }}" target="_blank" rel="noopener noreferrer"
                     class="toolbar-btn" title="Tonton di YouTube">
                    <i class="fa-brands fa-youtube" style="color:#ff0000"></i>YouTube
                  </a>
                </div>

                {{-- YouTube embed --}}
                <div class="video-wrap" id="video-wrap-{{ $m->id }}">
                  <div id="yt-player-{{ $m->id }}"
                       data-video-id="{{ $videoId }}"
                       data-materi-id="{{ $m->id }}"></div>
                </div>

              @elseif($m->url)
                <a href="{{ $m->url }}" target="_blank" rel="noopener noreferrer" class="link-card"
                   onclick="updateProgress({{ $m->id }}, 100)">
                  <div class="w-10 h-10 rounded-xl grid place-items-center flex-shrink-0 tc-video text-[15px]">
                    <i class="fa-solid fa-circle-play"></i>
                  </div>
                  <div class="flex-1 min-w-0">
                    <div class="text-[13px] font-semibold" style="color:var(--text)">Tonton Video</div>
                    <div class="text-[11px] mt-0.5 truncate" style="color:var(--muted)">{{ $m->url }}</div>
                  </div>
                  <i class="fa-solid fa-external-link text-[11px] flex-shrink-0" style="color:var(--muted)"></i>
                </a>
              @endif

            {{-- ── LINK ── --}}
            @elseif($m->tipe === 'link')
              @if($m->url)
                <a href="{{ $m->url }}" target="_blank" rel="noopener noreferrer" class="link-card"
                   data-link-materi="{{ $m->id }}">
                  <div class="w-10 h-10 rounded-xl grid place-items-center flex-shrink-0 tc-link text-[15px]">
                    <i class="fa-solid fa-link"></i>
                  </div>
                  <div class="flex-1 min-w-0">
                    <div class="text-[13px] font-semibold" style="color:var(--ac)">Buka Tautan</div>
                    <div class="text-[11px] mt-0.5 truncate" style="color:var(--muted)">{{ $m->url }}</div>
                  </div>
                  <i class="fa-solid fa-external-link text-[11px] flex-shrink-0" style="color:var(--muted)"></i>
                </a>
              @endif
            @endif

          </div>{{-- end card-body --}}

        </div>{{-- end materi-card --}}

      @endforeach
    </div>

    {{-- ── Rangkuman Pertemuan (satu untuk semua materi) ── --}}
    @if($pokokBahasan->rangkuman_aktif)
      <div class="rangkuman-section" id="rangkuman-section">
        <div class="rangkuman-head">
          <div class="w-8 h-8 rounded-xl grid place-items-center flex-shrink-0 text-[14px]"
               style="background:rgba(99,102,241,.12);color:#818cf8">
            <i class="fa-solid fa-pen-to-square"></i>
          </div>
          <div class="flex-1 min-w-0">
            <div class="text-[14px] font-semibold" style="color:var(--text)">Rangkuman Pertemuan Ini</div>
            <div class="text-[12px] mt-0.5" style="color:var(--muted)">
              Setelah membaca semua materi, tuliskan rangkumanmu berdasarkan pemahamanmu sendiri
            </div>
          </div>
          @if($pbRangkuman)
            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full flex-shrink-0"
                  style="background:rgba(16,185,129,.15);color:#10b981" id="rangkuman-badge">
              <i class="fa-solid fa-check mr-1"></i>Tersimpan
            </span>
          @else
            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full flex-shrink-0"
                  style="background:rgba(245,158,11,.15);color:#f59e0b" id="rangkuman-badge">
              Belum diisi
            </span>
          @endif
        </div>
        <div class="rangkuman-body">
          <div id="rangkuman-err" class="hidden text-[12px] mb-3 px-3 py-2 rounded-lg"
               style="background:rgba(239,68,68,.1);color:#fca5a5"></div>
          @if($pbRangkuman)
            <div id="rangkuman-view">
              <div class="rangkuman-saved" id="rangkuman-text">{{ $pbRangkuman->isi }}</div>
              <div class="rangkuman-meta" id="rangkuman-time">
                Disimpan {{ $pbRangkuman->updated_at->diffForHumans() }}
              </div>
              <button onclick="showRangkumanEdit()"
                      class="mt-3 text-[12px] font-semibold a-text hover:underline">
                <i class="fa-solid fa-pen text-[10px] mr-1"></i>Edit Rangkuman
              </button>
            </div>
            <div id="rangkuman-edit" class="hidden">
              <textarea class="rangkuman-textarea" id="rangkuman-inp"
                        placeholder="Tuliskan rangkumanmu di sini… (min. 10 karakter)">{{ $pbRangkuman->isi }}</textarea>
              <div class="flex items-center justify-center mt-3 gap-3">
                <button onclick="hideRangkumanEdit()" class="text-[12px] px-4 py-2 rounded-xl border"
                        style="color:var(--muted);border-color:var(--border)">Batal</button>
                <button onclick="saveRangkuman()" id="rangkuman-btn"
                        class="flex items-center gap-1.5 px-4 py-2 rounded-xl text-[12px] font-semibold text-white"
                        style="background:var(--ac)">
                  <i class="fa-solid fa-floppy-disk"></i>Simpan
                </button>
              </div>
            </div>
          @else
            <div id="rangkuman-view" class="hidden"></div>
            <div id="rangkuman-edit">
              <textarea class="rangkuman-textarea" id="rangkuman-inp"
                        placeholder="Tuliskan rangkumanmu di sini… (min. 10 karakter)"></textarea>
              <div class="flex items-center justify-center mt-3 gap-3">
                <button onclick="hideRangkumanEdit()" id="rangkuman-batal"
                        class="hidden text-[12px] px-4 py-2 rounded-xl border"
                        style="color:var(--muted);border-color:var(--border)">Batal</button>
                <button onclick="saveRangkuman()" id="rangkuman-btn"
                        class="flex items-center gap-1.5 px-4 py-2 rounded-xl text-[12px] font-semibold text-white"
                        style="background:var(--ac)">
                  <i class="fa-solid fa-floppy-disk"></i>Simpan Rangkuman
                </button>
              </div>
            </div>
          @endif
        </div>
      </div>
    @endif

  @endif

</div>

{{-- ══ FAB Speed Dial ═══════════════════════════════════════════════════════════ --}}
<div class="fab-backdrop" id="fab-backdrop" onclick="closeFab()"></div>
<div class="fab-root" id="fab-root">
  {{-- Sub-items: expand upward when opened --}}
  <div class="fab-items">
    <div class="fab-item" style="--d:.08s">
      <span class="fab-lbl">{{ $aiAssistantName }}</span>
      <button class="fab-sub fab-sub-ai" onclick="fabAi()" title="{{ $aiAssistantName }}">
        <i class="fa-solid fa-robot"></i>
      </button>
    </div>
    <div class="fab-item" style="--d:.04s">
      <span class="fab-lbl">Diskusi</span>
      <button class="fab-sub fab-sub-dsk" onclick="fabDiskusi()" title="Diskusi">
        <i class="fa-solid fa-comments"></i>
        <span class="fab-badge hidden" id="fab-badge">0</span>
      </button>
    </div>
  </div>
  {{-- Main trigger button --}}
  <button class="fab-main" id="fab-main" onclick="toggleFab()" title="Diskusi & AI">
    <i class="fa-solid fa-plus fab-main-icon" id="fab-main-icon"></i>
  </button>
</div>

{{-- ══ Drawers ══════════════════════════════════════════════════════════════════ --}}
<div class="drawer-overlay" id="drawer-overlay" onclick="closeDrawers()"></div>

{{-- Discussion Drawer --}}
<div class="drawer" id="diskusi-drawer">
  <div class="drawer-hd">
    <i class="fa-solid fa-comments flex-shrink-0" style="color:var(--ac)"></i>
    <h3 id="diskusi-drawer-title">Diskusi</h3>
    <button class="drawer-close" onclick="closeDrawers()"><i class="fa-solid fa-xmark"></i></button>
  </div>
  <div class="drawer-msgs" id="diskusi-msgs"></div>
  <div class="drawer-inp-wrap">
    <textarea id="diskusi-inp" class="drawer-inp" placeholder="Tulis pesan…" rows="2"
              onkeydown="if(event.key==='Enter'&&!event.shiftKey){event.preventDefault();sendDiskusi();}"></textarea>
    <button class="drawer-send" id="diskusi-send" onclick="sendDiskusi()">
      <i class="fa-solid fa-paper-plane"></i>
    </button>
  </div>
</div>

{{-- AI Chat Drawer --}}
<div class="drawer" id="ai-drawer">
  <div class="drawer-hd">
    <i class="fa-solid fa-robot flex-shrink-0" style="color:#818cf8"></i>
    <h3 id="ai-drawer-title">{{ $aiAssistantName }}</h3>
    <button class="drawer-close" onclick="closeDrawers()"><i class="fa-solid fa-xmark"></i></button>
  </div>
  <div class="drawer-msgs" id="ai-msgs">
    <div class="drawer-empty" id="ai-welcome">
      <div class="w-12 h-12 rounded-2xl grid place-items-center" style="background:rgba(99,102,241,.12)">
        <i class="fa-solid fa-robot text-[22px]" style="color:#818cf8"></i>
      </div>
      <p>Tanyakan apa saja tentang <strong>{{ $pokokBahasan->judul }}</strong>.<br>{{ $aiAssistantName }} siap membantu kamu memahami topik ini.</p>
    </div>
  </div>
  <div class="drawer-inp-wrap">
    <textarea id="ai-inp" class="drawer-inp" placeholder="Tanya tentang materi ini…" rows="2"
              onkeydown="if(event.key==='Enter'&&!event.shiftKey){event.preventDefault();sendAiChat();}"></textarea>
    <button class="drawer-send" id="ai-send" onclick="sendAiChat()">
      <i class="fa-solid fa-paper-plane"></i>
    </button>
  </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/pdfjs-dist@3.11.174/build/pdf.min.js"
        onerror="window._pdfJsLoadFailed=true"></script>
<script src="https://www.youtube.com/iframe_api" async></script>

<script>
// ── Config ───────────────────────────────────────────────────────────────────
const CSRF       = '{{ $csrfToken }}';
const KELAS_ID   = {{ $kelas->id }};
const PB_ID      = {{ $pokokBahasan->id }};
const KELAS_SLUG = `{{ $kelas->id }}/pokok-bahasan/{{ $pokokBahasan->id }}`;
const ROUTE_PROG      = (id) => `/mahasiswa/materi/${id}/progress`;
const ROUTE_READERS   = `/mahasiswa/kelas/${KELAS_SLUG}/readers`;
const ROUTE_DISKUSI   = `/diskusi/pb/${PB_ID}`;
const ROUTE_DEL_DSK   = (id) => `/diskusi/${id}`;
const ROUTE_AI         = `/mahasiswa/pokok-bahasan/${PB_ID}/ai-chat`;
const ROUTE_AI_HISTORY = `/mahasiswa/pokok-bahasan/${PB_ID}/ai-chat`;
const ROUTE_RANGKUMAN = `/mahasiswa/pokok-bahasan/${PB_ID}/rangkuman`;
const AUTH_USER_ID  = {{ auth()->id() }};
window._userIsInstruktur = {{ auth()->user()->roles->contains('name','instruktur') ? 'true' : 'false' }};
@php
$materiContexts = $materi->mapWithKeys(fn($m) => [
    $m->id => [
        'judul'     => $m->judul,
        'deskripsi' => $m->deskripsi ?? '',
        'tipe'      => $m->tipe,
        'konten'    => $m->tipe === 'teks' ? mb_substr(strip_tags($m->konten ?? ''), 0, 3000) : '',
    ]
])->toArray();
@endphp
const MATERI_CTX = @json($materiContexts);
const AI_NAME    = @json($aiAssistantName);

const savedProgress = {
  @foreach($materi as $m)
  {{ $m->id }}: {{ $aksesMap[$m->id]->progress ?? 0 }},
  @endforeach
};
const progCache = { ...savedProgress };
const lastSent  = {};

// ── Time trackers ─────────────────────────────────────────────────────────────
const timeTrackers = {};

function getTracker(id) {
  if (!timeTrackers[id]) timeTrackers[id] = { pending: 0, timer: null };
  return timeTrackers[id];
}
function startTimer(id) {
  const t = getTracker(id);
  if (t.timer) return;
  t.timer = setInterval(() => t.pending++, 1000);
}
function stopTimer(id) {
  const t = timeTrackers[id];
  if (!t?.timer) return;
  clearInterval(t.timer); t.timer = null;
}
function stopAllTimers() {
  Object.keys(timeTrackers).forEach(id => stopTimer(parseInt(id)));
}

document.addEventListener('visibilitychange', () => {
  if (document.hidden) {
    stopAllTimers();
  } else {
    document.querySelectorAll('[data-timer-active="1"]').forEach(el => {
      startTimer(parseInt(el.dataset.materiId));
    });
  }
});
window.addEventListener('pagehide', () => {
  stopAllTimers();
  Object.entries(timeTrackers).forEach(([id, t]) => {
    if (t.pending > 0) flushDurasi(parseInt(id), t.pending);
  });
});
setInterval(() => {
  Object.entries(timeTrackers).forEach(([id, t]) => {
    if (t.pending >= 5 && t.timer) {
      const dur = t.pending; t.pending = 0;
      flushDurasi(parseInt(id), dur);
    }
  });
}, 15000);

async function flushDurasi(materiId, durasi) {
  if (durasi <= 0) return;
  try {
    await fetch(ROUTE_PROG(materiId), {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
      body: JSON.stringify({ progress: progCache[materiId] ?? 0, kelas_id: KELAS_ID, durasi }),
    });
  } catch (e) {}
}

// ── Progress update (+ drain pending time) ───────────────────────────────────
async function updateProgress(materiId, progress) {
  progress = Math.min(100, Math.max(0, Math.round(progress)));
  const now     = Date.now();
  const isNew   = progress > (progCache[materiId] ?? 0);
  const throttle = (now - (lastSent[materiId] ?? 0)) < 3000;
  const t       = getTracker(materiId);
  const durasi  = t.pending; t.pending = 0;

  if (!isNew && throttle && durasi === 0) return;

  if (isNew) progCache[materiId] = progress;
  lastSent[materiId] = now;

  const bar   = document.getElementById(`prog-bar-${materiId}`);
  const label = document.getElementById(`prog-label-${materiId}`);
  const badge = document.getElementById(`done-badge-${materiId}`);
  if (bar)   bar.style.width   = progress + '%';
  if (label) label.textContent = progress + '%';
  if (badge) badge.classList.toggle('show', progress >= 100);

  try {
    await fetch(ROUTE_PROG(materiId), {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
      body: JSON.stringify({ progress, kelas_id: KELAS_ID, durasi }),
    });
  } catch (e) { console.warn('[progress]', e); }
}

// ── Initial access ping ───────────────────────────────────────────────────────
const accessObserver = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    if (!entry.isIntersecting) return;
    const id = parseInt(entry.target.dataset.materiId);
    if ((progCache[id] ?? 0) === 0) updateProgress(id, 5);
    accessObserver.unobserve(entry.target);
  });
}, { threshold: 0.2 });
document.querySelectorAll('.materi-card').forEach(el => accessObserver.observe(el));

// ── TEKS scroll tracking + timer ─────────────────────────────────────────────
function debounce(fn, ms) {
  let t; return (...a) => { clearTimeout(t); t = setTimeout(() => fn(...a), ms); };
}

const teksTimerObserver = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    const id = parseInt(entry.target.dataset.materiId);
    entry.target.dataset.timerActive = entry.isIntersecting ? '1' : '0';
    entry.isIntersecting ? startTimer(id) : stopTimer(id);
  });
}, { threshold: 0.1 });

document.querySelectorAll('.teks-content').forEach(el => {
  teksTimerObserver.observe(el);
  const id = parseInt(el.dataset.materiId);
  const calc = () => {
    const top = el.getBoundingClientRect().top + window.scrollY;
    const pct = Math.min(100, Math.round((window.scrollY + window.innerHeight - top) / el.offsetHeight * 100));
    if (pct > 0) updateProgress(id, pct);
  };
  window.addEventListener('scroll', debounce(calc, 400), { passive: true });
  setTimeout(calc, 500);
});

// ── LINK click ────────────────────────────────────────────────────────────────
document.querySelectorAll('[data-link-materi]').forEach(el => {
  el.addEventListener('click', () => updateProgress(parseInt(el.dataset.linkMateri), 100));
});

// ── PDF.js ────────────────────────────────────────────────────────────────────
// Zoom level presets: [scale, display label]
const PDF_LEVELS  = [[0.8,'50%'],[1.2,'75%'],[1.6,'100%'],[2.0,'125%'],[2.4,'150%'],[3.2,'200%']];
const PDF_DEFAULT = 2;  // index of 1.6
const pdfInstances = {};  // materiId → { pdf, levelIndex, maxPageSeen, pageObserver }

function pdfFallback(container, msg) {
  const url = container.dataset.url;
  container.innerHTML = `
    <div class="pdf-loading">
      <i class="fa-solid fa-triangle-exclamation text-amber-400 text-[28px]"></i>
      <span>${msg}</span>
      <a href="${url}" target="_blank"
         class="px-4 py-2 rounded-lg text-[12px] font-semibold text-white mt-1"
         style="background:var(--ac)">
        <i class="fa-solid fa-arrow-up-right-from-square mr-1.5"></i>Buka PDF di Browser
      </a>
    </div>`;
}

// Timer: observe the whole card when PDF is loaded
// MUST be declared before initPdf() is called below
const pdfWrapObserver = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    const id = parseInt(entry.target.dataset.materiId);
    entry.target.dataset.timerActive = entry.isIntersecting ? '1' : '0';
    entry.isIntersecting ? startTimer(id) : stopTimer(id);
  });
}, { threshold: 0.1 });

if (typeof pdfjsLib !== 'undefined') {
  pdfjsLib.GlobalWorkerOptions.workerSrc =
    'https://cdn.jsdelivr.net/npm/pdfjs-dist@3.11.174/build/pdf.worker.min.js';
  document.querySelectorAll('.pdf-canvases[data-url]').forEach(container => initPdf(container));
} else {
  // PDF.js CDN gagal dimuat (jaringan/blokir) — tampilkan fallback
  const msg = window._pdfJsLoadFailed
    ? 'Gagal memuat PDF viewer (koneksi bermasalah). Buka file langsung:'
    : 'PDF viewer tidak tersedia di browser ini. Buka file langsung:';
  document.querySelectorAll('.pdf-canvases[data-url]').forEach(container =>
    pdfFallback(container, msg)
  );
}

async function initPdf(container) {
  const url  = container.dataset.url;
  const id   = parseInt(container.dataset.materiId);
  const card = container.closest('.materi-card');
  if (card) pdfWrapObserver.observe(card);

  const statusSpan = container.querySelector('.pdf-loading span');

  try {
    const loadingTask = pdfjsLib.getDocument({ url, cMapPacked: true });
    loadingTask.onProgress = function(data) {
      if (!statusSpan) return;
      if (data.total > 0) {
        const pct = Math.round((data.loaded / data.total) * 100);
        statusSpan.textContent = `Mengunduh PDF… ${pct}%`;
      } else {
        const kb = Math.round(data.loaded / 1024);
        statusSpan.textContent = `Mengunduh PDF… ${kb} KB`;
      }
    };
    const pdf = await loadingTask.promise;
    pdfInstances[id] = { pdf, levelIndex: PDF_DEFAULT, maxPageSeen: 0, pageObserver: null };
    await renderPdf(id);
  } catch (err) {
    pdfFallback(container, `Gagal memuat PDF: ${err.message}`);
  }
}

async function renderPdf(materiId) {
  const inst      = pdfInstances[materiId];
  if (!inst) return;

  const container  = document.getElementById(`pdf-canvases-${materiId}`);
  const pageLabel  = document.getElementById(`pdf-pages-${materiId}`);
  const zoomLabel  = document.getElementById(`pdf-zoom-${materiId}`);
  const btnOut     = document.getElementById(`zoom-out-${materiId}`);
  const btnIn      = document.getElementById(`zoom-in-${materiId}`);
  if (!container) return;

  // Save scroll ratio to restore after re-render
  const scrollRatio = container.scrollHeight > 0 ? container.scrollTop / container.scrollHeight : 0;

  // Disconnect previous page observer
  inst.pageObserver?.disconnect();

  const [scale, label] = PDF_LEVELS[inst.levelIndex];
  if (zoomLabel) zoomLabel.textContent = label;
  if (btnOut) btnOut.disabled = inst.levelIndex === 0;
  if (btnIn)  btnIn.disabled  = inst.levelIndex === PDF_LEVELS.length - 1;

  // Show loading spinner while first page renders
  container.innerHTML = `
    <div class="pdf-loading">
      <i class="fa-solid fa-spinner fa-spin text-[20px]" style="color:var(--ac)"></i>
      <span>Memuat ${label}…</span>
    </div>`;

  // Page visibility observer for progress (set up before appending canvases)
  inst.pageObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (!entry.isIntersecting) return;
      const pg = parseInt(entry.target.dataset.page);
      if (pg > inst.maxPageSeen) {
        inst.maxPageSeen = pg;
        if (pageLabel) pageLabel.textContent = `${pg} / ${inst.pdf.numPages} halaman`;
        updateProgress(materiId, Math.round((pg / inst.pdf.numPages) * 100));
      }
    });
  }, { root: container, threshold: 0.3 });

  let firstPage = true;
  for (let p = 1; p <= inst.pdf.numPages; p++) {
    const page     = await inst.pdf.getPage(p);
    const viewport = page.getViewport({ scale });
    const canvas   = document.createElement('canvas');
    canvas.width   = viewport.width;
    canvas.height  = viewport.height;
    canvas.dataset.page = p;
    await page.render({ canvasContext: canvas.getContext('2d'), viewport }).promise;

    if (firstPage) {
      // Replace loading spinner with first page immediately
      container.innerHTML = '';
      firstPage = false;
      if (pageLabel) pageLabel.textContent = `1 / ${inst.pdf.numPages} halaman`;
      // Restore scroll position on re-render
      requestAnimationFrame(() => {
        container.scrollTop = scrollRatio * container.scrollHeight;
      });
    }

    container.appendChild(canvas);
    inst.pageObserver.observe(canvas);
  }
}

function zoomPdf(materiId, dir) {
  const inst = pdfInstances[materiId];
  if (!inst) return;
  const newIdx = Math.max(0, Math.min(PDF_LEVELS.length - 1, inst.levelIndex + dir));
  if (newIdx === inst.levelIndex) return;
  inst.levelIndex = newIdx;
  renderPdf(materiId);
}

function togglePdfFullscreen(materiId) {
  const wrap = document.getElementById(`pdf-viewer-wrap-${materiId}`);
  if (!wrap) return;
  if (!document.fullscreenElement) {
    wrap.requestFullscreen().catch(e => console.warn('Fullscreen:', e));
  } else {
    document.exitFullscreen();
  }
}

// ── Video fullscreen ──────────────────────────────────────────────────────────
function toggleVideoFullscreen(materiId) {
  const wrap = document.getElementById(`video-wrap-${materiId}`);
  if (!wrap) return;
  if (!document.fullscreenElement) {
    wrap.requestFullscreen().catch(e => console.warn('Fullscreen:', e));
  } else {
    document.exitFullscreen();
  }
}

// Update fullscreen button icons on change
document.addEventListener('fullscreenchange', () => {
  const fsEl = document.fullscreenElement;

  document.querySelectorAll('[id^="fs-pdf-btn-"]').forEach(btn => {
    const id   = btn.dataset.materiId;
    const isFs = fsEl?.id === `pdf-viewer-wrap-${id}`;
    btn.querySelector('i').className = isFs ? 'fa-solid fa-compress' : 'fa-solid fa-expand';
    const txt = btn.querySelector('span');
    if (txt) txt.textContent = isFs ? 'Keluar' : 'Layar Penuh';
    btn.title = isFs ? 'Keluar layar penuh (Esc)' : 'Layar penuh';
  });

  document.querySelectorAll('[id^="fs-video-btn-"]').forEach(btn => {
    const id   = btn.dataset.materiId;
    const isFs = fsEl?.id === `video-wrap-${id}`;
    btn.querySelector('i').className = isFs ? 'fa-solid fa-compress' : 'fa-solid fa-expand';
    const txt  = btn.querySelector('span') ?? btn.childNodes[btn.childNodes.length - 1];
    if (txt?.nodeType === 3) txt.textContent = isFs ? ' Keluar' : ' Layar Penuh';
    btn.title = isFs ? 'Keluar layar penuh (Esc)' : 'Layar penuh';
  });
});

// ── YouTube: progress + watch time ───────────────────────────────────────────
const ytPlayers = {};

window.onYouTubeIframeAPIReady = function () {
  document.querySelectorAll('[id^="yt-player-"]').forEach(el => {
    const id      = parseInt(el.dataset.materiId);
    const videoId = el.dataset.videoId;
    ytPlayers[id] = new YT.Player(el, {
      videoId,
      playerVars: { rel: 0, modestbranding: 1 },
      events: { onStateChange: (e) => onYtStateChange(e, id) },
    });
  });
};

function onYtStateChange(event, materiId) {
  if (event.data === YT.PlayerState.PLAYING) {
    startTimer(materiId);
    const iv = setInterval(() => {
      const p = ytPlayers[materiId];
      if (!p?.getDuration) return;
      const dur = p.getDuration(), cur = p.getCurrentTime();
      if (dur > 0) {
        const pct = Math.round((cur / dur) * 100);
        updateProgress(materiId, pct);
        if (pct >= 95) { clearInterval(iv); updateProgress(materiId, 100); }
      }
    }, 4000);
    ytPlayers[`_iv_${materiId}`] = iv;
  } else {
    stopTimer(materiId);
    clearInterval(ytPlayers[`_iv_${materiId}`]);
    if (event.data === YT.PlayerState.ENDED) updateProgress(materiId, 100);
  }
}

// ══ FAB Speed Dial ════════════════════════════════════════════════════════════
let fabMateri = null; // { id, judul } — materi card currently in view
let fabIsOpen = false;

// Track which card is most visible
(function initFab() {
  const cards = [...document.querySelectorAll('.materi-card[data-materi-id]')];
  if (!cards.length) return;

  // Default to first card
  const first = cards[0];
  fabMateri = {
    id:    parseInt(first.dataset.materiId),
    judul: first.querySelector('.font-display.font-semibold')?.textContent?.trim() || '',
  };

  const io = new IntersectionObserver(entries => {
    let best = null;
    entries.forEach(e => {
      if (!best || e.intersectionRatio > best.ratio)
        best = { el: e.target, ratio: e.intersectionRatio };
    });
    if (best && best.ratio > 0.15) {
      fabMateri = {
        id:    parseInt(best.el.dataset.materiId),
        judul: best.el.querySelector('.font-display.font-semibold')?.textContent?.trim() || '',
      };
    }
  }, { threshold: [0.15, 0.5, 0.85] });

  cards.forEach(c => io.observe(c));
})();

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

function fabDiskusi() {
  closeFab();
  openDiskusi();
}

function fabAi() {
  closeFab();
  openAiChat();
}

// Close FAB when drawer opens (drawer has its own overlay)
const _origShowDrawer = showDrawer;
showDrawer = function(id) { closeFab(); _origShowDrawer(id); };

// ══ ACTIVE READERS ════════════════════════════════════════════════════════════
async function fetchReaders() {
  try {
    const r = await fetch(ROUTE_READERS, { headers: { 'Accept': 'application/json' } });
    if (!r.ok) return;
    const data = await r.json(); // { materiId: [{ user_id, name, initials, color, is_self }] }
    document.querySelectorAll('[id^="readers-"]').forEach(el => {
      const mid = el.id.replace('readers-', '');
      renderReaders(el, data[mid] || []);
    });
  } catch { /* silent */ }
}

function renderReaders(el, readers) {
  if (!readers.length) { el.innerHTML = ''; return; }
  // Sort: others first, self last
  const sorted  = [...readers].sort((a, b) => a.is_self - b.is_self);
  const visible = sorted.slice(0, 4);
  const extra   = sorted.length - visible.length;
  const others  = sorted.filter(r => !r.is_self);
  const label   = others.length
    ? others.slice(0, 2).map(r => r.name.split(' ')[0]).join(', ') + (others.length > 2 ? ' +lainnya' : '') + ' sedang membaca'
    : 'Hanya kamu yang membaca';

  el.innerHTML = visible.map(r =>
    `<div class="r-avatar" style="background:${r.color}" title="${escH(r.name)}${r.is_self?' (Kamu)':''}">${r.initials}</div>`
  ).join('') +
  (extra > 0 ? `<span class="r-more">+${extra}</span>` : '') +
  `<span class="r-more" style="margin-left:6px">${label}</span>`;
}

fetchReaders();
setInterval(fetchReaders, 30_000);

// ══ DRAWERS ═══════════════════════════════════════════════════════════════════
let activeDrawer = null;

function openDiskusi() {
  document.getElementById('diskusi-drawer-title').textContent = `Diskusi — {{ $pokokBahasan->judul }}`;
  document.getElementById('diskusi-msgs').innerHTML =
    `<div class="drawer-empty"><i class="fa-solid fa-spinner fa-spin"></i><span>Memuat…</span></div>`;
  showDrawer('diskusi-drawer');
  loadDiskusi();
}

async function openAiChat() {
  document.getElementById('ai-drawer-title').textContent = `${AI_NAME} — {{ $pokokBahasan->judul }}`;
  showDrawer('ai-drawer');
  document.getElementById('ai-inp').focus();

  // Load history from server if not yet loaded
  if (!aiHistoryLoaded) {
    aiHistoryLoaded = true;
    const msgsEl = document.getElementById('ai-msgs');
    msgsEl.innerHTML = `<div class="drawer-empty"><i class="fa-solid fa-spinner fa-spin"></i><span>Memuat riwayat…</span></div>`;
    try {
      const r = await fetch(ROUTE_AI_HISTORY, { headers: { 'Accept': 'application/json' } });
      if (r.ok) {
        const j = await r.json();
        aiMessages = j.messages || [];
        renderAiMessages();
      } else {
        renderAiMessages();
      }
    } catch {
      renderAiMessages();
    }
  }
}

function showDrawer(id) {
  closeDrawers(false);
  document.getElementById(id).classList.add('open');
  document.getElementById('drawer-overlay').classList.add('open');
  activeDrawer = id;
  if (id === 'diskusi-drawer') dskStartFast();
}

function closeDrawers(resetActive = true) {
  document.querySelectorAll('.drawer').forEach(d => d.classList.remove('open'));
  document.getElementById('drawer-overlay').classList.remove('open');
  if (resetActive) { activeDrawer = null; dskStopFast(); }
}

document.addEventListener('keydown', e => { if (e.key === 'Escape') closeDrawers(); });

// ══ DISKUSI ════════════════════════════════════════════════════════════════════
// Beri tahu global poll bahwa halaman ini punya local poll sendiri
window._hasDskLocalPoll = true;

let _dskLastId    = 0;
let _dskFastTimer = null;
let _dskSlowTimer = null;

// ── Browser notifications ─────────────────────────────────────
(function initNotifPermission() {
  if (!('Notification' in window) || Notification.permission !== 'default') return;
  // Request on first user interaction so browser allows the prompt
  const ask = () => { Notification.requestPermission(); document.removeEventListener('click', ask); };
  document.addEventListener('click', ask, { once: true });
})();

function dskShowNotif(messages, onClickFn) {
  if (!('Notification' in window) || Notification.permission !== 'granted') return;
  const others = messages.filter(d => !d.is_own);
  if (!others.length) return;
  const last  = others[others.length - 1];
  const extra = others.length > 1 ? ` (+${others.length - 1} lainnya)` : '';
  const body  = `${last.name}: ${last.pesan.substring(0, 80)}${last.pesan.length > 80 ? '…' : ''}${extra}`;
  const n = new Notification('💬 Diskusi — {{ $pokokBahasan->judul }}', {
    body,
    icon: '{{ asset("favicon.ico") }}',
    tag: 'lms-diskusi-{{ $pokokBahasan->id }}',
    renotify: true,
  });
  n.onclick = () => { window.focus(); n.close(); onClickFn?.(); };
  setTimeout(() => n.close(), 12000);
}

function dskPlaySound() {
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
  } catch { /* browser may block without user gesture */ }
}

function dskStartFast() {
  dskStopFast();
  _dskFastTimer = setInterval(dskPollFast, 4000);
}
function dskStopFast() {
  if (_dskFastTimer) { clearInterval(_dskFastTimer); _dskFastTimer = null; }
}
function dskStartSlow() {
  if (_dskSlowTimer) return;
  _dskSlowTimer = setInterval(dskPollSlow, 30000);
}

async function dskPollFast() {
  // Only run when diskusi drawer is open
  if (activeDrawer !== 'diskusi-drawer') return;
  try {
    const r = await fetch(`${ROUTE_DISKUSI}?kelas_id=${KELAS_ID}&after_id=${_dskLastId}`, {
      headers: { 'Accept': 'application/json' }
    });
    if (!r.ok) return;
    const j = await r.json();
    updateDiskusiCount(j.others_count ?? diskusiOthersCount);
    if (!j.diskusi?.length) return;

    const el = document.getElementById('diskusi-msgs');
    if (!el) return;
    el.querySelector('.drawer-empty')?.remove();
    let hasNew = false;
    j.diskusi.forEach(d => {
      if (d.id > _dskLastId) _dskLastId = d.id;
      if (!document.getElementById(`dsk-row-${d.id}`)) {
        el.insertAdjacentHTML('beforeend', diskusiRow(d));
        if (!d.is_own) hasNew = true;
      }
    });
    window.gdskSyncLastId?.(_dskLastId);
    el.scrollTop = el.scrollHeight;
    if (hasNew) {
      markDiskusiSeen();
      dskPlaySound();
      dskShowNotif(j.diskusi, null);
      injectDskNavNotif(j.diskusi, null);
    }
  } catch { /* silent */ }
}

async function dskPollSlow() {
  // Background badge refresh when drawer is closed
  if (activeDrawer === 'diskusi-drawer') return;
  try {
    const prev = diskusiOthersCount;
    const r = await fetch(`${ROUTE_DISKUSI}?kelas_id=${KELAS_ID}&after_id=${_dskLastId}`, {
      headers: { 'Accept': 'application/json' }
    });
    if (!r.ok) return;
    const j = await r.json();
    updateDiskusiCount(j.others_count ?? diskusiOthersCount);
    if (j.diskusi?.length) j.diskusi.forEach(d => { if (d.id > _dskLastId) _dskLastId = d.id; });
    window.gdskSyncLastId?.(_dskLastId);
    if (diskusiOthersCount > prev) {
      dskPlaySound();
      dskShowNotif(j.diskusi, () => openDiskusi());
      injectDskNavNotif(j.diskusi, () => openDiskusi());
    }
  } catch { /* silent */ }
}

async function loadDiskusi() {
  try {
    const r = await fetch(`${ROUTE_DISKUSI}?kelas_id=${KELAS_ID}`, {
      headers: { 'Accept': 'application/json' }
    });
    const j = await r.json();
    if (!r.ok) { showDiskusiError(j.message || 'Gagal memuat'); return; }
    renderDiskusiList(j.diskusi);
    updateDiskusiCount(j.others_count ?? 0);
    markDiskusiSeen();
    // Track last message ID for incremental polling
    if (j.diskusi?.length) { _dskLastId = j.diskusi.reduce((m, d) => Math.max(m, d.id), _dskLastId); window.gdskSyncLastId?.(_dskLastId); }
  } catch { showDiskusiError('Terjadi kesalahan.'); }
}

function renderDiskusiList(list) {
  const el = document.getElementById('diskusi-msgs');
  if (!list.length) {
    el.innerHTML = `<div class="drawer-empty">
      <i class="fa-solid fa-comments text-[28px] opacity-30"></i>
      <span>Belum ada diskusi. Jadilah yang pertama!</span>
    </div>`;
    return;
  }
  el.innerHTML = list.map(d => diskusiRow(d)).join('');
  el.scrollTop = el.scrollHeight;
}

function diskusiRow(d) {
  const ownClass = d.is_own ? 'own' : '';
  const instrTag = d.is_instruktur
    ? `<span class="instruktur-tag"><i class="fa-solid fa-chalkboard-user text-[8px] mr-0.5"></i>Instruktur</span>`
    : '';
  const canDel = d.is_own || window._userIsInstruktur;
  const del = canDel
    ? `<button class="chat-del" onclick="deleteDiskusi(${d.id})" title="Hapus"><i class="fa-solid fa-trash"></i></button>`
    : '';
  return `<div class="chat-row ${ownClass}" id="dsk-row-${d.id}">
    <div class="c-avatar" style="background:${d.color}">${d.initials}</div>
    <div class="c-body">
      <div class="c-meta">${escH(d.name)}${instrTag}</div>
      <div class="c-bubble">${escH(d.pesan)}</div>
      <div class="c-meta">${d.waktu} ${del}</div>
    </div>
  </div>`;
}

function showDiskusiError(msg) {
  document.getElementById('diskusi-msgs').innerHTML =
    `<div class="drawer-empty"><i class="fa-solid fa-triangle-exclamation text-amber-400"></i><span>${escH(msg)}</span></div>`;
}

// diskusiOthersCount tracks messages from OTHER users only (not self) — for unread badge
let diskusiOthersCount = 0;
const SEEN_KEY = `lms_dsk_seen_pb_${PB_ID}_${KELAS_ID}`;

function getSeenCount() {
  return parseInt(localStorage.getItem(SEEN_KEY) || '0');
}

function markDiskusiSeen() {
  localStorage.setItem(SEEN_KEY, String(diskusiOthersCount));
  refreshFabBadge();
}

function refreshFabBadge() {
  const unread = Math.max(0, diskusiOthersCount - getSeenCount());
  const badge = document.getElementById('fab-badge');
  if (!badge) return;
  if (unread > 0) { badge.textContent = unread > 99 ? '99+' : unread; badge.classList.remove('hidden'); }
  else badge.classList.add('hidden');
}

// n = others_count (messages from other users, not self)
function updateDiskusiCount(n) {
  diskusiOthersCount = n;
  refreshFabBadge();
}

async function sendDiskusi() {
  const inp = document.getElementById('diskusi-inp');
  const pesan = inp.value.trim();
  if (!pesan) return;

  const btn = document.getElementById('diskusi-send');
  btn.disabled = true;
  try {
    const fd = new FormData();
    fd.append('kelas_id', KELAS_ID);
    fd.append('pesan', pesan);
    fd.append('_token', CSRF);
    const r = await fetch(ROUTE_DISKUSI, {
      method: 'POST',
      headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
      body: fd,
    });
    const j = await r.json();
    if (!r.ok) { alert(j.message || 'Gagal'); return; }
    inp.value = '';
    // Append new row to list (remove empty state first)
    const el = document.getElementById('diskusi-msgs');
    const empty = el.querySelector('.drawer-empty');
    if (empty) empty.remove();
    el.insertAdjacentHTML('beforeend', diskusiRow(j.diskusi));
    el.scrollTop = el.scrollHeight;
    // Mark as seen (user just sent, so they've seen all)
    markDiskusiSeen();
  } catch { alert('Terjadi kesalahan.'); }
  finally { btn.disabled = false; }
}

async function deleteDiskusi(id) {
  if (!confirm('Hapus pesan ini?')) return;
  try {
    const r = await fetch(ROUTE_DEL_DSK(id), {
      method: 'DELETE',
      headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
    });
    if (r.ok) {
      const row = document.getElementById(`dsk-row-${id}`);
      const wasOthers = row && !row.classList.contains('own');
      row?.remove();
      const el = document.getElementById('diskusi-msgs');
      if (!el.querySelector('.chat-row')) {
        el.innerHTML = `<div class="drawer-empty">
          <i class="fa-solid fa-comments text-[28px] opacity-30"></i>
          <span>Belum ada diskusi. Jadilah yang pertama!</span>
        </div>`;
      }
      // Only decrement others_count if the deleted message was from someone else
      if (wasOthers) {
        updateDiskusiCount(Math.max(0, diskusiOthersCount - 1));
        markDiskusiSeen();
      }
    }
  } catch { /* silent */ }
}

// Initial badge fetch + start background slow poll
(async () => {
  try {
    const r = await fetch(`${ROUTE_DISKUSI}?kelas_id=${KELAS_ID}`, {
      headers: { 'Accept': 'application/json' }
    });
    if (r.ok) {
      const j = await r.json();
      updateDiskusiCount(j.others_count ?? 0);
      if (j.diskusi?.length) { _dskLastId = j.diskusi.reduce((m, d) => Math.max(m, d.id), 0); window.gdskSyncLastId?.(_dskLastId); }
    }
  } catch { /* silent */ }
  dskStartSlow();
})();

// ══ AI CHAT ════════════════════════════════════════════════════════════════════
let aiMessages = [];      // [{ role, content }] — synced with DB
let aiHistoryLoaded = false;

function renderAiMessages() {
  const el = document.getElementById('ai-msgs');
  if (!aiMessages.length) {
    el.innerHTML = `<div class="drawer-empty" id="ai-welcome">
      <div class="w-12 h-12 rounded-2xl grid place-items-center" style="background:rgba(99,102,241,.12)">
        <i class="fa-solid fa-robot text-[22px]" style="color:#818cf8"></i>
      </div>
      <p>Tanyakan apa saja tentang <strong>{{ $pokokBahasan->judul }}</strong>.<br>${AI_NAME} siap membantu kamu memahami topik ini.</p>
    </div>`;
    return;
  }
  el.innerHTML = aiMessages.map(msg => aiRow(msg)).join('');
  el.scrollTop = el.scrollHeight;
}

function aiRow(msg) {
  if (msg.role === 'user') {
    return `<div class="chat-row own">
      <div class="c-avatar" style="background:var(--ac)">Sy</div>
      <div class="c-body"><div class="c-bubble">${escH(msg.content)}</div></div>
    </div>`;
  }
  return `<div class="chat-row ai">
    <div class="c-avatar" style="background:#4f46e5"><i class="fa-solid fa-robot text-[10px]"></i></div>
    <div>
      <div class="c-bubble">${formatAiText(msg.content)}</div>
    </div>
  </div>`;
}

function formatAiText(text) {
  // Basic markdown: **bold**, `code`, newlines
  return escH(text)
    .replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>')
    .replace(/`(.+?)`/g, '<code style="background:rgba(99,102,241,.15);padding:1px 5px;border-radius:4px;font-size:11px">$1</code>')
    .replace(/\n/g, '<br>');
}

async function sendAiChat() {
  const inp = document.getElementById('ai-inp');
  const content = inp.value.trim();
  if (!content) return;

  const btn = document.getElementById('ai-send');
  btn.disabled = true;
  inp.value = '';

  aiMessages.push({ role: 'user', content });

  const msgsEl = document.getElementById('ai-msgs');
  msgsEl.querySelector('#ai-welcome')?.remove();
  msgsEl.insertAdjacentHTML('beforeend', aiRow({ role: 'user', content }));

  // Typing indicator
  const typingId = 'ai-typing-' + Date.now();
  msgsEl.insertAdjacentHTML('beforeend', `
    <div class="chat-row ai" id="${typingId}">
      <div class="c-avatar" style="background:#4f46e5"><i class="fa-solid fa-robot text-[10px]"></i></div>
      <div class="c-bubble"><span class="typing-dots"><span></span><span></span><span></span></span></div>
    </div>`);
  msgsEl.scrollTop = msgsEl.scrollHeight;

  try {
    const fd = new FormData();
    fd.append('kelas_id', KELAS_ID);
    fd.append('message', content);

    const r = await fetch(ROUTE_AI, {
      method: 'POST',
      headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
      body: fd,
    });
    const j = await r.json();

    document.getElementById(typingId)?.remove();

    if (!r.ok) {
      const errMsg = j.error || 'Gagal mendapatkan respons AI.';
      msgsEl.insertAdjacentHTML('beforeend', `
        <div class="chat-row ai">
          <div class="c-avatar" style="background:#ef4444"><i class="fa-solid fa-exclamation text-[10px]"></i></div>
          <div class="c-bubble" style="border-color:rgba(239,68,68,.3);color:#fca5a5">${escH(errMsg)}</div>
        </div>`);
      aiMessages.pop(); // roll back optimistic user message
    } else {
      aiMessages.push({ role: 'assistant', content: j.reply });
      msgsEl.insertAdjacentHTML('beforeend', aiRow({ role: 'assistant', content: j.reply }));
    }
  } catch {
    document.getElementById(typingId)?.remove();
    msgsEl.insertAdjacentHTML('beforeend', `
      <div class="chat-row ai">
        <div class="c-avatar" style="background:#ef4444"><i class="fa-solid fa-exclamation text-[10px]"></i></div>
        <div class="c-bubble" style="color:#fca5a5">Koneksi gagal. Coba lagi.</div>
      </div>`);
    aiMessages.pop();
  } finally {
    btn.disabled = false;
    msgsEl.scrollTop = msgsEl.scrollHeight;
    document.getElementById('ai-inp').focus();
  }
}

function escH(s) {
  return String(s || '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}

// ══ RANGKUMAN ══════════════════════════════════════════════════════════════════
function showRangkumanEdit() {
  _hideRangkumanErr();
  document.getElementById('rangkuman-view')?.classList.add('hidden');
  document.getElementById('rangkuman-edit')?.classList.remove('hidden');
  document.getElementById('rangkuman-inp')?.focus();
}
function hideRangkumanEdit() {
  document.getElementById('rangkuman-edit')?.classList.add('hidden');
  document.getElementById('rangkuman-view')?.classList.remove('hidden');
}

function _showRangkumanErr(msg) {
  const el = document.getElementById('rangkuman-err');
  if (!el) return;
  el.textContent = msg;
  el.classList.remove('hidden');
}
function _hideRangkumanErr() {
  document.getElementById('rangkuman-err')?.classList.add('hidden');
}

async function saveRangkuman() {
  const inp = document.getElementById('rangkuman-inp');
  const isi = inp?.value.trim();
  if (!isi || isi.length < 10) {
    _showRangkumanErr('Rangkuman minimal 10 karakter.');
    inp?.focus();
    return;
  }
  _hideRangkumanErr();

  const btn = document.getElementById('rangkuman-btn');
  if (btn) { btn.disabled = true; btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1"></i>Menyimpan…'; }

  try {
    const r = await fetch(ROUTE_RANGKUMAN, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
      body: JSON.stringify({ kelas_id: KELAS_ID, isi }),
    });
    const j = await r.json();
    if (!r.ok) throw new Error(j.message || 'Gagal menyimpan');

    const viewEl = document.getElementById('rangkuman-view');
    const textEl = document.getElementById('rangkuman-text');
    const timeEl = document.getElementById('rangkuman-time');
    const badge  = document.getElementById('rangkuman-badge');

    if (inp) inp.value = j.isi;

    if (textEl) {
      textEl.textContent = j.isi;
    } else if (viewEl) {
      viewEl.innerHTML = `
        <div class="rangkuman-saved" id="rangkuman-text">${escH(j.isi)}</div>
        <div class="rangkuman-meta" id="rangkuman-time">Disimpan ${escH(j.updated_at)}</div>
        <button onclick="showRangkumanEdit()" class="mt-3 text-[12px] font-semibold a-text hover:underline">
          <i class="fa-solid fa-pen text-[10px] mr-1"></i>Edit Rangkuman
        </button>`;
      // Unhide the Batal button now that there is a saved rangkuman to revert to
      document.getElementById('rangkuman-batal')?.classList.remove('hidden');
    }
    if (timeEl) timeEl.textContent = 'Disimpan ' + j.updated_at;
    if (badge)  { badge.innerHTML = '<i class="fa-solid fa-check mr-1"></i>Tersimpan'; badge.style.background = 'rgba(16,185,129,.15)'; badge.style.color = '#10b981'; }

    viewEl?.classList.remove('hidden');
    hideRangkumanEdit();
  } catch(e) {
    _showRangkumanErr(e.message || 'Gagal menyimpan, coba lagi.');
  } finally {
    if (btn) { btn.disabled = false; btn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i>Simpan'; }
  }
}
</script>
@endpush
