@extends('layouts.mahasiswa')
@section('title', 'Tugas Saya')
@section('page-title', 'Tugas')

@push('styles')
<style>
/* ── Section label ── */
.section-label {
  font-size:10px; font-weight:700; letter-spacing:.08em; text-transform:uppercase;
  color:var(--muted); margin-bottom:10px; padding-left:2px;
}

/* ── Tugas card ── */
.tugas-card {
  background:var(--surface); border:1px solid var(--border); border-radius:18px;
  overflow:hidden; transition:border-color .15s, transform .15s;
  text-decoration:none; display:block;
}
.tugas-card:hover { border-color:rgba(var(--ac-rgb),.5); transform:translateY(-2px); }
.tugas-card-top { height:3px; }
.tugas-card-body { padding:16px 18px; display:flex; flex-direction:column; gap:10px; }

/* ── Role chip ── */
.role-chip {
  display:inline-flex; align-items:center; gap:5px;
  padding:3px 9px; border-radius:20px; font-size:10px; font-weight:700;
}
.role-ketua  { background:rgba(245,158,11,.14); color:#fbbf24; }
.role-anggota{ background:rgba(16,185,129,.12);  color:#34d399; }

/* ── Status badge ── */
.status-badge {
  display:inline-flex; align-items:center; gap:4px;
  padding:2px 8px; border-radius:20px; font-size:10px; font-weight:700;
}
.s-draft   { background:rgba(100,116,139,.15); color:#94a3b8; }
.s-aktif   { background:rgba(16,185,129,.15);  color:#34d399; }
.s-selesai { background:rgba(59,130,246,.15);  color:#60a5fa; }

/* ── Deadline ── */
.dl-normal  { background:rgba(100,116,139,.12); color:#94a3b8; }
.dl-overdue { background:rgba(244,63,94,.12);   color:#fb7185; }
.dl-soon    { background:rgba(245,158,11,.12);  color:#fbbf24; }

/* ── Topik chip ── */
.topik-chip {
  display:inline-flex; align-items:center; gap:5px; max-width:100%;
  padding:4px 10px; border-radius:10px; font-size:11px;
  background:rgba(99,102,241,.1); color:#818cf8;
  white-space:nowrap; overflow:hidden; text-overflow:ellipsis;
}

/* ── Anggota avatars ── */
.av-row { display:flex; align-items:center; gap:-4px; }
.av-item {
  width:24px; height:24px; border-radius:7px;
  display:grid; place-items:center; font-size:9px; font-weight:700;
  border:2px solid var(--surface); flex-shrink:0; margin-left:-4px;
}
.av-item:first-child { margin-left:0; }

/* ── Empty ── */
.empty-box {
  background:var(--surface); border:1px solid var(--border); border-radius:18px;
  padding:48px 24px; text-align:center; color:var(--muted);
}
</style>
@endpush

@section('content')
@php
use Carbon\Carbon;

$deadlineClass = function(?Carbon $dl, string $status): string {
    if (!$dl || $status === 'selesai') return 'dl-normal';
    if ($dl->isPast()) return 'dl-overdue';
    if ($dl->diffInHours() <= 48) return 'dl-soon';
    return 'dl-normal';
};
@endphp

<div class="space-y-6 animate-fadeUp">

  {{-- ── Sebagai Ketua ─────────────────────────────────────────── --}}
  <div>
    <div class="section-label">
      <i class="fa-solid fa-crown mr-1.5" style="color:#fbbf24"></i>Sebagai Ketua Kelompok
    </div>

    @if($sebagaiKetua->isEmpty())
      <div class="empty-box">
        <i class="fa-solid fa-crown text-[28px] opacity-20 block mb-3"></i>
        <div class="text-[13px] font-semibold mb-1" style="color:var(--text)">Belum ada tugas sebagai ketua</div>
        <p class="text-[12px]">Instruktur akan menunjuk kamu sebagai ketua kelompok.</p>
      </div>
    @else
      <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
        @foreach($sebagaiKetua as $kelompok)
          @php
            $tugas   = $kelompok->tugas;
            $kelas   = $tugas->kelas;
            $mk      = $kelas->mataKuliah;
            $periode = $kelas->periodeAkademik;
            $isOverdue = $tugas->deadline && $tugas->deadline->isPast() && $tugas->status !== 'selesai';
          @endphp
          <a href="{{ route('mahasiswa.tugas.kelompok.show', $kelompok->id) }}" class="tugas-card">
            <div class="tugas-card-top" style="background:linear-gradient(90deg,#f59e0b,#fbbf24)"></div>
            <div class="tugas-card-body">

              {{-- Top row --}}
              <div class="flex items-start justify-between gap-2">
                <span class="role-chip role-ketua">
                  <i class="fa-solid fa-crown text-[9px]"></i>Ketua
                </span>
                <span class="status-badge s-{{ $tugas->status }}">
                  <i class="fa-solid fa-circle text-[7px]"></i>{{ ucfirst($tugas->status) }}
                </span>
              </div>

              {{-- Nama tugas & kelompok --}}
              <div>
                <div class="font-display font-bold text-[15px] leading-snug" style="color:var(--text)">
                  {{ $tugas->judul }}
                </div>
                <div class="text-[12px] mt-0.5 font-semibold" style="color:var(--muted)">
                  <i class="fa-solid fa-users mr-1"></i>{{ $kelompok->nama_kelompok }}
                </div>
              </div>

              {{-- MK & periode --}}
              <div class="text-[11px]" style="color:var(--muted)">
                <i class="fa-solid fa-book-open mr-1"></i>{{ $mk?->nama ?? '—' }}
                @if($periode) · {{ $periode->nama }} @endif
              </div>

              <div style="border-top:1px solid var(--border)"></div>

              {{-- Anggota + deadline --}}
              <div class="flex items-center justify-between gap-2">
                <div class="flex items-center gap-2">
                  {{-- Avatar anggota --}}
                  <div class="av-row">
                    @foreach($kelompok->anggota->take(4) as $ang)
                      <div class="av-item a-bg-lt a-text">
                        {{ strtoupper(substr($ang->mahasiswa?->nama ?? '?', 0, 1)) }}
                      </div>
                    @endforeach
                    @if($kelompok->anggota_count > 4)
                      <div class="av-item" style="background:var(--surface2);color:var(--muted);font-size:8px">
                        +{{ $kelompok->anggota_count - 4 }}
                      </div>
                    @endif
                  </div>
                  <span class="text-[11px]" style="color:var(--muted)">
                    {{ $kelompok->anggota_count }} anggota
                  </span>
                </div>

                @if($tugas->deadline)
                  <span class="status-badge {{ $deadlineClass($tugas->deadline, $tugas->status) }} text-[10px]">
                    <i class="fa-regular fa-clock"></i>{{ $tugas->deadline->format('d M') }}
                  </span>
                @endif
              </div>

            </div>
          </a>
        @endforeach
      </div>
    @endif
  </div>

  {{-- ── Sebagai Anggota ──────────────────────────────────────── --}}
  <div>
    <div class="section-label">
      <i class="fa-solid fa-user-group mr-1.5" style="color:#34d399"></i>Sebagai Anggota Kelompok
    </div>

    @if($sebagaiAnggota->isEmpty())
      <div class="empty-box">
        <i class="fa-solid fa-user-group text-[28px] opacity-20 block mb-3"></i>
        <div class="text-[13px] font-semibold mb-1" style="color:var(--text)">Belum ada tugas sebagai anggota</div>
        <p class="text-[12px]">Ketua kelompok akan menambahkan kamu ke kelompoknya.</p>
      </div>
    @else
      <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
        @foreach($sebagaiAnggota as $entry)
          @php
            $kelompok = $entry->kelompok;
            $tugas    = $kelompok->tugas;
            $kelas    = $tugas->kelas;
            $mk       = $kelas->mataKuliah;
            $periode  = $kelas->periodeAkademik;
          @endphp
          <a href="{{ route('mahasiswa.tugas.anggota.submit.show', $entry->id) }}" class="tugas-card">
            <div class="tugas-card-top" style="background:linear-gradient(90deg,#10b981,#06b6d4)"></div>
            <div class="tugas-card-body">

              {{-- Top row --}}
              <div class="flex items-start justify-between gap-2">
                <span class="role-chip role-anggota">
                  <i class="fa-solid fa-user text-[9px]"></i>Anggota
                </span>
                <span class="status-badge s-{{ $tugas->status }}">
                  <i class="fa-solid fa-circle text-[7px]"></i>{{ ucfirst($tugas->status) }}
                </span>
              </div>

              {{-- Nama tugas & kelompok --}}
              <div>
                <div class="font-display font-bold text-[15px] leading-snug" style="color:var(--text)">
                  {{ $tugas->judul }}
                </div>
                <div class="text-[12px] mt-0.5 font-semibold" style="color:var(--muted)">
                  <i class="fa-solid fa-users mr-1"></i>{{ $kelompok->nama_kelompok }}
                </div>
              </div>

              {{-- Topik --}}
              @if($entry->topik)
                <div class="topik-chip" title="{{ $entry->topik }}">
                  <i class="fa-solid fa-tag text-[9px]"></i>{{ $entry->topik }}
                </div>
              @else
                <div class="text-[11px]" style="color:var(--muted)">
                  <i class="fa-regular fa-clock mr-1"></i>Topik belum ditentukan
                </div>
              @endif

              {{-- MK & periode --}}
              <div class="text-[11px]" style="color:var(--muted)">
                <i class="fa-solid fa-book-open mr-1"></i>{{ $mk?->nama ?? '—' }}
                @if($periode) · {{ $periode->nama }} @endif
              </div>

              <div style="border-top:1px solid var(--border)"></div>

              {{-- Ketua + status submit + deadline --}}
              <div class="flex items-center justify-between gap-2">
                <div class="flex items-center gap-1.5">
                  @if($kelompok->ketua)
                    <div class="w-5 h-5 rounded-lg grid place-items-center text-[8px] font-bold flex-shrink-0"
                         style="background:rgba(245,158,11,.14);color:#fbbf24">
                      {{ strtoupper(substr($kelompok->ketua->nama ?? '?', 0, 1)) }}
                    </div>
                    <span class="text-[11px]" style="color:var(--muted)">{{ $kelompok->ketua->nama }}</span>
                  @endif
                </div>
                <span class="status-badge {{ $entry->status_submit === 'submitted' ? 's-aktif' : 's-draft' }}">
                  {{ $entry->status_submit === 'submitted' ? 'Dikumpulkan' : 'Belum dikumpulkan' }}
                </span>
              </div>

              @if($tugas->deadline)
                <div>
                  <span class="status-badge {{ $deadlineClass($tugas->deadline, $tugas->status) }}">
                    <i class="fa-regular fa-clock"></i>Deadline: {{ $tugas->deadline->format('d M Y, H:i') }}
                    @if($tugas->deadline->isPast() && $tugas->status !== 'selesai')
                      &bull; Lewat
                    @endif
                  </span>
                </div>
              @endif

            </div>
          </a>
        @endforeach
      </div>
    @endif
  </div>

  {{-- ── Tugas Individu ──────────────────────────────────────── --}}
  <div>
    <div class="section-label">
      <i class="fa-solid fa-user-pen mr-1.5 a-text"></i>Tugas Individu
    </div>

    @if($tugasIndividu->isEmpty())
      <div class="empty-box">
        <i class="fa-solid fa-user-pen text-[28px] opacity-20 block mb-3"></i>
        <div class="text-[13px] font-semibold mb-1" style="color:var(--text)">Belum ada tugas individu</div>
        <p class="text-[12px]">Instruktur akan membuat tugas individu yang bisa kamu kerjakan.</p>
      </div>
    @else
      <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
        @foreach($tugasIndividu as $tugas)
          @php
            $kelas   = $tugas->kelas;
            $mk      = $kelas->mataKuliah;
            $periode = $kelas->periodeAkademik;
            $isSubmitted = $tugas->sudah_submit > 0;
            $isOverdue   = $tugas->deadline && $tugas->deadline->isPast() && $tugas->status !== 'selesai';
          @endphp
          <a href="{{ route('mahasiswa.tugas.individu.show', $tugas->id) }}" class="tugas-card">
            <div class="tugas-card-top" style="background:linear-gradient(90deg,#6366f1,#8b5cf6)"></div>
            <div class="tugas-card-body">

              <div class="flex items-start justify-between gap-2">
                <span class="role-chip" style="background:rgba(99,102,241,.12);color:#818cf8">
                  <i class="fa-solid fa-user-pen text-[9px]"></i>Individu
                </span>
                <span class="status-badge s-{{ $tugas->status }}">
                  <i class="fa-solid fa-circle text-[7px]"></i>{{ ucfirst($tugas->status) }}
                </span>
              </div>

              <div>
                <div class="font-display font-bold text-[15px] leading-snug" style="color:var(--text)">
                  {{ $tugas->judul }}
                </div>
                @if($tugas->deskripsi)
                  <div class="text-[11px] mt-0.5" style="color:var(--muted)">{{ Str::limit($tugas->deskripsi, 60) }}</div>
                @endif
              </div>

              <div class="text-[11px]" style="color:var(--muted)">
                <i class="fa-solid fa-book-open mr-1"></i>{{ $mk?->nama ?? '—' }}
                @if($periode) · {{ $periode->nama }} @endif
              </div>

              <div style="border-top:1px solid var(--border)"></div>

              <div class="flex items-center justify-between gap-2">
                <div class="flex items-center gap-2">
                  @if($tugas->nilai_individu !== null)
                    <span class="status-badge" style="background:rgba(245,158,11,.12);color:#fbbf24">
                      <i class="fa-solid fa-star text-[8px]"></i>Nilai: {{ $tugas->nilai_individu }}
                    </span>
                  @else
                    <span class="status-badge {{ $isSubmitted ? 's-aktif' : 's-draft' }}">
                      {{ $isSubmitted ? 'Dikumpulkan' : 'Belum dikumpulkan' }}
                    </span>
                  @endif
                </div>
                @if($tugas->deadline)
                  <span class="status-badge {{ $deadlineClass($tugas->deadline, $tugas->status) }} text-[10px]">
                    <i class="fa-regular fa-clock"></i>{{ $tugas->deadline->format('d M') }}
                    @if($isOverdue) · Lewat @endif
                  </span>
                @endif
              </div>

            </div>
          </a>
        @endforeach
      </div>
    @endif
  </div>

</div>
@endsection
