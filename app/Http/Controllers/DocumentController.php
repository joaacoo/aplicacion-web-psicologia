<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class DocumentController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:usuarios,id',
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240', // 10MB max, strict mimes
            'name' => 'required|string|max:255',
            'type' => 'nullable|in:recibo,certificado,otro',
            'scope' => 'nullable|in:single,all' // New scope parameter
        ]);

        $file = $request->file('file');
        // Store physically once
        $path = $file->store('documents', 'public');
        $type = $request->type ?? 'otro';

        if ($request->scope === 'all') {
            // Bulk create for all ACTIVE patients
            $patients = \App\Models\User::where('rol', 'paciente')->get();
            
            foreach ($patients as $patient) {
                Document::create([
                    'user_id' => $patient->id,
                    'name' => $request->name,
                    'file_path' => $path, // Point to same file
                    'type' => $type,
                ]);
            }
            $msg = 'Documento enviado a todos los pacientes.';
        } else {
            // Single creation
            Document::create([
                'user_id' => $request->user_id,
                'name' => $request->name,
                'file_path' => $path,
                'type' => $type,
            ]);
            $msg = 'Documento subido correctamente.';
        }

        return response()->json(['success' => true, 'message' => $msg], 200);
    }

    public function download(Document $document)
    {
        // Security check: Only Admin or the Owner can download
        if (Auth::user()->rol !== 'admin' && Auth::id() !== $document->user_id) {
            abort(403);
        }

        return Storage::download('public/' . $document->file_path, $document->name . '.' . pathinfo($document->file_path, PATHINFO_EXTENSION));
    }

    public function destroy(Document $document)
    {
        if (Auth::user()->rol !== 'admin') {
            abort(403);
        }

        $filePath = $document->file_path;
        
        // Delete record first
        $document->delete();

        // Check if any other document record is using this file
        $othersUsingFile = Document::where('file_path', $filePath)->exists();

        if (!$othersUsingFile) {
            // If no one else uses it, delete the physical file
            Storage::delete('public/' . $filePath);
        }

        return back()->with('success', 'Documento eliminado.');
    }
}
