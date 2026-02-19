@extends('layouts.app')

@section('title', 'Documentos - Admin')
@section('header_title', 'Biblioteca de Archivos')

@section('content')
<style>
    @media (max-width: 768px) {
        .admin-library-grid {
            grid-template-columns: 1fr !important;
            gap: 1.5rem !important;
        }
        /* Better mobile spacing */
        .admin-library-grid > div {
            padding: 1rem !important;
        }
        table {
            font-size: 0.8rem !important;
        }
        th, td {
            padding: 0.4rem !important;
        }
    }
</style>
<div class="flex flex-col gap-8">
    <!-- Gestión de Materiales (Biblioteca) -->
    <div class="neobrutalist-card" style="background: white; margin-bottom: 2rem;">
        <div style="margin-bottom: 1.5rem; border-bottom: 3px solid #000; padding-bottom: 0.5rem;">
            <h3 style="margin: 0; font-size: 1.5rem;"><i class="fa-solid fa-folder-open"></i> Biblioteca de Materiales</h3>
        </div>

        <div class="admin-library-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; flex-wrap: wrap;">
            <!-- Form Upload -->
            <div style="background: #f9f9f9; padding: 1.5rem; border: 3px solid #000; border-radius: 10px;">
                <h4 style="margin-top:0;">Cargar nuevo material</h4>
                <form action="{{ route('admin.resources.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div style="margin-bottom: 1rem;">
                        <label style="display:block; font-weight:bold;">Título:</label>
                        <input type="text" name="title" class="neobrutalist-input" style="width:100%; border-width:2px;" required>
                    </div>
                    <div style="margin-bottom: 1rem;">
                        <label style="display:block; font-weight:bold;">Para el paciente (Opcional):</label>
                        <select name="paciente_id" class="neobrutalist-input" style="width:100%; border-width:2px;">
                            <option value="">-- Todos (Global) --</option>
                            @foreach($patients as $p)
                                <option value="{{ $p->id }}">{{ $p->nombre }} ({{ $p->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div style="margin-bottom: 1rem;">
                        <label style="display:block; font-weight:bold;">Archivo:</label>
                        <input type="file" name="file" class="neobrutalist-input" style="width:100%; border-width:2px;" required>
                    </div>
                    <button type="submit" class="neobrutalist-btn bg-celeste" style="width:100%;">Subir Material</button>
                </form>
            </div>

            <!-- List Materials -->
            <div style="overflow-y: auto; overflow-x: auto; max-height: 400px;">
                <h4 style="margin-top:0;">Archivos subidos</h4>
                <table style="width: 100%; border-collapse: collapse; background: white; border: 2px solid #000;">
                    <thead>
                        <tr style="background: #000; color: #fff; font-size: 0.8rem;">
                            <th style="padding: 0.5rem;">Título</th>
                            <th style="padding: 0.5rem;">Tipo</th>
                            <th style="padding: 0.5rem;">Para</th>
                            <th style="padding: 0.5rem;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="materialesTableBody">
                    @forelse($resources as $res)
                        @php
                            $isImage = in_array(strtolower($res->file_type), ['jpg', 'jpeg', 'png', 'webp', 'gif']);
                            $isPdf = strtolower($res->file_type) === 'pdf';
                        @endphp
                        <tr style="border-bottom: 2px solid #000;">
                            <td data-label="Título" style="padding: 0.8rem;">
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    @if($isImage)
                                        <div style="width: 35px; height: 35px; border: 1px solid #000; border-radius: 4px; overflow: hidden; flex-shrink: 0;">
                                            <img src="{{ asset('storage/' . $res->file_path) }}" style="width: 100%; height: 100%; object-fit: cover;">
                                        </div>
                                    @else
                                        <div style="width: 35px; height: 35px; border: 1px solid #000; border-radius: 4px; background: #eee; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                            <i class="fa-solid {{ $isPdf ? 'fa-file-pdf' : 'fa-file' }}" style="color: {{ $isPdf ? '#e11d48' : '#666' }}; font-size: 1rem;"></i>
                                        </div>
                                    @endif
                                    <span style="font-weight: 700;">{{ $res->title }}</span>
                                </div>
                            </td>
                            <td data-label="Tipo" style="padding: 0.8rem; font-size: 0.8rem; color: #666; font-family: monospace;">{{ strtoupper($res->file_type) }}</td>
                            <td data-label="Para" style="padding: 0.8rem;">
                                @if($res->patient)
                                    <span style="background: var(--color-lila); padding: 2px 6px; border: 1px solid #000; border-radius: 4px; font-size: 0.75rem;">{{ $res->patient->nombre }}</span>
                                @else
                                    <span style="background: #eee; padding: 2px 6px; border: 1px solid #000; border-radius: 4px; font-size: 0.75rem;">Global</span>
                                @endif
                            </td>
                            <td style="padding: 0.8rem; text-align: right;">
                                <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                                    <a href="{{ route('resources.download', $res->id) }}" class="neobrutalist-btn bg-celeste" style="padding: 0.3rem 0.6rem; font-size: 0.75rem;"><i class="fa-solid fa-download"></i></a>
                                    <form id="delete-res-{{ $res->id }}" action="{{ route('admin.resources.destroy', $res->id) }}" method="POST" style="margin:0;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="neobrutalist-btn bg-lila" style="padding: 0.3rem 0.6rem; font-size: 0.75rem;" onclick="confirmAction('delete-res-{{ $res->id }}', '¿Borrar recurso?')"><i class="fa-solid fa-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="padding: 2rem; text-align: center; color: #666;">No hay archivos compartidos.</td>
                        </tr>
                    @endforelse
                </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@include('dashboard.admin.partials.modals')
@include('dashboard.admin.partials.scripts')
@endsection
