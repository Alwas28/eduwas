<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Kode Verifikasi — EduWAS</title>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Segoe UI',Arial,sans-serif;background:#0a0e1a;color:#e2e8f0;line-height:1.6}
.wrap{max-width:520px;margin:40px auto;padding:0 16px}
.card{background:#111827;border:1px solid #1e2a42;border-radius:20px;overflow:hidden}
.header{background:linear-gradient(135deg,#10b981,#06b6d4);padding:36px 32px;text-align:center}
.logo{display:inline-flex;align-items:center;gap:10px;margin-bottom:16px}
.logo-icon{width:44px;height:44px;border-radius:12px;background:rgba(255,255,255,.2);display:flex;align-items:center;justify-content:center;font-size:20px}
.logo-text{font-size:22px;font-weight:700;color:#fff;letter-spacing:-.3px}
.header-title{font-size:20px;font-weight:700;color:#fff;margin-bottom:4px}
.header-sub{font-size:13.5px;color:rgba(255,255,255,.8)}
.body{padding:36px 32px}
.greeting{font-size:15px;color:#94a3b8;margin-bottom:20px}
.greeting strong{color:#e2e8f0}
.desc{font-size:14px;color:#64748b;margin-bottom:28px;line-height:1.7}
.pin-label{font-size:11px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:#64748b;margin-bottom:14px}
.pin-row{display:flex;gap:8px;justify-content:center;margin-bottom:28px}
.pin-digit{
  width:54px;height:64px;border-radius:12px;
  background:#161d2e;border:2px solid #1e2a42;
  display:flex;align-items:center;justify-content:center;
  font-size:28px;font-weight:700;color:#10b981;
  font-family:'Courier New',monospace;
}
.expires{
  background:#161d2e;border:1px solid #1e2a42;border-radius:10px;
  padding:12px 16px;font-size:13px;color:#64748b;
  display:flex;align-items:center;gap:10px;margin-bottom:24px;
}
.expires-icon{font-size:16px}
.divider{height:1px;background:#1e2a42;margin:24px 0}
.warning{font-size:12.5px;color:#64748b;line-height:1.65}
.warning strong{color:#94a3b8}
.footer{padding:20px 32px;border-top:1px solid #1e2a42;text-align:center}
.footer p{font-size:12px;color:#334155}
@media(max-width:500px){
  .pin-digit{width:40px;height:52px;font-size:22px}
  .body,.header,.footer{padding-left:20px;padding-right:20px}
}
</style>
</head>
<body>
<div class="wrap">
  <div class="card">

    <div class="header">
      <div class="logo">
        <div class="logo-icon">🎓</div>
        <span class="logo-text">EduWAS</span><br><span style="font-size:11px;color:rgba(255,255,255,.7);letter-spacing:.3px;">Education With AI System</span>
      </div>
      <div class="header-title">Verifikasi Email Anda</div>
      <div class="header-sub">Masukkan kode berikut untuk mengaktifkan akun</div>
    </div>

    <div class="body">
      <p class="greeting">Halo, <strong>{{ $name }}</strong> 👋</p>
      <p class="desc">
        Terima kasih telah mendaftar di <strong>EduWAS</strong>. Gunakan kode verifikasi di bawah ini untuk mengkonfirmasi alamat email Anda dan mulai belajar.
      </p>

      <div class="pin-label">Kode Verifikasi 6 Digit</div>
      <div class="pin-row">
        @foreach($digits as $d)
        <div class="pin-digit">{{ $d }}</div>
        @endforeach
      </div>

      <div class="expires">
        <span class="expires-icon">⏰</span>
        <span>Kode ini akan <strong style="color:#f59e0b">kedaluwarsa dalam {{ $expires }} menit</strong>. Segera masukkan sebelum waktu habis.</span>
      </div>

      <div class="divider"></div>

      <div class="warning">
        <strong>Tidak merasa mendaftar?</strong><br>
        Jika Anda tidak mendaftar di EduWAS, abaikan email ini. Tidak ada tindakan yang diperlukan dan akun tidak akan aktif tanpa verifikasi.
      </div>
    </div>

    <div class="footer">
      <p>© {{ date('Y') }} EduWAS — Universitas Muhammadiyah Kendari<br>
      Email ini dikirim otomatis, mohon tidak membalas email ini.</p>
    </div>

  </div>
</div>
</body>
</html>
