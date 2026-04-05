@extends('layouts.instruktur')
@section('title', 'Penilaian — ' . $ujian->judul)
@section('page-title', 'Penilaian Ujian')

@push('styles')
<style>
/* ── Page header ── */
.pn-header { display:flex; align-items:flex-start; justify-content:space-between; gap:16px; margin-bottom:20px; flex-wrap:wrap; }
.pn-back { display:inline-flex; align-items:center; gap:6px; font-size:12.5px; color:var(--muted); text-decoration:none; padding:6px 10px; border-radius:8px; border:1px solid var(--border); background:var(--surface); transition:all .15s; }
.pn-back:hover { border-color:var(--ac); color:var(--ac); }
.pn-title { font-size:18px; font-weight:700; color:var(--text); margin:0; }
.pn-subtitle { font-size:12.5px; color:var(--muted); margin-top:3px; }

/* ── Stats bar ── */
.pn-stats { display:flex; gap:12px; flex-wrap:wrap; margin-bottom:20px; }
.pn-stat { background:var(--surface); border:1px solid var(--border); border-radius:12px; padding:12px 16px; flex:1; min-width:130px; }
.pn-stat-val { font-size:22px; font-weight:700; color:var(--text); line-height:1; }
.pn-stat-label { font-size:11.5px; color:var(--muted); margin-top:4px; }

/* ── Table card ── */
.tbl-card { background:var(--surface); border:1px solid var(--border); border-radius:16px; overflow:hidden; }
.tbl-toolbar { padding:12px 16px; display:flex; align-items:center; justify-content:space-between; border-bottom:1px solid var(--border); gap:10px; flex-wrap:wrap; }
.tbl-toolbar-title { font-size:13.5px; font-weight:700; color:var(--text); }
.tbl-wrap { overflow-x:auto; }
table.pn-tbl { width:100%; border-collapse:collapse; min-width:700px; }
table.pn-tbl th { padding:10px 14px; font-size:11.5px; font-weight:600; color:var(--muted); text-align:left; border-bottom:1px solid var(--border); white-space:nowrap; background:var(--surface2); }
table.pn-tbl td { padding:11px 14px; font-size:13px; color:var(--text); border-bottom:1px solid var(--border); vertical-align:middle; }
table.pn-tbl tr:last-child td { border-bottom:none; }
table.pn-tbl tbody tr { transition:background .1s; }
table.pn-tbl tbody tr:hover { background:var(--surface2); }

/* ── Progress bar ── */
.prog-wrap { display:flex; align-items:center; gap:8px; }
.prog-bar { flex:1; height:6px; border-radius:3px; background:var(--border); overflow:hidden; min-width:60px; }
.prog-fill { height:100%; border-radius:3px; background:var(--ac); transition:width .3s; }
.prog-fill.done { background:#10b981; }
.prog-label { font-size:11px; color:var(--muted); white-space:nowrap; }

/* ── Status badges ── */
.bdg { display:inline-flex; align-items:center; gap:4px; padding:3px 9px; border-radius:20px; font-size:11.5px; font-weight:600; }
.bdg-draft  { background:rgba(100,116,139,.12); color:#94a3b8; }
.bdg-public { background:rgba(16,185,129,.12);  color:#10b981; }
.bdg-warn   { background:rgba(245,158,11,.12);  color:#f59e0b; }

/* ── Action buttons ── */
.btn-xs { display:inline-flex; align-items:center; gap:5px; padding:5px 10px; border-radius:8px; font-size:12px; font-weight:600; border:none; cursor:pointer; transition:all .15s; }
.btn-xs-ac  { background:rgba(var(--ac-rgb,99,102,241),.1); color:var(--ac); }
.btn-xs-ac:hover { background:var(--ac); color:#fff; }
.btn-xs-green { background:rgba(16,185,129,.1); color:#10b981; }
.btn-xs-green:hover { background:#10b981; color:#fff; }
.btn-xs-red { background:rgba(239,68,68,.1); color:#ef4444; }
.btn-xs-red:hover { background:#ef4444; color:#fff; }
.btn-xs-gray { background:var(--surface2); color:var(--muted); }
.btn-xs-gray:hover { background:var(--border); color:var(--text); }

/* ── Modal ── */
.modal-backdrop { position:fixed; inset:0; background:rgba(0,0,0,.65); z-index:200; display:flex; align-items:flex-start; justify-content:center; padding:20px 16px; backdrop-filter:blur(3px); overflow-y:auto; }
.modal-box { background:var(--surface); border:1px solid var(--border); border-radius:20px; width:100%; max-width:780px; margin:auto; display:flex; flex-direction:column; box-shadow:0 24px 64px rgba(0,0,0,.3); }
.modal-head { padding:18px 22px 14px; display:flex; align-items:center; gap:12px; border-bottom:1px solid var(--border); }
.modal-head-icon { width:38px; height:38px; border-radius:11px; background:rgba(var(--ac-rgb,99,102,241),.12); display:grid; place-items:center; font-size:15px; flex-shrink:0; }
.modal-head-title { flex:1; font-size:16px; font-weight:700; color:var(--text); }
.modal-head-sub { font-size:12px; color:var(--muted); margin-top:2px; }
.modal-close { width:32px; height:32px; border-radius:8px; border:none; background:var(--surface2); color:var(--muted); cursor:pointer; display:grid; place-items:center; font-size:13px; transition:all .15s; }
.modal-close:hover { background:var(--border); color:var(--text); }
.modal-body { padding:20px 22px; overflow-y:auto; max-height:70vh; }
.modal-foot { padding:14px 22px; border-top:1px solid var(--border); display:flex; gap:8px; justify-content:flex-end; flex-wrap:wrap; }

/* ── Essay card ── */
.essay-card { border:1px solid var(--border); border-radius:14px; overflow:hidden; margin-bottom:14px; }
.essay-card:last-child { margin-bottom:0; }
.essay-card-head { padding:10px 14px; background:var(--surface2); display:flex; align-items:center; justify-content:space-between; gap:8px; }
.essay-card-num { font-size:11.5px; font-weight:700; color:var(--muted); }
.essay-card-bobot { font-size:11.5px; color:var(--muted); }
.essay-card-body { padding:14px; }
.essay-q { font-size:13.5px; color:var(--text); font-weight:600; margin-bottom:8px; line-height:1.5; }
.essay-ans { font-size:13px; color:var(--sub); background:var(--surface2); border:1px solid var(--border); border-radius:10px; padding:10px 12px; line-height:1.6; white-space:pre-wrap; word-break:break-word; margin-bottom:12px; min-height:48px; }
.essay-ans.empty { color:var(--muted); font-style:italic; }
.essay-grade-row { display:grid; grid-template-columns:110px 1fr; gap:10px; align-items:start; }
.essay-ai-fb { font-size:12px; color:var(--muted); background:rgba(var(--ac-rgb,99,102,241),.05); border:1px solid rgba(var(--ac-rgb,99,102,241),.15); border-radius:8px; padding:8px 10px; margin-top:8px; line-height:1.5; }
.essay-ai-fb-label { font-size:10.5px; font-weight:700; color:var(--ac); margin-bottom:3px; }

/* ── Form ── */
.f-label { font-size:12px; font-weight:600; color:var(--muted); margin-bottom:4px; display:block; }
.f-input { width:100%; padding:8px 12px; border:1px solid var(--border); border-radius:9px; background:var(--surface); color:var(--text); font-size:13px; outline:none; transition:border-color .15s; }
.f-input:focus { border-color:var(--ac); }
.f-textarea { resize:vertical; min-height:70px; font-family:inherit; line-height:1.5; }

/* ── Divider ── */
.sect-divider { height:1px; background:var(--border); margin:16px 0; }

/* ── Empty state ── */
.empty-state { text-align:center; padding:48px 16px; color:var(--muted); }
.empty-state-icon { font-size:36px; margin-bottom:12px; opacity:.5; }
.empty-state-text { font-size:14px; }
</style>
@endpush

@section('content')

{{-- Header --}}
<div class="pn-header">
  <div>
    <a href="{{ route('instruktur.ujian.index') }}" class="pn-back mb-2">
      <i class="fas fa-arrow-left"></i> Kembali ke Ujian
    </a>
    <h1 class="pn-title mt-2">{{ $ujian->judul }}</h1>
    <p class="pn-subtitle">
      {{ $ujian->kelas->mataKuliah->nama ?? '—' }} &middot;
      {{ $ujian->kelas->nama ?? '—' }} &middot;
      Penilaian Essay &amp; Nilai
    </p>
  </div>
  <button onclick="publishAll()" id="btn-publish-all"
    class="btn-xs btn-xs-green" style="padding:8px 14px;font-size:13px;">
    <i class="fas fa-check-double"></i> Publish Semua Nilai
  </button>
</div>

{{-- Stats --}}
@php
  $totalSubmit  = $sesiList->count();
  $totalPublic  = $sesiList->where('nilai_status', 'public')->count();
  $essayExists  = $sesiList->sum('_essay_total') > 0;
  $essayDone    = $sesiList->sum('_essay_graded');
  $essayTotal   = $sesiList->sum('_essay_total');
@endphp
<div class="pn-stats">
  <div class="pn-stat">
    <div class="pn-stat-val">{{ $totalSubmit }}</div>
    <div class="pn-stat-label">Telah Mengumpulkan</div>
  </div>
  <div class="pn-stat">
    <div class="pn-stat-val" id="stat-public">{{ $totalPublic }}</div>
    <div class="pn-stat-label">Nilai Dipublikasi</div>
  </div>
  @if($essayExists)
  <div class="pn-stat">
    <div class="pn-stat-val">{{ $essayDone }} / {{ $essayTotal }}</div>
    <div class="pn-stat-label">Essay Dinilai</div>
  </div>
  @endif
  @if($totalSubmit > 0 && $sesiList->whereNotNull('nilai')->count() > 0)
  <div class="pn-stat">
    <div class="pn-stat-val">{{ number_format($sesiList->whereNotNull('nilai')->avg('nilai'), 1) }}</div>
    <div class="pn-stat-label">Rata-rata Nilai</div>
  </div>
  @endif
</div>

{{-- Table --}}
<div class="tbl-card">
  <div class="tbl-toolbar">
    <span class="tbl-toolbar-title">
      <i class="fas fa-list-check" style="color:var(--ac);margin-right:6px;"></i>
      Daftar Peserta
    </span>
  </div>

  @if($sesiList->isEmpty())
  <div class="empty-state">
    <div class="empty-state-icon"><i class="fas fa-inbox"></i></div>
    <div class="empty-state-text">Belum ada mahasiswa yang mengumpulkan ujian.</div>
  </div>
  @else
  <div class="tbl-wrap">
    <table class="pn-tbl" id="pn-table">
      <thead>
        <tr>
          <th>#</th>
          <th>Mahasiswa</th>
          <th>Dikumpulkan</th>
          @if($essayExists)<th>Essay</th>@endif
          <th>Nilai Akhir</th>
          <th>Status</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        @foreach($sesiList as $i => $sesi)
        <tr id="row-{{ $sesi->id }}" data-sesi="{{ $sesi->id }}">
          <td class="t-muted" style="font-size:12px;">{{ $i + 1 }}</td>
          <td>
            <div style="font-weight:600;font-size:13.5px;">{{ $sesi->mahasiswa->nama ?? '—' }}</div>
            <div style="font-size:11.5px;color:var(--muted);">{{ $sesi->mahasiswa->nim ?? '' }}</div>
          </td>
          <td style="font-size:12.5px;color:var(--muted);">
            {{ $sesi->submitted_at->format('d M Y, H:i') }}
          </td>
          @if($essayExists)
          <td>
            @if($sesi->_essay_total > 0)
            <div class="prog-wrap">
              <div class="prog-bar">
                <div class="prog-fill {{ $sesi->_essay_graded >= $sesi->_essay_total ? 'done' : '' }}"
                  style="width:{{ $sesi->_essay_total > 0 ? round($sesi->_essay_graded / $sesi->_essay_total * 100) : 0 }}%">
                </div>
              </div>
              <span class="prog-label">{{ $sesi->_essay_graded }}/{{ $sesi->_essay_total }}</span>
            </div>
            @else
            <span style="font-size:12px;color:var(--muted);">—</span>
            @endif
          </td>
          @endif
          <td id="nilai-{{ $sesi->id }}" style="font-weight:700;font-size:14px;color:var(--text);">
            {{ $sesi->nilai !== null ? number_format($sesi->nilai, 1) : '—' }}
          </td>
          <td id="status-{{ $sesi->id }}">
            <span class="bdg {{ $sesi->nilai_status === 'public' ? 'bdg-public' : 'bdg-draft' }}">
              <i class="fas {{ $sesi->nilai_status === 'public' ? 'fa-eye' : 'fa-eye-slash' }}"></i>
              {{ $sesi->nilai_status === 'public' ? 'Publik' : 'Draft' }}
            </span>
          </td>
          <td>
            <div style="display:flex;gap:6px;flex-wrap:wrap;">
              @if($sesi->_essay_total > 0)
              <button onclick="openGrading({{ $sesi->id }})"
                class="btn-xs btn-xs-ac">
                <i class="fas fa-pen"></i> Nilai Essay
              </button>
              @endif
              <button onclick="togglePublish({{ $sesi->id }}, '{{ $sesi->nilai_status }}')"
                id="pub-btn-{{ $sesi->id }}"
                class="btn-xs {{ $sesi->nilai_status === 'public' ? 'btn-xs-gray' : 'btn-xs-green' }}">
                <i class="fas {{ $sesi->nilai_status === 'public' ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                {{ $sesi->nilai_status === 'public' ? 'Unpublish' : 'Publish' }}
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

{{-- Grading Modal --}}
<div id="grade-modal" class="modal-backdrop" style="display:none;" onclick="if(event.target===this)closeModal()">
  <div class="modal-box">
    <div class="modal-head">
      <div class="modal-head-icon"><i class="fas fa-pen-to-square" style="color:var(--ac)"></i></div>
      <div>
        <div class="modal-head-title" id="modal-student-name">Penilaian Essay</div>
        <div class="modal-head-sub" id="modal-student-nim"></div>
      </div>
      <button class="modal-close" onclick="closeModal()"><i class="fas fa-times"></i></button>
    </div>
    <div class="modal-body" id="modal-body">
      <div style="text-align:center;padding:40px;color:var(--muted);">
        <i class="fas fa-spinner fa-spin fa-2x"></i>
        <div style="margin-top:10px;font-size:13px;">Memuat soal...</div>
      </div>
    </div>
    <div class="modal-foot">
      <button onclick="aiGrade()" id="btn-ai-grade"
        class="btn-xs btn-xs-ac" style="padding:8px 14px;font-size:13px;">
        <i class="fas fa-robot"></i> AI Grade Semua Essay
      </button>
      <button onclick="saveAllGrades()" id="btn-save"
        class="btn-xs btn-xs-green" style="padding:8px 14px;font-size:13px;">
        <i class="fas fa-floppy-disk"></i> Simpan Semua
      </button>
      <button onclick="closeModal()" class="btn-xs btn-xs-gray" style="padding:8px 14px;font-size:13px;">
        Tutup
      </button>
    </div>
  </div>
</div>

{{-- Toast --}}
<div id="toast" style="position:fixed;bottom:24px;right:24px;z-index:999;display:none;max-width:340px;">
  <div style="background:var(--surface);border:1px solid var(--border);border-radius:12px;padding:12px 16px;box-shadow:0 8px 30px rgba(0,0,0,.2);display:flex;align-items:center;gap:10px;">
    <span id="toast-icon" style="font-size:16px;"></span>
    <span id="toast-msg" style="font-size:13.5px;color:var(--text);flex:1;"></span>
  </div>
</div>

@endsection

@push('scripts')
<script>
const ROUTES = {
  aiGrade:    (sesiId) => `{{ url('instruktur/ujian/' . $ujian->id . '/penilaian') }}/${sesiId}/ai-grade`,
  gradeJawaban: (sesiId, jawId) => `{{ url('instruktur/ujian/' . $ujian->id . '/penilaian') }}/${sesiId}/jawaban/${jawId}/grade`,
  publish:    (sesiId) => `{{ url('instruktur/ujian/' . $ujian->id . '/penilaian') }}/${sesiId}/publish`,
  publishAll: `{{ route('instruktur.ujian.penilaian.publish-all', $ujian) }}`,
};

const CSRF = '{{ csrf_token() }}';

// ── Sesi data from PHP ──
@php
$sesiDataJs = $sesiList->map(function ($s) {
    return [
        'id'           => $s->id,
        'mahasiswa'    => ['nama' => $s->mahasiswa->nama ?? '', 'nim' => $s->mahasiswa->nim ?? ''],
        'nilai_status' => $s->nilai_status,
        'essay_total'  => $s->_essay_total,
        'jawaban'      => $s->jawaban->filter(function ($j) { return $j->soal && $j->soal->tipe === 'essay'; })->values()->map(function ($j) {
            return [
                'id'                  => $j->id,
                'pertanyaan'          => $j->soal->pertanyaan ?? '',
                'bobot'               => $j->soal->bobot ?? 10,
                'pembahasan'          => $j->soal->pembahasan ?? '',
                'jawaban_essay'       => $j->jawaban_essay ?? '',
                'nilai'               => $j->nilai,
                'feedback_ai'         => $j->feedback_ai ?? '',
                'feedback_instruktur' => $j->feedback_instruktur ?? '',
            ];
        })->values(),
    ];
})->values();
@endphp
const sesiData = {!! json_encode($sesiDataJs) !!};

let currentSesiId = null;

// ── Modal ──
function openGrading(sesiId) {
  currentSesiId = sesiId;
  const sesi = sesiData.find(s => s.id === sesiId);
  if (!sesi) return;

  document.getElementById('modal-student-name').textContent = sesi.mahasiswa.nama;
  document.getElementById('modal-student-nim').textContent  = sesi.mahasiswa.nim;
  renderEssayCards(sesi);
  document.getElementById('grade-modal').style.display = 'flex';
  document.body.style.overflow = 'hidden';
}

function closeModal() {
  document.getElementById('grade-modal').style.display = 'none';
  document.body.style.overflow = '';
  currentSesiId = null;
}

function renderEssayCards(sesi) {
  const body = document.getElementById('modal-body');
  if (!sesi.jawaban.length) {
    body.innerHTML = '<div class="empty-state"><div class="empty-state-icon"><i class="fas fa-check-circle"></i></div><div class="empty-state-text">Tidak ada soal essay.</div></div>';
    return;
  }

  body.innerHTML = sesi.jawaban.map((j, idx) => `
    <div class="essay-card" id="ec-${j.id}">
      <div class="essay-card-head">
        <span class="essay-card-num">Soal Essay ${idx + 1}</span>
        <span class="essay-card-bobot">Bobot: ${j.bobot}</span>
      </div>
      <div class="essay-card-body">
        <div class="essay-q">${escHtml(j.pertanyaan)}</div>
        <div class="essay-ans ${j.jawaban_essay ? '' : 'empty'}">${j.jawaban_essay ? escHtml(j.jawaban_essay) : '(Tidak ada jawaban)'}</div>
        ${j.pembahasan ? `<div style="font-size:11.5px;color:var(--muted);margin-bottom:10px;"><span style="font-weight:700;">Kunci/Rubrik:</span> ${escHtml(j.pembahasan)}</div>` : ''}
        <div class="essay-grade-row">
          <div>
            <label class="f-label">Nilai (0–${j.bobot})</label>
            <input type="number" class="f-input" id="val-${j.id}"
              min="0" max="${j.bobot}" step="1"
              value="${j.nilai !== null && j.nilai !== undefined ? j.nilai : ''}"
              placeholder="0–${j.bobot}" />
          </div>
          <div>
            <label class="f-label">Feedback Instruktur</label>
            <textarea class="f-input f-textarea" id="fb-${j.id}" rows="2"
              placeholder="Opsional...">${escHtml(j.feedback_instruktur || '')}</textarea>
          </div>
        </div>
        ${j.feedback_ai ? `
        <div class="essay-ai-fb" id="ai-fb-${j.id}">
          <div class="essay-ai-fb-label"><i class="fas fa-robot"></i> Feedback AI</div>
          ${escHtml(j.feedback_ai)}
        </div>` : `<div class="essay-ai-fb" id="ai-fb-${j.id}" style="display:none;"></div>`}
      </div>
    </div>
  `).join('');
}

function escHtml(str) {
  return String(str)
    .replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;')
    .replace(/"/g,'&quot;').replace(/'/g,'&#39;');
}

// ── Save all grades ──
async function saveAllGrades() {
  const sesi = sesiData.find(s => s.id === currentSesiId);
  if (!sesi) return;

  const btn = document.getElementById('btn-save');
  btn.disabled = true;
  btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';

  let allOk = true;
  for (const j of sesi.jawaban) {
    const nilaiEl = document.getElementById(`val-${j.id}`);
    const fbEl    = document.getElementById(`fb-${j.id}`);
    if (!nilaiEl) continue;
    const nilai = nilaiEl.value.trim();
    if (nilai === '') continue; // skip ungraded

    const res  = await fetch(ROUTES.gradeJawaban(currentSesiId, j.id), {
      method: 'POST',
      headers: {'Content-Type':'application/json','X-CSRF-TOKEN':CSRF,'Accept':'application/json'},
      body: JSON.stringify({ nilai: parseFloat(nilai), feedback_instruktur: fbEl?.value || '' }),
    });
    const data = await res.json();
    if (!data.ok) { allOk = false; showToast('Gagal menyimpan nilai: ' + (data.message || ''), 'error'); }
    else {
      j.nilai = parseFloat(nilai);
      j.feedback_instruktur = fbEl?.value || '';
    }
  }

  btn.disabled = false;
  btn.innerHTML = '<i class="fas fa-floppy-disk"></i> Simpan Semua';
  if (allOk) showToast('Nilai berhasil disimpan.', 'success');
}

// ── AI Grade ──
async function aiGrade() {
  if (!currentSesiId) return;

  const btn = document.getElementById('btn-ai-grade');
  btn.disabled = true;
  btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> AI sedang menilai...';

  const res  = await fetch(ROUTES.aiGrade(currentSesiId), {
    method: 'POST',
    headers: {'X-CSRF-TOKEN':CSRF,'Accept':'application/json'},
  });
  const data = await res.json();

  btn.disabled = false;
  btn.innerHTML = '<i class="fas fa-robot"></i> AI Grade Semua Essay';

  if (!data.ok) { showToast(data.message || 'Gagal AI grade.', 'error'); return; }

  const sesi = sesiData.find(s => s.id === currentSesiId);
  for (const r of (data.results || [])) {
    if (!r.ok) continue;
    const j = sesi?.jawaban.find(x => x.id === r.jawaban_id);
    if (j) {
      j.nilai      = r.nilai;
      j.feedback_ai = r.feedback || '';
    }
    // Update UI fields
    const valEl  = document.getElementById(`val-${r.jawaban_id}`);
    const aiFbEl = document.getElementById(`ai-fb-${r.jawaban_id}`);
    if (valEl && r.nilai !== undefined) valEl.value = r.nilai;
    if (aiFbEl && r.feedback) {
      aiFbEl.style.display = '';
      aiFbEl.innerHTML = `<div class="essay-ai-fb-label"><i class="fas fa-robot"></i> Feedback AI</div>${escHtml(r.feedback)}`;
    }
  }

  showToast(`AI selesai menilai ${data.results?.filter(r=>r.ok).length ?? 0} jawaban essay.`, 'success');
}

// ── Publish toggle ──
async function togglePublish(sesiId, currentStatus) {
  const newStatus = currentStatus === 'public' ? 'draft' : 'public';

  const res  = await fetch(ROUTES.publish(sesiId), {
    method: 'POST',
    headers: {'Content-Type':'application/json','X-CSRF-TOKEN':CSRF,'Accept':'application/json'},
    body: JSON.stringify({ status: newStatus }),
  });
  const data = await res.json();
  if (!data.ok) { showToast('Gagal mengubah status.', 'error'); return; }

  // Update sesiData
  const sesi = sesiData.find(s => s.id === sesiId);
  if (sesi) sesi.nilai_status = data.status;

  // Update row UI
  updateRowStatus(sesiId, data.status, data.nilai);
  updateStatPublic();
  showToast(data.status === 'public' ? 'Nilai dipublikasikan.' : 'Nilai dikembalikan ke draft.', 'success');
}

function updateRowStatus(sesiId, status, nilai) {
  const isPublic = status === 'public';

  const statusEl = document.getElementById(`status-${sesiId}`);
  if (statusEl) {
    statusEl.innerHTML = `<span class="bdg ${isPublic ? 'bdg-public' : 'bdg-draft'}">
      <i class="fas ${isPublic ? 'fa-eye' : 'fa-eye-slash'}"></i>
      ${isPublic ? 'Publik' : 'Draft'}
    </span>`;
  }

  const pubBtn = document.getElementById(`pub-btn-${sesiId}`);
  if (pubBtn) {
    pubBtn.className = `btn-xs ${isPublic ? 'btn-xs-gray' : 'btn-xs-green'}`;
    pubBtn.innerHTML = `<i class="fas ${isPublic ? 'fa-eye-slash' : 'fa-eye'}"></i> ${isPublic ? 'Unpublish' : 'Publish'}`;
    pubBtn.setAttribute('onclick', `togglePublish(${sesiId}, '${status}')`);
  }

  if (nilai !== null && nilai !== undefined) {
    const nilaiEl = document.getElementById(`nilai-${sesiId}`);
    if (nilaiEl) nilaiEl.textContent = parseFloat(nilai).toFixed(1);
  }
}

function updateStatPublic() {
  const count = sesiData.filter(s => s.nilai_status === 'public').length;
  const el = document.getElementById('stat-public');
  if (el) el.textContent = count;
}

// ── Publish All ──
async function publishAll() {
  const btn = document.getElementById('btn-publish-all');
  btn.disabled = true;
  btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';

  const res  = await fetch(ROUTES.publishAll, {
    method: 'POST',
    headers: {'X-CSRF-TOKEN':CSRF,'Accept':'application/json'},
  });
  const data = await res.json();

  btn.disabled = false;
  btn.innerHTML = '<i class="fas fa-check-double"></i> Publish Semua Nilai';

  if (!data.ok) { showToast('Gagal publish semua.', 'error'); return; }

  // Update all in sesiData + UI
  sesiData.forEach(s => {
    s.nilai_status = 'public';
    updateRowStatus(s.id, 'public', null);
  });
  updateStatPublic();
  showToast(`${data.count} nilai berhasil dipublikasikan.`, 'success');
}

// ── Toast ──
let toastTimer;
function showToast(msg, type = 'success') {
  const el   = document.getElementById('toast');
  const icon = document.getElementById('toast-icon');
  const msgEl= document.getElementById('toast-msg');

  icon.textContent = type === 'success' ? '✅' : '❌';
  msgEl.textContent = msg;
  el.style.display = 'block';

  clearTimeout(toastTimer);
  toastTimer = setTimeout(() => { el.style.display = 'none'; }, 3500);
}

// Close modal on Escape
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });
</script>
@endpush
