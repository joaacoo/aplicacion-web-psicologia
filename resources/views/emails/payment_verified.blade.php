@extends('emails.layout')

@section('header', 'Pago Verificado')

@section('content')
    @php
        $isVirtual = isset($appointment) && $appointment->modalidad === 'virtual';
        $isPresencial = isset($appointment) && $appointment->modalidad === 'presencial';
        $meetLink = $appointment->link_reunion ?? ($link_reunion ?? null);
        $consultorioLink = \App\Models\Setting::get('consultorio_link', 'https://www.google.com/maps/place/Adrogu%C3%A9/@-34.7988324,-58.3932301,3a,46y,321.03h,83.59t/data=!3m7!1e1!3m5!1s1PDRcDL0B2QDySTFe3RLeA!2e0!6shttps:%2F%2Fstreetviewpixels-pa.googleapis.com%2Fv1%2Fthumbnail%3Fcb_client%3Dmaps_sv.tactile%26w%3D900%26h%3D600%26pitch%3D6.412813024248052%26panoid%3D1PDRcDL0B2QDySTFe3RLeA%26yaw%3D321.03038456478566!7i16384!8i8192!4m10!1m2!2m1!1sestacion+de+adrogue!3m6!1s0x95bcd33e279f87af:0x5b7018552a97a57f!8m2!3d-34.7977604!4d-58.3941247!15sChNlc3RhY2lvbiBkZSBhZHJvZ3VlkgENdHJhaW5fc3RhdGlvbuABAA!16s%2Fg%2F121lm_m7?entry=ttu&g_ep=EgoyMDI2MDIyMy4wIKXMDSoASAFQAw%3D%3D');
    @endphp

    <p>Hola <strong>{{ $nombre }}</strong>,</p>
    <p>Te informo que tu pago para el turno del día <strong>{{ $fecha }}</strong> ha sido verificado con éxito.</p>
    
    <div style="background: #DCFCE7; padding: 15px; border: 2px solid #000; margin: 20px 0;">
        <p style="margin:0; font-weight: 900; color: #166534; text-align: center;">¡PAGO REGISTRADO!</p>
        
        @if($isVirtual)
            <p style="margin:10px 0 0 0;">Como tu sesión es <strong>virtual</strong>, podés unirte a la reunión directamente desde aquí:</p>
            <div style="text-align: center;">
                <a href="{{ $meetLink ?: url('/dashboard') }}" class="btn" style="background: #D9F99D; margin-top: 10px;">Unirse a Google Meet</a>
            </div>
        @else
            <p style="margin:10px 0 0 0;">Como tu sesión es <strong>presencial</strong>, te espero en el consultorio en el horario acordado.</p>
            <div style="text-align: center;">
                <a href="{{ $consultorioLink }}" class="btn" style="background: #BAE6FD; margin-top: 10px;">Ver ubicación del consultorio</a>
            </div>
        @endif
    </div>

    <p>Ya podés ver los detalles actualizados en tu portal.</p>

    <a href="{{ url('/dashboard') }}" class="btn" style="background: #A5B4FC;">Ir a mi Portal</a>

    <p><strong>Lic. Nazarena De Luca</strong></p>
@endsection
