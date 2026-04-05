@php
  $data = [
    'id'         => $m->id,
    'judul'      => $m->judul,
    'deskripsi'  => $m->deskripsi,
    'tipe'       => $m->tipe,
    'tipe_label' => $m->tipeLabel(),
    'tipe_icon'  => $m->tipeIcon(),
    'tipe_color' => $m->tipeColor(),
    'url'        => $m->url,
    'file_url'   => $m->fileUrl(),
    'nama_file'  => $m->nama_file,
    'ukuran'     => $m->ukuranHuman(),
    'konten'     => $m->konten,
    'urutan'     => $m->urutan,
    'status'     => $m->status,
    'created_at' => $m->created_at->diffForHumans(),
  ];
@endphp

<div class="materi-card p-4 flex items-start gap-4" data-materi-id="{{ $m->id }}" data-tipe="{{ $m->tipe }}">

  {{-- Tipe icon --}}
  <div class="w-10 h-10 rounded-xl grid place-items-center flex-shrink-0 text-[14px] {{ $m->tipeColor() }}">
    <i class="fa-solid {{ $m->tipeIcon() }}"></i>
  </div>

  {{-- Info --}}
  <div class="flex-1 min-w-0 space-y-1">
    <div class="flex items-center gap-2 flex-wrap">
      <span class="font-semibold text-[14px]" style="color:var(--text)">{{ $m->judul }}</span>
      @if($m->status === 'Aktif')
        <span class="badge-aktif text-[10.5px] font-bold px-2 py-0.5 rounded-full">Aktif</span>
      @else
        <span class="badge-draft text-[10.5px] font-bold px-2 py-0.5 rounded-full">Draft</span>
      @endif
      <span class="text-[10.5px] px-2 py-0.5 rounded-full" style="background:var(--border);color:var(--muted)">#{{ $m->urutan }}</span>
    </div>

    @if($m->deskripsi)
      <p class="text-[12.5px]" style="color:var(--muted)">{{ $m->deskripsi }}</p>
    @endif

    <div class="flex items-center gap-3 flex-wrap pt-0.5">
      <span class="text-[11px]" style="color:var(--muted)">
        <i class="fa-solid fa-tag text-[9px] mr-1"></i>{{ $m->tipeLabel() }}
      </span>
      @if($m->tipe === 'dokumen' && $m->nama_file)
        <span class="text-[11px]" style="color:var(--muted)">{{ $m->nama_file }} · {{ $m->ukuranHuman() }}</span>
      @elseif(in_array($m->tipe, ['link','video']) && $m->url)
        <span class="text-[11px] truncate max-w-[260px]" style="color:var(--muted)">{{ $m->url }}</span>
      @elseif($m->tipe === 'teks' && $m->konten)
        <span class="text-[11px]" style="color:var(--muted)">{{ Str::limit($m->konten, 80) }}</span>
      @endif
      <span class="text-[11px]" style="color:var(--muted)">{{ $m->created_at->diffForHumans() }}</span>
    </div>
  </div>

  {{-- Actions --}}
  <div class="flex items-center gap-1.5 flex-shrink-0">
    @if($m->tipe === 'dokumen' && $m->fileUrl())
      <a href="{{ $m->fileUrl() }}" target="_blank"
        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[12px] font-semibold border transition-colors"
        style="border-color:var(--border);color:var(--sub)"
        onmouseover="this.style.borderColor='var(--ac)';this.style.color='var(--ac)'"
        onmouseout="this.style.borderColor='var(--border)';this.style.color='var(--sub)'">
        <i class="fa-solid fa-download text-[10px]"></i>Unduh
      </a>
    @elseif(in_array($m->tipe, ['link','video']) && $m->url)
      <a href="{{ $m->url }}" target="_blank" rel="noopener"
        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[12px] font-semibold border transition-colors"
        style="border-color:var(--border);color:var(--sub)"
        onmouseover="this.style.borderColor='var(--ac)';this.style.color='var(--ac)'"
        onmouseout="this.style.borderColor='var(--border)';this.style.color='var(--sub)'">
        <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>Buka
      </a>
    @endif

    <button onclick='openEditModal({{ json_encode($data) }})'
      class="w-8 h-8 rounded-lg grid place-items-center text-[12px] border transition-colors"
      style="border-color:var(--border);color:var(--muted)"
      onmouseover="this.style.borderColor='var(--ac)';this.style.color='var(--ac)'"
      onmouseout="this.style.borderColor='var(--border)';this.style.color='var(--muted)'"
      title="Edit">
      <i class="fa-solid fa-pen"></i>
    </button>

    <button onclick="deleteMateri({{ $m->id }}, '{{ addslashes($m->judul) }}')"
      class="w-8 h-8 rounded-lg grid place-items-center text-[12px] border transition-colors"
      style="border-color:var(--border);color:var(--muted)"
      onmouseover="this.style.borderColor='#f87171';this.style.color='#f87171'"
      onmouseout="this.style.borderColor='var(--border)';this.style.color='var(--muted)'"
      title="Hapus">
      <i class="fa-solid fa-trash-can"></i>
    </button>
  </div>

</div>
