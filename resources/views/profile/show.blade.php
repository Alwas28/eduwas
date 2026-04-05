@php
  /** @var \App\Models\User $authUser */
  $authUser       = auth()->user();
  $authRoles      = $authUser->roles->pluck('name')->map(fn($n) => strtolower($n));
  $isOwner        = $mahasiswa->user_id && $mahasiswa->user_id === $authUser->id;
  $canEditPeserta = $authUser->allAccesses()->contains('edit.peserta');
  $layout         = $authRoles->contains('mahasiswa') ? 'layouts.mahasiswa' : 'layouts.admin';

  $enrollments = $mahasiswa->enrollments;
  $aktif       = $enrollments->where('status', 'Aktif');
  $lulus       = $enrollments->where('status', 'Lulus');
  $rataRata    = $enrollments->whereNotNull('nilai_akhir')->avg('nilai_akhir');
  $totalSks    = $aktif->sum(fn($e) => $e->kelas->mataKuliah?->sks ?? 0);

  $statusColor = match($mahasiswa->status) {
    'Aktif'   => 'rgba(16,185,129,.15)',
    'Cuti'    => 'rgba(245,158,11,.15)',
    'Dropout' => 'rgba(239,68,68,.15)',
    'Lulus'   => 'rgba(59,130,246,.15)',
    default   => 'rgba(148,163,184,.15)',
  };
  $statusText = match($mahasiswa->status) {
    'Aktif'   => '#10b981',
    'Cuti'    => '#f59e0b',
    'Dropout' => '#ef4444',
    'Lulus'   => '#3b82f6',
    default   => '#94a3b8',
  };
@endphp

@extends($layout)
@section('title', $isOwner ? 'Profil Saya' : 'Profil — ' . $mahasiswa->nama)
@section('page-title', $isOwner ? 'Profil Saya' : 'Profil Mahasiswa')

@push('styles')
<style>
/* ── Layout ── */
.profile-grid { display:grid; grid-template-columns:280px 1fr; gap:20px; align-items:start; }
@media(max-width:900px){ .profile-grid { grid-template-columns:1fr; } }

/* ── Cards ── */
.s-card { background:var(--surface); border:1px solid var(--border); border-radius:16px; overflow:hidden; }
.s-head { padding:12px 16px; border-bottom:1px solid var(--border); display:flex; align-items:center; justify-content:space-between; gap:8px; }
.s-body { padding:16px; }

/* ── Identity card ── */
.id-card { padding:20px; display:flex; flex-direction:column; align-items:center; text-align:center; gap:12px; }
.id-avatar { position:relative; width:80px; height:80px; }
.id-avatar img, .id-initials {
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
.id-nim { font-size:12px; font-weight:700; font-family:monospace; padding:3px 10px; border-radius:8px; }
.id-badge { display:inline-flex; align-items:center; gap:5px; padding:3px 10px; border-radius:20px; font-size:11.5px; font-weight:600; }

/* ── Info row ── */
.info-row { display:flex; align-items:flex-start; gap:10px; padding:9px 0; border-bottom:1px solid var(--border); }
.info-row:last-child { border-bottom:none; }
.info-icon { width:30px; height:30px; border-radius:8px; display:grid; place-items:center; font-size:11px; flex-shrink:0; }

/* ── Stat mini ── */
.stat-mini { border-radius:12px; border:1px solid var(--border); padding:12px 14px; display:flex; align-items:center; gap:10px; background:var(--surface); }
.stat-mini-icon { width:34px; height:34px; border-radius:10px; display:grid; place-items:center; font-size:12px; flex-shrink:0; }

/* ── Edit toggle ── */
.edit-btn { display:inline-flex; align-items:center; gap:6px; padding:5px 10px; border-radius:9px; font-size:12px; font-weight:600; border:1px solid var(--border); color:var(--sub); background:transparent; cursor:pointer; transition:all .15s; }
.edit-btn:hover { border-color:var(--ac); color:var(--ac); }
.edit-btn.active { background:var(--ac); border-color:var(--ac); color:#fff; }

/* ── Tab switcher ── */
.form-tab { display:flex; border-radius:10px; padding:3px; gap:3px; background:var(--surface2); margin-bottom:14px; }
.form-tab button { flex:1; padding:6px; border:none; border-radius:8px; font-size:12px; font-weight:600; cursor:pointer; transition:all .15s; color:var(--muted); background:transparent; }
.form-tab button.active { background:var(--surface); color:var(--text); box-shadow:0 1px 4px rgba(0,0,0,.15); }

/* ── Password eye toggle ── */
.pw-wrap { position:relative; }
.pw-wrap input { padding-right:38px; }
.pw-eye { position:absolute; right:10px; top:50%; transform:translateY(-50%); border:none; background:none; color:var(--muted); cursor:pointer; font-size:13px; padding:2px 4px; }

/* ── Enrollment table ── */
.enr-table { width:100%; border-collapse:collapse; }
.enr-table th { padding:9px 14px; text-align:left; font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:var(--muted); background:var(--surface2); }
.enr-table td { padding:10px 14px; border-bottom:1px solid var(--border); font-size:13px; color:var(--text); vertical-align:middle; }
.enr-table tr:last-child td { border-bottom:none; }
.enr-table tbody tr:hover td { background:var(--surface2); }

/* ── Toast ── */
#profile-toast { position:fixed; bottom:24px; left:50%; transform:translateX(-50%) translateY(80px); background:var(--surface); border:1px solid var(--border); border-radius:14px; padding:10px 20px; font-size:13px; font-weight:600; color:var(--text); box-shadow:0 8px 24px rgba(0,0,0,.25); transition:transform .3s,opacity .3s; opacity:0; z-index:300; white-space:nowrap; }
#profile-toast.show { transform:translateX(-50%) translateY(0); opacity:1; }
</style>
@endpush

@section('content')
<div class="space-y-5 pt-8">

  {{-- Back (admin) --}}
  @if(!$isOwner)
  <div>
    <a href="{{ route('admin.peserta.index') }}"
      class="inline-flex items-center gap-2 text-[13px] font-medium transition-colors"
      style="color:var(--muted)"
      onmouseover="this.style.color='var(--ac)'" onmouseout="this.style.color='var(--muted)'">
      <i class="fa-solid fa-arrow-left text-[11px]"></i> Kembali ke Daftar Peserta
    </a>
  </div>
  @endif

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

  {{-- Hidden avatar form (owner only) --}}
  @if($isOwner)
  <form method="POST" action="{{ route('mahasiswa.profile.avatar') }}" enctype="multipart/form-data" id="avatar-form" style="display:none">
    @csrf
    <input type="file" id="avatar-input" name="avatar" accept="image/jpg,image/jpeg,image/png,image/webp"
      onchange="previewAvatar(this)">
  </form>
  @endif

  {{-- ── MAIN GRID ── --}}
  <div class="profile-grid">

    {{-- ── LEFT SIDEBAR ── --}}
    <div class="space-y-4">

      {{-- Identity card --}}
      <div class="s-card">
        <div class="id-card">
          {{-- Avatar --}}
          <div class="id-avatar">
            @if($mahasiswa->user?->avatarUrl())
              <img id="avatar-img" src="{{ $mahasiswa->user->avatarUrl() }}" alt="Foto">
            @else
              <div id="avatar-placeholder" class="id-initials a-grad">
                {{ strtoupper(substr($mahasiswa->nama, 0, 1)) }}
              </div>
              <img id="avatar-img" src="" alt="" style="display:none;width:80px;height:80px;border-radius:50%;object-fit:cover;border:3px solid var(--border);">
            @endif
            @if($isOwner)
            <label for="avatar-input" class="id-avatar-btn" title="Ubah Foto">
              <i class="fa-solid fa-camera"></i>
            </label>
            @endif
          </div>

          {{-- Name --}}
          <div>
            <div class="id-name">{{ $mahasiswa->nama }}</div>
            <div class="id-sub">{{ $mahasiswa->jurusan?->nama ?? 'Mahasiswa' }}</div>
          </div>

          {{-- NIM --}}
          <span class="id-nim a-bg-lt a-text">{{ $mahasiswa->nim }}</span>

          {{-- Status --}}
          <span class="id-badge" style="background:{{ $statusColor }};color:{{ $statusText }}">
            <i class="fa-solid fa-circle text-[7px]"></i>{{ $mahasiswa->status }}
          </span>

          @if($mahasiswa->user_id)
          <span class="id-badge" style="background:rgba(16,185,129,.12);color:#10b981;font-size:11px">
            <i class="fa-solid fa-shield-check text-[9px]"></i>Akun Aktif
          </span>
          @endif

          {{-- Avatar pending save --}}
          @if($isOwner)
          <div id="avatar-action" class="w-full px-3 py-2.5 rounded-xl text-[12px]"
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
          @endif
        </div>
      </div>

      {{-- Data Pribadi --}}
      <div class="s-card">
        <div class="s-head">
          <div class="flex items-center gap-2">
            <div class="a-bg-lt a-text w-8 h-8 rounded-xl grid place-items-center text-[12px]">
              <i class="fa-solid fa-id-card"></i>
            </div>
            <span class="font-display font-semibold text-[14px]" style="color:var(--text)">Data Pribadi</span>
          </div>
          @if($isOwner || $canEditPeserta)
          <button type="button" id="btn-toggle-edit" class="edit-btn" onclick="toggleEditMhs()">
            <i class="fa-solid fa-pen text-[11px]"></i>Edit
          </button>
          @endif
        </div>

        {{-- View mode --}}
        <div id="mhs-detail-view" class="s-body">
          @php
            $jk = match($mahasiswa->jenis_kelamin) { 'L' => 'Laki-laki', 'P' => 'Perempuan', default => null };
            $details = [
              ['fa-id-card',        'NIM',           $mahasiswa->nim,                  'a-bg-lt a-text'],
              ['fa-calendar',       'Angkatan',      $mahasiswa->angkatan,             'bg-blue-500/10 text-blue-400'],
              ['fa-venus-mars',     'Jenis Kelamin', $jk,                             'bg-purple-500/10 text-purple-400'],
              ['fa-map-marker-alt', 'Tempat Lahir',  $mahasiswa->tempat_lahir,         'bg-rose-500/10 text-rose-400'],
              ['fa-cake-candles',   'Tanggal Lahir', $mahasiswa->tanggal_lahir?->format('d M Y'), 'bg-amber-500/10 text-amber-400'],
              ['fa-phone',          'No. HP',        $mahasiswa->no_hp,               'bg-emerald-500/10 text-emerald-400'],
              ['fa-envelope',       'Email',         $mahasiswa->email,               'bg-sky-500/10 text-sky-400'],
              ['fa-location-dot',   'Alamat',        $mahasiswa->alamat,              'bg-orange-500/10 text-orange-400'],
            ];
          @endphp
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
        </div>

        {{-- Edit form (owner: personal fields) --}}
        @if($isOwner)
        <form id="mhs-edit-form" method="POST" action="{{ route('mahasiswa.profile.update') }}" class="s-body space-y-3" style="display:none">
          @csrf
          <div>
            <label class="f-label">Nama Lengkap <span class="text-rose-400">*</span></label>
            <input type="text" name="nama" class="f-input @error('nama') is-invalid @enderror"
              value="{{ old('nama', $mahasiswa->nama) }}" required>
            @error('nama')<p class="f-error">{{ $message }}</p>@enderror
          </div>
          <div>
            <label class="f-label">Email <span class="text-rose-400">*</span></label>
            <input type="email" name="email" class="f-input @error('email') is-invalid @enderror"
              value="{{ old('email', $mahasiswa->email) }}" required>
            @error('email')<p class="f-error">{{ $message }}</p>@enderror
          </div>
          <div>
            <label class="f-label">Jenis Kelamin</label>
            <select name="jenis_kelamin" class="f-input">
              <option value="">— Pilih —</option>
              <option value="L" {{ old('jenis_kelamin', $mahasiswa->jenis_kelamin) === 'L' ? 'selected' : '' }}>Laki-laki</option>
              <option value="P" {{ old('jenis_kelamin', $mahasiswa->jenis_kelamin) === 'P' ? 'selected' : '' }}>Perempuan</option>
            </select>
          </div>
          <div class="grid grid-cols-2 gap-2">
            <div>
              <label class="f-label">Tempat Lahir</label>
              <input type="text" name="tempat_lahir" class="f-input"
                value="{{ old('tempat_lahir', $mahasiswa->tempat_lahir) }}">
            </div>
            <div>
              <label class="f-label">Tanggal Lahir</label>
              <input type="date" name="tanggal_lahir" class="f-input"
                value="{{ old('tanggal_lahir', $mahasiswa->tanggal_lahir?->format('Y-m-d')) }}">
            </div>
          </div>
          <div>
            <label class="f-label">No. HP</label>
            <input type="text" name="no_hp" class="f-input"
              value="{{ old('no_hp', $mahasiswa->no_hp) }}">
          </div>
          <div>
            <label class="f-label">Alamat</label>
            <textarea name="alamat" class="f-input" rows="2">{{ old('alamat', $mahasiswa->alamat) }}</textarea>
          </div>
          <div class="flex gap-2 pt-1">
            <button type="submit" class="flex-1 py-2 rounded-xl text-[13px] font-semibold text-white a-grad">
              <i class="fa-solid fa-floppy-disk mr-1 text-[11px]"></i>Simpan
            </button>
            <button type="button" onclick="toggleEditMhs()" class="px-3 py-2 rounded-xl text-[13px] font-semibold border"
              style="border-color:var(--border);color:var(--sub)">Batal</button>
          </div>
        </form>

        {{-- Edit form (admin: full fields via AJAX) --}}
        @elseif($canEditPeserta)
        <form id="mhs-edit-form" style="display:none" class="s-body space-y-3" onsubmit="submitMhsEdit(event)">
          @csrf @method('PUT')
          <div>
            <label class="f-label">Nama <span class="text-rose-400">*</span></label>
            <input type="text" name="nama" class="f-input" value="{{ $mahasiswa->nama }}" required>
          </div>
          <div class="grid grid-cols-2 gap-2">
            <div>
              <label class="f-label">NIM <span class="text-rose-400">*</span></label>
              <input type="text" name="nim" class="f-input" value="{{ $mahasiswa->nim }}" required>
            </div>
            <div>
              <label class="f-label">Angkatan</label>
              <input type="number" name="angkatan" class="f-input" value="{{ $mahasiswa->angkatan }}" min="2000" max="2099">
            </div>
          </div>
          <div>
            <label class="f-label">Jurusan</label>
            <select name="jurusan_id" class="f-input">
              <option value="">— Pilih Jurusan —</option>
              @foreach($jurusan ?? [] as $j)
                <option value="{{ $j->id }}" {{ $mahasiswa->jurusan_id == $j->id ? 'selected' : '' }}>{{ $j->nama }}</option>
              @endforeach
            </select>
          </div>
          <div class="grid grid-cols-2 gap-2">
            <div>
              <label class="f-label">Jenis Kelamin</label>
              <select name="jenis_kelamin" class="f-input">
                <option value="">—</option>
                <option value="L" {{ $mahasiswa->jenis_kelamin === 'L' ? 'selected' : '' }}>Laki-laki</option>
                <option value="P" {{ $mahasiswa->jenis_kelamin === 'P' ? 'selected' : '' }}>Perempuan</option>
              </select>
            </div>
            <div>
              <label class="f-label">Status <span class="text-rose-400">*</span></label>
              <select name="status" class="f-input" required>
                @foreach(['Aktif','Cuti','Dropout','Lulus'] as $st)
                  <option value="{{ $st }}" {{ $mahasiswa->status === $st ? 'selected' : '' }}>{{ $st }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div>
            <label class="f-label">Email</label>
            <input type="email" name="email" class="f-input" value="{{ $mahasiswa->email }}">
          </div>
          <div class="grid grid-cols-2 gap-2">
            <div>
              <label class="f-label">Tempat Lahir</label>
              <input type="text" name="tempat_lahir" class="f-input" value="{{ $mahasiswa->tempat_lahir }}">
            </div>
            <div>
              <label class="f-label">Tanggal Lahir</label>
              <input type="date" name="tanggal_lahir" class="f-input" value="{{ $mahasiswa->tanggal_lahir?->format('Y-m-d') }}">
            </div>
          </div>
          <div>
            <label class="f-label">No. HP</label>
            <input type="text" name="no_hp" class="f-input" value="{{ $mahasiswa->no_hp }}">
          </div>
          <div>
            <label class="f-label">Alamat</label>
            <textarea name="alamat" class="f-input" rows="2">{{ $mahasiswa->alamat }}</textarea>
          </div>
          <div class="flex gap-2 pt-1">
            <button type="submit" id="btn-save-mhs" class="flex-1 py-2 rounded-xl text-[12.5px] font-semibold text-white a-grad">
              <i class="fa-solid fa-floppy-disk mr-1 text-[11px]"></i>Simpan
            </button>
            <button type="button" onclick="toggleEditMhs()" class="px-3 py-2 rounded-xl text-[12.5px] font-semibold border"
              style="border-color:var(--border);color:var(--sub)">Batal</button>
          </div>
        </form>
        @endif
      </div>

      {{-- Pengaturan Akun (owner only) --}}
      @if($isOwner)
      <div class="s-card">
        <div class="s-head">
          <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-xl grid place-items-center text-[12px]" style="background:rgba(99,102,241,.14);color:#818cf8">
              <i class="fa-solid fa-shield-halved"></i>
            </div>
            <span class="font-display font-semibold text-[14px]" style="color:var(--text)">Keamanan Akun</span>
          </div>
        </div>
        <div class="s-body">
          <div class="form-tab">
            <button id="tab-info" class="active" onclick="switchTab('info')">
              <i class="fa-solid fa-user mr-1"></i>Akun
            </button>
            <button id="tab-password" onclick="switchTab('password')">
              <i class="fa-solid fa-lock mr-1"></i>Password
            </button>
          </div>

          {{-- Informasi Akun --}}
          <div id="panel-info">
            @if(session('status') === 'account-updated')
              <div class="mb-3 px-3 py-2 rounded-xl text-[12px] font-medium flex items-center gap-2" style="background:rgba(16,185,129,.1);color:#10b981">
                <i class="fa-solid fa-circle-check"></i>Akun berhasil diperbarui.
              </div>
            @endif
            <form method="POST" action="{{ route('profile.update') }}" class="space-y-3">
              @csrf @method('PATCH')
              <div>
                <label class="f-label">Nama Akun <span class="text-rose-400">*</span></label>
                <input type="text" name="name" class="f-input" value="{{ old('name', $authUser->name) }}" required>
              </div>
              <div>
                <label class="f-label">Email Login <span class="text-rose-400">*</span></label>
                <input type="email" name="email" class="f-input" value="{{ old('email', $authUser->email) }}" required>
              </div>
              <button type="submit" class="w-full py-2 rounded-xl text-[13px] font-semibold text-white a-grad">
                <i class="fa-solid fa-floppy-disk mr-1 text-[11px]"></i>Simpan
              </button>
            </form>
          </div>

          {{-- Password --}}
          <div id="panel-password" style="display:none">
            @if(session('status') === 'password-updated')
              <div class="mb-3 px-3 py-2 rounded-xl text-[12px] font-medium flex items-center gap-2" style="background:rgba(16,185,129,.1);color:#10b981">
                <i class="fa-solid fa-circle-check"></i>Password berhasil diperbarui.
              </div>
            @endif
            <form method="POST" action="{{ route('password.update') }}" class="space-y-3">
              @csrf @method('PUT')
              <div>
                <label class="f-label">Password Saat Ini <span class="text-rose-400">*</span></label>
                <div class="pw-wrap">
                  <input type="password" name="current_password" id="pw-current"
                    class="f-input @error('current_password','updatePassword') is-invalid @enderror"
                    autocomplete="current-password">
                  <button type="button" class="pw-eye" onclick="togglePw('pw-current',this)">
                    <i class="fa-regular fa-eye"></i>
                  </button>
                </div>
                @error('current_password','updatePassword')<p class="f-error">{{ $message }}</p>@enderror
              </div>
              <div>
                <label class="f-label">Password Baru <span class="text-rose-400">*</span></label>
                <div class="pw-wrap">
                  <input type="password" name="password" id="pw-new"
                    class="f-input @error('password','updatePassword') is-invalid @enderror"
                    autocomplete="new-password">
                  <button type="button" class="pw-eye" onclick="togglePw('pw-new',this)">
                    <i class="fa-regular fa-eye"></i>
                  </button>
                </div>
                @error('password','updatePassword')<p class="f-error">{{ $message }}</p>@enderror
              </div>
              <div>
                <label class="f-label">Konfirmasi Password <span class="text-rose-400">*</span></label>
                <div class="pw-wrap">
                  <input type="password" name="password_confirmation" id="pw-confirm"
                    class="f-input" autocomplete="new-password">
                  <button type="button" class="pw-eye" onclick="togglePw('pw-confirm',this)">
                    <i class="fa-regular fa-eye"></i>
                  </button>
                </div>
              </div>
              <button type="submit" class="w-full py-2 rounded-xl text-[13px] font-semibold text-white"
                style="background:linear-gradient(135deg,#6366f1,#8b5cf6)">
                <i class="fa-solid fa-key mr-1 text-[11px]"></i>Perbarui Password
              </button>
            </form>
          </div>
        </div>
      </div>
      @endif

    </div>

    {{-- ── RIGHT ── --}}
    <div class="space-y-4">

      {{-- Stats --}}
      <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div class="stat-mini">
          <div class="stat-mini-icon a-bg-lt a-text"><i class="fa-solid fa-door-open"></i></div>
          <div>
            <div class="font-bold text-[20px] leading-none" style="color:var(--text)">{{ $enrollments->count() }}</div>
            <div class="text-[11px] mt-0.5" style="color:var(--muted)">Total Kelas</div>
          </div>
        </div>
        <div class="stat-mini">
          <div class="stat-mini-icon" style="background:rgba(99,102,241,.14);color:#818cf8"><i class="fa-solid fa-layer-group"></i></div>
          <div>
            <div class="font-bold text-[20px] leading-none" style="color:var(--text)">{{ $totalSks }}</div>
            <div class="text-[11px] mt-0.5" style="color:var(--muted)">SKS Aktif</div>
          </div>
        </div>
        <div class="stat-mini">
          <div class="stat-mini-icon" style="background:rgba(245,158,11,.14);color:#fbbf24"><i class="fa-solid fa-star-half-stroke"></i></div>
          <div>
            <div class="font-bold text-[20px] leading-none" style="color:var(--text)">{{ $rataRata !== null ? number_format($rataRata,1) : '—' }}</div>
            <div class="text-[11px] mt-0.5" style="color:var(--muted)">Rata-rata</div>
          </div>
        </div>
        <div class="stat-mini">
          <div class="stat-mini-icon" style="background:rgba(16,185,129,.14);color:#34d399"><i class="fa-solid fa-graduation-cap"></i></div>
          <div>
            <div class="font-bold text-[20px] leading-none" style="color:var(--text)">{{ $lulus->count() }}</div>
            <div class="text-[11px] mt-0.5" style="color:var(--muted)">Lulus</div>
          </div>
        </div>
      </div>

      {{-- Riwayat Kelas --}}
      <div class="s-card">
        <div class="s-head">
          <div class="flex items-center gap-2">
            <div class="a-bg-lt a-text w-8 h-8 rounded-xl grid place-items-center text-[12px]">
              <i class="fa-solid fa-book-open"></i>
            </div>
            <span class="font-display font-semibold text-[14px]" style="color:var(--text)">Riwayat Kelas</span>
          </div>
          <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold a-bg-lt a-text">{{ $enrollments->count() }}</span>
        </div>

        @if($enrollments->isEmpty())
          <div class="s-body py-12 text-center">
            <i class="fa-solid fa-door-open text-3xl mb-3 block opacity-30" style="color:var(--muted)"></i>
            <p class="text-[13px] font-semibold" style="color:var(--text)">Belum ada kelas</p>
          </div>
        @else
          <div style="overflow-x:auto">
            <table class="enr-table">
              <thead>
                <tr>
                  <th>Mata Kuliah</th>
                  <th>Periode</th>
                  <th class="text-center">SKS</th>
                  <th class="text-center">Nilai</th>
                  <th class="text-center">Status</th>
                </tr>
              </thead>
              <tbody>
                @foreach($enrollments->sortByDesc(fn($e) => $e->kelas->periodeAkademik?->created_at) as $enrollment)
                @php
                  $mk    = $enrollment->kelas->mataKuliah;
                  $kode  = $mk?->kode ?? '?';
                  if ($enrollment->kelas->kode_seksi) $kode .= '-' . $enrollment->kelas->kode_seksi;
                  $grade = $enrollment->grade ?? null;
                  $gradeColor = match($grade) {
                    'A' => 'bg-emerald-500/15 text-emerald-400',
                    'B' => 'bg-blue-500/15 text-blue-400',
                    'C' => 'bg-amber-500/15 text-amber-400',
                    'D' => 'bg-orange-500/15 text-orange-400',
                    'E' => 'bg-rose-500/15 text-rose-400',
                    default => 'bg-slate-500/15 text-slate-400',
                  };
                  $stColor = match($enrollment->status) {
                    'Aktif'   => 'bg-emerald-500/15 text-emerald-400',
                    'Lulus'   => 'bg-blue-500/15 text-blue-400',
                    'Dropout' => 'bg-rose-500/15 text-rose-400',
                    default   => 'bg-slate-500/15 text-slate-400',
                  };
                @endphp
                <tr>
                  <td>
                    <div class="font-semibold text-[13px]" style="color:var(--text)">{{ $mk?->nama ?? '—' }}</div>
                    <div class="font-mono text-[11px] a-text mt-0.5">{{ $kode }}</div>
                    <div class="text-[11px] mt-0.5" style="color:var(--muted)">
                      @foreach($enrollment->kelas->instruktur->take(1) as $ins){{ $ins->nama }}@endforeach
                    </div>
                  </td>
                  <td style="color:var(--muted);font-size:12px;white-space:nowrap">{{ $enrollment->kelas->periodeAkademik?->nama ?? '—' }}</td>
                  <td class="text-center font-semibold" style="color:var(--text)">{{ $mk?->sks ?? '—' }}</td>
                  <td class="text-center">
                    @if($enrollment->nilai_akhir !== null)
                      <div class="inline-flex items-center gap-1.5">
                        <span class="font-bold text-[14px]" style="color:var(--text)">{{ number_format($enrollment->nilai_akhir,1) }}</span>
                        @if($grade)
                          <span class="inline-flex items-center justify-center w-6 h-6 rounded-lg text-[11px] font-bold {{ $gradeColor }}">{{ $grade }}</span>
                        @endif
                      </div>
                    @else
                      <span style="color:var(--muted)">—</span>
                    @endif
                  </td>
                  <td class="text-center">
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold {{ $stColor }}">
                      <i class="fa-solid fa-circle text-[6px]"></i>{{ $enrollment->status }}
                    </span>
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @endif
      </div>

      {{-- Akun info (admin viewing) --}}
      @if(!$isOwner && $mahasiswa->user_id && $mahasiswa->user)
      <div class="s-card">
        <div class="s-head">
          <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-xl grid place-items-center text-[12px]" style="background:rgba(99,102,241,.14);color:#818cf8">
              <i class="fa-solid fa-user-shield"></i>
            </div>
            <span class="font-display font-semibold text-[14px]" style="color:var(--text)">Informasi Akun</span>
          </div>
        </div>
        <div class="s-body grid grid-cols-1 sm:grid-cols-3 gap-4">
          <div>
            <p class="text-[10.5px] uppercase tracking-wide font-semibold mb-1" style="color:var(--muted)">Nama Akun</p>
            <p class="text-[13px]" style="color:var(--text)">{{ $mahasiswa->user->name }}</p>
          </div>
          <div>
            <p class="text-[10.5px] uppercase tracking-wide font-semibold mb-1" style="color:var(--muted)">Email Login</p>
            <p class="text-[13px]" style="color:var(--text)">{{ $mahasiswa->user->email }}</p>
          </div>
          <div>
            <p class="text-[10.5px] uppercase tracking-wide font-semibold mb-1" style="color:var(--muted)">Terdaftar</p>
            <p class="text-[13px]" style="color:var(--text)">{{ $mahasiswa->user->created_at->format('d M Y') }}</p>
          </div>
        </div>
      </div>
      @endif

    </div>
  </div>
</div>

<div id="profile-toast"></div>
@endsection

@push('scripts')
<script>
// ── Edit toggle (shared for both owner form and admin form) ──
function toggleEditMhs() {
  const view = document.getElementById('mhs-detail-view');
  const form = document.getElementById('mhs-edit-form');
  const btn  = document.getElementById('btn-toggle-edit');
  if (!form) return;
  const editing = form.style.display !== 'none';
  view.style.display = editing ? '' : 'none';
  form.style.display = editing ? 'none' : '';
  if (btn) btn.innerHTML = editing
    ? '<i class="fa-solid fa-pen text-[11px]"></i>Edit'
    : '<i class="fa-solid fa-xmark text-[11px]"></i>Batal';
}

@if($errors->any() && $isOwner)
  document.addEventListener('DOMContentLoaded', () => toggleEditMhs());
@endif

// ── Admin AJAX submit ──
@if($canEditPeserta && !$isOwner)
const MHS_UPDATE_URL = '{{ route("admin.peserta.update", $mahasiswa) }}';
async function submitMhsEdit(e) {
  e.preventDefault();
  const form = e.target;
  const btn  = document.getElementById('btn-save-mhs');
  btn.disabled = true;
  btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1 text-[11px]"></i>Menyimpan…';
  try {
    const res  = await fetch(MHS_UPDATE_URL, {
      method: 'POST', body: new FormData(form),
      headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
    });
    const json = await res.json();
    if (res.ok) {
      showToast(json.message || 'Data berhasil disimpan.', 'success');
      setTimeout(() => location.reload(), 900);
    } else {
      const err = json.errors ? Object.values(json.errors)[0][0] : (json.message || 'Gagal.');
      showToast(err, 'error');
      btn.disabled = false;
      btn.innerHTML = '<i class="fa-solid fa-floppy-disk mr-1 text-[11px]"></i>Simpan';
    }
  } catch {
    showToast('Terjadi kesalahan jaringan.', 'error');
    btn.disabled = false;
    btn.innerHTML = '<i class="fa-solid fa-floppy-disk mr-1 text-[11px]"></i>Simpan';
  }
}
@endif

// ── Avatar (owner) ──
@if($isOwner)
let _origSrc = null;
function previewAvatar(input) {
  if (!input.files || !input.files[0]) return;
  const reader = new FileReader();
  reader.onload = e => {
    const img = document.getElementById('avatar-img');
    const ph  = document.getElementById('avatar-placeholder');
    if (!_origSrc) _origSrc = img.src;
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
  if (_origSrc) { img.src = _origSrc; }
  else { img.style.display = 'none'; const ph = document.getElementById('avatar-placeholder'); if (ph) ph.style.display = ''; }
  document.getElementById('avatar-action').style.display = 'none';
}

// Tab switcher
function switchTab(tab) {
  ['info','password'].forEach(t => {
    document.getElementById('tab-' + t).classList.toggle('active', t === tab);
    document.getElementById('panel-' + t).style.display = t === tab ? '' : 'none';
  });
}

// Password toggle
function togglePw(inputId, btn) {
  const input = document.getElementById(inputId);
  const isText = input.type === 'text';
  input.type = isText ? 'password' : 'text';
  btn.querySelector('i').className = isText ? 'fa-regular fa-eye' : 'fa-regular fa-eye-slash';
}
@endif

// Toast
let _toastTimer;
function showToast(msg, type = '') {
  const el = document.getElementById('profile-toast');
  el.textContent = msg;
  el.style.color = type === 'error' ? '#fca5a5' : type === 'success' ? '#6ee7b7' : 'var(--text)';
  el.style.borderColor = type === 'error' ? 'rgba(239,68,68,.3)' : 'var(--border)';
  el.classList.add('show');
  clearTimeout(_toastTimer);
  _toastTimer = setTimeout(() => el.classList.remove('show'), 3200);
}
</script>
@endpush
