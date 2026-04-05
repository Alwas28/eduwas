@extends('layouts.instruktur')
@section('title', 'Rekap Aktivitas — ' . $pokokBahasan->judul)
@section('page-title', 'Rekap Aktivitas')

@push('styles')
<style>
/* ── Cards ── */
.page-card { background:var(--surface); border:1px solid var(--border); border-radius:18px; overflow:hidden; }
.card-head  { padding:14px 18px; border-bottom:1px solid var(--border); display:flex; align-items:center; justify-content:space-between; gap:10px; }
.card-body  { padding:16px 18px; }

/* ── Stat chips ── */
.stat-box { padding:16px 20px; border-radius:14px; border:1px solid var(--border); background:var(--surface2); }

/* ── Tipe icons ── */
.tipe-icon { width:30px; height:30px; border-radius:9px; display:grid; place-items:center; font-size:12px; flex-shrink:0; }
.tc-dokumen { background:rgba(59,130,246,.15);  color:#60a5fa; }
.tc-video   { background:rgba(244,63,94,.15);   color:#fb7185; }
.tc-link    { background:rgba(139,92,246,.15);  color:#a78bfa; }
.tc-teks    { background:rgba(245,158,11,.15);  color:#fbbf24; }

/* ── Materi rekap row ── */
.materi-rekap { border:1px solid var(--border); border-radius:14px; overflow:hidden; margin-bottom:10px; }
.materi-rekap-head { display:flex; align-items:center; gap:12px; padding:13px 16px; background:var(--surface2); cursor:pointer; user-select:none; }
.materi-rekap-head:hover { background:var(--surface); }
.materi-rekap-body { display:none; border-top:1px solid var(--border); }
.materi-rekap.open .materi-rekap-body { display:block; }
.materi-rekap.open .rekap-chevron { transform:rotate(180deg); }
.rekap-chevron { transition:transform .2s; color:var(--muted); font-size:11px; flex-shrink:0; }

/* ── Student table ── */
.student-table { width:100%; border-collapse:collapse; font-size:12px; }
.student-table th { padding:8px 14px; text-align:left; font-size:10px; font-weight:600; text-transform:uppercase; letter-spacing:.05em; color:var(--muted); background:var(--surface2); border-bottom:1px solid var(--border); }
.student-table td { padding:9px 14px; border-bottom:1px solid var(--border); color:var(--text); }
.student-table tr:last-child td { border-bottom:none; }
.student-table tbody tr:hover td { background:var(--surface2); }

/* ── Empty state ── */
.empty-state { display:flex; flex-direction:column; align-items:center; justify-content:center; padding:48px 24px; gap:10px; }

/* ── Progress bar ── */
.progress-bar { height:5px; border-radius:3px; background:var(--border); overflow:hidden; margin-top:4px; }
.progress-fill { height:100%; border-radius:3px; background:var(--ac); transition:width .4s; }

/* ── Rangkuman grading ── */
.rkm-card { border:1px solid var(--border); border-radius:14px; overflow:hidden; margin-bottom:10px; }
.rkm-head { display:flex; align-items:flex-start; gap:12px; padding:13px 16px; cursor:pointer; user-select:none; }
.rkm-head:hover { background:var(--surface2); }
.rkm-body { display:none; border-top:1px solid var(--border); padding:14px 16px; }
.rkm-card.open .rkm-body { display:block; }
.rkm-card.open .rkm-chevron { transform:rotate(180deg); }
.rkm-chevron { transition:transform .2s; color:var(--muted); font-size:11px; flex-shrink:0; margin-top:3px; }
.nilai-input {
  width:64px; text-align:center; padding:5px 8px; border-radius:8px;
  border:1px solid var(--border); background:var(--surface2); color:var(--text);
  font-size:13px; font-weight:700; outline:none; transition:border-color .2s;
}
.nilai-input:focus { border-color:rgba(var(--ac-rgb),.6); }
.catatan-input {
  width:100%; resize:none; padding:8px 11px; border-radius:10px;
  border:1px solid var(--border); background:var(--surface2); color:var(--text);
  font-size:12px; line-height:1.5; outline:none; transition:border-color .2s;
  font-family:inherit; min-height:60px; max-height:160px;
}
.catatan-input:focus { border-color:rgba(var(--ac-rgb),.6); }
.nilai-badge {
  display:inline-flex; align-items:center; justify-content:center;
  min-width:36px; height:22px; border-radius:6px; font-size:11px; font-weight:700; padding:0 6px;
}
.nilai-badge.graded   { background:rgba(16,185,129,.15); color:#10b981; }
.nilai-badge.ungraded { background:rgba(100,116,139,.1); color:var(--muted); }

</style>
@endpush

@section('content')
<div class="space-y-5 animate-fadeUp">

  {{-- Breadcrumb --}}
  <div class="flex items-center gap-2 text-[12px]" style="color:var(--muted)">
    <a href="{{ route('instruktur.materi.index', ['mk_id' => $mataKuliah->id]) }}" class="a-text hover:underline">Materi Ajar</a>
    <i class="fa-solid fa-chevron-right text-[10px]"></i>
    <a href="{{ route('instruktur.pokok-bahasan.materi', $pokokBahasan->id) }}" class="a-text hover:underline">{{ $mataKuliah->kode }}</a>
    <i class="fa-solid fa-chevron-right text-[10px]"></i>
    <a href="{{ route('instruktur.pokok-bahasan.materi', $pokokBahasan->id) }}" class="a-text hover:underline">Pertemuan {{ $pokokBahasan->pertemuan }}</a>
    <i class="fa-solid fa-chevron-right text-[10px]"></i>
    <span style="color:var(--text)">Rekap Aktivitas</span>
  </div>

  {{-- Header --}}
  <div class="flex items-start justify-between gap-4 flex-wrap">
    <div class="flex items-start gap-4">
      <div class="w-11 h-11 rounded-xl grid place-items-center font-display font-bold text-[17px] a-bg-lt a-text flex-shrink-0">
        {{ $pokokBahasan->pertemuan }}
      </div>
      <div>
        <h2 class="font-display font-bold text-[19px]" style="color:var(--text)">{{ $pokokBahasan->judul }}</h2>
        <div class="flex items-center gap-3 mt-1 text-[11px]" style="color:var(--muted)">
          <span><i class="fa-solid fa-book-open mr-1"></i>{{ $mataKuliah->kode }} — {{ $mataKuliah->nama }}</span>
          <span><i class="fa-solid fa-layer-group mr-1"></i>{{ $materiList->count() }} materi</span>
        </div>
      </div>
    </div>
    <a href="{{ route('instruktur.pokok-bahasan.materi', $pokokBahasan->id) }}"
       class="flex items-center gap-1.5 px-3 py-2 rounded-xl text-[12px] font-semibold border hover:opacity-80 transition-opacity"
       style="border-color:var(--border);color:var(--muted)">
      <i class="fa-solid fa-arrow-left text-[10px]"></i>Kembali
    </a>
  </div>

  {{-- Summary stats --}}
  <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
    @php
      $totalAksesPerMateri = $materiList->map(fn($m) => $m->akses->sum('jumlah_akses'));
      $materiTeraksed      = $materiList->filter(fn($m) => $m->akses->isNotEmpty())->count();
    @endphp
    <div class="stat-box">
      <div class="text-[11px]" style="color:var(--muted)">Pengakses Unik</div>
      <div class="font-display font-bold text-[26px] mt-1" style="color:var(--text)">{{ $totalPengakses }}</div>
      <div class="text-[10px] mt-0.5" style="color:var(--muted)">mahasiswa berbeda</div>
    </div>
    <div class="stat-box">
      <div class="text-[11px]" style="color:var(--muted)">Total Akses</div>
      <div class="font-display font-bold text-[26px] mt-1" style="color:var(--text)">{{ $totalAkses }}</div>
      <div class="text-[10px] mt-0.5" style="color:var(--muted)">kali dibuka</div>
    </div>
    <div class="stat-box">
      <div class="text-[11px]" style="color:var(--muted)">Materi Diakses</div>
      <div class="font-display font-bold text-[26px] mt-1" style="color:var(--text)">{{ $materiTeraksed }}</div>
      <div class="text-[10px] mt-0.5" style="color:var(--muted)">dari {{ $materiList->count() }} materi</div>
    </div>
    <div class="stat-box">
      <div class="text-[11px]" style="color:var(--muted)">Rata-rata per Materi</div>
      <div class="font-display font-bold text-[26px] mt-1" style="color:var(--text)">
        {{ $materiList->count() ? round($totalAkses / $materiList->count(), 1) : 0 }}
      </div>
      <div class="text-[10px] mt-0.5" style="color:var(--muted)">akses / materi</div>
    </div>
  </div>

  {{-- Avg progress per materi (if any data) --}}
  @php
    $allAkses = $materiList->flatMap(fn($m) => $m->akses);
    $avgProg  = $allAkses->isNotEmpty() ? round($allAkses->avg('progress')) : 0;
  @endphp
  @if($allAkses->isNotEmpty())
  <div class="grid grid-cols-1 gap-4">
    <div class="stat-box flex items-center gap-4">
      <div class="flex-1">
        <div class="text-[11px] mb-1" style="color:var(--muted)">Rata-rata Progress Baca</div>
        <div class="progress-bar" style="height:8px;border-radius:4px">
          <div class="progress-fill" style="height:8px;border-radius:4px;width:{{ $avgProg }}%"></div>
        </div>
      </div>
      <div class="font-display font-bold text-[28px] flex-shrink-0 {{ $avgProg >= 100 ? 'text-emerald-400' : 'a-text' }}">
        {{ $avgProg }}%
      </div>
    </div>
  </div>
  @endif

  {{-- Per-materi rekap --}}
  <div class="page-card">
    <div class="card-head">
      <div>
        <div class="text-[13px] font-semibold" style="color:var(--text)">Detail per Materi</div>
        <div class="text-[11px] mt-0.5" style="color:var(--muted)">Klik baris untuk melihat detail mahasiswa</div>
      </div>
      <button onclick="toggleAll()" id="btn-toggle-all"
              class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[11px] font-semibold border hover:opacity-80 transition-opacity"
              style="border-color:var(--border);color:var(--muted)">
        <i class="fa-solid fa-expand-alt"></i>
        <span>Buka Semua</span>
      </button>
    </div>
    <div class="card-body">
      @if($materiList->isEmpty())
        <div class="empty-state" style="color:var(--muted)">
          <i class="fa-solid fa-inbox text-[30px] opacity-30"></i>
          <div class="text-[13px] font-semibold" style="color:var(--text)">Belum ada materi</div>
        </div>
      @else
        @php $maxAkses = $materiList->max(fn($m) => $m->akses->sum('jumlah_akses')) ?: 1; @endphp
        @foreach($materiList as $m)
          @php
            $unikCount  = $m->akses->unique('user_id')->count();
            $totalCount = $m->akses->sum('jumlah_akses');
            $pct        = round(($totalCount / $maxAkses) * 100);
            $tipeClass  = match($m->tipe) { 'dokumen'=>'tc-dokumen','video'=>'tc-video','link'=>'tc-link','teks'=>'tc-teks',default=>'a-bg-lt a-text' };
          @endphp
          <div class="materi-rekap" id="mr-{{ $m->id }}">
            <div class="materi-rekap-head" onclick="toggleRekap('{{ $m->id }}')">
              <div class="tipe-icon {{ $tipeClass }}">
                <i class="fa-solid {{ $m->tipeIcon() }}"></i>
              </div>
              <div class="flex-1 min-w-0">
                <div class="text-[13px] font-semibold" style="color:var(--text)">{{ $m->judul }}</div>
                <div class="text-[10px] mt-0.5" style="color:var(--muted)">{{ $m->tipeLabel() }}</div>
                <div class="progress-bar" style="max-width:200px">
                  <div class="progress-fill" style="width:{{ $pct }}%"></div>
                </div>
              </div>
              <div class="text-right flex-shrink-0 ml-2">
                <div class="text-[15px] font-display font-bold" style="color:var(--text)">{{ $unikCount }}</div>
                <div class="text-[10px]" style="color:var(--muted)">mahasiswa</div>
              </div>
              <div class="text-right flex-shrink-0 ml-4 hidden sm:block">
                <div class="text-[15px] font-display font-bold" style="color:var(--text)">{{ $totalCount }}</div>
                <div class="text-[10px]" style="color:var(--muted)">akses</div>
              </div>
              <div class="ml-3 flex-shrink-0">
                @if($m->status === 'Aktif')
                  <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-emerald-500/15 text-emerald-400">Aktif</span>
                @else
                  <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-amber-500/15 text-amber-400">Draft</span>
                @endif
              </div>
              <i class="fa-solid fa-chevron-down rekap-chevron ml-2"></i>
            </div>
            <div class="materi-rekap-body">
              @if($m->akses->isEmpty())
                <div class="py-5 text-center text-[12px]" style="color:var(--muted)">
                  <i class="fa-solid fa-eye-slash opacity-40 mr-1.5"></i>Belum ada mahasiswa yang mengakses materi ini.
                </div>
              @else
                <table class="student-table">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Mahasiswa</th>
                      <th>Progress</th>
                      <th>Waktu Aktif</th>
                      <th>Jumlah Akses</th>
                      <th>Pertama Diakses</th>
                      <th>Terakhir Diakses</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($m->akses->unique('user_id')->values() as $i => $akses)
                    @php
                      $prog      = $akses->progress ?? 0;
                      $durasi    = $akses->durasi_detik ?? 0;
                      // Flag: progress tinggi tapi waktu sangat singkat (< 20 detik)
                      $suspicious = $prog >= 80 && $durasi > 0 && $durasi < 20;
                    @endphp
                    <tr>
                      <td class="text-[11px]" style="color:var(--muted)">{{ $i + 1 }}</td>
                      <td>
                        <div class="font-semibold text-[12px]">{{ $akses->user?->name ?? '—' }}</div>
                      </td>
                      <td style="min-width:110px">
                        <div class="flex items-center gap-2">
                          <div class="progress-bar flex-1" style="max-width:70px">
                            <div class="progress-fill" style="width:{{ $prog }}%"></div>
                          </div>
                          <span class="text-[11px] font-semibold {{ $prog >= 100 ? 'text-emerald-400' : '' }}"
                                style="{{ $prog < 100 ? 'color:var(--muted)' : '' }}">
                            {{ $prog }}%
                          </span>
                        </div>
                      </td>
                      <td style="min-width:120px">
                        <div class="flex items-center gap-1.5">
                          <span class="text-[12px] font-semibold {{ $suspicious ? '' : '' }}"
                                style="{{ $durasi > 0 ? 'color:var(--text)' : 'color:var(--muted)' }}">
                            {{ $akses->durasiHuman() }}
                          </span>
                          @if($suspicious)
                            <span title="Progress tinggi tapi waktu baca sangat singkat — perlu dicek"
                                  class="inline-flex items-center gap-1 text-[10px] font-bold px-1.5 py-0.5 rounded-full"
                                  style="background:rgba(245,158,11,.15);color:#fbbf24">
                              <i class="fa-solid fa-triangle-exclamation"></i>Cepat
                            </span>
                          @elseif($durasi > 0 && $prog >= 80)
                            <span class="text-[10px]" style="color:#10b981">
                              <i class="fa-solid fa-check"></i>
                            </span>
                          @endif
                        </div>
                      </td>
                      <td>
                        <span class="font-semibold">{{ $akses->jumlah_akses }}x</span>
                      </td>
                      <td style="color:var(--muted)">
                        {{ $akses->pertama_diakses_at?->format('d M Y H:i') ?? '—' }}
                      </td>
                      <td style="color:var(--muted)">
                        {{ $akses->terakhir_diakses_at?->diffForHumans() ?? '—' }}
                      </td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              @endif
            </div>
          </div>
        @endforeach
      @endif
    </div>
  </div>

  {{-- ── Rangkuman Mahasiswa ──────────────────────────────────── --}}
  @if($pokokBahasan->rangkuman_aktif || $rangkumanList->isNotEmpty())
  <div class="page-card">
    <div class="card-head">
      <div>
        <div class="text-[13px] font-semibold" style="color:var(--text)">
          Rangkuman Mahasiswa
          @if(!$pokokBahasan->rangkuman_aktif)
            <span class="ml-2 text-[10px] font-semibold px-2 py-0.5 rounded-full bg-amber-500/15 text-amber-400">Nonaktif</span>
          @endif
        </div>
        <div class="text-[11px] mt-0.5" style="color:var(--muted)">
          {{ $rangkumanList->count() }} rangkuman diterima · {{ $rangkumanList->whereNotNull('nilai')->count() }} sudah dinilai
        </div>
      </div>
    </div>
    <div class="card-body">
      @if($rangkumanList->isEmpty())
        <div class="empty-state" style="color:var(--muted)">
          <i class="fa-solid fa-pen-to-square text-[28px] opacity-30"></i>
          <div class="text-[13px] font-semibold" style="color:var(--text)">Belum ada rangkuman</div>
          <p class="text-[12px] text-center">Mahasiswa belum mengumpulkan rangkuman untuk pertemuan ini.</p>
        </div>
      @else
        @foreach($rangkumanList as $rkm)
          @php $isGraded = $rkm->nilai !== null; @endphp
          <div class="rkm-card" id="rkm-{{ $rkm->id }}">
            <div class="rkm-head" onclick="toggleRkm({{ $rkm->id }})">
              <div class="w-8 h-8 rounded-lg grid place-items-center flex-shrink-0 text-[11px] font-bold"
                   style="background:rgba(99,102,241,.12);color:#818cf8">
                <i class="fa-solid fa-pen-to-square"></i>
              </div>
              <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 flex-wrap">
                  <span class="text-[13px] font-semibold" style="color:var(--text)">
                    {{ $rkm->user?->name ?? '—' }}
                  </span>
                  <span class="nilai-badge {{ $isGraded ? 'graded' : 'ungraded' }}" id="rkm-badge-{{ $rkm->id }}">
                    {{ $isGraded ? $rkm->nilai : '—' }}
                  </span>
                </div>
                <div class="text-[11px] mt-0.5 line-clamp-1" style="color:var(--muted)">
                  {{ Str::limit($rkm->isi, 100) }}
                </div>
                <div class="text-[10px] mt-0.5" style="color:var(--muted)">
                  Dikirim {{ $rkm->updated_at->diffForHumans() }}
                  @if($rkm->catatan)
                    · <span style="color:#818cf8"><i class="fa-solid fa-comment-dots mr-0.5"></i>Ada catatan</span>
                  @endif
                </div>
              </div>
              <i class="fa-solid fa-chevron-down rkm-chevron ml-2"></i>
            </div>
            <div class="rkm-body">
              {{-- Rangkuman text --}}
              <div class="text-[13px] leading-relaxed mb-4 p-3 rounded-xl"
                   style="background:var(--surface2);color:var(--text);white-space:pre-wrap;word-break:break-word">{{ $rkm->isi }}</div>
              {{-- Grading form --}}
              <div class="flex flex-col gap-3">
                <div class="flex items-center gap-3">
                  <label class="text-[12px] font-semibold flex-shrink-0" style="color:var(--text)">Nilai (0–100)</label>
                  <input type="number" min="0" max="100" step="1"
                         class="nilai-input" id="nilai-inp-{{ $rkm->id }}"
                         value="{{ $rkm->nilai ?? '' }}" placeholder="—">
                  <span class="text-[11px]" style="color:var(--muted)">/100</span>
                </div>
                <div>
                  <label class="text-[12px] font-semibold block mb-1.5" style="color:var(--text)">Catatan untuk Mahasiswa <span class="font-normal" style="color:var(--muted)">(opsional)</span></label>
                  <textarea class="catatan-input" id="catatan-inp-{{ $rkm->id }}"
                            placeholder="Berikan masukan atau komentar untuk rangkuman ini…">{{ $rkm->catatan ?? '' }}</textarea>
                </div>
                <div class="flex items-center justify-between gap-3">
                  <span class="text-[11px] hidden" style="color:#10b981" id="rkm-saved-{{ $rkm->id }}">
                    <i class="fa-solid fa-check mr-1"></i>Tersimpan
                  </span>
                  <button onclick="saveGrade({{ $rkm->id }})" id="rkm-btn-{{ $rkm->id }}"
                          class="flex items-center gap-1.5 px-4 py-2 rounded-xl text-[12px] font-semibold text-white ml-auto"
                          style="background:var(--ac)">
                    <i class="fa-solid fa-floppy-disk"></i>Simpan Nilai
                  </button>
                </div>
              </div>
            </div>
          </div>
        @endforeach
      @endif
    </div>
  </div>
  @endif

</div>

@endsection

@push('scripts')
<script>
const CSRF = '{{ csrf_token() }}';

function toggleRekap(id) {
  document.getElementById('mr-'+id)?.classList.toggle('open');
}

let allOpen = false;
function toggleAll() {
  allOpen = !allOpen;
  document.querySelectorAll('.materi-rekap').forEach(el => el.classList.toggle('open', allOpen));
  const btn = document.getElementById('btn-toggle-all');
  btn.querySelector('span').textContent = allOpen ? 'Tutup Semua' : 'Buka Semua';
  btn.querySelector('i').className = allOpen ? 'fa-solid fa-compress-alt' : 'fa-solid fa-expand-alt';
}

function toggleRkm(id) {
  document.getElementById('rkm-'+id)?.classList.toggle('open');
}

async function saveGrade(id) {
  const nilaiInp   = document.getElementById('nilai-inp-'+id);
  const catatanInp = document.getElementById('catatan-inp-'+id);
  const btn        = document.getElementById('rkm-btn-'+id);
  const savedEl    = document.getElementById('rkm-saved-'+id);
  const badge      = document.getElementById('rkm-badge-'+id);

  const nilaiVal = nilaiInp?.value.trim();
  const nilai = nilaiVal === '' ? null : parseInt(nilaiVal);
  if (nilai !== null && (isNaN(nilai) || nilai < 0 || nilai > 100)) {
    nilaiInp?.focus();
    return;
  }

  if (btn) { btn.disabled = true; btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1"></i>Menyimpan…'; }

  try {
    const r = await fetch(`/instruktur/pb-rangkuman/${id}/grade`, {
      method: 'PATCH',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
      body: JSON.stringify({ nilai, catatan: catatanInp?.value || null }),
    });
    const j = await r.json();
    if (!r.ok) throw new Error(j.message || 'Gagal menyimpan');

    if (badge) {
      badge.textContent = nilai !== null ? nilai : '—';
      badge.className = 'nilai-badge ' + (nilai !== null ? 'graded' : 'ungraded');
    }
    if (savedEl) { savedEl.classList.remove('hidden'); setTimeout(() => savedEl.classList.add('hidden'), 3000); }
  } catch(e) {
    alert(e.message || 'Gagal menyimpan nilai.');
  } finally {
    if (btn) { btn.disabled = false; btn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i>Simpan Nilai'; }
  }
}
</script>
@endpush
