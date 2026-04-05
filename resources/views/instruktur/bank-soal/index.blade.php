@extends('layouts.instruktur')
@section('title', 'Bank Soal')
@section('page-title', 'Bank Soal')

@push('styles')
<style>
/* ── Layout ── */
.bs-layout { display:grid; grid-template-columns:260px 1fr; gap:20px; align-items:start; }
@media(max-width:860px){ .bs-layout { grid-template-columns:1fr; } }

/* ── Sidebar MK ── */
.mk-item { display:flex; align-items:center; gap:10px; padding:9px 12px; border-radius:11px; cursor:pointer; transition:all .15s; border:1px solid transparent; }
.mk-item:hover { background:var(--surface2); }
.mk-item.active { background:var(--ac); border-color:var(--ac); color:#fff; }
.mk-item.active .mk-sks { color:rgba(255,255,255,.7); }
.mk-icon { width:34px; height:34px; border-radius:9px; display:grid; place-items:center; font-size:12px; flex-shrink:0; }

/* ── Soal card ── */
.soal-card { background:var(--surface); border:1px solid var(--border); border-radius:14px; overflow:hidden; transition:border-color .15s; }
.soal-card:hover { border-color:var(--ac); }
.soal-head { display:flex; align-items:flex-start; gap:12px; padding:14px 16px; }
.soal-num { min-width:28px; height:28px; border-radius:8px; display:grid; place-items:center; font-size:11px; font-weight:700; flex-shrink:0; margin-top:1px; }
.soal-body { padding:0 16px 14px 56px; }
.soal-pilihan { display:flex; align-items:flex-start; gap:8px; padding:6px 10px; border-radius:8px; margin-bottom:4px; font-size:13px; }
.soal-pilihan.benar { background:rgba(16,185,129,.1); color:#10b981; font-weight:600; }
.soal-pilihan.salah { color:var(--sub); }
.pilihan-huruf { width:22px; height:22px; border-radius:6px; display:grid; place-items:center; font-size:10px; font-weight:700; flex-shrink:0; }
.benar .pilihan-huruf { background:rgba(16,185,129,.2); color:#10b981; }
.salah .pilihan-huruf { background:var(--surface2); color:var(--muted); }

/* ── Badge tingkat ── */
.bdg-mudah  { background:rgba(16,185,129,.12); color:#10b981; }
.bdg-sedang { background:rgba(245,158,11,.12);  color:#f59e0b; }
.bdg-sulit  { background:rgba(239,68,68,.12);   color:#ef4444; }
.bdg-essay  { background:rgba(99,102,241,.12);  color:#818cf8; }
.bdg-pg     { background:rgba(6,182,212,.12);   color:#22d3ee; }

/* ── Tabs ── */
.tab-bar { display:flex; gap:4px; padding:4px; border-radius:12px; background:var(--surface2); }
.tab-btn { flex:1; padding:7px 12px; border:none; border-radius:9px; font-size:12.5px; font-weight:600; cursor:pointer; transition:all .15s; color:var(--muted); background:transparent; }
.tab-btn.active { background:var(--surface); color:var(--text); box-shadow:0 1px 4px rgba(0,0,0,.14); }

/* ── Modal ── */
.modal-backdrop { position:fixed; inset:0; background:rgba(0,0,0,.55); z-index:100; display:flex; align-items:center; justify-content:center; padding:16px; }
.modal-box { background:var(--surface); border:1px solid var(--border); border-radius:20px; width:100%; max-width:600px; max-height:90vh; display:flex; flex-direction:column; overflow:hidden; }
.modal-head { padding:16px 20px; border-bottom:1px solid var(--border); display:flex; align-items:center; justify-content:space-between; flex-shrink:0; }
.modal-body { padding:20px; overflow-y:auto; flex:1; }
.modal-foot { padding:14px 20px; border-top:1px solid var(--border); display:flex; gap:8px; justify-content:flex-end; flex-shrink:0; }

/* ── PG option builder ── */
.pg-option { display:flex; align-items:center; gap:8px; padding:8px 10px; border-radius:10px; border:1px solid var(--border); margin-bottom:6px; background:var(--surface2); }
.pg-option input[type=text] { flex:1; background:transparent; border:none; outline:none; font-size:13px; color:var(--text); }
.pg-radio { width:16px; height:16px; accent-color:var(--ac); flex-shrink:0; cursor:pointer; }

/* ── AI modal ── */
.ai-result-item { border:1px solid var(--border); border-radius:12px; padding:14px; margin-bottom:10px; cursor:pointer; transition:all .15s; }
.ai-result-item:hover { border-color:var(--ac); }
.ai-result-item.selected { border-color:var(--ac); background:rgba(var(--ac-rgb),.06); }
.ai-check { width:20px; height:20px; border-radius:6px; border:2px solid var(--border); flex-shrink:0; display:grid; place-items:center; transition:all .15s; }
.ai-result-item.selected .ai-check { background:var(--ac); border-color:var(--ac); }

/* ── Toast ── */
#bs-toast { position:fixed; bottom:24px; left:50%; transform:translateX(-50%) translateY(80px); background:var(--surface); border:1px solid var(--border); border-radius:14px; padding:10px 20px; font-size:13px; font-weight:600; box-shadow:0 8px 24px rgba(0,0,0,.25); transition:transform .3s,opacity .3s; opacity:0; z-index:300; white-space:nowrap; }
#bs-toast.show { transform:translateX(-50%) translateY(0); opacity:1; }

/* ── Empty ── */
.empty-state { text-align:center; padding:64px 20px; }
</style>
@endpush

@section('content')
@php
  $csrf = csrf_token();
  $essay = $soalList->where('tipe','essay');
  $pg    = $soalList->where('tipe','pilihan_ganda');
@endphp

<div class="space-y-5">

  {{-- Header --}}
  <div class="flex items-center justify-between animate-fadeUp">
    <div>
      <h2 class="font-display font-bold text-[20px]" style="color:var(--text)">Bank Soal</h2>
      <p class="text-[13px] mt-0.5" style="color:var(--muted)">Kelola kumpulan soal per mata kuliah</p>
    </div>
    @if($mk)
    <div class="flex items-center gap-2">
      <button onclick="openAiModal()"
        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-[13px] font-semibold border transition-colors"
        style="border-color:var(--ac);color:var(--ac)"
        onmouseover="this.style.background='rgba(var(--ac-rgb),.08)'"
        onmouseout="this.style.background='transparent'">
        <i class="fa-solid fa-wand-magic-sparkles text-[12px]"></i>Buat dengan AI
      </button>
      <button onclick="openSoalModal()"
        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-[13px] font-semibold text-white a-grad">
        <i class="fa-solid fa-plus text-[12px]"></i>Tambah Soal
      </button>
    </div>
    @endif
  </div>

  <div class="bs-layout animate-fadeUp d1">

    {{-- ── Sidebar Mata Kuliah ── --}}
    <div class="rounded-2xl border overflow-hidden" style="background:var(--surface);border-color:var(--border)">
      <div class="px-4 py-3 border-b flex items-center gap-2" style="border-color:var(--border)">
        <i class="fa-solid fa-book a-text text-[12px]"></i>
        <span class="font-display font-semibold text-[13px]" style="color:var(--text)">Mata Kuliah</span>
      </div>
      <div class="p-3 space-y-1">
        @forelse($mataKuliahList as $m)
        @php
          $isActive = $mk && $mk->id === $m->id;
          $count    = \App\Models\BankSoal::where('instruktur_id', $instruktur->id)->where('mata_kuliah_id', $m->id)->count();
        @endphp
        <a href="{{ route('instruktur.bank-soal.index', ['mk' => $m->id]) }}"
          class="mk-item {{ $isActive ? 'active' : '' }}">
          <div class="mk-icon {{ $isActive ? 'bg-white/20' : 'a-bg-lt a-text' }}">
            <i class="fa-solid fa-book-open"></i>
          </div>
          <div class="flex-1 min-w-0">
            <div class="text-[13px] font-semibold truncate {{ $isActive ? 'text-white' : '' }}" style="{{ $isActive ? '' : 'color:var(--text)' }}">{{ $m->nama }}</div>
            <div class="text-[11px] mk-sks" style="{{ $isActive ? '' : 'color:var(--muted)' }}">{{ $m->sks }} SKS · {{ $count }} soal</div>
          </div>
        </a>
        @empty
        <div class="text-center py-8">
          <i class="fa-solid fa-book text-[24px] opacity-20 block mb-2" style="color:var(--muted)"></i>
          <p class="text-[12px]" style="color:var(--muted)">Belum ada mata kuliah</p>
        </div>
        @endforelse
      </div>
    </div>

    {{-- ── Konten Soal ── --}}
    <div>
      @if(!$mk)
        <div class="rounded-2xl border" style="background:var(--surface);border-color:var(--border)">
          <div class="empty-state">
            <div class="a-bg-lt a-text w-16 h-16 rounded-2xl grid place-items-center text-2xl mx-auto mb-4">
              <i class="fa-solid fa-layer-group"></i>
            </div>
            <p class="font-display font-semibold text-[16px] mb-1" style="color:var(--text)">Pilih Mata Kuliah</p>
            <p class="text-[13px]" style="color:var(--muted)">Pilih mata kuliah di sidebar untuk melihat bank soal</p>
          </div>
        </div>
      @else
        {{-- Header konten --}}
        <div class="flex items-center justify-between mb-4">
          <div>
            <h3 class="font-display font-bold text-[16px]" style="color:var(--text)">{{ $mk->nama }}</h3>
            <p class="text-[12px] mt-0.5" style="color:var(--muted)">
              <span class="font-mono a-text">{{ $mk->kode }}</span> ·
              {{ $essay->count() }} essay · {{ $pg->count() }} pilihan ganda
            </p>
          </div>
        </div>

        {{-- Tab --}}
        <div class="tab-bar mb-4">
          <button class="tab-btn active" id="tab-essay" onclick="switchTab('essay')">
            <i class="fa-solid fa-pen-nib mr-1.5 text-[11px]"></i>Essay
            <span class="ml-1 text-[11px] opacity-70">({{ $essay->count() }})</span>
          </button>
          <button class="tab-btn" id="tab-pg" onclick="switchTab('pg')">
            <i class="fa-solid fa-list-ul mr-1.5 text-[11px]"></i>Pilihan Ganda
            <span class="ml-1 text-[11px] opacity-70">({{ $pg->count() }})</span>
          </button>
        </div>

        {{-- Panel Essay --}}
        <div id="panel-essay" class="space-y-3">
          @forelse($essay as $i => $soal)
          <div class="soal-card" id="soal-{{ $soal->id }}">
            <div class="soal-head">
              <div class="soal-num bdg-essay">{{ $i + 1 }}</div>
              <div class="flex-1 min-w-0">
                <div class="text-[13.5px] leading-snug" style="color:var(--text)">{{ $soal->pertanyaan }}</div>
                <div class="flex items-center gap-2 flex-wrap mt-2">
                  <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10.5px] font-semibold bdg-{{ $soal->tingkat_kesulitan }}">
                    {{ ucfirst($soal->tingkat_kesulitan) }}
                  </span>
                  <span class="text-[11px]" style="color:var(--muted)">Bobot: {{ $soal->bobot }}</span>
                </div>
              </div>
              <div class="flex items-center gap-1.5 flex-shrink-0 ml-2">
                <button onclick='editSoal(@json($soal))' title="Edit"
                  class="w-8 h-8 rounded-lg grid place-items-center transition-opacity hover:opacity-70"
                  style="background:var(--surface2);color:var(--sub)">
                  <i class="fa-solid fa-pen text-[11px]"></i>
                </button>
                <button onclick="deleteSoal({{ $soal->id }}, '{{ addslashes(mb_substr($soal->pertanyaan, 0, 40)) }}')" title="Hapus"
                  class="w-8 h-8 rounded-lg grid place-items-center transition-opacity hover:opacity-70"
                  style="background:rgba(239,68,68,.1);color:#f87171">
                  <i class="fa-solid fa-trash text-[11px]"></i>
                </button>
              </div>
            </div>
            @if($soal->pembahasan)
            <div class="soal-body">
              <div class="text-[11.5px] px-3 py-2 rounded-lg" style="background:var(--surface2);color:var(--muted)">
                <i class="fa-solid fa-lightbulb text-amber-400 mr-1.5"></i>
                <span style="color:var(--sub)">Pembahasan:</span> {{ $soal->pembahasan }}
              </div>
            </div>
            @endif
          </div>
          @empty
          <div class="rounded-2xl border" style="background:var(--surface);border-color:var(--border)">
            <div class="empty-state">
              <div class="a-bg-lt a-text w-12 h-12 rounded-2xl grid place-items-center text-xl mx-auto mb-3">
                <i class="fa-solid fa-pen-nib"></i>
              </div>
              <p class="font-semibold text-[14px] mb-1" style="color:var(--text)">Belum ada soal essay</p>
              <p class="text-[12.5px]" style="color:var(--muted)">Klik "Tambah Soal" atau gunakan AI untuk membuat soal</p>
            </div>
          </div>
          @endforelse
        </div>

        {{-- Panel PG --}}
        <div id="panel-pg" class="space-y-3" style="display:none">
          @forelse($pg as $i => $soal)
          <div class="soal-card" id="soal-{{ $soal->id }}">
            <div class="soal-head">
              <div class="soal-num bdg-pg">{{ $i + 1 }}</div>
              <div class="flex-1 min-w-0">
                <div class="text-[13.5px] leading-snug" style="color:var(--text)">{{ $soal->pertanyaan }}</div>
                <div class="flex items-center gap-2 flex-wrap mt-2">
                  <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10.5px] font-semibold bdg-{{ $soal->tingkat_kesulitan }}">
                    {{ ucfirst($soal->tingkat_kesulitan) }}
                  </span>
                  <span class="text-[11px]" style="color:var(--muted)">Bobot: {{ $soal->bobot }}</span>
                </div>
              </div>
              <div class="flex items-center gap-1.5 flex-shrink-0 ml-2">
                <button onclick='editSoal(@json($soal->load("pilihan")))' title="Edit"
                  class="w-8 h-8 rounded-lg grid place-items-center transition-opacity hover:opacity-70"
                  style="background:var(--surface2);color:var(--sub)">
                  <i class="fa-solid fa-pen text-[11px]"></i>
                </button>
                <button onclick="deleteSoal({{ $soal->id }}, '{{ addslashes(mb_substr($soal->pertanyaan, 0, 40)) }}')" title="Hapus"
                  class="w-8 h-8 rounded-lg grid place-items-center transition-opacity hover:opacity-70"
                  style="background:rgba(239,68,68,.1);color:#f87171">
                  <i class="fa-solid fa-trash text-[11px]"></i>
                </button>
              </div>
            </div>
            <div class="soal-body">
              @foreach($soal->pilihan as $p)
              <div class="soal-pilihan {{ $p->is_benar ? 'benar' : 'salah' }}">
                <div class="pilihan-huruf">{{ $p->huruf }}</div>
                <span>{{ $p->teks }}</span>
                @if($p->is_benar)
                  <i class="fa-solid fa-check ml-auto text-[11px]"></i>
                @endif
              </div>
              @endforeach
              @if($soal->pembahasan)
              <div class="text-[11.5px] px-3 py-2 rounded-lg mt-2" style="background:var(--surface2)">
                <i class="fa-solid fa-lightbulb text-amber-400 mr-1.5"></i>
                <span style="color:var(--sub)">Pembahasan:</span>
                <span style="color:var(--muted)">{{ $soal->pembahasan }}</span>
              </div>
              @endif
            </div>
          </div>
          @empty
          <div class="rounded-2xl border" style="background:var(--surface);border-color:var(--border)">
            <div class="empty-state">
              <div class="a-bg-lt a-text w-12 h-12 rounded-2xl grid place-items-center text-xl mx-auto mb-3">
                <i class="fa-solid fa-list-ul"></i>
              </div>
              <p class="font-semibold text-[14px] mb-1" style="color:var(--text)">Belum ada soal pilihan ganda</p>
              <p class="text-[12.5px]" style="color:var(--muted)">Klik "Tambah Soal" atau gunakan AI untuk membuat soal</p>
            </div>
          </div>
          @endforelse
        </div>
      @endif
    </div>

  </div>
</div>

{{-- ══ MODAL TAMBAH / EDIT SOAL ══ --}}
<div id="modal-soal" class="modal-backdrop" style="display:none" onclick="if(event.target===this)closeModal('modal-soal')">
  <div class="modal-box">
    <div class="modal-head">
      <div class="flex items-center gap-2">
        <div class="a-bg-lt a-text w-8 h-8 rounded-xl grid place-items-center text-[13px]">
          <i class="fa-solid fa-pen-to-square"></i>
        </div>
        <span class="font-display font-bold text-[15px]" style="color:var(--text)" id="modal-soal-title">Tambah Soal</span>
      </div>
      <button onclick="closeModal('modal-soal')"
        class="w-8 h-8 rounded-lg grid place-items-center hover:opacity-70 transition-opacity"
        style="background:var(--surface2);color:var(--muted)">
        <i class="fa-solid fa-xmark text-[13px]"></i>
      </button>
    </div>
    <div class="modal-body space-y-4" id="modal-soal-body">

      {{-- Tipe (hanya saat tambah baru) --}}
      <div id="tipe-row">
        <label class="f-label">Tipe Soal</label>
        <div class="flex gap-2">
          <label class="flex-1 flex items-center gap-2 px-3 py-2.5 rounded-xl border cursor-pointer transition-colors" id="lbl-essay"
            style="border-color:var(--ac);background:rgba(var(--ac-rgb),.07)">
            <input type="radio" name="soal_tipe" value="essay" checked onchange="onTipeChange('essay')" class="accent-[var(--ac)]">
            <span class="font-semibold text-[13px]" style="color:var(--text)">
              <i class="fa-solid fa-pen-nib a-text mr-1.5"></i>Essay
            </span>
          </label>
          <label class="flex-1 flex items-center gap-2 px-3 py-2.5 rounded-xl border cursor-pointer transition-colors" id="lbl-pg"
            style="border-color:var(--border)">
            <input type="radio" name="soal_tipe" value="pilihan_ganda" onchange="onTipeChange('pilihan_ganda')" class="accent-[var(--ac)]">
            <span class="font-semibold text-[13px]" style="color:var(--text)">
              <i class="fa-solid fa-list-ul a-text mr-1.5"></i>Pilihan Ganda
            </span>
          </label>
        </div>
      </div>

      {{-- Pertanyaan --}}
      <div>
        <label class="f-label">Pertanyaan <span class="text-rose-400">*</span></label>
        <textarea id="inp-pertanyaan" rows="4" class="f-input" placeholder="Tulis pertanyaan…"></textarea>
      </div>

      {{-- Pilihan Ganda options --}}
      <div id="pg-section" style="display:none">
        <label class="f-label">Pilihan Jawaban <span class="text-rose-400">*</span> <span class="text-[11px] font-normal" style="color:var(--muted)">(tandai jawaban benar)</span></label>
        <div id="pg-options"></div>
        <button type="button" onclick="addPgOption()" class="text-[12px] font-semibold a-text mt-1 hover:underline">
          <i class="fa-solid fa-plus mr-1 text-[10px]"></i>Tambah pilihan
        </button>
      </div>

      {{-- Tingkat & Bobot --}}
      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="f-label">Tingkat Kesulitan</label>
          <select id="inp-tingkat" class="f-input">
            <option value="mudah">Mudah</option>
            <option value="sedang" selected>Sedang</option>
            <option value="sulit">Sulit</option>
          </select>
        </div>
        <div>
          <label class="f-label">Bobot Nilai</label>
          <input type="number" id="inp-bobot" class="f-input" value="10" min="1" max="100">
        </div>
      </div>

      {{-- Pembahasan --}}
      <div>
        <label class="f-label">Pembahasan / Kunci Jawaban <span class="text-[11px] font-normal" style="color:var(--muted)">(opsional)</span></label>
        <textarea id="inp-pembahasan" rows="2" class="f-input" placeholder="Tulis pembahasan atau kunci jawaban…"></textarea>
      </div>

    </div>
    <div class="modal-foot">
      <button onclick="closeModal('modal-soal')"
        class="px-4 py-2 rounded-xl text-[13px] font-semibold border transition-colors"
        style="border-color:var(--border);color:var(--sub)">Batal</button>
      <button onclick="saveSoal()" id="btn-save-soal"
        class="px-5 py-2 rounded-xl text-[13px] font-semibold text-white a-grad">
        <i class="fa-solid fa-floppy-disk mr-1.5 text-[11px]"></i>Simpan Soal
      </button>
    </div>
  </div>
</div>

{{-- ══ MODAL AI GENERATE ══ --}}
<div id="modal-ai" class="modal-backdrop" style="display:none" onclick="if(event.target===this)closeModal('modal-ai')">
  <div class="modal-box" style="max-width:680px">
    <div class="modal-head">
      <div class="flex items-center gap-2">
        <div class="w-8 h-8 rounded-xl grid place-items-center text-[13px]" style="background:rgba(139,92,246,.15);color:#a78bfa">
          <i class="fa-solid fa-wand-magic-sparkles"></i>
        </div>
        <span class="font-display font-bold text-[15px]" style="color:var(--text)">Buat Soal dengan AI</span>
      </div>
      <button onclick="closeModal('modal-ai')"
        class="w-8 h-8 rounded-lg grid place-items-center hover:opacity-70 transition-opacity"
        style="background:var(--surface2);color:var(--muted)">
        <i class="fa-solid fa-xmark text-[13px]"></i>
      </button>
    </div>
    <div class="modal-body space-y-4" id="ai-modal-body">

      {{-- Step 1: Upload + config --}}
      <div id="ai-step-1">
        <div class="px-4 py-3 rounded-xl text-[12.5px]" style="background:rgba(139,92,246,.08);border:1px solid rgba(139,92,246,.2);color:#a78bfa">
          <i class="fa-solid fa-circle-info mr-1.5"></i>
          Upload file PDF materi kuliah. AI akan membaca isinya dan membuat soal secara otomatis.
        </div>

        {{-- PDF Upload --}}
        <div class="mt-4">
          <label class="f-label">File Materi (PDF) <span class="text-rose-400">*</span></label>
          <div id="ai-drop-zone" onclick="document.getElementById('ai-pdf-input').click()"
            class="mt-1 flex flex-col items-center justify-content-center gap-2 py-8 border-2 border-dashed rounded-xl cursor-pointer transition-colors"
            style="border-color:var(--border)"
            ondragover="event.preventDefault();this.style.borderColor='var(--ac)'"
            ondragleave="this.style.borderColor='var(--border)'"
            ondrop="event.preventDefault();this.style.borderColor='var(--border)';handleAiPdfDrop(event)">
            <input type="file" id="ai-pdf-input" accept=".pdf" class="hidden" onchange="handleAiPdfSelect(this)">
            <i class="fa-solid fa-file-pdf text-[32px]" style="color:var(--muted)"></i>
            <p class="text-[13px] font-semibold" style="color:var(--text)" id="ai-pdf-name">Tap untuk pilih PDF</p>
            <p class="text-[11.5px]" style="color:var(--muted)">atau drag & drop di sini · Maks. 20MB</p>
          </div>
        </div>

        {{-- Jumlah soal --}}
        <div class="grid grid-cols-2 gap-3 mt-4">
          <div>
            <label class="f-label"><i class="fa-solid fa-pen-nib a-text mr-1.5"></i>Jumlah Soal Essay</label>
            <input type="number" id="ai-jml-essay" class="f-input" value="3" min="0" max="15">
          </div>
          <div>
            <label class="f-label"><i class="fa-solid fa-list-ul a-text mr-1.5"></i>Jumlah Pilihan Ganda</label>
            <input type="number" id="ai-jml-pg" class="f-input" value="5" min="0" max="20">
          </div>
        </div>
      </div>

      {{-- Step 2: Loading --}}
      <div id="ai-step-loading" style="display:none" class="text-center py-10 space-y-4">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl mx-auto" style="background:rgba(139,92,246,.1)">
          <i class="fa-solid fa-spinner fa-spin text-[24px]" style="color:#a78bfa"></i>
        </div>
        <p class="font-semibold text-[14px]" style="color:var(--text)">AI sedang membuat soal…</p>
        <p class="text-[12.5px]" style="color:var(--muted)">Membaca materi dan menghasilkan pertanyaan, mohon tunggu sebentar</p>
      </div>

      {{-- Step 3: Review hasil --}}
      <div id="ai-step-result" style="display:none">
        <div class="flex items-center justify-between mb-3">
          <p class="font-semibold text-[14px]" style="color:var(--text)">
            Hasil Generate AI — pilih soal yang ingin disimpan
          </p>
          <button onclick="toggleSelectAll()" id="btn-select-all" class="text-[12px] font-semibold a-text hover:underline">
            Pilih Semua
          </button>
        </div>
        <div id="ai-result-list" class="space-y-2 max-h-[380px] overflow-y-auto pr-1"></div>
      </div>

    </div>
    <div class="modal-foot" id="ai-modal-foot">
      <button onclick="closeModal('modal-ai')"
        class="px-4 py-2 rounded-xl text-[13px] font-semibold border transition-colors"
        style="border-color:var(--border);color:var(--sub)">Batal</button>
      <button onclick="runAiGenerate()" id="btn-ai-generate"
        class="px-5 py-2 rounded-xl text-[13px] font-semibold text-white"
        style="background:linear-gradient(135deg,#6366f1,#8b5cf6)">
        <i class="fa-solid fa-wand-magic-sparkles mr-1.5 text-[11px]"></i>Generate Soal
      </button>
    </div>
  </div>
</div>

<div id="bs-toast"></div>
@endsection

@push('scripts')
<script>
const CSRF         = '{{ $csrf }}';
const MK_ID        = {{ $mk?->id ?? 'null' }};
const ROUTE_STORE  = '{{ route("instruktur.bank-soal.store") }}';
const ROUTE_UPDATE = (id) => `{{ url("instruktur/bank-soal") }}/${id}`;
const ROUTE_DELETE = (id) => `{{ url("instruktur/bank-soal") }}/${id}`;
const ROUTE_AI_GEN = '{{ route("instruktur.bank-soal.ai-generate") }}';
const ROUTE_AI_SAV = '{{ route("instruktur.bank-soal.ai-save") }}';

// ── Tab ─────────────────────────────────────────────────────────────────────
function switchTab(tab) {
  document.getElementById('panel-essay').style.display = tab === 'essay' ? '' : 'none';
  document.getElementById('panel-pg').style.display    = tab === 'pg'    ? '' : 'none';
  document.getElementById('tab-essay').classList.toggle('active', tab === 'essay');
  document.getElementById('tab-pg').classList.toggle('active', tab === 'pg');
}

// ── Modal helpers ────────────────────────────────────────────────────────────
function closeModal(id) { document.getElementById(id).style.display = 'none'; }

// ── Soal Modal ───────────────────────────────────────────────────────────────
let _editId   = null;
let _editTipe = null;

function openSoalModal(tipe = null) {
  _editId = null; _editTipe = tipe;
  document.getElementById('modal-soal-title').textContent = 'Tambah Soal';
  document.getElementById('tipe-row').style.display = '';
  document.getElementById('inp-pertanyaan').value   = '';
  document.getElementById('inp-tingkat').value      = 'sedang';
  document.getElementById('inp-bobot').value        = '10';
  document.getElementById('inp-pembahasan').value   = '';
  resetPgOptions();
  if (tipe === 'pilihan_ganda') {
    document.querySelector('input[name=soal_tipe][value=pilihan_ganda]').checked = true;
    onTipeChange('pilihan_ganda');
  } else {
    document.querySelector('input[name=soal_tipe][value=essay]').checked = true;
    onTipeChange('essay');
  }
  document.getElementById('modal-soal').style.display = 'flex';
}

function editSoal(soal) {
  _editId   = soal.id;
  _editTipe = soal.tipe;
  document.getElementById('modal-soal-title').textContent = 'Edit Soal';
  document.getElementById('tipe-row').style.display       = 'none';
  document.getElementById('inp-pertanyaan').value          = soal.pertanyaan;
  document.getElementById('inp-tingkat').value             = soal.tingkat_kesulitan;
  document.getElementById('inp-bobot').value               = soal.bobot;
  document.getElementById('inp-pembahasan').value          = soal.pembahasan ?? '';

  if (soal.tipe === 'pilihan_ganda') {
    document.getElementById('pg-section').style.display = '';
    const opts = document.getElementById('pg-options');
    opts.innerHTML = '';
    (soal.pilihan || []).forEach((p, i) => addPgOption(p.teks, p.is_benar));
  } else {
    document.getElementById('pg-section').style.display = 'none';
  }
  document.getElementById('modal-soal').style.display = 'flex';
}

function onTipeChange(tipe) {
  const isPg = tipe === 'pilihan_ganda';
  document.getElementById('pg-section').style.display = isPg ? '' : 'none';
  document.getElementById('lbl-essay').style.borderColor = !isPg ? 'var(--ac)' : 'var(--border)';
  document.getElementById('lbl-essay').style.background  = !isPg ? 'rgba(var(--ac-rgb),.07)' : 'transparent';
  document.getElementById('lbl-pg').style.borderColor    = isPg  ? 'var(--ac)' : 'var(--border)';
  document.getElementById('lbl-pg').style.background     = isPg  ? 'rgba(var(--ac-rgb),.07)' : 'transparent';
  if (isPg && document.getElementById('pg-options').children.length === 0) resetPgOptions();
}

const HURUF = ['A','B','C','D','E'];
function resetPgOptions() {
  const opts = document.getElementById('pg-options');
  opts.innerHTML = '';
  ['','','',''].forEach((_, i) => addPgOption());
}

function addPgOption(val = '', benar = false) {
  const opts  = document.getElementById('pg-options');
  const idx   = opts.children.length;
  if (idx >= 5) return;
  const huruf = HURUF[idx];
  const div   = document.createElement('div');
  div.className = 'pg-option';
  div.innerHTML = `
    <input type="radio" name="pg_benar" value="${idx}" class="pg-radio" ${benar ? 'checked' : ''}>
    <span style="font-weight:700;font-size:12px;color:var(--muted);min-width:14px">${huruf}</span>
    <input type="text" placeholder="Pilihan ${huruf}…" value="${escH(val)}"
      style="flex:1;background:transparent;border:none;outline:none;font-size:13px;color:var(--text)">
    ${idx >= 4 ? `<button type="button" onclick="this.parentElement.remove()" style="border:none;background:none;color:var(--muted);cursor:pointer;font-size:12px"><i class="fa-solid fa-xmark"></i></button>` : ''}
  `;
  opts.appendChild(div);
}

async function saveSoal() {
  const pertanyaan = document.getElementById('inp-pertanyaan').value.trim();
  if (!pertanyaan) { showToast('Pertanyaan tidak boleh kosong.', true); return; }

  const tipe = _editId ? _editTipe
    : document.querySelector('input[name=soal_tipe]:checked').value;

  const payload = {
    mata_kuliah_id:    MK_ID,
    tipe,
    pertanyaan,
    tingkat_kesulitan: document.getElementById('inp-tingkat').value,
    bobot:             parseInt(document.getElementById('inp-bobot').value),
    pembahasan:        document.getElementById('inp-pembahasan').value.trim() || null,
  };

  if (tipe === 'pilihan_ganda') {
    const opts = document.querySelectorAll('#pg-options .pg-option');
    const benar = document.querySelector('#pg-options input[name=pg_benar]:checked');
    if (!benar) { showToast('Pilih jawaban yang benar.', true); return; }
    payload.pilihan      = Array.from(opts).map(o => ({ teks: o.querySelector('input[type=text]').value.trim() }));
    payload.pilihan_benar = parseInt(benar.value);
    if (payload.pilihan.some(p => !p.teks)) { showToast('Semua pilihan harus diisi.', true); return; }
  }

  const btn = document.getElementById('btn-save-soal');
  btn.disabled = true;

  const isEdit = !!_editId;
  const url    = isEdit ? ROUTE_UPDATE(_editId) : ROUTE_STORE;
  const method = isEdit ? 'PUT' : 'POST';

  try {
    const r = await fetch(url, {
      method,
      headers: { 'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN': CSRF },
      body: JSON.stringify(payload),
    });
    const j = await r.json();
    if (r.ok) {
      showToast(j.message);
      closeModal('modal-soal');
      setTimeout(() => location.reload(), 700);
    } else {
      showToast(j.message || 'Gagal menyimpan.', true);
    }
  } catch { showToast('Koneksi gagal.', true); }
  finally { btn.disabled = false; }
}

async function deleteSoal(id, preview) {
  if (!confirm(`Hapus soal:\n"${preview}…"?\n\nTindakan ini tidak dapat dibatalkan.`)) return;
  try {
    const fd = new FormData(); fd.append('_method','DELETE');
    const r  = await fetch(ROUTE_DELETE(id), {
      method: 'POST',
      headers: { 'Accept':'application/json','X-CSRF-TOKEN': CSRF },
      body: fd,
    });
    const j = await r.json();
    if (r.ok) {
      document.getElementById('soal-' + id)?.remove();
      showToast(j.message);
    } else {
      showToast(j.message || 'Gagal menghapus.', true);
    }
  } catch { showToast('Koneksi gagal.', true); }
}

// ── AI Generate ──────────────────────────────────────────────────────────────
let _aiPdfFile   = null;
let _aiResults   = [];  // { tipe, pertanyaan, pembahasan, tingkat_kesulitan, pilihan?, jawaban_benar? }

function openAiModal() {
  _aiPdfFile = null; _aiResults = [];
  document.getElementById('ai-step-1').style.display       = '';
  document.getElementById('ai-step-loading').style.display = 'none';
  document.getElementById('ai-step-result').style.display  = 'none';
  document.getElementById('ai-pdf-name').textContent       = 'Tap untuk pilih PDF';
  document.getElementById('ai-drop-zone').style.borderColor = 'var(--border)';
  document.getElementById('ai-modal-foot').innerHTML = `
    <button onclick="closeModal('modal-ai')" class="px-4 py-2 rounded-xl text-[13px] font-semibold border transition-colors" style="border-color:var(--border);color:var(--sub)">Batal</button>
    <button onclick="runAiGenerate()" id="btn-ai-generate" class="px-5 py-2 rounded-xl text-[13px] font-semibold text-white" style="background:linear-gradient(135deg,#6366f1,#8b5cf6)">
      <i class="fa-solid fa-wand-magic-sparkles mr-1.5 text-[11px]"></i>Generate Soal
    </button>`;
  document.getElementById('modal-ai').style.display = 'flex';
}

function handleAiPdfDrop(e) {
  const f = e.dataTransfer.files[0];
  if (f && f.type === 'application/pdf') setAiPdf(f);
  else showToast('Hanya file PDF yang diterima.', true);
}
function handleAiPdfSelect(input) {
  if (input.files[0]) setAiPdf(input.files[0]);
}
function setAiPdf(f) {
  _aiPdfFile = f;
  document.getElementById('ai-pdf-name').textContent = f.name;
  document.getElementById('ai-drop-zone').style.borderColor = 'var(--ac)';
}

async function runAiGenerate() {
  if (!_aiPdfFile) { showToast('Pilih file PDF terlebih dahulu.', true); return; }
  if (!MK_ID) { showToast('Pilih mata kuliah terlebih dahulu.', true); return; }

  const jmlEssay = parseInt(document.getElementById('ai-jml-essay').value) || 0;
  const jmlPg    = parseInt(document.getElementById('ai-jml-pg').value)    || 0;
  if (jmlEssay + jmlPg === 0) { showToast('Tentukan jumlah soal yang ingin dibuat.', true); return; }

  document.getElementById('ai-step-1').style.display       = 'none';
  document.getElementById('ai-step-loading').style.display = '';
  document.getElementById('ai-modal-foot').innerHTML = '';

  const fd = new FormData();
  fd.append('mata_kuliah_id', MK_ID);
  fd.append('pdf', _aiPdfFile);
  fd.append('jumlah_essay', jmlEssay);
  fd.append('jumlah_pg', jmlPg);

  try {
    const r = await fetch(ROUTE_AI_GEN, {
      method: 'POST',
      headers: { 'Accept':'application/json','X-CSRF-TOKEN': CSRF },
      body: fd,
    });
    const j = await r.json();

    if (!r.ok || j.error) {
      showToast(j.error || 'AI gagal menghasilkan soal.', true);
      document.getElementById('ai-step-loading').style.display = 'none';
      document.getElementById('ai-step-1').style.display       = '';
      document.getElementById('ai-modal-foot').innerHTML = `
        <button onclick="closeModal('modal-ai')" class="px-4 py-2 rounded-xl text-[13px] font-semibold border transition-colors" style="border-color:var(--border);color:var(--sub)">Batal</button>
        <button onclick="runAiGenerate()" class="px-5 py-2 rounded-xl text-[13px] font-semibold text-white" style="background:linear-gradient(135deg,#6366f1,#8b5cf6)">
          <i class="fa-solid fa-rotate-right mr-1.5"></i>Coba Lagi</button>`;
      return;
    }

    _aiResults = [
      ...(j.essay || []).map(s => ({ ...s, tipe: 'essay', _sel: true })),
      ...(j.pilihan_ganda || []).map(s => ({ ...s, tipe: 'pilihan_ganda', _sel: true })),
    ];

    renderAiResults();

  } catch(e) {
    showToast('Koneksi gagal.', true);
    document.getElementById('ai-step-loading').style.display = 'none';
    document.getElementById('ai-step-1').style.display       = '';
  }
}

function renderAiResults() {
  document.getElementById('ai-step-loading').style.display = 'none';
  document.getElementById('ai-step-result').style.display  = '';

  const list = document.getElementById('ai-result-list');
  list.innerHTML = '';
  _aiResults.forEach((soal, idx) => {
    const div = document.createElement('div');
    div.className = 'ai-result-item selected';
    div.id = 'ai-item-' + idx;
    div.onclick = () => toggleAiItem(idx);

    const bdgCls  = soal.tipe === 'essay' ? 'bdg-essay' : 'bdg-pg';
    const bdgLbl  = soal.tipe === 'essay' ? 'Essay' : 'Pilihan Ganda';
    const tingkat = soal.tingkat_kesulitan ?? 'sedang';
    const tdgCls  = 'bdg-' + tingkat;

    let pilihanHtml = '';
    if (soal.tipe === 'pilihan_ganda' && soal.pilihan) {
      const huruf = ['A','B','C','D','E'];
      pilihanHtml = '<div class="mt-2 space-y-1">' + soal.pilihan.map((p, i) => `
        <div class="flex items-center gap-2 text-[12px] ${i === soal.jawaban_benar ? 'font-semibold' : ''}" style="color:${i === soal.jawaban_benar ? '#10b981' : 'var(--muted)'}">
          <span style="min-width:16px;font-weight:700">${huruf[i]}</span>
          <span>${escH(p)}</span>
          ${i === soal.jawaban_benar ? '<i class="fa-solid fa-check ml-auto text-[10px]"></i>' : ''}
        </div>`).join('') + '</div>';
    }

    div.innerHTML = `
      <div class="flex items-start gap-3">
        <div class="ai-check mt-0.5" id="ai-chk-${idx}">
          <i class="fa-solid fa-check text-white text-[9px]"></i>
        </div>
        <div class="flex-1 min-w-0">
          <div class="flex items-center gap-2 mb-1.5 flex-wrap">
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10.5px] font-semibold ${bdgCls}">${bdgLbl}</span>
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10.5px] font-semibold ${tdgCls}">${ucFirst(tingkat)}</span>
          </div>
          <p class="text-[13px] leading-snug" style="color:var(--text)">${escH(soal.pertanyaan)}</p>
          ${pilihanHtml}
          ${soal.pembahasan ? `<p class="text-[11.5px] mt-2 px-3 py-1.5 rounded-lg" style="background:var(--surface2);color:var(--muted)"><i class="fa-solid fa-lightbulb text-amber-400 mr-1.5"></i>${escH(soal.pembahasan)}</p>` : ''}
        </div>
      </div>`;
    list.appendChild(div);
  });

  document.getElementById('ai-modal-foot').innerHTML = `
    <button onclick="closeModal('modal-ai')" class="px-4 py-2 rounded-xl text-[13px] font-semibold border transition-colors" style="border-color:var(--border);color:var(--sub)">Batal</button>
    <button onclick="saveAiResults()" id="btn-ai-save" class="px-5 py-2 rounded-xl text-[13px] font-semibold text-white" style="background:linear-gradient(135deg,#10b981,#059669)">
      <i class="fa-solid fa-floppy-disk mr-1.5 text-[11px]"></i>Simpan yang Dipilih
    </button>`;
}

function toggleAiItem(idx) {
  _aiResults[idx]._sel = !_aiResults[idx]._sel;
  const el  = document.getElementById('ai-item-' + idx);
  const chk = document.getElementById('ai-chk-' + idx);
  el.classList.toggle('selected', _aiResults[idx]._sel);
  chk.style.background    = _aiResults[idx]._sel ? 'var(--ac)' : 'transparent';
  chk.style.borderColor   = _aiResults[idx]._sel ? 'var(--ac)' : 'var(--border)';
}

function toggleSelectAll() {
  const allSel = _aiResults.every(s => s._sel);
  _aiResults.forEach((_, i) => { _aiResults[i]._sel = !allSel; toggleAiItem(i); _aiResults[i]._sel = !allSel; });
  _aiResults.forEach((_, i) => {
    const el  = document.getElementById('ai-item-' + i);
    const chk = document.getElementById('ai-chk-' + i);
    el.classList.toggle('selected', !allSel);
    chk.style.background  = !allSel ? 'var(--ac)' : 'transparent';
    chk.style.borderColor = !allSel ? 'var(--ac)' : 'var(--border)';
    _aiResults[i]._sel    = !allSel;
  });
}

async function saveAiResults() {
  const selected = _aiResults.filter(s => s._sel);
  if (selected.length === 0) { showToast('Pilih minimal 1 soal untuk disimpan.', true); return; }

  const btn = document.getElementById('btn-ai-save');
  btn.disabled = true;

  try {
    const r = await fetch(ROUTE_AI_SAV, {
      method: 'POST',
      headers: { 'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN': CSRF },
      body: JSON.stringify({ mata_kuliah_id: MK_ID, soal: selected }),
    });
    const j = await r.json();
    if (r.ok) {
      showToast(j.message);
      closeModal('modal-ai');
      setTimeout(() => location.reload(), 700);
    } else {
      showToast(j.message || 'Gagal menyimpan.', true);
      btn.disabled = false;
    }
  } catch { showToast('Koneksi gagal.', true); btn.disabled = false; }
}

// ── Toast ─────────────────────────────────────────────────────────────────────
let _tt;
function showToast(msg, err = false) {
  const el = document.getElementById('bs-toast');
  el.textContent = msg;
  el.style.color       = err ? '#fca5a5' : 'var(--text)';
  el.style.borderColor = err ? 'rgba(239,68,68,.3)' : 'var(--border)';
  el.classList.add('show');
  clearTimeout(_tt);
  _tt = setTimeout(() => el.classList.remove('show'), 3200);
}

// ── Helpers ──────────────────────────────────────────────────────────────────
function escH(s) {
  return String(s || '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
function ucFirst(s) { return s ? s.charAt(0).toUpperCase() + s.slice(1) : ''; }
</script>
@endpush
