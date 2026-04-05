@extends('layouts.admin')
@section('title','Kelola Access — '.$role->display_name)
@section('page-title','Role Access')

@push('styles')
@include('admin.partials.datatable-styles')
<style>
/* Layout */
.ra-layout { display: flex; gap: 20px; align-items: flex-start; }
.ra-sidebar {
  width: 220px; flex-shrink: 0;
  position: sticky; top: 80px;
  border-radius: 16px; border: 1px solid var(--border);
  background: var(--surface); overflow: hidden;
}
.ra-content { flex: 1; min-width: 0; }

/* Sidebar nav */
.ra-nav-item {
  display: flex; align-items: center; justify-content: space-between;
  padding: 8px 14px; cursor: pointer;
  font-size: 12.5px; font-weight: 600; color: var(--sub);
  border-left: 3px solid transparent;
  transition: all .15s;
}
.ra-nav-item:hover { background: var(--surface2); color: var(--text); }
.ra-nav-item.active { color: var(--ac); border-left-color: var(--ac); background: rgba(var(--ac-rgb),.06); }
.ra-nav-badge {
  font-size: 10px; font-weight: 700;
  padding: 1px 6px; border-radius: 999px;
  background: var(--surface2); color: var(--muted);
  transition: all .15s;
}
.ra-nav-item.active .ra-nav-badge { background: rgba(var(--ac-rgb),.15); color: var(--ac); }

/* Access group card */
.access-group-card {
  border-radius: 16px; border: 1px solid var(--border);
  background: var(--surface); overflow: hidden; margin-bottom: 16px;
}
.access-group-header {
  display: flex; align-items: center; justify-content: space-between;
  padding: 14px 20px; border-bottom: 1px solid var(--border);
}

/* Checkbox item */
.ac-item {
  display: flex; align-items: flex-start; gap: 10px;
  padding: 10px 12px; border-radius: 10px; cursor: pointer;
  border: 1px solid transparent; transition: all .15s;
}
.ac-item:hover { background: var(--surface2); }
.ac-item.checked {
  background: rgba(var(--ac-rgb),.06);
  border-color: rgba(var(--ac-rgb),.25);
}
.ac-item input[type=checkbox] { display: none; }
.ac-box {
  width: 18px; height: 18px; border-radius: 5px; flex-shrink: 0; margin-top: 2px;
  border: 1.5px solid var(--border); background: var(--surface2);
  display: grid; place-items: center; transition: all .15s;
}
.ac-item.checked .ac-box {
  background: var(--ac); border-color: var(--ac); color: #fff;
}
.ac-item.checked .ac-box::after { content: '✓'; font-size: 11px; font-weight: 700; color: #fff; }
.ac-item:not(.checked) .ac-box::after { content: ''; }

/* Sticky save bar */
.save-bar {
  position: sticky; bottom: 0; z-index: 30;
  background: var(--surface); border-top: 1px solid var(--border);
  padding: 12px 20px;
  display: flex; align-items: center; justify-between;
  gap: 12px; border-radius: 0 0 16px 16px;
  box-shadow: 0 -4px 16px rgba(0,0,0,.06);
}

/* Search highlight */
.ac-item.hidden-item { display: none; }

@media (max-width: 768px) {
  .ra-layout { flex-direction: column; }
  .ra-sidebar { width: 100%; position: static; }
}
</style>
@endpush

@section('content')
<div id="toast-container" class="toast-wrap"></div>

{{-- Breadcrumb + Header --}}
<div class="animate-fadeUp">
  <div class="flex items-center gap-2 text-[12px] mb-3" style="color:var(--muted)">
    <a href="{{ route('admin.role-access.index') }}" class="hover:underline a-text">Role Access</a>
    <i class="fa-solid fa-chevron-right text-[9px]"></i>
    <span style="color:var(--text)">{{ $role->display_name }}</span>
  </div>

  <div class="flex items-center justify-between flex-wrap gap-3 mb-5">
    <div class="flex items-center gap-3">
      @php $roleColors=['bg-blue-500/15 text-blue-400','bg-violet-500/15 text-violet-400','bg-emerald-500/15 text-emerald-400','bg-amber-500/15 text-amber-400','bg-rose-500/15 text-rose-400'];
      $rc=$roleColors[$role->id % count($roleColors)]; @endphp
      <div class="{{ $rc }} w-12 h-12 rounded-xl grid place-items-center text-[17px] font-bold flex-shrink-0">{{ strtoupper(substr($role->display_name,0,1)) }}</div>
      <div>
        <h2 class="font-display font-bold text-[20px]" style="color:var(--text)">{{ $role->display_name }}</h2>
        <div class="flex items-center gap-3 mt-0.5">
          <code class="text-[11.5px]" style="color:var(--muted);font-family:monospace">{{ $role->name }}</code>
          <span id="hdr-count" class="text-[11.5px] a-text font-semibold">0 access dipilih</span>
        </div>
      </div>
    </div>
    <div class="flex items-center gap-2">
      <div class="flex items-center gap-2 rounded-lg px-3 py-2 border" style="background:var(--surface);border-color:var(--border)">
        <i class="fa-solid fa-magnifying-glass text-[12px]" style="color:var(--muted)"></i>
        <input id="global-search" type="text" placeholder="Cari access..." class="bg-transparent outline-none text-[13px]" style="color:var(--text);width:180px" oninput="doSearch(this.value)">
        <button type="button" onclick="clearSearch()" id="btn-clear-search" class="hidden" style="color:var(--muted)"><i class="fa-solid fa-xmark text-[11px]"></i></button>
      </div>
      <a href="{{ route('admin.role-access.index') }}" class="px-4 py-2 rounded-xl border text-[13px] font-semibold transition-colors" style="border-color:var(--border);color:var(--sub)" onmouseover="this.style.background='var(--surface2)'" onmouseout="this.style.background='transparent'">Kembali</a>
      <button type="button" id="btn-save-top" onclick="doSave()" class="px-5 py-2 rounded-xl text-[13px] font-semibold text-white a-grad shadow">
        <i class="fa-solid fa-floppy-disk mr-1.5 text-[11px]"></i>Simpan
      </button>
    </div>
  </div>
</div>

@if($accesses->isEmpty())
<div class="rounded-2xl p-12 text-center border" style="background:var(--surface);border-color:var(--border)">
  <i class="fa-solid fa-key text-4xl mb-3 block a-text opacity-40"></i>
  <p class="text-[13px]" style="color:var(--muted)">Belum ada access tersedia. Buat access terlebih dahulu.</p>
</div>
@else

{{-- Two-column layout --}}
<div class="ra-layout animate-fadeUp d1">

  {{-- Sidebar: Group Navigation --}}
  <div class="ra-sidebar">
    <div class="px-4 py-3 border-b" style="border-color:var(--border)">
      <div class="text-[10.5px] font-bold uppercase tracking-[1px]" style="color:var(--muted)">Group Access</div>
    </div>
    {{-- All --}}
    <div class="ra-nav-item active" id="nav-all" onclick="filterGroup('all')">
      <span>Semua</span>
      <span class="ra-nav-badge" id="nav-badge-all">{{ $accesses->count() }}</span>
    </div>
    @foreach($groups as $group)
    @php $gCount = $accesses->where('group',$group)->count(); @endphp
    <div class="ra-nav-item" id="nav-{{ Str::slug($group) }}" onclick="filterGroup('{{ Str::slug($group) }}')">
      <span class="truncate" title="{{ $group }}">{{ $group }}</span>
      <span class="ra-nav-badge" id="nav-badge-{{ Str::slug($group) }}">{{ $gCount }}</span>
    </div>
    @endforeach
    {{-- Summary --}}
    <div class="px-4 py-3 border-t mt-1" style="border-color:var(--border)">
      <div class="text-[10.5px] font-bold uppercase tracking-[1px] mb-2" style="color:var(--muted)">Ringkasan</div>
      @php $total = $accesses->count(); @endphp
      <div class="h-1.5 rounded-full overflow-hidden mb-1.5" style="background:var(--border)">
        <div class="h-full rounded-full a-grad" id="sidebar-progress" style="width:0%"></div>
      </div>
      <div class="text-[11px]" style="color:var(--muted)"><span id="sidebar-selected">0</span>/{{ $total }} dipilih</div>
    </div>
  </div>

  {{-- Content: Grouped Access --}}
  <div class="ra-content">
    @foreach($groups as $group)
    @php
      $groupAccesses = $accesses->where('group', $group)->values();
      $slug = Str::slug($group);
    @endphp
    <div class="access-group-card" id="grp-{{ $slug }}" data-group="{{ $slug }}">
      <div class="access-group-header">
        <div class="flex items-center gap-2">
          <span class="font-display font-bold text-[13.5px]" style="color:var(--text)">{{ $group }}</span>
          <span class="text-[10.5px] px-2 py-0.5 rounded-full font-semibold" style="background:var(--surface2);color:var(--muted)" id="grp-count-{{ $slug }}">{{ $groupAccesses->count() }} item</span>
        </div>
        <div class="flex items-center gap-3">
          <div class="flex items-center gap-1.5 text-[11.5px]" style="color:var(--muted)">
            <span id="grp-checked-{{ $slug }}">0</span>/{{ $groupAccesses->count() }} dipilih
          </div>
          <button type="button" onclick="toggleGroup('{{ $slug }}')" id="btn-grp-{{ $slug }}"
            class="text-[11.5px] font-semibold a-text flex items-center gap-1.5 px-2.5 py-1 rounded-lg transition-colors"
            style="background:rgba(var(--ac-rgb),.08)">
            <i class="fa-solid fa-check-double text-[10px]"></i>
            <span id="btn-grp-label-{{ $slug }}">Pilih Semua</span>
          </button>
        </div>
      </div>

      <div class="p-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2" id="grp-items-{{ $slug }}">
        @foreach($groupAccesses as $ac)
        <label class="ac-item {{ in_array($ac->id, $roleAccessIds) ? 'checked' : '' }}"
          data-group="{{ $slug }}"
          data-name="{{ strtolower($ac->display_name.' '.$ac->name) }}">
          <input type="checkbox" class="access-cb" value="{{ $ac->id }}" {{ in_array($ac->id, $roleAccessIds) ? 'checked' : '' }}>
          <div class="ac-box"></div>
          <div class="flex-1 min-w-0">
            <div class="text-[12.5px] font-semibold" style="color:var(--text)">{{ $ac->display_name }}</div>
            <code class="text-[10.5px] block truncate" style="color:var(--muted);font-family:monospace">{{ $ac->name }}</code>
          </div>
        </label>
        @endforeach
      </div>

      {{-- Empty state for search --}}
      <div id="grp-empty-{{ $slug }}" class="hidden py-6 text-center text-[12px]" style="color:var(--muted)">
        <i class="fa-solid fa-magnifying-glass mr-1.5 opacity-50"></i>Tidak ditemukan
      </div>
    </div>
    @endforeach
  </div>

</div>

{{-- Sticky bottom save bar --}}
<div class="save-bar animate-fadeUp d2 mt-4">
  <div class="flex items-center gap-3 flex-1">
    <div class="h-2 w-32 rounded-full overflow-hidden" style="background:var(--border)">
      <div class="h-full rounded-full a-grad transition-all" id="bar-progress" style="width:0%"></div>
    </div>
    <span class="text-[12px]" style="color:var(--muted)"><span id="bar-count">0</span>/{{ $accesses->count() }} access dipilih</span>
  </div>
  <div class="flex gap-3">
    <button type="button" onclick="selectAllGlobal()" id="btn-select-all" class="px-3 py-2 rounded-xl border text-[12px] font-semibold transition-colors" style="border-color:var(--border);color:var(--sub)" onmouseover="this.style.background='var(--surface2)'" onmouseout="this.style.background='transparent'">
      <i class="fa-solid fa-check-double text-[10px] mr-1"></i>Pilih Semua
    </button>
    <button type="button" id="btn-save" onclick="doSave()" class="px-5 py-2 rounded-xl text-[13px] font-semibold text-white a-grad shadow">
      <i class="fa-solid fa-floppy-disk mr-1.5 text-[11px]"></i>Simpan
    </button>
  </div>
</div>

@endif
@endsection

@push('scripts')
@include('admin.partials.datatable-scripts')
<script>
const TOTAL_ACCESS = {{ $accesses->count() }};
let currentGroup = 'all';
let allGlobalSelected = false;

// Init
document.addEventListener('DOMContentLoaded', () => {
  // Attach change events
  document.querySelectorAll('.access-cb').forEach(cb => {
    cb.addEventListener('change', function(){
      const label = this.closest('.ac-item');
      label.classList.toggle('checked', this.checked);
      updateCounts();
    });
  });
  updateCounts();
});

function updateCounts(){
  const total = document.querySelectorAll('.access-cb').length;
  const checked = document.querySelectorAll('.access-cb:checked').length;

  // Header count
  document.getElementById('hdr-count').textContent = `${checked} access dipilih`;

  // Progress bars
  const pct = total > 0 ? Math.round(checked/total*100) : 0;
  document.getElementById('bar-progress').style.width = pct+'%';
  document.getElementById('bar-count').textContent = checked;
  document.getElementById('sidebar-progress').style.width = pct+'%';
  document.getElementById('sidebar-selected').textContent = checked;

  // Per-group counts
  document.querySelectorAll('.access-group-card[data-group]').forEach(item => {
    const group = item.dataset.group;
    if(!group) return;
    const grpChecked = document.querySelectorAll(`.ac-item[data-group="${group}"] .access-cb:checked`).length;
    const grpTotal   = document.querySelectorAll(`.ac-item[data-group="${group}"] .access-cb`).length;
    const el = document.getElementById(`grp-checked-${group}`);
    if(el) el.textContent = grpChecked;
    // update toggle button label
    const btnLabel = document.getElementById(`btn-grp-label-${group}`);
    if(btnLabel) btnLabel.textContent = (grpChecked === grpTotal && grpTotal > 0) ? 'Hapus Semua' : 'Pilih Semua';
    // update nav badge
    const navBadge = document.getElementById(`nav-badge-${group}`);
    if(navBadge && currentGroup === 'all') navBadge.textContent = grpTotal;
  });

  // Global toggle btn
  allGlobalSelected = checked === total && total > 0;
  const btnSel = document.getElementById('btn-select-all');
  if(btnSel) btnSel.innerHTML = allGlobalSelected
    ? '<i class="fa-solid fa-xmark text-[10px] mr-1"></i>Hapus Semua'
    : '<i class="fa-solid fa-check-double text-[10px] mr-1"></i>Pilih Semua';
}

function toggleGroup(slug){
  const cbs = document.querySelectorAll(`.ac-item[data-group="${slug}"]:not(.hidden-item) .access-cb`);
  const anyChecked = [...cbs].some(cb => cb.checked);
  cbs.forEach(cb => {
    cb.checked = !anyChecked;
    cb.closest('.ac-item').classList.toggle('checked', !anyChecked);
  });
  updateCounts();
}

function selectAllGlobal(){
  allGlobalSelected = !allGlobalSelected;
  document.querySelectorAll('.access-cb').forEach(cb => {
    cb.checked = allGlobalSelected;
    cb.closest('.ac-item').classList.toggle('checked', allGlobalSelected);
  });
  updateCounts();
}

function filterGroup(group){
  currentGroup = group;
  // update nav active
  document.querySelectorAll('.ra-nav-item').forEach(el => el.classList.remove('active'));
  document.getElementById(`nav-${group}`)?.classList.add('active');
  // show/hide group cards
  document.querySelectorAll('.access-group-card').forEach(card => {
    if(group === 'all' || card.dataset.group === group){
      card.style.display = '';
    } else {
      card.style.display = 'none';
    }
  });
  // scroll to group
  if(group !== 'all'){
    const card = document.getElementById(`grp-${group}`);
    if(card) card.scrollIntoView({behavior:'smooth', block:'start'});
  }
}

function doSearch(q){
  q = q.trim().toLowerCase();
  const btnClear = document.getElementById('btn-clear-search');
  if(btnClear) btnClear.classList.toggle('hidden', q === '');

  document.querySelectorAll('.access-group-card').forEach(card => {
    const slug = card.dataset.group;
    const items = card.querySelectorAll('.ac-item');
    let visibleCount = 0;
    items.forEach(item => {
      const match = item.dataset.name?.includes(q) ?? true;
      item.classList.toggle('hidden-item', !match);
      if(match) visibleCount++;
    });
    // show/hide empty state
    const emptyEl = document.getElementById(`grp-empty-${slug}`);
    if(emptyEl) emptyEl.classList.toggle('hidden', visibleCount > 0);
    // hide whole card if no results and searching
    card.style.display = (q !== '' && visibleCount === 0) ? 'none' : '';
    // update count badge
    const countEl = document.getElementById(`grp-count-${slug}`);
    if(countEl) countEl.textContent = q !== '' ? `${visibleCount} ditemukan` : `${items.length} item`;
  });
}

function clearSearch(){
  const inp = document.getElementById('global-search');
  if(inp){ inp.value = ''; doSearch(''); inp.focus(); }
}

function doSave(){
  setLoading('btn-save', true);
  setLoading('btn-save-top', true);
  const checked = [...document.querySelectorAll('.access-cb:checked')].map(cb => cb.value);
  const body = new URLSearchParams({_method:'PUT'});
  checked.forEach(id => body.append('access_ids[]', id));

  fetch(`/admin/role-access/{{ $role->id }}`, {
    method: 'POST',
    headers: {'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'},
    body,
  })
  .then(async r => ({ok:r.ok, data:await r.json()}))
  .then(({ok, data}) => {
    showToast(ok ? 'success' : 'error', data.message);
  })
  .catch(() => showToast('error','Gagal terhubung ke server.'))
  .finally(() => {
    setLoading('btn-save', false);
    setLoading('btn-save-top', false);
  });
}

@if(session('success'))document.addEventListener('DOMContentLoaded',()=>showToast('success','{{ session("success") }}'));@endif
@if(session('error'))document.addEventListener('DOMContentLoaded',()=>showToast('error','{{ session("error") }}'));@endif
</script>

@endpush
