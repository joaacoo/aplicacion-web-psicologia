@extends('emails.layout')

@section('header', '¡Bienvenida/o!')

@section('content')
    <p>Hola <strong>{{ $nombre }}</strong>,</p>
    <p>Gracias por registrarte en mi portal de turnos. Estoy muy contenta de acompañarte en este proceso.</p>
    
    <div style="background: #FEF3C7; padding: 15px; border: 2px solid #000; margin: 20px 0;">
        <p style="margin:0;"><strong>Recordá:</strong> Si es tu primer turno, deberás subir el comprobante de pago al momento de reservar para confirmar tu espacio.</p>
    </div>

    <p>Ya podés ingresar a tu portal para ver las disponibilidades y pedir tu primer turno.</p>

    <a href="{{ url('/') }}" class="btn" style="background: #BAE6FD;">Ir al Portal</a>

    <p>Cualquier duda, podés escribirme respondiendo a este mail.</p>
    <p><strong>Lic. Nazarena De Luca</strong></p>
@endsection
