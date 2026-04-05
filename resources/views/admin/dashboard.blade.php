@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')

{{-- Welcome Banner --}}
<div class="relative overflow-hidden rounded-2xl border p-6 md:p-8 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 animate-fadeUp"
  style="background:var(--surface);border-color:var(--border)">
  <div class="absolute inset-0 pointer-events-none overflow-hidden">
    <div class="absolute -top-10 -right-10 w-60 h-60 rounded-full" style="background:radial-gradient(circle,rgba(var(--ac-rgb),.1),transparent 65%)"></div>
    <div class="absolute -bottom-16 right-28 w-56 h-56 rounded-full" style="background:radial-gradient(circle,rgba(var(--ac-rgb),.06),transparent 65%)"></div>
  </div>
  <div class="relative">
    <h1 class="font-display text-xl md:text-[22px] font-bold mb-1" style="color:var(--text)">Selamat datang kembali, {{ auth()->user()->name }}! 👋</h1>
    <p class="text-sm" style="color:var(--muted)">Pantau perkembangan platform pembelajaran EduWAS hari ini.</p>
  </div>
  <div class="relative hidden md:flex gap-8 flex-shrink-0">
    <div class="text-center"><div class="font-display text-2xl font-bold a-text">0</div><div class="text-[11px] mt-0.5" style="color:var(--muted)">Peserta Aktif</div></div>
    <div class="text-center"><div class="font-display text-2xl font-bold a-text">0</div><div class="text-[11px] mt-0.5" style="color:var(--muted)">Kelas Aktif</div></div>
    <div class="text-center"><div class="font-display text-2xl font-bold a-text">0%</div><div class="text-[11px] mt-0.5" style="color:var(--muted)">Tingkat Selesai</div></div>
  </div>
</div>

{{-- Stat Cards --}}
<div class="grid grid-cols-2 xl:grid-cols-4 gap-4">
  <div class="stat-card relative rounded-2xl p-5 cursor-default overflow-hidden animate-fadeUp d1 border transition-all hover:-translate-y-0.5"
    style="background:var(--surface);border-color:var(--border)"
    onmouseover="this.style.borderColor='var(--ac)'" onmouseout="this.style.borderColor='var(--border)'">
    <div class="a-bg-lt a-text w-11 h-11 rounded-xl grid place-items-center text-lg mb-4"><i class="fa-solid fa-users"></i></div>
    <div class="font-display text-[28px] font-bold leading-none mb-1" style="color:var(--text)">0</div>
    <div class="text-[12.5px] mb-2.5" style="color:var(--muted)">Total Peserta</div>
    <div class="flex items-center gap-1 text-[11.5px] font-semibold" style="color:var(--muted)"><i class="fa-solid fa-minus"></i> Belum ada data</div>
    <div class="absolute bottom-0 left-0 right-0 h-[3px]" style="background:var(--border)"><div class="pf a-bg" style="width:0%"></div></div>
  </div>

  <div class="stat-card relative rounded-2xl p-5 cursor-default overflow-hidden animate-fadeUp d2 border transition-all hover:-translate-y-0.5"
    style="background:var(--surface);border-color:var(--border)"
    onmouseover="this.style.borderColor='var(--ac)'" onmouseout="this.style.borderColor='var(--border)'">
    <div class="a-bg-lt a-text w-11 h-11 rounded-xl grid place-items-center text-lg mb-4"><i class="fa-solid fa-door-open"></i></div>
    <div class="font-display text-[28px] font-bold leading-none mb-1" style="color:var(--text)">0</div>
    <div class="text-[12.5px] mb-2.5" style="color:var(--muted)">Kelas Aktif</div>
    <div class="flex items-center gap-1 text-[11.5px] font-semibold" style="color:var(--muted)"><i class="fa-solid fa-minus"></i> Belum ada data</div>
    <div class="absolute bottom-0 left-0 right-0 h-[3px]" style="background:var(--border)"><div class="pf a-bg" style="width:0%"></div></div>
  </div>

  <div class="stat-card relative rounded-2xl p-5 cursor-default overflow-hidden animate-fadeUp d3 border transition-all hover:-translate-y-0.5"
    style="background:var(--surface);border-color:var(--border)"
    onmouseover="this.style.borderColor='var(--ac)'" onmouseout="this.style.borderColor='var(--border)'">
    <div class="a-bg-lt a-text w-11 h-11 rounded-xl grid place-items-center text-lg mb-4"><i class="fa-solid fa-chalkboard-user"></i></div>
    <div class="font-display text-[28px] font-bold leading-none mb-1" style="color:var(--text)">0</div>
    <div class="text-[12.5px] mb-2.5" style="color:var(--muted)">Instruktur</div>
    <div class="flex items-center gap-1 text-[11.5px] font-semibold" style="color:var(--muted)"><i class="fa-solid fa-minus"></i> Belum ada data</div>
    <div class="absolute bottom-0 left-0 right-0 h-[3px]" style="background:var(--border)"><div class="pf a-bg" style="width:0%"></div></div>
  </div>

  <div class="stat-card relative rounded-2xl p-5 cursor-default overflow-hidden animate-fadeUp d4 border transition-all hover:-translate-y-0.5"
    style="background:var(--surface);border-color:var(--border)"
    onmouseover="this.style.borderColor='var(--ac)'" onmouseout="this.style.borderColor='var(--border)'">
    <div class="a-bg-lt a-text w-11 h-11 rounded-xl grid place-items-center text-lg mb-4"><i class="fa-solid fa-book-open"></i></div>
    <div class="font-display text-[28px] font-bold leading-none mb-1" style="color:var(--text)">0</div>
    <div class="text-[12.5px] mb-2.5" style="color:var(--muted)">Mata Kuliah</div>
    <div class="flex items-center gap-1 text-[11.5px] font-semibold" style="color:var(--muted)"><i class="fa-solid fa-minus"></i> Belum ada data</div>
    <div class="absolute bottom-0 left-0 right-0 h-[3px]" style="background:var(--border)"><div class="pf a-bg" style="width:0%"></div></div>
  </div>
</div>

{{-- Mid Row: Kelas Aktif + Aktivitas Terkini --}}
<div class="grid grid-cols-1 xl:grid-cols-[1fr_340px] gap-5">

  {{-- Tabel Kelas Aktif --}}
  <div class="rounded-2xl overflow-hidden border animate-fadeUp d2" style="background:var(--surface);border-color:var(--border)">
    <div class="flex items-center justify-between px-5 py-4 border-b" style="border-color:var(--border)">
      <span class="font-display font-semibold text-[15px]" style="color:var(--text)">Kelas Aktif</span>
      <a href="#" class="text-[12px] a-text border px-3 py-1 rounded-lg font-medium transition-colors a-bg-lt" style="border-color:rgba(var(--ac-rgb),.3)">Lihat Semua</a>
    </div>
    <div class="overflow-x-auto">
      <table class="w-full">
        <thead>
          <tr class="border-b" style="border-color:var(--border)">
            <th class="text-left text-[10.5px] font-semibold tracking-[.7px] uppercase px-5 py-3" style="color:var(--muted)">Kelas</th>
            <th class="text-left text-[10.5px] font-semibold tracking-[.7px] uppercase px-4 py-3 hidden sm:table-cell" style="color:var(--muted)">Instruktur</th>
            <th class="text-left text-[10.5px] font-semibold tracking-[.7px] uppercase px-4 py-3 hidden md:table-cell" style="color:var(--muted)">Peserta</th>
            <th class="text-left text-[10.5px] font-semibold tracking-[.7px] uppercase px-4 py-3" style="color:var(--muted)">Progress</th>
            <th class="text-left text-[10.5px] font-semibold tracking-[.7px] uppercase px-4 py-3 hidden sm:table-cell" style="color:var(--muted)">Status</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td colspan="5" class="px-5 py-10 text-center">
              <div class="a-bg-lt a-text w-12 h-12 rounded-2xl grid place-items-center text-xl mx-auto mb-3"><i class="fa-solid fa-door-open"></i></div>
              <p class="text-[13px] font-medium" style="color:var(--text)">Belum ada kelas aktif</p>
              <p class="text-[12px] mt-1" style="color:var(--muted)">Buat periode akademik dan kelas terlebih dahulu</p>
              <a href="#" class="inline-flex items-center gap-1.5 mt-3 text-[12px] a-text font-medium">
                <i class="fa-solid fa-plus text-[11px]"></i> Buat Kelas
              </a>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

  {{-- Aktivitas Terkini --}}
  <div class="rounded-2xl overflow-hidden border animate-fadeUp d3" style="background:var(--surface);border-color:var(--border)">
    <div class="flex items-center justify-between px-5 py-4 border-b" style="border-color:var(--border)">
      <span class="font-display font-semibold text-[15px]" style="color:var(--text)">Aktivitas Terkini</span>
      <a href="#" class="text-[12px] a-text font-medium">Lihat Log</a>
    </div>
    <div class="flex flex-col items-center justify-center py-10 px-5 text-center">
      <div class="a-bg-lt a-text w-12 h-12 rounded-2xl grid place-items-center text-xl mb-3"><i class="fa-solid fa-clock-rotate-left"></i></div>
      <p class="text-[13px] font-medium" style="color:var(--text)">Belum ada aktivitas</p>
      <p class="text-[12px] mt-1" style="color:var(--muted)">Aktivitas peserta akan muncul di sini</p>
    </div>
  </div>

</div>

{{-- Bottom Row --}}
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">

  {{-- Instruktur --}}
  <div class="rounded-2xl overflow-hidden border animate-fadeUp d4" style="background:var(--surface);border-color:var(--border)">
    <div class="flex items-center justify-between px-5 py-4 border-b" style="border-color:var(--border)">
      <span class="font-display font-semibold text-[15px]" style="color:var(--text)">Instruktur</span>
      <a href="#" class="text-[12px] a-text font-medium">Semua</a>
    </div>
    <div class="flex flex-col items-center justify-center py-10 px-5 text-center">
      <div class="a-bg-lt a-text w-12 h-12 rounded-2xl grid place-items-center text-xl mb-3"><i class="fa-solid fa-chalkboard-user"></i></div>
      <p class="text-[13px] font-medium" style="color:var(--text)">Belum ada instruktur</p>
      <a href="#" class="inline-flex items-center gap-1.5 mt-3 text-[12px] a-text font-medium">
        <i class="fa-solid fa-plus text-[11px]"></i> Tambah Instruktur
      </a>
    </div>
  </div>

  {{-- Periode Akademik --}}
  <div class="rounded-2xl overflow-hidden border animate-fadeUp d5" style="background:var(--surface);border-color:var(--border)">
    <div class="px-5 py-4 border-b flex items-center justify-between" style="border-color:var(--border)">
      <span class="font-display font-semibold text-[15px]" style="color:var(--text)">Periode Akademik</span>
      <a href="#" class="text-[12px] a-text font-medium">Kelola</a>
    </div>
    <div class="flex flex-col items-center justify-center py-10 px-5 text-center">
      <div class="a-bg-lt a-text w-12 h-12 rounded-2xl grid place-items-center text-xl mb-3"><i class="fa-solid fa-calendar-days"></i></div>
      <p class="text-[13px] font-medium" style="color:var(--text)">Belum ada periode aktif</p>
      <p class="text-[12px] mt-1" style="color:var(--muted)">Buat periode akademik untuk memulai</p>
      <a href="#" class="inline-flex items-center gap-1.5 mt-3 text-[12px] a-text font-medium">
        <i class="fa-solid fa-plus text-[11px]"></i> Buat Periode
      </a>
    </div>
  </div>

  {{-- Kalender Akademik --}}
  <div class="rounded-2xl overflow-hidden border animate-fadeUp d6" style="background:var(--surface);border-color:var(--border)">
    <div class="flex items-center justify-between px-5 py-4 border-b" style="border-color:var(--border)">
      <div>
        <span class="font-display font-semibold text-[15px]" style="color:var(--text)">Kalender Akademik</span>
        <p class="text-[11px] mt-0.5" style="color:var(--muted)">{{ \Carbon\Carbon::now()->translatedFormat('F Y') }}</p>
      </div>
    </div>
    <div class="p-4">
      @php
        $now = \Carbon\Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $daysInMonth = $now->daysInMonth;
        $firstDayOfWeek = $startOfMonth->dayOfWeek; // 0=Sun, 1=Mon...
      @endphp
      <div class="grid grid-cols-7 gap-0.5 mb-1">
        @foreach(['Min','Sen','Sel','Rab','Kam','Jum','Sab'] as $day)
          <div class="text-[10px] font-semibold text-center py-1" style="color:var(--muted)">{{ $day }}</div>
        @endforeach
      </div>
      <div class="grid grid-cols-7 gap-0.5 text-[11px]">
        {{-- Empty cells before first day --}}
        @for($i = 0; $i < $firstDayOfWeek; $i++)
          <div class="aspect-square"></div>
        @endfor
        {{-- Days --}}
        @for($d = 1; $d <= $daysInMonth; $d++)
          @if($d === $now->day)
            <div class="aspect-square grid place-items-center rounded-lg cursor-pointer text-white font-bold a-bg">{{ $d }}</div>
          @else
            <div class="aspect-square grid place-items-center rounded-lg cursor-pointer transition-colors" style="color:var(--text)" onmouseover="this.style.background='var(--surface2)'" onmouseout="this.style.background='transparent'">{{ $d }}</div>
          @endif
        @endfor
      </div>
    </div>
  </div>

</div>

@endsection
