@extends('emails.layout')

@section('header', 'Recordatorio de Turno')

@section('content')
    <p>Hola <strong>{{ $nombre }}</strong>,</p>
    <p>Te escribo para recordarte tu turno del día <strong>{{ $fecha }}</strong>.</p>
    
    @php
        $isVirtual = isset($appointment) && $appointment->modalidad === 'virtual';
        $isPresencial = isset($appointment) && $appointment->modalidad === 'presencial';
        $meetLink = $appointment->meet_link ?? null;
        $consultorioLink = \App\Models\Setting::get('consultorio_link', 'https://www.google.com/maps/place/Adrogu%C3%A9/@-34.7988324,-58.3932301,3a,46y,321.03h,83.59t/data=!3m7!1e1!3m5!1s1PDRcDL0B2QDySTFe3RLeA!2e0!6shttps:%2F%2Fstreetviewpixels-pa.googleapis.com%2Fv1%2Fthumbnail%3Fcb_client%3Dmaps_sv.tactile%26w%3D900%26h%3D600%26pitch%3D6.412813024248052%26panoid%3D1PDRcDL0B2QDySTFe3RLeA%26yaw%3D321.03038456478566!7i16384!8i8192!4m10!1m2!2m1!1sestacion+de+adrogue!3m6!1s0x95bcd33e279f87af:0x5b7018552a97a57f!8m2!3d-34.7977604!4d-58.3941247!15sChNlc3RhY2lvbiBkZSBhZHJvZ3VlkgENdHJhaW5fc3RhdGlvbuABAA!16s%2Fg%2F121lm_m7?entry=ttu&g_ep=EgoyMDI2MDIyMy4wIKXMDSoASAFQAw%3D%3D');
    @endphp

    @if($tipo_aviso == 'recordatorio')
        <div style="background: #FEF3C7; padding: 15px; border: 2px solid #000; margin: 20px 0;">
            <p style="margin:0;"><strong>Recordatorio administrativo:</strong> Notamos que aún no has subido el comprobante de pago. Recordá hacerlo para mantener tu cuenta al día.</p>
        </div>
        <p>Podés gestionarlo ingresando a tu portal:</p>
        <a href="{{ url('/dashboard') }}" class="btn" style="background: #A5B4FC;">Ir al Portal</a>
    @elseif($tipo_aviso == 'ultimatum')
        <div style="background: #FEE2E2; padding: 15px; border: 4px solid #000; margin: 20px 0;">
            <p style="margin:0; font-weight: 900; color: #991B1B;">⚠️ PAGO PENDIENTE</p>
            <p style="margin:5px 0 0 0;">Te recordamos que tu sesión está próxima y aún figura un pago sin registrar. Recordá regularizarlo a la brevedad.</p>
        </div>
        <p>Podés subir el comprobante en tu portal:</p>
        <a href="{{ url('/dashboard') }}" class="btn" style="background: #A5B4FC;">Subir Comprobante</a>
    @elseif($tipo_aviso == 'proxima_sesion')
        <div style="background: #ECFDF5; padding: 15px; border: 3px solid #000; margin: 20px 0;">
            <p style="margin:0; font-weight: 800; color: #065F46;">🎬 ¡Todo listo para tu sesión!</p>
            @if($isVirtual)
                <p style="margin:5px 0 0 0;">Tu encuentro comienza en 1 hora aproximadamente. Podés unirte directamente haciendo clic en el botón de abajo.</p>
            @else
                <p style="margin:5px 0 0 0;">Tu encuentro comienza en 1 hora aproximadamente. Te espero en el consultorio.</p>
            @endif
        </div>
        
        @if($isVirtual)
            <a href="{{ $meetLink ?: url('/dashboard') }}" class="btn" style="background: #10B981; color: white;">UNIRSE A LA SESIÓN</a>
            <p style="font-size: 0.85rem; color: #666; margin-top: 10px;">(El link de Meet también está activo en tu portal)</p>
        @else
            <a href="{{ $consultorioLink }}" class="btn" style="background: #3B82F6; color: white;">VER UBICACIÓN DEL CONSULTORIO</a>
            <p style="font-size: 0.85rem; color: #666; margin-top: 10px;">(La sesión se realizará de forma presencial)</p>
        @endif

    @elseif($tipo_aviso == 'recordatorio_confirmado')
        <div style="background: #DBEAFE; padding: 15px; border: 2px solid #000; margin: 20px 0;">
            <p style="margin:0;"><strong>¡Todo listo!</strong> Te recordamos tu sesión de mañana.</p>
            @if($isVirtual)
                <p style="margin:5px 0 0 0;">Modalidad: Virtual. Podés acceder al link de la reunión desde tu portal 1 hora antes del comienzo.</p>
            @else
                <p style="margin:5px 0 0 0;">Modalidad: Presencial. Te espero en el consultorio en el horario acordado.</p>
            @endif
        </div>
        
        @if($isPresencial)
            <a href="{{ $consultorioLink }}" class="btn" style="background: #3B82F6; color: white;">VER UBICACIÓN DEL CONSULTORIO</a>
        @else
            <a href="{{ url('/dashboard') }}" class="btn" style="background: #60A5FA; color: white;">Ir al Portal</a>
        @endif
    @endif

    <p>Si ya lo subiste o ya realizaste el pago, por favor desestimá este mail.</p>
    <p><strong>Lic. Nazarena De Luca</strong></p>
@endsection
