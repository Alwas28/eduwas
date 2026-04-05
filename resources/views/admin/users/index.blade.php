@extends('layouts.admin')

@section('title', 'Users')
@section('page-title', 'Users')

{{-- ── DataTables CSS ── --}}
@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
<style>
/* ══ DataTables Reset & Theming ══ */
.dataTables_wrapper { color: var(--text); }

.dataTables_wrapper .dataTables_length select,
.dataTables_wrapper .dataTables_filter input {
  background: var(--surface2);
  border: 1px solid var(--border);
  color: var(--text);
  border-radius: 8px;
  padding: 6px 10px;
  outline: none;
  font-size: 13px;
  font-family: 'Plus Jakarta Sans', sans-serif;
}
.dataTables_wrapper .dataTables_filter input:focus { border-color: var(--ac); }
.dataTables_wrapper .dataTables_length label,
.dataTables_wrapper .dataTables_filter label { color: var(--muted); font-size: 13px; gap: 8px; display: flex; align-items: center; }

table.dataTable thead th {
  background: var(--surface2) !important;
  color: var(--muted) !important;
  font-size: 10.5px !important;
  font-weight: 600 !important;
  letter-spacing: .7px !important;
  text-transform: uppercase !important;
  border-bottom: 1px solid var(--border) !important;
  padding: 12px 16px !important;
  white-space: nowrap;
}
table.dataTable thead th:first-child { border-radius: 12px 0 0 0; }
table.dataTable thead th:last-child  { border-radius: 0 12px 0 0; }

table.dataTable tbody td {
  padding: 12px 16px !important;
  border-bottom: 1px solid var(--border) !important;
  font-size: 13px;
  color: var(--text);
  vertical-align: middle;
}
table.dataTable tbody tr { background: var(--surface) !important; transition: background .15s; }
table.dataTable tbody tr:hover { background: var(--card-hover) !important; }
table.dataTable tbody tr:last-child td { border-bottom: none !important; }

table.dataTable.no-footer { border: none !important; }
table.dataTable thead .sorting,
table.dataTable thead .sorting_asc,
table.dataTable thead .sorting_desc { background-image: none !important; cursor: pointer; }
table.dataTable thead .sorting_asc::after  { content: ' ↑'; color: var(--ac); }
table.dataTable thead .sorting_desc::after { content: ' ↓'; color: var(--ac); }
table.dataTable thead .sorting::after      { content: ' ↕'; opacity: .3; }

.dataTables_wrapper .dataTables_info { color: var(--muted); font-size: 12px; }
.dataTables_wrapper .dataTables_paginate .paginate_button {
  background: transparent !important;
  border: 1px solid var(--border) !important;
  color: var(--sub) !important;
  border-radius: 8px !important;
  padding: 4px 10px !important;
  font-size: 12px !important;
  margin: 0 2px !important;
  cursor: pointer;
  transition: all .15s;
}
.dataTables_wrapper .dataTables_paginate .paginate_button:hover {
  background: var(--surface2) !important;
  border-color: var(--ac) !important;
  color: var(--text) !important;
}
.dataTables_wrapper .dataTables_paginate .paginate_button.current {
  background: var(--ac) !important;
  border-color: var(--ac) !important;
  color: #fff !important;
}
.dataTables_wrapper .dataTables_paginate .paginate_button.disabled { opacity: .4; cursor: default; }

/* ══ Modal ══ */
.modal-backdrop {
  position: fixed; inset: 0; background: rgba(0,0,0,.55);
  backdrop-filter: blur(4px); z-index: 200;
  display: none; align-items: center; justify-content: center; padding: 16px;
}
.modal-backdrop.modal-open { display: flex; }
.modal-box {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: 20px;
  width: 100%; max-width: 480px;
  box-shadow: 0 24px 60px rgba(0,0,0,.4);
  animation: fadeUp .25s ease both;
}
.modal-box.modal-sm { max-width: 380px; }

/* ══ Form inputs ══ */
.f-input {
  width: 100%;
  background: var(--surface2);
  border: 1px solid var(--border);
  color: var(--text);
  border-radius: 10px;
  padding: 9px 12px;
  font-size: 13.5px;
  font-family: 'Plus Jakarta Sans', sans-serif;
  outline: none;
  transition: border-color .15s;
}
.f-input:focus { border-color: var(--ac); }
.f-input::placeholder { color: var(--muted); }
.f-label { font-size: 12.5px; font-weight: 600; color: var(--sub); margin-bottom: 6px; display: block; }
.f-error { font-size: 11.5px; color: #f87171; margin-top: 4px; }

/* ══ Toast ══ */
.toast-wrap { position: fixed; top: 20px; right: 20px; z-index: 9999; display: flex; flex-direction: column; gap: 10px; pointer-events: none; }
.toast {
  display: flex; align-items: center; gap: 12px;
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: 14px;
  padding: 14px 18px;
  min-width: 280px; max-width: 360px;
  box-shadow: 0 8px 32px rgba(0,0,0,.3);
  pointer-events: all;
  animation: slideIn .3s ease both;
}
.toast.toast-out { animation: slideOut .3s ease forwards; }
@keyframes slideIn { from { opacity:0; transform: translateX(40px); } to { opacity:1; transform: translateX(0); } }
@keyframes slideOut { to { opacity:0; transform: translateX(40px); } }
.toast-icon { width: 36px; height: 36px; border-radius: 10px; display:grid; place-items:center; font-size: 15px; flex-shrink:0; }
</style>
@endpush

@section('content')

{{-- ── Toast Container ── --}}
<div id="toast-container" class="toast-wrap"></div>

{{-- ── Page Header ── --}}
<div class="flex items-center justify-between animate-fadeUp">
  <div>
    <h2 class="font-display font-bold text-[20px]" style="color:var(--text)">Manajemen Users</h2>
    <p class="text-[13px] mt-0.5" style="color:var(--muted)">Kelola semua akun pengguna sistem</p>
  </div>
  <button onclick="openModal('modal-create')"
    class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-[13.5px] font-semibold text-white a-grad shadow-lg transition-all hover:-translate-y-0.5">
    <i class="fa-solid fa-plus text-[12px]"></i> Tambah User
  </button>
</div>

{{-- ── Table Card ── --}}
<div class="rounded-2xl overflow-hidden border animate-fadeUp d1" style="background:var(--surface);border-color:var(--border)">
  <div class="flex items-center justify-between px-5 py-4 border-b" style="border-color:var(--border)">
    <span class="font-display font-semibold text-[15px]" style="color:var(--text)">Daftar Users</span>
    <span class="text-[12px] px-2.5 py-1 rounded-full a-bg-lt a-text font-semibold">{{ $users->count() }} total</span>
  </div>
  <div class="p-5">
    <table id="users-table" class="w-full" style="width:100%">
      <thead>
        <tr>
          <th>#</th>
          <th>Nama</th>
          <th>Email</th>
          <th>Status</th>
          <th>Bergabung</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        @foreach($users as $i => $user)
        <tr>
          <td class="text-center" style="color:var(--muted);width:48px">{{ $i + 1 }}</td>
          <td>
            <div class="flex items-center gap-3">
              <div class="a-grad w-9 h-9 rounded-xl grid place-items-center font-bold text-sm text-white flex-shrink-0">
                {{ strtoupper(substr($user->name, 0, 1)) }}
              </div>
              <div>
                <div class="font-semibold text-[13.5px]" style="color:var(--text)">{{ $user->name }}</div>
                @if($user->id === auth()->id())
                  <span class="text-[10.5px] a-text font-semibold">(Anda)</span>
                @endif
              </div>
            </div>
          </td>
          <td style="color:var(--sub)">{{ $user->email }}</td>
          <td>
            @if($user->email_verified_at)
              <span class="inline-flex items-center gap-1.5 bg-emerald-500/15 text-emerald-400 text-[11px] font-semibold px-2.5 py-1 rounded-full">
                <i class="fa-solid fa-circle-check text-[10px]"></i> Terverifikasi
              </span>
            @else
              <span class="inline-flex items-center gap-1.5 bg-amber-500/15 text-amber-400 text-[11px] font-semibold px-2.5 py-1 rounded-full">
                <i class="fa-solid fa-clock text-[10px]"></i> Belum Verifikasi
              </span>
            @endif
          </td>
          <td class="text-[12.5px]" style="color:var(--muted)">{{ $user->created_at->format('d M Y') }}</td>
          <td>
            <div class="flex items-center gap-1.5">
              <button onclick="openEdit({{ $user->id }}, '{{ addslashes($user->name) }}', '{{ $user->email }}')"
                class="w-8 h-8 rounded-lg grid place-items-center border text-[12px] transition-all"
                style="background:var(--surface2);border-color:var(--border);color:var(--muted)"
                onmouseover="this.style.borderColor='var(--ac)';this.style.color='var(--ac)'"
                onmouseout="this.style.borderColor='var(--border)';this.style.color='var(--muted)'"
                title="Edit">
                <i class="fa-solid fa-pen-to-square"></i>
              </button>
              @if($user->id !== auth()->id())
              <button onclick="openDelete({{ $user->id }}, '{{ addslashes($user->name) }}')"
                class="w-8 h-8 rounded-lg grid place-items-center border text-[12px] transition-all"
                style="background:var(--surface2);border-color:var(--border);color:var(--muted)"
                onmouseover="this.style.borderColor='#f87171';this.style.color='#f87171'"
                onmouseout="this.style.borderColor='var(--border)';this.style.color='var(--muted)'"
                title="Hapus">
                <i class="fa-solid fa-trash"></i>
              </button>
              @endif
            </div>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>

{{-- ════════════════════════════════════════
     MODAL: TAMBAH USER
════════════════════════════════════════ --}}
<div id="modal-create" class="modal-backdrop">
  <div class="modal-box">
    <div class="flex items-center justify-between px-6 py-5 border-b" style="border-color:var(--border)">
      <div class="flex items-center gap-3">
        <div class="a-bg-lt a-text w-10 h-10 rounded-xl grid place-items-center text-base"><i class="fa-solid fa-user-plus"></i></div>
        <div>
          <h3 class="font-display font-bold text-[16px]" style="color:var(--text)">Tambah User</h3>
          <p class="text-[12px]" style="color:var(--muted)">Buat akun pengguna baru</p>
        </div>
      </div>
      <button onclick="closeModal('modal-create')" class="w-8 h-8 rounded-lg grid place-items-center text-[13px] transition-colors"
        style="color:var(--muted)" onmouseover="this.style.background='var(--surface2)'" onmouseout="this.style.background='transparent'">
        <i class="fa-solid fa-xmark"></i>
      </button>
    </div>
    <form id="form-create" class="px-6 py-5 space-y-4">
      @csrf
      <div>
        <label class="f-label">Nama Lengkap <span class="text-red-400">*</span></label>
        <input type="text" name="name" class="f-input" placeholder="Masukkan nama lengkap">
        <p class="f-error hidden" id="err-create-name"></p>
      </div>
      <div>
        <label class="f-label">Email <span class="text-red-400">*</span></label>
        <input type="email" name="email" class="f-input" placeholder="contoh@email.com">
        <p class="f-error hidden" id="err-create-email"></p>
      </div>
      <div>
        <label class="f-label">Password <span class="text-red-400">*</span></label>
        <div class="relative">
          <input type="password" name="password" id="create-password" class="f-input pr-10" placeholder="Minimal 8 karakter">
          <button type="button" onclick="togglePass('create-password', this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-[13px]" style="color:var(--muted)">
            <i class="fa-solid fa-eye"></i>
          </button>
        </div>
        <p class="f-error hidden" id="err-create-password"></p>
      </div>
      <div>
        <label class="f-label">Konfirmasi Password <span class="text-red-400">*</span></label>
        <div class="relative">
          <input type="password" name="password_confirmation" id="create-password-confirm" class="f-input pr-10" placeholder="Ulangi password">
          <button type="button" onclick="togglePass('create-password-confirm', this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-[13px]" style="color:var(--muted)">
            <i class="fa-solid fa-eye"></i>
          </button>
        </div>
      </div>
      <div class="flex gap-3 pt-2">
        <button type="button" onclick="closeModal('modal-create')"
          class="flex-1 py-2.5 rounded-xl border text-[13.5px] font-semibold transition-colors"
          style="border-color:var(--border);color:var(--sub)"
          onmouseover="this.style.background='var(--surface2)'" onmouseout="this.style.background='transparent'">
          Batal
        </button>
        <button type="submit" id="btn-create"
          class="flex-1 py-2.5 rounded-xl text-[13.5px] font-semibold text-white a-grad shadow transition-all hover:-translate-y-0.5">
          <i class="fa-solid fa-plus mr-1.5 text-[12px]"></i>Tambah User
        </button>
      </div>
    </form>
  </div>
</div>

{{-- ════════════════════════════════════════
     MODAL: EDIT USER
════════════════════════════════════════ --}}
<div id="modal-edit" class="modal-backdrop">
  <div class="modal-box">
    <div class="flex items-center justify-between px-6 py-5 border-b" style="border-color:var(--border)">
      <div class="flex items-center gap-3">
        <div class="a-bg-lt a-text w-10 h-10 rounded-xl grid place-items-center text-base"><i class="fa-solid fa-pen-to-square"></i></div>
        <div>
          <h3 class="font-display font-bold text-[16px]" style="color:var(--text)">Edit User</h3>
          <p class="text-[12px]" style="color:var(--muted)">Perbarui data pengguna</p>
        </div>
      </div>
      <button onclick="closeModal('modal-edit')" class="w-8 h-8 rounded-lg grid place-items-center text-[13px] transition-colors"
        style="color:var(--muted)" onmouseover="this.style.background='var(--surface2)'" onmouseout="this.style.background='transparent'">
        <i class="fa-solid fa-xmark"></i>
      </button>
    </div>
    <form id="form-edit" class="px-6 py-5 space-y-4">
      @csrf
      @method('PUT')
      <input type="hidden" name="_user_id" id="edit-user-id">
      <div>
        <label class="f-label">Nama Lengkap <span class="text-red-400">*</span></label>
        <input type="text" name="name" id="edit-name" class="f-input" placeholder="Masukkan nama lengkap">
        <p class="f-error hidden" id="err-edit-name"></p>
      </div>
      <div>
        <label class="f-label">Email <span class="text-red-400">*</span></label>
        <input type="email" name="email" id="edit-email" class="f-input" placeholder="contoh@email.com">
        <p class="f-error hidden" id="err-edit-email"></p>
      </div>
      <div class="rounded-xl p-4" style="background:var(--surface2);border:1px solid var(--border)">
        <p class="text-[12px] font-semibold mb-3" style="color:var(--muted)">
          <i class="fa-solid fa-lock mr-1.5"></i>Ganti Password <span class="font-normal">(kosongkan jika tidak ingin mengubah)</span>
        </p>
        <div class="space-y-3">
          <div>
            <div class="relative">
              <input type="password" name="password" id="edit-password" class="f-input pr-10" placeholder="Password baru">
              <button type="button" onclick="togglePass('edit-password', this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-[13px]" style="color:var(--muted)">
                <i class="fa-solid fa-eye"></i>
              </button>
            </div>
            <p class="f-error hidden" id="err-edit-password"></p>
          </div>
          <div class="relative">
            <input type="password" name="password_confirmation" id="edit-password-confirm" class="f-input pr-10" placeholder="Konfirmasi password baru">
            <button type="button" onclick="togglePass('edit-password-confirm', this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-[13px]" style="color:var(--muted)">
              <i class="fa-solid fa-eye"></i>
            </button>
          </div>
        </div>
      </div>
      <div class="flex gap-3 pt-2">
        <button type="button" onclick="closeModal('modal-edit')"
          class="flex-1 py-2.5 rounded-xl border text-[13.5px] font-semibold transition-colors"
          style="border-color:var(--border);color:var(--sub)"
          onmouseover="this.style.background='var(--surface2)'" onmouseout="this.style.background='transparent'">
          Batal
        </button>
        <button type="submit" id="btn-edit"
          class="flex-1 py-2.5 rounded-xl text-[13.5px] font-semibold text-white a-grad shadow transition-all hover:-translate-y-0.5">
          <i class="fa-solid fa-floppy-disk mr-1.5 text-[12px]"></i>Simpan Perubahan
        </button>
      </div>
    </form>
  </div>
</div>

{{-- ════════════════════════════════════════
     MODAL: HAPUS USER
════════════════════════════════════════ --}}
<div id="modal-delete" class="modal-backdrop">
  <div class="modal-box modal-sm">
    <div class="px-6 py-6 text-center">
      <div class="w-14 h-14 rounded-2xl bg-red-500/15 text-red-400 grid place-items-center text-2xl mx-auto mb-4">
        <i class="fa-solid fa-triangle-exclamation"></i>
      </div>
      <h3 class="font-display font-bold text-[17px] mb-2" style="color:var(--text)">Hapus User?</h3>
      <p class="text-[13px]" style="color:var(--muted)">
        Anda akan menghapus akun <span id="delete-user-name" class="font-semibold" style="color:var(--text)"></span>.<br>
        Tindakan ini tidak dapat dibatalkan.
      </p>
    </div>
    <div class="flex gap-3 px-6 pb-6">
      <button onclick="closeModal('modal-delete')"
        class="flex-1 py-2.5 rounded-xl border text-[13.5px] font-semibold transition-colors"
        style="border-color:var(--border);color:var(--sub)"
        onmouseover="this.style.background='var(--surface2)'" onmouseout="this.style.background='transparent'">
        Batal
      </button>
      <button id="btn-delete" onclick="confirmDelete()"
        class="flex-1 py-2.5 rounded-xl text-[13.5px] font-semibold text-white bg-red-500 hover:bg-red-600 transition-colors">
        <i class="fa-solid fa-trash mr-1.5 text-[12px]"></i>Ya, Hapus
      </button>
    </div>
  </div>
</div>

@endsection

{{-- ── Scripts ── --}}
@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script>
// ════════════════════════════════════════
// DataTable Init
// ════════════════════════════════════════
$(document).ready(function () {
  $('#users-table').DataTable({
    language: {
      search:           '',
      searchPlaceholder:'Cari user...',
      lengthMenu:       'Tampilkan _MENU_ data',
      info:             'Menampilkan _START_–_END_ dari _TOTAL_ data',
      infoEmpty:        'Tidak ada data',
      infoFiltered:     '(difilter dari _MAX_ total)',
      paginate: { previous: '<i class="fa-solid fa-chevron-left"></i>', next: '<i class="fa-solid fa-chevron-right"></i>' },
      emptyTable:       'Tidak ada data user',
      zeroRecords:      'Tidak ditemukan data yang cocok',
    },
    columnDefs: [
      { orderable: false, targets: [0, 5] },
      { className: 'text-center', targets: [0] },
    ],
    pageLength: 10,
    dom: '<"flex flex-col sm:flex-row gap-3 items-start sm:items-center justify-between mb-4"lf>rt<"flex flex-col sm:flex-row gap-3 items-start sm:items-center justify-between mt-4"ip>',
  });
});

// ════════════════════════════════════════
// Modal Helpers
// ════════════════════════════════════════
function openModal(id) {
  document.getElementById(id).classList.add('modal-open');
  document.body.style.overflow = 'hidden';
}
function closeModal(id) {
  document.getElementById(id).classList.remove('modal-open');
  document.body.style.overflow = '';
  clearErrors(id);
}

// Close on backdrop click
document.querySelectorAll('.modal-backdrop').forEach(el => {
  el.addEventListener('click', function (e) {
    if (e.target === this) closeModal(this.id);
  });
});

// ════════════════════════════════════════
// Toggle Password Visibility
// ════════════════════════════════════════
function togglePass(inputId, btn) {
  const input = document.getElementById(inputId);
  const icon  = btn.querySelector('i');
  if (input.type === 'password') {
    input.type = 'text';
    icon.classList.replace('fa-eye', 'fa-eye-slash');
  } else {
    input.type = 'password';
    icon.classList.replace('fa-eye-slash', 'fa-eye');
  }
}

// ════════════════════════════════════════
// Toast Notification
// ════════════════════════════════════════
function showToast(type, message) {
  const container = document.getElementById('toast-container');
  const isSuccess = type === 'success';
  const toast = document.createElement('div');
  toast.className = 'toast';

  toast.innerHTML = `
    <div class="toast-icon" style="background:${isSuccess ? 'rgba(16,185,129,.15)' : 'rgba(248,113,113,.15)'};color:${isSuccess ? '#34d399' : '#f87171'}">
      <i class="fa-solid ${isSuccess ? 'fa-circle-check' : 'fa-circle-xmark'}"></i>
    </div>
    <div class="flex-1 min-w-0">
      <p style="font-size:13.5px;font-weight:600;color:var(--text)">${isSuccess ? 'Berhasil' : 'Gagal'}</p>
      <p style="font-size:12px;color:var(--muted);margin-top:2px">${message}</p>
    </div>
    <button onclick="dismissToast(this.closest('.toast'))" style="color:var(--muted);font-size:13px;padding:4px;flex-shrink:0">
      <i class="fa-solid fa-xmark"></i>
    </button>
  `;

  container.appendChild(toast);

  setTimeout(() => dismissToast(toast), 4000);
}
function dismissToast(toast) {
  if (!toast || toast.classList.contains('toast-out')) return;
  toast.classList.add('toast-out');
  setTimeout(() => toast.remove(), 300);
}

// ════════════════════════════════════════
// Error Handling
// ════════════════════════════════════════
function showErrors(prefix, errors) {
  Object.keys(errors).forEach(field => {
    const el = document.getElementById(`err-${prefix}-${field}`);
    if (el) { el.textContent = errors[field][0]; el.classList.remove('hidden'); }
  });
}
function clearErrors(modalId) {
  document.querySelectorAll(`#${modalId} .f-error`).forEach(el => {
    el.textContent = ''; el.classList.add('hidden');
  });
  document.querySelectorAll(`#${modalId} .f-input`).forEach(el => {
    el.style.borderColor = '';
  });
}
function setLoading(btnId, loading) {
  const btn = document.getElementById(btnId);
  btn.disabled = loading;
  btn.style.opacity = loading ? '.6' : '1';
}

// ════════════════════════════════════════
// CREATE
// ════════════════════════════════════════
document.getElementById('form-create').addEventListener('submit', function (e) {
  e.preventDefault();
  clearErrors('modal-create');
  setLoading('btn-create', true);

  const data = new FormData(this);

  fetch('{{ route("admin.users.store") }}', {
    method: 'POST',
    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
    body: data,
  })
  .then(async res => ({ ok: res.ok, data: await res.json() }))
  .then(({ ok, data }) => {
    if (ok) {
      closeModal('modal-create');
      document.getElementById('form-create').reset();
      showToast('success', data.message);
      setTimeout(() => location.reload(), 1200);
    } else {
      if (data.errors) showErrors('create', data.errors);
      else showToast('error', data.message || 'Terjadi kesalahan.');
    }
  })
  .catch(() => showToast('error', 'Gagal terhubung ke server.'))
  .finally(() => setLoading('btn-create', false));
});

// ════════════════════════════════════════
// EDIT
// ════════════════════════════════════════
function openEdit(id, name, email) {
  document.getElementById('edit-user-id').value = id;
  document.getElementById('edit-name').value    = name;
  document.getElementById('edit-email').value   = email;
  document.getElementById('edit-password').value = '';
  document.getElementById('edit-password-confirm').value = '';
  openModal('modal-edit');
}

document.getElementById('form-edit').addEventListener('submit', function (e) {
  e.preventDefault();
  clearErrors('modal-edit');
  setLoading('btn-edit', true);

  const id   = document.getElementById('edit-user-id').value;
  const data = new FormData(this);
  data.append('_method', 'PUT');

  fetch(`/admin/users/${id}`, {
    method: 'POST',
    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
    body: data,
  })
  .then(async res => ({ ok: res.ok, data: await res.json() }))
  .then(({ ok, data }) => {
    if (ok) {
      closeModal('modal-edit');
      showToast('success', data.message);
      setTimeout(() => location.reload(), 1200);
    } else {
      if (data.errors) showErrors('edit', data.errors);
      else showToast('error', data.message || 'Terjadi kesalahan.');
    }
  })
  .catch(() => showToast('error', 'Gagal terhubung ke server.'))
  .finally(() => setLoading('btn-edit', false));
});

// ════════════════════════════════════════
// DELETE
// ════════════════════════════════════════
let deleteUserId = null;

function openDelete(id, name) {
  deleteUserId = id;
  document.getElementById('delete-user-name').textContent = name;
  openModal('modal-delete');
}

function confirmDelete() {
  if (!deleteUserId) return;
  setLoading('btn-delete', true);

  fetch(`/admin/users/${deleteUserId}`, {
    method: 'POST',
    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
    body: new URLSearchParams({ _method: 'DELETE' }),
  })
  .then(async res => ({ ok: res.ok, data: await res.json() }))
  .then(({ ok, data }) => {
    closeModal('modal-delete');
    if (ok) {
      showToast('success', data.message);
      setTimeout(() => location.reload(), 1200);
    } else {
      showToast('error', data.message || 'Terjadi kesalahan.');
    }
  })
  .catch(() => showToast('error', 'Gagal terhubung ke server.'))
  .finally(() => setLoading('btn-delete', false));
}

// ════════════════════════════════════════
// Flash message dari session (redirect)
// ════════════════════════════════════════
@if(session('success'))
  document.addEventListener('DOMContentLoaded', () => showToast('success', '{{ session("success") }}'));
@endif
@if(session('error'))
  document.addEventListener('DOMContentLoaded', () => showToast('error', '{{ session("error") }}'));
@endif
</script>
@endpush
