<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>{{ $kelompok->nama_kelompok }} — {{ $kelompok->tugas->judul }}</title>
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family: 'DejaVu Sans', sans-serif; font-size:11pt; color:#1e293b; line-height:1.6; }

/* Cover */
.cover { text-align:center; padding:60px 40px; border-bottom:3px solid #6366f1; margin-bottom:32px; }
.cover-label { font-size:9pt; font-weight:bold; color:#6366f1; letter-spacing:.15em; text-transform:uppercase; margin-bottom:8px; }
.cover-title { font-size:20pt; font-weight:bold; color:#0f172a; margin-bottom:6px; }
.cover-sub   { font-size:13pt; color:#334155; margin-bottom:24px; }
.cover-meta  { font-size:9.5pt; color:#64748b; }
.cover-meta td { padding:3px 12px; }
.cover-meta .lbl { font-weight:bold; color:#475569; text-align:right; }

/* Section */
.section-title {
  font-size:10pt; font-weight:bold; color:#6366f1;
  text-transform:uppercase; letter-spacing:.1em;
  border-bottom:1.5px solid #e2e8f0; padding-bottom:5px;
  margin: 22px 0 12px;
}

/* Anggota table */
table.anggota { width:100%; border-collapse:collapse; font-size:9.5pt; margin-bottom:20px; }
table.anggota th {
  background:#f1f5f9; color:#475569; font-size:8.5pt;
  text-transform:uppercase; letter-spacing:.06em;
  padding:7px 10px; text-align:left; border:1px solid #e2e8f0;
}
table.anggota td { padding:8px 10px; border:1px solid #e2e8f0; vertical-align:top; }
table.anggota .ketua-tag {
  display:inline-block; font-size:7.5pt; font-weight:bold;
  background:#fef3c7; color:#92400e;
  padding:1px 6px; border-radius:4px; margin-left:4px;
}

/* Konten masing-masing anggota */
.anggota-block { margin-bottom:24px; page-break-inside:avoid; }
.anggota-header {
  background:#f8fafc; border:1px solid #e2e8f0; border-radius:4px;
  padding:9px 12px; margin-bottom:10px;
  display:flex; justify-content:space-between;
}
.anggota-name  { font-weight:bold; font-size:10pt; color:#1e293b; }
.anggota-topik { font-size:9pt; color:#6366f1; }
.anggota-status-submitted { font-size:8pt; color:#10b981; }
.anggota-status-belum     { font-size:8pt; color:#94a3b8; }
.konten-box {
  border:1px solid #e2e8f0; border-radius:4px; padding:12px 14px;
  font-size:10pt; line-height:1.7;
}
.no-konten { color:#94a3b8; font-style:italic; font-size:10pt; }

/* Final */
.final-box {
  border:2px solid #6366f1; border-radius:6px;
  padding:16px 18px; font-size:10.5pt; line-height:1.75;
}

/* Rich content */
h2 { font-size:13pt; font-weight:bold; margin:.6em 0 .3em; }
h3 { font-size:11pt; font-weight:bold; margin:.5em 0 .2em; }
ul, ol { padding-left:1.4em; margin:.3em 0; }
blockquote { border-left:3px solid #6366f1; padding-left:10px; color:#64748b; font-style:italic; margin:.4em 0; }
img { max-width:100%; height:auto; border-radius:4px; margin:.4em 0; }
p { margin:.3em 0; }

/* Footer */
.pdf-footer { text-align:center; font-size:8pt; color:#94a3b8; margin-top:40px; padding-top:12px; border-top:1px solid #e2e8f0; }
</style>
</head>
<body>

{{-- ── Cover ── --}}
<div class="cover">
  <div class="cover-label">Tugas Kelompok</div>
  <div class="cover-title">{{ $kelompok->tugas->judul }}</div>
  <div class="cover-sub">{{ $kelompok->nama_kelompok }}</div>
  <table class="cover-meta" style="margin:0 auto">
    <tr>
      <td class="lbl">Mata Kuliah</td>
      <td>{{ $kelompok->tugas->kelas?->mataKuliah?->nama ?? '—' }}</td>
    </tr>
    @if($kelompok->tugas->kelas?->periodeAkademik)
    <tr>
      <td class="lbl">Periode</td>
      <td>{{ $kelompok->tugas->kelas->periodeAkademik->nama }}</td>
    </tr>
    @endif
    @if($kelompok->tugas->deadline)
    <tr>
      <td class="lbl">Deadline</td>
      <td>{{ $kelompok->tugas->deadline->format('d M Y, H:i') }}</td>
    </tr>
    @endif
    <tr>
      <td class="lbl">Dikumpulkan</td>
      <td>{{ now()->format('d M Y, H:i') }}</td>
    </tr>
  </table>
</div>

{{-- ── Daftar Anggota ── --}}
<div class="section-title">Daftar Anggota Kelompok</div>
<table class="anggota">
  <thead>
    <tr>
      <th>#</th>
      <th>Nama</th>
      <th>NIM</th>
      <th>Topik</th>
      <th>Status</th>
    </tr>
  </thead>
  <tbody>
    @foreach($kelompok->anggota as $i => $ang)
    <tr>
      <td>{{ $i + 1 }}</td>
      <td>
        {{ $ang->mahasiswa?->nama ?? '—' }}
        @if($ang->mahasiswa_id === $kelompok->ketua_mahasiswa_id)
          <span class="ketua-tag">Ketua</span>
        @endif
      </td>
      <td>{{ $ang->mahasiswa?->nim ?? '—' }}</td>
      <td>{{ $ang->topik ?? '—' }}</td>
      <td>
        @if($ang->status_submit === 'submitted')
          <span class="anggota-status-submitted">&#10003; Dikumpulkan</span>
        @else
          <span class="anggota-status-belum">Belum</span>
        @endif
      </td>
    </tr>
    @endforeach
  </tbody>
</table>

{{-- ── Konten per Anggota ── --}}
<div class="section-title">Konten Tugas Masing-masing Anggota</div>

@foreach($kelompok->anggota as $ang)
<div class="anggota-block">
  <div class="anggota-header">
    <div>
      <div class="anggota-name">
        {{ $ang->mahasiswa?->nama ?? '—' }}
        @if($ang->mahasiswa_id === $kelompok->ketua_mahasiswa_id)
          <span class="ketua-tag">Ketua</span>
        @endif
      </div>
      @if($ang->topik)
        <div class="anggota-topik">{{ $ang->topik }}</div>
      @endif
    </div>
    <div>
      @if($ang->status_submit === 'submitted' && $ang->submitted_at)
        <span class="anggota-status-submitted">Dikumpulkan {{ $ang->submitted_at->format('d M Y') }}</span>
      @else
        <span class="anggota-status-belum">Belum dikumpulkan</span>
      @endif
    </div>
  </div>
  <div class="konten-box">
    @if($ang->konten)
      {!! $ang->konten !!}
    @else
      <span class="no-konten">Belum ada konten yang dikirim.</span>
    @endif
  </div>
</div>
@endforeach

{{-- ── Dokumen Final (Kompilasi Ketua) ── --}}
@if($konten)
<div class="section-title">Dokumen Final (Kompilasi Ketua)</div>
<div class="final-box">
  {!! $konten !!}
</div>
@endif

<div class="pdf-footer">
  Dokumen ini dibuat secara otomatis oleh sistem EduLearn &bull; {{ now()->format('d M Y, H:i') }}
</div>

</body>
</html>
