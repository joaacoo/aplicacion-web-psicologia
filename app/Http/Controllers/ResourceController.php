<?php

namespace App\Http\Controllers;

use App\Models\Resource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ResourceController extends Controller
{
    public function index()
    {
        // This might be consumed by dashboard controllers instead of a standalone page
        return redirect()->back(); 
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'file' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240', // 10MB
            'paciente_id' => 'nullable|exists:usuarios,id',
        ]);

        $path = $request->file('file')->store('resources', 'public');

        $resource = Resource::create([
            'paciente_id' => $request->paciente_id,
            'title' => $request->title,
            'description' => $request->description,
            'file_path' => $path,
            'file_type' => $request->file('file')->getClientOriginalExtension(),
        ]);

        $targetText = $request->paciente_id ? ' para el paciente #' . $request->paciente_id : ' (Global)';
        $this->logActivity('recurso_subido', 'Subió un nuevo recurso: ' . $request->title . $targetText, [
            'recurso_id' => $resource->id,
            'paciente_id' => $request->paciente_id
        ]);

        return back()->with('success', 'Recurso subido correctamente.');
    }

    public function destroy($id)
    {
        $resource = Resource::findOrFail($id);
        
        $this->logActivity('recurso_eliminado', 'Eliminó el recurso: ' . $resource->title, [
            'recurso_id' => $resource->id
        ]);

        if (Storage::disk('public')->exists($resource->file_path)) {
            Storage::disk('public')->delete($resource->file_path);
        }

        $resource->delete();
        return back()->with('success', 'Recurso eliminado.');
    }

    public function download($id)
    {
        $resource = Resource::findOrFail($id);
        
        // Security check: If resource is for a specific patient, only that patient or admin can download
        if (auth()->user()->rol !== 'admin' && $resource->paciente_id && $resource->paciente_id !== auth()->id()) {
            abort(403);
        }

        if (!Storage::disk('public')->exists($resource->file_path)) {
            abort(404);
        }

        return Storage::disk('public')->download($resource->file_path, $resource->title . '.' . $resource->file_type);
    }
}
