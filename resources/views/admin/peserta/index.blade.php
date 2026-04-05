@extends('layouts.admin')
@section('title','Peserta')
@section('page-title','Peserta')

@push('styles')
@include('admin.partials.datatable-styles')
@endpush

@section('content')
<div id="toast-container" class="toast-wrap"></div>

{{-- Header --}}
<div class="flex items-center justify-between animate-fadeUp">
  <div>
    <h2 class="font-display font-bold text-[20px]" style="color:var(--text)">Peserta / Mahasiswa</h2>
    <p class="text-[13px] mt-0.5" style="color:var(--muted)">Kelola data mahasiswa terdaftar</p>
  </div>
  @canaccess('tambah.peserta')
  <button onclick="openModal('modal-create')"
    class="flex items-center gap-2 px-4 py-2 rounded-xl text-[13px] font-semibold text-white a-grad shadow">
    <i class="fa-solid fa-plus text-[11px]"></i> Tambah Peserta
  </button>
  @endcanaccess
</div>

{{-- Stat Cards --}}
<div class="grid grid-cols-2 sm:grid-cols-5 gap-4 animate-fadeUp d1">
  <div class="rounded-2xl p-5 border flex items-center gap-4" style="background:var(--surface);border-color:var(--border)">
    <div class="a-bg-lt a-text w-11 h-11 rounded-xl grid place-items-center text-lg flex-shrink-0"><i class="fa-solid fa-users"></i></div>
    <div>
      <div class="font-display text-[26px] font-bold" style="color:var(--text)">{{ $stats['total'] }}</div>
      <div class="text-[12px]" style="color:var(--muted)">Total</div>
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
    <div class="bg-amber-500/15 text-amber-400 w-11 h-11 rounded-xl grid place-items-center text-lg flex-shrink-0"><i class="fa-solid fa-clock-rotate-left"></i></div>
    <div>
      <div class="font-display text-[26px] font-bold" style="color:var(--text)">{{ $stats['cuti'] }}</div>
      <div class="text-[12px]" style="color:var(--muted)">Cuti</div>
    </div>
  </div>
  <div class="rounded-2xl p-5 border flex items-center gap-4" style="background:var(--surface);border-color:var(--border)">
    <div class="bg-rose-500/15 text-rose-400 w-11 h-11 rounded-xl grid place-items-center text-lg flex-shrink-0"><i class="fa-solid fa-user-xmark"></i></div>
    <div>
      <div class="font-display text-[26px] font-bold" style="color:var(--text)">{{ $stats['dropout'] }}</div>
      <div class="text-[12px]" style="color:var(--muted)">Dropout</div>
    </div>
  </div>
  <div class="rounded-2xl p-5 border flex items-center gap-4" style="background:var(--surface);border-color:var(--border)">
    <div class="bg-blue-500/15 text-blue-400 w-11 h-11 rounded-xl grid place-items-center text-lg flex-shrink-0"><i class="fa-solid fa-graduation-cap"></i></div>
    <div>
      <div class="font-display text-[26px] font-bold" style="color:var(--text)">{{ $stats['lulus'] }}</div>
      <div class="text-[12px]" style="color:var(--muted)">Lulus</div>
    </div>
  </div>
</div>

{{-- Table --}}
<div class="rounded-2xl overflow-hidden border animate-fadeUp d2" style="background:var(--surface);border-color:var(--border)">
  <div class="flex items-center justify-between px-5 py-4 border-b" style="border-color:var(--border)">
    <span class="font-display font-semibold text-[15px]" style="color:var(--text)">Daftar Mahasiswa</span>
    <span class="text-[12px] px-2.5 py-1 rounded-full a-bg-lt a-text font-semibold">{{ $stats['total'] }} mahasiswa</span>
  </div>
  <div class="p-5">
    <table id="peserta-table" class="w-full" style="width:100%">
      <thead>
        <tr>
          <th>#</th>
          <th>NIM</th>
          <th>Nama</th>
          <th>Jurusan</th>
          <th>Angkatan</th>
          <th>Status</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        @foreach($mahasiswa as $i => $mhs)
        <tr>
          <td class="text-center" style="color:var(--muted);width:48px">{{ $i + 1 }}</td>
          <td>
            <code class="text-[12px] font-bold px-2 py-0.5 rounded-lg a-bg-lt a-text" style="font-family:monospace">{{ $mhs->nim }}</code>
          </td>
          <td>
            <div>
              <div class="font-semibold text-[13.5px]" style="color:var(--text)">{{ $mhs->nama }}</div>
              @if($mhs->email)
                <div class="text-[11.5px]" style="color:var(--muted)">{{ $mhs->email }}</div>
              @endif
            </div>
          </td>
          <td>
            @if($mhs->jurusan && $mhs->jurusan->id)
              <div>
                <div class="text-[13px] font-medium" style="color:var(--text)">{{ $mhs->jurusan->nama }}</div>
                @if($mhs->jurusan->fakultas)
                  <div class="text-[11px]" style="color:var(--muted)">{{ $mhs->jurusan->fakultas->nama }}</div>
                @endif
              </div>
            @else
              <span class="text-[12px]" style="color:var(--muted)">—</span>
            @endif
          </td>
          <td class="text-center">
            @if($mhs->angkatan)
              <span class="text-[13px] font-semibold" style="color:var(--text)">{{ $mhs->angkatan }}</span>
            @else
              <span style="color:var(--muted)">—</span>
            @endif
          </td>
          <td>
            @php
              $statusColor = match($mhs->status) {
                'Aktif'   => 'bg-emerald-500/15 text-emerald-400',
                'Cuti'    => 'bg-amber-500/15 text-amber-400',
                'Dropout' => 'bg-rose-500/15 text-rose-400',
                'Lulus'   => 'bg-blue-500/15 text-blue-400',
                default   => 'bg-slate-500/15 text-slate-400',
              };
            @endphp
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold {{ $statusColor }}">
              <i class="fa-solid fa-circle text-[7px]"></i> {{ $mhs->status }}
            </span>
          </td>
          <td>
            <div class="flex items-center gap-2">
              @canaccess('lihat.peserta')
              <a href="{{ route('admin.peserta.show', $mhs->id) }}"
                class="w-8 h-8 rounded-lg grid place-items-center text-[12px] border transition-colors"
                style="border-color:var(--border);color:var(--sub)"
                onmouseover="this.style.borderColor='var(--ac)';this.style.color='var(--ac)'"
                onmouseout="this.style.borderColor='var(--border)';this.style.color='var(--sub)'"
                title="Lihat Profil">
                <i class="fa-solid fa-eye"></i>
              </a>
              @endcanaccess
              @canaccess('edit.peserta')
              <button onclick="openEdit({{ $mhs->id }},{{ json_encode($mhs->nim) }},{{ json_encode($mhs->nama) }},{{ json_encode($mhs->email) }},{{ $mhs->jurusan_id ?? 'null' }},{{ $mhs->angkatan ?? 'null' }},{{ json_encode($mhs->jenis_kelamin) }},{{ json_encode($mhs->tempat_lahir) }},{{ json_encode($mhs->tanggal_lahir?->format('Y-m-d')) }},{{ json_encode($mhs->no_hp) }},{{ json_encode($mhs->alamat) }},{{ json_encode($mhs->status) }})"
                class="w-8 h-8 rounded-lg grid place-items-center text-[12px] border transition-colors"
                style="border-color:var(--border);color:var(--sub)"
                onmouseover="this.style.borderColor='var(--ac)';this.style.color='var(--ac)'"
                onmouseout="this.style.borderColor='var(--border)';this.style.color='var(--sub)'">
                <i class="fa-solid fa-pen"></i>
              </button>
              @endcanaccess
              @canaccess('hapus.peserta')
              <button onclick="openDelete({{ $mhs->id }},{{ json_encode($mhs->nama) }})"
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
  <div class="modal-box" style="max-width:600px">
    <div class="flex items-center justify-between px-6 py-5 border-b" style="border-color:var(--border)">
      <div class="flex items-center gap-3">
        <div class="a-bg-lt a-text w-10 h-10 rounded-xl grid place-items-center"><i class="fa-solid fa-user-plus"></i></div>
        <h3 class="font-display font-bold text-[16px]" style="color:var(--text)">Tambah Mahasiswa</h3>
      </div>
      <button onclick="closeModal('modal-create')" class="w-8 h-8 rounded-lg grid place-items-center" style="color:var(--muted)"
        onmouseover="this.style.background='var(--surface2)'" onmouseout="this.style.background='transparent'">
        <i class="fa-solid fa-xmark"></i>
      </button>
    </div>
    <form id="form-create" class="px-6 py-5 space-y-4 overflow-y-auto" style="max-height:70vh">
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="f-label">NIM <span class="text-red-400">*</span></label>
          <input type="text" name="nim" class="f-input" placeholder="cth: 2022010001" style="text-transform:uppercase">
          <p class="f-error hidden" id="err-create-nim"></p>
        </div>
        <div>
          <label class="f-label">Angkatan</label>
          <input type="number" name="angkatan" class="f-input" placeholder="cth: 2022" min="2000" max="2099">
          <p class="f-error hidden" id="err-create-angkatan"></p>
        </div>
      </div>
      <div>
        <label class="f-label">Nama Lengkap <span class="text-red-400">*</span></label>
        <input type="text" name="nama" class="f-input" placeholder="Nama lengkap mahasiswa">
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
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="f-label">Jurusan</label>
          <select name="jurusan_id" id="create-jurusan" class="f-input">
            <option value="">— Pilih Jurusan —</option>
            @foreach($jurusan as $jur)
              <option value="{{ $jur->id }}">{{ $jur->kode }} — {{ $jur->nama }}</option>
            @endforeach
          </select>
          <p class="f-error hidden" id="err-create-jurusan_id"></p>
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
      </div>
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="f-label">Tempat Lahir</label>
          <input type="text" name="tempat_lahir" class="f-input" placeholder="cth: Jakarta">
          <p class="f-error hidden" id="err-create-tempat_lahir"></p>
        </div>
        <div>
          <label class="f-label">Tanggal Lahir</label>
          <input type="date" name="tanggal_lahir" class="f-input">
          <p class="f-error hidden" id="err-create-tanggal_lahir"></p>
        </div>
      </div>
      <div>
        <label class="f-label">Alamat</label>
        <textarea name="alamat" class="f-input" rows="2" placeholder="Alamat lengkap (opsional)"></textarea>
        <p class="f-error hidden" id="err-create-alamat"></p>
      </div>
      <div>
        <label class="f-label">Status <span class="text-red-400">*</span></label>
        <select name="status" class="f-input">
          <option value="Aktif" selected>Aktif</option>
          <option value="Cuti">Cuti</option>
          <option value="Dropout">Dropout</option>
          <option value="Lulus">Lulus</option>
        </select>
        <p class="f-error hidden" id="err-create-status"></p>
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
  <div class="modal-box" style="max-width:600px">
    <div class="flex items-center justify-between px-6 py-5 border-b" style="border-color:var(--border)">
      <div class="flex items-center gap-3">
        <div class="a-bg-lt a-text w-10 h-10 rounded-xl grid place-items-center"><i class="fa-solid fa-pen"></i></div>
        <h3 class="font-display font-bold text-[16px]" style="color:var(--text)">Edit Mahasiswa</h3>
      </div>
      <button onclick="closeModal('modal-edit')" class="w-8 h-8 rounded-lg grid place-items-center" style="color:var(--muted)"
        onmouseover="this.style.background='var(--surface2)'" onmouseout="this.style.background='transparent'">
        <i class="fa-solid fa-xmark"></i>
      </button>
    </div>
    <form id="form-edit" class="px-6 py-5 space-y-4 overflow-y-auto" style="max-height:70vh">
      <input type="hidden" id="edit-id">
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="f-label">NIM <span class="text-red-400">*</span></label>
          <input type="text" name="nim" id="edit-nim" class="f-input" style="text-transform:uppercase">
          <p class="f-error hidden" id="err-edit-nim"></p>
        </div>
        <div>
          <label class="f-label">Angkatan</label>
          <input type="number" name="angkatan" id="edit-angkatan" class="f-input" min="2000" max="2099">
          <p class="f-error hidden" id="err-edit-angkatan"></p>
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
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="f-label">Jurusan</label>
          <select name="jurusan_id" id="edit-jurusan" class="f-input">
            <option value="">— Pilih Jurusan —</option>
            @foreach($jurusan as $jur)
              <option value="{{ $jur->id }}">{{ $jur->kode }} — {{ $jur->nama }}</option>
            @endforeach
          </select>
          <p class="f-error hidden" id="err-edit-jurusan_id"></p>
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
      </div>
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="f-label">Tempat Lahir</label>
          <input type="text" name="tempat_lahir" id="edit-tempat_lahir" class="f-input">
          <p class="f-error hidden" id="err-edit-tempat_lahir"></p>
        </div>
        <div>
          <label class="f-label">Tanggal Lahir</label>
          <input type="date" name="tanggal_lahir" id="edit-tanggal_lahir" class="f-input">
          <p class="f-error hidden" id="err-edit-tanggal_lahir"></p>
        </div>
      </div>
      <div>
        <label class="f-label">Alamat</label>
        <textarea name="alamat" id="edit-alamat" class="f-input" rows="2"></textarea>
        <p class="f-error hidden" id="err-edit-alamat"></p>
      </div>
      <div>
        <label class="f-label">Status <span class="text-red-400">*</span></label>
        <select name="status" id="edit-status" class="f-input">
          <option value="Aktif">Aktif</option>
          <option value="Cuti">Cuti</option>
          <option value="Dropout">Dropout</option>
          <option value="Lulus">Lulus</option>
        </select>
        <p class="f-error hidden" id="err-edit-status"></p>
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
      <h3 class="font-display font-bold text-[16px] mb-1" style="color:var(--text)">Hapus Mahasiswa?</h3>
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
$(()=>$('#peserta-table').DataTable({
  language:{...DT_LANG, searchPlaceholder:'Cari nama, NIM...'},
  columnDefs:[
    {orderable:false, targets:[0,6]},
    {className:'text-center', targets:[0,4]},
  ],
  pageLength:15, dom:DT_DOM,
}));

const FIELDS_CREATE = ['nim','nama','email','no_hp','angkatan','tempat_lahir','tanggal_lahir','alamat'];
const FIELDS_EDIT   = ['nim','nama','email','no_hp','angkatan','tempat_lahir','tanggal_lahir','alamat'];

// CREATE
document.getElementById('form-create').addEventListener('submit', function(e){
  e.preventDefault();
  clearErrors('create');
  setLoading('btn-create', true);
  const form = new FormData(this);
  const body = new URLSearchParams();
  body.append('nim', form.get('nim'));
  body.append('nama', form.get('nama'));
  body.append('email', form.get('email') || '');
  body.append('no_hp', form.get('no_hp') || '');
  body.append('jurusan_id', document.getElementById('create-jurusan').value);
  body.append('angkatan', form.get('angkatan') || '');
  body.append('jenis_kelamin', form.get('jenis_kelamin') || '');
  body.append('tempat_lahir', form.get('tempat_lahir') || '');
  body.append('tanggal_lahir', form.get('tanggal_lahir') || '');
  body.append('alamat', form.get('alamat') || '');
  body.append('status', form.get('status'));

  fetch('/admin/peserta', {
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
function openEdit(id, nim, nama, email, jurusanId, angkatan, jenisKelamin, tempatLahir, tanggalLahir, noHp, alamat, status){
  document.getElementById('edit-id').value = id;
  document.getElementById('edit-nim').value = nim;
  document.getElementById('edit-nama').value = nama;
  document.getElementById('edit-email').value = email || '';
  document.getElementById('edit-no_hp').value = noHp || '';
  document.getElementById('edit-jurusan').value = jurusanId ?? '';
  document.getElementById('edit-angkatan').value = angkatan ?? '';
  document.getElementById('edit-jenis_kelamin').value = jenisKelamin || '';
  document.getElementById('edit-tempat_lahir').value = tempatLahir || '';
  document.getElementById('edit-tanggal_lahir').value = tanggalLahir || '';
  document.getElementById('edit-alamat').value = alamat || '';
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
  body.append('nim', document.getElementById('edit-nim').value);
  body.append('nama', document.getElementById('edit-nama').value);
  body.append('email', document.getElementById('edit-email').value);
  body.append('no_hp', document.getElementById('edit-no_hp').value);
  body.append('jurusan_id', document.getElementById('edit-jurusan').value);
  body.append('angkatan', document.getElementById('edit-angkatan').value);
  body.append('jenis_kelamin', document.getElementById('edit-jenis_kelamin').value);
  body.append('tempat_lahir', document.getElementById('edit-tempat_lahir').value);
  body.append('tanggal_lahir', document.getElementById('edit-tanggal_lahir').value);
  body.append('alamat', document.getElementById('edit-alamat').value);
  body.append('status', document.getElementById('edit-status').value);

  fetch(`/admin/peserta/${id}`, {
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
  fetch(`/admin/peserta/${deleteId}`, {
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
  ['nim','nama','email','no_hp','jurusan_id','angkatan','jenis_kelamin','tempat_lahir','tanggal_lahir','alamat','status'].forEach(f=>{
    const el = document.getElementById(`err-${prefix}-${f}`);
    if(el){ el.textContent=''; el.classList.add('hidden'); }
  });
}

@if(session('success'))document.addEventListener('DOMContentLoaded',()=>showToast('success','{{ session("success") }}'));@endif
@if(session('error'))document.addEventListener('DOMContentLoaded',()=>showToast('error','{{ session("error") }}'));@endif
</script>
@endpush
