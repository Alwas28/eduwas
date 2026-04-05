@extends('layouts.admin')
@section('title','Jurusan')
@section('page-title','Jurusan')

@push('styles')
@include('admin.partials.datatable-styles')
@endpush

@section('content')
<div id="toast-container" class="toast-wrap"></div>

{{-- Header --}}
<div class="flex items-center justify-between animate-fadeUp">
  <div>
    <h2 class="font-display font-bold text-[20px]" style="color:var(--text)">Jurusan</h2>
    <p class="text-[13px] mt-0.5" style="color:var(--muted)">Kelola data jurusan beserta fakultasnya</p>
  </div>
  @canaccess('tambah.jurusan')
  <button onclick="openModal('modal-create')"
    class="flex items-center gap-2 px-4 py-2 rounded-xl text-[13px] font-semibold text-white a-grad shadow">
    <i class="fa-solid fa-plus text-[11px]"></i> Tambah Jurusan
  </button>
  @endcanaccess
</div>

{{-- Stat Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 animate-fadeUp d1">
  <div class="rounded-2xl p-5 border flex items-center gap-4" style="background:var(--surface);border-color:var(--border)">
    <div class="a-bg-lt a-text w-11 h-11 rounded-xl grid place-items-center text-lg flex-shrink-0"><i class="fa-solid fa-sitemap"></i></div>
    <div><div class="font-display text-[26px] font-bold" style="color:var(--text)">{{ $jurusan->count() }}</div><div class="text-[12px]" style="color:var(--muted)">Total Jurusan</div></div>
  </div>
  <div class="rounded-2xl p-5 border flex items-center gap-4" style="background:var(--surface);border-color:var(--border)">
    <div class="bg-emerald-500/15 text-emerald-400 w-11 h-11 rounded-xl grid place-items-center text-lg flex-shrink-0"><i class="fa-solid fa-circle-check"></i></div>
    <div><div class="font-display text-[26px] font-bold" style="color:var(--text)">{{ $jurusan->where('aktif', true)->count() }}</div><div class="text-[12px]" style="color:var(--muted)">Aktif</div></div>
  </div>
  <div class="rounded-2xl p-5 border flex items-center gap-4" style="background:var(--surface);border-color:var(--border)">
    <div class="bg-violet-500/15 text-violet-400 w-11 h-11 rounded-xl grid place-items-center text-lg flex-shrink-0"><i class="fa-solid fa-building-columns"></i></div>
    <div><div class="font-display text-[26px] font-bold" style="color:var(--text)">{{ $fakultas->count() }}</div><div class="text-[12px]" style="color:var(--muted)">Fakultas Tersedia</div></div>
  </div>
</div>

{{-- Table --}}
<div class="rounded-2xl overflow-hidden border animate-fadeUp d2" style="background:var(--surface);border-color:var(--border)">
  <div class="flex items-center justify-between px-5 py-4 border-b" style="border-color:var(--border)">
    <span class="font-display font-semibold text-[15px]" style="color:var(--text)">Daftar Jurusan</span>
    <span class="text-[12px] px-2.5 py-1 rounded-full a-bg-lt a-text font-semibold">{{ $jurusan->count() }} jurusan</span>
  </div>
  <div class="p-5">
    <table id="jurusan-table" class="w-full" style="width:100%">
      <thead>
        <tr>
          <th>#</th>
          <th>Kode</th>
          <th>Nama Jurusan</th>
          <th>Fakultas</th>
          <th>Status</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        @foreach($jurusan as $i => $jur)
        <tr>
          <td class="text-center" style="color:var(--muted);width:48px">{{ $i + 1 }}</td>
          <td>
            <code class="text-[12px] font-bold px-2 py-0.5 rounded-lg a-bg-lt a-text" style="font-family:monospace">{{ $jur->kode }}</code>
          </td>
          <td>
            <div>
              <div class="font-semibold text-[13.5px]" style="color:var(--text)">{{ $jur->nama }}</div>
              @if($jur->singkatan)
                <div class="text-[11.5px]" style="color:var(--muted)">{{ $jur->singkatan }}</div>
              @endif
            </div>
          </td>
          <td>
            @if($jur->fakultas)
              <div class="flex items-center gap-2">
                <div class="a-bg-lt a-text w-7 h-7 rounded-lg grid place-items-center text-[11px] font-bold flex-shrink-0">
                  {{ strtoupper(substr($jur->fakultas->kode, 0, 2)) }}
                </div>
                <div>
                  <div class="text-[12.5px] font-medium" style="color:var(--text)">{{ $jur->fakultas->nama }}</div>
                  <div class="text-[11px]" style="color:var(--muted)">{{ $jur->fakultas->kode }}</div>
                </div>
              </div>
            @else
              <span class="text-[12px]" style="color:var(--muted)">—</span>
            @endif
          </td>
          <td>
            @if($jur->aktif)
              <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-500/15 text-emerald-400">
                <i class="fa-solid fa-circle text-[7px]"></i> Aktif
              </span>
            @else
              <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-rose-500/15 text-rose-400">
                <i class="fa-solid fa-circle text-[7px]"></i> Nonaktif
              </span>
            @endif
          </td>
          <td>
            <div class="flex items-center gap-2">
              @canaccess('edit.jurusan')
              <button onclick="openEdit({{ $jur->id }},{{ $jur->fakultas_id }},{{ json_encode($jur->kode) }},{{ json_encode($jur->nama) }},{{ json_encode($jur->singkatan) }},{{ json_encode($jur->deskripsi) }},{{ $jur->aktif ? 'true' : 'false' }})"
                class="w-8 h-8 rounded-lg grid place-items-center text-[12px] border transition-colors"
                style="border-color:var(--border);color:var(--sub)"
                onmouseover="this.style.borderColor='var(--ac)';this.style.color='var(--ac)'"
                onmouseout="this.style.borderColor='var(--border)';this.style.color='var(--sub)'">
                <i class="fa-solid fa-pen"></i>
              </button>
              @endcanaccess
              @canaccess('hapus.jurusan')
              <button onclick="openDelete({{ $jur->id }},{{ json_encode($jur->nama) }})"
                class="w-8 h-8 rounded-lg grid place-items-center text-[12px] border transition-colors"
                style="border-color:var(--border);color:var(--sub)"
                onmouseover="this.style.borderColor='#f87171';this.style.color='#f87171'"
                onmouseout="this.style.borderColor='var(--border)';this.style.color='var(--sub)'">
                <i class="fa-solid fa-trash"></i>
              </button>
              @endcanaccess
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
        <div class="a-bg-lt a-text w-10 h-10 rounded-xl grid place-items-center"><i class="fa-solid fa-sitemap"></i></div>
        <h3 class="font-display font-bold text-[16px]" style="color:var(--text)">Tambah Jurusan</h3>
      </div>
      <button onclick="closeModal('modal-create')" class="w-8 h-8 rounded-lg grid place-items-center" style="color:var(--muted)" onmouseover="this.style.background='var(--surface2)'" onmouseout="this.style.background='transparent'"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <form id="form-create" class="px-6 py-5 space-y-4">

      {{-- Pilih Fakultas --}}
      <div>
        <label class="f-label">Fakultas <span class="text-red-400">*</span></label>
        @if($fakultas->isEmpty())
          <div class="rounded-lg p-3 text-[12.5px] border" style="background:var(--surface2);border-color:var(--border);color:var(--muted)">
            <i class="fa-solid fa-circle-exclamation mr-1.5 text-amber-400"></i>
            Belum ada fakultas aktif. <a href="{{ route('admin.fakultas.index') }}" class="a-text underline">Tambah fakultas dulu</a>.
          </div>
        @else
          <select name="fakultas_id" id="create-fakultas" class="f-input">
            <option value="">— Pilih Fakultas —</option>
            @foreach($fakultas as $fak)
              <option value="{{ $fak->id }}">{{ $fak->kode }} — {{ $fak->nama }}</option>
            @endforeach
          </select>
        @endif
        <p class="f-error hidden" id="err-create-fakultas_id"></p>
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="f-label">Kode <span class="text-red-400">*</span></label>
          <input type="text" name="kode" class="f-input" placeholder="cth: TI, SI, AK" style="text-transform:uppercase">
          <p class="f-error hidden" id="err-create-kode"></p>
        </div>
        <div>
          <label class="f-label">Singkatan</label>
          <input type="text" name="singkatan" class="f-input" placeholder="cth: Jur. TI">
          <p class="f-error hidden" id="err-create-singkatan"></p>
        </div>
      </div>
      <div>
        <label class="f-label">Nama Jurusan <span class="text-red-400">*</span></label>
        <input type="text" name="nama" class="f-input" placeholder="cth: Teknik Informatika">
        <p class="f-error hidden" id="err-create-nama"></p>
      </div>
      <div>
        <label class="f-label">Deskripsi</label>
        <textarea name="deskripsi" class="f-input" rows="2" placeholder="Deskripsi singkat (opsional)"></textarea>
        <p class="f-error hidden" id="err-create-deskripsi"></p>
      </div>
      <div class="flex items-center gap-3">
        <input type="checkbox" name="aktif" id="create-aktif" value="1" checked class="w-4 h-4 rounded" style="accent-color:var(--ac)">
        <label for="create-aktif" class="text-[13px] font-medium" style="color:var(--text);cursor:pointer">Aktif</label>
      </div>
      <div class="flex gap-3 pt-2">
        <button type="button" onclick="closeModal('modal-create')" class="flex-1 px-4 py-2 rounded-xl border text-[13px] font-semibold" style="border-color:var(--border);color:var(--sub)" onmouseover="this.style.background='var(--surface2)'" onmouseout="this.style.background='transparent'">Batal</button>
        <button type="submit" id="btn-create" class="flex-1 px-4 py-2 rounded-xl text-[13px] font-semibold text-white a-grad shadow"><i class="fa-solid fa-floppy-disk mr-1.5 text-[11px]"></i>Simpan</button>
      </div>
    </form>
  </div>
</div>

{{-- MODAL EDIT --}}
<div id="modal-edit" class="modal-backdrop">
  <div class="modal-box">
    <div class="flex items-center justify-between px-6 py-5 border-b" style="border-color:var(--border)">
      <div class="flex items-center gap-3">
        <div class="a-bg-lt a-text w-10 h-10 rounded-xl grid place-items-center"><i class="fa-solid fa-pen"></i></div>
        <h3 class="font-display font-bold text-[16px]" style="color:var(--text)">Edit Jurusan</h3>
      </div>
      <button onclick="closeModal('modal-edit')" class="w-8 h-8 rounded-lg grid place-items-center" style="color:var(--muted)" onmouseover="this.style.background='var(--surface2)'" onmouseout="this.style.background='transparent'"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <form id="form-edit" class="px-6 py-5 space-y-4">
      <input type="hidden" id="edit-id">

      {{-- Pilih Fakultas --}}
      <div>
        <label class="f-label">Fakultas <span class="text-red-400">*</span></label>
        <select name="fakultas_id" id="edit-fakultas" class="f-input">
          <option value="">— Pilih Fakultas —</option>
          @foreach($fakultas as $fak)
            <option value="{{ $fak->id }}">{{ $fak->kode }} — {{ $fak->nama }}</option>
          @endforeach
        </select>
        <p class="f-error hidden" id="err-edit-fakultas_id"></p>
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="f-label">Kode <span class="text-red-400">*</span></label>
          <input type="text" name="kode" id="edit-kode" class="f-input" style="text-transform:uppercase">
          <p class="f-error hidden" id="err-edit-kode"></p>
        </div>
        <div>
          <label class="f-label">Singkatan</label>
          <input type="text" name="singkatan" id="edit-singkatan" class="f-input">
          <p class="f-error hidden" id="err-edit-singkatan"></p>
        </div>
      </div>
      <div>
        <label class="f-label">Nama Jurusan <span class="text-red-400">*</span></label>
        <input type="text" name="nama" id="edit-nama" class="f-input">
        <p class="f-error hidden" id="err-edit-nama"></p>
      </div>
      <div>
        <label class="f-label">Deskripsi</label>
        <textarea name="deskripsi" id="edit-deskripsi" class="f-input" rows="2"></textarea>
        <p class="f-error hidden" id="err-edit-deskripsi"></p>
      </div>
      <div class="flex items-center gap-3">
        <input type="checkbox" name="aktif" id="edit-aktif" value="1" class="w-4 h-4 rounded" style="accent-color:var(--ac)">
        <label for="edit-aktif" class="text-[13px] font-medium" style="color:var(--text);cursor:pointer">Aktif</label>
      </div>
      <div class="flex gap-3 pt-2">
        <button type="button" onclick="closeModal('modal-edit')" class="flex-1 px-4 py-2 rounded-xl border text-[13px] font-semibold" style="border-color:var(--border);color:var(--sub)" onmouseover="this.style.background='var(--surface2)'" onmouseout="this.style.background='transparent'">Batal</button>
        <button type="submit" id="btn-edit" class="flex-1 px-4 py-2 rounded-xl text-[13px] font-semibold text-white a-grad shadow"><i class="fa-solid fa-floppy-disk mr-1.5 text-[11px]"></i>Simpan</button>
      </div>
    </form>
  </div>
</div>

{{-- MODAL HAPUS --}}
<div id="modal-delete" class="modal-backdrop">
  <div class="modal-box modal-sm">
    <div class="p-6 text-center">
      <div class="bg-rose-500/15 text-rose-400 w-14 h-14 rounded-2xl grid place-items-center text-2xl mx-auto mb-4"><i class="fa-solid fa-trash-can"></i></div>
      <h3 class="font-display font-bold text-[16px] mb-1" style="color:var(--text)">Hapus Jurusan?</h3>
      <p class="text-[12.5px]" style="color:var(--muted)">Jurusan <strong id="delete-name" style="color:var(--text)"></strong> akan dihapus permanen.</p>
    </div>
    <div class="flex gap-3 px-6 pb-6">
      <button onclick="closeModal('modal-delete')" class="flex-1 px-4 py-2 rounded-xl border text-[13px] font-semibold" style="border-color:var(--border);color:var(--sub)" onmouseover="this.style.background='var(--surface2)'" onmouseout="this.style.background='transparent'">Batal</button>
      <button id="btn-delete" onclick="doDelete()" class="flex-1 px-4 py-2 rounded-xl text-[13px] font-semibold text-white" style="background:#f87171"><i class="fa-solid fa-trash-can mr-1.5 text-[11px]"></i>Hapus</button>
    </div>
  </div>
</div>
@endsection

@push('scripts')
@include('admin.partials.datatable-scripts')
<script>
$(()=>$('#jurusan-table').DataTable({
  language:{...DT_LANG, searchPlaceholder:'Cari jurusan...'},
  columnDefs:[{orderable:false,targets:[0,5]},{className:'text-center',targets:[0]}],
  pageLength:10, dom:DT_DOM
}));

// CREATE
document.getElementById('form-create').addEventListener('submit', function(e){
  e.preventDefault();
  setLoading('btn-create', true);
  const body = new URLSearchParams();
  body.append('fakultas_id', document.getElementById('create-fakultas')?.value || '');
  body.append('kode', this.kode.value);
  body.append('nama', this.nama.value);
  body.append('singkatan', this.singkatan.value || '');
  body.append('deskripsi', this.deskripsi.value || '');
  body.append('aktif', document.getElementById('create-aktif').checked ? 1 : 0);

  fetch('/admin/jurusan', {
    method:'POST',
    headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'},
    body,
  })
  .then(async r=>({ok:r.ok, status:r.status, data:await r.json()}))
  .then(({ok, status, data})=>{
    if(!ok && status===422){ showErrors('create', data.errors); return; }
    closeModal('modal-create');
    showToast(ok?'success':'error', data.message);
    if(ok) setTimeout(()=>location.reload(), 1200);
  })
  .catch(()=>showToast('error','Gagal terhubung ke server.'))
  .finally(()=>setLoading('btn-create', false));
});

// EDIT
let deleteId = null;
function openEdit(id, fakultasId, kode, nama, singkatan, deskripsi, aktif){
  document.getElementById('edit-id').value = id;
  document.getElementById('edit-fakultas').value = fakultasId;
  document.getElementById('edit-kode').value = kode;
  document.getElementById('edit-nama').value = nama;
  document.getElementById('edit-singkatan').value = singkatan || '';
  document.getElementById('edit-deskripsi').value = deskripsi || '';
  document.getElementById('edit-aktif').checked = aktif;
  openModal('modal-edit');
}
document.getElementById('form-edit').addEventListener('submit', function(e){
  e.preventDefault();
  const id = document.getElementById('edit-id').value;
  setLoading('btn-edit', true);
  const body = new URLSearchParams({_method:'PUT'});
  body.append('fakultas_id', document.getElementById('edit-fakultas').value);
  body.append('kode', document.getElementById('edit-kode').value);
  body.append('nama', document.getElementById('edit-nama').value);
  body.append('singkatan', document.getElementById('edit-singkatan').value);
  body.append('deskripsi', document.getElementById('edit-deskripsi').value);
  body.append('aktif', document.getElementById('edit-aktif').checked ? 1 : 0);

  fetch(`/admin/jurusan/${id}`, {
    method:'POST',
    headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'},
    body,
  })
  .then(async r=>({ok:r.ok, status:r.status, data:await r.json()}))
  .then(({ok, status, data})=>{
    if(!ok && status===422){ showErrors('edit', data.errors); return; }
    closeModal('modal-edit');
    showToast(ok?'success':'error', data.message);
    if(ok) setTimeout(()=>location.reload(), 1200);
  })
  .catch(()=>showToast('error','Gagal terhubung ke server.'))
  .finally(()=>setLoading('btn-edit', false));
});

// DELETE
function openDelete(id, nama){
  deleteId = id;
  document.getElementById('delete-name').textContent = nama;
  openModal('modal-delete');
}
function doDelete(){
  setLoading('btn-delete', true);
  fetch(`/admin/jurusan/${deleteId}`, {
    method:'POST',
    headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'},
    body: new URLSearchParams({_method:'DELETE'}),
  })
  .then(async r=>({ok:r.ok, data:await r.json()}))
  .then(({ok, data})=>{
    closeModal('modal-delete');
    showToast(ok?'success':'error', data.message);
    if(ok) setTimeout(()=>location.reload(), 1200);
  })
  .catch(()=>showToast('error','Gagal terhubung ke server.'))
  .finally(()=>setLoading('btn-delete', false));
}

@if(session('success'))document.addEventListener('DOMContentLoaded',()=>showToast('success','{{ session("success") }}'));@endif
@if(session('error'))document.addEventListener('DOMContentLoaded',()=>showToast('error','{{ session("error") }}'));@endif
</script>
@endpush
