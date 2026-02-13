<?php

namespace App\Http\Controllers;

use App\Models\ClinicalHistory;
use App\Models\Paciente;
use App\Models\Turno;
use Illuminate\Http\Request;

class ClinicalHistoryController extends Controller
{
    /**
     * Display list of turnos (appointments) for a patient
     * Shows which turnos have clinical notes and which don't
     */
    public function index($pacienteId)
    {
        $paciente = Paciente::with('user')->findOrFail($pacienteId);
        
        // Get all turnos for this patient with their clinical history (if exists)
        $turnos = Turno::where('paciente_id', $pacienteId)
            ->with('clinicalHistory')
            ->orderBy('fecha_hora', 'DESC')
            ->get();
        
        return view('dashboard.admin.clinical_history.index', [
            'paciente' => $paciente,
            'turnos' => $turnos,
        ]);
    }

    /**
     * Store a new clinical note for a specific turno
     */
    public function store(Request $request, $pacienteId, $turnoId)
    {
        $paciente = Paciente::findOrFail($pacienteId);
        $turno = Turno::findOrFail($turnoId);
        
        // Verify turno belongs to this paciente
        if ($turno->paciente_id != $paciente->id) {
            abort(403, 'Este turno no pertenece al paciente especificado');
        }

        // VALIDACIÓN DE ESTADO:
        // Permitimos explícitamente agregar notas a turnos con estado 'cancelado'.
        // No hay restricciones sobre el estado del turno para dar máxima flexibilidad.
        
        // Validate content
        $validated = $request->validate([
            'content' => 'required|string|min:10|max:5000',
        ], [
            'content.required' => 'El contenido de la nota es obligatorio',
            'content.min' => 'La nota debe tener al menos 10 caracteres',
            'content.max' => 'La nota no puede exceder 5000 caracteres',
        ]);
        
        // Create or update note for this turno
        ClinicalHistory::updateOrCreate(
            ['turno_id' => $turnoId],
            [
                'paciente_id' => $pacienteId,
                'content' => $validated['content'],
            ]
        );
        
        return redirect()->back()->with('success', '✅ Nota clínica guardada correctamente');
    }

    /**
     * Update an existing clinical note
     */
    public function update(Request $request, $pacienteId, $turnoId)
    {
        $note = ClinicalHistory::where('turno_id', $turnoId)->firstOrFail();
        
        // Validate content
        $validated = $request->validate([
            'content' => 'required|string|min:10|max:5000',
        ], [
            'content.required' => 'El contenido de la nota es obligatorio',
            'content.min' => 'La nota debe tener al menos 10 caracteres',
            'content.max' => 'La nota no puede exceder 5000 caracteres',
        ]);
        
        // VALIDACIÓN DE ESTADO:
        // Al igual que en store(), permitimos editar notas de turnos 'cancelados'.
        $note->update(['content' => $validated['content']]);
        
        return redirect()->back()->with('success', '✅ Nota clínica actualizada correctamente');
    }

    /**
     * Search and filter clinical notes
     * (Will be implemented in Phase 2)
     */
    public function search($pacienteId)
    {
        $paciente = Paciente::with('user')->findOrFail($pacienteId);
        
        // Start query: get turnos with notes
        $query = Turno::where('paciente_id', $pacienteId)
            ->with('clinicalHistory');
        
        // Search by keyword in note content (case-insensitive)
        if (request('search')) {
            $searchTerm = request('search');
            $query->whereHas('clinicalHistory', function ($q) use ($searchTerm) {
                // LOWER() converts to lowercase for case-insensitive comparison
                $q->whereRaw('LOWER(content) LIKE ?', ['%' . strtolower($searchTerm) . '%']);
            });
        }
        
        // Filter by turno date range
        if (request('date_from')) {
            $query->whereDate('fecha_hora', '>=', request('date_from'));
        }
        
        if (request('date_to')) {
            $query->whereDate('fecha_hora', '<=', request('date_to'));
        }
        
        // Filter by turno type (presencial/virtual)
        if (request('tipo')) {
            $query->where('tipo', request('tipo'));
        }
        
        // Get only turnos WITH notes
        $turnos = $query->whereHas('clinicalHistory')
            ->orderBy('fecha_hora', 'DESC')
            ->paginate(10);
        
        return view('dashboard.admin.clinical_history.index', [
            'paciente' => $paciente,
            'turnos' => $turnos,
            'searching' => true,
        ]);
    }

    /**
     * Export clinical history as PDF
     */
    public function exportPdf($pacienteId)
    {
        $paciente = Paciente::with('user')->findOrFail($pacienteId);
        
        // Get all turnos with clinical notes
        $turnos = Turno::where('paciente_id', $pacienteId)
            ->with('clinicalHistory')
            ->orderBy('fecha_hora', 'DESC')
            ->get();
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('dashboard.admin.clinical_history.pdf', [
            'paciente' => $paciente,
            'turnos' => $turnos,
            'generated_at' => now(),
        ]);
        
        $filename = "historia_clinica_{$paciente->nombre}_{$paciente->id}_" . date('Y-m-d') . ".pdf";
        
        return $pdf->download($filename);
    }
}
