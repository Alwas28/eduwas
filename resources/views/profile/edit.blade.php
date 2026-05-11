@php
  $roles  = auth()->user()->roles->pluck('name')->map(fn($n) => strtolower($n));
  $layout = $roles->contains('mahasiswa') ? 'layouts.mahasiswa' : 'layouts.admin';
@endphp

@extends($layout)
@section('title', 'Profil Saya')
@section('page-title', 'Profil Saya')

@push('styles')
<style>
.avatar-ring { position:relative; display:inline-block; cursor:pointer }
.avatar-ring:hover .avatar-overlay { opacity:1 }
.avatar-overlay {
  position:absolute; inset:0; border-radius:9999px; background:rgba(0,0,0,.5);
  display:flex; align-items:center; justify-content:center;
  opacity:0; transition:opacity .2s; flex-direction:column; gap:4px;
}
</style>
@endpush

@section('content')
<div class="max-w-3xl mx-auto space-y-5">

  <div class="animate-fadeUp">
    <h2 class="font-display font-bold text-[20px]" style="color:var(--text)">Profil Saya</h2>
    <p class="text-[13px] mt-0.5" style="color:var(--muted)">Kelola informasi akun dan keamanan Anda</p>
  </div>

  {{-- ── AVATAR CARD ── --}}
  <div class="rounded-2xl border overflow-hidden animate-fadeUp d1" style="background:var(--surface);border-color:var(--border)">
    <div class="relative px-6 py-8 flex flex-col sm:flex-row items-center sm:items-start gap-6">
      <div class="absolute inset-0 opacity-[0.04] a-grad pointer-events-none"></div>

      <form method="POST" action="{{ route('profile.avatar') }}" enctype="multipart/form-data" id="avatar-form">
        @csrf
        <input type="file" id="avatar-input" name="avatar" accept="image/jpg,image/jpeg,image/png,image/webp"
          class="hidden" onchange="previewAvatar(this)">
        <label for="avatar-input" class="avatar-ring">
          @if(auth()->user()->avatarUrl())
            <img id="avatar-img" src="{{ auth()->user()->avatarUrl() }}" alt="Avatar"
              class="w-24 h-24 rounded-full object-cover border-4" style="border-color:var(--ac)">
          @else
            <div id="avatar-placeholder"
              class="a-grad w-24 h-24 rounded-full grid place-items-center font-display font-bold text-[28px] text-white">
              {{ auth()->user()->initials() }}
            </div>
            <img id="avatar-img" src="" alt="" class="hidden w-24 h-24 rounded-full object-cover border-4" style="border-color:var(--ac)">
          @endif
          <div class="avatar-overlay">
            <i class="fa-solid fa-camera text-white text-[16px]"></i>
            <span class="text-white text-[10px] font-semibold">Ubah Foto</span>
          </div>
        </label>
      </form>

      <div class="relative text-center sm:text-left">
        <h3 class="font-display font-bold text-[22px]" style="color:var(--text)">{{ auth()->user()->name }}</h3>
        <p class="text-[13px] mt-0.5" style="color:var(--muted)">{{ auth()->user()->email }}</p>
        <div class="flex items-center justify-center sm:justify-start gap-2 mt-2 flex-wrap">
          @foreach(auth()->user()->roles as $role)
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold a-bg-lt a-text">
              {{ $role->display_name ?? $role->name }}
            </span>
          @endforeach
        </div>
        <p class="text-[12px] mt-2" style="color:var(--muted)">
          <i class="fa-regular fa-clock mr-1"></i>Bergabung {{ auth()->user()->created_at->format('d M Y') }}
        </p>
        <div id="avatar-action" class="mt-3 items-center gap-2" style="display:none">
          <p class="text-[12px] font-medium text-amber-400">
            <i class="fa-solid fa-circle-exclamation mr-1"></i>Foto dipilih
          </p>
          <button type="button" onclick="document.getElementById('avatar-form').submit()"
            class="px-3 py-1.5 rounded-lg text-[12px] font-semibold text-white a-grad">
            <i class="fa-solid fa-floppy-disk mr-1"></i>Simpan
          </button>
        </div>
      </div>
    </div>

    @if(session('status') === 'avatar-updated')
      <div class="mx-6 mb-4 px-4 py-2.5 rounded-xl text-[12.5px] font-medium" style="background:rgba(16,185,129,.12);color:#10b981">
        <i class="fa-solid fa-circle-check mr-1.5"></i>Foto profil berhasil diperbarui.
      </div>
    @endif
    @error('avatar')
      <div class="mx-6 mb-4 px-4 py-2.5 rounded-xl text-[12.5px]" style="background:rgba(248,113,113,.1);color:#f87171">
        <i class="fa-solid fa-circle-xmark mr-1.5"></i>{{ $message }}
      </div>
    @enderror
  </div>

  {{-- ── INFORMASI AKUN ── --}}
  <div class="rounded-2xl border overflow-hidden animate-fadeUp d2" style="background:var(--surface);border-color:var(--border)">
    <div class="px-6 py-4 border-b flex items-center gap-3" style="border-color:var(--border)">
      <div class="a-bg-lt a-text w-9 h-9 rounded-xl grid place-items-center text-[13px]"><i class="fa-solid fa-user"></i></div>
      <span class="font-display font-semibold text-[15px]" style="color:var(--text)">Informasi Akun</span>
    </div>
    <form method="POST" action="{{ route('profile.update') }}" class="px-6 py-5 space-y-4">
      @csrf @method('PATCH')
      @if(session('status') === 'profile-updated')
        <div class="px-4 py-2.5 rounded-xl text-[12.5px] font-medium" style="background:rgba(16,185,129,.12);color:#10b981">
          <i class="fa-solid fa-circle-check mr-1.5"></i>Informasi akun berhasil diperbarui.
        </div>
      @endif
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label class="f-label">Nama Lengkap <span style="color:#f87171">*</span></label>
          <input type="text" name="name" class="f-input @error('name') is-invalid @enderror"
            value="{{ old('name', auth()->user()->name) }}" required>
          @error('name')<p class="f-error">{{ $message }}</p>@enderror
        </div>
        <div>
          <label class="f-label">Alamat Email <span style="color:#f87171">*</span></label>
          <input type="email" name="email" class="f-input @error('email') is-invalid @enderror"
            value="{{ old('email', auth()->user()->email) }}" required>
          @error('email')<p class="f-error">{{ $message }}</p>@enderror
        </div>
      </div>
      <div class="flex justify-end">
        <button type="submit" class="px-5 py-2 rounded-xl text-[13px] font-semibold text-white a-grad shadow">
          <i class="fa-solid fa-floppy-disk mr-1.5 text-[11px]"></i>Simpan Perubahan
        </button>
      </div>
    </form>
  </div>

  {{-- ── UBAH PASSWORD ── --}}
  <div class="rounded-2xl border overflow-hidden animate-fadeUp d3" style="background:var(--surface);border-color:var(--border)">
    <div class="px-6 py-4 border-b flex items-center justify-between" style="border-color:var(--border)">
      <div class="flex items-center gap-3">
        <div class="w-9 h-9 rounded-xl grid place-items-center text-[13px]" style="background:rgba(99,102,241,.14);color:#818cf8">
          <i class="fa-solid fa-lock"></i>
        </div>
        <span class="font-display font-semibold text-[15px]" style="color:var(--text)">Ubah Password</span>
      </div>
      @if(!$roles->contains('mahasiswa'))
      <a href="{{ route('admin.keamanan.index') }}" class="text-[12px] font-semibold a-text hover:underline flex items-center gap-1">
        <i class="fa-solid fa-shield-halved text-[10px]"></i>Pusat Keamanan
      </a>
      @endif
    </div>
    <form method="POST" action="{{ route('password.update') }}" class="px-6 py-5 space-y-4">
      @csrf @method('PUT')
      @if(session('status') === 'password-updated')
        <div class="px-4 py-2.5 rounded-xl text-[12.5px] font-medium" style="background:rgba(16,185,129,.12);color:#10b981">
          <i class="fa-solid fa-circle-check mr-1.5"></i>Password berhasil diperbarui.
        </div>
      @endif
      <div>
        <label class="f-label">Password Saat Ini <span style="color:#f87171">*</span></label>
        <input type="password" name="current_password"
          class="f-input @error('current_password', 'updatePassword') is-invalid @enderror"
          autocomplete="current-password">
        @error('current_password', 'updatePassword')<p class="f-error">{{ $message }}</p>@enderror
      </div>
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label class="f-label">Password Baru <span style="color:#f87171">*</span></label>
          <input type="password" name="password"
            class="f-input @error('password', 'updatePassword') is-invalid @enderror"
            autocomplete="new-password">
          @error('password', 'updatePassword')<p class="f-error">{{ $message }}</p>@enderror
        </div>
        <div>
          <label class="f-label">Konfirmasi Password Baru <span style="color:#f87171">*</span></label>
          <input type="password" name="password_confirmation" class="f-input" autocomplete="new-password">
        </div>
      </div>
      <div class="flex justify-end">
        <button type="submit" class="px-5 py-2 rounded-xl text-[13px] font-semibold text-white"
          style="background:linear-gradient(135deg,#6366f1,#8b5cf6);box-shadow:0 4px 12px rgba(99,102,241,.3)">
          <i class="fa-solid fa-key mr-1.5 text-[11px]"></i>Perbarui Password
        </button>
      </div>
    </form>
  </div>

  {{-- ── INFO AKUN (Admin only) ── --}}
  @if(!$roles->contains('mahasiswa'))
  <div class="rounded-2xl border overflow-hidden animate-fadeUp d4" style="background:var(--surface);border-color:var(--border)">
    <div class="px-6 py-4 border-b flex items-center gap-3" style="border-color:var(--border)">
      <div class="w-9 h-9 rounded-xl grid place-items-center text-[13px]" style="background:rgba(16,185,129,.12);color:#10b981">
        <i class="fa-solid fa-shield-halved"></i>
      </div>
      <span class="font-display font-semibold text-[15px]" style="color:var(--text)">Informasi Administrator</span>
    </div>
    <div class="px-6 py-5">
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="rounded-xl p-4" style="background:var(--surface2)">
          <p class="text-[11px] font-semibold uppercase tracking-wider mb-1.5" style="color:var(--muted)">Role</p>
          <div class="flex flex-wrap gap-1.5">
            @foreach(auth()->user()->roles as $r)
              <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold a-bg-lt a-text">
                <i class="fa-solid fa-circle-check text-[9px]"></i>{{ $r->display_name ?? $r->name }}
              </span>
            @endforeach
          </div>
        </div>
        <div class="rounded-xl p-4" style="background:var(--surface2)">
          <p class="text-[11px] font-semibold uppercase tracking-wider mb-1.5" style="color:var(--muted)">Bergabung Sejak</p>
          <p class="text-[13px] font-semibold" style="color:var(--text)">
            <i class="fa-regular fa-calendar mr-1.5 a-text"></i>{{ auth()->user()->created_at->format('d M Y') }}
          </p>
          <p class="text-[11px] mt-0.5" style="color:var(--muted)">{{ auth()->user()->created_at->diffForHumans() }}</p>
        </div>
        <div class="rounded-xl p-4" style="background:var(--surface2)">
          <p class="text-[11px] font-semibold uppercase tracking-wider mb-1.5" style="color:var(--muted)">Status Akun</p>
          <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold" style="background:rgba(16,185,129,.12);color:#10b981">
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>Aktif
          </span>
          <p class="text-[11px] mt-2" style="color:var(--muted)">
            <a href="{{ route('admin.keamanan.index') }}" class="a-text hover:underline">
              <i class="fa-solid fa-lock mr-1"></i>Pengaturan Keamanan →
            </a>
          </p>
        </div>
      </div>
    </div>
  </div>
  @endif

</div>
@endsection

@push('scripts')
<script>
function previewAvatar(input) {
  if (!input.files || !input.files[0]) return;
  const reader = new FileReader();
  reader.onload = e => {
    const img = document.getElementById('avatar-img');
    const ph  = document.getElementById('avatar-placeholder');
    img.src = e.target.result;
    img.classList.remove('hidden');
    if (ph) ph.classList.add('hidden');
  };
  reader.readAsDataURL(input.files[0]);
  document.getElementById('avatar-action').style.display = 'flex';
}
</script>
@endpush
