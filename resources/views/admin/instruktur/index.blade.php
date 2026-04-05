@extends('layouts.admin')
@section('title','Instruktur')
@section('page-title','Instruktur')

@push('styles')
@include('admin.partials.datatable-styles')
@endpush

@section('content')
<div id="toast-container" class="toast-wrap"></div>

{{-- Header --}}
<div class="flex items-center justify-between animate-fadeUp">
  <div>
    <h2 class="font-display font-bold text-[20px]" style="color:var(--text)">Instruktur / Dosen</h2>
    <p class="text-[13px] mt-0.5" style="color:var(--muted)">Kelola data instruktur dan dosen pengajar</p>
  </div>
  @canaccess('tambah.instruktur')
  <button onclick="openModal('modal-create')"
    class="flex items-center gap-2 px-4 py-2 rounded-xl text-[13px] font-semibold text-white a-grad shadow">
    <i class="fa-solid fa-plus text-[11px]"></i> Tambah Instruktur
  </button>
  @endcanaccess
</div>

{{-- Stat Cards --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 animate-fadeUp d1">
  <div class="rounded-2xl p-5 border flex items-center gap-4" style="background:var(--surface);border-color:var(--border)">
    <div class="a-bg-lt a-text w-11 h-11 rounded-xl grid place-items-center text-lg flex-shrink-0"><i class="fa-solid fa-chalkboard-user"></i></div>
    <div>
      <div class="font-display text-[26px] font-bold" style="color:var(--text)">{{ $stats['total'] }}</div>
      <div class="text-[12px]" style="color:var(--muted)">Total Instruktur</div>
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
    <div class="bg-rose-500/15 text-rose-400 w-11 h-11 rounded-xl grid place-items-center text-lg flex-shrink-0"><i class="fa-solid fa-circle-xmark"></i></div>
    <div>
      <div class="font-display text-[26px] font-bold" style="color:var(--text)">{{ $stats['nonaktif'] }}</div>
      <div class="text-[12px]" style="color:var(--muted)">Nonaktif</div>
    </div>
  </div>
  <div class="rounded-2xl p-5 border flex items-center gap-4" style="background:var(--surface);border-color:var(--border)">
    <div class="bg-violet-500/15 text-violet-400 w-11 h-11 rounded-xl grid place-items-center text-lg flex-shrink-0"><i class="fa-solid fa-graduation-cap"></i></div>
    <div>
      <div class="font-display text-[26px] font-bold" style="color:var(--text)">{{ $stats['s3'] }}</div>
      <div class="text-[12px]" style="color:var(--muted)">Doktor (S3)</div>
    </div>
  </div>
</div>

{{-- Table --}}
<div class="rounded-2xl overflow-hidden border animate-fadeUp d2" style="background:var(--surface);border-color:var(--border)">
  <div class="flex items-center justify-between px-5 py-4 border-b" style="border-color:var(--border)">
    <span class="font-display font-semibold text-[15px]" style="color:var(--text)">Daftar Instruktur</span>
    <span class="text-[12px] px-2.5 py-1 rounded-full a-bg-lt a-text font-semibold">{{ $stats['total'] }} instruktur</span>
  </div>
  <div class="p-5">
    <table id="instruktur-table" class="w-full" style="width:100%">
      <thead>
        <tr>
          <th>#</th>
          <th>NIDN / NIP</th>
          <th>Nama</th>
          <th>Bidang Keahlian</th>
          <th>Pendidikan</th>
          <th>Status</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        @foreach($instruktur as $i => $ins)
        <tr>
          <td class="text-center" style="color:var(--muted);width:48px">{{ $i + 1 }}</td>
          <td>
            <div class="space-y-0.5">
              @if($ins->nidn)
                <div>
                  <span class="text-[10px] font-semibold uppercase" style="color:var(--muted)">NIDN</span>
                  <code class="ml-1 text-[12px] font-bold px-1.5 py-0.5 rounded a-bg-lt a-text" style="font-family:monospace">{{ $ins->nidn }}</code>
                </div>
              @endif
              @if($ins->nip)
                <div>
                  <span class="text-[10px] font-semibold uppercase" style="color:var(--muted)">NIP</span>
                  <code class="ml-1 text-[12px] font-bold px-1.5 py-0.5 rounded a-bg-lt a-text" style="font-family:monospace">{{ $ins->nip }}</code>
                </div>
              @endif
              @if(!$ins->nidn && !$ins->nip)
                <span class="text-[12px]" style="color:var(--muted)">—</span>
              @endif
            </div>
          </td>
          <td>
            <div>
              <div class="font-semibold text-[13.5px]" style="color:var(--text)">{{ $ins->nama }}</div>
              @if($ins->email)
                <div class="text-[11.5px]" style="color:var(--muted)">{{ $ins->email }}</div>
              @endif
            </div>
          </td>
          <td>
            @if($ins->bidang_keahlian)
              <span class="text-[13px]" style="color:var(--text)">{{ $ins->bidang_keahlian }}</span>
            @else
              <span style="color:var(--muted)">—</span>
            @endif
          </td>
          <td class="text-center">
            @if($ins->pendidikan_terakhir)
              @php
                $pdColor = match($ins->pendidikan_terakhir) {
                  'S3' => 'bg-violet-500/15 text-violet-400',
                  'S2' => 'bg-blue-500/15 text-blue-400',
                  'S1' => 'bg-slate-500/15 text-slate-400',
                  default => 'bg-slate-500/15 text-slate-400',
                };
              @endphp
              <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold {{ $pdColor }}">
                {{ $ins->pendidikan_terakhir }}
              </span>
            @else
              <span style="color:var(--muted)">—</span>
            @endif
          </td>
          <td>
            @if($ins->status === 'Aktif')
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
              <a href="{{ route('admin.instruktur.show', $ins->id) }}"
                class="w-8 h-8 rounded-lg grid place-items-center text-[12px] border transition-colors"
                style="border-color:var(--border);color:var(--sub)"
                onmouseover="this.style.borderColor='var(--ac)';this.style.color='var(--ac)'"
                onmouseout="this.style.borderColor='var(--border)';this.style.color='var(--sub)'">
                <i class="fa-solid fa-eye"></i>
              </a>
              @canaccess('edit.instruktur')
              <button onclick="openEdit({{ $ins->id }},{{ json_encode($ins->nidn) }},{{ json_encode($ins->nip) }},{{ json_encode($ins->nama) }},{{ json_encode($ins->email) }},{{ json_encode($ins->jenis_kelamin) }},{{ json_encode($ins->bidang_keahlian) }},{{ json_encode($ins->pendidikan_terakhir) }},{{ json_encode($ins->no_hp) }},{{ json_encode($ins->status) }})"
                class="w-8 h-8 rounded-lg grid place-items-center text-[12px] border transition-colors"
                style="border-color:var(--border);color:var(--sub)"
                onmouseover="this.style.borderColor='var(--ac)';this.style.color='var(--ac)'"
                onmouseout="this.style.borderColor='var(--border)';this.style.color='var(--sub)'">
                <i class="fa-solid fa-pen"></i>
              </button>
              @endcanaccess
              @canaccess('hapus.instruktur')
              <button onclick="openDelete({{ $ins->id }},{{ json_encode($ins->nama) }})"
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
  <div class="modal-box" style="max-width:560px">
    <div class="flex items-center justify-between px-6 py-5 border-b" style="border-color:var(--border)">
      <div class="flex items-center gap-3">
        <div class="a-bg-lt a-text w-10 h-10 rounded-xl grid place-items-center"><i class="fa-solid fa-chalkboard-user"></i></div>
        <h3 class="font-display font-bold text-[16px]" style="color:var(--text)">Tambah Instruktur</h3>
      </div>
      <button onclick="closeModal('modal-create')" class="w-8 h-8 rounded-lg grid place-items-center" style="color:var(--muted)"
        onmouseover="this.style.background='var(--surface2)'" onmouseout="this.style.background='transparent'">
        <i class="fa-solid fa-xmark"></i>
      </button>
    </div>
    <form id="form-create" class="px-6 py-5 space-y-4">
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="f-label">NIDN</label>
          <input type="text" name="nidn" class="f-input" placeholder="cth: 0012345678">
          <p class="f-error hidden" id="err-create-nidn"></p>
        </div>
        <div>
          <label class="f-label">NIP</label>
          <input type="text" name="nip" class="f-input" placeholder="cth: 198501012010011001">
          <p class="f-error hidden" id="err-create-nip"></p>
        </div>
      </div>
      <div>
        <label class="f-label">Nama Lengkap <span class="text-red-400">*</span></label>
        <input type="text" name="nama" class="f-input" placeholder="Nama lengkap instruktur">
        <p class="f-error hidden" id="err-create-nama"></p>
      </div>
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="f-label">Email</label>
          <input type="email" name="email" class="f-input" placeholder="email@kampus.ac.id">
          <p class="f-error hidden" id="err-create-email"></p>
        </div>
        <div>
          <label class="f-label">No. HP</label>
          <input type="text" name="no_hp" class="f-input" placeholder="cth: 081234567890">
          <p class="f-error hidden" id="err-create-no_hp"></p>
        </div>
      </div>
      <div>
        <label class="f-label">Bidang Keahlian</label>
        <input type="text" name="bidang_keahlian" class="f-input" placeholder="cth: Kecerdasan Buatan, Jaringan Komputer">
        <p class="f-error hidden" id="err-create-bidang_keahlian"></p>
      </div>
      <div class="grid grid-cols-3 gap-4">
        <div>
          <label class="f-label">Pendidikan</label>
          <select name="pendidikan_terakhir" class="f-input">
            <option value="">— Pilih —</option>
            <option value="S1">S1</option>
            <option value="S2">S2</option>
            <option value="S3">S3</option>
          </select>
          <p class="f-error hidden" id="err-create-pendidikan_terakhir"></p>
        </div>
        <div>
          <label class="f-label">Jenis Kelamin</label>
          <select name="jenis_kelamin" class="f-input">
            <option value="">— Pilih —</option>
            <option value="L">Laki-laki</option>
            <option value="P">Perempuan</option>
          </select>
          <p class="f-error hidden" id="err-create-jenis_kelamin"></p>
        </div>
        <div>
          <label class="f-label">Status <span class="text-red-400">*</span></label>
          <select name="status" class="f-input">
            <option value="Aktif" selected>Aktif</option>
            <option value="Nonaktif">Nonaktif</option>
          </select>
          <p class="f-error hidden" id="err-create-status"></p>
        </div>
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
  <div class="modal-box" style="max-width:560px">
    <div class="flex items-center justify-between px-6 py-5 border-b" style="border-color:var(--border)">
      <div class="flex items-center gap-3">
        <div class="a-bg-lt a-text w-10 h-10 rounded-xl grid place-items-center"><i class="fa-solid fa-pen"></i></div>
        <h3 class="font-display font-bold text-[16px]" style="color:var(--text)">Edit Instruktur</h3>
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
          <label class="f-label">NIDN</label>
          <input type="text" name="nidn" id="edit-nidn" class="f-input">
          <p class="f-error hidden" id="err-edit-nidn"></p>
        </div>
        <div>
          <label class="f-label">NIP</label>
          <input type="text" name="nip" id="edit-nip" class="f-input">
          <p class="f-error hidden" id="err-edit-nip"></p>
        </div>
      </div>
      <div>
        <label class="f-label">Nama Lengkap <span class="text-red-400">*</span></label>
        <input type="text" name="nama" id="edit-nama" class="f-input">
        <p class="f-error hidden" id="err-edit-nama"></p>
      </div>
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="f-label">Email</label>
          <input type="email" name="email" id="edit-email" class="f-input">
          <p class="f-error hidden" id="err-edit-email"></p>
        </div>
        <div>
          <label class="f-label">No. HP</label>
          <input type="text" name="no_hp" id="edit-no_hp" class="f-input">
          <p class="f-error hidden" id="err-edit-no_hp"></p>
        </div>
      </div>
      <div>
        <label class="f-label">Bidang Keahlian</label>
        <input type="text" name="bidang_keahlian" id="edit-bidang_keahlian" class="f-input">
        <p class="f-error hidden" id="err-edit-bidang_keahlian"></p>
      </div>
      <div class="grid grid-cols-3 gap-4">
        <div>
          <label class="f-label">Pendidikan</label>
          <select name="pendidikan_terakhir" id="edit-pendidikan_terakhir" class="f-input">
            <option value="">— Pilih —</option>
            <option value="S1">S1</option>
            <option value="S2">S2</option>
            <option value="S3">S3</option>
          </select>
          <p class="f-error hidden" id="err-edit-pendidikan_terakhir"></p>
        </div>
        <div>
          <label class="f-label">Jenis Kelamin</label>
          <select name="jenis_kelamin" id="edit-jenis_kelamin" class="f-input">
            <option value="">— Pilih —</option>
            <option value="L">Laki-laki</option>
            <option value="P">Perempuan</option>
          </select>
          <p class="f-error hidden" id="err-edit-jenis_kelamin"></p>
        </div>
        <div>
          <label class="f-label">Status <span class="text-red-400">*</span></label>
          <select name="status" id="edit-status" class="f-input">
            <option value="Aktif">Aktif</option>
            <option value="Nonaktif">Nonaktif</option>
          </select>
          <p class="f-error hidden" id="err-edit-status"></p>
        </div>
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
      <h3 class="font-display font-bold text-[16px] mb-1" style="color:var(--text)">Hapus Instruktur?</h3>
      <p class="text-[12.5px]" style="color:var(--muted)">Data <strong id="delete-name" style="color:var(--text)"></strong> akan dihapus permanen.</p>
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
$(()=>$('#instruktur-table').DataTable({
  language:{...DT_LANG, searchPlaceholder:'Cari nama, NIDN, NIP...'},
  columnDefs:[
    {orderable:false, targets:[0,6]},
    {className:'text-center', targets:[0,4]},
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
  body.append('nidn', form.get('nidn') || '');
  body.append('nip', form.get('nip') || '');
  body.append('nama', form.get('nama'));
  body.append('email', form.get('email') || '');
  body.append('no_hp', form.get('no_hp') || '');
  body.append('bidang_keahlian', form.get('bidang_keahlian') || '');
  body.append('pendidikan_terakhir', form.get('pendidikan_terakhir') || '');
  body.append('jenis_kelamin', form.get('jenis_kelamin') || '');
  body.append('status', form.get('status'));

  fetch('/admin/instruktur', {
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
function openEdit(id, nidn, nip, nama, email, jenisKelamin, bidang, pendidikan, noHp, status){
  document.getElementById('edit-id').value = id;
  document.getElementById('edit-nidn').value = nidn || '';
  document.getElementById('edit-nip').value = nip || '';
  document.getElementById('edit-nama').value = nama;
  document.getElementById('edit-email').value = email || '';
  document.getElementById('edit-no_hp').value = noHp || '';
  document.getElementById('edit-bidang_keahlian').value = bidang || '';
  document.getElementById('edit-pendidikan_terakhir').value = pendidikan || '';
  document.getElementById('edit-jenis_kelamin').value = jenisKelamin || '';
  document.getElementById('edit-status').value = status;
  clearErrors('edit');
  openModal('modal-edit');
}

document.getElementById('form-edit').addEventListener('submit', function(e){
  e.preventDefault();
  clearErrors('edit');
  const id = document.getElementById('edit-id').value;
  setLoading('btn-edit', true);
  const body = new URLSearchParams({_method:'PUT'});
  body.append('nidn', document.getElementById('edit-nidn').value);
  body.append('nip', document.getElementById('edit-nip').value);
  body.append('nama', document.getElementById('edit-nama').value);
  body.append('email', document.getElementById('edit-email').value);
  body.append('no_hp', document.getElementById('edit-no_hp').value);
  body.append('bidang_keahlian', document.getElementById('edit-bidang_keahlian').value);
  body.append('pendidikan_terakhir', document.getElementById('edit-pendidikan_terakhir').value);
  body.append('jenis_kelamin', document.getElementById('edit-jenis_kelamin').value);
  body.append('status', document.getElementById('edit-status').value);

  fetch(`/admin/instruktur/${id}`, {
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
  fetch(`/admin/instruktur/${deleteId}`, {
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
  ['nidn','nip','nama','email','no_hp','bidang_keahlian','pendidikan_terakhir','jenis_kelamin','status'].forEach(f=>{
    const el = document.getElementById(`err-${prefix}-${f}`);
    if(el){ el.textContent=''; el.classList.add('hidden'); }
  });
}

@if(session('success'))document.addEventListener('DOMContentLoaded',()=>showToast('success','{{ session("success") }}'));@endif
@if(session('error'))document.addEventListener('DOMContentLoaded',()=>showToast('error','{{ session("error") }}'));@endif
</script>
@endpush
