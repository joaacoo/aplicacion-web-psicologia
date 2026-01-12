@extends('layouts.app')

@section('title', 'Subir Comprobante - Lic. Nazarena De Luca')

@section('content')
<div class="flex justify-center items-center" style="min-height: 60vh;">
    <div class="neobrutalist-card" style="width: 100%; max-width: 500px; background: var(--color-amarillo);">
        <h2 class="text-center mb-4">Comprobante de Pago</h2>
        <p class="text-center mb-4">
            Turno: <strong>{{ $appointment->fecha_hora->format('d/m/Y H:i') }}</strong>
        </p>

        <form action="{{ route('payments.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="appointment_id" value="{{ $appointment->id }}">
            
            <div class="mb-4">
                <label class="block font-bold mb-2">Subir Foto / PDF del Comprobante</label>
                <input type="file" name="proof" id="proof-input" class="neobrutalist-input" accept="image/*,.pdf" required>
            </div>

            <!-- Preview Container -->
            <div id="preview-outer" style="display: none; margin-bottom: 1.5rem; border: var(--border-thick); background: white; padding: 10px; box-shadow: 4px 4px 0px #000;">
                <p style="font-weight: 700; font-size: 0.8rem; margin-bottom: 0.5rem;">Vista previa:</p>
                <div id="preview-container" style="max-height: 300px; overflow: hidden; display: flex; justify-content: center;">
                    <img id="image-preview" src="#" alt="Preview" style="max-width: 100%; height: auto; display: none;">
                    <div id="pdf-preview" style="display: none; padding: 2rem; background: #eee; width: 100%; text-align: center;">
                        <i class="fa-solid fa-file-pdf" style="font-size: 3rem; color: #d00;"></i>
                        <p style="margin-top: 1rem; font-weight: 700;">Documento PDF seleccionado</p>
                    </div>
                </div>
            </div>

            <button type="submit" class="neobrutalist-btn w-full bg-verde">Enviar Comprobante</button>
        </form>
    </div>
</div>

<script>
    document.getElementById('proof-input').addEventListener('change', function(e) {
        const file = e.target.files[0];
        const previewOuter = document.getElementById('preview-outer');
        const imgPreview = document.getElementById('image-preview');
        const pdfPreview = document.getElementById('pdf-preview');

        if (file) {
            previewOuter.style.display = 'block';
            
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imgPreview.src = e.target.result;
                    imgPreview.style.display = 'block';
                    pdfPreview.style.display = 'none';
                }
                reader.readAsDataURL(file);
            } else if (file.type === 'application/pdf') {
                imgPreview.style.display = 'none';
                pdfPreview.style.display = 'block';
            }
        } else {
            previewOuter.style.display = 'none';
        }
    });
</script>
@endsection
