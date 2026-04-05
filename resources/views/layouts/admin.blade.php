<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'Dashboard') — EduLearn Admin</title>

<!-- Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Clash+Display:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<!-- Vite -->
@vite(['resources/css/app.css', 'resources/js/app.js'])

<style>
*{box-sizing:border-box}
body{font-family:'Plus Jakarta Sans',sans-serif;transition:background .25s,color .25s}
.font-display,h1,h2,h3{font-family:'Clash Display',sans-serif}

/* ════ ACCENT VARS ════ */
:root{--ac:#4f6ef7;--ac2:#7c3aed;--ac-rgb:79,110,247;--ac-lt:rgba(79,110,247,.14);--ac-lt2:rgba(79,110,247,.08)}
.ac-blue   {--ac:#4f6ef7;--ac2:#7c3aed;--ac-rgb:79,110,247; --ac-lt:rgba(79,110,247,.14);--ac-lt2:rgba(79,110,247,.07)}
.ac-violet {--ac:#8b5cf6;--ac2:#ec4899;--ac-rgb:139,92,246; --ac-lt:rgba(139,92,246,.14);--ac-lt2:rgba(139,92,246,.07)}
.ac-emerald{--ac:#10b981;--ac2:#06b6d4;--ac-rgb:16,185,129; --ac-lt:rgba(16,185,129,.14);--ac-lt2:rgba(16,185,129,.07)}
.ac-rose   {--ac:#f43f5e;--ac2:#f97316;--ac-rgb:244,63,94;  --ac-lt:rgba(244,63,94,.14); --ac-lt2:rgba(244,63,94,.07)}
.ac-amber  {--ac:#f59e0b;--ac2:#ef4444;--ac-rgb:245,158,11; --ac-lt:rgba(245,158,11,.14);--ac-lt2:rgba(245,158,11,.07)}

/* ════ BASE THEME VARS ════ */
body{
  --bg:      #0f1117;
  --surface: #161b27;
  --surface2:#1c2336;
  --border:  #252d42;
  --text:    #e2e8f0;
  --muted:   #64748b;
  --sub:     #94a3b8;
  --card-hover: rgba(255,255,255,.025);
  --scrollbar:#252d42;
}
body.light{
  --bg:      #f1f5f9;
  --surface: #ffffff;
  --surface2:#f8fafc;
  --border:  #e2e8f0;
  --text:    #1e293b;
  --muted:   #94a3b8;
  --sub:     #64748b;
  --card-hover: rgba(0,0,0,.025);
  --scrollbar:#cbd5e1;
}

/* ════ SEMANTIC HELPERS ════ */
.t-bg    {background:var(--bg)!important}
.t-surf  {background:var(--surface)!important}
.t-surf2 {background:var(--surface2)!important}
.t-border{border-color:var(--border)!important}
.t-text  {color:var(--text)!important}
.t-muted {color:var(--muted)!important}
.t-sub   {color:var(--sub)!important}

/* ════ ACCENT HELPERS ════ */
.a-text  {color:var(--ac)!important}
.a-bg    {background:var(--ac)!important}
.a-bg-lt {background:var(--ac-lt)!important}
.a-border{border-color:var(--ac)!important}
.a-grad  {background:linear-gradient(135deg,var(--ac),var(--ac2))!important}

/* ════ SIDEBAR SCROLL ════ */
#sb-nav{overflow-y:auto;scrollbar-width:thin;scrollbar-color:var(--scrollbar) transparent}
#sb-nav::-webkit-scrollbar{width:4px}
#sb-nav::-webkit-scrollbar-thumb{background:var(--scrollbar);border-radius:99px}

/* ════ ACTIVE NAV ════ */
.nav-active{
  background:var(--ac-lt)!important;
  color:var(--ac)!important;
  border-left:2.5px solid var(--ac)!important;
  padding-left:calc(0.75rem - 2.5px)!important;
}
.nav-inactive{color:var(--sub);transition:background .15s,color .15s}
.nav-inactive:hover{background:var(--surface2);color:var(--text)}

/* ════ PROGRESS BAR ════ */
.pf{height:100%;border-radius:99px}

/* ════ THEME PANEL ════ */
#tp{transition:transform .2s ease,opacity .2s ease}
#tp.tp-hide{transform:translateY(6px);opacity:0;pointer-events:none}

/* ════ ANIM DELAYS ════ */
.d1{animation-delay:.05s}.d2{animation-delay:.10s}.d3{animation-delay:.15s}
.d4{animation-delay:.20s}.d5{animation-delay:.25s}.d6{animation-delay:.30s}

/* ════ DONUT ════ */
.donut{transform:rotate(-90deg)}

/* ════ TRANSITIONS ════ */
#sb,header,.stat-card,.card-panel{transition:background .25s,border-color .25s,color .25s}

@keyframes fadeUp{from{opacity:0;transform:translateY(14px)}to{opacity:1;transform:translateY(0)}}
.animate-fadeUp{animation:fadeUp .35s ease both}
</style>
@stack('styles')
</head>
<body class="ac-blue" style="background:var(--bg);color:var(--text)">

<!-- Overlay mobile -->
<div id="ov" onclick="closeSB()" class="fixed inset-0 bg-black/60 z-40 hidden backdrop-blur-sm"></div>

<!-- ══════════ SIDEBAR ══════════ -->
<aside id="sb" class="fixed top-0 left-0 h-full w-[258px] flex flex-col z-50 -translate-x-full lg:translate-x-0 transition-transform duration-300 border-r"
  style="background:var(--surface);border-color:var(--border)">

  <!-- Logo -->
  <div class="flex items-center gap-3 px-5 py-[21px] border-b flex-shrink-0" style="border-color:var(--border)">
    <div class="a-grad w-9 h-9 rounded-[10px] grid place-items-center font-display font-bold text-[15px] text-white">E</div>
    <span class="font-display font-bold text-[18px] tracking-tight" style="color:var(--text)">Edu<span class="a-text">Learn</span></span>
  </div>

  <!-- Scrollable nav -->
  <div id="sb-nav" class="flex-1 py-3 px-3">

    <p class="text-[10px] font-semibold tracking-[1.3px] uppercase px-2 mt-1 mb-1" style="color:var(--muted)">Utama</p>
    <a href="{{ route('admin.dashboard') }}"
      class="nav-item {{ request()->routeIs('admin.dashboard') ? 'nav-active' : 'nav-inactive' }} flex items-center gap-2.5 px-3 py-[9px] rounded-lg text-[13.5px] font-medium mb-0.5">
      <i class="fa-solid fa-grid-2 w-4 text-center text-[13px]"></i>Dashboard
    </a>

    <p class="text-[10px] font-semibold tracking-[1.3px] uppercase px-2 mt-4 mb-1" style="color:var(--muted)">Pengguna</p>
    @canaccess('lihat.peserta')
    <a href="{{ route('admin.peserta.index') }}"
      class="nav-item {{ request()->routeIs('admin.peserta.*') ? 'nav-active' : 'nav-inactive' }} flex items-center gap-2.5 px-3 py-[9px] rounded-lg text-[13.5px] font-medium mb-0.5">
      <i class="fa-solid fa-users w-4 text-center text-[13px]"></i>Peserta
    </a>
    @endcanaccess
    @canaccess('lihat.instruktur')
    <a href="{{ route('admin.instruktur.index') }}"
      class="nav-item {{ request()->routeIs('admin.instruktur.*') ? 'nav-active' : 'nav-inactive' }} flex items-center gap-2.5 px-3 py-[9px] rounded-lg text-[13.5px] font-medium mb-0.5">
      <i class="fa-solid fa-chalkboard-user w-4 text-center text-[13px]"></i>Instruktur
    </a>
    @endcanaccess
    <a href="{{ route('admin.verifikasi.index') }}"
      class="nav-item {{ request()->routeIs('admin.verifikasi.*') ? 'nav-active' : 'nav-inactive' }} flex items-center gap-2.5 px-3 py-[9px] rounded-lg text-[13.5px] font-medium mb-0.5">
      <i class="fa-solid fa-envelope-circle-check w-4 text-center text-[13px]"></i>Verifikasi Email
    </a>

    <p class="text-[10px] font-semibold tracking-[1.3px] uppercase px-2 mt-4 mb-1" style="color:var(--muted)">Akademik</p>
    @canaccess('lihat.fakultas')
    <a href="{{ route('admin.fakultas.index') }}"
      class="nav-item {{ request()->routeIs('admin.fakultas.*') ? 'nav-active' : 'nav-inactive' }} flex items-center gap-2.5 px-3 py-[9px] rounded-lg text-[13.5px] font-medium mb-0.5">
      <i class="fa-solid fa-building-columns w-4 text-center text-[13px]"></i>Fakultas
    </a>
    @endcanaccess
    @canaccess('lihat.jurusan')
    <a href="{{ route('admin.jurusan.index') }}"
      class="nav-item {{ request()->routeIs('admin.jurusan.*') ? 'nav-active' : 'nav-inactive' }} flex items-center gap-2.5 px-3 py-[9px] rounded-lg text-[13.5px] font-medium mb-0.5">
      <i class="fa-solid fa-sitemap w-4 text-center text-[13px]"></i>Jurusan
    </a>
    @endcanaccess
    @canaccess('lihat.periode-akademik')
    <a href="{{ route('admin.periode-akademik.index') }}"
      class="nav-item {{ request()->routeIs('admin.periode-akademik.*') ? 'nav-active' : 'nav-inactive' }} flex items-center gap-2.5 px-3 py-[9px] rounded-lg text-[13.5px] font-medium mb-0.5">
      <i class="fa-solid fa-calendar-days w-4 text-center text-[13px]"></i>Periode Akademik
    </a>
    @endcanaccess
    @canaccess('lihat.matakuliah')
    <a href="{{ route('admin.matakuliah.index') }}"
      class="nav-item {{ request()->routeIs('admin.matakuliah.*') ? 'nav-active' : 'nav-inactive' }} flex items-center gap-2.5 px-3 py-[9px] rounded-lg text-[13.5px] font-medium mb-0.5">
      <i class="fa-solid fa-book w-4 text-center text-[13px]"></i>Mata Kuliah
    </a>
    @endcanaccess
    @canaccess('lihat.kelas')
    <a href="{{ route('admin.kelas.index') }}"
      class="nav-item {{ request()->routeIs('admin.kelas.*') ? 'nav-active' : 'nav-inactive' }} flex items-center gap-2.5 px-3 py-[9px] rounded-lg text-[13.5px] font-medium mb-0.5">
      <i class="fa-solid fa-door-open w-4 text-center text-[13px]"></i>Kelas
    </a>
    @endcanaccess
    @canaccess('lihat.enrollment')
    <a href="{{ route('admin.enrollment.index') }}"
      class="nav-item {{ request()->routeIs('admin.enrollment.*') ? 'nav-active' : 'nav-inactive' }} flex items-center gap-2.5 px-3 py-[9px] rounded-lg text-[13.5px] font-medium mb-0.5">
      <i class="fa-solid fa-user-plus w-4 text-center text-[13px]"></i>Enrollment
    </a>
    @endcanaccess

    <p class="text-[10px] font-semibold tracking-[1.3px] uppercase px-2 mt-4 mb-1" style="color:var(--muted)">Konten & Aktivitas</p>
    <a href="#"
      class="nav-item {{ request()->routeIs('admin.tugas.*') ? 'nav-active' : 'nav-inactive' }} flex items-center gap-2.5 px-3 py-[9px] rounded-lg text-[13.5px] font-medium mb-0.5">
      <i class="fa-solid fa-file-lines w-4 text-center text-[13px]"></i>Tugas
    </a>
    <a href="#"
      class="nav-item {{ request()->routeIs('admin.ujian.*') ? 'nav-active' : 'nav-inactive' }} flex items-center gap-2.5 px-3 py-[9px] rounded-lg text-[13.5px] font-medium mb-0.5">
      <i class="fa-solid fa-pen-to-square w-4 text-center text-[13px]"></i>Ujian
    </a>
    <a href="#"
      class="nav-item {{ request()->routeIs('admin.bank-soal.*') ? 'nav-active' : 'nav-inactive' }} flex items-center gap-2.5 px-3 py-[9px] rounded-lg text-[13.5px] font-medium mb-0.5">
      <i class="fa-solid fa-database w-4 text-center text-[13px]"></i>Bank Soal
    </a>

    <p class="text-[10px] font-semibold tracking-[1.3px] uppercase px-2 mt-4 mb-1" style="color:var(--muted)">Monitoring</p>
    <a href="#"
      class="nav-item {{ request()->routeIs('admin.progress.*') ? 'nav-active' : 'nav-inactive' }} flex items-center gap-2.5 px-3 py-[9px] rounded-lg text-[13.5px] font-medium mb-0.5">
      <i class="fa-solid fa-chart-line w-4 text-center text-[13px]"></i>Progress Peserta
    </a>
    <a href="#"
      class="nav-item {{ request()->routeIs('admin.laporan.*') ? 'nav-active' : 'nav-inactive' }} flex items-center gap-2.5 px-3 py-[9px] rounded-lg text-[13.5px] font-medium mb-0.5">
      <i class="fa-solid fa-chart-bar w-4 text-center text-[13px]"></i>Laporan
    </a>
    <a href="{{ route('admin.log.index') }}"
      class="nav-item {{ request()->routeIs('admin.log.*') ? 'nav-active' : 'nav-inactive' }} flex items-center gap-2.5 px-3 py-[9px] rounded-lg text-[13.5px] font-medium mb-0.5">
      <i class="fa-solid fa-scroll w-4 text-center text-[13px]"></i>Log Aktivitas
    </a>

    <p class="text-[10px] font-semibold tracking-[1.3px] uppercase px-2 mt-4 mb-1" style="color:var(--muted)">Hak Akses</p>
    <a href="{{ route('admin.users.index') }}"
      class="nav-item {{ request()->routeIs('admin.users.*') ? 'nav-active' : 'nav-inactive' }} flex items-center gap-2.5 px-3 py-[9px] rounded-lg text-[13.5px] font-medium mb-0.5">
      <i class="fa-solid fa-users w-4 text-center text-[13px]"></i>Users
    </a>
    <a href="{{ route('admin.roles.index') }}"
      class="nav-item {{ request()->routeIs('admin.roles.*') ? 'nav-active' : 'nav-inactive' }} flex items-center gap-2.5 px-3 py-[9px] rounded-lg text-[13.5px] font-medium mb-0.5">
      <i class="fa-solid fa-id-badge w-4 text-center text-[13px]"></i>Roles
    </a>
    <a href="{{ route('admin.access.index') }}"
      class="nav-item {{ request()->routeIs('admin.access.*') ? 'nav-active' : 'nav-inactive' }} flex items-center gap-2.5 px-3 py-[9px] rounded-lg text-[13.5px] font-medium mb-0.5">
      <i class="fa-solid fa-key w-4 text-center text-[13px]"></i>Access
    </a>
    <a href="{{ route('admin.user-roles.index') }}"
      class="nav-item {{ request()->routeIs('admin.user-roles.*') ? 'nav-active' : 'nav-inactive' }} flex items-center gap-2.5 px-3 py-[9px] rounded-lg text-[13.5px] font-medium mb-0.5">
      <i class="fa-solid fa-user-tag w-4 text-center text-[13px]"></i>User Roles
    </a>
    <a href="{{ route('admin.role-access.index') }}"
      class="nav-item {{ request()->routeIs('admin.role-access.*') ? 'nav-active' : 'nav-inactive' }} flex items-center gap-2.5 px-3 py-[9px] rounded-lg text-[13.5px] font-medium mb-0.5">
      <i class="fa-solid fa-lock w-4 text-center text-[13px]"></i>Role Access
    </a>

    <p class="text-[10px] font-semibold tracking-[1.3px] uppercase px-2 mt-4 mb-1" style="color:var(--muted)">Sistem</p>
    @canaccess('lihat.pengaturan')
    <a href="{{ route('admin.pengaturan.index') }}"
      class="nav-item {{ request()->routeIs('admin.pengaturan.*') ? 'nav-active' : 'nav-inactive' }} flex items-center gap-2.5 px-3 py-[9px] rounded-lg text-[13.5px] font-medium mb-0.5">
      <i class="fa-solid fa-gear w-4 text-center text-[13px]"></i>Pengaturan
    </a>
    @endcanaccess
    <a href="#"
      class="nav-item {{ request()->routeIs('admin.keamanan.*') ? 'nav-active' : 'nav-inactive' }} flex items-center gap-2.5 px-3 py-[9px] rounded-lg text-[13.5px] font-medium mb-0.5">
      <i class="fa-solid fa-shield-halved w-4 text-center text-[13px]"></i>Keamanan
    </a>
  </div>

  <!-- User footer -->
  <div class="flex-shrink-0 p-3 border-t" style="border-color:var(--border)">
    <div x-data="{ open: false }" class="relative">
      <button @click="open = !open" class="w-full flex items-center gap-2.5 p-2.5 rounded-xl cursor-pointer transition-colors text-left"
        onmouseover="this.style.background='var(--surface2)'" onmouseout="this.style.background='transparent'">
        <div class="a-grad w-9 h-9 rounded-xl grid place-items-center font-bold text-sm text-white flex-shrink-0">
          {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
        </div>
        <div class="flex-1 min-w-0">
          <div class="text-[13px] font-semibold truncate" style="color:var(--text)">{{ auth()->user()->name }}</div>
          <div class="text-[11px]" style="color:var(--muted)">Administrator</div>
        </div>
        <i class="fa-solid fa-ellipsis text-sm" style="color:var(--muted)"></i>
      </button>
      <div x-show="open" x-transition @click.outside="open = false"
        class="absolute bottom-full left-0 right-0 mb-1 rounded-xl border shadow-xl overflow-hidden"
        style="background:var(--surface);border-color:var(--border)">
        <a href="{{ route('profile.edit') }}" class="flex items-center gap-2.5 px-4 py-3 text-[13px] nav-inactive">
          <i class="fa-solid fa-user w-4 text-center text-[12px]"></i>Profil Saya
        </a>
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit" class="w-full flex items-center gap-2.5 px-4 py-3 text-[13px] text-red-400 hover:bg-red-500/10 transition-colors">
            <i class="fa-solid fa-right-from-bracket w-4 text-center text-[12px]"></i>Keluar
          </button>
        </form>
      </div>
    </div>
  </div>
</aside>

<!-- ══════════ MAIN ══════════ -->
<div class="lg:ml-[258px] flex flex-col min-h-screen">

  <!-- Topbar -->
  <header class="sticky top-0 z-30 h-16 flex items-center px-5 gap-3 flex-shrink-0 border-b" style="background:var(--surface);border-color:var(--border)">
    <button onclick="toggleSB()" class="lg:hidden text-[18px] p-1.5 rounded-lg transition-colors" style="color:var(--text)"
      onmouseover="this.style.background='var(--surface2)'" onmouseout="this.style.background='transparent'">
      <i class="fa-solid fa-bars"></i>
    </button>
    <span class="font-display font-semibold text-[19px]" style="color:var(--text)">@yield('page-title', 'Dashboard')</span>

    <!-- Search -->
    <div class="hidden sm:flex items-center gap-2 rounded-lg px-3.5 py-[7px] ml-auto w-60 border transition-colors" style="background:var(--surface2);border-color:var(--border)">
      <i class="fa-solid fa-magnifying-glass text-[13px]" style="color:var(--muted)"></i>
      <input class="bg-transparent outline-none text-[13px] w-full placeholder-slate-400" style="color:var(--text)"
        placeholder="Cari peserta, kelas..."
        onfocus="this.closest('div').style.borderColor='var(--ac)'"
        onblur="this.closest('div').style.borderColor='var(--border)'">
    </div>

    <div class="flex items-center gap-2 sm:ml-0 ml-auto">
      <!-- Theme btn -->
      <div class="relative">
        <button id="tb-btn" onclick="toggleTP()" title="Tema"
          class="w-9 h-9 rounded-lg grid place-items-center transition-all border text-[13px]"
          style="background:var(--surface2);border-color:var(--border);color:var(--muted)"
          onmouseover="this.style.borderColor='var(--ac)';this.style.color='var(--ac)'"
          onmouseout="this.style.borderColor='var(--border)';this.style.color='var(--muted)'">
          <i class="fa-solid fa-swatchbook"></i>
        </button>

        <!-- Theme Panel -->
        <div id="tp" class="tp-hide absolute right-0 top-11 rounded-2xl p-4 w-72 shadow-2xl z-50 border"
          style="background:var(--surface);border-color:var(--border)">
          <p class="text-[10px] font-semibold tracking-widest uppercase mb-2.5" style="color:var(--muted)">Mode Tampilan</p>
          <div class="grid grid-cols-2 gap-2 mb-4">
            <button onclick="setMode('dark')" id="mode-dark"
              class="mode-btn flex items-center gap-2.5 px-3 py-2.5 rounded-xl border text-[12.5px] font-medium transition-all"
              style="background:#1e2433;border-color:#3a4460;color:#e2e8f0">
              <span class="w-7 h-7 rounded-lg bg-[#0f1117] grid place-items-center text-slate-400 text-sm flex-shrink-0"><i class="fa-solid fa-moon"></i></span>
              <div class="text-left"><div class="font-semibold text-[12px]">Dark</div><div class="text-[10px] text-slate-500">Mode gelap</div></div>
            </button>
            <button onclick="setMode('light')" id="mode-light"
              class="mode-btn flex items-center gap-2.5 px-3 py-2.5 rounded-xl border text-[12.5px] font-medium transition-all"
              style="background:#f8fafc;border-color:#e2e8f0;color:#1e293b">
              <span class="w-7 h-7 rounded-lg bg-white grid place-items-center text-amber-500 text-sm flex-shrink-0 border border-slate-200"><i class="fa-solid fa-sun"></i></span>
              <div class="text-left"><div class="font-semibold text-[12px]" style="color:#1e293b">Light</div><div class="text-[10px] text-slate-400">Mode terang</div></div>
            </button>
          </div>
          <p class="text-[10px] font-semibold tracking-widest uppercase mb-2.5" style="color:var(--muted)">Warna Aksen</p>
          <div class="grid grid-cols-5 gap-2 mb-3">
            <button onclick="setAccent('blue')"    data-ac="blue"    title="Biru"   class="ac-swatch w-full aspect-square rounded-xl cursor-pointer transition-transform hover:scale-105" style="background:linear-gradient(135deg,#4f6ef7,#7c3aed)"></button>
            <button onclick="setAccent('violet')"  data-ac="violet"  title="Violet" class="ac-swatch w-full aspect-square rounded-xl cursor-pointer transition-transform hover:scale-105" style="background:linear-gradient(135deg,#8b5cf6,#ec4899)"></button>
            <button onclick="setAccent('emerald')" data-ac="emerald" title="Hijau"  class="ac-swatch w-full aspect-square rounded-xl cursor-pointer transition-transform hover:scale-105" style="background:linear-gradient(135deg,#10b981,#06b6d4)"></button>
            <button onclick="setAccent('rose')"    data-ac="rose"    title="Rose"   class="ac-swatch w-full aspect-square rounded-xl cursor-pointer transition-transform hover:scale-105" style="background:linear-gradient(135deg,#f43f5e,#f97316)"></button>
            <button onclick="setAccent('amber')"   data-ac="amber"   title="Amber"  class="ac-swatch w-full aspect-square rounded-xl cursor-pointer transition-transform hover:scale-105" style="background:linear-gradient(135deg,#f59e0b,#ef4444)"></button>
          </div>
          <div class="flex flex-col gap-0.5">
            <button onclick="setAccent('blue')"    data-ac="blue"    class="ac-lbl flex items-center gap-2 px-2.5 py-1.5 rounded-lg text-[12px] transition-colors text-left w-full" style="color:var(--sub)"><span class="w-2.5 h-2.5 rounded-full bg-[#4f6ef7] flex-shrink-0"></span>Biru Elektrik</button>
            <button onclick="setAccent('violet')"  data-ac="violet"  class="ac-lbl flex items-center gap-2 px-2.5 py-1.5 rounded-lg text-[12px] transition-colors text-left w-full" style="color:var(--sub)"><span class="w-2.5 h-2.5 rounded-full bg-[#8b5cf6] flex-shrink-0"></span>Violet Magenta</button>
            <button onclick="setAccent('emerald')" data-ac="emerald" class="ac-lbl flex items-center gap-2 px-2.5 py-1.5 rounded-lg text-[12px] transition-colors text-left w-full" style="color:var(--sub)"><span class="w-2.5 h-2.5 rounded-full bg-[#10b981] flex-shrink-0"></span>Hijau Emerald</button>
            <button onclick="setAccent('rose')"    data-ac="rose"    class="ac-lbl flex items-center gap-2 px-2.5 py-1.5 rounded-lg text-[12px] transition-colors text-left w-full" style="color:var(--sub)"><span class="w-2.5 h-2.5 rounded-full bg-[#f43f5e] flex-shrink-0"></span>Rose Coral</button>
            <button onclick="setAccent('amber')"   data-ac="amber"   class="ac-lbl flex items-center gap-2 px-2.5 py-1.5 rounded-lg text-[12px] transition-colors text-left w-full" style="color:var(--sub)"><span class="w-2.5 h-2.5 rounded-full bg-[#f59e0b] flex-shrink-0"></span>Amber Gold</button>
          </div>
        </div>
      </div>

      <!-- Notification bell -->
      @include('admin.partials.notification-bell')

      <!-- Avatar -->
      <div class="a-grad w-9 h-9 rounded-xl grid place-items-center font-bold text-sm text-white cursor-pointer">
        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
      </div>
    </div>
  </header>

  <!-- ══════════ PAGE CONTENT ══════════ -->
  <main class="flex-1 p-5 md:p-7 space-y-6" style="background:var(--bg)">
    @yield('content')
  </main>

</div>

<script>
/* ── Sidebar ── */
function toggleSB(){document.getElementById('sb').classList.toggle('-translate-x-full');document.getElementById('ov').classList.toggle('hidden')}
function closeSB(){document.getElementById('sb').classList.add('-translate-x-full');document.getElementById('ov').classList.add('hidden')}

/* ── Theme panel toggle ── */
let tpOpen=false;
function toggleTP(){
  tpOpen=!tpOpen;
  document.getElementById('tp').classList.toggle('tp-hide',!tpOpen);
}
document.addEventListener('click',e=>{
  if(!e.target.closest('#tb-btn')&&!e.target.closest('#tp')){tpOpen=false;document.getElementById('tp').classList.add('tp-hide')}
});

/* ── MODE ── */
let curMode='dark';
const ACCENT_MAP={
  blue:   {ac:'#4f6ef7',ac2:'#7c3aed',rgb:'79,110,247'},
  violet: {ac:'#8b5cf6',ac2:'#ec4899',rgb:'139,92,246'},
  emerald:{ac:'#10b981',ac2:'#06b6d4',rgb:'16,185,129'},
  rose:   {ac:'#f43f5e',ac2:'#f97316',rgb:'244,63,94'},
  amber:  {ac:'#f59e0b',ac2:'#ef4444',rgb:'245,158,11'},
};
let curAccent='blue';

function setMode(m){
  curMode=m;
  document.body.classList.toggle('light',m==='light');
  syncModeUI();
  localStorage.setItem('edu-mode',m);
}
function syncModeUI(){
  const d=document.getElementById('mode-dark'),l=document.getElementById('mode-light');
  if(curMode==='dark'){d.style.outline='2px solid var(--ac)';d.style.outlineOffset='2px';l.style.outline='none'}
  else{l.style.outline='2px solid var(--ac)';l.style.outlineOffset='2px';d.style.outline='none'}
}

/* ── ACCENT ── */
function setAccent(a){
  curAccent=a;
  document.body.classList.remove('ac-blue','ac-violet','ac-emerald','ac-rose','ac-amber');
  document.body.classList.add('ac-'+a);
  document.querySelectorAll('.ac-swatch').forEach(s=>{
    s.style.outline=s.dataset.ac===a?'2.5px solid '+ACCENT_MAP[a].ac:'';
    s.style.outlineOffset=s.dataset.ac===a?'2px':'';
  });
  document.querySelectorAll('.ac-lbl').forEach(l=>{
    const active=l.dataset.ac===a;
    l.style.background=active?'var(--surface2)':'transparent';
    l.style.color=active?'var(--text)':'var(--sub)';
    l.style.fontWeight=active?'600':'400';
  });
  syncModeUI();
  localStorage.setItem('edu-accent',a);
}

/* ── INIT (restore from localStorage) ── */
const savedMode=localStorage.getItem('edu-mode')||'dark';
const savedAccent=localStorage.getItem('edu-accent')||'blue';
setAccent(savedAccent);
setMode(savedMode);
</script>

@stack('scripts')
</body>
</html>
