@extends('layouts.admin')
@section('title', 'Profil — ' . $instruktur->nama)
@section('page-title', 'Profil Instruktur')

@php
  $authUser         = auth()->user();
  $accesses         = $authUser->allAccesses();
  $canEdit          = $accesses->contains('edit.instruktur');
  $canCreateAccount = $accesses->contains('tambah.user');
  $canResetPassword = $accesses->contains('reset-password.user');
  $hasAccount       = !!$instruktur->user_id;
@endphp

@push('styles')
<style>
.avatar-ring { position:relative; display:inline-block; }
.avatar-ring .avatar-overlay {
  position:absolute; inset:0; border-radius:9999px;
  background:rgba(0,0,0,.5); display:flex; align-items:center;
  justify-content:center; flex-direction:column; gap:4px;
  opacity:0; transition:opacity .2s;
}
.avatar-ring:hover .avatar-overlay { opacity:1; }
.avatar-ring.uploading .avatar-overlay { opacity:1; }

/* ── Toast ── */
.toast-wrap { position:fixed; top:20px; right:20px; z-index:9999; display:flex; flex-direction:column; gap:10px; pointer-events:none; }
.toast {
  display:flex; align-items:center; gap:12px;
  background:var(--surface); border:1px solid var(--border);
  border-radius:14px; padding:14px 18px;
  min-width:280px; max-width:360px;
  box-shadow:0 8px 32px rgba(0,0,0,.3);
  pointer-events:all; animation:slideIn .3s ease both;
}
.toast.toast-out { animation:slideOut .3s ease forwards; }
@keyframes slideIn { from { opacity:0; transform:translateX(40px); } to { opacity:1; transform:translateX(0); } }
@keyframes slideOut { to { opacity:0; transform:translateX(40px); } }
.toast-icon { width:36px; height:36px; border-radius:10px; display:grid; place-items:center; font-size:15px; flex-shrink:0; }
</style>
@endpush

@section('content')
<div id="toast-container" class="toast-wrap"></div>
<div class="space-y-5">

  {{-- Back --}}
  <div class="animate-fadeUp">
    <a href="{{ route('admin.instruktur.index') }}"
      class="inline-flex items-center gap-2 text-[13px] font-medium transition-colors"
      style="color:var(--muted)"
      onmouseover="this.style.color='var(--ac)'" onmouseout="this.style.color='var(--muted)'">
      <i class="fa-solid fa-arrow-left text-[11px]"></i> Kembali ke Daftar Instruktur
    </a>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    {{-- ────── LEFT ────── --}}
    <div class="lg:col-span-1 space-y-4">

      {{-- Identity card --}}
      <div class="rounded-2xl border overflow-hidden animate-fadeUp d1" style="background:var(--surface);border-color:var(--border)">
        <div class="h-20 a-grad"></div>

        <div class="pb-5 px-5 text-center">

          {{-- Avatar --}}
          <div class="flex justify-center -mt-10 mb-3">
            @if($hasAccount)
              <div class="avatar-ring" onclick="document.getElementById('avatar-input').click()" style="cursor:pointer">
                @if($instruktur->user?->avatarUrl())
                  <img id="avatar-img" src="{{ $instruktur->user->avatarUrl() }}" alt="Avatar"
                    class="w-20 h-20 rounded-full object-cover border-4" style="border-color:var(--surface)">
                @else
                  <div id="avatar-placeholder"
                    class="a-grad w-20 h-20 rounded-full grid place-items-center font-display font-bold text-[26px] text-white border-4"
                    style="border-color:var(--surface)">
                    {{ strtoupper(substr($instruktur->nama, 0, 1)) }}
                  </div>
                @endif
                <div class="avatar-overlay">
                  <i class="fa-solid fa-camera text-white text-[14px] icon-camera"></i>
                  <i class="fa-solid fa-spinner fa-spin text-white text-[14px] icon-spin" style="display:none"></i>
                  <span class="text-white text-[9px] font-semibold label-avatar">Ubah Foto</span>
                </div>
              </div>
              <input type="file" id="avatar-input" accept="image/jpg,image/jpeg,image/png,image/webp"
                class="hidden" onchange="uploadAvatar(this)">
            @else
              <div class="a-grad w-20 h-20 rounded-full grid place-items-center font-display font-bold text-[26px] text-white border-4"
                style="border-color:var(--surface)">
                {{ strtoupper(substr($instruktur->nama, 0, 1)) }}
              </div>
            @endif
          </div>

          <h2 class="font-display font-bold text-[18px]" style="color:var(--text)">{{ $instruktur->nama }}</h2>
          @if($instruktur->nidn)
            <p class="text-[13px] mt-0.5 font-mono font-semibold a-text">{{ $instruktur->nidn }}</p>
          @endif
          @if($instruktur->bidang_keahlian)
            <p class="text-[12.5px] mt-1" style="color:var(--muted)">{{ $instruktur->bidang_keahlian }}</p>
          @endif

          @php
            $statusCls = $instruktur->status === 'Aktif'
              ? 'bg-emerald-500/15 text-emerald-400'
              : 'bg-slate-500/15 text-slate-400';
          @endphp
          <div class="mt-3 flex justify-center gap-2 flex-wrap">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[12px] font-semibold {{ $statusCls }}">
              <i class="fa-solid fa-circle text-[7px]"></i>{{ $instruktur->status }}
            </span>
            @if($instruktur->pendidikan_terakhir)
              <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-semibold a-bg-lt a-text">
                {{ $instruktur->pendidikan_terakhir }}
              </span>
            @endif
            @if($hasAccount)
              <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-semibold"
                style="background:rgba(16,185,129,.1);color:#10b981">
                <i class="fa-solid fa-circle-check text-[9px]"></i>Akun Aktif
              </span>
            @else
              <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-semibold"
                style="background:var(--surface2);color:var(--muted)">
                <i class="fa-regular fa-circle text-[9px]"></i>Belum Ada Akun
              </span>
            @endif
          </div>
        </div>
      </div>

      {{-- Data Pribadi --}}
      <div class="rounded-2xl border animate-fadeUp d2" style="background:var(--surface);border-color:var(--border)">
        <div class="px-5 py-4 border-b flex items-center justify-between" style="border-color:var(--border)">
          <span class="font-display font-semibold text-[14px]" style="color:var(--text)">Data Pribadi</span>
          @if($canEdit)
          <button type="button" id="btn-toggle-edit" onclick="toggleEdit()"
            class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[12px] font-semibold border transition-colors"
            style="border-color:var(--border);color:var(--sub)"
            onmouseover="this.style.borderColor='var(--ac)';this.style.color='var(--ac)'"
            onmouseout="this.style.borderColor='var(--border)';this.style.color='var(--sub)'">
            <i class="fa-solid fa-pen-to-square text-[11px]"></i> Edit
          </button>
          @endif
        </div>

        {{-- Read-only --}}
        <div id="detail-view" class="px-5 py-4 space-y-3">
          @php
            $details = [
              ['fa-id-card',          'NIDN',              $instruktur->nidn],
              ['fa-id-badge',         'NIP',               $instruktur->nip],
              ['fa-venus-mars',       'Jenis Kelamin',     $instruktur->jenis_kelamin === 'L' ? 'Laki-laki' : ($instruktur->jenis_kelamin === 'P' ? 'Perempuan' : null)],
              ['fa-book',             'Bidang Keahlian',   $instruktur->bidang_keahlian],
              ['fa-graduation-cap',   'Pendidikan',        $instruktur->pendidikan_terakhir],
              ['fa-phone',            'No. HP',            $instruktur->no_hp],
              ['fa-envelope',         'Email',             $instruktur->email],
            ];
          @endphp
          @foreach($details as [$ic, $lb, $vl])
            @if($vl)
            <div class="flex items-start gap-3">
              <div class="w-7 h-7 rounded-lg grid place-items-center flex-shrink-0 text-[11px] a-bg-lt a-text mt-0.5">
                <i class="fa-solid {{ $ic }}"></i>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-[11px] uppercase tracking-wide font-semibold" style="color:var(--muted)">{{ $lb }}</p>
                <p class="text-[13px] mt-0.5 break-words" style="color:var(--text)">{{ $vl }}</p>
              </div>
            </div>
            @endif
          @endforeach
          @if(!collect($details)->filter(fn($d) => $d[2])->count())
            <p class="text-[13px] text-center py-4" style="color:var(--muted)">Belum ada data pribadi.</p>
          @endif
        </div>

        {{-- Inline edit form --}}
        @if($canEdit)
        <form id="edit-form" style="display:none" class="px-5 py-4 space-y-3" onsubmit="submitEdit(event)">
          @csrf @method('PUT')
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="f-label">NIDN</label>
              <input type="text" name="nidn" class="f-input" value="{{ $instruktur->nidn }}">
            </div>
            <div>
              <label class="f-label">NIP</label>
              <input type="text" name="nip" class="f-input" value="{{ $instruktur->nip }}">
            </div>
          </div>
          <div>
            <label class="f-label">Nama Lengkap <span class="text-rose-400">*</span></label>
            <input type="text" name="nama" class="f-input" value="{{ $instruktur->nama }}" required>
          </div>
          <div>
            <label class="f-label">Email</label>
            <input type="email" name="email" class="f-input" value="{{ $instruktur->email }}">
          </div>
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="f-label">No. HP</label>
              <input type="text" name="no_hp" class="f-input" value="{{ $instruktur->no_hp }}">
            </div>
            <div>
              <label class="f-label">Jenis Kelamin</label>
              <select name="jenis_kelamin" class="f-input">
                <option value="">—</option>
                <option value="L" {{ $instruktur->jenis_kelamin === 'L' ? 'selected' : '' }}>Laki-laki</option>
                <option value="P" {{ $instruktur->jenis_kelamin === 'P' ? 'selected' : '' }}>Perempuan</option>
              </select>
            </div>
          </div>
          <div>
            <label class="f-label">Bidang Keahlian</label>
            <input type="text" name="bidang_keahlian" class="f-input" value="{{ $instruktur->bidang_keahlian }}">
          </div>
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="f-label">Pendidikan Terakhir</label>
              <select name="pendidikan_terakhir" class="f-input">
                <option value="">—</option>
                @foreach(['S1','S2','S3'] as $p)
                  <option value="{{ $p }}" {{ $instruktur->pendidikan_terakhir === $p ? 'selected' : '' }}>{{ $p }}</option>
                @endforeach
              </select>
            </div>
            <div>
              <label class="f-label">Status <span class="text-rose-400">*</span></label>
              <select name="status" class="f-input" required>
                <option value="Aktif" {{ $instruktur->status === 'Aktif' ? 'selected' : '' }}>Aktif</option>
                <option value="Nonaktif" {{ $instruktur->status === 'Nonaktif' ? 'selected' : '' }}>Nonaktif</option>
              </select>
            </div>
          </div>
          <div class="flex items-center gap-2 pt-1">
            <button type="submit" id="btn-save-edit" class="px-4 py-2 rounded-xl text-[12.5px] font-semibold text-white a-grad">
              <i class="fa-solid fa-floppy-disk mr-1 text-[11px]"></i>Simpan
            </button>
            <button type="button" onclick="toggleEdit()"
              class="px-4 py-2 rounded-xl text-[12.5px] font-semibold border transition-colors"
              style="border-color:var(--border);color:var(--sub)"
              onmouseover="this.style.background='var(--surface2)'" onmouseout="this.style.background='transparent'">
              Batal
            </button>
          </div>
        </form>
        @endif
      </div>

    </div>

    {{-- ────── RIGHT ────── --}}
    <div class="lg:col-span-2 space-y-4">

      {{-- Stat --}}
      @php $totalKelas = $instruktur->kelas->count(); @endphp
      <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 animate-fadeUp d1">
        <div class="rounded-2xl border p-4 flex items-center gap-3" style="background:var(--surface);border-color:var(--border)">
          <div class="a-bg-lt a-text w-10 h-10 rounded-xl grid place-items-center flex-shrink-0">
            <i class="fa-solid fa-chalkboard-user text-[14px]"></i>
          </div>
          <div>
            <div class="font-display text-[22px] font-bold" style="color:var(--text)">{{ $totalKelas }}</div>
            <div class="text-[11px]" style="color:var(--muted)">Kelas Diampu</div>
          </div>
        </div>
        <div class="rounded-2xl border p-4 flex items-center gap-3" style="background:var(--surface);border-color:var(--border)">
          <div class="w-10 h-10 rounded-xl grid place-items-center flex-shrink-0" style="background:rgba(16,185,129,.14);color:#34d399">
            <i class="fa-solid fa-circle-play text-[14px]"></i>
          </div>
          <div>
            <div class="font-display text-[22px] font-bold" style="color:var(--text)">
              {{ $instruktur->kelas->where('status','Aktif')->count() }}
            </div>
            <div class="text-[11px]" style="color:var(--muted)">Kelas Aktif</div>
          </div>
        </div>
        <div class="rounded-2xl border p-4 flex items-center gap-3" style="background:var(--surface);border-color:var(--border)">
          <div class="w-10 h-10 rounded-xl grid place-items-center flex-shrink-0" style="background:rgba(99,102,241,.14);color:#818cf8">
            <i class="fa-solid fa-users text-[14px]"></i>
          </div>
          <div>
            <div class="font-display text-[22px] font-bold" style="color:var(--text)">
              {{ $instruktur->kelas->sum(fn($k) => $k->enrollments_count ?? 0) }}
            </div>
            <div class="text-[11px]" style="color:var(--muted)">Total Peserta</div>
          </div>
        </div>
      </div>

      {{-- Account management --}}
      <div class="rounded-2xl border overflow-hidden animate-fadeUp d2" style="background:var(--surface);border-color:var(--border)">
        <div class="px-5 py-4 border-b flex items-center gap-3" style="border-color:var(--border)">
          <div class="w-9 h-9 rounded-xl grid place-items-center text-[13px] a-bg-lt a-text">
            <i class="fa-solid fa-circle-user"></i>
          </div>
          <span class="font-display font-semibold text-[15px]" style="color:var(--text)">Manajemen Akun</span>
        </div>

        @if(!$hasAccount)
        {{-- No account --}}
        <div class="px-5 py-5">
          <div class="flex items-center gap-3 p-4 rounded-xl mb-4" style="background:var(--surface2)">
            <i class="fa-solid fa-user-slash text-[18px]" style="color:var(--muted)"></i>
            <div>
              <p class="text-[13px] font-semibold" style="color:var(--text)">Instruktur belum memiliki akun</p>
              <p class="text-[12px] mt-0.5" style="color:var(--muted)">Instruktur ini belum dapat login ke sistem.</p>
            </div>
          </div>

          @if($canCreateAccount)
          <form id="form-create-account" onsubmit="submitCreateAccount(event)" class="space-y-3">
            <p class="text-[12.5px] font-semibold uppercase tracking-wide mb-3" style="color:var(--muted)">Buat Akun Baru</p>
            <div>
              <label class="f-label">Nama Akun <span class="text-rose-400">*</span></label>
              <input type="text" name="name" class="f-input" value="{{ $instruktur->nama }}" required>
            </div>
            <div>
              <label class="f-label">Email Login <span class="text-rose-400">*</span></label>
              <input type="email" name="email" class="f-input"
                value="{{ $instruktur->email }}" required placeholder="email@domain.com">
            </div>
            <div>
              <label class="f-label">Password <span class="text-rose-400">*</span></label>
              <div class="relative">
                <input type="password" name="password" id="inp-password" class="f-input pr-10"
                  required minlength="8" placeholder="Min. 8 karakter">
                <button type="button" onclick="togglePw()"
                  class="absolute right-3 top-1/2 -translate-y-1/2 text-[13px]" style="color:var(--muted)">
                  <i id="pw-eye" class="fa-regular fa-eye"></i>
                </button>
              </div>
            </div>
            <button type="submit" id="btn-create-account"
              class="w-full px-4 py-2.5 rounded-xl text-[13px] font-semibold text-white a-grad shadow">
              <i class="fa-solid fa-user-plus mr-1.5 text-[11px]"></i>Buat Akun
            </button>
          </form>
          @else
          <p class="text-[12.5px]" style="color:var(--muted)">
            <i class="fa-solid fa-lock mr-1.5"></i>Anda tidak memiliki hak akses untuk membuat akun.
          </p>
          @endif
        </div>

        @else
        {{-- Has account --}}
        <div class="px-5 py-5 space-y-4">

          {{-- Account info --}}
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 p-4 rounded-xl" style="background:var(--surface2)">
            <div>
              <p class="text-[11px] uppercase tracking-wide font-semibold mb-1" style="color:var(--muted)">Nama Akun</p>
              <p class="text-[13px]" style="color:var(--text)">{{ $instruktur->user->name }}</p>
            </div>
            <div>
              <p class="text-[11px] uppercase tracking-wide font-semibold mb-1" style="color:var(--muted)">Email Login</p>
              <p class="text-[13px] break-all" style="color:var(--text)">{{ $instruktur->user->email }}</p>
            </div>
            <div>
              <p class="text-[11px] uppercase tracking-wide font-semibold mb-1" style="color:var(--muted)">Terdaftar</p>
              <p class="text-[13px]" style="color:var(--text)">{{ $instruktur->user->created_at->format('d M Y') }}</p>
            </div>
          </div>

          {{-- Actions row --}}
          <div class="flex flex-wrap gap-3">
            @if($canResetPassword)
            <button type="button" onclick="doResetPassword()"
              class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-[12.5px] font-semibold border transition-colors"
              style="border-color:#f59e0b;color:#f59e0b"
              onmouseover="this.style.background='rgba(245,158,11,.08)'" onmouseout="this.style.background='transparent'">
              <i class="fa-solid fa-key text-[11px]"></i> Reset Password
            </button>
            @endif

            <button type="button" onclick="document.getElementById('avatar-input').click()"
              class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-[12.5px] font-semibold border transition-colors"
              style="border-color:var(--border);color:var(--sub)"
              onmouseover="this.style.borderColor='var(--ac)';this.style.color='var(--ac)'"
              onmouseout="this.style.borderColor='var(--border)';this.style.color='var(--sub)'">
              <i class="fa-solid fa-camera text-[11px]"></i> Ubah Foto
            </button>
          </div>

          @if(!$canResetPassword)
          <p class="text-[12px]" style="color:var(--muted)">
            <i class="fa-solid fa-shield-halved mr-1"></i>
            Anda membutuhkan akses <code class="px-1.5 py-0.5 rounded text-[11px]" style="background:var(--surface2)">reset-password.user</code> untuk mereset password.
          </p>
          @endif
        </div>
        @endif
      </div>

      {{-- Kelas Diampu --}}
      <div class="rounded-2xl border overflow-hidden animate-fadeUp d3" style="background:var(--surface);border-color:var(--border)">
        <div class="px-5 py-4 border-b flex items-center justify-between" style="border-color:var(--border)">
          <span class="font-display font-semibold text-[15px]" style="color:var(--text)">Kelas yang Diampu</span>
          <span class="text-[12px] px-2.5 py-1 rounded-full font-semibold a-bg-lt a-text">
            {{ $instruktur->kelas->count() }} kelas
          </span>
        </div>

        @if($instruktur->kelas->isEmpty())
          <div class="py-12 text-center">
            <div class="a-bg-lt a-text w-12 h-12 rounded-2xl grid place-items-center text-xl mx-auto mb-3">
              <i class="fa-solid fa-chalkboard"></i>
            </div>
            <p class="text-[13px]" style="color:var(--muted)">Belum mengampu kelas apapun.</p>
          </div>
        @else
          <div style="overflow-x:auto">
            <table class="w-full text-[13px]">
              <thead>
                <tr style="background:var(--surface2)">
                  <th class="text-left px-5 py-3 text-[11px] font-semibold uppercase tracking-wide" style="color:var(--muted)">Mata Kuliah</th>
                  <th class="text-left px-4 py-3 text-[11px] font-semibold uppercase tracking-wide" style="color:var(--muted)">Periode</th>
                  <th class="text-center px-4 py-3 text-[11px] font-semibold uppercase tracking-wide" style="color:var(--muted)">Status</th>
                </tr>
              </thead>
              <tbody>
                @foreach($instruktur->kelas->sortByDesc(fn($k) => $k->periodeAkademik?->created_at) as $kelas)
                @php
                  $stCls = match($kelas->status) {
                    'Aktif'      => 'bg-emerald-500/15 text-emerald-400',
                    'Selesai'    => 'bg-blue-500/15 text-blue-400',
                    'Dibatalkan' => 'bg-rose-500/15 text-rose-400',
                    default      => 'bg-slate-500/15 text-slate-400',
                  };
                @endphp
                <tr class="border-t" style="border-color:var(--border)"
                  onmouseover="this.style.background='var(--surface2)'" onmouseout="this.style.background='transparent'">
                  <td class="px-5 py-3">
                    <div class="font-semibold" style="color:var(--text)">{{ $kelas->mataKuliah?->nama ?? '—' }}</div>
                    <div class="text-[11.5px] font-mono a-text">{{ $kelas->kodeDisplay }}</div>
                  </td>
                  <td class="px-4 py-3" style="color:var(--muted)">
                    {{ $kelas->periodeAkademik?->nama ?? '—' }}
                  </td>
                  <td class="px-4 py-3 text-center">
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold {{ $stCls }}">
                      <i class="fa-solid fa-circle text-[7px]"></i>{{ $kelas->status }}
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

{{-- Modal: new password result --}}
<div id="modal-new-password" class="modal-backdrop">
  <div class="modal-box" style="max-width:420px">
    <div class="p-6 text-center">
      <div class="w-14 h-14 rounded-2xl grid place-items-center text-2xl mx-auto mb-4"
        style="background:rgba(245,158,11,.12);color:#f59e0b">
        <i class="fa-solid fa-key"></i>
      </div>
      <h3 class="font-display font-bold text-[16px] mb-1" style="color:var(--text)">Password Berhasil Direset</h3>
      <p class="text-[12.5px] mb-4" style="color:var(--muted)">
        Salin password baru ini dan berikan kepada instruktur. Password tidak dapat dilihat kembali setelah ditutup.
      </p>
      <div class="flex items-center gap-2 p-3 rounded-xl mb-4" style="background:var(--surface2)">
        <code id="new-password-text" class="flex-1 font-mono text-[15px] font-bold text-left" style="color:var(--text)"></code>
        <button type="button" onclick="copyPassword()"
          class="px-3 py-1.5 rounded-lg text-[12px] font-semibold border transition-colors a-text"
          style="border-color:var(--border)"
          onmouseover="this.style.background='var(--surface)'" onmouseout="this.style.background='transparent'">
          <i class="fa-regular fa-copy"></i>
        </button>
      </div>
      <button type="button" onclick="closeModal('modal-new-password')"
        class="w-full px-4 py-2 rounded-xl text-[13px] font-semibold text-white a-grad">
        Selesai
      </button>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
const CSRF        = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
const UPDATE_URL  = '{{ route("admin.instruktur.update", $instruktur) }}';
const ACCOUNT_URL = '{{ route("admin.instruktur.create-account", $instruktur) }}';
const RESET_URL   = '{{ route("admin.instruktur.reset-password", $instruktur) }}';
const AVATAR_URL  = '{{ route("admin.instruktur.avatar", $instruktur) }}';

// ── Toast ───────────────────────────────────────────────────────
function showToast(type, message) {
  const container = document.getElementById('toast-container');
  const isSuccess = type === 'success';
  const toast = document.createElement('div');
  toast.className = 'toast';
  toast.innerHTML = `
    <div class="toast-icon" style="background:${isSuccess ? 'rgba(16,185,129,.15)' : 'rgba(248,113,113,.15)'};color:${isSuccess ? '#34d399' : '#f87171'}">
      <i class="fa-solid ${isSuccess ? 'fa-circle-check' : 'fa-circle-xmark'}"></i>
    </div>
    <div class="flex-1 min-w-0">
      <p style="font-size:13.5px;font-weight:600;color:var(--text)">${isSuccess ? 'Berhasil' : 'Gagal'}</p>
      <p style="font-size:12px;color:var(--muted);margin-top:2px">${message}</p>
    </div>
    <button onclick="dismissToast(this.closest('.toast'))" style="color:var(--muted);font-size:13px;padding:4px;flex-shrink:0">
      <i class="fa-solid fa-xmark"></i>
    </button>`;
  container.appendChild(toast);
  setTimeout(() => dismissToast(toast), 4000);
}
function dismissToast(toast) {
  if (!toast || toast.classList.contains('toast-out')) return;
  toast.classList.add('toast-out');
  setTimeout(() => toast.remove(), 300);
}

// ── Edit data pribadi ──────────────────────────────────────────
function toggleEdit() {
  const view = document.getElementById('detail-view');
  const form = document.getElementById('edit-form');
  const btn  = document.getElementById('btn-toggle-edit');
  const editing = form.style.display !== 'none';
  form.style.display = editing ? 'none' : '';
  view.style.display = editing ? '' : 'none';
  btn.innerHTML = editing
    ? '<i class="fa-solid fa-pen-to-square text-[11px]"></i> Edit'
    : '<i class="fa-solid fa-xmark text-[11px]"></i> Batal';
}

async function submitEdit(e) {
  e.preventDefault();
  const btn = document.getElementById('btn-save-edit');
  btn.disabled = true;
  btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1 text-[11px]"></i>Menyimpan...';
  try {
    const res  = await fetch(UPDATE_URL, {
      method: 'POST',
      body: new FormData(e.target),
      headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    });
    const json = await res.json();
    if (res.ok) {
      showToast('success', json.message);
      setTimeout(() => location.reload(), 900);
    } else {
      const msg = json.errors ? Object.values(json.errors)[0][0] : json.message;
      showToast('error', msg);
      btn.disabled = false;
      btn.innerHTML = '<i class="fa-solid fa-floppy-disk mr-1 text-[11px]"></i>Simpan';
    }
  } catch {
    showToast('error', 'Gagal terhubung ke server.');
    btn.disabled = false;
    btn.innerHTML = '<i class="fa-solid fa-floppy-disk mr-1 text-[11px]"></i>Simpan';
  }
}

// ── Create account ─────────────────────────────────────────────
@if($canCreateAccount && !$hasAccount)
async function submitCreateAccount(e) {
  e.preventDefault();
  const btn  = document.getElementById('btn-create-account');
  btn.disabled = true;
  btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1.5 text-[11px]"></i>Membuat akun...';
  try {
    const res  = await fetch(ACCOUNT_URL, {
      method: 'POST',
      body: new FormData(e.target),
      headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    });
    const json = await res.json();
    if (res.ok) {
      showToast('success', json.message);
      setTimeout(() => location.reload(), 1000);
    } else {
      const msg = json.errors ? Object.values(json.errors)[0][0] : json.message;
      showToast('error', msg);
      btn.disabled = false;
      btn.innerHTML = '<i class="fa-solid fa-user-plus mr-1.5 text-[11px]"></i>Buat Akun';
    }
  } catch {
    showToast('error', 'Gagal terhubung ke server.');
    btn.disabled = false;
  }
}

function togglePw() {
  const inp = document.getElementById('inp-password');
  const eye = document.getElementById('pw-eye');
  inp.type = inp.type === 'password' ? 'text' : 'password';
  eye.className = inp.type === 'password' ? 'fa-regular fa-eye' : 'fa-regular fa-eye-slash';
}
@endif

// ── Reset password ─────────────────────────────────────────────
@if($canResetPassword && $hasAccount)
async function doResetPassword() {
  if (!confirm('Reset password instruktur {{ addslashes($instruktur->nama) }}? Password lama tidak dapat digunakan lagi.')) return;
  try {
    const res  = await fetch(RESET_URL, {
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    });
    const json = await res.json();
    if (res.ok) {
      document.getElementById('new-password-text').textContent = json.new_password;
      openModal('modal-new-password');
    } else {
      showToast('error', json.message);
    }
  } catch { showToast('error', 'Gagal terhubung ke server.'); }
}
@endif

function copyPassword() {
  const text = document.getElementById('new-password-text').textContent;
  navigator.clipboard.writeText(text).then(() => showToast('success', 'Password disalin ke clipboard.'));
}

// ── Avatar upload ──────────────────────────────────────────────
@if($hasAccount)
async function uploadAvatar(input) {
  if (!input.files || !input.files[0]) return;
  const ring = document.querySelector('.avatar-ring');
  if (ring) {
    ring.classList.add('uploading');
    ring.querySelector('.icon-camera')?.style.setProperty('display','none');
    ring.querySelector('.icon-spin')?.style.setProperty('display','');
    ring.querySelector('.label-avatar') && (ring.querySelector('.label-avatar').textContent = 'Mengunggah...');
  }
  const form = new FormData();
  form.append('avatar', input.files[0]);
  form.append('_token', CSRF);
  try {
    const res  = await fetch(AVATAR_URL, {
      method: 'POST',
      body: form,
      headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    });
    const json = await res.json();
    if (res.ok) {
      const img = document.getElementById('avatar-img');
      const ph  = document.getElementById('avatar-placeholder');
      if (img) {
        img.src = json.url + '?t=' + Date.now();
      } else if (ph) {
        const newImg = document.createElement('img');
        newImg.id = 'avatar-img';
        newImg.src = json.url;
        newImg.alt = 'Avatar';
        newImg.className = 'w-20 h-20 rounded-full object-cover border-4';
        newImg.style.borderColor = 'var(--surface)';
        ph.replaceWith(newImg);
      }
      showToast('success', json.message);
    } else {
      showToast('error', json.message || 'Gagal mengunggah foto.');
    }
  } catch { showToast('error', 'Gagal terhubung ke server.'); }
  finally {
    if (ring) {
      ring.classList.remove('uploading');
      ring.querySelector('.icon-camera')?.style.setProperty('display','');
      ring.querySelector('.icon-spin')?.style.setProperty('display','none');
      ring.querySelector('.label-avatar') && (ring.querySelector('.label-avatar').textContent = 'Ubah Foto');
    }
    input.value = '';
  }
}
@endif
</script>
@endpush
