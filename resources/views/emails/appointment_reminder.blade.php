@extends('emails.layout')

@section('header', 'Recordatorio de Turno')

@section('content')
    <p>Hola <strong>{{ $nombre }}</strong>,</p>
    <p>Te escribo para recordarte tu turno del día <strong>{{ $fecha }}</strong>.</p>
    
    @if($tipo_aviso == 'recordatorio')
        <div style="background: #FEF3C7; padding: 15px; border: 2px solid #000; margin: 20px 0;">
            <p style="margin:0;"><strong>Importante:</strong> Notamos que aún no has subido el comprobante de pago. Recordá hacerlo para asegurar tu lugar.</p>
        </div>
        <p>Podés subirlo rápidamente ingresando a tu portal:</p>
        <a href="{{ url('/') }}" class="btn" style="background: #A5B4FC;">Subir Comprobante</a>
    @elseif($tipo_aviso == 'ultimatum')
        <div style="background: #FEE2E2; padding: 15px; border: 4px solid #000; margin: 20px 0;">
            <p style="margin:0; font-weight: 900; color: #991B1B;">⚠️ ÚLTIMO AVISO</p>
            <p style="margin:5px 0 0 0;">Si no subís el comprobante en la próxima hora, el sistema cancelará automáticamente tu turno para liberar el horario.</p>
        </div>
        <p>Podés subirlo rápidamente ingresando a tu portal:</p>
        <a href="{{ url('/') }}" class="btn" style="background: #A5B4FC;">Subir Comprobante</a>
    @elseif($tipo_aviso == 'proxima_sesion')
        <div style="background: #ECFDF5; padding: 15px; border: 3px solid #000; margin: 20px 0;">
            <p style="margin:0; font-weight: 800; color: #065F46;">🎬 ¡Todo listo para tu sesión!</p>
            <p style="margin:5px 0 0 0;">Tu encuentro comienza en 1 hora aproximadamente. Podés unirte directamente haciendo clic en el botón de abajo.</p>
        </div>
        <a href="{{ url('/dashboard') }}" class="btn" style="background: #10B981; color: white;">INGRESAR AL PORTAL</a>
        <p style="font-size: 0.85rem; color: #666; margin-top: 10px;">(El link de Meet estará activo 1 hora antes de la sesión)</p>
    @elseif($tipo_aviso == 'recordatorio_confirmado')
        <div style="background: #DBEAFE; padding: 15px; border: 2px solid #000; margin: 20px 0;">
            <p style="margin:0;"><strong>¡Todo listo!</strong> Te recordamos tu sesión de mañana. Recordá que podés acceder al link de la reunión desde tu portal 1 hora antes del comienzo.</p>
        </div>
        <p>Acceder a mi portal:</p>
        <a href="{{ url('/dashboard') }}" class="btn" style="background: #60A5FA; color: white;">Ir al Portal</a>
    @endif

    <p>Si ya lo subiste o ya realizaste el pago, por favor desestimá este mail.</p>
    <p><strong>Lic. Nazarena De Luca</strong></p>
@endsection
