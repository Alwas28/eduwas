@extends('layouts.admin')
@section('title','Periode Akademik')
@section('page-title','Periode Akademik')

@push('styles')
@include('admin.partials.datatable-styles')
@endpush

@section('content')
<div id="toast-container" class="toast-wrap"></div>

{{-- Header --}}
<div class="flex items-center justify-between animate-fadeUp">
  <div>
    <h2 class="font-display font-bold text-[20px]" style="color:var(--text)">Periode Akademik</h2>
    <p class="text-[13px] mt-0.5" style="color:var(--muted)">Kelola periode dan tahun ajaran akademik</p>
  </div>
  @canaccess('tambah.periode-akademik')
  <button onclick="openModal('modal-create')"
    class="flex items-center gap-2 px-4 py-2 rounded-xl text-[13px] font-semibold text-white a-grad shadow">
    <i class="fa-solid fa-plus text-[11px]"></i> Tambah Periode
  </button>
  @endcanaccess
</div>

{{-- Stat Cards --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 animate-fadeUp d1">
  <div class="rounded-2xl p-5 border flex items-center gap-4" style="background:var(--surface);border-color:var(--border)">
    <div class="a-bg-lt a-text w-11 h-11 rounded-xl grid place-items-center text-lg flex-shrink-0"><i class="fa-solid fa-calendar-days"></i></div>
    <div>
      <div class="font-display text-[26px] font-bold" style="color:var(--text)">{{ $stats['total'] }}</div>
      <div class="text-[12px]" style="color:var(--muted)">Total Periode</div>
    </div>
  </div>
  <div class="rounded-2xl p-5 border flex items-center gap-4" style="background:var(--surface);border-color:var(--border)">
    <div class="bg-emerald-500/15 text-emerald-400 w-11 h-11 rounded-xl grid place-items-center text-lg flex-shrink-0"><i class="fa-solid fa-circle-check"></i></div>
    <div>
      <div class="font-display text-[26px] font-bold" style="color:var(--text)">{{ $stats['aktif'] }}</div>
      <div class="text-[12px]" style="color:var(--muted)">Aktif</div>
    </div>
  </div>
  <div class="rounded-2xl p-5 border flex items-center gap-4" style="background:var(--surface);border-color:var(--border)">
    <div class="bg-amber-500/15 text-amber-400 w-11 h-11 rounded-xl grid place-items-center text-lg flex-shrink-0"><i class="fa-solid fa-clock"></i></div>
    <div>
      <div class="font-display text-[26px] font-bold" style="color:var(--text)">{{ $stats['tidak_aktif'] }}</div>
      <div class="text-[12px]" style="color:var(--muted)">Tidak Aktif</div>
    </div>
  </div>
  <div class="rounded-2xl p-5 border flex items-center gap-4" style="background:var(--surface);border-color:var(--border)">
    <div class="bg-slate-500/15 text-slate-400 w-11 h-11 rounded-xl grid place-items-center text-lg flex-shrink-0"><i class="fa-solid fa-flag-checkered"></i></div>
    <div>
      <div class="font-display text-[26px] font-bold" style="color:var(--text)">{{ $stats['selesai'] }}</div>
      <div class="text-[12px]" style="color:var(--muted)">Selesai</div>
    </div>
  </div>
</div>

{{-- Table --}}
<div class="rounded-2xl overflow-hidden border animate-fadeUp d2" style="background:var(--surface);border-color:var(--border)">
  <div class="flex items-center justify-between px-5 py-4 border-b" style="border-color:var(--border)">
    <span class="font-display font-semibold text-[15px]" style="color:var(--text)">Daftar Periode Akademik</span>
    <span class="text-[12px] px-2.5 py-1 rounded-full a-bg-lt a-text font-semibold">{{ $stats['total'] }} periode</span>
  </div>
  <div class="p-5">
    <table id="periode-table" class="w-full" style="width:100%">
      <thead>
        <tr>
          <th>#</th>
          <th>Kode</th>
          <th>Nama Periode</th>
          <th>Tahun Ajaran</th>
          <th>Semester</th>
          <th>Tanggal</th>
          <th>Status</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        @foreach($periodes as $i => $periode)
        <tr>
          <td class="text-center" style="color:var(--muted);width:48px">{{ $i + 1 }}</td>
          <td>
            <code class="text-[12px] font-bold px-2 py-0.5 rounded-lg a-bg-lt a-text" style="font-family:monospace">{{ $periode->kode }}</code>
          </td>
          <td>
            <div class="font-semibold text-[13.5px]" style="color:var(--text)">{{ $periode->nama }}</div>
            @if($periode->deskripsi)
              <div class="text-[11.5px] mt-0.5 truncate max-w-[200px]" style="color:var(--muted)">{{ $periode->deskripsi }}</div>
            @endif
          </td>
          <td>
            <span class="text-[13px] font-medium" style="color:var(--text)">{{ $periode->tahun_ajaran }}</span>
          </td>
          <td>
            @if($periode->semester === 'Ganjil')
              <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-violet-500/15 text-violet-400">
                <i class="fa-solid fa-1 text-[9px]"></i> Ganjil
              </span>
            @else
              <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-blue-500/15 text-blue-400">
                <i class="fa-solid fa-2 text-[9px]"></i> Genap
              </span>
            @endif
          </td>
          <td>
            <div class="text-[12px]" style="color:var(--text)">
              <span>{{ $periode->tanggal_mulai->format('d M Y') }}</span>
              <span style="color:var(--muted)"> – </span>
              <span>{{ $periode->tanggal_selesai->format('d M Y') }}</span>
            </div>
          </td>
          <td>
            @if($periode->status === 'Aktif')
              <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-500/15 text-emerald-400">
                <i class="fa-solid fa-circle text-[7px]"></i> Aktif
              </span>
            @elseif($periode->status === 'Selesai')
              <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-slate-500/15 text-slate-400">
                <i class="fa-solid fa-circle text-[7px]"></i> Selesai
              </span>
            @else
              <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-amber-500/15 text-amber-400">
                <i class="fa-solid fa-circle text-[7px]"></i> Tidak Aktif
              </span>
            @endif
          </td>
          <td>
            <div class="flex items-center gap-2">
              @canaccess('edit.periode-akademik')
              <button onclick="openEdit({{ $periode->id }},{{ json_encode($periode->kode) }},{{ json_encode($periode->nama) }},{{ json_encode($periode->tahun_ajaran) }},{{ json_encode($periode->semester) }},{{ json_encode($periode->tanggal_mulai->format('Y-m-d')) }},{{ json_encode($periode->tanggal_selesai->format('Y-m-d')) }},{{ json_encode($periode->status) }},{{ json_encode($periode->deskripsi) }})"
                class="w-8 h-8 rounded-lg grid place-items-center text-[12px] border transition-colors"
                style="border-color:var(--border);color:var(--sub)"
                onmouseover="this.style.borderColor='var(--ac)';this.style.color='var(--ac)'"
                onmouseout="this.style.borderColor='var(--border)';this.style.color='var(--sub)'">
                <i class="fa-solid fa-pen"></i>
              </button>
              @endcanaccess
              @canaccess('hapus.periode-akademik')
              <button onclick="openDelete({{ $periode->id }},{{ json_encode($periode->nama) }})"
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
  <div class="modal-box" style="max-width:520px">
    <div class="flex items-center justify-between px-6 py-5 border-b" style="border-color:var(--border)">
      <div class="flex items-center gap-3">
        <div class="a-bg-lt a-text w-10 h-10 rounded-xl grid place-items-center"><i class="fa-solid fa-calendar-plus"></i></div>
        <h3 class="font-display font-bold text-[16px]" style="color:var(--text)">Tambah Periode Akademik</h3>
      </div>
      <button onclick="closeModal('modal-create')" class="w-8 h-8 rounded-lg grid place-items-center" style="color:var(--muted)"
        onmouseover="this.style.background='var(--surface2)'" onmouseout="this.style.background='transparent'">
        <i class="fa-solid fa-xmark"></i>
      </button>
    </div>
    <form id="form-create" class="px-6 py-5 space-y-4">
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="f-label">Kode <span class="text-red-400">*</span></label>
          <input type="text" name="kode" class="f-input" placeholder="cth: 2024-1, GNJ24" style="text-transform:uppercase">
          <p class="f-error hidden" id="err-create-kode"></p>
        </div>
        <div>
          <label class="f-label">Tahun Ajaran <span class="text-red-400">*</span></label>
          <input type="text" name="tahun_ajaran" class="f-input" placeholder="cth: 2024/2025">
          <p class="f-error hidden" id="err-create-tahun_ajaran"></p>
        </div>
      </div>
      <div>
        <label class="f-label">Nama Periode <span class="text-red-400">*</span></label>
        <input type="text" name="nama" class="f-input" placeholder="cth: Semester Ganjil 2024/2025">
        <p class="f-error hidden" id="err-create-nama"></p>
      </div>
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="f-label">Semester <span class="text-red-400">*</span></label>
          <select name="semester" class="f-input">
            <option value="">-- Pilih Semester --</option>
            <option value="Ganjil">Ganjil</option>
            <option value="Genap">Genap</option>
          </select>
          <p class="f-error hidden" id="err-create-semester"></p>
        </div>
        <div>
          <label class="f-label">Status <span class="text-red-400">*</span></label>
          <select name="status" class="f-input">
            <option value="Tidak Aktif" selected>Tidak Aktif</option>
            <option value="Aktif">Aktif</option>
            <option value="Selesai">Selesai</option>
          </select>
          <p class="f-error hidden" id="err-create-status"></p>
        </div>
      </div>
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="f-label">Tanggal Mulai <span class="text-red-400">*</span></label>
          <input type="date" name="tanggal_mulai" class="f-input">
          <p class="f-error hidden" id="err-create-tanggal_mulai"></p>
        </div>
        <div>
          <label class="f-label">Tanggal Selesai <span class="text-red-400">*</span></label>
          <input type="date" name="tanggal_selesai" class="f-input">
          <p class="f-error hidden" id="err-create-tanggal_selesai"></p>
        </div>
      </div>
      <div>
        <label class="f-label">Deskripsi</label>
        <textarea name="deskripsi" class="f-input" rows="2" placeholder="Deskripsi singkat (opsional)"></textarea>
        <p class="f-error hidden" id="err-create-deskripsi"></p>
      </div>
      <div class="flex gap-3 pt-2">
        <button type="button" onclick="closeModal('modal-create')" class="flex-1 px-4 py-2 rounded-xl border text-[13px] font-semibold"
          style="border-color:var(--border);color:var(--sub)"
          onmouseover="this.style.background='var(--surface2)'" onmouseout="this.style.background='transparent'">Batal</button>
        <button type="submit" id="btn-create" class="flex-1 px-4 py-2 rounded-xl text-[13px] font-semibold text-white a-grad shadow">
          <i class="fa-solid fa-floppy-disk mr-1.5 text-[11px]"></i>Simpan
        </button>
      </div>
    </form>
  </div>
</div>

{{-- MODAL EDIT --}}
<div id="modal-edit" class="modal-backdrop">
  <div class="modal-box" style="max-width:520px">
    <div class="flex items-center justify-between px-6 py-5 border-b" style="border-color:var(--border)">
      <div class="flex items-center gap-3">
        <div class="a-bg-lt a-text w-10 h-10 rounded-xl grid place-items-center"><i class="fa-solid fa-pen"></i></div>
        <h3 class="font-display font-bold text-[16px]" style="color:var(--text)">Edit Periode Akademik</h3>
      </div>
      <button onclick="closeModal('modal-edit')" class="w-8 h-8 rounded-lg grid place-items-center" style="color:var(--muted)"
        onmouseover="this.style.background='var(--surface2)'" onmouseout="this.style.background='transparent'">
        <i class="fa-solid fa-xmark"></i>
      </button>
    </div>
    <form id="form-edit" class="px-6 py-5 space-y-4">
      <input type="hidden" id="edit-id">
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="f-label">Kode <span class="text-red-400">*</span></label>
          <input type="text" name="kode" id="edit-kode" class="f-input" style="text-transform:uppercase">
          <p class="f-error hidden" id="err-edit-kode"></p>
        </div>
        <div>
          <label class="f-label">Tahun Ajaran <span class="text-red-400">*</span></label>
          <input type="text" name="tahun_ajaran" id="edit-tahun_ajaran" class="f-input">
          <p class="f-error hidden" id="err-edit-tahun_ajaran"></p>
        </div>
      </div>
      <div>
        <label class="f-label">Nama Periode <span class="text-red-400">*</span></label>
        <input type="text" name="nama" id="edit-nama" class="f-input">
        <p class="f-error hidden" id="err-edit-nama"></p>
      </div>
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="f-label">Semester <span class="text-red-400">*</span></label>
          <select name="semester" id="edit-semester" class="f-input">
            <option value="Ganjil">Ganjil</option>
            <option value="Genap">Genap</option>
          </select>
          <p class="f-error hidden" id="err-edit-semester"></p>
        </div>
        <div>
          <label class="f-label">Status <span class="text-red-400">*</span></label>
          <select name="status" id="edit-status" class="f-input">
            <option value="Tidak Aktif">Tidak Aktif</option>
            <option value="Aktif">Aktif</option>
            <option value="Selesai">Selesai</option>
          </select>
          <p class="f-error hidden" id="err-edit-status"></p>
        </div>
      </div>
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="f-label">Tanggal Mulai <span class="text-red-400">*</span></label>
          <input type="date" name="tanggal_mulai" id="edit-tanggal_mulai" class="f-input">
          <p class="f-error hidden" id="err-edit-tanggal_mulai"></p>
        </div>
        <div>
          <label class="f-label">Tanggal Selesai <span class="text-red-400">*</span></label>
          <input type="date" name="tanggal_selesai" id="edit-tanggal_selesai" class="f-input">
          <p class="f-error hidden" id="err-edit-tanggal_selesai"></p>
        </div>
      </div>
      <div>
        <label class="f-label">Deskripsi</label>
        <textarea name="deskripsi" id="edit-deskripsi" class="f-input" rows="2"></textarea>
        <p class="f-error hidden" id="err-edit-deskripsi"></p>
      </div>
      <div class="flex gap-3 pt-2">
        <button type="button" onclick="closeModal('modal-edit')" class="flex-1 px-4 py-2 rounded-xl border text-[13px] font-semibold"
          style="border-color:var(--border);color:var(--sub)"
          onmouseover="this.style.background='var(--surface2)'" onmouseout="this.style.background='transparent'">Batal</button>
        <button type="submit" id="btn-edit" class="flex-1 px-4 py-2 rounded-xl text-[13px] font-semibold text-white a-grad shadow">
          <i class="fa-solid fa-floppy-disk mr-1.5 text-[11px]"></i>Simpan
        </button>
      </div>
    </form>
  </div>
</div>

{{-- MODAL HAPUS --}}
<div id="modal-delete" class="modal-backdrop">
  <div class="modal-box modal-sm">
    <div class="p-6 text-center">
      <div class="bg-rose-500/15 text-rose-400 w-14 h-14 rounded-2xl grid place-items-center text-2xl mx-auto mb-4"><i class="fa-solid fa-trash-can"></i></div>
      <h3 class="font-display font-bold text-[16px] mb-1" style="color:var(--text)">Hapus Periode?</h3>
      <p class="text-[12.5px]" style="color:var(--muted)">Periode <strong id="delete-name" style="color:var(--text)"></strong> akan dihapus permanen.</p>
    </div>
    <div class="flex gap-3 px-6 pb-6">
      <button onclick="closeModal('modal-delete')" class="flex-1 px-4 py-2 rounded-xl border text-[13px] font-semibold"
        style="border-color:var(--border);color:var(--sub)"
        onmouseover="this.style.background='var(--surface2)'" onmouseout="this.style.background='transparent'">Batal</button>
      <button id="btn-delete" onclick="doDelete()" class="flex-1 px-4 py-2 rounded-xl text-[13px] font-semibold text-white" style="background:#f87171">
        <i class="fa-solid fa-trash-can mr-1.5 text-[11px]"></i>Hapus
      </button>
    </div>
  </div>
</div>
@endsection

@push('scripts')
@include('admin.partials.datatable-scripts')
<script>
$(()=>$('#periode-table').DataTable({
  language:{...DT_LANG,searchPlaceholder:'Cari periode...'},
  columnDefs:[{orderable:false,targets:[0,7]},{className:'text-center',targets:[0]}],
  pageLength:10, dom:DT_DOM,
  order:[[0,'asc']]
}));

// CREATE
document.getElementById('form-create').addEventListener('submit', function(e){
  e.preventDefault();
  clearErrors('create');
  setLoading('btn-create', true);
  const form = new FormData(this);
  const body = new URLSearchParams();
  body.append('kode', form.get('kode'));
  body.append('nama', form.get('nama'));
  body.append('tahun_ajaran', form.get('tahun_ajaran'));
  body.append('semester', form.get('semester'));
  body.append('tanggal_mulai', form.get('tanggal_mulai'));
  body.append('tanggal_selesai', form.get('tanggal_selesai'));
  body.append('status', form.get('status'));
  body.append('deskripsi', form.get('deskripsi') || '');

  fetch('/admin/periode-akademik', {
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
function openEdit(id, kode, nama, tahun_ajaran, semester, tanggal_mulai, tanggal_selesai, status, deskripsi){
  document.getElementById('edit-id').value = id;
  document.getElementById('edit-kode').value = kode;
  document.getElementById('edit-nama').value = nama;
  document.getElementById('edit-tahun_ajaran').value = tahun_ajaran;
  document.getElementById('edit-semester').value = semester;
  document.getElementById('edit-tanggal_mulai').value = tanggal_mulai;
  document.getElementById('edit-tanggal_selesai').value = tanggal_selesai;
  document.getElementById('edit-status').value = status;
  document.getElementById('edit-deskripsi').value = deskripsi || '';
  clearErrors('edit');
  openModal('modal-edit');
}

document.getElementById('form-edit').addEventListener('submit', function(e){
  e.preventDefault();
  clearErrors('edit');
  const id = document.getElementById('edit-id').value;
  setLoading('btn-edit', true);
  const body = new URLSearchParams({_method:'PUT'});
  body.append('kode', document.getElementById('edit-kode').value);
  body.append('nama', document.getElementById('edit-nama').value);
  body.append('tahun_ajaran', document.getElementById('edit-tahun_ajaran').value);
  body.append('semester', document.getElementById('edit-semester').value);
  body.append('tanggal_mulai', document.getElementById('edit-tanggal_mulai').value);
  body.append('tanggal_selesai', document.getElementById('edit-tanggal_selesai').value);
  body.append('status', document.getElementById('edit-status').value);
  body.append('deskripsi', document.getElementById('edit-deskripsi').value);

  fetch(`/admin/periode-akademik/${id}`, {
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
  fetch(`/admin/periode-akademik/${deleteId}`, {
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

function clearErrors(prefix){
  ['kode','nama','tahun_ajaran','semester','tanggal_mulai','tanggal_selesai','status','deskripsi'].forEach(f=>{
    const el = document.getElementById(`err-${prefix}-${f}`);
    if(el){ el.textContent=''; el.classList.add('hidden'); }
  });
}

@if(session('success'))document.addEventListener('DOMContentLoaded',()=>showToast('success','{{ session("success") }}'));@endif
@if(session('error'))document.addEventListener('DOMContentLoaded',()=>showToast('error','{{ session("error") }}'));@endif
</script>
@endpush
