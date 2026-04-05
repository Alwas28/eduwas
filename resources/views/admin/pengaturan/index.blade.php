@extends('layouts.admin')
@section('title', 'Pengaturan Sistem')
@section('page-title', 'Pengaturan')

@push('styles')
<style>
.setting-card { background:var(--surface); border:1px solid var(--border); border-radius:18px; overflow:hidden; }
.setting-head { padding:16px 20px; border-bottom:1px solid var(--border); display:flex; align-items:center; gap:12px; }
.setting-body { padding:20px; display:flex; flex-direction:column; gap:18px; }
.field-label { font-size:12px; font-weight:600; color:var(--text); margin-bottom:6px; display:block; }
.field-hint  { font-size:11px; color:var(--muted); margin-top:4px; }
.field-input {
  width:100%; padding:9px 13px; border-radius:10px;
  border:1px solid var(--border); background:var(--surface2); color:var(--text);
  font-size:13px; outline:none; transition:border-color .2s; font-family:inherit;
}
.field-input:focus { border-color:rgba(var(--ac-rgb),.6); }
.field-select {
  width:100%; padding:9px 13px; border-radius:10px;
  border:1px solid var(--border); background:var(--surface2); color:var(--text);
  font-size:13px; outline:none; cursor:pointer; appearance:none;
  background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' fill='%236b7280'%3E%3Cpath d='M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z'/%3E%3C/svg%3E");
  background-repeat:no-repeat; background-position:right 12px center; background-size:16px; padding-right:36px;
}
.field-select:focus { border-color:rgba(var(--ac-rgb),.6); }
.pw-wrap { position:relative; }
.pw-toggle {
  position:absolute; right:10px; top:50%; transform:translateY(-50%);
  background:none; border:none; cursor:pointer; color:var(--muted); font-size:13px; padding:4px;
}
.pw-toggle:hover { color:var(--text); }
.model-badge { font-size:10px; font-weight:700; padding:2px 7px; border-radius:20px; margin-left:6px; vertical-align:middle; }
.model-free { background:rgba(16,185,129,.15); color:#10b981; }
.model-paid { background:rgba(245,158,11,.15); color:#f59e0b; }
.save-row { display:flex; align-items:center; gap-12px; gap:10px; }
.save-msg { font-size:12px; font-weight:600; opacity:0; transition:opacity .3s; }
.save-msg.show { opacity:1; }
</style>
@endpush

@section('content')
@php $canEdit = auth()->user()?->allAccesses()->contains('edit.pengaturan'); @endphp
<div class="space-y-6 animate-fadeUp" style="max-width:760px">

  {{-- Header --}}
  <div>
    <h2 class="font-display font-bold text-[20px]" style="color:var(--text)">Pengaturan Sistem</h2>
    <p class="text-[12px] mt-1" style="color:var(--muted)">Konfigurasi fitur AI Chat dan integrasi layanan eksternal.</p>
  </div>

  {{-- ── Asisten AI ──────────────────────────────────────────── --}}
  <div class="setting-card" id="card-asisten">
    <div class="setting-head">
      <div class="w-9 h-9 rounded-xl grid place-items-center flex-shrink-0 text-[14px]"
           style="background:rgba(99,102,241,.12);color:#818cf8">
        <i class="fa-solid fa-robot"></i>
      </div>
      <div>
        <div class="text-[14px] font-semibold" style="color:var(--text)">Asisten AI</div>
        <div class="text-[11px]" style="color:var(--muted)">Konfigurasi persona asisten yang ditampilkan ke mahasiswa</div>
      </div>
    </div>
    <div class="setting-body">
      <div>
        <label class="field-label" for="ai_assistant_name">
          Nama Asisten AI
        </label>
        <input type="text" id="ai_assistant_name" name="ai_assistant_name"
               class="field-input" maxlength="60"
               value="{{ $settings['ai_assistant_name']?->value ?? 'Tanya Asdos' }}"
               placeholder="Tanya Asdos"
               {{ !$canEdit ? 'readonly' : '' }}>
        <p class="field-hint">Nama ini ditampilkan pada tombol dan judul chat di halaman materi mahasiswa.</p>
      </div>
      @if($canEdit)
      <div class="save-row">
        <button onclick="saveSetting('asisten')"
                class="flex items-center gap-1.5 px-4 py-2 rounded-xl text-[12px] font-semibold text-white"
                style="background:var(--ac)" id="btn-asisten">
          <i class="fa-solid fa-floppy-disk"></i>Simpan
        </button>
        <span class="save-msg" id="msg-asisten" style="color:#10b981"></span>
      </div>
      @endif
    </div>
  </div>

  {{-- ── OpenRouter ──────────────────────────────────────────── --}}
  <div class="setting-card" id="card-openrouter">
    <div class="setting-head">
      <div class="w-9 h-9 rounded-xl grid place-items-center flex-shrink-0 text-[14px]"
           style="background:rgba(245,158,11,.12);color:#f59e0b">
        <i class="fa-solid fa-plug"></i>
      </div>
      <div>
        <div class="text-[14px] font-semibold" style="color:var(--text)">Konfigurasi OpenRouter</div>
        <div class="text-[11px]" style="color:var(--muted)">Hubungkan ke <strong>openrouter.ai</strong> untuk mengaktifkan fitur AI Chat</div>
      </div>
    </div>
    <div class="setting-body">
      {{-- API Key --}}
      <div>
        <label class="field-label" for="openrouter_api_key">
          API Key
        </label>
        <div class="pw-wrap">
          <input type="password" id="openrouter_api_key" name="openrouter_api_key"
                 class="field-input pr-10"
                 value="{{ $settings['openrouter_api_key']?->value ?? '' }}"
                 placeholder="sk-or-v1-…"
                 autocomplete="new-password"
                 {{ !$canEdit ? 'readonly' : '' }}>
          <button type="button" class="pw-toggle" onclick="togglePw()" title="Tampilkan/sembunyikan">
            <i class="fa-solid fa-eye" id="pw-icon"></i>
          </button>
        </div>
        <p class="field-hint">Dapatkan API key dari <strong>openrouter.ai/keys</strong>. Kosongkan untuk tidak mengubah nilai saat ini.</p>
      </div>

      {{-- Model --}}
      <div>
        <label class="field-label" for="openrouter_model">
          Model AI
        </label>
        <select id="openrouter_model" name="openrouter_model"
                class="field-select"
                {{ !$canEdit ? 'disabled' : '' }}>
          @foreach($modelOptions as $val => $label)
            @php $isFree = str_contains($val, ':free'); @endphp
            <option value="{{ $val }}" {{ ($settings['openrouter_model']?->value ?? 'google/gemma-3-27b-it:free') === $val ? 'selected' : '' }}>
              {{ $label }}
            </option>
          @endforeach
        </select>
        <p class="field-hint">Model gratis memiliki rate limit. Untuk produksi skala besar, pertimbangkan model berbayar.</p>
      </div>

      @if($canEdit)
      <div class="save-row">
        <button onclick="saveSetting('openrouter')"
                class="flex items-center gap-1.5 px-4 py-2 rounded-xl text-[12px] font-semibold text-white"
                style="background:var(--ac)" id="btn-openrouter">
          <i class="fa-solid fa-floppy-disk"></i>Simpan
        </button>
        <span class="save-msg" id="msg-openrouter" style="color:#10b981"></span>
      </div>
      @endif
    </div>
  </div>

  {{-- Info box --}}
  <div class="px-4 py-3 rounded-xl text-[12px]" style="background:rgba(99,102,241,.08);border:1px solid rgba(99,102,241,.2);color:var(--muted)">
    <i class="fa-solid fa-circle-info mr-2" style="color:#818cf8"></i>
    Model gratis yang direkomendasikan untuk Bahasa Indonesia:
    <strong style="color:var(--text)">google/gemma-3-27b-it</strong> (kualitas baik) atau
    <strong style="color:var(--text)">meta-llama/llama-3.3-70b-instruct</strong> (lebih akurat, rate limit lebih ketat).
  </div>

</div>
@endsection

@push('scripts')
<script>
const CSRF = '{{ csrf_token() }}';

function togglePw() {
  const inp  = document.getElementById('openrouter_api_key');
  const icon = document.getElementById('pw-icon');
  if (inp.type === 'password') {
    inp.type = 'text';
    icon.className = 'fa-solid fa-eye-slash';
  } else {
    inp.type = 'password';
    icon.className = 'fa-solid fa-eye';
  }
}

async function saveSetting(section) {
  const btn = document.getElementById('btn-' + section);
  const msg = document.getElementById('msg-' + section);

  const body = {};
  if (section === 'asisten') {
    body.ai_assistant_name = document.getElementById('ai_assistant_name')?.value?.trim();
  } else {
    body.openrouter_api_key = document.getElementById('openrouter_api_key')?.value;
    body.openrouter_model   = document.getElementById('openrouter_model')?.value;
  }

  if (btn) { btn.disabled = true; btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1"></i>Menyimpan…'; }

  try {
    const r = await fetch('{{ route('admin.pengaturan.update') }}', {
      method: 'PATCH',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
      body: JSON.stringify(body),
    });
    const j = await r.json();
    if (!r.ok) throw new Error(j.message || 'Gagal menyimpan');
    if (msg) {
      msg.textContent = '✓ ' + (j.message || 'Tersimpan');
      msg.style.color = '#10b981';
      msg.classList.add('show');
      setTimeout(() => msg.classList.remove('show'), 3000);
    }
  } catch(e) {
    if (msg) {
      msg.textContent = e.message || 'Gagal menyimpan.';
      msg.style.color = '#ef4444';
      msg.classList.add('show');
      setTimeout(() => msg.classList.remove('show'), 4000);
    }
  } finally {
    if (btn) { btn.disabled = false; btn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i>Simpan'; }
  }
}
</script>
@endpush
