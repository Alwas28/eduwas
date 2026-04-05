<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with('user')
            ->when($request->module,  fn($q, $v) => $q->where('module', $v))
            ->when($request->action,  fn($q, $v) => $q->where('action', $v))
            ->when($request->user_id, fn($q, $v) => $q->where('user_id', $v))
            ->when($request->date,    fn($q, $v) => $q->whereDate('created_at', $v));

        $logs    = $query->latest()->paginate(100)->withQueryString();
        $modules = ActivityLog::distinct()->orderBy('module')->pluck('module');
        $actions = ActivityLog::distinct()->orderBy('action')->pluck('action');

        $stats = [
            'total'   => ActivityLog::count(),
            'created' => ActivityLog::where('action', 'created')->count(),
            'updated' => ActivityLog::where('action', 'updated')->count(),
            'deleted' => ActivityLog::where('action', 'deleted')->count(),
            'login'   => ActivityLog::where('action', 'login')->count(),
        ];

        return view('admin.log.index', compact('logs', 'modules', 'actions', 'stats'));
    }

    public function destroy(ActivityLog $log)
    {
        $log->delete();
        return response()->json(['message' => 'Log berhasil dihapus.']);
    }

    public function destroyAll(Request $request)
    {
        $query = ActivityLog::query()
            ->when($request->module, fn($q, $v) => $q->where('module', $v))
            ->when($request->action, fn($q, $v) => $q->where('action', $v));

        $count = $query->count();
        $query->delete();

        return response()->json(['message' => "{$count} log berhasil dibersihkan."]);
    }
}
