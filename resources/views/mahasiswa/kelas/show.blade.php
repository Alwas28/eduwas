@extends('layouts.mahasiswa')
@section('title', $kelas->mataKuliah?->nama ?? 'Detail Kelas')
@section('page-title', 'Detail Kelas')

@push('styles')
<style>
/* ── PB card ── */
.pb-card {
  display:flex; align-items:center; gap:14px;
  padding:14px 18px;
  border:1px solid var(--border); border-radius:14px;
  background:var(--surface);
  text-decoration:none;
  transition:border-color .15s, transform .15s;
}
.pb-card:hover { border-color:rgba(var(--ac-rgb),.5); transform:translateY(-1px); }

/* ── Progress ring ── */
.progress-ring { position:relative; width:44px; height:44px; flex-shrink:0; }
.progress-ring svg { transform:rotate(-90deg); }
.progress-ring .ring-bg  { fill:none; stroke:var(--border); stroke-width:4; }
.progress-ring .ring-val { fill:none; stroke:var(--ac);    stroke-width:4; stroke-linecap:round; transition:stroke-dashoffset .5s; }
.progress-ring .ring-text { position:absolute; inset:0; display:flex; align-items:center; justify-content:center; font-size:9px; font-weight:700; color:var(--text); }

/* ── Info row ── */
.info-row { display:flex; align-items:center; gap:6px; font-size:12px; color:var(--muted); flex-wrap:wrap; }

/* ── Stat card ── */
.stat-card { background:var(--surface); border:1px solid var(--border); border-radius:14px; padding:14px 18px; }

/* ── Section header ── */
.section-head { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:var(--muted); margin-bottom:10px; }

/* ── RPS card ── */
.rps-card {
  display:flex; align-items:center; gap:12px; padding:14px 16px;
  border-radius:12px; border:1px solid var(--border); background:var(--surface2);
}
</style>
@endpush

@section('content')
@php
$mk      = $kelas->mataKuliah;
$periode = $kelas->periodeAkademik;

// Progress keseluruhan kelas
$totalMateriAll  = $pokokBahasanList->sum(fn($pb) => $pb->materi->count());
$aksesedAll      = $pokokBahasanList->sum(fn($pb) => $pb->materi->filter(fn($m) => $aksesMap->has($m->id))->count());
$avgProgressAll  = $totalMateriAll > 0
    ? round($pokokBahasanList->flatMap(fn($pb) => $pb->materi->map(fn($m) => $aksesMap[$m->id]->progress ?? 0))->avg())
    : 0;
@endphp
<div class="space-y-5 animate-fadeUp">

  {{-- Breadcrumb --}}
  <div class="flex items-center gap-2 text-[12px]" style="color:var(--muted)">
    <a href="{{ route('mahasiswa.kelas.index') }}" class="a-text hover:underline">Kelas Saya</a>
    <i class="fa-solid fa-chevron-right text-[10px]"></i>
    <span style="color:var(--text)">{{ $mk?->kode ?? '—' }}</span>
  </div>

  {{-- Header card --}}
  <div class="rounded-2xl border overflow-hidden" style="background:var(--surface);border-color:var(--border)">
    <div class="h-1.5 a-grad"></div>
    <div class="p-5">
      <div class="flex items-start gap-4 flex-wrap">
        <div class="flex-1 min-w-0">
          <div class="flex items-center gap-2 flex-wrap mb-1">
            <span class="font-mono font-bold text-[12px] px-2.5 py-1 rounded-lg a-bg-lt a-text">
              {{ $kelas->kodeDisplay }}
            </span>
            @if($enrollment->status === 'Aktif')
              <span class="text-[10.5px] font-bold px-2 py-0.5 rounded-full"
                    style="background:rgba(16,185,129,.12);color:#10b981">
                <i class="fa-solid fa-circle text-[7px] mr-0.5"></i>Aktif
              </span>
            @endif
          </div>
          <h1 class="font-display font-bold text-[22px] leading-snug" style="color:var(--text)">
            {{ $mk?->nama ?? '—' }}
          </h1>
          <div class="info-row mt-2">
            <span><i class="fa-solid fa-calendar-days mr-1"></i>{{ $periode?->nama ?? '—' }}</span>
            <span>•</span>
            <span><i class="fa-solid fa-graduation-cap mr-1"></i>{{ $mk?->sks ?? '—' }} SKS</span>
            @if($kelas->instruktur->isNotEmpty())
              <span>•</span>
              <span><i class="fa-solid fa-chalkboard-user mr-1"></i>
                {{ $kelas->instruktur->map(fn($i) => $i->nama)->join(', ') }}
              </span>
            @endif
          </div>
        </div>

        {{-- Overall progress ring --}}
        <div class="flex flex-col items-center gap-1">
          <div class="progress-ring">
            @php $r = 18; $circ = 2 * M_PI * $r; $offset = $circ * (1 - $avgProgressAll / 100); @endphp
            <svg width="44" height="44" viewBox="0 0 44 44">
              <circle class="ring-bg"  cx="22" cy="22" r="{{ $r }}"/>
              <circle class="ring-val" cx="22" cy="22" r="{{ $r }}"
                stroke-dasharray="{{ $circ }}"
                stroke-dashoffset="{{ $offset }}"/>
            </svg>
            <div class="ring-text">{{ $avgProgressAll }}%</div>
          </div>
          <div class="text-[10px]" style="color:var(--muted)">Progress</div>
        </div>
      </div>

      {{-- Stats row --}}
      <div class="grid grid-cols-3 gap-3 mt-4">
        <div class="stat-card text-center">
          <div class="font-display font-bold text-[20px]" style="color:var(--text)">{{ $pokokBahasanList->count() }}</div>
          <div class="text-[10px] mt-0.5" style="color:var(--muted)">Pertemuan</div>
        </div>
        <div class="stat-card text-center">
          <div class="font-display font-bold text-[20px]" style="color:var(--text)">{{ $totalMateriAll }}</div>
          <div class="text-[10px] mt-0.5" style="color:var(--muted)">Total Materi</div>
        </div>
        <div class="stat-card text-center">
          <div class="font-display font-bold text-[20px]" style="color:var(--text)">{{ $aksesedAll }}</div>
          <div class="text-[10px] mt-0.5" style="color:var(--muted)">Materi Dibaca</div>
        </div>
      </div>
    </div>
  </div>

  {{-- RPS --}}
  @foreach($kelas->instruktur as $ins)
    {{-- RPS per instruktur tidak ada, tapi kelas bisa punya 1 RPS --}}
  @endforeach
  @if($kelas->rps_path)
  <div>
    <div class="section-head">Rencana Pembelajaran Semester (RPS)</div>
    <div class="rps-card">
      <div class="w-10 h-10 rounded-xl grid place-items-center flex-shrink-0 text-[15px]"
           style="background:rgba(239,68,68,.15);color:#f87171">
        <i class="fa-solid fa-file-pdf"></i>
      </div>
      <div class="flex-1 min-w-0">
        <div class="text-[13px] font-semibold truncate" style="color:var(--text)">{{ $kelas->rps_nama_file }}</div>
        <div class="text-[11px] mt-0.5" style="color:var(--muted)">{{ $kelas->rpsUkuranHuman() }}</div>
      </div>
      <a href="{{ $kelas->rpsUrl() }}" target="_blank" download
         class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[11px] font-semibold hover:opacity-80 transition-opacity"
         style="background:var(--ac);color:#fff">
        <i class="fa-solid fa-download"></i>Download
      </a>
    </div>
  </div>
  @endif

  {{-- Daftar pertemuan --}}
  <div>
    <div class="section-head">Materi Per Pertemuan</div>

    @if($pokokBahasanList->isEmpty())
      <div class="rounded-2xl border py-12 text-center" style="background:var(--surface);border-color:var(--border)">
        <div class="a-bg-lt a-text w-14 h-14 rounded-2xl grid place-items-center text-xl mx-auto mb-3">
          <i class="fa-solid fa-book-open"></i>
        </div>
        <p class="text-[13px]" style="color:var(--muted)">Belum ada materi yang tersedia.</p>
      </div>
    @else
      <div class="space-y-3">
        @foreach($pokokBahasanList as $pb)
          @php
            $pbMateriIds  = $pb->materi->pluck('id');
            $totalM       = $pb->materi->count();
            $aksesedCount = $pbMateriIds->filter(fn($id) => $aksesMap->has($id))->count();
            $avgProg      = $totalM > 0
              ? round($pbMateriIds->map(fn($id) => $aksesMap[$id]->progress ?? 0)->avg())
              : 0;
            $r2 = 15; $circ2 = 2 * M_PI * $r2; $off2 = $circ2 * (1 - $avgProg / 100);
          @endphp
          @if($totalM === 0)
            <div class="pb-card" style="opacity:.5;cursor:default">
          @else
            <a href="{{ route('mahasiswa.materi.show', [$kelas->id, $pb->id]) }}" class="pb-card">
          @endif
            {{-- Nomor pertemuan --}}
            <div class="w-10 h-10 rounded-xl grid place-items-center font-display font-bold text-[15px] flex-shrink-0 a-bg-lt a-text">
              {{ $pb->pertemuan }}
            </div>

            {{-- Info --}}
            <div class="flex-1 min-w-0">
              <div class="font-semibold text-[14px] truncate" style="color:var(--text)">{{ $pb->judul }}</div>
              @if($pb->deskripsi)
                <div class="text-[11px] mt-0.5 truncate" style="color:var(--muted)">{{ $pb->deskripsi }}</div>
              @endif
              <div class="flex items-center gap-3 mt-1">
                <span class="text-[11px]" style="color:var(--muted)">
                  <i class="fa-solid fa-layer-group mr-1"></i>{{ $totalM }} materi
                </span>
                @if($aksesedCount > 0)
                  <span class="text-[11px]" style="color:var(--muted)">
                    <i class="fa-solid fa-check mr-1 text-emerald-400"></i>{{ $aksesedCount }} dibaca
                  </span>
                @endif
              </div>
            </div>

            {{-- Progress ring kecil --}}
            @if($totalM > 0)
              <div class="flex flex-col items-center gap-0.5 flex-shrink-0">
                <div class="progress-ring" style="width:38px;height:38px">
                  <svg width="38" height="38" viewBox="0 0 38 38">
                    <circle class="ring-bg"  cx="19" cy="19" r="{{ $r2 }}"/>
                    <circle class="ring-val" cx="19" cy="19" r="{{ $r2 }}"
                      stroke-dasharray="{{ $circ2 }}"
                      stroke-dashoffset="{{ $off2 }}"/>
                  </svg>
                  <div class="ring-text" style="font-size:8px">{{ $avgProg }}%</div>
                </div>
              </div>
              <i class="fa-solid fa-chevron-right text-[11px] flex-shrink-0" style="color:var(--muted)"></i>
            @endif

          @if($totalM === 0)
            </div>
          @else
            </a>
          @endif
        @endforeach
      </div>
    @endif
  </div>

</div>
@endsection
