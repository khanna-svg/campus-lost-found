<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;

class AuditLogger
{
    /**
     * Record an immutable entry in the system audit trail.
     */
    public function log(
        string $action,
        ?Model $subject = null,
        string $description = '',
        array $properties = []
    ): ActivityLog {
        $subjectType = null;
        $subjectId = null;

        if ($subject) {
            $subjectType = class_basename($subject);
            $subjectId = $subject->getKey();
        }

        // Limit user-agent to 250 chars as defined by schema varchar(255)
        $ua = Str::limit((string) Request::userAgent(), 250);
        $ip = Request::ip();

        return ActivityLog::create([
            'actor_id' => Auth::id(),
            'action' => $action,
            'subject_type' => $subjectType,
            'subject_id' => $subjectId,
            'description' => $description,
            'properties' => ! empty($properties) ? $properties : null,
            'ip_address' => $ip,
            'user_agent' => $ua ?: null,
            'created_at' => now(),
        ]);
    }
}
