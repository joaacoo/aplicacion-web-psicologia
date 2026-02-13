<?php

namespace App\Observers;

use App\Models\ClinicalHistory;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class ClinicalHistoryObserver
{
    public function created(ClinicalHistory $history): void
    {
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'create',
            'model_type' => 'ClinicalHistory',
            'model_id' => $history->id,
            'description' => "Nota clínica creada para turno #{$history->turno_id}",
        ]);
    }

    public function updated(ClinicalHistory $history): void
    {
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'update',
            'model_type' => 'ClinicalHistory',
            'model_id' => $history->id,
            'description' => "Nota clínica editada para turno #{$history->turno_id}",
        ]);
    }

    public function deleted(ClinicalHistory $history): void
    {
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'delete',
            'model_type' => 'ClinicalHistory',
            'model_id' => $history->id,
            'description' => "Nota clínica eliminada (soft delete) para turno #{$history->turno_id}",
        ]);
    }

    public function restored(ClinicalHistory $history): void
    {
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'restore',
            'model_type' => 'ClinicalHistory',
            'model_id' => $history->id,
            'description' => "Nota clínica restaurada para turno #{$history->turno_id}",
        ]);
    }
}
