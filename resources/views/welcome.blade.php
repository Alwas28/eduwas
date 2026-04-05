<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>EduWAS — Education With AI System</title>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Clash+Display:wght@500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
*{box-sizing:border-box;margin:0;padding:0}
:root{
  --bg:#0a0e1a; --surface:#111827; --surface2:#161d2e; --border:#1e2a42;
  --text:#e2e8f0; --muted:#64748b; --sub:#94a3b8;
  --ac:#10b981; --ac2:#06b6d4; --ac-rgb:16,185,129;
}
html{scroll-behavior:smooth}
body{font-family:'Plus Jakarta Sans',sans-serif;background:var(--bg);color:var(--text);line-height:1.6;overflow-x:hidden}
h1,h2,h3{font-family:'Clash Display',sans-serif;line-height:1.2}
a{text-decoration:none;color:inherit}
::-webkit-scrollbar{width:5px}
::-webkit-scrollbar-thumb{background:#1e2a42;border-radius:99px}

.container{max-width:1160px;margin:0 auto;padding:0 24px}
.badge{display:inline-flex;align-items:center;gap:6px;font-size:11.5px;font-weight:700;letter-spacing:.04em;padding:4px 12px;border-radius:99px}
.btn{display:inline-flex;align-items:center;gap:8px;padding:11px 22px;border-radius:12px;font-size:14px;font-weight:600;border:none;cursor:pointer;transition:all .2s;font-family:inherit}
.btn-primary{background:linear-gradient(135deg,var(--ac),var(--ac2));color:#fff}
.btn-primary:hover{opacity:.88;transform:translateY(-1px)}
.btn-outline{background:transparent;border:1.5px solid var(--border);color:var(--text)}
.btn-outline:hover{border-color:var(--ac);color:var(--ac)}
.chip{display:inline-flex;align-items:center;gap:5px;font-size:11px;font-weight:600;padding:3px 9px;border-radius:6px}

/* NAVBAR */
nav{position:fixed;top:0;left:0;right:0;z-index:100;padding:0 24px;transition:all .3s}
nav.scrolled{background:rgba(10,14,26,.94);backdrop-filter:blur(14px);border-bottom:1px solid var(--border)}
.nav-inner{max-width:1160px;margin:0 auto;display:flex;align-items:center;justify-content:space-between;height:64px}
.nav-logo{display:flex;align-items:center;gap:10px}
.nav-logo-icon{width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,var(--ac),var(--ac2));display:grid;place-items:center;font-size:16px;color:#fff}
.nav-logo-text{font-family:'Clash Display',sans-serif;font-size:18px;font-weight:700;background:linear-gradient(135deg,var(--ac),var(--ac2));-webkit-background-clip:text;-webkit-text-fill-color:transparent}
.nav-links{display:flex;align-items:center;gap:4px}
.nav-link{padding:7px 14px;border-radius:9px;font-size:13.5px;font-weight:500;color:var(--sub);transition:all .15s}
.nav-link:hover{color:var(--text);background:var(--surface2)}
@media(max-width:768px){.nav-links{display:none}}

/* HERO */
.hero{min-height:100vh;display:flex;align-items:center;padding:100px 0 80px;position:relative;overflow:hidden}
.hero-bg{position:absolute;inset:0;pointer-events:none}
.glow{position:absolute;border-radius:50%;filter:blur(130px);opacity:.16}
.glow-1{width:600px;height:600px;background:var(--ac);top:-150px;left:-120px}
.glow-2{width:500px;height:500px;background:var(--ac2);bottom:-100px;right:-80px}
.hero-grid{position:absolute;inset:0;background-image:linear-gradient(var(--border) 1px,transparent 1px),linear-gradient(90deg,var(--border) 1px,transparent 1px);background-size:60px 60px;opacity:.18}
.hero-inner{display:grid;grid-template-columns:1fr 1fr;gap:64px;align-items:center;position:relative;z-index:1}
@media(max-width:900px){.hero-inner{grid-template-columns:1fr;text-align:center}}
.hero-title{font-size:clamp(36px,5vw,60px);font-weight:700;margin-bottom:20px}
.hero-title span{background:linear-gradient(135deg,var(--ac),var(--ac2));-webkit-background-clip:text;-webkit-text-fill-color:transparent}
.hero-desc{font-size:16px;color:var(--sub);margin-bottom:32px;line-height:1.8;max-width:480px}
@media(max-width:900px){.hero-desc{margin:0 auto 32px}}
.hero-actions{display:flex;gap:12px;flex-wrap:wrap}
@media(max-width:900px){.hero-actions{justify-content:center}}
.hero-stats{display:flex;gap:36px;margin-top:44px;flex-wrap:wrap}
@media(max-width:900px){.hero-stats{justify-content:center}}
.h-stat-num{font-family:'Clash Display',sans-serif;font-size:30px;font-weight:700;background:linear-gradient(135deg,var(--ac),var(--ac2));-webkit-background-clip:text;-webkit-text-fill-color:transparent}
.h-stat-label{font-size:12px;color:var(--muted);margin-top:2px}
.hero-visual{position:relative}
@media(max-width:900px){.hero-visual{display:none}}
.hcard{background:var(--surface);border:1px solid var(--border);border-radius:16px;padding:18px 20px;margin-bottom:12px}
.hcard-float{animation:floatY 4s ease-in-out infinite}
.hcard-float2{animation:floatY 4s ease-in-out infinite;animation-delay:1.8s}
@keyframes floatY{0%,100%{transform:translateY(0)}50%{transform:translateY(-8px)}}

/* SECTIONS */
section{padding:96px 0}
.section-label{display:inline-flex;align-items:center;gap:7px;font-size:11.5px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--ac);margin-bottom:12px}
.section-title{font-size:clamp(26px,3.5vw,40px);font-weight:700;margin-bottom:14px}
.section-sub{font-size:15.5px;color:var(--sub);max-width:560px;line-height:1.7}
.section-head{margin-bottom:52px}
.section-head.center{text-align:center}
.section-head.center .section-sub{margin:0 auto}

/* STATS STRIP */
.stats-strip{background:linear-gradient(135deg,rgba(16,185,129,.07),rgba(6,182,212,.07));border-top:1px solid var(--border);border-bottom:1px solid var(--border);padding:52px 0}
.stats-row{display:grid;grid-template-columns:repeat(4,1fr);gap:24px;text-align:center}
@media(max-width:700px){.stats-row{grid-template-columns:repeat(2,1fr)}}
.stat-num{font-family:'Clash Display',sans-serif;font-size:44px;font-weight:700;background:linear-gradient(135deg,var(--ac),var(--ac2));-webkit-background-clip:text;-webkit-text-fill-color:transparent}
.stat-label{font-size:13px;color:var(--muted);margin-top:4px}

/* FEATURES */
#features{background:var(--surface)}
.feat-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:20px}
.feat-card{background:var(--bg);border:1px solid var(--border);border-radius:18px;padding:28px;transition:all .22s;position:relative;overflow:hidden}
.feat-card::after{content:'';position:absolute;inset:0;border-radius:18px;background:linear-gradient(135deg,rgba(var(--ac-rgb),.06),transparent);opacity:0;transition:opacity .22s;pointer-events:none}
.feat-card:hover{border-color:rgba(var(--ac-rgb),.5);transform:translateY(-4px);box-shadow:0 16px 48px rgba(0,0,0,.25)}
.feat-card:hover::after{opacity:1}
.feat-icon{width:50px;height:50px;border-radius:14px;display:grid;place-items:center;font-size:20px;margin-bottom:18px}
.feat-title{font-size:16px;font-weight:700;color:var(--text);margin-bottom:9px}
.feat-desc{font-size:13.5px;color:var(--sub);line-height:1.65}
.feat-tags{display:flex;flex-wrap:wrap;gap:6px;margin-top:14px}

/* MATA KULIAH */
.mk-filter{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:32px}
.mk-filter-btn{padding:7px 16px;border-radius:99px;font-size:12.5px;font-weight:600;border:1.5px solid var(--border);background:transparent;color:var(--sub);cursor:pointer;transition:all .15s;font-family:inherit}
.mk-filter-btn:hover,.mk-filter-btn.active{border-color:var(--ac);color:var(--ac);background:rgba(var(--ac-rgb),.08)}
.mk-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:16px}
.mk-card{background:var(--surface);border:1px solid var(--border);border-radius:16px;padding:20px;transition:all .2s}
.mk-card:hover{border-color:rgba(var(--ac-rgb),.45);transform:translateY(-2px)}
.mk-card-top{display:flex;align-items:flex-start;gap:14px;margin-bottom:12px}
.mk-icon{width:44px;height:44px;border-radius:12px;display:grid;place-items:center;font-size:17px;flex-shrink:0}
.mk-nama{font-size:14px;font-weight:700;color:var(--text);line-height:1.35;margin-bottom:3px}
.mk-kode{font-size:11px;color:var(--muted);font-family:monospace;letter-spacing:.04em}
.mk-footer{display:flex;align-items:center;justify-content:space-between;padding-top:12px;border-top:1px solid var(--border)}

/* INSTRUKTUR */
#instruktur{background:var(--surface)}
.ins-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(210px,1fr));gap:16px}
.ins-card{background:var(--bg);border:1px solid var(--border);border-radius:16px;padding:28px 20px;text-align:center;transition:all .2s}
.ins-card:hover{border-color:rgba(var(--ac-rgb),.45);transform:translateY(-3px)}
.ins-avatar{width:68px;height:68px;border-radius:50%;margin:0 auto 14px;display:flex;align-items:center;justify-content:center;font-size:26px;font-weight:700;font-family:'Clash Display',sans-serif}
.ins-nama{font-size:14px;font-weight:700;color:var(--text);margin-bottom:4px;line-height:1.35}
.ins-nidn{font-size:11px;color:var(--muted);font-family:monospace;margin-bottom:8px}
.ins-keahlian{font-size:12.5px;color:var(--sub);line-height:1.5;margin-bottom:10px}

/* HOW IT WORKS */
.steps-row{display:grid;grid-template-columns:repeat(4,1fr);gap:0;position:relative;margin-top:8px}
.steps-row::before{content:'';position:absolute;top:31px;left:calc(12.5% + 20px);right:calc(12.5% + 20px);height:1px;background:linear-gradient(90deg,var(--ac),var(--ac2));opacity:.25;pointer-events:none}
@media(max-width:768px){.steps-row{grid-template-columns:1fr 1fr}.steps-row::before{display:none}}
@media(max-width:480px){.steps-row{grid-template-columns:1fr}}
.step{text-align:center;padding:0 12px}
.step-num{width:62px;height:62px;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 18px;font-family:'Clash Display',sans-serif;font-size:20px;font-weight:700;border:2px solid var(--border);position:relative;z-index:1;background:var(--bg);transition:all .2s}
.step-num.grad{background:linear-gradient(135deg,var(--ac),var(--ac2));border-color:transparent;color:#fff;box-shadow:0 6px 24px rgba(var(--ac-rgb),.35)}
.step-title{font-size:15px;font-weight:700;margin-bottom:8px;color:var(--text)}
.step-desc{font-size:13px;color:var(--sub);line-height:1.65}

/* CTA */
.cta-section{background:linear-gradient(135deg,rgba(16,185,129,.1),rgba(6,182,212,.07));border-top:1px solid var(--border);border-bottom:1px solid var(--border);padding:100px 0;text-align:center}
.cta-title{font-size:clamp(28px,4vw,48px);font-weight:700;margin-bottom:16px}
.cta-title span{background:linear-gradient(135deg,var(--ac),var(--ac2));-webkit-background-clip:text;-webkit-text-fill-color:transparent}

/* FOOTER */
footer{padding:52px 0 32px;border-top:1px solid var(--border)}
.footer-inner{display:grid;grid-template-columns:2fr 1fr 1fr;gap:48px}
@media(max-width:768px){.footer-inner{grid-template-columns:1fr;gap:28px}}
.footer-desc{font-size:13.5px;color:var(--muted);margin-top:12px;line-height:1.75;max-width:300px}
.footer-col-title{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--muted);margin-bottom:14px}
.footer-link{display:block;font-size:13.5px;color:var(--sub);margin-bottom:9px;transition:color .15s}
.footer-link:hover{color:var(--ac)}
.footer-bottom{display:flex;align-items:center;justify-content:space-between;padding-top:24px;margin-top:32px;border-top:1px solid var(--border);font-size:12.5px;color:var(--muted);flex-wrap:wrap;gap:8px}

/* ANIMATE ON SCROLL */
.aos{opacity:0;transform:translateY(22px);transition:opacity .6s ease,transform .6s ease}
.aos.visible{opacity:1;transform:translateY(0)}
.d1{transition-delay:.08s}.d2{transition-delay:.16s}.d3{transition-delay:.24s}.d4{transition-delay:.32s}
</style>
</head>
<body>

{{-- NAVBAR --}}
<nav id="navbar">
  <div class="nav-inner">
    <div class="nav-logo">
      <div class="nav-logo-icon"><i class="fas fa-graduation-cap"></i></div>
      <div><span class="nav-logo-text">EduWAS</span><p style="font-size:9px;opacity:.55;margin:0;letter-spacing:.3px;line-height:1;font-family:sans-serif;">Education With AI System</p></div>
    </div>
    <div class="nav-links">
      <a href="#features"    class="nav-link">Fitur</a>
      <a href="#matakuliah"  class="nav-link">Mata Kuliah</a>
      <a href="#instruktur"  class="nav-link">Instruktur</a>
      <a href="#howitworks"  class="nav-link">Cara Kerja</a>
    </div>
    <div style="display:flex;align-items:center;gap:10px;">
      @auth
        <a href="{{ Auth::user()->homeRoute() }}" class="btn btn-primary" style="font-size:13px;padding:9px 18px;">
          <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>
      @else
        <a href="{{ route('login') }}" class="btn btn-outline" style="font-size:13px;padding:9px 18px;">Masuk</a>
      @endauth
    </div>
  </div>
</nav>

{{-- HERO --}}
<section class="hero">
  <div class="hero-bg">
    <div class="glow glow-1"></div>
    <div class="glow glow-2"></div>
    <div class="hero-grid"></div>
  </div>
  <div class="container">
    <div class="hero-inner">

      <div>
        <div style="margin-bottom:18px;">
          <span class="badge" style="background:rgba(var(--ac-rgb),.1);color:var(--ac);border:1px solid rgba(var(--ac-rgb),.22);">
            <i class="fas fa-bolt"></i> Platform LMS Modern
          </span>
        </div>
        <h1 class="hero-title">Belajar Lebih Cerdas,<br><span>Bersama EduWAS</span></h1>
        <p class="hero-desc">Platform manajemen pembelajaran berbasis AI yang menghubungkan mahasiswa dan instruktur dalam ekosistem belajar yang terstruktur, interaktif, dan efisien.</p>
        <div class="hero-actions">
          @auth
            <a href="{{ Auth::user()->homeRoute() }}" class="btn btn-primary">
              <i class="fas fa-arrow-right"></i> Ke Dashboard
            </a>
          @else
            <a href="{{ route('login') }}" class="btn btn-primary">
              <i class="fas fa-sign-in-alt"></i> Mulai Belajar
            </a>
          @endauth
          <a href="#features" class="btn btn-outline">
            <i class="fas fa-play-circle"></i> Lihat Fitur
          </a>
        </div>
        <div class="hero-stats">
          <div>
            <div class="h-stat-num">{{ number_format($stats['mahasiswa']) }}+</div>
            <div class="h-stat-label">Mahasiswa Terdaftar</div>
          </div>
          <div>
            <div class="h-stat-num">{{ $stats['instruktur'] }}</div>
            <div class="h-stat-label">Instruktur Aktif</div>
          </div>
          <div>
            <div class="h-stat-num">{{ $stats['mata_kuliah'] }}</div>
            <div class="h-stat-label">Mata Kuliah</div>
          </div>
          <div>
            <div class="h-stat-num">{{ $stats['kelas'] }}</div>
            <div class="h-stat-label">Kelas Berjalan</div>
          </div>
        </div>
      </div>

      {{-- Hero visual --}}
      <div class="hero-visual">
        <div class="hcard hcard-float" style="border-color:rgba(var(--ac-rgb),.28);">
          <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px;">
            <div style="width:38px;height:38px;border-radius:10px;background:rgba(var(--ac-rgb),.14);display:grid;place-items:center;color:var(--ac);font-size:17px;flex-shrink:0;">
              <i class="fas fa-robot"></i>
            </div>
            <div>
              <div style="font-size:13px;font-weight:700;">AI Grading Aktif</div>
              <div style="font-size:11px;color:var(--muted);">Penilaian otomatis berjalan</div>
            </div>
            <span style="margin-left:auto;background:rgba(var(--ac-rgb),.12);color:var(--ac);font-size:10px;font-weight:700;padding:3px 9px;border-radius:6px;">LIVE</span>
          </div>
          <div style="display:flex;flex-direction:column;gap:8px;">
            @foreach(['Memeriksa jawaban essay…','Memberikan feedback personal…','Menentukan nilai akhir…'] as $i => $step)
            <div style="display:flex;align-items:center;gap:10px;font-size:12.5px;color:var(--sub);">
              <div style="width:20px;height:20px;border-radius:50%;background:{{ $i < 2 ? 'rgba(var(--ac-rgb),.14)' : 'var(--border)' }};display:grid;place-items:center;flex-shrink:0;">
                <i class="fas {{ $i < 2 ? 'fa-check' : 'fa-spinner fa-spin' }}" style="font-size:8px;color:{{ $i < 2 ? 'var(--ac)' : 'var(--muted)' }};"></i>
              </div>
              {{ $step }}
            </div>
            @endforeach
          </div>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
          <div class="hcard hcard-float2" style="padding:16px;">
            <div style="font-size:11px;color:var(--muted);margin-bottom:8px;text-transform:uppercase;letter-spacing:.06em;font-weight:700;">Progress</div>
            <div style="font-size:28px;font-weight:700;font-family:'Clash Display',sans-serif;color:var(--ac);">78%</div>
            <div style="height:5px;background:var(--border);border-radius:99px;margin-top:8px;overflow:hidden;">
              <div style="width:78%;height:100%;background:linear-gradient(90deg,var(--ac),var(--ac2));border-radius:99px;"></div>
            </div>
          </div>
          <div class="hcard" style="padding:16px;">
            <div style="font-size:11px;color:var(--muted);margin-bottom:8px;text-transform:uppercase;letter-spacing:.06em;font-weight:700;">Ujian Aktif</div>
            <div style="font-size:28px;font-weight:700;font-family:'Clash Display',sans-serif;color:#f59e0b;">3</div>
            <div style="font-size:11.5px;color:var(--sub);margin-top:4px;">2 akan segera dimulai</div>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>

{{-- STATS STRIP --}}
<div class="stats-strip">
  <div class="container">
    <div class="stats-row">
      <div class="aos"><div class="stat-num" data-target="{{ $stats['mahasiswa'] }}">0</div><div class="stat-label">Mahasiswa Aktif</div></div>
      <div class="aos d1"><div class="stat-num" data-target="{{ $stats['instruktur'] }}">0</div><div class="stat-label">Instruktur Berpengalaman</div></div>
      <div class="aos d2"><div class="stat-num" data-target="{{ $stats['mata_kuliah'] }}">0</div><div class="stat-label">Mata Kuliah Tersedia</div></div>
      <div class="aos d3"><div class="stat-num" data-target="{{ $stats['kelas'] }}">0</div><div class="stat-label">Kelas Berjalan</div></div>
    </div>
  </div>
</div>

{{-- FEATURES --}}
<section id="features">
  <div class="container">
    <div class="section-head center aos">
      <div class="section-label"><i class="fas fa-sparkles"></i> Fitur Unggulan</div>
      <h2 class="section-title">Semua yang Kamu Butuhkan<br>dalam Satu Platform</h2>
      <p class="section-sub">Dirancang khusus untuk mendukung proses belajar mengajar yang lebih efektif, terukur, dan menyenangkan.</p>
    </div>

    @php
    $features = [
      ['icon'=>'fa-robot',         'color'=>'#818cf8','bg'=>'rgba(99,102,241,.13)',   'title'=>'AI Grading & Feedback',      'desc'=>'Penilaian tugas dan ujian essay otomatis menggunakan AI. Setiap mahasiswa mendapat feedback konstruktif yang personal.',              'tags'=>['AI Powered','Otomatis','Real-time']],
      ['icon'=>'fa-file-alt',      'color'=>'#10b981','bg'=>'rgba(16,185,129,.13)',   'title'=>'Bank Soal & Ujian Adaptif',  'desc'=>'Buat bank soal essay dan pilihan ganda, susun ujian dengan soal & pilihan yang diacak — setiap mahasiswa mendapat set berbeda.',    'tags'=>['Anti-kecurangan','Random Soal','Fleksibel']],
      ['icon'=>'fa-book-open',     'color'=>'#f59e0b','bg'=>'rgba(245,158,11,.13)',   'title'=>'Materi Terstruktur',         'desc'=>'Konten per pokok bahasan dengan tracking progress membaca, rangkuman pribadi, dan diskusi terintegrasi langsung di materi.',          'tags'=>['Progress Tracking','Rangkuman','Diskusi']],
      ['icon'=>'fa-tasks',         'color'=>'#22d3ee','bg'=>'rgba(6,182,212,.13)',    'title'=>'Tugas Kelompok & Individu',  'desc'=>'Sistem tugas mendukung format kelompok maupun individu, dilengkapi rich text editor, upload lampiran, dan penilaian terstruktur.',  'tags'=>['Kelompok','Individu','Rich Text']],
      ['icon'=>'fa-comments',      'color'=>'#c084fc','bg'=>'rgba(168,85,247,.13)',   'title'=>'Diskusi Interaktif',         'desc'=>'Forum diskusi terintegrasi per pokok bahasan. Mahasiswa dan instruktur berinteraksi langsung untuk berbagi pengetahuan.',            'tags'=>['Per Materi','Kolaboratif','Real-time']],
      ['icon'=>'fa-brain',         'color'=>'#f43f5e','bg'=>'rgba(244,63,94,.13)',    'title'=>'AI Tutor Personal',          'desc'=>'Asisten AI yang menjawab pertanyaan seputar materi yang sedang dipelajari, tersedia kapan saja dan di mana saja.',                  'tags'=>['ChatBot','Per Materi','24/7']],
      ['icon'=>'fa-qrcode',        'color'=>'#10b981','bg'=>'rgba(16,185,129,.13)',   'title'=>'Enroll via QR Code',         'desc'=>'Bergabung ke kelas hanya dengan scan QR Code dari instruktur. Proses pendaftaran kelas menjadi lebih cepat dan mudah.',             'tags'=>['QR Scan','Mudah','Cepat']],
      ['icon'=>'fa-chart-line',    'color'=>'#f59e0b','bg'=>'rgba(245,158,11,.13)',   'title'=>'Tracking & Analytics',       'desc'=>'Pantau progress membaca, kehadiran, nilai tugas, dan aktivitas belajar mahasiswa secara real-time di dashboard.',                   'tags'=>['Dashboard','Analitik','Progress']],
      ['icon'=>'fa-shield-alt',    'color'=>'#818cf8','bg'=>'rgba(99,102,241,.13)',   'title'=>'Multi Role & Akses',         'desc'=>'Sistem RBAC dengan tiga peran: Admin, Instruktur, dan Mahasiswa. Setiap role memiliki hak akses yang terdefinisi dengan baik.',     'tags'=>['Admin','Instruktur','Mahasiswa']],
    ];
    @endphp

    <div class="feat-grid">
      @foreach($features as $i => $f)
      <div class="feat-card aos {{ $i > 0 ? 'd'.min($i,4) : '' }}">
        <div class="feat-icon" style="background:{{ $f['bg'] }};color:{{ $f['color'] }};"><i class="fas {{ $f['icon'] }}"></i></div>
        <div class="feat-title">{{ $f['title'] }}</div>
        <div class="feat-desc">{{ $f['desc'] }}</div>
        <div class="feat-tags">
          @foreach($f['tags'] as $tag)
          <span class="chip" style="background:{{ $f['bg'] }};color:{{ $f['color'] }};">{{ $tag }}</span>
          @endforeach
        </div>
      </div>
      @endforeach
    </div>
  </div>
</section>

{{-- MATA KULIAH --}}
<section id="matakuliah">
  <div class="container">
    <div class="section-head aos">
      <div class="section-label"><i class="fas fa-book"></i> Mata Kuliah</div>
      <h2 class="section-title">Pilihan Mata Kuliah Tersedia</h2>
      <p class="section-sub">Temukan mata kuliah yang sesuai dengan bidang studi dan minat kamu.</p>
    </div>

    @php
    $jurusanList  = $mataKuliahList->groupBy(fn($mk) => $mk->jurusan->nama ?? 'Umum');
    $mkColors     = ['#10b981','#818cf8','#f59e0b','#22d3ee','#f43f5e','#c084fc','#fb923c','#34d399'];
    $mkIconsArr   = ['fa-code','fa-database','fa-network-wired','fa-calculator','fa-flask','fa-globe','fa-microchip','fa-pencil-ruler'];
    function hexRgb(string $hex): string {
        $hex = ltrim($hex,'#');
        return hexdec(substr($hex,0,2)).','.hexdec(substr($hex,2,2)).','.hexdec(substr($hex,4,2));
    }
    @endphp

    <div class="mk-filter aos">
      <button class="mk-filter-btn active" onclick="mkFilter('all',this)">Semua ({{ $mataKuliahList->count() }})</button>
      @foreach($jurusanList->keys() as $jn)
      <button class="mk-filter-btn" onclick="mkFilter('{{ Str::slug($jn) }}',this)">{{ $jn }} ({{ $jurusanList[$jn]->count() }})</button>
      @endforeach
    </div>

    <div class="mk-grid" id="mk-grid">
      @foreach($mataKuliahList as $mk)
      @php
        $ci  = $loop->index % count($mkColors);
        $col = $mkColors[$ci];
        $ico = $mkIconsArr[$ci];
        $rgb = hexRgb($col);
      @endphp
      <div class="mk-card aos" data-jurusan="{{ Str::slug($mk->jurusan->nama ?? 'Umum') }}" style="transition-delay:{{ ($loop->index % 4) * 0.06 }}s">
        <div class="mk-card-top">
          <div class="mk-icon" style="background:rgba({{ $rgb }},.12);color:{{ $col }};"><i class="fas {{ $ico }}"></i></div>
          <div style="min-width:0;">
            <div class="mk-nama">{{ $mk->nama }}</div>
            <div class="mk-kode">{{ $mk->kode }}</div>
          </div>
        </div>
        @if($mk->deskripsi)
        <p style="font-size:12.5px;color:var(--sub);line-height:1.5;margin-bottom:12px;">{{ Str::limit($mk->deskripsi,80) }}</p>
        @endif
        <div class="mk-footer">
          <span style="font-size:12px;font-weight:700;color:{{ $col }};"><i class="fas fa-star" style="font-size:9px;margin-right:3px;"></i>{{ $mk->sks }} SKS</span>
          <div style="display:flex;align-items:center;gap:7px;">
            @if($mk->jenis)
            <span class="chip" style="background:rgba({{ $rgb }},.1);color:{{ $col }};">{{ ucfirst($mk->jenis) }}</span>
            @endif
            <span style="font-size:11px;color:var(--muted);">Sem {{ $mk->semester }}</span>
          </div>
        </div>
      </div>
      @endforeach
    </div>

    @if($mataKuliahList->count() > 8)
    <div style="text-align:center;margin-top:24px;">
      <button id="mk-more-btn" onclick="mkShowAll()"
        style="display:inline-flex;align-items:center;gap:8px;padding:11px 24px;border-radius:12px;border:1.5px solid var(--border);background:transparent;color:var(--sub);font-size:13.5px;font-weight:600;cursor:pointer;font-family:inherit;transition:all .15s;"
        onmouseover="this.style.borderColor='var(--ac)';this.style.color='var(--ac)'"
        onmouseout="this.style.borderColor='var(--border)';this.style.color='var(--sub)'">
        <i class="fas fa-chevron-down"></i> Tampilkan Semua Mata Kuliah
      </button>
    </div>
    @endif
  </div>
</section>

{{-- INSTRUKTUR --}}
<section id="instruktur">
  <div class="container">
    <div class="section-head center aos">
      <div class="section-label"><i class="fas fa-chalkboard-teacher"></i> Tim Pengajar</div>
      <h2 class="section-title">Instruktur Berpengalaman</h2>
      <p class="section-sub">Belajar dari para pengajar profesional yang berpengalaman di bidangnya masing-masing.</p>
    </div>

    @php
    $avatarPalette = [
      ['bg'=>'rgba(16,185,129,.15)','color'=>'#10b981'],
      ['bg'=>'rgba(99,102,241,.15)','color'=>'#818cf8'],
      ['bg'=>'rgba(245,158,11,.15)','color'=>'#f59e0b'],
      ['bg'=>'rgba(6,182,212,.15)', 'color'=>'#22d3ee'],
      ['bg'=>'rgba(244,63,94,.15)', 'color'=>'#f43f5e'],
      ['bg'=>'rgba(168,85,247,.15)','color'=>'#c084fc'],
    ];
    @endphp

    <div class="ins-grid">
      @forelse($instrukturList as $ins)
      @php
        $ap  = $avatarPalette[$loop->index % count($avatarPalette)];
        $ini = mb_strtoupper(mb_substr($ins->nama ?? ($ins->user->name ?? '?'), 0, 2));
      @endphp
      <div class="ins-card aos" style="transition-delay:{{ ($loop->index % 4) * 0.07 }}s">
        <div class="ins-avatar" style="background:{{ $ap['bg'] }};color:{{ $ap['color'] }};">{{ $ini }}</div>
        <div class="ins-nama">{{ $ins->nama ?? $ins->user->name }}</div>
        @if($ins->nidn)
        <div class="ins-nidn">NIDN {{ $ins->nidn }}</div>
        @endif
        @if($ins->bidang_keahlian)
        <div class="ins-keahlian">{{ $ins->bidang_keahlian }}</div>
        @endif
        @if($ins->pendidikan_terakhir)
        <span class="chip" style="background:{{ $ap['bg'] }};color:{{ $ap['color'] }};">{{ $ins->pendidikan_terakhir }}</span>
        @endif
      </div>
      @empty
      <div style="grid-column:1/-1;text-align:center;padding:60px 20px;color:var(--muted);">
        <i class="fas fa-user-tie" style="font-size:36px;opacity:.3;display:block;margin-bottom:12px;"></i>
        Belum ada instruktur aktif.
      </div>
      @endforelse
    </div>
  </div>
</section>

{{-- HOW IT WORKS --}}
<section id="howitworks">
  <div class="container">
    <div class="section-head center aos">
      <div class="section-label"><i class="fas fa-map"></i> Cara Kerja</div>
      <h2 class="section-title">Mulai Belajar dalam 4 Langkah</h2>
      <p class="section-sub">Proses yang sederhana untuk memulai perjalanan belajarmu di EduWAS.</p>
    </div>
    <div class="steps-row">
      @php $steps = [
        ['icon'=>'fa-user-plus',  'title'=>'Daftar Akun',        'desc'=>'Buat akun mahasiswa dan lengkapi profil akademikmu.',                         'grad'=>true],
        ['icon'=>'fa-qrcode',     'title'=>'Gabung Kelas',        'desc'=>'Scan QR Code dari instruktur atau masukkan kode kelas untuk bergabung.',       'grad'=>true],
        ['icon'=>'fa-book-reader','title'=>'Ikuti Materi',        'desc'=>'Akses materi, selesaikan tugas, dan ikuti diskusi sesuai jadwal.',              'grad'=>false],
        ['icon'=>'fa-trophy',     'title'=>'Raih Nilai Terbaik',  'desc'=>'Kerjakan ujian, pantau progress, dan dapatkan feedback dari instruktur & AI.', 'grad'=>false],
      ]; @endphp
      @foreach($steps as $i => $s)
      <div class="step aos d{{ $i+1 }}">
        <div class="step-num {{ $s['grad'] ? 'grad' : '' }}"><i class="fas {{ $s['icon'] }}"></i></div>
        <div class="step-title">{{ $s['title'] }}</div>
        <div class="step-desc">{{ $s['desc'] }}</div>
      </div>
      @endforeach
    </div>
  </div>
</section>

{{-- CTA --}}
<div class="cta-section">
  <div class="container">
    <div class="aos">
      <div class="section-label" style="justify-content:center;margin-bottom:18px;"><i class="fas fa-rocket"></i> Mulai Sekarang</div>
      <h2 class="cta-title">Siap Memulai<br><span>Perjalanan Belajarmu?</span></h2>
      <p style="font-size:16px;color:var(--sub);margin-bottom:36px;">Bergabung bersama ribuan mahasiswa yang sudah merasakan<br>manfaat belajar dengan EduWAS.</p>
      <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap;">
        @auth
          <a href="{{ Auth::user()->homeRoute() }}" class="btn btn-primary" style="font-size:15px;padding:13px 30px;">
            <i class="fas fa-tachometer-alt"></i> Buka Dashboard
          </a>
        @else
          <a href="{{ route('login') }}" class="btn btn-primary" style="font-size:15px;padding:13px 30px;">
            <i class="fas fa-sign-in-alt"></i> Masuk ke EduWAS
          </a>
        @endauth
      </div>
    </div>
  </div>
</div>

{{-- FOOTER --}}
<footer>
  <div class="container">
    <div class="footer-inner">
      <div>
        <div class="nav-logo">
          <div class="nav-logo-icon"><i class="fas fa-graduation-cap"></i></div>
          <div><span class="nav-logo-text">EduWAS</span><p style="font-size:9px;opacity:.55;margin:0;letter-spacing:.3px;line-height:1;font-family:sans-serif;">Education With AI System</p></div>
        </div>
        <p class="footer-desc">Platform Learning Management System modern yang membantu institusi pendidikan mengelola proses belajar mengajar secara digital dan efisien.</p>
      </div>
      <div>
        <div class="footer-col-title">Platform</div>
        <a href="#features"   class="footer-link">Fitur</a>
        <a href="#matakuliah" class="footer-link">Mata Kuliah</a>
        <a href="#instruktur" class="footer-link">Instruktur</a>
        <a href="#howitworks" class="footer-link">Cara Kerja</a>
      </div>
      <div>
        <div class="footer-col-title">Akun</div>
        <a href="{{ route('login') }}" class="footer-link">Masuk</a>
        @auth
        <a href="{{ Auth::user()->homeRoute() }}" class="footer-link">Dashboard</a>
        @endauth
      </div>
    </div>
    <div class="footer-bottom">
      <span>© {{ date('Y') }} EduWAS. All rights reserved.</span>
      <span style="display:flex;align-items:center;gap:5px;">Dibangun dengan <i class="fas fa-heart" style="color:#f43f5e;font-size:10px;margin:0 2px;"></i> untuk pendidikan</span>
    </div>
  </div>
</footer>

<script>
/* Navbar scroll */
window.addEventListener('scroll', () => {
  document.getElementById('navbar').classList.toggle('scrolled', scrollY > 40);
});

/* Animate on scroll */
const obs = new IntersectionObserver(entries =>
  entries.forEach(e => { if(e.isIntersecting) e.target.classList.add('visible'); }),
  { threshold: 0.1 }
);
document.querySelectorAll('.aos').forEach(el => obs.observe(el));

/* Count-up */
function countUp(el, target, dur = 1500) {
  let s = 0, step = target / (dur / 16);
  const run = () => {
    s = Math.min(s + step, target);
    el.textContent = Math.floor(s).toLocaleString('id');
    if (s < target) requestAnimationFrame(run);
  };
  run();
}
const cntObs = new IntersectionObserver(entries => {
  entries.forEach(e => {
    if (e.isIntersecting) {
      countUp(e.target, +e.target.dataset.target);
      cntObs.unobserve(e.target);
    }
  });
}, { threshold: 0.5 });
document.querySelectorAll('[data-target]').forEach(el => cntObs.observe(el));

/* Mata Kuliah filter */
const MK_LIMIT = 8;
let mkExpanded = false;

function applyMkLimit(jurusan) {
  let shown = 0;
  document.querySelectorAll('.mk-card').forEach(card => {
    const match = jurusan === 'all' || card.dataset.jurusan === jurusan;
    const show  = match && (mkExpanded || shown < MK_LIMIT);
    card.style.display = show ? '' : 'none';
    if (match && show) shown++;
  });
  const btn = document.getElementById('mk-more-btn');
  if (btn) btn.style.display = !mkExpanded && shown >= MK_LIMIT ? '' : 'none';
}

function mkFilter(jurusan, btn) {
  document.querySelectorAll('.mk-filter-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  applyMkLimit(jurusan);
}

function mkShowAll() {
  mkExpanded = true;
  applyMkLimit('all');
}

/* Initial limit */
window.addEventListener('DOMContentLoaded', () => applyMkLimit('all'));

/* Smooth scroll */
document.querySelectorAll('a[href^="#"]').forEach(a => {
  a.addEventListener('click', e => {
    const t = document.querySelector(a.getAttribute('href'));
    if (t) { e.preventDefault(); t.scrollIntoView({ behavior:'smooth', block:'start' }); }
  });
});
</script>
</body>
</html>
