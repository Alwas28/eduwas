@extends('layouts.instruktur')
@section('title', 'Kelas Saya')
@section('page-title', 'Kelas Saya')

@push('styles')
<style>
.tab-active  { background:var(--ac); color:#fff; }
.tab-inactive{ color:var(--muted); }
.tab-inactive:hover { color:var(--text); }

.kelas-card { transition: border-color .18s, transform .18s, box-shadow .18s; }
.kelas-card:hover {
  border-color: var(--ac) !important;
  transform: translateY(-2px);
  box-shadow: 0 8px 28px rgba(var(--ac-rgb), .12);
}

.feature-chip {
  display:inline-flex; align-items:center; gap:5px;
  padding:4px 10px; border-radius:99px; font-size:11px; font-weight:600;
  background:var(--surface2); color:var(--muted);
  border:1px solid var(--border);
}

.progress-bar-fill {
  height:100%; border-radius:99px;
  background: linear-gradient(90deg, var(--ac), var(--ac2));
  transition: width .4s ease;
}
</style>
@endpush

@section('content')
<div class="space-y-5">

  {{-- ── HEADER ──────────────────────────────────────────────── --}}
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 animate-fadeUp">
    <div>
      <h2 class="font-display font-bold text-[20px]" style="color:var(--text)">Kelas yang Diampu</h2>
      <p class="text-[13px] mt-0.5" style="color:var(--muted)">
        Daftar seluruh kelas yang Anda ampu di EduLearn
      </p>
    </div>
  </div>

  {{-- ── STATS ────────────────────────────────────────────────── --}}
  <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-5 gap-3">

    <div class="rounded-2xl border p-4 flex items-center gap-3 animate-fadeUp d1" style="background:var(--surface);border-color:var(--border)">
      <div class="a-bg-lt a-text w-10 h-10 rounded-xl grid place-items-center flex-shrink-0">
        <i class="fa-solid fa-chalkboard-user text-[14px]"></i>
      </div>
      <div class="min-w-0">
        <div class="font-display text-[24px] font-bold leading-none" style="color:var(--text)">{{ $stats['total'] }}</div>
        <div class="text-[11px] mt-0.5" style="color:var(--muted)">Total Kelas</div>
      </div>
    </div>

    <div class="rounded-2xl border p-4 flex items-center gap-3 animate-fadeUp d2" style="background:var(--surface);border-color:var(--border)">
      <div class="w-10 h-10 rounded-xl grid place-items-center flex-shrink-0" style="background:rgba(16,185,129,.14);color:#34d399">
        <i class="fa-solid fa-circle-play text-[14px]"></i>
      </div>
      <div class="min-w-0">
        <div class="font-display text-[24px] font-bold leading-none" style="color:var(--text)">{{ $stats['aktif'] }}</div>
        <div class="text-[11px] mt-0.5" style="color:var(--muted)">Kelas Aktif</div>
      </div>
    </div>

    <div class="rounded-2xl border p-4 flex items-center gap-3 animate-fadeUp d3" style="background:var(--surface);border-color:var(--border)">
      <div class="w-10 h-10 rounded-xl grid place-items-center flex-shrink-0" style="background:rgba(99,102,241,.14);color:#818cf8">
        <i class="fa-solid fa-users text-[14px]"></i>
      </div>
      <div class="min-w-0">
        <div class="font-display text-[24px] font-bold leading-none" style="color:var(--text)">{{ $stats['peserta'] }}</div>
        <div class="text-[11px] mt-0.5" style="color:var(--muted)">Total Peserta</div>
      </div>
    </div>

    <div class="rounded-2xl border p-4 flex items-center gap-3 animate-fadeUp d4" style="background:var(--surface);border-color:var(--border)">
      <div class="w-10 h-10 rounded-xl grid place-items-center flex-shrink-0" style="background:rgba(59,130,246,.14);color:#60a5fa">
        <i class="fa-solid fa-flag-checkered text-[14px]"></i>
      </div>
      <div class="min-w-0">
        <div class="font-display text-[24px] font-bold leading-none" style="color:var(--text)">{{ $stats['selesai'] }}</div>
        <div class="text-[11px] mt-0.5" style="color:var(--muted)">Selesai</div>
      </div>
    </div>

    <div class="rounded-2xl border p-4 flex items-center gap-3 animate-fadeUp d5 col-span-2 sm:col-span-1" style="background:var(--surface);border-color:var(--border)">
      <div class="w-10 h-10 rounded-xl grid place-items-center flex-shrink-0" style="background:rgba(248,113,113,.14);color:#f87171">
        <i class="fa-solid fa-ban text-[14px]"></i>
      </div>
      <div class="min-w-0">
        <div class="font-display text-[24px] font-bold leading-none" style="color:var(--text)">{{ $stats['dibatalkan'] }}</div>
        <div class="text-[11px] mt-0.5" style="color:var(--muted)">Dibatalkan</div>
      </div>
    </div>

  </div>

  {{-- ── FILTER BAR ───────────────────────────────────────────── --}}
  <div class="animate-fadeUp d2 flex flex-col sm:flex-row gap-3 items-start sm:items-center justify-between">

    {{-- Tab filter --}}
    <div class="flex items-center gap-1 p-1 rounded-xl" style="background:var(--surface);border:1px solid var(--border)">
      @php
        $tabs = [
          'semua'      => ['label' => 'Semua',      'count' => $stats['total']],
          'aktif'      => ['label' => 'Aktif',      'count' => $stats['aktif']],
          'selesai'    => ['label' => 'Selesai',    'count' => $stats['selesai']],
          'dibatalkan' => ['label' => 'Dibatalkan', 'count' => $stats['dibatalkan']],
        ];
      @endphp
      @foreach($tabs as $key => $tab)
      <button type="button" data-tab="{{ $key }}" onclick="setTab('{{ $key }}')"
        class="tab-btn px-3 py-1.5 rounded-lg text-[12.5px] font-semibold transition-all {{ $key === 'semua' ? 'tab-active' : 'tab-inactive' }}">
        {{ $tab['label'] }}
        @if($tab['count'] > 0)
          <span class="tab-count-{{ $key }} ml-1 text-[11px] opacity-75">({{ $tab['count'] }})</span>
        @endif
      </button>
      @endforeach
    </div>

    {{-- Periode filter --}}
    <div class="flex items-center gap-2">
      <i class="fa-solid fa-filter text-[12px]" style="color:var(--muted)"></i>
      <select id="filter-periode" onchange="filterKelas()"
        class="f-input text-[13px] py-2 pr-8" style="width:auto;min-width:180px">
        <option value="">Semua Periode</option>
        @foreach($periodeList as $p)
          <option value="{{ $p->id }}" {{ $periodeAktif && $p->id === $periodeAktif->id ? 'selected' : '' }}>
            {{ $p->nama }}{{ $p->status === 'Aktif' ? ' ★' : '' }}
          </option>
        @endforeach
      </select>
    </div>

  </div>

  {{-- ── EMPTY STATE (no kelas at all) ──────────────────────── --}}
  @if($kelas->isEmpty())
  <div class="animate-fadeUp d2 rounded-2xl border py-20 text-center" style="background:var(--surface);border-color:var(--border)">
    <div class="a-bg-lt a-text w-16 h-16 rounded-2xl grid place-items-center text-2xl mx-auto mb-4">
      <i class="fa-solid fa-chalkboard"></i>
    </div>
    <p class="font-display font-semibold text-[16px] mb-1" style="color:var(--text)">Belum Mengampu Kelas</p>
    <p class="text-[13px]" style="color:var(--muted)">Kelas yang ditugaskan kepada Anda akan muncul di sini.</p>
  </div>
  @else

  {{-- ── EMPTY FILTER RESULT ──────────────────────────────────── --}}
  <div id="empty-filter" class="hidden rounded-2xl border py-14 text-center" style="background:var(--surface);border-color:var(--border)">
    <div class="a-bg-lt a-text w-14 h-14 rounded-2xl grid place-items-center text-xl mx-auto mb-3">
      <i class="fa-solid fa-filter-circle-xmark"></i>
    </div>
    <p class="font-display font-semibold text-[15px] mb-1" style="color:var(--text)">Tidak Ada Hasil</p>
    <p class="text-[13px]" style="color:var(--muted)">Tidak ada kelas yang sesuai dengan filter yang dipilih.</p>
    <button onclick="resetFilter()" class="mt-3 text-[12.5px] font-semibold a-text hover:underline">
      Reset filter
    </button>
  </div>

  {{-- ── KELAS GRID ───────────────────────────────────────────── --}}
  <div id="kelas-grid" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">

    @foreach($kelas->sortByDesc(fn($k) => $k->periodeAkademik?->created_at) as $k)
    @php
      $mk      = $k->mataKuliah;
      $pa      = $k->periodeAkademik;
      $enr     = $k->enrollments_count ?? 0;
      $cap     = $k->kapasitas;
      $pct     = ($cap && $cap > 0) ? min(100, round($enr / $cap * 100)) : null;
      $isPAktif = $periodeAktif && $k->periode_akademik_id === $periodeAktif->id;

      $stCls = match($k->status) {
        'Aktif'      => 'bg-emerald-500/15 text-emerald-400',
        'Selesai'    => 'bg-blue-500/15 text-blue-400',
        'Dibatalkan' => 'bg-rose-500/15 text-rose-400',
        default      => 'bg-slate-500/15 text-slate-400',
      };
      $stIcon = match($k->status) {
        'Aktif'      => 'fa-circle-play',
        'Selesai'    => 'fa-flag-checkered',
        'Dibatalkan' => 'fa-ban',
        default      => 'fa-circle',
      };

      // Fill color based on capacity usage
      $fillColor = 'var(--ac)';
      if ($pct !== null) {
        if ($pct >= 90) $fillColor = '#f87171';
        elseif ($pct >= 70) $fillColor = '#fb923c';
      }
    @endphp

    <div class="kelas-card animate-fadeUp d2 rounded-2xl border overflow-hidden flex flex-col"
      style="background:var(--surface);border-color:var(--border)"
      data-status="{{ strtolower($k->status) }}"
      data-periode="{{ $k->periode_akademik_id }}">

      {{-- Top accent bar + kode + periode badge --}}
      <div class="h-1 a-grad"></div>
      <div class="px-5 pt-4 pb-0 flex items-start justify-between gap-2">
        <div class="flex items-center gap-2 flex-wrap">
          <span class="font-mono font-bold text-[12px] px-2.5 py-1 rounded-lg a-bg-lt a-text">
            {{ $k->kode_display }}
          </span>
          @if($isPAktif)
          <span class="text-[10px] font-bold px-2 py-0.5 rounded-full" style="background:rgba(16,185,129,.12);color:#10b981">
            <i class="fa-solid fa-circle text-[6px] mr-0.5"></i>Periode Aktif
          </span>
          @endif
        </div>
        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold flex-shrink-0 {{ $stCls }}">
          <i class="fa-solid {{ $stIcon }} text-[9px]"></i>{{ $k->status }}
        </span>
      </div>

      <div class="px-5 py-4 flex flex-col flex-1 gap-3">

        {{-- Nama mata kuliah --}}
        <div>
          <h3 class="font-display font-bold text-[15.5px] leading-snug" style="color:var(--text)">
            {{ $mk?->nama ?? '—' }}
          </h3>
          @if($mk?->jurusan)
          <p class="text-[11.5px] mt-0.5" style="color:var(--muted)">
            {{ $mk->jurusan->nama }}
          </p>
          @endif
        </div>

        {{-- Info row: periode + SKS --}}
        <div class="flex items-center gap-4 text-[12px]" style="color:var(--muted)">
          <span class="flex items-center gap-1.5">
            <i class="fa-regular fa-calendar text-[11px]"></i>
            {{ $pa?->nama ?? '—' }}
          </span>
          @if($mk?->sks)
          <span class="flex items-center gap-1.5">
            <i class="fa-solid fa-book-open text-[11px]"></i>
            {{ $mk->sks }} SKS
          </span>
          @endif
        </div>

        {{-- Peserta / kapasitas --}}
        <div>
          <div class="flex items-center justify-between mb-1.5 text-[12px]">
            <span style="color:var(--muted)">
              <i class="fa-solid fa-users text-[11px] mr-1"></i>Peserta
            </span>
            <span class="font-semibold" style="color:var(--text)">
              {{ $enr }}{{ $cap ? ' / ' . $cap : '' }}
              @if($pct !== null)
                <span class="text-[11px] font-normal ml-1" style="color:var(--muted)">({{ $pct }}%)</span>
              @endif
            </span>
          </div>
          @if($pct !== null)
          <div class="h-1.5 rounded-full overflow-hidden" style="background:var(--surface2)">
            <div class="progress-bar-fill" style="width:{{ $pct }}%;background:{{ $fillColor }}"></div>
          </div>
          @else
          <div class="h-1.5 rounded-full" style="background:var(--surface2)">
            <div class="progress-bar-fill a-grad" style="width:0%"></div>
          </div>
          @endif
        </div>

        {{-- Divider --}}
        <div style="border-top:1px solid var(--border)"></div>

        {{-- Feature chips (coming soon) --}}
        <div class="flex flex-wrap gap-1.5">
          <span class="feature-chip">
            <i class="fa-solid fa-folder-open text-[10px]"></i>Materi
            <span class="text-[9px] font-bold opacity-60">Segera</span>
          </span>
          <span class="feature-chip">
            <i class="fa-solid fa-file-lines text-[10px]"></i>Tugas
            <span class="text-[9px] font-bold opacity-60">Segera</span>
          </span>
          <span class="feature-chip">
            <i class="fa-solid fa-pen-to-square text-[10px]"></i>Ujian
            <span class="text-[9px] font-bold opacity-60">Segera</span>
          </span>
        </div>

        {{-- Action row --}}
        <div class="flex items-center gap-2 mt-auto pt-1">
          <a href="{{ route('instruktur.kelas.peserta', $k->id) }}"
            class="flex-1 flex items-center justify-center gap-1.5 py-2 rounded-xl text-[12px] font-semibold border transition-colors"
            style="border-color:var(--border);color:var(--sub)"
            onmouseover="this.style.borderColor='var(--ac)';this.style.color='var(--ac)'"
            onmouseout="this.style.borderColor='var(--border)';this.style.color='var(--sub)'">
            <i class="fa-solid fa-users text-[10px]"></i>Peserta
          </a>
          <a href="{{ route('coming.soon') }}"
            class="flex-1 flex items-center justify-center gap-1.5 py-2 rounded-xl text-[12px] font-semibold text-white a-grad transition-opacity"
            onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
            <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>Kelola
          </a>
        </div>

      </div>
    </div>
    @endforeach

  </div>
  @endif

</div>
@endsection

@push('scripts')
<script>
const cards     = Array.from(document.querySelectorAll('.kelas-card'));
const emptyEl   = document.getElementById('empty-filter');
let currentTab  = 'semua';

// Pre-select active periode on load
document.addEventListener('DOMContentLoaded', () => filterKelas());

function setTab(tab) {
  currentTab = tab;
  document.querySelectorAll('.tab-btn').forEach(btn => {
    const active = btn.dataset.tab === tab;
    btn.classList.toggle('tab-active', active);
    btn.classList.toggle('tab-inactive', !active);
  });
  filterKelas();
}

function filterKelas() {
  const periode = document.getElementById('filter-periode')?.value ?? '';
  let visible = 0;
  cards.forEach(card => {
    const statusMatch  = currentTab === 'semua' || card.dataset.status === currentTab;
    const periodeMatch = !periode || card.dataset.periode === periode;
    const show = statusMatch && periodeMatch;
    card.classList.toggle('hidden', !show);
    if (show) visible++;
  });
  if (emptyEl) emptyEl.classList.toggle('hidden', visible > 0);
}

function resetFilter() {
  document.getElementById('filter-periode').value = '';
  setTab('semua');
}
</script>
@endpush
