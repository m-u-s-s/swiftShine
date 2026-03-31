<?php

namespace App\Support;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ActivityLogger
{
    public static function log(string $action, ?Model $target = null, array $meta = []): void
    {
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'target_type' => $target ? get_class($target) : null,
            'target_id' => $target?->getKey(),
            'meta' => $meta,
        ]);
    }

    public static function system(string $action, ?Model $target = null, array $meta = []): void
    {
        ActivityLog::create([
            'user_id' => null,
            'action' => $action,
            'target_type' => $target ? get_class($target) : null,
            'target_id' => $target?->getKey(),
            'meta' => $meta,
        ]);
    }
}