<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Daftar Akun — EduWAS</title>
<link href="https://fonts.googleapis.com/css2?family=Clash+Display:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
@vite(['resources/css/app.css', 'resources/js/app.js'])
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{
  font-family:'Plus Jakarta Sans',sans-serif;
  --bg:#0a0e1a;--surface:#111827;--surface2:#161d2e;
  --border:#1e2a42;--text:#e2e8f0;--muted:#64748b;--sub:#94a3b8;
  --ac:#10b981;--ac2:#06b6d4;--ac-rgb:16,185,129;--ac-lt:rgba(16,185,129,.14);
  background:var(--bg);color:var(--text);min-height:100vh;
  display:flex;flex-direction:column;
}
h1,h2,h3{font-family:'Clash Display',sans-serif}

/* ── Navbar ── */
nav{
  height:60px;flex-shrink:0;
  background:rgba(10,14,26,.9);backdrop-filter:blur(12px);
  border-bottom:1px solid var(--border);
  display:flex;align-items:center;padding:0 28px;
  position:sticky;top:0;z-index:50;
}
.nav-inner{width:100%;display:flex;align-items:center;justify-content:space-between}
.nav-logo{display:flex;align-items:center;gap:10px;text-decoration:none}
.nav-logo-icon{width:34px;height:34px;border-radius:9px;background:linear-gradient(135deg,var(--ac),var(--ac2));display:grid;place-items:center;font-size:15px;color:#fff;flex-shrink:0}
.nav-logo-text{font-family:'Clash Display',sans-serif;font-size:17px;font-weight:700;background:linear-gradient(135deg,var(--ac),var(--ac2));-webkit-background-clip:text;-webkit-text-fill-color:transparent}
.nav-btn{display:inline-flex;align-items:center;gap:7px;padding:7px 16px;border-radius:9px;font-size:13px;font-weight:600;font-family:inherit;cursor:pointer;border:none;text-decoration:none;transition:all .15s}
.nav-btn-outline{background:transparent;border:1.5px solid var(--border);color:var(--sub)}
.nav-btn-outline:hover{border-color:var(--ac);color:var(--ac)}
.nav-btn-primary{background:linear-gradient(135deg,var(--ac),var(--ac2));color:#fff}
.nav-btn-primary:hover{opacity:.88;transform:translateY(-1px)}

/* ── Main layout ── */
.auth-wrap{display:flex;flex:1;min-height:0}
.auth-left{
  flex:0 0 420px;background:var(--surface);border-right:1px solid var(--border);
  display:flex;flex-direction:column;justify-content:space-between;padding:48px 40px;
  position:relative;overflow:hidden;
}
.left-glow{position:absolute;border-radius:50%;filter:blur(110px);pointer-events:none}
.left-glow-1{width:380px;height:380px;background:var(--ac);opacity:.09;top:-80px;left:-80px}
.left-glow-2{width:280px;height:280px;background:var(--ac2);opacity:.07;bottom:-40px;right:-40px}
.left-grid{position:absolute;inset:0;background-image:linear-gradient(var(--border) 1px,transparent 1px),linear-gradient(90deg,var(--border) 1px,transparent 1px);background-size:48px 48px;opacity:.22}
.left-content{position:relative;z-index:1}
.auth-right{flex:1;display:flex;align-items:center;justify-content:center;padding:40px 24px;overflow-y:auto}
.auth-form-box{width:100%;max-width:480px}

/* ── Left panel content ── */
.left-logo{display:flex;align-items:center;gap:10px;text-decoration:none;margin-bottom:36px}
.left-title{font-size:28px;font-weight:700;line-height:1.3;color:var(--text);margin-bottom:12px}
.left-title span{background:linear-gradient(135deg,var(--ac),var(--ac2));-webkit-background-clip:text;-webkit-text-fill-color:transparent}
.left-desc{font-size:13.5px;color:var(--muted);line-height:1.75;margin-bottom:32px}
.feature-item{display:flex;align-items:flex-start;gap:12px;margin-bottom:18px}
.feature-icon{width:36px;height:36px;border-radius:10px;display:grid;place-items:center;flex-shrink:0;font-size:14px}
.feature-title{font-size:13.5px;font-weight:600;color:var(--text);margin-bottom:2px}
.feature-desc{font-size:12px;color:var(--muted);line-height:1.5}

/* ── Inputs ── */
.f-label{display:block;font-size:11.5px;font-weight:700;color:var(--muted);margin-bottom:6px;letter-spacing:.4px;text-transform:uppercase}
.f-input{
  width:100%;background:var(--surface2);border:1.5px solid var(--border);color:var(--text);
  border-radius:11px;padding:10px 14px;font-size:13.5px;font-family:inherit;outline:none;
  transition:border-color .15s,box-shadow .15s;
}
.f-input:focus{border-color:var(--ac);box-shadow:0 0 0 3px var(--ac-lt)}
.f-input::placeholder{color:var(--muted)}
.f-input.is-invalid{border-color:#f87171;box-shadow:0 0 0 3px rgba(248,113,113,.12)}
.f-error{font-size:12px;color:#f87171;margin-top:5px;display:flex;align-items:center;gap:5px}
select.f-input option{background:var(--surface2);color:var(--text)}
.pw-wrap{position:relative}
.pw-wrap .f-input{padding-right:44px}
.pw-toggle{position:absolute;right:13px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:var(--muted);font-size:14px;padding:4px;transition:color .15s}
.pw-toggle:hover{color:var(--sub)}

/* ── Form header ── */
.form-title{font-size:24px;font-weight:700;color:var(--text);margin-bottom:4px}
.form-sub{font-size:13.5px;color:var(--muted);margin-bottom:26px}

/* ── Field groups ── */
.f-group{margin-bottom:14px}
.f-grid-2{display:grid;grid-template-columns:1fr 1fr;gap:14px}
@media(max-width:520px){.f-grid-2{grid-template-columns:1fr}}

/* ── Submit btn ── */
.btn-submit{
  width:100%;padding:12px;border-radius:12px;border:none;cursor:pointer;
  font-family:inherit;font-size:14px;font-weight:700;color:#fff;
  background:linear-gradient(135deg,var(--ac),var(--ac2));
  box-shadow:0 4px 20px rgba(var(--ac-rgb),.3);
  transition:opacity .15s,transform .1s;
  display:flex;align-items:center;justify-content:center;gap:8px;
}
.btn-submit:hover{opacity:.9;transform:translateY(-1px)}
.btn-submit:active{transform:translateY(0)}
.btn-submit:disabled{opacity:.6;cursor:not-allowed;transform:none}

@media(max-width:860px){
  .auth-left{display:none}
  .auth-right{padding:28px 16px}
}
</style>
</head>
<body>

{{-- ── Navbar ── --}}
<nav>
  <div class="nav-inner">
    <a href="/" class="nav-logo">
      <div class="nav-logo-icon"><i class="fas fa-graduation-cap"></i></div>
      <span class="nav-logo-text">EduWAS</span><span style="font-size:9px;color:var(--muted);display:block;margin-top:-2px;letter-spacing:.3px;font-family:sans-serif;font-weight:400;">Education With AI System</span>
    </a>
    <div style="display:flex;align-items:center;gap:8px;">
      <span style="font-size:13px;color:var(--muted);">Sudah punya akun?</span>
      <a href="{{ route('login') }}" class="nav-btn nav-btn-outline">
        <i class="fas fa-sign-in-alt"></i> Masuk
      </a>
    </div>
  </div>
</nav>

<div class="auth-wrap">

  {{-- ── LEFT PANEL ── --}}
  <div class="auth-left">
    <div class="left-glow left-glow-1"></div>
    <div class="left-glow left-glow-2"></div>
    <div class="left-grid"></div>

    <div class="left-content">
      <h1 class="left-title">Mulai perjalanan<br>belajarmu <span>hari ini</span></h1>
      <p class="left-desc">Daftarkan dirimu dan akses semua materi, tugas, ujian, dan kelas yang tersedia di EduWAS.</p>

      <div>
        <div class="feature-item">
          <div class="feature-icon" style="background:rgba(var(--ac-rgb),.13);color:var(--ac);">
            <i class="fas fa-book-open-reader"></i>
          </div>
          <div>
            <div class="feature-title">Akses Materi Kuliah</div>
            <div class="feature-desc">Materi, referensi, dan konten perkuliahan tersedia kapan saja</div>
          </div>
        </div>
        <div class="feature-item">
          <div class="feature-icon" style="background:rgba(6,182,212,.13);color:#22d3ee;">
            <i class="fas fa-clipboard-check"></i>
          </div>
          <div>
            <div class="feature-title">Tugas &amp; Ujian Online</div>
            <div class="feature-desc">Submit tugas dan ikuti ujian langsung dari browser</div>
          </div>
        </div>
        <div class="feature-item">
          <div class="feature-icon" style="background:rgba(245,158,11,.13);color:#f59e0b;">
            <i class="fas fa-chart-line"></i>
          </div>
          <div>
            <div class="feature-title">Pantau Perkembangan</div>
            <div class="feature-desc">Nilai, kehadiran, dan progress belajar dalam satu tempat</div>
          </div>
        </div>
        <div class="feature-item">
          <div class="feature-icon" style="background:rgba(99,102,241,.13);color:#818cf8;">
            <i class="fas fa-robot"></i>
          </div>
          <div>
            <div class="feature-title">AI Grading &amp; Tutor</div>
            <div class="feature-desc">Penilaian otomatis dan asisten belajar berbasis AI</div>
          </div>
        </div>
      </div>
    </div>

    <p style="font-size:12px;color:var(--muted);position:relative;z-index:1;">&copy; {{ date('Y') }} EduWAS. Semua hak dilindungi.</p>
  </div>

  {{-- ── RIGHT FORM ── --}}
  <div class="auth-right">
    <div class="auth-form-box">

      <div class="form-title">Buat Akun Baru</div>
      <p class="form-sub">Isi data diri Anda untuk mendaftar sebagai peserta</p>

      <form method="POST" action="{{ route('register') }}" id="reg-form">
        @csrf

        {{-- Nama --}}
        <div class="f-group">
          <label class="f-label">Nama Lengkap <span style="color:#f87171">*</span></label>
          <input type="text" name="nama" class="f-input @error('nama') is-invalid @enderror"
            value="{{ old('nama') }}" placeholder="Nama sesuai dokumen resmi" autofocus>
          @error('nama')<p class="f-error"><i class="fas fa-circle-exclamation" style="font-size:10px;"></i> {{ $message }}</p>@enderror
        </div>

        {{-- NIM + Angkatan --}}
        <div class="f-group f-grid-2">
          <div>
            <label class="f-label">NIM <span style="color:#f87171">*</span></label>
            <input type="text" name="nim" class="f-input @error('nim') is-invalid @enderror"
              value="{{ old('nim') }}" placeholder="cth: 2024001001">
            @error('nim')<p class="f-error"><i class="fas fa-circle-exclamation" style="font-size:10px;"></i> {{ $message }}</p>@enderror
          </div>
          <div>
            <label class="f-label">Angkatan <span style="color:#f87171">*</span></label>
            <input type="number" name="angkatan" class="f-input @error('angkatan') is-invalid @enderror"
              value="{{ old('angkatan', date('Y')) }}" min="2000" max="{{ date('Y') + 1 }}" placeholder="{{ date('Y') }}">
            @error('angkatan')<p class="f-error"><i class="fas fa-circle-exclamation" style="font-size:10px;"></i> {{ $message }}</p>@enderror
          </div>
        </div>

        {{-- Email --}}
        <div class="f-group">
          <label class="f-label">Email Universitas <span style="color:#f87171">*</span></label>
          <div style="display:flex;align-items:stretch;gap:0;">
            <input type="text" name="email_username" id="email_username"
              class="f-input @error('email_username') is-invalid @enderror"
              style="border-radius:11px 0 0 11px;flex:1;"
              value="{{ old('email_username') }}"
              placeholder="username.nim"
              autocomplete="username" spellcheck="false">
            <div style="
              background:var(--surface2);border:1.5px solid var(--border);
              border-left:none;border-radius:0 11px 11px 0;
              padding:10px 14px;font-size:13.5px;color:var(--muted);
              white-space:nowrap;display:flex;align-items:center;
              font-weight:600;
            ">@umkendari.ac.id</div>
          </div>
          @error('email_username')<p class="f-error"><i class="fas fa-circle-exclamation" style="font-size:10px;"></i> {{ $message }}</p>@enderror
        </div>

        {{-- Jurusan + Jenis Kelamin --}}
        <div class="f-group f-grid-2">
          <div>
            <label class="f-label">Jurusan <span style="color:#f87171">*</span></label>
            <select name="jurusan_id" class="f-input @error('jurusan_id') is-invalid @enderror">
              <option value="">— Pilih Jurusan —</option>
              @foreach($jurusans as $j)
                <option value="{{ $j->id }}" {{ old('jurusan_id') == $j->id ? 'selected' : '' }}>{{ $j->nama }}</option>
              @endforeach
            </select>
            @error('jurusan_id')<p class="f-error"><i class="fas fa-circle-exclamation" style="font-size:10px;"></i> {{ $message }}</p>@enderror
          </div>
          <div>
            <label class="f-label">Jenis Kelamin <span style="color:#f87171">*</span></label>
            <select name="jenis_kelamin" class="f-input @error('jenis_kelamin') is-invalid @enderror">
              <option value="">— Pilih —</option>
              <option value="L" {{ old('jenis_kelamin') === 'L' ? 'selected' : '' }}>Laki-laki</option>
              <option value="P" {{ old('jenis_kelamin') === 'P' ? 'selected' : '' }}>Perempuan</option>
            </select>
            @error('jenis_kelamin')<p class="f-error"><i class="fas fa-circle-exclamation" style="font-size:10px;"></i> {{ $message }}</p>@enderror
          </div>
        </div>

        {{-- Password --}}
        <div class="f-group">
          <label class="f-label">Password <span style="color:#f87171">*</span></label>
          <div class="pw-wrap">
            <input type="password" name="password" id="pw1"
              class="f-input @error('password') is-invalid @enderror"
              placeholder="Minimal 8 karakter" autocomplete="new-password">
            <button type="button" class="pw-toggle" onclick="togglePw('pw1',this)">
              <i class="fa-regular fa-eye"></i>
            </button>
          </div>
          @error('password')<p class="f-error"><i class="fas fa-circle-exclamation" style="font-size:10px;"></i> {{ $message }}</p>@enderror
        </div>

        {{-- Konfirmasi Password --}}
        <div class="f-group">
          <label class="f-label">Konfirmasi Password <span style="color:#f87171">*</span></label>
          <div class="pw-wrap">
            <input type="password" name="password_confirmation" id="pw2"
              class="f-input" placeholder="Ulangi password" autocomplete="new-password">
            <button type="button" class="pw-toggle" onclick="togglePw('pw2',this)">
              <i class="fa-regular fa-eye"></i>
            </button>
          </div>
        </div>

        {{-- Submit --}}
        <div style="margin-top:20px;">
          <button type="submit" id="btn-reg" class="btn-submit">
            <i class="fas fa-user-plus"></i> Daftarkan Akun
          </button>
        </div>

      </form>

      <p style="text-align:center;font-size:13px;color:var(--muted);margin-top:20px;">
        Sudah punya akun?
        <a href="{{ route('login') }}" style="color:var(--ac);font-weight:600;text-decoration:none;">Masuk di sini</a>
      </p>

    </div>
  </div>

</div>

<script>
function togglePw(id, btn) {
  const input = document.getElementById(id);
  const icon  = btn.querySelector('i');
  const hide  = input.type === 'password';
  input.type  = hide ? 'text' : 'password';
  icon.className = hide ? 'fa-regular fa-eye-slash' : 'fa-regular fa-eye';
}

document.getElementById('reg-form').addEventListener('submit', function() {
  const btn = document.getElementById('btn-reg');
  btn.disabled = true;
  btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses…';
});
</script>
</body>
</html>
