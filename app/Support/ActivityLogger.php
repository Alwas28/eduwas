<?php

namespace App\Support;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class ActivityLogger
{
    /**
     * Log an activity.
     *
     * @param  string       $action      e.g. 'created', 'updated', 'deleted', 'login'
     * @param  string       $module      e.g. 'users', 'roles', 'access'
     * @param  string|null  $description Human-readable message
     * @param  Model|null   $subject     Target Eloquent model (optional)
     * @param  array|null   $properties  Extra data (old/new values, etc.)
     */
    public static function log(
        string $action,
        string $module,
        ?string $description = null,
        ?Model $subject = null,
        ?array $properties = null
    ): ActivityLog {
        /** @var Request $request */
        $request = request();

        return ActivityLog::create([
            'user_id'      => auth()->id(),
            'action'       => $action,
            'module'       => $module,
            'description'  => $description,
            'subject_type' => ($subject && $subject->getKey()) ? get_class($subject) : null,
            'subject_id'   => $subject?->getKey() ?: null,
            'ip_address'   => $request->ip(),
            'user_agent'   => $request->userAgent(),
            'properties'   => $properties,
        ]);
    }
}
