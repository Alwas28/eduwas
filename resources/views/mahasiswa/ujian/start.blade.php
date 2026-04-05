@extends('layouts.mahasiswa')
@section('title', 'Ketentuan Ujian')
@section('page-title', 'Ketentuan Ujian')

@section('content')
<div class="max-w-2xl mx-auto space-y-5 pt-2">

  {{-- Header --}}
  <div class="t-surf border t-border rounded-2xl overflow-hidden animate-fadeUp">
    <div class="h-1.5" style="background:linear-gradient(90deg,var(--ac),var(--ac2))"></div>
    <div class="p-6">
      <div class="flex items-start gap-4">
        <div class="w-12 h-12 rounded-xl flex items-center justify-center text-white text-xl flex-shrink-0"
          style="background:linear-gradient(135deg,var(--ac),var(--ac2))">
          <i class="fas fa-file-pen"></i>
        </div>
        <div>
          <h1 class="text-xl font-bold t-text font-display">{{ $ujian->judul }}</h1>
          <p class="text-sm t-muted mt-1">
            {{ $ujian->kelas->mataKuliah->nama ?? '' }} &bull;
            {{ $ujian->instruktur->nama ?? '' }} &bull;
            Durasi <strong class="t-sub">{{ $ujian->durasi }} menit</strong>
          </p>
        </div>
      </div>
    </div>
  </div>

  {{-- Deskripsi / Petunjuk dari instruktur --}}
  @if($ujian->deskripsi)
  <div class="t-surf border t-border rounded-2xl p-6 animate-fadeUp d1">
    <h2 class="text-sm font-bold t-text mb-3 flex items-center gap-2">
      <span class="w-6 h-6 rounded-lg a-bg-lt a-text grid place-items-center text-xs">
        <i class="fas fa-circle-info"></i>
      </span>
      Deskripsi &amp; Petunjuk Ujian
    </h2>
    <div class="text-sm t-sub leading-relaxed prose-sm"
      style="line-height:1.75;">{!! $ujian->deskripsi !!}</div>
  </div>
  @endif

  {{-- Info soal --}}
  <div class="t-surf border t-border rounded-2xl p-6 animate-fadeUp d2">
    <h2 class="text-sm font-bold t-text mb-4 flex items-center gap-2">
      <span class="w-6 h-6 rounded-lg a-bg-lt a-text grid place-items-center text-xs">
        <i class="fas fa-list-check"></i>
      </span>
      Informasi Soal
    </h2>
    <div class="grid grid-cols-2 gap-3">
      @if($ujian->ada_essay)
      <div class="t-surf2 rounded-xl p-4 border t-border">
        <div class="text-xs t-muted mb-1">Soal Essay</div>
        <div class="text-2xl font-bold a-text">{{ $ujian->jumlah_soal_essay }}</div>
        <div class="text-xs t-muted mt-1">
          {{ $ujian->acak_soal_essay ? '🔀 Urutan diacak' : 'Urutan tetap' }}
        </div>
      </div>
      @endif
      @if($ujian->ada_pg)
      <div class="t-surf2 rounded-xl p-4 border t-border">
        <div class="text-xs t-muted mb-1">Pilihan Ganda</div>
        <div class="text-2xl font-bold a-text">{{ $ujian->jumlah_soal_pg }}</div>
        <div class="text-xs t-muted mt-1">
          {{ $ujian->acak_soal_pg ? '🔀 Soal diacak' : '' }}
          {{ $ujian->acak_pilihan_pg ? ' · 🔀 Pilihan diacak' : '' }}
        </div>
      </div>
      @endif
      <div class="t-surf2 rounded-xl p-4 border t-border">
        <div class="text-xs t-muted mb-1">Waktu Mulai</div>
        <div class="text-sm font-bold t-text">{{ $ujian->waktu_mulai->format('H:i') }}</div>
        <div class="text-xs t-muted">{{ $ujian->waktu_mulai->format('d M Y') }}</div>
      </div>
      <div class="t-surf2 rounded-xl p-4 border t-border">
        <div class="text-xs t-muted mb-1">Waktu Selesai</div>
        <div class="text-sm font-bold" style="color:#f59e0b">{{ $ujian->waktu_selesai->format('H:i') }}</div>
        <div class="text-xs t-muted">{{ $ujian->waktu_selesai->format('d M Y') }}</div>
      </div>
    </div>
  </div>

  {{-- AI Monitoring Alert --}}
  <div class="rounded-2xl p-5 animate-fadeUp d3 border"
    style="background:rgba(139,92,246,.08);border-color:rgba(139,92,246,.25)">
    <div class="flex items-start gap-3">
      <div class="w-10 h-10 rounded-xl grid place-items-center flex-shrink-0 text-lg"
        style="background:rgba(139,92,246,.15);color:#a78bfa">
        <i class="fas fa-robot"></i>
      </div>
      <div>
        <div class="text-sm font-bold mb-1" style="color:#a78bfa">Diawasi oleh AI & Pengawas</div>
        <p class="text-xs leading-relaxed" style="color:rgba(167,139,250,.8)">
          Ujian ini dipantau secara real-time oleh sistem AI dan pengawas manusia.
          Setiap aktivitas mencurigakan akan dicatat dan dilaporkan.
        </p>
      </div>
    </div>
  </div>

  {{-- Ketentuan --}}
  <div class="t-surf border t-border rounded-2xl p-6 animate-fadeUp d3">
    <h2 class="text-sm font-bold t-text mb-4 flex items-center gap-2">
      <span class="w-6 h-6 rounded-lg grid place-items-center text-xs" style="background:rgba(248,113,113,.1);color:#f87171">
        <i class="fas fa-triangle-exclamation"></i>
      </span>
      Ketentuan Ujian — Harap Baca dengan Seksama
    </h2>
    <div class="space-y-3">
      @php
        $rules = [
          ['fas fa-ban',            '#f87171', 'Dilarang berpindah tab atau window selama ujian berlangsung. Setiap perpindahan akan dicatat dan dilaporkan ke pengawas.'],
          ['fas fa-arrows-rotate',  '#f87171', 'Dilarang refresh halaman. Jika terjadi, sesi ujian tetap berjalan dengan waktu yang terus berhitung.'],
          ['fas fa-copy',           '#f87171', 'Dilarang menyalin (copy) soal atau jawaban. Fungsi copy-paste dinonaktifkan selama ujian.'],
          ['fas fa-camera',         '#f87171', 'Dilarang mengambil screenshot soal. Konten soal dilindungi dan terdapat watermark identitas peserta.'],
          ['fas fa-clock',          '#f59e0b', 'Ujian akan otomatis dikumpulkan ketika waktu habis. Pastikan seluruh jawaban telah diisi sebelum waktu berakhir.'],
          ['fas fa-floppy-disk',    '#10b981', 'Jawaban disimpan secara otomatis setiap perubahan. Anda tidak perlu menekan tombol simpan secara manual.'],
          ['fas fa-shield-check',   '#10b981', 'Tombol Submit tersedia kapan saja jika Anda sudah selesai mengerjakan sebelum waktu habis.'],
        ];
      @endphp
      @foreach($rules as [$icon, $color, $text])
      <div class="flex items-start gap-3 p-3 rounded-xl t-surf2">
        <i class="{{ $icon }} mt-0.5 flex-shrink-0 text-sm" style="color:{{ $color }}"></i>
        <p class="text-xs t-sub leading-relaxed">{{ $text }}</p>
      </div>
      @endforeach
    </div>
  </div>

  {{-- Konfirmasi & Mulai --}}
  <div class="t-surf border t-border rounded-2xl p-6 animate-fadeUp d4">
    <label class="flex items-start gap-3 cursor-pointer mb-5">
      <input type="checkbox" id="cb-agree" class="mt-0.5 w-4 h-4 rounded accent-emerald-500 flex-shrink-0">
      <span class="text-sm t-sub leading-relaxed">
        Saya telah membaca dan memahami seluruh ketentuan ujian di atas, dan bersedia mematuhinya selama ujian berlangsung.
      </span>
    </label>

    <form method="POST" action="{{ route('mahasiswa.ujian.begin', $ujian) }}" id="begin-form">
      @csrf
      <button type="submit" id="btn-begin" disabled
        class="w-full py-3 rounded-xl font-bold text-sm text-white flex items-center justify-center gap-2 transition-all"
        style="background:linear-gradient(135deg,var(--ac),var(--ac2));box-shadow:0 4px 20px rgba(var(--ac-rgb),.3);opacity:.5;cursor:not-allowed">
        <i class="fas fa-play"></i> Mulai Ujian Sekarang
      </button>
    </form>
  </div>

  <a href="{{ route('mahasiswa.ujian.index') }}"
    class="flex items-center justify-center gap-2 text-xs t-muted hover:t-sub transition-colors py-2">
    <i class="fas fa-arrow-left text-[10px]"></i> Kembali ke Daftar Ujian
  </a>

</div>

<script>
document.getElementById('cb-agree').addEventListener('change', function() {
  const btn = document.getElementById('btn-begin');
  btn.disabled = !this.checked;
  btn.style.opacity = this.checked ? '1' : '.5';
  btn.style.cursor  = this.checked ? 'pointer' : 'not-allowed';
});

document.getElementById('begin-form').addEventListener('submit', function() {
  const btn = document.getElementById('btn-begin');
  btn.disabled = true;
  btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memulai ujian…';
});
</script>
@endsection
