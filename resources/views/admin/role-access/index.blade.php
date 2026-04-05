@extends('layouts.admin')
@section('title','Role Access')
@section('page-title','Role Access')

@push('styles')
@include('admin.partials.datatable-styles')
<style>
.role-card {
  border-radius: 16px;
  border: 1px solid var(--border);
  background: var(--surface);
  overflow: hidden;
  transition: box-shadow .2s, border-color .2s;
}
.role-card:hover { box-shadow: 0 4px 24px rgba(0,0,0,.08); border-color: var(--ac); }
.group-pill {
  display: inline-flex; align-items: center; gap: 4px;
  padding: 2px 8px; border-radius: 999px;
  font-size: 10.5px; font-weight: 600;
  background: var(--surface2); color: var(--muted);
}
.group-pill.active { background: rgba(var(--ac-rgb),.12); color: var(--ac); }
</style>
@endpush

@section('content')
<div id="toast-container" class="toast-wrap"></div>

{{-- Header --}}
<div class="flex items-center justify-between animate-fadeUp">
  <div>
    <h2 class="font-display font-bold text-[20px]" style="color:var(--text)">Role Access</h2>
    <p class="text-[13px] mt-0.5" style="color:var(--muted)">Atur hak akses (permission) untuk setiap role</p>
  </div>
</div>

{{-- Stat Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 animate-fadeUp d1">
  <div class="rounded-2xl p-5 border flex items-center gap-4" style="background:var(--surface);border-color:var(--border)">
    <div class="a-bg-lt a-text w-11 h-11 rounded-xl grid place-items-center text-lg flex-shrink-0"><i class="fa-solid fa-id-badge"></i></div>
    <div><div class="font-display text-[26px] font-bold" style="color:var(--text)">{{ $roles->count() }}</div><div class="text-[12px]" style="color:var(--muted)">Total Roles</div></div>
  </div>
  <div class="rounded-2xl p-5 border flex items-center gap-4" style="background:var(--surface);border-color:var(--border)">
    <div class="bg-violet-500/15 text-violet-400 w-11 h-11 rounded-xl grid place-items-center text-lg flex-shrink-0"><i class="fa-solid fa-key"></i></div>
    <div><div class="font-display text-[26px] font-bold" style="color:var(--text)">{{ $accesses->count() }}</div><div class="text-[12px]" style="color:var(--muted)">Total Access</div></div>
  </div>
  <div class="rounded-2xl p-5 border flex items-center gap-4" style="background:var(--surface);border-color:var(--border)">
    <div class="bg-emerald-500/15 text-emerald-400 w-11 h-11 rounded-xl grid place-items-center text-lg flex-shrink-0"><i class="fa-solid fa-layer-group"></i></div>
    <div><div class="font-display text-[26px] font-bold" style="color:var(--text)">{{ $groups->count() }}</div><div class="text-[12px]" style="color:var(--muted)">Group Access</div></div>
  </div>
</div>

{{-- Role Cards Grid --}}
<div class="animate-fadeUp d2">
  <div class="flex items-center justify-between mb-4">
    <span class="font-display font-semibold text-[15px]" style="color:var(--text)">Daftar Role & Permission</span>
    <span class="text-[12px] px-2.5 py-1 rounded-full a-bg-lt a-text font-semibold">{{ $roles->count() }} roles</span>
  </div>

  @if($roles->isEmpty())
  <div class="rounded-2xl p-12 text-center border" style="background:var(--surface);border-color:var(--border)">
    <i class="fa-solid fa-id-badge text-4xl mb-3 block a-text opacity-40"></i>
    <p style="color:var(--muted)" class="text-[13px]">Belum ada role. Buat role terlebih dahulu.</p>
  </div>
  @else
  <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
    @php
      $roleColors = ['bg-blue-500/15 text-blue-400','bg-violet-500/15 text-violet-400','bg-emerald-500/15 text-emerald-400','bg-amber-500/15 text-amber-400','bg-rose-500/15 text-rose-400'];
    @endphp
    @foreach($roles as $role)
    @php
      $c = $roleColors[$role->id % count($roleColors)];
      $total = $accesses->count();
      $count = $role->accesses_count;
      $pct   = $total > 0 ? round($count / $total * 100) : 0;
      // group coverage
      $roleAccessGrouped = $role->accesses->groupBy('group');
    @endphp
    <div class="role-card">
      {{-- Card Header --}}
      <div class="p-5 border-b" style="border-color:var(--border)">
        <div class="flex items-start justify-between gap-3">
          <div class="flex items-center gap-3">
            <div class="{{ $c }} w-11 h-11 rounded-xl grid place-items-center text-[15px] font-bold flex-shrink-0">
              {{ strtoupper(substr($role->display_name, 0, 1)) }}
            </div>
            <div>
              <div class="font-display font-bold text-[14.5px]" style="color:var(--text)">{{ $role->display_name }}</div>
              <code class="text-[11px]" style="color:var(--muted);font-family:monospace">{{ $role->name }}</code>
            </div>
          </div>
          <a href="{{ route('admin.role-access.edit', $role) }}"
            class="flex-shrink-0 flex items-center gap-1.5 px-3 py-1.5 rounded-lg border text-[12px] font-medium transition-all a-text"
            style="background:var(--surface2);border-color:var(--border)"
            onmouseover="this.style.borderColor='var(--ac)'"
            onmouseout="this.style.borderColor='var(--border)'">
            <i class="fa-solid fa-sliders text-[11px]"></i> Kelola
          </a>
        </div>

        {{-- Progress --}}
        <div class="mt-4">
          <div class="flex items-center justify-between mb-1.5">
            <span class="text-[11.5px]" style="color:var(--muted)">Coverage access</span>
            <span class="text-[12px] font-bold a-text">{{ $pct }}%</span>
          </div>
          <div class="h-2 rounded-full overflow-hidden" style="background:var(--border)">
            <div class="h-full rounded-full a-grad transition-all" style="width:{{ $pct }}%"></div>
          </div>
          <div class="mt-1.5 text-[11px]" style="color:var(--muted)">{{ $count }} dari {{ $total }} access diberikan</div>
        </div>
      </div>

      {{-- Group Coverage --}}
      <div class="p-5">
        @if($groups->isEmpty())
          <p class="text-[12px] text-center py-2" style="color:var(--muted)">Belum ada group access</p>
        @else
        <div class="space-y-2">
          @foreach($groups as $group)
          @php
            $groupTotal = $accesses->where('group', $group)->count();
            $groupCount = isset($roleAccessGrouped[$group]) ? $roleAccessGrouped[$group]->count() : 0;
            $groupPct   = $groupTotal > 0 ? round($groupCount / $groupTotal * 100) : 0;
            $isFullGroup = $groupCount === $groupTotal && $groupTotal > 0;
          @endphp
          <div class="flex items-center gap-2">
            <div class="text-[10.5px] font-semibold uppercase tracking-wide flex-shrink-0" style="color:var(--muted);min-width:80px;max-width:80px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="{{ $group }}">{{ $group }}</div>
            <div class="flex-1 h-1.5 rounded-full overflow-hidden" style="background:var(--border)">
              <div class="h-full rounded-full transition-all {{ $isFullGroup ? 'bg-emerald-400' : 'a-bg' }}" style="width:{{ $groupPct }}%"></div>
            </div>
            <span class="text-[10.5px] flex-shrink-0 font-medium" style="color:var(--muted)">{{ $groupCount }}/{{ $groupTotal }}</span>
            @if($isFullGroup)
            <i class="fa-solid fa-circle-check text-emerald-400 text-[11px] flex-shrink-0"></i>
            @endif
          </div>
          @endforeach
        </div>
        @endif

        {{-- Recent access badges --}}
        @if($role->accesses->isNotEmpty())
        <div class="flex flex-wrap gap-1 mt-3 pt-3 border-t" style="border-color:var(--border)">
          @foreach($role->accesses->take(5) as $ac)
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold a-bg-lt a-text">{{ $ac->display_name }}</span>
          @endforeach
          @if($role->accesses->count() > 5)
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold" style="background:var(--surface2);color:var(--muted)">+{{ $role->accesses->count() - 5 }} lainnya</span>
          @endif
        </div>
        @else
        <div class="mt-3 pt-3 border-t text-center text-[12px]" style="border-color:var(--border);color:var(--muted)">
          <i class="fa-solid fa-ban mr-1 opacity-50"></i> Belum ada access diberikan
        </div>
        @endif
      </div>
    </div>
    @endforeach
  </div>
  @endif
</div>

@endsection

@push('scripts')
<script>
@if(session('success'))document.addEventListener('DOMContentLoaded',()=>showToast('success','{{ session("success") }}'));@endif
@if(session('error'))document.addEventListener('DOMContentLoaded',()=>showToast('error','{{ session("error") }}'));@endif
</script>
@endpush
