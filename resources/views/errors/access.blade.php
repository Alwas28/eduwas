<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $title ?? 'Akses' }} — EduWAS</title>
<link href="https://fonts.googleapis.com/css2?family=Clash+Display:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
@vite(['resources/css/app.css', 'resources/js/app.js'])
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{
  font-family:'Plus Jakarta Sans',sans-serif;
  --bg:#0f1117;--surface:#161b27;--surface2:#1c2336;
  --border:#252d42;--text:#e2e8f0;--muted:#64748b;--sub:#94a3b8;
  --ac:#4f6ef7;--ac2:#7c3aed;--ac-lt:rgba(79,110,247,.14);
  background:var(--bg);color:var(--text);min-height:100vh;
  display:flex;flex-direction:column;
}
.font-display{font-family:'Clash Display',sans-serif}
.a-grad{background:linear-gradient(135deg,var(--ac),var(--ac2))}
.a-text{color:var(--ac)}
.a-bg-lt{background:var(--ac-lt)}

/* Grid background */
.grid-bg{
  position:fixed;inset:0;pointer-events:none;
  background-image:
    linear-gradient(rgba(79,110,247,.04) 1px,transparent 1px),
    linear-gradient(90deg,rgba(79,110,247,.04) 1px,transparent 1px);
  background-size:40px 40px;
}
.glow{
  position:fixed;pointer-events:none;border-radius:9999px;filter:blur(80px);
}
</style>
</head>
<body>

<div class="grid-bg"></div>

{{-- Glows --}}
<div class="glow" style="width:400px;height:400px;top:-100px;left:-100px;background:rgba({{ $glowRgb ?? '79,110,247' }},.08)"></div>
<div class="glow" style="width:300px;height:300px;bottom:-80px;right:-80px;background:rgba({{ $glowRgb ?? '79,110,247' }},.06)"></div>

{{-- Header --}}
<div class="relative z-10 flex items-center justify-between px-6 py-4 border-b" style="border-color:var(--border)">
  <a href="/" class="flex items-center gap-2.5">
    <div class="a-grad w-8 h-8 rounded-[9px] grid place-items-center font-display font-bold text-[14px] text-white">E</div>
    <span class="font-display font-bold text-[17px] tracking-tight" style="color:var(--text)">Edu<span class="a-text">Learn</span></span>
  </a>
  @auth
  <form method="POST" action="{{ route('logout') }}">
    @csrf
    <button type="submit" class="flex items-center gap-2 px-3 py-1.5 rounded-xl text-[12.5px] font-medium border transition-colors"
      style="border-color:var(--border);color:var(--muted)"
      onmouseover="this.style.borderColor='#f87171';this.style.color='#f87171'"
      onmouseout="this.style.borderColor='var(--border)';this.style.color='var(--muted)'">
      <i class="fa-solid fa-right-from-bracket text-[11px]"></i> Keluar
    </button>
  </form>
  @endauth
</div>

{{-- Content --}}
<div class="relative z-10 flex-1 flex items-center justify-center px-6 py-16">
  <div class="text-center max-w-md w-full">

    {{-- Icon --}}
    <div class="inline-flex items-center justify-center w-20 h-20 rounded-3xl mb-6 mx-auto text-3xl"
      style="background:{{ $iconBg ?? 'var(--ac-lt)' }};color:{{ $iconColor ?? 'var(--ac)' }}">
      <i class="fa-solid {{ $icon ?? 'fa-ban' }}"></i>
    </div>

    {{-- Code badge --}}
    @if(isset($code))
    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-[12px] font-bold mb-4 border"
      style="background:var(--surface2);border-color:var(--border);color:var(--muted)">
      <span style="color:{{ $iconColor ?? 'var(--ac)' }}">{{ $code }}</span>
      <span style="color:var(--border)">·</span>
      <span>EduWAS</span>
    </div>
    @endif

    {{-- Title --}}
    <h1 class="font-display font-bold text-[28px] sm:text-[32px] mb-3 leading-tight" style="color:var(--text)">
      {{ $title ?? 'Terjadi Kesalahan' }}
    </h1>

    {{-- Description --}}
    <p class="text-[14px] leading-relaxed mb-8" style="color:var(--muted)">
      {{ $description ?? 'Halaman yang Anda cari tidak dapat diakses.' }}
    </p>

    {{-- Details box (optional) --}}
    @if(isset($detail))
    <div class="rounded-2xl border px-5 py-4 mb-6 text-left" style="background:var(--surface);border-color:var(--border)">
      <p class="text-[12.5px] leading-relaxed" style="color:var(--sub)">
        <i class="fa-solid fa-circle-info mr-2" style="color:{{ $iconColor ?? 'var(--ac)' }}"></i>
        {{ $detail }}
      </p>
    </div>
    @endif

    {{-- Actions --}}
    <div class="flex items-center justify-center gap-3 flex-wrap">
      @auth
        @if(isset($homeRoute))
        <a href="{{ $homeRoute }}"
          class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-[13.5px] font-semibold text-white a-grad shadow-lg">
          <i class="fa-solid fa-house text-[11px]"></i>
          {{ $homeLabel ?? 'Ke Dashboard' }}
        </a>
        @endif

        <a href="javascript:history.back()"
          class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-[13.5px] font-semibold border transition-colors"
          style="border-color:var(--border);color:var(--sub)"
          onmouseover="this.style.background='var(--surface2)'" onmouseout="this.style.background='transparent'">
          <i class="fa-solid fa-arrow-left text-[11px]"></i> Kembali
        </a>
      @else
        <a href="{{ route('login') }}"
          class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-[13.5px] font-semibold text-white a-grad shadow-lg">
          <i class="fa-solid fa-arrow-right-to-bracket text-[11px]"></i> Masuk
        </a>
      @endauth
    </div>

    {{-- Contact admin hint --}}
    @if(isset($showContact) && $showContact)
    <p class="text-[12px] mt-6" style="color:var(--muted)">
      Butuh bantuan? Hubungi administrator sistem Anda.
    </p>
    @endif

  </div>
</div>

{{-- Footer --}}
<div class="relative z-10 text-center py-4 border-t" style="border-color:var(--border)">
  <p class="text-[12px]" style="color:var(--muted)">&copy; {{ date('Y') }} EduWAS. Semua hak dilindungi.</p>
</div>

</body>
</html>
