@extends('layouts.app')

@section('title', 'Mis Documentos')

@section('content')
<link rel="icon" type="image/jpeg" href="{{ asset('img/069b6f01-e0b6-4089-9e31-e383edf4ff62.jpg') }}">
<div class="flex flex-col gap-8 text-black" style="padding: 2rem; max-width: 1200px; margin: 0 auto;">
    
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <h2 style="font-family: 'Syne', sans-serif; font-weight: 800; font-size: 2rem; margin-bottom: 0.5rem;">Mis Documentos</h2>
            <p style="font-size: 1rem; color: #666; font-weight: 600;">Accedé a tus recibos, informes y certificados compartidos.</p>
        </div>
        <a href="{{ route('patient.dashboard') }}" class="neobrutalist-btn bg-white">
            <i class="fa-solid fa-arrow-left"></i> Volver al Portal
        </a>
    </div>

    <!-- Documents Grid -->
    @if(isset($documents) && count($documents) > 0)
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 2rem;">
            @foreach($documents as $doc)
            <div class="neobrutalist-card" style="background: white; padding: 1.5rem; display: flex; flex-direction: column; justify-content: space-between; height: 100%;">
                
                <div style="margin-bottom: 1.5rem;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
                        <span style="font-weight: 900; font-size: 0.8rem; background: #f3f4f6; padding: 4px 8px; border: 2px solid #000; border-radius: 6px;">
                            {{ strtoupper($doc->type) }}
                        </span>
                        <span style="font-size: 0.85rem; font-weight: 700; color: #666;">
                            {{ $doc->created_at->format('d/m/Y') }}
                        </span>
                    </div>
                    
                    <h3 style="font-weight: 800; font-size: 1.2rem; margin-bottom: 0.5rem; line-height: 1.3;">
                        {{ $doc->name }}
                    </h3>
                    
                    <div style="display: flex; align-items: center; gap: 0.5rem; color: #e11d48; font-weight: 700;">
                        <i class="fa-solid fa-file-pdf" style="font-size: 1.2rem;"></i>
                        <span>Archivo disponible</span>
                    </div>
                </div>

                <a href="{{ route('documents.download', $doc->id) }}" class="neobrutalist-btn bg-celeste w-full text-center" style="display: block; padding: 10px;">
                    <i class="fa-solid fa-download"></i> Descargar
                </a>
            </div>
            @endforeach
        </div>
    @else
        <div class="neobrutalist-card" style="background: #f9fafb; padding: 4rem; text-align: center; border-style: dashed;">
            <i class="fa-solid fa-folder-open" style="font-size: 3rem; color: #9ca3af; margin-bottom: 1.5rem;"></i>
            <h3 style="font-weight: 800; font-size: 1.5rem; color: #374151; margin-bottom: 0.5rem;">No hay documentos</h3>
            <p style="color: #6b7280; font-weight: 600;">No se encontraron archivos compartidos en tu cuenta.</p>
        </div>
    @endif

</div>

<style>
    .neobrutalist-card {
        border: 3px solid #000;
        box-shadow: 6px 6px 0px #000;
        border-radius: 12px;
        transition: transform 0.2s;
    }
    .neobrutalist-btn {
        border: 3px solid #000;
        box-shadow: 4px 4px 0px #000;
        border-radius: 8px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        transition: all 0.2s;
    }
    .neobrutalist-btn:hover {
        transform: translate(-2px, -2px);
        box-shadow: 6px 6px 0px #000;
    }
    .neobrutalist-btn:active {
        transform: translate(2px, 2px);
        box-shadow: 2px 2px 0px #000;
    }
    .bg-celeste { background: #bae6fd; color: black; }
    .bg-white { background: white; color: black; }
</style>
@endsection
