<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Log an administrative activity.
     */
    protected function logActivity($action, $description = null, $metadata = null)
    {
        try {
            \App\Models\ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => $action,
                'description' => $description,
                'metadata' => $metadata
            ]);
        } catch (\Exception $e) {
            \Log::error("Failed to log activity: " . $e->getMessage());
        }
    }
}
