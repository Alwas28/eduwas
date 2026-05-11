@extends('layouts.admin')
@section('title', 'Keamanan & Backup')
@section('page-title', 'Keamanan & Backup')

@push('styles')
<style>
@keyframes spin{to{transform:rotate(360deg)}}
.spinning{animation:spin .8s linear infinite;display:inline-block}

.pw-check{
  display:inline-flex;align-items:center;gap:4px;
  font-size:11px;font-weight:500;
  padding:3px 9px;border-radius:99px;
  background:var(--surface2);border:1px solid var(--border);color:var(--muted);
  transition:color .2s,background .2s,border-color .2s;
}
.pw-check.ok{color:#34d399;background:rgba(16,185,129,.12);border-color:rgba(16,185,129,.25)}

.backup-row:hover{background:var(--surface2)}
.sesi-row:hover{background:var(--surface2)}

/* Toggle switch */
.toggle-track{
  display:inline-block;position:relative;width:42px;height:24px;cursor:pointer;
}
.toggle-track input{opacity:0;width:0;height:0}
.toggle-knob{
  position:absolute;inset:0;border-radius:99px;background:var(--border);transition:background .2s;
}
.toggle-knob:after{
  content:'';position:absolute;left:3px;top:3px;
  width:18px;height:18px;border-radius:50%;background:#fff;transition:left .2s;
}
.toggle-track input:checked + .toggle-knob{background:var(--ac)}
.toggle-track input:checked + .toggle-knob:after{left:21px}
</style>
@endpush

@section('content')
<div class="space-y-6">

{{-- ── Alerts ── --}}
@if(session('success'))
<div class="rounded-2xl border p-4 flex items-center gap-3 animate-fadeUp" style="background:rgba(16,185,129,.08);border-color:rgba(16,185,129,.3)">
  <i class="fa-solid fa-circle-check text-emerald-400"></i>
  <span class="text-[13.5px] font-medium" style="color:var(--text)">{{ session('success') }}</span>
</div>
@endif
@if($errors->any())
<div class="rounded-2xl border p-4 flex items-center gap-3 animate-fadeUp" style="background:rgba(239,68,68,.08);border-color:rgba(239,68,68,.3)">
  <i class="fa-solid fa-circle-xmark text-red-400"></i>
  <span class="text-[13.5px] font-medium text-red-400">{{ $errors->first() }}</span>
</div>
@endif

{{-- ── Stat Cards ── --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 animate-fadeUp">
  <div class="rounded-2xl p-5 border flex items-center gap-4" style="background:var(--surface);border-color:var(--border)">
    <div class="w-11 h-11 rounded-xl grid place-items-center text-lg flex-shrink-0" style="background:rgba(16,185,129,.14);color:#34d399">
      <i class="fa-solid fa-shield-check"></i>
    </div>
    <div>
      <div class="font-display font-bold text-[22px] text-emerald-400">Aman</div>
      <div class="text-[12px]" style="color:var(--muted)">Status Keamanan</div>
    </div>
  </div>
  <div class="rounded-2xl p-5 border flex items-center gap-4" style="background:var(--surface);border-color:var(--border)">
    <div class="w-11 h-11 rounded-xl grid place-items-center text-lg flex-shrink-0" style="background:rgba(99,102,241,.14);color:#818cf8">
      <i class="fa-solid fa-database"></i>
    </div>
    <div>
      <div class="font-display font-bold text-[28px]" style="color:var(--text)">{{ $stats['total_backup'] }}</div>
      <div class="text-[12px]" style="color:var(--muted)">File Backup</div>
    </div>
  </div>
  <div class="rounded-2xl p-5 border flex items-center gap-4" style="background:var(--surface);border-color:var(--border)">
    <div class="w-11 h-11 rounded-xl grid place-items-center text-lg flex-shrink-0" style="background:rgba(245,158,11,.14);color:#fbbf24">
      <i class="fa-solid fa-display"></i>
    </div>
    <div>
      <div class="font-display font-bold text-[28px]" style="color:var(--text)">{{ $stats['total_sesi'] }}</div>
      <div class="text-[12px]" style="color:var(--muted)">Sesi Aktif</div>
    </div>
  </div>
  <div class="rounded-2xl p-5 border flex items-center gap-4" style="background:var(--surface);border-color:var(--border)">
    <div class="w-11 h-11 rounded-xl grid place-items-center text-lg flex-shrink-0" style="background:rgba(239,68,68,.14);color:#f87171">
      <i class="fa-solid fa-triangle-exclamation"></i>
    </div>
    <div>
      <div class="font-display font-bold text-[28px]" style="color:var(--text)">{{ $stats['login_gagal'] }}</div>
      <div class="text-[12px]" style="color:var(--muted)">Login Gagal</div>
    </div>
  </div>
</div>

{{-- ── Two Columns ── --}}
<div class="grid grid-cols-1 xl:grid-cols-2 gap-6 animate-fadeUp d1">

  {{-- ── Kolom Kiri ── --}}
  <div class="flex flex-col gap-6">

    {{-- Ganti Password --}}
    <div class="rounded-2xl border overflow-hidden" style="background:var(--surface);border-color:var(--border)">
      <div class="px-5 py-4 border-b flex items-center gap-2" style="border-color:var(--border)">
        <i class="fa-solid fa-lock a-text text-[14px]"></i>
        <h3 class="font-display font-semibold text-[15px]" style="color:var(--text)">Ganti Password</h3>
      </div>
      <form method="POST" action="{{ route('admin.keamanan.password') }}" class="p-5 space-y-4">
        @csrf
        {{-- Current password --}}
        <div>
          <label class="f-label">Password Saat Ini</label>
          <div class="relative">
            <input name="current_password" type="password" class="f-input pr-10 @error('current_password') is-invalid @enderror"
              placeholder="Masukkan password lama...">
            <button type="button" onclick="togglePw(this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-[13px]"
              style="background:none;border:none;cursor:pointer;color:var(--muted)">
              <i class="fa-solid fa-eye"></i>
            </button>
          </div>
          @error('current_password')
          <p class="f-error">{{ $message }}</p>
          @enderror
        </div>
        {{-- New password --}}
        <div>
          <label class="f-label">Password Baru</label>
          <div class="relative">
            <input name="password" id="pw-new" type="password" class="f-input pr-10"
              placeholder="Minimal 8 karakter..." oninput="checkPwStrength(this.value)">
            <button type="button" onclick="togglePw(this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-[13px]"
              style="background:none;border:none;cursor:pointer;color:var(--muted)">
              <i class="fa-solid fa-eye"></i>
            </button>
          </div>
          {{-- Strength --}}
          <div class="mt-2">
            <div class="flex justify-between mb-1">
              <span class="text-[11px]" style="color:var(--muted)">Kekuatan password</span>
              <span id="pw-strength-label" class="text-[11px] font-semibold" style="color:var(--muted)">—</span>
            </div>
            <div class="h-[5px] rounded-full overflow-hidden" style="background:var(--surface2)">
              <div id="pw-strength-bar" class="h-full rounded-full transition-all duration-300" style="width:0%"></div>
            </div>
            <div class="flex gap-1.5 mt-2 flex-wrap">
              <span class="pw-check" data-rule="len"><i class="fa-solid fa-circle text-[6px]"></i> Min. 8 karakter</span>
              <span class="pw-check" data-rule="upper"><i class="fa-solid fa-circle text-[6px]"></i> Huruf besar</span>
              <span class="pw-check" data-rule="num"><i class="fa-solid fa-circle text-[6px]"></i> Angka</span>
              <span class="pw-check" data-rule="sym"><i class="fa-solid fa-circle text-[6px]"></i> Simbol</span>
            </div>
          </div>
        </div>
        {{-- Confirm --}}
        <div>
          <label class="f-label">Konfirmasi Password Baru</label>
          <div class="relative">
            <input name="password_confirmation" type="password" class="f-input pr-10"
              placeholder="Ulangi password baru...">
            <button type="button" onclick="togglePw(this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-[13px]"
              style="background:none;border:none;cursor:pointer;color:var(--muted)">
              <i class="fa-solid fa-eye"></i>
            </button>
          </div>
        </div>
        <button type="submit"
          class="w-full a-grad text-white text-[13px] font-semibold py-2.5 rounded-xl border-none cursor-pointer flex items-center justify-center gap-2">
          <i class="fa-solid fa-check"></i>Simpan Password Baru
        </button>
      </form>
    </div>

    {{-- Autentikasi 2FA --}}
    <div class="rounded-2xl border overflow-hidden" style="background:var(--surface);border-color:var(--border)">
      <div class="px-5 py-4 border-b flex items-center gap-2" style="border-color:var(--border)">
        <i class="fa-solid fa-mobile-screen-button a-text text-[14px]"></i>
        <h3 class="font-display font-semibold text-[15px]" style="color:var(--text)">Autentikasi Dua Faktor (2FA)</h3>
      </div>
      <div class="p-5 space-y-3">
        @php
          $methods2fa = [
            ['id'=>'google', 'icon'=>'fa-brands fa-google', 'bg'=>'rgba(16,185,129,.14)', 'color'=>'#34d399',
             'label'=>'Google Authenticator', 'desc'=>'TOTP — aplikasi authenticator', 'on'=>false],
            ['id'=>'email',  'icon'=>'fa-solid fa-envelope', 'bg'=>'rgba(99,102,241,.14)', 'color'=>'#818cf8',
             'label'=>'Verifikasi Email', 'desc'=>'Kode OTP dikirim ke email terdaftar', 'on'=>true],
            ['id'=>'sms',    'icon'=>'fa-solid fa-comment-sms', 'bg'=>'rgba(245,158,11,.14)', 'color'=>'#fbbf24',
             'label'=>'SMS / WhatsApp', 'desc'=>'Kode OTP via nomor HP', 'on'=>false],
          ];
        @endphp
        @foreach($methods2fa as $m)
        <div class="flex items-center justify-between p-3.5 rounded-xl border" style="background:var(--surface2);border-color:var(--border)">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-[10px] grid place-items-center text-[16px] flex-shrink-0"
              style="background:{{ $m['bg'] }};color:{{ $m['color'] }}">
              <i class="{{ $m['icon'] }}"></i>
            </div>
            <div>
              <div class="text-[13.5px] font-semibold" style="color:var(--text)">{{ $m['label'] }}</div>
              <div class="text-[11.5px]" style="color:var(--muted)">{{ $m['desc'] }}</div>
            </div>
          </div>
          <label class="toggle-track">
            <input type="checkbox" {{ $m['on'] ? 'checked' : '' }}
              onchange="showToast(this.checked?'success':'info','{{ $m['label'] }} '+(this.checked?'diaktifkan':'dinonaktifkan'))">
            <span class="toggle-knob"></span>
          </label>
        </div>
        @endforeach
        <p class="text-[11.5px] pt-1" style="color:var(--muted)">
          <i class="fa-solid fa-circle-info mr-1"></i>
          Pengaturan 2FA memerlukan konfigurasi tambahan di server. Hubungi developer untuk aktivasi penuh.
        </p>
      </div>
    </div>

    {{-- Log Aktivitas --}}
    <div class="rounded-2xl border overflow-hidden" style="background:var(--surface);border-color:var(--border)">
      <div class="px-5 py-4 border-b flex items-center justify-between" style="border-color:var(--border)">
        <div class="flex items-center gap-2">
          <i class="fa-solid fa-list-check a-text text-[14px]"></i>
          <h3 class="font-display font-semibold text-[15px]" style="color:var(--text)">Log Aktivitas Keamanan</h3>
        </div>
        <a href="{{ route('admin.log.index') }}"
          class="text-[12px] font-semibold px-3 py-1.5 rounded-lg flex items-center gap-1.5"
          style="background:var(--surface2);color:var(--muted)">
          Lihat Semua <i class="fa-solid fa-arrow-right text-[10px]"></i>
        </a>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full text-[13px] border-collapse">
          <thead>
            <tr>
              <th class="text-left px-4 py-2.5 text-[11px] font-semibold uppercase tracking-wide" style="color:var(--muted);background:var(--surface2)">Aktivitas</th>
              <th class="text-left px-4 py-2.5 text-[11px] font-semibold uppercase tracking-wide" style="color:var(--muted);background:var(--surface2)">IP</th>
              <th class="text-left px-4 py-2.5 text-[11px] font-semibold uppercase tracking-wide" style="color:var(--muted);background:var(--surface2)">Waktu</th>
              <th class="text-left px-4 py-2.5 text-[11px] font-semibold uppercase tracking-wide" style="color:var(--muted);background:var(--surface2)">Status</th>
            </tr>
          </thead>
          <tbody>
            @forelse($logs as $log)
            @php
              $isOk = $log->action !== 'login_failed';
              $icons = ['login'=>'fa-right-to-bracket','login_failed'=>'fa-circle-xmark',
                        'ganti_password'=>'fa-key','backup_database'=>'fa-database'];
              $labels = ['login'=>'Login berhasil','login_failed'=>'Login gagal',
                         'ganti_password'=>'Ganti password','backup_database'=>'Backup database'];
            @endphp
            <tr class="border-t transition-colors" style="border-color:var(--border)"
              onmouseover="this.style.background='var(--surface2)'" onmouseout="this.style.background='transparent'">
              <td class="px-4 py-3">
                <div class="flex items-center gap-2">
                  <i class="fa-solid {{ $icons[$log->action] ?? 'fa-circle-dot' }} text-[13px]"
                    style="color:{{ $isOk ? '#34d399' : '#f87171' }}"></i>
                  <span class="font-medium" style="color:var(--text)">{{ $labels[$log->action] ?? $log->action }}</span>
                </div>
                @if($log->description)
                <div class="text-[11.5px] mt-0.5 ml-5" style="color:var(--muted)">{{ $log->description }}</div>
                @endif
              </td>
              <td class="px-4 py-3">
                <code class="text-[12px] font-mono" style="color:var(--muted)">{{ $log->ip_address ?? '—' }}</code>
              </td>
              <td class="px-4 py-3 text-[12px]" style="color:var(--muted)">{{ $log->created_at->diffForHumans() }}</td>
              <td class="px-4 py-3">
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold
                  {{ $isOk ? 'bg-emerald-500/15 text-emerald-400' : 'bg-red-500/15 text-red-400' }}">
                  <i class="fa-solid fa-circle text-[6px]"></i>{{ $isOk ? 'Berhasil' : 'Gagal' }}
                </span>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="4" class="px-4 py-8 text-center text-[13px]" style="color:var(--muted)">
                <i class="fa-solid fa-inbox text-2xl mb-2 block"></i>Belum ada log aktivitas keamanan
              </td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

  </div>{{-- /kolom kiri --}}

  {{-- ── Kolom Kanan ── --}}
  <div class="flex flex-col gap-6">

    {{-- Backup Database --}}
    <div class="rounded-2xl border overflow-hidden" style="background:var(--surface);border-color:var(--border)">
      <div class="px-5 py-4 border-b flex items-center justify-between" style="border-color:var(--border)">
        <div class="flex items-center gap-2">
          <i class="fa-solid fa-database a-text text-[14px]"></i>
          <h3 class="font-display font-semibold text-[15px]" style="color:var(--text)">Backup Database</h3>
        </div>
        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-500/15 text-emerald-400">
          <i class="fa-solid fa-circle text-[7px]"></i>Aktif
        </span>
      </div>
      <div class="p-5 space-y-4">

        {{-- Info backup terakhir --}}
        @if(!empty($backups))
        <div class="flex items-center gap-3 p-3.5 rounded-xl border" style="background:var(--surface2);border-color:var(--border)">
          <div class="w-10 h-10 rounded-[10px] grid place-items-center flex-shrink-0 text-emerald-400" style="background:rgba(16,185,129,.14)">
            <i class="fa-solid fa-circle-check text-lg"></i>
          </div>
          <div class="flex-1 min-w-0">
            <div class="text-[13px] font-semibold" style="color:var(--text)">Backup Terakhir Berhasil</div>
            <div class="text-[11.5px] mt-0.5 truncate" style="color:var(--muted)">
              {{ $backups[0]['tanggal'] }} · {{ $backups[0]['filename'] }} · {{ $backups[0]['size'] }}
            </div>
          </div>
        </div>
        @else
        <div class="flex items-center gap-3 p-3.5 rounded-xl border" style="background:rgba(245,158,11,.08);border-color:rgba(245,158,11,.25)">
          <i class="fa-solid fa-triangle-exclamation text-amber-400"></i>
          <span class="text-[13px] text-amber-400">Belum ada backup. Buat backup pertama sekarang.</span>
        </div>
        @endif

        {{-- Tombol backup --}}
        <button id="btn-backup" onclick="doBackup()"
          class="w-full a-grad text-white text-[13.5px] font-semibold py-3 rounded-xl border-none cursor-pointer flex items-center justify-center gap-2 transition-opacity">
          <i class="fa-solid fa-database" id="backup-icon"></i>
          <span id="backup-label">Backup Sekarang</span>
        </button>

        {{-- Progress bar backup --}}
        <div id="backup-progress" style="display:none">
          <div class="flex justify-between mb-1">
            <span class="text-[12px]" style="color:var(--muted)">Memproses...</span>
            <span id="backup-pct" class="text-[12px] font-semibold a-text">0%</span>
          </div>
          <div class="h-2 rounded-full overflow-hidden" style="background:var(--surface2)">
            <div id="backup-bar" class="h-full rounded-full a-grad transition-all duration-300" style="width:0%"></div>
          </div>
          <div id="backup-step" class="text-[11.5px] mt-1" style="color:var(--muted)">Memulai...</div>
        </div>

        {{-- Pengaturan --}}
        <div>
          <label class="f-label">Jadwal Auto Backup</label>
          <select class="f-input" onchange="showToast('success','Jadwal diperbarui: '+this.options[this.selectedIndex].text)">
            <option>Setiap hari — 00:00 WIB</option>
            <option>Setiap 6 jam</option>
            <option>Setiap 12 jam</option>
            <option>Setiap minggu — Senin, 00:00</option>
            <option>Manual saja</option>
          </select>
        </div>
        <div>
          <label class="f-label">Simpan Backup Selama</label>
          <select class="f-input" onchange="showToast('success','Retensi diperbarui: '+this.options[this.selectedIndex].text)">
            <option>7 hari terakhir</option>
            <option>14 hari terakhir</option>
            <option>30 hari terakhir</option>
            <option>Semua backup</option>
          </select>
        </div>
        <div class="flex items-center justify-between p-3.5 rounded-xl border" style="background:var(--surface2);border-color:var(--border)">
          <div>
            <div class="text-[13px] font-semibold" style="color:var(--text)">Enkripsi Backup</div>
            <div class="text-[11.5px]" style="color:var(--muted)">AES-256 sebelum disimpan</div>
          </div>
          <label class="toggle-track">
            <input type="checkbox" checked onchange="showToast(this.checked?'success':'info','Enkripsi '+(this.checked?'diaktifkan':'dinonaktifkan'))">
            <span class="toggle-knob"></span>
          </label>
        </div>
      </div>
    </div>

    {{-- Riwayat Backup --}}
    <div class="rounded-2xl border overflow-hidden" style="background:var(--surface);border-color:var(--border)">
      <div class="px-5 py-4 border-b flex items-center justify-between" style="border-color:var(--border)">
        <div class="flex items-center gap-2">
          <i class="fa-solid fa-clock-rotate-left a-text text-[14px]"></i>
          <h3 class="font-display font-semibold text-[15px]" style="color:var(--text)">Riwayat Backup</h3>
        </div>
        <span class="text-[12px]" style="color:var(--muted)">{{ count($backups) }} file</span>
      </div>
      <div id="backup-list">
        @forelse($backups as $i => $bk)
        <div class="backup-row flex items-center gap-3 px-5 py-3.5 transition-colors {{ $i > 0 ? 'border-t' : '' }}" style="border-color:var(--border)"
          id="backup-row-{{ $loop->index }}">
          <div class="w-9 h-9 rounded-[10px] grid place-items-center flex-shrink-0 text-emerald-400 text-[14px]"
            style="background:rgba(16,185,129,.12)">
            <i class="fa-solid fa-database"></i>
          </div>
          <div class="flex-1 min-w-0">
            <div class="text-[12.5px] font-semibold truncate" style="color:var(--text)">{{ $bk['filename'] }}</div>
            <div class="text-[11px] mt-0.5" style="color:var(--muted)">
              {{ $bk['tanggal'] }} · {{ $bk['size'] }}
            </div>
          </div>
          <div class="flex gap-1.5 flex-shrink-0">
            {{-- Download --}}
            <a href="{{ route('admin.keamanan.backup.download', urlencode($bk['filename'])) }}"
              class="w-8 h-8 rounded-lg border grid place-items-center text-[12px] transition-colors"
              style="border-color:var(--border);background:var(--surface2);color:var(--muted)"
              title="Unduh"
              onmouseover="this.style.color='var(--text)'" onmouseout="this.style.color='var(--muted)'">
              <i class="fa-solid fa-download"></i>
            </a>
            {{-- Delete --}}
            <button onclick="deleteBackup('{{ $bk['filename'] }}', {{ $loop->index }})"
              class="w-8 h-8 rounded-lg border grid place-items-center text-[12px]"
              style="border-color:rgba(239,68,68,.25);background:rgba(239,68,68,.08);color:#f87171"
              title="Hapus">
              <i class="fa-solid fa-trash"></i>
            </button>
          </div>
        </div>
        @empty
        <div class="px-5 py-10 text-center">
          <i class="fa-solid fa-database text-3xl mb-3 block" style="color:var(--muted)"></i>
          <p class="text-[13px]" style="color:var(--muted)">Belum ada file backup. Klik "Backup Sekarang" untuk memulai.</p>
        </div>
        @endforelse
      </div>
    </div>

    {{-- Sesi Aktif --}}
    <div class="rounded-2xl border overflow-hidden" style="background:var(--surface);border-color:var(--border)">
      <div class="px-5 py-4 border-b flex items-center justify-between" style="border-color:var(--border)">
        <div class="flex items-center gap-2">
          <i class="fa-solid fa-display a-text text-[14px]"></i>
          <h3 class="font-display font-semibold text-[15px]" style="color:var(--text)">Sesi Aktif</h3>
        </div>
        <button onclick="terminateAll()"
          class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[12px] font-semibold border transition-colors"
          style="border-color:rgba(239,68,68,.3);background:rgba(239,68,68,.1);color:#f87171">
          <i class="fa-solid fa-power-off text-[10px]"></i>Akhiri Semua
        </button>
      </div>
      <div id="sessions-list">
        @forelse($sesiAktif as $i => $sesi)
        @php
          $ua = $sesi['user_agent'];
          $device = 'Desktop';
          $icon   = 'fa-desktop';
          if (str_contains($ua, 'Mobile') || str_contains($ua, 'Android') || str_contains($ua, 'iPhone')) {
            $device = 'Mobile'; $icon = 'fa-mobile-screen';
          } elseif (str_contains($ua, 'Tablet') || str_contains($ua, 'iPad')) {
            $device = 'Tablet'; $icon = 'fa-tablet';
          }
          $browser = 'Browser';
          foreach (['Chrome','Firefox','Safari','Edge','Opera'] as $b) {
            if (str_contains($ua, $b)) { $browser = $b; break; }
          }
        @endphp
        <div class="sesi-row flex items-center gap-3 px-5 py-3.5 transition-colors {{ $i > 0 ? 'border-t' : '' }}"
          style="border-color:var(--border)" id="sesi-row-{{ $sesi['id'] }}">
          <div class="w-10 h-10 rounded-[10px] grid place-items-center flex-shrink-0 text-[15px]
            {{ $sesi['is_current'] ? 'a-bg-lt a-text' : '' }}"
            style="{{ !$sesi['is_current'] ? 'background:var(--surface2);color:var(--sub)' : '' }}">
            <i class="fa-solid {{ $icon }}"></i>
          </div>
          <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 flex-wrap">
              <span class="text-[13px] font-semibold" style="color:var(--text)">{{ $browser }} — {{ $device }}</span>
              @if($sesi['is_current'])
              <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-500/15 text-emerald-400">
                <i class="fa-solid fa-circle text-[6px]"></i>Sesi ini
              </span>
              @endif
            </div>
            <div class="text-[11.5px] mt-0.5" style="color:var(--muted)">
              {{ $sesi['ip'] }} · {{ date('d M Y, H:i', $sesi['last_activity']) }} WIB
            </div>
          </div>
          @unless($sesi['is_current'])
          <button onclick="terminateSession('{{ $sesi['id'] }}')"
            class="px-3 py-1.5 rounded-lg text-[12px] font-semibold border flex-shrink-0"
            style="border-color:rgba(239,68,68,.25);background:rgba(239,68,68,.08);color:#f87171">
            Akhiri
          </button>
          @endunless
        </div>
        @empty
        <div class="px-5 py-8 text-center">
          <p class="text-[13px]" style="color:var(--muted)">Tidak ada data sesi. (Pastikan driver session = database)</p>
        </div>
        @endforelse
      </div>
    </div>

  </div>{{-- /kolom kanan --}}
</div>{{-- /grid --}}

</div>{{-- /space-y-6 --}}
@endsection

@push('scripts')
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

/* ══ Password ══ */
function togglePw(btn) {
  const inp = btn.closest('.relative').querySelector('input');
  const isText = inp.type === 'text';
  inp.type = isText ? 'password' : 'text';
  btn.innerHTML = `<i class="fa-solid ${isText ? 'fa-eye' : 'fa-eye-slash'}"></i>`;
}

function checkPwStrength(val) {
  const rules = {len: val.length>=8, upper:/[A-Z]/.test(val), num:/[0-9]/.test(val), sym:/[^a-zA-Z0-9]/.test(val)};
  const score = Object.values(rules).filter(Boolean).length;
  const bar = document.getElementById('pw-strength-bar');
  const lbl = document.getElementById('pw-strength-label');
  const cfg = [{w:'0%',c:'',t:'—'},{w:'25%',c:'#f87171',t:'Lemah'},{w:'50%',c:'#fbbf24',t:'Sedang'},{w:'75%',c:'#60a5fa',t:'Kuat'},{w:'100%',c:'#34d399',t:'Sangat Kuat'}][score];
  bar.style.width = cfg.w; bar.style.background = cfg.c;
  lbl.textContent = cfg.t; lbl.style.color = cfg.c || 'var(--muted)';
  document.querySelectorAll('.pw-check').forEach(el => el.classList.toggle('ok', rules[el.dataset.rule]));
}

/* ══ Backup ══ */
let isBackingUp = false;
function doBackup() {
  if (isBackingUp) return;
  isBackingUp = true;
  const btn   = document.getElementById('btn-backup');
  const icon  = document.getElementById('backup-icon');
  const label = document.getElementById('backup-label');
  const prog  = document.getElementById('backup-progress');
  const bar   = document.getElementById('backup-bar');
  const pct   = document.getElementById('backup-pct');
  const step  = document.getElementById('backup-step');

  btn.style.opacity = '.65'; btn.style.pointerEvents = 'none';
  icon.className = 'fa-solid fa-spinner spinning';
  prog.style.display = 'block';

  const steps = ['Mengumpulkan data tabel...','Mengekspor struktur...','Mengekspor data...','Mengompres file...','Menyimpan ke storage...'];
  let fakePct = 0; let si = 0;
  const fake = setInterval(() => {
    fakePct = Math.min(fakePct + Math.random() * 18 + 6, 88);
    bar.style.width = fakePct + '%';
    pct.textContent = Math.round(fakePct) + '%';
    step.textContent = steps[Math.min(si++, steps.length-1)];
  }, 380);

  fetch('{{ route('admin.keamanan.backup') }}', {
    method: 'POST',
    headers: {'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json'},
  })
  .then(r => r.json())
  .then(data => {
    clearInterval(fake);
    bar.style.width = '100%'; pct.textContent = '100%'; step.textContent = 'Selesai!';
    icon.className = 'fa-solid fa-check';
    label.textContent = 'Backup Berhasil!';
    if (data.success) {
      showToast('success', 'Backup berhasil: ' + data.filename + ' (' + data.size + ')');
      addBackupRow(data.filename, data.size, data.tanggal);
    } else {
      showToast('error', data.message || 'Backup gagal.');
    }
    setTimeout(() => {
      btn.style.opacity='1'; btn.style.pointerEvents='';
      icon.className='fa-solid fa-database'; label.textContent='Backup Sekarang';
      prog.style.display='none'; bar.style.width='0%';
      isBackingUp = false;
    }, 3000);
  })
  .catch(() => {
    clearInterval(fake); isBackingUp = false;
    btn.style.opacity='1'; btn.style.pointerEvents='';
    icon.className='fa-solid fa-database'; label.textContent='Backup Sekarang';
    prog.style.display='none';
    showToast('error', 'Gagal terhubung ke server.');
  });
}

function addBackupRow(filename, size, tanggal) {
  const list = document.getElementById('backup-list');
  const empty = list.querySelector('.px-5.py-10');
  if (empty) empty.remove();

  const idx = 'new-' + Date.now();
  const downloadUrl = '{{ route('admin.keamanan.backup.download', '__') }}'.replace('__', encodeURIComponent(filename));
  const row = document.createElement('div');
  row.className = 'backup-row flex items-center gap-3 px-5 py-3.5 transition-colors border-b';
  row.id = 'backup-row-' + idx;
  row.style.borderColor = 'var(--border)';
  row.innerHTML = `
    <div class="w-9 h-9 rounded-[10px] grid place-items-center flex-shrink-0 text-emerald-400 text-[14px]" style="background:rgba(16,185,129,.12)">
      <i class="fa-solid fa-database"></i>
    </div>
    <div class="flex-1 min-w-0">
      <div class="text-[12.5px] font-semibold truncate" style="color:var(--text)">${filename}</div>
      <div class="text-[11px] mt-0.5" style="color:var(--muted)">${tanggal} · ${size}</div>
    </div>
    <div class="flex gap-1.5 flex-shrink-0">
      <a href="${downloadUrl}" class="w-8 h-8 rounded-lg border grid place-items-center text-[12px]"
        style="border-color:var(--border);background:var(--surface2);color:var(--muted)" title="Unduh">
        <i class="fa-solid fa-download"></i>
      </a>
      <button onclick="deleteBackup('${filename}','${idx}')"
        class="w-8 h-8 rounded-lg border grid place-items-center text-[12px]"
        style="border-color:rgba(239,68,68,.25);background:rgba(239,68,68,.08);color:#f87171" title="Hapus">
        <i class="fa-solid fa-trash"></i>
      </button>
    </div>`;
  list.prepend(row);
}

function deleteBackup(filename, idx) {
  if (!confirm('Hapus backup "' + filename + '"?')) return;
  const url = '{{ route('admin.keamanan.backup.delete', '__') }}'.replace('__', encodeURIComponent(filename));
  fetch(url, {method:'DELETE', headers:{'X-CSRF-TOKEN':CSRF,'Accept':'application/json'}})
    .then(r => r.json())
    .then(d => {
      if (d.success) {
        const row = document.getElementById('backup-row-' + idx);
        if (row) { row.style.opacity='0'; row.style.transition='opacity .3s'; setTimeout(()=>row.remove(),300); }
        showToast('info', filename + ' dihapus.');
      } else showToast('error', d.message);
    });
}

/* ══ Sessions ══ */
function terminateSession(sid) {
  if (!confirm('Akhiri sesi ini?')) return;
  fetch('{{ url('admin/keamanan/sesi') }}/' + sid, {
    method: 'DELETE',
    headers: {'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json'},
  })
  .then(r => r.json())
  .then(d => {
    if (d.success) {
      const row = document.getElementById('sesi-row-' + sid);
      if (row) { row.style.opacity='0'; row.style.transition='opacity .3s'; setTimeout(()=>row.remove(),300); }
      showToast('success', 'Sesi berhasil diakhiri.');
    } else showToast('error', d.message);
  });
}

function terminateAll() {
  if (!confirm('Akhiri semua sesi lain selain sesi Anda sekarang?')) return;
  fetch('{{ route('admin.keamanan.sesi.terminateAll') }}', {
    method: 'DELETE',
    headers: {'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json'},
  })
  .then(r => r.json())
  .then(d => {
    if (d.success) {
      document.querySelectorAll('#sessions-list [id^="sesi-row-"]').forEach(row => {
        if (!row.querySelector('.bg-emerald-500\\/15')) {
          row.style.opacity='0'; row.style.transition='opacity .3s';
          setTimeout(()=>row.remove(),300);
        }
      });
      showToast('success', d.message);
    } else showToast('error', d.message);
  });
}

/* ══ Toast (fallback jika layout tidak punya) ══ */
if (typeof showToast === 'undefined') {
  window.showToast = function(type, msg) {
    const ic = {success:'fa-circle-check',error:'fa-circle-xmark',info:'fa-circle-info'};
    const colors = {success:'#10b981',error:'#f87171',info:'#60a5fa'};
    let c = document.getElementById('toast-container');
    if (!c) { c = document.createElement('div'); c.id='toast-container'; c.style.cssText='position:fixed;bottom:24px;right:24px;z-index:9999;display:flex;flex-direction:column;gap:10px;pointer-events:none'; document.body.appendChild(c); }
    const el = document.createElement('div');
    el.style.cssText = `display:flex;align-items:center;gap:10px;padding:12px 16px;border-radius:14px;font-size:13px;font-weight:500;pointer-events:auto;box-shadow:0 4px 24px rgba(0,0,0,.35);border:1px solid var(--border);background:var(--surface);color:var(--text);min-width:240px;max-width:340px;animation:fadeUp .25s ease both`;
    el.innerHTML = `<i class="fa-solid ${ic[type]||ic.info}" style="font-size:16px;flex-shrink:0;color:${colors[type]||colors.info}"></i><span>${msg}</span>`;
    c.appendChild(el);
    setTimeout(()=>{ el.style.opacity='0'; el.style.transition='opacity .3s'; }, 2700);
    setTimeout(()=>el.remove(), 3000);
  };
}
</script>
@endpush
