@extends('layouts.instruktur')
@section('title', 'Ubah Password')
@section('page-title', 'Ubah Password')

@push('styles')
<style>
.pw-card { background:var(--surface); border:1px solid var(--border); border-radius:18px; overflow:hidden; max-width:480px; margin:0 auto; }
.pw-head { padding:20px 24px; border-bottom:1px solid var(--border); display:flex; align-items:center; gap:12px; }
.pw-body { padding:24px; }

.pw-wrap { position:relative; }
.pw-wrap input { padding-right:42px; }
.pw-eye { position:absolute; right:11px; top:50%; transform:translateY(-50%); border:none; background:none; color:var(--muted); cursor:pointer; font-size:14px; padding:4px; line-height:1; }
.pw-eye:hover { color:var(--text); }

.strength-bar { height:4px; border-radius:99px; transition:width .3s, background .3s; }
</style>
@endpush

@section('content')
<div class="space-y-4 animate-fadeUp">

  {{-- Breadcrumb --}}
  <div class="flex items-center gap-2 text-[12px]" style="color:var(--muted)">
    <a href="{{ route('instruktur.profile') }}" class="a-text hover:underline">Profil Saya</a>
    <i class="fa-solid fa-chevron-right text-[10px]"></i>
    <span style="color:var(--text)">Ubah Password</span>
  </div>

  <div class="pw-card">
    <div class="pw-head">
      <div class="w-10 h-10 rounded-xl grid place-items-center"
        style="background:rgba(99,102,241,.14);color:#818cf8;font-size:16px">
        <i class="fa-solid fa-lock"></i>
      </div>
      <div>
        <h2 class="font-display font-bold text-[16px]" style="color:var(--text)">Ubah Password</h2>
        <p class="text-[12px] mt-0.5" style="color:var(--muted)">Gunakan password yang kuat dan unik</p>
      </div>
    </div>

    <div class="pw-body">
      @if(session('status') === 'password-updated')
        <div class="mb-4 px-4 py-3 rounded-xl flex items-center gap-2.5 text-[13px] font-medium"
          style="background:rgba(16,185,129,.1);color:#10b981;border:1px solid rgba(16,185,129,.2)">
          <i class="fa-solid fa-circle-check text-[15px]"></i>
          Password berhasil diperbarui. Silakan login ulang jika diminta.
        </div>
      @endif

      <form method="POST" action="{{ route('instruktur.profile.password.update') }}" class="space-y-4" id="pw-form">
        @csrf

        {{-- Password Saat Ini --}}
        <div>
          <label class="f-label">Password Saat Ini <span class="text-rose-400">*</span></label>
          <div class="pw-wrap">
            <input type="password" name="current_password" id="pw-current"
              class="f-input @error('current_password') is-invalid @enderror"
              autocomplete="current-password" placeholder="Masukkan password saat ini">
            <button type="button" class="pw-eye" onclick="togglePw('pw-current', this)">
              <i class="fa-regular fa-eye"></i>
            </button>
          </div>
          @error('current_password')
            <p class="f-error">{{ $message }}</p>
          @enderror
        </div>

        <div style="border-top:1px solid var(--border);margin:4px 0"></div>

        {{-- Password Baru --}}
        <div>
          <label class="f-label">Password Baru <span class="text-rose-400">*</span></label>
          <div class="pw-wrap">
            <input type="password" name="password" id="pw-new"
              class="f-input @error('password') is-invalid @enderror"
              autocomplete="new-password" placeholder="Minimal 8 karakter"
              oninput="checkStrength(this.value)">
            <button type="button" class="pw-eye" onclick="togglePw('pw-new', this)">
              <i class="fa-regular fa-eye"></i>
            </button>
          </div>
          {{-- Strength bar --}}
          <div class="mt-2" style="background:var(--surface2);border-radius:99px;height:4px">
            <div id="strength-bar" class="strength-bar" style="width:0%;background:#ef4444"></div>
          </div>
          <p id="strength-label" class="text-[11px] mt-1" style="color:var(--muted)"></p>
          @error('password')
            <p class="f-error">{{ $message }}</p>
          @enderror
        </div>

        {{-- Konfirmasi --}}
        <div>
          <label class="f-label">Konfirmasi Password Baru <span class="text-rose-400">*</span></label>
          <div class="pw-wrap">
            <input type="password" name="password_confirmation" id="pw-confirm"
              class="f-input" autocomplete="new-password" placeholder="Ulangi password baru"
              oninput="checkMatch()">
            <button type="button" class="pw-eye" onclick="togglePw('pw-confirm', this)">
              <i class="fa-regular fa-eye"></i>
            </button>
          </div>
          <p id="match-label" class="text-[11px] mt-1"></p>
        </div>

        <div class="flex items-center gap-3 pt-1">
          <button type="submit" id="btn-submit"
            class="flex-1 py-2.5 rounded-xl text-[13px] font-semibold text-white"
            style="background:linear-gradient(135deg,#6366f1,#8b5cf6)">
            <i class="fa-solid fa-key mr-1.5 text-[11px]"></i>Perbarui Password
          </button>
          <a href="{{ route('instruktur.profile') }}"
            class="px-4 py-2.5 rounded-xl text-[13px] font-semibold border transition-colors"
            style="border-color:var(--border);color:var(--sub)"
            onmouseover="this.style.borderColor='var(--ac)';this.style.color='var(--ac)'"
            onmouseout="this.style.borderColor='var(--border)';this.style.color='var(--sub)'">
            Batal
          </a>
        </div>
      </form>
    </div>
  </div>

</div>
@endsection

@push('scripts')
<script>
function togglePw(id, btn) {
  const input = document.getElementById(id);
  const show  = input.type === 'password';
  input.type  = show ? 'text' : 'password';
  btn.querySelector('i').className = show ? 'fa-regular fa-eye-slash' : 'fa-regular fa-eye';
}

function checkStrength(val) {
  const bar   = document.getElementById('strength-bar');
  const label = document.getElementById('strength-label');
  if (!val) { bar.style.width = '0%'; label.textContent = ''; return; }

  let score = 0;
  if (val.length >= 8)  score++;
  if (val.length >= 12) score++;
  if (/[A-Z]/.test(val)) score++;
  if (/[0-9]/.test(val)) score++;
  if (/[^A-Za-z0-9]/.test(val)) score++;

  const levels = [
    { pct:'20%', color:'#ef4444', text:'Sangat Lemah' },
    { pct:'40%', color:'#f97316', text:'Lemah' },
    { pct:'60%', color:'#f59e0b', text:'Cukup' },
    { pct:'80%', color:'#84cc16', text:'Kuat' },
    { pct:'100%',color:'#10b981', text:'Sangat Kuat' },
  ];
  const lvl = levels[Math.min(score - 1, 4)] || levels[0];
  bar.style.width      = lvl.pct;
  bar.style.background = lvl.color;
  label.textContent    = lvl.text;
  label.style.color    = lvl.color;
}

function checkMatch() {
  const pw   = document.getElementById('pw-new').value;
  const conf = document.getElementById('pw-confirm').value;
  const lbl  = document.getElementById('match-label');
  if (!conf) { lbl.textContent = ''; return; }
  if (pw === conf) {
    lbl.textContent  = '✓ Password cocok';
    lbl.style.color  = '#10b981';
  } else {
    lbl.textContent  = '✗ Password tidak cocok';
    lbl.style.color  = '#f87171';
  }
}
</script>
@endpush
