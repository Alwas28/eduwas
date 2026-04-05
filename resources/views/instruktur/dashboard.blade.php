@extends('layouts.instruktur')
@section('title', 'Dashboard Instruktur')
@section('page-title', 'Dashboard')

@php
  $authUser = auth()->user();
  $hour     = now()->hour;
  $greeting = $hour < 11 ? 'Selamat pagi' : ($hour < 15 ? 'Selamat siang' : ($hour < 18 ? 'Selamat sore' : 'Selamat malam'));
  $nama     = $instruktur?->nama ?? $authUser->name;
  $today    = now()->translatedFormat('l, d F Y');
@endphp

@section('content')
<div class="space-y-6">

  {{-- ── HERO ── --}}
  <div class="relative rounded-2xl overflow-hidden animate-fadeUp" style="background:var(--surface);border:1px solid var(--border)">
    <div class="absolute -top-16 -right-16 w-64 h-64 rounded-full opacity-20 pointer-events-none"
      style="background:radial-gradient(circle,var(--ac),transparent 70%)"></div>
    <div class="absolute -bottom-10 -left-10 w-48 h-48 rounded-full opacity-10 pointer-events-none"
      style="background:radial-gradient(circle,var(--ac2),transparent 70%)"></div>

    <div class="relative flex flex-col sm:flex-row items-start sm:items-center gap-5 px-6 py-6">
      <div class="flex-shrink-0">
        @if($authUser->avatarUrl())
          <img src="{{ $authUser->avatarUrl() }}" alt="Avatar" class="w-16 h-16 rounded-2xl object-cover border-2 a-border">
        @else
          <div class="a-grad w-16 h-16 rounded-2xl grid place-items-center font-display font-bold text-[28px] text-white">
            {{ strtoupper(substr($nama, 0, 1)) }}
          </div>
        @endif
      </div>
      <div class="flex-1 min-w-0">
        <p class="text-[12.5px] font-medium mb-0.5" style="color:var(--muted)">{{ $greeting }},</p>
        <h1 class="font-display font-bold text-[22px] leading-tight truncate" style="color:var(--text)">{{ $nama }}</h1>
        @if($instruktur?->bidang_keahlian)
          <p class="text-[13px] mt-0.5 a-text font-medium">{{ $instruktur->bidang_keahlian }}</p>
        @endif
        <p class="text-[12px] mt-1.5" style="color:var(--muted)">
          <i class="fa-regular fa-calendar mr-1.5"></i>{{ $today }}
        </p>
      </div>
      <div class="flex flex-wrap gap-2 flex-shrink-0">
        @if($instruktur?->nidn)
          <div class="px-3 py-1.5 rounded-xl text-[11.5px] font-mono font-semibold a-bg-lt a-text">NIDN {{ $instruktur->nidn }}</div>
        @endif
        <div class="px-3 py-1.5 rounded-xl text-[11.5px] font-semibold flex items-center gap-1.5
          {{ $instruktur?->status === 'Aktif' ? 'bg-emerald-500/15 text-emerald-400' : 'bg-slate-500/15 text-slate-400' }}">
          <i class="fa-solid fa-circle text-[7px]"></i>{{ $instruktur?->status ?? 'Aktif' }}
        </div>
      </div>
    </div>
  </div>

  {{-- ── STATS ── --}}
  <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="rounded-2xl border p-4 flex items-center gap-3 animate-fadeUp d1" style="background:var(--surface);border-color:var(--border)">
      <div class="a-bg-lt a-text w-11 h-11 rounded-xl grid place-items-center flex-shrink-0">
        <i class="fa-solid fa-chalkboard-user text-[15px]"></i>
      </div>
      <div>
        <div class="font-display text-[26px] font-bold leading-none" style="color:var(--text)">{{ $stats['total'] }}</div>
        <div class="text-[11.5px] mt-0.5" style="color:var(--muted)">Total Kelas</div>
      </div>
    </div>
    <div class="rounded-2xl border p-4 flex items-center gap-3 animate-fadeUp d2" style="background:var(--surface);border-color:var(--border)">
      <div class="w-11 h-11 rounded-xl grid place-items-center flex-shrink-0" style="background:rgba(16,185,129,.14);color:#34d399">
        <i class="fa-solid fa-circle-play text-[15px]"></i>
      </div>
      <div>
        <div class="font-display text-[26px] font-bold leading-none" style="color:var(--text)">{{ $stats['aktif'] }}</div>
        <div class="text-[11.5px] mt-0.5" style="color:var(--muted)">Kelas Aktif</div>
      </div>
    </div>
    <div class="rounded-2xl border p-4 flex items-center gap-3 animate-fadeUp d3" style="background:var(--surface);border-color:var(--border)">
      <div class="w-11 h-11 rounded-xl grid place-items-center flex-shrink-0" style="background:rgba(99,102,241,.14);color:#818cf8">
        <i class="fa-solid fa-users text-[15px]"></i>
      </div>
      <div>
        <div class="font-display text-[26px] font-bold leading-none" style="color:var(--text)">{{ $stats['peserta'] }}</div>
        <div class="text-[11.5px] mt-0.5" style="color:var(--muted)">Total Peserta</div>
      </div>
    </div>
    <div class="rounded-2xl border p-4 flex items-center gap-3 animate-fadeUp d4" style="background:var(--surface);border-color:var(--border)">
      <div class="w-11 h-11 rounded-xl grid place-items-center flex-shrink-0" style="background:rgba(100,116,139,.14);color:#94a3b8">
        <i class="fa-solid fa-circle-check text-[15px]"></i>
      </div>
      <div>
        <div class="font-display text-[26px] font-bold leading-none" style="color:var(--text)">{{ $stats['selesai'] }}</div>
        <div class="text-[11.5px] mt-0.5" style="color:var(--muted)">Kelas Selesai</div>
      </div>
    </div>
  </div>

  {{-- ── MAIN GRID ── --}}
  <div class="grid grid-cols-1 xl:grid-cols-3 gap-5">

    {{-- Kelas Aktif --}}
    <div class="xl:col-span-2 space-y-4">

      <div class="rounded-2xl border overflow-hidden animate-fadeUp d2" style="background:var(--surface);border-color:var(--border)">
        <div class="px-5 py-4 border-b flex items-center justify-between" style="border-color:var(--border)">
          <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-lg grid place-items-center text-[12px] a-bg-lt a-text">
              <i class="fa-solid fa-circle-play"></i>
            </div>
            <span class="font-display font-semibold text-[15px]" style="color:var(--text)">Kelas Aktif</span>
          </div>
          @if($stats['aktif'] > 0)
            <span class="text-[12px] font-semibold px-2.5 py-1 rounded-full a-bg-lt a-text">{{ $stats['aktif'] }} kelas</span>
          @endif
        </div>

        @if($kelasAktif->isEmpty())
          <div class="py-14 text-center">
            <div class="w-14 h-14 rounded-2xl grid place-items-center text-[22px] mx-auto mb-3 a-bg-lt a-text">
              <i class="fa-solid fa-chalkboard"></i>
            </div>
            <p class="text-[13.5px] font-semibold" style="color:var(--text)">Belum ada kelas aktif</p>
            <p class="text-[12px] mt-1" style="color:var(--muted)">Kelas yang Anda ampu akan muncul di sini.</p>
          </div>
        @else
          <div class="divide-y" style="border-color:var(--border)">
            @foreach($kelasAktif as $k)
            @php
              $mk  = $k->mataKuliah;
              $pa  = $k->periodeAkademik;
              $enr = $k->enrollments_count ?? 0;
              $cap = $k->kapasitas;
              $pct = $cap && $cap > 0 ? min(100, round($enr / $cap * 100)) : null;
            @endphp
            <div class="px-5 py-4 flex items-center gap-4 transition-colors border-t"
              style="border-color:var(--border)"
              onmouseover="this.style.background='var(--surface2)'" onmouseout="this.style.background='transparent'">
              <div class="w-10 h-10 rounded-xl grid place-items-center flex-shrink-0 font-display font-bold text-[13px] text-white a-grad">
                {{ strtoupper(substr($mk?->kode ?? '?', 0, 2)) }}
              </div>
              <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 flex-wrap">
                  <p class="text-[13.5px] font-semibold truncate" style="color:var(--text)">{{ $mk?->nama ?? '—' }}</p>
                  <span class="text-[10px] font-mono font-bold px-1.5 py-0.5 rounded a-bg-lt a-text">{{ $k->kode_display }}</span>
                </div>
                <p class="text-[12px] mt-0.5" style="color:var(--muted)">
                  <i class="fa-regular fa-calendar text-[10px] mr-1"></i>{{ $pa?->nama ?? '—' }}
                  @if($mk?->sks) <span class="mx-1">·</span>{{ $mk->sks }} SKS @endif
                </p>
                @if($pct !== null)
                  <div class="mt-2 flex items-center gap-2">
                    <div class="flex-1 h-1.5 rounded-full" style="background:var(--surface2)">
                      <div class="h-1.5 rounded-full a-grad" style="width:{{ $pct }}%"></div>
                    </div>
                    <span class="text-[10.5px] font-semibold flex-shrink-0" style="color:var(--muted)">{{ $enr }}/{{ $cap }}</span>
                  </div>
                @else
                  <p class="text-[11.5px] mt-1" style="color:var(--muted)"><i class="fa-solid fa-users text-[10px] mr-1"></i>{{ $enr }} peserta</p>
                @endif
              </div>
              <span class="flex-shrink-0 inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-500/15 text-emerald-400">
                <i class="fa-solid fa-circle text-[7px]"></i>Aktif
              </span>
            </div>
            @endforeach
          </div>
        @endif
      </div>

      {{-- Riwayat Kelas --}}
      @if($kelas->count() > $kelasAktif->count())
      <div class="rounded-2xl border overflow-hidden animate-fadeUp d3" style="background:var(--surface);border-color:var(--border)">
        <div class="px-5 py-4 border-b flex items-center justify-between" style="border-color:var(--border)">
          <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-lg grid place-items-center text-[12px]" style="background:var(--surface2);color:var(--muted)">
              <i class="fa-solid fa-list"></i>
            </div>
            <span class="font-display font-semibold text-[15px]" style="color:var(--text)">Riwayat Kelas</span>
          </div>
          <span class="text-[12px] font-semibold px-2.5 py-1 rounded-full" style="background:var(--surface2);color:var(--muted)">
            {{ $kelas->count() - $stats['aktif'] }} kelas
          </span>
        </div>
        <div style="overflow-x:auto">
          <table class="w-full text-[13px]">
            <thead>
              <tr style="background:var(--surface2)">
                <th class="text-left px-5 py-3 text-[11px] font-semibold uppercase tracking-wide" style="color:var(--muted)">Mata Kuliah</th>
                <th class="text-left px-4 py-3 text-[11px] font-semibold uppercase tracking-wide" style="color:var(--muted)">Periode</th>
                <th class="text-center px-4 py-3 text-[11px] font-semibold uppercase tracking-wide" style="color:var(--muted)">Peserta</th>
                <th class="text-center px-4 py-3 text-[11px] font-semibold uppercase tracking-wide" style="color:var(--muted)">Status</th>
              </tr>
            </thead>
            <tbody>
              @foreach($kelas->where('status','!=','Aktif')->sortByDesc(fn($k) => $k->periodeAkademik?->created_at) as $k)
              @php
                $stCls = match($k->status) {
                  'Selesai'    => 'bg-blue-500/15 text-blue-400',
                  'Dibatalkan' => 'bg-rose-500/15 text-rose-400',
                  default      => 'bg-slate-500/15 text-slate-400',
                };
              @endphp
              <tr class="border-t transition-colors" style="border-color:var(--border)"
                onmouseover="this.style.background='var(--surface2)'" onmouseout="this.style.background='transparent'">
                <td class="px-5 py-3">
                  <div class="font-semibold" style="color:var(--text)">{{ $k->mataKuliah?->nama ?? '—' }}</div>
                  <div class="text-[11px] font-mono a-text">{{ $k->kode_display }}</div>
                </td>
                <td class="px-4 py-3" style="color:var(--muted)">{{ $k->periodeAkademik?->nama ?? '—' }}</td>
                <td class="px-4 py-3 text-center" style="color:var(--muted)">{{ $k->enrollments_count ?? 0 }}</td>
                <td class="px-4 py-3 text-center">
                  <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold {{ $stCls }}">
                    <i class="fa-solid fa-circle text-[7px]"></i>{{ $k->status }}
                  </span>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
      @endif

    </div>

    {{-- RIGHT PANEL --}}
    <div class="space-y-4">

      {{-- Profil Singkat --}}
      <div class="rounded-2xl border overflow-hidden animate-fadeUp d1" style="background:var(--surface);border-color:var(--border)">
        <div class="h-14 a-grad"></div>
        <div class="px-5 pb-5 -mt-7 text-center">
          <div class="flex justify-center mb-2">
            @if($authUser->avatarUrl())
              <img src="{{ $authUser->avatarUrl() }}" alt="Avatar"
                class="w-14 h-14 rounded-xl object-cover border-4" style="border-color:var(--surface)">
            @else
              <div class="a-grad w-14 h-14 rounded-xl grid place-items-center font-display font-bold text-[22px] text-white border-4"
                style="border-color:var(--surface)">
                {{ strtoupper(substr($nama, 0, 1)) }}
              </div>
            @endif
          </div>
          <p class="font-display font-bold text-[15px]" style="color:var(--text)">{{ $nama }}</p>
          @if($instruktur?->pendidikan_terakhir)
            <p class="text-[11.5px] font-semibold a-text mt-0.5">{{ $instruktur->pendidikan_terakhir }}</p>
          @endif
          @if($instruktur?->email)
            <p class="text-[11.5px] mt-1 truncate" style="color:var(--muted)">{{ $instruktur->email }}</p>
          @endif
        </div>
      </div>

      {{-- Menu Cepat --}}
      <div class="rounded-2xl border overflow-hidden animate-fadeUp d2" style="background:var(--surface);border-color:var(--border)">
        <div class="px-5 py-3.5 border-b" style="border-color:var(--border)">
          <span class="font-display font-semibold text-[14px]" style="color:var(--text)">Menu Cepat</span>
        </div>
        <div class="p-3 grid grid-cols-2 gap-2">
          @php
            $menus = [
              ['fa-folder-open',      'Materi',       route('instruktur.materi.index'),       'rgba(99,102,241,.14)',  '#818cf8'],
              ['fa-clipboard-list',   'Tugas',        route('instruktur.tugas.index'),        'rgba(245,158,11,.14)', '#fbbf24'],
              ['fa-pen-to-square',    'Ujian',        route('instruktur.ujian.index'),        'rgba(239,68,68,.14)',  '#f87171'],
              ['fa-star-half-stroke', 'Rekap Nilai',  route('instruktur.rekap-nilai.index'), 'rgba(16,185,129,.14)', '#34d399'],
            ];
          @endphp
          @foreach($menus as [$mIc, $mTitle, $mHref, $mBg, $mColor])
          <a href="{{ $mHref }}" class="flex flex-col items-center gap-2 p-3 rounded-xl transition-colors text-center"
            style="background:var(--surface2)"
            onmouseover="this.style.background='var(--ac-lt)'" onmouseout="this.style.background='var(--surface2)'">
            <div class="w-9 h-9 rounded-xl grid place-items-center text-[15px]"
              style="background:{{ $mBg }};color:{{ $mColor }}">
              <i class="fa-solid {{ $mIc }}"></i>
            </div>
            <span class="text-[12px] font-semibold" style="color:var(--text)">{{ $mTitle }}</span>
          </a>
          @endforeach
        </div>
      </div>

      {{-- Info Akun --}}
      <div class="rounded-2xl border px-5 py-4 animate-fadeUp d3" style="background:var(--surface);border-color:var(--border)">
        <p class="text-[11px] font-semibold uppercase tracking-wide mb-3" style="color:var(--muted)">Info Akun</p>
        <div class="space-y-2.5">
          <div class="flex items-center gap-2.5">
            <div class="w-7 h-7 rounded-lg grid place-items-center flex-shrink-0 text-[10px] a-bg-lt a-text">
              <i class="fa-solid fa-envelope"></i>
            </div>
            <p class="text-[12px] truncate" style="color:var(--text)">{{ $authUser->email }}</p>
          </div>
          <div class="flex items-center gap-2.5">
            <div class="w-7 h-7 rounded-lg grid place-items-center flex-shrink-0 text-[10px] a-bg-lt a-text">
              <i class="fa-solid fa-calendar-check"></i>
            </div>
            <p class="text-[12px]" style="color:var(--muted)">Bergabung {{ $authUser->created_at->translatedFormat('d F Y') }}</p>
          </div>
          <div class="flex items-center gap-2.5">
            <div class="w-7 h-7 rounded-lg grid place-items-center flex-shrink-0 text-[10px]" style="background:rgba(16,185,129,.14);color:#34d399">
              <i class="fa-solid fa-circle-check"></i>
            </div>
            <p class="text-[12px]" style="color:var(--muted)">Akun terverifikasi</p>
          </div>
        </div>
      </div>

    </div>
  </div>

</div>
@endsection
