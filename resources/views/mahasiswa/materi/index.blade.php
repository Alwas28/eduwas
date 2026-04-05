@extends('layouts.mahasiswa')
@section('title', 'Materi Saya')
@section('page-title', 'Materi')

@push('styles')
<style>
/* ── Stats ── */
.stat-card {
  background:var(--surface); border:1px solid var(--border); border-radius:16px;
  padding:16px 20px; display:flex; align-items:center; gap:14px;
}
.stat-icon {
  width:42px; height:42px; border-radius:12px; display:grid; place-items:center;
  font-size:16px; flex-shrink:0;
}

/* ── MK card ── */
.mk-card {
  background:var(--surface); border:1px solid var(--border); border-radius:18px;
  overflow:hidden; transition:border-color .15s, transform .15s;
  text-decoration:none; display:block;
}
.mk-card:hover { border-color:rgba(var(--ac-rgb),.5); transform:translateY(-2px); }
.mk-card-top { height:4px; }
.mk-card-body { padding:18px 20px; display:flex; flex-direction:column; gap:14px; }

/* ── Progress bar ── */
.prog-bar { height:5px; border-radius:3px; background:var(--border); overflow:hidden; }
.prog-fill { height:100%; border-radius:3px; background:var(--ac); transition:width .5s ease; }

/* ── Filter bar ── */
.filter-bar { display:flex; align-items:center; gap:8px; flex-wrap:wrap; }
.f-tab {
  padding:6px 14px; border-radius:10px; font-size:12.5px; font-weight:600; cursor:pointer; border:none;
  transition:background .15s, color .15s;
}
.f-tab.active   { background:var(--ac); color:#fff; }
.f-tab.inactive { background:var(--surface); color:var(--muted); border:1px solid var(--border); }
.f-tab.inactive:hover { color:var(--text); }

.search-wrap { position:relative; flex:1; min-width:160px; max-width:280px; }
.search-wrap input {
  width:100%; padding:8px 12px 8px 34px;
  border-radius:10px; border:1px solid var(--border); background:var(--surface);
  color:var(--text); font-size:13px; outline:none;
}
.search-wrap input:focus { border-color:var(--ac); }
.search-wrap .s-icon {
  position:absolute; left:10px; top:50%; transform:translateY(-50%);
  font-size:12px; color:var(--muted); pointer-events:none;
}
</style>
@endpush

@section('content')
@php
$durH = (function() use ($totalDurasi): string {
    $d = (int) $totalDurasi;
    if ($d <= 0)  return '0 dtk';
    if ($d < 60)  return $d . ' dtk';
    $mnt = intdiv($d, 60);
    if ($mnt < 60) return $mnt . ' mnt';
    $jam = intdiv($mnt, 60); $mnt2 = $mnt % 60;
    return $jam . ' jam' . ($mnt2 > 0 ? ' ' . $mnt2 . ' mnt' : '');
})();
@endphp

<div class="space-y-5 animate-fadeUp">

  {{-- Stats row --}}
  <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
    <div class="stat-card">
      <div class="stat-icon a-bg-lt a-text"><i class="fa-solid fa-layer-group"></i></div>
      <div>
        <div class="font-display font-bold text-[22px]" style="color:var(--text)">{{ $totalMateri }}</div>
        <div class="text-[11px] mt-0.5" style="color:var(--muted)">Total Materi</div>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-icon" style="background:rgba(16,185,129,.12);color:#10b981"><i class="fa-solid fa-circle-check"></i></div>
      <div>
        <div class="font-display font-bold text-[22px]" style="color:var(--text)">{{ $totalSelesai }}</div>
        <div class="text-[11px] mt-0.5" style="color:var(--muted)">Selesai</div>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-icon" style="background:rgba(79,110,247,.12);color:#818cf8"><i class="fa-solid fa-chart-line"></i></div>
      <div>
        <div class="font-display font-bold text-[22px]" style="color:var(--text)">{{ $avgProgress }}%</div>
        <div class="text-[11px] mt-0.5" style="color:var(--muted)">Rata-rata Progress</div>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-icon" style="background:rgba(245,158,11,.12);color:#fbbf24"><i class="fa-solid fa-clock"></i></div>
      <div>
        <div class="font-display font-bold text-[22px]" style="color:var(--text)">{{ $durH }}</div>
        <div class="text-[11px] mt-0.5" style="color:var(--muted)">Total Waktu</div>
      </div>
    </div>
  </div>

  {{-- Filter bar --}}
  <div class="filter-bar">
    <button class="f-tab active"   data-filter="semua"   onclick="setFilter('semua')">Semua</button>
    <button class="f-tab inactive" data-filter="belum"   onclick="setFilter('belum')">Belum Mulai</button>
    <button class="f-tab inactive" data-filter="sedang"  onclick="setFilter('sedang')">Sedang</button>
    <button class="f-tab inactive" data-filter="selesai" onclick="setFilter('selesai')">Selesai</button>
    <div class="search-wrap ml-auto">
      <i class="fa-solid fa-magnifying-glass s-icon"></i>
      <input type="text" id="search-inp" placeholder="Cari mata kuliah…" oninput="applyFilter()">
    </div>
  </div>

  {{-- Empty state --}}
  @if($enrollments->isEmpty())
    <div class="rounded-2xl border py-16 text-center" style="background:var(--surface);border-color:var(--border)">
      <i class="fa-solid fa-book-open text-[32px] opacity-25 block mb-3" style="color:var(--muted)"></i>
      <div class="text-[14px] font-semibold mb-1" style="color:var(--text)">Belum ada materi</div>
      <p class="text-[12px]" style="color:var(--muted)">Anda belum terdaftar di kelas manapun.</p>
    </div>
  @else

  {{-- No result state --}}
  <div id="no-result" class="hidden rounded-2xl border py-12 text-center" style="background:var(--surface);border-color:var(--border)">
    <i class="fa-solid fa-filter-circle-xmark text-[28px] opacity-25 block mb-3" style="color:var(--muted)"></i>
    <p class="text-[13px]" style="color:var(--muted)">Tidak ada yang sesuai filter.</p>
  </div>

  {{-- MK cards grid --}}
  <div id="mk-grid" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
    @foreach($enrollments as $enrollment)
      @php
        $kelas   = $enrollment->kelas;
        $mk      = $kelas->mataKuliah;
        $periode = $kelas->periodeAkademik;
        $stats   = $kelasStats[$kelas->id] ?? ['total' => 0, 'selesai' => 0, 'avg' => 0];

        $filterStatus = $stats['avg'] >= 100 ? 'selesai'
            : ($stats['avg'] > 0 ? 'sedang' : 'belum');

        // Warna progress
        $progColor = $stats['avg'] >= 100 ? '#10b981'
            : ($stats['avg'] > 0 ? 'var(--ac)' : 'var(--border)');

        $statusLabel = match($enrollment->status) {
          'Aktif'   => ['Aktif',   'rgba(16,185,129,.12)', '#10b981'],
          'Lulus'   => ['Lulus',   'rgba(59,130,246,.12)', '#60a5fa'],
          'Dropout' => ['Dropout', 'rgba(239,68,68,.12)',  '#f87171'],
          default   => [$enrollment->status, 'var(--surface2)', 'var(--muted)'],
        };
      @endphp

      <a href="{{ route('mahasiswa.kelas.show', $kelas->id) }}"
         class="mk-card"
         data-status="{{ $filterStatus }}"
         data-title="{{ strtolower($mk?->nama ?? '') }}">

        {{-- Top gradient bar --}}
        <div class="mk-card-top a-grad"></div>

        <div class="mk-card-body">

          {{-- Top row --}}
          <div class="flex items-start justify-between gap-2">
            <div class="flex items-center gap-2 flex-wrap">
              <span class="font-mono font-bold text-[12px] px-2.5 py-1 rounded-lg a-bg-lt a-text">
                {{ $kelas->kodeDisplay }}
              </span>
              <span class="text-[10.5px] font-bold px-2 py-0.5 rounded-full"
                    style="background:{{ $statusLabel[1] }};color:{{ $statusLabel[2] }}">
                <i class="fa-solid fa-circle text-[7px] mr-0.5"></i>{{ $statusLabel[0] }}
              </span>
            </div>
          </div>

          {{-- Nama MK --}}
          <div>
            <h3 class="font-display font-bold text-[16px] leading-snug" style="color:var(--text)">
              {{ $mk?->nama ?? '—' }}
            </h3>
            <div class="flex items-center gap-2 mt-1.5 text-[12px]" style="color:var(--muted)">
              <span><i class="fa-solid fa-calendar-days mr-1"></i>{{ $periode?->nama ?? '—' }}</span>
              @if($mk?->sks)
                <span>•</span>
                <span>{{ $mk->sks }} SKS</span>
              @endif
            </div>
          </div>

          {{-- Instruktur --}}
          @if($kelas->instruktur->isNotEmpty())
            <div class="flex items-center gap-1.5 flex-wrap">
              @foreach($kelas->instruktur->take(2) as $ins)
                <div class="flex items-center gap-1.5">
                  <div class="w-5 h-5 rounded-full a-grad grid place-items-center text-[8px] text-white font-bold flex-shrink-0">
                    {{ strtoupper(substr($ins->nama, 0, 1)) }}
                  </div>
                  <span class="text-[12px] truncate" style="color:var(--sub)">{{ $ins->nama }}</span>
                </div>
              @endforeach
              @if($kelas->instruktur->count() > 2)
                <span class="text-[11px]" style="color:var(--muted)">+{{ $kelas->instruktur->count() - 2 }} lainnya</span>
              @endif
            </div>
          @endif

          <div style="border-top:1px solid var(--border)"></div>

          {{-- Progress --}}
          <div>
            <div class="flex items-center justify-between mb-2">
              <span class="text-[11px] font-semibold" style="color:var(--muted)">Progress</span>
              <div class="flex items-center gap-3 text-[11px]" style="color:var(--muted)">
                <span><i class="fa-solid fa-layer-group mr-1"></i>{{ $stats['total'] }} materi</span>
                <span class="font-bold" style="color:{{ $progColor }}">{{ $stats['avg'] }}%</span>
              </div>
            </div>
            <div class="prog-bar">
              <div class="prog-fill" style="width:{{ $stats['avg'] }}%;background:{{ $progColor }}"></div>
            </div>
            <div class="flex items-center justify-between mt-1.5 text-[11px]" style="color:var(--muted)">
              <span>{{ $stats['selesai'] }}/{{ $stats['total'] }} selesai</span>
              @if($stats['avg'] >= 100)
                <span style="color:#10b981"><i class="fa-solid fa-check mr-0.5"></i>Tuntas</span>
              @endif
            </div>
          </div>

        </div>
      </a>
    @endforeach
  </div>

  @endif

</div>
@endsection

@push('scripts')
<script>
let currentFilter = 'semua';

function setFilter(f) {
  currentFilter = f;
  document.querySelectorAll('.f-tab').forEach(btn => {
    const active = btn.dataset.filter === f;
    btn.classList.toggle('active', active);
    btn.classList.toggle('inactive', !active);
  });
  applyFilter();
}

function applyFilter() {
  const q     = document.getElementById('search-inp').value.trim().toLowerCase();
  const cards = document.querySelectorAll('#mk-grid .mk-card');
  let visible = 0;
  cards.forEach(card => {
    const statusOk = currentFilter === 'semua' || card.dataset.status === currentFilter;
    const searchOk = !q || card.dataset.title.includes(q);
    const show = statusOk && searchOk;
    card.classList.toggle('hidden', !show);
    if (show) visible++;
  });
  const noRes = document.getElementById('no-result');
  if (noRes) noRes.classList.toggle('hidden', visible > 0);
}
</script>
@endpush
