@extends('layouts.mahasiswa')
@section('title', 'Nilai')
@section('page-title', 'Nilai')

@push('styles')
<style>
/* ── Stats ── */
.stat-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:12px}
@media(max-width:640px){.stat-grid{grid-template-columns:repeat(2,1fr)}}
.stat-card{background:var(--surface);border:1px solid var(--border);border-radius:14px;padding:16px 12px;display:flex;flex-direction:column;align-items:center;text-align:center;gap:8px}
.stat-icon{width:40px;height:40px;border-radius:11px;display:grid;place-items-center;font-size:16px;flex-shrink:0}
.stat-val{font-family:'Clash Display',sans-serif;font-size:20px;font-weight:700;color:var(--text);line-height:1}
.stat-lbl{font-size:11.5px;color:var(--muted)}

/* ── Kelas grid: 2 cols on md+ ── */
.kelas-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:14px}
@media(max-width:768px){.kelas-grid{grid-template-columns:1fr}}

/* ── Kelas card ── */
.kelas-card{background:var(--surface);border:1px solid var(--border);border-radius:14px;overflow:hidden;transition:border-color .2s;display:flex;flex-direction:column}
.kelas-card:hover{border-color:var(--ac)}
.kelas-header{padding:12px 14px;border-bottom:1px solid var(--border)}
.kelas-body{padding:12px 14px;flex:1;display:flex;flex-direction:column;gap:14px}

/* ── Pills ── */
.pill{display:inline-flex;align-items:center;gap:4px;padding:2px 8px;border-radius:99px;font-size:11px;font-weight:600}
.pill-aktif{background:rgba(16,185,129,.12);color:#10b981}
.pill-lulus{background:rgba(79,110,247,.12);color:#818cf8}
.pill-pending{background:rgba(100,116,139,.12);color:var(--muted)}
.chip{display:inline-flex;align-items:center;gap:4px;font-size:10.5px;padding:2px 8px;border-radius:99px;background:var(--surface2);border:1px solid var(--border);color:var(--muted)}

/* ── Progress bar ── */
.prog-wrap{background:var(--surface2);border-radius:99px;height:5px;overflow:hidden}
.prog-fill{height:100%;border-radius:99px;background:linear-gradient(90deg,var(--ac),var(--ac2));transition:width .6s ease}

/* ── Instruktur nilai block ── */
.instr-block{background:var(--surface2);border:1px solid var(--border);border-radius:10px;padding:12px 14px}
.instr-block+.instr-block{margin-top:10px}
.instr-name{font-size:11.5px;font-weight:700;color:var(--sub);margin-bottom:10px;display:flex;align-items:center;gap:5px}

/* ── Nilai row ── */
.nilai-row{display:grid;grid-template-columns:repeat(auto-fit,minmax(80px,1fr));gap:8px}
.nilai-box{background:var(--surface);border:1px solid var(--border);border-radius:8px;padding:8px 10px;text-align:center}
.nilai-box-label{font-size:10px;color:var(--muted);font-weight:600;text-transform:uppercase;letter-spacing:.4px;margin-bottom:3px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.nilai-box-val{font-family:'Clash Display',sans-serif;font-size:18px;font-weight:700;line-height:1}
.nilai-null{color:var(--muted);font-family:inherit;font-size:12px;font-weight:400}

/* ── Notice ── */
.notice{background:rgba(245,158,11,.08);border:1px solid rgba(245,158,11,.2);border-radius:8px;padding:8px 12px;font-size:12px;color:#fbbf24;display:flex;align-items:center;gap:7px}
</style>
@endpush

@section('content')
<div class="space-y-7 animate-fadeUp">

{{-- ── Summary Stats ── --}}
<div class="stat-grid">
  <div class="stat-card">
    <div class="stat-icon a-bg-lt"><i class="fa-solid fa-star a-text"></i></div>
    <div class="stat-val">{{ $rataRata !== null ? number_format($rataRata, 1) : '—' }}</div>
    <div class="stat-lbl">Rata-rata Nilai</div>
  </div>
  <div class="stat-card">
    <div class="stat-icon" style="background:rgba(79,110,247,.12)"><i class="fa-solid fa-layer-group" style="color:#818cf8"></i></div>
    <div class="stat-val">{{ $totalSksLulus }}</div>
    <div class="stat-lbl">SKS Lulus</div>
  </div>
  <div class="stat-card">
    <div class="stat-icon" style="background:rgba(245,158,11,.12)"><i class="fa-solid fa-door-open" style="color:#fbbf24"></i></div>
    <div class="stat-val">{{ $kelasAktif }}</div>
    <div class="stat-lbl">Kelas Aktif</div>
  </div>
  <div class="stat-card">
    <div class="stat-icon" style="background:rgba(16,185,129,.12)"><i class="fa-solid fa-circle-check" style="color:#10b981"></i></div>
    <div class="stat-val">{{ $kelasLulus }}</div>
    <div class="stat-lbl">Kelas Lulus</div>
  </div>
</div>

{{-- ── Kelas Cards ── --}}
@if($kelasData->isEmpty())
<div class="text-center py-14" style="color:var(--muted)">
  <i class="fa-solid fa-star-half-stroke text-4xl block mb-3 opacity-30"></i>
  <p class="text-sm">Kamu belum terdaftar di kelas manapun.</p>
</div>
@else
<div class="kelas-grid">
@foreach($kelasData as $idx => $data)
@php
  $enrollment = $data['enrollment'];
  $kelas      = $data['kelas'];
  $mk         = $kelas->mataKuliah;
  $periode    = $kelas->periodeAkademik;
  $status     = $enrollment->status ?? 'Aktif';
  $pillClass  = match($status) { 'Lulus' => 'pill-lulus', 'Aktif' => 'pill-aktif', default => 'pill-pending' };
  $statusIcon = match($status) { 'Lulus' => 'fa-circle-check', 'Aktif' => 'fa-circle-dot', default => 'fa-clock' };
  $progPct    = $data['total_materi'] > 0 ? min(100, round($data['accessed_materi'] / $data['total_materi'] * 100)) : 0;
@endphp
<div class="kelas-card animate-fadeUp" style="animation-delay:{{ ($idx % 4) * 0.05 + 0.05 }}s">

  {{-- Header --}}
  <div class="kelas-header">
    <div class="flex items-start justify-between gap-2">
      <div class="min-w-0 flex-1">
        <div class="font-display font-semibold text-[13.5px] leading-tight truncate" style="color:var(--text)">
          {{ $mk->nama ?? 'Mata Kuliah' }}
        </div>
        <div class="flex flex-wrap gap-1.5 mt-1.5">
          @if($periode)<span class="chip"><i class="fa-solid fa-calendar-days" style="font-size:9px"></i>{{ $periode->nama }}</span>@endif
          <span class="chip"><i class="fa-solid fa-code-branch" style="font-size:9px"></i>{{ $kelas->kode_display }}</span>
          @if($mk)<span class="chip">{{ $mk->sks }} SKS</span>@endif
        </div>
      </div>
      <span class="pill {{ $pillClass }} flex-shrink-0 mt-0.5">
        <i class="fa-solid {{ $statusIcon }}" style="font-size:9px"></i>{{ $status }}
      </span>
    </div>
  </div>

  {{-- Body --}}
  <div class="kelas-body">

    {{-- Progress Materi --}}
    <div>
      <div class="flex justify-between mb-1">
        <span class="text-[11px]" style="color:var(--muted)"><i class="fa-solid fa-book-open mr-1"></i>Materi</span>
        <span class="text-[11px] font-semibold" style="color:var(--sub)">{{ $data['accessed_materi'] }}/{{ $data['total_materi'] }} ({{ $progPct }}%)</span>
      </div>
      <div class="prog-wrap"><div class="prog-fill" style="width:{{ $progPct }}%"></div></div>
    </div>

    {{-- Nilai per instruktur --}}
    @if(! $data['komponen_setup'])
      <div class="notice"><i class="fa-solid fa-circle-info text-[11px]"></i>Instruktur belum mengatur komponen nilai.</div>
    @elseif(count($data['nilai_per_instruktur']) === 0)
      <div class="notice"><i class="fa-solid fa-circle-info text-[11px]"></i>Belum ada nilai tersedia.</div>
    @else
      @foreach($data['nilai_per_instruktur'] as $nd)
      @php $instr = $nd['instruktur']; @endphp
      <div class="instr-block">
        <div class="instr-name">
          <i class="fa-solid fa-user-tie" style="font-size:10px"></i>{{ $instr->nama ?? '—' }}
        </div>
        <div class="nilai-row">

          @if($nd['has_tugas'])
          <div class="nilai-box">
            <div class="nilai-box-label">Tugas</div>
            @if($nd['nilai_tugas'] !== null)
              <div class="nilai-box-val" style="color:{{ $nd['nilai_tugas'] >= 75 ? '#10b981' : ($nd['nilai_tugas'] >= 55 ? '#fbbf24' : '#f87171') }}">
                {{ number_format($nd['nilai_tugas'], 1) }}
              </div>
            @else
              <div class="nilai-box-val nilai-null">—</div>
            @endif
          </div>
          @endif

          @foreach($nd['ujian_detail'] as $ud)
          <div class="nilai-box">
            <div class="nilai-box-label" title="{{ $ud['label'] }}">{{ $ud['label'] }}</div>
            @if($ud['nilai'] !== null)
              <div class="nilai-box-val" style="color:{{ $ud['nilai'] >= 75 ? '#10b981' : ($ud['nilai'] >= 55 ? '#fbbf24' : '#f87171') }}">
                {{ number_format($ud['nilai'], 1) }}
              </div>
            @else
              <div class="nilai-box-val nilai-null">—</div>
            @endif
          </div>
          @endforeach

          @if(! $nd['has_tugas'] && count($nd['ujian_detail']) === 0)
          <div class="nilai-box">
            <div class="nilai-box-label">Nilai</div>
            <div class="nilai-box-val nilai-null">—</div>
          </div>
          @endif

        </div>
      </div>
      @endforeach
    @endif

  </div>
</div>
@endforeach
</div>
@endif

</div>
@endsection
