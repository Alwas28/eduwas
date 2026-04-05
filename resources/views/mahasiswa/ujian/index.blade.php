@extends('layouts.mahasiswa')
@section('title', 'Ujian')
@section('page-title', 'Ujian')

@section('content')
<div class="space-y-5 pt-2">

  <div class="flex items-center justify-between">
    <div>
      <h2 class="text-lg font-bold t-text font-display">Ujian Berlangsung</h2>
      <p class="text-xs t-muted mt-0.5">Ujian yang sedang aktif untuk kelas Anda</p>
    </div>
    <div class="text-xs t-muted border t-border rounded-lg px-3 py-1.5">
      <i class="fas fa-clock mr-1"></i>{{ now()->format('d M Y, H:i') }}
    </div>
  </div>

  @forelse($ujianList as $ujian)
  @php
    $sesi    = $sesiMap->get($ujian->id);
    $selesai = $sesi && $sesi->submitted_at;
    $aktif   = $sesi && $sesi->mulai_at && !$selesai;
    $sisa    = $ujian->waktu_selesai->diffInMinutes(now());
  @endphp
  <div class="t-surf border t-border rounded-2xl overflow-hidden animate-fadeUp">
    {{-- Top bar --}}
    <div class="h-1.5" style="background:linear-gradient(90deg,var(--ac),var(--ac2))"></div>
    <div class="p-5">
      <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
        <div class="flex-1">
          <div class="flex items-center gap-2 mb-2 flex-wrap">
            <span class="text-xs font-semibold px-2 py-0.5 rounded-full a-bg-lt a-text">
              {{ $ujian->kelas->mataKuliah->kode ?? '' }}
            </span>
            @if($selesai)
              <span class="text-xs px-2 py-0.5 rounded-full" style="background:rgba(16,185,129,.12);color:#10b981">
                <i class="fas fa-circle-check mr-1"></i>Selesai
              </span>
            @elseif($aktif)
              <span class="text-xs px-2 py-0.5 rounded-full animate-pulse" style="background:rgba(245,158,11,.12);color:#f59e0b">
                <i class="fas fa-circle mr-1" style="font-size:7px"></i>Sedang Dikerjakan
              </span>
            @else
              <span class="text-xs px-2 py-0.5 rounded-full" style="background:rgba(99,102,241,.12);color:#818cf8">
                <i class="fas fa-hourglass-half mr-1"></i>Belum Dimulai
              </span>
            @endif
          </div>
          <h3 class="text-base font-bold t-text mb-1">{{ $ujian->judul }}</h3>
          <p class="text-xs t-muted">{{ $ujian->kelas->mataKuliah->nama ?? '' }} — {{ $ujian->instruktur->nama ?? '' }}</p>
        </div>

        <div class="flex items-center gap-3">
          <div class="text-right">
            <div class="text-xs t-muted mb-1">Waktu Tersisa</div>
            <div class="text-sm font-bold" style="color:#f59e0b">
              {{ $ujian->waktu_selesai->diffForHumans(['parts' => 2, 'short' => true]) }}
            </div>
            <div class="text-xs t-muted">s/d {{ $ujian->waktu_selesai->format('H:i') }}</div>
          </div>
          <div class="text-right">
            <div class="text-xs t-muted mb-1">Durasi</div>
            <div class="text-sm font-bold t-text">{{ $ujian->durasi }} mnt</div>
          </div>
        </div>
      </div>

      <div class="flex items-center gap-3 mt-4 pt-4 border-t t-border flex-wrap">
        <div class="flex items-center gap-4 text-xs t-muted flex-1">
          @if($ujian->ada_essay)
            <span><i class="fas fa-pen-to-square mr-1 a-text"></i>{{ $ujian->jumlah_soal_essay }} Essay</span>
          @endif
          @if($ujian->ada_pg)
            <span><i class="fas fa-list-check mr-1 a-text"></i>{{ $ujian->jumlah_soal_pg }} Pilihan Ganda</span>
          @endif
          <span><i class="fas fa-robot mr-1" style="color:#818cf8"></i>Diawasi AI</span>
        </div>

        @if($selesai)
          <a href="{{ route('mahasiswa.ujian.selesai', $ujian) }}"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold"
            style="background:rgba(16,185,129,.1);color:#10b981;border:1px solid rgba(16,185,129,.2)">
            <i class="fas fa-eye"></i> Lihat Hasil
          </a>
        @elseif($aktif)
          <a href="{{ route('mahasiswa.ujian.exam', $ujian) }}"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold text-white"
            style="background:linear-gradient(135deg,#f59e0b,#ef4444)">
            <i class="fas fa-circle-play"></i> Lanjutkan Ujian
          </a>
        @else
          <a href="{{ route('mahasiswa.ujian.start', $ujian) }}"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold text-white"
            style="background:linear-gradient(135deg,var(--ac),var(--ac2))">
            <i class="fas fa-play"></i> Mulai Ujian
          </a>
        @endif
      </div>
    </div>
  </div>
  @empty
  <div class="t-surf border t-border rounded-2xl p-12 text-center">
    <div class="text-4xl mb-4 opacity-30"><i class="fas fa-clipboard-list"></i></div>
    <p class="t-muted text-sm">Tidak ada ujian yang sedang berlangsung.</p>
  </div>
  @endforelse

</div>
@endsection
