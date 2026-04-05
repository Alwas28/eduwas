@extends('layouts.mahasiswa')
@section('title','Dashboard')
@section('page-title','Dashboard')

@section('content')
<div class="space-y-6">

{{-- ── No Profile Warning ── --}}
@if(!$mahasiswa)
<div class="rounded-2xl border p-6 flex items-start gap-4" style="background:rgba(245,158,11,.08);border-color:rgba(245,158,11,.3)">
  <div class="w-10 h-10 rounded-xl grid place-items-center flex-shrink-0 text-amber-400" style="background:rgba(245,158,11,.15)">
    <i class="fa-solid fa-triangle-exclamation"></i>
  </div>
  <div>
    <div class="font-semibold text-[14px] text-amber-400 mb-1">Profil belum terhubung</div>
    <p class="text-[13px]" style="color:var(--muted)">Akun Anda belum memiliki data profil mahasiswa. Silakan hubungi administrator.</p>
  </div>
</div>
@else

{{-- ── Welcome Banner ── --}}
<div class="rounded-2xl border overflow-hidden animate-fadeUp" style="background:var(--surface);border-color:var(--border)">
  <div class="relative px-6 py-5 overflow-hidden">
    <div class="absolute -right-12 -top-12 w-48 h-48 rounded-full opacity-10 a-grad pointer-events-none"></div>
    <div class="absolute -right-4 top-8 w-24 h-24 rounded-full opacity-5 a-grad pointer-events-none"></div>
    <div class="relative flex items-center justify-between gap-4 flex-wrap">
      <div class="flex items-center gap-4">
        @if(auth()->user()->avatar)
          <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="Avatar"
            class="w-14 h-14 rounded-2xl object-cover border-2 a-border flex-shrink-0">
        @else
          <div class="a-grad w-14 h-14 rounded-2xl grid place-items-center flex-shrink-0">
            <span class="text-white font-display font-bold text-[22px]">{{ strtoupper(substr($mahasiswa->nama, 0, 1)) }}</span>
          </div>
        @endif
        <div>
          <p class="text-[12px] mb-0.5" style="color:var(--muted)">Selamat datang kembali 👋</p>
          <h2 class="font-display font-bold text-[20px]" style="color:var(--text)">{{ $mahasiswa->nama }}</h2>
          <div class="flex items-center gap-3 mt-1 flex-wrap">
            <span class="text-[12px] font-mono font-semibold a-text">{{ $mahasiswa->nim }}</span>
            <span style="color:var(--border)">·</span>
            <span class="text-[12px]" style="color:var(--muted)">{{ $mahasiswa->jurusan?->nama ?? '—' }}</span>
            <span style="color:var(--border)">·</span>
            <span class="text-[12px]" style="color:var(--muted)">Angkatan {{ $mahasiswa->angkatan }}</span>
          </div>
        </div>
      </div>
      @if($periodeAktif)
      <div class="flex-shrink-0">
        <div class="inline-flex items-center gap-2 px-3 py-2 rounded-xl border" style="background:var(--surface2);border-color:var(--border)">
          <i class="fa-solid fa-calendar-days text-[12px] a-text"></i>
          <div>
            <div class="text-[10.5px]" style="color:var(--muted)">Periode Aktif</div>
            <div class="text-[12.5px] font-semibold" style="color:var(--text)">{{ $periodeAktif->nama }}</div>
            <div class="text-[10.5px]" style="color:var(--muted)">{{ $periodeAktif->tahun_ajaran }} · {{ $periodeAktif->semester }}</div>
          </div>
        </div>
      </div>
      @endif
    </div>
  </div>
</div>

{{-- ── Stat Cards ── --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 animate-fadeUp d1">
  <div class="rounded-2xl p-5 border flex items-center gap-4" style="background:var(--surface);border-color:var(--border)">
    <div class="a-bg-lt a-text w-11 h-11 rounded-xl grid place-items-center text-lg flex-shrink-0">
      <i class="fa-solid fa-door-open"></i>
    </div>
    <div>
      <div class="font-display text-[28px] font-bold" style="color:var(--text)">{{ $stats['kelas_aktif'] }}</div>
      <div class="text-[12px]" style="color:var(--muted)">Kelas Semester Ini</div>
    </div>
  </div>
  <div class="rounded-2xl p-5 border flex items-center gap-4" style="background:var(--surface);border-color:var(--border)">
    <div class="w-11 h-11 rounded-xl grid place-items-center text-lg flex-shrink-0" style="background:rgba(99,102,241,.14);color:#818cf8">
      <i class="fa-solid fa-layer-group"></i>
    </div>
    <div>
      <div class="font-display text-[28px] font-bold" style="color:var(--text)">{{ $stats['total_sks'] }}</div>
      <div class="text-[12px]" style="color:var(--muted)">Total SKS</div>
    </div>
  </div>
  <div class="rounded-2xl p-5 border flex items-center gap-4" style="background:var(--surface);border-color:var(--border)">
    <div class="w-11 h-11 rounded-xl grid place-items-center text-lg flex-shrink-0" style="background:rgba(245,158,11,.14);color:#fbbf24">
      <i class="fa-solid fa-star-half-stroke"></i>
    </div>
    <div>
      <div class="font-display text-[28px] font-bold" style="color:var(--text)">
        {{ $stats['rata_nilai'] !== null ? number_format($stats['rata_nilai'], 1) : '—' }}
      </div>
      <div class="text-[12px]" style="color:var(--muted)">Rata-rata Nilai</div>
    </div>
  </div>
  <div class="rounded-2xl p-5 border flex items-center gap-4" style="background:var(--surface);border-color:var(--border)">
    <div class="w-11 h-11 rounded-xl grid place-items-center text-lg flex-shrink-0" style="background:rgba(16,185,129,.14);color:#34d399">
      <i class="fa-solid fa-circle-check"></i>
    </div>
    <div>
      <div class="font-display text-[28px] font-bold" style="color:var(--text)">{{ $stats['total_kelas'] }}</div>
      <div class="text-[12px]" style="color:var(--muted)">Total Kelas Diikuti</div>
    </div>
  </div>
</div>

{{-- ── Quick Nav ── --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-3 animate-fadeUp d2">
  @php
    $quickNavs = [
      ['fa-book-open-reader', 'rgba(99,102,241,.14)', '#818cf8', 'Materi', route('mahasiswa.materi.index')],
      ['fa-clipboard-list',   'rgba(245,158,11,.14)', '#fbbf24', 'Tugas',  route('mahasiswa.tugas.index')],
      ['fa-file-pen',         'rgba(239,68,68,.14)',  '#f87171', 'Ujian',  route('mahasiswa.ujian.index')],
      ['fa-star-half-stroke', 'rgba(16,185,129,.14)', '#34d399', 'Nilai',  route('mahasiswa.nilai.index')],
    ];
  @endphp
  @foreach($quickNavs as [$qIcon, $qBg, $qColor, $qLabel, $qHref])
  <a href="{{ $qHref }}" class="rounded-2xl border p-4 flex items-center gap-3 transition-all"
    style="background:var(--surface);border-color:var(--border)"
    onmouseover="this.style.borderColor='{{ $qColor }}';this.style.background='var(--surface2)'"
    onmouseout="this.style.borderColor='var(--border)';this.style.background='var(--surface)'">
    <div class="w-9 h-9 rounded-xl grid place-items-center flex-shrink-0 text-[14px]"
      style="background:{{ $qBg }};color:{{ $qColor }}">
      <i class="fa-solid {{ $qIcon }}"></i>
    </div>
    <span class="text-[13px] font-semibold" style="color:var(--text)">{{ $qLabel }}</span>
    <i class="fa-solid fa-chevron-right ml-auto text-[10px]" style="color:var(--muted)"></i>
  </a>
  @endforeach
</div>

{{-- ── Kelas Semester Ini ── --}}
<div class="animate-fadeUp d3">
  <div class="flex items-center justify-between mb-4">
    <h3 class="font-display font-semibold text-[16px]" style="color:var(--text)">
      Kelas Semester Ini
      @if($periodeAktif)
        <span class="ml-2 text-[12px] font-normal" style="color:var(--muted)">{{ $periodeAktif->nama }}</span>
      @endif
    </h3>
    <span class="text-[12px] px-2.5 py-1 rounded-full font-semibold a-bg-lt a-text">
      {{ $kelasPeriodeAktif->count() }} kelas
    </span>
  </div>

  @if($kelasPeriodeAktif->isEmpty())
  <div class="rounded-2xl border p-10 text-center" style="background:var(--surface);border-color:var(--border)">
    <div class="a-bg-lt a-text w-14 h-14 rounded-2xl grid place-items-center text-2xl mx-auto mb-4">
      <i class="fa-solid fa-door-open"></i>
    </div>
    <p class="font-semibold text-[14px] mb-1" style="color:var(--text)">Belum ada kelas semester ini</p>
    <p class="text-[13px]" style="color:var(--muted)">
      @if($periodeAktif) Anda belum didaftarkan ke kelas pada periode {{ $periodeAktif->nama }}.
      @else Belum ada periode akademik aktif. @endif
    </p>
  </div>
  @else
  <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
    @foreach($kelasPeriodeAktif as $enrollment)
    @php
      $kelas = $enrollment->kelas;
      $mk    = $kelas->mataKuliah;
      $statusColor = match($enrollment->status) {
        'Aktif'   => 'bg-emerald-500/15 text-emerald-400',
        'Dropout' => 'bg-rose-500/15 text-rose-400',
        'Lulus'   => 'bg-blue-500/15 text-blue-400',
        default   => 'bg-slate-500/15 text-slate-400',
      };
      $kodeDisplay = $mk?->kode ?? '?';
      if ($kelas->kode_seksi) $kodeDisplay .= '-' . $kelas->kode_seksi;
    @endphp
    <div class="rounded-2xl border flex flex-col overflow-hidden transition-all"
      style="background:var(--surface);border-color:var(--border)"
      onmouseover="this.style.borderColor='var(--ac)'" onmouseout="this.style.borderColor='var(--border)'">

      {{-- Header --}}
      <div class="px-5 py-4 border-b" style="border-color:var(--border)">
        <div class="flex items-start justify-between gap-2">
          <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 mb-1 flex-wrap">
              <code class="text-[11px] font-bold px-2 py-0.5 rounded-lg a-bg-lt a-text font-mono">{{ $kodeDisplay }}</code>
              @if($mk?->sks)
                <span class="text-[11px] px-2 py-0.5 rounded-lg font-medium" style="background:var(--surface2);color:var(--muted)">{{ $mk->sks }} SKS</span>
              @endif
            </div>
            <h4 class="font-semibold text-[14px] leading-snug" style="color:var(--text)">{{ $mk?->nama ?? 'Mata Kuliah' }}</h4>
          </div>
          <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold flex-shrink-0 {{ $statusColor }}">
            <i class="fa-solid fa-circle text-[7px]"></i>{{ $enrollment->status }}
          </span>
        </div>
      </div>

      {{-- Body --}}
      <div class="px-5 py-4 flex-1 space-y-2.5">
        <div class="flex items-start gap-2">
          <i class="fa-solid fa-chalkboard-user text-[12px] mt-0.5 flex-shrink-0 a-text"></i>
          <div class="text-[12.5px] leading-snug" style="color:var(--sub)">
            @if($kelas->instruktur->isEmpty())
              <span style="color:var(--muted)">Belum ada instruktur</span>
            @else
              {{ $kelas->instruktur->pluck('nama')->join(', ') }}
            @endif
          </div>
        </div>
        @if($mk?->jurusan)
        <div class="flex items-center gap-2">
          <i class="fa-solid fa-building-columns text-[12px] flex-shrink-0 a-text"></i>
          <span class="text-[12.5px]" style="color:var(--sub)">{{ $mk->jurusan->nama }}</span>
        </div>
        @endif
        @if($enrollment->nilai_akhir !== null)
        <div class="flex items-center gap-2">
          <i class="fa-solid fa-star text-[12px] flex-shrink-0" style="color:#fbbf24"></i>
          <span class="text-[12.5px] font-semibold" style="color:var(--text)">Nilai Akhir: {{ number_format($enrollment->nilai_akhir, 1) }}</span>
        </div>
        @endif
      </div>

      {{-- Footer --}}
      <div class="px-5 py-3 border-t" style="border-color:var(--border)">
        <a href="{{ route('mahasiswa.kelas.show', $kelas) }}"
          class="w-full flex items-center justify-center gap-2 px-3 py-2 rounded-xl text-[12.5px] font-semibold transition-all a-bg-lt a-text"
          onmouseover="this.style.background='var(--ac)';this.style.color='white'"
          onmouseout="this.style.background='var(--ac-lt)';this.style.color='var(--ac)'">
          <i class="fa-solid fa-arrow-right-to-bracket text-[11px]"></i>Masuk Kelas
        </a>
      </div>
    </div>
    @endforeach
  </div>
  @endif
</div>

{{-- ── Riwayat Kelas ── --}}
@if($kelasLainnya->isNotEmpty())
<div class="animate-fadeUp d4">
  <div class="flex items-center justify-between mb-4">
    <h3 class="font-display font-semibold text-[16px]" style="color:var(--text)">Riwayat Kelas</h3>
    <span class="text-[12px] px-2.5 py-1 rounded-full font-semibold" style="background:var(--surface2);color:var(--muted)">
      {{ $kelasLainnya->count() }} kelas
    </span>
  </div>
  <div class="rounded-2xl border overflow-hidden" style="background:var(--surface);border-color:var(--border)">
    @foreach($kelasLainnya as $i => $enrollment)
    @php
      $kelas = $enrollment->kelas;
      $mk    = $kelas->mataKuliah;
      $grade = $enrollment->grade;
      $gradeColor = match($grade) {
        'A' => 'bg-emerald-500/15 text-emerald-400', 'B' => 'bg-blue-500/15 text-blue-400',
        'C' => 'bg-amber-500/15 text-amber-400',     'D' => 'bg-orange-500/15 text-orange-400',
        'E' => 'bg-rose-500/15 text-rose-400',       default => '',
      };
      $kodeDisplay = ($mk?->kode ?? '?') . ($kelas->kode_seksi ? '-'.$kelas->kode_seksi : '');
    @endphp
    <div class="flex items-center gap-4 px-5 py-4 {{ $i > 0 ? 'border-t' : '' }} transition-colors"
      style="border-color:var(--border)"
      onmouseover="this.style.background='var(--surface2)'" onmouseout="this.style.background='transparent'">
      <div class="a-bg-lt a-text w-9 h-9 rounded-lg grid place-items-center text-[12px] flex-shrink-0 font-mono font-bold">
        {{ $mk?->sks ?? '?' }}
      </div>
      <div class="flex-1 min-w-0">
        <div class="text-[13.5px] font-semibold truncate" style="color:var(--text)">{{ $mk?->nama ?? '—' }}</div>
        <div class="text-[12px]" style="color:var(--muted)">{{ $kodeDisplay }} · {{ $kelas->periodeAkademik?->nama ?? '—' }}</div>
      </div>
      <div class="flex items-center gap-2 flex-shrink-0">
        @if($enrollment->nilai_akhir !== null)
          <span class="text-[13px] font-bold" style="color:var(--text)">{{ number_format($enrollment->nilai_akhir, 1) }}</span>
          @if($grade)
            <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-[12px] font-bold {{ $gradeColor }}">{{ $grade }}</span>
          @endif
        @else
          <span class="text-[12px]" style="color:var(--muted)">—</span>
        @endif
        @php $stColor = match($enrollment->status) { 'Aktif' => 'bg-emerald-500/15 text-emerald-400', 'Lulus' => 'bg-blue-500/15 text-blue-400', 'Dropout' => 'bg-rose-500/15 text-rose-400', default => 'bg-slate-500/15 text-slate-400' }; @endphp
        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold {{ $stColor }}">
          <i class="fa-solid fa-circle text-[7px]"></i>{{ $enrollment->status }}
        </span>
      </div>
    </div>
    @endforeach
  </div>
</div>
@endif

@endif {{-- end if $mahasiswa --}}
</div>
@endsection
