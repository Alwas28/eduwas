<?php

use App\Http\Controllers\Admin\AccessController;
use App\Http\Controllers\Instruktur\AiChatController as InstrukturAiChat;
use App\Http\Controllers\Instruktur\BankSoalController as InstrukturBankSoal;
use App\Http\Controllers\Instruktur\UjianController as InstrukturUjian;
use App\Http\Controllers\Instruktur\DashboardController as InstrukturDashboard;
use App\Http\Controllers\Instruktur\KelasController as InstrukturKelas;
use App\Http\Controllers\Instruktur\MateriController as InstrukturMateri;
use App\Http\Controllers\Instruktur\PokokBahasanController as InstrukturPB;
use App\Http\Controllers\Instruktur\ProfileController as InstrukturProfile;
use App\Http\Controllers\Instruktur\RekapNilaiController as InstrukturRekapNilai;
use App\Http\Controllers\Instruktur\TugasController as InstrukturTugas;
use App\Http\Controllers\Mahasiswa\DashboardController as MahasiswaDashboard;
use App\Http\Controllers\Mahasiswa\AiChatController as MahasiswaAiChat;
use App\Http\Controllers\Mahasiswa\DiskusiController as MahasiswaDiskusi;
use App\Http\Controllers\Mahasiswa\KelasController as MahasiswaKelas;
use App\Http\Controllers\Mahasiswa\MateriController as MahasiswaMateri;
use App\Http\Controllers\Mahasiswa\TugasController as MahasiswaTugas;
use App\Http\Controllers\Mahasiswa\TugasIndividuController as MahasiswaTugasIndividu;
use App\Http\Controllers\Mahasiswa\NilaiController as MahasiswaNilai;
use App\Http\Controllers\Mahasiswa\UjianController as MahasiswaUjian;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\FakultasController;
use App\Http\Controllers\Admin\EnrollmentController;
use App\Http\Controllers\Admin\InstrukturController;
use App\Http\Controllers\Admin\JurusanController;
use App\Http\Controllers\Admin\KelasController;
use App\Http\Controllers\Admin\MahasiswaController;
use App\Http\Controllers\Admin\MataKuliahController;
use App\Http\Controllers\Admin\PeriodeAkademikController;
use App\Http\Controllers\Admin\RoleAccessController;
use App\Http\Controllers\Admin\PengaturanController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\UserRoleController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $stats = [
        'mahasiswa'   => \App\Models\Mahasiswa::count(),
        'instruktur'  => \App\Models\Instruktur::count(),
        'mata_kuliah' => \App\Models\MataKuliah::where('aktif', true)->count(),
        'kelas'       => \App\Models\Kelas::count(),
    ];
    $mataKuliahList = \App\Models\MataKuliah::with('jurusan')
        ->where('aktif', true)
        ->orderBy('jurusan_id')
        ->orderBy('semester')
        ->get();
    $instrukturList = \App\Models\Instruktur::with('user')
        ->where('status', 'aktif')
        ->get();
    return view('welcome', compact('stats', 'mataKuliahList', 'instrukturList'));
});

// Smart dashboard redirect — role-based
Route::get('/dashboard', function () {
    /** @var \App\Models\User $authUser */
    $authUser = Auth::user();
    $roles = $authUser->roles->pluck('name')->map(fn($n) => strtolower($n));

    if ($roles->isEmpty()) {
        return redirect()->route('access.status', ['reason' => 'no_role']);
    }

    if ($roles->contains('mahasiswa')) {
        return redirect()->route('mahasiswa.dashboard');
    }

    if ($roles->contains('instruktur') && !$roles->contains('admin')) {
        return redirect()->route('instruktur.dashboard');
    }

    return redirect()->route('admin.dashboard');
})->middleware('auth')->name('dashboard');

// Admin routes
Route::middleware(['auth', 'role:admin,instruktur'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    Route::get('fakultas', [FakultasController::class, 'index'])->name('fakultas.index')->middleware('access:lihat.fakultas');
    Route::post('fakultas', [FakultasController::class, 'store'])->name('fakultas.store')->middleware('access:tambah.fakultas');
    Route::put('fakultas/{fakultas}', [FakultasController::class, 'update'])->name('fakultas.update')->middleware('access:edit.fakultas');
    Route::delete('fakultas/{fakultas}', [FakultasController::class, 'destroy'])->name('fakultas.destroy')->middleware('access:hapus.fakultas');

    Route::get('periode-akademik', [PeriodeAkademikController::class, 'index'])->name('periode-akademik.index')->middleware('access:lihat.periode-akademik');
    Route::post('periode-akademik', [PeriodeAkademikController::class, 'store'])->name('periode-akademik.store')->middleware('access:tambah.periode-akademik');
    Route::put('periode-akademik/{periodeAkademik}', [PeriodeAkademikController::class, 'update'])->name('periode-akademik.update')->middleware('access:edit.periode-akademik');
    Route::delete('periode-akademik/{periodeAkademik}', [PeriodeAkademikController::class, 'destroy'])->name('periode-akademik.destroy')->middleware('access:hapus.periode-akademik');

    Route::get('jurusan', [JurusanController::class, 'index'])->name('jurusan.index')->middleware('access:lihat.jurusan');
    Route::post('jurusan', [JurusanController::class, 'store'])->name('jurusan.store')->middleware('access:tambah.jurusan');
    Route::put('jurusan/{jurusan}', [JurusanController::class, 'update'])->name('jurusan.update')->middleware('access:edit.jurusan');
    Route::delete('jurusan/{jurusan}', [JurusanController::class, 'destroy'])->name('jurusan.destroy')->middleware('access:hapus.jurusan');

    Route::get('matakuliah', [MataKuliahController::class, 'index'])->name('matakuliah.index')->middleware('access:lihat.matakuliah');
    Route::post('matakuliah', [MataKuliahController::class, 'store'])->name('matakuliah.store')->middleware('access:tambah.matakuliah');
    Route::put('matakuliah/{mataKuliah}', [MataKuliahController::class, 'update'])->name('matakuliah.update')->middleware('access:edit.matakuliah');
    Route::delete('matakuliah/{mataKuliah}', [MataKuliahController::class, 'destroy'])->name('matakuliah.destroy')->middleware('access:hapus.matakuliah');

    Route::get('peserta', [MahasiswaController::class, 'index'])->name('peserta.index')->middleware('access:lihat.peserta');
    Route::get('peserta/{mahasiswa}/profil', [MahasiswaController::class, 'show'])->name('peserta.show')->middleware('access:lihat.peserta');
    Route::post('peserta', [MahasiswaController::class, 'store'])->name('peserta.store')->middleware('access:tambah.peserta');
    Route::put('peserta/{mahasiswa}', [MahasiswaController::class, 'update'])->name('peserta.update')->middleware('access:edit.peserta');
    Route::delete('peserta/{mahasiswa}', [MahasiswaController::class, 'destroy'])->name('peserta.destroy')->middleware('access:hapus.peserta');

    Route::get('instruktur', [InstrukturController::class, 'index'])->name('instruktur.index')->middleware('access:lihat.instruktur');
    Route::get('instruktur/{instruktur}/profil', [InstrukturController::class, 'show'])->name('instruktur.show')->middleware('access:lihat.instruktur');
    Route::post('instruktur', [InstrukturController::class, 'store'])->name('instruktur.store')->middleware('access:tambah.instruktur');
    Route::put('instruktur/{instruktur}', [InstrukturController::class, 'update'])->name('instruktur.update')->middleware('access:edit.instruktur');
    Route::delete('instruktur/{instruktur}', [InstrukturController::class, 'destroy'])->name('instruktur.destroy')->middleware('access:hapus.instruktur');
    Route::post('instruktur/{instruktur}/buat-akun', [InstrukturController::class, 'createAccount'])->name('instruktur.create-account');
    Route::post('instruktur/{instruktur}/reset-password', [InstrukturController::class, 'resetPassword'])->name('instruktur.reset-password');
    Route::post('instruktur/{instruktur}/avatar', [InstrukturController::class, 'uploadAvatar'])->name('instruktur.avatar');

    Route::get('kelas', [KelasController::class, 'index'])->name('kelas.index')->middleware('access:lihat.kelas');
    Route::post('kelas', [KelasController::class, 'store'])->name('kelas.store')->middleware('access:tambah.kelas');
    Route::put('kelas/{kelas}', [KelasController::class, 'update'])->name('kelas.update')->middleware('access:edit.kelas');
    Route::delete('kelas/{kelas}', [KelasController::class, 'destroy'])->name('kelas.destroy')->middleware('access:hapus.kelas');

    Route::get('enrollment', [EnrollmentController::class, 'index'])->name('enrollment.index')->middleware('access:lihat.enrollment');
    Route::post('enrollment', [EnrollmentController::class, 'store'])->name('enrollment.store')->middleware('access:tambah.enrollment');
    Route::put('enrollment/{enrollment}', [EnrollmentController::class, 'update'])->name('enrollment.update')->middleware('access:edit.enrollment');
    Route::delete('enrollment/{enrollment}', [EnrollmentController::class, 'destroy'])->name('enrollment.destroy')->middleware('access:hapus.enrollment');

    Route::resource('users', UserController::class)->except(['create', 'edit']);
    Route::resource('roles', RoleController::class)->except(['create', 'edit', 'show']);
    Route::resource('access', AccessController::class)->except(['create', 'edit', 'show']);
    Route::get('user-roles', [UserRoleController::class, 'index'])->name('user-roles.index');
    Route::put('user-roles/{user}', [UserRoleController::class, 'update'])->name('user-roles.update');
    Route::get('log', [ActivityLogController::class, 'index'])->name('log.index');
    Route::delete('log/all', [ActivityLogController::class, 'destroyAll'])->name('log.destroyAll');
    Route::delete('log/{log}', [ActivityLogController::class, 'destroy'])->name('log.destroy');

    Route::get('role-access', [RoleAccessController::class, 'index'])->name('role-access.index');
    Route::get('role-access/{role}/edit', [RoleAccessController::class, 'edit'])->name('role-access.edit');
    Route::put('role-access/{role}', [RoleAccessController::class, 'update'])->name('role-access.update');

    Route::get('pengaturan',   [PengaturanController::class, 'index'])  ->middleware('access:lihat.pengaturan')->name('pengaturan.index');
    Route::patch('pengaturan', [PengaturanController::class, 'update']) ->middleware('access:edit.pengaturan') ->name('pengaturan.update');

    Route::get('verifikasi',              [\App\Http\Controllers\Admin\VerifikasiController::class, 'index']) ->name('verifikasi.index');
    Route::get('verifikasi/data',         [\App\Http\Controllers\Admin\VerifikasiController::class, 'data'])  ->name('verifikasi.data');
    Route::post('verifikasi/{user}/verify', [\App\Http\Controllers\Admin\VerifikasiController::class, 'verify'])->name('verifikasi.verify');
    Route::post('verifikasi/{user}/resend', [\App\Http\Controllers\Admin\VerifikasiController::class, 'resend'])->name('verifikasi.resend');
});

// ── Shared diskusi routes (mahasiswa + instruktur) ────────────
Route::middleware(['auth'])->group(function () {
    Route::get('diskusi/check',             [MahasiswaDiskusi::class, 'globalCheck'])->name('diskusi.check');
    Route::get('diskusi/pb/{pokokBahasan}', [MahasiswaDiskusi::class, 'index'])->name('diskusi.index');
    Route::post('diskusi/pb/{pokokBahasan}',[MahasiswaDiskusi::class, 'store'])->name('diskusi.store');
    Route::delete('diskusi/{diskusi}',      [MahasiswaDiskusi::class, 'destroy'])->name('diskusi.destroy');
});

// ── Mahasiswa routes ──────────────────────────────────────────
Route::middleware(['auth', 'role:mahasiswa'])->prefix('mahasiswa')->name('mahasiswa.')->group(function () {
    Route::get('dashboard', [MahasiswaDashboard::class, 'index'])->name('dashboard');
    Route::get('profile',         [MahasiswaDashboard::class, 'profile'])       ->name('profile');
    Route::post('profile',        [MahasiswaDashboard::class, 'updateProfile']) ->name('profile.update');
    Route::post('profile/avatar', [MahasiswaDashboard::class, 'updateAvatar'])  ->name('profile.avatar');
    Route::get('materi', [MahasiswaMateri::class, 'index'])->name('materi.index');
    Route::get('kelas', [MahasiswaKelas::class, 'index'])->name('kelas.index');
    Route::get('kelas/{kelas}', [MahasiswaKelas::class, 'show'])->name('kelas.show');
    Route::post('kelas/join', [MahasiswaKelas::class, 'joinByToken'])->name('kelas.join');
    Route::get('kelas/{kelas}/pokok-bahasan/{pokokBahasan}', [MahasiswaMateri::class, 'showPB'])->name('materi.show');
    Route::get('kelas/{kelas}/pokok-bahasan/{pokokBahasan}/readers', [MahasiswaMateri::class, 'activeReaders'])->name('materi.readers');
    Route::post('materi/{materi}/progress',            [MahasiswaMateri::class, 'updateProgress']) ->name('materi.progress');
    Route::post('pokok-bahasan/{pokokBahasan}/rangkuman', [MahasiswaMateri::class, 'storeRangkuman'])->name('pb.rangkuman');

    // Tugas
    Route::get('tugas',                                                              [MahasiswaTugas::class, 'index'])         ->name('tugas.index');
    Route::get('tugas/kelompok/{kelompok}',                                          [MahasiswaTugas::class, 'show'])          ->name('tugas.kelompok.show');
    Route::post('tugas/kelompok/{kelompok}/anggota',                                 [MahasiswaTugas::class, 'storeAnggota'])  ->name('tugas.anggota.store');
    Route::delete('tugas/kelompok/{kelompok}/anggota/{anggota}',                     [MahasiswaTugas::class, 'destroyAnggota'])->name('tugas.anggota.destroy');
    Route::patch('tugas/kelompok/{kelompok}/anggota/{anggota}/topik',                [MahasiswaTugas::class, 'updateTopik'])   ->name('tugas.anggota.topik');
    Route::get('tugas/kelompok/{kelompok}/final',                                    [MahasiswaTugas::class, 'showFinal'])      ->name('tugas.kelompok.final');
    Route::patch('tugas/kelompok/{kelompok}/final',                                  [MahasiswaTugas::class, 'saveFinal'])      ->name('tugas.kelompok.final.save');
    Route::post('tugas/kelompok/{kelompok}/final/submit',                            [MahasiswaTugas::class, 'submitFinal'])    ->name('tugas.kelompok.final.submit');
    Route::delete('tugas/kelompok/{kelompok}/final/submit',                          [MahasiswaTugas::class, 'unsubmitFinal'])  ->name('tugas.kelompok.final.unsubmit');
    Route::post('tugas/kelompok/{kelompok}/self',                                    [MahasiswaTugas::class, 'storeKetuaEntry'])->name('tugas.ketua.self.store');
    Route::post('tugas/upload-gambar',                                               [MahasiswaTugas::class, 'uploadGambar'])  ->name('tugas.upload-gambar');
    Route::get('tugas/anggota/{anggota}/submit',                                     [MahasiswaTugas::class, 'showSubmit'])    ->name('tugas.anggota.submit.show');
    Route::patch('tugas/anggota/{anggota}/konten',                                   [MahasiswaTugas::class, 'saveKonten'])    ->name('tugas.anggota.konten');
    Route::post('tugas/anggota/{anggota}/submit',                                    [MahasiswaTugas::class, 'submit'])        ->name('tugas.anggota.submit');
    Route::delete('tugas/anggota/{anggota}/submit',                                  [MahasiswaTugas::class, 'unsubmit'])      ->name('tugas.anggota.unsubmit');

    // Nilai & Progress
    Route::get('nilai', [MahasiswaNilai::class, 'index'])->name('nilai.index');

    // Ujian
    Route::get('ujian',                         [MahasiswaUjian::class, 'index'])    ->name('ujian.index');
    Route::get('ujian/{ujian}/start',           [MahasiswaUjian::class, 'start'])    ->name('ujian.start');
    Route::post('ujian/{ujian}/begin',          [MahasiswaUjian::class, 'begin'])    ->name('ujian.begin');
    Route::get('ujian/{ujian}/exam',            [MahasiswaUjian::class, 'exam'])     ->name('ujian.exam');
    Route::post('ujian/{ujian}/submit',         [MahasiswaUjian::class, 'submit'])   ->name('ujian.submit');
    Route::post('ujian/{ujian}/auto-save',      [MahasiswaUjian::class, 'autoSave']) ->name('ujian.auto-save');
    Route::post('ujian/{ujian}/violation',      [MahasiswaUjian::class, 'violation'])->name('ujian.violation');
    Route::get('ujian/{ujian}/selesai',         [MahasiswaUjian::class, 'selesai'])  ->name('ujian.selesai');
    Route::post('ujian/keep-alive',             [MahasiswaUjian::class, 'keepAlive'])->name('ujian.keep-alive');

    // Tugas Individu (mahasiswa)
    Route::get('tugas/individu/{tugas}',               [MahasiswaTugasIndividu::class, 'show'])     ->name('tugas.individu.show');
    Route::post('tugas/individu/{tugas}/submit',       [MahasiswaTugasIndividu::class, 'submit'])   ->name('tugas.individu.submit');
    Route::delete('tugas/individu/{tugas}/submit',     [MahasiswaTugasIndividu::class, 'unsubmit']) ->name('tugas.individu.unsubmit');

    // AI Chat (per Pokok Bahasan)
    Route::get('pokok-bahasan/{pokokBahasan}/ai-chat',  [MahasiswaAiChat::class, 'history'])->name('ai.history');
    Route::post('pokok-bahasan/{pokokBahasan}/ai-chat', [MahasiswaAiChat::class, 'chat'])   ->name('ai.chat');
});

// ── Instruktur routes ─────────────────────────────────────────
Route::middleware(['auth', 'role:instruktur'])->prefix('instruktur')->name('instruktur.')->group(function () {
    Route::get('dashboard', [InstrukturDashboard::class, 'index'])->name('dashboard');
    Route::get('bank-soal',              [InstrukturBankSoal::class, 'index'])      ->name('bank-soal.index');
    Route::post('bank-soal',             [InstrukturBankSoal::class, 'store'])      ->name('bank-soal.store');
    Route::put('bank-soal/{bankSoal}',   [InstrukturBankSoal::class, 'update'])     ->name('bank-soal.update');
    Route::delete('bank-soal/{bankSoal}',[InstrukturBankSoal::class, 'destroy'])    ->name('bank-soal.destroy');
    Route::post('bank-soal/ai-generate', [InstrukturBankSoal::class, 'aiGenerate']) ->name('bank-soal.ai-generate');
    Route::post('bank-soal/ai-save',     [InstrukturBankSoal::class, 'aiSave'])     ->name('bank-soal.ai-save');
    Route::get('profile',              [InstrukturProfile::class, 'show'])          ->name('profile');
    Route::post('profile',             [InstrukturProfile::class, 'update'])         ->name('profile.update');
    Route::post('profile/avatar',      [InstrukturProfile::class, 'updateAvatar'])   ->name('profile.avatar');
    Route::get('profile/password',     [InstrukturProfile::class, 'passwordPage'])   ->name('profile.password');
    Route::post('profile/password',    [InstrukturProfile::class, 'updatePassword']) ->name('profile.password.update');
    Route::get('kelas', [InstrukturKelas::class, 'index'])->name('kelas.index');
    Route::get('kelas/{kelas}/peserta',                    [InstrukturKelas::class, 'peserta'])        ->name('kelas.peserta');
    Route::get('kelas/{kelas}/peserta/search',             [InstrukturKelas::class, 'searchMahasiswa'])->name('kelas.peserta.search');
    Route::post('kelas/{kelas}/peserta',                   [InstrukturKelas::class, 'enrollMahasiswa']) ->name('kelas.peserta.enroll');
    Route::post('kelas/{kelas}/peserta/by-id',             [InstrukturKelas::class, 'enrollById'])      ->name('kelas.peserta.enroll-by-id');
    Route::delete('kelas/{kelas}/peserta/{enrollment}',    [InstrukturKelas::class, 'unenroll'])         ->name('kelas.peserta.unenroll');

    Route::get('materi',                      [InstrukturMateri::class, 'index'])        ->name('materi.index');
    Route::post('materi',                     [InstrukturMateri::class, 'store'])        ->name('materi.store');
    Route::put('materi/{materi}',             [InstrukturMateri::class, 'update'])       ->name('materi.update');
    Route::delete('materi/{materi}',          [InstrukturMateri::class, 'destroy'])      ->name('materi.destroy');
    Route::patch('materi/{materi}/toggle',    [InstrukturMateri::class, 'togglePublish'])->name('materi.toggle');

    Route::post('pokok-bahasan',                        [InstrukturPB::class, 'store'])  ->name('pokok-bahasan.store');
    Route::put('pokok-bahasan/{pokokBahasan}',          [InstrukturPB::class, 'update']) ->name('pokok-bahasan.update');
    Route::delete('pokok-bahasan/{pokokBahasan}',       [InstrukturPB::class, 'destroy'])->name('pokok-bahasan.destroy');
    Route::get('pokok-bahasan/{pokokBahasan}/materi',   [InstrukturMateri::class, 'showPB'])  ->name('pokok-bahasan.materi');
    Route::get('pokok-bahasan/{pokokBahasan}/preview',  [InstrukturMateri::class, 'pbPreview'])->name('pokok-bahasan.preview');
    Route::get('pokok-bahasan/{pokokBahasan}/rekap',    [InstrukturMateri::class, 'pbRekap'])  ->name('pokok-bahasan.rekap');
    Route::post('materi/reorder',                       [InstrukturMateri::class, 'reorder'])->name('materi.reorder');

    Route::patch('pokok-bahasan/{pokokBahasan}/toggle-rangkuman', [InstrukturMateri::class, 'togglePbRangkuman'])->name('pokok-bahasan.toggle-rangkuman');
    Route::patch('pb-rangkuman/{pbRangkuman}/grade',              [InstrukturMateri::class, 'gradeRangkuman'])    ->name('pb-rangkuman.grade');

    Route::post('rps/{kelas}',    [InstrukturMateri::class, 'uploadRps']) ->name('rps.upload');
    Route::delete('rps/{kelas}',  [InstrukturMateri::class, 'deleteRps']) ->name('rps.delete');

    Route::post('materi/{materi}/ai-chat', [InstrukturAiChat::class, 'chat'])->name('materi.ai-chat');

    // Tugas
    Route::get('tugas',                                  [InstrukturTugas::class, 'index'])          ->name('tugas.index');
    Route::post('tugas',                                 [InstrukturTugas::class, 'store'])          ->name('tugas.store');
    Route::put('tugas/{tugas}',                          [InstrukturTugas::class, 'update'])         ->name('tugas.update');
    Route::delete('tugas/{tugas}',                       [InstrukturTugas::class, 'destroy'])        ->name('tugas.destroy');
    Route::post('tugas/{tugas}/kelompok',                [InstrukturTugas::class, 'storeKelompok'])  ->name('tugas.kelompok.store');
    Route::put('tugas/{tugas}/kelompok/{kelompok}',      [InstrukturTugas::class, 'updateKelompok']) ->name('tugas.kelompok.update');
    Route::delete('tugas/{tugas}/kelompok/{kelompok}',   [InstrukturTugas::class, 'destroyKelompok'])->name('tugas.kelompok.destroy');
    Route::get('tugas/{tugas}/kelompok/{kelompok}/submission',  [InstrukturTugas::class, 'showSubmission'])->name('tugas.kelompok.submission');
    Route::post('tugas/{tugas}/kelompok/{kelompok}/grade',      [InstrukturTugas::class, 'grade'])         ->name('tugas.kelompok.grade');
    Route::post('tugas/{tugas}/kelompok/{kelompok}/ai-grade',   [InstrukturTugas::class, 'aiGrade'])       ->name('tugas.kelompok.ai-grade');

    // Tugas Individu (instruktur)
    Route::post('tugas/upload-soal-gambar',                      [InstrukturTugas::class, 'uploadSoalGambar'])        ->name('tugas.upload-soal-gambar');
    Route::get('tugas/{tugas}/individu/submissions',             [InstrukturTugas::class, 'showIndividuSubmissions']) ->name('tugas.individu.submissions');
    Route::post('tugas/{tugas}/individu/grade-all',              [InstrukturTugas::class, 'gradeAll'])                ->name('tugas.individu.grade-all');
    Route::post('tugas/{tugas}/individu/ai-grade',               [InstrukturTugas::class, 'aiGradeIndividu'])         ->name('tugas.individu.ai-grade');

    // Rekap Nilai
    Route::get('rekap-nilai',                                          [InstrukturRekapNilai::class, 'index'])          ->name('rekap-nilai.index');
    Route::get('rekap-nilai/{kelas}',                                  [InstrukturRekapNilai::class, 'show'])           ->name('rekap-nilai.show');
    Route::post('rekap-nilai/{kelas}/komponen',                        [InstrukturRekapNilai::class, 'storeKomponen'])  ->name('rekap-nilai.komponen.store');
    Route::delete('rekap-nilai/{kelas}/komponen/{komponen}',           [InstrukturRekapNilai::class, 'destroyKomponen'])->name('rekap-nilai.komponen.destroy');
    Route::post('rekap-nilai/{kelas}/komponen/{komponen}/pilihan',     [InstrukturRekapNilai::class, 'simpanPilihan'])  ->name('rekap-nilai.pilihan.store');

    // Ujian
    Route::get('ujian',                              [InstrukturUjian::class, 'index'])          ->name('ujian.index');
    Route::get('ujian/soal-by-kelas',                [InstrukturUjian::class, 'getSoalByKelas']) ->name('ujian.soal-by-kelas');
    Route::get('ujian/{ujian}',                      [InstrukturUjian::class, 'show'])           ->name('ujian.show');
    Route::post('ujian',                             [InstrukturUjian::class, 'store'])          ->name('ujian.store');
    Route::put('ujian/{ujian}',                      [InstrukturUjian::class, 'update'])         ->name('ujian.update');
    Route::delete('ujian/{ujian}',                   [InstrukturUjian::class, 'destroy'])        ->name('ujian.destroy');
    Route::get('ujian/{ujian}/pengawas',             [InstrukturUjian::class, 'pengawas'])       ->name('ujian.pengawas');
    Route::get('ujian/{ujian}/pengawas/data',        [InstrukturUjian::class, 'pengawasData'])   ->name('ujian.pengawas.data');
    Route::post('ujian/{ujian}/reset-sesi/{mahasiswa}', [InstrukturUjian::class, 'resetSesi'])   ->name('ujian.reset-sesi');
    // Penilaian
    Route::get('ujian/{ujian}/penilaian',                               [InstrukturUjian::class, 'penilaian'])        ->name('ujian.penilaian');
    Route::post('ujian/{ujian}/penilaian/{sesi}/ai-grade',              [InstrukturUjian::class, 'aiGradeEssay'])     ->name('ujian.penilaian.ai-grade');
    Route::post('ujian/{ujian}/penilaian/{sesi}/jawaban/{jawaban}/grade',[InstrukturUjian::class, 'gradeJawaban'])    ->name('ujian.penilaian.grade-jawaban');
    Route::post('ujian/{ujian}/penilaian/{sesi}/publish',               [InstrukturUjian::class, 'publishNilai'])    ->name('ujian.penilaian.publish');
    Route::post('ujian/{ujian}/penilaian/publish-all',                  [InstrukturUjian::class, 'publishAllNilai']) ->name('ujian.penilaian.publish-all');
});

// ── Access status / error pages ───────────────────────────────
Route::get('/access-status', function () {
    $reason = request('reason', 'forbidden');
    /** @var \App\Models\User|null $authUser */
    $authUser = Auth::user();
    $home = $authUser?->homeRoute();

    $map = [
        'no_role' => [
            'code'        => '403',
            'icon'        => 'fa-user-slash',
            'iconBg'      => 'rgba(239,68,68,.12)',
            'iconColor'   => '#f87171',
            'glowRgb'     => '239,68,68',
            'title'       => 'Akun Belum Dikonfigurasi',
            'description' => 'Akun Anda belum memiliki role yang ditetapkan. Silakan hubungi administrator untuk mengatur akses Anda.',
            'showContact' => true,
        ],
        'area_mismatch' => [
            'code'        => '403',
            'icon'        => 'fa-shield-halved',
            'iconBg'      => 'rgba(245,158,11,.12)',
            'iconColor'   => '#fbbf24',
            'glowRgb'     => '245,158,11',
            'title'       => 'Area Tidak Sesuai',
            'description' => 'Anda tidak memiliki izin untuk mengakses area ini. Silakan kembali ke halaman yang sesuai dengan peran Anda.',
            'homeRoute'   => $home,
            'homeLabel'   => 'Ke Halaman Saya',
            'showContact' => false,
        ],
        'forbidden' => [
            'code'        => '403',
            'icon'        => 'fa-ban',
            'iconBg'      => 'rgba(239,68,68,.12)',
            'iconColor'   => '#f87171',
            'glowRgb'     => '239,68,68',
            'title'       => 'Akses Ditolak',
            'description' => 'Anda tidak memiliki hak akses untuk halaman atau tindakan ini.',
            'homeRoute'   => $home,
            'homeLabel'   => 'Ke Dashboard',
            'showContact' => true,
        ],
    ];

    $vars = $map[$reason] ?? $map['forbidden'];
    return view('errors.access', $vars);
})->middleware('auth')->name('access.status');

Route::get('/coming-soon', function () {
    /** @var \App\Models\User|null $authUser */
    $authUser = Auth::user();
    return view('errors.access', [
        'code'        => null,
        'icon'        => 'fa-rocket',
        'iconBg'      => 'rgba(79,110,247,.12)',
        'iconColor'   => 'var(--ac)',
        'glowRgb'     => '79,110,247',
        'title'       => 'Segera Hadir',
        'description' => 'Fitur ini sedang dalam pengembangan dan akan tersedia dalam waktu dekat.',
        'homeRoute'   => $authUser?->homeRoute(),
        'homeLabel'   => 'Ke Dashboard',
        'showContact' => false,
    ]);
})->middleware('auth')->name('coming.soon');

Route::middleware('auth')->group(function () {
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Notifications
    Route::post('/notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.readAll');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'read'])->name('notifications.read');
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::delete('/notifications', [NotificationController::class, 'destroyAll'])->name('notifications.destroyAll');
});

require __DIR__.'/auth.php';
