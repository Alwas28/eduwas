@extends('layouts.instruktur')
@section('title', 'Preview — Pertemuan ' . $pokokBahasan->pertemuan . ' ' . $pokokBahasan->judul)
@section('page-title', 'Preview Materi')

@push('styles')
<style>
/* ── Page wrapper ── */
.preview-wrap { max-width:860px; margin:0 auto; }

/* ── PB header card ── */
.pb-header-card {
  background:var(--surface); border:1px solid var(--border); border-radius:18px;
  padding:20px 24px; display:flex; align-items:flex-start; gap:16px;
}

/* ── Materi card ── */
.materi-card {
  background:var(--surface); border:1px solid var(--border); border-radius:18px;
  overflow:hidden;
}
.materi-card.is-draft { opacity:.65; }
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

/* ── Teks content ── */
.teks-content {
  font-size:14px; line-height:1.75; color:var(--text);
  white-space:pre-wrap; word-break:break-word;
}

/* ── PDF embed ── */
.pdf-embed {
  width:100%; border:none; border-radius:10px;
  background:var(--surface2); display:block;
  min-height:600px;
}
.pdf-fallback {
  display:flex; flex-direction:column; align-items:center; justify-content:center;
  padding:40px; gap:10px; background:var(--surface2); border-radius:10px;
  color:var(--muted); font-size:13px;
}

/* ── File download card ── */
.file-card {
  display:flex; align-items:center; gap:12px;
  padding:14px 16px; border-radius:12px;
  border:1px solid var(--border); background:var(--surface2);
}

/* ── Link card ── */
.link-card {
  display:flex; align-items:center; gap:12px;
  padding:14px 16px; border-radius:12px;
  border:1px solid var(--border); background:var(--surface2);
  text-decoration:none; transition:border-color .15s;
}
.link-card:hover { border-color:rgba(var(--ac-rgb),.5); }

/* ── Video embed ── */
.video-wrap {
  position:relative; width:100%; padding-top:56.25%; /* 16:9 */
  border-radius:12px; overflow:hidden; background:#000;
}
.video-wrap iframe { position:absolute; inset:0; width:100%; height:100%; border:none; }

/* ── Draft badge ── */
.draft-notice {
  display:inline-flex; align-items:center; gap:5px;
  padding:2px 9px; border-radius:20px; font-size:10px; font-weight:700;
  background:rgba(245,158,11,.15); color:#fbbf24;
}

/* ── Divider ── */
.pertemuan-divider { text-align:center; font-size:11px; color:var(--muted); padding:8px 0; }
</style>
@endpush

@section('content')
<div class="space-y-5 animate-fadeUp preview-wrap">

  {{-- Breadcrumb --}}
  <div class="flex items-center gap-2 text-[12px]" style="color:var(--muted)">
    <a href="{{ route('instruktur.materi.index', ['mk_id' => $mataKuliah->id]) }}" class="a-text hover:underline">Materi Ajar</a>
    <i class="fa-solid fa-chevron-right text-[10px]"></i>
    <a href="{{ route('instruktur.pokok-bahasan.materi', $pokokBahasan->id) }}" class="a-text hover:underline">{{ $mataKuliah->kode }}</a>
    <i class="fa-solid fa-chevron-right text-[10px]"></i>
    <a href="{{ route('instruktur.pokok-bahasan.materi', $pokokBahasan->id) }}" class="a-text hover:underline">Pertemuan {{ $pokokBahasan->pertemuan }}</a>
    <i class="fa-solid fa-chevron-right text-[10px]"></i>
    <span style="color:var(--text)">Preview</span>
  </div>

  {{-- PB header --}}
  <div class="pb-header-card">
    <div class="w-12 h-12 rounded-xl grid place-items-center font-display font-bold text-[18px] a-bg-lt a-text flex-shrink-0">
      {{ $pokokBahasan->pertemuan }}
    </div>
    <div class="flex-1 min-w-0">
      <div class="flex items-center gap-2 flex-wrap">
        <h1 class="font-display font-bold text-[20px]" style="color:var(--text)">{{ $pokokBahasan->judul }}</h1>
        <span class="text-[11px] px-2 py-0.5 rounded-full bg-emerald-500/15 text-emerald-400 font-semibold">Preview Instruktur</span>
      </div>
      @if($pokokBahasan->deskripsi)
        <p class="text-[13px] mt-1" style="color:var(--muted)">{{ $pokokBahasan->deskripsi }}</p>
      @endif
      <div class="flex items-center gap-3 mt-2 text-[11px]" style="color:var(--muted)">
        <span><i class="fa-solid fa-book-open mr-1"></i>{{ $mataKuliah->kode }} — {{ $mataKuliah->nama }}</span>
        <span><i class="fa-solid fa-layer-group mr-1"></i>{{ $materi->count() }} materi</span>
        <span><i class="fa-solid fa-check-circle mr-1 text-emerald-400"></i>{{ $materi->where('status','Aktif')->count() }} aktif</span>
      </div>
    </div>
    <a href="{{ route('instruktur.pokok-bahasan.materi', $pokokBahasan->id) }}"
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
      <p class="text-[12px] mt-1">Tambahkan materi dari halaman kelola.</p>
    </div>
  @else
    <div class="space-y-5">
      @foreach($materi as $i => $m)
        @php
          $tipeClass = match($m->tipe) {
            'dokumen' => 'tc-dokumen',
            'video'   => 'tc-video',
            'link'    => 'tc-link',
            'teks'    => 'tc-teks',
            default   => 'a-bg-lt a-text',
          };
          $tipeIcon = match($m->tipe) {
            'dokumen' => 'fa-file-lines',
            'video'   => 'fa-circle-play',
            'link'    => 'fa-link',
            'teks'    => 'fa-align-left',
            default   => 'fa-file',
          };
          $ext = $m->file_path ? strtolower(pathinfo($m->file_path, PATHINFO_EXTENSION)) : '';
          $isPdf = $ext === 'pdf';
        @endphp

        <div class="materi-card {{ $m->status !== 'Aktif' ? 'is-draft' : '' }}">

          {{-- Card header --}}
          <div class="materi-card-head">
            <div class="w-8 h-8 rounded-lg grid place-items-center text-[11px] font-bold flex-shrink-0 {{ $tipeClass }}">
              <i class="fa-solid {{ $tipeIcon }}"></i>
            </div>
            <div class="flex-1 min-w-0">
              <div class="flex items-center gap-2 flex-wrap">
                <span class="font-display font-bold text-[15px]" style="color:var(--text)">{{ $m->judul }}</span>
                <span class="tipe-badge {{ $tipeClass }}">
                  <i class="fa-solid {{ $tipeIcon }}"></i>{{ $m->tipeLabel() }}
                </span>
                @if($m->status !== 'Aktif')
                  <span class="draft-notice"><i class="fa-solid fa-eye-slash"></i>Draft</span>
                @endif
              </div>
              @if($m->deskripsi)
                <p class="text-[12px] mt-0.5 truncate" style="color:var(--muted)">{{ $m->deskripsi }}</p>
              @endif
            </div>
            <span class="text-[11px] font-semibold flex-shrink-0 px-2 py-1 rounded-lg"
                  style="background:var(--surface2);color:var(--muted)">#{{ $i + 1 }}</span>
          </div>

          {{-- Card body --}}
          <div class="materi-card-body">

            {{-- TEKS --}}
            @if($m->tipe === 'teks')
              <div class="teks-content">{{ $m->konten }}</div>

            {{-- DOKUMEN --}}
            @elseif($m->tipe === 'dokumen')
              @if($m->file_path)
                @if($isPdf)
                  {{-- Inline PDF viewer --}}
                  <div class="mb-3 flex items-center justify-between gap-2">
                    <div class="text-[12px]" style="color:var(--muted)">
                      <i class="fa-solid fa-file-pdf text-red-400 mr-1"></i>{{ $m->nama_file }}
                      @if($m->ukuran_file) &bull; {{ $m->ukuranHuman() }}@endif
                    </div>
                    <a href="{{ $m->fileUrl() }}" target="_blank" download
                       class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[11px] font-semibold hover:opacity-80 transition-opacity"
                       style="background:var(--surface2);color:var(--muted);border:1px solid var(--border)">
                      <i class="fa-solid fa-download"></i>Download
                    </a>
                  </div>
                  <object class="pdf-embed" data="{{ $m->fileUrl() }}#toolbar=1&navpanes=0" type="application/pdf">
                    <div class="pdf-fallback">
                      <i class="fa-solid fa-file-pdf text-[28px] text-red-400"></i>
                      <div>Browser tidak mendukung tampilan PDF langsung.</div>
                      <a href="{{ $m->fileUrl() }}" target="_blank"
                         class="px-4 py-2 rounded-lg text-[12px] font-semibold text-white mt-1"
                         style="background:var(--ac)">
                        <i class="fa-solid fa-external-link mr-1.5"></i>Buka PDF
                      </a>
                    </div>
                  </object>
                @else
                  {{-- Non-PDF file download card --}}
                  <div class="file-card">
                    <div class="w-10 h-10 rounded-xl grid place-items-center flex-shrink-0 tc-dokumen text-[16px]">
                      <i class="fa-solid fa-file-lines"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                      <div class="text-[13px] font-semibold truncate" style="color:var(--text)">{{ $m->nama_file }}</div>
                      @if($m->ukuran_file)
                        <div class="text-[11px] mt-0.5" style="color:var(--muted)">{{ $m->ukuranHuman() }}</div>
                      @endif
                    </div>
                    <a href="{{ $m->fileUrl() }}" target="_blank" download
                       class="flex items-center gap-1.5 px-4 py-2 rounded-lg text-[12px] font-semibold text-white flex-shrink-0"
                       style="background:var(--ac)">
                      <i class="fa-solid fa-download"></i>Download
                    </a>
                  </div>
                @endif
              @else
                <div class="text-center py-6 text-[12px]" style="color:var(--muted)">
                  <i class="fa-solid fa-triangle-exclamation mb-2 block text-amber-400"></i>File belum diunggah.
                </div>
              @endif

            {{-- VIDEO --}}
            @elseif($m->tipe === 'video')
              @php
                $videoId  = null;
                $videoUrl = $m->url ?? '';
                // YouTube: youtu.be/ID or youtube.com/watch?v=ID or youtube.com/embed/ID
                if (preg_match('/(?:youtu\.be\/|youtube\.com\/(?:watch\?v=|embed\/|shorts\/))([A-Za-z0-9_-]{11})/', $videoUrl, $match)) {
                    $videoId = $match[1];
                }
              @endphp
              @if($videoId)
                <div class="video-wrap">
                  <iframe src="https://www.youtube.com/embed/{{ $videoId }}"
                          allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                          allowfullscreen loading="lazy"></iframe>
                </div>
              @elseif($videoUrl)
                {{-- Generic video link --}}
                <a href="{{ $videoUrl }}" target="_blank" class="link-card">
                  <div class="w-10 h-10 rounded-xl grid place-items-center flex-shrink-0 tc-video text-[16px]">
                    <i class="fa-solid fa-circle-play"></i>
                  </div>
                  <div class="flex-1 min-w-0">
                    <div class="text-[13px] font-semibold truncate" style="color:var(--text)">Tonton Video</div>
                    <div class="text-[11px] mt-0.5 truncate" style="color:var(--muted)">{{ $videoUrl }}</div>
                  </div>
                  <i class="fa-solid fa-external-link text-[11px] flex-shrink-0" style="color:var(--muted)"></i>
                </a>
              @endif

            {{-- LINK --}}
            @elseif($m->tipe === 'link')
              @if($m->url)
                <a href="{{ $m->url }}" target="_blank" rel="noopener noreferrer" class="link-card">
                  <div class="w-10 h-10 rounded-xl grid place-items-center flex-shrink-0 tc-link text-[16px]">
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
  @endif

</div>
@endsection
