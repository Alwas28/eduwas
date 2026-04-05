@extends('layouts.instruktur')
@section('title', 'Profil Saya')
@section('page-title', 'Profil Saya')

@push('styles')
<style>
/* ── Layout ── */
.profile-grid { display:grid; grid-template-columns:280px 1fr; gap:20px; align-items:start; }
@media(max-width:900px){ .profile-grid { grid-template-columns:1fr; } }

/* ── Cards ── */
.s-card { background:var(--surface); border:1px solid var(--border); border-radius:16px; overflow:hidden; }
.s-head { padding:12px 16px; border-bottom:1px solid var(--border); display:flex; align-items:center; gap:10px; }
.s-body { padding:16px; }

/* ── Identity card ── */
.id-card { padding:20px; display:flex; flex-direction:column; align-items:center; text-align:center; gap:12px; }
.id-avatar { position:relative; width:80px; height:80px; }
.id-avatar img, .id-avatar .id-initials {
  width:80px; height:80px; border-radius:50%; object-fit:cover;
  border:3px solid var(--border); display:block;
}
.id-initials { display:flex; align-items:center; justify-content:center; font-size:28px; font-weight:700; font-family:var(--font-display,sans-serif); color:#fff; }
.id-avatar-btn {
  position:absolute; bottom:-2px; right:-2px;
  width:26px; height:26px; border-radius:50%; border:2px solid var(--surface);
  background:var(--ac); color:#fff; display:grid; place-items:center; font-size:10px;
  cursor:pointer; transition:transform .15s;
}
.id-avatar-btn:hover { transform:scale(1.1); }
.id-name { font-size:16px; font-weight:700; color:var(--text); line-height:1.3; }
.id-sub { font-size:12px; color:var(--muted); margin-top:2px; }
.id-badge { display:inline-flex; align-items:center; gap:5px; padding:3px 10px; border-radius:20px; font-size:11.5px; font-weight:600; }
.id-nidn { font-size:12px; font-weight:700; font-family:monospace; padding:3px 10px; border-radius:8px; }
.id-divider { width:100%; height:1px; background:var(--border); }
.id-action-row { display:flex; gap:8px; width:100%; }

/* ── Info rows ── */
.info-row { display:flex; align-items:flex-start; gap:10px; padding:9px 0; border-bottom:1px solid var(--border); }
.info-row:last-child { border-bottom:none; }
.info-icon { width:30px; height:30px; border-radius:8px; display:grid; place-items:center; font-size:11px; flex-shrink:0; }

/* ── Stats ── */
.stat-mini { border-radius:12px; border:1px solid var(--border); padding:12px 14px; display:flex; align-items:center; gap:10px; background:var(--surface); }
.stat-mini-icon { width:34px; height:34px; border-radius:10px; display:grid; place-items:center; font-size:12px; flex-shrink:0; }

/* ── Edit toggle ── */
.edit-btn { display:inline-flex; align-items:center; gap:5px; margin-left:auto; padding:5px 10px; border-radius:8px; font-size:12px; font-weight:600; border:1px solid var(--border); background:transparent; color:var(--sub); cursor:pointer; transition:all .15s; }
.edit-btn:hover { border-color:var(--ac); color:var(--ac); }
.edit-btn.active { background:var(--ac); border-color:var(--ac); color:#fff; }

/* ── Kelas table ── */
.kl-table { width:100%; border-collapse:collapse; }
.kl-table th { padding:9px 14px; text-align:left; font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:var(--muted); background:var(--surface2); }
.kl-table td { padding:10px 14px; border-bottom:1px solid var(--border); font-size:13px; vertical-align:middle; }
.kl-table tr:last-child td { border-bottom:none; }
.kl-table tbody tr:hover td { background:var(--surface2); }
</style>
@endpush

@section('content')
@php
  $user  = auth()->user();
  $kelas = $instruktur->kelas;
  $aktif = $kelas->where('status', 'Aktif');
  $totalPeserta = $kelas->sum(fn($k) => $k->enrollments_count ?? 0);

  $details = [
    ['fa-id-badge',       'NIDN',            $instruktur->nidn,               'a-bg-lt a-text'],
    ['fa-id-card',        'NIP',             $instruktur->nip,                'bg-blue-500/10 text-blue-400'],
    ['fa-venus-mars',     'Jenis Kelamin',   $instruktur->jenis_kelamin,      'bg-purple-500/10 text-purple-400'],
    ['fa-graduation-cap', 'Pendidikan',      $instruktur->pendidikan_terakhir,'bg-amber-500/10 text-amber-400'],
    ['fa-microscope',     'Bidang Keahlian', $instruktur->bidang_keahlian,    'bg-emerald-500/10 text-emerald-400'],
    ['fa-phone',          'No. HP',          $instruktur->no_hp,              'bg-rose-500/10 text-rose-400'],
    ['fa-envelope',       'Email',           $instruktur->email,              'bg-sky-500/10 text-sky-400'],
  ];

  $stBg  = $instruktur->status === 'Aktif' ? 'rgba(16,185,129,.15)' : 'rgba(239,68,68,.15)';
  $stTxt = $instruktur->status === 'Aktif' ? '#10b981' : '#ef4444';
@endphp

<div class="space-y-5 pt-8">

  {{-- Flash --}}
  @if(session('status') === 'profile-updated')
    <div class="px-4 py-3 rounded-xl flex items-center gap-2 text-[13px] font-medium"
      style="background:rgba(16,185,129,.1);color:#10b981;border:1px solid rgba(16,185,129,.2)">
      <i class="fa-solid fa-circle-check"></i> Profil berhasil diperbarui.
    </div>
  @endif
  @if(session('status') === 'avatar-updated')
    <div class="px-4 py-3 rounded-xl flex items-center gap-2 text-[13px] font-medium"
      style="background:rgba(16,185,129,.1);color:#10b981;border:1px solid rgba(16,185,129,.2)">
      <i class="fa-solid fa-circle-check"></i> Foto profil berhasil diperbarui.
    </div>
  @endif

  {{-- Hidden avatar form --}}
  <form method="POST" action="{{ route('instruktur.profile.avatar') }}" enctype="multipart/form-data" id="avatar-form" style="display:none">
    @csrf
    <input type="file" id="avatar-input" name="avatar"
      accept="image/jpg,image/jpeg,image/png,image/webp"
      onchange="previewAvatar(this)">
  </form>

  {{-- Main grid --}}
  <div class="profile-grid">

    {{-- ── LEFT COLUMN ── --}}
    <div class="space-y-4">

      {{-- Identity card --}}
      <div class="s-card">
        <div class="id-card">
          {{-- Avatar --}}
          <div class="id-avatar">
            @if($user->avatarUrl())
              <img id="avatar-img" src="{{ $user->avatarUrl() }}" alt="Foto">
            @else
              <div id="avatar-placeholder" class="id-initials a-grad">
                {{ strtoupper(substr($instruktur->nama, 0, 1)) }}
              </div>
              <img id="avatar-img" src="" alt="" style="display:none;width:80px;height:80px;border-radius:50%;object-fit:cover;border:3px solid var(--border);">
            @endif
            <label for="avatar-input" class="id-avatar-btn" title="Ubah Foto">
              <i class="fa-solid fa-camera"></i>
            </label>
          </div>

          {{-- Name & role --}}
          <div>
            <div class="id-name">{{ $instruktur->nama }}</div>
            <div class="id-sub">Instruktur</div>
          </div>

          {{-- NIDN badge --}}
          @if($instruktur->nidn || $instruktur->nip)
          <span class="id-nidn a-bg-lt a-text">{{ $instruktur->nidn ?? $instruktur->nip }}</span>
          @endif

          {{-- Status --}}
          <span class="id-badge" style="background:{{ $stBg }};color:{{ $stTxt }}">
            <i class="fa-solid fa-circle text-[7px]"></i>{{ $instruktur->status ?? 'Aktif' }}
          </span>

          {{-- Pending avatar banner --}}
          <div id="avatar-action" class="w-full px-3 py-2.5 rounded-xl items-center gap-2 text-[12px]"
            style="display:none;background:rgba(245,158,11,.1);border:1px solid rgba(245,158,11,.2)">
            <div class="flex items-center gap-2 mb-2" style="color:var(--text)">
              <i class="fa-solid fa-circle-exclamation text-amber-400"></i>
              <span class="font-medium">Foto baru dipilih</span>
            </div>
            <div class="flex gap-2">
              <button type="button" onclick="cancelAvatar()"
                class="flex-1 py-1.5 rounded-lg text-[11.5px] font-semibold border" style="border-color:var(--border);color:var(--sub)">
                Batal
              </button>
              <button type="button" onclick="document.getElementById('avatar-form').submit()"
                class="flex-1 py-1.5 rounded-lg text-[11.5px] font-semibold text-white a-grad">
                <i class="fa-solid fa-floppy-disk mr-1"></i>Simpan
              </button>
            </div>
          </div>
        </div>
      </div>

      {{-- Data Diri --}}
      <div class="s-card">
        <div class="s-head">
          <div class="a-bg-lt a-text w-8 h-8 rounded-xl grid place-items-center text-[12px]">
            <i class="fa-solid fa-id-card"></i>
          </div>
          <span class="font-display font-semibold text-[14px]" style="color:var(--text)">Data Diri</span>
          <button type="button" onclick="toggleEdit()" id="edit-toggle-btn" class="edit-btn">
            <i class="fa-solid fa-pen text-[10px]"></i> Edit
          </button>
        </div>

        {{-- View mode --}}
        <div id="data-diri-view" class="s-body">
          @php $anyData = collect($details)->filter(fn($d) => $d[2])->isNotEmpty(); @endphp
          @if($anyData)
            @foreach($details as [$ic, $lb, $vl, $cls])
              @if($vl)
              <div class="info-row">
                <div class="info-icon {{ $cls }}"><i class="fa-solid {{ $ic }}"></i></div>
                <div class="flex-1 min-w-0">
                  <p class="text-[10.5px] uppercase tracking-wide font-semibold mb-0.5" style="color:var(--muted)">{{ $lb }}</p>
                  <p class="text-[13px] break-words leading-snug" style="color:var(--text)">{{ $vl }}</p>
                </div>
              </div>
              @endif
            @endforeach
          @else
            <div class="text-center py-6" style="color:var(--muted)">
              <i class="fa-solid fa-user-slash text-2xl mb-2 block opacity-40"></i>
              <p class="text-[13px]">Belum dilengkapi.</p>
              <button type="button" onclick="toggleEdit()" class="mt-1 text-[12px] font-semibold" style="color:var(--ac)">
                Lengkapi sekarang →
              </button>
            </div>
          @endif
        </div>

        {{-- Edit mode --}}
        <div id="data-diri-form" class="s-body" style="display:none">
          @if($errors->any())
            <div class="mb-3 px-3 py-2.5 rounded-xl text-[12px] font-medium" style="background:rgba(248,113,113,.1);color:#f87171">
              <i class="fa-solid fa-circle-xmark mr-1"></i>{{ $errors->first() }}
            </div>
          @endif
          <form method="POST" action="{{ route('instruktur.profile.update') }}" class="space-y-3">
            @csrf

            <p class="text-[10.5px] uppercase font-bold tracking-wide" style="color:var(--muted)">Data Pribadi</p>

            <div>
              <label class="f-label">Nama Lengkap <span class="text-rose-400">*</span></label>
              <input type="text" name="nama" class="f-input" value="{{ old('nama', $instruktur->nama) }}" required>
            </div>

            <div class="grid grid-cols-2 gap-2">
              <div>
                <label class="f-label">NIDN</label>
                <input type="text" name="nidn" class="f-input" value="{{ old('nidn', $instruktur->nidn) }}">
              </div>
              <div>
                <label class="f-label">NIP</label>
                <input type="text" name="nip" class="f-input" value="{{ old('nip', $instruktur->nip) }}">
              </div>
            </div>

            <div>
              <label class="f-label">Jenis Kelamin</label>
              <select name="jenis_kelamin" class="f-input">
                <option value="">— Pilih —</option>
                <option value="Laki-laki" {{ old('jenis_kelamin', $instruktur->jenis_kelamin) === 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                <option value="Perempuan" {{ old('jenis_kelamin', $instruktur->jenis_kelamin) === 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
              </select>
            </div>

            <div>
              <label class="f-label">Pendidikan Terakhir</label>
              <input type="text" name="pendidikan_terakhir" class="f-input"
                placeholder="cth. S2 Teknik Informatika"
                value="{{ old('pendidikan_terakhir', $instruktur->pendidikan_terakhir) }}">
            </div>

            <div>
              <label class="f-label">Bidang Keahlian</label>
              <input type="text" name="bidang_keahlian" class="f-input"
                placeholder="cth. Machine Learning"
                value="{{ old('bidang_keahlian', $instruktur->bidang_keahlian) }}">
            </div>

            <div style="height:1px;background:var(--border)"></div>
            <p class="text-[10.5px] uppercase font-bold tracking-wide" style="color:var(--muted)">Akun</p>

            <div>
              <label class="f-label">Nama Akun <span class="text-rose-400">*</span></label>
              <input type="text" name="name" class="f-input" value="{{ old('name', $user->name) }}" required>
            </div>

            <div>
              <label class="f-label">Email <span class="text-rose-400">*</span></label>
              <input type="email" name="email" class="f-input" value="{{ old('email', $user->email) }}" required>
            </div>

            <div>
              <label class="f-label">No. HP</label>
              <input type="text" name="no_hp" class="f-input" value="{{ old('no_hp', $instruktur->no_hp) }}">
            </div>

            <div class="flex gap-2 pt-1">
              <button type="submit" class="flex-1 py-2 rounded-xl text-[13px] font-semibold text-white a-grad">
                <i class="fa-solid fa-floppy-disk mr-1 text-[11px]"></i>Simpan
              </button>
              <button type="button" onclick="toggleEdit()" class="px-3 py-2 rounded-xl text-[13px] font-semibold border"
                style="border-color:var(--border);color:var(--sub)">Batal</button>
            </div>
          </form>
        </div>
      </div>

      {{-- Keamanan --}}
      <div class="s-card">
        <div class="s-head">
          <div class="w-8 h-8 rounded-xl grid place-items-center text-[12px]"
            style="background:rgba(99,102,241,.14);color:#818cf8">
            <i class="fa-solid fa-shield-halved"></i>
          </div>
          <span class="font-display font-semibold text-[14px]" style="color:var(--text)">Keamanan</span>
        </div>
        <div class="s-body">
          <a href="{{ route('instruktur.profile.password') }}"
            class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[13px] font-semibold border transition-colors"
            style="border-color:var(--border);color:var(--sub)"
            onmouseover="this.style.borderColor='var(--ac)';this.style.color='var(--ac)'"
            onmouseout="this.style.borderColor='var(--border)';this.style.color='var(--sub)'">
            <i class="fa-solid fa-lock text-[12px]"></i>
            <span class="flex-1">Ubah Password</span>
            <i class="fa-solid fa-chevron-right text-[10px]"></i>
          </a>
        </div>
      </div>

    </div>

    {{-- ── RIGHT COLUMN ── --}}
    <div class="space-y-4">

      {{-- Stats --}}
      <div class="grid grid-cols-3 gap-3">
        <div class="stat-mini">
          <div class="stat-mini-icon a-bg-lt a-text"><i class="fa-solid fa-chalkboard-user"></i></div>
          <div>
            <div class="font-bold text-[20px] leading-none" style="color:var(--text)">{{ $kelas->count() }}</div>
            <div class="text-[11px] mt-0.5" style="color:var(--muted)">Total Kelas</div>
          </div>
        </div>
        <div class="stat-mini">
          <div class="stat-mini-icon" style="background:rgba(16,185,129,.14);color:#34d399"><i class="fa-solid fa-circle-play"></i></div>
          <div>
            <div class="font-bold text-[20px] leading-none" style="color:var(--text)">{{ $aktif->count() }}</div>
            <div class="text-[11px] mt-0.5" style="color:var(--muted)">Kelas Aktif</div>
          </div>
        </div>
        <div class="stat-mini">
          <div class="stat-mini-icon" style="background:rgba(99,102,241,.14);color:#818cf8"><i class="fa-solid fa-users"></i></div>
          <div>
            <div class="font-bold text-[20px] leading-none" style="color:var(--text)">{{ $totalPeserta }}</div>
            <div class="text-[11px] mt-0.5" style="color:var(--muted)">Total Peserta</div>
          </div>
        </div>
      </div>

      {{-- Kelas table --}}
      <div class="s-card">
        <div class="s-head" style="justify-content:space-between">
          <div class="flex items-center gap-2">
            <div class="a-bg-lt a-text w-8 h-8 rounded-xl grid place-items-center text-[12px]">
              <i class="fa-solid fa-chalkboard"></i>
            </div>
            <span class="font-display font-semibold text-[14px]" style="color:var(--text)">Kelas yang Diampu</span>
          </div>
          <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold a-bg-lt a-text">{{ $kelas->count() }}</span>
        </div>

        @if($kelas->isEmpty())
          <div class="s-body py-12 text-center">
            <i class="fa-solid fa-chalkboard text-3xl mb-3 block opacity-30" style="color:var(--muted)"></i>
            <p class="text-[13px] font-semibold" style="color:var(--text)">Belum mengampu kelas</p>
          </div>
        @else
          <div style="overflow-x:auto">
            <table class="kl-table">
              <thead>
                <tr>
                  <th>Mata Kuliah</th>
                  <th>Periode</th>
                  <th style="text-align:center">Peserta</th>
                  <th style="text-align:center">Status</th>
                </tr>
              </thead>
              <tbody>
                @foreach($kelas->sortByDesc(fn($k) => $k->periodeAkademik?->created_at) as $k)
                @php
                  $sc = match($k->status) {
                    'Aktif'   => 'bg-emerald-500/15 text-emerald-400',
                    'Selesai' => 'bg-blue-500/15 text-blue-400',
                    default   => 'bg-slate-500/15 text-slate-400',
                  };
                @endphp
                <tr>
                  <td>
                    <div class="font-semibold" style="color:var(--text)">{{ $k->mataKuliah?->nama ?? '—' }}</div>
                    <div class="font-mono text-[11px] a-text">{{ $k->kode_display }}</div>
                  </td>
                  <td style="color:var(--muted);white-space:nowrap;font-size:12px">{{ $k->periodeAkademik?->nama ?? '—' }}</td>
                  <td style="text-align:center;font-weight:600;color:var(--text)">
                    {{ $k->enrollments_count ?? 0 }}
                    @if($k->kapasitas)<span style="font-size:11px;font-weight:400;color:var(--muted)">/{{ $k->kapasitas }}</span>@endif
                  </td>
                  <td style="text-align:center">
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold {{ $sc }}">
                      <i class="fa-solid fa-circle text-[6px]"></i>{{ $k->status }}
                    </span>
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @endif
      </div>

    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
@if($errors->any())
  document.addEventListener('DOMContentLoaded', () => toggleEdit(true));
@endif

function toggleEdit(forceOpen = false) {
  const view = document.getElementById('data-diri-view');
  const form = document.getElementById('data-diri-form');
  const btn  = document.getElementById('edit-toggle-btn');
  const open = forceOpen || form.style.display === 'none';
  view.style.display = open ? 'none' : '';
  form.style.display = open ? '' : 'none';
  btn.classList.toggle('active', open);
  btn.innerHTML = open
    ? '<i class="fa-solid fa-times text-[10px]"></i> Batal'
    : '<i class="fa-solid fa-pen text-[10px]"></i> Edit';
}

let originalAvatarSrc = null;

function previewAvatar(input) {
  if (!input.files || !input.files[0]) return;
  const reader = new FileReader();
  reader.onload = e => {
    const img = document.getElementById('avatar-img');
    const ph  = document.getElementById('avatar-placeholder');
    if (!originalAvatarSrc) originalAvatarSrc = img.src;
    img.src = e.target.result;
    img.style.display = '';
    if (ph) ph.style.display = 'none';
  };
  reader.readAsDataURL(input.files[0]);
  document.getElementById('avatar-action').style.display = '';
}

function cancelAvatar() {
  document.getElementById('avatar-input').value = '';
  const img = document.getElementById('avatar-img');
  if (originalAvatarSrc) {
    img.src = originalAvatarSrc;
  } else {
    img.style.display = 'none';
    const ph = document.getElementById('avatar-placeholder');
    if (ph) ph.style.display = '';
  }
  document.getElementById('avatar-action').style.display = 'none';
}
</script>
@endpush
