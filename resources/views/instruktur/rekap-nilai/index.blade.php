@extends('layouts.instruktur')
@section('title', 'Rekap Nilai')
@section('page-title', 'Rekap Nilai')

@push('styles')
<style>
.kelas-card{background:var(--surface);border:1px solid var(--border);border-radius:16px;padding:18px 20px;display:flex;align-items:center;gap:14px;transition:border-color .2s,box-shadow .2s;text-decoration:none}
.kelas-card:hover{border-color:var(--ac);box-shadow:0 4px 20px rgba(0,0,0,.12)}
.mk-icon{width:44px;height:44px;border-radius:12px;display:grid;place-items-center;font-size:17px;flex-shrink:0}
.periode-section{margin-bottom:28px}
.periode-label{font-size:11px;font-weight:700;letter-spacing:1.2px;text-transform:uppercase;color:var(--muted);margin-bottom:12px;padding-left:2px}
</style>
@endpush

@section('content')
<div class="space-y-2 animate-fadeUp">

@if($kelasList->isEmpty())
  <div class="text-center py-16" style="color:var(--muted)">
    <i class="fa-solid fa-chalkboard-teacher text-4xl mb-3 block opacity-30"></i>
    <p class="text-sm">Belum ada kelas yang Anda ampu.</p>
  </div>
@else
  @foreach($kelasList as $periode => $kelas)
  <div class="periode-section">
    <div class="periode-label">
      <i class="fa-solid fa-calendar-days mr-1.5"></i>{{ $periode }}
    </div>
    <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-3">
      @foreach($kelas as $k)
      @php
        $instrList = $k->instruktur->pluck('nama')->join(', ');
      @endphp
      <a href="{{ route('instruktur.rekap-nilai.show', $k) }}" class="kelas-card">
        <div class="mk-icon a-bg-lt">
          <i class="fa-solid fa-book-open a-text text-[16px]"></i>
        </div>
        <div class="flex-1 min-w-0">
          <div class="font-semibold text-[14px] truncate" style="color:var(--text)">{{ $k->mataKuliah->nama ?? '—' }}</div>
          <div class="text-[12px] mt-0.5" style="color:var(--muted)">
            {{ $k->kode_display }}
            @if($k->mataKuliah) · {{ $k->mataKuliah->sks }} SKS @endif
          </div>
          @if($instrList)
          <div class="text-[11.5px] mt-0.5 truncate" style="color:var(--muted)">
            <i class="fa-solid fa-user-tie text-[10px] mr-1"></i>{{ $instrList }}
          </div>
          @endif
        </div>
        <i class="fa-solid fa-chevron-right text-[12px] flex-shrink-0" style="color:var(--muted)"></i>
      </a>
      @endforeach
    </div>
  </div>
  @endforeach
@endif

</div>
@endsection
