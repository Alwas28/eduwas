@extends('layouts.admin')
@section('title','Mata Kuliah')
@section('page-title','Mata Kuliah')

@push('styles')
@include('admin.partials.datatable-styles')
@endpush

@section('content')
<div id="toast-container" class="toast-wrap"></div>

{{-- Header --}}
<div class="flex items-center justify-between animate-fadeUp">
  <div>
    <h2 class="font-display font-bold text-[20px]" style="color:var(--text)">Mata Kuliah</h2>
    <p class="text-[13px] mt-0.5" style="color:var(--muted)">Master data mata kuliah per jurusan</p>
  </div>
  @canaccess('tambah.matakuliah')
  <button onclick="openModal('modal-create')"
    class="flex items-center gap-2 px-4 py-2 rounded-xl text-[13px] font-semibold text-white a-grad shadow">
    <i class="fa-solid fa-plus text-[11px]"></i> Tambah Mata Kuliah
  </button>
  @endcanaccess
</div>

{{-- Stat Cards --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 animate-fadeUp d1">
  <div class="rounded-2xl p-5 border flex items-center gap-4" style="background:var(--surface);border-color:var(--border)">
    <div class="a-bg-lt a-text w-11 h-11 rounded-xl grid place-items-center text-lg flex-shrink-0"><i class="fa-solid fa-book"></i></div>
    <div>
      <div class="font-display text-[26px] font-bold" style="color:var(--text)">{{ $stats['total'] }}</div>
      <div class="text-[12px]" style="color:var(--muted)">Total MK</div>
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
    <div class="bg-violet-500/15 text-violet-400 w-11 h-11 rounded-xl grid place-items-center text-lg flex-shrink-0"><i class="fa-solid fa-book-open"></i></div>
    <div>
      <div class="font-display text-[26px] font-bold" style="color:var(--text)">{{ $stats['wajib'] }}</div>
      <div class="text-[12px]" style="color:var(--muted)">MK Wajib</div>
    </div>
  </div>
  <div class="rounded-2xl p-5 border flex items-center gap-4" style="background:var(--surface);border-color:var(--border)">
    <div class="bg-amber-500/15 text-amber-400 w-11 h-11 rounded-xl grid place-items-center text-lg flex-shrink-0"><i class="fa-solid fa-book-bookmark"></i></div>
    <div>
      <div class="font-display text-[26px] font-bold" style="color:var(--text)">{{ $stats['pilihan'] }}</div>
      <div class="text-[12px]" style="color:var(--muted)">MK Pilihan</div>
    </div>
  </div>
</div>

{{-- Table --}}
<div class="rounded-2xl overflow-hidden border animate-fadeUp d2" style="background:var(--surface);border-color:var(--border)">
  <div class="flex items-center justify-between px-5 py-4 border-b" style="border-color:var(--border)">
    <span class="font-display font-semibold text-[15px]" style="color:var(--text)">Daftar Mata Kuliah</span>
    <span class="text-[12px] px-2.5 py-1 rounded-full a-bg-lt a-text font-semibold">{{ $stats['total'] }} mata kuliah</span>
  </div>
  <div class="p-5">
    <table id="mk-table" class="w-full" style="width:100%">
      <thead>
        <tr>
          <th>#</th>
          <th>Kode</th>
          <th>Nama Mata Kuliah</th>
          <th>Jurusan</th>
          <th>SKS</th>
          <th>Smt</th>
          <th>Jenis</th>
          <th>Status</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        @foreach($mataKuliah as $i => $mk)
        <tr>
          <td class="text-center" style="color:var(--muted);width:48px">{{ $i + 1 }}</td>
          <td>
            <code class="text-[12px] font-bold px-2 py-0.5 rounded-lg a-bg-lt a-text" style="font-family:monospace">{{ $mk->kode }}</code>
          </td>
          <td>
            <div class="font-semibold text-[13.5px]" style="color:var(--text)">{{ $mk->nama }}</div>
            @if($mk->deskripsi)
              <div class="text-[11.5px] mt-0.5 truncate max-w-[200px]" style="color:var(--muted)">{{ $mk->deskripsi }}</div>
            @endif
          </td>
          <td>
            @if($mk->jurusan)
              <div>
                <div class="text-[13px] font-medium" style="color:var(--text)">{{ $mk->jurusan->nama }}</div>
                @if($mk->jurusan->fakultas)
                  <div class="text-[11px]" style="color:var(--muted)">{{ $mk->jurusan->fakultas->nama }}</div>
                @endif
              </div>
            @else
              <span style="color:var(--muted)">—</span>
            @endif
          </td>
          <td class="text-center">
            <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg font-bold text-[13px] a-bg-lt a-text">{{ $mk->sks }}</span>
          </td>
          <td class="text-center">
            @if($mk->semester)
              <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-[12px] font-semibold border" style="border-color:var(--border);color:var(--sub)">{{ $mk->semester }}</span>
            @else
              <span style="color:var(--muted)" class="text-[12px]">—</span>
            @endif
          </td>
          <td>
            @if($mk->jenis === 'Wajib')
              <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-violet-500/15 text-violet-400">
                <i class="fa-solid fa-circle text-[7px]"></i> Wajib
              </span>
            @else
              <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-amber-500/15 text-amber-400">
                <i class="fa-solid fa-circle text-[7px]"></i> Pilihan
              </span>
            @endif
          </td>
          <td>
            @if($mk->aktif)
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
              @canaccess('edit.matakuliah')
              <button onclick="openEdit({{ $mk->id }},{{ $mk->jurusan_id }},{{ json_encode($mk->kode) }},{{ json_encode($mk->nama) }},{{ $mk->sks }},{{ $mk->semester ?? 'null' }},{{ json_encode($mk->jenis) }},{{ json_encode($mk->deskripsi) }},{{ $mk->aktif ? 'true' : 'false' }})"
                class="w-8 h-8 rounded-lg grid place-items-center text-[12px] border transition-colors"
                style="border-color:var(--border);color:var(--sub)"
                onmouseover="this.style.borderColor='var(--ac)';this.style.color='var(--ac)'"
                onmouseout="this.style.borderColor='var(--border)';this.style.color='var(--sub)'">
                <i class="fa-solid fa-pen"></i>
              </button>
              @endcanaccess
              @canaccess('hapus.matakuliah')
              <button onclick="openDelete({{ $mk->id }},{{ json_encode($mk->nama) }})"
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
  <div class="modal-box" style="max-width:540px">
    <div class="flex items-center justify-between px-6 py-5 border-b" style="border-color:var(--border)">
      <div class="flex items-center gap-3">
        <div class="a-bg-lt a-text w-10 h-10 rounded-xl grid place-items-center"><i class="fa-solid fa-book-medical"></i></div>
        <h3 class="font-display font-bold text-[16px]" style="color:var(--text)">Tambah Mata Kuliah</h3>
      </div>
      <button onclick="closeModal('modal-create')" class="w-8 h-8 rounded-lg grid place-items-center" style="color:var(--muted)"
        onmouseover="this.style.background='var(--surface2)'" onmouseout="this.style.background='transparent'">
        <i class="fa-solid fa-xmark"></i>
      </button>
    </div>
    <form id="form-create" class="px-6 py-5 space-y-4">

      {{-- Jurusan --}}
      <div>
        <label class="f-label">Jurusan <span class="text-red-400">*</span></label>
        @if($jurusan->isEmpty())
          <div class="rounded-lg p-3 text-[12.5px] border" style="background:var(--surface2);border-color:var(--border);color:var(--muted)">
            <i class="fa-solid fa-circle-exclamation mr-1.5 text-amber-400"></i>
            Belum ada jurusan aktif. <a href="{{ route('admin.jurusan.index') }}" class="a-text underline">Tambah jurusan dulu</a>.
          </div>
        @else
          <select name="jurusan_id" id="create-jurusan" class="f-input">
            <option value="">— Pilih Jurusan —</option>
            @foreach($fakultas as $fak)
              @php $jursanFak = $jurusan->where('fakultas_id', $fak->id); @endphp
              @if($jursanFak->isNotEmpty())
                <optgroup label="{{ $fak->kode }} — {{ $fak->nama }}">
                  @foreach($jursanFak as $jur)
                    <option value="{{ $jur->id }}">{{ $jur->kode }} — {{ $jur->nama }}</option>
                  @endforeach
                </optgroup>
              @endif
            @endforeach
          </select>
        @endif
        <p class="f-error hidden" id="err-create-jurusan_id"></p>
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="f-label">Kode MK <span class="text-red-400">*</span></label>
          <input type="text" name="kode" class="f-input" placeholder="cth: IF101, SI201" style="text-transform:uppercase">
          <p class="f-error hidden" id="err-create-kode"></p>
        </div>
        <div>
          <label class="f-label">Nama Mata Kuliah <span class="text-red-400">*</span></label>
          <input type="text" name="nama" class="f-input" placeholder="cth: Algoritma & Pemrograman">
          <p class="f-error hidden" id="err-create-nama"></p>
        </div>
      </div>

      <div class="grid grid-cols-3 gap-4">
        <div>
          <label class="f-label">SKS <span class="text-red-400">*</span></label>
          <select name="sks" class="f-input">
            @for($s = 1; $s <= 6; $s++)
              <option value="{{ $s }}" {{ $s == 2 ? 'selected' : '' }}>{{ $s }} SKS</option>
            @endfor
          </select>
          <p class="f-error hidden" id="err-create-sks"></p>
        </div>
        <div>
          <label class="f-label">Semester</label>
          <select name="semester" class="f-input">
            <option value="">— Semua —</option>
            @for($s = 1; $s <= 8; $s++)
              <option value="{{ $s }}">Semester {{ $s }}</option>
            @endfor
          </select>
          <p class="f-error hidden" id="err-create-semester"></p>
        </div>
        <div>
          <label class="f-label">Jenis <span class="text-red-400">*</span></label>
          <select name="jenis" class="f-input">
            <option value="Wajib" selected>Wajib</option>
            <option value="Pilihan">Pilihan</option>
          </select>
          <p class="f-error hidden" id="err-create-jenis"></p>
        </div>
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
  <div class="modal-box" style="max-width:540px">
    <div class="flex items-center justify-between px-6 py-5 border-b" style="border-color:var(--border)">
      <div class="flex items-center gap-3">
        <div class="a-bg-lt a-text w-10 h-10 rounded-xl grid place-items-center"><i class="fa-solid fa-pen"></i></div>
        <h3 class="font-display font-bold text-[16px]" style="color:var(--text)">Edit Mata Kuliah</h3>
      </div>
      <button onclick="closeModal('modal-edit')" class="w-8 h-8 rounded-lg grid place-items-center" style="color:var(--muted)"
        onmouseover="this.style.background='var(--surface2)'" onmouseout="this.style.background='transparent'">
        <i class="fa-solid fa-xmark"></i>
      </button>
    </div>
    <form id="form-edit" class="px-6 py-5 space-y-4">
      <input type="hidden" id="edit-id">

      <div>
        <label class="f-label">Jurusan <span class="text-red-400">*</span></label>
        <select name="jurusan_id" id="edit-jurusan" class="f-input">
          <option value="">— Pilih Jurusan —</option>
          @foreach($fakultas as $fak)
            @php $jursanFak = $jurusan->where('fakultas_id', $fak->id); @endphp
            @if($jursanFak->isNotEmpty())
              <optgroup label="{{ $fak->kode }} — {{ $fak->nama }}">
                @foreach($jursanFak as $jur)
                  <option value="{{ $jur->id }}">{{ $jur->kode }} — {{ $jur->nama }}</option>
                @endforeach
              </optgroup>
            @endif
          @endforeach
        </select>
        <p class="f-error hidden" id="err-edit-jurusan_id"></p>
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="f-label">Kode MK <span class="text-red-400">*</span></label>
          <input type="text" name="kode" id="edit-kode" class="f-input" style="text-transform:uppercase">
          <p class="f-error hidden" id="err-edit-kode"></p>
        </div>
        <div>
          <label class="f-label">Nama Mata Kuliah <span class="text-red-400">*</span></label>
          <input type="text" name="nama" id="edit-nama" class="f-input">
          <p class="f-error hidden" id="err-edit-nama"></p>
        </div>
      </div>

      <div class="grid grid-cols-3 gap-4">
        <div>
          <label class="f-label">SKS <span class="text-red-400">*</span></label>
          <select name="sks" id="edit-sks" class="f-input">
            @for($s = 1; $s <= 6; $s++)
              <option value="{{ $s }}">{{ $s }} SKS</option>
            @endfor
          </select>
          <p class="f-error hidden" id="err-edit-sks"></p>
        </div>
        <div>
          <label class="f-label">Semester</label>
          <select name="semester" id="edit-semester" class="f-input">
            <option value="">— Semua —</option>
            @for($s = 1; $s <= 8; $s++)
              <option value="{{ $s }}">Semester {{ $s }}</option>
            @endfor
          </select>
          <p class="f-error hidden" id="err-edit-semester"></p>
        </div>
        <div>
          <label class="f-label">Jenis <span class="text-red-400">*</span></label>
          <select name="jenis" id="edit-jenis" class="f-input">
            <option value="Wajib">Wajib</option>
            <option value="Pilihan">Pilihan</option>
          </select>
          <p class="f-error hidden" id="err-edit-jenis"></p>
        </div>
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
      <h3 class="font-display font-bold text-[16px] mb-1" style="color:var(--text)">Hapus Mata Kuliah?</h3>
      <p class="text-[12.5px]" style="color:var(--muted)">Mata kuliah <strong id="delete-name" style="color:var(--text)"></strong> akan dihapus permanen.</p>
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
$(()=>$('#mk-table').DataTable({
  language:{...DT_LANG, searchPlaceholder:'Cari mata kuliah...'},
  columnDefs:[
    {orderable:false, targets:[0,8]},
    {className:'text-center', targets:[0,4,5]},
  ],
  pageLength:15, dom:DT_DOM,
}));

// CREATE
document.getElementById('form-create').addEventListener('submit', function(e){
  e.preventDefault();
  clearErrors('create');
  setLoading('btn-create', true);
  const form = new FormData(this);
  const body = new URLSearchParams();
  body.append('jurusan_id', document.getElementById('create-jurusan')?.value || '');
  body.append('kode', form.get('kode'));
  body.append('nama', form.get('nama'));
  body.append('sks', form.get('sks'));
  body.append('semester', form.get('semester') || '');
  body.append('jenis', form.get('jenis'));
  body.append('deskripsi', form.get('deskripsi') || '');
  body.append('aktif', document.getElementById('create-aktif').checked ? 1 : 0);

  fetch('/admin/matakuliah', {
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
function openEdit(id, jurusanId, kode, nama, sks, semester, jenis, deskripsi, aktif){
  document.getElementById('edit-id').value = id;
  document.getElementById('edit-jurusan').value = jurusanId;
  document.getElementById('edit-kode').value = kode;
  document.getElementById('edit-nama').value = nama;
  document.getElementById('edit-sks').value = sks;
  document.getElementById('edit-semester').value = semester ?? '';
  document.getElementById('edit-jenis').value = jenis;
  document.getElementById('edit-deskripsi').value = deskripsi || '';
  document.getElementById('edit-aktif').checked = aktif;
  clearErrors('edit');
  openModal('modal-edit');
}

document.getElementById('form-edit').addEventListener('submit', function(e){
  e.preventDefault();
  clearErrors('edit');
  const id = document.getElementById('edit-id').value;
  setLoading('btn-edit', true);
  const body = new URLSearchParams({_method:'PUT'});
  body.append('jurusan_id', document.getElementById('edit-jurusan').value);
  body.append('kode', document.getElementById('edit-kode').value);
  body.append('nama', document.getElementById('edit-nama').value);
  body.append('sks', document.getElementById('edit-sks').value);
  body.append('semester', document.getElementById('edit-semester').value);
  body.append('jenis', document.getElementById('edit-jenis').value);
  body.append('deskripsi', document.getElementById('edit-deskripsi').value);
  body.append('aktif', document.getElementById('edit-aktif').checked ? 1 : 0);

  fetch(`/admin/matakuliah/${id}`, {
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
  fetch(`/admin/matakuliah/${deleteId}`, {
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
  ['jurusan_id','kode','nama','sks','semester','jenis','deskripsi'].forEach(f=>{
    const el = document.getElementById(`err-${prefix}-${f}`);
    if(el){ el.textContent=''; el.classList.add('hidden'); }
  });
}

@if(session('success'))document.addEventListener('DOMContentLoaded',()=>showToast('success','{{ session("success") }}'));@endif
@if(session('error'))document.addEventListener('DOMContentLoaded',()=>showToast('error','{{ session("error") }}'));@endif
</script>
@endpush
