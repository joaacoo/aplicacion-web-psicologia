<?php

namespace App\Observers;

use App\Models\Turno;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class TurnoObserver
{
    public function created(Turno $turno): void
    {
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'create',
            'model_type' => 'Turno',
            'model_id' => $turno->id,
            'description' => "Turno creado para paciente #{$turno->paciente_id}",
        ]);
    }

    public function updated(Turno $turno): void
    {
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'update',
            'model_type' => 'Turno',
            'model_id' => $turno->id,
            'description' => "Turno editado para paciente #{$turno->paciente_id}",
        ]);
    }

    public function deleted(Turno $turno): void
    {
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'delete',
            'model_type' => 'Turno',
            'model_id' => $turno->id,
            'description' => "Turno eliminado (soft delete) para paciente #{$turno->paciente_id}",
        ]);
    }

    public function restored(Turno $turno): void
    {
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'restore',
            'model_type' => 'Turno',
            'model_id' => $turno->id,
            'description' => "Turno restaurado para paciente #{$turno->paciente_id}",
        ]);
    }
}
