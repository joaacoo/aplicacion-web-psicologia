@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6" style="margin: 0 auto; max-width: 600px;">
        <div class="neobrutalist-card" style="text-align: center;">
            <div style="font-size: 3rem; margin-bottom: 1rem;">⏳</div>
            <h2 style="font-weight: 800; font-size: 1.8rem; margin-bottom: 0.5rem;">Lista de Espera</h2>
            <p style="margin-bottom: 2rem; color: #555;">
                Actualmente no hay turnos disponibles que coincidan con tu búsqueda, o la agenda está completa. 
                <br>Dejanos tus datos y preferencia horaria para avisarte cuando se libere un lugar.
            </p>

            <form action="{{ route('waitlist.store') }}" method="POST" style="text-align: left;">
                @csrf
                
                @if(request('date'))
                    <div style="background: #fffadc; border: 2px solid #000; padding: 0.8rem; border-radius: 10px; margin-bottom: 1.5rem; font-size: 0.9rem;">
                        <strong>Horario solicitado:</strong> {{ \Carbon\Carbon::parse(request('date'))->format('d/m') }} a las {{ request('time') }} hs.
                        <input type="hidden" name="fecha_especifica" value="{{ request('date') }}">
                        <input type="hidden" name="hora_inicio" value="{{ request('time') }}">
                    </div>
                @endif
                
                <div style="margin-bottom: 1rem;">
                    <label style="font-weight: 700;">Nombre Completo</label>
                    <input type="text" name="name" class="neobrutalist-input" required placeholder="Tu nombre..." value="{{ auth()->check() ? auth()->user()->name : '' }}">
                </div>

                <div style="margin-bottom: 1rem;">
                    <label style="font-weight: 700;">Teléfono / WhatsApp</label>
                    <input type="text" name="phone" class="neobrutalist-input" required placeholder="Ej: 11 1234 5678" value="{{ auth()->check() ? auth()->user()->telefono : '' }}">
                </div>

                <div style="margin-bottom: 1rem;">
                    <label style="font-weight: 700;">Modalidad Preferida</label>
                    <select name="modality" class="neobrutalist-input" required>
                        <option value="Virtual">Virtual</option>
                        <option value="Presencial">Presencial</option>
                        <option value="Indistinto">Indistinto</option>
                    </select>
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label style="font-weight: 700;">Disponibilidad Horaria</label>
                    <textarea name="availability" class="neobrutalist-input" rows="3" required placeholder="Ej: Lunes a Viernes después de las 17hs..."></textarea>
                </div>

                <button type="submit" class="neobrutalist-btn bg-verde w-full">Unirme a la Lista</button>
            </form>
           
            <div style="margin-top: 1.5rem;">
                <a href="{{ route('dashboard') }}" style="color: #666; font-weight: 600; text-decoration: none;">&larr; Volver al inicio</a>
            </div>
        </div>
    </div>
</div>
@endsection
