@extends('layouts.admin')
@section('title','User Roles')
@section('page-title','User Roles')

@push('styles')
@include('admin.partials.datatable-styles')
@endpush

@section('content')
<div id="toast-container" class="toast-wrap"></div>

{{-- Header --}}
<div class="flex items-center justify-between animate-fadeUp">
  <div>
    <h2 class="font-display font-bold text-[20px]" style="color:var(--text)">User Roles</h2>
    <p class="text-[13px] mt-0.5" style="color:var(--muted)">Atur peran untuk setiap pengguna — 1 user dapat memiliki banyak role</p>
  </div>
</div>

{{-- Info --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 animate-fadeUp d1">
  <div class="rounded-2xl p-5 border flex items-center gap-4" style="background:var(--surface);border-color:var(--border)">
    <div class="a-bg-lt a-text w-11 h-11 rounded-xl grid place-items-center text-lg flex-shrink-0"><i class="fa-solid fa-users"></i></div>
    <div><div class="font-display text-[26px] font-bold" style="color:var(--text)">{{ $users->count() }}</div><div class="text-[12px]" style="color:var(--muted)">Total Users</div></div>
  </div>
  <div class="rounded-2xl p-5 border flex items-center gap-4" style="background:var(--surface);border-color:var(--border)">
    <div class="bg-emerald-500/15 text-emerald-400 w-11 h-11 rounded-xl grid place-items-center text-lg flex-shrink-0"><i class="fa-solid fa-user-check"></i></div>
    <div><div class="font-display text-[26px] font-bold" style="color:var(--text)">{{ $users->filter(fn($u)=>$u->roles->isNotEmpty())->count() }}</div><div class="text-[12px]" style="color:var(--muted)">Sudah Punya Role</div></div>
  </div>
  <div class="rounded-2xl p-5 border flex items-center gap-4" style="background:var(--surface);border-color:var(--border)">
    <div class="bg-amber-500/15 text-amber-400 w-11 h-11 rounded-xl grid place-items-center text-lg flex-shrink-0"><i class="fa-solid fa-user-xmark"></i></div>
    <div><div class="font-display text-[26px] font-bold" style="color:var(--text)">{{ $users->filter(fn($u)=>$u->roles->isEmpty())->count() }}</div><div class="text-[12px]" style="color:var(--muted)">Belum Punya Role</div></div>
  </div>
</div>

{{-- Table --}}
<div class="rounded-2xl overflow-hidden border animate-fadeUp d2" style="background:var(--surface);border-color:var(--border)">
  <div class="flex items-center justify-between px-5 py-4 border-b" style="border-color:var(--border)">
    <span class="font-display font-semibold text-[15px]" style="color:var(--text)">Daftar User & Roles</span>
    <span class="text-[12px] px-2.5 py-1 rounded-full a-bg-lt a-text font-semibold">{{ $users->count() }} users</span>
  </div>
  <div class="p-5">
    <table id="userroles-table" class="w-full" style="width:100%">
      <thead>
        <tr>
          <th>#</th>
          <th>User</th>
          <th>Email</th>
          <th>Roles</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        @foreach($users as $i => $user)
        @php $userRoleIds = $user->roles->pluck('id')->toArray(); @endphp
        <tr>
          <td class="text-center" style="color:var(--muted);width:48px">{{ $i + 1 }}</td>
          <td>
            <div class="flex items-center gap-3">
              <div class="a-grad w-9 h-9 rounded-xl grid place-items-center font-bold text-sm text-white flex-shrink-0">{{ strtoupper(substr($user->name,0,1)) }}</div>
              <div>
                <div class="font-semibold text-[13.5px]" style="color:var(--text)">{{ $user->name }}</div>
                @if($user->id===auth()->id())<span class="text-[10.5px] a-text font-semibold">(Anda)</span>@endif
              </div>
            </div>
          </td>
          <td style="color:var(--sub);font-size:12.5px">{{ $user->email }}</td>
          <td>
            <div class="flex flex-wrap gap-1.5">
              @forelse($user->roles as $role)
                @php $colors=['bg-blue-500/15 text-blue-400','bg-violet-500/15 text-violet-400','bg-emerald-500/15 text-emerald-400','bg-amber-500/15 text-amber-400','bg-rose-500/15 text-rose-400'];
                $c=$colors[$role->id % count($colors)]; @endphp
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold {{ $c }}">{{ $role->display_name }}</span>
              @empty
                <span class="text-[12px]" style="color:var(--muted)">— Belum ada role</span>
              @endforelse
            </div>
          </td>
          <td>
            <button onclick="openManage({{ $user->id }},'{{ addslashes($user->name) }}',{{ json_encode($userRoleIds) }})"
              class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg border text-[12px] font-medium transition-all"
              style="background:var(--surface2);border-color:var(--border);color:var(--sub)"
              onmouseover="this.style.borderColor='var(--ac)';this.style.color='var(--ac)'"
              onmouseout="this.style.borderColor='var(--border)';this.style.color='var(--sub)'">
              <i class="fa-solid fa-user-tag text-[11px]"></i> Kelola Roles
            </button>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>

{{-- MODAL KELOLA ROLES --}}
<div id="modal-manage" class="modal-backdrop">
  <div class="modal-box modal-lg">
    <div class="flex items-center justify-between px-6 py-5 border-b" style="border-color:var(--border)">
      <div class="flex items-center gap-3">
        <div class="a-bg-lt a-text w-10 h-10 rounded-xl grid place-items-center"><i class="fa-solid fa-user-tag"></i></div>
        <div>
          <h3 class="font-display font-bold text-[16px]" style="color:var(--text)">Kelola Roles</h3>
          <p class="text-[12px]" style="color:var(--muted)">User: <span id="modal-username" class="font-semibold" style="color:var(--text)"></span></p>
        </div>
      </div>
      <button onclick="closeModal('modal-manage')" class="w-8 h-8 rounded-lg grid place-items-center" style="color:var(--muted)" onmouseover="this.style.background='var(--surface2)'" onmouseout="this.style.background='transparent'"><i class="fa-solid fa-xmark"></i></button>
    </div>

    <form id="form-manage" class="px-6 py-5">
      <input type="hidden" id="manage-user-id">

      <p class="text-[12.5px] font-semibold mb-3" style="color:var(--muted)">
        <i class="fa-solid fa-circle-info mr-1.5 a-text"></i>
        Centang role yang ingin diberikan. User dapat memiliki lebih dari 1 role.
      </p>

      @if($roles->isEmpty())
        <div class="text-center py-8" style="color:var(--muted)">
          <i class="fa-solid fa-id-badge text-3xl mb-3 block a-text opacity-50"></i>
          Belum ada role tersedia. Buat role terlebih dahulu.
        </div>
      @else
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 max-h-72 overflow-y-auto pr-1" id="roles-checklist">
          @foreach($roles as $role)
          @php $colors=['bg-blue-500/15 text-blue-400','bg-violet-500/15 text-violet-400','bg-emerald-500/15 text-emerald-400','bg-amber-500/15 text-amber-400','bg-rose-500/15 text-rose-400'];
          $c=$colors[$role->id % count($colors)]; @endphp
          <label class="cb-item" data-id="{{ $role->id }}">
            <input type="checkbox" name="role_ids[]" value="{{ $role->id }}" class="role-cb">
            <div class="{{ $c }} w-8 h-8 rounded-lg grid place-items-center text-[13px] font-bold flex-shrink-0">{{ strtoupper(substr($role->display_name,0,1)) }}</div>
            <div class="flex-1 min-w-0">
              <div class="text-[13px] font-semibold" style="color:var(--text)">{{ $role->display_name }}</div>
              <div class="text-[11px]" style="color:var(--muted)"><code style="font-family:monospace">{{ $role->name }}</code></div>
            </div>
          </label>
          @endforeach
        </div>

        <div class="flex items-center justify-between mt-4 pt-4 border-t" style="border-color:var(--border)">
          <button type="button" onclick="toggleAllRoles()" id="btn-toggle-all"
            class="text-[12px] a-text font-medium flex items-center gap-1.5">
            <i class="fa-solid fa-check-double text-[11px]"></i> Pilih Semua
          </button>
          <div class="flex gap-3">
            <button type="button" onclick="closeModal('modal-manage')" class="px-4 py-2 rounded-xl border text-[13px] font-semibold" style="border-color:var(--border);color:var(--sub)" onmouseover="this.style.background='var(--surface2)'" onmouseout="this.style.background='transparent'">Batal</button>
            <button type="submit" id="btn-manage" class="px-5 py-2 rounded-xl text-[13px] font-semibold text-white a-grad shadow"><i class="fa-solid fa-floppy-disk mr-1.5 text-[11px]"></i>Simpan</button>
          </div>
        </div>
      @endif
    </form>
  </div>
</div>
@endsection

@push('scripts')
@include('admin.partials.datatable-scripts')
<script>
$(()=>$('#userroles-table').DataTable({language:{...DT_LANG,searchPlaceholder:'Cari user...'},columnDefs:[{orderable:false,targets:[0,3,4]},{className:'text-center',targets:[0]}],pageLength:10,dom:DT_DOM}));

// Checkbox visual update
document.querySelectorAll('.role-cb').forEach(cb=>{
  cb.addEventListener('change',function(){
    this.closest('.cb-item').classList.toggle('checked',this.checked);
  });
});

function openManage(userId, userName, activeRoleIds){
  document.getElementById('manage-user-id').value = userId;
  document.getElementById('modal-username').textContent = userName;
  // reset & set checkboxes
  document.querySelectorAll('.role-cb').forEach(cb=>{
    cb.checked = activeRoleIds.includes(parseInt(cb.value));
    cb.closest('.cb-item').classList.toggle('checked', cb.checked);
  });
  updateToggleBtn();
  openModal('modal-manage');
}

let allSelected = false;
function toggleAllRoles(){
  allSelected = !allSelected;
  document.querySelectorAll('.role-cb').forEach(cb=>{
    cb.checked = allSelected;
    cb.closest('.cb-item').classList.toggle('checked', allSelected);
  });
  updateToggleBtn();
}
function updateToggleBtn(){
  const total = document.querySelectorAll('.role-cb').length;
  const checked = document.querySelectorAll('.role-cb:checked').length;
  allSelected = checked === total;
  const btn = document.getElementById('btn-toggle-all');
  if(btn) btn.innerHTML = allSelected
    ? '<i class="fa-solid fa-xmark text-[11px]"></i> Hapus Semua'
    : '<i class="fa-solid fa-check-double text-[11px]"></i> Pilih Semua';
}
document.querySelectorAll('.role-cb').forEach(cb=>cb.addEventListener('change', updateToggleBtn));

document.getElementById('form-manage').addEventListener('submit', function(e){
  e.preventDefault();
  setLoading('btn-manage', true);
  const userId = document.getElementById('manage-user-id').value;
  const checked = [...document.querySelectorAll('.role-cb:checked')].map(cb=>cb.value);
  const body = new URLSearchParams({_method:'PUT'});
  checked.forEach(id => body.append('role_ids[]', id));

  fetch(`/admin/user-roles/${userId}`, {
    method: 'POST',
    headers: {'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'},
    body,
  })
  .then(async r=>({ok:r.ok,data:await r.json()}))
  .then(({ok,data})=>{
    closeModal('modal-manage');
    showToast(ok?'success':'error', data.message);
    if(ok) setTimeout(()=>location.reload(), 1200);
  })
  .catch(()=>showToast('error','Gagal terhubung ke server.'))
  .finally(()=>setLoading('btn-manage',false));
});

@if(session('success'))document.addEventListener('DOMContentLoaded',()=>showToast('success','{{ session("success") }}'));@endif
@if(session('error'))document.addEventListener('DOMContentLoaded',()=>showToast('error','{{ session("error") }}'));@endif
</script>
@endpush
