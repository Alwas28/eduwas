<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ config('app.name', 'EduLearn') }}</title>
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

/* Glow background */
.page-glow{position:fixed;inset:0;pointer-events:none;overflow:hidden;z-index:0}
.glow-orb{position:absolute;border-radius:50%;filter:blur(120px)}
.glow-1{width:500px;height:500px;background:var(--ac);opacity:.07;top:-150px;left:-100px}
.glow-2{width:400px;height:400px;background:var(--ac2);opacity:.06;bottom:-100px;right:-100px}

/* Navbar */
nav{
  height:60px;flex-shrink:0;position:relative;z-index:10;
  background:rgba(10,14,26,.85);backdrop-filter:blur(12px);
  border-bottom:1px solid var(--border);
  display:flex;align-items:center;padding:0 28px;
}
.nav-logo{display:flex;align-items:center;gap:10px;text-decoration:none}
.nav-logo-icon{width:34px;height:34px;border-radius:9px;background:linear-gradient(135deg,var(--ac),var(--ac2));display:grid;place-items:center;font-size:15px;color:#fff}
.nav-logo-text{font-family:'Clash Display',sans-serif;font-size:17px;font-weight:700;background:linear-gradient(135deg,var(--ac),var(--ac2));-webkit-background-clip:text;-webkit-text-fill-color:transparent}

/* Center container */
.guest-wrap{flex:1;display:flex;align-items:center;justify-content:center;padding:40px 16px;position:relative;z-index:1}
.guest-card{
  width:100%;max-width:400px;
  background:var(--surface);border:1px solid var(--border);
  border-radius:20px;padding:36px 32px;
  box-shadow:0 20px 60px rgba(0,0,0,.3);
}

/* Form helpers */
.f-label{display:block;font-size:11.5px;font-weight:700;color:var(--muted);margin-bottom:6px;letter-spacing:.4px;text-transform:uppercase}
.f-input{
  width:100%;background:var(--surface2);border:1.5px solid var(--border);color:var(--text);
  border-radius:11px;padding:10px 14px;font-size:13.5px;font-family:inherit;outline:none;
  transition:border-color .15s,box-shadow .15s;
}
.f-input:focus{border-color:var(--ac);box-shadow:0 0 0 3px var(--ac-lt)}
.f-input::placeholder{color:var(--muted)}
.f-input.is-invalid{border-color:#f87171}
.f-error{font-size:12px;color:#f87171;margin-top:5px}
.pw-wrap{position:relative}
.pw-wrap .f-input{padding-right:44px}
.pw-toggle{position:absolute;right:13px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:var(--muted);font-size:14px;padding:4px}
.pw-toggle:hover{color:var(--sub)}
.btn-submit{
  width:100%;padding:11px;border-radius:12px;border:none;cursor:pointer;
  font-family:inherit;font-size:14px;font-weight:700;color:#fff;
  background:linear-gradient(135deg,var(--ac),var(--ac2));
  box-shadow:0 4px 20px rgba(var(--ac-rgb),.3);
  transition:opacity .15s,transform .1s;
  display:flex;align-items:center;justify-content:center;gap:8px;
}
.btn-submit:hover{opacity:.9;transform:translateY(-1px)}
.btn-submit:disabled{opacity:.6;cursor:not-allowed;transform:none}
</style>
</head>
<body>
<div class="page-glow">
  <div class="glow-orb glow-1"></div>
  <div class="glow-orb glow-2"></div>
</div>

<nav>
  <a href="/" class="nav-logo">
    <div class="nav-logo-icon"><i class="fas fa-graduation-cap"></i></div>
    <span class="nav-logo-text">EduLearn</span>
  </a>
</nav>

<div class="guest-wrap">
  <div class="guest-card">
    {{ $slot }}
  </div>
</div>
</body>
</html>
