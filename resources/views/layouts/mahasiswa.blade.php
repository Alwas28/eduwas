<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'Dashboard') — EduWAS</title>

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
:root{--ac:#10b981;--ac2:#06b6d4;--ac-rgb:16,185,129;--ac-lt:rgba(16,185,129,.14);--ac-lt2:rgba(16,185,129,.08)}
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
.nav-soon{opacity:.5;cursor:not-allowed;pointer-events:none}

/* ════ THEME PANEL ════ */
#tp{transition:transform .2s ease,opacity .2s ease}
#tp.tp-hide{transform:translateY(6px);opacity:0;pointer-events:none}

/* ════ ANIM DELAYS ════ */
.d1{animation-delay:.05s}.d2{animation-delay:.10s}.d3{animation-delay:.15s}
.d4{animation-delay:.20s}.d5{animation-delay:.25s}.d6{animation-delay:.30s}

/* ════ TRANSITIONS ════ */
#sb,header{transition:background .25s,border-color .25s,color .25s}

@keyframes fadeUp{from{opacity:0;transform:translateY(14px)}to{opacity:1;transform:translateY(0)}}
.animate-fadeUp{animation:fadeUp .35s ease both}

/* ════ TOAST ════ */
.toast-wrap{position:fixed;bottom:24px;right:24px;z-index:9999;display:flex;flex-direction:column;gap:10px;pointer-events:none}
.toast{display:flex;align-items:center;gap:10px;padding:12px 16px;border-radius:14px;font-size:13px;font-weight:500;
  pointer-events:auto;box-shadow:0 4px 24px rgba(0,0,0,.35);animation:fadeUp .25s ease both;
  border:1px solid var(--border);background:var(--surface);color:var(--text);min-width:240px;max-width:340px}
.toast.success .ti{color:#10b981}.toast.error .ti{color:#f87171}.toast.info .ti{color:#60a5fa}

/* ════ MODAL (shared) ════ */
.modal-backdrop{position:fixed;inset:0;background:rgba(0,0,0,.6);backdrop-filter:blur(4px);z-index:60;display:flex;align-items:center;justify-content:center;padding:16px;opacity:0;pointer-events:none;transition:opacity .2s}
.modal-backdrop.open{opacity:1;pointer-events:all}
.modal-box{background:var(--surface);border:1px solid var(--border);border-radius:20px;width:100%;box-shadow:0 24px 64px rgba(0,0,0,.5);transform:translateY(16px);transition:transform .25s}
.modal-backdrop.open .modal-box{transform:translateY(0)}
.modal-sm{max-width:400px}

/* ════ FORM HELPERS ════ */
.f-label{display:block;font-size:12.5px;font-weight:600;color:var(--sub);margin-bottom:6px;letter-spacing:.3px}
.f-input{width:100%;background:var(--surface2);border:1px solid var(--border);color:var(--text);border-radius:12px;padding:9px 13px;font-size:13.5px;font-family:inherit;outline:none;transition:border-color .15s,box-shadow .15s}
.f-input:focus{border-color:var(--ac);box-shadow:0 0 0 3px var(--ac-lt)}
.f-input.is-invalid{border-color:#f87171}
.f-error{font-size:12px;color:#f87171;margin-top:4px}
.f-hint{font-size:11.5px;color:var(--muted);margin-top:3px}
select.f-input option{background:var(--surface2);color:var(--text)}

/* ════ LOADING BTN ════ */
.btn-loading{opacity:.7;pointer-events:none}
</style>
@stack('styles')
</head>
<body class="ac-emerald" style="background:var(--bg);color:var(--text)">

<div id="toast-container" class="toast-wrap"></div>

<!-- Overlay mobile -->
<div id="ov" onclick="closeSB()" class="fixed inset-0 bg-black/60 z-40 hidden backdrop-blur-sm"></div>

<!-- ══════════ SIDEBAR ══════════ -->
<aside id="sb" class="fixed top-0 left-0 h-full w-[258px] flex flex-col z-50 -translate-x-full lg:translate-x-0 transition-transform duration-300 border-r"
  style="background:var(--surface);border-color:var(--border)">

  <!-- Logo -->
  <div class="flex items-center gap-3 px-5 py-[21px] border-b flex-shrink-0" style="border-color:var(--border)">
    <div class="a-grad w-9 h-9 rounded-[10px] grid place-items-center font-display font-bold text-[15px] text-white">E</div>
    <div><span class="font-display font-bold text-[18px] tracking-tight" style="color:var(--text)">Edu<span class="a-text">WAS</span></span><p style="font-size:9px;color:var(--muted);margin:0;letter-spacing:.3px;line-height:1;">Education With AI System</p></div>
  </div>

  <!-- Scrollable nav -->
  <div id="sb-nav" class="flex-1 py-3 px-3">

    <p class="text-[10px] font-semibold tracking-[1.3px] uppercase px-2 mt-1 mb-1" style="color:var(--muted)">Utama</p>
    <a href="{{ route('mahasiswa.dashboard') }}"
      class="nav-item {{ request()->routeIs('mahasiswa.dashboard') ? 'nav-active' : 'nav-inactive' }} flex items-center gap-2.5 px-3 py-[9px] rounded-lg text-[13.5px] font-medium mb-0.5">
      <i class="fa solid fa-dashboard fa-grid-2 w-4 text-center text-[13px]"></i>Dashboard
    </a>

    <p class="text-[10px] font-semibold tracking-[1.3px] uppercase px-2 mt-4 mb-1" style="color:var(--muted)">Pembelajaran</p>
    <a href="{{ route('mahasiswa.kelas.index') }}"
      class="nav-item {{ request()->routeIs('mahasiswa.kelas.*') ? 'nav-active' : 'nav-inactive' }} flex items-center gap-2.5 px-3 py-[9px] rounded-lg text-[13.5px] font-medium mb-0.5">
      <i class="fa-solid fa-door-open w-4 text-center text-[13px]"></i>
      <span class="flex-1">Kelas Saya</span>
    </a>
    <a href="{{ route('mahasiswa.materi.index') }}"
      class="nav-item {{ request()->routeIs('mahasiswa.materi.*') ? 'nav-active' : 'nav-inactive' }} flex items-center gap-2.5 px-3 py-[9px] rounded-lg text-[13.5px] font-medium mb-0.5">
      <i class="fa-solid fa-book-open-reader w-4 text-center text-[13px]"></i>
      <span class="flex-1">Materi</span>
    </a>
    <a href="{{ route('mahasiswa.tugas.index') }}"
      class="nav-item {{ request()->routeIs('mahasiswa.tugas.*') ? 'nav-active' : 'nav-inactive' }} flex items-center gap-2.5 px-3 py-[9px] rounded-lg text-[13.5px] font-medium mb-0.5">
      <i class="fa-solid fa-clipboard-list w-4 text-center text-[13px]"></i>
      <span class="flex-1">Tugas</span>
    </a>
    <a href="{{ route('mahasiswa.ujian.index') }}"
      class="nav-item {{ request()->routeIs('mahasiswa.ujian.*') ? 'nav-active' : 'nav-inactive' }} flex items-center gap-2.5 px-3 py-[9px] rounded-lg text-[13.5px] font-medium mb-0.5">
      <i class="fa-solid fa-file-pen w-4 text-center text-[13px]"></i>
      <span class="flex-1">Ujian</span>
    </a>

    <p class="text-[10px] font-semibold tracking-[1.3px] uppercase px-2 mt-4 mb-1" style="color:var(--muted)">Hasil Belajar</p>
    <a href="{{ route('mahasiswa.nilai.index') }}"
      class="nav-item {{ request()->routeIs('mahasiswa.nilai.*') ? 'nav-active' : 'nav-inactive' }} flex items-center gap-2.5 px-3 py-[9px] rounded-lg text-[13.5px] font-medium mb-0.5">
      <i class="fa-solid fa-star-half-stroke w-4 text-center text-[13px]"></i>
      <span class="flex-1">Nilai</span>
    </a>

    <p class="text-[10px] font-semibold tracking-[1.3px] uppercase px-2 mt-4 mb-1" style="color:var(--muted)">Akun</p>
    <a href="{{ route('mahasiswa.profile') }}"
      class="nav-item {{ request()->routeIs('mahasiswa.profile') ? 'nav-active' : 'nav-inactive' }} flex items-center gap-2.5 px-3 py-[9px] rounded-lg text-[13.5px] font-medium mb-0.5">
      <i class="fa-solid fa-user w-4 text-center text-[13px]"></i>Profil Saya
    </a>

  </div>

  <!-- User footer -->
  <div class="flex-shrink-0 p-3 border-t" style="border-color:var(--border)">
    @php $mhs = auth()->user()->mahasiswa; @endphp
    <div x-data="{ open: false }" class="relative">
      <button @click="open = !open" class="w-full flex items-center gap-2.5 p-2.5 rounded-xl cursor-pointer transition-colors text-left"
        onmouseover="this.style.background='var(--surface2)'" onmouseout="this.style.background='transparent'">
        <div class="a-grad w-9 h-9 rounded-xl grid place-items-center font-bold text-sm text-white flex-shrink-0">
          {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
        </div>
        <div class="flex-1 min-w-0">
          <div class="text-[13px] font-semibold truncate" style="color:var(--text)">{{ auth()->user()->name }}</div>
          <div class="text-[11px]" style="color:var(--muted)">
            {{ $mhs?->nim ?? 'Mahasiswa' }}
            @if($mhs?->jurusan) · {{ $mhs->jurusan->nama }} @endif
          </div>
        </div>
        <i class="fa-solid fa-ellipsis text-sm" style="color:var(--muted)"></i>
      </button>
      <div x-show="open" x-transition @click.outside="open = false"
        class="absolute bottom-full left-0 right-0 mb-1 rounded-xl border shadow-xl overflow-hidden"
        style="background:var(--surface);border-color:var(--border)">
        <a href="{{ route('mahasiswa.profile') }}" class="flex items-center gap-2.5 px-4 py-3 text-[13px] nav-inactive">
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

    <div class="flex items-center gap-2 ml-auto">

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
      <div class="a-grad w-9 h-9 rounded-xl grid place-items-center font-bold text-sm text-white flex-shrink-0">
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

/* ── Theme panel ── */
let tpOpen=false;
function toggleTP(){
  tpOpen=!tpOpen;
  document.getElementById('tp').classList.toggle('tp-hide',!tpOpen);
}
document.addEventListener('click',e=>{
  if(!e.target.closest('#tb-btn')&&!e.target.closest('#tp')){tpOpen=false;document.getElementById('tp').classList.add('tp-hide')}
});

/* ── Mode ── */
let curMode='dark';
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

/* ── Accent ── */
let curAccent='emerald';
function setAccent(a){
  curAccent=a;
  document.body.classList.remove('ac-blue','ac-violet','ac-emerald','ac-rose','ac-amber');
  document.body.classList.add('ac-'+a);
  const COLORS={blue:'#4f6ef7',violet:'#8b5cf6',emerald:'#10b981',rose:'#f43f5e',amber:'#f59e0b'};
  document.querySelectorAll('.ac-swatch').forEach(s=>{
    s.style.outline=s.dataset.ac===a?'2.5px solid '+COLORS[a]:'';
    s.style.outlineOffset=s.dataset.ac===a?'2px':'';
  });
  document.querySelectorAll('.ac-lbl').forEach(l=>{
    const active=l.dataset.ac===a;
    l.style.background=active?'var(--surface2)':'transparent';
    l.style.color=active?'var(--text)':'var(--sub)';
    l.style.fontWeight=active?'600':'400';
  });
  syncModeUI();
  localStorage.setItem('edu-accent-mhs',a);
}

/* ── Init ── */
const savedMode=localStorage.getItem('edu-mode')||'dark';
const savedAccent=localStorage.getItem('edu-accent-mhs')||'emerald';
setAccent(savedAccent);
setMode(savedMode);

/* ── Toast ── */
function showToast(type,msg){
  const wrap=document.getElementById('toast-container');
  const t=document.createElement('div');
  t.className=`toast ${type}`;
  const icons={success:'fa-circle-check',error:'fa-circle-xmark',info:'fa-circle-info'};
  t.innerHTML=`<i class="ti fa-solid ${icons[type]||icons.info} text-[15px]"></i><span>${msg}</span>`;
  wrap.appendChild(t);
  setTimeout(()=>{t.style.opacity='0';t.style.transform='translateY(6px)';t.style.transition='.3s';setTimeout(()=>t.remove(),300)},3500);
}

/* ── Modal ── */
function openModal(id){document.getElementById(id).classList.add('open')}
function closeModal(id){document.getElementById(id).classList.remove('open')}
document.addEventListener('keydown',e=>{if(e.key==='Escape')document.querySelectorAll('.modal-backdrop.open').forEach(m=>m.classList.remove('open'))});

/* ── Btn loading ── */
function setLoading(id,on){
  const b=document.getElementById(id);
  if(!b)return;
  if(on){b._orig=b.innerHTML;b.innerHTML='<i class="fa-solid fa-spinner fa-spin mr-1.5 text-[11px]"></i>Memproses...';b.classList.add('btn-loading')}
  else{b.innerHTML=b._orig||b.innerHTML;b.classList.remove('btn-loading')}
}

/* ── Restore session flash ── */
@if(session('success'))document.addEventListener('DOMContentLoaded',()=>showToast('success','{{ session("success") }}'));@endif
@if(session('error'))document.addEventListener('DOMContentLoaded',()=>showToast('error','{{ session("error") }}'));@endif
</script>
@stack('scripts')
</body>
</html>
