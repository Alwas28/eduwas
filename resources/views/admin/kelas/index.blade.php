@extends('layouts.admin')
@section('title','Kelas')
@section('page-title','Kelas')

@push('styles')
@include('admin.partials.datatable-styles')
<style>
.ins-list { max-height: 180px; overflow-y: auto; scrollbar-width: thin; }
.ins-list::-webkit-scrollbar { width: 4px; }
.ins-list::-webkit-scrollbar-thumb { background: var(--scrollbar); border-radius: 99px; }
.ins-item { display: flex; align-items: center; gap: 10px; padding: 7px 10px; border-radius: 10px; cursor: pointer; transition: background .12s; }
.ins-item:hover { background: var(--surface2); }
.ins-item input[type=checkbox] { accent-color: var(--ac); width: 15px; height: 15px; flex-shrink: 0; }
</style>
@endpush

@section('content')
<div id="toast-container" class="toast-wrap"></div>

{{-- Header --}}
<div class="flex items-center justify-between animate-fadeUp">
  <div>
    <h2 class="font-display font-bold text-[20px]" style="color:var(--text)">Kelas</h2>
    <p class="text-[13px] mt-0.5" style="color:var(--muted)">Kelola kelas per mata kuliah dan periode akademik</p>
  </div>
  @canaccess('tambah.kelas')
  <button onclick="openModal('modal-create')"
    class="flex items-center gap-2 px-4 py-2 rounded-xl text-[13px] font-semibold text-white a-grad shadow">
    <i class="fa-solid fa-plus text-[11px]"></i> Tambah Kelas
  </button>
  @endcanaccess
</div>

{{-- Stat Cards --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 animate-fadeUp d1">
  <div class="rounded-2xl p-5 border flex items-center gap-4" style="background:var(--surface);border-color:var(--border)">
    <div class="a-bg-lt a-text w-11 h-11 rounded-xl grid place-items-center text-lg flex-shrink-0"><i class="fa-solid fa-door-open"></i></div>
    <div>
      <div class="font-display text-[26px] font-bold" style="color:var(--text)">{{ $stats['total'] }}</div>
      <div class="text-[12px]" style="color:var(--muted)">Total Kelas</div>
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
    <div class="bg-blue-500/15 text-blue-400 w-11 h-11 rounded-xl grid place-items-center text-lg flex-shrink-0"><i class="fa-solid fa-flag-checkered"></i></div>
    <div>
      <div class="font-display text-[26px] font-bold" style="color:var(--text)">{{ $stats['selesai'] }}</div>
      <div class="text-[12px]" style="color:var(--muted)">Selesai</div>
    </div>
  </div>
  <div class="rounded-2xl p-5 border flex items-center gap-4" style="background:var(--surface);border-color:var(--border)">
    <div class="bg-rose-500/15 text-rose-400 w-11 h-11 rounded-xl grid place-items-center text-lg flex-shrink-0"><i class="fa-solid fa-ban"></i></div>
    <div>
      <div class="font-display text-[26px] font-bold" style="color:var(--text)">{{ $stats['dibatalkan'] }}</div>
      <div class="text-[12px]" style="color:var(--muted)">Dibatalkan</div>
    </div>
  </div>
</div>

{{-- Table --}}
<div class="rounded-2xl overflow-hidden border animate-fadeUp d2" style="background:var(--surface);border-color:var(--border)">
  <div class="flex items-center justify-between px-5 py-4 border-b" style="border-color:var(--border)">
    <span class="font-display font-semibold text-[15px]" style="color:var(--text)">Daftar Kelas</span>
    <span class="text-[12px] px-2.5 py-1 rounded-full a-bg-lt a-text font-semibold">{{ $stats['total'] }} kelas</span>
  </div>
  <div class="p-5">
    <table id="kelas-table" class="w-full" style="width:100%">
      <thead>
        <tr>
          <th>#</th>
          <th>Kelas</th>
          <th>Mata Kuliah</th>
          <th>Periode</th>
          <th>Instruktur</th>
          <th>Kapasitas</th>
          <th>Status</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        @foreach($kelas as $i => $kel)
        @php
          $kodeDisplay = $kel->mataKuliah?->kode ?? '?';
          if ($kel->kode_seksi) $kodeDisplay .= '-' . $kel->kode_seksi;
        @endphp
        <tr>
          <td class="text-center" style="color:var(--muted);width:48px">{{ $i + 1 }}</td>
          <td>
            <code class="text-[12px] font-bold px-2 py-0.5 rounded-lg a-bg-lt a-text" style="font-family:monospace">{{ $kodeDisplay }}</code>
          </td>
          <td>
            <div>
              <div class="font-semibold text-[13.5px]" style="color:var(--text)">{{ $kel->mataKuliah?->nama ?? '—' }}</div>
              @if($kel->mataKuliah?->jurusan)
                <div class="text-[11.5px]" style="color:var(--muted)">{{ $kel->mataKuliah->jurusan->nama }}</div>
              @endif
            </div>
          </td>
          <td>
            @if($kel->periodeAkademik)
              <div>
                <div class="text-[13px] font-medium" style="color:var(--text)">{{ $kel->periodeAkademik->nama }}</div>
                <div class="text-[11px]" style="color:var(--muted)">{{ $kel->periodeAkademik->tahun_ajaran }} · {{ $kel->periodeAkademik->semester }}</div>
              </div>
            @else
              <span style="color:var(--muted)">—</span>
            @endif
          </td>
          <td>
            @if($kel->instruktur->isEmpty())
              <span class="text-[12px]" style="color:var(--muted)">Belum ditugaskan</span>
            @else
              <div class="flex flex-wrap gap-1">
                @foreach($kel->instruktur->take(2) as $ins)
                  <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[11px] font-medium a-bg-lt a-text">{{ $ins->nama }}</span>
                @endforeach
                @if($kel->instruktur->count() > 2)
                  <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[11px] font-medium border" style="border-color:var(--border);color:var(--muted)">
                    +{{ $kel->instruktur->count() - 2 }} lagi
                  </span>
                @endif
              </div>
            @endif
          </td>
          <td class="text-center">
            @if($kel->kapasitas)
              <span class="text-[13px] font-semibold" style="color:var(--text)">{{ $kel->kapasitas }}</span>
            @else
              <span style="color:var(--muted)">—</span>
            @endif
          </td>
          <td>
            @php
              $stColor = match($kel->status) {
                'Aktif'      => 'bg-emerald-500/15 text-emerald-400',
                'Selesai'    => 'bg-blue-500/15 text-blue-400',
                'Dibatalkan' => 'bg-rose-500/15 text-rose-400',
                default      => 'bg-slate-500/15 text-slate-400',
              };
            @endphp
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold {{ $stColor }}">
              <i class="fa-solid fa-circle text-[7px]"></i> {{ $kel->status }}
            </span>
          </td>
          <td>
            <div class="flex items-center gap-2">
              <button onclick="openQR({{ json_encode($kodeDisplay) }},{{ json_encode($kel->enroll_token) }})"
                class="w-8 h-8 rounded-lg grid place-items-center text-[12px] border transition-colors"
                style="border-color:var(--border);color:var(--sub)"
                onmouseover="this.style.borderColor='var(--ac)';this.style.color='var(--ac)'"
                onmouseout="this.style.borderColor='var(--border)';this.style.color='var(--sub)'"
                title="QR Enroll Mandiri">
                <i class="fa-solid fa-qrcode"></i>
              </button>
              @canaccess('edit.kelas')
              <button onclick="openEdit({{ $kel->id }},{{ $kel->mata_kuliah_id }},{{ $kel->periode_akademik_id }},{{ json_encode($kel->kode_seksi) }},{{ $kel->kapasitas ?? 'null' }},{{ json_encode($kel->status) }},{{ json_encode($kel->instruktur->pluck('id')->values()->all()) }})"
                class="w-8 h-8 rounded-lg grid place-items-center text-[12px] border transition-colors"
                style="border-color:var(--border);color:var(--sub)"
                onmouseover="this.style.borderColor='var(--ac)';this.style.color='var(--ac)'"
                onmouseout="this.style.borderColor='var(--border)';this.style.color='var(--sub)'">
                <i class="fa-solid fa-pen"></i>
              </button>
              @endcanaccess
              @canaccess('hapus.kelas')
              <button onclick="openDelete({{ $kel->id }},{{ json_encode($kodeDisplay) }})"
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
        <div class="a-bg-lt a-text w-10 h-10 rounded-xl grid place-items-center"><i class="fa-solid fa-door-open"></i></div>
        <h3 class="font-display font-bold text-[16px]" style="color:var(--text)">Tambah Kelas</h3>
      </div>
      <button onclick="closeModal('modal-create')" class="w-8 h-8 rounded-lg grid place-items-center" style="color:var(--muted)"
        onmouseover="this.style.background='var(--surface2)'" onmouseout="this.style.background='transparent'">
        <i class="fa-solid fa-xmark"></i>
      </button>
    </div>
    <form id="form-create" class="px-6 py-5 space-y-4 overflow-y-auto" style="max-height:72vh">

      {{-- Mata Kuliah --}}
      <div>
        <label class="f-label">Mata Kuliah <span class="text-red-400">*</span></label>
        @if($mataKuliah->isEmpty())
          <div class="rounded-lg p-3 text-[12.5px] border" style="background:var(--surface2);border-color:var(--border);color:var(--muted)">
            <i class="fa-solid fa-circle-exclamation mr-1.5 text-amber-400"></i>
            Belum ada mata kuliah aktif. <a href="{{ route('admin.matakuliah.index') }}" class="a-text underline">Tambah dulu</a>.
          </div>
        @else
          <select name="mata_kuliah_id" id="create-mk" class="f-input">
            <option value="">— Pilih Mata Kuliah —</option>
            @foreach($mataKuliah as $mk)
              <option value="{{ $mk->id }}">{{ $mk->kode }} — {{ $mk->nama }}
                @if($mk->jurusan) ({{ $mk->jurusan->nama }})@endif
              </option>
            @endforeach
          </select>
        @endif
        <p class="f-error hidden" id="err-create-mata_kuliah_id"></p>
      </div>

      {{-- Periode --}}
      <div>
        <label class="f-label">Periode Akademik <span class="text-red-400">*</span></label>
        <select name="periode_akademik_id" id="create-periode" class="f-input">
          <option value="">— Pilih Periode —</option>
          @foreach($periodes as $p)
            <option value="{{ $p->id }}" {{ $p->status === 'Aktif' ? 'selected' : '' }}>
              {{ $p->nama }} — {{ $p->tahun_ajaran }} {{ $p->semester }}
              @if($p->status === 'Aktif') ★@endif
            </option>
          @endforeach
        </select>
        <p class="f-error hidden" id="err-create-periode_akademik_id"></p>
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="f-label">Kode Seksi</label>
          <input type="text" name="kode_seksi" class="f-input" placeholder="cth: A, B, Reguler" style="text-transform:uppercase">
          <p class="f-hint">Kosongkan jika hanya 1 kelas</p>
          <p class="f-error hidden" id="err-create-kode_seksi"></p>
        </div>
        <div>
          <label class="f-label">Kapasitas</label>
          <input type="number" name="kapasitas" class="f-input" placeholder="cth: 40" min="1" max="999">
          <p class="f-hint">Kosongkan jika tidak dibatasi</p>
          <p class="f-error hidden" id="err-create-kapasitas"></p>
        </div>
      </div>

      <div>
        <label class="f-label">Status <span class="text-red-400">*</span></label>
        <select name="status" class="f-input">
          <option value="Aktif" selected>Aktif</option>
          <option value="Selesai">Selesai</option>
          <option value="Dibatalkan">Dibatalkan</option>
        </select>
        <p class="f-error hidden" id="err-create-status"></p>
      </div>

      {{-- Instruktur --}}
      <div>
        <label class="f-label">Instruktur Pengajar</label>
        @if($instruktur->isEmpty())
          <div class="rounded-lg p-3 text-[12.5px] border" style="background:var(--surface2);border-color:var(--border);color:var(--muted)">
            <i class="fa-solid fa-circle-exclamation mr-1.5 text-amber-400"></i>
            Belum ada instruktur aktif. <a href="{{ route('admin.instruktur.index') }}" class="a-text underline">Tambah dulu</a>.
          </div>
        @else
          <div class="ins-list rounded-xl border p-2" style="border-color:var(--border)">
            @foreach($instruktur as $ins)
            <label class="ins-item">
              <input type="checkbox" class="create-ins-cb" value="{{ $ins->id }}">
              <div class="flex-1 min-w-0">
                <div class="text-[13px] font-medium truncate" style="color:var(--text)">{{ $ins->nama }}</div>
                @if($ins->nidn || $ins->bidang_keahlian)
                  <div class="text-[11px] truncate" style="color:var(--muted)">
                    {{ $ins->nidn ?? '' }}{{ $ins->nidn && $ins->bidang_keahlian ? ' · ' : '' }}{{ $ins->bidang_keahlian ?? '' }}
                  </div>
                @endif
              </div>
              @if($ins->pendidikan_terakhir)
                <span class="text-[10px] font-bold px-1.5 py-0.5 rounded a-bg-lt a-text flex-shrink-0">{{ $ins->pendidikan_terakhir }}</span>
              @endif
            </label>
            @endforeach
          </div>
          <p class="f-hint">Pilih satu atau lebih instruktur (team teaching diperbolehkan)</p>
        @endif
        <p class="f-error hidden" id="err-create-instruktur_ids"></p>
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
        <h3 class="font-display font-bold text-[16px]" style="color:var(--text)">Edit Kelas</h3>
      </div>
      <button onclick="closeModal('modal-edit')" class="w-8 h-8 rounded-lg grid place-items-center" style="color:var(--muted)"
        onmouseover="this.style.background='var(--surface2)'" onmouseout="this.style.background='transparent'">
        <i class="fa-solid fa-xmark"></i>
      </button>
    </div>
    <form id="form-edit" class="px-6 py-5 space-y-4 overflow-y-auto" style="max-height:72vh">
      <input type="hidden" id="edit-id">

      <div>
        <label class="f-label">Mata Kuliah <span class="text-red-400">*</span></label>
        <select name="mata_kuliah_id" id="edit-mk" class="f-input">
          <option value="">— Pilih Mata Kuliah —</option>
          @foreach($mataKuliah as $mk)
            <option value="{{ $mk->id }}">{{ $mk->kode }} — {{ $mk->nama }}
              @if($mk->jurusan) ({{ $mk->jurusan->nama }})@endif
            </option>
          @endforeach
        </select>
        <p class="f-error hidden" id="err-edit-mata_kuliah_id"></p>
      </div>

      <div>
        <label class="f-label">Periode Akademik <span class="text-red-400">*</span></label>
        <select name="periode_akademik_id" id="edit-periode" class="f-input">
          <option value="">— Pilih Periode —</option>
          @foreach($periodes as $p)
            <option value="{{ $p->id }}">
              {{ $p->nama }} — {{ $p->tahun_ajaran }} {{ $p->semester }}
              @if($p->status === 'Aktif') ★@endif
            </option>
          @endforeach
        </select>
        <p class="f-error hidden" id="err-edit-periode_akademik_id"></p>
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="f-label">Kode Seksi</label>
          <input type="text" name="kode_seksi" id="edit-kode-seksi" class="f-input" style="text-transform:uppercase">
          <p class="f-error hidden" id="err-edit-kode_seksi"></p>
        </div>
        <div>
          <label class="f-label">Kapasitas</label>
          <input type="number" name="kapasitas" id="edit-kapasitas" class="f-input" min="1" max="999">
          <p class="f-error hidden" id="err-edit-kapasitas"></p>
        </div>
      </div>

      <div>
        <label class="f-label">Status <span class="text-red-400">*</span></label>
        <select name="status" id="edit-status" class="f-input">
          <option value="Aktif">Aktif</option>
          <option value="Selesai">Selesai</option>
          <option value="Dibatalkan">Dibatalkan</option>
        </select>
        <p class="f-error hidden" id="err-edit-status"></p>
      </div>

      {{-- Instruktur --}}
      <div>
        <label class="f-label">Instruktur Pengajar</label>
        @if($instruktur->isEmpty())
          <div class="rounded-lg p-3 text-[12.5px] border" style="background:var(--surface2);border-color:var(--border);color:var(--muted)">
            Belum ada instruktur aktif.
          </div>
        @else
          <div class="ins-list rounded-xl border p-2" style="border-color:var(--border)">
            @foreach($instruktur as $ins)
            <label class="ins-item">
              <input type="checkbox" class="edit-ins-cb" value="{{ $ins->id }}">
              <div class="flex-1 min-w-0">
                <div class="text-[13px] font-medium truncate" style="color:var(--text)">{{ $ins->nama }}</div>
                @if($ins->nidn || $ins->bidang_keahlian)
                  <div class="text-[11px] truncate" style="color:var(--muted)">
                    {{ $ins->nidn ?? '' }}{{ $ins->nidn && $ins->bidang_keahlian ? ' · ' : '' }}{{ $ins->bidang_keahlian ?? '' }}
                  </div>
                @endif
              </div>
              @if($ins->pendidikan_terakhir)
                <span class="text-[10px] font-bold px-1.5 py-0.5 rounded a-bg-lt a-text flex-shrink-0">{{ $ins->pendidikan_terakhir }}</span>
              @endif
            </label>
            @endforeach
          </div>
          <p class="f-hint">Pilih satu atau lebih instruktur (team teaching diperbolehkan)</p>
        @endif
        <p class="f-error hidden" id="err-edit-instruktur_ids"></p>
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

{{-- MODAL QR CODE --}}
<div id="modal-qr" class="modal-backdrop">
  <div class="modal-box modal-sm">
    <div class="flex items-center justify-between px-6 py-5 border-b" style="border-color:var(--border)">
      <div class="flex items-center gap-3">
        <div class="a-bg-lt a-text w-10 h-10 rounded-xl grid place-items-center"><i class="fa-solid fa-qrcode"></i></div>
        <div>
          <h3 class="font-display font-bold text-[15px]" style="color:var(--text)">QR Enroll Mandiri</h3>
          <p id="qr-kelas-name" class="text-[12px]" style="color:var(--muted)"></p>
        </div>
      </div>
      <button onclick="closeModal('modal-qr')" class="w-8 h-8 rounded-lg grid place-items-center" style="color:var(--muted)"
        onmouseover="this.style.background='var(--surface2)'" onmouseout="this.style.background='transparent'">
        <i class="fa-solid fa-xmark"></i>
      </button>
    </div>
    <div class="p-6 flex flex-col items-center gap-4">
      <div id="qr-code-container" class="p-4 rounded-2xl bg-white shadow-inner"></div>
      <p class="text-[12px] text-center max-w-xs" style="color:var(--muted)">
        Mahasiswa dapat scan QR ini untuk self-enroll ke kelas. Link berlaku selama token tidak direset.
      </p>
      <div class="w-full rounded-xl border px-3 py-2 flex items-center gap-2" style="border-color:var(--border);background:var(--surface2)">
        <span id="qr-url-display" class="flex-1 text-[11.5px] truncate" style="color:var(--muted);font-family:monospace"></span>
        <button onclick="copyQrUrl()" class="text-[11px] font-semibold a-text flex-shrink-0 px-2 py-1 rounded-lg a-bg-lt">
          <i class="fa-regular fa-copy mr-1"></i>Salin
        </button>
      </div>
    </div>
  </div>
</div>

{{-- MODAL HAPUS --}}
<div id="modal-delete" class="modal-backdrop">
  <div class="modal-box modal-sm">
    <div class="p-6 text-center">
      <div class="bg-rose-500/15 text-rose-400 w-14 h-14 rounded-2xl grid place-items-center text-2xl mx-auto mb-4"><i class="fa-solid fa-trash-can"></i></div>
      <h3 class="font-display font-bold text-[16px] mb-1" style="color:var(--text)">Hapus Kelas?</h3>
      <p class="text-[12.5px]" style="color:var(--muted)">Kelas <strong id="delete-name" style="color:var(--text)"></strong> akan dihapus permanen beserta data instrukturnya.</p>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
$(()=>$('#kelas-table').DataTable({
  language:{...DT_LANG, searchPlaceholder:'Cari kelas, mata kuliah...'},
  columnDefs:[
    {orderable:false, targets:[0,4,7]},
    {className:'text-center', targets:[0,5]},
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
  body.append('mata_kuliah_id', document.getElementById('create-mk').value);
  body.append('periode_akademik_id', document.getElementById('create-periode').value);
  body.append('kode_seksi', form.get('kode_seksi') || '');
  body.append('kapasitas', form.get('kapasitas') || '');
  body.append('status', form.get('status'));
  document.querySelectorAll('.create-ins-cb:checked').forEach(cb => {
    body.append('instruktur_ids[]', cb.value);
  });

  fetch('/admin/kelas', {
    method: 'POST',
    headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json'},
    body,
  })
  .then(async r => ({ok: r.ok, status: r.status, data: await r.json()}))
  .then(({ok, status, data}) => {
    if (!ok && status === 422) { showErrors('create', data.errors); return; }
    closeModal('modal-create');
    showToast(ok ? 'success' : 'error', data.message);
    if (ok) setTimeout(() => location.reload(), 1200);
  })
  .catch(() => showToast('error', 'Gagal terhubung ke server.'))
  .finally(() => setLoading('btn-create', false));
});

// EDIT
let deleteId = null;
function openEdit(id, mkId, periodeId, kodeSeksi, kapasitas, status, instrukturIds) {
  document.getElementById('edit-id').value = id;
  document.getElementById('edit-mk').value = mkId;
  document.getElementById('edit-periode').value = periodeId;
  document.getElementById('edit-kode-seksi').value = kodeSeksi || '';
  document.getElementById('edit-kapasitas').value = kapasitas ?? '';
  document.getElementById('edit-status').value = status;
  document.querySelectorAll('.edit-ins-cb').forEach(cb => {
    cb.checked = instrukturIds.includes(parseInt(cb.value));
  });
  clearErrors('edit');
  openModal('modal-edit');
}

document.getElementById('form-edit').addEventListener('submit', function(e){
  e.preventDefault();
  clearErrors('edit');
  const id = document.getElementById('edit-id').value;
  setLoading('btn-edit', true);
  const body = new URLSearchParams({_method: 'PUT'});
  body.append('mata_kuliah_id', document.getElementById('edit-mk').value);
  body.append('periode_akademik_id', document.getElementById('edit-periode').value);
  body.append('kode_seksi', document.getElementById('edit-kode-seksi').value);
  body.append('kapasitas', document.getElementById('edit-kapasitas').value);
  body.append('status', document.getElementById('edit-status').value);
  document.querySelectorAll('.edit-ins-cb:checked').forEach(cb => {
    body.append('instruktur_ids[]', cb.value);
  });

  fetch(`/admin/kelas/${id}`, {
    method: 'POST',
    headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json'},
    body,
  })
  .then(async r => ({ok: r.ok, status: r.status, data: await r.json()}))
  .then(({ok, status, data}) => {
    if (!ok && status === 422) { showErrors('edit', data.errors); return; }
    closeModal('modal-edit');
    showToast(ok ? 'success' : 'error', data.message);
    if (ok) setTimeout(() => location.reload(), 1200);
  })
  .catch(() => showToast('error', 'Gagal terhubung ke server.'))
  .finally(() => setLoading('btn-edit', false));
});

// DELETE
function openDelete(id, nama) {
  deleteId = id;
  document.getElementById('delete-name').textContent = nama;
  openModal('modal-delete');
}
function doDelete() {
  setLoading('btn-delete', true);
  fetch(`/admin/kelas/${deleteId}`, {
    method: 'POST',
    headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json'},
    body: new URLSearchParams({_method: 'DELETE'}),
  })
  .then(async r => ({ok: r.ok, data: await r.json()}))
  .then(({ok, data}) => {
    closeModal('modal-delete');
    showToast(ok ? 'success' : 'error', data.message);
    if (ok) setTimeout(() => location.reload(), 1200);
  })
  .catch(() => showToast('error', 'Gagal terhubung ke server.'))
  .finally(() => setLoading('btn-delete', false));
}

// QR CODE
let currentQrUrl = '';
let qrInstance = null;

function openQR(kodeDisplay, enrollToken) {
  document.getElementById('qr-kelas-name').textContent = kodeDisplay;
  const url = `${location.origin}/enroll/${enrollToken}`;
  currentQrUrl = url;
  document.getElementById('qr-url-display').textContent = url;

  const container = document.getElementById('qr-code-container');
  container.innerHTML = '';
  qrInstance = new QRCode(container, {
    text: url,
    width: 200,
    height: 200,
    colorDark: '#000000',
    colorLight: '#ffffff',
    correctLevel: QRCode.CorrectLevel.M,
  });
  openModal('modal-qr');
}

function copyQrUrl() {
  navigator.clipboard.writeText(currentQrUrl).then(() => {
    showToast('success', 'Link berhasil disalin!');
  }).catch(() => {
    showToast('error', 'Gagal menyalin link.');
  });
}

function clearErrors(prefix) {
  ['mata_kuliah_id','periode_akademik_id','kode_seksi','kapasitas','status','instruktur_ids'].forEach(f => {
    const el = document.getElementById(`err-${prefix}-${f}`);
    if (el) { el.textContent = ''; el.classList.add('hidden'); }
  });
}

@if(session('success'))document.addEventListener('DOMContentLoaded',()=>showToast('success','{{ session("success") }}'));@endif
@if(session('error'))document.addEventListener('DOMContentLoaded',()=>showToast('error','{{ session("error") }}'));@endif
</script>
@endpush
