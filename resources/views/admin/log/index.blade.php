@extends('layouts.admin')
@section('title','Log Aktivitas')
@section('page-title','Log Aktivitas')

@push('styles')
@include('admin.partials.datatable-styles')
<style>
/* Action badges */
.act-badge {
  display: inline-flex; align-items: center; gap: 5px;
  padding: 3px 10px; border-radius: 999px;
  font-size: 10.5px; font-weight: 700;
}
.act-created { background:rgba(52,211,153,.12); color:#34d399; }
.act-updated { background:rgba(251,191,36,.12);  color:#fbbf24; }
.act-deleted { background:rgba(248,113,113,.12); color:#f87171; }
.act-login   { background:rgba(96,165,250,.12);  color:#60a5fa; }
.act-logout  { background:rgba(148,163,184,.12); color:#94a3b8; }
.act-default { background:var(--surface2); color:var(--muted); }

/* Filter bar */
.flt-input {
  background:var(--surface2); border:1px solid var(--border); color:var(--text);
  border-radius:8px; padding:6px 10px; font-size:12.5px; outline:none;
  font-family:'Plus Jakarta Sans',sans-serif; cursor:pointer; transition:border-color .15s;
}
.flt-input:focus { border-color:var(--ac); }

/* Detail modal property diff */
.diff-row { display:grid; grid-template-columns:120px 1fr 1fr; gap:8px; padding:6px 0; border-bottom:1px solid var(--border); font-size:12px; }
.diff-row:last-child { border-bottom:none; }
.diff-old { color:#f87171; text-decoration:line-through; opacity:.8; }
.diff-new { color:#34d399; font-weight:600; }

/* Timeline dot */
.tl-dot {
  width:8px; height:8px; border-radius:50%; flex-shrink:0; margin-top:5px;
}

/* Row hover detail btn */
tr:not(:hover) .btn-detail { opacity:0; }
tr:hover .btn-detail { opacity:1; }
</style>
@endpush

@section('content')
<div id="toast-container" class="toast-wrap"></div>

{{-- Header --}}
<div class="flex items-center justify-between animate-fadeUp">
  <div>
    <h2 class="font-display font-bold text-[20px]" style="color:var(--text)">Log Aktivitas</h2>
    <p class="text-[13px] mt-0.5" style="color:var(--muted)">Rekam jejak semua aktivitas dalam sistem</p>
  </div>
  <button onclick="openModal('modal-clear')"
    class="flex items-center gap-2 px-4 py-2 rounded-xl border text-[12.5px] font-semibold transition-colors"
    style="border-color:var(--border);color:#f87171"
    onmouseover="this.style.background='rgba(248,113,113,.08)'"
    onmouseout="this.style.background='transparent'">
    <i class="fa-solid fa-trash-can text-[11px]"></i> Bersihkan
  </button>
</div>

{{-- Stat Cards --}}
<div class="grid grid-cols-2 sm:grid-cols-5 gap-3 animate-fadeUp d1">
  @php
    $statItems = [
      ['label'=>'Total Log',  'val'=>$stats['total'],   'icon'=>'fa-scroll',                    'cls'=>'a-bg-lt a-text'],
      ['label'=>'Created',    'val'=>$stats['created'], 'icon'=>'fa-plus',                      'cls'=>'bg-emerald-500/15 text-emerald-400'],
      ['label'=>'Updated',    'val'=>$stats['updated'], 'icon'=>'fa-pen',                       'cls'=>'bg-amber-500/15 text-amber-400'],
      ['label'=>'Deleted',    'val'=>$stats['deleted'], 'icon'=>'fa-trash',                     'cls'=>'bg-rose-500/15 text-rose-400'],
      ['label'=>'Login',      'val'=>$stats['login'],   'icon'=>'fa-arrow-right-to-bracket',    'cls'=>'bg-blue-500/15 text-blue-400'],
    ];
  @endphp
  @foreach($statItems as $s)
  <div class="rounded-2xl p-4 border flex items-center gap-3" style="background:var(--surface);border-color:var(--border)">
    <div class="{{ $s['cls'] }} w-9 h-9 rounded-xl grid place-items-center flex-shrink-0 text-[13px]"><i class="fa-solid {{ $s['icon'] }}"></i></div>
    <div>
      <div class="font-display text-[20px] font-bold leading-none" style="color:var(--text)">{{ number_format($s['val']) }}</div>
      <div class="text-[11px] mt-0.5" style="color:var(--muted)">{{ $s['label'] }}</div>
    </div>
  </div>
  @endforeach
</div>

{{-- Filter Bar --}}
<form method="GET" action="{{ route('admin.log.index') }}" id="filter-form" class="animate-fadeUp d2">
  <div class="rounded-2xl px-4 py-3 border flex flex-wrap items-center gap-2" style="background:var(--surface);border-color:var(--border)">
    <i class="fa-solid fa-filter text-[11px]" style="color:var(--muted)"></i>

    <select name="module" class="flt-input" onchange="this.form.submit()">
      <option value="">Semua Module</option>
      @foreach($modules as $m)
        <option value="{{ $m }}" {{ request('module') === $m ? 'selected' : '' }}>{{ ucfirst($m) }}</option>
      @endforeach
    </select>

    <select name="action" class="flt-input" onchange="this.form.submit()">
      <option value="">Semua Action</option>
      @foreach($actions as $a)
        <option value="{{ $a }}" {{ request('action') === $a ? 'selected' : '' }}>{{ ucfirst($a) }}</option>
      @endforeach
    </select>

    <input type="date" name="date" value="{{ request('date') }}" class="flt-input" onchange="this.form.submit()" style="color-scheme:dark">

    @if(request()->hasAny(['module','action','user_id','date']))
    <a href="{{ route('admin.log.index') }}"
      class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[12px] font-semibold transition-colors"
      style="background:var(--surface2);color:var(--muted)"
      onmouseover="this.style.color='var(--text)'" onmouseout="this.style.color='var(--muted)'">
      <i class="fa-solid fa-xmark text-[10px]"></i> Reset Filter
    </a>
    @endif

    <div class="ml-auto flex items-center gap-2">
      <span class="text-[12px]" style="color:var(--muted)">{{ number_format($logs->total()) }} log ditemukan</span>
      @if($logs->total() > 0)
      <button type="button" onclick="openModal('modal-clear')"
        class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[11.5px] font-semibold transition-colors"
        style="background:rgba(248,113,113,.08);color:#f87171">
        <i class="fa-solid fa-trash-can text-[10px]"></i> Hapus Terfilter
      </button>
      @endif
    </div>
  </div>
</form>

{{-- Table --}}
<div class="rounded-2xl overflow-hidden border animate-fadeUp d3" style="background:var(--surface);border-color:var(--border)">
  @if($logs->isEmpty())
  <div class="py-16 text-center">
    <i class="fa-solid fa-scroll text-5xl mb-4 block a-text opacity-20"></i>
    <p class="font-display font-bold text-[15px] mb-1" style="color:var(--text)">Tidak ada log</p>
    <p class="text-[12.5px]" style="color:var(--muted)">
      @if(request()->hasAny(['module','action','date']))
        Tidak ada log yang cocok dengan filter saat ini.
      @else
        Aktivitas akan tercatat di sini secara otomatis.
      @endif
    </p>
  </div>
  @else

  <div class="p-4">
    <table id="log-table" class="w-full" style="width:100%">
      <thead>
        <tr>
          <th>#</th>
          <th>Waktu</th>
          <th>User</th>
          <th>Action</th>
          <th>Module</th>
          <th>Deskripsi</th>
          <th>IP Address</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        @foreach($logs as $log)
        @php
          $actClass = match($log->action) {
            'created' => 'act-created',
            'updated' => 'act-updated',
            'deleted' => 'act-deleted',
            'login'   => 'act-login',
            'logout'  => 'act-logout',
            default   => 'act-default',
          };
          $actIcon = match($log->action) {
            'created' => 'fa-plus',
            'updated' => 'fa-pen',
            'deleted' => 'fa-trash',
            'login'   => 'fa-arrow-right-to-bracket',
            'logout'  => 'fa-arrow-right-from-bracket',
            default   => 'fa-circle-dot',
          };
          $props = $log->properties;
        @endphp
        <tr data-id="{{ $log->id }}"
            data-time="{{ $log->created_at->format('d M Y, H:i:s') }}"
            data-user="{{ $log->user->name }}"
            data-action="{{ $log->action }}"
            data-actclass="{{ $actClass }}"
            data-acticon="{{ $actIcon }}"
            data-module="{{ $log->module }}"
            data-desc="{{ $log->description }}"
            data-ip="{{ $log->ip_address }}"
            data-ua="{{ $log->user_agent }}"
            data-props="{{ $props ? json_encode($props) : '' }}">
          <td class="text-center" style="color:var(--muted);width:48px">{{ $loop->iteration }}</td>
          <td style="white-space:nowrap">
            <div class="text-[12.5px] font-medium" style="color:var(--text)">{{ $log->created_at->format('d M Y') }}</div>
            <div class="text-[11px]" style="color:var(--muted)">{{ $log->created_at->format('H:i:s') }}</div>
          </td>
          <td>
            <div class="flex items-center gap-2">
              <div class="a-grad w-7 h-7 rounded-lg grid place-items-center font-bold text-[11px] text-white flex-shrink-0">{{ strtoupper(substr($log->user->name,0,1)) }}</div>
              <span class="text-[12.5px] font-medium" style="color:var(--text)">{{ $log->user->name }}</span>
            </div>
          </td>
          <td>
            <span class="act-badge {{ $actClass }}">
              <i class="fa-solid {{ $actIcon }} text-[9px]"></i>{{ ucfirst($log->action) }}
            </span>
          </td>
          <td>
            <span class="text-[11.5px] px-2 py-0.5 rounded-full font-semibold" style="background:var(--surface2);color:var(--sub)">{{ ucfirst($log->module) }}</span>
          </td>
          <td style="max-width:260px">
            <div class="text-[12.5px] truncate" style="color:var(--text)" title="{{ $log->description }}">{{ $log->description ?? '—' }}</div>
          </td>
          <td>
            <code class="text-[11px]" style="color:var(--muted);font-family:monospace">{{ $log->ip_address ?? '—' }}</code>
          </td>
          <td>
            <div class="flex items-center gap-1">
              <button onclick="openDetail(this.closest('tr'))"
                class="btn-detail w-7 h-7 rounded-lg grid place-items-center text-[11px] transition-all"
                style="color:var(--muted);background:var(--surface2)"
                title="Lihat detail"
                onmouseover="this.style.color='var(--ac)'"
                onmouseout="this.style.color='var(--muted)'">
                <i class="fa-solid fa-eye"></i>
              </button>
              <button onclick="deleteLog({{ $log->id }}, this)"
                class="w-7 h-7 rounded-lg grid place-items-center text-[11px] transition-all"
                style="color:var(--muted)"
                title="Hapus log"
                onmouseover="this.style.background='rgba(248,113,113,.1)';this.style.color='#f87171'"
                onmouseout="this.style.background='transparent';this.style.color='var(--muted)'">
                <i class="fa-solid fa-trash"></i>
              </button>
            </div>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  @endif
</div>

{{-- MODAL DETAIL --}}
<div id="modal-detail" class="modal-backdrop">
  <div class="modal-box" style="max-width:560px">
    <div class="flex items-center justify-between px-6 py-5 border-b" style="border-color:var(--border)">
      <div class="flex items-center gap-3">
        <div class="a-bg-lt a-text w-10 h-10 rounded-xl grid place-items-center"><i class="fa-solid fa-scroll"></i></div>
        <div>
          <h3 class="font-display font-bold text-[15px]" style="color:var(--text)">Detail Log</h3>
          <p class="text-[11.5px]" style="color:var(--muted)" id="d-time"></p>
        </div>
      </div>
      <button onclick="closeModal('modal-detail')" class="w-8 h-8 rounded-lg grid place-items-center" style="color:var(--muted)" onmouseover="this.style.background='var(--surface2)'" onmouseout="this.style.background='transparent'"><i class="fa-solid fa-xmark"></i></button>
    </div>

    <div class="px-6 py-5 space-y-4">
      {{-- Info grid --}}
      <div class="grid grid-cols-2 gap-3">
        <div class="rounded-xl p-3 border" style="background:var(--surface2);border-color:var(--border)">
          <div class="text-[10.5px] font-bold uppercase tracking-[.8px] mb-1" style="color:var(--muted)">User</div>
          <div class="text-[13px] font-semibold" style="color:var(--text)" id="d-user"></div>
        </div>
        <div class="rounded-xl p-3 border" style="background:var(--surface2);border-color:var(--border)">
          <div class="text-[10.5px] font-bold uppercase tracking-[.8px] mb-1" style="color:var(--muted)">Action & Module</div>
          <div class="flex items-center gap-2">
            <span class="act-badge" id="d-actbadge"></span>
            <span class="text-[12px] px-2 py-0.5 rounded-full font-semibold" style="background:var(--border);color:var(--sub)" id="d-module"></span>
          </div>
        </div>
      </div>

      <div class="rounded-xl p-3 border" style="background:var(--surface2);border-color:var(--border)">
        <div class="text-[10.5px] font-bold uppercase tracking-[.8px] mb-1" style="color:var(--muted)">Deskripsi</div>
        <div class="text-[13px]" style="color:var(--text)" id="d-desc"></div>
      </div>

      <div class="grid grid-cols-2 gap-3">
        <div class="rounded-xl p-3 border" style="background:var(--surface2);border-color:var(--border)">
          <div class="text-[10.5px] font-bold uppercase tracking-[.8px] mb-1" style="color:var(--muted)">IP Address</div>
          <code class="text-[12px]" style="color:var(--text);font-family:monospace" id="d-ip"></code>
        </div>
        <div class="rounded-xl p-3 border" style="background:var(--surface2);border-color:var(--border)">
          <div class="text-[10.5px] font-bold uppercase tracking-[.8px] mb-1" style="color:var(--muted)">User Agent</div>
          <div class="text-[11px] truncate" style="color:var(--muted)" id="d-ua" title=""></div>
        </div>
      </div>

      {{-- Properties diff --}}
      <div id="d-props-wrap" class="hidden">
        <div class="text-[10.5px] font-bold uppercase tracking-[.8px] mb-2" style="color:var(--muted)">Perubahan Data</div>
        <div class="rounded-xl border overflow-hidden" style="border-color:var(--border)">
          <div class="diff-row px-4" style="background:var(--surface2)">
            <div class="text-[10px] font-bold uppercase tracking-wide" style="color:var(--muted)">Field</div>
            <div class="text-[10px] font-bold uppercase tracking-wide" style="color:var(--muted)">Sebelum</div>
            <div class="text-[10px] font-bold uppercase tracking-wide" style="color:var(--muted)">Sesudah</div>
          </div>
          <div id="d-props-body" class="px-4" style="background:var(--surface)"></div>
        </div>
      </div>

      {{-- Raw JSON fallback --}}
      <div id="d-props-raw-wrap" class="hidden">
        <div class="text-[10.5px] font-bold uppercase tracking-[.8px] mb-2" style="color:var(--muted)">Data Tambahan</div>
        <pre id="d-props-raw" class="rounded-xl p-3 text-[11.5px] overflow-x-auto" style="background:var(--surface2);color:var(--muted);border:1px solid var(--border);font-family:monospace;white-space:pre-wrap"></pre>
      </div>
    </div>
  </div>
</div>

{{-- MODAL BERSIHKAN --}}
<div id="modal-clear" class="modal-backdrop">
  <div class="modal-box modal-sm">
    <div class="p-6 text-center">
      <div class="bg-rose-500/15 text-rose-400 w-14 h-14 rounded-2xl grid place-items-center text-2xl mx-auto mb-4"><i class="fa-solid fa-trash-can"></i></div>
      <h3 class="font-display font-bold text-[16px] mb-1" style="color:var(--text)">Bersihkan Log?</h3>
      <p class="text-[12.5px] mb-1" style="color:var(--muted)" id="clear-desc">
        @if(request()->hasAny(['module','action','date']))
          Log yang terfilter saat ini akan dihapus permanen.
        @else
          Semua log aktivitas akan dihapus permanen.
        @endif
      </p>
      <p class="text-[11.5px]" style="color:#f87171">Tindakan ini tidak dapat dibatalkan.</p>
    </div>
    <div class="flex gap-3 px-6 pb-6">
      <button onclick="closeModal('modal-clear')" class="flex-1 px-4 py-2 rounded-xl border text-[13px] font-semibold" style="border-color:var(--border);color:var(--sub)" onmouseover="this.style.background='var(--surface2)'" onmouseout="this.style.background='transparent'">Batal</button>
      <button id="btn-clear" onclick="doClear()" class="flex-1 px-4 py-2 rounded-xl text-[13px] font-semibold text-white" style="background:#f87171">
        <i class="fa-solid fa-trash-can mr-1.5 text-[11px]"></i>Hapus
      </button>
    </div>
  </div>
</div>

@endsection

@push('scripts')
@include('admin.partials.datatable-scripts')
<script>
$(()=>$('#log-table').DataTable({
  language:{...DT_LANG, searchPlaceholder:'Cari log...'},
  columnDefs:[
    {orderable:false, targets:[0,7]},
    {className:'text-center', targets:[0]},
    {type:'date', targets:[1]},
  ],
  order:[[0,'desc']],
  pageLength:25,
  dom: DT_DOM,
}));

// DETAIL MODAL
function openDetail(tr){
  const d = tr.dataset;
  document.getElementById('d-time').textContent   = d.time;
  document.getElementById('d-user').textContent   = d.user;
  document.getElementById('d-ip').textContent     = d.ip || '—';
  document.getElementById('d-desc').textContent   = d.desc || '—';
  document.getElementById('d-module').textContent = d.module ? d.module.charAt(0).toUpperCase()+d.module.slice(1) : '—';

  const ua = document.getElementById('d-ua');
  ua.textContent = d.ua || '—';
  ua.title = d.ua || '';

  // Action badge
  const badge = document.getElementById('d-actbadge');
  badge.className = `act-badge ${d.actclass}`;
  badge.innerHTML = `<i class="fa-solid ${d.acticon} text-[9px]"></i>${d.action.charAt(0).toUpperCase()+d.action.slice(1)}`;

  // Properties
  const propsWrap    = document.getElementById('d-props-wrap');
  const propsRawWrap = document.getElementById('d-props-raw-wrap');
  const propsBody    = document.getElementById('d-props-body');
  propsWrap.classList.add('hidden');
  propsRawWrap.classList.add('hidden');
  propsBody.innerHTML = '';

  if(d.props){
    try {
      const props = JSON.parse(d.props);
      if(props.old && props.new){
        // diff view
        propsWrap.classList.remove('hidden');
        const allKeys = new Set([...Object.keys(props.old), ...Object.keys(props.new)]);
        allKeys.forEach(key => {
          const oldVal = props.old[key] ?? '—';
          const newVal = props.new[key] ?? '—';
          const changed = String(oldVal) !== String(newVal);
          const row = document.createElement('div');
          row.className = 'diff-row';
          row.innerHTML = `
            <div class="font-semibold" style="color:var(--sub)">${key}</div>
            <div class="${changed ? 'diff-old' : ''}" style="${!changed ? 'color:var(--muted)' : ''}">${oldVal === true ? 'Ya' : oldVal === false ? 'Tidak' : oldVal}</div>
            <div class="${changed ? 'diff-new' : ''}" style="${!changed ? 'color:var(--muted)' : ''}">${newVal === true ? 'Ya' : newVal === false ? 'Tidak' : newVal}</div>
          `;
          propsBody.appendChild(row);
        });
      } else {
        // raw json
        propsRawWrap.classList.remove('hidden');
        document.getElementById('d-props-raw').textContent = JSON.stringify(props, null, 2);
      }
    } catch(err) {
      propsRawWrap.classList.remove('hidden');
      document.getElementById('d-props-raw').textContent = d.props;
    }
  }

  openModal('modal-detail');
}

// DELETE single
function deleteLog(id, btn){
  if(!confirm('Hapus log ini?')) return;
  btn.disabled = true;
  fetch(`/admin/log/${id}`, {
    method:'POST',
    headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'},
    body: new URLSearchParams({_method:'DELETE'}),
  })
  .then(async r=>({ok:r.ok, data:await r.json()}))
  .then(({ok, data})=>{
    showToast(ok?'success':'error', data.message);
    if(ok){
      const row = btn.closest('tr');
      row.style.transition = 'opacity .3s';
      row.style.opacity = '0';
      setTimeout(()=>{ row.remove(); }, 300);
    }
  })
  .catch(()=>showToast('error','Gagal terhubung ke server.'))
  .finally(()=>{ btn.disabled = false; });
}

// CLEAR (filtered or all)
function doClear(){
  setLoading('btn-clear', true);
  const qs = window.location.search;

  fetch('/admin/log/all' + qs, {
    method:'POST',
    headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'},
    body: new URLSearchParams({_method:'DELETE'}),
  })
  .then(async r=>({ok:r.ok, data:await r.json()}))
  .then(({ok, data})=>{
    closeModal('modal-clear');
    showToast(ok?'success':'error', data.message);
    if(ok) setTimeout(()=>location.reload(), 1200);
  })
  .catch(()=>showToast('error','Gagal terhubung ke server.'))
  .finally(()=>setLoading('btn-clear', false));
}

@if(session('success'))document.addEventListener('DOMContentLoaded',()=>showToast('success','{{ session("success") }}'));@endif
@if(session('error'))document.addEventListener('DOMContentLoaded',()=>showToast('error','{{ session("error") }}'));@endif
</script>
@endpush
