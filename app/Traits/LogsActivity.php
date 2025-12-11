<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

trait LogsActivity
{
  /**
   * Log an activity
   */
  public static function logActivity(
    string $action,
    string $model,
    $modelId = null,
    string $description = '',
    array $oldValues = [],
    array $newValues = []
  ) {
    if (Auth::check() && Auth::user()->role === 'admin') {
      ActivityLog::create([
        'user_id' => Auth::id(),
        'action' => $action,
        'model' => $model,
        'model_id' => $modelId,
        'description' => $description,
        'old_values' => !empty($oldValues) ? $oldValues : null,
        'new_values' => !empty($newValues) ? $newValues : null,
        'ip_address' => request()->ip(),
        'user_agent' => request()->userAgent(),
      ]);
    }
  }
}
