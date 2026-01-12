@extends('emails.layout')

@section('header', 'Recordatorio de Turno')

@section('content')
    <p>Hola <strong>{{ $nombre }}</strong>,</p>
    <p>Te escribo para recordarte tu turno del día <strong>{{ $fecha }}</strong>.</p>
    
    @if($tipo_aviso == 'recordatorio')
        <div style="background: #FEF3C7; padding: 15px; border: 2px solid #000; margin: 20px 0;">
            <p style="margin:0;"><strong>Importante:</strong> Notamos que aún no has subido el comprobante de pago. Recordá hacerlo para asegurar tu lugar.</p>
        </div>
    @elseif($tipo_aviso == 'ultimatum')
        <div style="background: #FEE2E2; padding: 15px; border: 4px solid #000; margin: 20px 0;">
            <p style="margin:0; font-weight: 900; color: #991B1B;">⚠️ ÚLTIMO AVISO</p>
            <p style="margin:5px 0 0 0;">Si no subís el comprobante en la próxima hora, el sistema cancelará automáticamente tu turno para liberar el horario.</p>
        </div>
    @endif

    <p>Podés subirlo rápidamente ingresando a tu portal:</p>

    <a href="{{ url('/') }}" class="btn" style="background: #A5B4FC;">Subir Comprobante</a>

    <p>Si ya lo subiste o ya realizaste el pago, por favor desestimá este mail.</p>
    <p>Saludos,<br><strong>Lic. Nazarena De Luca</strong></p>
@endsection
