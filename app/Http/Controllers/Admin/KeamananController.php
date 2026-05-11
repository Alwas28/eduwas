<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class KeamananController extends Controller
{
    public function index()
    {
        // Log aktivitas login terbaru
        $logs = ActivityLog::with('user')
            ->whereIn('action', ['login', 'login_failed', 'ganti_password', 'backup_database'])
            ->latest()
            ->limit(20)
            ->get();

        // Sesi aktif dari database sessions
        $sesiAktif = [];
        try {
            $sesiAktif = DB::table('sessions')
                ->join('users', 'sessions.user_id', '=', 'users.id')
                ->select('sessions.*', 'users.name as user_name')
                ->orderByDesc('sessions.last_activity')
                ->get()
                ->map(function ($s) {
                    return [
                        'id'            => $s->id,
                        'user_name'     => $s->user_name,
                        'ip'            => $s->ip_address ?? '—',
                        'user_agent'    => $s->user_agent ?? '—',
                        'last_activity' => $s->last_activity,
                        'is_current'    => $s->id === session()->getId(),
                    ];
                });
        } catch (\Exception $e) {
            // sessions table mungkin belum ada
        }

        // Daftar file backup
        $backups = $this->getBackupFiles();

        // Stats
        $stats = [
            'total_backup'   => count($backups),
            'total_sesi'     => count($sesiAktif),
            'total_log'      => ActivityLog::count(),
            'login_gagal'    => ActivityLog::where('action', 'login_failed')->count(),
        ];

        return view('admin.keamanan.index', compact('logs', 'sesiAktif', 'backups', 'stats'));
    }

    /* ═══════════ BACKUP ═══════════ */

    public function backup(Request $request)
    {
        try {
            $backupDir = storage_path('app/backups');
            if (!is_dir($backupDir)) {
                mkdir($backupDir, 0775, true);
            }

            $filename  = 'edulearn_db_' . now()->format('Ymd_His') . '.sql';
            $filepath  = $backupDir . '/' . $filename;

            $db   = config('database.connections.' . config('database.default'));
            $host = escapeshellarg($db['host']);
            $port = escapeshellarg($db['port'] ?? 3306);
            $user = escapeshellarg($db['username']);
            $pass = $db['password'];
            $name = escapeshellarg($db['database']);

            // Build mysqldump command
            $passOpt  = $pass !== '' ? '-p' . escapeshellarg($pass) : '';
            $command  = "mysqldump -h {$host} -P {$port} -u {$user} {$passOpt} {$name} > " . escapeshellarg($filepath) . " 2>&1";

            exec($command, $output, $returnCode);

            if ($returnCode !== 0) {
                throw new \Exception('mysqldump gagal: ' . implode(' ', $output));
            }

            // Log aktivitas
            ActivityLog::create([
                'user_id'    => Auth::id(),
                'module'     => 'keamanan',
                'action'     => 'backup_database',
                'description'=> 'Backup database: ' . $filename,
                'ip_address' => $request->ip(),
            ]);

            $size = $this->formatBytes(filesize($filepath));

            return response()->json([
                'success'  => true,
                'message'  => 'Backup berhasil disimpan.',
                'filename' => $filename,
                'size'     => $size,
                'tanggal'  => now()->translatedFormat('d M Y, H:i') . ' WIB',
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function download(string $filename)
    {
        $filename = basename($filename); // sanitize path traversal
        $path     = storage_path('app/backups/' . $filename);

        if (!file_exists($path)) {
            abort(404, 'File backup tidak ditemukan.');
        }

        return response()->download($path, $filename, [
            'Content-Type' => 'application/sql',
        ]);
    }

    public function deleteBackup(string $filename)
    {
        $filename = basename($filename);
        $path     = storage_path('app/backups/' . $filename);

        if (file_exists($path)) {
            unlink($path);
            ActivityLog::create([
                'user_id'     => Auth::id(),
                'module'      => 'keamanan',
                'action'      => 'deleted',
                'description' => 'Hapus backup: ' . $filename,
                'ip_address'  => request()->ip(),
            ]);
            return response()->json(['success' => true, 'message' => 'Backup dihapus.']);
        }

        return response()->json(['success' => false, 'message' => 'File tidak ditemukan.'], 404);
    }

    /* ═══════════ PASSWORD ═══════════ */

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
        ], [
            'current_password.required' => 'Password saat ini wajib diisi.',
            'password.required'         => 'Password baru wajib diisi.',
            'password.confirmed'        => 'Konfirmasi password tidak cocok.',
            'password.min'              => 'Password minimal 8 karakter.',
        ]);

        if (!Hash::check($request->current_password, Auth::user()->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini tidak sesuai.'])->withInput();
        }

        Auth::user()->update(['password' => Hash::make($request->password)]);

        ActivityLog::create([
            'user_id'     => Auth::id(),
            'module'      => 'keamanan',
            'action'      => 'ganti_password',
            'description' => 'Ganti password akun administrator',
            'ip_address'  => $request->ip(),
        ]);

        return back()->with('success', 'Password berhasil diperbarui.');
    }

    /* ═══════════ SESI ═══════════ */

    public function terminateSession(Request $request, string $sessionId)
    {
        if ($sessionId === session()->getId()) {
            return response()->json(['success' => false, 'message' => 'Tidak dapat mengakhiri sesi aktif Anda sendiri.'], 422);
        }

        try {
            DB::table('sessions')->where('id', $sessionId)->delete();
            return response()->json(['success' => true, 'message' => 'Sesi berhasil diakhiri.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal mengakhiri sesi.'], 500);
        }
    }

    public function terminateAllSessions(Request $request)
    {
        try {
            DB::table('sessions')
                ->where('user_id', Auth::id())
                ->where('id', '!=', session()->getId())
                ->delete();

            return response()->json(['success' => true, 'message' => 'Semua sesi lain berhasil diakhiri.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal mengakhiri sesi.'], 500);
        }
    }

    /* ═══════════ HELPERS ═══════════ */

    private function getBackupFiles(): array
    {
        $backupDir = storage_path('app/backups');
        if (!is_dir($backupDir)) {
            return [];
        }

        $files = glob($backupDir . '/*.sql');
        if (!$files) return [];

        $result = [];
        foreach ($files as $f) {
            $result[] = [
                'filename' => basename($f),
                'size'     => $this->formatBytes(filesize($f)),
                'tanggal'  => date('d M Y, H:i', filemtime($f)) . ' WIB',
                'modified' => filemtime($f),
            ];
        }

        usort($result, fn($a, $b) => $b['modified'] <=> $a['modified']);
        return $result;
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes >= 1048576) return number_format($bytes / 1048576, 1) . ' MB';
        if ($bytes >= 1024)    return number_format($bytes / 1024, 1) . ' KB';
        return $bytes . ' B';
    }
}
