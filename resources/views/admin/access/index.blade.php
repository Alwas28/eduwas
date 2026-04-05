@extends('layouts.admin')
@section('title','Access')
@section('page-title','Access')

@push('styles')
@include('admin.partials.datatable-styles')
@endpush

@section('content')
<div id="toast-container" class="toast-wrap"></div>

{{-- Header --}}
<div class="flex items-center justify-between animate-fadeUp">
  <div>
    <h2 class="font-display font-bold text-[20px]" style="color:var(--text)">Manajemen Access</h2>
    <p class="text-[13px] mt-0.5" style="color:var(--muted)">Kelola daftar hak akses (permission) sistem</p>
  </div>
  <button onclick="openModal('modal-create')"
    class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-[13.5px] font-semibold text-white a-grad shadow-lg transition-all hover:-translate-y-0.5">
    <i class="fa-solid fa-plus text-[12px]"></i> Tambah Access
  </button>
</div>

{{-- Stat Cards --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 animate-fadeUp d1">
  @foreach($groups as $group)
  <div class="rounded-2xl p-4 border flex items-center gap-3" style="background:var(--surface);border-color:var(--border)">
    <div class="a-bg-lt a-text w-10 h-10 rounded-xl grid place-items-center text-base flex-shrink-0"><i class="fa-solid fa-key"></i></div>
    <div>
      <div class="font-display text-[22px] font-bold leading-none" style="color:var(--text)">{{ $accesses->where('group',$group)->count() }}</div>
      <div class="text-[11.5px] mt-0.5 truncate" style="color:var(--muted)">{{ $group }}</div>
    </div>
  </div>
  @endforeach
  @if($groups->isEmpty())
  <div class="col-span-4 rounded-2xl p-4 border text-center text-[13px]" style="background:var(--surface);border-color:var(--border);color:var(--muted)">
    Belum ada data access
  </div>
  @endif
</div>

{{-- Table --}}
<div class="rounded-2xl overflow-hidden border animate-fadeUp d2" style="background:var(--surface);border-color:var(--border)">
  <div class="flex items-center justify-between px-5 py-4 border-b" style="border-color:var(--border)">
    <span class="font-display font-semibold text-[15px]" style="color:var(--text)">Daftar Access</span>
    <span class="text-[12px] px-2.5 py-1 rounded-full a-bg-lt a-text font-semibold">{{ $accesses->count() }} total</span>
  </div>
  <div class="p-5">
    <table id="access-table" class="w-full" style="width:100%">
      <thead>
        <tr>
          <th>#</th>
          <th>Nama Access</th>
          <th>Display Name</th>
          <th>Group</th>
          <th>Deskripsi</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        @foreach($accesses as $i => $access)
        <tr>
          <td class="text-center" style="color:var(--muted);width:48px">{{ $i + 1 }}</td>
          <td><code class="px-2.5 py-1 rounded-lg text-[12px] font-semibold a-bg-lt a-text" style="font-family:monospace">{{ $access->name }}</code></td>
          <td><span class="font-medium" style="color:var(--text)">{{ $access->display_name }}</span></td>
          <td>
            @php $gc=['Users'=>'bg-blue-500/15 text-blue-400','Roles'=>'bg-violet-500/15 text-violet-400','Kelas'=>'bg-emerald-500/15 text-emerald-400','Akademik'=>'bg-amber-500/15 text-amber-400'];
            $cls=$gc[$access->group]??'bg-slate-500/15 text-slate-400'; @endphp
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold {{ $cls }}">{{ $access->group }}</span>
          </td>
          <td style="color:var(--muted)">{{ $access->description ?: '—' }}</td>
          <td>
            <div class="flex items-center gap-1.5">
              <button onclick="openEdit({{ $access->id }},'{{ addslashes($access->name) }}','{{ addslashes($access->display_name) }}','{{ addslashes($access->group) }}','{{ addslashes($access->description ?? '') }}')"
                class="w-8 h-8 rounded-lg grid place-items-center border text-[12px] transition-all"
                style="background:var(--surface2);border-color:var(--border);color:var(--muted)"
                onmouseover="this.style.borderColor='var(--ac)';this.style.color='var(--ac)'"
                onmouseout="this.style.borderColor='var(--border)';this.style.color='var(--muted)'" title="Edit">
                <i class="fa-solid fa-pen-to-square"></i>
              </button>
              <button onclick="openDelete({{ $access->id }},'{{ addslashes($access->display_name) }}')"
                class="w-8 h-8 rounded-lg grid place-items-center border text-[12px] transition-all"
                style="background:var(--surface2);border-color:var(--border);color:var(--muted)"
                onmouseover="this.style.borderColor='#f87171';this.style.color='#f87171'"
                onmouseout="this.style.borderColor='var(--border)';this.style.color='var(--muted)'" title="Hapus">
                <i class="fa-solid fa-trash"></i>
              </button>
            </div>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>

{{-- MODAL TAMBAH --}}
<div id="modal-create" class="modal-backdrop">
  <div class="modal-box">
    <div class="flex items-center justify-between px-6 py-5 border-b" style="border-color:var(--border)">
      <div class="flex items-center gap-3">
        <div class="a-bg-lt a-text w-10 h-10 rounded-xl grid place-items-center"><i class="fa-solid fa-key"></i></div>
        <div><h3 class="font-display font-bold text-[16px]" style="color:var(--text)">Tambah Access</h3><p class="text-[12px]" style="color:var(--muted)">Buat hak akses baru</p></div>
      </div>
      <button onclick="closeModal('modal-create')" class="w-8 h-8 rounded-lg grid place-items-center" style="color:var(--muted)" onmouseover="this.style.background='var(--surface2)'" onmouseout="this.style.background='transparent'"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <form id="form-create" class="px-6 py-5 space-y-4">
      @csrf
      <div>
        <label class="f-label">Nama Access <span class="text-red-400">*</span></label>
        <input type="text" name="name" class="f-input" placeholder="contoh: users.view, kelas.create">
        <p class="f-hint">Huruf kecil, angka, strip (-), dan titik (.) saja.</p>
        <p class="f-error hidden" id="err-create-name"></p>
      </div>
      <div>
        <label class="f-label">Display Name <span class="text-red-400">*</span></label>
        <input type="text" name="display_name" class="f-input" placeholder="contoh: Lihat Users, Tambah Kelas">
        <p class="f-error hidden" id="err-create-display_name"></p>
      </div>
      <div>
        <label class="f-label">Group <span class="text-red-400">*</span></label>
        <input type="text" name="group" class="f-input" placeholder="contoh: Users, Roles, Kelas, Akademik" list="group-list">
        <datalist id="group-list">
          @foreach($groups as $g)<option value="{{ $g }}">@endforeach
          <option value="Users"><option value="Roles"><option value="Kelas"><option value="Akademik"><option value="Umum">
        </datalist>
        <p class="f-error hidden" id="err-create-group"></p>
      </div>
      <div>
        <label class="f-label">Deskripsi</label>
        <textarea name="description" class="f-input" rows="2" placeholder="Deskripsi singkat (opsional)"></textarea>
        <p class="f-error hidden" id="err-create-description"></p>
      </div>
      <div class="flex gap-3 pt-2">
        <button type="button" onclick="closeModal('modal-create')" class="flex-1 py-2.5 rounded-xl border text-[13.5px] font-semibold" style="border-color:var(--border);color:var(--sub)" onmouseover="this.style.background='var(--surface2)'" onmouseout="this.style.background='transparent'">Batal</button>
        <button type="submit" id="btn-create" class="flex-1 py-2.5 rounded-xl text-[13.5px] font-semibold text-white a-grad shadow"><i class="fa-solid fa-plus mr-1.5 text-[12px]"></i>Tambah</button>
      </div>
    </form>
  </div>
</div>

{{-- MODAL EDIT --}}
<div id="modal-edit" class="modal-backdrop">
  <div class="modal-box">
    <div class="flex items-center justify-between px-6 py-5 border-b" style="border-color:var(--border)">
      <div class="flex items-center gap-3">
        <div class="a-bg-lt a-text w-10 h-10 rounded-xl grid place-items-center"><i class="fa-solid fa-pen-to-square"></i></div>
        <div><h3 class="font-display font-bold text-[16px]" style="color:var(--text)">Edit Access</h3><p class="text-[12px]" style="color:var(--muted)">Perbarui hak akses</p></div>
      </div>
      <button onclick="closeModal('modal-edit')" class="w-8 h-8 rounded-lg grid place-items-center" style="color:var(--muted)" onmouseover="this.style.background='var(--surface2)'" onmouseout="this.style.background='transparent'"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <form id="form-edit" class="px-6 py-5 space-y-4">
      @csrf @method('PUT')
      <input type="hidden" id="edit-id">
      <div>
        <label class="f-label">Nama Access <span class="text-red-400">*</span></label>
        <input type="text" name="name" id="edit-name" class="f-input">
        <p class="f-hint">Huruf kecil, angka, strip (-), dan titik (.) saja.</p>
        <p class="f-error hidden" id="err-edit-name"></p>
      </div>
      <div>
        <label class="f-label">Display Name <span class="text-red-400">*</span></label>
        <input type="text" name="display_name" id="edit-display_name" class="f-input">
        <p class="f-error hidden" id="err-edit-display_name"></p>
      </div>
      <div>
        <label class="f-label">Group <span class="text-red-400">*</span></label>
        <input type="text" name="group" id="edit-group" class="f-input" list="group-list">
        <p class="f-error hidden" id="err-edit-group"></p>
      </div>
      <div>
        <label class="f-label">Deskripsi</label>
        <textarea name="description" id="edit-description" class="f-input" rows="2"></textarea>
        <p class="f-error hidden" id="err-edit-description"></p>
      </div>
      <div class="flex gap-3 pt-2">
        <button type="button" onclick="closeModal('modal-edit')" class="flex-1 py-2.5 rounded-xl border text-[13.5px] font-semibold" style="border-color:var(--border);color:var(--sub)" onmouseover="this.style.background='var(--surface2)'" onmouseout="this.style.background='transparent'">Batal</button>
        <button type="submit" id="btn-edit" class="flex-1 py-2.5 rounded-xl text-[13.5px] font-semibold text-white a-grad shadow"><i class="fa-solid fa-floppy-disk mr-1.5 text-[12px]"></i>Simpan</button>
      </div>
    </form>
  </div>
</div>

{{-- MODAL HAPUS --}}
<div id="modal-delete" class="modal-backdrop">
  <div class="modal-box modal-sm">
    <div class="px-6 py-6 text-center">
      <div class="w-14 h-14 rounded-2xl bg-red-500/15 text-red-400 grid place-items-center text-2xl mx-auto mb-4"><i class="fa-solid fa-triangle-exclamation"></i></div>
      <h3 class="font-display font-bold text-[17px] mb-2" style="color:var(--text)">Hapus Access?</h3>
      <p class="text-[13px]" style="color:var(--muted)">Anda akan menghapus <span id="delete-name" class="font-semibold" style="color:var(--text)"></span>. Tindakan ini tidak dapat dibatalkan.</p>
    </div>
    <div class="flex gap-3 px-6 pb-6">
      <button onclick="closeModal('modal-delete')" class="flex-1 py-2.5 rounded-xl border text-[13.5px] font-semibold" style="border-color:var(--border);color:var(--sub)" onmouseover="this.style.background='var(--surface2)'" onmouseout="this.style.background='transparent'">Batal</button>
      <button id="btn-delete" onclick="confirmDelete()" class="flex-1 py-2.5 rounded-xl text-[13.5px] font-semibold text-white bg-red-500 hover:bg-red-600 transition-colors"><i class="fa-solid fa-trash mr-1.5 text-[12px]"></i>Hapus</button>
    </div>
  </div>
</div>
@endsection

@push('scripts')
@include('admin.partials.datatable-scripts')
<script>
$(()=>$('#access-table').DataTable({language:{...DT_LANG,searchPlaceholder:'Cari access...'},columnDefs:[{orderable:false,targets:[0,5]},{className:'text-center',targets:[0]}],pageLength:10,dom:DT_DOM}));

document.getElementById('form-create').addEventListener('submit',function(e){
  e.preventDefault();clearErrors('modal-create');setLoading('btn-create',true);
  fetch('{{ route("admin.access.store") }}',{method:'POST',headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'},body:new FormData(this)})
  .then(async r=>({ok:r.ok,data:await r.json()}))
  .then(({ok,data})=>{if(ok){closeModal('modal-create');this.reset();showToast('success',data.message);setTimeout(()=>location.reload(),1200)}else{if(data.errors)showErrors('create',data.errors);else showToast('error',data.message)}})
  .catch(()=>showToast('error','Gagal terhubung ke server.')).finally(()=>setLoading('btn-create',false));
});

function openEdit(id,name,display_name,group,description){
  document.getElementById('edit-id').value=id;
  document.getElementById('edit-name').value=name;
  document.getElementById('edit-display_name').value=display_name;
  document.getElementById('edit-group').value=group;
  document.getElementById('edit-description').value=description;
  openModal('modal-edit');
}
document.getElementById('form-edit').addEventListener('submit',function(e){
  e.preventDefault();clearErrors('modal-edit');setLoading('btn-edit',true);
  const id=document.getElementById('edit-id').value;
  const body=new FormData(this);body.append('_method','PUT');
  fetch(`/admin/access/${id}`,{method:'POST',headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'},body})
  .then(async r=>({ok:r.ok,data:await r.json()}))
  .then(({ok,data})=>{if(ok){closeModal('modal-edit');showToast('success',data.message);setTimeout(()=>location.reload(),1200)}else{if(data.errors)showErrors('edit',data.errors);else showToast('error',data.message)}})
  .catch(()=>showToast('error','Gagal terhubung ke server.')).finally(()=>setLoading('btn-edit',false));
});

let delId=null;
function openDelete(id,name){delId=id;document.getElementById('delete-name').textContent=name;openModal('modal-delete')}
function confirmDelete(){
  if(!delId)return;setLoading('btn-delete',true);
  fetch(`/admin/access/${delId}`,{method:'POST',headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'},body:new URLSearchParams({_method:'DELETE'})})
  .then(async r=>({ok:r.ok,data:await r.json()}))
  .then(({ok,data})=>{closeModal('modal-delete');showToast(ok?'success':'error',data.message);if(ok)setTimeout(()=>location.reload(),1200)})
  .catch(()=>showToast('error','Gagal terhubung ke server.')).finally(()=>setLoading('btn-delete',false));
}
@if(session('success'))document.addEventListener('DOMContentLoaded',()=>showToast('success','{{ session("success") }}'));@endif
@if(session('error'))document.addEventListener('DOMContentLoaded',()=>showToast('error','{{ session("error") }}'));@endif
</script>
@endpush
