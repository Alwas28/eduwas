@php
  $notifUnread = auth()->user()->unreadNotifications()->count();
  $notifications = auth()->user()->notifications()->latest()->take(10)->get();
@endphp

<div class="relative" id="notif-wrap">
  <button id="notif-btn" onclick="toggleNotif()"
    class="relative w-9 h-9 rounded-lg grid place-items-center border transition-colors text-[13px]"
    style="background:var(--surface2);border-color:var(--border);color:var(--muted)"
    onmouseover="this.style.borderColor='var(--ac)';this.style.color='var(--ac)'"
    onmouseout="this.style.borderColor='var(--border)';this.style.color='var(--muted)'">
    <i class="fa-solid fa-bell"></i>
    @if($notifUnread > 0)
      <span id="notif-badge"
        class="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 rounded-full text-[10px] font-bold text-white grid place-items-center a-grad border-2"
        style="border-color:var(--surface)">
        {{ $notifUnread > 99 ? '99+' : $notifUnread }}
      </span>
    @else
      <span id="notif-badge" class="hidden absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 rounded-full text-[10px] font-bold text-white grid place-items-center a-grad border-2"
        style="border-color:var(--surface)"></span>
    @endif
  </button>

  {{-- Panel --}}
  <div id="notif-panel"
    class="hidden absolute right-0 top-11 w-[340px] rounded-2xl border shadow-2xl z-50 overflow-hidden"
    style="background:var(--surface);border-color:var(--border)">

    {{-- Header --}}
    <div class="flex items-center justify-between px-4 py-3 border-b" style="border-color:var(--border)">
      <div class="flex items-center gap-2">
        <span class="font-display font-semibold text-[14px]" style="color:var(--text)">Notifikasi</span>
        @if($notifUnread > 0)
          <span class="text-[11px] font-bold px-2 py-0.5 rounded-full a-bg-lt a-text">{{ $notifUnread }} baru</span>
        @endif
      </div>
      @if($notifications->isNotEmpty())
        <button onclick="readAllNotif()" class="text-[12px] font-medium a-text hover:underline">Tandai semua dibaca</button>
      @endif
    </div>

    {{-- List --}}
    <div id="notif-list" style="max-height:380px;overflow-y:auto;scrollbar-width:thin;scrollbar-color:var(--scrollbar) transparent">
      @forelse($notifications as $notif)
        @php
          $data     = $notif->data;
          $unread   = is_null($notif->read_at);
          $iconMap  = ['enrollment' => 'fa-door-open', 'tugas' => 'fa-clipboard-list', 'nilai' => 'fa-star-half-stroke'];
          $colorMap = ['emerald' => 'bg-emerald-500/15 text-emerald-400', 'blue' => 'bg-blue-500/15 text-blue-400', 'amber' => 'bg-amber-500/15 text-amber-400', 'rose' => 'bg-rose-500/15 text-rose-400'];
          $icon     = $data['icon'] ?? ($iconMap[$data['type'] ?? ''] ?? 'fa-bell');
          $color    = $colorMap[$data['color'] ?? 'emerald'] ?? 'a-bg-lt a-text';
        @endphp
        <div id="notif-{{ $notif->id }}"
          class="flex items-start gap-3 px-4 py-3 border-b cursor-pointer transition-colors {{ $unread ? '' : 'opacity-60' }}"
          style="border-color:var(--border);{{ $unread ? 'background:var(--surface2)' : '' }}"
          onmouseover="this.style.background='var(--card-hover)'"
          onmouseout="this.style.background='{{ $unread ? 'var(--surface2)' : 'transparent' }}'"
          onclick="readNotif('{{ $notif->id }}', this)">
          <div class="w-9 h-9 rounded-xl grid place-items-center flex-shrink-0 text-[13px] {{ $color }}">
            <i class="fa-solid {{ $icon }}"></i>
          </div>
          <div class="flex-1 min-w-0">
            <div class="flex items-start justify-between gap-1">
              <p class="text-[12.5px] font-semibold leading-snug" style="color:var(--text)">{{ $data['title'] ?? 'Notifikasi' }}</p>
              @if($unread)
                <span class="w-2 h-2 rounded-full flex-shrink-0 mt-1 a-bg"></span>
              @endif
            </div>
            <p class="text-[12px] mt-0.5 leading-snug" style="color:var(--muted)">{{ $data['body'] ?? '' }}</p>
            <p class="text-[11px] mt-1" style="color:var(--muted)">{{ $notif->created_at->diffForHumans() }}</p>
          </div>
        </div>
      @empty
        <div class="py-10 text-center">
          <div class="w-12 h-12 rounded-2xl a-bg-lt a-text grid place-items-center text-xl mx-auto mb-3">
            <i class="fa-regular fa-bell"></i>
          </div>
          <p class="text-[13px] font-medium" style="color:var(--text)">Belum ada notifikasi</p>
          <p class="text-[12px] mt-0.5" style="color:var(--muted)">Notifikasi baru akan muncul di sini</p>
        </div>
      @endforelse
    </div>

    {{-- Footer --}}
    @if($notifications->isNotEmpty())
      <div class="px-4 py-2.5 border-t flex justify-between items-center" style="border-color:var(--border)">
        <span class="text-[12px]" style="color:var(--muted)">{{ $notifications->count() }} dari {{ auth()->user()->notifications()->count() }} notifikasi</span>
        <button onclick="clearAllNotif()" class="text-[11.5px]" style="color:var(--muted)">Hapus semua</button>
      </div>
    @endif
  </div>
</div>

<script>
function toggleNotif() {
  document.getElementById('notif-panel').classList.toggle('hidden');
}
document.addEventListener('click', function(e) {
  if (!document.getElementById('notif-wrap').contains(e.target)) {
    document.getElementById('notif-panel').classList.add('hidden');
  }
});

function readNotif(id, el) {
  fetch(`/notifications/${id}/read`, {
    method: 'POST',
    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
  })
  .then(r => r.json())
  .then(data => {
    el.style.background = 'transparent';
    el.style.opacity = '0.6';
    el.querySelector('.a-bg')?.remove();
    decrementBadge();
    if (data.url) window.location.href = data.url;
  });
}

function readAllNotif() {
  fetch('/notifications/read-all', {
    method: 'POST',
    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
  })
  .then(() => {
    document.querySelectorAll('#notif-list .a-bg').forEach(d => d.remove());
    document.querySelectorAll('#notif-list [id^="notif-"]').forEach(el => {
      el.style.background = 'transparent';
      el.style.opacity = '0.6';
    });
    zeroBadge();
  });
}

function clearAllNotif() {
  fetch('/notifications', {
    method: 'DELETE',
    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
  })
  .then(() => location.reload());
}

function decrementBadge() {
  const badge = document.getElementById('notif-badge');
  if (!badge) return;
  const cur = parseInt(badge.textContent) || 0;
  if (cur <= 1) { zeroBadge(); } else { badge.textContent = cur - 1; badge.classList.remove('hidden'); }
}
function zeroBadge() {
  const badge = document.getElementById('notif-badge');
  if (badge) badge.classList.add('hidden');
}
function incrementNavBadge() {
  const badge = document.getElementById('notif-badge');
  if (!badge) return;
  const cur = parseInt(badge.textContent) || 0;
  badge.textContent = cur + 1 > 99 ? '99+' : cur + 1;
  badge.classList.remove('hidden');
}

// ── Diskusi navbar notification ───────────────────────────────
let _dskNavCb = null;
function _nbEsc(s) { const d = document.createElement('div'); d.textContent = String(s); return d.innerHTML; }

function injectDskNavNotif(messages, onClickFn) {
  const others = (messages || []).filter(d => !d.is_own);
  if (!others.length) return;

  const last   = others[others.length - 1];
  const count  = others.length;
  const title  = count > 1 ? `${count} pesan baru di Diskusi` : `Pesan baru dari ${last.name}`;
  const body   = last.pesan.length > 72 ? last.pesan.substring(0, 72) + '…' : last.pesan;

  const list = document.getElementById('notif-list');
  if (!list) return;

  // Remove empty-state placeholder
  list.querySelector('.py-10')?.remove();

  // Replace existing dsk notification instead of stacking
  list.querySelector('#dsk-nav-notif')?.remove();

  _dskNavCb = onClickFn;

  const item = document.createElement('div');
  item.id = 'dsk-nav-notif';
  item.className = 'flex items-start gap-3 px-4 py-3 border-b cursor-pointer transition-colors';
  item.style.cssText = 'border-color:var(--border);background:var(--surface2)';
  item.onmouseover = () => item.style.background = 'var(--card-hover, var(--surface2))';
  item.onmouseout  = () => item.style.background = item._read ? 'transparent' : 'var(--surface2)';
  item.onclick     = () => dskNavNotifClick(item);
  item.innerHTML   = `
    <div class="w-9 h-9 rounded-xl grid place-items-center flex-shrink-0 text-[13px] a-bg-lt a-text">
      <i class="fa-solid fa-comments"></i>
    </div>
    <div class="flex-1 min-w-0">
      <div class="flex items-start justify-between gap-1">
        <p class="text-[12.5px] font-semibold leading-snug" style="color:var(--text)">${_nbEsc(title)}</p>
        <span id="dsk-nav-dot" class="w-2 h-2 rounded-full flex-shrink-0 mt-1 a-bg"></span>
      </div>
      <p class="text-[12px] mt-0.5 leading-snug" style="color:var(--muted)">${_nbEsc(body)}</p>
      <p class="text-[11px] mt-1" style="color:var(--muted)">Baru saja</p>
    </div>`;

  list.insertAdjacentElement('afterbegin', item);
  incrementNavBadge();
}

function dskNavNotifClick(el) {
  if (!el._read) {
    el._read = true;
    el.style.background = 'transparent';
    el.style.opacity    = '0.6';
    document.getElementById('dsk-nav-dot')?.remove();
    decrementBadge();
  }
  document.getElementById('notif-panel')?.classList.add('hidden');
  _dskNavCb?.();
}

// ── Global diskusi polling (semua halaman) ────────────────────
// Key ini juga dibaca/ditulis oleh polling lokal di masing-masing halaman
// sehingga tidak terjadi notifikasi ganda.
const _GDSK_KEY = 'lms_global_dsk_last_id';

function _gdskGetLastId()     { return parseInt(localStorage.getItem(_GDSK_KEY) || '0'); }
function _gdskSetLastId(id)   { if (id > _gdskGetLastId()) localStorage.setItem(_GDSK_KEY, String(id)); }

// Dipanggil oleh polling lokal (show.blade, pokok-bahasan.blade, rekap.blade)
// agar global poll tahu pesan ini sudah ditangani.
window.gdskSyncLastId = _gdskSetLastId;

(function startGlobalDskPoll() {
  const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';

  async function poll() {
    // Jika halaman ini punya local poll aktif, biarkan local poll yang menangani
    if (window._hasDskLocalPoll) return;

    const afterId = _gdskGetLastId();
    try {
      const r = await fetch(`/diskusi/check?after_id=${afterId}`, {
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
      });
      if (!r.ok) return;
      const data = await r.json();
      if (!data.items?.length) return;

      // Simpan max_id agar poll berikutnya tidak ulang
      _gdskSetLastId(data.max_id);

      data.items.forEach(item => {
        // Suara
        try {
          const ctx = new (window.AudioContext || window.webkitAudioContext)();
          const o = ctx.createOscillator(), g = ctx.createGain();
          o.connect(g); g.connect(ctx.destination);
          o.type = 'sine';
          o.frequency.setValueAtTime(880, ctx.currentTime);
          o.frequency.exponentialRampToValueAtTime(660, ctx.currentTime + 0.12);
          g.gain.setValueAtTime(0.1, ctx.currentTime);
          g.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.28);
          o.start(); o.stop(ctx.currentTime + 0.28);
        } catch {}

        // Navbar notification
        const cb = () => { window.location.href = item.url; };
        injectDskNavNotif(item.messages, cb);

        // Browser notification
        if ('Notification' in window && Notification.permission === 'granted') {
          const count = item.count;
          const title = count > 1
            ? `${count} pesan baru di Diskusi`
            : `Pesan baru dari ${_nbEsc(item.last_name)}`;
          const n = new Notification('💬 ' + title, {
            body: item.last_pesan.substring(0, 90),
            icon: '{{ asset("favicon.ico") }}',
            tag : 'lms-dsk-global-' + item.pb_id,
            renotify: true,
          });
          n.onclick = () => { window.focus(); n.close(); window.location.href = item.url; };
          setTimeout(() => n.close(), 12000);
        }
      });
    } catch { /* silent */ }
  }

  // Mulai setelah 5 detik (beri waktu halaman load), lalu setiap 45 detik
  setTimeout(poll, 5000);
  setInterval(poll, 45000);

  // Request notif permission saat ada klik pertama
  if ('Notification' in window && Notification.permission === 'default') {
    const ask = () => { Notification.requestPermission(); document.removeEventListener('click', ask); };
    document.addEventListener('click', ask, { once: true });
  }
})();
</script>
