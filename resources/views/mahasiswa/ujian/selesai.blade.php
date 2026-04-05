@extends('layouts.mahasiswa')
@section('title', 'Ujian Selesai')
@section('page-title', 'Ujian Selesai')

@push('styles')
<style>
.essay-review-card { background:var(--surface); border:1px solid var(--border); border-radius:14px; overflow:hidden; margin-bottom:12px; }
.essay-review-head { padding:10px 14px; background:var(--surface2); display:flex; align-items:center; justify-content:space-between; }
.essay-review-body { padding:14px; }
.essay-q  { font-size:13.5px; font-weight:600; color:var(--text); margin-bottom:8px; line-height:1.5; }
.essay-ans { font-size:13px; color:var(--sub); background:var(--surface2); border:1px solid var(--border); border-radius:9px; padding:10px 12px; line-height:1.6; white-space:pre-wrap; word-break:break-word; margin-bottom:10px; }
.essay-fb { font-size:12.5px; border-radius:9px; padding:10px 12px; line-height:1.6; margin-top:6px; }
.essay-fb-ai   { background:rgba(16,185,129,.06); border:1px solid rgba(16,185,129,.18); color:var(--sub); }
.essay-fb-inst { background:rgba(16,185,129,.06); border:1px solid rgba(16,185,129,.18); color:var(--sub); }
.fb-label { font-size:11px; font-weight:700; margin-bottom:3px; }
.fb-label-ai   { color:#10b981; }
.fb-label-inst { color:#10b981; }
</style>
@endpush

@section('content')
<div class="max-w-2xl mx-auto pt-10 pb-16 px-4 space-y-6">

  {{-- Icon & Title --}}
  <div class="text-center space-y-4">
    <div class="w-20 h-20 rounded-2xl mx-auto grid place-items-center text-4xl"
      style="background:rgba(16,185,129,.12)">✅</div>
    <div>
      <h1 class="text-2xl font-bold t-text font-display mb-1">Ujian Telah Dikumpulkan</h1>
      <p class="text-sm t-muted">{{ $ujian->judul }}</p>
    </div>
  </div>

  {{-- Summary card --}}
  <div class="t-surf border t-border rounded-2xl p-6 space-y-3">
    <div class="flex justify-between text-sm">
      <span class="t-muted">Dikumpulkan pukul</span>
      <span class="t-text font-semibold">{{ $sesi->submitted_at->format('H:i:s, d M Y') }}</span>
    </div>
    <div class="flex justify-between text-sm">
      <span class="t-muted">Durasi pengerjaan</span>
      <span class="t-text font-semibold">{{ number_format($sesi->mulai_at->diffInSeconds($sesi->submitted_at) / 60, 2) }} menit</span>
    </div>
    @if($sesi->pelanggaran > 0)
    <div class="flex justify-between text-sm">
      <span class="t-muted">Pelanggaran tercatat</span>
      <span class="font-semibold" style="color:#f87171">{{ $sesi->pelanggaran }}x</span>
    </div>
    @endif

    @if($sesi->nilai_status === 'public' && $sesi->nilai !== null)
    <div class="h-px" style="background:var(--border);"></div>
    <div class="flex justify-between items-center">
      <span class="t-muted text-sm">Nilai Akhir</span>
      <span class="font-bold text-2xl" style="color:var(--ac)">{{ number_format($sesi->nilai, 1) }}</span>
    </div>
    @elseif($sesi->nilai_status === 'draft')
    <div class="h-px" style="background:var(--border);"></div>
    <div class="flex items-center gap-2 text-sm" style="color:#f59e0b;">
      <i class="fas fa-clock"></i>
      <span>Nilai sedang dalam proses penilaian. Pantau secara berkala.</span>
    </div>
    @endif
  </div>

  {{-- Essay review (only when public & has essay) --}}
  @if($sesi->nilai_status === 'public' && $essayJawaban->isNotEmpty())
  <div>
    <h2 class="text-base font-bold t-text mb-3">
      <i class="fas fa-pen-to-square" style="color:var(--ac);margin-right:6px;"></i>
      Hasil Penilaian Essay
    </h2>

    @foreach($essayJawaban as $i => $jawaban)
    <div class="essay-review-card">
      <div class="essay-review-head">
        <span style="font-size:12px;font-weight:700;color:var(--muted);">Soal Essay {{ $i + 1 }}</span>
        <span style="font-size:12px;font-weight:700;color:var(--ac);">
          {{ $jawaban->nilai !== null ? $jawaban->nilai . ' / ' . ($jawaban->soal->bobot ?? '?') : 'Belum dinilai' }}
        </span>
      </div>
      <div class="essay-review-body">
        <div class="essay-q">{{ $jawaban->soal->pertanyaan ?? '' }}</div>
        <div class="essay-ans">{{ $jawaban->jawaban_essay ?: '(Tidak ada jawaban)' }}</div>

        @if($jawaban->feedback_instruktur)
        <div class="essay-fb essay-fb-inst">
          <div class="fb-label fb-label-inst"><i class="fas fa-comment-dots"></i> Tanggapan {{ $sapaan }} {{ $namaInstruktur }}</div>
          {{ $jawaban->feedback_instruktur }}
        </div>
        @elseif($jawaban->feedback_ai)
        <div class="essay-fb essay-fb-ai">
          <div class="fb-label fb-label-ai"><i class="fas fa-comment-dots"></i> Tanggapan Asisten {{ $sapaan }} {{ $namaInstruktur }}</div>
          {{ $jawaban->feedback_ai }}
        </div>
        @endif
      </div>
    </div>
    @endforeach
  </div>
  @endif

  {{-- Back button --}}
  <div class="text-center">
    <a href="{{ route('mahasiswa.ujian.index') }}"
      class="inline-flex items-center gap-2 px-6 py-3 rounded-xl text-sm font-bold text-white"
      style="background:linear-gradient(135deg,var(--ac),var(--ac2))">
      <i class="fas fa-arrow-left"></i> Kembali ke Daftar Ujian
    </a>
  </div>

</div>
@endsection
