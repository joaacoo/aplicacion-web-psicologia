<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Appointment;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class ActivityLogController extends Controller
{
    public function revert(Request $request, $id)
    {
        $request->validate([
            'password' => 'required'
        ]);

        // Verify Admin Password
        if (!Hash::check($request->password, Auth::user()->password)) {
            return response()->json(['error' => 'La contraseña es incorrecta.'], 403);
        }

        $log = ActivityLog::findOrFail($id);
        $metadata = $log->metadata;

        try {
            switch ($log->action) {
                case 'pago_verificado':
                    if (isset($metadata['pago_id'])) {
                        $payment = Payment::find($metadata['pago_id']);
                        if ($payment) {
                            $payment->update(['estado' => 'pendiente', 'verificado_en' => null]);
                            $payment->appointment->update(['estado' => 'pendiente']);
                        }
                    }
                    break;

                case 'pago_rechazado':
                    if (isset($metadata['pago_id'])) {
                        $payment = Payment::find($metadata['pago_id']);
                        if ($payment) $payment->update(['estado' => 'pendiente']);
                    }
                    break;

                case 'turno_cancelado':
                    if (isset($metadata['turno_id'])) {
                        $appt = Appointment::find($metadata['turno_id']);
                        if ($appt) $appt->update(['estado' => 'pendiente']);
                    }
                    break;

                case 'turno_confirmado':
                    if (isset($metadata['turno_id'])) {
                        $appt = Appointment::find($metadata['turno_id']);
                        if ($appt) $appt->update(['estado' => 'pendiente']);
                    }
                    break;

                default:
                    return response()->json(['error' => 'Esta acción no se puede revertir.'], 400);
            }

            // Log the reversal itself
            $log->update(['action' => $log->action . '_revertido']);
            $this->logActivity('accion_revertida', 'Revirtió la acción: ' . str_replace('_', ' ', $log->action), [
                'original_log_id' => $log->id
            ]);

            return response()->json(['success' => 'Acción revertida correctamente.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Hubo un error al revertir: ' . $e->getMessage()], 500);
        }
    }
}
