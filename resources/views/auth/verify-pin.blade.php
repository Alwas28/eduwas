<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Verifikasi Email — EduLearn</title>
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

/* Navbar */
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

/* Main layout */
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
.auth-form-box{width:100%;max-width:420px}

/* Left panel */
.left-title{font-size:28px;font-weight:700;line-height:1.3;color:var(--text);margin-bottom:12px}
.left-title span{background:linear-gradient(135deg,var(--ac),var(--ac2));-webkit-background-clip:text;-webkit-text-fill-color:transparent}
.left-desc{font-size:13.5px;color:var(--muted);line-height:1.75;margin-bottom:32px}
.step-item{display:flex;align-items:flex-start;gap:14px;margin-bottom:20px}
.step-num{width:32px;height:32px;border-radius:50%;background:rgba(var(--ac-rgb),.13);border:1.5px solid rgba(var(--ac-rgb),.3);color:var(--ac);font-weight:700;font-size:13px;display:grid;place-items:center;flex-shrink:0}
.step-title{font-size:13.5px;font-weight:600;color:var(--text);margin-bottom:2px}
.step-desc{font-size:12px;color:var(--muted);line-height:1.5}

/* Form header */
.form-title{font-size:24px;font-weight:700;color:var(--text);margin-bottom:4px}
.form-sub{font-size:13.5px;color:var(--muted);margin-bottom:8px}
.email-badge{
  display:inline-flex;align-items:center;gap:7px;
  background:rgba(var(--ac-rgb),.1);border:1px solid rgba(var(--ac-rgb),.25);
  border-radius:8px;padding:5px 12px;font-size:12.5px;color:var(--ac);
  font-weight:600;margin-bottom:28px;
}

/* PIN boxes */
.pin-row{display:flex;gap:10px;justify-content:center;margin-bottom:24px}
.pin-box{
  width:52px;height:60px;border-radius:12px;
  background:var(--surface2);border:2px solid var(--border);
  font-size:26px;font-weight:700;color:var(--ac);
  font-family:'Courier New',monospace;
  text-align:center;outline:none;caret-color:var(--ac);
  transition:border-color .15s,box-shadow .15s;
}
.pin-box:focus{border-color:var(--ac);box-shadow:0 0 0 3px var(--ac-lt)}
.pin-box.is-invalid{border-color:#f87171;box-shadow:0 0 0 3px rgba(248,113,113,.12)}

/* Error / success banners */
.f-error-box{
  background:rgba(248,113,113,.1);border:1px solid rgba(248,113,113,.3);
  border-radius:10px;padding:10px 14px;font-size:13px;color:#fca5a5;
  display:flex;align-items:center;gap:9px;margin-bottom:16px;
}
.f-success-box{
  background:rgba(var(--ac-rgb),.1);border:1px solid rgba(var(--ac-rgb),.3);
  border-radius:10px;padding:10px 14px;font-size:13px;color:var(--ac);
  display:flex;align-items:center;gap:9px;margin-bottom:16px;
}

/* Submit */
.btn-submit{
  width:100%;padding:12px;border-radius:12px;border:none;cursor:pointer;
  font-family:inherit;font-size:14px;font-weight:700;color:#fff;
  background:linear-gradient(135deg,var(--ac),var(--ac2));
  box-shadow:0 4px 20px rgba(var(--ac-rgb),.3);
  transition:opacity .15s,transform .1s;
  display:flex;align-items:center;justify-content:center;gap:8px;
}
.btn-submit:hover{opacity:.9;transform:translateY(-1px)}
.btn-submit:disabled{opacity:.6;cursor:not-allowed;transform:none}

/* Resend */
.resend-row{text-align:center;font-size:13px;color:var(--muted);margin-top:16px}
.btn-resend{background:none;border:none;cursor:pointer;color:var(--ac);font-weight:600;font-size:13px;font-family:inherit;padding:0;transition:opacity .15s}
.btn-resend:hover{opacity:.75}
.btn-resend:disabled{color:var(--muted);cursor:not-allowed}

.divider{height:1px;background:var(--border);margin:20px 0}
.logout-link{display:flex;align-items:center;gap:6px;font-size:12.5px;color:var(--muted);text-decoration:none;justify-content:center;transition:color .15s}
.logout-link:hover{color:var(--sub)}

@media(max-width:860px){
  .auth-left{display:none}
  .auth-right{padding:28px 16px}
}
</style>
</head>
<body>

<nav>
  <div class="nav-inner">
    <a href="/" class="nav-logo">
      <div class="nav-logo-icon"><i class="fas fa-graduation-cap"></i></div>
      <span class="nav-logo-text">EduLearn</span>
    </a>
  </div>
</nav>

<div class="auth-wrap">

  {{-- LEFT PANEL --}}
  <div class="auth-left">
    <div class="left-glow left-glow-1"></div>
    <div class="left-glow left-glow-2"></div>
    <div class="left-grid"></div>
    <div class="left-content">
      <h1 class="left-title">Hampir selesai!<br>Verifikasi <span>emailmu</span></h1>
      <p class="left-desc">Kami mengirimkan kode 6 digit ke email universitas Anda. Masukkan kode tersebut untuk mengaktifkan akun.</p>
      <div>
        <div class="step-item">
          <div class="step-num">1</div>
          <div>
            <div class="step-title">Cek Kotak Masuk Email</div>
            <div class="step-desc">Buka email @umkendari.ac.id Anda di browser atau aplikasi mail</div>
          </div>
        </div>
        <div class="step-item">
          <div class="step-num">2</div>
          <div>
            <div class="step-title">Temukan Email dari EduLearn</div>
            <div class="step-desc">Cari subjek "Kode Verifikasi Email — EduLearn"</div>
          </div>
        </div>
        <div class="step-item">
          <div class="step-num">3</div>
          <div>
            <div class="step-title">Masukkan 6 Digit Kode</div>
            <div class="step-desc">Ketik atau tempel kode verifikasi di formulir ini</div>
          </div>
        </div>
        <div class="step-item">
          <div class="step-num">4</div>
          <div>
            <div class="step-title">Akun Aktif &amp; Siap Belajar</div>
            <div class="step-desc">Setelah terverifikasi, Anda langsung dapat mengakses semua fitur</div>
          </div>
        </div>
      </div>
    </div>
    <p style="font-size:12px;color:var(--muted);position:relative;z-index:1;">&copy; {{ date('Y') }} EduLearn. Semua hak dilindungi.</p>
  </div>

  {{-- RIGHT FORM --}}
  <div class="auth-right">
    <div class="auth-form-box">

      <div style="text-align:center;margin-bottom:28px;">
        <div style="width:64px;height:64px;border-radius:18px;background:linear-gradient(135deg,var(--ac),var(--ac2));display:grid;place-items:center;font-size:28px;color:#fff;margin:0 auto 16px;">
          <i class="fas fa-envelope-open-text"></i>
        </div>
        <div class="form-title">Verifikasi Email</div>
        <p class="form-sub">Kode dikirim ke:</p>
        <div class="email-badge">
          <i class="fas fa-at" style="font-size:11px;"></i>
          {{ auth()->user()->email }}
        </div>
      </div>

      {{-- Success (resent) --}}
      @if(session('resent'))
      <div class="f-success-box">
        <i class="fas fa-circle-check"></i>
        Kode baru telah dikirim! Periksa inbox email Anda.
      </div>
      @endif

      {{-- Error --}}
      @error('pin')
      <div class="f-error-box">
        <i class="fas fa-circle-exclamation"></i>
        {{ $message }}
      </div>
      @enderror

      <form method="POST" action="{{ route('verification.pin.verify') }}" id="pin-form">
        @csrf
        <input type="hidden" name="pin" id="pin-hidden">

        <p style="text-align:center;font-size:12px;color:var(--muted);margin-bottom:14px;letter-spacing:.4px;text-transform:uppercase;font-weight:700;">
          Masukkan Kode 6 Digit
        </p>

        <div class="pin-row" id="pin-row">
          @for($i = 0; $i < 6; $i++)
          <input type="text" class="pin-box @error('pin') is-invalid @enderror"
            maxlength="1" inputmode="numeric" pattern="[0-9]"
            autocomplete="one-time-code" data-idx="{{ $i }}">
          @endfor
        </div>

        <button type="submit" id="btn-verify" class="btn-submit" disabled>
          <i class="fas fa-shield-check"></i> Verifikasi Akun
        </button>
      </form>

      <div class="resend-row" style="margin-top:18px;">
        Tidak menerima kode?
        <form method="POST" action="{{ route('verification.pin.resend') }}" style="display:inline;">
          @csrf
          <button type="submit" class="btn-resend" id="btn-resend">
            Kirim Ulang
          </button>
        </form>
        <span id="cooldown-msg" style="display:none;color:var(--muted);font-size:13px;">
          (tunggu <span id="cooldown-sec">60</span> detik)
        </span>
      </div>

      <div class="divider"></div>

      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="logout-link" style="width:100%;background:none;border:none;cursor:pointer;">
          <i class="fas fa-arrow-left-from-bracket" style="font-size:12px;"></i>
          Keluar &amp; Daftar dengan Akun Lain
        </button>
      </form>

    </div>
  </div>

</div>

<script>
(function () {
  const boxes   = Array.from(document.querySelectorAll('.pin-box'));
  const hidden  = document.getElementById('pin-hidden');
  const btnVerify = document.getElementById('btn-verify');
  const form    = document.getElementById('pin-form');

  function getPin() {
    return boxes.map(b => b.value).join('');
  }

  function updateBtn() {
    const pin = getPin();
    const ok = pin.length === 6 && /^\d{6}$/.test(pin);
    btnVerify.disabled = !ok;
    hidden.value = pin;
  }

  boxes.forEach((box, i) => {
    box.addEventListener('keydown', e => {
      if (e.key === 'Backspace' && !box.value && i > 0) {
        boxes[i - 1].focus();
        boxes[i - 1].select();
      }
    });

    box.addEventListener('input', e => {
      // Allow only digits
      box.value = box.value.replace(/\D/g, '').slice(-1);
      if (box.value && i < 5) boxes[i + 1].focus();
      updateBtn();
    });

    box.addEventListener('paste', e => {
      e.preventDefault();
      const text = (e.clipboardData || window.clipboardData).getData('text');
      const digits = text.replace(/\D/g, '').slice(0, 6);
      digits.split('').forEach((d, idx) => {
        if (boxes[idx]) boxes[idx].value = d;
      });
      const next = Math.min(digits.length, 5);
      boxes[next].focus();
      updateBtn();
    });
  });

  // Auto-focus first box
  boxes[0].focus();

  // Submit: set hidden pin value
  form.addEventListener('submit', () => {
    hidden.value = getPin();
  });

  // Resend cooldown
  const btnResend = document.getElementById('btn-resend');
  const cooldownMsg = document.getElementById('cooldown-msg');
  const secEl = document.getElementById('cooldown-sec');

  @if(session('resent'))
  startCooldown();
  @endif

  function startCooldown() {
    btnResend.disabled = true;
    btnResend.style.display = 'none';
    cooldownMsg.style.display = 'inline';
    let sec = 60;
    secEl.textContent = sec;
    const iv = setInterval(() => {
      sec--;
      secEl.textContent = sec;
      if (sec <= 0) {
        clearInterval(iv);
        btnResend.disabled = false;
        btnResend.style.display = 'inline';
        cooldownMsg.style.display = 'none';
      }
    }, 1000);
  }

  document.querySelector('form[action="{{ route('verification.pin.resend') }}"]')
    .addEventListener('submit', () => startCooldown());

  // Poll every 4 seconds — auto redirect when admin verifies
  const checkUrl = '{{ route('verification.pin.check') }}';
  setInterval(async () => {
    try {
      const res  = await fetch(checkUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
      const json = await res.json();
      if (json.verified && json.redirect) {
        window.location.href = json.redirect;
      }
    } catch {}
  }, 4000);
})();
</script>
</body>
</html>
