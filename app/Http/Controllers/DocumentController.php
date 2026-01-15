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
            'user_id' => 'required|exists:users,id',
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240', // 10MB max, strict mimes
            'name' => 'required|string|max:255',
            'type' => 'required|in:recibo,certificado,otro',
        ]);

        $file = $request->file('file');
        $path = $file->store('documents', 'public');

        Document::create([
            'user_id' => $request->user_id,
            'name' => $request->name,
            'file_path' => $path,
            'type' => $request->type,
        ]);

        return back()->with('success', 'Documento subido correctamente.');
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

        Storage::delete('public/' . $document->file_path);
        $document->delete();

        return back()->with('success', 'Documento eliminado.');
    }
}
